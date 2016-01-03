<?php

if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

require APP_PATH . "/vendor/autoload.php";

Cola\Warper\Warper::web();
