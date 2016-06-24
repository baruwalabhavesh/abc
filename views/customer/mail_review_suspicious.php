<?php
/******** 
@USE : review suspicious block
@PARAMETER : 
@RETURN : 
@USED IN PAGES : location_detail.php
*********/
//require_once("classes/Config.Inc.php");
//check_customer_session();
//include_once(SERVER_PATH."/classes/DB.php");
require_once(LIBRARY."/class.phpmailer.php");
//$objDB = new DB();

	/* $sql = "select * from review_rating r inner join locations l on l.id=r.location_id  inner join campaigns c on r.campaign_id=c.id where r.id=".$_REQUEST['review_id'];
	$RS = $objDB->Conn->Execute($sql); */
	$RS = $objDB->Conn->Execute("select * from review_rating r inner join locations l on l.id=r.location_id  inner join campaigns c on r.campaign_id=c.id where r.id=?",array($_REQUEST['review_id']));

	$review =  str_replace(' ', '&nbsp;', trim($_REQUEST['comment']));
	
	
	$array_lc = array();
	$array_lc['id'] = $RS->fields['location_id'];
	$RS_loc = $objDB->Show("locations",$array_lc);
	$address = $RS_loc->fields['address'] . ", " . $RS_loc->fields['city'] . ", " . $RS_loc->fields['state'] . ", " . $RS_loc->fields['zip'] . ", " . $RS_loc->fields['country'];
	
	$array_cust = array();
	$array_cust['id'] = $RS->fields['customer_id'];
	$RS_cust = $objDB->Show("customer_user",$array_cust);
	$cust_name = $RS_cust->fields['firstname']." ".$RS_cust->fields['lastname'];
	
    $body = "";      
	$body = "<div>";
		$body .= "<p>".$client_msg['location_detail']['label_mail_hello']."</p>";
		$body .= "<p>".$client_msg['location_detail']['label_mail_campaign']." ".$RS->fields['location_name']."</p>";
		$body .= "<p>".$client_msg['location_detail']['label_mail_location']." ".$RS->fields['title']."</p>";
		$body .= "<p>".$client_msg['location_detail']['label_mail_review']."<br/> ".$RS->fields['review']."</p>";
		$body .= "<br/>";
		if($_REQUEST['reason']==18)
		{
		$body .= "<p>Reason that I think this review is suspicious is  Review written by owner or staff";
		}
		else if($_REQUEST['reason'] == 19)
		{
			$body .= "<p>Reason that I think this review is suspicious is Review written by competitor";
		}
		else if($_REQUEST['reason']  == 9)
		{
			$body .= "<p>Reason that I think this review is suspicious is Other/None of the above";
		}
		$body .= "<br/><hr/>";
		$body .= "<p>".$client_msg['location_detail']['label_mail_comment']." <br/>".$review;
		$body .= "</p>";
	$body .= "</div>";
	
	$newbody ='';
	$newbody .='<body bgcolor="#e4e4e4" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" style="-webkit-font-smoothing: antialiased;width:100% !important;background:#e4e4e4;-webkit-text-size-adjust:none;">
	
<table cellspacing="0" cellpadding="0" style="width:100%; border:0;clear:both; margin:20px 0;">
  <tbody style="width:100%; display:inline-block;">
    <tr style="width:100%; display:inline-block;">
      <td style="width:100%; display:inline-block;"><table bgcolor="#D2D2D2" align="center" style="width:100%; max-width:600px; padding:20px; border-radius: 10px;">
          <tbody>
            <tr>
              <td style="width:100%; display:inline-block;"><img width="205" height="30" style="-ms-interpolation-mode: bicubic; padding:0 0 20px;" alt="Scanflip" src="/templates/images/scanflip-logo.png"></td>
            </tr>
            <tr>
              <td><table bgcolor="#FFF" style="width:100%; display:inline-block;border-left:5px solid #F37E0A;">
                  <tbody><tr><td bgcolor="white" style="padding:15px 15px">
 
								<p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;"><strong>Merchant Name : </strong>'.$RS_loc->fields['location_name'].'</p>
								<p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;"><strong>Location : </strong>'.$address.'</p>
								<p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;"><strong>Review ID : </strong>'.$_REQUEST['review_id'].'</p>
								<p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;"><strong>Reviewed By : </strong>'.$cust_name.'</p>
								<p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;"><strong>Review Date : </strong>'.$RS->fields['reviewed_datetime'].'</p>
								<p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;"><strong>Review : </strong>'.$RS->fields['review'].'</p>
								<p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;"><strong>Remarks : </strong>'.$review.'</p>
								
                        </td></tr></tbody>
                </table></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>

</body>';

	// echo $body;
	// echo $newbody;
	
       $mail = new PHPMailer();
                $mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
					$mail->AddAddress($client_msg['location_detail']['suspicious_to_email']);
					$mail->From = $_SESSION['customer_info']['emailaddress'];
					$mail->FromName = $_SESSION['customer_info']['firstname']." ".$_SESSION['customer_info']['lastname'];
					$mail->Subject    = $client_msg['location_detail']['suspicious_subject'];
					$mail->MsgHTML($newbody);
		$mail->Send(); 
		//echo $body;
   echo "sucess";
?>

