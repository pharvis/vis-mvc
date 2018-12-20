<?php

namespace Mvc\View;

use Core\Common\Str;
use Core\Web\Http\HttpContext;
use Core\Web\View\IView;
use Core\Web\View\NativeView;

class NativeViewEngine extends ViewEngine{
    
    public function __construct(){
        $this->setViewLocationFormats(['~/views/{controller}/{action}.php']);
    }

    public function findView(HttpContext $httpContext) : IView{
        
        $view = new NativeView();
        $view->setBasePath($httpContext->getRequest()->getServer()->getBasePath());
        
        foreach($this->getViewLocationFormats() as $location){

            $file = (string)Str::set($location)->replaceTokens(
                $httpContext->getRequest()->getParameters()
                ->map(function($v){ return (string)Str::set($v)->toUpperFirst(); })
                ->toArray()
            );
                
            $view->setViewFiles($file);
        }
        
        return $view;
    }
}