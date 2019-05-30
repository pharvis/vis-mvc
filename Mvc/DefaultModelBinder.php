<?php

namespace Mvc;

use Core\Common\Obj;
use Core\Common\Str;
use Core\Common\Arr;
use Core\Common\Date;

class DefaultModelBinder{
    
    public function bind($httpRequest, BindingContext $bindingContext){
        
        $collection = $httpRequest->getCollection();
        
            switch($bindingContext->getType()){
                case 'string':
                case 'int':
                case 'float': 
                case 'bool':
                case 'array': 
                    return $collection->get($bindingContext->getName(), $bindingContext->getDefaultValue());
                case 'object': 
                    return (object)$collection->get($bindingContext->getName(), $bindingContext->getDefaultValue());
                default: 
                    
                    switch($bindingContext->getType()){
                        case Str::class:
                        case Arr::class:
                        case Date::class:

                            if($collection->exists($bindingContext->getName())){ 
                                $value = $collection->get($bindingContext->getName());
                                return Obj::create($bindingContext->getType(), [$value])->get();
                            }
                            
                            if($bindingContext->getIsOptional()){
                                return null;
                            }
                            
                            return Obj::create($bindingContext->getType())->get();
                        default:  
                            
                            $value = $collection->get($bindingContext->getName());

                            if(is_array($value)){
                                return Obj::create($bindingContext->getType())->setProperties($value)->get();
                            }
                            if($bindingContext->getIsOptional()){
                                return null;
                            }
                            return Obj::create($bindingContext->getType())->get();
                    }
            }
    }
}