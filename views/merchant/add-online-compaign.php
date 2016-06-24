<?php

/**
 * @uses add online campaign
 * @used in pages : compaigns.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where_act['active']=1;
$RSCat = $objDB->Show("categories",$array_where_act);
$array =array();

  $arr_c=file(WEB_PATH.'/merchant/process.php?num_campaigns_per_month=yes&mer_id='.$_SESSION['merchant_id']);
                
//	if(trim($arr_c[0]) == "")
//	{
//		unset($arr_c[0]);
//		$arr = array_values($arr_c);
//	}

	$json = json_decode($arr_c[0]);

            $total_campaigns= $json->total_records;
	$addcampaign_status = $json->status;
   
                   if($addcampaign_status=="false") 
                   {
                       header("Location:".WEB_PATH."/merchant/compaigns.php?action=active");
                   }
                   
                   
$array_where_user['id'] = $_SESSION['merchant_id'];
$RS_User = $objDB->Show("merchant_user", $array_where_user);
$m_parent = $RS_User->fields['merchant_parent'];
if($RS_User->fields['merchant_parent'] == 0)
{
	$array['created_by'] = $_SESSION['merchant_id'];
        $array['active'] = 1;
	$RSStore = $objDB->Show("locations", $array);
}
else
{
	$media_acc_array = array(); 
	$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id']; 
	$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array); 
	$location_val = $RSmedia->fields['location_access'];
	
	
	//$sql = "SELECT * FROM locations  WHERE id in (select l.id from locations l where l.created_by=".$m_parent ." and l.id = ".$location_val .")";
	//$RSStore = $objDB->execute_query($sql);
	$arr=file(WEB_PATH.'/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id='.$m_parent.'&loc_id='.$location_val);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
        
	$total_records= $json->total_records;
	$records_array = $json->records;
}


$array = array();
$array['merchant_id'] = $_SESSION['merchant_id'];
$RSGroups = $objDB->Show("merchant_groups",$array);



$pack_data1 = $json_array = array();
$pack_data = $json_array = array();
if($RS_User->fields['merchant_parent'] == 0)
{
    $pack_data['merchant_id'] = $_SESSION['merchant_id'];
    $get_pack_data = $objDB->Show("merchant_billing",$pack_data);
    $pack_data1['id'] = $get_pack_data->fields['pack_id'];
    $get_billing_pack_data = $objDB->Show("billing_packages",$pack_data1);
}
 else 
 {
     $arr=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
        
	$main_merchant_id= $json->main_merchant_id;
	
        
    $pack_data['merchant_id'] = $main_merchant_id;
    $get_pack_data = $objDB->Show("merchant_billing",$pack_data);
    $pack_data1['id'] = $get_pack_data->fields['pack_id'];
    $get_billing_pack_data = $objDB->Show("billing_packages",$pack_data1);    
 }


/* T_10 */
if(isset($_REQUEST['template_id'])){
	$array_where['id'] = $_REQUEST['template_id'];
$RS_template = $objDB->Show("campaigns_template", $array_where);
}
/* T_10 */
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Manage Campaigns</title>
<body>
add online campaign
</body>
</html>
