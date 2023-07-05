<?php

namespace CouponsPlus\Original\Presentation;

use CouponsPlus\Original\Environment\Env;

Class Component
{
    protected $file;

    public function render()
    {
        (object) $self = $this;

        include $this->templateFile();
    }

    public function getRenderedMarkup()
    {
        ob_start();

        $this->render();

        return ob_get_clean();
    }
    

    /*
        Overridable by children components
    */
    public function directory()
    {
        return Env::directory() . 'app/views';
    }

    private function templateFile()
    {
        return "{$this->directory()}/".strtolower($this->file);
    }
}