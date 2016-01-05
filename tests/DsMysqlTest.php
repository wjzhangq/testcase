<?php
if (!defined("APP_PATH")){
	define("APP_PATH", dirname(dirname(__FILE__)));
}

class DsMysqlTest extends PHPUnit_Framework_TestCase
{

  protected $my_db;

  protected function setUp()
  {
      $this->my_db = new Cola\Warper\DsMysql();
  }

  protected function tearDown()
  {
      $this->db->close();
  }

  public function testQuery(){
      $this->db->query($a);
  }
}
