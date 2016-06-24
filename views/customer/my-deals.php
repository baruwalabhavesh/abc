<?php
/******** 
@USE : display my offers
@PARAMETER : 
@RETURN : 
@USED IN PAGES : my-profile.php, process.php, register.php, process_mobile.php, print_coupon.php, facebook_login.php, mynotification.php, my-emailsettings.php, change-password.php, zipcodediv.php, header.php, process.php
*********/
//setcookie("test123","My Camapign",time()+3600*24*30);
setcookie("test123","my dael", time()+3600*24*30,"","");
setcookie("testreg","",time()-360000*24*30);

//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
check_customer_session();
//include_once(LIBRARY."/func_print_coupon.php");
require_once LIBRARY.'/Mobile_Detect.php';
require_once LIBRARY.'/google-api-php-client/src/Google_Client.php';
require_once LIBRARY.'/google-api-php-client/src/contrib/Google_PlusService.php';
require_once LIBRARY.'/google-api-php-client/src/contrib/Google_Oauth2Service.php';

$detect = new Mobile_Detect;
//$objDB = new DB();
//$objDBWrt = new DB('write');
//$objJSON = new JSON();

########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############
$google_client_id 	= GOOGLE_CLIENT_ID;
$google_client_secret 	= GOOGLE_CLIENT_SECRET;
$google_redirect_url 	= GOOGLE_REDIRECT_URL; //path to your script
$google_developer_key 	= GOOGLE_DEVELOPER_KEY;

$gClient = new Google_Client();
$gClient->setClientId($google_client_id);
$gClient->setClientSecret($google_client_secret);
$gClient->setRedirectUri($google_redirect_url);
$gClient->setDeveloperKey($google_developer_key);
$gClient->setApprovalPrompt('auto');

$google_oauthV2 = new Google_Oauth2Service($gClient);

if ($gClient->getAccessToken()) 
{
	//For logged in user, get details from google using access token
	$user 				= $google_oauthV2->userinfo->get();
	$user_id 				= $user['id'];
	$user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	$email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	$profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	$profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	$personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
	$_SESSION['token'] 	= $gClient->getAccessToken();
}
else 
{
	//For Guest user, get google login url
	$authUrl = $gClient->createAuthUrl();
}

$total_records1 =  "";
// to set lati & long in cookie
//echo $_COOKIE['myck'];

//Start facebook sharing code
if(isset($_SESSION['customer_id'] ))
{
    /* $Sql_new = "select * from customer_user  where id=".$_SESSION['customer_id'];
    $RS_user_data = $objDB->Conn->Execute($Sql_new); */
    $RS_user_data = $objDB->Conn->Execute("select * from customer_user  where id=?",array($_SESSION['customer_id']));
    $cust_id_with_url=base64_encode($_SESSION['customer_id']);
}

require LIBRARY.'/fb-sdk/src/facebook.php';

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
                    'redirect_uri'=>'',
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
                            if($content->profile_image_url == "http://abs.twimg.com/sticky/default_profile_images/default_profile_1_normal.png")
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


if(isset($_COOKIE['myck']))
{
    	$myck=$_COOKIE['myck'];
}
$mlatitude="";
$mlongitude="";
 $cookie_life = time() + 31536000;
// echo "<pre>";
// print_r($_COOKIE);
// echo "</pre>";
if(isset($_COOKIE['curr_address']))
{
    
	if($_COOKIE['curr_address'] == "yes")
	{
		
		if($_COOKIE['mycurrent_lati'] != "" && $_COOKIE['mycurrent_long'] != "" )
	{
			$mlatitude = $_COOKIE['mycurrent_lati'];
		$mlongitude = $_COOKIE['mycurrent_long'];
		//exit;
		 $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$_COOKIE["mycurrent_lati"] .','.$_COOKIE["mycurrent_long"].'&sensor=false');
		 $output= json_decode($geocode);

		 $current_address_string = $output->results[0]->formatted_address;
		 $current_address_string = str_replace("+"," ",$current_address_string);
		 setcookie("searched_location", $current_address_string,$cookie_life);
		//////////  if user login  then set currant latitude and longitude ///////
			if(isset($_SESSION['customer_id']))
			{
				$timezone1  = getClosestTimezone($_COOKIE["mycurrent_lati"] ,$_COOKIE["mycurrent_long"]);
					 $timezone = new DateTimeZone($timezone1);
					  $offset1   = $timezone->getOffset(new DateTime);
						//timezone_offset_string( $offset1 );
					 $tz = timezone_offset_string( $offset1 );
					 $curr_timezone = $tz;
		 
				//Upadte customer_user set current_loaction =".$_REQUEST['current_location']." , curr_latitude=".$_REQUEST['current_latitude']." , curr_longitude=".$_REQUEST['current_longitude']." where id=".$_REQUEST['customer_id'];
			//  echo WEB_PATH.'/process.php?set_user_current_location=yes&curr_location='.$current_address_string.'&curr_latitude='.$_COOKIE['mycurrent_lati'].'&curr_longitude='.$_COOKIE['mycurrent_long'].'&customer_id='.$_SESSION['customer_id']."&curr_timezone=".urlencode($curr_timezone);
			   // $arr=file(WEB_PATH.'/process.php?set_user_current_location=yes&curr_location='.$current_address_string.'&curr_latitude='.$_COOKIE['mycurrent_lati'].'&curr_longitude='.$_COOKIE['mycurrent_long'].'&customer_id='.$_SESSION['customer_id']."&curr_timezone=".urlencode($curr_timezone));
			  if($_COOKIE['mycurrent_lati']!="" && $curr_timezone != "" )
			  {
				if($current_address_string!="")
				{
					/* $update_sql = "Update customer_user set current_location ='".$current_address_string."' , curr_latitude='".$_COOKIE['mycurrent_lati']."' , curr_longitude='".$_COOKIE['mycurrent_long']."' ,curr_timezone= '".$curr_timezone."'  where id=".$_SESSION['customer_id'];
					// echo $update_sql;
					$objDB->Conn->Execute($update_sql); */
					$objDBWrt->Conn->Execute("Update customer_user set current_location =? , curr_latitude=? , curr_longitude=?,curr_timezone=?  where id=?",array($current_address_string,$_COOKIE['mycurrent_lati'],$_COOKIE['mycurrent_long'],$curr_timezone,$_SESSION['customer_id']));
				}
			  }
			}
			//exit();
		 ///////////  if user login  then set currant latitude and longitude /////// 
	  
		 ?>

	<?php }
	else{
		 if(isset($_SESSION['customer_id']))
		{
			if($_SESSION['customer_info']['postalcode'] != "")
			{

	//          setcookie('mycurrent_lati', $_SESSION['customer_info']['curr_latitude'], $cookie_life);
	//			setcookie('mycurrent_long', $_SESSION['customer_info']['curr_longitude'], $cookie_life);
				$locc=GetLatitudeLongitude($_SESSION['customer_info']['postalcode'] );
				if($locc !="")
				{
					
                                    $mlatitude = $locc["location"]["latitude"];	
                                    $mlongitude = $locc["location"]["longitude"];

                                    $cookie_life = time() + 31536000;
                                    setcookie('mycurrent_lati', $mlatitude, $cookie_life);
                                    setcookie('mycurrent_long', $mlongitude, $cookie_life);
            //			
                                     $_SESSION['mycurrent_lati'] = $mlatitude ;
                                    $_SESSION['mycurrent_long'] = $mlongitude;
									
				}
				 setcookie("searched_location",$_SESSION['customer_info']['postalcode'] ,$cookie_life);
				 $mlatitude = $_SESSION['customer_info']['curr_latitude'];
				 $mlongitude =  $_SESSION['customer_info']['curr_longitude'];
			}
		}
	}
	}
}
 function timezoneoffsetstring($offset)
{
  //  echo 1; 
        return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
}
function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
    $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
                                    : DateTimeZone::listIdentifiers();

    if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

        $time_zone = '';
        $tz_distance = 0;

        //only one identifier?
        if (count($timezone_ids) == 1) {
            $time_zone = $timezone_ids[0];
        } else {

            foreach($timezone_ids as $timezone_id) {
                $timezone = new DateTimeZone($timezone_id);
                $location = $timezone->getLocation();
                $tz_lat   = $location['latitude'];
                $tz_long  = $location['longitude'];

                $theta    = $cur_long - $tz_long;
                $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat))) 
                + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                $distance = acos($distance);
                $distance = abs(rad2deg($distance));
                // echo '<br />'.$timezone_id.' '.$distance; 

                if (!$time_zone || $tz_distance > $distance) {
                    $time_zone   = $timezone_id;
                    $tz_distance = $distance;
                } 

            }
        }
        return  $time_zone;
    }
    return 'unknown';
}
function getClosestTimezone($lat, $lng)
  {
    $diffs = array();
    foreach(DateTimeZone::listIdentifiers() as $timezoneID) {
      $timezone = new DateTimeZone($timezoneID);
      $location = $timezone->getLocation();
      $tLat = $location['latitude'];
      $tLng = $location['longitude'];
      $diffLat = abs($lat - $tLat);
      $diffLng = abs($lng - $tLng);
      $diff = $diffLat + $diffLng;
      $diffs[$timezoneID] = $diff;

    }

    //asort($diffs);
    $timezone = array_keys($diffs, min($diffs));


    return $timezone[0];

  }

$Cat_DetailHtml="";

// Up to here
//print_r($_REQUEST);
function repalce_char($value){
	$replace = array(",","&nbsp;","\n","\t", "\r","&amp;", "&raquo;","$");
	$value = str_replace($replace," ",$value);
	$value = strip_tags($value);
	$value = preg_replace('/\s+/',' ',$value);
	$value = trim($value);
	$value = mysql_escape_string($value);
	return $value;
}
if(isset($_COOKIE['myck']))
{
 	if($myck=="true")
	{
	   // echo "In If";
		 setcookie("myck","",time()-36000);
		// echo $_COOKIE['searched_location']."===";
		 $zip = repalce_char($_COOKIE['searched_location']);
                $locc = GetLatitudeLongitude($zip);
                if($locc !="")
                {                                        
                        $mlatitude = $locc["location"]["latitude"];	
                        $mlongitude = $locc["location"]["longitude"];

                        $cookie_life = time() + 31536000;
                        setcookie('mycurrent_lati', $mlatitude, $cookie_life);
                        setcookie('mycurrent_long', $mlongitude, $cookie_life);
                        $_SESSION['mycurrent_lati']=$mlatitude;
                        $_SESSION['mycurrent_long']=$mlongitude;	
                }
                else
                {					  
                        $mlatitude = "";	
                        $mlongitude = "";
                }
                if(isset($_COOKIE['miles_cookie']))
                {
                    $dismile=$_COOKIE['miles_cookie'];
                }
                else{
                    $dismile=50;
                }
	//	$locc=GetLatitudeLongitude($_COOKIE['searched_location']);
	//	if($locc !="")
	//	{
	//		
	//			$mlatitude = $locc["location"]["latitude"];	
	//			$mlongitude = $locc["location"]["longitude"];
	//									  
	//			$cookie_life = time() + 31536000;
	//			setcookie('mycurrent_lati', $mlatitude, $cookie_life);
	//			setcookie('mycurrent_long', $mlongitude, $cookie_life);
	//			
	//			$_SESSION['mycurrent_lati']=$mlatitude;
	//			$_SESSION['mycurrent_long']=$mlongitude;
	//						
	//	}
	//        else{
	//            echo "wronng postal code";
	//        }
	}
	else
	{
            if(isset($_COOKIE['curr_address'])) 
		{
			if($_COOKIE['curr_address'] == "yes")
			{

			}
		}
		else
		{ 
                    if(isset($_COOKIE['mycurrent_lati']) && isset( $_COOKIE['mycurrent_long']))
                    {
			$mlatitude = $_COOKIE['mycurrent_lati'];
			$mlongitude = $_COOKIE['mycurrent_long'];
                    }
		}
			
	}
}
else{
	//echo "In Else";
		if(isset($_COOKIE['curr_address'])) 
		{
			if($_COOKIE['curr_address'] == "yes")
			{

			}
		}
		else
		{ 
                    if(isset($_COOKIE['mycurrent_lati']) && isset( $_COOKIE['mycurrent_long']))
                    {
			$mlatitude = $_COOKIE['mycurrent_lati'];
			$mlongitude = $_COOKIE['mycurrent_long'];
                    }
		}
}
//echo $mlongitude."==".$mlatitude;
$latitude_ = $mlatitude;
$longitude_ = $mlongitude;
// to set lati & long in cookie
//echo "123";

//echo WEB_PATH.'/process.php?get_customer_deals=yes&lati='.$mlatitude.'&long='.$mlongitude.'&customer_id='.$_SESSION['customer_id'];

//exit;
$dismile = 50;
$cust_s = $_SESSION['customer_id'];
//echo WEB_PATH.'/process.php?btnGetSavedOffers=yes&dismile='.$dismile.'&mlatitude='.$mlatitude.'&mlongitude='.$mlongitude.'&category_id=0'.'&customer_id='.$cust_s;
$arr_loc=file(WEB_PATH.'/process.php?btnGetSavedOffers=yes&dismile='.$dismile.'&mlatitude='.$mlatitude.'&mlongitude='.$mlongitude.'&category_id=0'.'&customer_id='.$cust_s);
							if(trim($arr_loc[0]) == "")
							{
								unset($arr_loc[0]);
								$arr_loc = array_values($arr_loc);
							}
										$all_json_str_loc = $arr_loc[0];
									   // echo $all_json_str;
										//exit;
							$json_loc = json_decode($arr_loc[0]);
							$total_records1_loc = $json_loc->total_records;
							$total_records1 = $json_loc->records;
							$records_array1_loc = $json_loc->records;
							$business_tags = $json_loc->business_tags_records;
							//$marker_records_array1_loc = $json_loc->marker_records;
							/*(echo "<pre>";
							echo $total_records1_loc;
							print_r($records_array1_loc);
							echo "</pre>";*/
							//exit(); 
?>
<?php

			$arr = array();
				if(isset($_SESSION['customer_id']))
				{
					$cust_s = $_SESSION['customer_id'];
				}
				else{
					$cust_s = "";
				}
               
	if(isset($_SESSION['customer_id'])){
                                   //$not_found_msg = "<div class='div_msg_err'>Sorry, there are no offers within <span id='span_miles'></span> miles from your search location. Please try changing your filter setting.</div>";
								   $not_found_msg = "<div class='warning'><p>!</p><span class='div_msg_err' style='display: block;'>".$client_msg['mymerchant']['msg_Sorry_No_Offers_Within'].$client_msg['mymerchant']['msg_Miles_Search_Location'].$client_msg['mymerchant']['msg_Please_Filter_Setting']."</span></div>";
                               }
                               else{
                                   //$not_found_msg = "<div class='div_msg_err'>Sorry, there are no offers within <span id='span_miles'></span> miles from your search location. Please try changing your filter setting or <a href='./register.php'> register </a> to receive notification as soon as offers become available near you.</div>";
								   $not_found_msg = "<div class='warning'><p>!</p><span class='div_msg_err' style='display: block;'>".$client_msg['mymerchant']['msg_Sorry_No_Offers_Within'].$client_msg['mymerchant']['msg_Miles_Search_Location'].$client_msg['mymerchant']['msg_Please_Filter_Setting']."</span></div>";
                               }
	
		// 11 11 2013 new deal 24 hour logic

		$arr_new_subs_merchant_deal = array();
		$category  = $_COOKIE['cat_remember'];
		

		
			
		// 11 11 2013 new deal 24 hour logic
?>
<!DOCTYPE HTML>
<html prefix="og: https://ogp.me/ns#" lang="en">
<head >
<title>ScanFlip | My Reserved Campaigns</title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="Cache-control" content="public,max-age=86400,must-revalidate">
<meta content="NOINDEX" name="ROBOTS">
<meta name="viewport" content="user-scalable=no">
<link rel="stylesheet" href="<?php echo ASSETS_CSS ?>/c/ui.totop.css" />
    <!-- end scroll top -->
<link href="<?php echo ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">

<link type="text/css" href="<?php echo ASSETS_CSS ?>/c/jquery.jscrollpane.css" rel="stylesheet" media="all" />


<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>-->
<!--<script type="text/javascript" src="<?=WEB_PATH?>/js/jquery-1.9.0.min.js"></script>-->
					
<!-- start category slider 05 12 2013 -->


<!-- start category slider 05 12 2013 -->

<?php  if($total_records1>0 || $total_records1!="" ){  ?> 
  

<script type='text/javascript' src="https://maps.google.com/maps/api/js?sensor=true"></script>
<!--<script type='text/javascript' src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script> -->

<script type="text/javascript">
jQuery(document).ready(function(){

	var hashval = window.location.hash ;
	hashval = unescape(hashval);
	hashval  = hashval.substring(2,(hashval.length -1)); 
	/**/
            if(hashval.length != 0)
            {
			jQuery("#hdn_reload_pre_selection").val(hashval);
			 
                // window.location.hash = '';
				 //jQuery(".location_tool[locid='"+locid+"'] .loca_total_offers span").trigger("mousedown");
				/* display marker */
				//jQuery(".location_tool[locid='"+locid+"']").trigger("mouseenter");
				/* display marker */
            }
});

 var visited_arr = new Array();
    function try1(val,campid,locid) {
          //  alert("In"+campid+"=="+locid);
          
            for (var prop in markerArray) {
                   if(prop != "indexOf")
                                     markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
                            }
           
            infowindow.setContent(infowindowcontent[locid]);
            markerArray[locid].setIcon('<?php echo ASSETS_IMG?>/c/pin-small-blue.png'); 
            infowindow.open(map,markerArray[locid]);
			
			var arr = [];
			var obj = {};
			  obj['href'] =  $(val).attr('mypopupid');
			  arr.push(obj);
			  var org_text = $(val).attr('mypopupid');
			//  alert($(val).parent().parent('.deal_blk'));

			 var index_of_cur = $(val).parent().parent('.deal_blk').index();
			 var index_of_cur = $(val).parent().parent().parent().index();
			 
			 var tot_element = ($('.searchdeal_offers #deal_slider_'+locid +'  .deal_blk').length);
			 
			 for(var k = index_of_cur ;k < tot_element ;k++)
			 {
				if($('.searchdeal_offers #deal_slider_'+locid +'  .deal_blk:eq(' + k + ')').parent().attr("class") == "show_deal_blk" )
				 {
					 var obj = {};
					if(org_text != $('.searchdeal_offers #deal_slider_'+locid +'  .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid'))
					{
					 obj['href'] = $('.searchdeal_offers #deal_slider_'+locid +'  .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid');
					 //alert($(this).find('.dealtitle').attr('mypopupid')); 
					 arr.push(obj);
					}  
				}
			 }
			 for(var k = 0 ;k < index_of_cur ;k++)
			 {
				if($('.searchdeal_offers #deal_slider_'+locid +'  .deal_blk:eq(' + k + ')').parent().attr("class") == "show_deal_blk" )
				 {
						 var obj = {};
						if(org_text != $('.searchdeal_offers #deal_slider_'+locid +' .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid'))
						{
						 obj['href'] = $('.searchdeal_offers #deal_slider_'+locid +' .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid');
					   /*  alert($(this).find('.dealtitle').attr('mypopupid')); */
						 arr.push(obj);
						}  
				}
			 }
			
				
			    jQuery.fancybox(arr,{
				//href: this.href,
				
				//href: $(val).attr('mypopupid'),
				
				content:jQuery('#myDivID_3').html(),
				
				width: 425,
				height: 370,
				fitToView:true,
				imageScale:true,
				autoSize:false,
				type: 'html',
				openEffect : 'elastic',
				openSpeed  : 300,
                                 
				closeEffect : 'fade',
				closeSpeed  : 300,
				// topRatio: 0,
                              
				changeFade : 'fast',			
				prevEffect : 'left',
				nextEffect : 'right',
				
				helpers: {
					overlay: {
					opacity: 0.4
					} // overlay
				},
				 afterShow:function(){
				
					
                                      ct = getParam( 'cust_id' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                    //alert(ct);
                                    if(ct  != 0)
                                        {
                                        //    alert(jQuery("#hdn_campaign_list").length);
                                            if(jQuery("#hdn_campaign_list").length >0)
                                            {
                                                 campid =  getParam( 'campid' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                    ct = getParam( 'cust_id' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                    locid = getParam( 'locationid' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                    visited_c = getParam( 'visited' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                                campaigns = jQuery("#hdn_campaign_list").val();
                                             //   alert(campaigns);
                                                var camp_list_arr = campaigns.split(";");
                                                if( in_array(campid,camp_list_arr)){
                                                
                                                         if( !in_array(campid,visited_arr)){
                                     visited_arr.push(campid);
                                    //alert(visited_arr.length);
                                    if(visited_c == 0){
                                    url_d = $(this).attr('href');
                                     url_d = url_d.replace(/(visited=)[^\&]+/, '$1' + "1");
                            jQuery(".searchdeal_offers .deal_blk[locid='"+locid+"'][campid='"+ campid+"'] .dealtitle").attr('mypopupid',url_d);
                          //  alert( jQuery(".displayul .deal_blk[campid='"+ campid+"'] .dealtitle").length);
                                    jQuery(".searchdeal_offers .deal_blk[campid='"+ campid+"'] .dealtitle").each(function(){
                                        url_d = $(this).attr('mypopupid');
                                     url_d = url_d.replace(/(visited=)[^\&]+/, '$1' + "1");
                                             $(this).attr('mypopupid',url_d);
                                    });
                                    jQuery(".searchdeal_offers .deal_blk[campid='"+ campid+"'] .dealtitle").each(function(){
                                        url_d = $(this).attr('mypopupid');
                                     url_d = url_d.replace(/(visited=)[^\&]+/, '$1' + "1");
                                             $(this).attr('mypopupid',url_d);
                                    });
                                    
                            v_c = $("#visited_counter").val();
                            n_v_c = parseInt(v_c)+ 1;
                            $("#visited_counter").val(n_v_c);
                          //  alert(campid+"==="+jQuery(".displayul .deal_blk[campid='"+ campid+"'] .dealtitle").length);
                            //alert()
                                    }
                                    
                                     }
                                                }
                                                
                                            }
                                        

                                    jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'campaign_view_ajax=true&campaign_id='+campid+'&customer_id='+ct,
                  async:false,
		  success:function(msg)
		  {
//                      alert(msg);
                  }
              });
                                        }          
                                },
                                beforeShow:function(){

				
				//$(".fancybox-inner").addClass("Class_fancy_ie");
                                 //  alert($(this).attr('href')+"==="+decodeURI($(this).attr('href') ));
                                 //  jQuery(".fancybox-wrap").fadeOut("slow");
                                  // jQuery(".fancybox-wrap").fadeIn("slow");
                                      jQuery(".fancybox-inner .popupmainclass").css("display","block");
                                    jQuery(".fancybox-inner .email_popup_div").css("display","none");
                                    jQuery(".fancybox-inner .mainloginclass").css("display","none");
                                    jQuery(".fancybox-inner .updateprofile").css("display","none");
                                      jQuery(".fancybox-inner  .errormainclass").css("display","none");
                                    jQuery(".fancybox-inner  .successmainclass").css("display","none");
                                      //    alert(decodeURIComponent($(this).attr('href') )); 
                                            var ct =getParam( 'CampTitle' , decodeURI($(this).attr('href') ));
                                            
                                    ct = unescape(ct );
											//alert(ct);
                                        ct = ct.replace(/\+/g," ");
											//alert(ct);
											//alert(escape(ct));
                                     //   alert(ct);
                                        var campaign_title= ct;
                                         jQuery('.fancybox-inner .CampTitle').html( ct );
                                         
                                          var ct =getParam( 'businessname' , decodeURI($(this).attr('href') ));
                                          var business_name=ct;
                                         
                                    ct = unescape(ct );
                                        ct = ct.replace(/\+/g," ");
                                         jQuery('.fancybox-inner .bussinessclass span').html( ct );
                                        ct =getParam( 'number_of_use' , decodeURIComponent($(this).attr('href') ));
                                         var ct1 =getParam( 'new_customer' , decodeURIComponent($(this).attr('href') ));
                                         var disp_str ="";
                                     if(ct==1)
                                    {
                                        if(ct1==1)
                                        {
                                            disp_str = "<div style='margin-top:5px;'><b>Limit : </b>One Per Customer,Valid For New Customer Only</div>";
                                        }
                                        else 
                                        {
                                            disp_str ="<div style='margin-top:5px;'><b>Limit : </b>One Per Customer</div>";
                                        }
                                    }
                                    else if(ct==2)
                                        disp_str = "<div style='margin-top:5px;'><b>Limit : </b>One Per Customer Per Day</div>";
                                    else if(ct==3)
                                        disp_str = "<div style='margin-top:5px;'><b>Limit : </b>Earn Redemption Points On Every Visit</div>";
                                       jQuery('.fancybox-inner .limitclass').html(disp_str);
                                       
                                      
                                     ct = getParam( 'address' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ")+" "+getParam( 'city' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ")+" "+getParam( 'state' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ")+" "+getParam( 'country' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ")
                                     var location_address=ct;
                                     
                                    jQuery('.fancybox-inner .popup_address').html(decodeURIComponent(ct));
                                    ct = getParam( 'campaign_tag' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                    var campaign_tag=ct;
                                     ct = getParam( 'img_src' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                     jQuery('.fancybox-inner .popup_image').attr("src",(decodeURIComponent(ct)));
                                     
                                      ct = getParam( 'redeem_rewards' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                      var sharepoint=ct;
                                     jQuery('.fancybox-inner .popupredeemrewards p').html(decodeURIComponent(ct));
                                     
                                      ct = getParam( 'referral_rewards' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                     jQuery('.fancybox-inner .popupreferralrewards p').html(decodeURIComponent(ct));
                                     
                                      ct = getParam( 'o_left' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                     if(ct > 10)
									  {
										jQuery('.fancybox-inner .popupoleft1 p').html("10+");
									  }
									  else
									  {
										jQuery('.fancybox-inner .popupoleft1 p').html(decodeURIComponent(ct));
									  }
                                     
                                     ct = getParam( 'expiration_date' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                      jQuery('.fancybox-inner .popup_expiration').html(decodeURIComponent(ct));
                                    
                                    
                                    ct = getParam( 'cust_id' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                    //alert(ct);
                                    if(ct  == 0)
                                        {
                                         
                                                   jQuery('.fancybox-inner .popup_sharediv').detach();
                                                   jQuery('.fancybox-inner .popup_sharetext').html('<?php echo $client_msg['common']['label_Popup_Without_Login_Please'];?> <a href="javascript:void(0)" id="Notificationlogin" ><?php echo $client_msg['common']['label_Popup_Without_Login'];?></a> <?php echo $client_msg['common']['label_To_Share_Reserve'];?>');
                                        }
                                        else{
                                                     jQuery('.fancybox-inner .popup_sharetext').html("<?php echo $client_msg['common']['label_Share_Earn_Points'];?>");
                                              
                                        }
                                        
                                      var dci =   getParam( 'decoded_cust_id' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," "); 
                                      
                                       var imgsrc =   getParam( 'img_src' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," "); 
                                        
                                         
                                        //var summary =  getParam( 'deal_desc' , unescape(decodeURI($(this).attr('href')) )).replace(/\+/g," ");
										
										var summary =  unescape(getParam( 'deal_desc' , decodeURI($(this).attr('href')) )).replace(/\+/g," ");
										
                                          var webpath =  getParam( 'webpath' , unescape(decodeURI($(this).attr('href')) )).replace(/\+/g," ");
                                       
                                          
                                          //For facebook
                                       var th_link = webpath+"/register.php?campaign_id="+campid+"&l_id="+locid+"&share=true&customer_id="+dci;
                                       ct= getParam('fblink',$(this).attr('href') );
                                      //jQuery(".fancybox-inner .btn_share_facebook").attr("onClick","window.open('http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - "+encodeURIComponent(campaign_title)+"&p[summary]="+encodeURIComponent(summary)+"&p[url]="+encodeURIComponent(th_link)+"&p[images][0]="+imgsrc+"', 'sharer', 'toolbar=0,status=0,width=548,height=325')");
                                        
//jQuery(".fancybox-inner .btn_share_facebook").attr("onClick","facebookfunction('"+campaign_title+"','"+summary+"','"+th_link+"','"+imgsrc+"')");
                                jQuery(".fancybox-inner .btn_share_facebook").attr("id","facebook_"+campid+"_"+locid);
                                jQuery(".fancybox-inner .btn_share_facebook").attr("data",campid+'###'+locid+'###'+business_name+'###'+campaign_title+'###'+summary+'###'+th_link+'###'+imgsrc+'###'+location_address+'###'+sharepoint+'###'+campaign_tag);
                                
                                
							
										// 01 10 2013
										//jQuery(".fancybox-inner .btn_share_facebook").attr("onClick","facebookfunction('"+escape(campaign_title)+"','"+escape(summary)+"','"+th_link+"','"+imgsrc+"')");
										// 01 10 2013
										
                                        // for facebook
                                       
                                       // For tweeter
                                       var th_link = webpath+"/register.php?campaign_id="+campid+"&l_id="+locid+"&share=true&customer_id="+dci;
                                       ct= getParam('fblink',$(this).attr('href') );
//                                       <a data-count="none" data-lang="en" class="twitter_link" url="http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D" href="https://twitter.com/intent/tweet?original_referer=http%3A%2F%2Fwww.scanflip.com%2Fpopup_for_mymerchant.php%3FCampTitle%3DKfc-private-kfc-toronto-camp3%26businessname%3DKFC%26number_of_use%3D3%26new_customer%3D0%26address%3DF4-1000%2BGerrard%2BSt%2BE%26city%3DToronto%26state%3DON%26country%3DUSA%26zip%3DM4M%25203G6%26redeem_rewards%3D12%26referral_rewards%3D12%26o_left%3D2%26expiration_date%3D2013-05-23%25205%3A04%3A00%2520AM%26img_src%3Dhttp%3A%2F%2Fwww.scanflip.com%2Fmerchant%2Fimages%2Fdefault_campaign.jpg%26campid%3D549%26locationid%3D91%26deal_desc%3Ddgdf&amp;text=ScanFlip%20%7C%20Campaign&amp;tw_p=tweetbutton&amp;url=http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D">Twitter</a> 
                                     jQuery(".fancybox-inner .twitter_link").attr("url",encodeURIComponent(th_link));
                                 //   jQuery(".fancybox-inner .twitter_link").attr("href","http://twitter.com/share?url="+encodeURIComponent(th_link)+"&text="+encodeURIComponent(campaign_title));
                                   //   jQuery(".fancybox-inner .twitter_link").attr("href","http://twitter.com/share?url="+encodeURIComponent('http://www.joyofkosher.com')+"&text=Google+Offers+-+$69+for+4+days/3+nights+in+Orlando+($637+value)");
                                  // jQuery(".fancybox-inner .twitter_link").attr("href","https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20&tw_p=tweetbutton&url="+encodeURIComponent(th_link))
                                 
                                //jQuery(".fancybox-inner .twitter_link").attr("onClick","window.open('https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20"+encodeURIComponent(campaign_title)+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link)+"','_blank')");
                                
                                //jQuery(".fancybox-inner .twitter_link").attr("onClick","twitterfunction('"+webpath+"','"+campaign_title+"','"+th_link+"')");
								
								// 01 10 2013
				//jQuery(".fancybox-inner .twitter_link").attr("onClick","twitterfunction('"+webpath+"','"+escape(campaign_title)+"','"+th_link+"')");
								// 01 10 2013
								
                                // jQuery(".fancybox-inner .twitter_link").attr("onClick","facebookfunction('"+campaign_title+"','"+summary+"','"+th_link+"','"+imgsrc+"')");
                                 //
  //   jQuery(".fancybox-inner .twitter_link").attr("target","_parent_parent");
  //   
                             jQuery(".fancybox-inner .twitter_link").attr("id","facebook_"+campid+"_"+locid);
                                jQuery(".fancybox-inner .twitter_link").attr("data",campid+'###'+locid+'###'+business_name+'###'+campaign_title+'###'+summary+'###'+th_link+'###'+imgsrc+'###'+location_address+'###'+sharepoint+'###'+campaign_tag);
                                
                              //jQuery(".fancybox-inner .google_plus_link").attr("id","<?php echo WEB_PATH?>/register.php?campaign_id="+campid+"&l_id="+locid+"&share=true&customer_id=<?php echo $cust_id_with_url;?>");  
                             //jQuery(".fancybox-inner .google_plus_link").attr("onClick","google_sharing('"+campid+"','"+locid+"','true','<?php echo $cust_id_with_url;?>')");
                                  // For tweeter
                                        
                                        
                                        
                                        campid =  getParam( 'campid' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                        locid = getParam( 'locationid' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
							// for google plus
							
							var th_link1 = webpath+"/register.php?campaign_id="+campid+"&l_id="+locid+"&domain=3&share=true&customer_id="+dci;
							
							jQuery("#hdn_campid").val(campid);
							jQuery("#hdn_locid").val(locid);
							
							//alert(th_link1);
							
							var campaign_tag_temp=campaign_tag;
							var campaign_tag_array=campaign_tag_temp.split(',');
							var tag_main_all="";
							if(campaign_tag_temp != "" || typeof(campaign_tag_temp)!="undefined")
							{
								if(campaign_tag_array[0] == "" || typeof(campaign_tag_array[0])=="undefined")
								{

								}
								else
									{
										tag_main_all += "#"+campaign_tag_array[0]+" ";
									}
								if(campaign_tag_array[1] == "" || typeof(campaign_tag_array[1])=="undefined")
								{

								}
								else
									{
										tag_main_all += "#"+campaign_tag_array[1]+" ";
									}
								if(campaign_tag_array[2] == "" || typeof(campaign_tag_array[2])=="undefined")
								{

								}    
								else
									{
										tag_main_all += "#"+campaign_tag_array[2]+" ";
									}
							}
							
							//alert(business_name);
							business_name1 = unescape(business_name );											
                            business_name1 = business_name1.replace(/\+/g," ");
							//alert(business_name1);
							var g_p_msg="#Scanflip Offer -"+campaign_title+'.. now available at '+business_name1+' participating locations.Limited Offers.. Reserve Now! '+tag_main_all;
							
							//alert(th_link1);
							
							jQuery(".fancybox-inner .google_plus_link").attr("data-prefilltext",g_p_msg);
							jQuery(".fancybox-inner .google_plus_link").attr("data-contenturl",th_link1);
							jQuery(".fancybox-inner .google_plus_link").attr("data-calltoactionurl",th_link1);
							jQuery(".fancybox-inner .google_plus_link").attr("data-calltoactionurl",th_link1);
							
							jQuery(".fancybox-inner .google_plus_link").attr("data-contentdeeplinkid","campaign_id="+campid+"&l_id="+locid+"&customer_id="+dci);
							jQuery(".fancybox-inner .google_plus_link").attr("data-calltoactiondeeplinkid","campaign_id="+campid+"&l_id="+locid+"&customer_id="+dci);
							
                            
							jQuery(".fancybox-inner .google_plus_link").removeAttr("data-gapiscan");
							jQuery(".fancybox-inner .google_plus_link").removeAttr("data-onload");
							jQuery(".fancybox-inner .google_plus_link").removeAttr("data-gapiattached");
							
							(function() 
							{
									var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
									po.src = 'https://apis.google.com/js/plusone.js';
									var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
							})();
							
							// for google plus
							
                                           setCookie("redirecting_url_fb","<?php echo WEB_PATH ?>/search-deal.php#@"+campid+"|"+locid+"@",365);
                                          jQuery(".btn_mymerchantreserve").attr("lid",locid);
                                          jQuery(".btn_mymerchantreserve").attr("cid",campid);
                                        var camptitle =getParam( 'CampTitle' , decodeURIComponent($(this).attr('href') ));
                                        camptitle = camptitle.replace(/\+/g," ");
                                        
                                        var vid = getParam('voucher_id',decodeURIComponent($(this).attr('href') )).replace(/\+/g," " );
                                        $(".fancybox-inner #ShowVoucherId").attr("vid",vid);
                                      //  alert(vid);
                                          ct= getParam('is_reserve',decodeURIComponent($(this).attr('href') )).replace(/\+/g," " );
                                          var mydeals = getParam('is_mydeals',decodeURIComponent($(this).attr('href') )).replace(/\+/g," " ); 
                                          if(dci == 0)
                                              {
                                                   $(".fancybox-inner .btn_mymerchantreserve").detach();
                                              }
                                          if(mydeals == 1){
                                          if(ct == 1)
                                              {
                                                     ct1= getParam('br',decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                                   $(".fancybox-inner .barcode").attr("src","showbarcode.php?br="+ct1);
                                                    $(".fancybox-inner .btn_mymerchantreserve").detach();
                            $(".fancybox-inner .sharediv").css("display","block");
                            $(".fancybox-inner .showvocherdiv").css("display","none");
                            $(".fancybox-inner #ShowshareId").css("display","none");
                            $(".fancybox-inner #ShowVoucherId").css("display","block");
			    $(".fancybox-inner #saveofferid").show();
										
                            //jQuery(".fancybox-inner .popupoleft1").css("display","none");
                            jQuery(".fancybox-inner .printclass").css("display","block");
                                              }
                                          }
										
                                        if(parseInt(jQuery(".fancybox-inner .popupoleft1 p").text()) == 0)
										  {
											jQuery(".fancybox-inner .popupoleft1").css("display","none");
										  }
                                      //  alert(jQuery('.fancybox-inner .email_popup_div #reffer_campaign_id').length);
                                        jQuery('.fancybox-inner .email_popup_div #reffer_campaign_id').val(campid);
                                      //  alert(jQuery('.fancybox-inner .email_popup_div #reffer_campaign_id').val());
                                        jQuery('.fancybox-inner .email_popup_div #reffer_campaign_name').val(camptitle);
                                         jQuery('.fancybox-inner .email_popup_div #refferal_location_id').val(locid);
                                         
                                         /* For reserve button */
                                        // alert(jQuery("#rservedeals_value").val()+"===reservedeals");
                                        //  alert(jQuery("#deal_barcode").val()+"===Barcode");
      var ar =jQuery("#rservedeals_value").val();
   //   alert(ar);
      ar = ar.substring(0,ar.length-1);
        a =  ar.split(";");
        var clid= campid+"="+locid;
        var disp_flag = false;
       // alert(a.length+"=="+jQuery("#rservedeals_value",window.parent.document).val()+"==");
       if(ar != "")
           {
               
            c = jQuery("#deal_barcode").val().split(";");
            for(i=0;i<c.length;i++){
                d = c[i].split("&");
                if(clid  == d[0])
                {
                         $(".fancybox-inner .barcode").attr("src",d[1]);
                }
            }
       for(i=0;i<a.length;i++){
                b= a[i].split(":");
                if(clid == b[0]+"="+b[1])
                {
                    disp_flag = true;
                  //  alert("in");
                     $(".fancybox-inner .btn_mymerchantreserve").detach();
                            $(".fancybox-inner .sharediv").css("display","block");
                            $(".fancybox-inner .showvocherdiv").css("display","none");
                            $(".fancybox-inner #ShowshareId").css("display","none");
                            $(".fancybox-inner #ShowVoucherId").css("display","block");
			    $(".fancybox-inner #saveofferid").show();
                       //     alert("out");
                }
        }
           }
           
           /* for reserve button */
                                          
                                         
                                         bind_popup_event();
                                        
                                },
                                beforeLoad:function(){
                                  //  alert($(this).attr('href'));
				 //$(".fancybox-opened").css ("width","425");	
				  //$(".fancybox-inner").css ("overflow","hidden");
				 // $(".fancybox-outer").css ("overflow","hidden");
 				 // $(".fancybox-outer").css ("width","425");
                                    
                                },
				afterLoad: function(){
 
				   $('body, html').css('overflowX','hidden');
				    
                                     var ct =getParam( 'CampTitle' , $(this).attr('href') );
                                    jQuery('.fancybox-inner .CampTitle').html(ct);
                                    // alert(jQuery('.fancybox-inner .CampTitle').html());
                                      //alert("after load");  
                                //  alert($(this).attr('href'));
                                  var $href_url=$(this).attr('href');
                                  var $str_arr=$href_url.split("&");
                                  var $camp_id=$str_arr[14].split("=");
                                  var $loc_id=$str_arr[15].split("=");
                                  //alert($camp_id[1]);
                                  //alert($loc_id[1]);
                                  
                                  for (var prop in markerArray) {
                                      if(prop != "indexOf")
                                     markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
                                    }
           
                                    infowindow.setContent(infowindowcontent[$loc_id[1]]);
                                    markerArray[$loc_id[1]].setIcon('<?php echo ASSETS_IMG?>/c/pin-small-blue.png'); 
                                    infowindow.open(map,markerArray[$loc_id[1]]);
                                  
                                },
                                afterClose:function(){
									jQuery('script[src^="https://apis.google.com/js/plusone.js"]').remove();
				del_cookie("redirecting_url_fb");
				del_cookie("redirecting_url_fb_location");
                                  /* v_c = $("#visited_counter").val();
                                   alert(v_c+"v_C");
                                      m_c = $("#m_counter").html();
                                      alert(m_c+"m_C");
                                  
                                      t_c = parseInt(m_c) - parseInt(v_c);
                                     alert(t_c+"t_c");
                                       $("#m_counter").html(t_c);
                                       if(t_c == 0){
                                           $("#m_counter").css("display","none");
                                       }
                                     alert( $("#m_counter").html());
                                  $("#visited_counter").val("0"); */
                                      jQuery(".fancybox-inner .popupmainclass").css("display","block");
                                    jQuery(".fancybox-inner .email_popup_div").css("display","none");
                                    jQuery(".fancybox-inner .mainloginclass").css("display","none");
                                    jQuery(".fancybox-inner .updateprofile").css("display","none");
                                      jQuery(".fancybox-inner  .errormainclass").css("display","none");
                                    jQuery(".fancybox-inner  .successmainclass").css("display","none");
                                   selected_cat_id=getCookie("cat_remember");
                           miles_cookie=getCookie("miles_cookie");
                     //   alert(jQuery("#hdn_reserve_err").val());
                                  if(jQuery("#hdn_reserve_err").val() != "")
                                      {
                                         
                               /*     if(jQuery(".showalld").length != 0)
                                    {
                                        l = jQuery("#hdn_reserve_err").val();
                                            if(jQuery(".displayul .deal_blk[locid='"+l+"']").length == 0){
                                                     markerArray[l].setVisible(false);
                                                      infowindow.close();
                                               }
                                           filter_deals_algorithm_by_location(selected_cat_id,miles_cookie,l);
                                            }else{ */
                                                 filter_locations(selected_cat_id,miles_cookie);
                                            //}
                                             jQuery("#hdn_reserve_err").val("");
                                      }
                                //alert("in close");
                            if(jQuery("#rservedeals_value").val()!= ''){
                           if(jQuery(".showalld").length != 0)
                            {
                                 jQuery(".searchdeal_offers .deal_blk").each(function(){
                                    l = jQuery(this).attr("locid") ;
                                 });
                                    a =  jQuery("#rservedeals_value").val().split(";");
                                   for(i=0;i<a.length;i++){
                                       b= a[i].split(":");
                                      // jQuery(".searchdeal_offers .deal_blk[locid='"+b[1]+"'][campid='"+b[0]+"']").detach();
                                       //jQuery(".searchdeal_offers .deal_blk[locid='"+b[1]+"'][campid='"+b[0]+"']").detach();
                                        
                                   }
                                        if(jQuery(".searchdeal_offers .deal_blk[locid='"+l+"']").length == 0){
                                            // markerArray[l].setVisible(false);
                                             // infowindow.close();
                                       }
                                   //filter_deals_algorithm_by_location(selected_cat_id,miles_cookie,l);
                            }else{
                                a =  jQuery("#rservedeals_value").val().split(";");
                                   for(i=0;i<a.length;i++){
                                       b= a[i].split(":");
                                     //  jQuery(".searchdeal_offers .deal_blk[locid='"+b[1]+"'][campid='"+b[0]+"']").detach();
                                      // jQuery(".searchdeal_offers .deal_blk[locid='"+b[1]+"'][campid='"+b[0]+"']").detach();
                                      
                                   }
                                   // filter_locations(selected_cat_id,miles_cookie);
                                    
                            }
                            jQuery("#rservedeals_value").val("");
                                }
                                }
			}); // fancybox
			
			var top = (jQuery(window).height() / 2) - (jQuery(".fancybox-wrap").outerHeight() / 2);
                       
    var left = (jQuery(window).width() / 2) - (jQuery(".fancybox-wrap").outerWidth() / 2);
    
    jQuery(".fancybox-wrap").css({ top: top, left: left});
                        //jQuery('.fancybox-wrap').css('margin-top', '200px');
                     //   bind_reserve_event();
		}
                function in_array (needle, haystack, argStrict) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: vlado houba
  // +   input by: Billy
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
  // *     returns 1: true
  // *     example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
  // *     returns 2: false
  // *     example 3: in_array(1, ['1', '2', '3']);
  // *     returns 3: true
  // *     example 3: in_array(1, ['1', '2', '3'], false);
  // *     returns 3: true
  // *     example 4: in_array(1, ['1', '2', '3'], true);
  // *     returns 4: false
  var key = '',
    strict = !! argStrict;

  if (strict) {
    for (key in haystack) {
      if (haystack[key] === needle) {
        return true;
      }
    }
  } else {
    for (key in haystack) {
      if (haystack[key] == needle) {
        return true;
      }
    }
  }

  return false;
}
  function getParam( name , url )
{
   
 name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
 var regexS = "[\\?&]"+name+"=([^&#]*)";
 var regex = new RegExp( regexS );
 var results = regex.exec(url);
 if( results == null )
    return "";
 else
  return results[1];
}

</script>
<script type='text/javascript'>//<![CDATA[
      var not_found_msg1 = "<?php echo  $not_found_msg; ?>";
    var map = null;
     var infowindow = null;
    var infowindowcontent =[];
				var markerArray = [];
	jQuery(window).load(function(){
		jQuery(".jspVerticalBar").hide();  
	});			
    jQuery(document).ready(function(){
	var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
	if(!iOS)
	{
		jQuery(function() {
			jQuery('.location').jScrollPane({
			horizontalGutter:5,
			verticalGutter:5,
			'showArrows': false,
                        mouseWheelSpeed: 50,
						 animateScroll:true,
			  animateDuration:700,
				animateEase:'linear'
                        
			});
                        
                        
                           
		});
}
		jQuery('.location').mouseenter(function(){
			jQuery(this).find('.jspVerticalBar').stop(true, true).fadeIn('slow');
		});
		jQuery('.location').mouseleave(function(){
			jQuery(this).find('.jspVerticalBar').stop(true, true).fadeOut('slow');
		});
  
	//alert("ready 940");
  if ( $.browser.msie )
  {
    jQuery('html').addClass('ie');
    if(jQuery.browser.version == '6.0')
    {
	jQuery('html').addClass('ie6');
    }
    else if(jQuery.browser.version == '7.0')
    {
	jQuery('html').addClass('ie7');
    }
    else if(jQuery.browser.version == '8.0')
    {
	jQuery('html').addClass('ie8');
    }
    else if(jQuery.browser.version == '9.0')
    {
	jQuery('html').addClass('ie9');
    }
 }
 else if ( jQuery.browser.webkit )
 {
    jQuery('html').addClass('webkit');
 }
 else if ( jQuery.browser.mozilla )
 {
    jQuery('html').addClass('mozilla');
 }
 else if ( jQuery.browser.opera )
 {
    jQuery('html').addClass('opera');
 }
 del_cookie("redirecting_url_fb");
 del_cookie("redirecting_url_fb_location");
   //create a global array to store markers
				var locations = [
			
						<?
                                                    //$RSCompLoc->MoveFirst();
						//$total = $RSCompLoc->RecordCount();
						$total=0;
						$count = 1;
						$lat = $lng = "";
						$DetailHtml="";
						$Campaign_list ="";
						$ids ="";
						   $loc_arr=array();
							foreach($records_array1_loc as $Row)
							{	
							
							$lat = (string) $Row->latitude;
							$lng = (string) $Row->longitude;
							
							$location_name = $Row->location_name;
							$location_pic = $Row->picture;
							//--------------------------------------------------------------------------
										//$storeid = $Row->locid;
										$storeid = $Row->location_id;
							
							?>
											
							
							
			  <?php  //}
                       
                           if(!in_array($Row->location_id ,$loc_arr)){
                                         array_push($loc_arr,$Row->location_id );
                                         	$lat = (string) $Row->latitude;
							$lng = (string) $Row->longitude;
							
							$location_name = $Row->location_name;
							$location_pic = $Row->picture;
							//--------------------------------------------------------------------------
							$storeid = $Row->location_id;
							
							/*$address = $Row->address.", ".$Row->city.", ".$Row->state.", ".$Row->zip.", ".$Row->country;			*/
						//	$image = '<img height="60" src="'.WEB_PATH.'/merchant/images/location/'.$location_pic.'" border="0" />';
							$MARKER_HTML = "<div  class='markerslayout'>";
							
										
							/*
							if($_SESSION['customer_id'] != ""){
								$MARKER_HTML .= '<div style="text-align:right"><a href="'.WEB_PATH.'/my-profile.php?new-location='.$Row['city'].'" target="_parent">Get Current Location</a></div>';
							}
							*/
                           
                        $busines_name  =  $Row->business;
       
		//$businessname = $busines_name ;
         //   }
                
       // }
                                                        $deal_distance1 ="";
                                                        
                                                        if(isset($_COOKIE['mycurrent_lati']) && isset($_COOKIE['mycurrent_long']))
                                                        {
														/*
														$from_lati1=$_COOKIE['mycurrent_lati'];
                
                                                        $from_long1=$_COOKIE['mycurrent_long'];
														*/
														$from_lati1=$mlatitude;
														$from_long1=$mlongitude;

                                                        $to_lati1=$Row->latitude;

                                                        $to_long1=$Row->longitude;

                                                        $deal_distance1 = $objJSON->distance($from_lati1, $from_long1, $to_lati1, $to_long1, "M")."Mi";
                                                        }
                                                        
                                                        
							//$MARKER_HTML .= '<div><b><a href="location_detail.php?id='.$cat_id.'" target="_parent" >'.$location_name.'</a></b></div>';
                            $MARKER_HTML .= "<div ><div class='marker_location_style'><b><a href='".$Row->location_permalink."' target='_parent' title='Merchant Location' >".$busines_name."</a></b></div><div class='marker_miles'><b>".$deal_distance1."</b></div></div>";
							//$MARKER_HTML .= $image."<br />";
							//$MARKER_HTML .= "<div>".$address."</div>";
                                                        
                                                        /*
                 Location Hours Code
                 */
                   $location_time="";
                   
                  // $location_time.="Today 12:00PM - 9:00PM";
                   if($Row->timezone_name != "")
                   {
                        //$location_time.=$Row->timezone_name;
                        $time_zone=$Row->timezone_name;
                   }
                   date_default_timezone_set($time_zone);
                   $current_day=date('D');
                   $current_time=date('g:i A');
                   
                     $location_time="";
                   $start_time = "";
                   $end_time="";
                   $status_time="";
                   
                           $start_time = $Row->location_starttime;
                           $end_time=$Row->location_endtime;
                           $location_time.=$Row->location_starttime." - ";
                           $location_time.=$Row->location_endtime;
                       
                   //$location_str .= "<br/>"."Current Time : ".$current_time ."Start Time :".$start_time."End Time : ".$end_time;
                   $st_time    =   strtotime($start_time);
                    $end_time   =   strtotime($end_time);
                    $cur_time   =   strtotime($current_time);
				//$MARKER_HTML  .= "==".$Row->currently_open;
					if($location_time != "")
                   {
											if($Row->currently_open!="Currently Close")	
											{
														$t_st = "<span class='loca_open_map'>Currently Open</span>";
												$MARKER_HTML  .= "<div class='open_close_div'>".$t_st;
											}
											else
											{
														$t_st = "<span class='loca_closen_map'>Currently Close</span>";
												$MARKER_HTML  .= "<div class='open_close_div'>".$t_st;
											}
											}
											else{
													$t_st = "<span class='loca_closen_map'>Currently Close</span>";
												$MARKER_HTML  .= "<div class='open_close_div'>".$t_st;
											}
		          
				        $maphref="https://maps.google.com/maps?saddr=&daddr=".$lat.",".$lng;
               $MARKER_HTML .="&nbsp;&nbsp;&nbsp;<a target='_blank' href='".$maphref."'  class='miles_icon' title='Get Direction'></a></div>";
							
							$MARKER_HTML .= "</div>";
                                                        if($count != 1) echo ",";
						?>
							["<?=$MARKER_HTML?>", <?=$lat?>, <?=$lng?>, <?=$count?>,"<?=$busines_name?>",'<? echo "0"; ?>',<?=$storeid?>]
						<?
							if($count != $total_records1) //echo ",";
							$count++;
                           }
                           else{
                               
                           }
                                                        }
                                                        
                           
                           
	      
						
						?>
					];
					
				
				var zoomlevel="";	
                
				<?php
					if(isset($_COOKIE["zoominglevel"]) && isset($_REQUEST['category_id']))
					{
				?>
					zoomlevel="<?php echo $_COOKIE["zoominglevel".$_REQUEST['category_id']];?>";
				<?php
					}
				?>
				
		
				if(zoomlevel != "")
				{
					
				}
				else
				{
					zoomlevel=10;
					
				}
				
				if(locations.length==1)
                                {
                                        zoomlevel=10
                                }
                        
				 function initialize() {
                                      checkCookie();
				  	var myOptions = {
						zoom:parseInt(zoomlevel),
						center: new google.maps.LatLng(<?=$lat?>, <?=$lng?>),
						mapTypeControl: true,
						mapTypeControlOptions: {
							style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
						},
						
						zoomControl: true,
						zoomControlOptions: {
						    style: google.maps.ZoomControlStyle.LARGE,
						    position: google.maps.ControlPosition.LEFT_CENTER
						},

						navigationControl: true,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
                                        
                                        //google.maps.event.trigger(map,"resize");
                                        //map.checkResize();

					google.maps.event.addListener(map, 'click', function() {
						infowindow.close();
                                                   for (var prop in markerArray) {
                                                       if(prop != "indexOf")
                                                         markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
                                    
                            }
					});
                                        
                                        
                                        
					google.maps.event.addListener(map, 'zoom_changed', function() {
						zoomLevel = map.getZoom();
						
						cat_id='<?php if(isset($_REQUEST['category_id'])){echo $_REQUEST['category_id'];}else{echo '0';}?>';
						//alert(cat_id);
						
						setCookie("zoominglevel"+cat_id,zoomLevel,365);
						
						//this is where you will do your icon height and width change.     
					});
					

					// Add markers to the map
					// Set up markers based on the number of elements within the myPoints array
                                      
					for (var i = 0; i < locations.length; i++) {
					
						createMarker(new google.maps.LatLng(locations[i][1], locations[i][2]), locations[i][0], locations[i][3], locations[i][4],locations[i][5],locations[i][6]);
					}
				//	var markerCluster = new MarkerClusterer(map, markerArray);
					// by deafult
				                      if(locations.length>1)
                                        {
                                            // start to set map possition in center to show all markers

                                            var latlngbounds = new google.maps.LatLngBounds();
                                            for (var i = 0; i < locations.length; i++) 
                                            {
                                                    //alert(locations[i][1]);
                                                    //alert(locations[i][2]);

                                               var lat = locations[i][1];
                                               var lng = locations[i][2];

                                               var latlng2 = new google.maps.LatLng(lat, lng);
                                               latlngbounds.extend(latlng2);
                                            }

                                            map.setCenter(latlngbounds.getCenter());
                                            map.fitBounds(latlngbounds); 

                                            // end to set map possition in center to show all markers
                                        }
										
                                      selected_cat_id = getCookie("cat_remember");
									  jQuery("#fltr_category").text(jQuery("#category_slider .current[mycatid='"+selected_cat_id+"'] span").text());
									miles_cookie = getCookie("miles_cookie");
										filter_locations(selected_cat_id,miles_cookie);
										var firstlocid = "";
										//firstlocid = jQuery(".location_tool").first().attr("locid");
										jQuery(".location_tool").each(function() {
										
											if(jQuery(this).css("display") != "none" )
											{
												if(firstlocid == ""){
													firstlocid = jQuery(this).attr("locid");
												}
											}
										});
										if(jQuery("#hdn_reload_pre_selection").val() != "" )
									{
									//alert("in if");
										hashval = jQuery("#hdn_reload_pre_selection").val();
										var split_arr = hashval.split("|");
										var campid = split_arr[0];
										var locid = split_arr[1];
										//alert(campid+"=="+locid);
										jQuery(".location_tool[locid='"+locid+"'] .loca_total_offers span").trigger("click");
										
										/* display marker */
										jQuery(".location_tool[locid='"+locid+"']").trigger("mouseenter");
										/* display marker */
										jQuery("#deal_slider_"+locid+" .deal_blk[campid='"+campid+"'][locid='"+locid+"'] .dealtitle").trigger("click");

									}else
									{
										//jQuery("#hdn_is_offer_div_click").val("0");
                                        //jQuery(".location_tool[locid='"+firstlocid+"'] .loca_total_offers span").trigger("click");
										
										/* display marker */
									//	jQuery(".location_tool[locid='"+firstlocid+"']").trigger("mouseenter");
										/* display marker */

									}
										//alert("starting"+firstlocid);
										
									//	$(".temp_location").html($(".filterd_location").html());
										
										/* number of reacords */
										jQuery(".location_tool").each(function(){
											
										});
										/* number of records */
										//jQuery(".location_tool .loca_total_offers span").trigger("click");
                                        if(typeof getCookie('searched_location') == "undefined" || getCookie('searched_location') == "" || getCookie('searched_location') == null ){
                                    }else{
											
										var aplyview=getCookie("view");
										//alert(aplyview);
										if(aplyview=="gridview")
										{
												//jQuery('#gridview').trigger('click');
												//jQuery(".info").css("display","none");
										}
										else
										{
											//	jQuery('#mapview').trigger('click');
												//jQuery(".info").css("display","block");
										}
											
                                    }
                                
				 }
				 infowindow = new google.maps.InfoWindow({
					size: new google.maps.Size(150, 50)
					//maxWidth : 400
				});
				 google.maps.event.addListener(infowindow,'closeclick',function(){
                                             for (var prop in markerArray) {
                                                 if(prop != "indexOf")
                                                 markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
                                            }
                                           });
			//	 jQuery(document).ready(function(){
     
     //});

				
				function createMarker(latlng,myContent , myNum, myTitle,id,lid) {
				        var cat_id = id; 
					var contentString = myContent;
					var marker = new google.maps.Marker({
						position: latlng,
						map: map,
						zIndex: Math.round(latlng.lat() * -100000) << 5,
						title: myTitle+" Offers" ,
						icon: new google.maps.MarkerImage('<?php echo ASSETS_IMG?>/c/pin-small.png')
					});
                                        //   marker.setVisible(false);
                                        
                                          infowindowcontent[lid]=myContent;  
					google.maps.event.addListener(marker, 'click', function() {
                                             for (var prop in markerArray) {
                                                 if(prop != "indexOf")
                                                 markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
                                            }
						infowindow.setContent( infowindowcontent[lid]);
						infowindow.open(map, marker);
                                                       
						marker.setIcon('<?php echo ASSETS_IMG?>/c/pin-small-blue.png');
						
						//alert(lid);
						//alert(jQuery("div.location_tool[locid='"+lid+"']").attr("scroll"));
						
						//alert(jQuery(".location_tool:visible").length);
						if(jQuery(".location_tool:visible").length>3)
						{	
							/*
							var scrollvalue="-"+jQuery("div.location_tool[locid='"+lid+"']").attr("scroll")+"px";
							//alert(scrollvalue);
							jQuery(".jspPane").animate({top:scrollvalue},500);
							*/
							var pane = jQuery('.location');
							var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
	if(!iOS)
	{
							pane.jScrollPane(
								{
									horizontalGutter:5,
									verticalGutter:5
									/*
									'showArrows': false,
									mouseWheelSpeed: 50,
									animateScroll:true,
									animateDuration:700,
									animateEase:'linear'
									*/
								}
							);
							}
							var api = pane.data('jsp');

							// Note, there is also scrollToX and scrollToY methods if you only
							// want to scroll in one dimension
							
							var scrollvalue=jQuery("div.location_tool[locid='"+lid+"']").attr("scroll");
							
							if(iOS)
							{
								jQuery('.location1').scrollTo(jQuery("div.location_tool[locid='"+lid+"']"),{ offsetTop : '250'});
							}
							else
							{
								api.scrollToY(parseInt(scrollvalue));
							}
						}
						jQuery(".location_tool").each(function(){
							jQuery(this).removeClass("current_loc");
						});
						jQuery("div.location_tool[locid='"+lid+"']").addClass("current_loc");
						/* for Highlighting */
						jQuery("div.location_tool").css('border-radius','')
						.css('box-shadow','')
						.css('opacity','1')
						
						jQuery("div.location_tool[locid='"+lid+"']").css('border-radius','5px 5px 5px 5px')
						.css('box-shadow','0 0 10px rgba(0,0,0,0.35)')
						.css('opacity','1')
						/* for Highlighting */
						
						var p = jQuery('.leftside').position();
						var ptop = p.top;
						var pleft = p.left;
						jQuery(window).scrollTop(ptop);
	
                        //parent.document.getElementById("slider_iframe").src = parent.document.getElementById("slider_iframe").src+"&order_by_cat="+cat_id;
					});
                                      
					
                    infowindowcontent[lid]=myContent;
					markerArray[lid]=marker;
                                        
					//markerArray.push(marker); //push local var marker into global array
				}
				

			//window.onload = initialize();
                            var prevload = window.onload ;
			window.onload = function(){
                            prevload();
                            initialize();
                        }
			
			
                          <?php
					if(isset($_COOKIE["zoominglevel"]) && isset($_REQUEST['category_id']))
					{
				?>
					zoomlevel="<?php echo $_COOKIE["zoominglevel".$_REQUEST['category_id']];?>";
				<?php
					}
				?>
                          
                          });
                       </script>

 <?php }else { // if(isset($_COOKIE['curr_address']))
		?> 


 <script type='text/javascript' src="https://maps.google.com/maps/api/js?sensor=true&.js"></script>
<!--<script type='text/javascript' src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script> -->
<?php if($mlatitude != "" && $mlongitude != "" ) { ?>
<script language="javascript">
               
               jQuery(document).ready(function(){
                //alert("ready 1611");   
               
          var zoomlevel="";         
        <?php
		if(isset($_COOKIE["zoominglevel"]) && isset($_REQUEST['category_id']))
		{
		?>		
		zoomlevel="<?php echo $_COOKIE["zoominglevel".$_REQUEST['category_id']];?>";
        <?php
		}
		?>        
               
		zoomlevel=7; 
		if(zoomlevel != "")
		{
			
		}
		else
		{
			zoomlevel=7;
			
		} 
			//alert(zoomlevel);   
			function initialize() {
			//alert("hi");
                               checkCookie();
					var myOptions = {
						zoom:parseInt(zoomlevel),
						center: new google.maps.LatLng(<?=$mlatitude?>, <?=$mlongitude?>),
						mapTypeControl: true,
						mapTypeControlOptions: {
							style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
						},
						navigationControl: true,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);                                       
                                        
					google.maps.event.addListener(map, 'zoom_changed', function() {
						zoomLevel = map.getZoom();
						
						cat_id='<?php if(isset($_REQUEST['category_id'])){echo $_REQUEST['category_id'];}else{echo '0';}?>';
						//alert(cat_id);
						
						setCookie("zoominglevel"+cat_id,zoomLevel,365);
						
						//this is where you will do your icon height and width change.     
					});

				}
                                var mlati = '<?=$mlatitude?>';
                                var mlong = '<?=$mlongitude?>';
                               
                         //       if(mlati !="" && mlong!="")
		   //  window.onload = initialize;
	    });
				//	alert("No deal found in given location");
				</script>
				<style>
	#imager{position:absolute;z-index:3;}
	#imagek{position:absolute;z-index:2;}
</style>	
 <?php } } ?>

</head>
<?php flush(); ?>
<body class="common mydeals" onLoad="checkCookie()">
<?php
require_once(CUST_LAYOUT."/header.php");
?>

 <div id="main_parent_print_div" style="display:none">
        <div id="print_coupon_div" >
    
        </div> 
 </div>

            <script>
    
    if (document.cookie.indexOf("curr_address") >= 0) {
	document.getElementById("zip").value = "<?php if(isset($current_address_string)){ echo $current_address_string;} ?>";
	document.getElementById('hdn_serach_value').value = "<?php if(isset($current_address_string)){ echo $current_address_string;} ?>";
         setCookie("curr_address","",-36000);
        }
      
        </script>
            <?php // echo $latitude_  ."====". $longitude_; 
            if($latitude_ != "" || $longitude_ != ""){ //exit(); 
                
            ?>
	<div id="content" class="cantent">
		<div class="my_main_div">
			<div id="contentContainer" class="contentContainer">
            <div class="mainside">
             <div id="hdn_error_message" style="display:none">
        <?php  echo  $not_found_msg; ?>
    </div>
                <input type="hidden" name="is_profileset" id="is_profileset" value="" />
				<input type="hidden" name="print_redirect_link" id="print_redirect_link" value="" />
                <input type="hidden" name="profile_view" id="profile_view" value="" />
                <input type="hidden" name="visited_deals" id="visited_deals" value="" />
                 <input type="hidden" name="visited_counter" id="visited_counter" value ="0" />
                 <input type="hidden" name="redeemvoucher_value" id="redeemvoucher_value" value="" />
                  <input type="hidden" name="hdn_reserve_err" id="hdn_reserve_err" value="" />
                  <input type="hidden" name="rservedeals_value" id="rservedeals_value" value="" />
                   <input type="hidden" name="deal_barcode" id="deal_barcode" value="" />
				    <input type="hidden" name="medium" id="medium" value="" />
				<input type="hidden" name="hdn_campid" id="hdn_campid" value="" />
				   <input type="hidden" name="hdn_locid" id="hdn_locid" value="" />
				
				<div class="leftside">
					<div class="wrapper">
						<?php	if( $detect->isiOS() ){

?>
						<div class="location1">
						<?php
						}
						else{
						?>
						<div class="location">
						<?php
						}
						?>
							<div id="filterView" class="location_header">
								<div class="newFilterControls">
									<h5 class="headerVisible">Search Filter : </h5>
                                                                        <!--<div class="filterclass"><a href="javascript:void(0)" rel="shareit" class="filterbutton">Filter</a></div>-->
                                    <input  type="text" name="shareit-field" id="shareit-field" class="field" placeholder="Filter by merchant name , product or services"  />
									<ul class="chips filterbuttonclass">
                                                                            <li class="chip"></li>
										<li class="chip"><span class="filterbuttondistance" rel="shareit">Distance</span></li>
										<li class="chip"><span class="filterbuttonopennow">Open Now</span></li>
										<li class="chip"><span class="filterbuttondistance"  rel="price">Price</span></li>
										<br/>
										<li style="float:left;clear:both;margin-left:4px;" class="chip"><span  class="filternewcampaigns">New Camapign</span></li>
										<li class="chip"><span class="filterbuttondistance" rel="discount">Discount</span></li>
										<li class="chip"><span  class="filterbuttonexpiringtoday">Expiring Today</span></li>
										
										
									</ul>
                                    <div class="arrow-up" id="filterdistanceidarrow" disp1="0"></div>
									<!-- Distance filter -->
									<div id="shareit-box" disp="0" >

										<div id="shareit-header"></div>
										<div id="shareit-body">
										<div id="shareit-blank"></div>
											<div id="shareit-icon">
												<table>
												<tr>
												<td>
												<?php
												$selected_miles = $_COOKIE["miles_cookie"];
												?>
													<table >
													<tr>
													<td>
														<a href="javascript:void(0)" style="" mval="2" class="<?php if($selected_miles==2){ echo 'selected_miles' ; } ?> miles twomi">&nbsp;2Mi</a>
													</td>
													<td>
														<a href="javascript:void(0)" style="" mval="5" class="<?php if($selected_miles==5){ echo 'selected_miles' ; } ?> miles fivemi">&nbsp;5Mi</a>
													</td>
													<td>
														<a href="javascript:void(0)" mval="10" style="" class="<?php if($selected_miles==10){ echo 'selected_miles' ; } ?> miles tenmi">10Mi</a>
													</td>

													</tr>
													<tr>
													<td>
														<a href="javascript:void(0)" mval="15" style=""  class="<?php if($selected_miles==15){ echo 'selected_miles' ; } ?> miles fifteenmi">15Mi</a>
													</td>
													<td>
														<a href="javascript:void(0)" mval="20" style="" class="<?php if($selected_miles==20){ echo 'selected_miles' ; } ?> miles twentymi">20Mi</a>
													</td>
													<td>
														<a href="javascript:void(0)" mval="25" style="" class="<?php if($selected_miles==25){ echo 'selected_miles' ; } ?> miles twentyfivemi">25Mi</a>
													</td>
													</tr>
													</table>
												</td>
												<td>
													<a href="javascript:void(0)" mval="50" style="" class="<?php if($selected_miles==50){ echo 'selected_miles' ; } ?> miles allmi">All</a>
												</td>
												</tr>
												</table>
											</div>
										</div>	

									</div>
									<!-- End of Distance filter -->
									<!-- price filter -->
									<div class="arrow-up" id="filterpriceidarrow" disp1="0"></div>
									<div id="price-box" class="price-box" disp="0" >
										<div id="price-header"></div>
										<div id="price-body">
											<div id="price-blank"></div>
											<div id="price-icon">
												<table>
												<tr>
												<td>
													<a href="javascript:void(0)" mval="1" style="" class="prices twentyfivemi">$</a>  
												</td>
												<td>
													<a href="javascript:void(0)" mval="2" style="" class="prices twentyfivemi">$$</a>
												</td>
												<td>
													<a href="javascript:void(0)" mval="3" style="" class="prices twentyfivemi">$$$</a>
												</td>
												<td>
													<a href="javascript:void(0)" mval="4" style="" class="prices twentyfivemi">$$$$</a>
												</td>
												</tr>
												</table>
											</div>
										</div>
									</div>
									<!-- End price filter -->
										<!-- Discount filter -->
									<div class="arrow-up" id="filterdiscountidarrow" disp1="0" ></div>
									<div id="discount-box" class="discount-box" disp="0" >
										<div id="discount-header"></div>
										<div id="discount-body">
											<div id="discount-blank"></div>
											<div id="discount-icon">
												<table>
												<tr>
												<td>
													<a href="javascript:void(0)" mval="1" style="" class="discounts twentyfivemi">%</a>  
												</td>
												<td>
													<a href="javascript:void(0)" mval="2" style="" class="discounts twentyfivemi">%%</a>
												</td>
												<td>
													<a href="javascript:void(0)" mval="3" style="" class="discounts twentyfivemi">%%%</a>
												</td>
												<td>
													<a href="javascript:void(0)" mval="4" style="" class="discounts twentyfivemi">%%%%</a>
												</td>
												
												</tr>
												</table>
											</div>
										</div>
									</div>
									<!-- End discount filter -->
								</div>
								<div class="newFilterControls">
									<h5 class="headerVisible">Sort By :</h5>
                                    <ul class="chips">
										<li class="chip"><span rel="sortit" sorttye="asc" class="sortbuttondistance">Distance</span></li>
										<li class="chip"><span  rel="sortit" sorttye="asc" class="sortbuttonrating">Rating</span></li>
									</ul>
								</div>
								<?php
									if(!isset($_COOKIE['cat_remember']))
					{
						$categoryid = 0;
					}
					else
					{
						$categoryid = $_COOKIE['cat_remember'];
						$curr_fltr_category="All Categories";
					}
								?>
								<div class="selected_fltrs">
                 <h5 class="headerVisible">
                      <?php echo $client_msg['search_deal']['label_Fitered_By']?>
                  </h5>
				  <ul>
				  <li>
                  <span id="fltr_category">
                      <?php echo $curr_fltr_category; ?>                   
                  </span>
				  <span id="fltr_category_close">
                 	   
                  </span>
				  </li>
				 <?php
                        if(isset($_COOKIE["miles_cookie"]))
                        {
                            $val_cat_remember = trim($_COOKIE["miles_cookie"]);
                        }
                        else{
                            $val_cat_remember = 50;
                        }
                        ?>	  
						<li>
                  <span id="fltr_mile"><?php echo $val_cat_remember ; ?> Mi</span>
				  <span id="fltr_mile_close">
					 <!--<img src="<?php echo WEB_PATH; ?>/templates/images/filter_close.png" style="" class="filterimage" alt=""></img>-->
					  
				 </span>
				 </li>
				 <li>
				 <span id="fltr_price" style="display:none"></span>
				  <span id="fltr_price_close" style="display:none" class="filterimage"><!--<img src="<?php echo WEB_PATH; ?>/templates/images/filter_close.png" style="" class="filterimage" alt=""></img>-->
					 </span>
					  </li>
					    <li>
										 <span id="fltr_discount" style="display:none"></span>
										  <span id="fltr_discount_close" style="display:none" class="filterimage"><!--<img src="<?php echo WEB_PATH; ?>/templates/images/filter_close.png" style="" class="filterimage" alt=""></img>-->
											  </span>
											  </li>
				 <li>
				 <span id="fltr_expiring" style="display:none">
                      <?php echo "Expiring Today" ?>                   
                  </span>
				  <span id="fltr_expiring_close" style="display:none" class="filterimage" ><!--<img src="<?php echo WEB_PATH; ?>/templates/images/filter_close.png" style="" class="filterimage" alt=""></img>-->
					  </span>
					  </li>

					<li>
				 <span id="fltr_newcampaign" style="display:none">
                      <?php echo "New Campaigns" ?>                   
                  </span>
				  <span id="fltr_newcampaign_close" style="display:none" class="filterimage" ><!--<img src="<?php echo WEB_PATH; ?>/templates/images/filter_close.png" style="" class="filterimage" alt=""></img>-->
					  </span>
					  </li>		
					  <li>
				  <span id="fltr_opennow" style="display:none">
				  <?php echo "Open Now" ?>                   
                  </span>
				  <span id="fltr_opennow_close" style="display:none" class="filterimage" ><!--<img src="<?php echo WEB_PATH; ?>/templates/images/filter_close.png" style="" class="filterimage" alt=""></img>-->
					 </span>
				</li>
				</ul>
			  </div>
								<div style="clear:both"></div>
								<div class="newFilterControls location_Category" style="display:none;">
									<h5 class="headerVisible">Location Category :</h5>
                                    <ul class="chips">

										
									<li class="chip"><span></span><div class="div_loca_cat_close" id="loca_cat_close"></div></li>									
										
									</ul>
								</div>
							</div>
							<div style="clear:both"></div>
							<div class="filterd_location">
							<?php	
							
							//echo WEB_PATH.'/process_mobile.php?btnGetSearchDealLocations=yes&dismile='.$dismile.'&mlatitude='.$mlatitude.'&mlongitude='.$mlongitude.'&category_id=0'.'&customer_id='.$cust_s;
							
							$scroll=100;
							/*echo "<pre>";
							print_r($records_array1_loc);
							echo "</pre>";
							exit;*/
							//echo $total_records1_loc;
							if($total_records1_loc>0)
							{
								foreach($records_array1_loc as $Row_loc)
								{
								$campaig_category_string = "";
								$campaig_timelefttoexpire_string = "";
									$b_tag =  $Row_loc->business_tags;
									
										
								$t_l_estring= "";
								  $ex_array = explode(",",$Row_loc->limelefttoexpire);
								  for($i=0;$i<count($ex_array);$i++)
								  {
									  if($ex_array[$i] <= 24)
									  {
										$f_str = 1;
										}else
										{
											$f_str = 0;
										}
										$t_l_estring = $t_l_estring.$f_str.",";
										
								  }
								  $t_l_estring = trim($t_l_estring,",");
								/*** discount counting ***/
								  $dis_estring= "";
								  $ex_array = explode(",",$Row_loc->all_discount);
								  for($i=0;$i<count($ex_array);$i++)
								  {
										if($ex_array[$i] <= 20)
										{
											$f_str = 1;
										}
										else if($ex_array[$i] >= 21 && $ex_array[$i] <= 49)
										{
											$f_str = 2;
										}
										else if($ex_array[$i] >= 50 && $ex_array[$i] <= 69)
										{
											$f_str = 3;
										}
										else if($ex_array[$i] >= 70 && $ex_array[$i] <= 100)
										{
											$f_str = 4;
										}
										$dis_estring = $dis_estring.$f_str.",";
										
								  }
								  $dis_estring = trim($dis_estring,",");
								 // if(
								  //$discount = ;
								  $distance = $objJSON->distance($mlatitude, $mlongitude, $Row_loc->latitude, $Row_loc->longitude, "M");
								?>	
									<?php
									if($scroll==100)
									{
									?>
									<div style="display:none" is_new="<?php echo  $Row_loc->all_new_campaigns; ?>" levels="<?php echo $Row_loc->all_campaign_level ?>" d_range="<?php echo $dis_estring; ?>"  p_range="<?php echo $Row_loc->pricerange; ?>" o_c_status="<?php echo $Row_loc->is_open ; ?>"  v="<?php echo $Row_loc->limelefttoexpire; ?>" t_l_e="<?php echo $t_l_estring; ?>" class="location_tool" avg_rating="<?php echo $Row_loc->avarage_rating;?>" miles="<?php echo $Row_loc->distance; ?>" categories="<?php echo $Row_loc->all_categories; ?>" locid="<?php echo $Row_loc->location_id ?>" scroll="<?php echo $scroll;?>" style="border-radius:5px 5px 5px 5px;box-shadow:0 0 10px rgba(0,0,0,0.35);opacity:1;">
									<?php
									//echo $Row_loc->all_discount."==".$dis_estring;
									}
									else
									{
									
									?>
									<div style="display:none" is_new="<?php echo  $Row_loc->all_new_campaigns; ?>" levels="<?php echo $Row_loc->all_campaign_level ?>"  d_range="<?php echo $dis_estring; ?>"  p_range="<?php echo $Row_loc->pricerange; ?>" o_c_status="<?php echo $Row_loc->is_open ; ?>" v="<?php echo $Row_loc->limelefttoexpire; ?>" t_l_e="<?php echo $t_l_estring;  ?>"  class="location_tool" avg_rating="<?php echo $Row_loc->avarage_rating;?>" miles="<?php echo $Row_loc->distance; ?>" categories="<?php echo $Row_loc->all_categories; ?>"  locid="<?php echo $Row_loc->location_id ?>" scroll="<?php echo $scroll;?>">
									<?php 
									//echo $Row_loc->all_discount."==".$dis_estring;
									}
									$arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='.$Row_loc->location_id);
									if(trim($arr[0]) == "")
									 {
											 unset($arr[0]);
											 $arr = array_values($arr);
									 }
									$json = json_decode($arr[0]);
									$busines_name  = $json->bus_name;
						
									?>
									<input type="hidden" value="<?php echo $b_tag; ?>" class="business_tag"  />
									<input type="hidden" class="merchant_name" value="<?php echo $busines_name; ?>" />
										<div class="loca_image" style="height:0px;">
											<?php 
												if($Row_loc->picture!="")
												{
													$loc_image = ASSETS_IMG."/m/location/mythumb/".$Row_loc->picture; 		
											?>	
												<img width="100" height="100" src="<?=$loc_image?>" border="0" />
											<?php
												}
												else
												{
											?>											
												<img width="100" height="100" src="<?=ASSETS_IMG?>/c/Merchant_Location.png" border="0" />
											<?php
												}
											?>											
										</div>
										<div class="loca_name_rating">
											<div class="loca_name">
												<div class="loca_mile">
													<?php echo $Row_loc->distance." Mi" ?>
												</div>
												<a href="<?php echo $Row_loc->location_permalink; ?>" ><?php echo $Row_loc->location_name ?></a>
											</div>
											
										<div class="loc_center">
												
											<div class='rating'>
												<?php
													echo $objJSON->get_location_rating($Row_loc->location_id);
												?>
											</div>
											
										<?php
											if($Row_loc->pricerange!="")
											{
												if($Row_loc->pricerange==1 )
												{
													$val_text="<div class='loca_pricerange' style='display:block;' title='Inexpensive'>$</div>";
												}
												else if($Row_loc->pricerange==2 )
												{		
													$val_text="<div class='loca_pricerange' style='display:block;' title='Moderate'>$$</div>";
												}
												else if($Row_loc->pricerange==3 )
												{
													$val_text="<div class='loca_pricerange' style='display:block;' title='Expensive'>$$$</div>";
												}
												else if($Row_loc->pricerange==4 )
												{
													$val_text="<div class='loca_pricerange' style='display:block;' title='Very Expensive'>$$$$</div>";
												}
											}
											echo $val_text;
										?>
										
											<?php
											if($Row_loc->currently_open=="Currently Close")	
											{
											?>
												<div class='loca_close'>
													<?php
														echo $Row_loc->currently_open;
													?>
												</div>

											<?php
											}
											else
											{
											?>
												<div class='loca_open'>
													<?php
														echo $Row_loc->currently_open;
													?>
												</div>
											<?php
											}
											?>
											</div>
											<div class="loca_categories">
											<?php
											//echo count($Row_loc->location_categories);	
											if(count($Row_loc->location_categories)>0)
											{
												foreach($Row_loc->location_categories as $Row_loc_cat)
												{
													echo "<span>".$Row_loc_cat->cat_name."</span>";				
												}
											}
											?>
											</div>
											<div class="location_marker_iconn"><img src="<?php echo ASSETS_IMG; ?>/m/marker_rounded_grey.png"></div>
											<!--<div class="location_marker_iconn">&#xf041</div>-->
											<div class="loca_address">
											<?php
												$location_str="";
												if($Row_loc->address!="")
													$location_str .= $Row_loc->address;
												if($Row_loc->city!="")
													$location_str .= ",".$Row_loc->city;
												if($Row_loc->state!="")
												//	$location_str .= ",".$Row_loc->state;
												if($Row_loc->zip!="")
													//$location_str .= ",".$Row_loc->zip;	
												if($Row_loc->country!="")
													$location_str .= ",".$Row_loc->country;
												//echo "<br/>".$address ."<br />";
												//$address = "<table><tr><td width='30%' style='vertical-align:top;'>Address : </td><td>".$row->address.",".$Row_loc->city.",<br/> ".$Row_loc->state.",".$Row_loc->zip.",".$RSLocation->fields['country']."<br/><td></tr></table>";
												//echo $address;
												//echo $Row_loc->website."<br />";
												if($Row_loc->phone_number != "")
												{
													$phno = explode("-",$Row_loc->phone_number);
													$newphno = "(".$phno[1].") ".$phno[2]."-".$phno[3];
													$location_str .= "<br/>".$newphno;
												}
												echo $location_str;
											?>
											</div>
										</div>
										
										<?php 
										if(!isset($_SESSION['customer_id']))
										{
									
							//	$MARKER_HTML .= '<a href="'.WEB_PATH.'/register.php?btnRegisterStore=1&location_id='.base64_encode($storeid).'" target="_parent">Subscribe to Store</a>&nbsp;&nbsp;';							
							}							
							else							
							{
							
                                                            $RS_user_subscribe = "select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$Row_loc->location_id." and subscribed_status=1";
                                                            $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);
                                                           if($check_subscribe->RecordCount()==0)
                                                            {
															?>
															<div class="save_subscribe">
																<a href='javascript:void(0)' class='subscribestore' s_lid1='<?php echo $Row_loc->location_id; ?>' s_lid='<?php echo base64_encode($Row_loc->location_id); ?>'>Subscribe</a><img src="<?php echo ASSETS_IMG ?>/c/24.GIF" class="sub_loader" />
															</div>
								
                                                            <!--    $MARKER_HTML .= "<a href='javascript:void(0)' class='subscribestore' s_lid1='".$storeid."' s_lid='".base64_encode($storeid)."'>Subscribe to Store</a>&nbsp;&nbsp;"; -->
                                                            <?php
															}
                                                            else
                                                            {
															?>
															<div class="save_subscribe">
																<a href='javascript:void(0)' class='unsubscribestore' s_lid1='<?php echo $Row_loc->location_id; ?>' s_lid='<?php echo base64_encode($Row_loc->location_id); ?>'>Unsubscribe</a><img src="<?php echo ASSETS_IMG ?>/c/24.GIF" class="sub_loader" />
															</div>
								
														<!--//$MARKER_HTML .= "<a href='".WEB_PATH."/process.php?btnunsubscribelocation=1&location_id=".base64_encode($storeid)."' target='_parent'>Unsubscribe to Store</a>&nbsp;&nbsp;";
                                                            //    $MARKER_HTML .= "<a href='javascript:void(0)' class='unsubscribestore' s_lid1='".$storeid."' s_lid='".base64_encode($storeid)."'>Unsubscribe to Store</a>&nbsp;&nbsp;"; -->
                                                            <?php
															}
							}	
										?>
										
										<div class="loca_mile_offers">
										<span class="call_location_details">Location Details</span>
										<div class="loca_total_offers"  data-title="course">
												<?php
												//echo count($Row_loc->campaigns);	
												if(count($Row_loc->campaigns)>0)
												{
													echo "<span>".count($Row_loc->campaigns)." Offers</span>";
												}
												?>
												<img style="display:none" src="<?php echo ASSETS_IMG ?>/c/24.GIF" class="offer_loader">
											</div>
											<img  data-title="camp" src="<?php echo ASSETS_IMG; ?>/c/reload16X16.png" class="flip_map_loc" style="display:none"/>
										</div>
									</div> <!-- end of div location_tool -->
								<?php	
									
									$scroll=$scroll+170;
								}
							} // if(total_records1_loc) bracket
							?>
							
						</div><!-- end div filterd_location -->
						</div><!-- end div location -->
					</div><!-- end div wrapper -->		
						<div class="temp_location" style="display:none">
						
						</div>
				
				</div><!-- end div leftside -->

                   
      <div class="rightdiv">
		<div class="rightdiv_categorylist">
					<?php 
					
					if(!isset($_COOKIE['cat_remember']))
					{
						$categoryid = 0;
					}
					else
					{
						$categoryid = $_COOKIE['cat_remember'];
						$curr_fltr_category="All Categories";
					}
							  
					$cat_output="";
					$curr= "";
					if(!isset($_COOKIE['cat_remember']) ||  $_COOKIE['cat_remember']==0)
					{
						$curr="current";
					}
					
					
					$cat_output='<div class="list_carousel" >
					<ul id="category_slider" style="width:100%;">
							<li style="list-style:none" class="btnfilterstaticscampaigns '.$curr.'" mycatid="0">';
				if($curr=="current")
				{	
					$cat_output.='<img main_image="'.ASSETS_IMG.'/c/categories/icon-all.jpg" active_image="'.ASSETS_IMG.'/c/categories/icon-all-hover.png" src="'.ASSETS_IMG.'/c/categories/icon-all-hover.png" alt="All Categories" width="75" height="79" >';
				}
				else
				{
					$cat_output.='<img main_image="'.ASSETS_IMG.'/c/categories/icon-all.jpg" active_image="'.ASSETS_IMG.'/c/categories/icon-all-hover.png" src="'.ASSETS_IMG.'/c/categories/icon-all.jpg" alt="All Categories" width="75" height="79" >';
				}
				$cat_output.='<span style="display:none;">All Categories</span>';
				$cat_output.='</li>';
								
						$arr=file(WEB_PATH.'/process.php?btnGetAllCategories=yes');
						if(trim($arr[0]) == "")
						{
							unset($arr[0]);
							$arr = array_values($arr);
						}
						$json = json_decode($arr[0]);
						$total_records= $json->total_records;
						$records_array = $json->records;

						if($total_records>0)
						{					
							foreach($records_array as $Row)
							{
								$cat_name = (string) $Row->cat_name;
								$cat_image = (string) $Row->cat_scroller_image;
								$cat_hover_image = (string) $Row->cat_scroller_image_active;
								$curr= "";
								if($_COOKIE['cat_remember']==$Row->id)
								{
									$curr="current";
								}
								if($curr=="current")
								{		
									$cat_output.='<li style="list-style:none" class="btnfilterstaticscampaigns '.$curr.'" mycatid="'.$Row->id.'"><img main_image="'.ASSETS_IMG.$cat_image.'" active_image="'.ASSETS_IMG.$cat_hover_image.'" src="'.ASSETS_IMG.$cat_hover_image.'"  alt="'.$cat_name.'" width="75" height="79"><span style="display:none;">'.$cat_name.'</span></li>';	
								}
								else
								{
									$cat_output.='<li style="list-style:none" class="btnfilterstaticscampaigns '.$curr.'" mycatid="'.$Row->id.'"><img main_image="'.ASSETS_IMG.$cat_image.'" active_image="'.ASSETS_IMG.$cat_hover_image.'" src="'.ASSETS_IMG.$cat_image.'"  alt="'.$cat_name.'" width="75" height="79"><span style="display:none;">'.$cat_name.'</span></li>';	
								}
							}
						}		
					
					$cat_output.='</ul>
						
                                                        <a id="prev2" class="prev" href="#"></a>
                                                        <a id="next2" class="next" href="#"></a>
					
					
					</div>';
					echo $cat_output;
					?>
				</div><!-- rightdiv_categorylist -->
				<!--<img id="image1" width="300" src="http://www.scanflip.com/merchant/images/location/store_1381341410.png" style="width: 0px; display: block; height: 190px; margin-left: 150px; opacity: 0.5;">
<img id="image2" width="300" src="http://www.scanflip.com/merchant/images/location/location_1391705042.png" style="width: 300px; height: 190px; margin-left: 0px; opacity: 1; display: block;">				  -->
 <div class="slider-viewport">
    <div class="slider-carriage">
      <?php 
			if(isset($_COOKIE['view']))
			{
				if($_COOKIE['view']=="gridview")
				{
				?>
				<div id="camp" class="show"  >
				<div id="">
				<div id="map_categories" style="display:none;">
			
				  <div id="map_canvas" ></div>
				</div>
				</div>
				</div>
				<?php
				}
				else
				{
				?>	
				<div id="camp" class="show"  >
				<div id="">
				<div id="map_categories">
					
					  <div id="map_canvas" ></div>
					</div>
				</div>
				</div>
				<?php
				}
			}
			else
			{
			?>	
			<div id="camp" class="show">
			<div id="">
			<div id="map_categories">
			
			  <div id="map_canvas" ></div>
			  
			</div>
			</div>
			</div>
			<?php
			}
		?>
	<!-- <img id="image789"  src="http://www.scanflip.com/merchant/images/location/location_1391705042.png" "> -->
          <?php 
			//echo $total_records1;
			
		  if($total_records1 >0) 
		  { 
		  ?>
		    <div id="course" class="show" >
		  <div id="imagek" style="display:block;" >
          <div class="dealslistmain" >
          <div id="dealslist" >
				
			  <?php 
			if(isset($_COOKIE['view']))
			{
				if($_COOKIE['view']=="gridview")
				{
				?>	
				<div class="info" style="display:none;"> 
				<p>!</p>
				<div class="info_text_div">
					<span><?php echo $client_msg['search_deal']['label_Grid_Marker']?></span>
				</div>
					<!--
					<div class="info_d_image">
						<img src="images/pin-small.png" style="float:right;" />
					</div>
					-->
				</div>
				<?php
				}
				else
				{
				?>	
				<div class="info" style="display:none;"> 
				<p>!</p>
				<div class="info_text_div">
					<span><?php echo $client_msg['search_deal']['label_Grid_Marker']?></span>
				</div>
					<!--
					<div class="info_d_image">
						<img src="images/pin-small.png" style="float:right;" />
					</div>
					-->
				</div>
				<?php
				}
			}
			else
			{
			?>	
			<div class="info" style="display:none;"> 
				<p>!</p>
				<div class="info_text_div">
					<span><?php echo $client_msg['search_deal']['label_Grid_Marker']?></span>
				</div>
					<!--
					<div class="info_d_image">
						<img src="images/pin-small.png" style="float:right;" />
					</div>
					-->
				</div>
			<?php
			}
		?>
			  
    <div class="div_msg" ></div>
    <div class="deallistwrapper displayul" mylistsize="<?php echo $total_records1 ?>" style="display:none" >
    
    </div>
<div class="deallistwrapper displayul_all" mylistsize="<?php echo $total_records1 ?>" >
<!--    <div class="div_msg" ></div>-->
    <!-- start of T_3 -->
    
   
		 <?php 
    if(isset($_REQUEST['order_by_cat']))
                {		
                echo '<a onclick="remove_filter()"  style="cursor:pointer"><div class="showalld" >Show All Deals<br></div></a><hr>';
                echo $Cat_DetailHtml;
                	if($Cat_DetailHtml == "")
                	{ 
                    		echo $client_msg['common']['label_Currently_No_Deals'];
                	}
                
		}
		else 
		{
                                        
                                        if($DetailHtml == "")
                                        {
                                            echo $client_msg['common']['label_Currently_No_Deals'];
                                        }
                                        else
                                        {
						
                                                echo $DetailHtml;
						/*
                                                echo $hiddendata;
                                                */
                                        }
		}
           ?>
			
   

    <!-- end of T_3 --> 
  </div>
<div class="deallistwrapper mainul" style="display:none"></div>
<div class="deallistwrapper navigationul" style="display:none"></div>

<div class="searchdeal_offers" >

</div>
<div class="searchdeal_offers_copy" style="display:none" >
</div>
				</div><!-- end of dealslist --->
				</div><!-- end of dealslistmian --->
			</div><!-- end of imagek --->
		</div><!-- end of course --->

	
		</div>  <!-- slider-carriage -->
  </div><!-- slider-viewport -->
  
  <div id="ajax_loader" style="float: left;margin-left: 500px;padding-top: 50px;display:none">
  	<img src="<?php echo ASSETS_IMG ?>/c/ajax-loader.gif">
  </div>
      <div style="clear:both;"></div>

    <?php 
	}
	else 
	{
	

              ?>
			  <style>
				#map_canvas{height:auto !important;}
			</style>
<div id="imagek" >
<div id="dealslist" >
  <div class="deallistwrapper" style=""> 
		<?php
		/*
		if(isset($_COOKIE['searched_location']))
			echo "bbb".$_COOKIE['searched_location']."ccc";
		else
			echo "not set";
			*/
		if(isset($_COOKIE['curr_address']))
		{
		?>
		<div class="warning">
		
			<p style="margin-top:4px;">!</p>
		   <span class="div_msg div_msg_err" >
				<!--Sorry currently you do not have any saved / reserved offers . Please browse Scanflip to find offers near you.-->
                                <?php echo $client_msg['my_deal']['label_Currently_Not_Save_Offers'];?>
			</span>
	  </div>
	 <?php
	  }
	  else if(isset($_COOKIE['searched_location']))
	  {
	  ?>
	  <div class="warning" >
	  
			<p style="margin-top:4px;">!</p>
		   <span class="div_msg div_msg_err" >
				<!--Sorry currently you do not have any saved / reserved offers. Please browse Scanflip to find offers near you.-->
                                <?php echo $client_msg['my_deal']['label_Currently_Not_Save_Offers'];?>
			</span>
	  </div>
	  <?php
	  }
	  ?>
      <!--<div class="div_msg div_msg_err">Sorry Currently there are no offers available at your search area.</div>-->


</div>
<script>
jQuery(document).ready(function(){

	
		
});
</script>
     </div> 
</div>
     </div> 
	 </div> 
          <?php } ?>

		     
	
	</div>	  <!-- end div rightside -->   

	
	 </div><!-- end div mainside -->
	 
	<?php  
	   } 
            else 
		{
                ?>
			<div id="content" class="cantent">
		<div class="my_main_div">
			<div id="contentContainer" class="contentContainer">
			
            
   		<div class="mainside" style="height:400px">&nbsp;</div><!-- end div mainside -->
            <?php
		}
             ?>
<div class="recently_view_div">
<div class="recently_view">
Recently Viewed :
<hr/>
<div class="recently_view_camp_list">
<?php 
if(isset($_COOKIE['recently_viewed_campaigns_list1']))
{
if($_COOKIE['recently_viewed_campaigns_list1'] != "")
{
	$campaignlist_array = unserialize($_COOKIE['recently_viewed_campaigns_list1']);

//print_r($campaignlist_array);
$campaign_string = "";
$location_string = "";
for($i=0;$i<count($campaignlist_array);$i++)
{
	$c_l = explode("-",$campaignlist_array[$i]);
	$campaign_string .= $c_l[0]."-";
	$location_string .= $c_l[1]."-";
}
$campaign_string = trim($campaign_string,"-");
$location_string = trim($location_string,"-");
$arr_rec=file(WEB_PATH.'/process.php?get_recently_viewed_campaign=yes&camapigns='.$campaign_string .'&locations='.$location_string);
if(trim($arr_loc[0]) == "")
{
	unset($arr_rec[0]);
	$arr_rec = array_values($arr_rec);
}
			$all_json_str_rec = $arr_rec[0];
		   // echo $all_json_str;
			//exit;
$json_rec = json_decode($arr_rec[0]);
$total_records1_rec = $json_rec->total_records;
$records_array1_rec = $json_rec->records;
echo "<ul>";
							
foreach($records_array1_rec as $Row)
{	
	 
	if($Row->business_logo!="")
	{
		$img_src=ASSETS_IMG."/m/campaign/block/".$Row->business_logo; 
	}
	else
	{
		$img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
	}
	$camp_title = $Row->title;
	$redirect_url = $Row->permalink;
?>

<li>
<a href="<?php echo $redirect_url; ?>">
<div class="deal_blk" >
	<div class="offersexyellow">
		<div class="grid_img_div">
		<img width="240" height="200" src="<?php echo $img_src; ?>">
		</div>
		<div title="Campaign Title" class="dealtitle" >
			<?php echo $camp_title; ?>
		</div>
	</div>
</div>
</a>
</li>
<?php 
}
echo "</ul>";
}
else
{
?>
<div class="div_msg_recently">
        <div class="warning">
		<p style="padding-bottom:0px;">!</p>
		<span class="div_msg_err_recently" style="display: block;margin-top: 7px;text-align: left;">
		After viewing campaign detail pages, look here to find an easy way to navigate back to pages you are interested in.
		</span>
		</div>
</div>
<?php
}
}
else
{
?>
<div class="div_msg_recently">
        <div class="warning">
		<p style="padding-bottom:0px;margin-top:-2px;">!</p>
		<span class="div_msg_err_recently" style="display: block;">
		After viewing campaign detail pages, look here to find an easy way to navigate back to pages you are interested in.
		</span>
		</div>
</div>
<?php
} 
?>
	</div><!-- end div recently_view_camp_list -->
				</div><!-- end div recently_view -->
			</div><!-- end div recently_view_div -->

		
		
</div><!-- end div contentContainer -->	


 
 
<?php require_once(CUST_LAYOUT."/before-footer.php");?>
 
</div><!-- end div my_main_div -->

</div><!-- end div content -->

	<?
		require_once(CUST_LAYOUT."/footer.php");
		?>	
	


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
<div style="display:none"id="confirmation"> 
        <div align="center" style="margin-top:21px;height:40px;text-align:center;text-transform:capitalize;padding:10px;font-size:15px;">
            Are You Sure You Want To Unreserve This Offer ?
        </div>
        <div align="center" style="clear:both">
              <input type="submit"  value="Unreserve" id="btnconfirmunreserve" name="btnconfirmunreserve" style="margin-left: 7px; margin-right: 10px;">
              <input type="submit"  value="Cancel" id="btnconfirmcamcel" name="btnconfirmcamcel" style="margin-left: 7px;  margin-right: 10px;">
        </div>
        
        <div>
            
        </div>
    </div>
	<div style="">
	<!--<img src="<?php echo ASSETS_IMG; ?>/c/reload16X16.png" class="flip_map" style="display:none"/>-->
	<img src="<?php echo ASSETS_IMG; ?>/c/reload16X16.png" class="flipmap3" style="display:none"/>
	
	</div>
	<input type="hidden" name="hdn_price" id="hdn_price" value="" />
	<input type="hidden" name="hdn_price" id="hdn_discount" value="" />
	<input type="hidden" name="hdn_is_expiring_today" id="hdn_is_expiring_today" value ="0" />
	<input type="hidden" name="hdn_is_new_campaign" id="hdn_is_new_campaign" value ="0" />
	<input type="hidden" name="hdn_is_opennow" id="hdn_is_opennow" value ="0" />
	<input type="hidden" name="hdn_is_offer_div_click" id="hdn_is_offer_div_click" value="1" />
	<input type="hidden" name="hdn_istouched" id="hdn_istouched" value="0" />
</body>
</html>





<script>


 function open_popup1(popup_name)
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
function close_popup(popup_name)
{

	$("#" + popup_name + "FrontDivProcessing").fadeOut(100, function () {
	$("#" + popup_name + "BackDiv").fadeOut(100, function () {
		 $("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				$("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
 function open_popup(popup_name)
{
    
	
	$("#" + popup_name + "FrontDivProcessing").fadeIn(100, function () {
		$("#" + popup_name + "BackDiv").fadeIn(100, function () {
			 $("#" + popup_name + "PopUpContainer").fadeIn(100, function () {         
	
			 });
		});
	});
	
	
}
///bind_hover();

    if(document.cookie.indexOf("cat_remember") >=0)
                        {
                            selected_cat_id=getCookie("cat_remember");
                          
                        }
                        else {
                                selected_cat_id = 0;
                                setCookie("cat_remember",selected_cat_id,365);
                            }
                            
                            if(document.cookie.indexOf("miles_cookie")  >=0)
                        {
                            miles_cookie=getCookie("miles_cookie");
                             if(miles_cookie == "")
                            {
                                miles_cookie = 50;
                                  setCookie("miles_cookie",miles_cookie,365);
                            }
                        }
                        else {
                             miles_cookie = 50;
                            setCookie("miles_cookie",miles_cookie,365);
                               
                            }
   var WEB_PATH = "<?=WEB_PATH?>";
	function inputKeyUp(e)
        {
            if(e.keyCode==13)
            {
                search_deal();
            }
        }
	 function search_deal(){
		
	 //alert("search deal");
         var zip = '';
		var catid = jQuery("#slider_iframe").contents().find(".current").attr("id");
                  catid= 0;
                if(jQuery("#slider_iframe").contents().find(".current").length == 0)
                 {
                        //catid= 0;
                 }
                 else
                     {
                      //   catid =jQuery("#slider_iframe").contents().find(".current").attr("id");
                     }
		     
              /*
		var zip="<?php echo  $_COOKIE['searched_location'];?>";
                if(zip=="")
                {
                    zip = document.getElementById('zip').value;
                    alert("blank");
                }
                else
                {
                    alert("cookie");
                }
                //alert(zip);
                */
               //alert("call search deal");
               var oldzip=getCookie("searched_location");
			   
                zip =  document.getElementById('zip').value;
		setCookie("searched_location",zip,365);
		var newzip=getCookie("searched_location");
		document.getElementById('hdn_serach_value').value = zip;
		if(zip == "Current Location"){
			
		//	var latitude = document.getElementById("my_latitude").value;
		//	var longitude = document.getElementById("my_longitude").value;
			//document.getElementById("slider_iframe").src = WEB_PATH+"/search-deal-map.php?latitude="+latitude+"&longitude="+longitude+"&category_id="+catid;
                          window.location.href=  WEB_PATH+"/my-deals.php";
			return false;
		}
                
		
		var mymiles = document.getElementById("hdnmiles").value;
		var zoominglevel="<?php if(isset($_COOKIE['zoominglevel']))echo $_COOKIE['zoominglevel'];?>";
		
		if(zoominglevel == "")
		{
		    zoominglevel="10";
		}
		if(oldzip!=newzip)
		{
			setCookie("myck",'true',365);
		}
		else
		{
			setCookie("myck",'false',365);
		}

		if(zip=="Enter Your Zipcode Here")
		{
			
		}
		else
		{
			
		   // document.getElementById("slider_iframe").src = "<?=WEB_PATH?>/search-deal-map.php?zip="+zip+"&category_id="+catid+"&miles="+mymiles+"&zoominglevel="+zoominglevel;
			 window.location.href=  WEB_PATH+"/my-deals.php";
			 
		}
		
		// end to solve two times load problem
		
		// start logic show last 3 searched location 
		
			var recently_searched=getCookie("recently_searched");
			//alert(recently_searched);
			  if (recently_searched!=null && recently_searched!="")
			  {
			  //alert("set");
				var $ar_se= new Array();
				$ar_se=recently_searched;
				$ar_se=jQuery.parseJSON($ar_se);
				//alert($ar_se);
				//alert($ar_se.length);
				$ar_se.push(zip);
				//alert($ar_se.length);
				$ar_se=JSON.stringify($ar_se);
				setCookie("recently_searched",$ar_se,365);
			  }
			else
			  {
			  //alert("not set");
				var $a = new Array();
				$a.push(zip);
				$ar_se= JSON.stringify($a);
				setCookie("recently_searched",$ar_se,365);
			  }

			
		// end logic show last 3 searched location 
		
	}
	
	function getCookie(c_name)
	{
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++)
	  {
	  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
	  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
	  x=x.replace(/^\s+|\s+$/g,"");
	  if (x==c_name)
	    {
	    return unescape(y);
	    }
	  }
	}
	
	function setCookie(c_name,value,exdays)
	{
     var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
	}
	
	function checkCookie()
	{
      //   alert("=="+getCookie('searched_location')+"==")   ;
            if(typeof getCookie('searched_location') == "undefined" || getCookie('searched_location') == "" || getCookie('searched_location') == null ){
               medaitor(jQuery("#is_geo_location_supported").val());
               return false;
            }
         check_geolocation_support();
            var catid=getCookie("cat_remember");
		
		if(catid == 0)
		{
		    catid= 0;
		}
		else
		{
		    
		        //catid = catid; 
		}
                  
		var val = "<?php if(isset($_SESSION['customer_id']))echo $_SESSION['customer_id'] ?>";
               var zip = getCookie('searched_location');
               // zip = zip.replace(" ","");
                 zip = zip.replace(/\+/g," ");
                //  zip = zip.replace(" ","");
               
		//var mymiles = document.getElementById("hdnmiles").value;
		var zoominglevel="<?php if(isset($_COOKIE['zoominglevel']))echo $_COOKIE['zoominglevel'];?>";
		
		if(zoominglevel == "")
		{
		     zoominglevel="10";
		     
		}
               
                if(val != "")
		{
			
			//var value=getCookie("searched_location");
			var value=zip;
                        setCookie("searched_location",zip,365);
			if(value==null || value=="")
			{
				
				//document.getElementById('zip').value = "Enter Your Zipcode Here";
				//document.getElementById('hdn_serach_value').value = "Enter Your Zipcode Here";	
			}else{
			
				document.getElementById('zip').value = value;
				document.getElementById('hdn_serach_value').value = value;
			}
		}
		else{
			
			//var value=getCookie("searched_location");
                        var value=zip;
			//alert("onload "+value);
                        setCookie("searched_location",zip,365);
			if (value!=null && value!="")
			{
			
				document.getElementById('zip').value = value;
				document.getElementById('hdn_serach_value').value = value;
			}
			else
			{
			
				document.getElementById('zip').value = "Enter Your Zipcode Here";
				document.getElementById('hdn_serach_value').value = "Enter Your Zipcode Here";
			}
		}
	}
jQuery(document).ready(function() {
	//alert("ready 2400");							
//alert(jQuery("#shareit-field").val());	
jQuery('span[rel=shareit]').click(function(event) {
					event.stopPropagation(); event.preventDefault();		
$= jQuery.noConflict();
$("#price-box").attr("disp","0");
$("#price-box").hide();
$("#filterpriceidarrow").attr("disp1","0");
$("#filterpriceidarrow").hide();
$("#discount-box").attr("disp","0");
$("#discount-box").hide();
$("#filterdiscountidarrow").attr("disp1","0");
$("#filterdiscountidarrow").hide();
		//display the box
                if($("#shareit-box").attr("disp")==0)
                {
                        $("#shareit-box").attr("disp","1");
                        $("#shareit-box").show();
						/*if($("#price-box").attr("disp")==1)
						{
							jQuery('a[rel=price]').trigger("click");
						}*/
                }
                else
                {
                        $("#shareit-box").attr("disp","0");
                        $("#shareit-box").hide();
                }
                if($("#filterdistanceidarrow").attr("disp1")==0)
                {
                        $("#filterdistanceidarrow").attr("disp1","1");
                        $("#filterdistanceidarrow").show();
                }
                else
                {
                        $("#filterdistanceidarrow").attr("disp1","0");
                        $("#filterdistanceidarrow").hide();
                }
             
	});
        
        jQuery('span[rel=price]').click(function(event) {
					event.stopPropagation(); event.preventDefault();
		
$= jQuery.noConflict();
$("#shareit-box").attr("disp","0");
$("#shareit-box").hide();
$("#filterdistanceidarrow").attr("disp1","0");
$("#filterdistanceidarrow").hide();
$("#discount-box").attr("disp","0");
$("#discount-box").hide();
$("#filterdiscountidarrow").attr("disp1","0");
$("#filterdiscountidarrow").hide();
		//display the box
                if($("#price-box").attr("disp")==0)
                {
                        $("#price-box").attr("disp","1");
                        $("#price-box").show();
						//10-9-2013
					/*	if($("#shareit-box").attr("disp")==1)
						{
							jQuery('a[rel=shareit]').trigger("click");
						}
						*/
                }
                else
                {
                        $("#price-box").attr("disp","0");
                        $("#price-box").hide();
                }
                if($("#filterpriceidarrow").attr("disp1")==0)
                {
                        $("#filterpriceidarrow").attr("disp1","1");
                        $("#filterpriceidarrow").show();
                }
                else
                {
                        $("#filterpriceidarrow").attr("disp1","0");
                        $("#filterpriceidarrow").hide();
                }
             
	});
	
	jQuery('span[rel=discount]').click(function(event) {
	
					event.stopPropagation(); event.preventDefault();		
$= jQuery.noConflict();
$("#shareit-box").attr("disp","0");
$("#shareit-box").hide();
$("#price-box").attr("disp","0");
$("#price-box").hide();
$("#filterpriceidarrow").attr("disp1","0");
$("#filterpriceidarrow").hide();
$("#filterdistanceidarrow").attr("disp1","0");
$("#filterdistanceidarrow").hide();
		//display the box
		         if($("#discount-box").attr("disp")==0)
                {
                        $("#discount-box").attr("disp","1");
						//alert($("#discount-box").attr("disp"));
                        $("#discount-box").show();
						/*if($("#price-box").attr("disp")==1)
						{
							jQuery('a[rel=price]').trigger("click");
						}*/
                }
                else
                {
				          $("#discount-box").attr("disp","0");
                        $("#discount-box").hide();
                }
                if($("#filterdiscountidarrow").attr("disp1")==0)
                {
                        $("#filterdiscountidarrow").attr("disp1","1");
                        $("#filterdiscountidarrow").show();
                }
                else
                {
                        $("#filterdiscountidarrow").attr("disp1","0");
                        $("#filterdiscountidarrow").hide();
                }
				 
             
	});
	//grab all the anchor tag with rel set to shareit
	$('#shareit-box').click(function(event) {
					event.stopPropagation(); event.preventDefault();

		//display the box
                if($("#shareit-box").attr("disp")==0)
                {
                        $("#shareit-box").attr("disp","1");
                        $("#shareit-box").show();
                }
                else
                {
                    //    $("#shareit-box").attr("disp","0");
                      //  $("#shareit-box").hide();
                }
                if($(".arrow-up").attr("disp1")==0)
                {
                        $(".arrow-up").attr("disp1","1");
                        $(".arrow-up").show();
                }
                else
                {
                      //  $(".arrow-up").attr("disp1","0");
                       // $(".arrow-up").hide();
                }
            
	});
	/*
	jQuery('#viewlink').toggle(
		function() {
			//alert("click 1");
			jQuery(".pop-display").css("display","block");
		},
		function(){
			//alert("click 2");
			jQuery(".pop-display").css("display","none");
		}
	);
	*/
	//10-9-2013
	jQuery('a[id=viewlink]').click(function(event){
event.stopPropagation();
			if(jQuery(".pop-display").css("display")=="none")
			{
				jQuery(".pop-display").css("display","block");

				if(jQuery("#shareit-box").attr("disp")==1)
				{
					jQuery('a[rel=shareit]').trigger("click");
				}
			}
			else
			{
				jQuery(".pop-display").css("display","none");
			}
	});
	
});

jQuery("#mapview").click(function(event){
event.stopPropagation();
	setCookie("view","mapview",365);							  							  
	jQuery("#map_categories").css('display','block');
	jQuery(".info").css("display","none");
	
	/*
	jQuery(".deallistwrapper").css('height','744px');
	jQuery(".deal_blk").css("margin","18px 9px 0px 10px");
	jQuery(".offersexyellow").css('height','228px');    
	jQuery(".mer_ikon_img").css('display','block');
	jQuery(".percetage").css('margin-top','0px').css("width","233px");
	jQuery(".grid_img_div").css('display','none');
	*/
	
	jQuery("#dealslist").removeClass("gridview");
	jQuery("#dealslist").addClass("mapview");
	
	jQuery(".rightdiv").removeClass("gridview");
	jQuery(".rightdiv").addClass("mapview");
    
        //alert(locations.length);
                
        /*
        maps = new google.maps.Map(document.getElementById('map_canvas'));
        maps.checkResize();
        */
       /*google.maps.event.trigger(document.getElementById('map_canvas'),"resize");*/
	   
	   //alert(map.getZoom());
           if(typeof getCookie('searched_location') == "undefined" || getCookie('searched_location') == "" || getCookie('searched_location') == null ){
             }
             else {
                
	///   map = new google.maps.Map(document.getElementById('map_canvas'));
	    if(map.getZoom()=="7")
	   {
              			// to slove grid view -> map view when no deal found
					var mlati = '<?=$mlatitude?>';
                                        var mlong = '<?=$mlongitude?>';
					var zoomlevel=7;
					checkCookie();
                                        if(mlong != "" && mlati != "")
                                            {
					var myOptions = {
						zoom:parseInt(zoomlevel),
						center: new google.maps.LatLng(mlati,mlong),
						mapTypeControl: true,
						mapTypeControlOptions: {
							style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
						},
						navigationControl: true,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);                                       
                                        
					google.maps.event.addListener(map, 'zoom_changed', function() {
						zoomLevel = map.getZoom();
						
						cat_id='<?php if(isset($_REQUEST['category_id'])){echo $_REQUEST['category_id'];}else{echo '0';}?>';
						//alert(cat_id);
						
						setCookie("zoominglevel"+cat_id,zoomLevel,365);
						
						
						//this is where you will do your icon height and width change.     
					});
					}
	   }
	   else if(jQuery(".displayul .deal_blk").length==1) // default grid->map and 1 location map marker problem
			   {
					//alert("before resize");
					google.maps.event.trigger(map, 'resize');
					
					//alert(jQuery(".displayul .deal_blk").attr("locid"));
					//alert(markerArray[71].position.lat());
					//alert(markerArray[71].position.lng());
					var l_lid=jQuery(".displayul .deal_blk").attr("locid");
					var l_lat=markerArray[l_lid].position.lat();
					var l_lng=markerArray[l_lid].position.lng();
					map.setCenter(new google.maps.LatLng(l_lat, l_lng));
					map.setZoom(10);
					//alert("after resize");
			   }
	   else
	   {
               
		   google.maps.event.trigger(map,"resize");
       }
       }
       selected_cat_id=getCookie("cat_remember");                                             
        miles_cookie=getCookie("miles_cookie");
				
        filter_deals_algorithm(selected_cat_id,miles_cookie);
        
});


jQuery("#gridview").click(function(event){
event.stopPropagation();
	setCookie("view","gridview",365);							  								   
	jQuery("#map_categories").css('display','none');
	jQuery(".info").css("display","none");
	
	/*
	jQuery(".deallistwrapper").css('height','1090px');
	jQuery(".deal_blk").css("margin","5px 9px 15px 10px");
	jQuery(".offersexyellow").css('height','346px');    
	jQuery(".mer_ikon_img").css('display','none');
	jQuery(".percetage").css('margin-top','15px').css("width","180px");
	jQuery(".grid_img_div").css('display','block');
	*/
	
	jQuery("#dealslist").removeClass("mapview");
	jQuery("#dealslist").addClass("gridview");

	jQuery(".rightdiv").removeClass("mapview");
	jQuery(".rightdiv").addClass("gridview");
	
});

/* start solve safari problem */

$(window).bind("pageshow", function(event) {
    if (event.originalEvent.persisted) {
        window.location.reload() 
    }
});

/* end solve safari problem */

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
?>
<div id="NotificationLoadingDataPopUpContainer" class="container_popup"  style="display: none;">
    <div id="NotificationLoadingDataBackDiv" class="divBack">
    </div>
    <div id="NotificationLoadingDataFrontDivProcessing" class="Processing" style="display:none;">

        <div id="NotificationLoadingDataMaindivLoading" align="center" valign="middle" class="textDivLoading"
             style="left: 45%;top: 40%;">

            <div id="NotificationLoadingDatamainContainer" class="loading innerContainer" style="height:auto;width:auto">
				Loading ...
            </div>
        </div>
    </div>
</div>

   <div id="NotificationloaderPopUpContainer" class="container_popup"  style="display: none;">
    <div id="NotificationloaderBackDiv" class="divBack">
    </div>
    <div id="NotificationloaderFrontDivProcessing" class="Processing" style="display:none;">

        <div id="NotificationloaderMaindivLoading" align="center" valign="middle" class="imgDivLoading"
             style="left: 45%;top: 40%;">

            <div id="NotificationloadermainContainer" class="loading innerContainer" style="height:auto;width:auto">
                <img src="<?php echo ASSETS_IMG; ?>/c/128.GIF" style="display: block;" id="image_loader_div"/>
            </div>
        </div>
    </div>
</div>
<div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
                                        
     <div id="myDivID_3">
       <div id="popupmainid" class="popupmainclass" style="padding:11px;">
     <div class="detailclass">
          <div class="detailclassleft">
                  <div class="bussinessclass">
                    <span style="color:#3C99F4">
                         KFC                    </span></div>
                    <div class="CampTitle">
                        Kfc-private-kfc-toronto-camp3                        
                    </div>
                    <div class="limitclass">
                        <div style="margin-top:5px;"><b>Limit : </b><span class="popup_limit">Earn Redemption Points On Every Visit</span></div>               
                    </div>
                     <div class="locationclass">
                        <div class="locationlabel"><b>Where to Redeem :</b><span class="popup_address"> F4-1000 Gerrard St E Toronto ON USA </span></div>
                    </div>
	 </div>
           <div class="detailclassright">
                    <div class="popupimageclass">
                       <img src="" class="popup_image">
                    </div>
          </div>
     </div>
  <div style="clear:both"></div>  
     <div style="height:50px;" class="dealstatusclass">
						<div class="title_redeem_share_popup">
							<div class="title_for_redeem_popup">
								<?php echo $client_msg['common']['label_Redeem_Earn'];?>
							</div>
							<div class="title_for_share_popup">
								<?php echo $client_msg['common']['label_Share_Earn'];?>
							</div>
						</div>
                        <div class="popupredeemrewards">
                            <p>12</p>
                            <span><?php echo $client_msg['common']['label_Scanflip_Point'];?></span>
                        </div>
                        
                        <div class="popupreferralrewards">
                            <p>12</p>
                            <span><?php echo $client_msg['common']['label_Scanflip_Point'];?></span>
                        </div>
                        <div class="popupoleft1">
                            <p>2</p>
                            <span><?php echo $client_msg['common']['label_Offer_Left'];?></span>
                        </div>
                        <!--<div class="printclass" style="display:none">
				<a href="" name="btn_print" id="printid" style="color:white !important">
				<?php echo $client_msg['common']['label_Print_Offeres'];?>
				</a>
			</div> -->
			
      </div>
    
      
      <div class="expirationclass">
          <div class="expirationlabel">
              <div style="float:left">
                  <b>Expiry Date: </b>
			<span class="popup_expiration">05/23/13 5:04 AM </span>             </div>
              <div class="errorclasspopup" style="color:red;font-size: 14px;margin-top: 4px;width: 100%;float:left;height: 15px;"></div> 
				<div class="share_loader" style="display:none;">
				
			  </div> 			  
              <div class="reserve" style="">
                  <img src="<?php echo ASSETS_IMG; ?>/c/save_deal.png" style="display:none;margin-right:25px;float:left;" id="saveofferid">
			<div style="display:none;" class="cust_attr_tooltip">
									<div class="arrow-down"></div>
									<?php echo $client_msg['common']['label_Offer_Reserved'];?>
			</div>
                  <input type="hidden" value="549=91" id="hdn_cl" name="hdn_cl">
                  <input type="hidden" value="" id="hdn_is_reserve" name="hdn_is_reserve">
                  <input type="hidden" value="" id="hdn_reserve_barcode" name="hdn_reserve_barcode">
                  <a href="javascript:void(0)" style="display:none;float:right;width:106px;text-align: left;margin-right:10px;padding:7px 5px 5px 15px;" id="ShowVoucherId">Show Voucher</a>
                  <a href="javascript:void(0)" style="display:none;float:right;width:102px;text-align: center;margin-right:10px;padding:7px 10px 5px 15px;" id="ShowshareId">Share It</a>
                  <input type="submit" lid="91" cid="549" value="Reserve" id="btnreserve" name="btnreserve" style="margin-left: 7px; display: block; float: right; margin-right: 10px;" class="btn_mymerchantreserve">
                                                  
		
              </div>
              <div style="clear:both"></div> 

               </div>
	  </div>
 
      <br>
      <?php $button_image1=ASSETS_IMG."/c/button-corne-white.png";
$button_image2=ASSETS_IMG."/c/button-corne-hover2.jpg";
?>
 
          <div align="center" class="showvocherdiv" >
	<img class="barcode" src="">
      </div>
      <div class="sharediv">
        <span class="popup_sharetext">
                            <?php echo $client_msg['common']['label_Share_Earn_Points'];?>
                    </span>
        <div class="popup_sharediv">
             <div >
                                        <a id="btn_msg_div" target="_parent" class="email_link"  href="javascript:void(0)" >Email</a>
                                 </div>
                                     
             <div>
                  			    <!--<a href="javascript: void(0)" class="popup_facebook_sharing"   target="_parent" onclick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=Kfc-private-kfc-toronto-camp3&amp;p[summary]=dgdf&amp;p[url]=http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D&amp;&amp;p[images][0]=http://www.scanflip.com/merchant/images/default_campaign.jpg', 'sharer', 'toolbar=0,status=0,width=548,height=325');" class="btn_share_facebook">
                                            Facebook
			    </a>
                                            -->
                                          <a class="btn_share_facebook loginshare" id="" data="" target="_parent"  href="javascript: void(0)">     
                                Facebook
                        </a>  
				
                            	     </div>
            
               <div>
				
				<!--				<a  href="http://www.scanflip.com/register.php?url=http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D" url="http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D" class="twitter-share-button" data-lang="en" data-count="none">Tweetr</a> -->
                                	<a  href="javascript:void(0)" onClick="" data-count="none"  data-lang="en" class="twitter_link" >Twitter</a>
                                        <input type="hidden" id="campaigntadid" name="campaigntadid"  value=""></input>                 
								 
				
			    <script type="text/javascript" charset="utf-8">
var customer_id="<?php if(isset($_SESSION['customer_id']))echo $_SESSION['customer_id'] ?>";

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
                            </div>
             
                   <div>
                                     <!--<a class="google_plus_link"  target="_blank" href="https://plus.google.com/share?url=http://www.scanflip.com/register.php?campaign_id=549&amp;l_id=91&amp;share=true&amp;customer_id=OTk=">-->
                                   <?php 
                                   $campaign_id=549;
                                   $l_id=91;
                                   $share="true";
                                 
                                   ?>
<?php //Bahman changed data-contentdeeplinkid="/scanflip/register" to data-contentdeeplinkid="/register" ?>
									<a href="javascript:void(0)" 
										class="g-interactivepost google_plus_link" 
										data-prefilltext="#Scanflip Offer -$16 for All-Inclusive Daytime Admission (a $30 Value).. now available at Best Westerns Plus Cairn Croft Hotel participating locations.Limited Offers.. Reserve Now" 
										data-contenturl="https://www.scanflip.com/Sport-Activities/Best-Westerns-Plus-Cairn-Croft-Hotel16-for-All-Inclusive-Daytime-Admission-a-30-Value-MTAwOA==-ODE=.html" 
										data-calltoactionurl="https://www.scanflip.com/Sport-Activities/Best-Westerns-Plus-Cairn-Croft-Hotel16-for-All-Inclusive-Daytime-Admission-a-30-Value-MTAwOA==-ODE=.html" 
										data-clientid="1043802627758-jsh3e6koq6a34tqij458b4s0adk1ca88.apps.googleusercontent.com" 
										data-cookiepolicy="single_host_origin" 
										data-calltoactionlabel="RESERVE" 
										data-contentdeeplinkid="/scanflip/register" 
										data-calltoactiondeeplinkid="/register" 
										data-onshare="callbackfromgoogleplus" 										
									 >
                                         Google+
                                     </a>

                           
                            
                            </div>
        
			
        </div>
      </div>
      
     

      
</div>  
      
                            <div class="email_popup_div container_popup" style="padding:5px;">
                                
                                    <div id="" class="div_share_friend" style="height:310px;text-align:left;width:386px;line-height:17px">
                                  
                                    <div class="">
					<?php $img_src=ASSETS_IMG."/c/popup_logo.png";?>
                                        <img src="<?php echo $img_src;?>" alt="Scanflip" width="375px"/><br/>
                                        
                                    </div>
                                    <div class="share_friends"><?php echo $client_msg['sharing_email']['label_Share_With_Friends'];?></div>
                                    <div class="share_msg"><?php echo $client_msg['sharing_email']['label_To_Share_This_Offer_With_Friends'];?></div>
                                    <div class="message_pop">
                                        <strong><?php echo $client_msg['sharing_email']['label_Messages'];?></strong><br/>
                                        <strong><?php echo $client_msg['sharing_email']['label_I_Thought'];?></strong> <?php echo $client_msg['sharing_email']['label_You_Might'];?>
                                    </div>        
                                   
                                    <div class="notice_mail"><?php echo $client_msg['sharing_email']['label_Specify_up_to'];?></div>
                                      <div style="color:#FF0000;" id="popup_error" ></div>
                                    <div class="text_area"><textarea rows="4" cols="45" style="resize:none;margin-left:10px;overflow: auto;" id="txt_share_frnd" name="txt_share_frnd" ></textarea></div>
                                   
                                    <div class="buttons_bot">
                                       
                                        <input type="hidden" name="reffer_campaign_id1" id="reffer_campaign_id" value="" />
                                        <input type="hidden" name="reffer_campaign_name1" id="reffer_campaign_name" value="" />
                                        <input type="hidden" name="refferal_location_id1" value="" id="refferal_location_id" />
                                        <input type="button" class="btnsharegridbutton" value="Share" name="btn_share_grid" id="btnsharegridbutton" />
                                        <input type="button" class="btnsharecancelbutton" value="Cancel"  id="btn_cancel"  />
					<?php $img_src=ASSETS_IMG."/c/ajax-offer-loader.gif";?>
					<img src="<?php echo $img_src; ?>" alt="" id="shareloading" height="20px" width="20px" style="display:none"/>
                                    </div>
                               
                            </div>
                        </div><!-- -->
                        
                        <div class="mainloginclass" id="mainloginid" style="display:none">
                    <div id="modal-login">
                        <h2 id="modal-login-title"><?php echo $client_msg['login_register']['label_Login']?></h2>
              
                        <div class="calltoaction callout unit100">
                         <?php echo $client_msg['login_register']['label_Not_Member_Yet']?>  <a href="<?= WEB_PATH."/register.php" ?>" target="_parent"><strong><?php echo $client_msg['login_register']['label_Join_Now']?></strong></a>
                        </div>
                        <div id="form_login">
                                <form class="form_vertical" action="process.php" method="post"  id="login_frm">
                                    
                                  <div id="msg_error" style="display:block;color:red;font-size: 14px;height: 20px">
                                        
                                    </div>
                                  <label for="email-modal"><?php echo $client_msg['login_register']['Field_email']?></label><br>
                                  
                              
                              
                              
                                  <input type="text" value="" class="js-focus unit100" maxlength="128" name="emailaddress" id="email-modal">
                              
                                  <label for="password"><?php echo $client_msg['login_register']['Field_password']?></label><br>
                                  
                              
                              
                              
                                  <input type="password" maxlength="15" class="unit100" name="password" id="password" style="padding:0.231em;">
                              
                                  <div>
                                   <input type="hidden" name="hdn_reload_pre_selection" id="hdn_reload_pre_selection" value="" />
                                    <input type="submit" class="btn btn_primary mr10" value="<?php echo $client_msg['login_register']['Field_login_button']?>" name="btnLogin" id="login_submit">
                                  </div>
                                </form>
              
                                <div class="mt20" style="">
                                    <div style="padding-bottom: 5px">
                                      <span style="float:left;width:140px;margin-top:10px;" >
                                            <?php echo $client_msg['login_register']['label_Register_Sign_In']?>
                                      </span>
                                   </div>
                                  <div class="left ml5" style="float:left;">
                                      <span class="btn btn_gplus glyph glyph_inverse glyph_gplus" id="g-signin-custom">
                                             <a target="_parent" class="login" href="<?php echo $authUrl ?>">
												google+
											</a>
                                      </span>
                                   </div>
                                  
                                    
                                   <div class="left ml5" style="margin-left:10px;float:left;">
                                        <a href="<?php echo WEB_PATH."/facebook_login.php" ?>" target="_parent" class="btn btn_facebook glyph glyph_inverse glyph_facebook">
                                        
                                              facebook
                                        </a>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                
                                				   
                
                                <div class="mt20">
                                  <a href="javascript:void(0)" class="textlink" style="border-bottom:1px solid #0F2326;color:#0F2326"><?php echo $client_msg['login_register']['label_Forgot_Password']?></a>
                                </div>
                            </div>
                 </div>
        </div> 
                        <?php
                        if(isset($_SESSION['customer_id'])){
                        $JSON = $objJSON->get_customer_profile();
$RS = json_decode($JSON);
                        ?>
        <div class="updateprofile" id="updateprofile" style="display:none;padding:11px;" >
                    <div id="modal-login" style="height:310px;text-align:left;width:386px;line-height:17px">
                        <h2 id="modal-login-title"><?php echo $client_msg['login_register']['label_Update_Profile'];?></h2>
              
                        <div class="unit100" style="height:30px;">
                        </div>
                        <div id="form_login">
                                <form class="form_vertical" action="process.php" method="post"  id="login_frm">
                              
                                  <label for="email-modal"><?php echo "* ".$client_msg['login_register']['label_Gender'];?></label>
                                  <select name="gender" id="gender" class="genderclass"   style="padding:0.231em;margin:0 0 0.154em;" class="js-focus"  >
				        <option></option>
                                        <option value="1" <?php if($RS[0]->gender == 1){ echo "selected";} ?> >Male</option>
                                        <option value="2" <?php if($RS[0]->gender == 2){ echo "selected";} ?> >Female</option>
                                </select>
				  <div class="err_gender" style="color:red;height: 18px"></div>
                              
                                  <label for="dob"><?php echo "* ".$client_msg['login_register']['label_Date_Of_Birth'];?></label>
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
	      <option ></option>
		<?
		for($i=date("Y")-60; $i<=date("Y"); $i++){	
		?>
			<option value="<?=$i?>" <? if($RS[0]->dob_year == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
		<?
		}
		?>
	</select>
	<div class="err_dob" style="color:red;height: 18px"></div>
                            
                                   <label for="country"><?php echo "* ".$client_msg['login_register']['label_Country'];?></label>
                                  <select name="country" id="country"  class="countryclass" style="margin:0 0 0.154em;">
				    <option ></option>
		<option value="USA" <? if($RS[0]->country == "USA") echo "selected";?> country_code="US">USA</option>
		<option value="Canada" <? if($RS[0]->country == "Canada") echo "selected";?>  country_code="CA">Canada</option>
	   
	</select>
				   <div class="err_country" style="color:red;height: 18px"></div>
                             
                                   <label for="postalcode"><?php echo "* ".$client_msg['login_register']['label_Postal_Code'];?> </label>
                                   <input type="text" maxlength="15" class="postalcodeclass"  name="postalcode" id="postalcode" class="js-focus" style="padding:0.231em;margin:0 0 0.154em;" value="<?=$RS[0]->postalcode?>">
<!--                                   <input type="text" name="postalcode" id="postalcode" style="width:120px;" value=""  class="unit100" style="padding:0.231em;" >-->
                                   
                                    
				   <div class="err_postalcode" style="color:red;height: 18px"></div>
				   
				  
                                   
                                  <div>
                                   
                                    <p class="actions" style="" align="center">
            	<input type="button" id="btnupdateprofile" name="btnupdateprofile" value="Save" onClick="">
                <input type="button" class="btnsharecancelbutton" value="Cancel" id="btn_cancel_forgot"  />
            </p>
                                  </div>
                                </form>
              
                            </div>
                 </div>
        </div>
                        <?php } ?>
        <!----  --->
        <div class="forgotmainclass" id="forgotmainid" style="display:none;padding-left:20px;padding-right:20px">
        <form action="" method="post" id="reg_form">
        <div style="font-weight:bold;padding:5px 5px 5px 0px;"><?php echo $client_msg['login_register']['label_Forgot_Password_Assistance'];?></div>
        <div>
		<label for="email-requestPasswordReset"><?php echo $client_msg['login_register']['label_Forgot_Assistance'];?></label>
	</div>
	
	
            <div style="margin-top:8px;">
                <table>
					<tr>
						<td width="44%" style="font-size:13px;">
							<b><?php echo $client_msg['login_register']['Field_Forgot_Emailaddress'];?></b>
						</td>
						<td colspan="2">
							<input type="text" name="email" id="email" style="width:100%"/>
						</td>
					</tr>
                                         <tr>
                                            <td colspan="2">
                                                <div id="emailerror" style="height:10px;color:red;font-size: 13px"></div>
                                            </td>
                                        </tr>
					<tr>
						<td width="44%" style="font-size:13px;">
							<b><?php echo $client_msg['login_register']['Field_Forgot_Captcha'];?></b>
						</td>
						<td colspan="2">
							<input type="text" id="mycaptcha_rpc" name="mycaptcha_rpc" style="width:50%;" /><br/>
						</td>
					</tr>
					<tr>
						<td width="44%" style="font-size:13px;">
							<a id="captcha_image" href="javascript:void(0)"><?php echo $client_msg['login_register']['label_captcha_different'];?></a>
						</td>
						<!--<td>
							<img id="captcha_image_src" src="captcha.gif" style=""/>
						</td> -->
						<td>
							<img id="captcha_ajax_loading" class="img_captcha_loading" style="display:none" src="<?php echo ASSETS_IMG;?>/c/ajax-loader-1.gif"/>
						</td>
					</tr>
                                        <tr>
                                            <td colspan="2">
                                                <div id="captchaerror" style="height:10px;color:red;font-size: 13px"></div>
                                            </td>
                                        </tr>
				</table>
            </div>
            
	    
	    <div class="forgotmsgdiv">
	      
	    </div>
            <p class="actions" style="">
            	<input type="button" id="btnRequestPassword" name="btnRequestPassword" value="Continue" >
                <input type="button" class="btnsharecancelbutton" value="Cancel"  id="btn_cancel_forgot"  />
            </p>
	    <div style="margin-bottom:20px;">
	      <b><?php echo $client_msg['login_register']['label_Having_Trouble'];?></b>&nbsp;&nbsp;<a href="javascript:void(0)"><?php echo $client_msg['login_register']['label_Contact_Service'];?></a>
	  </div>
            <p>
            	<?php //echo $_SESSION['req_pass_msg']; ?>
            </p>
        </form>
    </div>
        <!---- --->
        <div class="errormainclass" style="display:none;padding-left:20px;padding-right:20px;padding-top:15px">
          <div style="font-weight:bold;padding:5px 5px 5px 0px;"><?php echo $client_msg['login_register']['label_Forgot_Password_Assistance'];?></div>
	  <br />
	  <table>
					<tr>
						<td width="32%" style="font-size:13px;">
							<b><?php echo $client_msg['login_register']['Field_Forgot_Emailaddress'];?></b>
						</td>
						<td>
							<div id="errorlabelid"></div>
						</td>
						<td>
						
					</tr>
					<tr>
					    <td colspan="2">
					      <p style='color:red;font-size:13px;'><?php echo $client_msg['login_register']['Msg_Not_Found_Forgot'];?></p>
					    </td>
					</tr>
	  </table>
	  <p class="actions" style="">
            	<input type="button" id="btn_goback_error" name="btn_goback_error" value="Go Back">
                
		<a href="javascript:parent.jQuery.fancybox.close();" id="btn_cancel_error" class="btn_cancel_error" >Cancel</a>
            </p>
	    <div style="margin-bottom:20px;">
	      <b>Having trouble ?</b>&nbsp;&nbsp;<a href="javascript:void(0)">Contact Customer Service</a>
	  </div>
	  
    </div>
    <div class="successmainclass" style="display:none;padding-left:20px;padding-right:20px;padding-top:15px">
          <div style="font-weight:bold;padding:5px 5px 5px 0px;"><?php echo $client_msg['login_register']['label_Forgot_Password_Assistance'];?></div>
	  <br />
	  <br />
	  <div style="color:#E06500;font-size:15px;font-weight:bold">
	   <?php echo $client_msg['login_register']['label_Check_Email'];?> 
	  </div>
	  <div style="font-size:13px;">
            	<?php echo $client_msg['login_register']['label_Succedd_Forgot'];?>
          </div>
	  
	  
    </div>
                  
        </div>
    <div id="enter_zipcode_div" style="display:none">
        <div  style="width:423px">
        <div class="geolocation_error" style="margin:10px;" align="center">
            <div class="geo_errormsg"><?php echo $client_msg['login_register']['label_Geolocation_Is_Not_Supported'];?></div>
             <br/>
           <input type="submit" name="geo_cancle" id="geo_cancle" value="OK" />
        </div>
        <div class="zipcode_section" style="margin:10px;display:none;">
                <div style="margin-bottom:10px"><?php echo $client_msg['login_register']['label_Enter_Your_Current_Location'];?></div>
        <div>
            <input type="text" name="enter_zip" id="enter_zip" value="" style="width:255px" onKeyUp="popup_searchdeal(this.value,event)"/> 
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" name="searchdeal_zip" id="searchdeal_zip" value="search deal" />
        </div>   
        </div>
        <div class="zipcodeerror_section"  style="display:none;margin:10px;" align="center">
            <div><?php echo $client_msg['login_register']['label_Enter_Valid_Current_Location'];?></div>
               <br/>
                 <input type="submit" name="searchdeal_cancle" id="searchdeal_cancle" value="OK" />
        </div>
        </div>
    </div>
    <?php  if(isset($_SESSION['customer_id'])){ ?>
    <div id="div_profile" style="display:none">
        <input type="hidden" name="subscribe_location_id" id="subscribe_location_id" value="" />
     <div class="updateprofile" id="updateprofile" style="display:block;padding:11px;" >
                    <div id="modal-login" style="height:310px;text-align:left;width:386px;line-height:17px">
                        <h2 id="modal-login-title"><?php echo $client_msg['login_register']['label_Update_Profile'];?>Update Profile</h2>
              
<div class="unit100" style="height:30px;">
                        </div>
                        <div id="form_login">
                                <form class="form_vertical" action="process.php" method="post"  id="login_frm">
				      <label for="email-modal"><?php echo "* ".$client_msg['login_register']['label_Gender'];?></label>
                                  <select name="gender" id="gender" class="genderclass"   style="padding:0.231em;margin:0 0 0.154em;" class="js-focus"  >
				        <option></option>
                                        <option value="1" <?php if($RS[0]->gender == 1){ echo "selected";} ?> >Male</option>
                                        <option value="2" <?php if($RS[0]->gender == 2){ echo "selected";} ?> >Female</option>
                                </select>
				  <div class="err_gender" style="color:red;height: 18px"></div>
                              
                                  <label for="dob"><?php echo "* ".$client_msg['login_register']['label_Date_Of_Birth'];?></label>
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
	      <option ></option>
		<?
		for($i=date("Y")-60; $i<=date("Y"); $i++){	
		?>
			<option value="<?=$i?>" <? if($RS[0]->dob_year == $i) echo "selected";?>><? if($i<10) echo "0".$i; else echo $i;?></option>
		<?
		}
		?>
	</select>
	<div class="err_dob" style="color:red;height: 18px"></div>
                            
                                   <label for="country"><?php echo "* ".$client_msg['login_register']['label_Country']; ?> </label>
                                  <select name="country" id="country"  class="countryclass" style="margin:0 0 0.154em;">
				    <option ></option>
		<option value="USA" <? if($RS[0]->country == "USA") echo "selected";?> country_code="US">USA</option>
		<option value="Canada" <? if($RS[0]->country == "Canada") echo "selected";?>  country_code="CA">Canada</option>
	   
	</select>
				   <div class="err_country" style="color:red;height: 18px"></div>
                             
                                   <label for="postalcode">* Postal Code: </label>
                                   <input type="text" maxlength="15" class="postalcodeclass"  name="postalcode" id="postalcode" class="js-focus" style="padding:0.231em;margin:0 0 0.154em;" value="<?=$RS[0]->postalcode?>">
				   <div class="err_postalcode" style="color:red;height: 18px"></div>
                                  <div>
                                   
                                    <p class="actions" style="" align="center">
            	<input type="button" id="btnupdateprofile" name="btnupdateprofile" value="Save" onClick="">
                <!--<input type="button" class="btnsharecancelbutton" value="Cancel" id="btn_cancel_forgot"  />-->
		<input type="button" class="btncancelprofile" value="Cancel" id="btncancelprofile"  />
            </p>
                                  </div>
                             
                                </form>
              
                            </div>
                 </div>
        </div>
        </div>
    <?php } ?>
               <?php 
if($total_records1 > 0)
{
?>

<?php } ?>                 </div>

<div id="dialog-message" title="Message Box" style="display:none">

    </div>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/search-deal-script1.js"></script>
 <script type="text/javascript" src="<?php echo ASSETS_JS?>/c/jquery.form.js"></script>
<script>
    jQuery("body").on("click","#btncancelprofile",function(event){
event.stopPropagation();
       jQuery.fancybox.close();
    });
      jQuery(document).ready(function(){
	  //alert("ready 3218");
         if(jQuery("#div_profile #postalcode").val() == "")
             {
                 jQuery("#is_profileset").val("0");
             }
             else{
                 jQuery("#is_profileset").val("1");
             }
	     jQuery('body').on("change",".genderclass",function () {
		
		jQuery(".genderclass option:selected").each(function(){
		        jQuery(".genderclass option").removeAttr("selected");
			if (jQuery(this).text() != "")
			{
			  
			  jQuery(this).attr("selected","selected");
			}
			
		    });
		  //jQuery(this).attr("selected","selected");
	    });
	    jQuery('body').on("change",'.dateofmonth',function () {
		
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
	    jQuery('body').on("change",".dateofday",function () {
		
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
	    jQuery('body').on("change",".dateofyear",function () {
		
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
	    jQuery('body').on("change",".countryclass",function () {
		jQuery(".countryclass option:selected").each(function(){
		
		
		        jQuery(".countryclass option").removeAttr("selected");
			if (jQuery(this).text() != "")
			{
			  
			  jQuery(this).attr("selected","selected");
			  //alert(jQuery(this).val());
			}
		    });
			var change_value=this.value;
			
			if(change_value == "USA")
			{
				$(".fancybox-inner #state").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");
			   
			   
			}
			else
			{
				  $(".fancybox-inner #state").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>"); 
				
			}
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

    jQuery("body").on("click",".fancybox-inner #btnupdateprofile",function(event){
event.stopPropagation();
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
	
	
        
          if(flag)
              {
          $.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'btnUpdateProfile_compulsary_field=true&customer_id=<?php if(isset($_SESSION['customer_id']))echo $_SESSION['customer_id'] ?>&country='+$(".fancybox-inner #country").val()+'&gender='+$(".fancybox-inner #gender").val()+'&dob_month='+$(".fancybox-inner #dob_month").val()+'&dob_day='+$(".fancybox-inner #dob_day").val()+'&dob_year='+$(".fancybox-inner #dob_year").val()+'&postalcode='+$(".fancybox-inner #postalcode").val()+'&firstname='+$(".fancybox-inner #firstname").val()+'&lastname='+$(".fancybox-inner #lastname").val()+'&state='+$(".fancybox-inner #state").val()+'&city='+$(".fancybox-inner #city").val(),
                  async:false,
		  success:function(msg)
		  {
                      jQuery("#is_profileset").val("1");
                      if(jQuery("#profile_view").val()=="rserve")
                          {
                       $(".fancybox-inner .btn_mymerchantreserve").trigger("click");
                         jQuery(".fancybox-inner .popupmainclass").css("display","block");
                         jQuery(".fancybox-inner .email_popup_div").css("display","none");
                         jQuery(".fancybox-inner .mainloginclass").css("display","none");
                         jQuery(".fancybox-inner .updateprofile").css("display","none");
                          }
                        if(jQuery("#profile_view").val()=="subscribe")
                        {
                                 jQuery(".subscribestore[s_lid='"+jQuery(".fancybox-inner #subscribe_location_id").val()+"']").trigger("click");
                                 jQuery.fancybox.close(); 

                        }
						if(jQuery("#profile_view").val() == "print_link")
						{
							pre_value = jQuery("#print_redirect_link").val();
							pre_array= pre_value.split("=");
							cid = pre_array[0];
							lid = pre_array[1];
							alert(pre_value+"=="+cid+"=="+lid);
							jQuery(".deal_blk[locid='"+lid+"'][campid='"+cid+"'] .btn_print1").trigger("click");
							jQuery.fancybox.close(); 
						}
                       
                  }
          });
        }
    });
  $("body").on("click",".fancybox-inner .btn_mymerchantreserve",function(event){
event.stopPropagation();
   //alert("sadjhsad");
	//alert("hello"+jQuery("#rservedeals_value").val());
        
    var ele = $(this);
    //alert(ele.attr("cid")+":"+ele.attr("lid")+";");
    var reserve_val = "";
    var deal_barcode_val = "";
if($("#rservedeals_value").val()!=" ")
    {
        reserve_val = $("#rservedeals_value").val();
    }
      if($("#deal_barcode").val()!=" ")
    {
        deal_barcode_val = $("#deal_barcode").val();
    }
   /// alert(deal_barcode_val);
   $.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'btnreservedeal=true&campaign_id='+$(this).attr("cid")+'&location_id='+$(this).attr("lid")+'&reload=yes&r_url=searchdel&timestamp='+formattedDate(),
          async:false,
		  success:function(msg)
		  {
		               var obj = jQuery.parseJSON(msg);
					  if(obj.loginstatus == "false")
                      {
					//	alert(obj.link);
                            parent.window.location.href= obj.link;
                      }
                      else
                      {
                     if(obj.status == "true")
                         {
						 
                           reserve_val += ele.attr("cid")+":"+ele.attr("lid")+";";
                              $("#rservedeals_value").val(reserve_val);
                            //parent_tr.detach();
                            $(".fancybox-inner  .btn_mymerchantreserve").detach();
                            $(".fancybox-inner  .barcode").attr("src",obj.barcode);
                            $(".fancybox-inner  .sharediv").css("display","block");
                            $(".fancybox-inner  .showvocherdiv").css("display","none");
                            $(".fancybox-inner  #ShowshareId").css("display","none");
                            $(".fancybox-inner  #ShowVoucherId").css("display","block");
			    $(".fancybox-inner  #saveofferid").show();
                          //  alert(obj.barcode);
                            deal_barcode_val += ele.attr("cid")+"="+ele.attr("lid")+"&"+obj.barcode+";";
							 var ct =getParam( 'br' , obj.barcode );
                         
                            $("#deal_barcode").val(deal_barcode_val);
							
							
							if(jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view").length == 1)
							{
								jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view-reserve").css("display","none");
								camp_href = jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view-reserve").attr("href");
								jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view").show();
								jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .btn_unreserve").show();
								jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .btn_print1").css("display","block");
							}
							else
							{
								jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view-reserve").hide();
								camp_href = jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view-reserve").attr("href");
								jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview").append('<a class="view" href="'+camp_href+'" target= "_parent">View</a>&nbsp<a href="javascript:void(0)" class="btn_unreserve" u_campid="'+ele.attr("cid")+'" u_locid="'+ele.attr("lid")+'" >Unreserve</a>&nbsp');
								jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .kat_img").after("<img class='btn_print1' cid='"+ele.attr("cid")+"' lid='"+ele.attr("lid")+"' barcodea='"+ct+"' align='left' style='margin:5px 0 0 10px;' src='<?php echo ASSETS_IMG?>/c/icon-print-deal2-black.png' />");
								//
							}
							var url_d = jQuery("#dealslist  .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid');
                            if(ct!= ""){
                              url_d = url_d.replace(/(br=)[^\&]+/, '$1' + ""+ct+"");
                            }
							oleft = getParam( 'o_left' , url_d );
						//	alert(oleft);
							oleft = parseInt(oleft) - 1;
						//	alert(obj.o_left);
                            url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "1");
                             url_d = url_d.replace(/(is_mydeals=)[^\&]+/, '$1' + "1");
							  url_d = url_d.replace(/(o_left=)[^\&]+/, '$1' + ""+obj.o_left+"");
                            //url_d = url_d.replace(/(br=)[^\&]+/, '$1' + ct);
                            jQuery("#dealslist   .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid',url_d);
							
							/*   
                            var url_d = jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid');
                            url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "1");
                            url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
                            jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid',url_d);
                             url_d = jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid');
                            url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "1");
                            url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
                            jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid',url_d);
                             url_d = jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid');
                            url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "1");
                            url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
                            jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid',url_d);
							*/
                         }
                         else{
                             //alert("Sorry , No offers left for this campaign for your selected location");
                            // jQuery(".fancybox-inner .errorclasspopup").html("Sorry , "+obj.error_msg);
                             jQuery(".fancybox-close").trigger("click");
                             jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"']").detach();
                             jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"']").detach();
                             jQuery("#hdn_reserve_err").val(ele.attr("lid"));
							 jQuery.fancybox({
									content:"<div class='content_msg'  ><div >"+obj.error_msg+"</div><div class='footer_btn_msg'><input type='submit' name='cancel' id='cancel' value='Ok' onclick='jQuery.fancybox.close()'/></div></div>",
									width:'auto',
									height: 'auto',
									fitToView:true,
									imageScale:true,
									autoSize:false,
									type: 'html',
									openEffect : 'elastic',
									openSpeed  : 300,
									mouseWheel: false,               
									closeEffect : 'fade',
									closeSpeed  : 300,
									helpers: {
											overlay: {
											opacity: 0.3
											} // overlay
									},
									beforeShow:function(){
										//jQuery(".fancybox-inner").addClass("offer_redem");
									}
								});
                         }
						}
						// alert(1);
                  }
   });
   return false;
 //  window.location.href = $(this).attr("redirect_url");
});
  $("body").on("click","#btnsharegridbutton",function(event){
event.stopPropagation();
                
		var email_str = jQuery(".fancybox-inner .email_popup_div #txt_share_frnd").val();
                var  c = jQuery(".fancybox-inner .email_popup_div #reffer_campaign_id").val() ;
                var  l =  jQuery(".fancybox-inner .email_popup_div #refferal_location_id").val();
                var msg ="";
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
                            msg = "<?php echo $client_msg['sharing_email']['msg_Please_Check_Email'];?>";
				flag = false;
				break;
			}
		}
                    }
                 else
                 {
                      msg = "<?php echo $client_msg['sharing_email']['msg_Please_Enter_Email'];?>"; 
                     flag = false;
		}
               
		if(flag)
		{ 
		        //open_popuploader('Notificationloader');
		       $.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/process.php",
									  data: "btn_share_grid=yes&reffer_campaign_id=" +c +'&refferal_location_id='+l+'&txt_share_frnd='+email_str+"&domain=4&medium="+jQuery("#medium").val()+'&timestamp='+formattedDate(),
                                      async : true,
                                      success: function(msg) {
                                        
                                         
                                          //close_popuploader('Notificationloader');
										  /*
                                          jQuery(".fancybox-inner .popupmainclass").css("display","block");
                                          jQuery(".fancybox-inner .email_popup_div").css("display","none");
                                          jQuery(".fancybox-inner .mainloginclass").css("display","none");
                                          jQuery(".fancybox-inner .updateprofile").css("display","none");
                                            */ 
                                      }
                        });
		       jQuery(".fancybox-inner .popupmainclass").css("display","block");
			  jQuery(".fancybox-inner .email_popup_div").css("display","none");
			  jQuery(".fancybox-inner .mainloginclass").css("display","none");
			  jQuery(".fancybox-inner .updateprofile").css("display","none");
			return true;
		}
		else {
			$(".fancybox-inner .email_popup_div #popup_error").text(msg)
			 
			return false;
		}
		
                
	});
        
         
     function processLogJson(data) {
       
	//alert(data.c_id+"==="+data.l_id);
	if(data.status == "true"){
            
	    //$(".mainloginclass").hide();
            //$(".popupmainclass").show();												
		window.top.location.href = data.redirecting_path;
		if((data.redirecting_path).indexOf("search-deal") != -1)
		{
			location.reload();
		}
             //window.close();
                
	}else{
           
		//alert(data.message);
                jQuery('.fancybox-inner #msg_error').html(data.message);
		return false;
	}
     
}

 jQuery(".fancybox-inner #saveofferid").live({
        mouseenter:
           function()
           {
        jQuery(".fancybox-inner .cust_attr_tooltip").css("display","block");
           },
        mouseleave:
           function()
           {
jQuery(".fancybox-inner .cust_attr_tooltip").css("display","none");
           }
       }
    );
//        jQuery(".fancybox-inner #saveofferid").live("hover",function(){
//	//  $(".cust_attr_tooltip").show();
//        alert("1");
//        jQuery(".fancybox-inner .cust_attr_tooltip").css("display","block");
//	  },function(){
//         alert("2");
//	  //$(".cust_attr_tooltip").hide();
//          jQuery(".fancybox-inner .cust_attr_tooltip").css("display","none");
//	});
        $("body").on("click",".fancybox-inner #Notificationlogin",function(event){
event.stopPropagation();
				
                jQuery(".fancybox-inner .popupmainclass").css("display","none");
                    jQuery(".fancybox-inner .email_popup_div").css("display","none");
            jQuery(".fancybox-inner .mainloginclass").css("display","block");
        });
	
	$("body").on("click","#captcha_image",function(event){
event.stopPropagation();
	    
	    var captcha_path="<?php if(isset($path_captcha)){echo $path_captcha;}?>";
		 d = new Date();
	     var image_path="<?php echo WEB_PATH?>/captcha.gif?"+d.getTime();
		//jQuery("#captcha_ajax_loading").css("display","block");
		jQuery(".img_captcha_loading").css("display","block");
		
	             $.ajax({
			       url:captcha_path ,
			       
			       success: function(result){
						
				//location.reload();
				//jQuery("#captcha_ajax_loading").css("display","none"); 
				jQuery(".img_captcha_loading").css("display","none"); 
				 $(".fancybox-inner .forgotmainclass #captcha_image_src").attr('src',image_path);
				
				 
			       }
			  });
	    
	});
	
	jQuery('body').on("click",".fancybox-inner #ShowVoucherId",function(event){
event.stopPropagation();
	    // $(".sharediv").show();
            var vid = jQuery(this).attr("vid");
      //      alert(vid);
            $.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/process.php",
                                      data: "show_voucher=yes&vid="+vid,
                                      async : false,
                                      success: function(msg) { 
                                    ///   alert(msg);
                                      
                                          var obj = jQuery.parseJSON(msg);
                                          if(obj.loginstatus == "false")
                                              {
                                                  parent.window.location.href= obj.link;
                                              }
                                              else
                                              {
                                                        if(obj.status == "true")
                                                         {
                                                             
                                                             
                                                         
                                                                jQuery(".fancybox-inner .sharediv").css("display","none");
                                                                jQuery(".fancybox-inner .showvocherdiv").css("display","block");
                                                                jQuery(".fancybox-inner #ShowshareId").css("display","block");
                                                                jQuery(".fancybox-inner #ShowVoucherId").css("display","none");
                                                                

                                                         }
                                                         else{
                                                             //alert("");
                                                                var msg="<?php echo $client_msg["search_deal"]["Msg_No_Offers"];?>";
                                                               
                                                                jQuery(".fancybox-inner .errorclasspopup").html(msg);
                                                                            
                                                         }
                                              }
                                              
                                             
                                      }
                        });
            
	});
        jQuery('body').on("click",".fancybox-inner #ShowshareId",function(){
	    // $(".sharediv").show();
             jQuery(".fancybox-inner .sharediv").css("display","block");
	     jQuery(".fancybox-inner .showvocherdiv").css("display","none");
             jQuery(".fancybox-inner #ShowshareId").css("display","none");
             jQuery(".fancybox-inner #ShowVoucherId").css("display","block");
	});	


jQuery("body").on("click",".subscribestore",function(event){
event.stopPropagation();
    var flag = true;
   var  locid = $(this).attr("s_lid");
    var  locid1 = $(this).attr("s_lid1");
   if(jQuery("#is_profileset").val() != 1){
 $.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'is_userprofileset=true&custome_id=<?php if(isset($_SESSION['customer_id']))echo $_SESSION['customer_id']; ?>',
                  async:false,
		  success:function(msg)
		  {
                       var obj = jQuery.parseJSON(msg);
                     
                      if(obj.is_profileset == 1)
                      {
                          flag= true;
                      }
                  else{
                       jQuery("#profile_view").val("subscribe");
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
                                   jQuery(".fancybox-inner #subscribe_location_id").val(locid);
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
    var ele = jQuery(this);
    //alert('btnRegisterStore=1&location_id='+$(this).attr("s_lid"));
	var locid = jQuery(this).attr("s_lid");
	jQuery(".location_tool[locid='"+ locid +"']").find(".sub_loader").css("display","block");
     $.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'btnRegisterStore=1&location_id='+$(this).attr("s_lid"),
                 // async:false,
		  success:function(msg)
		  {
				var obj = jQuery.parseJSON(msg);
                        if(obj.loginstatus == "false")
                        {
							//alert(obj.link);
                           parent.window.location.href=obj.link;
                        }
                        else
                        {  
                      
          //            alert(ele.text());
                     ele.text("Unsubscribe");
            //          alert(ele.text());
                      ele.removeClass("subscribestore");
                      ele.addClass("unsubscribestore");
					  ele.parent().prev().removeClass("save_subscribe_icon");
						 ele.parent().prev().addClass("save_unsubscribe_icon");
                      //alert(locid);
                         jQuery("#temp_infowiondow").html(infowindowcontent[locid1]);
                    var ele1 = jQuery("#temp_infowiondow .subscribestore");
                    ele1.text("Unsubscribe to store");
                    ele1.removeClass("subscribestore");
                      ele1.addClass("unsubscribestore");
      infowindowcontent[locid1] = jQuery("#temp_infowiondow").html();
                                  // alert(infowindowcontent[locid1])  ;
                     // alert(1);
                     jQuery(".location_tool[locid='"+ locid +"']").find(".sub_loader").css("display","none");     
					 }
                  }
   });
 //  return false;
});
/*
function facebookfunction(campaign_title,summary,th_link,imgsrc)
{
    
     $.ajax({
		  type:"POST",
		  url:'process.php',
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
                          // window.location='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - "+encodeURIComponent(campaign_title)+"&p[summary]="+encodeURIComponent(summary)+"&p[url]="+encodeURIComponent(th_link)+"&p[images][0]="+imgsrc+"', 'sharer', 'toolbar=0,status=0,width=548,height=325';
                           urlpath='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - '+encodeURIComponent(campaign_title)+'&p[summary]='+encodeURIComponent(summary)+'&p[url]='+encodeURIComponent(th_link)+'&p[images][0]='+imgsrc;
						   // 01 10 2013
						   urlpath='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - '+encodeURIComponent(unescape(campaign_title))+'&p[summary]='+encodeURIComponent(unescape(summary))+'&p[url]='+encodeURIComponent(th_link)+'&p[images][0]='+imgsrc;
						   // 01 10 2013
                         var open_popup=window.open(urlpath,'_blank','width=500,height=400');
						 if (open_popup == null || typeof(open_popup)=='undefined')
						 {
							//alert("Turn off your pop-up blocker!");
                                                        var msg="<?php echo $client_msg['common']['Msg_Turn_Off_Popup']?>";
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
*/
function twitterfunction(webpath,campaign_title,th_link)
{
	//alert("in twitter");
    //alert(campaign_title);
     $.ajax({
		  type:"POST",
		  url:'process.php',
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
                          // window.location='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - "+encodeURIComponent(campaign_title)+"&p[summary]="+encodeURIComponent(summary)+"&p[url]="+encodeURIComponent(th_link)+"&p[images][0]="+imgsrc+"', 'sharer', 'toolbar=0,status=0,width=548,height=325';
                          //jQuery(".fancybox-inner .twitter_link").attr("onClick","window.open('https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20"+encodeURIComponent(campaign_title)+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link)+"','_blank')");
                          urlpath="https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20"+encodeURIComponent(campaign_title)+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link);
						// 01 10 2013
						  urlpath="https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20"+encodeURIComponent(unescape(campaign_title))+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link);
						// 01 10 2013
						var open_popup=window.open(urlpath,'_blank','width=500,height=550');
						if (open_popup == null || typeof(open_popup)=='undefined')
						 {
							//alert("Turn off your pop-up blocker!");
                                                        var msg="<?php echo $client_msg['common']['Msg_Turn_Off_Popup']?>";
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

function googlefunction(campaign_id,l_id,share)
{
    
    
     $.ajax({
		  type:"POST",
		  url:'process.php',
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
                          var urlpath="https://plus.google.com/share?url=https://www.scanflip.com/register.php?campaign_id="+campaign_id+"&amp;l_id="+l_id+"&amp;share=true&amp;customer_id=OTk=";
                            //alert(urlpath);              
                           var open_popup=window.open(urlpath,'_blank');
						   if (open_popup == null || typeof(open_popup)=='undefined')
						  {
							//alert("Turn off your pop-up blocker!");
                                                        var msg="<?php echo $client_msg['common']['Msg_Turn_Off_Popup']?>";
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
jQuery(document).ready(function(){
//alert("ready 3938");
       jQuery("body").on("click","#btn_msg_div",function(event){
event.stopPropagation();
          
           jQuery(".email_popup_div").hide();
           $.ajax({
		  type:"POST",
		  url:'process.php',
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
                           jQuery(".email_popup_div").show();
                       }
                      //alert(msg);
          
                          
                  }
            });
       })
      
//alert(infowindowcontent[103]);
//jQuery("#temp_infowiondow").html(infowindowcontent[103]);
//alert(jQuery("#temp_infowiondow .unsubscribestore").length);
//var ele = jQuery("#temp_infowiondow .unsubscribestore");
//  ele.text("Subscribe to store");
//            //          alert(ele.text());
//                      ele.removeClass("unsubscribestore");
//                      ele.addClass("Subscribestore");
//      infowindowcontent[103] = jQuery("#temp_infowiondow").html();
//alert(infowindowcontent[103]);
//                      
});

  jQuery(".fancybox-inner #btnRequestPassword").live("click",function(event){
event.stopPropagation();
			//alert("hi");
           var forgot_path="<?php echo WEB_PATH ;  ?>/validate_captcha.php";
           var mycaptcha_rpc=jQuery(".fancybox-inner  #mycaptcha_rpc").val();
           var email=jQuery(".fancybox-inner  #email").val();
           var return_value=validate_register();
              if(return_value==false)
              {
              }
              else
              {
                    jQuery.ajax({
                         url:forgot_path ,
                         data:'mycaptcha_rpc='+mycaptcha_rpc+'&email='+ email,
                         success: function(result){
                            if(result == "error")
							{
							   jQuery(".fancybox-inner .email_popup_div").css("display","none");
												jQuery(".fancybox-inner .popupmainclass").css("display","none");
							  jQuery(".fancybox-inner  .forgotmainclass").css("display","none");
							  jQuery(".fancybox-inner  .errormainclass").css("display","block");
												jQuery(".fancybox-inner  .successmainclass").css("display","none");
							  jQuery(".fancybox-inner  #errorlabelid").html(email);
							 
							  
							}
							else if(result == "success")
							{
								/*
								 $ code 7-06-2013
								*/
								jQuery(".fancybox-inner").css("height","320px");
								 jQuery(".fancybox-inner").css("overflow","hidden");
								 jQuery(".fancybox-inner").css("width","100%");
								/*
								 end $ code 7-06-2013
								*/
								
											  jQuery(".fancybox-inner .email_popup_div").css("display","none");
							  jQuery(".fancybox-inner .popupmainclass").css("display","none");
											  jQuery(".fancybox-inner .mainloginclass").css("display","none");
							  jQuery(".fancybox-inner  .forgotmainclass").css("display","none");
							  jQuery(".fancybox-inner  .successmainclass").css("display","block");
							  
							}
							else
							{
								jQuery(".fancybox-inner  .forgotmsgdiv").html(result);  
							}
                         }
                    });
              }
   });
   
	  
/* for enter zipcode */
function enter_zip_code_popup()
{
     check_geolocation_support();
 var flag=true; 
 if(typeof getCookie('mycurrent_lati') == "undefined" || getCookie('mycurrent_lati') == "" || getCookie('mycurrent_lati') == null )
     {
 //   check_user_last_saved_current address
    $.ajax({
        type:"POST",
        url:'process.php',
        data :'check_user_last_saved_current=true',
        async:false,
        success:function(msg){
             var obj = jQuery.parseJSON(msg);
                      if(obj.mlatitude == "" || obj.mlatitude == ""){
                          flag= false;
                      }else{
                          // setCookie("searched_location",jQuery(".fancybox-inner #enter_zip").val(),365);
                           window.location.reload(false);
                      }
               }
               });
    
if(! flag)
    {
        if(jQuery(".fancybox-inner .zipcodeerror_section").length != 0)
            {
         jQuery(".fancybox-inner .zipcodeerror_section").css("display","none");
    jQuery(".fancybox-inner .zipcode_section").css("display","block");
     jQuery(".fancybox-inner .geolocation_error").css("display","none");
            } 
            else{
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
            afterShow:function(){
              //  alert("In after");
                 $(".fancybox-inner").css("height","98px");
            },
            beforeShow:function(){
              //  alert("In before");
                //alert(jQuery("#activation_code").val());
               //  setCookie("code",jQuery("#activation_code").val(),365);
                   // $(".fancybox-inner").css("height","98px");
                    $(".fancybox-inner").addClass("enterZipcode");
                     jQuery(".fancybox-inner .zipcodeerror_section").css("display","none");
                       $(".fancybox-outer").css("width","423px");
    jQuery(".fancybox-inner .zipcode_section").css("display","block");
     jQuery(".fancybox-inner .geolocation_error").css("display","none");
            },
            helpers: {
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
            
'afterShow': function() {

    $('.fancybox-close').attr('id','close');
    //override fancybox_close btn
    
 jQuery("#close").unbind("click");
   jQuery("#close").detach();
}
            
            // helpers
    }); 
            }
}

     }
     else{
    jQuery.fancybox.close();
}
}          
jQuery("body").on("click","#searchdeal_zip",function(event){
event.stopPropagation();
    $.ajax({
        type:"POST",
        url:'process.php',
        data :'getlatitudelongitude_zipcode=true&searched_location='+ jQuery(".fancybox-inner #enter_zip").val(),
        async:false,
        success:function(msg)
        {
             var obj = jQuery.parseJSON(msg);
            if(obj.mlatitude == "" || obj.mlatitude == ""){
                jQuery(".fancybox-inner .zipcode_section").css("display","none");
                jQuery(".fancybox-inner .zipcodeerror_section").css("display","block");
                jQuery(".fancybox-inner .geolocation_error").css("display","none");
                return false;
            }
            else{
                setCookie("searched_location",jQuery(".fancybox-inner #enter_zip").val(),365);
	//	window.location.href=  WEB_PATH+"/search-deal.php";
	 window.location.reload(false);
            }
        }
    });
});

jQuery("body").on("click","#searchdeal_cancle",function(event){
event.stopPropagation();
 jQuery(".fancybox-inner .zipcodeerror_section").css("display","none");
    jQuery(".fancybox-inner .zipcode_section").css("display","block");
     jQuery(".fancybox-inner .geolocation_error").css("display","none");
	   
});
function popup_searchdeal(txt_val,e)
{
        if(e.keyCode==13)
        {
           // search_deal();
           jQuery("#searchdeal_zip").trigger("click");

        }
		
}
function check_geolocation_support()
{
 
   if(Modernizr.geolocation)
    {
         jQuery("#get_current_location").css("display","block");
    }
    else{
        jQuery("#get_current_location").css("display","none");
              jQuery.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'set_not_supported_session_value=true',
                  async:false,
		  success:function(msg)
		  {
                  
                  }
              });         
      
        <?php   ?>
    }

}
//check_geolocation_support();
function myTimer()
{
	/* if u user not respond to geo location */
	visitorGeolocation = new geolocate(false, true, 'visitorGeolocation');
	var callback = function(){
			setCookie("mycurrent_lati",visitorGeolocation.getField('latitude'),365);
			setCookie("mycurrent_long",visitorGeolocation.getField('longitude'),365);
			setCookie("searched_location",visitorGeolocation.getField('cityName')+","+visitorGeolocation.getField('regionName')+","+visitorGeolocation.getField('countryName'),365);
			  window.location.reload(false);
		};
	visitorGeolocation.checkcookie_geo(callback);
	/* if u user not respond to geo location */
   // getLocation1(jQuery("#is_geo_location_supported").val());
}
jQuery("body").on("click","#geo_cancle",function(event){
event.stopPropagation();
 //alert("click");
  /*  jQuery(".fancybox-inner .zipcodeerror_section").css("display","none");
    jQuery(".fancybox-inner .zipcode_section").css("display","block");
     jQuery(".fancybox-inner .geolocation_error").css("display","none"); */
     enter_zip_code_popup();
});
 jQuery("#popupcancel").live("click",function(event){
event.stopPropagation();
           jQuery.fancybox.close(); 
       return false; 
    });
 /* for entering zipcode */
</script>
 <div id="temp_infowiondow" style="display:none">
     
 </div>
<!--[if IE]>
<style>
.tableMerchant.reviews_table.dataTable{
float:none !important;
}

	/*
   .fancybox-opened 
   {
		width:auto !important;
   }
  
   .fancybox-opened .fancybox-outer 
   {
   		width:auto !important;
   }
   
   .fancybox-inner 
   {
   		width:auto !important;
   }
    */
</style>
<![endif]-->
<style>
.tableMerchant.reviews_table.dataTable{
float:none !important;
}

.show_deal_blk{
	display : block !important;
}
.hide_deal_blk{
	display : none !important;
}

</style>

<script type="text/javascript">
/*
jQuery(".ratinn_box").mouseenter(function() {
	alert("hi");
	var p = jQuery(this).position();
	//alert(p.top+"====="+p.left);
	l = p.left -18;
	t = p.top - 35;
	var msg = jQuery(this).attr("title");
	jQuery(this).next().css("left",l);
	jQuery(this).next().css("top",t);
	jQuery(this).next().css("display","block");
	 
}).mouseleave(function() {
    jQuery(this).next().css("display","none");
});
*/
jQuery(".ratinn_box").live('mouseenter',function(event){
event.stopPropagation();event.preventDefault();
	var p = jQuery(this).position();
	//alert(p.top+"====="+p.left);
	//l = p.left -18;
	//t = p.top - 35;
	l = p.left -18+50;
	t = p.top - 35+30;
	
	var msg = jQuery(this).attr("title");
	jQuery(this).next().css("left",l);
	jQuery(this).next().css("top",t);
	jQuery(this).next().css("display","block");
	 
});
jQuery(".ratinn_box").live('mouseleave',function() {
    jQuery(this).next().css("display","none");
});
jQuery(".fancybox-inner .btn_share_facebook").live("click",function(){
	//open_popuploader('Notificationloader');
	jQuery(".share_loader").css("display","block");
   //alert(this.id);
   //alert(jQuery(this).attr('data'));
   var data_array=jQuery(this).attr('data').split('###');
   
        var campaign_id=data_array[0];
        //alert(campaign_id);
        var l_id=data_array[1];
        //alert(l_id);
        var bussiness_name= data_array[2];
        //alert(bussiness_name);
        var campaign_title= 'Scanflip Offer -'+data_array[3];
        var original_campaign_title=data_array[3];
        //alert(campaign_title);
        var summary=data_array[4];
        //alert(decodeURIComponent(summary));
        var redirect_url="<?php echo WEB_PATH."/my-deals.php";?>";
	//alert(redirect_url);
        var th_link=data_array[5];
        //alert(th_link);
        var imgsrc=data_array[6];
        //alert(imgsrc);
        var location_address=data_array[7];
        //alert(location_address);
        var sharepoint=data_array[8];
        //alert(sharepoint);
        
        var campaign_tag_temp=data_array[9];
        var campaign_tag_array=campaign_tag_temp.split(',');
        var tag_main_all="";
        if(campaign_tag_temp != "" || typeof(campaign_tag_temp)!="undefined")
        {
            if(campaign_tag_array[0] == "" || typeof(campaign_tag_array[0])=="undefined")
            {

            }
            else
                {
                    tag_main_all += "#"+campaign_tag_array[0]+" ";
                }
            if(campaign_tag_array[1] == "" || typeof(campaign_tag_array[1])=="undefined")
            {

            }
            else
                {
                    tag_main_all += "#"+campaign_tag_array[1]+" ";
                }
            if(campaign_tag_array[2] == "" || typeof(campaign_tag_array[2])=="undefined")
            {

            }    
            else
                {
                    tag_main_all += "#"+campaign_tag_array[2]+" ";
                }
        }
     
    
     var campaign_tag=tag_main_all;
    
    

        
        
        
        var cust_id="<?php echo $_SESSION['customer_id'];?>";
   //alert(cust_id);
    
    
   
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
                                data :'campaign_title='+encodeURIComponent(campaign_title)+'&summary='+summary+'&th_link='+encodeURIComponent(th_link)+'&imgsrc='+imgsrc+'&customer_id='+cust_id+'&campaign_id='+campaign_id+'&l_id='+l_id+'&campaign_tag='+campaign_tag+'&bussiness_name='+bussiness_name+'&location_address='+location_address+'&sharepoint='+sharepoint+'&redirect_url='+redirect_url,
                               // async:false,
                                success:function(msg)
                                {
                                   //alert(msg);
								   //close_popuploader('Notificationloader');
								   jQuery(".share_loader").css("display","none");
                                   var error_array=jQuery.trim(msg).split('|');
                                   
                                    if(error_array[0] == "OAuthException")
                                        {
                                            
                                          
                                               var msg="<?php echo $client_msg["common"]["Msg_sharing_authorize"];?>";
                                                               var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='Yes' id='"+campaign_id+"_"+l_id+"' data='"+escape(encodeURIComponent(original_campaign_title))+"###"+escape(encodeURIComponent(summary))+"###"+encodeURIComponent(th_link)+"###"+imgsrc+"###"+cust_id+"###"+campaign_id+"###"+l_id+"###"+campaign_tag+"###"+bussiness_name+"###"+location_address+"###"+sharepoint+"###"+encodeURIComponent(redirect_url)+"'  class='tokenyes' name='tokenyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'><input type='button'  value='No' id='"+campaign_id+"_"+l_id+"'  class='tokenno' name='tokenno' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
                                        else if(error_array[0] == "GraphMethodException")
                                        {
                                        
                                        
                                                
                                                window.location.href = error_array[1];
                                        }
                                        else if(error_array[0] == "error")
                                        {
                                                var msg="<?php echo $client_msg["common"]["Msg_sharing_authorize"];?>";
                                                               var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='Yes' id='"+campaign_id+"_"+l_id+"' redirect='"+error_array[1]+"###"+error_array[0]+"' class='facebooktokenyes' name='facebooktokenyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'><input type='button'  value='No' id='"+campaign_id+"_"+l_id+"'  class='tokenno' name='tokenno' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
														data :'btn_increment_share_counter=yes&customer_id='+cust_id+'&reffer_campaign_id='+campaign_id+'&refferal_location_id='+l_id+'&domain=1&medium='+jQuery("#medium").val()+'&timestamp='+formattedDate(),
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
                                                //var success_array=jQuery.trim(msg).split('-');
                                                
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

jQuery(".fancybox-inner .tokenyes").live("click",function(event){
event.stopPropagation();event.preventDefault();
    
     var data_array=jQuery(this).attr('data').split('###');
     
   var campaign_title=unescape(decodeURIComponent("Scanflip Offer - "+data_array[0]));
        
	var summary=unescape(decodeURIComponent(data_array[1]));
	
      var redirect_url="<?php echo WEB_PATH."/my-deals.php";?>";
	var th_link=decodeURIComponent(data_array[2]);

       
    
    var imgsrc=data_array[3];
   
    var facebook_user_id="<?php echo $RS_user_data->fields['facebook_user_id']; ?>";
        
    
    var campaign_id=data_array[5];
    
    var l_id=data_array[6];
   
    var campaign_tag=data_array[7];;
   
   var cust_id="<?php echo $_SESSION['customer_id'];?>";
  
    var bussiness_name= data_array[8];
  
    var location_address=data_array[9];
  
    var sharepoint=data_array[10];
    
                jQuery.ajax({
                                                            type:"POST",
                                                            url:'<?php echo WEB_PATH;?>/facebook_share_reassign.php',
                                                            data :'facebook_user_id='+facebook_user_id+'&customer_id='+cust_id,
                                                            success:function(msg)
                                                            {
                                                               
                                                                jQuery.ajax({
                                                                      type:"POST",
                                                                      url:'<?php echo WEB_PATH;?>/facebook_share.php',
                                                                      data :'campaign_title='+encodeURIComponent(campaign_title)+'&summary='+summary+'&th_link='+encodeURIComponent(th_link)+'&imgsrc='+imgsrc+'&customer_id='+cust_id+'&campaign_id='+campaign_id+'&l_id='+l_id+'&campaign_tag='+campaign_tag+'&bussiness_name='+bussiness_name+'&location_address='+location_address+'&sharepoint='+sharepoint+'&redirect_url='+redirect_url,
                                                                      // async:false,
                                                                      success:function(msg)
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

jQuery(".fancybox-inner .facebooktokenyes").live("click",function(event){
event.stopPropagation();event.preventDefault();
        var data_redirect_array=jQuery(this).attr('redirect').split('###');
    if(data_redirect_array[1] == "error")
        {
             window.location.href=data_redirect_array[0];
        }
                   
});



/*
jQuery(".fancybox-inner .tokenno").live("click",function(){
        jQuery.fancybox.close();
        var data_array=jQuery(this).attr('data').split('|');
        //var campaign_title=escape(encodeURIComponent(data_array[0] + " " + data_array[5]));
   var campaign_title=escape(encodeURIComponent(data_array[0]));
        alert(campaign_title);
        alert(unescape(decodeURIComponent(campaign_title)));
	var summary=data_array[1];
        //alert(summary); 
	var redirect_url="<?php echo WEB_PATH."/my-deals.php";?>";
	var th_link=data_array[2];
        alert(th_link);
    var imgsrc=data_array[3];
    
    alert(imgsrc);
    var facebook_user_id="<?php echo $RS_user_data->fields['facebook_user_id']; ?>";
        
    
    var campaign_id=data_array[5];
    //alert(campaign_id);
    var l_id=data_array[6];
    //alert(l_id);
    var campaign_tag="<?php if(isset($tag_main)){echo $tag_main;}?>";
    
   var cust_id="<?php echo $_SESSION['customer_id'];?>";
   
    var bussiness_name= data_array[8];
    //alert(bussiness_name);
    var location_address=data_array[9];
    //alert(location_address);
    var sharepoint=data_array[10];
    //alert(sharepoint);
           urlpath='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - '+decodeURIComponent(campaign_title)+'&p[url]='+encodeURIComponent(th_link)+'&p[images][0]='+imgsrc;
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
        
        
});

*/
//Twitter Share code

$(".fancybox-inner .twitter_link").live("click",function(event){
event.stopPropagation();event.preventDefault();
	//open_popuploader('Notificationloader');
		jQuery(".share_loader").css("display","block");
    var data_array=jQuery(this).attr('data').split('###');
   var campaign_id=data_array[0];
   var l_id=data_array[1];
   var bussiness_name= data_array[2];
   
        var campaign_title='Scanflip Offer -'+data_array[3];
        var summary=data_array[4];
        //alert(summary);
    var th_link=data_array[5];
    var imgsrc=data_array[6];
     
    var location_address=data_array[7];
    var sharepoint=data_array[8];
   
    var campaign_tag_temp=data_array[9];
        var campaign_tag_array=campaign_tag_temp.split(',');
        var tag_main_all="";
        if(campaign_tag_temp != "" || typeof(campaign_tag_temp)!="undefined")
        {
            if(campaign_tag_array[0] == "" || typeof(campaign_tag_array[0])=="undefined")
            {

            }
            else
                {
                    tag_main_all += "#"+campaign_tag_array[0]+" ";
                }
            if(campaign_tag_array[1] == "" || typeof(campaign_tag_array[1])=="undefined")
            {

            }
            else
                {
                    tag_main_all += "#"+campaign_tag_array[1]+" ";
                }
            if(campaign_tag_array[2] == "" || typeof(campaign_tag_array[2])=="undefined")
            {

            }    
            else
                {
                    tag_main_all += "#"+campaign_tag_array[2]+" ";
                }
        }
     
    
     var campaign_tag=tag_main_all;
   var redirect_url="<?php echo WEB_PATH."/my-deals.php";?>";
    var cust_id="<?php echo $_SESSION['customer_id'];?>";
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
                                data :'campaign_title='+encodeURIComponent(campaign_title)+'&summary='+summary+'&th_link='+th_link+'&imgsrc='+imgsrc+'&customer_id='+cust_id+'&campaign_id='+campaign_id+'&l_id='+l_id+'&campaign_tag='+campaign_tag+'&redirect_url='+redirect_url,
                               // async:false,
                                success:function(msg)
                                {
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
														data :'btn_increment_share_counter=yes&customer_id='+cust_id+'&reffer_campaign_id='+campaign_id+'&refferal_location_id='+l_id+'&domain=2&medium='+jQuery("#medium").val()+'&timestamp='+formattedDate(),
													   // async:false,
														success:function(msg)
														{
														}
													});
													/* 15-03-2014 share counter */
                                            }
                                            //var reautho_arr = msg.split("|");
                                            if(jQuery.trim(email_arr[0]) == "reautho")
                                            {
                                                                var msg="<?php echo $client_msg["common"]["Msg_sharing_authorize"];?>";
                                                                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                                                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
                                                                var footer_msg="<div style='text-align:center'><hr><input type='button'  value='Yes' id='"+campaign_id+"_"+l_id+"' data='"+escape(encodeURIComponent(campaign_title))+"###"+encodeURIComponent(th_link)+"###"+cust_id+"###"+campaign_id+"###"+l_id+"###"+campaign_tag+"###"+encodeURIComponent(redirect_url)+"'  class='twittertokenyes' name='twittertokenyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'><input type='button'  value='No' id='"+campaign_id+"_"+l_id+"'  class='twittertokenno' name='tokenno' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
jQuery(".fancybox-inner .twittertokenyes").live("click",function(event){
event.stopPropagation();event.preventDefault();
    
     var data_array=jQuery(this).attr('data').split('###');
     //var data_array=jQuery(this).attr('data');
     var campaign_title=unescape(decodeURIComponent(data_array[0]));
       var th_link=data_array[1];
        var cust_id="<?php echo $_SESSION['customer_id'];?>";
        var campaign_id=data_array[3];
        var l_id=data_array[4];
        var campaign_tag=data_array[5];
        
        var redirect_url="<?php echo WEB_PATH."/my-deals.php";?>";
	jQuery.ajax({
                                                            type:"POST",
                                                            url:'<?php echo WEB_PATH;?>/twitteroauth/twitter_login.php',
                                                            data :'campaign_title='+campaign_title+'&th_link='+th_link+'&customer_id='+cust_id+'&campaign_id='+campaign_id+'&l_id='+l_id+'&campaign_tag='+campaign_tag+'&redirect_url='+redirect_url,
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
/*
jQuery(".fancybox-inner .twittertokenno").live("click",function(){
        jQuery.fancybox.close();
        
        var data_array=jQuery(this).attr('data').split('|');
        var camp_loc_id=jQuery(this).attr('id');
        var campaign_title=escape(encodeURIComponent(data_array[0] + " " + data_array[5]));
       	var th_link=data_array[1];
        var campaign_id=data_array[3];
        //alert(campaign_id);
        var l_id=data_array[4];
        //alert(l_id);
        var campaign_tag=data_array[5];
        
        var cust_id="<?php echo $_SESSION['customer_id'];?>";
        var webpath="<?php echo WEB_PATH; ?>";
          urlpath="https://twitter.com/intent/tweet?original_referer="+webpath+"&text="+decodeURIComponent(campaign_title)+' '+ "&tw_p=tweetbutton&url="+encodeURIComponent(th_link);
	//urlpath="https://twitter.com/intent/tweet?original_referer="+webpath+"&text="+unescape(campaign_title)+' '+th_link+' '+hastag+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link);					 
            var open_popup=window.open(urlpath,'_blank','width=500,height=550');
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
        
        
});

*/
function google_sharing(camid,l_id,share,cust_id)
{
//alert(camid+" and "+ l_id);
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
jQuery(".fancybox-inner #sharignpopup").live("click",function(event){
event.stopPropagation();event.preventDefault();
        jQuery.fancybox.close();
});
jQuery(".fancybox-inner #popupcancel").live("click",function(event){
event.stopPropagation();event.preventDefault();
        jQuery.fancybox.close();
});
jQuery(".fancybox-inner .tokenno").live("click",function(event){
event.stopPropagation();event.preventDefault();
        jQuery.fancybox.close();
});
jQuery(".fancybox-inner .twittertokenno").live("click",function(event){
event.stopPropagation();event.preventDefault();
        jQuery.fancybox.close();
});
</script>
<!-- start category slider 05 12 2013 -->

<!--<script type="text/javascript" src="<?php echo WEB_PATH ?>/js/jquery.jscrollpane.min.js"></script>	-->
<script>
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
 // some code.. 
 document.write('<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/jquery.jscrollpane.min.js"><\/script>');
}
else{
     document.write('<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/newjscrollpane.js"><\/script>');
}
</script>	
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/jquery.mousewheel.js"></script>
<!-- end category slider 05 12 2013 -->

<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS?>/c/jquery.carouFredSel-6.2.1-packed.js"></script>
<script type="text/javascript">
bind_location_tool_effect();
function bind_location_tool_effect()
{

jQuery(".call_location_details").click(function(event){
event.stopPropagation();event.preventDefault();
    $(window).scrollTop(0);
	jQuery("body").addClass("add-over-hidden");
	
	/*
	popup_name = 'Notificationloader';
	$("#" + popup_name + "FrontDivProcessing").css("display","block");
	$("#" + popup_name + "PopUpContainer").css("display","block");
	$("#" + popup_name + "BackDiv").css("display","block");
	*/
	open_loader();				
	var location_id=jQuery(this).parent().parent().attr("locid");
	
	var businessname=jQuery(this).parent().parent().find(".loca_name a").text();
	var address=jQuery(this).parent().parent().find(".loca_address").text();
	
	jQuery.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'get_location_details_all_from_ajax=yes&location_id='+location_id+'&business='+businessname+'&address='+address,
          async:true,
		  success:function(msg)
		  {
				var obj = jQuery.parseJSON(msg);
                    
                if (obj.status=="true") 
				{
					/*
					jQuery(".location_fancybox").html(obj.locationdetailhtml);
					jQuery.fancybox({
						content:jQuery('.location_fancybox').html(),
						width: 435,
						height: 345,
						type: 'html',
						openEffect : 'elastic',
						openSpeed  : 300, 
						closeEffect : 'elastic',
						closeSpeed  : 300,
						// topRatio: 0,
						changeFade : 'fast',  
						helpers: {
							overlay: {
							opacity: 0.3
							} // overlay
						}				
                    });
					*/
					jQuery("#LocationFancymainContainer").html(obj.all_html);
					
					//alert(jQuery("#pricerange_text").text());
					var pricerange_text=jQuery("#pricerange_text").text();
					
					if(pricerange_text=="Inexpensive")
					{
						jQuery("#pricerange_text").text("Inexpensive ($)");
					}
					else if(pricerange_text=="Moderate")
					{
						jQuery("#pricerange_text").text("Moderate ($$)");
					}
					else if(pricerange_text=="Expensive")
					{
						jQuery("#pricerange_text").text("Expensive ($$$)");
					}
					else if(pricerange_text=="Very Expensive")
					{
						jQuery("#pricerange_text").text("Very Expensive ($$$$)");
					}
					
					// start visitor rating
					
					var locid=location_id;
					
					jQuery.ajax({
							type:"POST",
							url:'<?php echo WEB_PATH; ?>/rating_trend_chart.php',
							async:false,
							data :'locid='+locid,
							success:function(msg)
							{
								obj = jQuery.parseJSON(msg);
								//alert(obj.rating_info);
								all_rating = obj.rating_info;
								//alert(obj.status);
								st_yr = obj.start_year;
								st_mnt = obj.start_month;
								visitor_detail = obj.visitor_detail;
								ratings = obj.rating_values;
								max_rating = obj.max_rating;
								max_rating_heading = obj.max_rating_heading;

								if(max_rating!=0)
								{
									jQuery(".no_rating_found").css("display","none");
									
									jQuery("#visitorrating_"+locid+" .excellent").parent().next().text(ratings[4]);
									jQuery("#visitorrating_"+locid+" .verygood").parent().next().text(ratings[3]);
									jQuery("#visitorrating_"+locid+" .good").parent().next().text(ratings[2]);
									jQuery("#visitorrating_"+locid+" .fair").parent().next().text(ratings[1]);
									jQuery("#visitorrating_"+locid+" .poor").parent().next().text(ratings[0]);
									
									jQuery("#visitorrating_"+locid+" .excellent").animate({
									width:visitor_detail[4]+"%" }, 500, function() {
									});
									jQuery("#visitorrating_"+locid+" .verygood").animate({
									width:visitor_detail[3]+"%" }, 500, function() {
									});
									jQuery("#visitorrating_"+locid+" .good").animate({
									width:visitor_detail[2]+"%" }, 500, function() {
									});
									jQuery("#visitorrating_"+locid+" .fair").animate({
									width:visitor_detail[1]+"%" }, 500, function() {
									});
									jQuery("#visitorrating_"+locid+" .poor").animate({
									width:visitor_detail[0]+"%" }, 500, function() {
									});

									// end visitor rating
									
									// start rating trend
									
									if(all_rating!="") // to solve blank rating trend chart problem
									{
										var test = "["+all_rating+"]";
									
										jQuery('#container_'+locid).attr("rating_trend_data",test);
									}
									else
									{
										jQuery('#container_'+locid).html("<img class='notenoughdata' src='<?php echo ASSETS_IMG; ?>/c/not_enough_data.png'/>");	
									}
									
									
									// end rating trend
								}
								else
								{
									jQuery(".location_popup_rating").css("display","none");
								}
								/*** intializing tab links for ipad ****/
								var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
								if(iOS)
								{
									jQuery("#tab-type a").bind('touchstart', function(e){
																//e.stopPropagation(); 
										if(jQuery("#hdn_istouched").val() == "0")
										{
											jQuery(this).trigger("click");
											jQuery("#hdn_istouched").val("1");
										}
									});
								}
									
						   }
					});
					
					
					open_popup('LocationFancy');
				}
				/*
				$("#" + popup_name + "FrontDivProcessing").css("display","none");
				$("#" + popup_name + "PopUpContainer").css("display","none");
				$("#" + popup_name + "BackDiv").css("display","none");
				*/
				close_loader();
				open_popup('LocationFancy');
		  }
	});

});
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
jQuery(".loca_total_offers span").live("click",function(event){
event.stopPropagation();
jQuery("#hdn_istouched").val("0");
var loginstatus = true;
var locid=jQuery(this).parent().parent().parent().attr("locid");
/* check whether user login or not and restore previous section */

//if(jQuery(".subscribestore").length != 0 )
//{
$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'load_previous_offers_left_section=true&location_id='+locid,
          async:false,
		  success:function(msg)
		  {
                      var obj = jQuery.parseJSON(msg);
                       if(obj.status == "false")
                       {
							loginstatus = false;
                          parent.window.location.href= obj.link;
						  return false;
                       }
                       else
                       {
						loginstatus = true;
					   }
		   }
	});
//}
if(!loginstatus)
{
	return false;
}
else
{
	//return true;
}
var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
/* check whether user login or not and restore previous section */
jQuery(".searchdeal_offers").css("display","block");
//alert("in mouse click");
jQuery(".flip_map_loc").css("display","none");
//jQuery(this).css("color","orange");
	try
	{
		jQuery(".location_tool").each(function(){
			jQuery(this).removeClass("current_loc");
			jQuery(this).find(".flip_map_loc").css("display","none");
			jQuery(this).find(".loca_total_offers span").css("display","block");
		});
		
		jQuery(".flip_map").css("display","block");
		//var locid=jQuery(this).attr("locid");
		var locid=jQuery(this).parent().parent().parent().attr("locid");

		//alert(locid+"inn filter location");
		var selected_location_element = jQuery(this).parent().parent().parent();
		selected_location_element.addClass("current_loc");
		//alert(selected_location_element.attr("class"));
		//alert(selected_location_element.attr("miles")+"=="+selected_location_element.attr("locid"));
		var selected_cat_id=getCookie("cat_remember");
		var miles_cookie=getCookie("miles_cookie");
		<?php 
		if(isset($_SESSION['customer_id']))
		{
		?>
			var customer_id = "<?php echo $_SESSION['customer_id']; ?>";
		<?php 	
		}
		else
		{
		?>
			var customer_id = "";
		<?php
		}
		?>
		/*var mlat="<?php echo $_COOKIE['mycurrent_lati'] ?>";
		var mlng="<?php echo $_COOKIE['mycurrent_long'] ?>"; */
		//alert(mlat+"=="+mlng);
		var mlat=getCookie('mycurrent_lati');
		var mlng=getCookie('mycurrent_long');
		//alert(mlat+"=="+mlng);
		/*
		alert("location_id="+locid);
		alert("category_id="+selected_cat_id);
		alert("miles="+miles_cookie);
		alert("mlatitude="+mlat);
		alert("mlongitude="+mlng);
		alert("customer_id="+customer_id);
		*/
		//alert(jQuery("#hdn_is_offer_div_click").val());

var is_expiringtoday =  jQuery("#hdn_is_expiring_today").val();
var is_new =  jQuery("#hdn_is_new_campaign").val();
var is_discount = jQuery("#hdn_discount").val();
		if(jQuery(".searchdeal_offers #campaignlis_"+locid).length == 0)
		{
	//	alert("<?=WEB_PATH?>/process.php?btnGetSavedOfferForLocation=yes&category_id=0&location_id=" + locid +"&dismile=50&mlatitude="+mlat+"&mlongitude="+mlng+"&customer_id="+customer_id);
		//open_popuploader('Notificationloader');
		jQuery(".location_tool[locid='"+locid+"'] .offer_loader").css("display","block");
		//jQuery(".table_loader").css("display","block");
					jQuery("#imagek").css("opacity","0.1");
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/process.php",
			data: "btnGetSavedOfferForLocation=yes&category_id=0&location_id=" + locid +"&dismile=50&mlatitude="+mlat+"&mlongitude="+mlng+"&customer_id="+customer_id,
			async:false,
			success: function(msg) 
			{

				var obj=jQuery.parseJSON(msg);
				//alert(obj.status);
				//alert(obj.DetailHtml);
				if(obj.loginstatus == "false")
				{
					 parent.window.location.href=obj.link;
				}
				else
				{
				if(obj.status=="false")
				{
					//flag="false";
					//alert_msg +="<div>* "+ obj.message +"</div>";
				}
				else
				{
					jQuery(".searchdeal_offers .campaignlist").each(function(){
						jQuery(this).css("display","none");
					});
					jQuery(".searchdeal_offers").append("<div style='display:none' class='campaignlist' camp_locid='"+locid+"' id='campaignlis_"+locid+"' camp_catid='"+selected_cat_id+"' camp_miles='"+miles_cookie+"' >"+obj.DetailHtml+"</div>");
					//jQuery(".searchdeal_offers_copy").append("<div style='display:block' class='campaignlist' camp_locid='"+locid+"' id='campaignlist_"+locid+"' camp_catid='"+selected_cat_id+"' camp_miles='"+miles_cookie+"' >"+obj.DetailHtml+"</div>");
					jQuery("#campaignlis_"+locid).css("display","block");
				//	jQuery(".searchdeal_offers #campaignlis_"+locid).html("")	;
				}
				/*main_element.css("color","blue");
		jQuery(".location_tool").each(function(){
			jQuery(this).find(".loca_total_offers span").removeClass("current_offer");
		}); */
		}
				
			}
		});
		}
		else{
	/*	main_element.css("color","blue");
		jQuery(".location_tool").each(function(){
				jQuery(this).find(".loca_total_offers span").removeClass("current_offer");
		});*/
		jQuery(".searchdeal_offers .campaignlist").each(function(){
						jQuery(this).css("display","none");
					});
		jQuery("#campaignlis_"+locid).css("display","block");	
			//jQuery(".searchdeal_offers #campaignlis_"+locid).html("")	;
			
		} 
	
		if(selected_cat_id != 0)
					{
				//	alert("In selected category");
					jQuery("#campaignlis_"+locid+" .deal_blk").each(function(){
						if(jQuery(this).attr("is_expire")  != 1 && is_expiringtoday== 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
						
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("is_new")  != 1 && is_new == 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("discount_val")  != is_discount && is_discount != "" ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else
						{
						if(jQuery(this).attr("catid") == selected_cat_id)
						{
						jQuery(this).parent().attr("high","yes");
						jQuery(this).parent().removeClass("hide_deal_blk");
						jQuery(this).parent().addClass("show_deal_blk");
						
							jQuery(this).parent().css("display","block");
							
							//jQuery(".searchdeal_offers #campaignlis_"+locid).("")	;
						}
						else{
						jQuery(this).parent().attr("high","no");
						jQuery(this).parent().removeClass("show_deal_blk");
						jQuery(this).parent().addClass("hide_deal_blk");
						
							jQuery(this).parent().css("display","none");
							
						}
						}
					
					});
					}
					else
					{
						jQuery("#campaignlis_"+locid+" .deal_blk").each(function(){
						if(jQuery(this).attr("is_expire")  != 1 && is_expiringtoday== 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
						
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("is_new")  != 1 && is_new == 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("discount_val")  != is_discount && is_discount != "" )  
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else
						{
							//jQuery("#campaignlis_"+locid+" .deal_blk").each(function(){
							
							jQuery(this).parent().removeClass("hide_deal_blk");
							jQuery(this).parent().addClass("show_deal_blk");
							
							jQuery(this).parent().attr("high","yes");
								jQuery(this).parent().css("display","block");
								
							//});
						}
					
					});
						
					
					}
					//$('.carousel-inner > .item .carousel-caption').css('display','none');
					if(jQuery("#hdn_is_offer_div_click").val()== 1)
					{
						jQuery(this).css("display","none");
						jQuery(".location_tool[locid="+locid+"] .flip_map_loc").css("display","block");
						//evt.preventDefault();
						$('.slider-carriage').stop(false, false).animate({
							left: (-100 * $('#' + $(this).parent().data('title')).position().left / $('.slider-viewport').width()) + '%'
						}, 500);
					}
					else
					{
						if(iOS)
						{
									/*jQuery(".deal_title").each(function(){
															jQuery(this).attr("onmouseover",jQuery(this).attr("onClick"));
															alert(jQuery(this).attr("onmouseover"));
															jQuery(this).attr("onClick","");
														}); */
												jQuery(".next1").bind('touchstart', function(e){
												//alert("touch");
															//e.stopPropagation(); 
															if(jQuery("#hdn_istouched").val() == "0")
															{
																jQuery(".next1").trigger("click");
																jQuery("#hdn_istouched").val("1");
															}
														});
														jQuery(".next1").bind('touchend', function(e){
															//alert("touched");
														});
														jQuery(".prev1").bind('touchstart', function(e){
															//e.stopPropagation(); 
															if(jQuery("#hdn_istouched").val() == "0")
															{
																jQuery(".prev1").trigger("click");
																jQuery("#hdn_istouched").val("1");
															}
														});
														jQuery(".prev1").bind('touchend', function(e){
															//alert("touched");
														});
														jQuery(".campaignlist .view-reserve").bind('touchstart', function(e){
														//alert("touch");
															//e.stopPropagation(); 
															if(jQuery("#hdn_istouched").val() == "0")
															{
																jQuery(this).trigger("click");
																jQuery("#hdn_istouched").val("1");
															}
														});
														}
						
					}
					/**** for view and reserve button bind click event ********/
					if(iOS)
					{
														//jQuery(".campaignlist .view-reserve").
														
														jQuery(".next").bind('touchstart', function(e){
															//e.stopPropagation(); 
															if(jQuery("#hdn_istouched").val() == "0")
															{
																jQuery(".next").trigger("click");
																jQuery("#hdn_istouched").val("1");
															}
														});
														jQuery(".next").bind('touchend', function(e){
															//alert("touched");
														});
														jQuery(".prev").bind('touchstart', function(e){
															//e.stopPropagation(); 
															if(jQuery("#hdn_istouched").val() == "0")
															{
																jQuery(".prev").trigger("click");
																jQuery("#hdn_istouched").val("1");
															}
														});
														jQuery(".prev").bind('touchend', function(e){
															//alert("touched");
														});
					}
					/**** for view and reserve button bind click event ********/
					var content = jQuery('#deal_slider_'+locid).html();
					jQuery('#deal_slider_'+locid).html("");
					jQuery('#deal_slider_'+locid).html(content);
					
					//alert("li count="+jQuery("ul[id='deal_slider_"+locid+"'] li[class='show_deal_blk']").length);
					
					/*
					jQuery("ul[id='deal_slider_"+locid+"'] li[class='show_deal_blk']").each(function() {
						alert(jQuery(this).attr("high"));
						alert(jQuery(this).html());
					});
					*/
                                        
                                        jQuery('#campaignlis_'+locid).find("#prev1").css("display","none");
                                        jQuery('#campaignlis_'+locid).find("#next1").css("display","none");
                                        var li_length=jQuery("ul[id='deal_slider_"+locid+"'] li[class='show_deal_blk']").length;
										//jQuery('#deal_slider_'+locid).css("width","530px");
                                        //jQuery('#deal_slider_'+locid).css("height","410px");
										//if(li_length > 2)
                                        //{
                                                
                                                  //	jQuery('#deal_slider_'+locid).css("width","530px");
													//jQuery('#deal_slider_'+locid).css("height","410px");         
                                                         jQuery('#deal_slider_'+locid).css("overflow","hidden");
                                                       jQuery('#deal_slider_'+locid).css("overflow","hidden");
                                                       
                                                 //      setTimeout(function(){
                                                       //jQuery("#imagek").css("width","600px");

                                               //	jQuery('#deal_slider_'+locid).css("width","530px");
                                                       //jQuery('#campaignlis_'+locid+' #deal_slider_'+locid).carouFredSel({
                                                      jQuery('#deal_slider_'+locid).carouFredSel({
                                                       //responsive:true,
																width:'100%',
                                                               circular: false, 
                                                               auto: false,
                                                               prev: '#'+locid+'prev1',
                                                               next: '#'+locid+'next1',
                                                               pagination: "#pager2",
                                                               mousewheel: false,
                                                             //  height : 375,
                                                            //   width:530,
                                                               align:"center",
                                                               swipe: {
                                                                               onMouse: true,
                                                                               onTouch: true
                                                               }
                                                       }); 
                                            //   },1000);
										/*	
                                        }
                                        else{
                                          			jQuery('#campaignlis_'+locid+' .list_carousel1').css("overflow","inherit");
                                                    jQuery('#campaignlis_'+locid).find("#"+locid+"prev1").css("display","none");
                                                    jQuery('#campaignlis_'+locid).find("#"+locid+"next1").css("display","none");

                                           
                                        }
										*/
					//jQuery('#campaignlis_'+locid).css("display","block");
					jQuery('#campaignlis_'+locid+' .list_carousel1').css("overflow","inherit");
					jQuery("#hdn_is_offer_div_click").val("1");
					// close_popuploader('Notificationloader'); 
					jQuery(".location_tool[locid='"+locid+"'] .offer_loader").css("display","none");
		//jQuery(".table_loader").css("display","block");
					jQuery("#imagek").css("opacity","1");
					 //					
	}
	catch(e)
	{
		alert(e);
	}
//	 setTimeout(function(){
	
	//},200);
	setTimeout(function() {
		if(jQuery(".strip").length > 0)
		{
			var ele_strip = jQuery(".strip");
			ele_strip.slideUp('300');
		}
		if(jQuery(".strip_grid").length > 0)
		{
			var ele_strip = jQuery(".strip_grid");
			ele_strip.slideUp('300');
		}
		
    }, 5000);
});
}
else
{
    jQuery(".location_tool .loca_total_offers span").hover(
    function(){
            var main_element = jQuery(this);
            jQuery(this).addClass("current_offer");
    },
    function(){
    //alert("in mouse up");
    //jQuery(this).css("color","blue");
                    jQuery(".location_tool").each(function(){
                            jQuery(this).find(".loca_total_offers span").removeClass("current_offer");
                    });
    });
    jQuery(".location_tool").on("click",function(){

	jQuery(".location_tool").each(function(){
			jQuery(this).removeClass("current_loc");
	});
	jQuery(this).addClass("current_loc");
	
	/* display marker */
	jQuery(".location_tool[locid='"+jQuery(this).attr("locid")+"']").trigger("mouseenter");
	/* display marker */
	jQuery(".flip_map").trigger("click");
	
})
.on("click",".loca_total_offers span",function(event){
event.stopPropagation();
var loginstatus = true;
var locid=jQuery(this).parent().parent().parent().attr("locid");
/* check whether user login or not and restore previous section */

//if(jQuery(".subscribestore").length != 0 )
//{
$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'load_previous_offers_left_section=true&location_id='+locid,
          async:false,
		  success:function(msg)
		  {
                      var obj = jQuery.parseJSON(msg);
                       if(obj.status == "false")
                       {
							loginstatus = false;
                          parent.window.location.href= obj.link;
						  return false;
                       }
                       else
                       {
						loginstatus = true;
					   }
		   }
	});
//}
if(!loginstatus)
{
	return false;
}
else
{
	//return true;
}
/* check whether user login or not and restore previous section */
jQuery(".searchdeal_offers").css("display","block");
//alert("in mouse click");
jQuery(".flip_map_loc").css("display","none");
//jQuery(this).css("color","orange");
	try
	{
		jQuery(".location_tool").each(function(){
			jQuery(this).removeClass("current_loc");
			jQuery(this).find(".flip_map_loc").css("display","none");
			jQuery(this).find(".loca_total_offers span").css("display","block");
		});
		
		jQuery(".flip_map").css("display","block");
		//var locid=jQuery(this).attr("locid");
		var locid=jQuery(this).parent().parent().parent().attr("locid");

		//alert(locid+"inn filter location");
		var selected_location_element = jQuery(this).parent().parent().parent();
		selected_location_element.addClass("current_loc");
		//alert(selected_location_element.attr("class"));
		//alert(selected_location_element.attr("miles")+"=="+selected_location_element.attr("locid"));
		var selected_cat_id=getCookie("cat_remember");
		var miles_cookie=getCookie("miles_cookie");
		<?php 
		if(isset($_SESSION['customer_id']))
		{
		?>
			var customer_id = "<?php echo $_SESSION['customer_id']; ?>";
		<?php 	
		}
		else
		{
		?>
			var customer_id = "";
		<?php
		}
		?>
		/*var mlat="<?php echo $_COOKIE['mycurrent_lati'] ?>";
		var mlng="<?php echo $_COOKIE['mycurrent_long'] ?>"; */
		//alert(mlat+"=="+mlng);
		var mlat=getCookie('mycurrent_lati');
		var mlng=getCookie('mycurrent_long');
		//alert(mlat+"=="+mlng);
		/*
		alert("location_id="+locid);
		alert("category_id="+selected_cat_id);
		alert("miles="+miles_cookie);
		alert("mlatitude="+mlat);
		alert("mlongitude="+mlng);
		alert("customer_id="+customer_id);
		*/
		//alert(jQuery("#hdn_is_offer_div_click").val());


var is_expiringtoday =  jQuery("#hdn_is_expiring_today").val();
var is_new =  jQuery("#hdn_is_new_campaign").val();
var is_discount = jQuery("#hdn_discount").val();
		if(jQuery(".searchdeal_offers #campaignlis_"+locid).length == 0)
		{
	//	alert("<?=WEB_PATH?>/process.php?btnGetSavedOfferForLocation=yes&category_id=0&location_id=" + locid +"&dismile=50&mlatitude="+mlat+"&mlongitude="+mlng+"&customer_id="+customer_id);
		//open_popuploader('Notificationloader');
		jQuery(".location_tool[locid='"+locid+"'] .offer_loader").css("display","block");
		//jQuery(".table_loader").css("display","block");
		jQuery("#imagek").css("opacity","0.1");
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/process.php",
			data: "btnGetSavedOfferForLocation=yes&category_id=0&location_id=" + locid +"&dismile=50&mlatitude="+mlat+"&mlongitude="+mlng+"&customer_id="+customer_id,
			async:false,
			success: function(msg) 
			{

				var obj=jQuery.parseJSON(msg);
				//alert(obj.status);
				//alert(obj.DetailHtml);
				if(obj.loginstatus == "false")
				{
					 parent.window.location.href=obj.link;
				}
				else
				{
				if(obj.status=="false")
				{
					//flag="false";
					//alert_msg +="<div>* "+ obj.message +"</div>";
				}
				else
				{
					jQuery(".searchdeal_offers .campaignlist").each(function(){
						jQuery(this).css("display","none");
					});
					jQuery(".searchdeal_offers").append("<div style='display:none' class='campaignlist' camp_locid='"+locid+"' id='campaignlis_"+locid+"' camp_catid='"+selected_cat_id+"' camp_miles='"+miles_cookie+"' >"+obj.DetailHtml+"</div>");
					//jQuery(".searchdeal_offers_copy").append("<div style='display:block' class='campaignlist' camp_locid='"+locid+"' id='campaignlist_"+locid+"' camp_catid='"+selected_cat_id+"' camp_miles='"+miles_cookie+"' >"+obj.DetailHtml+"</div>");
					jQuery("#campaignlis_"+locid).css("display","block");
				//	jQuery(".searchdeal_offers #campaignlis_"+locid).html("")	;
				}
				/*main_element.css("color","blue");
		jQuery(".location_tool").each(function(){
			jQuery(this).find(".loca_total_offers span").removeClass("current_offer");
		}); */
		}
				
			}
		});
		}
		else{
	/*	main_element.css("color","blue");
		jQuery(".location_tool").each(function(){
				jQuery(this).find(".loca_total_offers span").removeClass("current_offer");
		});*/
		jQuery(".searchdeal_offers .campaignlist").each(function(){
						jQuery(this).css("display","none");
					});
		jQuery("#campaignlis_"+locid).css("display","block");	
			//jQuery(".searchdeal_offers #campaignlis_"+locid).html("")	;
			
		} 
	
		if(selected_cat_id != 0)
					{
				//	alert("In selected category");
					jQuery("#campaignlis_"+locid+" .deal_blk").each(function(){
						if(jQuery(this).attr("is_expire")  != 1 && is_expiringtoday== 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
						
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("is_new")  != 1 && is_new == 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("discount_val")  != is_discount && is_discount != "" )  
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else
						{
						if(jQuery(this).attr("catid") == selected_cat_id)
						{
						jQuery(this).parent().attr("high","yes");
						jQuery(this).parent().removeClass("hide_deal_blk");
						jQuery(this).parent().addClass("show_deal_blk");
						
							jQuery(this).parent().css("display","block");
							
							//jQuery(".searchdeal_offers #campaignlis_"+locid).("")	;
						}
						else{
						jQuery(this).parent().attr("high","no");
						jQuery(this).parent().removeClass("show_deal_blk");
						jQuery(this).parent().addClass("hide_deal_blk");
						
							jQuery(this).parent().css("display","none");
							
						}
						}
					
					});
					}
					else
					{
					
						jQuery("#campaignlis_"+locid+" .deal_blk").each(function(){
						if(jQuery(this).attr("is_expire")  != 1 && is_expiringtoday== 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
						
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("is_new")  != 1 && is_new == 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("discount_val")  != is_discount && is_discount != "" )  
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else
						{
							//jQuery("#campaignlis_"+locid+" .deal_blk").each(function(){
							
							jQuery(this).parent().removeClass("hide_deal_blk");
							jQuery(this).parent().addClass("show_deal_blk");
							
							jQuery(this).parent().attr("high","yes");
								jQuery(this).parent().css("display","block");
								
							//});
						}
					
					});
						
					
					}
					//$('.carousel-inner > .item .carousel-caption').css('display','none');
					if(jQuery("#hdn_is_offer_div_click").val()== 1)
					{
						jQuery(this).css("display","none");
						jQuery(".location_tool[locid="+locid+"] .flip_map_loc").css("display","block");
						//evt.preventDefault();
						$('.slider-carriage').stop(false, false).animate({
							left: (-100 * $('#' + $(this).parent().data('title')).position().left / $('.slider-viewport').width()) + '%'
						}, 500);
					}
					else
					{
						
						
					}
					var content = jQuery('#deal_slider_'+locid).html();
					jQuery('#deal_slider_'+locid).html("");
					jQuery('#deal_slider_'+locid).html(content);
					
					//alert("li count="+jQuery("ul[id='deal_slider_"+locid+"'] li[class='show_deal_blk']").length);
					
					/*
					jQuery("ul[id='deal_slider_"+locid+"'] li[class='show_deal_blk']").each(function() {
						alert(jQuery(this).attr("high"));
						alert(jQuery(this).html());
					});
					*/
                                        
                                        jQuery('#campaignlis_'+locid).find("#prev1").css("display","none");
                                        jQuery('#campaignlis_'+locid).find("#next1").css("display","none");
                                        var li_length=jQuery("ul[id='deal_slider_"+locid+"'] li[class='show_deal_blk']").length;
										//jQuery('#deal_slider_'+locid).css("width","530px");
                                        //jQuery('#deal_slider_'+locid).css("height","410px");
										//if(li_length > 2)
                                      //  {
                                                
                                                  //	jQuery('#deal_slider_'+locid).css("width","530px");
												//	jQuery('#deal_slider_'+locid).css("height","410px");         
                                                         jQuery('#deal_slider_'+locid).css("overflow","hidden");
                                                       jQuery('#deal_slider_'+locid).css("overflow","hidden");
                                                       
                                                 //      setTimeout(function(){
                                                       //jQuery("#imagek").css("width","600px");

                                               //	jQuery('#deal_slider_'+locid).css("width","530px");
                                                       //jQuery('#campaignlis_'+locid+' #deal_slider_'+locid).carouFredSel({
                                                      jQuery('#deal_slider_'+locid).carouFredSel({
                                                       //responsive:true,
																width:'100%',
                                                               circular: false, 
                                                               auto: false,
                                                               prev: '#'+locid+'prev1',
                                                               next: '#'+locid+'next1',
                                                               pagination: "#pager2",
                                                               mousewheel: false,
                                                           //    height : 375,
                                                           //    width:530,
                                                               align:"center",
                                                               swipe: {
                                                                               onMouse: true,
                                                                               onTouch: true
                                                               }
                                                       }); 
                                            //   },1000);
                                       /* }
                                        else{
                                          			jQuery('#campaignlis_'+locid+' .list_carousel1').css("overflow","inherit");
                                                    jQuery('#campaignlis_'+locid).find("#"+locid+"prev1").css("display","none");
                                                    jQuery('#campaignlis_'+locid).find("#"+locid+"next1").css("display","none");

                                           
                                        }*/
										
					//jQuery('#campaignlis_'+locid).css("display","block");
					jQuery('#campaignlis_'+locid+' .list_carousel1').css("overflow","inherit");
					jQuery("#hdn_is_offer_div_click").val("1");
					// close_popuploader('Notificationloader'); 
					jQuery(".location_tool[locid='"+locid+"'] .offer_loader").css("display","none");
					//jQuery(".table_loader").css("display","block");
					jQuery("#imagek").css("opacity","1");
					 //					
	}
	catch(e)
	{
		alert(e);
	}
//	 setTimeout(function(){
	
	//},200);
	setTimeout(function() {
		if(jQuery(".strip").length > 0)
		{
			var ele_strip = jQuery(".strip");
			ele_strip.slideUp('300');
		}
		if(jQuery(".strip_grid").length > 0)
		{
			var ele_strip = jQuery(".strip_grid");
			ele_strip.slideUp('300');
		}
		
    }, 5000);
});
}


}

												
	jQuery('#category_slider').carouFredSel({
		width:'100%',
		circular: false, 
		auto: false,
		prev: '#prev2',
		next: '#next2',
		pagination: "#pager2",
		mousewheel: true,
		height : 80,
		
		align:"center",
		swipe: {
				onMouse: true,
				onTouch: true
		}
	});
	jQuery('.list_carousel').css("overflow","inherit");
	

jQuery(".btn_unreserve").live("click",function(event){
event.stopPropagation();event.preventDefault();
var ele = jQuery(this);
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
    
    jQuery("#btnconfirmunreserve").attr("u_campid",ele.attr("u_campid"));
    jQuery("#btnconfirmunreserve").attr("u_locid",ele.attr("u_locid"));
	jQuery("#btnconfirmunreserve").attr("level",ele.attr("level"));
	//alert("hello"+jQuery("#rservedeals_value").val());
        jQuery.fancybox({
				content:jQuery('#confirmation').html(),
				width: 435,
				height: 345,
				type: 'html',
				openEffect : 'elastic',
				openSpeed  : 300,
                                closeEffect : 'elastic',
				closeSpeed  : 300,
				// topRatio: 0,
               
                                changeFade : 'fast',  
				beforeShow:function(){
									$(".fancybox-inner").addClass("Class_fancy_ie_unreserve");
									},
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
                                
        });
   
     return false;
    //alert(ele.attr("cid")+":"+ele.attr("lid")+";");
   
   return false;
                       }
                  }
                  });
 //  window.location.href = $(this).attr("redirect_url");
});
jQuery("#btnconfirmunreserve").live("click",function(event){
event.stopPropagation();event.preventDefault();
        var ele  = $(this);
		
        var reserve_val = "";
    var deal_barcode_val = "";
if(jQuery("#rservedeals_value").val()!=" ")
    {
        reserve_val = jQuery("#rservedeals_value").val();
    }
      if(jQuery("#deal_barcode").val()!=" ")
    {
        deal_barcode_val = jQuery("#deal_barcode").val();
    }
//alert('<?php echo WEB_PATH; ?>/process.php?btnunreservedeal=true&campaign_id='+$(this).attr("u_campid")+'&l_id='+$(this).attr("u_locid")+'&customer_id=<?php echo $_SESSION['customer_id']; ?>');
 jQuery.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnunreservedeal=true&campaign_id='+$(this).attr("u_campid")+'&l_id='+$(this).attr("u_locid")+'&customer_id=<?php if(isset($customer_id_p)){echo $customer_id_p;} ?>&reload=yes',
          async:false,
		  success:function(msg)
		  {
			//alert(msg);
				 var obj = jQuery.parseJSON(msg);
				  //alert(obj.status);
				  // 9-9-2013 for one per customer redeem deal can't unreserve
				  if(obj.status == "false")
				  {
					//alert("Sorry this offer is already redeemed.");
					if(obj.unreserve_status == "false")
					  {
						//alert("flase");
						 jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btn_print1").css("display","none");
                         reserve_val += ele.attr("u_campid")+":"+ele.attr("u_locid")+";";
                         jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .view").hide();
							 jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .btn_unreserve").hide();
						  if(jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .view-reserve").length == 1)
							  {
								  jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .view-reserve").show();
							  }else{
							 
							 jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview").append('<a target="_parent" class="view-reserve" href="campaign.php?campaign_id='+ele.attr("u_campid")+'&l_id='+ele.attr("u_locid")+'" offer_left="5" redeem="0" reserve="0">View And Reserve</a>');
							  }
								
							 var url_d = jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ ele.attr("u_campid")+"'] .dealtitle").attr('mypopupid');
							  oleft = getParam( 'o_left' , url_d );
								oleft = parseInt(oleft) +1;
							
								url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "0");
								   url_d = url_d.replace(/(is_mydeals=)[^\&]+/, '$1' + "0");
									url_d = url_d.replace(/(o_left=)[^\&]+/, '$1' + ""+obj.o_left+"");
							 ///   url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
								jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ ele.attr("u_campid")+"'] .dealtitle").attr('mypopupid',url_d);

								 jQuery.fancybox.close(); 
						  }
						else
						{
						jQuery.fancybox({

								content:"<div style='width: 220px; height: 125px;'><div style='height:50px;text-align:center;padding-top:20px;'> <?php echo $client_msg['location_detail']['msg_Alreday_Redeemed'];?></div><div style='margin-left:39%;'><input type='submit' name='cancel' id='cancel' value='Ok' onclick='jQuery.fancybox.close()'/></div></div>",
								width: 220,
								height: 150,
								fitToView:true,
								imageScale:true,
								autoSize:false,
								type: 'html',
								openEffect : 'elastic',
								openSpeed  : 300,
								mouseWheel: false,               
								closeEffect : 'fade',
								closeSpeed  : 300,
								helpers: {
										overlay: {
										opacity: 0.3
										} // overlay
								},
								beforeShow:function(){
									jQuery(".fancybox-inner").addClass("offer_redem");
								}
							});
							}
				  }
				  else
				  {
                      jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btn_print1").css("display","none");
                         reserve_val += ele.attr("u_campid")+":"+ele.attr("u_locid")+";";
                         jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .view").hide();
                         jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .btn_unreserve").hide();
                      if(jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .view-reserve").length == 1)
                          {
                              jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .view-reserve").show();
                          }else{
                         
                         jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview").append('<a target="_parent" class="view-reserve" href="campaign.php?campaign_id='+ele.attr("u_campid")+'&l_id='+ele.attr("u_locid")+'" offer_left="5" redeem="0" reserve="0">View And Reserve</a>');
                          }
                         var url_d = jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ ele.attr("u_campid")+"'] .dealtitle").attr('mypopupid');
							oleft = getParam( 'o_left' , url_d );
							oleft = parseInt(oleft) + 1;
						   //alert(obj.o_left);
						   url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "0");
                               url_d = url_d.replace(/(is_mydeals=)[^\&]+/, '$1' + "0");
							   url_d = url_d.replace(/(o_left=)[^\&]+/, '$1' + ""+obj.o_left+"");
                         ///   url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
                            jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ ele.attr("u_campid")+"'] .dealtitle").attr('mypopupid',url_d);

                             jQuery.fancybox.close(); 
					  /*		 
                      //alert(ele.attr("level"));
					  // 1-8-13
					  if(ele.attr("level")==0)
					  {
						jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"']").css("display","none");
					  }
					 // 1-8-13					  
					 */
				  }
          }
   });
       return false;
    });
    jQuery("#btnconfirmcamcel").live("click",function(event){
event.stopPropagation();event.preventDefault();
           jQuery.fancybox.close(); 
       return false; 
    });
</script>	
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/jquery.PrintArea.js_4.js"></script>
<script type="text/javascript">
jQuery(".btn_print1").live('click',function(event){
event.stopPropagation();event.preventDefault();
 var cid=jQuery(this).attr('cid');
         var lid=jQuery(this).attr('lid');
         var barcodea=jQuery(this).attr('barcodea');
		 ele = jQuery(this);
 if(jQuery("#is_profileset").val() != 1){
     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'is_userprofileset=true&custome_id=<?php if(isset($customer_id_p)){echo $customer_id_p;} ?>',
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
					   jQuery("#print_redirect_link").val(cid+"="+lid);
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
     

        try
        {
       jQuery.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH?>/func_print_coupon1.php',
		  data :'load_print_coupon_data1=true&cid='+cid+'&lid='+lid+'&barcodea='+barcodea,
                  
                  async:false,
		  success:function(msg)
		  {
                     //alert(msg);
                     jQuery("#print_coupon_div").html(msg);
                    
                     jQuery("#coupon_div_"+cid+"_"+lid).css('display','block');
                    //jQuery("#print_coupon_div").printArea();
                    timeout=setInterval(function()
                    {
                        //alert('hi')
                        jQuery("#print_coupon_div").printArea();
                        clearTimeout(timeout);
                    },1000);
                  }
       });
       }
       catch(e)
       {
          // alert(e);
       }
     
    });	
	
var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
if(!iOS)
{
	jQuery(".filterd_location").live("mouseleave",function(){
		var new_locid = 0;
		for (var prop in markerArray)
		{
			if(prop != "indexOf")
			{
				markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
            }
        }
		infowindow.close();
		for (var prop in markerArray)
		{
			if(prop != "indexOf")
			{
				markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
            }
        }
		if(jQuery(".filterd_location").find(".current_loc").length == 1)
		{
		new_locid = jQuery(".filterd_location").find(".current_loc").attr("locid");
		lokid = new_locid;
		infowindow.setContent(infowindowcontent[lokid]);
		markerArray[lokid].setIcon('<?php echo ASSETS_IMG?>/c/pin-small-blue.png'); 
		infowindow.open(map,markerArray[lokid]);
		}
		else{
		   /*var latlngbounds = new google.maps.LatLngBounds();
			for (var prop in markerArray)
			{
				if(prop != "indexOf")
				{
				 var latlng2 = new google.maps.LatLng(markerArray[prop].position.lat(), markerArray[prop].position.lng());
				 latlngbounds.extend(latlng2);
			
				}
			}	
	// map.setCenter(latlngbounds.getCenter());
				 map.fitBounds(latlngbounds);			 */
		}
	}); 
	}
	function close_popuploader(popup_name)
    {
		/*$("#" + popup_name + "FrontDivProcessing").css("display","none");
	$("#" + popup_name + "PopUpContainer").css("display","none");
	$("#" + popup_name + "BackDiv").css("display","none");*/
        $("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
            $("#" + popup_name + "BackDiv").fadeOut(200, function () {
                $("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
                    $("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
                    $("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
                    $("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
                });
            });
        });
	
    }
    function open_popuploader(popup_name)
    {

	$("#" + popup_name + "FrontDivProcessing").css("display","block");
	$("#" + popup_name + "PopUpContainer").css("display","block");
	$("#" + popup_name + "BackDiv").css("display","block");
        /*$("#" + popup_name + "FrontDivProcessing").fadeIn(10, function () {
            $("#" + popup_name + "BackDiv").fadeIn(10, function () {
                $("#" + popup_name + "PopUpContainer").fadeIn(10, function () {         
	
                });
            });
        });*/
	
	
    }
jQuery(".loca_categories span").live("click",function(event){
event.stopPropagation();event.preventDefault();

	var cat_nm=jQuery(this).text();
	var inc_scroll = 100;
	var setnull = 0;
	var selected_location=jQuery(this).parent().parent().parent().attr("locid");
	//alert(selected_location);
	
	jQuery(".location_tool").each(function(){
		jQuery(this).removeClass("current_loc");
			jQuery(this).find(".flip_map_loc").css("display","none");
			jQuery(this).find(".loca_total_offers span").css("display","block");
	});
	var selected_location_element = jQuery(this).parent().parent().parent();
	selected_location_element.addClass("current_loc");
	
	for (var prop in markerArray) 
	{
		if(prop != "indexOf") 
		{
			markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
			markerArray[prop].setVisible(false);
		}
	}
	
	jQuery(".location_tool").each(function(){
		var locat=jQuery(this);
		var locat_id=locat.attr("locid");
		jQuery(this).css("display","block");
		
		jQuery(".location_tool[locid='"+ locat_id +"']:not(:contains('"+cat_nm+"'))" ).css("display","none");
		
		
		
		// 22 01 2015 hotel filter not working as it searches hotel in div block location_tool and hotel in present in business name so all div displayed containing Hotel in div block location_tool . So search only in loca_categories span
		
		jQuery(".location_tool[locid='"+ locat_id +"'] .loca_categories:not(:contains('"+cat_nm+"'))" ).css("display","none");
		if(jQuery(this).find(".loca_categories").is(':visible'))
		{

		}
		else
		{
			jQuery(this).css("display","none");
		}
		jQuery(".location_tool .loca_categories").css("display","block");
		
		// 22 01 2015 hotel filter not working as it searches hotel in div block location_tool and hotel in present in business name so all div displayed containing Hotel in div block location_tool . So search only in loca_categories span
		
		
		
		// start bug automotive then aquarim
		var category_id=getCookie("cat_remember");
		var location_categories = jQuery(this).attr("categories");
		var loc_arr = location_categories.split(",");
		var no_times = 0;
		for(i=0;i<loc_arr.length;i++)
		{
			if(loc_arr[i] == category_id )
			{
				no_times++;
			}
		}
		//alert(locat_id+" "+location_categories+" "+no_times);
		
		if(category_id!=0)
		{
			if(no_times<=0)
			{
				jQuery(this).css("display","none");	
			}
		}
		// bug end automotive then aquarim
		
		if (locat.is(':visible')) 
		{
			v = parseInt(locat_id);
			markerArray[v].setVisible(true);
			//jQuery(this).css("display","block");
			jQuery(this).attr("scroll",inc_scroll);
			inc_scroll =  inc_scroll+170;
		} 
		else 
		{
			//jQuery(this).css("display","none");
			jQuery(this).attr("scroll",setnull);
		}

	});
	
	
	jQuery(".newFilterControls.location_Category .chips .chip span").text(cat_nm);
	jQuery(".newFilterControls.location_Category").css("display","block");
	var p = jQuery('.leftside').position();
	var ptop = p.top;
	var pleft = p.left;
	jQuery(window).scrollTop(ptop);
	jQuery(".jspPane").css("top","0px");
	//jQuery(".jspPane").animate({top:"0px"},500);
	
	var selected_cat_id=getCookie("cat_remember");
	var miles_cookie=getCookie("miles_cookie");
			
	<?php 
	if(isset($_SESSION['customer_id']))
	{
	?>
		var customer_id = "<?php echo $_SESSION['customer_id']; ?>";
	<?php 	
	}
	else
	
	{
	?>
		var customer_id = "";
	<?php
	}
	?>
	
	var mlat=getCookie('mycurrent_lati');
	var mlng=getCookie('mycurrent_long');
	
	if(jQuery(".searchdeal_offers #campaignlis_"+selected_location).length == 0)
	{
		open_popuploader('Notificationloader');
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/process.php",
			data: "btnGetSearchDealForLocation=yes&category_id=0&location_id=" + selected_location +"&dismile=50&mlatitude="+mlat+"&mlongitude="+mlng+"&customer_id="+customer_id,
			async:false,
			success: function(msg) 
			{

				var obj=jQuery.parseJSON(msg);
				//alert(obj.status);
				//alert(obj.DetailHtml);
				
				if(obj.status=="false")
				{
					//flag="false";
					//alert_msg +="<div>* "+ obj.message +"</div>";
				}
				else
				{
					jQuery(".searchdeal_offers .campaignlist").each(function(){
						jQuery(this).css("display","none");
					});
					jQuery(".searchdeal_offers").append("<div style='display:block' class='campaignlist' camp_locid='"+selected_location+"' id='campaignlis_"+selected_location+"' camp_catid='"+selected_cat_id+"' camp_miles='"+miles_cookie+"' >"+obj.DetailHtml+"</div>");
					//jQuery(".searchdeal_offers_copy").append("<div style='display:block' class='campaignlist' camp_locid='"+locid+"' id='campaignlist_"+locid+"' camp_catid='"+selected_cat_id+"' camp_miles='"+miles_cookie+"' >"+obj.DetailHtml+"</div>");
					jQuery("#campaignlis_"+selected_location).css("display","block");
				//	jQuery(".searchdeal_offers #campaignlis_"+locid).html("")	;
				}
				
				
			}
		});
		close_popuploader('Notificationloader');
	}
	else
	{
		jQuery(".searchdeal_offers .campaignlist").each(function(){
			jQuery(this).css("display","none");
		});
		jQuery("#campaignlis_"+selected_location).css("display","block");	
		
	}	
	var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
	if(!iOS)
	{
		jQuery('.location').jScrollPane({
			horizontalGutter:5,
			verticalGutter:5,
			'showArrows': false,
            mouseWheelSpeed: 50,
                animateScroll:true,
			  animateDuration:700,
				animateEase:'linear'        
			});
			
			}
			$('.slider-carriage').stop(false, false).animate({
            left: (-100 * $('#camp').position().left / $('.slider-viewport').width()) + '%'
        },500,function(){
		});
		//	jQuery(".flip_map").trigger("click");
});
jQuery("#loca_cat_close").live("click",function(event){
event.stopPropagation();event.preventDefault();
	var cat_nm=jQuery(".newFilterControls location_Category .chips chip span").text();
	jQuery(".location_tool").each(function(){
		var locat=jQuery(this);
		var locat_id=locat.attr("locid"); 
		jQuery(".location_tool[locid='"+ locat_id +"']:not(:contains('"+cat_nm+"'))" ).css("display","block");
	});
	jQuery(".newFilterControls.location_Category .chips .chip span").text("");
	jQuery(".newFilterControls.location_Category").css("display","none");
	
	var selected_cat_id=getCookie("cat_remember");
	var miles_cookie=getCookie("miles_cookie");
	filter_locations(selected_cat_id,miles_cookie);
});	
function del_cookie(name)
{
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

 var index_val = 0;
jQuery("li[id^='thumb_']").live("hover",function(){
                                
}, function(){
	var image_name=jQuery(this).attr('id');
	var location_array=jQuery(this).attr('id').split("_");
   
	// alert(image_name);
	var path_name="<?=ASSETS_IMG?>/m/location/"+image_name+".png";
	//jQuery(this).css('border','3px solid #FFCB00');
	jQuery(".main_image_class_"+location_array[3]).hide();
	jQuery("#main_"+location_array[1]+"_"+location_array[2]+"_"+location_array[3]).show();
	jQuery("#main_"+location_array[1]+"_"+location_array[2]+"_"+location_array[3]).removeClass("current_main");
	jQuery("#main_"+location_array[1]+"_"+location_array[2]+"_"+location_array[3]).addClass("current_main");
	jQuery(".thumb_"+location_array[3]).each(function(){
		jQuery(this).removeClass("current_more");
	});
	var listItem = jQuery(this);
	var li_index=jQuery( ".thumb_"+location_array[3] ).index( listItem );
	index_val=li_index;
	//prevLi = jQuery(li[li_index]).addClass('current_more');
	jQuery(this).addClass("current_more");
	//jQuery(this).removeClass("current_more");
});
				
jQuery("a[id^='previous_more_']").live("click",function(event){
event.stopPropagation();event.preventDefault();
	var location_id=jQuery(this).attr('id').split('_');
	var lengthMinusOne = jQuery("#list_"+location_id[2]+" li").length - 1;
	index_val--;
	if (index_val < 0)
	{
		index_val = lengthMinusOne;
	}	
	jQuery("#list_"+location_id[2]+" li" ).removeClass('current_more');
	jQuery("#list_"+location_id[2]+" li:eq("+index_val+")").addClass('current_more');
	//var image_id=jQuery("#list_"+location_id[2]+" li" ).find('li .current_more').length;
	var image_id=jQuery("#list_"+location_id[2]+" li:eq("+index_val+")").attr('id');
	var location_image_name=image_id.split('_');
   jQuery(".main_image_class_"+location_id[2]).hide();
   jQuery("#main_"+location_image_name[1]+"_"+location_image_name[2]+"_"+location_image_name[3]).show();
});

jQuery("a[id^='next_more_']").live("click",function(event){
event.stopPropagation();event.preventDefault();
	var location_id=jQuery(this).attr('id').split('_');
	var lengthMinusOne = jQuery("#list_"+location_id[2]+" li").length - 1;
	index_val++;
	if (index_val > lengthMinusOne)
	{
		index_val = 0;
	}
	jQuery("#list_"+location_id[2]+" li" ).removeClass('current_more');
	jQuery("#list_"+location_id[2]+" li:eq("+index_val+")").addClass('current_more');
	//var image_id=jQuery("#list_"+location_id[2]+" li" ).find('li .current_more').length;
	var image_id=jQuery("#list_"+location_id[2]+" li:eq("+index_val+")").attr('id');
	var location_image_name=image_id.split('_');
	
   //alert(image_id);
	jQuery(".main_image_class_"+location_id[2]).hide();
	jQuery("#main_"+location_image_name[1]+"_"+location_image_name[2]+"_"+location_image_name[3]).show();
	
	//jQuery('.thumbimageclass').removeClass('current_more').next('li').addClass('current_more');	 
});



jQuery("li#tab-type a").live("click",function(event){
event.stopPropagation();event.preventDefault();

	var ele = (jQuery("#LocationFancymainContainer").find("div[id^='locationdetailinfo_']"));
	var locid = ele.attr("id").split("_")[1];
	
	jQuery("#sidemenu li a").each(function() {
		jQuery(this).removeClass("current");
	});
   jQuery(this).addClass("current");
	var cls = jQuery(this).parent().attr("class");
	jQuery(".tabs").each(function(){
		jQuery(this).css("display","none");
	});
	var v  = "";
                
	var r_n = "";
	if(cls== "div_details")
	{
		jQuery("#div_details").css("display","block");
	}
	else if(cls== "div_images")
	{
		jQuery("#div_images").css("display","block");
	}
	else if(cls== "div_menus")
	{
		jQuery("#div_menus").css("display","block");
	}
	else if(cls== "div_ratings")
	{
		jQuery("#div_ratings").css("display","block");
		if(jQuery('#container_'+locid).attr("rating_trend_data"))
		{
			/**** initilizing rating trade data ******/
			var parsedTest = JSON.parse(jQuery('#container_'+locid).attr("rating_trend_data"));
			jQuery('#container_'+locid).highcharts({
						chart: {
							zoomType: 'null',
							spacingRight: 20
						},
						credits :{
									enabled:false
								},
						exporting: {
							buttons: {
								contextButtons: {
									enabled: false,
									menuItems: null
								}
							},
							enabled: false
						},
						 title: {
							text: 'Rating Trend',
							style: {
							display: 'none'
									}
						},           
						xAxis: {
							
							type: 'datetime',
							title: {
								text: null
							}
						},
						yAxis: {
							min:1,
							max:5,
							title: {
								text: null
							}
						},
						tooltip: {
						   shared: true
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							area: {
								fillColor: {
									linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
									stops: [
										[0, Highcharts.getOptions().colors[0]],
										[1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
									]
								},
								lineWidth: 1,
								marker: {
									enabled: false
								},
								shadow: false,
								states: {
									hover: {
										lineWidth: 1
									}
								},
								threshold: null
							}
						},   
						series: [{
							type: 'area',
							name: 'Current Rating',
							pointInterval: 24 * 3600 * 1000 ,//point interval in data is for every days average rating
							pointStart: Date.UTC(st_yr, st_mnt, 01),
							//avg rating for last 3 months
							data: parsedTest ,
							enableMouseTracking: false
						}]
			});	
		}
	}
	else
	{
		jQuery("#div_reviews").css("display","block");
	}
				
});
	
</script>	

<script type="text/javascript" >
$(document).ready(function(){
	$(".flip_map , .flip_map_loc").live("click",function(event){
event.stopPropagation();event.preventDefault();
	jQuery(".flip_map").css("display","none");
		jQuery(".loca_total_offers span").css("display","block");
		jQuery(".flip_map_loc").css("display","none");
		//alert( $('#' + $(this).data('title')).position().left );
        $('.slider-carriage').stop(false, false).animate({
            left: (-100 * $('#' + $(this).data('title')).position().left / $('.slider-viewport').width()) + '%'
        }, 500);
	
	});
});
jQuery("a[id^='review_more_']").live("click",
    function(event){
event.stopPropagation();event.preventDefault();
		//alert(jQuery(this).attr("id"));
		var review_id=jQuery(this).attr("review_id");
		if(jQuery(this).attr("more")==1)
		{
			//jQuery("#review_"+review_id).css("height","auto");
			//jQuery(this).text("Show Less");
			jQuery(this).attr("more","0");
			jQuery("#review_"+review_id).css("height","auto");
			jQuery("#review_"+review_id).html(jQuery("#review_"+review_id+"_hidden").html());
		}
		else
		{
			//jQuery("#review_"+review_id).css("height","75px");
			//jQuery(this).text("Show More");
			//jQuery(this).attr("more","1");
		}
    }
);

/****** for un subscribe functionality ************/
jQuery("body").on("click",".unsubscribestore",function(event){
event.stopPropagation();event.preventDefault();
var locid1 = $(this).attr("s_lid1");
    var ele = jQuery(this);
	 
  // alert('process.php?btnunsubscribelocation=1&location_id='+$(this).attr("s_lid"));
	//return false;
	jQuery(".location_tool[locid='"+ locid1 +"']").find(".sub_loader").css("display","block");
     $.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'btnunsubscribelocation=1&location_id='+$(this).attr("s_lid"),
                 // async:false,
		  success:function(msg)
		  {
			    var obj = jQuery.parseJSON(msg);
                        if(obj.loginstatus == "false")
                        {
							//alert(obj.link);
                           parent.window.location.href=obj.link;
                        }
                        else
                        {      
      //                alert(ele.text());
                     ele.text("Subscribe");
        //              alert(ele.text());
                      ele.removeClass("unsubscribestore");
                      ele.addClass("subscribestore");
					  
                         ele.parent().prev().removeClass("save_unsubscribe_icon");
						 ele.parent().prev().addClass("save_subscribe_icon");
                             jQuery("#temp_infowiondow").html(infowindowcontent[locid1]);
                    var ele1 = jQuery("#temp_infowiondow .unsubscribestore");
                    ele1.text("Subscribe to store");
                    ele1.removeClass("unsubscribestore");
                      ele1.addClass("subscribestore");
      infowindowcontent[locid1] = jQuery("#temp_infowiondow").html();
			        var selected_cat_id=getCookie("cat_remember");
		var miles_cookie=getCookie("miles_cookie");
		no_of_private_deal = get_private_deals(selected_cat_id,miles_cookie,locid1);
	//	alert(no_of_private_deal);
                //offers_content = jQuery(".location_tool[locid='"++"'].loca_total_offers span").text();
				offers_content = jQuery(".location_tool[locid='"+locid1+"'] .loca_total_offers span").text();
				total_no_of_deals = parseInt(offers_content.match(/\d+/),10);
                    // alert(1);
                    /* 28-02-2014 */ 
					 /*jQuery(".displayul .deal_blk[locid="+locid1+"][level=2]").each(function(){
							jQuery(this).detach();
					});
					 jQuery(".displayul_all .deal_blk[locid="+locid1+"][level=2]").each(function(){
							jQuery(this).detach();
					}); */
					
					/* 28-02-2014 */
					
					levels= jQuery(".location_tool[locid='"+locid1+"']").attr("levels");
					levels_arr = levels.split(",");
					o_l = 0;
					for(c=0;c<levels_arr.length;c++)
					{
						if(parseInt(levels_arr[c]) == 2)
						{
							
						}
						else
						{
							o_l = o_l + 1 ;
						}
					}
					
					//jQuery(".location_tool[locid='"+locid1+"'] .loca_total_offers span").text(o_l+" offers");
					if(jQuery("#deal_slider_"+locid1).length == 1)
					{
						jQuery("#deal_slider_"+locid1+" .deal_blk[level=2]").each(function(){
					     var campid=jQuery(this).attr('campid');
						//alert("hi");
									//alert("deal is unreserved");
									jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btn_print1").css("display","none");
									//reserve_val += ele.attr("u_campid")+":"+ele.attr("u_locid")+";";
									jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .view").hide();
									jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .btn_unreserve").hide();
								    if(jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .view-reserve").length == 1)
									{
										jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .view-reserve").show();
									}
									else
									{
										jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview").append('<a target="_parent" class="view-reserve" href="campaign.php?campaign_id='+campid+'&l_id='+locid1+'" offer_left="5" redeem="0" reserve="0">View And Reserve</a>');
									}
									
									var url_d = jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .dealtitle").attr('mypopupid');
									url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "0");
									url_d = url_d.replace(/(is_mydeals=)[^\&]+/, '$1' + "0");
									///   url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
									jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+ campid+"'] .dealtitle").attr('mypopupid',url_d);
									
								
					 });
					   jQuery("#deal_slider_"+locid1+" .deal_blk[level=2]").parent().detach();
					}
					<?php 
		if(isset($_SESSION['customer_id']))
		{
		?>
			var customer_id = "<?php echo $_SESSION['customer_id']; ?>";
		<?php 	
		}
		else
		{
		?>
			var customer_id = "";
		<?php
		}
		?>
		/*var mlat="<?php echo $_COOKIE['mycurrent_lati'] ?>";
		var mlng="<?php echo $_COOKIE['mycurrent_long'] ?>"; */
		//alert(mlat+"=="+mlng);
		var mlat=getCookie('mycurrent_lati');
		var mlng=getCookie('mycurrent_long');
					 //ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                //     ele1.find(".tot_deal_counter").parent().detach();
				//alert("<?php echo WEB_PATH; ?>/process.php?btnGetSearchDealForLocation=yes&category_id=0&location_id=" + locid1 +"&dismile=50&mlatitude="+mlat+"&mlongitude="+mlng+"&customer_id="+customer_id);
					   $.ajax({
								type:"POST",
								url:'<?php echo WEB_PATH; ?>/process.php',
								data :"btnGetSavedOfferForLocation=yes&category_id=0&location_id=" + locid1 +"&dismile=50&mlatitude="+mlat+"&mlongitude="+mlng+"&customer_id="+customer_id,
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									new_deal_count=obj.total_records;
									//if(new_deal_count>old_deal_count)
									//{
										//if(jQuery(".searchdeal_offers #campaignlis_"+locid1).length == 0)
										//{
										//	alert("in if");
										//	jQuery(".searchdeal_offers").append("<div  class='campaignlist' camp_locid='"+locid1+"' id='campaignlis_"+locid1+"' camp_catid='"+selected_cat_id+"' camp_miles='"+miles_cookie+"' >"+obj.DetailHtml+"</div>");
									//	}
										//else{
											jQuery(".location_tool[locid="+locid1+"]").attr("levels",obj.levels);
											jQuery(".location_tool[locid="+locid1+"]").attr("categories",obj.categories);
											jQuery(".location_tool[locid="+locid1+"]").attr("t_l_e",obj.expiring);
											
										//	alert("in else");
										//	jQuery(".searchdeal_offers #campaignlis_"+locid).html("");
										//	jQuery(".searchdeal_offers #campaignlis_"+locid).html(obj.DetailHtml);
										//}
									//}
								}			  
							});
					   
					   selected_cat_id=getCookie("cat_remember");
                          
                       
                       miles_cookie=getCookie("miles_cookie");
					
                          
                      // filter_locations(selected_cat_id,miles_cookie);
					// 9-8-2013 to solve marker infowindow closed when unsubscribe
						infowindow.setContent(infowindowcontent[locid1]);
						markerArray[locid1].setIcon('<?php echo ASSETS_IMG?>/c/pin-small-blue.png'); 
						infowindow.open(map,markerArray[locid1]);
						/**** fileterinf data ***************/
							var selected_cat_id=getCookie("cat_remember");
							var miles_cookie=getCookie("miles_cookie");
							var locid= locid1;
							var is_expiringtoday =  jQuery("#hdn_is_expiring_today").val();
							var is_new =  jQuery("#hdn_is_new_campaign").val();
							var is_discount = jQuery("#hdn_discount").val();
							if(selected_cat_id != 0)
					{
				//	alert("In selected category");
					jQuery("#campaignlis_"+locid+" .deal_blk").each(function(){
						if(jQuery(this).attr("is_expire")  != 1 && is_expiringtoday== 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
						
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("is_new")  != 1 && is_new == 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("discount_val")  == is_discount && is_discount == 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else
						{
						if(jQuery(this).attr("catid") == selected_cat_id)
						{
						jQuery(this).parent().attr("high","yes");
						jQuery(this).parent().removeClass("hide_deal_blk");
						jQuery(this).parent().addClass("show_deal_blk");
						
							jQuery(this).parent().css("display","block");
							
							//jQuery(".searchdeal_offers #campaignlis_"+locid).("")	;
						}
						else{
						jQuery(this).parent().attr("high","no");
						jQuery(this).parent().removeClass("show_deal_blk");
						jQuery(this).parent().addClass("hide_deal_blk");
						
							jQuery(this).parent().css("display","none");
							
						}
						}
					
					});
					}
					else
					{
						jQuery("#campaignlis_"+locid+" .deal_blk").each(function(){
						if(jQuery(this).attr("is_expire")  != 1 && is_expiringtoday== 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
						
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("is_new")  != 1 && is_new == 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else if(jQuery(this).attr("discount_val")  == is_discount && is_discount == 1  ) 
						{
							jQuery(this).parent().attr("high","no");
							jQuery(this).parent().removeClass("show_deal_blk");
							jQuery(this).parent().addClass("hide_deal_blk");
							jQuery(this).parent().css("display","none");
							
						}
						else
						{
							jQuery("#campaignlis_"+locid+" .deal_blk").each(function(){
							
							jQuery(this).parent().removeClass("hide_deal_blk");
							jQuery(this).parent().addClass("show_deal_blk");
							
							jQuery(this).parent().attr("high","yes");
								jQuery(this).parent().css("display","block");
								
							});
						}
					
					});
						
					
					}
					//$('.carousel-inner > .item .carousel-caption').css('display','none');
					
					var content = jQuery('#deal_slider_'+locid).html();
					jQuery('#deal_slider_'+locid).html("");
					jQuery('#deal_slider_'+locid).html(content);
					
					  jQuery('#campaignlis_'+locid).find("#prev1").css("display","none");
                                        jQuery('#campaignlis_'+locid).find("#next1").css("display","none");
                                        var li_length=jQuery("ul[id='deal_slider_"+locid+"'] li[class='show_deal_blk']").length;
										//jQuery('#deal_slider_'+locid).css("width","530px");
                                       // jQuery('#deal_slider_'+locid).css("height","410px");
										jQuery(".location_tool[locid='"+locid+"'] .loca_total_offers span").text(li_length+" offers");
									//	alert(li_length+"un subscribe"+jQuery("#deal_slider_"+locid1+" .deal_blk[level=2]").parent().attr("class"));
									if(li_length == 0)
									{
										jQuery(".location_tool[locid="+locid+"]").css("display","none");
										mile_val1 = getCookie("miles_cookie");
										filter_locations(getCookie("cat_remember"),mile_val1);
									}
									
										/*if(li_length > 2)
                                        { */
										//alert("inun subscribe");
                                                
                                                  //	jQuery('#deal_slider_'+locid).css("width","530px");
												//	jQuery('#deal_slider_'+locid).css("height","410px");         
                                                         jQuery('#deal_slider_'+locid).css("overflow","hidden");
                                                       jQuery('#deal_slider_'+locid).css("overflow","hidden");
                                                       
                                           
                                               //	jQuery('#deal_slider_'+locid).css("width","530px");
                                                       //jQuery('#campaignlis_'+locid+' #deal_slider_'+locid).carouFredSel({
                                                      jQuery('#deal_slider_'+locid).carouFredSel({
                                                       //responsive:true,
																width:'100%',
                                                               circular: false, 
                                                               auto: false,
                                                               prev: '#'+locid+'prev1',
                                                               next: '#'+locid+'next1',
                                                               pagination: "#pager2",
                                                               mousewheel: false,
                                                           //    height : 375,
                                                           //    width:530,
                                                               align:"center",
                                                               swipe: {
                                                                               onMouse: true,
                                                                               onTouch: true
                                                               }
                                                       }); 
                                            //   },1000);
                                       /* }
                                        else{
										//alert("in else un subscribe");
                                          			jQuery('#campaignlis_'+locid+' .list_carousel1').css("overflow","inherit");
                                                    jQuery('#campaignlis_'+locid).find("#"+locid+"prev1").css("display","none");
                                                    jQuery('#campaignlis_'+locid).find("#"+locid+"next1").css("display","none");

                                           
                                        } */
										
					//jQuery('#campaignlis_'+locid).css("display","block");
					jQuery('#campaignlis_'+locid+' .list_carousel1').css("overflow","inherit");
					
				
					/********* filtering data ************/
					// 9-8-2013
					jQuery(".location_tool[locid='"+ locid1 +"']").find(".sub_loader").css("display","none");
					}
                                       if(total_no_of_deals == no_of_private_deal)
                                        {
                                            //no_of_private_deal_all = get_private_deals(0,50,locid1);
											jQuery(".location_tool[locid="+locid1+"]").css("display","none");
											mile_val1 = getCookie("miles_cookie");
										filter_locations(getCookie("cat_remember"),mile_val1);
                                        }
                  }
   });
});
jQuery("body").on("click",".unsubscribestore1",function(event){
event.stopPropagation();event.preventDefault();
var locid1 = $(this).attr("s_lid1");
    var ele = jQuery(this);
  // alert('process.php?btnunsubscribelocation=1&location_id='+$(this).attr("s_lid"));
	//return false;
	jQuery(".location_tool[locid='"+ locid1 +"']").find(".sub_loader").css("display","block");
     $.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'btnunsubscribelocation=1&location_id='+$(this).attr("s_lid"),
                 // async:false,
		  success:function(msg)
		  {
			    var obj = jQuery.parseJSON(msg);
                        if(obj.loginstatus == "false")
                        {
							//alert(obj.link);
                           parent.window.location.href=obj.link;
                        }
                        else
                        {      
      //                alert(ele.text());
                     ele.text("Subscribe");
        //              alert(ele.text());
                      ele.removeClass("unsubscribestore");
                      ele.addClass("subscribestore");
					  
                         ele.parent().prev().removeClass("save_unsubscribe_icon");
						 ele.parent().prev().addClass("save_subscribe_icon");
                             jQuery("#temp_infowiondow").html(infowindowcontent[locid1]);
                    var ele1 = jQuery("#temp_infowiondow .unsubscribestore");
                    ele1.text("Subscribe to store");
                    ele1.removeClass("unsubscribestore");
                      ele1.addClass("subscribestore");
      infowindowcontent[locid1] = jQuery("#temp_infowiondow").html();
			
                     // alert(1);
                    /* 28-02-2014 */ 
					 /*jQuery(".displayul .deal_blk[locid="+locid1+"][level=2]").each(function(){
							jQuery(this).detach();
					});
					 jQuery(".displayul_all .deal_blk[locid="+locid1+"][level=2]").each(function(){
							jQuery(this).detach();
					}); */
					
					/* 28-02-2014 */
					
					levels= jQuery(".location_tool[locid='"+locid1+"']").attr("levels");
					levels_arr = levels.split(",");
					o_l = 0;
					for(c=0;c<levels_arr.length;c++)
					{
						if(parseInt(levels_arr[c]) == 2)
						{
							
						}
						else
						{
							o_l = o_l + 1 ;
						}
					}
					
					jQuery(".location_tool[locid='"+locid1+"'] .loca_total_offers span").text(o_l+" offers");
					if(jQuery("#deal_slider_"+locid1).length == 1)
					{
						jQuery("#deal_slider_"+locid1+" .deal_blk[level=2]").each(function(){
					     var campid=jQuery(this).attr('campid');
						//alert("hi");
									//alert("deal is unreserved");
									jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btn_print1").css("display","none");
									//reserve_val += ele.attr("u_campid")+":"+ele.attr("u_locid")+";";
									jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .view").hide();
									jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .btn_unreserve").hide();
								    if(jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .view-reserve").length == 1)
									{
										jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .view-reserve").show();
									}
									else
									{
										jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview").append('<a target="_parent" class="view-reserve" href="campaign.php?campaign_id='+campid+'&l_id='+locid1+'" offer_left="5" redeem="0" reserve="0">View And Reserve</a>');
									}
									
									var url_d = jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .dealtitle").attr('mypopupid');
									url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "0");
									url_d = url_d.replace(/(is_mydeals=)[^\&]+/, '$1' + "0");
									///   url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
									jQuery("#deal_slider_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+ campid+"'] .dealtitle").attr('mypopupid',url_d);
									
								
					 });
					   jQuery("#deal_slider_"+locid1+" .deal_blk[level=2]").css("display","none");
					}
					 //ele1 = jQuery(".displayul .deal_blk[merid='"+mid+"'][locid='"+loc_arr[i]+"'][campid='"+temp_arr[loc_arr[i]]+"']");
                //     ele1.find(".tot_deal_counter").parent().detach();
					   
					   
					   selected_cat_id=getCookie("cat_remember");
                          
                       
                       miles_cookie=getCookie("miles_cookie");
					
                          
                      // filter_locations(selected_cat_id,miles_cookie);
					// 9-8-2013 to solve marker infowindow closed when unsubscribe
						infowindow.setContent(infowindowcontent[locid1]);
						markerArray[locid1].setIcon('<?php echo ASSETS_IMG?>/c/pin-small-blue.png'); 
						infowindow.open(map,markerArray[locid1]);
					// 9-8-2013
					jQuery(".location_tool[locid='"+ locid1 +"']").find(".sub_loader").css("display","none");
					}
                  }
   });
});
/******* for unsubscribe functionality ************/
</script>
<div class="location_fancybox" style="display:none;">
</div>
 <div id="LocationFancyPopUpContainer" class="container_popup"  style="display: none;">
    <div id="LocationFancyBackDiv" class="divBack">
    </div>
    <div id="LocationFancyFrontDivProcessing" class="Processing" style="display:none;">

        <div id="LocationFancyMaindivLoading" align="center" valign="middle" class="imgDivLoading"
             style="left: 25%;top: 10%;">
			<div class="modal-close-button" style="visibility: visible;"><a tabindex="0" onClick="close_popup('LocationFancy');" id="fancybox-close" style="display:inline;"></a></div>
            <div id="LocationFancymainContainer" class="innerContainer" style="height:auto;width:auto">
                
            </div>
        </div>
    </div>
</div>
<script src="<?php echo ASSETS_JS?>/c/highcharts.js"></script>
<script src="<?php echo ASSETS_JS?>/c/exporting.js"></script>
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
jQuery("body").on("click","#login_frm #btn_cancel_forgot",function(event){
event.stopPropagation();event.preventDefault();
		jQuery.fancybox.close();
    });
</script>
<script>
$(function() {
    $('.container').click(function(evt) {
	//alert("inn");
        evt.preventDefault();
        $('.slider-carriage').stop(false, false).animate({
            left: (-100 * $('#' + $(this).data('title')).position().left / $('.slider-viewport').width()) + '%'
        }, 500);
    });
});
var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
    // Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
var isFirefox = typeof InstallTrigger !== 'undefined';   // Firefox 1.0+
var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
    // At least Safari 3+: "[object HTMLElementConstructor]"
var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
var isIE = /*@cc_on!@*/false || !!document.documentMode;   // At least IE6
if(isIE)
{
$('[placeholder]').focus(function() {
  var input = $(this);
  if (input.val() == input.attr('placeholder')) {
    input.val('');
    input.removeClass('placeholder');
  }
}).blur(function() {
  var input = $(this);
  if (input.val() == '' || input.val() == input.attr('placeholder')) {
    input.addClass('placeholder');
    input.val(input.attr('placeholder'));
  }
}).blur();
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
var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
if(iOS)    
{

 jQuery('<link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS; ?>/c/iosdevicecss.css" />').appendTo("head");

function openlink(link)
{
	//alert(link);
}
jQuery(".campaignlist .view-reserve").live("click",function(){
//alert(jQuery(this).attr("linkurl"));
				window.location.href = jQuery(this).attr("linkurl");
});
};
jQuery.fn.scrollTo = function( target, options, callback ){
  if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
  var settings = $.extend({
    scrollTarget  : target,
    offsetTop     : 50,
    duration      : 500,
    easing        : 'swing'
  }, options);
  return this.each(function(){
    var scrollPane = $(this);
    var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
    var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
    scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
      if (typeof callback == 'function') { callback.call(this); }
    });
  });
}
// for google plus 	

function callbackfromgoogleplus(data)
{
	//alert(data.status);
	//alert(jQuery("#hdn_campid").val());
	//alert(jQuery("#hdn_locid").val());
	//alert(data.toSource());
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
		var cust_id="<?php echo $_SESSION['customer_id'];?>";
		var campaign_id=jQuery("#hdn_campid").val();
		var l_id=jQuery("#hdn_locid").val();					
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
	}
}

// for google plus 
</script>
