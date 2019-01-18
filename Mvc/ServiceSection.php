<?php

namespace Mvc;

use Core\Configuration\IConfigurationSection;
use Core\Configuration\Configuration;
use Mvc\Service\ServiceContainer;
use Mvc\Service\Service;
use Mvc\Service\Argument;

class ServiceSection implements IConfigurationSection{
    
    public function execute(Configuration $configuration, \XmlConfigElement $xml){
        
        $serviceContainer = new ServiceContainer();

        if($xml->hasPath('mvc.0.services.0.service')){
            foreach($xml->mvc[0]->services[0]->service as $serv){
                $service = new Service($serv->class[0]);

                foreach($serv->constructorArg as $arg){
                    $argumentAttributes = $arg->getAttributes();
                    $argument = new Argument();

                    if(array_key_exists('type', $argumentAttributes)){

                        if($argumentAttributes['type'] =='property'){
                            $arg = $configuration->get('settings')->path((string)$arg);
                        }
                        else if($argumentAttributes['type'] =='ref'){
                            $argument->setIsReference(true);
                        }
                    }
                     
                    $argument->setValue((string)$arg);
                    $service->addConstructorArg($argument);
                }

                $serviceAttributes = $serv->getAttributes();

                if(array_key_exists('name', $serviceAttributes)){
                    $serviceContainer->add($serviceAttributes['name'], $service);
                }
            }
        }

        $configuration->add('serviceContainer', $serviceContainer);
    }
}

