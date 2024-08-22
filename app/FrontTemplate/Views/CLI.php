<?php

namespace Synaptic4UParser\FrontTemplate\Views;

use Exception;
use Synaptic4UParser\Core\Template;
use Synaptic4UParser\FrontTemplate\IUserInterface;
use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

class CLI implements IUserInterface
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

    public function display(array $params = [])
    {
        $template['base_dir'] = 'LogParserApp';
        $template['eol'] = PHP_EOL;

        if (print_r($this->template->build('front_template', $template))) {
            return 1;
        }

        return 0;
    }

    public function finished(array $params = [])
    {
        $template['eol'] = PHP_EOL;

        if (print_r($this->template->build('finished', $template))) {
            return 1;
        }

        return 0;
    }

    public function fullReport(array $template): mixed
    {
        $template['eol'] = PHP_EOL;

        if (print_r($this->template->build('full_report', $template))) {
            return 1;
        }

        return 0;
    }

    protected function replaceEOL(string $string): string
    {
        return $string; // str_replace('~~~~~', PHP_EOL, $string);
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
