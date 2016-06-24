<?php
/******** 
@USE : location detail page
@PARAMETER : 
@RETURN : 
@USED IN PAGES : search-deal.php, mymerchants.php, my-deals.php, process_mobile.php, frequent-mail-by-admin.php, process_qrcode.php, qr.php, campaign.php, process.php
*********/

//require_once("classes/Config.Inc.php");
//check_customer_session();
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(LIBRARY."/func_print_coupon.php");
//include_once(SERVER_PATH."/classes/JSON.php");
require_once LIBRARY.'/google-api-php-client/src/Google_Client.php';
require_once LIBRARY.'/google-api-php-client/src/contrib/Google_PlusService.php';
require_once LIBRARY.'/google-api-php-client/src/contrib/Google_Oauth2Service.php';

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



$decode_location_id = base64_decode($_REQUEST['id']);

/*
$decode_location_id= $_REQUEST['id'];
*/
//$objJSON = new JSON();
//$objDB = new DB();
//$objDBWrt = new DB('write');
$path_captcha=WEB_PATH."/captcha.php";
$o_left=0;

//Start facebook sharing code
if(isset($_SESSION['customer_id'] ))
{
   /*  $Sql_new = "select * from customer_user  where id=".$_SESSION['customer_id'];
    $RS_user_data = $objDB->Conn->Execute($Sql_new); */
	$cust_id = $_SESSION['customer_id'];
	$cust_s = $_SESSION['customer_id'];
$RS_user_data = $_SESSION['customer_id'];
    $cust_id_with_url=base64_encode($_SESSION['customer_id']);
}
else
{
	$cust_s="";
	$cust_id_with_url="";
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
                   
                   
                    if($RS_user_data['access_token'] != "")
                    {
                        
                    }
                    else
                    {
                        
                        if($user_email['email'] == $RS_user_dat['emailaddress'])
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
                        
                        if($RS_user_data['profile_pic'] == "")
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
                      
                       if($RS_user_data['profile_pic'] == "")
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


/*
$sql_all_location = "select * from locations";
 $RS_role = $objDB->Conn->Execute($sql_all_location );
	while($Row_role = $RS_role->FetchRow()){
		$array_new = $where_clause1 = array();
			$permalink_url = $objJSON->create_location_url($Row_role['id'],$Row_role['location_name'],$Row_role['city']);
			$array_new['location_permalink'] = $permalink_url;
            
			
			$where_clause1['id'] = $Row_role['id'];
			$objDB->Update($array_new , "locations", $where_clause1);
	}
	*/
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

$location_id=$decode_location_id;


?>

<?php
if(isset($_COOKIE['is_scanflip_scan_qrcode']))
{
	if($_COOKIE['is_scanflip_scan_qrcode'] != "")
	{
            
		if(isset($_COOKIE['scanflip_customer_id']) == ""){
                    $custid = 0;
                }
                else{
                    $custid = $_COOKIE['scanflip_customer_id'];
                }
                
        }
$uniqueid = create_unique_code()."".strtotime(date("Y-m-d H:i:s"));
     $q_id = $_COOKIE['is_scanflip_scan_qrcode'];
     $locationid = $decode_location_id;
$insert_array = array();
       
        $rs_timezone = $objDB->Conn->Execute("select timezone from locations where id= ?",array($decode_location_id));
         $timezone = $rs_timezone->fields['timezone'];
         
    /* $dt_sql  = "SElect CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR('". $timezone."',1, POSITION(',' IN '". $timezone."')-1)) dte " ;
    //echo $dt_sql;
    $RS_dt = $objDB->Conn->Execute($dt_sql); */
    $RS_dt = $objDB->Conn->Execute("Select CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(?,1, POSITION(',' IN ?)-1)) dte",array($timezone,$timezone));
    
        
   //  if($custid == 0){
  
    $cookie_time = time()+(20 * 365 * 24 * 60 * 60);
    $data=array();
    if(isset($_COOKIE['scanflip_scan_qrcode']))
    {
        $data = unserialize($_COOKIE['scanflip_scan_qrcode']);
    }

    $insert_array = array();
    if(count($data) != 0 ){
//    foreach ($data as $key=>$value)
//    {
        if(array_key_exists($q_id,$data))   
        {
             $arr_new = array();
                $arr_new = explode("-",$data[$q_id]);
            if($arr_new[0]."-".$arr_new[1] == "0-".$locationid)
            {
             //   echo "=== in if";
                $insert_array['is_unique']= 0;
                $arr = array();
                $arr = explode("-",$data[$q_id]);
                $uid =  $arr[2] ;
                
                if($custid != 0)
                {
                     /* $sql = "select * from scan_qrcode where campaign_id=0 and location_id=".$locationid." and qrcode_id=".$q_id." and user_id=".$custid." and unique_id='".$arr_new[2]."'";
                     $RS_unique = $objDB->Conn->Execute($sql); */
                     $RS_unique = $objDB->Conn->Execute("select * from scan_qrcode where campaign_id=0 and location_id=?  and qrcode_id= ? and user_id=? and unique_id=?",array($locationid,$q_id,$custid,$arr_new[2]));
//                        if($RS_unique->RecordCount() == 0)
//                        {
                             /* $sql= "update scan_qrcode set user_id=".$custid." where unique_id='".$arr_new[2]."' ";
                             $objDB->Conn->Execute($sql); */
                             $objDBWrt->Conn->Execute("update scan_qrcode set user_id=? where unique_id=?",array($custid,$arr_new[2]));
                      //  }
                        
                }
            }else{
             //   echo "== in else";
                $insert_array['is_unique']= 1;
                $data[$q_id] = "0-".$locationid."-".$uniqueid;
                 $uid = $uniqueid;
            }
        }
        else{
          //  echo "In else".$campaignid."-".$locationid;
           // echo "In else";
            $data[$q_id] = "0-".$locationid."-".$uniqueid;
            $insert_array['is_unique']= 1;
            $uid = $uniqueid;
        }
   // }
    
}
else{
   // echo "In main else".$campaignid."-".$locationid;
    $insert_array['is_unique']= 1;
    $data[$q_id] = "0-".$locationid."-".$uniqueid;
    $uid = $uniqueid;
}
 //  echo $insert_array['is_unique'];
  //  print_r($data);
  setcookie('scanflip_scan_qrcode', serialize($data), $cookie_time);

    
//      //  $insert_array = array();
            $insert_array['qrcode_id']= $q_id;
            $insert_array['campaign_id']= 0;
            $insert_array['location_id']= $locationid;
            $insert_array['is_location']= 1;
            $insert_array['is_superadmin']= 0;
              $insert_array['unique_id']= $uid;
      $insert_array['scaned_date']= $RS_dt->fields['dte'];
            $insert_array['user_id']=$custid ;
              $objDB->Insert( $insert_array, "scan_qrcode");
              setcookie('is_scanflip_scan_qrcode', "", time()-36666);
               setcookie('mycurrent_lati_qrcode', "", time()-36666);
                          setcookie('mycurrent_long_qrcode', "", time()-36666);
}
   

/*************  start location information coding *****************/

/* if(isset($_SESSION['customer_id']))
{
	$cust_s = $_SESION['customer_id'];
} */

$sql = "select latitude , longitude , created_by from locations where id=".$decode_location_id;

$RSLocation = $objDB->Conn->Execute($sql);


// echo WEB_PATH.'/process.php?btnGetmarkerforLocations_locationdetail=yes&dismile=50&mlatitude='.$RSLocation->fields['latitude'].'&mlongitude='.$RSLocation->fields['longitude'].'&category_id=0&customer_id='.$cust_s.'&merchant_id='.$RSLocation->fields['created_by'];
$arr=file(WEB_PATH.'/process.php?btnGetmarkerforLocations_locationdetail=yes&dismile=50&mlatitude='.$RSLocation->fields['latitude'].'&mlongitude='.$RSLocation->fields['longitude'].'&category_id=0&customer_id='.$cust_s.'&merchant_id='.$RSLocation->fields['created_by']);
//print_r($arr);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;  
$total_records1 = $json->total_records;
// echo "<pre>";
// print_r($records_array);
// echo "</pre>";
			$records_array1 = $json->records;
			$marker_records_array1 = $json->marker_records;
			$marker_total_records =$json->marker_total_records;	
	$selected_location =  array();
	foreach($records_array as $Row)
		{
                    
                      $storeid = $Row->locid;
					  if($storeid == $decode_location_id)
					  {
						$selected_location[0] = $Row;
					  }
                       
					}
							
$tel_arr = explode("-",$selected_location[0]->phone_number);
/*************/

			// 19 10 2013 solve problem of location click event when session timeout and on location no public deal found then redirect to nearest location.
			
			// echo "total records = ".$total_records1;
			
			//$total_records1 = 0;
			if($total_records1==0 || $total_records1=="0")
			{
				//header("Location:".WEB_PATH."/register.php");
				exit();
			}
						//echo "<br>";
			$loc_arr=array();
			foreach($records_array1 as $Row)
            {
				//echo "===".$Row->locid."<br/>";

				array_push($loc_arr,$Row->locid);
			}
			
			// print_r($loc_arr);
			
			if(in_array($decode_location_id,$loc_arr))
			{
				
			}
			else
			{
				//header("Location:".WEB_PATH."/location_detail.php?id=".$loc_arr[0]);
				foreach($records_array as $Row)
				{
                    
                      $storeid = $Row->locid;
					  if($storeid == $loc_arr[0])
					  {
						$permalink = $Row->location_permalink;
					  }
                       
					}
				header("Location:".$permalink);
				exit();
			}
			//exit();
/*************/
 array_shift($tel_arr);

$telephone_number = implode("-",$tel_arr);
if($selected_location[0]->picture !="")
							{
								$loc_image = ASSETS_IMG."/m/location/mythumb/".$selected_location[0]->picture;  
							}
							else
							{
								$loc_image = ASSETS_IMG."/c/Merchant_Location.png"; 
							}
?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml"lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Cache-control" content="public,max-age=86400,must-revalidate">
<title> <?php echo $selected_location[0]->business." - ".$selected_location[0]->city.",".$selected_location[0]->state.",".$selected_location[0]->country." | Scanflip";?></title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta name="description"  content="<?php echo substr ( strip_tags( $selected_location[0]->aboutus_short) , 0 ,155 ); ?>"/>
<meta name="keywords" content="Scanflip,Recommendation,Reviews, local business, coupons, offers, local offers , savings , discounts, deals ,<?php echo $selected_location[0]->business_tags; ?> ">

<!-- Open Graph data -->
<meta property="fb:app_id" content="654125114605525">
<meta property="og:title" content="<?php echo $selected_location[0]->business." - ".$selected_location[0]->city.",".$selected_location[0]->state.",".$selected_location[0]->country." | Scanflip";?>" >
<meta property="og:description"  content="<?php echo substr ( strip_tags( $selected_location[0]->aboutus_short) , 0 ,155 ); ?>"/>
<meta property="og:url" content="<?php  echo $_SERVER['SCRIPT_URI']; ?>">
<meta property="og:image" content="<?php  echo $loc_image;?>">


<link href="<?=ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">
<link rel="alternate" href="android-app://com.lanet.scanflip/location/location_id=<?php echo $decode_location_id; ?>" />
<!--<link rel="icon" href="path to /favicon.ico" type="x-icon"> -->
<link rel="image_src" href="<?php  echo $loc_image;?>">
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "LocalBusiness",
  "address":{
    "@type": "PostalAddress",
	"addressCountry": "Canada",
	  "streetAddress": "<?php echo $selected_location[0]->address;?>",
    "addressLocality": "<?php echo $selected_location[0]->city;?>",
    "addressRegion": "<?php echo $selected_location[0]->state;?>",
    "postalCode": "<?php echo $selected_location[0]->zip;?>",
  	"telephone": "Location_phone #"
  },
  "name":"<?php echo $selected_location[0]->business;?>",
  
  "description": "<?php echo substr ( strip_tags( $selected_location[0]->aboutus_short) , 0 ,155 ); ?>",
  "url":"https://www.scanflip.com/Merchant_location_URL",
  "logo":"https://www.scanflip.com/Merchant_Business_Logo",
  "photo" : {
	"@type" : "ImageObject",
	"url":["https://www.scanflip.com/Merchant_location_URL","https://www.scanflip.com/Merchant_location_URL"]
  },
  "aggregateRating": {
    "@type": "AggregateRating",
     "ratingValue": "4",
    "ratingCount": "48"
  },
  "review": [
    {
      "@type": "Review",
	  "author": "Ellie",
      "datePublished": "2011-04-01",
      "description": "The lamp burned out and now I have to replace it.",
	  "reviewRating": {
        "@type": "Rating",
        "bestRating": "5",
        "ratingValue": "2",
        "worstRating": "1"
      }
	 },
	 {
      "@type": "Review",
      "author": "Lucas",
      "datePublished": "2011-03-25",
      "description": "Great microwave for the price. It is small and fits in my apartment.",
      "reviewRating": {
      "@type": "Rating",
      "bestRating": "5",
      "ratingValue": "4",
	  "worstRating": "1"
	  }
      } ],
  "priceRange": "$$",
  "paymentAccepted": "Cash,Credit Card",
    "openingHours": [
    "Mo-Sa 11:00-14:30",
    "Mo-Th 17:00-21:30",
    "Fr-Sa 17:00-22:00"
  ],
   "makesOffer":[{
	"@type": "Offer",
	"category": "Offer_category",
	"availabilityStarts": "DateTime",
	"availabilityEnds" : "DateTime",
	"name" : "Campaign Title",
	"image" : "Campaign Image URL",
	"url" : "campaign page URL"
	}]
  
 }
</script>



<style>

.info{ width:100%; float:left; padding:15px; margin-bottom: 5px;}
.info img ,.success img ,.warning img ,.error img{ float:left; }
.msgClass{
    /*width: 700px !important; 
    height: 500px !important; */
}
</style>
<style type="text/css" title="currentStyle">
			@import "<?php echo ASSETS_CSS ?>/c/demo_page.css";
			@import "<?php echo ASSETS_CSS ?>/c/demo_table.css";
		</style>
		<style>
		 .innerContainer {

        border-bottom-left-radius: 0px;
        border-bottom-right-radius: 0px;
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;
    }
		.dataTables_length{
		display:none;
		}
		
				</style>
<!--<script src="<?php echo WEB_PATH ?>/js/jquery-1.9.0.min.js" type="text/javascript"></script>-->
<script type='text/javascript' src="https://maps.google.com/maps/api/js?sensor=true"></script>
<script type='text/javascript' src="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/c/jquery.dataTablesnextprevious.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/c/history.js"></script>

<script type="text/javascript">

try{

function call_pricerange_popup()
{
	jQuery(".pricerange_text").toggle(function(){
			//jQuery(".pricerangebox").css("display","block");
			jQuery(this).prev().css("display","block");
		},
		function () {
			//jQuery(".pricerangebox").css("display","none");
			jQuery(this).prev().css("display","none");
		}
		);
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

jQuery(document).ready(function(){
	
	var hashval = window.location.hash ;
	hashval = unescape(hashval);
	 hashval1  = hashval.substring(1,(hashval.length ));
	/**/
	
	/*if(hashval!="")
	{
	//alert("in");
	}
	else
	{
		jQuery("li#tab-type[class='div_offers'] a").trigger("click");
	} */
	if(jQuery("li#tab-type[data-hashval='"+hashval1+"']").find("a").length != 0)
	{
	jQuery("li#tab-type[data-hashval='"+hashval1+"']").find("a").trigger("click");
	var errorDiv = $('#div_reviews');
			 setTimeout( function(){
				var div_name = jQuery("li#tab-type[data-hashval='"+hashval1+"']").attr("class");
			  var p = $('#'+div_name).position();
	var ptop = p.top;
	var pleft = p.left;

$(window).scrollTop(ptop);

			 },500 );
			 call_pricerange_popup();
			
	}
	else
	{
		
		jQuery("li#tab-type[class='div_offers'] a").trigger("click");
		
	hashval  = hashval.substring(2,(hashval.length -1));
	
            if(hashval.length != 0)
            {
				jQuery("#hdn_reload_pre_selection_loc").val(hashval);
				if(jQuery("#hdn_reload_pre_selection_loc").val() != "" )
				{
					hashval = jQuery("#hdn_reload_pre_selection_loc").val();
					var split_arr = hashval.split("|");
					var campid = split_arr[0];
					var locid = split_arr[1];
					jQuery(".deal_blk[campid='"+campid+"'][locid='"+locid+"'] .dealtitle").trigger("click");

				}
			}
	}
});
   var visited_arr = new Array();
     function try1(val,campid,locid) {
          //  alert("In"+campid+"=="+locid);
            //for (var prop in markerArray) {
              //                       markerArray[prop].setIcon('./images/pin-small.png');
                //            }
           
           // infowindow.setContent(infowindowcontent[locid]);
            //markerArray[locid].setIcon('./images/pin-small-blue.png'); 
            //infowindow.open(map,markerArray[locid]);
			
			var arr = [];
			var obj = {};
			  obj['href'] =  $(val).attr('mypopupid');
			  arr.push(obj);
			  var org_text = $(val).attr('mypopupid');
			  
			//  alert($(val).parent().parent('.deal_blk'));
			 var index_of_cur = $(val).parent().parent('#locationarea_'+locid+' .deal_blk').index();
			 
			 var tot_element = ($('#locationarea_'+locid+' .deal_blk').length);
			
			 //alert(index_of_cur);
			 //alert(tot_element);
			 //alert(locid);
			 for(var k = index_of_cur ;k < tot_element ;k++)
			 {
				 var obj = {};
				if(org_text != $('#locationarea_'+locid+' .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid'))
				{
				 obj['href'] = $('#locationarea_'+locid+' .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid');
			     //alert($(this).find('.dealtitle').attr('mypopupid')); 
				 //alert(obj['href']);
				 arr.push(obj);
				}  
			 }
			 for(var k = 0 ;k < index_of_cur ;k++)
			 {
				 var obj = {};
				if(org_text != $('#locationarea_'+locid+' .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid'))
				{
				 obj['href'] = $('#locationarea_'+locid+' .deal_blk:eq(' + k + ')').find('.dealtitle').attr('mypopupid');
			   /*  alert($(this).find('.dealtitle').attr('mypopupid')); */
				 arr.push(obj);
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
					$(".fancybox-inner").css ("overflow","hidden");
                                      ct = getParam( 'cust_id' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                    //alert(ct);
                                    if(ct  != 0)
                                        {
//                                     campid =  getParam( 'campid' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
//                                    ct = getParam( 'cust_id' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
//                                    locid = getParam( 'locationid' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
//                                    visited_c = getParam( 'visited' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
//                                    if( !in_array(campid,visited_arr)){
//                                     visited_arr.push(campid);
//                                    //alert(visited_arr.length);
//                                    if(visited_c == 0){
//                                    
//                                    url_d = $(this).attr('href');
//                                     url_d = url_d.replace(/(visited=)[^\&]+/, '$1' + "1");
//                            jQuery(".displayul .deal_blk[locid='"+locid+"'][campid='"+ campid+"'] .dealtitle").attr('mypopupid',url_d);
//                            v_c = $("#visited_counter").val();
//                            n_v_c = parseInt(v_c)+ 1;
//                            $("#visited_counter").val(n_v_c);
//                                    }
//                                    
//                                     }
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
                            jQuery(".displayul .deal_blk[locid='"+locid+"'][campid='"+ campid+"'] .dealtitle").attr('mypopupid',url_d);
                          //  alert( jQuery(".displayul .deal_blk[campid='"+ campid+"'] .dealtitle").length);
                                    jQuery(".displayul .deal_blk[campid='"+ campid+"'] .dealtitle").each(function(){
                                        url_d = $(this).attr('mypopupid');
                                     url_d = url_d.replace(/(visited=)[^\&]+/, '$1' + "1");
                                             $(this).attr('mypopupid',url_d);
                                    });
                                    jQuery(".displayul_all .deal_blk[campid='"+ campid+"'] .dealtitle").each(function(){
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
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'<?php echo WEB_PATH; ?>/campaign_view_ajax=true&campaign_id='+campid+'&customer_id='+ct,
                  async:false,
		  success:function(msg)
		  {
//                      alert(msg);
                  }
              });
                                        }          
                                },
                                beforeShow:function(){
				$(".fancybox-inner").css ("overflow","hidden");
									//$(".fancybox-inner").addClass("Class_fancy_ie");
									//alert(this.href);
                                   //alert($(this).attr('href')+"==="+decodeURI($(this).attr('href') ));
                                 //  jQuery(".fancybox-wrap").fadeOut("slow");
                                  // jQuery(".fancybox-wrap").fadeIn("slow");
                                    
				      jQuery(".fancybox-inner .popupmainclass").css("display","block");
                                    jQuery(".fancybox-inner .email_popup_div").css("display","none");
                                    jQuery(".fancybox-inner .mainloginclass").css("display","none");
                                      jQuery(".fancybox-inner  .errormainclass").css("display","none");
                                    jQuery(".fancybox-inner  .successmainclass").css("display","none");
                                      //    alert(decodeURIComponent($(this).attr('href') )); 
                                            var ct =getParam( 'CampTitle' , decodeURI($(this).attr('href') ));
                                            
                                    ct = unescape(ct );
                                        ct = ct.replace(/\+/g," ");
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
                                    
                                    if(ct  == 0)
                                        {
                                         
                                                   jQuery('.fancybox-inner .popup_sharediv').detach();
                                                   jQuery('.fancybox-inner .popup_sharetext').html('Please <a href="javascript:void(0)" id="Notificationlogin" >Login</a> to share and reserve this offer.');
                                        }
                                        else{
                                                     jQuery('.fancybox-inner .popup_sharetext').html(" Share to Earn Points Go for it.");
                                              
                                        }
                                        
                                      var dci =   getParam( 'decoded_cust_id' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," "); 
                                      
                                       var imgsrc =   getParam( 'img_src' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," "); 
                                        
                                       //var summary =  getParam( 'deal_desc' , unescape(decodeURI($(this).attr('href')) )).replace(/\+/g," ");
									   
									   var summary =  unescape(getParam( 'deal_desc' , decodeURI($(this).attr('href')) )).replace(/\+/g," ");
									   
                                          var webpath =  getParam( 'webpath' , unescape(decodeURI($(this).attr('href')) )).replace(/\+/g," ");
                                       
                                          
                                          //For facebook
                                       var th_link = webpath+"/register.php?campaign_id="+campid+"&l_id="+locid+"&share=true&customer_id="+dci;
                                       ct= getParam('fblink',$(this).attr('href') );
                                       //jQuery(".fancybox-inner .btn_share_facebook").attr("onClick","facebookfunction('"+campaign_title+"','"+summary+"','"+th_link+"','"+imgsrc+"')");
                                       //jQuery(".fancybox-inner .btn_share_facebook").attr("onClick","window.open('http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - "+encodeURIComponent(campaign_title)+"&p[summary]="+encodeURIComponent(summary)+"&p[url]="+encodeURIComponent(th_link)+"&p[images][0]="+imgsrc+"', 'sharer', 'toolbar=0,status=0,width=548,height=325')");
                                       //jQuery(".fancybox-inner .btn_share_facebook").attr("onClick","facebookfunction('"+campaign_title+"' ,'" +summary + "' ,'"  +th_link+"' ,'"+imgsrc+"')");
                                       
									   // 01 10 2013
					//					jQuery(".fancybox-inner .btn_share_facebook").attr("onClick","facebookfunction('"+escape(campaign_title)+"','"+escape(summary)+"','"+th_link+"','"+imgsrc+"')");
                                        //					
										// 01 10 2013
										
                                        // for facebook
                                        
                                       jQuery(".fancybox-inner .btn_share_facebook").attr("id","facebook_"+campid+"_"+locid);
                                jQuery(".fancybox-inner .btn_share_facebook").attr("data",campid+'###'+locid+'###'+business_name+'###'+campaign_title+'###'+summary+'###'+th_link+'###'+imgsrc+'###'+location_address+'###'+sharepoint+'###'+campaign_tag);
                                        
                                       // For tweeter
                                       var th_link = webpath+"/register.php?campaign_id="+campid+"&l_id="+locid+"&share=true&customer_id="+dci;
                                       ct= getParam('fblink',$(this).attr('href') );
//                                       <a data-count="none" data-lang="en" class="twitter_link" url="http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D" href="https://twitter.com/intent/tweet?original_referer=http%3A%2F%2Fwww.scanflip.com%2Fpopup_for_mymerchant.php%3FCampTitle%3DKfc-private-kfc-toronto-camp3%26businessname%3DKFC%26number_of_use%3D3%26new_customer%3D0%26address%3DF4-1000%2BGerrard%2BSt%2BE%26city%3DToronto%26state%3DON%26country%3DUSA%26zip%3DM4M%25203G6%26redeem_rewards%3D12%26referral_rewards%3D12%26o_left%3D2%26expiration_date%3D2013-05-23%25205%3A04%3A00%2520AM%26img_src%3Dhttp%3A%2F%2Fwww.scanflip.com%2Fmerchant%2Fimages%2Fdefault_campaign.jpg%26campid%3D549%26locationid%3D91%26deal_desc%3Ddgdf&amp;text=ScanFlip%20%7C%20Campaign&amp;tw_p=tweetbutton&amp;url=http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D">Twitter</a> 
//2-12-2013
//jQuery(".fancybox-inner .twitter_link").attr("url",encodeURIComponent(th_link));
//end 2-12-2013
                                 //   jQuery(".fancybox-inner .twitter_link").attr("href","http://twitter.com/share?url="+encodeURIComponent(th_link)+"&text="+encodeURIComponent(campaign_title));
                                   //   jQuery(".fancybox-inner .twitter_link").attr("href","http://twitter.com/share?url="+encodeURIComponent('http://www.joyofkosher.com')+"&text=Google+Offers+-+$69+for+4+days/3+nights+in+Orlando+($637+value)");
                                  // jQuery(".fancybox-inner .twitter_link").attr("href","https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20&tw_p=tweetbutton&url="+encodeURIComponent(th_link))
                                // jQuery(".fancybox-inner .twitter_link").attr("onClick","window.open('https://twitter.com/intent/tweet?original_referer="+webpath+"&text=ScanFlip%20Offer%20-%20"+encodeURIComponent(campaign_title)+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link)+"')");
        //2-12-2013                        
        //jQuery(".fancybox-inner .twitter_link").attr("onClick","twitterfunction('"+webpath+"','"+campaign_title+"','"+th_link+"')");
	//End 2-12-2013							
								// 01 10 2013
                                                                //2-12-2013
								//jQuery(".fancybox-inner .twitter_link").attr("onClick","twitterfunction('"+webpath+"','"+escape(campaign_title)+"','"+th_link+"')");
                                                               //2-12-2013 
                                jQuery(".fancybox-inner .twitter_link").attr("id","facebook_"+campid+"_"+locid);
                                jQuery(".fancybox-inner .twitter_link").attr("data",campid+'###'+locid+'###'+business_name+'###'+campaign_title+'###'+summary+'###'+th_link+'###'+imgsrc+'###'+location_address+'###'+sharepoint+'###'+campaign_tag);
                                                           
								// 01 10 2013
								//alert(campid);
								
                                // 
// 
     // For tweeter
                                        
                                        campid =  getParam( 'campid' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                                        locid = getParam( 'locationid' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
										
										//jQuery(".fancybox-inner .google_plus_link").attr("onClick","google_sharing('"+campid+"','"+locid+"','true','<?php echo $cust_id_with_url;?>')");
										
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
							
										
										var fb_redirect_url = jQuery(".fancybox-inner .btn_facebook").attr("href");
										jQuery(".fancybox-inner .btn_facebook").attr("href",fb_redirect_url);
										setCookie("redirecting_url_fb_location","<?php echo WEB_PATH ?>/location-detail.php#@"+campid+"|"+locid+"@",365);
										
									//	alert(getCookie("redirecting_url_fb_location")+"alert cookie value ");
                                            jQuery(".fancybox-inner #hdn_reload_pre_selection_loc").val(campid+"#"+locid);
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
                                                 //    alert( ct1+"barcode");
                                                   $(".fancybox-inner .barcode").attr("src","<?php echo WEB_PATH; ?>/showbarcode.php?br="+ct1);
                                                    $(".fancybox-inner .btn_mymerchantreserve").detach();
                            $(".fancybox-inner .sharediv").css("display","block");
                            $(".fancybox-inner .showvocherdiv").css("display","none");
                            $(".fancybox-inner #ShowshareId").css("display","none");
                            $(".fancybox-inner #ShowVoucherId").css("display","block");
			    $(".fancybox-inner #saveofferid").show();
                             oleft = getParam( 'o_left' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                             if(parseInt(oleft) <=0)
                                 {
                                     jQuery(".fancybox-inner .popupoleft1").css("display","none");
                                 }
                           // jQuery(".fancybox-inner .popupoleft1").css("display","none");
                          //  jQuery(".fancybox-inner .printclass").css("display","block");
                                              }
                                          }
                                        
                                      //  alert(jQuery('.fancybox-inner .email_popup_div #reffer_campaign_id').length);
                                        jQuery('.fancybox-inner .email_popup_div #reffer_campaign_id').val(campid);
                                      //  alert(jQuery('.fancybox-inner .email_popup_div #reffer_campaign_id').val());
                                        jQuery('.fancybox-inner .email_popup_div #reffer_campaign_name').val(camptitle);
                                         jQuery('.fancybox-inner .email_popup_div #refferal_location_id').val(locid);
                                         
                                         /* For reserve button */
      var ar =jQuery("#rservedeals_value").val();
    //alert(ar);
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
                                         
                                         oleft = getParam( 'o_left' , decodeURIComponent($(this).attr('href') )).replace(/\+/g," ");
                             if(parseInt(oleft) <=0)
                                 {
                                     jQuery(".fancybox-inner .popupoleft1").css("display","none");
                                 }
                                        bind_popup_event();
                                        
                                },
                                beforeLoad:function(){
                                 //   alert($(this).attr('href'));
                                    
                                },
				afterLoad: function(){ 
                                     var ct =getParam( 'CampTitle' , $(this).attr('href') );
                                    jQuery('.fancybox-inner .CampTitle').html(ct);
                                     //alert(jQuery('.fancybox-inner .CampTitle').html());
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
                                     markerArray[prop].setIcon('/assets/images/c/pin-small.png');
                                    }
           
                                    /*infowindow.setContent(infowindowcontent[$loc_id[1]]);
                                    markerArray[$loc_id[1]].setIcon('./images/pin-small-blue.png'); 
                                    infowindow.open(map,markerArray[$loc_id[1]]);
                                  */
                                },
                                afterClose:function(){
									jQuery('script[src^="https://apis.google.com/js/plusone.js"]').remove();
								delete_cookie("redirecting_url_fb");
				delete_cookie("redirecting_url_fb_location");
                                     v_c = $("#visited_counter").val();
                                      m_c = $("#m_counter").html();
                                      t_c = parseInt(m_c) - parseInt(v_c);
                                       $("#m_counter").html(t_c);
                                        if(t_c == 0){
                                           $("#m_counter").css("display","none");
                                       }
                                  $("#visited_counter").val("0");
                                      jQuery(".fancybox-inner .popupmainclass").css("display","block");
                                    jQuery(".fancybox-inner .email_popup_div").css("display","none");
                                    jQuery(".fancybox-inner .mainloginclass").css("display","none");
                                      jQuery(".fancybox-inner  .errormainclass").css("display","none");
                                    jQuery(".fancybox-inner  .successmainclass").css("display","none");
                                   selected_cat_id=getCookie("cat_remember");
                           miles_cookie=getCookie("miles_cookie");
                     //   alert(jQuery("#hdn_reserve_err").val());
                                  if(jQuery("#hdn_reserve_err").val() != "")
                                      {
                                         /*
                                    if(jQuery(".showalld").length != 0)
                                    {
                                        l = jQuery("#hdn_reserve_err").val();
                                            if(jQuery(".displayul .deal_blk[locid='"+l+"']").length == 0){
                                                     markerArray[l].setVisible(false);
                                                      infowindow.close();
                                               }
                                           //filter_deals_algorithm_by_location(selected_cat_id,miles_cookie,l);
                                            }else{
                                                 filter_deals_algorithm(selected_cat_id,miles_cookie);
                                            }*/
                                             jQuery("#hdn_reserve_err").val("");
                                      }
                                //alert("in close");
                            if(jQuery("#rservedeals_value").val()!= ''){
                           /*
			   if(jQuery(".showalld").length != 0)
                            {
                                 jQuery(".displayul .deal_blk").each(function(){
                                    l = jQuery(this).attr("locid") ;
                                 });
                                    a =  jQuery("#rservedeals_value").val().split(";");
                                   for(i=0;i<a.length;i++){
                                       b= a[i].split(":");
                                       jQuery(".displayul_all .deal_blk[locid='"+b[1]+"'][campid='"+b[0]+"']").detach();
                                       jQuery(".displayul .deal_blk[locid='"+b[1]+"'][campid='"+b[0]+"']").detach();
                                        
                                   }
                                        if(jQuery(".displayul .deal_blk[locid='"+l+"']").length == 0){
                                             markerArray[l].setVisible(false);
                                              infowindow.close();
                                       }
                                   //filter_deals_algorithm_by_location(selected_cat_id,miles_cookie,l);
                            }else{
                                a =  jQuery("#rservedeals_value").val().split(";");
                                   for(i=0;i<a.length;i++){
                                       b= a[i].split(":");
                                       jQuery(".displayul_all .deal_blk[locid='"+b[1]+"'][campid='"+b[0]+"']").detach();
                                       jQuery(".displayul .deal_blk[locid='"+b[1]+"'][campid='"+b[0]+"']").detach();
                                      
                                   }
                                    filter_deals_algorithm(selected_cat_id,miles_cookie);
                                    
                            }*/
                            jQuery("#rservedeals_value").val("");
                                }
                                }
			}); // fancybox
                       
                        var top = (jQuery(window).height() / 2) - (jQuery(".fancybox-wrap").outerHeight() / 2);
                       
    var left = (jQuery(window).width() / 2) - (jQuery(".fancybox-wrap").outerWidth() / 2);
     
    jQuery(".fancybox-wrap").css({ top: top, left: left});
                        //jQuery('.fancybox-wrap').css('margin-top', '200px');
                  //      bind_reserve_event();
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
}
catch(e)
{
}
</script>
<style>.menuBody{overflow-y:auto;}</style>
<script type="text/javascript" src="<?php echo ASSETS; ?>/raty/jquery.raty.min.js"></script>
</head>

<body  onload="checkCookie()" class="locationdetailpage" >
  <?
		require_once(CUST_LAYOUT."/header.php");
		?>

<div id="dialog-message" title="Message Box" style="display:none">

    </div>    
<div id="content" class="cantent">
		<div class="my_main_div">
			<div id="contentContainer" class="contentContainer">
        <?php 
		/*
		if(isset($_COOKIE['mycurrent_lati']) && isset($_COOKIE['mycurrent_long'])) 
		{
        */ 

		if(isset($_COOKIE['mycurrent_lati']) && isset($_COOKIE['mycurrent_long'])) 
		{
		}
		else
		{
			//echo WEB_PATH.'/process.php?get_location_details=yes&location_id='.$decode_location_id;
			/* $arr_loc=file(WEB_PATH.'/process.php?get_location_details=yes&location_id='.$decode_location_id);
			if(trim($arr_loc[0]) == "")
			{
				unset($arr_loc[0]);
				$arr_loc = array_values($arr_loc);
			}
			$all_json_str = $arr_loc[0];                   
			$json_all_loc = json_decode($arr_loc[0]);
			$loc_record = $json_all_loc->records; */
			//print_r($loc_record);

			//$dismile=0;	
			$mlatitude=$selected_location[0]->latitude;
			$mlongitude=$selected_location[0]->longitude;
		}
		
       
		
//echo 1;
             if(isset($_COOKIE['miles_cookie']))
                {
                 $dismile=$_COOKIE['miles_cookie'];
                }
                else{
                    $dismile=50 ;
                }
                 //echo WEB_PATH.'/process.php?btnGetmarkerforLocations=yes&dismile='.$dismile.'&mlatitude='.$mlatitude.'&mlongitude='.$mlongitude.'&category_id=0'.'&customer_id='.$_SESSION['customer_id'];
               //  exit;
                 $array_where1 = array();
                 
	
				if(isset($_SESSION['customer_id']))
				{
					$cust_s = $_SESSION['customer_id'];
				}
				else{
					$cust_s = "";
				}
				//$cust_s="a";
				
				if(isset($_COOKIE['mycurrent_lati']) && isset($_COOKIE['mycurrent_long']))
				{
				}
				else
				{
					 $dismile=0 ;
				}
				
				/* 19-03-2014 display locations within 50 miles from location's lat lng  */
				$mlatitude =$selected_location[0]->latitude;
				$mlongitude = $selected_location[0]->longitude;
				/* 19-03-2014 display locations within 50 miles from location's lat lng  */
				
            
			
                        ?>
    <?php
define("MAP_WIDTH","530px");
define("MAP_HEIGHT","300px");

include_once(LIBRARY."/GoogleMap.php");
include_once(LIBRARY."/JSMin.php");

$MAP_OBJECT = new GoogleMapAPI(); $MAP_OBJECT->_minify_js = isset($_REQUEST["min"])?FALSE:TRUE;

?>
<?php 

$where_clause = array();

$location_str = "";
?>

<script type='text/javascript'>
try{
function setCookie(c_name,value,exdays)
	{
     var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
//Bahman 09/29/14 changed ;path=/scanflip; to ;path=;
	var c_value=  escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString())+";path=;";
	document.cookie=c_name + "=" + c_value;
	}
function delete_cookie(name)
{
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
	var map = null;
				var markerArray = []; //create a global array to store markers
                               /// var locations = [];
				delete_cookie("redirecting_url_fb");
				delete_cookie("redirecting_url_fb_location");
				var locations = [
				<?
                              $loc_arr = array();

						$count = 1;$total =0;
						$locationdetailhtml="";
                                 foreach($records_array1 as $Row)
                                                {
												
                                         $storeid = $Row->locid;
                      
            
                    
                     if(isset($_SESSION['customer_id']))
                    {
                        /*  $array_where_camp2['campaign_id'] = $Row->cid;
                    $array_where_camp2['customer_id'] = $_SESSION['customer_id'];
                    $array_where_camp2['location_id'] = $storeid;
                    $RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
                    //echo $RS_cust_camp->RecordCount()."-";
                    $reserved=$RS_cust_camp->RecordCount();
                    
                    
                    $array_where_camp['campaign_id'] = $Row->cid;
                    $array_where_camp['customer_id'] = $_SESSION['customer_id'];
                    $array_where_camp['referred_customer_id'] = 0;
                    $array_where_camp['location_id'] = $storeid;
                    $RS_camp = $objDB->Show("reward_user", $array_where_camp);
                    //echo $RS_camp->RecordCount()."-";
                    //echo "bb".$Row->number_of_use."-";
                    $redeemed=$RS_camp->RecordCount();
                    
                    $array_where_camp1['campaign_id'] = $Row->cid;
                    $array_where_camp1['location_id'] = $storeid;
                    $RS_camp1 = $objDB->Show("campaign_location", $array_where_camp1);
                    //echo $RS_camp1->fields['offers_left']."-";
                    $offer_left=$RS_camp1->fields['offers_left'];
                         //echo "b";
                        if($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && $Row->number_of_use==1)
                         {
                            //echo "1 ".$Row->cid." ".$storeid;
                         }                       
                         elseif($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && ($Row->number_of_use==2 || $Row->number_of_use==3) && $RS_camp1->fields['offers_left']==0)
                         {
                             //echo "2 ".$Row->cid." ".$storeid;
                         }
                         elseif($RS_cust_camp->RecordCount()==0 && $RS_camp->RecordCount()==0 && $RS_camp1->fields['offers_left']==0)
                         {   
                             //echo "3 ".$Row->cid." ".$storeid;
                         } */
                         if(1)//else
                         {
                              if(!in_array($Row->locid ,$loc_arr)){
                                         array_push($loc_arr,$Row->locid );
                                         
                                         // start location details div 17 12 2013
							
							if($Row->locid == $decode_location_id)
							{
								$display_css = "block";
							}
							else
							{
								$display_css = "none";
							}
							
							$detail="false";
							$locationdetailhtml.='<div id="locationdetailinfo_'.$Row->locid.'" style="display:'.$display_css.'" class="locationdetailinfo">';			
							$locationdetailhtml.='<ul>';
							
							if($Row->dining!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Dining Options :';
								$locationdetailhtml.='<span>'.$Row->dining.'</span></li>';
							}
							if($Row->reservation!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Takes Reservation :';
								$locationdetailhtml.='<span>'.$Row->reservation.'</span></li>';
							}
							
							if($Row->takeout!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Takeout :';
								$locationdetailhtml.='<span>'.$Row->takeout.'</span></li>';
							}
							if($Row->good_for!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Good For :';
								$locationdetailhtml.='<span>'.$Row->good_for.'</span></li>';
							}
							if($Row->pricerange!="0" && $Row->pricerange!="")
							{
								$detail="true";
								if($Row->pricerange==1)
								{
									$val_text="$";
								}
								else if($Row->pricerange==2)
								{		
									$val_text="$$";
								}
								else if($Row->pricerange==3)
								{
									$val_text="$$$";
								}
								else if($Row->pricerange==4)
								{
									$val_text="$$$$";
								}				
								
									
								$locationdetailhtml.='<li >';
								$locationdetailhtml.='<div style="display: none;" class="pricerangebox">';
								$locationdetailhtml.='$ = Inexpensive, Under $10';
								$locationdetailhtml.='		<br>';
								$locationdetailhtml.='		$$ = Moderate, $11 - $30';
								$locationdetailhtml.='		<br>';
								$locationdetailhtml.='		$$$ = Expensive, $31 - $60';
								$locationdetailhtml.='		<br>';
								$locationdetailhtml.='		$$$$ = Very Expensive, Above $61';
								$locationdetailhtml.='</div>';
								$locationdetailhtml.='Price Range :';
								$locationdetailhtml.='<span class="pricerange_text" id="pricerange_text">'.$val_text.'</span></li>';
							}
							if($Row->parking!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Parking :';
								$locationdetailhtml.='<span>'.$Row->parking.'</span></li>';
							}
							if($Row->wheelchair!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Wheelchair Accessible :';
								$locationdetailhtml.='<span>'.$Row->wheelchair.'</span></li>';
							}
							if($Row->payment_method!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Payment Method :';
								$locationdetailhtml.='<span>'.$Row->payment_method.'</span></li>';
							}
							if($Row->minimum_age!="" && $Row->minimum_age!="0")
							{
								$detail="true";	
								$locationdetailhtml.='<li>Minimum Age Restriction :';

								$locationdetailhtml.='<span>'.$Row->minimum_age.' years to enter the location.</span></li>';
								
							}
							if($Row->pet!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Pet Allowed :';
								$locationdetailhtml.='<span>'.$Row->pet.'</span></li>';
							}
							
							if($Row->ambience!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Ambience :';
								$locationdetailhtml.='<span>'.$Row->ambience.'</span></li>';;
							}
							if($Row->attire!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Attire :';
								$locationdetailhtml.='<span>'.$Row->attire.'</span></li>';
							}
							if($Row->noise_level!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Noise level :';
								$locationdetailhtml.='<span>'.$Row->noise_level.'</span></li>';
							}

							if($Row->wifi!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Wifi :';
								$locationdetailhtml.='<span>'.$Row->wifi.'</span></li>';
							}
							if($Row->has_tv!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Has TV :';
								$locationdetailhtml.='<span>'.$Row->has_tv.'</span></li>';
							}
							if($Row->airconditioned!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Airconditioned :';
								$locationdetailhtml.='<span>'.$Row->airconditioned.'</span></li>';
							}
							if($Row->smoking!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Smoking :';
								$locationdetailhtml.='<span>'.$Row->smoking.'</span></li>';
							}
							if($Row->alcohol!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Alcohol :';
								$locationdetailhtml.='<span>'.$Row->alcohol.'</span></li>';
							}			
							
							if($Row->will_deliver!="")
							{
								$detail="true";
									$locationdetailhtml.='<li>Will Deliver :';
									$locationdetailhtml.='<span>'.$Row->will_deliver;
									if($Row->will_deliver=="Yes")
									{										
										$m_o="";
										$d_a="";
										if($Row->minimum_order!="" && $Row->minimum_order!="0")
										{
											//$locationdetailhtml.='Minimum Order : $ '.$Row->minimum_order;
											$m_o='Minimum Order : $ '.$Row->minimum_order;
										}
										if($Row->deliveryarea_from!="" && $Row->deliveryarea_to!="" && $Row->deliveryarea_from!="0" && $Row->deliveryarea_to!="0")
										{
											//$locationdetailhtml.='Delivery Area : '.$Row->deliveryarea_from.' to '.$Row->deliveryarea_to;
											$d_a='Delivery Area : '.$Row->deliveryarea_from.' to '.$Row->deliveryarea_to;
										}
										if($m_o !="" && $d_a !="")
										{
											$locationdetailhtml.=' ( Minimum Order : $ '.$Row->minimum_order;
											$locationdetailhtml.=', Delivery Area : '.$Row->deliveryarea_from.' to '.$Row->deliveryarea_to.')';
										}										
										else if($m_o !="" && $d_a =="")
										{
											$locationdetailhtml.=' ( Minimum Order : $ '.$Row->minimum_order.')';
										}
										else if($m_o =="" && $d_a !="")
										{
											$locationdetailhtml.=' ( Delivery Area : '.$Row->deliveryarea_from.' to '.$Row->deliveryarea_to.')';
										}
									}
									$locationdetailhtml.='</span></li>';
							}
							if($Row->caters!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Caters :';
								$locationdetailhtml.='<span>'.$Row->caters.'</span></li>';
							}
							
							$locationdetailhtml.='</ul>';
							
							$locationdetailhtml.='<div class="services_amenities">';
							if($Row->services!="")
							{
								$detail="true";
								$locationdetailhtml.='<div class="services">';
								$locationdetailhtml.='<h4>Services :</h4>';
								$services_arr = explode(",",$Row->services);
								foreach($services_arr as $sa)
								{
									$locationdetailhtml.='<span>'.$sa.'</span>';
								}
								$locationdetailhtml.='</div>';
							}
							if($Row->amenities!="")
							{
								$detail="true";
								$locationdetailhtml.='<div class="amenities">';
								$locationdetailhtml.='<h4>Amenities :</h4>';
								$amenities_arr = explode(",",$Row->amenities);
								foreach($amenities_arr as $aa)
								{
									$locationdetailhtml.='<span>'.$aa.'</span>';
								}
								$locationdetailhtml.='</div>';
							}
							$locationdetailhtml.='</div>';
							
							if($detail=="false")
							{
								$locationdetailhtml.='<div style="padding:15px;font-weight:bold">'.$client_msg['location_detail']['Msg_no_locationdetail'].'</div>';
							}
							
							//$locationdetailhtml.='scanflip';
							$locationdetailhtml.='</div>';
							
							// end location details div	17 12 2013
                                                        
						$lat = $Row->latitude;
						$lon = $Row->longitude;
						$address = $Row->address.", ".$Row->city.", ".$Row->state.", ".$Row->zip.", ".$Row->country;
						$MARKER_HTML = "<div style='clear:both;width:auto;font:Arial, Helvetica, sans-serif;'>";
						$MARKER_HTML .= "<div>".$address."</div><br />";
						$MARKER_HTML .= "</div>";
                                                  if($Row->locid == $decode_location_id)
                      {
                          $display_css = "block";
                      }
                      else {
                          $display_css = "none";
                      }
					/*
					if($total_records1==1)
					{
						$location_str .= '<div class="address_det" id="addressinfo_'.$Row->locid.'" style="display:'.$display_css.'"><div class="my_loc_bckgnd_img1" loc_id="'.$Row->locid.'" >';
					}
					else
					{
						$location_str .= '<div class="address_det" id="addressinfo_'.$Row->locid.'" style="display:'.$display_css.'"><div class="my_loc_bckgnd_img" loc_id="'.$Row->locid.'" >';
					}
					*/
					$location_str .= '<div class="address_det" id="addressinfo_'.$Row->locid.'" style="display:'.$display_css.'"><div class="my_loc_bckgnd_img" loc_id="'.$Row->locid.'" >';
					
					 $location_str.='<input type="hidden" class="hdnlatlong" lat="'.$Row->latitude.'" long="'.$Row->longitude.'" />';
                   
                           // $location_str .= "<b>".$Row->location_name."</b><br/>";
                   if($Row->address!="")
                        $location_1 = $Row->address;
                    if($Row->city!="")
                         $location_2 = $Row->city;
                    if($Row->state!="")
                         $location_3 = $Row->state;
                    if($Row->country!="")
                         $location_4 = $Row->country;
                    if($Row->zip!="")
                         $location_5 = $Row->zip;
                    //echo "<br/>".$address ."<br />";
                    //$address = "<table><tr><td width='30%' style='vertical-align:top;'>Address : </td><td>".$row->address.",".$row->city.",<br/> ".$row->state.",".$row->zip.",".$RSLocation->fields['country']."<br/><td></tr></table>";
                    //echo $address;
                    //echo $row->website."<br />";
                    if($Row->phone_number != ""){
                        $phno = explode("-",$Row->phone_number);
                     $newphno = "(".$phno[1].") ".$phno[2]."-".$phno[3];
                       //  $location_str .= "<br/>".$newphno;
                    }
                   $location_str .= "<div  class='location_pg_addr'>";
						  //$location_str .= "<span>";
$location_str .= "<span >".$location_1."</span>,<br/>";
$location_str .= "<span >".$location_2."</span>,";
$location_str .= "<span >".$location_3."</span>, ";
$location_str .= "<span >".$location_4."</span>, ";
$location_str .= "<span >".$location_5 ."</span></span><br/>";
$location_str .= "<span >".$newphno."</span><br/>";
$location_str .= '</div>';
                    
                    
                    
                $from_lati=$_COOKIE['mycurrent_lati'];
                $from_long=$_COOKIE['mycurrent_long'];
				              
                $to_lati=$Row->latitude;
                
                $to_long=$Row->longitude;
              
                
                $deal_distance="";
                $deal_distance.="</div>";
                $share_link = "";
                  /*website code */ 
                     $deal_distance .= "<div class='websiteinfo'>";
                    if($Row->website != "")
                    {
                        $share_link .= "<a target='_blank' href='".$Row->website."' class='webiteclass'>Website </a>";
                    }
					else{
					   $share_link .= "<a  href='javascript:void(0)' class='grayoutclass'>Website </a>";
					}
					if($Row->facebook != "")
                    {
						$share_link .= "<a target='_blank' href='".$Row->facebook."' class='webiteclass'> | Facebook | </a>";
					}
					else
					{
						$share_link .= "<a  href='javascript:void(0)' class='grayoutclass'> | Facebook | </a>";
					}
					if($Row->google != "")
					{
						$share_link .= "<a target='_blank' href='".$Row->google."' class='webiteclass'>Google+</a>";
					}
					else
					{
						$share_link .= "<a  href='javascript:void(0)' class='grayoutclass'>Google+</a>";
					}
					 $deal_distance .=$share_link;
                     $deal_distance .= "</div>";
                    /*end of website code */
                $mile_distance="";
                $mile_distance.= "<div id='milesdiv'><b>Miles Away : </b>";
                    if($from_lati == $to_lati && $from_long == $to_long ){
                $mile_distance.=  "0 ";
                    }
                    else{
                         $mile_distance.= $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M") . " ";
                    }
					$mile_distance.="</div>";
                //$deal_distance.= $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M") . " ";
                
                //$maphref="http://maps.google.com/maps?saddr=".$from_lati.",".$from_long."&daddr=".$to_lati.",".$to_long;
				
				$maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;
                    
                   
                   
                                     
                if($from_lati!="" && $from_long!="")        
                    $location_str .= $deal_distance.$mile_distance;
			    else
				{
					$mile_distance = "<div id='milesdiv'><b>Miles Away : </b><a id='askmile'>Click here</div>";
					$mile_distance.= '<div id="enter_zipcode_div" style="display:none">
					<div  style="width:423px">
						<div class="zipcode_section" style="margin:10px;">
							<div style="margin-bottom:10px">
								Please enter your current location (City , Country) or Zip code:
							</div>
							<div>
								<input type="text" name="enter_zip" id="enter_zip" value="" onkeyup="popup_searchdeal(this.value,event)"/> 
								&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="button" name="searchdeal_zip" id="searchdeal_zip" value="OK" />
								<input type="button" name="searchdeal_zip_cancel" id="searchdeal_zip_cancel" value="Cancel" />
							</div>   
						</div>
					</div>
				</div>';
					$location_str .= $deal_distance.$mile_distance;
				}
				
                $location_str .= '<div class="gettdirection"><a href="'.$maphref.'" target="_blank">Get Directions</a> </div>';
                                    
                                     
                               
                    if($Row->picture!="")
                    {
                        $loc_image = ASSETS_IMG."/m/location/mythumb/".$Row->picture;
                    }
					/*
                    elseif($RS1->fields['merchant_icon']!="")
                    {
                            $loc_image = WEB_PATH."/merchant/images/icon/".$RS1->fields['merchant_icon'];
                         
                      
                    }
					*/
                    else 
                    {
                              $loc_image = ASSETS_IMG."/c/Merchant_Location.png";
                    }
                      
                                     
                                      if(!isset($_SESSION['customer_id'])){
            //	$MARKER_HTML .= '<a href="'.WEB_PATH.'/register.php?btnRegisterStore=1&location_id='.base64_encode($storeid).'" target="_parent">Subscribe to Store</a>&nbsp;&nbsp;';
            }else{
                $RS_user_subscribe = "select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$Row->locid." and subscribed_status=1";
                ///echo $RS_user_subscribe;
                $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);
                if($check_subscribe->RecordCount()==0)
                {
                   //$location_str.= "<a href='".WEB_PATH."/process.php?btnRegisterStore=1&page=location&location_id=".base64_encode($Row->locid)."' target='_parent' class='reserve_print' id='btn_reserve_deal'>Subscribe</a>&nbsp;&nbsp;<br/>";
                     $location_str .= "<div class='save_subscribe_icon'></div><div class='subscrib'><a href='javascript:void(0)' class='subscribestore reserve_print' s_lid1='".$Row->locid."' s_lid='".base64_encode($Row->locid)."'  >Subscribe</a><img class='sub_loader' src='".ASSETS_IMG."/c/24.GIF'></div>";
                   
                }
                else
                {
                   // $location_str.= "<a href='".WEB_PATH."/process.php?btnunsubscribelocation=1&page=location&location_id=".base64_encode($Row->locid)."' target='_parent' class='reserve_print' id='btn_reserve_deal'>Unsubscribe</a>&nbsp;&nbsp;<br/>";
                     $location_str .= "<div class='save_unsubscribe_icon'></div><div class='subscrib'><a href='javascript:void(0)' class='unsubscribestore reserve_print'  s_lid1='".$Row->locid."' s_lid='".base64_encode($Row->locid)."'  >Unsubscribe</a><img class='sub_loader' src='".ASSETS_IMG."/c/24.GIF'></div>";
                }
            }
             $location_str.="</div>"            ;
              if($count != 1) echo ",";
				?>
				["<?=$MARKER_HTML?>", <?=$lat?>, <?=$lon?>,<?=$Row->locid?>,'<?=$loc_image?>']
				<?
				if($count != $total) //echo ",";
							$count++;
                                     }
                         }
                    }else{
						
                         if(!in_array($Row->locid ,$loc_arr)){
						
                                         array_push($loc_arr,$Row->locid );
							
							// start location details div 17 12 2013
							
							if($Row->locid == $decode_location_id)
							{
								$display_css = "block";
							}
							else
							{
								$display_css = "none";
							}
							
							$detail="false";
							$locationdetailhtml.='<div id="locationdetailinfo_'.$Row->locid.'" style="display:'.$display_css.'" class="locationdetailinfo">';			
							$locationdetailhtml.='<ul>';
							
							if($Row->dining!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Dining Options :';
								$locationdetailhtml.='<span>'.$Row->dining.'</span></li>';
							}
							if($Row->reservation!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Takes Reservation :';
								$locationdetailhtml.='<span>'.$Row->reservation.'</span></li>';
							}
							
							if($Row->takeout!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Takeout :';
								$locationdetailhtml.='<span>'.$Row->takeout.'</span></li>';
							}
							if($Row->good_for!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Good For :';
								$locationdetailhtml.='<span>'.$Row->good_for.'</span></li>';
							}
							if($Row->pricerange!="0" && $Row->pricerange!="")
							{
								$detail="true";
								if($Row->pricerange==1)
								{
									$val_text="$";
								}
								else if($Row->pricerange==2)
								{		
									$val_text="$$";
								}
								else if($Row->pricerange==3)
								{
									$val_text="$$$";
								}
								else if($Row->pricerange==4)
								{
									$val_text="$$$$";
								}				
								
									
								$locationdetailhtml.='<li >';
								$locationdetailhtml.='<div style="display: none;" class="pricerangebox">';
								$locationdetailhtml.='$ = Inexpensive, Under $10';
								$locationdetailhtml.='		<br>';
								$locationdetailhtml.='		$$ = Moderate, $11 - $30';
								$locationdetailhtml.='		<br>';
								$locationdetailhtml.='		$$$ = Expensive, $31 - $60';
								$locationdetailhtml.='		<br>';
								$locationdetailhtml.='		$$$$ = Very Expensive, Above $61';
								$locationdetailhtml.='</div>';
								$locationdetailhtml.='Price Range :';
								$locationdetailhtml.='<span class="pricerange_text" id="pricerange_text">'.$val_text.'</span></li>';
							}
							if($Row->parking!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Parking :';
								$locationdetailhtml.='<span>'.$Row->parking.'</span></li>';
							}
							if($Row->wheelchair!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Wheelchair Accessible :';
								$locationdetailhtml.='<span>'.$Row->wheelchair.'</span></li>';
							}
							if($Row->payment_method!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Payment Method :';
								$locationdetailhtml.='<span>'.$Row->payment_method.'</span></li>';
							}
							if($Row->minimum_age!="")
							{
								$detail="true";	
								$locationdetailhtml.='<li>Minimum Age Restriction :';
								
								if($Row->minimum_age=="0")
								{
									$locationdetailhtml.='<span>None</span></li>';
								}
								else
								{
									$locationdetailhtml.='<span>'.$Row->minimum_age.' years to enter the location.</span></li>';
								}
								
							}
							if($Row->pet!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Pet Allowed :';
								$locationdetailhtml.='<span>'.$Row->pet.'</span></li>';
							}
							
							if($Row->ambience!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Ambience :';
								$locationdetailhtml.='<span>'.$Row->ambience.'</span></li>';;
							}
							if($Row->attire!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Attire :';
								$locationdetailhtml.='<span>'.$Row->attire.'</span></li>';
							}
							if($Row->noise_level!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Noise level :';
								$locationdetailhtml.='<span>'.$Row->noise_level.'</span></li>';
							}

							if($Row->wifi!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Wifi :';
								$locationdetailhtml.='<span>'.$Row->wifi.'</span></li>';
							}
							if($Row->has_tv!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Has TV :';
								$locationdetailhtml.='<span>'.$Row->has_tv.'</span></li>';
							}
							if($Row->airconditioned!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Airconditioned :';
								$locationdetailhtml.='<span>'.$Row->airconditioned.'</span></li>';
							}
							if($Row->smoking!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Smoking :';
								$locationdetailhtml.='<span>'.$Row->smoking.'</span></li>';
							}
							if($Row->alcohol!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Alcohol :';
								$locationdetailhtml.='<span>'.$Row->alcohol.'</span></li>';
							}			
							
							if($Row->will_deliver!="")
							{
								$detail="true";
									$locationdetailhtml.='<li>Will deliver :';
									$locationdetailhtml.='<span>'.$Row->will_deliver;
									if($Row->will_deliver=="Yes")
									{										
										$m_o="";
										$d_a="";
										if($Row->minimum_order!="" && $Row->minimum_order!="0")
										{
											//$locationdetailhtml.='Minimum Order : $ '.$Row->minimum_order;
											$m_o='Minimum Order : $ '.$Row->minimum_order;
										}
										if($Row->deliveryarea_from!="" && $Row->deliveryarea_to!="" && $Row->deliveryarea_from!="0" && $Row->deliveryarea_to!="0")
										{
											//$locationdetailhtml.='Delivery Area : '.$Row->deliveryarea_from.' to '.$Row->deliveryarea_to;
											$d_a='Delivery Area : '.$Row->deliveryarea_from.' to '.$Row->deliveryarea_to;
										}
										if($m_o !="" && $d_a !="")
										{
											$locationdetailhtml.=' ( Minimum Order : $ '.$Row->minimum_order;
											$locationdetailhtml.=', Delivery Area : '.$Row->deliveryarea_from.' to '.$Row->deliveryarea_to.')';
										}										
										else if($m_o !="" && $d_a =="")
										{
											$locationdetailhtml.=' ( Minimum Order : $ '.$Row->minimum_order.')';
										}
										else if($m_o =="" && $d_a !="")
										{
											$locationdetailhtml.=' ( Delivery Area : '.$Row->deliveryarea_from.' to '.$Row->deliveryarea_to.')';
										}
									}
									$locationdetailhtml.='</span></li>';
							}
							if($Row->caters!="")
							{
								$detail="true";
								$locationdetailhtml.='<li>Caters :';
								$locationdetailhtml.='<span>'.$Row->caters.'</span></li>';
							}
							
							$locationdetailhtml.='</ul>';
							
							$locationdetailhtml.='<div class="services_amenities">';
							if($Row->services!="")
							{
								$detail="true";
								$locationdetailhtml.='<div class="services">';
								$locationdetailhtml.='<h4>Services :</h4>';
								$services_arr = explode(",",$Row->services);
								foreach($services_arr as $sa)
								{
									$locationdetailhtml.='<span>'.$sa.'</span>';
								}
								$locationdetailhtml.='</div>';
							}
							if($Row->amenities!="")
							{
								$detail="true";
								$locationdetailhtml.='<div class="amenities">';
								$locationdetailhtml.='<h4>Amenities :</h4>';
								$amenities_arr = explode(",",$Row->amenities);
								foreach($amenities_arr as $aa)
								{
									$locationdetailhtml.='<span>'.$aa.'</span>';
								}
								$locationdetailhtml.='</div>';
							}
							$locationdetailhtml.='</div>';
							
							if($detail=="false")
							{
								$locationdetailhtml.='<div style="padding:15px;font-weight:bold">'.$client_msg['location_detail']['Msg_no_locationdetail'].'</div>';
							}
							
							//$locationdetailhtml.='scanflip';
							$locationdetailhtml.='</div>';
							
							// end location details div	17 12 2013
										 
						$lat = $Row->latitude;
						$lon = $Row->longitude;
						$address = $Row->address.", ".$Row->city.", ".$Row->state.", ".$Row->zip.", ".$Row->country;
						$MARKER_HTML = "<div style='clear:both;width:auto;font:Arial, Helvetica, sans-serif;'>";
						$MARKER_HTML .= "<div>".$address."</div><br />";
						$MARKER_HTML .= "</div>";
                                                  if($Row->locid == $decode_location_id)
                      {
                          $display_css = "block";
                      }
                      else {
                          $display_css = "none";
                      }
					/*  
					if($total_records1==1)
					{
						$location_str .= '<div class="address_det" id="addressinfo_'.$Row->locid.'" style="display:'.$display_css.'"><div class="my_loc_bckgnd_img1" loc_id="'.$Row->locid.'" >';
					}
					else
					{
						$location_str .= '<div class="address_det" id="addressinfo_'.$Row->locid.'" style="display:'.$display_css.'"><div class="my_loc_bckgnd_img" loc_id="'.$Row->locid.'" >';
					}
                    */ 
					$location_str .= '<div class="address_det" id="addressinfo_'.$Row->locid.'" style="display:'.$display_css.'"><div class="my_loc_bckgnd_img" loc_id="'.$Row->locid.'" >';
					 $location_str.='<input type="hidden" class="hdnlatlong" lat="'.$Row->latitude.'" long="'.$Row->longitude.'" />';
                   
                           // $location_str .= "<b>".$Row->location_name."</b><br/>";
                    if($Row->address!="")
                        $location_1 = $Row->address;
                    if($Row->city!="")
                         $location_2 = $Row->city;
                    if($Row->state!="")
                         $location_3 = $Row->state;
                    if($Row->country!="")
                         $location_4 = $Row->country;
                    if($Row->zip!="")
                         $location_5 = $Row->zip;
                    //echo "<br/>".$address ."<br />";
                    //$address = "<table><tr><td width='30%' style='vertical-align:top;'>Address : </td><td>".$row->address.",".$row->city.",<br/> ".$row->state.",".$row->zip.",".$RSLocation->fields['country']."<br/><td></tr></table>";
                    //echo $address;
                    //echo $row->website."<br />";
                    if($Row->phone_number != ""){
                        $phno = explode("-",$Row->phone_number);
                     $newphno = "(".$phno[1].") ".$phno[2]."-".$phno[3];
                       //  $location_str .= "<br/>".$newphno;
                    }
                   $location_str .= "<div  class='location_pg_addr'>";
						  //$location_str .= "<span >";
$location_str .= "<span >".$location_1."</span>,<br/>";
$location_str .= "<span >".$location_2."</span>,";
$location_str .= "<span >".$location_3."</span>, ";
$location_str .= "<span >".$location_4."</span>, ";
$location_str .= "<span >".$location_5 ."</span></span><br/>";
$location_str .= "<span >".$newphno."</span><br/>";
$location_str .= '</div>';
                $from_lati=$_COOKIE['mycurrent_lati'];
                $from_long=$_COOKIE['mycurrent_long'];
				              
                $to_lati=$Row->latitude;
                
                $to_long=$Row->longitude;
                
                
                $deal_distance="";
                $deal_distance= "</div>";
                    
                /*website code */ 
                     $deal_distance .= "<div class='websiteinfo'>";
                      $share_link = "";
                  /*website code */ 
                     
                    if($Row->website != "")
                    {
                        $share_link .= "<a target='_blank' href='".$Row->website."' class='webiteclass'>Website </a>";
                    }
					else{
					   $share_link .= "<a  href='javascript:void(0)' class='grayoutclass'>Website </a>";
					}
					if($Row->facebook != "")
                    {
						$share_link .= "<a target='_blank' href='".$Row->facebook."' class='webiteclass'> | Facebook | </a>";
					}
					else
					{
						$share_link .= "<a  href='javascript:void(0)' class='grayoutclass'> | Facebook | </a>";
					}
					if($Row->google != "")
					{
						$share_link .= "<a target='_blank' href='".$Row->google."' rel='publisher' class='webiteclass'>Google+</a>";
					}
					else
					{
						$share_link .= "<a  href='javascript:void(0)' class='grayoutclass'>Google+</a>";
					}
					 $deal_distance .=$share_link;
                     $deal_distance .= "</div>";
                    /*end of website code */
                
                   $mile_distance="";
                   $mile_distance.= "<div id='milesdiv'><b>Miles Away : </b>";
                    if($from_lati == $to_lati && $from_long == $to_long ){
                $mile_distance.=  "0 ";
                    }
                    else{
                         $mile_distance.= $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M") . " ";
                    }
					$mile_distance.= "</div>";
                //$maphref="http://maps.google.com/maps?saddr=".$from_lati.",".$from_long."&daddr=".$to_lati.",".$to_long;
				
				$maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;
                    
                   
                   
                                     
                 if($from_lati!="" && $from_long!="")        
                    $location_str .= $deal_distance.$mile_distance;
			    else
				{
					$mile_distance = "<div id='milesdiv'><b>Miles Away : </b><a id='askmile'>Click here</div>";
					$mile_distance.= '<div id="enter_zipcode_div" style="display:none">
					<div  style="width:423px">
						<div class="zipcode_section" style="margin:10px;">
							<div style="margin-bottom:10px">
								Please enter your current location (City , Country) or Zip code:
							</div>
							<div>
								<input type="text" name="enter_zip" id="enter_zip" value="" onkeyup="popup_searchdeal(this.value,event)"/> 
								&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="button" name="searchdeal_zip" id="searchdeal_zip" value="OK" />
								<input type="button" name="searchdeal_zip_cancel" id="searchdeal_zip_cancel" value="Cancel" />
							</div>   
						</div>
					</div>
				</div>';
					$location_str .= $deal_distance.$mile_distance;
				} 
				
				
                $location_str .= '<div class="gettdirection"><a href="'.$maphref.'" target="_blank">Get Directions</a><div>';
                                    
                                     
                               
                    if($Row->picture!="")
                    {
               
                   $loc_image = ASSETS_IMG."/m/location/mythumb/".$Row->picture;
                        
                    }
					/*
                    elseif($RS1->fields['merchant_icon']!="")
                    {
                            $loc_image = WEB_PATH."/merchant/images/icon/".$RS1->fields['merchant_icon'];
                         
                      
                    }
					*/
                    else 
                    {
                              $loc_image = ASSETS_IMG."/c/Merchant_Location.png";
                    }
                    

                                     
                                      if(!isset($_SESSION['customer_id'] )){
            //	$MARKER_HTML .= '<a href="'.WEB_PATH.'/register.php?btnRegisterStore=1&location_id='.base64_encode($storeid).'" target="_parent">Subscribe to Store</a>&nbsp;&nbsp;';
            }else{
                $RS_user_subscribe = "select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$Row->locid." and subscribed_status=1";
                ///echo $RS_user_subscribe;
                $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);
                if($check_subscribe->RecordCount()==0)
                {
                   //$location_str.= "<a href='".WEB_PATH."/process.php?btnRegisterStore=1&page=location&location_id=".base64_encode($Row->locid)."' target='_parent' class='reserve_print' id='btn_reserve_deal'>Subscribe</a>&nbsp;&nbsp;<br/>";
                     $location_str .= "<a href='javascript:void(0)' class='subscribestore reserve_print' s_lid1='".$Row->locid."' s_lid='".base64_encode($Row->locid)."'  >Subscribe</a>&nbsp;&nbsp;";
                   
                }
                else
                {
                   // $location_str.= "<a href='".WEB_PATH."/process.php?btnunsubscribelocation=1&page=location&location_id=".base64_encode($Row->locid)."' target='_parent' class='reserve_print' id='btn_reserve_deal'>Unsubscribe</a>&nbsp;&nbsp;<br/>";
                     $location_str .= "<a href='javascript:void(0)' class='unsubscribestore reserve_print'  s_lid1='".$Row->locid."' s_lid='".base64_encode($Row->locid)."'  >Unsubscribe</a>&nbsp;&nbsp;<img class='sub_loader' src='".ASSETS_IMG."/c/24.GIF'>";
                }
            }
             $location_str.="</div></div>"            ;
             $location_str.="</div>"            ;
              if($count != 1) echo ",";
				?>
				["<?=$MARKER_HTML?>", <?=$lat?>, <?=$lon?>,<?=$Row->locid?>,'<?=$loc_image?>']
				<?
				if($count != $total) // echo ",";
							$count++;
                                     }
                    }
                                    
                                     }
				?>
];
}
catch(e)
{
}				
</script>
<script>
try{
var timeout;
var timeout1;
		markersArray = new Array();
		  function initialize() {
				   map = new google.maps.Map(document.getElementById('map_canvas'), {
					  zoom:  14,
					  center: new google.maps.LatLng(<?=$lat?>, <?=$lon?>),
					  mapTypeId: google.maps.MapTypeId.ROADMAP
					});
				   for (var i = 0; i < locations.length; i++) {
					
						createMarker(locations[i][1], locations[i][2], locations[i][0],locations[i][3],locations[i][4]);
					}
                                        //alert(locations.length);
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
		  }
			
				 function handleNoGeolocation(errorFlag) {
					contentString = "Error: The Geolocation service failed.";
					setUserLocationPoint(contentString, true);
				}
//				function setUserLocationPoint(infoWinsowContentObj, defaultOpen) {
//					if (marker) {
//						marker.setMap(null);
//					}
//					map.setCenter(someLocation);
//				}
		
			var infowindow = new google.maps.InfoWindow({
					size: new google.maps.Size(150, 50)
				});
                               //alert(jQuery("#hdn_location_id").val());
				 function createMarker(lat,lan,mycontent,lid,limg) {
                                     
                                 //alert(lid);
                                    
                                     var locaid = lid;
                                     var l_image = limg ;
				 	var content = mycontent;
                                        //alert(jQuery("#hdn_location_id").val()+"===="+lid);
                                        if(jQuery("#hdn_location_id").val() == lid){
										
                                            var marker = new google.maps.Marker({
								  locid:locaid,
								  position: new google.maps.LatLng(lat,lan),
								  map: map,
								  zIndex: Math.round(lat * -100000) << 5,
								  icon: new google.maps.MarkerImage('<?=ASSETS_IMG?>/c/pin-small-blue.png')
								});
                                        }else{
						var marker = new google.maps.Marker({
								  locid:locaid,	
								  position: new google.maps.LatLng(lat,lan),
								  map: map,
								  zIndex: Math.round(lat * -100000) << 5,
								  icon: new google.maps.MarkerImage('<?=ASSETS_IMG?>/c/pin-small.png')
								});
					}
					google.maps.event.addListener(marker, 'click', function() {
                                              for (var i=0; i<markersArray.length; i++) {
                                                   if(i != "indexOf")
                                              markersArray[i].setIcon('<?=ASSETS_IMG?>/c/pin-small.png');
                                    }

                                   
                                             marker.setIcon('<?=ASSETS_IMG?>/c/pin-small-blue.png'); 
											 
											jQuery(".pricerangebox").css("display","none");
											call_pricerange_popup();
												
                                                jQuery("div[id^='locationarea_']").each(function(){
                                                   jQuery(this).css("display","none");
                                                });
											jQuery("div[id^='locationreview_']").each(function(){
                                                   jQuery(this).css("display","none");
                                                });
                                                 jQuery("div[id^='div_main_']").each(function(){
                                                   jQuery(this).css("display","none");
                                                });
                                                jQuery("#locationarea_"+locaid).css("display","block");
												
												// start location details div 17 12 2013

												jQuery("div[id^='locationdetailinfo_']").each(function(){
                                                   jQuery(this).css("display","none");
                                                });
												jQuery("#locationdetailinfo_"+locaid).css("display","block");
												
												// end location details div 17 12 2013
												
												//alert("#locationarea_"+locaid+"="+jQuery("#locationarea_"+locaid).attr("redirect_url"));
												var r_n = "";
												jQuery(".tabs").each(function(){
													if(jQuery(this).css("display") == "block")
													{
													
													cls= jQuery(this).attr("id");
																r_n = jQuery("."+cls).attr("data-hashval");
													
																/*if(cls== "div_about_us")
																{
																	r_n = jQuery(".div_about_us").attr("data-hashval");
																}
																else if(cls== "div_more_images")
																{
																	r_n = "photos";
																}
																else if(cls== "div_offers")
																{
																	r_n = "offers";
																}
																else if(cls== "div_menu")
																{
																	r_n = "menus";
																}
																else if(cls== "div_location_detail")
																{
																	r_n = "locationinfo";
																}
																else
																{
																	r_n = "reviews";
																} */
													}
												});
												try
												{
													if (typeof window.history.replaceState != 'undefined') { 
														window.history.replaceState( {} ,'foo',jQuery("#locationreview_"+locaid).attr("redirect_url")+"#"+r_n );;
													}
												}
												catch(e)
												{
												}
	//var History = window.History; // Note: We are using a capital H instead of a lower h
	

	//History.replaceState( {} ,'foo',jQuery("#locationreview_"+locaid).attr("redirect_url") ); 
	

                                                jQuery("#div_main_"+locaid).css("display","block");
                                                jQuery("#locationreview_"+locaid).css("display","inline");
                                                jQuery("div[id^='addressinfo_']").each(function(){
                                                    jQuery(this).css("display","none");
                                                });
                                                jQuery("#addressinfo_"+locaid).css("display","block");
                                                jQuery(".image_merch_bus img").attr("src",l_image);
						//infowindow.setContent(content);
						//infowindow.open(map,marker);
						
						/* Rating coding come here  */
						 draw_chart(locaid);
						/* Rating coding come here  */
                                                facebook_data_function(locaid);
											/*********** get campaigns grid data **************/
											if(jQuery("#div_offers").css("display") == "block" )
											{
											var v  = locaid;
											 
											if(1)//$("#locationarea__"+v).length == 0)
											{
											
											//open_popup('Notificationloader');
											jQuery("div[id^='locationarea_']").each(function(){
												jQuery(this).css("display","none");
											});
											if (timeout) clearTimeout(timeout);
											timeout=setTimeout(function()
											{
											//alert('<?php echo WEB_PATH; ?>/process.php?getcampaignlistoflocation=true&locid='+v+'&mlatitude=<?php echo $selected_location[0]->latitude; ?>&mlongitude=<?php echo $selected_location[0]->longitude; ?>&customer_id=<?php echo $cust_s ?>');
													if($("#locationarea_"+v).length == 0)
													{
														jQuery("#offer_tab_loader").show();											
											//jQuery("#offer_tab_loader").css("display","block");
														 jQuery.ajax({
														  type:"POST",
														  url:'<?php echo WEB_PATH; ?>/process.php',
														//  async:false,
														  data :'getcampaignlistoflocation=true&locid='+v+'&mlatitude=<?php echo $selected_location[0]->latitude; ?>&mlongitude=<?php echo $selected_location[0]->longitude; ?>&customer_id=<?php echo $cust_s ?>',
															  success:function(msg)
															  {
															
												$("#div_offers #dealslist").append(msg);
											jQuery("#offer_tab_loader").hide();
											bind_deal_blk_hover();
															  }
														  });
																										  
															  $("div[id^='locationarea_']").each(function(){
																$(this).css("display","none");
															});
															$("#locationarea_"+v).css("display", "block" );
													}
													else{
													
															$("div[id^='locationarea_']").each(function(){
																$(this).css("display","none");
															});
															$("#locationarea_"+v).css("display", "block" );
														}
												///alert("in if");
												
											},1000);
														$("div[id^='locationarea_']").each(function(){
															$(this).css("display","none");
														});
														$("#locationarea_"+v).css("display", "block");													  
											}
											else{
												//alert("hi");
												$("div[id^='locationarea_']").each(function(){
													$(this).css("display","none");
												});
												$("#locationarea_"+locaid).css("display", "block" );
											}
											
										
									
									}
									/*********** get cmapiagns grid data *************/
                                                
									if(jQuery("#div_reviews").css("display") == "block" )
									{
											var v  = locaid;
											$('#hdn_location_id').val(v);
											var rtable = $('#review_table_by_loc').dataTable();
											rtable.fnDraw();
											 
											if($("#review_list_"+v).length == 0)
											{
											
											//open_popup('Notificationloader');
											jQuery("div[id^='review_list_']").each(function(){
												jQuery(this).css("display","none");
											});
											//jQuery("#review_tab_loader").css("display","block");
											jQuery("#review_tab_loader").show();												
											//alert(jQuery("#review_tab_loader").css("display"));
											if (timeout) clearTimeout(timeout);
											timeout=setTimeout(function()
											{
												
												 /*jQuery.ajax({
												  type:"POST",
												  url:'<?php echo WEB_PATH; ?>/process.php',
												  data :'get_location_reviews_grid=true&location_id='+v,
														  //async:true,
														 // async:false,
													  success:function(msg)
													  {
										//                       alert(msg);
																$("#div_reviews").append(msg);
																$('#example_'+v).dataTable( {
																	"bDestroy": true,
																	'bFilter': false,
																	"aaSorting": [],
																	"sDom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
																	"aoColumns": [	
																				{ "bSortable": false },
																				{ "bSortable": false , "bVisible":    false ,"sType": 'numeric'},
																				{ "bSortable": false , "bVisible":    false ,"sType": 'numeric' },
																				{ "bSortable": false , "bVisible":    false ,"sType": 'numeric' }
																				]											
																} );
																//close_popup('Notificationloader');
																//jQuery("#review_tab_loader").css("display","none");
																jQuery("#review_tab_loader").hide();	
																//alert(jQuery("#review_tab_loader").css("display"));
																
																// 12 11 2013 display none prev next when one page only																				
										
																//alert(jQuery("a[id$='_previous']").attr("class"));
																//alert(jQuery("a[id$='_next']").attr("class"));
																//alert("#review_list_"+locaid+" a[id$='_previous']");
																var prev_class=jQuery("#review_list_"+v+" a[id$='_previous']").attr("class");
																var next_class=jQuery("#review_list_"+v+" a[id$='_next']").attr("class");
																//alert(prev_class);
																//alert(next_class);
																if(prev_class=="paginate_disabled_previous" && next_class=="paginate_disabled_next")
																{
																	//alert("same here");
																	//jQuery(".paginate_disabled_previous").css("display","none");
																	//jQuery(".paginate_disabled_next").css("display","none");
																	
																	jQuery("a[id='example_"+v+"_previous']").css("display","none");
																	jQuery("a[id='example_"+v+"_next']").css("display","none");
																	
																	jQuery("#review_list_"+v+" .bottom a").css("display","none");
																}
																																																
																// 12 11 2013 display none prev next when one page only	
										
														  }
													  });*/
											},1000);
														$("div[id^='review_list_']").each(function(){
															$(this).css("display","none");
														});
														$("#review_list_"+v).css("display", "block");													  
											}
											else{
												//alert("hi");
												$("div[id^='review_list_']").each(function(){
													$(this).css("display","none");
												});
												$("#review_list_"+locaid).css("display", "block" );
											}
											
										
									
									}
                                                                        
                                    if(jQuery("#div_menu").css("display") == "block" )
									{
										
											var v  = locaid;
											 
											if($("#menu_"+v).length == 0)
											{
											//open_popup('Notificationloader');
												jQuery("div[id^='menu_']").each(function(){
															jQuery(this).css("display","none");
												});
												jQuery("#menu_tab_loader").show();
												
												
												//alert(jQuery("#menu_tab_loader").css("display"));
												
												if (timeout1) clearTimeout(timeout1);
											timeout1=setTimeout(function()
											{
													//alert("in if");
													jQuery("#menu_tab_loader").show();
													//alert(jQuery("#menu_tab_loader").css("display"));
												 jQuery.ajax({
												  type:"POST",
												  url:'<?php echo WEB_PATH; ?>/process.php',
												  data :'get_location_menu=true&location_id='+v,
														  //async:false,
														  //async:false,
													  success:function(msg)
													  {
										//                       alert(msg);
															 //var responce_arr = msg.split("###");
															 
															   // if(responce_arr[0] == "error")
																 //   {
																		//alert(responce_arr[1]);
																   //     $("#div_menu").html(responce_arr[1]);
																	//}
																	//else
																	//{
																	//alert(jQuery("#menu_tab_loader").css("display"));
																	jQuery("#div_menu").append(msg);
																	jQuery("#menu_tab_loader").hide();
																	//alert(jQuery("#menu_tab_loader").css("display"));
																	
																	//}
																//close_popup('Notificationloader');
																
                                                                 //jQuery("#menu_tab_loader").css("display","none");
																
																//alert(jQuery("#menu_tab_loader").css("display"));																																	
														  }
														  
													  });
													  	
												},1000);	  
														$("div[id^='menu_']").each(function(){
															$(this).css("display","none");
														});
														$("#menu_"+v).css("display", "block");													  
											}
											else{
												//alert("hi");
												$("div[id^='menu_']").each(function(){
													$(this).css("display","none");
												});
												$("#menu_"+locaid).css("display", "block" );
                                                                                               $(".mainmanuTitle").each(function(){
                                                                                                    $(this).addClass("inactive");
                                                                                                });  
                                                                                                $("#0"+locaid).removeClass("inactive");
                                                                                                $("#smbody0"+locaid).css("display", "block" );
											}
									
									}
									
                                    if(jQuery("#div_more_images").css("display") == "block" )
									{
											var v  = locaid;
											var lati=lat;
                                            var longi=lan;
											if($("#moreimages_"+v).length == 0)
											{
												//open_popup('Notificationloader');
												jQuery("div[id^='moreimages_']").each(function(){
															jQuery(this).css("display","none");
												});
												
												//jQuery("#photo_tab_loader").css("display","block");
												jQuery("#photo_tab_loader").show();												
												//alert(jQuery("#photo_tab_loader").css("display"));
												
												if (timeout) clearTimeout(timeout);
											timeout=setTimeout(function()
											{
												
												//alert("in if");
												 jQuery.ajax({
													  type:"POST",
													  url:'<?php echo WEB_PATH; ?>/process.php',
													  data :'get_more_images=true&location_id='+v+'&latitude='+lati+'&longitude='+longi,
													  //async:true,
													 // async:false,
													  success:function(msg)
													  {       
																											  
														  jQuery("#div_more_images").append(msg);
														  //close_popup('Notificationloader');
														  
														  //jQuery("#photo_tab_loader").css("display","none");
															jQuery("#photo_tab_loader").hide();	
															//alert(jQuery("#photo_tab_loader").css("display"));
																
													  }
													});
												},1000);	
                                                        $("div[id^='moreimages_']").each(function(){
															$(this).css("display","none");
														});
														$("#moreimages_"+v).css("display", "block");
																										  
											}
											else{
												$("div[id^='moreimages_']").each(function(){
													$(this).css("display","none");
												});
												$("#moreimages_"+locaid).css("display", "block" );
											}
											
										
									
									}
					     });
                                             
                                    markersArray.push(marker);
					//infowindow.setContent(content);
					//	infowindow.open(map,marker);
					//infowindow.setContent(contentString);
					//infowindow.open(map,marker);
						//infowindow.open(map, marker);
						//markerArray.push(marker); 
				 }
window.onload = initialize;
			
}
catch(e)
{
}
</script>

        <div class="mainside">
            <input type="hidden" name="visited_deals" id="visited_deals" value="" />
			<input type="hidden" name="first_time_loaded" id="first_time_loaded" value="" />
			<input type="hidden" name="print_redirect_link" id="print_redirect_link" value="0" />
            <input type="hidden" name="redeemvoucher_value" id="redeemvoucher_value" value="" />
            <input type="hidden" name="rservedeals_value" id="rservedeals_value" value="" />
            <input type="hidden" name="hdn_reserve_err" id="hdn_reserve_err" value="" />
            <input type="hidden" name="deal_barcode" id="deal_barcode" value="" />
			<input type="hidden" name="medium" id="medium" value="" />
            <input type="hidden" name="hdn_campid" id="hdn_campid" value="" />
				   <input type="hidden" name="hdn_locid" id="hdn_locid" value="" />
				   
            <div class="rightside">
                 <div class="location_detail"> <!-- class="dealDetail" -->
<!--        <div class="locationdetail_header">&nbsp;</div>-->
        <!-- class="dealDetailHeader " -->
        <div class="dealDetailDiv">
          <div class="detail" >
            <table width="100%"  border="0" cellspacing="2" cellpadding="2" >
              <tr>
                <td class="left_location_detail"  align="left" valign="top">
				<div class="locat_detail">                
			
                  <?php
					
    /*     
         $busines_name="";
	$array_where['id'] = $storeid;
	$RSlocation = $objDB->Show("locations", $array_where);
      $loc_image = WEB_PATH."/merchant/images/location/".$RSlocation->fields['picture'];  
	  */
	   $busines_name="";

      $loc_image = ASSETS_IMG."/m/location/mythumb/".$selected_location[0]->picture;  
	  
        if($total_records1 ==0){
            
        }
        else 
        {
            if($selected_location[0]->location_name!="")
            {
                $busines_name=$selected_location[0]->location_name;
            }
            else 
            {
               
       
		//$businessname = $busines_name ;
            }
                
        }
         
        	 ?>
		<div class="hdr_loc_detail">
 		<div class="merc_name" >
		<span >
		<?php echo $selected_location[0]->business; ?></span></div>
		
             
		
		</div>
        <div class="loca_detail_left">
        <div class="about_us_brief_location" >
		<span >
		<?php  echo $selected_location[0]->aboutus_short; ?></span></div> 
		<!--  <p style="color:#000;font-size:20px;margin:0;line-height:25px;margin-top:5px;">Location Name : "<b><?php echo $busines_name; ?>"</b></p>-->
                  <?  //} ?>
				  <?php
				  if($selected_location[0]->merchant_icon!="")
								{
								    $img_src=ASSETS_IMG."/m/icon/".$selected_location[0]->merchant_icon; 
								}
							       
								else 
								{
								    $img_src=ASSETS_IMG."/c/Merchant.png";
								}
								
				  ?>
				                   
					<?php
						$size = getimagesize($img_src);
						$height=$size[1];
						if($height>=111)
						{
					?>
						<div class="image_merch_bus1" style="display:inline-block">
							<img src="<?=$img_src?>" border="0" />
						 </div>
					<?php
						}
						else
						{
					?>
						<div class="image_merch_bus2" style="display:inline-block">
							<img src="<?=$img_src?>" border="0" />
						 </div>
					<?php
						}
					?>
				 
				  
				  <!---  dynamic information -->
				  <?php 
				  $ct =0;
				  $location_array = array();
				   foreach($records_array1 as $Row)
		{
                    
                      $storeid = $Row->locid;
                      
					 // echo "<pre>";
					  //print_r($location_array);
					 // echo "</pre>";
					  if(!in_array($Row->locid ,$location_array)){
                    	/* $array_where['id'] = $Row->locid ;
	$RSlocation1 = $objDB->Show("locations", $array_where); */
					if($Row->locid == $decode_location_id)
                      {
					//   echo "in if";
                          $display_css = "inline";
                      }
                      else {
					  //echo "in else";
                          $display_css = "none";
                      }
                        if($ct != 0){
                            $new_div_start ="</div><div id='locationreview_".$Row->locid."' redirect_url='".$Row->location_permalink."' style='display:$display_css'>";
                        }  
                        else{
                             $new_div_start ="<div id='locationreview_".$Row->locid."' redirect_url='".$Row->location_permalink."' style='display:$display_css'>";
                        }
                        $ct = $ct +1;
						 array_push($location_array,$Row->locid );
						echo $new_div_start;
				  ?>
				  <div class="localReviews">
               <div class="r_box">
			      <?php 
				   $rating_title ="Not Yet Rated";
            $class =  "full-gray";
           if( $Row->avarage_rating < 0 && $Row->avarage_rating  < 1)
            {
               // echo "in .5";
                $class =  "orange-half";
				$rating_title = "Poor";
            }
            else if ($Row->avarage_rating >= 1 && $Row->avarage_rating <= 1.74) {
               // echo "in 1";
                  $class =  "orange-one";
				  $rating_title = "Poor";
            }
            else if ($Row->avarage_rating >= 1.75 && $Row->avarage_rating <= 2.24) {
               // echo "2";
                  $class =  "orange-two";
				  $rating_title = "Fair";
            }
            else if ($Row->avarage_rating >= 2.25 && $Row->avarage_rating <= 2.74) {
                //echo "2,5";
                  $class =  "orange-two_h";
				  $rating_title = "Good";
            }
            else if ($Row->avarage_rating >= 2.75 && $Row->avarage_rating <= 3.24) {
                //echo "3";
                  $class =  "orange-three";
				  $rating_title = "Good";
            }
            else if ($Row->avarage_rating >= 3.25 && $Row->avarage_rating <= 3.74) {
              //  echo "3.5";
                  $class =  "orange-three_h";
				  $rating_title = "Very Good";
            }
            else if ($Row->avarage_rating >= 3.75 && $Row->avarage_rating <= 4.24) {
               // echo "4";
                  $class =  "orange-four";
				  $rating_title = "Very Good";
            }
            else if ($Row->avarage_rating >= 4.25 && $Row->avarage_rating <= 4.74) {
            //  echo "4.5";
                  $class =  "orange-four_h";
				  $rating_title = "Excellent";
            }
            else if($Row->avarage_rating >= 4.75) {
               // echo "5";
                  $class =  "orange";
				  $rating_title = "Excellent";
            }
          //  echo $class;
           ?>
           <div class="ratinn_box <?php echo $class; ?>" >        
           </div>
            <div class="cust_attr_tooltip" >
            	<div class="arrow_down" ></div>
            	<span id="star_tooltip"><?php echo $rating_title; ?></span>
            </div>

   <!--                     <div class="rating" id="rating_<?php echo $Row->locid; ?>" style="display:inline" >
            </div> -->
			<div  class="loc_rati">
				<span class="fine_print" ><?php echo  $Row->no_of_rating; ?></span><span class="fine_print">Ratings</span>
			</div>
			<input type="hidden" name="hdn_rating_val_<?php echo $Row->locid; ?>" id="hdn_rating_val_<?php echo $Row->locid; ?>" value="<?php echo round($Row->avarage_rating,2) ; ?>" />
			<!--<meta itemprop="ratingValue" content ="<?php //echo round($Row->avarage_rating,2) ?>"> -->
            </div>
                                    
			<?php 
			
				 // echo WEB_PATH.'/process.php?get_latest_location_review=yes&location_id='.$storeid;
					 $arr=file(WEB_PATH.'/process.php?get_latest_location_review=yes&location_id='.$Row->locid);
                        if(trim($arr[0]) == "")
                             {
                                     unset($arr[0]);
										$arr = array_values($arr);
                             }
                             $json = json_decode($arr[0]);
                        $review_available  = $json->is_review;
						$review = $json->review;
						
				  ?>
            <div data-component="restaurantorderinginfo-reviews" class="reviews_ghflyout">
                    
					<?php if($review_available != 0 ){ 
					$review = str_replace("&nbsp;", " ",$review);
					$review =  str_replace('<br>', ' ',$review);
					$output = preg_replace('!\s+!', ' ', $review);
					?>
					<span class="most-recent-positive-review"><?php echo substr($output,0,50); if(strlen($output)>50) { echo " ... &nbsp;&nbsp;&nbsp;<span class='readreview' rev='".$Row->no_of_reviews."' > Show More</span>" ;
					}?></span>
					<?php  } ?>
					<div>
                        <span class="readreview" rel="nofollow" rev="<?php echo   $Row->no_of_reviews; ?>" data-component="ghflyout-open-label" class="label openLabel" href="/restaurant/264230/info/reviews">
							<?php echo   $Row->no_of_reviews; ?> Local Reviews
                        </span>
						</div>
                        <span data-component="ghflyout-close-label" class="label closeLabel" style="display: none;">
                            Hide Reviews
                        </span>
                   </div>
            
        </div>
	<div class="location_hrs">
            <!--<span style="font-weight:bold;">Location Hours:</span> 10:30AM-10:30PM</br>
           <span style="font-weight:bold;">Currently Close</span>-->
           
           <?php
           
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
                   
                   /* $sql="select * from location_hours where location_id=".$Row->locid." ORDER BY FIELD(day, 'mon', 'tue', 'wed', 'thu', 'fri', 'sat','sun')";
                   //echo $sql; 
                   $RS_hours_data = $objDB->execute_query($sql); */
                   $RS_hours_data = $objDB->Conn->Execute("select * from location_hours where location_id=? ORDER BY FIELD(day, 'mon', 'tue', 'wed', 'thu', 'fri', 'sat','sun')",array($Row->locid));
                   $location_time="";
                   $start_time = "";
                   $end_time="";
                   $status_time="";
                   if($RS_hours_data->RecordCount()>0){
                       echo "<a href='javascript:void(0)' >Location Hours</a><div  id='lct_hrs_".$Row->locid."' class='locationhours' style='display:none;'>";
                       while($Row_data = $RS_hours_data->FetchRow()){
                           $start_time = $Row_data['start_time'];
                           $end_time=$Row_data['end_time'];
                           $location_time.=$Row_data['start_time']." - ";
                           $location_time.=$Row_data['end_time'];
						   //$my_start_tim = $Row_data['start_time'];
						   //$my_start_tim = date('H:i', strtotime($my_start_tim));
						   //$my_close_tim = $Row_data['end_time'];
						  // $my_close_tim = date('H:i', strtotime($my_close_tim));
						   echo "<span><p>".$Row_data['day']."</p>".$Row_data['start_time']." - ".$Row_data['end_time']."</span>";
                       }
					   echo "</div>";
                   }
                   
		     
                   
                    if ($Row->is_open==1) 
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
		   echo "<div></div>"; // echo "==".$Row->locid."==";
		   ?>
		     <div style="clear:both;height:10px"></div>
									<div class="rating_heading_top" style="display:none">
										<div class="rating_heading_half_left"> Visitors Rating</div>
										<span class="rating_heading_seperator">|</span>
										<div class="rating_heading_half_right"> Rating Trend
										</div>
									</div>
									 <div style="clear:both"></div>
									 <div style="clear:both"></div>
									 <!-- start of rating div -->
									 <div><!-- class="reprt_back"> -->
									<div id="visitorrating_<?php echo $Row->locid; ?>" class="visitorrating" style="display:none" >
										
										<div class="rting_strip">
										<div class="rating_heading" >Excellent : </div><div class="mainratingdiv_containere" id="">
										<div class="div_container excellent"></div>
														</div><span></span></div>
										
										<div class="rting_strip">
										<div class="rating_heading">Very Good : </div><div class="mainratingdiv_containere" id="">
										<div class="div_container verygood"></div>
										</div><span></span></div>
										
										<div class="rting_strip">
										<div class="rating_heading"> Good : </div><div class="mainratingdiv_containere" id="">
										<div class="div_container good"></div>
										</div><span></span></div>
										
										<div class="rting_strip">
										<div class="rating_heading"> Fair : </div><div class="mainratingdiv_containere" id="">
										<div class="div_container fair"></div>
										</div><span></span></div>
										
										
										<div class="rting_strip">
										<div class="rating_heading"> Poor : </div><div class="mainratingdiv_containere" id="">
										<div class="div_container poor"></div>
										</div><span></span></div>
		</div>
		
		<!-- end of rating div -->
                
                
                
<div id="container_<?php echo $Row->locid; ?>" class="ratingtrend" ></div>  
</div>
		<!-- end of report bolck div -->

<div class="facebookmaindatadiv" style="display:none"> 
   <?php
    if($Row->location_likes != "0" && $Row->location_likes != "" ||  $Row->location_talking_about_this != "0" && $Row->location_talking_about_this != ""  || $Row->location_were_here != "0" && $Row->location_were_here != ""  )
    {
    ?>  
    <div class="facebook_heading_top" >
        <ul>         
        <li class="facebook_heading_half_left_image"> </li>
        <li class="facebook_colon"> : </li>
        
                         <?php 
                        if($Row->location_likes == "" || $Row->location_likes == "0" )
                        {
                            
                        }
                        else
                        { ?>
                            <li class="facebook_heading_half_left">
                            <?php echo $Row->location_likes." likes";  ?>
                            </li>
                       <?php }
                    ?> 
                        
                    
                          <?php 
                        if($Row->location_talking_about_this == "" || $Row->location_talking_about_this == "0" )
                        {
                            
                        
                        }
                        else
                        { ?>
                            
                            <li class="facebook_heading_seperator">
                            <?php 
                            if($Row->location_likes == "" || $Row->location_likes == "0" )
                            {
                                echo $Row->location_talking_about_this." talking about this";
                            }
                            else
                            {
                                echo '<span class="facebook_bullet_first">&#9204;</span>'.$Row->location_talking_about_this." talking about this";   
                            }
                                
                           
                            
                            ?>
                            </li>
                        <?php }
                    ?> 
                        
                    
                        <?php 
                        if($Row->location_were_here == "" || $Row->location_were_here == "0" )
                        {
                            
                        }
                        else
                        { ?>
                             <li class="facebook_heading_half_right">
                                 
                             <?php 
                             if($Row->location_talking_about_this != "0" && $Row->location_talking_about_this != "0"   || $Row->location_likes != "" && $Row->location_likes != "0")
                             {
                                 echo '<span class="facebook_bullet_first">&#9204;</span>'.$Row->location_were_here." were here";  
                             }
                             else
                             {
                                 echo $Row->location_were_here." were here";  
                             }
                             
                             
                             ?>
                             </li>           
                     <?php   }
                    ?> 
                        
        </ul>  
                    
            </div>
    <?php
    
    }
    ?>
    
    <?php
    if($Row->location_visitors != "" && $Row->location_visitors != "0" ||  $Row->location_check_ins != ""  && $Row->location_check_ins != "0" )
    {
    ?>
        <div class="facebook_heading_second_block" style="display:none" >
            <ul>
                    
                        <?php 
                        
                        if($Row->location_visitors == "" || $Row->location_visitors == "0")
                        {
                            
                        }
                        else
                        { ?>
                            <li class="facebook_heading_half_left_second"> 
                                <?php echo "Total visitors : ".$Row->location_visitors;  ?>
                            </li>
                       <?php }
                    ?> 
    
                      
                        <?php 
                        if($Row->location_check_ins == "" || $Row->location_check_ins == "0")
                        {
                            
                        }
                        else
                        { ?>
                            <?php 
                            if($Row->location_visitors == "" || $Row->location_visitors == "0")
                            { ?>
                                <li class="facebook_heading_half_right_second">
                                    <?php echo "Total Check-ins :".$Row->location_check_ins;  ?>
                            </li>
                            <?php }
                            else
                            { ?>
                                <li class="facebook_heading_half_right_second">
                                    <?php echo "<span class='facebook_bullet_second'>&#9204;</span>Total Check-ins :".$Row->location_check_ins;  ?>
                            </li>
                           <?php  } ?>
                            
                       <?php }
                    ?>
                    
                   </ul> 
            </div>
    <?php } ?>
    <div class="facebooksignup" style="display:none">
        <div>
       <?php  echo $client_msg['location_detail']['label_facebook_sign_up']; ?>
        </div>
        <a href="">Sign in with Facebook</a>
    </div>
</div>

<div class="locudiv">
    
    <!--<a href="javascript:void(0)" class="locumanudetail">Menu Detail</a>-->
</div>

		   <?php
                                          }
		   
		   } ?>
		   <!--- end of dynamic reviews -->
		   </div>
		<div style="clear:both">
		</div>
		<script>
		try{
			$(document).ready(function() {
				
				// 30 10 2013
				
					//alert(jQuery(".address_det").size());	
					/*
					if(jQuery(".address_det").size()==1)
					{
						jQuery(".address_det").find("div[class='my_loc_bckgnd_img']").removeClass("my_loc_bckgnd_img").addClass("my_loc_bckgnd_img1");
					}
					*/
				// 30 10 2031
				//alert("ready");
				
				// 18 10 2013 solve problem of location click event when session timeout and on location no public deal found then redirect to nearest location.
				/*
				var locaid1="<?php echo $decode_location_id ?>";
				
				var loc_find=jQuery("#locationarea_"+locaid1).length;
				
				if(loc_find==0)
				{
					var nearest_loc_id=jQuery("div[id^=locationarea_]:first").attr("id");
					var nearest_loc=nearest_loc_id.split("_");
					nearest_loc_id=nearest_loc[1];
					window.location.href='<?= WEB_PATH ?>/location_detail.php?id='+nearest_loc_id;
				}
				*/
				// 18 10 2013
				
			
			//$('div[id^="rating"]').rating('example.php', {maxvalue: 1});
			/*$('div[id^="rating"]').each(function(){
			
				var ids =$(this).attr("id");
				var arr = ids.split("_");
				var d = arr[1];
				
					$(this).raty({ half: true,readOnly: true,
							score:jQuery("#hdn_rating_val_"+d).val()
					});
			}); */
					
			});
			
			}
			catch(e)
			{
			}
			</script>
                   <?php
				   /*
                    if($row->picture!="")
                    {
                ?>
                    <div class="image_merch_bus"><img src="<?=WEB_PATH?>/merchant/images/location/<?=$row->picture?>" border="0" /> </div>
                          <?php 
                    }
                    elseif($RS1->fields['merchant_icon']!="")
                    {
                ?>
                          <p class="image_merch_bus"><img src="<?=WEB_PATH?>/merchant/images/icon/<?=$RS1->fields['merchant_icon']?>" border="0" /> </p>
                          <?php
                    }
                    else 
                    {
               ?>
                          <p class="image_merch_bus"><img src="<?=WEB_PATH?>/merchant/images/default_location.jpg" border="0" /> </p>
                          <?php         
                    }
					*/
                ?>
				
				
		
                                  
<!--<div id="rating_visitors" style="max-width: 210px; height: 200px; margin: 0 auto" ></div> -->
                  <!-- old about us code -->
                  <!--<div class="aboutus_location"><span class="about">About Us</span><p><?php if(isset($RS1->fields['aboutus'])){echo $RS1->fields['aboutus'];} ?></p></div>-->
                  <!-- end about us code 
                  <!--        <a href="javascript:void(0)" class="reserve_print" id="btn_reserve_deal">                                    
			Subscribe
         </a>-->
                  
                  <?php
//            if($_SESSION['customer_id'] == ""){
//            //	$MARKER_HTML .= '<a href="'.WEB_PATH.'/register.php?btnRegisterStore=1&location_id='.base64_encode($storeid).'" target="_parent">Subscribe to Store</a>&nbsp;&nbsp;';
//            }else{
//                $RS_user_subscribe = "select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$storeid." and subscribed_status=1";
//                $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);
//                if($check_subscribe->RecordCount()==0)
//                {
//                    echo "<a href='".WEB_PATH."/process.php?btnRegisterStore=1&page=location&location_id=".base64_encode($decode_location_id)."' target='_parent' class='reserve_print' id='btn_reserve_deal'>Subscribe</a>&nbsp;&nbsp;";
//                   
//                }
//                else
//                {
//                    echo "<a href='".WEB_PATH."/process.php?btnunsubscribelocation=1&page=location&location_id=".base64_encode($decode_location_id)."' target='_parent' class='reserve_print' id='btn_reserve_deal'>Unsubscribe</a>&nbsp;&nbsp;";
//                }
//            }
        ?>
                 
                  
                  <!-- <p style="text-align: justify; margin: 0;"><?php if(isset($RS[0]->description)){echo $RS[0]->description;}?></p> -->
                  
                  </div>
                <input type="hidden" name="hdn_location_id" id="hdn_location_id" value="<?php echo $decode_location_id; ?>" />
                  <div class="loca_detail_right">
                      <div class="map_canvas_outer">
                          <div id="map_canvas">
<!--                    <iframe id="slider_iframe" src="<?=WEB_PATH?>/location-map.php?location_id=<?=$decode_location_id?>" frameborder="0" style="width:100%; height:280px;" scrolling="no"></iframe>-->
                          </div> 
                      </div>
					
                      <div class="image_merch_bus" >
							<?php
								if($selected_location[0]->picture!="")
								{
							?>
							<img src="<?=$loc_image?>" border="0" />
							<?php
							}
							else
							{
							?>
							<img  src="<?=ASSETS_IMG?>/c/Merchant_Location.png" border="0" /> 
							<?php
							}
							?>
					  </div>
						
                      <?php
                    //}
                    //echo here
                      echo "<div class='address_block'>".$location_str."</div>";
                  ?>
                  
                  
                  
				</div>
		</div>
	</div>
     </td>
    </tr>
    <tr style="background-color: white;">
	<td width="" align="left" valign="top">
            <!--
            start prin div
            -->
            <div id="main_parent_print_div" style="display:none">
            <div id="print_coupon_div" >
            
            </div>
            </div>
            <!--
            end print div
            -->
            <?php
			
			
			
        $DetailHtml="";
	 $photos_div="";
        $Campaign_list ="";
						
         		$c_arr = array();
	$loc_arr = array();
         $loc_arr_more=array();
        $ct =0;
        $ct_more=0;
        //echo count($records_array1);
	
		
			 ?>
			 <?php
			 ?>
			 <div id="media-upload-header">
                        <ul id="sidemenu">
                            <li id="tab-type" data-hashval="aboutus" class="div_about_us"><a ><?php echo $client_msg['location_detail']['label_About_Us'];?></a></li>
							<?php
							if($selected_location[0]->location_detail_display==1)
							{
							?>
                            <li id="tab-type" data-hashval="<?php echo $selected_location[0]->location_detail_title;?>" class="div_location_detail"><a ><?php echo $selected_location[0]->location_detail_title;?></a></li>
							<?php
							}
							?>
							<?php
							if($selected_location[0]->menu_price_display==1)
							{
							?>
							<li id="tab-type"  data-hashval="<?php echo $selected_location[0]->menu_price_title;?>" class="div_menu"><a ><?php echo $selected_location[0]->menu_price_title;?></a></li>
                            <?php
							}
							?>
							<li id="tab-type" data-hashval="photos" class="div_more_images"><a><?php echo $client_msg['location_detail']['label_More_Images'];?></a></span></li> 
                            <li id="tab-type" data-hashval="offers"  class="div_offers" ><a ><?php echo $client_msg['location_detail']['label_Campaigns'];?></a></li>
                            <li id="tab-type" data-hashval="reviews" class="div_reviews"><a><?php echo $client_msg['location_detail']['label_Reviews'];?></a></span></li>
                                                       
                        </ul>
                </div>
           <div class="all_div_container">
				<div id="div_offers" class="tabs" style="display:none;">
                  <h1 ><?php echo $client_msg['location_detail']['label_Offers_Availabel'];?></h1>
				  
				 	<div id="offer_tab_loader" class="ajaxloader" align="center" style="display:none;position: absolute; left: 426px; margin-top: 103px;">
						<img style="position: absolute; z-index: 2147483647;" src="<?php echo ASSETS_IMG;?>/c/24.GIF">
					</div><div id="dealslist" class="dealslist_div" style="display:none;"> 
                    <!-- start of T_3 -->
                  
                    <!-- end of T_3 --> 
                  </div>
		  <div class="deallistwrapper mainul" style="display:none"></div>
		<div class="deallistwrapper navigationul" style="display:none"></div>
		</div><!-- div_offers div -->
				
				<div id="div_reviews" class="tabs" style="display:none;">
					
					<div id="review_tab_loader" class="ajaxloader" align="center" style="display:none;position: absolute; left: 426px; margin-top: 103px;">
						<!--<img style="position: absolute; z-index: 2147483647;" src="<?php echo ASSETS_IMG;?>/c/24.GIF">-->
					</div>
					<div id="div_reviews_empty" style="display: none;">
					<h1><?php echo $client_msg['location_detail']['label_Review_Neighborhood']?></h1>
					<hr>
					
				<div class="sortlinks">
				<div class="no_Records_Found" align="center">Visit this location and be the first to write a review.</div>		
				</div>
			 </div>

					<div class="review_location" style="display:block;"><div class="sortlinks">
						<h1><?php echo $client_msg['location_detail']['label_Review_Neighborhood']?></h1>
						<input type="hidden" id="sort_by_review" data-order="desc" value="reviewed_datetime" />
                                                                    <?php echo $client_msg['location_detail']['label_Sort']; ?>
                                                                    <a class="sort_data" sort="desc" data-name="rating" ><?php echo $client_msg['location_detail']['label_Rating']; ?></a> | 
                                                                    <a class="sort_data_datetime" style="color:#FF870F;" sort="asc" data-name="reviewed_datetime"><?php echo $client_msg['location_detail']['label_Date']; ?></a> | 
                                                                    <a class="sort_data_helpfull" sort="desc" data-name="is_usefull" ><?php echo $client_msg['location_detail']['label_helpfull']; ?></a>
                                                                </div>
<table width="100%"  border="0" cellspacing="1" cellpadding="2" class="" id="review_table_by_loc"></table>

</div>
					
				</div><!-- div_reviews div -->
                                
                                <div id="div_about_us" class="tabs" style="display:none;">
                                    <div >
                                        <p><?php echo $selected_location[0]->aboutus; ?></p>
                                    </div>
                                </div>
								<!-- start location details div 17 12 2013 -->
								<div id="div_location_detail" class="tabs" style="display:none;">
								<!--
									<div class="pricerangebox" style="display:none;">
										$ = Inexpensive, Under $10
										<br>
										$$ = Moderate, $11 - $30
										<br>
										$$$ = Expensive, $31 - $60
										<br>
										$$$$ = Very Expensive, Above $61
									</div>
								-->
									<?php echo $locationdetailhtml; ?>
                                </div>
								<!-- end location details div 17 12 2013 -->
								
                                <!-- Div More Images -->
	
                                <div id="div_more_images" class="tabs" style="display:none;">
					
                                    
                                    <div id="photo_tab_loader" class="ajaxloader" align="center" style="display:none;position: absolute; left: 426px; margin-top: 103px;">
										<img style="position: absolute; z-index: 2147483647;" src="<?php echo ASSETS_IMG;?>/c/24.GIF">
									</div>
				</div>
                                <!-- End Images -->
                                
                                <div id="div_menu" class="tabs" style="display:none;">
                                        <div id="menu_tab_loader" class="ajaxloader" align="center" style="display:none;position: absolute; left: 426px; margin-top: 103px;">
											<img style="position: absolute; z-index: 2147483647;" src="<?php echo ASSETS_IMG;?>/c/24.GIF">
										</div>
                                </div>
                     </div>           
                </td>
              </tr>
            </table>
            <?php 
          
                  //  }
                //}
                ?>
            <!--end of detail--></div>
          <!--end of dealDetailDiv--></div>
        <div class="dealDetailFooter">&nbsp;</div><!--end of dealDetailFooter--> 
        <!--end of dealDetail--></div>
            </div>
        </div>
      <script>
	  try{
	  function close_popup(popup_name)
    {

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
    function open_popup(popup_name)
    {

	
        $("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
            $("#" + popup_name + "BackDiv").fadeIn(200, function () {
                $("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
                });
            });
        });
	
	
    }
	  jQuery("#btncancelprofile").live("click",function(){
       jQuery.fancybox.close();
    });
    }
catch(e)
{
}
            </script>
     
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
	</div>
	<?php require_once(CUST_LAYOUT."/before-footer.php");?>
	</div>
	</div>

    <?
		require_once(CUST_LAYOUT."/footer.php");
		?>

<div id="NotificationloaderPopUpContainer" class="container_popup"  style="display: none;">
                                        <div id="NotificationloaderBackDiv" class="divBack">
                                        </div>
                                        <div id="NotificationloaderFrontDivProcessing" class="Processing" style="display:none;">
                                            
                                             <div id="NotificationloaderMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                                              style="left: 45%;top: 40%;">
                                                
                                                <div id="NotificationloadermainContainer" class="loading innerContainer" style="height:auto;width:auto">
                                                        <img src="<?=ASSETS_IMG?>/c/128.GIF" style="display: block;" id="image_loader_div"/>
                                                 </div>
                                            </div>
                                        </div>
     
</div>

 

<div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
                                        <div id="NotificationBackDiv" class="divBack">
                                        </div>
                                        <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">
                                            
                                             <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                                              style="left: 45%;top: 40%;">
                                                
                                                <div id="NotificationmainContainer" class="innerContainer" style="height:auto;width:auto">
                                                        <img src="<?=ASSETS_IMG?>/c/128.GIF" style="display: block;" id="image_loader_div"/>
                                                 </div>
                                            </div>
                                        </div>
     <div id="myDivID_3">
       <div id="popupmainid" class="popupmainclass" style="padding:11px;">
	<div class="detailclass">
          <div class="detailclassleft">
                  <div class="bussinessclass">
                    <span>
                         KFC                    </span></div>
                    <div class="CampTitle">
                        Kfc-private-kfc-toronto-camp3                        
                    </div>
                    <div class="limitclass">
                        <div style="margin-top:5px;"><b><?php echo $client_msg['common']['label_limit'];?> </b><span class="popup_limit">Earn Redemption Points On Every Visit</span></div>               
                    </div>
                     <div class="locationclass">
                        <div class="locationlabel"><b><?php echo $client_msg['common']['label_Where_To_Redeem'];?></b><span class="popup_address"> F4-1000 Gerrard St E Toronto ON USA </span></div>
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
                        <div class="printclass" style="display:none">
				<a href="" name="btn_print" id="printid" style="color:white !important">
				<?php echo $client_msg['common']['label_Print_Offeres'];?>
				</a>
			</div>
			
      </div>
     
      
      <div class="expirationclass">
          <div class="expirationlabel">
              <div style="float:left">
                  <b><?php echo $client_msg['common']['label_Expiry_Date'];?> </b>
			<span class="popup_expiration">05/23/13 5:04 AM </span>             </div>
                    <div class="errorclasspopup" style="color:red;font-size: 14px;margin-top: 4px;width: 100%;float:left;height: 15px;"></div> 
					<div class="share_loader" style="display:none;">
				
			  </div> 	
              <div class="reserve" style="">
                  <img src="<?php echo ASSETS_IMG?>/c/save_deal.png" style="display:none;margin-right:25px;float:left;" id="saveofferid">
			<div style="display:none;" class="cust_attr_tooltip">
									<div class="arrow_down"></div>
					<?php echo $client_msg['common']['label_Offer_Reserved'];?>
			</div>
                  <input type="hidden" name="is_profileset" id="is_profileset" value="" />
                <input type="hidden" name="profile_view" id="profile_view" value="" />
                <input type="hidden" name="visited_counter" id="visited_counter" value ="0" />
                <input type="hidden" name="visited_deals" id="visited_deals" value="" />
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
          
          <div align="center" class="showvocherdiv" style="display:none;">
	<img class="barcode" src="">
      </div>
      <div class="sharediv">
        <span class="popup_sharetext">
                            <?php echo $client_msg['common']['label_Share_Earn_Points'];?>
                    </span>
        <div class="popup_sharediv">
             <div >
                                        <a id="btn_msg_div" target="_parent" class="email_link" href="javascript:void(0)" >Email</a>
                                 </div>
                                     
             <div >
                  			   <!-- <a href="javascript: void(0)" target="_parent" onclick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=Kfc-private-kfc-toronto-camp3&amp;p[summary]=dgdf&amp;p[url]=http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D&amp;&amp;p[images][0]=http://174.36.167.50/scanflip/merchant/images/default_campaign.jpg', 'sharer', 'toolbar=0,status=0,width=548,height=325');" class="btn_share_facebook">
				Facebook
			    </a>-->
                 <a class="btn_share_facebook loginshare" id="" data="" target="_parent"  href="javascript: void(0)">     
                                Facebook
                        </a> 
                            	     </div>
            
               <div>
				
				<!--				<a  href="http://174.36.167.50/scanflip/register.php?url=http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D" url="http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D" class="twitter-share-button" data-lang="en" data-count="none">Tweetr</a> -->
                                	<!--<a data-count="none" data-lang="en" class="twitter_link" url="http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D549%26l_id%3D91%26share%3Dtrue%26customer_id%3DOTk%3D" onclick="">Twitter</a> -->
                                   <a  href="javascript:void(0)" onclick="" data-count="none"  data-lang="en" class="twitter_link" >Twitter</a>      
								 
				
			      <script charset="utf-8" type="text/javascript">
				  try{
			      var customer_id="99";
			      
			      //if(customer_id != "")
			      //{
					window.twttr = (function (d,s,id) {

        var t, js, fjs = d.getElementsByTagName(s)[0];

        if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;

        js.src="//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);

        return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });

      }(document, "script", "twitter-wjs"));							
			     // }
			     
 
	}
catch(e)
{
}	   
    </script>
                            </div>
             
                   <div >
                       <?php 
                                   $campaign_id=549;
                                   $l_id=91;
                                   $share="true";
                                 
                                   ?>
                   <a href="javascript:void(0)" 
										class="g-interactivepost google_plus_link" 
										data-prefilltext="#Scanflip Offer -$16 for All-Inclusive Daytime Admission (a $30 Value).. now available at Best Westerns Plus Cairn Croft Hotel participating locations.Limited Offers.. Reserve Now" 
										data-contenturl="https://www.scanflip.com/Sport-Activities/Best-Westerns-Plus-Cairn-Croft-Hotel16-for-All-Inclusive-Daytime-Admission-a-30-Value-MTAwOA==-ODE=.html" 
										data-calltoactionurl="https://www.scanflip.com/Sport-Activities/Best-Westerns-Plus-Cairn-Croft-Hotel16-for-All-Inclusive-Daytime-Admission-a-30-Value-MTAwOA==-ODE=.html" 
										data-clientid="1043802627758-jsh3e6koq6a34tqij458b4s0adk1ca88.apps.googleusercontent.com" 
										data-cookiepolicy="single_host_origin" 
										data-calltoactionlabel="RESERVE" 
										data-contentdeeplinkid="/scanflip/register" 
										data-calltoactiondeeplinkid="/scanflip/register" 
										data-onshare="callbackfromgoogleplus" 		
									 >
                                         Google+
                                     </a>

                           
                            
                            </div>
        
			
        </div>
      </div>
      
     

      
</div>  
      
                            <div class="email_popup_div container_popup" style="padding:11px;">
                                
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
                                    <div class="text_area"><textarea rows="4" cols="45" style="resize:none" id="txt_share_frnd" name="txt_share_frnd" ></textarea></div>
                                   
                                    <div class="buttons_bot">
                                       
                                        <input type="hidden" name="reffer_campaign_id1" id="reffer_campaign_id" value="" />
                                        <input type="hidden" name="reffer_campaign_name1" id="reffer_campaign_name" value="" />
                                        <input type="hidden" name="refferal_location_id1" value="" id="refferal_location_id" />
                                        <input type="button" class="btnsharegridbutton" value="Share" name="btn_share_grid" id="btnsharegridbutton" />
                                        <input type="button" class="btnsharecancelbutton" value="Cancel"  id="btn_cancel"  />
					<?php $img_src=ASSETS_IMG."/c/loding.gif";?>
					<img src="<?php echo $img_src; ?>" alt="" id="shareloading" height="20px" width="20px" style="display:none"/>
                                    </div>
                               
                            </div>
                        </div><!-- -->
                        
                        <div class="mainloginclass" id="mainloginid" style="display:none">
                    <div id="modal-login">
                        <h2 id="modal-login-title"><?php echo $client_msg['login_register']['label_Login'];?></h2>
              
                        <div class="calltoaction callout unit100">
                         <?php echo $client_msg['login_register']['label_Not_Member_Yet'];?> <a href="<?= WEB_PATH."/register.php" ?>" target="_parent"><strong><?php echo $client_msg['login_register']['label_Join_Now'];?></strong></a>
                        </div>
                        <div id="form_login">
                                <form class="form_vertical" action="<?php echo WEB_PATH; ?>/process.php" method="post"  id="login_frm">
                                    
                                 <div id="msg_error" style="display:block;color:red;height: 20px;font-size: 13px;font-style: italic;font-weight: bolder;">
                                        
                                    </div>
                                  <label for="email-modal"><?php echo $client_msg['login_register']['Field_email'];?></label>
                                  
                              
                              
                              
                                  <input type="text" value="" class="js-focus unit100" maxlength="128" name="emailaddress" id="email-modal">
                              
                                  <label for="password"><?php echo $client_msg['login_register']['Field_password'];?></label>
                                  
                              
                              
                              
                                  <input type="password" maxlength="15" class="unit100" name="password" id="password" style="padding:0.231em;">
                              
                                  <div>
                                   	<input id="hdn_reload_pre_selection_loc" type="hidden" name="hdn_reload_pre_selection_loc" value="" />
                                    <input type="submit" class="btn btn_primary mr10" value="<?php echo $client_msg['login_register']['Field_login_button'];?>" name="btnLogin" id="login_submit">
                                  </div>
                                </form>
              
                                <div class="mt20" style="">
                                    <div style="padding-bottom: 5px">
                                      <span style="float:left;width:140px;margin-top:10px;">
                                             <?php echo $client_msg['login_register']['label_Register_Sign_In'];?>
                                      </span>
                                   </div>
                                  <div class="left ml5" style="float:left;">
								   <?php 
								   $redirect_query_location = "select location_permalink from locations where id=".$decode_location_id;
								 $RS_redirect_query = $objDB->Conn->Execute($redirect_query_location);		
								 $location_url = $RS_redirect_query->fields['location_permalink'];
								   ?>
                                      <span class="btn btn_gplus glyph glyph_inverse glyph_gplus" id="g-signin-custom">
                                            <a target="_parent" location_url="<?php echo $location_url; ?>" class="login" href="<?php echo $authUrl; ?>">
												google+
											</a>
                                      </span>
                                   </div>
                                  
                                    
                                   <div class="left ml5" style="margin-left:10px;float:left;">
								   <?php 
								   //$redirect_query_location = "select location_permalink from locations where id=".$decode_location_id;
								 //$RS_redirect_query = $objDB->Conn->Execute($redirect_query_location);		
								 //$location_url = $RS_redirect_query->fields['location_permalink'];
								   ?>
                                        <a href="<?php echo WEB_PATH."/facebook_login.php?redirect_url=".$location_url; ?>" target="_parent" class="btn btn_facebook glyph glyph_inverse glyph_facebook">
                                        
                                              facebook
                                        </a>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                
                                				   
                
                                <div class="mt20">
                                  <a href="javascript:void(0)" class="textlink" style="border-bottom:1px solid #0F2326;color:#0F2326"><?php echo $client_msg['login_register']['label_Forgot_Password'];?></a>
                                </div>
                            </div>
                 </div>
        </div> <!--- -->
        <div class="forgotmainclass" id="forgotmainid" style="display:none;padding-left:20px;padding-right:20px">
        <form action="" method="post" id="reg_form">
        <div style="font-weight:bold;padding:5px 5px 5px 0px;"><?php echo $client_msg['login_register']['label_Forgot_Password_Assistance']?></div>
        <div>
		<label for="email-requestPasswordReset"><?php echo $client_msg['login_register']['label_Forgot_Assistance']?></label>
	</div>
	
	
            <div class="emaibox">
				<table>
					<tbody><tr style="width:100%;float:left;">
						<td width="150px" style="font-size:0.8em;">
							<b><?php echo $client_msg['login_register']['Field_Forgot_Emailaddress'];?></b>
						</td>
						<td width="150px">
							<input type="text" name="email" id="email" style="width:100%">
						</td>
						
					</tr>
                                        <tr style="margin-bottom: 10px;width: 100%;float: left;height:20px;">
                                            <td style="width:300px;margin-bottom:0px;">
                                                <div id="emailerror" style="font-style: italic;width: 100%;margin-top: 5px;font-weight: bolder;color: red;display: inline-block;font-size: 13px;float: left;"></div>
                                            </td>
                                        </tr>
					<tr style="width:100%;float:left;">
						<td style="width:150px">
							<b><?php echo $client_msg['login_register']['Field_Forgot_Captcha'];?></b>
						</td>
						<td colspan="2">
							<input type="text" id="mycaptcha_rpc" name="mycaptcha_rpc" style="width:50%;" /><br/>
						</td>
						
					</tr>
					<tr style="width:100%;float:left;">
						<td width="150px" style="font-size:0.8em;">
							<a id="captcha_image" href="javascript:void(0)"><?php echo $client_msg['login_register']['label_captcha_different'];?></a>
						</td>
						<td>
							<!--<img id="captcha_image_src" src="captcha.gif" style=""/>-->
							 <img src="<?php echo WEB_PATH ?>/get_captcha_c_p.php" alt="" id="captcha_image_src" />
						</td>
						<td>
							<img id="captcha_ajax_loading" class="img_captcha_loading" style="display:none" src="<?php echo ASSETS_IMG;?>/c/ajax-loader-1.gif"/>
						</td>
					</tr>
                                         <tr style="margin-bottom: 10px;width: 100%;float: left;height:20px;">
                                            <td style="width:100%;margin-bottom:0px;">
                                                <div id="captchaerror" style="font-style: italic;width: 100%;margin-top: 5px;font-weight: bolder;color: red;display: inline-block;font-size: 13px;float: left;"></div>
                                            </td>
                                        </tr>
				</table>
            </div>
            
	    
	    <div class="forgotmsgdiv">
	      
	    </div>
            <p class="actions" style="">
            	<input type="button" id="btnRequestPassword" name="btnRequestPassword" value="Continue" onClick="">
                <input type="button" class="btnsharecancelbutton" value="Cancel"  id="btn_cancel_forgot"  />
            </p>
	    <div style="margin-bottom:20px;">
	      <b><?php echo $client_msg['login_register']['label_Having_Trouble']?></b>&nbsp;&nbsp;<a href="javascript:void(0)"><?php echo $client_msg['login_register']['label_Contact_Service']?></a>
	  </div>
            <p>
            	<?php //echo $_SESSION['req_pass_msg']; ?>
            </p>
        </form>
    </div>
         <?php
                        if(isset($_SESSION['customer_id'])){
                        $JSON = $objJSON->get_customer_profile();
$RS = json_decode($JSON);
                        ?>
        <div class="updateprofile" id="updateprofile" style="display:none;padding:11px;" >
                    <div id="modal-login" style="height:310px;text-align:left;width:386px;line-height:17px">
                        <h2 id="modal-login-title">Update Profile</h2>
              
<div class="unit100" style="height:30px;">
                        </div>
                        <div id="form_login">
                                <form class="form_vertical" action="<?php echo WEB_PATH; ?>/process.php" method="post"  id="Not_Set_Login_Frm_reserve">
                              
                                  <label for="email-modal"><?php echo "* ".$language_msg["profile"]["gender"];?></label>
                                  <select name="gender" id="gender" class="genderclass"   style="padding:0.231em;margin:0 0 0.154em;"   >
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
                            
                                   <label for="country">* Country:</label>
                                  <select name="country" id="country" class="countryclass" style="margin:0 0 0.154em;">
				     <option></option>
		<option value="USA" <? if($RS[0]->country == "USA") echo "selected";?> country_code="US">USA</option>
		<option value="Canada" <? if($RS[0]->country == "Canada") echo "selected";?>  country_code="CA">Canada</option>
	   
	</select>
                             
			     <div class="err_country" style="color:red;height: 18px"></div>
                                   <label for="postalcode">* Postal Code:</label>
                                   <input type="text" maxlength="15"  name="postalcode" id="postalcode" class="postalcodeclass" style="padding:0.231em;margin:0 0 0.154em;" value="<?=$RS[0]->postalcode?>">
<!--                                   <input type="text" name="postalcode" id="postalcode" style="width:120px;" value=""  class="unit100" style="padding:0.231em;" >-->
                                   <br/>
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
	      <b><?php echo $client_msg['login_register']['label_Having_Trouble'];?></b>&nbsp;&nbsp;<a href="javascript:void(0)"><?php echo $client_msg['login_register']['label_Contact_Service'];?></a>
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
</div>

 <div style="display: none"id="confirmation"> 
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
	<!--
<div id="enter_zipcode_div" style="display:none">
        <div  style="width:430px">
      
        <div class="zipcode_section" style="margin:10px;">
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
<?php  if(isset($_SESSION['customer_id'])){ ?>
<div id="div_profile" style="display:none">
        <input type="hidden" name="subscribe_location_id" id="subscribe_location_id" value="" />
     <div class="updateprofile" id="updateprofile" style="display:block;padding:11px;overflow-y:scroll" >
                    <div id="modal-login" style="height:310px;text-align:left;width:386px;line-height:17px">
                        <h2 id="modal-login-title"><?php echo $client_msg['login_register']['label_Update_Profile'];?></h2>
              
<div class="unit100" style="height:30px;">
                        </div>
                        <div id="form_login">
                                <form class="form_vertical" action="process.php" method="post"  id="Not_Set_Login_Frm_Sububscribe">
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
  <div id="wrong_file_data" style="display:none;width:400px;height:320px">
                       
                            
                            <form  action="search-deal.php" method="post" enctype="multipart/form-data">
                                  <div style="height:auto;text-align:center;padding:10px;font-size:15px;margin-right: 19px;width:400px;">
				    <?php echo $client_msg['location_detail']['label_This_Campaign_Not_Longer'];?>
				  </div>
					<div style="margin-top:16px;margin-bottom: 10px;" align="center">
                                            
                                            <input type="submit" class="popupcancel" name="popupcancel" id="popupcancel" value="Cancel" />
                                        </div>
                        </form>
                       
					
                </div>
<div class="inner withClose" id="overlayInnerDiv_review2" style="display:none"><div>
<br>
<div style="width:auto; margin:10px;">

<input type="hidden" value="151127055" name="reviewid">
<input type="hidden" value="2512206" name="locationid">
<input type="hidden" value="" name="fp">
<div style="display:none; margin-bottom: 100px; color: red;" id="errorMsg">
The explanation is required.
<br>
<br>
</div>
<div style="display:none; margin-bottom: 100px; color: red;" id="explanationTooLongErrorMsg">
Please limit your explanation to 200 characters.
<br>
<br>
</div>

<div>
<!-- Select a reason -->
<?php echo $client_msg['location_detail']['label_suspicious_heading1'];   ?>

<br><br>
</div>
<div>
<select  style="width:100%" id="reasonSelect" name="reason">
<option value="-1">Select</option>
<option value="18"><?php echo $client_msg['location_detail']['option_staff'];   ?></option>
<option value="19"><?php echo $client_msg['location_detail']['option_competitor'];   ?></option>
<option value="9"><?php echo $client_msg['location_detail']['option_noneofabove'];   ?></option>
</select>
<br>
<br>
</div>
<!-- Expain why review is written by the owner. -->
<div style="display:none;" id="reason18msg" class="reasons">
<?php echo $client_msg['location_detail']['text_staff'];   ?>
<br>
<br>
</div>
<div style="display:none;" id="reason19msg" class="reasons">
<?php echo $client_msg['location_detail']['text_competitor'];   ?>
<br>
<br>
</div>
<div style="display:none;" id="reason20msg" class="reasons">
<?php echo $client_msg['location_detail']['text_noneofabove'];   ?>
<br>
<br>
</div>
<div style="display:none;" id="reason9msg" class="reasons">
<?php echo $client_msg['location_detail']['text_noneofabove'];   ?>
<br>
<br>
</div> 
<div style="display:none" id="reasonExplanation">
<div class="review_error">&nbsp;
</div>
<textarea style="width:98%;" rows="5" name="comment_suspicious" class="comment"></textarea>
<div style="text-align:right;">
<div style="float:left;"><span class="character_counter"></span></div>
<?php echo $client_msg['location_detail']['label_total_no_of_characters'];   ?> characters maximum
</div>
</div>
<div style="display:none" class="button" id="iapSubmitButton">
<div class="button">
<input type="button" name="btn_review_suspicioups" id="btn_review_suspicioups" value="Submit" />
</div>
<div style="clear:both;"></div>
<div class="vrEmailPrivacy" style="margin-top:15px;">
<p><?php echo $client_msg['location_detail']['label_email_privacy'];   ?> </p> </div>

</div>
<div>
</div></div>
</div>
</div>
	<div class="inner withClose" id="overlayInnerDiv_review1" style="display:none"><div>
<div style="width:350px; margin:10px;">

<div style="display:none; margin-bottom: 10px; color: red;" id="explanationTooLongErrorMsg">
Please limit your explanation to 200 characters.
<br>
<br>
</div>
<input type="hidden" value="151127055" name="reviewid">
<input type="hidden" value="16" name="reason">
<input type="hidden" value="2512206" name="locationid">
<input type="hidden" value="" name="fp">
<div style="margin-bottom: 10px;">
<?php echo $client_msg['location_detail']['label_violates_heading1']; ?><a target="_blank" href="<?php echo WEB_PATH; ?>/guidline.php">guidelines.</a>
</div>

<div>
<div>
<div class="review_error">&nbsp;
</div>
<textarea style="width:100%;" rows="5" name="comment_violates" class="comment"></textarea>
</div>
<div style="text-align:right;">
<div style="float:left;"><span class="character_counter"></span> </div>
<?php echo $client_msg['location_detail']['label_violates_total_no_of_characters'] ; ?> characters maximum
</div>
</div>
<div class="button">
<input type="button" name="btn_review_violates" id="btn_review_violates" value="Submit" />
</div>
<div style="clear:both;">
</div>
<div class="vrEmailPrivacy" style="margin-top:15px;">
<?php echo $client_msg['location_detail']['label_violates_email_privacy'] ; ?>  </div>

</div>
</div>
</div>	
<input type="hidden" name="hdn_resson_review" id="hdn_resson_review" value="" />
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
<?php 

 $street_main_image = file_get_contents('https://maps.googleapis.com/maps/api/streetview?size=400x310&location=46.414382,10.013988&sensor=false&key=AIzaSyBsvIV_4NNaCz9d2tSS6EeW01wIj98lmFA'); 
$image_path_main=ASSETS_IMG."/m/location/"; 
$fp  = fopen('main.jpeg', 'w+'); 

fputs($fp, $street_main_image); 

//$street_thumb_image = file_get_contents('http://maps.googleapis.com/maps/api/streetview?size=70x70&location=46.414382,10.013988&sensor=false&key=AIzaSyBsvIV_4NNaCz9d2tSS6EeW01wIj98lmFA'); 
//$image_path_thumb=WEB_PATH."/merchant/images/location/thumb/"; 
//$fp_thumb  = fopen($image_path_thumb.'location_'.strtotime(date("Y-m-d H:i:s")).'.jpeg', 'w+'); 

//fputs($fp_thumb, $street_thumb_image);

?>

<?php
    $venue_id_sql="select * from locations where id=".$decode_location_id;
    $venue_id_detail=$objDB->Conn->Execute($venue_id_sql);
    
    if($venue_id_detail->fields['venue_id'] != "")
    {
?>
<!--
<div class="locumaindiv" style="display:block">
        <div class="sublocumaindiv" style="display:block;width:700px;margin:0 auto">
            <script type="text/javascript" id="-locu-widget" src="https://widget.locu.com/menuwidget/locu.widget.developer.v2.0.js?venue-id=<?php echo $venue_id_detail->fields['venue_id'];?>&widget-key=54eee8f8fde75da95fc2fd147b3893679815f6ba"></script>
        </div>
</div>
-->
    <?php } ?>

</body>
<?php if(isset($_SESSION['customer_id'])){
    $customer_id_p = $_SESSION['customer_id'];
} else{
    $customer_id_p = "";
}
?>

</html>
<script>
try{
// twttr.events.bind('tweet', function(event) {
//        location.replace(location.href + "&twitshare=1");
//
// });
}
catch(e)
{
}
</script>
<script>
 try{
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
						
							jQuery(".deal_blk[locid='"+lid+"'][campid='"+cid+"'] .btn_print1").trigger("click");
							jQuery.fancybox.close(); 
						}
                  }
          });
        }
    });                    
//function bind_reserve_event()
//{
	//alert("hello");
jQuery(".fancybox-inner .btn_mymerchantreserve").live("click",function(){
   //alert("sadjhsad");
	//alert("hello"+jQuery("#rservedeals_value").val());
        
    var flag= true;
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
    //if(jQuery("#is_profileset").val() != 1){
     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'is_userprofileset=true&custome_id=<?php echo $customer_id_p; ?>&campaign_id='+$(this).attr("cid")+'&location_id='+$(this).attr("lid")+'&reload=yes',
          async:false,
		  success:function(msg)
		  {
		//  alert(msg);
		  //return flase;
                       var obj = jQuery.parseJSON(msg);
                      
                      if(obj.is_profileset == 1)
                      {
                          flag= true;
                      }
                  else{
                       jQuery("#profile_view").val("rserve");
                        flag = false;
                    jQuery(".fancybox-inner .popupmainclass").css("display","none");
                    jQuery(".fancybox-inner .email_popup_div").css("display","none");
                    jQuery(".fancybox-inner .mainloginclass").css("display","none");
					
					//jQuery(".fancybox-inner #Not_Set_Login_Frm_Sububscribe").html("");
					jQuery(".fancybox-inner #Not_Set_Login_Frm_reserve").html(jQuery("#notprofilesetid").html());
                       jQuery(".fancybox-inner .updateprofile").css("display","block");
                      //alert("You Profile IS not set. First set it");
					  
						   //alert("final"+jQuery(".fancybox-prev").length+"==="+jQuery(".fancybox-next").length);
						   jQuery(".fancybox-prev").css("display","none");
						   jQuery(".fancybox-next").css("display","none");
                  }
                  }
     });
     if(! flag)
     {
         return false;
     }
 //    }
if(jQuery("#rservedeals_value").val()!=" ")
    {
        reserve_val = jQuery("#rservedeals_value").val();
    }
      if(jQuery("#deal_barcode").val()!=" ")
    {
        deal_barcode_val = jQuery("#deal_barcode").val();
    }
   //alert('btnreservedeal=true&campaign_id='+$(this).attr("cid")+'&location_id='+$(this).attr("lid"));
  jQuery.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnreservedeal=true&campaign_id='+$(this).attr("cid")+'&location_id='+$(this).attr("lid")+'&reload=yes&is_location=yes&timestamp='+formattedDate(),
          async:false,
		  success:function(msg)
		  {
			     //aert(msg);
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
                              jQuery("#rservedeals_value").val(reserve_val);
                            //parent_tr.detach();
                            jQuery(".fancybox-inner  .btn_mymerchantreserve").detach();
                           // alert("=="+obj.barcode+"=="+(obj.barcode).length);
                           // if(jQuery(".fancybox-inner  .barcode").attr("src") != ""){
                                jQuery(".fancybox-inner  .barcode").attr("src",obj.barcode);
                           // }
                            jQuery(".fancybox-inner  .sharediv").css("display","block");
                            jQuery(".fancybox-inner  .showvocherdiv").css("display","none");
                            jQuery(".fancybox-inner  #ShowshareId").css("display","none");
                            jQuery(".fancybox-inner  #ShowVoucherId").css("display","block");
			    jQuery(".fancybox-inner  #saveofferid").show();
                            deal_barcode_val += ele.attr("cid")+"="+ele.attr("lid")+"&"+obj.barcode+";";
                               var ct =getParam( 'br' , obj.barcode );
                             $("#deal_barcode").val(deal_barcode_val);
                           if(jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view").length == 1)
                               {
                                      jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view-reserve").css("display","none");
                                      jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view").show();
                         jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .btn_unreserve").show();
                          jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .btn_print1").css("display","block");
                               }
                               else{
							    redirect_href = jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view-reserve").attr("href");
						
							   jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .view-reserve").hide();
                          jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview").append('<a class="view" href="'+redirect_href+'" target= "_parent">View</a>&nbsp<a href="javascript:void(0)" class="btn_unreserve" u_campid="'+ele.attr("cid")+'" u_locid="'+ele.attr("lid")+'" >Unreserve</a>&nbsp');
                           jQuery("#dealslist .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ele.attr("cid")+"'] .btnview .kat_img").after("<img class='btn_print1' cid='"+ele.attr("cid")+"' lid='"+ele.attr("lid")+"' barcodea='"+ct+"' align='left' style='margin:5px 0 0 10px;' src='<?php echo ASSETS_IMG?>/c/icon-print-deal2-black.png' />");
                           //
                               }
                            jQuery("#deal_barcode").val(deal_barcode_val);
                             var ct =getParam( 'br' , obj.barcode );
                            var url_d = jQuery("#dealslist  .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid');
                            if(ct!= ""){
                              url_d = url_d.replace(/(br=)[^\&]+/, '$1' + ""+ct+"");
                            }
                            url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "1");
                             url_d = url_d.replace(/(is_mydeals=)[^\&]+/, '$1' + "1");
							  url_d = url_d.replace(/(o_left=)[^\&]+/, '$1' + ""+obj.o_left+"");
                            //url_d = url_d.replace(/(br=)[^\&]+/, '$1' + ct);
                            jQuery("#dealslist   .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid',url_d);
                            //'<a class="view" href="campaign.php?campaign_id='.$Row->cid.'&l_id='.$storeid.'" target= "_parent">View</a>&nbsp<a href="javascript:void(0)" class="btn_unreserve" u_campid="'.$Row->cid.'" u_locid="'.$storeid.'" >Unreserve</a>&nbsp'
                            //jQuery("#dealslist   .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle").attr('mypopupid',url_d);
                            
            //jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .view")
                         }
                         else{
                             //alert("Sorry , "+obj.error_msg);
                             jQuery(".fancybox-close").trigger("click");
               // jQuery(".displayul_all .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"']").detach();
                            // jQuery(".displayul .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"']").detach();
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
                    // alert(1);
                    }
                 }
   });
   return false;
 //  window.location.href = $(this).attr("redirect_url");
});
//}

        var $s = jQuery.noConflict();  
	$s("#btn_share_div").click(function(){
		
		 $s("#activatesahreDiv").slideToggle("slow");

	});
	$s("#btn_share").click(function(){
		
		var email_str = $s("#txt_share_frnd").val();
		
		var email_arr = email_str.split(";");
		
		var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
		var flag = true;
		for(i=0;i<email_arr.length;i++)
		{
			if(! mailformat.test(email_arr[i]))
			{
				flag = false;
				break;
			}
		}
		if(flag)
		{
			return true;
		}
		else {
			$s("#activatesahreDiv p:first").text("Please enter valid email addresses separated by ; ")
			return false;
		}
	});
        var $s = jQuery.noConflict();
$s(".btnfilterstaticscampaigns").live("click",function(){
	
	$s(".btnfilterstaticscampaigns").each(function(index){
            $s(this).css("color","#3B3B3B");    
    });
		 
    $s(this).css('color','orange');
	var WEB_PATH = "<?=WEB_PATH?>";
	selected_cat_id = $s(this).attr("mycatid");
    setCookie("cat_remember",selected_cat_id,365);
	
	window.location.href='<?= WEB_PATH ?>/search-deal.php';
                  
});

}
catch(e)
{
}

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

<script type="text/javascript" src="<?=ASSETS_JS?>/c/jquery.PrintArea.js_4.js"></script>

<script type="text/javascript" src="<?=ASSETS_JS?>/c/jquery.form.js"></script>
<script>
    try{
    jQuery.noConflict();
    
    
    function bind_popup_event()
   {
//     alert( jQuery('.fancybox-inner #login_frm').length);
       jQuery('.fancybox-inner #login_frm').ajaxForm({ 
		dataType:  'json', 
		success:   processLogJson 
	});
   }
   function processLogJson(data) {
       
	//alert(data.c_id+"==="+data.l_id);
	jQuery('.fancybox-inner #msg_error').html("");
	//alert("<?php echo $decode_location_id?>");
	//alert(data.redirecting_path);
		if(data.status == "true"){
		    var location_id="<?php echo $decode_location_id?>";
		   
			window.top.location.href = data.redirecting_path;
			location.reload();
		     //window.close();
			
		}else{
		   
			//alert(data.message);
                        jQuery('.fancybox-inner #msg_error').html(data.message);
			return false;
		}
     
	}
	}
catch(e)
{
}
	
	</script>

<script type="text/javascript">
  try{  
function facebookfunction(campaign_title,summary,th_link,imgsrc)
{
    //alert(imgsrc);
   
    
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
                          // window.location='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - "+encodeURIComponent(campaign_title)+"&p[summary]="+encodeURIComponent(summary)+"&p[url]="+encodeURIComponent(th_link)+"&p[images][0]="+imgsrc+"', 'sharer', 'toolbar=0,status=0,width=548,height=325';
                           urlpath='https://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - '+encodeURIComponent(campaign_title)+'&p[summary]='+encodeURIComponent(summary)+'&p[url]='+encodeURIComponent(th_link)+'&p[images][0]='+imgsrc;
                           //urlpath='https://www.facebook.com/sharer/sharer.php?s=100&p[title]=Scanflip+Offer+-+ABC&p[summary]=With+purchase+of+a+regular+fountain+drink%0D%0A%0D%0AExcluding+everyday+value+and+breakfast+menu.+Cannot+be+combined+with+any+other+offers.+Taxes+Extra&p[url]=http%3A%2F%2Fwww.scanflip.com%2Fregister.php%3Fcampaign_id%3D632%26l_id%3D70%26share%3Dtrue%26customer_id%3DOTk%3D&p[images][0]=http%3A%2F%2Fwww.scanflip.com%2Fmerchant%2Fimages%2Flogo%2Fcampaign_1374831916.png';
            ///alert(urlpath); 
							// 01 10 2013
						   urlpath='https://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - '+encodeURIComponent(unescape(campaign_title))+'&p[summary]='+encodeURIComponent(unescape(summary))+'&p[url]='+encodeURIComponent(th_link)+'&p[images][0]='+imgsrc;
						   // 01 10 2013
                            var open_popup=window.open(urlpath+'&p[images][0]='+decodeURIComponent(imgsrc),'_blank','width=500,height=400');
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

function twitterfunction(webpath,campaign_title,th_link)
{
    
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

function googlefunction(campaign_id,l_id,share)
{
    
    
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
                          var urlpath="https://plus.google.com/share?url=https://www.scanflip.com/register.php?campaign_id="+campaign_id+"&amp;l_id="+l_id+"&amp;share=true&amp;customer_id=OTk=";
                            //alert(urlpath);              
                           var open_popup=window.open(urlpath,'_blank');
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
     
     jQuery(".fancybox-inner #btn_msg_div").live("click",function(){
	      
        jQuery.ajax({
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
                           
                       jQuery(".fancybox-inner .popupmainclass").css("display","none");
                        
                        jQuery(".fancybox-inner .email_popup_div").css("display","block");
                           //jQuery(".email_popup_div").show();
                           
                       }
                      //alert(msg);
          
                          
                  }
            });
                 
                
                 
                jQuery(".fancybox-inner .popupmainclass").css("display","block");
                jQuery(".fancybox-inner .mainloginclass").css("display","none");
                jQuery(".fancybox-inner .updateprofile").css("display","none");
                 //$(".popupmainclass").show();
	    //   open_popup('emailNotification');
                

	});
    jQuery(".fancybox-inner #Notificationlogin").live("click",function(){
	//alert(getCookie("redirecting_url_fb_location"));
                jQuery(".fancybox-inner .popupmainclass").css("display","none");
                    jQuery(".fancybox-inner .email_popup_div").css("display","none");
            jQuery(".fancybox-inner .mainloginclass").css("display","block");
            jQuery(".fancybox-inner .updateprofile").css("display","none");
        });
    jQuery(".fancybox-inner .textlink").live("click",function(){
              jQuery(".fancybox-inner .forgotmainclass").css("display","block");
              jQuery(".fancybox-inner .email_popup_div").css("display","none");
              jQuery(".fancybox-inner .popupmainclass").css("display","none");
              jQuery(".fancybox-inner .mainloginclass").css("display","none");
              jQuery(".fancybox-inner .updateprofile").css("display","none");
        });
    jQuery(".fancybox-inner #btn_cancel_forgot").live("click",function(){
            jQuery(".fancybox-inner .email_popup_div").css("display","none");
                    jQuery(".fancybox-inner .popupmainclass").css("display","block");
                jQuery(".fancybox-inner .mainloginclass").css("display","none");
                 jQuery(".fancybox-inner .forgotmainclass").css("display","none");
                 jQuery(".fancybox-inner .updateprofile").css("display","none");
        });
		jQuery("body").on("click","#Not_Set_Login_Frm_Sububscribe #btn_cancel_forgot",function(){
		jQuery.fancybox.close();
    });
        jQuery(".fancybox-inner .email_popup_div #btn_cancel").live("click",function(){
           jQuery(".fancybox-inner .email_popup_div").css("display","none");
           jQuery(".fancybox-inner .popupmainclass").css("display","block");
           jQuery(".fancybox-inner .mainloginclass").css("display","none");
           jQuery(".fancybox-inner .updateprofile").css("display","none");
                 jQuery(".fancybox-inner .forgotmainclass").css("display","none");
        });
        jQuery(".fancybox-inner #btnRequestPassword").live("click",function(){
           var forgot_path="<?php echo WEB_PATH;?>/validate_captcha.php";
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
				     jQuery(".fancybox-inner").css("height","320px");
				     jQuery(".fancybox-inner").css("overflow","hidden");
				     jQuery(".fancybox-inner").css("width","405px");
				     
				     
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
	jQuery("#btnsharegridbutton").live("click",function(){
                
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
			jQuery.ajax({
				type: "POST",
				url: "<?=WEB_PATH?>/process.php",
				data: "btn_share_grid=yes&reffer_campaign_id=" +c +'&refferal_location_id='+l+'&txt_share_frnd='+email_str+"&domain=4&medium="+jQuery("#medium").val()+'&timestamp='+formattedDate(),
				async : true,
				success: function(msg){
					//close_popuploader('Notificationloader');
					/*
					jQuery(".fancybox-inner .popupmainclass").css("display","block");
					jQuery(".fancybox-inner .email_popup_div").css("display","none");
					jQuery(".fancybox-inner .mainloginclass").css("display","none");
					*/
				}
			});
			jQuery(".fancybox-inner .popupmainclass").css("display","block");
			jQuery(".fancybox-inner .email_popup_div").css("display","none");
			jQuery(".fancybox-inner .mainloginclass").css("display","none");
			return true;
		}
		else 
		{
			jQuery(".fancybox-inner .email_popup_div #popup_error").text(msg);		 
			return false;
		}               
	});
	jQuery('.fancybox-inner #ShowVoucherId').live("click",function(){
		//alert("show voucher");
	    // $(".sharediv").show();
            var vid = jQuery(this).attr("vid");
      //      alert(vid);
      var arr =  vid.split("-");
      //if(parseInt(arr[3]) < 10)
	  //{
            $.ajax({
					  type: "POST",
					  url: "<?=WEB_PATH?>/process.php",
					  data: "show_voucher=yes&vid="+vid,
					  async : false,
					  success: function(msg) {
					   //alert(msg);
							
						  var obj = jQuery.parseJSON(msg);
						   if(obj.loginstatus == "false")
							 {
								parent.window.location.href=obj.link;
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
								 else
								 {
									 if($("#redeemvoucher_value").val()!=" ")
									 {
											redeemvoucher_val = $("#redeemvoucher_value").val();
									 }
										   redeemvoucher_val += arr[0]+":"+arr[1]+";";
										$("#redeemvoucher_value").val(redeemvoucher_val);
									 //alert("This offer is already redeemed");
									 jQuery(".fancybox-inner .errorclasspopup").html("<?php echo $client_msg['location_detail']['msg_Alreday_Redeemed']?>");
								 }

							 
						 }
					  }
					});
        //}    
	});
        
     jQuery('.fancybox-inner #ShowshareId').live("click",function(){
	    // $(".sharediv").show();
             jQuery(".fancybox-inner .sharediv").css("display","block");
	     jQuery(".fancybox-inner .showvocherdiv").css("display","none");
             jQuery(".fancybox-inner #ShowshareId").css("display","none");
             jQuery(".fancybox-inner #ShowVoucherId").css("display","block");
	});
		
	jQuery("#captcha_image").live("click",function(){
	   change_captcha();
	});
	function change_captcha()
	 {
		//document.getElementById('captcha_image_src').src="get_captcha_c_p.php?rnd=" + Math.random();
		jQuery(".fancybox-inner #captcha_image_src").attr("src","<?php echo WEB_PATH ?>/get_captcha_c_p.php?rnd=" + Math.random());
	 }
	function validate_register(){
        
         if(email_validation( jQuery(".fancybox-inner #email").val()) == false && jQuery(".fancybox-inner #mycaptcha_rpc").val() == "")
            {
                //alert("hi");
                jQuery(".fancybox-inner #emailerror").html("<?php echo $client_msg['login_register']['Msg_valid_email'];?>");
                jQuery(".fancybox-inner #captchaerror").html("<?php echo $client_msg['login_register']['Msg_Captcha_Not'];?>");
                document.getElementById("email").focus();
                error_var="false";
		return false;
            }
	else if(email_validation( jQuery(".fancybox-inner #email").val()) == false){
		//alert("Please Enter valid Email");
                jQuery(".fancybox-inner #emailerror").html("<?php echo $client_msg['login_register']['Msg_valid_email'];?>");
		jQuery(".fancybox-inner #email").focus();
                error_var="false";
		return false;
	}
        else if( jQuery(".fancybox-inner #mycaptcha_rpc").val() == ""){
		//alert("Please Enter Captcha");
                jQuery("fancybox-inner #captchaerror").html("<?php echo $client_msg['login_register']['Msg_Captcha_Not'];?>");
		jQuery(".fancybox-inner #mycaptcha_rpc").focus();
		error_var="false";
                return false;
	}
        else
        {
            jQuery(".fancybox-inner #captchaerror").html("");
            jQuery(".fancybox-inner #emailerror").html("");
            error_var="true";
            return true;
            
        }
	
	
}


jQuery(".btn_unreserve").live("click",function(){
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
jQuery("#btnconfirmunreserve").live("click",function(){
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
 jQuery.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnunreservedeal=true&campaign_id='+$(this).attr("u_campid")+'&l_id='+$(this).attr("u_locid")+'&customer_id=<?php echo $customer_id_p; ?>',
                  async:false,
		  success:function(msg)
		  {
				 var obj = jQuery.parseJSON(msg);
				  //alert(obj.status);
				  // 9-9-2013 for one per customer redeem deal can't unreserve
				  if(obj.status == "false")
				  {
					//alert("Sorry this offer is already redeemed.");
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
                          redirect_href = jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview .view").attr("href");
						//  alert(redirect_href);
							jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview").append('<a target="_parent" class="view-reserve" href="'+redirect_href+'" offer_left="5" redeem="0" reserve="0">View And Reserve</a>');
                         
                         //jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ele.attr("u_campid")+"'] .btnview").append('<a target="_parent" class="view-reserve" href="campaign.php?campaign_id='+ele.attr("u_campid")+'&l_id='+ele.attr("u_locid")+'" offer_left="5" redeem="0" reserve="0">View And Reserve</a>');
                          }
                         var url_d = jQuery("#dealslist .deal_blk[locid='"+ele.attr("u_locid")+"'][campid='"+ ele.attr("u_campid")+"'] .dealtitle").attr('mypopupid');
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
    jQuery("#btnconfirmcamcel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}
	jQuery(".fancybox-inner  #btn_goback_error").live("click",function(){
	           $(".fancybox-inner  .forgotmainclass").css("display","block");
		    jQuery(".fancybox-inner .email_popup_div").css("display","none");
		   jQuery(".fancybox-inner .popupmainclass").css("display","none");
                jQuery(".fancybox-inner .mainloginclass").css("display","none");
		   $(".fancybox-inner  .errormainclass").css("display","none");
	   });
    
    
    jQuery(".btn_print1").live('click',function(){
        var cid=jQuery(this).attr('cid');
         var lid=jQuery(this).attr('lid');
         var barcodea=jQuery(this).attr('barcodea');
          ele = jQuery(this);
		  jQuery("#Not_Set_Login_Frm_Sububscribe").html(jQuery("#notprofilesetid").html());
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
                    // alert(msg);
                     jQuery("#print_coupon_div").html(msg);
                    
                     jQuery("#coupon_div_"+cid+"_"+lid).css('display','block');
                    //jQuery("#print_coupon_div").printArea();
                    if (timeout) clearTimeout(timeout);
					timeout=setTimeout(function()
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
           //alert(e);
       }
      
    });
	
	jQuery(document).ready(function(){
	
		var aplyview=getCookie("view");
		//alert(aplyview);
		if(aplyview=="gridview")
		{
			/*
			jQuery(".offersexyellow").css('height','320px');
			jQuery(".deal_blk").css("margin","5px 9px 15px 0px");
			jQuery(".mer_ikon_img").css('display','none');
			jQuery(".percetage").css('margin-top','15px').css("width","165px");
			jQuery(".grid_img_div").css('display','block');
			*/
			jQuery("#dealslist").removeClass("mapview");
			jQuery("#dealslist").addClass("gridview");
			jQuery("#dealslist").css("display","block");
			
		}
		else
		{
			/*
			jQuery(".offersexyellow").css('height','202px');
			jQuery(".deal_blk").css("margin","18px 20px 0px 0px");
			jQuery(".mer_ikon_img").css('display','block');
			jQuery(".percetage").css('margin-top','0px').css("width","233px");
			jQuery(".grid_img_div").css('display','none');
			*/	
			jQuery("#dealslist").removeClass("gridview");
			jQuery("#dealslist").addClass("mapview");
			jQuery("#dealslist").css("display","block");
			
		}
	
	});
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
	
jQuery(".subscribestore").live("click",function(){
    var flag = true;
   var  locid = $(this).attr("s_lid");
    var  locid1 = $(this).attr("s_lid1");
	jQuery(".fancybox-inner #login_frm").html("");
	jQuery("#Not_Set_Login_Frm_Sububscribe").html(jQuery("#notprofilesetid").html());
   if(jQuery("#is_profileset").val() != 1){
 $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'is_userprofileset=true&custome_id=<?php if(isset($_SESSION['customer_id']))echo $_SESSION['customer_id']; ?>',
                  async:false,
		  success:function(msg)
		  {
		  
		  //return false;
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
				//  jQuery(".location_tool[locid='"+ locid1 +"']").find(".sub_loader").css("display","none");
                  }
     });
     if(! flag)
     {
         return false;
     }
     }
	 
    var ele = jQuery(this);
    var flag_login=true;
    $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnsharingbuttonloginornot=true&custome_id=<?php echo $customer_id_p; ?>',
                  async:false,
		  success:function(msg)
		  {
                       var obj = jQuery.parseJSON(msg);
                     
                      if(obj.status == "false")
                      {
                          parent.window.location.href= obj.link;
                          flag_login= false;
                      }
                  else{
                         flag_login= true;
                      //alert("You Profile IS not set. First set it");
                        }
                 }
     });
    //alert('btnRegisterStore=1&location_id='+$(this).attr("s_lid"));
    if(flag_login == true)
    {
	//alert(locid1 +"==="+ jQuery("#addressinfo_"+ locid1).find(".sub_loader").length);
	//alert(jQuery("#addressinfo_"+ locid1).find(".sub_loader").css("display"));
	jQuery("#addressinfo_"+ locid1).find(".sub_loader").css("display","block");
	//alert("hi");
	//alert(jQuery("#addressinfo_"+ locid1).find(".sub_loader").css("display"));
     $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
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
//                         jQuery("#temp_infowiondow").html(infowindowcontent[locid1]);
//                    var ele1 = jQuery("#temp_infowiondow .subscribestore");
//                    ele1.text("Unsubscribe");
//                    ele1.removeClass("subscribestore");
//                      ele1.addClass("unsubscribestore");
//      infowindowcontent[locid1] = jQuery("#temp_infowiondow").html();
                                  // alert(infowindowcontent[locid1])  ;
                     // alert(1);
					 //alert("hi");
					 // start 2-8-13
					 jQuery("#locationarea_"+locid1+" .deal_blk[level=2]").css("display","block");
					 // end 2-8-13
                     
					// 03 10 2013
					
						//alert(jQuery("#locationarea_"+locid1+" .deal_blk").length);
						var old_deal_count=jQuery("#locationarea_"+locid1+" .deal_blk").length;
						var new_deal_count="";
						jQuery("#locationarea_"+locid1).detach();
						
				jQuery("li#tab-type[data-hashval='offers']").find("a").trigger("click");						
				
						/* $.ajax({
								type:"POST",
								url:'<?php echo WEB_PATH; ?>/process.php',
								data :'btnGetActiveDealsCountOnLocationPage=yes&loc_id='+locid1,
								// async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									//alert(obj.status);
									//alert(obj.total_records);
									new_deal_count=obj.total_records;
									
									//alert(new_deal_count);
									//alert(old_deal_count);
									
									if(new_deal_count>old_deal_count)
									{
										window.location.reload(true);
									}
								}			  
							}); */
					}
					// 03 10 2013
                  }
   });
   jQuery("#addressinfo_"+ locid1).find(".sub_loader").css("display","none");
   }
 //  return false;
});
jQuery(".unsubscribestore").live("click",function(){

//jQuery("#image_loader_div").css("display","block");

var locid1 = $(this).attr("s_lid1");
    var ele = jQuery(this);
     var flag = true;
    
    $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'btnsharingbuttonloginornot=true&custome_id=<?php echo $customer_id_p; ?>',
                  async:false,
		  success:function(msg)
		  {
                       var obj = jQuery.parseJSON(msg);
                     
                      if(obj.status == "false")
                      {
                          parent.window.location.href= obj.link;
                          flag= false;
                      }
                  else{
                         flag= true;
                      //alert("You Profile IS not set. First set it");
                        }
                 }
     });
    if(flag == true)
        {
		//open_popup('Notificationloader');
		jQuery("#addressinfo_"+ locid1).find(".sub_loader").css("display","block");
           $.ajax({
		  type:"POST",
		  url:'<?php echo WEB_PATH; ?>/process.php',
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
//                             jQuery("#temp_infowiondow").html(infowindowcontent[locid1]);
//                    var ele1 = jQuery("#temp_infowiondow .unsubscribestore");
//                    ele1.text("Subscribe to store");
//                    ele1.removeClass("unsubscribestore");
//                      ele1.addClass("subscribestore");
//      infowindowcontent[locid1] = jQuery("#temp_infowiondow").html();        
                     // alert(1);
					 
					 // start 2-8-13
					 
					 jQuery("#locationarea_"+locid1+" .deal_blk[level=2]").each(function(){
					     var campid=jQuery(this).attr('campid');
						//alert("hi");
						//alert(jQuery(this).attr('locid'));
						//alert(jQuery(this).attr('campid'));
							
									//alert("deal is unreserved");
									jQuery("#locationarea_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btn_print1").css("display","none");
									//reserve_val += ele.attr("u_campid")+":"+ele.attr("u_locid")+";";
									jQuery("#locationarea_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .view").hide();
									jQuery("#locationarea_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .btn_unreserve").hide();
								    if(jQuery("#locationarea_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .view-reserve").length == 1)
									{
										jQuery("#locationarea_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview .view-reserve").show();
									}
									else
									{
										jQuery("#locationarea_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .btnview").append('<a target="_parent" class="view-reserve" href="campaign.php?campaign_id='+campid+'&l_id='+locid1+'" offer_left="5" redeem="0" reserve="0">View And Reserve</a>');
									}
									 var url_d = jQuery("#locationarea_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .dealtitle").attr('mypopupid');
                          
									isReserve = getParam( 'is_reserve' , url_d );
									oleft = getParam( 'o_left' , url_d );
									if(isReserve == 1)
									{
										oleft = parseInt(oleft) + 1;
									}
									var url_d = jQuery("#locationarea_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+campid+"'] .dealtitle").attr('mypopupid');
									url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "0");
									url_d = url_d.replace(/(is_mydeals=)[^\&]+/, '$1' + "0");
									url_d = url_d.replace(/(o_left=)[^\&]+/, '$1' + oleft);
									///   url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
									jQuery("#locationarea_"+locid1+" .deal_blk[locid='"+locid1+"'][campid='"+ campid+"'] .dealtitle").attr('mypopupid',url_d);
									
								
					 });
					 
					 jQuery("#locationarea_"+locid1+" .deal_blk[level=2]").css("display","none");
					 // end 2-8-13
					 
					 // start 5-8-2013
					 var location_count = 0;
					 jQuery("#locationarea_"+locid1+" .deal_blk").each(function(){
						if(jQuery(this).css("display") == "block")
						{
							location_count++;
						}
					 });
					// alert(location_count);
					if(location_count==0)
					{
					error_msg = "<div class='no_Records_Found' align='center'><?php echo $client_msg['location_detail']['table_no_campaigns_found']; ?></div>";
						jQuery("#locationarea_"+locid1).html(error_msg);
					}
					//	alert(jQuery("#locationarea_"+locid1+" .deal_blk:visible").size()+"visible deal block size");
						/* if(location_count<1)
						{
							// start 6-8-2013
						//	alert("in if");
							if(typeof jQuery("#locationarea_"+locid1).next().attr("id") === "undefined" )
							{
								//alert("in second if");
								//alert(jQuery("#locationarea_"+locid1).prev().attr("id"));
								var nearest_loc_id=jQuery("#locationarea_"+locid1).prev().attr("id");
								if(typeof jQuery("#locationarea_"+locid1).prev().attr("id") === "undefined" )
								{
								//alert("in third if");
									// only one location on location page
									window.location.href='<?= WEB_PATH ?>/search-deal.php';
								}
								else
								{
								//alert("In third else");
									nearest_loc_id=nearest_loc_id.split("_");
									nearest_loc_id=nearest_loc_id[1];
									for (var i=0; i<markersArray.length; i++) 
									{
										//alert(nearest_loc_id+"=="+markersArray[i]['locid']);
										//alert(markersArray[i]['__gm_id']);
										
										if(nearest_loc_id==markersArray[i]['locid'])
										{
											//markersArray[i].setIcon('<?=WEB_PATH?>/images/pin-small-blue.png');
											google.maps.event.trigger(markersArray[i], 'click');
										}
										else
										{
											//markersArray[i].setIcon('<?=WEB_PATH?>/images/pin-small.png');
										}
										if(markersArray[i]['locid']==locid1)
										{
											markersArray[i].setVisible(false);
										}
									}
								}
								
							}
							else
							{
								//alert("In second else");
								//alert(jQuery("#locationarea_"+locid1).next().attr("id"));
								var nearest_loc_id=jQuery("#locationarea_"+locid1).next().attr("id");
								nearest_loc_id=nearest_loc_id.split("_");
								nearest_loc_id=nearest_loc_id[1];
								for (var i=0; i<markersArray.length; i++) 
								{
									//alert(nearest_loc_id+"=="+markersArray[i]['locid']);
									//alert(markersArray[i]['__gm_id']);
									
									if(nearest_loc_id==markersArray[i]['locid'])
									{
										//markersArray[i].setIcon('<?=WEB_PATH?>/images/pin-small-blue.png');
										google.maps.event.trigger(markersArray[i], 'click');
									}
									else
									{
										//markersArray[i].setIcon('<?=WEB_PATH?>/images/pin-small.png');
									}
									if(markersArray[i]['locid']==locid1)
									{
										markersArray[i].setVisible(false);
									}
								}
							}
							// end 6-8-2013
						} */
						
					 // end 5-8-2013
					 
                     
					 
				//	jQuery("#image_loader_div").css("display","none"); 
				//close_popup('Notificationloader');
			jQuery("#addressinfo_"+ locid1).find(".sub_loader").css("display","none");
					 }
                     }
         
       
                 // }
   });
   }
});


function medaitor_loc(val)
{
     //alert("In mediator");
      myVar=setInterval(function(){myTimer()},30000);
      //getLocation_loc(val);
	 //alert("out mediator"); 
}
function myTimer()
{
    getLocation_loc(jQuery("#is_geo_location_supported").val());
}
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
		  url:'<?php echo WEB_PATH; ?>/process.php',
		  data :'set_not_supported_session_value=true',
                  async:false,
		  success:function(msg)
		  {
                  
                  }
              });         
    }

}
function getLocation_loc(val)
{
  if (navigator.geolocation)
  {
    //alert("In in");
	navigator.geolocation.getCurrentPosition(showPosition_loc , function(errorCode) {                  
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
  
function showPosition_loc(position)
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
window.onload=function() {
checkCookie();
initialize();
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
                jQuery(".fancybox-inner .geolocation_error").css("display","none");
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
	jQuery("#enter_zipcode_div").css("display","none");
	jQuery.ajax({
        type:"POST",
        url:'<?php echo WEB_PATH; ?>/process.php',
        data :'getlatitudelongitude_zipcode1=true&searched_location='+ jQuery("#enter_zip").val()+"&to_lat="+jQuery("#loc_lat").val()+"&to_lng="+jQuery("#loc_lng").val(),
        async:false,
        success:function(msg)
        {
			//alert(msg);
			//jQuery("#milesdiv").html(msg);
			setCookie("searched_location",jQuery("#enter_zip").val(),365);
			window.location.reload(false);
		}
	});
});

jQuery("body").on("click","#searchdeal_cancle",function(){
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
}
catch(e)
{
}
</script>

<!--[if IE]>
<style>
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
<script>
 try{
 jQuery(".my_loc_bckgnd_img").toggle(function(){
//alert("hi1");
var ar=jQuery(this).attr("loc_id");
ar=ar.split("_");
jQuery("#hdn_location_id").val(ar[0]);
var lat=jQuery(this).find(".hdnlatlong").attr("lat");
var longi=jQuery(this).find(".hdnlatlong").attr("long");
//alert(lat+"=="+longi);

var sw = new google.maps.LatLng(lat,longi);
var ne = new google.maps.LatLng(lat,longi);
var bounds = new google.maps.LatLngBounds(sw, ne);
var zoom = // do some magic to calculate the zoom level
map.setCenter(bounds.getCenter());
map.setZoom(map.getZoom()+4);

},function(){
//alert("hi2");
//map.setZoom(map.getZoom()-2);
initialize();
});

// 19 10 2013
jQuery(".my_loc_bckgnd_img1").toggle(function(){
//alert("hi1");
var ar=jQuery(this).attr("loc_id");
ar=ar.split("_");
jQuery("#hdn_location_id").val(ar[0]);
var lat=jQuery(this).find(".hdnlatlong").attr("lat");
var longi=jQuery(this).find(".hdnlatlong").attr("long");
//alert(lat+"=="+longi);

var sw = new google.maps.LatLng(lat,longi);
var ne = new google.maps.LatLng(lat,longi);
var bounds = new google.maps.LatLngBounds(sw, ne);
var zoom = // do some magic to calculate the zoom level
map.setCenter(bounds.getCenter());
map.setZoom(map.getZoom()+4);

},function(){
//alert("hi2");
//map.setZoom(map.getZoom()-2);
initialize();
});
// 19 10 2013

// tab js
jQuery("li#tab-type a").click(function() {


//window.history.replacestate({},'','');
   //alert("In clicking");
        //$('#ul_merchantwise_campaigns td:first a').trigger('click');
        jQuery("#sidemenu li a").each(function() {
            jQuery(this).removeClass("current");
        });
       jQuery(this).addClass("current");
        var cls = jQuery(this).parent().attr("class");
        jQuery(".tabs").each(function(){
            jQuery(this).css("display","none");
        });
		var v  = "";
                
               
				jQuery("div[id^='addressinfo']").each(function(){
				//alert("1");
					if($(this).css("display") == "block")
					{
					//alert("In Bloack");
						var v_arr = ($(this).attr("id")).split("_");
					v =  v_arr[1];
					}
				});
				var r_n = "";
				r_n = jQuery("."+cls).attr("data-hashval");
				//alert("1");
				try
				{
					if (typeof window.history.replaceState != 'undefined') { 
						window.history.replaceState( {} ,'foo',jQuery("#locationreview_"+v).attr("redirect_url")+"#"+r_n );
					}
				}
				catch(e)
				{
				}
				//alert("2");
                
				/*************** campaigns ************************/
					
		if(cls == "div_offers")
		{
				
				//alert("in");
				var v  = "";
				jQuery("div[id^='addressinfo']").each(function(){
				//alert("1");
					if($(this).css("display") == "block")
					{
					//alert("In Bloack");
						var v_arr = ($(this).attr("id")).split("_");
					v =  v_arr[1];
					}
				});
				
					if($("#locationarea_"+v).length == 0)
				{
					
				jQuery("#offer_tab_loader").show();
			//	open_popup('Notificationloader');
                   //alert('getcampaignlistoflocation=true&locid='+v+'&mlatitude=<?php echo $selected_location[0]->latitude; ?>&mlongitude=<?php echo $selected_location[0]->longitude; ?>&customer_id=0');
					jQuery.ajax({
					  type:"POST",
					  url:'<?php echo WEB_PATH; ?>/process.php',
					  //async:false,
					  data :'getcampaignlistoflocation=true&locid='+v+'&mlatitude=<?php echo $selected_location[0]->latitude; ?>&mlongitude=<?php echo $selected_location[0]->longitude; ?>&customer_id=<?php echo $cust_s ?>',
					  success:function(msg)
					  {
						 //  close_popup('Notificationloader');
                                                //$("#offer_tab_loader").css("display","none");
			//                      alert(msg);
			jQuery("#offer_tab_loader").hide();
			$("#div_offers #dealslist").append(msg);
			bind_deal_blk_hover();
	var p = $('#media-upload-header').position();
	var ptop = p.top;
	var pleft = p.left;

							$(window).scrollTop(ptop);
							if(jQuery("#first_time_loaded").val() == 0 )
							{
							jQuery("#first_time_loaded").val("1");
								/*************** check if its scan from mobile then check wheteher location have any deal to solve ************/
		 var cookies_value="<?php if(isset($_COOKIE['is_scanflip_scan_qrcode'])){echo $_COOKIE['is_scanflip_scan_qrcode'];}else{echo "";}?>";
	
	 if (cookies_value != "") {
	    
	    var deal_count=jQuery("#dealslist:visible #locationarea_<?php echo $decode_location_id?> .deal_blk").length;
		
	   if (deal_count == 0) {
	       jQuery.fancybox({
		   		   content:jQuery('#wrong_file_data').html(),
                   // href:"<?=WEB_PATH?>/process.php?getMapForLocation=yes&locationid="+locationid+"&campaignid="+campaignid,
                    width: 435,
                    height: 345,
                   
                    openEffect : 'elastic',
                    openSpeed  : 300,
                    closeEffect : 'elastic',
                    closeSpeed  : 300,
                    // topRatio: 0,

                    changeFade : 'fast',  
					 beforeShow:function(){
						jQuery(".fancybox-inner").addClass("Class_for_activation");
					},
					afterClose: function () {
			         
                     window.location.href = "<?=WEB_PATH?>/search-deal.php";
            		},					
                    helpers:  {
                            overlay: {
                            opacity: 0.3
                            } // overlay
                    }
           
		});
	   }
	   
	 }
	 
	 /***************  ************/
							}
						  }
					  }); 
						  $("div[id^='locationarea_']").each(function(){
							$(this).css("display","none");
						});
						$("#locationarea_"+v).css("display", "block" );
				}
				else{
				
						$("div[id^='locationarea_']").each(function(){
							$(this).css("display","none");
						});
						$("#locationarea_"+v).css("display", "block" );
					}
				
		}
		/************* campaigns *********************/
				
				
				
                if(cls == "div_menu")
                    {
                        
                        	//alert("in");
				var v  = "";
				jQuery("div[id^='addressinfo']").each(function(){
				//alert("1");
					if($(this).css("display") == "block")
					{
					//alert("In Bloack");
						var v_arr = ($(this).attr("id")).split("_");
					v =  v_arr[1];
					}
				});
				
			//	open_popup('Notificationloader');
                        
                        
                        if($("#menu_"+v).length == 0)
				{
                        	$(".ajaxloader").css("display","block");
					///alert("in if");
					 jQuery.ajax({
					  type:"POST",
					  url:'<?php echo WEB_PATH; ?>/process.php',
					  data :'get_location_menu=true&location_id='+v,
							 // async:true,
						  success:function(msg)
						  {
						
                                                
                                                        $(".ajaxloader").css("display","none");
                                                        //var responce_arr = msg.split("###");

                                                        //if(responce_arr[0] == "error")
                                                        //{
                                                            
                                                          //  $("#div_menu").html(responce_arr[1]);
                                                       // }
                                                        //else
                                                        //{

                                                          //      $("#div_menu").append(msg);
                                                        //}
			
                                                    $("#div_menu").append(msg);

			
			
							$("div[id^='menu_']").each(function(){
							$(this).css("display","none");
						});
						$("#menu_"+v).css("display", "block" );
							
							
							
							
						  }
					  }); 
                                  }
                                  else
                                  {
                                        $("div[id^='menu_']").each(function(){
							$(this).css("display","none");
						});
						$("#menu_"+v).css("display", "block" );
                                  }
						
				
				
                    }
		if(cls == "div_reviews")
		{
				
				//alert("in");
				var v  = "";
				jQuery("div[id^='addressinfo']").each(function(){
				//alert("1");
					if($(this).css("display") == "block")
					{
					//alert("In Bloack");
						var v_arr = ($(this).attr("id")).split("_");
					v =  v_arr[1];
					}
				});
				if($("#review_list_"+v).length == 0)
				{
			//	open_popup('Notificationloader');
                    //$(".ajaxloader").css("display","block");
						var rtable= '';
					if(!$('#review_table_by_loc').hasClass('dataTable')){
					rtable = $('#review_table_by_loc').dataTable({
                                        "bFilter": false,
                                        "sPaginationType": "two_button",
                                        "bProcessing": true,
					"oLanguage": {
						  "sProcessing": "Loading..."
					   }, 
                                        "bServerSide": true,
					"aaSorting": [],
                                        "iDisplayLength": 10,
                                        "sAjaxSource": "<?php echo WEB_PATH; ?>/process.php",
					"sDom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
                                        //"iDeferLoading": <?php //echo $total_records1;  ?>,
					"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
						  nRow.className = "tablereview";
						  return nRow;
						},
					"fnDrawCallback": function( aData) {
						if(aData._iRecordsDisplay <= 0){
							$('#div_reviews_empty').show();
							$('.review_location').hide();
						}else{
							$('#div_reviews_empty').hide();
							$('.review_location').show();
						}

					    },
                                        "fnServerParams": function (aoData) {
                                                aoData.push({"name": "get_location_reviews_show", "value": true}, {"name": 'location_id', "value": $('#hdn_location_id').val()},{"name":"order","value":$('#sort_by_review').val()+' '+$('#sort_by_review').data('order')});
                                        },
					"fnServerData": fnDataTablesPipeline,
                                        "aoColumns": [
                                                 { "bSortable": true ,"sClass": "td_review" },
						 { "bSortable": true , "bVisible":    false ,"sType": 'numeric',},
						 { "bSortable": true , "bVisible":    false ,"sType": 'numeric' },
						 { "bSortable": true , "bVisible":    false ,"sType": 'numeric' }
                                        ]
                });	
	}
		
		


					 /*jQuery.ajax({
					  type:"POST",
					  url:'<?php echo WEB_PATH; ?>/process.php',
					  async:false,
					  data :'get_location_reviews_grid=true&location_id='+v,
						  success:function(msg)
						  {
						//  close_popup('Notificationloader');
                                                $(".ajaxloader").css("display","none");
			//                      alert(msg);
			
			$("#div_reviews").append(msg);

			
			 $('#example_'+v).dataTable( {
                                'bFilter': false,
								"aaSorting": [],
							"sDom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
                                        "aoColumns": [	
                                            { "bSortable": false },
											{ "bSortable": false , "bVisible":    false ,"sType": 'numeric'},
											{ "bSortable": false , "bVisible":    false ,"sType": 'numeric' },
											{ "bSortable": false , "bVisible":    false ,"sType": 'numeric' }
										]
										} ); 
                            // 12 11 2013 display none prev next when one page only
							
						
							
							//alert(jQuery("a[id$='_previous']").attr("class"));
							//alert(jQuery("a[id$='_next']").attr("class"));
							var prev_class=jQuery("#review_list_"+v+" a[id$='_previous']").attr("class");
							var next_class=jQuery("#review_list_"+v+" a[id$='_next']").attr("class");
							//alert(prev_class);
							//alert(next_class);
							if(prev_class=="paginate_disabled_previous" && next_class=="paginate_disabled_next")
							{
								//alert("same");
								//jQuery(".paginate_disabled_previous").css("display","none");
								//jQuery(".paginate_disabled_next").css("display","none");
								
								jQuery("a[id='example_"+v+"_previous']").css("display","none");
								jQuery("a[id='example_"+v+"_next']").css("display","none");
								
								jQuery("#review_list_"+v+" .bottom a").css("display","none");
								
							}
							
							
							
							// 12 11 2013 display none prev next when one page only
							var p = $('#media-upload-header').position();
	var ptop = p.top;
	var pleft = p.left;

$(window).scrollTop(ptop);
						  }
					  }); */
						  $("div[id^='review_list_']").each(function(){
							$(this).css("display","none");
						});
						$("#review_list_"+v).css("display", "block" );
				}
				else{
				
						$("div[id^='review_list_']").each(function(){
							$(this).css("display","none");
						});
						$("#review_list_"+v).css("display", "block" );
					}
				
		}
                if(cls == "div_more_images")
		{
                        //alert("in");
				var v  = "";
				jQuery("div[id^='addressinfo']").each(function(){
				//alert("1");
					if($(this).css("display") == "block")
					{
					//alert("In Bloack");
						var v_arr = ($(this).attr("id")).split("_");
					v =  v_arr[1];
					}
				});
				if($("#moreimages_"+v).length == 0)
				{
                                    
                                    var latitude=jQuery("#addressinfo_"+v).find("input[type=hidden]").attr("lat");
                                    var longitude=jQuery("#addressinfo_"+v).find("input[type=hidden]").attr("long");
                        
                                   
			//	open_popup('Notificationloader');
                        	$(".ajaxloader").css("display","block");
					///alert("in if");
					 jQuery.ajax({
					  type:"POST",
					  url:'<?php echo WEB_PATH; ?>/process.php',
                                          data :'get_more_images=true&location_id='+v+'&latitude='+latitude+'&longitude='+longitude,
					  
							//  async:true,
						  success:function(msg)
						  {
						
                                                        $(".ajaxloader").css("display","none");
		
			
                                                        $("#div_more_images").append(msg);

                                                    }
					  });
                                           $("div[id^='moreimages_']").each(function(){
							$(this).css("display","none");
						});
						$("#moreimages_"+v).css("display", "block" );
						 
				}
				else{
				
					$("div[id^='moreimages_']").each(function(){
							$(this).css("display","none");
						});
						$("#moreimages_"+v).css("display", "block" );	
					}
                }
                
	
        jQuery('#'+cls).css("display","block");
    });
jQuery(".like").live("click",function(){

//$(".ajaxloader").css("display","block");
	var r_id = $(this).parent().parent().attr("urld");
	var parent_ele = $(this).parent();
	var counter_val = parseInt(parent_ele.find(".like_counter").text());
        var counter_val1 = parseInt(parent_ele.find(".unlike_counter").text());
		var loginstatus = false;
	jQuery.ajax({
                        type:"POST",
                        url:'<?php echo WEB_PATH; ?>/process.php',
                        async:false,
                        data :'btncheckloginornot_review=true&link='+window.location.href,
                                     async:false,
                        success:function(msg)
                        {
                                                var obj = jQuery.parseJSON(msg);
                                                 if(obj.status == "false")
                                                 {
                                                     setCookie("review_tab",1,365);
                                                              window.location.href= obj.link;
															  loginstatus = false;
                                                              return false;							
                                                 }
                                                 else
                                                 {
												 loginstatus = true;
                                                 }
												 

                       }
              });
//return false;
//alert('like_review=true&review_id='+r_id+'&customer_id=<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>');
if(loginstatus)
{
jQuery(".usefull[urld='"+r_id+"'] li").find(".sub_loader").css("display","block");
	jQuery.ajax({
					  type:"POST",
					  url:'<?php echo WEB_PATH; ?>/process.php',
					  data :'like_review=true&review_id='+r_id+'&customer_id=<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>',
						async:true,
						  success:function(msg)
						  {
						  var obj = jQuery.parseJSON(msg);
						  counter_val = counter_val+ obj.counter;
                                                  counter_val1 = counter_val1+ obj.counter1;
						  parent_ele.find(".like_counter").text(counter_val);
                          //       parent_ele.find(".unlike_counter").text(counter_val1);
                        //         $(".ajaxloader").css("display","none");
					jQuery(".usefull[urld='"+r_id+"'] li").find(".sub_loader").css("display","none");
							  }
						  }); 
						  }
});
jQuery(".unlike").live("click",function(){
  //$(".ajaxloader").css("display","block");
	open_popup('Notificationloader');
      
	var r_id = $(this).parent().parent().attr("urld");
	var parent_ele = $(this).parent();
	var counter_val = parseInt(parent_ele.find(".unlike_counter").text());
       var counter_val1 = parseInt(parent_ele.find(".like_counter").text());
        jQuery.ajax({
                        type:"POST",
                        url:'<?php echo WEB_PATH; ?>/process.php',
                        async:false,
                        data :'btncheckloginornot_review=true&link='+window.location.href,
                                       // async:false,
                        success:function(msg)
                        {
                                                var obj = jQuery.parseJSON(msg);
                                                //alert(obj.status);
                                                //alert(obj.link);

                                                 if(obj.status == "false")
                                                 {
                                                     	setCookie("review_tab",1,365);
                                                              window.location.href= obj.link;
                                                              return false;							
                                                 }
                                                 else
                                                 {
                                                 }

                       }
              }); 
			//alert('unlike_review=true&review_id='+r_id+'&customer_id=<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>');
	jQuery.ajax({
					  type:"POST",
					  url:'<?php echo WEB_PATH; ?>/process.php',
					  data :'unlike_review=true&review_id='+r_id+'&customer_id=<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>',
							  async:true,
						  success:function(msg)
						  {
							var obj = jQuery.parseJSON(msg);
							
						   counter_val = counter_val+ obj.counter;
                          counter_val1 = counter_val1+ obj.counter1;
						  parent_ele.find(".unlike_counter").text(counter_val);
                                                 // parent_ele.find(".like_counter").text(counter_val1);
						  close_popup('Notificationloader');
                        //                        $(".ajaxloader").css("display","none");
			//                      alert(msg);
			//$("#div_reviews").append(msg);
							  }
						  }); 
});

jQuery(".sort_data").live("click",function(){

//var ids = $(this).parent().parent().attr("id");
//var ids_arr = ids.split("_");
//var id= ids_arr[2];
//jQuery("#review_list_"+id+" .sort_data").css("color","#FF870F");
//jQuery("#review_list_"+id+" .sort_data_datetime").css("color","#333333");
//jQuery("#review_list_"+id+" .sort_data_helpfull").css("color","#333333");
//var oTable_new = $('#example_'+id).dataTable();
var rtable = $('#review_table_by_loc').dataTable();


jQuery(".review_location .sort_data").css("color","#FF870F");
jQuery(".review_location .sort_data_datetime").css("color","#333333");
jQuery(".review_location .sort_data_helpfull").css("color","#333333");
if($(this).attr("sort") == "desc")
{
$(this).attr("sort", "asc");
$('#sort_by_review').val($(this).data('name'));
$('#sort_by_review').data('order','asc');
//oTable_new.fnSort([  [1,'desc']] );
//rtable.fnSort([  [1,'desc']] );
rtable.fnDraw();

}
else{
$(this).attr("sort", "desc");
$('#sort_by_review').val($(this).data('name'));
$('#sort_by_review').data('order','desc');
//oTable_new.fnSort([  [1,'asc']] );
//rtable.fnSort([  [1,'asc']] );
rtable.fnDraw();

}  
});
jQuery(".sort_data_helpfull").live("click",function(){

//var ids = $(this).parent().parent().attr("id");
//var ids_arr = ids.split("_");
//var id= ids_arr[2];
//jQuery("#review_list_"+id+" .sort_data").css("color","#333333");
//jQuery("#review_list_"+id+" .sort_data_datetime").css("color","#333333");
//jQuery("#review_list_"+id+" .sort_data_helpfull").css("color","#FF870F");

jQuery(".review_location .sort_data").css("color","#333333");
jQuery(".review_location .sort_data_datetime").css("color","#333333");
jQuery(".review_location .sort_data_helpfull").css("color","#FF870F");
//var oTable_new = $('#example_'+id).dataTable();
var rtable = $('#review_table_by_loc').dataTable();
if($(this).attr("sort") == "desc")
{
$(this).attr("sort", "asc");
//oTable_new.fnSort([  [3,'desc']] );
$('#sort_by_review').val($(this).data('name'));
$('#sort_by_review').data('order','desc');
rtable.fnDraw();
}
else{
$(this).attr("sort", "desc");
//oTable_new.fnSort([  [3,"asc"]] );
$('#sort_by_review').val($(this).data('name'));
$('#sort_by_review').data('order','asc');
rtable.fnDraw();

} ;
  
});
jQuery(".sort_data_datetime").live("click",function(){

//var ids = $(this).parent().parent().attr("id");
//var ids_arr = ids.split("_");
//var id= ids_arr[2];
//jQuery("#review_list_"+id+" .sort_data").css("color","#333333");
//jQuery("#review_list_"+id+" .sort_data_datetime").css("color","#FF870F");
//jQuery("#review_list_"+id+" .sort_data_helpfull").css("color","#333333");
//var oTable_new = $('#example_'+id).dataTable();

jQuery(".review_location .sort_data").css("color","#333333");
jQuery(".review_location .sort_data_datetime").css("color","#FF870F");
jQuery(".review_location .sort_data_helpfull").css("color","#333333");

var rtable = $('#review_table_by_loc').dataTable();

if($(this).attr("sort") == "desc")
{
$(this).attr("sort", "asc");
//oTable_new.fnSort([  [2,'desc']] );
$('#sort_by_review').val($(this).data('name'));
$('#sort_by_review').data('order','desc');
rtable.fnDraw();
}
else{
$(this).attr("sort", "desc");
//oTable_new.fnSort([  [2,'asc']] );
$('#sort_by_review').val($(this).data('name'));
$('#sort_by_review').data('order','asc');
rtable.fnDraw();
} ;
  
});
jQuery(".readreview").live("click",function(){

//alert("In");
	jQuery("li#tab-type[class='div_reviews'] a").trigger("click");
	//jQuery("#div_reviews").scrollIntoview();
	
	var p = $('#div_reviews').position();
	var ptop = p.top;
	var pleft = p.left;
$(window).scrollTop(ptop);
});
jQuery(document).ready(function(){ 
    if(getCookie('review_tab')  == "1")
     {
         jQuery("li#tab-type[class='div_reviews'] a").trigger("click");
	//jQuery("#div_reviews").scrollIntoview();
	
	var p = $('#div_reviews').position();
	var ptop = p.top;
	var pleft = p.left;
$(window).scrollTop(ptop);
			   var st = "";
    var err_msg = "";
                //window.location.hash = '';
	
     }
     setCookie("review_tab","",365);
     del_cookie('review_tab');
//var  hashval =  getParam( 're' , decodeURIComponent(window.location )).replace(/\+/g," ");
  
});

var oCache = {
	iCacheLower: -1
};

function fnSetKey( aoData, sKey, mValue )
{
	for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
	{
		if ( aoData[i].name == sKey )
		{
			aoData[i].value = mValue;
		}
	}
}

function fnGetKey( aoData, sKey )
{
	for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
	{
		if ( aoData[i].name == sKey )
		{
			return aoData[i].value;
		}
	}
	return null;
}

function fnDataTablesPipeline ( sSource, aoData, fnCallback ) {
	var iPipe = 2; /* Ajust the pipe size */
	
	var bNeedServer = false;
	var sEcho = fnGetKey(aoData, "sEcho");
	var iRequestStart = fnGetKey(aoData, "iDisplayStart");
	var iRequestLength = fnGetKey(aoData, "iDisplayLength");
	var iRequestEnd = iRequestStart + iRequestLength;
	oCache.iDisplayStart = iRequestStart;
	
	/* outside pipeline? */
	if ( oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper )
	{
		bNeedServer = true;
	}
	
	/* sorting etc changed? */
	if ( oCache.lastRequest && !bNeedServer )
	{
		for( var i=0, iLen=aoData.length ; i<iLen ; i++ )
		{
			if ( aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho" )
			{
				if ( aoData[i].value != oCache.lastRequest[i].value )
				{
					bNeedServer = true;
					break;
				}
			}
		}
	}
	
	/* Store the request for checking next time around */
	oCache.lastRequest = aoData.slice();
	
	if ( bNeedServer )
	{
		if ( iRequestStart < oCache.iCacheLower )
		{
			iRequestStart = iRequestStart - (iRequestLength*(iPipe-1));
			if ( iRequestStart < 0 )
			{
				iRequestStart = 0;
			}
		}
		
		oCache.iCacheLower = iRequestStart;
		oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
		oCache.iDisplayLength = fnGetKey( aoData, "iDisplayLength" );
		fnSetKey( aoData, "iDisplayStart", iRequestStart );
		fnSetKey( aoData, "iDisplayLength", iRequestLength*iPipe );
		
		$.getJSON( sSource, aoData, function (json) { 
			/* Callback processing */
			oCache.lastJson = jQuery.extend(true, {}, json);
			
			if ( oCache.iCacheLower != oCache.iDisplayStart )
			{
				json.aaData.splice( 0, oCache.iDisplayStart-oCache.iCacheLower );
			}
			json.aaData.splice( oCache.iDisplayLength, json.aaData.length );
			
			fnCallback(json)
		} );
	}
	else
	{
		json = jQuery.extend(true, {}, oCache.lastJson);
		json.sEcho = sEcho; /* Update the echo for each response */
		json.aaData.splice( 0, iRequestStart-oCache.iCacheLower );
		json.aaData.splice( iRequestLength, json.aaData.length );
		fnCallback(json);
		return;
	}
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
  
function del_cookie(name)
{
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
/* rating detail */
jQuery('.rating_detail').click(function(){
    
jQuery.fancybox({
				href: 'rating_trend_chart.php',
				//href: $(val).attr('mypopupid'),
				//content:#myDivID_3,
				width: 400,
				height: 320,
				type: 'iframe',
				openEffect : 'elastic',
				openSpeed  : 300,
                                scrolling : 'no',
				closeEffect : 'elastic',
				closeSpeed  : 300,
                                beforeShow:function(){
                                    //alert(jQuery("#activation_code").val());
                                     setCookie("code",jQuery("#activation_code").val(),365);
                                        $(".fancybox-inner").addClass("Class_fancy_ie_login");
                                },
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
                                // helpers
			}); 
                        });
/* rating detail */


jQuery("a[id^='review_more_']").live("click",
    function(){
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
}
catch(e)
{
}
</script>

<script src="<?php echo ASSETS_JS ?>/c/highcharts.js"></script>
<script src="<?php echo ASSETS_JS ?>/c/exporting.js"></script>


<script>
try{
 jQuery(function () {
    // var all_rating ;
    draw_chart("<?php echo $decode_location_id ?>");
    var facebook_page_id="<?php echo $selected_location[0]->facebook_pageid;?>";
    
    if(facebook_page_id == "")
        {
            jQuery('.facebookmaindatadiv').hide();
            
            
        }
        else
        {
            //jQuery('.facebookmaindatadiv').show();
            
        }
    });
    
 function draw_chart(location_id)
 {
  
  if(jQuery('#container_'+location_id).html() ==  "")
  {
      var obj = "";
      jQuery(".facebookmaindatadiv").hide();
      jQuery.ajax({
                        type:"POST",
                        url:'<?php echo WEB_PATH; ?>/rating_trend_chart.php',
                        async:true,
                        data :'locid='+location_id,
                                       //async:false,
                        success:function(msg)
                        {

                            obj = jQuery.parseJSON(msg);
                        //    alert(obj.rating_info);
                            all_rating = obj.rating_info;
                                                //alert(obj.status);
                                                st_yr = obj.start_year;
                                                st_mnt = obj.start_month;
						visitor_detail = obj.visitor_detail;
                                                ratings = obj.rating_values;
                                                max_rating = obj.max_rating;
                                                max_rating_heading = obj.max_rating_heading;
                                                
                      
			  //alert(visitor_detail+"=="+visitor_detail.length);
			  /*div_container excellent verygood good fair poor */
                          jQuery(".rating_hedings").each(function() {
                             jQuery(this).css("display","none");
                          });
                          var reviews = jQuery("#locationreview_"+location_id+" .readreview").attr("rev");
                           if(max_rating ==0){
                               jQuery("#locationreview_"+location_id + " .rating_heading_top").detach();
                               jQuery("#visitorrating_"+location_id ).detach();
                               jQuery("#container_"+location_id).detach();
                               
                           }
                           
                          if(jQuery("#location_rating_heading_"+location_id).length == 0)
                          {
                             if(max_rating !=0){
                                  
                              if(max_rating_heading == "Excellent" || max_rating_heading == "Good" || max_rating_heading == "Very Good")
                              {
                                 jQuery(".merc_name").after('<div class="rating_hedings" id="location_rating_heading_'+location_id+'" > <span > '+max_rating_heading+'! '+max_rating+'% </span> of visitors recommended. ( Based on '+reviews+' Review. )</div>');
                              }
                              else{
                                  jQuery(".merc_name").after('<div class="rating_hedings" id="location_rating_heading_'+location_id+'" > </div>');
                              }
                              }
                          }
                          else{
                              jQuery('location_rating_heading_'+location_id).css("display","block");
                          }
                          
              jQuery("#visitorrating_"+location_id+" .excellent").parent().next().text(ratings[4]);
              jQuery("#visitorrating_"+location_id+" .verygood").parent().next().text(ratings[3]);
              jQuery("#visitorrating_"+location_id+" .good").parent().next().text(ratings[2]);
              jQuery("#visitorrating_"+location_id+" .fair").parent().next().text(ratings[1]);
              jQuery("#visitorrating_"+location_id+" .poor").parent().next().text(ratings[0]);
			
                jQuery("#visitorrating_"+location_id+" .excellent").animate({
				width:visitor_detail[4]+"%" }, 500, function() {
				});
				jQuery("#visitorrating_"+location_id+" .verygood").animate({
				width:visitor_detail[3]+"%" }, 500, function() {
				});
				jQuery("#visitorrating_"+location_id+" .good").animate({
				width:visitor_detail[2]+"%" }, 500, function() {
				});
				jQuery("#visitorrating_"+location_id+" .fair").animate({
				width:visitor_detail[1]+"%" }, 500, function() {
				});
				jQuery("#visitorrating_"+location_id+" .poor").animate({
				width:visitor_detail[0]+"%" }, 500, function() {
				});
				
			  for(i=0;i<5;i++)
			  {
				//alert(visitor_detail[i]);
			  }
			  //alert(all_rating);
              var test = "["+all_rating+"]";
parsedTest = JSON.parse(test);
           //   alert("=="+all_rating+"==");
             // all_rating = "0,0,0,0,0,0,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,4,4,4,4,4,4,4,4,3,4";
            //  var spl_arr = new Array();
            //  spl_arr = all_rating.split(",");
            //  alert(spl_arr);

					   if(max_rating !=0)
					   {
							  jQuery("#locationreview_"+location_id + " .rating_heading_top").css("display","block");
											   
							  jQuery('#visitorrating_'+location_id).css("display","block");
							  jQuery('#container_'+location_id).css("display","block");
							  
							  if(all_rating!="") // to solve blank rating trend chart problem
							  {
								 jQuery('#container_'+location_id).highcharts({
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
							  else
							  {
									jQuery('#container_'+location_id).html("<img class='notenoughdata1' src='<?php echo ASSETS_IMG ?>/c/not_enough_data.png'/>");	
							  }
									
							 
						}
				}
            });
	}
	else 
	{
		 jQuery(".rating_hedings").each(function() {
				 jQuery(this).css("display","none");
			  });
		jQuery('#location_rating_heading_'+location_id).css("display","block");
	}
 }
 
function facebook_data_function(location_id)
{
  /*
   jQuery.ajax({
		type:"POST",
		url:'<?php echo WEB_PATH; ?>/facebook_page_data.php',
		
		data :'locid='+location_id,
					   // async:false,
		success:function(msg)
		{
			//alert(msg);
			if(jQuery.trim(msg) == "No Data")
				{
					jQuery(".facebookmaindatadiv").hide();
				}
				else
					{
						
						//var success_arr = jQuery.trim(msg).split("###");
					   
						jQuery(".facebookmaindatadiv").html(msg);
						//jQuery(".facebookmaindatadiv").show();
						
						//jQuery(".facebook_heading_half_left").html(success_arr[0]+" likes.");
						//jQuery(".facebook_heading_seperator").html(success_arr[1]+" talking about this.");
						//jQuery(".facebook_heading_half_right").html(success_arr[2]+" were here.");
						//alert(success_arr[0]+" and "+success_arr[1]+" and "+success_arr[2])
					}
		}
   });
   */
}
    }
catch(e)
{
}
    </script>
<script type="text/javascript">
                try{
               var list = jQuery("#list");
                var li = list.children();
                var lengthMinusOne = li.length - 1;
                var index = 0;
                var num = jQuery("#list li").length;
                var prevLi=jQuery(li[0]).addClass('current_more');
                
                    
                 jQuery("li[id^='thumb_']").live("hover",function(){
                   
                    
                }, function() {
                    
                
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
                    index=li_index;
                    //prevLi = jQuery(li[li_index]).addClass('current_more');
                    jQuery(this).addClass("current_more");
                    //jQuery(this).removeClass("current_more");
                    
                });
                
                    //jQuery("a[id^='next_more_']").click(function(){
                    //live("click",function(){
                    jQuery("a[id^='next_more_']").live("click",function(){
                    
                     
                    
                    
                var location_id=jQuery(this).attr('id').split('_');
                
                var lengthMinusOne = jQuery("#list_"+location_id[2]+" li").length - 1;
                    //var $next, $selected = jQuery(".client_more");

                    //$selected.removeClass("client_more");
                    // $next = $selected.domNext().addClass("client_more");
                   
                    index++;
                   
                   
                    if (index > lengthMinusOne)
                        {
                            index = 0;
                        }
                   
                    jQuery("#list_"+location_id[2]+" li" ).removeClass('current_more');
                    jQuery("#list_"+location_id[2]+" li:eq("+index+")").addClass('current_more');
                    //var image_id=jQuery("#list_"+location_id[2]+" li" ).find('li .current_more').length;
                    var image_id=jQuery("#list_"+location_id[2]+" li:eq("+index+")").attr('id');
                    var location_image_name=image_id.split('_');
                    
                   //alert(image_id);
                    jQuery(".main_image_class_"+location_id[2]).hide();
                    jQuery("#main_"+location_image_name[1]+"_"+location_image_name[2]+"_"+location_image_name[3]).show();
                    
                    //jQuery('.thumbimageclass').removeClass('current_more').next('li').addClass('current_more');
                    
                     
                });
                
               // jQuery("#previous_more").click(function(){
                  jQuery("a[id^='previous_more_']").live("click",function(){  
               var location_id=jQuery(this).attr('id').split('_');
                var lengthMinusOne = jQuery("#list_"+location_id[2]+" li").length - 1;
                
                    index--;
                    if (index < 0)
                        {
                            index = lengthMinusOne;
                        }
                    jQuery("#list_"+location_id[2]+" li" ).removeClass('current_more');
                    jQuery("#list_"+location_id[2]+" li:eq("+index+")").addClass('current_more');
                    //var image_id=jQuery("#list_"+location_id[2]+" li" ).find('li .current_more').length;
                    var image_id=jQuery("#list_"+location_id[2]+" li:eq("+index+")").attr('id');
                    var location_image_name=image_id.split('_');
                   jQuery(".main_image_class_"+location_id[2]).hide();
                   jQuery("#main_"+location_image_name[1]+"_"+location_image_name[2]+"_"+location_image_name[3]).show();
                });
 /** problem with review section ***/
  
  
jQuery(".review1").live("click",function(){
var revid = jQuery(this).parent().parent().prev().attr("revid111");
jQuery("#hdn_resson_review").val(revid);
    jQuery.ajax({
                        type:"POST",
                        url:'<?php echo WEB_PATH; ?>/process.php',
                        async:false,
                        data :'btncheckloginornot_review=true&link='+window.location.href,
                                       // async:false,
                        success:function(msg)
                        {
                                                var obj = jQuery.parseJSON(msg);
                                                 if(obj.status == "false")
                                                 {
                                                     setCookie("review_tab",1,365);
                                                              window.location.href= obj.link;
                                                              return false;							
                                                 }
                                                 else
                                                 {
                                                      jQuery.fancybox({
		   		   content:jQuery('#overlayInnerDiv_review1').html(),
                   // href:"<?=WEB_PATH?>/process.php?getMapForLocation=yes&locationid="+locationid+"&campaignid="+campaignid,
                    width: 435,
                    height: 345,
                    hideOnContentClick: false,
					hideOnOverlayClick:false,
					enableEscapeButton : false ,
                    openEffect : 'elastic',
                    openSpeed  : 300,
                    closeEffect : 'elastic',
                    closeSpeed  : 300,
                    // topRatio: 0,
					afterShow:function(){
					$("#fancybox-overlay").unbind();
					},
                    changeFade : 'fast',  
                    beforeShow:function(){
                           jQuery(".fancybox-inner").addClass("Class_for_activation");
                   },
                   afterClose: function () {
			         
            		},					
                    keys:{
                close:null
            },
															
			helpers:  {
							overlay: {
							opacity: 0.3,
							closeClick:false
							} // overlay
			}
           
		}); 
                                                 }

                       }
              });
   
});
jQuery(".review_problem2").live("click",function(){
//alert("dshfghdsfg ");
var revid = jQuery(this).parent().parent().prev().attr("revid111");
jQuery("#hdn_resson_review").val(revid);
jQuery.ajax({
                        type:"POST",
                        url:'<?php echo WEB_PATH; ?>/process.php',
                        async:false,
                        data :'btncheckloginornot_review=true&link='+window.location.href,
                        async:false,
                        success:function(msg)
                        {
						                         var obj = jQuery.parseJSON(msg);
                                                 if(obj.status == "false")
                                                 {
                                                     setCookie("review_tab",1,365);
                                                              window.location.href= obj.link;
                                                              return false;							
                                                 }
                                                 else
                                                 {
                                                            jQuery.fancybox({
                                                                               content:jQuery('#overlayInnerDiv_review2').html(),
                                                                                    width: 435,
                                                                                    openEffect : 'elastic',
                                                                                    openSpeed  : 300,
                                                                                    closeEffect : 'elastic',
                                                                                    closeSpeed  : 300,
                                                                                    // topRatio: 0,
                                                                                    hideOnContentClick: false,
																					hideOnOverlayClick:false,
																				enableEscapeButton : false ,
                                                                                    changeFade : 'fast',  
                                                                                    beforeShow:function(){
                                                                                               jQuery(".fancybox-inner").addClass("Class_for_activation");
                                                                               },
                                                                               afterClose: function () {

                                                                                    },
																					afterShow:function(){
																					$("#fancybox-overlay").unbind();
																					},
																					keys:{
                close:null
            },
															
                                                                                    helpers:  {
                                                                                                    overlay: {
                                                                                                    opacity: 0.3,
																									closeClick:false
                                                                                                    } // overlay
                                                                                    }

                                                            }); 
                                                   
                                                 }

                       }
              }); 
			   
     
});
jQuery(".fancybox-inner #reasonSelect").live("change",function(){
   
   jQuery(".fancybox-inner .reasons").each(function(){
		jQuery(this).css("display","none");
	});
	if(jQuery(this).val() != -1)
	{
	jQuery(".fancybox-inner").height("auto");
		jQuery(".fancybox-inner #reason"+jQuery(this).val()+"msg").css("display","block");
		jQuery(".fancybox-inner #reasonExplanation").css("display", "block");
		jQuery(".fancybox-inner #iapSubmitButton").css("display", "block");
	}
	else{
		//jQuery(".fancybox-inner #reason"+jQuery(this).val()+"msg").css("display","none");
		jQuery(".fancybox-inner #reasonExplanation").css("display", "none");
		jQuery(".fancybox-inner #iapSubmitButton").css("display", "none");
	}
});
jQuery(".fancybox-inner #btn_review_suspicioups").live("click", function(){
a = (jQuery(".fancybox-inner #reasonExplanation textarea.comment").val().trim()).replace(/\n/g, "<br>");
a = a.trim();
//alert(a.length);
if(a.length != 0)
{

jQuery.ajax({
                        type:"POST",
                        url:'<?php echo WEB_PATH; ?>/process.php',
                        async:false,
                        data :'btncheckloginornot_review=true&link='+window.location.href,
                        async:false,
                        success:function(msg)
                        {
						                         var obj = jQuery.parseJSON(msg);
                                                 if(obj.status == "false")
                                                 {
                                                     setCookie("review_tab",1,365);
                                                              window.location.href= obj.link;
                                                              return false;							
                                                 }
                                                 else
                                                 {
												
													review_id = jQuery("#hdn_resson_review").val();
													// alert('mail_review_suspicious.php?review_id='+review_id+'&comment='+a+'&reason='+jQuery(".fancybox-inner #reasonSelect").val());
													jQuery.ajax({
																			type:"POST",
																			url:'<?php echo WEB_PATH; ?>/mail_review_suspicious.php',
																			async:false,
																			data :'review_id='+review_id+'&comment='+a+'&reason='+jQuery(".fancybox-inner #reasonSelect").val(),
																			async:false,
																			success:function(msg)
																			{
																				//alert(msg);
																				jQuery.fancybox.close(); 
																			}
																});
												}
									}
              }); 
			  }
			  else{
				//alert("Please enter reason why this review is suspicious");
				jQuery(".fancybox-inner .review_error").html("Please enter reason why this review is suspicious");
			  }
});

jQuery(".fancybox-inner #btn_review_violates").live("click", function(){
a = (jQuery(".fancybox-inner textarea.comment").val().trim()).replace(/\n/g, "<br>");
a = a.trim();

if(a.length != 0)
{
jQuery.ajax({
                        type:"POST",
                        url:'<?php echo WEB_PATH; ?>/process.php',
                        async:false,
                        data :'btncheckloginornot_review=true&link='+window.location.href,
                        async:false,
                        success:function(msg)
                        {
						                         var obj = jQuery.parseJSON(msg);
                                                 if(obj.status == "false")
                                                 {
                                                     setCookie("review_tab",1,365);
                                                              window.location.href= obj.link;
                                                              return false;							
                                                 }
                                                 else
                                                 {
review_id = jQuery("#hdn_resson_review").val();

jQuery.ajax({
                        type:"POST",
                        url:'<?php echo WEB_PATH; ?>/mail_review_voilates.php',
                        async:false,
                        data :'review_id='+review_id+'&comment='+a,
                        async:false,
                        success:function(msg)
                        {
							//alert(msg);
							jQuery.fancybox.close(); 
						}
			});
												}
									}
              }); 
			  }else
			  {
			   				//alert("Please enter reason why this review is suspicious");
							jQuery(".fancybox-inner .review_error").html("Please enter reason why this review is suspicious");
			  }
});
function limits(obj, limit){

    var text = $(obj).val(); 
    var length = text.length;
    if(length > limit){
       $(obj).val(text.substr(0,limit));
     } else { // alert the user of the remaining char. I do alert here, but you can do any other thing you like
      //alert(limit -length+ " characters remaining!");
	  jQuery(".fancybox-inner .character_counter").text(limit -length+ " characters remaining!");
     }
 }


jQuery('.fancybox-inner textarea.comment').live("keyup",function(){

    limits($(this), <?php echo $client_msg['location_detail']['label_violates_total_no_of_characters'];?>);
});
	jQuery(document).on('mouseenter',".ratinn_box",function() {
	var p = jQuery(this).position();
	//alert(p.top+"====="+p.left);
	
	//l = p.left -18;
	//t = p.top - 35;
	
	l = p.left -18+50;
	t = p.top - 35+37;
	
	var msg = jQuery(this).attr("title");
	//jQuery(this).next().css("left",l);
	//jQuery(this).next().css("top",t);
	 jQuery(this).next().css("display","block");
	 
})
jQuery(document).on('mouseleave',".ratinn_box",function() {
    jQuery(this).next().css("display","none");
});
jQuery(".problem_left").live("click",function(){

       //jQuery(this).next().toggle();
      //alert("hi"); 
	  //alert(jQuery(this).next().attr("disp"));
     if(jQuery(this).next().attr("disp")==0)
	{
		// display none other when open 
		jQuery('.problem_div').attr("disp","0");
		jQuery('.problem_div').css("display","none");    
		// display none other when open 
		
		jQuery(this).next().attr("disp","1");
		jQuery(this).next().show();
        //jQuery('.problem_div').attr("disp","1");
		//alert("open");		
	}
	else
	{
		jQuery(this).next().attr("disp","0");
		jQuery(this).next().hide();
        //jQuery('.problem_div').attr("disp","0");
		//alert("close");		
	}
        //jQuery(this).next().slideToggle( "slow" );
	//jQuery(this).next().css("display","block");
});
jQuery("body").click
(

  function(e)
  {
	//if((e.target.className).trim() == "actiontd")
    if((e.target.className) == "review1" || (e.target.className) == "review_problem2")
    {
    
    }
    else
	{
		//jQuery('.problem_div').css("display","none");
		//jQuery('.problem_div').attr("disp","0");
	}
  }
);
/** problem with review section ***/ 

jQuery(".fancybox-inner .btn_share_facebook").live("click",function(){
   //alert(this.id);
   //alert(jQuery(this).attr('data'));
   //open_popuploader('Notificationloader');
    jQuery(".share_loader").css("display","block");
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
        var redirect_url="<?php echo  $selected_location[0]->location_permalink; ?>";
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
    
    

        
        
        
        var cust_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>";
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

jQuery(".fancybox-inner .tokenyes").live("click",function(){
    
    
     var data_array=jQuery(this).attr('data').split('###');
     
   var campaign_title=unescape(decodeURIComponent("Scanflip Offer - "+data_array[0]));
        
	var summary=unescape(decodeURIComponent(data_array[1]));
	
        var redirect_url="<?php echo  $selected_location[0]->location_permalink; ?>";
	var th_link=decodeURIComponent(data_array[2]);

       
    
    var imgsrc=data_array[3];
   
    var facebook_user_id="<?php if(isset($RS_user_data['facebook_user_id'])){echo $RS_user_data['facebook_user_id'];} ?>";
        
    
    var campaign_id=data_array[5];
    
    var l_id=data_array[6];
   
    var campaign_tag=data_array[7];;
   
   var cust_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>";
  
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

jQuery(".fancybox-inner .facebooktokenyes").live("click",function(){
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
   var campaign_title=data_array[0];
        //alert(campaign_title);
	var summary=data_array[1];
        //alert(summary); 
	//var redirect_url="<?php echo WEB_PATH."/search-deal.php";?>";
        var redirect_url="<?php echo $Row->location_permalink;?>";
	var th_link=data_array[2];
        //alert(th_link);
    var imgsrc=data_array[3];
    //alert(imgsrc);
    var facebook_user_id="<?php if(isset($RS_user_data->fields['facebook_user_id'])){echo $RS_user_data->fields['facebook_user_id'];} ?>";
        
    
    var campaign_id=data_array[5];
    //alert(campaign_id);
    var l_id=data_array[6];
    //alert(l_id);
    var campaign_tag="<?php if(isset($tag_main)){echo $tag_main;}?>";
    
   var cust_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];}?>";
   
    var bussiness_name= data_array[8];
    //alert(bussiness_name);
    var location_address=data_array[9];
    //alert(location_address);
    var sharepoint=data_array[10];
    //alert(sharepoint);
           urlpath='http://www.facebook.com/sharer.php?s=100&p[title]=Scanflip Offer - '+campaign_title+'&p[summary]='+summary+'&p[url]='+encodeURIComponent(th_link)+'&p[images][0]='+imgsrc+'&p[message]=Hello How are u?';
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

jQuery(".fancybox-inner .twitter_link").live("click",function(){
	
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
    var redirect_url="<?php echo  $selected_location[0]->location_permalink; ?>";
    
    var cust_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>";
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
jQuery(".fancybox-inner .twittertokenyes").live("click",function(){
    
     var data_array=jQuery(this).attr('data').split('###');
     //var data_array=jQuery(this).attr('data');
     var campaign_title=unescape(decodeURIComponent(data_array[0]));
       var th_link=data_array[1];
        var cust_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];}?>";
        var campaign_id=data_array[3];
        var l_id=data_array[4];
        var campaign_tag=data_array[5];
        
        
        var redirect_url="<?php echo  $selected_location[0]->location_permalink; ?>";
	jQuery.ajax({
                                                            type:"POST",
                                                            url:'<?php echo WEB_PATH;?>/twitteroauth/twitter_login.php',
                                                            data :'campaign_title='+encodeURIComponent(campaign_title)+'&th_link='+th_link+'&customer_id='+cust_id+'&campaign_id='+campaign_id+'&l_id='+l_id+'&campaign_tag='+campaign_tag+'&redirect_url='+redirect_url,
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
   var campaign_title=data_array[0];
        alert(campaign_title);
	var summary=data_array[1];
        //alert(summary); 
	var redirect_url="<?php echo $Row->location_permalink;?>";
	var th_link=data_array[2];
        
      //  alert(th_link);
    var imgsrc=data_array[3];
    //alert(imgsrc);
    var facebook_user_id="<?php if(isset($RS_user_data->fields['facebook_user_id'])){echo $RS_user_data->fields['facebook_user_id'];} ?>";
        
    
    var campaign_id=data_array[5];
    //alert(campaign_id);
    var l_id=data_array[6];
    //alert(l_id);
    var campaign_tag="<?php if(isset($tag_main)){echo $tag_main;}?>";
    
   var cust_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];}?>";
   
    var bussiness_name= data_array[8];
    //alert(bussiness_name);
    var location_address=data_array[9];
    //alert(location_address);
    var sharepoint=data_array[10];
    //alert(sharepoint);
        
    var webpath="<?php echo WEB_PATH; ?>";
          urlpath="https://twitter.com/intent/tweet?original_referer="+webpath+"&text="+campaign_title+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link);
	//urlpath="https://twitter.com/intent/tweet?original_referer="+webpath+"&text="+campaign_title+' '+th_link+' '+hastag+"&tw_p=tweetbutton&url="+encodeURIComponent(th_link);					 
    
    
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
                          var urlpath="https://plus.google.com/share?url="+encodeURIComponent(url)+"&content=hi";
                         
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

jQuery(".fancybox-inner #sharignpopup").live("click",function(){
        jQuery.fancybox.close();
});

jQuery(".fancybox-inner #popupcancel").live("click",function(){
        jQuery.fancybox.close();
});
jQuery(".fancybox-inner .tokenno").live("click",function(){
        jQuery.fancybox.close();
});
jQuery(".fancybox-inner .twittertokenno").live("click",function(){
        jQuery.fancybox.close();
});
//jQuery("#-locu-widget-widget-instance").hide();

   jQuery(".locumanudetail").click(function(){




                                                                            jQuery.fancybox({
                                                                                                    content:jQuery('.locumaindiv').html(),

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

});
function myFunction(id)
     {
         $("div[id^='smbody']").each(function(){
							jQuery(this).css("display","none");
                                                        
						});
         $(".mainmanuTitle").each(function(){
             jQuery(this).addClass("inactive");
         });                                       
         $("#"+id).removeClass("inactive");
         jQuery("#smbody"+id).show();
         
         
         
     }
	 
call_pricerange_popup();

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
}
catch(e)
{
	alert(e);
}
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
<script type='text/javascript'>
try{
function checkCookie()
{
    
                //alert("cc");
    if(typeof getCookie('mycurrent_lati') == "undefined" || getCookie('mycurrent_lati') == "" || getCookie('mycurrent_lati') == null ){
$.ajax({
        type:"POST",
        url:'<?php echo WEB_PATH; ?>/process.php',
        data :'check_user_last_saved_current=true',
        async:false,
        success:function(msg){
			//alert(msg);
             var obj = jQuery.parseJSON(msg);
                      if(obj.mlatitude == "" || obj.mlatitude == ""){
                          flag= false;
                      }else{
                          // setCookie("searched_location",jQuery(".fancybox-inner #enter_zip").val(),365);
                        //    window.location.href=  WEB_PATH+"/search-deal.php";
						//alert("reload");
                         window.location.reload(false);
                      }
               }
               });
    
if(! flag)
    {
        //alert("in function calling");
		medaitor_loc(jQuery("#is_geo_location_supported").val()); 
	/*	
jQuery.fancybox({
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
	*/
            }
       return false;
    }
}
  jQuery(document).ready(function(){
          
	
		 if(locations.length<=1)
		 {
			jQuery(".info").css("display","none");
		 }
		 else
		 {
			jQuery(".info").css("display","block");
		 }
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
	  }
catch(e)
{
}
</script>
<script>
try{
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
jQuery("div.location_hrs a").toggle(function(){
	jQuery(this).next().css("display","block");
},function()
{
	jQuery(this).next().css("display","none");
});
}
catch(e)
{
}
function bind_deal_blk_hover()
{
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    }
    else{
	jQuery(document).ready(function(){
	jQuery('.deal_blk').hover(
    function(){
	   // alert(jQuery(this).find(".strip").length);
       var aplyview=getCookie("view");
		if(aplyview=="gridview")
		{
			 if(jQuery(this).find(".strip_grid").length > 0){
        var ele_strip = jQuery(this).find(".strip_grid");
        ele_strip.slideDown('300');
        }
		}
		else
		{
                    if(jQuery(this).find(".strip").length > 0){
        var ele_strip = jQuery(this).find(".strip");
        ele_strip.slideDown('300');
        }
                }
        
        jQuery(this).css('border-radius','5px 5px 5px 5px')
        .css('box-shadow','0 0 10px rgba(0,0,0,0.35)')
        .css('opacity','1')
    },
    function(){
          if(jQuery(this).find(".strip").length > 0){
           var ele_strip = jQuery(this).find(".strip");
         ele_strip.slideUp('300');
          }
          if(jQuery(this).find(".strip_grid").length > 0){
           var ele_strip = jQuery(this).find(".strip_grid");
         ele_strip.slideUp('300');
          }
        jQuery('.deal_blk').each(function(){
           jQuery(this).css('opacity','1'); 
        });
        //jQuery(this).css('background','none repeat scroll 0 0 #FFFFFF')
        jQuery(this).css('border-radius','')
        jQuery(this).css('box-shadow','')
        
    });
	});
    }
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
		var cust_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];}?>";
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

jQuery(".fancybox-inner #g-signin-custom a.login").live("click",function(){
	//alert(jQuery(this).attr("location_url"));
	//setCookie("location_page_referrer",jQuery(this).attr("location_url"),365);
});

</script>
