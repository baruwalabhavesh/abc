<?php

echo "-------------------MEMCACHE FILE------------------";
//$mem  = new Memcached();
//$mem->addServer('127.0.0.1', 11211, 33);
$result = array();
$keys = array();
$memcached->add('key2','yes',10);
$keys = $memcached->getAllKeys();
echo "<pre>";
print_r($keys);
if (!empty($keys)) {
    foreach ($keys as $value) {

        $result = $memcached->get($value);
        echo "<pre>";
        echo "[[".$value."]]<br>";
        print_r($result);
    }
}
?>