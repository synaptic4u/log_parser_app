<?php

namespace Synaptic4UParser\Parser;


use Synaptic4UParser\Core\Log;
use Synaptic4UParser\Files\FileReader;
use Synaptic4UParser\Tables\Tables;
use DateTime;

class Parser{
    protected $file_reader;
    protected $tree;
    protected $config;
    protected $path;
    protected $tables;

    public function __construct($config, $path)
    {
        $this->config = $config;
        $this->path = $path;
        $this->file_reader = new FileReader();
        $this->tables = new Tables();
    }

    public function loadLogs(array $tree): array
    {
        $result = [];
        $accept = [];
        $reject = [];
        $dump = [];
        $config_dump = [];

        $names = array_keys($tree);

        foreach($this->config->log_include as $table){
            $name = $table->name;
            $alias = $table->alias;
            array_walk($names, function($value, $key, $table) use (&$accept){
                if(substr_count($value, $table->name) > 0){
                    $accept[$value] = $table->alias;
                } 
            }, $table);
        }

        // Result contains false positives - need to clean it after.
        foreach($this->config->log_exclude as $name){
            array_walk($names, function($value, $key, $name) use (&$reject){
                if(substr_count($value, $name) > 0){
                    $reject[$value] = $name;
                } 
            }, $name);
        }
        
        $reject = array_diff_key($reject, $accept);

        // Array of logs that haven't been assigned
        $dump = array_diff_key($tree, $accept, $reject);

        if(sizeof($this->config->log_dump) > 0){
            foreach($this->config->log_dump as $name){
                array_walk($names, function($value, $key, $name) use (&$config_dump){
                    if(substr_count($value, $name) > 0){
                        $config_dump[$value] = $name;
                    } 
                }, $name);
            }
        }

        $this->log(json_encode([
            'Location' => __METHOD__.'() : '.$this->path,
            "Accepted log files parsed to own tables" => (sizeof($accept) > 0) ? $accept : 'No files to log',
            "Log files to be rejected:" => (sizeof($reject) > 0) ? $reject : 'No files to log',
            "Log files parsed to log_dump" => (sizeof($dump) > 0) ? $dump : 'No files to log'
        ], JSON_PRETTY_PRINT));


        (sizeof($dump) > 0) ? $this->tables->createLogDump() : ""
        ;
        foreach ($accept as $key => $alias) {
            $result[$key] = $this->parseLog($tree[$key], $alias);
        }

        $dump = array_merge($dump, $config_dump);

        foreach ($dump as $key => $name) {
            $result[$key] = $this->dumpLog($tree[$key]);
        }

        return $result;
    }

    protected function parseLog(string $file, string $alias):array
    {
        $start = microtime(true);

        $this->log([
            'Location' => __METHOD__.'(): '.substr($file, strripos($file, "/")+1),
            'alias' => $alias
        ]);

        $result = [];
        $list = [];
        $columns = [];

        $rows = $this->file_reader->parseFile($file);

        // if($alias === 'fail2ban'){
        //     $rows = $this->cleanFile($rows);
        // }

        $nu_rows = sizeof($rows);
        
        if($nu_rows > 0){
            foreach ($rows as $key => $row) {
                $line = $this->file_reader->stringClear($row);
                if (strlen($line > 10)) {
                    $columns = $this->parseLine($line, $alias);
                    $id = $this->insertLog($columns, $alias);
                    array_push($list, $id);
                }else{
                    $nu_rows--;
                }
            }
        }
        
        $rows = null;

        $finish = microtime(true);

        $result = [
            substr($file, strripos($file, "/")+1) => [
                'start' => $start,
                'finish' => $finish,
                'duration min:sec' => (($finish - $start) > 60) ? (floor(($finish - $start)/60)).':'.(($finish - $start)%60) : '0:'.(($finish - $start)%60),
                'duration sec.microseconds' => $finish - $start,
                'nu_rows' => ($nu_rows > 0) ? $nu_rows : "File is empty or the row length is less then 10 charachters.",
                'nu_result' => sizeof($list)
            ]
        ];

        $this->log([
            'Location' => __METHOD__.'(): '.substr($file, strripos($file, "/")+1),
            'alias' => $alias,
            "result" => json_encode($result, JSON_PRETTY_PRINT)
        ]);
        
        return $result;
    }

    protected function cleanFile(array $rows):array
    {
        // Cleans the Fail2ban log file.
        // Fail2ban errors are writing to multiple lines in the file.
        // Creates errors when parsing the file.

        $rows_result = [];
        $rows_reverse = [];
        $buffer = '';

        $rows_reverse = array_reverse($rows);

        foreach($rows_reverse as $key =>$row){
            $line = $this->file_reader->stringClear($row);
        
            if(substr($line, 0, 2) !== '22'){
                $buffer .= $line.' '.$buffer;
            }
            $rows_result[] = $line.' '.$buffer;
        }
        
        return $rows_result;
    }

    protected function stringClean(array $columns):array
    {
        // Sanitizes the string - Been having issues with mysql importing database from a mysql dump file.
        // Still going to fiddle here and see what is what!!!
        foreach ($columns as $key => $string) {
            $columns[$key] = str_replace('"',"",$string);
            $columns[$key] = str_replace("'","",$string);
            $columns[$key] = str_replace(',',"~",$string);
            $columns[$key] = str_replace('`',"#",$string);
            $columns[$key] = str_replace("[","",str_replace("]","",$string));    
        }

        return $columns;
    }

    protected function parseLine(string $line, string $alias): array
    {
        $columns = [];
        $columns_raw = explode(" ", $line);
        $table = $this->config->log_include->$alias;

        $perf = 0;

        foreach($table->columns as $key => $column){
            if($key >= sizeof($columns_raw)){
                break;
            }
            // Normal date parsing
            if($column === 'loggedon'){
                // First test the format
                $check = str_replace("[","",str_replace("]","",$columns_raw[0]));
                
                if(is_numeric(substr($check, 0, 2))){
                    if($table->loggedon_format == 'YYYY-MM-DD HH:mm:ss')
                    {
                        $date_string = implode(" ", array_slice($columns_raw, 0, 2));
                        // print_r($date_string.PHP_EOL);
                        if(strrpos($date_string, ":") === strlen($date_string)-1){
                            $date_string = substr_replace($date_string, "", strlen($date_string)-1, 1);
                        }
                        $columns[$column] = date_format(date_create($date_string), "Y-m-d H:i:s");
                    }
                    if($table->loggedon_format == 'YYYY-MM-DD HH:mm:ss,m-3')
                    {
                        $columns[$column] = date_format(date_create(str_replace(",",".",implode(" ", array_slice($columns_raw, 0, 2)))), "Y-m-d H:i:s");
                    }
                    if($table->loggedon_format == 'YYYY-MM-DD HH:mm:ss.m-6')
                    {
                        $date_string = implode(" ", array_slice($columns_raw, 0, 2));
                        $date_string = str_replace("[", "", str_replace("]", "", $date_string));
                        // print_r($date_string.PHP_EOL);
                        $columns[$column] = date_format(date_create($date_string), "Y-m-d H:i:s");
                    }
                    $columns_raw = array_slice($columns_raw, 2);
                }else{
                    if($table->loggedon_format == '[Day MMM DD HH:mm:ss.m-6 YYYY]' || $table->alias == 'daemon_error')
                    {
                        // [Wed Jan 26 21:15:36.983242 2022]
                        $date_string = implode(" ", array_slice($columns_raw, 0, 5));
                        $date_string = str_replace("[", "", str_replace("]", "", $date_string));
                        // print_r($date_string.PHP_EOL);
                        $date =  DateTime::createFromFormat('D M j G:i:s.u Y', $date_string);
                        $columns[$column] = $date->format('Y-m-d H:i:s.u');
                        // $columns[$column] = date_format(date_create(implode(" ", array_slice($columns_raw, 0, 5))), "Y-m-d H:i:s");
                        $columns_raw = array_slice($columns_raw, 5);
                    }
                    if($table->loggedon_format == 'MMM DD HH:mm:ss')
                    {
                        $date_string = implode(" ", array_slice($columns_raw, 0, 3));
                        $columns[$column] = date_format(date_create($date_string), "Y-m-d H:i:s");
                        $columns_raw = array_slice($columns_raw, 3);
                    }
                }
            }else{
                // Specialized conditional parsing
                if($alias === 'daemon_error'){
                    if(substr_count($columns_raw[0], '[:error]') === 0){
                        if(substr_count($columns_raw[0], '[-:error]') === 0){
                            $columns[$column] = array_shift($columns_raw);
                            $client = 0;
                            foreach($columns_raw as $key => $val){
                                if(substr_count($val, 'unique_id') > 0){
                                    // echo '<br>Key: '.$key.' Val: '.$val;
                                    $columns['unique_id'] = implode(" ", array_splice($columns_raw, $key, 2));
                                }
                                if(substr_count($val, 'client') > 0 && $client === 0){
                                    // echo '<br>Key: '.$key.' Val: '.$val;
                                    $columns['client_ip'] = implode(" ", array_splice($columns_raw, $key, 2));
                                    $client = 1;
                                }
                            }
                            $columns['log'] = implode(" ", $columns_raw);
                            break;
                        }
                    }
                }
                
                // Normal field parsing
                if($key === array_key_last($table->columns)){
                    $columns[$column] = implode(" ", $columns_raw);
                }else{
                    // Specialized conditional parsing
                    if($alias === 'modsec_perf' || $alias === 'daemon_perf'){
                        if($column === 'PerfModSecInbound' || $perf === 1){
                            $perf = 1;
                            $columns[$column] = implode(" ",array_splice($columns_raw, 0, 2));
                        }
                    }else{
                        $temp = [];

                        if(substr_count($table->field_encapsulated, "brackets") === 1){
                            // print_r("brackets".PHP_EOL);
                            if(substr_count($columns_raw[0], '[') > 0 && substr_count($columns_raw[0], ']') > 0){
                                $columns[$column] = array_shift($columns_raw);
                            }elseif (substr_count($columns_raw[0], '[') > 0 && substr_count($columns_raw[0], ']') == 0) {
                                $temp[] = array_shift($columns_raw);
                                while(substr_count($columns_raw[0], '[') == 0 && substr_count($columns_raw[0], ']') == 0){
                                    $temp[] = array_shift($columns_raw);
                                }
                                if(substr_count($columns_raw[0], '[') == 0 && substr_count($columns_raw[0], ']') > 0){
                                    $temp[] = array_shift($columns_raw);
                                }
                                $columns[$column] = implode(" ", $temp);
                            }
                        }elseif(substr_count($table->field_encapsulated, "double quotes") === 1){
                            // print_r("double quotes".PHP_EOL);
                            if(substr_count($columns_raw[0], '"')  === 2 || substr_count($columns_raw[0], '"')  === 0){
                                $columns[$column] = array_shift($columns_raw);
                            }elseif (substr_count($columns_raw[0], '"') > 0 && substr($columns_raw[0], 0, 1) === '"') {
                                $temp[] = array_shift($columns_raw);
                                while(substr_count($columns_raw[0], '"')  === 0){
                                    $temp[] = array_shift($columns_raw);
                                }
                                if(substr_count($columns_raw[0], '"') > 0 && substr($columns_raw[0], strlen($columns_raw[0])-1) === '"'){
                                    $temp[] = array_shift($columns_raw);
                                }
                                $columns[$column] = implode(" ", $temp);
                            }
                        }else{
                            $columns[$column] = array_shift($columns_raw);
                        }
                    }
                }
            }
        }

        return $this->stringClean($columns);
    }

    protected function dumpLog(string $file)
    {

        $this->log([
            'Location' => __METHOD__.'(): '.substr($file, strripos($file, "/")+1)
        ]);

        $start = microtime(true);

        $rows = $this->file_reader->parseFile($file);
        
        if(substr_count($file, "apache2/audit/www-data") > 0){
            $blob = implode(" ", $rows);

            $id = $this->insertDump($blob, $file);

            $result = [
                substr($file, strripos($file, "/")+1) => [
                    'id' => $id,
                    'special_log_type' => "audit/www-data"
                ]
            ];
        }else{
            $nu_rows = sizeof($rows);
            $list = [];
            $columns = [];
    
            foreach ($rows as $row) {
                if (strlen($row > 10)) {
                    $line = $this->file_reader->stringClear($row);
    
                    $id = $this->insertDump($line, $file);
                    array_push($list, $id);
                }
            }
    
            $result = [
                substr($file, strripos($file, "/")+1) => [
                    'nu_rows' => $nu_rows,
                    'nu_result' => sizeof($list)
                ]
            ];
        }
        $rows = null;

        $finish = microtime(true);

        $this->log([
            'Location' => __METHOD__.'(): '.substr($file, strripos($file, "/")+1),
            "result" => json_encode($result, JSON_PRETTY_PRINT),
            'start' => $start,
            'finish' => $finish,
            'duration min:sec' => (($finish - $start) > 60) ? (floor(($finish - $start)/60)).':'.(($finish - $start)%60) : '0:'.(($finish - $start)%60),
            'duration sec.microseconds' => $finish - $start,
        ]);
    
        return $result;
    }

    protected function insertLog(array $columns, string $alias):int
    {
        return $this->tables->insertLog($columns, $alias);
    }

    protected function insertDump(string $line, string $file):int
    {
        return $this->tables->dumpLog($line, $file);
    }

    protected function error($msg)
    {
        new Log($msg, 'error');
    }

    protected function log($msg)
    {
        new Log($msg, 'activity');
    }
}

?>