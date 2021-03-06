<?php

namespace Synaptic4UParser\Tree;

use Synaptic4UParser\Logs\Activity;
use Synaptic4UParser\Logs\Error;
use Synaptic4UParser\Logs\Log;

/**
 * Class::Tree :
 * Builds and flattens associative arrays created from directory structure compared to config structure.
 *
 * Tree::buildTree : Walks through directory and builds multi-dimensional array of file name paths.
 * Tree::flattenTree : flattens the multi-dimensional array into a single dimensional array.
 */
class Tree
{
    protected $file_exclude_types;

    public function __construct($file_exclude_types)
    {
        $this->file_exclude_types = $file_exclude_types;
    }

    /**
     * Recursive method : Cycles through children directories to build multi-dimensional array of file paths.
     * Need to update it to have an ignore list set out in the main config.json,
     * currently it is hard coded.
     *
     * @param string $path : Parent directory path to loop through
     * @param array  $tree : Empty initialised array
     *
     * @return array : A associative multi-dimensional array of all files
     */
    public function buildTree(string $path, array $tree): array
    {
        $cnt = 0;
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if ('.' !== $file) {
                        if ('..' !== $file) {
                            $newfile = $path.$file;
                            $newpath = $newfile.'/';
                            if (is_dir($newpath)) {
                                $tree[$path][$file] = $this->buildTree($newpath, []);
                            } else {
                                if (!in_array(substr($newfile, strripos($newfile, '.')), $this->file_exclude_types)) {
                                    $tree[$path][$file] = $newfile;
                                }
                                // if (0 === substr_count($newfile, '.xz') && '.xz' != substr($newfile, strripos($newfile, '.'))) {
                                //     if (0 === substr_count($newfile, '.gz') && '.gz' != substr($newfile, strripos($newfile, '.'))) {
                                //         if (0 === substr_count($newfile, '.tar') && '.tar' != substr($newfile, strripos($newfile, '.'))) {
                                //             if (0 === substr_count($newfile, '.zip') && '.zip' != substr($newfile, strripos($newfile, '.'))) {
                                //                 $tree[$path][$file] = $newfile;
                                //             }
                                //         }
                                //     }
                                // }
                            }
                        }
                    }
                    ++$cnt;
                }
                closedir($dh);
            }
        }

        return $tree;
    }

    /**
     * Method flattens a multi-dimensional array into a associative single dimensional array.
     *
     * @param array $tree : Associative multi-dimensional array
     *
     * @return array : Single dimensional associative array
     */
    public function flattenTree(array $tree): array
    {
        $flat_tree = [];
        array_walk_recursive($tree, function ($value, $key) use (&$flat_tree) {
            $flat_tree[$key] = $value;
        });

        return $flat_tree;
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
