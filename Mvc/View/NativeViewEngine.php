<?php

namespace Mvc\View;

use Core\Common\Str;
use Core\Web\Http\HttpContext;
use Core\Web\View\IView;
use Core\Web\View\NativeView;
use Core\Web\View\Methods\Escape;
use Core\Web\View\Methods\Obj;

class NativeViewEngine extends ViewEngine{
    
    protected $view = null;
    
    public function __construct(){
        $this->setViewLocationFormat('~/views/{controller}/{action}.php');
        $this->view = new NativeView();
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
        
        $this->view->getViewMethods()->add('escape', new Escape());
        $this->view->getViewMethods()->add('request', new Obj($httpContext->getRequest()));
        $this->view->getViewMethods()->add('response', new Obj($httpContext->getResponse()));

        return $this->view;
    }
}