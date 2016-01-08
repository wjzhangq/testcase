<?php
return array(
	'cache' => "redis://127.0.0.1:6379",
	'api' => "http://api.wefit.com.cn",
	'default_db' => "mysql://root:123456@db.wefit.com.cn:3306/test?charset=utf8",
	'protocol' => array(
		"mysql" => 'Cola\Warper\DsMysql',
	),
	'dns' => array(
		'api.wefit.com.cn' => array('127.0.0.1', '10.0.0.1'),
		'db.wefit.com.cn' => '127.0.0.1',
	),
);