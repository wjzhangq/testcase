<?php 

/**
 * 一个工具类
 */
class warper{
	static $cfg = array();

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
}