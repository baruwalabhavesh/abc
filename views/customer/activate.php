<?php
/******** 
@USE : activate deal
@PARAMETER : 
@RETURN : 
@USED IN PAGES : process.php, process_mobile.php
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
//echo base64_encode("ali_ecom@yahoo.com")."<hr>";
$array = array();
$array['emailaddress'] = mysql_escape_string(base64_decode($_REQUEST['id']));
$RS = $objDB->Show("customer_user",$array);
//echo "1<br />";
if($RS->RecordCount()>0){
   // echo "2<br />";
	$array = $where_clause = array();
	$array['active'] = "1";
       // echo mysql_escape_string(base64_decode($_REQUEST['id']));
	$where_clause['emailaddress'] = mysql_escape_string(base64_decode($_REQUEST['id']));
	$objDB->Update($array, "customer_user", $where_clause);
	header("Location: ".WEB_PATH."/register.php?lmsg=Your account has been activated");
//	exit();
}else{
   // echo "3<br />";
	echo "Unable to Activate";
	exit();
}
if(isset($_REQUEST['c_id']))
{
 //   echo "4<br />";
    $url= WEB_PATH."/campaign.php?campaign_id=".$_REQUEST['c_id']."&l_id=".$_REQUEST['l_id'];
    $encoded_url =urlencode(WEB_PATH."/campaign.php?campaign_id=".$_REQUEST['c_id']."&l_id=".$_REQUEST['l_id']);
    header("Location: ".WEB_PATH."/register.php?lmsg=Your account has been activated&url=".$encoded_url);
    $_SESSION['active_user_emailid'] = mysql_escape_string(base64_decode($_REQUEST['id']));
	exit();
}
 else {
    // echo "5<br />";
       header("Location: ".WEB_PATH."/register.php?lmsg=Your account has been activated");
        $_SESSION['active_user_emailid'] = mysql_escape_string(base64_decode($_REQUEST['id']));
	exit();
}

?>
