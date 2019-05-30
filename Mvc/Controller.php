<?php

namespace Mvc;

use Core\Common\Obj;
use Core\Common\Str;
use Core\Configuration\Configuration;
use Core\Web\Http\HttpException;
use Core\Web\Http\IGenericController;
use Core\Web\Http\HttpContext;
use Core\Web\Http\Request;
use Core\Web\Http\Response;
use Mvc\View\ViewEngines;

abstract class Controller implements IGenericController{
    
    private $config = null;
    private $httpContext = null;
    private $request = null;
    private $response = null;
    private $viewEngines = null;
    
    public final function service(Configuration $config, HttpContext $httpContext) : void{

        $this->config = $config;
        $this->config->add('modules', new ModulesSection());
        $this->config->add('serviceContainer', new ServiceSection());
        $this->config->add('viewEngines', new ViewEngineSection());

        $this->httpContext = $httpContext;
        $this->request = $httpContext->getRequest();
        $this->response = $httpContext->getResponse();
        $this->viewEngines = $this->config->get('viewEngines');
        $modules = $this->config->get('modules');

        $parameters = $this->request->getParameters();

        if(!$parameters->exists('controller')){
            $parameters->add('controller', (string)Str::set(get_called_class())->getAfterLastIndexOf('\\'));
        }
        
        if(!$parameters->exists('action')){
            $parameters->add('action', 'index');
        }

        $action = $parameters->get('action');

        $reflect = Obj::from($this);
        
        if($reflect->hasMethod($action)){
            
            foreach($modules as $module){
                $module->load($this);
            }

            $this->load();
            
            $params = $reflect->getMethodParameters($action);
            
            $db = new DefaultModelBinder();
            $actionArgs = [];
            
            foreach($params as $param){
                $defaultValue = $param->isOptional() ? $param->getDefaultValue() : null;
                $actionArgs[] = $db->bind($this->request, new BindingContext($param->name, $param->getType(), $param->isOptional(), $defaultValue));
            }

            $result = $reflect->invokeMethod($action, $actionArgs);

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
            
            foreach($modules as $module){
                $module->unload($this);
            }
        }else{
            throw new ActionNotFoundException(sprintf("action '%s' not found.", $action), $action, $this->request->getUrl()->getRawUri());
        }
    }
    
    public function getConfiguration() : Configuration{
        return $this->config;
    }
    
    public function getRequest() : Request{
        return $this->request;
    }
    
    public function getResponse() : Response{
        return $this->response;
    }
    
    public function getViewEngines() : ViewEngines{
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
        return $this->config->get('serviceContainer')->get($name);
    }
}