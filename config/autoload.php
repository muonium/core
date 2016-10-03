<?php 
function autoloader($class_name) {
    if(!empty($class_name)) {
        $class_name = str_replace("\\", "/", $class_name);
        if(file_exists(ROOT.'/'.$class_name.".php")) {
            require_once(ROOT.'/'.$class_name.".php");
        }
    }
}

spl_autoload_register('autoloader');

