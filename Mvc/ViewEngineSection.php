<?php

namespace Mvc;

use Core\Common\Obj;
use Core\Configuration\IConfigurationSection;
use Core\Configuration\Configuration;
use Mvc\View\ViewEngineCollection;
use Mvc\View\NativeViewEngine;

class ViewEngineSection implements IConfigurationSection{
    
    public function execute(Configuration $configuration, \SimpleXMLElement $xml){
        $viewEngines = new ViewEngineCollection();
        $viewEngines->add((new NativeViewEngine())->setIsDefault(true));
        
        if(isset($xml->mvc->viewEngines)){
            foreach($xml->mvc->viewEngines->engine as $engine){
                $default = strtolower($xml->viewEngines->engine['default']) == 'true' ? true : false;
                $viewEngine = Obj::create((string)$engine->class)->get();
                $viewEngine->setViewLocationFormats((array)$engine->locationFormat);
                $viewEngine->setIsDefault($default);
                $viewEngines->add($viewEngine);
            }
        }

        $configuration->add('viewEngines', $viewEngines);
    }
}