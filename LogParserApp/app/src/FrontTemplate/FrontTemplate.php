<?php

namespace Synaptic4UParser\FrontTemplate;

class FrontTemplate
{
    public function __construct(IUserInterface $ui)
    {
        $this->ui = $ui;
    }

    public function display(): mixed
    {
        return $this->ui->display();
    }

    public function finished(): mixed
    {
        return $this->ui->finished();
    }

    public function timeReport(array $result): mixed
    {
        return $this->ui->timeReport($result);
    }
}
