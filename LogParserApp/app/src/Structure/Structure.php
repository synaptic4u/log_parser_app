<?php

namespace Synaptic4UParser\Structure;

use Synaptic4UParser\Core\Log;
use Synaptic4UParser\DB\DB;
use Synaptic4UParser\Tables\Tables;

class Structure
{
    protected $config;
    protected $db;
    protected $tables;
    protected $table_list;
    protected $log_structure;

    public function __construct($config)
    {
        $this->config = $config;

        $this->db = new DB();

        $this->tables = new Tables();

        $this->table_list = $this->tables->readTablesList();
    }

    public function parse()
    {
        $diff = [];
        $create = [];
        $cnt = 0;

        foreach ($this->config->log_include as $key => $table) {
            if (in_array($table->alias, $this->table_list)) {
                $diff[$key] = $this->compareStructure($table);

                $message = 'Nu: '.$cnt.' Exists: '.$key.': '.json_encode($diff[$key], JSON_PRETTY_PRINT);
                $this->log($message);
                print_r($message.PHP_EOL);
            } else {
                $create[$key] = $this->tables->createTable($table);

                $message = 'Nu: '.$cnt.' Created: '.$key.': '.json_encode($create[$key]);
                $this->log($message);
                print_r($message.PHP_EOL);
            }
            ++$cnt;
        }
        // print_r(json_encode($this->config->log_include, JSON_PRETTY_PRINT));
        // print_r(json_encode($this->config->log_exclude, JSON_PRETTY_PRINT));
    }

    public function compareStructure($table)
    {
        // Comparison evaluated on using the config & db as the source, it will report any differences.
        // Will later write alter table functionality.
        $columns = $this->tables->readTableColumns($table);

        $diff = array_diff($table->columns, $columns);
        $diff2 = array_diff($columns, $table->columns);

        $result = [
            'alias' => $table->alias,
            'nu of rows' => $this->getRowCount($table->alias),
            'max logid' => $this->getMaxLogID($table->alias),
            'config structure variance' => (sizeof($diff) > 0) ? $diff : 'None',
            'database structure variance' => (sizeof($diff2) > 0) ? $diff2 : 'None',
        ];

        if ((sizeof($diff) + sizeof($diff2)) > 0) {
            $result['variance'] = json_encode([$columns, $table->columns], JSON_PRETTY_PRINT);
        }

        return $result;
    }

    protected function getRowCount($table)
    {
        $sql = 'select count(*) as nu_rows from '.$table.' where 1 = ?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $count = $res['nu_rows'];
        }

        return $count;
    }

    protected function getMaxLogID($table)
    {
        $sql = 'select max(logid) as max_logid from '.$table.' where 1 = ?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $id = $res['max_logid'];
        }

        return $id;
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
