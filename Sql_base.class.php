<?php
/**
 * 数据表基类
 *
 * @require php >= 5.3
 *
 * query 高级用法：
 * $obj->query("select count(*) from my_table where a=1");
 * $obj->query("select @field from @table where a=1");
 * $obj->query("select @field from @table where a < @a", array('a'=>1));
 * $obj->query(array('a'=>1))
 */
class Sql_base
{
    protected $table = ""; //数据库表名
    protected $ds_name = ""; //数据源名称
    protected $fields = array(); //字段定义

    /**
     * 获取一个全局实例
     * @return [type] [description]
     */
    public static function instance()
    {
    }

    public function __construct()
    {
    }

    public function insert()
    {
    }

    public function delete()
    {
    }

    public function update()
    {
    }

    public function query()
    {
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
