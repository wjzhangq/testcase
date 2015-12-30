<?php
if (!defined("APP_PATH")){
    define("APP_PATH", dirname(__FILE__));
}

/**
 * 一个工具类
 * ds定义：
 * mysql://user:password@sql.wefit.com/wefit?charset=utf8
 */
class warper
{
    static $cfg = NULL; //配置项
    static $di = array();  //依赖注入对象
    static $ds = array();  //数据源

    protected $name;

    /**
     * init di
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
    /*end di*/

    /**
     * 格式化配置文件
     * @param  [type] $var    [description]
     * @param  string $indent [description]
     * @return [type]         [description]
     */
    public static function var_export54($var, $indent="") {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                         . ($indexed ? "" : self::var_export54($key) . " => ")
                         . self::var_export54($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }

    public static function get_cfg($cfg_key)
    {
        $file_path = APP_PATH . '/config/cfg.inc.php';

        if (NULL === self::$cfg){
            if (!file_exists($file_path)){
                throw Exception(sprintf("cfg file %s is not exist", $file_path));
            }

            self::$cfg = include($file_path);
        }

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

        file_put_contents($file_path, "<?php\nreturn " . self::var_export54(self::$cfg) . ";");

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
