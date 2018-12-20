<?php

namespace Mvc\View;

class ViewEngineCollection implements \IteratorAggregate{
    
    protected $collection = [];
    
    public function add(ViewEngine $viewEngine){
        $this->collection[get_class($viewEngine)] = $viewEngine;
    }
    
    public function remove(string $class) : bool{
        if($this->hasType($class)){
            unset($this->collection[$class]);
            return true;
        }
        return false;
    }
    
    public function count() : int{
        return count($this->collection);
    }
    
    public function clear(){
        $this->collection = [];
    }
    
    public function getTypeOf(string $class) : ViewEngine{
        if(array_key_exists($class, $this->collection)){
            return $this->collection[$class];
        }
    }
    
    public function hasType(string $class) : bool{
        return array_key_exists($class, $this->collection);
    }

    public function getIterator(){
        return new \ArrayIterator($this->collection);
    }
}