<?php

namespace Synaptic4UParser\Files;

use Synaptic4UParser\Core\Log;
use Exception;

class FileReader{

    protected function readLogFile(string $file): array
    {
        $rows = [];
        try {
            $log_txt = file_get_contents($file);

            $rows = explode("\n", $log_txt);
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString()
            ]);
        }finally{
            return $rows;
        }
    }

    public function parseFile(string $file): array
    {
        return $this->readLogFile($file);
    }

    public function readJSONFile(string $file, int $dir_include = 0): mixed
    {
        // If dir_include is not set to 1
        if($dir_include === 0)
        {
            // Must accept the directory beginning with a "/".
            // Travels 2 directories up.
            $file = dirname(__FILE__, 2).$file;
        }
        return json_decode(file_get_contents($file));
    }

    public function stringClear(string $string): string
    {
        return  str_replace("     ", " ", 
                    str_replace("    ", " ", 
                        str_replace("   ", " ", 
                            str_replace("  ", " ", 
                                str_replace(" :", ":", 
                                    str_replace(" ;", ";", $string)
                                )
                            )
                        )
                    )
                );
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

?>