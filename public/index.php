<?php
$config = array_merge(
    include '../system/config/main.php',
    include '../system/config/database.php'
);

require_once '../vendor/autoload.php';
require_once '../system/bootstrap.php';

use system\components\App;

$app = new App($config);
$app->start();