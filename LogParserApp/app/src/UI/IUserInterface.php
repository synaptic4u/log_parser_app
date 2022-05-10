<?php

namespace Synaptic4UParser\UI;

interface IUserInterface
{
    public function display(string $dir, array $params = []);

    public function finished(string $dir, array $params = []);
}
