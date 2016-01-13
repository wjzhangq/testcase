<?php

var_dump(my_parse('./ArrayUtil.php'));

function my_parse($path){
	$ret = array();
	$pre_class_list = get_declared_classes();
	include($path);
	$diff_list = array_diff(get_declared_classes(), $pre_class_list);

	foreach($diff_list as $class_name){
		$method_list = array();
		$ref = new ReflectionClass($class_name);
		$methods = $ref->getMethods();
			$i = 0;
		foreach ($methods as $method_ref) {
			$i++;
			if ($i > 3){
				break;
			}
			$method = array('name'=>$method_ref->name, 'is_public'=>$method_ref->isPublic(), 'is_static'=>$method_ref->isStatic());

			//$comment = $method_ref->getDocComment();


			$params = $method_ref->getParameters();
			$param_list = array();
			foreach($params as $param_ref){
				if ($param_ref->isOptional()){
					$param_list[$param_ref->name] = $param_ref->getDefaultValue();
				}else{
					$param_list[$param_ref->name] = NULL;
				}
			}
			$method['param'] = $param_list;

			$method_list[$method['name']] = $method;
		}

		$ret[$class_name] = $method_list;
	}

	return $ret;
}