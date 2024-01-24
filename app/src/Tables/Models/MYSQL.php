<?php

namespace Synaptic4UParser\Tables\Models;

use Exception;
use Synaptic4UParser\DB\DB;
use Synaptic4UParser\Logs\Log;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Tables\Models\ITablesDB;

class MYSQL implements ITablesDB
{
    protected $db;

    public function __construct($options)
    {
        try {
            $db = '\\Synaptic4UParser\\DB\\'.$options['DB'];
            $this->db = new DB(new $db());
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);
        }
    }

    /**
     * Reads a list of all the tables from the database.
     *
     * @return mixed : Returns a std::Class object of results
     */
    public function readTablesList(): mixed
    {
        $table_list = [];
        $sql = 'show tables where 1=?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $table_list[] = $res[0];
        }

        return $table_list;
        // print_r(json_encode($table_list, JSON_PRETTY_PRINT).PHP_EOL);
    }

    /**
     * Reads all the column names from the given table.
     *
     * @param mixed $table : a std::Class object
     *
     * @return mixed : array containing the table's column names with logid column removed
     */
    public function readTableColumns($table): mixed
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

    /**
     * Creates the table in the database.
     * Column names are dynamically allocated from the $table->columns property.
     *
     * @param [type] $table : std::Class object containing the table's alias and columns
     *
     * @return array : array containing table name, row count and success status
     */
    public function createTable($table): array
    {
        $sql = 'CREATE TABLE IF NOT EXISTS '.$table->alias.'( `logid` int(11) auto_increment primary key,';

        foreach ($table->columns as $key => $column) {
            if ('loggedon' == $column) {
                $sql .= '`'.$column.'` datetime(6) default null, index `'.$column.'`(`'.$column.'`),';
            } else {
                if ($key === array_key_last($table->columns)) {
                    $sql .= '`'.$column.'` longtext default null, index `'.$column.'`(`'.$column.'`(768))';
                } else {
                    if ('daemon_access' === $table->alias && 'method_params' == $column) {
                        $sql .= '`'.$column.'` longtext default null, index `'.$column.'`(`'.$column.'`(768)),';
                    } else {
                        $sql .= '`'.$column.'` longtext default null, index `'.$column.'`(`'.$column.'`(768)),';
                    }
                }
            }
        }

        $sql .= ');';

        $result = $this->db->query([], $sql);

        $count = $this->db->getrowCount();
        $success = $this->db->getStatus();

        return [
            'nu' => null,
            'created' => null,
            'alias' => $table->alias,
            'count' => $count,
            'success' => $success,
        ];
    }

    /**
     * Creates a MySQL insert statements dynamically.
     *
     * @param array  $columns : Associative array of table's column names and field content
     * @param string $alias   : Table name
     *
     * @return int : The last inserted ID
     */
    public function insertLog(array $columns, string $alias): int
    {
        $sql = 'insert into '.$alias.'( ';
        $param = '';

        foreach ($columns as $key => $column) {
            if ($key === array_key_last($columns)) {
                $sql .= '`'.$key.'`';
                $param .= '?';
            } else {
                $sql .= '`'.$key.'`,';
                $param .= '?,';
            }
        }

        $sql .= ')values('.$param.');';

        $result = $this->db->query(array_values($columns), $sql);

        return $this->db->getLastId();
    }

    /**
     * Creates insert statement for log_dump table.
     *
     * @param string $line : Field contents (log file row or entire log file)
     * @param string $file : full path name of file
     *
     * @return int : The last inserted ID
     */
    public function dumpLog(string $line, string $file): int
    {
        $sql = 'insert into log_dump(`file`, `contents`)values(?, ?);';

        $result = $this->db->query([
            $file,
            $line,
        ], $sql);

        return $this->db->getLastId();
    }

    /**
     * Creates the log_dump table.
     *
     * @return mixed : Return the query's status
     */
    public function createLogDump(): mixed
    {
        $sql = 'CREATE TABLE IF NOT EXISTS log_dump (`dumpid` int(11) NOT NULL AUTO_INCREMENT,`dumpdate` datetime default current_timestamp,`file` text not null,`contents` longtext default NULL,PRIMARY KEY (`dumpid`),KEY `dumpdate` (`dumpdate`),KEY `content` (`contents`(768)),KEY `file` (`contents`(768)));';

        $result = $this->db->query([], $sql);

        return $this->db->getStatus();
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    protected function error($msg)
    {
        new Log($msg, new Error());
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    protected function log($msg)
    {
        new Log($msg, new Activity());
    }
}
