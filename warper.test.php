<?php
require("warper.php");

$cache = warper::get_cfg("cache"); //array("a"=>1)

$my_log = warper::di("log");
$my_log->trace("kkk");