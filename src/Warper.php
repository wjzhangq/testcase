<?php
namespace Cola\Warper;

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
     * @return [type] [description]
     */
    public static function var_export54($var, $indent="")
    {
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

        if (NULL === self::$cfg) {
            if (!file_exists($file_path)) {
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
     * web 路由方式
     *
     * @return [type] [description]
     */
    public static function web()
    {
        /*get url path*/
        $uri = $_SERVER['REQUEST_URI'];
        $tmp_uri = parse_url($uri);
        $path = $tmp_uri['path'];
        /*end get path*/

        $path_is_dir = ('/' == $path[strlen($path) -1]);
        $path_array = explode("/", trim($path, '/'));
        $path_count = count($path_array);

        //$app_path = str_replace('/', '.', trim($path, '/'));

        /* app file path */
        if ($path_is_dir){
            if ("" === $path_array[0]){
                $file_path = APP_PATH . '/page/index.php';
                $class_name = 'page\\page';
            }else{
                $file_path = APP_PATH . '/' . implode(".", $path_array) . '.index.php';
                $class_name = 'page\\' . implode('\\', $path_array) . '\page';
            }
            $method = "index";
        }else{
            $tmp_path_array = array_slice($path_array, 0, $path_count - 1);
            $method = $path_array[$path_count -1];
            $method or $method = 'index';
            if ($tmp_path_array){
                $file_path = APP_PATH . '/' . implode('.'. $tmp_path_array) . '.index.php';
                $class_name = 'page\\' . implode('\\', $tmp_path_array) . '\page'; 
            }else{
                $file_path = APP_PATH . '/page/index.php';
                $class_name = 'page\page';
            }
            
        }

        /* end app file path */
        if ($path_is_dir){
            $tpl_path = APP_PATH . '/www/tpl.pc' . $path . 'index.tpl';
        }else{
            $tpl_path = APP_PATH . '/www/tpl.pc' . $path . '.tpl';
        }
        
        $data = array();
        if (file_exists($file_path)){
            require_once($file_path);
            if (!class_exists($class_name)){
                throw new \Exception(sprintf("class %s is not exist!", $class_name), 404);
            }
            $my_page = new $class_name();

            if(!method_exists($my_page, $method)){
                throw new \Exception(sprintf("method %s is not exist!", $method), 404);
            }

            $data = $my_page->$method();
        }

        if (file_exists($tpl_path)){
            $my_view = new \Smarty();

            if ($data){
                foreach($data as $k=>$v){
                    $my_view->assign($k, $v);
                }
            }
            $my_view->display($tpl_path);
        }else{
            echo json_encode($data);
        }
    }

    public static function rexplode($step, $buf, $num=0)
    {
        if ($num == 0) {
            return explode($step, $buf);
        }

        $tmp = explode($step, $buf);
        $tmp_count = count($tmp);

        if ($tmp_count == $num) {
            return $tmp;
        }

        if ($tmp_count > $num) {
            $diff_count = $tmp_count - $num;
            $ret = array_slice($tmp, -$num);
            $ret[0] = implode($step, array_slice($tmp, 0, $diff_count+1));

            return $ret;
        }

        $diff_count = $num - $tmp_count;
        $ret = array();
        for ($i=0; $i < $diff_count; $i++) {
            $ret[] = null;
        }
        foreach($tmp as $v){
            $ret[] = $v;
        }

        return $ret;
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
