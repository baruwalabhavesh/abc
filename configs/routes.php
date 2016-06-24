<?php

// Split the URL into parts 
$action = '';
$query = '';

$library = scandir(LIBRARY);
$cust_includes = scandir(INCLUDES . '/customer');
$mer_includes = scandir(INCLUDES . '/merchant');
$services = scandir(SERVICES);
$admin_includes = scandir(INCLUDES . '/admin');

if (!empty($library)) {
        unset($library[0]);
        unset($library[1]);
}
if (!empty($admin_includes)) {
        unset($admin_includes[0]);
        unset($admin_includes[1]);
}

if (!empty($cust_includes)) {
        unset($cust_includes[0]);
        unset($cust_includes[1]);
}

if (!empty($mer_includes)) {
        unset($mer_includes[0]);
        unset($mer_includes[1]);
}
if (!empty($services)) {
        unset($services[0]);
        unset($services[1]);
}

if (isset($_GET['load'])) {
        $params = array();
        $params = explode("/", $_GET['load']);

        $controller = $params[0];

        //customer routings
        if (strtolower($controller) != 'merchant') {

                //check in includes folder
                if (in_array($controller, $cust_includes)) {
                        $action = '/includes';
                        $action .= '/customer';
                        $action .= '/' . $controller;
                } else if (in_array($controller, $library)) { //check in library
                        $action = '/libraries';
                        $action .= '/' . $controller;
                } else if (in_array($controller, $services)) { //check in services
                        $action = '/services';
                        $action .= '/' . $controller;
                } else if ($controller == 'admin') { //admin route
                        if (in_array($params[1], $admin_includes)) {
                                $action = '/includes';
                                $action .= '/' . $controller;
                        } else {
                                $action = '/views';
                                $action .= '/' . $controller;
                                if (empty($params[1])) {
                                        $action .='/index.php';
                                }
                        }
                } else {
                        $action = '/views';
                        $action .= '/customer';
                        $action .= '/' . $controller;
                }
        } else { // merchant routings
                if (in_array($params[1], $mer_includes)) {
                        $action = '/includes';
                        $action .= '/' . $controller;
                } else if (in_array($params[1], $library)) {
                        $action = '/libraries';
                        //$action .= '/' . $controller;
                } else {
                        $action = '/views';
                        $action .= '/' . $controller;
			if (empty($params[1])){			
				$action .='/index.php';
			}
                }
        }

        if (isset($params[1]) && !empty($params[1])) {
                $action .= '/' . $params[1];
        }

        if (isset($params[2]) && !empty($params[2])) {
                $query = '/' . $params[2];
        }
        if (isset($params[3]) && !empty($params[3])) {
                $query .= '/' . $params[3];
        }
        if (isset($params[4]) && !empty($params[4])) {
                $query .= '/' . $params[4];
        }
}else{
        $action .='/views';
        $action .='/customer';
        $action .='/index.php';
}
//echo ROOT . $action . $query;exit;
require_once ROOT . $action . $query;

