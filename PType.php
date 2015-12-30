<?php 

/**
 * 定义PHP中Type, like golang
 * todo: 数组
 * todo: 匿名类型
 * todo: 参数检查
 */
class PType implements ArrayAccess {
	static $dt = array(); //类型列表
	static $dt_default = array(); //类型默认值
	static $dt_mix = array(); //有混合的字段

	static $system_dt = array('integer'=>1, 'boolean'=>1, 'double'=>1, 'string'=>1, 'array'=>1, 'NULL'=>1);
	var $__name__;
	var $__dict__ = array();

	public function __construct($name, $data=null){
		if (is_string($name)){
			//自定义类型
			if (!isset(self::$dt[$name])){
				throw new Exception(sprintf("Unkonw User Type %s", $name));
			}
			$this->__name__ = $name;

			//根据dt构建真实数据
			$this->__dict__ = self::$dt_default[$name];
		}else{

		}

		if (is_array($data)){
			$my_dt = &self::$dt[$name];
			foreach($data as $k=>$v){
				if (!isset($my_dt[$k])){
					continue;
				}

				//类型转换
				//todo: 参数转换
				$v_type = $my_dt[$k];
				$this->__dict__[$k] = $v;
			}
		}	
	}

	public function typeHash(){
		$my_dt = &self::$dt[$this->__name__];

		$hash1 = 0;
		$hash2 = 0;

		foreach($my_dt as $k=>$v){
			$hash1 ^= crc32($k);
			$hash2 ^= crc32("$k=$v");
		}

		return ($hash1 & 0xFFFF) << 16 | $hash2 & 0xFFFF;
	}


	public function hash(){
		$my_hash = 0;
		foreach($this->__dict__ as $k=>$v){
			if ($v instanceof PType){
				$my_hash ^= $v->hash();
			}else{
				//todo:有bug
				$my_hash ^= crc32($k . '=' . strval($v));
			}
		}
		return $my_hash;
	}

	public function toArray(){
		if (!isset(self::$dt_mix[$this->__name__])){
			return $this->__dict__;
		}else{
			//拷贝
			$ret = array();
			foreach($this->__dict__ as $k=>$v){
				if ($v instanceof PType){
					$ret[$k] = $v->toArray();
				}else{
					$ret[$k] = $v;
				}
			}
			return $ret;
		}
	}

    //implements ArrayAccess
    function offsetExists($offset)
    {
        return isset($this->__dict__[$offset]);
    }
    
    //implements ArrayAccess
    function offsetGet($offset)
    {
    	return $this->__dict__[$offset];
    }
    
    //implements ArrayAccess
    function offsetSet($offset, $value)
    {
    	if (!isset(self::$dt[$this->__name__][$offset])){
    		return;
    	}
    	$value_type = self::$dt[$this->__name__][$offset];

    	$this->__dict__[$offset] = $value;
    }
    
    //implements ArrayAccess
    function offsetUnset($offset)
    {
        //do nothing
    }
    

	/**
	 * 注册新的类型
	 * @param  [type] $name   [description]
	 * @param  [type] $struct [description]
	 * @return [type]         [description]
	 */
	static public function register($name, $struct){
		if (isset(self::$dt[$name])){
			throw Exception(sprintf("PType %s is Exist!", $name));
		}

		$my_struct = array();
		$my_defalut = array();
		$my_mix = array();
		foreach($struct as $k=>$v){
			if ('_' == $k[0]){
				//继承
				if (!isset(self::$dt[$v])){
					//一个未知的类型
					throw new Exception(sprintf("Unkonw extend type %s", $v));
				}
				$my_struct = array_merge($my_struct, self::$dt[$v]);
				if (isset(self::$dt_default[$v])){
					$my_defalut = array_merge($my_defalut, self::$dt_default[$v]);
				}
				if (isset(self::$dt_mix[$v])){
					$my_mix = array_merge($my_mix, self::$dt_mix[$v]);
				}
				continue;
			}

			if (!is_string($v)){
				$my_struct[$k] = gettype($v);
				$my_defalut[$k] = $v;
			}else{
				if (strncmp('[]', $v, 2) == 0){
					//数组特殊处理
					$post_v = substr($v, 2);
					if (!isset(self::$system_dt[$post_v]) && !isset(self::$dt[$post_v])){
						throw new Exception(sprintf("Unkonw type %s", $post_v));
					}
					$my_struct[$k] = $v;
					$my_defalut[$k] = array();
				}else{
					if (!isset(self::$system_dt[$v]) && !isset(self::$dt[$v])){
						//一个未知的类型,作为字符串默认值
						$my_struct[$k] = "string";
						$my_defalut[$k] = $v;
						//throw new Exception(sprintf("Unkonw type %s", $v));
					}else{
						$my_struct[$k] = $v;
						if (isset(self::$system_dt[$v])){
							$my_defalut[$k] = NULL;
						}else{
							$my_defalut[$k] = new PType($v);
							$my_mix[$k] = $v;
						}
					}
				}
			}
		}

		//注册成功
		self::$dt[$name] = $my_struct;
		if ($my_defalut){
			self::$dt_default[$name] = $my_defalut;
		}
		if ($my_mix){
			self::$dt_mix[$name] = $my_mix;
		}
	}

	/**
	 * 用户var去填充$target
	 * @param  [array] &$target 填充对象，使用引用
	 * @param  [array] $var     数据来源
	 * @return [array]            修改过的key
	 */
	static public function fill(&$target, $var){
		$ret = array();

		return $ret;
	}
}
