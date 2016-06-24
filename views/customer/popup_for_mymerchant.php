<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/******** 
@USE : popup for deal block
@PARAMETER : 
@RETURN : 
@USED IN PAGES : mymerchants.php, my-deals.php, location_detail.php, search-deal.php, process.php
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();
//start of facebook login code

//end of facebook login code

//forgot password code
function create_unique_code($customer_id)
{
    $code_length=16;
    //echo $alfa = "1AB2CD3EF4G5HI6JK7LM8N9OP10QRSTU".$campaign_id."VWXYZ";
    $alfa = "12345678910ABCDEFGHIJKLMNOPQRSTUVWXYZ".$customer_id."abcdefghijklmnopqrstuvwxyz";
    $code="";
    for($i = 0; $i < $code_length; $i ++) 
    {
      $code .= $alfa[rand(0, strlen($alfa)-1)];
    } 
    return $code;
}
if(isset($_POST['email']))
{
    if(md5($_REQUEST['mycaptcha_rpc'])!=$_SESSION['my_session_captcha'])
    {
        $_SESSION['req_pass_msg']="Captcha does not match.";
    }
    else 
    {   
	$array_where['emailaddress'] = $_POST['email'];
	$RS = $objDB->Show("customer_user", $array_where);
        
        if($RS->RecordCount()<=0){
            $_SESSION['req_pass_msg']="Email not exist.";
        }
        else 
        {
            $token=create_unique_code($RS->fields['id']);
        
            $array_values['token'] = $token;
            $array_where['emailaddress'] = $_POST['email'];
            $objDB->Update($array_values,"customer_user", $array_where);

            $mail = new PHPMailer();
            $body = "<p>Hi ".$RS->fields['firstname'].",<br/><br/>"; 
            $body .= "Changing your password is simple. Please use the link below<br/><br/>";
            $body .= "<a href='".WEB_PATH."/forgot_password.php?token=".$token."'>".WEB_PATH."/forgot_password.php?token=".$token."</a></p>";

            $body .= "<p>Thank You,</p>";
            $body .= "<p>ScanFlip Support</p>";

            $mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
            $mail->AddAddress($_POST['email']);
            $mail->From = "no-reply@scanflip.com";
            $mail->FromName = "ScanFlip Support";
            $mail->Subject    = "Reset Your ScanFlip Password";
            $mail->MsgHTML($body);
                                       //echo $body;
            $mail->Send();

            $_SESSION['req_pass_msg']="<h2>Please check your email</h2><p>We've sent you an email that will allow you to reset your password quickly and easily.</p>";
        }
    }

}

//end forgot password


$CampTitle=urldecode($_REQUEST['CampTitle']);

$businessname=$_REQUEST['businessname'];
$number_of_use=$_REQUEST['number_of_use'];
$new_customer=$_REQUEST['new_customer'];
$address=urldecode($_REQUEST['address']);
$city=urldecode($_REQUEST['city']);
$state=urldecode($_REQUEST['state']);
$country=urldecode($_REQUEST['country']);
$zip=$_REQUEST['zip'];
$redeem_rewards=$_REQUEST['redeem_rewards'];
$referral_rewards=$_REQUEST['referral_rewards'];
$o_left=$_REQUEST['o_left'];
$expiration_date=$_REQUEST['expiration_date'];
$img_src=$_REQUEST['img_src'];
$campid=$_REQUEST['campid'];
$locationid=$_REQUEST['locationid'];
$deal_desc=$_REQUEST['deal_desc'];


    $path_captcha=WEB_PATH."/captcha.php";

?>

<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="<?php echo ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">

<style>
body
{
    margin:8px;
}
.popupredeemrewards{
    background: url("<?=WEB_PATH?>/templates/images/popup_redeem.png") no-repeat scroll 0 0 transparent;
    display: inline-block;
    height: 45px;
    width: 125px;
    padding-top:5px;
}
.popupreferralrewards{
    background: url("<?=WEB_PATH?>/templates/images/popup_share.png") no-repeat scroll 0 0 transparent;
    display: inline-block;
    height: 45px;
    width: 125px;
    padding-top:5px;
}
.popupoleft1{
    background: url("<?=WEB_PATH?>/templates/images/popup_offer_left2.png") no-repeat scroll 0 0 transparent;
    display: inline-block;
    height: 45px;
    width: 125px;
    padding-top:5px;
    position: absolute;
}
.popupredeemrewards p {
    color: rgb(0,133,255);
    font-size: 20px;
   
    margin: 0;
    padding: 0;
    text-align: center;
}
.popupreferralrewards p {
    color: rgb(0,133,255);
    font-size: 20px;
   
    margin: 0;
    padding: 0;
    text-align: center;
}
.popupoleft1 p {
    color: rgb(0,133,255);
    font-size: 20px;
   
    margin: 0;
    padding: 0;
    text-align: center;
}
.popupredeemrewards span {
    font-size: 12px;
    color:#EF5A00;
    font-weight: bold;
}
.popupreferralrewards span {
    font-size: 12px;
    color:#EF5A00;
    font-weight: bold;
}
.popupoleft1 span {
    font-size: 12px;
    color:#EF5A00;
    font-weight: bold;
}
.bussinessclass{
    font-size:12px;
    padding-bottom:5px;
}
.CampTitle{
    font-size:12px;
    padding-bottom:5px;
}
.detailclassleft{
    float:left;
    width:220px;
    height:135px;
}
.detailclassright{
   height:135px;
   float:right;
}
.locationclass{
    font-size:12px;
}
.limitclass{
    font-size:12px;
    padding-bottom:5px;
}
.expirationclass{
    font-size:12px;
    padding-top:5px;
    padding-bottom:5px;
}
.ui-state-disabled:hover {
     cursor: default !important;
}

</style>
<?php $path_forgot_ajax=WEB_PATH."/validate_captcha.php";?>
<!--<script type="text/javascript" src="<?=WEB_PATH?>/js/fancybox/jquery_for_popup.js"></script>-->
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/pass_strength.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_JS?>/c/con_pass_strength.js"></script>
<script type="text/javascript">
function open_popup(popup_name)
{
$ = jQuery.noConflict();
	if($("#hdn_image_id").val()!="")
	{
		$('input[name=use_image][value='+$("#hdn_image_id").val()+']').attr("checked","checked");
	}
	$("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		$("#" + popup_name + "BackDiv").fadeIn(200, function () {
			 $("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
			 });
		});
	});
	
	}
function close_popup(popup_name)
{

	$("#" + popup_name + "FrontDivProcessing").fadeOut(100, function () {
	$("#" + popup_name + "BackDiv").fadeOut(100, function () {
		 $("#" + popup_name + "PopUpContainer").fadeOut(100, function () {         
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				$("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
$(document).ready(function(){
    
         
        $("#btn_msg_div_<?php echo $campid."-".$locationid;?>").click(function(){
		
		//alert("hi");
                //$.fancybox.close();
                $(".popupmainclass").hide();
                 //$(".popupmainclass").show();
	        open_popup('Notification<?php echo $campid.$locationid;?>');
                

	});
        $("#btnsharegridbutton<?php echo $campid."-".$locationid;?>").click(function(){
		//alert("in");
                
		var email_str = $("#txt_share_frnd<?php echo $campid.$locationid;?>").val();
                
                var msg ="";
          
             //   alert(email_str.length);
                var flag = true;
		if(email_str!="")
                    {
		
                var email_arr = email_str.split(";");
                var arr = new Array();
                //if(email_arr instanceof Array)
              
		for(i=0;i<email_arr.length;i++)
		{
                
                    var email_arr1 = email_arr[i].split(",");
                 
                    for(j=0;j<email_arr1.length;j++)
                    {
                        arr.push(email_arr1[j]);
                    }
                    
                }
		var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
		
		for(i=0;i<arr.length;i++)
		{
                 
			if(! mailformat.test(arr[i]))
			{
                            msg = "Please check email address. Either email address is not correct or you are missing colon (,) or Semi-colon (;) between email addresses";
				flag = false;
				break;
			}
		}
                    }
                 else
                 {
                      msg = "Please enter email address"; 
                     flag = false;
		}
               
		if(flag)
		{
		       
		        $("#shareloading<?php echo $campid.$locationid;?>").show();
		       $.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/process.php",
                                      data: "btn_share_grid=yes&reffer_campaign_id=" + <?php echo $campid;?> +'&refferal_location_id='+<?php echo $locationid; ?> +'&txt_share_frnd='+email_str,
                                      async : false,
                                      success: function(msg) {
                                        
                                          //close_popup('Notification<?php echo $campid.$locationid;?>');  
                                             $("#shareloading<?php echo $campid.$locationid;?>").hide();
                                             close_popup('Notification<?php echo $campid.$locationid;?>');
                                              $(".popupmainclass").show();
                                             
                                      }
                        });
		       
			return true;
		}
		else {
			$("#<?php echo $campid.$locationid;;?>").text(msg)
			 
			return false;
		}
		
                
	});
        $("#Notificationlogin").click(function(){
           $(".mainloginclass").show();
           $(".popupmainclass").hide();
        });
        $("#btn_cancel").click(function(){
            $(".popupmainclass").show();
        });
        $(".textlink").click(function(){
           $(".forgotmainclass").show(); 
            $(".mainloginclass").hide();
        });
        $("#btn_cancel_forgot").click(function(){
            $(".popupmainclass").show();
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
        
         $("#btn_goback_error").click(function(){
	           $(".forgotmainclass").show();
		   
		   
		   $(".errormainclass").hide();
	   });
         
        $('#login_frm').ajaxForm({ 
        dataType:  'json', 
        success:   processLogJson 
    });
	
	
	
      
    
})
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
var location_id = "<?php echo $locationid; ?>";
 function processLogJson(data) {
       
	//alert(data.c_id+"==="+data.l_id);
	if(data.status == "true"){
            
	    //$(".mainloginclass").hide();
            //$(".popupmainclass").show();												
		window.top.location.href = "<?php echo WEB_PATH;?>/search-deal.php";
		//window.opener.reload();
             //window.close();
                
	}else{
           
		alert(data.message);
		return false;
	}
     
}
</script>

     
        

</head>
<body>


<div class="popupmainclass" id="popupmainid">
     <div class="detailclass">
          <div class="detailclassleft">
                  <div class="bussinessclass">
                    <span style="color:#3C99F4">
                         <?php echo $businessname;?>
                     </span>
                    </div>
                    <div class="CampTitle">
                        <?php echo $CampTitle;?>
                        
                    </div>
                    <div class="limitclass">
                        <?php
                        if($number_of_use==1)
                                    {
                                        if($new_customer==1)
                                        {
                                            echo "<div style='margin-top:5px;'><b>Limit : </b>One Per Customer,Valid For New Customer Only</div>";
                                        }
                                        else 
                                        {
                                            echo "<div style='margin-top:5px;'><b>Limit : </b>One Per Customer</div>";
                                        }
                                    }
                                    elseif($number_of_use==2)
                                        echo "<div style='margin-top:5px;'><b>Limit : </b>One Per Customer Per Day</div>";
                                    elseif($number_of_use==3)
                                        echo "<div style='margin-top:5px;'><b>Limit : </b>Earn Redemption Points On Every Visit</div>";
                                        
                         ?>               
                        
                        
                    </div>
                    
                     <div class="locationclass">
                        <div class="locationlabel"><b>Where to Redeem :</b> <?php echo $address." ".$city." ".$state." ".$country; ?>
                        </div>
                       
                        
                    </div>
		    
		    
          </div>
           <div class="detailclassright">
                    <div class="popupimageclass">
                       <img src="<?php echo $img_src;?>" height="130" width="145"></img>
                       
                    </div>
          </div>
     </div>
  <div style="clear:both"></div>  
     <div class="dealstatusclass" style="height:50px;">
                        <div class="popupredeemrewards">
                            <p><?php echo $redeem_rewards;?></p>
                            <span>Redeem Point</span>
                        </div>
                        
                        <div class="popupreferralrewards">
                            <p><?php echo $referral_rewards;?></p>
                            <span>Share Point</span>
                        </div>
                        <div class="popupoleft1">
                            <p><?php echo $o_left;?></p>
                            <span>Offer Left</span>
                        </div>
      </div>
     <style>
       #ShowshareId{
	background-image:url(templates/images/button-corne-white.png);background-repeat:no-repeat;background-color:#ff8810;
        border:0px;color:#000;font-size:15px;padding:7px 15px 5px 22px;font-weight:bold;
      }
      #ShowVoucherId{
background-image:url(templates/images/button-corne-white.png);background-repeat:no-repeat;background-color:#ff8810;
        border:0px;color:#000;font-size:15px;padding:7px 15px 5px 22px;font-weight:bold;
      }
      #ShowVoucherId:hover{
          background-image:url(images/button-corner-hover2.jpg);
      }
         #ShowshareId:hover{
          background-image:url(images/button-corner-hover2.jpg);
      }
    .cust_attr_tooltip {
	background: none repeat scroll 0 0 white;
	border: 2px solid black;
	border-radius: 5px 5px 5px 5px;
	display: block;
	font-size: 12px;
	font-weight:bold;
	padding: 10px;
	position: absolute;
	top: -48px;
	width: 90px;
	left:-49px;
	float:right;
    }
    
	.arrow-down {
	width: 0; 
	height: 0; 
	border-left: 7px solid transparent;
	border-right: 7px solid transparent;
	
	border-top: 7px solid #000;
        left: 50px;
	position: absolute;
	top: 106%;
	
    }
      </style>
      
      <div class="expirationclass">
          <div class="expirationlabel">
              <div style="float:left" >
                  <b>Expiry Date: </b>
			<?php 
                   // echo $expiration_date ; 
                    echo date("m/d/y g:i A", strtotime($expiration_date));
                  ?>
              </div>
              
              <div style="float:right;position:absolute;right:0px;">
                  <img id="saveofferid" style="display:none;margin-right:25px;float:left;" src="<?php echo WEB_PATH."/templates/images/save_deal.png";?>" ></img>
			<div class="cust_attr_tooltip" style="display:none;">
									<div class="arrow-down"></div>
									Offer Reserved
			</div>
                  <input type="hidden" name="hdn_cl" id="hdn_cl" value="<?php echo $campid."=".$locationid; ?>" />
                  <input type="hidden" name="hdn_is_reserve" id="hdn_is_reserve" value="<?php echo $_REQUEST['is_reserve'] ; ?>" />
                  <input type="hidden" name="hdn_reserve_barcode" id="hdn_reserve_barcode" value="<?php echo $_REQUEST['br'] ; ?>" />
			 <a id="ShowVoucherId" style="display:none;float:right;width:106px;text-align: left;margin-right:10px;padding:7px 5px 5px 15px;" href="javascript:void(0)">Show Voucher</a>
                         <a id="ShowshareId" style="display:none;float:right;width:102px;text-align: center;margin-right:10px;padding:7px 10px 5px 15px;" href="javascript:void(0)">Share It</a>
			    <?php
			    if(isset($_SESSION['customer_id']))
			    {
					 if($_SESSION['customer_id'] != "")
					 {
									?>
					   <input type="submit" class="btn_mymerchantreserve" style="margin-left:7px;display:none;float:right;margin-right:10px;" name="btnreserve" id="btnreserve" value="Reserve" cid="<?php echo $campid; ?>" lid="<?php echo $locationid; ?>" />
								<?php
					  }
					  else
					  {
					
					  }
				}
				 ?>
                      
		
              </div>
              <div style="clear:both"></div> 
<!--	    <table>
		<tr>
		    <td width="50%">
			<b>Expiry Date: </b>
			<?php 
                    //echo $expiration_date ; 
                    echo date("m/d/y g:i A", strtotime($expiration_date));
                  ?>
		    </td>
		    
		 
		    <td align="right">
                        
			<img id="saveofferid" style="display:none" src="<?php echo WEB_PATH."/templates/images/save_deal.png";?>" style="margin-left:57px;"></img>
			<div class="cust_attr_tooltip" style="display:none;">
									<div class="arrow-down"></div>
									Offer Reserved
			</div>
			 <a id="ShowVoucherId" style="display:none" href="javascript:void(0)">Show Voucher</a>
                         <a id="ShowshareId" style="display:none" href="javascript:void(0)">Share It</a>
			 <input type="submit" class="btn_mymerchantreserve" style="margin-left:7px;display:block;float:right" name="btnreserve" id="btnreserve" value="Reserve" cid="<?php echo $campid; ?>" lid="<?php echo $locationid; ?>" />
		
		    </td>
			
		</tr>
	    </table>-->
               </div>
	  </div>
 
      <br>
      
          <style>
  
 .email_link {
   color: #fff !important;
    font-family: Arial;
   background:#615F5D;
    height: 33px;
    line-height: 33px;
   text-align: center;
   display: block;
   font-size:17px;
   font-weight: bold;
   width:85px;
    font-family: Arial;
   
}

 .btn_share_facebook {
   color: #fff !important;
    font-family: Arial;
   background:#3c5a98;;
  height: 33px;
    line-height: 33px;
   text-align: center;
   display: block;
   font-size: 17px;
   font-weight: bold;
    width:85px;
    font-family: Arial;
   
}
 .google_plus_link
{
    color: #fff  !important;
    font-family: Arial;
   background: #d95333;
   height: 33px;
  line-height: 33px;
   text-align: center;
   display: block;
   font-size: 17px;
   font-weight: bold;
    width:100px;
   
}
  .twitter_link
{
    color: #fff  !important;
   font-family: Arial;
    background: #47c7fa;
   height: 33px;
  line-height: 33px;
   text-align: center;
   display: block;
   font-size: 17px;
   font-weight:bold;
   width:85px;
   
}
                                    </style>
          <div style="border:2px solid;padding:5px;height:60px;display:none;margin-top:10px;" class="showvocherdiv" align="center">
	<img src="" class="barcode" style="height:60px" align="center" />
      </div>
      <div style="border:2px solid;padding:5px;height:60px;margin-top:10px;" class="sharediv">
        <span >
            <?php
            if(isset($_SESSION['customer_id']))
			{
				 if($_SESSION['customer_id'] != "")
				 { 
				?>
					Share to Earn Points Go for it.
				<?php 
				} 
				else
				 {
					 ?>
					Please <a href="javascript:void(0)" id="Notificationlogin" >Login</a> to share and reserve this offer. 
				 <?php  
				 }
            }
            else
            {
				 ?>
					Please <a href="javascript:void(0)" id="Notificationlogin" >Login</a> to share and reserve this offer. 
				 <?php  
			}
              ?>
        </span>
        <div style="margin-top:10px;padding-left:5px;">
             <div class="p1" style="overflow: hidden;float:left">
                    <?php
                     if(isset($_SESSION['customer_id']))
					{
						if($_SESSION['customer_id'] != "")
						 { 
							 ?>
						<a href="javascript:void(0)" class="email_link" target="_parent" id="btn_msg_div_<?php echo $campid."-".$locationid;?>" >Email</a>
						<?php
						 }
						 else 
						 {
						
						 } 
					}
                    ?>
             </div>
             <?php
			        
				$share="true";
				$th_link = WEB_PATH."/register.php?campaign_id=".$campid."&l_id=".$locationid."&share=$share";
				if(isset($_SESSION['customer_id']))
				{
					if($_SESSION['customer_id'] != "")
					{
							$th_link .= "&customer_id=".base64_encode($_SESSION['customer_id']);                                 
					}
				}
				$title=urlencode($CampTitle);
				$url=urlencode($th_link);
                                $summary=urlencode($deal_desc);
				$image=$img_src;
					
					//$image="http://ia.media-imdb.com/images/M/MV5BMjEwOTA2MjMwMl5BMl5BanBnXkFtZTcwODc3MDgxOA@@._V1._SY314_CR3,0,214,314_.jpg";
					//echo $image; 
			?>
                        
             <div class="p2" style="overflow: hidden;float:left;margin-left:2px">
                  <?php
                    if(isset($_SESSION['customer_id']))
					{
					   if($_SESSION['customer_id'] != "")
					   {
					   ?>
							<a class="btn_share_facebook" onClick="window.open('https://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&p[images][0]=<?php echo $image;?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');" target="_parent" href="javascript: void(0)">
							Facebook
							</a>
					   <?php 
					   }
					}
                   ?>
	     </div>
            
               <div style="overflow: hidden;float:left;margin-left:2px;" id="twitter">
				
				<?php
				if(isset($_SESSION['customer_id']))
				{
					if($_SESSION['customer_id'] != "")
					{ 
					?>
							<!--<a  href="<?php echo WEB_PATH."/register.php?url=".urlencode($th_link);?>" url="<?=urlencode($th_link)?>" class="twitter-share-button" data-lang="en" data-count="none">Tweetr</a> -->
							<a  href="https://twitter.com/intent/tweet?original_referer=<?php echo curPageURL(); ?>&text=ScanFlip%20%7C%20Campaign&tw_p=tweetbutton&url=<?php echo urlencode($th_link);?>" url="<?=urlencode($th_link)?>" class="twitter_link" data-lang="en" data-count="none">Twitter</a> 
					<?php 
					}
				}
				?>
				 
				
			      <script type="text/javascript" charset="utf-8">
			      var customer_id="<?php if(isset($_SESSION['customer_id'])){echo $_SESSION['customer_id'];} ?>";
			      
			      //if(customer_id != "")
			      //{
					window.twttr = (function (d,s,id) {

        var t, js, fjs = d.getElementsByTagName(s)[0];

        if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;

        js.src="//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);

        return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });

      }(document, "script", "twitter-wjs"));							
			     // }
			     
                             
      

		   
    </script> 
                            </div>
             
                   <div style="overflow: hidden; margin-left: 2px;float:left"  align="center">
                                <?php
                                if(isset($_SESSION['customer_id']))
								{
									 if($_SESSION['customer_id'] != "")
									 { 
									?>
										<a href="https://plus.google.com/share?url=<?=$th_link?>" target="_blank" class="google_plus_link" >
											Google+
										</a>
									<?php 
									}
								}
                                ?>


                           
                            
                            </div>
        
			
        </div>
      </div>
      
     

      
</div>
<?php $button_image1=ASSETS_IMG."/c/button-corne-white.png";
$button_image2=ASSETS_IMG."/c/button-corne-hover2.jpg";
?>
<style>
    .share_friends {
    color: #FF8810;
    font-size: 16px;
    font-weight: bold;
    margin: 5px 0 0;
}
.share_msg {
    font-size: 11px;
    line-height: 17px;
}
.message_pop {
    font-size: 12px;
    margin: 5px 0 0;
}
.from_mail {
    font-size: 12px;
    margin: 8px 0 0;
}
.notice_mail {
    font-size: 12px;
    font-weight: bold;
    margin: 8px 0 0;
    text-align: justify;
}
.text_area {
    margin: 5px 0 0;
    
}
.text_area textarea {
    background: linear-gradient(to bottom, #EAEAEA 0%, #FFFFFF 41%) repeat scroll 0 0 transparent;
    border: 1px solid #666666;
}
.plz_note {
    font-size: 12px;
    line-height: 16px;
    margin: 5px 0 0;
    text-align: justify;
}
.buttons_bot {
    margin: 12px 0 0;
    text-align: right;
}

.btnsharegridbutton{
    cursor: pointer;
    background-color: #FF8810;
    background-image: url("<?php echo $button_image1; ?>");
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
}
.btnsharegridbutton:hover{
    cursor: pointer;
    background-color: #FF8810;
    background-image: url("<?php echo $button_image2; ?>");
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
    background-image: url("<?php echo $button_image1; ?>");
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
    background-image: url("<?php echo $button_image2; ?>");
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
}

</style>
  <div id="Notification<?php echo $campid.$locationid;?>PopUpContainer" class="container_popup"  style="display: none;">
 <div id="Notification<?php echo $campid.$locationid;?>BackDiv" class="divBack" style="background-color:white !important"></div>
    <div id="Notification<?php echo $campid.$locationid;?>FrontDivProcessing" class="Processing" style="display:none;">                                            
            <div id="Notification<?php echo $campid.$locationid;?>MaindivLoading" align="center" valign="middle" class="imgDivLoading" style="">
                <div class="modal-close-button" style="visibility: visible;"><a tabindex="0" onclick="close_popup('Notification<?php echo $campid.$locationid;?>');" id="fancybox-close" style="display:inline;"></a></div>
                <div id="Notification<?php echo $campid.$locationid;?>mainContainer" class="innerContainer" style="height:366px;width:448px;background:none;margin-top:0px">
                    <div class="main_content">
                        <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                            <div class="campaign_detail_div" style="">
                                
                                    <div id="activatesahreDiv" class="div_share_friend" style="height:310px;text-align:left;width:393px;line-height:17px">
                                  
                                    <div class="head">
					<?php $img_src=ASSETS_IMG."/c/popup_logo.png";?>
                                        <img src="<?php echo $img_src;?>" alt="Scanflip"/><br/>
                                        
                                    </div>
                                    <div class="share_friends">Share With Friends</div>
                                    <div class="share_msg">To Share this offer with friends,complete the from below.</div>
                                    <div class="message_pop">
                                        <strong>Message :</strong><br/>
                                        <strong>I thought</strong> you might enjoy knowing this deals from Scanflip
                                    </div>        
                                   
                                    <div class="notice_mail">Specify up to 50 of your friend's email below separated by commas (,) or semicolons (;)</div>
                                      <div style="color:#FF0000;" id="<?php echo $campid.$locationid;?>"></div>
                                    <div class="text_area"><textarea rows="4" cols="45" style="resize:none"  name="txt_share_frnd" id="txt_share_frnd<?php echo $campid.$locationid;?>"></textarea></div>
                                   
                                    <div class="buttons_bot">
                                       
                                        <input type="hidden" name="reffer_campaign_id1" id="reffer_campaign_id" value="<?php if(isset($_REQUEST['campaign_id'])){echo $_REQUEST['campaign_id'];} ?>" />
                                        <input type="hidden" name="reffer_campaign_name1" id="reffer_campaign_name" value="<?php if(isset($camp_title)){echo $camp_title;} ?>" />
                                        <input type="hidden" name="refferal_location_id1" value="<?php if(isset($_REQUEST['l_id'])){echo $_REQUEST['l_id'];} ?>" id="refferal_location_id" />
                                        <input type="button" class="btnsharegridbutton" value="Share" name="btn_share_grid" id="btnsharegridbutton<?php echo $campid."-".$locationid;?>" />
                                        <input type="button" class="btnsharecancelbutton" value="Cancel" onclick="close_popup('Notification<?php echo $campid.$locationid;?>');" id="btn_cancel"  />
					<?php $img_src=ASSETS_IMG."/c/ajax-offer-loader.gif";?>
					<img src="<?php echo $img_src; ?>" alt="" id="shareloading<?php echo $campid.$locationid;?>" height="20px" width="20px" style="display:none"/>
                                    </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
  </div>
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
    width: 90%;
}
.calltoaction {
    background-color: #FFFFFF;
    border: 1px solid #DCDDDE;
    margin: 0.769em 0;
    padding: 0.769em;
    text-align: center;
    width:86% !important;
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
        margin-bottom: 10px;
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
*/
.glyph_inverse:before {
    color: #FFFFFF;
}

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
    /*font: bold 1em/1.538em AvSans,sans-serif;*/
    height: 1.923em;
    letter-spacing: 0.15em;
    padding: 0.154em 0.538em;
    position: relative;
    text-transform: capitalize;
    top: 0;
}
  </style>
  
  <div class="mainloginclass" id="mainloginid" style="display:none">
                    <div id="modal-login">
                        <h2 id="modal-login-title">Login</h2>
              
                        <div class="calltoaction callout unit100">
                          Not a member yet? <a href="<?php echo WEB_PATH."/register.php" ?>" target="_parent"><strong>Join now!</strong></a>
                        </div>
                        <div id="form_login">
                                <form class="form_vertical" action="process.php" method="post"  id="login_frm">
                              
                                  <label for="email-modal">Email</label>
                                  
                              
                              
                              
                                  <input type="text" value="" class="js-focus unit100" maxlength="128" name="emailaddress" id="email-modal">
                              
                                  <label for="password">Password</label>
                                  
                              
                              
                              
                                  <input type="password" maxlength="15" class="unit100" name="password" id="password">
                              
                                  <div>
                                   
                                    <input type="submit" class="btn btn_primary mr10" value="Login" name="btnLogin" id="login_submit">
                                  </div>
                                </form>
              
                                <div class="mt20" style="">
                                    <div style="padding-bottom: 5px">
                                      <span style="text-align :center;" >
                                             Sign In With
                                      </span>
                                   </div>
                                  <div class="left ml5">
                                      <span class="btn btn_gplus glyph glyph_inverse glyph_gplus" id="g-signin-custom">
                                             google+
                                      </span>
                                   </div>
                                  
                                    
                                   <div class="left ml5" style="margin-left:20px">
                                        <a href="<?php echo WEB_PATH."/facebook_login.php" ?>" target="_parent" class="btn btn_facebook glyph glyph_inverse glyph_facebook">
                                        
                                              facebook
                                        </a>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                
                                				   
                
                                <div class="mt20">
                                  <a href="javascript:void(0)" class="textlink" style="border-bottom:1px solid #0F2326;color:#0F2326">I forgot my password</a>
                                </div>
                            </div>
                 </div>
        </div>
    <style>
        
        #btnRequestPassword{
          cursor: pointer;
    background-color: #FF8810;
    background-image: url("<?php echo $button_image1; ?>");
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
    background-image: url("<?php echo $button_image2; ?>");
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
        }  
        
    </style>
   
 
    
     <div class="forgotmainclass" id="forgotmainid" style="display:none;padding-left:20px;padding-right:20px">
        <form action="" method="post" id="reg_form">
        <div style="font-weight:bold;padding:5px 5px 5px 0px;">Scanflip.com Password Assistance</div>
        <div>
		<label for="email-requestPasswordReset">Enter the e-mail address associated with your scanflip.com account, then click continue.<br/>
        We'll email you a link to a page where you can easily create a new password.</label>
	</div>
	
	
            <div style="margin-top:8px;">
                <table>
					<tr>
						<td width="44%" style="font-size:13px;">
							<b>Email Address :</b>
						</td>
						<td>
							<input type="text" name="email" id="email" style="width:100%"/>
						</td>
					</tr>
					<tr>
						<td width="44%" style="font-size:13px;">
							<b>Type the characters you see in this image :</b>
						</td>
						<td>
							<input type="text" id="mycaptcha_rpc" name="mycaptcha_rpc" style="width:50%;" /><br/>
						</td>
					</tr>
					<tr>
						<td width="44%" style="font-size:13px;">
							<a id="captcha_image1" href="javascript:void(0)">Try a different image</a>
						</td>
						<td>
							<img id="captcha_image_src1" src="captcha.php" style=""/>
						</td>
					</tr>
				</table>
            </div>
            
	    
	    <div class="forgotmsgdiv">
	      
	    </div>
            <p class="actions" style="">
            	<input type="button" id="btnRequestPassword" name="btnRequestPassword" value="Continue" onClick="">
                <input type="button" class="btnsharecancelbutton" value="Cancel" onClick="close_popup('Notification<?php echo $campid.$locationid;?>');" id="btn_cancel_forgot"  />
            </p>
	    <div>
	      <b>Having trouble ?</b>&nbsp;&nbsp;<a href="javascript:void(0)">Contact Customer Service</a>
	  </div>
            <p>
            	<?php //echo $_SESSION['req_pass_msg']; ?>
            </p>
        </form>
    </div>
      <style>
    #btn_goback_error{
          cursor: pointer;
    background-color: #FF8810;
    background-image: url("<?php echo $button_image1; ?>");
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;  
        }  
        #btn_goback_error:hover{
             cursor: pointer;
    background-color: #FF8810;
    background-image: url("<?php echo $button_image2; ?>");
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
    background-image: url("<?php echo $button_image1; ?>");
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
    background-image: url("<?php echo $button_image2; ?>");
    background-repeat: no-repeat;
    border: 0 none;
    color: #000000 !important;
    font-size: 15px;
    font-weight: bold;
    padding: 7px 15px 5px;
    text-decoration: none;
}

    </style>
    <div class="errormainclass" style="display:none;padding-left:20px;padding-right:20px;padding-top:15px">
          <div style="font-weight:bold;padding:5px 5px 5px 0px;">Scanflip.com Password Assistance</div>
	  <br />
	  <table>
					<tr>
						<td width="32%" style="font-size:13px;">
							<b>Email Address :</b>
						</td>
						<td>
							<div id="errorlabelid"></div>
						</td>
						<td>
						
					</tr>
					<tr>
					    <td colspan="2">
					      <p style='color:red;font-size:13px;'>There was a problem with your request.We're sorry.We weren't able to identify you given the information provided.</p>
					    </td>
					</tr>
	  </table>
	  <p class="actions" style="">
            	<input type="button" id="btn_goback_error" name="btn_goback_error" value="Go Back">
                
		<a href="javascript:parent.jQuery.fancybox.close();" id="btn_cancel_error" class="btn_cancel_error" >Cancel</a>
            </p>
	    <div>
	      <b>Having trouble ?</b>&nbsp;&nbsp;<a href="javascript:void(0)">Contact Customer Service</a>
	  </div>
	  
    </div>
    <div class="successmainclass" style="display:none;padding-left:20px;padding-right:20px;padding-top:15px">
          <div style="font-weight:bold;padding:5px 5px 5px 0px;">Scanflip.com Password Assistance</div>
	  <br />
	  <br />
	  <div style="color:#E06500;font-size:15px;font-weight:bold">
	    Check your e-mail.
	  </div>
	  <div style="font-size:13px;">
            	If the e-mail address you entered is associated with a customer account in our records, you will receive an e-mail from us with instructions for resetting your password.If you don't receive this e-mail, please check your junk mail folder or visit our Help pages to contact Customer Services for further assistance.
          </div>
	  
	  
    </div>
</body>
</html>
<script type="text/javascript">
    $(document).ready(function(){
      //  alert(jQuery("#hdn_is_reserve").val());
      var ar =jQuery("#rservedeals_value",window.parent.document).val();
      ar = ar.substring(0,ar.length-1);
        a =  ar.split(";");
        var clid= jQuery("#hdn_cl").val();
        var disp_flag = false;
       // alert(a.length+"=="+jQuery("#rservedeals_value",window.parent.document).val()+"==");
       if(ar != "")
           {
               
            c = jQuery("#deal_barcode",window.parent.document).val().split(";");
            for(i=0;i<c.length;i++){
                d = c[i].split(":");
                if(clid  == d[0])
                {
                         $(".barcode").attr("src",d[1]);
                }
            }
       for(i=0;i<a.length;i++){
                b= a[i].split(":");
                  //     alert( b[0]+"="+b[1]);
                if(clid == b[0]+"="+b[1])
                {
                    disp_flag = true;
                  //  alert("in");
                     $(".btn_mymerchantreserve").detach();
                            $(".sharediv").css("display","block");
                            $(".showvocherdiv").css("display","none");
                            $("#ShowshareId").css("display","none");
                            $("#ShowVoucherId").css("display","block");
			    $("#saveofferid").show();
                       //     alert("out");
                }
        }
           }
        if(!disp_flag)
        {
            $(".btn_mymerchantreserve").css("display","block");
        }
		bind_reserve_event();
	$("#saveofferid").hover(function(){
	//  $(".cust_attr_tooltip").show();
        $(".cust_attr_tooltip").css("display","block");
	  },function(){
	  //$(".cust_attr_tooltip").hide();
          $(".cust_attr_tooltip").css("display","none");
	});
	$('#ShowVoucherId').click(function(){
	    // $(".sharediv").show();
             $(".sharediv").css("display","none");
	     $(".showvocherdiv").css("display","block");
             $("#ShowshareId").css("display","block");
             $("#ShowVoucherId").css("display","none");
	});
        $('#ShowshareId').click(function(){
	    // $(".sharediv").show();
             $(".sharediv").css("display","block");
	     $(".showvocherdiv").css("display","none");
             $("#ShowshareId").css("display","none");
             $("#ShowVoucherId").css("display","block");
	});
   });
function bind_reserve_event()
{
	//alert("hello");
$(".btn_mymerchantreserve").click(function(){
	//alert("hello"+$("#rservedeals_value",window.parent.document).val());
    var ele = $(this);
    var reserve_val = "";
    var deal_barcode_val = "";
if($("#rservedeals_value",window.parent.document).val()!=" ")
    {
        reserve_val = $("#rservedeals_value",window.parent.document).val();
    }
      if($("#deal_barcode",window.parent.document).val()!=" ")
    {
        deal_barcode_val = $("#deal_barcode",window.parent.document).val();
    }                        

//return false;
   $.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'btnreservedeal=true&campaign_id='+$(this).attr("cid")+'&location_id='+$(this).attr("lid"),
                 // async:false,
		  success:function(msg)
		  {
                      var obj = jQuery.parseJSON(msg);
                     if(obj.status == "true")
                         {
                             reserve_val += ele.attr("cid")+":"+ele.attr("lid")+";";
                        //      alert(reserve_val);
                              $("#rservedeals_value",window.parent.document).val(reserve_val);
                           //alert("Deal successfully reserved");
                            //parent_tr.detach();
                            $(".btn_mymerchantreserve").detach();
                            $(".barcode").attr("src",obj.barcode);
                            $(".sharediv").css("display","block");
                            $(".showvocherdiv").css("display","none");
                            $("#ShowshareId").css("display","none");
                            $("#ShowVoucherId").css("display","block");
			    $("#saveofferid").show();
deal_barcode_val += ele.attr("cid")+"="+ele.attr("lid")+":"+obj.barcode+";";
$("#deal_barcode",window.parent.document).val(deal_barcode_val);
var url_d = jQuery(".displayul .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle",window.parent.document).attr('mypopupid');
url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "1");
url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
jQuery(".displayul .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle",window.parent.document).attr('mypopupid',url_d);
 url_d = jQuery(".navigationul .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle",window.parent.document).attr('mypopupid');
url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "1");
url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
jQuery(".navigationul .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle",window.parent.document).attr('mypopupid',url_d);
 url_d = jQuery(".displayul_all .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle",window.parent.document).attr('mypopupid');
url_d = url_d.replace(/(is_reserve=)[^\&]+/, '$1' + "1");
url_d = url_d.replace(/(br=)[^\&]+/, '$1' + "fgdgety");
jQuery(".displayul_all .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"'] .dealtitle",window.parent.document).attr('mypopupid',url_d);
                         }
                         else{
                             alert("Sorry , No offers left for this campaign for your selected location");
                           
                             jQuery(".fancybox-close",window.parent.document).trigger("click");
                             jQuery(".displayul_all .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"']",window.parent.document).detach();
                             jQuery(".displayul .deal_blk[locid='"+ele.attr("lid")+"'][campid='"+ ele.attr("cid")+"']",window.parent.document).detach();
                             jQuery("#hdn_reserve_err",window.parent.document).val(ele.attr("lid"));
                             
                             
                       
                         }
                         
                                     
                     // alert(1);
                        
                  }
   });
   return false;
 //  window.location.href = $(this).attr("redirect_url");
});
}
function getParam( name , url )
{
 name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
 var regexS = "[\\?&]"+name+"=([^&#]*)";
 var regex = new RegExp( regexS );
 var results = regex.exec(url);
 if( results == null )
    return "";
 else
  return results[1];
}

</script>

