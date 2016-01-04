<?php
/**
 * 框架代码生成类
 */
namespace Cola\Warper;

class WarperBuild{
	
	/**
	 * 生成目录
	 * @return [type] [description]
	 */
	public static function buildDirTree($root_path){
		$dir_tree = array(
			'www/' => '网站根目录，只有该目录下文件才能被访问到',
			'www/static/' => '静态资源文件夹',
			'www/static/js/' => 'js目录',
			'www/static/css/' => 'css目录',
			'www/static/images' => '图片目录',
			'www/tpl.pc/' => 'pc端模板目录',
			'www/demo.pc/' => 'pc端demo设计页面',
			'page/' => '控制器类目录',
			'data/' => '数据模型目录',
			'config/' => '配置文件目录',
			'cache/' => 'cache目录，包含smarty编译文件，session文件等等',
			'cache/compile/' => 'smarty 编译文件目录',
			'scripts/' => '后台脚本',
			'logs/' => '网站日志目录',
		);
		$file_list = array(
			'www/index.php' => 1,
			'config/cfg.inc.php' => 1,
			'config/di.inc.php' => 1,
			'config/ds.inc.php' =>1,
		);

		$root_path .= '/';

		foreach ($dir_tree as $k=>$v) {
			$path = $root_path . $k;
			if (!is_dir($path)){
				echo sprintf("build dir %s ...\n", $path);
				if(!mkdir($path)){
					throw new Exception(sprintf("dir %s make falure", $path), -10);
				}

				file_put_contents($path . "/readme.txt", $v . "\n");
			}
		}
	}

	public static function buildDirFile($root_path){
		$tpl_list = array();
		$tpl_list['www/index.php'] = <<<ETO
<?php

if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

require APP_PATH . "/vendor/autoload.php";

Cola\Warper\Warper::web();
ETO;

		$cfg_list = array();
		$cfg_list['config/cfg.inc.php'] = array('debug'=>false);
		$cfg_list['config/di.inc.php'] = array(
		    'smarty' => array(
		        "class" => "Smarty",
		        "new" => array(),
		        "init" => array(
		            "setCompileDir" => array($root_path . '/cache/compile'),
		        ),
		        "attr" => array(
		            'debugging' => true,
		            'cache_lifetime' => 120,
		        ),
		    ),
		);

		$cfg_list['config/ds.inc.php'] = array(
			'cache' => "redis://127.0.0.1:6379",
			'api' => "http://api.wefit.com.cn",
			'default_db' => "mysql://user:password@db.wefit.com.cn:6379/db?charset=utf8",
			'dns' => array(
				'api.wefit.com.cn' => array('127.0.0.1', '10.0.0.1'),
				'db.wefit.com.cn' => '127.0.0.1',
			),
		);

		$root_path .= '/';
		foreach($tpl_list as $k=>$v){
			$path = $root_path . $k;
			if (!file_exists($path)){
				echo sprintf("build file %s ...\n", $path);
				file_put_contents($path, $v);
			}
		}

		foreach ($cfg as $key => $value) {
			$path = $root_path . $k;
			if (!file_exists($path)){
				echo sprintf("build file %s ...\n", $path);
				file_put_contents($path, "<?php\n return " . Warper::var_export54($v) . "\n");
			}
		}
	}


	/**
	 * 开启一个web服务用于调试代码
	 */
	public static function Web($port=8080){

	}
}