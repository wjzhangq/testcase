<?php
namespace Cola\Warper;

/**
 * 一个工具类
 * ds定义：
 * mysql://user:password@sql.wefit.com/wefit?charset=utf8
 */
class Warper
{
    static $cfg = NULL; //配置项
    static $di = array();  //依赖注入对象
    static $di_cfg = NULL;
    static $ds = array();  //数据源
    static $ds_cfg = NULL;

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

        $file_path = APP_PATH . '/config/di.inc.php';
        if (!is_array(self::$di_cfg)) {
            self::$di_cfg = include($file_path);
        }

        if (!isset(self::$di_cfg[$name])) {
            $new_obj  = new warper($name);
            self::$di[$name] = $new_obj;

            return $new_obj;
        }

        $my_cfg = self::$di_cfg[$name];

        if (empty($my_cfg['new'])) {
            $new_obj = new $my_cfg['class']();
        } else {
            $reflectionClass = new ReflectionClass($my_cfg['class']);
            $new_obj = $reflectionClass->newInstanceArgs($my_cfg['new']);
        }

        if (!empty($my_cfg['init'])) {
            foreach ($my_cfg['init'] as $k=>$v) {
                call_user_func_array(array($new_obj, $k), $v);
            }
        }

        if (!empty($my_cfg['attr'])) {
            foreach ($my_cfg['attr'] as $k => $v) {
                $new_obj->$k = $v;
            }
        }

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

        $file_path = APP_PATH . '/config/ds.inc.php';
        if (!is_array(self::$ds_cfg)) {
            self::$ds_cfg = include($file_path);
        }

        if (!isset(self::$ds_cfg[$sname])) {
            $new_obj  = new warper($sname);
            self::$ds[$sname] = $new_obj;

            return $new_obj;
        }

        $my_cfg = self::$ds_cfg[$sname];

        $post_my_cfg = parse_url($my_cfg);

        if (strlen($post_my_cfg['host']) != strcspn($post_my_cfg['host'], '1234567890.')){
            $post_my_cfg['ip'] = self::get_dns($post_my_cfg['host']);
        }else{
            $post_my_cfg['ip'] = $post['host'];
        }

        /*array(8){["scheme"]=>string(5)"mysql"["host"]=>string(15)"db.wefit.com.cn"["port"]=>int(6379)["user"]=>string(4)"user"["pass"]=>string(8)"password"["path"]=>string(3)"/db"["query"]=>string(12)"charset=utf8"["point"]=>"dbname"["attr"]=>["charset"=>"utf8"]["ip"]=>string(9)"127.0.0.1")}*/
        isset($post_my_cfg['path']) or $post_my_cfg['path'] = '/';
        isset($post_my_cfg['pass']) and $post_my_cfg['pass'] = urldecode($post_my_cfg['pass']);
        $post_my_cfg['point'] = trim($post_my_cfg['path'], '/'); //去除/的影响
        if (!empty($post_my_cfg['query'])){
            parse_str($post_my_cfg['query'], $my_attr);
        }else{
            $my_attr = array();
        }
        $post_my_cfg['attr'] = $my_attr;

        $class_name = self::get_ds_class($post_my_cfg['scheme']);

        $new_obj = new $class_name($post_my_cfg);
        self::$ds[$sname] = $new_obj;


        return $new_obj;
        
    }

    public static function get_ds_class($scheme){
        $default_protocol = array(
            'mysql' => 'Cola\Warper\DsMysql',
        );

        $file_path = APP_PATH . '/config/ds.inc.php';
        if (!is_array(self::$ds_cfg)) {
            self::$ds_cfg = include($file_path);
        }

        if (!empty(self::$ds_cfg['protocol'][$scheme])){
            return self::$ds_cfg['protocol'][$scheme];
        }

        if (isset($default_protocol[$scheme])){
            return $default_protocol[$scheme];
        }

        throw Exception(sprintf("protocol %s is unknow", $scheme));
    }

    public static function get_dns($hostname){
        $file_path = APP_PATH . '/config/ds.inc.php';
        if (!is_array(self::$ds_cfg)) {
            self::$ds_cfg = include($file_path);
        }

        if (empty(self::$ds_cfg['dns'][$hostname])){

            return gethostbyname($hostname);
        }

        $ret = self::$ds_cfg['dns'][$hostname];
        if (is_string($ret)){
            return $ret;
        }
        if (!is_array($ret)){
            return NULL;
        }

        $ret_count = count($ret);
        if ($ret_count < 2){
            return $ret[0];
        }

        return $ret[mt_rand() % $ret_count];
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
        if ($path_is_dir) {
            if ("" === $path_array[0]) {
                $file_path = APP_PATH . '/page/index.php';
                $class_name = 'page\\page';
            } else {
                $file_path = APP_PATH . '/' . implode(".", $path_array) . '.index.php';
                $class_name = 'page\\' . implode('\\', $path_array) . '\page';
            }
            $method = "index";
        } else {
            $tmp_path_array = array_slice($path_array, 0, $path_count - 1);
            $method = $path_array[$path_count -1];
            $method or $method = 'index';
            if ($tmp_path_array) {
                $file_path = APP_PATH . '/' . implode('.'. $tmp_path_array) . '.index.php';
                $class_name = 'page\\' . implode('\\', $tmp_path_array) . '\page';
            } else {
                $file_path = APP_PATH . '/page/index.php';
                $class_name = 'page\page';
            }

        }

        /* end app file path */
        if ($path_is_dir) {
            $tpl_path = APP_PATH . '/www/tpl.pc' . $path . 'index.tpl';
        } else {
            $tpl_path = APP_PATH . '/www/tpl.pc' . $path . '.tpl';
        }

        $data = array();
        if (file_exists($file_path)) {
            require_once($file_path);
            if (!class_exists($class_name)) {
                throw new \Exception(sprintf("class %s is not exist!", $class_name), 404);
            }
            $my_page = new $class_name();

            if (!method_exists($my_page, $method)) {
                throw new \Exception(sprintf("method %s is not exist!", $method), 404);
            }

            $data = $my_page->$method();
        }

        if (file_exists($tpl_path)) {
            $my_view = new \Smarty();

            if ($data) {
                foreach ($data as $k=>$v) {
                    $my_view->assign($k, $v);
                }
            }
            $my_view->display($tpl_path);
        } else {
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
        foreach ($tmp as $v) {
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
