<?php

namespace Mvc;

use Core\Web\View\IView;

class ViewResult implements IActionResult{
    
    protected $view = null;
    protected $viewParameters = [];
    
    public function __construct(IView $view, $parameters){
        $this->view = $view;
        $this->viewParameters = $parameters;
    }

    public function execute() : string{
        return $this->view->render($this->viewParameters);
    }
}