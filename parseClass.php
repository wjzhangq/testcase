<?php 

main($argv);

/**
 * 		array(
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
 */


function main($args){
	if (empty($args[1])){
		echo "filepath is require\n";
		return;
	}

	$class_path = $args[1];
	if (!file_exists($class_path)){
		echo sprintf("path %s is not exits!\n", $class_path);
		return;
	}

	$buf = file_get_contents($class_path);
	
	$n = preg_match_all('/([a-z|\s]+)function\s+([^\s|\(|\{)]+)\s*\(([^)]+)\)/', $buf, $match);
	
	$methods = array();
	$method_empty = array(
		'method' => '',
		'params' => array(),
		'is_public' => 0,
		'is_return' => 0,
		'is_static' => 0,
		'testcase' => array(),
	);
	if ($n > 0){
		for($i=0; $i < $n ; $i++){
			$row = $method_empty;
			$row['method'] = $match[2][$i];
			$row['params'] = parse_params($match[3][$i]);
			//var_dump($row);
		}
	}
}


function parse_params($buf){
	$ret = array();
	$tmp = explode(',', $buf);
	foreach ($tmp as $v) {
		$v = trim($v);
		if ($v && $v[0]== '$'){
			$pos = strpos($v, '=');
			if (false === $pos){
				$ret[substr($v, 1)] = null;
			}else{
				$my_key = trim(substr($v, 1, $pos-1));
				$my_value = trim(substr($v, $pos+1), ' \'"');
				$ret[$my_key] = $my_value;
			}
		}
	}

	var_dump($ret);
	return $ret;
}


