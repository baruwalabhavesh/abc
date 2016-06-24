<?php
/**
 * @uses change password when user forgot his password
 * @used in pages :forgot_ajax_page.php,process.php,request_password.php,header.php
 * @author Sangeeta Raghavani
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<?php
//require_once("../classes/Config.Inc.php");
//check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");

?>
<html>
<head>
<title>ScanFlip | Forgot Password </title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
</head>
<link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS?>/m/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS?>/m/fancybox/jquery.fancybox.css" media="screen" />
<script type="text/javascript" src="<?php echo ASSETS_JS?>/m/fancybox/jquery_for_popup.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/m/fancybox/jquery.fancybox.js"></script>
         <script type="text/javascript" src="<?php echo ASSETS_JS?>/m/new_pass_strength.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/m/con_new_pass_strength.js"></script>



<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
<?php

//$objDB = new DB('read');
$array_where=array();
$array_where['token'] = $_REQUEST['token'];
$RS = $objDB->Show("merchant_user", $array_where);

$forg_merchant_id=$RS->fields['id'];
$array_where['id'] = $RS->fields['id'];
$_SESSION['forgot_mer_id']=$RS->fields['id'];
//$RS = $objDB->Show("merchant_user", $array_where);

?>


<body>
<div class="my_main_div">
<!--start header--><div class="my_inner_div">

		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
		
<div id="contentContainer">
	<div id="content">
	
	
		<form action="<?=WEB_PATH?>/merchant/process.php" method="post" id="change_password_form">
		<table width="70%"  border="0" cellspacing="2" cellpadding="2" align="center">
  <tr>
    <td>&nbsp;</td>
    <td style="color:#FF0000; " align="left" id="msg_div"><?=$_SESSION['msg']?>&nbsp;</td>
  </tr>
  <!--<tr>
    <td width="35%"><?php echo $language_msg["profile"]["old_password"];?></td>
    <td width="65%">
	<input type="password" name="old_password" id="old_password" style="width:200px; ">
	</td>
  </tr>-->
  <tr>
    <td width="200"><?php echo $language_msg["profile"]["new_password"];?></td>
    <td><input type="password" name="new_password" id="new_password" style="width:200px; " >
    <span id="result_new"></span></td>
  </tr>
  <tr>
    <td width="200"><?php echo $language_msg["profile"]["con_new_password"];?></td>
   <td><input type="password" name="con_new_password" id="con_new_password" style="width:200px; " >
       <span id="result_con_new"></span></td>
  </tr>
  <tr >
        <td align="left" style="vertical-align: top;">Type the text shown in image :</td>
        <td align="left">
                <input type="text" id="mycaptcha_fpm" name="mycaptcha_fpm" /><br/>
				<img src="get_captcha_m_f_p.php" alt="" id="captcha" /><br/>
				<a id="captcha_image" href="javascript:void(0)"><?php echo $merchant_msg['popup_forgot']['msg_try_different_image'];?></a>
        </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
		<input type="submit" name="btnUpdateForgotPassword" value="Save" >
                <script>function btncanfpm(){                                                
                                                window.location="<?=WEB_PATH?>/merchant";}
                                                
                                                </script>
         <input type="submit" name="btncancelfpm" value="Cancel" onClick="btncanfpm()"  >
	</td>
  </tr>
</table>
</form>
 </div>
<div style="clear: both"></div>
	<!--end of content--></div>
	<!--start footer--><div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		?>
		<!--end of footer--></div>
<!--end of contentContainer--></div>

</div>

</body>
</html>
<?php
$_SESSION['req_pass_msg']="";
$_SESSION['msg']="";
?>
<style>
#result_old
{
    margin-left: 5px;
    font-weight: bold;
}
#result_new
{
    margin-left: 5px;
    font-weight: bold;
}
#result_con_new
{
    margin-left: 5px;
    font-weight: bold;
}
.short
{
    color:#FF0000;
    font-weight: bold;
}
.weak
{
    color:#E66C2C;
    font-weight: bold;
}
.good
{
    color:#2D98F3;
    font-weight: bold;
}
.strong
{
    color:#006400;
    font-weight: bold;
}
.fancybox-close {top:0px;right:0px;margin-right:5px;margin-top:5px;}
</style>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/jquery.form.js"></script>
<script language="javascript">
    jQuery(document).ready(function() { 
    // bind form using ajaxForm 
    jQuery('#change_password_form').ajaxForm({ 
        dataType:  'json', 
        success:   processChangePasswordJson 
    });
	
	jQuery('#captcha_image').click(function() {  
			
			change_captcha();
		 });
	 
		 function change_captcha()
		 {
			document.getElementById('captcha').src="get_captcha_m_f_p.php?rnd=" + Math.random();
		 }
	
});
function processChangePasswordJson(data) { 
	if(data.status == "true"){
                <?php
                    $_SESSION['msg']="";
                ?>
                alert('Password Has Been Changed Successfully.');
		window.location.href='<?=WEB_PATH?>/merchant/register.php';
	}
	else
	{
		alert(data.message);
                <?php
                    //$_SESSION['msg']="";
                ?>
	} 
}
// 369
function gotohomepage()
{
	window.location.href='<?=WEB_PATH?>/merchant/register.php';
}
</script>
<?php
if($RS->RecordCount()>0)
{
	//echo "active";
}
else
{
	//echo "expire";
?>
<script>
	
	jQuery.fancybox({
			content:"<div style='text-align:left;padding:5px;padding-top:15px;text-transform:capitalize;'>Sorry, password reset token has expired.</div><div style='margin-top:10%;'><input type='submit' name='cancel' id='cancel' value='Ok' onclick='gotohomepage()'/></div>",

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
</script>	
<?php
}
