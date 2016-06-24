<?php
/**
 * @uses merchant registration
 * @used in pages :forgot_password.php,logout-register.php,manage-customer.php,merchant-setup.php,process.php,header.php,upload_file.php,why-scanflip.php
 * @author Sangeeta Raghavani
 */

session_start();
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
//$objDBWrt = new DB('write');
//require '../fb-sdk/src/facebook.php';
//
//$facebook = new Facebook(array(
//  'appId'  => '415409831827914',
//  'secret' => 'bc354474f07701897511e0a165acf6c5',
//));

/*Facebook Login Code */

require LIBRARY.'/fb-sdk/src/facebook.php';

//include_once(LIBRARY."/fb-sdk/src/facebook_secret.php");


$facebook = new Facebook(array(
  'appId'  => facebookappId,
  'secret' => facebooksecret,
));


//setcookie('scanflip_customer_id', "", time()-3650,'','','');

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
	
	//$objUser->add_facebook_user($user_profile);
        
    $where_clause = $array_values = array();
        $array = $json_array = $where_clause = array();
	//$where_clause['emailaddress'] = $user_profile['email'];
	
      //  $RS = $objDB->Show("merchant_user", $where_clause);
	/*if($RS->RecordCount()>0){
		$Row = $RS->FetchRow();
		$_SESSION['customer_id'] = $Row['id'];
		$_SESSION['customer_info'] = $Row;
		$array_values['profile_pic'] = "https://graph.facebook.com/".$user."/picture";
		$where_clause['id'] = $Row['id'];
		$objDB->Update($array_values, "merchant_user", $where_clause);
		$_SESSION['facebook_usr_login'] = 1;
	}else{*/
	
	$_SESSION['facebook_usr_login'] = 1;
		if(isset($user_profile['email']))
		{
			//$array_values['email'] = $user_profile['email'];
		}
		else
		{
			
		}
		/*
		$array_values['firstname'] = $user_profile['first_name'];
		$array_values['lastname'] = $user_profile['last_name'];
		$array_values['created_date'] = date("Y-m-d H:i:s");
		$array_values['merchant_icon'] = "https://graph.facebook.com/".$user."/picture";
		*/
		$array_values['email'] = '';
		$array_values['firstname'] = '';
		$array_values['lastname'] = '';
		$array_values['created_date'] = date("Y-m-d H:i:s");
		$array_values['merchant_icon'] = '';
		
		$id = $objDBWrt->Insert($array_values, "merchant_user");
		
		$_SESSION['merchant_id'] = $id;
		$_SESSION['profile_complete'] = 0;
		$_SESSION['facebook_merchant_register'] = 1;
		$f_url1=WEB_PATH."/merchant/merchant-setup.php";
		header("Location:".$f_url1);
		exit();
		 
		
	//}

    
        
}

$params = array(
  'scope' => 'user_birthday, friends_birthday, user_location, friends_location, email,manage_pages'
  //'redirect_uri' => 'http://www.scanflip.com/search-deal.php'
);
$loginUrl = $facebook->getLoginUrl($params);


/*End Of login code */

/**
   * Attempts to find the closest timezone by coordinates
   *
   * @static
   * @param $lat
   * @param $lng
   */

//$array = array();
//$string_address = "sdasd,india";
//	$string_address = urlencode($string_address);
//	$geocode= file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".$string_address."&sensor=false");
//	$geojson= json_decode($geocode,true);
//	if($geojson['status']=='OK'){
//		$array['latitude'] = $geojson['results'][0]['geometry']['location']['lat'];
//		$array['longitude'] = $geojson['results'][0]['geometry']['location']['lng'];
//	}
//        print_r($array);
//$user = $facebook->getUser();
//if ($user) {
//  try {
//    $user_profile = $facebook->api('/me');
//  } catch (FacebookApiException $e) {
//    error_log($e);
//    $user = null;
//  }
//}
//
//if($user){
//	//echo "<pre>";
//	//print_r($user_profile);
//	//print_r($_SESSION);
//	//echo "</pre>";
//	//$objUser->add_facebook_user($user_profile);
//	
//	$where_clause = $array_values = array();
//	$where_clause['email'] = $user_profile['email'];
//	$RS = $objDB->Show("merchant_user", $where_clause);
//	if($RS->RecordCount()>0){
//		$Row = $RS->FetchRow();
//		$_SESSION['merchant_id'] = $Row['id'];
//		$_SESSION['merchant_info'] = $Row;
//	}else{
//		$array_values['email'] = $user_profile['email'];
//		$array_values['firstname'] = $user_profile['first_name'];
//		$array_values['lastname'] = $user_profile['last_name'];
//		$array_values['created_date'] = date("Y-m-d H:i:s");
//		$array_values['city'] = $user_profile['location']['name'];
//		$array_values['approve'] = "1";
//		$objDB->Insert($array_values, "merchant_user");
//		
//		$where_clause['email'] = $user_profile['email'];
//		$RS = $objDB->Show("merchant_user", $where_clause);
//		$Row = $RS->FetchRow();
//		$_SESSION['merchant_id'] = $Row['id'];
//		$_SESSION['merchant_info'] = $Row;
//	}
//	
//}
//if(isset($_SESSION['fb_480932418587835_user_id'])){
//	header("Location: my-account.php");
//	exit();
//}
//$params = array(
//  'scope' => 'user_birthday, friends_birthday, user_location, friends_location, email, user_interests'
//);
//$loginUrl = $facebook->getLoginUrl($params);

//echo md5("11111");
 $path_captcha=WEB_PATH."/merchant/captcha.php";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="content-type" content="text/html;charset=utf-8">

<title>ScanFlip Merchant | Register or Login</title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<!--<script type="text/javascript" src="https://code.jquery.com/jquery-1.6.2.min.js"></script>-->
<!-- load from CDN-->
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->

<script type="text/javascript" src="<?=ASSETS_JS?>/m/jquery.form.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/old_pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/new_pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/con_new_pass_strength.js"></script>

<script language="javascript">
function validate_register()
{
	var msg="";
	var flag="true";
	if(flag=="true")
	{
		if(document.getElementById("firstname").value == "")
		{
			//alert("Please Enter First Name");
			msg +="<div><?php echo $merchant_msg['login_register']['Msg_first_name']; ?></div>";
			flag="false";
			//document.getElementById("firstname").focus();
			//return false;
		}
		if(document.getElementById("lastname").value == ""){
			//alert("Please Enter Last Name");
			msg +="<div><?php echo $merchant_msg['login_register']['Msg_last_name']; ?></div>";
			//document.getElementById("lastname").focus();
			//return false;
			flag="false";
		}
		if(document.getElementById("business").value == ""){
			//alert("Please Enter Business Name");
			msg +="<div><?php echo $merchant_msg['login_register']['Msg_business_name']; ?></div>";
			//document.getElementById("business").focus();
			//return false;
			flag="false";
		}	
		if(email_validation(document.getElementById("email").value) == false){
			//alert("Please Enter Valid Email");
			msg +="<div><?php echo $merchant_msg['login_register']['Msg_valid_email']; ?></div>";
			//document.getElementById("email").focus();
			//return false;
			flag="false";
		}
		if(document.getElementById("code").value == ""){
			msg +="<div><?php echo $merchant_msg['login_register']['Msg_captcha']; ?></div>";
			flag="false";
		}
		else
		{
			var code=jQuery("#code").val();
			jQuery.ajax({
			   url:"process.php",
			   data:"captchacode_check_m_r=yes&code="+code,
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
						msg +="<div><?php echo $merchant_msg['login_register']['Msg_captcha']; ?></div>";
					} 
				}
			});	
		}  
	
		var head_msg="<div class='head_msg'>Message Box</div>"
		var content_msg="<div class='content_msg'>"+msg+"</div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		
		if(flag=="false")
		{
			jQuery.fancybox({
				content:jQuery('#dialog-message').html(),	
				type: 'html',				
				openSpeed  : 300,								
				closeSpeed  : 300,
				// topRatio: 0,				
				changeFade : 'fast',  				
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
			});
			return false;
		}
	}    
	return true;
}
function validate_login()
{
   var msg="";
  var flag="true";
   if(flag=="true")
  {
    
    if(email_validation(document.getElementById("lemail").value) == false)
    {
		//alert("Please Enter Valid Email");
		//document.getElementById("lemail").focus();
		//return false;
		msg +="<div><?php echo $merchant_msg['login_register']['Msg_valid_email']; ?></div>";
		flag="false";
    }
    if(document.getElementById("lpassword").value == "")
    {
		//alert("Please Enter Password");
		//document.getElementById("lpassword").focus();
		//return false;
		msg +="<div><?php echo $merchant_msg['login_register']['Msg_password']; ?></div>";
		flag="false";
    }
    var head_msg="<div class='head_msg'>Message Box</div>"
	var content_msg="<div class='content_msg'>"+msg+"</div>";
	var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
	jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
	
	if(flag=="false")
	{
	  
	
	    jQuery.fancybox({
				    content:jQuery('#dialog-message').html(),
				    
				    type: 'html',
				    
				    openSpeed  : 300,
				    
				    closeSpeed  : 300,
				    // topRatio: 0,
		    
				    changeFade : 'fast',  
				    
				    helpers: {
					    overlay: {
					    opacity: 0.3
					    } // overlay
				    }
	    });
	    return false;
	}
  }
    return true;
        
}
function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}
jQuery(document).ready(function() { 
    // bind form using ajaxForm
    
    jQuery('#reg_form').ajaxForm({ 
		beforeSubmit: validate_register,
        dataType:  'json', 
        success:   processRegJson 
    });
	jQuery('#login_frm').ajaxForm({
            beforeSubmit: validate_login,
        dataType:  'json', 
        success:   processLogJson 
    });
    jQuery('#country').change(function(){
		var change_value=this.value;
		if(change_value == "USA")
		{
			$("#state").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ' >NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");
			
		   
		}
		else
		{
			 $("#state").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
		}
    });	
	
	
});
function processRegJson(data) {
	//console.log(data);
	//return false; 
	if(data.status == "true"){
		/*
		jQuery("#firstname").val('');
		jQuery("#lastname").val('');
		jQuery("#business").val('');
		jQuery("#email").val('');
		jQuery("#phone_number").val('');
		jQuery("#address").val('');
		jQuery("#city").val('');
		jQuery("#zipcode").val('');
		jQuery("#mycaptcham").val('');
		
		
		var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
		var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+data.message+"</div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		jQuery.fancybox({
					content:jQuery('#dialog-message').html(),
					type: 'html',
					openSpeed  : 300,
					closeSpeed  : 300,
					changeFade : 'fast',  
					helpers: {
						overlay: {
						opacity: 0.3
						} // overlay
					}
		});
		*/
		window.location = data.url;
	}
	else
	{
		//alert(data.message);
		var msg=data.message;
		var head_msg="<div class='head_msg'>Message Box</div>"
	var content_msg="<div class='content_msg'>"+msg+"</div>";
	var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
	jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
  
	jQuery.fancybox({
				content:jQuery('#dialog-message').html(),
				
				type: 'html',
				
				openSpeed  : 300,
                                
				closeSpeed  : 300,
				// topRatio: 0,
                
                                changeFade : 'fast',  
				
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
        });
		
		
	}
     
}
function processLogJson(data) 
{ 
    //alert(data.status);
    //return false;
	if(data.status == "true")
	{
		var header_url="<?php if(isset($_REQUEST['url'])){echo $_REQUEST['url'];} ?>";
		//alert(header_url);
		if(header_url == "")
		{
			//alert("if");
			window.location = "<?php echo WEB_PATH?>/merchant/my-account.php";
		}
		else
		{
			//alert("else");
			window.location = decodeURIComponent(header_url);
		}
	}
	else if(data.status=="merchantsetup")
	{
		
		window.location = data.url;
	}
	else
    { 
        //alert(data.message);
		var msg=data.message;
		var head_msg="<div class='head_msg'>Message Box</div>"
		var content_msg="<div class='content_msg'>"+msg+"</div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
	  
		jQuery.fancybox({
			content:jQuery('#dialog-message').html(),					
			type: 'html',				
			openSpeed  : 300,									
			closeSpeed  : 300,
			// topRatio: 0,				
			changeFade : 'fast',  					
			helpers: {
				overlay: {
				opacity: 0.3
				} // overlay
			}
		});  
	}  
}

</script>

<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
</head>
<body>
  
  <div id="dialog-message" title="Message Box" style="display:none">

    </div>
<div >

<!---start header---->
	<div>
		<?php 
		require_once(MRCH_LAYOUT."/header.php");
		
		if(isset($_SESSION['profile_complete']))
		{
			if($_SESSION['profile_complete']==0)
			{
				header("Location:".WEB_PATH."/merchant/merchant-setup.php");
			}
			else
			{
				header("Location:".WEB_PATH."/merchant/my-account.php");
			}
		}
		?>
		<!--end header--></div>
	<div id="contentContainer">
		<div id="content">
<!--<p><img src="./templates/images/heading-login-register.jpg" alt="SCANFLIP" /></p>-->
	<div class="regster_facebook">
		<span class="regist">Register via</span> 
	<div class="cls_left"><a href="<?php echo $loginUrl;?>">
		<img src="<?php echo ASSETS_IMG.'/m/signinfb.png' ?>" />
	</a>
	</div></div>
	<div class="or">OR</div>
	<div class="register">Register</div>
	<div class="login">Login</div><br>
	

<p class="cls_left">
<span class="register_heading_left">Tell us about your business</span> 
<span class="register_heading_right">- All fields are required</span>
</p>
	<table class="reg_login"><tr>
	<td valign="top"  class="reg_login_width">

				<form action="process.php" method="post" id="reg_form">
				<table width="100%"  border="0" cellspacing="2" cellpadding="2">
				  <tr>
				    <td colspan="2" align="center" class="table_errore_message" id="error_div">
					<?php
					if(isset($_REQUEST['rmsg']))
					{
						echo $_REQUEST['rmsg'];
					}
					?>
					</td>
			      </tr>
                              
				  <tr>
					<td class="table_th_40"  align="right"><?php echo $merchant_msg['login_register']['Field_first_name']; ?></td>
					<td class="table_th_60" align="left">
						<input type="text" name="firstname" id="firstname" />
					</td>
				  </tr>
				  <tr>
					<td align="right"><?php echo $merchant_msg['login_register']['Field_last_name']; ?></td>
					<td align="left">
						<input type="text" name="lastname" id="lastname" />
					</td>
				  </tr>
                  <tr>
					<td align="right"><?php echo $merchant_msg['login_register']['Field_business_name']; ?></td>
					<td align="left">
						<input type="text" name="business" id="business" />
					</td>
				  </tr>
				  
				  
				  <tr>
					<td align="right"><?php echo $merchant_msg['login_register']['Field_email']; ?></td>
					<td align="left">
						<input type="text" name="email" id="email" />
					</td>
				  </tr>
				  
                                  <tr>
					<td align="right" class="vertically_top">Type the text shown in image : </td>
					<td align="left">
						<div class="captcha_textbox" >
						
							<!--<input type="text" id="mycaptcham" name="mycaptcham" style="width:122px;margin:0px;padding:6px 3px 6px;" />-->
							<input name="code" type="text" id="code">
							</div>
						<br/>
                        <div class="captcha_div">
							<!--<img id="captcha_image_src" src="captcha.gif" style="display:block;margin-top:-4px;" />-->
							<img src="get_captcha_m_r.php" alt="" id="captcha" />
						    <!--<img id="captcha_ajax_loading" style="display:none" src="<?php //echo WEB_PATH;?>/templates/images/ajax-loader-captcha.gif"/> -->
						</div>
						<div class="captch_image">
							<a id="captcha_image" href="javascript:void(0)"><?php echo $merchant_msg['popup_forgot']['msg_try_different_image'];?></a>
						</div>
					</td>
				  </tr>
				  
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<input type="submit" id="btnRegister" name="btnRegister" value="<?php echo $merchant_msg['login_register']['Field_register_button']; ?>" onClick=""  />
					</td>
				  </tr>
				</table>
				</form>
			</td>
	<!--<td><img src="./templates/images/heading-or.jpg" alt="SCANFLIP" /></td>-->
	<td valign="top" class="reg_login_width">
			<div class="login_main">
                <div class="signin">
                	<?php echo $merchant_msg['login_register']['Field_sign_in_with_email']; ?>
                </div>
                <form action="process.php" method="post" id="login_frm">
					<input type="hidden" id="redirecttab" name="redirecttab" value="<?php if(isset($_REQUEST['tab'])){ echo $_REQUEST['tab'];}else{echo "";}  ?>"/>
                    <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                      <tr>
                        <td colspan="2" align="center" class="table_errore_message">
						
						<?php
					if(isset($_REQUEST['lmsg']))
					{
						echo $_REQUEST['lmsg'];
					}
					?>
						</td>
                      </tr>
                      <tr>
                        <td>
                        	<div class="label_logi"><?php echo $merchant_msg['login_register']['Field_email']; ?></div>
                            <div class="input_logi">
                            	<?php
								/*
								$_COOKIE["a"]="123";
								$_COOKIE["b"]="456";
								$cookie_time =  time()+60*60*24*30;
								setcookie("c","789",$cookie_time,'','','');
								setcookie('d','222',$cookie_time,'','','');
								setcookie("e","e cookie 789",$cookie_time,'','','');
								setcookie('f','f cookie 222',$cookie_time,'','','');
								setcookie('g','g cookie 222',$cookie_time,'','','');
								echo $_COOKIE["a"];
								echo "<br/>";
								echo $_COOKIE["b"];
								echo "<br/>";
								echo $_COOKIE["c"];
								echo "<br/>";
								echo $_COOKIE["d"];
								echo "<br/>";
								echo $_COOKIE["e"];
								echo "<br/>";
								echo $_COOKIE["f"];
								print_r($_COOKIE);
								*/
								//print_r($_COOKIE);
								
									if( isset( $_COOKIE['cookie_email'] ) )
									{			
								?>
										<input type="text" name="lemail" id="lemail" value="<?php echo $_COOKIE['cookie_email'] ?>" />
								<?php
									}
									else
									{
								?>
										<input type="text" name="lemail" id="lemail" />
								<?php
									}
								?>
                            </div>
                        </td>
                      </tr>
                      
                      <tr>                        
                        <td>
                        	<div class="label_logi"><?php echo $merchant_msg['login_register']['Field_password']; ?></div>
                            <div class="input_logi">
                            	<?php
                            
									if( isset( $_COOKIE['cookie_password'] ) )
									{			
								?>
										<input autocomplete="off" type="password" name="lpassword" id="lpassword" value="<?php echo $_COOKIE['cookie_password'] ?>" />
								<?php
									}
									else
									{
								?>
										<input autocomplete="off" type="password" name="lpassword" id="lpassword" />
								<?php
									}
								?>
                            </div>
                        </td>
                      	</tr>
                       <tr>                       
                        <td class="chk_keep_me_login" >
                            <?php
                            
                                if( isset( $_COOKIE['cookie_email'] ) || isset( $_COOKIE['cookie_password'] ) )
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
                            <?php echo $merchant_msg['login_register']['Field_keep_me_signin']; ?>
                        </td>
                      </tr>                                  
                      <tr>
                        <td>
                            <input type="submit" name="btnLogin" value="<?php echo $merchant_msg['login_register']['Field_login_button']; ?>" class="login_but">
                            <!--<a class="cant_access" id="" href="<?php echo WEB_PATH.'/merchant/request_password.php' ?>" title="Forgot your Password">Can't access your account?</a>-->
			    <a class="cant_access" id="fancybox" href="<?php echo WEB_PATH.'/merchant/forgot.php' ?>"> <?php echo $merchant_msg['login_register']['Field_cant_access']; ?></a>
                        </td>
                      </tr>
                      <!--<tr>
                        <td colspan="2" align="center">
                        <a href="<?=$loginUrl?>">
                            <img src="../images/fb-login.jpg"  border="0" />
                        </a>
                        </td>
                      </tr>-->
                    </table>
                    </form>
                </div>
				
			</td>
		  </tr>
		</table>
		
		<div class="clear">&nbsp;</div>
<!--end of content--></div>
<!--end of contentContainer--></div>

<!---------start footer--------------->
       <div>
		<?php
		require_once(MRCH_LAYOUT."/footer.php");
		?>
		<!--end of footer--></div>
	
</div>
</body>
</html>




<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS?>/m/fancybox/jquery.fancybox.css" media="screen" />

<script type="text/javascript" src="<?=ASSETS_JS?>/m/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript">

  $(document).ready(function() {
    jQuery('#fancybox').fancybox({
				href: this.href,
				width: 400,
				height: 320,
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
    
    jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
	/*
	jQuery("#captcha_image").click(function(){
	     var captcha_path="<?php echo $path_captcha;?>";
	     
	   
	             jQuery.ajax({
			       url:captcha_path,
				//   data:"rand="+Math.random(),
			       //cache: false,
				   //async: true,
			       success: function(result){
					//alert("sucess");	
					
				//location.reload();
				
				//$s("#captcha_image_src").attr('src','captcha.gif');
				 
				 d = new Date();
				 jQuery("#captcha_image_src").attr('src','captcha.gif?'+d.getTime());
				
			     
				 }
			  });
		     
	    });	
		
		jQuery("#captcha_image").trigger("click");
		*/
		
		 jQuery('#captcha_image').click(function() {  
			
			change_captcha();
		 });
	 
	 function change_captcha()
	 {
	 	document.getElementById('captcha').src="get_captcha_m_r.php?rnd=" + Math.random();
	 }
	 
  });
</script>



