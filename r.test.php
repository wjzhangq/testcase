<?php
ini_set("display_errors", "on");

$r_class = array(
	'path'=>"r.php",
	'class'=>'a',
	'methods' => array(
		array(
			'method' => 'foo',
			'params' => array(
				'a' => null,
			),
			'is_public'=>1,
			'is_return'=>0,
			'testcase' => array(
				array('input'=>array('a'=>1), 'output'=>null),
				array('input'=>array('a'=>2), 'output'=>null),
			),
		)
	)
);

function test_run($class_info){
	require_once($class_info['path']);

	if (!class_exists($class_info['class'])){
		throw new Exception(sprintf("class:%s is not in path:%s", $class_info['class'], $class_info['path']), 1);
	}


	$my_obj = new $class_info['class'];

	foreach($class_info['methods'] as $method){
		echo sprintf("test method:%s ... \n", $method['method']);
		foreach($method['testcase'] as $v){
			$my_input = $v['input'];
			if (count($my_input) < count($method['params'])){
				$my_input = array_merge($method['params'], $my_input);
			}

			call_user_func_array(array($my_obj, $method['method']), $my_input);
		}
	}
}


test_run($r_class);

?>