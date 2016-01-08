<?php 
namespace Cola\Warper;
/**
 * mysql 数据操作类
 *
 * @require php >= 5.3
 *
 * query 高级用法：
 * $obj->query("select count(*) from my_table where a=1");
 * $obj->query("select @field from @table where a=1");
 * $obj->query("select @field from @table where a < @a", array('a'=>1));
 * $obj->query(array('a'=>1))
 */
class DsMysql
{
    protected $pdo; //pdo 连接
    protected $db_name = ""; //数据库表名
    protected $ds_name = ""; //数据源名称

    /**
     * 数据库初始化函数
     *  
     * @param array $param ["scheme" =>"mysql", "host" =>"db.wefit.com.cn", "port"=> 6379, "user" => "user", "pass"=> "password", "point" => "dbname", "attr" => ["charset"=>"utf8"], "ip"=> "127.0.0.1"]
     */
    public function __construct($param)
    {
        empty($param['port']) and $param['port'] = 3306;
        empty($param['user']) and $param['user'] = 'root';
        isset($param['pass']) or $param['pass'] = '';

        if (empty($param['attr']['charset'])){
            $dsn = sprintf("mysql:host=%s;port=%d;dbname=%s", $param['ip'], $param['port'],$param['point']);
        }else{
            $dsn = sprintf("mysql:host=%s;port=%d;dbname=%s;charset=%s", $param['ip'], $param['port'],$param['point'], $param['attr']['charset']);
        }

        $this->pdo = new \pdo($dsn, $param['user'], $param['pass']);
    }

    /**
     * 通过call变相直接调用pdo方法
     * @return [type] [description]
     */
    public function __call($key, $args){
        if (!method_exists($this->pdo, $key)){
            throw \Exception(sprintf("method %s is not exist!", $key));
        }

        call_user_func_array(array($this->pdo, $key), $args);
    }

    public function insert($sql, $param=array())
    {
        $post_sql = $sql;

        $stmt = $this->pdo->exec($sql);
        $last_id = false;
        if ($stmt > 0){
            $last_id =  $this->pdo->lastInsertId();
        }

        return $last_id;
    }

    public function delete($sql, $param)
    {
        return $this->update($sql, $param);
    }

    public function update($sql, $param)
    {
        $post_sql = $sql;

        $stmt = $this->pdo->exec($sql);
    }

    public function query($sql, $param=array())
    {
        $post_sql = $sql;
        $stmt = $this->pdo->query($sql, \PDO::FETCH_ASSOC);

        if (false === $stmt){
            $my_error = $this->pdo->errorInfo();
            throw new \Exception('query error:' . $my_error[2], $my_error[1]);
        }

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function query_one()
    {
    }

    public function formate($str, $param)
    {
        if (false === strrpos($str, '@')) {
            return $str;
        }

        isset($param['table']) or $param['table'] = $this->table;
        isset($param['fields']) or $param['fields'] = '*';

        $post_str = preg_replace_callback('/@(\w+)/', function ($match) use ($param) {
            return isset($param[$match[1]]) ? $param[$match[1]] : $match[0];
        }, $str);

        return $post_str;
    }
}
