<?php

namespace Synaptic4UParser\Structure;

use Exception;
use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;
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
    protected $view;
    protected $model;
    protected $tables;
    protected $table_list;
    protected $log_structure;

    /**
     * Creates DB instance for the db member.
     *
     * Creates Table instance for the tables member.
     *
     * @param mixed $config  : JSON Object from config file
     * @param mixed $options
     */
    public function __construct($config, $options)
    {
        try {
            $this->config = $config;

            $view = __NAMESPACE__.'\\Views\\'.$options['UI'];
            $this->view = new $view();

            $model = __NAMESPACE__.'\\Models\\'.$options['DB'];
            $this->model = new $model($options);

            $this->tables = new Tables($options);

            $this->table_list = $this->tables->readTablesList();
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);
        }
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

                $diff[$key]['nu'] = $cnt;
                $diff[$key]['exists'] = $key;

                $this->log([
                    'Location' => __METHOD__.'()',
                    'message' => $diff[$key],
                ]);

                $this->view->exists($diff[$key]);
            // print_r($diff[$key].PHP_EOL);
            } else {
                $create[$key] = $this->tables->createTable($table);

                $create[$key]['nu'] = $cnt;
                $create[$key]['created'] = $key;

                $this->log([
                    'Location' => __METHOD__.'()',
                    'message' => $create[$key],
                ]);

                $this->view->created($create[$key]);
                // print_r($create[$key].PHP_EOL);
            }
            ++$cnt;
        }

        $this->view->processing();
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
            'nu' => null,
            'exists' => null,
            'alias' => $table->alias,
            'nu_of_ows' => $this->model->getrowCount($table->alias),
            'max_logid' => $this->model->getMaxLogID($table->alias),
            'config_structure_variance' => (sizeof($diff) > 0) ? $diff : 'None',
            'database_structure_variance' => (sizeof($diff2) > 0) ? $diff2 : 'None',
        ];

        if ((sizeof($diff) + sizeof($diff2)) > 0) {
            $result['variance'] = json_encode([$columns, $table->columns], JSON_PRETTY_PRINT);
        }

        return $result;
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
