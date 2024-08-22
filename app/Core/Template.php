<?php

namespace Synaptic4UParser\Core;

use Exception;
use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

/**
 * Class : Template.
 *
 * Writes php variables into a template and returns the result.
 *
 * Template::__construct() :
 * Initializes object. Assigns the directory path to classes attribute.
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
 * Template::error() :
 * Logs an error message.
 */
class Template
{
    protected $folder;

    /**
     * Assigns directory path to attribute.
     */
    public function __construct(string $dir_path)
    {
        $this->folder = $dir_path;
    }

    /**
     * Calls Template::get_template.
     * Calls Template::build_template to parse the template writing the params array.
     *
     * @param string $name  : Template file to retrieve
     * @param array  $array : Associtive array of values to be written to template
     *
     * @return string : returns a string
     */
    public function build(string $name, array $array = []): string
    {
        try {
            $template = $this->get_template($name);

            $output = '';

            if (null !== $template) {
                $output = $this->build_template($template, $array);
            } else {
                throw new Exception('Cannot find the file: '.$this->folder.'/'.$name.'.php');
            }
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'Error' => $e->__toString(),
            ]);
        } finally {
            return $output;
        }
    }

    /**
     * Trims unnessary new lines and whitespace.
     * Doesn't work well with CLI PHP_EOL.
     * Will perfect solution when I add html templates.
     *
     * @param string $buffer : Output of object buffer
     *
     * @return string : Trimmed string
     */
    public function string_replace(string $buffer): string
    {
        return $buffer; // str_replace("\n", '', str_replace('  ', ' ', $buffer));
    }

    /**
     * Gets the template to parse.
     * Checks if template exists.
     *
     * @param string $name : Template name
     *
     * @return mixed : Returns file or null
     */
    protected function get_template(string $name): mixed
    {
        $file = $this->folder.$name.'.php';

        if (file_exists($file)) {
            return $file;
        }

        return null;
    }

    /**
     * Parses the template file through the object buffer and writes param array to template in the ob.
     *
     * @param string $template : full path to the template file
     * @param array  $array    : Associative array of values to write to template
     *
     * @return string : Returns the parsed object buffer as string
     */
    protected function build_template(string $template, $array = []): string
    {
        $contents = '';

        ob_start();

        foreach ($array as $key => $value) {
            ${$key} = $value;
        }

        include $template;

        $contents = ob_get_contents();

        ob_end_clean();

        return $this->string_replace($contents);
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    private function error($msg)
    {
        new Log($msg, new Error());
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    private function log($msg)
    {
        new Log($msg, new Activity());
    }
}
