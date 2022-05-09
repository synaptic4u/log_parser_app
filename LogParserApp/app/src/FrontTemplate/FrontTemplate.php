<?php

namespace Synaptic4UParser\FrontTemplate;

use Synaptic4UParser\UI\IUserInterface;

class FrontTemplate
{
    public function __construct(IUserInterface $ui)
    {
        $this->ui = $ui;
        $this->dir_path = $this->getViewPath();
    }

    public function display(): mixed
    {
        return $this->ui->display($this->dir_path);
    }

    public function finished(): mixed
    {
        return $this->ui->finished($this->dir_path);
    }

    protected function getViewPath(): string
    {
        $path = get_class($this->ui);

        return __DIR__.'/Views/'.substr($path, strrpos($path, '\\') + 1, strlen($path));
    }
}
