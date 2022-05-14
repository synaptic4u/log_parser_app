<?php

namespace Synaptic4UParser\Tables\Models;

interface ITablesDB
{
    public function getRowCount($table): int;

    public function getMaxLogID($table): mixed;
}
