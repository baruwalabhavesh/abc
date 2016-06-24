<?php
/******** 
@USE : campaign detail page
@PARAMETER : 
@RETURN : 
@USED IN PAGES : activate-deal.php, activate.php, campaign_facebook.php, location_detail.php, my-deals.php, mymerchants.php, process.php, process_mobile.php, process_qrcode.php
*********/ 
//require_once("classes/Config.Inc.php");
//check_customer_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//include_once(SERVER_PATH."/classes/DB.php");
//session_start();
//$objDB = new DB();
//$objDBWrt = new DB('write');
//$objJSON = new JSON();
$cookie_time =  time() + 31536000;
$campaignlist_array = array();
//echo $_COOKIE['recently_viewed_campaigns_list1'];


$decode_campaign_id = base64_decode($_REQUEST['campaign_id']);
$decode_location_id = base64_decode($_REQUEST['l_id']); 

/*
$decode_campaign_id =  $_REQUEST['campaign_id'];
$decode_location_id = $_REQUEST['l_id'];
*/

$cust_in_same_group_of_camp=0;

if($_COOKIE['recently_viewed_campaigns_list1'] != "")
{
	$campaignlist_array = unserialize($_COOKIE['recently_viewed_campaigns_list1']);
}
$new_campaignlist_array = array();
array_unshift($new_campaignlist_array,($decode_campaign_id."-".$decode_location_id));

for($i=0;$i<count($campaignlist_array);$i++)
{
	if(count($new_campaignlist_array) <5)
	{
		if(($decode_campaign_id."-".$decode_location_id)!=$campaignlist_array[$i])   
		{
			
			array_push($new_campaignlist_array,$campaignlist_array[$i]);
		}
	}
}
$campaignlist_array = $new_campaignlist_array;
		
		// setcookie('recently_viewed_campaign', "", time()-36666);
		


$recently_viewed_campaignlist_string = serialize($campaignlist_array);


/* $redirect_url_sql = "Select permalink from campaign_location where  campaign_id=".$decode_campaign_id." and location_id=".$decode_location_id;
$redirect_url_data = $objDB->Conn->Execute($redirect_url_sql); */
$redirect_url_data = $objDB->Conn->Execute("Select permalink from campaign_location where campaign_id = ? and location_id = ?",array($decode_campaign_id,$decode_location_id));

$redirect_url = $redirect_url_data->fields['permalink'];
$r_r_url = $redirect_url_data->fields['permalink'];
//Start facebook sharing code
require LIBRARY.'/fb-sdk/src/facebook.php';
include_once(LIBRARY."/Mobile_Detect.php");
$medium=1;
$detect = new Mobile_Detect;

// 1. Check for mobile environment.
if ($detect->isMobile()) {
    // Your code here.
	$medium=2;
}
// 2. Check for tablet device.
if($detect->isTablet()){
    // Your code here.
	$medium=2;
}
/*$facebook = new Facebook(array(
  'appId'  => '654125114605525',
  'secret' => '2870b451a0e7d287f1899d0e401d3c4e',
  
));*/
//include_once(LIBRARY."/fb-sdk/src/facebook_secret.php");
$facebook = new Facebook(array(
  'appId'  => facebookappId,
  'secret' => facebooksecret,
  
));
 

 
// Get User ID
$user = $facebook->getUser();

//echo $user;  
if ($user != "" || $user != 0 ) {
    
  try {
	 	  $accessToken = $facebook->getAccessToken();
                 
		 $user_profiles = $facebook->api('me/accounts/pages');  
                 
                 $user_email=$facebook->api("/".$user."?fields=email");
                 $user_picture=$facebook->api("/".$user."?fields=picture");
                 
                 
                 //print_r($user_email);
                 //echo $user_email;
                 
                 $facebook->destroySession();
		 if (!empty( $accessToken )) {			
                    $_SESSION['accessTokenInSession']=$accessToken;
                    $_SESSION['facebookUserId'] =$user;
                   
                    /* $Sql_new = "select * from customer_user  where id=".$_SESSION['customer_id'];
					$RS_user_data = $objDB->Conn->Execute($Sql_new); */
					$RS_user_data = $objDB->Conn->Execute("select * from customer_user  where id= ?",array($_SESSION['customer_id']));
                    if($RS_user_data->fields['access_token'] != "")
                    {
                        
                    }
                    else
                    {
                        
                        if($user_email['email'] == $RS_user_data->fields['emailaddress'])
                        {
                            /* $sql="UPDATE customer_user SET access_token='".$accessToken."',facebook_user_id='".$user."' WHERE id='".$_SESSION['customer_id']."'";
                            $objDB->Conn->Execute($sql); */
                            $objDBWrt->Conn->Execute("UPDATE customer_user SET access_token=?,facebook_user_id=? WHERE id=?",array($accessToken,$user,$_SESSION['customer_id']));
                        }
                        else
                        {
                            /* $sql="UPDATE customer_user SET access_token='".$accessToken."',facebook_user_id='".$user."',facebook_email_id='".$user_email['email']."' WHERE id='".$_SESSION['customer_id']."'";
                            $objDB->Conn->Execute($sql); */
                            $objDBWrt->Conn->Execute("UPDATE customer_user SET access_token=?,facebook_user_id=?,facebook_email_id=? WHERE id=?",array($accessToken,$user,$user_email['email'],$_SESSION['customer_id']));
                        }
                        
                        if($RS_user_data->fields['profile_pic'] == "")
                        {
                            
                            if($user_picture['picture']['data']['url'] == "https://fbcdn-profile-a.akamaihd.net/static-ak/rsrc.php/v2/yo/r/UlIqmHJn-SK.gif")
                            {
                                
                            }
                            else
                            {
                                $user_pic="https://graph.facebook.com/".$user."/picture";
                                /* $sql_progile_image="UPDATE customer_user SET profile_pic='".$user_pic."' WHERE id='".$_SESSION['customer_id']."'";
                                $objDB->Conn->Execute($sql_progile_image); */
                                $objDBWrt->Conn->Execute("UPDATE customer_user SET profile_pic=? WHERE id=?",array($user_pic,$_SESSION['customer_id']));
                            }
                        }
                      //echo $sql; 
                      
                       
                      
                    }
                  // }

                    
	 	 } else {
                    $status = 'No access token recieved';
	 	 }
 	 } catch (FacebookApiException $e) {
                error_log($e); 				
	 }
}
else
{

   if(isset($redirect_url))
    {
		$params = array(
                    'redirect_uri'=>$redirect_url,
                    'scope'=>'publish_actions,publish_stream,read_stream,email'
               );
        $loginUrl = $facebook->getLoginUrl($params);
	}
	else
	{ 
		$params = array(
                    'redirect_uri'=>$redirect_url,
                    'scope'=>'publish_actions,publish_stream,read_stream,email'
               );
        $loginUrl = $facebook->getLoginUrl($params);
	}

}

//End Of facebook sharing code

//Twitter sharing code Function 
require_once(LIBRARY.'/twitteroauth/twitteroauth.php');

//include_once(LIBRARY."/twitteroauth/twitter_secret.php");






if(isset($_REQUEST['oauth_token']))
{
 //echo $_SESSION['request_token']."and ".$_SESSION['request_token_secret'];
 
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['request_token'], $_SESSION['request_token_secret']);
    
    
    $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
        
    if($access_token)
    {
        $connection_a = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $params =array();
        $params['include_entities']='false';
        $content = $connection_a->get('account/verify_credentials',$params);
        if($content && isset($content->screen_name) && isset($content->name))
        {
            $_SESSION['name']=$content->name;
            $_SESSION['image']=$content->profile_image_url;
            $_SESSION['twitter_id']=$content->screen_name;
            
            //echo $content->name."</br>";
            //echo $content->profile_image_url."</br>";
            //echo $content->screen_name."</br>";
            
                    /* $Sql_new = "select * from customer_user  where id=".$_SESSION['customer_id'];
					$RS_user_data = $objDB->Conn->Execute($Sql_new); */
					$RS_user_data = $objDB->Conn->Execute("select * from customer_user  where id= ?",array($_SESSION['customer_id']));
                   // if($RS_user_data->fields['twitter_access_token'] != "")
                    //{
                        
                    //}
                    //else
                    //{
                        //if($user_email['email'] == $RS_user_data->fields['emailaddress'])
                        //{
                        //        $sql_twitter="UPDATE customer_user SET twitter_user_name='".$content->screen_name."',twitter_access_token='".$access_token['oauth_token']."',twitter_access_token_secret='".$access_token['oauth_token_secret']."',twitter_user_id='".$access_token['user_id']."' WHERE id='".$_SESSION['customer_id']."'";
                        //}
                        //else
                        //{
                          //  $sql="UPDATE customer_user SET access_token='".$accessToken."',facebook_user_id='".$user."',facebook_email_id='".$user_email['email']."' WHERE id='".$_SESSION['customer_id']."'";
                        //}
                      //echo $sql; 
                      
                       /* $sql_twitter="UPDATE customer_user SET twitter_user_name='".$content->screen_name."',twitter_access_token='".$access_token['oauth_token']."',twitter_access_token_secret='".$access_token['oauth_token_secret']."',twitter_user_id='".$access_token['user_id']."' WHERE id='".$_SESSION['customer_id']."'";
                       $objDB->Conn->Execute($sql_twitter); */
                       $objDBWrt->Conn->Execute("UPDATE customer_user SET twitter_user_name=?,twitter_access_token=?,twitter_access_token_secret=?,twitter_user_id=? WHERE id=?",array($content->screen_name,$access_token['oauth_token'],$access_token['oauth_token_secret'],$access_token['user_id'],$_SESSION['customer_id']));
                       
                       if($RS_user_data->fields['profile_pic'] == "")
                        {
                            //$user_pic="https://graph.facebook.com/".$user."/picture";
                            if($content->profile_image_url == "https://abs.twimg.com/sticky/default_profile_images/default_profile_1_normal.png")
                            {
                                
                            }
                            else
                            {
                                /* $sql_progile_image="UPDATE customer_user SET profile_pic='".$content->profile_image_url."' WHERE id='".$_SESSION['customer_id']."'";
                                $objDB->Conn->Execute($sql_progile_image); */
                                $objDBWrt->Conn->Execute("UPDATE customer_user SET profile_pic=? WHERE id=?",array($content->profile_image_url,$_SESSION['customer_id']));
                            }
                        }
                      
                    //}
           
 
        }
        else
        {
              // echo "<h4> Login Error </h4>";
        }
                    
    }
}
else
{
 
    //echo "<h4> Login Error </h4>";
}

//End of twitter sharing code


$fb_title ="";
$redirect_url="";
$summary ="";
$url ="";
$image = "";
$th_link = "";
$fb_campaign_tag="";
$sharepoint="";
//$text = "http://www.scanflip.com/Beauty-Spa/Best-Westerns-Plus-Cairn-Croft-Hotel50--off---Use-Promo-Code--GP33927--schedule-763-81.html";
//$text = preg_replace("/(^|\s+)(\S(\s+|$))+/", " ", $text);
///$text = trim(preg_replace('/-+/', '-', $text), '-');
//echo "@@@".$text."@@@@";

function create_unique_code()
{
    $code_length=8;
    //echo $alfa = "1AB2CD3EF4G5HI6JK7LM8N9OP10QRSTU".$campaign_id."VWXYZ";
    $alfa = "12345678910ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $code="";
    for($i = 0; $i < $code_length; $i ++) 
    {
      $code .= $alfa[rand(0, strlen($alfa)-1)];
    } 
    return $code;
}
$o_left=0;
//echo "session value".$_SESSION['count'];
//$_SESSION['count'] = 1;
unset($_SESSION['count']);
//$objDB = new DB();
//$objJSON = new JSON();
$JSON = $objJSON->get_compain_details($decode_campaign_id);
$RS = json_decode($JSON);
$insert_array = array();

 unset($_SESSION['count']);
if(isset($_COOKIE['is_scanflip_scan_qrcode']))
{ 
	if($_COOKIE['is_scanflip_scan_qrcode'] != "")
	{
		if(!isset($_COOKIE['scanflip_customer_id'])){
		$custid = 0;
		}
		else{
			$custid = $_COOKIE['scanflip_customer_id'];
		}
	$uniqueid = create_unique_code()."".strtotime(date("Y-m-d H:i:s"));
		$q_id = $_COOKIE['is_scanflip_scan_qrcode'];
		$ip_addr = $_SERVER['REMOTE_ADDR'];
	$geoplugin = unserialize( file_get_contents('https://www.geoplugin.net/php.gp?ip='.$ip_addr) );

	if ( is_numeric($geoplugin['geoplugin_latitude']) && is_numeric($geoplugin['geoplugin_longitude']) ) {

	$lat = $geoplugin['geoplugin_latitude'];
	$long = $geoplugin['geoplugin_longitude'];
	}

		$locationid = $decode_location_id;
	  $campaignid = $decode_campaign_id;
	  
		/* $Sql  = "SELECT * FROM locations  where id=".$decode_location_id;	
		$RS_location = $objDB->Conn->Execute($Sql); */
		
		$RS_location = $objDB->Conn->Execute("SELECT * FROM locations  where id= ?",array($decode_location_id));
		
		$timezone = $RS_location->fields['timezone'];
		
		/* $dt_sql  = "Select CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR('". $timezone."',1, POSITION(',' IN '". $timezone."')-1)) dte " ;
	   // echo $dt_sql;
		$RS_dt = $objDB->Conn->Execute($dt_sql); */
		
		$RS_dt = $objDB->Conn->Execute("Select CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(?,1, POSITION(',' IN ?)-1)) dte",array($timezone,$timezone));
		
	   // if($RS_location->RecordCount() != 0){
		$locationid= $RS_location->fields['id'];
		
		//check for campaign-qrcode scan unique status
		//if($custid == 0){
	 
		$cookie_time = time()+(20 * 365 * 24 * 60 * 60);
                $data =  array();
                if(isset($_COOKIE['scanflip_scan_qrcode']))
                {
		$data = unserialize($_COOKIE['scanflip_scan_qrcode']);
	
		$insert_array = array();
		if(count($data) != 0 ){

			if(array_key_exists($q_id,$data))   
			{
				$arr_new = array();
					$arr_new = explode("-",$data[$q_id]);
				if($arr_new[0]."-".$arr_new[1] == $campaignid."-".$locationid)
				{
				   $insert_array['is_unique']= 0;
					$arr = array();
					$arr = explode("-",$data[$q_id]);
					$uid =  $arr[2] ;
					if($custid != 0)
					{
						/* $sql = "select * from scan_qrcode where campaign_id=".$campaignid." and location_id=".$locationid." and qrcode_id=".$q_id." and user_id=".$custid." and unique_id='".$arr_new[2]."'";
						$RS_unique = $objDB->Conn->Execute($sql); */
						$RS_unique = $objDB->Conn->Execute("select * from scan_qrcode where campaign_id= ? and location_id=?  and qrcode_id= ? and user_id=? and unique_id=?",array($campaignid,$locationid,$q_id,$custid,$arr_new[2]));
						
						/* $sql= "update scan_qrcode set user_id=".$custid." where unique_id='".$arr_new[2]."' ";
						$objDB->Conn->Execute($sql); */
						$objDBWrt->Conn->Execute("update scan_qrcode set user_id=? where unique_id=?",array($custid,$arr_new[2]));
					}
				}else{
				
					$insert_array['is_unique']= 1;
					$data[$q_id] = $campaignid."-".$locationid."-".$uniqueid ;
					$uid = $uniqueid ;
				}
			}
			else{
			  
				$data[$q_id] = $campaignid."-".$locationid."-".$uniqueid ;
				$uid = $uniqueid ;
				$insert_array['is_unique']= 1;
			}
	   // }
		
	}
        }
	else{
	   
		$insert_array['is_unique']= 1;
		$data[$q_id] = $campaignid."-".$locationid."-".$uniqueid;
		$uid = $uniqueid ;
	}
	 
		setcookie('scanflip_scan_qrcode', serialize($data), $cookie_time);
	$serialized_data_to_send = serialize($data);
		
	   // $insert_array = array();
				
				$insert_array['qrcode_id']= $q_id;
				$insert_array['campaign_id']= $campaignid;
				$insert_array['location_id']= $locationid;
				$insert_array['is_location']= 0;
				$insert_array['is_superadmin']= 0;
				$insert_array['unique_id']= $uid;
				$insert_array['scaned_date']= $RS_dt->fields['dte'];
				$insert_array['user_id']=$custid ;
				$objDB->Insert($insert_array, "scan_qrcode");
			  
			  setcookie('is_scanflip_scan_qrcode', "", time()-36666);
                          setcookie('mycurrent_lati_qrcode', "", time()-36666);
                          setcookie('mycurrent_long_qrcode', "", time()-36666);
	}
}
$Sql  = "SELECT * FROM locations  where id=".$decode_location_id;
			
		$RS_location = $objDB->Conn->Execute($Sql);
        
//if($_SESSION['is_scanflip_scan_qrcode'] != "")
//{
//    if($_SESSION['scanflip_customer_id'] == ""){
//    $custid = 0;
//}
//else{
//    $custid = $_SESSION['scanflip_customer_id'];
//}
//$uniqueid = create_unique_code()."".strtotime(date("Y-m-d H:i:s"));
//    $q_id = $_SESSION['is_scanflip_scan_qrcode'];
//    $ip_addr = $_SERVER['REMOTE_ADDR'];
//$geoplugin = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip_addr) );
//
//if ( is_numeric($geoplugin['geoplugin_latitude']) && is_numeric($geoplugin['geoplugin_longitude']) ) {
//
//$lat = $geoplugin['geoplugin_latitude'];
//$long = $geoplugin['geoplugin_longitude'];
//}
//
//    $locationid = $decode_location_id;
//  $campaignid = $decode_campaign_id;
//    $Sql  = "SELECT * FROM locations  where id=".$decode_location_id;
//        
//    $RS_location = $objDB->Conn->Execute($Sql);
//    $timezone = $RS_location->fields['timezone'];
//    $dt_sql  = "SElect CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR('". $timezone."',1, POSITION(',' IN '". $timezone."')-1)) dte " ;
//   // echo $dt_sql;
//    $RS_dt = $objDB->Conn->Execute($dt_sql);
//   // if($RS_location->RecordCount() != 0){
//    $locationid= $RS_location->fields['id'];
//    
//    //check for campaign-qrcode scan unique status
//    //if($custid == 0){
//  // echo "In If";
//    $cookie_time = time()+(20 * 365 * 24 * 60 * 60);
//    $data = unserialize($_SESSION['scanflip_scan_qrcode']);
////    echo "<pre>";
////    print_r($data);
////    echo "</pre><br/>";
//    $insert_array = array();
//    if(count($data) != 0 ){
//
//        if(array_key_exists($q_id,$data))   
//        {
//            $arr_new = array();
//                $arr_new = explode("-",$data[$q_id]);
//            if($arr_new[0]."-".$arr_new[1] == $campaignid."-".$locationid)
//            {
//               $insert_array['is_unique']= 0;
//                $arr = array();
//                $arr = explode("-",$data[$q_id]);
//                $uid =  $arr[2] ;
//                if($custid != 0)
//                {
//                $sql = "select * from scan_qrcode where campaign_id=".$campaignid." and location_id=".$locationid." and qrcode_id=".$q_id." and user_id=".$custid." and unique_id='".$arr_new[2]."'";
//                        $RS_unique = $objDB->Conn->Execute($sql);
//                          $sql= "update scan_qrcode set user_id=".$custid." where unique_id='".$arr_new[2]."' ";
//                                $objDB->Conn->Execute($sql);
//                }
//            }else{
//             //   echo "== in else";
//                $insert_array['is_unique']= 1;
//                $data[$q_id] = $campaignid."-".$locationid."-".$uniqueid ;
//                $uid = $uniqueid ;
//            }
//        }
//        else{
//          //  echo "In else".$campaignid."-".$locationid;
//           // echo "In else";
//            $data[$q_id] = $campaignid."-".$locationid."-".$uniqueid ;
//            $uid = $uniqueid ;
//            $insert_array['is_unique']= 1;
//        }
//   // }
//    
//}
//else{
//   // echo "In main else".$campaignid."-".$locationid;
//    $insert_array['is_unique']= 1;
//    $data[$q_id] = $campaignid."-".$locationid."-".$uniqueid;
//    $uid = $uniqueid ;
//}
// //  echo $insert_array['is_unique'];
//  //  print_r($data);
//    $_SESSION['scanflip_scan_qrcode']= serialize($data);//, $cookie_time);
//
//    
//   // $insert_array = array();
//            $insert_array['qrcode_id']= $q_id;
//            $insert_array['campaign_id']= $campaignid;
//            $insert_array['location_id']= $locationid;
//            $insert_array['is_location']= 0;
//            $insert_array['is_superadmin']= 0;
//            $insert_array['unique_id']= $uid;
//            $insert_array['scaned_date']= $RS_dt->fields['dte'];
//            $insert_array['user_id']=$custid ;
//          $objDB->Insert($insert_array, "scan_qrcode");
//          unset($_SESSION['is_scanflip_scan_qrcode']);
////setcookie('is_scanflip_scan_qrcode', "", time()-36666);
//}
//$_COOKIE['is_scanflip_scan_qrcode']

?>
<?php
if(isset($_SESSION['customer_id'])){
$arr=file(WEB_PATH.'/process.php?campaign_counter=yes&customer_id='.$_SESSION['customer_id'].'&campaign_id='.$decode_campaign_id);
}
if(isset($_REQUEST['chk_for_share_campaign']))
{
	check_customer_session();
}

//if(isset($_REQUEST['btnActivationCode_'])){
//
//  
//    if(isset($_REQUEST['campid']))
//    {
//	$json = $objJSON->activate_new_deal("",$_REQUEST['activation_code'],$_REQUEST['campid'],$_REQUEST['loc_id']);
//
//    
//	$json = json_decode($json);
//        
//	if($json->status == "true"){
//		if($camp_id!="")
//		{                
//			
//		   header("Location: campaign.php?campaign_id=".$_REQUEST['campid']."&l_id=".$_REQUEST['loc_id']);
//			exit();
//		}
//	}
//	}
//
//}
?>
<?php 
if(isset($_SESSION['customer_id']))
{
$cst_id = $_SESSION['customer_id'];
}
else
{
$cst_id="";
}
if(isset($_REQUEST['customer_id'])){
	$objJSON->reward_user($_REQUEST['customer_id'], $decode_campaign_id);
}
// $array = $json_array = array();
//	$array['customer_id'] = 31;
//	$array['campaign_id'] = 43;
//	$array['referral_reward'] = 5;
//        $array['location_id'] = 18;
//        $array['referred_customer_id'] = 31;
//        $array['social_share'] = 1;
//	$array['reward_date'] = date("Y-m-d H:i:s");
//	$objDB->Insert($array, "reward_user");
$arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='.$decode_location_id);
                        if(trim($arr[0]) == "")
                             {
                                     unset($arr[0]);
                                     $arr = array_values($arr);
                             }
                             $json = json_decode($arr[0]);
                        $busines_name  = $json->bus_name;

//$objJSON->create_camapign_url($decode_campaign_id,$decode_location_id,$RS[0]->title,$busines_name,$RS[0]->category_id);
/*echo "<pre>";
print_r($_SERVER['SCRIPT_URI']);
echo "</pre>"; */

                        //echo $RSMer->fields['firstname'];
                        if($RS[0]->business_logo!="")
                        {
                            $img_src=ASSETS_IMG."/m/campaign/".$RS[0]->business_logo; 
                        }
                        else 
                        {
							 $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
                        }
						$tel_arr = explode("-",$RS_location->fields['phone_number']);

 array_shift($tel_arr);

$telephone_number = implode("-",$tel_arr);
?>

<!DOCTYPE HTML>
<html prefix="og: http://ogp.me/ns#" itemscope itemtype="https://schema.org/Offer/ScanflipOffer" itemid="<?php  echo $_SERVER['SCRIPT_URI']; ?>" lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="Cache-control" content="public,max-age=86400,must-revalidate">
<title> <?php echo "Scanflip Offer - ".$RS[0]->title; ?></title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<?php $title=htmlspecialchars(urlencode("Scanflip Offer - ".$RS[0]->title)); ?>

<meta name="title" itemprop="itemOffered" property="og:title" content="<?php echo htmlspecialchars(urldecode($title));?>">
<!--<meta   content="<?php// echo htmlspecialchars(urldecode($title));?>"> -->
<meta name="keywords" content="coupons,offers,offers of the day,local offers,savings,discounts,deals,local deals, <?php echo $RS[0]->campaign_tag; ?>">
<meta name="description" itemprop="description" property="og:description" content="<?php echo strip_tags($RS[0]->description);?>" > 
<meta name="owner" content="<?=$busines_name?>">
<meta property="fb:app_id" content="654125114605525">
<meta property="og:type" content="my_scanflip:my_business">
<meta property="og:site_name" content="www.scanflip.com">
<meta itemprop="url" property="og:url" content="<?php  echo $_SERVER['SCRIPT_URI']; ?>">
<meta itemprop="image" property="og:image" content="<?php echo $img_src; ?>">

<meta property="place:location:latitude" content="<?=$RS_location->fields['latitude'] ?>">
<meta property="place:location:longitude" content="<?=$RS_location->fields['longitude'] ?>">
<meta property="business:contact_data:street_address" content="<?=$RS_location->fields['address'] ?>">
<meta property="business:contact_data:locality" content="<?=$RS_location->fields['city'] ?>">
<meta property="business:contact_data:postal_code" content="<?=$RS_location->fields['zip'] ?>">
<meta property="business:contact_data:country_name" content="<?=$RS_location->fields['country'] ?>">
<meta property="business:contact_data:phone_number" content="<?=$telephone_number;?>">

    
    <!-- by vritika -->
<meta name="twitter:app:name:iphone" content="Scanflip"/>
<meta name="twitter:app:id:iphone" content="com.lanet.scanflip"/>
<!--<meta name="twitter:app:url:iphone" content="example://action/5149e249222f9e600a7540ef"/>-->
<meta name="twitter:app:name:ipad" content="Scanflip"/>
<meta name="twitter:app:id:ipad" content="com.lanet.scanflip"/>
<!--<meta name="twitter:app:url:ipad" content="example://action/5149e249222f9e600a7540ef"/>-->
<?php $var = $RS[0]->start_date;$var1 = $RS[0]->expiration_date;  ?>

<meta itemprop="availabilityStarts" content="<?php echo date('c',strtotime($var)); ?>">  
<meta itemprop="availabilityEnds" content="<?php echo date('c',strtotime($var1)); ?>">
<meta itemprop="validFrom" content="<?php echo date('c',strtotime($var)); ?>">
<meta itemprop="validThrough" content="<?php echo date('c',strtotime($var)); ?>">


<!-- Pending Tags-->
<!--Here is example markup to enable app install prompts and deep-linking, and 
note that this metadata can be appended to any card type. Additionally the App ID value 
for iPhone and iPad should be the numeric App Store ID, while the App ID for Google Play 
should be the Android package name. https://dev.twitter.com/docs/cards/markup-reference , https://dev.twitter.com/docs/cards -->
<!--<meta name="twitter:app:name:iphone" content="Scanflip App"/>
<meta name="twitter:app:id:iphone" content="APP Store ID"/>
<meta name="twitter:app:url:iphone" content="example://action/5149e249222f9e600a7540ef"/>
<meta name="twitter:app:name:googleplay" content="Scanflip App"/> 
<meta name="twitter:app:id:googleplay" content="com.lanet.scanflip"/>
<meta name="twitter:app:url:googleplay" content="http://example.com/action/5149e249222f9e600a7540ef"/>-->

<meta itemprop="availability" content="https://schema.org/InStoreOnly">
<!--<link rel="icon" href="path to /favicon.ico" type="x-icon">-->
<link rel="image_src" href="<?php echo $img_src; ?>"> 
<?php
$share="";
$th_link = WEB_PATH."/campaign_facebook.php?campaign_id=$_REQUEST[campaign_id]&l_id=$_REQUEST[l_id]&share=$share";
$img_src="";
if($RS[0]->business_logo!="")
{
    $img_src=ASSETS_IMG."/m/campaign/".$RS[0]->business_logo; 
}
else 
{
    $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
}
?>

<link href="<?=ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">
<script src="<?php echo ASSETS_JS ?>/c/jquery.js"></script>
<script type='text/javascript' src="https://maps.google.com/maps/api/js?sensor=true"></script>
<script type='text/javascript' src="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>
<link rel="alternate" href="android-app://com.lanet.scanflip/campaign/campaign_id=<?php echo $decode_campaign_id; ?>&location_id=<?php echo $decode_location_id; ?>" />
<style type="text/css">

    @media screen and (-webkit-min-device-pixel-ratio:0){
    .percetage a{width:300px!important;}}
/*@import "js/jquery.countdown.css";
#defaultCountdown {
	width: 240px;
	height: 22px;
	display: block;
	overflow: hidden;
}*/
</style>
<link href='https://fonts.googleapis.com/css?family=Crimson+Text:400,600' rel='stylesheet' type='text/css'>

<script type="text/javascript">
function formattedDate(date) {
    var d = new Date(date || Date.now()),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
	
	var h=d.getHours();
	var m=d.getMinutes();
	var s=d.getSeconds();
	
	if (h.length < 2) h = '0' + h;
    if (m.length < 2) m = '0' + m;
	if (s.length < 2) s = '0' + s;
	
    return [year,month,day ].join('-')+" "+[h,m,s ].join(':');
}
/*
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35713507-1']);
   var campaign_id = window.location.toString().split("=")[0];
   _gaq.push(['_setCustomVar', 2, 'campaigns', '<?php echo $RS[0]->title ?>' , 3]);
_gaq.push(['_setCustomVar', 1, 'Member','no', 1]);

		 _gaq.push(['_setCustomVar', 1, 'campaign_id','<?php echo $RS[0]->title ?>' , 1]);
		   _gaq.push(['_setCustomVar', 1, 'Member','<?php echo $RS[0]->title ?>', 1]);

  
  _gaq.push(['_trackPageview']);
_gaq.push(['_trackPageLoadTime']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
*/
</script>
</head>

<body class="campaignpage" onLoad="checkCookie()"  >
<?php
require_once(CUST_LAYOUT."/header.php");
?>
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/jquery.countdown.js"></script>
<div id="content" class="cantent">
	<div class="my_main_div">
		<div id="contentContainer" class="contentContainer">
	<?php 
	
	//$current_url1 = $_SERVER['REQUEST_URI'];
	/*echo "Current Request Location :<br>"; 
	echo $current_url1; 
	$p_path = $_SERVER['HTTP_REFERER'];
	echo "<br>Previous Location :";
	echo $_SERVER['HTTP_REFERER'];
	*/?>
    <?php
	/*
	if(isset($_COOKIE['mycurrent_lati']) && isset($_COOKIE['mycurrent_long']))
	{
	*/
	?>  
	<div class="dealDetailDiv">
        <div class="detail">
		
          <input type="hidden" name="is_profileset" id="is_profileset" value="" />
		  <input type="hidden" name="print_redirect_link" id="print_redirect_link" value="" />
          <input type="hidden" name="profile_view" id="profile_view" value="" />
		  <input type="hidden" name="hdn_is_walkin" id="hdn_is_walkin" value="<?php echo $RS[0]->is_walkin; ?>" />
			 
         
            <?php
				if(isset($_SESSION['customer_id']))
				{
					/* $Sql_new = "SELECT * FROM customer_campaigns WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$decode_campaign_id."' AND location_id =".$decode_location_id." and activation_status=1";
				    $RS_new = $objDB->Conn->Execute($Sql_new); */
				    $RS_new = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id= ? AND campaign_id=? AND location_id =? and activation_status=1",array($_SESSION['customer_id'],$decode_campaign_id,$decode_location_id));
				   
						if($RS_new->RecordCount()<=0){
							$no_rec = "inactive";
						}
					/* $Sql_new_coupon = "SELECT * FROM  coupon_codes where customer_campaign_code =".$decode_campaign_id." AND location_id=".$decode_location_id." and customer_id=".$_SESSION['customer_id']." and active=1";
					$RS_new_coupon = $objDB->Conn->Execute($Sql_new_coupon); */
					$RS_new_coupon = $objDB->Conn->Execute("SELECT * FROM coupon_codes WHERE customer_campaign_code= ? AND location_id=? AND customer_id =? and active=1",array($decode_campaign_id,$decode_location_id,$_SESSION['customer_id']));
						
					
				}
       
         ?>
            <table width="100%"  border="0" cellspacing="0" cellpadding="0" >
              <tr>
                <td align="left" valign="top"><?php 
        
       
		 ?>
                 <!-- <div id="dealslist">
                    <div class="offersexyellow">-->
                      <div id="campaign_detail offersexyellow">
                        
                        <div class="main-passport">
                          <div class="other_details">
						 <div class="percetage" itemscope itemtype="https://schema.org/Organization" itemprop="seller">
                          <?php 
                $where_clause = array();
		$where_clause['id'] = $decode_location_id;
		$RSLocation = $objDB->Show("locations", $where_clause);
                /*
                if($RSLocation->fields['location_name']!="")
                    echo "<a href='".WEB_PATH."/location_detail.php?id=".$RSLocation->fields['id']."'>".$RSLocation->fields['location_name']."</a>";
                elseif($busines_name!="")
                    echo $busines_name;
                else
                    echo "Scanflip Merchant";
                
                 */
				 
                //echo "<a href='".WEB_PATH."/location_detail.php?id=".$RSLocation->fields['id']."'>".$busines_name."</a>";
				//echo "<a href='javascript:void(0)' style='cursor:default;' >".$busines_name."</a>";
				echo "<span class='busness_tit' itemprop='name'>".$busines_name."</span>";
             ?>
                        </div>
                        <?php if($RS[0]->discount != ""){
                ?>
                       
                        <?php
            }?>
                            <div class="dealtitle1">
                              <?=$RS[0]->title?>
                            </div>
                            <? $camp_title = $RS[0]->discount."  ".$RS[0]->title;  ?>
                            <div class="button_wrapper" >
                                <div class="divoffersaveblock" >
                                    <h2 class="goh-offer-saved">Offer saved.</h2>
                                    <div class="goh-offer-app-download-title" >Download Scanflip App to redeem your offer.</div>
                                    <a target="_blank" class="download-app-button" href="#" >
                                        <span class="download-app-icon android">Android app</span>
                                    </a>
                                    <a target="_blank" href="#" class="download-app-button" >
                                        <span class="download-app-icon ios">iPhone app</span>
                                    </a>
                                    <a data-g-action="Close" class="goh-grey-close-icon" id="savable-bubble-promo-dismiss"></a>
                                    
                                </div>
                                <div class="divoffersaveblockonly" >
                                    <h2 class="goh-offer-saved"><?php echo $client_msg['campaign']['label_Offer_Saved'];?> <br/><a href="" id="print_link"><?php echo $client_msg['campaign']['label_Offer_Print'];?></a><?php echo $client_msg['campaign']['label_Offer_Print_After'];?></h2>
                                   
                                    
                                </div>
                              <?php
		 $where_clause = array();
		 $where_clause['customer_id'] = $cst_id;
		$where_clause['customer_campaign_code'] = $decode_campaign_id;
                $where_clause['location_id'] =$decode_location_id;
                
	$RS_cp = $objDB->Show("coupon_codes",$where_clause);
	
	if($RS_cp->RecordCount()>0){
	
	$barcodea = $RS_cp->fields['coupon_code'];
             
            //$barcodea = $cst_id.substr($CodeDetails->fields['activation_code'],0,3).$decode_campaign_id;
		//echo "if";
	}else{
        //echo "else";
	
                $where_clause = array();
		$where_clause['id'] = $decode_campaign_id;
		$RSCompDetails = $objDB->Show("campaigns", $where_clause);
                
                $where_x = array();
		$where_x['campaign_id'] = $decode_campaign_id;
		$CodeDetails = $objDB->Show("activation_codes", $where_x);
              
		$where_clause = array();
		
			    $where_clause = array();
		$where_clause['id'] = $decode_location_id;
		$RSLocation = $objDB->Show("locations", $where_clause);
                $avcivation_code = $CodeDetails->fields['activation_code'];
		         // b redeem_rewards
                //$barcodea = $RSLocation->fields['location_name'].$RSLocation->fields['zip'].$_SESSION['customer_id'].$_REQUEST[campaign_id];
		 //$barcodea = $cst_id.substr($CodeDetails->fields['activation_code'],0,2).$decode_campaign_id.substr($RSLocation->fields['location_name'],0,2).$decode_location_id;
		 $barcodea = $objJSON->generate_voucher_code($cst_id,$CodeDetails->fields['activation_code'],$decode_campaign_id,$RSLocation->fields['location_name'],$decode_location_id);	
                //echo $RSLocation->fields['location_name'];
               // echo $RSLocation->fields['zip'];
                //echo $_SESSION['customer_id'].$_REQUEST[campaign_id];
	
        

        }
       if(isset($_SESSION['customer_id']))
       {
           if($RS_new->RecordCount()>0)
           { ?>
                              <!--                <a class="reserve_print" href="print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>">                                    
					Print
									</a>-->
                              <?php
           }
       }
                ?>
                              <?php 
                 
                /* $location_max_sql = "Select num_activation_code , offers_left , active,permalink from campaign_location where  campaign_id=".$decode_campaign_id." and location_id=".$decode_location_id;
				$location_max = $objDB->Conn->Execute($location_max_sql); */
				$location_max = $objDB->Conn->Execute("Select num_activation_code , offers_left , active,permalink from campaign_location where  campaign_id= ? and location_id=?",array($decode_campaign_id,$decode_location_id));
				
           $max_coupon = $location_max->fields['num_activation_code'];
           $o_left = $location_max->fields['offers_left'];
           $is_active =  $location_max->fields['active'];
           
          // echo  $max_coupon."==";
						
						/* $remain_sql="select count(*) as total from coupon_codes where customer_campaign_code =".$decode_campaign_id." AND location_id=".$decode_location_id;
                        $RS_remain = $objDB->Conn->Execute($remain_sql); */
                        $RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes where customer_campaign_code = ? AND location_id=?",array($decode_campaign_id,$decode_location_id));
                        
                        $remain_val = $RS_remain->fields['total'];
                      //  echo  $remain_val."==";
                        $max_coupon = $max_coupon - $remain_val;
                         $is_new_user= 0;
                        ?>
                              <?php 
		
                            if(isset($_SESSION['customer_id']))
			    {
				
							$curdate = strtotime(date('Y-m-d H:i:s'));
							$expdate = strtotime($RS[0]->expiration_date);  
							if($expdate < $curdate)
							{
								echo "<span class='campaignexpirelbl'>".$client_msg['campaign']['Msg_Campaign_Is_Expired']."</span>";
							}
							else
							{
                                
                                // Starting //
                            /* */
                                      /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where  location_id=". $decode_location_id." and customer_id=".$_SESSION['customer_id'].") ";
									  //    echo "sql_check===".$sql_chk."<br/>";
                                      $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
                                      $Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where  location_id=? and customer_id= ?)",array($decode_location_id,$_SESSION['customer_id']));
                                      
                                      
                                            if($Rs_is_new_customer->RecordCount()==0)
                                            {
                                                $is_new_user= 1;
                                            }
                                            else {
                                                  $is_new_user= 0;
                                            }
                                            /* */
                                
                               /* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$decode_campaign_id." and cg.group_id=mg.id and mg.location_id=".$decode_location_id;
                               //$sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$decode_campaign_id." and cg.group_id=mg.id ";
                               //  echo "<br/>===".$sql."===<br />";
                               $RS_campaign_groups = $objDB->Conn->Execute($sql); */
                               
                               $RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id= ? and cg.group_id=mg.id and mg.location_id=?",array($decode_campaign_id,$decode_location_id));
                               
                                $c_g_str = "";
                                $cnt =1;
                              //  echo $RS_campaign_groups->RecordCount()."====";
                               if($is_new_user == 0)
                               {
                                 $is_it_in_group = 0;
                                if($RS[0]->level == 0)
                                { 
                                    if($RS_campaign_groups->RecordCount()>0)
                                        {
                                     while($Row_campaign = $RS_campaign_groups->FetchRow())
                                        { 
                                            $c_g_str .= $Row_campaign['group_id'];
                                            if($cnt != $RS_campaign_groups->RecordCount())
                                            {
                                                $c_g_str .= "," ;
                                            }
                                            $cnt++;
                                        }
                                       
										/* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
										// echo $Sql_new_;
										$RS_check_s = $objDB->Conn->Execute($Sql_new_); */
										
										$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id= ? AND group_id in( select  id from merchant_groups where id in(?))",array($_SESSION['customer_id'],$c_g_str));
										
                                        /* for checking whether customer in campaign group */

                                        while($Row_Check_Cust_group = $RS_check_s->FetchRow())
                                        {
                                            /* $query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
                                            $RS_query = $objDB->Conn->Execute($query); */
											$RS_query = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id= ?  and group_id=? AND group_id in( select  id from merchant_groups where id in(?))",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id'],$c_g_str));
											
                                             if($RS_query->RecordCount() > 0)
                                             {

                                                  $is_it_in_group = 1;
                                             }
                                        }
                                        if($is_it_in_group == 1 )
                                            { 
                                                  $cust_in_same_group_of_camp = 1;  	
                                            }
                                           else {
                                                $cust_in_same_group_of_camp = 0;
                                           }
                                        }
                                            else{
                                                $cust_in_same_group_of_camp = 1;  	
                                            }
                                } 
                                else
                                {
                                 //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                   $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$decode_location_id."  )";
                                   $cust_in_same_group_of_camp = 1;  	
                                }
                               }else{
                                    $cust_in_same_group_of_camp = 1;  
                               }
                                //
                               
                               
                               // echo "<br />SQl_new===".$Sql_new_ ."=====<br />";
                         
                            /* for checking whether customer in campaign group */
                                  
                                
								/* $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$decode_location_id."  )";
								// echo $Sql_new;
								$RS_check_s = $objDB->Conn->Execute($Sql_new); */
								
								$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id= ? AND group_id in( select  id from merchant_groups where location_id =?)",array($_SESSION['customer_id'],$decode_location_id));
								
                                $onepercust_flag = 1;
                                $oneperday_multi_flag = 1;
                                $share_flag = 1;
                                
                                  
                                $its_new_user =0;
                                   if($RS[0]->new_customer == 1)
                                          {
                                              /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=". $decode_location_id.") ";
                                              $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                              
                                              $RS_location = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=?  and location_id=?)",array($_SESSION['customer_id'],$decode_location_id));
                                              
                                              if($subscibed_store_rs->RecordCount()==0)
                                              {
                                                  $share_flag= 1;
                                                  $its_new_user =1;
                                              }
                                              else {
                                                    $share_flag= 0;
                                                    $its_new_user =0;
                                              }
                                              
                                          }
                                  if($RS_check_s->RecordCount()>0 && $RS_new->RecordCount()<=0 )
                                  { 
                                      if(  $o_left>0 && $is_active==1)
                                      {
                                          //echo "I main if";
                                      //    echo $share_flag;
                                            if( $share_flag== 1)
                                            {
                                          //      echo "In if";
                                                if($cust_in_same_group_of_camp ==1 || $its_new_user==1)
                                                {
													if($RS[0]->is_walkin == 1)
													{
													?>
														  <a href="javascript:void(0)"  class="reserve_print" id="btn_reserve_deal"> <?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?> </a> 
													<?php
													}
													else{
                                                     ?>
                                <!-- directly reserve button -->
                                                 <a class="reserve_print"  id="directly_reserve" bval="<?php echo $barcodea; ?>"> <?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?> </a>
<!--      <a class="reserve_print" id="directly_reserve" href="print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>"> Reserve Offer </a>-->
                                                    
                                                 
                                                    <?php
													}
                                                }
                                               else{
                                                     ?>
                                                 <a href="javascript:void(0)"  class="reserve_print" id="dont_btn_reserve_deal"> <?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?> </a>
                                            <?php 
                                                }
                                            }  else {
                                              //  echo "in else";
                                                ?>
                                                 <a href="javascript:void(0)"  class="reserve_print" id="dont_btn_reserve_deal"> <?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?></a>
                                            <?php }
                                          
                                      ?>
                              
                              <?php
                                      }  else { ?>
                              <a href="javascript:void(0)" class="reserve_print"  id="dont_btn_reserve_deal"> <?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?> </a>
                              <?php  }
                                  }
                                  else if($RS_check_s->RecordCount()>0 || $RS_new->RecordCount()>0 )
                                  { 
                                     
								//  if(  $o_left>0 && $is_active==1)
                                      //{
                                      
                                      if($RS[0]->new_customer == 1)
                                       {
                                          
                                               /* $sql_is_redeem = "select * from reward_user where campaign_id =".$decode_campaign_id." AND location_id=".$decode_location_id." and customer_id=".$_SESSION['customer_id']." and referred_customer_id=0";
                                               $RS_is_redeem =$objDB->Conn->Execute($sql_is_redeem); */
												$RS_is_redeem = $objDB->Conn->Execute("select * from reward_user where campaign_id =? AND location_id=? and customer_id=? and referred_customer_id=0",array($decode_campaign_id,$decode_location_id,$_SESSION['customer_id']));
												
                                                 if($RS_is_redeem->RecordCount() == 0)
                                                 {
                                                    
						 ?>
<!--                                                    <a class="reserve_print" id="print_btn" href="print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>"> Print </a>-->
                               <h2 class="goh-offer-saved"><?php echo $client_msg['campaign']['label_Offer_Saved'];?> <br/> <a href="<?php echo WEB_PATH; ?>/print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>" id="print_link"><?php echo $client_msg['campaign']['label_Offer_Print'];?> </a><?php echo $client_msg['campaign']['label_Offer_Print_After'];?> </h2>
                                        <?php
                                                  }
                                                  else{
                                                
                                                      // for one per customer redeem deal (new customers only)
                                                      $onepercust_flag = 0;
                                
                                                      ?>
                                                       <h2 class="goh-offer-saved"><?php echo $client_msg['campaign']['label_Offer_Saved'];?> <br/><a href="javascript:void(0)" id="dont_btn_reserve_deal"> <?php echo $client_msg['campaign']['label_Offer_Print'];?> </a></h2>
                                              <?php    }
                                      }
                                      else
                                      {
                                        
                                          if($RS[0]->number_of_use ==1)
                                          {
                                            
												/* $sql_is_redeem = "select * from reward_user where campaign_id =".$decode_campaign_id." AND location_id=".$decode_location_id." and customer_id=".$_SESSION['customer_id']." and referred_customer_id=0"; 
                                                $RS_is_redeem =$objDB->Conn->Execute($sql_is_redeem); */
												$RS_is_redeem = $objDB->Conn->Execute("select * from reward_user where campaign_id =? AND location_id=? and customer_id=? and referred_customer_id=0",array($decode_campaign_id,$decode_location_id,$_SESSION['customer_id']));
													
                                                 if($RS_is_redeem->RecordCount() == 0)
                                                 {
                                      
						 ?>
<!--                                                    <a class="reserve_print" id="print_btn" href="print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>"> Print </a>-->
                                                      <h2 class="goh-offer-saved"><?php echo $client_msg['campaign']['label_Offer_Saved'];?> <br/><a href="<?php echo WEB_PATH; ?>/print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>" id="print_link"><?php echo $client_msg['campaign']['label_Offer_Print'];?></a> <?php echo $client_msg['campaign']['label_Offer_Print_After'];?></h2>
                                        <?php
                                                  }
                                                  else{
                                                  
                                                      // for one per customer redeem deal
                                                      $onepercust_flag = 0;
                                
                                                      ?>
                                                       <h2 class="goh-offer-saved"><?php echo $client_msg['campaign']['label_Offer_Saved'];?><br/><a href="javascript:void(0)" id="dont_btn_reserve_deal"> <?php echo $client_msg['campaign']['label_Offer_Print'];?> </a></h2>
                                              <?php    }
                                          }
                                          else{
                                           
                                          /* $sql_is_redeem = "select * from reward_user where campaign_id =".$decode_campaign_id." AND location_id=".$decode_location_id." and customer_id=".$_SESSION['customer_id']." and referred_customer_id=0";   
                                          $RS_is_redeem =$objDB->Conn->Execute($sql_is_redeem); */
                                          
                                          $RS_is_redeem = $objDB->Conn->Execute("select * from reward_user where campaign_id =? AND location_id=? and customer_id=? and referred_customer_id=0",array($decode_campaign_id,$decode_location_id,$_SESSION['customer_id']));
                                          
                                                 if($RS_is_redeem->RecordCount() == 0)
                                                 {
                                                 
                                                         ?>
<!--                                                    <a class="reserve_print" id="print_btn" href="print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>"> Print </a>-->
                                                       <h2 class="goh-offer-saved"><?php echo $client_msg['campaign']['label_Offer_Saved'];?><br/> <a href="<?php echo WEB_PATH; ?>/print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>" id="print_link"><?php echo $client_msg['campaign']['label_Offer_Print'];?></a> <?php echo $client_msg['campaign']['label_Offer_Print_After'];?></h2>
                                                        <?php 
                                                 }
                                                 else{
                                                   
                                                      if($o_left>0 )
                                                     {
                                                          
                                                     
                                       ?>
                                                       <h2 class="goh-offer-saved"><?php echo $client_msg['campaign']['label_Offer_Saved'];?><br/> <a href="<?php echo WEB_PATH; ?>/print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>" id="print_link"><?php echo $client_msg['campaign']['label_Offer_Print'];?></a> <?php echo $client_msg['campaign']['label_Offer_Print_After'];?></h2>
<!--                                                    <a class="reserve_print" id="print_btn" href="print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>"> Print </a>-->
                                     <?php 
                                                     }
                                                     else{
                                                       
                                                      // for one per customer per day , multiple use 
                                                         $oneperday_multi_flag = 0;
                                                         ?>
                                                       <h2 class="goh-offer-saved"><?php echo $client_msg['campaign']['label_Offer_Saved'];?><br/><a href="javascript:void(0)" id="dont_btn_reserve_deal"> <?php echo $client_msg['campaign']['label_Offer_Print'];?> </a></h2>
                                              <?php      
                                                     }
                                                 }
                                                 }
                                      }
                              
//}  else { ?>
                              <!--                                           <a href="javascript:void(0)" class="reserve_print" id="dont_btn_reserve_deal">                                    
			print
                            </a>-->
                              <?php // }
						/* $Sql_new = "SELECT * FROM customer_campaigns WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$decode_campaign_id."' AND location_id =".$decode_location_id;
						$RS_new = $objDB->Conn->Execute($Sql_new); */
						$RS_new = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?",array($_SESSION['customer_id'],$decode_campaign_id,$decode_location_id));
                if($RS_new->RecordCount()>0){
                    ?>
                              <!--                       <a class="reserve_print" href="includes/process.php?btnunreservedeal=1&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>">                                    
					UnReserve Offer
									</a>-->
                              <?php
                }
									?>
                              <?php  }  else {
                                      if(  $o_left>0 && $is_active==1) {
                                          if($cust_in_same_group_of_camp ==1)
                                          {
                                              
                                          }
                                            if( $share_flag== 1)
                                            {
                                                 if($cust_in_same_group_of_camp ==1 || $its_new_user==1)
                                                {
                                                     if($RS[0]->level == 0)
                                                     { ?>
                                                          <a href="javascript:void(0)"  class="reserve_print" id="btn_reserve_deal"> <?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?> </a> 
                                                     <?php }
                                                     else{ ?>
                                                           <a href="javascript:void(0)"  class="reserve_print" id="btn_reserve_deal" activation_code="<?php echo $avcivation_code; ?>"> <?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?> </a>
                                                     <?php }
                                      ?>
                              
                              <!-- After checking Reserve Offer -->
                              
                              <!-- asking for activation code -->
                            
                              <?php }else{
                                                     ?>
                                                 <a href="javascript:void(0)"  class="reserve_print" id="dont_btn_reserve_deal"><?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?>  </a>
                                            <?php 
                                                }
                                            }
                                            else{?>
                                                 <a href="javascript:void(0)"  class="reserve_print" id="dont_btn_reserve_deal"> <?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?> </a>
                                            <?php }
                                      }
                                      else{?>
                              <a href="javascript:void(0)" class="reserve_print" id="dont_btn_reserve_deal"> <?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?></a>
                              <?php  }
                                  }
                              } 
						//// completing //		
							}
							else {
							
							$curdate = strtotime(date('Y-m-d H:i:s'));
							$expdate = strtotime($RS[0]->expiration_date);  
							if($expdate < $curdate)
							{
								echo "<span class='campaignexpirelbl'>".$client_msg['campaign']['Msg_Campaign_Is_Expired']."</span>";
							}
							else
							{
								// if(  $o_left>0) { 
							
							?>
                              <a  href="<?php echo WEB_PATH."/register.php?url=".$r_r_url ;?>"  class="reserve_print"><?php echo $client_msg['campaign']['Btn_Reserve_Offers'];?></a>
                              <?php				//		} else{?>
<!--                              <a href="javascript:void(0)" class="reserve_print" id="dont_btn_reserve_deal"> Reserve Offer </a>-->
                              <?php  //}
							}
						}
							
                          /*  if($no_rec =="inactive"){ */
                              
                            
                          /*}else{*/ ?>
                              <?php /*?> <!--  <a style="margin: 5px 0 0 0; display: block;" href="print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>">                                    
			<img src="<?=WEB_PATH?>/templates/images/print-coupon_new.png" border="0" />
                            </a> --><?php */?>
                              <?php // }
                        ?>
                            </div>    
                            <div class="dealpointclass" >
								<div class="w100per">
									 <div class="title_for_redeem">
										<?php echo $client_msg['campaign']['label_Redeem_Earn'];?>
									 </div>
									 <div class="title_for_share">
										<?php echo $client_msg['campaign']['label_Share_Earn'];?>
									 </div>
								 </div>
                              <?php if($RS[0]->redeem_rewards != ""){
                ?>
<!--                              <div class="dealdt2">Redeem point : <span><?php echo $RS[0]->redeem_rewards; ?></span></div>-->
                              <div class="redeem_point">
                                              <p><?php echo $RS[0]->redeem_rewards; ?></p>
					       <span><?php echo $client_msg['campaign']['label_Scanflip_Point'];?></span>
                                            </div>
                              <!--<div class="offerstrip" ><span class="">Redeem point :</span><?php echo $RS[0]->redeem_rewards; ?></div>-->
                              <?php
            }?>
                              <?php if($RS[0]->referral_rewards != ""){
                /* $Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$decode_campaign_id;       
				$RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location); */
				$RS_max_no_location = $objDB->Conn->Execute("SELECT max_no_sharing from campaigns WHERE id= ?",array($decode_campaign_id));
				
			 /* $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$decode_campaign_id." and referred_customer_id<>0";
			 $RS_shared = $objDB->Conn->Execute($Sql_shared); */
			 $RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id= ? and referred_customer_id<>0",array($decode_campaign_id));
			 
                //echo $RS_shared->RecordCount().">=".$RS_max_no_location->fields['max_no_sharing'] ;        
			 if($RS_shared->RecordCount() >= $RS_max_no_location->fields['max_no_sharing'] ){
                            ?>
<!--                              <div class="dealdt3">Sharing point : <span> <?php echo 0; ?></span></div>-->
                              <div class="referral_point">
						<p><?php echo 0; ?></p>
						<span><?php echo $client_msg['campaign']['label_Scanflip_Point'];?></span>
                                            </div>
                              <?php
                         }
                         else
                         {
                
                ?>
<!--                              <div class="dealdt3">Sharing point : <span> <?php echo $RS[0]->referral_rewards; ?></span></div>-->
                             <div class="referral_point">
						<p><?php echo $RS[0]->referral_rewards; ?></p>
						<span><?php echo $client_msg['campaign']['label_Scanflip_Point'];?></span>
                                            </div>
                              <?php } ?>
                              <!--<div class="offerstrip" ><span class="">Sharing point :</span><?php echo $RS[0]->referral_rewards; ?></div>-->
                              <?php
            }?>
                                 <?php
                    if($o_left>0 )//&& $o_left!=0)
                    {
                 ?>
<!--                              <div class="dealdtl">Offers left : <span> <?php echo  $o_left; ?></span></div>-->
                            
                                
                             <div class="offer_left">
								<p>
									<?php 
										
										if($o_left>10)
											echo  "10+";
										else
											echo  $o_left;
									?>
								</p>
								<span><?php echo $client_msg['campaign']['label_Offer_Left'];?></span>
                             </div>
                              <?php
                    }
              ?>
                            </div> 
							</div>
							<div class="image_det1" >
								<?php
								if($RS[0]->deal_value!=0)
								{
								?>
                              <div class="discount_wrap">
									<div class="dealmetric title">
										 <div class="value">Value</div>
										 <div class="discount">Discount</div>
										 <div class="saving">Savings</div>
									</div>
									<div class="dealmetric values">
										<div class="value">$<?php echo $RS[0]->deal_value ?></div>
										<div class="discount"><?php echo $RS[0]->discount ?>%</div>
										<div class="saving">$<?php echo $RS[0]->saving ?></div>
									</div>
						      </div>
							  <?php
								}
								?>
						<?php
                        $img_src="";
                        
                        $where_clause123 = array();
                        $where_clause123['id'] = $RSLocation->fields['created_by'];
                        $RSMer = $objDB->Show("merchant_user", $where_clause123);
                        //echo $RSMer->fields['firstname'];
                        if($RS[0]->business_logo!="")
                        {
                            $img_src=ASSETS_IMG."/m/campaign/".$RS[0]->business_logo; 
                        }
                        /*
                        elseif($RSLocation->fields['picture']!="")
                        {
                            $img_src=ASSETS_IMG."/m/location/".$RSLocation->fields['picture']; 
                        }
                        elseif($RSMer->fields['merchant_icon']!="")
                        {
                            $img_src=ASSETS_IMG."/m/icon/".$RSMer->fields['merchant_icon']; 
                        }*/
                        else 
                        {
                            $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
                        }
                        
                        $merchany_icon = "";
                        if($RSMer->fields['merchant_icon']!="")
                        {
                            $merchany_icon=ASSETS_IMG."/m/icon/".$RSMer->fields['merchant_icon']; 
                        }
                        else 
                        {
                            $merchany_icon=ASSETS_IMG."/c/Merchant.png";
                        }
                        ?>
                        <img src="<?php echo $img_src; ?>" border="0" /> 
                      </div>

                       
                          </div>
                          </div>
                      </td>
                      </tr>
                      <tr>
                      <td>
                            <?php
                            function conver_to_time($conv_fr_zon=0,$conv_fr_time="",$conv_to_zon=0)
 {
  //echo $conv_fr_zon."<br>";
  $cd = strtotime($conv_fr_time); 
  
  $gmdate = date('Y-m-d H:i:s', mktime(date('H',$cd)-$conv_fr_zon,date('i',$cd),date('s',$cd),date('m',$cd),date('d',$cd),date('Y',$cd))); 
  //echo $gmdate."<br>";
    
  $gm_timestamp = strtotime($gmdate);
  $finaldate = date('Y-m-d H:i:s', mktime(date('H',$gm_timestamp )+$conv_to_zon,date('i',$gm_timestamp ),date('s',$gm_timestamp ),date('m',$gm_timestamp ),date('d',$gm_timestamp ),date('Y',$gm_timestamp ))); 
  
  return $finaldate;
 }
                            
                             $l_arr = array();
               $l_arr['id']=$decode_location_id;
               $RSLocation = $objDB->Show("locations", $l_arr);
         
              $i =$RSLocation->fields['timezone']; // "-11:30,0";
			$indec = substr($i,0,1);
			$last = substr($i,1,strpos($i,","));
                        
			$hmarr = explode(":",$last);
                       
			$hoffset = $hmarr[0]*60*60;
			$moffset = $hmarr[1]*60;
			$hoffset1 = 6*60*60;
                      
			
			  $offset=$hoffset + $moffset -($hoffset1 )   ;
                         
			  $dateFormat="Y-m-d H:i:s";
			
			  if($indec == "+")
				$timeNdate1 = date($dateFormat, strtotime($RS[0]->expiration_date)+$offset);
			  else
				$timeNdate1 = date($dateFormat, strtotime($RS[0]->expiration_date)-$offset);
            //  current date and time
              $i =$RSLocation->fields['timezone'];// "-11:30,0";
              $tz = substr($i,0,strpos($i,","));
			$indec = substr($i,0,1);
			$last = substr($i,1,strpos($i,","));
			$hmarr = explode(":",$last);
			$hoffset = $hmarr[0]*60*60;
			$moffset = $hmarr[1]*60;
			$hoffset1 = 6*60*60;
                        $tz= str_replace(':','.',$tz);
                       
			  $offset=$hoffset + $moffset + $hoffset1;
			  $dateFormat="Y-m-d H:i:s";

			  if($indec == "+")
				$timeNdate1_curr = date($dateFormat, strtotime(date('Y-m-d H:i:s'))+$offset);
			  else
				$timeNdate1_curr = date($dateFormat, strtotime(date('Y-m-d H:i:s'))-$offset);
                          
              $curr_loc_dt =conver_to_time(CURR_TIMEZONE,date('Y-m-d H:i:s'),$tz);
            
			  // calculate difference
                     
              $years="";
              $months="";
              
                                $date1 = $curr_loc_dt;
				$date2 = $RS[0]->expiration_date;
				
				
				$diff = abs(strtotime($date2) - strtotime($date1));
				$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        $hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 

        $minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 

        $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60)); 
				$diff = "";
				
				if($years != 0)
				{
					$diff .= $years." years ";
				}
				if($months != 0)
				{
					$diff .= $months." months ";
				}
				if($days != 0)
				{
					$diff .= $days." days ";
				}
				$diff .= $hours."hours :".$minuts."minute :".$seconds."second";
                                      
			  $camp_d = explode("-",$date2);
        $dt = explode(" ",$camp_d[2]);
        $cam_time = explode(":",$dt[1]);
				
		
			  ?>
                          
                           
                            <div style="clear:both;padding-left: 5px;" />
                            <div class="div_seprator" ></div>
                         
                            
                         
                              <?php 
                
               
                
		    
                $address = $RSLocation->fields['address'].", ".$RSLocation->fields['city'].", ".$RSLocation->fields['state'].", ".$RSLocation->fields['zip'].", ".$RSLocation->fields['country']; 
				?>
              
                   <script type="text/javascript">
    
        $(function () {
                    
                austDay = new Date(<?=$camp_d[0];?>, <?=$camp_d[1];?>-1, <?=$dt[0];?>, <?=$cam_time[0];?>, <?=$cam_time[1];?>, <?=$cam_time[2];?>  );
              
                //new Date(2008, 8 - 1, 8)});
                
                $('#defaultCountdown').countdown({until: austDay, timezone: '<?=$tz;?>'});
                $('#year').text(austDay.getFullYear());
        });
		
		
        </script>
              <div class="div_location_info" width="100%">
					<?php
						$size = getimagesize($merchany_icon);
						$height=$size[1];
						if($height>=111)
						{
					?>
						<div class="image_info1">&nbsp;
							<img src="<?php echo  $merchany_icon; ?>" />
						</div>
					<?php
						}
						else
						{
					?>
						<div class="image_info2">&nbsp;
							<img src="<?php echo  $merchany_icon; ?>" />
						</div>
					<?php
						}
					?>
					
                   
                    <div class="address_info">
						<?php
							if($RS[0]->number_of_use==1)
							{
								if($RS[0]->new_customer==1)
								{
									echo "<div class='limit' ><b>".$client_msg['campaign']['label_Limit'] ." </b>".$client_msg['campaign']['label_One_Per_New'] ."</div>";
								}
								else 
								{
									echo "<div class='limit' ><b>".$client_msg['campaign']['label_Limit']." </b>".$client_msg['campaign']['label_One_Per_Customer']."</div>";
								}
							}
							elseif($RS[0]->number_of_use==2)
								echo "<div class='limit' ><b>".$client_msg['campaign']['label_Limit']." </b>".$client_msg['campaign']['label_One_Per_Day']."</div>";
							elseif($RS[0]->number_of_use==3)
								echo "<div class='limit' ><b>".$client_msg['campaign']['label_Limit']." </b>".$client_msg['campaign']['label_Earn_Redemption']."</div>";
							echo "<div class='exp_date' ><b>".$client_msg['campaign']['label_Expiration_Date']."</b>". date("m/d/y g:i A", strtotime($RS[0]->expiration_date ))."</div>"; 
						?>
                        <div class="how_to_use" >

                            <b><?php echo $client_msg['campaign']['label_How_Use']; ?>  </b> <?php echo $client_msg['campaign']['label_In_Store'];  ?>,&nbsp;&nbsp;&nbsp;<br> <b> <?php echo $client_msg['campaign']['label_Time_Expire'];  ?> </b>
                        </div>                           
                            <div id="defaultCountdown" style="display:inline"></div>
							<?php
								$curdate = strtotime(date('Y-m-d H:i:s'));
								$expdate = strtotime($RS[0]->expiration_date);  
								if($expdate < $curdate)
								{
								}
								else
								{								
									if($days <=2)
									{
                              ?>
										<span class="expiring_soon">( Expiring Soon ! )</span>
                              <?php
									}
								}
                              ?>
                              
<!--                              <span> <?php echo $diff; ?></span>-->
                              <div ><b><?php echo $client_msg['campaign']['label_Share']." "; ?></b><?php echo $client_msg['campaign']['label_To_Earn']; ?> </div>
                    </div>
                    </div>
               </div>
                 
           
               
                              <?
			  $share="true";
			//$th_link = WEB_PATH."/campaign.php?campaign_id=$_REQUEST[campaign_id]&l_id=$_REQUEST[l_id]";
			$th_link = WEB_PATH."/register.php?".$r_r_url ;
                        //  $th_link = WEB_PATH."/campaign.php?campaign_id=$_REQUEST[campaign_id]&l_id=$_REQUEST[l_id]&share=$share";
			if(isset($_SESSION['customer_id']))
			{
				if($_SESSION['customer_id'] != ""){
					$th_link .= "&customer_id=".base64_encode($_SESSION['customer_id']);                                 
				}
			}
			?>
                              
                              <!--<a id="fbshare" class="fbshare" href="#" onclick="fbshare()" name="fb_share" share_url="<?=$th_link?>"></a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>-->
                              
                              <?php
if(isset($_SESSION['customer_id']))
{
    
		if(isset($_REQUEST['fbshare']))
		{
			if($_REQUEST['fbshare']==1)
			{
					  
					  
						/* $Sql = "SELECT * FROM merchant_subscribs WHERE user_id=".$_SESSION['customer_id']." and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=".$decode_location_id."  )";
						$RS_new = $objDB->Conn->Execute($Sql); */
						$RS_new = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id= ? and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=?)",array($_SESSION['customer_id'],$decode_location_id));
						
						if($RS_new->RecordCount()<=0)
						{
							/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$decode_location_id." and private = 1";
							$RS_group = $objDB->Conn->Execute($sql_group); */
							$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($decode_location_id));
							
							$array_group= array();
							$array_group['merchant_id']=$RS_group->fields['merchant_id'];
							$array_group['group_id']=$RS_group->fields['id'];
							$array_group['user_id']=$_SESSION['customer_id'];
							$objDB->Insert($array_group, "merchant_subscribs");
						}
						
					//    exit;
			   // $array_values = $where_clause = $array = array();
			//    $array_values['redeem_rewards'] = ($RS[0]->redeem_rewards)+5;
			//    $where_clause['id'] = $RS[0]->id;
			//    $objDB->Update($array_values, "campaigns", $where_clause); 
				
				// b
				$array = $json_array = array();
			//	$array['customer_id'] = $_SESSION['customer_id'];
			//	$array['campaign_id'] = $decode_campaign_id;
			//	$array['referral_reward'] = $RS[0]->referral_rewards;
			//	$array['reward_date'] = date("Y-m-d H:i:s");
			//	$objDB->Insert($array, "reward_user");
					
				/*  4-1-2013 */	
	//				$array_f=array();
	//				$array_f['customer_id'] = $_SESSION['customer_id'];
	//                                $array_f['campaign_id'] = $decode_campaign_id;
	//			$array_f['referral_reward'] = $RS[0]->referral_rewards;
	//				$array_f['location_id'] = $decode_location_id;
	//				$array_f['referred_customer_id'] =  $_SESSION['customer_id'];
	//				$array_f['social_share'] = 1;
	//			$array_f['reward_date'] = date("Y-m-d H:i:s");
	//				$objDB->Insert($array_f, "reward_user");
	//				$array_f=array();
				/*  4-1-2013 */	
				
				/* $redirect_query = "select permalink from campaign_location where campaign_id=".$decode_campaign_id." and location_id=".$decode_location_id;
				$redirect_RS = $objDB->Conn->Execute($redirect_query ); */
				$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=",array($decode_campaign_id,$decode_location_id));
				
				//$json_array['permalink'] =  $redirect_RS->fields['permalink'];
				header("Location: ".$redirect_RS->fields['permalink']);
				//header("Location: ".WEB_PATH."/campaign.php?campaign_id=".$decode_campaign_id."&l_id=".$decode_location_id);    
			}
		}
}
else
{
	if(isset($_REQUEST['fbshare']))
	{
		if($_REQUEST['fbshare']==1)
		{   
		   // echo "In fbshare1233";
		  //  exit;
			header("Location: ".WEB_PATH."/register.php");
		}
	}
}
$fb_desc_share = $RS[0]->deal_detail_description;
$fb_title = $RS[0]->title;
$redirect_url=$location_max->fields['permalink'];

$sharepoint= $RS[0]->redeem_rewards;

 $tag_main="";
if($RS[0]->campaign_tag != "")
{
    $fb_campaign_tag_temp=  explode(",",$RS[0]->campaign_tag);
    $tag_count= count($fb_campaign_tag_temp);

   
    
        for($i=0;$i<$tag_count;$i++)
        {
            $tag_main.="#".$fb_campaign_tag_temp[$i]." ";

        }
    
}
 if($RS[0]->business_logo!="")
                        {
                           // $img_src=ASSETS_IMG."/m/logo/".$RS[0]->business_logo; 
                            $fb_img_share = ASSETS_IMG."/m/campaign/".$RS[0]->business_logo;
                        }
                        else 
                        {
                            $fb_img_share =ASSETS_IMG."/c/Merchant_Offer.png";
                        }
$campaign_tag_array = explode(",", $RS[0]->campaign_tag);
$campaign_tag_str="";
//echo count($campaign_tag_array);

if(count($campaign_tag_array)>1)
{
	for($inc=0;$inc<=count($campaign_tag_array)-1;$inc++)
	{
		$campaign_tag_str.=" #".$campaign_tag_array[$inc];
	}
}
else
{
	if($RS[0]->campaign_tag!="")
	{
		$campaign_tag_str.=" #".$RS[0]->campaign_tag;
	}
}
//echo $campaign_tag_str;
$camp_share_link="#Scanflip Offer -".$RS[0]->title.".. now available at ".$RSMer->fields['business']." participating locations.Limited Offers.. Reserve Now!".$campaign_tag_str;

if(isset($_SESSION['customer_id']))
{
	if(isset($_REQUEST['twitshare']))
	{
		if($_REQUEST['twitshare']==1)
		{
			   
				/* $Sql = "SELECT * FROM merchant_subscribs WHERE user_id=".$_SESSION['customer_id']." and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=".$decode_location_id."  )";
				$RS_new = $objDB->Conn->Execute($Sql); */
				$RS_new = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id= ? AND group_id in( select  id from merchant_groups where private=1 and location_id=?)",array($_SESSION['customer_id'],$decode_location_id));		
							if($RS_new->RecordCount()<=0)
							{
								/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$decode_location_id." and private = 1";
								$RS_group = $objDB->Conn->Execute($sql_group); */
								$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($decode_location_id));
								$array_group= array();
								$array_group['merchant_id']=$RS_group->fields['merchant_id'];
								$array_group['group_id']=$RS_group->fields['id'];
								$array_group['user_id']=$_SESSION['customer_id'];
								$objDB->Insert($array_group, "merchant_subscribs");
							}
				/* $redirect_query = "select permalink from campaign_location where campaign_id=".$decode_campaign_id." and location_id=".$decode_location_id;
				$redirect_RS = $this->Conn->Execute($redirect_query ); */
				$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=",array($decode_campaign_id,$decode_location_id));
				
				//$json_array['permalink'] =  $redirect_RS->fields['permalink'];
				header("Location: ".$redirect_RS->fields['permalink']);	
			//header("Location: ".WEB_PATH."/campaign.php?campaign_id=".$decode_campaign_id."&l_id=".$decode_location_id);    
		}
	}
}
else
{
	if(isset($_REQUEST['twitshare']))
	{
		if($_REQUEST['twitshare']==1)
		{        
			header("Location: ".WEB_PATH."/register.php");
		}
	}
}

?>
<!--<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
<link rel="image_src" href="<?=$fb_img_share?>" />
<a href="https://www.facebook.com/share.php?u=<?=$th_link?>&amp;t=<?=$fb_title?>" title="share this on facebook" target="_self">Share</a>-->
<?php

                    $title=urlencode($fb_title);
                    $url=urlencode($th_link);
                    $summary=urlencode($fb_desc_share);
                    $image=$fb_img_share;
		    //$image="http://ia.media-imdb.com/images/M/MV5BMjEwOTA2MjMwMl5BMl5BanBnXkFtZTcwODc3MDgxOA@@._V1._SY314_CR3,0,214,314_.jpg";
		    //echo $image; 
?>
<!--<style type="text/css">
	@font-face {
	font-family: 'zArista';
	src: url('font/zarista.eot');
	src: url('font/zarista.eot?#iefix') format('embedded-opentype'),
	   url('font/zarista.woff') format('woff'),
	   url('font/zarista.ttf') format('truetype'),
	   url('font/zarista.svg#zArista') format('svg');
	font-weight: normal;
	font-style: normal;
	}
</style> -->
<div class="share_loader" style="display:none;">
				
</div> 
<div class="socials-linked">
    <div class="socials_linked_email" >
      <? 
	if(isset($_SESSION['customer_id']))
	{
	  if($_SESSION['customer_id'] != ""){ ?>
        
                 <a href="javascript:void(0)" id="btn_msg_div"  class="email_link">
<!--<img src="<?php echo ASSETS_IMG.'/images/msg.png'?>" name="msg_share" />-->Email</a>
      <? }
	}else {?>
                  <a  href="<?php echo WEB_PATH."/register.php?url=".$r_r_url ;?>" class="email_link"  ><!--<img src="<?php echo WEB_PATH.'/images/msg.png'?>" name="msg_share" />-->Email</a>
      <?php }
	
	  ?>
    </div>
    <div >
<? 
if(isset($_SESSION['customer_id']))
{
	if($_SESSION['customer_id'] != "")
	{ 
		/* $Sql_new = "select * from customer_user  where id=".$_SESSION['customer_id'];
	    $RS_user_data = $objDB->Conn->Execute($Sql_new); */
        $RS_user_data = $objDB->Conn->Execute("select * from customer_user  where id= ?",array($_SESSION['customer_id']));
               
                  //  echo $RS_location->fields['facebook_user_id'];
                
                   if($RS_user_data->fields['facebook_user_id'] != "")
                   { ?>
                       <a class="btn_share_facebook loginshare"  target="_parent" href="javascript: void(0)">     
                                Facebook
                        </a>
                  <?php  }
                  else
                  { ?>
                      <a href="<?php echo $loginUrl; ?>" class="btn_share_facebook  notlogin" target="_parent" >Facebook</a>
                  <?php }
                    ?>        

<?php }
}else
{ ?>
<a class="btn_share_facebook"  href="<?php echo WEB_PATH."/register.php?url=".$r_r_url;?>"  > Facebook </a>
<?php 
}
	?>

   
    </div>
    <div >

<? if(isset($_SESSION['customer_id']))
   {
	if($_SESSION['customer_id'] != ""){ ?>
<!--				<a  href="<?php echo WEB_PATH."/register.php?url=".urlencode($th_link);?>" url="<?=urlencode($th_link)?>" class="twitter-share-button" data-lang="en" data-count="none">Tweetr</a> -->
            <!--<a  href="https://twitter.com/intent/tweet?original_referer=<?php echo curPageURL(); ?>&text=ScanFlip%20Offer%20-%20 <?php echo $fb_title;?>&tw_p=tweetbutton&url=<?php echo urlencode($th_link);?>" url="<?=urlencode($th_link)?>" class="twitter_link" data-lang="en" data-count="none">Twitter</a>-->
        <a  href="javascript: void(0)" class="twitter_link" data-lang="en" data-count="none">Twitter</a>
<?php }
?>
        <script type="text/javascript" charset="utf-8">
var customer_id="<?php echo $_SESSION['customer_id'] ?>";

//if(customer_id != "")
//{
window.twttr = (function (d,s,id) {

var t, js, fjs = d.getElementsByTagName(s)[0];

if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;

js.src="//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);

return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });

}(document, "script", "twitter-wjs"));							
// }
</script> 
        <?php
}else
        { ?>
        <a  href="<?php echo WEB_PATH."/register.php?url=".$r_r_url ;?>" class="twitter_link" >Twitter</a>
<?php 
   }	?>



</div>

    <div >
		<? 
		if(isset($_SESSION['customer_id']))
	{
		if($_SESSION['customer_id'] != ""){ ?>
		<!--<a href='https://plus.google.com/share?url=<?=$th_link?>' onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
  return false;" class="google_plus_link">-->
		
		<!--<a  href="https://plus.google.com/share?url=<?php echo urlencode($th_link)?>" class="google_plus_link" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600'); return false;">-->
        
		<!--
		<a  href="javascript:void(0)" id="<?php echo $th_link;?>" class="google_plus_link" onclick="googlefunction(this.id);" >
		-->
		
		<!--
		<a href="javascript:void(0)" id="<?php echo $th_link;?>" class="google_plus_link" onclick="google_sharing( '<?php echo $decode_campaign_id;?>','<?php echo $decode_location_id;?>','true','<?php echo base64_encode($_SESSION['customer_id']);?>' )" >       
            Google+    
        </a>
		-->
		
		<script type="text/javascript">
		  (function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		  function callbackfromgoogleplus(data) {
				//alert(data.status);
				if(data.post_id)
				{
					var msg="<?php echo $client_msg["common"]["Msg_sharing_success_facebook"];?>";
					var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
					var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
					var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='sharignpopup' name='sharignpopup' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
					jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

					jQuery.fancybox({
						content:jQuery('#dialog-message').html(),
						type: 'html',
						openSpeed  : 300,
						closeSpeed  : 300,
						// topRatio: 0,
						modal : 'true',
						changeFade : 'fast',  
						beforeShow : function(){
							jQuery(".fancybox-inner").addClass("msgClass");
						},
						hideOnOverlayClick : false, // prevents closing clicking OUTSIE fancybox
						hideOnContentClick : false, // prevents closing clicking INSIDE fancybox
						enableEscapeButton : false,       

						helpers: {
								overlay: {
								opacity: 0.3
								} // overlay
						}
					})
																			
					//alert(response.post_id);
					/* 15-03-2014 share counter */	
					var cust_id="<?php echo $_SESSION['customer_id'];?>";
					var campaign_id="<?php echo $decode_campaign_id;?>";
					var l_id="<?php echo $decode_location_id;?>";					
					//alert('<?php echo WEB_PATH;?>/process.php?btn_increment_share_counter=yes&customer_id='+cust_id+'&reffer_campaign_id='+campaign_id+'&refferal_location_id='+l_id+'&domain=3&medium='+jQuery("#medium").val()+'&timestamp='+formattedDate());
					jQuery.ajax({
						type:"POST",
						url:'<?php echo WEB_PATH;?>/process.php',
						data :'btn_increment_share_counter=yes&customer_id='+cust_id+'&reffer_campaign_id='+campaign_id+'&refferal_location_id='+l_id+'&domain=3&medium='+jQuery("#medium").val()+'&timestamp='+formattedDate(),
					   // async:false,
						success:function(msg)
						{
						}
					});
					/* 15-03-2014 share counter */
				}
				
		  }
		</script>
						
		<a href="javascript:void(0)" 
		  class="g-interactivepost google_plus_link"
		  data-contenturl="<?php echo WEB_PATH.'/register.php?campaign_id='.$decode_campaign_id.'&l_id='.$decode_location_id.'&share=true&customer_id='.base64_encode($_SESSION['customer_id']).'&domain=3'?>"		  
		  data-clientid="1043802627758-jsh3e6koq6a34tqij458b4s0adk1ca88.apps.googleusercontent.com"
		  data-cookiepolicy="single_host_origin"
		  data-prefilltext="<?php echo $camp_share_link; ?>"
		  data-calltoactionurl="<?php echo WEB_PATH.'/register.php?campaign_id='.$decode_campaign_id.'&l_id='.$decode_location_id.'&domain=3&share=true&customer_id='.base64_encode($_SESSION['customer_id']).'&domain=3'?>" 
		  data-calltoactionlabel="RESERVE"
		  data-contentdeeplinkid="campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>&customer_id=<?php echo base64_encode($_SESSION['customer_id']); ?>" 
		  data-calltoactiondeeplinkid="campaign_id=<?php echo $decode_campaign_id; ?>&l_id=<?php echo $decode_location_id; ?>&customer_id=<?php echo base64_encode($_SESSION['customer_id']); ?>" 
		  data-onshare="callbackfromgoogleplus"
		  >
		  Google+
		</a>
			
		<!-- 
				  data-contentdeeplinkid: '/scanflip/register' 
		  data-calltoactiondeeplinkid: '/scanflip/register' 
		  start Rendering the button with JavaScript
		<a href="javascript:void(0)" id="sharePost" class="google_plus_link">Google+</a>
		<script type="text/javascript">
		  var options = {
			contenturl: "<?php echo WEB_PATH.'/register.php?campaign_id='.$decode_campaign_id.'&l_id='.$decode_location_id.'&share=true&customer_id='.base64_encode($_SESSION['customer_id']).'&domain=3'?>",
			clientid: '1043802627758-jsh3e6koq6a34tqij458b4s0adk1ca88.apps.googleusercontent.com',
			cookiepolicy: 'single_host_origin',
			prefilltext: "<?php echo $camp_share_link; ?>",
			calltoactionlabel: 'RESERVE',
			calltoactionurl: "<?php echo WEB_PATH.'/register.php?campaign_id='.$decode_campaign_id.'&l_id='.$decode_location_id.'&share=true&customer_id='.base64_encode($_SESSION['customer_id']).'&domain=3'?>",
			contentdeeplinkid: '/pages',
			calltoactiondeeplinkid: '/pages/create',
			onshare: function(response){
				//alert(response.status);
				//alert(response.post_id);
				if(response.post_id)
				{
					var msg="<?php echo $client_msg["common"]["Msg_sharing_success_facebook"];?>";
					var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
					var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
					var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='sharignpopup' name='sharignpopup' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
					jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

					jQuery.fancybox({
						content:jQuery('#dialog-message').html(),
						type: 'html',
						openSpeed  : 300,
						closeSpeed  : 300,
						// topRatio: 0,
						modal : 'true',
						changeFade : 'fast',  
						beforeShow : function(){
							jQuery(".fancybox-inner").addClass("msgClass");
						},
						hideOnOverlayClick : false, // prevents closing clicking OUTSIE fancybox
						hideOnContentClick : false, // prevents closing clicking INSIDE fancybox
						enableEscapeButton : false,       

						helpers: {
								overlay: {
								opacity: 0.3
								} // overlay
						}
					})
																			
					//alert(response.post_id);
					/* 15-03-2014 share counter */	
					var cust_id="<?php echo $_SESSION['customer_id'];?>";
					var campaign_id="<?php echo $decode_campaign_id;?>";
					var l_id="<?php echo $decode_location_id;?>";					
					//alert('<?php echo WEB_PATH;?>/process.php?btn_increment_share_counter=yes&customer_id='+cust_id+'&reffer_campaign_id='+campaign_id+'&refferal_location_id='+l_id+'&domain=3&medium='+jQuery("#medium").val()+'&timestamp='+formattedDate());
					jQuery.ajax({
						type:"POST",
						url:'<?php echo WEB_PATH;?>/process.php',
						data :'btn_increment_share_counter=yes&customer_id='+cust_id+'&reffer_campaign_id='+campaign_id+'&refferal_location_id='+l_id+'&domain=3&medium='+jQuery("#medium").val()+'&timestamp='+formattedDate(),
					   // async:false,
						success:function(msg)
						{
						}
					});
					/* 15-03-2014 share counter */
				}
			}
		  };
		  // Call the render method when appropriate within your app to display
		  // the button.
		  gapi.interactivepost.render('sharePost', options);
		</script>
		end Rendering the button with JavaScript
		-->
        <?php }
		}else
        { ?>
         <a href="<?php echo WEB_PATH."/register.php?url=".$r_r_url ;?>" class="google_plus_link" >
        <!--    <img src="images/google-plus.png" alt="Google+" title="Google+"/>-->Google+
        </a>
        <?php 
		}
		 ?>
		
    </div>
	
</div>
<div style="clear:both"></div>

                <?php
               // echo "<div style='margin-top:5px;'><b>Address :</b> ".$address."</div>";
               
				
				?>
                          
                            </p>
                            
                          </div>
                          
                          <!--        <p style="border-bottom: 1px solid #CCCCCC;color: #000;font-size: 17px; margin: 0;"><b>Category : </b>
             <?php
                        $arr=file(WEB_PATH.'/process.php?getCategoryfromid=yes&cat_id='.$RS[0]->category_id);
                        if(trim($arr[0]) == "")
                             {
                                     unset($arr[0]);
                                     $arr = array_values($arr);
                             }
                             $json = json_decode($arr[0]);
                        $mycat = $json->records;
                        
                        foreach($mycat as $Rownew)
                        {
                            echo $Rownew->cat_name ;
                         
                        }
                        
               ?>
        </p> --> 
                          <!--        <p style="border-bottom: 1px solid #CCCCCC;color: #000;font-size: 17px; margin: 0;"><b>Locations : </b>
             <?php 
                        $arr=file(WEB_PATH.'/process.php?get_location_name_of_campaigns=yes&camp_id='.$RS[0]->id);
                        if(trim($arr[0]) == "")
                             {
                                     unset($arr[0]);
                                     $arr = array_values($arr);
                             }
                             $json = json_decode($arr[0]);
                        $locations = $json->records;
                        $t_records = $json->total_records;
                        $cnt = 1;
                        
                        foreach($locations as $Rownew)
                        {
                         echo $Rownew->location_name ;
                         if($t_records != $cnt){
                          echo " , ";
                         }
                         $cnt++;
                        }
                        
               ?>
        </p>-->
                          
                        
                      <!--</div>-->
                     
<!--                    </div>-->
                  </div></td>
                <!--<td width="50%" align="left" valign="top"> 
      </td>  
-->
                <?php
//            $where_clause = array();
//            $where_clause['campaign_id'] = $decode_campaign_id;
//            $RSComp = $objDB->Show("campaign_location", $where_clause);
//            $RSComp->MoveFirst();
//						$total = $RSComp->RecordCount();
//						
//						$count = 1;
//						$lat = $lon = "";
				/*	if($RSComp->RecordCount()>0){
                                            echo "<br/><br/><b>Address : </b><br/>";
					while($Row = $RSComp->FetchRow()){
						$where_clause = array();
						$where_clause['id'] = $Row['location_id'];
						$RSLocation = $objDB->Show("locations", $where_clause);
						
						$lat = $RSLocation->fields['latitude'];
						$lon = $RSLocation->fields['longitude'];
						$address = $RSLocation->fields['address'].", ".$RSLocation->fields['city'].", ".$RSLocation->fields['state'].", ".$RSLocation->fields['zip'].", ".$RSLocation->fields['country'];
						//echo $address."<hr>";
						//$image = '<img height="60" src="'.WEB_PATH.'/merchant/images/location/'.$RSLocation->fields['picture'].'" border="0" />';
						//$MARKER_HTML .= "<b>".$RSLocation->fields['location_name']."</b><br />";
						$phone_number="Phone Number : ".$RSLocation->fields['phone_number']."<br/><br/>";
						$MARKER_HTML .= $address."<br />".$phone_number;
                                                echo $MARKER_HTML;
						
                                        }
                                        }*/
                                    
                                        
        ?>
              </tr>
              
              
              <tr>
                  <td>
                      <div class="detail_lft" >                      
                            <?php
                            if($RS[0]->description!='')
                            {
                                //echo strlen($RS[0]->description);
                                //echo strlen(strip_tags($RS[0]->description));
                            ?>
                                <div class="ofr_detail"><?php echo $client_msg['campaign']['label_Offers_Detail']?></div>
                                <?php
                                if(strlen(strip_tags($RS[0]->description))>800)
                                {
                                ?>
                                <div id="desc_div" style="height:240px;">
                                    <?php
										echo substr($RS[0]->description,0,600)."...".'<a id="desc_more" href="javascript:void(0)" >show more</a>';
									?>
                                </div>
								<div id="desc_div_hidden" style="display:none;">
                                    <?php
										echo $RS[0]->description;
									?>
                                </div>
                                <?php
                                }
                               else
                                {
                                ?>
                                <div id="desc_div" style="font-size:14px;text-align: justify; margin: 0;height:auto;overflow:hidden;padding-left:10px;">
                                    <?=$RS[0]->description?>
                                </div>
                                <?php
                                }
                                ?>
                                <?php
								/*
                                if(strlen(strip_tags($RS[0]->description))>800)
                                {
                                ?>
                                <span style="float:right;margin-right: 10px;">
                                    <a id="desc_more" href="javascript:void(0)" style="background: none repeat scroll 0 0 #727273;
        box-shadow: 2px 2px 2px #BFBFBF;
        color: #FFFFFF !important;padding:5px;">Show More</a>
                                </span>
                                <?php
                                }
								*/
                                ?>
                            <?php
                            }
                            ?>
                            <?php
                            if($RS[0]->terms_condition!='')
                            {
                                //echo strlen($RS[0]->terms_condition);
								$terms_and_condition=$RS[0]->terms_condition."<p>Additional Terms</p><p>
									No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.
									</p>";
                            ?>
                                <div class="terms_condi" ><?php echo $client_msg['campaign']['label_Terms_Condition'];?></div>
                                <?php
                                if(strlen(strip_tags($terms_and_condition))>800)
                                {
                                ?>
                                <div id="terms_div" style="height:240px;">
									<?php
										echo substr($terms_and_condition,0,500)."...".'<a id="terms_more" href="javascript:void(0)" >show more</a>';
									?>
                                </div>
								<div id="terms_div_hidden" style="display:none;">
                                    <?php
										echo $terms_and_condition;
									?>
                                </div>
                                <?php
                                }
                                else
                                {
                                ?>
                                <div id="terms_div" style="height:auto;">
                                  <?=$terms_and_condition?>
                                </div>
                                <?php
                                }
                                ?>
                                <?php
								/*	
                                if(strlen(strip_tags($terms_and_condition))>800)
                                {
                                ?>
                                <span style="float:right;margin-right: 10px;">
                                    <a id="terms_more" href="javascript:void(0)" style="background: none repeat scroll 0 0 #727273;
        box-shadow: 2px 2px 2px #BFBFBF;
        color: #FFFFFF !important;padding:5px;">Show More</a>
                                </span>
                                <?php
                                }
								*/
                                ?>
                            <?php
                            }
							else
							{
							?>
								<div class="terms_condi" ><?php echo $client_msg['campaign']['label_Terms_Condition'];?></div>
                                <div style="padding-left:10px;">
									<p>
									No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.
									</p>
								</div>
                                
							<?php
							}
                            ?>
                      </div>
                      <div class="detail_rgt" >
                              <?php
                //$url = "http://api.ipinfodb.com/v3/ip-city/?key=0fd27970ffe2d63e0c7599eef6f54fe42d365d2cd6804e8810a6dc85aeeac380";
                //$json_lati_long = file_get_contents($url);
                //$data_lati_long = split(";",$json_lati_long);
                 $from_long = "";
                $from_lati = "";
                 if(isset($_COOKIE['mycurrent_lati'])){
                $from_lati=$_COOKIE['mycurrent_lati'];
                
                $from_long=$_COOKIE['mycurrent_long'];
                }
                else{
                    if(isset($_SESSION['customer_id']))
                    {
                        $from_lati =  $_SESSION['customer_info']['curr_latitude'];
                        $from_long =  $_SESSION['customer_info']['curr_longitude'];
                    }
                }
                if($from_lati != "" && $from_long != "")
                {
                $to_lati=$RSLocation->fields['latitude'];
                
                $to_long=$RSLocation->fields['longitude'];
                
		$maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;
                }
				$to_lati=$RSLocation->fields['latitude'];
                
                $to_long=$RSLocation->fields['longitude'];
		echo "<div class='where_to_redem'>";
                ?><?php echo $client_msg['campaign']['label_Where_Redeem'];?>
                         
                  
                              <?php
              
//                if($from_lati!="" && $from_long!="")
//                {
//                    echo "&nbsp;<span>,</span>&nbsp; <b>".$objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M")." </b>Miles Away";
//                    
//                    //echo $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M") . "</div>";
//                }
		  echo "</div>";
                
                //echo $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "K") . " Kilometers<br>";
                //echo $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "N") . " Nautical Miles<br>";
                
                
                ?>
                          <div class="map_main">
                    <iframe id="slider_iframe" src="<?=WEB_PATH?>/location-map.php?location_id=<?=$decode_location_id?>" frameborder="0" style="height:300px;margin:0;" scrolling="no"></iframe>
                  </div>
                  <div>
                      <?php
                        if($RSLocation->fields['location_name']!="")
						{
                    //echo "<a href='".WEB_PATH."/location_detail.php?id=".$RSLocation->fields['id']."'>".$RSLocation->fields['location_name']."</a>";
					  echo "<a href='".$RSLocation->fields['location_permalink']."'>".$RSLocation->fields['location_name']."</a>";
					}
                elseif($busines_name!="")
				{
                    echo "<a href='".$RSLocation->fields['location_permalink']."'>".$busines_name."</a>";
					}
                else
				{
                    echo "<a href='".$RSLocation->fields['location_permalink']."'>Scanflip Merchant</a>";
					}
					
					// start see more offers
					
//					$zip = repalce_char($_COOKIE['searched_location']);
//					$locc = GetLatitudeLongitude($zip);
//					
//					if($locc !="")
//					{
//                                         
//						$mlatitude = $locc["location"]["latitude"];	
//						$mlongitude = $locc["location"]["longitude"];
//											
//						$cookie_life = time() + 31536000;
//						setcookie('mycurrent_lati', $mlatitude, $cookie_life);
//						setcookie('mycurrent_long', $mlongitude, $cookie_life);
//						$_SESSION['mycurrent_lati']=$mlatitude;
//						$_SESSION['mycurrent_long']=$mlongitude;
//					
//					}
//					else
//					{
//                                          
//						$mlatitude = 0;	
//						$mlongitude = 0;
//					
//					}
					
					if(isset($_COOKIE['miles_cookie']))
                                        {
                                            $dismile=$_COOKIE['miles_cookie'];
                                        }
                                        else{
                                            $dismile= 50 ;
                                        }
					$cust_id="";
					if(isset($_SESSION['customer_id']))
					{
						$cust_id=$_SESSION['customer_id'];
					}
                                        if(isset($_COOKIE['cat_remember']))
                                        {
                                            $catremember = $_COOKIE['cat_remember'];
                                        }
                                        else{
                                            $catremember = 0;
                                        }
										$catremember = 0;
					//echo WEB_PATH.'/process.php?btnGetActiveDealsOnCampaignPage=yes&dismile='.$dismile.'&mlatitude='.$mlatitude.'&mlongitude='.$mlongitude.'&category_id='.$catremember.'&customer_id='.$cust_id.'&merchant_id='.$RSLocation->fields['created_by'].'&loc_id='.$RSLocation->fields['id'];
					$arr=file(WEB_PATH.'/process.php?btnGetActiveDealsOnCampaignPage=yes&dismile='.$dismile.'&mlatitude='.$mlatitude.'&mlongitude='.$mlongitude.'&category_id='.$catremember.'&customer_id='.$cust_id.'&merchant_id='.$RSLocation->fields['created_by'].'&loc_id='.$RSLocation->fields['id']);
					if(trim($arr[0]) == "")
					{
						unset($arr[0]);
						$arr = array_values($arr);
					}
					$json = json_decode($arr[0]);
					$total_records1 = $json->total_records;
					
					
					// end see more offers
					
					if($total_records1>1)
					{
                      ?>
					  <div class="shw_mre" >
						<?php echo "<a href='".$RSLocation->fields['location_permalink']."'>Show ".($total_records1-1)." More Offers</a>"?>
					  </div>
					<?php
					}
					?>					
                  </div>
                           <?php
                         
							//$deal_distance .= "<div class='websiteinfo'>";
					$share_link = "";		
                    if($RSLocation->fields['website'] != "")
                    {
                        $share_link .= "<a target='_blank' href='".$RSLocation->fields['website']."' class='webiteclass'>Website </a>";
                    }
					else{
					   $share_link .= "<a  href='javascript:void(0)' class='grayoutclass'>Website </a>";
					}
					if($RSLocation->fields['facebook'] != "")
                    {
						$share_link .= "<a target='_blank' href='".$RSLocation->fields['facebook']."' class='webiteclass'> | Facebook | </a>";
					}
					else
					{
						$share_link .= "<a  href='javascript:void(0)' class='grayoutclass'> | Facebook | </a>";
					}
					if($RSLocation->fields['google'] != "")
					{
						$share_link .= "<a target='_blank' href='".$RSLocation->fields['google']."' rel='publisher' class='webiteclass'>Google+</a>";
					}
					else
					{
						$share_link .= "<a  href='javascript:void(0)' class='grayoutclass'>Google+</a>";
					}
					// $deal_distance .=$share_link;
                    // $deal_distance .= "</div>";
							echo $share_link;
                          //  echo "<br/>";
                           
                           $phno = explode("-",$RSLocation->fields['phone_number']);
                     $newphno = "(".$phno[1].") ".$phno[2]."-".$phno[3];
                          ?>
						  <div  itemscope itemtype="http://schema.org/LocalBusiness"  style="width:300px;">
						  <span itemprop="address" itemscope itemtype="https://schema.org/PostalAddress"> 
<span itemprop="streetAddress"><?php echo $RSLocation->fields['address']; ?></span>,<br/>
<span itemprop="addressLocality"><?php echo $RSLocation->fields['city']; ?></span>,
<span itemprop="addressRegion"><?php echo $RSLocation->fields['state']; ?></span>, 
<span itemprop="addressCountry"><?php echo $RSLocation->fields['country']; ?></span>, 
<span itemprop="postalCode"><?php echo $RSLocation->fields['zip']; ?></span></span><br/>
<span itemprop="telephone"><?php echo $newphno; ?></span>
                          <?php
						  $address = $RSLocation->fields['address'].",<br/> ".$RSLocation->fields['city'].", ".$RSLocation->fields['state'].", ".$RSLocation->fields['country'].",".$RSLocation->fields['zip']."<br /> ".$newphno; 
                           //echo $address; ?>
						   <!--<meta content="US" itemprop="addressCountry">
<meta content="Des Moines" itemprop="addressLocality">
<meta content="WA" itemprop="addressRegion">
<meta content="98198" itemprop="postalCode">
<meta content="23839 Pacific Hwy S" itemprop="streetAddress"> -->
</div>

                           <div class="location_hrs">
                            <?php
                              /*
                 Location Hours Code
                 */
                   $location_time="";
                   
                  // $location_time.="Today 12:00PM - 9:00PM";
                   
                   if($RSLocation->fields['timezone_name'] != "")
                   {
                        //$location_time.=$Row->timezone_name;
                        $time_zone=$RSLocation->fields['timezone_name'];
                   }
                   date_default_timezone_set($time_zone);
                   $current_day=date('D');
                   $current_time=date('g:i A');
                   
                   /* $sql="select * from location_hours where location_id=".$RSLocation->fields['id']." ORDER BY FIELD(day, 'mon', 'tue', 'wed', 'thu', 'fri', 'sat','sun')" ; 
                   $RS_hours_data = $objDB->execute_query($sql); */
                   $RS_hours_data = $objDB->Conn->Execute("select * from location_hours where location_id=? ORDER BY FIELD(day, 'mon', 'tue', 'wed', 'thu', 'fri', 'sat','sun')",array($RSLocation->fields['id']));
                   
                   $location_time="";
                   $start_time = "";
                   $end_time="";
                   $status_time="";
				   
					if($RS_hours_data->RecordCount()>0){
					
						echo "<a href='javascript:void(0)' >Location Hours</a><div class='locationhours1' style='display:none;'>";
						
						while($Row_data = $RS_hours_data->FetchRow()){
							$start_time = $Row_data['start_time'];
							$end_time=$Row_data['end_time'];
							$location_time.=$Row_data['start_time']." - ";
							$location_time.=$Row_data['end_time'];
							echo "<span><p>".$Row_data['day']."</p>".$Row_data['start_time']." - ".$Row_data['end_time']."</span>";
						}
						echo "</div>";
					}
                   
                           
                   
					if ($RSLocation->fields['is_open']==1) 
					{ 
					  $status_time.="<span class='cur_open'>".$client_msg['common']['label_Currently_Open']."</span>";

					}
					else 
					{ 
					   $status_time.="<span class='cur_close'>".$client_msg['common']['label_Currently_Close']."</span>";
					}
					
                    echo $status_time;     
                 
                /*
                 End Of Location Hours Code
                 */
                   ?>   
                   </div>       
			   <?php
				if($from_lati!="" && $from_long!="")
				{
					echo "<div id='milesdiv'><b>Miles Away : </b>".$objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M")." </div>";
					//echo $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M") . "</div>";
				}
				else
				{
					echo "<div id='milesdiv'><b>Miles Away : </b><a id='askmile'>Click here</div>";
				}
			   ?>
				<input type="hidden" id="loc_lat" name="loc_lat" value="<?php echo $to_lati ?>"/>
				<input type="hidden" id="loc_lng" name="loc_lng" value="<?php echo $to_long ?>"/>
				<div id="enter_zipcode_div" style="display:none">
					<div  style="width:423px">
						<div class="zipcode_section" style="margin:10px;">
							<div style="margin-bottom:10px">
								Please enter your current location (City , Country) or Zip code:
							</div>
							<div>
								<input type="text" name="enter_zip" id="enter_zip" value="" onKeyUp="popup_searchdeal(this.value,event)"/> 
								&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="button" name="searchdeal_zip" id="searchdeal_zip" value="OK" />
								<input type="button" name="searchdeal_zip_cancel" id="searchdeal_zip_cancel" value="Cancel" />
							</div>   
						</div>
					</div>
				</div>
						   <a href="<?php echo $maphref; ?>" target="_blank" class="get_dire">Get Directions</a>
                      </div>
                          
                      </div>
                  </td>
              </tr>
              <tr>
               <td class="lst_how_to" >
              <div class="div_how_to" ><?php echo $client_msg['campaign']['label_How_Redeem'];?></div>
				<p class="p_how_to">
					<!--Get the <b><?php echo $client_msg['campaign']['label_Scanflip_App'];?></b><?php echo $client_msg['campaign']['label_Scanflip_App_Content'];?>-->
					Get the <b>Scanflip App</b> on your <a target="_new" href="<?php echo $client_msg["index"]["android_app_link"];?>"><span class="icon-android-camp"></span>Android</a> or <a target="_new" href="<?php echo $client_msg["index"]["iphone_app_link"];?>"><span class="icon-apple"></span>iPhone</a> to redeem in-store. You can also print your voucher from my offer page of your Scanflip account.
				</p>
              </td>
              </tr>
            </table>
            <!--end of detail--></div>
          <!--end of dealDetailDiv--></div>        
        <!--end of dealDetail--></div>
        </div>
      </div>
   <?php 
   /*
   } 
   else
   {
       ?>
    <div class="mainside" style="height:400px">&nbsp;</div>
    <?php
   }
   */
   ?>
      <!--end of content--></div>
    <!--end of contentContainer--></div>
  <!--start footer-->
  <? require_once(CUST_LAYOUT."/footer.php");?>
</div>

<!--- sharing popup -->
 <div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
 <div id="NotificationBackDiv" class="divBack"></div>
    <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">                                            
            <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading" style="left:32%;top: 11%;">
                <div class="modal-close-button" style="visibility: visible;"><a tabindex="0" onclick="close_popup('Notification');" id="fancybox-close" style="display:inline;"></a></div>
                <div id="NotificationmainContainer" class="innerContainer" style="height:366px;width:448px;background:none;">
                    <div class="main_content">
                        <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                            <div class="campaign_detail_div" style="">
                                <form action="<?php echo WEB_PATH; ?>/process.php" method="post" enctype="multipart/form-data">
                                    <div id="activatesahreDiv" class="div_share_friend" style="background-image:none;border:2px solid #EAEAEA;height:356px;text-align:left">
                                  
                                    <div class="head">
					<?php $img_src=ASSETS_IMG."/c/popup_logo.png";?>
                                        <img src="<?php echo $img_src;?>" alt="Scanflip"/><br/>
                                       
                                    </div>
                                    <div class="share_friends">Share With Friends</div>
                                    <div class="share_msg">To Share this offer with friends,complete the from below.</div>
                                    <div class="message_pop">
                                        <strong>Message :</strong><br/>
                                        <strong>I thought</strong> you might enjoy knowing this deals from Scanflip
                                    </div>        
                                   
                                    <div class="notice_mail">Specify up to 50 of your friend's email below separated by commas (,) or semicolons (;)</div>
                                      <p style="color:#FF0000;"></p>
                                    <div class="text_area"><textarea rows="5" cols="50"  name="txt_share_frnd" id="txt_share_frnd"></textarea></div>
                                   
                                    <div class="buttons_bot">
                                        <input type="hidden" name="reffer_campaign_id" id="reffer_campaign_id" value="<?=$decode_campaign_id?>" />
                                        <input type="hidden" name="reffer_campaign_id" id="reffer_campaign_id" value="<?=$decode_campaign_id?>" />
                                        <input type="hidden" name="reffer_campaign_name" id="reffer_campaign_name" value="<?php echo $camp_title; ?>" />
                                        <input type="hidden" name="refferal_location_id" value="<?php echo $decode_location_id; ?>" id="refferal_location_id" />
										<input type="hidden" name="medium" id="medium" value="" />
										<input type="hidden" name="domain" id="domain" value="4" />
										<input type="hidden" name="timestamp" id="timestamp" value="" />
                                        <input type="submit" value="Share" name="btn_share" id="btn_share" />
                                        <input type="button" value="Cancel" onclick="close_popup('Notification');" id="btn_cancel"  />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
  </div>

  <!--- end of sharing popup --->
  
  <!--- activation popup -->
<div id="activationPopUpContainer" class="container_popup"  style="display: none;">
        <div id="activationBackDiv" class="divBack"></div>
        <div id="activationFrontDivProcessing" class="Processing" style="display:none;">            
             <div id="activationMaindivLoading" align="center" valign="middle" class="imgDivLoading" style="left:32%;top: 11%;">                
                <div class="modal-close-button" style="visibility: visible;">                
                    <a  tabindex="0" onclick="close_popup('activation');" id="fancybox-close" style="display: inline;right:-22px;top:20px"></a>
                </div>
                <div id="activationmainContainer" class="innerContainer" style="height:209px;width:427px">
                        <div class="main_content">                       
                            <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                                <div class="campaign_detail_div" style="">
                                <div id="activatesahreDiv" class="div_share_friend" style="background-image:none;border:2px solid #C3E1FF">
                                <p align="center" style="color:#FF0000;"></p>
                              <b><?php echo $language_msg["activation"]["enter_activation_code"];?>: </b>
                                <form  method="post">
                                       <?php if(isset($decode_location_id)){?>
                                <input type="hidden" name="campid" id="campid" value="<?php echo $decode_campaign_id; ?>" />
                                <input type="hidden" name="loc_id" id="loc_id" value="<?php  echo $decode_location_id; ?>" />
                                <?php }// else { ?>
                                 <p><input class="activateForm" type="text" style="width:370px; " value="<?php if(isset($_REQUEST['activation_code'])){echo $_REQUEST['activation_code'];} ?>" name="activation_code" id="activation_code" /></p>
                                 <input type="submit" name="btnActivationCode_" id="btnActivationCode_" value="<?php echo $language_msg["activation"]["activation_code"];?>" />
                            </form>
                        </div>
                                </div>
    
                          
    
                            </div>
                          
                        </div>
                 </div>
            </div>
       </div> 
  </div>
  <!--- end of activation popup --->
  
  <!---error activation popup -->
<div id="activationerrorPopUpContainer" class="container_popup"  style="display: none;">
                                        <div id="activationerrorBackDiv" class="divBack">
                                        </div>
                                        <div id="activationerrorFrontDivProcessing" class="Processing" style="display:none;">
                                            
                                             <div id="activationerrorMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                                              style="left:32%;top: 11%;">
                                                
                                                                <div class="modal-close-button" style="visibility: visible;">
                                                             
                                                                    <a  tabindex="0" onclick="close_popup('activationerror');" id="fancybox-close" style="display: inline;right:-22px;top:20px"></a>
                                                                </div>
                                                <div id="activationerrormainContainer" class="innerContainer" style="height:209px;width:427px">
                                                        <div class="main_content"> 	
                                                       
                                                            <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                                                                <div class="campaign_detail_div" style="">
                                                                <div id="activatesahreDiv" class="div_share_friend" style="background:none;width:auto;height:auto">
                                                                <p align="center" style="color:#FF0000;"></p>
                                                                <?php
                                                                if($cust_in_same_group_of_camp==0)
                                                                { ?>
                                                                    <b><?php echo $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];?></b>
																<?php    
															    }
                                                                else
																{
                                                                    if($onepercust_flag == 0)
                                                                    {
																	?>
                                                                            <b><?php echo $client_msg['campaign']['Msg_Already_Redeemed'];?></b>
                                                                       <?php 
																	}
																	if($o_left<=0 ) 
																	{    
																	    // 07-09-2013 o p c and redeem then no need to give msg ol message
																		/*
                                                                        if($onepercust_flag == 0)
                                                                        {
																		?>
                                                                            <b>Sorry no offer left for <?php echo $RS[0]->title ; ?>.</br>Please check other offers from merchant.</b>
                                                                        <?php 
																	    }
																		*/
																		// 07-09-2013 o p c and redeem then no need to give msg ol message
                                                                        if($oneperday_multi_flag  == 0)
                                                                        {
																		?>
                                                                            <b><?php echo $client_msg['campaign']['Msg_offer_left_zero'];?></b>
                                                                        <?php
                                                                        }
                                                                        if($onepercust_flag != 0 && $oneperday_multi_flag != 0)
                                                                        {
                                                                         ?>
																			<b><?php echo $client_msg['campaign']['Msg_offer_left_zero'];?></b>
																		<?php
                                                                        }                                                               
                                                                    }
                                                                 else  if( $share_flag== 0)
                                                                {?>
                                                                  <b><?php echo $client_msg['search_deal']['New_Customers_Only'] ?></b>   
                                                                <?php }
                                                                 else if($is_active==0) {
                                                                ?>
                                                               <b><?php echo $client_msg['campaign']['Msg_Campaign_Is_Expired'];?></b>
                                                               <?php
                                                                 }
                                                                }
                                                                 
                                                                ?>
																<div style="margin-top:15px;">
                                                               <form action="<?php echo WEB_PATH; ?>/process.php" method="post">
                                                                   <input type="hidden" name="hdn_campaign_id" id="hdn_campaign_id" value="<?=$decode_campaign_id?>" />
                                                                   <input type="hidden" name="hdn_location_id" id="hdn_location_id" value="<?=$decode_location_id?>" />
                                                                       
                                                                   <input type="submit" name="getmoremercahntdeals" id="getmoremercahntdeals" value="OK" />
                                                               </form>
															   
                                                        </div>
                                                                </div>
								
                                                          
							
                                                            </div>
                                                          
                                                        </div>
                                                 </div>
                                            </div>
                                       </div> 
  </div>
</div>
  <!--- end of error activation popup --->
  
  <!-- Sharing Ajax loding -->
   
  
  <!-- End Sharing Ajax loding -->
  
   <?php  if(isset($_SESSION['customer_id'])){
        $JSON = $objJSON->get_customer_profile();
$RS = json_decode($JSON);
?>
    <div id="div_profile" style="display:none">
        <input type="hidden" name="subscribe_location_id" id="subscribe_location_id" value="" />
     <div class="updateprofile" id="updateprofile" style="display:block;padding:11px;" >
                    <div id="modal-login" style="height:310px;text-align:left;width:386px;line-height:17px">
                        <h2 id="modal-login-title" style="margin-bottom: 30px;color: black !important;font-size: 1.6923em;font-weight: normal;line-height: 0.818em;">Update Profile</h2>
              
                        <div class="calltoaction callout unit100" style="display: none">
                         Your Profile is not set. Set it Now.
                        </div>
                        <div id="form_login">
                                <form class="form_vertical" action="<?php echo WEB_PATH; ?>/process.php" method="post"  id="login_frm">
                              
                                  <label for="email-modal"><?php echo "* ".$language_msg["profile"]["gender"];?></label>
                                  <select name="gender" id="gender"   style="padding:0.231em;margin:0 0 0.154em;" class="genderclass"  >
                                        <option></option>
                                        <option value="1" <?php if($RS[0]->gender == 1){ echo "selected";} ?> >Male</option>
                                        <option value="2" <?php if($RS[0]->gender == 2){ echo "selected";} ?> >Female</option>
                                </select>
                                  <div class="err_gender" style="color:red;height: 18px"></div>
                             
                                  <label for="dob"><?php echo "* ".$language_msg["profile"]["date_of_birth"];?></label>
                                 <select name="dob_month" id="dob_month" class="dateofmonth"  style="margin:0 0 0.154em;">
                                    <option></option>
		<?
		
		
		for($i=1; $i<=12; $i++){	
		?>
			<option value="<?=$i?>" <? if($RS[0]->dob_month == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
		<?
		}
		?>
	</select>
	-
	<select name="dob_day" id="dob_day" class="dateofday"  style="margin:0 0 0.154em;">
            <option></option>
		<?
		for($i=1; $i<=31; $i++){	
		?>
			<option value="<?=$i?>" <? if($RS[0]->dob_day == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
		<?
		}
		?>
	</select>
	- 
	<select name="dob_year" id="dob_year" class="dateofyear"  style="margin:0 0 0.154em;">
            <option></option>
		<?
		for($i=date("Y")-60; $i<=date("Y"); $i++){	
		?>
			<option value="<?=$i?>" <? if($RS[0]->dob_year == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
		<?
		}
		?>
	</select>
        <div class="err_dob" style="color:red;height: 18px"></div>
                             
                                   <label for="country">* Country</label>
                                  <select name="country" id="country" class="countryclass" style="margin:0 0 0.154em;">
                                 <option></option>    
		<option value="USA" <? if($RS[0]->country == "USA") echo "selected";?> country_code="US">USA</option>
		<option value="Canada" <? if($RS[0]->country == "Canada") echo "selected";?>  country_code="CA">Canada</option>
	   
	</select>
                                  <div class="err_country" style="color:red;height: 18px"></div>
                              
                                   <label for="postalcode">* Postal Code</label>
                                   <input type="text" maxlength="15"  name="postalcode" id="postalcode" class="postalcodeclass" style="padding:0.231em;margin:0 0 0.154em;" value="<?=$RS[0]->postalcode?>">
<!--                                   <input type="text" name="postalcode" id="postalcode" style="width:120px;" value=""  class="unit100" style="padding:0.231em;" >-->
                                  
                                   <div class="err_postalcode" style="color:red;height: 18px"></div>
                                  <div>
                                   
                                    <p class="actions" style="" align="center">
            	<input type="button" id="btnupdateprofile" name="btnupdateprofile" value="Save" onClick="">
                <input type="button" class="btncancelprofile" value="Cancel" id="btncancelprofile"  />
            </p>
                                  </div>
                                </form>
              
                            </div>
                 </div>
        </div>
        </div>
    <?php } ?>
      <div id="wrong_file_data" style="display:none;width:400px;height:320px">
                        <?php
                        $sql_location="select * from campaign_location where campaign_id='".$decode_campaign_id."' and offers_left>=1 and active=1";
                       
                        $RS = $objDB->Conn->Execute($sql_location);
                         if($RS->RecordCount()<=0){?>
                            
                            <form  action="search-deal.php" method="post" enctype="multipart/form-data">
                                  <div style="height:auto;text-align:center;padding:10px;font-size:15px;margin-right: 19px;width:400px;"><?php echo $client_msg['campaign']['Msg_Sorry_All_Voucher_Codes'];?>
                                  </div>
					<div style="margin-top:16px;margin-bottom: 10px;" align="center">
                                            
                                            <input type="submit" class="popupcancel" name="popupcancel" id="popupcancel" value="Cancel" />
                                        </div>
                        </form>
                         <?php }
                         
                  
                            
                        // }
                        ?>
					
                </div>
				<!--
<div id="enter_zipcode_div" style="display:none">
        <div  style="width:423px">
  
        <div class="zipcode_section" style="margin:10px;display:none;">
                <div style="margin-bottom:10px">Please enter your current location (City , Country) or Zip code:</div>
        <div>
            <input type="text" name="enter_zip" id="enter_zip" value="" style="width:255px" onkeyup="popup_searchdeal(this.value,event)"/> 
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" name="searchdeal_zip" id="searchdeal_zip" value="Get Location" />
        </div>   
        </div>
        <div class="zipcodeerror_section"  style="display:none;margin:10px;" align="center">
            <div>Please enter valid current location (City, Country) or Zip code to search offers</div>
               <br/>
                 <input type="submit" name="searchdeal_cancle" id="searchdeal_cancle" value="OK" />
        </div>
        </div>
    </div>
	-->
  <div id="dialog-message" title="Message Box" style="display:none">

    </div>
  <div id="sharingPopUpContainer" class="container_popup"  style="display: none;">
 <div id="sharingBackDiv" class="divBack"></div>
    <div id="sharingFrontDivProcessing" class="Processing" style="display:none;">                                            
            <div id="sharingMaindivLoading" align="center" valign="middle" class="imgDivLoading" style="left:32%;top: 11%;">
               
                <div id="sharingmainContainer" class="innerContainer" style="background:none; left: 45%;position: fixed;top: 50%;width: 100%;">
                    <div class="main_content">
                        <div class="message-box message-success" id="jqReviewHelpfulMessagesharing" style="display: block;height:30px;">
                            <div class="campaign_detail_div" style="">
                                <img src="<?php echo ASSETS_IMG ;?>/c/<?php echo $client_msg['common']['common_image_loader_name']; ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php if(isset($_SESSION['customer_id'])){
    $customer_id_p = $_SESSION['customer_id'];
} else{
    $customer_id_p = "";
}
?>
<?php
if(isset($_SESSION['customer_id']))
{
?>
<script>
try
{
 twttr.events.bind('tweet', function(event) {
        location.replace(location.href + "&twitshare=1");

 });
 }
 catch(e)
 {
 }
</script>
<?php
}
?>
<script>
     jQuery("#btncancelprofile").live("click",function(){
       jQuery.fancybox.close();
    });
    function del_cookie(name)
{
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
      jQuery(document).ready(function(){
          
      
        //alert(jQuery(".reserve_print").length);
//        var cookies_value="<?php if(isset($_COOKIE['is_scanflip_scan_qrcode']))echo $_COOKIE['is_scanflip_scan_qrcode'];?>";
//       
//        if(cookies_value != "")
//        {
//          
//           var offerleft="<?php echo $o_left;?>";
//           var locationid="<?php echo $decode_location_id;?>";
//            var campaignid="<?php echo $decode_campaign_id;?>";
//			var allofferleft="<?php echo $RS->RecordCount();?>";
//            if(jQuery(".reserve_print").length != 0)            
//                {
//           if(offerleft == 0)
//           {
//             
//			 if(allofferleft<=0)
//			 {
//				  jQuery.fancybox({
//		   		   content:jQuery('#wrong_file_data').html(),
                   // href:"<?=WEB_PATH?>/process.php?getMapForLocation=yes&locationid="+locationid+"&campaignid="+campaignid,
//                    width: 435,
//                    height: 345,
//                   
//                    openEffect : 'elastic',
//                    openSpeed  : 300,
//                    closeEffect : 'elastic',
//                    closeSpeed  : 300,
//                    // topRatio: 0,
//
//                    changeFade : 'fast',  
//					 beforeShow:function(){
//						jQuery(".fancybox-inner").addClass("Class_for_activation");
//					},
//					afterClose: function () {
//			         
//                     window.location.href = "<?=WEB_PATH?>/search-deal.php";
//            		},					
//                    helpers:  {
//                            overlay: {
//                            opacity: 0.3
//                            } // overlay
//                    }
//           
//		});
//			 }
//			 else
//			 {
//               jQuery.fancybox({
//		   // content:jQuery('#wrong_file_data').html(),
//                    href:"<?=WEB_PATH?>/process.php?getMapForLocation=yes&locationid="+locationid+"&campaignid="+campaignid,
//                    width: 435,
//                    height: 345,
//                    type: 'iframe',
//                    openEffect : 'elastic',
//                    openSpeed  : 300,
//                    closeEffect : 'elastic',
//                    closeSpeed  : 300,
//                    // topRatio: 0,
//
//                    changeFade : 'fast',  
//					 beforeShow:function(){
//						jQuery(".fancybox-inner").addClass("Class_for_activation");
//					},
//                    helpers:  {
//                            overlay: {
//                            opacity: 0.3
//                            } // overlay
//                    }
//           
//		});
//			 }
//           }
//           
//    
//            
//        }
//           del_cookie("is_scanflip_scan_qrcode");
//        }
         if(jQuery("#div_profile #postalcode").val() == "")
             {
                 jQuery("#is_profileset").val("0");
             }
             else{
                 jQuery("#is_profileset").val("1");
             }
             
             jQuery('.genderclass').live("change",function () {
		
		jQuery(".genderclass option:selected").each(function(){
		        jQuery(".genderclass option").removeAttr("selected");
			if (jQuery(this).text() != "")
			{
			  jQuery(this).attr("selected","selected");
			}
			
		    });
		  //jQuery(this).attr("selected","selected");
	    });
	    jQuery('.dateofmonth').live("change",function () {
		
		jQuery(".dateofmonth option:selected").each(function(){
		        jQuery("#dob_month option").removeAttr("selected");
			if (jQuery(this).text() != "")
			{
			  
			  jQuery(this).attr("selected","selected");
			  //alert(jQuery(this).val());
			}
		    });
		  //jQuery(this).attr("selected","selected");
	    });
	    jQuery('.dateofday').live("change",function () {
		
		jQuery(".dateofday option:selected").each(function(){
		        jQuery(".dateofday option").removeAttr("selected");
			if (jQuery(this).text() != "")
			{
			  
			  jQuery(this).attr("selected","selected");
			  //alert(jQuery(this).val());
			}
		    });
		  //jQuery(this).attr("selected","selected");
	    });
	    jQuery('.dateofyear').live("change",function () {
		
		jQuery(".dateofyear option:selected").each(function(){
		        jQuery(".dateofyear option").removeAttr("selected");
			if (jQuery(this).text() != "")
			{
			  
			  jQuery(this).attr("selected","selected");
			  //alert(jQuery(this).val());
			}
		    });
		  //jQuery(this).attr("selected","selected");
	    });
	    jQuery('.countryclass').live("change",function () {
		
		jQuery(".countryclass option:selected").each(function(){
		        jQuery(".countryclass option").removeAttr("selected");
			if (jQuery(this).text() != "")
			{
			  
			  jQuery(this).attr("selected","selected");
			  //alert(jQuery(this).val());
			}
		    });
		  //jQuery(this).attr("selected","selected");
	    });
      });
    function isValidPostalCode(postalCode, countryCode) {
    switch (countryCode) {
        case "US":
            postalCodeRegex = /^([0-9]{5})(?:[-\s]*([0-9]{4}))?$/;
            break;
        case "CA":
            postalCodeRegex = /^([A-Z][0-9][A-Z])\s*([0-9][A-Z][0-9])$/;
            break;
        default:
            postalCodeRegex = /^(?:[A-Z0-9]+([- ]?[A-Z0-9]+)*)?$/;
    }
    return postalCodeRegex.test(postalCode);
}
    jQuery("body").on("click",".fancybox-inner #btnupdateprofile",function(){
	 
        var flag = true;
        var country_code = jQuery(".fancybox-inner #country option:selected").attr("country_code") ;
        var zip = jQuery(".fancybox-inner #postalcode").val().toUpperCase();
		
        //var gender=jQuery(".genderclass option:selected").val();
	var first_name = jQuery(".fancybox-inner .firstnameclass").val();
	
	var last_name = jQuery(".fancybox-inner #lastname").val();
	
	
	var gender = jQuery(".genderclass").find('option:selected').text();
	
	var dob_month=jQuery(".dateofmonth").find('option:selected').text();
	var dob_day=jQuery(".dateofday").find('option:selected').text();
	var dob_year=jQuery(".dateofyear").find('option:selected').text();
	var country=jQuery(".countryclass").find('option:selected').text();
	var state=jQuery(".fancybox-inner #state").find('option:selected').text();
	
	var city = jQuery(".fancybox-inner #city").val();
	
	//var postalcode=jQuery(".postalcodeclass").val();
	
	
	
	
	
	 
	/*if(isValidPostalCode(zip,country_code)){
           flag = true;
	 
            jQuery(".fancybox-inner .err_postalcode").text("");
        }
        else{
	    
              jQuery(".fancybox-inner .err_postalcode").text("Please Input Valid Postal Code");
            flag = false;
        }*/
    
	if (first_name == "") {
	    
	    
	    jQuery(".fancybox-inner .err_firstname").text("<?php echo $client_msg['login_register']['Msg_First_Name']?>");
        flag = false;
	}
	else
	{
	    
	    jQuery(".fancybox-inner .err_firstname").text("");
            flag = true;
	}
	if (last_name == "") {
	    
	    
	    jQuery(".fancybox-inner .err_lastname").text("<?php echo $client_msg['login_register']['Msg_Last_Name']?>");
        flag = false;
	}
	else
	{
	    
	    jQuery(".fancybox-inner .err_lastname").text("");
            flag = true;
	}	
	
	if (gender == "") {
	    
	    
	    jQuery(".fancybox-inner .err_gender").text("<?php echo $client_msg['login_register']['Msg_Select_Gender']?>");
            flag = false;
	}
	else
	{
	    
	    jQuery(".fancybox-inner .err_gender").text("");
            flag = true;
	}
	if (dob_month == "" || dob_day == "" || dob_year == "") {
	    
	    jQuery(".fancybox-inner .err_dob").text("<?php echo $client_msg['login_register']['Msg_Select_Date_Of_Birth']?>");
            flag = false;
	}
	else
	{
	    jQuery(".fancybox-inner .err_dob").text("");
            flag = true;
	}
	
	if (country == "") {
	    jQuery(".fancybox-inner .err_country").text("<?php echo $client_msg['login_register']['Msg_Select_Country']?>");
            flag = false;
	}
	else
	{
	    jQuery(".fancybox-inner .err_country").text("");
            flag = true;
	}
	if (state == "") {
	    jQuery(".fancybox-inner .err_state").text("<?php echo $client_msg['login_register']['Msg_Select_State']?>");
            flag = false;
	}
	else
	{
	    jQuery(".fancybox-inner .err_state").text("");
            flag = true;
	}
	if (city == "") {
	    jQuery(".fancybox-inner .err_city").text("<?php echo $client_msg['login_register']['Msg_Select_City']?>");
            flag = false;
	}
	else
	{
	    jQuery(".fancybox-inner .err_city").text("");
            flag = true;
	}
	
	if (zip == "") {
	    jQuery(".fancybox-inner .err_postalcode").text("<?php echo $client_msg['login_register']['Msg_Enter_Postal_Code']?>");
            flag = false;
		
	}
	else
	{
	    if(isValidPostalCode(zip,country_code)){
		flag = true;
	     
		 jQuery(".fancybox-inner .err_postalcode").text("");
	     }
	     else{
		 
		   jQuery(".fancybox-inner .err_postalcode").text("<?php echo $client_msg['login_register']['Msg_Input_Valid_Postal_Code']?>");
		 flag = false;
	     }	
	    //jQuery(".fancybox-inner .err_postalcode").text("");
            //flag = true;
	}
	if (gender == "" || dob_month == "" || dob_day == "" || dob_year == "" || country == "" || first_name == "" || last_name == "" || state=="" || city=="")
	{
	    flag = false;
	}
	
	
       
	//	return false;
          if(flag)
              {
          $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnUpdateProfile_compulsary_field=true&customer_id=<?php if(isset($_SESSION['customer_id']))echo $_SESSION['customer_id'] ?>&country='+$(".fancybox-inner #country").val()+'&gender='+$(".fancybox-inner #gender").val()+'&dob_month='+$(".fancybox-inner #dob_month").val()+'&dob_day='+$(".fancybox-inner #dob_day").val()+'&dob_year='+$(".fancybox-inner #dob_year").val()+'&postalcode='+$(".fancybox-inner #postalcode").val()+'&firstname='+$(".fancybox-inner #firstname").val()+'&lastname='+$(".fancybox-inner #lastname").val()+'&state='+$(".fancybox-inner #state").val()+'&city='+$(".fancybox-inner #city").val(),
                  async:false,
		  success:function(msg)
		  {
                      jQuery("#is_profileset").val("1");
                      trigger_val = jQuery("#profile_view").val();
					 // alert(jQuery("#print_link").attr("href"));
                      
					  if(trigger_val == "print_link")
					  {
						window.location.href = jQuery("#print_redirect_link").val();
					  }
					  else{
						jQuery("#"+trigger_val).trigger("click");
					  }
                        jQuery.fancybox.close(); 
						
						//jQuery("#print_link").trigger("click");
						//jQuery("#print_link").trigger("click");
                       
                  }
          });
        }
    });
    jQuery(".fancybox-inner #btnupdateprofile1").live("click",function(){
     //   alert("Save function");
        var flag = true;
        var country_code = jQuery(".fancybox-inner #country option:selected").attr("country_code") ;
        var zip = jQuery(".fancybox-inner #postalcode").val().toUpperCase();
        var gender = jQuery(".genderclass").find('option:selected').text();
	var dob_month=jQuery(".dateofmonth").find('option:selected').text();
	var dob_day=jQuery(".dateofday").find('option:selected').text();
	var dob_year=jQuery(".dateofyear").find('option:selected').text();
	var country=jQuery(".countryclass").find('option:selected').text();
        
        /*if(isValidPostalCode(zip,country_code)){
           flag = true;
            jQuery(".fancybox-inner .err_postalcode").text("");
        }
        else{
              jQuery(".fancybox-inner .err_postalcode").text("Please Input Valid Postal Code");
            flag = false;   
        }*/
        if (gender == "") {
	    
	    
	    jQuery(".fancybox-inner .err_gender").text("Please Select Gender");
            flag = false;
	}
	else
	{
	    
	    jQuery(".fancybox-inner .err_gender").text("");
            flag = true;
	}
	if (dob_month == "" || dob_day == "" || dob_year == "") {
	    
	    jQuery(".fancybox-inner .err_dob").text("Please Select Date of Birth");
            flag = false;
	}
	else
	{
	    jQuery(".fancybox-inner .err_dob").text("");
            flag = true;
	}
	
	if (country == "") {
	    jQuery(".fancybox-inner .err_country").text("Please Select Country");
            flag = false;
	}
	else
	{
	    jQuery(".fancybox-inner .err_country").text("");
            flag = true;
	}
	if (zip == "") {
	    jQuery(".fancybox-inner .err_postalcode").text("Please Enter Postal Code");
            flag = false;
	}
	else
	{
	    if(isValidPostalCode(zip,country_code)){
		flag = true;
	      
		 jQuery(".fancybox-inner .err_postalcode").text("");
	     }
	     else{
		 
		   jQuery(".fancybox-inner .err_postalcode").text("Please Input Valid Postal Code");
		 flag = false;
	     }	
	    //jQuery(".fancybox-inner .err_postalcode").text("");
            //flag = true;
	}
	if (gender == "" || dob_month == "" || dob_day == "" || dob_year == "" || country == "" )
	{
	    flag = false;
	}
        
        
          if(flag)
              {
          $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnUpdateProfile_compulsary_field=true&customer_id=<?php echo $customer_id_p ?>&country='+$(".fancybox-inner #country").val()+'&gender='+$(".fancybox-inner #gender").val()+'&dob_month='+$(".fancybox-inner #dob_month").val()+'&dob_day='+$(".fancybox-inner #dob_day").val()+'&dob_year='+$(".fancybox-inner #dob_year").val()+'&postalcode='+$(".fancybox-inner #postalcode").val(),
                  async:false,
		  success:function(msg)
		  {
                      jQuery("#is_profileset").val("1");
                      trigger_val = jQuery("#profile_view").val();
                      jQuery("#"+trigger_val).trigger("click");
                        jQuery.fancybox.close(); 
//                      if(jQuery("#profile_view").val()=="rserve")
//                          {
//                       $(".fancybox-inner .btn_mymerchantreserve").trigger("click");
//                         jQuery(".fancybox-inner .popupmainclass").css("display","block");
//                         jQuery(".fancybox-inner .email_popup_div").css("display","none");
//                         jQuery(".fancybox-inner .mainloginclass").css("display","none");
//                         jQuery(".fancybox-inner .updateprofile").css("display","none");
//                          }
//                        if(jQuery("#profile_view").val()=="subscribe")
//                        {
//                                 jQuery(".subscribestore[s_lid='"+jQuery(".fancybox-inner #subscribe_location_id").val()+"']").trigger("click");
//                                 jQuery.fancybox.close(); 
//
//                        }
                       
                  }
          });
        }
    });
    $("#directly_reserve").click(function(){
     //  alert("IN reserve deal functionality");
       var  is_profileset_val=jQuery("#is_profileset").val();
       
       //is_profileset_val=0;
        if(is_profileset_val != 1){
            var redirect_url="";
           
     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'is_userprofileset=true&custome_id=<?php echo $customer_id_p; ?>',
                  async:false,
		  success:function(msg)
		  {
                    var obj = jQuery.parseJSON(msg);
                    
                   if (obj.loginstatus=="false") {
                        //alert("1");
                        //var pathname = "<?php echo WEB_PATH;?>register.php?url="<?php echo WEB_PATH;?>"+window.location.pathname+"?campaign_id=<?php echo $decode_campaign_id?>&l_id=<?php echo $decode_location_id?>";
                        //alert(pathname);
                        parent.window.location.href=obj.link;
                    }
                    else
                    {
                    //    alert("2");                    
                      if(obj.is_profileset == 1)
                      {
					//  alert("4");
                          flag= true;
                      }
                    
                  else{
				//  alert("5");
                       jQuery("#profile_view").val("directly_reserve");
                        flag = false;
                    jQuery.fancybox({
				//href: this.href,
				
				//href: $(val).attr('mypopupid'),
				
				content:jQuery('#div_profile').html(),
				
				width: 435,
				height: 345,
				type: 'html',
				openEffect : 'elastic',
				openSpeed  : 300,
                                 
				closeEffect : 'elastic',
				closeSpeed  : 300,
				// topRatio: 0,
               
                                changeFade : 'fast',  
				beforeShow : function(){
				 
                                   //jQuery(".fancybox-inner #subscribe_location_id").val(locid);
                                },
								afterShow:function(){
								jQuery(".fancybox-inner #login_frm").html(jQuery("#notprofilesetid").html());
								},
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
                                
                    });
                      //alert("You Profile IS not set. First set it");
                  }
            }
                  }
     });
     
        if(! flag)
        {
            return false;
        }
     }
	// alert("5");
        var ele  = $(this);
      $.ajax({
           type: "POST",
                                      url: "<?=WEB_PATH?>/process.php",
                                      data: "reserve_sub_deal=yes&campaign_id=<?php echo $decode_campaign_id ?>&l_id=<?php echo $decode_location_id ?>&barcodea=" + $(this).attr("bval"),
                                      async : false,
                                      success: function(msg) {
                                      //    alert(msg);
									 // alert("6");
                                             var obj = jQuery.parseJSON(msg);
                                             if(obj.status == "false")
                                             {
										//	 alert("7");
                                               // parent.window.location.href=obj.link;
											   var msg="<?php echo $client_msg["campaign"]["Msg_No_Offer_Left"];?>";
                                                               
                                                                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel_no_offer' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,

                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });  
                                             }
                                             else
                                             {
													//alert("8");
                                                      if(obj.status == "true")
                                                       {
													   //alert("9");
                                                             $(".divoffersaveblock").show();
                                                             $(".reserve_print").hide();
															  if(jQuery("#is_profileset").val() != 1){
															 $("#print_link").attr("href","<?php echo WEB_PATH ?>/print_coupon.php?campaign_id=<?php echo $decode_campaign_id ?>&l_id=<?php echo $decode_location_id ?>&barcodea="+ ele.attr("bval"));
															  $("#print_link").addClass("preventprint");
															  }else
															  {
															  $("#print_link").attr("href","<?php echo WEB_PATH ?>/print_coupon.php?campaign_id=<?php echo $decode_campaign_id ?>&l_id=<?php echo $decode_location_id ?>&barcodea="+ ele.attr("bval"));
															  }
                                                            
                                                            $(".offer_left p").text(obj.o_left);
                                                       }
                                                       else{
                                                          // alert("10");
                                                          // alert("Sorry , No Offers left");
                                                          var msg="<?php echo $client_msg["campaign"]["Msg_No_Offer_Left"];?>";
                                                               
                                                                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,

                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });  
                                                        }
                                             }
                                      }
       });
    });
$("#btnActivationCode_").click(function(){
if(jQuery("#is_profileset").val() != 1){
			$.ajax({
					type:"POST",
					url:'<?php echo WEB_PATH; ?>/process.php',
					data :'is_userprofileset=true&custome_id=<?php echo $customer_id_p; ?>',
					async:false,
			success:function(msg)  {
                       var obj = jQuery.parseJSON(msg);
                      
                      if(obj.is_profileset == 1)
						{
                          flag= true;
						}
					else {
                       jQuery("#profile_view").val("rserve");
                        flag = false;
						jQuery.fancybox({
							//href: this.href,
				
								//href: $(val).attr('mypopupid'),
				
							content:jQuery('#btnActivationCode_').html(),
				
							width: 435,
							height: 345,
							type: 'html',
							openEffect : 'elastic',
							openSpeed  : 300,
                                 
							closeEffect : 'elastic',
							closeSpeed  : 300,
							changeFade : 'fast',  
				
							beforeShow : function(){
                               //    jQuery(".fancybox-inner #subscribe_location_id").val(locid);
                                },
								afterShow: function(){
								
								jQuery(".fancybox-inner #login_frm").html(jQuery("#notprofilesetid").html());
								},
							helpers: {
									overlay: {
										opacity: 0.3
									} // overlay
							}
                                
						});
                      //alert("You Profile IS not set. First set it");
					}
                }
			});
			if(! flag){
         return false;
			}
     }
		$.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/process.php",
                                      data: "checkactivationcode_for_campaign1=yes&activationcode=" + $("#activation_code").val()+'&camp_id='+$("#campid").val()+"&loc_id="+$("#loc_id").val(),
                                      async : false,
                                      success: function(msg) {
                                         var obj = jQuery.parseJSON(msg);
										// alert(msg);
										 //return false;
                                                      if(obj.status == "true")
                                                       {
                                                           var obj2 = jQuery.parseJSON(obj.act_json);
                                                             $(".divoffersaveblock").show();
                                                             $(".reserve_print").hide();
															 if(jQuery("#is_profileset").val() != 1){
															   $("#print_link").attr("href","<?php echo WEB_PATH ?>/print_coupon.php?campaign_id=<?php echo $decode_campaign_id ?>&l_id=<?php echo $decode_location_id ?>&barcodea="+ obj2.barcode);
															  $("#print_link").addClass("preventprint");
															  }else
															  {
															  $("#print_link").attr("href","<?php echo WEB_PATH ?>/print_coupon.php?campaign_id=<?php echo $decode_campaign_id ?>&l_id=<?php echo $decode_location_id ?>&barcodea="+ obj2.barcode);
															  }
                                                            
                                                             var a_val = $(".offer_left p").text();
                                                             a_val = parseInt(a_val)-1;
                                                             $(".offer_left p").text(a_val );
                                                              flag = true;
                                                       }
                                                       else{
                                                            flag = false;
                                                        // alert("Wrong Activation Code");
                                                        
                                                         var msg="<?php echo $client_msg["campaign"]["Msg_Wrong_Code"];?>";
                                                               
                                                                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,

                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },
																									afterShow : function(){
																									//	alert(jQuery(".fancybox-wrap").length);
								jQuery(".fancybox-wrap").css("zIndex","9999999999999");
								//alert(jQuery(".fancybox-wrap").css("zIndex"));
																									},

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            }); 
                                                         
                                                       }
                                       }
                        });
                        
                        if(flag)
                            {
                               close_popup('activation');
                                //alert("in");
                                return false;
                            }
                            else
                                {
                                    return false;
                                }
});
$("#dont_btn_reserve_deal").click(function(){
	//open_popup('activationerror');
	//alert("hi");
	jQuery.fancybox({
					content:jQuery('#activationerrorPopUpContainer .campaign_detail_div').html(),
					height:'auto',
					width:'auto',
					type: 'html',
					fitToView:true,
					imageScale:true,
					autoSize:false,
					openSpeed  : 300,
					closeSpeed  : 300,
					changeFade : 'fast',  
					helpers: {
						overlay: {
						opacity: 0.3
						} // overlay
					}
		});		  
});
$("#btn_reserve_deal").click(function(){
    
    if(jQuery("#is_profileset").val() != 1){
     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'is_userprofileset=true&custome_id=<?php echo $customer_id_p; ?>',
                  async:false,
		  success:function(msg)
		  {
                       var obj = jQuery.parseJSON(msg);
                      //alert(obj)
                      if(obj.status == 1)
                      {
                          flag= true;
                      }
                  else{
                       jQuery("#profile_view").val("btn_reserve_deal");
                        flag = false;
                    jQuery.fancybox({
				//href: this.href,
				
				//href: $(val).attr('mypopupid'),
				
				content:jQuery('#div_profile').html(),
				
				width: 435,
				height: 345,
				type: 'html',
				openEffect : 'elastic',
				openSpeed  : 300,
                                 
				closeEffect : 'elastic',
				closeSpeed  : 300,
				// topRatio: 0,
               
                                changeFade : 'fast',  
				beforeShow : function(){
                                  // jQuery(".fancybox-inner #subscribe_location_id").val(locid);
                                },
								afterShow:function(){
								jQuery(".fancybox-inner #login_frm").html(jQuery("#notprofilesetid").html());
								},
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
                                
                    });
                      //alert("You Profile IS not set. First set it");
                  }
                  }
     });
     if(! flag)
     {
         return false;
     }
     }
    var attr = $(this).attr("activation_code");
    if (typeof attr == 'undefined' || attr == false) {
			  open_popup('activation');
        }else{
           $.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/process.php",
                                      data: "checkactivationcode_for_campaign1=yes&activationcode=" + $(this).attr("activation_code")+'&camp_id='+$("#campid").val()+"&loc_id="+$("#loc_id").val(),
                                      async : false,
                                      success: function(msg) {
                                         var obj = jQuery.parseJSON(msg);
                                                      if(obj.status == "true")
                                                       {
                                                           var obj2 = jQuery.parseJSON(obj.act_json);
                                                             $(".divoffersaveblock").show();
                                                             $(".reserve_print").hide();
															 if(jQuery("#is_profileset").val() != 1){
															  $("#print_link").attr("href","<?php echo WEB_PATH ?>/print_coupon.php?campaign_id=<?php echo $decode_campaign_id ?>&l_id=<?php echo $decode_location_id ?>&barcodea="+ obj2.barcode);
															  $("#print_link").addClass("preventprint");
															  }else
															  {
															  $("#print_link").attr("href","<?php echo WEB_PATH ?>/print_coupon.php?campaign_id=<?php echo $decode_campaign_id ?>&l_id=<?php echo $decode_location_id ?>&barcodea="+ obj2.barcode);
															  }
                                                            
                                                             var a_val = $(".offer_left p").text();
                                                             a_val = parseInt(a_val)-1;
                                                             $(".offer_left p").text(a_val );
                                                              flag = true;
                                                       }
                                                       else{
                                                            flag = false;
                                                         //alert("Wrong Activation Code");
                                                         
                                                         var msg="<?php echo $client_msg["campaign"]["Msg_Wrong_Code"];?>";
                                                               
                                                                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,

                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            }); 
                                                       }
                                       }
                        });
        }
});
	$("#btn_share_div").click(function(){
	
		  open_popup('Notification');

	});
       //Facebook Share code
       
       $(".loginshare").click(function(){
	
        //open_popup('sharing');
		//open_popuploader('Notificationloader');
		jQuery(".share_loader").css("display","block");
	var campaign_title='Scanflip Offer -'+escape(decodeURI(('<?php echo urlencode($fb_title); ?>')));
        
	var summary=escape(decodeURI(("<?php echo urlencode($summary);?>")));
	var redirect_url="<?php echo $redirect_url;?>";
	var th_link="<?php echo $url; ?>";
    var imgsrc="<?php echo $image;?>";
    var customer_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>";
    var facebook_user_id="<?php if(isset($RS_user_data->fields['facebook_user_id'])){echo $RS_user_data->fields['facebook_user_id'];} ?>";
    var campaign_id="<?php echo $decode_campaign_id;?>";
    var l_id="<?php echo $decode_location_id;?>";
    var campaign_tag="<?php echo $tag_main;?>";
    
    var bussiness_name="<?php echo $busines_name;?>";
    var location_address="<?php echo $RSLocation->fields['address'].",".$RSLocation->fields['city'].",".$RSLocation->fields['state'];?>";
    var sharepoint="<?php echo $sharepoint; ?>";
    
    //alert(th_link);
    
	// 01 10 2013
	
     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH;?>/process.php',
		  data :'btnsharingbuttonloginornot=true',
                 // async:false,
		  success:function(msg)
		  {
                      
                      var obj = jQuery.parseJSON(msg);
                       if(obj.status == "false")
                       {
                         
                          parent.window.location.href= obj.link;
                       }
                       else
                       { 
                           $.ajax({
                                type:"POST",
                                url:'<?php echo WEB_PATH;?>/facebook_share.php',
                                data :'campaign_title='+unescape(campaign_title)+'&summary='+decodeURIComponent(unescape(summary))+'&th_link='+th_link+'&imgsrc='+imgsrc+'&customer_id='+customer_id+'&campaign_id='+campaign_id+'&l_id='+l_id+'&campaign_tag='+campaign_tag+'&bussiness_name='+bussiness_name+'&location_address='+location_address+'&sharepoint='+sharepoint+'&redirect_url='+redirect_url,
                               // async:false,
                                success:function(msg)
                                {
                                    
                                //close_popup('sharing');
								//close_popuploader('Notificationloader');
								jQuery(".share_loader").css("display","none");
                                    var error_arr = jQuery.trim(msg).split("|");
                                    if(error_arr[0] == "OAuthException")
                                        {
                                            
                                          
                                               var msg="<?php echo $client_msg["common"]["Msg_sharing_authorize"];?>";
                                                               var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='Yes' id='tokenyes' name='tokenyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'><input type='button'  value='No' id='tokenno' name='tokenno' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,
                                                                                                    modal : 'true',
                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });
                                               
                                            
                                             
                                            
                                        }
                                         else if(error_arr[0] == "error")
                                        {
                                                var msg="<?php echo $client_msg["common"]["Msg_sharing_authorize"];?>";
                                                               var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='Yes' id='"+campaign_id+"_"+l_id+"' redirect='"+error_arr[1]+"###"+error_arr[0]+"' class='facebooktokenyes' name='facebooktokenyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'><input type='button'  value='No' id='"+campaign_id+"_"+l_id+"'  class='tokenno' name='tokenno' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,
                                                                                                    modal : 'true',
                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });
                                        }  
                                        else
                                            {
                                           
                                              if(jQuery.trim(msg) == "success")
                                              {
                                                 
                                                            var msg="<?php echo $client_msg["common"]["Msg_sharing_success_facebook"];?>";
                                                               var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='sharignpopup' name='sharignpopup' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,
                                                                                                    modal : 'true',
                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },
                                                                                                    hideOnOverlayClick : false, // prevents closing clicking OUTSIE fancybox
                                                                                                    hideOnContentClick : false, // prevents closing clicking INSIDE fancybox
                                                                                                    enableEscapeButton : false,       

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });
													/* 15-03-2014 share counter */		
														
													jQuery.ajax({
														type:"POST",
														url:'<?php echo WEB_PATH;?>/process.php',
														data :'btn_increment_share_counter=yes&customer_id='+customer_id+'&reffer_campaign_id='+campaign_id+'&refferal_location_id='+l_id+'&domain=1&medium='+jQuery("#medium").val()+'&timestamp='+formattedDate(),
													   // async:false,
														success:function(msg)
														{
														}
													});
													/* 15-03-2014 share counter */
                                              }
                                              else
                                              {
                                               // var web_path="<?php echo WEB_PATH; ?>";
                                                //alert(web_path);
                                                //var replace_arr=split(web_path,"")
                                                window.location.href = jQuery.trim(msg);
                                                
                                              }
                                                
                                               
                                            }
                                    
                                }
                           });
                          
                       }
                      //alert(msg);
          
                          
                  }
            });
                   


});
       
       //End of facebook share code


//twitter share code

$(".twitter_link").click(function(){
	//open_popup('sharing');
	//open_popuploader('Notificationloader');
	jQuery(".share_loader").css("display","block");
	var campaign_title='Scanflip Offer -'+escape(decodeURI(('<?php echo urlencode($fb_title); ?>')));
        
	var summary=escape(decodeURI(("<?php echo urlencode($summary);?>")));
	var redirect_url="<?php echo $redirect_url;?>";
	var th_link="<?php echo $url; ?>";
    var imgsrc="<?php echo $image;?>";
    var customer_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>";
    var facebook_user_id="<?php if(isset($RS_user_data->fields['facebook_user_id'])){echo $RS_user_data->fields['facebook_user_id'];} ?>";
    var campaign_id="<?php echo $decode_campaign_id;?>";
    var l_id="<?php echo $decode_location_id;?>";
    var campaign_tag="<?php echo $tag_main;?>";
    

     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH;?>/process.php',
		  data :'btnsharingbuttonloginornot=true',
                 // async:false,
		  success:function(msg)
		  {
                      
                      var obj = jQuery.parseJSON(msg);
                       if(obj.status == "false")
                       {
                         
                          parent.window.location.href= obj.link;
                       }
                       else
                       {
                           
                           $.ajax({
                                type:"POST",
                                url:'<?php echo WEB_PATH;?>/twitteroauth/twitter_login.php',
                                data :'campaign_title='+unescape(campaign_title)+'&summary='+decodeURIComponent(unescape(summary))+'&th_link='+th_link+'&imgsrc='+imgsrc+'&customer_id='+customer_id+'&campaign_id='+campaign_id+'&l_id='+l_id+'&campaign_tag='+campaign_tag+'&redirect_url='+redirect_url,
                               // async:false,
                                success:function(msg)
                                {
									//alert(msg);
                                  //close_popup('sharing');
								  //close_popuploader('Notificationloader');
								  jQuery(".share_loader").css("display","none");
                                    var email_arr = msg.split("|");
                                       
                                        if(jQuery.trim(email_arr[0]) == "success")
                                        {
                                            window.location.href=email_arr[1];
                                        }
                                        
                                        if(jQuery.trim(msg) == "successtweet")
                                            {
                                                            var msg="<?php echo $client_msg["common"]["Msg_sharing_success_twitter"];?>";
                                                               var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='sharignpopup' name='sharignpopup' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,
                                                                                                    modal : 'true',
                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });
																			
												/* 15-03-2014 share counter */				
												jQuery.ajax({
													type:"POST",
													url:'<?php echo WEB_PATH;?>/process.php',
													data :'btn_increment_share_counter=yes&customer_id='+customer_id+'&reffer_campaign_id='+campaign_id+'&refferal_location_id='+l_id+'&domain=2&medium='+jQuery("#medium").val()+'&timestamp='+formattedDate(),
												   // async:false,
													success:function(msg)
													{
													}
												});
												/* 15-03-2014 share counter */
																										
                                            }
                                            var reautho_arr = msg.split("|");
                                            if(jQuery.trim(reautho_arr[0]) == "reautho")
                                            {
                                                                var msg="<?php echo $client_msg["common"]["Msg_sharing_authorize"];?>";
                                                                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='Yes' id='twittertokenyes' name='tokenyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'><input type='button'  value='No' id='twittertokenno' name='tokenno' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,
                                                                                                    modal : 'true',
                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });
                                            }
                                        
                                }
                            });
                         
                          
                       }
               
          
                          
                  }
            });


});

jQuery(".fancybox-inner #sharignpopup").live("click",function(){
        jQuery.fancybox.close();
});
    
$("#btn_msg_div").click(function(){

		
		$.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnsharingbuttonloginornot=true',
                 // async:false,
		  success:function(msg)
		  {
                      var obj = jQuery.parseJSON(msg);
                       if(obj.status == "false")
                       {
                          
                           jQuery.fancybox.close();
                          parent.window.location.href= obj.link;
                       }
                       else
                       {
                          // jQuery(".email_popup_div").show();
                          open_popup('Notification');
                       }
                      //alert(msg);
          
                          
                  }
            });
		  

	});
	$("#btn_share").click(function(){
		//alert("in");
        jQuery("#timestamp").val(formattedDate());        
		var email_str = $("#txt_share_frnd").val();
                
                var msg ="";
            //    alert(email_str);
             //   alert(email_str.length);
                var flag = true;
		if(email_str!="")
                    {
		
                var email_arr = email_str.split(";");
                var arr = new Array();
                //if(email_arr instanceof Array)
              
		for(i=0;i<email_arr.length;i++)
		{
                
                    var email_arr1 = email_arr[i].split(",");
                 
                    for(j=0;j<email_arr1.length;j++)
                    {
                        arr.push(email_arr1[j]);
                    }
                    
                }
		var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
		
		for(i=0;i<arr.length;i++)
		{
                 
			if(! mailformat.test(arr[i]))
			{
                            msg = "Please check email address. Either email address is not correct or you are missing colon (,) or Semi-colon (;) between email addresses";
				flag = false;
				break;
			}
		}
                    }
                 else
                 {
                      msg = "Please enter email address"; 
                     flag = false;
		}
               
		if(flag)
		{
			return true;
		}
		else {
			$("#activatesahreDiv p:first").text(msg)
			return false;
		}
                
	});
    
function close_popup(popup_name)
{
$ = jQuery.noConflict();
	$("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
	$("#" + popup_name + "BackDiv").fadeOut(200, function () {
		 $("#" + popup_name + "PopUpContainer").fadeOut(200, function () {         
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				$("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
 function open_popup(popup_name)
{
$ = jQuery.noConflict();
	if($("#hdn_image_id").val()!="")
	{
		$('input[name=use_image][value='+$("#hdn_image_id").val()+']').attr("checked","checked");
	}
	$("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		$("#" + popup_name + "BackDiv").fadeIn(200, function () {
			 $("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
			 });
		});
	});
	
	}
$("#print_btn").click(function(){
    //alert($(this).attr("myhref"));
//    /*
//    url="";
//    jQuery.ajax({
//                type: "POST",
//                url: url,
//                data:"category_id="+selected_cat_id+"&zip="+parent.document.getElementById("zip").value,
//                success: function(result){
//                 //alert(result);
//                //jQuery('#dealslist').show();		
//                 //jQuery('#ajax_loader').hide();
//                 result=result.split("###");
//                 //alert(result[1]);
//                 if(result[1]>0)
//                     {
//                        parent.document.getElementById("slider_iframe").src = "<?=WEB_PATH?>/search-deal-map.php?category_id="+selected_cat_id+"&zip="+parent.document.getElementById("zip").value+"&zoominglevel="+zoominglevel; 
//                     }
//                     else
//                     {
//                         result="Sorry Currently there are no offers available in this category in your search area";
//                         jQuery('.deallistwrapper').html(result);
//                     }
//
//                }
//           });
//           */
});	
	
$(".btnfilterstaticscampaigns").live("click",function(){
	var $s = jQuery.noConflict();
	$s(".btnfilterstaticscampaigns").each(function(index){
            $s(this).css("color","#3B3B3B");    
    });
		 
    $s(this).css('color','orange');
	var WEB_PATH = "<?=WEB_PATH?>";
	selected_cat_id = $s(this).attr("mycatid");
    setCookie("cat_remember",selected_cat_id,365);
	
	window.location.href='<?= WEB_PATH ?>/search-deal.php';
                  
});
/* $ code */
$("#dont_btn_reserve_deal1").click(function(){
   $(".divoffersaveblock").show();
   $("#dont_btn_reserve_deal1").hide();
});
$("#savable-bubble-promo-dismiss").click(function(){
    
    $(".divoffersaveblock").hide();
    $(".divoffersaveblockonly").show();
    $("#dont_btn_reserve_deal1").hide();
    
});
/* $ end code*/

function setCookie(c_name,value,exdays)
	{
             //  alert(c_name);
			//	alert(value);
            //    alert(exdays);
                
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString())+";path=/;";
	document.cookie=c_name + "=" + c_value;
	}
/* function setCookie(name, value, expires, path, domain, secure){
    document.cookie= name + "=" + escape(value) +
    ((expires) ? "; expires=" + expires.toGMTString() : "") +
    ("; path=/") +       //you having wrong quote here
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
} */
jQuery("#desc_more").toggle(
    function(){
        //jQuery("#desc_div").css("height","auto");
        //jQuery(this).text("Show Less");
		jQuery("#desc_div").css("height","auto");
		jQuery("#desc_div").html(jQuery("#desc_div_hidden").html());
    },
    function(){
        //jQuery("#desc_div").css("height","240px");
        //jQuery(this).text("Show More");
    }
);
jQuery("#terms_more").toggle(
    function(){
        //jQuery("#terms_div").css("height","auto");
        //jQuery(this).text("Show Less");
		jQuery("#terms_div").css("height","auto");
		jQuery("#terms_div").html(jQuery("#terms_div_hidden").html());
    },
    function(){
        //jQuery("#terms_div").css("height","240px");
        //jQuery(this).text("Show More");
    }
);
function googlefunction(datahref)
{
    
    
    var data1=encodeURIComponent(datahref);
    
   //var data1=encodeURIComponent("http://www.scanflip.com/campaign/public-campaign-new-user-Best-Western's-Plus-Cairn-Croft-Hotel-Toronto-Canada/760/81?share=true&customer_id=MTY4");
   //var data2=encodeURIComponent("http://www.scanflip.com/campaign_facebook.php?campaign_id=752&l_id=80&share=true&customer_id=MTY4");
   
     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnsharingbuttonloginornot=true',
                 // async:false,
		  success:function(msg)
		  {
                      
                      var obj = jQuery.parseJSON(msg);
                       if(obj.status == "false")
                       {
                         
                          parent.window.location.href= obj.link;
                       }
                       else
                       {
                          // alert('hi');
                           //alert(campaign_id +l_id+ share+ customer_id);
                          // window.location='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - "+encodeURIComponent(campaign_title)+"&p[summary]="+encodeURIComponent(summary)+"&p[url]="+encodeURIComponent(th_link)+"&p[images][0]="+imgsrc+"', 'sharer', 'toolbar=0,status=0,width=548,height=325';
                          //jQuery(".fancybox-inner .twitter_link").attr("onClick","window.open('https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20"+encodeURIComponent(campaign_title)+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link)+"','_blank')");
                         // urlpath="https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20"+encodeURIComponent(campaign_title)+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link);
                          var urlpath="https://plus.google.com/share?url="+encodeURIComponent(datahref);
                            //alert(urlpath);              
                           var open_popup=window.open(urlpath,'_blank','height=600,width=600');
						   if (open_popup == null || typeof(open_popup)=='undefined')
							{
								//alert("Turn off your pop-up blocker!");
                                                                var msg="<?php echo $client_msg["common"]["Msg_Turn_Off_Popup"];?>";
                                                               
                                                                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,

                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });
							}
                       }
                      //alert(msg);
          
                          
                  }
            });


}
function medaitor_camp(val)
{
     //alert("In mediator");
      myVar=setInterval(function(){myTimer()},30000);
      ///getLocation_camp(val);
	 //alert("out mediator"); 
}
function myTimer()
{
    //getLocation_camp(jQuery("#is_geo_location_supported").val());
}
function getLocation_camp(val)
{
  if (navigator.geolocation)
  {
    //alert("In in");
	navigator.geolocation.getCurrentPosition(showPosition_camp , function(errorCode) {                  
		check_geolocation_support();
		//alert(errorCode.code);
		if (errorCode.code == 0) 
		{
			// alert("Please enter your current location or postal code to search offers near you.");
			//display_fancybox("Please enter your current location or postal code to search offers near you.");
			clearInterval(myVar);
			//enter_zip_code_popup();
		}
		if (errorCode.code == 3) 
		{
			//   alert("Please enter your current location or postal code to search offers near you.");
			//display_fancybox("Please enter your current location or postal code to search offers near you.");
			clearInterval(myVar);
			//enter_zip_code_popup();
		}
		if (errorCode.code == 1) 
		{	  
			//   alert("Please allow location sharing for Scanflip by manually enabling  your share location permission of browser settings.");
			// alert("Cannot retrieve your current location.Please enable location sharing for your browser.");
			//display_fancybox("Cannot retrieve your current location.Please enable location sharing for your browser.");
			clearInterval(myVar);
			//  enter_zip_code_popup();
		}
		if( errorCode.code == 2)
		{		   
			//alert("We can't find your current location.Please enter your current location or postal code to search offers near you.");
			// alert("Cannot find your location. Make sure your network connection is active and click the link request current location to try again.");
			//display_fancybox("Cannot find your location. Make sure your network connection is active and click the link request current location to try again.");
			clearInterval(myVar);
			// enter_zip_code_popup();
		}
	});
  }
  else
  {
	 if(val == ""){
		  display_fancybox("Geolocation is not supported by your current browser");
	  }
	  else{
	
	   enter_zip_code_popup();
	  }
		 clearInterval(myVar);
		 
		 //  enter_zip_code_popup();          
  }
      
}
  
function showPosition_camp(position)
{
	clearInterval(myVar);
	  
	var val;
	val="Latitude: " + position.coords.latitude + 
	"<br />Longitude: " + position.coords.longitude;

	setCookie("mycurrent_lati",position.coords.latitude,365);
	setCookie("mycurrent_long",position.coords.longitude,365);
	//               setCookie("cat_remember",0,365);
	//               setCookie("miles_cookie",50,365);
	setCookie("curr_address","yes",365);

	check_geolocation_support();
	window.location.reload(false);
}

function checkCookie()
{

    
                //alert("cc");
    if(typeof getCookie('mycurrent_lati') == "undefined" || getCookie('mycurrent_lati') == "" || getCookie('mycurrent_lati') == null )
	{
		$.ajax({
        type:"POST",
        url:'<?php echo WEB_PATH; ?>/process.php',
        data :'check_user_last_saved_current=true',
        async:false,
        success:function(msg){
             var obj = jQuery.parseJSON(msg);
                      if(obj.mlatitude == "" || obj.mlatitude == ""){
                          flag= false;
                      }else{
                          // setCookie("searched_location",jQuery(".fancybox-inner #enter_zip").val(),365);
                        //    window.location.href=  WEB_PATH+"/search-deal.php";
                         window.location.reload(false);
                      }
               }
        });
			   
		//alert(flag);
	
		if(! flag)
		{
		
			medaitor_camp(jQuery("#is_geo_location_supported").val());
            //return false;
       	/*
		
		//  alert("in function calling");
			$.fancybox({
				href: this.href,
				//href: $(val).attr('mypopupid'),
				content:jQuery('#enter_zipcode_div').html(),
				width: 470,
				height:98,
				type: 'html',
				openEffect : 'elastic',
				openSpeed  : 300,
				scrolling : 'no',
				closeEffect : 'elastic',
				closeSpeed  : 300,
				afterShow:function()
				{
					//  alert("In after");
					$(".fancybox-inner").css("height","98px");
				},
				beforeShow:function()
				{
					//  alert("In before");
					//alert(jQuery("#activation_code").val());
					//  setCookie("code",jQuery("#activation_code").val(),365);
					// $(".fancybox-inner").css("height","98px");
					$(".fancybox-inner").addClass("enterZipcode");
					jQuery(".fancybox-inner .zipcodeerror_section").css("display","none");
					$(".fancybox-outer").css("width","423px");
					jQuery(".fancybox-inner .zipcode_section").css("display","block");
				},
				helpers: 
				{
					overlay: {
					opacity: 0.3,
					closeClick:false
					} // overlay
				},
				enableEscapeButton : false,
				keys:{
				close:null
				},
				showCloseButton: false,
				'afterShow': function()
				{
					$('.fancybox-close').attr('id','close');
					//override fancybox_close btn

					jQuery("#close").unbind("click");
					jQuery("#close").detach();
				}
			// helpers
			}); 
	*/
		}
		return false;
    }
	
}
jQuery("body").on("click","#searchdeal_zip",function(){
	/*
    $.ajax({
        type:"POST",
        url:'<?php echo WEB_PATH; ?>/process.php',
        data :'getlatitudelongitude_zipcode=true&searched_location='+ jQuery(".fancybox-inner #enter_zip").val(),
        async:false,
        success:function(msg)
        {
             var obj = jQuery.parseJSON(msg);
            if(obj.mlatitude == "" || obj.mlatitude == ""){
                jQuery(".fancybox-inner .zipcode_section").css("display","none");
                jQuery(".fancybox-inner .zipcodeerror_section").css("display","block");
              
                return false;
            }
            else{
                setCookie("searched_location",jQuery(".fancybox-inner #enter_zip").val(),365);
		//window.location.href=  WEB_PATH+"/search-deal.php";
                window.location.reload(false);
            }
        }
    });
	*/
	
	//alert("get");
	//alert(jQuery("#loc_lat").val());
	//alert(jQuery("#loc_lng").val());
	
	jQuery("#enter_zipcode_div").css("display","none");
	jQuery.ajax({
        type:"POST",
        url:'<?php echo WEB_PATH; ?>/process.php',
        data :'getlatitudelongitude_zipcode1=true&searched_location='+ jQuery("#enter_zip").val()+"&to_lat="+jQuery("#loc_lat").val()+"&to_lng="+jQuery("#loc_lng").val(),
        async:false,
        success:function(msg)
        {
			//alert(msg);
			jQuery("#milesdiv").html(msg);
			setCookie("searched_location",jQuery("#enter_zip").val(),365);
		}
	});
});

jQuery("body").on("click","#searchdeal_cancle",function(){
 jQuery(".fancybox-inner .zipcodeerror_section").css("display","none");
    jQuery(".fancybox-inner .zipcode_section").css("display","block");
   
	   
});
function popup_searchdeal(txt_val,e)
{
        if(e.keyCode==13)
        {
           // search_deal();
           jQuery("#searchdeal_zip").trigger("click");

        }
		
}
jQuery("#popupcancel_no_offer").live("click",function(){

window.location.href= '<?php echo  WEB_PATH."/search-deal.php" ?>';
});

jQuery(".fancybox-inner #twittertokenyes").live("click",function(){
    
    var campaign_title='Scanflip Offer -'+escape(decodeURI(('<?php echo urlencode($fb_title); ?>')));
        
	var summary=escape(decodeURI(("<?php echo urlencode($summary);?>")));
	var redirect_url="<?php echo $redirect_url;?>";
	var th_link="<?php echo $url; ?>";
    var imgsrc="<?php echo $image;?>";
    var customer_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>";
    var facebook_user_id="<?php if(isset($RS_user_data->fields['facebook_user_id'])){echo $RS_user_data->fields['facebook_user_id'];} ?>";
    var campaign_id="<?php echo $decode_campaign_id;?>";
    var l_id="<?php echo $decode_location_id;?>";
    var campaign_tag="<?php echo $tag_main;?>";
    
    
            jQuery.ajax({
                                                            type:"POST",
                                                            url:'<?php echo WEB_PATH;?>/twitteroauth/twitter_login.php',
                                                            data :'campaign_title='+unescape(campaign_title)+'&summary='+decodeURIComponent(unescape(summary))+'&th_link='+th_link+'&imgsrc='+imgsrc+'&customer_id='+customer_id+'&campaign_id='+campaign_id+'&l_id='+l_id+'&campaign_tag='+campaign_tag+'&redirect_url='+redirect_url,
                                                            success:function(msg)
                                                            {
                                                              
                                                                var email_arr = msg.split("|");
                                                              
                                                                if(jQuery.trim(email_arr[0]) == "reautho")
                                                                {
                                                                    
                                                                    
                                                                     jQuery.fancybox.close();
                                                                    parent.window.location.href= email_arr[1];
                                                                }
                                                                        
                                                                  
                                                            }
                                                         });
});

jQuery(".fancybox-inner #tokenyes").live("click",function(){
    
    var campaign_title='Scanflip Offer -'+escape(decodeURI(('<?php echo urlencode($fb_title); ?>')));
        
	var summary=escape(decodeURI(("<?php echo urlencode($summary);?>")));
	var redirect_url="<?php echo $redirect_url;?>";
	var th_link="<?php echo $url; ?>";
    var imgsrc="<?php echo $image;?>";
    var customer_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>";
    var facebook_user_id="<?php if(isset($RS_user_data->fields['facebook_user_id'])){echo $RS_user_data->fields['facebook_user_id'];} ?>";
    var campaign_id="<?php echo $decode_campaign_id;?>";
    var l_id="<?php echo $decode_location_id;?>";
    var campaign_tag="<?php echo $tag_main;?>";
    
    var bussiness_name="<?php echo $busines_name;?>";
    var location_address="<?php echo $RSLocation->fields['address'].",".$RSLocation->fields['city'].",".$RSLocation->fields['state'];?>";
    var sharepoint="<?php echo $sharepoint; ?>";
    
    
            jQuery.ajax({
                                                            type:"POST",
                                                            url:'<?php echo WEB_PATH;?>/facebook_share_reassign.php',
                                                            data :'facebook_user_id='+facebook_user_id+'&customer_id='+customer_id,
                                                            success:function(msg)
                                                            {
                                                               
                                                                jQuery.ajax({
                                                                      type:"POST",
                                                                      url:'<?php echo WEB_PATH;?>/facebook_share.php',
                                                                      data :'campaign_title='+unescape(campaign_title)+'&summary='+decodeURIComponent(unescape(summary))+'&th_link='+th_link+'&imgsrc='+imgsrc+'&customer_id='+customer_id+'&campaign_id='+campaign_id+'&l_id='+l_id+'&campaign_tag='+campaign_tag+'&bussiness_name='+bussiness_name+'&location_address='+location_address+'&sharepoint='+sharepoint+'&redirect_url='+redirect_url,
                                                                      // async:false,
                                                                      success:function(msg)
                                                                      {
                                                                          
                                                                             var msg="Campaign has been shared successfully.";
                                                                             var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                             var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                             var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='sharignpopup' name='sharignpopup' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                             jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,

                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });
                                                                                
                                                                      }
                                                                });
                                                                        
                                                                  
                                                            }
                                                         });
});

jQuery(".fancybox-inner .facebooktokenyes").live("click",function(){
        var data_redirect_array=jQuery(this).attr('redirect').split('###');
    if(data_redirect_array[1] == "error")
        {
             window.location.href=data_redirect_array[0];
        }
                   
});

jQuery(".fancybox-inner #sharignpopup").live("click",function(){
        jQuery.fancybox.close();
});
jQuery(".fancybox-inner #popupcancel").live("click",function(){
        jQuery.fancybox.close();
});
jQuery(".fancybox-inner #tokenno").live("click",function(){
        jQuery.fancybox.close();
});
jQuery(".fancybox-inner #twittertokenno").live("click",function(){
        jQuery.fancybox.close();
});
jQuery(".fancybox-inner .tokenno").live("click",function(){
        jQuery.fancybox.close();
});
/*
jQuery(".fancybox-inner #tokenno").live("click",function(){
        jQuery.fancybox.close();
        var campaign_title=escape(decodeURI(('<?php echo urlencode($fb_title); ?>')))+" "+"<?php echo $tag_main;?>";
        
	var summary=escape(decodeURI(("<?php echo urlencode($summary);?>")));
	
	var th_link="<?php echo $url; ?>";
    var imgsrc="<?php echo $image;?>";
    var customer_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>";
    var facebook_user_id="<?php if(isset($RS_user_data->fields['facebook_user_id'])){echo $RS_user_data->fields['facebook_user_id'];} ?>";
    var campaign_id="<?php echo $decode_campaign_id;?>";
    var l_id="<?php echo $decode_location_id;?>";
    var caption="My First Message";
    
           urlpath='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - '+unescape(campaign_title)+'&p[summary]='+decodeURIComponent(unescape(summary))+'&p[url]='+th_link+'&p[images][0]='+imgsrc+'&p[caption]='+caption;
						   // 01 10 2013
                         var open_popup=window.open(urlpath,'_blank','width=400,height=300');
						 if (open_popup == null || typeof(open_popup)=='undefined')
						 {
							//alert("Turn off your pop-up blocker!");
                                                        
                                                         var msg="<?php echo $client_msg["common"]["Msg_Turn_Off_Popup"];?>";
                                                               
                                                                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,

                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });
                                                        
						 }
        
        
});
                */
</script>
<?php
function GetLatitudeLongitude($_zipcodeaddress)
{

//echo $_zipcodeaddress."GetLatitudeLongitude";
$_zipcodeaddress = urlencode ($_zipcodeaddress);
$_geocode= file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$_zipcodeaddress."&sensor=false");					
$_geojson= json_decode($_geocode,true);
//print_r($_geojson);
	if( $_geojson['status']=='OK')
	{					
		$_lat= $_geojson['results'][0]['geometry']['location']['lat'];
		$_lng= $_geojson['results'][0]['geometry']['location']['lng'];
		$_address = $_geojson['results'][0]['formatted_address'];						
	
		$_output = array (
				"location" => 
				array (									
					"latitude" => "$_lat" ,
					"longitude"  => "$_lng",
					"address"  => "$_address[0]",
					"city"  => "$_address[1]",
					"zip"  => "$_address[2]",
					"state"  => "$_address[2]",
					"country"  => "$_address[2]"
					),
					);								
	}
	else
	{
		$_output= '';						
	}
	return $_output;

}
function repalce_char($value){
	$replace = array(",","&nbsp;","\n","\t", "\r","&amp;", "&raquo;","$");
	$value = str_replace($replace," ",$value);
	$value = strip_tags($value);
	$value = preg_replace('/\s+/',' ',$value);
	$value = trim($value);
	$value = mysql_escape_string($value);
	return $value;
}


?>
<script>
/*
jQuery(document).ready(function(e){
 //e.preventDefault();
  
//window.history.pushState(�object or string�, �Title�, �/new-url�);
//console.log( window.location.href );
//window.history.replaceState( {} ,'foo', 'http://www.scanflip.com/location/Best-Western-Plus-Cairn-Croft-Hotel-Toronto-70.html' );
//console.log( window.location.href );  
//});
*/
function check_geolocation_support()
{
 
   if(navigator.geolocation)
    {
         jQuery("#get_current_location").css("display","block");
    }
    else{
        jQuery("#get_current_location").css("display","none");
              jQuery.ajax({
		  type:"POST",
		  url:'/process.php',
		  data :'set_not_supported_session_value=true',
                  async:false,
		  success:function(msg)
		  {
                  
                  }
              });         
    }

}

jQuery("#askmile").click(function(){
	jQuery("#enter_zipcode_div").css("display","block");
});

jQuery("#searchdeal_zip").click(function(){
	//alert("ok");
});

jQuery("#searchdeal_zip_cancel").click(function(){
	jQuery("#enter_zipcode_div").css("display","none");
	//alert("cancel");
});
jQuery(document).ready(function(){
del_cookie("is_scanflip_scan_qrcode");
	setCookie("scanflip_scan_qrcode",'<?php if(isset($serialized_data_to_send)){ echo $serialized_data_to_send;} ?>',365);
	setCookie("recently_viewed_campaigns",'<?php if(isset($recently_viewed_campaignlist_string)){echo $recently_viewed_campaignlist_string;} ?>',365);
	
	setCookie("camp_page1",'<?php if(isset($current_url1)){echo $current_url1;} ?>',365);
	setCookie("p_path",'<?php if(isset($p_path)){echo $p_path;} ?>',365);
	
});

jQuery("body").on("click","#Not_Set_Login_Frm #btn_cancel_forgot",function(){
		
		jQuery(".fancybox-inner .updateprofile").css("display","none");
		jQuery(".fancybox-inner .popupmainclass").css("display","block");
		
		
    });
</script>
<?php 
 
    if(isset($_SESSION['not_supported_geolocation']))
    {
         // echo $_SESSION['not_supported_geolocation'];
        $s="0";
     }
     else{
         $s= "";
     }
    ?>
<input type="hidden" name="is_geo_location_supported" id="is_geo_location_supported" value="<?php echo $s ; ?>" />
<script>
jQuery(document).ready(function(){
	setCookie("recently_viewed_campaigns_list1",'<?php echo $recently_viewed_campaignlist_string; ?>',365);
});
jQuery("#print_link").live("click",function(){
ele = jQuery(this);
 if(jQuery("#is_profileset").val() != 1){
     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'is_userprofileset=true&custome_id=<?php echo $customer_id_p; ?>',
                  async:false,
		  success:function(msg)
		  {
                       var obj = jQuery.parseJSON(msg);
                      //alert(obj)
                      if(obj.status == 1)
                      {
                          flag= true;
                      }
                  else{
                       jQuery("#profile_view").val("print_link");
					   jQuery("#print_redirect_link").val(ele.attr("href"));
                        flag = false;
                    jQuery.fancybox({
				//href: this.href,
				
				//href: $(val).attr('mypopupid'),
				
				content:jQuery('#div_profile').html(),
				
				width: 435,
				height: 345,
				type: 'html',
				openEffect : 'elastic',
				openSpeed  : 300,
                                 
				closeEffect : 'elastic',
				closeSpeed  : 300,
				// topRatio: 0,
               
                                changeFade : 'fast',  
				beforeShow : function(){
                                  // jQuery(".fancybox-inner #subscribe_location_id").val(locid);
                                },
								afterShow:function(){
								jQuery(".fancybox-inner #login_frm").html(jQuery("#notprofilesetid").html());
								},
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
                                
                    });
                      //alert("You Profile IS not set. First set it");
                  }
                  }
     });
     if(! flag)
     {
         return false;
     }
     }
});
</script>
<script>
/**  * jQuery.browser.mobile (http://detectmobilebrowser.com/)  * jQuery.browser.mobile will be true if the browser is a mobile device  **/ 
(function(a){jQuery.browser.mobile=/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);
if(jQuery.browser.mobile)
{
   //alert('You are using a mobile device!');
   jQuery("#medium").val("2");
}
else
{
   //alert('You are not using a mobile device!');
   jQuery("#medium").val("1");
}

var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};
if( isMobile.any() ) 
{
	//alert('Mobile');
	//alert('You are using a mobile device!');
	jQuery("#medium").val("2");
}
function formattedDate(date) {
    var d = new Date(date || Date.now()),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
	
	var h=d.getHours();
	var m=d.getMinutes();
	var s=d.getSeconds();
	
	if (h.length < 2) h = '0' + h;
    if (m.length < 2) m = '0' + m;
	if (s.length < 2) s = '0' + s;
	
    return [year,month,day ].join('-')+" "+[h,m,s ].join(':');
}

function close_popuploader(popup_name)
    {
		/*$("#" + popup_name + "FrontDivProcessing").css("display","none");
	$("#" + popup_name + "PopUpContainer").css("display","none");
	$("#" + popup_name + "BackDiv").css("display","none");*/
        jQuery("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
            jQuery("#" + popup_name + "BackDiv").fadeOut(200, function () {
                jQuery("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
                    jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
                    jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
                    jQuery("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
                });
            });
        });
	
    }
    function open_popuploader(popup_name)
    {

	jQuery("#" + popup_name + "FrontDivProcessing").css("display","block");
	jQuery("#" + popup_name + "PopUpContainer").css("display","block");
	jQuery("#" + popup_name + "BackDiv").css("display","block");
        /*$("#" + popup_name + "FrontDivProcessing").fadeIn(10, function () {
            $("#" + popup_name + "BackDiv").fadeIn(10, function () {
                $("#" + popup_name + "PopUpContainer").fadeIn(10, function () {         
	
                });
            });
        });*/
	
	
    }
jQuery("div.location_hrs a").toggle(function(){
	jQuery(this).next().css("display","block");
},function()
{
	jQuery(this).next().css("display","none");
});
function google_sharing(camid,l_id,share,cust_id)
{

        var data1=jQuery(this).attr('id');
		var url="<?php echo WEB_PATH?>/register.php?campaign_id="+camid+"&l_id="+l_id+"&share=true&customer_id="+cust_id+"&domain=3";
     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnsharingbuttonloginornot=true',
                 // async:false,
		  success:function(msg)
		  {
                      
                      var obj = jQuery.parseJSON(msg);
                       if(obj.status == "false")
                       {
                         
                          parent.window.location.href= obj.link;
                       }
                       else
                       {
                          // alert('hi');
                           //alert(campaign_id +l_id+ share+ customer_id);
                          // window.location='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - "+encodeURIComponent(campaign_title)+"&p[summary]="+encodeURIComponent(summary)+"&p[url]="+encodeURIComponent(th_link)+"&p[images][0]="+imgsrc+"', 'sharer', 'toolbar=0,status=0,width=548,height=325';
                          //jQuery(".fancybox-inner .twitter_link").attr("onClick","window.open('https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20"+encodeURIComponent(campaign_title)+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link)+"','_blank')");
                         // urlpath="https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20"+encodeURIComponent(campaign_title)+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link);
                          var urlpath="https://plus.google.com/share?url="+encodeURIComponent(url);
                         
                            //alert(urlpath);              
                           var open_popup=window.open(urlpath,'_blank','height=600,width=600');
						   if (open_popup == null || typeof(open_popup)=='undefined')
							{
								//alert("Turn off your pop-up blocker!");
                                                                var msg="<?php echo $client_msg["common"]["Msg_Turn_Off_Popup"];?>";
                                                               
                                                                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                                                jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);

                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('#dialog-message').html(),

                                                                                                    type: 'html',

                                                                                                    openSpeed  : 300,

                                                                                                    closeSpeed  : 300,
                                                                                                    // topRatio: 0,

                                                                                                    changeFade : 'fast',  
                                                                                                    beforeShow : function(){
                                                                                                        $(".fancybox-inner").addClass("msgClass");
                                                                                                    },

                                                                                                    helpers: {
                                                                                                            overlay: {
                                                                                                            opacity: 0.3
                                                                                                            } // overlay
                                                                                                    }
                                                                            });
							}
                       }
                      //alert(msg);
          
                          
                  }
            });
}
</script>
 <div id="NotificationloaderPopUpContainer" class="container_popup"  style="display: none;">
    <div id="NotificationloaderBackDiv" class="divBack">
    </div>
    <div id="NotificationloaderFrontDivProcessing" class="Processing" style="display:none;">

        <div id="NotificationloaderMaindivLoading" align="center" valign="middle" class="imgDivLoading"
             style="left: 45%;top: 40%;">

            <div id="NotificationloadermainContainer" class="loading innerContainer" style="height:auto;width:auto">
                <img src="<?= ASSETS_IMG ?>/c/128.GIF" style="display: block;" id="image_loader_div"/>
            </div>
        </div>
    </div>
</div>
<?php
if(isset($_REQUEST['domain']))
{
	$medium=1;
	$detect = new Mobile_Detect;

	// 1. Check for mobile environment.
	if ($detect->isMobile()) {
		// Your code here.
		$medium=2;
	}
	// 2. Check for tablet device.
	if($detect->isTablet()){
		// Your code here.
		$medium=2;
	}
	
	$array_loc=array();
	$array_loc['id'] = $decode_location_id;
	$RS_location = $objDB->Show("locations",$array_loc);
	$time_zone=$RS_location->fields['timezone_name'];
	date_default_timezone_set($time_zone);
							
	$pageview_array = array();
	$pageview_array['campaign_id'] = $decode_campaign_id;
	$pageview_array['location_id'] = $decode_location_id;
	$pageview_array['pageview_domain'] = $_REQUEST['domain'];
	$pageview_array['pageview_medium'] = $medium;
	$pageview_array['timestamp'] = date("Y-m-d H:i:s");
	//$pageview_array['timestamp'] = $user_datetime;
	
	$objDB->Insert($pageview_array, "pageview_counter");
	$pageview_array = array();
}
?>
