<?php

namespace Synaptic4UParser\Files;

use Synaptic4UParser\Core\Log;

/**
 * Class::FileWriter
 * Handles all the file writing functionality.
 * FileWriter::setPath()
 * FileWriter::appendArrayToFile()
 * FileWriter::writeArrayToFile()
 * FileWriter::appendTextToFile()
 * FileWriter::writeTextToFile()
 */
class FileWriter{

    protected $path;

    protected function setPath(string $file)
    {
        // Must accept the directory beginning with a "/".
        // Travels 2 directories up.
        $this->path = dirname(__FILE__, 2).$file;
    }

    public function appendArrayToFile(string $file, array $params)
    {
        $this->setPath($file);
        
        $log = fopen($this->path, 'a');

        $content = json_encode($params, JSON_PRETTY_PRINT).PHP_EOL;

        fwrite($log, $content);

        fclose($log);
    }

    public function writeArrayToFile(string $file, array $params)
    {
        $this->setPath($file);
        
        $log = fopen($this->path, 'w');

        $content = json_encode($params, JSON_PRETTY_PRINT);

        fwrite($log, $content);

        fclose($log);
    }

    public function appendTextToFile(string $file, string $content)
    {
        $this->setPath($file);

        $log = fopen($this->path, 'a');

        fwrite($log, $content);

        fclose($log);
    }

    public function writeTextToFile(string $file, string $content)
    {
        $this->setPath($file);

        $log = fopen($this->path, 'w');

        fwrite($log, $content);

        fclose($log);
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