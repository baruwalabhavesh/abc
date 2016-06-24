<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
session_start();
$mc = new Memcached();
$mc->addServer("127.0.0.1", 11212);

$mc->setOption(Memcached::OPT_PREFIX_KEY, 'md_');

$keys = $mc->getAllKeys();
//print_r($keys);
$m_array = preg_grep('/^md_/', $keys);
//print_r($m_array);

$rp= $mc->getOption(Memcached::OPT_PREFIX_KEY);
$exp = '/^'.$rp.'0/';
$unprefixed_keys = preg_filter($exp, '$1', $m_array);

//$rr = preg_match ('/^md_/i', $m_array, $matches);
//echo "<pre>";print_r($unprefixed_keys);

$result = $mc->getMulti($unprefixed_keys, $cas);
//echo "<pre>";print_r($result);

//$mc->deleteMulti($unprefixed_keys);

/*foreach ($keys as $index => $key) {
        if (strpos($key, 'md_') !== 0) {
                echo "dd";
                unset($keys[$index]);
        } else {
                echo $key;
                $mc->delete($key);
        }
}*/

$rr = $mc->getAllKeys();
print_r($rr);

$v = $mc->get('skey');

$mc->set('_by_by', array('5','6'), time() + 300);

if ($v) {

        if (!array_key_exists('key4', $v)) {
                $items = array(
                        //'key2' => 'value2',
                        'key4' => 'value3'
                );

                $items1 = array_merge($items, $v);
                $mc->replace('skey', $items1, time() + 300);
        }
} else {
        
        $items = array(
                'key1' => 'value1',
        );
        $mc->set('skey', $items, time() + 300) or die("Failed to save data at Memcached server");
}
//exit;

/* $tt = $mc->get("tag_10");
  if ($tt) {
  print_r($tt);
  } else {
  $tags = $mc->set("tag_10", array("key1", "key1", "key1", "key2", "key3", "key1", "key4"));
  $tags = $mc->set("tag_2", array("key1", "key1", "key1", "key2", "key3", "key1", "key4"));
  } */


//exit;

$result = $mc->get("test_key");

if ($result) {
        echo $result;
} else {
        echo "No data on Cache. Please refresh page pressing F5";
        $mc->set("test_key", "test data pulled from Cache!") or die("Failed to save data at Memcached server");
}


if (class_exists('Memcached')) {
        // Memcache is enabled.
        echo "memcached enabled.";
} else {
        echo "not installed.";
}


if (!isset($_SESSION['visit'])) {
        echo "This is the first time you're visiting this server\n";
        $_SESSION['visit'] = 0;
} else
        echo "Your number of visits: " . $_SESSION['visit'] . "\n";

$_SESSION['visit'] ++;

//echo "Server IP: " . $_SERVER['SERVER_ADDR'] . "\n";
//echo "Client IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
//echo "<pre>";print_r($_COOKIE);

/*$meminstance = new Memcache();
$meminstance->pconnect('localhost', 11211);

mysql_connect("localhost", "test_scanflip", "Bi0a4c#9") or die(mysql_error());
mysql_select_db("DBM23985412") or die(mysql_error());

$querykey = "KEY" . md5('sangeeta');

$result = $meminstance->get($querykey);

if (!$result) {
        $result = mysql_fetch_array(mysql_query("select * from sections")) or die('mysql error');
        $meminstance->set($querykey, $result, 0, 10);
        print "got result from mysql\n";
        return 0;
}

print "got result from memcached\n";
return 0;*/

