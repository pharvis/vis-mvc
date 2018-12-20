<?php

namespace Mvc;

use Core\Configuration\IConfigurationSection;
use Core\Configuration\Configuration;
use Mvc\Service\ServiceContainer;
use Mvc\Service\Service;
use Mvc\Service\Argument;

class ServiceSection implements IConfigurationSection{
    
    public function execute(Configuration $configuration, \SimpleXMLElement $xml){
        
        $serviceContainer = new ServiceContainer();

        if(isset($xml->mvc->services)){
            foreach($xml->mvc->services->service as $serv){
                $service = new Service($serv->class);

                foreach($serv->constructorArg as $arg){
                    $argument = new Argument();

                    if(isset($arg['type']) && $arg['type'] == 'property'){
                        $arg = $configuration->get('settings')->path((string)$arg);
                    }
                    if(isset($arg['type']) && $arg['type'] == 'ref'){
                        $argument->setIsReference(true);
                    }

                    $argument->setValue((string)$arg);
                    $service->addConstructorArg($argument);
                }

                $serviceContainer->add((string)$serv['name'], $service);
            }
        }
        
        $configuration->add('serviceContainer', $serviceContainer);
    }
}

