<?php

namespace Synaptic4UParser\Structure;

use Synaptic4UParser\Core\Log;
use Synaptic4UParser\DB\DB;
use Synaptic4UParser\Tables\Tables;

/**
 * Class::Structure :
 * Contains the functionality to read and compare the structure between the config and database.
 *
 * Structure::parse() :
 * Compares DB schema and config structure. Creates non-existant tables.
 *
 * Structure::compareStructure() :
 * Compares the config and db table structure. Reports variance.
 * NB! Still need to write alter table functionality
 *
 * Structure::getRowCount() :
 * Queries the given table for total number of rows.
 *
 * Structure::getMaxLogID() :
 * Queries table for the max primary key : logid.
 */
class Structure
{
    protected $config;
    protected $db;
    protected $tables;
    protected $table_list;
    protected $log_structure;

    /**
     * Creates DB instance for the db member.
     *
     * Creates Table instance for the tables member.
     *
     * @param mixed $config : JSON Object from config file
     */
    public function __construct($config)
    {
        $this->config = $config;

        $this->db = new DB();

        $this->tables = new Tables();

        $this->table_list = $this->tables->readTablesList();
    }

    /**
     * Compares the existing database schema with the config include structure.
     * Creates the database table from the config structure.
     */
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

    /**
     * Compares the config and db table structure.
     * Creates a report for any differences.
     *
     * @param mixed $table : Std::Class object containg the table structure
     *
     * @return array : Associative array with the structure variance
     */
    public function compareStructure($table): array
    {
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

    /**
     * Queries the given table for total number of rows.
     *
     * @param mixed $table
     *
     * @return int : Number of table rows
     */
    protected function getRowCount($table): int
    {
        $sql = 'select count(*) as nu_rows from '.$table.' where 1 = ?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $count = $res['nu_rows'];
        }

        return $count;
    }

    /**
     * Queries table for the max primary key : logid.
     *
     * @param mixed $table : std::Class
     */
    protected function getMaxLogID($table): mixed
    {
        $sql = 'select max(logid) as max_logid from '.$table.' where 1 = ?;';

        $result = $this->db->query([1], $sql);

        foreach ($result as $res) {
            $id = $res['max_logid'];
        }

        return $id;
    }

    /**
     * Prepped to later introduce error logging. Not functional in this version.
     *
     * @param array $msg : Error message
     */
    protected function error($msg)
    {
        new Log($msg, 'error');
    }

    /**
     * Activity logging. Not fully functional in this version.
     *
     * @param array $msg : Message
     */
    protected function log($msg)
    {
        new Log($msg, 'activity');
    }
}
