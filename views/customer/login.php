<?php
/******** 
@USE : to login
@PARAMETER : 
@RETURN : 
@USED IN PAGES : mymerchants.php, popup_for_mymerchant.php, my-deals.php, register.php, location_detail.php, search-deal.php, campaign.php, header.php
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");

require_once LIBRARY.'/google-api-php-client/src/Google_Client.php';
require_once LIBRARY.'/google-api-php-client/src/contrib/Google_PlusService.php';
require_once LIBRARY.'/google-api-php-client/src/contrib/Google_Oauth2Service.php';

//$objDB = new DB(); 
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



//$_SESSION['code'] = $_REQUEST['code'];
if(isset($_REQUEST['page']))
{
    $page_name=$_REQUEST['page'];
	//$_COOKIE['page']=$page_name;
	$cookie_time = time()+60*60*24*30;
	setcookie('page', $page_name, $cookie_time,'','','');
}

  if(isset($_REQUEST['request']))
  {
  $request_action=$_REQUEST['request'];
  }
  $path_forgot_ajax=WEB_PATH."/validate_captcha.php";
  $url=WEB_PATH."/search-deal.php";
  require LIBRARY.'/fb-sdk/src/facebook.php';

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
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

if($user){
	
	//$objUser->add_facebook_user($user_profile);
	$where_clause = $array_values = array();
	$where_clause['emailaddress'] = $user_profile['email'];
	$RS = $objDB->Show("customer_user", $where_clause);
	if($RS->RecordCount()>0){
		$Row = $RS->FetchRow();
		
		$_SESSION['customer_id'] = $Row['id'];
		$_SESSION['customer_info'] = $Row;
		$_SESSION['facebook_usr_login'] = 1;
		//echo "123";
	}else{
		$_SESSION['facebook_usr_login'] = 1;
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
		
	}
	?>
	<script>
	var page_name="<?php if(isset($_REQUEST['page'])) echo $page_name;?>";
	
	 window.location.href = '<?php echo WEB_PATH ?>'+'/'+page_name;
	</script>
	<?php
     //header("Location:".WEB_PATH."/search-deal.php");
   
     
     
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
  
  $path_captcha=WEB_PATH."/captcha.php";
 ?>
 <html>
  <head>
    <link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/pass_strength.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/con_pass_strength.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/fancybox/jquery.fancybox.js"></script>
    <script>
       $(document).ready(function(){
		
	var request_action="<?php if(isset($request_action))echo $request_action;?>";
	  if(request_action=="forgot")
	  {
	    $(".forgotmainclass").show(); 
	      $(".mainloginclass").hide();
	      $("#btn_cancel_forgot").hide();
	  }
	
	   $(".textlink").click(function(){
			$(".fancybox-inner", window.parent.document).css("height","350px");
			$(".forgotmainclass").show(); 
			$(".mainloginclass").hide();
	   });
	   $("#btn_cancel_forgot").click(function(){
	       $(".mainloginclass").show();
	       $(".forgotmainclass").hide();
	    });
	   $('#login_frm').ajaxForm({ 
	       dataType:  'json', 
	       success:   processLogJson 
	   });
	   
	   $("#btnRequestPassword").click(function(){
		    var forgot_path="<?php echo $path_forgot_ajax; ?>";
		    var mycaptcha_rpc=$("#mycaptcha_rpc").val();
			//alert(mycaptcha_rpc);
		    var email=$("#email").val();
		    var return_value=validate_register();
		    if(return_value==false)
		    {
		    }
		    else
		    {
			  $.ajax({
			       url:forgot_path ,
			       data:'mycaptcha_rpc='+mycaptcha_rpc+'&email='+ email,
			       success: function(result){
				
				if(result == "error")
				{
				  
				  $(".errormainclass").show();
				  $(".forgotmainclass").hide();
				  $("#errorlabelid").html(email);
				 
				  
				}
				else if(result == "success")
				{
				  
				  $(".forgotmainclass").hide();
				  $(".successmainclass").show();
				  
				}
				else
				{
				    //$(".forgotmsgdiv").html(result); 
					$("#captchaerror").html(result); 
				}
				
				  
			       }
			  });
		    }
	    });
		/*
	   $("#captcha_image").click(function(){
	     var captcha_path="<?php echo $path_captcha;?>";
	
	    $("#captcha_ajax_loading").css("display","block");
	             $.ajax({
			       url:captcha_path ,
			       
			       success: function(result){
				
				//location.reload();
				 //$("#captcha_image_src").attr('src','captcha.gif');
				 $("#captcha_ajax_loading").css("display","none"); 
				 d = new Date();
				 $("#captcha_image_src").attr('src','captcha.gif?'+d.getTime());
			       }
			  });
		     
	    });
		
	  //$("#captcha_image").trigger("click");
	  d = new Date();
	  $("#captcha_image_src").attr('src','captcha.gif?'+d.getTime());
	  */
	   jQuery('#captcha_image').click(function() {  
			
			change_captcha();
		 });
	 
		 function change_captcha()
		 {
			document.getElementById('captcha').src="get_captcha_c_p.php?rnd=" + Math.random();
		 }
	   $("#btn_goback_error").click(function(){
	           $(".forgotmainclass").show();
		   
		   
		   $(".errormainclass").hide();
	   });
       });
       function closeFB() {
	    $.fancybox.close(); 
	}
function processLogJson(data) {
       
	//alert(data.c_id+"==="+data.l_id);
	var page_name="<?php if(isset($_REQUEST['page'])) echo $page_name;?>";
	//alert(page_name);
	path_url="<?php echo WEB_PATH;?>/";
	redirect_url= path_url + page_name;
	
	if(data.status == "true"){
            
	    //$(".mainloginclass").hide();
            //$(".popupmainclass").show();												
		window.top.location.href = redirect_url;
		//window.opener.reload();
             //window.close();
	     
                
	}else{
            jQuery('#msg_error').show();
           jQuery('#msg_error').html(data.message);
		//alert(data.message);
		return false;
	}
     
}

function validate_register(){
    
        if(email_validation(document.getElementById("email").value) == false && document.getElementById("mycaptcha_rpc").value == "")
            {
                jQuery("#emailerror").html("<?php echo $client_msg['login_register']['Msg_valid_email'];?>");
                jQuery("#captchaerror").html("<?php echo $client_msg['login_register']['Msg_Captcha_Not'];?>");
                document.getElementById("email").focus();
                error_var="false";
		return false;
            }
	else if(email_validation(document.getElementById("email").value) == false){
		//alert("Please Enter valid Email");
		jQuery("#emailerror").html("<?php echo $client_msg['login_register']['Msg_valid_email'];?>");
                document.getElementById("email").focus();
                error_var="false";
		return false;
	}
        else if(document.getElementById("mycaptcha_rpc").value == ""){
		//alert("Please Enter Captcha");
		jQuery("#captchaerror").html("<?php echo $client_msg['login_register']['Msg_Captcha_Not'];?>");
                document.getElementById("mycaptcha_rpc").focus();
		error_var="false";
                return false;
	}
        else
        {
            error_var="true";
            jQuery("#captchaerror").html("");
            jQuery("#emailerror").html("");
        }
	
	
}
function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}
    </script>
 <style type="text/css">
  .mainloginclass{
    padding-left:34px;
    padding-right:39px;
  }
    h2, .h2 {
            font-size: 1.6923em;
            font-weight: normal;
            line-height: 0.818em;
	    color:black !important;
        }
.unit100 {
    float: none;
    width: 91%;
}
.calltoaction {
    background-color: #FFFFFF;
    border: 1px solid #DCDDDE;
    margin: 0.769em 0;
    padding: 0.769em;
    text-align: center;
    width:84% !important;
}
#email-modal{
    -moz-border-bottom-colors:none;
    -moz-border-left-colors:none;
    -moz-border-right-colors:none;
    -moz-border-top-colors:none;
    background:none repeat scroll 0 0 #FFFFFF;
    border-image:none;
    border-left: 1px solid #C3C3C3;
    border-right: 1px solid #C3C3C3;
    border-top: 1px solid #C3C3C3;
    border-style: solid;
    border-width: 1px;
    display: inline-block;
    font-size: 1em;
    line-height: 1.385em;
    margin: 0 0 1.154em;
    padding: 0.231em;
}

#password{
    -moz-border-bottom-colors:none;
    -moz-border-left-colors:none;
    -moz-border-right-colors:none;
    -moz-border-top-colors:none;
    background:none repeat scroll 0 0 #FFFFFF;
    border-image:none;
    border-left: 1px solid #C3C3C3;
    border-right: 1px solid #C3C3C3;
    border-top: 1px solid #C3C3C3;
    border-style: solid;
    border-width: 1px;
    display: inline-block;
    font-size: 1em;
    line-height: 1.385em;
    margin: 0 0 1.154em;
    padding: 0.231em;
}
.mt20, .mv20, .ma20 {
    margin-top: 7px;
}
.left{
    float:left;
}
/*
.glyph_gplus:before {
    content: "g";
}

.glyph_facebook:before {
    content: "f";
}
.glyph_inverse:before {
    color: #FFFFFF;
}
*/
.glyph:before {
    color: #AAAAAA;
    display: inline-block;
    /*font-family: BeautylishGlyph;*/
    font-size: 13px;
    font-weight: normal;
    line-height: 10px;
    padding-right: 3px;
    text-transform: none;
}


.btn_gplus, a.btn_gplus, a.btn_gplus:link, a.btn_gplus:visited {
    background: none repeat scroll 0 0 #E2412B !important;
    border-color: #E2412B;
    color: #FFFFFF;
}
.btn_facebook, a.btn_facebook, a.btn_facebook:link, a.btn_facebook:visited {
    background: none repeat scroll 0 0 #004FBA !important;
    border-color: #004FBA;
    color: #FFFFFF;
}
a.btn, a.btn:link, a.btn:visited {
    height: 1.880em;
}
.glyph {
    vertical-align: middle;
}
.btn, .btn:link, .btn:visited {
    background: none repeat scroll 0 0 #E5E3E3;
    border: 1px solid #D3D3D3;
    /*color: #0F2326;*/
    cursor: pointer;
    display: inline-block;
	/*
    font: bold 1em/1.538em AvSans,sans-serif;
	*/
    height: 1.923em;
    letter-spacing: 0.15em;
    padding: 0.154em 0.538em;
    position: relative;
    text-transform: capitalize;
    top: 0;
}
body{
    margin:8px;
	overflow:hidden;
}
@-moz-document url-prefix() {#reg_form tr td{ margin-bottom:0;}}


  </style>
  </head>
  <?php 
	
	//if(isset($_REQUEST['code']) && $_REQUEST['code']=="123456")
	if(isset($_REQUEST['code']) && $_REQUEST['page'])
	{
	?>
	<body>
	</body>
	<?php
	}
	else
	{
  ?>
  <body>
  <div class="mainloginclass" style="display:block">
                    <div id="modal-login">
                        <h2 id="modal-login-title"><?php echo $client_msg['login_register']['label_Login']?></h2>
              
                        <div class="calltoaction callout unit100">
                           <?php echo $client_msg['login_register']['label_Not_Member_Yet'];?><a href="<?= WEB_PATH."/register.php" ?>" target="_parent"><strong><?php echo $client_msg['login_register']['label_Join_Now'];?></strong></a>
                        </div>
                        <div id="form_login">
                                <form class="form_vertical" action="process.php" method="post"  id="login_frm">
                                    <div id="msg_error" style="font-style: italic;font-weight: bolder;display:block;color:red;font-size: 13px;height: 20px">
                                        
                                    </div>
                                  <label for="email-modal"><?php echo $client_msg['login_register']['Field_email']?></label>
                                  
                              
                              
                                  <input type="hidden" name="hdn_is_activationcode" id="hdn_is_activationcode" value="<?php if(isset($_REQUEST['code'])){ echo $_REQUEST['code'];  } else{ echo ""; } ?>" />
                                  <input type="text"  value="" class="js-focus unit100" maxlength="128" name="emailaddress" id="email-modal">
                              
                                  <label for="password"><?php echo $client_msg['login_register']['Field_password']?></label>
                                  
                              
                              
                              
                                  <input type="password" maxlength="15" class="unit100" name="password" id="password">
                              
                                  <div>
                                    
                                    <input type="submit" class="btn btn_primary mr10" value="<?php echo $client_msg['login_register']['label_Login']?>" name="btnLogin" id="login_submit">
                                  </div>
                                </form>
              
                                 <div class="mt20" style="">
                                    <div style="padding-bottom: 5px">
                                      <div style="float:left;width:140px;margin-top:5px;" >
                                            <?php echo $client_msg['login_register']['label_Register_Sign_In'];?>
                                      </div>
									  <div class="left ml5" style="float:left;">
                                      <span class="btn btn_gplus glyph glyph_inverse glyph_gplus" id="g-signin-custom">
                                            <a target="_parent" class="login" href="<?php echo $authUrl ?>">
												google+
											</a>
                                      </span>
									 </div>
                                   <div class="left ml5" style="margin-left:10px;float:left;">
                                        <a href="<?=$loginUrl?>" target="_parent" class="btn btn_facebook glyph glyph_inverse glyph_facebook">
                                        
                                              facebook
                                        </a>
                                    </div>
                                   </div>
                                  
                                    <div class="clear"></div>
                                </div>
                                
                
                                <div class="mt20">
                                  <a href="javascript:void(0)" class="textlink" style="border-bottom:1px solid #0F2326;color:#0F2326"><?php echo $client_msg['login_register']['label_Forgot_Password']?></a>
                                </div>
                            </div>
                 </div>
        </div>
 
    
    <div class="forgotmainclass" id="forgotmainid" style="display:none;padding-left:20px;padding-right:20px;">
        <form action="" method="post" id="reg_form">
        <div class="pass_assis" ><?php echo $client_msg['login_register']['label_Forgot_Password_Assistance'];?></div>
        <div>
			<label for="email-requestPasswordReset"><?php echo $client_msg['login_register']['label_Forgot_Assistance'];?></label>
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
							<img src="get_captcha_c_p.php" alt="" id="captcha" />
						</td>
						<td>
							<img id="captcha_ajax_loading" style="display:none" src="<?php echo ASSETS_IMG;?>/c/ajax-loader-1.gif"/>
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
	    <div>
	      <b><?php echo $client_msg['login_register']['label_Having_Trouble'];?></b>&nbsp;&nbsp;<a href="javascript:void(0)"><?php echo $client_msg['login_register']['label_Contact_Service'];?></a>
	  </div>
            <p>
            	<?php //echo $_SESSION['req_pass_msg']; ?>
            </p>
        </form>
    </div>
   
    <div class="errormainclass">
          <div class="pass_assis"><?php echo $client_msg['login_register']['label_Forgot_Password_Assistance'];?></div>
	  <br />
	  <table>
					<tr>
						<td width="32%" style="font-size:0.8em;">
							<b><?php echo $client_msg['login_register']['Field_Forgot_Emailaddress'];?></b>
						</td>
						<td>
							<div id="errorlabelid"></div>
						</td>
						<td>
						
					</tr>
					<tr>
					    <td colspan="2">
					      <p class="errbox" ><?php echo $client_msg['login_register']['Msg_Not_Found_Forgot'];?></p>
					    </td>
					</tr>
	  </table>
	  <p class="actions" style="">
            	<input type="button" id="btn_goback_error" name="btn_goback_error" value="Go Back">
                
		<a href="javascript:parent.jQuery.fancybox.close();" id="btn_cancel_error" class="btn_cancel_error" >Cancel</a>
            </p>
	    <div>
	      <b><?php echo $client_msg['login_register']['label_Having_Trouble'];?></b>&nbsp;&nbsp;<a href="javascript:void(0)"><?php echo $client_msg['login_register']['label_Contact_Service'];?></a>
	  </div>
	  
    </div>
    <div class="successmainclass">
          <div class="pass_assis"><?php echo $client_msg['login_register']['label_Forgot_Password_Assistance'];?></div>
	  <br />
	  <br />
	  <div class="chkmel" >
	    <?php echo $client_msg['login_register']['label_Check_Email'];?>
	  </div>
	  <div class="chkmel_next" >
            <?php echo $client_msg['login_register']['label_Succedd_Forgot'];?>	
          </div>
	  
	  
    </div>
  </body>
  <?php
	}
  ?>
 </html>
