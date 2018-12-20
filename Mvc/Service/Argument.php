<?php

namespace Mvc\Service;

class Argument{

    protected $value = '';
    protected $isReference = false;
    
    public function setValue(string $value){
        $this->value = $value;
    }
    
    public function getValue(){
        return $this->value;
    }
    
    public function setIsReference(bool $isReference){
        $this->isReference = $isReference;
    }
    
    public function getIsReference() : bool{
        return $this->isReference;
    }
}