<?php

namespace Synaptic4UParser\Structure\Models;

interface IStructureDB
{
    public function getRowCount($table): int;

    public function getMaxLogID($table): mixed;
}
