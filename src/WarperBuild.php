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


	/**
	 * 开启一个web服务用于调试代码
	 */
	public static function Web($port=8080){

	}
}