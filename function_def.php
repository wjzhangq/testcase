<?php 
$fun_def = array(
	'id' => 1,
	'name' => "test",
	"desc" => "测试程序",
	'param' => array(
		"a int",
		"b=0"
	),
	"return" => "r int"
);


function generate_function($fun_def){
	$tpl = <<<ETO
function %s (%s){
    %s
}
ETO;
	$my_name = $fun_def['name'];

	$my_param = array();
	if ($fun_def['param']){
		foreach($fun_def['param'] as $v){
			$post_v = parse_value_def($v);
			if (null === $post_v['default']){
				$my_param[] = '$' . $post_v['name'];
			}else{
				$my_param[] = '$' . $post_v['name'] . '=' . $post_v['default'];
			}
		}
	}

	$my_return = '';
	if ($fun_def['return']){
		$post_return = parse_value_def($fun_def['return']);
		$my_return = '$' . $post_return['name'] . '=null;' . "\n    //todo code\n\n" . '    return $'.$post_return['name'] . ";\n";
	}

	return sprintf($tpl, $my_name, implode(',', $my_param), $my_return);
}

/**
 * return array('name'=>'kk', 'default'=>null, 'type'=>'int')
 */
function parse_value_def($value_def){
	$ret = array('name'=>null, 'default'=>null, 'type'=>null);

	$value_def = trim($value_def);
	$my_pos = strpos($value_def, '=');
	if (false === $my_pos){
		$tmp = preg_split('/\s+/', $value_def);
		$ret['name'] = $tmp[0];
		$ret['type'] = isset($tmp[1]) ? $tmp[1] : null;
	}else{
		$tmp = explode('=', $value_def, 2);
		$ret['name'] = $tmp[0];
		$ret['default'] = $tmp[1];
		$ret['type']="wait";
	}

	return $ret;
}


var_dump(generate_function($fun_def));

