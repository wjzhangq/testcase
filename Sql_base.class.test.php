<?php
require(str_replace('.test.php', '.php', basename(__FILE__)));

$a = new Sql_base();
$t = $a->formate("select @fields from @table where a < @a ", array('a'=>1));
var_dump($t);
