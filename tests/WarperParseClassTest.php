<?php
if (!defined("APP_PATH")){
    define("APP_PATH", dirname(dirname(__FILE__)));
}

class WarperTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        //echo "start";
    }

    public function testPase(){
        $class_name = 'Cola\Warper\DsMysql';

        $ret = Cola\Warper\Warper::parse_class($class_name);

        var_dump($ret);
    }

}
