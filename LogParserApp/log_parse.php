<?php
/**
 * Initial test script - Depreciated! Not used!
 */
if (file_exists(dirname(__FILE__, 1).'/vendor/autoload.php')) {
    require_once dirname(__FILE__, 1).'/vendor/autoload.php';
    // var_dump(dirname(__FILE__, 1).'/vendor/autoload.php');
}

// use Exception;
use Synaptic4UParser\DB\DB;

    function flatten(array $array)
    {
        $return = [];
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });

        return $return;
    }

    function dir_cursor($path, $tree)
    {
        $cnt = 0;
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if ('.' !== $file) {
                        if ('..' !== $file) {
                            $newfile = $path.$file;
                            $newpath = $newfile.'/';
                            if (is_dir($newpath)) {
                                $tree[$path][$file] = dir_cursor($newpath, []);
                            } else {
                                $tree[$path][$file] = $newfile;
                            }
                        }
                    }
                    ++$cnt;
                }
                closedir($dh);
            }
        }

        return $tree;
    }

    function insert_log(array $params, string $sql)
    {
        $db = new DB();

        $result = $db->query($params, $sql);

        return $db->getLastId();
    }

    function parse_file(string $file)
    {
        $log_txt = file_get_contents($file);

        return explode("\n", $log_txt);
    }

    function parse_system_log(string $file)
    {
        $rows = parse_file($file);
        $nu_rows = sizeof($rows);
        $list = [];
        $columns = [];

        foreach ($rows as $row) {
            if (strlen($row > 10)) {
                $line = explode(' ', string_clear($row));
                // Jan 29 23:52:44
                $columns['loggedon'] = date_format(date_create(implode(' ', array_slice($line, 0, 3))), 'Y-m-d H:i:s');
                $txt = array_slice($line, 3);
                $columns['server'] = array_shift($txt);
                $columns['session'] = array_shift($txt);
                if (strpos($file, 'auth.log') > 0) {
                    $columns['user'] = array_shift($txt);
                // $id = insert_auth_log($columns);
                } else {
                    $columns['file'] = substr($file, strripos($file, '/') + 1);
                    // $id = insert_system_log($columns);
                }
                $columns['command_action'] = implode(' ', $txt);

                $id = (strpos($file, 'auth.log') > 0) ? insert_auth_log($columns) : insert_system_log($columns);
                array_push($list, $id);
            }
        }
        $rows = null;

        return [
            substr($file, strripos($file, '/') + 1) => [
                'nu_rows' => $nu_rows,
                'nu_result' => sizeof($list),
            ],
        ];

        // $result = [
        //     'nu_rows' => $nu_rows,
        //     'nu_result' => sizeof($list)
        // ];
    }

    function insert_auth_log(array $params)
    {
        $sql = 'insert into auth_logs(`loggedon`, `server`, `session`, `user`, `command_action`)values(?, ?, ?, ?, ?);';

        return insert_log([
            $params['loggedon'],
            $params['server'],
            $params['session'],
            $params['user'],
            $params['command_action'],
        ], $sql);
    }

    function insert_system_log(array $params)
    {
        $sql = 'insert into system_logs(`loggedon`, `server`, `session`, `file`, `command_action`)values(?, ?, ?, ?, ?);';

        return insert_log([
            $params['loggedon'],
            $params['server'],
            $params['session'],
            $params['file'],
            $params['command_action'],
        ], $sql);
    }

    function string_clear(string $string)
    {
        return str_replace('     ', ' ', str_replace('    ', ' ', str_replace('   ', ' ', str_replace('  ', ' ', str_replace(' :', ':', str_replace(' ;', ';', $string))))));
    }

    function walk_thru_logs($tree)
    {
        $system_logs = [
            'auth.log',
            'daemon.log',
            'debug',
            'kern.log',
            'mail.info',
            'mail.log',
            'mail.warn',
            'messages',
            'syslog',
            'user.log',
        ];
        $alternatives = [
            'alternatives.log',
        ];
        $dpkg = [
            'dpkg.log',
        ];
        $apache = [
            'access.log',
            'daemon-access.log',
            'daemon-error.log',
            'daemon-perf.log',
            'error.log',
            'modsec_audit.log',
            'modsec_debug.log',
            'modsec-perf.log',
            'other_vhosts_access.log',
        ];
        $ufw = [
            'ufw.log',
        ];
        $fail2ban = [
            'fail2ban.log',
        ];
        $lynis = [
            'lynis.log',
            'lynis-report.dat',
        ];
        $ignore = [
            'btmp',
            'wtmp',
            'faillog',
            'lastlog',
            'cloud-init.log',
            'cloud-init-output.log',
            'www-data-ip',
            'www-data-ip.dir',
            'www-data-ip.pag',
            'www-data-global.pag',
            'www-data-global.dir',
            'www-data-global',
        ];

        $result = [];
        foreach ($tree as $node) {
            foreach ($system_logs as $log) {
                // if(strpos($node, $log) > 0){
                //     $result[substr($node,strripos($node, "/")+1)] = parse_system_log($node);
                // }
                if ((strpos($node, $log) > 0) && (false == strpos($node, 'apache2'))) {
                    // $result[substr($node,strripos($node, "/")+1)] = $node;
                    $result[substr($node, strripos($node, '/') + 1)] = parse_system_log($node);
                }
            }
        }

        return $result;
    }

    function write_array_to_file(string $file, array $params)
    {
        $log = fopen($file, 'a');

        $content = json_encode($params, JSON_PRETTY_PRINT);

        fwrite($log, $content);

        fclose($log);
    }

    $tree = [];
    $file = '/home/mila/linode_backup/var/log/apache2/audit/www-data/20220130/20220130-0604/20220130-060418-YfYOQrud27QUzxDFRrx4AwAAAIU';

    $path = '/home/mila/linode_backup/var/log/';

    $tree = dir_cursor($path, $tree);
    $newtree = flatten($tree);

    $result = walk_thru_logs($newtree);

    write_array_to_file('/media/mila/daily/linode_scripts/tree.txt', $tree);
    write_array_to_file('/media/mila/daily/linode_scripts/flattened.txt', $newtree);
    write_array_to_file('/media/mila/daily/linode_scripts/result.txt', $result);

    // $node = json_decode(file_get_contents('/media/mila/daily/linode_scripts/result.txt'), true);

    // $node = array_values($node);
    print_r(json_encode($result), JSON_PRETTY_PRINT);
