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
			),
		)
	)
);

var_dump($r_class);