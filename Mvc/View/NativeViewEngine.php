<?php

namespace Mvc\View;

use Core\Common\Str;
use Core\Web\Http\HttpContext;
use Core\Web\View\IView;
use Core\Web\View\NativeView;

class NativeViewEngine extends ViewEngine{
    
    protected $view = null;
    
    public function __construct(){
        $this->setViewLocationFormat('~/views/{controller}/{action}.php');
        $this->view = new NativeView();
        $this->view->addMethod('escape', new \Core\Web\View\Methods\Escape());
    }
    
    public function getView() : NativeView{
        return $this->view;
    }

    public function findView(HttpContext $httpContext, string $viewName = '') : IView{
        
        if($viewName){
            $this->view->getViewFiles()->add($viewName);
        }
        
        $this->view->setBasePath($httpContext->getRequest()->getServer()->getBasePath());
        
        foreach($this->getViewLocationFormats() as $location){

            $file = (string)Str::set($location)->replaceTokens(
                $httpContext->getRequest()->getParameters()
                ->map(function($v){ return (string)Str::set($v)->toUpperFirst(); })
                ->toArray()
            );

            $this->view->getViewFiles()->add($file);
        }
        
        return $this->view;
    }
}