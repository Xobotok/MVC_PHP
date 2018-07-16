<?php

namespace system\components;


class Router {
    private $_data;
    private $controllerName;
    private $actionName;

public function __construct($request){
    $this->_data = $request;
    }
    public function getController()
    {
        $items = explode('/', $this->_data['route']);
        $this->controllerName = $items[0];
        $this->actionName = $items[1];

        $clearController = $this->controllerClear($this->controllerName);
        $controllerPath = ROOT . "/app/controllers/{$clearController}Controller.php";

        if (file_exists($controllerPath)) {
            try {
            require_once $controllerPath;

            $controller = "app\controllers\\".$clearController."Controller";
            return new $controller($this->controllerName);
            } catch (\Exception $error) {
    echo $error->getMessage(); die();
            }
        } else {
            echo "Class '{$clearController}' not found"; die();
        }
    }
    public function getAction() {
    $clearAction = ucfirst($this->actionName);
        return "action{$clearAction}";

    }
    function controllerClear($string) {
    $sub_result = explode('-',$string);
        $new_result = '';
    foreach ($sub_result as $val) {
        $newval = ucfirst($val);
        $new_result .= $newval;
    }
    return $new_result;
    }

}
