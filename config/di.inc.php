<?php
return array(
    'smarty' => array(
        "class" => "Smarty",
        "new" => array(),
        "init" => array(
            "setCompileDir" => array(APP_PATH . '/cache/compile'),
        ),
        "attr" => array(
            'debugging' => true,
            'cache_lifetime' => 120,
        ),
    ),
);
