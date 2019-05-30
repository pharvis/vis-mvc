<?php

namespace Mvc;

class ActionNotFoundException extends \Core\Web\Http\ResourceNotFoundException{
    
    protected $action = '';
    
    /**
     * Initializes a new instance of ControllerNotFoundException with a $message,
     * the controller $class name and the request $uri.
     */
    public function __construct(string $message, string $action, string $uri) {
        parent::__construct($message, $uri);
        $this->action = $action;
    }
    
    /**
     * Gets the controller class name associated with this exception.
     */
    public function getAction() : string{
        return $this->action;
    }
}