<?php
//require_once(SERVER_PATH."/classes/Xtreme.php");
//require_once(SERVER_PATH."/classes/class.phpmailer.php");
if(session_id() == '') {
session_start();
}
class JSON extends Xtreme{ 

	public $Conn = null;

	function __construct($para=""){
		parent::__construct($para);
		$this->Conn = parent::GetConnection();

	}
	
	function mysql_escape(&$attr)
	{
		if(is_array($attr)){
			foreach ($attr as $key => $value){
				$value = trim($value);
				$attr[$key] = mysql_escape_string($value);
			}
		}else{
			$attr = trim($attr);
			$attr = mysql_escape_string($attr);
		}	
	}
        
        function distance($lat1, $lon1, $lat2, $lon2, $unit) 
        {

          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);

          if ($unit == "K") {
            return round(($miles * 1.609344),2);
          } else if ($unit == "N") {
              return round(($miles * 0.8684),2);
            } else {
                return round($miles,2);
              }
        }
        
      
        
        
	function get_cutomer_session_id($session_data)
	{
		/* $Sql = "SELECT * FROM user_sessions WHERE session_data='".mysql_escape_string($session_data)."' ORDER BY id DESC LIMIT 1";
		//echo $Sql."<hr>";
		$RS = $this->Conn->Execute($Sql); */
		$RS = $this->Conn->Execute("SELECT * FROM user_sessions WHERE session_data=? ORDER BY id DESC LIMIT 1",array(mysql_escape_string($session_data)));
				
		if($RS->RecordCount()>0){
			$session_id = base64_decode($RS->fields['session_id']);
			return $session_id;
		}
		return "";
		
		
	}
	function increase_image_count_for_merchant()
	{
		
		if($_SESSION['merchant_info']['merchant_parent']==0)
		{
			/* $sql = "Update merchant_user set image_count=image_count+1 where id=".$_SESSION['merchant_id'];
			$this->Conn->Execute($sql); */
			$this->Conn->Execute("Update merchant_user set image_count=image_count+1 where id=?",array($_SESSION['merchant_id']));
		}
		else
		{
	        $arr_1=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
			if(trim($arr_1[0]) == "")
			{
			 unset($arr_1[0]);
			 $arr_1 = array_values($arr_1);
			}
			$json_1 = json_decode($arr_1[0]);
			$main_merchant_id = $json_1->main_merchant_id;
	
			/* $sql = "Update merchant_user set image_count=image_count+1 where id=".$main_merchant_id;
			$this->Conn->Execute($sql); */
			$this->Conn->Execute("Update merchant_user set image_count=image_count+1 where id=?",array($main_merchant_id));
		}
		
		$_SESSION['merchant_info']['image_count']=$_SESSION['merchant_info']['image_count']+1;
	}
	function decrease_image_count_for_merchant()
	{
		if($_SESSION['merchant_info']['merchant_parent']==0)
		{
			/* $sql = "Update merchant_user set image_count=image_count-1 where id=".$_SESSION['merchant_id'];
			$this->Conn->Execute($sql); */
			$this->Conn->Execute("Update merchant_user set image_count=image_count-1 where id=?",array($_SESSION['merchant_id']));
		}
		else
		{
			$arr_1=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
			if(trim($arr_1[0]) == "")
			{
			 unset($arr_1[0]);
			 $arr_1 = array_values($arr_1);
			}
			$json_1 = json_decode($arr_1[0]);
			$main_merchant_id = $json_1->main_merchant_id;
			
			/* $sql = "Update merchant_user set image_count=image_count-1 where id=".$main_merchant_id;
			$this->Conn->Execute($sql); */
			$this->Conn->Execute("Update merchant_user set image_count=image_count-1 where id=?",array($main_merchant_id));
		}
		
		$_SESSION['merchant_info']['image_count']=$_SESSION['merchant_info']['image_count']-1;
	}
	
	
	function increase_video_count_for_merchant()
	{
		
		if($_SESSION['merchant_info']['merchant_parent']==0)
		{
			/* $sql = "Update merchant_user set image_count=image_count+1 where id=".$_SESSION['merchant_id'];
			$this->Conn->Execute($sql); */
			$this->Conn->Execute("Update merchant_user set video_count=video_count+1 where id=?",array($_SESSION['merchant_id']));
		}
		else
		{
	        $arr_1=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
			if(trim($arr_1[0]) == "")
			{
			 unset($arr_1[0]);
			 $arr_1 = array_values($arr_1);
			}
			$json_1 = json_decode($arr_1[0]);
			$main_merchant_id = $json_1->main_merchant_id;
	
			/* $sql = "Update merchant_user set video_count=video_count+1 where id=".$main_merchant_id;
			$this->Conn->Execute($sql); */
			$this->Conn->Execute("Update merchant_user set video_count=video_count+1 where id=?",array($main_merchant_id));
		}
		
		$_SESSION['merchant_info']['video_count']=$_SESSION['merchant_info']['video_count']+1;
	}
	function decrease_video_count_for_merchant()
	{
		if($_SESSION['merchant_info']['merchant_parent']==0)
		{
			/* $sql = "Update merchant_user set image_count=image_count-1 where id=".$_SESSION['merchant_id'];
			$this->Conn->Execute($sql); */
			$this->Conn->Execute("Update merchant_user set video_count=video_count-1 where id=?",array($_SESSION['merchant_id']));
		}
		else
		{
			$arr_1=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
			if(trim($arr_1[0]) == "")
			{
			 unset($arr_1[0]);
			 $arr_1 = array_values($arr_1);
			}
			$json_1 = json_decode($arr_1[0]);
			$main_merchant_id = $json_1->main_merchant_id;
			
			/* $sql = "Update merchant_user set video_count=video_count-1 where id=".$main_merchant_id;
			$this->Conn->Execute($sql); */
			$this->Conn->Execute("Update merchant_user set video_count=video_count-1 where id=?",array($main_merchant_id));
		}
		
		$_SESSION['merchant_info']['video_count']=$_SESSION['merchant_info']['video_count']-1;
	}
	
	//echo get_main_merchant_id(400);
	
        //get main merchant id
	function get_main_merchant_id($id)
	{
		/* $Sql = "select merchant_parent from merchant_user where id=".$id; 
		$rs = $this->Conn->Execute($Sql); */
		$rs = $this->Conn->Execute("select merchant_parent from merchant_user where id=?",array($id));
		
		if($rs->fields['merchant_parent'] == 0)
		{
			
			return $id;
		}
		else
		{
		   
			//$mainid= $rs->fields['merchant_parent'];
			$this->get_main_merchant_id($rs->fields['merchant_parent']);
			//call_user_func("get_main_merchant_id",$mainid);
			
		}
	}
        
        //
	function get_customer_deals($customer_id = "", $order_by = "",$lati,$long)
	{
		if($customer_id == ""){
                    if(isset($_SESSION['customer_id']))
                    {
			$customer_id = $_SESSION['customer_id'];
                    }
		}else{
			$customer_id = $this->get_cutomer_session_id($customer_id);
		}
		if($customer_id == ""){
			$json_array = array();
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			return $json;
		}
		if($order_by == "expiration_date"){
			$order_by = "C.expiration_date";
		}elseif($order_by == "title"){
			$order_by = "C.title";
		}elseif($order_by == "cat_name"){
			$order_by = "CAT.cat_name";
		}else{
			$order_by = "C.id";
		}
		$json_array = array();
		if(isset($_SESSION['mycurrent_lati']))
                {
                    $mlatitude=$_SESSION['mycurrent_lati'];
                    $mlongitude=$_SESSION['mycurrent_long'];
                }
	  	if(isset($_COOKIE['miles_cookie']))
		{
			$miles = $_COOKIE['miles_cookie'];
		}
		else
		{
			$miles = 50;
		}         
		if(isset($_COOKIE['cat_remember']))
		{
			$category_id  = $_COOKIE['cat_remember'];
		}
		else
		{
			$category_id  = 0;
		}
		 $cat_wh = "";
		if( $category_id !=0)
		{
			$cat_wh = " and C.category_id=". $category_id ;
		}
		else
		{
			$cat_wh=" and CAT.active=1";
		}
        if(isset($_COOKIE['zoominglevel']))
		{
			 $zooming_level = $_COOKIE['zoominglevel'];
		}
		else
		{
			 $zooming_level = 20;
		}
		if($lati=="" && $long=="")
		{
		   if($_COOKIE['searched_location'] == "")
		   {
				$curr_latitude = $_SESSION['customer_info']['curr_latitude'];
				$curr_longitude = $_SESSION['customer_info']['curr_longitude'];
		   }
		   else
		   {
			   
				$curr_latitude = $_COOKIE['mycurrent_lati'];
				$curr_longitude = $_COOKIE['mycurrent_long'];
		   }
		}
		else
		{
			$curr_latitude = $lati;
			$curr_longitude = $long;
		}
		
		 $Where_miles = "";
		if($curr_longitude != "")
		{
			$Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE ( 69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
		}
		
		  if($curr_latitude!="" && $curr_longitude!="")
		{
			/* $Sql = "SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,(((acos(sin((".$curr_latitude."*pi()/180)) * 
				sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
				cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
				pi()/180))))*180/pi())*60*1.1515
			) as distance
					FROM campaigns C, customer_campaigns CC, categories CAT,locations l,campaign_location cl
					WHERE CAT.active=1 and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date   and cl.active=1 and l.id=cl.location_id and cl.campaign_id=C.id 
					and cl.location_id in
	 (SELECT DISTINCT ss.location_id from subscribed_stores ss where CAT.active=1 and ss.customer_id=".$customer_id." and ss.subscribed_status=1 ".$Where_miles." ) and l.active=1 and CC.customer_id = '$customer_id' AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id 
					".$cat_wh." ORDER BY distance";
			//echo $Sql."<hr>";
			//exit; 
			$RS = $this->Conn->Execute($Sql);*/
			$RS = $this->Conn->Execute("SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,(((acos(sin((? *pi()/180)) * 
				sin((`latitude`*pi()/180))+cos((? *pi()/180)) * 
				cos((`latitude`*pi()/180)) * cos(((? - `longitude`)* 
				pi()/180))))*180/pi())*60*1.1515
			) as distance
					FROM campaigns C, customer_campaigns CC, categories CAT,locations l,campaign_location cl
					WHERE CAT.active=1 and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date   and cl.active=1 and l.id=cl.location_id and cl.campaign_id=C.id 
					and cl.location_id in
	 (SELECT DISTINCT ss.location_id from subscribed_stores ss where CAT.active=1 and ss.customer_id=? and ss.subscribed_status=1 ? ) and l.active=1 and CC.customer_id = '$customer_id' AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id 
					? ORDER BY distance",array($curr_latitude,$curr_latitude,$curr_longitude,$customer_id,$Where_miles,$cat_wh));
		}
		else
		{
            /* $Sql = "SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude
				FROM campaigns C, customer_campaigns CC, categories CAT,locations l,campaign_location cl
				WHERE CAT.active=1 and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date and cl.active=1 and l.id=cl.location_id and cl.campaign_id=C.id and l.active=1 and CC.customer_id = '$customer_id' AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id 
				".$cat_wh." ORDER BY C.created_date DESC";
			//echo $Sql."<hr>";
			//exit; 
			$RS = $this->Conn->Execute($Sql);*/
			$RS = $this->Conn->Execute("SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude
				FROM campaigns C, customer_campaigns CC, categories CAT,locations l,campaign_location cl
				WHERE CAT.active=1 and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date and cl.active=1 and l.id=cl.location_id and cl.campaign_id=C.id and l.active=1 and CC.customer_id = '$customer_id' AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id 
				? ORDER BY C.created_date DESC",array($cat_wh));
		}		
					
		if($RS->RecordCount()>0){
			$count=0;
			while($Row = $RS->FetchRow()){
				//echo "<pre>";
				//print_r($Row);
				//echo "</pre>";
				//exit;
				$json_array[$count] = $Row;
				$count++;
			}
		}else{
			$json_array['message'] = "No Deal is Found";
		}
		$json = json_encode($json_array);
		return $json;
	
	}
	function get_customer_redeemed_deals($customer_id = "", $order_by = "")
	{
		if($customer_id == ""){
			$customer_id = $_SESSION['customer_id'];
		}else{
			$customer_id = $this->get_cutomer_session_id($customer_id);
		}
		if($customer_id == ""){
			$json_array = array();
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			return $json;
		}
		if($order_by == "expiration_date"){
			$order_by = "C.expiration_date";
		}elseif($order_by == "title"){
			$order_by = "C.title";
		}elseif($order_by == "cat_name"){
			$order_by = "CAT.cat_name";
		}else{
			$order_by = "C.id";
		}
		$json_array = array();
		
		$mlatitude=$_SESSION['mycurrent_lati'];
	        $mlongitude=$_SESSION['mycurrent_long'];
		
		/*	
		$Sql = "SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip
				FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id and (cl.num_activation_code - (select count(*) from coupon_codes where location_id=cl.campaign_id)) > 0  
                                AND CONVERT_TZ(NOW(),'-06:00',SUBSTR(cl.timezone,1, POSITION(',' IN cl.timezone)-1)) BETWEEN C.start_date AND C.expiration_date and
                                 ru.customer_id = '$customer_id' AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1  GROUP BY ru.campaign_id , ru.location_id
				ORDER BY C.created_date DESC";*/
		if($mlatitude!="" && $mlongitude!="")
		{
			/* $Sql = "SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id  
                                AND CAT.active=1 and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and C.is_walkin!=1  and cl.active=1  and 
                                 ru.customer_id = '$customer_id' AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1  GROUP BY ru.campaign_id , ru.location_id
				ORDER BY distance"; 
			$RS = $this->Conn->Execute($Sql);*/
			$RS = $this->Conn->Execute("SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,(((acos(sin((? *pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((? *pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((? - `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id  
                                AND CAT.active=1 and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and C.is_walkin!=1  and cl.active=1  and 
                                 ru.customer_id = '$customer_id' AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1  GROUP BY ru.campaign_id , ru.location_id
				ORDER BY distance",array($mlatitude,$mlatitude,$mlongitude));
		}
		else
		{
		       /* $Sql = "SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id  
                                AND CAT.active=1 and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date and C.is_walkin!=1 and cl.active=1  and 
                                 ru.customer_id = '$customer_id' AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1  GROUP BY ru.campaign_id , ru.location_id
				ORDER BY C.created_date DESC";	
				$RS = $this->Conn->Execute($Sql);*/
				$RS = $this->Conn->Execute("SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id  
                                AND CAT.active=1 and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date and C.is_walkin!=1 and cl.active=1  and 
                                 ru.customer_id = '$customer_id' AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1  GROUP BY ru.campaign_id , ru.location_id
				ORDER BY C.created_date DESC");	
		}
                /*$Sql = "SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id  
                                AND   CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and cl.active=1  and 
                                 ru.customer_id = '$customer_id' AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1  GROUP BY ru.campaign_id , ru.location_id
				ORDER BY distance";*/
		
		
				
		if($RS->RecordCount()>0){
			$count=0;
			while($Row = $RS->FetchRow()){
				$json_array[$count] = $Row;
				$count++;
			}
		}else{
			$json_array['message'] = "No Deal is Found";
		}
		
		$json = json_encode($json_array);
		return $json;
	}
	function get_customer_subscribes_merchant($id,$lati,$long)
    {
             //$date_wh = "AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','+00:00') BETWEEN CONVERT_TZ(c.start_date,'+00:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) AND CONVERT_TZ(c.expiration_date,'+00:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))"; 
           //$Sql="select cl.location_id,l.location_name,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and c.created_by in (SELECT DISTINCT merchant_id from merchant_subscribs where user_id=".$id.") ".  $date_wh;
               //and CONVERT_TZ(NOW(),'+00:00','+00:00') BETWEEN CONVERT_TZ(start_date,'+00:00',SUBSTR(timezone,1, POSITION(',' IN timezone)-1)) AND CONVERT_TZ(expiration_date,'+00:00',SUBSTR(timezone,1, POSITION(',' IN timezone)-1))";
            /*
            $Sql="select cl.location_id,l.location_name,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and c.created_by in
 (SELECT DISTINCT merchant_id from merchant_subscribs where user_id=".$id.") AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','+00:00') BETWEEN 
CONVERT_TZ(c.start_date,'+00:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) AND CONVERT_TZ(c.expiration_date,'+00:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))
and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ".$id." and location_id=cl.location_id ) and 
(cl.num_activation_code - (select count(*) from coupon_codes where location_id=cl.campaign_id)) > 0 and cl.active=1";
             
             */
            
            // 22-1-2013 
            /*
            $Sql="select cl.location_id,l.location_name,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and c.created_by in
 (SELECT DISTINCT merchant_id from merchant_subscribs where user_id=".$id.") AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date 
and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ".$id." and location_id=cl.location_id ) and cl.offers_left>0 and cl.active=1";
             */
            //23-1-2013
            /*
            $Sql="select cl.location_id,l.location_name,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and cl.location_id in
 (SELECT DISTINCT location_id from subscribed_stores where customer_id=".$id." and subscribed_status=1) AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date 
and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ".$id." and location_id=cl.location_id ) and cl.offers_left>0 and cl.active=1";
            */
            //30-1-2013
             $mlatitude=$_SESSION['mycurrent_lati'];
	    $mlongitude=$_SESSION['mycurrent_long'];
            if(isset($_COOKIE['miles_cookie']))
            {
                $miles = $_COOKIE['miles_cookie'];
            }
            else
            {
                $miles = 50;
            }
            if(isset($_COOKIE['cat_remember']))
            {
                $category_id  = $_COOKIE['cat_remember'];
            }
            else
            {
                $category_id  = 0;
            }
             $cat_wh = "";
        if( $category_id !=0)
        {
           $cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
        }
        else
        {
            $cat_str = "";
        }
		if(isset($_COOKIE['zoominglevel']))
		{
			 $zooming_level = $_COOKIE['zoominglevel'];
		}
		else
		{
			 $zooming_level = 20;
		}
		if($lati=="" && $long=="")
		{
		   if($_COOKIE['searched_location'] == "")
		   {
				$curr_latitude = $_SESSION['customer_info']['curr_latitude'];
				$curr_longitude = $_SESSION['customer_info']['curr_longitude'];
		   }
		   else
		   {
			   
				$curr_latitude = $_COOKIE['mycurrent_lati'];
				$curr_longitude = $_COOKIE['mycurrent_long'];
		   }
		}
		else
		{
			$curr_latitude = $lati;
			$curr_longitude = $long;
		}
		
		 $Where_miles = "";
		if($curr_longitude != "")
		{
			//$Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE ( 69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
                        $Where_miles = "(69.1*(l.latitude-($curr_latitude))*69.1*(l.latitude-($curr_latitude)))+(53.0*(l.longitude-($curr_longitude))*53.0*(l.longitude-($curr_longitude)))<=".$dismile*$dismile ;	
		}
		/*else
		{
			
			$curr_latitude = $_COOKIE['mycurrent_lati'];
			$curr_longitude = $_COOKIE['mycurrent_long'];
			$Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE (69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
		}*/
	   //echo $Where_miles ."===";
                
         if($curr_latitude!="" && $curr_longitude!="")
	    {
           
//              $Sql="select cl.location_id,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,(((acos(sin((".$curr_latitude."*pi()/180)) * 
//            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
//            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
//            pi()/180))))*180/pi())*60*1.1515
//        ) as distance,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and cl.location_id in
// (SELECT DISTINCT ss.location_id from subscribed_stores ss where CAT.active=1 and ss.customer_id=".$id." and ss.subscribed_status=1 ".$Where_miles." )  and cl.active=1 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and c.is_walkin!=1 and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ".$id." and location_id=cl.location_id) and cl.offers_left>0 and cl.active=1 ".$cat_wh ."  ORDER BY distance";
 			//echo "if";
             
            /* $Sql = "SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,
                 (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id where ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id)
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date";

			//	   exit;
            $RS =$this->Conn->Execute($Sql); */
            $RS =$this->Conn->Execute("SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,
                 (((acos(sin((? *pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((? *pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((? - `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id where ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id)
  ?  ? and ?
 ORDER BY distance,c.expiration_date",array($curr_latitude,$curr_latitude,$curr_longitude,$cat_str,$date_wh,$Where_miles));
	    }
	    else
	    {
			/* $Sql = "SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,
                (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id where ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id)
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date";
//               $Sql="select cl.location_id,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and cl.location_id in
// (SELECT DISTINCT location_id from subscribed_stores where CAT.active=1 and customer_id=".$id." and subscribed_status=1)  and cl.active=1 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and c.is_walkin!=1 and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ".$id." and location_id=cl.location_id) and cl.offers_left>0 and cl.active=1 ".$cat_wh ."  "; 
 			//echo "else";

			//	   exit;
            $RS =$this->Conn->Execute($Sql); */
            $RS =$this->Conn->Execute("SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,
                (((acos(sin((? *pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((? *pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((? - `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id where ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id)
  ?  ? and ?
 ORDER BY distance,c.expiration_date",array($curr_latitude,$curr_latitude,$curr_longitude,$cat_str,$date_wh,$Where_miles));
	    }
       
            if($RS->RecordCount()>0){
			$count=0;
			while($Row = $RS->FetchRow()){
				$json_array[$count] = $Row;
				$count++;
			}
		}else{
			//$json_array['message'] = "No Deal Found";
		}
		$json = json_encode($json_array);
		return $json;
    }
	function activate_new_deal($customer_id = "", $activation_code , $cid , $lid)
	{
		//echo $activation_code."=".$cid."=".$lid."<br/>";	
		$this->Conn->StartTrans();
		$_SESSION['msg']= "";
		$json_array = array();
		if($customer_id == "")
		{
			$customer_id = $_SESSION['customer_id'];
		}
		else
		{
			$customer_id = $this->get_cutomer_session_id($customer_id);
		}
		if($customer_id == "")
		{
			$json_array = array();
            $json_array['error_msg'] = "Invalid Customer ID";
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			$this->Conn->CompleteTrans(); 
			return $json;
		}
		//echo $customer_id."<hr>";
		if($cid !=  0)
		{
			/* $Sql = "SELECT * FROM activation_codes WHERE activation_code='$activation_code' and campaign_id=".$cid;
			$RS = $this->Conn->Execute($Sql); */
			$RS = $this->Conn->Execute("SELECT * FROM activation_codes WHERE activation_code='$activation_code' and campaign_id=?",array($cid));
			//echo "campaign activation recordcount = ".$RS->RecordCount()."<br/>";
			if($RS->RecordCount()<=0)
			{
				$json_array['status'] = "false";
				$json_array['message'] = "Please enter valid activation code";
				  $json_array['error_msg'] = "Please enter valid activation code";
				$_SESSION['msg'] = "Please enter valid activation code";
				$json = json_encode($json_array);
				$this->Conn->CompleteTrans(); 
				return $json;
			}
		}
		else
		{
			/* $Sql = "SELECT * FROM activation_codes WHERE activation_code='$activation_code'";
			$RS = $this->Conn->Execute($Sql); */
			$RS = $this->Conn->Execute("SELECT * FROM activation_codes WHERE activation_code='$activation_code'");
			if($RS->RecordCount()<=0)
			{
				$json_array['status'] = "false";
				$json_array['message'] = "Please enter valid Activation code";
				$json_array['error_msg'] = "Please enter valid activation code";
				$_SESSION['msg'] = "Please enter valid activation code";
				$json = json_encode($json_array);
				$this->Conn->CompleteTrans(); 
				return $json;
			}
		}
              
		$campaign_id = $RS->fields['campaign_id'];
		//echo "campaign id = ".$campaign_id."<br/>";
		if($lid == 0)
		{
			/* $Sql = "select * from campaign_location where campaign_id = ".$campaign_id;
			$RS_loc = $this->Conn->Execute($Sql); */
			$RS_loc = $this->Conn->Execute("select * from campaign_location where campaign_id = ?",array($campaign_id));

			$lid = $RS_loc->fields['location_id'];
		}
		/* $sql_o = "select * from campaign_location where campaign_id =".$campaign_id." and location_id =". $lid ." and active=1";
		// echo   $sql_o;exit;
		$RS_o = $this->Conn->Execute($sql_o); */
		$RS_o = $this->Conn->Execute("select * from campaign_location where campaign_id = ? and location_id =? and active=1",array($campaign_id,$lid));

		/* $Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=".$lid;
		$RS = $this->Conn->Execute($Sql); */
		$RS = $this->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=?",array($lid));
		//echo "RS_o recordcount = ".$RS_o->RecordCount()."<br/>";
		//echo "RS recordcount = ".$RS->RecordCount()."<br/>";
		if($RS_o->RecordCount()>0)
		{
			if($RS->RecordCount()<=0)
			{
				/* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
				//  echo  "<br/>".$Sql_num_activation ."<br/>";
				$RS_num_activation = $this->Conn->Execute($Sql_num_activation); */
				$RS_num_activation = $this->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?",array($campaign_id,$lid));

				$offers_left = $RS_num_activation->fields['offers_left'];
				$used_campaign = $RS_num_activation->fields['used_offers'];
				//echo "====".$offers_left."====<br />";
				//exit;
				$share_flag= 1;
				if($offers_left > 0)
				{
					/* $Sql_max_is_walkin = "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=".$campaign_id;
					//  echo $Sql_max_is_walkin ;
					$RS_max_is_walkin = $this->Conn->Execute( $Sql_max_is_walkin); */
					$RS_max_is_walkin = $this->Conn->Execute("SELECT is_walkin , level ,new_customer  from campaigns WHERE id=?",array($campaign_id));

					//echo $RS_max_is_walkin->fields['new_customer']."=== new customer";
					if($RS_max_is_walkin->fields['new_customer'] == 1)
					{
						/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
						//    echo "<br />".$sql_chk."===<br/>";
						$subscibed_store_rs = $this->Conn->Execute($sql_chk); */
						$subscibed_store_rs = $this->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($_SESSION['customer_id'],$lid));

						if($subscibed_store_rs->RecordCount()==0)
						{
						$share_flag= 1;
						}
						else {
						$share_flag= 0;
						}
					}
					//  echo "=====".$share_flag."===========";
					/* check whether new customer for this store */
					$allow_for_reserve= 0;
					$is_new_user= 0;
					/*************** *************************/
					/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
					// echo $sql_chk."sql_chk<br/>";
					$Rs_is_new_customer = $this->Conn->Execute($sql_chk); */
					$Rs_is_new_customer = $this->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($_SESSION['customer_id'],$lid));
					//echo "====".$Rs_is_new_customer->RecordCount()."====";
					if($Rs_is_new_customer->RecordCount()==0)
					{
						$is_new_user= 1;
					}
					else
					{
						$is_new_user= 0;
					}
					/*************** *************************/
           
					if($is_new_user==0)  
					{
						/* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;
						// echo "<br/>===".$sql."===<br />";
						$RS_campaign_groups = $this->Conn->Execute($sql); */
						$RS_campaign_groups = $this->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$lid));

						$c_g_str = "";
						$cnt =1;          
						$is_it_in_group = 0;
						/*
						echo "RS_max_is_walkin->fields['level'] = ".$RS_max_is_walkin->fields['level']."<br/>";
						echo "RS_max_is_walkin->fields['is_walkin'] = ".$RS_max_is_walkin->fields['is_walkin']."<br/>";
						echo "RS_campaign_groups->RecordCount() = ".$RS_campaign_groups->RecordCount()."<br/>";
						exit();
						*/
						if($RS_max_is_walkin->fields['level'] == 0)
						{
							if($RS_max_is_walkin->fields['is_walkin'] == 0)
							{ 
								if($RS_campaign_groups->RecordCount()>0)
								{
									while($Row_campaign = $RS_campaign_groups->FetchRow())
									{ 
										$c_g_str = $Row_campaign['group_id'];
										if($cnt != $RS_campaign_groups->RecordCount())
										{
											$c_g_str .= "," ;
										}
									}
                                    /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
									// echo $Sql_new_."sql_new_ =============";
                                    $RS_check_s = $this->Conn->Execute($Sql_new_); */
                                    $RS_check_s = $this->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?))",array($_SESSION['customer_id'],$c_g_str));
                                    
									while($Row_Check_Cust_group = $RS_check_s->FetchRow())
									{
										/* $query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
										$RS_query = $this->Conn->Execute($query); */
										$RS_query = $this->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id=? AND group_id in(?)",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id'],$c_g_str));
										if($RS_query->RecordCount() > 0)
										{
											$is_it_in_group = 1;
										}
									}
									if($is_it_in_group == 1 )
									{ 
										$allow_for_reserve = 1;  	
									}
									else
									{
										$allow_for_reserve = 0;
									}
								}
								else
								{
									$allow_for_reserve = 0;  	
								}
							}
							else
							{
								// $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
								/* $query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
								$RS_all_user_group = $this->Conn->Execute($query); */
								$RS_all_user_group = $this->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =1 )",array($_SESSION['customer_id'],$lid));
								if($RS_all_user_group->RecordCount() > 0)
								{
									$allow_for_reserve = 1;
								}
								else
								{
									$allow_for_reserve = 0;  
								}
							}
						}
						else
						{
							//   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
							$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.") )";
							$allow_for_reserve= 1;
						}
					}
					else
					{
						$allow_for_reserve= 1; 
					}
           
					//
					// echo "<br />SQl_new===".$Sql_new_ ."=====<br />";
					/*echo "share_flag = ".$share_flag ." allow_for_reserve = ".$allow_for_reserve;
					exit();*/
					/* for checking whether customer in campaign group */
					if($share_flag== 1)
					{
						if($allow_for_reserve==1)
						{
							//  $this->Conn->StartTrans();
							//echo "1";
							/* $Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
							customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
							$this->Conn->Execute($Sql); */
							$this->Conn->Execute("INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
							customer_id='$customer_id', campaign_id='$campaign_id' , location_id=?",array($lid));
							//echo "2";
							/* $update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$lid." ";
							$this->Conn->Execute($update_num_activation); */
							$this->Conn->Execute("Update  campaign_location set offers_left=?, used_offers=? where campaign_id=? and location_id =?",array(($offers_left-1),($used_campaign+1),$campaign_id,$lid));
							//echo "3";
							$RSLocation_nm  = $this->Conn->Execute("select * from locations where id =?",array($lid));
							//echo "4";
							//$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$lid;
							//echo "generate_voucher_code(".$customer_id.",".$activation_code.",".$campaign_id.",".$RSLocation_nm->fields['location_name'].",".$lid.")";
							
							$br = $this->generate_voucher_code($customer_id,$activation_code,$campaign_id,$RSLocation_nm->fields['location_name'],$lid);
							
							//echo " br = ".$br;
							$json_array['campaign_id'] = $campaign_id;
							$json_array['location_id'] = $lid;
							$json_array['barcode'] = $br;
							//echo "5";
							//exit();
							//
							/* $select_coupon_code = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
							$select_rs = $this->Conn->Execute($select_coupon_code); */
							$select_rs = $this->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?",array($customer_id,$campaign_id,$lid));
							//echo "6";
							if($select_rs->RecordCount()<=0)
							{
								$array_ =array();
								$array_['customer_id'] = $customer_id;
								$array_['customer_campaign_code'] = $campaign_id;

								$array_['coupon_code'] = $br;
								$array_['active']=1;
								$array_['location_id'] = $lid;
								$array_['generated_date'] = date('Y-m-d H:i:s');
								/* $insert_coupon_code = "Insert into coupon_codes set customer_id=".$customer_id." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$lid." , generated_date='".date('Y-m-d H:i:s')."' ";
								$this->Conn->Execute($insert_coupon_code); */
								$this->Conn->Execute("Insert into coupon_codes set customer_id=?, customer_campaign_code=?, coupon_code=?, active=1 , location_id=?, generated_date='?'",array($customer_id,$campaign_id,$br,$lid,date('Y-m-d H:i:s')));   
								//echo "7";
							}
							///
							//
							//Make entry in subscribed_stre table for first time subscribe to loaction
							/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
							$RS_group = $this->Conn->Execute($sql_group); */
							$RS_group = $this->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($lid));
							//echo "8";
							/* $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
							$subscibed_store_rs =$this->Conn->Execute($sql_chk); */
							$subscibed_store_rs = $this->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?",array($_SESSION['customer_id'],$lid));
							//echo "9";
							if($subscibed_store_rs->RecordCount()==0)
							{
								/* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
								$this->Conn->Execute($insert_subscribed_store_sql); */
								$this->Conn->Execute("insert into subscribed_stores set customer_id= ?,location_id=?,subscribed_date=?,subscribed_status=1",array($_SESSION['customer_id'],$lid,date('Y-m-d H:i:s')));
								//echo "10";
							}
							else
							{
								if($subscibed_store_rs->fields['subscribed_status']==0)
								{
									/* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
									$this->Conn->Execute($up_subscribed_store); */
									$this->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id=? and location_id=?",array($_SESSION['customer_id'],$lid));
									//echo "11";
								}
							}
							// If campaign is walking deal then make entry in coupon_codes table //

							/* $RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
							$check_subscribe = $this->Conn->Execute($RS_user_subscribe); */
							$check_subscribe = $this->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ? and private = 1) and user_id = ?",array($lid,$_SESSION['customer_id']));
							//echo "12";
							if($check_subscribe->RecordCount()==0)
							{
								/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
								$RS_group = $this->Conn->Execute($sql_group); */
								$RS_group = $this->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($lid));
								//echo "13";
								if($RS_group->RecordCount()>0)
								{
									/* $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;
									$RS_user_group =$this->Conn->Execute($sql_user_group); */
									$RS_user_group = $this->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));
									//echo "14";	
									if($RS_user_group->RecordCount()<=0)
									{
										/* $insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
										$this->Conn->Execute($insert_sql); */
										$this->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =?, group_id =?, user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$_SESSION['customer_id']));
										//echo "15";
									}
								}
							}
							//
							//	$this->Conn->CompleteTrans(); 
						}
						else
						{
							$json_array['status'] = "newuser";
							$json_array['campaign_for_new_user'] = "All offers are reserved by other customers or campaign has expired";
							$json_array['error_msg'] = "All offers are reserved by other customers or campaign has expired";
							$_SESSION['campaign_for_new_user'] = "All offers are reserved by other customers or campaign has expired";
							$json = json_encode($json_array);
							$this->Conn->CompleteTrans(); 
							return $json;
						}
					}
					else
					{
						$json_array['status'] = "newuser";
						$json_array['campaign_for_new_user'] = "This campaign is for new users only";
						$json_array['error_msg'] = "This campaign is for new users only";
						$_SESSION['campaign_for_new_user'] = "This campaign is for new users only";
						$json = json_encode($json_array);
						$this->Conn->CompleteTrans(); 
						return $json;
					}
			
					// --- For data entry in merchant_subscribs
					//			$camp_array['id']=$campaign_id;		
					//			$RS_campaign  = $this->Conn->Execute("select * from campaigns where id =".$campaign_id);
					//			$m_id = $RS_campaign->fields['created_by'];
					//			
					//			
					//			
					//			$Sql = "SELECT * FROM merchant_subscribs WHERE merchant_id='$m_id' AND user_id='$customer_id'";
					//				$RS_ms = $this->Conn->Execute($Sql);
					//				if($RS_ms->RecordCount()<=0){
					//					
					//					$Sql = "INSERT INTO merchant_subscribs SET merchant_id='$m_id', user_id='$customer_id'";
					//					$this->Conn->Execute($Sql);
					//				}



					// ---
				}
                else
                {
					$json_array['status'] = "ended";
					$json_array['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired";
					$json_array['error_msg'] = "All offers are reserved by other customers or campaign has expired";
					$_SESSION['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired";
					$this->Conn->CompleteTrans(); 
					$json = json_encode($json_array);
					return $json;
				}
			}
            else
            {
				if($RS->fields['activation_status'] == 0)
				{
					/* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
					//  echo  "<br/>".$Sql_num_activation ."<br/>";
					$RS_num_activation = $this->Conn->Execute($Sql_num_activation); */
					$RS_num_activation = $this->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?",array($campaign_id,$lid));

					$offers_left = $RS_num_activation->fields['offers_left'];
					$used_campaign = $RS_num_activation->fields['used_offers'];
					//  echo "---".$offers_left."========================<br />";
					//  exit;
					$share_flag= 1;
					if($offers_left > 0)
					{
						/* $Sql_max_is_walkin = "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=".$campaign_id;
						//  echo $Sql_max_is_walkin ;
						$RS_max_is_walkin = $this->Conn->Execute( $Sql_max_is_walkin); */
						$RS_max_is_walkin = $this->Conn->Execute("SELECT is_walkin , level ,new_customer  from campaigns WHERE id=?",array($campaign_id));

						//    echo $RS_max_is_walkin->fields['new_customer']."=== new customer";
						if($RS_max_is_walkin->fields['new_customer'] == 1)
						{
							/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
							//    echo "<br />".$sql_chk."===<br/>";
							$subscibed_store_rs =$this->Conn->Execute($sql_chk); */
							$subscibed_store_rs = $this->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($_SESSION['customer_id'],$lid));
							if($subscibed_store_rs->RecordCount()==0)
							{
								$share_flag= 1;
							}
							else 
							{
								$share_flag= 0;
							}
						}
						//  echo "=====".$share_flag."===========";
						/* check whether new customer for this store */
						$allow_for_reserve= 0;
						$is_new_user= 0;
						/*************** *************************/
						/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
						// echo $sql_chk."sql_chk<br/>";
						$Rs_is_new_customer=$this->Conn->Execute($sql_chk); */
						$Rs_is_new_customer = $this->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($_SESSION['customer_id'],$lid));
						if($Rs_is_new_customer->RecordCount()==0)
						{
							$is_new_user= 1;
						}
						else 
						{
							$is_new_user= 0;
						}
						/*************** *************************/

						if($is_new_user==0)  
						{
							/* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;
							// echo "<br/>===".$sql."===<br />";
							$RS_campaign_groups = $this->Conn->Execute($sql); */
							$RS_campaign_groups = $this->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$lid));
							$c_g_str = "";
							$cnt =1;

							$is_it_in_group = 0;
							if($RS_max_is_walkin->fields['level'] == 0)
							{
								if($RS_max_is_walkin->fields['is_walkin'] == 0)
								{ 
									if($RS_campaign_groups->RecordCount()>0)
									{
										while($Row_campaign = $RS_campaign_groups->FetchRow())
										{ 
											$c_g_str = $Row_campaign['group_id'];
											if($cnt != $RS_campaign_groups->RecordCount())
											{
												$c_g_str .= "," ;
											}
										}
										/* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
										// echo $Sql_new_."sql_new_ =============";
										$RS_check_s = $this->Conn->Execute($Sql_new_); */
										$RS_check_s = $this->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?))",array($_SESSION['customer_id'],$c_g_str));
										while($Row_Check_Cust_group = $RS_check_s->FetchRow())
										{
											/* $query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
											$RS_query = $this->Conn->Execute($query); */
											$RS_query = $this->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id=? AND group_id in(?)",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id'],$c_g_str));
											if($RS_query->RecordCount() > 0)
											{
												$is_it_in_group = 1;
											}
										}
										if($is_it_in_group == 1 )
										{ 
											$allow_for_reserve = 1;  	
										}
										else 
										{
											$allow_for_reserve = 0;
										}
									}
									else
									{
										$allow_for_reserve = 0;  	
									}
								}
								else
								{
									// $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
									/* $query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
									$RS_all_user_group = $this->Conn->Execute($query); */
									$RS_all_user_group = $this->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =1 )",array($_SESSION['customer_id'],$lid));
									if($RS_all_user_group->RecordCount() > 0)
									{
										$allow_for_reserve = 1;
									}
									else
									{
										$allow_for_reserve = 0;  
									}
								}
                            }
                            else
							{
								//   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
								$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$lid."  )";
								$allow_for_reserve= 1;
							}
						}
						else
						{
							$allow_for_reserve= 1; 
						}
           
						//
						// echo "<br />SQl_new===".$Sql_new_ ."=====<br />";

						/* for checking whether customer in campaign group */
						if($share_flag== 1)
						{
							if($allow_for_reserve==1)
							{
								// $this->Conn->StartTrans();
								//  $Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
								//customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
								/*$Sql = "Update customer_campaigns SET activation_status=1 where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$lid;  
								$this->Conn->Execute($Sql);*/

								$this->Conn->Execute("Update customer_campaigns SET activation_status=1 where customer_id=? and campaign_id=? and location_id=?",array($customer_id,$campaign_id,$lid));

								$RSLocation_nm  = $this->Conn->Execute("select * from locations where id =?",array($lid));

								$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$lid;
								$json_array['campaign_id'] = $campaign_id;
								$json_array['location_id'] = $lid;
								$json_array['barcode'] = $br;
                        
								//
								/* $select_coupon_code = "update coupon_codes set active= 1 where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
								$select_rs = $this->Conn->Execute($select_coupon_code); */
								$select_rs = $this->Conn->Execute("update coupon_codes set active= 1 where customer_id=? and customer_campaign_code=? and location_id=?",array($customer_id,$campaign_id,$lid));
								///
								//
								//Make entry in subscribed_stre table for first time subscribe to loaction
								/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
								$RS_group = $this->Conn->Execute($sql_group); */
								$RS_group = $this->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($lid));

								/* $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
								$subscibed_store_rs =$this->Conn->Execute($sql_chk); */
								$subscibed_store_rs = $this->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?",array($_SESSION['customer_id'],$lid));

								if($subscibed_store_rs->RecordCount()==0)
								{
									/* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
									$this->Conn->Execute($insert_subscribed_store_sql); */
									$this->Conn->Execute("insert into subscribed_stores set customer_id= ?,location_id=?,subscribed_date=?,subscribed_status=1",array($_SESSION['customer_id'],$lid,date('Y-m-d H:i:s')));
								}
								else 
								{
									if($subscibed_store_rs->fields['subscribed_status']==0)
									{
										/* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
										$this->Conn->Execute($up_subscribed_store); */
										$this->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id=? and location_id=?",array($_SESSION['merchant_id'],$lid));
									}
								}
								// If campaign is walking deal then make entry in coupon_codes table //

								/* $RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
								$check_subscribe = $this->Conn->Execute($RS_user_subscribe); */
								$check_subscribe = $this->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ? and private = 1) and user_id = ?",array($lid,$_SESSION['customer_id']));
								if($check_subscribe->RecordCount()==0)
								{
									/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
									$RS_group = $this->Conn->Execute($sql_group); */	
									$RS_group = $this->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($lid));

									if($RS_group->RecordCount()>0)
									{
										/* $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;
										$RS_user_group =$this->Conn->Execute($sql_user_group); */
										$RS_user_group = $this->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));

										if($RS_user_group->RecordCount()<=0)
										{
											/* $insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
											$this->Conn->Execute($insert_sql); */
											$this->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =?, group_id =?, user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$_SESSION['customer_id']));
										}
									}
								}
							//
							//$this->Conn->CompleteTrans();
							}
							else
							{
								$json_array['status'] = "newuser";
								$json_array['campaign_for_new_user'] = "All offers are reserved by other customers or campaign has expired";
								$json_array['error_msg'] = "All offers are reserved by other customers or campaign has expired";
								$_SESSION['campaign_for_new_user'] = "All offers are reserved by other customers or campaign has expired";
								$json = json_encode($json_array);
								$this->Conn->CompleteTrans(); 
								return $json;
							}
						}
						else
						{
							$json_array['status'] = "newuser";
							$json_array['campaign_for_new_user'] = "This campaign is for new users only";
							$json_array['error_msg'] = "This campaign is for new users only";
							$_SESSION['campaign_for_new_user'] = "This campaign is for new users only";
							$json = json_encode($json_array);
							$this->Conn->CompleteTrans(); 
							return $json;
						}
			
						// --- For data entry in merchant_subscribs
						//			$camp_array['id']=$campaign_id;		
						//			$RS_campaign  = $this->Conn->Execute("select * from campaigns where id =".$campaign_id);
						//			$m_id = $RS_campaign->fields['created_by'];
						//			
						//			
						//			
						//			$Sql = "SELECT * FROM merchant_subscribs WHERE merchant_id='$m_id' AND user_id='$customer_id'";
						//				$RS_ms = $this->Conn->Execute($Sql);
						//				if($RS_ms->RecordCount()<=0){
						//					
						//					$Sql = "INSERT INTO merchant_subscribs SET merchant_id='$m_id', user_id='$customer_id'";
						//					$this->Conn->Execute($Sql);
						//				}



						// ---
					}
					else
					{
						$json_array['status'] = "ended";
						$json_array['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired";
						$json_array['error_msg'] = "All offers are reserved by other customers or campaign has expired";
						$_SESSION['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired";
						$json = json_encode($json_array);
						$this->Conn->CompleteTrans(); 
						return $json;
					}
				}
				
				$redirect_query = "select permalink from campaign_location where campaign_id=".$campaign_id." and location_id=".$lid;
				$redirect_RS = $this->Conn->Execute($redirect_query );
				$json_array['permalink'] =  $redirect_RS->fields['permalink'];
				$json_array['campaign_id'] = $campaign_id;
				$json_array['location_id'] = $lid;
				$json_array['status'] = "true";
				$json = json_encode($json_array);
				$this->Conn->CompleteTrans(); 
				return $json;
			}
		}
		else
		{
			$json_array['status'] = "ended";
			$json_array['campaign_end_message'] = "This campaign is ended for your selected location so you can not reserve this campaign. For more campaign visit browse deal page";
			$json_array['error_msg'] = "This campaign is ended for your selected location so you can not reserve this campaign. For more campaign visit browse deal page";
			$_SESSION['campaign_end_message'] = "This campaign is ended for your selected location so you can not reserve this campaign. For more campaign visit browse deal page";
			$json = json_encode($json_array);
			$this->Conn->CompleteTrans(); 
			return $json;
		}
		$redirect_query = "select permalink from campaign_location where campaign_id=".$campaign_id." and location_id=".$lid;
		$redirect_RS = $this->Conn->Execute($redirect_query );
		$json_array['permalink'] =  $redirect_RS->fields['permalink'];
		$json_array['campaign_id'] = $campaign_id;
		$json_array['location_id'] = $lid;
		$json_array['status'] = "true";
		$json = json_encode($json_array);
		$this->Conn->CompleteTrans(); 
		return $json;
	}
	function get_customer_expire_deals($customer_id = "")
	{
		$json_array = array();
		if($customer_id == ""){
			$customer_id = $_SESSION['customer_id'];
		}else{
			$customer_id = $this->get_cutomer_session_id($customer_id);
		}
		if($customer_id == ""){
			$json_array = array();
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			return $json;
		}
		$today_date = date("Y-m-d H:i:s");
		$last_month_date = date('Y-m-d', strtotime(date('Y-m-d')) - 60*60*24*30)." 00:00:00";
		
		$mlatitude=$_SESSION['mycurrent_lati'];
	        $mlongitude=$_SESSION['mycurrent_long'];
		
		$dt_wh = " AND CONVERT_TZ(C.expiration_date,'+00:00',SUBSTR(C.timezone,1, POSITION(',' IN C.timezone)-1)) < CONVERT_TZ(NOW(),'+00:00','+00:00') ";
	
		//$Sql = "SELECT C.*, CAT.cat_name  ,l.timezone as loc_timezone , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip
		//		FROM campaigns C, customer_campaigns CC, categories CAT ,locations l
		//		WHERE l.active=1 and CC.customer_id = '$customer_id' AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id
		//		".$dt_wh ." ORDER BY C.id DESC";
                /*
		$Sql="SELECT C.*, CAT.cat_name  , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip
		FROM campaigns C, customer_campaigns CC, categories CAT ,locations l
		WHERE l.active=1 and CC.customer_id = 31 AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id
		AND CONVERT_TZ(C.expiration_date,'+00:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) < CONVERT_TZ(NOW(),'-06:00','+00:00') and 
		(C.expiration_date BETWEEN '".$last_month_date."' and '".$today_date."')  ORDER BY C.id DESC";
                 
                 */
         if($mlatitude!="" && $mlongitude!="")
		{
			/*$Sql="SELECT C.*, CAT.cat_name  , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,(((acos(sin((".$mlatitude."*pi()/180)) * 
				sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
				cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
				pi()/180))))*180/pi())*60*1.1515
			) as distance FROM campaigns C, customer_campaigns CC, categories CAT ,locations l
			WHERE l.active=1  and C.is_walkin!=1 and CC.customer_id = ".$customer_id." AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id
			AND C.expiration_date <  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
			(C.expiration_date BETWEEN '".$last_month_date."' and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)))  ORDER BY distance ";
			//	echo $Sql."<hr>";
		   //   exit;
			$RS = $this->Conn->Execute($Sql);
			*/
			$RS = $this->Conn->Execute("SELECT C.*, CAT.cat_name  , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,(((acos(sin((? *pi()/180)) * 
				sin((`latitude`*pi()/180))+cos((?*pi()/180)) * 
				cos((`latitude`*pi()/180)) * cos(((?- `longitude`)* 
				pi()/180))))*180/pi())*60*1.1515
			) as distance FROM campaigns C, customer_campaigns CC, categories CAT ,locations l
			WHERE l.active=1  and C.is_walkin!=1 and CC.customer_id = ? AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id
			AND C.expiration_date <  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
			(C.expiration_date BETWEEN '?' and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)))  ORDER BY distance ",array($mlatitude,$mlatitude,$mlongitude,$customer_id,$last_month_date));				
		}
		else
		{
			/*$Sql="SELECT C.*, CAT.cat_name  , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude FROM campaigns C, customer_campaigns CC, categories CAT ,locations l
			WHERE l.active=1  and C.is_walkin!=1 and CC.customer_id = ".$customer_id." AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id
			AND C.expiration_date <  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
			(C.expiration_date BETWEEN '".$last_month_date."' and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)))  ORDER BY C.id DESC";
			//	echo $Sql."<hr>";
		   //   exit;
			$RS = $this->Conn->Execute($Sql);*/
			$RS = $this->Conn->Execute("SELECT C.*, CAT.cat_name  , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude FROM campaigns C, customer_campaigns CC, categories CAT ,locations l
			WHERE l.active=1  and C.is_walkin!=1 and CC.customer_id = ? AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id
			AND C.expiration_date <  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
			(C.expiration_date BETWEEN '?' and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)))  ORDER BY C.id DESC",array($customer_id,$last_month_date));		
		}
                
			
		if($RS->RecordCount()>0){
			$count=0;
			while($Row = $RS->FetchRow()){
				$json_array[$count] = $Row;
				$count++;
			}
		}else{
			$json_array['message'] = "No Deal is Found";
		}
		$json = json_encode($json_array);
		return $json;
		
	}
	function get_customer_profile($customer_id = "")
	{
		$json_array = array();
		if($customer_id == ""){
			if(isset($_SESSION['customer_id']))
			{
				$customer_id = $_SESSION['customer_id'];
			}
		}else{
			$customer_id = $this->get_cutomer_session_id($customer_id);
		}
		if($customer_id == ""){
			$json_array = array();
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			return $json;
		}
		/* $Sql = "SELECT * FROM customer_user WHERE id='$customer_id'";
		$RS = $this->Conn->Execute($Sql); */
		$RS = $this->Conn->Execute("SELECT * FROM customer_user WHERE id='$customer_id'");
		if($RS->RecordCount()>0){
			$Row = $RS->FetchRow();
			$json_array[] = $Row;
		}
		$json = json_encode($json_array);
		return $json;				
	
	}
        
	function get_compain_details($campaign_id)
	{
		//$campaign_id = mysql_escape_string(base64_decode($campaign_id));
		$campaign_id = mysql_escape_string($campaign_id);
		/* $Sql= "SELECT * FROM campaigns WHERE id='$campaign_id'";
		$RS = $this->Conn->Execute($Sql); */
		$RS = $this->Conn->Execute("SELECT * FROM campaigns WHERE id='$campaign_id'");
		if($RS->RecordCount()>0){
			$Row = $RS->FetchRow();
			$json_array[] = $Row;
		}else{
			$json_array['message'] = "Enter Valid Deal ID";
		}
		$json = json_encode($json_array);
		return $json;				
		
	}
	function get_compaign_map_locations($campaign_id)
	{
		$campaign_id = mysql_escape_string($campaign_id);
		$Sql = "SELECT C.*, CL.*, L.*
				FROM campaigns C, campaign_location CL, locations L
				WHERE C.id='$campaign_id' AND CL.campaign_id='$campaign_id' AND L.id=CL.location_id";
		echo $Sql."<hr>";		
	}
	
	
	
	function reward_user($customer_id, $campaign_id){
		$json_array = array();
		$customer_id = base64_decode($customer_id);
		$campaign_id = base64_decode($campaign_id);
		/* $Sql = "INSERT INTO reward_user SET customer_id='$customer_id', campaign_id='$campaign_id', earned_reward=5, referral_reward=5, referred_customer_id=0, reward_date=Now()";
		$this->Conn->Execute($Sql); */
		$this->Conn->Execute("INSERT INTO reward_user SET customer_id='$customer_id', campaign_id='$campaign_id', earned_reward=5, referral_reward=5, referred_customer_id=0, reward_date=Now()");
		$json_array['message'] = "Reward has been assigned successfully.";
		$json = json_encode($json_array);
		return $json;				
		
	}
	function import_customers(){
		$filename = $_FILES['import_customer']['tmp_name'];
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));

		$rows = explode("\n",$contents);
		
		foreach($rows as $index=>$row){
			if($row == "" || $index == 0) continue;
			$cell = explode(",",$row);
			$this->mysql_escape($cell);
			//print_r($cell);echo "<hr>";
			/* $Sql = "SELECT * FROM merchant_groups WHERE group_name='".$cell['7']."' AND merchant_id='$_SESSION[merchant_id]'";
			$RS = $this->Conn->Execute($Sql); */
			$RS = $this->Conn->Execute("SELECT * FROM merchant_groups WHERE group_name='?' AND merchant_id='$_SESSION[merchant_id]'",array($cell['7']));
			if($RS->RecordCount()>0){
				$group_id = $RS->fields['id'];
			}else{
				/* $Sql = "INSERT INTO merchant_groups SET group_name='".$cell['7']."', merchant_id='$_SESSION[merchant_id]'";
				$this->Conn->Execute($Sql); */
				$this->Conn->Execute("INSERT INTO merchant_groups SET group_name='?', merchant_id='$_SESSION[merchant_id]'",array($cell['7']));
				$group_id = $this->Conn->Insert_ID();
			}
			/* $Sql = "SELECT * FROM customer_user WHERE emailaddress='".$cell['6']."'";
			//echo $Sql."<hr>";
			$RS = $this->Conn->Execute($Sql); */
			$RS = $this->Conn->Execute("SELECT * FROM customer_user WHERE emailaddress='?'",array($cell['6']));
			if($RS->RecordCount()>0){
				$user_id = $RS->fields['id'];
				$user_email = $RS->fields['emailaddress'];
				/* $Sql = "SELECT * FROM merchant_subscribs WHERE merchant_id='$_SESSION[merchant_id]' AND user_id='$user_id' AND group_id='$group_id'";
				$RS = $this->Conn->Execute($Sql); */
				$RS = $this->Conn->Execute("SELECT * FROM merchant_subscribs WHERE merchant_id='$_SESSION[merchant_id]' AND user_id='$user_id' AND group_id='$group_id'");
				if($RS->RecordCount()<=0){
					/* $Sql = "INSERT INTO merchant_subscribs SET merchant_id='$_SESSION[merchant_id]', user_id='$user_id', group_id='$group_id'";
					$this->Conn->Execute($Sql); */
					$this->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id='$_SESSION[merchant_id]', user_id='$user_id', group_id='$group_id'");
					$mail = new PHPMailer();
					$body = "<p>You have been added to Scanflip by $_SESSION[merchant_info][firstname] $_SESSION[merchant_info][lastname]."; 
					$body .= "In order to see the $_SESSION[merchant_info][firstname] $_SESSION[merchant_info][lastname]'s campaignss, please login to your Scanflip account at ".WEB_PATH." </p>";
					$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
					$mail->AddAddress($user_email);
					$mail->From = "no-reply@scanflip.com";
					$mail->FromName = "ScanFlip Support";
					$mail->Subject    = "ScanFlip Subscription";
					$mail->MsgHTML($body);
					$mail->Send();
				}
			}else{
				$password = date("YmdHis");
				if($cell['5'] == "Male") $gender = 1; else $gender = 2;
				/* $Sql = "INSERT INTO customer_user SET firstname='".$cell['0']."', lastname='".$cell['1']."', dob_year='".$cell['2']."', dob_month='".$cell['3']."', 
						dob_day='".$cell['4']."', gender='$gender', emailaddress='".$cell['6']."', password='".md5($password)."', registered_date=Now(), active='1'";
				//echo $Sql."<hr>";		
				$this->Conn->Execute($Sql); */
				$this->Conn->Execute("INSERT INTO customer_user SET firstname='?', lastname='?', dob_year='?', dob_month='?', 
						dob_day='?', gender='$gender', emailaddress='?', password='?', registered_date=Now(), active='1'",array($cell['0'],$cell['1'],$cell['2'],$cell['3'],$cell['4'],$cell['6'],md5($password)));
                                
                $user_id = $this->Conn->Insert_ID();
				/* $Sql = "INSERT INTO merchant_subscribs SET merchant_id='$_SESSION[merchant_id]', user_id='$user_id', group_id='$group_id'";
				$this->Conn->Execute($Sql); */
				$this->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id='$_SESSION[merchant_id]', user_id='$user_id', group_id='$group_id'");
				
				$mail = new PHPMailer();
				$body = "<p>You have been added to Scan Flip by $_SESSION[merchant_info][firstname] $_SESSION[merchant_info][lastname]."; 
				$body .= "In order to see the $_SESSION[merchant_info][firstname] $_SESSION[merchant_info][lastname]'s deals, please login to your Scan Flip account at ".WEB_PATH." </p>";
				$body .= "<p>Your Username is: ".$cell['6']."</p>";
				$body .= "<p>Your Password: $password</p>";

				$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
				$mail->AddAddress($cell['6']);
				$mail->From = "no-reply@scanflip.com";
				$mail->FromName = "ScanFlip Support";
				$mail->Subject    = "ScanFlip Subscription";
				$mail->MsgHTML($body);
				$mail->Send();
				
			}
			
			
		}

	}
	function get_location_rating($locid)
	{
		$Sql = "select * from locations where id=".$locid;
          
        $RSlocation_rating =$this->Conn->Execute($Sql);
		
		$rating_title ="Not Yet Rated";
		$class =  "full-gray";
		if( $RSlocation_rating->fields['avarage_rating'] < 0 && $RSlocation_rating->fields['avarage_rating']  < 1)
		{
		   // echo "in .5";
			$class =  "orange-half";
			$rating_title = "Poor";
		}
		else if ($RSlocation_rating->fields['avarage_rating'] >= 1 && $RSlocation_rating->fields['avarage_rating'] <= 1.74) {
		   // echo "in 1";
			  $class =  "orange-one";
			  $rating_title = "Poor";
		}
		else if ($RSlocation_rating->fields['avarage_rating'] >= 1.75 && $RSlocation_rating->fields['avarage_rating'] <= 2.24) {
		   // echo "2";
			  $class =  "orange-two";
			  $rating_title = "Fair";
		}
		else if ($RSlocation_rating->fields['avarage_rating'] >= 2.25 && $RSlocation_rating->fields['avarage_rating'] <= 2.74) {
			//echo "2,5";
			  $class =  "orange-two_h";
			  $rating_title = "Good";
		}
		else if ($RSlocation_rating->fields['avarage_rating'] >= 2.75 && $RSlocation_rating->fields['avarage_rating'] <= 3.24) {
			//echo "3";
			  $class =  "orange-three";
			  $rating_title = "Good";
		}
		else if ($RSlocation_rating->fields['avarage_rating'] >= 3.25 && $RSlocation_rating->fields['avarage_rating'] <= 3.74) {
		  //  echo "3.5";
			  $class =  "orange-three_h";
			  $rating_title = "Very Good";
		}
		else if ($RSlocation_rating->fields['avarage_rating'] >= 3.75 && $RSlocation_rating->fields['avarage_rating'] <= 4.24) {
		   // echo "4";
			  $class =  "orange-four";
			  $rating_title = "Very Good";
		}
		else if ($RSlocation_rating->fields['avarage_rating'] >= 4.25 && $RSlocation_rating->fields['avarage_rating'] <= 4.74) {
		//  echo "4.5";
			  $class =  "orange-four_h";
			  $rating_title = "Excellent";
		}
		else if($RSlocation_rating->fields['avarage_rating'] >= 4.75) {
		   // echo "5";
			  $class =  "orange";
			  $rating_title = "Excellent";
		}
		
		$businessname="";
		//$businessname.='<div class="restaurantOrderingInfoRatings yelpRating">';
		$businessname.='<div class="ratinn_box '.$class.'" title="'.$rating_title.'" >';
		//$businessname.='<meta itemprop="ratingValue" content="'.$RSlocation_rating->fields['avarage_rating'].'">'; 

		$businessname.='</div>';
		/*
		$businessname.='<div class="cust_attr_tooltip" style="display: none; left: 91px; top: 11px;">';
		$businessname.='						<div class="arrow-down" style="top:105%"></div>';
		$businessname.='						<span id="star_tooltip">'.$rating_title.'</span>';
		$businessname.='</div>';
		*/
		//$businessname.='</div>';
		
		return $businessname;
		
	}
	/******** location rewriting ***********************/
	function location_url($location_id , $display_name) 
	{
		$url_strung = WEB_PATH."/location/".$display_name."/".$location_id;
		return $url_strung ;
	}
	function campaign_url($campaign_id ,$location_id ,$display_name) 
	{
		$url_strung = WEB_PATH."/campaign/".$display_name."/".$campaign_id."/".$location_id;
		return $url_strung ;
	}
	function activate_new_deal_mobile($customer_id = "", $activation_code , $cid , $lid)
	{
		$json_array = array();
		if($customer_id == "")
		{
			$json_array = array();
			$json_array['error_msg'] = "Invalid Customer ID";
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			return $json;
		}
		if($cid !=  0)
		{
			/* $Sql = "SELECT * FROM activation_codes WHERE activation_code='$activation_code' and campaign_id=".$cid;
			$RS = $this->Conn->Execute($Sql); */
			$RS = $this->Conn->Execute("SELECT * FROM activation_codes WHERE activation_code='$activation_code' and campaign_id=?",array($cid));
			if($RS->RecordCount()<=0)
			{
				$json_array['status'] = "false";
				$json_array['message'] = "Please enter valid activation code";
				$json_array['error_msg'] = "Please enter valid activation code";
				$_SESSION['msg'] = "Please enter valid activation code";
				$json = json_encode($json_array);
				return $json;
			}
		}
		else
		{
			/* $Sql = "SELECT * FROM activation_codes WHERE activation_code='$activation_code'";
			$RS = $this->Conn->Execute($Sql); */
			$RS = $this->Conn->Execute("SELECT * FROM activation_codes WHERE activation_code='$activation_code'");
			if($RS->RecordCount()<=0)
			{
				$json_array['status'] = "false";
				$json_array['message'] = "Please enter valid activation code";
				$json_array['error_msg'] = "Please enter valid activation code";
				$_SESSION['msg'] = "Please enter valid activation code";
				$json = json_encode($json_array);
				return $json;
			}
		}

		$campaign_id = $RS->fields['campaign_id'];
		if($lid == 0)
		{
			/* $Sql = "select * from campaign_location where campaign_id = ".$campaign_id;
			$RS_loc = $this->Conn->Execute($Sql); */
			$RS_loc = $this->Conn->Execute("select * from campaign_location where campaign_id = ?",array($campaign_id));
			$lid = $RS_loc->fields['location_id'];
		}
		/* $sql_o = "select * from campaign_location where campaign_id =".$campaign_id." and location_id =". $lid ." and active=1";
		$RS_o = $this->Conn->Execute($sql_o); */
		$RS_o = $this->Conn->Execute("select * from campaign_location where campaign_id = ? and location_id =? and active=1",array($campaign_id,$lid));
		
		/* $Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=".$lid;
		$RS = $this->Conn->Execute($Sql); */
		$RS = $this->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=?",array($lid));
		
		if($RS_o->RecordCount()>0)
		{
			if($RS->RecordCount()<=0)
			{
				/* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
				$RS_num_activation = $this->Conn->Execute($Sql_num_activation); */
				$RS_num_activation = $this->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?",array($campaign_id,$lid));
				
				$offers_left = $RS_num_activation->fields['offers_left'];
				$used_campaign = $RS_num_activation->fields['used_offers'];
				$share_flag= 1;
				if($offers_left > 0)
				{
					/* $Sql_max_is_walkin = "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=".$campaign_id;
					$RS_max_is_walkin = $this->Conn->Execute( $Sql_max_is_walkin); */
					$RS_max_is_walkin = $this->Conn->Execute("SELECT is_walkin , level ,new_customer  from campaigns WHERE id=?",array($campaign_id));
					if($RS_max_is_walkin->fields['new_customer'] == 1)
					{
						/*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$customer_id." and location_id=".$lid.") ";
						$subscibed_store_rs =$this->Conn->Execute($sql_chk); */
						$subscibed_store_rs = $this->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($customer_id,$lid));
						if($subscibed_store_rs->RecordCount()==0)
						{
							$share_flag= 1;
						}
						else
						{
							$share_flag= 0;
						}
					}
					$allow_for_reserve= 0;
					$is_new_user= 0;
					
					/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$customer_id." and location_id=".$lid.") ";
					$Rs_is_new_customer=$this->Conn->Execute($sql_chk); */
					$Rs_is_new_customer = $this->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($customer_id,$lid));
					
					if($Rs_is_new_customer->RecordCount()==0)
					{
						$is_new_user= 1;
					}
					else
					{
						$is_new_user= 0;
					}
					if($is_new_user==0)  
					{
						/* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;
						$RS_campaign_groups = $this->Conn->Execute($sql); */
						$RS_campaign_groups = $this->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$lid));
						$c_g_str = "";
						$cnt =1;

						$is_it_in_group = 0;
						if($RS_max_is_walkin->fields['level'] == 0)
						{
							if($RS_max_is_walkin->fields['is_walkin'] == 0)
							{ 
								if($RS_campaign_groups->RecordCount()>0)
								{
									while($Row_campaign = $RS_campaign_groups->FetchRow())
									{ 
										$c_g_str = $Row_campaign['group_id'];
										if($cnt != $RS_campaign_groups->RecordCount())
										{
											$c_g_str .= "," ;
										}
									}
									/* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$customer_id."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
									$RS_check_s = $this->Conn->Execute($Sql_new_); */ 
									$RS_check_s = $this->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?))",array($customer_id,$c_g_str));          
									
									while($Row_Check_Cust_group = $RS_check_s->FetchRow())
									{
										/* $query = "Select * from merchant_subscribs where  user_id='".$customer_id."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
										$RS_query = $this->Conn->Execute($query); */
										$RS_query = $this->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id=? AND group_id in(?)",array($customer_id,$Row_Check_Cust_group['group_id'],$c_g_str));
										if($RS_query->RecordCount() > 0)
										{
											$is_it_in_group = 1;
										}
									}
									if($is_it_in_group == 1 )
									{ 
										$allow_for_reserve = 1;  	
									}
									else
									{
										$allow_for_reserve = 0;
									}
								}
								else
								{
									$allow_for_reserve = 0;  	
								}
							}
							else
							{
								/* $query = "Select * from merchant_subscribs where  user_id=".$customer_id." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
								$RS_all_user_group = $this->Conn->Execute($query); */
								$RS_all_user_group = $this->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =1 )",array($customer_id,$lid));
								
								if($RS_all_user_group->RecordCount() > 0)
								{
									$allow_for_reserve = 1;
								}
								else
								{
									$allow_for_reserve = 0;  
								}
							}
						}
						else
						{
							$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$customer_id."' AND group_id in( select  id from merchant_groups where location_id  =".$lid."  )";
							$allow_for_reserve= 1;
						}
					}
					else
					{
						$allow_for_reserve= 1; 
					}
					if($share_flag== 1)
					{
						if($allow_for_reserve==1)
						{
							/* $Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
							customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
							$this->Conn->Execute($Sql); */
							$this->Conn->Execute("INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
									customer_id='$customer_id', campaign_id='$campaign_id' , location_id=?",array($lid));
							
							/* $update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$lid." ";
							$this->Conn->Execute($update_num_activation); */
							$this->Conn->Execute("Update  campaign_location set offers_left=?, used_offers=? where campaign_id=? and location_id =?",array(($offers_left-1),($used_campaign+1),$campaign_id,$lid));
							
							$RSLocation_nm  = $this->Conn->Execute("select * from locations where id =?",array($lid));
							
							//$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$lid;
							$br = $this->generate_voucher_code($customer_id,$activation_code,$campaign_id,$RSLocation_nm->fields['location_name'],$lid);
							$json_array['campaign_id'] = $campaign_id;
							$json_array['location_id'] = $lid;
							$json_array['barcode'] = $br;

							/* $select_coupon_code = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
							$select_rs = $this->Conn->Execute($select_coupon_code); */
							$select_rs = $this->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?",array($customer_id,$campaign_id,$lid));
							
							if($select_rs->RecordCount()<=0)
							{
								$array_ =array();
								$array_['customer_id'] = $customer_id;
								$array_['customer_campaign_code'] = $campaign_id;
								$array_['coupon_code'] = $br;
								$array_['active']=1;
								$array_['location_id'] = $lid;
								$array_['generated_date'] = date('Y-m-d H:i:s');
								/* $insert_coupon_code = "Insert into coupon_codes set customer_id=".$customer_id." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$lid." , generated_date='".date('Y-m-d H:i:s')."' ";
								$this->Conn->Execute($insert_coupon_code); */
								$this->Conn->Execute("Insert into coupon_codes set customer_id=?, customer_campaign_code=?, coupon_code=?, active=1 , location_id=? , generated_date='?'",array($customer_id,$campaign_id,$br,$lid,date('Y-m-d H:i:s')));   
							}

							/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
							$RS_group = $this->Conn->Execute($sql_group); */
							$RS_group = $this->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($lid));

							/* $sql_chk ="select * from subscribed_stores where customer_id= ".$customer_id." and location_id=".$lid;
							$subscibed_store_rs =$this->Conn->Execute($sql_chk); */
							$subscibed_store_rs = $this->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?",array($customer_id,$lid));
							
							if($subscibed_store_rs->RecordCount()==0)
							{
								/* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$customer_id." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
								$this->Conn->Execute($insert_subscribed_store_sql); */
								$this->Conn->Execute("insert into subscribed_stores set customer_id= ?,location_id=?,subscribed_date=?,subscribed_status=1",array($customer_id,$lid,date('Y-m-d H:i:s')));
							}
							else
							{
								if($subscibed_store_rs->fields['subscribed_status']==0)
								{
									/* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$customer_id." and location_id=".$lid;
									$this->Conn->Execute($up_subscribed_store); */
									$this->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id=? and location_id=?",array($customer_id,$lid));
								}
							}

						/* $RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$customer_id;
						$check_subscribe = $this->Conn->Execute($RS_user_subscribe); */
						$check_subscribe = $this->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ? and private = 1) and user_id = ?",array($lid,$customer_id));
							if($check_subscribe->RecordCount()==0)
							{
								/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
								$RS_group = $this->Conn->Execute($sql_group); */
								$RS_group = $this->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($lid));
								
								if($RS_group->RecordCount()>0)
								{
									/* $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;
									$RS_user_group =$this->Conn->Execute($sql_user_group); */
									$RS_user_group = $this->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));
									
									if($RS_user_group->RecordCount()<=0)
									{
										/* $insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$customer_id;
										$this->Conn->Execute($insert_sql); */
										$this->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =?, group_id =?, user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));
									}
								}
							}
						}
						else
						{
							$json_array['status'] = "newuser";
							$json_array['campaign_for_new_user'] = "Sorry this offer is available to limited customers. Please check other offers from merchant";
							$json_array['error_msg'] = "Sorry this offer is available to limited customers. Please check other offers from merchant";
							$_SESSION['campaign_for_new_user'] = "Sorry this offer is available to limited customers. Please check other offers from merchant";
							$json = json_encode($json_array);
							return $json;
						}
					}
					else
					{
						$json_array['status'] = "newuser";
						$json_array['campaign_for_new_user'] = "This campaign is for new users only";
						$json_array['error_msg'] = "This campaign is for new users only";
						$_SESSION['campaign_for_new_user'] = "This is campaign for new users only";
						$json = json_encode($json_array);
						return $json;
					}
				}
				else
				{
					$json_array['status'] = "ended";
					$json_array['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired.";
					$json_array['error_msg'] = "All offers are reserved by other customers or campaign has expired.";
					$_SESSION['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired.";
					$json = json_encode($json_array);
					return $json;
				}
			}
			else
			{
				if($RS->fields['activation_status'] == 0)
				{
					/* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
					$RS_num_activation = $this->Conn->Execute($Sql_num_activation); */
					$RS_num_activation = $this->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?",array($campaign_id,$lid));
					
					$offers_left = $RS_num_activation->fields['offers_left'];
					$used_campaign = $RS_num_activation->fields['used_offers'];
					$share_flag= 1;
					if($offers_left > 0)
					{
						/* $Sql_max_is_walkin = "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=".$campaign_id;
						$RS_max_is_walkin = $this->Conn->Execute( $Sql_max_is_walkin); */
						$RS_max_is_walkin = $this->Conn->Execute("SELECT is_walkin , level ,new_customer  from campaigns WHERE id=?",array($campaign_id));
						if($RS_max_is_walkin->fields['new_customer'] == 1)
						{
							/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$customer_id." and location_id=".$lid.") ";
							$subscibed_store_rs =$this->Conn->Execute($sql_chk); */
							$subscibed_store_rs = $this->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($customer_id,$lid));
							
							if($subscibed_store_rs->RecordCount()==0)
							{
								$share_flag= 1;
							}
							else 
							{
								$share_flag= 0;
							}
						}

						$allow_for_reserve= 0;
						$is_new_user= 0;
						/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$customer_id." and location_id=".$lid.") ";
						$Rs_is_new_customer=$this->Conn->Execute($sql_chk); */
						$Rs_is_new_customer = $this->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($customer_id,$lid));
						
						if($Rs_is_new_customer->RecordCount()==0)
						{
							$is_new_user= 1;
						}
						else
						{
							$is_new_user= 0;
						}

						if($is_new_user==0)  
						{
						
							/* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;
							$RS_campaign_groups = $this->Conn->Execute($sql); */
							$RS_campaign_groups = $this->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$lid));
							
							$c_g_str = "";
							$cnt =1;

							$is_it_in_group = 0;
							if($RS_max_is_walkin->fields['level'] == 0)
							{
								if($RS_max_is_walkin->fields['is_walkin'] == 0)
								{ 
									if($RS_campaign_groups->RecordCount()>0)
									{
										while($Row_campaign = $RS_campaign_groups->FetchRow())
										{ 
											$c_g_str = $Row_campaign['group_id'];
											if($cnt != $RS_campaign_groups->RecordCount())
											{
												$c_g_str .= "," ;
											}
										}
										$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$customer_id."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
										$RS_check_s = $this->Conn->Execute($Sql_new_); 
										$RS_check_s = $this->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?))",array($customer_id,$c_g_str));          
										
										while($Row_Check_Cust_group = $RS_check_s->FetchRow())
										{
											/* $query = "Select * from merchant_subscribs where  user_id='".$customer_id."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
											$RS_query = $this->Conn->Execute($query); */
											$RS_query = $this->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id=? AND group_id in(?)",array($customer_id,$Row_Check_Cust_group['group_id'],$c_g_str));
											
											if($RS_query->RecordCount() > 0)
											{
												$is_it_in_group = 1;
											}
										}
										if($is_it_in_group == 1 )
										{ 
											$allow_for_reserve = 1;  	
										}
										else
										{
											$allow_for_reserve = 0;
										}
									}
									else
									{
										$allow_for_reserve = 0;  	
									}

								}
								else
								{
									/* $query = "Select * from merchant_subscribs where  user_id=".$customer_id." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
									$RS_all_user_group = $this->Conn->Execute($query); */
									$RS_all_user_group = $this->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =1 )",array($customer_id,$lid));
									
									if($RS_all_user_group->RecordCount() > 0)
									{
										$allow_for_reserve = 1;
									}
									else
									{
										$allow_for_reserve = 0;  
									}
								}
							}
							else
							{
								$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$customer_id."' AND group_id in( select  id from merchant_groups where location_id  =".$lid."  )";
								$allow_for_reserve= 1;
							}
						}
						else
						{
							$allow_for_reserve= 1; 
						}
						if($share_flag== 1)
						{
							if($allow_for_reserve==1)
							{
								/* $Sql = "Update customer_campaigns SET activation_status=1 where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$lid;
								$this->Conn->Execute($Sql); */
								$this->Conn->Execute("Update customer_campaigns SET activation_status=1 where customer_id=? and campaign_id=? and location_id=?",array($customer_id,$campaign_id,$lid));
									
								$RSLocation_nm  = $this->Conn->Execute("select * from locations where id =?",array($lid));
								
								$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$lid;
								$json_array['campaign_id'] = $campaign_id;
								$json_array['location_id'] = $lid;
								$json_array['barcode'] = $br;

								/* $select_coupon_code = "update coupon_codes set active= 1 where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
								$select_rs = $this->Conn->Execute($select_coupon_code); */
								$select_rs = $this->Conn->Execute("update coupon_codes set active= 1 where customer_id=? and customer_campaign_code=? and location_id=?",array($customer_id,$campaign_id,$lid));

								/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
								$RS_group = $this->Conn->Execute($sql_group); */
								$RS_group = $this->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($lid));
								
								/* $sql_chk ="select * from subscribed_stores where customer_id= ".$customer_id." and location_id=".$lid;
								$subscibed_store_rs =$this->Conn->Execute($sql_chk); */
								$subscibed_store_rs = $this->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?",array($customer_id,$lid));
								
								if($subscibed_store_rs->RecordCount()==0)
								{
									/* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$customer_id." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
									$this->Conn->Execute($insert_subscribed_store_sql); */
									$this->Conn->Execute("insert into subscribed_stores set customer_id= ?,location_id=?,subscribed_date=?,subscribed_status=1",array($customer_id,$lid,date('Y-m-d H:i:s')));
								}
								else
								{
									if($subscibed_store_rs->fields['subscribed_status']==0)
									{
										/* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$customer_id." and location_id=".$lid;
										$this->Conn->Execute($up_subscribed_store); */
										$this->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id=? and location_id=?",array($customer_id,$lid));
									}
								}

								/* $RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$customer_id;
								$check_subscribe = $this->Conn->Execute($RS_user_subscribe); */
								$check_subscribe = $this->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ? and private = 1) and user_id = ?",array($lid,$customer_id));
								
								if($check_subscribe->RecordCount()==0)
								{
									/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
									$RS_group = $this->Conn->Execute($sql_group); */
									$RS_group = $this->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($lid));
								
									if($RS_group->RecordCount()>0)
									{
										/* $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;
										$RS_user_group =$this->Conn->Execute($sql_user_group); */
										$RS_user_group = $this->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));
										
										if($RS_user_group->RecordCount()<=0)
										{
											/* $insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$customer_id;
											$this->Conn->Execute($insert_sql); */
											$this->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =?, group_id =?, user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));
											
										}
									}
								}
							}
							else
							{
								$json_array['status'] = "newuser";
								$json_array['campaign_for_new_user'] = "Sorry this offer is available to limited customers. Please check other offers from merchant";
								$json_array['error_msg'] = "Sorry this offer is available to limited customers. Please check other offers from merchant";
								$_SESSION['campaign_for_new_user'] = "Sorry this offer is available to limited customers. Please check other offers from merchant";
								$json = json_encode($json_array);
								return $json;
							}
						}
						else
						{
							$json_array['status'] = "newuser";
							$json_array['campaign_for_new_user'] = "This campaign is for new users only";
							$json_array['error_msg'] = "This campaign is for new users only";
							$_SESSION['campaign_for_new_user'] = "This campaign is for new users only";
							$json = json_encode($json_array);
							return $json;
						}
					}
					else
					{
						$json_array['status'] = "ended";
						$json_array['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired.";
						$json_array['error_msg'] = "All offers are reserved by other customers or campaign has expired.";
						$_SESSION['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired.";
						$json = json_encode($json_array);
						return $json;
					}
				}
				$json_array['campaign_id'] = $campaign_id;
				$json_array['location_id'] = $lid;
				$json_array['status'] = "true";
				$json = json_encode($json_array);
				return $json;
			}
		}
		else
		{
			$json_array['status'] = "ended";
			$json_array['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired";
			$json_array['error_msg'] = "All offers are reserved by other customers or campaign has expired";
			$_SESSION['campaign_end_message'] = "All offers are reserved by other customers or campaign has expired";
			$json = json_encode($json_array);
			return $json;
		}
		$json_array['campaign_id'] = $campaign_id;
		$json_array['location_id'] = $lid;
		$json_array['status'] = "true";
		$json = json_encode($json_array);
		return $json;	
	}
	function create_camapign_url($campaign_id,$location_id,$title,$business,$category_id)
{

	$special_characters = array("+"," ","!","@","/","_","=","|","#","$","%","^","&","*","(",")","?","<",">","{","}","[","]",",",";",".",":",'"','\\');
	$sql_user_category = "select * from categories where id=".$category_id;
	$RS_user_category =$this->Conn->Execute($sql_user_category);
	while($Row_category = $RS_user_category->FetchRow())
	{ 
		$category_name = $Row_category['cat_name'];
	}
	
	$return_string = WEB_PATH."/".$category_name."/";
	$str_cat = str_replace(" ","",$category_name);
	$str_cat = str_replace($special_characters,"-",$str_cat);
	$str_cat = trim(preg_replace('/-+/', '-', $str_cat ), '-');
	$return_string = WEB_PATH."/".$str_cat."/";
	//echo "<br/>category".strlen($return_string);
	$str_busi = $business;
	$str_busi = str_replace("'","",$str_busi);
	$str_busi = str_replace($special_characters,"-",$str_busi);
	$str_busi = trim(preg_replace('/-+/', '-', $str_busi ), '-');
	$return_string = $return_string.$str_busi;
	//echo "<br/>business".strlen($str_busi);
	$url_string =$title;

	$url_string =  str_replace($special_characters,"-",$url_string);
	$url_string = str_replace("'","",$url_string);
	$url_string = trim(preg_replace('/-+/', '-', $url_string ), '-');
	$str_arr = explode("-",$url_string);
	$count_lenght = 0;
	$connected_string  = "";
	for($i=0;$i<count($str_arr);$i++)
	{
		if( strlen($str_arr[$i]) == 0)
		{
			$count_lenght = $count_lenght+1;
			//echo  strlen($str_arr[$i])."-".$count_lenght;
		}
		else{

			$count_lenght = $count_lenght+ strlen($str_arr[$i]);
			//echo  strlen($str_arr[$i])."-".$count_lenght;
		}


		if($count_lenght <= 50 )
		{

			$connected_string = $connected_string.$str_arr[$i]."-";
			$count_lenght= $count_lenght+1;
			//echo "<br/><<<".$connected_string.">>>>>".strlen($connected_string)."==";
		} 
	}
	
	$return_string = $return_string.$connected_string.base64_encode($campaign_id)."-".base64_encode($location_id).".html";
	//echo "<br/>campaign string".strlen($connected_string);
 return $return_string;
	//$url_string = WEB_PATH."/"
} 
function create_location_url($location_id,$title,$city)
{

	$special_characters = array("+"," ","!","@","/","_","=","|","#","$","%","^","&","*","(",")","?","<",">","{","}","[","]",",",";",".",":",'"','\\');
	$url_string =$title;
	$url_string =  str_replace($special_characters,"-",$url_string);
	$url_string = str_replace("'","",$url_string);
	$url_string = trim(preg_replace('/-+/', '-', $url_string ), '-');
	
	$url_string1 =$city;
	$url_string1 =  str_replace($special_characters,"-",$url_string1);
	$url_string1 = str_replace("'","",$url_string1);
	$url_string1 = trim(preg_replace('/-+/', '-', $url_string1 ), '-');

	$return_string = WEB_PATH."/location/".$url_string."-".$url_string1."-".base64_encode($location_id).".html";
	//echo "<br/>campaign string".strlen($connected_string);
 return $return_string;
	//$url_string = WEB_PATH."/"
} 

	function generate_voucher_code($customer_id,$activation_code,$campaign_id,$RSLocation_nm,$lid)
	{
		//$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm,0,2).$lid;
		//$br = $customer_id.substr($activation_code,0,2).$lid.substr($RSLocation_nm,0,2);		
		
		do
		{
			$key = '';
			$num_arr = array(1,2,3,4,5,6,7,8,9);
			$char_arr = array("A","B","C","D","E","F","G","H","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y","Z");
			//$keys = array_merge(range(0, 9), range('A', 'Z'));
			$keys = array_merge($num_arr, $char_arr);

			for ($i = 0; $i < 8; $i++) {
				$key .= $keys[array_rand($keys)];
			}

			/* $Sql = "SELECT * FROM coupon_codes WHERE customer_campaign_code='$campaign_id' and location_id='$lid' and coupon_code='$key'";
			$RS = $this->Conn->Execute($Sql); */
			$RS = $this->Conn->Execute("SELECT * FROM coupon_codes WHERE customer_campaign_code='$campaign_id' and location_id='$lid' and coupon_code='$key'");
	
		}while($RS->RecordCount()>0);
				
		return $key;
    
	}  
	
	
}

?>
