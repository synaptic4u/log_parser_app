<?php

namespace Synaptic4UParser\DB;

interface DBInterface
{
    public function query($params, $sql): mixed;

    public function getLastId(): int;

    public function getrowCount(): int;

    public function getStatus(): mixed;

    public function error($msg);
}
