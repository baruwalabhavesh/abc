<?php
/******** 
@USE : to change password
@PARAMETER : 
@RETURN : 
@USED IN PAGES : profile-left.php
*********/
//require_once("classes/Config.Inc.php");
check_customer_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//$objJSON = new JSON();
$JSON = $objJSON->get_customer_profile();
$RS = json_decode($JSON);

?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Change Password</title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">


<link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
require_once(CUST_LAYOUT."/header.php");
?>
<div id="content" class="cantent">
	<div class="my_main_div">
		<div id="contentContainer" class="contentContainer">
		
	<div style="min-height:530px;" class="manage_profile">
		
<!--    <script type="text/javascript" src="<?=WEB_PATH?>/js/jquery-1.6.2.min.js"></script>-->
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/old_pass_strength.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/new_pass_strength.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/con_new_pass_strength.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS; ?>/c/jquery.form.js"></script>

	
	<table width="100%"  border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td width="25%" align="left" valign="top">
	<?
		require_once(CUST_LAYOUT."/profile-left.php");
	?>
	</td>
    <td width="75%" align="left" valign="top">
		<form action="<?=WEB_PATH?>/process.php" method="post" id="change_password_form" class="chnge_pass">
		<table width="100%"  border="0" cellspacing="2" cellpadding="2">
  <tr>
    <!--<td>&nbsp;</td>
    <td style="color:#FF0000; " align="left" id="msg_div"><?=$_SESSION['msg']?>&nbsp;</td>-->
    <td colspan="2" align="left">
	
		<div class="warning" id="warningsbox" style="display: none">
		    <img src="<?php echo ASSETS_IMG; ?>/c/wornning.png" alt="" />
		    <span id="msg_div" ><?=$_SESSION['msg']?></span>
		</div>
		<div class="success" id="succssbox" style="display: none">
		    <img src="<?php echo ASSETS_IMG; ?>/c/hoory.png" alt="" />
		    <span id="msg_div1" ><?=$_SESSION['msg']?></span>
		</div>    
	
	
    </td> 
  </tr>
  <tr>
    <td><?php echo $language_msg["profile"]["old_password"];?></td>
    <td>
	<input type="password" name="old_password" id="old_password" style="width:200px; ">
        <!--<span id="result_old"></span>-->
	</td>
  </tr>
  <tr>
    <td><?php echo $language_msg["profile"]["new_password"];?></td>
    <td><input type="password" name="new_password" id="new_password" style="width:200px; " >
         <span id="result_new"></span>
    </td>
  </tr>
  <tr>
    <td><?php echo $language_msg["profile"]["con_new_password"];?></td>
   <td><input type="password" name="con_new_password" id="con_new_password" style="width:200px; " >
        <span id="result_con_new"></span>
   </td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td>
		<input type="submit" name="btnUpdatePassword" value="Save" >
                &nbsp;&nbsp;
          <script>function btncanpassword(){                                                
                                                window.location="<?=WEB_PATH?>/my-deals.php";}
                                                
                                                </script>
         <input type="submit" name="btncancelpassword" value="Cancel" onClick="btncanpassword()" >
	</td>
  </tr>
</table>
</form>
	</td>
  </tr>
</table>
</div>
</div>
<?php require_once(CUST_LAYOUT."/before-footer.php");?>
</div>
</div>
		<?
		require_once(CUST_LAYOUT."/footer.php");
		?>
</body>
</html>
<?
$_SESSION['msg'] = "";
?>
<script language="javascript">
$(document).ready(function() { 
    // bind form using ajaxForm 
    $('#change_password_form').ajaxForm({ 
		beforeSubmit: validate,
        dataType:  'json', 
        success:   processUpdateJson 
    });
	
	
});
function processUpdateJson(data) {
    
     if(data.message == "Password has been changed successfully")
     {
	jQuery("#warningsbox").hide();
	jQuery("#succssbox").show();
	
	 document.getElementById("msg_div1").innerHTML = data.message;
	return false;
     }
     else
     {
	jQuery("#succssbox").hide();
	jQuery("#warningsbox").show();
	 document.getElementById("msg_div").innerHTML = data.message;
	return false;
     }
         
}
function validate(){
	document.getElementById("msg_div").innerHTML = "";
	return true;
}
</script>
<script type="text/javascript">
$("a#myprofile").css("background-color","orange");
$("a#password-link").css("color","orange");
$("a#profile-link").css("color","#0066FF");
</script> 
<style>


</style>
