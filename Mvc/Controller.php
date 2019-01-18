<?php

namespace Mvc;

use Core\Common\Obj;
use Core\Common\Str;
use Core\Configuration\Configuration;
use Core\Web\Http\HttpException;
use Core\Web\Http\GenericController;
use Core\Web\Http\HttpContext;
use Core\Web\Http\Request;
use Core\Web\Http\Response;
use Mvc\View\ViewEngineCollection;

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

        if(!$parameters->exists('controller')){
            $parameters->add('controller', (string)Str::set(get_called_class())->getAfterLastIndexOf('\\'));
        }
        
        if(!$parameters->exists('action')){
            $parameters->add('action', 'index');
        }

        $action = $parameters->get('action');

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
        }else{
            throw new ActionNotFoundException(sprintf("action '%s' not found.", $action));
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

    public function view(array $params = [], string $viewName = ''){
        
        if($this->viewEngines->count() == 0){
            throw new HttpException("No ViewEngine registered.");
        }

        foreach($this->viewEngines as $viewEngine){
            if($viewEngine->getIsDefault()){
                $view = $viewEngine->findView($this->httpContext, $viewName);  
                if($view){
                    return new ViewResult($view, $params);
                }
            }
        }
        throw new HttpException("No default ViewEngine found.");
    }
    
    public function json(array $params = [], int $options = null){
        return new JsonResult($this->getResponse(), $params, $options);
    }
    
    public function redirect(string $location, $params = null){
        return new RedirectResult($this->getRequest(), $this->getResponse(), $location, $params);
    }

    public function load(){}
    
    public function render(string $response){
        $this->response->write($response);
    }
    
    public function __get($name) {
        return $this->getConfiguration()->get('serviceContainer')->get($name);
    }
}