<?php

class NachoTest extends PHPUnit_Framework_TestCase
{
  public function testDi()
  {
    $my_log = Cola\Warper\Warper::di("log");
    $my_log->trace("kkk");
  }
}
