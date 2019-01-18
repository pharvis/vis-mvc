<?php

namespace Mvc;

use Core\Web\Http\Request;
use Core\Web\Http\Response;

class RedirectResult implements IActionResult{
    
    protected $request = null;
    protected $response = null;
    protected $location = '';
    protected $params = null;
    
    public function __construct(Request $request, Response $response, string $location, $params = null){
        $this->request = $request;
        $this->response = $response;
        $this->location = $location;
        $this->params = $params;
    }
    
    public function execute() : string{
        $this->response->redirect($this->location);
    }
}