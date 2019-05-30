<?php

namespace Mvc;

interface IHttpModule{
    
    public function load(Controller $controller) : void;
    public function unload(Controller $controller) : void;
}