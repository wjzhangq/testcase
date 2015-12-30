<?php
return array(
	'cache' => "redis://127.0.0.1:6379",
	'api' => "http://api.wefit.com.cn",
	'default_db' => "mysql://user:password@db.wefit.com.cn:6379/db?charset=utf8",
	'dns' => array(
		'api.wefit.com.cn' => array('127.0.0.1', '10.0.0.1'),
		'db.wefit.com.cn' => '127.0.0.1',
	),
);