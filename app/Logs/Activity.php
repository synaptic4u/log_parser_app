<?php

namespace Synaptic4UParser\Logs;

use Exception;
use Synaptic4UParser\Files\Writer\FileWriter;
use Synaptic4UParser\Files\Writer\FileWriterText;

/**
 * Class::Activity
 * Interface::ILog.
 *
 * Passes log message to FileWriter instance to be written to file.
 */
class Activity implements ILog
{
    protected $path;

    /**
     * Assignes log path to local variable.
     * Instantiates the FileWriter class.
     */
    public function __construct()
    {
        try {
            $this->path = '/logs/activity.txt';

            $this->file_writer = new FileWriter();
        } catch (Exception $e) {
            //  This is the log file, so...? Go look in the OS error log!
        }
    }

    /**
     * Calls to FileWriter::appendToFile -> Passes IFileWriter instance.
     *
     * @param string $msg : Received log message
     */
    public function writeLog(string $msg)
    {
        $this->file_writer->appendToFile(
            new FileWriterText(),
            $this->path,
            $msg
        );
    }
}
