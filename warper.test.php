<?php
require("warper.php");



$cache = warper::get_cfg("cache"); //array("a"=>1)
$tt = warper::get_cfg("tt"); //"错误"

$my_log = warper::di("log");
$my_log->trace("kkk");