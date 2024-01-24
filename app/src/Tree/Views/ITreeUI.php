<?php

namespace Synaptic4UParser\Tree\Views;

interface ITreeUI
{
    public function display(array $params = []);

    public function finished(array $params = []);

    public function timeReport(array $result);
}
