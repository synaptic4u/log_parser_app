<?php

namespace Synaptic4UParser\UI\CLI;

use Exception;
use Synaptic4UParser\Core\Log;
use Synaptic4UParser\Core\Template;
use Synaptic4UParser\UI\IUserInterface;

class CLI implements IUserInterface
{
    public function __construct()
    {
        try {
            $this->template = new Template(__DIR__, 'Views');
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'Error' => $e->__toString(),
            ]);
        }
    }

    public function display(string $dir, array $params = [])
    {
        $template['base_dir'] = dirname(__FILE__, 4);
        $template['eol'] = '~~~~~';

        print_r($this->replaceEOL($this->template->build($dir, 'front_template', $template)));
    }

    public function finished(string $dir, array $params = [])
    {
        $template['eol'] = '~~~~~';
        print_r($this->replaceEOL($this->template->build($dir, 'finished', $template)));
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
