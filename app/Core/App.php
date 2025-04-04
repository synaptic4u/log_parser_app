<?php

namespace Synaptic4UParser\Core;

use Exception;
use Synaptic4UParser\Files\Reader\FileReader;
use Synaptic4UParser\Files\Writer\FileWriter;
use Synaptic4UParser\Files\Writer\FileWriterArray;
use Synaptic4UParser\FrontTemplate\FrontTemplate;
use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;
use Synaptic4UParser\Parser\Parser;
use Synaptic4UParser\Structure\Structure;
use Synaptic4UParser\Tree\Tree;

// To call from the CLI: php /var/www/LogParserApp/app.php
// CREATE DATABASE /*!32312 IF NOT EXISTS*/ `logs` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

/**
 * Class::App :
 * Contains the functionality to initialise and run the whole app to parse all log files.
 *
 * App::construct() :
 * Constructor will load all the config files.
 * Creates the FileReader, FileWriter & Parser instances.
 * Calls the main Parser::loadLogs to run the log parsing.
 *
 * App::finalReport() :
 * Writes the final result to file and prints to screen.
 *
 * App::readConfig() :
 * Reads the config_path file.
 *
 * App::getTree() :
 * Builds the tree of all the files to be parsed.
 *
 * App::writeTree() :
 * Writes tree structures to file.
 *
 * App::buildStructure() :
 * Checks the variance between the config.json file & the mysql database structure.
 * Attempts to create non-existant tables.
 *
 * App::loadLogs() :
 * Cycles through the tree array and parses each log file into the database.
 */
class App
{
    private $setup;
    private $setup_path;
    private $options;
    private $config_path;
    private $config_path_list;
    private $config_log_path;
    private $config;
    private $tree;
    private $flat_tree;
    private $file_reader;
    private $file_writer;
    private $parser;
    private $result;
    private $front_template;

    /**
     * Constructor will load all the config files:
     * config_path_list.json -> JSON list of all the directories to step through to build the file list.
     * config.json -> JSON object of log files to parse.
     * Creates the FileReader, FileWriter instances.
     *
     * @param mixed $setup
     */
    public function __construct()
    {
        try {
            $start = microtime(true);

            $this->result = [
                'app_timer' => [],
                'log_files' => [],
            ];

            $this->config_log_path = dirname(__FILE__, 3).'/config_path_list.json';
            $this->config_path = dirname(__FILE__, 3).'/config.json';
            $this->setup_path = dirname(__FILE__, 3).'/setup.json';

            $this->file_reader = new FileReader();

            $this->file_writer = new FileWriter();

            $this->config = $this->readConfig($this->config_path);
            $this->config_path_list = $this->readConfig($this->config_log_path);
            $this->setup = $this->readConfig($this->setup_path);

            $this->options = $this->buildOptions();

            $this->front_template = new FrontTemplate(new ('\\Synaptic4UParser\\FrontTemplate\\Views\\'.$this->options['UI'])());

            $this->displayInit();

            $this->cyclePathList();

            $finish = microtime(true);

            $this->result['app_timer'] = [
                'date_time' => date('Y-m-d H:i:s'),
                'start' => $start,
                'finish' => $finish,
                'duration_min_sec' => (($finish - $start) > 60) ? (floor(($finish - $start) / 60)).':'.(($finish - $start) % 60) : '0:'.(($finish - $start) % 60),
                'duration_sec_microseconds' => $finish - $start,
            ];

            $this->fullReport();

            $this->displayComplete();

            $this->log([
                'Location' => __METHOD__.'()',
                'config' => json_encode($this->config, JSON_PRETTY_PRINT),
            ]);
        } catch (Exception $e) {
            // Errors currently print to screen
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);
        }
    }

    /**
     * Writes the final result to file: result.txt.
     * Calls FileWriter::appendToFile() -> passes FileWriterArray for FileWriter interface.
     * Prints the final result to the screen.
     */
    public function fullReport()
    {
        $this->file_writer->appendToFile(new FileWriterArray(), '/structure_files/result.txt', $this->result);

        $this->log([
            'Location' => __METHOD__.'()',
            'full_report' => json_encode($this->result, JSON_PRETTY_PRINT),
        ]);

        $check = $this->front_template->fullReport($this->result);

        if(0 === (int) $check){
            throw new Exception('Cannot print time report.');
        }
    }

    /**
     * Cycles through the path list.
     * Calls geTree(),  writeTree() & buildStructure().
     * Creates Parser instance.
     * Calls the main Parser::loadLogs to run the log parsing.
     */
    private function cyclePathList()
    {
        foreach ($this->config_path_list->log_path as $path) {
            print_r($path.PHP_EOL);

            $this->getTree($path);

            $this->writeTree();

            $this->buildStructure();

            $this->parser = new Parser($this->config, $path, $this->options);

            $this->result['log_files'] = $this->loadLogs();
        }
    }

    private function displayComplete()
    {
        $check = $this->front_template->finished();


        if(0 === (int) $check){
            throw new Exception('Cannot print successfully completed message.');
        }
    }

    /**
     * Calls FrontTemplate::display()
     * Returns the loading display for app.
     */
    private function displayInit()
    {
        $check = $this->front_template->display();

        if(0 === (int) $check){
            throw new Exception('Cannot print introduction.');
        }
    }

    /**
     * Cylces through options from setup.json.
     * Stores selected options in options array.
     *
     * @return array $options : Associative array of selected options
     */
    private function buildOptions(): array
    {
        $options = [];

        try {
            foreach ($this->setup->options as $option => $type) {
                foreach ($type as $key => $value) {
                    if (1 === (int) $value) {
                        $class = strtoupper($key);

                        $options[$option] = $class;
                    }
                }
            }

            return $options;
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);

            exit(
                'Location: '.__METHOD__.'()'.PHP_EOL.
                'error: '.$e->__toString().PHP_EOL.PHP_EOL
            );
        }
    }

    /**
     * Reads the config_path file -> List of all directories to search through.
     */
    private function readConfig(string $config_path): mixed
    {
        return $this->file_reader->readJSONFile($config_path, 1);
    }

    /**
     * Creates new Tree instance.
     * $tree->buildTree() -> Builds the tree which is a multi-dimensional array of all the files to be parsed.
     * $tree->flattenTree() -> Results in a one dimensional array of files to be parsed.
     *
     * @param mixed $path : linux formatted directory path string
     */
    private function getTree($path)
    {
        $tree = new Tree($this->config->file_exclude_types);

        $this->tree = $tree->buildTree($path, []);

        $this->flat_tree = $tree->flattenTree($this->tree);
    }

    /**
     * Writes both tree structures to file.
     */
    private function writeTree()
    {
        $this->file_writer->writeToFile(new FileWriterArray(), '/structure_files/tree.txt', $this->tree);

        $this->file_writer->writeToFile(new FileWriterArray(), '/structure_files/flattened.txt', $this->flat_tree);
    }

    /**
     * Builds a comparison data structure between the config.json file & the mysql database.
     * Then attempts to create the tables in the database that do not exist from the alias structure in the config file.
     * Lastly writes the structure to the screen and log file.
     */
    private function buildStructure()
    {
        $structure = new Structure($this->config, $this->options);
        $structure->parse();
    }

    /**
     * Cycles through the tree array and parses each log file into the database.
     *
     * @return array : Associative array with results
     */
    private function loadLogs(): array
    {
        return $this->parser->loadLogs($this->flat_tree);
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    private function error($msg)
    {
        new Log($msg, new Error());
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    private function log($msg)
    {
        new Log($msg, new Activity());
    }
}
