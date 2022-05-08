<?php

namespace Synaptic4UParser\Core;

use Exception;

class Template
{
    protected $folder;

    public function __construct($dir = null, $folder = null)
    {
        if (($folder) && ($dir)) {
            $this->folder = $dir.'/'.$folder;
        }
    }

    public function build($name, $array = [])
    {
        $template = $this->get_template($name);

        $output = '';

        if ($template) {
            $output = $this->build_template($template, $array);
        }

        return $output;
    }

    public function string_replace($buffer)
    {
        return str_replace("\n", '', str_replace('  ', ' ', $buffer));
    }

    protected function get_template($name)
    {
        $file = $this->folder.'/'.$name.'.php';

        if (file_exists($file)) {
            return $file;
        }
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

    protected function escape($variable)
    {
        return htmlspecialchars($variable, ENT_QUOTES, 'UTF-8');
    }

    protected function error($msg)
    {
        new Log($msg, 'error');
    }

    protected function log($msg)
    {
        new Log($msg, 'activity');
    }
}
