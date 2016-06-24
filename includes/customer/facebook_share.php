<?php
/******** 
@USE : campaign share on facebook
@PARAMETER : 
@RETURN : 
@USED IN PAGES : mymerchants.php, my-deals.php, location_detail.php, search-deal.php, campaign.php
*********/
//require_once("classes/Config.Inc.php");
//check_customer_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

require LIBRARY.'/fb-sdk/src/facebook.php';

/*$facebook = new Facebook(array(
 'appId'  => '654125114605525',
  'secret' => '2870b451a0e7d287f1899d0e401d3c4e',
));*/
include_once(LIBRARY."/fb-sdk/src/facebook_secret.php");

$facebook = new Facebook(array(
  'appId'  => facebookappId,
  'secret' => facebooksecret,
  
));

$result = mysql_query("select * from customer_user where id=".$_REQUEST['customer_id']);

 
while( $userData = mysql_fetch_array($result))
{     
	try
	{
		$permissions = $facebook->api("/".$userData['facebook_user_id']."/permissions");
        if (array_key_exists('publish_stream', $permissions['data'][0])) 
        {
			$attachment = array(    
				//'from' => array(
				//              'name' => 'scanflip',
				//            'id'   => '100006500324909'
				//      ),

				'name'    => 'ScanFlip | Powering Smart Savings from Local Merchants',

				'access_token' =>$userData['access_token'],
				'link' => $_REQUEST['th_link'].'&domain=1',
				//'description' => strip_tags($_REQUEST['summary']),
				'picture'=>$_REQUEST['imgsrc'],
				//'message'=>"Great deal from ".$_REQUEST['bussiness_name'].".".$_REQUEST['campaign_tag']." "
				'message'=>"#".$_REQUEST['campaign_title'].".. now available at ".$_REQUEST['bussiness_name']." participating locations.Limited Offers.. Reserve Now! ".$_REQUEST['campaign_tag']
			);
			$status = $facebook->api("/".$userData['facebook_user_id']."/feed", "post", $attachment);
			echo "success";
		}
		else
		{           
			//ehader("Location:{$facebook->getLoginUrl(array('scope' => 'publish_stream'))}");
			$params = array(
			'redirect_uri'=>$_REQUEST['redirect_url'],
			'scope'=>'publish_actions,publish_stream,read_stream,email'
			);
			echo "error"."|".$loginUrl = $facebook->getLoginUrl($params);
		}
    }
    catch(FacebookApiException $x )
    {
		//echo $x;
		
		echo "<pre>";
		print_r($x);
		echo "</pre>";
		exit();
        $error_type=  explode(":",$x);
        $params = array(
			'redirect_uri'=>$_REQUEST['redirect_url'],
			'scope'=>'publish_actions,publish_stream,read_stream,email'
		);
		$loginUrl = $facebook->getLoginUrl($params);
		echo $error_type[0]."|".$loginUrl;           
    }
}

//header("Location:http://localhost/scanflip/examples/send_to_all_user_of_facebook_user_interface.php");

?>
