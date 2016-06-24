<?php
/******** 
@USE : forgot password page
@PARAMETER : 
@RETURN : 
@USED IN PAGES : popup_for_mymerchant.php, process_mobile.php, forgot_ajax_page.php, request_password.php
*********/
//require_once("classes/Config.Inc.php");
//check_customer_session();
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ScanFlip | Forgot Password</title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

</head>

<!--<script type="text/javascript" src="<?=WEB_PATH?>/js/jquery-1.6.2.min.js"></script>-->
 <link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS?>/c/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo ASSETS_CSS?>/c/fancybox/jquery.fancybox.css" media="screen" />
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/fancybox/jquery_for_popup.js"></script>
<!--<script type="text/javascript" src="<?=WEB_PATH?>/js/jquery-1.9.0.min.js"></script>-->

<!--<script type="text/javascript" src="<?=WEB_PATH?>/js/fancybox/jquery.fancybox-buttons.js"></script>-->
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/fancybox/jquery.fancybox.js"></script>
         <script type="text/javascript" src="<?php echo ASSETS_JS?>/c/new_pass_strength.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/con_new_pass_strength.js"></script>



<link href="<?php echo ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">
<?php
$objDB = new DB();

$array_where['token'] = $_REQUEST['token'];
$RS = $objDB->Show("customer_user", $array_where);

$forg_cust_id=$RS->fields['id'];
$array_where['id'] = $RS->fields['id'];
$_SESSION['forgot_cust_id']=$RS->fields['id'];
//$RS = $objDB->Show("customer_user", $array_where);
?>


<body>
<?php require_once(CUST_LAYOUT."/header.php");?>
<div id="content" class="cantent">
	<div class="my_main_div">
		<div id="contentContainer" class="contentContainer">

       <form action="<?=WEB_PATH?>/process.php" method="post" id="change_password_form">
		<table width="70%"  border="0" cellspacing="2" cellpadding="2" align="center">
  <tr>
    <td>&nbsp;</td>
    <td align="left" id="msg_div"><?=$_SESSION['msg']?>&nbsp;</td>
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
  <tr>
        <td align="left" style="vertical-align: top;">Type the text shown in image :</td>
        <td align="left">
                <input type="text" id="mycaptcha_fpc" name="mycaptcha_fpc" /><br/>
				<img src="get_captcha_c_f_p.php" alt="" id="captcha" /></br>
				<a id="captcha_image" href="javascript:void(0)"><?php echo $merchant_msg['popup_forgot']['msg_try_different_image'];?></a>
        </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
		<input type="submit" name="btnUpdateForgotPassword" value="Save" >
                <script>function btncanfp(){                                                
                                                window.location="<?=WEB_PATH?>";}
                                                
                                                </script>
         <input type="submit" name="btncancelfp" value="Cancel" onClick="btncanfp()"  >
	</td>
  </tr>
</table>
</form>
        		</div>
	
	<?php require_once(CUST_LAYOUT."/before-footer.php");?>
</div><!--end of my_main_div-->
</div><!--end of content-->

<?php require_once(CUST_LAYOUT."/footer.php");?>

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
</style>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/jquery.form.js"></script>
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
		
	 	document.getElementById('captcha').src="get_captcha_c_f_p.php?rnd=" + Math.random();
	 	
	 }
	
});
function processChangePasswordJson(data) { 
	if(data.status == "true"){
                <?php
                    $_SESSION['msg']="";
                ?>
                alert('Password Has Been Changed Successfully.');
		window.location.href='<?=WEB_PATH?>/register.php';
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
	window.location.href='<?=WEB_PATH?>/register.php';
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
			content:"<div style='text-align:left;padding:5px;padding-top:15px;text-transform:capitalize;'>* Sorry, your token is expired</div><div style='margin-left:35%;margin-top:10%;'><input type='submit' name='cancel' id='cancel' value='Ok' onclick='gotohomepage()'/></div>",

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
