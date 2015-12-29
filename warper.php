<?php 

/**
 * 一个工具类
 */
class warper{
	static $cfg = array();
	static $di = array();

	protected $name;

	/**
	 * 初始化一个di
	 * @param [type] $name [description]
	 */
	public function __construct($name){
		$this->name = $name;
	}

	public function __call($key, $args){
		var_dump($key, $args);
	}

	public function __set($key, $value){

	}

	public function __get($key){

	}

	public static function get_cfg($cfg_key){
		$file_path = 'config.php';

		if (isset(self::$cfg[$cfg_key])){
			return self::$cfg[$cfg_key];
		}

		//获取默认值
		$default_val = null;
		try{
			throw new Exception(__FILE__);
		}catch(Exception $e){
			$my_trace = $e->getTrace()[0];
			$fp = fopen($my_trace['file'], 'r');
			$i = 0;
			while(!feof($fp)){
				$buf = fgets($fp, 10240);
				$i++;

				if ($i < $my_trace['line']){
					continue;
				}

				if (false !== strpos($buf, "//")){
					$tmp = preg_split('/;\s*\/\//', $buf, 2);
					$tmp[1] = isset($tmp[1]) ? trim($tmp[1]) : '';
					try{
						eval('$default_val=' . $tmp[1] . ';');
					}catch(Exception $e){
						throw $e;
					}
				}
				
				break;
			}
		}
		self::$cfg[$cfg_key] = $default_val;

		//todo:save config

		return $default_val;
	}

	/**
	 * 依赖注入实现
	 * @param  [type] $name 模块名称
	 * @return [type]       [description]
	 */
	public static function di($name){
		if (isset(self::$di[$name])){
			return self::$di[$name];
		}

		$new_obj  = new warper($name);
		self::$di[$name] = $new_obj;

		return $new_obj;
	}
}