<?php
/******** 
@USE : to process facebook login
@PARAMETER : 
@RETURN : 
@USED IN PAGES : mymnerchants.php, popup_for_mymerchant.php, my-deals.php, location_detail.php, search-deal.php
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();
require LIBRARY.'/fb-sdk/src/facebook.php';
$facebook = new Facebook(array(
  'appId'  => facebookappId,
  'secret' => facebooksecret,
));

//echo $_COOKIE['redirecting_url_fb'];;
$user = $facebook->getUser();
if ($user) {
  try {
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

if($user){
	//echo "<pre>";
	//print_r($user_profile);
	//print_r($_SESSION);
	//echo "</pre>";
	
	//$objUser->add_facebook_user($user_profile);
	$where_clause = $array_values = array();
	$where_clause['emailaddress'] = $user_profile['email'];
	$RS = $objDB->Show("customer_user", $where_clause);
	
	if($RS->RecordCount()>0){
	  
		$Row = $RS->FetchRow();
		$_SESSION['customer_id'] = $Row['id'];
		$_SESSION['customer_info'] = $Row;
		if(isset($_REQUEST['redirect_url']))
		{
			if(isset($_COOKIE['redirecting_url_fb']))
			{
		//	echo "In if";
			$url1 = $_COOKIE['redirecting_url_fb'];
			$url_parts = explode("#",$url1);
			$v = trim($url_parts[1],"@");
			$arr = explode("|",$v);
			$f_url1 =check_campaign_criteria($arr[0] , $arr[1]);
			}
			else if(isset($_COOKIE['redirecting_url_fb_location']))
			{
			//echo "in else";
			$url1 = $_COOKIE['redirecting_url_fb_location'];
			$url_parts = explode("#",$url1);
			$v = trim($url_parts[1],"@");
			$arr = explode("|",$v);
			$redirect_query_location = "select location_permalink from locations where id=".$arr[1];
			//echo $redirect_query_location;
			 $RS_redirect_query = $objDB->Conn->Execute($redirect_query_location);		
			 $location_url = $RS_redirect_query->fields['location_permalink'];		
			$url_perameter_2 = rawurlencode("@".$arr[0]."|".$arr[1]."@");			 
			$f_url1 = $location_url."#".$url_perameter_2;
			//echo $f_url1;
			}
			else{
				$f_url1= $_REQUEST['redirect_url'];
			}
		?>
			  <script>
	//alert("1");
	//alert('<?php echo $f_url1 ?>1');
	window.location.href = '<?php echo $f_url1 ?>';
	</script>
		<?php }
		
	}else{
		$array_values['emailaddress'] = $user_profile['email'];
		$array_values['firstname'] = $user_profile['first_name'];
		$array_values['lastname'] = $user_profile['last_name'];
		$array_values['dob_year'] = date("Y", strtotime($user_profile['birthday']));
		$array_values['dob_month'] = date("m", strtotime($user_profile['birthday']));
		$array_values['dob_day'] = date("d", strtotime($user_profile['birthday']));
		$array_values['registered_date'] = date("Y-m-d H:i:s");
		$array_values['active'] = 1;
		$objDB->Insert($array_values, "customer_user");
		
		$where_clause['emailaddress'] = $user_profile['email'];
		$RS = $objDB->Show("customer_user", $where_clause);
		$Row = $RS->FetchRow();
		$_SESSION['customer_id'] = $Row['id'];
		$_SESSION['customer_info'] = $Row;
			if(isset($_REQUEST['redirect_url']))
		{
    		if(isset($_COOKIE['redirecting_url_fb']))
			{
			$url1 = $_COOKIE['redirecting_url_fb'];
			$url_parts = explode("#",$url1);
			$v = trim($url_parts[1],"@");
			$arr = explode("|",$v);
			$f_url1 =check_campaign_criteria($arr[0] , $arr[1]);
			}
			else if(isset($_COOKIE['redirecting_url_fb_location']))
			{
		//	echo "in if";
			$url1 = $_COOKIE['redirecting_url_fb_location'];
			$url_parts = explode("#",$url1);
			$v = trim($url_parts[1],"@");
			$arr = explode("|",$v);
			
			/* $redirect_query_location = "select location_permalink from locations where id=".$arr[1];
			$RS_redirect_query = $objDB->Conn->Execute($redirect_query_location); */
			$RS_redirect_query = $objDB->Conn->Execute("select location_permalink from locations where id=?",array($arr[1]));
			 		
			$location_url = $RS_redirect_query->fields['location_permalink'];		
			$url_perameter_2 = rawurlencode("@".$arr[0]."|".$arr[1]."@");			
//echo f_url1."====";
			$f_url1 = $location_url."#".$url_perameter_2;
			}
			else{
				$f_url1= $_REQUEST['redirect_url'];
			}
			
				?>
			  <script>
	//alert('<?php echo $f_url1; ?>2');
     window.location.href = '<?php echo $f_url1; ?>';
	</script>
		<?php }
	}
	
        ?>
       
      <script>
	  
//	alert('<?php echo $f_url1; ?>3');
	   window.location.href = '<?php echo $f_url1; ?>';
	</script>
      
     <?php
        
}
if(isset($_SESSION['fb_480932418587835_user_id'])){
	header("Location: my-account.php");
	exit();
}
$params = array(
  'scope' => 'user_birthday, friends_birthday, user_location, friends_location, email, user_interests'
  //'redirect_uri' => 'http://www.scanflip.com/search-deal.php'
);
 
  $loginUrl = $facebook->getLoginUrl($params);
?>
<script>
   function facebookurl()
   {
        //alert("<?php echo $loginUrl;?>");
	window.location.href = '<?php echo $loginUrl; ?>';
   }
</script>
<html>
  <head>
  </head>
  <body onload="facebookurl()">
  </body>
</html>
<?php
function check_campaign_criteria($campaign_id , $location_id)
{


$url_perameter_2 = rawurlencode("@".$campaign_id."|".$location_id."@");
$objDB = new DB();

	/* $Sql_campaign = "SELECT is_walkin , new_customer, level ,number_of_use  from campaigns WHERE
				id=".$campaign_id ;
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
			customer_campaign_code =?)",array($_SESSION['customer_id'],$location_id,$campaign_id));
			
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
?>
