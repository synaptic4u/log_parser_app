<?php

namespace Synaptic4UParser\Tables\Models;

interface ITablesDB
{
    public function readTablesList(): mixed;

    public function readTableColumns($table): mixed;

    public function createTable($table): array;

    public function insertLog(array $columns, string $alias): int;

    public function dumpLog(string $line, string $file): int;

    public function createLogDump(): mixed;
}
