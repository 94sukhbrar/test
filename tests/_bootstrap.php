<?php
// This is global bootstrap for autoloading

define('YII_TEST', 'codeception');

require __DIR__ . '/../common.php';

$config = require (DB_CONFIG_PATH . '/web.php');


require (VENDOR_PATH . 'autoload.php');
require (VENDOR_PATH . 'yiisoft/yii2/Yii.php');

// Here you can initialize variables that will be available to your tests
require "LoginHelper.php";
