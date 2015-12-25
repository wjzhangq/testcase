<?php

class a{
    function __construct(){

    }

    function foo($a){
        echo $a . "\n";
    }

    function foo1($b=1){
        echo $b . "\n";
    }
}


// $my_r = new ReflectionClass('a');
// $methods = $my_r->getMethods();
// foreach ($methods as $k => $v) {
//     var_dump($k);
//     var_dump($v->getParameters());
// }