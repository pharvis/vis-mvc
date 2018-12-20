<?php

namespace Mvc\Service;

class Service{
    
    protected $class;
    protected $args = [];
    
    public function __construct(string $class){
        $this->class = $class;
    }
    
    public function getClass() : string{
        return $this->class;
    }
    
    public function addConstructorArg($arg){
        $this->args[] = $arg;
    }
    
    public function getConstructorArgs() : array{
        return $this->args;
    }
}