<?php

namespace Synaptic4UParser\FrontTemplate\Views;

use Synaptic4UParser\FrontTemplate\IUserInterface;
use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

class WEB implements IUserInterface
{
    public function display(array $params = [])
    {
        return 1;
    }

    public function finished(array $params = [])
    {
        return 1;
    }

    public function timeReport(array $result)
    {
        return 1;
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
