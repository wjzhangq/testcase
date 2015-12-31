<?php

if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

require APP_PATH . "/warper.php";

warper::web();
