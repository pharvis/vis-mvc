<?php

namespace Mvc;

use Core\Common\Str;
use Core\Configuration\Configuration;
use Core\Configuration\IConfigurationSection;
use Core\Web\Http\HttpException;

class ModulesSection implements IConfigurationSection{
    
    public function execute(Configuration $config, \XmlConfigElement $xml){

        $modules = [];
         
        if($xml->hasPath('mvc.0.modules.0.module')){
  
            foreach($xml->mvc[0]->modules[0]->module as $module){
               $moduleClass = (string)Str::set($module)->replace('.', '\\');

               $moduleInstance = new $moduleClass();

               if(!$moduleInstance instanceof IHttpModule){
                   throw new HttpException("HttpModule '$moduleClass' must inherit from IHttpModule");
               }
               $modules[] = $moduleInstance;
           }
        }
        
        return $modules;
    }
}

