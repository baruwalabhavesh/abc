<?php
/**
 * @uses filter report by year
 * @used in pages :reports.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
require(LIBRARY.'/gapi.class.php');
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array = array();
$months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	$from_date = "2012-01-01 00:00:00";
	$to_date = "2012-01-31 23:59:59";
$str_campaigns = array();
if(isset($_REQUEST['generated_campaigns']))
{
    
    foreach($months as $month){
	$from_date = $_REQUEST['year']."-".$month."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$month."-31 23:59:59";
	$from_date = urlencode($from_date);
	$to_date = urlencode($to_date);
        $arr=file(WEB_PATH.'/merchant/process.php?campaign_list_of_month=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
        if($total_records>0){
		foreach($records_array as $R)
		{
                    array_push($str_campaigns,intval($R->total));
			
		}
	}
        
    }
 
    $str_campaigns = json_encode($str_campaigns);
    echo $str_campaigns;
    exit;
}
else if(isset($_REQUEST['reserved_campaigns']))
{
    
    foreach($months as $month){
	$from_date = $_REQUEST['year']."-".$month."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$month."-31 23:59:59";
	$from_date = urlencode($from_date);
	$to_date = urlencode($to_date);
        $arr=file(WEB_PATH.'/merchant/process.php?reserve_campaign_list_of_month=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
        if($total_records>0){
		foreach($records_array as $R)
		{
                    array_push($str_campaigns,intval($R->total));
			
		}
	}
        
    }
 
    $str_campaigns = json_encode($str_campaigns);
    echo $str_campaigns;
    exit;
}
else if(isset($_REQUEST['referral_user']))
{
    
    foreach($months as $month){
	$from_date = $_REQUEST['year']."-".$month."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$month."-31 23:59:59";
	$from_date = urlencode($from_date);
	$to_date = urlencode($to_date);
        $arr=file(WEB_PATH.'/merchant/process.php?referral_user_list_of_month=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
        if($total_records>0){
		foreach($records_array as $R)
		{
                    array_push($str_campaigns,intval($R->total));
			
		}
	}
        
    }
 
    $str_campaigns = json_encode($str_campaigns);
    echo $str_campaigns;
    exit;
}

else if(isset($_REQUEST['redeemed_user']))
{
    
    foreach($months as $month){
	$from_date = $_REQUEST['year']."-".$month."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$month."-31 23:59:59";
	$from_date = urlencode($from_date);
	$to_date = urlencode($to_date);
        $arr=file(WEB_PATH.'/merchant/process.php?redeemed_user_list_of_month=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
        if($total_records>0){
		foreach($records_array as $R)
		{
                    array_push($str_campaigns,intval($R->total));
			
		}
	}
        
    }
 
    $str_campaigns = json_encode($str_campaigns);
    echo $str_campaigns;
    exit;
}

else if(isset($_REQUEST['active_campaigns']))
{
     foreach($months as $month){
	$from_date = $_REQUEST['year']."-".$month."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$month."-31 23:59:59";
	$from_date = urlencode($from_date);
	$to_date = urlencode($to_date);
  	$arr=file(WEB_PATH.'/merchant/process.php?active_campaign_list_of_month=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
        if($total_records>0){
		foreach($records_array as $R)
		{
                    array_push($str_campaigns,intval($R->total));
			
		}
	}
        
    }
 
    $str_campaigns = json_encode($str_campaigns);
    echo $str_campaigns;
    exit;
}
else if(isset($_REQUEST['expire_campaigns']))
{
     foreach($months as $month){
	$from_date = $_REQUEST['year']."-".$month."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$month."-31 23:59:59";
	$from_date = urlencode($from_date);
	$to_date = urlencode($to_date);
  	$arr=file(WEB_PATH.'/merchant/process.php?expired_campaign_list_of_month=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
        if($total_records>0){
		foreach($records_array as $R)
		{
                    array_push($str_campaigns,intval($R->total));
			
		}
	}
        
    }
 
    $str_campaigns = json_encode($str_campaigns);
    echo $str_campaigns;
    exit;
}
else if(isset($_REQUEST['generated_coupons']))
{
     foreach($months as $month){
	$from_date = $_REQUEST['year']."-".$month."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$month."-31 23:59:59";
	$from_date = urlencode($from_date);
	$to_date = urlencode($to_date);
  	$arr=file(WEB_PATH.'/merchant/process.php?generated_coupon_monthly=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
        if($total_records>0){
		foreach($records_array as $R)
		{
                    array_push($str_campaigns,intval($R->total));
			
		}
	}
        
    }
 
    $str_campaigns = json_encode($str_campaigns);
    echo $str_campaigns;
    exit;
}
else if(isset($_REQUEST['reddemed_coupons']))
{
     $merchant_id = $_SESSION["merchant_id"];
	$months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	$redeem_array = array();
	$final_array = array();
	$data_array = array();
	
	foreach($months as $month){
	$from_date = $_REQUEST['year']."-".$month."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$month."-31 23:59:59";
		$location_str .= "'".$Row1['location_name']."',";
	   /*$sql = "select r.campaign_id  cid ,count(*) as total , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where
		r.reward_date>='$from_date' and r.reward_date<='$to_date' 
		and r.campaign_id in ( select id from campaigns where created_by = $merchant_id or created_by in ( select id from merchant_user where merchant_parent =$merchant_id ))
		group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')";	
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select r.campaign_id  cid ,count(*) as total , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where
		r.reward_date>=? and r.reward_date<=? 
		and r.campaign_id in ( select id from campaigns where created_by = ? or created_by in ( select id from merchant_user where merchant_parent =? ))
		group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')",array($from_date,$to_date,$merchant_id,$merchant_id));

		while($Row = $RS->FetchRow())
		{
					
			$main_array = array();
			$main_array['name'] = $Row['title'];
			
			$main_array['stack'] = "redeemed";
			
			$t_val = $Row['total'];
			$temp_array = array();
			if($t_val == "")
			{
				$t_val = 0;	
			}
			$str = "";
			if($count == 0)
			{
				array_push($temp_array,intval($Row['total']));
				$data_array[$Row['cid']] =$temp_array;
					
			}else if(!key_exists($Row['cid'],$data_array))
			{
				
				for($v=0;$v<$count;$v++)
				{
					array_push($temp_array,0);
				}
				array_push($temp_array,intval($Row['total']));
				$data_array[$Row['cid']] = $temp_array;
		        }
			else{
				//echo  $data_array[$Row['cid']];
				$temp_array = $data_array[$Row['cid']];
				$temp_array = $temp_array;
				array_push($temp_array ,intval($Row['total']));
				$data_array[$Row['cid']] =$temp_array;
			}
			$main_array['data'] = $data_array;
			$redeem_array[$Row['cid']] = $main_array ;
			
		}
		$count++;
		
	}
	
	foreach($data_array as $key=>$value)
	{
		$a =$data_array[$key];
		$arr = array();
		$arr  = $a;
		for($j=count($a);$j<12;$j++){
			array_push($a,0);
		}
		$redeem_array[$key]['data'] = $a;
		array_push($final_array,$redeem_array[$key]);
	}
	
	if(count($data_array) == 0)
	{
	    $a =array();
		$arr = array();
		$arr  = $a;
		for($j=0;$j<12;$j++){
			array_push($a,0);
		}
		$redeem_array[0]['name'] =""; 
		$redeem_array[0]['data'] = $a;
		array_push($final_array,$redeem_array[0]);
	}
	$json_array=array();
	$json_array['records']=$final_array;
	$json_array['status']='true';
	$json = json_encode($final_array);
	echo $json;

    exit;
}
else if($_REQUEST['used_points'])
{
    $merchant_id =  $_SESSION["merchant_id"];
	$months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	$redeem_array = array();
	$final_array = array();
	$data_array = array();
	
	foreach($months as $month){
	$from_date =  $_REQUEST['year']."-".$month."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$month."-31 23:59:59";
		$location_str .= "'".$Row1['location_name']."',";
	  /* $sql = "select r.campaign_id  cid ,sum(earned_reward) total_reward_point , sum(referral_reward) total_referral_point , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where
		r.reward_date>='$from_date' and r.reward_date<='$to_date' 
		and r.campaign_id in ( select id from campaigns where created_by = $merchant_id or created_by in ( select id from merchant_user where merchant_parent =$merchant_id ))
		group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')";

	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select r.campaign_id  cid ,sum(earned_reward) total_reward_point , sum(referral_reward) total_referral_point , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where
		r.reward_date>=? and r.reward_date<=? 
		and r.campaign_id in ( select id from campaigns where created_by = ? or created_by in ( select id from merchant_user where merchant_parent =? ))
		group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')",array($from_date,$to_date,$merchant_id,$merchant_id));


		while($Row = $RS->FetchRow())
		{
					
			$main_array = array();
			$main_array['name'] = $Row['title'];
			
			$main_array['stack'] = "redeemed";
			
			$t_val = $Row['total_reward_point'] + $Row['total_referral_point'];
			$temp_array = array();
			if($t_val == "")
			{
				$t_val = 0;	
			}
			$str = "";
			if($count == 0)
			{
				array_push($temp_array,intval($Row['total_reward_point'] + $Row['total_referral_point']));
				$data_array[$Row['cid']] =$temp_array;
					
			}else if(!key_exists($Row['cid'],$data_array))
			{
				
				for($v=0;$v<$count;$v++)
				{
					array_push($temp_array,0);
				}
				array_push($temp_array,intval($Row['total_reward_point'] + $Row['total_referral_point']));
				$data_array[$Row['cid']] = $temp_array;
		        }
			else{
				//echo  $data_array[$Row['cid']];
				$temp_array = $data_array[$Row['cid']];
				$temp_array = $temp_array;
				array_push($temp_array ,intval($Row['total_reward_point'] + $Row['total_referral_point']));
				$data_array[$Row['cid']] =$temp_array;
			}
			$main_array['data'] = $data_array;
			$redeem_array[$Row['cid']] = $main_array ;
			
		}
		$count++;
		
	}
	foreach($data_array as $key=>$value)
	{
		$a =$data_array[$key];
		$arr = array();
		$arr  = $a;
		for($j=count($a);$j<12;$j++){
			array_push($a,0);
		}
		$redeem_array[$key]['data'] = $a;
		array_push($final_array,$redeem_array[$key]);
	}
	if(count($data_array) == 0)
	{
	    $a =array();
		$arr = array();
		$arr  = $a;
		for($j=0;$j<12;$j++){
			array_push($a,0);
		}
		$redeem_array[0]['name'] =""; 
		$redeem_array[0]['data'] = $a;
		array_push($final_array,$redeem_array[0]);
	}
	$json_array=array();
	$json_array['records']=$final_array;
	$json_array['status']='true';
	$json = json_encode($final_array);
	echo $json;
}
else if($_REQUEST['locationwise_campaign'])
{
  $from_date = $_REQUEST['year']."-01-01 00:00:00";
	$to_date = $_REQUEST['year']."-12-31 23:59:59";
	$merchant_id = $_SESSION["merchant_id"];
	/*$Sql = "SELECT * FROM locations WHERE created_by=$merchant_id ORDER BY id DESC";
	$RS1 = $objDB->Conn->Execute($Sql);*/
	$RS1 = $objDB->Conn->Execute("SELECT * FROM locations WHERE created_by=? ORDER BY id DESC",array($merchant_id));

	$redeem_array = array();
	$final_array = array();
	$data_array = array();
	$count=0;
	//echo "<pre>";
	//			
	//echo "</pre>";
	$location_str =array();
	while($Row1 = $RS1->FetchRow())
	{
		//$location_str .=  $Row1['location_name'];
		array_push($location_str ,$Row1['location_name']);
	   /*$sql = "select c.created_by as cid ,count(*) as total, (select firstname from merchant_user where id=c.created_by) as user_name
		from campaigns c 
		where c.created_date>='$from_date' AND c.created_date<='$to_date' and
		c.id in ( select cl.campaign_id from campaign_location cl where cl.location_id = ".$Row1['id'].") and (c.created_by = $merchant_id or
		c.created_by in (select id from merchant_user where merchant_parent = $merchant_id))
		group by c.created_by";	
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select c.created_by as cid ,count(*) as total, (select firstname from merchant_user where id=c.created_by) as user_name
		from campaigns c 
		where c.created_date>=? AND c.created_date<=? and
		c.id in ( select cl.campaign_id from campaign_location cl where cl.location_id = ?) and (c.created_by = ? or
		c.created_by in (select id from merchant_user where merchant_parent = ?))
		group by c.created_by",array($from_date,$to_date,$Row1['id'],$merchant_id,$merchant_id));
		
		while($Row = $RS->FetchRow())
		{
			$main_array = array();
			$main_array['name'] = $Row['user_name'];
			
			$main_array['stack'] = "generated";
			
			$t_val = $Row['total'];
			$temp_array = array();
			if($t_val == "")
			{
				$t_val = 0;	
			}
			$str = "";
			if($count == 0)
			{
				array_push($temp_array,intval($Row['total']));
				$data_array[$Row['cid']] =$temp_array;
					
			}else if(!key_exists($Row['cid'],$data_array))
			{
				
				for($v=0;$v<$count;$v++)
				{
					array_push($temp_array,0);
				}
				array_push($temp_array,intval($Row['total']));
				$data_array[$Row['cid']] = $temp_array;
		        }
			else{
				//echo  $data_array[$Row['cid']];
				$temp_array = $data_array[$Row['cid']];
				$temp_array = $temp_array;
				array_push($temp_array ,intval($Row['total']));
				$data_array[$Row['cid']] =$temp_array;
			}
			$main_array['data'] = $data_array;
			$redeem_array[$Row['cid']] = $main_array ;
			
		}
		$count++;
		
	}
	//$location_str = trim($location_str,",");
	foreach($data_array as $key=>$value)
	{
		$a =$data_array[$key];
		$arr = array();
		$arr  = $a;
		for($j=count($a);$j<$RS1->RecordCount();$j++){
			array_push($a,0);
		}
		$redeem_array[$key]['data'] = $a;
		array_push($final_array,$redeem_array[$key]);
	}
	if(count($data_array) == 0)
	{
	    $a =array();
		$arr = array();
		$arr  = $a;
		for($j=0;$j<count($location_str);$j++){
			array_push($a,0);
		}
		$redeem_array[0]['name'] =""; 
		$redeem_array[0]['data'] = $a;
		array_push($final_array,$redeem_array[0]);
	}
	
	$json_array=array();
	$json_array['records']=$final_array;
	$json_array['locations'] =  $location_str;
	$json = json_encode($json_array);
	echo $json;
	exit();
}
else if($_REQUEST['total_used_points'])
{
    $from_date = $_REQUEST['year']."-".$_REQUEST['month']."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$_REQUEST['month']."-31 23:59:59";
	$merchant_id = $_SESSION["merchant_id"];
	/*$Sql = "SELECT * FROM campaigns WHERE created_by=$merchant_id or created_by in ( select id from merchant_user where merchant_parent = $merchant_id  )ORDER BY id DESC";
	$RS1 = $objDB->Conn->Execute($Sql);*/
	$RS1 = $objDB->Conn->Execute("SELECT * FROM campaigns WHERE created_by=? or created_by in ( select id from merchant_user where merchant_parent = ?  )ORDER BY id DESC",array($merchant_id,$merchant_id));

	$redeem_array = array();
	$final_array = array();
	$data_array = array();
	$count=0;
	//echo "<pre>";
	//			
	//echo "</pre>";
	$location_str =array();
	$value_str  = array();
	while($Row1 = $RS1->FetchRow())
	{
		
	   /*$sql = "select r.campaign_id  cid ,sum(earned_reward) total_reward_point , sum(referral_reward) total_referral_point , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where  r.reward_date >='".$from_date."' AND r.reward_date <='".$to_date."' and
		r.campaign_id = ".$Row1['id']." group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')";	
		$RS = $objDB->Conn->Execute($sql);*/
		$RS = $objDB->Conn->Execute("select r.campaign_id  cid ,sum(earned_reward) total_reward_point , sum(referral_reward) total_referral_point , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where  r.reward_date >=? AND r.reward_date <=? and
		r.campaign_id =? group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')",array($from_date,$to_date,$Row1['id']));

		while($Row = $RS->FetchRow())
		{
                    $v =$Row['total_reward_point'] + $Row['total_referral_point'];
                    if($v>0){
		    array_push($location_str,$Row1['title']);
		    array_push($value_str,intval($Row['total_reward_point'] + $Row['total_referral_point']));
			//$location_str .= "'".$Row1['title']."',";
			//	$value_str .=  	$Row['total_reward_point'] + $Row['total_referral_point'] . ",";
                    }
		}

	}
	
	$json_array=array();
	$json_array['campaign_total_values']=$value_str;
	$json_array['campaigns'] =  $location_str;
	$json = json_encode($json_array);
	echo $json;
	exit;
    
}
else if($_REQUEST['redeemed_used_points'])
{
 $from_date = $_REQUEST['year']."-".$_REQUEST['month']."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$_REQUEST['month']."-31 23:59:59";
	$merchant_id = $_SESSION["merchant_id"];
	/*$Sql = "SELECT * FROM campaigns WHERE created_by=$merchant_id or created_by in ( select id from merchant_user where merchant_parent = $merchant_id  )ORDER BY id DESC";
	$RS1 = $objDB->Conn->Execute($Sql);*/
	$RS1 = $objDB->Conn->Execute("SELECT * FROM campaigns WHERE created_by=? or created_by in ( select id from merchant_user where merchant_parent = ?  )ORDER BY id DESC",array($merchant_id,$merchant_id));

	$redeem_array = array();
	$final_array = array();
	$data_array = array();
	$count=0;
	//echo "<pre>";
	//			
	//echo "</pre>";
	$location_str =array();
	$value_str  = array();
	while($Row1 = $RS1->FetchRow())
	{
		
	   /*$sql = "select r.campaign_id  cid ,sum(earned_reward) total_reward_point , sum(referral_reward) total_referral_point , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where  r.reward_date >='".$from_date."' AND r.reward_date <='".$to_date."' and
		r.campaign_id = ".$Row1['id']." group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')";	
		$RS = $objDB->Conn->Execute($sql);*/
		$RS = $objDB->Conn->Execute("select r.campaign_id  cid ,sum(earned_reward) total_reward_point , sum(referral_reward) total_referral_point , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where  r.reward_date >=? AND r.reward_date <=? and
		r.campaign_id = ? group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')",array($from_date,$to_date,$Row1['id']));

		while($Row = $RS->FetchRow())
		{
                    if($Row['total_reward_point']>0){
		     array_push($location_str,$Row1['title']);
		    array_push($value_str,intval($Row['total_reward_point'] ));
                    }
		}

	}
	
	$json_array=array();
	$json_array['campaign_total_values']=$value_str;
	$json_array['campaigns'] =  $location_str;
	$json = json_encode($json_array);
	echo $json;
	exit();
}
else if($_REQUEST['shared_used_points'])
{
 $from_date = $_REQUEST['year']."-".$_REQUEST['month']."-01 00:00:00";
	$to_date = $_REQUEST['year']."-".$_REQUEST['month']."-31 23:59:59";
        $merchant_id = $_SESSION["merchant_id"];
	/*$Sql = "SELECT * FROM campaigns WHERE created_by=$merchant_id or created_by in ( select id from merchant_user where merchant_parent = $merchant_id  )ORDER BY id DESC";
	$RS1 = $objDB->Conn->Execute($Sql);*/
	$RS1 = $objDB->Conn->Execute("SELECT * FROM campaigns WHERE created_by=? or created_by in ( select id from merchant_user where merchant_parent = ?  )ORDER BY id DESC",array($merchant_id,$merchant_id));

	$redeem_array = array();
	$final_array = array();
	$data_array = array();
	$count=0;
	//echo "<pre>";
	//			
	//echo "</pre>";
	$location_str =array();
	$value_str  = array();
	while($Row1 = $RS1->FetchRow())
	{
		
	   /*$sql = "select r.campaign_id  cid ,sum(earned_reward) total_reward_point , sum(referral_reward) total_referral_point , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where  r.reward_date >='".$from_date."' AND r.reward_date <='".$to_date."' and
		r.campaign_id = ".$Row1['id']." group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')";	
		$RS = $objDB->Conn->Execute($sql);*/
		$RS = $objDB->Conn->Execute("select r.campaign_id  cid ,sum(earned_reward) total_reward_point , sum(referral_reward) total_referral_point , (select title from campaigns where id = r.campaign_id) as title
		from reward_user r
		where  r.reward_date >=? AND r.reward_date <=? and
		r.campaign_id = ? group by r.campaign_id , DATE_FORMAT(r.reward_date,'%Y %c %d')",array($from_date,$to_date,$Row1['id']));

		while($Row = $RS->FetchRow())
		{
                    if($Row['total_referral_point']>0){
		     array_push($location_str,$Row1['title']);
		    array_push($value_str,intval($Row['total_referral_point'] ));
                    }
			
		}

	}
	
	$json_array=array();
	$json_array['campaign_total_values']=$value_str;
	$json_array['campaigns'] =  $location_str;
	$json = json_encode($json_array);
	echo $json;
	exit();
}
if(isset($_REQUEST['loadGAReport']))
{
 ///-- For google analytic reports ///

    define('ga_account','punitapatel26@gmail.com');
    define('ga_password','puni2689');
    define('ga_profile_id'  ,'65247208');
     
    $ga = new gapi(ga_account,ga_password);
    
    
    $dimensions = array('customVarValue1');
    $metrics    = array('visits');
    $filter = 'ga:customVarValue1 != yes &&  ga:customVarValue1 != no &&  ga:customVarValue1 != 123 &&  ga:customVarValue1 != 5 && year == '.$_REQUEST['year'].'  &&  month == '.$_REQUEST['month'];
    $ga->requestReportData(ga_profile_id, $dimensions, $metrics,'-visits',$filter,'','',1,25);
    $gaResults = $ga->getResults();
    $i=1;
    $str_campaign_names_for_ga = array();
    $str_campaign_total_visits = array();
    foreach($gaResults as $result)
    {
     // echo $result->getCustomVarValue1()."  ".$result->getVisits()."<br />";
     array_push($str_campaign_names_for_ga,$result->getCustomVarValue1());
     array_push($str_campaign_total_visits,intval($result->getVisits()));
    }
    $main_array = array();
    $main_array['names'] = $str_campaign_names_for_ga;
    $main_array['visits'] = $str_campaign_total_visits;
    $str_campaigns = json_encode($main_array);
    echo $str_campaigns;
    exit;
    
}
?>
