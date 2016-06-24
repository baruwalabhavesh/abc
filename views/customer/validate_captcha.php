<?php
/******** 
@USE : forgot password popup
@PARAMETER : 
@RETURN : 
@USED IN PAGES : mymerchants.php, popup_for_mymerchant.php, my-deals.php, login.php, location_detail.php, search-deal.php
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();
require_once(LIBRARY."/class.phpmailer.php");
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
    
    
    //if(md5($_REQUEST['mycaptcha_rpc'])!= isset($_SESSION['my_session_captcha']))
	//echo $_REQUEST['mycaptcha_rpc'] . " ============= ". $_SESSION['random_number_c_p'];
	//exit();
	if(strtolower($_REQUEST['mycaptcha_rpc'])!= strtolower($_SESSION['random_number_c_p']))
    {
       
	echo "<span style='color:red'>Captcha does not match.</span>";
	
    }
    else 
    {   
	$array_where['emailaddress'] = $_REQUEST['email'];
	$RS = $objDB->Show("customer_user", $array_where);
        
        if($RS->RecordCount()<=0){
           //echo "<span style='color:red'>There was a problem with your request.We're sorry.We weren't able to identify you given the information provided.</span>";
	   echo "error";
        }
        else 
        {
            $token=create_unique_code($RS->fields['id']);
        
            $array_values['token'] = $token;
			$array_values['token_created_at'] = date('Y-m-d H:i:s');
            $array_where['emailaddress'] = $_REQUEST['email'];
            $objDB->Update($array_values,"customer_user", $array_where);
			//echo "123";
            $mail = new PHPMailer();
            //echo "456";
            $body = "<p>Hi ".$RS->fields['firstname'].",<br/><br/>"; 
            $body .= "Changing your password is simple. Please use the link below in 24 hours<br/><br/>";
            $body .= "<a href='".WEB_PATH."/forgot_password.php?token=".$token."'>".WEB_PATH."/forgot_password.php?token=".$token."</a></p>";

            $body .= "<br/><br/><p>Thank You,</p>";
            $body .= "<p>ScanFlip Support</p>";

            $mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
            $mail->AddAddress($_REQUEST['email']);
            $mail->From = "no-reply@scanflip.com";
            $mail->FromName = "ScanFlip Support";
            $mail->Subject    = "Reset Your ScanFlip Password";
            $mail->MsgHTML($body);
                                       //echo $body;
            $mail->Send();

           //echo "<span style='color:green'>If the e-mail address you entered is associated with a customer account in our records, you will receive an e-mail from us with instructions for resetting your password.If you don't receive this e-mail, please check your junk mail folder or visit our Help pages to contact Customer Services for further assistance.</span>";
	   echo "success";
        }
    }


 
 
?>
