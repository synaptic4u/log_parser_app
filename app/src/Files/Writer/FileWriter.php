<?php

namespace Synaptic4UParser\Files\Writer;

/**
 * Class::FileWriter
 * Handles all the file writing functionality.
 * Uses the IFileWriter interface to determine to write array or string.
 *
 * FileWriter::setPath()
 * FileWriter::appendToFile()
 * FileWriter::writeToFile()
 */
class FileWriter
{
    protected $path;

    public function appendToFile(IFileWriter $file_writer, string $file, $params)
    {
        $this->setPath($file);

        $file_writer->appendToFile($this->path, $params);
    }

    public function writeToFile(IFileWriter $file_writer, string $file, $params)
    {
        $this->setPath($file);

        $file_writer->writeToFile($this->path, $params);
    }

    protected function setPath(string $file)
    {
        // Must accept the directory beginning with a "/".
        // Travels 2 directories up.
        $this->path = dirname(__FILE__, 3).$file;
    }
}
