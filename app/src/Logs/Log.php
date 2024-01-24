<?php

namespace Synaptic4UParser\Logs;

use Exception;
use Synaptic4UParser\Logs\ILog;

/**
 * Class::Log :
 * Builds the log message.
 * Passes log message to the received ILog instance.
 *
 * Log::construct() :
 * Saves paramaters to local variables.
 * Calls the Log::writeLog() method.
 *
 * Log::buildMessage() :
 * Builds the log message with timestamp and encodes arrays.
 */
class Log
{
    protected $msg;

    /**
     * Calls Log::buildMessage.
     * Calls ILog::writeLog -> Writes log to appropiate log file.
     *
     * @param array $msg : Associative array with log message
     * @param ILog  $log : instance of interface implementing Log object
     */
    public function __construct(array $msg, ILog $log)
    {
        try {
            $log->writeLog($this->buildMessage($msg));
        } catch (Exception $e) {
            //  This is the log file, so...? Go look in Apache2 error log!
        }
    }

    /**
     * Builds the log message from local $msg array into a string.
     * Creates date and JSON encodes all arrays.
     *
     * @param mixed $msg
     *
     * @return string : Log message string
     */
    protected function buildMessage(array $msg): string
    {
        $date = date('Y-m-d H:i:s');

        $message = "\n".$date."\n";

        foreach ($msg as $key => $value) {
            $value = (1 == is_array($value)) ? json_encode($value, JSON_PRETTY_PRINT) : $value;
            $message .= $key.': '.$value."\n";
        }

        return $message;
    }
}
