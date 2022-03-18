<?php

namespace Synaptic4UParser\Core;

use Exception;
use Synaptic4UParser\Files\FileReader;
use Synaptic4UParser\Files\FileWriter;
use Synaptic4UParser\Parser\Parser;
use Synaptic4UParser\Structure\Structure;
use Synaptic4UParser\Tree\Tree;

// To call from the CLI: php /var/www/LogParserApp/app.php
// CREATE DATABASE /*!32312 IF NOT EXISTS*/ `logs` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

class App
{
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

            $this->file_reader = new FileReader();

            $this->file_writer = new FileWriter();

            $this->config = $this->readConfig($this->config_path);
            $this->config_path_list = $this->readConfig($this->config_log_path);

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

            $finish = microtime(true);

            $this->result['app_timer'] = [
                'date_time' => date('Y-m-d H:i:s'),
                'start' => $start,
                'finish' => $finish,
                'duration min:sec' => (($finish - $start) > 60) ? (floor(($finish - $start) / 60)).':'.(($finish - $start) % 60) : '0:'.(($finish - $start) % 60),
                'duration sec.microseconds' => $finish - $start,
            ];

            $this->showReport();

            $this->log([
                'Location' => __METHOD__.'()',
                'config' => json_encode($this->config, JSON_PRETTY_PRINT),
            ]);
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);
        }
    }

    public function showReport()
    {
        $this->file_writer->appendArrayToFile('/structure_files/result.txt', $this->result);
        print_r(json_encode($this->result, JSON_PRETTY_PRINT).PHP_EOL);
    }

    protected function readConfig(string $config_path): mixed
    {
        return $this->file_reader->readJSONFile($config_path, 1);
    }

    protected function getTree($path)
    {
        $tree = new Tree();

        $this->tree = $tree->buildTree($path, []);

        $this->flat_tree = $tree->flattenTree($this->tree);
    }

    protected function writeTree()
    {
        $this->file_writer->writeArrayToFile('/structure_files/tree.txt', $this->tree);

        $this->file_writer->writeArrayToFile('/structure_files/flattened.txt', $this->flat_tree);
    }

    protected function buildStructure()
    {
        $structure = new Structure($this->config);
        $structure->parse();
    }

    protected function loadLogs(): array
    {
        return $this->parser->loadLogs($this->flat_tree);
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
