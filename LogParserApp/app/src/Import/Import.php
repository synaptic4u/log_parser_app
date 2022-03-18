<?php

namespace Synaptic4UParser\Import;


use Synaptic4UParser\DB\DB;
use Synaptic4UParser\Core\Log;

class Import{

    protected $db;

    public function __construct()
    {
        $this->db = new DB();
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