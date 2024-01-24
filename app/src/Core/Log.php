<?php

namespace Synaptic4UParser\Core;

use Exception;
use Synaptic4UParser\Files\Writer\FileWriter;
use Synaptic4UParser\Files\Writer\FileWriterText;

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
    protected $path;
    protected $file_writer;

    /**
     * Saves paramaters to local variables.
     * Calls the Log::writeLog() method.
     */
    public function __construct(mixed $msg, string $file)
    {
        try {
            $this->msg = $msg;

            $this->path = $file;

            $this->file_writer = new FileWriter();

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
        $this->file_writer->appendToFile(
            new FileWriterText(),
            $this->path,
            $this->buildMessage()
        );
    }

    /**
     * Builds the partial path for the file.
     *
     * @param mixed $file
     *
     * @return string : Path to file
     */
    protected function buildPath($file): string
    {
        return '/logs/'.$file.'.txt';
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

        foreach ($this->msg as $key => $value) {
            $value = (1 == is_array($value)) ? json_encode($value, JSON_PRETTY_PRINT) : $value;
            $message .= $key.': '.$value."\n";
        }

        return $message;
    }
}
