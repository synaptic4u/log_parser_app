<?php

namespace Synaptic4UParser\Core;

use Exception;

/**
 * Class : Template.
 *
 * Writes php variables into a template and returns the result.
 *
 * Template::__construct() :
 * Initializes object. Receives the directory path to the template.
 *
 * Template::build() :
 * Calls Template::get_template() and Template::build_template().
 * If Template::build_template fails, returns empthy string.
 *
 * Template::string_replace() :
 * Replaces unnessary new lines with white space.
 *
 * Template::get_template() :
 * Adds file name to the directory path and checks to see if exists.
 *
 * Template::build_template() :
 * Creates object buffer, cycles through paramater array and inserts variables in template.
 *
 * Template::escape() :
 * Parse string and escapes htmlspecialchars();
 *
 * Template::error() :
 * Logs an error message.
 */
class Template
{
    protected $folder;

    public function __construct(string $dir = null, string $folder = null)
    {
        if (($folder) && ($dir)) {
            $this->folder = $dir.'/'.$folder;
        }
    }

    public function build(string $name, array $array = []): string
    {
        $template = $this->get_template($name);

        $output = '';

        if ($template) {
            $output = $this->build_template($template, $array);
        }

        return $output;
    }

    public function string_replace(string $buffer): string
    {
        return str_replace("\n", '', str_replace('  ', ' ', $buffer));
    }

    protected function get_template(string $name): mixed
    {
        $file = $this->folder.'/'.$name.'.php';

        if (file_exists($file)) {
            return $file;
        }

        return throw new Exception('File does not exist :'.$file);
    }

    protected function build_template($template, $array): string
    {
        $contents = '';

        try {
            ob_start();

            foreach ($array as $key => $value) {
                ${$key} = $value;
            }

            include $template;

            $contents = ob_get_contents();

            ob_end_clean();
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'Error' => $e->__toString(),
            ]);
        } finally {
            return $this->string_replace($contents);
        }
    }

    protected function escape(string $variable): string
    {
        return htmlspecialchars($variable, ENT_QUOTES, 'UTF-8');
    }

    protected function error(array $msg)
    {
        new Log($msg, 'error');
    }
}
