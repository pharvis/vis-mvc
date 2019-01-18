<?php

namespace Mvc;

use Core\Web\Http\Response;

class JsonResult implements IActionResult{
    
    protected $response = null;
    protected $data = [];
    protected $options = null;
    
    public function __construct(Response $response, array $data = [], int $options = null){
        $this->response = $response;
        $this->data = $data;
        $this->options = $options;
    }
    
    public function execute() : string{
        $this->response->setContentType('application/json');
        return json_encode($this->data, $this->options);
    }
}