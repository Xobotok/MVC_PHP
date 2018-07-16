<?php
namespace system\components;
class App {
use Singleton;

public $config;
public $request;
public $connection;

public $controller;
public $action;

public static $current;

public function __construct(array $config) {
    if (empty(static::$current)) {
        $this->config = $config;
        static::$current = $this;
    } else {
        return static::$current;
    }
}
public function start(){
    $this->connection = $this->setConnection();
    $this->route($_GET);

    var_dump(App::$current);
    var_dump($_GET);
}
private function setConnection() {
    $settings = $this->config['db'];

    $host = $settings['host'];
    $user = $settings['user'];
    $password = $settings['password'];
    $database = $settings['database'];
    $charset = 'utf8';

    try {
        $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
        $dbh = new \PDO($dsn, $user, $password);
    } catch (\PDOException $error) {
        echo 'Database connection error - '.$error->getMessage();
        die();
    }
}

    /**
     * @param $request
     */
    private function route($request){
    $router = new Router($request);

    $this->controller = $router->getController();
    $this->action = $router->getAction();

    try {
        ($this->controller)->{$this->action};
    } catch (\Exception $error) {
        echo $error-> getMessage(); die();
    }
}
}