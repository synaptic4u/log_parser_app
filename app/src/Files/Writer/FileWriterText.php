<?php

namespace Synaptic4UParser\Files\Writer;

use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

class FileWriterText implements IFileWriter
{
    public function appendToFile(string $path, $content)
    {
        $file = fopen($path, 'a');

        fwrite($file, $content);

        fclose($file);
    }

    public function writeToFile(string $path, $content)
    {
        $file = fopen($path, 'w');

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
