<?php
namespace Synaptic4UParser\Structure;

use Synaptic4UParser\DB\DB;
use Synaptic4UParser\Core\Log;
use Synaptic4UParser\Tables\Tables;

class Structure{
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

        foreach($this->config->log_include as $key => $table){
            if(in_array($table->alias, $this->table_list)){
                
                $diff[$key] = $this->compareStructure($table);
                
                print_r('Nu: '.$cnt.' Exists: '.$key.': '.json_encode($diff[$key]).PHP_EOL);
            }else{
                $create[$key] = $this->tables->createTable($table);

                print_r('Nu: '.$cnt.' Created: '.$key.': '.json_encode($create[$key]).PHP_EOL);
            }
            $cnt++;
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
            'check' => sizeof($diff) + sizeof($diff2), 
            'config_diff' => $diff, 
            'db_diff' => $diff2
        ];

        if($result['check'] > 0){
            print_r("There's a difference".PHP_EOL);
            print_r(json_encode([$result, $columns, $table->columns], JSON_PRETTY_PRINT).PHP_EOL);

            // Add alter table functionality here.
        }

        return $result;
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