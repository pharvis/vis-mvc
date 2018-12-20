<?php
spl_autoload_register(function(string $class){
    $file = __DIR__.'/'. str_replace('\\', '/', $class) . '.php';
    if(is_file($file)){
        include $file;
    }
});
__HALT_COMPILER();