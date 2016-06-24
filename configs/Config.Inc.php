<?php
/**
 * @uses Database and server constant define here
 * @param 
 * @used in pages : every page
 * 
 */
error_reporting(0);
//error_reporting(E_ALL);
//error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('error_log',ROOT.'/logs/php_error_log.log');

ob_start();
if(session_id() == '') {
session_start();
session_cache_expire(5);
$cache_expire = session_cache_expire();
}
session_cache_expire(5);
$cache_expire = session_cache_expire();
$dir = dirname(__FILE__);

#DATABASE CONSTANT
if (!defined('DATABASE_TYPE')) define('DATABASE_TYPE','mysqlt');
if (!defined('DATABASE_HOST')) define('DATABASE_HOST','localhost');
if (!defined('DATABASE_USER')) define('DATABASE_USER','root');
if (!defined('DATABASE_PASSWORD')) define('DATABASE_PASSWORD','root');
if (!defined('DATABASE_NAME')) define('DATABASE_NAME','scanflip');

// 11 07 2014

$con = mysql_connect(DATABASE_HOST,DATABASE_USER,DATABASE_PASSWORD);
if(!$con)
{
	echo 'Could not connect: ' . mysql_error();
}

$db_selected = mysql_select_db(DATABASE_NAME, $con);
if(!$db_selected)
{
	echo "Can\'t use ".DATABASE_NAME." : " . mysql_error();
}

$result = mysql_query("SELECT value FROM admin_settings where id=14");
$server_path_arr = mysql_fetch_array($result);
$server_path = $server_path_arr['value'];

$result = mysql_query("SELECT value FROM admin_settings where id=15");
$web_path_arr = mysql_fetch_array($result);
$web_path = $web_path_arr['value'];

$result = mysql_query("SELECT value FROM admin_settings where id=16");
$timezone_api_arr = mysql_fetch_array($result);
$timezone_api_key = $timezone_api_arr['value'];

mysql_close($con);

// 11 07 2014

#GENERAL PATH CONSTANTS
if (!defined('SERVER_PATH'))
{
	//define('SERVER_PATH',"/var/www/vhosts/scanflip.com/httpdocs/test_scanflip");
	define('SERVER_PATH',"/var/www/html/bhaveshscanflip");
	// 11 07 2014
	//define('SERVER_PATH',$server_path);
	// 11 07 2014
}
if (!defined('WEB_PATH')) 
{
	//define('WEB_PATH',"https://test.scanflip.com");
	define('WEB_PATH',"http://scanfliplocal");
	// 11 07 2014
	//define('WEB_PATH',$web_path);
	// 11 07 2014
}

#SET TIMEZONE DB CONSTANT
/*
http://timezonedb.com/activate?user=scanflip&code=3f00e19f21

Username : scanflip
Password : latikajn
API Key  : V5R3I13VWW28

For more information of how to use our API, please refer to:
http://timezonedb.com/api

*/
if (!defined('timezonedb_username')) define('timezonedb_username',"scanflip");
if (!defined('timezonedb_password')) define('timezonedb_password',"latikajn");
if (!defined('timezonedb_apikey'))
{
	
	define('timezonedb_apikey',$timezone_api_key);
	
}
//sangeeta
if (!defined('ASSETS')) {
        define('ASSETS', WEB_PATH . '/assets');
}
//image upload path
if (!defined('UPLOAD_IMG')) {
        define('UPLOAD_IMG', './assets/images');
}

if (!defined('ASSETS_JS')) {
        define('ASSETS_JS', ASSETS . '/js');
}
if (!defined('ASSETS_CSS')) {
        define('ASSETS_CSS', ASSETS . '/css');
}
if (!defined('ASSETS_IMG')) {
        define('ASSETS_IMG', ASSETS . '/images');
}
if (!defined('CUST_PRCSS')) { // customer process
        define('CUST_PRCSS', WEB_PATH . '/includes/customer');
}
if (!defined('MRCNT_PRCSS')) { // customer process
        define('MRCNT_PRCSS', WEB_PATH . '/includes/merchant');
}
if (!defined('LIBRARY')) { // customer process
        define('LIBRARY', ROOT . '/libraries');
}
if (!defined('INCLUDES')) {
        define('INCLUDES', ROOT . '/includes');
}

if (!defined('CUST_VIEW')) { // customer views
        define('CUST_VIEW', ROOT . '/views/customer');
}
if (!defined('CUST_LAYOUT')) { // customer layout
        define('CUST_LAYOUT', ROOT . '/views/layouts/customer');
}
if (!defined('MRCH_VIEW')) { // merchant views
        define('MRCH_VIEW', ROOT . '/views/merchant');
}
if (!defined('MRCH_LAYOUT')) { // merchant layout
        define('MRCH_LAYOUT', ROOT . '/views/layouts/merchant');
}
if (!defined('SERVICES')) { // merchant layout
        define('SERVICES', ROOT . '/services');
}
if (!defined('ADMIN_LAYOUT')) { // admin layout
        define('ADMIN_LAYOUT', ROOT . '/views/layouts/admin');
}
if (!defined('ADMIN_VIEW')) { // admin views
        define('ADMIN_VIEW', ROOT . '/views/admin');
}
if (!defined('REWARD_ZONE_APPL_TRANS_FEE')) { // admin views
        define('REWARD_ZONE_APPL_TRANS_FEE', 20/100);
}

include_once(LIBRARY."/languages/english.php");
include_once(LIBRARY."/languages/merchant_labels.php");
include_once(LIBRARY."/languages/client_labels.php");

function timezone_offset_string( $offset )
{
        return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
}
$timezone =date_default_timezone_get();
$offset = timezone_offset_get(new DateTimeZone( $timezone ), new DateTime() );
$offset=timezone_offset_string( $offset );

if (!defined('CURR_TIMEZONE')) 
define('CURR_TIMEZONE',$offset);


function check_merchant_session(){
	if( ! isset($_SESSION['merchant_id']) || $_SESSION['merchant_id']==""){
		header("Location: ".WEB_PATH."/merchant/register.php");
		exit();
	}
}
function check_customer_session(){
	if(! isset($_SESSION['customer_id'] )){
		header("Location: ".WEB_PATH."/register.php?url=".curPageURL());
		exit();
	}
}
function check_admin_session(){
	if(! isset($_SESSION['admin_id'])){
		header("Location: ".WEB_PATH."/admin/login.php");
		exit();
	}

}
function curPageURL(){
	/*
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return urlencode($pageURL);
*/
	$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')=== FALSE ? 'http' : 'https';
    $host     = $_SERVER['HTTP_HOST'];
    $script   = $_SERVER['SCRIPT_NAME'];
    $params   = $_SERVER['QUERY_STRING'];
    $currentUrl = $protocol . '://' . $host . $script . '?' . $params;
 	return urlencode($currentUrl);
}

function check_customer_session_redirecturl($url){
	
	if(! isset($_SESSION['customer_id'] )){
	 header("Location: ".WEB_PATH."/register.php?url=".$url);
	 exit();
	}
}

?>
