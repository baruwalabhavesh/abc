<?php
/******** 
@USE : my email setting page
@PARAMETER : 
@RETURN : 
@USED IN PAGES : email_unsubscribed.php, profile-left.php, process.php, reserve-campaign-schedular.php, frequent-mail-by-admin.php
*********/
//require_once("classes/Config.Inc.php");
check_customer_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objJSON = new JSON();
$JSON = $objJSON->get_customer_profile();
$RS = json_decode($JSON);
//$objDB = new DB();

//Update email settings

$where_array = array();
$where_array['customer_id'] = $_SESSION['customer_id'];
$RS = $objDB->Show("customer_email_settings",$where_array);
if($RS->RecordCount() == 0)
{
    $e_s_1 =1;
    $e_s_2 =1;
    $e_s_3 =1;
    $e_s_4 = 50;
}
else
{
    $e_s_1 = $RS->fields['campaign_email'];
    $e_s_2 = $RS->fields['subscribe_merchant_new_campaign'];
    $e_s_3 = $RS->fields['subscribe_merchant_reserve_campaign'];
    $e_s_4 = $RS->fields['merchant_radius'];
}
//Update email settings

?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Manage Email Settings</title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.2.min.js"></script>-->
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
	
	<table width="100%"  border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td width="25%" align="left" valign="top">
	<?
		require_once(CUST_LAYOUT."/profile-left.php");
	?>
	</td>
    <td width="75%" align="left" valign="top">
		<form class="myemailsetting" method="post" action="<?=WEB_PATH?>/process.php" ><!--- id="reg_form" action="<?=WEB_PATH?>/includes/customer/process.php" -->
		<table width="100%"  border="0" cellspacing="2" cellpadding="2">
 
  <tr>
    
    <td colspan="2"><h2>Update my email address</h2></td>
  </tr>
    <tr>
  	<td colspan="2" style="color:red">
	                  
              <div class="warning" id="warningsbox" style="display: none">
		    <img src="<?php echo ASSETS_IMG; ?>/c/wornning.png" alt="" />
		    <span id="msg_div" ></span>
		</div>
	      <?php //echo $_SESSION['msg_email_update']; ?>
	     
	      
	      <?php 
              if(isset($_SESSION['msg_email_update']))
              {
                if($_SESSION['msg_email_update'] != ""){ ?>
                <div class="success" id="succssbox" style="display: block">
                      <img src="<?php echo ASSETS_IMG; ?>/c/hoory.png" alt="" />
                      <span id="msg_div1" style="font-size: 13px;font-weight: bold;margin-left:10px;"><?php echo $_SESSION['msg_email_update']; ?> </span>
                  </div>
              <?php } } ?>
	
	</td>
  </tr>
  <tr>
      <td colspan="2">
          We have email address listed as : <span id="user_email"> <?php echo $_SESSION['customer_info']['emailaddress']; ?> </span>
      </td>
  </tr>

  <tr>
    <td>Change my email address to:</td>
    <td><input type="text" name="change_email_address" id="change_email_address" style="width:200px; " value=""></td>
  </tr>
  <tr>
    <td>Confirm new email address:</td>
    <td><input type="text" name="confirm_emailaddress" id="confirm_emailaddress" style="width:200px; " value=""></td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td>
	<input type="submit" name="btnUpdateemailaddress" value="Save" id="btnUpdateemailaddress" >
        <input type="submit" name="btncancelprofile" value="Cancel" onClick="btncanprofile()"  >
	</td>
  </tr>
 <tr>
    
    <td colspan=2><h2>Manage campaign email subscription</h2></td>
    
  </tr>
 <tr>
    <td colspan=2>
	 <?php 
         if(isset($_SESSION['msg_email_setting']))
         {
         if($_SESSION['msg_email_setting'] != ""){?>
	            <div class="success" id="succssbox" style="display: block">
			<img src="<?php echo ASSETS_IMG; ?>/c/hoory.png" alt="" />
			<span id="msg_div1" style="font-size: 13px;font-weight: bold;margin-left:10px;"><?php echo $_SESSION['msg_email_setting']; ?> </span>
		    </div>
         <?php }
         } ?>
		  <div class="warning" id="warningsbox_error" style="display:none;">
		    <img src="<?php echo ASSETS_IMG; ?>/c/wornning.png" alt="" />
		    <span id="msg_div_eroor" style="font-size: 13px;font-weight: bold;margin-left:10px;">
				Search radius must be between 2 miles to 50 miles
			</span>
		</div>
    </td>
 </tr>
<tr>
    <td><?php echo $language_msg["profile"]["campaign_email"];?></td>
    <td> <input type="radio" name="rd_campaign_email" value="1" <?php if($e_s_1==1) echo "checked"; ?>   > ON &nbsp;&nbsp;
        <input type="radio" name="rd_campaign_email" value="0"  <?php if($e_s_1==0) echo "checked"; ?> > OFF</td>
  </tr>
  <tr>
    <td><?php echo $language_msg["profile"]["subscribe_merchant_new_campaign"];?></td>
    <td> <input type="radio" name="rd_subscribe_merchant_new_campaign" value="1"  <?php if($e_s_2==1) echo "checked"; ?>  > ON &nbsp;&nbsp;
        <input type="radio" name="rd_subscribe_merchant_new_campaign" value="0"  <?php if($e_s_2==0) echo "checked"; ?> > OFF</td>
  </tr>
  <tr>
    <td><?php echo $language_msg["profile"]["subscribe_merchant_reserve_campaign"];?></td>
    <td> <input type="radio" name="rd_subscribe_merchant_reserve_campaign" value="1" <?php if($e_s_3==1) echo "checked"; ?>   > ON &nbsp;&nbsp;
        <input type="radio" name="rd_subscribe_merchant_reserve_campaign" value="0" <?php if($e_s_3==0) echo "checked"; ?>  > OFF</td>
  </tr>
  
  <tr>
    <td><?php echo $language_msg["profile"]["merchant_radius"];?><span class="notification_tooltip" title="Search radius to find merchants from current location or postal code for campaign subscription mail.">&nbsp;&nbsp;&nbsp;</span></td>
    <td> <input type="text" autocomplete="off" name="txt_merchant_radius" id="txt_merchant_radius" style="width:50px; " value="<?=$e_s_4?>">&nbsp;Miles</td>
  </tr>

  
  <tr>
    <td>&nbsp;</td>
    <td>
		<input type="submit" name="btnUpdateemailsettings" value="Save" id="btnUpdateemailsettings" >
                <script>function btncanprofile(){                                                
                                                window.location="<?=WEB_PATH?>/my-deals.php";}
                                                
                                                </script>
         <input type="submit" name="btncancelprofile" value="Cancel" onClick="btncanprofile()"  >
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
$_SESSION['msg_email_update'] = "";
$_SESSION['msg_email_setting'] ="";
?>
<?php 

?>
<script language="javascript">
jQuery('input').focus(function() {
	//alert('Handler for .focus() called.');
	//jQuery("#succssbox").css("display","none");
	jQuery("#succssbox").fadeOut("slow");
});
jQuery(document).ready(function() { 
    // bind form using ajaxForm 
    /*
    jQuery('#reg_form').ajaxForm({ 
        dataType:  'json', 
        success:   processUpdateJson 
    });
	*/
	
});
function processUpdateJson(data) {
    alert(data.message);
        jQuery(".success").show();
	document.getElementById("msg_div").innerHTML = data.message;
	return false;
}
</script>
<!--
<link rel="stylesheet" href="<?=WEB_PATH?>/merchant/css/jquery.tooltip.css" />
<script src="<?=WEB_PATH?>/merchant/js/jquery.tooltip.js" type="text/javascript"></script>
-->
<script type="text/javascript">    
jQuery("#btnUpdateemailsettings").click(function(){
	var numericReg = /^([2-9]|[1-4][0-9]|50)$/;
	 var txt_merchant_radius=jQuery("#txt_merchant_radius").val()
	if(!numericReg.test(txt_merchant_radius)) 
	{
		jQuery("#warningsbox_error").css("display","block");
		//alert("Search radius must be between 2 miles to 50 miles");
		return false;
	}
	
});

jQuery(".notification_tooltip").each(function(){
	jQuery(this).attr("data-toggle","tooltip");
	jQuery(this).attr("data-placement","right");
//	alert(jQuery(this).attr("data-placement"));
	jQuery(this).attr("data-html","true");
});
													
jQuery('.notification_tooltip').tooltip({
	track: true,
	delay: 0,
	showURL: false,
	showBody: "<br>",
	fade: 250
});
jQuery("#btnUpdateemailaddress").click(function(){
    //change_email_address confirm_emailaddress
    email_str1 = jQuery("#change_email_address").val();
    email_str2 = jQuery("#confirm_emailaddress").val();
     var msg ="";
     
     //Check for email address1  is match with datbase
     //if(email_str1 !="" && email_str2 != ""){
		   
                    ///    }else{
                        
                      //  }
                        
//                        if(flag)
//                            {
//                                return true;
//                            }
//                            else
//                                {
//                                    return false;
//                                }
     //
     
     
     
            //    alert(email_str);
             //   alert(email_str.length);
             //   var flag = true;
	         if(email_str1 == "")
		     {
			    msg = "Please enter your new email address";
			    //jQuery(".warning").show();
				jQuery("#warningsbox").show();
				
			    jQuery("#succssbox").hide();
			    jQuery("#msg_div").html(msg);
			    flag = false;
			    return false;
			  
		     }
		     if(email_str2 == "")
		     {
			    msg = "Please enter confirm new email address";
			    //jQuery(".warning").show();
				jQuery("#warningsbox").show();
			    jQuery("#succssbox").hide();
			    jQuery("#msg_div").html(msg);
			    flag = false;
			     return false;
			  
			  
		     }
		if(email_str2 != "" || email_str1 !="" )
                {
		     //if(email_arr instanceof Array)
		    
                     var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
                        if(! mailformat.test(email_str2) || ! mailformat.test(email_str1))
                        {
							
                            msg = "Please enter valid email address";
			    //jQuery(".warning").show();
				jQuery("#warningsbox").show();
			    jQuery("#succssbox").hide();
			    jQuery("#msg_div").html(msg);
			    flag = false;

                        }
			else if(email_str2.toLowerCase() != email_str1.toLowerCase())
			{
			    msg = "Please enter valid confirm new email address";
			    //jQuery(".warning").show();
				jQuery("#warningsbox").show();
			    jQuery("#succssbox").hide();
			    jQuery("#msg_div").html(msg);
			    flag = false;
			}
			else
			{
			     $.ajax({
					    type: "POST",
					    url: "<?=WEB_PATH?>/process.php",
					    data: "check_user_email_valid=yes&email=" + email_str1,
					    async : false,
					    success: function(msg) {
					       
						if(msg.indexOf('true') !== -1 )
						    {
		                                        
							flag = true;
							
						      
						    }
						    else if(msg.indexOf('false') !== -1 )
						    {
															 
							
						        
							message="Email address already exists in the system";
							//jQuery(".warning").show();
							jQuery("#warningsbox").show();
							jQuery("#succssbox").hide();
							jQuery("#msg_div").html(message);
							flag = false;
						        
						    }
						    
						   
					    }
			      });
			}
		
                 }
                
            
		if(flag)
		{
			return true;
		}
		else {
			
			return false;
		}
             
                
    });

$("a#myprofile").css("background-color","orange");
$("a#email-link").css("color","orange");
$("a#password-link").css("color","#0066FF");
</script>
<style>
h3 {
    color: black !important;
	font-size:14px !important;
    font-weight: lighter;
	background-color:hsl(0, 0%, 93%) !important;
	border:none !important;
}
</style>
