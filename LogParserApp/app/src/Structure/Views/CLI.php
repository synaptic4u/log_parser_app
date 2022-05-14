<?php

namespace Synaptic4UParser\Structure\Views;

use Exception;
use Synaptic4UParser\Core\Template;
use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

class CLI implements IStructureUI
{
    public function __construct()
    {
        try {
            $this->template = new Template(__DIR__.'/CLI/');
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'Error' => $e->__toString(),
            ]);
        }
    }

    public function exists(array $params = []): int
    {
        $template = $params;
        $template['eol'] = PHP_EOL;

        if (print_r($this->template->build('front_template', $template))) {
            return 1;
        }

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
