<?php
define('ROOT', dirname(__DIR__));
define('DEBUG', true);

if(DEBUG) {
    ini_set('display_errors', 'on');
}

spl_autoload_register(function ($className){
    $class = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    $class = ROOT."/{$class}.php";

    if (file_exists($class)) {
        require_once $class;
    } else {
        echo "Class '<b>{$className}</b>' not found!";
        die();
    }
});
