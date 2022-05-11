<?php

namespace Synaptic4UParser\FrontTemplate;

interface IUserInterface
{
    public function display(array $params = []);

    public function finished(array $params = []);
}
