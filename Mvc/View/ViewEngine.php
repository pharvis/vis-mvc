<?php

namespace Mvc\View;

use Core\Web\Http\HttpContext;
use Core\Web\View\IView;

abstract class ViewEngine{
    
    private $locationFormats = [];
    private $isDefault = false;

    public function setViewLocationFormats(array $locationFormats){
        $this->locationFormats = array_merge($this->locationFormats, $locationFormats);
        return $this;
    }
    
    public function getViewLocationFormats() : array{
        return $this->locationFormats;;
    }

    public function setIsDefault(bool $isDefault){
        $this->isDefault = $isDefault;
        return $this;
    }
    
    public function getIsDefault() : bool{
        return $this->isDefault;
    }
    
    public abstract function findView(HttpContext $httpContext) : IView;
}