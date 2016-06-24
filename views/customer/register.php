<?php
/******** 
@USE : to register
@PARAMETER : 
@RETURN : 
@USED IN PAGES : mymerchants.php, twitter_login.php, forgot_password.php, popup_for_mymerchant.php,process.php, my-deals.php, process_mobile.php, reserve-campaign-schedular.php, frequent-mail-by-admin.php, login.php, location_detail.php, search-deal.php, activate.php, header.php, campaign.php
*********/
//don't remove
$q=$_SERVER['QUERY_STRING'];
unset($_REQUEST['load']);
$q=(!empty($_REQUEST))?$_REQUEST:'';

$c=$_COOKIE['get_url1'];
if(isset($_REQUEST['is_location']))
{
    $_SESSION['is_location']=1;
}
if(isset($_REQUEST['url']))
{
    $_SESSION['url_value']=$_REQUEST['url'];
}

/*
 $ser1=$_SERVER['SERVER_NAME'];
echo $ser1;

$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
echo "<br>"; 
echo $protocol;


$url=$protocol.$_SERVER['REQUEST_URI'];
echo $url;
 */


//error_reporting(E_ALL);
//require_once("classes/Config.Inc.php");
//echo $date_wh = "AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','+00:00') BETWEEN CONVERT_TZ(t.start_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1)) AND CONVERT_TZ(t.expiration_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1))";
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");

//$objDB = new DB();
//$objDBWrt = new DB('write');

//include_once(SERVER_PATH."/classes/active-campaign-scedular.php");
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
//print_r($_REQUEST);
// facebook login also return code variable.
// facebook login redirection problem solved as state variable only return from facebook not google.


if(isset($_REQUEST['campaign_id']) && isset($_REQUEST['l_id']))
{
	$cookie_time = time()+60*60*24*30;
	setcookie("shr_campaign_id",$_REQUEST['campaign_id'],$cookie_time,'','','');
	setcookie("shr_l_id",$_REQUEST['l_id'],$cookie_time,'','','');
	setcookie("shr_domain",$_REQUEST['domain'],$cookie_time,'','','');
	setcookie("shr_customer_id",$_REQUEST['customer_id'],$cookie_time,'','','');
	
}
	

if (isset($_GET['code']) && !isset($_GET['state'])) 
{ 
	/*
	echo "<pre>";
	print_r($_COOKIE);
	echo "</pre>";
	echo $_COOKIE['location_page_referrer'];
	exit();
	*/
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	
	$user 				= $google_oauthV2->userinfo->get();
	$user_id 				= $user['id'];
	$user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	$email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	$profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	$profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	$personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
	
	//echo "1";
	
	$where_clause = $array_values = array();
	$array = $json_array = $where_clause = array();
	$where_clause['emailaddress'] = $user['email'];
	
	//echo "2";
	
	$RS = $objDB->Show("customer_user", $where_clause);
	if($RS->RecordCount()>0)
	{
		//echo "3";
		$Row = $RS->FetchRow();
		$_SESSION['customer_id'] = $Row['id'];
		$_SESSION['customer_info'] = $Row;
		
		if($Row['profile_pic']=="")
		{
			$array_values['profile_pic'] = $user['picture'];
			
			$image_path_user=UPLOAD_IMG."/c/usr_pic/";
			$name ="usr_".$user['id'].".png";
			
			$fb_img_json= file_get_contents($array_values['profile_pic']);
			$fp  = fopen($image_path_user.$name, 'w+');
			fputs($fp, $fb_img_json);	
			
			$array_values['profile_pic']=$name;
			
		}
		$array_values['lastvisit_date'] = date("Y-m-d H:i:s");
		
		$array_values['active'] = 1;
		$array_values['is_registered'] = 1;
		$array_values['emailnotification'] = 1;
		$array_values['notification_setting'] = 1;
		
		$where_clause['id'] = $Row['id'];
		$objDB->Update($array_values, "customer_user", $where_clause);
		$_SESSION['google_usr_login'] = 1;
		
		
		/*
		echo "<pre>";
		print_r($_COOKIE);
		echo "</pre>";
		exit();
		*/
	
		if(isset($_COOKIE['shr_campaign_id']) && isset($_COOKIE['shr_l_id']) && $_COOKIE['shr_campaign_id']!="" && $_COOKIE['shr_l_id']!="")
		{
			$tmp_campaign_id = $_COOKIE['shr_campaign_id'];
			$tmp_l_id = $_COOKIE['shr_l_id'];
			$tmp_domain = $_COOKIE['shr_domain'];
			$tmp_customer_id = $_COOKIE['shr_customer_id'];
			
			$cookie_time = time()-3600;
			setcookie("shr_campaign_id",'',$cookie_time,'','','');
			setcookie("shr_l_id",'',$cookie_time,'','','');
			setcookie("shr_domain",'',$cookie_time,'','','');
			setcookie("shr_customer_id",'',$cookie_time,'','','');
	
			share_after_login_fdb_gp($tmp_campaign_id ,$tmp_l_id ,$tmp_domain,$tmp_customer_id);
			exit();
		}
						
		// 08-10-2014 solve problem of register page comming for seconds after facebook sign in
			
		if(isset($_SESSION['customer_id']))
		{
			//echo $_COOKIE['page'];
			//exit();
			if($_COOKIE['page']!="") // for popup google login for myoffer, mymerchant,shopredeem
			{
				$page_name=$_COOKIE['page'];
				//header("Location:".WEB_PATH."/".$page_name);
				//exit();
				//echo "5";
				//echo "hi 1";
				//exit();
				?>
				<script type="text/javascript">
					window.parent.location.href = '<?php echo WEB_PATH ."/". $_COOKIE['page']; ?>';								
					//window.close();
				</script>
				<?php
			}
			else
			{
				//echo $c;
				
				//echo "6";
				//original Redirect 
				//header("Location:".WEB_PATH."/search-deal.php");
				
				//change By Bhumin
				//$c=$_COOKIE['get_url'];
			    //header("Location:".WEB_PATH."/search-deal.php");
				//header("Location:".WEB_PATH.$c);
				if($c=="/register.php")
				{
					//echo "hi 2";
					//exit();
					header("Location:".WEB_PATH."/search-deal.php");
				}
				else
				{
					
					
					//echo "hi 3";

					// come from location page popup reserve button then google+ on register page
					
					$findme   = 'is_location=yes&url=';
					$pos = strpos($c, $findme);
					if ($pos === false)
					{												
					}
					else
					{
						//echo $c;
						//echo "</br>";	
						$newurl = substr($c,34);
						//echo $newurl;
						//exit();
						header("Location:".$newurl);
						exit();
						
					}
					
					// come from location page popup reserve button then google+ on register page
					
					$findme   = '/register.php?url=';
					$pos = strpos($c, $findme);
					if ($pos === false)
					{
						//echo "abc";
						//exit();
						// when click on google+ from popup					
	
							//echo "not found";
							header("Location:".WEB_PATH.$c);
							
						// when click on google+ from popup
						
					}
					else
					{
						//echo "def";
						//echo "found";
						//echo $c;
						//exit();
						$findme1   = '?purl=';
						$pos1 = strpos($c, $findme1);
						if ($pos1 === false)
						{
							//echo "ghi";
							//exit();
							$findme2   = 'location';
							$pos2 = strpos($c, $findme2);
							//echo $pos2;
							//exit();
							if ($pos2 === false)
							{
								// start when come from campaign page reserve button
								//echo $c;
								//echo "</br>";
								$newurl = substr($c,18);
								//echo $newurl;
								//exit();
								header("Location:".$newurl);
								exit();
			
								// end when come from campaign page reserve button
								
							}
							else
							{
								// start location page review image click then register page google+
								
								$newurl = substr($c,18);
								$newurl = urldecode($newurl);
								//echo $newurl;
								//exit();
								header("Location:".$newurl);
								exit();
			
								// start location page review image click then register page google+
							}
						}
						else
						{
							//echo "jkl";
							//exit();
							// when click on reserve from popup then google+ on register page
						
							//echo "not found";
							$newurl = substr($c,18);
							$newurl = str_replace("/register.php?url=","",$newurl);
							$newurl = str_replace("?purl=","#",$newurl);
							//echo $newurl;
							//exit();
							header("Location:".$newurl);
							
							// when click on reserve from popup then google+ on register page
						}
						
						
					}
				}
				
				exit();
			}
		}
	}
	else
	{
		//echo "4";
		$where_clause = $array_values = array();
		$_SESSION['google_usr_login'] = 1;
		$array_values['emailaddress'] = $user['email'];
		$array_values['firstname'] = $user['given_name'];
		$array_values['lastname'] = $user['family_name'];
		$array_values['registered_date'] = date("Y-m-d H:i:s");
		$array_values['lastvisit_date'] = date("Y-m-d H:i:s");
		$array_values['active'] = 1;
		$array_values['profile_pic'] = $user['picture'];
			
			
		$image_path_user=UPLOAD_IMG."/c/usr_pic/";
		$name ="usr_".$user['id'].".png";
		
		$fb_img_json= file_get_contents($array_values['profile_pic']);
		$fp  = fopen($image_path_user.$name, 'w+');
		fputs($fp, $fb_img_json);	
		
		$array_values['profile_pic']=$name;
			
		$array_values['is_registered'] = 1;
		$array_values['emailnotification'] = 1;
		$array_values['notification_setting'] = 1;
		
		$objDB->Insert($array_values, "customer_user");
			
		$where_clause['emailaddress'] = $user['email'];
		$RS = $objDB->Show("customer_user", $where_clause);
		$Row = $RS->FetchRow();
		$_SESSION['customer_id'] = $Row['id'];
		$_SESSION['customer_info'] = $Row;
			
		$array_settings = array();
		$array_settings['campaign_email'] = 1;
		$array_settings['subscribe_merchant_new_campaign'] = 1;
		$array_settings['subscribe_merchant_reserve_campaign'] = 1;
		$array_settings['customer_id'] =  $Row['id'] ;
		$array_settings['merchant_radius'] = 1;
		   
		$c_id = $objDB->Insert($array_settings, "customer_email_settings");
		
		
		if(isset($_COOKIE['shr_campaign_id']) && isset($_COOKIE['shr_l_id']) && $_COOKIE['shr_campaign_id']!="" && $_COOKIE['shr_l_id']!="")
		{
			$tmp_campaign_id = $_COOKIE['shr_campaign_id'];
			$tmp_l_id = $_COOKIE['shr_l_id'];
			$tmp_domain = $_COOKIE['shr_domain'];
			$tmp_customer_id = $_COOKIE['shr_customer_id'];
			
			$cookie_time = time()-3600;
			setcookie("shr_campaign_id",'',$cookie_time,'','','');
			setcookie("shr_l_id",'',$cookie_time,'','','');
			setcookie("shr_domain",'',$cookie_time,'','','');
			setcookie("shr_customer_id",'',$cookie_time,'','','');
	
			share_after_login_fdb_gp($tmp_campaign_id ,$tmp_l_id ,$tmp_domain,$tmp_customer_id);
			exit();
		}
		
		if(isset($_SESSION['customer_id']))
		{
			if($_COOKIE['page']!="") // for popup google login for myoffer, mymerchant,shopredeem
			{
				$page_name=$_COOKIE['page'];
				//header("Location:".WEB_PATH."/".$page_name);
				//exit();
				//echo "7";
				?>
				<script type="text/javascript">
					window.parent.location.href = '<?php echo WEB_PATH ."/". $_COOKIE['page']; ?>';				
					
					//window.close();
				</script>
				<?php
			}
			else
			{
				//echo "6";
				//original Redirect 
				//header("Location:".WEB_PATH."/search-deal.php");
				
				//change By Bhumin
				//$c=$_COOKIE['get_url'];
			    //header("Location:".WEB_PATH."/search-deal.php");
				//header("Location:".WEB_PATH.$c);
				if($c=="/register.php")
				{
					//echo "hi 2";
					//exit();
					header("Location:".WEB_PATH."/search-deal.php");
					
				}
				else
				{
					
					//echo $c;
					//echo "hi 3";
					//exit();
					$findme   = '/register.php?url=';
					$pos = strpos($c, $findme);
					if ($pos === false)
					{
						// when click on google+ from popup
						
							//echo "not found";
							header("Location:".WEB_PATH.$c);
							
						// when click on google+ from popup
						
					}
					else
					{
						//echo "found";
						
						$findme1   = '?purl=';
						$pos1 = strpos($c, $findme1);
						if ($pos1 === false)
						{
							//echo "ghi";
							//exit();
							$findme2   = 'location';
							$pos2 = strpos($c, $findme2);
							//echo $pos2;
							//exit();
							if ($pos2 === false)
							{
								// start when come from campaign page reserve button
								
								$newurl = substr($c,18);
								header("Location:".$newurl);
								exit();
			
								// end when come from campaign page reserve button
								
							}
							else
							{
								// start location page review image click then register page google+
								
								$newurl = substr($c,18);
								$newurl = urldecode($newurl);
								//echo $newurl;
								//exit();
								header("Location:".$newurl);
								exit();
			
								// start location page review image click then register page google+
							}
						}
						else
						{
							// when click on reserve from popup then google+ on register page
						
							//echo "not found";
							$newurl = substr($c,18);
							$newurl = str_replace("/register.php?url=","",$newurl);
							$newurl = str_replace("?purl=","#",$newurl);
							//echo $newurl;
							//exit();
							header("Location:".$newurl);
							
							// when click on reserve from popup then google+ on register page
						}
						
						
					}
				}
				exit();
			}
		}
	}
	return;
}
if (isset($_SESSION['token'])) 
{ 
	$gClient->setAccessToken($_SESSION['token']);
}
if ($gClient->getAccessToken()) 
{
	//For logged in user, get details from google using access token	
}
else 
{
	//For Guest user, get google login url
	$authUrl = $gClient->createAuthUrl();
}

//echo $authUrl;
if(isset($authUrl)) //user is not logged in, show login button
{
}
else
{
	
}
//$_SESSION['customer_id']=122;

//echo md5("11111");


//print_r( $_SESSION);
//echo $_REQUEST['url']."====";
$f_url = '';
if(isset($_REQUEST['url']))
{
   $url = $_REQUEST['url'];
    $parts = parse_url($url);
    //if(isset($parts['query']))
    //{
		parse_str($parts['query'], $query);
	//}
    //echo $query['purl'];
	
	//if(isset($query['purl']))
	//{
		$f_url = urlencode($query['purl']);
		$url= str_replace("?purl=", "#", $url);
		$url =str_replace($query['purl'], "", $url);
		$f_url = $url.$f_url;
	//}
}	
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
//echo $medium;
//exit();


//include_once(LIBRARY."/fb-sdk/src/facebook_secret.php");

$facebook = new Facebook(array(
  'appId'  => facebookappId,
  'secret' => facebooksecret,
));


//setcookie('scanflip_customer_id', "", time()-3650,'','','');
//echo $_COOKIE['scanflip_customer_id']."========";
/*
$facebook = new Facebook(array(
  'appId'  => '480932418587835',
  'secret' => 'f5d50c8795716a5a91e7baf98db784f7',
));
*/
//$_SESSION['customer_id'] =122;
 $user = $facebook->getUser();
if ($user) {
  try {
    $user_profile = $facebook->api('/me');
   
    //$email=$facebook->api('/me', array('fields' => 'id,email'));
    //print_r($email);
    //print_r($user_profile);
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}


if($user){
//echo "1";
?>
<!--
<html> 
	<body style="margin-top: 10%;">
		<center>
			<img src="<?php echo WEB_PATH ?>/images/scanflip_loading.gif?123" alt="ScanFlip Logo">		
		</center>
	</body>
</html>
-->
<?php
//sleep(5);
//exit();
	//echo "<pre>";
	//print_r($user_profile);
	//print_r($_SESSION);
	//echo "</pre>";
	//$objUser->add_facebook_user($user_profile);
        
    $where_clause = $array_values = array();
        $array = $json_array = $where_clause = array();
	$where_clause['emailaddress'] = $user_profile['email'];
	
        $RS = $objDB->Show("customer_user", $where_clause);
	if($RS->RecordCount()>0){
	//echo "2";
		$Row = $RS->FetchRow();
		$_SESSION['customer_id'] = $Row['id'];
		$_SESSION['customer_info'] = $Row;
		
		if($Row['profile_pic']=="")
		{
			$array_values['profile_pic'] = "https://graph.facebook.com/".$user."/picture";
			//echo $array_values['profile_pic'];
			//exit();
		
			// 12/08/2014 store fb image
			
			$image_path_user=UPLOAD_IMG."/c/usr_pic/";
			$name ="usr_".$user.".png";
			
			$fb_img_json= file_get_contents($array_values['profile_pic']);
			$fp  = fopen($image_path_user.$name, 'w+');
			fputs($fp, $fb_img_json);	
			
			$array_values['profile_pic']=$name;
			
			// 12/08/2014 store fb image
		}
		$array_values['dob_year'] = date("Y", strtotime($user_profile['birthday']));
		$array_values['dob_month'] = date("m", strtotime($user_profile['birthday']));
		$array_values['dob_day'] = date("d", strtotime($user_profile['birthday']));
		$array_values['lastvisit_date'] = date("Y-m-d H:i:s");
		
		$array_values['active'] = 1;
		$array_values['is_registered'] = 1;
		$array_values['emailnotification'] = 1;
		$array_values['notification_setting'] = 1;
		
		$where_clause['id'] = $Row['id'];
		$objDB->Update($array_values, "customer_user", $where_clause);
		$_SESSION['facebook_usr_login'] = 1;
		//echo "3";
		// 08-10-2014 solve problem of register page comming for seconds after facebook sign in
		/*
		if(isset($_SESSION['customer_id']))
		{
			header("Location:".WEB_PATH."/search-deal.php");
		}
		*/
		//echo "123";
		// 08-10-2014 solve problem of register page comming for seconds after facebook sign in
	}else{
	//echo "4";
	$_SESSION['facebook_usr_login'] = 1;
		$array_values['emailaddress'] = $user_profile['email'];
		$array_values['firstname'] = $user_profile['first_name'];
		$array_values['lastname'] = $user_profile['last_name'];
		$array_values['dob_year'] = date("Y", strtotime($user_profile['birthday']));
		$array_values['dob_month'] = date("m", strtotime($user_profile['birthday']));
		$array_values['dob_day'] = date("d", strtotime($user_profile['birthday']));
		$array_values['registered_date'] = date("Y-m-d H:i:s");
		$array_values['lastvisit_date'] = date("Y-m-d H:i:s");
		$array_values['active'] = 1;
		$array_values['profile_pic'] = "https://graph.facebook.com/".$user."/picture";
		
		// 12/08/2014 store fb image
		
		$image_path_user=UPLOAD_IMG."/c/usr_pic/";
		$name ="usr_".$user.".png";
		
		$fb_img_json= file_get_contents($array_values['profile_pic']);
		$fp  = fopen($image_path_user.$name, 'w+');
		fputs($fp, $fb_img_json);	
		
		$array_values['profile_pic']=$name;
		
		// 12/08/2014 store fb image
		
		$array_values['is_registered'] = 1;
		$array_values['emailnotification'] = 1;
		$array_values['notification_setting'] = 1;
		
		$objDB->Insert($array_values, "customer_user");
		
		$where_clause['emailaddress'] = $user_profile['email'];
		$RS = $objDB->Show("customer_user", $where_clause);
		$Row = $RS->FetchRow();
		$_SESSION['customer_id'] = $Row['id'];
		$_SESSION['customer_info'] = $Row;
		
		$array_settings = array();
        $array_settings['campaign_email'] = 1;
		$array_settings['subscribe_merchant_new_campaign'] = 1;
		$array_settings['subscribe_merchant_reserve_campaign'] = 1;
        $array_settings['customer_id'] =  $Row['id'] ;
        $array_settings['merchant_radius'] = 1;
       
		$c_id = $objDB->Insert($array_settings, "customer_email_settings");
		//echo "5";
	}
	//echo "6";
	
	/*
	echo "<pre>";
	print_r($_REQUEST);
	echo "</pre>";	
	exit();
	*/	
		
	if(isset($_REQUEST['campaign_id']) && isset($_REQUEST['l_id']))
	{
		share_after_login_fdb_gp($_REQUEST['campaign_id'],$_REQUEST['l_id'],$_REQUEST['domain'],$_REQUEST['customer_id']);
		exit();
	}
     //header("Location:search-deal.php");
	 //header("Location:'http://www.scanflip.com/search-deal.php");
        if(isset($_SESSION['url_value']))
        {
			//echo "abc";
            $url1 = $_SESSION['url_value'];
			$parts1 = parse_url($url1);
			parse_str($parts1['query'], $query);

			//echo $query['purl'];
			$url_arr = explode("?",$url1);
			if($query['purl'] != "")
			{
				//echo "def";
				$f_url1 = urlencode($query['purl']);
				$url1= str_replace("?purl=", "#", $url1);
				$url1 =str_replace($query['purl'], "", $url1);
				$f_url1 = $url1.$f_url1;
				//echo f_url1."===";
				$v = trim($query['purl'],"@");
				$arr = explode("|",$v);
				if($arr[0] != 00)
				{
			
					if(isset($_SESSION['is_location']))
					{
						$f_url1 =check_campaign_criteria($arr[0] , $arr[1]);
						$ex_arr = explode("#",$f_url1);
						$f_url1 =$url_arr[0]."#".$ex_arr[1];
					}
					else
					{
						$f_url1 =check_campaign_criteria($arr[0] , $arr[1]);
					}
				}
				else
				{
					$url_perameter_2 = rawurlencode("@".$arr[0]."|".$arr[1]."@");
					$f_url1 = $parts1['scheme']."://".$parts1['host']."".$parts1['path']."#".$url_perameter_2 ;
				}

			}
			else
			{
				//echo "ghi";
				$f_url1 = $_SESSION['url_value'];
			}
			//echo $f_url1;
			//exit;
			//check_campaign_criteria($campaign_id , $location_id)
	?>
	
             <script>
			
				window.location.href='<?php echo $f_url1; ?>';
		   
            </script>
			
			<?php
			//echo "jkl";	
			unset($_SESSION['url_value']);			
			unset($_SESSION['is_location']);
			exit;			
        }
        else
        {
			/*
			echo "<pre>";
			print_r($_REQUEST);
			echo "</pre>";
			$newurl = str_replace("?purl=","#",$_REQUEST['url']);
			echo $newurl;
			exit();
			*/
			
			
			
			if(isset($_REQUEST['url']))
			{
				$newurl = str_replace("?purl=","#",$_REQUEST['url']);
				
				// fb login from register page comming from popup reserve
				
				header("Location:".$newurl);
				
				// fb login from register page comming from popup reserve
				
			}
			else
			{
				// normal fb login from register page
				//echo "noraml";
					
				echo "<script>window.location.href='search-deal.php';</script>";
				
				//echo "not redirect";
				exit();
				// normal fb login from register page
				
			}
			
        }
         
        	
}
else
{

	if(isset($_SESSION['customer_id']))
		{
		
			// 07-02-2015 comment below line because user is already loggedin and come from share link it reserves offer but redirect to searchdeal page.
			//header("Location:".WEB_PATH."/search-deal.php"); // if user login and go to register page , redirects to searchdeal page
			// 07-02-2015 comment below line because user is already loggedin and come from share link it reserves offer but redirect to searchdeal page.

		}
}


if(isset($_SESSION['fb_480932418587835_user_id'])){
	header("Location: my-account.php");
	exit();
}
$params = array(
  'scope' => 'user_birthday, friends_birthday, user_location, friends_location, email'
  //'redirect_uri' => 'http://www.scanflip.com/search-deal.php'
);

$loginUrl = $facebook->getLoginUrl($params);
 $path_captcha=WEB_PATH."/captcha.php";
 
 if(isset($_REQUEST['location_id']))
 {
     $l_id_s = $_REQUEST['location_id'];
 }
 else{
      $l_id_s = "";
 }
?>
<?php //echo $file=$_SERVER['DOCUMENT_ROOT']; ?>
<!DOCTYPE HTML>
<html >
<head>
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
</script>
<title>ScanFlip | Powering Smart Savings from Local Merchants</title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="twitter:card" content="app">
<meta name="twitter:site" content="@scanflip"/>
<meta name="twitter:app:country" content="No"/>
<meta name="twitter:description" content="ScanFlip | Powering Smart Savings from Local Merchants"/>
<meta name="twitter:app:name:googleplay" content="Scanflip"/>
<meta name="twitter:app:id:googleplay" content="com.lanet.scanflip"/>

<!-- please validate go through https://dev.twitter.com/cards/types/app -->
  
<meta name="twitter:app:name:iphone" content="Scanflip" />
<meta name="twitter:app:id:iphone" content="5408712" />
<meta name="twitter:app:url:iphone" content="scanflip://action/OBP22khyDh7t9cxKvuWA" />
<meta name="twitter:app:name:ipad" content="Scanflip" />
<meta name="twitter:app:id:ipad" content="5408712" />
<meta name="twitter:app:url:ipad" content="example://action/OBP22khyDh7t9cxKvuWA" />



<?php

if(isset($_REQUEST['campaign_id']) && isset($_REQUEST['l_id']))
{
    /* $Sql_new = "select * from campaigns  where id=".$_REQUEST['campaign_id'];
    $RS_user_data = $objDB->Conn->Execute($Sql_new); */
    $RS_user_data = $objDB->Conn->Execute("select * from campaigns  where id=?",array($_REQUEST['campaign_id']));
    //echo $RS_user_data->fields['title'];
     $img_src="";
	if( $RS_user_data->fields['business_logo']!="")
	{
		$img_src=ASSETS_IMG."/m/campaign/".$RS_user_data->fields['business_logo']; 
	}
	else 
	{
		$img_src=ASSETS_IMG."/c/Merchant_Offer.png";
	}       

	$tag_main="";
	if($RS_user_data->fields['campaign_tag'] != "")
	{
		$fb_campaign_tag_temp=  explode(",",$RS_user_data->fields['campaign_tag']);
		$tag_count= count($fb_campaign_tag_temp);

	   
		
			for($i=0;$i<$tag_count;$i++)
			{
				$tag_main.="#".$fb_campaign_tag_temp[$i]." ";

			}
		
	}
	$main_title=htmlspecialchars(urlencode($RS_user_data->fields['title']));

?>
<!--<meta property="og:image" content="<?php //echo $img_src;?>"/>
<meta property="og:description" content=""/>
<meta property="og:type" content="article" /> -->
 
<?php
}

?>

<!--<script type="text/javascript" src="<?=WEB_PATH?>/js/jquery-1.6.2.min.js"></script>-->
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/pass_strength.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/con_pass_strength.js"></script>

<script language="javascript">

var location_id = "<?=$l_id_s?>";

function validate_login()
{
    var msg="";
	var flag="true";
	if(flag == "true")
	{
		if(email_validation(document.getElementById("lemail").value) == false)
		{
			//alert("Please enter valid email");
			//document.getElementById("emailaddress").focus();
			//return false;
			msg +="<div><?php echo $client_msg['login_register']['Msg_valid_email']; ?></div>";
			flag="false";
		}
		if(document.getElementById("lpassword").value == "")
		{
			//alert("Please enter password");
			//document.getElementById("password").focus();
			//return false;
			msg +="<div><?php echo $client_msg['login_register']['Msg_password']; ?></div>";
			flag="false";
		}
		
		var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
		var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
		var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
        if(flag == "false")
        {
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
			return false;
		}
		else
		{
			return true;
		}
                
	}
}
function validate_register(){
    
 var msg="";
  var flag="true";
  if(flag == "true")
      {
	if(email_validation(document.getElementById("emailaddress").value) == false){
		//alert("Please enter valid email");
		//document.getElementById("emailaddress").focus();
		//return false;
                msg +="<div><?php echo $client_msg['login_register']['Msg_valid_email']; ?></div>";
                flag="false";
	}
	if(document.getElementById("password").value == ""){
		//alert("Please enter password");
		//document.getElementById("password").focus();
		//return false;
                msg +="<div><?php echo $client_msg['login_register']['Msg_password']; ?></div>";
                flag="false";
	}
	if(document.getElementById("password").value != document.getElementById("con_password").value){
		//alert("Password does not match");
		//document.getElementById("con_password").focus();
		//return false;
                msg +="<div><?php echo $client_msg['login_register']['Msg_Confirm_password']; ?></div>";
                flag="false";
	}
        if(document.getElementById("mycaptcha").value == ""){
		//alert("The text you entered did not match the security check");
		//document.getElementById("mycaptcha").focus();
		//return false;
                msg +="<div><?php echo $client_msg['login_register']['Msg_captcha']; ?></div>";
                flag="false";
		}
		else
		{
			var code=jQuery("#mycaptcha").val();
			jQuery.ajax({
			       url:"process.php",
				   data:"captchacode_check_c_r=yes&code="+code,
			       cache: false,
				   async: false,
			       success: function(result){
						if(result == "1")
						{
							flag="true";
						}
						else
						{
							flag="false";
							msg +="<div><?php echo $client_msg['login_register']['Msg_captcha']; ?></div>";
						}
				
			     
					}
			  });
		}
		
	if(document.getElementById("agree").checked == false){
		//alert("Please agree with terms and conditions password");
		//document.getElementById("password").focus();
		//return false;
                msg +="<div><?php echo $client_msg['login_register']['Msg_agree_with_scanflip']; ?></div>";
                flag="false";
	}
	if(document.getElementById("password").value != document.getElementById("con_password").value){
		//alert("Password does not match");
		//document.getElementById("con_password").focus();
		//return false;
               
                flag="false";
	}
        var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
	var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msg+"</div>";
	var footer_msg="<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
	jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
        if(flag == "false")
            {
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
                    return false;
            }
            else
                {
                    return true;
                }
      }
}
function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}
$s=jQuery.noConflict();
$s(document).ready(function() { 
    // bind form using ajaxForm 
    $s('#reg_form').ajaxForm({ 
		beforeSubmit: validate_register,
        dataType:  'json', 
        success:   processRegJson 
    });
	$s('#login_frm').ajaxForm({ 
		beforeSubmit: validate_login,
        dataType:  'json', 
        success:   processLogJson 
    });
	/*
    $s("#captcha_image").click(function(){
	     var captcha_path="<?php echo $path_captcha;?>";
	     $s("#captcha_ajax_loading").css("display","block");
	   
	             $s.ajax({
			       url:captcha_path,
				//   data:"rand="+Math.random(),
			       //cache: false,
				   //async: true,
			       success: function(result){
					//alert("sucess");	
					
				//location.reload();
				
				//$s("#captcha_image_src").attr('src','captcha.gif');
				$s("#captcha_ajax_loading").css("display","none"); 
				 d = new Date();
				 $s("#captcha_image_src").attr('src','captcha.gif?'+d.getTime());
				
			     
				 }
			  });
		     
	    });

		$s("#captcha_image").trigger("click");
		*/
		
		jQuery('#captcha_image').click(function() {  
			
			change_captcha();
		 });
	 	 function change_captcha()
		 {
			document.getElementById('captcha').src="get_captcha_c_r.php?rnd=" + Math.random();
		 }
		 
   jQuery('.fancybox').fancybox({
				href: this.href,
				//href: $(val).attr('mypopupid'),
				//content:#myDivID_3,
				width: 400,
				height: 335,
				type: 'iframe',
				openEffect : 'elastic',
				openSpeed  : 300,

				closeEffect : 'elastic',
				closeSpeed  : 300,
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				} // helpers
				
				
			}); // fancybox	
	
});
function processRegJson(data) {
    
	if(data.status == "true"){
//            if(data.share != "true")
//                {
		if(data.action == 0){
								if(location_id == "" && document.getElementById("lurl").value == "" )
								{
									window.location = "search-deal.php";
								}
								else if(location_id != "")
								{
									window.location = "process.php?btnRegisterStore=1&location_id="+location_id;
								}
								else
								{
									window.location = document.getElementById("lurl").value;
								}
		}else{
			/*
			alert(data.message);       
            window.location = "<?php echo WEB_PATH ?>/register.php";
			return false;
			*/
			var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+data.message+"</div>";
                var footer_msg="<div style='text-align:center'><hr>";
				footer_msg = footer_msg + '<input type="button" onclick="window.location=\'register.php\'" value="<?php echo $client_msg["index"]["Btn_Fancy_Ok"];?>" id="popupcancel" name="popupcancel" style="padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;"></div>';
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
//                }
//                else{
//                    window.location = "<?php echo WEB_PATH;?>/campaign.php?campaign_id="+data.c_id+"&l_id="+data.l_id;
//                }
		
	}else{
		//alert(data.message);
               var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+data.message+"</div>";
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
		return false;
	}
     
}
function processLogJson(data) { 
	//alert(data.redirecting_path);
//alert(data.share);
	if(data.status == "true"){
            if(data.share != "true")
                {
					//alert("not sharing");
								if(location_id == "" && document.getElementById("lurl").value == "" )
								{
									//alert("in if");
									window.location = "search-deal.php";
								}
								else if(location_id != "")
								{
									//alert("in else if");
									window.location = "process.php?btnRegisterStore=1&location_id="+location_id;
								}
								else
								{
									//alert("inelse");
									//alert('<?php echo WEB_PATH;?>/'+data.redirecting_path);
									//alert(document.getElementById("lurl").value);
									if(data.redirecting_path != "")
									{
										window.location = data.redirecting_path;
									}
									else
									{
										window.location = document.getElementById("lurl").value;
									}
								}
                }
				else
				{
					//alert("In main else");
                    window.location = data.permalink;
                }
	}else{
                var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+data.message+"</div>";
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
                    return false;
           
	
	}
     
}

</script>
<link href="<?php echo ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">
<?php //Change By Bhumin ?>
<link href='https://fonts.googleapis.com/css?family=Cuprum&amp;subset=latin' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS?>/c/jquery.confirm_c/jquery.confirm.css" />

</head>

<?php 
//if(isset($_REQUEST['campaign_id']) && isset($_REQUEST['l_id']) && isset($_REQUEST['code']))
if(isset($_REQUEST['code']))
{
?>
<body style="display:none;">
<?php
}else{
?>
<body>
<?php
}
?>

<?php require_once(CUST_LAYOUT."/header.php");?>
<div id="content" class="cantent">
	<div class="my_main_div">
		<div id="contentContainer" class="contentContainer">
			<div class="scan_main">
				<div class="scan_regis" ><?php echo $client_msg["login_register"]["label_Register_Sign_In"];?></div>
				
				<!--<div class="scan_login">Login</div>-->
				<div class="mt20" >
									   
					 <div class="left ml5 btn_google_register">
										  <span  id="g-signin-custom">
												<a class="login" href="<?php echo $authUrl ?>">
													<img src="<?php echo ASSETS_IMG.'/c/signing.png' ?>" />
												</a>
										  </span>
									   </div>
					
					   <div class="left ml6 btn_facebook_register">
											<a href="<?=$loginUrl?>" target="_parent"  >
												 <img src="<?php echo ASSETS_IMG.'/c/signinfb.png' ?>" />
											</a>
										</div>
					   </div>
		<!--<span class="recommand">(Recommended)</span>-->
			</div>
    
  
	
<div class="main-regi">
<div class="left-side">
	<?php 
	if(isset($_REQUEST['lmsg']))	{
	?>
		<div class="success">
			<img src="<?php echo ASSETS_IMG ?>/c/hoory.png" alt="" />
		   <span><?=$_REQUEST['lmsg']?></span>
		</div>
	<?php
	}
	else
	{
	?>
		<div class="success" style="display:none;">
			<img src="<?php echo ASSETS_IMG ?>/c/hoory.png" alt="" />
<!--		   <span>
					<?php
						if(isset($_REQUEST['lmsg']))
							echo $_REQUEST['lmsg'];
					?>
				</span>-->
		</div>
	<?php
	}
	?>
	<div valign="top"  class="registerdiv">
	
            <?php

                if(isset($_REQUEST['campaign_id']) && isset($_REQUEST['share']))
                {
                   
                    if(isset($_SESSION['customer_id']))
                    {
					
						// 18-03-2014 pageview counter logic
					
							$array_loc=array();
							$array_loc['id'] = $_REQUEST['l_id'];
							$RS_location = $objDB->Show("locations",$array_loc);
							$time_zone=$RS_location->fields['timezone_name'];
							date_default_timezone_set($time_zone);
							
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

							//echo "campid - ".$_COOKIE['campaign_id']." locid -".$_COOKIE['l_id']." domain - ".$_REQUEST['domain']." medium - ".$medium." timestamp - ".date("Y-m-d H:i:s");
							//exit();
							
							$user_datetime="<script>document.write(formattedDate())</script>";
							
							//$findme="</script>";
							//$pos = strpos($user_datetime, $findme);
							//echo $pos."<br/>";
							//echo substr($user_datetime, $pos)."<br/>";
							
							$pageview_array = array();
							$pageview_array['campaign_id'] = $_REQUEST['campaign_id'];
							$pageview_array['location_id'] = $_REQUEST['l_id'];
							$pageview_array['pageview_domain'] = $_REQUEST['domain'];
							$pageview_array['pageview_medium'] = $medium;
							$pageview_array['timestamp'] = date("Y-m-d H:i:s");
							//$pageview_array['timestamp'] = $user_datetime;
							
							$objDB->Insert($pageview_array, "pageview_counter");
							$pageview_array = array();
												
							
							// 18-03-2014 pageview counter logic
					
					
                        
                        //echo "1";
			//echo $_SESSION['customer_id'] ."===". base64_decode($_REQUEST['customer_id']);
                      //  exit;
						if($_SESSION['customer_id'] != base64_decode($_REQUEST['customer_id']))
						{
                        //echo "2";  
					
							
							 /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list */
				/* $Sql_max_is_walkin = "SELECT is_walkin , new_customer ,level from campaigns WHERE id=".$_REQUEST['campaign_id'];
				$RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin); */
				$RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin , new_customer ,level from campaigns WHERE id=?",array($_REQUEST['campaign_id']));
			   
			  // if($RS_max_is_walkin->fields['is_walkin'] == 1)
			   //{
				
				/* $Sql_c_c = "SELECT * FROM customer_campaigns WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$_REQUEST['campaign_id']."' AND location_id =".$_REQUEST['l_id'];
				$RS_c_c = $objDB->Conn->Execute($Sql_c_c); */
				$RS_c_c = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?",array($_SESSION['customer_id'],$_REQUEST['campaign_id'],$_REQUEST['l_id']));
				
                //echo "<br />".$RS_c_c->RecordCount()."Record count";
                //exit();
		 if($RS_c_c->RecordCount()<=0){
				
				/* $Sql = "SELECT * FROM activation_codes WHERE campaign_id=".$_REQUEST['campaign_id'];
				$RS_1 = $objDB->Conn->Execute($Sql); */
				$RS_1 = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE campaign_id=?",array($_REQUEST['campaign_id']));
				
				if($RS_1->RecordCount()>0){
					
					/* $location_max_sql = "Select num_activation_code , offers_left , used_offers from campaign_location where  campaign_id=".$_REQUEST['campaign_id']." and location_id=".$_REQUEST['l_id'];
					$location_max = $objDB->Conn->Execute($location_max_sql); */
					$location_max = $objDB->Conn->Execute("Select num_activation_code , offers_left , used_offers from campaign_location where  campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
					
          $offers_left = $location_max->fields['offers_left'];
                    $used_campaign = $location_max->fields['used_offers'];
           $o_left = $location_max->fields['offers_left'];
           $share_flag = 1;
        $its_new_user =0;
//        echo $o_left."offer left =========";
//        exit;
           if(  $o_left>0)
                                      {
                if($RS_max_is_walkin->fields['new_customer'] == 1)
               {
                    //   $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=". $_REQUEST['l_id'];
						
						/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where  location_id=". $_REQUEST['l_id']." and customer_id=".$_SESSION['customer_id'].") ";
                        $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                        $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where  location_id=? and customer_id=?)",array($_REQUEST['l_id'],$_SESSION['customer_id']));
                        
                        if($subscibed_store_rs->RecordCount()==0)
                        {
                              $its_new_user =1;
                            $share_flag= 1;
                        }
                        else {
                              $its_new_user =0;
                              $share_flag= 0;
                        }
               }
               
               /* check whether new customer for this store */
               $allow_for_reserve= 0;
               
               $is_new_user= 0;
               /*************** *************************/
				/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=". $_REQUEST['l_id'].") ";
				// echo $sql_chk."sql_chk<br/>";
				$Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
				$Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($_SESSION['customer_id'],$_REQUEST['l_id']));
				
                                            if($Rs_is_new_customer->RecordCount()==0)
                                            {
                                                $is_new_user= 1;
                                            }
                                            else {
                                                  $is_new_user= 0;
                                            }
                /*************** *************************/
                if($is_new_user == 1)
                {
                    $allow_for_reserve = 1;
                }
                else {
                    
                
				/* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_REQUEST['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_REQUEST['l_id'];
                // echo "<br/>===".$sql."===<br />";
				$RS_campaign_groups = $objDB->Conn->Execute($sql); */
				$RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
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
                                    $RS_check_s = $objDB->Conn->Execute($Sql_new_); */          
                                    $RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?))",array($_SESSION['customer_id'],$c_g_str));
                                    
                                      while($Row_Check_Cust_group = $RS_check_s->FetchRow())
                                                {
													/* $query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
                                                    $RS_query = $objDB->Conn->Execute($query); */
													$RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?)",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id'],$c_g_str));
													
                                                     if($RS_query->RecordCount() > 0)
                                                     {

                                                          $is_it_in_group = 1;
                                                     }
                                                }
                                      if($is_it_in_group == 1 )
                                            { 
                                                  $allow_for_reserve = 1;  	
                                            }
                                           else {
                                                $allow_for_reserve = 0;
                                           }
                                             }
                                            else{
                                                $allow_for_reserve = 0;  	
                                            }
                                             }
                                             else{
                                       // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                          
                                          /* $query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$_REQUEST['l_id']." and mg.private =1 ) ";
                                          $RS_all_user_group = $objDB->Conn->Execute($query); */
                                          $RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =1 )",array($_SESSION['customer_id'],$_REQUEST['l_id']));
                                          
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
                                     $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$_REQUEST['l_id']."  )";
                                     $allow_for_reserve= 0;
                                }
                                }
                               // echo "<br />SQl_new===".$Sql_new_ ."=====<br />";
                       
                            /* for checking whether customer in campaign group */
             //  echo $allow_for_reserve."==== alllow for reserve";
             ///  exit;
               if($share_flag== 1)
               {
                      if($allow_for_reserve==1 || $its_new_user ==1){
					$activation_code = $RS_1->fields['activation_code'];
					
								/* $Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
									customer_id='".$_SESSION['customer_id']."', campaign_id=".$_REQUEST['campaign_id']." , location_id=".$_REQUEST['l_id'];
								$objDB->Conn->Execute($Sql); */
								$objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
									customer_id=?, campaign_id=?, location_id=?",array($_SESSION['customer_id'],$_REQUEST['campaign_id'],$_REQUEST['l_id']));
								
                                         // ///
                                    /* $RSLocation_nm  =  $objDB->Conn->Execute("select * from locations where id =".$_REQUEST['l_id']); */
									$RSLocation_nm = $objDB->Conn->Execute("select * from locations where id =?",array($_REQUEST['l_id']));
										
                                    //$br =$_SESSION['customer_id'].substr($activation_code,0,2).$_REQUEST['campaign_id'].substr($RSLocation_nm->fields['location_name'],0,2).$_REQUEST['l_id'];
                                    $br = $objJSON->generate_voucher_code($_SESSION['customer_id'],$activation_code,$_REQUEST['campaign_id'],$RSLocation_nm->fields['location_name'],$_REQUEST['l_id']);

                                    /* $insert_coupon_code = "Insert into coupon_codes set customer_id=".$_SESSION['customer_id']." , customer_campaign_code=".$_REQUEST['campaign_id']." , coupon_code='".$br."' , active=1 , location_id=".$_REQUEST['l_id']." , generated_date='".date('Y-m-d H:i:s')."' ";
                                    $objDB->Conn->Execute($insert_coupon_code); */
                                    $objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=?, customer_campaign_code=?, coupon_code=?, active=1 , location_id=?, generated_date=?",array($_SESSION['customer_id'],$_REQUEST['campaign_id'],$br,$_REQUEST['l_id'],date('Y-m-d H:i:s')));
                                    
                                    /* $update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$_REQUEST['campaign_id']." and location_id =".$_REQUEST['l_id']." ";
									$objDB->Conn->Execute($update_num_activation); */
									$objDBWrt->Conn->Execute("Update  campaign_location set offers_left=?, used_offers=? where campaign_id=? and location_id =?",array(($offers_left-1),($used_campaign+1),$_REQUEST['campaign_id'],$_REQUEST['l_id']));
									
                                      }
               }
                                      }          // ///
				}
				
		  }
                  else{
                      if($RS_c_c->fields['activation_status'] == 0)
                      {
                        /* $Sql = "SELECT * FROM activation_codes WHERE campaign_id=".$_REQUEST['campaign_id'];
						$RS_1 = $objDB->Conn->Execute($Sql); */
						$RS_1 = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE campaign_id=?",array($_REQUEST['campaign_id']));
						
				if($RS_1->RecordCount()>0){
					
					/* $location_max_sql = "Select num_activation_code , offers_left , used_offers from campaign_location where  campaign_id=".$_REQUEST['campaign_id']." and location_id=".$_REQUEST['l_id'];
					$location_max = $objDB->Conn->Execute($location_max_sql); */
					$location_max = $objDB->Conn->Execute("Select num_activation_code , offers_left , used_offers from campaign_location where  campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
					
          $offers_left = $location_max->fields['offers_left'];
                    $used_campaign = $location_max->fields['used_offers'];
           $o_left = $location_max->fields['offers_left'];
           $share_flag = 1;
        $its_new_user =0;
//        echo $o_left."offer left =========";
//        exit;
           if(  $o_left>0)
                                      {
                if($RS_max_is_walkin->fields['new_customer'] == 1)
               {
                    //   $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=". $_REQUEST['l_id'];
                       /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where  location_id=". $_REQUEST['l_id']." and customer_id=".$_SESSION['customer_id'].") ";
                       $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                       $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where  location_id=? and customer_id=?)",array($_REQUEST['l_id'],$_SESSION['customer_id']));
                       
                        if($subscibed_store_rs->RecordCount()==0)
                        {
                              $its_new_user =1;
                            $share_flag= 1;
                        }
                        else {
                              $its_new_user =0;
                              $share_flag= 0;
                        }
               }
                    
               /* check whether new customer for this store */
               $allow_for_reserve= 0;
               
               $is_new_user= 0;
               /*************** *************************/
				/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=". $_REQUEST['l_id'].") ";
				// echo $sql_chk."sql_chk<br/>";
				$Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
				$Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($_SESSION['customer_id'],$_REQUEST['l_id']));
				
                                            if($Rs_is_new_customer->RecordCount()==0)
                                            {
                                                $is_new_user= 1;
                                            }
                                            else {
                                                  $is_new_user= 0;
                                            }
                /*************** *************************/
                if($is_new_user == 1)
                {
                    $allow_for_reserve = 1;
                }
                else {
                    
                
				/* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_REQUEST['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_REQUEST['l_id'];
                // echo "<br/>===".$sql."===<br />";
                $RS_campaign_groups = $objDB->Conn->Execute($sql); */
                $RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
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
                                    $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
                                    $RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?))",array($_SESSION['customer_id'],$c_g_str));
                                    
                                      while($Row_Check_Cust_group = $RS_check_s->FetchRow())
                                       {
											/* $query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
											$RS_query = $objDB->Conn->Execute($query); */
											$RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?)",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id'],$c_g_str));

											 if($RS_query->RecordCount() > 0)
											 {

												  $is_it_in_group = 1;
											 }
                                       }
                                      if($is_it_in_group == 1 )
                                            { 
                                                  $allow_for_reserve = 1;  	
                                            }
                                           else {
                                                $allow_for_reserve = 0;
                                           }
                                             }
                                            else{
                                                $allow_for_reserve = 0;  	
                                            }
                                             }
                                             else{
                                       // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                          /* $query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$_REQUEST['l_id']." and mg.private =1 ) ";
                                          $RS_all_user_group = $objDB->Conn->Execute($query); */
                                          $RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =1 )",array($_SESSION['customer_id'],$_REQUEST['l_id']));
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
                                     $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$_REQUEST['l_id']."  )";
                                     $allow_for_reserve= 0;
                                }
                                }
                               // echo "<br />SQl_new===".$Sql_new_ ."=====<br />";
                       
                            /* for checking whether customer in campaign group */
             //  echo $allow_for_reserve."==== alllow for reserve";
             ///  exit;
               if($share_flag== 1)
               {
                      if($allow_for_reserve==1 || $its_new_user ==1){
								
								/* $Sql = "Update customer_campaigns set activation_status='1' where customer_id='".$_SESSION['customer_id']."' and campaign_id=".$_REQUEST['campaign_id']." and location_id=".$_REQUEST['l_id'];
								$objDB->Conn->Execute($Sql); */
								$objDBWrt->Conn->Execute("Update customer_campaigns set activation_status='1' where customer_id=? and campaign_id=? and location_id=?",array($_SESSION['customer_id'],$_REQUEST['campaign_id'],$_REQUEST['l_id']));
                                         // ///
                                     
                                /* $select_coupon_code = "update coupon_codes set active= 1 where customer_id=".$_SESSION['customer_id']." and customer_campaign_code=".$_REQUEST['campaign_id']." and  location_id=".$_REQUEST['l_id']."  ";
								$objDB->Conn->Execute($select_coupon_code); */
								$objDBWrt->Conn->Execute("update coupon_codes set active= 1 where customer_id=? and customer_campaign_code=? and  location_id=?",array($_SESSION['customer_id'],$_REQUEST['campaign_id'],$_REQUEST['l_id']));
								
								
                                      }
               }
                                      }          // ///
				}
                      }
                  }
			 //  }
                        
                            /* check for whether max sharing reached and user is firt time subscribed to this loaction*/
				//echo $share_flag."== share flag".$allow_for_reserve."===allow for reserve". $its_new_user."===its new user";
				
				/* $Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$_REQUEST['campaign_id'];
				$RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location); */
				$RS_max_no_location = $objDB->Conn->Execute("SELECT max_no_sharing from campaigns WHERE id=?",array($_REQUEST['campaign_id']));
				
		//	 $Sql_shared = "SELECT * from reward_user WHERE  campaign_id=".$_REQUEST['campaign_id']." and referred_customer_id<>0";
			 
			// echo $Sql_shared ."===<br/>";
			// echo base64_decode($_REQUEST['customer_id'])."==".$_REQUEST['campaign_id']."==".$_SESSION['customer_id']."==".$_REQUEST['l_id'];
			// $RS_shared = $objDB->Conn->Execute($Sql_shared);

			// if($RS_shared->RecordCount() < $RS_max_no_location->fields['max_no_sharing'] ){
                
                /* $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=". $_REQUEST['l_id'];
				// echo $sql_chk ."===<br/>";
                $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=?",array($_SESSION['customer_id'],$_REQUEST['l_id']));
                
                        if($allow_for_reserve==1 || $its_new_user ==1)
                        {
							$redeem_array = array();
							$redeem_array['customer_id'] =  base64_decode($_REQUEST['customer_id']);
							$redeem_array['campaign_id'] = $_REQUEST['campaign_id'];
							$redeem_array['earned_reward'] = 0;
							$redeem_array['referral_reward'] = 0;
							$redeem_array['referred_customer_id'] = $_SESSION['customer_id'];
							$redeem_array['reward_date'] =  date("Y-m-d H:i:s");
												$redeem_array['coupon_code_id'] =  0;
												$redeem_array['location_id'] =  $_REQUEST['l_id'];
							$objDB->Insert($redeem_array, "reward_user");
					
							
                        }

						
			// }
			
			
			 /* check whether maximum share reached / sharing count now 0 so send mail to merchant*/
			 
			 /* $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$_REQUEST['campaign_id']." and referred_customer_id<>0";
			 $RS_shared = $objDB->Conn->Execute($Sql_shared); */
			 $RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>0",array($_REQUEST['campaign_id']));
			 
		 	 /* $Sql_active = "SELECT active,offers_left from campaign_location WHERE campaign_id=".$_REQUEST['campaign_id']." and location_id=".$_REQUEST['l_id'];
			 $RS_active = $objDB->Conn->Execute($Sql_active); */
			 $RS_active = $objDB->Conn->Execute("SELECT active,offers_left from campaign_location WHERE campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
			 
			
			 if( $RS_active->fields['offers_left'] > 0 && $RS_active->fields['active']){
				 
				/* $Sql_created_by = "SELECT created_by from locations WHERE id=".$_REQUEST['l_id'];
			    $RS_created_by = $objDB->Conn->Execute($Sql_created_by); */
				$RS_created_by = $objDB->Conn->Execute("SELECT created_by from locations WHERE id=?",array($_REQUEST['l_id']));
				
				$merchantid = $RS_created_by->fields['created_by'];
				
				/* $Sql_merchant_detail = "SELECT * from merchant_user WHERE id=".$merchantid;   
				$RS_merchant_detail = $objDB->Conn->Execute($Sql_merchant_detail); */
				$RS_merchant_detail = $objDB->Conn->Execute("SELECT * from merchant_user WHERE id=?",array($merchantid));
				
				/* $Sql_campaigns_detail = "SELECT * from campaigns WHERE id=".$_REQUEST['campaign_id'];
				$RS_campaigns_detail = $objDB->Conn->Execute($Sql_campaigns_detail); */
				$RS_campaigns_detail = $objDB->Conn->Execute("SELECT * from campaigns WHERE id=?",array($_REQUEST['campaign_id']));
				
				
				
				$mail = new PHPMailer();
				
				
				
				$merchant_id=$RS_merchant_detail->fields['id'];
				//$email_address=$RS_merchant_detail->fields['email'];
				$email_address="test.test1397@gmail.com";
			
				$body="<div>Hello,<span style='font-weight:bold'>".$RS_merchant_detail->fields['firstname']." ".$RS_merchant_detail->fields['lastname']."</span></div>";
				$body.="<br>";
				$body.="<div>Congratulations! Sharing points for <span style='font-weight:bold'>".$RS_campaigns_detail->fields['title']."</span> were allocated for new customer referral. 
				Please <a herf='".WEB_PATH."/merchant/register.php' > log in </a> if you wish to increase number of referral customers limit for your campaign  . </div>";
			        $body.="<br>";
				$body.="<div>Sincerely,</div>";
				$body.="<div>Scanflip Support Team</div>";
                                				
				$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
				
				$mail->AddAddress($email_address);
				
				$mail->From = "no-reply@scanflip.com";
				$mail->FromName = "ScanFlip Support";
				$mail->Subject    = "Scanflip offer - ".$RS_campaigns_detail->fields['title'];
				$mail->MsgHTML($body);
				$mail->Send();
			 }
			
			
			
			
			 /*  */
                          //make entry in subscribe strore
//                          $Sql = "SELECT * FROM subscribed_stores where customer_id= ".base64_decode($_REQUEST['customer_id'])." and location_id=".$_REQUEST['l_id'];
//                       //   echo "1==".$Sql."<br/>";
//                         $RS_all_sub_cust_single = $objDB->Conn->Execute($Sql);
//                         if($RS_all_sub_cust_single->RecordCount()==0)
//                        {
//                        $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ". base64_decode($_REQUEST['customer_id'])." ,location_id=".$_REQUEST['l_id']." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
//                          $objDB->Conn->Execute($insert_subscribed_store_sql);
//                        }
//                        else {
//                            if($subscibed_store_rs->fields['subscribed_status']==0)
//                            {
//                                $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ". base64_decode($_REQUEST['customer_id'])." and location_id=".$_REQUEST['l_id'];
//                                 $objDB->Conn->Execute($up_subscribed_store);
//                            }
//                        }
                          /* chaeck whether user in location private group or not */
//                        $Sql = "SELECT * FROM merchant_subscribs WHERE user_id=".base64_decode($_REQUEST['customer_id'])." and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=".$_REQUEST['l_id']."  )";
//                     //    echo "2==".$Sql."<br/>";
//                         $RS_new = $objDB->Conn->Execute($Sql);
//                             if($RS_new->RecordCount()<=0){
//                                     $sql_group = "select id , merchant_id from merchant_groups where location_id =".$_REQUEST['l_id']." and private = 1";
//                                     $RS_group = $objDB->Conn->Execute($sql_group);
//                                 $array_group= array();
//                                         $array_group['merchant_id']=$RS_group->fields['merchant_id'];
//                                         $array_group['group_id']=$RS_group->fields['id'];
//                                         $array_group['user_id']= base64_decode($_REQUEST['customer_id']);
//                                         $objDB->Insert($array_group, "merchant_subscribs");
//                             }
                     /* */
                         //
                      //    echo "3==<br/>";
                            //Make entry in subscribed_stre div for first time subscribe to loaction
                            
						/* $Sql = "SELECT * FROM subscribed_stores where customer_id= ". base64_decode($_REQUEST['customer_id']);
                        $RS_all_sub_cust = $objDB->Conn->Execute($Sql); */
						$RS_all_sub_cust = $objDB->Conn->Execute("SELECT * FROM subscribed_stores where customer_id= ?",array($_REQUEST['customer_id']));
                        
                               //Make entry in subscribed_stre div for first time subscribe to loaction
                        /* $sql_group = "select id , merchant_id from merchant_groups where location_id =". $_REQUEST['l_id']." and private = 1";
                        $RS_group = $objDB->Conn->Execute($sql_group); */
						$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($_REQUEST['l_id']));
                  
                        /* $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$_REQUEST['l_id']; 
                        $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
						$subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=?",array($_SESSION['customer_id'],$_REQUEST['l_id']));
                        
                        if($subscibed_store_rs->RecordCount()==0)
                        {
							/* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$_REQUEST['l_id']." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
							$objDB->Conn->Execute($insert_subscribed_store_sql); */
							$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id= ?,location_id=?,subscribed_date=?,subscribed_status=1",array($_SESSION['customer_id'],$_REQUEST['l_id'],date('Y-m-d H:i:s')));
							
                        }
                        else
                        {
                            if($subscibed_store_rs->fields['subscribed_status']==0)
                            {
								/* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$_REQUEST['l_id'];
                                $objDB->Conn->Execute($up_subscribed_store); */
                                $objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id= ? and location_id=?",array($_SESSION['customer_id'],$_REQUEST['l_id']));
                                
                            }
                        }
                        //   
                 
			 /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list  */
			/* $Sql = "SELECT * FROM merchant_subscribs WHERE user_id=".$_SESSION['customer_id']." and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=".$_REQUEST['l_id']."  )";
			$RS_new = $objDB->Conn->Execute($Sql); */
			$RS_new = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=?)",array($_SESSION['customer_id'],$_REQUEST['l_id']));
			
                             if($RS_new->RecordCount()<=0){
								 
                                     /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$_REQUEST['l_id']." and private = 1";
                                     $RS_group = $objDB->Conn->Execute($sql_group); */
                                     $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($_REQUEST['l_id']));
                                     
                                 $array_group= array();
                                         $array_group['merchant_id']=$RS_group->fields['merchant_id'];
                                         $array_group['group_id']=$RS_group->fields['id'];
                                         $array_group['user_id']= $_SESSION['customer_id'];
                                         $objDB->Insert($array_group, "merchant_subscribs");
                             }
                              //   
                 
							
						}
						
						/* $redirect_query = "select permalink from campaign_location where campaign_id=".$_REQUEST['campaign_id']." and location_id=".$_REQUEST['l_id'];
						$redirect_RS = $objDB->Conn->Execute($redirect_query ); */
						$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
						
						//$json_array['permalink'] =  $redirect_RS->fields['permalink'];
                        //original code
						
						//header("Location:".$redirect_RS->fields['permalink']);
						
						//Code By Bhumin===
						//Popup Open in ios When Redirect From facebook share post
						?>
					
						<script type="text/javascript">
							


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

						

						if( isMobile.iOS() ) 
						{
						//alert('in iOS');
						//if('<?= $q ?>' != "")
						//{
						//alert('in iOS1');
						$(".cantent").css("display", "none");
						$(document).ready(function(){
							//alert('in iOS 2');
							//$('.item .delete').click(function(){
								
								var elem = $(this).closest('.item');
								
								$.confirm({
									'title'		: 'Use Scanflip Application?',
									'message'	: 'You Use our Application. <br />Experience! Continue?',
									'buttons'	: {
										'Open with App'	: {
											'class'	: 'blue',
											'action': function(){
											 setTimeout(function () { window.location = '<?php echo $client_msg["index"]["iphone_app_link"];?>'; }, 25);
											
											window.location = 'scanflip:/<?php echo $_SERVER["REQUEST_URI"]; ?>';
												
											}
										},
										'Open in Browser'	: {
											'class'	: 'gray',
											'action': function(){
												var newLocation = "<?php echo $redirect_RS->fields['permalink']; ?>";
												window.location = newLocation;
											  //header("Location:".$redirect_RS->fields['permalink']);
											}	// Nothing to do in this case. You can as well omit the action property.
										},
										'Cancel'	: {
											'class'	: 'gray',
											'action': function(){
											var newLocation = "<?php echo $redirect_RS->fields['permalink']; ?>";
												window.location = newLocation;
											}	// Nothing to do in this case. You can as well omit the action property.
										}
									}
								});
								
							//});
							
						});


						(function($){
							
							$.confirm = function(params){
								
								if($('#confirmOverlay').length){
									// A confirm is already shown on the page:
									return false;
								}
								
								var buttonHTML = '';
								$.each(params.buttons,function(name,obj){
									
									// Generating the markup for the buttons:
									
									buttonHTML += '<a href="#" class="button '+obj['class']+'">'+name+'<span></span></a>';
									
									if(!obj.action){
										obj.action = function(){};
									}
								});
								
								var markup = [
									'<div id="confirmOverlay">',
									'<div id="confirmBox">',
												
									'<div id="confirmButtons">',
									buttonHTML,
									'</div></div></div>'
								].join('');
								
								$(markup).hide().appendTo('body').fadeIn();
								
								var buttons = $('#confirmBox .button'),
									i = 0;

								$.each(params.buttons,function(name,obj){
									buttons.eq(i++).click(function(){
										
										// Calling the action attribute when a
										// click occurs, and hiding the confirm.
										
										obj.action();
										$.confirm.hide();
										return false;
									});
								});
							}

							$.confirm.hide = function(){
								$('#confirmOverlay').fadeOut(function(){
									$(this).remove();
								});
							}
							
						})(jQuery);

						
						//}	
							
						}
						else
						{	  
							
												$("body").css("display", "none");
												window.location = "<?php echo $redirect_RS->fields['permalink']; ?>";
												
												/* var newLocation = "<?php echo $redirect_RS->fields['permalink']; ?>";
												window.location = newLocation; */
							
						}



						</script>
					
					<?php 	
                    
					}
                    else{
                    		$cookie_time = time()+60*60*24*30;
							setcookie("share","true",$cookie_time,'','','');
							setcookie("campaign_id",$_REQUEST['campaign_id'],$cookie_time,'','','');
							setcookie("l_id",$_REQUEST['l_id'],$cookie_time,'','','');
							setcookie("customer_id",$_REQUEST['customer_id'],$cookie_time,'','','');
                    }
                }
                else
                {
                    if(isset($_REQUEST['campaign_id']))
                    {
                        $c_s = $_REQUEST['campaign_id'];
                    }
                    else{
                        $c_s = "";
                    }
                    if(isset($_REQUEST['l_id']))
                    {
                        $l_s = $_REQUEST['l_id'];
                    }
                    else{
                        $l_s = "";
                    }
                    if(isset($_REQUEST['customer_id']))
                    {
                        $cust_s = $_REQUEST['customer_id'];
                    }
                    else{
                        $cust_s = "";
                    }
                   $cookie_time = time();
                   setcookie("share","true",$cookie_time-3600,'','','');
				   setcookie("campaign_id",$c_s,$cookie_time-3600,'','','');
                   setcookie("l_id",$l_s,$cookie_time-3600,'','','');
                   setcookie("customer_id",$cust_s,$cookie_time-3600,'','','');
                }

                ?>

			<div class="ordiv">
			<p>OR </p>
			</div>
	
            <form action="process.php" method="post" id="reg_form" >
				<input type="hidden" name="medium" id="medium" class="medium" value="" />
				<input type="hidden" name="hdn_user_datetime" id="hdn_user_datetime" class="hdn_user_datetime" value="" />
				<input type="hidden" name="domain" id="domain" value="<?php	if(isset($_REQUEST['domain'])){echo $_REQUEST['domain'];}else{echo "";}?>" />
                <?php
                if(isset($_REQUEST['campaign_id']) && isset($_REQUEST['share']))
                {
                    $cookie_time = time()+60*60*24*30;
                   setcookie("share","true",$cookie_time,'','','');
		   setcookie("campaign_id",$_REQUEST['campaign_id'],$cookie_time,'','','');
                   setcookie("l_id",$_REQUEST['l_id'],$cookie_time,'','','');
                   setcookie("customer_id",$_REQUEST['customer_id'],$cookie_time,'','','');
                }
                ?>
				<div class="reg_form_title">
					<?php echo $client_msg["login_register"]["label_Register"];?>
				</div>
				<div class="regir-form">
				  <div>
				    <div colspan="2" align="center" id="error_div"><?php if(isset($_REQUEST['rmsg'])){
                                        echo $_REQUEST['rmsg'];
                                    }
?></div>
			      </div>
				
					
					<div  class="in-text-put">
					<div class="emails"><?php echo $client_msg["login_register"]["Field_email"];?></div>
						<input type="text" name="emailaddress" id="emailaddress" />
					</div>
				  
				 
					
					<div class="in-text-put">
					<div class="emails"><?php echo $client_msg["login_register"]["Field_password"];?></div>
						<input autocomplete="off" type="password" name="password" id="password" />
                                                <span id="result"></span>
					</div>
				
				
					
					<div class="in-text-put">
					<div class="emails"><?php echo $client_msg["login_register"]["Field_Confirm_password"];?></div>
						<input autocomplete="off" type="password" name="con_password" id="con_password" />
                                                <span id="result_con"></span>
					</div>
				
				  
                 
					
					<div class="in-text-put" >
					<div class="emails"><?php echo $client_msg["login_register"]["Field_captcha"];?></div>
						<input type="text" id="mycaptcha" name="mycaptcha" /><br/>
						<table>
						  <tr>
						    <td>
						      <!--<img id="captcha_image_src" src="captcha.gif" style="display:block"/>-->
						      <img src="get_captcha_c_r.php" alt="" id="captcha" />
						    </td>
						    <td>
						      <a id="captcha_image" href="javascript:void(0)"><?php echo $client_msg["login_register"]["label_captcha_different"];?></a>
							  
						    </td>
							<td>
								<img id="captcha_ajax_loading" style="display:none" src="<?php echo ASSETS_IMG;?>/c/ajax-loader-1.gif"/>
							</td>
						  </tr>
						  
						</table>
						
						
					</div>

				  
				  <div>
					<div align="right" class="scan_term_left"> </div>
					<div align="left" class="scan_term_right">
			  
                  <div class="in-text-put">    
				  <div class="check-box">  <input type="checkbox" name="agree" id="agree" />  </div>
                  <?php echo $client_msg["login_register"]["label_I_Agree"];?><a href="<?=WEB_PATH?>/terms.php" target="_blank"> <?php echo $client_msg["login_register"]["label_Terms_Services"];?></a> <?php echo $client_msg["login_register"]["label_And"];?> <a href="<?=WEB_PATH?>/privacy-assist.php" target="_blank"><?php echo $client_msg["login_register"]["label_Privacy_Policy"];?></a>
                                                           
                                                    </div>
                                              
					</div>
				  </div>
				  
				<div class="registerdiv-regi">  
				 <?php if(isset($_REQUEST['location_id'])){
                                    $locationid_s = $_REQUEST['location_id'];
                                     } else{
                                         $locationid_s ="";
                                     }
                                     if(isset($_REQUEST['url'])){
                                    $url_s = $_REQUEST['url'];
								//$url_s = str_replace("?purl=", "#", $_REQUEST['url']);
								$url_s =  $f_url;
                                     } else{
                                         $url_s ="";
                                     }
		 if(isset($_REQUEST['url']))
         {
             $url1 = $_REQUEST['url'];
    $parts1 = parse_url($url1);
    //if(isset($parts1['query']))
    //{
		parse_str($parts1['query'], $query);
	//}
    //echo $query['purl'];
	$url_arr = explode("?",$url1);
	//if(isset($query['purl']))
	//{
		if($query['purl'] != "")
		{

			//echo f_url1."===";
			$v = trim($query['purl'],"@");
			$arr = explode("|",$v);
			if( $arr[0] != 0)
			
			{
				if(isset($_SESSION['is_location']))
				{
					$k = $arr[0]."#".$arr[1];
					echo '<input type="hidden" name="hdn_reload_pre_selection_loc" id="hdn_reload_pre_selection_loc" value="'.$k .'"/>';
				}
				else
				{
					$k = $arr[0]."#".$arr[1];
					echo '<input type="hidden" name="hdn_reload_pre_selection" id="hdn_reload_pre_selection" value="'.$k .'"/>';
				}
			}
			else{
				$url_s = str_replace("?purl=", "#", $_REQUEST['url']);
										$url_s =  $f_url;
			}
		}
		else
		{
			//$f_url1 = $_SESSION['url_value'];
		}
	//}
	//exit;

			
         }
                                    ?>
                                     
						<input type="hidden" name="location_id" value="<?=$locationid_s?>"/>	
                                               
                                                
						<input type="hidden" name="lurl" id="lurl" value="<?=$url_s?>"/>	
                                              
						<input type="submit" id="btnRegister" name="btnRegister" value="<?php echo $client_msg["login_register"]["Field_Register_button"];?>" onClick="">
					
				  </div>
				
				</form>
</div>
				

	<div class="logindiv" valign="top" >
			<div class="login_form_title">
					<?php echo $client_msg["login_register"]["label_Login"];?>
				</div>
	<form action="process.php" method="post"  id="login_frm">
		<input type="hidden" name="medium" id="medium" class="medium" value="" />
		<input type="hidden" name="hdn_user_datetime" id="hdn_user_datetime" class="hdn_user_datetime" value="" />
		<input type="hidden" name="domain" id="domain" value="<?php	if(isset($_REQUEST['domain'])){echo $_REQUEST['domain'];}else{echo "";}?>" />
					<div>
				  
				  <div class="in-text-put">
					<div class="emails"><?php echo $client_msg["login_register"]["Field_email"];?></div>
					<div>
                    <?php
                
                  // $_SESSION['active_user_emailid']="";
						$customer_email_id = "";
						if( isset($_SESSION['active_user_emailid']))
                                                {
                                                     $customer_email_id = $_SESSION['active_user_emailid'];
                                                }
                                                else
                                                {
                                                    if( isset( $_COOKIE['cookie_email_cust'] ) )
                                                    {
                                                        $customer_email_id = $_COOKIE['cookie_email_cust'];
                                                    }
                                                }
										
						?>
							
						<?php
							
						?>
								<input type="text" name="emailaddress" id="lemail" value="<?php echo $customer_email_id ?>" />
                        <?php
							$_SESSION['active_user_emailid']="";
						?>
					</div>
				  </div>
				  
				  <div class="in-text-put">
					<div class="emails"><?php echo $client_msg["login_register"]["Field_password"];?></div>
					<div >
                    <?php
						
							if( isset( $_COOKIE['cookie_password_cust'] ) )
							{			
						?>
								<input autocomplete="off" type="password" name="password" id="lpassword" value="<?php echo $_COOKIE['cookie_password_cust'] ?>" />
						<?php
							}
							else
							{
						?>
								<input autocomplete="off" type="password" name="password" id="lpassword" />
                        <?php
							}
						?>
					</div>
				  </div>
				 			
					<div class="check">
                    <?php
						
							if( isset( $_COOKIE['cookie_email_cust'] ) || isset( $_COOKIE['cookie_password_cust'] ) )
							{
						?>
                        		<input type="checkbox" name="keepme" id="keepme" checked /> 
                        <?php
							}
							else
							{			
						?>
								<input type="checkbox" name="keepme" id="keepme" /> 
                         <?php
							}

						 ?>
						<?php echo $client_msg["login_register"]["label_Keep_Me"];?>
					</div>
				  
				 
				  
				<div class="login-regi" >
                                   <?php if(isset($_REQUEST['location_id'])){
                                    $locationid_s = $_REQUEST['location_id'];
                                     } else{
                                         $locationid_s ="";
                                     }
                                     if(isset($_REQUEST['url'])){
                                    $url_s = $_REQUEST['url'];
									//$url_s = str_replace("?purl=", "#", $_REQUEST['url']);
									$url_s =  $f_url;
                                     } else{
                                         $url_s ="";
                                     }
                                  if(isset($_REQUEST['url']))
         {
             $url1 = $_REQUEST['url'];
    $parts1 = parse_url($url1);
    //if(isset($parts1['query']))
	//{
		parse_str($parts1['query'], $query);
	//}
    //echo $query['purl'];
	$url_arr = explode("?",$url1);
	//if(isset($query['purl']))
	//{
		if($query['purl'] != "")
		{

			//echo f_url1."===";
			$v = trim($query['purl'],"@");
			$arr = explode("|",$v);

			if($arr[0] != 0)
			{
				if(isset($_SESSION['is_location']))
				{
					$k = $arr[0]."#".$arr[1];
					echo '<input type="hidden" name="hdn_reload_pre_selection_loc" id="hdn_reload_pre_selection_loc" value="'.$k .'"/>';
				}
				else
				{
					$k = $arr[0]."#".$arr[1];
					echo '<input type="hidden" name="hdn_reload_pre_selection" id="hdn_reload_pre_selection" value="'.$k .'"/>';
				}
			}
			else{
				$url_s = str_replace("?purl=", "#", $_REQUEST['url']);
										$url_s =  $f_url;
			}
		}
		else
		{
			//$f_url1 = $_SESSION['url_value'];
		}
	//}
	//exit;

			
         }
                                    ?>
                                     
						<input type="hidden" name="location_id" value="<?=$locationid_s?>"/>	
                                               
                                                
						<input type="hidden" name="lurl" id="lurl" value="<?=$url_s?>"/>
						<input type="submit" id="btnLogin" name="btnLogin" value="<?php echo $client_msg["login_register"]["Field_login_button"];?>">
                         <a href="<?php echo WEB_PATH.'/login.php?request=forgot' ?>"  class="fancybox"><?php echo $client_msg["login_register"]["label_CanNot_Account"];?></a>
					</div>
				  
				   
				 <!-- <div class="scaflip_mer_image" style="display:none;">
						<a href="<?php //echo WEB_PATH."/merchant/register.php"; ?>">
						<img src="<?php //echo WEB_PATH.'/images/link-to-merchant.png';?>" /> 
						<img src="<?php //echo WEB_PATH.'/templates/images/logo.png';?>" /> 
						</a> 
				   </div> -->
				    <div class="fb-login" style="display:none">
					<a href="<?=$loginUrl?>">
						<img src="<?php echo ASSETS_IMG ?>/c/fb-login.jpg"  border="0" />
                                                <?php

                                                    
$client = new Google_Client();
$client->setApplicationName('Google+ PHP Starter Application');
// Visit https://code.google.com/apis/console?api=plus to generate your
// client id, client secret, and to register your redirect uri.
$client->setClientId('644860107311.apps.googleusercontent.com');
$client->setClientSecret('644860107311@developer.gserviceaccount.com');
$client->setRedirectUri("https://www.scanflip.com");
//$client->sediveveloperKey('AIzaSyAJODU6giwH4P8_S0XT0JQoHH4KOBVA7tc');
$plus = new Google_PlusService($client);



if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $activities = $plus->activities->listActivities('me', 'public');
  print 'Your Activities: <pre>' . print_r($activities, true) . '</pre>';

  // We're not done yet. Remember to update the cached access token.
  // Remember to replace $_SESSION with a real database or memcached.
  $_SESSION['token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
  print "<a href='$authUrl'>Connect Me!</a>";
}

                                                ?>
					</a>
					</div>
			     
					</div>
			  </form>
			  
			 

	
</div>

 

	</div><!--end of registerdiv-->
</div><!--end of left-side-->
</div><!--end of main-regi-->
</div> <!--end of contentContainer-->
<?php require_once(CUST_LAYOUT."/before-footer.php");?>
</div><!--end of my_main_div-->
</div><!--end of content-->

<?php require_once(CUST_LAYOUT."/footer.php");?>
		
    <div id="dialog-message" title="Message Box" style="display:none">

    </div>
</body>
</html>

<script type="text/javascript">
    jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
</script>
<?php

function check_campaign_criteria($campaign_id , $location_id)
{

$url_perameter_2 = rawurlencode("@".$campaign_id."|".$location_id."@");
$objDB = new DB();

	/* $Sql_campaign = "SELECT is_walkin , new_customer, level ,number_of_use  from campaigns WHERE id=".$campaign_id ;
	$RS_campaign = $objDB->Conn->Execute( $Sql_campaign); */
	$RS_campaign = $objDB->Conn->Execute("SELECT is_walkin , new_customer, level ,number_of_use  from campaigns WHERE id=?",array($campaign_id));
	

	/* $Sql_reserve = "SELECT * FROM customer_campaigns WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$campaign_id."' AND location_id =". $location_id;	
	$RS_is_reserve = $objDB->Conn->Execute( $Sql_reserve); */
	$RS_is_reserve = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?",array($_SESSION['customer_id'],$campaign_id,$location_id));

	/* $sql_redeem ="select * from coupon_redeem where coupon_id in
			( select id from coupon_codes where 
			customer_id=".$_SESSION['customer_id']." and location_id=". $location_id." and 
			customer_campaign_code =".$campaign_id .") ";	
	$RS_is_redeem = $objDB->Conn->Execute( $sql_redeem); */
	$RS_is_redeem = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in
			( select id from coupon_codes where 
			customer_id=? and location_id=? and 
			customer_campaign_code =?",array($_SESSION['customer_id'],$location_id,$campaign_id));
	
	if($RS_is_redeem->RecordCount() == 0)
	{
	
			if($RS_is_reserve->RecordCount() == 0)
			{
				/* $sql_subscribe ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$location_id." and subscribed_status=1";
				$RS_subscribe = $objDB->Conn->Execute($sql_subscribe); */
				$RS_subscribe = $objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=? and subscribed_status=1",array($_SESSION['customer_id'],$location_id));
				
				$redirecting_page = "search-deal.php#".$url_perameter_2;
				
			}
			else
			{
	
				$redirecting_page = "my-deals.php#".$url_perameter_2;
			}
	}	
	else{
	
		if($RS_campaign->fields['number_of_use'] == 1)
		{
		
			$redirecting_page = "search-deal.php";
		}
		else
		{
		
			$redirecting_page = "my-deals.php#".$url_perameter_2;
		}
	}
	
	return $redirecting_page;
	    
}

if(isset($_REQUEST['campaign_id']) && isset($_REQUEST['l_id']))
{
	/* $redirect_query = "select permalink from campaign_location where campaign_id=".$_REQUEST['campaign_id']." and location_id=".$_REQUEST['l_id'];
	$redirect_RS = $objDB->Conn->Execute($redirect_query ); */
	$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));
	
}

?>
<script>
/**  * jQuery.browser.mobile (http://detectmobilebrowser.com/)  * jQuery.browser.mobile will be true if the browser is a mobile device  **/ 
(function(a){jQuery.browser.mobile=/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);
if(jQuery.browser.mobile)
{
   //alert('You are using a mobile device!');
   jQuery("#medium").val("2");
   jQuery(".medium").val("2");
}
else
{
   //alert('You are not using a mobile device!');
   jQuery("#medium").val("1");
   jQuery(".medium").val("1");
}

var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        //return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        return navigator.userAgent.match(/iPhone/i);
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
	jQuery(".medium").val("2");
}

var userdatetime=formattedDate();
jQuery("#hdn_user_datetime").val(userdatetime);
jQuery(".hdn_user_datetime").val(userdatetime);


if( isMobile.iOS() ) 
{
//alert('in iOS');
//alert('<?php echo "a".$q."b"; ?>');
if('<?= $q ?>' != "")
{
//alert('in iOS1');
$(document).ready(function(){
	//alert('in iOS 2');
	//$('.item .delete').click(function(){
		
		var elem = $(this).closest('.item');
		
		$.confirm({
			'title'		: 'Use Scanflip Application?',
			'message'	: 'You Use our Application. <br />Experience! Continue?',
			'buttons'	: {
				'Open with App'	: {
					'class'	: 'blue',
					'action': function(){
					 setTimeout(function () { window.location = '<?php echo $client_msg["index"]["iphone_app_link"];?>'; }, 25);
					 
					
					window.location = 'scanflip:/<?php echo $_SERVER["REQUEST_URI"]; ?>'; 
						
					}
				},
				'Open in Browser'	: {
					'class'	: 'gray',
					'action': function(){
					window.location = "<?php if(isset($redirect_RS->fields['permalink'])){ echo $redirect_RS->fields['permalink'];} ?>";
					//window.location = '<?php echo $_SERVER["REQUEST_URI"]; ?>'; 
					}	// Nothing to do in this case. You can as well omit the action property.
				},
				'Cancel'	: {
					'class'	: 'gray',
					'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
				}
			}
		});
		
	//});
	
});


(function($){
	
	$.confirm = function(params){
		
		if($('#confirmOverlay').length){
			// A confirm is already shown on the page:
			return false;
		}
		
		var buttonHTML = '';
		$.each(params.buttons,function(name,obj){
			
			// Generating the markup for the buttons:
			
			buttonHTML += '<a href="#" class="button '+obj['class']+'">'+name+'<span></span></a>';
			
			if(!obj.action){
				obj.action = function(){};
			}
		});
		
		var markup = [
			'<div id="confirmOverlay">',
			'<div id="confirmBox">',
						
			'<div id="confirmButtons">',
			buttonHTML,
			'</div></div></div>'
		].join('');
		
		$(markup).hide().appendTo('body').fadeIn();
		
		var buttons = $('#confirmBox .button'),
			i = 0;

		$.each(params.buttons,function(name,obj){
			buttons.eq(i++).click(function(){
				
				// Calling the action attribute when a
				// click occurs, and hiding the confirm.
				
				obj.action();
				$.confirm.hide();
				return false;
			});
		});
	}

	$.confirm.hide = function(){
		$('#confirmOverlay').fadeOut(function(){
			$(this).remove();
		});
	}
	
})(jQuery);

}
	
	
}
else
{	  
	//alert('You Are Using Desktop');  
	
}

</script>
<?php
function share_after_login_fdb_gp($campaign_id , $location_id , $domain , $customer_id)
{
	// 18-03-2014 pageview counter logic
	
	$objDB = new DB();
	$objJSON = new JSON();
	$cid= $_SESSION['customer_id'];
	$array_loc=array();
	$array_loc['id'] = $location_id;
	$RS_location = $objDB->Show("locations",$array_loc);
	$time_zone=$RS_location->fields['timezone_name'];

	$pageview_array = array();
	$pageview_array['campaign_id'] = $campaign_id;
	$pageview_array['location_id'] = $location_id;;
	$pageview_array['pageview_domain'] = $domain;
	$pageview_array['pageview_medium'] = 1;
	$pageview_array['timestamp'] = $_REQUEST['hdn_user_datetime'];
	$objDB->Insert($pageview_array, "pageview_counter");
	$pageview_array = array();
			
	// 18-03-2014 pageview counter logic
							
	$json_array['share'] = "true";
	$json_array['c_id'] = $campaign_id;
	$json_array['l_id'] = $location_id;

	/* $redirect_query = "select permalink from campaign_location where campaign_id=".$campaign_id." and location_id=".$location_id;
	$redirect_RS = $objDB->Conn->Execute($redirect_query ); */
	$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$location_id));
	
	$json_array['permalink'] =  $redirect_RS->fields['permalink'];

	/*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list */
       
	/* $Sql_max_is_walkin = "SELECT is_walkin , new_customer, level  from campaigns WHERE id=".$campaign_id;
	$RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin); */
	$RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin , new_customer, level  from campaigns WHERE id=?",array($campaign_id));

	/* $Sql_c_c = "SELECT * FROM customer_campaigns WHERE customer_id='".$cid."' AND campaign_id='".$campaign_id."' AND location_id =".$location_id;
	$RS_c_c = $objDB->Conn->Execute($Sql_c_c); */
	$RS_c_c = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?",array($cid,$campaign_id,$location_id));

	if($RS_c_c->RecordCount()<=0)
	{
		/* $Sql = "SELECT * FROM activation_codes WHERE campaign_id=".$campaign_id;
		$RS_1 = $objDB->Conn->Execute($Sql); */
		$RS_1 = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE campaign_id=?",array($campaign_id));
		
		if($RS_1->RecordCount()>0)
		{
			/* $location_max_sql = "Select num_activation_code , offers_left,used_offers from campaign_location where  campaign_id=".$campaign_id." and location_id=".$location_id;
			$location_max = $objDB->Conn->Execute($location_max_sql); */
			$location_max = $objDB->Conn->Execute("Select num_activation_code , offers_left , used_offers from campaign_location where  campaign_id=? and location_id=?",array($campaign_id,$location_id));
			
			$offers_left = $location_max->fields['offers_left'];
			$used_campaign = $location_max->fields['used_offers'];
			$o_left = $location_max->fields['offers_left'];
			$share_flag = 1;
			$its_new_user = 0;
			// RESERVE DEAL LOGIC
			if(  $o_left>0)
            {
				if($RS_max_is_walkin->fields['new_customer'] == 1)
				{
					/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=".  $location_id.")";
					$subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
					$subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id =? and location_id=?)",array($cid,$location_id));
					
					if($subscibed_store_rs->RecordCount()==0)
					{
						$its_new_user =1;
						$share_flag= 1;
					}
					else 
					{
						$its_new_user =0;
						$share_flag= 0;
					}
				}
               
                $allow_for_reserve= 0;
                $is_new_user= 0;
               
				/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=". $location_id.") ";
				$Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
				$Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($cid,$location_id));
				
				if($Rs_is_new_customer->RecordCount()==0)
				{
					$is_new_user= 1;
				}
				else
				{
					$is_new_user= 0;
				}
                //echo "=======".$is_new_user."=====<br/>";
                /*************** *************************/
                if($is_new_user == 1)
                {
                    $allow_for_reserve = 1;
                }
                else 
                {
                
					/* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$location_id;
					$RS_campaign_groups = $objDB->Conn->Execute($sql); */
					$RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$location_id));

					$c_g_str = "";
					$cnt =1;

					$is_it_in_group = 0;
					if($RS_max_is_walkin->fields['level']== 0)
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
                                /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$cid."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
                                $RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?))",array($cid,$c_g_str));
                                
								while($Row_Check_Cust_group = $RS_check_s->FetchRow())
								{
									/* $query = "Select * from merchant_subscribs where  user_id='".$cid."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
									$RS_query = $objDB->Conn->Execute($query); */
									$RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?)",array($cid,$Row_Check_Cust_group['group_id'],$c_g_str));
									
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
							/* $query = "Select * from merchant_subscribs where  user_id=".$cid." and group_id=( select id from merchant_groups mg where mg.location_id=".$location_id." and mg.private =1 ) ";
							$RS_all_user_group = $objDB->Conn->Execute($query); */
							$RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =1 )",array($cid,$location_id));
							
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
						$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$cid."' AND group_id in( select  id from merchant_groups where location_id  =".$location_id."  )";
						$allow_for_reserve= 1;
                    }
				}
                             
                       
                /* for checking whether customer in campaign group */
                        
                           
                /* check whether new customer for this store */

               if($share_flag== 1)
               {
					if($allow_for_reserve==1 ||  $its_new_user ==1)
					{
						$activation_code = $RS_1->fields['activation_code'];
						
						/* $Sql = "INSERT INTO customer_campaigns SET  activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
						customer_id='".$cid."', campaign_id=".$campaign_id." , location_id=".$location_id;
						$objDB->Conn->Execute($Sql); */
						$objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
									customer_id=?, campaign_id=?, location_id=?",array($cid,$campaign_id,$location_id));
									
						/* $RSLocation_nm  =  $objDB->Conn->Execute("select * from locations where id =".$location_id); */
						$RSLocation_nm = $objDB->Conn->Execute("select * from locations where id =?",array($location_id));
						
						//$br = $cid.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$location_id;
						$br = $objJSON->generate_voucher_code($cid,$activation_code,$campaign_id,$RSLocation_nm->fields['location_name'],$location_id);
						/* $insert_coupon_code = "Insert into coupon_codes set customer_id=".$cid." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$location_id." , generated_date='".date('Y-m-d H:i:s')."' ";
						$objDB->Conn->Execute($insert_coupon_code); */
						$objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=?, customer_campaign_code=?, coupon_code=?, active=1 , location_id=?, generated_date=?",array($cid,$campaign_id,$br,$location_id,date('Y-m-d H:i:s')));

						/* $update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$location_id." ";
						$objDB->Conn->Execute($update_num_activation); */
						$objDBWrt->Conn->Execute("Update  campaign_location set offers_left=?, used_offers=? where campaign_id=? and location_id =?",array(($offers_left-1),($used_campaign+1),$campaign_id,$location_id));
						
                   }
               }
					
                                        // ///
			}
			// END OF RESERVE DEAL LOGIC
		}
				
	}
			
	/*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list  */

	/* check for whether max sharing reached and user is firt time subscribed to this loaction*/
	
	/* $Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$campaign_id;
	$RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location); */
	$RS_max_no_location = $objDB->Conn->Execute("SELECT max_no_sharing from campaigns WHERE id=?",array($campaign_id));
	
	$Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$campaign_id." and referred_customer_id<>0";

	/* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=".  $location_id.")";
	$subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
	$subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($cid,$location_id));
	
	if($allow_for_reserve==1 || $its_new_user ==1)
	{
		$redeem_array = array();
		$redeem_array['customer_id'] =  base64_decode($customer_id);//$_SESSION['customer_id'];
		$redeem_array['campaign_id'] = $campaign_id;
		$redeem_array['earned_reward'] = 0;
		$redeem_array['referral_reward'] = 0;
		$redeem_array['referred_customer_id'] = $cid;
		$redeem_array['reward_date'] =  date("Y-m-d H:i:s");
		$redeem_array['coupon_code_id'] =  0;
		$redeem_array['location_id'] =   $location_id;
		$objDB->Insert($redeem_array, "reward_user");
	}
		 			 
	/* check whether maximum share reached / sharing count now 0 so send mail to merchant*/
			 
	/* $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$campaign_id." and referred_customer_id<>0";
	$RS_shared = $objDB->Conn->Execute($Sql_shared); */
	$RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>0",array($campaign_id));
	 
	/* $Sql_active = "SELECT active,offers_left from campaign_location WHERE campaign_id=".$campaign_id." and location_id=".$location_id;
	$RS_active = $objDB->Conn->Execute($Sql_active); */
	$RS_active = $objDB->Conn->Execute("SELECT active,offers_left from campaign_location WHERE campaign_id=? and location_id=?",array($campaign_id,$location_id));		 
			 
	if($RS_shared->RecordCount() <= $RS_max_no_location->fields['max_no_sharing'] && $RS_active->fields['offers_left'] > 0 && $RS_active->fields['active'])
	{			
		/* $Sql_created_by = "SELECT created_by from locations WHERE id=".$location_id;
		$RS_created_by = $objDB->Conn->Execute($Sql_created_by); */
		$RS_created_by = $objDB->Conn->Execute("SELECT created_by from locations WHERE id=?",array($location_id));
		
		$merchantid = $RS_created_by->fields['created_by'];

		/* $Sql_merchant_detail = "SELECT * from merchant_user WHERE id=".$merchantid;
		$RS_merchant_detail = $objDB->Conn->Execute($Sql_merchant_detail); */
		$RS_merchant_detail = $objDB->Conn->Execute("SELECT * from merchant_user WHERE id=?",array($merchantid));

		/* $Sql_campaigns_detail = "SELECT * from campaigns WHERE id=".$campaign_id;
		$RS_campaigns_detail = $objDB->Conn->Execute($Sql_campaigns_detail); */
		$RS_campaigns_detail = $objDB->Conn->Execute("SELECT * from campaigns WHERE id=?",array($campaign_id));
							
		$mail = new PHPMailer();

		$merchant_id=$RS_merchant_detail->fields['id'];
		$email_address=$RS_merchant_detail->fields['email'];
		//$email_address="test.test1397@gmail.com";
	
		$body="<div>Hello,<span style='font-weight:bold'>".$RS_merchant_detail->fields['firstname']." ".$RS_merchant_detail->fields['lastname']."</span></div>";
		$body.="<br>";
		$body.="<div>Congratulations! Sharing points for <span style='font-weight:bold'>".$RS_campaigns_detail->fields['title']."</span> were allocated for new customer referral. 
		Please <a herf='".WEB_PATH."/merchant/register.php' > log in </a> if you wish to update number of referral customers limit for your campaign  . </div>";
			$body.="<br>";
		$body.="<div>Sincerely,</div>";
		$body.="<div>Scanflip Support Team</div>";
										
		$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
		
		$mail->AddAddress($email_address);
		
		$mail->From = "no-reply@scanflip.com";
		$mail->FromName = "ScanFlip Support";
		$mail->Subject    = "Scanflip offer - ".$RS_campaigns_detail->fields['title'];
		$mail->MsgHTML($body);
		$mail->Send();

	 }

				   
	//Make entry in subscribed_stre table for first time subscribe to loaction
	/* $sql_group = "select id , merchant_id from merchant_groups where location_id =". $location_id." and private = 1";
	$RS_group = $objDB->Conn->Execute($sql_group); */
	$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($location_id));	  
			
	/* $sql_chk ="select * from subscribed_stores where customer_id= ".$cid." and location_id=".$location_id;
	$subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
	$subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=?",array($cid,$location_id));
	
	if($subscibed_store_rs->RecordCount()==0)
	{
		/* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$cid." ,location_id=".$location_id." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
		$objDB->Conn->Execute($insert_subscribed_store_sql); */
		$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id= ?,location_id=?,subscribed_date=?,subscribed_status=1",array($cid,$location_id,date('Y-m-d H:i:s')));
	}
	else 
	{
		if($subscibed_store_rs->fields['subscribed_status']==0)
		{
			/* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$cid." and location_id=".$location_id;
			$objDB->Conn->Execute($up_subscribed_store); */
			$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id= ? and location_id=?",array($cid,$location_id));
		}
	}
	
	/* check whether share user in stores's private group */
	
	/* $Sql = "SELECT * FROM merchant_subscribs WHERE user_id=".$cid." and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=".$location_id."  )";
	$RS_new = $objDB->Conn->Execute($Sql); */
	$RS_new = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=?)",array($cid,$location_id));
	if($RS_new->RecordCount()<=0)
	{
		/* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$location_id." and private = 1";
		$RS_group = $objDB->Conn->Execute($sql_group); */
		$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = 1",array($location_id));
		$array_group= array();
		$array_group['merchant_id']=$RS_group->fields['merchant_id'];
		$array_group['group_id']=$RS_group->fields['id'];
		$array_group['user_id']= $cid;
		$objDB->Insert($array_group, "merchant_subscribs");
	}	  
 
	$cookie_time = time();
	setcookie("share","true",$cookie_time-3600,'','','');
	setcookie("campaign_id",$_REQUEST['campaign_id'],$cookie_time-3600,'','','');
	setcookie("l_id",$_REQUEST['l_id'],$cookie_time-3600,'','','');
	setcookie("customer_id",-$_REQUEST['customer_id'],$cookie_time-3600,'','','');
	/* $redirect_query = "select permalink from campaign_location where campaign_id=".$campaign_id." and location_id=".$location_id;
	$redirect_RS = $objDB->Conn->Execute($redirect_query ); */
	$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=",array($campaign_id,$location_id));
	header("Location:".$redirect_RS->fields['permalink']);
				
 }
?>
