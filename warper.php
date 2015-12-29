<?php

/**
 * 一个工具类
 * ds定义：
 * mysql://user:password@sql.wefit.com/wefit?charset=utf8
 */
class warper
{
    static $cfg = array(); //配置项
    static $di = array();  //依赖注入对象
    static $ds = array();  //数据源

    protected $name;

    /**
     * 初始化一个di
     * @param [type] $name [description]
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __call($key, $args)
    {
        var_dump($key, $args);
    }

    public function __set($key, $value)
    {
    }

    public function __get($key)
    {
    }

    public static function get_cfg($cfg_key)
    {
        $file_path = 'config.php';

        if (isset(self::$cfg[$cfg_key])) {
            return self::$cfg[$cfg_key];
        }

        //获取默认值
        $default_val = null;
        try {
            throw new Exception(__FILE__);
        } catch (Exception $e) {
            $my_trace = $e->getTrace()[0];
            $fp = fopen($my_trace['file'], 'r');
            $i = 0;
            while (!feof($fp)) {
                $buf = fgets($fp, 10240);
                $i++;

                if ($i < $my_trace['line']) {
                    continue;
                }

                if (false !== strpos($buf, "//")) {
                    $tmp = preg_split('/;\s*\/\//', $buf, 2);
                    $tmp[1] = isset($tmp[1]) ? trim($tmp[1]) : '';
                    try {
                        eval('$default_val=' . $tmp[1] . ';');
                    } catch (Exception $e) {
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
     * @return [type] [description]
     */
    public static function di($name)
    {
        if (isset(self::$di[$name])) {
            return self::$di[$name];
        }

        $new_obj  = new warper($name);
        self::$di[$name] = $new_obj;

        return $new_obj;
    }

    /**
     * 获取一个数据源头
     * @param  [type] $sname [description]
     * @return [type] [description]
     */
    public static function ds($sname)
    {
        if (isset(self::$ds[$sname])) {
            return self::$ds[$sname];
        }

        $new_obj = new warper($sname);
        self::$ds[$sname] = $new_obj;

        return $new_obj;
    }

    /**
     * todo: delete
     * @param  [type] $dns [description]
     * @return [type] [description]
     */
    public function get_datasouce($dns)
    {
        $raw = parse_url($dns);
        $ret = null;
        switch ($raw['scheme']) {
            case 'mysql':
                $my_dns = "mysql:host=".$raw['host'].";dbname=".ltrim($raw['path'], '/').";charset=utf8";
                $ret = new SimpleMysql($my_dns, $raw['user'], $raw['pass']);
                break;

            default:
                # code...
                break;
        }

        return $ret;
    }
}
