<?php

namespace Synaptic4UParser\Structure\Views;

use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

/**
 * NOT IN USE - Will still develop it.
 */
class DESKTOP implements IStructureUI
{
    public function exists(array $params = []): int
    {
        return 0;
    }

    public function created(array $params = []): int
    {
        return 0;
    }

    public function processing(): int
    {
        return 0;
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
