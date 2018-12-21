<?php

namespace Mvc;

use Core\Common\Obj;
use Core\Configuration\Configuration;
use Core\Web\Http\HttpException;
use Core\Web\Http\GenericController;
use Core\Web\Http\HttpContext;
use Core\Web\Http\Request;
use Core\Web\Http\Response;

abstract class Controller extends GenericController{
    
    private $configuration = null;
    private $httpContext = null;
    private $request = null;
    private $response = null;
    private $viewEngines = null;
    
    public final function service(HttpContext $httpContext){

        $this->getConfigurationManager()->executeSection(new ServiceSection());
        $this->getConfigurationManager()->executeSection(new ViewEngineSection());
        
        $this->configuration = $this->getConfigurationManager()->getConfiguration();
        $this->httpContext = $httpContext;
        $this->request = $httpContext->getRequest();
        $this->response = $httpContext->getResponse();
        $this->viewEngines = $this->getConfiguration()->get('viewEngines');

        $collection = $this->request->getCollection();
        $parameters = $this->request->getParameters();

        $action = $parameters->exists('action') ? $parameters->get('action') : $parameters->add('action', 'index')->get('action');

        if(Obj::from($this)->hasMethod($action)){

            $this->load();
            
            $result = Obj::from($this)->invokeMethod($action, $collection);

            if(!$result instanceof IActionResult){
                if(is_scalar($result) || $result === null){
                    $actionResult = new StringResult($result);
                }else{
                    $actionResult = new JsonResult($this->response, $result);
                }
            }else{
                $actionResult = $result;
            }
            
            $this->render($actionResult->execute());
        }
    }
    
    public function getConfiguration() : Configuration{
        return $this->configuration;
    }
    
    public function getRequest() : Request{
        return $this->request;
    }
    
    public function getResponse() : Response{
        return $this->response;
    }
    
    public function getViewEngines() : ViewEngineCollection{
        return $this->viewEngines;
    }

    public function view(array $params = []){
        
        if($this->viewEngines->count() == 0){
            throw new HttpException("No ViewEngine registered.");
        }
        
        foreach($this->viewEngines as $viewEngine){
            if($viewEngine->getIsDefault()){
                $view = $viewEngine->findView($this->httpContext);
                if($view){
                    return new ViewResult($view, $params);
                }
            }
        }
    }
    
    public function load(){}
    
    public function render(string $response){
        $this->response->write($response);
    }
    
    public function __get($name) {
        return $this->getConfiguration()->get('serviceContainer')->get($name);
    }
}