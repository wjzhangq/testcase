<?php 

require("PType.php");

$t = array('foo'=>1, 'bar'=>array('foo'=>2, 'bar'=>3));

var_dump(PType::fill($t, array('bar'=>array('bar'=>4))));
var_dump($t);