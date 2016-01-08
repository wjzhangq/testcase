<?php
if (!defined("APP_PATH")){
	define("APP_PATH", dirname(dirname(__FILE__)));
}

class DsMysqlTest extends PHPUnit_Framework_TestCase
{

  protected $my_db;

  protected function setUp()
  {
      $param = ["scheme" =>"mysql", "host" =>"db.wefit.com.cn", "port"=> 3306, "user" => "root", "pass"=> "123456", "point" => "test", "attr" => ["charset"=>"utf8"], "ip"=> "127.0.0.1"];
      $this->my_db = new Cola\Warper\DsMysql($param);
  }

  protected function tearDown()
  {
      //$this->my_db->close();
  }

  public function testInsert(){
      $age = mt_rand(1, 100000);
      $name = 'test_' . $age;
      $sql = "insert into test (`name`, `age`) value (\"$name\", $age)";

      $last_id = $this->my_db->insert($sql);
      var_dump($last_id);
  }

  public function testQuery(){
      $sql = "select * from test order by id desc limit 1";
      $ret = $this->my_db->query($sql);
      var_dump($ret);

  }

}
