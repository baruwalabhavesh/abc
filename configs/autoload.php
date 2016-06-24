<?php

if (file_exists(CONFIG_DIR . DS . 'Config.Inc.php')) {
        require_once CONFIG_DIR . DS . 'Config.Inc.php';
}
if (file_exists(CONFIG_DIR . DS . 'api.php')) {
        require_once CONFIG_DIR . DS . 'api.php';
}
if (file_exists(CONFIG_DIR . DS . 'cache.php')) {
        require_once CONFIG_DIR . DS . 'cache.php';
}


function sc_autoloader($class) {
        include CONFIG_DIR . DS . $class . '.php';
}

spl_autoload_register('sc_autoloader');

// Or, using an anonymous function as of PHP 5.3.0
spl_autoload_register(function ($class) {
        include CONFIG_DIR . DS . $class . '.php';
});

try {
        //global $objDB;
        
        $objDB = new DB('read');
        $objDBWrt = new DB('write');
	$objJSON = new JSON();
	$memcached = new Cache();

} catch (Exception $x) {
        echo $x;
}

if (file_exists(CONFIG_DIR . DS . 'routes.php')) {

        require_once CONFIG_DIR . DS . 'routes.php';
}

