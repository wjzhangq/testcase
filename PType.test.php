<?php 

require("PType.php");
$dt = array(
	'user' => array('name'=>'string', 'age'=>0),
	'group'=> array('user'=>'user', 'gname'=>"test"),
);

foreach ($dt as $k => $v) {
	PType::register($k, $v);
}

/*
$t = array('foo'=>1, 'bar'=>array('foo'=>2, 'bar'=>3));

var_dump(PType::fill($t, array('bar'=>array('bar'=>4))));
var_dump($t);
*/

PType::register("student", array("_"=>"user", "class"=>"string"));//展开用户的所有项

//var_dump(PType::$dt);
//var_dump(PType::$dt_default);
//var_dump(PType::$dt_mix);

$user1 = new PType("user", array('name'=>'wj')); //find dt
var_dump($user1->hash());
var_dump($user1->toArray());


$group = new PType("group", array('user'=>$user1, 'gname'=>'test2'));
// $group['gname'] = 'this ok';
// var_dump($group->toArray());
exit();
//匿名类型
$user2 = new PType(array("name"=>"string", "age"=>"integer kkk"), array('name'=>"wy"));

var_dump($user2->typeHash());
var_dump($user1->toJson());

//$user1['name'] = 'dj';
//var_dump($user1['name']);