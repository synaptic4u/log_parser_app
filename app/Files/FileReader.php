<?php

namespace Synaptic4UParser\Files;

use Exception;
use Synaptic4UParser\Core\Log;

/**
 * Class::FileReader :
 * Class to containing file reading functionality and white space cleaning.
 *
 * FileReader::parseFile() :
 * Calls FileReader::readLogFile() method.
 *
 * FileReader::readJSONFile() :
 * Retrieves contents of JSON encoded file.
 *
 * FileReader::stringClear() :
 * Replaces white space.
 *
 * FileReader::readLogFile() :
 * Retrieves the contents from a log file returned as a array.
 */
class FileReader
{
    /**
     * Calls FileReader::readLogFile.
     *
     * @param string $file : file path
     *
     * @return array : array of file contents separated by "\n"
     */
    public function parseFile(string $file): array
    {
        return $this->readLogFile($file);
    }

    /**
     * Retrieves contents of JSON encoded file.
     * Optional same directory or one directory up.
     *
     * @param string $file        : file name
     * @param int    $dir_include : selection to choose upper directory or current directory
     *
     * @return mixed : Returns std::Class object
     */
    public function readJSONFile(string $file, int $dir_include = 0): mixed
    {
        // If dir_include is not set to 1
        if (0 === $dir_include) {
            // Must accept the directory beginning with a "/".
            // Travels 2 directories up.
            $file = dirname(__FILE__, 2).$file;
        }

        return json_decode(file_get_contents($file));
    }

    /**
     * Replaces white space.
     *
     * @param string $string : string with whitespace
     *
     * @return string : string with minimal whitespace
     */
    public function stringClear(string $string): string
    {
        return str_replace(
            '     ',
            ' ',
            str_replace(
                '    ',
                ' ',
                str_replace(
                    '   ',
                    ' ',
                    str_replace(
                        '  ',
                        ' ',
                        str_replace(
                                    ' :',
                                    ':',
                                    str_replace(' ;', ';', $string)
                                )
                    )
                )
            )
        );
    }

    /**
     * Retrieves the contents from a log file returned as a array.
     *
     * @param string $file : file path
     *
     * @return array : file contents separated into array
     */
    protected function readLogFile(string $file): array
    {
        $rows = [];

        try {
            $log_txt = file_get_contents($file);

            $rows = explode("\n", $log_txt);
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);
        } finally {
            return $rows;
        }
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
