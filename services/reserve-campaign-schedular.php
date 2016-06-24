<?php
require_once("/var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/Config.Inc.php");
require_once(SERVER_PATH."/classes/class.phpmailer.php");
include_once(SERVER_PATH."/classes/DB.php");
$objDB = new DB();


$array_where_as = array();
$array_where_as['id'] = 5;
$RS = $objDB->Show("admin_settings", $array_where_as);
if($RS->RecordCount()>0)
{
	//echo $RS->fields['action'];

	if($RS->fields['action']==0)
	{	
		$array_values_as = $array_where_as = array();
		$array_values_as['action'] = 1;
		$array_where_as['id'] = 5;
		$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
//$query_updatenooflocation = "insert into merchant_store set merchant_id=1 , location_id=1";
//$objDB->Conn->Execute($query_updatenooflocation);

$query =  "SELECT * FROM `customer_email_settings` e, customer_user c where c.id=e.customer_id and c.active=1 and c.curr_latitude!='' and c.curr_longitude!='' and
       ( e.subscribe_merchant_reserve_campaign=1 or e.subscribe_merchant_new_campaign=1 ) ";//and customer_id=226";

/*
$query =  "SELECT * FROM `customer_email_settings` e, customer_user c where c.id=e.customer_id and c.active=1 and c.curr_latitude!='' and c.curr_longitude!='' and
       ( e.subscribe_merchant_reserve_campaign=1 or e.subscribe_merchant_new_campaign=1 ) and customer_id=226";//249
*/
//echo $query;
$RS = $objDB->Conn->Execute($query);
 $main_array=array(); 
while($Row = $RS->FetchRow())
{
	$flag = false;
	$main_array=array();
	$body = "";
	$deals_blocks = "";
	$deal_count=0;
	//echo $Row['customer_id']."==";
	//$body .= $Row['customer_id']."====";
	$mercahnt_radius = $Row['merchant_radius'];
	/*$body .= "<div style='clear:both;'>";
	//$body .= $Row['customer_id']."====";
	
	if($Row['firstname'] == "" && $Row['lastname'] == "" )
	{
		// User's firstname  and lastname
		$body .= "<div style='float:left;font-size:21px;font-weight:bold;width:361px;;'>Hello customer ,</div>";
	}
	else
	{
		// User's firstname  and lastname
		$body .= "<div style='float:left;font-size:17px;font-weight:bold;width:361px; padding-top: 10px;'>".$Row['firstname'] ." ".$Row['lastname']." , </div>";
	}
	$body .="<div style='float:right;margin-right: 15px;margin-top: 35px;'><p style=''>";
	$body .="<img alt='Scanflip' src='".WEB_PATH."/images/email_sharing.png'>";
	$body .="</p></div>";
	$body .= "</div>";*/   
	
	$body .='<center><body style="font-size:11px;font-family: arial,helvetica neue,helvetica,sans-serif; line-height: normal;color:#606060; margin:8px 0 0 0;">
<table style="width:100%; clear:both;" width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" align="center">
  <tbody>
    <tr>
        <td style="width:100%; clear:both;" width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" align="center">
          <table cellspacing="0" cellpadding="0" border="0" style=" width:100%; max-width:565px;">
            <tbody>
              <tr align="center" style="width:100%; display:inline-block;">
               <td align="center" valign="top" style=" display:block;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%; text-align:center; padding:0; width:100%;">
<div style=" display:inline-block;  text-align:left;">
  <img  alt="" src="'.ASSETS_IMG.'/c/e_left.png" style=" width:200px;max-width: 100%;padding-bottom: 0;border: 0;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;vertical-align: bottom;text-align:center; display:inline-block;">
</div>
<div style="display:inline-block;text-align: right;">
<img  alt="" src="'.ASSETS_IMG.'/c/e_right.png" style="padding-bottom: 0;border: 0;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;vertical-align: bottom;  width:264px; text-align:right; display:inline-block;">
</div>
</td>
              </tr>';
			  
	$customer_id = $Row['customer_id'];
	$user_latitude = $Row['curr_latitude'];
	$user_longitude = $Row['curr_longitude'];
	$tz =  $Row['curr_timezone'];

	// 06 02 2014
	// $user_timezone =  array_push($alltimezones,timezone_offset_string( $offset1 ));
	// 06 02 2014

	$curr_date_sql = "select DATE_FORMAT(CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','".$tz."') ,'%T') dt1";

	$curr_date = $objDB->Conn->Execute($curr_date_sql);
	// $res1 = mysql_fetch_assoc($curr_date);

	$comaparing_date_sql = "select  DATE_FORMAT(CONVERT_TZ('3-02-20 06:00:00','".CURR_TIMEZONE."','".$tz."') ,'%T') dt2";


	$curr_date2 = $objDB->Conn->Execute($comaparing_date_sql);


	$dt1 =date('H:i',strtotime($curr_date->fields['dt1']));
	$dt2 =date('H:i',strtotime($curr_date2->fields['dt2']));//$res2['dt2'];
	//echo $dt1." == ".$dt2;
	//echo "<br/>";
	
	if($tz !="" && $user_latitude !="" && $user_longitude !="")
	{
		if($dt1 == $dt2)
		{
			if($Row['subscribe_merchant_reserve_campaign'] == 1)
			{         
				$Sql = "SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,C.discount , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude ,l.phone_number ,l.country,
				(((acos(sin((".$user_latitude."*pi()/180)) *   sin((`latitude`*pi()/180))+cos((".$user_latitude."*pi()/180)) *  cos((`latitude`*pi()/180)) * cos(((".$user_longitude."- `longitude`)*   pi()/180))))*180/pi())*60*1.1515 ) as distance
				FROM campaigns C, customer_campaigns CC, categories CAT,locations l,campaign_location cl
				WHERE CC.activation_status=1 and CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date and cl.active=1
				and l.id=cl.location_id and cl.campaign_id=C.id and l.active=1 and CC.customer_id = '$customer_id' AND CC.campaign_id = C.id AND CAT.active =1 and CAT.id=C.category_id AND l.id= CC.location_id and CAT.active=1
				and (((acos(sin((".$user_latitude."*pi()/180)) *   sin((`latitude`*pi()/180))+cos((".$user_latitude."*pi()/180)) *  cos((`latitude`*pi()/180)) * cos(((".$user_longitude."- `longitude`)*   pi()/180))))*180/pi())*60*1.1515 ) <= ".$mercahnt_radius ." 
				ORDER BY C.created_date DESC";
				//echo  "===".$Sql."====1======";
				$RSStore = $objDB->Conn->Execute($Sql);
				$record_count=$RSStore->RecordCount();

					
				if($record_count>0)
				{
					$flag = true;
					/*
					$body .= '<tr style="width:100%; display:inline-block; padding-top:20px; padding-bottom:40px;">
					<td style="display:block;text-decoration: underline;font-size:24px;letter-spacing: 1px;font-weight:bold;line-height:30px;text-align: center; color:#606060;font-family: helvetica; padding:0 9px;">
					 Subscribed merchants reserved campaigns</td></tr>';
					 */
				}
				
				if($record_count>0)
				{
					/*
					$body .= '<tr style="width:100%; display:inline-block;">
					<td style="width:100%; display:inline-block;" align="center";>';
					*/
				}
				
				$arr = array();

				while($Row_my_deals = $RSStore->FetchRow())
				{
					$campaign_id = $Row_my_deals['id'];
					
					if(!in_array($campaign_id,$arr))  
					{
						$deal_count++;
						
						array_push($arr , $campaign_id);
						//$body .= get_deal($Row['customer_id'],$Row_my_deals['id'],$Row_my_deals['location_id'],$Row_my_deals['title'],$Row_my_deals['location_name'],$Row_my_deals['business_logo'],$Row_my_deals['address'],$Row_my_deals['city'],$Row_my_deals['state'],$Row_my_deals['zip'],$Row_my_deals['country'],$Row_my_deals['phone_number'], $Row_my_deals['latitude'],$Row_my_deals['longitude'],$Row_my_deals['category_id'] ,$Row_my_deals['distance'] ,$Row_my_deals['discount']);
						$deals_blocks .=get_deal($Row['customer_id'],$Row_my_deals['id'],$Row_my_deals['location_id'],$Row_my_deals['title'],$Row_my_deals['location_name'],$Row_my_deals['business_logo'],$Row_my_deals['address'],$Row_my_deals['city'],$Row_my_deals['state'],$Row_my_deals['zip'],$Row_my_deals['country'],$Row_my_deals['phone_number'], $Row_my_deals['latitude'],$Row_my_deals['longitude'],$Row_my_deals['category_id'] ,$Row_my_deals['distance'] ,$Row_my_deals['discount']);
					}
					
					if(!in_array($campaign_id,$main_array))  
					{
						$status=checkstatus($Row['customer_id'],$Row_my_deals['id'],$Row_my_deals['location_id']);
						if($status=="true")
						{
							array_push($main_array,$campaign_id);				
						}
					}
				}


				if($record_count>0)
				{
					//$body .="</td></tr>";
				}
				  
			}
			if($Row['subscribe_merchant_new_campaign'] == 1)
			{
				$sql ="SELECT c.id campaign_id ,c.category_id,c.title,c.discount, c.business_logo , l.id location_id , l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude , l.phone_number , l.country,
				(((acos(sin((".$user_latitude."*pi()/180)) *   sin((`latitude`*pi()/180))+cos((".$user_latitude."*pi()/180)) *  cos((`latitude`*pi()/180)) * cos(((".$user_longitude."- `longitude`)*   pi()/180))))*180/pi())*60*1.1515 ) as distance
				FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id left outer join customer_campaign_view views on views.campaign_id = cl.campaign_id
				WHERE l.active = 1 and c.is_walkin <> 1 
				and c.category_id in (select id from categories where active=1)
				and ( c.id in
				(select cg.campaign_id 
				from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id inner join merchant_groups mg on mg.id=ms.group_id where ms.user_id=".$customer_id.") 
				or c.level =1 
				)
				and l.id in 
				(SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.subscribed_status=1) 
				and (
				(c.id not in ( select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id))
				or (c.id in ( select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id and activation_status =0))
				)
				and (((acos(sin((".$user_latitude."*pi()/180)) *   sin((`latitude`*pi()/180))+cos((".$user_latitude."*pi()/180)) *  cos((`latitude`*pi()/180)) * cos(((".$user_longitude."- `longitude`)*   pi()/180))))*180/pi())*60*1.1515 ) <= ".$mercahnt_radius ." 
				AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0 
				and c.id not in( select campaign_id from customer_campaign_view where customer_id=".$customer_id.")";
				//echo  "===".$sql."====2=====";
				$RSStore = $objDB->Conn->Execute($sql);
				$record_count=$RSStore->RecordCount();
				

				if($record_count>0)
				{
					$flag = true;
					/*
					$body .= '<tr style="width:100%; display:inline-block; padding-top:20px; padding-bottom:40px;">
					<td style=" display:inline-block;text-decoration: underline;font-size:24px;letter-spacing: 1px;font-weight:bold;width:100%;line-height:30px;text-align: center; color:#606060;font-family: helvetica; width:100%;">
					 Subscribed merchants new campaigns</td></tr>';
					 */
					 
				}   
				
				if($record_count>0)
				{
					/*
					$body .= '<tr style="width:100%; display:inline-block;">
					<td style="width:100%; display:inline-block;" align="center";>';
					*/
				}
				
				$arr = array();

				while($Row_my_deals = $RSStore->FetchRow())
				{
					$campaign_id = $Row_my_deals['campaign_id'];
					
					if(!in_array($campaign_id,$arr))  
					{
						$deal_count++;
						
						array_push($arr , $campaign_id);
						//$body .= get_deal($Row['customer_id'],$Row_my_deals['campaign_id'],$Row_my_deals['location_id'],$Row_my_deals['title'],$Row_my_deals['location_name'],$Row_my_deals['business_logo'],$Row_my_deals['address'],$Row_my_deals['city'],$Row_my_deals['state'],$Row_my_deals['zip'],$Row_my_deals['country'],$Row_my_deals['phone_number'], $Row_my_deals['latitude'],$Row_my_deals['longitude'],$Row_my_deals['category_id'] ,$Row_my_deals['distance'] ,$Row_my_deals['discount']);
						$deals_blocks .=get_deal($Row['customer_id'],$Row_my_deals['campaign_id'],$Row_my_deals['location_id'],$Row_my_deals['title'],$Row_my_deals['location_name'],$Row_my_deals['business_logo'],$Row_my_deals['address'],$Row_my_deals['city'],$Row_my_deals['state'],$Row_my_deals['zip'],$Row_my_deals['country'],$Row_my_deals['phone_number'], $Row_my_deals['latitude'],$Row_my_deals['longitude'],$Row_my_deals['category_id'] ,$Row_my_deals['distance'] ,$Row_my_deals['discount']);
					}
					
					if(!in_array($campaign_id,$main_array))  
					{
						
						$status=checkstatus($Row['customer_id'],$Row_my_deals['campaign_id'],$Row_my_deals['location_id']);
						if($status=="true")
						{
							array_push($main_array,$campaign_id);				
						}
						
					}
				}

				if($record_count>0)
				{
					//$body .="</td></tr>";
				}
			}		       
		}
	}
	$body .= '<tr align="center" style="width:100%; display:inline-block; padding-top:20px; padding-bottom:40px;">
                <td align="center" style="width:100%;display:block;text-decoration: underline;font-size:24px;letter-spacing: 1px;font-weight:bold;line-height:30px;text-align: center; color:#606060;font-family: helvetica; padding:0;">
                 <div style="width:100%; display:inline-block;text-align: center;text-decoration:underline;">'.$merchant_msg['email']['subscribed_campaign_heading'].'</div></td></tr>';
	$body .= '<tr style="width:100%; display:inline-block;">
                <td style="width:100%; display:inline-block;" align="center">';
	$body .= $deals_blocks;			
	$body .="</td></tr>";
	
	$body .="</table>";
	$body .="</td></tr>";
	$body .="</table>";         
				
	$body .='<table style="background-color:#F2F2F2;width:100%; clear:both; padding:20px 0;font-size:11px;font-family: arial,helvetica neue,helvetica,sans-serif; line-height: normal;">       
 <tbody>
   <tr>
    <td style="width:100%; clear:both;" width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" align="center">
     <table style="width:100%;max-width:565px;" align="center">                        
     <tbody style="width:100%; display:inline-block;">
       <tr align="center" style="width:100%;display:inline-block;background:#FFF;">
         <td align="center" style="display:inline-block;padding: 5px 0;">
          <div style="width:auto; padding-right:10px; display:inline-block;font-size:10px;font-family:Arial,Helvetica Neue,Helvetica;">
           <a style="word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" target="_blank" href="'.$merchant_msg['email']['mail_facebook_link'].'"><img style="width: 48px;padding-bottom: 5px;max-width: 48px;display: block;border: 0;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnFollowBlockIcon" alt="Facebook" src="'.ASSETS_IMG.'/c/e_facebook.png"></a>
           <a style="display:inline-block;color: #333333;font-size: 10px;font-weight: normal;line-height: 100%;text-align: center;text-decoration: none;word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" target="_blank" href="'.$merchant_msg['email']['mail_facebook_link'].'">Facebook</a>
          </div>
         </td>
		 <td align="center" style="display:inline-block;padding: 5px 0;">
          <div style="width:auto; padding-right:10px; display:inline-block;font-size:10px;font-family:Arial,Helvetica Neue,Helvetica; ">
           <a style="word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" target="_blank" href="'.$merchant_msg['email']['mail_twitter_link'].'"><img   style="padding-bottom: 5px;width: 48px;max-width: 48px;display: block;border: 0;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" alt="Twitter" src="'.ASSETS_IMG.'/c/e_twiiter.png">
</a>
         <a style="display:inline-block;color: #333333;font-size: 10px;font-weight: normal;line-height: 100%;text-align: center;text-decoration: none;word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" target="_blank" href="'.$merchant_msg['email']['mail_twitter_link'].'">Twitter</a>
          </div>
         </td>
		 <td align="center" style="display:inline-block;padding: 5px 0;">
          <div style="width:auto; display:inline-block;font-size:10px;font-family:Arial,Helvetica Neue,Helvetica;">
           <a style="word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" target="_blank" href="'.$merchant_msg['email']['mail_google_link'].'"><img style="padding-bottom: 5px;width: 48px;max-width: 48px;display: block;border: 0;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" alt="Google Plus" src="'.ASSETS_IMG.'/c/e_google.png">
</a>      
        <a style="display:inline-block;color: #333333;font-size: 10px;font-weight: normal;line-height: 100%;text-align: center;text-decoration: none;word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" target="_blank" href="'.$merchant_msg['email']['mail_google_link'].'">Google Plus</a>
          </div>
         </td>
       </tr>         
       <tr style="width:100%; display:inline-block;">
     <td valign="top" style="padding:20px 0 0;color: #999999;font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size: 10px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;line-height: 125%;text-align: left;" class="mcnTextContent">
                        
                            <span style=" line-height:15px;font-family:arial,helvetica neue,helvetica,sans-serif"><span style="font-size:11px">This informational email has been sent to you as part of your ScanFlip membership. If you would like to stop receiving these emails, please click here to <span style="color:#0000FF"><a target="_new" style="text-decoration:none;" href="'.WEB_PATH.'/process.php?btnunsubscribeemail=yes&tag=new&id='.$Row['customer_id'].'">unsubscribe</a></span>, or visit the email settings page, and &nbsp;"Update&nbsp;<span style="color: #0D58E3;font-weight: lighter;"><a style="text-decoration: none;" target="_new" href="'.WEB_PATH.'/my-emailsettings.php">Manage my campaign subscription preferences</a></span></span></span>"<br>
<br>
<div style="text-align: left;line-height:15px;">Contact information: ScanFlip Corp .21133 Victory Blvd Suite# 201,&nbsp;Los &nbsp; Angeles, CA 91303. Please do not reply to this email, as we are unable to respond from this email address. &nbsp; &nbsp;</div>

<div style="width:100%;text-align:center; display:inline-block; padding-top: 25px;"><a style="text-decoration:none; border-right:1px solid #666; padding-right:8px;" target="_new" href="'.WEB_PATH.'/terms.php">Terms of Use</a>
<a style="padding-left:8px;text-decoration: none;" target="_new" href="'.WEB_PATH.'/privacy-assist.php">Privacy Policy.</a></div>

</div>

                        </td>        
   </tr>
     </tbody>
    </table>
    </td>
   </tr>
 </tbody>
</table>


</body></center>';
	
	//echo $deals_blocks;
	
	//echo count($main_array);
	
	//if($flag)
	if(count($main_array)>0)
	{
		//echo count($main_array);
		//echo $body;
		
		$mail = new PHPMailer();
		$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
		$mail->AddAddress($Row['emailaddress']);
		$mail->From = 'no-reply@scanflip.com';
		$mail->FromName = "ScanFlip Support";
		$mail->Subject    = "Scanflip campaigns from your subscribed merchants";    
		$mail->MsgHTML($body);
		$mail->Send();
		
	}
}
   
  
 // $body .= get_deal($Row['customer_id'],$Row_my_deals['id'],$Row_my_deals['city'],$Row_my_deals['state'],$Row_my_deals['zip'],$Row_my_deals['country'],$Row_my_deals['phone_number'], $Row_my_deals['latitude'],$Row_my_deals['longitude']);



	}	
	
	$array_values_as = $array_where_as = array();
	$array_values_as['action'] = 0;
	$array_where_as['id'] = 5;
	$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
}

function checkstatus($custid , $campid , $locid)
{
	$objDB = new DB();
	//echo "1";
	//echo $custid."-".$campid."-".$locid;
	$array_where_camp=array();
	$array_where_camp['campaign_id'] = $campid;
	$array_where_camp['customer_id'] = $custid;
	$array_where_camp['referred_customer_id'] = 0;
	$array_where_camp['location_id'] = $locid;
	$RS_camp = $objDB->Show("reward_user", $array_where_camp);
	
	////echo "<br/>2<br/>";
	//echo "2";
	$where_x1 = array();
	$where_x1['campaign_id'] = $campid;
	$where_x1['location_id'] = $locid;
	$campLoc = $objDB->Show("campaign_location", $where_x1);
	
	//echo "<br/>3<br/>";

	//$RS_camp->RecordCount()."-";
	//$campLoc->fields['offers_left'];
//echo "3";
	$where_camp = array();
	$where_camp['id'] = $campid;
	$campaign_record = $objDB->Show("campaigns", $where_camp);

	if($RS_camp->RecordCount()>0 && $campaign_record->fields['number_of_use']==1)
	{
		return "false";
	} 
	elseif ($RS_camp->RecordCount()>0 && ($campaign_record->fields['number_of_use']=2 || $campaign_record->fields['number_of_use']==3 ) && $campLoc->fields['offers_left']==0) 
	{
		return "false";
	}
	else
	{  
		return "true";
	}
	//echo "4";
} 
 
function get_deal($custid , $campid , $locid ,$camp_title ,$location_name ,$business_logo ,$address , $city ,$state ,$zip ,$country ,$phone_number , $latitude ,$longitude , $category_id ,$distance,$discount)
{
    $objDB = new DB();
  ////  echo "1";
     $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='.$locid);
                                                                                                        if(trim($arr[0]) == "")
                                                                                                             {
                                                                                                                     unset($arr[0]);
                                                                                                                     $arr = array_values($arr);
                                                                                                             }
                                                                                                             $json = json_decode($arr[0]);
                                                                                                        $busines_name  = $json->bus_name;
      $array_where_camp['campaign_id'] = $campid;
                        $array_where_camp['customer_id'] = $custid;
                        $array_where_camp['referred_customer_id'] = 0;
                        $array_where_camp['location_id'] = $locid;
                        $RS_camp = $objDB->Show("reward_user", $array_where_camp);
////echo "<br/>2<br/>";
                         $where_x1 = array();
                         $where_x1['campaign_id'] = $campid;
                         $where_x1['location_id'] = $locid;
                         $campLoc = $objDB->Show("campaign_location", $where_x1);
//echo "<br/>3<br/>";
                    
                        //$RS_camp->RecordCount()."-";
                        //$campLoc->fields['offers_left'];
						 
						 $where_camp = array();
                         $where_camp['id'] = $campid;
                         $campaign_record = $objDB->Show("campaigns", $where_camp);
						 
						
                        if($RS_camp->RecordCount()>0 && $campaign_record->fields['number_of_use']==1)
                        {
                        } 
                        elseif ($RS_camp->RecordCount()>0 && ($campaign_record->fields['number_of_use']=2 || $campaign_record->fields['number_of_use']==3 ) && $campLoc->fields['offers_left']==0) 
                        {

                        }
                        else
                        {  
                            $body = "";
                          //  $body .=" ,<br />";
                            
                            $lid = $locid;
                            $cid = $campid;
                            $cust_id =$custid;
//                            $array2 = $json_array = array();
//      $array2['id'] =$lid ;
//      $RS_location = $objDB->Show("locations",$array2);
//   
//      
//      $array1 = $json_array = array();
//      $array1['id'] =$cid;
//      $RS_campaigns = $objDB->Show("campaigns",$array1);
//      
//      $array2 = $json_array = array();
//      $array2['id'] =$cust_id ;
//      $RS_camp_mer = $objDB->Show("merchant_user",$array2);
   
		/// Make css chages here ///
		
		$web_path = WEB_PATH;
		$image_path="";	
		
		if($business_logo !="")
		{
			$img_src=ASSETS_IMG."/m/campaign/block/".$business_logo; 
			$image_path=UPLOAD_IMG."/m/campaign/block/".$business_logo; 
		}
		else 
		{
			$img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
		}
		if (file_exists($image_path))
		{
		}
		else
		{
			$img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
		}
		
		if(strlen($camp_title) > 50)
		{
			$e_str = "..";
		}
		else
		{
			$e_str = "";
		}
		$camp_title = substr($camp_title,0,50).$e_str;
		
		$campaign_value=$campaign_record->fields['deal_value'];
		$campaign_discount=$campaign_record->fields['discount'];
		$campaign_saving=$campaign_record->fields['saving'];
		
		//$activate_link = WEB_PATH."/register.php?campaign_id=".$campid."&l_id=".$locid."&share=true&customer_id=".base64_encode($custid)."&domain=4";
		$activate_link = $campLoc->fields['permalink']."?domain=4";
		
		$body .='<div class="col1" vertical-align="top" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;border:3px solid #FFF;width:180px ; display:inline-block;">
		<a target="_new" height="260" href="'.$activate_link.'" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;display:inline-block; width:170px;font-size:12px;text-decoration:none;color:#606060;text-align: center;padding: 5px;border-top:1px solid #d2d2d2;border-right:1px solid #d2d2d2;border-left:1px solid #d2d2d2;border-bottom:1px solid #d2d2d2;"> 
                  <div style="width:168px; height:164px;" ><img  alt="" src="'.$img_src.'" style="width:168px; height:164px;border: 0;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;vertical-align: top; padding: 0 0 9px;" /></div>';
			$body .='<div style="width: 100%; display: inline-block; height: 45px;">';
			if($campaign_value!="0")
			{	
				$body .='<div class="Campaign Value" style=" text-align: center;width:100%;float:left; padding-bottom:5px;font-size:11px;font-family:arial,helvetica neue;">
			    
				 
				 
			 <table align="center"  style="width:100%; display:inline-block;text-align: center;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
			   <tbody style="width:100%; display:inline-block;">
				 <tr align="center" style="width:100%; display:inline-block;padding:0px;">
				  <td style="display: inline-block; width:50px;padding:0px;">
				    <div style="-webkit-text-adjust:none;display: inline-block;font-weight:normal;text-align: center;font-size:10px;font-family:arial"><font style="font-size:100%;">Value</font></div>        
				  </td>
				  <td style="display: inline-block; width:60px;padding:0px;">
				  
				  <div style="-webkit-text-adjust:none;border-left:1px solid #606060;border-right:1px solid #606060;display:block;font-weight:normal;text-align: center;font-size:10px;font-family:arial; padding:0 5px;"><font style="font-size:100%;">Discount</font></div>
				  
				  </td>
				  
				  <td style="display: inline-block; width:50px;padding:0px;">
				 
				  <div style="-webkit-text-adjust:none;display: inline-block;font-weight:normal;text-align: center;font-size:10px;font-family:arial"><font style="font-size:100%;">Savings</font></div>
				  
				  </td>
				  
				  
				 </tr>        
				 
				 
				  <tr align="center" style="width:100%; display:inline-block;padding:0px;">
				  <td style="display: inline-block; padding:0px;width: 50px;">
				  
				    <div style="-webkit-text-adjust:none;display: inline-block;font-weight:normal;text-align: center;font-size:10px;font-family:arial">$'.$campaign_value.'</div>
				  </td>
				  <td style="display: inline-block;padding:0px;width:60px;">
				  
				  <div style="-webkit-text-adjust:none;border-left:1px solid #606060;border-right:1px solid #606060;display:block;font-weight:normal;text-align: center;font-size:10px;font-family:arial; padding:0 5px; ">'.$campaign_discount.'%</div>
				  
				  </td>
				  
				  <td style="display: inline-block;padding:0px;width: 50px;">
				 
				  <div style="display: inline-block;font-weight:normal;text-align: center;font-size:10px;font-family:arial">$'.$campaign_saving.'</div>
				  
				  </td>
				  
				  
				 </tr> 
				 
			 
			   </tbody>
			 </table>
			 
				 
				 
				 
				 
				</div>';
			}
			$body .='</div>';
			$body .='
			
			 <table style="width:100%; display:inline-block;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
			  <tbody style="width:100%; display:inline-block;">
			   <tr style="width:100%; display:inline-block;">
			    <td id="'.$campaign_record->fields['id'].'" height="31" style="width:100%; display:inline-block;overflow:hidden;color:#606060;font-family: arial,helvetica neue,helvetica,sans-serif;text-align:left;font-size: 12px; padding: 0px ;">'.$camp_title.'</td>
			   </tr>
			  </tbody>
			 </table>
			
			
							 </a></div>';
            
        return $body;
    }                 
}
?>
