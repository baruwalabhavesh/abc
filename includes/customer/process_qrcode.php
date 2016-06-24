<?php
/******** 
@USE : qrcode scan functions
@PARAMETER : 
@RETURN : 
@USED IN PAGES : call from scanning
*********/
header('Content-type: text/html; charset=utf-8');
//require_once("classes/Config.Inc.php");
//require_once(SERVER_PATH."/classes/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/func_print_coupon.php");
//include_once(SERVER_PATH."/classes/JSON.php");
require_once(LIBRARY."/PHP-PasswordLib-master/lib/PasswordLib/PasswordLib.php"); 
//$objJSON = new JSON();
//$objDB = new DB();

/******** scan qrcode **********/
if(isset($_REQUEST['scan_qrcode']))
{
$json_array= array();

	$qrcode= base64_decode($_REQUEST['qrcode']);
//echo $qrcode;
$json_array['qrcode'] = $qrcode;
 $mlatitude = $_REQUEST['current_latitude'];
    $mlongitude = $_REQUEST['current_longitude'];
	$curr_timezone = $_REQUEST['current_timezone'];
$sql_qrcode = "select id from qrcodes where qrcode='".$qrcode."' ";

$RS_qrcode = $objDB->Conn->Execute($sql_qrcode );
$q_id = $RS_qrcode->fields['id'];

//$sql = "select campaign_id from  qrcode_campaign where qrcode_id =".$q_id;
$sql = "select id 'campaign_id' from campaigns where qrcode_id  =".$q_id;

$RS = $objDB->Conn->Execute($sql);
$_qid=0;
$_campid =0;
$_locationid =0;
$_islocation =0;
if( ! isset($_REQUEST['customer_id'])){
    $custid = 0;
}
else{
    $custid = $_REQUEST['customer_id'];
}
if($RS->RecordCount()!= 0){
    $campaignid= $RS->fields['campaign_id'];
if(isset($_REQUEST['current_latitude']))
{
    
   
	if(isset($_REQUSET['customer_id']))
	{
		$update_sql = "Update customer_user set current_location ='".$_REQUEST['current_address']."' , curr_latitude='".$mlatitude."' , curr_longitude='".$mlongitude."' ,curr_timezone= '".$curr_timezone."'  where id=".$_REQUEST['customer_id'];

		$objDB->Conn->Execute($update_sql);
	}

	$Sql  = "SELECT l.id location_id ,l.location_name,l.address,l.city,l.state
 ,l.zip,l.country,l.picture,l.latitude,l.longitude,l.is_open,round((((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) AS distance 
       FROM campaign_location cl inner join locations l on l.id = cl.location_id 
where cl.offers_left>0 and  cl.campaign_id=".$campaignid." and cl.active =1
        ORDER BY distance" ;
	$json_array['redirecting_path'] = "";
    $RS_location = $objDB->Conn->Execute($Sql);
	$locationid= $RS_location->fields['location_id'];
	if($RS_location->RecordCount() == 1){
		$json_array['is_campaign'] = 1;
		$redirect_str = WEB_PATH."/campaign.php?campaign_id=".$campaignid."&l_id=".$locationid;
		$json_array['campaign_id'] = $campaignid;
		$json_array['location_id'] = $locationid;
		$json_array['status'] = "true";
		$json_array['redirecting_path'] = $redirect_str;
		$json_array['is_multiple_location'] =0;
    }
    else if($RS_location->RecordCount() > 1){
		$json_array['is_campaign'] = 1;
		$json_array['campaign_id'] = $campaignid;
		$json_array['is_multiple_location'] =1;
	/*	$sql = "select  l.id location_id ,l.location_name,l.address,l.city,l.state
 ,l.zip,l.country,l.picture,l.latitude,l.longitude,l.is_open,
  round((((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as miles_away
FROM campaign_location cl inner join locations l on l.id = cl.location_id 
where cl.offers_left>0 and  cl.campaign_id=".$campaignid." and cl.active=1"; */
	$json_array['status'] = "true";
         // $RSdata = $objDB->Conn->Execute($sql);
		  $count = 0;
		//  print_r($RSdata );
		  
			while($Row_campaign = $RS_location->FetchRow())
			{		
				
				$location_records[$count] = get_field_value($Row_campaign);				
				if($Row_campaign['location_name'] == "")
				{
					$sql = "select business from merchant_user where id=(select created_by from locations where id=".$locationid.")";
					$RS_bus= $objDB->Conn->Execute($sql);
					$busines_name = $RS_bus->fields['business'];   
					$location_records[$count]['business_name'] = $busines_name;
				}
				else
				{
					$location_records[$count]['business_name'] = $Row_campaign['location_name'];
				}
				$count++;
			}
		$json_array['location_list']=$location_records;
     //$redirect_str = WEB_PATH."/campaign.php?campaign_id=".$campaignid."&l_id=".$locationid;
    }else{
			$json_array['error_msg'] = "All offers are currently reserved at participating locations";
			$json_array['status'] = "false";
			$json_array['is_campaign'] = 1;
     		$json_array['is_qrcode_expire'] = 0;
    }
}
}
else{

   //$sql = "select location_id  from  qrcode_location where qrcode_id  =".$q_id;
   $sql = "select id 'location_id' from locations where qrcode_id  =".$q_id;
    
    $RS_loc = $objDB->Conn->Execute($sql);
    if($RS_loc->RecordCount() != 0)
    {
	
		$json_array['status'] = "true";
		$json_array['is_campaign'] = 0;
		$json_array['location_id'] = $RS_loc->fields['location_id'];
        $locationid = $RS_loc->fields['location_id'];
        $redirect_str = WEB_PATH."/location_detail.php?id=".$locationid;
		$json_array['redirecting_path'] = $redirect_str;
    }
    else{
		$json_array['status'] = "false";
		$json_array['is_qrcode_expire'] = 1;
		$json_array['error_msg'] = "QR Code has expired";
		$json_array['is_campaign'] = 0;
         //$redirect_str = WEB_PATH."/search-deal.php";
		//$json_array['redirecting_path'] = $redirect_str;
      /*   $Sql  = "select * from qrcode_group g , qrcodegroup_qrcode qq where g.id = qq.qrcodegroup_id and qq.qrcode_id  =".$q_id."  ";
       
         $RS_is_assigned = $objDB->Conn->Execute($Sql);
       //  print_r($RS_is_assigned);
         //echo $RS_is_assigned->fields['merchant_id']."==";
        // exit();
         if($RS_is_assigned->fields['merchant_id'] != 0){
        //if()
             $qrcodegenerator = $RS_is_assigned->fields['merchant_id'];
            

	//////////  if user login  then set currant latitude and longitude ///////
        if(isset($_SESSION['customer_id']))
        {
         $update_sql = "Update customer_user set current_location ='".$_REQUEST['current_address']."' , curr_latitude='".$mlatitude."' , curr_longitude='".$mlongitude."' ,curr_timezone= '".$curr_timezone."'  where id=".$_REQUEST['customer_id'];

		$objDB->Conn->Execute($update_sql);
            }
        
		$Sql  = "SELECT *, ( 3959 * acos( cos( radians($mlatitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($mlongitude) ) + 
            sin( radians($mlatitude) ) * sin( radians( 
            latitude ) ) ) ) AS distance 
        FROM locations where created_by =".$qrcodegenerator."  and active=1 
        ORDER BY distance LIMIT 1" ;
    //echo $Sql;
  //  exit;
		$RS_location = $objDB->Conn->Execute($Sql);
		if( $RS_location->RecordCount() != 0){
			$locationid= $RS_location->fields['id'];
			 $sql_l  = "select latitude,longitude , zip from  locations where id  =".$RS_location->fields['id'];
			  $RS_locdetail = $objDB->Conn->Execute($sql_l);
	   // echo $sql ;
		  $json_array['status'] = "true";
		$json_array['is_campaign'] = 2;
		$json_array['location_id'] = $locationid;
        $locationid = $locationid;
        $redirect_str = WEB_PATH."/location_detail.php?id=".$locationid;
		$json_array['redirecting_path'] = $redirect_str;
			
		}else{
		$json_array['status'] = "false";
		$json_array['is_campaign'] = 2;
		
         $redirect_str = WEB_PATH."/search-deal.php";
		$json_array['redirecting_path'] = $redirect_str;
				
			}
	}
	else{
       $json_array['status'] = "false";
		$json_array['is_campaign'] = 0;
		
         $redirect_str = WEB_PATH."/search-deal.php";
		$json_array['redirecting_path'] = $redirect_str;
    } */
 
}
}

$json = json_encode($json_array);
echo $json;

}
if(isset($_REQUEST['unique_scan']))
{
$json_array = array();
$json_array['status'] = "true";
		$locationid = $_REQUEST['location_id'];
	  $campaignid = $_REQUEST['campaign_id'];
	  $qrcode= $_REQUEST['qrcode'];
	  $mobile_id = $_REQUEST['mobile_id'];
		$Sql  = "SELECT * FROM locations  where id=".$_REQUEST['location_id'];
		
			
		$RS_location = $objDB->Conn->Execute($Sql);
		$timezone = $RS_location->fields['timezone'];
		$dt_sql  = "SElect CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR('". $timezone."',1, POSITION(',' IN '". $timezone."')-1)) dte " ;
	   // echo $dt_sql;
	   $sql_qrcode = "select id from qrcodes where qrcode='".$qrcode."' ";
		$RS_qrcode = $objDB->Conn->Execute($sql_qrcode );
		$q_id = $RS_qrcode->fields['id'];
		$RS_dt = $objDB->Conn->Execute($dt_sql);
		$sql = "select * from scan_qrcode where location_id=".$_REQUEST['location_id']." and campaign_id=".$_REQUEST['campaign_id']." and qrcode_id=".$q_id." and  mobile_uinque_id='".$_REQUEST['mobile_id']."' ";
		echo $sql;
		$RS_scans = $objDB->Conn->Execute($Sql);
		
		if($RS_scans->RecordCount() == 0)
		{
			if(isset($_REQUEST['customer_id']))
			{
				$sql= "update scan_qrcode set user_id=".$_REQUEST['customer_id']." where mobile_uinque_id='".$_REQUEST['mobile_id']."' ";
				$objDB->Conn->Execute($sql);
			}
			else
			{
			}
			$uid = 1;
			
		}
		else
		{
		$uid = 0;
		if(isset($_REQUEST['customer_id']))
			{
				$sql= "update scan_qrcode set user_id=".$_REQUEST['customer_id']." where mobile_uinque_id='".$_REQUEST['mobile_id']."' ";
				$objDB->Conn->Execute($sql);
			}
		}
		if(isset($_REQUEST['customer_id']))
		{
		}
		else
		{
			$custid = $_REQUEST['customer_id'];
		}
		$insert_array['qrcode_id']= $q_id;
				$insert_array['campaign_id']= $campaignid;
				$insert_array['location_id']= $locationid;
				$insert_array['is_location']= $_REQUEST['is_location'];
				$insert_array['is_superadmin']= 0;
				$insert_array['is_unique']= $uid;
				$insert_array['scaned_date']= $RS_dt->fields['dte'];
				$insert_array['user_id']=$custid ;
				$insert_array['mobile_uinque_id']=$_REQUEST['mobile_id'];
				$objDB->Insert($insert_array, "scan_qrcode");
				echo json_encode($json_array);
}
function get_field_value($Row)
{
	$ar = $Row;
	
	$ar1 = array_unique($ar);
	for ($i = 0; $i < (count($ar)) ; $i++) {
		if(key_exists($i,$ar))
		{
			unset($ar[$i]);
		}
	    }
	
	return $ar;	
}
?>
