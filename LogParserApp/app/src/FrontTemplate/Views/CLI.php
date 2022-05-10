<?php

namespace Synaptic4UParser\FrontTemplate\Views;

use Exception;
use Synaptic4UParser\Core\Log;
use Synaptic4UParser\Core\Template;
use Synaptic4UParser\FrontTemplate\IUserInterface;

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
        $template['base_dir'] = dirname(__FILE__, 4);
        $template['eol'] = '~~~~~';

        print_r($this->replaceEOL($this->template->build('front_template', $template)));
    }

    public function finished(array $params = [])
    {
        $template['eol'] = '~~~~~';
        print_r($this->replaceEOL($this->template->build('finished', $template)));
    }

    protected function replaceEOL(string $string): string
    {
        return str_replace('~~~~~', PHP_EOL, $string);
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
