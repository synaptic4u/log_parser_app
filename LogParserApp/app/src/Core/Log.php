<?php

namespace Synaptic4UParser\Core;

use Exception;

/**
 * Class::Log :
 * Logs messages to an activity or error log file.
 * Files are .txt files.
 *
 * Log::construct() :
 * Saves paramaters to local variables.
 * Calls the Log::writeLog() method.
 *
 * Log::writeLog() :
 * Opens the file, writes the log and closes the file.
 *
 * Log::buildPath() :
 * Builds the directory path for the file.
 *
 * Log::buildMessage() :
 * Builds the log message with timestamp and encodes arrays.
 */
class Log
{
    protected $msg;
    protected $file;

    /**
     * Saves paramaters to local variables.
     * Calls the Log::writeLog() method.
     */
    public function __construct(mixed $msg, string $file)
    {
        try {
            $this->msg = $msg;

            $this->file = $file;

            $this->writeLog();
        } catch (Exception $e) {
            //  This is the log file, so...? Go look in Apache2 error log!
        }
    }

    /**
     * Opens the file, writes the log and closes the file.
     */
    protected function writeLog()
    {
        $log = fopen($this->buildPath(), 'a');

        fwrite($log, $this->buildMessage());

        fclose($log);
    }

    /**
     * Builds the directory path for the file.
     *
     * @return string : Path to file
     */
    protected function buildPath(): string
    {
        $path = dirname(__FILE__, 2).'/logs/';

        return $path.$this->file.'.txt';
    }

    /**
     * Builds the log message from local $msg array into a string.
     * Creates date and JSON encodes all arrays.
     *
     * @return string : Log message string
     */
    protected function buildMessage(): string
    {
        $date = date('Y-m-d H:i:s');

        $message = "\n".$date."\n";

        if (1 == is_array($this->msg)) {
            foreach ($this->msg as $key => $value) {
                $value = (1 == is_array($value)) ? json_encode($value, JSON_PRETTY_PRINT) : $value;
                $message .= $key.': '.$value."\n";
            }
        } else {
            $message .= $this->msg."\n";
        }

        return $message;
    }
}
