<?php

namespace Synaptic4UParser\Tables;

use Synaptic4UParser\DB\DB;
use Synaptic4UParser\Core\Log;

class Tables{

    protected $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function readTablesList():mixed
    {
        $table_list = [];
        $sql = 'show tables where 1=?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $table_list[] = $res[0];
        }

        return $table_list;
    }

    public function readTableColumns($table):mixed
    {
        $columns = [];
        $sql = 'show columns from '.$table->alias.' where 1=?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $columns[] = $res[0];
        }

        array_shift($columns);

        return $columns;
    }

    public function createTable($table):array
    {
        $sql = 'CREATE TABLE IF NOT EXISTS '.$table->alias.'( `logid` int(11) auto_increment primary key,';

        foreach($table->columns as $key => $column){
            if($column == 'loggedon'){
                $sql .= '`'.$column.'` datetime(6) default null, index `'.$column.'`(`'.$column.'`),';
            }else{
                if($key === array_key_last($table->columns)){
                    $sql .= '`'.$column.'` text default null, index `'.$column.'`(`'.$column.'`)';
                }else{
                    if($table->alias === 'daemon_access' && $column == 'method_params'){
                        $sql .= '`'.$column.'` text default null, index `'.$column.'`(`'.$column.'`),';
                    }else{
                        $sql .= '`'.$column.'` text default null, index `'.$column.'`(`'.$column.'`),';
                    }
                }
            }
        }

        $sql .= ');';

        $result = $this->db->query([], $sql);

        $count = $this->db->getrowCount();
        $success = $this->db->getStatus();
        
        return [
            'alias' => $table->alias,
            'count' => $count, 
            'success' => $success
        ];
    }

    public function insertLog(array $columns, string $alias):int
    {
        $sql = 'replace into '.$alias.'( ';
        $param = '';

        foreach($columns as $key => $column){
            if($key === array_key_last($columns)){
                $sql .= '`'.$key.'`';
                $param .= '?';
            }else{
                $sql .= '`'.$key.'`,';
                $param .= '?,';
            }
        }

        $sql .= ')values('.$param.');';

        $result = $this->db->query(array_values($columns), $sql);

        return $this->db->getLastId();
    }

    public function dumpLog(string $line, string $file):int
    {
        $sql = "replace into log_dump(`file`, `contents`)values(?, ?);";

        $result = $this->db->query([
            $file,
            $line
        ], $sql);

        return $this->db->getLastId();
    }

    public function createLogDump():mixed
    {
        $sql = "CREATE TABLE IF NOT EXISTS log_dump (`dumpid` int(11) NOT NULL AUTO_INCREMENT,`dumpdate` datetime default current_timestamp,`file` text not null,`contents` text default NULL,PRIMARY KEY (`dumpid`),KEY `dumpdate` (`dumpdate`),KEY `content` (`contents`(768)),KEY `file` (`contents`(768)));";

        $result = $this->db->query([], $sql);

        return $this->db->getStatus();
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