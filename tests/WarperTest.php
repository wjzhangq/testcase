<?php
if (!defined("APP_PATH")){
	define("APP_PATH", dirname(dirname(__FILE__)));
}

class WarperTest extends PHPUnit_Framework_TestCase
{
  public function testDi()
  {
    // $my_log = Cola\Warper\Warper::di("log");
    // $my_log->trace("kkk");
  }

  public function testDs(){
  	$my_api = Cola\Warper\Warper::ds('api');

  	var_dump($my_api);
  }
}
