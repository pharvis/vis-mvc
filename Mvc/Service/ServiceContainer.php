<?php

namespace Mvc\Service;

use Core\Common\Arr;
use Core\Common\Obj;

class ServiceContainer{
    
    protected $container = null;
    
    public function __construct(){
        $this->container = new Arr();
    }
    
    public function add(string $name, $service){
        $this->container->add($name, $service);
    }
    
    public function get(string $name){
        $service = $this->container->get($name);
        $arguments = [];
        foreach($service->getConstructorArgs() as $argument){
            if($argument->getIsReference()){
                $arguments[] = $this->get($argument->getValue());
            }else{
                $arguments[] = $argument->getValue();
            }
        }
        return Obj::create($service->getClass(), $arguments)->get();
    }
}