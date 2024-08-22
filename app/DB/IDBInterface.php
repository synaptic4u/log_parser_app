<?php

namespace Synaptic4UParser\DB;

interface IDBInterface
{
    public function query($params, $sql): mixed;

    public function getLastId(): int;

    public function getrowCount(): int;

    public function getStatus(): mixed;
}
