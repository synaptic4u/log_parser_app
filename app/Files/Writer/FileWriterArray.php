<?php

namespace Synaptic4UParser\Files\Writer;

use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

class FileWriterArray implements IFileWriter
{
    public function appendToFile(string $path, $params)
    {
        $file = fopen($path, 'a');

        $content = json_encode($params, JSON_PRETTY_PRINT).PHP_EOL;

        fwrite($file, $content);

        fclose($file);
    }

    public function writeToFile(string $path, $params)
    {
        $file = fopen($path, 'w');

        $content = json_encode($params, JSON_PRETTY_PRINT);

        fwrite($file, $content);

        fclose($file);
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
