<?php
/**
 * @uses logout when merchant setup
 * @used in pages : add-compaign.php,add-location.php,add-user.php,copy-compaign.php,edit-compaign.php,edit-distributionlist.php,edit-location.php,edit-user.php,header.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDBWrt = new DB('write');
if(isset($_REQUEST['last_tab']))
{
	$array['last_tab'] = $_REQUEST['last_tab'];
    $where_clause['id']=$_SESSION['merchant_id'];
    $objDBWrt->Update($array, "merchant_user", $where_clause);
}
$_SESSION = array();
header("Location: ".WEB_PATH."/merchant/index.php");
exit();
?>
