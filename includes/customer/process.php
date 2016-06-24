<?php
/******** 
@USE : customer side functions
@PARAMETER : 
@RETURN : 
@USED IN PAGES : 
*********/

require_once(LIBRARY."/class.phpmailer.php");
include(LIBRARY.'/simpleimage.php');
include LIBRARY.'/locu.php';
require_once(LIBRARY."/PHP-PasswordLib-master/lib/PasswordLib/PasswordLib.php"); 

/**
 * @uses get customer's session id
 * @param type $session_data sesseion data
 * @used in pages : scanflip/process.php
 * @author Sangeeta
 * @return type $session_id
 */
function get_cutomer_session_id($session_data){
	global $objDB;
	
	/*$Sql = "SELECT * FROM user_sessions WHERE session_data='".mysql_escape_string($session_data)."' ORDER BY id DESC LIMIT 1";
        $RS = $objDB->Conn->Execute($Sql);*/
	$RS = $objDB->Conn->SelectLimit("SELECT * FROM user_sessions WHERE session_data=? ORDER BY id DESC",1,0,array(mysql_escape_string($session_data)));
        
        //$RS = $objDB->Conn->SelectLimit("SELECT * FROM user_sessions WHERE session_data=? ORDER BY id DESC", 1,-1,array(mysql_escape_string($session_data)); 
	
	if($RS->RecordCount()>0){ 
		/*$Sql = "DELETE FROM user_sessions WHERE session_id='".$RS->fields['session_id']."' AND sessiontime < '".strtotime(date("Y-m-d"))."'";
		$objDB->Conn->Execute($Sql);*/
		$objDBWrt->Conn->Execute("DELETE FROM user_sessions WHERE session_id=? AND sessiontime < ?",array($RS->fields['session_id'],strtotime(date("Y-m-d"))));
		$session_id = base64_decode($RS->fields['session_id']);
		return $session_id;
	}	
	return "";
	
}

/**
 * @uses check user session 
 * @used in pages : no use
 * @author Sangeeta
 * @return type array
 */

if(isset($_REQUEST['checkusersession']))
{
    if(isset($_SESSION['customer_id']))
    {
        $json_array['status'] = "true";
    }
    else{
        $json_array['status'] = "false";
    }
    $json = json_encode($json_array);
	echo $json;
	exit();   
}
/**
 * @uses process on login form submit
 * @param emailaddress
 * @used in pages : scanflip/location_detail.php,login.php,register.php,mymerchants.php,my-deals.php,popup-detail.php,popup-for-mydeal.php,search-deal.php
 * @author Sangeeta
 * 
 */


if(isset($_REQUEST['btnLogin'])){

	$allow_for_reserve = "";
	$its_new_user = "";
	
	if(isset($_REQUEST['emailaddress']) && $_REQUEST['emailaddress']!="")
	{
	}
	else
	{
		$json_array['status'] = "false";
		$json_array['message'] = $client_msg["login_register"]["Msg_enter_email"];
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$array = $json_array = array();
	$array['emailaddress'] = $_REQUEST['emailaddress'];
	
	$RS = $objDB->Show("customer_user",$array);
	$total=$RS->RecordCount();
	if($total == 0){
		$json_array['status'] = "false";
		$json_array['message'] = $client_msg["login_register"]["Msg_Email_not_registerd"];
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
	$PasswordLib2 = new \PasswordLib\PasswordLib;
	
	if($RS->fields['password']=="")
	{
		$result=0;
	}
	else
	{
		$result = $PasswordLib2->verifyPasswordHash($_REQUEST['password'],$RS->fields['password']);
	}
	
	if(!$result)
	{
		$json_array['status'] = "false";
		$json_array['message'] = $client_msg["login_register"]["Msg_Email_Password"];
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$array = $json_array = array();
	$array['emailaddress'] = $_REQUEST['emailaddress'];
	
	$RS = $objDB->Show("customer_user",$array);
	
	if($RS->fields['active'] == 0){
		$json_array['status'] = "false";
		$json_array['message'] = $client_msg["login_register"]["Msg_Not_Activated"];
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
        if(isset($_REQUEST['hdn_is_activationcode']))
        {
            $cookie_time = time()+(20 * 365 * 24 * 60 * 60);
            //setcookie("code",$_REQUEST['hdn_is_activationcode'],$cookie_time,'','','');
        }
	$Row = $RS->FetchRow();
       
$_SESSION['customer_id'] = $Row['id'];

            $cookie_time = time()+(20 * 365 * 24 * 60 * 60);
           setcookie("scanflip_customer_id",$Row['id'],$cookie_time,'','','');
        $cid = $Row['id'];
	$_SESSION['customer_info'] = $Row;
	
	$array_values = $where_clause = $array = array();
	$array_values['lastvisit_date'] = date("Y-m-d H:i:s");
	$where_clause['id'] = $_SESSION['customer_id'];
	$objDB->Update($array_values, "customer_user", $where_clause);
	
	// 07-11-2013
	$cust_login = array();
	$cust_login['customer_id'] = $_SESSION['customer_id'];
	$cust_login['last_login'] = date("Y-m-d");
	$objDB->Insert($cust_login, "customer_login");
	// 07-11-2013
	
	$user_session = session_id();
	$array['sessiontime'] = strtotime(date("Y-m-d H:i:s")); 
	$array['session_id'] = base64_encode($_SESSION['customer_id']); 
	$array['session_data'] = md5($array['sessiontime'].$user_session); 
	//$array['user_type'] = 1;
	$objDB->Insert($array, "user_sessions");		
	
	$json_array['status'] = "true";
	$json_array['customer_id'] = $array['session_data'];
	
	
	$cookie_email = $_REQUEST['emailaddress'];
	$cookie_password = $_REQUEST['password'];
	$cookie_time = time()+60*60*24*30;
        if(isset($_REQUEST['keepme'])){
	$rememberme = $_REQUEST['keepme'];
        }
	
        $json_array['share'] = "false";
        
        if( isset( $_COOKIE['share'] ) )
         {
		 
			// 18-03-2014 pageview counter logic		
			$array_loc=array();
			$array_loc['id'] = $_COOKIE['l_id'];
			$RS_location = $objDB->Show("locations",$array_loc);
			$time_zone=$RS_location->fields['timezone_name'];
			//date_default_timezone_set($time_zone);
			
			$pageview_array = array();
			$pageview_array['campaign_id'] = $_COOKIE['campaign_id'];
			$pageview_array['location_id'] = $_COOKIE['l_id'];;
			$pageview_array['pageview_domain'] = $_REQUEST['domain'];
			$pageview_array['pageview_medium'] = $_REQUEST['medium'];
			$pageview_array['timestamp'] = $_REQUEST['hdn_user_datetime'];
			$objDB->Insert($pageview_array, "pageview_counter");
			$pageview_array = array();
			
			// 18-03-2014 pageview counter logic
	
            $json_array['share'] = "true";
            $json_array['c_id'] = $_COOKIE['campaign_id'];
             $json_array['l_id'] = $_COOKIE['l_id'];
			 
			/* $redirect_query = "select permalink from campaign_location where campaign_id=".$_COOKIE['campaign_id']." and location_id=".$_COOKIE['l_id'];
				$redirect_RS = $objDB->Conn->Execute($redirect_query );*/
				$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($_COOKIE['campaign_id'],$_COOKIE['l_id']));

				$json_array['permalink'] =  $redirect_RS->fields['permalink'];
			 
			 
             /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list */
        
			 /* $Sql_max_is_walkin = "SELECT is_walkin , new_customer, level  from campaigns WHERE id=".$_COOKIE['campaign_id'];
                        
			   $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin);*/
				$RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin , new_customer, level  from campaigns WHERE id=?",array($_COOKIE['campaign_id']));

			 //  if($RS_max_is_walkin->fields['is_walkin'] == 1)
			 //  {
		/*$Sql_c_c = "SELECT * FROM customer_campaigns WHERE customer_id='".$cid."' AND campaign_id='".$_COOKIE['campaign_id']."' AND location_id =".$_COOKIE['l_id'];
          
		$RS_c_c = $objDB->Conn->Execute($Sql_c_c);*/
		$RS_c_c = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?",array($cid,$_COOKIE['campaign_id'],$_COOKIE['l_id']));
		 if($RS_c_c->RecordCount()<=0){
			 /*$Sql = "SELECT * FROM activation_codes WHERE campaign_id=".$_COOKIE['campaign_id'];
                        
				$RS_1 = $objDB->Conn->Execute($Sql);*/
				$RS_1 = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE campaign_id=?",array($_COOKIE['campaign_id']));

				if($RS_1->RecordCount()>0){
                                    /*$location_max_sql = "Select num_activation_code , offers_left,used_offers from campaign_location where  campaign_id=".$_COOKIE['campaign_id']." and location_id=".$_COOKIE['l_id'];
         $location_max = $objDB->Conn->Execute($location_max_sql);*/
				$location_max = $objDB->Conn->Execute("Select num_activation_code , offers_left,used_offers from campaign_location where  campaign_id=? and location_id=?",array($_COOKIE['campaign_id'],$_COOKIE['l_id']));

          $offers_left = $location_max->fields['offers_left'];
                    $used_campaign = $location_max->fields['used_offers'];
           $o_left = $location_max->fields['offers_left'];
           $share_flag = 1;
           $its_new_user = 0;
     
           // RESERVE DEAL LOGIC
           if(  $o_left>0)
            {
               if($RS_max_is_walkin->fields['new_customer'] == 1)
               {
                   /*  $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=".  $_COOKIE['l_id'].")";
                     //select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=".  $_COOKIE['l_id'].")
                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
			$subscibed_store_rs =$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($cid,$_COOKIE['l_id']));
                        if($subscibed_store_rs->RecordCount()==0)
                        {
                             $its_new_user =1;
                            $share_flag= 1;
                        }
                        else {
                             $its_new_user =0;
                              $share_flag= 0;
                        }
               }
               
               /* check whether new customer for this store */
                  $allow_for_reserve= 0;
               $is_new_user= 0;
               
              /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=". $_COOKIE['l_id'].") ";
          //     echo "sql_check===".$sql_chk."<br/>";
                                              $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk);*/
						$Rs_is_new_customer=$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($cid,$_COOKIE['l_id']));

                                            if($Rs_is_new_customer->RecordCount()==0)
                                            {
                                                $is_new_user= 1;
                                            }
                                            else {
                                                  $is_new_user= 0;
                                            }
                   
                if($is_new_user == 1)
                {
                    $allow_for_reserve = 1;
                }
                else {
                    
                
               /*$sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                
                                $RS_campaign_groups = $objDB->Conn->Execute($sql);*/
				$RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($_COOKIE['campaign_id'],$_COOKIE['l_id']));

                                $c_g_str = "";
                                $cnt =1;
                               
                                    $is_it_in_group = 0;
                                if($RS_max_is_walkin->fields['level']== 0)
                                {
                                    ///
                                    if($RS_max_is_walkin->fields['is_walkin'] == 0)
                                    {
                                    if($RS_campaign_groups->RecordCount()>0)
                                    {
                                     while($Row_campaign = $RS_campaign_groups->FetchRow())
                                        { 
                                            $c_g_str = $Row_campaign['group_id'];
                                            if($cnt != $RS_campaign_groups->RecordCount())
                                            {
                                                $c_g_str .= "," ;
                                            }
                                        }
                                   /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$cid."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                               
                                         $RS_check_s = $objDB->Conn->Execute($Sql_new_);  */   
					$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?)  )",array($cid,$c_g_str));    
      
                                      while($Row_Check_Cust_group = $RS_check_s->FetchRow())
                                                {
                                                    /*$query = "Select * from merchant_subscribs where  user_id='".$cid."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                     $RS_query = $objDB->Conn->Execute($query);*/
							$RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ",array($cid,$Row_Check_Cust_group['group_id'],$c_g_str));

                                                     if($RS_query->RecordCount() > 0)
                                                     {

                                                          $is_it_in_group = 1;
                                                     }
                                                }
                                      if($is_it_in_group == 1 )
                                            { 
                                                  $allow_for_reserve = 1;  	
                                            }
                                           else {
                                                $allow_for_reserve = 0;
                                           }
                                           }
                                            else{
                                                $allow_for_reserve = 0;  	
                                            }
                                     }
                                     //If it is walkin deal
                                     else{
                                       // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                          /*$query = "Select * from merchant_subscribs where  user_id=".$cid." and group_id=( select id from merchant_groups mg where mg.location_id=".$_COOKIE['l_id']." and mg.private =1 ) ";
                                        
                                          $RS_all_user_group = $objDB->Conn->Execute($query);*/
						$RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =? ) ",array($cid,$_COOKIE['l_id'],1));
                                          if($RS_all_user_group->RecordCount() > 0)
                                          {
                                               $allow_for_reserve = 1;
                                          }
                                          else
                                          {
                                              $allow_for_reserve = 0;  
                                          }
                                     }
                                }
                                else
                                {
                                 //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                     $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$cid."' AND group_id in( select  id from merchant_groups where location_id  =".$_COOKIE['l_id']."  )";
                                     $allow_for_reserve= 1;
                                }
                                }
                               
                            /* for checking whether customer in campaign group */
                        
                /* check whether new customer for this store */

               if($share_flag== 1)
               {
                   if($allow_for_reserve==1 ||  $its_new_user ==1){
                     $activation_code = $RS_1->fields['activation_code'];
								/*$Sql = "INSERT INTO customer_campaigns SET  activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
							customer_id='".$cid."', campaign_id=".$_COOKIE['campaign_id']." , location_id=".$_COOKIE['l_id'];
					$objDB->Conn->Execute($Sql);*/
					$arr =array('1',$activation_code,'Now()','Now()',$cid,$_COOKIE['campaign_id'],$_COOKIE['l_id']);
					$objDBWrt->Conn->Execute('insert into customer_campaigns (activation_status,activation_code,activation_date,coupon_generation_date,customer_id,campaign_id,location_id) values(?,?,?,?,?,?,?)',$arr);

                                      
                                      //$RSLocation_nm  =  $objDB->Conn->Execute("select * from locations where id =".$_COOKIE['l_id']);
					$RSLocation_nm  =  $objDB->Conn->Execute("select * from locations where id =?",array($_COOKIE['l_id']));
		
                                    //$br = $cid.substr($activation_code,0,2).$_COOKIE['campaign_id'].substr($RSLocation_nm->fields['location_name'],0,2).$_COOKIE['l_id'];
                                    $br = $objJSON->generate_voucher_code($cid,$activation_code,$_COOKIE['campaign_id'],$RSLocation_nm->fields['location_name'],$_COOKIE['l_id']);
                                        /*$insert_coupon_code = "Insert into coupon_codes set customer_id=".$cid." , customer_campaign_code=".$_COOKIE['campaign_id']." , coupon_code='".$br."' , active=1 , location_id=".$_COOKIE['l_id']." , generated_date='".date('Y-m-d H:i:s')."' ";
      
                                     $objDB->Conn->Execute($insert_coupon_code);*/
					$insert_coupon_code =array($cid,$_COOKIE['campaign_id'],$br,1,$_COOKIE['l_id'],date('Y-m-d H:i:s'));
                                     $objDBWrt->Conn->Execute('insert into coupon_codes (customer_id,customer_campaign_code,coupon_code,active,location_id,generated_date) values(?,?,?,?,?,?)',$insert_coupon_code);
                                     
                                       /*$update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$_COOKIE['campaign_id']." and location_id =".$_COOKIE['l_id']." ";
                        $objDB->Conn->Execute($update_num_activation);*/
			/*$update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$_COOKIE['campaign_id']." and location_id =".$_COOKIE['l_id']." ";
                                        $objDB->Conn->Execute($update_num_activation);*/
                                        $objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=? and location_id =? ",array(($offers_left-1),($used_campaign+1),$_COOKIE['campaign_id'],$_COOKIE['l_id']));
                   }
               }
                                      }
                                      // END OF RESERVE DEAL LOGIC
				}
				
		  }
			  // }
			 /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list  */
                           
                            /* check for whether max sharing reached and user is firt time subscribed to this loaction*/
			 /*$Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$_COOKIE['campaign_id'];
			   $RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location);*/
			$RS_max_no_location = $objDB->Conn->Execute( "SELECT max_no_sharing from campaigns WHERE id=?",array($_COOKIE['campaign_id']));

			 $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$_COOKIE['campaign_id']." and referred_customer_id<>0";
			// $RS_shared = $objDB->Conn->Execute($Sql_shared);

			//if($RS_shared->RecordCount() < $RS_max_no_location->fields['max_no_sharing'] ){
                           //  $sql_chk ="select * from subscribed_stores where customer_id= ".$cid." and location_id=". $_COOKIE['l_id'];
                          /*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=".  $_COOKIE['l_id'].")";
                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
			$subscibed_store_rs =$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)",array($cid,$_COOKIE['l_id']));

                        if($allow_for_reserve==1 || $its_new_user ==1)
                        {
                        	$redeem_array = array();
							$redeem_array['customer_id'] =  base64_decode($_COOKIE['customer_id']);//$_SESSION['customer_id'];
							$redeem_array['campaign_id'] = $_COOKIE['campaign_id'];
							$redeem_array['earned_reward'] = 0;
							$redeem_array['referral_reward'] = 0;
							$redeem_array['referred_customer_id'] = $cid;
							$redeem_array['reward_date'] =  date("Y-m-d H:i:s");
							$redeem_array['coupon_code_id'] =  0;
							$redeem_array['location_id'] =   $_COOKIE['l_id'];
							$objDB->Insert($redeem_array, "reward_user");
					
					
							
                        }

			// }
			 /* check whether maximum share reached / sharing count now 0 so send mail to merchant*/
			 
			 /*$Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$_COOKIE['campaign_id']." and referred_customer_id<>0";
			 $RS_shared = $objDB->Conn->Execute($Sql_shared);*/
			 $RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?",array($_COOKIE['campaign_id'],0));
			 
		 	 /*$Sql_active = "SELECT active,offers_left from campaign_location WHERE campaign_id=".$_COOKIE['campaign_id']." and location_id=".$_COOKIE['l_id'];
			 $RS_active = $objDB->Conn->Execute($Sql_active);*/
			 $RS_active = $objDB->Conn->Execute("SELECT active,offers_left from campaign_location WHERE campaign_id=? and location_id=?",array($_COOKIE['campaign_id'],$_COOKIE['l_id']));
			 
			 if($RS_shared->RecordCount() <= $RS_max_no_location->fields['max_no_sharing'] && $RS_active->fields['offers_left'] > 0 && $RS_active->fields['active']){

				/*$Sql_created_by = "SELECT created_by from locations WHERE id=".$_COOKIE['l_id'];
				
			        $RS_created_by = $objDB->Conn->Execute($Sql_created_by);*/
			        $RS_created_by = $objDB->Conn->Execute("SELECT created_by from locations WHERE id=?",array($_COOKIE['l_id']));
				
				$merchantid = $RS_created_by->fields['created_by'];
				
				/*$Sql_merchant_detail = "SELECT * from merchant_user WHERE id=".$merchantid;
			        
				$RS_merchant_detail = $objDB->Conn->Execute($Sql_merchant_detail);*/
				$RS_merchant_detail = $objDB->Conn->Execute("SELECT * from merchant_user WHERE id=?",array($merchantid));
				
				/*$Sql_campaigns_detail = "SELECT * from campaigns WHERE id=".$_COOKIE['campaign_id'];
				$RS_campaigns_detail = $objDB->Conn->Execute($Sql_campaigns_detail);*/
				$RS_campaigns_detail = $objDB->Conn->Execute("SELECT * from campaigns WHERE id=?",array($_COOKIE['campaign_id']));
				
				$mail = new PHPMailer();
				
				$merchant_id=$RS_merchant_detail->fields['id'];
				$email_address=$RS_merchant_detail->fields['email'];
				//$email_address="test.test1397@gmail.com";
			
				$body="<div>Hello,<span style='font-weight:bold'>".$RS_merchant_detail->fields['firstname']." ".$RS_merchant_detail->fields['lastname']."</span></div>";
				$body.="<br>";
				$body.="<div>Congratulations! Sharing points for <span style='font-weight:bold'>".$RS_campaigns_detail->fields['title']."</span> were allocated for new customer referral. 
				Please <a herf='".WEB_PATH."/merchant/register.php' > log in </a> if you wish to update number of referral customers limit for your campaign  . </div>";
			        $body.="<br>";
				$body.="<div>Sincerely,</div>";
				$body.="<div>Scanflip Support Team</div>";
                                				
				$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
				
				$mail->AddAddress($email_address);
				
				$mail->From = "no-reply@scanflip.com";
				$mail->FromName = "ScanFlip Support";
				$mail->Subject    = "Scanflip offer - ".$RS_campaigns_detail->fields['title'];
				$mail->MsgHTML($body);
				$mail->Send();
				
			 }
			
                            //Make entry in subscribed_stre table for first time subscribe to loaction
                        /*$sql_group = "select id , merchant_id from merchant_groups where location_id =". $_COOKIE['l_id']." and private = 1";
                                    $RS_group = $objDB->Conn->Execute($sql_group);*/
$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?",array($_COOKIE['l_id'],1));
         
                          /*$sql_chk ="select * from subscribed_stores where customer_id= ".$cid." and location_id=".$_COOKIE['l_id'];
                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
                          $subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=?",array($cid,$_COOKIE['l_id']));
                           
                        if($subscibed_store_rs->RecordCount()==0)
                        {
                        /*$insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$cid." ,location_id=".$_COOKIE['l_id']." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                          $objDB->Conn->Execute($insert_subscribed_store_sql);*/
			$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id= ? ,location_id=? ,subscribed_date=? ,subscribed_status=?",array($cid,$_COOKIE['l_id'],date('Y-m-d H:i:s'),1));

                        }
                        else {
                            if($subscibed_store_rs->fields['subscribed_status']==0)
                            {
                                /*$up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$cid." and location_id=".$_COOKIE['l_id'];
                                 $objDB->Conn->Execute($up_subscribed_store);*/
				$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id= ? and location_id=?",array($cid,$_COOKIE['l_id']));
                            }
                        }
                        /* check whether share user in stores's private group */
                        /*$Sql = "SELECT * FROM merchant_subscribs WHERE user_id=".$cid." and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=".$_COOKIE['l_id']."  )";
                         $RS_new = $objDB->Conn->Execute($Sql);*/
                         $RS_new = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id in( select id merchant_groups from merchant_groups where private=? and location_id=?  )",array($cid,1,$_COOKIE['l_id']));

                             if($RS_new->RecordCount()<=0){
                                     /*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$_COOKIE['l_id']." and private = 1";
                                     $RS_group = $objDB->Conn->Execute($sql_group);*/
                                     $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?",array($_COOKIE['l_id'],1));
                                 $array_group= array();
                                         $array_group['merchant_id']=$RS_group->fields['merchant_id'];
                                         $array_group['group_id']=$RS_group->fields['id'];
                                         $array_group['user_id']= $cid;
                                         $objDB->Insert($array_group, "merchant_subscribs");
                             }
                        //   
                  
         
                $cookie_time = time();
                   setcookie("share","true",$cookie_time-3600,'','','');
		   setcookie("campaign_id",$_REQUEST['campaign_id'],$cookie_time-3600,'','','');
                   setcookie("l_id",$_REQUEST['l_id'],$cookie_time-3600,'','','');
                   setcookie("customer_id",-$_REQUEST['customer_id'],$cookie_time-3600,'','','');
         }
      
	if(isset($_REQUEST['keepme']))
	{
		setcookie("cookie_email_cust",$cookie_email,$cookie_time,'','','');
		//setcookie("cookie_password_cust",$cookie_password,$cookie_time,'','','');
	}
	else
	{
		$time = time();
		setcookie("cookie_email_cust",'',$time - 3600,'','','');
		//setcookie("cookie_password_cust",'',$time - 3600,'','','');
	}
		$json_array['redirecting_path'] = "";
		if(isset($_REQUEST['hdn_reload_pre_selection']))
		{
			$previous_criteria = $_REQUEST['hdn_reload_pre_selection'];
			$c_arr =  explode("#",$previous_criteria);
			$json_array['redirecting_path'] =check_campaign_criteria($c_arr[0],$c_arr[1]);
		}
		if(isset($_REQUEST['hdn_reload_pre_selection_loc']))
		{
			$previous_criteria = $_REQUEST['hdn_reload_pre_selection_loc'];
			$c_arr =  explode("#",$previous_criteria);
			/*$redirect_query_location = "select location_permalink from locations where id=".$c_arr[1];
			 $RS_redirect_query = $objDB->Conn->Execute($redirect_query_location);	*/
				$RS_redirect_query = $objDB->Conn->Execute("select location_permalink from locations where id=?",array($c_arr[1]));	
	
			 $location_url = $RS_redirect_query->fields['location_permalink'];		
		$url_perameter_2 = rawurlencode("@".$c_arr[0]."|".$c_arr[1]."@");			 
			$json_array['redirecting_path'] = $location_url."#".$url_perameter_2;
		}
		
		// 07-11-2014 location page reserve , register page normal login goes to searchdeal page so remove redirecting path
		
		$json_array['redirecting_path'] = "";
		
		// 07-11-2014 location page reserve , register page normal login goes to searchdeal page so remove redirecting path
		
       $json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses register customer
 * @param emailaddress,password
 * @used in pages : scanflip/location_details.php,my-deals.php,mymerchants.php,register.php,search-deal.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnRegister'])){
	
	$array = $json_array = array();
	$array['emailaddress'] = $_REQUEST['emailaddress'];
	$array['is_registered'] = 1;
	//echo "<pre>";print_r($_REQUEST);echo "</pre>";
	$RS = $objDB->Show("customer_user",$array);
        
	if($RS->RecordCount()>0){
		$json_array['status'] = "false";
		$json_array['message'] = $client_msg["login_register"]["Msg_Email_Already"];
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
       
    $array_chk = array();
	$array_chk['emailaddress'] = $_REQUEST['emailaddress'];
	
	$RS_check = $objDB->Show("customer_user",$array_chk);
	if($RS_check->RecordCount()>0)
	{
		$where_clause = $array = array();
		
		$flag_share = false;
        ///$password = $_REQUEST['password'];
		$PasswordLib = new \PasswordLib\PasswordLib;
		//$hash = $PasswordLib->createPasswordHash($password);
		$array['password'] = $PasswordLib->createPasswordHash($_REQUEST['password']);
		$array['emailnotification'] = 1;
		$array['active'] = 1;
		$array['notification_setting'] = 1;
		$array['is_registered'] = 1;
		$where_clause['id'] = $RS_check->fields['id'];
		$objDBWrt->Update($array, "customer_user", $where_clause);
		$c_id = $RS_check->fields['id'];
	}
	else
	{
		$flag_share = false;
        ///$password = $_REQUEST['password'];
		$PasswordLib = new \PasswordLib\PasswordLib;
		//$hash = $PasswordLib->createPasswordHash($password);
		$array['password'] = $PasswordLib->createPasswordHash($_REQUEST['password']);
		$array['registered_date'] = date("Y-m-d H:i:s");
		$array['emailnotification'] = 1;
		$array['active'] = 1;
		$array['notification_setting'] = 1;
		$array['is_registered'] = 1;
		$c_id = $objDBWrt->Insert($array, "customer_user");
	}
	
       // insert default email settings into customer_campaign_settings
        $array_settings = array();
        $array_settings['campaign_email'] = 1;
	$array_settings['subscribe_merchant_new_campaign'] = 1;
	$array_settings['subscribe_merchant_reserve_campaign'] = 1;
        $array_settings['customer_id'] = $c_id ;
        $array_settings['merchant_radius'] = 1;
       
	$objDBWrt->Insert($array_settings, "customer_email_settings");
	
        // insert default email settings into customer_campaign_settings
        
        /* for share campaign */
      
           $objDB->Conn->StartTrans();
        if( isset( $_COOKIE['share'] ) )
         {
		 
			// 18-03-2014 pageview counter logic
					
			$array_loc=array();
			$array_loc['id'] = $_COOKIE['l_id'];
			$RS_location = $objDB->Show("locations",$array_loc);
			$time_zone=$RS_location->fields['timezone_name'];
			//date_default_timezone_set($time_zone);
			
			$pageview_array = array();
			$pageview_array['campaign_id'] = $_COOKIE['campaign_id'];
			$pageview_array['location_id'] = $_COOKIE['l_id'];;
			$pageview_array['pageview_domain'] = $_REQUEST['domain'];
			$pageview_array['pageview_medium'] = $_REQUEST['medium'];
			$pageview_array['timestamp'] = $_REQUEST['hdn_user_datetime'];
			$objDB->Insert($pageview_array, "pageview_counter");
			$pageview_array = array();
								
			
			// 18-03-2014 pageview counter logic
							
            $json_array['share'] = "true";
            $json_array['c_id'] = $_COOKIE['campaign_id'];
             $json_array['l_id'] = $_COOKIE['l_id'];
             $flag_share = true;
             $activation_code = base64_encode($_REQUEST['emailaddress']);
			   $activate_link = WEB_PATH."/activate.php?id=".$activation_code."&c_id=".$_COOKIE['campaign_id']."&l_id=".$_COOKIE['l_id'];
			 /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list */
			  /*$Sql_max_is_walkin = "SELECT is_walkin from campaigns WHERE id=".$_COOKIE['campaign_id'];
			   $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin);*/
			   $RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin from campaigns WHERE id=?",array($_COOKIE['campaign_id']));
			  // if($RS_max_is_walkin->fields['is_walkin'] == 1)
			 //  {
						  /* $Sql_c_c = "SELECT * FROM customer_campaigns WHERE customer_id='".$c_id."' AND campaign_id='".$_COOKIE['campaign_id']."' AND location_id =".$_COOKIE['l_id'];
		$RS_c_c = $objDB->Conn->Execute($Sql_c_c);*/
		$RS_c_c = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?",array($c_id,$_COOKIE['campaign_id'],$_COOKIE['l_id']));

		 if($RS_c_c->RecordCount()<=0){
			 /*$Sql = "SELECT * FROM activation_codes WHERE campaign_id=".$_COOKIE['campaign_id'];
				$RS_1 = $objDB->Conn->Execute($Sql);*/
				$RS_1 = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE campaign_id=?",array($_COOKIE['campaign_id']));

				if($RS_1->RecordCount()>0){
                                    /* $location_max_sql = "Select num_activation_code , offers_left,used_offers from campaign_location where  campaign_id=".$_COOKIE['campaign_id']." and location_id=".$_COOKIE['l_id'];
         $location_max = $objDB->Conn->Execute($location_max_sql);*/
         $location_max = $objDB->Conn->Execute("Select num_activation_code , offers_left,used_offers from campaign_location where  campaign_id=? and location_id=?",array($_COOKIE['campaign_id'],$_COOKIE['l_id']));

          $used_campaign =  $location_max->fields['used_offers'];
          $offers_left= $location_max->fields['offers_left'];
           $o_left = $location_max->fields['offers_left'];
           if(  $o_left>0)
                                      {
					$activation_code = $RS_1->fields['activation_code'];
								/*$Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
							customer_id='".$c_id."', campaign_id=".$_COOKIE['campaign_id']." , location_id=".$_COOKIE['l_id'];
					$objDB->Conn->Execute($Sql);*/
					$objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status=?, activation_code='?', activation_date= ?, coupon_generation_date=?,customer_id=?, campaign_id=? , location_id=?",array(1,$activation_code,'Now()','Now()',$c_id,$_COOKIE['campaign_id'],$_COOKIE['l_id']));
                                         // ///
                                    $RSLocation_nm  =  $objDB->Conn->Execute("select * from locations where id =".$_COOKIE['l_id']);
		
                                    //$br = $c_id.substr($activation_code,0,2).$_COOKIE['campaign_id'].substr($RSLocation_nm->fields['location_name'],0,2).$_COOKIE['l_id'];
                                    $br = $objJSON->generate_voucher_code($c_id,$activation_code,$_COOKIE['campaign_id'],$RSLocation_nm->fields['location_name'],$_COOKIE['l_id']);
                                        /*$insert_coupon_code = "Insert into coupon_codes set customer_id=".$c_id." , customer_campaign_code=".$_COOKIE['campaign_id']." , coupon_code='".$br."' , active=1 , location_id=".$_COOKIE['l_id']." , generated_date='".date('Y-m-d H:i:s')."' ";
      
                                    $objDB->Conn->Execute($insert_coupon_code);*/
				$objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=? , customer_campaign_code=? , coupon_code=? , active=?, location_id=? , generated_date=? ",array($c_id,$_COOKIE['campaign_id'],$br,1,$_COOKIE['l_id'],date('Y-m-d H:i:s')));

                                      /*$update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$_COOKIE['campaign_id']." and location_id =".$_COOKIE['l_id']." ";
                        $objDB->Conn->Execute($update_num_activation);*/
                        $objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=? and location_id =? ",array(($offers_left-1),($used_campaign+1),$_COOKIE['campaign_id'],$_COOKIE['l_id']));
                                      }
				}
				
		  }
			//   }
			 /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list  */
			 
			 
			 /* check for whether max sharing reached */
			 /*$Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$_COOKIE['campaign_id'];
			   $RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location);*/
			   $RS_max_no_location = $objDB->Conn->Execute( "SELECT max_no_sharing from campaigns WHERE id=?",array($_COOKIE['campaign_id']));
                           
			 /*$Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$_COOKIE['campaign_id']." and referred_customer_id<>0";
			 $RS_shared = $objDB->Conn->Execute(  $Sql_shared);*/
			 $RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?",array($_COOKIE['campaign_id'],0));
			// if($RS_shared->RecordCount() < $RS_max_no_location->fields['max_no_sharing'] ){
					$redeem_array = array();
					$redeem_array['customer_id'] = base64_decode($_COOKIE['customer_id']);
					$redeem_array['campaign_id'] = $_COOKIE['campaign_id'];
					$redeem_array['earned_reward'] = 0;
					$redeem_array['referral_reward'] = 0;
					$redeem_array['referred_customer_id'] = $c_id;
					$redeem_array['reward_date'] =  date("Y-m-d H:i:s");
					$redeem_array['coupon_code_id'] =  0;
					$redeem_array['location_id'] =  $_COOKIE['l_id'];
					$objDB->Insert($redeem_array, "reward_user");
                                        $redeem_array = array();
														
			 ///}
			 
			 //email code
			 
			 //			 $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$_COOKIE['campaign_id']." and referred_customer_id<>0";
//			 $RS_shared = $objDB->Conn->Execute($Sql_shared);
			 $RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?",array($_COOKIE['campaign_id'],0));
			 
		 	 /*$Sql_active = "SELECT active,offers_left from campaign_location WHERE campaign_id=".$_COOKIE['campaign_id']." and location_id=".$_COOKIE['l_id'];
			 $RS_active = $objDB->Conn->Execute($Sql_active);*/
			 
			 $RS_active = $objDB->Conn->Execute("SELECT active,offers_left from campaign_location WHERE campaign_id=? and location_id=?",array($_COOKIE['campaign_id'],$_COOKIE['l_id']));
			 
			 
			 if($RS_shared->RecordCount() <= $RS_max_no_location->fields['max_no_sharing'] && $RS_active->fields['offers_left'] > 0 && $RS_active->fields['active']){
				
				
				/*$Sql_created_by = "SELECT created_by from locations WHERE id=".$_COOKIE['l_id'];
				
			        $RS_created_by = $objDB->Conn->Execute($Sql_created_by);*/
			        $RS_created_by = $objDB->Conn->Execute("SELECT created_by from locations WHERE id=?",array($_COOKIE['l_id']));
				
				$merchantid = $RS_created_by->fields['created_by'];
				
				/*$Sql_merchant_detail = "SELECT * from merchant_user WHERE id=".$merchantid;
			        
				$RS_merchant_detail = $objDB->Conn->Execute($Sql_merchant_detail);*/
				$RS_merchant_detail = $objDB->Conn->Execute("SELECT * from merchant_user WHERE id=?",array($merchantid));
				
				/*$Sql_campaigns_detail = "SELECT * from campaigns WHERE id=".$_COOKIE['campaign_id'];
				$RS_campaigns_detail = $objDB->Conn->Execute($Sql_campaigns_detail);*/
				$RS_campaigns_detail = $objDB->Conn->Execute("SELECT * from campaigns WHERE id=?",array($_COOKIE['campaign_id']));
				$mail = new PHPMailer();
				
				$email_address=$RS_merchant_detail->fields['email'];
				//$email_address="test.test1397@gmail.com";
				
				$body="<div>Hello,<span style='font-weight:bold'>".$RS_merchant_detail->fields['firstname']." ".$RS_merchant_detail->fields['lastname']."</span></div>";
				$body.="<br>";
					$body.="<div>Congratulations! All Sharing points for your campaign :<span style='font-weight:bold'>".$RS_campaigns_detail->fields['title']."</span> are allocated for new customer referral. 
				Please log in to your merchant account and increase referral customers limit for this campaign to continue to increase new customer referral traffic to your locations. </div>";
			        $body.="<br>";
				$body.="<div>Sincerely,</div>";
				 $body.="<div>Scanflip Support Team</div>";
                                				
				$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
				
				$mail->AddAddress($email_address);
				
				$mail->From = "no-reply@scanflip.com";
				$mail->FromName = "ScanFlip Support";
				$mail->Subject    = "Scanflip offer - ";
				$mail->MsgHTML($body);
				$mail->Send();
				
			 }
			 
                            //Make entry in subscsribed_stre table for first time subscribe to loaction
                        
                        /*$sql_group = "select id , merchant_id from merchant_groups where location_id =". $_COOKIE['l_id']." and private = 1";
                                    $RS_group = $objDB->Conn->Execute($sql_group);*/
			$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?",array( $_COOKIE['l_id'],1));
                  
                          /*$sql_chk ="select * from subscribed_stores where customer_id= ".$c_id." and location_id=".$_COOKIE['l_id'];
                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
			$subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=?",array($c_id,$_COOKIE['l_id']));

                        if($subscibed_store_rs->RecordCount()==0)
                        {
                        /*$insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$c_id." ,location_id=".$_COOKIE['l_id']." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                          $objDB->Conn->Execute($insert_subscribed_store_sql);*/
			$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=?",array($c_id,$_COOKIE['l_id'],date('Y-m-d H:i:s'),1));
                        }
                        else {
                            if($subscibed_store_rs->fields['subscribed_status']==0)
                            {
                                /*$up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$c_id." and location_id=".$_COOKIE['l_id'];
                                 $objDB->Conn->Execute($up_subscribed_store);*/
				$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id=? and location_id=?",array($c_id,$_COOKIE['l_id']));

                            }
                        }
                        //   
                   
			 	  /* check whether share user in stores's private group */
                        /*$Sql = "SELECT * FROM merchant_subscribs WHERE user_id=".$c_id." and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=".$_COOKIE['l_id']."  )";
                         $RS_new = $objDB->Conn->Execute($Sql);*/
                         $RS_new = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id in( select id merchant_groups from merchant_groups where private=? and location_id=?  )",array($c_id,1,$_COOKIE['l_id']));

                             if($RS_new->RecordCount()<=0){
                                     /*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$_COOKIE['l_id']." and private = 1";
                                     $RS_group = $objDB->Conn->Execute($sql_group);*/
					$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?",array($_COOKIE['l_id'],1));

                                 $array_group= array();
                                         $array_group['merchant_id']=$RS_group->fields['merchant_id'];
                                         $array_group['group_id']=$RS_group->fields['id'];
                                         $array_group['user_id']= $c_id;
                                         $objDB->Insert($array_group, "merchant_subscribs");
                             }
                        
                $cookie_time = time();
                   setcookie("share","true",$cookie_time-3600,'','','');
		   setcookie("campaign_id",$_REQUEST['campaign_id'],$cookie_time-3600,'','','');
                   setcookie("l_id",$_REQUEST['l_id'],$cookie_time-3600,'','','');
                   setcookie("customer_id",-$_REQUEST['customer_id'],$cookie_time-3600,'','','');
         }
         else{
              $json_array['share'] = "false";
         }
        
          $objDB->Conn->CompleteTrans(); 
	$admin_settings = array();
	$admin_settings['setting'] = "User Activation";
	$RSAdmin = $objDB->Show("admin_settings",$admin_settings);
	
	if($RSAdmin->fields['action'] == 1){
            $array = array();
		$array['emailaddress'] = $_REQUEST['emailaddress'];
		$RS = $objDB->Show("customer_user",$array);
		$Row = $RS->FetchRow();
	$update_sql = "Update  customer_user set active=0 where id= ".$Row['id'];
        
        $objDBWrt->Conn->Execute($update_sql);
		$activation_code = base64_encode($array['emailaddress']);
             
                if(! $flag_share ){
		$activate_link = WEB_PATH."/activate.php?id=".$activation_code;
                }
		
		$mail = new PHPMailer();
		
		$body='<body bgcolor="#e4e4e4" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" style="-webkit-font-smoothing: antialiased;width:100% !important;background:#e4e4e4;-webkit-text-size-adjust:none;">
	
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#e4e4e4">
    <tr>
        <td bgcolor="#e4e4e4" width="100%">
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="table"
                style="background: #D2D2D2; border: 2px solid #ddd; padding: 20px; border-radius: 10px;">
                <tr>
                    <td width="600" class="cell">
                        <table width="600" cellpadding="0" cellspacing="0" border="0" class="table">
                            <tr>
                                <td width="250" class="logocell">
                                    <img src="'.ASSETS_IMG.'/c/scanflip-logo.png" width="205" height="30" alt="Scanflip"
                                        style="-ms-interpolation-mode: bicubic; padding: 20px 0;">
                                </td>
                            </tr>
                        </table>
                        <repeater>
			<layout label="New feature">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td bgcolor="#F37E0A" nowrap><img border="0" src="'.ASSETS_IMG.'/c/spacer.gif" width="5" height="1"></td>
				<td width="100%" bgcolor="#ffffff">
			
					<table width="100%" cellpadding="20" cellspacing="0" border="0">
					<tr>
						<td bgcolor="white" class="contentblock">
							<multiline label="Description">
							<p style=" color: black; font-family:Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; font-weight: normal; line-height: 19px; margin-bottom: 12px; margin-top: 0; padding-bottom: 0; padding-top: 0;">
							Your account has been created successfully.
							</p>
							<p style=" color: black; font-family:Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; font-weight: normal; line-height: 19px; margin-bottom: 12px; margin-top: 0; padding-bottom: 0; padding-top: 0;">
							Please  <a href="'.$activate_link.'">Click Here</a> to activate our account.
							</p>
							</multiline>
                    		<h5><p style=" color: black; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; font-weight: normal; line-height: 19px; margin-bottom: 12px; margin-top: 0; padding-bottom: 0; padding-top: 0;">Thank You,<br/>Scanflip Support Team.</p></h5>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>  
			</layout>
		</repeater>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>';

		/*
		$body = "<p>Your account has been created successfully </p>";
		$body .= "<p>Please  <a href='".$activate_link."'>Click Here</a> to activate our account</p>Thank you,<br/>Scanflip Support Team.";
		*/
		
		$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
		$mail->AddAddress($array['emailaddress']);
		$mail->From = "no-reply@scanflip.com";
		$mail->FromName = "ScanFlip Support";
		$mail->Subject    = "Activate Your Account";
		$mail->MsgHTML($body);
		$mail->Send();
		
		$json_array['action'] = "1";
                
	
	}else{
		
		$array = array();
		$array['emailaddress'] = $_REQUEST['emailaddress'];
		$RS = $objDB->Show("customer_user",$array);
		$Row = $RS->FetchRow();
		$_SESSION['customer_id'] = $Row['id'];
		$_SESSION['customer_info'] = $Row;
		
		$json_array['action'] = "0";
                
	}
        
	$array = array();
	$user_session = session_id();
       
	$array['sessiontime'] = strtotime(date("Y-m-d H:i:s")); 
	$array['session_id'] = base64_encode($_SESSION['customer_id']); 
	$array['session_data'] = md5($array['sessiontime'].$user_session); 
	//$array['user_type'] = 1;
	
        $objDBWrt->Insert($array, "user_sessions");	
	
       

	$json_array['status'] = "true";
	$json_array['customer_id'] = $array['session_data'];
	$json_array['message'] = $client_msg["login_register"]["Msg_Success"];
       
        $json = json_encode($json_array);
	echo $json;
	exit();	
}

/**
 * @uses on button activation code
 * @param activation_code
 * @used in pages : scanflip/campaign.php ,campaign_facebook.php,templates/header.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnActivationCode_']))
{
    
    $activation_code = $_REQUEST['activation_code'];
    $_SESSION['msg']= "";
		$json_array = array();

		if($_SESSION['customer_id'] =="")
                {
                    $url = urlencode(WEB_PATH."/process.php?btnActivationCode_=1&activation_code=".$activation_code) ;
                    header("Location: ".WEB_PATH."/register.php?url=".$url);
                    exit();
                }
               
		$customer_id = $_SESSION['customer_id'];
                
		/*$Sql = "SELECT * FROM activation_codes WHERE  activation_code='$activation_code'";
               
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE  activation_code=?",array($activation_code));
                
		if($RS->RecordCount()<=0){
                        $json_array['status'] = "invalid";
			$json_array['msg'] = "Please enter correct Activation Code";
                       // $_SESSION['msg'] = "Please enter valid Activation Code";
                        $json_array['error_msg'] = "Please enter correct Activation Code";
			$json = json_encode($json_array);
			echo $json;
                         exit;
		}

		$campaign_id = $RS->fields['campaign_id'];
               /*$Sql = "select * from campaign_location where  offers_left>0 and campaign_id = ".$campaign_id." and active=1";
                   
                    $RS_actloc = $objDB->Conn->Execute($Sql);*/
                    $RS_actloc = $objDB->Conn->Execute("select * from campaign_location where  offers_left>0 and campaign_id =? and active=?",array($campaign_id,1));
                
                if($RS_actloc->RecordCount() == 0){
                      $json_array['status'] = "ended";
			$json_array['campaign_end_message'] = "Sorry this campaign is no longer available from merchant. Please browse Scanflip to find campaigns nearby.";
                        $json_array['error_msg'] = "Sorry this campaign is no longer available from merchant. Please browse Scanflip to find campaigns nearby";
                        $_SESSION['campaign_end_message'] = "Sorry this campaign is no longer available from merchant. Please browse Scanflip to find campaigns nearby.";
			$json = json_encode($json_array);
			echo $json;
                        exit;
                }
                
                    /* $Sql = "select * from campaign_location where  offers_left>0 and  campaign_id = ".$campaign_id." and active=1";
                   
                    $RS_loc = $objDB->Conn->Execute($Sql);*/
                    $RS_loc = $objDB->Conn->Execute("select * from campaign_location where  offers_left>0 and  campaign_id = ? and active=?",array($campaign_id,1));
                    
                    if($RS_loc->RecordCount() == -166)
                    {
               
                        $lid = $RS_loc->fields['location_id'];
                 /*$sql_o = "select * from campaign_location where campaign_id =".$campaign_id." and location_id =". $lid ." and active=1";
                 $RS_o = $objDB->Conn->Execute($sql_o);*/
                 $RS_o = $objDB->Conn->Execute("select * from campaign_location where campaign_id =? and location_id =? and active=?",array($campaign_id,$lid,1));

		/*$Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=".$lid;
		
            
                $RS = $objDB->Conn->Execute($Sql);*/
                $RS = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id=?",array($customer_id,$campaign_id,$lid));

                 if($RS_o->RecordCount()>0){
                
		if($RS->RecordCount()<=0){
                  
                     /*$Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
                    
                    $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation);*/
			$RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =? ",array($campaign_id,$lid));

                    $offers_left = $RS_num_activation->fields['offers_left'];
                    $used_campaign = $RS_num_activation->fields['used_offers'];
                  
                     $share_flag= 1;
                    if($offers_left!= 0)
                    {
                       /* $Sql_max_is_walkin = "SELECT is_walkin ,level, new_customer  from campaigns WHERE id=".$campaign_id;
			   $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin);*/
			$RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin ,level, new_customer  from campaigns WHERE id=?",array($campaign_id));

                         if($RS_max_is_walkin->fields['new_customer'] == 1)
                          {
                                    /*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
                                 $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
				$subscibed_store_rs =$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));

				$RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin ,level, new_customer  from campaigns WHERE id=?",array($campaign_id));
                               if($subscibed_store_rs->RecordCount()==0)
                               {
                                   $share_flag= 1;
                               }
                               else {
                                     $share_flag= 0;
                               }
                          }
                          
                           /* check whether new customer for this store */
               $allow_for_reserve= 0;
               $is_new_user= 0;
               /*************** *************************/
               /*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
              
                                              $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk);*/
			$Rs_is_new_customer=$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));

                                            if($Rs_is_new_customer->RecordCount()==0)
                                            {
                                                $is_new_user= 1;
                                            }
                                            else {
                                                  $is_new_user= 0;
                                            }
                
                if($is_new_user==0)
                {
               /*$sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;
                               
                                $RS_campaign_groups = $objDB->Conn->Execute($sql);*/
				$RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$lid));

                                $c_g_str = "";
                                $cnt =1;
                               
                                    $is_it_in_group = 0;
                                if($RS_max_is_walkin->fields['level'] == 0)
                                {
                                    //
                                    if($RS_max_is_walkin->fields['is_walkin'] == 0)
                                    {
                                    if($RS_campaign_groups->RecordCount()>0)
                                        {
                                         while($Row_campaign = $RS_campaign_groups->FetchRow())
                                { 
                                    $c_g_str = $Row_campaign['group_id'];
                                    if($cnt != $RS_campaign_groups->RecordCount())
                                    {
                                        $c_g_str .= "," ;
                                    }
                                }
                                   /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                   
                                         $RS_check_s = $objDB->Conn->Execute($Sql_new_);  */
					$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?)  )",array($_SESSION['customer_id'],$c_g_str)); 
         
                                      while($Row_Check_Cust_group = $RS_check_s->FetchRow())
                                                {
                                                    /*$query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                     $RS_query = $objDB->Conn->Execute($query);*/
							$RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id'],$c_g_str));

                                                     if($RS_query->RecordCount() > 0)
                                                     {

                                                          $is_it_in_group = 1;
                                                     }
                                                }
                                      if($is_it_in_group == 1 )
                                            { 
                                                  $allow_for_reserve = 1;  	
                                            }
                                           else {
                                                $allow_for_reserve = 0;
                                           }
                                             }
                                            else{
                                                $allow_for_reserve = 0;  	
                                            }
                                            //
                                        }
                                         else{
                                       // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                         /* $query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
                                          $RS_all_user_group = $objDB->Conn->Execute($query);*/
					$RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =? ) ",array($_SESSION['customer_id'],$lid,1));

                                          if($RS_all_user_group->RecordCount() > 0)
                                          {
                                               $allow_for_reserve = 1;
                                          }
                                          else
                                          {
                                              $allow_for_reserve = 0;  
                                          }
                                          
                                     }
                                }
                                else
                                {
                                 //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                     $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$lid."  )";
                                     $allow_for_reserve = 1;
                                }
                }
                else{
                    $allow_for_reserve= 1;
                }
                            /* for checking whether customer in campaign group */
                          
                           if($share_flag== 1)
                            {
                                 if($allow_for_reserve==1){
                                /*$Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
                                            customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
                            $objDB->Conn->Execute($Sql);*/
				$objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status=?, activation_code=?, activation_date= ?, coupon_generation_date=?,customer_id=?, campaign_id=? , location_id=?",array(1,$activation_code,'Now()','Now()',$customer_id,$campaign_id,$lid));

                        /*$update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$lid." ";
                        $objDB->Conn->Execute($update_num_activation);*/
			$objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=? and location_id =? ",array(($offers_left-1),($used_campaign+1),$campaign_id,$lid));
                      
                $RSLocation_nm  = $objDB->Conn->Execute("select * from locations where id =".$lid);
		
		 //$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$lid;
		 $br = $objJSON->generate_voucher_code($customer_id,$activation_code,$campaign_id,$RSLocation_nm->fields['location_name'],$lid);
                      $json_array['campaign_id'] = $campaign_id;
                        $json_array['location_id'] = $lid;
                         /* $select_coupon_code = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
       $select_rs = $objDB->Conn->Execute($select_coupon_code);*/
	$select_rs = $objDB->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?  ",array($customer_id,$campaign_id,$lid));
       if($select_rs->RecordCount()<=0)
       {
                         $array_ =array();
	$array_['customer_id'] = $customer_id;
	$array_['customer_campaign_code'] = $campaign_id;
	$array_['coupon_code'] = $br;
	$array_['active']=1;
        $array_['location_id'] = $lid;
	$array_['generated_date'] = date('Y-m-d H:i:s');
	/*$insert_coupon_code = "Insert into coupon_codes set customer_id=".$customer_id." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$lid." , generated_date='".date('Y-m-d H:i:s')."' ";
       $objDB->Conn->Execute($insert_coupon_code);*/
	$objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=? , customer_campaign_code=? , coupon_code=? , active=? , location_id=? , generated_date=? ",array($customer_id,$campaign_id,$br,1,$lid,date('Y-m-d H:i:s')));
       }
        //Make entry in subscribed_stre table for first time subscribe to location
                        /*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                    $RS_group = $objDB->Conn->Execute($sql_group);*/
			$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?",array($lid,1));
                  
                         /* $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
			$subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=".$lid,array($_SESSION['customer_id']));

                        if($subscibed_store_rs->RecordCount()==0)
                        {
                        /*$insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                          $objDB->Conn->Execute($insert_subscribed_store_sql);*/
				$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=1",array($_SESSION['customer_id'],$lid,date('Y-m-d H:i:s')));
                        }
                        else {
                            if($subscibed_store_rs->fields['subscribed_status']==0)
                            {
                                /*$up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                                 $objDB->Conn->Execute($up_subscribed_store);*/
				$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=?  where  customer_id=? and location_id=?",array(1,$_SESSION['customer_id'],$lid));
                            }
                        }
                            /*$RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
                            $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);*/
                            $check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ? and private = 1) and user_id = ?",array($lid,$_SESSION['customer_id']));
                            if($check_subscribe->RecordCount()==0)
                            {
                                        /*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                        $RS_group = $objDB->Conn->Execute($sql_group);*/
					$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?",array($lid,1));				

                                        if($RS_group->RecordCount()>0){
                                            /*$sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;

                                            $RS_user_group =$objDB->Conn->Execute($sql_user_group);*/
						$RS_user_group =$objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id = ?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));

                                            if($RS_user_group->RecordCount()<=0)
                                            {
                                                /*$insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
                                                $objDB->Conn->Execute($insert_sql);*/
						$objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id =? , user_id = ?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$_SESSION['customer_id']));
                                            }
                                      }
                            }
                            
                                 }
                                  else{
                        $json_array['status'] = "newuser";
			$json_array['campaign_for_new_user'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
                        $_SESSION['campaign_for_new_user'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
                        $json_array['error_msg'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
			$json = json_encode($json_array);
			echo $json;
                         exit;
                    }
                           }
                           
                           else{
                                $json_array['status'] = "newuser";
			$json_array['campaign_for_new_user'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
                        $_SESSION['campaign_for_new_user'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
                        $json_array['error_msg'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
			$json = json_encode($json_array);
			echo $json;
                         exit;
                           }
                       
                }
                 else{
                        
                           $json_array['status'] = "ended";
			$json_array['campaign_end_message'] = "Sorry this campaign is no longer available from merchant for new customers. Please browse Scanflip to find campaigns nearby.";
                        $json_array['error_msg'] = "Sorry this campaign is no longer available from merchant for new customers. Please browse Scanflip to find campaigns nearby.";
                        $_SESSION['campaign_end_message'] = "Sorry this campaign is no longer available from merchant for new customers. Please browse Scanflip to find campaigns nearby.";
			$json = json_encode($json_array);
			echo $json;
                        exit;
                    }
                    }
                    else
                {
                        if($RS->fields['activation_status'] == 0)
                        {
                           /* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
                    $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation);*/
			$RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =? ",array($campaign_id,$lid));

                    $offers_left = $RS_num_activation->fields['offers_left'];
                    $used_campaign = $RS_num_activation->fields['used_offers'];
                 
                     $share_flag= 1;
                    if($offers_left!= 0)
                    {
                       /* $Sql_max_is_walkin = "SELECT is_walkin ,level, new_customer  from campaigns WHERE id=".$campaign_id;
			   $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin);*/
			$RS_max_is_walkin = $objDB->Conn->Execute( "SELECT is_walkin ,level, new_customer  from campaigns WHERE id=?",array($campaign_id));
                         if($RS_max_is_walkin->fields['new_customer'] == 1)
                          {
                                    /*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
                                 $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
				$subscibed_store_rs =$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));

                               if($subscibed_store_rs->RecordCount()==0)
                               {
                                   $share_flag= 1;
                               }
                               else {
                                     $share_flag= 0;
                               }
                          }
                           /* check whether new customer for this store */
               $allow_for_reserve= 0;
               $is_new_user= 0;
              
              /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
    
                                              $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk);*/
					$Rs_is_new_customer=$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));
                                            if($Rs_is_new_customer->RecordCount()==0)
                                           {
                                                $is_new_user= 1;
                                            }
                                            else {
                                                  $is_new_user= 0;
                                            }
               
                if($is_new_user==0)
                {
               /*$sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;

                                $RS_campaign_groups = $objDB->Conn->Execute($sql);*/
				$RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$lid));

                                $c_g_str = "";
                                $cnt =1;
                               
                                    $is_it_in_group = 0;
                                if($RS_max_is_walkin->fields['level'] == 0)
                                {
                                    //
                                    if($RS_max_is_walkin->fields['is_walkin'] == 0)
                                    {
                                    if($RS_campaign_groups->RecordCount()>0)
                                        {
                                         while($Row_campaign = $RS_campaign_groups->FetchRow())
                                { 
                                    $c_g_str = $Row_campaign['group_id'];
                                    if($cnt != $RS_campaign_groups->RecordCount())
                                    {
                                        $c_g_str .= "," ;
                                    }
                                }
                                    /*$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";

                                         $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
					$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id='? AND group_id in( select  id from merchant_groups where id in(?)  )",array($_SESSION['customer_id'],$c_g_str)); 

                                      while($Row_Check_Cust_group = $RS_check_s->FetchRow())
                                                {
                                                    /*$query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                     $RS_query = $objDB->Conn->Execute($query);*/
							$RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id'],$c_g_str));

                                                     if($RS_query->RecordCount() > 0)
                                                     {

                                                          $is_it_in_group = 1;
                                                     }
                                                }
                                      if($is_it_in_group == 1 )
                                            { 
                                                  $allow_for_reserve = 1;  	
                                            }
                                           else {
                                                $allow_for_reserve = 0;
                                           }
                                             }
                                            else{
                                                $allow_for_reserve = 0;  	
                                            }
                                            //
                                        }
                                         else{
                                       // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                          /*$query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
                                          $RS_all_user_group = $objDB->Conn->Execute($query);*/
					$RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =? ) ",array($_SESSION['customer_id'],$lid,1));

                                          if($RS_all_user_group->RecordCount() > 0)
                                          {
                                               $allow_for_reserve = 1;
                                          }
                                          else
                                          {
                                              $allow_for_reserve = 0;  
                                          }
                                          
                                     }
                                }
                                else
                                {
                                 //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                     $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$lid."  )";
                                     $allow_for_reserve= 1;
                                }
                }
                else{
                    $allow_for_reserve= 1;
                }
                       
                            /* for checking whether customer in campaign group */
                          
                          
                           if($share_flag== 1)
                            {
                                 if($allow_for_reserve==1){
                               // $Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
                                      //      customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
                     
					 /*$Sql = "Update customer_campaigns SET activation_status=1 where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$lid;
                        
                            $objDB->Conn->Execute($Sql);*/
				$objDBWrt->Conn->Execute("Update customer_campaigns SET activation_status=? where customer_id=? and campaign_id=? and location_id=?",array(1,$customer_id,$campaign_id,$lid));

                          $json_array['campaign_id'] = $campaign_id;
                        $json_array['location_id'] = $lid;
                         /*$select_coupon_code = "update coupon_codes set active= 1 where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
       $objDB->Conn->Execute($select_coupon_code);*/
			$objDBWrt->Conn->Execute("update coupon_codes set active= ? where customer_id=? and customer_campaign_code=? and location_id=?",array(1,$customer_id,$campaign_id,$lid));
       
        //Make entry in subscribed_stre table for first time subscribe to loaction
                       /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                    $RS_group = $objDB->Conn->Execute($sql_group);*/
				$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?",array($lid,1));
                  
                          /*$sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
			$subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=?",array($_SESSION['customer_id'],$lid));

                        if($subscibed_store_rs->RecordCount()==0)
                        {
                        	/*$insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                          $objDB->Conn->Execute($insert_subscribed_store_sql);*/
			$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=?",array($_SESSION['customer_id'],$lid,date('Y-m-d H:i:s'),1));

                        }
                        else {
                            if($subscibed_store_rs->fields['subscribed_status']==0)
                            {
                                /*$up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                                 $objDB->Conn->Execute($up_subscribed_store);*/
				$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=?  where  customer_id=? and location_id=?",array(1,$_SESSION['customer_id'],$lid));
                            }
                        }
                        
                            /*$RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
                            $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);*/
				$check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ? and private = ?) and user_id =?",array($lid,1,$_SESSION['customer_id']));

                            if($check_subscribe->RecordCount()==0)
                            {
                                        $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                        $RS_group = $objDB->Conn->Execute($sql_group);		

                                        if($RS_group->RecordCount()>0){
                                            /*$sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;

                                            $RS_user_group =$objDB->Conn->Execute($sql_user_group);*/
					$RS_user_group =$objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id = ?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));


                                            if($RS_user_group->RecordCount()<=0)
                                            {
                                                /*$insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
                                                $objDB->Conn->Execute($insert_sql);*/
						$objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id = ? , user_id = ?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$_SESSION['customer_id']));
                                            }
                                      }
                            }
                                 }
                                  else{
                        
                      $json_array['status'] = "newuser";
			$json_array['campaign_for_new_user'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
                        $_SESSION['campaign_for_new_user'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
                        $json_array['error_msg'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
			$json = json_encode($json_array);
			echo $json;
                        exit;
                    }
                           }
                           
                           else{
                                 $json_array['status'] = "newuser";
			$json_array['campaign_for_new_user'] = "This campaign for new users only";
                        $json_array['error_msg'] =  "This campaign for new users only";
                        $_SESSION['campaign_for_new_user'] = "This campaign for new users only";
			$json = json_encode($json_array);
			echo $json;
                         exit;
                           }
                }
                 else{
                        
                       $json_array['status'] = "ended";
			$json_array['campaign_end_message'] = "Sorry this campaign is no longer available from merchant for new customers. Please browse Scanflip to find campaigns nearby.";
                        $json_array['error_msg'] = "Sorry this campaign is no longer available from merchant for new customers. Please browse Scanflip to find campaigns nearby.";
                        $_SESSION['campaign_end_message'] = "Sorry this campaign is no longer available from merchant for new customers. Please browse Scanflip to find campaigns nearby.";
			$json = json_encode($json_array);
			echo $json;
                        exit;
                    }
                            
                        }
                         $json_array['status'] = "true";
                    $json_array['campaign_id'] = $campaign_id;
                    $json_array['l_id'] = $lid;
                    $json = json_encode($json_array);
                    echo $json;
                        
                      exit();
                }
                 }
                 else
                {
                      $json_array['status'] = "ended";
			$json_array['campaign_end_message'] = "Sorry this campaign is no longer available from merchant for new customers. Please browse Scanflip to find more offers";
                        $json_array['error_msg'] = "Sorry this campaign is no longer available from merchant for new customers. Please browse Scanflip to find more offers";
                        $_SESSION['campaign_end_message'] = "Sorry this campaign is no longer available from merchant for new customers. Please browse Scanflip to find more offers";
			$json = json_encode($json_array);
			echo $json;
                        exit;
                }
                    }
                    else
                    {
                      
                       $json_array['status'] = "false";
            		$json = json_encode($json_array);
			echo $json;
                        exit;  
                    }
                    $json_array['status'] = "true";
                    $json_array['campaign_id'] = $campaign_id;
                    $json_array['l_id'] = $lid;
                    $json = json_encode($json_array);
                    echo $json;
 exit();
}

/**
 * @uses on button activation code
 * @param activation_code
 * @used in pages : scanflip/activate-deal.php ,activedeal.php,index.php,templates/header.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnActivationCode']))
{
    
    $activation_code = $_REQUEST['activation_code'];
    $_SESSION['msg']= "";
		$json_array = array();
              
		if(!isset($_SESSION['customer_id']))
                {
                    $url = urlencode(WEB_PATH."/process.php?btnActivationCode=1&activation_code=".$activation_code) ;
                    header("Location: ".WEB_PATH."/register.php?url=".$url);
                    exit();
                }
               
		$customer_id = $_SESSION['customer_id'];
                
		$Sql = "SELECT * FROM activation_codes WHERE activation_code='$activation_code'";
               
		$RS = $objDB->Conn->Execute($Sql);
                
		if($RS->RecordCount()<=0){
			
                        $_SESSION['msg'] = "Please enter valid Activation Code";
			// header("Location:activate-deal.php");
			 header("Location:index.php");
                          exit();
		}
		$campaign_id = $RS->fields['campaign_id'];
              
                    $Sql = "select * from campaign_location where campaign_id = ".$campaign_id;
                   
                    $RS_loc = $objDB->Conn->Execute($Sql);
                    if($RS_loc->RecordCount() == 1)
                    {
                        $lid = $RS_loc->fields['location_id'];
                 $sql_o = "select * from campaign_location where campaign_id =".$campaign_id." and location_id =". $lid ." and active=1";
              
                 $RS_o = $objDB->Conn->Execute($sql_o);
		$Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=".$lid;
                $RS = $objDB->Conn->Execute($Sql);
                 if($RS_o->RecordCount()>0){
                
		if($RS->RecordCount()<=0){
                  
                     $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
                    
                    $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation);
                    $offers_left = $RS_num_activation->fields['offers_left'];
                    $used_campaign = $RS_num_activation->fields['used_offers'];
                  
                     $share_flag= 1;
                    if($offers_left!= 0)
                    {
                        $Sql_max_is_walkin = "SELECT is_walkin ,level, new_customer  from campaigns WHERE id=".$campaign_id;
			   $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin);
                         if($RS_max_is_walkin->fields['new_customer'] == 1)
                          {
                                    $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
                                 $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);
                               if($subscibed_store_rs->RecordCount()==0)
                               {
                                   $share_flag= 1;
                               }
                               else {
                                     $share_flag= 0;
                               }
                          }
                          
                           /* check whether new customer for this store */
               $allow_for_reserve= 0;
               $is_new_user= 0;
               
               $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
		    $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk);
		    if($Rs_is_new_customer->RecordCount()==0)
		    {
		        $is_new_user= 1;
		    }
		    else {
		          $is_new_user= 0;
		    }
                if($is_new_user==0)
                {
               $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;
                                $RS_campaign_groups = $objDB->Conn->Execute($sql);
                                $c_g_str = "";
                                $cnt =1;
                               
                                    $is_it_in_group = 0;
                                if($RS_max_is_walkin->fields['level'] == 0)
                                {
                                    if($RS_max_is_walkin->fields['is_walkin'] == 0)
                                    {
                                    if($RS_campaign_groups->RecordCount()>0)
                                        {
                                         while($Row_campaign = $RS_campaign_groups->FetchRow())
                                { 
                                    $c_g_str = $Row_campaign['group_id'];
                                    if($cnt != $RS_campaign_groups->RecordCount())
                                    {
                                        $c_g_str .= "," ;
                                    }
                                }
                                    $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                         $RS_check_s = $objDB->Conn->Execute($Sql_new_);           
                                      while($Row_Check_Cust_group = $RS_check_s->FetchRow())
                                                {
                                                    $query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                     $RS_query = $objDB->Conn->Execute($query);

                                                     if($RS_query->RecordCount() > 0)
                                                     {

                                                          $is_it_in_group = 1;
                                                     }
                                                }
                                      if($is_it_in_group == 1 )
                                            { 
                                                  $allow_for_reserve = 1;  	
                                            }
                                           else {
                                                $allow_for_reserve = 0;
                                           }
                                             }
                                            else{
                                                $allow_for_reserve = 0;  	
                                            }
                                        }
                                         else{
                                       // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                          $query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
                                          $RS_all_user_group = $objDB->Conn->Execute($query);
                                          if($RS_all_user_group->RecordCount() > 0)
                                          {
                                               $allow_for_reserve = 1;
                                          }
                                          else
                                          {
                                              $allow_for_reserve = 0;  
                                          }
                                          
                                     }
                                }
                                else
                                {
                                 //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                     $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$lid."  )";
                                     $allow_for_reserve = 1;
                                }
                }
                else{
                    $allow_for_reserve= 1;
                }
                            /* for checking whether customer in campaign group */
                          
                           if($share_flag== 1)
                            {
                                 if($allow_for_reserve==1){
                                /*$Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
                                            customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
                     
                            $objDB->Conn->Execute($Sql);*/
				$objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status=?, activation_code=?, activation_date=?, coupon_generation_date=?,customer_id=?, campaign_id=? , location_id=?",array(1,$activation_code,'Now()','Now()',$customer_id,$campaign_id,$lid));

                        /*$update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$lid." ";
                        $objDB->Conn->Execute($update_num_activation);*/
			$objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=? and location_id =?",array(($offers_left-1),($used_campaign+1),$campaign_id,$lid));
                      
                //$RSLocation_nm  = $objDB->Conn->Execute("select * from locations where id =".$lid);
		$RSLocation_nm  = $objDB->Conn->Execute("select * from locations where id =?",array($lid));
		
		 //$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$lid;
		 $br = $objJSON->generate_voucher_code($customer_id,$activation_code,$campaign_id,$RSLocation_nm->fields['location_name'],$lid);
		 
                      $json_array['campaign_id'] = $campaign_id;
                        $json_array['location_id'] = $lid;
						/*$redirect_query = "select permalink from campaign_location where campaign_id=".$campaign_id." and location_id=".$lid;
				$redirect_RS = $objDB->Conn->Execute($redirect_query );*/
			$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid));

					$json_array['permalink'] = $redirect_RS->fields['permalink'];
                          /*$select_coupon_code = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
       $select_rs = $objDB->Conn->Execute($select_coupon_code);*/
	$select_rs = $objDB->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?",array($customer_id,$campaign_id,$lid));

       if($select_rs->RecordCount()<=0)
       {
                         $array_ =array();
	$array_['customer_id'] = $customer_id;
	$array_['customer_campaign_code'] = $campaign_id;
	$array_['coupon_code'] = $br;
	$array_['active']=1;
        $array_['location_id'] = $lid;
	$array_['generated_date'] = date('Y-m-d H:i:s');
	/*$insert_coupon_code = "Insert into coupon_codes set customer_id=".$customer_id." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$lid." , generated_date='".date('Y-m-d H:i:s')."' ";
       
       $objDB->Conn->Execute($insert_coupon_code);*/
	$objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=? , customer_campaign_code=? , coupon_code=? , active=? , location_id=? , generated_date=?",array($customer_id,$campaign_id,$br,1,$lid,date('Y-m-d H:i:s')));
       }
        //Make entry in subscribed_stre table for first time subscribe to loaction
                        /*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                    $RS_group = $objDB->Conn->Execute($sql_group);*/
				$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?",array($lid,1));
                  
                          /*$sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
			$subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?",array($_SESSION['customer_id'],$lid));

                        if($subscibed_store_rs->RecordCount()==0)
                        {
                        	/*$insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                          	$objDB->Conn->Execute($insert_subscribed_store_sql);*/
				$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=?",array($_SESSION['customer_id'],$lid,date('Y-m-d H:i:s'),1));
                        }
                        else {
                            if($subscibed_store_rs->fields['subscribed_status']==0)
                            {
                                /*$up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                                 $objDB->Conn->Execute($up_subscribed_store);*/
				$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=? where  customer_id=? and location_id=?",array(1,$_SESSION['customer_id'],$lid));
                            }
                        }
                            /*$RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
                            $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);*/
				$check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id =? and private =?) and user_id =?",array($lid,1,$_SESSION['customer_id']));

                            if($check_subscribe->RecordCount()==0)
                            {
                                        /*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                        $RS_group = $objDB->Conn->Execute($sql_group);	*/
					$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?",array($lid,1));			

                                        if($RS_group->RecordCount()>0){
                                            /*$sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;

                                            $RS_user_group =$objDB->Conn->Execute($sql_user_group);*/
						$RS_user_group =$objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id = ?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));

                                            if($RS_user_group->RecordCount()<=0)
                                            {
                                                /*$insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
                                                $objDB->Conn->Execute($insert_sql);*/
						$objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id =? , user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$_SESSION['customer_id']));
                                            }
                                      }
                            }
                                 }
                                  else{
                        
                        $_SESSION['campaign_for_new_user'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
			
                        header("Location:index.php?activation_code=".$activation_code."&hp=".$campaign_id);
                          exit();
                    }
                           }
                           
                           else{
                                 $_SESSION['campaign_for_new_user'] =  "This campaign for new users only";
			
                        header("Location:index.php?activation_code=".$activation_code."&hp=".$campaign_id);
                          exit();
                           }
                           
                }
                 else{
                        
                        $_SESSION['campaign_end_message'] = "This campaign is ended for your selected location so you can not reserve this campaign. For more campaign visit browse deal page";
			
                        header("Location:index.php?activation_code=".$activation_code."&hp=".$campaign_id);
                          exit();
                    }
                    }
                    else
                {
                        if($RS->fields['activation_status'] == 0)
                        {
                           /* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
                    $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation);*/
			$RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =? ",array($campaign_id,$lid));

                    $offers_left = $RS_num_activation->fields['offers_left'];
                    $used_campaign = $RS_num_activation->fields['used_offers'];

                     $share_flag= 1;
                    if($offers_left!= 0)
                    {
                        /*$Sql_max_is_walkin = "SELECT is_walkin ,level, new_customer  from campaigns WHERE id=".$campaign_id;
			   $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin);*/
			$RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin ,level, new_customer  from campaigns WHERE id=?",array($campaign_id));

                         if($RS_max_is_walkin->fields['new_customer'] == 1)
                          {
                                    /*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
                                 $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
				$subscibed_store_rs =$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));

                               if($subscibed_store_rs->RecordCount()==0)
                               {
                                   $share_flag= 1;
                               }
                               else {
                                     $share_flag= 0;
                               }
                          }
                           /* check whether new customer for this store */
               $allow_for_reserve= 0;
               $is_new_user= 0;
               
               /*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
              
                                              $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk);*/
				$Rs_is_new_customer=$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));
                                            if($Rs_is_new_customer->RecordCount()==0)
                                            {
                                                $is_new_user= 1;
                                            }
                                            else {
                                                  $is_new_user= 0;
                                            }
               
                if($is_new_user==0)
                {
                     
               /*$sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;
                               
                                $RS_campaign_groups = $objDB->Conn->Execute($sql);*/
				$RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$lid));

                                $c_g_str = "";
                                $cnt =1;
                               
                                    $is_it_in_group = 0;
                                if($RS_max_is_walkin->fields['level'] == 0)
                                {
                                    //
                                    if($RS_max_is_walkin->fields['is_walkin'] == 0)
                                    {
                                    if($RS_campaign_groups->RecordCount()>0)
                                        {
                                         while($Row_campaign = $RS_campaign_groups->FetchRow())
                                { 
                                    $c_g_str = $Row_campaign['group_id'];
                                    if($cnt != $RS_campaign_groups->RecordCount())
                                    {
                                        $c_g_str .= "," ;
                                    }
                                }
                                    /*$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                   
                                         $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
					$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=?' AND group_id in( select  id from merchant_groups where id in(?)  )",array($_SESSION['customer_id'],$c_g_str)); 
                                      while($Row_Check_Cust_group = $RS_check_s->FetchRow())
                                                {
                                                    /*$query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                     $RS_query = $objDB->Conn->Execute($query);*/
							$RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id'],$c_g_str));

                                                     if($RS_query->RecordCount() > 0)
                                                     {

                                                          $is_it_in_group = 1;
                                                     }
                                                }
                                      if($is_it_in_group == 1 )
                                            { 
                                                  $allow_for_reserve = 1;  	
                                            }
                                           else {
                                                $allow_for_reserve = 0;
                                           }
                                             }
                                            else{
                                                $allow_for_reserve = 0;  	
                                            }
                                            
                                        }
                                         else{
                                       // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                          /*$query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
                                          $RS_all_user_group = $objDB->Conn->Execute($query);*/
					$RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =? ) ",array($_SESSION['customer_id'],$lid,1));

                                          if($RS_all_user_group->RecordCount() > 0)
                                          {
                                               $allow_for_reserve = 1;
                                          }
                                          else
                                          {
                                              $allow_for_reserve = 0;  
                                          }
                                          
                                     }
                                }
                                else
                                {
                                 //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                     $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$lid."  )";
                                     $allow_for_reserve= 1;
                                }
                }
                else{
                    $allow_for_reserve= 1;
                }
                       
                            /* for checking whether customer in campaign group */
                          
                          
                           if($share_flag== 1)
                            {
                                 if($allow_for_reserve==1){
                               // $Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
                                      //      customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;

					 /*$Sql = "Update customer_campaigns SET activation_status=1 where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$lid;
                        
                            $objDB->Conn->Execute($Sql);*/
				$objDBWrt->Conn->Execute("Update customer_campaigns SET activation_status=1 where customer_id=? and campaign_id=? and location_id=?",array($customer_id,$campaign_id,$lid));


				/*$redirect_query = "select permalink from campaign_location where campaign_id=".$campaign_id." and location_id=".$lid;
				$redirect_RS = $objDB->Conn->Execute($redirect_query );*/
			$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid));

		$json_array['permalink'] = $redirect_RS->fields['permalink'];
                          $json_array['campaign_id'] = $campaign_id;
                        $json_array['location_id'] = $lid;
                         /*$select_coupon_code = "update coupon_codes set active= 1 where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
       $objDB->Conn->Execute($select_coupon_code);*/
		$objDBWrt->Conn->Execute("update coupon_codes set active=? where customer_id=? and customer_campaign_code=? and location_id=?",array(1,$customer_id,$campaign_id,$lid));
     
       
        //Make entry in subscribed_stre table for first time subscribe to loaction
                        /*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                    $RS_group = $objDB->Conn->Execute($sql_group);*/
				$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?",array($lid,1));
                  
                          /*$sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
			$subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?",array($_SESSION['customer_id'],$lid));

                        if($subscibed_store_rs->RecordCount()==0)
                        {
                        /*$insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                          $objDB->Conn->Execute($insert_subscribed_store_sql);*/
			$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id= ? ,location_id=? ,subscribed_date=? ,subscribed_status=?",array($_SESSION['customer_id'],$lid,date('Y-m-d H:i:s'),1));
                        }
                        else {
                            if($subscibed_store_rs->fields['subscribed_status']==0)
                            {
                                /*$up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                                 $objDB->Conn->Execute($up_subscribed_store);*/
				$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id=? and location_id=?",array($_SESSION['customer_id'],$lid));

                            }
                        }
                           /* $RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
                            $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);*/
				$check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id =? and private =?) and user_id = ?",array($lid,1,$_SESSION['customer_id']));

                            if($check_subscribe->RecordCount()==0)
                            {
                                        /*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                        $RS_group = $objDB->Conn->Execute($sql_group);	*/
					$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?",array($lid,1));			

                                        if($RS_group->RecordCount()>0){
                                            /*$sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;

                                            $RS_user_group =$objDB->Conn->Execute($sql_user_group);*/
						$RS_user_group =$objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));

                                            if($RS_user_group->RecordCount()<=0)
                                            {
                                                /*$insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
                                                $objDB->Conn->Execute($insert_sql);*/
						$objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id =? , user_id = ?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$_SESSION['customer_id']));

                                            }
                                      }
                            }
                                 }
                                  else{
                        
                        $_SESSION['campaign_for_new_user'] = "Sorry this campaign is available to limited customers. Please browse Scanflip to find campaigns nearby.";
			
                        header("Location:index.php?activation_code=".$activation_code."&hp=".$campaign_id);
                          exit();
                    }
                           }
                           
                           else{
                                 $_SESSION['campaign_for_new_user'] =  "This campaign for new users only";
			
                        header("Location:index.php?activation_code=".$activation_code."&hp=".$campaign_id);
                          exit();
                           }
                }
                 else{
                        
                        $_SESSION['campaign_end_message'] = "This campaign is ended for your selected location so you can not reserve this campaign. For more campaign visit browse deal page";
			
                        header("Location:index.php?activation_code=".$activation_code."&hp=".$campaign_id);
                          exit();
                    }
                            
                        }
                      header("Location: index.php?campaign_id=".$campaign_id."&l_id=".$lid);
                      exit();
                }
                 }
                 else
                {
                      $_SESSION['campaign_end_message'] = "This campaign is ended for your selected location so you can not reserve this campaign. For more campaign visit browse deal page";
                        header("Location:index.php?activation_code=".$activation_code."&hp=".$campaign_id);
                          exit();
                }
                    }
                    else
                    {
                      
                          
			
                       header("Location:index.php?activation_code=".$activation_code."&hp=".$campaign_id);
                         exit();
                    }
/*$redirect_query = "select permalink from campaign_location where campaign_id=".$campaign_id." and location_id=".$lid;
$redirect_RS = $objDB->Conn->Execute($redirect_query );*/
$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid));
                     header("Location: ".$redirect_RS->fields['permalink']);
 exit();
}

/**
 * @uses check activation code for campaign
 * @param camp_id,activationcode,loc_id
 * @used in pages : campaign.php,campaign_facebook.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['search1_frm']))
{
   header("Location: search-deal.php");
   exit(); 
}

/**
 * @uses check activation code for campaign
 * @param camp_id,activationcode,loc_id
 * @used in pages : no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['checkactivationcode_for_campaign']))
{
   
	 $campaign_id = $_REQUEST['camp_id'];
    //$Sql = "SELECT * FROM activation_codes WHERE activation_code='".$_REQUEST['activationcode']."' and campaign_id = ".$campaign_id;
    	
		//$RS = $objDB->Conn->Execute($Sql);
		$RS = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE activation_code=? and campaign_id = ?",array($_REQUEST['activationcode'],$campaign_id));
		if($RS->RecordCount()<=0){
//			$json_array['status'] = "false";
//			$json_array['message'] = "Please enter valid Activation Code";
                        $json_array['status'] = "false";
		}
                else {
                    $json = $objJSON->activate_new_deal("",$_REQUEST['activationcode'],$campaign_id,$_REQUEST['loc_id']);
                    $json_array['act_json'] = $json;
                    $json_array['status'] = "true";
                    }
                   $json = json_encode($json_array); 
              echo $json;
	exit();  
		
}

/**
 * @uses check activation code for campaign
 * @param camp_id,activationcode,loc_id
 * @used in pages : campaign.php,campaign_facebook.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['checkactivationcode_for_campaign1']))
{
   
	 $campaign_id = $_REQUEST['camp_id'];
     /*$Sql = "SELECT * FROM activation_codes WHERE activation_code='".$_REQUEST['activationcode']."' and campaign_id = ".$campaign_id;
	
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE activation_code=? and campaign_id = ?",array($_REQUEST['activationcode'],$campaign_id));
                
		if($RS->RecordCount()<=0){
//			$json_array['status'] = "false";
//			$json_array['message'] = "Please enter valid Activation Code";
                        $json_array['status'] = "false";
		}
                else {
                    $json = $objJSON->activate_new_deal("",$_REQUEST['activationcode'],$campaign_id,$_REQUEST['loc_id']);
                    $json_array['act_json'] = $json;
                    $json_array['status'] = "true";
                    }
                   $json = json_encode($json_array); 
              echo $json;
	exit();  
		
}

/**
 * @uses check activation code
 * @param activationcode
 * @used in pages : header.php,activate-deal.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['checkactivationcode']))
{
    //$Sql = "SELECT * FROM activation_codes WHERE activation_code='".$_REQUEST['activationcode']."'";
		//$RS = $objDB->Conn->Execute($Sql);
		$RS = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE activation_code=?",array($_REQUEST['activationcode']));
                
		if($RS->RecordCount()<=0){
			$json_array['status'] = "false";
			$json_array['message'] = "Please enter valid Activation Code";
			$json = json_encode($json_array);
			echo "###false###";
		}
                else {
                    $campaign_id = $RS->fields['campaign_id'];
                    //$sql  = "SELECT * from campaign_location WHERE offers_left >0  and campaign_id = ".$campaign_id;
                    //$RSdata = $objDB->Conn->Execute($sql);
                    $RSdata = $objDB->Conn->Execute("SELECT * from campaign_location WHERE offers_left = ?  and campaign_id =? ",array('>0',$campaign_id));
                    
                    if($RSdata->RecordCount()<= 0) 
                    {
                        echo "###false###";
                    }
                    else if($RSdata->RecordCount() == 1)
                    {
                    }
                    else
                    { ?>
                       
                    <?php }
                }

               ?>
	 
<?php }

/**
 * @uses get map for activation code
 * @param message,activationcode
 * @used in pages : header.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['getMapForActivationCode']))
{
?>
<style>
.main_map_locations
{
    margin: 8px;
}
.main_map_locations input[type="submit"]
{
	background-position: -11px -6px;
}
#activateok 
{
	background-position: -11px -6px;
}
</style>
<?php
if($_REQUEST['message'] == "" || $_REQUEST['message'] == "undefined" ){
    } else {
      ?>
        <div id="div_msg" style="height:auto;text-align:center;padding:10px;font-size:15px;margin-top:10%;"> <?php echo $_REQUEST['message'];  ?></div>
        <div style="clear:both"></div>
        <div align="center" ><input type="submit" name="activateok" id="activateok" value="Ok" /></div>
        <link href="<?php echo ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">
         <script src="<?php echo ASSETS_JS ?>/c/jquery-1.7.2.min.js" type="text/javascript"></script>
        <script>
            $("#activateok").live("click",function(){
                 parent.$.fancybox.close();
            });
       </script>
           
  <?php      }   
     ?>


<?php
if($_REQUEST['message'] == "" || $_REQUEST['message'] == "undefined" ){ 
    //$Sql = "SELECT * FROM activation_codes WHERE activation_code='".$_REQUEST['activationcode']."'";
        //$RS = $objDB->Conn->Execute($Sql);
                $RS = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE activation_code=?",array($_REQUEST['activationcode']));
                
				
				
             if($RS->RecordCount()<=0){
			$json_array['status'] = "false";
			$json_array['message'] = "Please enter valid Activation Code";
			$json = json_encode($json_array);
			
		}
                else {
                    $campaign_id = $RS->fields['campaign_id'];
                    //$sql  = "SELECT * from campaign_location WHERE offers_left>0 and active=1 and campaign_id = ".$campaign_id;
                      //  $RSdata = $objDB->Conn->Execute($sql);
                        $RSdata = $objDB->Conn->Execute("SELECT * from campaign_location WHERE offers_left>0 and active=? and campaign_id = ?",array(1,$campaign_id));
                       
                    if($RSdata->RecordCount()<= 0) 
                    {
                       
                    }

                    else
                    { ?>
       <link href="<?php echo ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">
	   
        <div id="div_msg" class="" style="display:none;height:auto"></div>
                        <input type="hidden" name="txt_activation_code" id="txt_activation_code" value="<?php echo $_REQUEST['activationcode']; ?>" />
                        <input type="hidden" name="hdn_location" id="hdn_location" value="" />
						<div class="main_map_locations">
							<div style="text-transform:capitalize;padding:10px;font-size:15px;" style="height:auto"> Select merchant location to activate your offer</div>
							<div id="map_locations" class="fancybox.iframe" style="width:400px;height:250px" ></div>
							<div align="center" style="margin-top:7px;"> <input type="submit" name="activatedeal" id="activatedeal" value="Activate" disabled/>
								<input type="submit" name="activecancel" id="activecancel" value="Cancel" /> 
							</div>
						</div>
                       <script src="<?php echo ASSETS_JS ?>/c/jquery-1.7.2.min.js" type="text/javascript"></script>
                       <script type='text/javascript' src="https://maps.google.com/maps/api/js?sensor=true&.js"></script>
<script type='text/javascript' src="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>	
<script>

      var map = null;
     var infowindow = null;
    var infowindowcontent =[];
    var markerArray = [];
var locations = [
    <?php
    $count =1;
    $total = $RSdata->RecordCount();
     while($Row_u = $RSdata->FetchRow())
                        { 
         
                            //$Sql_s = "SELECT * from locations WHERE id=".$Row_u['location_id'];
                          
                    //$RS_loc = $objDB->Conn->Execute($Sql_s);
                    $RS_loc = $objDB->Conn->Execute("SELECT * from locations WHERE id=?",array($Row_u['location_id']));
                    
                    $storeid =  $RS_loc->fields['id'];
                $lat = $RS_loc->fields['latitude'];
                   $lng = $RS_loc->fields['longitude'];

                   $location_name = $RS_loc->fields['location_name'];

                   $cat_id =1;
                   $address = $RS_loc->fields['address'] . ", " . $RS_loc->fields['city'] . ", " . $RS_loc->fields['state'] . ", " . $RS_loc->fields['zip'] . ", " . $RS_loc->fields['country'];
                  
                   $MARKER_HTML = "<div  style='clear:both; width:auto;font:Arial, Helvetica, sans-serif;'>";
                      $busines_name = "";
//                   if ($RS_loc->fields['location_name']!= "") {
//                       $busines_name = $RS_loc->fields['location_name'];
//                   } else {
                       $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $storeid);
                       if (trim($arr[0]) == "") {
                           unset($arr[0]);
                           $arr = array_values($arr);
                       }
                       $json = json_decode($arr[0]);
                       $busines_name = $json->bus_name;
                  //  }
            $deal_distance1 = "";

                   if($_COOKIE['mycurrent_lati']!="" && $_COOKIE['mycurrent_long']!="")
                    {
                            $from_lati1=$_COOKIE['mycurrent_lati'];

                            $from_long1=$_COOKIE['mycurrent_long'];

                            $to_lati1=$RS_loc->fields['latitude'];

                            $to_long1=$RS_loc->fields['longitude'];

                            $deal_distance1 = $objJSON->distance($from_lati1, $from_long1, $to_lati1, $to_long1, "M")."Mi";
                    }
                                                        
                   $MARKER_HTML .= "<div style='font-weight: 400;'><b>" . $busines_name . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>" . $deal_distance1 . "</b></div>";
                   $MARKER_HTML .= "<div>" . $address . "</div>";
                 
                   $MARKER_HTML .= "</div>";

                ?>
                      ["<?=$MARKER_HTML?>", <?=$lat?>, <?=$lng?>, <?=$count?>,"<?=$busines_name?>",'<?=$cat_id?>',<?=$storeid?>]
                <?
                if($count != $total) echo ",";
							$count++;
                        }
                        
    ?>
];
$("#activatedeal").live("click",function(){

$.ajax({
               type: "POST",
               url: "<?php echo WEB_PATH?>/process.php",
               data: "btnactivationcodeloginornot=true&activationcode="+$("#txt_activation_code").val(),
               async : false,
               success: function(msg) {
                 var obj = jQuery.parseJSON(msg);
                    st = obj.status;
					if(st == "false")
					{
						window.parent.location.href= obj.link;
						return false;
					}
                    
               }
       });
if ($("#hdn_location").val() =="")
	{
		alert("Please Click On Mapmarker To Select Location Before Activating Your Offer");
		return;
	}
	
	
var lval =0;
if($("#hdn_location").val() !="")
    {
        lval  = $("#hdn_location").val();
    }
  var err_msg = "";
   $.ajax({
               type: "POST",
               url: "<?php echo WEB_PATH?>/process.php",
               data: "activate_new_deal_campaignpage=yes&activation_code="+$("#txt_activation_code").val()+"&campid=0&loc_id="+lval,
               async : false,
               success: function(msg) {
			       var obj = jQuery.parseJSON(msg);
                    st = obj.status;
                    if(st == "false")
                        {
                        }
                   else if(st != "true"){
                     
                        err_msg = obj.error_msg;
                        jQuery(".main_map_locations").css("display","none");
						
						$("#div_msg").html("<div style='text-align: center; padding: 20px;'>"+err_msg+"</div><div align='center'><input id='activateok' type='submit' value='Ok' name='activateok'></div>");
						$("#div_msg").css("display",'block');
                        //$("#map_locations").css("display",'block');
                        //$(".fancybox-inner", window.parent.document).css("height","450px");
						$(".fancybox-inner", window.parent.document).css("height","150px");
                    }
                    else if(st == "true")
                        {
                           //parent.window.location.href = "campaign.php?campaign_id="+obj.campaign_id+"&l_id="+obj.location_id;
						    parent.window.location.href = obj.permalink;
                        }
               }
       });

       return false;
});
jQuery("#activateok").live("click",function(){
	parent.$.fancybox.close();
});
jQuery("#activecancel").live("click",function(){
   //jQuery(".fancybox-close").trigger("click");
   parent.$.fancybox.close();
});
 function initialize() {
   
                                      var myOptions = {
                                          zoom:parseInt(10),
						center: new google.maps.LatLng(<?=$lat?>, <?=$lng?>),
						mapTypeControl: true,
						mapTypeControlOptions: {
							style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
						},
						
						zoomControl: true,
						zoomControlOptions: {
						    style: google.maps.ZoomControlStyle.LARGE,
						    position: google.maps.ControlPosition.LEFT_CENTER
						},

						navigationControl: true,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
                                       
					map = new google.maps.Map(document.getElementById("map_locations"), myOptions);
                                        
                                        //google.maps.event.trigger(map,"resize");
                                        //map.checkResize();

					google.maps.event.addListener(map, 'click', function() {
						infowindow.close();
                                                   for (var prop in markerArray) {
                                                                markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
                                                    }
                                                  
					});
                                        
                                       // Add markers to the map
					// Set up markers based on the number of elements within the myPoints array
					for (var i = 0; i < locations.length; i++) {
					
						createMarker(new google.maps.LatLng(locations[i][1], locations[i][2]), locations[i][0], locations[i][3], locations[i][4],locations[i][5],locations[i][6]);
					}
					// by deafult
                                        if(locations.length > 1)
                                        {
                                            // start to set map possition in center to show all markers

                                            var latlngbounds = new google.maps.LatLngBounds();
                                            for (var i = 0; i < locations.length; i++) 
                                            {
                                                    //alert(locations[i][1]);
                                                    //alert(locations[i][2]);

                                               var lat = locations[i][1];
                                               var lng = locations[i][2];

                                               var latlng2 = new google.maps.LatLng(lat, lng);
                                               latlngbounds.extend(latlng2);
                                            }

                                            map.setCenter(latlngbounds.getCenter());
                                            map.fitBounds(latlngbounds); 

                                            // end to set map possition in center to show all markers
                                        }
                                      
                                                                       
				 }
                infowindow = new google.maps.InfoWindow({
					size: new google.maps.Size(150, 50)
				});
				 google.maps.event.addListener(infowindow,'closeclick',function(){
                                             for (var prop in markerArray) {
                                                 markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
                                            }
                                           });
                                           function createMarker(latlng,myContent , myNum, myTitle,id,lid) {
				        var cat_id = id; 
					var contentString = myContent;
					var marker = new google.maps.Marker({
						position: latlng,
						map: map,
						zIndex: Math.round(latlng.lat() * -100000) << 5,
						title: myTitle,
						icon: new google.maps.MarkerImage('<?php echo ASSETS_IMG?>/c/pin-small.png')
					});
                                          // marker.setVisible(false);
                                           
					google.maps.event.addListener(marker, 'click', function() {
                                             for (var prop in markerArray) {
                                                 markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
                                            }
						infowindow.setContent(contentString);
						infowindow.open(map, marker);
                                                       
                                                       $("#div_msg").val("");
                                                       $("#div_msg").css("display",'none');
                                                marker.setIcon('<?php echo ASSETS_IMG?>/c/pin-small-blue.png');
                                                $("#activatedeal").removeAttr("disabled");
					  jQuery("#hdn_location").val(lid);
                                                //parent.document.getElementById("slider_iframe").src = parent.document.getElementById("slider_iframe").src+"&order_by_cat="+cat_id;
					});
                                      
					
                                        infowindowcontent[lid]=myContent;
					markerArray[lid]=marker;
                                        
					//markerArray.push(marker); //push local var marker into global array
				}
				initialize();
                                               
                         
</script>
                    <?php }
                }
}
             
}

/**
 * @uses register store
 * @param message,activationcode
 * @used in pages : location_detail.php,mydeals.php,search-deal.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnRegisterStore'])){

/****** check customer session ************/
if(!isset($_SESSION['customer_id'])){

$json_array = array();
if(isset($_REQUEST['reload']))
{
$url_perameter_2 = "?purl=".urlencode("@".$_REQUEST['campaign_id']."|".$_REQUEST['location_id']."@");

}
else
{
$url_perameter_2 ="";
}
$json_array['loginstatus'] = "false";
if(isset($_REQUEST['r_url']))
{
if(isset($_REQUEST['is_location']))
{

$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".WEB_PATH."/search-deal.php".$url_perameter_2;
}
else
{
$json_array['link'] = WEB_PATH."/register.php?url=".WEB_PATH."/search-deal.php".$url_perameter_2;
}

}
else{
if(isset($_REQUEST['is_location']))
{

$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
}
else
{
$json_array['link'] = WEB_PATH."/register.php?url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
}
}
$json_array['is_profileset'] = 1;
$json = json_encode($json_array);

//header("Location:".WEB_PATH."/register.php?url=".$_SERVER['HTTP_REFERER']);
echo $json;
exit();
}

/****** check customer session ************/
	
	$storearray = $json_array = array();
	$location_id= base64_decode($_REQUEST['location_id']);
	//$location_id= $_REQUEST['location_id'];
	if($_REQUEST['customer_id'] == ""){
		$customer_id = $_SESSION['customer_id'];

		
	}else
	{
		$customer_id = get_cutomer_session_id($_REQUEST['customer_id']);

	}
	
	if($customer_id == "")
	{
            $json_array['message'] = "Invalid Customer ID";
	}
	else
	{	
		$storearray['location_id']=$location_id;		
		$RSstoe = $objDB->Show("campaign_location",$storearray);
	    if($RSstoe->RecordCount()<=0)
		{
            $json_array['loginstatus'] = "false";
			$json_array['status'] = "false";
			$json_array['message'] = "There is no campaign for this store";
			$json = json_encode($json_array);
			echo $json;

                        if(isset($_REQUEST['page']))
                        {
						//$redirect_query_location = "select location_permalink from locations where id=".$location_id;
						 //$RS_redirect_query = $objDB->Conn->Execute($redirect_query_location);		
						 $RS_redirect_query = $objDB->Conn->Execute("select location_permalink from locations where id=?",array($location_id));
                                                 
						 $location_url = $RS_redirect_query->fields['location_permalink'];
                            header("Location:".$location_url);
                            exit();
                        }
                        else 
                        {
                        }
		}
		else
		{
        
                    //$sql_group = "select id , merchant_id from merchant_groups where location_id =".$location_id." and private = 1";
                  
                    //$RS_group = $objDB->Conn->Execute($sql_group);		
                    $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?",array($location_id,1));		
        
                    
                if($RS_group->RecordCount()>0){
//                    $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;
//                    
//                    $RS_user_group =$objDB->Conn->Execute($sql_user_group);
                    $RS_user_group =$objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id = ?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));
            
                    if($RS_user_group->RecordCount()<=0)
                    {
                            $array_group= array();
                            $array_group['merchant_id']=$RS_group->fields['merchant_id'];
                            $array_group['group_id']=$RS_group->fields['id'];
                            $array_group['user_id']=$customer_id;
                            $objDB->Insert($array_group, "merchant_subscribs");
                    }
                }
            
                
                //make entry in subscribe store table
                //$sql_chk ="select * from subscribed_stores where customer_id= ".$customer_id." and location_id=".$location_id;
                          //$subscibed_store_rs =$objDB->Conn->Execute($sql_chk);
                          $subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=?",array($customer_id,$location_id));
                        if($subscibed_store_rs->RecordCount()==0)
                        {
                       /* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id=  ".$customer_id.",location_id=".$location_id." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                         $objDB->Conn->Execute($insert_subscribed_store_sql);*/
			$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=?,location_id=? ,subscribed_date=? ,subscribed_status=?",array($customer_id,$location_id,date('Y-m-d H:i:s'),1));

                        }
                        else{
                            if($subscibed_store_rs->fields['subscribed_status']==0)
                            {
                                /*$up_subscribed_store = "Update subscribed_stores set subscribed_status=1 where  customer_id= ".$customer_id." and location_id=".$location_id;
                                 $objDB->Conn->Execute($up_subscribed_store);*/
				$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=? where  customer_id=? and location_id=?",array(1,$customer_id,$location_id));
                            }
                        }
                
			$json_array['loginstatus'] = "true";
			$json_array['status'] = "true";
			$json_array['message'] = "The campaigns have been added to my deals.";
			$json = json_encode($json_array);
			echo $json;
                        if(isset($_REQUEST['page']))
                        {
						//$redirect_query_location = "select location_permalink from locations where id=".$location_id;
						 //$RS_redirect_query = $objDB->Conn->Execute($redirect_query_location);		
						 $RS_redirect_query = $objDB->Conn->Execute("select location_permalink from locations where id=?",array($location_id));		
						 $location_url = $RS_redirect_query->fields['location_permalink'];
                            header("Location:".$location_url);
                            //header("Location: location_detail.php?id=".$location_id);
                            exit();
                        }
                        else 
                        {
                            exit();
                        }
		}	
		
	}
		$json_array['loginstatus'] = "false";		
	    $json_array['status'] = "false";		
		$json = json_encode($json_array);
		echo $json;
                if(isset($_REQUEST['page']))
                {
//				$redirect_query_location = "select location_permalink from locations where id=".$location_id;
//						 $RS_redirect_query = $objDB->Conn->Execute($redirect_query_location);		
						 $RS_redirect_query = $objDB->Conn->Execute("select location_permalink from locations where id=?",array($location_id));		
						 $location_url = $RS_redirect_query->fields['location_permalink'];
                            header("Location:".$location_url);
                    //header("Location: location_detail.php?id=".$location_id);
                    exit();
                }
                else 
                {
                }
}

/**
 * @uses generate coupon
 * @param activation_code
 * @used in pages : no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGenerateCoupon'])){
	$array = $json_array = array();
	$array['activation_code'] = mysql_escape_string($_REQUEST['activation_code']);
//	$Sql = "SELECT AC.*, C.*
//			FROM activation_codes AC, campaigns C
//			WHERE AC.activation_code='$array[activation_code]' AND AC.campaign_id=C.id";
//	$RS = $objDB->Conn->Execute($Sql);		
	$RS = $objDB->Conn->Execute("SELECT AC.*, C.*
			FROM activation_codes AC, campaigns C
			WHERE AC.activation_code=? AND AC.campaign_id=C.id",array($array[activation_code]));		
	//$RS = $objDB->Show("activation_codes",$array);
	if($RS->RecordCount()<=0){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Activation Code."; 
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	if($RS->fields['max_coupns'] == NULL) $RS->fields['max_coupns'] = 0;
	$json_array['status'] = "false";
//	$Sql = "SELECT COUNT(*) as total FROM coupon_codes WHERE customer_campaign_code='$array[activation_code]'";
//	$RSTotal = $objDB->Conn->Execute($Sql);
	$RSTotal = $objDB->Conn->Execute("SELECT COUNT(*) as total FROM coupon_codes WHERE customer_campaign_code=?",array($array[activation_code]));
	if($RS->fields['max_coupns'] <= $RSTotal->fields['total']){
		$json_array['status'] = "false";
		$json_array['message'] = "Execeeded total number of generated coupens.";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$coupon_code = $RS->fields['campaign_id'].date("YmdHis");
	/*$Sql = "INSERT INTO coupon_codes SET customer_campaign_code='$array[activation_code]', coupon_code='$coupon_code', generated_date=Now()";
	$objDB->Conn->Execute($Sql);*/
	$objDBWrt->Conn->Execute("INSERT INTO coupon_codes SET customer_campaign_code=?, coupon_code=?, generated_date=?",array($array[activation_code],$coupon_code,'Now()'));

	$json_array['status'] = "true";
	// 369
	if(isset($_REQUEST['customer_id'])){
		$json_array['message'] = WEB_PATH."/my-deals.php?campaign_id=".$RS->fields['campaign_id'];
	}else{
		$json_array['message'] = "You are not logged in";
	}
	// 369
	$json = json_encode($json_array);
	echo $json;
	exit();

}

/**
 * @uses update profile has mendatory fields
 * @param customer_id,firstname,lastname,country,state,city,postalcode
 * @used in pages : comapign.php,campaign_facebook.php,location_detail.php,search-deal.php,header.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnUpdateProfile_compulsary_field']))
{
   
    $array = $json_array = $where_clause = array();
    $where_clause['id'] = $_REQUEST['customer_id'];
    $array['firstname'] = $_REQUEST['firstname'];
	$array['lastname'] = $_REQUEST['lastname'];
	
	$array['country'] = $_REQUEST['country'];
	
	$array['state'] = $_REQUEST['state'];
	$array['city'] = $_REQUEST['city'];
	
    $postalcode=str_replace(" ","",$_REQUEST['postalcode']);
    $array['postalcode'] = strtoupper($postalcode);
	
    $array['gender'] = $_REQUEST['gender'];
	
    $array['dob_month'] = $_REQUEST['dob_month'];
    $array['dob_day'] = $_REQUEST['dob_day'];
    $array['dob_year'] = $_REQUEST['dob_year'];
    
    $string_address =  $_REQUEST['country'].",".$_REQUEST['postalcode'];
	$string_address = urlencode($string_address);
	$geocode= file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$string_address."&sensor=false");
	$geojson= json_decode($geocode,true);
	if($geojson['status']=='OK'){
		$array['curr_latitude'] = $geojson['results'][0]['geometry']['location']['lat'];
		$array['curr_longitude'] = $geojson['results'][0]['geometry']['location']['lng'];
                    $timezone1  = getClosestTimezone($array['curr_latitude'] ,$array['curr_longitude'] );
                  $timezone = new DateTimeZone($timezone1);
                  $offset1   = $timezone->getOffset(new DateTime);
                   // //timezone_offset_string( $offset1 );
                 $tz = timezone_offset_string( $offset1 );
                 $array['curr_timezone'] = $tz;

}else {
              $array['curr_latitude'] = "";
		$array['curr_longitude'] = "";
                $array['curr_timezone'] = "";
              
        }
        $objDB->Update($array, "customer_user", $where_clause);
        $json_array['message'] = "true";
	$json_array['message'] = "Profile has been updated successfully";
        $json = json_encode($json_array);
	
	echo $json;
	exit();
    
}

/**
 * @uses update profile
 * @param firstname_pp,lastname_pp,gender_pp,dob_month_pp,dob_day_pp,dob_year_pp,mobileno_area_code_pp,mobileno2_pp,mobileno_pp
 * 
 * @used in pages : my-profile.php.campaign.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnUpdateProfile'])){
    
	
	$array = $json_array = $where_clause = array();
	$array['firstname'] = ucwords(strtolower($_REQUEST['firstname_pp']));
	$array['lastname'] = ucwords(strtolower($_REQUEST['lastname_pp']));
	
	$array['gender'] = $_REQUEST['gender_pp'];
	
	$array['dob_month'] = $_REQUEST['dob_month_pp'];
	$array['dob_day'] = $_REQUEST['dob_day_pp'];
	$array['dob_year'] = $_REQUEST['dob_year_pp'];
	
	if($_REQUEST['mobileno_area_code_pp']!="" && $_REQUEST['mobileno2_pp']!="" & $_REQUEST['mobileno_pp']!="")
	{	
		$mobile_no=$_REQUEST['mobile_country_code_pp']."-".$_REQUEST['mobileno_area_code_pp']."-".$_REQUEST['mobileno2_pp']."-".$_REQUEST['mobileno_pp'];
		//$mobile_no=$_REQUEST['mobileno_area_code_pp']."-".$_REQUEST['mobileno2_pp']."-".$_REQUEST['mobileno_pp'];
		$array['mobileno']=$mobile_no;
	}
	$array['country'] = $_REQUEST['country_pp'];
	
	$postalcode=str_replace(" ","",$_REQUEST['postalcode1_pp']);
	$array['postalcode'] = strtoupper($postalcode);
	
	$array['city'] = ucwords(strtolower($_REQUEST['city_pp']));
	$array['state'] = $_REQUEST['state_pp'];
	
	$array['profile_pic'] = $_REQUEST['hdn_image_path'];
	
	$array['emailnotification'] = $_REQUEST['emailnotification'];
	//$array['current_location'] = $_REQUEST['current_location'];
        if($_REQUEST['hdn_current_address'] !="")
        {
        }
        else{
	
	$string_address =  $_REQUEST['city_pp'].",".$_REQUEST['state_pp'].",".$_REQUEST['country_pp'].",".$_REQUEST['postalcode1_pp'];
	$string_address = urlencode($string_address);
	$geocode= file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$string_address."&sensor=false");
	$geojson= json_decode($geocode,true);
	if($geojson['status']=='OK'){
		$array['curr_latitude'] = $geojson['results'][0]['geometry']['location']['lat'];
		$array['curr_longitude'] = $geojson['results'][0]['geometry']['location']['lng'];
                    $timezone1  = getClosestTimezone($array['curr_latitude'] ,$array['curr_longitude'] );
                  $timezone = new DateTimeZone($timezone1);
                  $offset1   = $timezone->getOffset(new DateTime);
                    //timezone_offset_string( $offset1 );
                 $tz = timezone_offset_string( $offset1 );
                 $array['curr_timezone'] = $tz;

}else {
              $array['curr_latitude'] = "";
		$array['curr_longitude'] = "";
                $array['curr_timezone'] = "";
              
        }
	
        }        
        $where_clause['id'] = $_SESSION['customer_id'];
                
	if($where_clause['id'] == ""){
		$json_array['message'] = "Invalid Customer ID";
	}else{
		$objDB->Update($array, "customer_user", $where_clause);
		$json_array['message'] = "Profile has been updated successfully";
                $json_array['image_url'] = ASSETS_IMG."/c/usr_pic/". $_REQUEST['hdn_image_path'];
	}
	
	$json = json_encode($json_array);
	
	echo $json;
	exit();

}

/**
 * @uses get profile
 * @param customer_id
 * 
 * @used in pages : no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnGetProfile'])){

	$array = $json_array = $where_clause = array();
	
	if($_REQUEST['customer_id'] == ""){
		$where_clause['id'] = $_SESSION['customer_id'];
		
	}else{
		$where_clause['id'] = get_cutomer_session_id($_REQUEST['customer_id']);
	}
	if($where_clause['id'] == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Invalid Customer ID";
	}else{
		$RS=$objDB->Show("customer_user", $where_clause);
		if($RS->RecordCount()<=0)
		{
			$json_array['message'] = "Invalid Customer Id";
		}
		else
		{
			$Row = $RS->FetchRow();
			$json_array['status'] = "true";
			$json_array['customer_id'] = $Row['id'];
			$json_array['firstname'] = $Row['firstname'];
			$json_array['lastname'] = $Row['lastname'];
			$json_array['emailaddress'] = $Row['emailaddress'];
			$json_array['current_location'] = $Row['current_location'];
			
		}
	}
	
	$json = json_encode($json_array);
	echo $json;
	exit();

}
/**
 * @uses activate new deal
 * @param customer_id
 * 
 * @used in pages : no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnActivateNewDeal'])){
	$json = $objJSON->activate_new_deal($_REQUEST['customer_id'], $_REQUEST['activation_code']);
	echo $json;
	exit();	
}

/**
 * @uses activate new deal campignpage
 * @param activation_code,campid,loc_id
 * @used in pages : process.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['activate_new_deal_campaignpage']))
{
	$json = $objJSON->activate_new_deal("",$_REQUEST['activation_code'],$_REQUEST['campid'],$_REQUEST['loc_id']);
	echo $json;
	exit();	
}


/**
 * @uses update password 
 * @param customer_id
 * @used in pages : change-password.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnUpdatePassword'])){
	$array = $json_array =array();
	
	$array['id'] = $_SESSION['customer_id'];
	if($array['id'] == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Invalid customer id";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	else
	{

			if($_REQUEST['old_password'] == ""){
				$json_array['message'] = "Please enter current password";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			if($_REQUEST['new_password'] == ""){
				$json_array['message'] = "Please enter new password";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			if($_REQUEST['con_new_password'] == ""){
				$json_array['message'] = "Please enter confirm new password";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			if($_REQUEST['new_password'] != $_REQUEST['con_new_password']){
				$json_array['message'] = "New password does not match";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			
		//	$array['password'] = md5($_REQUEST['old_password']);
			$RS = $objDB->Show("customer_user",$array);
			$PasswordLib2 = new \PasswordLib\PasswordLib;
			$result = $PasswordLib2->verifyPasswordHash($_REQUEST['old_password'],$RS->fields['password']);
			if(!$result ){
				$json_array['message'] = "Please enter Valid current Password";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			$array = $json_array = $where_clause = array();
			$PasswordLib = new \PasswordLib\PasswordLib;
//$hash = $PasswordLib->createPasswordHash($password);
	//$array['password'] = $PasswordLib->createPasswordHash($_REQUEST['password']);
			$array['password'] =  $PasswordLib->createPasswordHash($_REQUEST['new_password']);
			
                        $where_clause['id'] = $_SESSION['customer_id'];
                        
			if($where_clause['id'] == ""){
				$json_array['message'] = "Invalid Customer ID";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			
			$objDB->Update($array, "customer_user", $where_clause);
			
			$json_array['message'] = "Password has been changed successfully";
			$json = json_encode($json_array);
			echo $json;
			exit();
	}
}

/**
 * @uses update forgot password
 * @param new_password,con_new_password,mycaptcha_fpc
 * @used in pages : forgot_password.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnUpdateForgotPassword']))
{
		
	$array = $json_array =array();
	$cust_id = $_SESSION['forgot_cust_id'];
	
	if($cust_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Invalid Customer ID";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	else
	{

			if($_REQUEST['new_password'] == ""){
				$json_array['message'] = "Please enter New Password";
				$json_array['status'] = "false";
				//$_SESSION['msg'] = "Please enter New Password";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			if($_REQUEST['con_new_password'] == ""){
				$json_array['message'] = "Please enter Confirm New Password";
				$json_array['status'] = "false";
				//$_SESSION['msg'] = "Please enter Confirm New Password";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			if($_REQUEST['new_password'] != $_REQUEST['con_new_password']){
				$json_array['message'] = "New Password does not Match";
				$json_array['status'] = "false";
				//$_SESSION['msg'] = "New Password does not Match";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			
                        if($_REQUEST['mycaptcha_fpc'] == ""){
				$json_array['message'] = "Please enter captcha";
				$json_array['status'] = "false";
				//$_SESSION['msg'] = "Please enter captcha";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
                        
			if(strtolower($_REQUEST['mycaptcha_fpc'])==strtolower($_SESSION['random_number_c_f_p']))
                        {

                        }
                        else 
                        {
                            $json_array['status'] = "false";
                            $json_array['message'] = "Captcha does not match";
                            $json = json_encode($json_array);
                            echo $json;
                            exit();
                        }
			
			$array = $json_array = $where_clause = array();
			$PasswordLib = new \PasswordLib\PasswordLib;
//$hash = $PasswordLib->createPasswordHash($password);
	//$array['password'] = $PasswordLib->createPasswordHash($_REQUEST['password']);
			$array['password'] =  $PasswordLib->createPasswordHash($_REQUEST['new_password']);
			//$array['password'] = md5($_REQUEST['new_password']);
			
			
			$where_clause['id'] = $cust_id;
			
			if($where_clause['id'] == ""){
				$json_array['message'] = "Invalid Customer ID";
				$json_array['status'] = "false";
				//$_SESSION['msg'] = "Invalid Customer ID";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			
			$objDB->Update($array, "customer_user", $where_clause);
			
			$json_array['message'] = "Password has been changed successfully";
			$json_array['status'] = "true";
			//$_SESSION['msg'] = "Password has been changed successfully";
			$json = json_encode($json_array);
			echo $json;
			exit();
	}
}

/**
 * @uses search deal by zipcode
 * @param zip
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnSearchDealByZip'])){
	$json_array = array();
	$zip = mysql_escape_string($_REQUEST['zip']);
//	$Sql = "SELECT 
//			L.location_name,L.address,L.city, L.state, L.zip, L.country , 
//			CL.campaign_id, C.title, C.business_logo, C.description, C.start_date, C.expiration_date
//			FROM locations L, campaign_location CL, campaigns C
//			WHERE L.zip='$zip' AND CL.location_id=L.id AND C.id=CL.campaign_id
//			ORDER BY L.id DESC";
//			
//	$RS = $objDB->Conn->Execute($Sql);
	$RS = $objDB->Conn->Execute("SELECT 
			L.location_name,L.address,L.city, L.state, L.zip, L.country , 
			CL.campaign_id, C.title, C.business_logo, C.description, C.start_date, C.expiration_date
			FROM locations L, campaign_location CL, campaigns C
			WHERE L.zip=? AND CL.location_id=L.id AND C.id=CL.campaign_id
			ORDER BY L.id DESC",array($zip));
        
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$Row['description'] = htmlentities($Row['description']);
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = $RS->RecordCount();
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses get deal details
 * @param zip,customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnGetDealDetails'])){
	
	if($_REQUEST['customer_id'] == ""){
		$array['id'] = $_SESSION['customer_id'];
		
	}else{
		$array['id'] = get_cutomer_session_id($_REQUEST['customer_id']);
	}
	
	if($array['id'] == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Invalid Customer ID";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	else
	{
		$json = $objJSON->get_compain_details($_REQUEST['campaign_id']);
		echo $json;
		exit();	
	}
}
/**
 * @uses print coupon
 * @param customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnPrintCoupon'])){
	
	if($_REQUEST['customer_id'] == ""){
		$json_array['message'] = "Please enter customer id";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	else
	{
		$array = $json_array = array();
		$array['campaign_id'] = mysql_escape_string($_REQUEST['campaign_id']);
		$RS = $objDB->Show("activation_codes",$array);
		if($RS->RecordCount()>0){
			$Row = $RS->FetchRow();
			$json_array['status'] = "true";
			$json_array['total_records'] = $RS->RecordCount();
			$json_array[0] = $Row;
		}else{
			$json_array['status'] = "false";
			$json_array['total_records'] = $RS->RecordCount();
		}
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
}

/**
 * @uses get customer's deals
 * @param customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnMyDeals'])){
	$json = $objJSON->get_customer_deals($_REQUEST['customer_id'], $_REQUEST['order']);
	echo $json;
	exit();	
}

/**
 * @uses get customer's expire deals
 * @param customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnGetExpiredDeals'])){
	$json = $objJSON->get_customer_expire_deals($_REQUEST['customer_id']);
	echo $json;
	exit();	
}

/**
 * @uses share to friends
 * @param txt_share_frnd
 * @used in pages :campaign.php,campaign_facebook.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btn_share']))
{
   
    $email_arr = explode(";",$_REQUEST['txt_share_frnd']);
    $arr = array();
  
  
    for($i=0;$i<count($email_arr);$i++)
    {
     
        $email_arr1 = explode(",",$email_arr[$i]);
       if(is_array($email_arr1)){
        for($j=0;$j<count($email_arr1);$j++)
        {
            array_push($arr,$email_arr1[$j]) ;
        }
       }  else {
           array_push($arr,$email_arr[$i]) ;
       }
       
    }

  
	              for($i=0;$i<count($arr);$i++)
                       {
				$array = $json_array = array();
				$array['emailaddress'] = $arr[$i];
        
		/* 15-03-2014 share counter */
		
		$array_loc = array();
		$array_loc['id'] = $_REQUEST['refferal_location_id'];
		$RS_location = $objDB->Show("locations",$array_loc);
		$time_zone=$RS_location->fields['timezone_name'];
		//date_default_timezone_set($time_zone);
		$timestamp = $_REQUEST['timestamp'];
		
		$share_counter = array();
		$share_counter['customer_id'] = $_SESSION['customer_id'];
		$share_counter['campaign_id'] = $_REQUEST['reffer_campaign_id'];
		$share_counter['location_id'] = $_REQUEST['refferal_location_id'];
		$share_counter['campaign_share_domain'] = $_REQUEST['domain'];
		$share_counter['campaign_share_medium'] = $_REQUEST['medium'];
		$share_counter['timestamp'] = $timestamp;
		$objDB->Insert($share_counter, "share_counter");
		
		/* 15-03-2014 share counter */
		
		$activate_link = WEB_PATH."/register.php?campaign_id=".$_REQUEST['reffer_campaign_id']."&l_id=".$_REQUEST['refferal_location_id']."&share=true&customer_id=".base64_encode($_SESSION['customer_id'])."&domain=".$_REQUEST['domain'];
  
      $mail = new PHPMailer();
      
      $array2 = $json_array = array();
      $array2['id'] =$_REQUEST['refferal_location_id'] ;
      $RS_location = $objDB->Show("locations",$array2);
      
      $array1 = $json_array = array();
      $array1['id'] =$_REQUEST['reffer_campaign_id'] ;
      $RS_campaigns = $objDB->Show("campaigns",$array1);
      
      $array2 = $json_array = array();
      $array2['id'] =$RS_campaigns->fields['created_by'] ;
      $RS_camp_mer = $objDB->Show("merchant_user",$array2);
      
        //$body = "<p>I thought you might enjoy knowing this deal from Scanflip.</p>";
	//$body .= "<p>Please  <a href='".$activate_link."'>Click Here</a> to register / login with scanflip. Share deal with your friend and earn share points </p>";
                                        
	$body ="<div id='dealslist'>";
		
		
		$body .="<div class='offersexyellow' style='background: none repeat scroll 0 0 #D1D1D1 !important;border: 1px solid #D1D1D1;color: #000000;margin: 2px 0 10px;padding: 5px 5px 10px;position: relative;'>";
			$body .="<div id='campaign_detail'>";
                         $body .="<p style='text-align:right;width:645px; margin :0 auto'>";
						$body .="<img alt='Scanflip' src='".ASSETS_IMG."/c/email_sharing.png'>";
						$body .="</p>";	
                        $body .="<div class='offerstrip' style='font-size:18px;background:#fff; width:630px;padding-left:15px;margin :0 auto'><span 
                            '>".$_SESSION['customer_info']['firstname']." ".$_SESSION['customer_info']['lastname']."</span> wants to share offer with you on scanflip</div>";
                       			
						
					$body .="<div style='overflow:hidden;padding:15px; width:615px; background:#fff; margin :0 auto'>";
	
						$body .="<div class='other_details' style='float: right;width:450px; '>";
							
	                         $body .="<div class='dealtitle' style='border-bottom: 1px dashed #C8C8C8;font-size: 19px;overflow: hidden;text-align: justify;'><b>".$RS_campaigns->fields['title']."</b></div>";
							 
						//	$body .="<div class='percetage' style='float:left;color: #000000;font-family: Arial;font-size: 16px;line-height: 25px;overflow: hidden;padding: 4px 85px 7px 0;text-shadow: 1px 1px 1px #A09F9F;'>";
                                                                //$urltitle=WEB_PATH."/location_detail.php?id=".$RS_location->fields['id'];
                                                                //$body .="<a href='".$urltitle."'>".$RS_location->fields['location_name']."</a>";
                                                                $body .="<div>".$RS_location->fields['location_name'];
                                                        $body .="</div>";
	
	
							$body .="<div class='counter' style='clear:both;margin: -8px 0 0;overflow: hidden;padding: 0px; float:left;'>";	
							$body .="<div style='clear:both'>";
	
								
								
								
								$address = "Location : ".$RS_location->fields['address'].", ".$RS_location->fields['city'].", ".$RS_location->fields['state'].", ".$RS_location->fields['zip'].", ".$RS_location->fields['country'];
                                                                if($RS_location->fields['phone_number']!="")
                                                                {
                                                                    $phno = $RS_location->fields['phone_number'];
																	$phno = explode("-",$phno);
                     												$phno = "Phone Number : (".$phno[1].") ".$phno[2]."-".$phno[3];
                                                                }
                                                                else
                                                                {
                                                                    $phno = $RS_camp_mer->fields['phone_number'];
																	$phno = explode("-",$phno);
                     												$phno = "Phone Number : (".$phno[1].") ".$phno[2]."-".$phno[3];
                                                                }
                                                                
								
								$body .="<div style='margin-top:5px;float:left'>";
								$body .=$address;
								$body .="</div>";
                                                                
                                                                $body .="<div style='margin-top:5px;float:left'>";
								$body .=$phno;
								$body .="</div>";
								
								 $from_lati=$_SESSION['mycurrent_lati'];
                
								$from_long=$_SESSION['mycurrent_long'];
								
								$to_lati=$RS_location->fields['latitude'];
								
								$to_long=$RS_location->fields['longitude'];
								
								$maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;
								
							$body .="</div>";
                                                        
                                                        $body .="</div>";
						$body .="</div>";
							$body .="<p class=image_det' style='border: 3px solid #BBBBBB;float: left;margin: 0 !important;overflow: hidden;padding: 0 !important;width: 130px;'>";
							
							if($RS_campaigns->fields['business_logo']!="")
							{
							    $img_src=ASSETS_IMG."/m/campaign/".$RS_campaigns->fields['business_logo']; 
							}
                        				else 
							{
							    $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
							}
                        
							$activate_link = WEB_PATH."/register.php?campaign_id=".$_REQUEST['reffer_campaign_id']."&l_id=".$_REQUEST['refferal_location_id']."&share=true&customer_id=".base64_encode($_SESSION['customer_id'])."&domain=".$_REQUEST['domain'];
  
                        
							$body .="<img style='border: 5px solid #FFFFFF;height: auto !important;vertical-align:middle;width: 120px !important;' border='0' src='".$img_src."'>";
							$body .="</p>";
							$body .="<div>";
								$body .="<div class='button_wrapper' style='float:left;height: 30px; padding:12px 4px 0 4px; text-align:left; margin-top: 22px;clear:both;width:145px'>";
								$body .="<a class='reserve_print' href='".$activate_link."' style='background: url(".ASSETS_IMG."/c/vie-button.png) repeat-x scroll 0 0 transparent !important;border: 1px solid #FE8915;border-radius:2px;color: #FFFFFF !important;font-size: 12px;font-weight: bold;padding:4px 12px 4px 12px;text-decoration:none;'>Get Offer</a>";
								$body .="</div>";
								
								$body .="<div style='float:right;padding-top:25px;padding-right:30px'>";
								$body .="<img style='width:100px' border='0' src='".ASSETS_IMG."/c/app_store.png' >";
								$body .="<img style='width:100px' border='0' src='".ASSETS_IMG."/c/google_play.png'>";
								$body .="</div>";
							$body .="</div>";
							
							
							
						$body .="</div>";
                                                
                                                
                                                
                                                
                                                $body .="<div style='width:630px !important; margin: 0 auto; padding-top:10px; '>";
		     
			$body .="<img alt='Scanflip' src='".ASSETS_IMG."/c/email_sharing.png'>";
			$body .="<br>";
			$body .="Powering Smart Savings From Local Merchants";
			$body .="</div>";
			$body .="</div>";
			$body .="</div>";
		       	$body .="</div>";
                        $body .= "<p >Thanks, </p>";
                	$body .= "<p >". $_SESSION['customer_info']['firstname']." ".$_SESSION['customer_info']['lastname']." </p>";
			$body .="</div>";
					
			$newbody ='';
			$newbody .='<body bgcolor="#e4e4e4" style="font-size:11px;font-family: arial,helvetica neue,helvetica,sans-serif; line-height: normal;color:#606060; margin:8px; padding:0;">
<table cellspacing="0" cellpadding="0"  style="width:100%; border:0;clear:both; margin:20px 0;">
  <tbody style="width:100%; display:inline-block;">
    <tr style="width:100%; display:inline-block;">
      <td style="width:100%; display:inline-block;"><table align="center" bgcolor="#D2D2D2" style="width:100%; max-width:600px; padding:20px; border-radius: 10px;">
          <tbody>
            <tr>
              <td  style="width:100%; display:inline-block;"><img src="'.ASSETS_IMG.'/c/scanflip-logo.png" width="205" height="30" alt="Scanflip"
												style="-ms-interpolation-mode: bicubic; padding:0 0 20px;"></td>
            </tr>
            <tr>
              <td><table bgcolor="#FFF" style="width:100%; display:inline-block;border-left:5px solid #F37E0A;">
                  <tbody>
                    <tr align="left" style=" width:100%; display:inline-block;">
                      <td style="display:inline-block; padding:15px;"><div style="font-size:16px;font-weight: bold; color:#000; width:100%; display:inline-block; font-family:Arial;">'.$_SESSION['customer_info']['firstname']." ".$_SESSION['customer_info']['lastname'].' wants to share offer with you on Scanflip</div></td>
                    </tr>
                    <tr bgcolor="#FFF" style="width:100%; display:inline-block;">
                      <td valign="top" style="padding:15px 0px 0px 15px;"><p style=" margin:0;margin:0!;padding:0;"> <img src="'.$img_src.'" width="80" height="80" style="border:3px solid #bbbbbb;padding:5px;min-height:auto;vertical-align:middle;"></p></td>
                      <td  bgcolor="white" style="padding:15px 15px 0px;"><div style=" width:100%; display:inline-block;border-bottom:1px dashed #c8c8c8;font-size:19px;text-align:justify"><b>'.$RS_campaigns->fields['title'].'</b></div>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0; padding:5px 0; display:inline-block;">'.$RS_location->fields['location_name'].'</p>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;">'.$address.'</p>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0; padding:5px 0;display:inline-block;">'.$phno.'</p>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0; padding:5px 0;display:inline-block;"></p>
                        </td>
                    </tr>
                    <tr style=" width:100%; display:inline-block;">
                      <td valign="top"; align="left" style="display: block;padding:0 15px 0;font-size:11px;font-family:lucida grande,tahoma,verdana,arial,sans-serif;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;color:#999999;border:none"><h5 align="left"  style="margin:0px; text-align:left; width:auto; padding:15px 0 0 0; display:inline-block;"><a style="background:#F37E0A; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; display:inline-block; border-radius: 3px; color: #FFFFFF; font-size: 14px; padding: 5px 15px; text-align: center; text-decoration: none;" href="'.$activate_link.'">Get Offer</a></h5></td>
                    </tr>
                    <tr style=" width:100%; display:inline-block;">
                      <td valign="top"; align="right" style="display: block;padding:0px 15px 15px;font-size:11px;font-family:lucida grande,tahoma,verdana,arial,sans-serif;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;color:#999999;border:none"><div style=" width:100%; display:inline-block; text-align:right;padding-top:15px;">
                      <img border="0" src="'.ASSETS_IMG.'/c/app_store.png" style="width:100px;display: inline-block; height:auto;">
                      <img border="0" src="'.ASSETS_IMG.'/c/google_play.png" style="width:100px;display: inline-block; height:auto;"></div></td>
                    </tr>
                  </tbody>
                </table></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>
</body>';
				        
					$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
					$mail->AddAddress($array['emailaddress']);
					$mail->From = "no-reply@scanflip.com";
					$mail->FromName = "ScanFlip Support";
					$mail->Subject    = "Scanflip offer - ".$RS_campaigns->fields['title'];
					//$mail->MsgHTML($body);
					$mail->MsgHTML($newbody);
					$mail->Send();

				
}
 // 4-7-2014
//$redirect_query = "select permalink from campaign_location where campaign_id=".$_REQUEST['reffer_campaign_id']." and location_id=".$_REQUEST['refferal_location_id'];
//$redirect_RS = $objDB->Conn->Execute($redirect_query );
$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($_REQUEST['reffer_campaign_id'],$_REQUEST['refferal_location_id']) );
			 
//$path = WEB_PATH."/campaign.php?campaign_id=".$_REQUEST['reffer_campaign_id']."&l_id=".$_REQUEST['refferal_location_id'];
	 header("Location:".$redirect_RS->fields['permalink']);

}

/**
 * @uses share campaign grid
 * @param txt_share_frnd
 * @used in pages :campaign.php,campaign_facebook.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btn_share_grid']))
{
   
    $email_arr = explode(";",$_REQUEST['txt_share_frnd']);
    $arr = array();
  
  
    for($i=0;$i<count($email_arr);$i++)
    {
     
        $email_arr1 = explode(",",$email_arr[$i]);
       if(is_array($email_arr1)){
        for($j=0;$j<count($email_arr1);$j++)
        {
            array_push($arr,$email_arr1[$j]) ;
        }
       }  else {
           array_push($arr,$email_arr[$i]) ;
       }
       
    }

  
		  for($i=0;$i<count($arr);$i++)
			   {
				echo  $arr[$i]."<br />";
		$array = $json_array = array();
		$array['emailaddress'] = $arr[$i];
	 
		/* 15-03-2014 share counter */
		
		$array_loc = array();
		$array_loc['id'] = $_REQUEST['refferal_location_id'];
		$RS_location = $objDB->Show("locations",$array_loc);
		$time_zone=$RS_location->fields['timezone_name'];
		//date_default_timezone_set($time_zone);
		$timestamp=$_REQUEST['timestamp'];
		
		$share_counter = array();
		$share_counter['customer_id'] = $_SESSION['customer_id'];
		$share_counter['campaign_id'] = $_REQUEST['reffer_campaign_id'];
		$share_counter['location_id'] = $_REQUEST['refferal_location_id'];
		$share_counter['campaign_share_domain'] = $_REQUEST['domain'];
		$share_counter['campaign_share_medium'] = $_REQUEST['medium'];
		$share_counter['timestamp'] = $timestamp;
		$objDB->Insert($share_counter, "share_counter");
		
		/* 15-03-2014 share counter */
		
		$activate_link = WEB_PATH."/register.php?campaign_id=".$_REQUEST['reffer_campaign_id']."&l_id=".$_REQUEST['refferal_location_id']."&share=true&customer_id=".base64_encode($_SESSION['customer_id'])."&domain=".$_REQUEST['domain'];
		
 
      $mail = new PHPMailer();
      
    
     
      $array2 = $json_array = array();
      $array2['id'] =$_REQUEST['refferal_location_id'] ;
      $RS_location = $objDB->Show("locations",$array2);
     
     
      $array1 = $json_array = array();
      $array1['id'] =$_REQUEST['reffer_campaign_id'] ;
      $RS_campaigns = $objDB->Show("campaigns",$array1);
      
      $array2 = $json_array = array();
      $array2['id'] =$RS_campaigns->fields['created_by'] ;
      $RS_camp_mer = $objDB->Show("merchant_user",$array2);
                   
	$body ="<div id='dealslist'>";
		
		
		$body .="<div class='offersexyellow' style='background: none repeat scroll 0 0 #EBEBEB !important;border: 1px solid #D1D1D1;color: #000000;margin: 2px 0 10px;padding: 5px 5px 10px;position: relative;'>";
			$body .="<div id='campaign_detail'>";
                         $body .="<p style='text-align:right;width:645px; margin :0 auto'>";
						$body .="<img alt='Scanflip' src='".ASSETS_IMG."/c/email_sharing.png'>";
						$body .="</p>";	
                        $body .="<div class='offerstrip' style='font-size:18px;background:#fff; width:630px;padding-left:15px;margin :0 auto'><span 
                            '>".$_SESSION['customer_info']['firstname']." ".$_SESSION['customer_info']['lastname']."</span> wants to share offer with you on Scanflip</div>";
                       			
						
					$body .="<div style='overflow:hidden;padding:15px; width:615px; background:#fff; margin :0 auto'>";
	
						$body .="<div class='other_details' style='float: right;width:450px; '>";
							
	                         $body .="<div class='dealtitle' style='border-bottom: 1px dashed #C8C8C8;font-size: 19px;overflow: hidden;text-align: justify;'><b>".$RS_campaigns->fields['title']."</b></div>";
						
                                                        $body .="<div>".$RS_location->fields['location_name'];
                                                        $body .="</div>";
	
	
							$body .="<div class='counter' style='clear:both;margin: -8px 0 0;overflow: hidden;padding: 0px; float:left;'>";	
							$body .="<div style='clear:both'>";
	
								
								
								
								$address = "Location : ".$RS_location->fields['address'].", ".$RS_location->fields['city'].", ".$RS_location->fields['state'].", ".$RS_location->fields['zip'].", ".$RS_location->fields['country'];
                                                                if($RS_location->fields['phone_number']!="")
                                                                {
								     $phno = explode("-",$RS_location->fields['phone_number']);
								     
								     $phno = "Phone Number : "."(".$phno[1].") ".$phno[2]."-".$phno[3];
								     
                                                                     //$phno = "Phone Number : ".$RS_location->fields['phone_number'];
                                                                }
                                                                else
                                                                {
								    $phno = explode("-",$RS_camp_mer->fields['phone_number']);
								    $phno = "Phone Number : "."(".$phno[1].") ".$phno[2]."-".$phno[3];
                                                                    
								    //$phno = "Phone Number : ".$RS_camp_mer->fields['phone_number'];
                                                                }
                                                                
								
								$body .="<div style='margin-top:5px;float:left'>";
								$body .=$address;
								$body .="</div>";
                                                                
                                                                $body .="<div style='margin-top:5px;float:left'>";
								$body .=$phno;
								$body .="</div>";
								
								
								
								
								
								$to_lati=$RS_location->fields['latitude'];
								
								$to_long=$RS_location->fields['longitude'];
								
								$maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;
								
							$body .="</div>";
                                                        
                                                        $body .="</div>";
						$body .="</div>";
							$body .="<p class=image_det' style='border: 3px solid #BBBBBB;float: left;margin: 0 !important;overflow: hidden;padding: 0 !important;width: 130px;'>";
							
							if($RS_campaigns->fields['business_logo']!="")
							{
							    $img_src=ASSETS_IMG."/m/campaign/".$RS_campaigns->fields['business_logo']; 
							}
                        	else 
							{
							    $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
							}
                        
							
                        
							$body .="<img style='border: 5px solid #FFFFFF;height: auto !important;vertical-align:middle;width: 120px !important;' border='0' src='".$img_src."'>";
							$body .="</p>";
							$body .="<div>";
								$body .="<div class='button_wrapper' style='float:left;height: 30px; padding:12px 4px 0 4px; text-align:left; margin-top: 22px;clear:both;width:145px'>";
								$body .="<a class='reserve_print' href='".$activate_link."' style='background: url(".ASSETS_IMG."/c/vie-button.png) repeat-x scroll 0 0 transparent !important;border: 1px solid #FE8915;border-radius:2px;color: #FFFFFF !important;font-size: 12px;font-weight: bold;padding:4px 12px 4px 12px;text-decoration:none;'>Get Offer</a>";
								$body .="</div>";
								
								$body .="<div style='float:right;padding-top:25px;padding-right:30px'>";
								$body .="<img style='width:100px' border='0' src='".ASSETS_IMG."/c/app_store.png' >";
								$body .="<img style='width:100px' border='0' src='".ASSETS_IMG."/c/google_play.png'>";
								$body .="</div>";
							$body .="</div>";
						$body .="</div>";
                                                $body .="<div style='width:630px !important; margin: 0 auto; padding-top:10px; '>";
		     
			$body .="<img alt='Scanflip' src='".ASSETS_IMG."/c/email_sharing.png'>";
			$body .="<br>";
			$body .="Powering Smart Savings From Local Merchants";
		$body .="</div>";	
					$body .="</div>";
				$body .="</div>";
		       	$body .="</div>";
                        $body .= "<p >Thanks, </p>";
                
                $body .= "<p >". $_SESSION['customer_info']['firstname']." ".$_SESSION['customer_info']['lastname']." </p>";
		$body .="</div>";	
		$newbody="";
		$newbody .='<body bgcolor="#e4e4e4" style="font-size:11px;font-family: arial,helvetica neue,helvetica,sans-serif; line-height: normal;color:#606060; margin:8px; padding:0;">
<table cellspacing="0" cellpadding="0"  style="width:100%; border:0;clear:both; margin:20px 0;">
  <tbody style="width:100%; display:inline-block;">
    <tr style="width:100%; display:inline-block;">
      <td style="width:100%; display:inline-block;"><table align="center" bgcolor="#D2D2D2" style="width:100%; max-width:600px; padding:20px; border-radius: 10px;">
          <tbody>
            <tr>
              <td  style="width:100%; display:inline-block;"><img src="'.ASSETS_IMG.'/c/scanflip-logo.png" width="205" height="30" alt="Scanflip"
												style="-ms-interpolation-mode: bicubic; padding:0 0 20px;"></td>
            </tr>
            <tr>
              <td><table bgcolor="#FFF" style="width:100%; display:inline-block;border-left:5px solid #F37E0A;">
                  <tbody>
                    <tr align="left" style=" width:100%; display:inline-block;">
                      <td style="display:inline-block; padding:15px;"><div style="font-size:16px;font-weight: bold; color:#000; width:100%; display:inline-block; font-family:Arial;">'.$_SESSION['customer_info']['firstname']." ".$_SESSION['customer_info']['lastname'].' wants to share offer with you on Scanflip</div></td>
                    </tr>
                    <tr bgcolor="#FFF" style="width:100%; display:inline-block;">
                      <td valign="top" style="padding:15px 0px 0px 15px;"><p style=" margin:0;margin:0!;padding:0;"> <img src="'.$img_src.'" width="80" height="80" style="border:3px solid #bbbbbb;padding:5px;min-height:auto;vertical-align:middle;"></p></td>
                      <td  bgcolor="white" style="padding:15px 15px 0px;"><div style=" width:100%; display:inline-block;border-bottom:1px dashed #c8c8c8;font-size:19px;text-align:justify"><b>'.$RS_campaigns->fields['title'].'</b></div>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0; padding:5px 0; display:inline-block;">'.$RS_location->fields['location_name'].'</p>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;">'.$address.'</p>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0; padding:5px 0;display:inline-block;">'.$phno.'</p>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0; padding:5px 0;display:inline-block;"></p>
                        </td>
                    </tr>
                    <tr style=" width:100%; display:inline-block;">
                      <td valign="top"; align="left" style="display: block;padding:0 15px 0;font-size:11px;font-family:lucida grande,tahoma,verdana,arial,sans-serif;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;color:#999999;border:none"><h5 align="left"  style="margin:0px; text-align:left; width:auto; padding:15px 0 0 0; display:inline-block;"><a style="background:#F37E0A; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; display:inline-block; border-radius: 3px; color: #FFFFFF; font-size: 14px; padding: 5px 15px; text-align: center; text-decoration: none;" href="'.$activate_link.'">Get Offer</a></h5></td>
                    </tr>
                    <tr style=" width:100%; display:inline-block;">
                      <td valign="top"; align="right" style="display: block;padding:0px 15px 15px;font-size:11px;font-family:lucida grande,tahoma,verdana,arial,sans-serif;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;color:#999999;border:none"><div style=" width:100%; display:inline-block; text-align:right;padding-top:15px;">
                      <img border="0" src="'.ASSETS_IMG.'/c/app_store.png" style="width:100px;display: inline-block; height:auto;">
                      <img border="0" src="'.ASSETS_IMG.'/c/google_play.png" style="width:100px;display: inline-block; height:auto;"></div></td>
                    </tr>
                  </tbody>
                </table></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>
</body>';
		
					$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
					$mail->AddAddress($array['emailaddress']);
					$mail->From = "no-reply@scanflip.com";
					$mail->FromName = "ScanFlip Support";
					$mail->Subject    = "Scanflip offer - ".$RS_campaigns->fields['title'];
					//$mail->MsgHTML($body);
					$mail->MsgHTML($newbody);
					$mail->Send();

				
}

}

/**
 * @uses get all category
 * @param txt_share_frnd
 * @used in pages :my-deals.php,search-deal.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnGetAllCategories']))
{
	 $json_array = array();
	 $records = array();
	 
	 //$Sql = "SELECT * FROM categories ORDER  by `orders` ASC";
//         $Sql = "SELECT * FROM categories where active=1 ORDER  by `orders` ASC";
//	 
//	 $RS = $objDB->Conn->Execute($Sql);
	 $RS = $objDB->Conn->Execute("SELECT * FROM categories where active=? ORDER  by `orders` ASC",array(1));
	 if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	 exit();
}

/**
 * @uses get orderby category deals
 * @param order_by_cat
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnGetOrderByCatDeals']))
{
	 $json_array = array();
	 $records = array();
	 $order_by_cat=$_REQUEST['order_by_cat'];
	 $date_f = date("Y-m-d H:i:s");
         $category = $_REQUEST['categoryid'];
         if($category == 0)
         {
             $wh = "";
         }
         else
         {
             $wh= " AND c.category_id= ".$category." ";
         }
	if(isset($_REQUEST['customer_id']))
	 {
		// $date_wh = "AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','+00:00') BETWEEN CONVERT_TZ(t.start_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1)) AND CONVERT_TZ(t.expiration_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1))";
		    $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 "; 
	 	$customer_id = $_REQUEST['customer_id'];
		$Sql = "SELECT c.*,l.timezone as  loc_timezone  FROM categories CAT,campaign_location cl , campaigns c, locations l WHERE CAT.id=c.category_id and CAT.active=1 and cl.offers_left>0  and cl.location_id=  '".$order_by_cat."' ".$wh."  AND cl.campaign_id = c.id AND 
                        (cl.campaign_id IN (Select cg.campaign_id from  campaign_groups cg , merchant_groups mg where  cg.group_id=mg.id and mg.location_id=".$order_by_cat ."
                        and cg.group_id in(select ms.group_id from merchant_subscribs ms where ms.user_id=".$customer_id.")) OR cl.campaign_id IN (SELECT t.id  FROM `campaigns` t WHERE t.`level` =1 ) ) 
                  and l.id= cl.location_id ".$date_wh;
              ///  $Sql = "SELECT sl.* FROM locations sl WHERE id IN (
                //           SELECT cl.location_id FROM campaigns c,campaign_location cl WHERE (cl.num_activation_code - (select count(*) from coupon_codes where location_id=cl.campaign_id)) > 0 AND  cl.campaign_id = c.id and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")  ) and ".$Where;
	 }
	 else
	 {
		    $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 "; 
		 $Sql = "SELECT c.*,l.timezone as  loc_timezone  FROM categories CAT,campaign_location cl , campaigns c , locations l WHERE CAT.id=c.category_id and CAT.active=1 and cl.offers_left>0 and  c.level = '1' and cl.location_id=  '".$order_by_cat."' ".$wh." AND cl.campaign_id = c.id  and l.id= cl.location_id ".$date_wh;	 
                 
                 }
                 
 //for locationwise group's campaigns
				 /*
				 SELECT c.* ,l.timezone as loc_timezone FROM campaign_location cl , campaigns c, locations l WHERE 
cl.location_id= 80 AND cl.campaign_id = c.id AND
 (cl.campaign_id IN 
			(Select cg.campaign_id from  campaign_groups cg , merchant_groups mg where  cg.group_id=mg.id and mg.location_id=80 
and cg.group_id in(select ms.group_id from merchant_subscribs ms where ms.user_id=99)) OR cl.campaign_id IN
				(SELECT t.id FROM `campaigns` t WHERE t.`level` =1 ) ) 
				and l.id= cl.location_id  and cl.active=1 and cl.offers_left>0*/
				 ///
//echo $Sql;
//exit;
	 $RS = $objDB->Conn->Execute($Sql);
	 if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	 exit();
}

/**
 * @uses get location bussiness name
 * @param l_id
 * @used in pages :campaign.php, campaign_facebook.php,func_print_coupon.php, frequent-mail-by-admin.php(cron),reserve-campaign-scheduler.php(cron),location-map.php,mypoiints.php,myreviews.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getlocationbusinessname']))
{
  $sql = "select business from merchant_user where id=(select created_by from locations where id=".$_REQUEST['l_id'].")";

        $RS_bus= $objDB->Conn->Execute($sql);
        //$RS_bus= $objDB->Conn->Execute("select business from merchant_user where id=(select created_by from locations where id=".$_REQUEST['l_id'].")");
         $json_array = array();
        $busines_name = $RS_bus->fields['business'];   
         $json_array['status'] = "true";
		  $json_array['bus_name'] = $busines_name ;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
}

/**
 * @uses get deals loaction
 * @param category_id,dismile,mlatitude,mlongitude
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetDealsLocations']))
{

	 $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 $category_id=$_REQUEST['category_id'];
	 $dismile=$_REQUEST['dismile'];
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	 if(isset($_REQUEST['dismile']))
	 {
		
	 	$Where = "(69.1*(sl.latitude-($mlatitude))*69.1*(sl.latitude-($mlatitude)))+(53.0*(sl.longitude-($mlongitude))*53.0*(sl.longitude-($mlongitude)))<=".$dismile*$dismile ;	
                $Sql = "SELECT sl.* FROM locations sl WHERE ".$Where;
               // $date_wh = " AND c.start_date<='".$date_f."' AND c.expiration_date >='".$date_f."'";
				
	 }

        // $date_wh = "AND CONVERT_TZ(NOW(),'+00:00','+00:00') BETWEEN CONVERT_TZ(c.start_date,'+00:00',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) AND CONVERT_TZ(c.expiration_date,'+00:00',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1))";
          $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
        
         if($_REQUEST['firstlimit']=="" && $_REQUEST['lastlimit']=="")
         {
             $firstlimit=0;
             $lastlimit=9;
         }
         else
         {
             $firstlimit=$_REQUEST['firstlimit'];
             $lastlimit=$_REQUEST['lastlimit'];
         }
         
          $cust_where ="";
	 
	  $cust_where ="";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		$cust_where = " and (cl.campaign_id IN (Select cg.campaign_id from  campaign_groups cg , merchant_groups mg where  cg.group_id=mg.id 
                        and mg.location_id = cl.location_id and cg.group_id in(select ms.group_id from merchant_subscribs ms where ms.user_id=".$customer_id.")) OR cl.campaign_id IN (SELECT t.id  FROM `campaigns` t WHERE t.`level` =1 ) ) ";
	 }
	 else{
		 $cust_where = " and cl.campaign_id IN (SELECT t.id  FROM `campaigns` t WHERE t.`level` =1 ) ";
	 }
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$limit_data = "SELECT sl.*,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM locations sl WHERE id IN (
                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
		 }
		 else
		 {
			
                        $limit_data = "SELECT sl.*,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM locations sl WHERE id IN (
                            SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id ".$cust_where." and  cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")  AND   c.category_id = ".$_REQUEST['category_id']."  ) and ".$Where." ORDER BY distance ";
		 }
	 }
         $RS_limit_data=$objDB->Conn->Execute($limit_data);
         if($RS_limit_data->RecordCount()>0)
	 {
              $json_array['query']= $limit_data;
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS_limit_data->RecordCount();
		  $count=0;
		  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
               $json_array['query']= $limit_data;
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	
	  if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$Sql = "SELECT sl.* FROM locations sl WHERE id IN (
                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id ".$cust_where." and  cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where;
		 }
		 else
		 {
			$Sql = "SELECT sl.* FROM locations sl WHERE id IN (
                            SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id ".$cust_where."  and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")  AND   c.category_id = ".$_REQUEST['category_id']."  ) and ".$Where;
		 }
	 }
	 $RS = $objDB->Conn->Execute($Sql);
	 if($RS->RecordCount()>0)
	 {
         $records = array();
		  $json_array['all_records'] = $RS->RecordCount();
		  $json_array['marker_query']= $Sql;
		  $json_array['marker_status'] = "true";
		  $json_array['marker_total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records_all[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["marker_records"]= $records_all;
	          
	 }
	 else
	 {
		
                   $json_array['marker_query']= $Sql;
		  $json_array['marker_status'] = "false";
		  $json_array['all_records'] = 0;
		  $json = json_encode($json_array1);
		  echo $json;
		  exit();
	 }
	 
	 $json = json_encode($json_array);
	
	 echo $json;
	
	 exit();
}

/**
 * @uses get deals of category
 * @param categoryid,storeid,customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnGetDealsOfCategory']))
{
	 $json_array = array();
	 $records = array();
	 $customer_id=$_REQUEST['customer_id'];
	 $storeid=$_REQUEST['storeid'];
	 $date_f = date("Y-m-d H:i:s");
	 $category = $_REQUEST['categoryid'];
         if($category == 0)
         {
             $wh = "";
         }
         else
         {
             $wh= " AND c.category_id= ".$category." ";
         }

         if(isset($_REQUEST['customer_id']))
	 {
		// $date_wh = "AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','+00:00') BETWEEN CONVERT_TZ(t.start_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1)) AND CONVERT_TZ(t.expiration_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1))";
		  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
	 	$customer_id = $_REQUEST['customer_id'];
		$Sql = "SELECT c.* ,l.timezone as  loc_timezone FROM categories CAT,campaign_location cl , campaigns c, locations l WHERE CAT.active=1 and CAT.id=c.category_id and cl.location_id=  '".$storeid."' ".$wh."  AND cl.campaign_id = c.id AND (cl.campaign_id IN 
                    (Select cg.campaign_id from  campaign_groups cg , merchant_groups mg where  cg.group_id=mg.id and mg.location_id=".$storeid ."
                        and cg.group_id in(select ms.group_id from merchant_subscribs ms where ms.user_id=".$customer_id."))
                        OR cl.campaign_id IN (SELECT t.id  FROM `campaigns` t WHERE t.`level` =1 ) ) 
                  and l.id= cl.location_id ".$date_wh;
              ///  $Sql = "SELECT sl.* FROM locations sl WHERE id IN (
                //           SELECT cl.location_id FROM campaigns c,campaign_location cl WHERE (cl.num_activation_code - (select count(*) from coupon_codes where location_id=cl.campaign_id)) > 0 AND  cl.campaign_id = c.id and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")  ) and ".$Where;
	 }
	 else
	 {
		    $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
		 $Sql = "SELECT c.*,l.timezone as  loc_timezone  FROM categories CAT,campaign_location cl , campaigns c , locations l WHERE CAT.active=1 and CAT.id=c.category_id and c.level = '1' and cl.location_id=  '".$storeid."' ".$wh." AND cl.campaign_id = c.id  and l.id= cl.location_id ".$date_wh;	 
                 
        }
	 $RS = $objDB->Conn->Execute($Sql);
	 if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
			  if(isset($_REQUEST['customer_id']))
			  {
			  		// start to test valid deal
			  
					$array_where_camp2['campaign_id'] = $Row['id'];
					$array_where_camp2['customer_id'] = $customer_id;
					$array_where_camp2['location_id'] = $storeid;
					$RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
					
					$array_where_camp['campaign_id'] = $Row['id'];
					$array_where_camp['customer_id'] = $customer_id;
					$array_where_camp['referred_customer_id'] = 0;
					$array_where_camp['location_id'] = $storeid;
					$RS_camp = $objDB->Show("reward_user", $array_where_camp);
					
					$array_where_camp1['campaign_id'] = $Row['id'];
					$array_where_camp1['location_id'] = $storeid;
					$RS_camp1 = $objDB->Show("campaign_location", $array_where_camp1);
					
					if($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && $Row['number_of_use']==1)
					 {
					 }                       
					 elseif($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && ($Row['number_of_use']==2 || $Row['number_of_use']==3) && $RS_camp1->fields['offers_left']==0)
					 {
					 }
					 elseif($RS_cust_camp->RecordCount()==0 && $RS_camp->RecordCount()==0 && $RS_camp1->fields['offers_left']==0)
					 {        
					 }
					 else
					 {
						$records[$count] = get_field_value($Row);
						$count++;
					 }
					
					// end to test valid deal
			  }
			  else
			  {
				  	$records[$count] = get_field_value($Row);
		   			$count++;
			  }
			  
		   
		  }
		  // start to test valid deal count
		  $json_array['total_records'] = $count;
		  // end to test valid deal count
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	 exit();
}

/**
 * @uses get deals of category for location detail
 * @param customer_id,storeid
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetDealsOfCategoryForLocationDetail']))
{

	 $json_array = array();
	 $records = array();
	 $customer_id=$_REQUEST['customer_id'];
	 $storeid=$_REQUEST['storeid'];
	 $date_f = date("Y-m-d H:i:s");
         
         if(isset($_REQUEST['customer_id']))
	 {
		// $date_wh = "AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','+00:00') BETWEEN CONVERT_TZ(t.start_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1)) AND CONVERT_TZ(t.expiration_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1))";
		  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
	 	$customer_id = $_REQUEST['customer_id'];
                /*
		$Sql = "SELECT c.* ,l.timezone as  loc_timezone FROM campaign_location cl , campaigns c, locations l WHERE cl.location_id=  '".$storeid."' ".$wh."  AND cl.campaign_id = c.id AND (cl.campaign_id IN 
                    (Select cg.campaign_id from  campaign_groups cg , merchant_groups mg where  cg.group_id=mg.id and mg.location_id=".$storeid ."
                        and cg.group_id in(select ms.group_id from merchant_subscribs ms where ms.user_id=".$customer_id."))
                        OR cl.campaign_id IN (SELECT t.id  FROM `campaigns` t WHERE t.`level` =1 or t.`is_walkin`=1 ) ) 
                  and l.id= cl.location_id ".$date_wh;
                 */
                 
                // for active category 26-2-2013
                
                $Sql = "SELECT c.* ,l.timezone as  loc_timezone FROM categories CAT,campaign_location cl , campaigns c, locations l WHERE CAT.active=1 and CAT.id=c.category_id and cl.location_id=  '".$storeid."' ".$wh."  AND cl.campaign_id = c.id AND (cl.campaign_id IN 
                    (Select cg.campaign_id from  campaign_groups cg , merchant_groups mg where  cg.group_id=mg.id and mg.location_id=".$storeid ."
                        and cg.group_id in(select ms.group_id from merchant_subscribs ms where ms.user_id=".$customer_id."))
                        OR cl.campaign_id IN (SELECT t.id  FROM `campaigns` t WHERE t.`level` =1 or t.`is_walkin`=1 ) ) 
                  and l.id= cl.location_id ".$date_wh;
                 
                 
              ///  $Sql = "SELECT sl.* FROM locations sl WHERE id IN (
                //           SELECT cl.location_id FROM campaigns c,campaign_location cl WHERE (cl.num_activation_code - (select count(*) from coupon_codes where location_id=cl.campaign_id)) > 0 AND  cl.campaign_id = c.id and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")  ) and ".$Where;
	 }
	 else
	 {
		    $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
		 /*
                  $Sql = "SELECT c.*,l.timezone as  loc_timezone  FROM campaign_location cl , campaigns c , locations l WHERE c.level = '1' and cl.location_id=  '".$storeid."' ".$wh." AND cl.campaign_id = c.id  and l.id= cl.location_id ".$date_wh;	 
                  */
                    // for active category 26-2-2013
                    
                    $Sql = "SELECT c.*,l.timezone as  loc_timezone  FROM categories CAT,campaign_location cl , campaigns c , locations l WHERE CAT.active=1 and CAT.id=c.category_id and c.level = '1' and cl.location_id=  '".$storeid."' ".$wh." AND cl.campaign_id = c.id  and l.id= cl.location_id ".$date_wh;	 
                 
                 
        }
	 $RS = $objDB->Conn->Execute($Sql);
	 if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	 exit();
}
/**
 * @uses login for iphone
 * @param emailaddress,password
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnLoginforiphone'])){
	$array = $json_array = array();
	$array['emailaddress'] = $_REQUEST['emailaddress'];
	$array['password'] = md5($_REQUEST['password']);
	
	$RS = $objDB->Show("customer_user",$array);
	if($RS->RecordCount()<=0){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Email/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	if($RS->fields['active'] == 0){
		$json_array['status'] = "false";
		$json_array['message'] = "Your account is not activated";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$Row = $RS->FetchRow();
	//$_SESSION['customer_id'] = $Row['id'];
	//$_SESSION['customer_info'] = $Row;

	$array_values = $where_clause = $array = array();
	$array_values['lastvisit_date'] = date("Y-m-d H:i:s");
	$where_clause['id'] = $Row['id'];
	$objDB->Update($array_values, "customer_user", $where_clause);
	
	$user_session = session_id();
	$array['sessiontime'] = strtotime(date("Y-m-d H:i:s")); 
	$array['session_id'] = base64_encode($Row['id']); 
	$array['session_data'] = md5($array['sessiontime'].$user_session); 
	//$array['user_type'] = 1;
	$objDB->Insert($array, "user_sessions");		
	
	$json_array['status'] = "true";
	$json_array['customer_id'] = $Row['id'];
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses get customer's deals
 * @param customer_id,row_index,num_records
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetcustomerdeals'])){
		
		$customer_id = $_REQUEST['customer_id'];
		$row_index = $_REQUEST['row_index'];
		$num_records = $_REQUEST['num_records'];
		
		if($customer_id == ""){
			$json_array = array();
			$json_array['status'] = "false";
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			return $json;
		}
		
		$json_array = array();
		/*$Sql = "SELECT C.*, CAT.cat_name
				FROM campaigns C, customer_campaigns CC, categories CAT
				WHERE CC.customer_id = '$customer_id' AND CC.campaign_id = C.id AND CAT.id=C.category_id
				ORDER BY C.id DESC limit ".$row_index.",".$num_records;
		
		$RS = $objDB->Conn->Execute($Sql);*/				
		$RS = $objDB->Conn->SelectLimit("SELECT C.*, CAT.cat_name
				FROM campaigns C, customer_campaigns CC, categories CAT
				WHERE CC.customer_id =? AND CC.campaign_id = C.id AND CAT.id=C.category_id
				ORDER BY C.id DESC ",$num_records,$row_index,array($customer_id));
                
		if($RS->RecordCount()>0){
			$count=0;
			while($Row = $RS->FetchRow()){
				$records[$count] = get_field_value($Row);
				$count++;
			}
			$json_array['status'] = "true";
			$json_array["total_records"] = $RS->RecordCount();
 			$json_array["records"]= $records;
 			
		}else{
			$json_array['status'] = "false";
			$json_array['message'] = "No Deal is Found";
		}
		
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses get customer's expire deals
 * @param customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetcustomerexpiredeals'])){
		
		$customer_id = $_REQUEST['customer_id'];
		
		if($customer_id == ""){
			$json_array = array();
			$json_array['status'] = "false";
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			return $json;
		}
		$today_date = date("Y-m-d")." 00:00:00";
		/*$Sql = "SELECT C.*, CAT.cat_name
				FROM campaigns C, customer_campaigns CC, categories CAT
				WHERE CC.customer_id = '$customer_id' AND CC.campaign_id = C.id AND CAT.id=C.category_id
				AND C.expiration_date < '".$today_date."' 
				ORDER BY C.id DESC";
		
		$RS = $objDB->Conn->Execute($Sql);*/	
		$RS = $objDB->Conn->Execute("SELECT C.*, CAT.cat_name
				FROM campaigns C, customer_campaigns CC, categories CAT
				WHERE CC.customer_id = ? AND CC.campaign_id = C.id AND CAT.id=C.category_id
				AND C.expiration_date < ? 
				ORDER BY C.id DESC",array($customer_id,$today_date));					
		if($RS->RecordCount()>0){
			$count=0;
			while($Row = $RS->FetchRow()){
				$records[$count] = get_field_value($Row);
				$count++;
			}
			$json_array['status'] = "true";
			$json_array["total_records"] = $RS->RecordCount();
 			$json_array["records"]= $records;
		}else{
			$json_array['status'] = "false";
			$json_array['message'] = "No Deal is Found";
		}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses activte new deal
 * @param customer_id,activation_code
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnActivatenewdeal']))
{
		$json_array = array();
		
		$customer_id = $_REQUEST['customer_id'];
		$activation_code= $_REQUEST['activation_code'];
		if($customer_id == "")
		{
			$json_array = array();
			$json_array['status'] = "false";
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			return $json;
		}
		/*$Sql = "SELECT * FROM activation_codes WHERE activation_code='$activation_code'";
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE activation_code=?",array($activation_code));
		if($RS->RecordCount()<=0)
		{
			$json_array['status'] = "false";
			$json_array['message'] = "Please enter valid Activation Code";
			$json = json_encode($json_array);
			echo $json;
			exit();
		}
		$campaign_id = $RS->fields['campaign_id'];
		/*$Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id'";
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=?",array($customer_id,$campaign_id));
		if($RS->RecordCount()<=0){
			/*$Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
					customer_id='$customer_id', campaign_id='$campaign_id'";
			$objDB->Conn->Execute($Sql);*/
			$objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status=?, activation_code=?, activation_date= ?, coupon_generation_date=?,
					customer_id=?, campaign_id=?",array(1,$activation_code,'Now()','Now()',$customer_id,$campaign_id));

			// --- For data entry in merchant_subscribs
			$camp_array['id']=$campaign_id;		
			//$RS_campaign  = $objDB->Conn->Execute("select * from campaigns where id =".$campaign_id);
			$RS_campaign  = $objDB->Conn->Execute("select * from campaigns where id =?",array($campaign_id));
			$m_id = $RS_campaign->fields['created_by'];
				/*$Sql = "SELECT * FROM merchant_subscribs WHERE merchant_id='$m_id' AND user_id='$customer_id'";
				$RS_ms = $objDB->Conn->Execute($Sql);*/
				$RS_ms = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE merchant_id=? AND user_id=?",array($m_id,$customer_id));
				if($RS_ms->RecordCount()<=0){
					/*$Sql = "INSERT INTO merchant_subscribs SET merchant_id='$m_id', user_id='$customer_id'";
					$objDB->Conn->Execute($Sql);*/
					$objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id=?, user_id=?",array($m_id,$customer_id));
				}
			
		}
		
		$json_array['status'] = "true";
		$json_array['message'] = "Deal is Activated";
		$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses get customer's redeem deals
 * @param customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetcustomerredeemeddeals'])){
	
		
		$customer_id = $_REQUEST['customer_id'];
		
		if($customer_id == ""){
			$json_array = array();
			$json_array['status'] = "false";
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			return $json;
		}

		$json_array = array();
		/*$Sql = "SELECT C.*, CAT.cat_name , ru.reward_date redeemed_date ,ru.earned_reward
				FROM campaigns C, reward_user ru, categories CAT
				WHERE ru.customer_id = '$customer_id' AND ru.campaign_id = C.id AND CAT.id=C.category_id
				ORDER BY C.id DESC";
		
		$RS = $objDB->Conn->Execute($Sql);*/		
		$RS = $objDB->Conn->Execute("SELECT C.*, CAT.cat_name , ru.reward_date redeemed_date ,ru.earned_reward
				FROM campaigns C, reward_user ru, categories CAT
				WHERE ru.customer_id = ? AND ru.campaign_id = C.id AND CAT.id=C.category_id
				ORDER BY C.id DESC",array($customer_id));		
		if($RS->RecordCount()>0){
			$count=0;
			while($Row = $RS->FetchRow()){
				$records[$count] = get_field_value($Row);
				$count++;
			}
			$json_array['status'] = "true";
			$json_array["total_records"] = $RS->RecordCount();
 			$json_array["records"]= $records;
		}else{
			$json_array['status'] = "false";
			$json_array['message'] = "No Deal is Found";
		}
		
		$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses get customer's profile
 * @param customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetcustomerprofile'])){
		$customer_id = $_REQUEST['customer_id'];
		if($customer_id == ""){
			$json_array = array();
			$json_array['status'] = "false";
			$json_array['message'] = "Invalid Customer ID";
			$json = json_encode($json_array);
			echo $json;
			exit();
		}
		/*$Sql = "SELECT * FROM customer_user WHERE id='$customer_id'";
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("SELECT * FROM customer_user WHERE id=?",array($customer_id));
		$count=0;
		if($RS->RecordCount()>0){
			$Row = $RS->FetchRow();
			//$json_array[] = $Row;
			$records[$count] = get_field_value($Row);
			$json_array['status'] = "true";
			$json_array["total_records"] = $RS->RecordCount();
 			$json_array["records"]= $records;
		}
		else
		{
			$json_array['status'] = "false";
			$json_array["message"] = "Invalid Customer ID";
		}
		$json = json_encode($json_array);
	echo $json;
	exit();				
}

/**
 * @uses update password
 * @param customer_id
 * @used in pages :change-password .php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnUpdatepassword'])){
	$array = $json_array =array();
	$customer_id = $_REQUEST['customer_id'];
		
	if($customer_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Invalid Customer ID";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	else
	{

			if($_REQUEST['old_password'] == ""){
				$json_array['status'] = "false";
				$json_array['message'] = "Please enter Old Password";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			if($_REQUEST['new_password'] != $_REQUEST['con_new_password']){
				$json_array['status'] = "false";
				$json_array['message'] = "New Password does not Match";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			
			
			$array['password'] = md5($_REQUEST['old_password']);
			$RS = $objDB->Show("customer_user",$array);
			if($RS->RecordCount()<=0){
				$json_array['status'] = "false";
				$json_array['message'] = "Please enter Valid Old Password";
				$json = json_encode($json_array);
				echo $json;
				exit();
			}
			$array = $json_array = $where_clause = array();
			$array['password'] = md5($_REQUEST['new_password']);
			
			
			$where_clause['id'] = $customer_id;				
			
			$objDB->Update($array, "customer_user", $where_clause);
			
			$json_array['status'] = "true";
			$json_array['message'] = "Password has been changed successfully";
			$json = json_encode($json_array);
			echo $json;
			exit();
	}
}

function get_field_value($Row)
{
	$ar = $Row;
	
	$ar1 = array_unique($ar);
	for ($i = 0; $i < (count($ar)) ; $i++) {
		if(key_exists($i,$ar))
		{
			unset($ar[$i]);
		}
	    }
	
	return $ar;	
}

/**
 * @uses get campaign deatl for iphone
 * @param campaign_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetCampaignDetailforiphone']))
{
 $json_array = array();
 /*$Sql = "SELECT * FROM campaigns where id=".$_REQUEST['campaign_id'];
 $Rs = $objDB->Conn->Execute($Sql);*/
 $Rs = $objDB->Conn->Execute("SELECT * FROM campaigns where id=?",array($_REQUEST['campaign_id']));
 $count = 0;
 if($Rs->RecordCount()>0)
 {	 
	 while($Row = $Rs->FetchRow())
	 {
		$records[$count] = get_field_value($Row);
		$count++;
	 }
	 $json_array['status'] = "true";
	 $json_array["records"]= $records;
	 $json_array["total_records"] = $Rs->RecordCount();
 }
 else
 {
 	$json_array['status'] = "false";
 	$json_array["total_records"] = 0;
 }
 $json = json_encode($json_array);
 echo $json;
 exit();
}

/**
 * @uses get loaction details
 * @param location_id
 * @used in pages :location-detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_location_details']))
{
  //$campaign_id = mysql_escape_string(base64_decode($campaign_id));
 
  //$Sql= "SELECT * FROM locations WHERE id=".$_REQUEST['location_id'];
	//$RS = $objDB->Conn->Execute($Sql);

	$RS =$objDB->Conn->Execute("SELECT * FROM locations where id=?",array($_REQUEST['location_id'])); 
  if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	 exit();
}

/**
 * @uses get category name from id
 * @param cat_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnGetCategorynamefromid']))
{
	 $json_array = array();
	 $records = array();
	 
	 /*$Sql = "SELECT * FROM categories where id=".$_REQUEST['cat_id'];
	 $RS = $objDB->Conn->Execute($Sql);*/
	 $RS = $objDB->Conn->Execute("SELECT * FROM categories where id=?",array($_REQUEST['cat_id']));
	 if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	 exit();
}
/******** 
@USE : Get category detail of given category id
@PARAMETER : category id
@RETURN : category detail
@USED IN PAGES : compaigns.php , reports.php
*********/ 
if(isset($_REQUEST['getCategoryfromid']))
{
    $json_array=$records=array();
    /*$Sql="select * from categories where id=".$_REQUEST['cat_id'];
    $RS=$objDB->Conn->Execute($Sql);*/
	$RS=$objDB->Conn->Execute("select * from categories where id=?",array($_REQUEST['cat_id']));
    if($RS->RecordCount()>0)
    {
        $json_array['status']='true';
        $json_array['total_records']=$RS->RecordCount();
        $count=0;
        while($Row=$RS->FetchRow())
        {
            $records[$count] = get_field_value($Row);
            $count++;
	}
	$json_array["records"]= $records;
            
    }
    else
    {
      $json_array['status'] = "false";
      $json_array['total_records'] = 0;
      $json = json_encode($json_array);
      echo $json;
      exit();
    }
    
   $json = json_encode($json_array);
   echo $json;
   exit();
    
}

/**
 * @uses get location name from campaign
 * @param camp_id
 * @used in pages :campaign.php,campaign_facebook.php,print_coupon.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['get_location_name_of_campaigns']))
{
 /*$Sql = "Select l.* from campaign_location cl , locations l where campaign_id = ". $_REQUEST['camp_id'] ." and l.id = cl.location_id" ;
 $Rs = $objDB->execute_query($Sql);*/
$Rs = $objDB->Conn->Execute("Select l.* from campaign_location cl , locations l where campaign_id =? and l.id = cl.location_id",array($_REQUEST['camp_id']));
 $records = array();
 $count=0;
 while($Row = $Rs->FetchRow())
 {
  $records[$count] = get_field_value($Row);
  $count++;
 }
 $json_array["total_records"] = $Rs->RecordCount();
 $json_array["records"]= $records;
  $json = json_encode($json_array);
  echo $json;
  exit();
}

/**
 * @uses get all deals printed
 * @param camp_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getalldealprinted']))
{
    $ids = $_REQUEST['campaign_ids'];
    $ids = trim($_REQUEST['campaign_ids'],",");
   
    $arr = explode(",",$ids);
    
    for($i=0;$i<count($arr);$i++)
    {
        $carr= explode("_",$arr[$i]);
        $cid= $carr[1];
        $lid =$carr[3];
        $barcode = $carr[2];
        $where_clause = array();
		$where_clause['id'] = $cid;
		$RSCompDetails = $objDB->Show("campaigns", $where_clause);
                
                $where_clause_l = array();
                $where_clause_l['id'] = $lid;
                $loc_nm = $objDB->Show("locations",$where_clause_l);
                
               
                
                $title = $RSCompDetails->fields['title'];
                $expdate = $RSCompDetails->fields['expiration_date'];
                $discont = $RSCompDetails->fields['discount'];
                $desc = $RSCompDetails->fields['description'];
                $rpoint = $RSCompDetails->fields['redeem_rewards'];
                $timzone = $RSCompDetails->fields['timezone'];
                
               
        ?>
        
       <div id="coupon_div">
	<div style="border: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px; border-bottom: none;" class="c_div">
            <div style=" padding: 10px; font-weight: bold; font-size: 18px; color: rgb(255, 255, 255); background: none repeat scroll 0px 0px rgb(35, 30, 30);height:15px" class="c_title">
                        <?=$title;?><span style="float: right; font-weight: normal; font-size: 15px;" class="expr_div">Expiration Date :- <?=$expdate;?></span>
            </div>
            <div style="text-align: center; float: left; border-right: 1px dashed; width: 380px;" class="dis_title">
                <span style="display: block; font-weight: bold; margin: 10px 0 0 0; font-size: 25px;color: orange;"><?=$discont;?></span>
            <div style="margin: 10px 0 0 0;"><img src="<?php echo WEB_PATH; ?>/showbarcode.php?br=<?php echo $barcode; ?>" alt="barcode" /></div><br/><br/>
	
            </div> <div style="float: left; margin: 10px 0px 0px; padding: 10px; width: 417px;overflow: hidden;height: 180px" class="right_coupon_div">
                <div style=" margin: 10px 0 0 0; text-align: justify; height: auto;" class="coupon_desc"><?=$descs;?></div>
                       
            </div>
        </div>
                    <?php 
                    $str ="";
                    if($RSCompDetails->fields['number_of_use']==1){ 
                     $str = "* This coupon is limit for <b>one per customer</b> use." ;
                    }
                    elseif($RSCompDetails->fields['number_of_use']==2)
                    {
                       $str = "* This coupon is limit for <b>one per customer per day</b> use. ";
                    } 
                    
   $where_clause = array();
            $where_clause['campaign_id'] =$cid;
            $RSComp = $objDB->Show("campaign_location", $where_clause);
            $t_records = $RSComp->RecordCount();
 if($t_records > 1 || $str!= "")
 { ?>
     <div class="c_middle_div" style="border-left: 1px solid rgb(0, 0, 0);  border-right: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px; border-top:1px solid #ccc;border-bottom: none;">
     <?php                  
 }
     
  if($t_records > 1){
                    ?>
        <div class="participating_location"> You can find this deal available on following location also.
            <?php 
             while($Row = $RSComp->FetchRow()){
               {
                 $where_clause = array();
						$where_clause['id'] = $Row['location_id'];
						$RSLocation = $objDB->Show("locations", $where_clause);
             
                 if($Row['location_id']!= $_REQUEST['l_id']){
                     $address = $RSLocation->fields['address'].", ".$RSLocation->fields['city'].", ".$RSLocation->fields['state'].", ".$RSLocation->fields['zip'].", ".$RSLocation->fields['country'];
                     ?>
            <p style="margin-bottom: 8px;margin-top: 8px;  padding: 0px !important; padding-left: 20px !important;"><?=$RSLocation->fields['location_name']?>&nbsp;<span>(<?=$address?>)</span></p>
        <?php
                 }
               }
             }
            ?>
        </div>   
        <?php }
  
        if($t_records > 1 || $str!= "")
 {
        echo "<div class='coupon_use' style=' float: right;    font-size: 11px;'>";
                    echo $str;
                    echo "</div>";
 }
                    if($t_records > 1 || $str!= "")
 { ?>
     </div>
     <?php                  
 } ?>
   
	
            <div class="c_last_div" style=" border: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px;text-align:right;" >
                Store :- <span style="font-weight: bold;"><?=$loc_nm->fields['location_name'];?></span> &nbsp;&nbsp;|&nbsp;&nbsp; 
                Redemption point :- <span style="font-weight: bold;"><?=$rpoint;?></span>&nbsp;&nbsp;|&nbsp;&nbsp; 
                <span style="font-weight: bold;">Scanflip merchant</span>
            </div>
    </div>
<div style="height: 30px;">&nbsp;</div>
    <?php }
}


//20-12-2012
/**
 * @uses subscribe loaction
 * @param camp_id
 * @used in pages :deal-script.js,search-deal-script.js,my-deal.php,mymerchants.php,search-deal.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnunsubscribelocation']))
{
   /*** check customer session  ***/
   if(!isset($_SESSION['customer_id'])){

		$json_array = array();
		if(isset($_REQUEST['reload']))
		{
			$url_perameter_2 = "?purl=".urlencode("@".$_REQUEST['campaign_id']."|".$_REQUEST['location_id']."@");

		}
		else
		{
			$url_perameter_2 ="";
		}
		$json_array['loginstatus'] = "false";
		if(isset($_REQUEST['r_url']))
		{
			if(isset($_REQUEST['is_location']))
			{

				$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".WEB_PATH."/search-deal.php".$url_perameter_2;
			}
			else
			{
				$json_array['link'] = WEB_PATH."/register.php?url=".WEB_PATH."/search-deal.php".$url_perameter_2;
			}

		}
		else{
			if(isset($_REQUEST['is_location']))
			{

				$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
			}
			else
			{
				$json_array['link'] = WEB_PATH."/register.php?url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
			}
		}
		$json_array['is_profileset'] = 1;
		$json = json_encode($json_array);
		
		echo $json;
		exit();
}
/****** check customer session ********/

	$storearray = $json_array = array();
	$json_array['loginstatus'] = "true";
	$location_id= base64_decode($_REQUEST['location_id']);
	
	if($_REQUEST['customer_id'] == ""){
		$customer_id = $_SESSION['customer_id'];

		
	}else
	{
		$customer_id = get_cutomer_session_id($_REQUEST['customer_id']);

	}

	if($customer_id == "")
	{

		$json_array['message'] = "Invalid Customer ID";
	}
	else
	{	

		
		$storearray['location_id']=$location_id;		
		$RSstoe = $objDB->Show("campaign_location",$storearray);
	    if($RSstoe->RecordCount()<=0)
		{
            
			$json_array['status'] = "false";
                        $json_array['loginstatus'] = "false";
			$json_array['message'] = "There is no campaign for this store";
			$json = json_encode($json_array);
			echo $json;
                        if(isset($_REQUEST['page']))
                        {
						/*$redirect_query_location = "select location_permalink from locations where id=".$location_id;
						 $RS_redirect_query = $objDB->Conn->Execute($redirect_query_location);*/
						 $RS_redirect_query = $objDB->Conn->Execute("select location_permalink from locations where id=?",array($location_id));			
						 $location_url = $RS_redirect_query->fields['location_permalink'];
                            header("Location:".$location_url);
                            //header("Location: location_detail.php?id=".$location_id);
                            exit();
                        }
                        else 
                        {
                            header("Location: search-deal.php");
                            exit();
                        }
		}
		else
		{
                // unsubscribe from subscribed_store table
                     /*$sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$location_id;
                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
			$subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=?",array($_SESSION['customer_id'],$location_id));
                        if($subscibed_store_rs->RecordCount()==0)
                        {
                        }
                        else 
						{
                            if($subscibed_store_rs->fields['subscribed_status']==1)
                            {
                                /*$up_subscribed_store = "Update subscribed_stores set subscribed_status=0 where  customer_id= ".$_SESSION['customer_id']." and location_id=".$location_id;
                                $objDB->Conn->Execute($up_subscribed_store);*/
				$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=? where  customer_id=? and location_id=?",array(0,$_SESSION['customer_id'],$location_id));
								 
								// start unsubscribe location then unreserve private deal 2-8-2013  
								//$cust_private_camp_for_location_query = "select campaign_id from customer_campaigns cc,campaigns c where cc.campaign_id=c.id and c.level=0 and location_id=".$location_id." and customer_id=".$_SESSION['customer_id'];
								
								//start 13-8-2013 because dist list deal also unreserve with above query as c.level=0
								/*$cust_private_camp_for_location_query = "select cc.campaign_id from customer_campaigns cc,campaigns c,campaign_location cl where cc.campaign_id=c.id and c.level=0 and cc.location_id=".$location_id." and customer_id=".$_SESSION['customer_id']." and cc.location_id=cl.location_id and cc.campaign_id=cl.campaign_id and cl.campaign_type=2";
								//end 13-8-2013
								$RS_cust_private_camp_for_location =$objDB->Conn->Execute($cust_private_camp_for_location_query);*/
								$RS_cust_private_camp_for_location =$objDB->Conn->Execute("select cc.campaign_id from customer_campaigns cc,campaigns c,campaign_location cl where cc.campaign_id=c.id and c.level=0 and cc.location_id=? and customer_id=? and cc.location_id=cl.location_id and cc.campaign_id=cl.campaign_id and cl.campaign_type=2",array($location_id,$_SESSION['customer_id']));
								
								while($Row = $RS_cust_private_camp_for_location->FetchRow())
								{
									/*$sql = "select * from coupon_codes where customer_id=".$_SESSION['customer_id']." and customer_campaign_code=".$Row['campaign_id']." and location_id=".$location_id ;
									$RS_cc1 =$objDB->Conn->Execute($sql);*/
									$RS_cc1 =$objDB->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?" ,array($_SESSION['customer_id'],$Row['campaign_id'],$location_id));
									
									/*$sql = "Select * from coupon_redeem where coupon_id in (".$RS_cc1->fields['id'].")";
									$RS_c =$objDB->Conn->Execute($sql);*/
									$RS_c =$objDB->Conn->Execute("Select * from coupon_redeem where coupon_id in (?)",array($RS_cc1->fields['id']));
									
									if($RS_c->RecordCount() == 0)
									{									
										/*$sql = "select * from customer_campaigns where customer_id=".$_SESSION['customer_id']." and campaign_id=".$Row['campaign_id']." and location_id=".$location_id ;
										$RS_cc =$objDB->Conn->Execute($sql);*/
										$RS_cc =$objDB->Conn->Execute("select * from customer_campaigns where customer_id=? and campaign_id=? and location_id=?",array($_SESSION['customer_id'],$Row['campaign_id'],$location_id));
										if($RS_cc->RecordCount()>0)
										{			
											/*$Sql = "DELETE FROM customer_campaigns where customer_id=".$_SESSION['customer_id']." and campaign_id=".$Row['campaign_id']." and location_id=".$location_id ;
											$objDB->Conn->Execute($Sql);*/
					$objDBWrt->Conn->Execute("DELETE FROM customer_campaigns where customer_id=? and campaign_id=? and location_id=?" ,array($_SESSION['customer_id'],$Row['campaign_id'],$location_id)); 													
										}
										
										// Remove coupon codes //
										if($RS_cc1->RecordCount()>0)
										{
										//	$Sql = "UPDATE coupon_codes SET active=0 where customer_id=".$_SESSION['customer_id']." and customer_campaign_code=".$Row['campaign_id']." and location_id=".$location_id ;
											/*$Sql = "DELETE FROM coupon_codes where customer_id=".$_SESSION['customer_id']." and customer_campaign_code=".$Row['campaign_id']." and location_id=".$location_id ;
											$objDB->Conn->Execute($Sql);*/
											$objDBWrt->Conn->Execute("DELETE FROM coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?" ,array($_SESSION['customer_id'],$Row['campaign_id'],$location_id));
											
											/*$Sql = "UPDATE campaign_location SET offers_left=offers_left+1,used_offers=used_offers-1 where campaign_id=".$Row['campaign_id']." and location_id=".$location_id ;
											$objDB->Conn->Execute($Sql); */   
											$objDBWrt->Conn->Execute("UPDATE campaign_location SET offers_left=offers_left+1,used_offers=used_offers-1 where campaign_id=? and location_id=?",array($Row['campaign_id'],$location_id));       
										}
										// remove coupon codes //
									}
									else
									{
                                        /*$Sql = "UPDATE coupon_codes SET active=0 where customer_id=".$_SESSION['customer_id']." and customer_campaign_code=".$Row['campaign_id']." and location_id=".$location_id ;
										$objDB->Conn->Execute($Sql); */
										$objDBWrt->Conn->Execute("UPDATE coupon_codes SET active=? where customer_id=? and customer_campaign_code=? and location_id=?" ,array(0,$_SESSION['customer_id'],$Row['campaign_id'],$location_id)); 
										/*$Sql = "UPDATE customer_campaigns SET activation_status=0 where customer_id=".$_SESSION['customer_id']." and campaign_id=".$Row['campaign_id']." and location_id=".$location_id ;
										$objDB->Conn->Execute($Sql); */
										$objDBWrt->Conn->Execute("UPDATE customer_campaigns SET activation_status=? where customer_id=? and campaign_id=? and location_id=?" ,array(0,$_SESSION['customer_id'],$Row['campaign_id'],$location_id)); 
                                    }
								}
								// start unsubscribe location then unreserve private deal 2-8-2013 		
                            }
                        }
                // unsubscribe from subscribed_store table
             // for remove merchant subscribe entry //
              
            $json_array['status'] = "true";
            $json_array['loginstatus'] = "true";
		$json = json_encode($json_array);
		echo $json;
		exit();
		}	
		
	}
	
	    $json_array['status'] = "false";
            $json_array['loginstatus'] = "false";
		$json = json_encode($json_array);
		echo $json;
                if(isset($_REQUEST['page']))
                {
				/*$redirect_query_location = "select location_permalink from locations where id=".$location_id;
						 $RS_redirect_query = $objDB->Conn->Execute($redirect_query_location);*/
						$RS_redirect_query = $objDB->Conn->Execute("select location_permalink from locations where id=?",array($location_id));			
						 $location_url = $RS_redirect_query->fields['location_permalink'];
                            header("Location:".$location_url);
                    //header("Location: location_detail.php?id=".$location_id);
                    exit();
                }
                else 
                {
                  //  header("Location: search-deal.php");
                    exit();
                }
        
}
//28-12-2012 get my deals according to category , month ,year //
/**
 * @uses  get my deals according to category , month ,year
 * @param category
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_filter_my_deals']))
 {
    
        $cat_wh = "";
        if($_REQUEST['category']!=0)
        {
            $cat_wh = "and C.category_id=".$_REQUEST['category'];
        }
        else
        {
           $cat_wh=" and CAT.active=1";
        }
        $dt_w = "";
	
   
	$mlatitude=$_SESSION['mycurrent_lati'];
	$mlongitude=$_SESSION['mycurrent_long'];
	if(isset($_COOKIE['miles_cookie']))
	{
		$miles = $_COOKIE['miles_cookie'];
	}
	else
	{
		$miles = 50;
	}
	if(isset($_COOKIE['cat_remember']))
	{
		$category_id  = $_COOKIE['cat_remember'];
	}
	else
	{
		$category_id  = 0;
	}
	 $cat_wh = "";
	if( $category_id !=0)
	{
		$cat_wh = " and C.category_id=". $category_id ;
	}
	else
	{
		$cat_wh=" and CAT.active=1";
	}
	 if(isset($_COOKIE['zoominglevel']))
	{
		 $zooming_level = $_COOKIE['zoominglevel'];
	}
	else
	{
		 $zooming_level = 20;
	}
   if($_COOKIE['searched_location'] == "")
   {
	$curr_latitude = $_SESSION['customer_info']['curr_latitude'];
	$curr_longitude = $_SESSION['customer_info']['curr_longitude'];
   }
   else{
		 $curr_latitude = $_COOKIE['mycurrent_lati'];
		$curr_longitude = $_COOKIE['mycurrent_long'];
   }
	 $Where_miles = "";
	if($curr_longitude != "")
	{
		$Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE ( 69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
	}
	else
	{
		
		$curr_latitude = $_COOKIE['mycurrent_lati'];
		$curr_longitude = $_COOKIE['mycurrent_long'];
		$Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE (69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
	}
	 if($mlatitude!="" && $mlongitude!="")
	 {
		 /*$Sql = "SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,
		         (((acos(sin((".$mlatitude."*pi()/180)) * 
				sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
				cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
				pi()/180))))*180/pi())*60*1.1515
			    ) as distance
				FROM campaigns C, customer_campaigns CC, categories CAT,locations l,campaign_location cl
				WHERE  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and cl.active=1 and l.id=cl.location_id and cl.campaign_id=C.id 
				and cl.location_id in
 (SELECT DISTINCT ss.location_id from subscribed_stores ss where CAT.active=1 and ss.customer_id=".$_SESSION['customer_id']." and ss.subscribed_status=1 ".$Where_miles." ) and l.active=1 and CC.customer_id = '".$_SESSION['customer_id']."' AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id   ".
				$dt_w.$cat_wh." ORDER BY distance";*/

		$RS = $objDB->Conn->Execute("SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,
		         (((acos(sin((".$mlatitude."*pi()/180)) * 
				sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
				cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
				pi()/180))))*180/pi())*60*1.1515
			    ) as distance
				FROM campaigns C, customer_campaigns CC, categories CAT,locations l,campaign_location cl
				WHERE  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and cl.active=1 and l.id=cl.location_id and cl.campaign_id=C.id 
				and cl.location_id in
 (SELECT DISTINCT ss.location_id from subscribed_stores ss where CAT.active=1 and ss.customer_id=? and ss.subscribed_status=? ".$Where_miles." ) and l.active=1 and CC.customer_id = ? AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id   ".
				$dt_w.$cat_wh." ORDER BY distance",array($_SESSION['customer_id'],1,$_SESSION['customer_id']));		
	 }
	 else
	 {
	     /*$Sql = "SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude
				FROM campaigns C, customer_campaigns CC, categories CAT,locations l,campaign_location cl
				WHERE  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and cl.active=1 and l.id=cl.location_id and cl.campaign_id=C.id and l.active=1 and CC.customer_id = '".$_SESSION['customer_id']."' AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id   ".
				$dt_w.$cat_wh." ORDER BY C.created_date DESC";*/	

		$RS = $objDB->Conn->Execute("SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude
				FROM campaigns C, customer_campaigns CC, categories CAT,locations l,campaign_location cl
				WHERE  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and cl.active=1 and l.id=cl.location_id and cl.campaign_id=C.id and l.active=1 and CC.customer_id = ? AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id   ".
				$dt_w.$cat_wh." ORDER BY C.created_date DESC",array($_SESSION['customer_id']));
	 }
		?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0" style="padding:5px;float:left" id="example">
		<thead>
	 
	     </thead>
<tbody>
<?php
			while($Row = $RS->FetchRow()){
                           
                            ?>
	<?php 
         $rowstatus="";
        
			$i =$Row['timezone']; // "-11:30,0";
			$indec = substr($i,0,1);
			$last = substr($i,1,strpos($i,","));
			$hmarr = explode(":",$last);
			$hoffset = $hmarr[0]*60*60;
			$moffset = $hmarr[1]*60;
			
			  $offset=$hoffset + $moffset;
			  $dateFormat="Y-m-d H:i:s";
                          if($indec == "+")
              {
                    $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))+$offset);
              }
              else
              {
                    $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))-$offset);
              }
			
			  if($indec == "+")
                          {
//				$timeNdate= date($dateFormat, strtotime($Row['expiration_date'])+$offset);
//                                $timeNdate1= date($dateFormat, strtotime($Row['start_date'])+$offset);
                              $timeNdate=$Row['expiration_date'];
                               $timeNdate1=$Row['start_date'];
                          }
			  else
                          {
//				$timeNdate= date($dateFormat, strtotime($Row['expiration_date'])-$offset);
//                                $timeNdate1= date($dateFormat, strtotime($Row['start_date'])-$offset);
                               $timeNdate=$Row['expiration_date'];
                               $timeNdate1=$Row['start_date'];
                          }
              
				$today_date =  $timeNdate1_; //date("Y-m-d H:i:s");
				if($timeNdate < $today_date)
                                {
                                     $rowstatus="expire";
				
				}
				else
				{
                                   $rowstatus="active";  
                                  
				}
					
		?>
                <?php
                
           $array_where_camp['campaign_id'] = $Row['id'];
         $array_where_camp['customer_id'] = $_SESSION['customer_id'];
         $array_where_camp['referred_customer_id'] = 0;
         $array_where_camp['location_id'] =$Row['location_id'] ;
         $RS_camp = $objDB->Show("reward_user", $array_where_camp);
         
          $where_x1 = array();
		$where_x1['campaign_id'] = $Row['id'];
		$where_x1['location_id'] = $Row['location_id'];
                $campLoc = $objDB->Show("campaign_location", $where_x1);
            
         if($RS_camp->RecordCount()>0 && $Row['number_of_use']==1)
         {
         }
         elseif ($RS_camp->RecordCount()>0 && ($Row['number_of_use']==2 || $Row['number_of_use']==3 ) && $campLoc->fields['offers_left']==0) 
         {
         
         }
         else
         {          
               
        ?>     
	  <tr class="tableDeal" style="height:200px;">
              
       <td class="businesslogo">
	     <?php
	     if($Row['business_logo']!="")
		{
		    $img_src=ASSETS_IMG."/m/campaign/".$Row['business_logo']; 
		}
		else 
		{
		    $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
		}
	     //$img_src=WEB_PATH."/merchant/images/logo/".$Row->business_logo;
	     ?>
	     <img src="<?php echo $img_src;?>" />
	     
	</td>
        
        <?php 
        
			$i =$Row['timezone']; // "-11:30,0";
			$indec = substr($i,0,1);
			$last = substr($i,1,strpos($i,","));
			$hmarr = explode(":",$last);
			$hoffset = $hmarr[0]*60*60;
			$moffset = $hmarr[1]*60;
			
			  $offset=$hoffset + $moffset;
			  $dateFormat="Y-m-d H:i:s";
			  if($indec == "+")
                          {
				$timeNdate= date($dateFormat, strtotime($Row['expiration_date'])+$offset);
                                $timeNdate1= date($dateFormat, strtotime($Row['start_date'])+$offset);
                          }
			  else
                          {
				$timeNdate= date($dateFormat, strtotime($Row['expiration_date'])-$offset);
                                $timeNdate1= date($dateFormat, strtotime($Row['start_date'])-$offset);
                          }
     $hmarr = explode(":",CURR_TIMEZONE);
            $hoffset = $hmarr[0]*60*60;
            $moffset = $hmarr[1]*60;

              $offset=$hoffset + $moffset;
              $dateFormat="Y-m-d H:i:s";
              if($indec == "+")
              {
                    $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))+$offset);
              }
              else
              {
                    $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))-$offset);
              }
				$today_date =  $timeNdate1_; //date("Y-m-d H:i:s");
				
                                    // -- For barcode
                $where_x = array();
		$where_x['campaign_id'] = $Row['id'];
		$CodeDetails = $objDB->Show("activation_codes", $where_x);

		$barcodea = $_SESSION['customer_id'].substr($CodeDetails->fields['activation_code'],0,2).$Row['id'].substr($Row['location_name'],0,2).$Row['location_id'];
                                
					
		?>
	<td align="left" class="locationdata" style="line-height:1.5;">
            <div class="gridloctitle">
                           <?php
                           if($Row['location_name']!="")
                                {
                                    echo $Row['location_name'];
                                } 
                                else
                                {
                                // for business name 
                                                        $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='. $Row['location_id']);
                                                                        if(trim($arr[0]) == "")
                                                                             {
                                                                                     unset($arr[0]);
                                                                                     $arr = array_values($arr);
                                                                             }
                                                                             $json = json_decode($arr[0]);
                                                                        $busines_name  = $json->bus_name;
                                                                        echo $busines_name;
                                                        // for business name 
                                }
                           ?>
            </div>
            <div class="griddealtitle">
                <a href="<?=WEB_PATH?>/campaign.php?campaign_id=<?php echo $Row['id']; ?>&l_id=<?php echo $Row['location_id']; ?>" >
                <?php echo $Row['title']; ?>
                    </a>
            </div>
            <div class="griddealtype">
                                                
                                          <?php
                                                if($Row['number_of_use']==1)
                                                {
                                                    if($Row['new_customer']==1)
                                                    {
                                                        echo "Limit : One Per Customer,Valid For New Customer Only";
                                                    }
                                                    else 
                                                    {
                                                        echo "Limit : One Per Customer";
                                                    }
                                                }
                                                elseif($Row['number_of_use']==2)
                                                    echo "Limit : One Per Customer Per Day";
                                                elseif($Row['number_of_use']==3)
                                                    echo "Limit : Earn Redemption Points On Every Visit";                
                                          ?>

              </div>
            <div class="griddealpoint">
                                                <div class="redeem_point">
                                              <p><?php echo $Row['redeem_rewards']; ?></p>
					       <span> Redeem Point</span>
                                            </div>
                                            <div class="referral_point">
						<p><?php echo $Row['referral_rewards']; ?></p>
						<span>Share Point</span>
                                            </div>                 
                                          

            </div>
	    
	     <div class="Share" >Share the deal and Earn more Rewards
                              <? if($_SESSION['customer_id'] != ""){ ?>
                              <a href="javascript:void(0)" id="btnsharediv_<?php echo $Row['id']."-".$Row['location_id'];?>" >Go For It >></a> 
                              
                              <!--<input type="button" value="Go for it" id="btn_share_div" />-->
                              <? } else {?>
                              <a  href="<?php echo WEB_PATH."/register.php?url=".curPageURL();?>" id="btn_share_div<?php echo $Row->id;?>" >Go For It >></a> 
                              <!--<input type="submit" value="Go for it" id="btn_share_div" name="chk_for_share_campaign" />-->
                              <?php } ?>
                            </div>
	     <div class="sharing_icon_css">
                            <div class="p1">
                              <? if($_SESSION['customer_id'] != ""){ ?>
                              <a href="javascript:void(0)" id="btn_msg_div_<?php echo $Row->id."-".$Row->location_id;?>" ><img src="<?php echo ASSETS_IMG.'/c/msg.png'?>" name="msg_share" /></a>
                              <? } else {?>
                              <a  href="<?php echo WEB_PATH."/register.php?url=".curPageURL();?>" id="btn_msg_div" ><img src="<?php echo ASSETS_IMG.'/c/msg.png'?>" name="msg_share" /></a>
                              <?php } ?>
                            </div>
			    <?php
			        if($Row->business_logo!="")
				{
				   // $img_src=WEB_PATH."/merchant/images/logo/".$RS[0]->business_logo; 
				    $fb_img_share = ASSETS_IMG."/m/campaign/".$Row->business_logo;
				}
				else 
				{
				    $fb_img_share =ASSETS_IMG."/c/Merchant_Offer.png";
				}
				$share="true";
				$th_link = WEB_PATH."/register.php?campaign_id=".$Row->id."&l_id=".$Row->location_id."&share=$share";
			if($_SESSION['customer_id'] != ""){
				$th_link .= "&customer_id=".base64_encode($_SESSION['customer_id']);                                 
			}
			
			   
			  
					$title=urlencode($Row->title);
					
					$url=urlencode($th_link);
					$summary=urlencode($Row->deal_detail_description);
					$image=$fb_img_share;
					
					//$image="http://ia.media-imdb.com/images/M/MV5BMjEwOTA2MjMwMl5BMl5BanBnXkFtZTcwODc3MDgxOA@@._V1._SY314_CR3,0,214,314_.jpg";
					
			?>
			<div class="p2">
			    <a onClick="window.open('https://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&p[images][0]=<?php echo $image;?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');" target="_parent" href="javascript: void(0)">
				<img src="<?php echo ASSETS_IMG.'/c/fb.png'?>" name="fbshare" />
			    </a>
			    </div>
			<div class="p3">
			          <iframe allowtransparency="true" frameborder="0" scrolling="no" data-url="<?php echo $th_link;?>"  src="https://platform.twitter.com/widgets/tweet_button.html?count=none&url=<?php echo urlencode($th_link);?>"
            style="width:55px; height:20px;"></iframe>
				      
                        </div>


			<div class="p4">
                              
				<div class="g-plusone" data-size="medium" data-annotation="none" data-href="<?php echo $th_link;?>"></div>
					
					<!-- Place this tag after the last +1 button tag. -->
					<script type="text/javascript">
					  (function() {
					    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					    po.src = 'https://apis.google.com/js/plusone.js';
					    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>	
                            </div>
			
			    
			     
		
			    
	     </div>
	     
            <script>
        $("#btnsharediv_<?php echo $Row['id']."-".$Row['location_id'];?>").click(function(){
	         // alert("<?php echo $Row['id']."-".$Row['location_id'];?>");
		  open_popup1('Notification<?php echo $Row['id'].$Row['location_id'];?>');

	});    
        </script>
            
            
                       </td>
<!--		<td align="left"><?=$Row['cat_name']?></td>-->
                 <?php
		       $from_lati=$_SESSION['mycurrent_lati'];
                
			 $from_long=$_SESSION['mycurrent_long'];
			
			$to_lati=$Row['latitude'];
			
			$to_long=$Row['longitude'];
			
		       ?>
		       <td align="left" class="distance">
                           <div class="miles">
                            <div class="expir_tooltip">
                                               
                                                </div>
                               <span style="display:none;">
                                                    <?php 
                                                    echo date("Y-m-d g:i:s A", strtotime($Row['expiration_date']));
                                                    ?>
                                                </span>
						<?php 
                            if($from_lati!="" && from_long!="")
                                echo $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M")." Miles";
                            else
                                echo "-";
                        ?>
                           </div>
                         <div class="gridlocaddr">
                             <?php
                                                    if($from_lati!="" && from_long!="")
                                                    {
                                                        $maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;                                                   
                                                    ?>
                                                        <div class="div_getdirection">
                                                            <a href="<?php echo $maphref; ?>" target="_blank" style="color:#0066FF;">Get Direction</a>
                                                        </div>
                                                    <?php
                                                    
                                                    }
                                                ?>
                            <?php $address = $Row['address'].", ".$Row['city'].", ".$Row['state'];
                                                            echo $address;	
                                                    ?>
                        </div>
            
                       </td>

        <td class="sorting_last">
                  <?php 
                   $where_x = array();
		$where_x['campaign_id'] = $Row['id'];
		$CodeDetails = $objDB->Show("activation_codes", $where_x);

		$barcodea = $_SESSION['customer_id'].substr($CodeDetails->fields['activation_code'],0,2).$Row['id'].substr($Row['location_name'],0,2).$Row['location_id'];
                  ?>
                  <input type="checkbox" name="chk_<?=$Row['id']?>" id="chk_<?=$Row['id']?>_<?php echo $barcodea;?>_<?php echo $Row['location_id'] ?>" is_printable="1" print_error_msg="" >
              </td>
		</tr>
	    <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>PopUpContainer" class="container_popup"  style="display: none;">
 <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>BackDiv" class="divBack"></div>
    <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>FrontDivProcessing" class="Processing" style="display:none;">                                            
            <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>MaindivLoading" align="center" valign="middle" class="imgDivLoading" style="left:32%;top: 11%;">
                <div class="modal-close-button" style="visibility: visible;"><a tabindex="0" onclick="close_popup('Notification<?php echo $Row['id'].$Row['location_id'];?>');" id="fancybox-close" style="display:inline;"></a></div>
                <div id="Notification<?php echo $Row->id.$Row->location_id;?>mainContainer" class="innerContainer" style="height:366px;width:448px;background:none;">
                    <div class="main_content">
                        <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                            <div class="campaign_detail_div" style="">
                                
                                    <div id="activatesahreDiv" class="div_share_friend" style="background-image:none;border:2px solid #FF8810;height:375px;text-align:left">
                                  
                                    <div class="head">
					<?php $img_src=ASSETS_IMG."/c/header-logo_new.png";?>
                                        <img src="<?php echo $img_src;?>" alt="Scanflip"/><br/>
                                        Powering Smart Saving from local Merchants
                                    </div>
                                    <div class="share_friends">Share With Friends</div>
                                    <div class="share_msg">To Share this offer with friends,complete the from below.</div>
                                    <div class="message_pop">
                                        <strong>Message :</strong><br/>
                                        <strong>I thought</strong> you might enjoy knowing this deals from Scanflip
                                    </div>        
                                   
                                    <div class="notice_mail">Specify up to 50 of your friend's email below separated by commas (,) or semicolons (;)</div>
                                      <p style="color:#FF0000;" id="<?php echo $Row['id'].$Row['location_id'];?>"></p>
                                    <div class="text_area"><textarea rows="5" cols="50"  name="txt_share_frnd" id="txt_share_frnd<?php echo $Row['id'].$Row['location_id'];?>"></textarea></div>
                                   
                                    <div class="buttons_bot">
                                       
                                        <input type="hidden" name="reffer_campaign_id1" id="reffer_campaign_id" value="<?=$_REQUEST['campaign_id']?>" />
                                        <input type="hidden" name="reffer_campaign_name1" id="reffer_campaign_name" value="<?php echo $camp_title; ?>" />
                                        <input type="hidden" name="refferal_location_id1" value="<?php echo $_REQUEST['l_id']; ?>" id="refferal_location_id" />
                                        <input type="button" class="btnsharegridbutton" value="Share" name="btn_share_grid" id="btnsharegridbutton<?php echo $Row['id']."-".$Row['location_id']; ?>" />
                                        <input type="button" value="Cancel" onclick="close_popup('Notification<?php echo $Row['id'].$Row['location_id'];?>');" id="btn_cancel"  />
					<?php $img_src=ASSETS_IMG."/c/ajax-offer-loader.gif";?>
					<img src="<?php echo $img_src; ?>" alt="" id="shareloading<?php echo $Row['id'].$Row['location_id'];?>" height="20px" width="20px" style="display:none"/>
                                    </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
  </div>
  <script>

 $("#btnsharegridbutton<?php echo $Row['id']."-".$Row['location_id']; ?>").click(function(){
		
		var email_str = $("#txt_share_frnd<?php echo $Row['id'].$Row['location_id'];?>").val();
              	 var msg ="";
            
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
		        $("#shareloading<?php echo $Row['id'].$Row['location_id'];?>").show();
		       $.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/process.php",
                                      data: "btn_share_grid=yes&reffer_campaign_id=" + <?php echo $Row['id'];?> +'&refferal_location_id='+<?php echo $Row['location_id']; ?> +'&txt_share_frnd='+email_str,
                                      async : false,
                                      success: function(msg) {
                                        
                                          close_popup('Notification<?php echo $Row['id'].$Row['location_id'];?>');  
                                             $("#shareloading<?php echo $Row['id'].$Row['location_id'];?>").hide();  
                                      }
                        });
		       
			return true;
		}
		else {
			$("#<?php echo $Row['id'].$Row['location_id'];?>").text(msg)
			 
			return false;
		}
		
                
	});
</script>
	  
	  <?
         }	
			}
			
			
	  	
	 
	  ?>
	  </tbody>
</table>


            <div style="clear: both"></div>
	<!--end of content-->
<!--end of contentContainer-->
<div id="main_parent_print_div" style="display:none">
    <div id="print_coupon_div" >
    <?php
/*$Sql = "SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip
				FROM campaigns C, customer_campaigns CC, categories CAT,locations l
				WHERE l.active=1 and CC.customer_id = '".$_SESSION['customer_id']."' AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id ".
				$dt_w.$cat_wh." ORDER BY C.created_date DESC";
		
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("SELECT C.*, CAT.cat_name ,l.timezone as loc_timezone ,l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip
				FROM campaigns C, customer_campaigns CC, categories CAT,locations l
				WHERE l.active=1 and CC.customer_id = ? AND CC.campaign_id = C.id AND CAT.id=C.category_id AND l.id= CC.location_id ".
				$dt_w.$cat_wh." ORDER BY C.created_date DESC",array($_SESSION['customer_id']));
if($RS->RecordCount()>0) {
		while($Row = $RS->FetchRow()){?>
    <?php
   
      $where_x = array();
		$where_x['campaign_id'] = $Row['id'];
		$CodeDetails = $objDB->Show("activation_codes", $where_x);

    $barcodea = $_SESSION['customer_id'].substr($CodeDetails->fields['activation_code'],0,2).$Row['id'].substr($Row['location_name'],0,2).$Row['location_id'];
    
        $cid= $Row['id'];
        $lid =$Row['location_id'];
        $barcode = $barcodea;
        $where_clause = array();
		$where_clause['id'] = $cid;
		$RSCompDetails = $objDB->Show("campaigns", $where_clause);
                
                $where_clause_l = array();
                $where_clause_l['id'] = $lid;
                $loc_nm = $objDB->Show("locations",$where_clause_l);
                
                $title = $RSCompDetails->fields['title'];
                $expdate = $RSCompDetails->fields['expiration_date'];
                $discont = $RSCompDetails->fields['discount'];
                $desc = $RSCompDetails->fields['description'];
                $rpoint = $RSCompDetails->fields['redeem_rewards'];
                $timzone = $RSCompDetails->fields['timezone'];
                load_print_coupon_data($Row['id'],$Row['location_id'],$barcodea ,1);
               
        ?>
  
    <?php }
    }?>
</div>
</div>
            <?php
			
		
}
//28-12-2012 get my deals according to category , month ,year //
//29-12-2012 get redeem deals according to category , month ,year//

/**
 * @uses get filter of redeem deals
 * @param year,month,category
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_filter_redeem_deals']))
{
     if($_REQUEST['year'] != 0 && $_REQUEST['month'] != 0)
	{
		   $from_date = $_REQUEST['year']."-".$_REQUEST['month']."-01 00:00:00";
	    	$to_date =  $_REQUEST['year']."-".$_REQUEST['month']."-31 23:59:59";
	}
	else if($_REQUEST['year'] != 0 && $_REQUEST['month'] == 0)
	{
			   $from_date = $_REQUEST['year']."-01-01 00:00:00";
     	     	$to_date =  $_REQUEST['year']."-12-31 23:59:59";
	}
	else if($_REQUEST['year'] == 0 && $_REQUEST['month'] != 0)
        {
           
            //for($y=2000;$y<=date('Y');$y++)
			for($y=date('Y');$y<=date('Y')+2;$y++)
            {  
                $date_str .=  " (C.created_date>='".$y."-".$_REQUEST['month']."-01 00:00:00' AND C.created_date<='".$y."-".$_REQUEST['month']."-31 00:00:00' ) ";
                
                if($y != date('Y'))
                {
                    $date_str .= " || ";
                }
            }
            
        }
	
        else if($_REQUEST['year'] == 0 && $_REQUEST['month'] == 0)
	{
			  
	}
        $cat_wh = "";
        if($_REQUEST['category']!=0)
        {
            $cat_wh = " and C.category_id=".$_REQUEST['category'];
        }
        else
        {
            $cat_wh=" and CAT.active=1";
        }
        $dt_w = "";
        if($_REQUEST['year'] != 0)
        {
            $dt_w= " and (C.created_date>='$from_date' AND C.created_date<='$to_date') ";
        }
	else if($_REQUEST['month'] !=0)
	{
		 $dt_w=" and ( ".$date_str." ) " ;
		
	}

	$mlatitude=$_SESSION['mycurrent_lati'];
	          $mlongitude=$_SESSION['mycurrent_long'];
		  if($mlatitude!="" && $mlongitude!="")
		{
		    /*$Sql = "SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,
		                (((acos(sin((".$mlatitude."*pi()/180)) * 
				sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
				cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
				pi()/180))))*180/pi())*60*1.1515
			       ) as distance
				FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id 
                                AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and C.is_walkin!=1  and cl.active=1  and 
                                ru.customer_id = '".$_SESSION['customer_id']."' AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1 
                                  ".$dt_w." ".$cat_wh."  GROUP BY ru.campaign_id , ru.location_id 
				 ORDER BY distance";	*/

			$RS = $objDB->Conn->Execute("SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,
		                (((acos(sin((".$mlatitude."*pi()/180)) * 
				sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
				cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
				pi()/180))))*180/pi())*60*1.1515
			       ) as distance
				FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id 
                                AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and C.is_walkin!=1  and cl.active=1  and 
                                ru.customer_id = ? AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1 
                                  ".$dt_w." ".$cat_wh."  GROUP BY ru.campaign_id , ru.location_id 
				 ORDER BY distance",array($_SESSION['customer_id']));
		}
		else
		{
			
       /*$Sql = "SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude
				FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id 
                                AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and C.is_walkin!=1  and cl.active=1  and 
                                ru.customer_id = '".$_SESSION['customer_id']."' AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1 
                                  ".$dt_w." ".$cat_wh."  GROUP BY ru.campaign_id , ru.location_id 
				 ORDER BY C.created_date DESC";*/
			$RS = $objDB->Conn->Execute("SELECT  sum(ru.earned_reward)  as earned_reward,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude
				FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
				WHERE  l.id=cl.location_id and cl.campaign_id=C.id 
                                AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN C.start_date AND C.expiration_date  and C.is_walkin!=1  and cl.active=1  and 
                                ru.customer_id = ? AND ru.referred_customer_id=0 and ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1 
                                  ".$dt_w." ".$cat_wh."  GROUP BY ru.campaign_id , ru.location_id 
				 ORDER BY C.created_date DESC",array($_SESSION['customer_id']));
		} 
		
		?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0" style="padding:5px;float: left" id="example" >
	<thead>
	  <tr>
		<td colspan="4" align="center" >
                      <b>Select Year: </b>
                        <select id="opt_filter_year" >
                            <option value="0" <?php if($_REQUEST['year']==0) { echo "selected"; } ?> >--- ALL ---</option>
	 <?php
  for($y=date('Y');$y<=date('Y')+2;$y++)
  { ?>
	<option value="<?php echo $y; ?>" <?php if($_REQUEST['year']==$y) { echo "selected"; } ?> > <?php echo $y; ?> </option>
  <?php }
  ?>
	</select>
                        <b> Month: </b>
                        <select id="opt_filter_month" >
                            <option value="0" <?php if($_REQUEST['month']==0) { echo "selected"; } ?>  >---- ALL ----</option>
	<option value="01" <?php if($_REQUEST['month']=="01") { echo "selected"; } ?> >January</option>
	<option value="02" <?php if($_REQUEST['month']=="02") { echo "selected"; } ?> >February</option>
	<option value="03" <?php if($_REQUEST['month']=="03") { echo "selected"; } ?>  >March</option>
	<option value="04" <?php if($_REQUEST['month']=="04") { echo "selected"; } ?> >April</option>
	<option value="05" <?php if($_REQUEST['month']=="05") { echo "selected"; } ?> >May</option>
	<option value="06" <?php if($_REQUEST['month']=="06") { echo "selected"; } ?> >Jun</option>
	<option value="07" <?php if($_REQUEST['month']=="07") { echo "selected"; } ?> >July</option>
	<option value="08" <?php if($_REQUEST['month']=="08") { echo "selected"; } ?> >August</option>
	<option value="09" <?php if($_REQUEST['month']=="09") { echo "selected"; } ?>  >September</option>
	<option value="10" <?php if($_REQUEST['month']=="10") { echo "selected"; } ?> >October</option>
	<option value="11" <?php if($_REQUEST['month']=="11") { echo "selected"; } ?> >November</option>
	<option value="12" <?php if($_REQUEST['month']=="12") { echo "selected"; } ?> >December</option>
	</select>
                        <b> Category: </b>
                        <select id="opt_filter_category" >
                            <?php
                            $array_where_act['active']=1;
                            $RSCat = $objDB->Show("categories",$array_where_act);
                            ?>
                            <option value="0" <?php if($_REQUEST['category']==0) { echo "selected"; } ?>  >--- ALL ---</option>
                                    <?
					while($Row = $RSCat->FetchRow()){
					?>
						<option value="<?=$Row['id']?>"  <?php if($_REQUEST['category']==$Row['id']) { echo "selected"; } ?> ><?=$Row['cat_name']?></option>
					<?
					}
					?>
	</select>
                        <input type="button" value="Show Result" name="btnfilterstaticscampaigns" id="btnfilterstaticscampaigns" />
                        
                </td>
		</tr>
		
		 <tr>
		
		<td colspan="4" align="center">&nbsp;
                        
		</td>
		</tr>
		
	    <tr>

                
                <th width="23%" align="left" class="tableDealTh" ></th>
        	<th width="23%" align="left" class="tableDealTh"><?php echo "Campaign Name";?></th>
		<th width="14%" align="left" class="tableDealTh"><?php echo $language_msg["mydeals"]["head_category"];?></th>
                <th width="14%" align="left" class="tableDealTh">Distance</th>
				
		</tr>
	    </thead>
	<tbody>
	  <?
		while($Row = $RS->FetchRow()){
	  ?>
	  <tr class="tableDeal" style="height:115px;">
		<!--// 369-->
                <td class="businesslogo">
                    <?php
                    if($Row['business_logo']!="")
                       {
                           $img_src=ASSETS_IMG."/m/campaign/".$Row['business_logo']; 
                       }
                       else 
                       {
                           $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
                       }
                    //$img_src=WEB_PATH."/merchant/images/logo/".$Row->business_logo;
                    ?>
                    <img src="<?php echo $img_src;?>" width="100px" height="90px" />
	     
                </td>
        	
        <?php 
                                                    
                                 $i =$Row['timezone']; // "-11:30,0";
			$indec = substr($i,0,1);
			$last = substr($i,1,strpos($i,","));
			$hmarr = explode(":",$last);
			$hoffset = $hmarr[0]*60*60;
			$moffset = $hmarr[1]*60;
			
			  $offset=$hoffset + $moffset;
			  $dateFormat="Y-m-d H:i:s";
                          if($indec == "+")
              {
                    $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))+$offset);
              }
              else
              {
                    $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))-$offset);
              }
              $today_date =  $timeNdate1_;
			
			  if($indec == "+")
                          {
//				$timeNdate= date($dateFormat, strtotime($Row['expiration_date'])+$offset);
//                                $timeNdate1= date($dateFormat, strtotime($Row['start_date'])+$offset);
                              $timeNdate = $Row['expiration_date'];
                               $timeNdate1=$Row['start_date'];
                          }
			  else
                          {
//				$timeNdate= date($dateFormat, strtotime($Row['expiration_date'])-$offset);
//                                $timeNdate1= date($dateFormat, strtotime($Row['start_date'])-$offset);
                               $timeNdate = $Row['expiration_date'];
                               $timeNdate1=$Row['start_date'];
                          }
				
				$today_date =  $timeNdate1_;
                               
				?>
                <td align="left" style="line-height:1.5;" >
                    <div class="griddealtitle">
                <a href="<?=WEB_PATH?>/campaign.php?campaign_id=<?php echo $Row['id']; ?>&l_id=<?php echo $Row['location_id']; ?>" >
                	
						<?php echo $Row['title'] ?>
                                              
					</a>
                    </div>
                    <div class="gridloctitle">
                           <?php
                           if($Row['location_name']!="")
                                {
                                    echo $Row['location_name'];
                                } 
                                else
                                {
                                // for business name 
                                                        $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='. $Row['location_id']);
                                                                        if(trim($arr[0]) == "")
                                                                             {
                                                                                     unset($arr[0]);
                                                                                     $arr = array_values($arr);
                                                                             }
                                                                             $json = json_decode($arr[0]);
                                                                        $busines_name  = $json->bus_name;
                                                                        echo $busines_name;
                                                        // for business name 
                                }
                           ?>
                        </div>
                    <div class="gridlocaddr">
                        <?php
                                                    if($from_lati!="" && from_long!="")
                                                    {
                                                        $maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;                                                   
                                                    ?>
                                                        <div class="div_getdirection">
                                                            <a href="<?php echo $maphref; ?>" target="_blank" style="color:#0066FF;">Get Direction</a>
                                                        </div>
                                                    <?php
                                                    
                                                    }
                                                ?>
                       <?php $address = $Row['address'].", ".$Row['city'].", ".$Row['state'];
						echo $address;	
					?>
                  </div>
            
                       
		</td>
        <!--// 369-->
		<td align="left"><?=$Row['cat_name']?></td>
        <!--// 369-->
        
         <?php
		       $from_lati=$_SESSION['mycurrent_lati'];
                
			 $from_long=$_SESSION['mycurrent_long'];
			
			$to_lati=$Row['latitude'];
			
			$to_long=$Row['longitude'];
			
		       ?>
		       <td align="left" class="distance">
			   		<?php 
						if($from_lati!="" && from_long!="")
							echo $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M")." Miles";
						else
							echo "-";
					?>
               </td>

		</tr>
	  <?
			
			}
			
			
	  	
	  ?>
	  </tbody>
	</table><?php
			
		
}

//29-12-2012 get redeem deals category , month , year //

// merchant deal start filter

/**
 * @uses get filter of merchant deals
 * @param category
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_filter_merchant_deals']))
{
     
        $cat_wh = "";
        if($_REQUEST['category']!=0)
        {
            $cat_wh = " and c.category_id=".$_REQUEST['category'];
        }
        else
        {
            $cat_wh=" and CAT.active=1";
        }
        
        // 30-1-2013
        $mlatitude=$_SESSION['mycurrent_lati'];
	$mlongitude=$_SESSION['mycurrent_long'];
        
        
         if(isset($_COOKIE['miles_cookie']))
            {
                $miles = $_COOKIE['miles_cookie'];
            }
            else
            {
                $miles = 50;
            }
            if(isset($_COOKIE['cat_remember']))
            {
                $category_id  = $_COOKIE['cat_remember'];
            }
            else
            {
                $category_id  = 0;
            }
             $cat_wh = "";
        if( $category_id !=0)
        {
            $cat_wh = " and c.category_id=". $category_id ;
        }
        else
        {
            $cat_wh=" and CAT.active=1";
        }
            if(isset($_COOKIE['zoominglevel']))
            {
                 $zooming_level = $_COOKIE['zoominglevel'];
            }
            else
            {
                 $zooming_level = 20;
            }
           if($_COOKIE['searched_location'] == "")
           {
            $curr_latitude = $_SESSION['customer_info']['curr_latitude'];
            $curr_longitude = $_SESSION['customer_info']['curr_longitude'];
           }
           else{
                 $curr_latitude = $_COOKIE['mycurrent_lati'];
                $curr_longitude = $_COOKIE['mycurrent_long'];
           }
             $Where_miles = "";
            if($curr_longitude != "")
            {
                $Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE ( 69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
            }
            else
            {
                
                $curr_latitude = $_COOKIE['mycurrent_lati'];
                $curr_longitude = $_COOKIE['mycurrent_long'];
                $Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE (69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
            }
        
        
	if($mlatitude!="" && $mlongitude!="")
	{
		 /*$Sql="select cl.location_id,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,c.*,CAT.cat_name,
		 (((acos(sin((".$mlatitude."*pi()/180)) * 
		 sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
		 cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
		 pi()/180))))*180/pi())*60*1.1515
		 ) as distance
		 from campaigns c,categories CAT,campaign_location cl,locations l where l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and cl.location_id in
 (SELECT DISTINCT ss.location_id from subscribed_stores ss where CAT.active=1 and ss.customer_id=".$_SESSION['customer_id']." and ss.subscribed_status=1 ".$Where_miles." ) AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date  and c.is_walkin!=1 and cl.active=1 
and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ".$_SESSION['customer_id']." and location_id=cl.location_id ) and cl.offers_left>0 and cl.active=1".$cat_wh." ORDER BY distance";*/

		                 $RS = $objDB->Conn->Execute("select cl.location_id,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,c.*,CAT.cat_name,
		 (((acos(sin((".$mlatitude."*pi()/180)) * 
		 sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
		 cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
		 pi()/180))))*180/pi())*60*1.1515
		 ) as distance
		 from campaigns c,categories CAT,campaign_location cl,locations l where l.active=? and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and cl.location_id in
 (SELECT DISTINCT ss.location_id from subscribed_stores ss where CAT.active=1 and ss.customer_id=? and ss.subscribed_status=? ".$Where_miles." ) AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date  and c.is_walkin!=1 and cl.active=1 
and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id ) and cl.offers_left>? and cl.active=?".$cat_wh." ORDER BY distance",array(1,$_SESSION['customer_id'],1,$_SESSION['customer_id'],0,1));


	}
	else
	{
	   /* $Sql="select cl.location_id,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and cl.location_id in
 (SELECT DISTINCT location_id from subscribed_stores where customer_id=".$_SESSION['customer_id']." and subscribed_status=1) AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date  and c.is_walkin!=1 and cl.active=1 
and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ".$_SESSION['customer_id']." and location_id=cl.location_id ) and cl.offers_left>0 and cl.active=1".$cat_wh;*/

            $RS = $objDB->Conn->Execute("select cl.location_id,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.active=? and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and cl.location_id in
 (SELECT DISTINCT location_id from subscribed_stores where customer_id=? and subscribed_status=?) AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date  and c.is_walkin!=1 and cl.active=? 
and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id ) and cl.offers_left>? and cl.active=?".$cat_wh,array(1,$_SESSION['customer_id'],1,1,$_SESSION['customer_id'],0,1));	
	}
        //$RS = $objDB->Conn->Execute($Sql);
		?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0" style="padding:5px;float: left" id="example" >
	<thead>
	    </thead>
            
            <tbody>
	  <?
	  
      while($Row = $RS->FetchRow())
      {
		  $disp=0;
			if($Row['level']==0)
			{
				//$Sql="select * from campaign_groups cg,campaign_location cl where cg.campaign_id=cl.campaign_id and cg.campaign_id=398 and cl.location_id=70";
				/*$Sql="select * from campaign_groups cg,campaign_location cl where cg.campaign_id=cl.campaign_id and cg.campaign_id=".$Row['id']." and cl.location_id=".$Row['location_id'];
				
				$RS_camp_group =  $objDB->execute_query($Sql);*/
				$RS_camp_group =  $objDB->execute_query("select * from campaign_groups cg,campaign_location cl where cg.campaign_id=cl.campaign_id and cg.campaign_id=? and cl.location_id=?",array($Row['id'],$Row['location_id']));
				if($RS_camp_group->RecordCount()>0)
				{
					
					while($Row1 = $RS_camp_group->FetchRow())
					{
						//echo $Row1['group_id']."-";
						$array_where_camp1['group_id'] = $Row1['group_id'];
						$array_where_camp1['user_id'] = $_SESSION['customer_id'];
						$RS_mer_grp_subs = $objDB->Show("merchant_subscribs", $array_where_camp1);
						if($RS_mer_grp_subs->RecordCount()>0)
							$disp=1;
					}
				}
				if($disp==1)
				{
					 ?>
                       
                  <tr class="tableDeal" style="height:200px;">
                    
                    <?php 
                    
                        $i =$Row->timezone; // "-11:30,0";
                        $indec = substr($i,0,1);
                        $last = substr($i,1,strpos($i,","));
                        $hmarr = explode(":",$last);
                        $hoffset = $hmarr[0]*60*60;
                        $moffset = $hmarr[1]*60;
                        
                          $offset=$hoffset + $moffset;
                          $dateFormat="Y-m-d H:i:s";
                                      
                                       if($indec == "+")
                          {
                                $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))+$offset);
                          }
                          else
                          {
                                $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))-$offset);
                          }
                          $today_date =  $timeNdate1_;
                          
                                       $timeNdate=$Row['expiration_date'];
                                            $timeNdate1=$Row['start_date'];
            
                                                // -- For barcode
                            $where_x = array();
                    $where_x['campaign_id'] = $Row->id;
                    $CodeDetails = $objDB->Show("activation_codes", $where_x);
            
                    $barcodea = $_SESSION['customer_id'].substr($CodeDetails->fields['activation_code'],0,2).$Row['id'].substr($Row['location_name'],0,2).$Row['location_id'];
                                                // -- For barcode
                            
                    ?>
                        <td class="businesslogo">
                        <?php
                     if($Row['business_logo']!="")
                    {
                        $img_src=ASSETS_IMG."/m/campaign/".$Row['business_logo']; 
                    }
                    else 
                    {
                        $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
                    }
                     //$img_src=WEB_PATH."/merchant/images/logo/".$Row->business_logo;
                     ?>
                     <img src="<?php echo $img_src;?>" />
                    </td>
                    <?php
                        $where_x1 = array();
                        $where_x1['campaign_id'] = $Row['id'];
                        $where_x1['location_id'] = $Row['location_id'];
                        $campLoc = $objDB->Show("campaign_location", $where_x1);
                    ?>
                    
                    <!--// 369-->
                    
                    <!--// 369-->
                    <td align="left" class="locationdata" style="line-height:1.5;"><?php //echo $Row->location_name; ?>
                                        <?php //echo $Row->address.", ".$Row->city.", ".$Row->state.", ".$Row->zip?>
                        <div class="gridloctitle">
                                       <?php
                                            if($Row['location_name']!="")
                                            {
                                                echo $Row['location_name'];
                                            } 
                                            else
                                            {
                                                
                                            
                                            // for business name 
                                                                    $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='. $Row['location_id']);
                                                                                    if(trim($arr[0]) == "")
                                                                                         {
                                                                                                 unset($arr[0]);
                                                                                                 $arr = array_values($arr);
                                                                                         }
                                                                                         $json = json_decode($arr[0]);
                                                                                    $busines_name  = $json->bus_name;
                                                                                    echo $busines_name;
                                                                    // for business name 
                                            }
                                       ?>
                            </div>
                        
                        <div class="griddealtitle">
                            <a href="<?=WEB_PATH?>/campaign.php?campaign_id=<?php echo $Row['id']; ?>&l_id=<?php echo $Row['location_id']; ?>" >
                                
                                    <?php echo $Row['title']; ?>
                                                          
                                </a>
                        </div>
                        <div class="griddealtype">
                                                
                                          <?php 
                                                
                                                if($Row['number_of_use']==1)
                                                {
                                                    if($Row['new_customer']==1)
                                                    {
                                                        echo "Limit : One Per Customer,Valid For New Customer Only";
                                                    }
                                                    else 
                                                    {
                                                        echo "Limit : One Per Customer";
                                                    }
                                                }
                                                elseif($Row['number_of_use']==2)
                                                    echo "Limit : One Per Customer Per Day";
                                                elseif($Row['number_of_use']==3)
                                                    echo "Limit : Earn Redemption Points On Every Visit";                
                                          ?>

                     </div>
                        <div class="griddealpoint">
                                            <div class="redeem_point">
                                              <p><?php echo $Row['redeem_rewards']; ?></p>
					       <span> Redeem Point</span>
                                            </div>
                                            <div class="referral_point">
						<p><?php echo $Row['referral_rewards']; ?></p>
						<span>Share Point</span>
                                            </div>
                                          <div class="offer_left">
						<p><?php echo $campLoc->fields['offers_left']; ?></p>
						<span>Offer Left</span>
                                            </div>

                         </div>
                        <div class="Share" >Share the deal and Earn more Rewards
                              <? if($_SESSION['customer_id'] != ""){ ?>
                              <a href="javascript:void(0)" id="btnsharediv_<?php echo $Row['id']."-".$Row['location_id'];?>" >Go For It >></a> 
                              
                              <!--<input type="button" value="Go for it" id="btn_share_div" />-->
                              <? } else {?>
                              <a  href="<?php echo WEB_PATH."/register.php?url=".curPageURL();?>" id="btn_share_div<?php echo $Row->id;?>" >Go For It >></a> 
                              <!--<input type="submit" value="Go for it" id="btn_share_div" name="chk_for_share_campaign" />-->
                              <?php } ?>
                            </div>
	     <div class="sharing_icon_css">
                            <div class="p1">
                              <? if($_SESSION['customer_id'] != ""){ ?>
                              <a href="javascript:void(0)" id="btn_msg_div_<?php echo $Row['id']."-".$Row['location_id'];?>" ><img src="<?php echo ASSETS_IMG.'/c/msg.png'?>" name="msg_share" /></a>
                              <? } else {?>
                              <a  href="<?php echo WEB_PATH."/register.php?url=".curPageURL();?>" id="btn_msg_div" ><img src="<?php echo ASSETS_IMG.'/c/msg.png'?>" name="msg_share" /></a>
                              <?php } ?>
                            </div>
			     <?php
			        if($Row['business_logo']!="")
				{
				   // $img_src=WEB_PATH."/merchant/images/logo/".$RS[0]->business_logo; 
				    $fb_img_share = ASSETS_IMG."/m/campaign/".$Row['business_logo'];
				}
				else 
				{
				    $fb_img_share =ASSETS_IMG."/c/Merchant_Offer.png";
				}
				$share="true";
				$th_link = WEB_PATH."/register.php?campaign_id=".$Row['id']."&l_id=".$Row['location_id']."&share=$share";
			if($_SESSION['customer_id'] != ""){
				$th_link .= "&customer_id=".base64_encode($_SESSION['customer_id']);
				//echo $th_link; 
			}
			
			   
			  
					$title=urlencode($Row['title']);
					
					$url=urlencode($th_link);
					$summary=urlencode($Row['deal_detail_description']);
					$image=$fb_img_share;
					
					//$image="http://ia.media-imdb.com/images/M/MV5BMjEwOTA2MjMwMl5BMl5BanBnXkFtZTcwODc3MDgxOA@@._V1._SY314_CR3,0,214,314_.jpg";
					//echo $image; 
			?>
			<div class="p2">
			    <a onClick="window.open('https://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&p[images][0]=<?php echo $image;?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');" target="_parent" href="javascript: void(0)">
				<img src="<?php echo ASSETS_IMG.'/c/fb.png'?>" name="fbshare" />
			    </a>
			    </div>
			
			<div class="p3">
			          <iframe allowtransparency="true" frameborder="0" scrolling="no" data-url="<?php echo $th_link;?>"  src="https://platform.twitter.com/widgets/tweet_button.html?count=none&url=<?php echo urlencode($th_link);?>"
            style="width:55px; height:20px;"></iframe>
				      
                        </div>
			
			<div class="p4">
                             <div class="g-plusone" data-size="medium" data-annotation="none" data-href="<?php echo $th_link;?>"></div>
					
					<!-- Place this tag after the last +1 button tag. -->
					<script type="text/javascript">
					  (function() {
					    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					    po.src = 'https://apis.google.com/js/plusone.js';
					    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
                        </div>
	       
			
	       </div>
            <script>
        $("#btnsharediv_<?php echo $Row['id']."-".$Row['location_id'];?>").click(function(){
	          //alert("<?php echo $Row['id']."-".$Row['location_id'];?>");
		  open_popup1('Notification<?php echo $Row['id'].$Row['location_id'];?>');

	});    
        </script>
                       
                        
                                   </td>
<!--                                   <td align="left"><?=$Row['cat_name']?></td>-->
                                     <?php
                           $from_lati=$_SESSION['mycurrent_lati'];
                            
                         $from_long=$_SESSION['mycurrent_long'];
                        
                        $to_lati=$Row['latitude'];
                        
                        $to_long=$Row['longitude'];
                        
                           ?>
                           <td align="left" class="distance">
                               <div class="miles">
                                <div class="expir_tooltip">
                                               
                                                </div>
                                   <span style="display:none;">
                                                    <?php 
                                                    echo date("Y-m-d g:i:s A", strtotime($Row['expiration_date']));
                                                    ?>
                                                </span>
                                <?php 
                                    if($from_lati!="" && from_long!="")
                                        echo $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M")." Miles";
                                    else
                                        echo "-";
                                ?>
                               </div>
                               <div class="gridlocaddr">
                                   <?php
                                                    if($from_lati!="" && from_long!="")
                                                    {
                                                        $maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;                                                   
                                                    ?>
                                                        <div class="div_getdirection">
                                                            <a href="<?php echo $maphref; ?>" target="_blank" style="color:#0066FF;">Get Direction</a>
                                                        </div>
                                                    <?php
                                                    
                                                    }
                                                ?>
                                   <?php $address = $Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip'].", ".$Row['country'];
                                    echo $address;	
                                ?>
                              </div>
		               <?php //$Row->id."-".$Row->location_id
                            $where_clause = array();
		$where_clause['id'] = $Row->id;
		$RSCompDetails = $objDB->Show("campaigns", $where_clause);
                
                $where_x = array();
		$where_x['campaign_id'] = $Row->id;
		$CodeDetails = $objDB->Show("activation_codes", $where_x);

		$where_clause = array();
		
			    $where_clause = array();
		$where_clause['id'] = $Row->location_id;
		$RSLocation = $objDB->Show("locations", $where_clause);
		         // b redeem_rewards
                //$barcodea = $RSLocation->fields['location_name'].$RSLocation->fields['zip'].$_SESSION['customer_id'].$_REQUEST[campaign_id];
		  $barcodea = $cst_id.substr($CodeDetails->fields['activation_code'],0,2).$Row->id.substr($RSLocation->fields['location_name'],0,2).$Row->location_id;
			
                           ?>
                           <div style="float:right">
                               <input type="submit" class="btn_mymerchantreserve" name="btnreserve" id="btnreserve" value="Reserve" cid="<?php echo $Row->id; ?>" lid="<?php echo $Row->location_id; ?>" />
			   </div>
                            </td>
            	
                    </tr>
                  
                  <? 
				}
			}
			else
			{
               
	  ?>
                       
	  <tr class="tableDeal" style="height:200px;">
             
        <?php 
        //$i = "+5:30,0";
			$i =$Row->timezone; // "-11:30,0";
			$indec = substr($i,0,1);
			$last = substr($i,1,strpos($i,","));
			$hmarr = explode(":",$last);
			$hoffset = $hmarr[0]*60*60;
			$moffset = $hmarr[1]*60;
			
			  $offset=$hoffset + $moffset;
			  $dateFormat="Y-m-d H:i:s";
			//  echo date($dateFormat) ."======";
                          
                           if($indec == "+")
              {
                    $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))+$offset);
              }
              else
              {
                    $timeNdate1_= date($dateFormat, strtotime(date("Y-m-d H:i:s"))-$offset);
              }
              $today_date =  $timeNdate1_;
              
                           $timeNdate=$Row['expiration_date'];
                                $timeNdate1=$Row['start_date'];

                                    // -- For barcode
                $where_x = array();
		$where_x['campaign_id'] = $Row->id;
		$CodeDetails = $objDB->Show("activation_codes", $where_x);

		$barcodea = $_SESSION['customer_id'].substr($CodeDetails->fields['activation_code'],0,2).$Row['id'].substr($Row['location_name'],0,2).$Row['location_id'];
                                    // -- For barcode
				?>
<!--                <a href="<?=WEB_PATH?>/campaign.php?campaign_id=<?php echo $Row['id']; ?>&l_id=<?php echo $Row['location_id']; ?>" >-->
                	<?php /*?><a href="<?=WEB_PATH?>/print_coupon.php?barcodea=<?php echo $barcodea; ?>&campaign_id=<?php echo $Row->id; ?>&l_id=<?php echo $Row->location_id; ?>"><?php */?>
						<?php //echo $Row['title']; ?>
                                                <?php /*
                                                if($Row['discount']=="")
                                                {
                                                    echo $Row['title'];
                                                }
                                                else {
                                                    echo $Row['discount']." ".$Row['title'];
                                                }*/
                                                ?>
<!--					</a>-->
                <?php	
				
					
		?>
            <td class="businesslogo">
            <?php
	     if($Row['business_logo']!="")
		{
		    $img_src=ASSETS_IMG."/m/campaign/".$Row['business_logo']; 
		}
		else 
		{
		    $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
		}
	     //$img_src=WEB_PATH."/merchant/images/logo/".$Row->business_logo;
	     ?>
	     <img src="<?php echo $img_src;?>"  />
        </td>
		
		
        <!--// 369-->
	<?php
            $where_x1 = array();
		$where_x1['campaign_id'] = $Row['id'];
		$where_x1['location_id'] = $Row['location_id'];
                $campLoc = $objDB->Show("campaign_location", $where_x1);
        ?>
        <!--// 369-->
        <td align="left" class="locationdata" style="line-height:1.5;"><?php //echo $Row->location_name; ?>
                            <?php //echo $Row->address.", ".$Row->city.", ".$Row->state.", ".$Row->zip?>
            <div class="gridloctitle">
                           <?php
                                if($Row['location_name']!="")
                                {
                                    echo $Row['location_name'];
                                } 
                                else
                                {
                                    
                                
                                // for business name 
                                                        $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='. $Row['location_id']);
                                                                        if(trim($arr[0]) == "")
                                                                             {
                                                                                     unset($arr[0]);
                                                                                     $arr = array_values($arr);
                                                                             }
                                                                             $json = json_decode($arr[0]);
                                                                        $busines_name  = $json->bus_name;
                                                                        echo $busines_name;
                                                        // for business name 
                                }
                           ?>
                </div>
            <div class="griddealtitle">
                <a href="<?=WEB_PATH?>/campaign.php?campaign_id=<?php echo $Row['id']; ?>&l_id=<?php echo $Row['location_id']; ?>" >
                	
						<?php echo $Row['title']; ?>
                                              
					</a>
            </div>
            <div class="griddealtype">
                                                
                                          <?php 
                                                //echo $Row['number_of_use'];
                                                //echo $Row['new_customer'];
                                                if($Row['number_of_use']==1)
                                                {
                                                    if($Row['new_customer']==1)
                                                    {
                                                        echo "Limit : One Per Customer,Valid For New Customer Only";
                                                    }
                                                    else 
                                                    {
                                                        echo "Limit : One Per Customer";
                                                    }
                                                }
                                                elseif($Row['number_of_use']==2)
                                                    echo "Limit : One Per Customer Per Day";
                                                elseif($Row['number_of_use']==3)
                                                    echo "Limit : Earn Redemption Points On Every Visit";                
                                          ?>

              </div>
            <div class="griddealpoint">
                                            <div class="redeem_point">
                                              <p><?php echo $Row['redeem_rewards']; ?></p>
					       <span> Redeem Point</span>
                                            </div>
                                            <div class="referral_point">
						<p><?php echo $Row['referral_rewards']; ?></p>
						<span>Share Point</span>
                                            </div>
                                          <div class="offer_left">
						<p><?php echo $campLoc->fields['offers_left']; ?></p>
						<span>Offer Left</span>
                                            </div>

                                        </div>
	     <div class="Share" >Share the deal and Earn more Rewards
                              <? if($_SESSION['customer_id'] != ""){ ?>
                              <a href="javascript:void(0)" id="btnsharediv_<?php echo $Row['id']."-".$Row['location_id'];?>" >Go For It >></a> 
                              
                              <!--<input type="button" value="Go for it" id="btn_share_div" />-->
                              <? } else {?>
                              <a  href="<?php echo WEB_PATH."/register.php?url=".curPageURL();?>" id="btn_share_div<?php echo $Row->id;?>" >Go For It >></a> 
                              <!--<input type="submit" value="Go for it" id="btn_share_div" name="chk_for_share_campaign" />-->
                              <?php } ?>
                            </div>
	     <div class="sharing_icon_css">
                            <div class="p1">
                              <? if($_SESSION['customer_id'] != ""){ ?>
                              <a href="javascript:void(0)" id="btn_msg_div_<?php echo $Row['id']."-".$Row['location_id'];?>" ><img src="<?php echo ASSETS_IMG.'/c/msg.png'?>" name="msg_share" /></a>
                              <? } else {?>
                              <a  href="<?php echo WEB_PATH."/register.php?url=".curPageURL();?>" id="btn_msg_div" ><img src="<?php echo ASSETS_IMG.'/c/msg.png'?>" name="msg_share" /></a>
                              <?php } ?>
                            </div>
			     <?php
			        if($Row['business_logo']!="")
				{
				   // $img_src=WEB_PATH."/merchant/images/logo/".$RS[0]->business_logo; 
				    $fb_img_share = ASSETS_IMG."/m/campaign/".$Row['business_logo'];
				}
				else 
				{
				    $fb_img_share =ASSETS_IMG."/c/Merchant_Offer.png";
				}
				$share="true";
				$th_link = WEB_PATH."/register.php?campaign_id=".$Row['id']."&l_id=".$Row['location_id']."&share=$share";
			if($_SESSION['customer_id'] != ""){
				$th_link .= "&customer_id=".base64_encode($_SESSION['customer_id']);
				//echo $th_link; 
			}
			
			   
			  
					$title=urlencode($Row['title']);
					
					$url=urlencode($th_link);
					$summary=urlencode($Row['deal_detail_description']);
					$image=$fb_img_share;
					
					//$image="http://ia.media-imdb.com/images/M/MV5BMjEwOTA2MjMwMl5BMl5BanBnXkFtZTcwODc3MDgxOA@@._V1._SY314_CR3,0,214,314_.jpg";
					//echo $image; 
			?>
			<div class="p2">
			    <a onClick="window.open('https://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&p[images][0]=<?php echo $image;?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');" target="_parent" href="javascript: void(0)">
				<img src="<?php echo ASSETS_IMG.'/c/fb.png'?>" name="fbshare" />
			    </a>
			    </div>
			
			<div class="p3">
			          <iframe allowtransparency="true" frameborder="0" scrolling="no" data-url="<?php echo $th_link;?>"  src="https://platform.twitter.com/widgets/tweet_button.html?count=none&url=<?php echo urlencode($th_link);?>"
            style="width:55px; height:20px;"></iframe>
				      
                        </div>
			
			<div class="p4">
                           <!-- Place this tag where you want the +1 button to render. -->
				<div class="g-plusone" data-size="medium" data-annotation="none" data-href="<?php echo $th_link;?>"></div>
					
					<!-- Place this tag after the last +1 button tag. -->
					<script type="text/javascript">
					  (function() {
					    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					    po.src = 'https://apis.google.com/js/plusone.js';
					    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
                            </div>
	       
			
	       </div>
            <script>
        $("#btnsharediv_<?php echo $Row['id']."-".$Row['location_id'];?>").click(function(){
	          //alert("<?php echo $Row['id']."-".$Row['location_id'];?>");
		  open_popup1('Notification<?php echo $Row['id'].$Row['location_id'];?>');

	});    
        </script>
            
           
            
                       </td>
<!--                       <td align="left"><?=$Row['cat_name']?></td>-->
                         <?php
		       $from_lati=$_SESSION['mycurrent_lati'];
                
			 $from_long=$_SESSION['mycurrent_long'];
			
			$to_lati=$Row['latitude'];
			
			$to_long=$Row['longitude'];
			
		       ?>
		       <td align="left" class="distance">
                           <div class="miles">
                            <div class="expir_tooltip">
                                               
                                                </div>
                               <span style="display:none;">
                                                    <?php 
                                                    echo date("Y-m-d g:i:s A", strtotime($Row['expiration_date']));
                                                    ?>
                                                </span>
			   		<?php 
						if($from_lati!="" && from_long!="")
							echo $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M")." Miles";
						else
							echo "-";
					?>
                           </div>
                           <div class="gridlocaddr">
                               <?php
                                                    if($from_lati!="" && from_long!="")
                                                    {
                                                        $maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;                                                   
                                                    ?>
                                                        <div class="div_getdirection">
                                                            <a href="<?php echo $maphref; ?>" target="_blank" style="color:#0066FF;">Get Direction</a>
                                                        </div>
                                                    <?php
                                                    
                                                    }
                                                ?>
                       <?php $address = $Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip'].", ".$Row['country'];
						echo $address;	
					?>
                  </div>
			    <?php //$Row->id."-".$Row->location_id
                            $where_clause = array();
		$where_clause['id'] = $Row->id;
		$RSCompDetails = $objDB->Show("campaigns", $where_clause);
                
                $where_x = array();
		$where_x['campaign_id'] = $Row->id;
		$CodeDetails = $objDB->Show("activation_codes", $where_x);

		$where_clause = array();
		
			    $where_clause = array();
		$where_clause['id'] = $Row->location_id;
		$RSLocation = $objDB->Show("locations", $where_clause);
		         // b redeem_rewards
                //$barcodea = $RSLocation->fields['location_name'].$RSLocation->fields['zip'].$_SESSION['customer_id'].$_REQUEST[campaign_id];
		  $barcodea = $cst_id.substr($CodeDetails->fields['activation_code'],0,2).$Row->id.substr($RSLocation->fields['location_name'],0,2).$Row->location_id;
			
                           ?>
                           <div style="float:right">
                               <input type="submit" class="btn_mymerchantreserve" name="btnreserve" id="btnreserve" value="Reserve" cid="<?php echo $Row['id']; ?>" lid="<?php echo $Row['location_id']; ?>" />
			   </div>
			   
                </td>
	
		</tr>
  <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>PopUpContainer" class="container_popup"  style="display: none;">
 <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>BackDiv" class="divBack"></div>
    <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>FrontDivProcessing" class="Processing" style="display:none;">                                            
            <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>MaindivLoading" align="center" valign="middle" class="imgDivLoading" style="left:32%;top: 11%;">
                <div class="modal-close-button" style="visibility: visible;"><a tabindex="0" onclick="close_popup('Notification<?php echo $Row['id'].$Row['location_id'];?>');" id="fancybox-close" style="display:inline;"></a></div>
                <div id="Notification<?php echo $Row->id.$Row->location_id;?>mainContainer" class="innerContainer" style="height:366px;width:448px;background:none;">
                    <div class="main_content">
                        <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                            <div class="campaign_detail_div" style="">
                                
                                    <div id="activatesahreDiv" class="div_share_friend" style="background-image:none;border:2px solid #FF8810;height:375px;text-align:left">
                                  
                                    <div class="head">
					<?php $img_src=ASSETS_IMG."/c/header-logo_new.png";?>
                                        <img src="<?php echo $img_src;?>" alt="Scanflip"/><br/>
                                        Powering Smart Saving from local Merchants
                                    </div>
                                    <div class="share_friends">Share With Friends</div>
                                    <div class="share_msg">To Share this offer with friends,complete the from below.</div>
                                    <div class="message_pop">
                                        <strong>Message :</strong><br/>
                                        <strong>I thought</strong> you might enjoy knowing this deals from Scanflip
                                    </div>        
                                   
                                    <div class="notice_mail">Specify up to 50 of your friend's email below separated by commas (,) or semicolons (;)</div>
                                      <p style="color:#FF0000;" id="<?php echo $Row['id'].$Row['location_id'];?>"></p>
                                    <div class="text_area"><textarea rows="5" cols="50"  name="txt_share_frnd" id="txt_share_frnd<?php echo $Row['id'].$Row['location_id'];?>"></textarea></div>
                                   
                                    <div class="buttons_bot">
                                       
                                        <input type="hidden" name="reffer_campaign_id1" id="reffer_campaign_id" value="<?=$_REQUEST['campaign_id']?>" />
                                        <input type="hidden" name="reffer_campaign_name1" id="reffer_campaign_name" value="<?php echo $camp_title; ?>" />
                                        <input type="hidden" name="refferal_location_id1" value="<?php echo $_REQUEST['l_id']; ?>" id="refferal_location_id" />
                                        <input type="button" class="btnsharegridbutton" value="Share" name="btn_share_grid" id="btnsharegridbutton<?php echo $Row['id']."-".$Row['location_id']; ?>" />
                                        <input type="button" value="Cancel" onclick="close_popup('Notification<?php echo $Row['id'].$Row['location_id'];?>');" id="btn_cancel"  />
					<?php $img_src=ASSETS_IMG."/c/ajax-offer-loader.gif";?>
					<img src="<?php echo $img_src; ?>" alt="" id="shareloading<?php echo $Row['id'].$Row['location_id'];?>" height="20px" width="20px" style="display:none"/>
                                    </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
  </div>
  <script>

 $("#btnsharegridbutton<?php echo $Row['id']."-".$Row['location_id']; ?>").click(function(){
		//alert("in");
                
		var email_str = $("#txt_share_frnd<?php echo $Row['id'].$Row['location_id'];?>").val();
              	 var msg ="";
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
		      
		        $("#shareloading<?php echo $Row['id'].$Row['location_id'];?>").show();
		       $.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/process.php",
                                      data: "btn_share_grid=yes&reffer_campaign_id=" + <?php echo $Row['id'];?> +'&refferal_location_id='+<?php echo $Row['location_id']; ?> +'&txt_share_frnd='+email_str,
                                      async : false,
                                      success: function(msg) {
                                        
                                          close_popup('Notification<?php echo $Row['id'].$Row['location_id'];?>');  
                                             $("#shareloading<?php echo $Row['id'].$Row['location_id'];?>").hide();  
                                      }
                        });
		       
			return true;
		}
		else {
			$("#<?php echo $Row['id'].$Row['location_id'];?>").text(msg)
			 
			return false;
		}
		
                
	});
</script>
	  
	  <? 
			}
			
	  }	  ?>
	  </tbody>
            
	</table>
	<script>
	    function open_popup1(popup_name)
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
	</script>
	<?php
			
		
}
// merchant deal end filter
/**
 * @uses get filter my points
 * @param category
 * @used in pages :my-orders.php,mypoints.php,myreviews.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_filter_my_points']))
{
    	
        $cat_wh = "";
        if($_REQUEST['category']!=0)
        {
            $cat_wh = " and C.category_id=".$_REQUEST['category'];
        }
        else
        {
           $cat_wh=" and CAT.active=1";
        }
        $dt_w = "";
	
   
	//$Sql="select c.*,CAT.cat_name,sum(earned_reward) 'Redeem Points',sum(referral_reward) 'Sharing Points' from campaigns c,reward_user ru,categories CAT where c.category_id=CAT.id and CAT.active=1 and c.id=ru.campaign_id and ru.customer_id=".$_SESSION['customer_id']." ".$cat_wh." group by ru.campaign_id";
	
	
		$mlatitude=$_SESSION['mycurrent_lati'];
		$mlongitude=$_SESSION['mycurrent_long'];
		
		if(isset($_COOKIE['miles_cookie']))
		{
			$miles = $_COOKIE['miles_cookie'];
		}
		else
		{
			$miles = 50;
		}
		if(isset($_COOKIE['cat_remember']))
		{
			$category_id  = $_COOKIE['cat_remember'];
		}
		else
		{
			$category_id  = 0;
		}
		 $cat_wh = "";
        if( $category_id !=0)
        {
            $cat_wh = " and C.category_id=". $category_id ;
        }
        else
        {
            $cat_wh=" and CAT.active=1";
        }
		 if(isset($_COOKIE['zoominglevel']))
		{
			 $zooming_level = $_COOKIE['zoominglevel'];
		}
		else
		{
			 $zooming_level = 20;
		}
	   if($_COOKIE['searched_location'] == "")
	   {
		$curr_latitude = $_SESSION['customer_info']['curr_latitude'];
		$curr_longitude = $_SESSION['customer_info']['curr_longitude'];
	   }
	   else{
			 $curr_latitude = $_COOKIE['mycurrent_lati'];
			$curr_longitude = $_COOKIE['mycurrent_long'];
	   }
		 $Where_miles = "";
		if($curr_longitude != "")
		{
			$Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE ( 69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
		}
		else
		{
			
			$curr_latitude = $_COOKIE['mycurrent_lati'];
			$curr_longitude = $_COOKIE['mycurrent_long'];
			$Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE (69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
		}
		
	if($mlatitude!="" && $mlongitude!="")
	{
		$Sql="SELECT  sum(ru.earned_reward) 'Redeem Points',sum(ru.referral_reward) 'Sharing Points' ,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
      WHERE  l.id=cl.location_id and cl.campaign_id=C.id 
	  and cl.location_id in
 (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=".$_SESSION['customer_id']." and ss.subscribed_status=1 ".$Where_miles." ) AND CAT.active=1 and ru.customer_id =".$_SESSION['customer_id']." AND ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1 
      ".$cat_wh." GROUP BY ru.campaign_id , ru.location_id ORDER BY distance";
	}
	else
	{
        $Sql="SELECT  sum(ru.earned_reward) 'Redeem Points',sum(ru.referral_reward) 'Sharing Points' ,C.*, CAT.cat_name , ru.reward_date redeemed_date , l.id as location_id ,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude FROM campaigns C, reward_user ru, categories CAT , locations l,campaign_location cl
      WHERE  l.id=cl.location_id and cl.campaign_id=C.id AND CAT.active=1 and ru.customer_id =".$_SESSION['customer_id']." ".$cat_wh." AND ru.campaign_id = C.id AND CAT.id=C.category_id and ru.location_id = l.id and l.active = 1 
      GROUP BY ru.campaign_id , ru.location_id";
	}
        
		$RS = $objDB->Conn->Execute($Sql);
           
		?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0" style="padding:5px;float:left" id="example">
		<thead>
	 
		 </thead>
		 <tbody>
<?php
			while($Row = $RS->FetchRow()){
                           if($Row['Redeem Points']==0 && $Row['Sharing Points']==0)
                                    {
                                    }
                                    else
                                    {
                            ?>
	     
	  <tr class="tableDeal" style="height:200px;">
              
       <td class="businesslogo">
	     <?php
	     if($Row['business_logo']!="")
		{
		    $img_src=ASSETS_IMG."/m/campaign/".$Row['business_logo']; 
		}
		else 
		{
		    $img_src=ASSETS_IMG."/c/Merchant_Offer.png";
		}
	     //$img_src=WEB_PATH."/merchant/images/logo/".$Row->business_logo;
	     ?>
	     <img src="<?php echo $img_src;?>"  />
	     
	</td>
         <td align="left" class="locationdata" style="line-height:1.5;">
             <div class="gridloctitle">
                                   <?php
                                   if($Row['location_name']!="")
                                        {
                                            echo $Row['location_name'];
                                        } 
                                        else
                                        {
                                        // for business name 
                                                                $arr=file(WEB_PATH.'/process.php?getlocationbusinessname=yes&l_id='. $Row['location_id']);
                                                                                if(trim($arr[0]) == "")
                                                                                     {
                                                                                             unset($arr[0]);
                                                                                             $arr = array_values($arr);
                                                                                     }
                                                                                     $json = json_decode($arr[0]);
                                                                                $busines_name  = $json->bus_name;
                                                                                echo $busines_name;
                                                                // for business name 
                                        }
                                   ?>
                                   </div> 
             <div class="griddealtitle">
                        <?php 
                            echo $Row['title'];
                            
                        ?>
             </div>
             <div class="griddealtype">
                                                
                                          <?php
                                                if($Row['number_of_use']==1)
                                                {
                                                    if($Row['new_customer']==1)
                                                    {
                                                        echo "Limit : One Per Customer,Valid For New Customer Only";
                                                    }
                                                    else 
                                                    {
                                                        echo "Limit : One Per Customer";
                                                    }
                                                }
                                                elseif($Row['number_of_use']==2)
                                                    echo "Limit : One Per Customer Per Day";
                                                elseif($Row['number_of_use']==3)
                                                    echo "Limit : Earn Redemption Points On Every Visit";                
                                          ?>

              </div>
             <div class="griddealpoint">
                                            <div class="redeem_point">
                                              <p><?php echo $Row['Redeem Points']; ?></p>
					       <span> Redeemed Point</span>
                                            </div>
                                            <div class="referral_point">
						<p><?php echo $Row['Sharing Points']; ?></p>
						<span>Shared Point</span>
                                            </div>
                                           <div class="last_redeem_date">
                                          <p>
                                          <?php
												/*
													echo date("Y-m-d g:i:s A", strtotime($Row['expiration_date']));
												*/
												
												$my_cust_id=$_SESSION['customer_id'];
												$my_camp_id=$Row['id'];
												$my_camp_location_id=$Row['location_id'];
												
												$Sql = "select id as coupon_id from coupon_codes where customer_id='$my_cust_id' and customer_campaign_code='$my_camp_id' and location_id='$my_camp_location_id'";
												//echo $Sql."<hr>";
												$RS123 = $objDB->Conn->Execute($Sql);
												//echo $RS->RecordCount();
												//echo $RS->fields['coupon_id'];
												$Sql = "select max(redeem_date) as redeem_date from coupon_redeem where coupon_id=".$RS123->fields['coupon_id'];
												//echo $Sql."<hr>";
												$RS456 = $objDB->Conn->Execute($Sql);
												//echo $RS->RecordCount();
												echo date("Y-m-d g:i:s A", strtotime($RS456->fields['redeem_date']));
                        
                                        ?>
										 </p>
                                        <span>Last Redeem Date</span>
										 </div>
                                        </div>
	      <div class="Share" >Share the deal and Earn more Rewards
                              <? if($_SESSION['customer_id'] != ""){ ?>
                              <a href="javascript:void(0)" id="btnsharediv_<?php echo $Row['id']."-".$Row['location_id'];?>" >Go For It >></a> 
                              
                              <!--<input type="button" value="Go for it" id="btn_share_div" />-->
                              <? } else {?>
                              <a  href="<?php echo WEB_PATH."/register.php?url=".curPageURL();?>" id="btn_share_div<?php echo $Row->id;?>" >Go For It >></a> 
                              <!--<input type="submit" value="Go for it" id="btn_share_div" name="chk_for_share_campaign" />-->
                              <?php } ?>
                            </div>
	       <div class="sharing_icon_css">
                            <div class="p1">
                              <? if($_SESSION['customer_id'] != ""){ ?>
                              <a href="javascript:void(0)" id="btn_msg_div_<?php echo $Row['id']."-".$Row['location_id'];?>" ><img src="<?php echo ASSETS_IMG.'/c/msg.png'?>" name="msg_share" /></a>
                              <? } else {?>
                              <a  href="<?php echo WEB_PATH."/register.php?url=".curPageURL();?>" id="btn_msg_div" ><img src="<?php echo ASSETS_IMG.'/c/msg.png'?>" name="msg_share" /></a>
                              <?php } ?>
                            </div>
			     <?php
			        if($Row['business_logo']!="")
				{
				   // $img_src=WEB_PATH."/merchant/images/logo/".$RS[0]->business_logo; 
				    $fb_img_share = ASSETS_IMG."/m/campaign/".$Row['business_logo'];
				}
				else 
				{
				    $fb_img_share =ASSETS_IMG."/c/Merchant_Offer.png";
				}
				$share="true";
				$th_link = WEB_PATH."/register.php?campaign_id=".$Row['id']."&l_id=".$Row['location_id']."&share=$share";
			if($_SESSION['customer_id'] != ""){
				$th_link .= "&customer_id=".base64_encode($_SESSION['customer_id']);
				//echo $th_link; 
			}
			
			   
			  
					$title=urlencode($Row['title']);
					
					$url=urlencode($th_link);
					$summary=urlencode($Row['deal_detail_description']);
					$image=$fb_img_share;
					
					//$image="http://ia.media-imdb.com/images/M/MV5BMjEwOTA2MjMwMl5BMl5BanBnXkFtZTcwODc3MDgxOA@@._V1._SY314_CR3,0,214,314_.jpg";
					//echo $image; 
			?>
			<div class="p2">
			    <a onClick="window.open('https://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&p[images][0]=<?php echo $image;?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');" target="_parent" href="javascript: void(0)">
				<img src="<?php echo ASSETS_IMG.'/c/fb.png'?>" name="fbshare" />
			    </a>
			    </div>
			<div class="p3">
			          <iframe allowtransparency="true" frameborder="0" scrolling="no" data-url="<?php echo $th_link;?>"  src="https://platform.twitter.com/widgets/tweet_button.html?count=none&url=<?php echo urlencode($th_link);?>"
            style="width:55px; height:20px;"></iframe>
				      
                        </div>
			<div class="p4">
                             
				<div class="g-plusone" data-size="medium" data-annotation="none" data-href="<?php echo $th_link;?>"></div>
					
					<!-- Place this tag after the last +1 button tag. -->
					<script type="text/javascript">
					  (function() {
					    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					    po.src = 'https://apis.google.com/js/plusone.js';
					    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
					<!-- Place this tag where you want the +1 button to render. -->
                           </div>
	       
			
	       </div>
            <script>
        $("#btnsharediv_<?php echo $Row['id']."-".$Row['location_id'];?>").click(function(){
	          //alert("<?php echo $Row['id']."-".$Row['location_id'];?>");
		  open_popup1('Notification<?php echo $Row['id'].$Row['location_id'];?>');

	});    
        </script>
        </td>
<!--	<td class="categoryname" align="left"><?=$Row['cat_name']?></td>-->
         <?php
                
						   $from_lati=$_SESSION['mycurrent_lati'];
							
						 $from_long=$_SESSION['mycurrent_long'];
						
						$to_lati=$Row['latitude'];
						
						$to_long=$Row['longitude'];
                ?>
                <td align="left" class="distance">
                                   <div class="miles">
                                    <div class="expir_tooltip">
                                               
                                                </div>
                                       <span style="display:none;">
                                                    <?php 
                                                    echo date("Y-m-d g:i:s A", strtotime($Row['expiration_date']));
                                                    ?>
                                                </span>
                                                <?php 
                                                        if($from_lati!="" && from_long!="")
                                                                echo $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M")." Miles";
                                                        else
                                                                echo "-";
                                                ?>
                                       </div>
                                     <div class="gridlocaddr">
                                         <?php
                                                    if($from_lati!="" && from_long!="")
                                                    {
                                                        $maphref="https://maps.google.com/maps?saddr=&daddr=".$to_lati.",".$to_long;                                                   
                                                    ?>
                                                        <div class="div_getdirection">
                                                            <a href="<?php echo $maphref; ?>" target="_blank" style="color:#0066FF;">Get Direction</a>
                                                        </div>
                                                    <?php
                                                    
                                                    }
                                                ?>
                                                <?php $address = $Row['address'].", ".$Row['city'].", ".$Row['state'];
                                                        echo $address;	
                                                ?>
                                        </div>
                                         
                </td>
		
            </tr>
	  <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>PopUpContainer" class="container_popup"  style="display: none;">
 <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>BackDiv" class="divBack"></div>
    <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>FrontDivProcessing" class="Processing" style="display:none;">                                            
            <div id="Notification<?php echo $Row['id'].$Row['location_id'];?>MaindivLoading" align="center" valign="middle" class="imgDivLoading" style="left:32%;top: 11%;">
                <div class="modal-close-button" style="visibility: visible;"><a tabindex="0" onclick="close_popup('Notification<?php echo $Row['id'].$Row['location_id'];?>');" id="fancybox-close" style="display:inline;"></a></div>
                <div id="Notification<?php echo $Row->id.$Row->location_id;?>mainContainer" class="innerContainer" style="height:366px;width:448px;background:none;">
                    <div class="main_content">
                        <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                            <div class="campaign_detail_div" style="">
                                
                                    <div id="activatesahreDiv" class="div_share_friend" style="background-image:none;border:2px solid #FF8810;height:375px;text-align:left">
                                  
                                    <div class="head">
					<?php $img_src=ASSETS_IMG."/c/header-logo_new.png";?>
                                        <img src="<?php echo $img_src;?>" alt="Scanflip"/><br/>
                                        Powering Smart Saving from local Merchants
                                    </div>
                                    <div class="share_friends">Share With Friends</div>
                                    <div class="share_msg">To Share this offer with friends,complete the from below.</div>
                                    <div class="message_pop">
                                        <strong>Message :</strong><br/>
                                        <strong>I thought</strong> you might enjoy knowing this deals from Scanflip
                                    </div>        
                                   
                                    <div class="notice_mail">Specify up to 50 of your friend's email below separated by commas (,) or semicolons (;)</div>
                                      <p style="color:#FF0000;" id="<?php echo $Row['id'].$Row['location_id'];?>"></p>
                                    <div class="text_area"><textarea rows="5" cols="50"  name="txt_share_frnd" id="txt_share_frnd<?php echo $Row['id'].$Row['location_id'];?>"></textarea></div>
                                   
                                    <div class="buttons_bot">
                                       
                                        <input type="hidden" name="reffer_campaign_id1" id="reffer_campaign_id" value="<?=$_REQUEST['campaign_id']?>" />
                                        <input type="hidden" name="reffer_campaign_name1" id="reffer_campaign_name" value="<?php echo $camp_title; ?>" />
                                        <input type="hidden" name="refferal_location_id1" value="<?php echo $_REQUEST['l_id']; ?>" id="refferal_location_id" />
                                        <input type="button" class="btnsharegridbutton" value="Share" name="btn_share_grid" id="btnsharegridbutton<?php echo $Row['id']."-".$Row['location_id']; ?>" />
                                        <input type="button" value="Cancel" onclick="close_popup('Notification<?php echo $Row['id'].$Row['location_id'];?>');" id="btn_cancel"  />
					<?php $img_src=ASSETS_IMG."/c/ajax-offer-loader.gif";?>
					<img src="<?php echo $img_src; ?>" alt="" id="shareloading<?php echo $Row['id'].$Row['location_id'];?>" height="20px" width="20px" style="display:none"/>
                                    </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
  </div>
  <script>

 $("#btnsharegridbutton<?php echo $Row['id']."-".$Row['location_id']; ?>").click(function(){
		
		var email_str = $("#txt_share_frnd<?php echo $Row['id'].$Row['location_id'];?>").val();
              	var msg ="";
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
		        $("#shareloading<?php echo $Row['id'].$Row['location_id'];?>").show();
		       $.ajax({
                                      type: "POST",
                                      url: "<?=WEB_PATH?>/process.php",
                                      data: "btn_share_grid=yes&reffer_campaign_id=" + <?php echo $Row['id'];?> +'&refferal_location_id='+<?php echo $Row['location_id']; ?> +'&txt_share_frnd='+email_str,
                                      async : false,
                                      success: function(msg) {
                                        
                                          close_popup('Notification<?php echo $Row['id'].$Row['location_id'];?>');  
                                             $("#shareloading<?php echo $Row['id'].$Row['location_id'];?>").hide();  
                                      }
                        });
		       
			return true;
		}
		else {
			$("#<?php echo $Row['id'].$Row['location_id'];?>").text(msg)
			 
			return false;
		}
		
                
	});
</script>
	  
	  <?
         }	
                        }			 
	  ?>
	  </tbody>
</table>
<script>
	    function open_popup1(popup_name)
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
	</script>

<?php
}

//23-01-2013
/**
 * @uses get more merchant deals
 * @param hdn_campaign_id,hdn_location_id
 * @used in pages :my-orders.php,mypoints.php,myreviews.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getmoremercahntdeals']))
{
    $cid=$_REQUEST['hdn_campaign_id'];
    $lid=$_REQUEST['hdn_location_id'];
    
    /*$sql = "Select * from subscribed_stores where location_id=$lid and customer_id=".$_SESSION['customer_id']." and subscribed_status =1";
   
$RS_subscribed = $objDB->Conn->Execute($sql);*/
	$RS_subscribed = $objDB->Conn->Execute("Select * from subscribed_stores where location_id=? and customer_id=? and subscribed_status =?",array($lid,$_SESSION['customer_id'],1));
if($RS_subscribed->RecordCount() != 0)
{
     /*$Sql="select  c.* from campaigns c,campaign_location cl,locations l where 
l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and cl.location_id = ".$lid." and
 CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date  and cl.active=1 
and c.id not in ( select campaign_id from customer_campaigns where  customer_id = ".$_SESSION['customer_id']." and location_id=cl.location_id ) and c.id<>".$cid." and cl.offers_left>0  and cl.active=1";
    
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("select  c.* from campaigns c,campaign_location cl,locations l where 
l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and cl.location_id = ? and
 CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date  and cl.active=1 
and c.id not in ( select campaign_id from customer_campaigns where  customer_id =? and location_id=cl.location_id ) and c.id<>? and cl.offers_left>0  and cl.active=1",array($lid,$_SESSION['customer_id'],$cid));

                if($RS->RecordCount()==0)
                {
                    header("Location:search-deal.php");
                }
                else{
                    header("Location:mymerchants.php");
                }
}   
 else {
    header("Location:search-deal.php");
}
exit;                
}

/**
 * @uses update email address
 * @param confirm_emailaddress
 * @used in pages :my-emailsettings.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnUpdateemailaddress']))
{
	$_SESSION['customer_info']['emailaddress'] = $_REQUEST['confirm_emailaddress'];
    /*$sql ="Update customer_user set emailaddress='".$_REQUEST['confirm_emailaddress']."' where id=".$_SESSION['customer_id']." ";
	
    $objDB->Conn->Execute($sql);*/
	$objDBWrt->Conn->Execute("Update customer_user set emailaddress=? where id=?",array($_REQUEST['confirm_emailaddress'],$_SESSION['customer_id']));

    $_SESSION['msg_email_update'] = "Email address is updated";
    header("Location:".WEB_PATH."/my-emailsettings.php");
}

/**
 * @uses update email settings
 * @param rd_campaign_email,rd_subscribe_merchant_new_campaign,rd_subscribe_merchant_reserve_campaign
 * @used in pages :emailsettings.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnUpdateemailsettings']))
{
 
//    exit;
    /*$Sql = "Select * from customer_email_settings where customer_id= ".$_SESSION['customer_id'];
    $is_user_avialable = $objDB->Conn->Execute($Sql);*/
    $is_user_avialable = $objDB->Conn->Execute("Select * from customer_email_settings where customer_id=?",array($_SESSION['customer_id']));
    if($is_user_avialable->RecordCount() !=0)
    {
        /*$Sql = "Update customer_email_settings SET campaign_email=".$_REQUEST['rd_campaign_email']." ,subscribe_merchant_new_campaign=".$_REQUEST['rd_subscribe_merchant_new_campaign']." , subscribe_merchant_reserve_campaign =".$_REQUEST['rd_subscribe_merchant_reserve_campaign'].", merchant_radius=".$_REQUEST['txt_merchant_radius']." where customer_id= ".$_SESSION['customer_id'];
        
        $objDB->Conn->Execute($Sql);*/
	$objDBWrt->Conn->Execute("Update customer_email_settings SET campaign_email=? ,subscribe_merchant_new_campaign=? , subscribe_merchant_reserve_campaign =?, merchant_radius=? where customer_id=?",array($_REQUEST['rd_campaign_email'],$_REQUEST['rd_subscribe_merchant_new_campaign'],$_REQUEST['rd_subscribe_merchant_reserve_campaign'],$_REQUEST['txt_merchant_radius'],$_SESSION['customer_id']));

    }
    else{
        /*$Sql = "Insert into customer_email_settings values(0,".$_SESSION['customer_id'].",".$_REQUEST['rd_campaign_email'].",".$_REQUEST['rd_subscribe_merchant_new_campaign'].",". $_REQUEST['rd_subscribe_merchant_reserve_campaign'].",".$_REQUEST['txt_merchant_radius'].")";
        $objDB->Conn->Execute($Sql);*/
	$objDBWrt->Conn->Execute("Insert into customer_email_settings values(?,?,?,?,?,?)",array(0,$_SESSION['customer_id'],$_REQUEST['rd_campaign_email'],$_REQUEST['rd_subscribe_merchant_new_campaign'],$_REQUEST['rd_subscribe_merchant_reserve_campaign'],$_REQUEST['txt_merchant_radius']));
    }

   $_SESSION['msg_email_setting'] = "Email subscription services updated";
    header("Location:".WEB_PATH."/my-emailsettings.php");
}

/**
 * @uses update notifications settings
 * @param rdo_notification_setting
 * @used in pages :mynotification.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnUpdatenotificationsettings']))
{
	/*$Sql = "Update customer_user SET notification_setting=".$_REQUEST['rdo_notification_setting']." where id= ".$_SESSION['customer_id'];
	$objDB->Conn->Execute($Sql);*/
	$objDBWrt->Conn->Execute("Update customer_user SET notification_setting=? where id=?",array($_REQUEST['rdo_notification_setting'],$_SESSION['customer_id']));
	$_SESSION['msg_notification_setting'] = "Notification services updated";
    header("Location:".WEB_PATH."/mynotification.php");
}	
//timezone function
function timezoneoffsetstring($offset)
{
  
        return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
}
function getClosestTimezone($lat, $lng)
  {
    $diffs = array();
    foreach(DateTimeZone::listIdentifiers() as $timezoneID) {
      $timezone = new DateTimeZone($timezoneID);
      $location = $timezone->getLocation();
      $tLat = $location['latitude'];
      $tLng = $location['longitude'];
      $diffLat = abs($lat - $tLat);
      $diffLng = abs($lng - $tLng);
      $diff = $diffLat + $diffLng;
      $diffs[$timezoneID] = $diff;

    }
    $timezone = array_keys($diffs, min($diffs));


    return $timezone[0];

  }
//
/**
 * @uses check user email is valid or not
 * @param email
 * @used in pages :my-emailsettings.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['check_user_email_valid']))
{
    
        
	$array = $json_array = array();
	$array['emailaddress'] = $_REQUEST['email'];
	
	$RS = $objDB->Show("customer_user",$array);
	if($RS->RecordCount()>0){
		$json_array['status'] = "false";
		$json_array['message'] = "Email address already exists in the system";
		$json = json_encode($json_array);
		echo "false";
	}
	else
	{
	   echo "true";
	}
    
}

/**
 * @uses check deals of category
 * @param customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['checkbtnGetDealsOfCategory']))
{
//    echo "Here";
	 $json_array = array();
	 $records = array();
	 $customer_id=$_REQUEST['customer_id'];
	 $storeid=$_REQUEST['storeid'];
	 $date_f = date("Y-m-d H:i:s");
	 $category = $_REQUEST['categoryid'];
         if($category == 0)
         {
             $wh = "";
         }
         else
         {
             $wh= " AND c.category_id= ".$category." ";
         }

         if(isset($_REQUEST['customer_id']))
	 {
		// $date_wh = "AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','+00:00') BETWEEN CONVERT_TZ(t.start_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1)) AND CONVERT_TZ(t.expiration_date,'".CURR_TIMEZONE."',SUBSTR(t.timezone,1, POSITION(',' IN t.timezone)-1))";
		  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
	 	$customer_id = $_REQUEST['customer_id'];
		/*$Sql = "SELECT c.* ,l.timezone as  loc_timezone FROM campaign_location cl , campaigns c, locations l WHERE cl.location_id=  '".$storeid."' ".$wh."  AND cl.campaign_id = c.id AND (cl.campaign_id IN 
                    (Select cg.campaign_id from  campaign_groups cg , merchant_groups mg where  cg.group_id=mg.id and mg.location_id=".$storeid ."
                        and cg.group_id in(select ms.group_id from merchant_subscribs ms where ms.user_id=".$customer_id."))
                        OR cl.campaign_id IN (SELECT t.id  FROM `campaigns` t WHERE t.`level` =1 ) ) 
                  and l.id= cl.location_id ".$date_wh;*/
              ///  $Sql = "SELECT sl.* FROM locations sl WHERE id IN (
                //           SELECT cl.location_id FROM campaigns c,campaign_location cl WHERE (cl.num_activation_code - (select count(*) from coupon_codes where location_id=cl.campaign_id)) > 0 AND  cl.campaign_id = c.id and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")  ) and ".$Where;

		$RS = $objDB->Conn->Execute("SELECT c.* ,l.timezone as  loc_timezone FROM campaign_location cl , campaigns c, locations l WHERE cl.location_id=? ".$wh."  AND cl.campaign_id = c.id AND (cl.campaign_id IN 
                    (Select cg.campaign_id from  campaign_groups cg , merchant_groups mg where  cg.group_id=mg.id and mg.location_id=?
                        and cg.group_id in(select ms.group_id from merchant_subscribs ms where ms.user_id=?))
                        OR cl.campaign_id IN (SELECT t.id  FROM `campaigns` t WHERE t.`level` =1 ) ) 
                  and l.id= cl.location_id ".$date_wh,array($storeid,$storeid,$customer_id));
	 }
	 else
	 {
		    $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
		 /*$Sql = "SELECT c.*,l.timezone as  loc_timezone  FROM campaign_location cl , campaigns c , locations l WHERE  c.level = '1' and cl.location_id=  '".$storeid."' ".$wh." AND cl.campaign_id = c.id  and l.id= cl.location_id ".$date_wh;*/
		$RS = $objDB->Conn->Execute("SELECT c.*,l.timezone as  loc_timezone  FROM campaign_location cl , campaigns c , locations l WHERE  c.level = '1' and cl.location_id=? ".$wh." AND cl.campaign_id = c.id  and l.id= cl.location_id ".$date_wh,array($storeid,));	 
                 
        }
	
	 if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
			  if(isset($_REQUEST['customer_id']))
			  {
			  		// start to test valid deal
			  
					$array_where_camp2['campaign_id'] = $Row['id'];
					$array_where_camp2['customer_id'] = $customer_id;
					$array_where_camp2['location_id'] = $storeid;
					$RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
					//echo "reserve=".$RS_cust_camp->RecordCount()."-";
					
					$array_where_camp['campaign_id'] = $Row['id'];
					$array_where_camp['customer_id'] = $customer_id;
					$array_where_camp['referred_customer_id'] = 0;
					$array_where_camp['location_id'] = $storeid;
					$RS_camp = $objDB->Show("reward_user", $array_where_camp);
					
					$array_where_camp1['campaign_id'] = $Row['id'];
					$array_where_camp1['location_id'] = $storeid;
					$RS_camp1 = $objDB->Show("campaign_location", $array_where_camp1);
					
					if($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && $Row['number_of_use']==1)
					 {
					 }                       
					 elseif($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && ($Row['number_of_use']==2 || $Row['number_of_use']==3) && $RS_camp1->fields['offers_left']==0)
					 {
					 }
					 elseif($RS_cust_camp->RecordCount()==0 && $RS_camp->RecordCount()==0 && $RS_camp1->fields['offers_left']==0)
					 {        
					 }
					 else
					 {
						$records[$count] = get_field_value($Row);
						$count++;
					 }
					
					// end to test valid deal
			  }
			  else
			  {
				  	$records[$count] = get_field_value($Row);
		   			$count++;
			  }
			  
		   
		  }
		  // start to test valid deal count
		  $json_array['total_records'] = $count;
		  // end to test valid deal count
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	 exit();
}

/**
 * @uses check whether user profile is set or not
 * @param campaign_id,location_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['is_userprofileset']))
{ 
   
   if(isset($_REQUEST['reload']))
   {
		if(isset($_REQUEST['campaign_id']) && isset($_REQUEST['location_id']))
		{
			$url_perameter_2 = "#".rawurlencode("@".$_REQUEST['campaign_id']."|".$_REQUEST['location_id']."@");
		}
   }
   else
   {
		$url_perameter_2 ="";
   }
       if($_SESSION['customer_id'] == ""){
	   
        $json_array = array();
	$json_array['loginstatus'] = "false";
	
	 $json_array['status'] = "false";
	 $json_array['link'] = WEB_PATH."/register.php?url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
        		  $json_array['is_profileset'] = 1;
                          $json = json_encode($json_array);
                        echo $json;
	 exit();
	}
	else
	{
		
    $customer_id = $_SESSION['customer_id'];
      /* $cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>0 and  country <>""  and id='.$customer_id;//.$customer_id;
                  $RS_cust_data=$objDB->Conn->Execute($cust_sql);*/
$RS_cust_data=$objDB->Conn->Execute('select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>? and  country <>""  and id=?',array(0,$customer_id));
                   $is_profileset =  $RS_cust_data->RecordCount();
                   if($is_profileset == 0){
    $json_array = array();
	$json_array['loginstatus'] = "true";
                        $json_array['status'] = "false";
        		  $json_array['is_profileset'] = $is_profileset;
                          $json = json_encode($json_array);
                        echo $json;
                        exit();
                   }
                   else{
                     $json_array['status'] = "true";
        		  $json_array['is_profileset'] = 1;
                          $json = json_encode($json_array);
                        echo $json;
                        exit();  
                   }
	}
}
/******* shop & redeem check login or not ************/

/**
 * @uses check login or not for shop redeem
 * @param giftcardid
 * @used in pages :review-address.php,shop-redeem.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['shop_redeem_login_or_not']))
{
	$json_array = array();
	if(isset($_REQUEST['giftcardid']))
	{
	$url_perameter_2 = "?purl=".urlencode("@00|".$_REQUEST['giftcardid']."@");
	}
	if(isset($_SESSION['customer_id']))
	{
		$json_array['status'] = "true";
		
	}
	else
	{
		$json_array['status'] = "false";
		if(isset($_REQUEST['giftcardid']))
		{
		 $json_array['link'] = WEB_PATH."/register.php?url=".WEB_PATH."/shop-redeem.php".$url_perameter_2;
		 }
		 else{
			$json_array['link'] = WEB_PATH."/register.php?url=".urlencode($_SERVER['HTTP_REFERER']);
		 }
		 $json_array['hash_link'] = "#".urlencode("@00|".$_REQUEST['giftcardid']."@");
	}
	$json= json_encode($json_array);
	echo $json;
	exit();

}
/********* shop redeeem function **************/
/**
 * @uses reserve deal
 * @param giftcardid
 * @used in pages :location-detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnreservedeal']))
{
$objDB->Conn->StartTrans();
	$timestamp = $_REQUEST['timestamp'];
	
if(!isset($_SESSION['customer_id'])){

$json_array = array();
if(isset($_REQUEST['reload']))
{
$url_perameter_2 = "?purl=".urlencode("@".$_REQUEST['campaign_id']."|".$_REQUEST['location_id']."@");

}
else
{
$url_perameter_2 ="";
}
$json_array['loginstatus'] = "false";
if(isset($_REQUEST['r_url']))
{
if(isset($_REQUEST['is_location']))
{

$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".WEB_PATH."/search-deal.php".$url_perameter_2;
}
else
{
$json_array['link'] = WEB_PATH."/register.php?url=".WEB_PATH."/search-deal.php".$url_perameter_2;
}

}
else{
if(isset($_REQUEST['is_location']))
{

$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
}
else
{
$json_array['link'] = WEB_PATH."/register.php?url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
}
}
$json_array['is_profileset'] = 1;
$json = json_encode($json_array);

$objDB->Conn->CompleteTrans(); 
echo $json;
exit();
}
else
{

$_SESSION['msg']= "";
$json_array = array();
$json_array['is_profileset'] = 1;
$customer_id = $_SESSION['customer_id'];

$where_x = array();
$where_x['campaign_id'] = $_REQUEST['campaign_id'];
$CodeDetails = $objDB->Show("activation_codes", $where_x);
$activation_code = $CodeDetails->fields['activation_code'];
$campaign_id = $_REQUEST['campaign_id'];
$lid = $_REQUEST['location_id'];


 //$objDB->Conn->StartTrans();
 
/*$sql_o = "select * from campaign_location where campaign_id =".$campaign_id." and location_id =". $lid ." and active=1";
$RS_o = $objDB->Conn->Execute($sql_o);*/
$RS_o = $objDB->Conn->Execute("select * from campaign_location where campaign_id =? and location_id =? and active=1",array($campaign_id,$lid));
/*$Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=".$lid;
$RS = $objDB->Conn->Execute($Sql);*/

$RS = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id=?",array($customer_id,$campaign_id,$lid));

if($RS_o->RecordCount()>0){



if($RS->RecordCount()<=0){


/*$Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";

$RS_num_activation = $objDB->Conn->Execute($Sql_num_activation);*/
$RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =? ",array($campaign_id,$lid));

$offers_left = $RS_num_activation->fields['offers_left'];
$used_campaign = $RS_num_activation->fields['used_offers'];

$share_flag= 1;
if($offers_left > 0)
{

/*$Sql_max_is_walkin = "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=".$campaign_id;

$RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin);*/
$RS_max_is_walkin = $objDB->Conn->Execute( "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=?",array($campaign_id));
//    echo $RS_max_is_walkin->fields['new_customer']."=== new customer";
if($RS_max_is_walkin->fields['new_customer'] == 1)
{
/*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";

$subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
$subscibed_store_rs =$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));

if($subscibed_store_rs->RecordCount()==0)
{
$share_flag= 1;
}
else {
$share_flag= 0;
}
}

/* check whether new customer for this store */
$allow_for_reserve= 0;
$is_new_user= 0;
/*************** *************************/
/*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";

$Rs_is_new_customer=$objDB->Conn->Execute($sql_chk);*/
$Rs_is_new_customer=$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));

if($Rs_is_new_customer->RecordCount()==0)
{
$is_new_user= 1;
}
else {
$is_new_user= 0;
}
/*************** *************************/

if($is_new_user==0)  
{

/*$sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;

$RS_campaign_groups = $objDB->Conn->Execute($sql);*/
$RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$lid));

$c_g_str = "";
$cnt =1;

$is_it_in_group = 0;


if($RS_max_is_walkin->fields['level'] == 0)
{

if($RS_max_is_walkin->fields['is_walkin'] == 0)
{
if($RS_campaign_groups->RecordCount()>0)
{
while($Row_campaign = $RS_campaign_groups->FetchRow())
{ 
$c_g_str .= $Row_campaign['group_id'];
if($cnt != $RS_campaign_groups->RecordCount())
{
$c_g_str .= "," ;
}
$cnt++;
}
/*$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";

$RS_check_s = $objDB->Conn->Execute($Sql_new_); */          
$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )",array($_SESSION['customer_id']));     
         
while($Row_Check_Cust_group = $RS_check_s->FetchRow())
{
/*$query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

$RS_query = $objDB->Conn->Execute($query);*/
$RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (". $c_g_str.") ",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id']));

if($RS_query->RecordCount() > 0)
{

$is_it_in_group = 1;
}
}
if($is_it_in_group == 1 )
{ 
$allow_for_reserve = 1;  	
}
else 
{
$allow_for_reserve = 0;
}
}
else
{
$allow_for_reserve = 0;  	
}    
}
else
{
// $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
/*$query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";

$RS_all_user_group = $objDB->Conn->Execute($query);*/
$RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =1 ) ",array($_SESSION['customer_id'],$lid));

if($RS_all_user_group->RecordCount() > 0)
{
$allow_for_reserve = 1;
}
else
{
$allow_for_reserve = 0;  
}

}
}
else
{
//   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$lid."  )";
$allow_for_reserve= 1;
}
}else{
$allow_for_reserve= 1; 
}

/* for checking whether customer in campaign group */
if($share_flag== 1)
{
if($allow_for_reserve==1){
/*$Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= '".$timestamp."', coupon_generation_date='".$timestamp."',
customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
$objDB->Conn->Execute($Sql);*/
	$objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status=?, activation_code=?, activation_date=?, coupon_generation_date=?,customer_id=?, campaign_id=? , location_id=?",array(1,$activation_code,$timestamp,$timestamp,$customer_id,$campaign_id,$lid));

/*$update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$lid." ";
$objDB->Conn->Execute($update_num_activation);*/
	$objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=? and location_id =?",array(($offers_left-1),($used_campaign+1),$campaign_id,$lid));

//$RSLocation_nm  = $objDB->Conn->Execute("select * from locations where id =".$lid);
$RSLocation_nm  = $objDB->Conn->Execute("select * from locations where id =?",array($lid));

//$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$lid;
$br = $objJSON->generate_voucher_code($customer_id,$activation_code,$campaign_id,$RSLocation_nm->fields['location_name'],$lid);

$json_array['campaign_id'] = $campaign_id;
$json_array['location_id'] = $lid;

/*$select_coupon_code = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
$select_rs = $objDB->Conn->Execute($select_coupon_code);*/
$select_rs = $objDB->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?",array($customer_id,$campaign_id,$lid));

if($select_rs->RecordCount()<=0)
{
$array_ =array();
$array_['customer_id'] = $customer_id;
$array_['customer_campaign_code'] = $campaign_id;
$array_['coupon_code'] = $br;
$array_['active']=1;
$array_['location_id'] = $lid;
$array_['generated_date'] = $timestamp;
/*$insert_coupon_code = "Insert into coupon_codes set customer_id=".$customer_id." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$lid." , generated_date='".$timestamp."'";
$objDB->Conn->Execute($insert_coupon_code);*/
$objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=? , customer_campaign_code=? , coupon_code=? , active=1 , location_id=? , generated_date=?",array($customer_id,$campaign_id,$br,$lid,$timestamp));
}

//Make entry in subscribed_stre table for first time subscribe to loaction
/*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
$RS_group = $objDB->Conn->Execute($sql_group);*/
$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?",array($lid,1));

/*$sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
$subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
$subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?",array($_SESSION['customer_id'],$lid));
if($subscibed_store_rs->RecordCount()==0)
{
/*$insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
$objDB->Conn->Execute($insert_subscribed_store_sql);*/
$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=?",array($_SESSION['customer_id'],$lid,date('Y-m-d H:i:s'),1));
}
else {
if($subscibed_store_rs->fields['subscribed_status']==0)
{
/*$up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
$objDB->Conn->Execute($up_subscribed_store);*/
$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=?  where  customer_id=? and location_id=?",array(1,$_SESSION['customer_id'],$lid));
}
}
// If campaign is walking deal then make entry in coupon_codes table //

/*$RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
$check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);*/
$check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id =? and private = 1) and user_id =?",array($lid,$_SESSION['customer_id']));

if($check_subscribe->RecordCount()==0)
{
/*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
$RS_group = $objDB->Conn->Execute($sql_group);*/		
$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?",array($lid,1));				

if($RS_group->RecordCount()>0){
/*$sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;

$RS_user_group =$objDB->Conn->Execute($sql_user_group);*/
$RS_user_group =$objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));


if($RS_user_group->RecordCount()<=0)
{
/*$insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
$objDB->Conn->Execute($insert_sql);*/
	$objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id = ? , user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$_SESSION['customer_id']));
}
 //$objDB->Conn->CompleteTrans(); 
}
}

}
else{
 
/*$Sql = "select offers_left from campaign_location where campaign_id=".$campaign_id. " and location_id= ".$lid;
$o_left_rs = $objDB->Conn->Execute($Sql); */
$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid));
 
$o_left = $o_left_rs->fields['offers_left'];
$json_array['o_left'] = $o_left;
$json_array['status'] = "newuser";
$json_array['campaign_for_new_user'] = $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];
$_SESSION['campaign_for_new_user'] = $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];
$json_array['error_msg'] = $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];
$json = json_encode($json_array);
$objDB->Conn->CompleteTrans(); 
echo $json;
exit;
}
}
else{
/*$Sql = "select offers_left from campaign_location where campaign_id=".$campaign_id. " and location_id= ".$lid;
$o_left_rs = $objDB->Conn->Execute($Sql); */
$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=? ",array($campaign_id,$lid)); 

$o_left = $o_left_rs->fields['offers_left'];
$json_array['o_left'] = $o_left;
$json_array['status'] = "newuser";
$json_array['campaign_for_new_user'] =  $client_msg['search_deal']['New_Customers_Only'];
$json_array['error_msg'] =  $client_msg['search_deal']['New_Customers_Only'];
$_SESSION['campaign_for_new_user'] = $client_msg['search_deal']['New_Customers_Only'];
$json = json_encode($json_array);
$objDB->Conn->CompleteTrans(); 
echo $json;
exit;
}

}
else{
//$Sql = "select offers_left from campaign_location where campaign_id=".$campaign_id. " and location_id= ".$lid;
//$o_left_rs = $objDB->Conn->Execute($Sql); 
$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid)); 

$o_left = $o_left_rs->fields['offers_left'];
$json_array['o_left'] = $o_left;
$json_array['status'] = "ended";
$json_array['campaign_end_message'] = $client_msg['campaign']['Msg_offer_left_zero'];
$json_array['error_msg'] = $client_msg['campaign']['Msg_offer_left_zero'];
$_SESSION['campaign_end_message'] = $client_msg['campaign']['Msg_offer_left_zero'];
$json = json_encode($json_array);
$objDB->Conn->CompleteTrans(); 
echo $json;
exit;
}
}
else
{

//$br_sql = "Select coupon_code from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
//$br_rs =  $objDB->Conn->Execute($br_sql);
$br_rs =  $objDB->Conn->Execute("Select coupon_code from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=".$lid."  ",array($customer_id,$campaign_id));


$br = $br_rs->fields['coupon_code'];
if($RS->fields['activation_status'] == 0)
{
/*$Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
$RS_num_activation = $objDB->Conn->Execute($Sql_num_activation);*/
$RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?",array($campaign_id,$lid));

$offers_left = $RS_num_activation->fields['offers_left'];
$used_campaign = $RS_num_activation->fields['used_offers'];
$share_flag= 1;
if($offers_left > 0)
{
//$Sql_max_is_walkin = "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=".$campaign_id;
//
//$RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin);
$RS_max_is_walkin = $objDB->Conn->Execute( "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=?",array($campaign_id));
//    echo $RS_max_is_walkin->fields['new_customer']."=== new customer";
if($RS_max_is_walkin->fields['new_customer'] == 1)
{
/*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";

$subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
$subscibed_store_rs =$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));
if($subscibed_store_rs->RecordCount()==0)
{
$share_flag= 1;
}
else {
$share_flag= 0;
}
}
/* check whether new customer for this store */
$allow_for_reserve= 0;
$is_new_user= 0;
/*************** *************************/
/*$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";

$Rs_is_new_customer=$objDB->Conn->Execute($sql_chk);*/
$Rs_is_new_customer=$objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ",array($_SESSION['customer_id'],$lid));

if($Rs_is_new_customer->RecordCount()==0)
{
$is_new_user= 1;
}
else {
$is_new_user= 0;
}
/*************** *************************/

if($is_new_user==0)  
{
/*$sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;

$RS_campaign_groups = $objDB->Conn->Execute($sql);*/
$RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?",array($campaign_id,$lid));

$c_g_str = "";
$cnt =1;

$is_it_in_group = 0;
if($RS_max_is_walkin->fields['level'] == 0)
{
if($RS_max_is_walkin->fields['is_walkin'] == 0)
{ 
if($RS_campaign_groups->RecordCount()>0)
{
while($Row_campaign = $RS_campaign_groups->FetchRow())
{ 
$c_g_str = $Row_campaign['group_id'];
if($cnt != $RS_campaign_groups->RecordCount())
{
$c_g_str .= "," ;
}
}
/*$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";

$RS_check_s = $objDB->Conn->Execute($Sql_new_);   */

$RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )",array($_SESSION['customer_id']));                     
while($Row_Check_Cust_group = $RS_check_s->FetchRow())
{
/*$query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

$RS_query = $objDB->Conn->Execute($query);*/
$RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (". $c_g_str.") ",array($_SESSION['customer_id'],$Row_Check_Cust_group['group_id']));

if($RS_query->RecordCount() > 0)
{

$is_it_in_group = 1;
}
}
if($is_it_in_group == 1 )
{ 
$allow_for_reserve = 1;  	
}
else {
$allow_for_reserve = 0;
}
}
else{
$allow_for_reserve = 0;  	
}

}
else{
// $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
/*$query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";

$RS_all_user_group = $objDB->Conn->Execute($query);*/
$RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =1 ) ",array($_SESSION['customer_id'],$lid));

if($RS_all_user_group->RecordCount() > 0)
{
$allow_for_reserve = 1;
}
else
{
$allow_for_reserve = 0;  
}

}
}
else
{
//   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
$Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$lid."  )";
$allow_for_reserve= 1;
}
}else{
$allow_for_reserve= 1; 
}

/* for checking whether customer in campaign group */
if($share_flag== 1)
{
if($allow_for_reserve==1){
//$Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
//customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
/*$Sql = "Update customer_campaigns SET activation_status=1 where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$lid;

$objDB->Conn->Execute($Sql);*/
$objDBWrt->Conn->Execute("Update customer_campaigns SET activation_status=? where customer_id=? and campaign_id=? and location_id=?",array(1,$customer_id,$campaign_id,$lid));

$json_array['campaign_id'] = $campaign_id;
$json_array['location_id'] = $lid;


//
/*$br_sql = "Select coupon_code from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
$br_rs =  $objDB->Conn->Execute($br_sql);*/
$br_rs =  $objDB->Conn->Execute("Select coupon_code from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?",array($customer_id,$campaign_id,$lid));

$br = $br_rs->fields['coupon_code'];
/*$select_coupon_code = "update coupon_codes set active= 1 where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
$objDB->Conn->Execute($select_coupon_code);*/
$objDBWrt->Conn->Execute("update coupon_codes set active= ? where customer_id=? and customer_campaign_code=? and location_id=?",array(1,$customer_id,$campaign_id,$lid));

///
//
//Make entry in subscribed_stre table for first time subscribe to loaction
/*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
$RS_group = $objDB->Conn->Execute($sql_group);*/
$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?",array($lid,1));

/*$sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
$subscibed_store_rs =$objDB->Conn->Execute($sql_chk);*/
$subscibed_store_rs =$objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?",array($_SESSION['customer_id'],$lid));

if($subscibed_store_rs->RecordCount()==0)
{
/*$insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
$objDB->Conn->Execute($insert_subscribed_store_sql);*/
$objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=?",array($_SESSION['customer_id'],$lid,date('Y-m-d H:i:s'),1));
}
else {
if($subscibed_store_rs->fields['subscribed_status']==0)
{
/*$up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
$objDB->Conn->Execute($up_subscribed_store);*/
$objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=?  where  customer_id=? and location_id=?",array(1,$_SESSION['customer_id'],$lid));
}
}
// If campaign is walking deal then make entry in coupon_codes table //

/*$RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
$check_subscribe = $objDB->Conn->Execute($RS_user_subscribe);*/
$check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id =? and private = ?) and user_id =?",array($lid,1,$_SESSION['customer_id']));

if($check_subscribe->RecordCount()==0)
{
/*$sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
$RS_group = $objDB->Conn->Execute($sql_group);*/
$RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?",array($lid,1));		

if($RS_group->RecordCount()>0){
/*$sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;

$RS_user_group =$objDB->Conn->Execute($sql_user_group);*/
$RS_user_group =$objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id =?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$customer_id));		

if($RS_user_group->RecordCount()<=0)
{
/*$insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
$objDB->Conn->Execute($insert_sql);*/
$objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id =? , user_id = ?",array($RS_group->fields['merchant_id'],$RS_group->fields['id'],$_SESSION['customer_id']));
}
  //$objDB->Conn->CompleteTrans(); 
}
}
//
}
else{
/*$Sql = "select offers_left from campaign_location where campaign_id=".$campaign_id. " and location_id= ".$lid;
$o_left_rs = $objDB->Conn->Execute($Sql); */
$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid));

$o_left = $o_left_rs->fields['offers_left'];
$json_array['o_left'] = $o_left;
$json_array['status'] = "newuser";
$json_array['campaign_for_new_user'] = $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];
$_SESSION['campaign_for_new_user'] = $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];
$json_array['error_msg'] = $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];
$json = json_encode($json_array);
$objDB->Conn->CompleteTrans(); 
echo $json;
exit;
}
}
else{
/*$Sql = "select offers_left from campaign_location where campaign_id=".$campaign_id. " and location_id= ".$lid;
$o_left_rs = $objDB->Conn->Execute($Sql); */
$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid));

$o_left = $o_left_rs->fields['offers_left'];
$json_array['o_left'] = $o_left;
$json_array['status'] = "newuser";
$json_array['campaign_for_new_user'] = $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];
$json_array['error_msg'] =  $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];
$_SESSION['campaign_for_new_user'] = $client_msg['campaign']['Msg_This_Offer_Limited_Customers'];
$json = json_encode($json_array);
$objDB->Conn->CompleteTrans(); 
echo $json;
exit;
}

}
else{
/*$Sql = "select offers_left from campaign_location where campaign_id=".$campaign_id. " and location_id= ".$lid;
$o_left_rs = $objDB->Conn->Execute($Sql); */
$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid)); 

$o_left = $o_left_rs->fields['offers_left'];
$json_array['o_left'] = $o_left;
$json_array['status'] = "ended";
$json_array['campaign_end_message'] = $client_msg['campaign']['Msg_offer_left_zero'];
$json_array['error_msg'] = $client_msg['campaign']['Msg_offer_left_zero'];
$_SESSION['campaign_end_message'] = $client_msg['campaign']['Msg_offer_left_zero'];
$json = json_encode($json_array);
$objDB->Conn->CompleteTrans(); 
echo $json;
exit;
}
}
/*$Sql = "select offers_left from campaign_location where campaign_id=".$campaign_id. " and location_id= ".$lid;
$o_left_rs = $objDB->Conn->Execute($Sql); */
$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid)); 

$o_left = $o_left_rs->fields['offers_left'];
$json_array['o_left'] = $o_left;
$json_array['barcode'] = WEB_PATH."/showbarcode.php?br=".$br;

$json_array['campaign_id'] = $campaign_id;
$json_array['location_id'] = $lid;
$json_array['status'] = "true";
$json = json_encode($json_array);
$objDB->Conn->CompleteTrans(); 
echo $json;
exit;
}
}else
{
/*$Sql = "select offers_left from campaign_location where campaign_id=".$campaign_id. " and location_id= ".$lid;
$o_left_rs = $objDB->Conn->Execute($Sql); */
$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id= ?",array($campaign_id,$lid)); 

$o_left = $o_left_rs->fields['offers_left'];
$json_array['o_left'] = $o_left;
$json_array['status'] = "ended";
$json_array['campaign_end_message'] = $client_msg['campaign']['Msg_offer_left_zero'];
$json_array['error_msg'] = $client_msg['campaign']['Msg_offer_left_zero'];
$_SESSION['campaign_end_message'] = $client_msg['campaign']['Msg_offer_left_zero'];
$json = json_encode($json_array);
$objDB->Conn->CompleteTrans(); 
echo $json;
exit;
}
/*$Sql = "select offers_left from campaign_location where campaign_id=".$campaign_id. " and location_id= ".$lid;
$o_left_rs = $objDB->Conn->Execute($Sql); */
$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($campaign_id,$lid)); 

$o_left = $o_left_rs->fields['offers_left'];
$json_array['o_left'] = $o_left;
$json_array['barcode'] = WEB_PATH."/showbarcode.php?br=".$br;

$json_array['status'] = "true";
$json_array['loginstatus'] = "true";
$json = json_encode($json_array);
$objDB->Conn->CompleteTrans(); 
echo $json;
exit;
}

}

/*** changes web services fo loation detail *****/

/**
 * @uses get marker for locations for location detail
 * @param merchant_id
 * @used in pages :location-detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetmarkerforLocations_locationdetail']))
{
         $merchantid = $_REQUEST['merchant_id'];
	 $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 $category_id=$_REQUEST['category_id'];
	 $dismile=$_REQUEST['dismile'];
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	 if(isset($_REQUEST['dismile']))
	 {
		
	 	$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
          }
		 /*
   //$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
        */
		if($_REQUEST['customer_id']!="")
		 {
			  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";
		 }
		 else
		 {
			 $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
		 }
		 if(isset($_REQUEST['firstlimit']) && isset($_REQUEST['lastlimit']))
		 {
			 if($_REQUEST['firstlimit']=="" && $_REQUEST['lastlimit']=="")
			 {
				 $firstlimit=0;
				 $lastlimit=9;
			 }
			 else
			 {
				 $firstlimit=$_REQUEST['firstlimit'];
				 $lastlimit=$_REQUEST['lastlimit'];
			 }
         }
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) ";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) ";
		// 12-8-2013
		
		// 02-10-2013 dist list deal display if cust in dist list and reserved
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id  in ( select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id and activation_status=1  and cl.campaign_type=3)  )
) ";
		// 02-10-2013
		
		// 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , in this private deal also display if not subscribed
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id.") or c.level =1 ) ";
		// 03-10-2013
		
		// 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private=1 and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";

		 }
	 }
         $limit_data = "SELECT cl.permalink,group_concat(CAST(c.id AS CHAR(120)) separator ',') all_campaigns_id ,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=".$merchantid." and    l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh."  ORDER BY distance,locid";
		
		// 05-10-2013 apply miles filter
		
		$limit_data = "SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=".$merchantid." and    l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where ."  ORDER BY distance,locid";
		
		// 05-10-2013 apply miles filter
		
		// for improve performance
		$limit_data = "SELECT cl.permalink,l.*,l.id locid ,group_concat(CAST(c.id AS CHAR(120)) separator ',') all_campaigns_id
		,c.id cid ,mu.menu_price_display , mu.menu_price_title ,mu.business , mu.business_tags , mu.aboutus_short,mu.merchant_icon ,mu.location_detail_display , mu.location_detail_title , 
		mu.aboutus
			,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id = l.created_by
        WHERE  l.created_by=".$merchantid." and    l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where ." group by locid  ORDER BY distance,locid";

         $RS_limit_data=$objDB->Conn->Execute("SELECT cl.permalink,l.*,l.id locid ,group_concat(CAST(c.id AS CHAR(120)) separator ',') all_campaigns_id
		,c.id cid ,mu.menu_price_display , mu.menu_price_title ,mu.business , mu.business_tags , mu.aboutus_short,mu.merchant_icon ,mu.location_detail_display , mu.location_detail_title , 
		mu.aboutus
			,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id = l.created_by
        WHERE  l.created_by=? and    l.active = ?  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where ." group by locid  ORDER BY distance,locid",array($merchantid,1));
		 

         if($RS_limit_data->RecordCount()>0)
	 {
              $json_array['query']= $limit_data;
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS_limit_data->RecordCount();
		  $count=0;
		  while($Row = $RS_limit_data->FetchRow())
		  {
		  
			/******** check for private & reserve  data ******/
			$count12=0;
					$campaignlist = $Row["all_campaigns_id"];
					$cmapignlist_array = explode(",",$campaignlist);
					//print_r($cmapignlist_array);
					$locationwise_campaign_array = array();
					for($cnti=0;$cnti<count($cmapignlist_array);$cnti++)
					{
						if(isset($_SESSION['customer_id']))
						{	
								/***********/
							$storeid = $Row['locid'];
								 $array_where_camp2['campaign_id'] = $cmapignlist_array[$cnti];
							$array_where_camp2['customer_id'] = $_SESSION['customer_id'];
							$array_where_camp2['location_id'] = $storeid;
							$RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
							//echo $RS_cust_camp->RecordCount()."-";
							$reserved=$RS_cust_camp->RecordCount();
							
							
							$array_where_camp['campaign_id'] = $cmapignlist_array[$cnti];
							$array_where_camp['customer_id'] = $_SESSION['customer_id'];
							$array_where_camp['referred_customer_id'] = 0;
							$array_where_camp['location_id'] = $storeid;
							$RS_camp = $objDB->Show("reward_user", $array_where_camp);
							//echo $RS_camp->RecordCount()."-";
							//echo "bb".$Row->number_of_use."-";
							$redeemed=$RS_camp->RecordCount();
							
							$array_where_camp1['campaign_id'] = $cmapignlist_array[$cnti];
							$array_where_camp1['location_id'] = $storeid;
							$RS_camp1 = $objDB->Show("campaign_location", $array_where_camp1);
							//echo $RS_camp1->fields['offers_left']."-";
							$offer_left=$RS_camp1->fields['offers_left'];
								
								if($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && $Row['number_of_use']==1)
								 {
								 }                       
								 else if($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && ($Row['number_of_use']==2 || $Row['number_of_use']==3) && $RS_camp1->fields['offers_left']==0)
								 {
									 
								 }
								 else if($RS_cust_camp->RecordCount()==0 && $RS_camp->RecordCount()==0 && $RS_camp1->fields['offers_left']==0)
								 {   
									 
								 }
								else
								{
									
									$count12++;
								}
									
							}
							else
							{
								$count12++;
							}
						} 
			/******** check for r privete & reserve data ********/
			if($count12 !=0 )
			{
				$records[$Row['locid']] = get_field_value($Row);
			}
		   	$count++;
		  }
		  $json_array["records"]= $records;
                  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records_all[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["marker_records"]= $records_all;
                    $json_array['marker_total_records'] = $RS_limit_data->RecordCount();
                     $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 else
	 {
               $json_array['query']= $limit_data;
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
                   $json_array["records"]="";
		  $json_array["marker_records"]= "";
                    $json_array['marker_total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	exit();
}

/**
 * @uses get campaignlist to location
 * @param mlatitude,mlongitude
 * @used in pages :location-detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getcampaignlistoflocation']))
{
	$json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	  $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	  $date_f = date("Y-m-d H:i:s");
	
		 /*
   //$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
        */
		if($_REQUEST['customer_id']!="")
		 {
			  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";
		 }
		 else
		 {
			 $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
		 }
		
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) ";
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id  in ( select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id and activation_status=1  and cl.campaign_type=3)  )
) ";
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id.") or c.level =1 ) ";
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private=1 and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";

		 }
	 }
        
		// for improve performance
		/*$limit_data = "SELECT cl.permalink,l.location_name,c.*,l.id locid ,c.id cid,l.timezone timezone
		,mu.business ,l.created_by l_created_by , mu.merchant_icon , l.location_name , l.address, l.city , l.state , l.zip 
		,l.country , l.picture ,cl.num_activation_code , cl.offers_left, cl.active ,cl.campaign_type
		,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance 
		FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id = l.created_by
        WHERE  l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and cl.location_id=".$_REQUEST['locid']." ORDER BY c.id";
		
		 $RS_limit_data=$objDB->Conn->Execute($limit_data);*/
$RS_limit_data=$objDB->Conn->Execute("SELECT cl.permalink,l.location_name,c.*,l.id locid ,c.id cid,l.timezone timezone
		,mu.business ,l.created_by l_created_by , mu.merchant_icon , l.location_name , l.address, l.city , l.state , l.zip 
		,l.country , l.latitude, l.longitude, l.picture ,cl.num_activation_code , cl.offers_left, cl.active ,cl.campaign_type
		,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance 
		FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id = l.created_by
        WHERE  l.active =?  ".$cust_where." ".$cat_str  ."  ".$date_wh." and cl.location_id=? ORDER BY c.id",array(1,$_REQUEST['locid']));

		 $is_display = false;
		 $DetailHtml ="";
		 $loc_arr = array();
		 $ct =0;
		 //if($RS_limit_data->RecordCount() !=0) {
		  while($Row = $RS_limit_data->FetchRow())
		  {
						$storeid = $Row['locid'];
                       $reserved= 0;
                       $redeemed= 0;
                       $offer_left=0;
                    $array_where_camp1['campaign_id'] = $Row['cid'];
                    $array_where_camp1['location_id'] = $storeid;
                    $RS_camp1 = $objDB->Show("campaign_location", $array_where_camp1);
                    //echo $RS_camp1->fields['offers_left']."-";
                    $offer_left=$RS_camp1->fields['offers_left'];
                    if(isset($_SESSION['customer_id']))
                    {
                         $array_where_camp2['campaign_id'] = $Row['cid'];
                    $array_where_camp2['customer_id'] = $_SESSION['customer_id'];
                    $array_where_camp2['location_id'] = $storeid;
					//06-09-2013
					$array_where_camp2['activation_status'] = 1;
					//06-09-2013
                    $RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
                    //echo $RS_cust_camp->RecordCount()."-";
                    $reserved=$RS_cust_camp->RecordCount();
                    
                    
                    $array_where_camp['campaign_id'] =$Row['cid'];
                    $array_where_camp['customer_id'] = $_SESSION['customer_id'];
                    $array_where_camp['referred_customer_id'] = 0;
                    $array_where_camp['location_id'] = $storeid;
                    $RS_camp = $objDB->Show("reward_user", $array_where_camp);
                    //echo $RS_camp->RecordCount()."-";
                    //echo "bb".$Row->number_of_use."-";
                    $redeemed=$RS_camp->RecordCount();
                    
                   /*
				   if($_SESSION['customer_id']==249 && $Row->cid==972 && $storeid==70)
					{
						echo "number of use = ".$Row->number_of_use."<br/>";
						echo "offer left = ".$RS_camp1->fields['offers_left']."<br/>";
						echo "reserved = ".$reserved."<br/>";
						echo "redeemed = ".$redeemed."<br/>";
					}
					*/
                    
                        
                        if($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && $Row['number_of_use']==1)
                         {
                            //echo "1 ".$Row->cid." ".$storeid;
                         }                       
                         elseif($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && ($Row['number_of_use']==2 || $Row['number_of_use']==3) && $RS_camp1->fields['offers_left']==0)
                         {
                             //echo "2 ".$Row->cid." ".$storeid;
                         }
                         elseif($RS_cust_camp->RecordCount()==0 && $RS_camp->RecordCount()==0 && $RS_camp1->fields['offers_left']==0)
                         {   
                             //echo "3 ".$Row->cid." ".$storeid;
                         }
                         else
                         {
                              $new_div_start ="";
                              
                    if(!in_array($Row['locid'] ,$loc_arr)){
                     
						$ct="";
                        if($ct != 0){
                            $new_div_start ="<div id='locationarea_".$Row['locid']."' >";
                        }  
                        else{
                             $new_div_start ="<div id='locationarea_".$Row['locid']."' >";
                        }
                        $ct = $ct +1;
                                         array_push($loc_arr,$Row['locid'] );
                 
                    }
					          //$CampTitle = substr($Row->title,0,45);
							  $CampTitle = $Row['title'];
												  $CampId =$Row['cid'];
                                                                                                        // location name changes start
                                                 
        
	
	if($Row['merchant_icon']!="")
								{
								    $img_src=ASSETS_IMG."/m/icon/".$Row['merchant_icon']; 
								}
							       
								else 
								{
								    $img_src=ASSETS_IMG."/c/Merchant.png";
								}
	
        $mer_ikon_img="<div class='mer_ikon_img'><img src='".$img_src."' /></div>";
      
	  
		 
           
			$busines_name  = $Row['business'];
            if($Row['location_name']!="")
            {
                $businessname="<a target='_parent' title='Merchant Location' href='".WEB_PATH."/location_detail.php?id=".$Row['locid']."'>".$Row['location_name']."-".$Row['locid']."</a>";
            }
            else 
            {
               $businessname ="<a target='_parent' title='Merchant Location' href='".WEB_PATH."/location_detail.php?id=".$Row['locid']."'>".$busines_name ."</a>"; 
            }
            $businessname ="<div class='buss_rating'><a target='_parent' title='Merchant Location' href='javascript:void(0)'>".$busines_name ."</a>"; 
			$businessname.="</div>";
                
 
													$storeid = $Row['locid'];
                                                                                                        $CampId = $Row['cid'];
                                                                                                        $cmpdis = $Row['discount'];
                                                                                                        
                                                                                                        $array_where_cat['id'] = $Row['category_id'];
                                                                                                        $RS_Cat = $objDB->Show("categories", $array_where_cat);
                                                                                                       $RS_Cat_image="<img class='kat_img' align='left' src='".ASSETS_IMG.$RS_Cat->fields['cat_image']."' title='".$RS_Cat->fields['cat_name']."' />";
                                                                                                        if($reserved>0)
                                                                                                        {
                                                                                                            $where_x = array();
                                                                                                            $where_x['campaign_id'] = $CampId;
                                                                                                            $CodeDetails = $objDB->Show("activation_codes", $where_x);

                                                                                                            $barcodea = $_SESSION['customer_id'].substr($CodeDetails->fields['activation_code'],0,2).$CampId.substr($Row['location_name'],0,2).$storeid;
                                                                                                            $viewbtn=$RS_Cat_image;
                                                                                                            $viewbtn .="<img title='Print' class='btn_print1' cid='".$CampId."' lid='".$storeid."' barcodea='".$barcodea."' align='left' style='margin:5px 0 0 10px;' src='".ASSETS_IMG."/c/icon-print-deal2-black.png' />";
                                                                                                            //$viewbtn .= '<a class="view" href="campaign.php?campaign_id='.$Row->cid.'&l_id='.$storeid.'" target= "_parent">View</a>&nbsp';
																											$viewbtn .= '<a class="view" href="'.$Row['permalink'].'" target= "_parent">View</a>&nbsp';
                                                                                                             $viewbtn .= '<a href="javascript:void(0)" class="btn_unreserve" level="'.$Row['level'].'" u_campid="'.$Row['cid'].'" u_locid="'.$storeid.'" >Unreserve</a>&nbsp';
                                                                                                        }   
                                                                                                        else {
                                                                                                             $viewbtn=$RS_Cat_image;
                                                                                                              $viewbtn .= '<a class="view-reserve"  reserve="'.$reserved.'" redeem="'.$redeemed.'" offer_left="'.$offer_left.'" href="'.$Row['permalink'].'" target= "_parent">View And Reserve</a>';          
                                                                                                        }
                                                                                                       
                                                                              //popup page send detail
							       
							       $number_of_use=$Row['number_of_use'];
							       $new_customer=$Row['new_customer'];
							       
							       $address = $Row['address'];
							       $city = $Row['city'];
							       $state = $Row['state'];
							       $zip = $Row['zip'];
							       $country = $Row['country'];
							       
							        
								$max_coupon = $Row['num_activation_code'];
								$o_left = $Row['offers_left'];
								$is_active =  $Row['active'];
							       
							       
							       $redeem_rewards=$Row['redeem_rewards'];
							       $referral_rewards=$Row['referral_rewards'];
								   
								   // start check for sharing limitaion if 0 then sharing point display 0
								   if($Row['referral_rewards'] != "")
								   {
										 
										 /*$Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$CampId." and referred_customer_id<>0";
										 $RS_shared = $objDB->Conn->Execute($Sql_shared);*/
										$RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?",array($CampId,0));
													
										 if($RS_shared->RecordCount() >= $Row['max_no_sharing'] )
										 {
											 $referral_rewards=0;
										 }
										 else
										 {
											 $referral_rewards=$Row['referral_rewards'];
										 }
									}
								   // end check for sharing limitaion if 0 then sharing point display 0
								   
								   
							       $expiration_date = date("Y-m-d g:i:s A", strtotime($Row['expiration_date'] ));
							       
							        if($Row['business_logo']!="")
								{
								    $img_src=ASSETS_IMG."/m/campaign/block/".$Row['business_logo']; 
								}
							       
								else 
								{
								    $img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
								}
                        
                      
							       $popup_businessname=$Row['location_name'];
							       $deal_desc= $Row['deal_detail_description'];
							       $businessname_popup=$Row['location_name'];
							       
							       //End Popup page
							             $disply_css ="";
										 
										 // 07 10 2013 not apply category filter if user login
										// 07 10 2013 not apply category filter if user login
										 
                                                                 $visited = 0;
								    if($_SESSION['customer_id']== "")
								  {
								      $c = 0;
								  }
								  else{
								      $c = $_SESSION['customer_id'];
                                                                        /*$sql = "select * from customer_campaign_view where customer_id=".$_SESSION['customer_id']." and campaign_id =".$CampId;
     $RS = $objDB->Conn->Execute($sql);*/
$RS = $objDB->Conn->Execute("select * from customer_campaign_view where customer_id=? and campaign_id =?",array($_SESSION['customer_id'],$CampId));
     $visited = 0;
     if($RS->RecordCount() > 0)
     {
         $visited = 1;
     }
								  }
/*                                      $barcode_sql = "select coupon_code from coupon_codes where customer_campaign_code =".$CampId." AND location_id=".$storeid." and customer_id=".$_SESSION['customer_id'];
                                      $barcode_rs = $objDB->Conn->Execute($barcode_sql);*/
$barcode_rs = $objDB->Conn->Execute("select coupon_code from coupon_codes where customer_campaign_code =? AND location_id=? and customer_id=?",array($CampId,$storeid,$_SESSION['customer_id']));

								if($barcode_rs->fields['coupon_code'] == "")
                                                                {
                                                                    $barcode = "sd";
                                                                }
                                                                else{
                                                                     $barcode = $barcode_rs->fields['coupon_code'];
                                                                }
                                                                $campaign_tag=$Row['campaign_tag'];
									 $vi  = $CampId."-".$storeid."-".$Row['number_of_use']."-".$o_left;   
									 $popupurl=WEB_PATH."/popup_for_mymerchant.php?CampTitle=".urlencode($CampTitle)."&businessname=".urlencode($businessname_popup)."&number_of_use=".$number_of_use."&new_customer=".$new_customer."&address=".urlencode($address)."&city=".urlencode($city)."&state=".urlencode($state)."&country=".urlencode($country)."&zip=".$zip."&redeem_rewards=".$redeem_rewards."&referral_rewards=".$referral_rewards."&o_left=".$o_left."&expiration_date=".date("m/d/y g:i A", strtotime($expiration_date))."&img_src=".$img_src."&campid=".$CampId."&locationid=".$storeid."&deal_desc=".urlencode($deal_desc)."&is_reserve=".$reserved."&br=".$barcode."&cust_id=".$c."&decoded_cust_id=".base64_encode($_SESSION['customer_id'])."&webpath=".urlencode(WEB_PATH)."&is_mydeals=".$reserved."&voucher_id=".$vi."&visited=".$visited."&campaign_tag=".$campaign_tag;  
									 
        
      $campdiscount = '<div class="offersexyellow">';
	  
	  if($Row['is_new']==1)
		 {
			$campdiscount.="<div class='new_24_hour_deal'><p>NEW</p></div>";
		 }
		 
	$campdiscount .="<div class='grid_img_div'>";

	$campdiscount .="<img  src='".$img_src."'>";

	$campdiscount .="</div>";
	
	$campaign_value=$Row['deal_value'];
	$campaign_discount=$Row['discount'];
	$campaign_saving=$Row['saving'];
	
	$campdiscount.='<div class="dealmetric_wrap">';
	
	if($campaign_value!="0")
	{	
		$campdiscount.='<div class="dealmetric title">
		 <div class="value">Value</div>
		 <div class="discount">Discount</div>
		 <div class="saving">Savings</div>
		</div>

		<div class="dealmetric values">
		 <div class="value">$'.$campaign_value.'</div>
		 <div class="discount">'.$campaign_discount.'%</div>
		 <div class="saving">$'.$campaign_saving.'</div>
		</div>';
	}

	$campdiscount.='</div>';	
													 
   $campdiscount .= '<div title="Campaign Title" class="dealtitle" mypopupid="'.$popupurl.'" onClick="try1(this,'. $CampId .','.$storeid.')" data-fancybox-group="gallery">'.$CampTitle .'  </div>';
   $campdiscount .= '<div style="display:none" id="'.$CampId.$storeid.'">'. $CampId  . 'Deal and ' . $storeid.' Of Scanflip</div>';
  
//   if($_REQUEST['category_id'] == 0)
$from_lati=$_COOKIE['mycurrent_lati'];
                $from_long=$_COOKIE['mycurrent_long'];
				              
                $to_lati=$Row['latitude'];
                
                $to_long=$Row['longitude'];
                
                
                $deal_distance="";
                $deal_distance= "<b>Miles Away : </b>";
                    if($from_lati == $to_lati && $from_long == $to_long ){
                $deal_distance.=  "0 ";
                    }
                    else{
                         $deal_distance.= $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M") . " ";
                    }
               // $deal_distance.= $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M") . " ";
//                                                             {
                                                                $campdiscount .= '<div class="btnview">'.$viewbtn .'  </div>';
																if($from_lati!="" && $from_long!="")
																{
																	//$campdiscount .= '<div class="dealdistance">'.$deal_distance.'&nbsp;&nbsp;&nbsp;&nbsp;</div>';
																}	
																$campdiscount .= '</div></div>';                                                                    

			if ($CampTitle!=""){ $is_display=true; 
			$DetailHtml.= '<div class="deal_blk" level="'.$Row['campaign_type'].'" campid="'.$CampId.'" merid="'.$Row['l_created_by'].'" locid="'.$storeid.'" catid="'.$Row['category_id'].'" miles="'.$Row['distance'].'" style="display:'.$disply_css.'">'.$campdiscount;'</div><hr>';}
                                                                                     
                         }
						 
                    }
                    else{
                         $new_div_start ="";
                    if(!in_array($Row['locid'] ,$loc_arr)){
                      
                        if($ct != 0){
                            $new_div_start ="</div><div id='locationarea_".$Row['locid']."'>";
                        }  
                        else{
                             $new_div_start ="<div id='locationarea_".$Row['locid']."'>";
                        }
                        $ct = $ct +1;
                                         array_push($loc_arr,$Row['locid'] );
                 
                    }
                   
							  $CampTitle = $Row['title'];
												  $CampId =$Row['cid'];
                                                                                                        // location name changes start
                                                 
        
	if($Row['merchant_icon']!="")
								{
								    $img_src=ASSETS_IMG."/m/icon/".$Row['merchant_icon']; 
								}
							       
								else 
								{
								    $img_src=ASSETS_IMG."/c/Merchant.png";
								}
								
        $mer_ikon_img="<div class='mer_ikon_img'><img src='".$img_src."' /></div>";
      
                                                     $busines_name  = $Row['business'];
            if($Row['location_name']!="")
            {
                $businessname="<a target='_parent' title='Merchant Location' href='".WEB_PATH."/location_detail.php?id=".$Row['locid']."'>".$Row['location_name']."-".$Row['locid']."</a>";
            }
            else 
            {
               $businessname ="<a target='_parent' title='Merchant Location' href='".WEB_PATH."/location_detail.php?id=".$Row['locid']."'>".$busines_name ."</a>"; 
            }
            $businessname ="<div class='buss_rating'><a target='_parent' title='Merchant Location' href='javascript:void(0)'>".$busines_name ."</a>";
			$businessname.="</div>";	
               
 
													$storeid = $Row['locid'];
                                                                                                        $CampId = $Row['cid'];
                                                                                                        $cmpdis = $Row['discount'];
                                                                                                        $array_where_cat['id'] = $Row['category_id'];
                                                                                                        $RS_Cat = $objDB->Show("categories", $array_where_cat);
                                                                                                       $RS_Cat_image="<img class='kat_img' align='left' src='".ASSETS_IMG.$RS_Cat->fields['cat_image']."' title='".$RS_Cat->fields['cat_name']."' />";
                                                                                                       $viewbtn=$RS_Cat_image;
                                                                                                       $viewbtn .= '<a class="view-reserve" reserve="'.$reserved.'" redeem="'.$redeemed.'" offer_left="'.$offer_left.'" href="'.$Row['permalink'].'" target= "_parent">View And Reserve</a>';
					   //popup page send detail
							       
							       $number_of_use=$Row['number_of_use'];
							       $new_customer=$Row['new_customer'];
							       
							       $address = $Row['address'];
							       $city = $Row['city'];
							       $state = $Row['state'];
							       $zip = $Row['zip'];
							       $country = $Row['country'];
							       
							        
								$max_coupon = $Row['num_activation_code'];
								$o_left = $Row['offers_left'];
								$is_active =  $Row['active'];
							       
							       
							       $redeem_rewards=$Row['redeem_rewards'];
							       $referral_rewards=$Row['referral_rewards'];
								   
								   // start check for sharing limitaion if 0 then sharing point display 0
								   if($Row['referral_rewards'] != "")
								   {
										/* $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$CampId." and referred_customer_id<>0";
										 $RS_shared = $objDB->Conn->Execute($Sql_shared);*/
										$RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=".$CampId." and referred_customer_id<>0",array());
													
										 if($RS_shared->RecordCount() >= $Row['max_no_sharing'] )
										 {
											 $referral_rewards=0;
										 }
										 else
										 {
											 $referral_rewards=$Row['referral_rewards'];
										 }
									}
								   // end check for sharing limitaion if 0 then sharing point display 0
								   
							       $expiration_date = date("Y-m-d g:i:s A", strtotime($Row['expiration_date'] ));
							       
							        if($Row['business_logo']!="")
								{
								    $img_src=ASSETS_IMG."/m/campaign/block/".$Row['business_logo']; 
								}
							       
								else 
								{
								    $img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
								}
                        
                      
							       $popup_businessname=$Row['location_name'];
							       $deal_desc= $Row['deal_detail_description'];
							       $businessname_popup=$Row['location_name'];
                                                               $visited = 0;
							       if(!isset($_SESSION['customer_id']))
								{
								    $c = 0;
                                                                   
								}
								else{
								    $c = $_SESSION['customer_id'];
                                                                     /*$sql = "select * from customer_campaign_view where customer_id=".$_SESSION['customer_id']." and campaign_id =".$CampId;
                                                            $RS = $objDB->Conn->Execute($sql);*/
								$RS = $objDB->Conn->Execute("select * from customer_campaign_view where customer_id=? and campaign_id =?",array($_SESSION['customer_id'],$CampId));
                                                       
                                                            if($RS->RecordCount() > 0)
                                                            {
                                                                $visited = 1;
                                                            }
								}
							       //End Popup page    
								    $vi  = $CampId."-".$storeid."-".$Row['number_of_use']."-".$o_left;
                                                                                                       
       $popupurl=WEB_PATH."/popup_for_mymerchant.php?CampTitle=".urlencode($CampTitle)."&businessname=".urlencode($businessname_popup)."&number_of_use=".$number_of_use."&new_customer=".$new_customer."&address=".urlencode($address)."&city=".urlencode($city)."&state=".urlencode($state)."&country=".urlencode($country)."&zip=".$zip."&redeem_rewards=".$redeem_rewards."&referral_rewards=".$referral_rewards."&o_left=".$o_left."&expiration_date=".date("m/d/y g:i A", strtotime($expiration_date))."&img_src=".$img_src."&campid=".$CampId."&locationid=".$storeid."&deal_desc=".urlencode($deal_desc)."&is_reserve=".$reserved."&br=sd&cust_id=".$c."&decoded_cust_id=".base64_encode($c)."&webpath=".urlencode(WEB_PATH)."&is_mydeals=".$reserved."&voucher_id=".$vi."&visited=".$visited;
        
      $campdiscount = '<div class="offersexyellow">';
	  
	  if($Row['is_new']==1)
		 {
			$campdiscount.="<div class='new_24_hour_deal'><p>NEW</p></div>";
		 }
	  
	  $campdiscount .="<div class='grid_img_div'>";

	 $campdiscount .="<img  src='".$img_src."'>";

	 $campdiscount .="</div>";
	 
	 $campaign_value=$Row['deal_value'];
	$campaign_discount=$Row['discount'];
	$campaign_saving=$Row['saving'];
	
	$campdiscount.='<div class="dealmetric_wrap">';
	
	if($campaign_value!="0")
	{	
		$campdiscount.='<div class="dealmetric title">
		 <div class="value">Value</div>
		 <div class="discount">Discount</div>
		 <div class="saving">Savings</div>
		</div>

		<div class="dealmetric values">
		 <div class="value">$'.$campaign_value.'</div>
		 <div class="discount">'.$campaign_discount.'%</div>
		 <div class="saving">$'.$campaign_saving.'</div>
		</div>';
	}
	
	$campdiscount.='</div>';
	
	 // $campdiscount.='<div class="percetage" >'.$mer_ikon_img.$businessname .'  </div>';
       if($cmpdis!=""){
 //$campdiscount .= '<div class="offerstrip">'.$cmpdis .'  </div>';
       }
	   
	   
															 
   $campdiscount .= '<div title="Campaign Title" class="dealtitle" mypopupid="'.$popupurl.'" onClick="try1(this,'. $CampId .','.$storeid.')" data-fancybox-group="gallery">'.$CampTitle .'  </div>';
   $campdiscount .= '<div style="display:none" id="'.$CampId.$storeid.'">'. $CampId  . 'Deal and ' . $storeid.' Of Scanflip</div>';
   
    $from_lati=$_COOKIE['mycurrent_lati'];
                
                $from_long=$_COOKIE['mycurrent_long'];
                
                $to_lati=$Row['latitude'];
                
                $to_long=$Row['longitude'];
                
                
                $deal_distance="";
                
                $deal_distance= "Miles Away : ";
                    if($from_lati == $to_lati && $from_long == $to_long ){
                $deal_distance.=  "0 ";
                    }
                    else{
                         $deal_distance.= $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M") . " ";
                    }
               // $deal_distance.= $objJSON->distance($from_lati, $from_long, $to_lati, $to_long, "M"); 
//   if($_REQUEST['category_id'] == 0)
//                                                             {
                                                                $campdiscount .= '<div class="btnview">'.$viewbtn .'  </div>';
																if($from_lati!="" && $from_long!="")
																{
																	//$campdiscount .= '<div class="dealdistance">'.$deal_distance.'&nbsp;&nbsp;&nbsp;&nbsp;</div>';
																}
																$campdiscount .= '</div></div>';                              
			if ($CampTitle!=""){
			    
			   $is_display = true;
			    $DetailHtml.= '<div class="deal_blk" level="'.$Row['campaign_type'].'" campid="'.$CampId.'" merid="'.$Row['l_created_by'].'" locid="'.$storeid.'" catid="'.$Row['category_id'].'" miles="'.$Row['distance'].'">'.$campdiscount;'</div><hr>';}
                                                                                     
                        
                    }
                    
                     
                      
    }       
	 $new_div_start ="<div id='locationarea_".$_REQUEST['locid']."' >";
echo  $new_div_start.$DetailHtml."</div>";	
//}

if(!$is_display){
						 $error_msg = "<div class='no_Records_Found' align='center'>".$client_msg['location_detail']['table_no_campaigns_found']."</div>";
						echo $DetailHtml = $error_msg;
}
exit();
}


/**
 * @uses get marker for locations
 * @param merchant_id,category_id,dismile,mlatitude,mlongitude,dismile
 * @used in pages :location-detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetmarkerforLocations']))
{
         $merchantid = $_REQUEST['merchant_id'];
	 $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 $category_id=$_REQUEST['category_id'];
	 $dismile=$_REQUEST['dismile'];
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	 if(isset($_REQUEST['dismile']))
	 {
		
	 	$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
                $Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
         }
		 /*
   //$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
        */
		if($_REQUEST['customer_id']!="")
		 {
			  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";
		 }
		 else
		 {
			 $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
		 }
		 if(isset($_REQUEST['firstlimit']) && isset($_REQUEST['lastlimit']))
		 {
			 if($_REQUEST['firstlimit']=="" && $_REQUEST['lastlimit']=="")
			 {
				 $firstlimit=0;
				 $lastlimit=9;
			 }
			 else
			 {
				 $firstlimit=$_REQUEST['firstlimit'];
				 $lastlimit=$_REQUEST['lastlimit'];
			 }
         }
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) ";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) ";
		// 12-8-2013
		
		// 02-10-2013 dist list deal display if cust in dist list and reserved
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id  in ( select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id and activation_status=1  and cl.campaign_type=3)  )
) ";
		// 02-10-2013
		
		// 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , in this private deal also display if not subscribed
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id.") or c.level =1 ) ";
		// 03-10-2013
		
		// 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private=1 and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";

		 }
	 }
         $limit_data = "SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=".$merchantid." and    l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh."  ORDER BY distance,locid";
		
		// 05-10-2013 apply miles filter
		
		$limit_data = "SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=".$merchantid." and    l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where ."  ORDER BY distance,locid";
		
		 $RS_limit_data=$objDB->Conn->Execute("SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=? and    l.active = ?  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where ."  ORDER BY distance,locid",array($merchantid,1));
		 
         if($RS_limit_data->RecordCount()>0)
	 {
              $json_array['query']= $limit_data;
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS_limit_data->RecordCount();
		  $count=0;
		  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
                  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records_all[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["marker_records"]= $records_all;
                    $json_array['marker_total_records'] = $RS_limit_data->RecordCount();
                     $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 else
	 {
               $json_array['query']= $limit_data;
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
                   $json_array["records"]="";
		  $json_array["marker_records"]= "";
                    $json_array['marker_total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	exit();
}

/**
 * @uses get deals location copy
 * @param category_id,dismile,mlatitude,mlongitude,dismile
 * @used in pages :location-detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetDealsLocations-copy']))
{
     $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 $category_id=$_REQUEST['category_id'];
	// $dismile=$_REQUEST['dismile'];
         $dismile= 50;
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	 if(isset($_REQUEST['dismile']))
	 {
		$dismile= $_REQUEST['dismile'];
	 	$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
                //$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
      }
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
        
		if(isset($_REQUEST['firstlimit']) && isset($_REQUEST['lastlimit']))
		{		
			 if($_REQUEST['firstlimit']=="" && $_REQUEST['lastlimit']=="")
			 {
				 $firstlimit=0;
				 $lastlimit=9;
			 }
			 else
			 {
				 $firstlimit=$_REQUEST['firstlimit'];
				 $lastlimit=$_REQUEST['lastlimit'];
			 }
        } 
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		
	
		// 03-10-2013 dist list deal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013	
		
		// 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
		// 05-10-2013	
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		 }
	 }
         $limit_data = "SELECT cl.permalink , l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
 
         //$RS_limit_data=$objDB->Conn->Execute($limit_data);
	$RS_limit_data=$objDB->Conn->Execute("SELECT cl.permalink , l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE   l.active = ?  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date",array(1));

         if($RS_limit_data->RecordCount()>0)
	 {
             
          $json_array['is_profileset'] = 1;          
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS_limit_data->RecordCount();
                   $json_array['all_records'] = $RS_limit_data->RecordCount();
		  $count=0;
		  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
                  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records_all[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["marker_records"]= $records_all;
                    $json_array['marker_total_records'] = $RS_limit_data->RecordCount();
	 }
	 else
	 {
              $json_array['all_records'] = 0;
           $json_array["records"]="";
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
                   $json_array['is_profileset'] = 1;
		  $json_array["marker_records"]= "";
                    $json_array['marker_total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	exit();

}

/**
 * @uses filter deals for search deal
 * @param json_val
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['filter_deals_for_searchdeal']))
{
 
   $json = json_decode($_REQUEST['json_val']);
         $visible_location_arr = array();
       $campaign_arr = array();
       $counter_campaingn_arr =array();
       $counter_location_arr = array();
       $counter_merchant_arr = array();
       $all_merchant_arr =array();
       $c_l = array();
      // $total_records1 = $json->total_records;
			$records_array1 = json_decode($_REQUEST['json_val']);
                        echo count($records_array1);
                        exit;

    foreach($records_array1 as $Row)
    {
       

         $counter_merchant_arr[$Row->l_created_by] = $c_l;
    
    }
    $json_string = json_encode($counter_merchant_arr);
    echo $json_string;
}

/**
 * @uses get customer subcribes merchant
 * @param customer_id,lati,long,mycurrent_lati,mycurrent_long
 * @used in pages :json.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_customer_subscribes_merchant']))
{
    
    $id = $_REQUEST['customer_id'];
    $lati = $_REQUEST['lati'];
    $long = $_REQUEST['long'];
       if(isset($_SESSION['mycurrent_lati']))
    {
       $mlatitude=$_SESSION['mycurrent_lati'];
    }
    if(isset($_SESSION['mycurrent_long']))
    {
	    $mlongitude=$_SESSION['mycurrent_long'];
    }
            if(isset($_COOKIE['miles_cookie']))
            {
                $miles = $_COOKIE['miles_cookie'];
            }
            else
            {
                $miles = 50;
            }
            if(isset($_COOKIE['cat_remember']))
            {
                $category_id  = $_COOKIE['cat_remember'];
            }
            else
            {
                $category_id  = 0;
            }
             $cat_wh = "";
        if( $category_id !=0)
        {
           $cat_str = " and c.category_id = ".$category_id." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
        }
        else
        {
            $cat_str = "";
        }
		if(isset($_COOKIE['zoominglevel']))
		{
			 $zooming_level = $_COOKIE['zoominglevel'];
		}
		else
		{
			 $zooming_level = 20;
		}
		if($lati=="" && $long=="")
		{
		   if($_COOKIE['searched_location'] == "")
		   {
				$curr_latitude = $_SESSION['customer_info']['curr_latitude'];
				$curr_longitude = $_SESSION['customer_info']['curr_longitude'];
		   }
		   else
		   {
			   
				$curr_latitude = $_COOKIE['mycurrent_lati'];
				$curr_longitude = $_COOKIE['mycurrent_long'];
		   }
		}
		else
		{
			$curr_latitude = $lati;
			$curr_longitude = $long;
		}
		if(isset($_COOKIE['miles_cookie']))
            {
                $miles = $_COOKIE['miles_cookie'];
            }
            else
            {
                $miles = 50;
            }
			 $miles = 50;
		 $Where_miles = "";
		if($curr_longitude != "")
		{
			//$Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE ( 69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
                        $Where_miles = "(69.1*(l.latitude-($curr_latitude))*69.1*(l.latitude-($curr_latitude)))+(53.0*(l.longitude-($curr_longitude))*53.0*(l.longitude-($curr_longitude)))<=".$miles*$miles ;	
		}
             $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
         if($curr_latitude!="" && $curr_longitude!="")
	    {

             /*
             $limit_data = "SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                 (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status =0)  ))
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date";
			*/
			
			//13-8-2013
			/*$limit_data = "SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                 (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status =0)  ))
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date";*/
	
$RS_limit_data=$objDB->Conn->Execute("SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                 (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and c.is_walkin <> ? and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=?) or c.level =? ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=? and ss.subscribed_status=?)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status =?)  ))
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date",array(1,1,$id,1,$id,1,$id,$id,0));

	    }
	    else
	    {
		/*
	    $limit_data = "SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status =0)  ))
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date";
//               $Sql="select cl.location_id,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and cl.location_id in
// (SELECT DISTINCT location_id from subscribed_stores where CAT.active=1 and customer_id=".$id." and subscribed_status=1)  and cl.active=1 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and c.is_walkin!=1 and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ".$id." and location_id=cl.location_id) and cl.offers_left>0 and cl.active=1 ".$cat_wh ."  "; 
 			//echo "else";
		*/
		
		// 13-8-2013
			/*$limit_data = "SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status =0)  ))
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date";*/
		$RS_limit_data=$objDB->Conn->Execute("SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and c.is_walkin <> ? and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=?) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=? and ss.subscribed_status=?)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status =?)  ))
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date",array(1,1,$id,$id,1,$id,$id,0));
			
	    }
         // $RS_limit_data=$objDB->Conn->Execute($limit_data);

         if($RS_limit_data->RecordCount()>0)
	 {
              $json_array['query']= $limit_data;
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS_limit_data->RecordCount();
		  $count=0;
		  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
		  $records_all=array();
                  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records_all[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["marker_records"]= $records_all;
                    $json_array['marker_total_records'] = $RS_limit_data->RecordCount();
					
		   $json_array['all_records'] = "";
	 }
	 else
	 {
               $json_array['query']= $limit_data;
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
                   
		  $json_array["marker_records"]= "";
                    $json_array['marker_total_records'] = 0;
		   $json_array['records'] = "";
		   $json_array['all_records'] = "";
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	exit();
}

/**
 * @uses get customer deals
 * @param customer_id,lati,long,mycurrent_lati,mycurrent_long
 * @used in pages :json.php,my-deals.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_customer_deals']))
{
    
    $id = $_REQUEST['customer_id'];
    $lati = $_REQUEST['lati'];
    $long = $_REQUEST['long'];
    if(isset($_SESSION['mycurrent_lati']))
    {
       $mlatitude=$_SESSION['mycurrent_lati'];
    }
    if(isset($_SESSION['mycurrent_long']))
    {
	    $mlongitude=$_SESSION['mycurrent_long'];
    }
            if(isset($_COOKIE['miles_cookie']))
            {
                $miles = $_COOKIE['miles_cookie'];
            }
            else
            {
                $miles = 50;
            }
           
            if(isset($_COOKIE['cat_remember']))
            {
                $category_id  = $_COOKIE['cat_remember'];
            }
            else
            {
                $category_id  = 0;
            }
             $cat_wh = "";
        if( $category_id !=0)
        {
           $cat_str = " and c.category_id = ".$category_id." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
        }
        else
        {
            $cat_str = "";
        }
		if(isset($_COOKIE['zoominglevel']))
		{
			 $zooming_level = $_COOKIE['zoominglevel'];
		}
		else
		{
			 $zooming_level = 20;
		}
		if($lati=="" && $long=="")
		{
		   if($_COOKIE['searched_location'] == "")
		   {
				$curr_latitude = $_SESSION['customer_info']['curr_latitude'];
				$curr_longitude = $_SESSION['customer_info']['curr_longitude'];
		   }
		   else
		   {
			   
				$curr_latitude = $_COOKIE['mycurrent_lati'];
				$curr_longitude = $_COOKIE['mycurrent_long'];
		   }
		}
		else
		{
			$curr_latitude = $lati;
			$curr_longitude = $long;
		}
		if(isset($_COOKIE['miles_cookie']))
            {
                $miles = $_COOKIE['miles_cookie'];
            }
            else
            {
                $miles = 50;
            }
		 $Where_miles = "";
		if($curr_longitude != "")
		{
			//$Where_miles = " and ss.location_id in (SELECT sl.id FROM locations sl WHERE ( 69.1*(sl.latitude-(".$curr_latitude."))*69.1*(sl.latitude-(".$curr_latitude.")))+(53.0*(sl.longitude-(".$curr_longitude."))*53.0*(sl.longitude-(".$curr_longitude.")))<=".$miles*$miles ." ) " ;	
                        $Where_miles = "(69.1*(l.latitude-($curr_latitude))*69.1*(l.latitude-($curr_latitude)))+(53.0*(l.longitude-($curr_longitude))*53.0*(l.longitude-($curr_longitude)))<=".$miles*$miles ;	
		}
             $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 "; 
         if($curr_latitude!="" && $curr_longitude!="")
	    {

              /*$limit_data = "SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date";*/
	$RS_limit_data=$objDB->Conn->Execute("SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status=?)
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date",array(1,$id,1));
           
	    }
	    else
	    {
	    /*$limit_data = "SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date";*/
//               $Sql="select cl.location_id,l.location_name,l.address,l.city,l.state,l.zip,l.latitude,l.longitude,c.*,CAT.cat_name from campaigns c,categories CAT,campaign_location cl,locations l where l.active=1 and l.id=cl.location_id and cl.campaign_id=c.id and CAT.id=c.category_id and cl.location_id in
// (SELECT DISTINCT location_id from subscribed_stores where CAT.active=1 and customer_id=".$id." and subscribed_status=1)  and cl.active=1 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and c.is_walkin!=1 and c.id not in ( select  campaign_id from customer_campaigns where customer_id = ".$id." and location_id=cl.location_id) and cl.offers_left>0 and cl.active=1 ".$cat_wh ."  "; 
 			//echo "else";

	$RS_limit_data=$objDB->Conn->Execute("SELECT cl.permalink,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,
                (((acos(sin((".$curr_latitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$curr_latitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$curr_longitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status=?)
  ".$cat_str  ."  ".$date_wh." and ".$Where_miles ."
 ORDER BY distance,c.expiration_date",array(1,$id,1));

	    }
      //echo $limit_data;
	   //exit;
    $RS_limit_data=$objDB->Conn->Execute($limit_data);
    $records_all=array();
    if($RS_limit_data->RecordCount()>0)
	{
		$json_array['query']= $limit_data;
		$json_array['status'] = "true";
		//$json_array['total_records'] = $RS_limit_data->RecordCount();
		$count=0;
		$records=array();
		while($Row = $RS_limit_data->FetchRow())
		{			
				$array_where_camp=array();
				$array_where_camp['campaign_id'] = $Row['cid'];
				$array_where_camp['customer_id'] = $_REQUEST['customer_id'];
				$array_where_camp['referred_customer_id'] = 0;
				$array_where_camp['location_id'] = $Row['locid'];
				$RS_camp = $objDB->Show("reward_user", $array_where_camp);
				
				$array_where_camp1=array();
				$array_where_camp1['campaign_id'] = $Row['cid'];
				$array_where_camp1['location_id'] = $Row['locid'];
				$campLoc = $objDB->Show("campaign_location", $array_where_camp1);
				
				/*
				echo "cid : ".$Row['cid'];
				echo "</br>";
				echo "locid : ".$Row['locid'];
				echo "</br>";
				echo "custid : ".$_REQUEST['customer_id'];
				echo "</br>";
				echo "redem : ".$RS_camp->RecordCount();
				echo "</br>";
				echo "number of use : ".$Row['number_of_use'];
				echo "</br>";
				*/
				
				if($RS_camp->RecordCount()>0 && $Row['number_of_use']=="1")
				 {
					 //echo "1 ".$Row['cid'].",".$Row['locid'];
				 } 
				 else if ($RS_camp->RecordCount()>0 && ($Row['number_of_use']=="2" || $Row['number_of_use']=="3" ) && $campLoc->fields['offers_left']==0) 
				 {
					 //echo "2 ".$Row['cid'].",".$Row['locid'];
				 }
				 else
				 {
					//echo "3 else";
					$records[$count] = get_field_value($Row);
					$count++;
					//echo $count;
				 }
		}
			$json_array["records"]= $records;
			// 31-7-2013 add line
			$json_array["total_records"]= $count;
			// 31-7-2013 add line
			
            while($Row = $RS_limit_data->FetchRow())
			{
				$records_all[$count] = get_field_value($Row);
				$count++;
			}
			$json_array["marker_records"]= $records_all;
            $json_array['marker_total_records'] = $RS_limit_data->RecordCount();
			$json_array['all_records'] = "";
	 }
	 else
	 {
          $json_array['query']= $limit_data;
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
                   
		  $json_array["marker_records"]= "";
          $json_array['marker_total_records'] = 0;
		  $json_array['records'] = "";
		  $json_array['all_records'] = "";
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	 exit();
}

/**
 * @uses get customer deals
 * @param 
 * @used in pages :location-detail.php,mymerchants.php,search-deal.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['show_voucher'])){
   
     if(!isset($_SESSION['customer_id']))
     {
        $json_array = array();
	
		 $json_array['loginstatus'] = "false";
		 $json_array['link'] = WEB_PATH."/register.php?url=".$_SERVER['HTTP_REFERER'];
		$json_array['is_profileset'] = 1;
		  $json = json_encode($json_array);
         echo $json;
		exit();
	}
	else
	{
			$json_array = array();
			$vid = $_REQUEST['vid'];
			$v_arr = explode("-",$vid);
    
                     $array_where_camp['campaign_id'] = $v_arr[0];
                 $array_where_camp['customer_id'] = $_SESSION['customer_id'];
                 $array_where_camp['referred_customer_id'] = 0;
                 $array_where_camp['location_id'] = $v_arr[1];
                 $RS_camp = $objDB->Show("reward_user", $array_where_camp);
                if($RS_camp->RecordCount()>0 && $v_arr[2]==1)
                 {
                    $json_array["status"]= "false";
                 }
                 elseif($v_arr[2]==2 && $v_arr[3]!=0 && $RS_camp->RecordCount()>0 )
                 {
						$json_array["status"]= "true";
                 }
                 elseif ($RS_camp->RecordCount()>0 &&  ($v_arr[2]==2 || $v_arr[2]==3)  && $v_arr[3]==0) 
                 {
                    $json_array["status"]= "false";
                 }
                 else{
                     $json_array["status"]= "true";
                     
                 }
                 $json_array['loginstatus'] = "true";
                  $json = json_encode($json_array);
	 echo $json;
	exit();
  
        }
}

/**
 * @uses reserve sub deal
 * @param 
 * @used in pages :campaign.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['reserve_sub_deal']))
{
   $objDB->Conn->StartTrans();
  if(!isset($_SESSION['customer_id'])){
  
	 $json_array = array();
	
	 $json_array['status'] = "false";
	 $json_array['link'] = WEB_PATH."/register.php?url=".urlencode($_SERVER['HTTP_REFERER']);
         $json_array['is_profileset'] = 1;
         $json = json_encode($json_array);
		  $objDB->Conn->CompleteTrans(); 
         echo $json;
	 exit();
	}
	else
	{
	
	$timestamp = $_REQUEST['timestamp'];
	
/*    $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$_REQUEST['campaign_id']." and location_id =".$_REQUEST['l_id']." ";
                    $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation);*/
		$RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =? ",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));

                    $offers_left = $RS_num_activation->fields['offers_left'];
                    $used_campaign = $RS_num_activation->fields['used_offers'];
                    if($offers_left >0){
/*    $Sql_new = "SELECT * FROM customer_campaigns WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$_REQUEST['campaign_id']."' AND location_id =".$_REQUEST['l_id'];
$RS_new = $objDB->Conn->Execute($Sql_new);*/

$RS_new = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =".$_REQUEST['l_id'],array($_SESSION['customer_id'],$_REQUEST['campaign_id']));

 if($RS_new->RecordCount()<=0){
/*     $Sql = "SELECT * FROM activation_codes WHERE campaign_id=".$_REQUEST['campaign_id'];
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE campaign_id=?",array($_REQUEST['campaign_id']));

		if($RS->RecordCount()>0){
			$activation_code = $RS->fields['activation_code'];
                        /*$Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= '".$timestamp."', coupon_generation_date='".$timestamp."',
					customer_id='".$_SESSION['customer_id']."', campaign_id=".$_REQUEST['campaign_id']." , location_id=".$_REQUEST['l_id'];
			$objDB->Conn->Execute($Sql);*/
			$objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status=?, activation_code=?, activation_date= ?, coupon_generation_date=?,customer_id=?, campaign_id=? , location_id=?",array(1,$activation_code,$timestamp,$timestamp,$_SESSION['customer_id'],$_REQUEST['campaign_id'],$_REQUEST['l_id']));
		}
		
  }
  else{
      /*$Sql_new = "Update customer_campaigns set activation_status=1 WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$_REQUEST['campaign_id']."' AND location_id =".$_REQUEST['l_id'];
    $objDB->Conn->Execute($Sql_new);*/
 $objDBWrt->Conn->Execute("Update customer_campaigns set activation_status=? WHERE customer_id=? AND campaign_id=? AND location_id =?",array(1,$_SESSION['customer_id'],$_REQUEST['campaign_id'],$_REQUEST['l_id']));
 
  }

$br = $_REQUEST['barcodea'];
if(isset($_REQUEST['barcodea'])){
$where_clause = array();
	$array = $json_array = array();
	$array['customer_id'] = $_SESSION['customer_id'];
	$array['customer_campaign_code'] = $_REQUEST['campaign_id'];
        $array['location_id'] = $_REQUEST['l_id'];
	$RS = $objDB->Show("coupon_codes",$array);
	if($RS->RecordCount()>0){ 
		$array['generated_date'] = $timestamp;
		$where_clause_arr['customer_id'] = $_SESSION['customer_id'];
		
			$where_clause_arr['location_id'] = $_REQUEST['l_id'];
		$where_clause_arr['customer_campaign_code'] = $_REQUEST['campaign_id'];
		$array['active']=1;
		$objDB->Update($array, "coupon_codes", $where_clause_arr);
	}else{
	$RS = $objDB->Show("coupon_codes",$array);

        $array_ = $json_array = array();
	$array_['customer_id'] = $_SESSION['customer_id'];
	$array_['customer_campaign_code'] = $_REQUEST['campaign_id'];
	$array_['coupon_code'] = $br;
	$array_['active']=1;
        $array_['location_id'] = $_REQUEST['l_id'];
	$array_['generated_date'] = $timestamp;
	$objDB->Insert($array_, "coupon_codes");
        
        /*$update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$_REQUEST['campaign_id']." and location_id =".$_REQUEST['l_id']." ";
                        $objDB->Conn->Execute($update_num_activation);*/
	$objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=? and location_id =?",array(($offers_left-1),($used_campaign+1),$_REQUEST['campaign_id'],$_REQUEST['l_id']));
	}
        $json_array['status'] = "true";
        $json_array['o_left'] = $offers_left-1;
}
                    }
                    else{
                          $json_array['status'] = "false";
                    }
   $objDB->Conn->CompleteTrans();                   
echo json_encode($json_array);
exit();
 }
}
//for unreserve deal //

/**
 * @uses unreserved deal
 * @param l_id,customer_id
 * @used in pages :campaign.php,location.php,location_facebook.php,my-deals.php,mymerchants.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnunreservedeal']))
{
$json_array['unreserve_status'] = "true";
$location_id= $_REQUEST['l_id'];
	
	if($_REQUEST['customer_id'] == ""){
		$customer_id = $_SESSION['customer_id'];
	}else
	{
		$customer_id = $_REQUEST['customer_id'];

	}
		$storearray = array();
	$storearray['location_id']=$location_id;		
		$RSstoe = $objDB->Show("campaign_location",$storearray);
	    if($RSstoe->RecordCount()<=0)
		{
            
			$json_array['status'] = "false";
			$json_array['message'] = "There is no campaign for this store";
			$json = json_encode($json_array);
			header("Location: my-deals.php");
			exit();
		}
		else
		{
		$json_array['unreserve_status'] = "false";
                    /*$sql = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$_REQUEST['campaign_id']." and location_id=".$location_id ;
                    $RS_cc1 =$objDB->Conn->Execute($sql);*/
		$RS_cc1 =$objDB->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?",array($customer_id,$_REQUEST['campaign_id'],$location_id));

					if($RS_cc1->RecordCount() != 0)
					{
                    /*$sql = "Select * from coupon_redeem where coupon_id in (".$RS_cc1->fields['id'].")";
                     $RS_c =$objDB->Conn->Execute($sql);*/
		 $RS_c =$objDB->Conn->Execute("Select * from coupon_redeem where coupon_id in (?)",array($RS_cc1->fields['id']));

                    if($RS_c->RecordCount() == 0)
					{
                            /*$sql = "select * from customer_campaigns where customer_id=".$customer_id." and campaign_id=".$_REQUEST['campaign_id']." and location_id=".$location_id ;
                   			$RS_cc =$objDB->Conn->Execute($sql);*/
					$RS_cc =$objDB->Conn->Execute("select * from customer_campaigns where customer_id=? and campaign_id=? and location_id=?",array($customer_id,$_REQUEST['campaign_id'],$location_id));

							if($RS_cc->RecordCount()>0)
							{
									/*$Sql = "DELETE FROM customer_campaigns where customer_id=".$customer_id." and campaign_id=".$_REQUEST['campaign_id']." and location_id=".$location_id ;
                   					$objDB->Conn->Execute($Sql);   */
						$objDBWrt->Conn->Execute("DELETE FROM customer_campaigns where customer_id=? and campaign_id=? and location_id=?",array($customer_id,$_REQUEST['campaign_id'],$location_id));   
                            }
						
							if($RS_cc1->RecordCount()>0)
							{
								// $Sql = "UPDATE coupon_codes SET active=0 where customer_id=".$customer_id." and customer_campaign_code=".$_REQUEST['campaign_id']." and location_id=".$location_id ;
								    /*$Sql = "DELETE FROM coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$_REQUEST['campaign_id']." and location_id=".$location_id ;
									$objDB->Conn->Execute($Sql);  */
								$objDBWrt->Conn->Execute("DELETE FROM coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?",array($customer_id),$_REQUEST['campaign_id'],$location_id);

									/*$update_num_activation = "Update campaign_location set offers_left=offers_left+1 , used_offers=used_offers-1 where campaign_id=".$_REQUEST['campaign_id']." and location_id =".$_REQUEST['l_id']." ";
									$objDB->Conn->Execute($update_num_activation);  */
								$objDBWrt->Conn->Execute("Update campaign_location set offers_left=offers_left+1 , used_offers=used_offers-1 where campaign_id=? and location_id =?",array($_REQUEST['campaign_id'],$_REQUEST['l_id']));  
							}
					  }
					  else
					  {
							/*$Sql = "select * from campaigns where id=".$_REQUEST['campaign_id'];
							$RS_cam=$objDB->Conn->Execute($Sql);*/
							$RS_cam=$objDB->Conn->Execute("select * from campaigns where id=?",array($_REQUEST['campaign_id']));
 
							if($RS_cam->fields['number_of_use']==1)
							{
								/*$Sql = "select offers_left from campaign_location where campaign_id=".$_REQUEST['campaign_id']. " and location_id= ".$location_id;
								$o_left_rs = $objDB->Conn->Execute($Sql); */
								$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$location_id)); 

								$o_left = $o_left_rs->fields['offers_left'];
								$json_array['o_left'] = $o_left;
								$json_array['status'] = "false";		
								$json = json_encode($json_array);
								echo $json;
								exit();
							}
							else
							{
								/*$Sql = "select offers_left from campaign_location where campaign_id=".$_REQUEST['campaign_id']. " and location_id= ".$location_id;
								$o_left_rs = $objDB->Conn->Execute($Sql); */
								$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$location_id)); 

								$o_left = $o_left_rs->fields['offers_left'];
								/*$Sql = "UPDATE coupon_codes SET active=0 where customer_id=".$customer_id." and customer_campaign_code=".$_REQUEST['campaign_id']." and location_id=".$location_id ;
								$objDB->Conn->Execute($Sql); */
								$objDBWrt->Conn->Execute("UPDATE coupon_codes SET active=? where customer_id=? and customer_campaign_code=? and location_id=?",array(0,$customer_id,$_REQUEST['campaign_id'],$location_id)); 

								/*$Sql = "UPDATE customer_campaigns SET activation_status=0 where customer_id=".$customer_id." and campaign_id=".$_REQUEST['campaign_id']." and location_id=".$location_id ;
								$objDB->Conn->Execute($Sql); */
								$objDBWrt->Conn->Execute("UPDATE customer_campaigns SET activation_status=? where customer_id=? and campaign_id=? and location_id=?",array(0,$customer_id,$_REQUEST['campaign_id'],$location_id)); 
								$json_array['o_left'] = $o_left;
							}
					  }
					// remove coupon codes //
					/*$Sql = "select offers_left from campaign_location where campaign_id=".$_REQUEST['campaign_id']. " and location_id= ".$location_id;
								$o_left_rs = $objDB->Conn->Execute($Sql); */
								$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$location_id)); 

								$o_left = $o_left_rs->fields['offers_left'];
								$json_array['o_left'] = $o_left;
					 $json_array['status'] = "true";		
					$json = json_encode($json_array);
                                        echo $json;
					//header("Location:campaign.php?campaign_id=".$_REQUEST['campaign_id']."&l_id=".$location_id);
					exit();
					}
					else
					{
						/*$Sql = "select offers_left from campaign_location where campaign_id=".$_REQUEST['campaign_id']. " and location_id= ".$location_id;
						$o_left_rs = $objDB->Conn->Execute($Sql); */
						$o_left_rs = $objDB->Conn->Execute("select offers_left from campaign_location where campaign_id=? and location_id=?",array($_REQUEST['campaign_id'],$location_id));

						$o_left = $o_left_rs->fields['offers_left'];
						$json_array['o_left'] = $o_left;
						$json_array['status'] = "false";
						$json_array['unreserve_status'] = "false";
						$json_array['message'] = "Campaign is no longer reserved for this store";
						$json = json_encode($json_array);
						echo $json;
						exit();
					}
			   }
}

/**
 * @uses get customer order
 * @param customer_id,days
 * @used in pages :my-orders.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getCustomerorders']))
{
	$customer_id = $_REQUEST['customer_id'];
	//$Sql = "select gco.order_note,gco.order_date,gc.title,gco.order_number,gco.status from giftcards gc, giftcard_order gco where gc.id=gco.giftcard_id and gco.user_id=". $customer_id." order by order_date desc";
    
	if($_REQUEST['days'] != "")
    {
		/*echo $Sql = "select gco.ship_to_address,gc.card_value,gc.redeem_point_value,gco.order_note,gco.order_date,gc.title,gco.order_number,gco.status,DATEDIFF(NOW(),gco.order_date) from giftcards gc, giftcard_order gco where gc.id=gco.giftcard_id and gco.user_id=". $customer_id." and (DATEDIFF(NOW(),gco.order_date)<=".$_REQUEST['days'].") order by order_date desc";*/
		$RS = $objDB->Conn->Execute("select gco.ship_to_address,gc.card_value,gc.redeem_point_value,gco.order_note,gco.order_date,gc.title,gco.order_number,gco.status,DATEDIFF(NOW(),gco.order_date) from giftcards gc, giftcard_order gco where gc.id=gco.giftcard_id and gco.user_id=? and (DATEDIFF(NOW(),gco.order_date)<=?) order by order_date desc limit ".$_REQUEST[iDisplayStart].",".$_REQUEST[iDisplayLength],array($customer_id,$_REQUEST['days']));

		$rTotal = $objDB->Conn->Execute("select count(*) total from giftcards gc, giftcard_order gco where gc.id=gco.giftcard_id and gco.user_id=? and (DATEDIFF(NOW(),gco.order_date)<=?) order by order_date desc",array($customer_id,$_REQUEST['days']));

	}
	else
	{
		/*echo $Sql = "select gco.ship_to_address,gc.card_value,gc.redeem_point_value,gco.order_note,gco.order_date,gc.title,gco.order_number,gco.status from giftcards gc, giftcard_order gco where gc.id=gco.giftcard_id and gco.user_id=". $customer_id." order by order_date desc";*/
		$RS = $objDB->Conn->Execute("select gco.ship_to_address,gc.card_value,gc.redeem_point_value,gco.order_note,gco.order_date,gc.title,gco.order_number,gco.status from giftcards gc, giftcard_order gco where gc.id=gco.giftcard_id and gco.user_id=? order by order_date desc limit ".$_REQUEST[iDisplayStart].",".$_REQUEST[iDisplayLength],array($customer_id));

		$rTotal = $objDB->Conn->Execute("select count(*) total from giftcards gc, giftcard_order gco where gc.id=gco.giftcard_id and gco.user_id=? order by order_date desc",array($customer_id));

	}
	$rTotal = $rTotal->FetchRow();
	$total= $rTotal['total'];
	//$RS = $objDB->Conn->Execute($Sql);
	
	$Json = array();
	$Json['iDisplayLength'] = $_REQUEST['iDisplayLength'];
	$Json['iDisplayStart'] = $_REQUEST['iDisplayStart'];
	$Json['iTotalDisplayRecords'] = $total;
	$Json['sEcho'] = $_REQUEST['sEcho'];
	$Json['sSearch'] = $_REQUEST['sSearch'];
	$Json['aaData'] = array();

	if($RS->RecordCount()>0)
	{
		$k=0;
		while($Row = $RS->FetchRow())
		{
			$Json['aaData'][$k][]=date("Y-m-d g:i:s A", strtotime($Row['order_date']));
                        $Json['aaData'][$k][]="<a href='javascript:void(0)' ship_to='" . $Row['ship_to_address'] . "' reward_detail='" . $Row['title'] . "' reward_value='" . $Row['card_value'] . "' point_redeemed='" . $Row['redeem_point_value'] . "' notes='" . $Row['order_note'] . "' class='notclass'>" . $Row['order_number'] . "</a>";;
                        $Json['aaData'][$k][]=$Row['title'];
			if ($Row['status'] == 1)
                                $status= "Shipped";
                        else if ($Row['status'] == 2)
                                $status= "Pending";
                        else if ($Row['status'] == 0)
                                $status= "Cancelled";
                        $Json['aaData'][$k][]=$status;
                        $k++;
		}
		
	}
	echo json_encode($Json);
         
         exit;
		 
}

/**
 * @uses get customer transaction
 * @param customer_id,lati,long,miles
 * @used in pages :my-points.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getCustomerTransactions']))
{
    $customer_id = $_REQUEST['customer_id'];
    $mlatitude = $_REQUEST['lati'];
	$mlongitude = $_REQUEST['long'];
	$lati = $_REQUEST['lati'];
	$long = $_REQUEST['long'];;
	
	$miles = $_REQUEST['miles'];
	if(isset($_COOKIE['cat_remember']))
	{
		$category_id  = $_COOKIE['cat_remember'];
	}
	else
	{
		$category_id  = 0;
	}
	$cat_wh = "";
	if( $category_id !=0)
	{
                $cat_wh = " and c.category_id = ".$category_id." and c.category_id in(select cat.id from categories cat where cat.active=1)";
	}
	else
	{
		$cat_wh=" and  c.category_id in(select cat.id from categories cat where cat.active=1)";
	}
    if(isset($_COOKIE['zoominglevel']))
	{
		 $zooming_level = $_COOKIE['zoominglevel'];
	}
	else
	{
		 $zooming_level = 20;
	}
	
		$curr_latitude = $_REQUEST['lati'];
		$curr_longitude = $_REQUEST['long'];
	
	
	$Where_miles = "";
	if($curr_longitude != "")
	{
		$Where_miles = " (69.1*(l.latitude-($curr_latitude))*69.1*(l.latitude-($curr_latitude)))+(53.0*(l.longitude-($curr_longitude))*53.0*(l.longitude-($curr_longitude)))<=".$miles*$miles ;	
	}
        $days_cond = "";
	if($_REQUEST['days'] != "")
        {
            $days_cond = " and if(
(select DATEDIFF(NOW(),max(redeem_date) )  from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and 
cc.location_id = rw.location_id and customer_id= rw.customer_id ) IS NULL,DATEDIFF(NOW(),rw.reward_date)  ,
(select DATEDIFF(NOW(),max(redeem_date) )  from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and 
cc.location_id = rw.location_id and customer_id= rw.customer_id )) <=".$_REQUEST['days'];
        }
        else{
            $days_cond = "";
        }
  if($curr_latitude!="" && $curr_longitude!="")
  {	
	
	  
	/*$Sql = "Select rw.reward_date , sum(rw.earned_reward) tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint , rw.campaign_id campaign_id, rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.* , l.* ,l.id location_id  , c.id campaign_id  ,
          (select sum(cr.redeem_value) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) spent_point
,
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) last_redeemdate
,
(select   DATEDIFF(NOW(),max(redeem_date))  days from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) days
 from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
             where rw.customer_id = ". $customer_id ." ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc" ; */
		/*$Sql = "Select rw.reward_date , sum(rw.earned_reward) tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint , rw.campaign_id campaign_id, rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.* , l.* ,l.id location_id  , c.id campaign_id  ,
          (select sum(cr.redeem_value) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) spent_point
,
if(
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id )  IS NULL,rw.reward_date  ,
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id ) 
)  last_redeemdate 

 from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
             where rw.customer_id = ". $customer_id ." ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc" ;*/
	
	 $RS = $objDB->Conn->Execute("Select rw.reward_date , sum(rw.earned_reward) tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint , rw.campaign_id campaign_id, rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.title ,l.location_name,l.address,l.city,l.state,l.zip ,
          (select sum(cr.redeem_value) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) spent_point
,
if(
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id )  IS NULL,rw.reward_date  ,
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id ) 
)  last_redeemdate 

 from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
             where rw.customer_id = ? ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc limit 0,25",array($customer_id));
  }
  else
  {
	 	/*$Sql = "Select  rw.reward_date , rw.earned_reward tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint , rw.campaign_id campaign_id, rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.* , l.* ,l.id location_id  ,c.id campaign_id  ,
           DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,max(redeem_date)))  days ,
            if(cr.redeem_date IS NULL,rw.reward_date,max(redeem_date)) last_redeemdate
            ,  sum(cr.redeem_value) spent_point from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id  left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id
             where rw.customer_id = ". $customer_id ." ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc" ; */
		/*$Sql = "Select rw.reward_date , sum(rw.earned_reward) tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint , rw.campaign_id campaign_id, rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.* , l.* ,l.id location_id  , c.id campaign_id  ,
          (select sum(cr.redeem_value) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) spent_point
,
if(
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id )  IS NULL,rw.reward_date  ,
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id ) 
)  last_redeemdate 

 from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
             where rw.customer_id = ". $customer_id ." ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc" ;*/

$RS = $objDB->Conn->Execute("Select rw.reward_date , sum(rw.earned_reward) tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint ,  rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.title ,l.location_name,l.address,l.city,l.state,l.zip ,
          (select sum(cr.redeem_value) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) spent_point
,
if(
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id )  IS NULL,rw.reward_date  ,
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id ) 
)  last_redeemdate 

 from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
             where rw.customer_id =? ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc",array($customer_id));

  }  
  //echo $Sql;
  //exit;
//  $RS = $objDB->Conn->Execute($Sql);
	if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		 $json_array["records"]= $records;
		 $records_all=array();
         while($Row = $RS->FetchRow())
		  {
		   	$records_all[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["marker_records"]= $records_all;
                    $json_array['marker_total_records'] = $RS->RecordCount();
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  
		  $json_array['total_records'] = 0;
                   $json_array["records"]= "";
		  $json_array["marker_records"]= "";
                    $json_array['marker_total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	exit();


}

/**
 * @uses get filter my points
 * @param days
 * @used in pages :mypoints.php,myreviews.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getfiltermy_points']))
{
   

        $arr = array();
        if($_REQUEST['days'] == 0)
        {
        }
        else{
        }
        $customer_id = $_REQUEST['customer_id'];
        $mlatitude = $_REQUEST['lati'];
	$mlongitude = $_REQUEST['long'];
	$lati = $_REQUEST['lati'];
	$long = $_REQUEST['long'];;
	
	$miles = $_REQUEST['miles'];
	if(isset($_COOKIE['cat_remember']))
	{
		$category_id  = $_COOKIE['cat_remember'];
	}
	else
	{
		$category_id  = 0;
	}
	$cat_wh = "";
	if( $category_id !=0)
	{
                $cat_wh = " and c.category_id = ".$category_id." and c.category_id in(select cat.id from categories cat where cat.active=1)";
	}
	else
	{
		$cat_wh=" and  c.category_id in(select cat.id from categories cat where cat.active=1)";
	}
    if(isset($_COOKIE['zoominglevel']))
	{
		 $zooming_level = $_COOKIE['zoominglevel'];
	}
	else
	{
		 $zooming_level = 20;
	}
	
		$curr_latitude = $_REQUEST['lati'];
		$curr_longitude = $_REQUEST['long'];
	
	
	$Where_miles = "";
	if($curr_longitude != "")
	{
		$Where_miles = " (69.1*(l.latitude-($curr_latitude))*69.1*(l.latitude-($curr_latitude)))+(53.0*(l.longitude-($curr_longitude))*53.0*(l.longitude-($curr_longitude)))<=".$miles*$miles ;	
	}
        $days_cond = "";
	if($_REQUEST['days'] != "")
        {
            $days_cond = " and if(
(select DATEDIFF(NOW(),max(redeem_date) )  from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and 
cc.location_id = rw.location_id and customer_id= rw.customer_id ) IS NULL,DATEDIFF(NOW(),rw.reward_date)  ,
(select DATEDIFF(NOW(),max(redeem_date) )  from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and 
cc.location_id = rw.location_id and customer_id= rw.customer_id )) <=".$_REQUEST['days'];
        }
        else{
            $days_cond = "";
        }
  if($curr_latitude!="" && $curr_longitude!="")
  {	
	
	 $RS = $objDB->Conn->Execute("Select rw.reward_date , sum(rw.earned_reward) tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint , rw.campaign_id campaign_id, rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.title ,l.location_name,l.address,l.city,l.state,l.zip ,
          (select sum(cr.redeem_value) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) spent_point
,
if(
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id )  IS NULL,rw.reward_date  ,
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id ) 
)  last_redeemdate 

 from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
             where rw.customer_id = ? ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc limit ".$_REQUEST[iDisplayStart].",".$_REQUEST[iDisplayLength],array($customer_id));
         
        $rTotal= $objDB->Conn->Execute("select count(*) as total from (Select rw.reward_date , sum(rw.earned_reward) tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint , rw.campaign_id campaign_id, rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.title ,l.location_name,l.address,l.city,l.state,l.zip ,
          (select sum(cr.redeem_value) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) spent_point
,
if(
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id )  IS NULL,rw.reward_date  ,
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id ) 
)  last_redeemdate 

 from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
             where rw.customer_id = ? ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc) reward_user ",array($customer_id));
        $total = $rTotal->FetchRow();
        $total = $total['total'];
  }
  else
  {
	 

$RS = $objDB->Conn->Execute("Select rw.reward_date , sum(rw.earned_reward) tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint ,  rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.title ,l.location_name,l.address,l.city,l.state,l.zip ,
          (select sum(cr.redeem_value) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) spent_point
,
if(
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id )  IS NULL,rw.reward_date  ,
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id ) 
)  last_redeemdate 

 from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
             where rw.customer_id =? ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc limit ".$_REQUEST[iDisplayStart].",".$_REQUEST[iDisplayLength],array($customer_id));

$rTotal= $objDB->Conn->Execute("select count(*) as total from (Select rw.reward_date , sum(rw.earned_reward) tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint ,  rw.location_id location_id ,
            rw.coupon_code_id coupon_id , c.title ,l.location_name,l.address,l.city,l.state,l.zip ,
          (select sum(cr.redeem_value) from coupon_redeem cr , coupon_codes cc 
where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id and cc.location_id = rw.location_id and customer_id= rw.customer_id  ) spent_point
,if((select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id )  IS NULL,rw.reward_date  ,
(select max(redeem_date) from coupon_redeem cr , coupon_codes cc where cc.id = cr.coupon_id and cc.customer_campaign_code = rw.campaign_id
 and cc.location_id = rw.location_id and customer_id= rw.customer_id ) 
)  last_redeemdate from 
            reward_user rw  inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
             where rw.customer_id =? ".$days_cond." 
            group by rw.campaign_id , rw.location_id , rw.customer_id 
            order by last_redeemdate desc) reward_user ",array($customer_id));
        $total = $rTotal->FetchRow();
        $total = $total['total'];
        
        

  }  
  
$Json = array();
$Json['iDisplayLength'] = $_REQUEST['iDisplayLength'];
$Json['iDisplayStart'] = $_REQUEST['iDisplayStart'];
$Json['iTotalDisplayRecords'] = $total;
$Json['sEcho'] = $_REQUEST['sEcho'];
$Json['sSearch'] = $_REQUEST['sSearch'];
	$Json['aaData'] = array();
         
        if($RS->RecordCount()>0){
                $k =0;
                while($Row = $RS->FetchRow())
		  {
                        if($v['last_redeemdate'] == ""){
                                $Json['aaData'][$k][]=  date("Y-m-d g:i:s A", strtotime($Row['reward_date'] ));
                        }
                        else{
                                $Json['aaData'][$k][]= date("Y-m-d g:i:s A", strtotime($Row['last_redeemdate']));
                        }
                        
                        $address = $Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip'];
                        $Json['aaData'][$k][]=$Row['location_name'] .'<br>'.$address;
                        $Json['aaData'][$k][]=$Row['title'];
                        $Json['aaData'][$k][]="<span style='cursor: pointer;' title='Referral Point = ".$Row['tot_redeempoints']." Redemption Point = ".$Row['tot_sharingpoint']."'>".($Row['tot_redeempoints'] + $Row['tot_sharingpoint'])."</span>";
                        $Json['aaData'][$k][]=(!empty($Row['spent_point']))?'$'.$Row['spent_point']:'-';
                        $k++;
                  }
         }
	
        echo json_encode($Json);
         
         exit;
}
/**
 * @uses get filter my orders
 * @param days
 * @used in pages :my-orders.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['getfiltermy_orders']))
{
   

        $arr = array();
        if($_REQUEST['days'] == 0)
        {
           
		   $arr=file(WEB_PATH.'/process.php?getCustomerorders=yes&customer_id='.$_SESSION['customer_id']);
        }
        else{
			
            $arr=file(WEB_PATH.'/process.php?getCustomerorders=yes&customer_id='.$_SESSION['customer_id'].'&days='.$_REQUEST['days']);
        }
				if(trim($arr[0]) == "")
				{
					unset($arr[0]);
					$arr = array_values($arr);
				}
                                 $all_json_str = $arr[0];
				$json = json_decode($arr[0]);
				$total_records1 = $json->total_records;
				$records_array1 = $json->records;
				
    ?>
         <table width="100%"  border="0" cellspacing="1" cellpadding="2" class="tableMerchant" id="myordertable">
            <thead>
          <tr>
            <th align="left" class="tableDealTh" >Order Date</th>
            <th align="left" class="tableDealTh" >Order Number</th>              
			<th align="left" class="tableDealTh" style="width:230px;">Description</th>
			<th align="left" class="tableDealTh">Order Status</th>
          </tr>
          </thead>
        <tbody>
                <?php
                if(count($records_array1) > 0)
                {
                    foreach($records_array1 as $Row)
                    {   
                    ?>
						<tr class="tableDeal_" >
							<td>
							<?php
								echo  date("Y-m-d g:i:s A", strtotime($Row->order_date));                                                 
							?>
							</td>
                            <td>
							<?php
								echo  "<a href='javascript:void(0)' ship_to='".$Row->ship_to_address."' reward_detail='".$Row->title."' 
										reward_value='".$Row->card_value."' point_redeemed='".$Row->redeem_point_value."' 
										notes='".$Row->order_note."' class='notclass'>".$Row->order_number."</a>";
							?>
							</td>
							<td>
							<?php
								echo  $Row->title;                                                 
							?>
							</td>	
							<td>
							<?php
								if($Row->status==1)
									echo "Shipped";                                                 
								else if($Row->status==2)
									echo "Pending";   
								else if($Row->status==0)
									echo "Cancelled";                                                 
							?>
							</td>
							
						</tr>
                    <?php 
                    }
                }
                ?>
            </tbody>
                            </table>
        <?php
}
if(isset($_REQUEST['btncancelprofile']))
{
    header("Location:my-deals.php");
    exit;
}
if(isset($_REQUEST['btncancelpassword']))
{
    header("Location:my-deals.php");
    exit;
}
if(isset($_REQUEST['getuserpointsbalance']))
{
    $customer_id = $_REQUEST['customer_id'];
    $json_array = array();
    /*$Sql = "Select rw.earned_reward tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint 
             from 
            reward_user rw   left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id
             where rw.customer_id = ". $customer_id ." 
             group by rw.campaign_id , rw.location_id , rw.customer_id " ;
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("Select rw.earned_reward tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint 
             from 
            reward_user rw   left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id
             where rw.customer_id =? 
             group by rw.campaign_id , rw.location_id , rw.customer_id ",array($customer_id));

		$total_balance = 0;
		if($RS->RecordCount()>0)
		 {
				while($Row = $RS->FetchRow())
				{
					 $total_balance=$total_balance+$Row['tot_redeempoints'] + $Row['tot_sharingpoint']; 
				}
			}
		
		// start to remove giftcard balance
		
		$total_sum_gifcard=0;
		/*$Sql = "select giftcard_id from giftcard_order where user_id=".$customer_id ." and status!=0";
		$RS = $objDB->Conn->Execute($Sql);*/
		$RS = $objDB->Conn->Execute("select giftcard_id from giftcard_order where user_id=? and status!=?",array($customer_id,0));

		if($RS->RecordCount()>0)
		{
			while($Row = $RS->FetchRow())
			{
				/*$Sql1 = "select id,redeem_point_value from giftcards where id=".$Row['giftcard_id'] ;
				$RS1 = $objDB->Conn->Execute($Sql1);*/
				$RS1 = $objDB->Conn->Execute("select id,redeem_point_value from giftcards where id=?",array($Row['giftcard_id']));

				if($RS1->RecordCount()>0)
				{
					$total_sum_gifcard = $total_sum_gifcard + $RS1->fields['redeem_point_value'];
				}
			}
        }
		
		$total_balance = $total_balance - $total_sum_gifcard;
		
		// end to remove giftcard balance
		
        $json_array["point_balance"] = $total_balance;
         $json = json_encode($json_array);
	 echo $json;
	exit();
}
//End for unreserve deal //

// increse counter on view deal //

/**
 * @uses ajax call for campaign view 
 * @param customer_id,campaign_id
 * @used in pages :location-detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['campaign_view_ajax']))
{
    $json_array = array();
    /*$sql = "select * from customer_campaign_view where customer_id=".$_REQUEST['customer_id']." and campaign_id =".$_REQUEST['campaign_id'];
     $RS = $objDB->Conn->Execute($sql);*/
     $RS = $objDB->Conn->Execute("select * from customer_campaign_view where customer_id=? and campaign_id =?",array($_REQUEST['customer_id'],$_REQUEST['campaign_id']));
     if($RS->RecordCount() > 0)
     {
            /*$sql = "Update customer_campaign_view set visited_counter=visited_counter+1 , last_visited_datetime ='". date("Y-m-d H:i:s")."' where customer_id=".$_REQUEST['customer_id']." and campaign_id =".$_REQUEST['campaign_id'];*/
            $objDBWrt->Conn->Execute("Update customer_campaign_view set visited_counter=visited_counter+1 , last_visited_datetime =? where customer_id=? and campaign_id =?",array(date("Y-m-d H:i:s"),$_REQUEST['customer_id'],$_REQUEST['campaign_id']));
     }
     else{
         /*$sql = "insert into customer_campaign_view set visited_counter=1 , customer_id=".$_REQUEST['customer_id']." , campaign_id =".$_REQUEST['campaign_id']." , last_visited_datetime = '". date("Y-m-d H:i:s")."' ";*/
	$objDBWrt->Conn->Execute("insert into customer_campaign_view set visited_counter=? , customer_id=? , campaign_id =? , last_visited_datetime =?",array(1,$_REQUEST['customer_id'],$_REQUEST['campaign_id'],date("Y-m-d H:i:s")));

     }
     $json_array["status"] = "true";
         $json = json_encode($json_array);
	 echo $json;
	exit(); 
}

/**
 * @uses campaign counter
 * @param customer_id,campaign_id
 * @used in pages :campaign.php,campaign_facebook.php,header.hp
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['campaign_counter']))
{
    $json_array = array();
    /*$sql = "select * from customer_campaign_view where customer_id=".$_REQUEST['customer_id']." and campaign_id =".$_REQUEST['campaign_id'];
     $RS = $objDB->Conn->Execute($sql);*/
     $RS = $objDB->Conn->Execute("select * from customer_campaign_view where customer_id=? and campaign_id =?",array($_REQUEST['customer_id'],$_REQUEST['campaign_id']));
     if($RS->RecordCount() > 0)
     {
            /*$sql = "Update customer_campaign_view set visited_counter=visited_counter+1 , last_visited_datetime ='". date("Y-m-d H:i:s")."' where customer_id=".$_REQUEST['customer_id']." and campaign_id =".$_REQUEST['campaign_id'];*/
	$objDBWrt->Conn->Execute("Update customer_campaign_view set visited_counter=visited_counter+1 , last_visited_datetime =? where customer_id=? and campaign_id =?",array(date("Y-m-d H:i:s"),$_REQUEST['customer_id'],$_REQUEST['campaign_id']));
     }
     else{
        /* $sql = "insert into customer_campaign_view set visited_counter=1 , customer_id=".$_REQUEST['customer_id']." , campaign_id =".$_REQUEST['campaign_id']." , last_visited_datetime = '". date("Y-m-d H:i:s")."' ";*/
	$objDBWrt->Conn->Execute("insert into customer_campaign_view set visited_counter=? , customer_id=? , campaign_id =? , last_visited_datetime =?",array(1,$_REQUEST['customer_id'],$_REQUEST['campaign_id'],date("Y-m-d H:i:s")));
     }
   

     $json_array["status"] = "true";
         $json = json_encode($json_array);
	 echo $json;
	exit(); 
}

/**
 * @uses is merchant campaign viewed
 * @param category_id,mlatitude,mlongitude,dismile
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['is_mymerchant_campaign_viewed']))
{
   
    $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 $category_id=$_REQUEST['category_id'];
	// $dismile=$_REQUEST['dismile'];
         $dismile= 50;
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	 if(isset($_REQUEST['dismile']))
	 {
		
	 	$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
      }
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
        
         if($_REQUEST['firstlimit']=="" && $_REQUEST['lastlimit']=="")
         {
             $firstlimit=0;
             $lastlimit=9;
         }
         else
         {
             $firstlimit=$_REQUEST['firstlimit'];
             $lastlimit=$_REQUEST['lastlimit'];
         }
         
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id.") or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		 }
	 }
//         $limit_data = "SELECT  distinct c.id FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
//        WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh."  and ".$Where ."  ORDER BY distance,c.expiration_date";
         $id= $_REQUEST['customer_id'];
         
              
            /*$limit_data = "SELECT distinct c.id 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id 
left outer join customer_campaign_view views on views.campaign_id = cl.campaign_id
 WHERE l.active = 1 and c.is_walkin <> 1 ".$cat_str." and 
( c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id inner join merchant_groups mg on mg.id=ms.group_id
where ms.user_id=".$id.")
 or c.level =1 ) and l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=".$id." and ss.subscribed_status=1) and 
( (c.id not in ( select campaign_id from customer_campaigns where customer_id =".$id." and location_id=cl.location_id)) 
or (c.id in ( select campaign_id from customer_campaigns where customer_id =".$id." and location_id=cl.location_id and activation_status =0)) ) and  
(((acos(sin((".$mlatitude."*pi()/180)) * sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* pi()/180))))*180/pi())*60*1.1515 ) <=".$dismile."
 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0 
and c.id not in( select campaign_id from customer_campaign_view where customer_id=".$id.")";
 //echo $limit_data;
// exit;
         $RS_limit_data=$objDB->Conn->Execute($limit_data);*/
	$RS_limit_data=$objDB->Conn->Execute("SELECT distinct c.id 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id 
left outer join customer_campaign_view views on views.campaign_id = cl.campaign_id
 WHERE l.active = ? and c.is_walkin <> ? ".$cat_str." and 
( c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id inner join merchant_groups mg on mg.id=ms.group_id
where ms.user_id=?)
 or c.level =? ) and l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=? and ss.subscribed_status=?) and 
( (c.id not in ( select campaign_id from customer_campaigns where customer_id =? and location_id=cl.location_id)) 
or (c.id in ( select campaign_id from customer_campaigns where customer_id =? and location_id=cl.location_id and activation_status =?)) ) and  
(((acos(sin((".$mlatitude."*pi()/180)) * sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* pi()/180))))*180/pi())*60*1.1515 ) <=".$dismile."
 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=? and cl.offers_left>0 
and c.id not in( select campaign_id from customer_campaign_view where customer_id=?)",array(1,1,$id,1,$id,1,$id,$id,0,1,$id));
         
            
//		  $json_array['status'] = "true";
//		  $json_array['total_records'] = $RS_limit_data->fields['total'];
        
	 $json = json_encode($json_array);
	 echo $json;
	exit();
}

/**
 * @uses new campaign counter
 * @param category_id,mlatitude,mlongitude,dismile
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['new_campaign_counter']))
{
   
    $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 $category_id=$_REQUEST['category_id'];
	// $dismile=$_REQUEST['dismile'];
         $dismile= 50;
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	 if(isset($_REQUEST['dismile']))
	 {
		
	 	$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
      }
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
        
         if(isset($_REQUEST['firstlimit'])=="" && isset($_REQUEST['lastlimit'])=="")
         {
             $firstlimit=0;
             $lastlimit=9;
         }
         else
         {
             $firstlimit=$_REQUEST['firstlimit'];
             $lastlimit=$_REQUEST['lastlimit'];
         }
         
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id.") or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		 }
	 }
//         $limit_data = "SELECT  distinct c.id FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
//        WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh."  and ".$Where ."  ORDER BY distance,c.expiration_date";
         $id= $_REQUEST['customer_id'];
         
              
            /*$limit_data = "SELECT distinct c.id campid
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id 
left outer join customer_campaign_view views on views.campaign_id = cl.campaign_id
 WHERE l.active = 1 and c.is_walkin <> 1 ".$cat_str." and 
( c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id inner join merchant_groups mg on mg.id=ms.group_id
where ms.user_id=".$id.")
 or c.level =1 ) and l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=".$id." and ss.subscribed_status=1) and 
( (c.id not in ( select campaign_id from customer_campaigns where customer_id =".$id." and location_id=cl.location_id)) 
or (c.id in ( select campaign_id from customer_campaigns where customer_id =".$id." and location_id=cl.location_id and activation_status =0)) ) and  
(((acos(sin((".$mlatitude."*pi()/180)) * sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* pi()/180))))*180/pi())*60*1.1515 ) <=".$dismile."
 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0 
and c.id not in( select campaign_id from customer_campaign_view where customer_id=".$id.")";
 
         $RS_limit_data=$objDB->Conn->Execute($limit_data);*/

	$RS_limit_data=$objDB->Conn->Execute("SELECT distinct c.id campid
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id 
left outer join customer_campaign_view views on views.campaign_id = cl.campaign_id
 WHERE l.active = ? and c.is_walkin <> ? ".$cat_str." and 
( c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id inner join merchant_groups mg on mg.id=ms.group_id
where ms.user_id=?)
 or c.level =? ) and l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=? and ss.subscribed_status=?) and 
( (c.id not in ( select campaign_id from customer_campaigns where customer_id =? and location_id=cl.location_id)) 
or (c.id in ( select campaign_id from customer_campaigns where customer_id =? and location_id=cl.location_id and activation_status =?)) ) and  
(((acos(sin((".$mlatitude."*pi()/180)) * sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* pi()/180))))*180/pi())*60*1.1515 ) <=".$dismile."
 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=? and cl.offers_left>?
and c.id not in( select campaign_id from customer_campaign_view where customer_id=?)",array(1,1,$id,1,$id,1,$id,$id,0,1,0,$id));

          $campaign_list = "";
         while($Row_camps = $RS_limit_data->FetchRow())
         {
             $campaign_list .= $Row_camps['campid'].";";
             
         }
            
		  $json_array['status'] = "true";
                  $json_array['campaign_list'] = $campaign_list;
		  $json_array['total_records'] = $RS_limit_data->RecordCount();
        
	 $json = json_encode($json_array);
	 echo $json;
	exit();
}

/**
 * @uses new campaign counter for 24 hour
 * @param category_id,mlatitude,mlongitude,dismile
 * @used in pages :mymerchants.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['new_campaign_counter_for_24_hour']))
{
   
    $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 //$category_id=$_REQUEST['category_id'];
	// $dismile=$_REQUEST['dismile'];
         $dismile= 50;
	 $date_f = date("Y-m-d H:i:s");
	 
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	 /*
	 $mlatitude=$_COOKIE['mycurrent_lati'];
	 $mlongitude=$_COOKIE['mycurrent_long'];
	 */
	  
	  if(isset($_COOKIE['cat_remember']))
            {
                $category_id  = $_COOKIE['cat_remember'];
            }
            else
            {
                $category_id  = 0;
            }
	  
	 if(isset($_REQUEST['dismile']))
	 {
		
	 	$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
      }
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
        
         if(isset($_REQUEST['firstlimit'])=="" && isset($_REQUEST['lastlimit'])=="")
         {
             $firstlimit=0;
             $lastlimit=9;
         }
         else
         {
             $firstlimit=$_REQUEST['firstlimit'];
             $lastlimit=$_REQUEST['lastlimit'];
         }
         
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
		  /*
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id.") or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
	 */
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		 }
	 }
//         $limit_data = "SELECT  distinct c.id FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
//        WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh."  and ".$Where ."  ORDER BY distance,c.expiration_date";
         //$id= $_REQUEST['customer_id'];
         
          /*   
            $limit_data = "SELECT distinct c.id campid
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id 
left outer join customer_campaign_view views on views.campaign_id = cl.campaign_id
 WHERE l.active = 1 and c.is_walkin <> 1 ".$cat_str." and 
( c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id inner join merchant_groups mg on mg.id=ms.group_id
where ms.user_id=".$id.")
 or c.level =1 ) and l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=".$id." and ss.subscribed_status=1) and 
( (c.id not in ( select campaign_id from customer_campaigns where customer_id =".$id." and location_id=cl.location_id)) 
or (c.id in ( select campaign_id from customer_campaigns where customer_id =".$id." and location_id=cl.location_id and activation_status =0)) ) and  
(((acos(sin((".$mlatitude."*pi()/180)) * sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* pi()/180))))*180/pi())*60*1.1515 ) <=".$dismile."
 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0 
 and CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
 CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY ";
		*/
		/*$limit_data = "SELECT distinct c.id campid
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id WHERE l.active = 1 and c.is_walkin <> 1 ".$cat_str." and  
(((acos(sin((".$mlatitude."*pi()/180)) * sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* pi()/180))))*180/pi())*60*1.1515 ) <=".$dismile."
 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0 
 and CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
 CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY ";
 
         $RS_limit_data=$objDB->Conn->Execute($limit_data);*/
$RS_limit_data=$objDB->Conn->Execute("SELECT distinct c.id campid
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id WHERE l.active = ? and c.is_walkin <> 1 ".$cat_str." and  
(((acos(sin((".$mlatitude."*pi()/180)) * sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* pi()/180))))*180/pi())*60*1.1515 ) <=".$dismile."
 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=? and cl.offers_left>? 
 and CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
 CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY ",array(1,1,0));

          $campaign_list = "";
         while($Row_camps = $RS_limit_data->FetchRow())
         {
             $campaign_list .= $Row_camps['campid'].";";
             
         }
            
		  $json_array['status'] = "true";
                  $json_array['campaign_list'] = $campaign_list;
		  $json_array['total_records'] = $RS_limit_data->RecordCount();
        
	 $json = json_encode($json_array);
	 echo $json;
	exit();
}
// increse counter on view deal //

/**
 * @uses get deals counter expire soon
 * @param category_id,mlatitude,mlongitude,dismile
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['get_expire_soon_deals_counter']))
{
   $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 $category_id=$_REQUEST['category_id'];
	// $dismile=$_REQUEST['dismile'];
         $dismile= 50;
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	 if(isset($_REQUEST['dismile']))
	 {
		
	 	$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
      }
	  
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
   // 08 11 2013 remove offer left>0 condition
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
	// 08 11 2013 remove offer left>0 condition		
         if(isset($_REQUEST['firstlimit'])=="" && isset($_REQUEST['lastlimit'])=="")
         {
             $firstlimit=0;
             $lastlimit=9;
         }
         else
         {
             $firstlimit=$_REQUEST['firstlimit'];
             $lastlimit=$_REQUEST['lastlimit'];
         }
         
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id.") or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		 }
	 }
//         $limit_data = "SELECT  distinct c.id FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
//        WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh."  and ".$Where ."  ORDER BY distance,c.expiration_date";
         $id= $_REQUEST['customer_id'];
         
            /* $limit_data = "SELECT count(distinct c.id ) total 
                FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and 
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id where ms.user_id=$id)) 
and c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." and
 CONVERT_TZ(c.expiration_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
 CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY      
";
		
         $RS_limit_data=$objDB->Conn->Execute($limit_data);*/
	$RS_limit_data=$objDB->Conn->Execute("SELECT count(distinct c.id ) total 
                FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and 
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id where ms.user_id=?)) 
and c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status=?)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." and
 CONVERT_TZ(c.expiration_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
 CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY      
",array(1,$id,$id,1));
         
          $json_array['query'] = $limit_data;
		  $json_array['status'] = "true";
		  $json_array['old_total_records'] = $RS_limit_data->fields['total'];
        
		// start new code
		  /*$limit_data1 = "SELECT cl.permalink, c.id cid,c.title title,l.id locid,c.number_of_use
                FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and 
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id where ms.user_id=$id)) 
and c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." and
 CONVERT_TZ(c.expiration_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
 CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY      
"; 
      $RS_limit_data1=$objDB->Conn->Execute($limit_data1);*/
	 $RS_limit_data1=$objDB->Conn->Execute("SELECT cl.permalink, c.id cid,c.title title,l.id locid,c.number_of_use
                FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and 
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id where ms.user_id=?)) 
and c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status=?)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." and
 CONVERT_TZ(c.expiration_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
 CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY      
",array(1,$id,$id,1));

		$cnt=0;
		$camp_ary=array();
	  while($Row = $RS_limit_data1->FetchRow())
	  {
		  
		 
		 $array_where_camp=array();
		 $array_where_camp['campaign_id'] = $Row['cid'];
		 $array_where_camp['customer_id'] = $id;
		 $array_where_camp['referred_customer_id'] = 0;
		 $array_where_camp['location_id'] = $Row['locid'];
		 $RS_camp = $objDB->Show("reward_user", $array_where_camp);

		  $where_x1 = array();
		  $where_x1['campaign_id'] = $Row['cid'];
		  $where_x1['location_id'] = $Row['locid'];
		  $campLoc = $objDB->Show("campaign_location", $where_x1);
						  
		 if($RS_camp->RecordCount()>0 && $Row['number_of_use']==1)
		 {
			
		 } 
		 elseif ($RS_camp->RecordCount()>0 && ($Row['number_of_use']==2 || $Row['number_of_use']==3 ) && $campLoc->fields['offers_left']==0) 
		 {
			
		 }
		 else
		 {
			   if (in_array($Row['cid'],$camp_ary))
			  {
			  }
			  else
			  {
				array_push($camp_ary,$Row['cid']);
				$records[$cnt] = get_field_value($Row);
				$cnt++;
			  }
			  
			   
		 }
	  }
	  //count($camp_ary);
	  //$json_array['total_records'] = $cnt;
	  $json_array['total_records'] = count($camp_ary);
	  $json_array["records"]= $records;
	  // end new code
		
	 $json = json_encode($json_array);
	 echo $json;
	exit();   
}
/// set user current location ////
/**
 * @uses set user current location
 * @param curr_latitude,curr_timezone
 * @used in pages :my-delas.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['set_user_current_location']))
{
   // print_r($_REQUEST);
    if($_REQUEST['curr_latitude']!="" && $_REQUEST['curr_timezone'] != "" )
	{
		if(isset($_REQUEST['curr_location']) && $_REQUEST['curr_location']!="")
		{
			/*$update_sql = "Update customer_user set current_location ='".$_REQUEST['curr_location']."' , curr_latitude='".$_REQUEST['curr_latitude']."' , curr_longitude='".$_REQUEST['curr_longitude']."' , curr_timezone='".$_REQUEST['curr_timezone']."' where id=".$_REQUEST['customer_id'];
			//echo $update_sql;
			$objDB->Conn->Execute($update_sql);*/
$objDBWrt->Conn->Execute("Update customer_user set current_location =? , curr_latitude=? , curr_longitude=? , curr_timezone=? where id=?",array($_REQUEST['curr_location'],$_REQUEST['curr_latitude'],$_REQUEST['curr_longitude'],$_REQUEST['curr_timezone'],$_REQUEST['customer_id']));
		}
    }
    $json_array['status'] = "true";
   $json = json_encode($json_array);
	 echo $json;
	exit();
}

if(isset($_REQUEST['set_read_notofication']))
{
	$json_array = array();
    /*$update_sql = "Update notification set is_read=1 where id=".$_REQUEST['notification_id']." and customer_id=".$_REQUEST['customer_id'];
    //echo $update_sql;
    $objDB->Conn->Execute($update_sql);*/
$objDBWrt->Conn->Execute("Update notification set is_read=? where id=? and customer_id=?",array(1,$_REQUEST['notification_id'],$_REQUEST['customer_id']));
    $json_array['status'] = "true";
    $json = json_encode($json_array);
	echo $json;
	exit();
}

/// set user current location ///
/**
 * @uses set read notification
 * @param notification_id,customer_id
 * @used in pages :header.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnGetActiveDealsOnCampaignPage']))
{
    $merchantid = $_REQUEST['merchant_id'];
	 $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 $loc_id=$_REQUEST['loc_id'];
	 $category_id=$_REQUEST['category_id'];
	 $dismile=$_REQUEST['dismile'];
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
	 
	 if(isset($_REQUEST['dismile']))
	 {
		
	 	$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
                $Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
         }
		 
		 /*
   //$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
        */
		if($_REQUEST['customer_id']!="")
		 {
			  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";
		 }
		 else
		 {
			 $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
		 }
		 
		 if(isset($_REQUEST['firstlimit']) && isset($_REQUEST['lastlimit']))
		 {
			 if($_REQUEST['firstlimit']=="" && $_REQUEST['lastlimit']=="")
			 {
				 $firstlimit=0;
				 $lastlimit=9;
			 }
			 else
			 {
				 $firstlimit=$_REQUEST['firstlimit'];
				 $lastlimit=$_REQUEST['lastlimit'];
			 }
         }
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id where  ms.user_id=".$customer_id.") or c.level =1 ) ";
		
		// 02-10-2013 dist list deal display if cust in dist list and reserved
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id  in ( select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id and activation_status=1  and cl.campaign_type=3)  )
) ";
		// 02-10-2013
		
		// 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , in this private deal also display if not subscribed
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id.") or c.level =1 ) ";
		// 03-10-2013
		
		// 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private=1 and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         if(isset($_REQUEST['category_id']))
	 {
		 if($_REQUEST['category_id']==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";

		 }
	 }
         /*$limit_data = "SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=".$merchantid." and    l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh."  and ".$Where ." and l.id=".$loc_id." ORDER BY distance";
            
//
//and (
//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
 
    $RS_limit_data=$objDB->Conn->Execute($limit_data);*/
$RS_limit_data=$objDB->Conn->Execute("SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=? and    l.active = ?  ".$cust_where." ".$cat_str  ."  ".$date_wh."  and ".$Where ." and l.id=? ORDER BY distance",array($merchantid,1,$loc_id));
	
	// 10 10 2013		 
	$records_all=array();
    if($RS_limit_data->RecordCount()>0)
	{
		$json_array['query']= $RS_limit_data;
		$json_array['status'] = "true";
		//$json_array['total_records'] = $RS_limit_data->RecordCount();
		$count=0;
		$records=array();
		while($Row = $RS_limit_data->FetchRow())
		{		
			if($_REQUEST['customer_id']!="")	
			{
				$array_where_camp2=array();
				$array_where_camp2['campaign_id'] = $Row['cid'];
				$array_where_camp2['customer_id'] = $_REQUEST['customer_id'];
				$array_where_camp2['location_id'] = $Row['locid'];
				$RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
				//echo $RS_cust_camp->RecordCount()."-";
				$reserved=$RS_cust_camp->RecordCount();
					
				$array_where_camp=array();
				$array_where_camp['campaign_id'] = $Row['cid'];
				$array_where_camp['customer_id'] = $_REQUEST['customer_id'];
				$array_where_camp['referred_customer_id'] = 0;
				$array_where_camp['location_id'] = $Row['locid'];
				$RS_camp = $objDB->Show("reward_user", $array_where_camp);
				
				$array_where_camp1=array();
				$array_where_camp1['campaign_id'] = $Row['cid'];
				$array_where_camp1['location_id'] = $Row['locid'];
				$campLoc = $objDB->Show("campaign_location", $array_where_camp1);
				
				
				if($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && $Row['number_of_use']==1)
				 {
					//echo "1 ".$Row->cid." ".$storeid;
				 }                       
				 elseif($RS_cust_camp->RecordCount()>0 && $RS_camp->RecordCount()>0 && ($Row['number_of_use']==2 || $Row['number_of_use']==3) && $campLoc->fields['offers_left']==0)
				 {
					 //echo "2 ".$Row->cid." ".$storeid;
				 }
				 elseif($RS_cust_camp->RecordCount()==0 && $RS_camp->RecordCount()==0 && $campLoc->fields['offers_left']==0)
				 {   
					 //echo "3 ".$Row->cid." ".$storeid;
				 }
				 else
				 {
					//echo "3 else";
					$records[$count] = get_field_value($Row);
					$count++;
					//echo $count;
				 }
			}
			else
			{
				$records[$count] = get_field_value($Row);
				$count++;
			}
		}
		
			$json_array["records"]= $records;
			// 31-7-2013 add line
			$json_array["total_records"]= $count;
			// 31-7-2013 add line
			
            while($Row = $RS_limit_data->FetchRow())
			{
				$records_all[$count] = get_field_value($Row);
				$count++;
			}
			$json_array["marker_records"]= $records_all;
            $json_array['marker_total_records'] = $RS_limit_data->RecordCount();
	 }
	 else
	 {
          
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
                   
		  $json_array["marker_records"]= "";
          $json_array['marker_total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 
	// 10 10 2013	  
    
	 $json = json_encode($json_array);
	 echo $json;
	exit();
}

/**
 * @uses get nearest location
 * @param dismile,mlatitude,customer_id
 * @used in pages :search-deal.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetNearestLocations']))
{
    
	 $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
	 if(isset($_REQUEST['dismile']))
         {
	 $dismile=$_REQUEST['dismile'];
         }
         else{
             $dismile = 50;
         }
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_REQUEST['mlatitude'];
	 $mlongitude=$_REQUEST['mlongitude'];
         $Where ="";
	 if(isset($_REQUEST['dismile']))
	 {
		
	 	$Where = "and (69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
                $Sql = "SELECT sl.* FROM locations sl WHERE ".$Where;
         }
		 /*
   //$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
        */
		
		if($_REQUEST['customer_id']!="")
		 {
			  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";
		 }
		 else
		 {
			 $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
		 }

          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id where  ms.user_id=".$customer_id.") or c.level =1 ) ";
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         
         /*$limit_data = "SELECT distinct l.city,l.state,l.country FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE l.active = 1  ".$cust_where." ".$date_wh." ".$Where ." group by l.city ORDER BY 
		(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) limit 3";
            
//
//and (
//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
 
         $RS_limit_data=$objDB->Conn->Execute($limit_data);*/
		
		
		$RS_limit_data=$objDB->Conn->SelectLimit("SELECT distinct l.city,l.state,l.country FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE l.active = ?  ".$cust_where." ".$date_wh." ".$Where ." group by l.city ORDER BY 
		(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        )",3,0,array(1));
        
        
       /*
        $RS_limit_data=$objDB->Conn->Execute("SELECT distinct l.city,l.state,l.country FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE l.active = ?  ".$cust_where." ".$date_wh." ".$Where ." group by l.city ORDER BY 
		(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) limit 3",array(1));
        */

         if($RS_limit_data->RecordCount()>0)
	 {
              //$json_array['query']= $limit_data;
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS_limit_data->RecordCount();
		  $count=0;
		  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
                  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records_all[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["marker_records"]= $records_all;
                    $json_array['marker_total_records'] = $RS_limit_data->RecordCount();
	 }
	 else
	 {
               //$json_array['query']= $limit_data;
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
                   
		  $json_array["marker_records"]= "";
                    $json_array['marker_total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	exit();
}

/**
 * @uses get map for location
 * @param campaignid,locationid
 * @used in pages :qr-location.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getMapForLocation']) &&  $_REQUEST['getMapForLocation']== "yes")
{


             
                    $campaign_id = $_REQUEST['campaignid'];
					$location_id = $_REQUEST['locationid'];
                    /*$sql  = "SELECT * from campaign_location WHERE (offers_left>0 and campaign_id = ".$campaign_id." and active=1)";
                   // echo $sql  ;
                    $RSdata = $objDB->Conn->Execute($sql);*/
		$RSdata = $objDB->Conn->Execute("SELECT * from campaign_location WHERE (offers_left>0 and campaign_id = ? and active=?)",array($campaign_id,1));
					
                    if($RSdata->RecordCount()<= 0) 
                    { ?>
                      
                    <?php }

                    else
                    { ?>
       <link href="<?php echo ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">
	   
        <div id="div_msg" class="dealtitle" style="display:none;height:auto"></div>
                        <input type="hidden" name="txt_activation_code" id="txt_activation_code" value="<?php echo $_REQUEST['activationcode']; ?>" />
                        <input type="hidden" name="hdn_location" id="hdn_location" value="" />
<input type="hidden" name="redirection_url" id="redirection_url" value="" />                      
					  <div style="text-transform:capitalize;padding:10px;font-size:15px;" style="height:auto"> Select merchant location to view your offer</div>
                       <div id="map_locations" class="fancybox.iframe" style="width:400px;height:250px" ></div>
                    <!--   <div id="map_locations" class="fancybox.iframe" style="width:410px;height:329px" ></div> -->
                      <div align="center" style="margin-top:7px;"> <input type="submit" name="activatedeal" id="activatedeal" value="View" disabled/>
                        </div>
                       <script src="<?php echo WEB_PATH ?>/js/jquery-1.7.2.min.js" type="text/javascript"></script>
                       <script type='text/javascript' src="https://maps.google.com/maps/api/js?sensor=true&.js"></script>
<script type='text/javascript' src="https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>	
<script>

      var map = null;
     var infowindow = null;
    var infowindowcontent =[];
    var markerArray = [];
var locations = [
    <?php
    $count =1;
    $total = $RSdata->RecordCount();
	$hidden_redirecturl = "";
     while($Row_u = $RSdata->FetchRow())
                        { 
         
                        $Sql_s = "SELECT * from locations WHERE id=".$Row_u['location_id'];
                 
				 /*$redirect_query = "select permalink from campaign_location where campaign_id=".$Row_u['campaign_id']." and location_id=".$Row_u['location_id'];
				$redirect_RS = $objDB->Conn->Execute($redirect_query );*/
				$redirect_RS = $objDB->Conn->Execute("select permalink from campaign_location where campaign_id=? and location_id=?",array($Row_u['campaign_id'],$Row_u['location_id']) );

				$r_url = $redirect_RS->fields['permalink'];
                    //$RS_loc = $objDB->Conn->Execute($Sql_s);
			$RS_loc = $objDB->Conn->Execute("SELECT * from locations WHERE id=?",array($Row_u['location_id']));

                    $storeid =  $RS_loc->fields['id'];
                $lat = $RS_loc->fields['latitude'];
                   $lng = $RS_loc->fields['longitude'];
				   $campaign_id=$Row_u['campaign_id'];

                   $location_name = $RS_loc->fields['location_name'];

                   $cat_id =1;
                   $address = $RS_loc->fields['address'] . ", " . $RS_loc->fields['city'] . ", " . $RS_loc->fields['state'] . ", " . $RS_loc->fields['zip'] . ", " . $RS_loc->fields['country'];
                  $hidden_redirecturl .="<input type='hidden' name='hdn_url' class='hdn_url' campid='".$Row_u['campaign_id']."' locid='".$Row_u['location_id']."' value='".$Row_u['permalink']."' > ";
                   $MARKER_HTML = "<div  style='clear:both; width:auto;font:Arial, Helvetica, sans-serif;'>";
                      $busines_name = "";
//                   if ($RS_loc->fields['location_name']!= "") {
//                       $busines_name = $RS_loc->fields['location_name'];
//                   } else {
                       $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $storeid);
                       if (trim($arr[0]) == "") {
                           unset($arr[0]);
                           $arr = array_values($arr);
                       }
                       $json = json_decode($arr[0]);
                       $busines_name = $json->bus_name;
                  //  }
            $deal_distance1 = "";

                   if(isset($_COOKIE['mycurrent_lati']) && isset($_COOKIE['mycurrent_lati']))
                    {
                            $from_lati1=$_COOKIE['mycurrent_lati'];

                            $from_long1=$_COOKIE['mycurrent_long'];

                            $to_lati1=$RS_loc->fields['latitude'];

                            $to_long1=$RS_loc->fields['longitude'];

                            $deal_distance1 = $objJSON->distance($from_lati1, $from_long1, $to_lati1, $to_long1, "M")."Mi";
                    }
                                                        
                   $MARKER_HTML .= "<div><b>" . $busines_name . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>" . $deal_distance1 . "</b></div>";
                   $MARKER_HTML .= "<div>" . $address . "</div>";
                 
                   $MARKER_HTML .= "</div>";
//                   $count++;
                  
                ?>
                      ["<?=$MARKER_HTML?>", <?=$lat?>, <?=$lng?>, <?=$count?>,"<?=$busines_name?>",'<?=$cat_id?>',<?=$storeid?>,<?=$campaign_id?>,'<?=$r_url?>']
                <?
                if($count != $total) echo ",";
							$count++;
                        }
                        
    ?>
];
$("#activatedeal").live("click",function(){
//alert(jQuery("#redirection_url").val());
//return false;
//alert($("#hdn_location").val());
if ($("#hdn_location").val() =="")
	{
		alert("Please Click On Mapmarker To Select Location Before Activating Your Offer");
		return;
	}
	
	
var lval =0;
if($("#hdn_location").val() !="")
    {
        lval  = $("#hdn_location").val();
    }
  var err_msg = "";
  
window.parent.location.href = jQuery("#redirection_url").val();  
  

       return false;
});

 function initialize() {
   
                                      var myOptions = {
                                          zoom:parseInt(10),
						center: new google.maps.LatLng(<?=$lat?>, <?=$lng?>),
						mapTypeControl: true,
						mapTypeControlOptions: {
							style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
						},
						
						zoomControl: true,
						zoomControlOptions: {
						    style: google.maps.ZoomControlStyle.LARGE,
						    position: google.maps.ControlPosition.LEFT_CENTER
						},

						navigationControl: true,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
                                       
					map = new google.maps.Map(document.getElementById("map_locations"), myOptions);
                                        
                                        //google.maps.event.trigger(map,"resize");
                                        //map.checkResize();

					google.maps.event.addListener(map, 'click', function() {
//						infowindow.close();
//                                                   for (var prop in markerArray) {
//                                                                markerArray[prop].setIcon('<?=WEB_PATH?>/images/pin-small.png');
//                                                    }
													
                                                  
					});
                                        
                                       // Add markers to the map
					// Set up markers based on the number of elements within the myPoints array
					for (var i = 0; i < locations.length; i++) {
					
						createMarker(new google.maps.LatLng(locations[i][1], locations[i][2]), locations[i][0], locations[i][3], locations[i][4],locations[i][5],locations[i][6],locations[i][8]);
					}
					// by deafult
                                        if(locations.length > 1)
                                        {
                                            // start to set map possition in center to show all markers

                                            var latlngbounds = new google.maps.LatLngBounds();
                                            for (var i = 0; i < locations.length; i++) 
                                            {
                                                    //alert(locations[i][1]);
                                                    //alert(locations[i][2]);

                                               var lat = locations[i][1];
                                               var lng = locations[i][2];

                                               var latlng2 = new google.maps.LatLng(lat, lng);
                                               latlngbounds.extend(latlng2);
                                            }

                                            map.setCenter(latlngbounds.getCenter());
                                            map.fitBounds(latlngbounds); 

                                            // end to set map possition in center to show all markers
                                        }
                                      
                                                                       
				 }
                infowindow = new google.maps.InfoWindow({
					size: new google.maps.Size(150, 50)
				});
				
                                             function createMarker(latlng,myContent , myNum, myTitle,id,lid,rurl) {
				        var cat_id = id; 
					var contentString = myContent;
					var marker = new google.maps.Marker({
						position: latlng,
						map: map,
						zIndex: Math.round(latlng.lat() * -100000) << 5,
						title: myTitle,
						icon: new google.maps.MarkerImage('<?php echo ASSETS_IMG?>/c/pin-small.png')
					});
                                          // marker.setVisible(false);
                                           
					google.maps.event.addListener(marker, 'click', function() {
                                             for (var prop in markerArray) {
                                                 markerArray[prop].setIcon('<?php echo ASSETS_IMG?>/c/pin-small.png');
                                            }
						infowindow.setContent(contentString);
						infowindow.open(map, marker);
                                                       
                                                       $("#div_msg").val("");
                                                       $("#div_msg").css("display",'none');
                                                marker.setIcon('<?php echo ASSETS_IMG?>/c/pin-small-blue.png');
                                                $("#activatedeal").removeAttr("disabled");
					  jQuery("#hdn_location").val(lid);
					  jQuery("#redirection_url").val(rurl);
                                                //parent.document.getElementById("slider_iframe").src = parent.document.getElementById("slider_iframe").src+"&order_by_cat="+cat_id;
					});
                                      
					
                                        infowindowcontent[lid]=myContent;
					markerArray[lid]=marker;
                                        
					//markerArray.push(marker); //push local var marker into global array
				}
				initialize();
                                               
                         
</script>
                    <?php echo $hidden_redirecturl; }
                

             
}

/**
 * @uses button activation code login or not
 * @param activationcode
 * @used in pages :qr-location.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['btnactivationcodeloginornot']) )
{
    
    
     if(!isset($_SESSION['customer_id'])){
         
        $json_array = array();
	
	 $json_array['status'] = "false";
	 $url = WEB_PATH."/#".$_REQUEST['activationcode'];
	 $json_array['link'] =WEB_PATH."/register.php?url=".urlencode($url);
        		  $json_array['is_profileset'] = 1;
                          $json = json_encode($json_array);
                        echo $json;
	 exit();
	}
        else
        {
            $json_array = array();
	
	 $json_array['status'] = "true";
	 
        		  $json_array['is_profileset'] = 1;
                          $json = json_encode($json_array);
        echo $json;
	 exit();
        }
      
      
    
}

/**
 * @uses check on share whether login or not
 * @param 
 * @used in pages :campaign.php,location-details.php,my-deals.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnsharingbuttonloginornot']) &&  $_REQUEST['btnsharingbuttonloginornot']== "true")
{
    
    
     if(!isset($_SESSION['customer_id'])){
         
        $json_array = array();
	
	 $json_array['status'] = "false";
	 $json_array['link'] =WEB_PATH."/register.php?url=".urlencode($_SERVER['HTTP_REFERER']);
        		  $json_array['is_profileset'] = 1;
                          $json = json_encode($json_array);
                        echo $json;
	 exit();
	}
        else
        {
            $json_array = array();
	
	 $json_array['status'] = "true";
	 
        		  $json_array['is_profileset'] = 1;
                          $json = json_encode($json_array);
        echo $json;
	 exit();
        }
      
      
    
}

/**
 * @uses check user last current latitude and longitude saved or not
 * @param 
 * @used in pages :campaign.php,location-details.php
 * @author Sangeeta
 * @return type json array
 */

if(isset($_REQUEST['check_user_last_saved_current']))
{
     $json_array = array();
    $status = true;
   if(isset($_SESSION['customer_id']))
    {
        if($_SESSION['customer_info']['current_location'] != "")
        {
            if($_SESSION['customer_info']['curr_latitude'] != "" && $_SESSION['customer_info']['curr_longitude'] != "")
            {
                $status = false;
                    $mlatitude = $_SESSION['customer_info']['curr_latitude'];	
                $mlongitude = $_SESSION['customer_info']['curr_longitude'];

                $cookie_life = time() + 31536000;
                   setcookie('searched_location', $_SESSION['customer_info']['current_location'], $cookie_life);
                setcookie('mycurrent_lati', $mlatitude, $cookie_life);
                setcookie('mycurrent_long', $mlongitude, $cookie_life);
                $_SESSION['mycurrent_lati']=$mlatitude;
                $_SESSION['mycurrent_long']=$mlongitude;
                 $json_array['profile_set'] = "yes";
                 $json_array['mlatitude'] = $mlatitude;
        $json_array['mlongitude'] = $mlongitude;
         $json = json_encode($json_array);
        echo $json;
	 exit();
            }
        }
    }
    if($status)
    {
        $json_array['profile_set'] = "no";
         $json_array['mlatitude'] = "";
        $json_array['mlongitude'] = "";
        
         $json = json_encode($json_array);
        echo $json;
	 exit();
    }
}

/**
 * @uses get latitude , longitude and zipcode
 * @param searched_location
 * @used in pages :campaign.php,my-details.php,mymerchants.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getlatitudelongitude_zipcode']))
{
     $json_array = array();
	
    
     $zip = repalce_char($_REQUEST['searched_location']);
        $locc = GetLatitudeLongitude($zip);
        if($locc !="")
        {                                        
                $mlatitude = $locc["location"]["latitude"];	
                $mlongitude = $locc["location"]["longitude"];

                $cookie_life = time() + 31536000;
                setcookie('mycurrent_lati', $mlatitude, $cookie_life);
                setcookie('mycurrent_long', $mlongitude, $cookie_life);
                $_SESSION['mycurrent_lati']=$mlatitude;
                $_SESSION['mycurrent_long']=$mlongitude;	
        }
        else
        {					  
                $mlatitude = "";	
                $mlongitude = "";
        }
        $json_array['mlatitude'] = $mlatitude;
        $json_array['mlongitude'] = $mlongitude;
         $json = json_encode($json_array);
        echo $json;
	 exit();
    
}

/**
 * @uses get latitude , longitude and zipcode
 * @param searched_location
 * @used in pages :campaign.php,my-details.php,mymerchants.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getlatitudelongitude_zipcode1']))
{
     $json_array = array();   
	 $to_lati=$_REQUEST['to_lat'];
	 $to_long=$_REQUEST['to_lng'];
     $zip = repalce_char($_REQUEST['searched_location']);
        $locc = GetLatitudeLongitude($zip);
        if($locc !="")
        {                                        
                $mlatitude = $locc["location"]["latitude"];	
                $mlongitude = $locc["location"]["longitude"];

                $cookie_life = time() + 31536000;
                setcookie('mycurrent_lati', $mlatitude, $cookie_life);
                setcookie('mycurrent_long', $mlongitude, $cookie_life);
                $_SESSION['mycurrent_lati']=$mlatitude;
                $_SESSION['mycurrent_long']=$mlongitude;
				
				echo "<b>Miles Away : </b>".$objJSON->distance($mlatitude, $mlongitude, $to_lati, $to_long, "M");
				exit();	
        }
        else
        {					  
                $mlatitude = "";	
                $mlongitude = "";
        }
        $json_array['mlatitude'] = $mlatitude;
        $json_array['mlongitude'] = $mlongitude;
        $json = json_encode($json_array);
        echo $json;
		exit();
    
}


/**
 * @uses get latitude , longitude from address
 * @param $_zipcodeaddress
 * @used in pages :
 * @author Sangeeta
 * @return type json array
 */
function GetLatitudeLongitude($_zipcodeaddress)
{

$_zipcodeaddress = urlencode ($_zipcodeaddress);
$_geocode= file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$_zipcodeaddress."&sensor=false");					
$_geojson= json_decode($_geocode,true);

	if( $_geojson['status']=='OK')
	{					
		$_lat= $_geojson['results'][0]['geometry']['location']['lat'];
		$_lng= $_geojson['results'][0]['geometry']['location']['lng'];
		$_address = $_geojson['results'][0]['formatted_address'];						
	
		$_output = array (
				"location" => 
				array (									
					"latitude" => "$_lat" ,
					"longitude"  => "$_lng",
					"address"  => "$_address[0]",
					"city"  => "$_address[1]",
					"zip"  => "$_address[2]",
					"state"  => "$_address[2]",
					"country"  => "$_address[2]"
					),
					);								
	}
	else
	{
		$_output= '';						
	}
	return $_output;

}
function repalce_char($value){
	$replace = array(",","&nbsp;","\n","\t", "\r","&amp;", "&raquo;","$");
	$value = str_replace($replace," ",$value);
	$value = strip_tags($value);
	$value = preg_replace('/\s+/',' ',$value);
	$value = trim($value);
	$value = mysql_escape_string($value);
	return $value;
}
if(isset($_REQUEST['set_not_supported_session_value']))
{
    $_SESSION['not_supported_geolocation']= "no";
    echo  $_SESSION['not_supported_geolocation'];
}

/**
 * @uses get active deals count on location page
 * @param loc_id
 * @used in pages :location_detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetActiveDealsCountOnLocationPage']))
{
    
	$json_array = array();
	$records = array();
	$records_all=array();
	$json_array1 = array();
	$array_where_camp1=array();
	
	$loc_id=$_REQUEST['loc_id'];
	 
	$array_where_camp1['id'] = $loc_id;
	$RS_camp1 = $objDB->Show("locations", $array_where_camp1);
	
	$merchantid = $RS_camp1->fields['created_by'];
	
	if(isset($_COOKIE['miles_cookie']))
	{
		$dismile=$_COOKIE['miles_cookie'];
	}
	else{
		$dismile= 50 ;
	}
	
	$cust_id="";
	if(isset($_SESSION['customer_id']))
	{
		$cust_id=$_SESSION['customer_id'];
	}
	
	if(isset($_COOKIE['cat_remember']))
	{
		$category_id = $_COOKIE['cat_remember'];
	}
	else{
		$category_id = 0;
	}
										
	 //$category_id=$_REQUEST['category_id'];
	 //$dismile=$_REQUEST['dismile'];
	 $date_f = date("Y-m-d H:i:s");
	 $mlatitude=$_COOKIE['mycurrent_lati'];
	 $mlongitude=$_COOKIE['mycurrent_long'];
	 
	 if(isset($dismile))
	 {
		
	 	$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
                $Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
     }
		 
		 /*
   //$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
   $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
        */
		if($cust_id!="")
		 {
			  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
		 }
		 else
		 {
			 $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
		 }
		 
		 if(isset($_REQUEST['firstlimit']) && isset($_REQUEST['lastlimit']))
		 {
			 if($_REQUEST['firstlimit']=="" && $_REQUEST['lastlimit']=="")
			 {
				 $firstlimit=0;
				 $lastlimit=9;
			 }
			 else
			 {
				 $firstlimit=$_REQUEST['firstlimit'];
				 $lastlimit=$_REQUEST['lastlimit'];
			 }
         }
          $cust_where ="";
	
	  $cust_where ="";
        
          $cat_str = "";
	 if($cust_id!="")
	 {
		$customer_id = $cust_id;
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id where  ms.user_id=".$customer_id.") or c.level =1 ) ";
		
		// 02-10-2013 dist list deal display if cust in dist list and reserved
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id  in ( select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id and activation_status=1  and cl.campaign_type=3)  )
) ";
		// 02-10-2013
		
		// 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , in this private deal also display if not subscribed
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id.") or c.level =1 ) ";
		// 03-10-2013
		
		// 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private=1 and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013
	 }
	 else{
		 $cust_where = " and c.level=1 ";
	 }
         if(isset($category_id))
	 {
		 if($category_id==0)
		 {
			$cat_str = "";
		 }
		 else
		 {
			$cat_str = " and c.category_id = ".$category_id." and c.category_id in(select cat.id from categories cat where cat.active=1) ";

		 }
	 }
         /*$limit_data = "SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=".$merchantid." and    l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh."  and ".$Where ." and l.id=".$loc_id." ORDER BY distance";
            
//
//and (
//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";

         $RS_limit_data=$objDB->Conn->Execute($limit_data);*/
	$RS_limit_data=$objDB->Conn->Execute("SELECT l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by ,(((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=? and    l.active = ?  ".$cust_where." ".$cat_str  ."  ".$date_wh."  and ".$Where ." and l.id=? ORDER BY distance",array($merchantid,1,$loc_id));

         if($RS_limit_data->RecordCount()>0)
	 {
              $json_array['query']= $limit_data;
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS_limit_data->RecordCount();
		  $count=0;
		  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
                  while($Row = $RS_limit_data->FetchRow())
		  {
		   	$records_all[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["marker_records"]= $records_all;
                    $json_array['marker_total_records'] = $RS_limit_data->RecordCount();
	 }
	 else
	 {
               $json_array['query']= $limit_data;
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
                   
		  $json_array["marker_records"]= "";
                    $json_array['marker_total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	exit();
}

/**
 * @uses review notification
 * @param days,customer_id
 * @used in pages :getrating.php,myreviews.php,profile-left.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['review_notification']))
{
	$customer_id = $_REQUEST['customer_id'];
    
        $days_cond = "";
	if(isset($_REQUEST['days']))
	{
		if($_REQUEST['days'] != "")
		{
				$days_cond = " and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date))  <=".$_REQUEST['days'];
		}
		else
		{
			$days_cond = "";
		}
	}
	else
	{
		$days_cond = "";
	}
   /*echo $Sql = "Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)   ) last_redeem_employee , rw.reward_date ,l.*,c.* , rw.campaign_id campaign_id, rw.location_id location_id 
   , rw.coupon_code_id coupon_id ,l.id location_id  , max(redeem_date) last_redeemdate 
 from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id = ".$customer_id." 
and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=30 and review_rating_visibility=1 
group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate";*/


  //$RS = $objDB->Conn->Execute($Sql);
	$RTotal = $objDB->Conn->Execute("select count(*) total from (Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)) last_redeem_employee , max(redeem_date) last_redeemdate from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id = ? and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=? and review_rating_visibility=? group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate) reward_user",array($customer_id,30,1));

	$total = $RTotal->FetchRow();

	$total = $total['total'];
	if(isset($_REQUEST['check'])){
		echo $total;
		exit;
	}

	$RS = $objDB->Conn->Execute("Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)) last_redeem_employee , rw.reward_date ,l.location_name,l.address,l.city,l.state,l.zip,l.country,c.title , rw.campaign_id campaign_id, rw.location_id location_id , max(redeem_date) last_redeemdate from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id = ? and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=? and review_rating_visibility=? group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate limit ".$_REQUEST[iDisplayStart].",".$_REQUEST[iDisplayLength],array($customer_id,30,1));

	

	$Json = array();
	$Json['iDisplayLength'] = $_REQUEST['iDisplayLength'];
	$Json['iDisplayStart'] = $_REQUEST['iDisplayStart'];
	$Json['iTotalDisplayRecords'] = $total;
	$Json['sEcho'] = $_REQUEST['sEcho'];
	$Json['sSearch'] = $_REQUEST['sSearch'];
	$Json['aaData'] = array();
	if($RS->RecordCount()>0)
	 {
		  $k=0;
		  while($Row = $RS->FetchRow())
		  {
			$Json['aaData'][$k][]=  date("Y-m-d g:i:s A", strtotime($Row['reward_date'] ));
		   	$address = $Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip'].", ".$Row['country'];
			$location = "<a href='" . $Row['location_permalink'] . "'>" . $Row['location_name'] . "</a><br/>";
                        $Json['aaData'][$k][]=$location .$address;
                        $Json['aaData'][$k][]=$Row['title'];
                        //$Json['aaData'][$k][]='<a href="javascript:void(0)" class="rateit2" empid="'.$Row['last_redeem_employee'].'" campid="'.$Row['campaign_id'].'" locid="'.$Row['location_id'].'" >Rate and Review </a>';
                        $Json['aaData'][$k][]="<a href='javascript:void(0)' class='rateit2' empid='".$Row['last_redeem_employee']."' campid='".$Row['campaign_id']."' locid='".$Row['location_id']."'>Rate and Review</a>";
                        $k++;
		  }
		 
	 }
	echo json_encode($Json);
         
         exit;
	 
}

/**
 * @uses give ratings
 * @param customer_id,camp_id,loc_id
 * @used in pages :getrating.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['giveratings']))
{
	//$sql = "insert into review_rating ";
	//counting for avarage rating
	//$avg_rating = 
	//counting
	//$review =  str_replace(' ', '&nbsp;', trim($_REQUEST['reviews']));
	   $array_ = $json_array = array();
	$array_['customer_id'] = $_REQUEST['customer_id'];
	$array_['campaign_id'] = $_REQUEST['camp_id'];
	$array_['location_id'] = $_REQUEST['loc_id'];
	$array_['spam_flag']=0;
        $array_['spam_flag_counter'] = 0;
		$array_['is_notusefull'] = 0;
		$array_['is_usefull'] = 0;
		$array_['platform'] = 0;
	$array_['reviewed_datetime'] = date('Y-m-d H:i:s');
	$array_['review'] = trim($_REQUEST['reviews']) ;
	$array_['rating'] = $_REQUEST['ratings'];
	$array_['employee_id'] = $_REQUEST['employee_id'] ;
	$objDB->Insert($array_, "review_rating");
	
	if($_REQUEST['reviews'] == "")
	{
		$review_increase_counter = 0;
	}
	else{
		$review_increase_counter = 1;
	}
	/*$old_avg_rating_sql = "select AVG(rating) avarage_rating  from review_rating where location_id = ".$_REQUEST['loc_id'];
	$old_avg_rating_rs =  $objDB->Conn->Execute($old_avg_rating_sql);*/
	$old_avg_rating_rs =  $objDB->Conn->Execute("select AVG(rating) avarage_rating  from review_rating where location_id =?",array($_REQUEST['loc_id']));

	$old_avg_rating = $old_avg_rating_rs->fields['avarage_rating'];
	
	
	$update_array= array();
	/*$sql = "update reward_user  set review_rating_visibility=0 where customer_id=".$_REQUEST['customer_id']." and campaign_id=".$_REQUEST['camp_id']."
	 and location_id=".$_REQUEST['loc_id'];
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDBWrt->Conn->Execute("update reward_user  set review_rating_visibility=0 where customer_id=? and campaign_id=?
	 and location_id=?",array($_REQUEST['customer_id'],$_REQUEST['camp_id'],$_REQUEST['loc_id'])); 	 
	 
	 
	 /*$sql = "update locations set avarage_rating=".$old_avg_rating." , no_of_rating=no_of_rating +1 , no_of_reviews = no_of_reviews + ".$review_increase_counter."  where id=".$_REQUEST['loc_id'];
	 $RS = $objDB->Conn->Execute($sql);*/
	  $RS = $objDBWrt->Conn->Execute("update locations set avarage_rating=? , no_of_rating=no_of_rating +1 , no_of_reviews = no_of_reviews + ".$review_increase_counter."  where id=?",array($old_avg_rating,$_REQUEST['loc_id']));

}
/**
 * @uses on review check login or not
 * @param 
 * @used in pages :getrating.php,location_detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btncheckloginornot_review']))
{
     if(!isset($_SESSION['customer_id']))
	 { 
        $json_array = array();
		$json_array['status'] = "false";
		$json_array['link'] =WEB_PATH."/register.php?url=".urlencode($_REQUEST["link"]);
        $json_array['is_profileset'] = 1;
        $json = json_encode($json_array);
        echo $json;
		exit();
	}
    else
    {
        $json_array = array();
		$json_array['status'] = "true";
		$json_array['link'] = $_REQUEST["link"];
		$json_array['is_profileset'] = 1;
		$json = json_encode($json_array);
        echo $json;
		exit();
    }   
} 

/**
 * @uses  check login or not
 * @param 
 * @used in pages :
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btncheckloginornot']))
{
     if(!isset($_SESSION['customer_id']))
	 { 
        $json_array = array();
		$json_array['status'] = "false";
		$json_array['link'] =WEB_PATH."/register.php?url=".urlencode($_SERVER['HTTP_REFERER']);
        $json_array['is_profileset'] = 1;
        $json = json_encode($json_array);
        echo $json;
		exit();
	}
    else
    {
        $json_array = array();
		$json_array['status'] = "true";
		$json_array['link'] = $_REQUEST["link"];
		$json_array['is_profileset'] = 1;
		$json = json_encode($json_array);
        echo $json;
		exit();
    }   
} 

/**
 * @uses get latest location review
 * @param location_id
 * @used in pages :location_detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_latest_location_review']))
{

	/*$sql = "select review from review_rating where review <> '' and location_id = ".$_REQUEST['location_id']." order by reviewed_datetime desc limit 1";
	
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->SelectLimit("select review from review_rating where review <> '' and location_id =? order by reviewed_datetime desc",1,0,array($_REQUEST['location_id']));			 
	$json_array['status'] = "true";
	$json_array['review'] = $RS->fields['review'];
	$json_array['is_review'] = $RS->RecordCount();
	$json = json_encode($json_array);
	echo $json;
	exit(); 
}

/**
 * @uses get location review
 * @param location_id
 * @used in pages :location_detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_location_reviews']))
{
	/*$sql = "select re.* , u.firstname ,u.lastname , u.profile_pic , u.city , u.state from review_rating re inner join customer_user u on re.customer_id = u.id  where review <> '' and location_id = ".$_REQUEST['location_id']." order by reviewed_datetime desc";
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select re.* , u.firstname ,u.lastname , u.profile_pic , u.city , u.state from review_rating re inner join customer_user u on re.customer_id = u.id  where review <> '' and location_id =? order by reviewed_datetime desc",array($_REQUEST['location_id']));
	$records_all = array();
	$count = 0;
	  while($Row = $RS->FetchRow())
	  {
		$records_all[$count] = get_field_value($Row);
		$count++;
	  }
	$json_array['status'] = "true";
	$json_array['records'] = $records_all;
	$json_array['total_records'] = $RS->RecordCount();
	$json = json_encode($json_array);
	echo $json;
	exit();
}

if(isset($_REQUEST['get_location_reviews_show'])){
      $arr= array();
      //get location reviw by pagination
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_REQUEST['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_REQUEST['iDisplayLength'] );
	}
	
	$order = 'reviewed_datetime desc';
	if(isset($_REQUEST['order'])){
		$order = $_REQUEST['order'];
	}

	$RS = $objDB->Conn->Execute("SELECT SQL_CALC_FOUND_ROWS re.* , u.firstname ,u.lastname , u.profile_pic , u.city , u.state from review_rating re inner join customer_user u on re.customer_id = u.id  where review <> '' and location_id =? order by re.".$order."  ".$sLimit,array($_REQUEST['location_id']));
	
	
	$sQuery = $objDB->Conn->Execute("SELECT FOUND_ROWS()");
	$total= $sQuery->fields['FOUND_ROWS()'];

     /* $RS = $objDB->Conn->Execute("select re.* , u.firstname ,u.lastname , u.profile_pic , u.city , u.state from review_rating re inner join customer_user u on re.customer_id = u.id  where review <> '' and location_id =? order by reviewed_datetime desc limit ".$_REQUEST[iDisplayStart].",".$_REQUEST[iDisplayLength],array($_REQUEST['location_id']));
      
      //total reviews of location
      $rTotal = $objDB->Conn->Execute("select count(*) total from(select re.id, u.firstname  from review_rating re inner join customer_user u on re.customer_id = u.id  where review <> '' and location_id =? order by reviewed_datetime desc) review_rating ",array($_REQUEST['location_id']));
      $rTotal = $rTotal->FetchRow();
      $total= $rTotal['total'];*/
      
                $Json = array();
                $Json['iDisplayLength'] = $_REQUEST['iDisplayLength'];
                $Json['iDisplayStart'] = $_REQUEST['iDisplayStart'];
                $Json['iTotalDisplayRecords'] = $total;
                $Json['sEcho'] = $_REQUEST['sEcho'];
                $Json['sSearch'] = $_REQUEST['sSearch'];
                $Json['aaData'] = array();
                
                if($RS->RecordCount()>0)
                {
                        $k=0;
                        while($Row = $RS->FetchRow())
                        {       
                                        $html = "<div class='delivery_main'>";
                                        $html .= "<div class='fast_delivery' itemprop='review'>";
                                        $html .= "<div class='user_detail'>";
                                        $pos = strpos($Row['profile_pic'], 'http');
                                        if($pos===false)
                                        {
                                                if($Row['profile_pic'] != "")
                                                {
                                                        $html .= "<div class='rev_usr_pic'><img src='".ASSETS_IMG."/c/usr_pic/".$Row['profile_pic']."' /></div>";
                                                }
                                                else
                                                {
                                                        $html .= "<div class='rev_usr_pic'><img src='".ASSETS_IMG."/c/default_small_user.jpg' /></div>";
                                                }
                                        }else{

                                                $html .= "<div class='rev_usr_pic'><img src='".$Row['profile_pic']."' /></div>";
                                        }
                                        $html .="<div class='rev_usr_name'>";
                                        $html .=$Row['firstname']." ".strtoupper(substr($Row['lastname'],0,1))." (".ucwords($Row['city']).",".$Row['state'].")  ";
                                        
                                        $RSC = $objDB->Conn->Execute("select count(*) as total from review_rating where location_id=? and customer_id=?",array($_REQUEST['location_id'],$Row['customer_id']));
                                        
                                        $html .=$RSC->fields['total']." reviews";
                                        $html .="</div>";
                                        $html .="<div class='date' ><meta content='".date('Y-j-d', strtotime($Row->reviewed_datetime))."' itemprop='datePublished'>".date('M j, Y | g:i A', strtotime($Row['reviewed_datetime']))."</div>";
                                        $html .= "<div class='one'>";
                                        $rating = $Row['rating'];
                                        
                                        $class =  "full-gray";
                                                     $rating_title ="Not Yet Rated";
                                       if( $rating < 0 && $rating  < 1)
                                        {
                                            $class =  "orange-half";
                                                            $rating_title = "Poor";
                                        }
                                        else if ($rating >= 1 && $rating <= 1.74) {
                                            
                                              $class =  "orange-one";
                                                               $rating_title = "Poor";
                                        }
                                        else if ($rating >= 1.75 && $rating <= 2.24) {
                                           
                                              $class =  "orange-two";
                                                              $rating_title = "Fair";
                                        }
                                        else if ($rating >= 2.25 && $rating <= 2.74) {
                                            
                                              $class =  "orange-two_h";
                                                               $rating_title = "Good";
                                        }
                                        else if ($rating >= 2.75 && $rating <= 3.24) {
                                              $class =  "orange-three";
                                                               $rating_title = "Good";
                                        }
                                        else if ($rating >= 3.25 && $rating <= 3.74) {
                                              $class =  "orange-three_h";
                                                              $rating_title = "Very Good";
                                        }
                                        else if ($rating >= 3.75 && $rating <= 4.24) {
                                           //echo "4";
                                              $class =  "orange-four";
                                                              $rating_title = "Very Good";
                                        }
                                        else if ($rating >= 4.25 && $rating <= 4.74) {
                                              $class =  "orange-four_h";
                                                               $rating_title = "Excellent";
                                        }
                                        else if($rating >= 4.75) {
                                              $class =  "orange";
                                                               $rating_title = "Excellent";
                                        }
                                        
                                        $html .='<div class="ratinn_box '.$class.'" itemprop="reviewRating" itemscope="" itemtype="https://schema.org/Rating" ><meta content="'.round($rating,2).'" itemprop="ratingValue"></div>';
                                        $html .= '<div class="cust_attr_tooltip" style="display: none;">
									<div class="arrow_down" ></div>
									<span id="star_tooltip">'. $rating_title.'</span>
                                                 </div>';
                                        $html .='<div class="date_main"><ul class="link usefull" urld='.$Row['id'].'>
														
                                                        <li><a href="javascript:void(0)" class="like"><div class="fa-thumbs-o-up" title="Helpful"></div><div class="like_counter" >'. $Row["is_usefull"] .'</div>
                                                        
                                                        </a></li></ul></div>';
                                        $html .= "</div>";
                                        $html .= "</div>";
                                        $html .= '<div class="fast" itemprop="description" >';
                                        
                                        $review = $Row['review'];
			
                                        if(strlen(strip_tags($review))>400)
                                        {
                                                $html .= '<div id="review_'.$Row['id'] .'" style="height:75px;" > ';
                                                $html .= substr(trim($review),0,400)."...".'<a class="review_more" id="review_more_"'. $Row['id'].'" review_id="'.$Row['id'].'" more="1" href="javascript:void(0)">show more</a>';
                                                $html .= '</div>';
                                                $html .= '<div id="review_'.$Row['id'] .'_hidden" style="display:none;" >';
                                                $html .= trim($review);
                                                $html .= '</div>';

                                        }
                                        else
                                        {
                                                $html .= '<p id="review_'.$Row['id'].'" >';  
                                                $html .= trim($review);
                                                $html .= '</p>';  

                                        }
                                        $html .= "</div>";
                                        
                                        
                                        
                                        
                                        $html .= '<div class="problem_wrapper"><div class="problem_left"  revid111="'.$Row['id'].'" >Report</div>
                                                        <div class="problem_div" disp="0" style="display:none">
                                                                            
                                                            <ul>
                                                                <li class="review1" >Review violates guidelines</li>
                                                                <li class="review_problem2" >Review is suspicious</li>
                                                            </ul>

                                                        </div>
                                                  </div>';
                                        $html .= "</div>";
                                        $html .= "</div>";
                                
                                $Json['aaData'][$k][]=$html;
                                $Json['aaData'][$k][]=$rating;
                                $Json['aaData'][$k][]=$Row['reviewed_datetime'];
                                $Json['aaData'][$k][]=$Row['is_usefull'];
				$k++;
                               
                        }
                }
                
                echo json_encode($Json);
                exit;
      
}

if(isset($_REQUEST['reward_zone'])){

	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_REQUEST['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_REQUEST['iDisplayLength'] );
	}

	$where = '';
	$mwhere = '';
	
	if($_REQUEST['reward_type'] == 1) {

		if($_REQUEST['catid'] >0){
			$where .= 'g.category_id ='.$_REQUEST['catid'];
		}
		if($_REQUEST['min_cur'] >0  && $_REQUEST['max_cur'] >0){
			if($_REQUEST['catid'] >0){
				$where .= " AND ";
			}
			//$where .= '(redeem_point_value >='.$_REQUEST['min_cur'].' AND redeem_point_value <='.$_REQUEST['max_cur'].')';
			$where .= '(g.card_value BETWEEN '.$_REQUEST['min_cur'].' AND '.$_REQUEST['max_cur'].')';
		}
		if(isset($_REQUEST['ship_to'])){
			if($_REQUEST['catid'] >0 || ($_REQUEST['min_cur'] >0  && $_REQUEST['max_cur'] >0)){
				$where .= " AND ";
			}
			$where .= 'g.ship_to ="'.strtolower($_REQUEST["ship_to"]).'" AND';
		}
	
		if(isset($_REQUEST['merchant_name']) && !empty($_REQUEST['merchant_name'])){
			//$mwhere .= 'gm.merchant_name LIKE "%'.strtolower($_REQUEST["merchant_name"]).'%" OR m.business LIKE "%'..'%" AND ';
			$mwhere .= 'if(g.is_merchant=0 ,gm.merchant_name LIKE "%'.strtolower($_REQUEST["merchant_name"]).'%" , g.merchant_id= m.id AND m.business LIKE "%'.strtolower($_REQUEST["merchant_name"]).'%") AND ';
		}


		$Sql = "SELECT SQL_CALC_FOUND_ROWS g.id,title,image , g.value , g.card_value,g.discount,g.is_per ,g.redeem_point_value,g.ship_to ,g.active,g.category_id,g.merchant_id ,g.is_merchant, gm.merchant_name FROM giftcards g, giftcard_merchant_user gm, merchant_user m where gm.id = g.merchant_id AND ".$mwhere.$where." g.active=? and g.is_deleted = ? group by g.id order by g.id desc ".$sLimit;

	}else{
		if($_REQUEST['catid'] >0){
			$where .= 'r.category_id ='.$_REQUEST['catid'].' AND ';
		}
		if($_REQUEST['min_cur'] >0  && $_REQUEST['max_cur'] >0){
			
			//$where .= '(redeem_point_value >='.$_REQUEST['min_cur'].' AND redeem_point_value <='.$_REQUEST['max_cur'].')';
			$where .= '(r.value BETWEEN '.$_REQUEST['min_cur'].' AND '.$_REQUEST['max_cur'].') AND ';
		}
		
		if(isset($_REQUEST['merchant_name']) && !empty($_REQUEST['merchant_name'])){
			
			$mwhere .= 'm.business LIKE "%'.strtolower($_REQUEST["merchant_name"]).'%" AND ';
		}

			$Sql = "Select SQL_CALC_FOUND_ROWS r.id,r.title,r.merchant_id,r.category_id ,r.image,r.value,r.discount,r.is_percentage,m.business,c.cat_image from rewardzone_campaigns r,merchant_user m, categories c where r.category_id =c.id and m.id = r.merchant_id And ".$mwhere.$where." r.active=? and r.is_deleted=? group by r.id order by r.id desc ".$sLimit;

	}

        $RS = $objDB->Conn->Execute($Sql,array(1,0));

	$sQuery = $objDB->Conn->Execute("SELECT FOUND_ROWS()");
	
	$total= $sQuery->fields['FOUND_ROWS()'];

	$Json = array();
        $Json['iDisplayLength'] = $_REQUEST['iDisplayLength'];
        $Json['iDisplayStart'] = $_REQUEST['iDisplayStart'];
        $Json['iTotalDisplayRecords'] = $total;
        $Json['sEcho'] = $_REQUEST['sEcho'];
        $Json['sSearch'] = $_REQUEST['sSearch'];
        $Json['aaData'] = array();
	
	if ($RS->RecordCount() > 0) {
		$k=$i =0;
                    while ($Row = $RS->FetchRow()) {

				if($_REQUEST['reward_type'] == 1) {
					if($Row['is_merchant'] == 1){
						$redeem = reward_zone_redeem_point($Row['card_value'],$Row['discount'],$Row['is_per']);
						$redeem = $redeem['redeem'];
						
					}else{	$redeem = $Row['redeem_point_value']; }

					$html = '<div data-gift="'.$Row['id'].'" class="gft_c09" data-merchant="'.$Row['is_merchant'].'" data-reward="'.$_REQUEST['reward_type'].'">
					<div class="giftcard_img_div" ><img src="'.ASSETS_IMG . '/a/giftcards/' . $Row['image'].'" alt=""/> 
		                        </div>
		                        <div class="gft_dscr_block">
		                            <h3 title="'.$Row['title'].'">'.$Row['title'].'</h3>
		                            <div class="gft_pts_req">
		                                <samp>Redeem with '.$redeem.' Scanflip points</samp>
		                            </div>
		                        </div>
					</div>';
				}else{
					$redeem = reward_zone_redeem_point($Row['value'],$Row['discount'],$Row['is_percentage']);
						
					if($Row['is_percentage']== 1){
						$dis = $Row['discount'].'%';					
					}else{ 
					
					$dis= round(($Row['discount']/$Row['value'])*100).'%';
					
					 }

					$html = '<div class="deal_blk redeem_camp" campid="'.$Row['id'].'" merid="'.$Row['merchant_id'].'" catid="'.$Row['category_id'].'" data-reward="'.$_REQUEST['reward_type'].'" ><div class="offersexyellow"><span class="badge">'.$dis.' OFF</span><div class="grid_img_div"><img src="'.ASSETS_IMG . '/m/campaign/' . $Row['image'].'"></div><div class="dealmetric_wrap"><div class="dealmetric title">
				 <div class="value">Value</div>
				 <div class="discount">Redeem with Scanflip points</div>
				</div>

				<div class="dealmetric values">
				 <div class="value">$'.$Row['value'].'</div>
				 <div class="discount">'.$redeem['redeem'].'</div>
				</div></div><div title="'.$Row['title'].'" class="dealtitle">'.$Row['title'].'</div>
				<div class="btnview"><img class="kat_img" align="left" src="'.ASSETS_IMG . '/'.$Row['cat_image'].'" ></div>
				</div></div>';
				}
				
				$Json['aaData'][$k][]=$html;
				$k++;
						
		}
        }
		echo json_encode($Json);
                exit;

}
/**
 * @uses like review
 * @param location_id,review_id
 * @used in pages :location_detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['like_review']))
{
	/*$sql = "select customer_id from review_rating where id=".$_REQUEST['review_id'];
	$RS_user = $objDB->Conn->Execute($sql);*/
	$RS_user = $objDB->Conn->Execute("select customer_id from review_rating where id=?",array($_REQUEST['review_id']));

	$cnt = 0;
	$cnt1 = 0;
	if($RS_user->fields['customer_id'] == $_REQUEST['customer_id'] )
	{
			$json_array['status'] = "true";
	$json_array['counter'] = $cnt;
	$json_array['counter1'] = $cnt1;
	$json = json_encode($json_array);
	echo $json;
	exit();
	}
	
	$json_array = array();
	/*$sql ="select * from user_review_like where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
	
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select * from user_review_like where customer_id=? and review_id = ?",array($_REQUEST['customer_id'],$_REQUEST['review_id']));

	if($RS->RecordCount() == 0 )
	{
	   $array_ = $json_array = array();
		$array_['customer_id'] = $_REQUEST['customer_id'];
		$array_['review_id'] = $_REQUEST['review_id'];
		$array_['review_like'] = 1;
		$array_['review_unlike']=0;
		$array_['like_datetime'] = date('Y-m-d H:i:s');
		$objDB->Insert($array_, "user_review_like");
		/*$sql_update = "update review_rating set is_usefull=is_usefull +1  where id=".$_REQUEST['review_id'];
		
		$RS = $objDB->Conn->Execute($sql_update);*/
		$RS = $objDBWrt->Conn->Execute("update review_rating set is_usefull=is_usefull +1  where id=?",array($_REQUEST['review_id']));
		$cnt = 1;
		$cnt1= 0;
	}
	else{
		if($RS->fields['review_like'] == 1)
		{
			/*$sql = "update user_review_like  set review_like=0 where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
			$sql_update = "update review_rating set is_usefull=is_usefull -1    where id=".$_REQUEST['review_id'];*/
			$RS = $objDBWrt->Conn->Execute("update user_review_like  set review_like=? where customer_id=? and review_id =?",array(0,$_REQUEST['customer_id'],$_REQUEST['review_id']));
                        $RS = $objDBWrt->Conn->Execute("update review_rating set is_usefull=is_usefull -1    where id=?",array($_REQUEST['review_id']));
			$cnt = -1;	
			$cnt1= 1;
		}
		else{
			/*$sql = "update user_review_like set review_like=1  where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
			$sql_update = "update review_rating set is_usefull=is_usefull +1   where id=".$_REQUEST['review_id'];*/
			$RS = $objDBWrt->Conn->Execute("update user_review_like set review_like=?  where customer_id=? and review_id =?",array(1,$_REQUEST['customer_id'],$_REQUEST['review_id']));
                        $RS = $objDBWrt->Conn->Execute("update review_rating set is_usefull=is_usefull +1   where id=?",array($_REQUEST['review_id']));
			$cnt = 1;
			$cnt1= -1;
		}
	//$RS = $objDB->Conn->Execute($sql);
	//$RS = $objDB->Conn->Execute($sql_update);	
	}
	
	$json_array['status'] = "true";
	$json_array['counter'] = $cnt;
	$json_array['counter1'] = $cnt1;
	$json = json_encode($json_array);
	echo $json;
	exit();
}


/**
 * @uses unlike review
 * @param location_id,review_id
 * @used in pages :location_detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['unlike_review']))
{
/*	$sql = "select customer_id from review_rating where id=".$_REQUEST['review_id'];
	$RS_user = $objDB->Conn->Execute($sql);*/
	$RS_user = $objDB->Conn->Execute("select customer_id from review_rating where id=?",array($_REQUEST['review_id']));

	$cnt = 0;
	$cnt1 = 0;
	if($RS_user->fields['customer_id'] == $_REQUEST['customer_id'] )
	{
			$json_array['status'] = "true";
	$json_array['counter'] = $cnt;
	$json_array['counter1'] = $cnt1;
	$json = json_encode($json_array);
	echo $json;
	exit();
	}
	$json_array= array();
	/*$sql ="select * from user_review_like where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select * from user_review_like where customer_id=? and review_id =? ",array($_REQUEST['customer_id'],$_REQUEST['review_id']));

	if($RS->RecordCount() == 0 )
	{
	    $array_ = $json_array = array();
		$array_['customer_id'] = $_REQUEST['customer_id'];
		$array_['review_id'] = $_REQUEST['review_id'];
		$array_['review_like'] = 0;
		$array_['review_unlike']=1;
		$array_['like_datetime'] = date('Y-m-d H:i:s');
		$objDB->Insert($array_, "user_review_like");
		$sql_update = "update review_rating set is_notusefull= is_notusefull+1  where id=".$_REQUEST['review_id'];
		$RS = $objDB->Conn->Execute($sql_update);
		$cnt = 1;
		$cnt1= 0;
                
	}
	else{
		if($RS->fields['review_unlike'] == 1)
		{
		
			/*$sql = "update user_review_like  set review_unlike = 0 where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
			$sql_update = "update review_rating set is_notusefull=is_notusefull -1   where id=".$_REQUEST['review_id'];*/
			$RS = $objDBWrt->Conn->Execute("update user_review_like  set review_unlike = ? where customer_id=? and review_id =?",array(0,$_REQUEST['customer_id'],$_REQUEST['review_id']));
                        $RS = $objDBWrt->Conn->Execute("update review_rating set is_notusefull=is_notusefull -1   where id=?",array($_REQUEST['review_id']));
			$cnt = -1;
			$cnt1= -1;
		}
		else{
			/*$sql = "update user_review_like set  review_unlike = 1 where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
			$sql_update = "update review_rating set is_notusefull=is_notusefull +1  where id=".$_REQUEST['review_id'];*/
			$RS = $objDBWrt->Conn->Execute("update user_review_like set  review_unlike = ? where customer_id=? and review_id = ?",array(1,$_REQUEST['customer_id'],$_REQUEST['review_id']));
                        $RS = $objDBWrt->Conn->Execute("update review_rating set is_notusefull=is_notusefull +1  where id=?",array($_REQUEST['review_id']));
			$cnt = 1;
			$cnt1= 1;
		}
		
        //$RS = $objDB->Conn->Execute($sql);
	//$RS = $objDB->Conn->Execute($sql_update);
	}
        
      
	
	$json_array['status'] = "true";
	$json_array['counter'] = $cnt;
	$json_array['counter1'] = $cnt1;
	$json = json_encode($json_array);
	echo $json;
	exit();
}
/**
 * @uses earned recent visit counter
 * @param customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['earned_recent_visit_counter']))
{
   
    $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
        
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
	 }
	        
	$limit_data = "SELECT  cr.redeem_value ,cr.coupon_id,cc.customer_campaign_code ,l.created_by,( count(*) * c.redeem_rewards) aavarge_redeem_value,cc.customer_id,mu.business
, count(*) as total ,  c.redeem_rewards redeem_values
FROM `coupon_redeem` cr,
 coupon_codes cc inner join locations l on l.id = cc.location_id  inner join campaigns c on c.id = cc.customer_campaign_code inner join merchant_user mu on mu.id=l.created_by
where cr.coupon_id= cc.id and
CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) 
between CONVERT_TZ(cr.redeem_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))
 and CONVERT_TZ(cr.redeem_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY
and cc.customer_id=".$customer_id." group by l.created_by";

	// 14-04-14 change query because now coupon redeem at location timezone so no need to convert now.
	
	/*$limit_data = "SELECT  cr.redeem_value ,cr.coupon_id,cc.customer_campaign_code ,l.created_by,( count(*) * c.redeem_rewards) aavarge_redeem_value,cc.customer_id,mu.business
, count(*) as total ,  c.redeem_rewards redeem_values
FROM `coupon_redeem` cr,
 coupon_codes cc inner join locations l on l.id = cc.location_id  inner join campaigns c on c.id = cc.customer_campaign_code inner join merchant_user mu on mu.id=l.created_by
where cr.coupon_id= cc.id and
CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) 
between cr.redeem_date and cr.redeem_date + INTERVAL 1 DAY 
and cc.customer_id=".$customer_id." group by l.created_by";


         $RS_limit_data=$objDB->Conn->Execute($limit_data);*/

	$RS_limit_data=$objDB->Conn->Execute("SELECT  cr.redeem_value ,cr.coupon_id,cc.customer_campaign_code ,l.created_by,( count(*) * c.redeem_rewards) aavarge_redeem_value,cc.customer_id,mu.business
, count(*) as total ,  c.redeem_rewards redeem_values
FROM `coupon_redeem` cr,
 coupon_codes cc inner join locations l on l.id = cc.location_id  inner join campaigns c on c.id = cc.customer_campaign_code inner join merchant_user mu on mu.id=l.created_by
where cr.coupon_id= cc.id and
CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) 
between cr.redeem_date and cr.redeem_date + INTERVAL 1 DAY 
and cc.customer_id=? group by l.created_by",array($customer_id));

         $count=0;
		$records=array();
		if($RS_limit_data->RecordCount()>0)
	    {
			while($Row_camps = $RS_limit_data->FetchRow())
			{
				$records[$count] = get_field_value($Row_camps);
				$count++; 
			}
				
			  $json_array['status'] = "true";
			  $json_array["records"]= $records;
			  $json_array["total_records"]= $count;
		}
		else
		{
			$json_array['status'] = "false";
			$json_array["total_records"]= 0;
		}
        $json_array["query"]=$limit_data;
	 $json = json_encode($json_array);
	 echo $json;
	exit();
}
/**
 * @uses earned new customer refferel counter
 * @param customer_id
 * @used in pages :no use
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['earned_new_customer_referral_counter']))
{
   
    $json_array = array();
	 $records = array();
	 $records_all=array();
	 $json_array1 = array();
        
	 if($_REQUEST['customer_id']!="")
	 {
		$customer_id = $_REQUEST['customer_id'];
	 }
 
	$limit_data = "select sum(rw.referral_reward) avarage_referral_points,rw.customer_id,l.created_by,mu.business 
from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id  
inner join merchant_user mu on l.created_by=mu.id 
where CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))
 between CONVERT_TZ(rw.reward_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
CONVERT_TZ(rw.reward_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY and rw.customer_id=".$customer_id." 
 group by l.created_by";
 
	// 14-04-14 change query because now coupon redeem at location timezone so no need to convert now.
	
	/*$limit_data = "select sum(rw.referral_reward) avarage_referral_points,rw.customer_id,l.created_by,mu.business 
from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id  
inner join merchant_user mu on l.created_by=mu.id 
where CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))
 between rw.reward_date and rw.reward_date + INTERVAL 1 DAY and rw.customer_id=".$customer_id." 
 group by l.created_by";
 

         $RS_limit_data=$objDB->Conn->Execute($limit_data);*/
	$RS_limit_data=$objDB->Conn->Execute("select sum(rw.referral_reward) avarage_referral_points,rw.customer_id,l.created_by,mu.business 
from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id  
inner join merchant_user mu on l.created_by=mu.id 
where CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))
 between rw.reward_date and rw.reward_date + INTERVAL 1 DAY and rw.customer_id=? 
 group by l.created_by",array($customer_id));

         $count=0;
		$records=array();
		if($RS_limit_data->RecordCount()>0)
	    {
			while($Row_camps = $RS_limit_data->FetchRow())
			{
				$records[$count] = get_field_value($Row_camps);
				$count++; 
			}
				
			  $json_array['status'] = "true";
			  $json_array["records"]= $records;
			  $json_array["total_records"]= $count;
		}
		else
		{
			$json_array['status'] = "false";
			$json_array["total_records"]= 0;
		}
        
	 $json = json_encode($json_array);
	 echo $json;
	exit();
}

/**
 * @uses get location menu
 * @param location_id
 * @used in pages :locations.php,location-detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_location_menu']))
{
                    // create curl resource 
    $location_menu="";                
   
$location_venue_id=  $objDB->Conn->Execute("select * from locations where id=?",array($_REQUEST['location_id']));

//$single_details = array('90837987f15783ce9ce4');
 if($location_venue_id->fields['venue_id'] != "")
 {
 //$single_details = array($location_venue_id->fields['venue_id']);
   $my_file = 'locu_files/locu_'.$location_venue_id->fields['venue_id'].'.txt';
   

   if (file_exists($my_file)) {
       
        $file_data=file_get_contents($my_file);
        
        //$file_data_json = json_decode($file_data);
        $array_json = json_decode($file_data);
        
   }
   else
   {
       $handle = fopen($my_file, 'w');
       $ch = curl_init(); 
                    
        // set url 
        curl_setopt($ch, CURLOPT_URL, "https://api.locu.com/v1_0/venue/".$location_venue_id->fields['venue_id']."/?api_key=269fe167da30803613598800a3da6e0e590297ac"); 

        //return the transfer as a string 
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        // $output contains the output string 
        $output = curl_exec($ch); 
       file_put_contents($my_file,$output);
       $array_json = json_decode($output);
       
   }
      
                     $first_step=$array_json->objects;
                     $second_step=$first_step[0]->menus;
                   
                 $location_menu.='<div style="" id="menu_'.$_REQUEST['location_id'].'">';       
                  $location_menu.='<div style="" id="" class="">';
                      
                $location_menu.='<div class="inner withClose" id="overlayInnerDiv">';
                    $location_menu.='<div class="menuContainer">';
                    $mainmenucount=0;
                    for($k=0;$k<count($second_step);$k++)
                    { 
                        if($mainmenucount == 0)
                        {
                
                          $location_menu.='<div id="'.$k.$_REQUEST['location_id'].'" class="menuTitle mainmanuTitle" onclick="myFunction(this.id)">'.$second_step[$k]->menu_name.'</div>';
                        }
                        else
                        {
                          $location_menu.='<div id="'.$k.$_REQUEST['location_id'].'" class="menuTitle mainmanuTitle inactive" onclick="myFunction(this.id)">'.$second_step[$k]->menu_name.'</div>';  
                        }
                        $mainmenucount++;
                
                    }
                   
                    $count=0;
        for($k=0;$k<count($second_step);$k++)
        {
        //$get_menu_sections_array= $second_step[$k]['sections'];
        $get_menu_sections_array= $second_step[$k]->sections;
                    
                    
                 
                    
                   
                    if($count == 0)
                    { 
                       $location_menu.='<div class="menuBody " id="smbody'.$k.$_REQUEST['location_id'].'">';
                     }

                    else
                    {
                          $location_menu.='<div class="menuBody " id="smbody'.$k.$_REQUEST['location_id'].'" style="display:none">';
                     } 
                     
                            $location_menu.='<div class="menuDesc"></div>';
                     

                    for($i=0;$i<count($get_menu_sections_array);$i++)
                    {
                       // echo $get_menu_sections_array[$i]->section_name."</br>";
                        if($get_menu_sections_array[$i]->section_name != "")
                        {
                       
                        $location_menu.='<div class="menuSection">';
                                         $location_menu.='<div class="menuTitle menuSectionTitle">'.$get_menu_sections_array[$i]->section_name.'</div>';
                                   $location_menu.='</div>';

                       
                        }
                         $get_subsections_detail=$get_menu_sections_array[$i]->subsections[0];
                         $get_subsections_detail_second=$get_subsections_detail->contents;
                        
                         for($j=0;$j<count($get_subsections_detail_second);$j++)
                         { 
                             
                              if(isset($get_subsections_detail_second[$j]->name)  || isset($get_subsections_detail_second[$j]->description))
                             {
                                $location_menu.='<div class="menuItem">';
                               if(isset($get_subsections_detail_second[$j]->price) )
                             {
                               $location_menu.='<div class="menuItemRHS">';
                                     $location_menu.='<div class="menuPrice">';
                                         $location_menu.='<span class="menuPriceDesc"></span>';
                                         $location_menu.='<span class="menuPriceDesc"></span>';
                                         $location_menu.='<span class="menuPriceNum">'.$get_subsections_detail_second[$j]->price.'</span>';
                                     $location_menu.='</div>';
                                 $location_menu.='</div>';

                             }
                               $location_menu.='<div class="menuItemLHS">';
                                        if(isset($get_subsections_detail_second[$j]->name) )
                                       { 
                                                          $location_menu.='<div class="menuItemTitle">'.$get_subsections_detail_second[$j]->name.'</div>';


                                        }
                                       if(isset($get_subsections_detail_second[$j]->description) )
                                       { 
                                         $location_menu.='<div class="menuItemDesc">'.$get_subsections_detail_second[$j]->description.'</div>';

                                        }
                                   $location_menu.='</div>';
                               $location_menu.='</div>';
                             }
                           

                        } 
                         
                         
                    } 
                    
                    if($count == 0)
        {   
           $location_menu.='</div>';
          }else
       { 
          $location_menu.='</div>';
        }

       $count++;
                  
        }        
                   $location_menu.='</div>';
               $location_menu.='</div>';
                    $location_menu.='</div>';
                  $location_menu.='<hr>';
                     $location_menu.='<a id="powered_by_locu" style="padding:10px;" target="_blank" href="https://locu.com"><img height="25" width="155" src="'.ASSETS_IMG.'/c/powerbylocu.png" alt="Powered by Locu"></a>';
                    $location_menu.='</div>';
                     
                             
                   echo $location_menu;  
                         
 }
 else
 {
      $location_menu.='<div style="" id="menu_'.$_REQUEST['location_id'].'">';
      $location_menu.="<div style='padding:15px;font-weight:bold'>".$client_msg['location_detail']['Msg_no_menu_price_list']."</div>";
      $location_menu.="</div>";
      echo $location_menu;
      
 }
                                        
}

/**
 * @uses get next images
 * @param latitude,longitude
 * @used in pages :location-detail.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_more_images']))
{
	$photos_div = "";
    $photos_div_main="";
    
    //$image_path_main=SERVER_PATH."/merchant/images/location/";
    $image_path_main=UPLOAD_IMG."/m/location/";
	//$endpoint = "https://maps.google.com/cbk?output=json&hl=en&ll=39.470611,-0.3899&radius=10&cb_client=maps_sv&v=4";
	
	$endpoint = "https://maps.google.com/cbk?output=json&hl=en&ll=".$_REQUEST['latitude'].",".$_REQUEST['longitude'];
	
	$handler = curl_init();
	curl_setopt($handler, CURLOPT_HEADER, 0);
	curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($handler, CURLOPT_URL, $endpoint);
	$data = curl_exec($handler);
	curl_close($handler);
	// if data value is an empty json document ('{}') , the panorama is not available for that point
	if ($data==='{}') 
	{
		//print "StreetView Panorama isn't available for the selected location";
	}
	else
	{
		if (file_exists($image_path_main.'street_'.$_REQUEST['location_id'].'.jpeg')) 
		{
		
			$image = new SimpleImage();
			$image->load($image_path_main.'street_'.$_REQUEST['location_id'].'.jpeg');
			$image->resize(70,70);
			$image->save($image_path_main.'thumb/street_'.$_REQUEST['location_id'].'.jpeg');
			
		}
		else
		{
			$street_main_image = file_get_contents('https://maps.googleapis.com/maps/api/streetview?size=400x310&location='.$_REQUEST['latitude'].",".$_REQUEST['longitude'].'&sensor=false&key=AIzaSyBsvIV_4NNaCz9d2tSS6EeW01wIj98lmFA'); 
			$fp  = fopen($image_path_main.'street_'.$_REQUEST['location_id'].'.jpeg', 'w+');
			fputs($fp, $street_main_image);
			
			$image = new SimpleImage();
			$image->load($image_path_main.'street_'.$_REQUEST['location_id'].'.jpeg');
			$image->resize(70,70);
			$image->save($image_path_main.'thumb/street_'.$_REQUEST['location_id'].'.jpeg');	
		}
    }
    
    $photos_div.='<div style="" class="moreimages_tab" id="moreimages_'.$_REQUEST['location_id'].'">';
    
    //Location More Photos 
                            $photos_div_main='<div id="div_main_'.$_REQUEST['location_id'].'" style="width:100%;display:block">';
                            $photos_div.=$photos_div_main;
                            /*$sql_more="Select * from location_images where location_id='". $_REQUEST['location_id']."' ORDER BY image_id";
                            $RS_images = $objDB->execute_query($sql_more);*/
				$RS_images = $objDB->Conn->Execute("Select * from location_images where location_id=? ORDER BY image_id",array($_REQUEST['location_id']));
                            $count_images=$RS_images->RecordCount();
                            //if($count_images > 0)
                            //{ 
                                    $photos_div.='<div style="" id="div_block_main_image">';
                                
                                        $if_i=1;
												
                                                if ($data==='{}') 
												{
												}
												else
												{
												
													$photos_div.='<img class="main_image_class_'.$_REQUEST['location_id'].'" id="main_location_street_'.$_REQUEST['location_id'].'" style="display:block" src="'.ASSETS_IMG.'/m/location/street_'.$_REQUEST['location_id'].'.jpeg?'.date("His").'" >';
												}
                                                while($Row_more = $RS_images->FetchRow()){
                                                $image_name=  explode(".",$Row_more['main_image']);
                                                
                                                if($if_i==1)
                                                {
													if ($data==='{}') 
													{
														$photos_div.='<img class="main_image_class_'.$_REQUEST['location_id'].'" style="display:block" id="main_'.$image_name[0].'_'.$_REQUEST['location_id'].'" src="'.ASSETS_IMG.'/m/location/'.$Row_more['main_image'].'" class="displayimg"></img>';
													}	
                                                    else
													{
														$photos_div.='<img class="main_image_class_'.$_REQUEST['location_id'].'" style="display:none" id="main_'.$image_name[0].'_'.$_REQUEST['location_id'].'" src="'.ASSETS_IMG.'/m/location/'.$Row_more['main_image'].'" class="displayimg"></img>';
													}
                                                }
                                               else
                                               {


                                                    $photos_div.='<img class="main_image_class_'.$_REQUEST['location_id'].'" style="display:none" id="main_'.$image_name[0].'_'.$_REQUEST['location_id'].'" src="'.ASSETS_IMG.'/m/location/'.$Row_more['main_image'].'" class="displayimg"></img>';
                                                }

                                                $if_i++;
                                            }
                                            
                                
                                         $photos_div.='</div>';
                            
                             /*$sql="Select * from location_images where location_id='".$_REQUEST['location_id']."' ORDER BY image_id";
                                                  $RS_images = $objDB->execute_query($sql);*/
						$RS_images = $objDB->Conn->Execute("Select * from location_images where location_id=? ORDER BY image_id",array($_REQUEST['location_id']));
                                                  $count_images=$RS_images->RecordCount();
                                                 //if($count_images > 0)
                                                 //{
                           $photos_div.='<div id="div_thumb_image" style="">';
                                            $photos_div.='<ul id="list_'.$_REQUEST['location_id'].'">';
                                                                                              
                                                 
                                                      $if_thumb_i=1;
													  $my_thumb_cnt=0;
                                                     
														   if ($data==='{}') 
															{
															}
															else
															{
																$my_thumb_cnt++;
																$photos_div.='<li class="thumbimageclass thumb_'.$_REQUEST['location_id'].' current_more" id="thumb_location_street_'.$_REQUEST['location_id'].'" style=""><img style="" src="'.ASSETS_IMG.'/m/location/thumb/street_'.$_REQUEST['location_id'].'.jpeg?'.date("His").'" class="displayimg"></img></li>';
															}
                                                        
                                                        
                                                        while($Row_more = $RS_images->FetchRow())
														{
                                                            $image_name=  explode(".",$Row_more['main_image']);
                                                            if($if_thumb_i==1)
                                                            {
																if ($data==='{}') 
																{
																	$photos_div.='<li class="thumbimageclass thumb_'.$_REQUEST['location_id'].' current_more" id="thumb_'.$image_name[0].'_'.$_REQUEST['location_id'].'" style=""><img style="" src="'.ASSETS_IMG.'/m/location/thumb/'.$Row_more['main_image'].'" class="displayimg"></img></li>';
																}
																else
																{
																	$photos_div.='<li class="thumbimageclass thumb_'.$_REQUEST['location_id'].'" id="thumb_'.$image_name[0].'_'.$_REQUEST['location_id'].'" style=""><img style="" src="'.ASSETS_IMG.'/m/location/thumb/'.$Row_more['main_image'].'" class="displayimg"></img></li>';
																}
                                                            }
															else
															{  
																$photos_div.='<li class="thumbimageclass thumb_'.$_REQUEST['location_id'].'" id="thumb_'.$image_name[0].'_'.$_REQUEST['location_id'].'" style=""><img style="" src="'.ASSETS_IMG.'/m/location/thumb/'.$Row_more['main_image'].'" class="displayimg"></img></li>';
															}
															$if_thumb_i++;
															$my_thumb_cnt++;
                                                        }
                                                       
                                                     
                                                        $photos_div.='</ul>';
                                        $photos_div.='</div>';
                                                       
                                        if($if_thumb_i!=1)
                                        {
                                            $photos_div.='<div class="nextprevious">';
                                            $photos_div.='<a href="javascript:void(0)" id="previous_more_'.$_REQUEST['location_id'].'" class="previous_more"></a>';
                                            $photos_div.='<a href="javascript:void(0)" id="next_more_'.$_REQUEST['location_id'].'" class="next_more"></a>';
                                            $photos_div.='</div>';
                                        }
                      //End  Location More Photos  
                      
    $photos_div.='</div>';
	
    //echo $my_thumb_cnt;
	
	if($my_thumb_cnt>0)
	{
		echo $photos_div;
	}
	else
	{
		echo '<div style="" class="moreimages_tab" id="moreimages_'.$_REQUEST['location_id'].'">';
		echo '<div class="noimageclass" style="padding:15px;font-weight:bold;">';
		echo $client_msg["location_detail"]["Msg_No_More_Location_Image"];
		echo '</div>';
		echo '</div>';
	}
}

/**
 * @uses get search deal loaction's loaction
 * @param category_id,mlatitude,mlongitude,dismile
 * @used in pages :
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetSearchDealLocations_loc']))
{
	$json_array = array();
	$records = array();
	$records_all=array();
	$json_array1 = array();
	$category_id=$_REQUEST['category_id'];
	
	$dismile= 50;
	$date_f = date("Y-m-d H:i:s");
	$mlatitude=$_REQUEST['mlatitude'];
	$mlongitude=$_REQUEST['mlongitude'];
	if(isset($_REQUEST['dismile']))
	{
	$dismile= 50;
		$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
		//$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
	}
	$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
	$sel_expr = "  group_concat(CAST(time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'-06:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 AS CHAR(7))separator ',') as limelefttoexpire";
	
	$cust_where ="";

	$cat_str = "";
	if($_REQUEST['customer_id']!="")
	{
	$customer_id = $_REQUEST['customer_id'];
	$get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=".$customer_id." and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = ".$customer_id."
	and location_id=cl.location_id) total_reserved,";
		
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		//                $cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>"" and  country <>""  and id='.$customer_id;
		//                  $RS_cust_data=$objDB->Conn->Execute($cust_sql);
		//                   $is_profileset =  $RS_cust_data->RecordCount();
		//                   if($is_profileset == 0)
		//                   {
		//                       $json_array = array();
		//                        $json_array['status'] = "false";
		//        		  $json_array['is_profileset'] = 0;
		//                          $json = json_encode($json_array);
		//                        echo $json;
		//                        exit();
		//                   }

		// 03-10-2013 dist list deal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013	

		// 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
		// 05-10-2013	
	}
	else
	{
		$cust_where = " and c.level=1 ";
	}
	if(isset($_REQUEST['category_id']))
	{
		if($_REQUEST['category_id']==0)
		{
			$cat_str = "";
		}
		else
		{
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		}
	}
	
	/*
	$limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
    */
	
	
	
	/*$limit_data = "SELECT l.id location_id ,
	group_concat(CAST(c.is_new AS CHAR(7)) separator ',') all_new_campaigns ,
	group_concat(CAST(cl.campaign_type AS CHAR(7)) separator ',') all_campaign_level 
	, group_concat(CAST(c.category_id AS CHAR(7)) separator ',') all_categories
	, group_concat(CAST(c.discount AS CHAR(7)) separator ',') all_discount ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country
	,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating ,".$sel_expr."
	,l.categories,l.is_open,".$get_dat." round((((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance ,l.location_permalink, 
	count(*) total_deals,mu.business  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." group by cl.location_id ORDER BY distance,c.expiration_date";
    
	
	//
	//and (
	//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
	//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
	
	$RS_limit_data=$objDB->Conn->Execute($limit_data);*/
	$RS_limit_data=$objDB->Conn->Execute("SELECT l.id location_id ,
	group_concat(CAST(c.is_new AS CHAR(7)) separator ',') all_new_campaigns ,
	group_concat(CAST(cl.campaign_type AS CHAR(7)) separator ',') all_campaign_level 
	, group_concat(CAST(c.category_id AS CHAR(7)) separator ',') all_categories
	, group_concat(CAST(c.discount AS CHAR(7)) separator ',') all_discount ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country
	,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating ,".$sel_expr."
	,l.categories,l.is_open,".$get_dat." round((((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance ,l.location_permalink, 
	count(*) total_deals,mu.business  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = ?  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." group by cl.location_id ORDER BY distance,c.expiration_date",array(1));

	if($RS_limit_data->RecordCount()>0)
	{
		$json_array['is_profileset'] = 1;          
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS_limit_data->RecordCount();
		$json_array['all_records'] = $RS_limit_data->RecordCount();
		$count=0;
		$arr_main_merchant_arr = array();
		$arr_main_location_arr = array();
		$all_mercahnts = array();
		while($Row = $RS_limit_data->FetchRow())
		{
			$temp_merchant_arr = array();
		
	//	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
	if($arr_main_merchant_arr[$Row['merchant']] != 0 )
	{
	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
	}
	if( !in_array($Row['merchant'],$all_mercahnts))
	{
		array_push($all_mercahnts,$Row['merchant']);
	}
		$arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
		
		array_push($temp_merchant_arr,get_field_value($Row));
		 $arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
		 
			$records[$count] = get_field_value($Row);
			//$records[$count]["rating"] = $objJSON->get_location_rating($Row["locid"]);
				
			$count++;
		}
		
		$json = json_encode($arr_main_merchant_arr);
		$id=0;
		$final_array = array();
		$max_counter = 0;
	
	//select business_tags from merchant_users where id= ".$all_mercahnts[$k]]."
	$business_tag_records = array();
	$count_business_tag =0;
	for($k =0 ;$k<count($all_mercahnts);$k++)
	{
		/*$query = "select id,business_tags from merchant_user where id= ".$all_mercahnts[$k];
		
		$RS_business_tag = $objDB->Conn->Execute($query );*/
		$RS_business_tag = $objDB->Conn->Execute("select id,business_tags from merchant_user where id=?",array($all_mercahnts[$k]) );
		while($Row_business_tag = $RS_business_tag->FetchRow())
		{		
			$business_tag_records[$count_business_tag] = get_field_value($Row_business_tag);				
			$count_business_tag++;
		}
		$max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
		if($max_counter<= $max_counter1)
		{
			$max_counter = $max_counter1;
		}

	}
	
	$final_array = array();
	//echo $max_counter."==";
	for($j=0;$j<$max_counter;$j++)
	{
		for($y=0;$y<count($all_mercahnts);$y++)
		{
			if($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "")
			{
				// start location hour code
				
				$location_id=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];
				
				$time_zone=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"];
				date_default_timezone_set($time_zone);
                $current_day=date('D');
                $current_time=date('g:i A');
				/*$sql="select * from location_hours where location_id=".$location_id." and day='".strtolower($current_day)."'";
				$RS_hours_data = $objDB->execute_query($sql);*/
				$RS_hours_data = $objDB->Conn->Execute("select * from location_hours where location_id=? and day=?",array($location_id,strtolower($current_day)));

				$location_time="";
				$start_time = "";
				$end_time="";
				$status_time="";
				if($RS_hours_data->RecordCount()>0)
				{
					while($Row_data = $RS_hours_data->FetchRow())
					{
						$start_time = $Row_data['start_time'];
						$end_time=$Row_data['end_time'];
						$location_time.=$Row_data['start_time']." - ";
						$location_time.=$Row_data['end_time'];
					}
				}	
				$st_time    =   strtotime($start_time);
				$end_time   =   strtotime($end_time);
				$cur_time   =   strtotime($current_time);
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==1)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Open";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==0)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Close";
				}
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"]=$location_time;
				// end location hour code
				
				// start business name
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"]!="")
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"]=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"];
				}
				// end business name
				// start pricerange
				
				$val="";
				$val_text="";
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==1)
				{
					$val_text="Inexpensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==2)
				{		
					$val_text="Moderate";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==3)
				{
					$val_text="Expensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==4)
				{
					$val_text="Very Expensive";
				}
				else
				{
					$val_text="";
				}
				
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"]=$val_text;
				// end pricerange
				// start campaign array
				
				$count=0;
				$campaign_records=array();
				
				/*$campaign_sql = "SELECT c.id,c.title,c.category_id,cl.offers_left FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
					WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." and l.id=".$location_id." ORDER BY c.expiration_date";
				//echo $campaign_sql."<br/>";
				
				$RS_campaign_data=$objDB->Conn->Execute($campaign_sql);*/
				$RS_campaign_data=$objDB->Conn->Execute("SELECT c.id,c.title,c.category_id,cl.offers_left FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by WHERE   l.active = ?  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." and l.id=? ORDER BY c.expiration_date",array(1,$location_id));
				
				if($RS_campaign_data->RecordCount()>0)
				{
					while($Row_campaign = $RS_campaign_data->FetchRow())
					{		
						$campaign_records[$count] = get_field_value($Row_campaign);				
						$count++;
					}
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"]=$campaign_records;
				} 
				// end campaign array
				// start location category
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']!="")
				{
					$count=0;
					$cat_records=array();
					
					/*$cat_sql = "SELECT * from category_level where id in (".$arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'].")";
	   
					$RS_cat_data=$objDB->Conn->Execute($cat_sql);*/
					$RS_cat_data=$objDB->Conn->Execute("SELECT * from category_level where id in (?)",array($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']));
					
					if($RS_cat_data->RecordCount()>0)
					{
						while($Row_cat = $RS_cat_data->FetchRow())
						{		
							$cat_records[$count] = get_field_value($Row_cat);				
							$count++;
						}
						$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=$cat_records;
					}
				}
				else
				{
						$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=array();
				}
				// end location category
				array_push($final_array,$arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
				
			}
			
		} 
		
	} 
	$json_array['status'] = "true";
	
		$json_array["records"]= $final_array;
		
	//	$json_array["marker_records"]= $records_all;
		$json_array['business_tags_records'] = $business_tag_records;
		$json_array['total_records'] = count($json_array['records']);
		$json_array['marker_total_records'] = count($json_array['records']);
	}
	else
	{
		$json_array['all_records'] = 0;
		$json_array["records"]="";
		$json_array['business_tags_records'] = "";
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
		$json_array['is_profileset'] = 1;
		$json_array["marker_records"]= "";
		$json_array['marker_total_records'] = 0;
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses get search deal for location
 * @param category_id,mlatitude,mlongitude,dismile,location_id
 * @used in pages :my-deals.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetSearchDealForLocation']))
{
	$json_array = array();
	$records = array();
	$records_all=array();
	$json_array1 = array();
	$category_id=$_REQUEST['category_id'];
	$location_id=$_REQUEST['location_id'];
	$dismile=$_REQUEST['dismile'];
	//$dismile= 50;
	$date_f = date("Y-m-d H:i:s");
	$mlatitude=$_REQUEST['mlatitude'];
	$mlongitude=$_REQUEST['mlongitude'];
	if(isset($_REQUEST['dismile']))
	{
		$dismile= $_REQUEST['dismile'];
		$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
		//$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
	}
	$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
	$sel_expr = "  time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 as limelefttoexpire";
	$cust_where ="";

	$cat_str = "";
	if($_REQUEST['customer_id']!="")
	{
	$customer_id = $_REQUEST['customer_id'];
	$get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=".$customer_id." and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = ".$customer_id."
	and location_id=cl.location_id) total_reserved,";
		
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		//                $cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>"" and  country <>""  and id='.$customer_id;
		//                  $RS_cust_data=$objDB->Conn->Execute($cust_sql);
		//                   $is_profileset =  $RS_cust_data->RecordCount();
		//                   if($is_profileset == 0)
		//                   {
		//                       $json_array = array();
		//                        $json_array['status'] = "false";
		//        		  $json_array['is_profileset'] = 0;
		//                          $json = json_encode($json_array);
		//                        echo $json;
		//                        exit();
		//                   }

		// 03-10-2013 dist list deal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013	

		// 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
		// 05-10-2013	
	}
	else
	{
		$cust_where = " and c.level=1 ";
	}
	if(isset($_REQUEST['category_id']))
	{
		if($_REQUEST['category_id']==0)
		{
			$cat_str = "";
		}
		else
		{
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		}
	}
	
	
	/*$limit_data = "SELECT mu.business Business,mu.merchant_icon,l.avarage_rating,c.*,l.id locid ,c.id cid,l.location_name,l.timezone timezone ,l.created_by l_created_by ,
	l.location_permalink,l.address,l.city,l.state,l.zip,l.country,c.created_by c_created_by , ".$sel_expr." ,
	cl.offers_left,cl.campaign_type,cl.permalink ,round((((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." and l.id=".$location_id." ORDER BY distance,c.expiration_date";
    
	
	$RS_limit_data=$objDB->Conn->Execute($limit_data);*/
	$RS_limit_data=$objDB->Conn->Execute("SELECT mu.business Business,mu.merchant_icon,l.avarage_rating,c.*,l.id locid ,c.id cid,l.location_name,l.timezone timezone ,l.created_by l_created_by ,
	l.location_permalink,l.address,l.city,l.state,l.zip,l.country,l.latitude,l.longitude,c.created_by c_created_by , ".$sel_expr." ,
	cl.offers_left,cl.campaign_type,cl.permalink ,round((((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = ?  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." and l.id=? ORDER BY distance,c.expiration_date",array(1,$location_id));

	$levels = "";
    $categories = "";
    $expiring ="";
		
	if($RS_limit_data->RecordCount()>0)
	{
		$json_array['is_profileset'] = 1;          
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS_limit_data->RecordCount();
		$count=0;
		$arr_main_merchant_arr = array();
		$arr_main_location_arr = array();
		$all_mercahnts = array();
		
		$DetailHtml="";
		$DetailHtml.='<div class="list_carousel1" >';
		$DetailHtml.='<ul id="deal_slider_'.$location_id.'" style="width:100%;">';
			
		while($Row = $RS_limit_data->FetchRow())
		{		
			$records[$count] = get_field_value($Row);				
			$count++;
			
			$CampId=$Row['cid'];
			$storeid=$Row['locid'];
			
			
			$DetailHtml.='<li style="list-style:none">';
	 $t_l_estring= "";
								  $ex_array = explode(",",$Row['limelefttoexpire']);
								  for($i=0;$i<count($ex_array);$i++)
								  {
									  if($ex_array[$i] <= 24)
									  {
										$f_str = 1;
										}else
										{
											$f_str = 0;
										}
										$t_l_estring = $t_l_estring.$f_str.",";
										
								  }
								  $t_l_estring = trim($t_l_estring,",");
								  $levels .= $Row['campaign_type'].",";
								  $categories .= $Row['category_id'].",";
								  $expiring .= $t_l_estring.",";
								  $miles_arr = $Row['distance'].",";
								  $distance = $objJSON->distance($mlatitude, $mlongitude, $Row['latitude'], $Row['longitude'], "M");
								  $f_str = 0;
								  if($Row['discount'] <= 20)
										{
											$f_str = 1;
										}
										else if($Row['discount'] >= 21 && $Row['discount'] <= 49)
										{
											$f_str = 2;
										}
										else if($Row['discount'] >= 50 && $Row['discount'] <= 69)
										{
											$f_str = 3;
										}
										else if($Row['discount'] >= 70 && $Row['discount'] <= 100)
										{
											$f_str = 4;
										}
			$DetailHtml.='<div class="deal_blk" is_new="'.$Row['is_new'].'" discount_val="'.$f_str.'" is_expire = "'.$t_l_estring.'" level="'.$Row['campaign_type'].'" campid="'.$CampId.'" merid="'.$Row['l_created_by'].'" locid="'.$storeid.'" catid="'.$Row['category_id'].'" miles="'.$distance.'">';
			$DetailHtml.='<div class="offersexyellow">';
			

			if($Row['is_new']==1)
			{
				$DetailHtml.="<div class='new_24_hour_deal'><p>NEW</p></div>";
			}

			if($Row['business_logo']!="")
			{
				$img_src=ASSETS_IMG."/m/campaign/block/".$Row['business_logo']; 
			}
			   
			else 
			{
				$img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
			}
				
			$DetailHtml.="<div class='grid_img_div'>";
			$DetailHtml.="<img src='".$img_src."'>";
			$DetailHtml.="</div>";
			
			$campaign_value=$Row['deal_value'];
			$campaign_discount=$Row['discount'];
			$campaign_saving=$Row['saving'];
			
			$DetailHtml.='<div class="dealmetric_wrap">';
			
			if($campaign_value!="0")
			{	
				$DetailHtml.='<div class="dealmetric title">
				 <div class="value">Value</div>
				 <div class="discount">Discount</div>
				 <div class="saving">Savings</div>
				</div>

				<div class="dealmetric values">
				 <div class="value">$'.$campaign_value.'</div>
				 <div class="discount">'.$campaign_discount.'%</div>
				 <div class="saving">$'.$campaign_saving.'</div>
				</div>';
			}
			$DetailHtml.='</div>';
			
			if($Row['merchant_icon']!="")
			{
				$img_src=ASSETS_IMG."/m/icon/".$Row['merchant_icon']; 
			}
			   
			else 
			{
				$img_src=ASSETS_IMG."/c/Merchant.png";
			}
			$mer_ikon_img="<div class='mer_ikon_img'><img src='".$img_src."' /></div>";
			$businessname ="<div class='buss_rating'><a target='_parent' href='".$Row['location_permalink']."' title='Merchant Location' class='busi_name'>".$Row['Business'] ."</a>"; 
			$businessname.=$objJSON->get_location_rating($storeid);
			$businessname.="</div>";			
			//$DetailHtml.='<div class="percetage" style="">'.$mer_ikon_img.$businessname .'  </div>';
			$cmpdis =$Row['discount'];
			if($cmpdis!="")
			{
				//$DetailHtml.= '<div class="offerstrip">'.$cmpdis .'  </div>';
			}
			
			/*$Sql = "select count(*) tot from customer_campaigns where campaign_id = ".$CampId." and location_id = ".$storeid." and coupon_generation_date > DATE_SUB(NOW(), INTERVAL 48 HOUR)";
            $reserve_count1 = $objDB->Conn->Execute($Sql);*/
			$reserve_count1 = $objDB->Conn->Execute("select count(*) tot from customer_campaigns where campaign_id = ? and location_id =? and coupon_generation_date > DATE_SUB(NOW(), INTERVAL 48 HOUR)",array($CampId,$storeid));
			
			if($reserve_count1->fields['tot']  > 0)
			{
				$DetailHtml .= '<div class="strip" id="strip'.$CampId.$storeid.'" style="display:block"> '.$reserve_count1->fields['tot'].' People reserved this offer in last 48 hours</div>';
			}
			$CampTitle =$Row['title'];
			$businessname_popup=$Row['location_name'];
			$number_of_use=$Row['number_of_use'];
			$new_customer=$Row['new_customer'];
			$address = $Row['address'];
			$city = $Row['city'];
			$state = $Row['state'];
			$zip = $Row['zip'];
			$country = $Row['country'];
			$redeem_rewards=$Row['redeem_rewards'];
			$referral_rewards=$Row['referral_rewards'];
			// start check for sharing limitaion if 0 then sharing point display 0
			if($Row['referral_rewards'] != "")
			{
				/*$Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$CampId;

				$RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location);*/
				$RS_max_no_location = $objDB->Conn->Execute("SELECT max_no_sharing from campaigns WHERE id=?",array($CampId));

				/*$Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$CampId." and referred_customer_id<>0";
				$RS_shared = $objDB->Conn->Execute($Sql_shared);*/
				$RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?",array($CampId,0));

				if($RS_shared->RecordCount() >= $RS_max_no_location->fields['max_no_sharing'] )
				{
					$referral_rewards=0;
				}
				else
				{
					$referral_rewards=$Row['referral_rewards'];
				}
			}
			// end check for sharing limitaion if 0 then sharing point display 0
			
			$o_left = $Row['offers_left'];
			$expiration_date = date("Y-m-d g:i:s A", strtotime($Row['expiration_date'] ));
			if($Row['business_logo']!="")
			{
				$img_src=ASSETS_IMG."/m/campaign/block/".$Row['business_logo']; 
			}
			   
			else 
			{
				$img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
			}
			$deal_desc= $Row['deal_detail_description'];
			$visited = 0;
			if(!isset($_SESSION['customer_id']))
			{
				$c = 0;
			}
			else
			{
				$c = $_SESSION['customer_id'];
				/*$sql = "select * from customer_campaign_view where customer_id=".$_SESSION['customer_id']." and campaign_id =".$CampId;
				$RS = $objDB->Conn->Execute($sql);*/
				$RS = $objDB->Conn->Execute("select * from customer_campaign_view where customer_id=? and campaign_id =?",array($_SESSION['customer_id'],$CampId));

				if($RS->RecordCount() > 0)
				{
					$visited = 1;
				}
			}
			$campaign_tag=$Row['campaign_tag'];
			 $vi="";
			$popupurl=WEB_PATH."/popup_for_mymerchant.php?CampTitle=".urlencode($CampTitle)."&businessname=".urlencode($businessname_popup)."&number_of_use=".$number_of_use."&new_customer=".$new_customer."&address=".urlencode($address)."&city=".urlencode($city)."&state=".urlencode($state)."&country=".urlencode($country)."&zip=".$zip."&redeem_rewards=".$redeem_rewards."&referral_rewards=".$referral_rewards."&o_left=".$o_left."&expiration_date=".date("m/d/y g:i A", strtotime($expiration_date))."&img_src=".$img_src."&campid=".$CampId."&locationid=".$storeid."&deal_desc=".urlencode($deal_desc)."&is_reserve=0&br=sd&cust_id=".$c."&decoded_cust_id=".base64_encode($c)."&webpath=".urlencode(WEB_PATH)."&is_mydeals=0&voucher_id=".$vi."&visited=".$visited."&campaign_tag=".$campaign_tag;
			//$popupurl="";
			$DetailHtml .= '<div title="Campaign Title" class="dealtitle" mypopupid="'.$popupurl.'"  onClick="try1(this,'. $CampId .','.$storeid.')" data-fancybox-group="gallery">'.$CampTitle .'  </div>';
			
			$array_where_cat['id'] = $Row['category_id'];
			$RS_Cat = $objDB->Show("categories", $array_where_cat);
			$RS_Cat_image="<img class='kat_img' align='left' src='".ASSETS_IMG.$RS_Cat->fields['cat_image']."' title='".$RS_Cat->fields['cat_name']."' />";
			$viewbtn = '<a class="view-reserve" href="'.$Row['permalink'].'" target= "_parent">View And Reserve</a>&nbsp';
			
			$DetailHtml .= '<div class="btnview">'.$RS_Cat_image.$viewbtn .'  </div>';
			
			$DetailHtml.='</div>';
			$DetailHtml.='</div>';
			
			$DetailHtml.='</li>';
		
			
		}
		
		$DetailHtml.='</ul>';
		
		$DetailHtml.='<div class="clearfix"></div>';
		$DetailHtml.='<div class="navigation_center">';
		$DetailHtml.='<a id="'.$storeid.'prev1" class="prev1" href="#"></a>';
		$DetailHtml.='<a id="'.$storeid.'next1" class="next1" href="#"></a>';
		$DetailHtml.='</div>';											
		$DetailHtml.='</div>';
		
		$levels= trim($levels,",");
		$categories = trim($categories,",");
		$expiring = trim($expiring,",");
		$miles_arr = trim($miles_arr,",");
		$json_array['levels'] = $levels;
		$json_array['categories'] = $categories;
		$json_array['expiring'] = $expiring;
		$json_array['miles'] = $miles_arr;
		$json_array["records"]= $records;
		$json_array['total_records'] = count($json_array['records']);
		$json_array["DetailHtml"]= $DetailHtml;
	}
	else
	{
		$json_array["records"]="";
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
		$json_array['is_profileset'] = 1;
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses load previous offers left section
 * @param location_id
 * @used in pages :my-deals.php,mymerchants.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['load_previous_offers_left_section']))
{
	$json_array = array();
	$url_perameter_2 = "?purl=".urlencode("@00|".$_REQUEST['location_id']."@");
	if(isset($_SESSION['customer_id']))
	{
		$json_array['status'] = "true";
		
	}
	else
	{
		$json_array['status'] = "false";
		 $json_array['link'] = WEB_PATH."/register.php?url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
		 $json_array['hash_link'] = "#".urlencode("@00|".$_REQUEST['location_id']."@");
	}
	$json= json_encode($json_array);
	echo $json;
	exit();
}
/**
 * @uses get subscribed merchant's loaction
 * @param category_id,dismile,mlatitude,mlongitude,customer_id
 * @used in pages :my-deals.php,mymerchants.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetSubscribedMerchantLocations']))
{
	$json_array = array();
	$records = array();
	$records_all=array();
	$json_array1 = array();
	$category_id=$_REQUEST['category_id'];
	$dismile=$_REQUEST['dismile'];
	//$dismile= 50;
	$date_f = date("Y-m-d H:i:s");
	$mlatitude=$_REQUEST['mlatitude'];
	$mlongitude=$_REQUEST['mlongitude'];
	$id = $_REQUEST['customer_id'];
	$miles=$_REQUEST['dismile'];
	if(isset($_REQUEST['dismile']))
	{
		$dismile= $_REQUEST['dismile'];
		$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$miles*$miles ;	
		//$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
	}
	$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
	$sel_expr = "  group_concat(CAST(time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'-06:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 AS CHAR(7))separator ',') as limelefttoexpire";
	$cust_where ="";

	$cat_str = "";
	if($_REQUEST['customer_id']!="")
	{
	$customer_id = $_REQUEST['customer_id'];
	$get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=".$customer_id." and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = ".$customer_id."
	and location_id=cl.location_id) total_reserved,";
		
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		//                $cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>"" and  country <>""  and id='.$customer_id;
		//                  $RS_cust_data=$objDB->Conn->Execute($cust_sql);
		//                   $is_profileset =  $RS_cust_data->RecordCount();
		//                   if($is_profileset == 0)
		//                   {
		//                       $json_array = array();
		//                        $json_array['status'] = "false";
		//        		  $json_array['is_profileset'] = 0;
		//                          $json = json_encode($json_array);
		//                        echo $json;
		//                        exit();
		//                   }

		// 03-10-2013 dist list deal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013	

		// 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
		// 05-10-2013	
	}
	else
	{
		$cust_where = " and c.level=1 ";
	}
	if(isset($_REQUEST['category_id']))
	{
		if($_REQUEST['category_id']==0)
		{
			$cat_str = "";
		}
		else
		{
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		}
	}
	
	/*
	$limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
    */
	
	
	
	$limit_data = "SELECT l.id location_id , l.id locid ,
	group_concat(CAST(c.is_new AS CHAR(7)) separator ',') all_new_campaigns ,
	group_concat(CAST(cl.campaign_type AS CHAR(7)) separator ',') all_campaign_level ,
	c.id cid ,group_concat(CAST(c.category_id AS CHAR(7)) separator ',') all_categories 
	, group_concat(CAST(c.discount AS CHAR(7)) separator ',') all_discount 
	,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,".$sel_expr.",
	l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,
	l.categories,l.is_open,l.location_permalink,
                 round((((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance 
		,
	count(*) total_deals
	
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status =0)  ))
  ".$cat_str  ."  ".$date_wh." and ".$Where ." group by cl.location_id ORDER BY distance,c.expiration_date";
	
	//
	//and (
	//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
	//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
	$calculatedmiles = $dismile * $dismile;
	$RS_limit_data=$objDB->Conn->Execute("call mymerchant_script_web($calculatedmiles,$mlatitude,$mlongitude,$customer_id,0,'".CURR_TIMEZONE."')");
	 //$objDB->Conn->Close( );
	 //$objDB = new DB();
	
if($RS_limit_data->RecordCount()>0)
	{
		$json_array['is_profileset'] = 1;          
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS_limit_data->RecordCount();
		$json_array['all_records'] = $RS_limit_data->RecordCount();
		$count=0;
		$arr_main_merchant_arr = array();
		$arr_main_location_arr = array();
		$all_mercahnts = array();
		while($Row = $RS_limit_data->FetchRow())
		{
				$temp_merchant_arr = array();
				// remove indefined index error
				if(isset($arr_main_merchant_arr[$Row['merchant']]))
				{
					
				}
				else
				{
					$arr_main_merchant_arr[$Row['merchant']]="";
				}
				// remove indefined index error

				if($arr_main_merchant_arr[$Row['merchant']] != 0 )
				{
				$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
				}
				if( !in_array($Row['merchant'],$all_mercahnts))
				{
					array_push($all_mercahnts,$Row['merchant']);
				}
				$arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
		
				array_push($temp_merchant_arr,get_field_value($Row));
				$arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
		
		
				$records[$count] = get_field_value($Row);
				$count++;
		}
		
		$json = json_encode($arr_main_merchant_arr);
		$id=0;
		$final_array = array();
		$max_counter = 0;
	
	//select business_tags from merchant_users where id= ".$all_mercahnts[$k]]."
	$business_tag_records = array();
	$count_business_tag =0;
	for($k =0 ;$k<count($all_mercahnts);$k++)
	{
		$max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
		if($max_counter<= $max_counter1)
		{
			$max_counter = $max_counter1;
		}

	}
	$final_array = array();
	
	for($j=0;$j<$max_counter;$j++)
	{
		for($y=0;$y<count($all_mercahnts);$y++)
		{
		
			// remove indefined index error
			if(isset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]))
			{
				
			}
			else
			{
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]="";
			}
			// remove indefined index error
	
			if($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "")
			{
				// start location hour code
				
				//echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"]."</br>";
				//echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"]."</br>";
				// start distance
					$location_latitude=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["latitude"];
					$location_longitude=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["longitude"];
					$deal_distance = $objJSON->distance($mlatitude, $mlongitude, $location_latitude, $location_longitude, "M");
					//$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["distance"]=$deal_distance;
					// end distance
				$location_id=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];
				
				$time_zone=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone_name"];
				date_default_timezone_set($time_zone);
                $current_day=date('D');
                $current_time=date('g:i A');
				$location_time="";
					$start_time = "";
					$end_time="";
					$status_time="";
					
							$start_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"];
							$end_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];
							$location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"]." - ";
							$location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];
							
					
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==1)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]=
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Open";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==0)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Close";
				}
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"]=$location_time;
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"]!="")
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"]=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"];
				}
				$val="";
				$val_text="";
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==1)
				{
					$val_text="Inexpensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==2)
				{		
					$val_text="Moderate";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==3)
				{
					$val_text="Expensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==4)
				{
					$val_text="Very Expensive";
				}
				else
				{
					$val_text="";
				}
				
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"]=$val_text;
					
				// end pricerange
				
				
				// start campaign array
				
				$count=0;
				$campaign_records=array();
				
				$locationwise_campaign_array = array();
				$count=0;
					$campaignlist = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_id"];
					$cmapignlist_array = explode(",",$campaignlist);
					for($cnti=0;$cnti<count($cmapignlist_array);$cnti++)
					{
							$campaign_array  = array();
							$campaign_array['id'] = $cmapignlist_array[$cnti];
							array_push($locationwise_campaign_array,$campaign_array);
					}							
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"]= $locationwise_campaign_array;
				// end campaign array
				
				// start location category
				
				// start location category
					if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']!="")
					{
						$count=0;
						$catgory_array = array();
						$innercategory_array['id'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'];
						$innercategory_array['cat_name'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["locationcategories"];
						array_push($catgory_array,$innercategory_array);
							$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=$catgory_array;
					}
					else
					{
							$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=array();
					}
				
				// end location category
				
				array_push($final_array,$arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
				
			}
			
		} 
		
	} 
	$json_array['status'] = "true";
	
		$json_array["records"]= $final_array;
		
		$json_array['business_tags_records'] = $business_tag_records;
		$json_array['total_records'] = count($json_array['records']);
		$json_array['marker_total_records'] = count($json_array['records']);
	}
	else
	{
		$json_array['all_records'] = 0;
		$json_array["records"]="";
		$json_array['business_tags_records'] = "";
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
		$json_array['is_profileset'] = 1;
		$json_array["marker_records"]= "";
		$json_array['marker_total_records'] = 0;
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses get subscribed merchant for  loaction
 * @param location_id
 * @used in pages :my-deals.php,mymerchants.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetSubscribedMerchantForLocation']))
{

if(!isset($_SESSION['customer_id'])){

	$json_array = array();
	if(isset($_REQUEST['reload']))
	{
		$url_perameter_2 = "?purl=".urlencode("@".$_REQUEST['campaign_id']."|".$_REQUEST['location_id']."@");

	}
	else
	{
		$url_perameter_2 ="";
	}
	$json_array['loginstatus'] = "false";
	if(isset($_REQUEST['r_url']))
	{
		if(isset($_REQUEST['is_location']))
		{

			$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".WEB_PATH."/search-deal.php".$url_perameter_2;
		}
		else
		{
			$json_array['link'] = WEB_PATH."/register.php?url=".WEB_PATH."/search-deal.php".$url_perameter_2;
		}

	}
	else{
		if(isset($_REQUEST['is_location']))
		{

			$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
		}
		else
		{
			$json_array['link'] = WEB_PATH."/register.php?url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
		}
	}
	$json_array['is_profileset'] = 1;
	$json = json_encode($json_array);
	
	echo $json;
	exit();
}
	$json_array = array();
	$json_array['loginstatus'] = "true";
	$records = array();
	$records_all=array();
	$json_array1 = array();
	$category_id=$_REQUEST['category_id'];
	$location_id=$_REQUEST['location_id'];
	$dismile=$_REQUEST['dismile'];
	
	$date_f = date("Y-m-d H:i:s");
	$mlatitude=$_REQUEST['mlatitude'];
	$mlongitude=$_REQUEST['mlongitude'];
	$id = $_REQUEST['customer_id'];
	$miles=$_REQUEST['dismile'];
	if(isset($_REQUEST['dismile']))
	{
		$dismile= $_REQUEST['dismile'];
		$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$miles*$miles ;	
		//$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
	}
	$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
$sel_expr = "  time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 as limelefttoexpire";
	$cust_where ="";

	$cat_str = "";
	if($_REQUEST['customer_id']!="")
	{
	$customer_id = $_REQUEST['customer_id'];
	$get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=".$customer_id." and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = ".$customer_id."
	and location_id=cl.location_id) total_reserved,";
		
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		//                $cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>"" and  country <>""  and id='.$customer_id;
		//                  $RS_cust_data=$objDB->Conn->Execute($cust_sql);
		//                   $is_profileset =  $RS_cust_data->RecordCount();
		//                   if($is_profileset == 0)
		//                   {
		//                       $json_array = array();
		//                        $json_array['status'] = "false";
		//        		  $json_array['is_profileset'] = 0;
		//                          $json = json_encode($json_array);
		//                        echo $json;
		//                        exit();
		//                   }

		// 03-10-2013 dist list deal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013	

		// 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
		// 05-10-2013	
	}
	else
	{
		$cust_where = " and c.level=1 ";
	}
	if(isset($_REQUEST['category_id']))
	{
		if($_REQUEST['category_id']==0)
		{
			$cat_str = "";
		}
		else
		{
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		}
	}
	
	/*
	$limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
    */
	
	
	
	/*$limit_data = "SELECT mu.business Business,mu.merchant_icon,l.avarage_rating,c.*,l.id locid ,c.id cid,l.location_name,l.timezone timezone ,l.created_by l_created_by ,
	l.location_permalink,l.address,l.city,l.state,l.zip,l.country,c.created_by c_created_by , cl.offers_left,cl.campaign_type,cl.permalink , ".$sel_expr." ,
                 round((((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status =0)  ))
  ".$cat_str  ."  ".$date_wh." and ".$Where ." and l.id=".$location_id." ORDER BY distance,c.expiration_date";
	
	//
	//and (
	//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
	//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
	
	$RS_limit_data=$objDB->Conn->Execute($limit_data);*/

	$RS_limit_data=$objDB->Conn->Execute("SELECT mu.business Business,mu.merchant_icon,l.avarage_rating,c.*,l.id locid ,c.id cid,l.location_name,l.timezone timezone ,l.created_by l_created_by ,
	l.location_permalink,l.address,l.city,l.state,l.zip,l.country,l.latitude,l.longitude,c.created_by c_created_by , cl.offers_left,cl.campaign_type,cl.permalink , ".$sel_expr." ,
                 round((((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
 WHERE l.active = ? and c.is_walkin <> ? and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=?) or c.level =? ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=? and ss.subscribed_status=?)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status =?)  ))
  ".$cat_str  ."  ".$date_wh." and ".$Where ." and l.id=? ORDER BY distance,c.expiration_date",array(1,1,$id,1,$id,1,$id,$id,0,$location_id));

	
	if($RS_limit_data->RecordCount()>0)
	{
		$json_array['is_profileset'] = 1;          
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS_limit_data->RecordCount();
		$count=0;
		$arr_main_merchant_arr = array();
		$arr_main_location_arr = array();
		$all_mercahnts = array();
		
		$DetailHtml="";
		$DetailHtml.='<div class="list_carousel1" >';
		$DetailHtml.='<ul id="deal_slider_'.$location_id.'"  style="width:100%;">';
			
		while($Row = $RS_limit_data->FetchRow())
		{		
			$records[$count] = get_field_value($Row);				
			$count++;
			
			$CampId=$Row['cid'];
			$storeid=$Row['locid'];
			
			
			$DetailHtml.='<li style="list-style:none">';
			 $t_l_estring= "";
								  $ex_array = explode(",",$Row['limelefttoexpire']);
								  for($i=0;$i<count($ex_array);$i++)
								  {
									  if($ex_array[$i] <= 24)
									  {
										$f_str = 1;
										}else
										{
											$f_str = 0;
										}
										$t_l_estring = $t_l_estring.$f_str.",";
										
								  }
								  $t_l_estring = trim($t_l_estring,",");
								  $f_str = 0;
								  if($Row['discount'] <= 20)
										{
											$f_str = 1;
										}
										else if($Row['discount'] >= 21 && $Row['discount'] <= 49)
										{
											$f_str = 2;
										}
										else if($Row['discount'] >= 50 && $Row['discount'] <= 69)
										{
											$f_str = 3;
										}
										else if($Row['discount'] >= 70 && $Row['discount'] <= 100)
										{
											$f_str = 4;
										}
			$DetailHtml.='<div class="deal_blk"  is_new="'.$Row['is_new'].'"  discount_val="'.$f_str.'"  is_expire = "'.$t_l_estring.'" level="'.$Row['campaign_type'].'" campid="'.$CampId.'" merid="'.$Row['l_created_by'].'" locid="'.$storeid.'" catid="'.$Row['category_id'].'" miles="'.$Row['distance'].'">';
			$DetailHtml.='<div class="offersexyellow">';
			
			if($Row['is_new']==1)
			{
				$DetailHtml.="<div class='new_24_hour_deal'><p>NEW</p></div>";
			}
			
			if($Row['business_logo']!="")
			{
				$img_src=ASSETS_IMG."/m/campaign/block/".$Row['business_logo']; 
			}
			   
			else 
			{
				$img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
			}
								
			$DetailHtml.="<div class='grid_img_div'>";
			$DetailHtml.="<img  src='".$img_src."'>";
			$DetailHtml.="</div>";
			
			$campaign_value=$Row['deal_value'];
			$campaign_discount=$Row['discount'];
			$campaign_saving=$Row['saving'];
			
			$DetailHtml.='<div class="dealmetric_wrap">';
			
			if($campaign_value!="0")
			{	
				$DetailHtml.='<div class="dealmetric title">
				 <div class="value">Value</div>
				 <div class="discount">Discount</div>
				 <div class="saving">Savings</div>
				</div>

				<div class="dealmetric values">
				 <div class="value">$'.$campaign_value.'</div>
				 <div class="discount">'.$campaign_discount.'%</div>
				 <div class="saving">$'.$campaign_saving.'</div>
				</div>';
			}
			
			$DetailHtml.='</div>';
			
			if($Row['merchant_icon']!="")
			{
				$img_src=ASSETS_IMG."/m/icon/".$Row['merchant_icon']; 
			}
			   
			else 
			{
				$img_src=ASSETS_IMG."/c/Merchant.png";
			}
			$mer_ikon_img="<div class='mer_ikon_img'><img src='".$img_src."' /></div>";
			$businessname ="<div class='buss_rating'><a target='_parent' href='".$Row['location_permalink']."' title='Merchant Location' class='busi_name'>".$Row['Business'] ."</a>"; 
			$businessname.=$objJSON->get_location_rating($storeid);
			$businessname.="</div>";			
			//$DetailHtml.='<div class="percetage" style="">'.$mer_ikon_img.$businessname .'  </div>';
			$cmpdis =$Row['discount'];
			if($cmpdis!="")
			{
				//$DetailHtml.= '<div class="offerstrip">'.$cmpdis .'  </div>';
			}
			
/*$Sql = "select count(*) tot from customer_campaigns where campaign_id = ".$CampId." and location_id = ".$storeid." and coupon_generation_date > DATE_SUB(NOW(), INTERVAL 48 HOUR)";
            $reserve_count1 = $objDB->Conn->Execute($Sql);*/
			$reserve_count1 = $objDB->Conn->Execute("select count(*) tot from customer_campaigns where campaign_id = ? and location_id = ? and coupon_generation_date > DATE_SUB(NOW(), INTERVAL 48 HOUR)",array($CampId,$storeid));
			
			if($reserve_count1->fields['tot']  > 0)
			{
				$DetailHtml .= '<div class="strip" id="strip'.$CampId.$storeid.'" style="display:block"> '.$reserve_count1->fields['tot'].' People reserved this offer in last 48 hours</div>';
			}
			$CampTitle =$Row['title'];
			$businessname_popup=$Row['location_name'];
			$number_of_use=$Row['number_of_use'];
			$new_customer=$Row['new_customer'];
			$address = $Row['address'];
			$city = $Row['city'];
			$state = $Row['state'];
			$zip = $Row['zip'];
			$country = $Row['country'];
			$redeem_rewards=$Row['redeem_rewards'];
			$referral_rewards=$Row['referral_rewards'];
			// start check for sharing limitaion if 0 then sharing point display 0
			if($Row['referral_rewards'] != "")
			{
				/*$Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$CampId;

				$RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location);*/
				$RS_max_no_location = $objDB->Conn->Execute("SELECT max_no_sharing from campaigns WHERE id=?",array($CampId));

				/*$Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$CampId." and referred_customer_id<>0";
				$RS_shared = $objDB->Conn->Execute($Sql_shared);*/
				$RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?",array($CampId,0));

				if($RS_shared->RecordCount() >= $RS_max_no_location->fields['max_no_sharing'] )
				{
					$referral_rewards=0;
				}
				else
				{
					$referral_rewards=$Row['referral_rewards'];
				}
			}
			// end check for sharing limitaion if 0 then sharing point display 0
			
			$o_left = $Row['offers_left'];
			$expiration_date = date("Y-m-d g:i:s A", strtotime($Row['expiration_date'] ));
			if($Row['business_logo']!="")
			{
				$img_src=ASSETS_IMG."/m/campaign/block/".$Row['business_logo']; 
			}
			   
			else 
			{
				$img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
			}
			$deal_desc= $Row['deal_detail_description'];
			$visited = 0;
			if(!isset($_SESSION['customer_id']))
			{
				$c = 0;
			}
			else
			{
				$c = $_SESSION['customer_id'];
				/*$sql = "select * from customer_campaign_view where customer_id=".$_SESSION['customer_id']." and campaign_id =".$CampId;
				$RS = $objDB->Conn->Execute($sql);*/
				$RS = $objDB->Conn->Execute("select * from customer_campaign_view where customer_id=? and campaign_id =?",array($_SESSION['customer_id'],$CampId));

				if($RS->RecordCount() > 0)
				{
					$visited = 1;
				}
			}
			$campaign_tag=$Row['campaign_tag'];
			$vi=""; 
			$popupurl=WEB_PATH."/popup_for_mymerchant.php?CampTitle=".urlencode($CampTitle)."&businessname=".urlencode($businessname_popup)."&number_of_use=".$number_of_use."&new_customer=".$new_customer."&address=".urlencode($address)."&city=".urlencode($city)."&state=".urlencode($state)."&country=".urlencode($country)."&zip=".$zip."&redeem_rewards=".$redeem_rewards."&referral_rewards=".$referral_rewards."&o_left=".$o_left."&expiration_date=".date("m/d/y g:i A", strtotime($expiration_date))."&img_src=".$img_src."&campid=".$CampId."&locationid=".$storeid."&deal_desc=".urlencode($deal_desc)."&is_reserve=0&br=sd&cust_id=".$c."&decoded_cust_id=".base64_encode($c)."&webpath=".urlencode(WEB_PATH)."&is_mydeals=0&voucher_id=".$vi."&visited=".$visited."&campaign_tag=".$campaign_tag;
			//$popupurl="";
			$DetailHtml .= '<div title="Campaign Title" class="dealtitle" mypopupid="'.$popupurl.'" onClick="try1(this,'. $CampId .','.$storeid.')" data-fancybox-group="gallery">'.$CampTitle .'  </div>';
			
			$array_where_cat['id'] = $Row['category_id'];
			$RS_Cat = $objDB->Show("categories", $array_where_cat);
			$RS_Cat_image="<img class='kat_img' align='left' src='".ASSETS_IMG.$RS_Cat->fields['cat_image']."' title='".$RS_Cat->fields['cat_name']."' />";
			$viewbtn = '<a href="'.$Row['permalink'].'" target= "_parent" class="view-reserve">View And Reserve</a>&nbsp';
			
			$DetailHtml .= '<div class="btnview">'.$RS_Cat_image.$viewbtn .'  </div>';
			
			$DetailHtml.='</div>';
			$DetailHtml.='</div>';
			
			$DetailHtml.='</li>';
		
			
		}
		
		$DetailHtml.='</ul>';
		
		$DetailHtml.='<div class="clearfix"></div>';
		$DetailHtml.='<div class="navigation_center">';
		$DetailHtml.='<a id="'.$storeid.'prev1" class="prev1" href="#"></a>';
		$DetailHtml.='<a id="'.$storeid.'next1" class="next1" href="#"></a>';
		$DetailHtml.='</div>';											
		$DetailHtml.='</div>';
			
		$json_array["records"]= $records;
		$json_array['total_records'] = count($json_array['records']);
		$json_array["DetailHtml"]= $DetailHtml;
	}
	else
	{
		$json_array["records"]="";
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
		$json_array['is_profileset'] = 1;
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses get saved offers
 * @param location_id
 * @used in pages :my-deals.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetSavedOffers']))
{
	$json_array = array();
	$records = array();
	$records_all=array();
	$json_array1 = array();
	$category_id=$_REQUEST['category_id'];
	$dismile=$_REQUEST['dismile'];
	//$dismile= 50;
	$date_f = date("Y-m-d H:i:s");
	$mlatitude=$_REQUEST['mlatitude'];
	$mlongitude=$_REQUEST['mlongitude'];
	$id = $_REQUEST['customer_id'];
	$miles=$_REQUEST['dismile'];
	if(isset($_REQUEST['dismile']))
	{
		$dismile= $_REQUEST['dismile'];
		$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$miles*$miles ;	
		//$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
	}
	$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
	$sel_expr = "  group_concat(CAST(time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 AS CHAR(7))separator ',') as limelefttoexpire";
	$cust_where ="";

	$cat_str = "";
	if($_REQUEST['customer_id']!="")
	{
	$customer_id = $_REQUEST['customer_id'];
	$get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=".$customer_id." and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = ".$customer_id."
	and location_id=cl.location_id) total_reserved,";
		
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		//                $cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>"" and  country <>""  and id='.$customer_id;
		//                  $RS_cust_data=$objDB->Conn->Execute($cust_sql);
		//                   $is_profileset =  $RS_cust_data->RecordCount();
		//                   if($is_profileset == 0)
		//                   {
		//                       $json_array = array();
		//                        $json_array['status'] = "false";
		//        		  $json_array['is_profileset'] = 0;
		//                          $json = json_encode($json_array);
		//                        echo $json;
		//                        exit();
		//                   }

		// 03-10-2013 dist list deal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013	

		// 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
		// 05-10-2013	
	}
	else
	{
		$cust_where = " and c.level=1 ";
	}
	if(isset($_REQUEST['category_id']))
	{
		if($_REQUEST['category_id']==0)
		{
			$cat_str = "";
		}
		else
		{
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		}
	}
	
	/*
	$limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
    */
	
	
	/*
	l.id location_id , group_concat(CAST(c.category_id AS CHAR(7)) separator ',') all_categories ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country
	,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating
	,l.categories,l.is_open,".$get_dat." round((((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance ,l.location_permalink,
	count(*) total_deals,mu.business
	*/
	$limit_data = "SELECT c.id cid,l.id locid ,
	group_concat(CAST(c.is_new AS CHAR(7)) separator ',') all_new_campaigns ,
	group_concat(CAST(cl.campaign_type AS CHAR(7)) separator ',') all_campaign_level 
	, group_concat(CAST(c.discount AS CHAR(7)) separator ',') all_discount 
	,group_concat(CAST(c.category_id AS CHAR(7)) separator ',') all_categories , l.id location_id ,l.created_by merchant,l.location_name,l.address,
	l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,".$sel_expr." ,
	l.longitude,l.avarage_rating,l.categories,l.is_open,
                round((((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance ,l.location_permalink
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." group by cl.location_id ORDER BY distance,c.expiration_date";
	
	//
	//and (
	//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
	//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
	 $mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
if(!$mysqli) die('Could not connect: ' . mysqli_error());
mysqli_select_db($mysqli, DATABASE_NAME);
if(!$mysqli) die('Could not connect to DB: ' . mysqli_error()); 
 $calculatedmiles = 50*50;
// GetAllUserSessions(IN username CHAR(20))
$result = $mysqli->query("call mydeals_script_web($calculatedmiles,$mlatitude,$mlongitude,$customer_id,0,'".CURR_TIMEZONE."')");
if(!$result) die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
$RS_limit_data = $result;

	if($RS_limit_data->num_rows >0)
	{
		$json_array['is_profileset'] = 1;          
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS_limit_data->num_rows;
		$json_array['all_records'] = $RS_limit_data->num_rows;
		$count=0;
		$arr_main_merchant_arr = array();
		$arr_main_location_arr = array();
		$all_mercahnts = array();
		while($Row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
				$temp_merchant_arr = array();
				
				// remove indefined index error
				if(isset($arr_main_merchant_arr[$Row['merchant']]))
				{
					
				}
				else
				{
					$arr_main_merchant_arr[$Row['merchant']]="";
				}
				// remove indefined index error

				if($arr_main_merchant_arr[$Row['merchant']] != 0 )
				{
					$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
				}
				if( !in_array($Row['merchant'],$all_mercahnts))
				{
					array_push($all_mercahnts,$Row['merchant']);
				}
				$arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
		
				array_push($temp_merchant_arr,get_field_value($Row));
				$arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
		
		
				$records[$count] = get_field_value($Row);
				$count++;
		}
		
		$json = json_encode($arr_main_merchant_arr);
		$id=0;
		$final_array = array();
		$max_counter = 0;
	
	//select business_tags from merchant_users where id= ".$all_mercahnts[$k]]."
	$business_tag_records = array();
	$count_business_tag =0;
	for($k =0 ;$k<count($all_mercahnts);$k++)
	{
		$max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
		if($max_counter<= $max_counter1)
		{
			$max_counter = $max_counter1;
		}

	}
	$final_array = array();
	for($j=0;$j<$max_counter;$j++)
	{
		for($y=0;$y<count($all_mercahnts);$y++)
		{
		
			// remove indefined index error
			if(isset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]))
			{
				
			}
			else
			{
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]="";
			}
			// remove indefined index error

			if($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "")
			{
				// start location hour code
				// start distance
					$location_latitude=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["latitude"];
					$location_longitude=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["longitude"];
					$deal_distance = $objJSON->distance($mlatitude, $mlongitude, $location_latitude, $location_longitude, "M");
					//$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["distance"]=$deal_distance;
					// end distance
				$location_id=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];
				
				$time_zone=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone_name"];
				date_default_timezone_set($time_zone);
                $current_day=date('D');
                $current_time=date('g:i A');
				$location_time="";
					$start_time = "";
					$end_time="";
					$status_time="";
					
							$start_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"];
							$end_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];
							$location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"]." - ";
							$location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];
							
					
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==1)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]=
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Open";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==0)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Close";
				}
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"]=$location_time;
				// end location hour code
				// start business name
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"]!="")
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"]=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"];
				}
				
				// end business name
				
				// start pricerange
				
				$val="";
				$val_text="";
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==1)
				{
					$val_text="Inexpensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==2)
				{		
					$val_text="Moderate";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==3)
				{
					$val_text="Expensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==4)
				{
					$val_text="Very Expensive";
				}
				else
				{
					$val_text="";
				}
				
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"]=$val_text;
					
				// end pricerange
				// start campaign array
				
				$count=0;
				$campaign_records=array();
				
				$locationwise_campaign_array = array();
				$count=0;
					$campaignlist = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_id"];
					$campaignnumberofusecampaign_array = explode(",",$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_numberofuse"]);		
					$campaignofferleft_array = explode(",",$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_offersleft"]);
										
					$cmapignlist_array = explode(",",$campaignlist);
					for($cnti=0;$cnti<count($cmapignlist_array);$cnti++)
					{
							$campaign_array  = array();
								$array_where_camp=array();
						$array_where_camp['campaign_id'] = $cmapignlist_array[$cnti];;
						$array_where_camp['customer_id'] = $_REQUEST['customer_id'];
						$array_where_camp['referred_customer_id'] = 0;
						$array_where_camp['location_id'] = $location_id;
						$RS_camp = $objDB->Show("reward_user", $array_where_camp);
						if($RS_camp->RecordCount()>0 && $campaignnumberofusecampaign_array[$cnti]=="1")
						{
							//echo "1 ".$Row_campaign['id'].",".$location_id;
						} 
						else if ($RS_camp->RecordCount()>0 && ($campaignnumberofusecampaign_array[$cnti]=="2" || $campaignnumberofusecampaign_array[$cnti]=="3" ) && $campaignofferleft_array[$cnti]==0) 
						{
							//echo "2 ".$Row_campaign['id'].",".$location_id;
						}
						else
						{
							//echo "else";
							$campaign_array  = array();
							$campaign_array['id'] = $cmapignlist_array[$cnti];
						
							array_push($locationwise_campaign_array,$campaign_array);
							$count++;
						}
					}						
				if( $count != 0 )
				{
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"]= $locationwise_campaign_array;
				}
				// end campaign array
				
				// start location category
				
				// start location category
					if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']!="")
					{
						//$count=0;
						$catgory_array = array();
						$innercategory_array['id'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'];
						$innercategory_array['cat_name'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["locationcategories"];
						array_push($catgory_array,$innercategory_array);
							$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=$catgory_array;
					}
					else
					{
							$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=array();
					}
				
				// end location category
				if($count !=0)
				{
				array_push($final_array,$arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
				}
				
			}
			
		} 
		
	} 
	$json_array['status'] = "true";
	
		$json_array["records"]= $final_array;
		
		$json_array['business_tags_records'] = $business_tag_records;
		$json_array['all_records'] =  count($json_array['records']);
		$json_array['total_records'] = count($json_array['records']);
		$json_array['marker_total_records'] = count($json_array['records']);
	}
	else
	{
		$json_array['all_records'] = 0;
		$json_array["records"]="";
		$json_array['business_tags_records'] = "";
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
		$json_array['is_profileset'] = 1;
		$json_array["marker_records"]= "";
		$json_array['marker_total_records'] = 0;
		$json = json_encode($json_array);
		$result->close();
$mysqli->next_result();
$mysqli->close();
		echo $json;
		exit();
	}
	$json = json_encode($json_array);
	$result->close();
$mysqli->next_result();
$mysqli->close();
	echo $json;
	exit();
}

/**
 * @uses get saved offers for location
 * @param location_id
 * @used in pages :my-deals.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetSavedOfferForLocation']))
{
	if(!isset($_SESSION['customer_id'])){

	$json_array = array();
	if(isset($_REQUEST['reload']))
	{
		$url_perameter_2 = "?purl=".urlencode("@".$_REQUEST['campaign_id']."|".$_REQUEST['location_id']."@");

	}
	else
	{
		$url_perameter_2 ="";
	}
	$json_array['loginstatus'] = "false";
	if(isset($_REQUEST['r_url']))
	{
		if(isset($_REQUEST['is_location']))
		{

			$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".WEB_PATH."/search-deal.php".$url_perameter_2;
		}
		else
		{
			$json_array['link'] = WEB_PATH."/register.php?url=".WEB_PATH."/search-deal.php".$url_perameter_2;
		}

	}
	else{
		if(isset($_REQUEST['is_location']))
		{

			$json_array['link'] = WEB_PATH."/register.php?is_location=yes&url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
		}
		else
		{
			$json_array['link'] = WEB_PATH."/register.php?url=".$_SERVER['HTTP_REFERER'].$url_perameter_2;
		}
	}
	$json_array['is_profileset'] = 1;
	$json = json_encode($json_array);
	
	echo $json;
	exit();
	}
	$json_array = array();
	$json_array['loginstatus'] = "true";
	$records = array();
	$records_all=array();
	$json_array1 = array();
	$category_id=$_REQUEST['category_id'];
	$location_id=$_REQUEST['location_id'];
	$dismile=$_REQUEST['dismile'];
	//$dismile= 50;
	$date_f = date("Y-m-d H:i:s");
	$mlatitude=$_REQUEST['mlatitude'];
	$mlongitude=$_REQUEST['mlongitude'];
	$id = $_REQUEST['customer_id'];
	$miles=$_REQUEST['dismile'];
	if(isset($_REQUEST['dismile']))
	{
		$dismile= $_REQUEST['dismile'];
		$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$miles*$miles ;	
		//$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
	}
	$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>=0"; 
$sel_expr = "  time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 as limelefttoexpire";
	
	$cust_where ="";

	$cat_str = "";
	if($_REQUEST['customer_id']!="")
	{
	$customer_id = $_REQUEST['customer_id'];
	$get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=".$customer_id." and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = ".$customer_id."
	and location_id=cl.location_id) total_reserved,";
		
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		//                $cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>"" and  country <>""  and id='.$customer_id;
		//                  $RS_cust_data=$objDB->Conn->Execute($cust_sql);
		//                   $is_profileset =  $RS_cust_data->RecordCount();
		//                   if($is_profileset == 0)
		//                   {
		//                       $json_array = array();
		//                        $json_array['status'] = "false";
		//        		  $json_array['is_profileset'] = 0;
		//                          $json = json_encode($json_array);
		//                        echo $json;
		//                        exit();
		//                   }

		// 03-10-2013 dist list deal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013	

		// 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
		// 05-10-2013	
	}
	else
	{
		$cust_where = " and c.level=1 ";
	}
	if(isset($_REQUEST['category_id']))
	{
		if($_REQUEST['category_id']==0)
		{
			$cat_str = "";
		}
		else
		{
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		}
	}
	
	/*
	$limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
    */
	
	
	
	/*$limit_data = "SELECT mu.business Business,mu.merchant_icon,l.avarage_rating,c.*,
	l.id locid ,c.id cid,l.location_name,l.timezone timezone ,l.created_by l_created_by ,
	l.location_permalink,l.address,l.city,l.state,l.zip,l.country,c.created_by c_created_by , 
	cl.offers_left,cl.campaign_type,cl.permalink ,c.number_of_use ,".$sel_expr." ,
                round((((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
 WHERE l.active = 1 and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." and l.id=".$location_id." ORDER BY distance,c.expiration_date";
	
	//
	//and (
	//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
	//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
	
	$RS_limit_data=$objDB->Conn->Execute($limit_data);*/
	$RS_limit_data=$objDB->Conn->Execute("SELECT mu.business Business,mu.merchant_icon,l.avarage_rating,c.*,
	l.id locid ,c.id cid,l.location_name,l.timezone timezone ,l.created_by l_created_by ,
	l.location_permalink,l.address,l.city,l.state,l.zip,l.country,l.latitude,l.longitude,c.created_by c_created_by , 
	cl.offers_left,cl.campaign_type,cl.permalink ,c.number_of_use ,".$sel_expr." ,
                round((((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance 
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
 WHERE l.active =? and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status=?)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." and l.id=? ORDER BY distance,c.expiration_date",array(1,$id,1,$location_id));
	
	
	if($RS_limit_data->RecordCount()>0)
	{
		$json_array['is_profileset'] = 1;          
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS_limit_data->RecordCount();
		$json_array['all_records'] = $RS_limit_data->RecordCount();
		$count=0;
		$arr_main_merchant_arr = array();
		$arr_main_location_arr = array();
		$all_mercahnts = array();
		
		$levels = "";
		$categories = "";
		$expiring = "";
		
		$DetailHtml="";
		$DetailHtml.='<div class="list_carousel1" >';
		$DetailHtml.='<ul id="deal_slider_'.$location_id.'" style="width:100%;">';
		
		while($Row = $RS_limit_data->FetchRow())
		{			
			//echo $Row['cid']."-".$Row['locid']."-".$_REQUEST['customer_id']."</br>";
			$array_where_camp=array();
			$array_where_camp['campaign_id'] = $Row['cid'];
			$array_where_camp['customer_id'] = $_REQUEST['customer_id'];
			$array_where_camp['referred_customer_id'] = 0;
			$array_where_camp['location_id'] = $Row['locid'];
			$RS_camp = $objDB->Show("reward_user", $array_where_camp);
			
			$array_where_camp1=array();
			$array_where_camp1['campaign_id'] = $Row['cid'];
			$array_where_camp1['location_id'] = $Row['locid'];
			$campLoc = $objDB->Show("campaign_location", $array_where_camp1);
			
			if($RS_camp->RecordCount()>0 && $Row['number_of_use']=="1")
			{
				//echo "1 ".$Row_campaign['id'].",".$location_id;
			} 
			else if ($RS_camp->RecordCount()>0 && ($Row['number_of_use']=="2" || $Row['number_of_use']=="3" ) && $campLoc->fields['offers_left']==0) 
			{
				//echo "2 ".$Row_campaign['id'].",".$location_id;
			}
			else
			{	
				$records[$count] = get_field_value($Row);				
				$count++;
			
			
			$CampId=$Row['cid'];
			$storeid=$Row['locid'];
			
			 $t_l_estring= "";
								  $ex_array = explode(",",$Row['limelefttoexpire']);
								  for($i=0;$i<count($ex_array);$i++)
								  {
									  if($ex_array[$i] <= 24)
									  {
										$f_str = 1;
										}else
										{
											$f_str = 0;
										}
										$t_l_estring = $t_l_estring.$f_str.",";
										
								  }
								  $f_str = 0;
								  if($Row['discount'] <= 20)
										{
											$f_str = 1;
										}
										else if($Row['discount'] >= 21 && $Row['discount'] <= 49)
										{
											$f_str = 2;
										}
										else if($Row['discount'] >= 50 && $Row['discount'] <= 69)
										{
											$f_str = 3;
										}
										else if($Row['discount'] >= 70 && $Row['discount'] <= 100)
										{
											$f_str = 4;
										}
								  $t_l_estring = trim($t_l_estring,",");
								  $levels .= $Row['campaign_type'].",";
								  $categories .= $Row['category_id'].",";
								  $expiring .= $t_l_estring.",";
			$DetailHtml.='<li style="list-style:none">';

			$DetailHtml.='<div class="deal_blk"  is_new="'.$Row['is_new'].'"  discount_val="'.$f_str.'"  is_expire = "'.$t_l_estring.'" level="'.$Row['campaign_type'].'" campid="'.$CampId.'" merid="'.$Row['l_created_by'].'" locid="'.$storeid.'" catid="'.$Row['category_id'].'" miles="'.$Row['distance'].'">';
			$DetailHtml.='<div class="offersexyellow">';
			
			if($Row['is_new']==1)
			{
				$DetailHtml.="<div class='new_24_hour_deal'><p>NEW</p></div>";
			}
			if($Row['business_logo']!="")
			{
				$img_src=ASSETS_IMG."/m/campaign/block/".$Row['business_logo']; 
			}
			   
			else 
			{
				$img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
			}
								
			$DetailHtml.="<div class='grid_img_div'>";
			$DetailHtml.="<img  src='".$img_src."'>";
			$DetailHtml.="</div>";
			
			$campaign_value=$Row['deal_value'];
			$campaign_discount=$Row['discount'];
			$campaign_saving=$Row['saving'];
			
			$DetailHtml.='<div class="dealmetric_wrap">';
			
			if($campaign_value!="0")
			{	
				$DetailHtml.='<div class="dealmetric title">
				 <div class="value">Value</div>
				 <div class="discount">Discount</div>
				 <div class="saving">Savings</div>
				 </div>

				<div class="dealmetric values">
				 <div class="value">$'.$campaign_value.'</div>
				 <div class="discount">'.$campaign_discount.'%</div>
				 <div class="saving">$'.$campaign_saving.'</div>
				</div>';
			}
			
			$DetailHtml.='</div>';
			
			if($Row['merchant_icon']!="")
			{
				$img_src=ASSETS_IMG."/m/icon/".$Row['merchant_icon']; 
			}
			   
			else 
			{
				$img_src=ASSETS_IMG."/c/Merchant.png";
			}
			$mer_ikon_img="<div class='mer_ikon_img'><img src='".$img_src."' /></div>";
			$businessname ="<div class='buss_rating'><a target='_parent' href='".$Row['location_permalink']."' title='Merchant Location' class='busi_name'>".$Row['Business'] ."</a>"; 
			$businessname.=$objJSON->get_location_rating($storeid);
			$businessname.="</div>";			
			//$DetailHtml.='<div class="percetage" style="">'.$mer_ikon_img.$businessname .'  </div>';
			$cmpdis =$Row['discount'];
			if($cmpdis!="")
			{
				//$DetailHtml.= '<div class="offerstrip">'.$cmpdis .'  </div>';
			}
			
			/*$Sql = "select count(*) tot from customer_campaigns where campaign_id = ".$CampId." and location_id = ".$storeid." and coupon_generation_date > DATE_SUB(NOW(), INTERVAL 48 HOUR)";
            $reserve_count1 = $objDB->Conn->Execute($Sql);*/
			$reserve_count1 = $objDB->Conn->Execute("select count(*) tot from customer_campaigns where campaign_id = ? and location_id = ? and coupon_generation_date > DATE_SUB(NOW(), INTERVAL 48 HOUR)",array($CampId,$storeid));

			/*$Sql =  "Select count(*) tot from coupon_redeem where coupon_id in (select id from coupon_codes where customer_campaign_code=".$CampId." and location_id=".$storeid." ) and redeem_date > DATE_SUB(NOW(), INTERVAL 48 HOUR)  ";
			$reserve_count1 = $objDB->Conn->Execute($Sql);	*/
			$reserve_count1 = $objDB->Conn->Execute("Select count(*) tot from coupon_redeem where coupon_id in (select id from coupon_codes where customer_campaign_code=? and location_id=?) and redeem_date > DATE_SUB(NOW(), INTERVAL 48 HOUR)  ",array($CampId,$storeid));
		
			if($reserve_count1->fields['tot']  > 0)
			{
				$DetailHtml .= '<div class="strip" id="strip'.$CampId.$storeid.'" style="display:block"> '.$reserve_count1->fields['tot'].' People redeemed this offer in last 48 hours</div>';
			}
			$CampTitle =$Row['title'];
			$businessname_popup=$Row['location_name'];
			$number_of_use=$Row['number_of_use'];
			$new_customer=$Row['new_customer'];
			$address = $Row['address'];
			$city = $Row['city'];
			$state = $Row['state'];
			$zip = $Row['zip'];
			$country = $Row['country'];
			$redeem_rewards=$Row['redeem_rewards'];
			$referral_rewards=$Row['referral_rewards'];
			// start check for sharing limitaion if 0 then sharing point display 0
			if($Row['referral_rewards'] != "")
			{
				/*$Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$CampId;

				$RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location);*/
				$RS_max_no_location = $objDB->Conn->Execute( "SELECT max_no_sharing from campaigns WHERE id=?",array($CampId));

				/*$Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$CampId." and referred_customer_id<>0";
				$RS_shared = $objDB->Conn->Execute($Sql_shared);*/
				$RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?",array($CampId,0));

				if($RS_shared->RecordCount() >= $RS_max_no_location->fields['max_no_sharing'] )
				{
					$referral_rewards=0;
				}
				else
				{
					$referral_rewards=$Row['referral_rewards'];
				}
			}
			// end check for sharing limitaion if 0 then sharing point display 0
			
			$o_left = $Row['offers_left'];
			$expiration_date = date("Y-m-d g:i:s A", strtotime($Row['expiration_date'] ));
			if($Row['business_logo']!="")
			{
				$img_src=ASSETS_IMG."/m/campaign/block/".$Row['business_logo']; 
			}
			   
			else 
			{
				$img_src=ASSETS_IMG."/m/campaign/block/Merchant_Offer.png";
			}
			$deal_desc= $Row['deal_detail_description'];
			$visited = 0;
			if(!isset($_SESSION['customer_id']))
			{
				$c = 0;
			}
			else
			{
				$c = $_SESSION['customer_id'];
				/*$sql = "select * from customer_campaign_view where customer_id=".$_SESSION['customer_id']." and campaign_id =".$CampId;
				$RS = $objDB->Conn->Execute($sql);*/
				$RS = $objDB->Conn->Execute("select * from customer_campaign_view where customer_id=? and campaign_id =?",array($_SESSION['customer_id'],$CampId));

				if($RS->RecordCount() > 0)
				{
					$visited = 1;
				}
			}
			/*$barcode_sql = "select coupon_code from coupon_codes where customer_campaign_code =".$CampId." AND location_id=".$storeid." and customer_id=".$_SESSION['customer_id'];
            $barcode_rs = $objDB->Conn->Execute($barcode_sql);*/
			$barcode_rs = $objDB->Conn->Execute("select coupon_code from coupon_codes where customer_campaign_code =? AND location_id=? and customer_id=?",array($CampId,$storeid,$_SESSION['customer_id']));

			$campaign_tag=$Row['campaign_tag'];
			$vi  = $Row['cid']."-".$Row['locid']."-".$Row['number_of_use']."-".$Row['offers_left'];
			$popupurl=WEB_PATH."/popup_for_mymerchant.php?CampTitle=".urlencode($CampTitle)."&businessname=".urlencode($businessname_popup)."&number_of_use=".$number_of_use."&new_customer=".$new_customer."&address=".urlencode($address)."&city=".urlencode($city)."&state=".urlencode($state)."&country=".urlencode($country)."&zip=".$zip."&redeem_rewards=".$redeem_rewards."&referral_rewards=".$referral_rewards."&o_left=".$o_left."&expiration_date=".date("m/d/y g:i A", strtotime($expiration_date))."&img_src=".$img_src."&campid=".$CampId."&locationid=".$storeid."&deal_desc=".urlencode($deal_desc)."&is_reserve=1&br=".$barcode_rs->fields['coupon_code']."&cust_id=".$c."&decoded_cust_id=".base64_encode($c)."&webpath=".urlencode(WEB_PATH)."&is_mydeals=1&voucher_id=".$vi."&visited=".$visited."&campaign_tag=".$campaign_tag;
			//$popupurl="";
			$DetailHtml .= '<div title="Campaign Title" class="dealtitle" mypopupid="'.$popupurl.'" onClick="try1(this,'. $CampId .','.$storeid.')" data-fancybox-group="gallery">'.$CampTitle .'  </div>';
			
			$array_where_cat['id'] = $Row['category_id'];
			$RS_Cat = $objDB->Show("categories", $array_where_cat);
			$RS_Cat_image="<img class='kat_img' align='left' src='".ASSETS_IMG.$RS_Cat->fields['cat_image']."' title='".$RS_Cat->fields['cat_name']."' />";
			
			$where_x = array();
			$where_x['campaign_id'] = $CampId;
			$CodeDetails = $objDB->Show("activation_codes", $where_x);
			
			$array_where2['id'] = $storeid;
            $RSLocation2 = $objDB->Show("locations", $array_where2);
				 
			$barcodea = $_SESSION['customer_id'].substr($CodeDetails->fields['activation_code'],0,2).$CampId.substr($RSLocation2->fields['location_name'],0,2).$storeid;
				
			$RS_Cat_image.="<img title='Print' class='btn_print1' barcodea='".$barcodea."' cid='".$CampId."' lid='".$storeid."' align='left' style='margin:5px 0 0 10px;' src='".ASSETS_IMG."/c/icon-print-deal2-black.png' />";
			
			$viewbtn = '<a href="'.$Row['permalink'].'" class="view" target= "_parent">View</a>&nbsp';
            $viewbtn .= '<a href="javascript:void(0)" class="btn_unreserve" u_campid="'.$Row['cid'].'" u_locid="'.$Row['locid'].'" >Unreserve</a>&nbsp';
													$viewbtn .= '<a class="view-reserve" href="'.$Row['permalink'].'" style="display:none" target= "_parent">View And Reserve</a>&nbsp';
			$DetailHtml .= '<div class="btnview">'.$RS_Cat_image.$viewbtn .'  </div>';
			
			$DetailHtml.='</div>';
			$DetailHtml.='</div>';
			
			$DetailHtml.='</li>';
		
		}	
		}
		
		$DetailHtml.='</ul>';
		
		$DetailHtml.='<div class="clearfix"></div>';
		$DetailHtml.='<div class="navigation_center">';
		$DetailHtml.='<a id="'.$storeid.'prev1" class="prev1" href="#"></a>';
		$DetailHtml.='<a id="'.$storeid.'next1" class="next1" href="#"></a>';
		$DetailHtml.='</div>';											
		$DetailHtml.='</div>';
			
		$json_array["records"]= $records;
		$levels= trim($levels,",");
		$categories = trim($categories,",");
		$expiring = trim($expiring,",");
		$json_array['levels'] = $levels;
		$json_array['categories'] = $categories;
		$json_array['expiring'] = $expiring;
		$json_array['total_records'] = $count;
		$json_array['all_records'] = $count;
		$json_array["DetailHtml"]= $DetailHtml;
	}
	else
	{
		$json_array["records"]="";
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
		$json_array['is_profileset'] = 1;
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

function check_campaign_criteria($campaign_id , $location_id)
{
$url_perameter_2 = rawurlencode("@".$campaign_id."|".$location_id."@");
$objDB = new DB();
 
	/*$Sql_campaign = "SELECT is_walkin , new_customer, level ,number_of_use  from campaigns WHERE
				id=".$campaign_id ;
    
	$RS_campaign = $objDB->Conn->Execute( $Sql_campaign);*/
	$RS_campaign = $objDB->Conn->Execute( "SELECT is_walkin , new_customer, level ,number_of_use  from campaigns WHERE id=?",array($campaign_id) );

	//$sql_subscribe ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$location_id." and subscribed_status=1";
	
	
    
	/*$Sql_reserve = "SELECT * FROM customer_campaigns WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$campaign_id."' AND location_id =". $location_id;
	
	$RS_is_reserve = $objDB->Conn->Execute( $Sql_reserve);*/
	$RS_is_reserve = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?",array($_SESSION['customer_id'],$campaign_id,$location_id));

	/*$sql_redeem ="select * from coupon_redeem where coupon_id in
			( select id from coupon_codes where 
			customer_id=".$_SESSION['customer_id']." and location_id=". $location_id." and 
			customer_campaign_code =".$campaign_id .") ";*/
	$RS_is_redeem = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in
			( select id from coupon_codes where 
			customer_id=? and location_id=? and 
			customer_campaign_code =?) ",array($_SESSION['customer_id'],$location_id,$campaign_id));

	
	//$RS_is_redeem = $objDB->Conn->Execute( $sql_redeem);
	
	if($RS_is_redeem->RecordCount() == 0)
	{
	
			if($RS_is_reserve->RecordCount() == 0)
			{
	
				$RS_subscribe = $objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=? and subscribed_status=?",array($_SESSION['customer_id'],$location_id,1));
					$redirecting_page = "search-deal.php#".$url_perameter_2;
				
			}
			else
			{
	
				$redirecting_page = "my-deals.php#".$url_perameter_2;
			}
	}	
	else{
	
		if($RS_campaign->fields['number_of_use'] == 1)
		{
		
			$redirecting_page = "search-deal.php";
		}
		else
		{
		
			$redirecting_page = "my-deals.php#".$url_perameter_2;
		}
	}
	
	return $redirecting_page;
	    
}

/**
 * @uses display location detail
 * @param location_id
 * @used in pages :
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['display_locationdetail']))
{
	$array = $json_array = $where_clause = array();
	$where_clause['id'] = $_REQUEST['location_id'];
	$RS=$objDB->Show("locations", $where_clause);
	$Row = $RS->FetchRow();
	$where_clause1 = array();
	$where_clause1['id'] =$Row['created_by'];
	$RS1=$objDB->Show("merchant_user", $where_clause1);
	$Row1 = $RS1->FetchRow();
	$json_array['status'] = "true";
	$json_array['detail_display'] = $Row1['location_detail_display'];
	$json_array['menu_display'] = $Row1['menu_price_display'];	
	$json_array['detail_display_title'] = $Row1['location_detail_title'];
	$json_array['menu_display_title'] = $Row1['menu_price_title'];	
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses get location detail all from ajax
 * @param location_id
 * @used in pages :
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_location_details_all_from_ajax']))
{ 
	/*
	echo "<pre>";
	print_r($_REQUEST);
	echo "</pre>";
	exit();
	*/
	$json_array=array();
	
	$location_detail_html="";
	$location_images_html="";
	$location_menu_html="";
	$location_rating_html="";
	$location_review_html="";
	
	$all_html="";
	$all_html.='<div id="locationdetailinfo_'.$_REQUEST['location_id'].'" style="display:block" class="locationdetailinfo_popup">';			
	$all_html.='<div class="popup_business_css">'.$_REQUEST['business'].'</div>';
	$all_html.='<div class="popup_address_css">'.$_REQUEST['address'].'</div>';
	
	$all_html.='<div id="media-upload-header">';
    $all_html.='<ul id="sidemenu">';
	
	$arr_ld=file(WEB_PATH.'/process.php?display_locationdetail=yes&location_id='.$_REQUEST['location_id']);
	if(trim($arr_ld[0]) == "")
	{
		unset($arr_ld[0]);
		$arr_ld = array_values($arr_ld);
	}
	$json_ld = json_decode($arr_ld[0]);
	$detail_display = $json_ld->detail_display;
	$menu_display = $json_ld->menu_display;
	$detail_display_title = $json_ld->detail_display_title;
	$menu_display_title = $json_ld->menu_display_title;
	
	if($detail_display==1)
	{											
		$all_html.='<li id="tab-type" class="div_details"><a class="current">'.$detail_display_title.'</a></li>';
		$all_html.='<li id="tab-type" class="div_images"><a>'.$client_msg['location_detail']['label_More_Images'].'</a></span></li>';
	}
	else
	{
		$all_html.='<li id="tab-type" class="div_images"><a class="current">'.$client_msg['location_detail']['label_More_Images'].'</a></span></li>';
	}
	if($menu_display==1)
	{											
		$all_html.='<li id="tab-type" class="div_menus"><a >'.$menu_display_title.'</a></li>';
	}
	$all_html.='<li id="tab-type" class="div_ratings"><a>'.$client_msg['location_detail']['label_Ratings'].'</a></span></li>';
	$all_html.='<li id="tab-type" class="div_reviews"><a>'.$client_msg['location_detail']['label_Reviews'].'</a></span></li>';
	$all_html.="</ul>";
	$all_html.="</div>";
	// start location detail 
	
	
	
	/*$Sql = "SELECT id,dining,reservation,takeout,pricerange,ambience,attire,good_for,payment_method,parking,pet,wheelchair,wifi,
				has_tv,airconditioned,smoking,alcohol,noise_level,minimum_age,will_deliver,minimum_order,deliveryarea_from,deliveryarea_to,caters,services,amenities  
				FROM locations where id =".$_REQUEST['location_id'];
$RS = $objDB->Conn->Execute($Sql);*/
	
	$RS = $objDB->Conn->Execute("SELECT id,dining,reservation,takeout,pricerange,ambience,attire,good_for,payment_method,parking,pet,wheelchair,wifi,
				has_tv,airconditioned,smoking,alcohol,noise_level,minimum_age,will_deliver,minimum_order,deliveryarea_from,deliveryarea_to,caters,services,amenities  
				FROM locations where id =?",array($_REQUEST['location_id']));
				
	
	if($RS->RecordCount()>0)
	 {
			$count=0;		  
			
			$detail="false";
			
			while($Row = $RS->FetchRow())
			{
				$records[$count] = get_field_value($Row);
				
				$location_detail_html.='<ul >';
				if($Row['dining']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Dining : ';
					$location_detail_html.='<span>'.$Row['dining'].'</span></li>';
				}
				if($Row['reservation']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Takes Reservation : ';
					$location_detail_html.='<span>'.$Row['reservation'].'</span></li>';
				}
				if($Row['takeout']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Takeout : ';
					$location_detail_html.='<span>'.$Row['takeout'].'</span></li>';
				}
				if($Row['good_for']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Good For :';
					$location_detail_html.='<span>'.$Row['good_for'].'</span></li>';
				}
				
				//echo $Row['pricerange'];
				
				if($Row['pricerange']!="0" && $Row['pricerange']!="")
				{
					$detail="true";
					$val_text="";
					
					if($Row['pricerange']=="1")
					{
						//$val_text="$";
						$val_text="Inexpensive";
					}
					if($Row['pricerange']=="2")
					{		
						//$val_text="$$";
						$val_text="Moderate";
					}
					if($Row['pricerange']=="3")
					{
						//$val_text='$$$';
						$val_text="Expensive";
					}
					if($Row['pricerange']=="4")
					{
						//$val_text="$$$$";
						$val_text="Very Expensive";
					}
										
					$location_detail_html.='<li >Price Range :';
					$location_detail_html.='<span id="pricerange_text">'.$val_text.'</span></li>';
				}
				if($Row['parking']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Parking :';
					$location_detail_html.='<span>'.$Row['parking'].'</span></li>';
				}
				if($Row['wheelchair']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Wheelchair Accessible :';
					$location_detail_html.='<span>'.$Row['wheelchair'].'</span></li>';
				}
				if($Row['payment_method']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Payment Method :';
					$location_detail_html.='<span>'.$Row['payment_method'].'</span></li>';
				}
				if($Row['minimum_age']!="" && $Row['minimum_age']!="0")
				{
					$detail="true";	
					$location_detail_html.='<li>Minimum Age Restriction :';
					$location_detail_html.='<span>'.$Row['minimum_age'].' years to enter the location.</span></li>';
				}
				if($Row['pet']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Pet Allowed :';
					$location_detail_html.='<span>'.$Row['pet'].'</span></li>';
				}
				if($Row['ambience']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Ambience :';
					$location_detail_html.='<span>'.$Row['ambience'].'</span></li>';
				}		
				if($Row['attire']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Attire :';
					$location_detail_html.='<span>'.$Row['attire'].'</span></li>';
				}
				
				if($Row['noise_level']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Noise level :';
					$location_detail_html.='<span>'.$Row['noise_level'].'</span></li>';
				}
				
				
				
				
				if($Row['wifi']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Wifi :';
					$location_detail_html.='<span>'.$Row['wifi'].'</span></li>';
				}
				if($Row['has_tv']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Has TV :';
					$location_detail_html.='<span>'.$Row['has_tv'].'</span></li>';
				}
				if($Row['airconditioned']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Airconditioned :';
					$location_detail_html.='<span>'.$Row['airconditioned'].'</span></li>';
				}
				if($Row['smoking']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Smoking :';
					$location_detail_html.='<span>'.$Row['smoking'].'</span></li>';
				}
				if($Row['alcohol']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Alcohol :';
					$location_detail_html.='<span>'.$Row['alcohol'].'</span></li>';
				}
				
				
				if($Row['will_deliver']!="")
				{
					$detail="true";
						$location_detail_html.='<li>Will Deliver :';
						$location_detail_html.='<span>'.$Row['will_deliver'];
						if($Row['will_deliver']=="Yes")
						{										
							$m_o="";
							$d_a="";
							if($Row['minimum_order']!="" && $Row['minimum_order']!="0")
							{
								//$location_detail_html.=' ( Minimum Order : $'.$Row['minimum_order'].', Delivery Area : '.$Row['deliveryarea_from'].' to '.$Row['deliveryarea_to']." )";
								$m_o=' ( Minimum Order : $'.$Row['minimum_order'].', Delivery Area : '.$Row['deliveryarea_from'].' to '.$Row['deliveryarea_to']." )";
							}
							if($Row['deliveryarea_from']!="" && $Row['deliveryarea_to']!="" && $Row['deliveryarea_from']!="0" && $Row['deliveryarea_to']!="0")
							{
								//$location_detail_html.=' ( Minimum Order : $'.$Row['minimum_order'].', Delivery Area : '.$Row['deliveryarea_from'].' to '.$Row['deliveryarea_to']." )";
								$d_a=' ( Minimum Order : $'.$Row['minimum_order'].', Delivery Area : '.$Row['deliveryarea_from'].' to '.$Row['deliveryarea_to']." )";
							}
							if($m_o !="" && $d_a !="")
							{
								$locationdetailhtml.=' ( Minimum Order : $ '.$Row['minimum_order'];
								$locationdetailhtml.=', Delivery Area : '.$Row['deliveryarea_from'].' to '.$Row['deliveryarea_to'].')';
							}										
							else if($m_o !="" && $d_a =="")
							{
								$locationdetailhtml.=' ( Minimum Order : $ '.$Row['minimum_order'].')';
							}
							else if($m_o =="" && $d_a !="")
							{
								$locationdetailhtml.=' ( Delivery Area : '.$Row['deliveryarea_from'].' to '.$Row['deliveryarea_to'].')';
							}
						}
						$location_detail_html.='</span></li>';
				}
				if($Row['caters']!="")
				{
					$detail="true";
					$location_detail_html.='<li>Caters :';
					$location_detail_html.='<span>'.$Row['caters'].'</span></li>';
				}
				
				$location_detail_html.='</ul>';
				
				$location_detail_html.='<div class="services_amenities">';
				if($Row['services']!="")
				{
					$detail="true";
					$location_detail_html.='<div class="services">';
					$location_detail_html.='<h4>Services :</h4>';
					$services_arr = explode(",",$Row['services']);
					foreach($services_arr as $sa)
					{
						$location_detail_html.='<span>'.$sa.'</span>';
					}
					$location_detail_html.='</div>';
				}
				if($Row['amenities']!="")
				{
					$detail="true";
					$location_detail_html.='<div class="amenities">';
					$location_detail_html.='<h4>Amenities :</h4>';
					$amenities_arr = explode(",",$Row['amenities']);
					foreach($amenities_arr as $aa)
					{
						$location_detail_html.='<span>'.$aa.'</span>';
					}
					$location_detail_html.='</div>';
				}
				$location_detail_html.='</div>';
							
				if($detail=="false")
				{
					$location_detail_html.='<div style="padding:15px;font-weight:bold">'.$client_msg['location_detail']['Msg_no_locationdetail'].'</div>';
				}
					
				$count++;
			}

	 }
	 else
	 {

	 }
	 // end location detail 
	 
	 // start location image
	 
	 
	$photos_div_main="";
	/*$sql_lat_long_id="select * from locations where id=".$_REQUEST['location_id'];
	$location_lat_long_id=  $objDB->Conn->Execute($sql_lat_long_id);*/
	$location_lat_long_id=  $objDB->Conn->Execute("select * from locations where id=?",array($_REQUEST['location_id']));

	//$single_details = array('90837987f15783ce9ce4');
	
	$image_path_main=UPLOAD_IMG."/m/location/";
    
	//$endpoint = "https://maps.google.com/cbk?output=json&hl=en&ll=39.470611,-0.3899&radius=10&cb_client=maps_sv&v=4";
	
	$endpoint = "https://maps.google.com/cbk?output=json&hl=en&ll=".$location_lat_long_id->fields['latitude'].",".$location_lat_long_id->fields['longitude'];
	
	$handler = curl_init();
	curl_setopt($handler, CURLOPT_HEADER, 0);
	curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($handler, CURLOPT_URL, $endpoint);
	$data = curl_exec($handler);
	curl_close($handler);
	// if data value is an empty json document ('{}') , the panorama is not available for that point
	if ($data==='{}') 
	{
		//print "StreetView Panorama isn't available for the selected location";
	}
	else
	{
	
		if (file_exists($image_path_main.'street_'.$_REQUEST['location_id'].'.jpeg')) {
			
		}
		else
		{
			$street_main_image = file_get_contents('https://maps.googleapis.com/maps/api/streetview?size=400x310&location='.$location_lat_long_id->fields['latitude'].",".$location_lat_long_id->fields['longitude'].'&sensor=false&key=AIzaSyBsvIV_4NNaCz9d2tSS6EeW01wIj98lmFA'); 
			$fp  = fopen($image_path_main.'street_'.$_REQUEST['location_id'].'.jpeg', 'w+');
			fputs($fp, $street_main_image);
			
			$image = new SimpleImage();
			$image->load($image_path_main.'street_'.$_REQUEST['location_id'].'.jpeg');
			$image->resize(70,70);
			$image->save($image_path_main.'thumb/street_'.$_REQUEST['location_id'].'.jpeg');
		}
	}	
    $location_images_html.='<div style="" id="moreimages_'.$_REQUEST['location_id'].'">';
    /*$sql_more="Select * from location_images where location_id='". $_REQUEST['location_id']."' ORDER BY image_id";
	$RS_images = $objDB->execute_query($sql_more);*/
	$RS_images = $objDB->Conn->Execute("Select * from location_images where location_id=? ORDER BY image_id",array($_REQUEST['location_id']));
	
	$count_images=$RS_images->RecordCount();
	
    //Location More Photos 
                    
	$location_images_html='<div id="div_main_'.$_REQUEST['location_id'].'" style="width:100%;display:block">';
	
	/*$RS_images = $objDB->execute_query($sql_more);
	$count_images=$RS_images->RecordCount();*/
	//if($count_images > 0)
	//{ 
			$location_images_html.='<div style="" id="div_block_main_image1">';
		//$photos_div.='sharad';
		
				$if_i=1;
				   
						if ($data==='{}') 
						{
						}
						else
						{
							$location_images_html.='<img class="main_image_class_'.$_REQUEST['location_id'].'" id="main_location_street_'.$_REQUEST['location_id'].'" style="display:block" src="'.ASSETS_IMG.'/m/location/street_'.$_REQUEST['location_id'].'.jpeg?'.date("His").'" >';
						}
						while($Row_more = $RS_images->FetchRow()){
						$image_name=  explode(".",$Row_more['main_image']);
						
						if($if_i==1)
						{
							if ($data==='{}') 
							{
								$location_images_html.='<img class="main_image_class_'.$_REQUEST['location_id'].'" style="display:block" id="main_'.$image_name[0].'_'.$_REQUEST['location_id'].'" src="'.ASSETS_IMG.'/m/location/'.$Row_more['main_image'].'" class="displayimg"></img>';
							}
							else
							{	
								$location_images_html.='<img class="main_image_class_'.$_REQUEST['location_id'].'" style="display:none" id="main_'.$image_name[0].'_'.$_REQUEST['location_id'].'" src="'.ASSETS_IMG.'/m/location/'.$Row_more['main_image'].'" class="displayimg"></img>';
							}	
						}
					   else
					   {


							$location_images_html.='<img class="main_image_class_'.$_REQUEST['location_id'].'" style="display:none" id="main_'.$image_name[0].'_'.$_REQUEST['location_id'].'" src="'.ASSETS_IMG.'/m/location/'.$Row_more['main_image'].'" class="displayimg"></img>';
						}

						$if_i++;
					}
					
		
				 $location_images_html.='</div>';
                           // }
 //else {
   //  $photos_div.='<div class="noimageclass" style="padding:15px;font-weight:bold;">';
     
     //$photos_div.=$client_msg["location_detail"]["Msg_No_More_Location_Image"];
     //$photos_div.='</div>';
 //}
			  /*$sql="Select * from location_images where location_id='".$_REQUEST['location_id']."' ORDER BY image_id";
								  $RS_images = $objDB->execute_query($sql);*/
$RS_images = $objDB->Conn->Execute("Select * from location_images where location_id=? ORDER BY image_id",array($_REQUEST['location_id']));
								  $count_images=$RS_images->RecordCount();
								  //if($count_images > 0)
								 // {
		   $location_images_html.='<div id="div_thumb_image" style="">';
							$location_images_html.='<ul id="list_'.$_REQUEST['location_id'].'">';
																			  
								 
									  $if_thumb_i=1;
									 $my_thumb_cnt=0;
									   if ($data==='{}') 
										{
										}
										else
										{
											$my_thumb_cnt++;
											$location_images_html.='<li class="thumbimageclass thumb_'.$_REQUEST['location_id'].' current_more" id="thumb_location_street_'.$_REQUEST['location_id'].'" style=""><img style="" src="'.ASSETS_IMG.'/m/location/thumb/street_'.$_REQUEST['location_id'].'.jpeg?'.date("His").'" class="displayimg"></img></li>';
										}
										
										while($Row_more = $RS_images->FetchRow())
										{
											$image_name=  explode(".",$Row_more['main_image']);
											if($if_thumb_i==1)
											{
												if ($data==='{}') 
												{
													$location_images_html.='<li class="thumbimageclass thumb_'.$_REQUEST['location_id'].' current_more" id="thumb_'.$image_name[0].'_'.$_REQUEST['location_id'].'" style=""><img style="" src="'.ASSETS_IMG.'/m/location/thumb/'.$Row_more['main_image'].'" class="displayimg"></img></li>';
												}
												else
												{
													$location_images_html.='<li class="thumbimageclass thumb_'.$_REQUEST['location_id'].'" id="thumb_'.$image_name[0].'_'.$_REQUEST['location_id'].'" style=""><img style="" src="'.ASSETS_IMG.'/m/location/thumb/'.$Row_more['main_image'].'" class="displayimg"></img></li>';
												}	
											}
											else
											{  
												$location_images_html.='<li class="thumbimageclass thumb_'.$_REQUEST['location_id'].'" id="thumb_'.$image_name[0].'_'.$_REQUEST['location_id'].'" style=""><img style="" src="'.ASSETS_IMG.'/m/location/thumb/'.$Row_more['main_image'].'" class="displayimg"></img></li>';
											}
											$if_thumb_i++;
											$my_thumb_cnt++;
										}
									   
									 
										$location_images_html.='</ul>';
						$location_images_html.='</div>';
									   
						if($if_thumb_i!=1)
						{
							$location_images_html.='<div class="nextprevious">';
							$location_images_html.='<a href="javascript:void(0)" id="previous_more_'.$_REQUEST['location_id'].'" class="previous_more"></a>';
							$location_images_html.='<a href="javascript:void(0)" id="next_more_'.$_REQUEST['location_id'].'" class="next_more"></a>';
							$location_images_html.='</div>';
						}
	  //End  Location More Photos  

		$location_images_html.='</div>';
    
		if($my_thumb_cnt>0)
		{
			
		}
		else
		{
			$location_images_html='<div class="noimageclass" style="padding:15px;font-weight:bold;">';
			$location_images_html.=$client_msg["location_detail"]["Msg_No_More_Location_Image"];
			$location_images_html.='</div>';
		}
	 
	 // end location image
	 // start location menu
	 
				 $json_array = array();
				
								// create curl resource 
				$location_menu="";                
				
			/*$sql_venue_id="select * from locations where id=".$_REQUEST['location_id'];
			 $location_venue_id=  $objDB->Conn->Execute($sql_venue_id);*/
			$location_venue_id=  $objDB->Conn->Execute("select * from locations where id=?",array($_REQUEST['location_id']));

			 if($location_venue_id->fields['venue_id'] != "")
			 {
			 
			   $my_file = 'locu_files/locu_'.$location_venue_id->fields['venue_id'].'.txt';
			   

			   if (file_exists($my_file)) {
				   
					$file_data=file_get_contents($my_file);
					
					//$file_data_json = json_decode($file_data);
					$array_json = json_decode($file_data);
					
			   }
			   else
			   {
				   $handle = fopen($my_file, 'w');
				   $ch = curl_init(); 
								
					// set url 
					curl_setopt($ch, CURLOPT_URL, "https://api.locu.com/v1_0/venue/".$location_venue_id->fields['venue_id']."/?api_key=269fe167da30803613598800a3da6e0e590297ac"); 

					//return the transfer as a string 
				   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

					// $output contains the output string 
					$output = curl_exec($ch); 
				   file_put_contents($my_file,$output);
				   $array_json = json_decode($output);
				   
			   }
			
								 $first_step=$array_json->objects;
								 $second_step=$first_step[0]->menus;
							   
							 $location_menu.='<div style="" id="menu_'.$_REQUEST['location_id'].'">'; 				 
							  $location_menu.='<div style="" id="" class="main_menu_div">';
								  
							$location_menu.='<div class="inner withClose" id="overlayInnerDiv">';
								$location_menu.='<div class="menuContainer">';
								$mainmenucount=0;
								for($k=0;$k<count($second_step);$k++)
								{ 
									if($mainmenucount == 0)
									{
							
									  //$location_menu.='<div id="'.$k.$_REQUEST['location_id'].'" class="menuTitle mainmanuTitle" onclick="myFunction(this.id)">'.$second_step[$k]->menu_name.'</div>';
									}
									else
									{
									  //$location_menu.='<div id="'.$k.$_REQUEST['location_id'].'" class="menuTitle mainmanuTitle inactive" onclick="myFunction(this.id)">'.$second_step[$k]->menu_name.'</div>';  
									}
									$mainmenucount++;
							
								}
							   
								//get menu detail
								//$get_menu_array=$second_step[0]['menu_name'];
								//get section detail
			//echo $get_menu_array;
			//exit;
								$count=0;
					for($k=0;$k<count($second_step);$k++)
					{
					//$get_menu_sections_array= $second_step[$k]['sections'];
					$get_menu_sections_array= $second_step[$k]->sections;
								
								
							 
								
								$location_menu.='<div id="'.$k.$_REQUEST['location_id'].'" class="menuTitle mainmanuTitle" >'.$second_step[$k]->menu_name.'</div>';
								
								if($count == 0)
								{ 
								   $location_menu.='<div class="menuBody " id="smbody'.$k.$_REQUEST['location_id'].'">';
								 }

								else
								{
									  $location_menu.='<div class="menuBody " id="smbody'.$k.$_REQUEST['location_id'].'" >';
								 } 
								 
										$location_menu.='<div class="menuDesc"></div>';
								 

								for($i=0;$i<count($get_menu_sections_array);$i++)
								{
								   // echo $get_menu_sections_array[$i]->section_name."</br>";
									if($get_menu_sections_array[$i]->section_name != "")
									{
								   
									$location_menu.='<div class="menuSection">';
													 $location_menu.='<div class="menuTitle menuSectionTitle">'.$get_menu_sections_array[$i]->section_name.'</div>';
											   $location_menu.='</div>';

								   
									}
									 $get_subsections_detail=$get_menu_sections_array[$i]->subsections[0];
									 $get_subsections_detail_second=$get_subsections_detail->contents;
									
									 for($j=0;$j<count($get_subsections_detail_second);$j++)
									 { 
										 
										  if(isset($get_subsections_detail_second[$j]->name)  || isset($get_subsections_detail_second[$j]->description))
										 {
											$location_menu.='<div class="menuItem">';
										   if(isset($get_subsections_detail_second[$j]->price) )
										 {
										   $location_menu.='<div class="menuItemRHS">';
												 $location_menu.='<div class="menuPrice">';
													 $location_menu.='<span class="menuPriceDesc"></span>';
													 $location_menu.='<span class="menuPriceDesc"></span>';
													 $location_menu.='<span class="menuPriceNum">'.$get_subsections_detail_second[$j]->price.'</span>';
												 $location_menu.='</div>';
											 $location_menu.='</div>';

										 }    
										
										
										   $location_menu.='<div class="menuItemLHS">';
													if(isset($get_subsections_detail_second[$j]->name) )
												   { 
																	  $location_menu.='<div class="menuItemTitle">'.$get_subsections_detail_second[$j]->name.'</div>';


													}
												   if(isset($get_subsections_detail_second[$j]->description) )
												   { 
													 $location_menu.='<div class="menuItemDesc">'.$get_subsections_detail_second[$j]->description.'</div>';

													}
											   $location_menu.='</div>';
										   $location_menu.='</div>';
										 }
									   

									} 
									 
									 
								} 
								
								if($count == 0)
					{
				 
						   
					   $location_menu.='</div>';
					  }else
				   { 
					  $location_menu.='</div>';
					}

				   
				   $count++;
							  
					}        
							   $location_menu.='</div>';
						   $location_menu.='</div>';

								$location_menu.='</div>';
							 
								$location_menu.='</div>';
														    $location_menu.='<hr>';
								 $location_menu.='<a id="powered_by_locu" style="padding:10px;" target="_blank" href="https://locu.com"><img height="25" width="155" src="'.ASSETS_IMG.'/c/powerbylocu.png" alt="Powered by Locu"></a>'; 
								
								// close curl resource to free up system resources 
							 
					$location_menu_html=$location_menu;		
			 }
			 else
			 {
				  $location_menu.='<div style="" id="menu_'.$_REQUEST['location_id'].'">';			
				  $location_menu.="<div style='padding:15px;font-weight:bold'>".$client_msg['location_detail']['Msg_no_menu_price_list']."</div>";
				  $location_menu.="</div>";
				  
				  $location_menu_html=$location_menu;
				  
				  
			 }
	 
	 // end location menu
	 
	 // start location rating
	 
	 //$location_rating_html=file_get_contents(WEB_PATH."/locationchart.php?locid=".$_REQUEST['location_id']);
	 
	$location_rating_html.='<div class="location_popup_rating">';
	$location_rating_html.='<div class="rating_heading_top" style="display:block;">';
	$location_rating_html.='<div class="rating_heading_half_left"> Visitors Rating</div>';
	$location_rating_html.='<span class="rating_heading_seperator">|</span>';
	$location_rating_html.='<div class="rating_heading_half_right"> Rating Trend';
	$location_rating_html.='</div>';
	$location_rating_html.='</div>';
	$location_rating_html.='<div id="visitorrating_'.$_REQUEST['location_id'].'" class="visitorrating" style="display:block;">';
	$location_rating_html.='<div class="rting_strip">';
	$location_rating_html.='<div class="rating_heading" >Excellent : </div><div class="mainratingdiv_containere" id="">';
	$location_rating_html.='<div class="div_container excellent"></div>';
	$location_rating_html.='</div><span></span></div>';

	$location_rating_html.='<div class="rting_strip">';
	$location_rating_html.='<div class="rating_heading">Very Good : </div><div class="mainratingdiv_containere" id="">';
	$location_rating_html.='<div class="div_container verygood"></div>';
	$location_rating_html.='</div><span></span></div>';

	$location_rating_html.='<div class="rting_strip">';
	$location_rating_html.='<div class="rating_heading"> Good : </div><div class="mainratingdiv_containere" id="">';
	$location_rating_html.='<div class="div_container good"></div>';
	$location_rating_html.='</div><span></span></div>';

	$location_rating_html.='<div class="rting_strip">';
	$location_rating_html.='<div class="rating_heading"> Fair : </div><div class="mainratingdiv_containere" id="">';
	$location_rating_html.='<div class="div_container fair"></div>';
	$location_rating_html.='</div><span></span></div>';


	$location_rating_html.='<div class="rting_strip">';
	$location_rating_html.='<div class="rating_heading"> Poor : </div><div class="mainratingdiv_containere" id="">';
	$location_rating_html.='<div class="div_container poor"></div>';
	$location_rating_html.='</div><span></span></div>';
	$location_rating_html.='</div>';
	$location_rating_html.='<div id="container_'.$_REQUEST['location_id'].'" class="ratingtrend" style="display:block;"></div>';
	$location_rating_html.='</div>';
	$location_rating_html.="<div class='no_rating_found' style='padding:15px;font-weight:bold'>".$client_msg['location_detail']['table_no_rating_found']."</div>";

	 // end location rating
	 
	 // start location review
				
    //$Sql_lr = "select review from review_rating where review!='' and location_id=".$_REQUEST['location_id']." order by reviewed_datetime desc limit 5";
	/*$Sql_lr = "select re.* , u.firstname ,u.lastname , u.profile_pic , u.city , u.state from review_rating re inner join customer_user u on re.customer_id = u.id  where review!='' and location_id=".$_REQUEST['location_id']." order by reviewed_datetime desc limit 5";			 
	
	$RS_lr = $objDB->Conn->Execute($Sql_lr);*/
	$RS_lr = $objDB->Conn->SelectLimit("select re.* , u.firstname ,u.lastname , u.profile_pic , u.city , u.state from review_rating re inner join customer_user u on re.customer_id = u.id  where review!='' and location_id=? order by reviewed_datetime desc",5,0,array($_REQUEST['location_id']));
	if($RS_lr->RecordCount()>0)
	{
		$location_review_html.='<div class="">';
		$location_review_html .='<table width="100%" cellspacing="1" cellpadding="2" border="0" id="example_108" class="tableMerchant reviews_table dataTable" aria-describedby="example_108_info" style="width: 100%;">
	
			 <thead>';
		while($Row_lr = $RS_lr->FetchRow())
		{
		$location_review_html .= '<tr role="row"><th class="sorting_disabled" tabindex="0" rowspan="1" colspan="1" style="width: 970px;" aria-label=""></th></tr></thead><tbody role="alert" aria-live="polite" aria-relevant="all"><tr class="tablereview tabledeal odd">
		 <td class="td_review ">
		 <div class="delivery_main">
	<div class="fast_delivery">';
	
	 $location_review_html.='<div style="" class="user_detail">';
		
			$pos = strpos($Row_lr['profile_pic'], 'http');
			if($pos===false)
			{
				if($Row_lr['profile_pic'] != "")
			   {
					$location_review_html.='<div class="rev_usr_pic"><img src="'.ASSETS_IMG.'/c/usr_pic/'.$Row_lr['profile_pic'].'" /></div>';
			   }
			   else
			   {
					$location_review_html.='<div class="rev_usr_pic"><img src="'.ASSETS_IMG.'/c/default_small_user.jpg" /></div>';
			   }
			   
		
			}
			else
			{
		
				 $location_review_html.='<div class="rev_usr_pic"><img src="'.$Row_lr['profile_pic'].'" /></div>';
		
			}
		 $location_review_html.='<div class="rev_usr_name">'. $Row_lr['firstname'].' '.strtoupper(substr($Row_lr['lastname'],0,1)).' ('.ucwords($Row_lr['city']).','.$Row_lr['state'].') ';
									
				  $location_review_html.='</div>';
				  $location_review_html.='<div class="date">'.date('M j, Y | g:i A', strtotime($Row_lr['reviewed_datetime'])).'</div>'; 
				$location_review_html.='<div class="one">';
		  $rating_title ="Not Yet Rated";
            $class =  "full-gray";
           if( $Row_lr['rating'] < 0 && $Row_lr['rating']   < 1)
            {
                $class =  "orange-half";
				$rating_title = "Poor";
            }
            else if ($Row_lr['rating']  >= 1 && $Row_lr['rating']  <= 1.74) {
                  $class =  "orange-one";
				  $rating_title = "Poor";
            }
            else if ($Row_lr['rating']  >= 1.75 && $Row_lr['rating']  <= 2.24) {
                  $class =  "orange-two";
				  $rating_title = "Fair";
            }
            else if ($Row_lr['rating']  >= 2.25 && $Row_lr['rating']  <= 2.74) {
                  $class =  "orange-two_h";
				  $rating_title = "Good";
            }
            else if ($Row_lr['rating']  >= 2.75 && $Row_lr['rating'] <= 3.24) {
                  $class =  "orange-three";
				  $rating_title = "Good";
            }
            else if ($Row_lr['rating']  >= 3.25 && $Row_lr['rating']  <= 3.74) {
                  $class =  "orange-three_h";
				  $rating_title = "Very Good";
            }
            else if ($Row_lr['rating']  >= 3.75 && $Row_lr['rating']  <= 4.24) {
                  $class =  "orange-four";
				  $rating_title = "Very Good";
            }
            else if ($Row_lr['rating'] >= 4.25 && $Row_lr['rating'] <= 4.74) {
                  $class =  "orange-four_h";
				  $rating_title = "Excellent";
            }
            else if($Row_lr['rating']  >= 4.75) {
                  $class =  "orange";
				  $rating_title = "Excellent";
            }
           $location_review_html.='<div class="ratinn_box '.$class.'" >';
          
        
           $location_review_html.='</div>';
		   $location_review_html.='<div style="display: none;" class="cust_attr_tooltip">
									<div class="arrow_down"></div>
									<span id="star_tooltip">'.$rating_title.'</span>
			</div>
			</div></div>';
				$review = $Row_lr['review'];
				if(strlen(strip_tags($review))>400)
				{
				$location_review_html.='<div class="fast">
								<div id="review_'.$Row_lr['id'].'" style="height:75px;"> 
					'.substr(trim($review),0,300)."...".'<a class="review_more" id="review_more_"'. $Row_lr['id'].'" review_id="'.$Row_lr['id'].'" more="1" href="javascript:void(0)" style="padding:0px;">show more</a>
					</div>
					<div id="review_'.$Row_lr['id'].'_hidden" style="display:none;" >'.trim($review).'</div>
				</div>';
				}
				else
				{
					$location_review_html.='<div class="fast"><div id="review_'.$Row_lr['id'].'"> 
					'.trim($review).'
					</div></div>';
				}
      						
				$location_review_html.='</div>';
				  
				  $location_review_html.='</td></tr>';
		   }
						   
			$location_review_html.='</tbody></table>'	;
			//$location_review_html.="<div class='div_review'>".$Row_lr['review']."</div>";				

		$arr_loc = array();
		$arr_loc['id'] = $_REQUEST['location_id'];
		$RS_loc = $objDB->Show("locations",$arr_loc);
		$location_review_html.="<div class='div_show_more_reviews'><a target='_new' href='".$RS_loc->fields['location_permalink']."#reviews'>Show more reviews</a></div>";
		$location_review_html.='</div>';
	}
	else
	{
		$location_review_html.="<div style='padding:15px;font-weight:bold'>".$client_msg['location_detail']['table_no_records_found']."</div>";
	}
	
	 // end location review
	 
	if($detail_display==1)
	{
		$all_html.='<div id="div_details" class="tabs" style="display:block;">'.$location_detail_html.'</div>';
		$all_html.='<div id="div_images" class="tabs" style="display:none;">'.$location_images_html.'</div>';
	}
	else
	{
		$all_html.='<div id="div_images" class="tabs" style="display:block;">'.$location_images_html.'</div>';
	}
	if($menu_display==1)
	{
		$all_html.='<div id="div_menus" class="tabs" style="display:none;">'.$location_menu_html.'</div>';
	}

	$all_html.='<div id="div_ratings" class="tabs" style="display:none;">'.$location_rating_html.'</div>';
	$all_html.='<div id="div_reviews" class="tabs" style="display:none;">'.$location_review_html.'</div>';
	
	$all_html.="</div>";
	
	$json_array['status'] = "true";
	$json_array['all_html']=$all_html;
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/*** rexently viewed campaign */
/**
 * @uses get recently viewd campaign
 * @param camapigns,locations
 * @used in pages :my-deals.php,mymerchants.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['get_recently_viewed_campaign']))
{

	$json_array = array();
	$campaign_list = explode("-",$_REQUEST['camapigns']);
	$location_list = explode("-",$_REQUEST['locations']);
	$querystring = "";
	$querystring = "select c.id,c.title,c.business_logo,l.id ,business_logo ,cl.permalink from campaigns c,locations l ,campaign_location cl
 where cl.location_id=l.id and cl.campaign_id=c.id
 and (";
 
	for($i=0;$i<count($campaign_list);$i++)
	{
		$querystring .= " (c.id=".$campaign_list[$i]." and l.id=".$location_list[$i].") ";
		if($i == (count($campaign_list) -1))
		{
		}
		else{
			$querystring .= " or ";
		}
		
	}
	$querystring .= " ) order by  ";
	for($i=(count($campaign_list)-1);$i>=0;$i--)
	{
		
		$querystring .= " (c.id=".$campaign_list[$i]." and l.id=".$location_list[$i].") ";
		if($i == 0)
		{
		}
		else{
			$querystring .= " , ";
		}
		
	}
	$querystring .=  " asc";
	
	$count =0;
	$br_rs =  $objDB->Conn->Execute($querystring);
		if($br_rs->RecordCount() > 0)
		{
			while($Row = $br_rs->FetchRow())
			{ 
				$records[$count] = get_field_value($Row);
				$count++;
			}					
			
		}
	$json_array['status'] = "true";
	$json_array['total_records'] = $br_rs->RecordCount();
	$json_array['records'] = $records;
	$json = json_encode($json_array);
	echo $json;
	exit;
	
}

/**
 * @uses increment share counter
 * @param refferal_location_id
 * @used in pages :campaign.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btn_increment_share_counter']))
{
	$array_loc = array();
	$array_loc['id'] = $_REQUEST['refferal_location_id'];
	$RS_location = $objDB->Show("locations",$array_loc);
	$time_zone=$RS_location->fields['timezone_name'];
	//date_default_timezone_set($time_zone);
	$timestamp=$_REQUEST['timestamp'];
	$share_counter = array();
	$share_counter['customer_id'] = $_SESSION['customer_id'];
	$share_counter['campaign_id'] = $_REQUEST['reffer_campaign_id'];
	$share_counter['location_id'] = $_REQUEST['refferal_location_id'];
	$share_counter['campaign_share_domain'] = $_REQUEST['domain'];
	$share_counter['campaign_share_medium'] = $_REQUEST['medium'];
	$share_counter['timestamp'] = $timestamp;
	$id = $objDB->Insert($share_counter, "share_counter");	
}

/**
 * @uses captcha code check cr
 * @param code
 * @used in pages :contact-us.php,register.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['captchacode_check_c_r']))
{
	if(strtolower($_REQUEST['code']) == strtolower($_SESSION['random_number_c_r']))
	{
		
				
		echo 1;// submitted 
		
	}
	else
	{
		echo 0; // invalid code
	}
}

/**
 * @uses unsubscribe mail
 * @param code
 * @used in pages :frequent-mail-by-admin.php(cron),reserve-campaign-schedular.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnunsubscribeemail']))
{
	$json_array = $array_values = $where_clause1 = array();
	
	if($_REQUEST['tag']=="new")
	{
		$array_values['campaign_email'] = 0;
	}
	else
	{
		$array_values['subscribe_merchant_new_campaign'] = 0;
		$array_values['subscribe_merchant_reserve_campaign'] = 0;
	}
	$array_where['customer_id'] = $_REQUEST['id'];
	$objDB->Update($array_values,"customer_email_settings", $array_where);
	
	header("Location:email_unsubscribed.php?id=".$_REQUEST['id']);
	
}
/**
 * @uses unsubscribe customer
 * @param id
 * @used in pages :
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnunsubscribecustomer']))
{
	$json_array = $array_values = $where_clause1 = array();
	
	$array_values['opt_in'] = "0";
	$array_where['id'] = $_REQUEST['id'];
	$objDB->Update($array_values,"customer_user", $array_where);
	
	header("Location:unsubscribe.php?id=".$_REQUEST['id']);
	
}

/**
 * @uses get search deal loaction sp
 * @param id
 * @used in pages :
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetSearchDealLocations_sp']))
{

$current_day=date('D');
                $current_time=date('g:i A');
				echo $current_day."===".$current_time;
//$RS = $objDB->Conn->Execute("call search_deal_script(2500,43.653226,-79.3831843,0)");
//$rs = mysql_query("call search_deal_script(2500,43.653226,-79.3831843,0)");
$mysqli = new mysqli(DATABASE_HOST,DATABASE_USER,DATABASE_PASSWORD);
if(!$mysqli) die('Could not connect: ' . mysqli_error());
mysqli_select_db($mysqli, DATABASE_NAME);
$result = $mysqli->query("call search_deal_script(2500,43.653226,-79.3831843,0)");
if(!$result) die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
//$stmt = $mysqli->prepare("call search_deal_script(2500,43.653226,-79.3831843,0)");
$data = $result->fetch_assoc();
print_r($data);
//$result->free();
while ($mysqli->next_result()) {
echo "<br/>free each result.<br/>";
$result123 = $mysqli->use_result();
//if ($result instanceof mysqli_result) {
$data = $result123->fetch_assoc();
print_r($data);
 while($row = mysqli_fetch_array($result123, MYSQLI_ASSOC))
    {
        echo ($row['Uid'])."\n";
  print_r($row['Uemail']);
    }
$result123->free();
//}
}
exit();
//run statement
//$stmt->execute();

$recordset_Counter = 0;
if($result->num_rows > 0) 
{
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		  print_r($row);
    }
}
$result->mysqli_next_result();

exit();
// now get the @total var
$rs2 = mysql_query("select @total;");
$total = mysql_fetch_assoc($rs2);
print_r($total);

exit();
}

/**
 * @uses get search deal locations
 * @param category_id
 * @used in pages :my-deals.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetSearchDealLocations']))
{
	$json_array = array();
	$records = array();
	$records_all=array();
	$json_array1 = array();
	$category_id=$_REQUEST['category_id'];
	
	$dismile= 50;
	$date_f = date("Y-m-d H:i:s");
	$mlatitude=$_REQUEST['mlatitude'];
	$mlongitude=$_REQUEST['mlongitude'];
	if(isset($_REQUEST['dismile']))
	{
	$dismile= 50;
		$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;	
		//$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
	}
	$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0"; 
	$sel_expr = "  group_concat(CAST(time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'-06:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 AS CHAR(7))separator ',') as limelefttoexpire";
	
	$cust_where ="";

	$cat_str = "";
	if($_REQUEST['customer_id']!="")
	{
	$customer_id = $_REQUEST['customer_id'];
	$get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=".$customer_id." and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = ".$customer_id."
	and location_id=cl.location_id) total_reserved,";
		
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		//                $cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>"" and  country <>""  and id='.$customer_id;
		//                  $RS_cust_data=$objDB->Conn->Execute($cust_sql);
		//                   $is_profileset =  $RS_cust_data->RecordCount();
		//                   if($is_profileset == 0)
		//                   {
		//                       $json_array = array();
		//                        $json_array['status'] = "false";
		//        		  $json_array['is_profileset'] = 0;
		//                          $json = json_encode($json_array);
		//                        echo $json;
		//                        exit();
		//                   }

		// 03-10-2013 dist list deal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013	

		// 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
		// 05-10-2013	
	}
	else
	{
		$cust_where = " and c.level=1 ";
	}
	if(isset($_REQUEST['category_id']))
	{
		if($_REQUEST['category_id']==0)
		{
			$cat_str = "";
		}
		else
		{
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		}
	}
	
	/*
	$limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
    */
	
	$get_dat="";
	
	$limit_data = "SELECT l.id location_id ,
	group_concat(CAST(c.is_new AS CHAR(7)) separator ',') all_new_campaigns ,
	group_concat(CAST(cl.campaign_type AS CHAR(7)) separator ',') all_campaign_level 
	, group_concat(CAST(c.category_id AS CHAR(7)) separator ',') all_categories
	, group_concat(CAST(c.discount AS CHAR(7)) separator ',') all_discount ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country
	,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating ,".$sel_expr."
	,l.categories,l.is_open,".$get_dat." round((((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance ,l.location_permalink, 
	count(*) total_deals,mu.business  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." group by cl.location_id ORDER BY distance,c.expiration_date";
    
	
	//
	//and (
	//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
	//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
	//echo $limit_data;
	//exit;
	if($_REQUEST['customer_id'] != 0)
	{
		$customer_id = $_REQUEST['customer_id'];
	}
	else
	{
		$customer_id = 0;
	}
	$calculatedmiles = $dismile * $dismile;
	//echo "search_deal_script_web(".$calculatedmiles.",".$mlatitude.",".$mlongitude.",".$customer_id.",0)";
	$RS_limit_data=$objDB->Conn->Execute("call search_deal_script_web($calculatedmiles,$mlatitude,$mlongitude,$customer_id,0,'".CURR_TIMEZONE."')");
	
	 //$objDB->Conn->Close( );
	 //$objDB = new DB();
	
	//$RS_limit_data=$objDB->Conn->Execute($limit_data);
	if($RS_limit_data->RecordCount()>0)
	{
		$json_array['is_profileset'] = 1;          
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS_limit_data->RecordCount();
		$json_array['all_records'] = $RS_limit_data->RecordCount();
		$count=0;
		$arr_main_merchant_arr = array();
		$arr_main_location_arr = array();
		$all_mercahnts = array();
		while($Row = $RS_limit_data->FetchRow())
		{
				$temp_merchant_arr = array();
				
				// remove indefined index error
				if(isset($arr_main_merchant_arr[$Row['merchant']]))
				{
					
				}
				else
				{
					$arr_main_merchant_arr[$Row['merchant']]="";
				}
				// remove indefined index error
				
				if($arr_main_merchant_arr[$Row['merchant']] != 0 )
				{
					$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
				}
				if( !in_array($Row['merchant'],$all_mercahnts))
				{
					array_push($all_mercahnts,$Row['merchant']);
				}
				$arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
		
				array_push($temp_merchant_arr,get_field_value($Row));
				$arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
		
		
				$records[$count] = get_field_value($Row);
				$count++;
		}
		
		$json = json_encode($arr_main_merchant_arr);
		$id=0;
		$final_array = array();
		$max_counter = 0;
	
	//select business_tags from merchant_users where id= ".$all_mercahnts[$k]]."
	$business_tag_records = array();
	$count_business_tag =0;
	for($k =0 ;$k<count($all_mercahnts);$k++)
	{
		$max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
		if($max_counter<= $max_counter1)
		{
			$max_counter = $max_counter1;
		}

	}
	$final_array = array();
	
	for($j=0;$j<$max_counter;$j++)
	{
		for($y=0;$y<count($all_mercahnts);$y++)
		{
			// remove indefined index error
			if(isset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]))
			{
				
			}
			else
			{
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]="";
			}
			// remove indefined index error
				
			if($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "")
			{
				// start location hour code
				
				// start distance
					$location_latitude=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["latitude"];
					$location_longitude=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["longitude"];
					$deal_distance = $objJSON->distance($mlatitude, $mlongitude, $location_latitude, $location_longitude, "M");
					//$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["distance"]=$deal_distance;
					// end distance
				$location_id=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];
				
				$time_zone=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone_name"];
				
				date_default_timezone_set($time_zone);
                $current_day=date('D');
                $current_time=date('g:i A');
				$location_time="";
					$start_time = "";
					$end_time="";
					$status_time="";
					
							$start_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"];
							$end_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];
							$location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"]." - ";
							$location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];
							
					
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==1)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]=
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Open";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==0)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Close";
				}
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"]=$location_time;
				// end location hour code
				
				
				// start business name
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"]!="")
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"]=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"];
				}
				
				// end business name
				
				// start pricerange
				
				$val="";
				$val_text="";
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==1)
				{
					$val_text="Inexpensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==2)
				{		
					$val_text="Moderate";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==3)
				{
					$val_text="Expensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==4)
				{
					$val_text="Very Expensive";
				}
				else
				{
					$val_text="";
				}
				
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"]=$val_text;
					
				// end pricerange
				
				
				// start campaign array
				
				$count=0;
				$campaign_records=array();
				
				$locationwise_campaign_array = array();
				$count=0;
					$campaignlist = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_id"];
					$cmapignlist_array = explode(",",$campaignlist);
					for($cnti=0;$cnti<count($cmapignlist_array);$cnti++)
					{
							$campaign_array  = array();
							$campaign_array['id'] = $cmapignlist_array[$cnti];
							array_push($locationwise_campaign_array,$campaign_array);
					}							
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"]= $locationwise_campaign_array;
				// end campaign array
				
				// start location category
				
				// start location category
					if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']!="")
					{
						$count=0;
						$catgory_array = array();
						$innercategory_array['id'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'];
						$innercategory_array['cat_name'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["locationcategories"];
						array_push($catgory_array,$innercategory_array);
							$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=$catgory_array;
					}
					else
					{
							$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=array();
					}
				
				// end location category
				
				array_push($final_array,$arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
				
			}
			
		} 
		
	} 
	$json_array['status'] = "true";
	
		$json_array["records"]= $final_array;
		
		$json_array['business_tags_records'] = $business_tag_records;
		$json_array['total_records'] = count($json_array['records']);
		$json_array['marker_total_records'] = count($json_array['records']);
	}
	else
	{
		$json_array['all_records'] = 0;
		$json_array["records"]="";
		$json_array['business_tags_records'] = "";
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
		$json_array['is_profileset'] = 1;
		$json_array["marker_records"]= "";
		$json_array['marker_total_records'] = 0;
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}
/**
 * @uses get saved offers wsp
 * @param category_id
 * @used in pages :
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['btnGetSavedOffers_wsp']))
{
	$json_array = array();
	$records = array();
	$records_all=array();
	$json_array1 = array();
	$category_id=$_REQUEST['category_id'];
	$dismile=$_REQUEST['dismile'];
	//$dismile= 50;
	$date_f = date("Y-m-d H:i:s");
	$mlatitude=$_REQUEST['mlatitude'];
	$mlongitude=$_REQUEST['mlongitude'];
	$id = $_REQUEST['customer_id'];
	$miles=$_REQUEST['dismile'];
	if(isset($_REQUEST['dismile']))
	{
		$dismile= $_REQUEST['dismile'];
		$Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$miles*$miles ;	
		//$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
	}
	$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
	$sel_expr = "  group_concat(CAST(time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 AS CHAR(7))separator ',') as limelefttoexpire";
	$cust_where ="";

	$cat_str = "";
	if($_REQUEST['customer_id']!="")
	{
	$customer_id = $_REQUEST['customer_id'];
	$get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=".$customer_id." and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = ".$customer_id."
	and location_id=cl.location_id) total_reserved,";
		
		//$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
		// 12-8-2013
		//                $cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>"" and  country <>""  and id='.$customer_id;
		//                  $RS_cust_data=$objDB->Conn->Execute($cust_sql);
		//                   $is_profileset =  $RS_cust_data->RecordCount();
		//                   if($is_profileset == 0)
		//                   {
		//                       $json_array = array();
		//                        $json_array['status'] = "false";
		//        		  $json_array['is_profileset'] = 0;
		//                          $json = json_encode($json_array);
		//                        echo $json;
		//                        exit();
		//                   }

		// 03-10-2013 dist list deal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) ) )";
		// 03-10-2013	

		// 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
		$cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
		// 05-10-2013	
	}
	else
	{
		$cust_where = " and c.level=1 ";
	}
	if(isset($_REQUEST['category_id']))
	{
		if($_REQUEST['category_id']==0)
		{
			$cat_str = "";
		}
		else
		{
			$cat_str = " and c.category_id = ".$_REQUEST['category_id']." and c.category_id in(select cat.id from categories cat where cat.active=1) ";
		}
	}
	
	/*
	$limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
    */
	
	
	/*
	l.id location_id , group_concat(CAST(c.category_id AS CHAR(7)) separator ',') all_categories ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country
	,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating
	,l.categories,l.is_open,".$get_dat." round((((acos(sin((".$mlatitude."*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance ,l.location_permalink,
	count(*) total_deals,mu.business
	*/
	/*$limit_data = "SELECT c.id cid,l.id locid ,
	group_concat(CAST(c.is_new AS CHAR(7)) separator ',') all_new_campaigns ,
	group_concat(CAST(cl.campaign_type AS CHAR(7)) separator ',') all_campaign_level 
	, group_concat(CAST(c.discount AS CHAR(7)) separator ',') all_discount 
	,group_concat(CAST(c.category_id AS CHAR(7)) separator ',') all_categories , l.id location_id ,l.created_by merchant,l.location_name,l.address,
	l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,".$sel_expr." ,
	l.longitude,l.avarage_rating,l.categories,l.is_open,
                round((((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance ,l.location_permalink
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." group by cl.location_id ORDER BY distance,c.expiration_date";
	
	//
	//and (
	//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
	//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
	
	$RS_limit_data=$objDB->Conn->Execute($limit_data);*/
	$RS_limit_data=$objDB->Conn->Execute("SELECT c.id cid,l.id locid ,
	group_concat(CAST(c.is_new AS CHAR(7)) separator ',') all_new_campaigns ,
	group_concat(CAST(cl.campaign_type AS CHAR(7)) separator ',') all_campaign_level 
	, group_concat(CAST(c.discount AS CHAR(7)) separator ',') all_discount 
	,group_concat(CAST(c.category_id AS CHAR(7)) separator ',') all_categories , l.id location_id ,l.created_by merchant,l.location_name,l.address,
	l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,".$sel_expr." ,
	l.longitude,l.avarage_rating,l.categories,l.is_open,
                round((((acos(sin((".$mlatitude."*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance ,l.location_permalink
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status=?)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." group by cl.location_id ORDER BY distance,c.expiration_date",array(1,$id,1));

	if($RS_limit_data->RecordCount()>0)
	{
		$json_array['is_profileset'] = 1;          
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS_limit_data->RecordCount();
		$json_array['all_records'] = $RS_limit_data->RecordCount();
		$count=0;
		$arr_main_merchant_arr = array();
		$arr_main_location_arr = array();
		$all_mercahnts = array();
		while($Row = $RS_limit_data->FetchRow())
		{
			$temp_merchant_arr = array();
		//	echo "<br/>".count($arr_main_merchant_arr[$Row['merchant']])."===<br/>";
	//	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
	if($arr_main_merchant_arr[$Row['merchant']] != 0 )
	{
	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
	}
	if( !in_array($Row['merchant'],$all_mercahnts))
	{
		array_push($all_mercahnts,$Row['merchant']);
	}
		$arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
		
		array_push($temp_merchant_arr,get_field_value($Row));
		 $arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
		 
		
			$records[$count] = get_field_value($Row);
				
			$count++;
		}
		
		$json = json_encode($arr_main_merchant_arr);
		$id=0;
		$final_array = array();
		$max_counter = 0;
		
	for($k =0 ;$k<count($all_mercahnts);$k++)
	{
		/*$query = "select id,business_tags from merchant_user where id= ".$all_mercahnts[$k];
		
		$RS_business_tag = $objDB->Conn->Execute($query );*/
		$RS_business_tag = $objDB->Conn->Execute("select id,business_tags from merchant_user where id=?",array($all_mercahnts[$k]));
		while($Row_business_tag = $RS_business_tag->FetchRow())
					{		
						$business_tag_records[$count_business_tag] = get_field_value($Row_business_tag);				
						$count_business_tag++;
					}
		$max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
		if($max_counter<= $max_counter1)
		{
			$max_counter = $max_counter1;
		}

	}
	$final_array = array();
	for($j=0;$j<$max_counter;$j++)
	{
	
		for($y=0;$y<count($all_mercahnts);$y++)
		{
			if($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "")
			{
				// start location hour code
				
				$location_id=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];
				
				$time_zone=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"];
				date_default_timezone_set($time_zone);
                $current_day=date('D');
                $current_time=date('g:i A');
				/*$sql="select * from location_hours where location_id=".$location_id." and day='".strtolower($current_day)."'";
				$RS_hours_data = $objDB->execute_query($sql);*/
				$RS_hours_data = $objDB->Conn->Execute("select * from location_hours where location_id=? and day=?",array($location_id,strtolower($current_day)));

				$location_time="";
				$start_time = "";
				$end_time="";
				$status_time="";
				if($RS_hours_data->RecordCount()>0)
				{
					while($Row_data = $RS_hours_data->FetchRow())
					{
						$start_time = $Row_data['start_time'];
						$end_time=$Row_data['end_time'];
						$location_time.=$Row_data['start_time']." - ";
						$location_time.=$Row_data['end_time'];
					}
				}	
				$st_time    =   strtotime($start_time);
				$end_time   =   strtotime($end_time);
				$cur_time   =   strtotime($current_time);
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==1)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Open";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"]==0)
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"]="Currently Close";
				}
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"]=$location_time;
				// end location hour code
				
				
				// start business name
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"]!="")
				{
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"]=$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"];
				}
				
				// end business name
				
				// start pricerange
				
				$val="";
				$val_text="";
				
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==1)
				{
					$val_text="Inexpensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==2)
				{		
					$val_text="Moderate";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==3)
				{
					$val_text="Expensive";
				}
				else if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"]==4)
				{
					$val_text="Very Expensive";
				}
				else
				{
					$val_text="";
				}
				
				$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"]=$val_text;
					
				// end pricerange
				
				
				
				
				// start location category
				
				if($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']!="")
				{
					$count=0;
					$cat_records=array();
					
					/*$cat_sql = "SELECT * from category_level where id in (".$arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'].")";
	   
					$RS_cat_data=$objDB->Conn->Execute($cat_sql);*/
					$RS_cat_data=$objDB->Conn->Execute("SELECT * from category_level where id in (?)",array($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']));
					
					if($RS_cat_data->RecordCount()>0)
					{
						while($Row_cat = $RS_cat_data->FetchRow())
						{		
							$cat_records[$count] = get_field_value($Row_cat);				
							$count++;
						}
						$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=$cat_records;
					}
				}
				else
				{
						$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"]=array();
				}
				
				// end location category
				
				// start campaign array
				
				$count=0;
				$campaign_records=array();
				$t_l_estring = "";
				$campaign_sql = "SELECT c.id,c.title,c.category_id,cl.offers_left FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
					WHERE   l.active = 1  ".$cat_str  ."  ".$date_wh." and ".$Where." and l.id=".$location_id." ORDER BY c.expiration_date";
				$sel_expr = "  time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 as limelefttoexpire";
				$campaign_sql = "SELECT c.id,c.title,c.category_id,cl.offers_left,c.number_of_use ,cl.campaign_type , c.is_new ,
time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 limelefttoexpire				
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.id=".$location_id." and l.active = 1 and c.id  in ( select campaign_id from customer_campaigns where customer_id =". $_REQUEST['customer_id']." and location_id=cl.location_id and activation_status=1)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." ORDER BY c.expiration_date";
	
                                
                                $levels = "";
                                $categories = "";
                                $expiring = "";
                                $new_camp = "";
				
				//$RS_campaign_data=$objDB->Conn->Execute($campaign_sql);
				$RS_campaign_data=$objDB->Conn->Execute("SELECT c.id,c.title,c.category_id,cl.offers_left,c.number_of_use ,cl.campaign_type , c.is_new ,
time_to_sec(timediff(c.expiration_date, CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) )) / 3600 limelefttoexpire				
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.id=".$location_id." and l.active = ? and c.id  in ( select campaign_id from customer_campaigns where customer_id =? and location_id=cl.location_id and activation_status=?)
  ".$cat_str  ."  ".$date_wh." and ".$Where ." ORDER BY c.expiration_date",array(1,$_REQUEST['customer_id'],1));
				
				if($RS_campaign_data->RecordCount()>0)
				{
					while($Row_campaign = $RS_campaign_data->FetchRow())
					{
						
						$array_where_camp=array();
						$array_where_camp['campaign_id'] = $Row_campaign['id'];
						$array_where_camp['customer_id'] = $_REQUEST['customer_id'];
						$array_where_camp['referred_customer_id'] = 0;
						$array_where_camp['location_id'] = $location_id;
						$RS_camp = $objDB->Show("reward_user", $array_where_camp);
						
						$array_where_camp1=array();
						$array_where_camp1['campaign_id'] = $Row_campaign['id'];
						$array_where_camp1['location_id'] = $location_id;
						$campLoc = $objDB->Show("campaign_location", $array_where_camp1);
						
						if($RS_camp->RecordCount()>0 && $Row_campaign['number_of_use']=="1")
						{
							//echo "1 ".$Row_campaign['id'].",".$location_id;
						} 
						else if ($RS_camp->RecordCount()>0 && ($Row_campaign['number_of_use']=="2" || $Row_campaign['number_of_use']=="3" ) && $campLoc->fields['offers_left']==0) 
						{
							//echo "2 ".$Row_campaign['id'].",".$location_id;
						}
						else
						{
							//echo "else";
							$campaign_records[$count] = get_field_value($Row_campaign);				
							$count++;
                                                        $ex_array = $Row_campaign['limelefttoexpire'];
								
									  if($ex_array <= 24)
									  {
										$f_str = 1;
										}else
										{
											$f_str = 0;
										}
										$t_l_estring = $t_l_estring.$f_str.",";
                                                                                
                                                         $levels .= $Row_campaign['campaign_type'].",";
								  $categories .= $Row_campaign['category_id'].",";
								  $expiring .= $t_l_estring.",";
                                                                  $new_camp .= $Row_campaign['is_new'].",";
						}		
					}
                                        $levels= trim($levels,",");
                                        $categories = trim($categories,",");
                                        $expiring = trim($expiring,",");
                                        $new_camp = trim($new_camp,",");
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['c_levels'] = $levels;
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['c_categories'] = $categories;
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['c_expiring'] = $expiring;
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['c_new_campaigns'] = $new_camp;
					$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"]=$campaign_records;
				}
				
				// end campaign array
				
				if($count>0)
				{
					array_push($final_array,$arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
				}
				
			}
			
		} 
		
	} 
	$json_array['status'] = "true";
	
$json = json_encode($final_array);
		
		$json_array["records"]= $final_array;
		
		$json_array['business_tags_records'] = $business_tag_records;
		$json_array['total_records'] = count($json_array['records']);
		$json_array['all_records'] =  count($json_array['records']);
		$json_array['marker_total_records'] = count($json_array['records']);
	}
	else
	{
		$json_array['business_tags_records'] = "";
		$json_array['all_records'] = 0;
		$json_array["records"]="";
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
		$json_array['is_profileset'] = 1;
		$json_array["marker_records"]= "";
		$json_array['marker_total_records'] = 0;
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/**
 * @uses load gift card deatil
 * @param giftcardid
 * @used in pages :shop-redeem.php
 * @author Sangeeta 
 * @return type json array
 */
if(isset($_REQUEST['load_giftcard_detail']))
{
	/*$sql = "select * from giftcards where id=".$_REQUEST['giftcardid'];
	$RS_giftcard_data=$objDB->Conn->Execute($sql);*/
	$RS_giftcard_data=$objDB->Conn->Execute("select * from giftcards where id=?",array($_REQUEST['giftcardid']));

	
	if($RS_giftcard_data->fields['is_merchant'] == 1){
		$redeem = reward_zone_redeem_point($RS_giftcard_data->fields['card_value'],$RS_giftcard_data->fields['discount'],$RS_giftcard_data->fields['is_per']);
		$redeem = $redeem['redeem'];
	}else{
		$redeem = $RS_giftcard_data->fields['redeem_point_value'];
	}
?>
<div id="thin_middle">		
    <div id="thin_middle">
        <div class="page_title"><?php echo $RS_giftcard_data->fields['title']; ?></div>
        <div style="float:left;width:233px;">
            <a href="javascript:void(0);"><div id="prod_img">
                    <img src="<?php echo ASSETS_IMG."/a/giftcards/".$RS_giftcard_data->fields['image']; ?>" />
                </div>
            </a>
        </div>
        <div  id="prod_details">
            <hr>
            <div class="product_detailss">
                <span class="product_detailss_titel">Redeem with <?php echo $redeem; ?> scanflip Points</span>
            </div>
            <div class="left"><div class="calltoaction"><a href="javascript:void(0);" data-gftshipto="<?php echo $RS_giftcard_data->fields['ship_to']; ?>" data-gftcid="<?php echo $RS_giftcard_data->fields['id']; ?>" data-merchant="<?php echo $RS_giftcard_data->fields['is_merchant']; ?>" data-reward="<?php  echo $_REQUEST['reward_type']; ?>" data-ftencid="<?php echo base64_encode($RS_giftcard_data->fields['id']); ?>">
                        Redeem
                    </a></div></div>		
            <div style="clear:both"></div>
            <div class="clearfloat"></div>
            <div class="clearfloat_deta">
                <hr>
                <strong>Details</strong><br>
                <div style="float:left;" id="prod_details">

                    <?php echo $RS_giftcard_data->fields['description']; ?>
                    <p></p>
                </div>
                <div style="display:none;" id="marketing_popup"><p>&nbsp;</p></div></div>
        </div>
    </div>
</div>
<?php 
if($RS_giftcard_data->fields['is_merchant'] == 1){
       
        $mlatitude = '';
        $mlongitude = '';
        
        if(isset($_COOKIE['mycurrent_lati']) && isset( $_COOKIE['mycurrent_long']))
        {
            $mlatitude = $_COOKIE['mycurrent_lati'];
            $mlongitude = $_COOKIE['mycurrent_long'];
        }
        
        
        $res = $objDB->Conn->Execute('SELECT id, location_name,created_by,latitude,longitude , ROUND( (((ACOS( SIN( ( '.$mlatitude.' * PI( ) /180 ) ) * SIN( (`latitude` * PI( ) /180 ) ) + COS( ( '.$mlatitude.' * PI( ) /180 ) ) * COS( (`latitude` * PI( ) /180 ) ) * COS( (( '.$mlongitude.' - `longitude` ) * PI() /180 )))) *180 / PI()) *60 * 1.1515), 2) AS distance FROM locations where created_by=? HAVING distance <? ORDER BY distance',array($RS_giftcard_data->fields['merchant_id'],2500));
        
        if($res->RecordCount() > 0){
                
                while($r = $res->FetchRow()){
                        $locations[]=$r;
                }
        }
        
?>
<div style ="height: 300px; width: 100%;" id="map-canvas"></div>
<script>
initialize();
var map;
var Markers = [];

function initialize() {
        var bounds = new google.maps.LatLngBounds();
        var origin = new google.maps.LatLng(<?php echo $mlatitude; ?>,<?php echo $mlongitude; ?>);
        var mapOptions = {
          zoom: 8,
          center: origin
        };

        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        map.setTilt(45);
        var infowindow = new google.maps.InfoWindow();
	var locations = <?php echo json_encode($locations);?>;
        
        var marker, i;
        for (i = 0; i < locations.length; i++) 
        { 
                var position = new google.maps.LatLng(locations[i]['latitude'], locations[i]['longitude']);
                bounds.extend(position);
                
                marker = new google.maps.Marker({
                  position: position,
                  map: map,
                  icon: new google.maps.MarkerImage('<?php echo ASSETS_IMG; ?>/c/pin-small.png')
                 });

          google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
              infowindow.setContent(locations[i]['location_name']);
              infowindow.open(map, marker);
            }
          })(marker, i));
          
          // Automatically center the map fitting all markers on the screen
        map.fitBounds(bounds);
        }
 
}
 
// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        this.setZoom(10);
        google.maps.event.removeListener(boundsListener);
    });



    </script>

<?php
}

}

/*** addnew address book of user ******/
/**
 * @uses save countinue address
 * @param enterAddressFullName
 * @used in pages :addnew-address.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['savecontueaddress']))
{
		
	$array = $json_array = $where_clause = array();
	$array['full_name'] = ucwords(strtolower($_REQUEST['enterAddressFullName']));
	
	$array['address_line_1'] = $_REQUEST['enterAddressAddressLine1'];
	$array['address_line_2'] = $_REQUEST['enterAddressAddressLine2'];
	$array['city'] = $_REQUEST['enterAddressCity'];
	$array['state'] = $_REQUEST['enterAddressStateOrRegion'];
	$array['country'] = $_REQUEST['enterAddressCountryCode'];
	$array['phone_number'] = $_REQUEST['enterAddressPhoneNumber'];
	$postalcode=str_replace(" ","",$_REQUEST['enterAddressPostalCode']);
	$array['zip'] = strtoupper($postalcode);
	$array['address_type'] = $_REQUEST['AddressType'];
    $array['user_id'] = $_SESSION['customer_id'];

	$id  = $objDB->Insert($array, "customer_addressbook");		

	$json_array['message'] = "Address successfully added";
                
	$json = json_encode($json_array);
	 //header("Location:".WEB_PATH."/address_select.php?order=".$_REQUEST['processorder']);
	 header("Location:".WEB_PATH."/review-address.php?edit=".$id."&order=".$_REQUEST['processorder']);
	
	echo $json;
	exit();
}
/****** edit user address book *********/
/**
 * @uses edit user address
 * @param enterAddressFullName,enterAddressAddressLine1,enterAddressAddressLine2,enterAddressCity,enterAddressStateOrRegion
 * @used in pages :edit-address.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['editcontueaddress']))
{
	$array = $json_array = $where_clause = array();
	$array['full_name'] = ucwords(strtolower($_REQUEST['enterAddressFullName']));
	
	$array['address_line_1'] = $_REQUEST['enterAddressAddressLine1'];
	$array['address_line_2'] = $_REQUEST['enterAddressAddressLine2'];
	$array['city'] = $_REQUEST['enterAddressCity'];
	$array['state'] = $_REQUEST['enterAddressStateOrRegion'];
	$array['country'] = $_REQUEST['enterAddressCountryCode'];
	$array['phone_number'] = $_REQUEST['enterAddressPhoneNumber'];
	$postalcode=str_replace(" ","",$_REQUEST['enterAddressPostalCode']);
	$array['zip'] = strtoupper($postalcode);
	$array['address_type'] = $_REQUEST['AddressType'];
    $array['user_id'] = $_SESSION['customer_id'];
	
	$where_clause['id'] = $_REQUEST['addr_id'];
	
	$objDB->Update($array, "customer_addressbook", $where_clause);
	$json_array['message'] = "Address successfully updated.";
   $json = json_encode($json_array);
//	 header("Location:".WEB_PATH."/address_select.php?order=".$_REQUEST['processorder']);
	 header("Location:".WEB_PATH."/review-address.php?edit=".$_REQUEST['addr_id']."&order=".$_REQUEST['processorder']);
	echo $json;
	exit();
}
/*** get address book of user  **************/
if(isset($_REQUEST['getaddressbookofuser']))
{
/*	$sql= "select * from customer_addressbook where user_id=".$_REQUEST['user_id'];
	
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select * from customer_addressbook where user_id=?",array($_REQUEST['user_id']));

	if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json_array["records"]= array();
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	  exit();
}
/*** get selected address book of user  **************/
if(isset($_REQUEST['getselectedaddressbookofuser']))
{
	/*$sql= "select * from customer_addressbook where user_id=".$_REQUEST['user_id']." and id=".$_REQUEST['addrid'];
	
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select * from customer_addressbook where user_id=? and id=?",array($_REQUEST['user_id'],$_REQUEST['addrid']));

	if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json_array["records"]= array();
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	  exit();
}
if(isset($_REQUEST['proccessedredeem']))
{
	print_r($_REQUEST);exit;
	$array = $json_array = $where_clause = array();
	$array['user_id'] = $_SESSION['customer_id'];
	$array['giftcard_id'] = $_REQUEST['giftcard_id'];
	$array['order_date'] = date("Y-m-d H:i:s");
	$array['status'] = 2;
	$array['ship_to_address'] = urldecode($_REQUEST['shipping_address']);
	$array['order_note'] = "";
	$array['order_number'] = $_REQUEST['giftcard_id'].strtotime(date("Y-m-d H:i:s")).$array['user_id'];
	
	$id  = $objDB->Insert($array, "giftcard_order");
	
	$json_array['message'] = "Your Gift card order is placed successfully.";
	$json_array['order_number'] = $_REQUEST['giftcard_id'].strtotime(date("Y-m-d H:i:s")).$array['user_id'];
	$json = json_encode($json_array);
	// header("Location:".WEB_PATH."/success-order.php?order=".$array['order_number']);
	// header("Location:".WEB_PATH."/review-address.php?edit=".$id."&order=".$_REQUEST['processorder']);
	
	echo $json;
	exit();
}
/**
 * @uses get order detail
 * @param user_id,ordreno
 * @used in pages :sucess-order.php
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getoderdetail']))
{
	/*$sql= "select * from giftcard_order where user_id=".$_REQUEST['user_id']." and order_number=".$_REQUEST['ordreno'];
	
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select * from giftcard_order where user_id=? and order_number=?",array($_REQUEST['user_id'],$_REQUEST['ordreno']));

	if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json_array["records"]= array();
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	  exit();
}

/**
 * @uses get gift card detail
 * @param user_id,ordreno
 * @used in pages :
 * @author Sangeeta
 * @return type json array
 */
if(isset($_REQUEST['getgiftcardinformation']))
{
/*	$sql= "select * from giftcards where id	=".$_REQUEST['giftcardid'];
	
	$RS = $objDB->Conn->Execute($sql);*/
	$RS = $objDB->Conn->Execute("select * from giftcards where id=?",array($_REQUEST['giftcardid']));

	if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json_array["records"]= array();
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
	 $json = json_encode($json_array);
	 echo $json;
	  exit();
}

function create_unique_code_for_getcard() 
{
        $code_length = 6;
        $alfa = "12345678910ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $code = "";
        for ($i = 0; $i < $code_length; $i ++) {
                $code .= $alfa[rand(0, strlen($alfa) - 1)];
        }
        return $code;
}
/******** 
@USE : Get card for user
@PARAMETER : customer_id,card_id
@RETURN : status
@USED IN PAGES : 
*********/
if(isset($_REQUEST['getcard_for_user']))
{
	//$customer_id=99;
	$customer_id=$_REQUEST['customer_id'];
	
	//$card_id=2;
	$card_id=$_REQUEST['card_id'];

	$device_activation_mode=$_REQUEST['device_activation_mode'];

	
	$card_query=  'SELECT cards_left,card_status from merchant_loyalty_card where cards_left>0 and card_status=2 and id='.$card_id;
	$card_rs = $objDB->Conn->Execute($card_query);
	
	if($card_rs->RecordCount()==0)
	{
		$json_array['status'] = "false";
		$json_array['message'] = "card is not active.";
	}
	else
	{
	
		$RS = $objDB->Conn->Execute("SELECT * from customer_loyalty_reward_card where card_type=1 and card_id=? and user_id=? and card_status=1", array($card_id,$customer_id));
		$total_records = $RS->RecordCount();
		
		$json_array = array();
					
		if($total_records==0)
		{
			$card_query=  'SELECT cards_left,stamps_per_card,points_required from merchant_loyalty_card where id='.$card_id;
			$card_rs = $objDB->Conn->Execute($card_query);
			$card_left = $card_rs->fields['cards_left'];
			$stamps_per_card = $card_rs->fields['stamps_per_card'];
			$block_point = $card_rs->fields['points_required'];
			
			$user_card = array();
			
			$code ="";
			do
			{
				$code =  "LC".create_unique_code_for_getcard(); 
				$card_query1=  'select count(*) total from customer_loyalty_reward_card where code="'.$code.'" and code in(select code from customer_loyalty_reward_card)';
				$card_rs1 = $objDB->Conn->Execute($card_query1);
				$total = $card_rs1->fields['total'];		
			}while($total>0);
			
			$user_card['code'] = $code; 
			$user_card['user_id'] = $customer_id;
			$user_card['card_id'] = $card_id;
			$user_card['card_type'] = 1; // loyalty
			$user_card['card_status'] = 1; // active
			$user_card['total_stamp_left'] = $stamps_per_card;
			$user_card['date_activated'] = date("Y-m-d H:i:s");
			$user_card['device_activation_mode'] = $device_activation_mode;
			$user_card['block_point'] = $block_point;
			$user_card['spent_point'] = 0;
			$objDBWrt->Insert($user_card, "customer_loyalty_reward_card");
			
			$Sql = "Update merchant_loyalty_card set cards_left=cards_left-1 where id=".$card_id;
			$objDBWrt->Conn->Execute($Sql);
			if($card_left==1)
			{
				$sql='update merchant_loyalty_card set card_status = 4 where id ='.$card_id.';';
				$rs=$objDBWrt->Conn->Execute($sql);
			}
			
			$json_array['status'] = "true";
			$json_array['message'] = "user card inserted";
					
		}
		else
		{
			$json_array['status'] = "false";
			$json_array['message'] = "You already have one active loyalty card from the merchant";
		}
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/******** 
@USE : Delete card for user
@PARAMETER : customer_id,card_id
@RETURN : status
@USED IN PAGES : 
*********/
if(isset($_REQUEST['deletecard_for_user']))
{
	//$customer_id=99;
	$customer_id=$_REQUEST['customer_id'];
	
	//$card_id=2;
	$card_id=$_REQUEST['card_id'];
	
	$RS = $objDB->Conn->Execute("SELECT * from customer_loyalty_reward_card where card_type=1 and card_id=? and user_id=? and card_status=1", array($card_id,$customer_id));
	$total_records = $RS->RecordCount();
	
	//echo $total_records;
	
	$json_array = array();
                
	if($total_records>0)
	{
		if($RS->fields['total_stamp_left']>0)
		{
			// if total_stamp_left > 0 , then only release point from merchant and update status
			
			$array_mlc = array();
			$array_mlc['id'] = $RS->fields['card_id'];
			$RS_mlc = $objDB->Show("merchant_loyalty_card",$array_mlc);
			
			$array_mlrc = array();
			$array_mlrc['loyalty_card_id'] = $RS->fields['card_id'];
			$RS_mlrc = $objDB->Show("merchant_loyalty_reward_card",$array_mlrc);
			
			$pack_data = array();
			$pack_data['merchant_id'] = $RS_mlc->fields['merchant_id'];
			$get_pack_data = $objDB->Show("merchant_billing",$pack_data);

			$pack_data1 = array();
			$pack_data1['id'] = $get_pack_data->fields['pack_id'];
			$get_billing_pack_data = $objDB->Show("billing_packages",$pack_data1);
									
			$stamp_left = $RS->fields['total_stamp_left'];
			$reward_per_visit = $RS_mlc->fields['reward_per_visit'];
			$transaction_fees_stamp = $get_billing_pack_data->fields['transaction_fees_stamp']; 
		
			$release_point = ($stamp_left * $reward_per_visit) + ($stamp_left * $transaction_fees_stamp) + $RS_mlrc->fields['reward_points']; 
			
			$sql1='update merchant_point_management set ';
			$sql1.=' points_available = points_available + '.$release_point;
			$sql1.=',points_blocked = points_blocked - '.$release_point;
			$sql1.=',points_blocked_loyaltycard = points_blocked_loyaltycard - '.$release_point;
			$sql1.=' where merchant_id ='.$_SESSION['merchant_id'].';';
			$rs1=$objDBWrt->Conn->Execute($sql1);
									
			$Sql = "Update customer_loyalty_reward_card set block_point=block_point-".$release_point.",card_status=2,date_deleted='".date("Y-m-d H:i:s")."' where id=".$RS->fields['id'];
			$objDBWrt->Conn->Execute($Sql);
						
			$json_array['status'] = "true";
			$json_array['message'] = "user card deleted";
		}
		else
		{
			// if total_stamp_left = 0 , then only update status
			$Sql = "Update customer_loyalty_reward_card set card_status=2,date_deleted='".date("Y-m-d H:i:s")."' where id=".$RS->fields['id'];
			$objDBWrt->Conn->Execute($Sql);
						
			$json_array['status'] = "true";
			$json_array['message'] = "user card deleted";
		}        
	}
	else
	{
		$json_array['status'] = "false";
        $json_array['message'] = "you card is not active";
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}
if(isset($_REQUEST['load_rw_category'])){
	$json = array();

	if($_REQUEST['id'] >0){
		if($_REQUEST['id'] == 2){
			$array_where_act['active']=1;
			$RSCat = $objDB->Show("categories",$array_where_act);
	
			
		}else if($_REQUEST['id'] == 1){
                        $RSCat = $objDB->Conn->Execute("Select * from giftcard_categories where active=?",array(1));		
		}

		$i = 0;
		while($Row = $RSCat->FetchRow()){
	
			$json[$i]['id']=$Row['id'];	
			$json[$i]['cat_name']=$Row['cat_name'];
			$i++;	
		}

	}
	echo json_encode($json);
	exit;
}
function reward_zone_redeem_point($oprice,$dprice,$is_per){

        $app_trans_fee = REWARD_ZONE_APPL_TRANS_FEE;

        $app_fee = $oprice * $app_trans_fee;

        if(!empty($dprice)){
                if($is_per == 1){
                        $discount = $oprice*$dprice/100;
                }else{
                        $discount = $dprice;
                }
        }else{
                $discount = 0;
        }

        $discounted_value = $oprice -$discount;
        $redeem = round($discounted_value + $app_fee)*100;
        
        return array('redeem'=>$redeem,'discount'=>$discount,'discounted_value'=>$discounted_value,'app_fee'=>$app_fee*100);
}

if(isset($_REQUEST['redeem_merchant_reward_type'])){

	if($_REQUEST['reward_type'] == 1){
		$Res = $objDB->Conn->Execute('Select * from giftcards where id='.$_REQUEST['id']);
		if($Res->RecordCount()>0){
			$redeem = reward_zone_redeem_point($Res->fields['card_value'],$Res->fields['discount'],$Res->fields['is_per']);
			
			$rc['giftcard_id'] = $_REQUEST['id'];
			$rc['certificate_id'] = "gf_".$_REQUEST['giftcard_id'].strtotime(date("Y-m-d H:i:s")).$_SESSION['customer_id'];
			$rc['reward_zone_campaign_id'] = 0;
			$rc['original_amount'] = $Res->fields['card_value'];	
		}
	}else{
		$Res = $objDB->Conn->Execute('Select * from rewardzone_campaigns where id='.$_REQUEST['id']);
		if($Res->RecordCount()>0){
			$redeem = reward_zone_redeem_point($Res->fields['value'],$Res->fields['discount'],$Res->fields['is_percentage']);
			
			$rc['giftcard_id'] = 0;
			$rc['certificate_id'] = "cp_".$_REQUEST['id'].strtotime(date("Y-m-d H:i:s")).$_SESSION['customer_id'];
			$rc['reward_zone_campaign_id'] = $_REQUEST['id'];
			$rc['original_amount'] = $Res->fields['value'];
		}
	}
	if($Res->RecordCount()>0){
	
		$rc['user_id'] = $_SESSION['customer_id'];
		
		$rc['reward_type_id'] = $_REQUEST['reward_type'];
		$rc['balance'] = 0;
		
		$rc['discounted_amount'] = $redeem['discount'];
		$rc['points_available'] = $redeem['redeem']-$redeem['app_fee'];
		$rc['points_earned'] = 0;
		$rc['sale_price'] = $redeem['discounted_value'];
		$rc['date_issued'] = date('Y-m-d H:i:s');
		$rc['status'] = 1;
		$rc['created'] = date('Y-m-d H:i:s');
		
		echo $id = $objDBWrt->Insert($rc,"reward_certificates");
	}else{
		echo 0;
	}
}

if(isset($_REQUEST['view_rewardzone_campaign'])){
	if (isset($_REQUEST['id'])) {
		$id = $_REQUEST['id'];
		$camp = $objDB->Conn->Execute('Select r.id,r.title,r.merchant_id,r.category_id ,r.image,r.value,r.description,r.discount,r.terms_cond,r.is_percentage,m.business,c.cat_image from rewardzone_campaigns r,merchant_user m, categories c where r.category_id =c.id and m.id = r.merchant_id  and r.active=? and r.is_deleted=? and r.id=? ',array(1,0,$id));
		if($camp->RecordCount()>0){
		    $redeem = reward_zone_redeem_point($camp->fields['value'],$camp->fields['discount'],$camp->fields['is_percentage']);
						$redeem = $redeem['redeem'];   
					
						$dis= round(($camp->fields['discount']/$camp->fields['value'])*100).'%'; 
		?>

		<div id="thin_middle">		
		    <div id="thin_middle">
		        <div class="page_title"><?php echo $camp->fields['title']; ?></div>
		        <div style="float:left;width:233px;">
		            <a href="javascript:void(0);">
		                <div id="prod_img">
		                <div class="discount_wrap">
										<div class="dealmetric title">
											 <div class="value">Value</div>
											 <div class="discount">Discount</div>
											 
										</div>
										<div class="dealmetric values">
											<div class="value">$<?php echo $camp->fields['value']; ?></div>
											<div class="discount"><?php echo $dis; ?></div>
										
										</div>
							      </div>
		                    <img src="<?php echo ASSETS_IMG; ?>/m/campaign/<?php echo $camp->fields['image']; ?>" />
		                </div>
		            </a>
		        </div>
		        <div  id="prod_details">
		            <hr>
		            <div class="product_detailss">
		                <span class="product_detailss_titel">Redeem with <?php echo $redeem ; ?> scanflip Points</span>
		            </div>
		            <div class="left"><div class="calltoaction_c"><a href="javascript:void(0);"  data-campid="<?php echo $camp->fields['id']; ?>" data-reward="<?php echo $_REQUEST['reward_type']; ?>" >
		                        Redeem
		                    </a></div></div>		
		            <div style="clear:both"></div>
		            <hr>
		            <div class="clearfloat"></div>
		            
					
					<?php 
					$mlatitude = '';
					$mlongitude = '';
		
					if(isset($_COOKIE['mycurrent_lati']) && isset( $_COOKIE['mycurrent_long']))
					{
					    $mlatitude = $_COOKIE['mycurrent_lati'];
					    $mlongitude = $_COOKIE['mycurrent_long'];
					}
		
		
					$res = $objDB->Conn->Execute('SELECT id, location_name,created_by,latitude,longitude , ROUND( (((ACOS( SIN( ( '.$mlatitude.' * PI( ) /180 ) ) * SIN( (`latitude` * PI( ) /180 ) ) + COS( ( '.$mlatitude.' * PI( ) /180 ) ) * COS( (`latitude` * PI( ) /180 ) ) * COS( (( '.$mlongitude.' - `longitude` ) * PI() /180 )))) *180 / PI()) *60 * 1.1515), 2) AS distance FROM locations where created_by=? HAVING distance <? ORDER BY distance',array($camp->fields['merchant_id'],2500));
		
					if($res->RecordCount() > 0){
				
						while($r = $res->FetchRow()){
							$locations[]=$r;
						}
					}		
					?>
					
					<style>
					      html, body, #map-canvas {
						height: 100%;
						margin: 0px;
						padding: 0px
					      }
					</style>
					    
					<script>
						initialize();
						var map;
						var Markers = [];

						function initialize() {
							var bounds = new google.maps.LatLngBounds();
							var origin = new google.maps.LatLng(<?php echo $mlatitude; ?>,<?php echo $mlongitude; ?>);
							var mapOptions = {
							  zoom: 8,
							  center: origin
							};

							map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
							map.setTilt(45);
							var infowindow = new google.maps.InfoWindow();
							var locations = <?php echo json_encode($locations);?>;
		
							var marker, i;
							for (i = 0; i < locations.length; i++) 
							{ 
								var position = new google.maps.LatLng(locations[i]['latitude'], locations[i]['longitude']);
								bounds.extend(position);
				
								marker = new google.maps.Marker({
								  position: position,
								  map: map,
								  icon: new google.maps.MarkerImage('<?php echo ASSETS_IMG; ?>/c/pin-small.png')
								 });

							  google.maps.event.addListener(marker, 'click', (function(marker, i) {
							    return function() {
							      infowindow.setContent(locations[i]['location_name']);
							      infowindow.open(map, marker);
							    }
							  })(marker, i));
							  
							  // Automatically center the map fitting all markers on the screen
							map.fitBounds(bounds);
							}
						 
						}
						 
						// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
						    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
							this.setZoom(10);
							google.maps.event.removeListener(boundsListener);
						    });



						    </script>
					    
					

					</div>
                                        <div class="clearfloat_deta" style="clear:both;">
                                            
                                            <div class="">
                                                <ul id="sidemenu">
                                                    <li id="tab-type" class="div_camp_detail">
                                                        <a class="current">Detail</a>
                                                    </li>

                                                    <li id="tab-type" class="div_camp_terms" >
                                                        <a >Terms & Conditions</a>
                                                    </li>
                                                    <li id="tab-type" class="div_camp_locations" >
                                                        <a >Locations</a>
                                                    </li>					
                                                </ul>
                                                <div id="div_camp_detail" class="tabs" style="display:block;"><?php echo $camp->fields['description']; ?></div>
                                                <div id="div_camp_terms" class="tabs" style="display:none;"><?php echo $camp->fields['terms_cond']; ?></div>
                                                <div id="div_camp_locations" class="tabs" >
                                                    <div style ="height: 350px; width: 100%;" id="map-canvas"></div>
                                                </div>
                                            </div>
                                        </div>
		                </div>		
		                
		    </div>
		
	<?php }
		
	}
}
?>
