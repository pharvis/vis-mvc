<?php

namespace Mvc;

use Core\Web\Http\Response;

class JsonResult implements IActionResult{
    
    protected $response = null;
    protected $value = '';
    
    public function __construct(Response $response, $value){
        $this->response = $response;
        $this->value = $value;
    }
    
    public function execute() : string{
        $this->response->setContentType('application/json');
        return json_encode($this->value);
    }
}