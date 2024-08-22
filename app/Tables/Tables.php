<?php

namespace Synaptic4UParser\Tables;

use Exception;
use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

/**
 * Class::Tables :
 * Primary class to handle database table interactions : reading, creating, inserting.
 *
 * Table::construct() :
 * Initializes Class::DB.
 *
 * Table::readTablesList() :
 * Reads a list of all the tables from the database.
 *
 * Table::readTableColumns() :
 * Reads all the column names from the given table.
 *
 * Table::createTable() :
 * Creates the table in the database with dynamic table alias and field names.
 *
 * Table::insertLog() :
 * Creates a MySQL insert statements dynamically.
 *
 * Table::dumpLog() :
 * Creates insert statement for log_dump table.
 *
 * Table::createLogDump() :
 * Creates the log_dump table.
 */
class Tables
{
    protected $model;

    /**
     * Initialises Model class.
     *
     * @param mixed $options
     */
    public function __construct($options)
    {
        try {
            $model = __NAMESPACE__.'\\Models\\'.$options['DB'];
            $this->model = new $model($options);
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
        return $this->model->readTablesList();
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
        return $this->model->readTableColumns($table);
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
        return $this->model->createTable($table);
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
        return $this->model->insertLog($columns, $alias);
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
        return $this->model->dumpLog($line, $file);
    }

    /**
     * Creates the log_dump table.
     *
     * @return mixed : Return the query's status
     */
    public function createLogDump(): mixed
    {
        return $this->model->createLogDump();
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
