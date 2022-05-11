<?php

namespace Synaptic4UParser\FrontTemplate\Views;

use Synaptic4UParser\Core\Log;
use Synaptic4UParser\FrontTemplate\IUserInterface;

class WEB implements IUserInterface
{
    public function display(array $params = [])
    {
    }

    public function finished(array $params = [])
    {
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
