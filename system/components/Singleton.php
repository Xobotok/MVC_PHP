<?php

namespace system\components;

trait Singleton {
    public function __clone(){}
    public function __wakeup(){}
}