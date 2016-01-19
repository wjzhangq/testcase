<?php

if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

require APP_PATH . "/vendor/autoload.php";

//array('class_path'=>'', 'class'=>'', 'method'=>'', 'tpl_path'=>'', 'short_tpl_path'=>'');
$app_path =  Cola\Warper\Warper::parse_path();

try{
	/*获取数据*/
	$data = array();
	if (file_exists($app_path['class_path'])){
		require_once($app_path['class_path']);
		$class_name = $app_path['class'];
	    if (!class_exists($app_path['class'])) {
	        throw new Exception(sprintf("class %s is not exist!", $app_path['class']), 404);
	    }

		$class_info = Cola\Warper\Warper::parse_class($app_path['class']);

		if (!isset($class_info[$app_path['method']])){
			throw new Exception(sprintf("method %s is not exist!", $app_path['method']), 404);
		}

		$param = $class_info[$app_path['method']]['param'];

		$my_page = new $app_path['class']();
		if (!$param){
			//无参数
			$data = $my_page->$app_path['method']();
		}else{
			foreach ($param as $k => $v) {
				if (isset($_GET[$k])){
					$param[$k] = $_GET[$k];
				}else{
					//不存在，检查是否有默认值
					if (!isset($v)){
						throw new Exception(sprintf("param %s is require!", $k), 404);
					}
				}
			}
			$data = call_user_func_array(array($my_page, $app_path['method']), $param);
		}
	}

	/*获取模板*/
	if (file_exists($app_path['tpl_path'])) {
	    $my_view = Cola\Warper\Warper::di('smarty');
	    if ($data) {
	        foreach ($data as $k=>$v) {
	            $my_view->assign($k, $v);
	        }
	    }
	    $my_view->display($app_path['tpl_path']);
	} else {
	    echo json_encode($data);
	}

}catch(Exception $e){
	var_dump($e);
}
