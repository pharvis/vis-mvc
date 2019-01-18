<?php

namespace Mvc;

use Core\Common\Obj;
use Core\Configuration\IConfigurationSection;
use Core\Configuration\Configuration;
use Mvc\View\ViewEngineCollection;
use Mvc\View\NativeViewEngine;

class ViewEngineSection implements IConfigurationSection{
    
    public function execute(Configuration $configuration, \XmlConfigElement $xml){
        $viewEngines = new ViewEngineCollection();
        $viewEngines->add((new NativeViewEngine())->setIsDefault(true));
        
        if($xml->hasPath('mvc.0.viewEngines.0.engine')){ 
            foreach($xml->mvc[0]->viewEngines[0]->engine as $engine){
                //$default = strtolower($engine['default']) == 'true' ? 'ss' : false;
                $viewEngine = Obj::create((string)$engine->class[0])->get();
                
                foreach($engine->locationFormat as $locationFormat){
                    $viewEngine->setViewLocationFormat($locationFormat);
                }

                $viewEngine->setIsDefault(true);
                $viewEngines->add($viewEngine);
            }
        }

        $configuration->add('viewEngines', $viewEngines);
    }
}