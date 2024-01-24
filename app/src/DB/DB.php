<?php

namespace Synaptic4UParser\DB;

class DB
{
    protected $db;

    public function __construct(IDBInterface $db)
    {
        $this->db = $db;
    }

    public function query($params, $sql): mixed
    {
        return $this->db->query($params, $sql);
    }

    public function getLastId(): int
    {
        return $this->db->getLastId();
    }

    public function getrowCount(): int
    {
        return $this->db->getRowCount();
    }

    public function getStatus(): mixed
    {
        return $this->db->getStatus();
    }
}
