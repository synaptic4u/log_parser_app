<?php

namespace Synaptic4UParser\Core;

use Exception;
use Synaptic4UParser\Files\FileReader;
use Synaptic4UParser\Files\FileWriter;
use Synaptic4UParser\FrontTemplate\FrontTemplate;
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
 * App::showReport() :
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
    protected $setup;
    protected $setup_path;
    protected $options;
    protected $config_path;
    protected $config_path_list = [];
    protected $config_log_path;
    protected $config;
    protected $tree;
    protected $flat_tree;
    protected $file_reader;
    protected $file_writer;
    protected $parser;
    protected $result;

    /**
     * Constructor will load all the config files:
     * config_path_list.json -> JSON list of all the directories to step through to build the file list.
     * config.json -> JSON object of log files to parse.
     * Creates the FileReader, FileWriter & Parser instances.
     * Calls the main Parser::loadLogs to run the log parsing.
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

            $this->config_log_path = dirname(__FILE__, 4).'/config_path_list.json';
            $this->config_path = dirname(__FILE__, 4).'/config.json';
            $this->setup_path = dirname(__FILE__, 4).'/setup.json';

            $this->file_reader = new FileReader();

            $this->file_writer = new FileWriter();

            $this->config = $this->readConfig($this->config_path);
            $this->config_path_list = $this->readConfig($this->config_log_path);
            $this->setup = $this->readConfig($this->setup_path);

            $this->options = $this->buildOptions();

            $this->front_template = new FrontTemplate($this->options['UI']);

            $this->initDisplay();

            $this->cyclePathList();

            $finish = microtime(true);

            $this->result['app_timer'] = [
                'date_time' => date('Y-m-d H:i:s'),
                'start' => $start,
                'finish' => $finish,
                'duration min:sec' => (($finish - $start) > 60) ? (floor(($finish - $start) / 60)).':'.(($finish - $start) % 60) : '0:'.(($finish - $start) % 60),
                'duration sec.microseconds' => $finish - $start,
            ];

            $this->showReport();

            $this->displayFinished();

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
     * Calls FileWriter::appendArrayToFile()
     * Prints the final result to the screen.
     */
    public function showReport()
    {
        $this->file_writer->appendArrayToFile('/structure_files/result.txt', $this->result);
        print_r(json_encode($this->result, JSON_PRETTY_PRINT).PHP_EOL);
    }

    protected function cyclePathList()
    {
        foreach ($this->config_path_list->log_path as $path) {
            $start_loop = microtime(true);

            print_r($path.PHP_EOL);

            $this->getTree($path);

            $this->writeTree();

            $this->buildStructure();

            $this->parser = new Parser($this->config, $path);

            $this->result['log_files'] = $this->loadLogs();
            $finish_loop = microtime(true);

            $result['app_timer'] = [
                'Date & Time' => date('Y-m-d H:i:s'),
                'Log Path' => $path,
                'Start' => $start_loop,
                'Finish' => $finish_loop,
                'Duration min:sec' => (($finish_loop - $start_loop) > 60) ? (floor(($finish_loop - $start_loop) / 60)).':'.(($finish_loop - $start_loop) % 60) : '0:'.(($finish_loop - $start_loop) % 60),
                'Duration sec.microseconds' => $finish_loop - $start_loop,
            ];

            print_r(json_encode($result, JSON_PRETTY_PRINT).PHP_EOL);
        }
    }

    protected function displayFinished()
    {
        $this->front_template->finished();
    }

    protected function initDisplay()
    {
        $this->front_template->display();
    }

    protected function buildOptions(): array
    {
        $options = [];

        foreach ($this->setup->options as $option => $type) {
            foreach ($type as $key => $value) {
                if (1 === (int) $value) {
                    $class = strtoupper($key);

                    $full_class = '\Synaptic4UParser\\'.$option.'\\'.$class.'\\'.$class;
                    print_r('Class: '.$class.' Full class: '.$full_class.PHP_EOL);
                    $options[$option] = ('DB' === (string) $option) ? $full_class : new $full_class();
                }
            }
        }

        return $options;
    }

    /**
     * Reads the config_path file -> List of all directories to search through.
     */
    protected function readConfig(string $config_path): mixed
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
    protected function getTree($path)
    {
        $tree = new Tree();

        $this->tree = $tree->buildTree($path, []);

        $this->flat_tree = $tree->flattenTree($this->tree);
    }

    /**
     * Writes both tree structures to file.
     */
    protected function writeTree()
    {
        $this->file_writer->writeArrayToFile('/structure_files/tree.txt', $this->tree);

        $this->file_writer->writeArrayToFile('/structure_files/flattened.txt', $this->flat_tree);
    }

    /**
     * Builds a comparison data structure between the config.json file & the mysql database.
     * Then attempts to create the tables in the database that do not exist from the alias structure in the config file.
     * Lastly writes the structure to the screen and log file.
     */
    protected function buildStructure()
    {
        $structure = new Structure($this->config);
        $structure->parse();
    }

    /**
     * Cycles through the tree array and parses each log file into the database.
     *
     * @return array : Associative array with results
     */
    protected function loadLogs(): array
    {
        return $this->parser->loadLogs($this->flat_tree);
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    protected function error($msg)
    {
        new Log($msg, 'error');
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    protected function log($msg)
    {
        new Log($msg, 'activity');
    }
}
