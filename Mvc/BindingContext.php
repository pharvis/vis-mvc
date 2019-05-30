<?php

namespace Mvc;

class BindingContext{
    
    protected $name;
    protected $type;
    protected $isOptional;
    protected $defaultValue;
    
    public function __construct(string $name, string $type, bool $isOptional, $defaultValue){
        $this->name = $name;
        $this->type = $type;
        $this->isOptional = $isOptional;
        $this->defaultValue = $defaultValue;
    }
    
    public function getName() : string{
        return $this->name;
    }
    
    public function getType() : string{
        return $this->type;
    }
    
    public function getIsOptional() : string{
        return $this->isOptional;
    }
    
    public function getDefaultValue(){
        return $this->defaultValue;
    }
}