<?php

/**
 * @uses display forgot password popup
 * @used in pages : register.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$path_forgot_ajax=WEB_PATH."/merchant/validate_captcha.php";

$path_captcha=WEB_PATH."/merchant/captcha.php";
?>
<!DOCTYPE HTML>
 <html>
  <head>
    <link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?=ASSETS_JS?>/m/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="<?=ASSETS_JS?>/m/jquery.form.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/con_pass_strength.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS?>/m/fancybox/jquery.fancybox.js"></script>

    <script>
       $(document).ready(function(){
           
       
	
	 
	
	   $(".textlink").click(function(){
	      $(".forgotmainclass").show(); 
	      $(".mainloginclass").hide();
	   });
	   $("#btn_cancel_forgot").click(function(){
	       $(".mainloginclass").show();
	       $(".forgotmainclass").hide();
	    });
	  
	   
	   $("#btnRequestPassword").click(function(){
		    var forgot_path="<?php echo $path_forgot_ajax; ?>";
		    var mycaptcha_rpc=$("#mycaptcha_rpc").val();
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
				  
				  $(".forgotmainclass").hide();
				  $(".errormainclass").show();
				  $("#errorlabelid").html(email);
				 
				  
				}
				else if(result == "success")
				{
				  
				  $(".forgotmainclass").hide();
				  $(".successmainclass").show();
				  
				}
				else
				{
				    $(".forgotmsgdiv").html(result);  
				}
				
				  
			       }
			  });
		    }
	    });
		/*
	   $("#captcha_image").click(function(){
	     var captcha_path="<?php echo $path_captcha;?>";
	
	   
	             $.ajax({
			       url:captcha_path ,
			       
			       success: function(result){
				
				//location.reload();
				 //$("#captcha_image_src").attr('src','captcha.gif');
				 
				  d = new Date();
				 $("#captcha_image_src").attr('src','captcha.gif?'+d.getTime());
				 
			       }
			  });
		     
	    });
		
	  d = new Date();
	  $("#captcha_image_src").attr('src','captcha.gif?'+d.getTime());
	  */
	    jQuery('#captcha_image').click(function() {  
			
			change_captcha();
		 });
	 
		 function change_captcha()
		 {
			document.getElementById('captcha').src="get_captcha_m_p.php?rnd=" + Math.random();
		 }
		 
	   $("#btn_goback_error").click(function(){
	           $(".forgotmainclass").show();
		   
		   
		   $(".errormainclass").hide();
	   });
       });
       function closeFB() {
	    $.fancybox.close(); 
	}
jQuery(document).ready(function(){
jQuery("#btn_cancel_error").click(function(){

			parent.jQuery.fancybox.close();
}); 
});
function validate_register(){
    
	if(email_validation(document.getElementById("email").value) == false){
		alert("Please Enter valid Email");
		document.getElementById("email").focus();
                error_var="false";
		return false;
	}
        else if(document.getElementById("mycaptcha_rpc").value == ""){
		alert("Please Enter Captcha");
		document.getElementById("mycaptcha_rpc").focus();
		error_var="false";
                return false;
	}
        else
        {
            error_var="true";
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
	<style>
	 
    #btnRequestPassword{
          cursor: pointer;
    background-color: #FF8810;
    
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;  
        }  
        #btnRequestPassword:hover{
             cursor: pointer;
    background-color: #FF8810;
    
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
        }
	.btnsharecancelbutton{
    cursor: pointer;
    background-color: #FF8810;
    
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
}
.btnsharecancelbutton:hover{
    cursor: pointer;
    background-color: #FF8810;
    
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
}
.password_assistance
{
	font-weight:bold;padding:5px 5px 5px 0px;
}
.table_th_32
{
	width :32%;
}
.forgot_password_container
{
	margin-top:8px;
}
.table_th_44
{
	width:44%;
}
.f_psd_heading
{
	font-size:0.8em;
}
 #btn_goback_error  ,#btn_cancel_error{
          cursor: pointer;
    background-color: #FF8810;
    
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;  
        }  
        #btn_goback_error:hover , #btn_cancel_error:hover{
             cursor: pointer;
    background-color: #FF8810;
    
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
        }
	.btn_cancel_error{
    cursor: pointer;
    background-color: #FF8810;
   
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000 !important;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
}
.btn_cancel_error:hover{
    cursor: pointer;
    background-color: #FF8810;
   
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000 !important;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
    text-decoration: none;
}
.errormainclass
{
	display:none;padding-left:20px;padding-right:20px;padding-top:15px;
}
#forgot_password_body
{
	background-color: #eaeaea !important;
	background-image : none !important;
	text-align:left;
}
#forgot_password_body
{
	background-color: #eaeaea !important;
	background-image : none !important;
	text-align:left;
}
#forgotmainid
{
	display:block;padding-left:20px;padding-right:20px
}
.pswd_err_msg { color:red;font-size:13px; }
.successmainclass{ display:none;padding-left:20px;padding-right:20px;padding-top:15px }
.successmainclass div{font-weight:bold;padding:5px 5px 5px 0px;}
.msg_check_mail	  {		color:#E06500;font-size:15px;font-weight:bold	  }
.msg_recieve_emai	  {		font-size:13px;	  }
	</style>
  </head>
  <body id="forgot_password_body">
    
    <div class="forgotmainclass" id="forgotmainid" >
        <form action="" method="post" id="reg_form">
        <div class="password_assistance"><?php echo $merchant_msg['popup_forgot']['password_assistance'];?></div>
        <div>
		<?php echo $merchant_msg['popup_forgot']['content'];?>
	</div>
	
	
            <div class="forgot_password_container" >
                <table>
					<tr>
						<td class="table_th_44 f_psd_heading" >
							<b><?php echo $merchant_msg['popup_forgot']['Field_email'];?></b>
						</td>
						<td>
							<input type="text" name="email" id="email" style="width:100%"/>
						</td>
					</tr>
					<tr>
						<td class="table_th_44 f_psd_heading">
							<b><?php echo $merchant_msg['popup_forgot']['Field_type_chatacter'];?></b>
						</td>
						<td>
							<input type="text" id="mycaptcha_rpc" name="mycaptcha_rpc" class="table_th_50"  /><br/>
						</td>
					</tr>
					<tr>
						<td class="table_th_44 f_psd_heading">
							<a id="captcha_image" href="javascript:void(0)"><?php echo $merchant_msg['popup_forgot']['msg_try_different_image'];?></a>
						</td>
						<td>
							<img src="get_captcha_m_p.php" alt="" id="captcha" />
						</td>
					</tr>
				</table>
            </div>
            
	    
	    <div class="forgotmsgdiv">
	      
	    </div>
            <p class="actions" >
            	<input type="button" id="btnRequestPassword" name="btnRequestPassword" value="<?php echo $merchant_msg['index']['btn_continue'];?>" onClick="">
                <input type="button" style="display:none" class="btnsharecancelbutton" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" id="btn_cancel_forgot"  />
            </p>
	    <div>
	      <b>Having trouble ?</b>&nbsp;&nbsp;<a href="javascript:void(0)">Contact Customer Service</a>
	  </div>
            <p>
            	<?php //echo $_SESSION['req_pass_msg']; ?>
            </p>
        </form>
    </div>
  
    <div class="errormainclass" >
          <div class="password_assistance"><?php echo $merchant_msg['popup_forgot']['password_assistance'];?></div>
	  <br />
	  <table>
					<tr>
						<td class="table_th_32 f_psd_heading">
							<b><?php echo $merchant_msg['popup_forgot']['Field_email'];?></b>
						</td>
						<td>
							<div id="errorlabelid"></div>
						</td>
						<td>
						
					</tr>
					
					<tr>
					    <td colspan="2">
					      <p class="pswd_err_msg" ><?php echo $merchant_msg['popup_forgot']['msg_error'];?></p>
					    </td>
					</tr>
	  </table>
	  <p class="actions">
            	<input type="button" id="btn_goback_error" name="btn_goback_error" value="<?php echo $merchant_msg['index']['btn_go_back'];?>">
                <input type="button" id="btn_cancel_error" name="btn_cancel_error" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" >
		<!--<a href="javascript:parent.jQuery.fancybox.close();" id="btn_cancel_error" class="btn_cancel_error" ><?php echo $merchant_msg['index']['btn_cancel'];?></a>-->
            </p>
	    <div>
	      <b>Having trouble ?</b>&nbsp;&nbsp;<a href="javascript:void(0)">Contact Customer Service</a>
	  </div>
	  
    </div>
    <div class="successmainclass" >
          <div ><?php echo $merchant_msg['popup_forgot']['password_assistance'];?></div>
	  <br />
	  <br />
	  
	  
	  <div class="msg_check_mail">
	    <?php echo $merchant_msg['popup_forgot']['msg_check_mail'];?>
	  </div>
	  
	  <div class="msg_recieve_emai" >
            	<?php echo $merchant_msg['popup_forgot']['msg_recieve_email'];?>
          </div>
	  
	  
    </div>

   
  </body>
 </html>
