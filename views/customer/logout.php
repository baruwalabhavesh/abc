<?
/******** 
@USE : to logout
@PARAMETER : 
@RETURN : 
@USED IN PAGES : header.php
*********/
//require_once("classes/Config.Inc.php");
$_SESSION = array();
setcookie("read_notification", "no", time()+3600);
setcookie("subs_merchant_read_notification", "no", time()+3600);
setcookie("earned_recent_visit_notification", "no", time()+3600);
setcookie("earned_new_customer_notification", "no", time()+3600);
setcookie("page", "", time()-3600);
header("Location: ".WEB_PATH."/index.php");
exit();
?>
