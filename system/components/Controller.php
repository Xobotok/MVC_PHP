<?php


namespace system\components;


abstract class Controller {

    public $name;
    public $layout = 'main';

    public function __construct(string $name) {
        $this->name = $name;
    }
    public function render(string $view, array $params = []) {

    }
}