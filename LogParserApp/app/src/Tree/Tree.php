<?php

namespace Synaptic4UParser\Tree;

use Synaptic4UParser\Core\Log;

class Tree{

    public function buildTree(string $path, array $tree)
    {
        $cnt = 0;
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== ".") {
                        if ($file !== "..") {
                            $newfile = $path.$file;
                            $newpath = $newfile."/";
                            if (is_dir($newpath)) {
                                $tree[$path][$file] = $this->buildTree($newpath, []);
                            } else {
                                if(substr_count($newfile, '.xz') === 0 && substr($newfile, strripos($newfile, ".")) != '.xz'){
                                    if(substr_count($newfile,'.gz') === 0 && substr($newfile, strripos($newfile, ".")) != '.gz'){
                                        if(substr_count($newfile,'.tar') === 0 && substr($newfile, strripos($newfile, ".")) != '.tar'){
                                            if(substr_count($newfile,'.zip') === 0 && substr($newfile, strripos($newfile, ".")) != '.zip'){
                                                $tree[$path][$file] = $newfile;
                                            }
                                        }
                                    }   
                                }
                            }
                        }
                    }
                    $cnt++;
                }
                closedir($dh);
            }
        }
        return $tree;
    }

    public function flattenTree(array $tree){
        $flat_tree = array();
        array_walk_recursive($tree, function ($value, $key) use (&$flat_tree) {
            $flat_tree[$key] = $value;
        });
        return $flat_tree;
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