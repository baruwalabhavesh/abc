<?php
/******** 
@USE : customer app functions
@PARAMETER : 
@RETURN : 
@USED IN PAGES : call from app
*********/
//header('Content-type: text/html; charset=utf-8');
//require_once("classes/Config.Inc.php");
require_once(LIBRARY . "/class.phpmailer.php");
//include_once(SERVER_PATH . "/classes/DB.php");
//include_once(SERVER_PATH . "/classes/func_print_coupon.php");
//include_once(SERVER_PATH . "/classes/JSON.php");
//require_once(LIBRARY . "/PHP-PasswordLib-master/lib/PasswordLib/PasswordLib.php");
//$objJSON = new JSON();
//$objDB = new DB('read');
//$objDBWrt = new DB('write');

function get_field_value($Row) {
        $ar = $Row;

        $ar1 = array_unique($ar);
        for ($i = 0; $i < (count($ar)); $i++) {
                if (key_exists($i, $ar)) {
                        unset($ar[$i]);
                }
        }

        return $ar;
}
/**
 * @uses login
 * @param emailaddress
 * @return type $json
 */
if (isset($_REQUEST['btnLogin'])) {

        $array = $json_array = array();
        $array['emailaddress'] = $_REQUEST['emailaddress'];

        $RS = $objDB->Show("customer_user", $array);
        $total = $RS->RecordCount();
        if ($total == 0) {
                $json_array['msg'] = "hi";
                $json_array['status'] = "false";
                $json_array['message'] = $client_msg["login_register"]["Msg_Email_not_registerd"];
                $json = json_encode($json_array);
                echo $json;
                exit();
        }

        $array = $json_array = array();
        $array['emailaddress'] = $_REQUEST['emailaddress'];
        //$array['password'] = md5($_REQUEST['password']);
        $PasswordLib2 = new \PasswordLib\PasswordLib;
        ///echo "hello";
        //print_r($RS);
        $RS = $objDB->Show("customer_user", $array);
        if($RS->fields['password']=="")
        {
			 $result = 0;
		}
		else
		{
			$result = $PasswordLib2->verifyPasswordHash($_REQUEST['password'], $RS->fields['password']);
		}
//echo "===".$result;
        //$total=$RS->RecordCount();
        if (!$result) 
        {
                //echo "valid";
                $json_array['status'] = "false";
                $json_array['message'] = $client_msg["login_register"]["Msg_Email_Password"];
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
        if ($RS->fields['active'] == 0) {
                //echo "activate";
                $json_array['status'] = "false";
                $json_array['message'] = $client_msg["login_register"]["Msg_Not_Activated"];
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
        $Row = $RS->FetchRow();
        //$_SESSION['customer_id'] = $Row['id'];
        //$_SESSION['customer_info'] = $Row;



        $array_values = $where_clause = $array = array();
        $array_values['lastvisit_date'] = date("Y-m-d H:i:s");

        if (isset($_REQUEST['device_id'])) {
                $array_values['device_id'] = $_REQUEST['device_id'];
        }

        $where_clause['id'] = $Row['id'];
        $objDB->Update($array_values, "customer_user", $where_clause);

        $user_session = session_id();
        $array['sessiontime'] = strtotime(date("Y-m-d H:i:s"));
        $array['session_id'] = base64_encode($Row['id']);
        $array['session_data'] = md5($array['sessiontime'] . $user_session);
        //$array['user_type'] = 1;
        $objDB->Insert($array, "user_sessions");

        $json_array['status'] = "true";
        $json_array['message'] = "Successfully Login.";
        $json_array['customer_id'] = $Row['id'];

        $array_cust_info = array();
        $array_cust_info['emailaddress'] = $_REQUEST['emailaddress'];
        $RS_cust_info = $objDB->Show("customer_user", $array_cust_info);
        $Row_cust_info = $RS_cust_info->FetchRow();

        $json_array['customer_info'] = get_field_value($Row_cust_info);


        /* $cust_sql = 'select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>0 and  country <>""  and id='.$Row['id'];//.$customer_id;
          $RS_cust_data=$objDB->Conn->Execute($cust_sql); */
        $RS_cust_data = $objDB->Conn->Execute('select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>? and  country <>""  and id=?', array(0, $Row['id']));
        $is_profileset = $RS_cust_data->RecordCount();

        if ($is_profileset == 0) {
                $json_array['is_profileset'] = 0;
        } else {
                $json_array['is_profileset'] = 1;
        }

        $pos = strpos($json_array['customer_info']['profile_pic'], 'http');
        if ($pos === false) {
                if ($json_array['customer_info']['profile_pic'] != "") {
                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . "/c/usr_pic/" . $json_array['customer_info']['profile_pic'];
                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                } else {
                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . '/c/default_small_user.jpg';
                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                }
        } else {
                $json_array['customer_info']['profile_pic'] = $json_array['customer_info']['profile_pic'];
                $json_array['customer_info']['facebook_profile_pic'] = 1;
        }

        if ($json_array['customer_info']['card_id'] != "") {
                $json_array['customer_info']['card_qrcode_url'] = WEB_PATH . "/merchant/demopdf/demo_qrcode_card.php?size=200&card_id=" . $json_array['customer_info']['card_id'];
        }

        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses get location bussiness name
 * @param l_id
 * @return type $json
 */

if (isset($_REQUEST['getlocationbusinessname'])) {
        /* $sql = "select business from merchant_user where id=(select created_by from locations where id=".$_REQUEST['l_id'].")";

          $RS_bus= $objDB->Conn->Execute($sql); */
        $RS_bus = $objDB->Conn->Execute("select business from merchant_user where id=(select created_by from locations where id=?)", array($_REQUEST['l_id']));
        $json_array = array();
        $busines_name = $RS_bus->fields['business'];
        $json_array['status'] = "true";
        $json_array['bus_name'] = $busines_name;
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses generate card id
 * @param cust_id
 * @return type $json
 */

if (isset($_REQUEST['generate_cardid'])) {

        /*
          echo date("siHdmYs");
          echo "<br/>";
          echo strlen(date("siHdmYs"));
          echo "<br/>";
          exit();
         */


        $card_id = "";

        /*
          $card_id=date("siHdmYs");
         */
        /*
          $length=16;
          $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
          //$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
          $codeAlphabet.= "0123456789";
          for($i=0;$i<$length;$i++)
          {
          $card_id .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
          }
         */

        $card_id = $_REQUEST['cust_id'] . date("siH");

        //echo $card_id;
        //exit();

        $array_values = $where_clause = $array = array();
        $array_values['card_id'] = $card_id;
        $where_clause['id'] = $_REQUEST['cust_id'];
        $objDB->Update($array_values, "customer_user", $where_clause);

        $cSession = curl_init();
        curl_setopt($cSession, CURLOPT_URL, WEB_PATH . "/Apple-pass/generate-card/mypass.php?cust_id=" . $_REQUEST['cust_id']);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cSession, CURLOPT_HEADER, false);
        $result = curl_exec($cSession);
        curl_close($cSession);

        $json_array = array();
        $json_array['status'] = "true";
        $json_array['card_id'] = $card_id;
        $json_array['card_qrcode_url'] = WEB_PATH . "/merchant/demopdf/demo_qrcode_card.php?size=200&card_id=" . $card_id;
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses create unique code for qrcode
 * @param 
 * @return string
 */
function create_unique_code_for_qrcode() {
        $code_length = 8;
        //echo $alfa = "1AB2CD3EF4G5HI6JK7LM8N9OP10QRSTU".$campaign_id."VWXYZ";
        $alfa = "12345678910ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $code = "";
        for ($i = 0; $i < $code_length; $i ++) {
                $code .= $alfa[rand(0, strlen($alfa) - 1)];
        }
        return $code;
}

/**
 * @uses update qrcode metrics campaign
 * @param 
 * @return string
 */
if (isset($_REQUEST['update_qrcode_metrics_campaign'])) {
        $campaignid = $_REQUEST['campaign_id'];
        $locationid = $_REQUEST['location_id'];
        $custid = $_REQUEST['customer_id'];
        $qrcode = base64_decode($_REQUEST['qrcode']);

        /* $Sql  = "SELECT * FROM locations  where id=".$locationid;		
          $RS_location = $objDB->Conn->Execute($Sql); */
        $RS_location = $objDB->Conn->Execute("SELECT * FROM locations  where id=?", array($locationid));
        $timezone = $RS_location->fields['timezone'];
        $dt_sql = "SElect CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR('" . $timezone . "',1, POSITION(',' IN '" . $timezone . "')-1)) dte ";
        $RS_dt = $objDB->Conn->Execute($dt_sql);

        $uid = create_unique_code_for_qrcode() . "" . strtotime(date("Y-m-d H:i:s"));

        /* $sql_qrcode = "select id from qrcodes where qrcode='".$qrcode."' ";
          $RS_qrcode = $objDB->Conn->Execute($sql_qrcode ); */
        $RS_qrcode = $objDB->Conn->Execute("select id from qrcodes where qrcode=? ", array($qrcode));
        $q_id = $RS_qrcode->fields['id'];

        $insert_array = array();
        $insert_array['is_unique'] = 0;
        $insert_array['qrcode_id'] = $q_id;
        $insert_array['campaign_id'] = $campaignid;
        $insert_array['location_id'] = $locationid;
        $insert_array['is_location'] = 0;
        $insert_array['is_superadmin'] = 0;
        $insert_array['unique_id'] = $uid;
        $insert_array['scaned_date'] = $RS_dt->fields['dte'];
        $insert_array['user_id'] = $custid;
        $objDB->Insert($insert_array, "scan_qrcode");

        $json_array = array();
        $json_array['status'] = "true";
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses update qrcode metrics location
 * @param location_id,customer_id,qrcode
 * @return string
 */

if (isset($_REQUEST['update_qrcode_metrics_location'])) {
        $campaignid = 0;
        $locationid = $_REQUEST['location_id'];
        $custid = $_REQUEST['customer_id'];
        $qrcode = base64_decode($_REQUEST['qrcode']);

        /* $Sql  = "SELECT * FROM locations  where id=".$locationid;		
          $RS_location = $objDB->Conn->Execute($Sql); */
        $RS_location = $objDB->Conn->Execute("SELECT * FROM locations  where id=?", array($locationid));
        $timezone = $RS_location->fields['timezone'];
        $dt_sql = "SElect CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR('" . $timezone . "',1, POSITION(',' IN '" . $timezone . "')-1)) dte ";
        $RS_dt = $objDB->Conn->Execute($dt_sql);

        $uid = create_unique_code_for_qrcode() . "" . strtotime(date("Y-m-d H:i:s"));

        /* $sql_qrcode = "select id from qrcodes where qrcode='".$qrcode."' ";
          $RS_qrcode = $objDB->Conn->Execute($sql_qrcode ); */
        $RS_qrcode = $objDB->Conn->Execute("select id from qrcodes where qrcode=? ", array($qrcode));
        $q_id = $RS_qrcode->fields['id'];

        $insert_array = array();
        $insert_array['is_unique'] = 0;
        $insert_array['qrcode_id'] = $q_id;
        $insert_array['campaign_id'] = 0;
        $insert_array['location_id'] = $locationid;
        $insert_array['is_location'] = 1;
        $insert_array['is_superadmin'] = 0;
        $insert_array['unique_id'] = $uid;
        $insert_array['scaned_date'] = $RS_dt->fields['dte'];
        $insert_array['user_id'] = $custid;
        $objDB->Insert($insert_array, "scan_qrcode");

        $json_array = array();
        $json_array['status'] = "true";
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses save to card
 * @param card_id,location_id,campaign_id
 * @return string
 */

if (isset($_REQUEST['save_to_card'])) {
        $array = array();
        $array['card_id'] = $_REQUEST['card_id'];
        $array['campaign_id'] = $_REQUEST['campaign_id'];
        $array['location_id'] = $_REQUEST['location_id'];
        $objDB->Insert($array, "customer_card");

        $json_array = array();
        $json_array['status'] = "true";
        $json_array['message'] = "campaign added successfully to cart.";
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses remove from card
 * @param card_id,location_id,campaign_id
 * @return string
 */

if (isset($_REQUEST['remove_from_card'])) {
        $array = array();
        $array['card_id'] = $_REQUEST['card_id'];
        $array['campaign_id'] = $_REQUEST['campaign_id'];
        $array['location_id'] = $_REQUEST['location_id'];
        $objDB->Insert($array, "customer_card");

        /* $sql = "delete from customer_card where card_id=". $_REQUEST['card_id']." and campaign_id=".$_REQUEST['campaign_id']." and location_id=".$_REQUEST['location_id'];
          $RS= $objDB->Conn->Execute($sql); */
        $RS = $objDBWrt->Conn->Execute("delete from customer_card where card_id=? and campaign_id=? and location_id=?", array($_REQUEST['card_id'], $_REQUEST['campaign_id'], $_REQUEST['location_id']));

        $json_array = array();
        $json_array['status'] = "true";
        $json_array['message'] = "campaign removed successfully from cart.";
        $json = json_encode($json_array);
        echo $json;
        exit();
}


/**
 * @uses check activation code 
 * @param activation_code
 * @return string
 */
if (isset($_REQUEST['btnActivationCode_'])) {
        //print_r($_REQUEST);
        $activation_code = $_REQUEST['activation_code'];
        $_SESSION['msg'] = "";
        $json_array = array();

        /*
          if($_SESSION['customer_id'] =="")
          {
          $url = urlencode(WEB_PATH."/includes/customer/process.php?btnActivationCode_=1&activation_code=".$activation_code) ;
          header("Location: ".WEB_PATH."/register.php?url=".$url);
          exit();
          }
          $customer_id = $_SESSION['customer_id'];
         */
        /* $Sql = "SELECT * FROM activation_codes WHERE  activation_code='$activation_code'";

          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE  activation_code=?", array($activation_code));

        if ($RS->RecordCount() <= 0) {
                $json_array['status'] = "invalid";
                $json_array['msg'] = $client_msg["activate_offer"]["msg_Please_enter_correct_code"];
                $json_array['error_msg'] = $client_msg["activate_offer"]["msg_Please_enter_correct_code"];
                $json = json_encode($json_array);
                echo $json;
                exit;
        }

        $campaign_id = $RS->fields['campaign_id'];
        /* $Sql = "select * from campaign_location where  offers_left>0 and campaign_id = ".$campaign_id." and active=1";

          $RS_actloc = $objDB->Conn->Execute($Sql); */
        $RS_actloc = $objDB->Conn->Execute("select * from campaign_location where  offers_left>? and campaign_id = ? and active=?", array(0, $campaign_id, 1));

        if ($RS_actloc->RecordCount() == 0) {
                $json_array['status'] = "ended";
                $json_array['campaign_end_message'] = $client_msg["activate_offer"]["Msg_offer_left_zero"];
                $json_array['error_msg'] = $client_msg["activate_offer"]["Msg_offer_left_zero"];
                $json = json_encode($json_array);
                echo $json;
                exit;
        }

        /* $Sql = "select * from campaign_location where  offers_left>0 and  campaign_id = ".$campaign_id." and active=1";

          $RS_loc = $objDB->Conn->Execute($Sql); */
        $RS_loc = $objDB->Conn->Execute("select * from campaign_location where  offers_left>? and  campaign_id = ? and active=?", array(0, $campaign_id, 1));

        if ($RS_loc->RecordCount() == -166) {

                $lid = $RS_loc->fields['location_id'];
                /* $sql_o = "select * from campaign_location where campaign_id =".$campaign_id." and location_id =". $lid ." and active=1";
                  $RS_o = $objDB->Conn->Execute($sql_o); */
                $RS_o = $objDB->Conn->Execute("select * from campaign_location where campaign_id =? and location_id =? and active=?", array($campaign_id, $lid, 1));

                /* $Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=".$lid;
                  $RS = $objDB->Conn->Execute($Sql); */
                $RS = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id=?", array($customer_id, $campaign_id, $lid));

                if ($RS_o->RecordCount() > 0) {
                        if ($RS->RecordCount() <= 0) {
                                /* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
                                  $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
                                $RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?", array($campaign_id, $lid));
                                $offers_left = $RS_num_activation->fields['offers_left'];
                                $used_campaign = $RS_num_activation->fields['used_offers'];
                                $share_flag = 1;
                                if ($offers_left != 0) {
                                        /* $Sql_max_is_walkin = "SELECT is_walkin ,level, new_customer  from campaigns WHERE id=".$campaign_id;
                                          $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin); */
                                        $RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin ,level, new_customer  from campaigns WHERE id=?", array($campaign_id));
                                        if ($RS_max_is_walkin->fields['new_customer'] == 1) {
                                                /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
                                                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                                $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($_SESSION['customer_id'], $lid));
                                                if ($subscibed_store_rs->RecordCount() == 0) {
                                                        $share_flag = 1;
                                                } else {
                                                        $share_flag = 0;
                                                }
                                        }

                                        /* check whether new customer for this store */
                                        $allow_for_reserve = 0;
                                        $is_new_user = 0;

                                        /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";

                                          $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
                                        $Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($_SESSION['customer_id'], $lid));

                                        if ($Rs_is_new_customer->RecordCount() == 0) {
                                                $is_new_user = 1;
                                        } else {
                                                $is_new_user = 0;
                                        }


                                        if ($is_new_user == 0) {
                                                /* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;

                                                  $RS_campaign_groups = $objDB->Conn->Execute($sql); */
                                                $RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?", array($campaign_id, $lid));

                                                $c_g_str = "";
                                                $cnt = 1;

                                                $is_it_in_group = 0;
                                                if ($RS_max_is_walkin->fields['level'] == 0) {

                                                        if ($RS_max_is_walkin->fields['is_walkin'] == 0) {
                                                                if ($RS_campaign_groups->RecordCount() > 0) {
                                                                        while ($Row_campaign = $RS_campaign_groups->FetchRow()) {
                                                                                $c_g_str = $Row_campaign['group_id'];
                                                                                if ($cnt != $RS_campaign_groups->RecordCount()) {
                                                                                        $c_g_str .= ",";
                                                                                }
                                                                        }
                                                                        /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";

                                                                          $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
                                                                        $RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?)  )", array($_SESSION['customer_id'], $c_g_str));
                                                                        while ($Row_Check_Cust_group = $RS_check_s->FetchRow()) {
                                                                                /* $query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                                                  $RS_query = $objDB->Conn->Execute($query); */
                                                                                $RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ", array($_SESSION['customer_id'], $Row_Check_Cust_group['group_id'], $c_g_str));

                                                                                if ($RS_query->RecordCount() > 0) {
                                                                                        $is_it_in_group = 1;
                                                                                }
                                                                        }
                                                                        if ($is_it_in_group == 1) {
                                                                                $allow_for_reserve = 1;
                                                                        } else {
                                                                                $allow_for_reserve = 0;
                                                                        }
                                                                } else {
                                                                        $allow_for_reserve = 0;
                                                                }
                                                        } else {
                                                                /* $query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
                                                                  $RS_all_user_group = $objDB->Conn->Execute($query); */
                                                                $RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =? ) ", array($_SESSION['customer_id'], $lid, 1));

                                                                if ($RS_all_user_group->RecordCount() > 0) {
                                                                        $allow_for_reserve = 1;
                                                                } else {
                                                                        $allow_for_reserve = 0;
                                                                }
                                                        }
                                                } else {
                                                        $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='" . $_SESSION['customer_id'] . "' AND group_id in( select  id from merchant_groups where location_id  =" . $lid . "  )";
                                                        $allow_for_reserve = 1;
                                                }
                                        } else {
                                                $allow_for_reserve = 1;
                                        }

                                        /* for checking whether customer in campaign group */

                                        if ($share_flag == 1) {
                                                if ($allow_for_reserve == 1) {
                                                        /* $Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
                                                          customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
                                                          $objDB->Conn->Execute($Sql); */
                                                        $objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status=?, activation_code=?, activation_date= ?, coupon_generation_date=?,customer_id=?, campaign_id=? , location_id=?", array(1, $activation_code, 'Now()', 'Now()', $customer_id, $campaign_id, $lid));
                                                        /* $update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$lid." ";
                                                          $objDB->Conn->Execute($update_num_activation); */
                                                        $objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=? and location_id =?", array(($offers_left - 1), ($used_campaign + 1), $campaign_id, $lid));

                                                        //$RSLocation_nm  = $objDB->Conn->Execute("select * from locations where id =".$lid);
                                                        $RSLocation_nm = $objDB->Conn->Execute("select * from locations where id =?", array($lid));
                                                        //$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$lid;
                                                        $br = $objJSON->generate_voucher_code($customer_id, $activation_code, $campaign_id, $RSLocation_nm->fields['location_name'], $lid);
                                                        $json_array['campaign_id'] = $campaign_id;
                                                        $json_array['location_id'] = $lid;
                                                        /* $select_coupon_code = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";

                                                          $select_rs = $objDB->Conn->Execute($select_coupon_code); */
                                                        $select_rs = $objDB->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?", array($customer_id, $campaign_id, $lid));
                                                        if ($select_rs->RecordCount() <= 0) {
                                                                $array_ = array();
                                                                $array_['customer_id'] = $customer_id;
                                                                $array_['customer_campaign_code'] = $campaign_id;
                                                                $array_['coupon_code'] = $br;
                                                                $array_['active'] = 1;
                                                                $array_['location_id'] = $lid;
                                                                $array_['generated_date'] = date('Y-m-d H:i:s');
                                                                /* $insert_coupon_code = "Insert into coupon_codes set customer_id=".$customer_id." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$lid." , generated_date='".date('Y-m-d H:i:s')."' ";
                                                                  $objDB->Conn->Execute($insert_coupon_code); */
                                                                $objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=? , customer_campaign_code=? , coupon_code=? , active=1 , location_id=? , generated_date=? ", array($customer_id, $campaign_id, $br, $lid, date('Y-m-d H:i:s')));
                                                        }

                                                        //Make entry in subscribed_stre table for first time subscribe to loaction
                                                        /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                                          $RS_group = $objDB->Conn->Execute($sql_group); */
                                                        $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?", array($lid, 1));

                                                        /* $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                                                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                                        $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?", array($_SESSION['customer_id'], $lid));
                                                        if ($subscibed_store_rs->RecordCount() == 0) {
                                                                /* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                                                                  $objDB->Conn->Execute($insert_subscribed_store_sql); */
                                                                $objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=?", array($_SESSION['customer_id'], $lid, date('Y-m-d H:i:s'), 1));
                                                        } else {
                                                                if ($subscibed_store_rs->fields['subscribed_status'] == 0) {
                                                                        /* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                                                                          $objDB->Conn->Execute($up_subscribed_store); */
                                                                        $objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=1  where  customer_id=? and location_id=?", array($_SESSION['customer_id'], $lid));
                                                                }
                                                        }

                                                        /* $RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
                                                          $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe); */
                                                        $check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id =? and private = ?) and user_id = ?", array($lid, 1, $_SESSION['customer_id']));

                                                        if ($check_subscribe->RecordCount() == 0) {
                                                                /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                                                  $RS_group = $objDB->Conn->Execute($sql_group); */
                                                                $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?", array($lid, 1));

                                                                if ($RS_group->RecordCount() > 0) {
                                                                        /* $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;
                                                                          $RS_user_group =$objDB->Conn->Execute($sql_user_group); */
                                                                        $RS_user_group = $objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id =?", array($RS_group->fields['merchant_id'], $RS_group->fields['id'], $customer_id));

                                                                        if ($RS_user_group->RecordCount() <= 0) {
                                                                                /* $insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
                                                                                  $objDB->Conn->Execute($insert_sql); */
                                                                                $objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id = ? , user_id = ?", array($RS_group->fields['merchant_id'], $RS_group->fields['id'], $_SESSION['customer_id']));
                                                                        }
                                                                }
                                                        }
                                                } else {
                                                        $json_array['status'] = "newuser";
                                                        $json_array['campaign_for_new_user'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                                        $json_array['error_msg'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                                        $json = json_encode($json_array);
                                                        echo $json;
                                                        exit;
                                                }
                                        } else {
                                                $json_array['status'] = "newuser";
                                                $json_array['campaign_for_new_user'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                                $json_array['error_msg'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                                $json = json_encode($json_array);
                                                echo $json;
                                                exit;
                                        }
                                } else {

                                        $json_array['status'] = "ended";
                                        $json_array['campaign_end_message'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                        $json_array['error_msg'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                        $json = json_encode($json_array);
                                        echo $json;
                                        exit;
                                }
                        } else {
                                if ($RS->fields['activation_status'] == 0) {
                                        /* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
                                          $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
                                        $RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?", array($campaign_id, $lid));
                                        $offers_left = $RS_num_activation->fields['offers_left'];
                                        $used_campaign = $RS_num_activation->fields['used_offers'];
                                        $share_flag = 1;
                                        if ($offers_left != 0) {
                                                /* $Sql_max_is_walkin = "SELECT is_walkin ,level, new_customer  from campaigns WHERE id=".$campaign_id;
                                                  $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin); */
                                                $RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin ,level, new_customer  from campaigns WHERE id=?", array($campaign_id));
                                                if ($RS_max_is_walkin->fields['new_customer'] == 1) {
                                                        /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";
                                                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                                        $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($_SESSION['customer_id'], $lid));

                                                        if ($subscibed_store_rs->RecordCount() == 0) {
                                                                $share_flag = 1;
                                                        } else {
                                                                $share_flag = 0;
                                                        }
                                                }
                                                /* check whether new customer for this store */
                                                $allow_for_reserve = 0;
                                                $is_new_user = 0;

                                                /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$_SESSION['customer_id']." and location_id=".$lid.") ";             
                                                  $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
                                                $Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($_SESSION['customer_id'], $lid));
                                                if ($Rs_is_new_customer->RecordCount() == 0) {
                                                        $is_new_user = 1;
                                                } else {
                                                        $is_new_user = 0;
                                                }

                                                if ($is_new_user == 0) {

                                                        /* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;

                                                          $RS_campaign_groups = $objDB->Conn->Execute($sql); */
                                                        $RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?", array($campaign_id, $lid));

                                                        $c_g_str = "";
                                                        $cnt = 1;

                                                        $is_it_in_group = 0;
                                                        if ($RS_max_is_walkin->fields['level'] == 0) {
                                                                if ($RS_max_is_walkin->fields['is_walkin'] == 0) {
                                                                        if ($RS_campaign_groups->RecordCount() > 0) {
                                                                                while ($Row_campaign = $RS_campaign_groups->FetchRow()) {
                                                                                        $c_g_str = $Row_campaign['group_id'];
                                                                                        if ($cnt != $RS_campaign_groups->RecordCount()) {
                                                                                                $c_g_str .= ",";
                                                                                        }
                                                                                }
                                                                                /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";

                                                                                  $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
                                                                                $RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?)  )", array($_SESSION['customer_id'], $c_g_str));

                                                                                while ($Row_Check_Cust_group = $RS_check_s->FetchRow()) {
                                                                                        /* $query = "Select * from merchant_subscribs where  user_id='".$_SESSION['customer_id']."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                                                          $RS_query = $objDB->Conn->Execute($query); */
                                                                                        $RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ", array($_SESSION['customer_id'], $Row_Check_Cust_group['group_id'], $c_g_str));

                                                                                        if ($RS_query->RecordCount() > 0) {
                                                                                                $is_it_in_group = 1;
                                                                                        }
                                                                                }
                                                                                if ($is_it_in_group == 1) {
                                                                                        $allow_for_reserve = 1;
                                                                                } else {
                                                                                        $allow_for_reserve = 0;
                                                                                }
                                                                        } else {
                                                                                $allow_for_reserve = 0;
                                                                        }
                                                                } else {
                                                                        /* $query = "Select * from merchant_subscribs where  user_id=".$_SESSION['customer_id']." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
                                                                          $RS_all_user_group = $objDB->Conn->Execute($query); */
                                                                        $RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =?) ", array($_SESSION['customer_id'], $lid, 1));
                                                                        if ($RS_all_user_group->RecordCount() > 0) {
                                                                                $allow_for_reserve = 1;
                                                                        } else {
                                                                                $allow_for_reserve = 0;
                                                                        }
                                                                }
                                                        } else {
                                                                $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='" . $_SESSION['customer_id'] . "' AND group_id in( select  id from merchant_groups where location_id  =" . $lid . "  )";
                                                                $allow_for_reserve = 1;
                                                        }
                                                } else {
                                                        $allow_for_reserve = 1;
                                                }

                                                /* for checking whether customer in campaign group */

                                                if ($share_flag == 1) {
                                                        if ($allow_for_reserve == 1) {
                                                                /* $Sql = "Update customer_campaigns SET activation_status=1 where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$lid;                      
                                                                  $objDB->Conn->Execute($Sql); */
                                                                $objDBWrt->Conn->Execute("Update customer_campaigns SET activation_status=? where customer_id=? and campaign_id=? and location_id=?", array(1, $customer_id, $campaign_id, $lid));
                                                                $json_array['campaign_id'] = $campaign_id;
                                                                $json_array['location_id'] = $lid;

                                                                /* $select_coupon_code = "update coupon_codes set active= 1 where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
                                                                  $objDB->Conn->Execute($select_coupon_code); */
                                                                $objDBWrt->Conn->Execute("update coupon_codes set active=? where customer_id=? and customer_campaign_code=? and location_id=?", array(1, $customer_id, $campaign_id, $lid));

                                                                //Make entry in subscribed_stre table for first time subscribe to loaction
                                                                /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                                                  $RS_group = $objDB->Conn->Execute($sql_group); */
                                                                $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?", array($lid, 1));

                                                                /* $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                                                                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                                                $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?", array($_SESSION['customer_id'], $lid));

                                                                if ($subscibed_store_rs->RecordCount() == 0) {
                                                                        /* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$lid." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                                                                          $objDB->Conn->Execute($insert_subscribed_store_sql); */
                                                                        $objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=?", array($_SESSION['customer_id'], $lid, date('Y-m-d H:i:s'), 1));
                                                                } else {
                                                                        if ($subscibed_store_rs->fields['subscribed_status'] == 0) {
                                                                                /* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$lid;
                                                                                  $objDB->Conn->Execute($up_subscribed_store); */
                                                                                $objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=? where  customer_id=? and location_id=?", array(1, $_SESSION['customer_id'], $lid));
                                                                        }
                                                                }
                                                                /* $RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$_SESSION['customer_id'];
                                                                  $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe); */
                                                                $check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id =? and private =?) and user_id = ?", array($lid, 1, $_SESSION['customer_id']));
                                                                if ($check_subscribe->RecordCount() == 0) {
                                                                        /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                                                          $RS_group = $objDB->Conn->Execute($sql_group); */
                                                                        $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?", array($lid, 1));

                                                                        if ($RS_group->RecordCount() > 0) {
                                                                                /* $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;
                                                                                  $RS_user_group =$objDB->Conn->Execute($sql_user_group); */
                                                                                $RS_user_group = $objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id = ?", array($RS_group->fields['merchant_id'], $RS_group->fields['id'], $customer_id));
                                                                                if ($RS_user_group->RecordCount() <= 0) {
                                                                                        /* $insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$_SESSION['customer_id'];
                                                                                          $objDB->Conn->Execute($insert_sql); */
                                                                                        $objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id =? , user_id = ?", array($RS_group->fields['merchant_id'], $RS_group->fields['id'], $_SESSION['customer_id']));
                                                                                }
                                                                        }
                                                                }
                                                        } else {
                                                                $json_array['status'] = "newuser";
                                                                $json_array['campaign_for_new_user'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                                                $json_array['error_msg'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                                                $json = json_encode($json_array);
                                                                echo $json;
                                                                exit;
                                                        }
                                                } else {
                                                        $json_array['status'] = "newuser";
                                                        $json_array['campaign_for_new_user'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                                        $json_array['error_msg'] = $client_msg["activate_offer"]["Msg_This_Offer_Limited_Customers"];
                                                        $json = json_encode($json_array);
                                                        echo $json;
                                                        exit;
                                                }
                                        } else {
                                                $json_array['status'] = "ended";
                                                $json_array['campaign_end_message'] = $client_msg["activate_offer"]["Msg_offer_left_zero"];
                                                $json_array['error_msg'] = $client_msg["activate_offer"]["Msg_offer_left_zero"];
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
                } else {
                        $json_array['status'] = "ended";
                        $json_array['campaign_end_message'] = $client_msg["activate_offer"]["Msg_offer_left_zero"];
                        $json_array['error_msg'] = $client_msg["activate_offer"]["Msg_offer_left_zero"];
                        $json = json_encode($json_array);
                        echo $json;
                        exit;
                }
        } else {
                /* $sql  = "SELECT * from campaign_location WHERE offers_left>0 and campaign_id = ".$campaign_id;
                  $RSdata = $objDB->Conn->Execute($sql); */
                $RSdata = $objDB->Conn->Execute("SELECT * from campaign_location WHERE offers_left>? and campaign_id = ?", array(0, $campaign_id));
                if ($RSdata->RecordCount() <= 0) {
                        $json_array['status'] = "false";
                        $json = json_encode($json_array);
                        echo $json;
                        exit;
                } else {
                        $count = 0;
                        $records = array();
                        $total = $RSdata->RecordCount();
                        while ($Row_u = $RSdata->FetchRow()) {
                                /* $Sql_s = "SELECT * from locations WHERE id=".$Row_u['location_id'];

                                  $RS_loc = $objDB->Conn->Execute($Sql_s); */
                                $RS_loc = $objDB->Conn->Execute("SELECT * from locations WHERE id=?", array($Row_u['location_id']));
                                $RS_loc = $RS_loc->FetchRow();

                                $storeid = $RS_loc['id'];

                                $busines_name = "";
                                $arr = file(WEB_PATH . '/includes/customer/process_mobile.php?getlocationbusinessname=yes&l_id=' . $storeid);
                                if (trim($arr[0]) == "") {
                                        unset($arr[0]);
                                        $arr = array_values($arr);
                                }
                                $json = json_decode($arr[0]);
                                $busines_name = $json->bus_name;
                                $json_array['business'] = $busines_name;



                                /*
                                  $records[$count]["latitude"] = $RS_loc->fields['latitude'];
                                  $records[$count]["longitude"] = $RS_loc->fields['longitude'];
                                 */
                                /*
                                  $tmp_loc=array();
                                  $tmp_loc["lati"]=$RS_loc->fields['latitude'];
                                  $tmp_loc["long"]=$RS_loc->fields['longitude'];
                                  $records[$count]["location"]=$tmp_loc;
                                 */

                                $tmp_loc = array();

                                $from_lati1 = $_REQUEST['mycurrent_lati'];

                                $from_long1 = $_REQUEST['mycurrent_long'];

                                $to_lati1 = $RS_loc['latitude'];

                                $to_long1 = $RS_loc['longitude'];

                                $deal_distance = $objJSON->distance($from_lati1, $from_long1, $to_lati1, $to_long1, "M") . "Mi";

                                if ($deal_distance <= 20) {
                                        $records[$count] = get_field_value($Row_u);
                                        $records[$count]["location"] = get_field_value($RS_loc);
                                        $records[$count]["location"]["miles_away"] = $deal_distance;
                                        $count++;
                                }
                        }
                }

                if ($count <= 0) {
                        $json_array['status'] = "ended";
                        $json_array['campaign_end_message'] = "No offer available in 20 miles from your location";
                        $json_array['error_msg'] = "No offer available in 20 miles from your location";
                        $json = json_encode($json_array);
                        echo $json;
                        exit;
                } else {
                        $json_array['status'] = "true";
                        $json_array["records"] = $records;
                        $json_array["total_records"] = $count;
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        }
}

if (isset($_REQUEST['activate_new_deal_mobile'])) {
        $json = $objJSON->activate_new_deal_mobile($_REQUEST['cust_id'], $_REQUEST['activation_code'], 0, $_REQUEST['loc_id']);
        echo $json;
        exit();
}
//method name= facebook_login
//email ,user_profile_pic(user profile pic url) ,first_name ,last_name ,dob_year ,dob_month ,dob_day


if (isset($_REQUEST['facebook_login'])) {
        if ($_REQUEST['facebook_user_id'] != "") {
                $where_clause = $array_values = array();
                $array = $json_array = $where_clause = array();

                $where_clause['facebook_user_id'] = $_REQUEST['facebook_user_id'];

                $RS = $objDB->Show("customer_user", $where_clause);
                if ($RS->RecordCount() > 0) {
                        $Row = $RS->FetchRow();
                        //$_SESSION['customer_id'] = $Row['id'];
                        //$_SESSION['customer_info'] = $Row;
                        if (isset($_REQUEST['email'])) {
                                $array_values['facebook_email_id'] = $_REQUEST['email'];
                        }
                        if (isset($_REQUEST['first_name'])) {
                                $array_values['firstname'] = $_REQUEST['first_name'];
                        }
                        if (isset($_REQUEST['last_name'])) {
                                $array_values['lastname'] = $_REQUEST['last_name'];
                        }
                        if (isset($_REQUEST['dob_year'])) {
                                $array_values['dob_year'] = $_REQUEST['dob_year'];
                        }
                        if (isset($_REQUEST['dob_month'])) {
                                $array_values['dob_month'] = $_REQUEST['dob_month'];
                        }
                        if (isset($_REQUEST['dob_day'])) {
                                $array_values['dob_day'] = $_REQUEST['dob_day'];
                        }

                        if ($Row['profile_pic'] == "") {
                                if (isset($_REQUEST['user_profile_pic'])) {

                                        $array_values['profile_pic'] = $_REQUEST['user_profile_pic'];

                                        /*
                                          $pic_var = explode("/", $array_values['profile_pic']);
                                          if($pic_var[2] == "graph.facebook.com" || $pic_var[2] == "fbcdn-profile-a.akamaihd.net")
                                          {
                                          if($pic_var[2]=="graph.facebook.com")
                                          {
                                          $fb_img_json= file_get_contents($array_values['profile_pic']."?type=large&redirect=false");
                                          $fb_img_json= json_decode($fb_img_json,true);

                                          $array_values['profile_pic']=$fb_img_json['data']['url'];
                                          }
                                          }
                                         */

                                        $array_values['profile_pic'] = "https://graph.facebook.com/" . $_REQUEST['facebook_user_id'] . "/picture";

                                        $image_path_user = UPLOAD_IMG . "/c/usr_pic/";
                                        $image_path_user1 = UPLOAD_IMG . "/c/usr_pass_pic/";
                                        $image_path_user2 = UPLOAD_IMG . "/c/usr_pass_pic/big/";

                                        $name = "usr_" . $_REQUEST['facebook_user_id'] . ".png";

                                        $fb_img_json = file_get_contents($array_values['profile_pic']);

                                        $fp = fopen($image_path_user . $name, 'w+');
                                        fputs($fp, $fb_img_json);
                                        $fp1 = fopen($image_path_user1 . $name, 'w+');
                                        fputs($fp1, $fb_img_json);
                                        $fp2 = fopen($image_path_user2 . $name, 'w+');
                                        fputs($fp2, $fb_img_json);




                                        $array_values['profile_pic'] = $name;
                                }
                        }

                        if (isset($_REQUEST['device_id'])) {
                                $array_values['device_id'] = $_REQUEST['device_id'];
                        }

                        $array_values['lastvisit_date'] = date("Y-m-d H:i:s");
                        $where_clause['id'] = $Row['id'];
                        $objDB->Update($array_values, "customer_user", $where_clause);

                        $json_array = array();

                        $json_array['status'] = "true";
                        $json_array['customer_id'] = $Row['id'];

                        /* $cust_sql = 'select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>0 and  country <>""  and id='.$Row['id'];//.$customer_id;
                          $RS_cust_data=$objDB->Conn->Execute($cust_sql); */
                        $RS_cust_data = $objDB->Conn->Execute('select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>? and  country <>""  and id=?', array(0, $Row['id']));
                        $is_profileset = $RS_cust_data->RecordCount();

                        $json_array['is_profileset'] = $is_profileset;


                        $where_clause = array();
                        $where_clause['facebook_user_id'] = $_REQUEST['facebook_user_id'];
                        $RS = $objDB->Show("customer_user", $where_clause);
                        $Row = $RS->FetchRow();
                        /*
                          echo "<pre>";
                          print_r($Row);
                          echo "</pre>";
                         */
                        $json_array['customer_info'] = get_field_value($Row);

                        //$json_array = array();


                        $pos = strpos($json_array['customer_info']['profile_pic'], 'http');
                        if ($pos === false) {

                                if ($json_array['customer_info']['profile_pic'] != "") {
                                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . "/c/usr_pic/" . $json_array['customer_info']['profile_pic'];
                                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                                } else {
                                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . '/c/default_small_user.jpg';
                                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                                }
                        } else {

                                $json_array['customer_info']['profile_pic'] = $json_array['customer_info']['profile_pic'];
                                $pic_var = explode("/", $json_array['customer_info']['profile_pic']);
                                if ($pic_var[2] == "graph.facebook.com" || $pic_var[2] == "fbcdn-profile-a.akamaihd.net") {
                                        $json_array['customer_info']['facebook_profile_pic'] = 1;

                                        if ($pic_var[2] == "graph.facebook.com") {
                                                $fb_img_json = file_get_contents($json_array['customer_info']['profile_pic'] . "?type=large&redirect=false");
                                                $fb_img_json = json_decode($fb_img_json, true);

                                                $json_array['customer_info']['profile_pic'] = $fb_img_json['data']['url'];

                                                $array_values = $where_clause = array();
                                                $array_values['profile_pic'] = $fb_img_json['data']['url'];
                                                $where_clause['facebook_user_id'] = $_REQUEST['facebook_user_id'];
                                                $objDB->Update($array_values, "customer_user", $where_clause);
                                        }
                                }
                        }
                        if ($json_array['customer_info']['card_id'] != "") {
                                $json_array['customer_info']['card_qrcode_url'] = WEB_PATH . "/merchant/demopdf/demo_qrcode_card.php?size=200&card_id=" . $json_array['customer_info']['card_id'];
                        }
                        $json_array['message'] = "Profile Updated";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();

                        //$_SESSION['facebook_usr_login'] = 1;
                } else {
                        //$_SESSION['facebook_usr_login'] = 1;
                        $array_values['facebook_user_id'] = $_REQUEST['facebook_user_id'];
                        $array_values['access_token'] = $_REQUEST['access_token'];
                        $array_values['emailaddress'] = $_REQUEST['email'];
                        $array_values['facebook_email_id'] = $_REQUEST['email'];
                        $array_values['firstname'] = $_REQUEST['first_name'];
                        $array_values['lastname'] = $_REQUEST['last_name'];
                        if (isset($_REQUEST['dob_year'])) {
                                $array_values['dob_year'] = $_REQUEST['dob_year'];
                        }
                        if (isset($_REQUEST['dob_month'])) {
                                $array_values['dob_month'] = $_REQUEST['dob_month'];
                        }
                        if (isset($_REQUEST['dob_day'])) {
                                $array_values['dob_day'] = $_REQUEST['dob_day'];
                        }
                        $array_values['registered_date'] = date("Y-m-d H:i:s");
                        $array_values['lastvisit_date'] = date("Y-m-d H:i:s");
                        $array_values['emailnotification'] = 1;
                        $array_values['active'] = 1;
                        $array_values['notification_setting'] = 1;
                        $array_values['profile_pic'] = $_REQUEST['user_profile_pic'];

                        /*
                          $pic_var = explode("/", $array_values['profile_pic']);
                          if($pic_var[2] == "graph.facebook.com" || $pic_var[2] == "fbcdn-profile-a.akamaihd.net")
                          {
                          if($pic_var[2]=="graph.facebook.com")
                          {
                          $fb_img_json= file_get_contents($array_values['profile_pic']."?type=large&redirect=false");
                          $fb_img_json= json_decode($fb_img_json,true);

                          $array_values['profile_pic']=$fb_img_json['data']['url'];
                          }
                          }
                         */

                        $array_values['profile_pic'] = "https://graph.facebook.com/" . $_REQUEST['facebook_user_id'] . "/picture";

                        $image_path_user = UPLOAD_IMG . "/c/usr_pic/";
                        $image_path_user1 = UPLOAD_IMG . "/c/usr_pass_pic/";
                        $image_path_user2 = UPLOAD_IMG . "/c/usr_pass_pic/big/";

                        $name = "usr_" . $_REQUEST['facebook_user_id'] . ".png";

                        $fb_img_json = file_get_contents($array_values['profile_pic']);

                        $fp = fopen($image_path_user . $name, 'w+');
                        fputs($fp, $fb_img_json);
                        $fp1 = fopen($image_path_user1 . $name, 'w+');
                        fputs($fp1, $fb_img_json);
                        $fp2 = fopen($image_path_user2 . $name, 'w+');
                        fputs($fp2, $fb_img_json);

                        $array_values['profile_pic'] = $name;

                        if (isset($_REQUEST['device_id'])) {
                                $array_values['device_id'] = $_REQUEST['device_id'];
                        }

                        $objDB->Insert($array_values, "customer_user");

                        $where_clause['facebook_user_id'] = $_REQUEST['facebook_user_id'];
                        $RS = $objDB->Show("customer_user", $where_clause);
                        $Row = $RS->FetchRow();
                        //$_SESSION['customer_id'] = $Row['id'];
                        //$_SESSION['customer_info'] = $Row;
                        $json_array = array();
                        $json_array['status'] = "true";
                        $json_array['message'] = "Successfully Login.";
                        $json_array['customer_id'] = $Row['id'];
                        $json_array['customer_info'] = get_field_value($Row);

                        /* $cust_sql = 'select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>0 and  country <>""  and id='.$Row['id'];//.$customer_id;
                          $RS_cust_data=$objDB->Conn->Execute($cust_sql); */
                        $RS_cust_data = $objDB->Conn->Execute('select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>? and  country <>""  and id=?', array(0, $Row['id']));

                        $is_profileset = $RS_cust_data->RecordCount();

                        $json_array['is_profileset'] = $is_profileset;

                        $pos = strpos($json_array['customer_info']['profile_pic'], 'http');
                        if ($pos === false) {
                                if ($json_array['customer_info']['profile_pic'] != "") {
                                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . "/c/usr_pic/" . $json_array['customer_info']['profile_pic'];
                                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                                } else {
                                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . '/c/default_small_user.jpg';
                                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                                }
                        } else {
                                $json_array['customer_info']['profile_pic'] = $json_array['customer_info']['profile_pic'];
                                $pic_var = explode("/", $json_array['customer_info']['profile_pic']);
                                if ($pic_var[2] == "graph.facebook.com" || $pic_var[2] == "fbcdn-profile-a.akamaihd.net") {
                                        $json_array['customer_info']['facebook_profile_pic'] = 1;

                                        if ($pic_var[2] == "graph.facebook.com") {
                                                $fb_img_json = file_get_contents($json_array['customer_info']['profile_pic'] . "?type=large&redirect=false");
                                                $fb_img_json = json_decode($fb_img_json, true);

                                                $json_array['customer_info']['profile_pic'] = $fb_img_json['data']['url'];

                                                $array_values = $where_clause = array();
                                                $array_values['profile_pic'] = $fb_img_json['data']['url'];
                                                $where_clause['facebook_user_id'] = $_REQUEST['facebook_user_id'];
                                                $objDB->Update($array_values, "customer_user", $where_clause);
                                        }
                                }
                        }
                        if ($json_array['customer_info']['card_id'] != "") {
                                $json_array['customer_info']['card_qrcode_url'] = WEB_PATH . "/merchant/demopdf/demo_qrcode_card.php?size=200&card_id=" . $json_array['customer_info']['card_id'];
                        }
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        } else {
                $json_array = array();
                $json_array['status'] = "false";
                $json_array['message'] = "Please enter valid facebook user id";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

/**
 * @uses social user registration
 * @param facebook_user_id or google_user_id,emailaddress,password,firstname,lastname,country,postalcode,gender,dob_month
 * @return string
 */
if (isset($_REQUEST['btnRegister'])) {

        $array = $json_array = array();
        if (isset($_REQUEST['facebook_user_id'])) {
                $where_clause1 = array();
                $where_clause1['facebook_user_id'] = $_REQUEST['facebook_user_id'];
                $RS1 = $objDB->Show("customer_user", $where_clause1);
                if ($RS1->RecordCount() > 0) {
                        $json_array['status'] = "false";
                        $json_array['message'] = $client_msg["login_register"]["Msg_facebook_user_Already"];
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        }
        if (isset($_REQUEST['google_user_id'])) {
                $where_clause1 = array();
                $where_clause1['google_user_id'] = $_REQUEST['google_user_id'];
                $RS1 = $objDB->Show("customer_user", $where_clause1);
                if ($RS1->RecordCount() > 0) {
                        $json_array['status'] = "false";
                        $json_array['message'] = $client_msg["login_register"]["Msg_google_user_Already"];
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        }


        $array['emailaddress'] = $_REQUEST['emailaddress'];
        $array['is_registered'] = 1;
        $RS = $objDB->Show("customer_user", $array);

        if ($RS->RecordCount() > 0) {
                $json_array['status'] = "false";
                $json_array['message'] = $client_msg["login_register"]["Msg_Email_Already"];
                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {
                $array_chk = array();
                $array_chk['emailaddress'] = $_REQUEST['emailaddress'];
                //echo "<pre>";print_r($_REQUEST);echo "</pre>";
                $RS_check = $objDB->Show("customer_user", $array_chk);
                if ($RS_check->RecordCount() > 0) {
                        $PasswordLib = new \PasswordLib\PasswordLib;
                        //$hash = $PasswordLib->createPasswordHash($password);
                        $array['password'] = $PasswordLib->createPasswordHash($_REQUEST['password']);
                        //$array['registered_date'] = date("Y-m-d H:i:s");
                        $array['emailnotification'] = 1;
                        $array['active'] = 1;
                        $array['notification_setting'] = 1;
                        $array['is_registered'] = 1;
                        $array['firstname'] = $_REQUEST['firstname'];
                        $array['lastname'] = $_REQUEST['lastname'];
                        $array['country'] = $_REQUEST['country'];
                        $postalcode = str_replace(" ", "", $_REQUEST['postalcode']);
                        $array['postalcode'] = strtoupper($postalcode);
                        $array['gender'] = $_REQUEST['gender'];
                        $array['dob_month'] = $_REQUEST['dob_month'];
                        $array['dob_day'] = $_REQUEST['dob_day'];
                        $array['dob_year'] = $_REQUEST['dob_year'];
                        $string_address = $_REQUEST['country'] . "," . $_REQUEST['postalcode'];
                        $string_address = urlencode($string_address);
                        $geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $string_address . "&sensor=false");
                        $geojson = json_decode($geocode, true);
                        if ($geojson['status'] == 'OK') {
                                $array['curr_latitude'] = $geojson['results'][0]['geometry']['location']['lat'];
                                $array['curr_longitude'] = $geojson['results'][0]['geometry']['location']['lng'];
                                $timezone1 = getClosestTimezone($array['curr_latitude'], $array['curr_longitude']);
                                $timezone = new DateTimeZone($timezone1);
                                $offset1 = $timezone->getOffset(new DateTime);
                                // //timezone_offset_string( $offset1 );
                                $tz = timezone_offset_string($offset1);
                                $array['curr_timezone'] = $tz;
                        } else {
                                $array['curr_latitude'] = "";
                                $array['curr_longitude'] = "";
                                $array['curr_timezone'] = "";
                        }
                        if (isset($_REQUEST['facebook_user_id'])) {
                                $array['facebook_user_id'] = $_REQUEST['facebook_user_id'];
                                if (isset($_REQUEST['emailaddress'])) {
                                        $array['facebook_email_id'] = $_REQUEST['emailaddress'];
                                }
                        }
                        if (isset($_REQUEST['access_token'])) {
                                $array['access_token'] = $_REQUEST['access_token'];
                        }
                        if (isset($_REQUEST['user_profile_pic'])) {
                                $array['profile_pic'] = $_REQUEST['user_profile_pic'];

                                if (isset($_REQUEST['facebook_user_id'])) {
                                        $array['profile_pic'] = "https://graph.facebook.com/" . $_REQUEST['facebook_user_id'] . "/picture";

                                        $image_path_user = UPLOAD_IMG . "/c/usr_pic/";
                                        $image_path_user1 = UPLOAD_IMG . "/c/usr_pass_pic/";
                                        $image_path_user2 = UPLOAD_IMG . "/c/usr_pass_pic/big/";

                                        $name = "usr_" . $_REQUEST['facebook_user_id'] . ".png";

                                        $fb_img_json = file_get_contents($array['profile_pic']);

                                        $fp = fopen($image_path_user . $name, 'w+');
                                        fputs($fp, $fb_img_json);
                                        $fp1 = fopen($image_path_user1 . $name, 'w+');
                                        fputs($fp1, $fb_img_json);
                                        $fp2 = fopen($image_path_user2 . $name, 'w+');
                                        fputs($fp2, $fb_img_json);

                                        $array['profile_pic'] = $name;
                                }
                                if (isset($_REQUEST['google_user_id'])) {
                                        $image_path_user = UPLOAD_IMG . "/c/usr_pic/";
                                        $image_path_user1 = UPLOAD_IMG . "/c/usr_pass_pic/";
                                        $image_path_user2 = UPLOAD_IMG . "/c/usr_pass_pic/big/";

                                        $name = "usr_" . $_REQUEST['google_user_id'] . ".png";

                                        $fb_img_json = file_get_contents($array['profile_pic']);

                                        $fp = fopen($image_path_user . $name, 'w+');
                                        fputs($fp, $fb_img_json);
                                        $fp1 = fopen($image_path_user1 . $name, 'w+');
                                        fputs($fp1, $fb_img_json);
                                        $fp2 = fopen($image_path_user2 . $name, 'w+');
                                        fputs($fp2, $fb_img_json);

                                        $array['profile_pic'] = $name;
                                }
                        }

                        if (isset($_REQUEST['google_user_id'])) {
                                $array['google_user_id'] = $_REQUEST['google_user_id'];
                                if (isset($_REQUEST['emailaddress'])) {
                                        $array['google_email_id'] = $_REQUEST['emailaddress'];
                                }
                        }
                        if (isset($_REQUEST['google_access_token'])) {
                                $array['google_access_token'] = $_REQUEST['google_access_token'];
                        }
                        if (isset($_REQUEST['emailaddress'])) {
                                $array['emailaddress'] = $_REQUEST['emailaddress'];
                        }
                        if (isset($_REQUEST['device_id'])) {
                                $array['device_id'] = $_REQUEST['device_id'];
                        }
                        $where_clause['id'] = $RS_check->fields['id'];
                        $objDB->Update($array, "customer_user", $where_clause);
                        $c_id = $RS_check->fields['id'];

                        // insert default email settings into customer_campaign_settings
                        $array_settings = array();
                        $array_settings['campaign_email'] = 1;
                        $array_settings['subscribe_merchant_new_campaign'] = 1;
                        $array_settings['subscribe_merchant_reserve_campaign'] = 1;
                        $array_settings['customer_id'] = $c_id;
                        $array_settings['merchant_radius'] = 1;

                        $c_id = $objDB->Insert($array_settings, "customer_email_settings");
                } else {
                        $PasswordLib = new \PasswordLib\PasswordLib;
                        //$hash = $PasswordLib->createPasswordHash($password);
                        $array['password'] = $PasswordLib->createPasswordHash($_REQUEST['password']);
                        $array['registered_date'] = date("Y-m-d H:i:s");
                        $array['emailnotification'] = 1;
                        $array['active'] = 1;
                        $array['notification_setting'] = 1;
                        $array['is_registered'] = 1;
                        $array['firstname'] = $_REQUEST['firstname'];
                        $array['lastname'] = $_REQUEST['lastname'];
                        $array['country'] = $_REQUEST['country'];
                        $postalcode = str_replace(" ", "", $_REQUEST['postalcode']);
                        $array['postalcode'] = strtoupper($postalcode);
                        $array['gender'] = $_REQUEST['gender'];
                        $array['dob_month'] = $_REQUEST['dob_month'];
                        $array['dob_day'] = $_REQUEST['dob_day'];
                        $array['dob_year'] = $_REQUEST['dob_year'];
                        $string_address = $_REQUEST['country'] . "," . $_REQUEST['postalcode'];
                        $string_address = urlencode($string_address);
                        $geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $string_address . "&sensor=false");
                        $geojson = json_decode($geocode, true);
                        if ($geojson['status'] == 'OK') {
                                $array['curr_latitude'] = $geojson['results'][0]['geometry']['location']['lat'];
                                $array['curr_longitude'] = $geojson['results'][0]['geometry']['location']['lng'];
                                $timezone1 = getClosestTimezone($array['curr_latitude'], $array['curr_longitude']);
                                $timezone = new DateTimeZone($timezone1);
                                $offset1 = $timezone->getOffset(new DateTime);
                                // //timezone_offset_string( $offset1 );
                                $tz = timezone_offset_string($offset1);
                                $array['curr_timezone'] = $tz;
                        } else {
                                $array['curr_latitude'] = "";
                                $array['curr_longitude'] = "";
                                $array['curr_timezone'] = "";
                        }
                        if (isset($_REQUEST['facebook_user_id'])) {
                                $array['facebook_user_id'] = $_REQUEST['facebook_user_id'];
                                if (isset($_REQUEST['emailaddress'])) {
                                        $array['facebook_email_id'] = $_REQUEST['emailaddress'];
                                }
                        }
                        if (isset($_REQUEST['access_token'])) {
                                $array['access_token'] = $_REQUEST['access_token'];
                        }
                        if (isset($_REQUEST['user_profile_pic'])) {
                                $array['profile_pic'] = $_REQUEST['user_profile_pic'];

                                if (isset($_REQUEST['facebook_user_id'])) {
                                        $array['profile_pic'] = "https://graph.facebook.com/" . $_REQUEST['facebook_user_id'] . "/picture";

                                        $image_path_user = UPLOAD_IMG . "/c/usr_pic/";
                                        $image_path_user1 = UPLOAD_IMG . "/c/usr_pass_pic/";
                                        $image_path_user2 = UPLOAD_IMG . "/c/usr_pass_pic/big/";

                                        $name = "usr_" . $_REQUEST['facebook_user_id'] . ".png";

                                        $fb_img_json = file_get_contents($array['profile_pic']);

                                        $fp = fopen($image_path_user . $name, 'w+');
                                        fputs($fp, $fb_img_json);
                                        $fp1 = fopen($image_path_user1 . $name, 'w+');
                                        fputs($fp1, $fb_img_json);
                                        $fp2 = fopen($image_path_user2 . $name, 'w+');
                                        fputs($fp2, $fb_img_json);

                                        $array['profile_pic'] = $name;
                                }
                                if (isset($_REQUEST['google_user_id'])) {
                                        $image_path_user = UPLOAD_IMG . "/c/usr_pic/";
                                        $image_path_user1 = UPLOAD_IMG . "/c/usr_pass_pic/";
                                        $image_path_user2 = UPLOAD_IMG . "/c/usr_pass_pic/big/";

                                        $name = "usr_" . $_REQUEST['google_user_id'] . ".png";

                                        $fb_img_json = file_get_contents($array['profile_pic']);

                                        $fp = fopen($image_path_user . $name, 'w+');
                                        fputs($fp, $fb_img_json);
                                        $fp1 = fopen($image_path_user1 . $name, 'w+');
                                        fputs($fp1, $fb_img_json);
                                        $fp2 = fopen($image_path_user2 . $name, 'w+');
                                        fputs($fp2, $fb_img_json);

                                        $array['profile_pic'] = $name;
                                }
                        }

                        if (isset($_REQUEST['google_user_id'])) {
                                $array['google_user_id'] = $_REQUEST['google_user_id'];
                                if (isset($_REQUEST['emailaddress'])) {
                                        $array['google_email_id'] = $_REQUEST['emailaddress'];
                                }
                        }
                        if (isset($_REQUEST['google_access_token'])) {
                                $array['google_access_token'] = $_REQUEST['google_access_token'];
                        }
                        if (isset($_REQUEST['emailaddress'])) {
                                $array['emailaddress'] = $_REQUEST['emailaddress'];
                        }

                        if (isset($_REQUEST['device_id'])) {
                                $array['device_id'] = $_REQUEST['device_id'];
                        }

                        $c_id = $objDB->Insert($array, "customer_user");

                        // insert default email settings into customer_campaign_settings
                        $array_settings = array();
                        $array_settings['campaign_email'] = 1;
                        $array_settings['subscribe_merchant_new_campaign'] = 1;
                        $array_settings['subscribe_merchant_reserve_campaign'] = 1;
                        $array_settings['customer_id'] = $c_id;
                        $array_settings['merchant_radius'] = 1;

                        $c_id = $objDB->Insert($array_settings, "customer_email_settings");
                }
        }


        $admin_settings = array();
        $admin_settings['setting'] = "User Activation";
        $RSAdmin = $objDB->Show("admin_settings", $admin_settings);

        if ($RSAdmin->fields['action'] == 1) {
                $array = array();
                $array['emailaddress'] = $_REQUEST['emailaddress'];
                $RS = $objDB->Show("customer_user", $array);
                $Row = $RS->FetchRow();
                $update_sql = "Update  customer_user set active=0 where id= " . $Row['id'];

                $objDB->Conn->Execute($update_sql);
                $activation_code = base64_encode($array['emailaddress']);

                $activate_link = WEB_PATH . "/activate.php?id=" . $activation_code;

                $mail = new PHPMailer();
                $body = "<p>Your account has been created successfully </p>";
                $body .= "<p>Please  <a href='" . $activate_link . "'>Click Here</a> to activate our account</p>Thank you,<br/>Scanflip Support Team.";
                $mail->AddReplyTo('no-reply@scanflip.com', 'ScanFlip Support');
                $mail->AddAddress($array['emailaddress']);
                $mail->From = "no-reply@scanflip.com";
                $mail->FromName = "ScanFlip Support";
                $mail->Subject = "Activate Your Account";
                $mail->MsgHTML($body);
                $mail->Send();
                $json_array['action'] = "1";
        } else {
                $array = array();
                $array['emailaddress'] = $_REQUEST['emailaddress'];
                $RS = $objDB->Show("customer_user", $array);
                $Row = $RS->FetchRow();
                $_SESSION['customer_id'] = $Row['id'];
                $_SESSION['customer_info'] = $Row;
                $json_array['action'] = "0";
        }
        $json_array['status'] = "true";
        $json_array['customer_id'] = $Row['id'];
        $json_array['message'] = $client_msg["login_register"]["Msg_Success"];

        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses get all categories
 * @param 
 * @return string
 */
if (isset($_REQUEST['btnGetAllCategories'])) {
        $json_array = array();
        $records = array();

        //$Sql = "SELECT * FROM categories ORDER  by `orders` ASC";
        /* $Sql = "SELECT * FROM categories where active=1 ORDER  by `orders` ASC";	 
          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("SELECT * FROM categories where active=? ORDER  by `orders` ASC", array(1));

        $count = 0;
        if ($RS->RecordCount() > 0) {
                $records[$count]["id"] = "0";
                $records[$count]["cat_name"] = "All Categories";
                $records[$count]["cat_image"] = "all-catagories.png";
                $records[$count]["orders"] = "0";
                $records[$count]["active"] = "0";
                $records[$count]["mob_img"] = "all_black.png";
                $records[$count]["mob_img_hover"] = "all_white.png";

                $count = 1;
                while ($Row = $RS->FetchRow()) {
                        $records[$count] = get_field_value($Row);
                        $count++;
                }
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $json_array["records"] = $records;
        } else {
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
 * @uses get search deal location of mobile
 * @param currentLocationName,currentLocationName,customer_id
 * @return string
 */
if (isset($_REQUEST['btnGetSearchDealLocations_mobile'])) {
        if (isset($_REQUEST['customer_id'])) {
                if (isset($_REQUEST['currentLocationName']) && $_REQUEST['currentLocationName'] != "") {
                        /* $sql_update = "update customer_user set current_location = '".$_REQUEST['currentLocationName']."' where id=".$_REQUEST['customer_id'];
                          $RS_cl_update = $objDB->Conn->Execute($sql_update); */
                        $RS_cl_update = $objDBWrt->Conn->Execute("update customer_user set current_location =? where id=?", array(urldecode($_REQUEST['currentLocationName']), $_REQUEST['customer_id']));
                }
        }
        $json_array = array();
        $records = array();
        $records_all = array();
        $json_array1 = array();
        $category_id = $_REQUEST['category_id'];
        //$dismile=$_REQUEST['dismile'];

        $dismile = 20;
        $miles_array[0][2] = 0;
        $miles_array[0][5] = 0;
        $miles_array[0][10] = 0;
        $miles_array[0][15] = 0;
        $miles_array[0][20] = 0;

        $date_f = date("Y-m-d H:i:s");
        $mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];

        $user_mlatitude = $_REQUEST['user_mlatitude'];
        $user_mlongitude = $_REQUEST['user_mlongitude'];



        $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;
        //$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
        $date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";

        $cust_where = "";

        $cat_str = "";
        if ($_REQUEST['customer_id'] != "") {
                $customer_id = $_REQUEST['customer_id'];
                $get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=" . $customer_id . " and
		ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = " . $customer_id . "
		and location_id=cl.location_id) total_reserved,";

                //$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
                // 12-8-2013
                $cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
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
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) ) )";
                // 03-10-2013	
                // 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =" . $customer_id . " and location_id=cl.location_id) ) )";
                // 05-10-2013	
                // 13-02-2013 also include checkin campaign	
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 or c.is_walkin=1) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =" . $customer_id . " and location_id=cl.location_id) ) )";
                // 13-02-2013	
        } else {
                $cust_where = " and c.level=1 ";
                // 13-02-2013 also include checkin campaign		
                $cust_where = " and (c.level=1 or c.is_walkin=1) ";
                // 13-02-2013	
        }
        if (isset($_REQUEST['category_id'])) {
                if ($_REQUEST['category_id'] == 0) {
                        $cat_str = "";
                } else {
                        $cat_str = " and c.category_id = " . $_REQUEST['category_id'] . " and c.category_id in(select cat.id from categories cat where cat.active=1) ";
                }
        }

        /*
          $limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
         */



        $limit_data = "SELECT l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open," . $get_dat . " round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance , count(*) total_deals,mu.business,l.timezone FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  " . $cust_where . " " . $cat_str . "  " . $date_wh . " and " . $Where . " group by cl.location_id ORDER BY distance,c.expiration_date";


        // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

        $RS_limit_data = $objDB->Conn->Execute($limit_data);
        //echo $RS_limit_data->RecordCount();
        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS_limit_data->RecordCount();
                $json_array['all_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                while ($Row = $RS_limit_data->FetchRow()) {
                        $location_latitude = $Row["latitude"];
                        $location_longitude = $Row["longitude"];
                        $deal_distance = $objJSON->distance($user_mlatitude, $user_mlongitude, $location_latitude, $location_longitude, "M");
                        //echo "<br/>";
                        /*
                          if($deal_distance>=0 && $deal_distance<=2)
                          $miles_array[0][2]=$miles_array[0][2]+1;
                          if($deal_distance>2 && $deal_distance<=5)
                          $miles_array[0][5]=$miles_array[0][5]+1;
                          if($deal_distance>5 && $deal_distance<=10)
                          $miles_array[0][10]=$miles_array[0][10]+1;
                          if($deal_distance>10 && $deal_distance<=15)
                          $miles_array[0][15]=$miles_array[0][15]+1;
                          if($deal_distance>15 && $deal_distance<=20)
                          $miles_array[0][20]=$miles_array[0][20]+1;
                         */
                        if ($deal_distance <= 2)
                                $miles_array[0][2] = $miles_array[0][2] + 1;
                        if ($deal_distance <= 5)
                                $miles_array[0][5] = $miles_array[0][5] + 1;
                        if ($deal_distance <= 10)
                                $miles_array[0][10] = $miles_array[0][10] + 1;
                        if ($deal_distance <= 15)
                                $miles_array[0][15] = $miles_array[0][15] + 1;
                        if ($deal_distance <= 20)
                                $miles_array[0][20] = $miles_array[0][20] + 1;
                }
        }
        else {
                $json_array['status'] = "false";
                $json_array['error_msg'] = $client_msg["search_deal"]["Msg_no_deal_in_20_miles"];
                $json = json_encode($json_array);
                echo $json;
                exit;
        }
        /*
          echo "2 miles=".$miles_array[0][2]."<br/>";
          echo "5 miles=".$miles_array[0][5]."<br/>";
          echo "10 miles=".$miles_array[0][10]."<br/>";
          echo "15 miles=".$miles_array[0][15]."<br/>";
          echo "20 miles=".$miles_array[0][20]."<br/>";
          exit();
         */
        if (isset($_REQUEST['dismile'])) {
                if ($_REQUEST['dismile'] == 2) {
                        if ($miles_array[0][2] > 0) {
                                $dismile = 2;
                                $json_array['miles_data'] = 2;
                        } else if ($miles_array[0][5] > 0) {
                                $dismile = 5;
                                $json_array['miles_data'] = 5;
                        } else if ($miles_array[0][10] > 0) {
                                $dismile = 10;
                                $json_array['miles_data'] = 10;
                        } else if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 5) {
                        if ($miles_array[0][5] > 0) {
                                $dismile = 5;
                                $json_array['miles_data'] = 5;
                        } else if ($miles_array[0][10] > 0) {
                                $dismile = 10;
                                $json_array['miles_data'] = 10;
                        } else if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 10) {
                        if ($miles_array[0][10] > 0) {
                                $dismile = 10;
                                $json_array['miles_data'] = 10;
                        } else if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 15) {
                        if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 20) {
                        if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                }
        }
        if (isset($_REQUEST['is_current_location']) && $_REQUEST['is_current_location'] == 0) {
                $dismile = 20;
                $json_array['miles_data'] = 20;
        }
        $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;

        // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

        $limit_data = "SELECT l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open," . $get_dat . " round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance , count(*) total_deals,mu.business,mu.merchant_icon,l.timezone FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active = 1  " . $cust_where . " " . $cat_str . "  " . $date_wh . " and " . $Where . " group by cl.location_id ORDER BY distance,c.expiration_date";
        //  echo $limit_data;
        //exit();

        $RS_limit_data = $objDB->Conn->Execute($limit_data);
        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['data'] = "https://www.scanflip.com/includes/customer/process_mobile.php?btnGetSearchDealLocations=yes&category_id=" . $_REQUEST['category_id'] .
                        "&mlatitude=" . $_REQUEST['mlatitude'] .
                        "&mlongitude=" . $_REQUEST['mlongitude'] .
                        "&user_mlatitude=" . $_REQUEST['user_mlatitude'] .
                        "&user_mlongitude=" . $_REQUEST['user_mlongitude'] .
                        "&dismile=" . $_REQUEST['dismile'] .
                        "&is_current_location=" . $_REQUEST['is_current_location'] .
                        "&is_current_location=" . $_REQUEST['is_current_location'];

                $json_array['total_records'] = $RS_limit_data->RecordCount();
                $json_array['all_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                while ($Row = $RS_limit_data->FetchRow()) {
                        $temp_merchant_arr = array();
                        //	echo "<br/>".count($arr_main_merchant_arr[$Row['merchant']])."===<br/>";
                        //	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                        if ($arr_main_merchant_arr[$Row['merchant']] != 0) {
                                $temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                        }
                        if (!in_array($Row['merchant'], $all_mercahnts)) {
                                array_push($all_mercahnts, $Row['merchant']);
                        }
                        $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                        //print_r($arr_main_location_arr);

                        array_push($temp_merchant_arr, get_field_value($Row));
                        //print_r($temp_merchant_arr);
                        $arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
                        //print_r($arr_main_merchant_arr[$Row['merchant']]);
                        //exit();
                        /* if (! array_key_exists($Row['location_id'], $arr_main_location_arr)) {
                          $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                          }
                          else{
                          if(! in_array($Row['location_id']."-".$Row['location_id'],$arr_main_location_arr))
                          {
                          $arr_main_location_arr[$Row['location_id']] = $Row['location_id']."-".$Row['location_id'];
                          }
                          } */

                        $records[$count] = get_field_value($Row);
                        //$records[$count]["rating"] = $objJSON->get_location_rating($Row["locid"]);

                        $count++;
                }
                //echo "<pre>";
                //print_r($arr_main_merchant_arr);
                //echo "</pre>";
                $json = json_encode($arr_main_merchant_arr);
                $id = 0;
                $final_array = array();
                $max_counter = 0;
                //print_r($all_mercahnts);
                for ($k = 0; $k < count($all_mercahnts); $k++) {
                        $max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
                        if ($max_counter <= $max_counter1) {
                                $max_counter = $max_counter1;
                        }
                }
                $final_array = array();
                //echo $max_counter."==";
                for ($j = 0; $j < $max_counter; $j++) {
                        for ($y = 0; $y < count($all_mercahnts); $y++) {
                                //echo "1";
                                if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "") {
                                        // start distance
                                        $location_latitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["latitude"];
                                        $location_longitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["longitude"];
                                        $deal_distance = $objJSON->distance($user_mlatitude, $user_mlongitude, $location_latitude, $location_longitude, "M");
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["distance"] = $deal_distance;
                                        // end distance
                                        // start location hour code
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"]."</br>";
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"]."</br>";

                                        $location_id = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];

                                        $time_zone = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone_name"];
                                        date_default_timezone_set($time_zone);
                                        $current_day = date('D');
                                        $current_time = date('g:i A');
                                        /* $sql="select * from location_hours where location_id=".$location_id." and day='".strtolower($current_day)."'";
                                          $RS_hours_data = $objDB->execute_query($sql); */
                                        $RS_hours_data =  $objDB->Conn->Execute("select * from location_hours where location_id=? and day=?", array($location_id, strtolower($current_day)));
                                        $location_time = "";
                                        $start_time = "";
                                        $end_time = "";
                                        $status_time = "";
                                        if ($RS_hours_data->RecordCount() > 0) {
                                                while ($Row_data = $RS_hours_data->FetchRow()) {
                                                        $start_time = $Row_data['start_time'];
                                                        $end_time = $Row_data['end_time'];
                                                        $location_time.=$Row_data['start_time'] . " - ";
                                                        $location_time.=$Row_data['end_time'];
                                                }
                                        }
                                        $st_time = strtotime($start_time);
                                        $end_time = strtotime($end_time);
                                        $cur_time = strtotime($current_time);

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 1) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Open";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 0) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Close";
                                        }
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"] = $location_time;
                                        // end location hour code
                                        // start business name

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"] != "") {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"];
                                        } else {

                                                $arr = file(WEB_PATH . '/includes/customer/process.php?getlocationbusinessname=yes&l_id=' . $location_id);
                                                if (trim($arr[0]) == "") {
                                                        unset($arr[0]);
                                                        $arr = array_values($arr);
                                                }
                                                $json = json_decode($arr[0]);
                                                $busines_name = $json->bus_name;
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"] = $busines_name;
                                        }

                                        // end business name
                                        // start merchant business tags

                                        /* $tags_sql = "SELECT business_tags from merchant_user where id =".$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["merchant"]; 
                                          $Row_tags=$objDB->Conn->Execute($tags_sql); */
                                        $Row_tags = $objDB->Conn->Execute("SELECT business_tags from merchant_user where id =?", array($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["merchant"]));
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['business_tags'] = $Row_tags->fields['business_tags'];

                                        // end merchant business tags
                                        // start pricerange

                                        $val = "";
                                        $val_text = "";

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 1) {
                                                $val_text = "Inexpensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 2) {
                                                $val_text = "Moderate";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 3) {
                                                $val_text = "Expensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 4) {
                                                $val_text = "Very Expensive";
                                        } else {
                                                $val_text = "";
                                        }

                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"] = $val_text;

                                        // end pricerange
                                        // start campaign array

                                        $count = 0;
                                        $campaign_records = array();

                                        /* $campaign_sql = "SELECT c.id,c.business_logo,c.title,c.category_id,c.is_walkin,cl.offers_left,c.is_new,c.deal_value,c.discount,c.saving,
                                          DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date,c.expiration_date FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
                                          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." and l.id=".$location_id." ORDER BY c.expiration_date";

                                          $RS_campaign_data=$objDB->Conn->Execute($campaign_sql); */
                                        $RS_campaign_data = $objDB->Conn->Execute("SELECT c.id,c.business_logo,c.title,c.category_id,c.is_walkin,cl.offers_left,c.is_new,c.deal_value,c.discount,c.saving,DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date,c.expiration_date FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by WHERE   l.active =?  " . $cust_where . " " . $cat_str . "  " . $date_wh . " and " . $Where . " and l.id=? ORDER BY c.expiration_date", array(1, $location_id));

                                        if ($RS_campaign_data->RecordCount() > 0) {
                                                while ($Row_campaign = $RS_campaign_data->FetchRow()) {

                                                        $campaign_records[$count] = get_field_value($Row_campaign);
                                                        $image = explode(".", $Row_campaign['business_logo']);
                                                        //echo $image[0].".jpg";
                                                        //$campaign_records[$count]["business_logo"] = $image[0].".jpg";
                                                        $campaign_records[$count]["title"] = ucwords(strtolower($campaign_records[$count]["title"]));
                                                        $count++;
                                                }
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"] = $campaign_records;
                                        }

                                        // end campaign array
                                        // start location category

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'] != "") {
                                                $count = 0;
                                                $cat_records = array();

                                                /* $cat_sql = "SELECT * from category_level where id in (".$arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'].")";

                                                  $RS_cat_data=$objDB->Conn->Execute($cat_sql); */
                                                $RS_cat_data = $objDB->Conn->Execute("SELECT * from category_level where id in (?)", array($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']));

                                                if ($RS_cat_data->RecordCount() > 0) {
                                                        while ($Row_cat = $RS_cat_data->FetchRow()) {
                                                                $cat_records[$count] = get_field_value($Row_cat);
                                                                $count++;
                                                        }
                                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = $cat_records;
                                                }
                                        } else {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = array();
                                        }

                                        // end location category

                                        array_push($final_array, $arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
                                }
                        }
                }

                $json_array['status'] = "true";
                //echo "<pre>";
                //print_r($final_array);
                //echo "</pre>";
                //$json = json_encode($final_array);
                //echo $json;
                //exit();
                $json_array["records"] = $final_array;
                /*
                  while($Row = $RS_limit_data->FetchRow())
                  {
                  $records_all[$count] = get_field_value($Row);
                  $count++;
                  } */
                //	$json_array["marker_records"]= $records_all;
                $json_array['total_records'] = count($json_array['records']);
                $json_array['marker_total_records'] = count($json_array['records']);
        } else {
                $json_array['all_records'] = 0;
                $json_array["records"] = "";
                $json_array['status'] = "false";
                $json_array['error_msg'] = "";
                $json_array['total_records'] = 0;
                $json_array['is_profileset'] = 1;
                $json_array["marker_records"] = "";
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
 * @param category_id,location_id,dismile,mlatitude,mlongitude
 * @return string
 */
if (isset($_REQUEST['btnGetSearchDealForLocation'])) {
        $json_array = array();
        $records = array();
        $records_all = array();
        $json_array1 = array();
        $category_id = $_REQUEST['category_id'];
        $location_id = $_REQUEST['location_id'];
        $dismile = $_REQUEST['dismile'];
        //$dismile= 50;
        $date_f = date("Y-m-d H:i:s");
        $mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];
        if (isset($_REQUEST['dismile'])) {
                $dismile = $_REQUEST['dismile'];
                $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;
                //$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;
        }
        $date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";

        $cust_where = "";

        $cat_str = "";
        if ($_REQUEST['customer_id'] != "") {
                $customer_id = $_REQUEST['customer_id'];
                $get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=" . $customer_id . " and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = " . $customer_id . "
	and location_id=cl.location_id) total_reserved,";

                //$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
                // 12-8-2013
                $cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
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
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) ) )";
                // 03-10-2013	
                // 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =" . $customer_id . " and location_id=cl.location_id) ) )";
                // 05-10-2013	
        } else {
                $cust_where = " and c.level=1 ";
        }
        if (isset($_REQUEST['category_id'])) {
                if ($_REQUEST['category_id'] == 0) {
                        $cat_str = "";
                } else {
                        $cat_str = " and c.category_id = " . $_REQUEST['category_id'] . " and c.category_id in(select cat.id from categories cat where cat.active=1) ";
                }
        }


        /* $limit_data = "SELECT mu.business Business,l.avarage_rating,c.*, STR_TO_DATE(c.expiration_date, '%d/%m/%Y %H:%i:%S') expire_date,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,round((((acos(sin((".$mlatitude."*pi()/180)) * 
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515 ),2) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." and l.id=".$location_id." ORDER BY distance,c.expiration_date";



          $RS_limit_data=$objDB->Conn->Execute($limit_data); */
        $RS_limit_data = $objDB->Conn->Execute("SELECT mu.business Business,l.avarage_rating,c.*, STR_TO_DATE(c.expiration_date, '%d/%m/%Y %H:%i:%S') expire_date,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
	WHERE   l.active =?  " . $cust_where . " " . $cat_str . "  " . $date_wh . " and " . $Where . " and l.id=? ORDER BY distance,c.expiration_date", array(1, $location_id));

        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                while ($Row = $RS_limit_data->FetchRow()) {
                        $records[$count] = get_field_value($Row);
                        $count++;
                }
                $json_array["records"] = $records;
                $json_array['total_records'] = count($json_array['records']);
        } else {
                $json_array["records"] = "";
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
 * @uses get subscribed merchant location mobile
 * @param category_id,location_id,dismile,mlatitude,mlongitude
 * @return string
 */

if (isset($_REQUEST['btnGetSubscribedMerchantLocations_mobile'])) {
        if (isset($_REQUEST['customer_id'])) {
                if (isset($_REQUEST['currentLocationName']) && $_REQUEST['currentLocationName'] != "") {
                        /* $sql_update = "update customer_user set current_location = '".$_REQUEST['currentLocationName']."' where id=".$_REQUEST['customer_id'];
                          $RS_cl_update = $objDB->Conn->Execute($sql_update); */
                        $RS_cl_update = $objDBWrt->Conn->Execute("update customer_user set current_location =? where id=?", array(urldecode($_REQUEST['currentLocationName']), $_REQUEST['customer_id']));
                }
        }

        $json_array = array();
        $records = array();
        $records_all = array();
        $json_array1 = array();
        $category_id = $_REQUEST['category_id'];
        //$dismile=$_REQUEST['dismile'];
        //$dismile= 50;

        $dismile = 20;
        $miles_array[0][2] = 0;
        $miles_array[0][5] = 0;
        $miles_array[0][10] = 0;
        $miles_array[0][15] = 0;
        $miles_array[0][20] = 0;

        $date_f = date("Y-m-d H:i:s");
        $mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];

        $user_mlatitude = $_REQUEST['user_mlatitude'];
        $user_mlongitude = $_REQUEST['user_mlongitude'];

        $id = $_REQUEST['customer_id'];
        //$miles=$_REQUEST['dismile'];

        $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;
        //$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;

        $date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";

        $cust_where = "";

        $cat_str = "";
        if ($_REQUEST['customer_id'] != "") {
                $customer_id = $_REQUEST['customer_id'];
                $get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=" . $customer_id . " and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = " . $customer_id . "
	and location_id=cl.location_id) total_reserved,";

                //$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
                // 12-8-2013
                $cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
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
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) ) )";
                // 03-10-2013	
                // 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =" . $customer_id . " and location_id=cl.location_id) ) )";
                // 05-10-2013
                // 13-02-2013 also include checkin campaign		
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 or c.is_walkin=1) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =" . $customer_id . " and location_id=cl.location_id) ) )";
                // 13-02-2013	
        } else {
                $cust_where = " and c.level=1 ";
                // 13-02-2013 also include checkin campaign		
                $cust_where = " and (c.level=1 or c.is_walkin=1) ";
                // 13-02-2013
        }
        if (isset($_REQUEST['category_id'])) {
                if ($_REQUEST['category_id'] == 0) {
                        $cat_str = "";
                } else {
                        $cat_str = " and c.category_id = " . $_REQUEST['category_id'] . " and c.category_id in(select cat.id from categories cat where cat.active=1) ";
                }
        }

        /*
          $limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
         */



        /* $limit_data = "SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
          round((((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
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
          //echo $limit_data;
          //exit;

          // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

          $RS_limit_data=$objDB->Conn->Execute($limit_data); */
        $RS_limit_data = $objDB->Conn->Execute("SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
                 round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active =? and c.is_walkin <> ? and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=?) or c.level =? ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=? and ss.subscribed_status=?)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status =?)  ))
  " . $cat_str . "  " . $date_wh . " and " . $Where . " group by cl.location_id ORDER BY distance,c.expiration_date", array(1, 1, $id, 1, $id, 1, $id, $id, 0));

        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS_limit_data->RecordCount();
                $json_array['all_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                while ($Row = $RS_limit_data->FetchRow()) {
                        $location_latitude = $Row["latitude"];
                        $location_longitude = $Row["longitude"];
                        $deal_distance = $objJSON->distance($user_mlatitude, $user_mlongitude, $location_latitude, $location_longitude, "M");
                        //echo "<br/>";
                        /*
                          if($deal_distance>=0 && $deal_distance<=2)
                          $miles_array[0][2]=$miles_array[0][2]+1;
                          if($deal_distance>2 && $deal_distance<=5)
                          $miles_array[0][5]=$miles_array[0][5]+1;
                          if($deal_distance>5 && $deal_distance<=10)
                          $miles_array[0][10]=$miles_array[0][10]+1;
                          if($deal_distance>10 && $deal_distance<=15)
                          $miles_array[0][15]=$miles_array[0][15]+1;
                          if($deal_distance>15 && $deal_distance<=20)
                          $miles_array[0][20]=$miles_array[0][20]+1;
                         */
                        if ($deal_distance <= 2)
                                $miles_array[0][2] = $miles_array[0][2] + 1;
                        if ($deal_distance <= 5)
                                $miles_array[0][5] = $miles_array[0][5] + 1;
                        if ($deal_distance <= 10)
                                $miles_array[0][10] = $miles_array[0][10] + 1;
                        if ($deal_distance <= 15)
                                $miles_array[0][15] = $miles_array[0][15] + 1;
                        if ($deal_distance <= 20)
                                $miles_array[0][20] = $miles_array[0][20] + 1;
                }
        }
        else {
                $json_array['status'] = "false";
                $json_array['error_msg'] = $client_msg["search_deal"]["Msg_no_deal_in_20_miles"];
                $json = json_encode($json_array);
                echo $json;
                exit;
        }
        /*
          echo "2 miles=".$miles_array[0][2]."<br/>";
          echo "5 miles=".$miles_array[0][5]."<br/>";
          echo "10 miles=".$miles_array[0][10]."<br/>";
          echo "15 miles=".$miles_array[0][15]."<br/>";
          echo "20 miles=".$miles_array[0][20]."<br/>";
          exit();
         */
        if (isset($_REQUEST['dismile'])) {
                if ($_REQUEST['dismile'] == 2) {
                        if ($miles_array[0][2] > 0) {
                                $dismile = 2;
                                $json_array['miles_data'] = 2;
                        } else if ($miles_array[0][5] > 0) {
                                $dismile = 5;
                                $json_array['miles_data'] = 5;
                        } else if ($miles_array[0][10] > 0) {
                                $dismile = 10;
                                $json_array['miles_data'] = 10;
                        } else if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 5) {
                        if ($miles_array[0][5] > 0) {
                                $dismile = 5;
                                $json_array['miles_data'] = 5;
                        } else if ($miles_array[0][10] > 0) {
                                $dismile = 10;
                                $json_array['miles_data'] = 10;
                        } else if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 10) {
                        if ($miles_array[0][10] > 0) {
                                $dismile = 10;
                                $json_array['miles_data'] = 10;
                        } else if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 15) {
                        if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 20) {
                        if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                }
        }
        if (isset($_REQUEST['is_current_location']) && $_REQUEST['is_current_location'] == 0) {
                $dismile = 20;
                $json_array['miles_data'] = 20;
        }

        $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;

        /* $limit_data = "SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
          round((((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
          FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
          WHERE l.active = 1 and c.is_walkin <> 1 and
          (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=$id) or c.level =1 ) and
          l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
          and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status =0)  ))
          ".$cat_str  ."  ".$date_wh." and ".$Where ." group by cl.location_id ORDER BY distance,c.expiration_date";

          // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

          $RS_limit_data=$objDB->Conn->Execute($limit_data); */
        $RS_limit_data = $objDB->Conn->Execute("SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
                 round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and c.is_walkin <> ? and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=?) or c.level =? ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=? and ss.subscribed_status=?)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status =?)  ))
  " . $cat_str . "  " . $date_wh . " and " . $Where . " group by cl.location_id ORDER BY distance,c.expiration_date", array(1, 1, $id, 1, $id, 1, $id, $id, 0));

        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS_limit_data->RecordCount();
                $json_array['all_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                while ($Row = $RS_limit_data->FetchRow()) {
                        $temp_merchant_arr = array();
                        //	echo "<br/>".count($arr_main_merchant_arr[$Row['merchant']])."===<br/>";
                        //	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                        if ($arr_main_merchant_arr[$Row['merchant']] != 0) {
                                $temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                        }
                        if (!in_array($Row['merchant'], $all_mercahnts)) {
                                array_push($all_mercahnts, $Row['merchant']);
                        }
                        $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                        //print_r($arr_main_location_arr);

                        array_push($temp_merchant_arr, get_field_value($Row));
                        //print_r($temp_merchant_arr);
                        $arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
                        //print_r($arr_main_merchant_arr[$Row['merchant']]);
                        //exit();
                        /* if (! array_key_exists($Row['location_id'], $arr_main_location_arr)) {
                          $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                          }
                          else{
                          if(! in_array($Row['location_id']."-".$Row['location_id'],$arr_main_location_arr))
                          {
                          $arr_main_location_arr[$Row['location_id']] = $Row['location_id']."-".$Row['location_id'];
                          }
                          } */

                        $records[$count] = get_field_value($Row);
                        //$records[$count]["rating"] = $objJSON->get_location_rating($Row["locid"]);

                        $count++;
                }
                //echo "<pre>";
                //print_r($arr_main_merchant_arr);
                //echo "</pre>";
                $json = json_encode($arr_main_merchant_arr);
                $id = 0;
                $final_array = array();
                $max_counter = 0;
                //print_r($all_mercahnts);
                for ($k = 0; $k < count($all_mercahnts); $k++) {
                        $max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
                        if ($max_counter <= $max_counter1) {
                                $max_counter = $max_counter1;
                        }
                }
                $final_array = array();
                //echo $max_counter."==";
                for ($j = 0; $j < $max_counter; $j++) {
                        for ($y = 0; $y < count($all_mercahnts); $y++) {
                                //echo "1";
                                if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "") {
                                        // start distance
                                        $location_latitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["latitude"];
                                        $location_longitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["longitude"];
                                        $deal_distance = $objJSON->distance($user_mlatitude, $user_mlongitude, $location_latitude, $location_longitude, "M");
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["distance"] = $deal_distance;
                                        // end distance
                                        // start location hour code
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"]."</br>";
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"]."</br>";

                                        $location_id = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];

                                        $time_zone = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone_name"];
                                        date_default_timezone_set($time_zone);
                                        $current_day = date('D');
                                        $current_time = date('g:i A');
                                        /* $sql="select * from location_hours where location_id=".$location_id." and day='".strtolower($current_day)."'";
                                          $RS_hours_data = $objDB->execute_query($sql); */
                                        $RS_hours_data =  $objDB->Conn->Execute("select * from location_hours where location_id=? and day=?", array($location_id, strtolower($current_day)));
                                        $location_time = "";
                                        $start_time = "";
                                        $end_time = "";
                                        $status_time = "";
                                        if ($RS_hours_data->RecordCount() > 0) {
                                                while ($Row_data = $RS_hours_data->FetchRow()) {
                                                        $start_time = $Row_data['start_time'];
                                                        $end_time = $Row_data['end_time'];
                                                        $location_time.=$Row_data['start_time'] . " - ";
                                                        $location_time.=$Row_data['end_time'];
                                                }
                                        }
                                        $st_time = strtotime($start_time);
                                        $end_time = strtotime($end_time);
                                        $cur_time = strtotime($current_time);
                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 1) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Open";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 0) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Close";
                                        }
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"] = $location_time;
                                        // end location hour code
                                        // start business name

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"] != "") {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"];
                                        } else {

                                                $arr = file(WEB_PATH . '/includes/customer/process.php?getlocationbusinessname=yes&l_id=' . $location_id);
                                                if (trim($arr[0]) == "") {
                                                        unset($arr[0]);
                                                        $arr = array_values($arr);
                                                }
                                                $json = json_decode($arr[0]);
                                                $busines_name = $json->bus_name;
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"] = $busines_name;
                                        }

                                        // end business name
                                        // start merchant business tags

                                        /* $tags_sql = "SELECT business_tags,merchant_icon from merchant_user where id =".$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["merchant"]; 
                                          $Row_tags=$objDB->Conn->Execute($tags_sql); */
                                        $Row_tags = $objDB->Conn->Execute("SELECT business_tags,merchant_icon from merchant_user where id =?", array($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["merchant"]));
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['business_tags'] = $Row_tags->fields['business_tags'];
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['merchant_icon'] = $Row_tags->fields['merchant_icon'];


                                        // end merchant business tags
                                        // start pricerange

                                        $val = "";
                                        $val_text = "";

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 1) {
                                                $val_text = "Inexpensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 2) {
                                                $val_text = "Moderate";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 3) {
                                                $val_text = "Expensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 4) {
                                                $val_text = "Very Expensive";
                                        } else {
                                                $val_text = "";
                                        }

                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"] = $val_text;

                                        // end pricerange
                                        // start campaign array

                                        $count = 0;
                                        $campaign_records = array();

                                        /* $campaign_sql = "SELECT c.id,c.business_logo,c.title,c.category_id,c.is_walkin,cl.offers_left,c.is_new,c.is_new,c.deal_value,c.discount,c.saving,
                                          DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date,
                                          c.expiration_date FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
                                          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." and l.id=".$location_id." ORDER BY c.expiration_date";

                                          $RS_campaign_data=$objDB->Conn->Execute($campaign_sql); */

                                        if ($RS_campaign_data->RecordCount() > 0) {
                                                while ($Row_campaign = $RS_campaign_data->FetchRow()) {
                                                        $campaign_records[$count] = get_field_value($Row_campaign);
                                                        $campaign_records[$count]["title"] = ucwords(strtolower($campaign_records[$count]["title"]));
                                                        $count++;
                                                }
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"] = $campaign_records;
                                        }

                                        // end campaign array
                                        // start location category

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'] != "") {
                                                $count = 0;
                                                $cat_records = array();

                                                /* $cat_sql = "SELECT * from category_level where id in (".$arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'].")";

                                                  $RS_cat_data=$objDB->Conn->Execute($cat_sql); */
                                                $RS_cat_data = $objDB->Conn->Execute("SELECT * from category_level where id in (?)", array($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']));

                                                if ($RS_cat_data->RecordCount() > 0) {
                                                        while ($Row_cat = $RS_cat_data->FetchRow()) {
                                                                $cat_records[$count] = get_field_value($Row_cat);
                                                                $count++;
                                                        }
                                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = $cat_records;
                                                }
                                        } else {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = array();
                                        }

                                        // end location category

                                        array_push($final_array, $arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
                                }
                        }
                }
                $json_array['status'] = "true";
                //echo "<pre>";
                //print_r($final_array);
                //echo "</pre>";
//$json = json_encode($final_array);
                //echo $json;
                //exit();
                $json_array["records"] = $final_array;
                /*
                  while($Row = $RS_limit_data->FetchRow())
                  {
                  $records_all[$count] = get_field_value($Row);
                  $count++;
                  } */
                //	$json_array["marker_records"]= $records_all;
                $json_array['total_records'] = count($json_array['records']);
                $json_array['marker_total_records'] = count($json_array['records']);
        } else {
                $json_array['all_records'] = 0;
                $json_array["records"] = "";
                $json_array['status'] = "false";
                $json_array['error_msg'] = $client_msg["mymerchant"]["label_filter_area"];
                $json_array['total_records'] = 0;
                $json_array['is_profileset'] = 1;
                $json_array["marker_records"] = "";
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
 * @uses get saved offers
 * @param customer_id,currentLocationName,category_id
 * @return string
 */

if (isset($_REQUEST['btnGetSavedOffers_mobile'])) {
        if (isset($_REQUEST['customer_id'])) {
                if (isset($_REQUEST['currentLocationName']) && $_REQUEST['currentLocationName'] != "") {
                        /* $sql_update = "update customer_user set current_location = '".$_REQUEST['currentLocationName']."' where id=".$_REQUEST['customer_id'];
                          $RS_cl_update = $objDB->Conn->Execute($sql_update); */
                        $RS_cl_update = $objDBWrt->Conn->Execute("update customer_user set current_location = ? where id=?", array(urldecode($_REQUEST['currentLocationName']), $_REQUEST['customer_id']));
                }
        }

        $json_array = array();
        $records = array();
        $records_all = array();
        $json_array1 = array();
        $category_id = $_REQUEST['category_id'];
        //$dismile=$_REQUEST['dismile'];
        //$dismile= 50;

        $dismile = 20;
        $miles_array[0][2] = 0;
        $miles_array[0][5] = 0;
        $miles_array[0][10] = 0;
        $miles_array[0][15] = 0;
        $miles_array[0][20] = 0;

        $date_f = date("Y-m-d H:i:s");
        $mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];

        $user_mlatitude = $_REQUEST['user_mlatitude'];
        $user_mlongitude = $_REQUEST['user_mlongitude'];

        $id = $_REQUEST['customer_id'];


        $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;
        //$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;

        $date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";

        $cust_where = "";

        $cat_str = "";
        if ($_REQUEST['customer_id'] != "") {
                $customer_id = $_REQUEST['customer_id'];
                $get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=" . $customer_id . " and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = " . $customer_id . "
	and location_id=cl.location_id) total_reserved,";

                //$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
                // 12-8-2013
                $cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
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
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) ) )";
                // 03-10-2013	
                // 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =" . $customer_id . " and location_id=cl.location_id) ) )";
                // 05-10-2013
        } else {
                $cust_where = " and c.level=1 ";
        }
        if (isset($_REQUEST['category_id'])) {
                if ($_REQUEST['category_id'] == 0) {
                        $cat_str = "";
                } else {
                        $cat_str = " and c.category_id = " . $_REQUEST['category_id'] . " and c.category_id in(select cat.id from categories cat where cat.active=1) ";
                }
        }

        /*
          $limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
         */



        /* $limit_data = "SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
          round((((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
          FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
          WHERE l.active = 1 and
          c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
          ".$cat_str  ."  ".$date_wh." and ".$Where ." group by cl.location_id ORDER BY distance,c.expiration_date";


          //
          //and (
          //                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
          //                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
          //echo $limit_data;
          // exit;

          // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

          $RS_limit_data=$objDB->Conn->Execute($limit_data); */
        $RS_limit_data = $objDB->Conn->Execute("SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
                round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status=?)
  " . $cat_str . "  " . $date_wh . " and " . $Where . " group by cl.location_id ORDER BY distance,c.expiration_date", array(1, $id, 1));

        //echo $RS_limit_data->RecordCount();
        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS_limit_data->RecordCount();
                $json_array['all_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                while ($Row = $RS_limit_data->FetchRow()) {
                        $location_latitude = $Row["latitude"];
                        $location_longitude = $Row["longitude"];
                        $deal_distance = $objJSON->distance($user_mlatitude, $user_mlongitude, $location_latitude, $location_longitude, "M");
                        //echo "<br/>";
                        /*
                          if($deal_distance>=0 && $deal_distance<=2)
                          $miles_array[0][2]=$miles_array[0][2]+1;
                          if($deal_distance>2 && $deal_distance<=5)
                          $miles_array[0][5]=$miles_array[0][5]+1;
                          if($deal_distance>5 && $deal_distance<=10)
                          $miles_array[0][10]=$miles_array[0][10]+1;
                          if($deal_distance>10 && $deal_distance<=15)
                          $miles_array[0][15]=$miles_array[0][15]+1;
                          if($deal_distance>15 && $deal_distance<=20)
                          $miles_array[0][20]=$miles_array[0][20]+1;
                         */
                        if ($deal_distance <= 2)
                                $miles_array[0][2] = $miles_array[0][2] + 1;
                        if ($deal_distance <= 5)
                                $miles_array[0][5] = $miles_array[0][5] + 1;
                        if ($deal_distance <= 10)
                                $miles_array[0][10] = $miles_array[0][10] + 1;
                        if ($deal_distance <= 15)
                                $miles_array[0][15] = $miles_array[0][15] + 1;
                        if ($deal_distance <= 20)
                                $miles_array[0][20] = $miles_array[0][20] + 1;
                }
        }
        else {
                $json_array['status'] = "false";
                $json_array['error_msg'] = $client_msg["my_deal"]["label_Currently_Not_Save_Offers"];
                $json = json_encode($json_array);
                echo $json;
                exit;
        }

        if (isset($_REQUEST['dismile'])) {
                if ($_REQUEST['dismile'] == 2) {
                        if ($miles_array[0][2] > 0) {
                                $dismile = 2;
                                $json_array['miles_data'] = 2;
                        } else if ($miles_array[0][5] > 0) {
                                $dismile = 5;
                                $json_array['miles_data'] = 5;
                        } else if ($miles_array[0][10] > 0) {
                                $dismile = 10;
                                $json_array['miles_data'] = 10;
                        } else if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 5) {
                        if ($miles_array[0][5] > 0) {
                                $dismile = 5;
                                $json_array['miles_data'] = 5;
                        } else if ($miles_array[0][10] > 0) {
                                $dismile = 10;
                                $json_array['miles_data'] = 10;
                        } else if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 10) {
                        if ($miles_array[0][10] > 0) {
                                $dismile = 10;
                                $json_array['miles_data'] = 10;
                        } else if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 15) {
                        if ($miles_array[0][15] > 0) {
                                $dismile = 15;
                                $json_array['miles_data'] = 15;
                        } else if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                } else if ($_REQUEST['dismile'] == 20) {
                        if ($miles_array[0][20] > 0) {
                                $dismile = 20;
                                $json_array['miles_data'] = 20;
                        }
                }
        }
        if (isset($_REQUEST['is_current_location']) && $_REQUEST['is_current_location'] == 0) {
                $dismile = 20;
                $json_array['miles_data'] = 20;
        }
        $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;

        /* $limit_data = "SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
          round((((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
          FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
          WHERE l.active = 1 and
          c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
          ".$cat_str  ."  ".$date_wh." and ".$Where ." group by cl.location_id ORDER BY distance,c.expiration_date";

          // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

          $RS_limit_data=$objDB->Conn->Execute($limit_data); */
        $RS_limit_data = $objDB->Conn->Execute("SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
                round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = ? and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = ? and location_id=cl.location_id and activation_status=?)
  " . $cat_str . "  " . $date_wh . " and " . $Where . " group by cl.location_id ORDER BY distance,c.expiration_date", array(1, $id, 1));

        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS_limit_data->RecordCount();
                $json_array['all_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                while ($Row = $RS_limit_data->FetchRow()) {
                        $temp_merchant_arr = array();
                        //	echo "<br/>".count($arr_main_merchant_arr[$Row['merchant']])."===<br/>";
                        //	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                        if ($arr_main_merchant_arr[$Row['merchant']] != 0) {
                                $temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                        }
                        if (!in_array($Row['merchant'], $all_mercahnts)) {
                                array_push($all_mercahnts, $Row['merchant']);
                        }
                        $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                        //print_r($arr_main_location_arr);

                        array_push($temp_merchant_arr, get_field_value($Row));
                        //print_r($temp_merchant_arr);
                        $arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
                        //print_r($arr_main_merchant_arr[$Row['merchant']]);
                        //exit();
                        /* if (! array_key_exists($Row['location_id'], $arr_main_location_arr)) {
                          $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                          }
                          else{
                          if(! in_array($Row['location_id']."-".$Row['location_id'],$arr_main_location_arr))
                          {
                          $arr_main_location_arr[$Row['location_id']] = $Row['location_id']."-".$Row['location_id'];
                          }
                          } */

                        $records[$count] = get_field_value($Row);
                        //$records[$count]["rating"] = $objJSON->get_location_rating($Row["locid"]);

                        $count++;
                }
                //echo "<pre>";
                //print_r($arr_main_merchant_arr);
                //echo "</pre>";
                $json = json_encode($arr_main_merchant_arr);
                $id = 0;
                $final_array = array();
                $max_counter = 0;
                //print_r($all_mercahnts);
                for ($k = 0; $k < count($all_mercahnts); $k++) {
                        $max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
                        if ($max_counter <= $max_counter1) {
                                $max_counter = $max_counter1;
                        }
                }
                $final_array = array();
                //echo $max_counter."==";
                for ($j = 0; $j < $max_counter; $j++) {

                        for ($y = 0; $y < count($all_mercahnts); $y++) {
                                //echo "1";

                                if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "") {
                                        // start distance
                                        $location_latitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["latitude"];
                                        $location_longitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["longitude"];
                                        $deal_distance = $objJSON->distance($user_mlatitude, $user_mlongitude, $location_latitude, $location_longitude, "M");
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["distance"] = $deal_distance;
                                        // end distance
                                        // start location hour code
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"]."</br>";
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"]."</br>";

                                        $location_id = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];

                                        $time_zone = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone_name"];
                                        date_default_timezone_set($time_zone);
                                        $current_day = date('D');
                                        $current_time = date('g:i A');
                                        /* $sql="select * from location_hours where location_id=".$location_id." and day='".strtolower($current_day)."'";
                                          $RS_hours_data = $objDB->execute_query($sql); */
                                        $RS_hours_data =  $objDB->Conn->Execute("select * from location_hours where location_id=? and day=?", array($location_id, strtolower($current_day)));
                                        $location_time = "";
                                        $start_time = "";
                                        $end_time = "";
                                        $status_time = "";
                                        if ($RS_hours_data->RecordCount() > 0) {
                                                while ($Row_data = $RS_hours_data->FetchRow()) {
                                                        $start_time = $Row_data['start_time'];
                                                        $end_time = $Row_data['end_time'];
                                                        $location_time.=$Row_data['start_time'] . " - ";
                                                        $location_time.=$Row_data['end_time'];
                                                }
                                        }
                                        $st_time = strtotime($start_time);
                                        $end_time = strtotime($end_time);
                                        $cur_time = strtotime($current_time);
                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 1) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Open";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 0) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Close";
                                        }
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"] = $location_time;
                                        // end location hour code
                                        // start business name

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"] != "") {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_name"];
                                        } else {

                                                $arr = file(WEB_PATH . '/includes/customer/process.php?getlocationbusinessname=yes&l_id=' . $location_id);
                                                if (trim($arr[0]) == "") {
                                                        unset($arr[0]);
                                                        $arr = array_values($arr);
                                                }
                                                $json = json_decode($arr[0]);
                                                $busines_name = $json->bus_name;
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["business"] = $busines_name;
                                        }

                                        // end business name
                                        // start merchant business tags

                                        /* $tags_sql = "SELECT business_tags,merchant_icon from merchant_user where id =".$arr_main_merchant_arr[$all_mercahnts[$y]][$j]["merchant"]; 
                                          $Row_tags=$objDB->Conn->Execute($tags_sql); */
                                        $Row_tags = $objDB->Conn->Execute("SELECT business_tags,merchant_icon from merchant_user where id =?", array($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["merchant"]));
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['business_tags'] = $Row_tags->fields['business_tags'];
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['merchant_icon'] = $Row_tags->fields['merchant_icon'];

                                        // end merchant business tags 
                                        // start pricerange

                                        $val = "";
                                        $val_text = "";

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 1) {
                                                $val_text = "Inexpensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 2) {
                                                $val_text = "Moderate";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 3) {
                                                $val_text = "Expensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 4) {
                                                $val_text = "Very Expensive";
                                        } else {
                                                $val_text = "";
                                        }

                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"] = $val_text;

                                        // end pricerange
                                        // start location category

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'] != "") {
                                                $count = 0;
                                                $cat_records = array();

                                                /* $cat_sql = "SELECT * from category_level where id in (".$arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'].")";

                                                  $RS_cat_data=$objDB->Conn->Execute($cat_sql); */
                                                $RS_cat_data = $objDB->Conn->Execute("SELECT * from category_level where id in (?)", array($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories']));

                                                if ($RS_cat_data->RecordCount() > 0) {
                                                        while ($Row_cat = $RS_cat_data->FetchRow()) {
                                                                $cat_records[$count] = get_field_value($Row_cat);
                                                                $count++;
                                                        }
                                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = $cat_records;
                                                }
                                        } else {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = array();
                                        }

                                        // end location category
                                        // start campaign array

                                        $count = 0;
                                        $campaign_records = array();

                                        $campaign_sql = "SELECT c.id,c.title,c.category_id,cl.offers_left FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
					WHERE   l.active = 1  " . $cat_str . "  " . $date_wh . " and " . $Where . " and l.id=" . $location_id . " ORDER BY c.expiration_date";

                                        /* $campaign_sql = "SELECT c.id,c.business_logo,c.title,c.category_id,c.is_walkin,cl.offers_left,c.number_of_use,c.is_new,c.is_new,c.deal_value,c.discount,c.saving,
                                          DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date
                                          ,c.expiration_date
                                          FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
                                          WHERE l.id=".$location_id." and l.active = 1 and c.id  in ( select campaign_id from customer_campaigns where customer_id =". $_REQUEST['customer_id']." and location_id=cl.location_id and activation_status=1)
                                          ".$cat_str  ."  ".$date_wh." and ".$Where ." ORDER BY c.expiration_date";

                                          //echo $campaign_sql;
                                          $RS_campaign_data=$objDB->Conn->Execute($campaign_sql); */
                                        $RS_campaign_data = $objDB->Conn->Execute("SELECT c.id,c.business_logo,c.title,c.category_id,c.is_walkin,cl.offers_left,c.number_of_use,c.is_new,c.is_new,c.deal_value,c.discount,c.saving,
				DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date
				,c.expiration_date  
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.id=? and l.active = ? and c.id  in ( select campaign_id from customer_campaigns where customer_id =? and location_id=cl.location_id and activation_status=?)
  " . $cat_str . "  " . $date_wh . " and " . $Where . " ORDER BY c.expiration_date", array($location_id, 1, $_REQUEST['customer_id'], 1));

                                        if ($RS_campaign_data->RecordCount() > 0) {
                                                while ($Row_campaign = $RS_campaign_data->FetchRow()) {
                                                        //echo $Row_campaign['id']."-".$location_id."-".$_REQUEST['customer_id']."</br>";
                                                        $array_where_camp = array();
                                                        $array_where_camp['campaign_id'] = $Row_campaign['id'];
                                                        $array_where_camp['customer_id'] = $_REQUEST['customer_id'];
                                                        $array_where_camp['referred_customer_id'] = 0;
                                                        $array_where_camp['location_id'] = $location_id;
                                                        $RS_camp = $objDB->Show("reward_user", $array_where_camp);
                                                        //echo "<pre>";
                                                        //print_r($array_where_camp);
                                                        //echo "</pre>";
                                                        $array_where_camp1 = array();
                                                        $array_where_camp1['campaign_id'] = $Row_campaign['id'];
                                                        $array_where_camp1['location_id'] = $location_id;
                                                        $campLoc = $objDB->Show("campaign_location", $array_where_camp1);

                                                        if ($RS_camp->RecordCount() > 0 && $Row_campaign['number_of_use'] == "1") {
                                                                //echo "1 ".$Row_campaign['id'].",".$location_id;
                                                        } else if ($RS_camp->RecordCount() > 0 && ($Row_campaign['number_of_use'] == "2" || $Row_campaign['number_of_use'] == "3" ) && $campLoc->fields['offers_left'] == 0) {
                                                                //echo "2 ".$Row_campaign['id'].",".$location_id;
                                                        } else {
                                                                //echo "else";
                                                                $campaign_records[$count] = get_field_value($Row_campaign);
                                                                $campaign_records[$count]["title"] = ucwords(strtolower($campaign_records[$count]["title"]));
                                                                $count++;
                                                        }
                                                }
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"] = $campaign_records;
                                        }

                                        // end campaign array


                                        if ($count > 0) {
                                                array_push($final_array, $arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
                                        }
                                }
                        }
                }

                $json_array['status'] = "true";
                //echo "<pre>";
                //print_r($final_array);
                //echo "</pre>";
//$json = json_encode($final_array);
                //echo $json;
                //exit();
                $json_array["records"] = $final_array;
                /*
                  while($Row = $RS_limit_data->FetchRow())
                  {
                  $records_all[$count] = get_field_value($Row);
                  $count++;
                  } */
                //	$json_array["marker_records"]= $records_all;
                $json_array['total_records'] = count($json_array['records']);
                $json_array['marker_total_records'] = count($json_array['records']);
        } else {
                $json_array['all_records'] = 0;
                $json_array["records"] = "";
                $json_array['status'] = "false";
                $json_array['error_msg'] = $client_msg["my_deal"]["label_Currently_Not_Save_Offers"];
                $json_array['total_records'] = 0;
                $json_array['is_profileset'] = 1;
                $json_array["marker_records"] = "";
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
 * @uses user profile set
 * @param customer_id
 * @return string
 */

if (isset($_REQUEST['is_userprofileset'])) {
        $json_array = array();

        $customer_id = $_REQUEST['customer_id'];
        //$cust_sql = 'select * from customer_user where postalcode <>"" and  gender  <>"" and  dob_year <>"" and  country <>""  and id='.$customer_id;//.$customer_id;
        /* $cust_sql = 'select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>0 and  country <>""  and id='.$customer_id;//.$customer_id;
          $RS_cust_data=$objDB->Conn->Execute($cust_sql); */
        $RS_cust_data = $objDB->Conn->Execute('select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>? and  country <>""  and id=?', array(0, $customer_id));
        $is_profileset = $RS_cust_data->RecordCount();

        if ($is_profileset == 0) {

                $json_array['status'] = "false";
                $json_array['is_profileset'] = $is_profileset;
                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {
                $json_array['status'] = "true";
                $json_array['is_profileset'] = 1;
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}


/**
 * @uses update profile compulsary field
 * @param customer_id,firstname,lastname,state,city,mobileno,emailnotification,country,postalcode,gender,dob_month,dob_day,dob_year
 * @return string
 */

if (isset($_REQUEST['btnUpdateProfile_compulsary_field'])) {


        $array = $json_array = $where_clause = array();
        $where_clause['id'] = $_REQUEST['customer_id'];

        if (isset($_REQUEST['firstname'])) {
                $array['firstname'] = $_REQUEST['firstname'];
        }
        if (isset($_REQUEST['lastname'])) {
                $array['lastname'] = $_REQUEST['lastname'];
        }
        if (isset($_REQUEST['state'])) {
                $array['state'] = $_REQUEST['state'];
        }
        if (isset($_REQUEST['city'])) {
                $array['city'] = $_REQUEST['city'];
        }
        if (isset($_REQUEST['mobileno'])) {
                $array['mobileno'] = "001-" . $_REQUEST['mobileno'];
        }

        if (isset($_REQUEST['emailnotification'])) {
                $array['emailnotification'] = $_REQUEST['emailnotification'];
        }
        $array['country'] = $_REQUEST['country'];

        $postalcode = str_replace(" ", "", $_REQUEST['postalcode']);
        $array['postalcode'] = strtoupper($postalcode);
        $array['gender'] = $_REQUEST['gender'];

        if (isset($_REQUEST['dob_month'])) {
                $array['dob_month'] = $_REQUEST['dob_month'];
        }
        if (isset($_REQUEST['dob_day'])) {
                $array['dob_day'] = $_REQUEST['dob_day'];
        }
        if (isset($_REQUEST['dob_year'])) {
                $array['dob_year'] = $_REQUEST['dob_year'];
        }

        $string_address = $_REQUEST['country'] . "," . $_REQUEST['postalcode'];
        $string_address = urlencode($string_address);
        $geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $string_address . "&sensor=false");
        $geojson = json_decode($geocode, true);



        if ($geojson['status'] == 'OK') {

                $array['curr_latitude'] = $geojson['results'][0]['geometry']['location']['lat'];
                $array['curr_longitude'] = $geojson['results'][0]['geometry']['location']['lng'];
                $timezone1 = getClosestTimezone($array['curr_latitude'], $array['curr_longitude']);
                $timezone = new DateTimeZone($timezone1);
                $offset1 = $timezone->getOffset(new DateTime);
                // //timezone_offset_string( $offset1 );
                $tz = timezone_offset_string($offset1);
                $array['curr_timezone'] = $tz;
        } else {
                $array['curr_latitude'] = "";
                $array['curr_longitude'] = "";
                $array['curr_timezone'] = "";
        }
        $objDB->Update($array, "customer_user", $where_clause);
        $json_array['status'] = "true";
        $json_array['message'] = "Profile has been updated successfully";
        $json = json_encode($json_array);

        echo $json;
        exit();
}

/**
 * @uses get profile
 * @param customer_id
 * @return string
 */
if (isset($_REQUEST['btnGetProfile'])) {
        $array = $json_array = array();
        $array['id'] = $_REQUEST['customer_id'];
        $RS = $objDB->Show("customer_user", $array);
        $Row = $RS->FetchRow();
        $json_array['customer_info'] = get_field_value($Row);
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses get location for keyword
 * @param customer_id
 * @return string
 */

if (isset($_REQUEST['btnGetLocationForKeyword'])) {
        $json_array = array();
        $records = array();

        $keyword = $_REQUEST["keyword"];
        /* $Sql = "SELECT * from search_city where City like '".$keyword."%'" ; 
          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("SELECT * from search_city where City like '?%'", array($keyword));

        $count = 0;
        if ($RS->RecordCount() > 0) {
                $count = 1;
                while ($Row = $RS->FetchRow()) {
                        //$records[$count] = get_field_value($Row);
                        $records[$count] = $Row['City'] . "," . $Row['StateCode'] . "," . $Row['Country'];
                        $count++;
                }
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $json_array["records"] = $records;
        } else {
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
 * @uses get campaign
 * @param mobile_uinque_id,location_id,campaign_id,qrcode,mobile_uinque_id
 * @return string
 */

if (isset($_REQUEST['btnGetCampaign'])) {
        /*         * **** check for uinque scan ********** */
        if (isset($_REQUEST['mobile_uinque_id'])) {
                $locationid = $_REQUEST['location_id'];
                $campaignid = $_REQUEST['campaign_id'];
                $qrcode = $_REQUEST['qrcode'];
                $mobile_id = $_REQUEST['mobile_uinque_id'];
                /* $Sql  = "SELECT * FROM locations  where id=".$_REQUEST['location_id'];


                  $RS_location = $objDB->Conn->Execute($Sql); */
                $RS_location = $objDB->Conn->Execute("SELECT * FROM locations  where id=?", array($_REQUEST['location_id']));

                $timezone = $RS_location->fields['timezone'];
                $dt_sql = "SElect CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR('" . $timezone . "',1, POSITION(',' IN '" . $timezone . "')-1)) dte ";

                /* $sql_qrcode = "select id from qrcodes where qrcode='".$qrcode."' ";
                  $RS_qrcode = $objDB->Conn->Execute($sql_qrcode ); */
                $RS_qrcode = $objDB->Conn->Execute("select id from qrcodes where qrcode=? ", array($qrcode));
                $q_id = $RS_qrcode->fields['id'];
                $RS_dt = $objDB->Conn->Execute($dt_sql);
                /* $sql = "select * from scan_qrcode where location_id=".$_REQUEST['location_id']." and campaign_id=".$_REQUEST['campaign_id']." and qrcode_id=".$q_id." and  mobile_uinque_id='".$_REQUEST['mobile_uinque_id']."' ";
                  $RS_scans = $objDB->Conn->Execute($sql); */
                $RS_scans = $objDB->Conn->Execute("select * from scan_qrcode where location_id=? and campaign_id=? and qrcode_id=? and  mobile_uinque_id=? ", array($_REQUEST['location_id'], $_REQUEST['campaign_id'], $q_id, $_REQUEST['mobile_uinque_id']));

                if ($RS_scans->RecordCount() == 0) {
                        if (isset($_REQUEST['customer_id'])) {
                                /* $sql= "update scan_qrcode set user_id=".$_REQUEST['customer_id']." where mobile_uinque_id='".$_REQUEST['mobile_uinque_id']."' ";
                                  $objDB->Conn->Execute($sql); */
                                $objDBWrt->Conn->Execute("update scan_qrcode set user_id=? where mobile_uinque_id=?", array($_REQUEST['customer_id'], $_REQUEST['mobile_uinque_id']));
                        } else {
                                
                        }
                        $uid = 1;
                } else {
                        $uid = 0;
                        if (isset($_REQUEST['customer_id'])) {
                                /* $sql= "update scan_qrcode set user_id=".$_REQUEST['customer_id']." where mobile_uinque_id='".$_REQUEST['mobile_uinque_id']."' ";
                                  $objDB->Conn->Execute($sql); */
                                $objDBWrt->Conn->Execute("update scan_qrcode set user_id=? where mobile_uinque_id=?", array($_REQUEST['customer_id'], $_REQUEST['mobile_uinque_id']));
                        }
                }
                $custid = 0;

                if (isset($_REQUEST['customer_id'])) {
                        $custid = $_REQUEST['customer_id'];
                } else {
                        $custid = 0;
                }

                $insert_array['qrcode_id'] = $q_id;
                $insert_array['campaign_id'] = $campaignid;
                $insert_array['location_id'] = $locationid;
                $insert_array['is_location'] = 0;
                $insert_array['is_superadmin'] = 0;
                $insert_array['is_unique'] = $uid;
                $insert_array['scaned_date'] = $RS_dt->fields['dte'];
                $insert_array['user_id'] = $custid;
                $insert_array['mobile_uinque_id'] = $_REQUEST['mobile_uinque_id'];
                $objDB->Insert($insert_array, "scan_qrcode");
        }
        /*         * **** check for unique scan ********** */
        $json_array = array();
        $records = array();

        $campaign_id = $_REQUEST["campaign_id"];
        $location_id = $_REQUEST["location_id"];

        $reserve = "no";

        if (isset($_REQUEST["customer_id"])) {
                $customer_id = $_REQUEST["customer_id"];
                /* $sql_ac ="SELECT * FROM customer_campaigns WHERE activation_status=1 and campaign_id =".$campaign_id." and location_id=".$location_id." and customer_id=".$customer_id;
                  $RSsql_ac = $objDB->Conn->Execute($sql_ac); */
                $RSsql_ac = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE activation_status=1 and campaign_id =? and location_id=? and customer_id=?", array($campaign_id, $location_id, $customer_id));
                if ($RSsql_ac->RecordCount() > 0) {
                        $reserve = "yes";
                } else {
                        $reserve = "no";
                }
        } else {
                $reserve = "no";
        }
        //$Sql = "SELECT * from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=".$campaign_id." and l.id=".$location_id;

        if ($reserve == "yes") {
                /* $Sql = "SELECT DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date,l.timezone_name  ,l.timezone, business_logo,title,description,deal_detail_description,terms_condition,mob_img_hover,
                  redeem_rewards,referral_rewards,offers_left,expiration_date,number_of_use,new_customer,campaign_tag,is_walkin,is_new,c.deal_value,c.discount,c.saving,
                  latitude,longitude,address,city,state,zip,country,permalink from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=".$campaign_id." and l.id=".$location_id." and offers_left>=0"; */
                $RS = $objDB->Conn->Execute("SELECT DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date,l.timezone_name  ,l.timezone, business_logo,title,description,deal_detail_description,terms_condition,mob_img_hover,
		redeem_rewards,referral_rewards,offers_left,expiration_date,number_of_use,new_customer,campaign_tag,is_walkin,is_new,c.deal_value,c.discount,c.saving,
		latitude,longitude,address,city,state,zip,country,permalink from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=? and l.id=? and offers_left>=?", array($campaign_id, $location_id, 0));
        } else {
                /* $Sql = "SELECT DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date,l.timezone_name  ,l.timezone, business_logo,title,description,deal_detail_description,terms_condition,mob_img_hover,
                  redeem_rewards,referral_rewards,offers_left,expiration_date,number_of_use,new_customer,campaign_tag,is_walkin,is_new,c.deal_value,c.discount,c.saving,
                  latitude,longitude,address,city,state,zip,country,permalink from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=".$campaign_id." and l.id=".$location_id." and offers_left>0"; */
                $RS = $objDB->Conn->Execute("SELECT DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date,l.timezone_name  ,l.timezone, business_logo,title,description,deal_detail_description,terms_condition,mob_img_hover,
		redeem_rewards,referral_rewards,offers_left,expiration_date,number_of_use,new_customer,campaign_tag,is_walkin,is_new,c.deal_value,c.discount,c.saving,
		latitude,longitude,address,city,state,zip,country,permalink from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=? and l.id=? and offers_left>?", array($campaign_id, $location_id, 0));
        }



        $count = 0;
        if ($RS->RecordCount() > 0) {
                $count = 1;
                while ($Row = $RS->FetchRow()) {
                        $records[$count] = get_field_value($Row);
                        $records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));
                        if ($Row['business_logo'] != "") {
                                $image = explode(".", $Row['business_logo']);
                                //echo $image[0].".jpg";
                                $records[$count]["business_logo"] = $image[0] . ".jpg";
                        }
                        if ($Row['referral_rewards'] != "") {
                                /* $Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$campaign_id;

                                  $RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location); */
                                $RS_max_no_location = $objDB->Conn->Execute("SELECT max_no_sharing from campaigns WHERE id=?", array($campaign_id));
                                /* $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$campaign_id." and referred_customer_id<>0";
                                  $RS_shared = $objDB->Conn->Execute($Sql_shared); */
                                $RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?", array($campaign_id, 0));

                                if ($RS_shared->RecordCount() >= $RS_max_no_location->fields['max_no_sharing']) {
                                        $records[$count]['referral_rewards'] = "0";
                                } else {
                                        $records[$count]['referral_rewards'] = $Row['referral_rewards'];
                                }
                        }

                        if (isset($_REQUEST["customer_id"])) {
                                $customer_id = $_REQUEST["customer_id"];
                                /* $sql_ac ="SELECT * FROM customer_campaigns WHERE activation_status=1 and campaign_id =".$campaign_id." and location_id=".$location_id." and customer_id=".$customer_id;
                                  $RSsql_ac = $objDB->Conn->Execute($sql_ac); */
                                $RSsql_ac = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE activation_status=? and campaign_id =? and location_id=? and customer_id=?", array(1, $campaign_id, $location_id, $customer_id));
                                if ($RSsql_ac->RecordCount() > 0) {
                                        $records[$count]['reserve'] = "yes";
                                } else {
                                        $records[$count]['reserve'] = "no";
                                }
                                //$activate_link = WEB_PATH."/register.php?campaign_id=".$campaign_id."&l_id=".$location_id."&share=true&customer_id=".base64_encode($customer_id);
                                /* 20-03-2014 send domain */
                                $activate_link = WEB_PATH . "/register.php?campaign_id=" . $campaign_id . "&l_id=" . $location_id . "&share=true&customer_id=" . base64_encode($customer_id) . "&domain=";
                                /* 20-03-2014 send domain */
                                $records[$count]["share_link"] = $activate_link;
                        } else {
                                $records[$count]['reserve'] = "no";
                        }
                        //$Sql = "SELECT * from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=".$campaign_id." and l.id=".$location_id;

                        $arr1 = file(WEB_PATH . '/includes/customer/process_mobile.php?getlocationbusinessname=yes&l_id=' . $location_id);
                        if (trim($arr1[0]) == "") {
                                unset($arr1[0]);
                                $arr1 = array_values($arr1);
                        }
                        $json1 = json_decode($arr1[0]);
                        $busines_name1 = $json1->bus_name;
                        $records[$count]['business'] = $busines_name1;

                        if ($Row['terms_condition'] != "") {
                                $records[$count]['terms_condition'] = $Row['terms_condition'] . "<p> Additional Terms</p> No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.";
                        }
                        if ($Row['terms_condition'] == "") {
                                $records[$count]['terms_condition'] = "No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.";
                        }

                        $count++;
                }
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $json_array["records"] = $records;
        } else {
                $json_array['status'] = "false";
                //$json_array['campaign_end_message'] = $client_msg['campaign']['campaign_end_message'];
                $json_array['campaign_end_message'] = $client_msg["campaign"]["Msg_offer_left_zero"];

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
 * @uses get closest timezone
 * @param latitude,longitude
 * @return string
 */

function getClosestTimezone($lat, $lng) {
        $diffs = array();
        foreach (DateTimeZone::listIdentifiers() as $timezoneID) {
                $timezone = new DateTimeZone($timezoneID);
                $location = $timezone->getLocation();
                $tLat = $location['latitude'];
                $tLng = $location['longitude'];
                $diffLat = abs($lat - $tLat);
                $diffLng = abs($lng - $tLng);
                $diff = $diffLat + $diffLng;
                $diffs[$timezoneID] = $diff;
        }

        //asort($diffs);
        $timezone = array_keys($diffs, min($diffs));


        return $timezone[0];
}

/**
 * @uses get nearest location
 * @param dismile,mlatitude,mlongitude
 * @return string
 */
if (isset($_REQUEST['btnGetNearestLocations'])) {
        $json_array = array();
        $records = array();
        $records_all = array();
        $json_array1 = array();
        if (isset($_REQUEST['dismile'])) {
                $dismile = $_REQUEST['dismile'];
        } else {
                $dismile = 20;
        }
        $date_f = date("Y-m-d H:i:s");
        $mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];
        $Where = "";
		$get_dat = "";
        if (isset($_REQUEST['dismile'])) {
                $Where = "and (69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;
                $Sql = "SELECT sl.* FROM locations sl WHERE " . $Where;
        }
        /*
          //$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
          $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";
         */
        if(isset($_REQUEST['customer_id']))
        { 
			if ($_REQUEST['customer_id'] != "")
			{
					$date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";
			} 
			else 
			{
					$date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
			}
		}
		else
		{
			$date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";	
		}

//         if($_REQUEST['firstlimit']=="" && $_REQUEST['lastlimit']=="")
//         {
//             $firstlimit=0;
//             $lastlimit=9;
//         }
//         else
//         {
//             $firstlimit=$_REQUEST['firstlimit'];
//             $lastlimit=$_REQUEST['lastlimit'];
//         }

        $cust_where = "";

        $cust_where = "";

        $cat_str = "";
        if(isset($_REQUEST['customer_id']))
        {
			if ($_REQUEST['customer_id'] != "")
			{
					$customer_id = $_REQUEST['customer_id'];
					$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id where  ms.user_id=" . $customer_id . ") or c.level =1 ) ";
			} 
			else
			{
					$cust_where = " and c.level=1 ";
			}
		}
		else
		{
				$cust_where = " and c.level=1 ";
		}

        $limit_data = "SELECT distinct l.city,l.state,l.country,l.latitude,l.longitude FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE l.active = 1  " . $cust_where . " " . $date_wh . " " . $Where . " group by l.city ORDER BY 
		(((acos(sin((" . $mlatitude . "*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515
        ) limit 3";

//
//and (
//                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
//                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
        //echo $limit_data;
// exit(); 
        $RS_limit_data = $objDB->Conn->Execute($limit_data);
        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['message'] = $client_msg['search_deal']['label_No_Records'];
                //$json_array['query']= $limit_data;
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                while ($Row = $RS_limit_data->FetchRow()) {
                        $dismile = 20;
                        $mlatitude1 = $Row['latitude'];
                        $mlongitude1 = $Row['longitude'];

                        //echo $mlatitude1."</br>";
                        //echo $mlongitude1."</br>";
                        //echo $dismile."</br>";

                        $cust_where = "";
                        $cat_str = "";

                        $date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
                        $Where = "(69.1*(l.latitude-($mlatitude1))*69.1*(l.latitude-($mlatitude1)))+(53.0*(l.longitude-($mlongitude1))*53.0*(l.longitude-($mlongitude1)))<=" . $dismile * $dismile;

                        $limit_data1 = "SELECT l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open," . $get_dat . " round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
			sin((`latitude`*pi()/180))+cos((" . $mlatitude1 . "*pi()/180)) * 
			cos((`latitude`*pi()/180)) * cos(((" . $mlongitude1 . "- `longitude`)* 
			pi()/180))))*180/pi())*60*1.1515 ),2) as distance , count(*) total_deals,mu.business,l.timezone FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
			WHERE   l.active = 1  " . $cust_where . " " . $cat_str . "  " . $date_wh . " and " . $Where . " group by cl.location_id ORDER BY distance,c.expiration_date";

                        //echo $limit_data1."</br>";
                        $RS_limit_data1 = $objDB->Conn->Execute($limit_data1);
                        if ($RS_limit_data1->RecordCount() > 0) {
                                //echo $RS_limit_data1->RecordCount()."</br>";
                                $records[$count] = get_field_value($Row);
                                $count++;
                        }
                }
                $json_array["records"] = $records;
        } else {
                $json_array['message'] = $client_msg['search_deal']['label_Sorry_No_Offers_From_Merchant'];
                //$json_array['query']= $limit_data;
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
 * @uses reserv deal
 * @param customer_id,timestamp
 * @return string
 */

if (isset($_REQUEST['btnreservedeal'])) {
        $objDB->Conn->StartTrans();
        //print_r($_REQUEST);
        $json_array = array();
        $json_array['is_profileset'] = 1;
        $customer_id = $_REQUEST['customer_id'];
        $cid = $_REQUEST['customer_id'];
        $timestamp = $_REQUEST['timestamp'];


        //echo $customer_id."<hr>";
        $where_x = array();
        $where_x['campaign_id'] = $_REQUEST['campaign_id'];
        $CodeDetails = $objDB->Show("activation_codes", $where_x);
        $activation_code = $CodeDetails->fields['activation_code'];
        $campaign_id = $_REQUEST['campaign_id'];
        $lid = $_REQUEST['location_id'];
        $location_id = $_REQUEST['location_id'];
        //   echo $campaign_id."campaign id";

        /* $sql_o = "select * from campaign_location where campaign_id =".$campaign_id." and location_id =". $lid ." and active=1";

          $RS_o = $objDB->Conn->Execute($sql_o); */
        $RS_o = $objDB->Conn->Execute("select * from campaign_location where campaign_id =? and location_id =? and active=?", array($campaign_id, $lid, 1));

        /* 	$Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=".$lid;
          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id=?", array($customer_id, $campaign_id, $lid));

        if (isset($_REQUEST['refer_customer_id'])) {

                $refer_customer_id = $_REQUEST['refer_customer_id'];
                $refer_customer_id_decoded = base64_decode($refer_customer_id);
                //echo $refer_customer_id." = ".$refer_customer_id_decoded;
                //exit();
                //echo $campaign_id."-".$location_id."-".$customer_id."-".$refer_customer_id."-".$timestamp."<br/>";

                $json_array['share'] = "true";
                $json_array['c_id'] = $campaign_id;
                $json_array['l_id'] = $location_id;

                /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list */
                //     echo "Innn";
                /* 		$Sql_max_is_walkin = "SELECT is_walkin , new_customer, level  from campaigns WHERE id=".$campaign_id;

                  $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin); */
                $RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin , new_customer, level  from campaigns WHERE id=?", array($campaign_id));
                //  if($RS_max_is_walkin->fields['is_walkin'] == 1)
                //  {
                /* $Sql_c_c = "SELECT * FROM customer_campaigns WHERE customer_id='".$customer_id."' AND campaign_id='".$campaign_id."' AND location_id =".$location_id;
                  //    echo $Sql_c_c;
                  $RS_c_c = $objDB->Conn->Execute($Sql_c_c); */
                $RS_c_c = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?", array($customer_id, $campaign_id, $location_id));

                //echo "record count=".$RS_c_c->RecordCount()."<br/>";

                $allow_for_reserve = 0;
                $is_new_user = 0;
                $its_new_user = 0;

                if ($RS_c_c->RecordCount() <= 0) {
                        /* $Sql = "SELECT * FROM activation_codes WHERE campaign_id=".$campaign_id;

                          $RS_1 = $objDB->Conn->Execute($Sql); */
                        $RS_1 = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE campaign_id=?", array($campaign_id));

                        //echo "record count=".$RS_1->RecordCount()."<br/>";
                        if ($RS_1->RecordCount() > 0) {
                                /* $location_max_sql = "Select num_activation_code , offers_left,used_offers from campaign_location where  campaign_id=".$campaign_id." and location_id=".$location_id;
                                  $location_max = $objDB->Conn->Execute($location_max_sql); */
                                $location_max = $objDB->Conn->Execute("Select num_activation_code , offers_left,used_offers from campaign_location where  campaign_id=? and location_id=?", array($campaign_id, $location_id));

                                $offers_left = $location_max->fields['offers_left'];
                                $used_campaign = $location_max->fields['used_offers'];
                                $o_left = $location_max->fields['offers_left'];
                                $share_flag = 1;
                                $its_new_user = 0;
                                //     echo $o_left."== offers left";
                                // echo "<br/>1111111111111111111111111111";
                                // RESERVE DEAL LOGIC
                                if ($o_left > 0) {
                                        if ($RS_max_is_walkin->fields['new_customer'] == 1) {
                                                /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=".  $location_id.")";
                                                  //select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=".  $_COOKIE['l_id'].")
                                                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                                $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)", array($cid, $location_id));

                                                if ($subscibed_store_rs->RecordCount() == 0) {
                                                        $its_new_user = 1;
                                                        $share_flag = 1;
                                                } else {
                                                        $its_new_user = 0;
                                                        $share_flag = 0;
                                                }
                                        }

                                        /* check whether new customer for this store */
                                        $allow_for_reserve = 0;
                                        $is_new_user = 0;
                                        /*                                         * *************    ************************ */
                                        /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=". $location_id.") ";
                                          //     echo "sql_check===".$sql_chk."<br/>";
                                          $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
                                        $Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($cid, $location_id));

                                        if ($Rs_is_new_customer->RecordCount() == 0) {
                                                $is_new_user = 1;
                                        } else {
                                                $is_new_user = 0;
                                        }
                                        //echo "=======".$is_new_user."=====<br/>";
                                        /*                                         * ************* ************************ */
                                        if ($is_new_user == 1) {
                                                $allow_for_reserve = 1;
                                        } else {
                                                /* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$location_id;
                                                  // echo "<br/>===".$sql."===<br />";
                                                  $RS_campaign_groups = $objDB->Conn->Execute($sql); */
                                                $RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?", array($campaign_id, $location_id));

                                                $c_g_str = "";
                                                $cnt = 1;

                                                $is_it_in_group = 0;
                                                if ($RS_max_is_walkin->fields['level'] == 0) {
                                                        if ($RS_max_is_walkin->fields['is_walkin'] == 0) {
                                                                if ($RS_campaign_groups->RecordCount() > 0) {
                                                                        while ($Row_campaign = $RS_campaign_groups->FetchRow()) {
                                                                                $c_g_str = $Row_campaign['group_id'];
                                                                                if ($cnt != $RS_campaign_groups->RecordCount()) {
                                                                                        $c_g_str .= ",";
                                                                                }
                                                                        }
                                                                        /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$cid."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                                                          //   echo $Sql_new_."Sql_new_====<br />";
                                                                          $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
                                                                        $RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?)  )", array($cid, $c_g_str));

                                                                        while ($Row_Check_Cust_group = $RS_check_s->FetchRow()) {
                                                                                /* $query = "Select * from merchant_subscribs where  user_id='".$cid."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                                                  $RS_query = $objDB->Conn->Execute($query); */
                                                                                $RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ", array($cid, $Row_Check_Cust_group['group_id'], $c_g_str));

                                                                                if ($RS_query->RecordCount() > 0) {
                                                                                        $is_it_in_group = 1;
                                                                                }
                                                                        }
                                                                        if ($is_it_in_group == 1) {
                                                                                $allow_for_reserve = 1;
                                                                        } else {
                                                                                $allow_for_reserve = 0;
                                                                        }
                                                                } else {
                                                                        $allow_for_reserve = 0;
                                                                }
                                                        }
                                                        //If it is walkin deal
                                                        else {
                                                                // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                                                /* $query = "Select * from merchant_subscribs where  user_id=".$cid." and group_id=( select id from merchant_groups mg where mg.location_id=".$location_id." and mg.private =1 ) ";
                                                                  //echo $query;
                                                                  $RS_all_user_group = $objDB->Conn->Execute($query); */
                                                                $RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =?) ", array($cid, $location_id, 1));

                                                                if ($RS_all_user_group->RecordCount() > 0) {
                                                                        $allow_for_reserve = 1;
                                                                } else {
                                                                        $allow_for_reserve = 0;
                                                                }
                                                        }
                                                } else {
                                                        //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                                        $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='" . $cid . "' AND group_id in( select  id from merchant_groups where location_id  =" . $location_id . "  )";
                                                        $allow_for_reserve = 1;
                                                }
                                        }
                                        // echo "<br />SQl_new===".$Sql_new_ ."=====<br />";

                                        /* for checking whether customer in campaign group */


                                        /* check whether new customer for this store */
                                        //echo $allow_for_reserve."===allow for reserve";
                                        //exit;
                                        if ($share_flag == 1) {
                                                if ($allow_for_reserve == 1 || $its_new_user == 1) {
                                                        $activation_code = $RS_1->fields['activation_code'];
                                                        /* $Sql = "INSERT INTO customer_campaigns SET  activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
                                                          customer_id='".$cid."', campaign_id=".$campaign_id." , location_id=".$location_id;
                                                          $objDB->Conn->Execute($Sql); */
                                                        $objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET  activation_status=?, activation_code=?, activation_date= ?, coupon_generation_date=?,customer_id=?, campaign_id=? , location_id=?", array(1, $activation_code, 'Now()', 'Now()', $cid, $campaign_id, $location_id));
                                                        //$RSLocation_nm  =  $objDB->Conn->Execute("select * from locations where id =".$location_id);
                                                        $RSLocation_nm = $objDB->Conn->Execute("select * from locations where id =?", array($location_id));
                                                        //$br = $cid.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$location_id;
                                                        $br = $objJSON->generate_voucher_code($cid, $activation_code, $campaign_id, $RSLocation_nm->fields['location_name'], $location_id);

                                                        /* $insert_coupon_code = "Insert into coupon_codes set customer_id=".$cid." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$location_id." , generated_date='".date('Y-m-d H:i:s')."' ";

                                                          $objDB->Conn->Execute($insert_coupon_code); */
                                                        $objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=? , customer_campaign_code=? , coupon_code=? , active=? , location_id=? , generated_date=? ", array($cid, $campaign_id, $br, 1, $location_id, date('Y-m-d H:i:s')));

                                                        /* $update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$location_id." ";
                                                          $objDB->Conn->Execute($update_num_activation); */
                                                        $objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=? and location_id =? ", array(($offers_left - 1), ($used_campaign + 1), $campaign_id), $location_id);
                                                } else {
                                                        $json_array['status'] = "false";
                                                        $json_array['error_msg'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                                                        $json = json_encode($json_array);
                                                        $objDB->Conn->CompleteTrans();
                                                        echo $json;
                                                        exit;
                                                }
                                        }
                                } else {
                                        $json_array['status'] = "false";
                                        $json_array['error_msg'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                        $json = json_encode($json_array);
                                        $objDB->Conn->CompleteTrans();
                                        echo $json;
                                        exit;
                                }
                        }
                }

                /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list  */

                /* check for whether max sharing reached and user is firt time subscribed to this loaction */
                /* $Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$campaign_id;
                  $RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location); */
                $RS_max_no_location = $objDB->Conn->Execute("SELECT max_no_sharing from campaigns WHERE id=?", array($campaign_id));
                $Sql_shared = "SELECT * from reward_user WHERE campaign_id=" . $campaign_id . " and referred_customer_id<>0";
                // $RS_shared = $objDB->Conn->Execute($Sql_shared);
                //if($RS_shared->RecordCount() < $RS_max_no_location->fields['max_no_sharing'] ){
                //  $sql_chk ="select * from subscribed_stores where customer_id= ".$cid." and location_id=". $_COOKIE['l_id'];
                /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=".  $location_id.")";
                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)", array($cid, $location_id));

                //echo "allow_for_reserve=".$allow_for_reserve." its_new_user=".$its_new_user."<br/>";

                if ($allow_for_reserve == 1 || $its_new_user == 1) {
                        $redeem_array = array();
                        $redeem_array['customer_id'] = base64_decode($refer_customer_id); //$_SESSION['customer_id'];
                        $redeem_array['campaign_id'] = $campaign_id;
                        $redeem_array['earned_reward'] = 0;
                        $redeem_array['referral_reward'] = 0;
                        $redeem_array['referred_customer_id'] = $cid;
                        $redeem_array['reward_date'] = date("Y-m-d H:i:s");
                        $redeem_array['coupon_code_id'] = 0;
                        $redeem_array['location_id'] = $location_id;
                        $objDB->Insert($redeem_array, "reward_user");
                } else {
                        // make comment on 28/08/2014
                        /*
                          // 22 07 2014 dist list deal reserve but cust id not in dist list
                          $json_array['status'] = "newuser";
                          $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                          $json_array['error_msg'] =  $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                          $json = json_encode($json_array);
                          $objDB->Conn->CompleteTrans();
                          echo $json;
                          exit;
                         */
                        // make comment on 28/08/2014
                        // add on 28/08/2014

                        $array_where_camp2 = array();
                        $array_where_camp2['campaign_id'] = $campaign_id;
                        $array_where_camp2['customer_id'] = $cid;
                        $array_where_camp2['location_id'] = $location_id;
                        $RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
                        $reserved = $RS_cust_camp->RecordCount();

                        $array_where_camp = array();
                        $array_where_camp['campaign_id'] = $campaign_id;
                        $array_where_camp['customer_id'] = $cid;
                        $array_where_camp['referred_customer_id'] = 0;
                        $array_where_camp['location_id'] = $location_id;
                        $RS_camp = $objDB->Show("reward_user", $array_where_camp);
                        $redeemed = $RS_camp->RecordCount();

                        $array_where_camp1 = array();
                        $array_where_camp1['campaign_id'] = $campaign_id;
                        $array_where_camp1['location_id'] = $location_id;
                        $RS_camp1 = $objDB->Show("campaign_location", $array_where_camp1);

                        $array_where_camp3 = array();
                        $array_where_camp3['id'] = $campaign_id;
                        $RS_camp3 = $objDB->Show("campaigns", $array_where_camp3);

                        //echo "reserved = ".$reserved." redeemed = ".$redeemed." offer_left =".$RS_camp1->fields['offers_left']." number_of_use =".$RS_camp3->fields['number_of_use'];

                        if ($RS_cust_camp->RecordCount() > 0 && $RS_camp->RecordCount() > 0 && $RS_camp3->fields['number_of_use'] == 1) {
                                $json_array['status'] = "false";
                                $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_Already_Redeemed"];
                                $json_array['error_msg'] = $client_msg["campaign"]["Msg_Already_Redeemed"];
                                $json = json_encode($json_array);
                                $objDB->Conn->CompleteTrans();
                                echo $json;
                                exit;
                        } else if ($RS_cust_camp->RecordCount() > 0 && $RS_camp->RecordCount() > 0 && ($RS_camp3->fields['number_of_use'] == 2 || $RS_camp3->fields['number_of_use'] == 3) && $RS_camp1->fields['offers_left'] == 0) {
                                $json_array['status'] = "false";
                                $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                $json_array['error_msg'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                $json = json_encode($json_array);
                                $objDB->Conn->CompleteTrans();
                                echo $json;
                                exit;
                        } else if ($RS_cust_camp->RecordCount() == 0 && $RS_camp->RecordCount() == 0 && $RS_camp1->fields['offers_left'] == 0) {
                                $json_array['status'] = "false";
                                $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                $json_array['error_msg'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                $json = json_encode($json_array);
                                $objDB->Conn->CompleteTrans();
                                echo $json;
                                exit;
                        }
                        // add on 28/08/2014
                }

                /* check whether maximum share reached / sharing count now 0 so send mail to merchant */

                /* $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$campaign_id." and referred_customer_id<>0";
                  $RS_shared = $objDB->Conn->Execute($Sql_shared); */
                $RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>?", array($campaign_id, 0));

                /* $Sql_active = "SELECT active,offers_left from campaign_location WHERE campaign_id=".$campaign_id." and location_id=".$location_id;
                  $RS_active = $objDB->Conn->Execute($Sql_active); */
                $RS_active = $objDB->Conn->Execute("SELECT active,offers_left from campaign_location WHERE campaign_id=? and location_id=" . $location_id, array($campaign_id));



                if ($RS_shared->RecordCount() <= $RS_max_no_location->fields['max_no_sharing'] && $RS_active->fields['offers_left'] > 0 && $RS_active->fields['active']) {
                        /* $Sql_created_by = "SELECT created_by from locations WHERE id=".$location_id;

                          $RS_created_by = $objDB->Conn->Execute($Sql_created_by); */
                        $RS_created_by = $objDB->Conn->Execute("SELECT created_by from locations WHERE id=?", array($location_id));

                        $merchantid = $RS_created_by->fields['created_by'];

                        /* $Sql_merchant_detail = "SELECT * from merchant_user WHERE id=".$merchantid;

                          $RS_merchant_detail = $objDB->Conn->Execute($Sql_merchant_detail); */
                        $RS_merchant_detail = $objDB->Conn->Execute("SELECT * from merchant_user WHERE id=?", array($merchantid));

                        /* $Sql_campaigns_detail = "SELECT * from campaigns WHERE id=".$campaign_id;
                          $RS_campaigns_detail = $objDB->Conn->Execute($Sql_campaigns_detail); */
                        $RS_campaigns_detail = $objDB->Conn->Execute("SELECT * from campaigns WHERE id=?", array($campaign_id));

                        $mail = new PHPMailer();
                        $merchant_id = $RS_merchant_detail->fields['id'];
                        $email_address = $RS_merchant_detail->fields['email'];
                        //$email_address="test.test1397@gmail.com";
                        $body = "<div>Hello,<span style='font-weight:bold'>" . $RS_merchant_detail->fields['firstname'] . " " . $RS_merchant_detail->fields['lastname'] . "</span></div>";
                        $body.="<br>";
                        $body.="<div>Congratulations! Sharing points for <span style='font-weight:bold'>" . $RS_campaigns_detail->fields['title'] . "</span> were allocated for new customer referral. 
			Please <a herf='" . WEB_PATH . "/merchant/register.php' > log in </a> if you wish to increase number of referral customers limit for your campaign  . </div>";
                        $body.="<br>";
                        $body.="<div>Sincerely,</div>";
                        $body.="<div>Scanflip Support Team</div>";

                        $mail->AddReplyTo('no-reply@scanflip.com', 'ScanFlip Support');

                        $mail->AddAddress($email_address);

                        $mail->From = "no-reply@scanflip.com";
                        $mail->FromName = "ScanFlip Support";
                        $mail->Subject = "Scanflip offer - " . $RS_campaigns_detail->fields['title'];
                        $mail->MsgHTML($body);
                        $mail->Send();
                }

                //Make entry in subscribed_stre table for first time subscribe to loaction
                /* $sql_group = "select id , merchant_id from merchant_groups where location_id =". $location_id." and private = 1";
                  $RS_group = $objDB->Conn->Execute($sql_group); */
                $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?", array($location_id, 1));

                /* $sql_chk ="select * from subscribed_stores where customer_id= ".$cid." and location_id=".$location_id;
                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?", array($cid, $location_id));

                if ($subscibed_store_rs->RecordCount() == 0) {
                        /* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$cid." ,location_id=".$location_id." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                          $objDB->Conn->Execute($insert_subscribed_store_sql); */
                        $objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=?", array($cid, $location_id, date('Y-m-d H:i:s'), 1));
                } else {
                        if ($subscibed_store_rs->fields['subscribed_status'] == 0) {
                                /* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$cid." and location_id=".$location_id;
                                  $objDB->Conn->Execute($up_subscribed_store); */
                                $objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=?  where  customer_id=? and location_id=?", array(1, $cid, $location_id));
                        }
                }
                /* check whether share user in stores's private group */
                /* $Sql = "SELECT * FROM merchant_subscribs WHERE user_id=".$cid." and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=".$location_id."  )";
                  $RS_new = $objDB->Conn->Execute($Sql); */
                $RS_new = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id in( select id merchant_groups from merchant_groups where private=? and location_id=?)", array($cid, 1, $location_id));
                if ($RS_new->RecordCount() <= 0) {
                        /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$location_id." and private = 1";
                          $RS_group = $objDB->Conn->Execute($sql_group); */
                        $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?", array($location_id, 1));
                        $array_group = array();
                        $array_group['merchant_id'] = $RS_group->fields['merchant_id'];
                        $array_group['group_id'] = $RS_group->fields['id'];
                        $array_group['user_id'] = $cid;
                        $objDB->Insert($array_group, "merchant_subscribs");
                }
        } else 
        {
			if (isset($_REQUEST['is_walkin'])) 
			{
                if ($_REQUEST['is_walkin'] == 1) 
                {
                        $mycurrent_lati = $_REQUEST['mycurrent_lati'];

                        $mycurrent_long = $_REQUEST['mycurrent_long'];

                        $array1 = $where_clause1 = array();
                        $where_clause1['id'] = $lid;
                        $RS1 = $objDB->Show("locations", $where_clause1);
                        $Row1 = $RS1->FetchRow();

                        $to_lati1 = $Row1['latitude'];

                        $to_long1 = $Row1['longitude'];

                        $deal_distance = $objJSON->distance($mycurrent_lati, $mycurrent_long, $to_lati1, $to_long1, "M");

                        $deal_distance_in_feet = ($deal_distance * 5280.0);

                        if ($deal_distance_in_feet > 500) {
                                $json_array['status'] = "false";
                                //$json_array['error_msg'] = "cur lat ".$mycurrent_lati." lng ".$mycurrent_long." feet ".$deal_distance_in_feet." ".$client_msg["campaign"]["Msg_checkin_reserve"];
                                $json_array['error_msg'] = $client_msg["campaign"]["Msg_checkin_reserve"];
                                $json = json_encode($json_array);
                                $objDB->Conn->CompleteTrans();
                                echo $json;
                                exit;
                        }
                }
           }

                if ($RS_o->RecordCount() > 0) {

                        if ($RS->RecordCount() <= 0) {
                                /* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";

                                  $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
                                $RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?", array($campaign_id, $lid));
                                $offers_left = $RS_num_activation->fields['offers_left'];
                                $used_campaign = $RS_num_activation->fields['used_offers'];

                                $share_flag = 1;
                                if ($offers_left > 0) {
                                        /* $Sql_max_is_walkin = "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=".$campaign_id;

                                          $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin); */
                                        $RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin , level ,new_customer  from campaigns WHERE id=?", array($campaign_id));

                                        if ($RS_max_is_walkin->fields['new_customer'] == 1) {
                                                /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$customer_id." and location_id=".$lid.") ";

                                                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                                $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($customer_id, $lid));
                                                if ($subscibed_store_rs->RecordCount() == 0) {
                                                        $share_flag = 1;
                                                } else {
                                                        $share_flag = 0;
                                                }
                                        }


                                        /* check whether new customer for this store */
                                        $allow_for_reserve = 0;
                                        $is_new_user = 0;
                                        /*                                         * ************* ************************ */
                                        /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$customer_id." and location_id=".$lid.") ";

                                          $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
                                        $Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($customer_id, $lid));
                                        if ($Rs_is_new_customer->RecordCount() == 0) {
                                                $is_new_user = 1;
                                        } else {
                                                $is_new_user = 0;
                                        }
                                        /*                                         * ************* ************************ */

                                        if ($is_new_user == 0) {
                                                /* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;
                                                  $RS_campaign_groups = $objDB->Conn->Execute($sql); */
                                                $RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?", array($campaign_id, $lid));

                                                $c_g_str = "";
                                                $cnt = 1;

                                                $is_it_in_group = 0;

                                                if ($RS_max_is_walkin->fields['level'] == 0) {

                                                        if ($RS_max_is_walkin->fields['is_walkin'] == 0) {
                                                                if ($RS_campaign_groups->RecordCount() > 0) {
                                                                        while ($Row_campaign = $RS_campaign_groups->FetchRow()) {
                                                                                $c_g_str .= $Row_campaign['group_id'];
                                                                                if ($cnt != $RS_campaign_groups->RecordCount()) {
                                                                                        $c_g_str .= ",";
                                                                                }
                                                                                $cnt++;
                                                                        }
                                                                        /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$customer_id."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";

                                                                          $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
                                                                        $RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?))", array($customer_id, $c_g_str));
                                                                        while ($Row_Check_Cust_group = $RS_check_s->FetchRow()) {
                                                                                /* $query = "Select * from merchant_subscribs where  user_id='".$customer_id."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";
                                                                                  $RS_query = $objDB->Conn->Execute($query); */
                                                                                $RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ", array($customer_id, $Row_Check_Cust_group['group_id'], $c_g_str));

                                                                                if ($RS_query->RecordCount() > 0) {
                                                                                        $is_it_in_group = 1;
                                                                                }
                                                                        }
                                                                        if ($is_it_in_group == 1) {
                                                                                $allow_for_reserve = 1;
                                                                        } else {
                                                                                $allow_for_reserve = 0;
                                                                        }
                                                                } else {
                                                                        $allow_for_reserve = 0;
                                                                }
                                                        } else {
                                                                // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                                                /* $query = "Select * from merchant_subscribs where  user_id=".$customer_id." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) ";
                                                                  $RS_all_user_group = $objDB->Conn->Execute($query); */
                                                                $RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =? ) ", array($customer_id, $lid, 1));

                                                                if ($RS_all_user_group->RecordCount() > 0) {
                                                                        $allow_for_reserve = 1;
                                                                } else {
                                                                        $allow_for_reserve = 0;
                                                                }
                                                        }
                                                } else {
                                                        //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$customer_id."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                                        $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='" . $customer_id . "' AND group_id in( select  id from merchant_groups where location_id  =" . $lid . "  )";
                                                        $allow_for_reserve = 1;
                                                }
                                        } else {
                                                $allow_for_reserve = 1;
                                        }


                                        /* for checking whether customer in campaign group */
                                        if ($share_flag == 1) {
                                                if ($allow_for_reserve == 1) {
                                                        //$Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;

                                                        /* $Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= '".$timestamp."', coupon_generation_date='".$timestamp."',
                                                          customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;

                                                          $objDB->Conn->Execute($Sql); */
                                                        $objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status='1', activation_code=?, activation_date= ?, coupon_generation_date=?,
						customer_id=?, campaign_id=? , location_id=?", array($activation_code, $timestamp, $timestamp, $customer_id, $campaign_id, $lid));

                                                        /* $update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$lid." ";
                                                          $objDB->Conn->Execute($update_num_activation); */
                                                        $objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=? and location_id =?", array(($offers_left - 1), ($used_campaign + 1), $campaign_id, $lid));

                                                        //$RSLocation_nm  = $objDB->Conn->Execute("select * from locations where id =".$lid);
                                                        $RSLocation_nm = $objDB->Conn->Execute("select * from locations where id =?", array($lid));

                                                        //$br = $customer_id.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$lid;
                                                        $br = $objJSON->generate_voucher_code($customer_id, $activation_code, $campaign_id, $RSLocation_nm->fields['location_name'], $lid);

                                                        $json_array['campaign_id'] = $campaign_id;
                                                        $json_array['location_id'] = $lid;


                                                        /* $select_coupon_code = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
                                                          $select_rs = $objDB->Conn->Execute($select_coupon_code); */
                                                        $select_rs = $objDB->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?", array($customer_id, $campaign_id, $lid));
                                                        if ($select_rs->RecordCount() <= 0) {
                                                                $array_ = array();
                                                                $array_['customer_id'] = $customer_id;
                                                                $array_['customer_campaign_code'] = $campaign_id;
                                                                $array_['coupon_code'] = $br;
                                                                $array_['active'] = 1;
                                                                $array_['location_id'] = $lid;
                                                                $array_['generated_date'] = $timestamp;
                                                                /* $insert_coupon_code = "Insert into coupon_codes set customer_id=".$customer_id." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$lid." , generated_date='".$timestamp."' ";
                                                                  $objDB->Conn->Execute($insert_coupon_code); */
                                                                $objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=? , customer_campaign_code=? , coupon_code=? , active=? , location_id=? , generated_date=?", array($customer_id, $campaign_id, $br, 1, $lid, $timestamp));
                                                        }
                                                        //Make entry in subscribed_stre table for first time subscribe to loaction
                                                        /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                                          $RS_group = $objDB->Conn->Execute($sql_group); */
                                                        $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?", array($lid, 1));

                                                        /* $sql_chk ="select * from subscribed_stores where customer_id= ".$customer_id." and location_id=".$lid;
                                                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                                        $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?", array($customer_id, $lid));
                                                        if ($subscibed_store_rs->RecordCount() == 0) {
                                                                /* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$customer_id." ,location_id=".$lid." ,subscribed_date='".$timestamp."' ,subscribed_status=1";
                                                                  $objDB->Conn->Execute($insert_subscribed_store_sql); */
                                                                $objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=1", array($customer_id, $lid, $timestamp));
                                                        } else {
                                                                if ($subscibed_store_rs->fields['subscribed_status'] == 0) {
                                                                        /* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$customer_id." and location_id=".$lid;
                                                                          $objDB->Conn->Execute($up_subscribed_store); */
                                                                        $objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=?  where  customer_id=? and location_id=?", array(1, $customer_id, $lid));
                                                                }
                                                        }
                                                        // If campaign is walking deal then make entry in coupon_codes table //

                                                        /* $RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$customer_id;
                                                          $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe); */
                                                        $check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id =? and private = ?) and user_id = ?", array($lid, 1, $customer_id));
                                                        if ($check_subscribe->RecordCount() == 0) {
                                                                /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                                                  $RS_group = $objDB->Conn->Execute($sql_group); */
                                                                $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private =?", array($lid, 1));

                                                                if ($RS_group->RecordCount() > 0) {
                                                                        /* $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;

                                                                          $RS_user_group =$objDB->Conn->Execute($sql_user_group); */
                                                                        $RS_user_group = $objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id = ?", array($RS_group->fields['merchant_id'], $RS_group->fields['id'], $customer_id));

                                                                        if ($RS_user_group->RecordCount() <= 0) {
                                                                                /* $insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$customer_id;
                                                                                  $objDB->Conn->Execute($insert_sql); */
                                                                                $objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id = ? , user_id = ?", array($RS_group->fields['merchant_id'], $RS_group->fields['id'], $customer_id));
                                                                        }
                                                                }
                                                        }
                                                        //
                                                } else {
                                                        $json_array['status'] = "newuser";

                                                        $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                                                        $json_array['error_msg'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                                                        $json = json_encode($json_array);
                                                        $objDB->Conn->CompleteTrans();
                                                        echo $json;
                                                        exit;
                                                }
                                        } else {
                                                $json_array['status'] = "newuser";
                                                $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                                                $json_array['error_msg'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                                                $objDB->Conn->CompleteTrans();
                                                $json = json_encode($json_array);
                                                echo $json;
                                                exit;
                                        }

                                        // --- For data entry in merchant_subscribs
//			$camp_array['id']=$campaign_id;		
//			$RS_campaign  = $this->Conn->Execute("select * from campaigns where id =".$campaign_id);
//			$m_id = $RS_campaign->fields['created_by'];
//			
//			
//			
//			$Sql = "SELECT * FROM merchant_subscribs WHERE merchant_id='$m_id' AND user_id='$customer_id'";
//				$RS_ms = $this->Conn->Execute($Sql);
//				if($RS_ms->RecordCount()<=0){
//					
//					$Sql = "INSERT INTO merchant_subscribs SET merchant_id='$m_id', user_id='$customer_id'";
//					$this->Conn->Execute($Sql);
//				}
                                        // ---
                                } else {
                                        $json_array['status'] = "ended";
                                        $json_array['campaign_end_message'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                        $json_array['error_msg'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                        $json = json_encode($json_array);
                                        $objDB->Conn->CompleteTrans();
                                        echo $json;
                                        exit;
                                }
                        } else {
                                // start walkin and normal deal check already redeem offer left condition 25 07 2014
                                //if($_REQUEST['is_walkin']==1)
                                //{
                                //echo "walkin";
                                $array_where_camp2 = array();
                                $array_where_camp2['campaign_id'] = $campaign_id;
                                $array_where_camp2['customer_id'] = $cid;
                                $array_where_camp2['location_id'] = $location_id;
                                $RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
                                $reserved = $RS_cust_camp->RecordCount();

                                $array_where_camp = array();
                                $array_where_camp['campaign_id'] = $campaign_id;
                                $array_where_camp['customer_id'] = $cid;
                                $array_where_camp['referred_customer_id'] = 0;
                                $array_where_camp['location_id'] = $location_id;
                                $RS_camp = $objDB->Show("reward_user", $array_where_camp);
                                $redeemed = $RS_camp->RecordCount();

                                $array_where_camp1 = array();
                                $array_where_camp1['campaign_id'] = $campaign_id;
                                $array_where_camp1['location_id'] = $location_id;
                                $RS_camp1 = $objDB->Show("campaign_location", $array_where_camp1);

                                $array_where_camp3 = array();
                                $array_where_camp3['id'] = $campaign_id;
                                $RS_camp3 = $objDB->Show("campaigns", $array_where_camp3);

                                //echo "reserved = ".$reserved." redeemed = ".$redeemed." offer_left =".$RS_camp1->fields['offers_left']." number_of_use =".$RS_camp3->fields['number_of_use'];

                                if ($RS_cust_camp->RecordCount() > 0 && $RS_camp->RecordCount() > 0 && $RS_camp3->fields['number_of_use'] == 1) {
                                        $json_array['status'] = "false";
                                        $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_Already_Redeemed"];
                                        $json_array['error_msg'] = $client_msg["campaign"]["Msg_Already_Redeemed"];
                                        $json = json_encode($json_array);
                                        $objDB->Conn->CompleteTrans();
                                        echo $json;
                                        exit;
                                } else if ($RS_cust_camp->RecordCount() > 0 && $RS_camp->RecordCount() > 0 && ($RS_camp3->fields['number_of_use'] == 2 || $RS_camp3->fields['number_of_use'] == 3) && $RS_camp1->fields['offers_left'] == 0) {
                                        $json_array['status'] = "false";
                                        $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                        $json_array['error_msg'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                        $json = json_encode($json_array);
                                        $objDB->Conn->CompleteTrans();
                                        echo $json;
                                        exit;
                                } else if ($RS_cust_camp->RecordCount() == 0 && $RS_camp->RecordCount() == 0 && $RS_camp1->fields['offers_left'] == 0) {
                                        $json_array['status'] = "false";
                                        $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                        $json_array['error_msg'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                        $json = json_encode($json_array);
                                        $objDB->Conn->CompleteTrans();
                                        echo $json;
                                        exit;
                                }

                                //}
                                // end walkin and normal deal check already redeem offer left condition 25 07 2014

                                /* $br_sql = "Select coupon_code from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
                                  $br_rs =  $objDB->Conn->Execute($br_sql); */
                                $br_rs = $objDB->Conn->Execute("Select coupon_code from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?", array($customer_id, $campaign_id, $lid));
                                $br = $br_rs->fields['coupon_code'];

                                if ($RS->fields['activation_status'] == 0) {
                                        /* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
                                          $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
                                        $RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?", array($campaign_id, $lid));
                                        $offers_left = $RS_num_activation->fields['offers_left'];
                                        $used_campaign = $RS_num_activation->fields['used_offers'];
                                        $share_flag = 1;
                                        if ($offers_left > 0) {
                                                /* $Sql_max_is_walkin = "SELECT is_walkin , level ,new_customer  from campaigns WHERE id=".$campaign_id;
                                                  $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin); */
                                                $RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin , level ,new_customer  from campaigns WHERE id=?", array($campaign_id));
                                                if ($RS_max_is_walkin->fields['new_customer'] == 1) {
                                                        /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$customer_id." and location_id=".$lid.") ";
                                                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                                        $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($customer_id, $lid));
                                                        if ($subscibed_store_rs->RecordCount() == 0) {
                                                                $share_flag = 1;
                                                        } else {
                                                                $share_flag = 0;
                                                        }
                                                }

                                                /* check whether new customer for this store */
                                                $allow_for_reserve = 0;
                                                $is_new_user = 0;
                                                /*                                                 * ************* ************************ */
                                                /* 					$sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$customer_id." and location_id=".$lid.") ";
                                                  $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
                                                $Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($customer_id, $lid));
                                                if ($Rs_is_new_customer->RecordCount() == 0) {
                                                        $is_new_user = 1;
                                                } else {
                                                        $is_new_user = 0;
                                                }
                                                /*                                                 * ************* ************************ */

                                                if ($is_new_user == 0) {
                                                        /* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$lid;
                                                          $RS_campaign_groups = $objDB->Conn->Execute($sql); */
                                                        $RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?", array($campaign_id, $lid));
                                                        $c_g_str = "";
                                                        $cnt = 1;

                                                        $is_it_in_group = 0;
                                                        if ($RS_max_is_walkin->fields['level'] == 0) {
                                                                if ($RS_max_is_walkin->fields['is_walkin'] == 0) {
                                                                        if ($RS_campaign_groups->RecordCount() > 0) {
                                                                                while ($Row_campaign = $RS_campaign_groups->FetchRow()) {
                                                                                        $c_g_str = $Row_campaign['group_id'];
                                                                                        if ($cnt != $RS_campaign_groups->RecordCount()) {
                                                                                                $c_g_str .= ",";
                                                                                        }
                                                                                }
                                                                                /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$customer_id."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";

                                                                                  $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
                                                                                $RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?)  )", array($customer_id, $c_g_str));
                                                                                while ($Row_Check_Cust_group = $RS_check_s->FetchRow()) {
                                                                                        /* $query = "Select * from merchant_subscribs where  user_id='".$customer_id."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                                                          $RS_query = $objDB->Conn->Execute($query); */
                                                                                        $RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ", array($customer_id, $Row_Check_Cust_group['group_id'], $c_g_str));

                                                                                        if ($RS_query->RecordCount() > 0) {
                                                                                                $is_it_in_group = 1;
                                                                                        }
                                                                                }
                                                                                if ($is_it_in_group == 1) {
                                                                                        $allow_for_reserve = 1;
                                                                                } else {
                                                                                        $allow_for_reserve = 0;
                                                                                }
                                                                        } else {
                                                                                $allow_for_reserve = 0;
                                                                        }
                                                                } else {
                                                                        // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                                                        /* $query = "Select * from merchant_subscribs where  user_id=".$customer_id." and group_id=( select id from merchant_groups mg where mg.location_id=".$lid." and mg.private =1 ) "; */
                                                                        $RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =?) ", array($customer_id, $lid, 1));

                                                                        $RS_all_user_group = $objDB->Conn->Execute($query);
                                                                        if ($RS_all_user_group->RecordCount() > 0) {
                                                                                $allow_for_reserve = 1;
                                                                        } else {
                                                                                $allow_for_reserve = 0;
                                                                        }
                                                                }
                                                        } else {
                                                                //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$customer_id."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                                                $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='" . $customer_id . "' AND group_id in( select  id from merchant_groups where location_id  =" . $lid . "  )";
                                                                $allow_for_reserve = 1;
                                                        }
                                                } else {
                                                        $allow_for_reserve = 1;
                                                }

                                                /* for checking whether customer in campaign group */
                                                if ($share_flag == 1) {
                                                        if ($allow_for_reserve == 1) {
                                                                //$Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
                                                                //customer_id='$customer_id', campaign_id='$campaign_id' , location_id=".$lid;
                                                                /* $Sql = "Update customer_campaigns SET activation_status=1 where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$lid;

                                                                  $objDB->Conn->Execute($Sql); */
                                                                $objDBWrt->Conn->Execute("Update customer_campaigns SET activation_status=? where customer_id=? and campaign_id=? and location_id=?", array(1, $customer_id, $campaign_id, $lid));
                                                                $json_array['campaign_id'] = $campaign_id;
                                                                $json_array['location_id'] = $lid;

                                                                //
                                                                /* $br_sql = "Select coupon_code from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
                                                                  $br_rs =  $objDB->Conn->Execute($br_sql); */
                                                                $br_rs = $objDB->Conn->Execute("Select coupon_code from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?", array($customer_id, $campaign_id, $lid));

                                                                $br = $br_rs->fields['coupon_code'];
                                                                /* $select_coupon_code = "update coupon_codes set active= 1 where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
                                                                  $objDB->Conn->Execute($select_coupon_code); */
                                                                $objDBWrt->Conn->Execute("update coupon_codes set active= ? where customer_id=? and customer_campaign_code=? and location_id=?", array(1, $customer_id, $campaign_id, $lid));

                                                                ///
                                                                //
							//Make entry in subscribed_stre table for first time subscribe to loaction
                                                                /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                                                  $RS_group = $objDB->Conn->Execute($sql_group); */
                                                                $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?", array($lid, 1));

                                                                /* $sql_chk ="select * from subscribed_stores where customer_id= ".$customer_id." and location_id=".$lid;
                                                                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                                                $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?", array($customer_id, $lid));

                                                                if ($subscibed_store_rs->RecordCount() == 0) {
                                                                        /* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$customer_id." ,location_id=".$lid." ,subscribed_date='".$timestamp."' ,subscribed_status=1";
                                                                          $objDB->Conn->Execute($insert_subscribed_store_sql); */
                                                                        $objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=? ,location_id=? ,subscribed_date=? ,subscribed_status=?", array($customer_id, $lid, $timestamp, 1));
                                                                } else {
                                                                        if ($subscibed_store_rs->fields['subscribed_status'] == 0) {
                                                                                /* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$customer_id." and location_id=".$lid;
                                                                                  $objDB->Conn->Execute($up_subscribed_store); */
                                                                                $objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=?  where  customer_id=? and location_id=?", array(1, $customer_id, $lid));
                                                                        }
                                                                }
                                                                // If campaign is walking deal then make entry in coupon_codes table //

                                                                /* $RS_user_subscribe = "SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ".$lid." and private = 1) and user_id = ".$customer_id;
                                                                  $check_subscribe = $objDB->Conn->Execute($RS_user_subscribe); */

                                                                $check_subscribe = $objDB->Conn->Execute("SELECT * from merchant_subscribs where group_id in ( select  id from merchant_groups where location_id = ? and private = ?) and user_id = ?", array($lid, 1, $customer_id));

                                                                if ($check_subscribe->RecordCount() == 0) {
                                                                        /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$lid." and private = 1";
                                                                          $RS_group = $objDB->Conn->Execute($sql_group); */
                                                                        $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?", array($lid, 1));

                                                                        if ($RS_group->RecordCount() > 0) {
                                                                                /* $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;

                                                                                  $RS_user_group =$objDB->Conn->Execute($sql_user_group); */
                                                                                $RS_user_group = $objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id =? ", array($RS_group->fields['merchant_id'], $RS_group->fields['id'], $customer_id));

                                                                                if ($RS_user_group->RecordCount() <= 0) {
                                                                                        /* $insert_sql = "INSERT INTO merchant_subscribs SET merchant_id =".$RS_group->fields['merchant_id']." , group_id = ".$RS_group->fields['id']." , user_id = ".$customer_id;
                                                                                          $objDB->Conn->Execute($insert_sql); */
                                                                                        $objDBWrt->Conn->Execute("INSERT INTO merchant_subscribs SET merchant_id =? , group_id =? , user_id = ?", array($RS_group->fields['merchant_id'], $RS_group->fields['id'], $customer_id));
                                                                                }
                                                                        }
                                                                }
                                                                //
                                                        } else {
                                                                $json_array['status'] = "newuser";
                                                                $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                                                                $json_array['error_msg'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                                                                $json = json_encode($json_array);
                                                                $objDB->Conn->CompleteTrans();
                                                                echo $json;
                                                                exit;
                                                        }
                                                } else {
                                                        $json_array['status'] = "newuser";
                                                        $json_array['campaign_for_new_user'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                                                        $json_array['error_msg'] = $client_msg["campaign"]["Msg_This_Offer_Limited_Customers"];
                                                        $json = json_encode($json_array);
                                                        $objDB->Conn->CompleteTrans();
                                                        echo $json;
                                                        exit;
                                                }

                                                // --- For data entry in merchant_subscribs
                                                //			$camp_array['id']=$campaign_id;		
                                                //			$RS_campaign  = $this->Conn->Execute("select * from campaigns where id =".$campaign_id);
                                                //			$m_id = $RS_campaign->fields['created_by'];
                                                //			
                                                //			
                                                //			
                                                //			$Sql = "SELECT * FROM merchant_subscribs WHERE merchant_id='$m_id' AND user_id='$customer_id'";
                                                //				$RS_ms = $this->Conn->Execute($Sql);
                                                //				if($RS_ms->RecordCount()<=0){
                                                //					
                                                //					$Sql = "INSERT INTO merchant_subscribs SET merchant_id='$m_id', user_id='$customer_id'";
                                                //					$this->Conn->Execute($Sql);
                                                //				}
                                                // ---
                                        } else {
                                                $json_array['status'] = "ended";
                                                $json_array['campaign_end_message'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                                $json_array['error_msg'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                                                $json = json_encode($json_array);
                                                $objDB->Conn->CompleteTrans();
                                                echo $json;
                                                exit;
                                        }
                                }


                                /* $Sql_num_activation = "Select offers_left from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
                                  $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
                                $RS_num_activation = $objDB->Conn->Execute("Select offers_left from campaign_location where campaign_id=? and location_id =?", array($campaign_id, $lid));
                                $offers_left = $RS_num_activation->fields['offers_left'];

                                //$json_array['status'] = "false";
                                //$json_array['error_msg'] = $client_msg["campaign"]["Msg_already_reserve"];
                                $json_array['status'] = "true";
                                $json_array['reserve'] = 1;
                                $json_array['barcode'] = "showbarcode.php?br=" . $br;
                                $json_array['offers_left'] = $offers_left;
                                $json = json_encode($json_array);
                                $objDB->Conn->CompleteTrans();
                                echo $json;
                                exit;
                        }
                } else {


                        $json_array['status'] = "ended";
                        $json_array['campaign_end_message'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                        $json_array['error_msg'] = $client_msg["campaign"]["Msg_offer_left_zero"];
                        $json = json_encode($json_array);
                        $objDB->Conn->CompleteTrans();
                        echo $json;
                        exit;
                }
        }

        /* $Sql_num_activation = "Select offers_left from campaign_location where campaign_id=".$campaign_id." and location_id =".$lid." ";
          $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
        $RS_num_activation = $objDB->Conn->Execute("Select offers_left from campaign_location where campaign_id=? and location_id =?", array($campaign_id, $lid));
        $offers_left = $RS_num_activation->fields['offers_left'];


        $json_array['offers_left'] = $offers_left;
        $json_array['barcode'] = "showbarcode.php?br=" . $br;
        $json_array['status'] = "true";
        $json_array['loginstatus'] = "true";
        $json = json_encode($json_array);
        $objDB->Conn->CompleteTrans();
        echo $json;
        exit;
}

/**
 * @uses unreserv deal
 * @param customer_id,timestamp,campaign_id
 * @return string
 */

if (isset($_REQUEST['btnunreservedeal'])) {
        $campaign_id = $_REQUEST['campaign_id'];
        $location_id = $_REQUEST['location_id'];
        $customer_id = $_REQUEST['customer_id'];

        $storearray = array();
        $storearray['location_id'] = $location_id;
        $RSstoe = $objDB->Show("campaign_location", $storearray);
        if ($RSstoe->RecordCount() <= 0) {
                $json_array['status'] = "false";
                $json_array['message'] = "There is no campaign for this store";
                $json = json_encode($json_array);
                header("Location: my-deals.php");
                exit();
        } else {

                /* $Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=".$location_id;
                  $RS = $objDB->Conn->Execute($Sql); */
                $RS = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id=?", array($customer_id, $campaign_id, $location_id));

                if ($RS->RecordCount() == 0) {
                        /* $Sql_num_activation = "Select offers_left from campaign_location where campaign_id=".$campaign_id." and location_id =".$location_id." ";
                          $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
                        $RS_num_activation = $objDB->Conn->Execute("Select offers_left from campaign_location where campaign_id=? and location_id =?", array($campaign_id, $location_id));
                        $offers_left = $RS_num_activation->fields['offers_left'];

                        $json_array['status'] = "please_reserve";
                        $json_array['message'] = "Please reserve campaign";
                        $json_array['offers_left'] = $offers_left;

                        $json = json_encode($json_array);
                        echo $json;
                        exit;
                }

                /* $sql = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$location_id ;
                  $RS_cc1 =$objDB->Conn->Execute($sql); */
                $RS_cc1 = $objDB->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?", array($customer_id, $campaign_id, $location_id));

                /* $sql = "Select * from coupon_redeem where coupon_id in (".$RS_cc1->fields['id'].")";
                  $RS_c =$objDB->Conn->Execute($sql); */
                $RS_c = $objDB->Conn->Execute("Select * from coupon_redeem where coupon_id in (?)", array($RS_cc1->fields['id']));

                if ($RS_c->RecordCount() == 0) {
                        /* $sql = "select * from customer_campaigns where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$location_id ;
                          $RS_cc =$objDB->Conn->Execute($sql); */
                        $RS_cc = $objDB->Conn->Execute("select * from customer_campaigns where customer_id=? and campaign_id=? and location_id=?", array($customer_id, $campaign_id, $location_id));

                        if ($RS_cc->RecordCount() > 0) {
                                /* $Sql = "DELETE FROM customer_campaigns where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$location_id ;
                                  $objDB->Conn->Execute($Sql); */
                                $objDBWrt->Conn->Execute("DELETE FROM customer_campaigns where customer_id=? and campaign_id=? and location_id=" . $location_id, array($customer_id, $campaign_id));
                        }
                        if ($RS_cc1->RecordCount() > 0) {
                                // $Sql = "UPDATE coupon_codes SET active=0 where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$location_id ;
                                /* $Sql = "DELETE FROM coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$location_id ;
                                  $objDB->Conn->Execute($Sql); */
                                $objDBWrt->Conn->Execute("DELETE FROM coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?", array($customer_id, $campaign_id, $location_id));
                                /* $update_num_activation = "Update campaign_location set offers_left=offers_left+1 , used_offers=used_offers-1 where campaign_id=".$campaign_id." and location_id =".$location_id;
                                  $objDB->Conn->Execute($update_num_activation); */
                                $objDBWrt->Conn->Execute("Update campaign_location set offers_left=offers_left+1 , used_offers=used_offers-1 where campaign_id=? and location_id =?", array($campaign_id, $location_id));
                        }
                } else {
                        /* 			$Sql = "select * from campaigns where id=".$campaign_id;
                          $RS_cam=$objDB->Conn->Execute($Sql); */
                        $RS_cam = $objDB->Conn->Execute("select * from campaigns where id=?", array($campaign_id));
                        if ($RS_cam->fields['number_of_use'] == 1) {
                                $json_array['status'] = "false";
                                $json_array['message'] = $client_msg['my_deal']['msg_Alreday_Redeemed'];
                                $json = json_encode($json_array);
                                echo $json;
                                exit();
                        } else {
                                /* $Sql = "UPDATE coupon_codes SET active=0 where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$location_id ;
                                  $objDB->Conn->Execute($Sql); */
                                $objDBWrt->Conn->Execute("UPDATE coupon_codes SET active=? where customer_id=? and customer_campaign_code=? and location_id=?", array(0, $customer_id, $campaign_id, $location_id));
                                /* $Sql = "UPDATE customer_campaigns SET activation_status=0 where customer_id=".$customer_id." and campaign_id=".$campaign_id." and location_id=".$location_id ;
                                  $objDB->Conn->Execute($Sql); */
                                $objDBWrt->Conn->Execute("UPDATE customer_campaigns SET activation_status=? where customer_id=? and campaign_id=? and location_id=?", array(0, $customer_id, $campaign_id, $location_id));
                        }
                }
                // remove coupon codes //
                $json_array['status'] = "true";

                /* $Sql_num_activation = "Select offers_left from campaign_location where campaign_id=".$campaign_id." and location_id =".$location_id." ";
                  $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
                $RS_num_activation = $objDB->Conn->Execute("Select offers_left from campaign_location where campaign_id=? and location_id =?", array($campaign_id, $location_id));
                $offers_left = $RS_num_activation->fields['offers_left'];

                $json_array['offer_left'] = $offers_left;

                $json = json_encode($json_array);
                echo $json;
                //header("Location:campaign.php?campaign_id=".$_REQUEST['campaign_id']."&l_id=".$location_id);
                exit();
        }
}

/**
 * @uses get activation code
 * @param customer_id,location_id,campaign_id
 * @return string
 */

if (isset($_REQUEST['getActivationCode'])) {
        $json_array = array();
        $customer_id = $_REQUEST["customer_id"];
        $campaign_id = $_REQUEST["campaign_id"];
        $lid = $_REQUEST["location_id"];
        $location_id = $_REQUEST["location_id"];

        /* $Sql = "SELECT * FROM customer_campaigns WHERE customer_id='$customer_id' AND campaign_id='$campaign_id' AND location_id=".$location_id;
          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id=?", array($customer_id, $campaign_id, $location_id));

        if ($RS->RecordCount() == 0) {
                /* $Sql_num_activation = "Select offers_left from campaign_location where campaign_id=".$campaign_id." and location_id =".$location_id." ";
                  $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
                $RS_num_activation = $objDB->Conn->Execute("Select offers_left from campaign_location where campaign_id=? and location_id =? ", array($campaign_id, $location_id));
                $offers_left = $RS_num_activation->fields['offers_left'];

                $json_array['status'] = "please_reserve";
                $json_array['message'] = "Please reserve campaign";
                $json_array['offers_left'] = $offers_left;

                $json = json_encode($json_array);
                echo $json;
                exit;
        }

        /* 	$br_sql = "Select coupon_code from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$campaign_id." and location_id=".$lid."  ";
          $br_rs =  $objDB->Conn->Execute($br_sql); */
        $br_rs = $objDB->Conn->Execute("Select coupon_code from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?", array($customer_id, $campaign_id, $lid));
        $br = $br_rs->fields['coupon_code'];
        if ($br_rs->RecordCount() > 0) {
                $json_array['barcode'] = WEB_PATH . "/showbarcode.php?br=" . $br;
                $json_array['status'] = "true";
                $json = json_encode($json_array);
                echo $json;
                exit;
        } else {
                $json_array['status'] = "false";
                $json = json_encode($json_array);
                echo $json;
                exit;
        }
}

/* Facebook sharing process */

/**
 * @uses facebook share
 * @param customer_id,campaign_id
 * @return string
 */
if (isset($_REQUEST['facebook_share'])) {
        require 'fb-sdk/src/facebook.php';
        include_once("fb-sdk/src/facebook_secret.php");
        $facebook = new Facebook(array(
            'appId' => facebookappId,
            'secret' => facebooksecret,
        ));
        if ($_REQUEST['customer_id'] != "") {

                $where_clause = $array_values = array();
                $cam_id_where_clause = array();

                $array = $json_array = $where_clause = array();


                $where_clause['id'] = $_REQUEST['customer_id'];

                $cam_id_where_clause['id'] = $_REQUEST['campaign_id'];

                $RS = $objDB->Show("customer_user", $where_clause);

                $RS_cam = $objDB->Show("campaigns", $cam_id_where_clause);

                $Row_cam = $RS_cam->FetchRow();

                /* $share_sql = "select ca.title,ca.description,ca.business_logo,ca.campaign_tag,ca.redeem_rewards,ca.referral_rewards,la.location_name,la.address,la.city,la.state,la.country from campaigns as ca,locations as la where ca.id='".$_REQUEST['campaign_id']."' and la.id='".$_REQUEST['location_id']."'";
                  $share_rs =  $objDB->Conn->Execute($share_sql); */
                $share_rs = $objDB->Conn->Execute("select ca.title,ca.description,ca.business_logo,ca.campaign_tag,ca.redeem_rewards,ca.referral_rewards,la.location_name,la.address,la.city,la.state,la.country from campaigns as ca,locations as la where ca.id=? and la.id=?", array($_REQUEST['campaign_id'], $_REQUEST['location_id']));
//$br = $share_rs->fields['coupon_code'];


                $address = $share_rs->fields['address'] . "," . $share_rs->fields['city'] . "," . $share_rs->fields['state'] . "," . $share_rs->fields['country'];


                $Row = $RS->FetchRow();

                if ($Row['facebook_user_id'] != "" || $Row['access_token'] != "") {
                        try {

                                $permissions = $facebook->api("/" . $Row['facebook_user_id'] . "/permissions");

                                if (array_key_exists('publish_stream', $permissions['data'][0])) {

                                        $attachment = array(
                                            'name' => $share_rs->fields['title'],
                                            'access_token' => $Row['access_token'],
                                            'link' => "https://www.scanflip.com/register.php?campaign_id=" . $_REQUEST['campaign_id'] . "&l_id=" . $_REQUEST['location_id'] . "&share=true&customer_id=MTY4",
                                            'description' => strip_tags($share_rs->fields['description']),
                                            'picture' => ASSETS_IMG."/m/campaign/" . $share_rs->fields['business_logo'],
                                            'message' => "Great Offer From " . $share_rs->fields['location_name'] . "." . "Available at " . $address . "." . $share_rs->fields['location_name'] . " giving " . $share_rs->fields['redeem_rewards'] . " #scanflip points on every redemption." . " #" . $share_rs->fields['campaign_tag'] . " "
                                        );

                                        $status = $facebook->api("/" . $Row['facebook_user_id'] . "/feed", "post", $attachment);

                                        //echo "success";
                                        $json_array = array();
                                        $json_array['status'] = "true";
                                        $json_array['response_code'] = "1";
                                        $json_array['message'] = "Campaign has been shared successfully";
                                        $json = json_encode($json_array);
                                        echo $json;
                                        exit();
                                } else {

                                        //ehader("Location:{$facebook->getLoginUrl(array('scope' => 'publish_stream'))}");
                                        $params = array(
                                            'redirect_uri' => $_REQUEST['redirect_url'],
                                            'scope' => 'publish_actions,publish_stream,read_stream,email'
                                        );
                                        //echo "error"."|".$loginUrl = $facebook->getLoginUrl($params);
                                        //echo "notpermission";
                                        $json_array = array();
                                        $json_array['status'] = "false";
                                        $json_array['response_code'] = "2";
                                        $json_array['message'] = "Please grant ScanFlip permission.";
                                        $json = json_encode($json_array);
                                        echo $json;
                                        exit();
                                }
                        } catch (FacebookApiException $x) {
                                //echo $x;

                                $error_type = explode(":", $x);
                                $params = array(
                                    'redirect_uri' => $_REQUEST['redirect_url'],
                                    'scope' => 'publish_actions,publish_stream,read_stream,email'
                                );
                                $loginUrl = $facebook->getLoginUrl($params);
                                //echo $error_type[0]."|".$loginUrl;

                                $json_array = array();
                                $json_array['status'] = "false";
                                $json_array['response_code'] = "3";
                                $json_array['message'] = "Please authorize ScanFlip to post to your friends on your behalf.";
                                $json = json_encode($json_array);
                                echo $json;
                                exit();
                        }
                } else {

                        $json_array = array();
                        $json_array['status'] = "false";
                        $json_array['response_code'] = "4";
                        $json_array['message'] = "Not available facebook user id and access token";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        }
}

/**
 * @uses facebook login share
 * @param customer_id,campaign_id
 * @return string
 */
if (isset($_REQUEST['facebook_login_share'])) {

        require 'fb-sdk/src/facebook.php';
        include_once("fb-sdk/src/facebook_secret.php");
        $facebook = new Facebook(array(
            'appId' => facebookappId,
            'secret' => facebooksecret,
        ));
        if ($_REQUEST['facebook_login_share'] == "yes") {


                $where_clause = $array_values = array();
                $cam_id_where_clause = array();

                $array = $json_array = $where_clause = array();


                $where_clause['id'] = $_REQUEST['customer_id'];
                $RS = $objDB->Show("customer_user", $where_clause);
                $Row = $RS->FetchRow();

                //update code
                if ($Row['facebook_user_id'] != $_REQUEST['facebook_user_id']) {
                        /* $share_sql_update = "update customer_user set facebook_user_id='".$_REQUEST['facebook_user_id']."',access_token='".$_REQUEST['access_token']."',facebook_email_id='".$_REQUEST['facebook_email_id']."',profile_pic='".$_REQUEST['user_profile_pic']."' where id=".$_REQUEST['customer_id'];

                          $objDB->Conn->Execute($share_sql_update); */
                        $objDBWrt->Conn->Execute("update customer_user set facebook_user_id=?,access_token=?,facebook_email_id=?,profile_pic=? where id=?", array($_REQUEST['facebook_user_id'], $_REQUEST['access_token'], $_REQUEST['facebook_email_id'], $_REQUEST['user_profile_pic'], $_REQUEST['customer_id']));
                }
                //End of update code
                //update code
                if ($Row['access_token'] == "") {
                        /* $share_sql_update = "update customer_user set access_token='".$_REQUEST['access_token']."' where id=".$_REQUEST['customer_id'];

                          $objDB->Conn->Execute($share_sql_update); */
                        $objDBWrt->Conn->Execute("update customer_user set access_token=? where id=?", array($_REQUEST['access_token'], $_REQUEST['customer_id']));
                }
                //End of update code

                /* $share_sql = "select ca.title,ca.description,ca.business_logo,ca.campaign_tag,ca.redeem_rewards,ca.referral_rewards,la.location_name,la.address,la.city,la.state,la.country from campaigns as ca,locations as la where ca.id='".$_REQUEST['campaign_id']."' and la.id='".$_REQUEST['location_id']."'";
                  $share_rs =  $objDB->Conn->Execute($share_sql); */
                $share_rs = $objDB->Conn->Execute("select ca.title,ca.description,ca.business_logo,ca.campaign_tag,ca.redeem_rewards,ca.referral_rewards,la.location_name,la.address,la.city,la.state,la.country from campaigns as ca,locations as la where ca.id=? and la.id=?", array($_REQUEST['campaign_id'], $_REQUEST['location_id']));



                $address = $share_rs->fields['address'] . "," . $share_rs->fields['city'] . "," . $share_rs->fields['state'] . "," . $share_rs->fields['country'];

                $tag_main = "";
                if ($share_rs->fields['campaign_tag'] != "") {
                        $fb_campaign_tag_temp = explode(",", $share_rs->fields['campaign_tag']);
                        $tag_count = count($fb_campaign_tag_temp);



                        for ($i = 0; $i < $tag_count; $i++) {
                                $tag_main.="#" . $fb_campaign_tag_temp[$i] . " ";
                        }
                }
                if ($share_rs->fields['business_logo'] != "") {
                        // $img_src=WEB_PATH."/merchant/images/logo/".$RS[0]->business_logo; 
                        $fb_img_share = ASSETS_IMG . "/m/campaign/" . $share_rs->fields['business_logo'];
                } else {
                        $fb_img_share = ASSETS_IMG . "/c/Merchant_Offer.png";
                }




                if ($Row['facebook_user_id'] != "" || $Row['access_token'] != "") {
                        try {



                                $permissions = $facebook->api("/" . $Row['facebook_user_id'] . "/permissions");

                                if (array_key_exists('publish_stream', $permissions['data'][0])) {

                                        $attachment = array(
                                            'name' => $share_rs->fields['title'],
                                            'access_token' => $Row['access_token'],
                                            'link' => "https://www.scanflip.com/register.php?campaign_id=" . $_REQUEST['campaign_id'] . "&l_id=" . $_REQUEST['location_id'] . "&share=true&customer_id=MTY4",
                                            'description' => strip_tags($share_rs->fields['description']),
                                            'picture' => $fb_img_share,
                                            'message' => "Great Offer From " . $share_rs->fields['location_name'] . "." . "Available at " . $address . "." . $share_rs->fields['location_name'] . " giving " . $share_rs->fields['redeem_rewards'] . " #scanflip points on every redemption." . $tag_main . " "
                                        );

                                        $status = $facebook->api("/" . $Row['facebook_user_id'] . "/feed", "post", $attachment);

                                        echo "success";

                                        $json_array = array();
                                        $json_array['status'] = "true";
                                        $json_array['response_code'] = "1";
                                        $json_array['message'] = "Campaign has been shared successfully";
                                        $json = json_encode($json_array);
                                        echo $json;
                                        exit();
                                } else {

                                        //ehader("Location:{$facebook->getLoginUrl(array('scope' => 'publish_stream'))}");
                                        $params = array(
                                            'redirect_uri' => $_REQUEST['redirect_url'],
                                            'scope' => 'publish_actions,publish_stream,read_stream,email'
                                        );
                                        //echo "error"."|".$loginUrl = $facebook->getLoginUrl($params);
                                        //echo "notpermission";
                                        $json_array = array();
                                        $json_array['status'] = "false";
                                        $json_array['response_code'] = "2";
                                        $json_array['message'] = "Please grant ScanFlip permission.";
                                        $json = json_encode($json_array);
                                        echo $json;
                                        exit();
                                }
                        } catch (FacebookApiException $x) {
                                //echo $x;

                                $error_type = explode(":", $x);
                                $params = array(
                                    'redirect_uri' => $_REQUEST['redirect_url'],
                                    'scope' => 'publish_actions,publish_stream,read_stream,email'
                                );
                                $loginUrl = $facebook->getLoginUrl($params);
                                //echo $error_type[0]."|".$loginUrl;

                                $json_array = array();
                                $json_array['status'] = "false";
                                $json_array['response_code'] = "3";
                                $json_array['message'] = "Please authorize ScanFlip to post to your friends on your behalf.";
                                $json = json_encode($json_array);
                                echo $json;
                                exit();
                        }
                } else {

                        $json_array = array();
                        $json_array['status'] = "false";
                        $json_array['response_code'] = "4";
                        $json_array['message'] = "Not available facebook user id and access token";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        } else {
                $json_array = array();
                $json_array['status'] = "false";
                $json_array['response_code'] = "5";
                $json_array['message'] = "You are not share any campaign";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

/* End Facebook process */
/**
 * @uses get country wise state
 * @param country
 * @return string
 */
if (isset($_REQUEST['getcountrywisestate'])) {
        $json_array = array();
        $country = $_REQUEST['country'];
        if ($country == "USA") {
                $state = "AK,AL,AP,AR,AS,AZ,CA,CO,CT,DC,DE,FL,FM,GA,GS,GU,HI,IA,ID,IL,IN,KS,KY,LA,MA,MD,ME,MH,MI,MN,MO,MP,MS,MT,NC,ND,NE,NH,NJ,NM,NV,NY,OH,OK,OR,PA,PR,PW,RI,SC,SD,TN,TX,UT,VA,VI,VT,WA,WI,WV,WY";
                $json_array['status'] = "true";
                $json_array['state'] = $state;
                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {
                $state = "AB,BC,LB,MB,NB,NF,NS,NT,NU,ON,PE,PQ,QB,QC,SK,YT";
                $json_array['status'] = "true";
                $json_array['state'] = $state;
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}
/**
 * @uses get location detail
 * @param mobile_uinque_id,location_id,qrcode
 * @return string
 */
if (isset($_REQUEST['get_location_details'])) {
        /*         * ** check Whether it is unique scan *** */
        if (isset($_REQUEST['mobile_uinque_id'])) {
                $locationid = $_REQUEST['location_id'];
                $campaignid = 0;
                $qrcode = $_REQUEST['qrcode'];
                $mobile_id = $_REQUEST['mobile_uinque_id'];
                /* 		$Sql  = "SELECT * FROM locations  where id=".$_REQUEST['location_id'];	
                  $RS_location = $objDB->Conn->Execute($Sql); */
                $RS_location = $objDB->Conn->Execute("SELECT * FROM locations  where id=?", array($_REQUEST['location_id']));

                $timezone = $RS_location->fields['timezone'];
                $dt_sql = "SElect CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR('" . $timezone . "',1, POSITION(',' IN '" . $timezone . "')-1)) dte ";

                /* $sql_qrcode = "select id from qrcodes where qrcode='".$qrcode."' ";
                  $RS_qrcode = $objDB->Conn->Execute($sql_qrcode ); */
                $RS_qrcode = $objDB->Conn->Execute("select id from qrcodes where qrcode=?", array($qrcode));
                $q_id = $RS_qrcode->fields['id'];
                $RS_dt = $objDB->Conn->Execute($dt_sql);
                /* $sql = "select * from scan_qrcode where location_id=".$_REQUEST['location_id']." and campaign_id=". $campaignid." and qrcode_id=".$q_id." and  mobile_uinque_id='".$_REQUEST['mobile_uinque_id']."' ";
                  //echo $sql;
                  $RS_scans = $objDB->Conn->Execute($sql); */
                $RS_scans = $objDB->Conn->Execute("select * from scan_qrcode where location_id=? and campaign_id=? and qrcode_id=? and  mobile_uinque_id=? ", array($_REQUEST['location_id'], $campaignid, $q_id, $_REQUEST['mobile_uinque_id']));


                if ($RS_scans->RecordCount() == 0) {
                        if (isset($_REQUEST['customer_id'])) {
                                /* $sql= "update scan_qrcode set user_id=".$_REQUEST['customer_id']." where mobile_uinque_id='".$_REQUEST['mobile_uinque_id']."' ";
                                  $objDB->Conn->Execute($sql); */
                                $objDBWrt->Conn->Execute("update scan_qrcode set user_id=? where mobile_uinque_id=?", array($_REQUEST['customer_id'], $_REQUEST['mobile_uinque_id']));
                        } else {
                                
                        }
                        $uid = 1;
                } else {
                        $uid = 0;
                        if (isset($_REQUEST['customer_id'])) {
                                /* $sql= "update scan_qrcode set user_id=".$_REQUEST['customer_id']." where mobile_uinque_id='".$_REQUEST['mobile_uinque_id']."' ";
                                  $objDB->Conn->Execute($sql); */
                                $objDBWrt->Conn->Execute("update scan_qrcode set user_id=? where mobile_uinque_id=?", array($_REQUEST['customer_id'], $_REQUEST['mobile_uinque_id']));
                        }
                }
                if (isset($_REQUEST['customer_id'])) {
                        $custid = $_REQUEST['customer_id'];
                } else {
                        $custid = 0;
                }
                $insert_array['qrcode_id'] = $q_id;
                $insert_array['campaign_id'] = $campaignid;
                $insert_array['location_id'] = $locationid;
                $insert_array['is_location'] = 1;
                $insert_array['is_superadmin'] = 0;
                $insert_array['is_unique'] = $uid;
                $insert_array['scaned_date'] = $RS_dt->fields['dte'];
                $insert_array['user_id'] = $custid;
                $insert_array['mobile_uinque_id'] = $_REQUEST['mobile_uinque_id'];
                $objDB->Insert($insert_array, "scan_qrcode");
        }
        /*         * ** check Whether it is unique scan *** */
        /* $Sql = "SELECT l.id location_id ,l.timezone,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.phone_number,l.picture,
          l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,l.website,l.facebook,l.google,
          mu.business,mu.aboutus,mu.aboutus_short,mu.location_detail_title,mu.location_detail_display,mu.menu_price_title,mu.menu_price_display FROM locations l,merchant_user mu where mu.id =  l.created_by and l.id=".$_REQUEST['location_id'];

          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("SELECT l.id location_id ,l.timezone,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.phone_number,l.picture,
	l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,l.website,l.facebook,l.google,
	mu.business,mu.aboutus,mu.aboutus_short,mu.location_detail_title,mu.location_detail_display,mu.menu_price_title,mu.menu_price_display FROM locations l,merchant_user mu where mu.id =  l.created_by and l.id=?", array($_REQUEST['location_id']));

        if ($RS->RecordCount() > 0) {
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $count = 0;

                while ($Row = $RS->FetchRow()) {
                        $records[$count] = get_field_value($Row);

                        $records[$count]['phone_number'] = substr($Row['phone_number'], 4);
                        // location miles away

                        $from_lati1 = $_REQUEST['mycurrent_lati'];

                        $from_long1 = $_REQUEST['mycurrent_long'];

                        $to_lati1 = $Row['latitude'];

                        $to_long1 = $Row['longitude'];

                        $deal_distance = $objJSON->distance($from_lati1, $from_long1, $to_lati1, $to_long1, "M") . "Mi";
                        $records[$count]["miles_away"] = $deal_distance;

                        if ($Row["location_detail_display"] == "")
                                $records[$count]['location_detail_display'] = 0;

                        if ($Row["menu_price_display"] == "")
                                $records[$count]['menu_price_display'] = 0;

                        // pricerange

                        if ($Row["pricerange"] == 1) {
                                $val_text = "Inexpensive";
                        } else if ($Row["pricerange"] == 2) {
                                $val_text = "Moderate";
                        } else if ($Row["pricerange"] == 3) {
                                $val_text = "Expensive";
                        } else if ($Row["pricerange"] == 4) {
                                $val_text = "Very Expensive";
                        } else {
                                $val_text = "";
                        }
                        $records[0]["pricerange_text"] = $val_text;

                        // location categories

                        if ($Row['categories'] != "") {
                                $count1 = 0;
                                $cat_records = array();

                                /* $cat_sql = "SELECT * from category_level where id in (".$Row['categories'].")";

                                  $RS_cat_data=$objDB->Conn->Execute($cat_sql); */
                                $RS_cat_data = $objDB->Conn->Execute("SELECT * from category_level where id in (?)", array($Row['categories']));

                                if ($RS_cat_data->RecordCount() > 0) {
                                        while ($Row_cat = $RS_cat_data->FetchRow()) {
                                                $cat_records[$count1] = get_field_value($Row_cat);
                                                $count1++;
                                        }
                                        $records[0]["location_categories"] = $cat_records;
                                }
                        }

                        // location subscribe

                        if (isset($_REQUEST['customer_id'])) {
                                $customer_id = $_REQUEST['customer_id'];
                                /* $sql_chk ="select * from subscribed_stores where customer_id= ".$customer_id." and location_id=".$_REQUEST['location_id'];
                                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?", array($customer_id, $_REQUEST['location_id']));

                                if ($subscibed_store_rs->RecordCount() == 0) {
                                        $records[0]["location_subscribe"] = 0;
                                } else {
                                        if ($subscibed_store_rs->fields['subscribed_status'] == 1) {
                                                $records[0]["location_subscribe"] = 1;
                                        } else {
                                                $records[0]["location_subscribe"] = 0;
                                        }
                                }
                        } else {
                                $records[0]["location_subscribe"] = 0;
                        }

                        // location hours

                        /* $Sql_lh = "SELECT * FROM location_hours where location_id =".$_REQUEST['location_id']." and day=LCASE(left(DAYNAME(now()),3))";

                          $RS_lh = $objDB->Conn->Execute($Sql_lh); */
                        $RS_lh = $objDB->Conn->Execute("SELECT * FROM location_hours where location_id =? and day=LCASE(left(DAYNAME(now()),3))", array($_REQUEST['location_id']));

                        if ($RS_lh->RecordCount() > 0) {
                                $count2 = 0;
                                $lh_records = array();

                                while ($Row_lh = $RS_lh->FetchRow()) {
                                        $lh_records[$count2] = get_field_value($Row_lh);
                                        $count2++;
                                }
                                $records[0]['location_hours'] = $lh_records;
                        }

                        // location additional photos
                        //echo $Row['picture'];

                        $image_path_main = UPLOAD_IMG . "/m/location/";

                        $endpoint = "https://maps.google.com/cbk?output=json&hl=en&ll=" . $Row['latitude'] . "," . $Row['longitude'];

                        $handler = curl_init();
                        curl_setopt($handler, CURLOPT_HEADER, 0);
                        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($handler, CURLOPT_URL, $endpoint);
                        $data = curl_exec($handler);
                        curl_close($handler);
                        // if data value is an empty json document ('{}') , the panorama is not available for that point
                        if ($data === '{}') {
                                //print "StreetView Panorama isn't available for the selected location";
                        } else {
                                if (file_exists($image_path_main . 'street_' . $_REQUEST['location_id'] . '.jpeg')) {
                                        
                                } else {
                                        $street_main_image = file_get_contents('https://maps.googleapis.com/maps/api/streetview?size=400x310&location=' . $Row['latitude'] . "," . $Row['longitude'] . '&sensor=false&key=AIzaSyBsvIV_4NNaCz9d2tSS6EeW01wIj98lmFA');
                                        $fp = fopen($image_path_main . 'street_' . $_REQUEST['location_id'] . '.jpeg', 'w+');
                                        fputs($fp, $street_main_image);

                                        $image = new SimpleImage();
                                        $image->load($image_path_main . 'street_' . $_REQUEST['location_id'] . '.jpeg');
                                        $image->resize(70, 70);
                                        $image->save($image_path_main . 'thumb/street_' . $_REQUEST['location_id'] . '.jpeg');
                                }
                        }

                        $lp_records = array();

                        if ($data === '{}') {
                                $lp_records[0]['id'] = 0;
                                $lp_records[0]['main_image'] = $Row['picture'];
                                $lp_records[0]['counter'] = 1;
                        } else {
                                $lp_records[0]['id'] = 0;
                                $lp_records[0]['main_image'] = 'street_' . $_REQUEST['location_id'] . '.jpeg';
                                $lp_records[0]['counter'] = 1;

                                $lp_records[1]['id'] = 1;
                                $lp_records[1]['main_image'] = $Row['picture'];
                                $lp_records[1]['counter'] = 2;
                        }


                        /* $Sql_lp = "SELECT * FROM location_images where location_id =".$_REQUEST['location_id']." order by image_id";

                          $RS_lp = $objDB->Conn->Execute($Sql_lp); */
                        $RS_lp = $objDB->Conn->Execute("SELECT * FROM location_images where location_id =? order by image_id", array($_REQUEST['location_id']));
                        if ($RS_lp->RecordCount() > 0) {
                                if ($data === '{}') {
                                        $count3 = 1;
                                } else {
                                        $count3 = 2;
                                }

                                while ($Row_lp = $RS_lp->FetchRow()) {
                                        $lp_records[$count3] = get_field_value($Row_lp);
                                        $lp_records[$count3]['counter'] = $count3 + 1;
                                        $count3++;
                                }
                        }
                        $records[0]['location_photos'] = $lp_records;

                        // location review				

                        /* $Sql_lr = "select rr.id review_id,review,reviewed_datetime,rating,is_usefull,is_notusefull,rr.customer_id,cu.firstname,cu.lastname,cu.city,cu.state,cu.profile_pic from review_rating rr,customer_user cu where review!='' and cu.id=rr.customer_id and location_id=".$_REQUEST['location_id']." order by reviewed_datetime desc limit 10";

                          $RS_lr = $objDB->Conn->Execute($Sql_lr); */
                        $RS_lr = $objDB->Conn->SelectLimit("select rr.id review_id,review,reviewed_datetime,rating,is_usefull,is_notusefull,rr.customer_id,cu.firstname,cu.lastname,cu.city,cu.state,cu.profile_pic from review_rating rr,customer_user cu where review!='' and cu.id=rr.customer_id and location_id=? order by reviewed_datetime desc", 10, 0, array($_REQUEST['location_id']));

                        if ($RS_lr->RecordCount() > 0) {
                                $count4 = 0;
                                $lr_records = array();

                                while ($Row_lr = $RS_lr->FetchRow()) {
                                        $lr_records[$count4] = get_field_value($Row_lr);
                                        $lr_records[$count4]['firstname'] = ucfirst($Row_lr['firstname']);
                                        $lr_records[$count4]['lastname'] = ucfirst(substr($Row_lr['lastname'], 0, 1));
                                        $lr_records[$count4]['city'] = ucwords($Row_lr['city']);
                                        $lr_records[$count4]['review'] = trim(strip_tags(str_replace("&nbsp;", " ", $Row_lr['review'])));
                                        $lr_records[$count4]['reviewed_datetime'] = date('M j, Y | g:i A', strtotime($Row_lr['reviewed_datetime']));

                                        if (isset($_REQUEST['customer_id'])) {
                                                /* $Sql123 = "select review_like from user_review_like where review_id=".$Row_lr['review_id']." and customer_id =".$_REQUEST['customer_id'];
                                                  $RS123 = $objDB->Conn->Execute($Sql123); */
                                                $RS123 = $objDB->Conn->Execute("select review_like from user_review_like where review_id=? and customer_id =?", array($Row_lr['review_id'], $_REQUEST['customer_id']));

                                                if ($RS123->RecordCount() > 0) {
                                                        while ($Row123 = $RS123->FetchRow()) {
                                                                $lr_records[$count4]['customer_helpfull'] = $Row123['review_like'];
                                                        }
                                                }
                                        }

                                        if (isset($_REQUEST['customer_id'])) {
                                                if ($_REQUEST['customer_id'] == $Row_lr['customer_id']) {
                                                        $lr_records[$count4]['customer_review'] = 1;
                                                } else {
                                                        $lr_records[$count4]['customer_review'] = 0;
                                                }
                                        }

                                        // for profile pic

                                        $pos = strpos($lr_records[$count4]['profile_pic'], 'http');
                                        if ($pos === false) {
                                                if ($lr_records[$count4]['profile_pic'] != "") {
                                                        $lr_records[$count4]['profile_pic'] = ASSETS_IMG . "/c/usr_pic/" . $lr_records[$count4]['profile_pic'];
                                                } else {
                                                        $lr_records[$count4]['profile_pic'] = ASSETS_IMG . '/c/default_small_user.jpg';
                                                }
                                        } else {
                                                $pic_var = explode("/", $lr_records[$count4]['profile_pic']);
                                                if ($pic_var[2] == "graph.facebook.com" || $pic_var[2] == "fbcdn-profile-a.akamaihd.net") {
                                                        $lr_records[$count4]['facebook_pic'] = 1;
                                                        if ($pic_var[2] == "graph.facebook.com") {
                                                                $fb_img_json = file_get_contents($lr_records[$count4]['profile_pic'] . "?type=large&redirect=false");
                                                                $fb_img_json = json_decode($fb_img_json, true);

                                                                $lr_records[$count4]['facebook_profile_pic'] = $fb_img_json['data']['url'];
                                                        }
                                                }
                                                $lr_records[$count4]['profile_pic'] = $lr_records[$count4]['profile_pic'];
                                        }

                                        // for profile pic

                                        $count4++;
                                }



                                $records[0]['location_reviews'] = $lr_records;
                        }

                        // location rating count

                        /* $sql_rc = "select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  where  location_id = ".$_REQUEST['location_id']." group by re.rating";

                          $RS_rc = $objDB->Conn->Execute($sql_rc); */
                        $RS_rc = $objDB->Conn->Execute("select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  where  location_id =? group by re.rating", array($_REQUEST['location_id']));

                        //echo $RS_rc->RecordCount();
                        if ($RS_rc->RecordCount() > 0) {
                                $one = 0;
                                $two = 0;
                                $three = 0;
                                $four = 0;
                                $five = 0;
                                $lrc_records = array();
								$total_ratings = 0;
                               // $RS_rc = $objDB->Conn->Execute($sql_rc);
                                $avarage_rating = array();

                                while ($rating_row = $RS_rc->FetchRow()) {
                                        $total_ratings = $total_ratings + $rating_row['avarage_rating_counter'];
                                        if ($rating_row['avarage_rating'] <= 1) {
                                                $one = $one + $rating_row['avarage_rating_counter'];
                                                $key_value_pair ['Poor'] = $one;
                                        } else if ($rating_row['avarage_rating'] > 1 && $rating_row['avarage_rating'] <= 2) {
                                                $two = $two + $rating_row['avarage_rating_counter'];
                                                $key_value_pair ['Fair'] = $two;
                                        } else if ($rating_row['avarage_rating'] > 2 && $rating_row['avarage_rating'] <= 3) {
                                                $three = $three + $rating_row['avarage_rating_counter'];
                                                $key_value_pair ['Good'] = $three;
                                        } else if ($rating_row['avarage_rating'] > 3 && $rating_row['avarage_rating'] <= 4) {
                                                $four = $four + $rating_row['avarage_rating_counter'];
                                                $key_value_pair ['Very Good'] = $four;
                                        } else if ($rating_row['avarage_rating'] > 4 && $rating_row['avarage_rating'] <= 5) {
                                                $five = $five + $rating_row['avarage_rating_counter'];
                                                $key_value_pair ['Excellent'] = $five;
                                        }
                                }

                                $one_percentage = round(($one * 100) / $total_ratings, 2);
                                $two_percentage = round(($two * 100) / $total_ratings, 2);
                                $three_percentage = round(($three * 100) / $total_ratings, 2);
                                $four_percentage = round(($four * 100) / $total_ratings, 2);
                                $five_percentage = round(($five * 100) / $total_ratings, 2);
                                $rating_values = array();
                                array_push($rating_values, $one);
                                array_push($rating_values, $two);
                                array_push($rating_values, $three);
                                array_push($rating_values, $four);
                                array_push($rating_values, $five);

                                $rating_visitor_arr = array();
                                array_push($rating_visitor_arr, $one_percentage);
                                array_push($rating_visitor_arr, $two_percentage);
                                array_push($rating_visitor_arr, $three_percentage);
                                array_push($rating_visitor_arr, $four_percentage);
                                array_push($rating_visitor_arr, $five_percentage);

                                $max_rating = round(max($rating_visitor_arr));

                                if ($max_rating != 0) {
                                        $records[0]['location_main_percentage'] = $max_rating;

                                        $max_rating_heading = array_search(max($key_value_pair), $key_value_pair);

                                        if ($max_rating_heading == "Excellent" || $max_rating_heading == "Good" || $max_rating_heading == "Very Good") {
                                                $records[0]['location_main_rating_heading'] = $max_rating_heading;
                                        }
                                }

                                $array_where_loc1['id'] = $_REQUEST['location_id'];
                                $RSlocation1 = $objDB->Show("locations", $array_where_loc1);

                                $records[$count]["location_main_total_reviews"] = $RSlocation1->fields['no_of_reviews'];
                                $records[$count]["location_main_total_ratings"] = $RSlocation1->fields['no_of_rating'];
                                $rating_number = 0;
                                if ($RSlocation1->fields['avarage_rating'] < 0 && $RSlocation1->fields['avarage_rating'] < 1) {
                                        // echo "in .5";
                                        $class = "orange-half";
                                        $rating_number = 0.5;
                                        $rating_title = "Poor";
                                } else if ($RSlocation1->fields['avarage_rating'] >= 1 && $RSlocation1->fields['avarage_rating'] <= 1.74) {
                                        // echo "in 1";
                                        $class = "orange-one";
                                        $rating_number = 1;
                                        $rating_title = "Poor";
                                } else if ($RSlocation1->fields['avarage_rating'] >= 1.75 && $RSlocation1->fields['avarage_rating'] <= 2.24) {
                                        // echo "2";
                                        $class = "orange-two";
                                        $rating_number = 2;
                                        $rating_title = "Fair";
                                } else if ($RSlocation1->fields['avarage_rating'] >= 2.25 && $RSlocation1->fields['avarage_rating'] <= 2.74) {
                                        //echo "2,5";
                                        $class = "orange-two_h";
                                        $rating_number = 2.5;
                                        $rating_title = "Good";
                                } else if ($RSlocation1->fields['avarage_rating'] >= 2.75 && $RSlocation1->fields['avarage_rating'] <= 3.24) {
                                        //echo "3";
                                        $class = "orange-three";
                                        $rating_number = 3;
                                        $rating_title = "Good";
                                } else if ($RSlocation1->fields['avarage_rating'] >= 3.25 && $RSlocation1->fields['avarage_rating'] <= 3.74) {
                                        //  echo "3.5";
                                        $class = "orange-three_h";
                                        $rating_number = 3.5;
                                        $rating_title = "Very Good";
                                } else if ($RSlocation1->fields['avarage_rating'] >= 3.75 && $RSlocation1->fields['avarage_rating'] <= 4.24) {
                                        // echo "4";
                                        $class = "orange-four";
                                        $rating_number = 4;
                                        $rating_title = "Very Good";
                                } else if ($RSlocation1->fields['avarage_rating'] >= 4.25 && $RSlocation1->fields['avarage_rating'] <= 4.74) {
                                        //  echo "4.5";
                                        $class = "orange-four_h";
                                        $rating_number = 4.5;
                                        $rating_title = "Excellent";
                                } else if ($RSlocation1->fields['avarage_rating'] >= 4.75) {
                                        // echo "5";
                                        $class = "orange";
                                        $rating_number = 5;
                                        $rating_title = "Excellent";
                                }
                                $records[$count]["location_main_ratings_number"] = $rating_number;
                                $records[$count]["location_main_ratings_class"] = $class;
                                $records[$count]["location_main_ratings_title"] = $rating_title;

                                $records[0]['location_rating_count']['total_ratings'] = $total_ratings;
                                $records[0]['location_rating_count']['Poor'] = $one;
                                $records[0]['location_rating_count']['Fair'] = $two;
                                $records[0]['location_rating_count']['Good'] = $three;
                                $records[0]['location_rating_count']['Very Good'] = $four;
                                $records[0]['location_rating_count']['Excellent'] = $five;
                        }

                        $count++;
                }

                $json_array["records"] = $records;
        } else {
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
 * @uses get bussiness detail
 * @param location_id
 * @return string
 */

if (isset($_REQUEST['get_business_details'])) {
        /* $Sql = "SELECT mu.business,mu.aboutus,mu.aboutus_short,mu.location_detail_title,mu.location_detail_display,mu.menu_price_title,mu.menu_price_display,
          l.website,l.facebook,l.google,l.venue_id
          FROM locations l,merchant_user mu where mu.id =  l.created_by and l.id=".$_REQUEST['location_id'];

          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("SELECT mu.business,mu.aboutus,mu.aboutus_short,mu.location_detail_title,mu.location_detail_display,mu.menu_price_title,mu.menu_price_display,
	l.website,l.facebook,l.google,l.venue_id 
	FROM locations l,merchant_user mu where mu.id =  l.created_by and l.id=?", array($_REQUEST['location_id']));

        if ($RS->RecordCount() > 0) {
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $count = 0;

                while ($Row = $RS->FetchRow()) {
                        $records[$count] = get_field_value($Row);

                        if ($Row['venue_id'] != "") {
                                $my_file = 'locu_files/locu_' . $Row['venue_id'] . '.txt';
                                if (file_exists($my_file)) {
                                        $file_data = file_get_contents($my_file);
                                        $records[$count]['menu_price_display'] = 1;
                                } else {
                                        $records[$count]['menu_price_display'] = 0;
                                }
                        } else {
                                $records[$count]['menu_price_display'] = 0;
                        }

                        if ($Row["location_detail_display"] == "" || $Row["location_detail_display"] == 0) {
                                $records[$count]["location_detail_display"] = 0;
                        }
                        if ($Row["menu_price_display"] == "" || $Row["menu_price_display"] == 0) {
                                $records[$count]["menu_price_display"] = 0;
                        }
                        // location hours

                        /* $Sql_lh = "SELECT * FROM location_hours where location_id =".$_REQUEST['location_id'];

                          $RS_lh = $objDB->Conn->Execute($Sql_lh); */
                        $RS_lh = $objDB->Conn->Execute("SELECT * FROM location_hours where location_id =?", array($_REQUEST['location_id']));

                        if ($RS_lh->RecordCount() > 0) {
                                $count2 = 0;
                                $lh_records = array();

                                while ($Row_lh = $RS_lh->FetchRow()) {
                                        if ($Row_lh['day'] == "sun") {
                                                $lh_records[$count2] = get_field_value($Row_lh);
                                                $lh_records[$count2]['order'] = 0;
                                        } else if ($Row_lh['day'] == "mon") {
                                                $lh_records[$count2] = get_field_value($Row_lh);
                                                $lh_records[$count2]['order'] = 1;
                                        } else if ($Row_lh['day'] == "tue") {
                                                $lh_records[$count2] = get_field_value($Row_lh);
                                                $lh_records[$count2]['order'] = 2;
                                        } else if ($Row_lh['day'] == "wed") {
                                                $lh_records[$count2] = get_field_value($Row_lh);
                                                $lh_records[$count2]['order'] = 3;
                                        } else if ($Row_lh['day'] == "thu") {
                                                $lh_records[$count2] = get_field_value($Row_lh);
                                                $lh_records[$count2]['order'] = 4;
                                        } else if ($Row_lh['day'] == "fri") {
                                                $lh_records[$count2] = get_field_value($Row_lh);
                                                $lh_records[$count2]['order'] = 5;
                                        } else if ($Row_lh['day'] == "sat") {
                                                $lh_records[$count2] = get_field_value($Row_lh);
                                                $lh_records[$count2]['order'] = 6;
                                        }

                                        $count2++;
                                }
                                $records[0]['location_hours'] = $lh_records;
                        }

                        // location attributes

                        /* $Sql_lp = "SELECT dining 'Dining Option',reservation 'Takes Reservation',takeout 'Takeout',good_for 'Good For',pricerange 'Price Range',
                          parking 'Parking',wheelchair 'Wheelchair Accessible',payment_method 'Payment method',minimum_age 'Minimum Age Restriction',pet 'Pet Allowed',
                          ambience 'Ambience',attire 'Attire',noise_level 'Noise Level',wifi 'Wifi',has_tv 'Has TV',airconditioned 'Airconditioned',smoking 'Smoking',alcohol 'Alcohol',
                          will_deliver 'Will Deliver',minimum_order 'Minimum Order',deliveryarea_from 'Delivery Area From',deliveryarea_to 'To',caters 'Caters',services 'Services',amenities 'Amenities'
                          FROM locations where id =".$_REQUEST['location_id'];

                          $RS_lp = $objDB->Conn->Execute($Sql_lp); */
                        $RS_lp = $objDB->Conn->Execute("SELECT dining 'Dining Option',reservation 'Takes Reservation',takeout 'Takeout',good_for 'Good For',pricerange 'Price Range',parking 'Parking',wheelchair 'Wheelchair Accessible',payment_method 'Payment method',minimum_age 'Minimum Age Restriction',pet 'Pet Allowed',ambience 'Ambience',attire 'Attire',noise_level 'Noise Level',wifi 'Wifi',has_tv 'Has TV',airconditioned 'Airconditioned',smoking 'Smoking',alcohol 'Alcohol',will_deliver 'Will Deliver',minimum_order 'Minimum Order',deliveryarea_from 'Delivery Area From',deliveryarea_to 'To',caters 'Caters',services 'Services',amenities 'Amenities' 
				FROM locations where id =?", array($_REQUEST['location_id']));
                        if ($RS_lp->RecordCount() > 0) {
                                $count3 = 0;
                                $lp_records = array();

                                while ($Row_lp = $RS_lp->FetchRow()) {
                                        $lp_records[$count3] = get_field_value($Row_lp);
                                        //print_r($lp_records[$count3]);
                                        //echo "<br />";
                                        $main_arr = array();
                                        foreach ($lp_records[$count3] as $key => $value) {


                                                if ($key == 'Dining Option') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Takes Reservation') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Takeout') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Good For') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Price Range') {
                                                        if ($value != "" && $value != 0) {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                if ($value == 1) {
                                                                        $arr['value'] = "$ (Inexpensive)";
                                                                } else if ($value == 2) {
                                                                        $arr['value'] = "$$ (Moderate)";
                                                                } else if ($value == 3) {
                                                                        $arr['value'] = "$$$ (Expensive)";
                                                                } else if ($value == 4) {
                                                                        $arr['value'] = "$$$$ (Very Expensive)";
                                                                }
                                                                array_push($main_arr, $arr);
                                                        }
                                                }

                                                if ($key == 'Parking') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Wheelchair Accessible') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Payment method') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Minimum Age Restriction') {
                                                        if ($value != "" && $value != 0) {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value . " years to enter the location.";
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Pet Allowed') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Ambience') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Attire') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Noise Level') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Wifi') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Has TV') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Airconditioned') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Smoking') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Alcohol') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                /*
                                                  if($key=='Will Deliver')
                                                  {
                                                  if($value!="")
                                                  {
                                                  $arr = array();
                                                  $arr['key'] = $key;
                                                  $arr['value'] = $value;
                                                  array_push($main_arr,$arr);
                                                  }
                                                  }
                                                  if($key=='Minimum Order')
                                                  {
                                                  if($value!="" && $value!="0")
                                                  {
                                                  $arr = array();
                                                  $arr['key'] = $key;
                                                  $arr['value'] = $value;
                                                  array_push($main_arr,$arr);
                                                  }
                                                  }
                                                  if($key=='Delivery Area From')
                                                  {
                                                  if($value!="" && $value!="0")
                                                  {
                                                  $arr = array();
                                                  $arr['key'] = $key;
                                                  $arr['value'] = $value;
                                                  array_push($main_arr,$arr);
                                                  }
                                                  }
                                                  if($key=='To')
                                                  {
                                                  if($value!="" && $value!="0")
                                                  {
                                                  $arr = array();
                                                  $arr['key'] = $key;
                                                  $arr['value'] = $value;
                                                  array_push($main_arr,$arr);
                                                  }
                                                  }
                                                 */
                                                if ($key == 'Caters') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Services') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                                if ($key == 'Amenities') {
                                                        if ($value != "") {
                                                                $arr = array();
                                                                $arr['key'] = $key;
                                                                $arr['value'] = $value;
                                                                array_push($main_arr, $arr);
                                                        }
                                                }
                                        }
                                        $count3++;
                                }


                                $m_o = "";
                                $d_a = "";
                                $RS_lp->MoveFirst();

                                if ($RS_lp->fields['Will Deliver'] == "Yes") {
                                        if ($RS_lp->fields['Minimum Order'] != "" && $RS_lp->fields['Minimum Order'] != "0") {
                                                //$location_detail_html.=' ( Minimum Order : $'.$Row['minimum_order'].', Delivery Area : '.$Row['deliveryarea_from'].' to '.$Row['deliveryarea_to']." )";
                                                $m_o = 'Yes ( Minimum Order : $' . $RS_lp->fields['Minimum Order'] . ', Delivery Area : ' . $RS_lp->fields['Delivery Area From'] . ' to ' . $RS_lp->fields['To'] . " )";
                                        }
                                        if ($RS_lp->fields['Delivery Area From'] != "" && $RS_lp->fields['To'] != "" && $RS_lp->fields['Delivery Area From'] != "0" && $RS_lp->fields['To'] != "0") {
                                                //$location_detail_html.=' ( Minimum Order : $'.$Row['minimum_order'].', Delivery Area : '.$Row['deliveryarea_from'].' to '.$Row['deliveryarea_to']." )";
                                                $d_a = 'Yes ( Minimum Order : $' . $RS_lp->fields['Minimum Order'] . ', Delivery Area : ' . $RS_lp->fields['Delivery Area From'] . ' to ' . $RS_lp->fields['To'] . " )";
                                        }


                                        if ($m_o != "" && $d_a != "") {
                                                $wd = 'Yes ( Minimum Order : $ ' . $RS_lp->fields['Minimum Order'];
                                                $wd.=', Delivery Area : ' . $RS_lp->fields['Delivery Area From'] . ' to ' . $RS_lp->fields['To'] . ')';
                                                $arr = array();
                                                $arr['key'] = 'Will Deliver';
                                                $arr['value'] = $wd;
                                                array_push($main_arr, $arr);
                                        } else if ($m_o != "" && $d_a == "") {
                                                $wd = 'Yes ( Minimum Order : $ ' . $RS_lp->fields['Minimum Order'] . ')';
                                                $arr = array();
                                                $arr['key'] = 'Will Deliver';
                                                $arr['value'] = $wd;
                                                array_push($main_arr, $arr);
                                        } else if ($m_o == "" && $d_a != "") {
                                                $wd = 'Yes ( Delivery Area : ' . $RS_lp->fields['Delivery Area From'] . ' to ' . $RS_lp->fields['To'] . ')';
                                                $arr = array();
                                                $arr['key'] = 'Will Deliver';
                                                $arr['value'] = $wd;
                                                array_push($main_arr, $arr);
                                        }
                                } else if ($RS_lp->fields['Will Deliver'] == "") {
                                        
                                } else {
                                        $arr = array();
                                        $arr['key'] = 'Will Deliver';
                                        $arr['value'] = $RS_lp->fields['Will Deliver'];
                                        array_push($main_arr, $arr);
                                }
                                $records[0]['location_attributes'] = $main_arr;
                        }

                        $count++;
                }

                $json_array["records"] = $records;
        } else {
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
 * @uses get bussiness detail
 * @param start_value,per_page
 * @return string
 */
if (isset($_REQUEST['get_location_review_details'])) {
        if (isset($_REQUEST['start_value'])) {
                $start_value = $_REQUEST['start_value'];
        } else {
                $start_value = 0;
        }
        if (isset($_REQUEST['per_page'])) {
                $per_page = $_REQUEST['per_page'];
        } else {
                $per_page = 10;
        }



        /* $Sql = "select rr.id review_id,review,reviewed_datetime,rating,is_usefull,is_notusefull,rr.customer_id,cu.firstname,cu.lastname,cu.city,cu.state,cu.profile_pic from review_rating rr,customer_user cu where review!='' and cu.id=rr.customer_id and location_id=".$_REQUEST['location_id']." order by reviewed_datetime desc limit ".$start_value.",".$per_page;

          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->SelectLimit("select rr.id review_id,review,reviewed_datetime,rating,is_usefull,is_notusefull,rr.customer_id,cu.firstname,cu.lastname,cu.city,cu.state,cu.profile_pic from review_rating rr,customer_user cu where review!='' and cu.id=rr.customer_id and location_id=? order by reviewed_datetime desc", $per_page, $start_value, array($_REQUEST['location_id']));

        if ($RS->RecordCount() > 0) {
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $count = 0;

                while ($Row = $RS->FetchRow()) {

                        $records[$count] = get_field_value($Row);
                        $records[$count]['firstname'] = ucfirst($Row['firstname']);
                        $records[$count]['lastname'] = ucfirst(substr($Row['lastname'], 0, 1));
                        $records[$count]['city'] = ucwords($Row['city']);
                        $records[$count]['review'] = trim(strip_tags(str_replace("&nbsp;", " ", $Row['review'])));
                        $records[$count]['reviewed_datetime'] = date('M j, Y | g:i A', strtotime($Row['reviewed_datetime']));

                        if (isset($_REQUEST['customer_id'])) {
                                /* $Sql = "select review_like from user_review_like where review_id=".$Row['review_id']." and customer_id =".$_REQUEST['customer_id'];
                                  $RS1 = $objDB->Conn->Execute($Sql); */
                                $RS1 = $objDB->Conn->Execute("select review_like from user_review_like where review_id=? and customer_id =?", array($Row['review_id'], $_REQUEST['customer_id']));

                                if ($RS1->RecordCount() > 0) {
                                        while ($Row1 = $RS1->FetchRow()) {
                                                $records[$count]['customer_helpfull'] = $Row1['review_like'];
                                        }
                                }
                        }

                        if (isset($_REQUEST['customer_id'])) {
                                if ($_REQUEST['customer_id'] == $Row['customer_id']) {
                                        $records[$count]['customer_review'] = 1;
                                } else {
                                        $records[$count]['customer_review'] = 0;
                                }
                        }

                        // for profile pic

                        $pos = strpos($records[$count]['profile_pic'], 'http');
                        if ($pos === false) {
                                if ($records[$count]['profile_pic'] != "") {
                                        $records[$count]['profile_pic'] = ASSETS_IMG . "/c/usr_pic/" . $records[$count]['profile_pic'];
                                } else {
                                        $records[$count]['profile_pic'] = ASSETS_IMG . '/c/default_small_user.jpg';
                                }
                        } else {
                                $pic_var = explode("/", $records[$count]['profile_pic']);
                                if ($pic_var[2] == "graph.facebook.com" || $pic_var[2] == "fbcdn-profile-a.akamaihd.net") {
                                        $records[$count]['facebook_pic'] = 1;
                                        if ($pic_var[2] == "graph.facebook.com") {
                                                $fb_img_json = file_get_contents($records[$count]['profile_pic'] . "?type=large&redirect=false");
                                                $fb_img_json = json_decode($fb_img_json, true);

                                                $records[$count]['facebook_profile_pic'] = $fb_img_json['data']['url'];
                                        }
                                }
                                $records[$count]['profile_pic'] = $records[$count]['profile_pic'];
                        }

                        // for profile pic

                        $count++;
                }

                $json_array["records"] = $records;
        } else {
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
 * @uses get location campaign list
 * @param merchant_id,loc_id,category_id,mlatitude,mlongitude,dismile
 * @return string
 */
if (isset($_REQUEST['get_location_campaign_list'])) {
        $merchantid = $_REQUEST['merchant_id'];
        $json_array = array();
        $records = array();
        $records_all = array();
        $json_array1 = array();
        $loc_id = $_REQUEST['loc_id'];
        $category_id = $_REQUEST['category_id'];
        
        $date_f = date("Y-m-d H:i:s");
        $mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];

        if (isset($_REQUEST['dismile'])) 
        {
			$mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];
        
			$dismile = $_REQUEST['dismile'];
                $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;
                $Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=" . $merchantid . " and " . $Where;
        }

        /*
          //$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
          $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";
         */
        if ($_REQUEST['customer_id'] != "") {
                $date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";
        } else {
                $date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";
        }

        if (isset($_REQUEST['firstlimit']) && isset($_REQUEST['lastlimit'])) {
                if ($_REQUEST['firstlimit'] == "" && $_REQUEST['lastlimit'] == "") {
                        $firstlimit = 0;
                        $lastlimit = 9;
                } else {
                        $firstlimit = $_REQUEST['firstlimit'];
                        $lastlimit = $_REQUEST['lastlimit'];
                }
        }
        $cust_where = "";

        $cust_where = "";
		$Where = "";
        $cat_str = "";
        if ($_REQUEST['customer_id'] != "") {
                $customer_id = $_REQUEST['customer_id'];
                $cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id where  ms.user_id=" . $customer_id . ") or c.level =1 ) ";

                // 02-10-2013 dist list deal display if cust in dist list and reserved
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id  in ( select campaign_id from customer_campaigns where customer_id =" . $customer_id . " and location_id=cl.location_id and activation_status=1  and cl.campaign_type=3)  )
) ";
                // 02-10-2013
                // 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , in this private deal also display if not subscribed
                $cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . ") or c.level =1 ) ";
                // 03-10-2013
                // 03-10-2013 dist list dal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private=1 and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) ) )";
                // 03-10-2013
                // 04-02-2014 dist list dal display if cust in dist list and not reserved also and not subscribed also , remove private deal problem if not subscribed
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join  campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private=1 and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 or c.is_walkin=1) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) ) )";
                // 04-02-2014	
        } else {
                $cust_where = " and c.level=1 ";
                // 13-02-2013 also include checkin campaign		
                $cust_where = " and (c.level=1 or c.is_walkin=1) ";
                // 13-02-2013
        }
        if (isset($_REQUEST['category_id'])) {
                if ($_REQUEST['category_id'] == 0) {
                        $cat_str = "";
                } else {
                        $cat_str = " and c.category_id = " . $_REQUEST['category_id'] . " and c.category_id in(select cat.id from categories cat where cat.active=1) ";
                }
        }
        /* $limit_data = "SELECT c.*,c.id as cid,l.id as locid
          FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
          WHERE  l.created_by=".$merchantid." and    l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh."  and ".$Where ." l.id=".$loc_id;

          $RS_limit_data=$objDB->Conn->Execute($limit_data); */
        $RS_limit_data = $objDB->Conn->Execute("SELECT c.*,c.id as cid,l.id as locid
        FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
        WHERE  l.created_by=? and    l.active = ?  " . $cust_where . " " . $cat_str . "  " . $date_wh . "  and " . $Where . " l.id=?", array($merchantid, 1, $loc_id));

        // 10 10 2013		 
        $records_all = array();
        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['status'] = "true";
                //$json_array['total_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $records = array();
                while ($Row = $RS_limit_data->FetchRow()) {
                        if ($_REQUEST['customer_id'] != "") {
                                $array_where_camp2 = array();
                                $array_where_camp2['campaign_id'] = $Row['cid'];
                                $array_where_camp2['customer_id'] = $_REQUEST['customer_id'];
                                $array_where_camp2['location_id'] = $Row['locid'];
                                $RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
                                //echo $RS_cust_camp->RecordCount()."-";
                                $reserved = $RS_cust_camp->RecordCount();

                                $array_where_camp = array();
                                $array_where_camp['campaign_id'] = $Row['cid'];
                                $array_where_camp['customer_id'] = $_REQUEST['customer_id'];
                                $array_where_camp['referred_customer_id'] = 0;
                                $array_where_camp['location_id'] = $Row['locid'];
                                $RS_camp = $objDB->Show("reward_user", $array_where_camp);

                                $array_where_camp1 = array();
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

                                if ($RS_cust_camp->RecordCount() > 0 && $RS_camp->RecordCount() > 0 && $Row['number_of_use'] == 1) {
                                        //echo "1 ".$Row->cid." ".$storeid;
                                        /*
                                          $records[$count]['campaign_id'] = $Row['cid'];
                                          $records[$count]['location_id'] = $Row['locid'];
                                          $records[$count]['condition']=1;
                                          $count++;
                                         */
                                } elseif ($RS_cust_camp->RecordCount() > 0 && $RS_camp->RecordCount() > 0 && ($Row['number_of_use'] == 2 || $Row['number_of_use'] == 3) && $campLoc->fields['offers_left'] == 0) {
                                        //echo "2 ".$Row->cid." ".$storeid;
                                        /*
                                          $records[$count]['campaign_id'] = $Row['cid'];
                                          $records[$count]['location_id'] = $Row['locid'];
                                          $records[$count]['condition']=2;
                                          $count++;
                                         */
                                } elseif ($RS_cust_camp->RecordCount() == 0 && $RS_camp->RecordCount() == 0 && $campLoc->fields['offers_left'] == 0) {
                                        //echo "3 ".$Row->cid." ".$storeid;
                                        /*
                                          $records[$count]['campaign_id'] = $Row['cid'];
                                          $records[$count]['location_id'] = $Row['locid'];
                                          $records[$count]['condition']=3;
                                          $count++;
                                         */
                                } else {
                                        //echo "3 else";
                                        $records[$count] = get_field_value($Row);

                                        $image = explode(".", $Row['business_logo']);
                                        //echo $image[0].".jpg";
                                        $records[$count]["business_logo"] = $image[0] . ".jpg";
                                        $records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));

                                        $customer_id = $_REQUEST["customer_id"];
                                        /* $sql_ac ="SELECT * FROM customer_campaigns WHERE activation_status=1 and campaign_id =".$Row['cid']." and location_id=".$Row['locid']." and customer_id=".$customer_id;
                                          $RSsql_ac = $objDB->Conn->Execute($sql_ac); */
                                        $RSsql_ac = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE activation_status=? and campaign_id =? and location_id=? and customer_id=?", array(1, $Row['cid'], $Row['locid'], $customer_id));

                                        if ($RSsql_ac->RecordCount() > 0) {
                                                $records[$count]['reserve'] = 1;
                                        } else {
                                                $records[$count]['reserve'] = 0;
                                        }

                                        $count++;
                                        //echo $count;
                                }
                        } else {
                                $records[$count] = get_field_value($Row);

                                $image = explode(".", $Row['business_logo']);
                                //echo $image[0].".jpg";
                                $records[$count]["business_logo"] = $image[0] . ".jpg";
                                $records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));
                                $records[$count]['reserve'] = 0;
                                $count++;
                        }
                }


                $json_array["records"] = $records;
                // 31-7-2013 add line
                $json_array["total_records"] = $count;
                // 31-7-2013 add line
        } else {
                $json_array['status'] = "false";
                $json_array['total_records'] = 0;

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
 * @uses get location menu
 * @param location_id
 * @return string
 */
if (isset($_REQUEST['get_location_menu'])) {
        $json_array = array();

        /* $sql_venue_id="select * from locations where id=".$_REQUEST['location_id'];
          $location_venue_id=  $objDB->Conn->Execute($sql_venue_id); */
        $location_venue_id = $objDB->Conn->Execute("select * from locations where id=?", array($_REQUEST['location_id']));

        if ($location_venue_id->fields['venue_id'] != "") {
                $my_file = 'locu_files/locu_' . $location_venue_id->fields['venue_id'] . '.txt';
                if (file_exists($my_file)) {
                        $file_data = file_get_contents($my_file);
                        $json_array['status'] = "true";
                        $json_array["menu"] = $file_data;
                        //$json = json_encode($json_array);
                        echo $file_data;
                        exit();
                } else {
                        $handle = fopen($my_file, 'w');
                        $ch = curl_init();

                        // set url 
                        curl_setopt($ch, CURLOPT_URL, "https://api.locu.com/v1_0/venue/" . $location_venue_id->fields['venue_id'] . "/?api_key=269fe167da30803613598800a3da6e0e590297ac");

                        //return the transfer as a string 
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                        // $output contains the output string 
                        $output = curl_exec($ch);
                        file_put_contents($my_file, $output);
                        $json_array['status'] = "true";
                        $json_array["menu"] = $output;
                        //$json = json_encode($json_array);
                        echo $file_data;
                        exit();
                }
        } else {
                $json_array['status'] = "false";
                $json_array["message"] = $client_msg['location_detail']['Msg_no_menu_price_list'];
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

/**
 * @uses subscribe location
 * @param location_id,customer_id
 * @return string
 */
if (isset($_REQUEST['btn_subscribe_location'])) {
        $storearray = $json_array = array();
        $location_id = $_REQUEST['location_id'];
        $customer_id = $_REQUEST['customer_id'];

        $storearray['location_id'] = $location_id;
        $RSstoe = $objDB->Show("campaign_location", $storearray);
        if ($RSstoe->RecordCount() <= 0) {
                $json_array['status'] = "false";
                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {

                /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$location_id." and private = 1";

                  $RS_group = $objDB->Conn->Execute($sql_group); */
                $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?", array($location_id, 1));

                if ($RS_group->RecordCount() > 0) {
                        /* $sql_user_group = "select * from merchant_subscribs where merchant_id=".$RS_group->fields['merchant_id']." and group_id=".$RS_group->fields['id']." and user_id = ".$customer_id;

                          $RS_user_group =$objDB->Conn->Execute($sql_user_group); */
                        $RS_user_group = $objDB->Conn->Execute("select * from merchant_subscribs where merchant_id=? and group_id=? and user_id = ?", array($RS_group->fields['merchant_id'], $RS_group->fields['id'], $customer_id));

                        if ($RS_user_group->RecordCount() <= 0) {
                                $array_group = array();
                                $array_group['merchant_id'] = $RS_group->fields['merchant_id'];
                                $array_group['group_id'] = $RS_group->fields['id'];
                                $array_group['user_id'] = $customer_id;
                                $objDB->Insert($array_group, "merchant_subscribs");
                        }
                }


                //make entry in subscribe store table
                /* $sql_chk ="select * from subscribed_stores where customer_id= ".$customer_id." and location_id=".$location_id;
                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?", array($customer_id, $location_id));

                if ($subscibed_store_rs->RecordCount() == 0) {
                        /* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id=  ".$customer_id.",location_id=".$location_id." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                          $objDB->Conn->Execute($insert_subscribed_store_sql); */
                        $objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id=?,location_id=? ,subscribed_date=? ,subscribed_status=1", array($customer_id, $location_id, date('Y-m-d H:i:s')));
                } else {
                        if ($subscibed_store_rs->fields['subscribed_status'] == 0) {
                                /* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1 where  customer_id= ".$customer_id." and location_id=".$location_id;
                                  $objDB->Conn->Execute($up_subscribed_store); */
                                $objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=1 where  customer_id=? and location_id=?", array($customer_id, $location_id));
                        }
                }

                $json_array['status'] = "true";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

/**
 * @uses unsubscribe location
 * @param location_id,customer_id
 * @return string
 */

if (isset($_REQUEST['btn_unsubscribe_location'])) {
        $storearray = $json_array = array();
        $location_id = $_REQUEST['location_id'];
        $customer_id = $_REQUEST['customer_id'];

        $storearray['location_id'] = $location_id;
        $RSstoe = $objDB->Show("campaign_location", $storearray);
        if ($RSstoe->RecordCount() <= 0) {
                $json_array['status'] = "false";
                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {

                /* $sql_chk ="select * from subscribed_stores where customer_id= ".$customer_id." and location_id=".$location_id;
                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id= ? and location_id=?", array($customer_id, $location_id));

                if ($subscibed_store_rs->RecordCount() == 0) {
                        
                } else {
                        if ($subscibed_store_rs->fields['subscribed_status'] == 1) {
                                /* $up_subscribed_store = "Update subscribed_stores set subscribed_status=0 where  customer_id= ".$customer_id." and location_id=".$location_id;
                                  $objDB->Conn->Execute($up_subscribed_store); */
                                $objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=? where  customer_id=? and location_id=?", array(0, $customer_id, $location_id));

                                // start unsubscribe location then unreserve private deal 2-8-2013  
                                //$cust_private_camp_for_location_query = "select campaign_id from customer_campaigns cc,campaigns c where cc.campaign_id=c.id and c.level=0 and location_id=".$location_id." and customer_id=".$_SESSION['customer_id'];
                                //start 13-8-2013 because dist list deal also unreserve with above query as c.level=0
                                /* $cust_private_camp_for_location_query = "select cc.campaign_id from customer_campaigns cc,campaigns c,campaign_location cl where cc.campaign_id=c.id and c.level=0 and cc.location_id=".$location_id." and customer_id=".$customer_id." and cc.location_id=cl.location_id and cc.campaign_id=cl.campaign_id and cl.campaign_type=2";
                                  //end 13-8-2013
                                  $RS_cust_private_camp_for_location =$objDB->Conn->Execute($cust_private_camp_for_location_query); */
                                $RS_cust_private_camp_for_location = $objDB->Conn->Execute("select cc.campaign_id from customer_campaigns cc,campaigns c,campaign_location cl where cc.campaign_id=c.id and c.level=? and cc.location_id=? and customer_id=? and cc.location_id=cl.location_id and cc.campaign_id=cl.campaign_id and cl.campaign_type=?", array(0, $location_id, $customer_id, 2));

                                while ($Row = $RS_cust_private_camp_for_location->FetchRow()) {
                                        /* $sql = "select * from coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$Row['campaign_id']." and location_id=".$location_id ;
                                          $RS_cc1 =$objDB->Conn->Execute($sql); */
                                        $RS_cc1 = $objDB->Conn->Execute("select * from coupon_codes where customer_id=? and customer_campaign_code=? and location_id=" . $location_id, array($customer_id, $Row['campaign_id']));

                                        /* $sql = "Select * from coupon_redeem where coupon_id in (".$RS_cc1->fields['id'].")";
                                          $RS_c =$objDB->Conn->Execute($sql); */
                                        $RS_c = $objDB->Conn->Execute("Select * from coupon_redeem where coupon_id in (?)", array($RS_cc1->fields['id']));

                                        if ($RS_c->RecordCount() == 0) {
                                                /* $sql = "select * from customer_campaigns where customer_id=".$customer_id." and campaign_id=".$Row['campaign_id']." and location_id=".$location_id ;
                                                  $RS_cc =$objDB->Conn->Execute($sql); */
                                                $RS_cc = $objDB->Conn->Execute("select * from customer_campaigns where customer_id=? and campaign_id=? and location_id=?", array($customer_id, $Row['campaign_id'], $location_id));

                                                if ($RS_cc->RecordCount() > 0) {
                                                        /* $Sql = "DELETE FROM customer_campaigns where customer_id=".$customer_id." and campaign_id=".$Row['campaign_id']." and location_id=".$location_id ;
                                                          $objDB->Conn->Execute($Sql); */
                                                        $objDBWrt->Conn->Execute("DELETE FROM customer_campaigns where customer_id=? and campaign_id=? and location_id=?", array($customer_id, $Row['campaign_id'], $location_id));
                                                }

                                                // Remove coupon codes //
                                                if ($RS_cc1->RecordCount() > 0) {
                                                        //	$Sql = "UPDATE coupon_codes SET active=0 where customer_id=".$customer_id." and customer_campaign_code=".$Row['campaign_id']." and location_id=".$location_id ;
                                                        /* $Sql = "DELETE FROM coupon_codes where customer_id=".$customer_id." and customer_campaign_code=".$Row['campaign_id']." and location_id=".$location_id ;
                                                          $objDB->Conn->Execute($Sql); */
                                                        $objDBWrt->Conn->Execute("DELETE FROM coupon_codes where customer_id=? and customer_campaign_code=? and location_id=?", array($customer_id, $Row['campaign_id'], $location_id));

                                                        /* $Sql = "UPDATE campaign_location SET offers_left=offers_left+1,used_offers=used_offers-1 where campaign_id=".$Row['campaign_id']." and location_id=".$location_id ;
                                                          $objDB->Conn->Execute($Sql); */
                                                        $objDBWrt->Conn->Execute("UPDATE campaign_location SET offers_left=offers_left+1,used_offers=used_offers-1 where campaign_id=? and location_id=?", array($Row['campaign_id'], $location_id));
                                                }
                                                // remove coupon codes //
                                        } else {
                                                /* $Sql = "UPDATE coupon_codes SET active=0 where customer_id=".$customer_id." and customer_campaign_code=".$Row['campaign_id']." and location_id=".$location_id ;
                                                  $objDB->Conn->Execute($Sql); */
                                                $objDBWrt->Conn->Execute("UPDATE coupon_codes SET active=? where customer_id=? and customer_campaign_code=? and location_id=?", array(0, $customer_id, $Row['campaign_id'], $location_id));
                                                /* $Sql = "UPDATE customer_campaigns SET activation_status=0 where customer_id=".$customer_id." and campaign_id=".$Row['campaign_id']." and location_id=".$location_id ;
                                                  $objDB->Conn->Execute($Sql); */
                                                $objDBWrt->Conn->Execute("UPDATE customer_campaigns SET activation_status=? where customer_id=? and campaign_id=? and location_id=?", array(0, $customer_id, $Row['campaign_id'], $location_id));
                                        }
                                }
                                // start unsubscribe location then unreserve private deal 2-8-2013 		
                        }
                }

                $json_array['status'] = "true";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

/**
 * @uses click helpful
 * @param like_counter
 * @return string
 */
if (isset($_REQUEST['btn_click_helpful'])) {
        $like_counter = $_REQUEST['like_counter'];
        /* $sql = "select customer_id from review_rating where id=".$_REQUEST['review_id'];
          $RS_user = $objDB->Conn->Execute($sql); */
        $RS_user = $objDB->Conn->Execute("select customer_id from review_rating where id=?", array($_REQUEST['review_id']));
        $cnt = 0;
        $cnt1 = 0;
        if ($RS_user->fields['customer_id'] == $_REQUEST['customer_id']) {
                $json_array['status'] = "false";
                $json_array['counter'] = $cnt;
                //$json_array['counter1'] = $cnt1;
                $json = json_encode($json_array);
                echo $json;
                exit();
        }


        $json_array = array();
        /* $sql ="select * from user_review_like where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];

          $RS = $objDB->Conn->Execute($sql); */
        $RS = $objDB->Conn->Execute("select * from user_review_like where customer_id=? and review_id =?", array($_REQUEST['customer_id'], $_REQUEST['review_id']));

        if ($RS->RecordCount() == 0) {
                $array_ = $json_array = array();
                $array_['customer_id'] = $_REQUEST['customer_id'];
                $array_['review_id'] = $_REQUEST['review_id'];
                $array_['review_like'] = 1;
                $array_['review_unlike'] = 0;
                $array_['like_datetime'] = date('Y-m-d H:i:s');
                $objDB->Insert($array_, "user_review_like");
                /* $sql_update = "update review_rating set is_usefull=is_usefull +1  where id=".$_REQUEST['review_id'];

                  $RS = $objDB->Conn->Execute($sql_update); */
                $RS = $objDBWrt->Conn->Execute("update review_rating set is_usefull=is_usefull +1  where id=?", array($_REQUEST['review_id']));

                $cnt = 1;
                $cnt1 = 0;
        } else {
                if ($RS->fields['review_like'] == 1) {
//			$sql = "update user_review_like  set review_like=0 where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
//			$sql_update = "update review_rating set is_usefull=is_usefull -1    where id=".$_REQUEST['review_id'];
                        $cnt = -1;
                        $cnt1 = 1;
                        $RS = $objDBWrt->Conn->Execute("update user_review_like  set review_like=? where customer_id=? and review_id =?", array(0, $_REQUEST['customer_id'], $_REQUEST['review_id']));
                        $RS = $objDBWrt->Conn->Execute("update review_rating set is_usefull=is_usefull -1    where id=?", array($_REQUEST['review_id']));
                } else {
//			$sql = "update user_review_like set review_like=1  where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
//			$sql_update = "update review_rating set is_usefull=is_usefull +1   where id=".$_REQUEST['review_id'];
                        $cnt = 1;
                        $cnt1 = -1;
                        $RS = $objDBWrt->Conn->Execute("update user_review_like set review_like=?  where customer_id=? and review_id =?", array(1, $_REQUEST['customer_id'], $_REQUEST['review_id']));
                        $RS = $objDBWrt->Conn->Execute("update review_rating set is_usefull=is_usefull +1   where id=?", array($_REQUEST['review_id']));
                }
        }

        $json_array['status'] = "true";
        $json_array['counter'] = $cnt + $like_counter;
        //$json_array['counter1'] = $cnt1;
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses click not helpful
 * @param unlike_counter,review_id
 * @return string
 */

if (isset($_REQUEST['btn_click_nothelpful'])) {
        $unlike_counter = $_REQUEST['unlike_counter'];
        /* 	$sql = "select customer_id from review_rating where id=".$_REQUEST['review_id'];
          $RS_user = $objDB->Conn->Execute($sql); */
        $RS_user = $objDB->Conn->Execute("select customer_id from review_rating where id=?", array($_REQUEST['review_id']));
        $cnt = 0;
        $cnt1 = 0;
        if ($RS_user->fields['customer_id'] == $_REQUEST['customer_id']) {
                $json_array['status'] = "false";
                $json_array['counter'] = $cnt;
                //$json_array['counter1'] = $cnt1;
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
        $json_array = array();
        /* $sql ="select * from user_review_like where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
          $RS = $objDB->Conn->Execute($sql); */
        $RS = $objDB->Conn->Execute("select * from user_review_like where customer_id=? and review_id = ?", array($_REQUEST['customer_id'], $_REQUEST['review_id']));
        if ($RS->RecordCount() == 0) {
                $array_ = $json_array = array();
                $array_['customer_id'] = $_REQUEST['customer_id'];
                $array_['review_id'] = $_REQUEST['review_id'];
                $array_['review_like'] = 0;
                $array_['review_unlike'] = 1;
                $array_['like_datetime'] = date('Y-m-d H:i:s');
                $objDB->Insert($array_, "user_review_like");
                /* $sql_update = "update review_rating set is_notusefull= is_notusefull+1  where id=".$_REQUEST['review_id'];
                  $RS = $objDB->Conn->Execute($sql_update); */
                $RS = $objDBWrt->Conn->Execute("update review_rating set is_notusefull= is_notusefull+1  where id=?", array($_REQUEST['review_id']));
                $cnt = 1;
                $cnt1 = 0;
        } else {
                if ($RS->fields['review_unlike'] == 1) {

//			$sql = "update user_review_like  set review_unlike = 0 where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
//			$sql_update = "update review_rating set is_notusefull=is_notusefull -1   where id=".$_REQUEST['review_id'];
                        $cnt = -1;
                        $cnt1 = -1;
                        $RS = $objDBWrt->Conn->Execute("update user_review_like  set review_unlike = ? where customer_id=? and review_id =?", array(0, $_REQUEST['customer_id'], $_REQUEST['review_id']));
                        $RS = $objDBWrt->Conn->Execute("update review_rating set is_notusefull=is_notusefull -1   where id=?", array($_REQUEST['review_id']));
                } else {
//			$sql = "update user_review_like set  review_unlike = 1 where customer_id=".$_REQUEST['customer_id']." and review_id = ".$_REQUEST['review_id'];
//			$sql_update = "update review_rating set is_notusefull=is_notusefull +1  where id=".$_REQUEST['review_id'];
                        $cnt = 1;
                        $cnt1 = 1;
                        $RS = $objDBWrt->Conn->Execute("update user_review_like set  review_unlike = ? where customer_id=? and review_id =?", array(1, $_REQUEST['customer_id'], $_REQUEST['review_id']));
                        $RS = $objDBWrt->Conn->Execute("update review_rating set is_notusefull=is_notusefull +1  where id=?", array($_REQUEST['review_id']));
                }
        }



        $json_array['status'] = "true";
        $json_array['counter'] = $cnt + $unlike_counter;
        //$json_array['counter1'] = $cnt1;
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses click not helpful
 * @param unlike_counter,review_id
 * @return string
 */
if (isset($_REQUEST['btn_share_campaign'])) {
//echo "inn";
        $timestamp = $_REQUEST['timestamp'];
        $json_array = array();
        $email_arr = explode(";", $_REQUEST['txt_share_frnd']);
        $arr = array();

        for ($i = 0; $i < count($email_arr); $i++) {
                $email_arr1 = explode(",", $email_arr[$i]);
                if (is_array($email_arr1)) {
                        for ($j = 0; $j < count($email_arr1); $j++) {
                                array_push($arr, $email_arr1[$j]);
                        }
                } else {
                        array_push($arr, $email_arr[$i]);
                }
        }

        if (count($arr) > 0) {

                for ($i = 0; $i < count($arr); $i++) {
                        //      echo $arr[$i]."<br />";
                        $array = $json_array = array();
                        $array['emailaddress'] = $arr[$i];

                        /* 15-03-2014 share counter */

                        $array_loc = array();
                        $array_loc['id'] = $_REQUEST['refferal_location_id'];
                        $RS_location = $objDB->Show("locations", $array_loc);
                        $time_zone = $RS_location->fields['timezone_name'];
                        date_default_timezone_set($time_zone);

                        $share_counter = array();
                        $share_counter['customer_id'] = $_REQUEST['customer_id'];
                        $share_counter['campaign_id'] = $_REQUEST['reffer_campaign_id'];
                        $share_counter['location_id'] = $_REQUEST['refferal_location_id'];
                        $share_counter['campaign_share_domain'] = 4;
                        $share_counter['campaign_share_medium'] = 3;
                        $share_counter['timestamp'] = $timestamp;
                        $objDB->Insert($share_counter, "share_counter");

                        /* 15-03-2014 share counter */

                        $activate_link = WEB_PATH . "/register.php?campaign_id=" . $_REQUEST['reffer_campaign_id'] . "&l_id=" . $_REQUEST['refferal_location_id'] . "&share=true&customer_id=" . base64_encode($_REQUEST['customer_id']) . "&domain=4";

                        $mail = new PHPMailer();

                        $array2 = $json_array = array();
                        $array2['id'] = $_REQUEST['refferal_location_id'];
                        $RS_location = $objDB->Show("locations", $array2);

                        $array1 = $json_array = array();
                        $array1['id'] = $_REQUEST['reffer_campaign_id'];
                        $RS_campaigns = $objDB->Show("campaigns", $array1);

                        $array2 = $json_array = array();
                        $array2['id'] = $RS_campaigns->fields['created_by'];
                        $RS_camp_mer = $objDB->Show("merchant_user", $array2);


                        //$body = "<p>I thought you might enjoy knowing this deal from Scanflip.</p>";
                        //$body .= "<p>Please  <a href='".$activate_link."'>Click Here</a> to register / login with scanflip. Share deal with your friend and earn share points </p>";

                        $body = "<div id='dealslist'>";
                        $body .="<div class='offersexyellow' style='background: none repeat scroll 0 0 #EBEBEB !important;border: 1px solid #D1D1D1;color: #000000;margin: 2px 0 10px;padding: 5px 5px 10px;position: relative;'>";
                        $body .="<div id='campaign_detail'>";
                        $body .="<p style='text-align:right;width:645px; margin :0 auto'>";
                        $body .="<img alt='Scanflip' src='" . ASSETS_IMG . "/c/email_sharing.png'>";
                        $body .="</p>";
                        $body .="<div class='offerstrip' style='font-size:18px;background:#fff; width:630px;padding-left:15px;margin :0 auto'><span 
								'>" . $_REQUEST['firstname'] . " " . $_REQUEST['lastname'] . "</span> wants to share deal with you on scanflip</div>";

                        $body .="<div style='overflow:hidden;padding:15px; width:615px; background:#fff; margin :0 auto'>";

                        $body .="<div class='other_details' style='float: right;width:450px; '>";

                        $body .="<div class='dealtitle' style='border-bottom: 1px dashed #C8C8C8;font-size: 19px;overflow: hidden;text-align: justify;'><b>" . $RS_campaigns->fields['title'] . "</b></div>";

                        $body .="<div class='percetage' style='float:left;color: #000000;font-family: Arial;font-size: 16px;line-height: 25px;overflow: hidden;padding: 4px 85px 7px 0;text-shadow: 1px 1px 1px #A09F9F;'>";
                        //$urltitle=WEB_PATH."/location_detail.php?id=".$RS_location->fields['id'];
                        //$body .="<a href='".$urltitle."'>".$RS_location->fields['location_name']."</a>";
                        $body .=$RS_location->fields['location_name'];
                        $body .="</div>";

                        $body .="<div class='counter' style='clear:both;margin: -8px 0 0;overflow: hidden;padding: 0px; float:left;'>";
                        $body .="<div style='clear:both'>";

                        $address = "Location : " . $RS_location->fields['address'] . ", " . $RS_location->fields['city'] . ", " . $RS_location->fields['state'] . ", " . $RS_location->fields['zip'] . ", " . $RS_location->fields['country'];
                        if ($RS_location->fields['phone_number'] != "") {
                                $phno = $RS_location->fields['phone_number'];
                                $phno = explode("-", $phno);
                                $phno = "Phone Number : (" . $phno[1] . ") " . $phno[2] . "-" . $phno[3];
                        } else {
                                $phno = $RS_camp_mer->fields['phone_number'];
                                $phno = explode("-", $phno);
                                $phno = "Phone Number : (" . $phno[1] . ") " . $phno[2] . "-" . $phno[3];
                        }

                        $body .="<div style='margin-top:5px;float:left'>";
                        $body .=$address;
                        $body .="</div>";
                        $body .="<div style='margin-top:5px;float:left'>";
                        $body .=$phno;
                        $body .="</div>";

                        $to_lati = $RS_location->fields['latitude'];

                        $to_long = $RS_location->fields['longitude'];

                        $maphref = "https://maps.google.com/maps?saddr=&daddr=" . $to_lati . "," . $to_long;

                        $body .="</div>";
                        $body .="</div>";
                        $body .="</div>";
                        $body .="<p class=image_det' style='border: 3px solid #BBBBBB;float: left;margin: 0 !important;overflow: hidden;padding: 0 !important;width: 130px;'>";

                        if ($RS_campaigns->fields['business_logo'] != "") {
                                $img_src = ASSETS_IMG . "/m/campaign/" . $RS_campaigns->fields['business_logo'];
                        } else {
                                $img_src = ASSETS_IMG . "/c/Merchant_Offer.png";
                        }
                        $activate_link = WEB_PATH . "/register.php?campaign_id=" . $_REQUEST['reffer_campaign_id'] . "&l_id=" . $_REQUEST['refferal_location_id'] . "&share=true&customer_id=" . base64_encode($_REQUEST['customer_id']) . "&domain=4";
                        $body .="<img style='border: 5px solid #FFFFFF;height: auto !important;vertical-align:middle;width: 120px !important;' border='0' src='" . $img_src . "'>";
                        $body .="</p>";
                        $body .="<div>";
                        $body .="<div class='button_wrapper' style='float:left;height: 30px; padding:12px 4px 0 4px; text-align:left; margin-top: 22px;clear:both;width:145px'>";
                        $body .="<a class='reserve_print' href='" . $activate_link . "' style='background: url(" . ASSETS_IMG . "/c/vie-button.png) repeat-x scroll 0 0 transparent !important;border: 1px solid #FE8915;border-radius:2px;color: #FFFFFF !important;font-size: 12px;font-weight: bold;padding:4px 12px 4px 12px;'>Get Offer</a>";
                        $body .="</div>";

                        $body .="<div style='float:right;padding-top:25px;padding-right:30px'>";
                        $body .="<img style='width:100px' border='0' src='" . ASSETS_IMG . "/c/app_store.png' >";
                        $body .="<img style='width:100px' border='0' src='" . ASSETS_IMG . "/c/google_play.png'>";
                        $body .="</div>";
                        $body .="</div>";
                        $body .="</div>";

                        $body .="<div style='width:630px !important; margin: 0 auto; padding-top:10px; '>";

                        $body .="<img alt='Scanflip' src='" . ASSETS_IMG . "/c/email_sharing.png'>";
                        $body .="<br>";
                        $body .="Powering Smart Savings From Local Merchants";
                        $body .="</div>";
                        $body .="</div>";
                        $body .="</div>";
                        $body .="</div>";
                        $body .= "<p >Thanks, </p>";
                        $body .= "<p >" . $_REQUEST['firstname'] . " " . $_REQUEST['lastname'] . " </p>";
                        $body .="</div>";

                        $newbody = "";
                        $newbody .='<body bgcolor="#e4e4e4" style="font-size:11px;font-family: arial,helvetica neue,helvetica,sans-serif; line-height: normal;color:#606060; margin:8px; padding:0;">
<table cellspacing="0" cellpadding="0"  style="width:100%; border:0;clear:both; margin:20px 0;">
  <tbody style="width:100%; display:inline-block;">
    <tr style="width:100%; display:inline-block;">
      <td style="width:100%; display:inline-block;"><table align="center" bgcolor="#D2D2D2" style="width:100%; max-width:600px; padding:20px; border-radius: 10px;">
          <tbody>
            <tr>
              <td  style="width:100%; display:inline-block;"><img src="' . ASSETS_IMG . '/c/scanflip-logo.png" width="205" height="30" alt="Scanflip"
												style="-ms-interpolation-mode: bicubic; padding:0 0 20px;"></td>
            </tr>
            <tr>
              <td><table bgcolor="#FFF" style="width:100%; display:inline-block;border-left:5px solid #F37E0A;">
                  <tbody>
                    <tr align="left" style=" width:100%; display:inline-block;">
                      <td style="display:inline-block; padding:15px;"><div style="font-size:16px;font-weight: bold; color:#000; width:100%; display:inline-block; font-family:Arial;">' . $_REQUEST['firstname'] . ' ' . $_REQUEST['lastname'] . ' wants to share offer with you on Scanflip</div></td>
                    </tr>
                    <tr bgcolor="#FFF" style="width:100%; display:inline-block;">
                      <td valign="top" style="padding:15px 0px 0px 15px;"><p style=" margin:0;margin:0!;padding:0;"> <img src="' . $img_src . '" width="80" height="80" style="border:3px solid #bbbbbb;padding:5px;min-height:auto;vertical-align:middle;"></p></td>
                      <td  bgcolor="white" style="padding:15px 15px 0px;"><div style=" width:100%; display:inline-block;border-bottom:1px dashed #c8c8c8;font-size:19px;text-align:justify"><b>' . $RS_campaigns->fields['title'] . '</b></div>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0; padding:5px 0; display:inline-block;">' . $RS_location->fields['location_name'] . '</p>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0;display:inline-block;">' . $address . '</p>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0; padding:5px 0;display:inline-block;">' . $phno . '</p>
                        <p style="color:#000; font-family:Arial; font-size:13px; width:100%;margin:0; padding:5px 0;display:inline-block;"></p>
                        </td>
                    </tr>
                    <tr style=" width:100%; display:inline-block;">
                      <td valign="top"; align="left" style="display: block;padding:0 15px 0;font-size:11px;font-family:lucida grande,tahoma,verdana,arial,sans-serif;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;color:#999999;border:none"><h5 align="left"  style="margin:0px; text-align:left; width:auto; padding:15px 0 0 0; display:inline-block;"><a style="background:#F37E0A; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; display:inline-block; border-radius: 3px; color: #FFFFFF; font-size: 14px; padding: 5px 15px; text-align: center; text-decoration: none;" href="' . $activate_link . '">Get Offer</a></h5></td>
                    </tr>
                    <tr style=" width:100%; display:inline-block;">
                      <td valign="top"; align="right" style="display: block;padding:0px 15px 15px;font-size:11px;font-family:lucida grande,tahoma,verdana,arial,sans-serif;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;color:#999999;border:none"><div style=" width:100%; display:inline-block; text-align:right;padding-top:15px;">
                      <img border="0" src="' . ASSETS_IMG . '/c/app_store.png" style="width:100px;display: inline-block; height:auto;">
                      <img border="0" src="' . ASSETS_IMG . '/c/google_play.png" style="width:100px;display: inline-block; height:auto;"></div></td>
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

                        $mail->AddReplyTo('no-reply@scanflip.com', 'ScanFlip Support');
                        $mail->AddAddress($array['emailaddress']);
                        $mail->From = "no-reply@scanflip.com";
                        $mail->FromName = "ScanFlip Support";
                        $mail->Subject = "Scanflip offer - " . $RS_campaigns->fields['title'];
                        //$mail->MsgHTML($body);
                        $mail->MsgHTML($newbody);
                        $mail->Send();

                        $json_array['status'] = "true";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        } else {
                $json_array['status'] = "false";
                $json_array['message'] = "Please check email address. Either email address is not correct or you are missing colon (,) or Semi-colon (;) between email addresses";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

/**
 * @uses get recently viewd campaign
 * @param c1 to c6, l1 to l6
 * @return string
 */
if (isset($_REQUEST['btn_get_recently_viewed_campaigns'])) {
        $json_array = array();
        $count = 0;
        if (!isset($_REQUEST['c1']) && !isset($_REQUEST['c2']) && !isset($_REQUEST['c3']) && !isset($_REQUEST['c4']) && !isset($_REQUEST['c5']) && !isset($_REQUEST['c6']) && !isset($_REQUEST['l1']) && !isset($_REQUEST['l2']) && !isset($_REQUEST['l3']) && !isset($_REQUEST['l4']) && !isset($_REQUEST['l5']) && !isset($_REQUEST['l6'])
        ) {
                $json_array['status'] = "false";
                $json_array['message'] = '';
                $json = json_encode($json_array);
                echo $json;
                exit;
        }

        $json_array['status'] = "true";
        if (isset($_REQUEST["c1"]) && isset($_REQUEST["l1"])) {
                $camp_id = $_REQUEST["c1"];
                $loc_id = $_REQUEST["l1"];
                /* $br_sql = "select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=".$camp_id." and l.id=".$loc_id;

                  $br_rs =  $objDB->Conn->Execute($br_sql); */
                $br_rs = $objDB->Conn->Execute("select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=? and l.id=?", array($camp_id, $loc_id));

                if ($br_rs->RecordCount() > 0) {
                        while ($Row = $br_rs->FetchRow()) {
                                $records[$count] = get_field_value($Row);

                                $image = explode(".", $Row['business_logo']);
                                //echo $image[0].".jpg";
                                $records[$count]["business_logo"] = $image[0] . ".jpg";
                                $records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));
                                $count++;
                        }
                }
        }
        if (isset($_REQUEST["c2"]) && isset($_REQUEST["l2"])) {
                $camp_id = $_REQUEST["c2"];
                $loc_id = $_REQUEST["l2"];
                /* $br_sql = "select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=".$camp_id." and l.id=".$loc_id;

                  $br_rs =  $objDB->Conn->Execute($br_sql); */
                $br_rs = $objDB->Conn->Execute("select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=? and l.id=?", array($camp_id, $loc_id));
                if ($br_rs->RecordCount() > 0) {
                        while ($Row = $br_rs->FetchRow()) {
                                $records[$count] = get_field_value($Row);

                                $image = explode(".", $Row['business_logo']);
                                //echo $image[0].".jpg";
                                $records[$count]["business_logo"] = $image[0] . ".jpg";
                                $records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));
                                $count++;
                        }
                }
        }
        if (isset($_REQUEST["c3"]) && isset($_REQUEST["l3"])) {
                $camp_id = $_REQUEST["c3"];
                $loc_id = $_REQUEST["l3"];
                /* $br_sql = "select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=".$camp_id." and l.id=".$loc_id;

                  $br_rs =  $objDB->Conn->Execute($br_sql); */
                $br_rs = $objDB->Conn->Execute("select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=? and l.id=?", array($camp_id, $loc_id));
                if ($br_rs->RecordCount() > 0) {
                        while ($Row = $br_rs->FetchRow()) {
                                $records[$count] = get_field_value($Row);

                                $image = explode(".", $Row['business_logo']);
                                //echo $image[0].".jpg";
                                $records[$count]["business_logo"] = $image[0] . ".jpg";
                                $records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));
                                $count++;
                        }
                }
        }
        if (isset($_REQUEST["c4"]) && isset($_REQUEST["l4"])) {
                $camp_id = $_REQUEST["c4"];
                $loc_id = $_REQUEST["l4"];
                /* $br_sql = "select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=".$camp_id." and l.id=".$loc_id;

                  $br_rs =  $objDB->Conn->Execute($br_sql); */
                $br_rs = $objDB->Conn->Execute("select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=? and l.id=?", array($camp_id, $loc_id));
                if ($br_rs->RecordCount() > 0) {
                        while ($Row = $br_rs->FetchRow()) {
                                $records[$count] = get_field_value($Row);

                                $image = explode(".", $Row['business_logo']);
                                //echo $image[0].".jpg";
                                $records[$count]["business_logo"] = $image[0] . ".jpg";
                                $records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));
                                $count++;
                        }
                }
        }
        if (isset($_REQUEST["c5"]) && isset($_REQUEST["l5"])) {
                $camp_id = $_REQUEST["c5"];
                $loc_id = $_REQUEST["l5"];
                /* $br_sql = "select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=".$camp_id." and l.id=".$loc_id;

                  $br_rs =  $objDB->Conn->Execute($br_sql); */
                $br_rs = $objDB->Conn->Execute("select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=? and l.id=?", array($camp_id, $loc_id));
                if ($br_rs->RecordCount() > 0) {
                        while ($Row = $br_rs->FetchRow()) {
                                $records[$count] = get_field_value($Row);

                                $image = explode(".", $Row['business_logo']);
                                //echo $image[0].".jpg";
                                $records[$count]["business_logo"] = $image[0] . ".jpg";
                                $records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));
                                $count++;
                        }
                }
        }
        if (isset($_REQUEST["c6"]) && isset($_REQUEST["l6"])) {
                $camp_id = $_REQUEST["c6"];
                $loc_id = $_REQUEST["l6"];
                /* $br_sql = "select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=".$camp_id." and l.id=".$loc_id;

                  $br_rs =  $objDB->Conn->Execute($br_sql); */
                $br_rs = $objDB->Conn->Execute("select c.id campaign_id,c.title,c.business_logo,l.id location_id from campaigns c,locations l ,campaign_location cl where cl.location_id=l.id and cl.campaign_id=c.id and c.id=? and l.id=?", array($camp_id, $loc_id));
                if ($br_rs->RecordCount() > 0) {
                        while ($Row = $br_rs->FetchRow()) {
                                $records[$count] = get_field_value($Row);

                                $image = explode(".", $Row['business_logo']);
                                //echo $image[0].".jpg";
                                $records[$count]["business_logo"] = $image[0] . ".jpg";
                                $records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));
                                $count++;
                        }
                }
        }

        $json_array['records'] = $records;
        $json = json_encode($json_array);
        echo $json;
        exit;
}

/**
 * @uses get user points balance
 * @param timezone,current_location,curr_latitude,curr_longitude
 * @return string
 */
if (isset($_REQUEST['getuserpointsbalance'])) {
        $customer_id = $_REQUEST['customer_id'];

        $array = array();
        $array['lastvisit_date'] = date("Y-m-d H:i:s");

        //$array['curr_timezone'] = $_REQUEST['timezone'];
        $tz = timezone_offset_string($_REQUEST['timezone']);
        $curr_timezone = $tz;
        $array['curr_timezone'] = $curr_timezone;

        $array['current_location'] = urldecode($_REQUEST['current_location']);
        $array['curr_latitude'] = $_REQUEST['curr_latitude'];
        $array['curr_longitude'] = $_REQUEST['curr_longitude'];
        $where_clause['id'] = $customer_id;

        $objDB->Update($array, "customer_user", $where_clause);

        $json_array = array();
        /* $Sql = "Select rw.earned_reward tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint 
          from
          reward_user rw   left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id
          where rw.customer_id = ". $customer_id ."
          group by rw.campaign_id , rw.location_id , rw.customer_id " ;
          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("Select rw.earned_reward tot_redeempoints ,sum(rw.referral_reward) tot_sharingpoint 
             from 
            reward_user rw   left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id
             where rw.customer_id =? 
             group by rw.campaign_id , rw.location_id , rw.customer_id ", array($customer_id));

        $total_balance = 0;
        if ($RS->RecordCount() > 0) {
                while ($Row = $RS->FetchRow()) {
                        $total_balance = $total_balance + $Row['tot_redeempoints'] + $Row['tot_sharingpoint'];
                }
        }

        // start to remove giftcard balance

        $total_sum_gifcard = 0;
        /* $Sql = "select giftcard_id from giftcard_order where user_id=".$customer_id." and status!=0" ;
          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("select giftcard_id from giftcard_order where user_id=? and status!=?", array($customer_id, 0));
        if ($RS->RecordCount() > 0) {
                while ($Row = $RS->FetchRow()) {
                        /* $Sql1 = "select id,redeem_point_value from giftcards where id=".$Row['giftcard_id'] ;
                          $RS1 = $objDB->Conn->Execute($Sql1); */
                        $RS1 = $objDB->Conn->Execute("select id,redeem_point_value from giftcards where id=?", array($Row['giftcard_id']));
                        if ($RS1->RecordCount() > 0) {
                                $total_sum_gifcard = $total_sum_gifcard + $RS1->fields['redeem_point_value'];
                        }
                }
        }

        $total_balance = $total_balance - $total_sum_gifcard;

        // end to remove giftcard balance

        $json_array["point_balance"] = $total_balance;

        $custid = $_REQUEST['customer_id'];
        $count = 0;
        $pending_review_notification = array();

        $cust_array = array();
        $cust_array['id'] = $_REQUEST['customer_id'];
        $cust_info = $objDB->Show("customer_user", $cust_array);
        if ($cust_info->fields['notification_setting'] == 1) {
                // start for mydeals expiring today notification

                $customer_id = $custid;
                /* $expire_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=1 and t.is_read=0 and t.customer_id=".$customer_id;
                  $expire_data=$objDB->Conn->Execute($expire_data_query); */
                $expire_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(1, 0, $customer_id));
                if ($expire_data->RecordCount() > 0) {
                        $count++;
                }

                // end for mydeals expiring today notification
                // start for new campaign notification

                $customer_id = $custid;
                /* $new_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=2 and t.is_read=0 and t.customer_id=".$customer_id;
                  $new_data=$objDB->Conn->Execute($new_data_query); */
                $new_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(2, 0, $customer_id));
                if ($new_data->RecordCount() > 0) {
                        $count++;
                }

                // end for new campaign notification
                // start for pending review notification

                $customer_id = $custid;
                /* $pending_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=3 and t.is_read=0 and t.customer_id=".$customer_id;
                  $pending_data=$objDB->Conn->Execute($pending_data_query); */
                $pending_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(3, 0, $customer_id));
                if ($pending_data->RecordCount() > 0) {
                        $count++;
                }

                // end for pending review notification
                // start for earned recent visit notification

                $customer_id = $custid;
                /* $earned_redeem_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=4 and t.is_read=0 and t.customer_id=".$customer_id;
                  $earned_redeem_data=$objDB->Conn->Execute($earned_redeem_data_query); */
                $earned_redeem_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(4, 0, $customer_id));

                if ($earned_redeem_data->RecordCount() > 0) {
                        $count++;
                }

                // end for earned recent visit notification
                // start for earned new customer referral notification

                $customer_id = $custid;
                /* $earned_referral_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=5 and t.is_read=0 and t.customer_id=".$customer_id;
                  $earned_referral_data=$objDB->Conn->Execute($earned_referral_data_query); */
                $earned_referral_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(5, 0, $customer_id));
                if ($earned_referral_data->RecordCount() > 0) {
                        $count++;
                }

                // end for earned new customer referral notification
        }

        if ($count > 0) {
                $json_array["notification_count"] = $count;
        } else {
                $json_array["notification_count"] = $count;
        }

        /* $Sql = "Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)   ) last_redeem_employee , rw.reward_date ,l.location_name,l.address,l.city,l.state,l.zip,l.country,c.title, rw.campaign_id campaign_id, rw.location_id location_id 
          , rw.coupon_code_id coupon_id ,l.id location_id  , max(redeem_date) last_redeemdate
          from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id
          left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id = ".$customer_id."
          and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=30 and review_rating_visibility=1
          group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate";

          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)   ) last_redeem_employee , rw.reward_date ,l.location_name,l.address,l.city,l.state,l.zip,l.country,c.title, rw.campaign_id campaign_id, rw.location_id location_id 
   , rw.coupon_code_id coupon_id ,l.id location_id  , max(redeem_date) last_redeemdate 
 from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id =? 
and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=? and review_rating_visibility=? 
group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate", array($customer_id, 30, 1));

        $json_array['pending_review_count'] = $RS->RecordCount();

        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses get campaign share link
 * @param campaign_id,location_id,customer_id
 * @return string
 */
if (isset($_REQUEST['btn_get_campaign_share_link'])) {
        $json_array = array();

        $campaign_id = $_REQUEST['campaign_id'];
        $location_id = $_REQUEST['location_id'];
        $customer_id = $_REQUEST['customer_id'];

        $activate_link = WEB_PATH . "/register.php?campaign_id=" . $campaign_id . "&l_id=" . $location_id . "&share=true&customer_id=" . base64_encode($customer_id) . "&domain=";

        $json_array["status"] = 'true';
        $json_array["share_link"] = $activate_link;
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses increment share counter
 * @param timestamp,refferal_location_id,reffer_campaign_id
 * @return string
 */

if (isset($_REQUEST['btn_increment_share_counter'])) {
        $json_array = array();
        $timestamp = $_REQUEST['timestamp'];
        $array_loc = array();
        $array_loc['id'] = $_REQUEST['refferal_location_id'];
        $RS_location = $objDB->Show("locations", $array_loc);
        $time_zone = $RS_location->fields['timezone_name'];
        date_default_timezone_set($time_zone);

        $share_counter = array();
        $share_counter['customer_id'] = $_REQUEST['customer_id'];
        $share_counter['campaign_id'] = $_REQUEST['reffer_campaign_id'];
        $share_counter['location_id'] = $_REQUEST['refferal_location_id'];
        $share_counter['campaign_share_domain'] = $_REQUEST['domain'];
        $share_counter['campaign_share_medium'] = 3;
        $share_counter['timestamp'] = $timestamp;
        $objDB->Insert($share_counter, "share_counter");

        $json_array["status"] = 'true';
        $json = json_encode($json_array);
        echo $json;
        exit();
}
/**
 * @uses decode customer id
 * @param customer_id
 * @return string
 */

if (isset($_REQUEST['decode_customer_id'])) {
        $json_array = array();
        $customer_id = base64_decode($_REQUEST['customer_id']);
        $json_array["status"] = 'true';
        $json_array["customer_id"] = $customer_id;
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses increment pageview counter
 * @param timestamp,refferal_location_id,reffer_campaign_id
 * @return string
 */
if (isset($_REQUEST['btn_increment_pageview_counter'])) {
        $json_array = array();
        $timestamp = $_REQUEST['timestamp'];
        $array_loc = array();
        $array_loc['id'] = $_REQUEST['refferal_location_id'];
        $RS_location = $objDB->Show("locations", $array_loc);
        $time_zone = $RS_location->fields['timezone_name'];
        date_default_timezone_set($time_zone);

        $pageview_array = array();
        $pageview_array['campaign_id'] = $_REQUEST['reffer_campaign_id'];
        $pageview_array['location_id'] = $_REQUEST['refferal_location_id'];
        $pageview_array['pageview_domain'] = $_REQUEST['domain'];
        $pageview_array['pageview_medium'] = 3;
        $pageview_array['timestamp'] = $timestamp;
        $objDB->Insert($pageview_array, "pageview_counter");
        $pageview_array = array();

        $json_array["status"] = 'true';
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses review notification
 * @param customer_id,days
 * @return string
 */
if (isset($_REQUEST['review_notification'])) {
        $customer_id = $_REQUEST['customer_id'];

        $days_cond = "";
        if ($_REQUEST['days'] != "") {
                $days_cond = " and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date))  <=" . $_REQUEST['days'];
        } else {
                $days_cond = "";
        }
        /* $Sql = "Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)   ) last_redeem_employee , rw.reward_date ,l.*,c.* , rw.campaign_id campaign_id, rw.location_id location_id 
          , rw.coupon_code_id coupon_id ,l.id location_id  , max(redeem_date) last_redeemdate
          from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id
          left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id = ".$customer_id."
          and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=30 and review_rating_visibility=1
          group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate";


          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)   ) last_redeem_employee , rw.reward_date ,l.*,c.* , rw.campaign_id campaign_id, rw.location_id location_id 
   , rw.coupon_code_id coupon_id ,l.id location_id  , max(redeem_date) last_redeemdate 
 from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id = ? 
and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=? and review_rating_visibility=? 
group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate", array($customer_id, 30, 1));

        if ($RS->RecordCount() > 0) {
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $json_array['message'] = "You have " . $RS->RecordCount() . " pending reviews for your recent visits.";
                $count = 0;
                while ($Row = $RS->FetchRow()) {
                        $records[$count] = get_field_value($Row);
                        $count++;
                }
                //$json_array["records"]= $records;
        } else {
                $json_array['status'] = "false";
                $json_array['total_records'] = 0;
        }
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses pending review list
 * @param customer_id,days
 * @return string
 */
if (isset($_REQUEST['pending_review_list'])) {
        $customer_id = $_REQUEST['customer_id'];

        $days_cond = "";
        if(isset($_REQUEST['days']))
        {
			if ($_REQUEST['days'] != "") 
			{
					$days_cond = " and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date))  <=" . $_REQUEST['days'];
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
		
        /* $Sql = "Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)   ) last_redeem_employee , rw.reward_date ,l.location_name,l.address,l.city,l.state,l.zip,l.country,c.title, rw.campaign_id campaign_id, rw.location_id location_id 
          , rw.coupon_code_id coupon_id ,l.id location_id  , max(redeem_date) last_redeemdate
          from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id
          left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id = ".$customer_id."
          and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=30 and review_rating_visibility=1
          group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate";

          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)   ) last_redeem_employee , rw.reward_date ,l.location_name,l.address,l.city,l.state,l.zip,l.country,c.title, rw.campaign_id campaign_id, rw.location_id location_id 
   , rw.coupon_code_id coupon_id ,l.id location_id  , max(redeem_date) last_redeemdate 
 from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id =? 
and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=? and review_rating_visibility=? 
group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate", array($customer_id, 30, 1));

        if ($RS->RecordCount() > 0) {
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $count = 0;
                while ($Row = $RS->FetchRow()) {
                        $records[$count] = get_field_value($Row);

                        $arr = file(WEB_PATH . '/includes/customer/process_mobile.php?getlocationbusinessname=yes&l_id=' . $Row['location_id']);
                        if (trim($arr[0]) == "") {
                                unset($arr[0]);
                                $arr = array_values($arr);
                        }
                        $json = json_decode($arr[0]);
                        $records[$count]['busines_name'] = $json->bus_name;
                        $count++;
                }
                $json_array["records"] = $records;
        } else {
                $json_array['status'] = "false";
                $json_array['total_records'] = 0;
        }
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses give ratings
 * @param customer_id,camp_id,loc_id
 * @return string
 */

if (isset($_REQUEST['giveratings'])) {
        //$sql = "insert into review_rating ";
        //counting for avarage rating
        //$avg_rating = 
        //counting
        //$review =  str_replace(' ', '&nbsp;', trim($_REQUEST['reviews']));


        $array_ = $json_array = array();
        $array_['customer_id'] = $_REQUEST['customer_id'];
        $array_['campaign_id'] = $_REQUEST['camp_id'];
        $array_['location_id'] = $_REQUEST['loc_id'];
        $array_['spam_flag'] = 0;
        $array_['spam_flag_counter'] = 0;
        $array_['is_notusefull'] = 0;
        $array_['is_usefull'] = 0;
        $array_['platform'] = $_REQUEST['platform'];
        $array_['reviewed_datetime'] = date('Y-m-d H:i:s');
        $array_['review'] = trim($_REQUEST['reviews']);
        $array_['rating'] = $_REQUEST['ratings'];
        $array_['employee_id'] = $_REQUEST['employee_id'];
        $objDB->Insert($array_, "review_rating");

        if ($_REQUEST['reviews'] == "") {
                $review_increase_counter = 0;
        } else {
                $review_increase_counter = 1;
        }
        /* $old_avg_rating_sql = "select AVG(rating) avarage_rating  from review_rating where location_id = ".$_REQUEST['loc_id'];
          $old_avg_rating_rs =  $objDB->Conn->Execute($old_avg_rating_sql); */
        $old_avg_rating_rs = $objDB->Conn->Execute("select AVG(rating) avarage_rating  from review_rating where location_id = ?", array($_REQUEST['loc_id']));
        $old_avg_rating = $old_avg_rating_rs->fields['avarage_rating'];


        $update_array = array();
        /* $sql = "update reward_user  set review_rating_visibility=0 where customer_id=".$_REQUEST['customer_id']." and campaign_id=".$_REQUEST['camp_id']."
          and location_id=".$_REQUEST['loc_id'];
          $RS = $objDB->Conn->Execute($sql); */
        $RS = $objDBWrt->Conn->Execute("update reward_user  set review_rating_visibility=? where customer_id=? and campaign_id=?
	 and location_id=?", array(0, $_REQUEST['customer_id'], $_REQUEST['camp_id'], $_REQUEST['loc_id']));


        /* $sql = "update locations set avarage_rating=".$old_avg_rating." , no_of_rating=no_of_rating +1 , no_of_reviews = no_of_reviews + ".$review_increase_counter."  where id=".$_REQUEST['loc_id'];
          $RS = $objDB->Conn->Execute($sql); */
        $RS = $objDBWrt->Conn->Execute("update locations set avarage_rating=? , no_of_rating=no_of_rating +1 , no_of_reviews = no_of_reviews + " . $review_increase_counter . "  where id=?", array($old_avg_rating, $_REQUEST['loc_id']));

        $json_array = array();
        $json_array['status'] = "true";
        $json = json_encode($json_array);
        echo $json;
        exit();
}


/**
 * @uses give sharing points
 * @param timestamp,reffer_campaign_id,refferal_location_id
 * @return string
 */
if (isset($_REQUEST['give_sharing_points'])) {
        $json_array = array();

        $timestamp = $_REQUEST['timestamp'];
        $campaign_id = $_REQUEST['reffer_campaign_id'];
        $location_id = $_REQUEST['refferal_location_id'];
        $cid = $_REQUEST['customer_id'];
        $refer_customer_id = $_REQUEST['refer_customer_id']; // encoded customer id

        $array_loc = array();
        $array_loc['id'] = $_REQUEST['refferal_location_id'];
        $RS_location = $objDB->Show("locations", $array_loc);
        $time_zone = $RS_location->fields['timezone_name'];
        date_default_timezone_set($time_zone);

        $pageview_array = array();
        $pageview_array['campaign_id'] = $_REQUEST['reffer_campaign_id'];
        $pageview_array['location_id'] = $_REQUEST['refferal_location_id'];
        $pageview_array['pageview_domain'] = $_REQUEST['domain'];
        $pageview_array['pageview_medium'] = 3;
        $pageview_array['timestamp'] = $timestamp;
        $objDB->Insert($pageview_array, "pageview_counter");
        $pageview_array = array();

        $json_array['share'] = "true";
        $json_array['c_id'] = $campaign_id;
        $json_array['l_id'] = $location_id;

        /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list */
        //     echo "Innn";
        /* 	$Sql_max_is_walkin = "SELECT is_walkin , new_customer, level  from campaigns WHERE id=".$campaign_id;
          $RS_max_is_walkin = $objDB->Conn->Execute( $Sql_max_is_walkin); */
        $RS_max_is_walkin = $objDB->Conn->Execute("SELECT is_walkin , new_customer, level  from campaigns WHERE id=?", array($campaign_id));
        //  if($RS_max_is_walkin->fields['is_walkin'] == 1)
        //  {
        /* $Sql_c_c = "SELECT * FROM customer_campaigns WHERE customer_id='".$customer_id."' AND campaign_id='".$campaign_id."' AND location_id =".$location_id;
          $RS_c_c = $objDB->Conn->Execute($Sql_c_c); */
        $RS_c_c = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?", array($customer_id, $campaign_id, $location_id));

        if ($RS_c_c->RecordCount() <= 0) {
                /* $Sql = "SELECT * FROM activation_codes WHERE campaign_id=".$campaign_id;

                  $RS_1 = $objDB->Conn->Execute($Sql); */
                $RS_1 = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE campaign_id=?", array($campaign_id));
                if ($RS_1->RecordCount() > 0) {
                        /* $location_max_sql = "Select num_activation_code , offers_left,used_offers from campaign_location where  campaign_id=".$campaign_id." and location_id=".$location_id;
                          $location_max = $objDB->Conn->Execute($location_max_sql); */
                        $location_max = $objDB->Conn->Execute("Select num_activation_code , offers_left,used_offers from campaign_location where  campaign_id=? and location_id=?", array($campaign_id, $location_id));
                        $offers_left = $location_max->fields['offers_left'];
                        $used_campaign = $location_max->fields['used_offers'];
                        $o_left = $location_max->fields['offers_left'];
                        $share_flag = 1;
                        $its_new_user = 0;

                        // RESERVE DEAL LOGIC
                        if ($o_left > 0) {
                                if ($RS_max_is_walkin->fields['new_customer'] == 1) {
                                        /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=".  $location_id.")";

                                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
                                        $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)", array($cid, $location_id));

                                        if ($subscibed_store_rs->RecordCount() == 0) {
                                                $its_new_user = 1;
                                                $share_flag = 1;
                                        } else {
                                                $its_new_user = 0;
                                                $share_flag = 0;
                                        }
                                }

                                /* check whether new customer for this store */
                                $allow_for_reserve = 0;
                                $is_new_user = 0;
                                /*                                 * *************    ************************ */
                                /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=". $location_id.") ";
                                  $Rs_is_new_customer=$objDB->Conn->Execute($sql_chk); */
                                $Rs_is_new_customer = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?) ", array($cid, $location_id));
                                if ($Rs_is_new_customer->RecordCount() == 0) {
                                        $is_new_user = 1;
                                } else {
                                        $is_new_user = 0;
                                }

                                /*                                 * ************* ************************ */
                                if ($is_new_user == 1) {
                                        $allow_for_reserve = 1;
                                } else {
                                        /* $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$campaign_id." and cg.group_id=mg.id and mg.location_id=".$location_id;
                                          $RS_campaign_groups = $objDB->Conn->Execute($sql); */
                                        $RS_campaign_groups = $objDB->Conn->Execute("Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=? and cg.group_id=mg.id and mg.location_id=?", array($campaign_id, $location_id));

                                        $c_g_str = "";
                                        $cnt = 1;

                                        $is_it_in_group = 0;
                                        if ($RS_max_is_walkin->fields['level'] == 0) {
                                                if ($RS_max_is_walkin->fields['is_walkin'] == 0) {
                                                        if ($RS_campaign_groups->RecordCount() > 0) {
                                                                while ($Row_campaign = $RS_campaign_groups->FetchRow()) {
                                                                        $c_g_str = $Row_campaign['group_id'];
                                                                        if ($cnt != $RS_campaign_groups->RecordCount()) {
                                                                                $c_g_str .= ",";
                                                                        }
                                                                }
                                                                /* $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='".$cid."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                                                  $RS_check_s = $objDB->Conn->Execute($Sql_new_); */
                                                                $RS_check_s = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? AND group_id in( select  id from merchant_groups where id in(?)  )", array($cid, $c_g_str));
                                                                while ($Row_Check_Cust_group = $RS_check_s->FetchRow()) {
                                                                        /* $query = "Select * from merchant_subscribs where  user_id='".$cid."' and group_id=".$Row_Check_Cust_group['group_id']." and group_id in (". $c_g_str.") ";

                                                                          $RS_query = $objDB->Conn->Execute($query); */
                                                                        $RS_query = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=? and group_id in (?) ", array($cid, $Row_Check_Cust_group['group_id'], $c_g_str));

                                                                        if ($RS_query->RecordCount() > 0) {
                                                                                $is_it_in_group = 1;
                                                                        }
                                                                }
                                                                if ($is_it_in_group == 1) {
                                                                        $allow_for_reserve = 1;
                                                                } else {
                                                                        $allow_for_reserve = 0;
                                                                }
                                                        } else {
                                                                $allow_for_reserve = 0;
                                                        }
                                                }
                                                //If it is walkin deal
                                                else {
                                                        // $sql= "Select * from  campaign_groups cg , merchant_groups mg where cg.campaign_id=".$_COOKIE['campaign_id']." and cg.group_id=mg.id and mg.location_id=".$_COOKIE['l_id'];
                                                        /* $query = "Select * from merchant_subscribs where  user_id=".$cid." and group_id=( select id from merchant_groups mg where mg.location_id=".$location_id." and mg.private =1 ) ";

                                                          $RS_all_user_group = $objDB->Conn->Execute($query); */
                                                        $RS_all_user_group = $objDB->Conn->Execute("Select * from merchant_subscribs where  user_id=? and group_id=( select id from merchant_groups mg where mg.location_id=? and mg.private =? ) ", array($cid, $location_id, 1));

                                                        if ($RS_all_user_group->RecordCount() > 0) {
                                                                $allow_for_reserve = 1;
                                                        } else {
                                                                $allow_for_reserve = 0;
                                                        }
                                                }
                                        } else {
                                                //   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where id in(".$c_g_str.")  )";
                                                $Sql_new_ = "SELECT * FROM merchant_subscribs WHERE user_id='" . $cid . "' AND group_id in( select  id from merchant_groups where location_id  =" . $location_id . "  )";
                                                $allow_for_reserve = 1;
                                        }
                                }
                                // echo "<br />SQl_new===".$Sql_new_ ."=====<br />";

                                /* for checking whether customer in campaign group */


                                /* check whether new customer for this store */
                                //                 echo $allow_for_reserve."===allow for reserve";
                                //                 exit;
                                if ($share_flag == 1) {
                                        if ($allow_for_reserve == 1 || $its_new_user == 1) {
                                                $activation_code = $RS_1->fields['activation_code'];
                                                /* $Sql = "INSERT INTO customer_campaigns SET  activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
                                                  customer_id='".$cid."', campaign_id=".$campaign_id." , location_id=".$location_id;
                                                  $objDB->Conn->Execute($Sql); */
                                                $objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET  activation_status='?', activation_code=?, activation_date= ?, coupon_generation_date=?,
						customer_id=?, campaign_id=? , location_id=?", array(1, $activation_code, 'Now()', 'Now()' . $cid, $campaign_id, $location_id));

                                                //$RSLocation_nm  =  $objDB->Conn->Execute("select * from locations where id =".$location_id);
                                                $RSLocation_nm = $objDB->Conn->Execute("select * from locations where id =?", array($location_id));

                                                //$br = $cid.substr($activation_code,0,2).$campaign_id.substr($RSLocation_nm->fields['location_name'],0,2).$location_id;
                                                $br = $objJSON->generate_voucher_code($cid, $activation_code, $campaign_id, $RSLocation_nm->fields['location_name'], $location_id);
                                                /* $insert_coupon_code = "Insert into coupon_codes set customer_id=".$cid." , customer_campaign_code=".$campaign_id." , coupon_code='".$br."' , active=1 , location_id=".$location_id." , generated_date='".date('Y-m-d H:i:s')."' ";

                                                  $objDB->Conn->Execute($insert_coupon_code); */
                                                $objDBWrt->Conn->Execute("Insert into coupon_codes set customer_id=? , customer_campaign_code=? , coupon_code=? , active=? , location_id=? , generated_date=? ", array($cid, $campaign_id, $br, 1, $location_id, date('Y-m-d H:i:s')));

                                                /* $update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$campaign_id." and location_id =".$location_id." ";
                                                  $objDB->Conn->Execute($update_num_activation); */
                                                $objDBWrt->Conn->Execute("Update  campaign_location set offers_left=? , used_offers=? where campaign_id=" . $campaign_id . " and location_id =" . $location_id . " ", array(($offers_left - 1), ($used_campaign + 1)));
                                        }
                                }
                        }
                }
        }

        /*  check whether campaign is walkin or not  if yes then make entry in shared user's  campaign list  */

        /* check for whether max sharing reached and user is firt time subscribed to this loaction */
        /* $Sql_max_no_location = "SELECT max_no_sharing from campaigns WHERE id=".$campaign_id;
          $RS_max_no_location = $objDB->Conn->Execute( $Sql_max_no_location); */
        $RS_max_no_location = $objDB->Conn->Execute("SELECT max_no_sharing from campaigns WHERE id=?", array($campaign_id));
        $Sql_shared = "SELECT * from reward_user WHERE campaign_id=" . $campaign_id . " and referred_customer_id<>0";
        // $RS_shared = $objDB->Conn->Execute($Sql_shared);
        //if($RS_shared->RecordCount() < $RS_max_no_location->fields['max_no_sharing'] ){
        //  $sql_chk ="select * from subscribed_stores where customer_id= ".$cid." and location_id=". $_COOKIE['l_id'];
        /* $sql_chk ="select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=".$cid." and location_id=". $location_id.")";
          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
        $subscibed_store_rs = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in( select id from coupon_codes where customer_id=? and location_id=?)", array($cid, $location_id));

        if ($allow_for_reserve == 1 || $its_new_user == 1) {
                $redeem_array = array();
                $redeem_array['customer_id'] = base64_decode($refer_customer_id); //$_SESSION['customer_id'];
                $redeem_array['campaign_id'] = $campaign_id;
                $redeem_array['earned_reward'] = 0;
                $redeem_array['referral_reward'] = 0;
                $redeem_array['referred_customer_id'] = $cid;
                $redeem_array['reward_date'] = date("Y-m-d H:i:s");
                $redeem_array['coupon_code_id'] = 0;
                $redeem_array['location_id'] = $location_id;
                $objDB->Insert($redeem_array, "reward_user");
        }

        /* check whether maximum share reached / sharing count now 0 so send mail to merchant */

        /* $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$campaign_id." and referred_customer_id<>0";
          $RS_shared = $objDB->Conn->Execute($Sql_shared); */
        $RS_shared = $objDB->Conn->Execute("SELECT * from reward_user WHERE campaign_id=? and referred_customer_id<>0", array($campaign_id));

        /* $Sql_active = "SELECT active,offers_left from campaign_location WHERE campaign_id=".$campaign_id." and location_id=".$location_id;
          $RS_active = $objDB->Conn->Execute($Sql_active); */
        $RS_active = $objDB->Conn->Execute("SELECT active,offers_left from campaign_location WHERE campaign_id=? and location_id=?", array($campaign_id, $location_id));



        if ($RS_shared->RecordCount() <= $RS_max_no_location->fields['max_no_sharing'] && $RS_active->fields['offers_left'] > 0 && $RS_active->fields['active']) {
                /* $Sql_created_by = "SELECT created_by from locations WHERE id=".$location_id;

                  $RS_created_by = $objDB->Conn->Execute($Sql_created_by); */
                $RS_created_by = $objDB->Conn->Execute("SELECT created_by from locations WHERE id=?", array($location_id));

                $merchantid = $RS_created_by->fields['created_by'];

                /* $Sql_merchant_detail = "SELECT * from merchant_user WHERE id=".$merchantid;

                  $RS_merchant_detail = $objDB->Conn->Execute($Sql_merchant_detail); */
                $RS_merchant_detail = $objDB->Conn->Execute("SELECT * from merchant_user WHERE id=?", array($merchantid));

                /* $Sql_campaigns_detail = "SELECT * from campaigns WHERE id=".$campaign_id;
                  $RS_campaigns_detail = $objDB->Conn->Execute($Sql_campaigns_detail); */
                $RS_campaigns_detail = $objDB->Conn->Execute("SELECT * from campaigns WHERE id=?", array($campaign_id));

                $mail = new PHPMailer();
                $merchant_id = $RS_merchant_detail->fields['id'];
                $email_address = $RS_merchant_detail->fields['email'];
                //$email_address="test.test1397@gmail.com";
                $body = "<div>Hello,<span style='font-weight:bold'>" . $RS_merchant_detail->fields['firstname'] . " " . $RS_merchant_detail->fields['lastname'] . "</span></div>";
                $body.="<br>";
                $body.="<div>Congratulations! Sharing points for <span style='font-weight:bold'>" . $RS_campaigns_detail->fields['title'] . "</span> were allocated for new customer referral. 
		Please <a herf='" . WEB_PATH . "/merchant/register.php' > log in </a> if you wish to increase number of referral customers limit for your campaign  . </div>";
                $body.="<br>";
                $body.="<div>Sincerely,</div>";
                $body.="<div>Scanflip Support Team</div>";

                $mail->AddReplyTo('no-reply@scanflip.com', 'ScanFlip Support');

                $mail->AddAddress($email_address);

                $mail->From = "no-reply@scanflip.com";
                $mail->FromName = "ScanFlip Support";
                $mail->Subject = "Scanflip offer - " . $RS_campaigns_detail->fields['title'];
                $mail->MsgHTML($body);
                $mail->Send();
        }

        //Make entry in subscribed_stre table for first time subscribe to loaction
        /* $sql_group = "select id , merchant_id from merchant_groups where location_id =". $location_id." and private = 1";
          $RS_group = $objDB->Conn->Execute($sql_group); */
        $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?", array($location_id, 1));

        /* $sql_chk ="select * from subscribed_stores where customer_id= ".$cid." and location_id=".$location_id;
          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); */
        $subscibed_store_rs = $objDB->Conn->Execute("select * from subscribed_stores where customer_id=? and location_id=?", array($cid, $location_id));

        if ($subscibed_store_rs->RecordCount() == 0) {
                /* $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$cid." ,location_id=".$location_id." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
                  $objDB->Conn->Execute($insert_subscribed_store_sql); */
                $objDBWrt->Conn->Execute("insert into subscribed_stores set customer_id= ? ,location_id=? ,subscribed_date=? ,subscribed_status=?", array($cid, $location_id, date('Y-m-d H:i:s'), 1));
        } else {
                if ($subscibed_store_rs->fields['subscribed_status'] == 0) {
                        /* $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$cid." and location_id=".$location_id;
                          $objDB->Conn->Execute($up_subscribed_store); */
                        $objDBWrt->Conn->Execute("Update subscribed_stores set subscribed_status=?  where  customer_id=? and location_id=?", array(1, $cid, $location_id));
                }
        }
        /* check whether share user in stores's private group */
        /* $Sql = "SELECT * FROM merchant_subscribs WHERE user_id=".$cid." and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=".$location_id."  )";
          $RS_new = $objDB->Conn->Execute($Sql); */
        $RS_new = $objDB->Conn->Execute("SELECT * FROM merchant_subscribs WHERE user_id=? and group_id in( select id merchant_groups from merchant_groups where private=1 and location_id=?)", array($cid, $location_id));
        if ($RS_new->RecordCount() <= 0) {
                /* $sql_group = "select id , merchant_id from merchant_groups where location_id =".$location_id." and private = 1";
                  $RS_group = $objDB->Conn->Execute($sql_group); */
                $RS_group = $objDB->Conn->Execute("select id , merchant_id from merchant_groups where location_id =? and private = ?", array($location_id, 1));
                $array_group = array();
                $array_group['merchant_id'] = $RS_group->fields['merchant_id'];
                $array_group['group_id'] = $RS_group->fields['id'];
                $array_group['user_id'] = $cid;
                $objDB->Insert($array_group, "merchant_subscribs");
        }

        $json_array["status"] = 'true';
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses scan qr code
 * @param qrcode,current_latitude,current_longitude
 * @return string
 */
if (isset($_REQUEST['scan_qrcode'])) {

        $json_array = array();

        $qrcode = base64_decode($_REQUEST['qrcode']);
//echo $qrcode;
        $json_array['qrcode'] = $qrcode;
        $mlatitude = $_REQUEST['current_latitude'];
        $mlongitude = $_REQUEST['current_longitude'];

        //$curr_timezone = $_REQUEST['current_timezone'];
        $tz = timezone_offset_string($_REQUEST['current_timezone']);
        $curr_timezone = $tz;
        $curr_timezone = $curr_timezone;

        /* $sql_qrcode = "select id from qrcodes where qrcode='".$qrcode."' ";

          $RS_qrcode = $objDB->Conn->Execute($sql_qrcode ); */
        $RS_qrcode = $objDB->Conn->Execute("select id from qrcodes where qrcode=?", array($qrcode));

        $q_id = $RS_qrcode->fields['id'];
        /* $sql = "select campaign_id from  qrcode_campaign where qrcode_id =".$q_id;

          $RS = $objDB->Conn->Execute($sql); */
          
        //$RS = $objDB->Conn->Execute("select campaign_id from  qrcode_campaign where qrcode_id =?", array($q_id));
        $RS = $objDB->Conn->Execute("select id 'campaign_id' from campaigns where qrcode_id  =?", array($q_id));
        
        $_qid = 0;
        $_campid = 0;
        $_locationid = 0;
        $_islocation = 0;
        if (!isset($_REQUEST['customer_id'])) {
                $custid = 0;
        } else {
                $custid = $_REQUEST['customer_id'];
        }

        if ($RS->RecordCount() != 0) {
                $campaignid = $RS->fields['campaign_id'];
                if (isset($_REQUEST['current_latitude'])) {


                        if (isset($_REQUEST['customer_id'])) {
                                if (isset($_REQUEST['current_address']) && $_REQUEST['current_address'] != "") {
                                        /* $update_sql = "Update customer_user set current_location ='".$_REQUEST['current_address']."' , curr_latitude='".$mlatitude."' , curr_longitude='".$mlongitude."' ,curr_timezone= '".$curr_timezone."'  where id=".$_REQUEST['customer_id'];

                                          $objDB->Conn->Execute($update_sql); */
                                        $objDBWrt->Conn->Execute("Update customer_user set current_location =? , curr_latitude=? , curr_longitude=? ,curr_timezone=?  where id=?", array(urldecode($_REQUEST['current_address']), $mlatitude, $mlongitude, $curr_timezone, $_REQUEST['customer_id']));
                                }
                        }

                        /* $Sql  = "SELECT l.id location_id ,l.location_name,l.address,l.city,l.state
                          ,l.zip,l.country,l.picture,l.latitude,l.longitude,l.is_open,round((((acos(sin((".$mlatitude."*pi()/180)) *
                          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
                          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
                          pi()/180))))*180/pi())*60*1.1515 ),2) AS miles_away
                          FROM campaign_location cl inner join locations l on l.id = cl.location_id
                          where cl.offers_left>0 and  cl.campaign_id=".$campaignid." and cl.active =1
                          ORDER BY miles_away" ;

                          $RS_location = $objDB->Conn->Execute($Sql); */
                        $RS_location = $objDB->Conn->Execute("SELECT l.id location_id ,l.location_name,l.address,l.city,l.state
 ,l.zip,l.country,l.picture,l.latitude,l.longitude,l.is_open,round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ),2) AS miles_away 
       FROM campaign_location cl inner join locations l on l.id = cl.location_id 
where cl.offers_left>0 and  cl.campaign_id=? and cl.active =?
        ORDER BY miles_away", array($campaignid, 1));

                        $json_array['redirecting_path'] = "";
                        $locationid = $RS_location->fields['location_id'];
                        if ($RS_location->RecordCount() == 1) {
                                $json_array['is_campaign'] = 1;
                                $redirect_str = WEB_PATH . "/campaign.php?campaign_id=" . $campaignid . "&l_id=" . $locationid;
                                $json_array['campaign_id'] = $campaignid;
                                $json_array['location_id'] = $locationid;
                                $json_array['status'] = "true";
                                $json_array['redirecting_path'] = $redirect_str;
                                $json_array['is_multiple_location'] = 0;
                        } else if ($RS_location->RecordCount() > 1) {
                                $json_array['is_campaign'] = 1;
                                $json_array['campaign_id'] = $campaignid;
                                $json_array['is_multiple_location'] = 1;
                                /* 	$sql = "select  l.id location_id ,l.location_name,l.address,l.city,l.state
                                  ,l.zip,l.country,l.picture,l.latitude,l.longitude,l.is_open,
                                  round((((acos(sin((".$mlatitude."*pi()/180)) *
                                  sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
                                  cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
                                  pi()/180))))*180/pi())*60*1.1515 ),2) as miles_away
                                  FROM campaign_location cl inner join locations l on l.id = cl.location_id
                                  where cl.offers_left>0 and  cl.campaign_id=".$campaignid." and cl.active=1"; */
                                $json_array['status'] = "true";
                                // $RSdata = $objDB->Conn->Execute($sql);
                                $count = 0;
                                //  print_r($RSdata );

                                while ($Row_campaign = $RS_location->FetchRow()) {

                                        $from_lati1 = $mlatitude;

                                        $from_long1 = $mlongitude;

                                        $to_lati1 = $Row_campaign['latitude'];

                                        $to_long1 = $Row_campaign['longitude'];

                                        $deal_distance = $objJSON->distance($from_lati1, $from_long1, $to_lati1, $to_long1, "M") . "Mi";

                                        if ($deal_distance <= 20) {
                                                $location_records[$count] = get_field_value($Row_campaign);
                                                if ($Row_campaign['location_name'] == "") {
                                                        /* $sql = "select business from merchant_user where id=(select created_by from locations where id=".$locationid.")";
                                                          $RS_bus= $objDB->Conn->Execute($sql); */
                                                        $RS_bus = $objDB->Conn->Execute("select business from merchant_user where id=(select created_by from locations where id=?)", array($locationid));
                                                        $busines_name = $RS_bus->fields['business'];
                                                        $location_records[$count]['business_name'] = $busines_name;
                                                } else {
                                                        $location_records[$count]['business_name'] = $Row_campaign['location_name'];
                                                }
                                                $location_records[$count]['miles_away'] = $deal_distance;

                                                $count++;
                                        }
                                }
                                if ($count >= 1) {
                                        $json_array['location_list'] = $location_records;
                                } else {
                                        $json_array['error_msg'] = "All offers are currently reserved at participating locations";
                                        $json_array['status'] = "false";
                                        $json_array['is_campaign'] = 1;
                                        $json_array['is_qrcode_expire'] = 0;
                                }
                                //$redirect_str = WEB_PATH."/campaign.php?campaign_id=".$campaignid."&l_id=".$locationid;
                        } else {
                                $json_array['error_msg'] = "All offers are currently reserved at participating locations";
                                $json_array['status'] = "false";
                                $json_array['is_campaign'] = 1;
                                $json_array['is_qrcode_expire'] = 0;
                        }
                }
        } else {

                /* $sql = "select location_id  from  qrcode_location where qrcode_id  =".$q_id;

                  $RS_loc = $objDB->Conn->Execute($sql); */
                  
                //$RS_loc = $objDB->Conn->Execute("select location_id  from  qrcode_location where qrcode_id  =?", array($q_id));
				$RS_loc = $objDB->Conn->Execute("select id 'location_id' from locations where qrcode_id  =?", array($q_id));
                
                if ($RS_loc->RecordCount() != 0) {

                        $json_array['status'] = "true";
                        $json_array['is_campaign'] = 0;
                        $json_array['location_id'] = $RS_loc->fields['location_id'];
                        $locationid = $RS_loc->fields['location_id'];
                        $redirect_str = WEB_PATH . "/location_detail.php?id=" . $locationid;
                        $json_array['redirecting_path'] = $redirect_str;
                } else {
                        $json_array['status'] = "false";
                        $json_array['is_qrcode_expire'] = 1;
                        $json_array['error_msg'] = "QR Code is currently not linked to any active campaign or location";
                        $json_array['is_campaign'] = 0;
                        //$redirect_str = WEB_PATH."/search-deal.php";
                        //$json_array['redirecting_path'] = $redirect_str;
                        /*   $Sql  = "select * from qrcode_group g , qrcodegroup_qrcode qq where g.id = qq.qrcodegroup_id and qq.qrcode_id  =".$q_id."  ";

                          $RS_is_assigned = $objDB->Conn->Execute($Sql);
                          //  print_r($RS_is_assigned);
                          //echo $RS_is_assigned->fields['merchant_id']."==";
                          // exit();
                          if($RS_is_assigned->fields['merchant_id'] != 0){
                          //if()
                          $qrcodegenerator = $RS_is_assigned->fields['merchant_id'];


                          //////////  if user login  then set currant latitude and longitude ///////
                          if(isset($_SESSION['customer_id']))
                          {
                          $update_sql = "Update customer_user set current_location ='".$_REQUEST['current_address']."' , curr_latitude='".$mlatitude."' , curr_longitude='".$mlongitude."' ,curr_timezone= '".$curr_timezone."'  where id=".$_REQUEST['customer_id'];

                          $objDB->Conn->Execute($update_sql);
                          }

                          $Sql  = "SELECT *, ( 3959 * acos( cos( radians($mlatitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($mlongitude) ) +
                          sin( radians($mlatitude) ) * sin( radians(
                          latitude ) ) ) ) AS distance
                          FROM locations where created_by =".$qrcodegenerator."  and active=1
                          ORDER BY distance LIMIT 1" ;
                          //echo $Sql;
                          //  exit;
                          $RS_location = $objDB->Conn->Execute($Sql);
                          if( $RS_location->RecordCount() != 0){
                          $locationid= $RS_location->fields['id'];
                          $sql_l  = "select latitude,longitude , zip from  locations where id  =".$RS_location->fields['id'];
                          $RS_locdetail = $objDB->Conn->Execute($sql_l);
                          // echo $sql ;
                          $json_array['status'] = "true";
                          $json_array['is_campaign'] = 2;
                          $json_array['location_id'] = $locationid;
                          $locationid = $locationid;
                          $redirect_str = WEB_PATH."/location_detail.php?id=".$locationid;
                          $json_array['redirecting_path'] = $redirect_str;

                          }else{
                          $json_array['status'] = "false";
                          $json_array['is_campaign'] = 2;

                          $redirect_str = WEB_PATH."/search-deal.php";
                          $json_array['redirecting_path'] = $redirect_str;

                          }
                          }
                          else{
                          $json_array['status'] = "false";
                          $json_array['is_campaign'] = 0;

                          $redirect_str = WEB_PATH."/search-deal.php";
                          $json_array['redirecting_path'] = $redirect_str;
                          } */
                }
        }

        $json = json_encode($json_array);
        echo $json;
}


/**
 * @uses get notification of user
 * @param customer_id
 * @return string
 */
if (isset($_REQUEST['get_notification_of_user'])) {
        $custid = $_REQUEST['customer_id'];
        $json_array = array();
        $count = 0;
        $pending_review_notification = array();

        $cust_array = array();
        $cust_array['id'] = $_REQUEST['customer_id'];
        $cust_info = $objDB->Show("customer_user", $cust_array);

        if ($cust_info->fields['notification_setting'] == 1) {

                // start for mydeals expiring today notification

                $customer_id = $custid;
                /* $expire_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=1 and t.is_read=0 and t.customer_id=".$customer_id;
                  $expire_data=$objDB->Conn->Execute($expire_data_query); */
                $expire_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(1, 0, $customer_id));
                if ($expire_data->RecordCount() > 0) {
                        while ($Row = $expire_data->FetchRow()) {
                                if ($Row['counter'] == 1) {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = $Row['counter'] . " campaign reserved by you is expiring today.";
                                } else {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = $Row['counter'] . " campaigns reserved by you are expiring today.";
                                }
                        }
                        $count++;
                }


                // end for mydeals expiring today notification
                // start for new campaign notification

                $customer_id = $custid;
                /* $new_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=2 and t.is_read=0 and t.customer_id=".$customer_id;
                  $new_data=$objDB->Conn->Execute($new_data_query); */
                $new_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(2, 0, $customer_id));

                if ($new_data->RecordCount() > 0) {
                        while ($Row = $new_data->FetchRow()) {
                                if ($Row['counter'] == 1) {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = "Today " . $Row['counter'] . " new campaign is launched by scanflip merchant near by you.";
                                } else {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = "Today " . $Row['counter'] . " new campaigns were launched by scanflip merchants near by you.";
                                }
                        }
                        $count++;
                }


                // end for new campaign notification
                // start for pending review notification

                $customer_id = $custid;
                /* $pending_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=3 and t.is_read=0 and t.customer_id=".$customer_id;
                  $pending_data=$objDB->Conn->Execute($pending_data_query); */
                $pending_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(3, 0, $customer_id));

                if ($pending_data->RecordCount() > 0) {
                        while ($Row = $pending_data->FetchRow()) {
                                if ($Row['counter'] == 1) {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = "You have " . $Row['counter'] . " pending review for your recent visit.";
                                } else {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = "You have " . $Row['counter'] . " pending reviews for your recent visits.";
                                }
                        }
                        $count++;
                }


                // end for pending review notification
                // start for earned recent visit notification

                $customer_id = $custid;
                /* $earned_redeem_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=4 and t.is_read=0 and t.customer_id=".$customer_id;
                  $earned_redeem_data=$objDB->Conn->Execute($earned_redeem_data_query); */
                $earned_redeem_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(4, 0, $customer_id));

                if ($earned_redeem_data->RecordCount() > 0) {
                        while ($Row = $earned_redeem_data->FetchRow()) {
                                if ($Row['counter'] == 1) {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = "You earned " . $Row['counter'] . " scanflip point from scanflip merchant in last 24 hour";
                                } else {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = "You earned " . $Row['counter'] . " scanflip points from scanflip merchants in last 24 hour.";
                                }
                        }
                        $count++;
                }

                // end for earned recent visit notification
                // start for earned new customer referral notification

                $customer_id = $custid;
                /* $earned_referral_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=5 and t.is_read=0 and t.customer_id=".$customer_id;
                  $earned_referral_data=$objDB->Conn->Execute($earned_referral_data_query); */
                $earned_referral_data = $objDB->Conn->Execute("SELECT t.id 'id',t.counter 'counter',tp.notification_type FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=? and t.is_read=? and t.customer_id=?", array(5, 0, $customer_id));

                if ($earned_referral_data->RecordCount() > 0) {
                        while ($Row = $earned_referral_data->FetchRow()) {
                                if ($Row['counter'] == 1) {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = "You earned " . $Row['counter'] . " scanflip point for new customer referral in last 24 hour.";
                                } else {
                                        $pending_review_notification[$count]['notification_id'] = $Row['id'];
                                        $pending_review_notification[$count]['notification_type'] = $Row['notification_type'];
                                        $pending_review_notification[$count]['notification_message'] = "You earned " . $Row['counter'] . " scanflip points for new customer referral in last 24 hour.";
                                }
                        }
                        $count++;
                }

                // end for earned new customer referral notification
        }

        if (count($pending_review_notification) > 0) {
                $json_array['status'] = "true";

                $json_array['total_records'] = count($pending_review_notification);
                $json_array['notifications'] = $pending_review_notification;


                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {
                $json_array['status'] = "false";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

/**
 * @uses set read notification
 * @param notification_id,customer_id
 * @return string
 */
if (isset($_REQUEST['set_read_notofication'])) {
        $json_array = array();
        /* $update_sql = "Update notification set is_read=1 where id=".$_REQUEST['notification_id']." and customer_id=".$_REQUEST['customer_id'];
          $objDB->Conn->Execute($update_sql); */
        $objDBWrt->Conn->Execute("Update notification set is_read=? where id=? and customer_id=?", array(1, $_REQUEST['notification_id'], $_REQUEST['customer_id']));

        $json_array['status'] = "true";
        $json = json_encode($json_array);
        echo $json;
        exit();
}


/**
 * @uses send notification
 * @param type $apiKey
 * @param type $registrationIdsArray
 * @param type $messageData
 * @return type
 */
function sendNotification($apiKey, $registrationIdsArray, $messageData) {
        $apiKey = GCM_API_KEY;
        $headers = array("Content-Type:" . "application/json", "Authorization:" . "key=" . $apiKey);
        $data = array(
            'data' => $messageData,
            'registration_ids' => $registrationIdsArray
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
}

/*
  $message = "the test message";
  $tickerText = "ticker text message";
  $contentTitle = "content title";
  $contentText = "content body";

  $registrationId = 'DEVICE_ID';
  $apiKey = "YOUR_BROWSER_API_KEY";

  $response = sendNotification(
  $apiKey,
  array($registrationId),
  array('message' => $message, 'tickerText' => $tickerText, 'contentTitle' => $contentTitle, "contentText" => $contentText)
  );

  echo $response;
 */


/**
 * @uses set gcm registration id
 * @param gcm_registration_id,customer_id
 * @return type
 */
if (isset($_REQUEST['set_gcm_registration_id'])) {
        $json_array = array();
        /* $update_sql = "Update customer_user set gcm_registration_id='".$_REQUEST['gcm_registration_id']."' where id=".$_REQUEST['customer_id'];
          $objDB->Conn->Execute($update_sql); */
        $objDBWrt->Conn->Execute("Update customer_user set gcm_registration_id=? where id=?", array($_REQUEST['gcm_registration_id'], $_REQUEST['customer_id']));
        $json_array['status'] = "true";
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses send gcm message
 * @param ticker_text,content_title,content_text,device_id
 * @return type
 */
if (isset($_REQUEST['send_gcm_message'])) {
        $json_array = array();

        /*
          $message = "the test message";
          $tickerText = "ticker text message";
          $contentTitle = "scanflip";
          $contentText = "You earned 15 scanflip points from scanflip merchants in last 24 hour.";
         */

        $message = "";
        $tickerText = $_REQUEST['ticker_text'];
        $contentTitle = $_REQUEST['content_title'];
        $contentText = $_REQUEST['content_text'];

        $registrationId = $_REQUEST['device_id'];
        $apiKey = GCM_API_KEY;

        $response = sendNotification(
                $apiKey, array($registrationId), array('message' => $message, 'tickerText' => $tickerText, 'contentTitle' => $contentTitle, "contentText" => $contentText)
        );

        //echo $response;

        $json_array['status'] = "true";
        $json_array['message'] = $response;
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses update 
 * @param notification_setting,customer_id
 * @return type
 */
if (isset($_REQUEST['btnUpdatenotificationsettings'])) {
        $json_array = array();

        /* $Sql = "Update customer_user SET notification_setting=".$_REQUEST['notification_setting']." where id= ".$_REQUEST['customer_id'];
          //echo $Sql;
          $objDB->Conn->Execute($Sql); */
        $objDBWrt->Conn->Execute("Update customer_user SET notification_setting=? where id= ?", array($_REQUEST['notification_setting'], $_REQUEST['customer_id']));

        $json_array['status'] = "true";
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses google login 
 * @param google_user_id,email
 * @return type
 */
if (isset($_REQUEST['google_login'])) {

        if ($_REQUEST['google_user_id'] != "") {
                $where_clause = $array_values = array();
                $array = $json_array = $where_clause = array();

                $where_clause['google_user_id'] = $_REQUEST['google_user_id'];

                $RS = $objDB->Show("customer_user", $where_clause);
                if ($RS->RecordCount() > 0) {
                        $Row = $RS->FetchRow();
                        //$_SESSION['customer_id'] = $Row['id'];
                        //$_SESSION['customer_info'] = $Row;
                        if (isset($_REQUEST['email'])) {
                                $array_values['google_email_id'] = $_REQUEST['email'];
                        }
                        if (isset($_REQUEST['first_name'])) {
                                $array_values['firstname'] = $_REQUEST['first_name'];
                        }
                        if (isset($_REQUEST['last_name'])) {
                                $array_values['lastname'] = $_REQUEST['last_name'];
                        }
                        if (isset($_REQUEST['dob_year'])) {
                                $array_values['dob_year'] = $_REQUEST['dob_year'];
                        }
                        if (isset($_REQUEST['dob_month'])) {
                                $array_values['dob_month'] = $_REQUEST['dob_month'];
                        }
                        if (isset($_REQUEST['dob_day'])) {
                                $array_values['dob_day'] = $_REQUEST['dob_day'];
                        }

                        if ($Row['profile_pic'] == "") {
                                if (isset($_REQUEST['user_profile_pic'])) {
                                        $array_values['profile_pic'] = $_REQUEST['user_profile_pic'];
                                        //echo $array_values['profile_pic'] ;
                                        //exit();

                                        $image_path_user = UPLOAD_IMG . "/c/usr_pic/";
                                        $image_path_user1 = UPLOAD_IMG . "/c/usr_pass_pic/";
                                        $image_path_user2 = UPLOAD_IMG . "/c/usr_pass_pic/big/";

                                        $name = "usr_" . $_REQUEST['google_user_id'] . ".png";

                                        $fb_img_json = file_get_contents($array_values['profile_pic']);
                                        $fp = fopen($image_path_user . $name, 'w+');
                                        fputs($fp, $fb_img_json);
                                        $fp1 = fopen($image_path_user1 . $name, 'w+');
                                        fputs($fp1, $fb_img_json);
                                        $fp2 = fopen($image_path_user2 . $name, 'w+');
                                        fputs($fp2, $fb_img_json);


                                        $array_values['profile_pic'] = $name;
                                }
                        }

                        if (isset($_REQUEST['device_id'])) {
                                $array_values['device_id'] = $_REQUEST['device_id'];
                        }

                        $array_values['lastvisit_date'] = date("Y-m-d H:i:s");
                        $where_clause['id'] = $Row['id'];
                        $objDB->Update($array_values, "customer_user", $where_clause);

                        $json_array = array();

                        $json_array['status'] = "true";
                        $json_array['customer_id'] = $Row['id'];

                        $where_clause = array();
                        $where_clause['google_user_id'] = $_REQUEST['google_user_id'];
                        $RS = $objDB->Show("customer_user", $where_clause);
                        $Row = $RS->FetchRow();

                        $json_array['customer_info'] = get_field_value($Row);

                        /* $cust_sql = 'select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>0 and  country <>""  and id='.$Row['id'];//.$customer_id;
                          $RS_cust_data=$objDB->Conn->Execute($cust_sql); */
                        $RS_cust_data = $objDB->Conn->Execute('select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>? and  country <>""  and id=?', array(0, $Row['id']));

                        $is_profileset = $RS_cust_data->RecordCount();

                        $json_array['is_profileset'] = $is_profileset;

                        $pos = strpos($json_array['customer_info']['profile_pic'], 'http');
                        if ($pos === false) {
                                if ($json_array['customer_info']['profile_pic'] != "") {
                                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . "/c/usr_pic/" . $json_array['customer_info']['profile_pic'];
                                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                                } else {
                                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . '/c/default_small_user.jpg';
                                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                                }
                        } else {
                                $json_array['customer_info']['profile_pic'] = $json_array['customer_info']['profile_pic'];
                                $json_array['customer_info']['facebook_profile_pic'] = 1;
                        }


                        if ($json_array['customer_info']['card_id'] != "") {
                                $json_array['customer_info']['card_qrcode_url'] = WEB_PATH . "/merchant/demopdf/demo_qrcode_card.php?size=200&card_id=" . $json_array['customer_info']['card_id'];
                        }

                        $json_array['message'] = "Profile Updated";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();

                        //$_SESSION['facebook_usr_login'] = 1;
                } else {
                        //$_SESSION['facebook_usr_login'] = 1;
                        $array_values['google_user_id'] = $_REQUEST['google_user_id'];
                        $array_values['google_access_token'] = $_REQUEST['access_token'];
                        $array_values['emailaddress'] = $_REQUEST['email'];
                        $array_values['google_email_id'] = $_REQUEST['email'];
                        $array_values['firstname'] = $_REQUEST['first_name'];
                        $array_values['lastname'] = $_REQUEST['last_name'];
                        if (isset($_REQUEST['dob_year'])) {
                                $array_values['dob_year'] = $_REQUEST['dob_year'];
                        }
                        if (isset($_REQUEST['dob_month'])) {
                                $array_values['dob_month'] = $_REQUEST['dob_month'];
                        }
                        if (isset($_REQUEST['dob_day'])) {
                                $array_values['dob_day'] = $_REQUEST['dob_day'];
                        }
                        $array_values['registered_date'] = date("Y-m-d H:i:s");
                        $array_values['lastvisit_date'] = date("Y-m-d H:i:s");
                        $array_values['emailnotification'] = 1;
                        $array_values['active'] = 1;
                        $array_values['notification_setting'] = 1;

                        if (isset($_REQUEST['user_profile_pic'])) {
                                $array_values['profile_pic'] = $_REQUEST['user_profile_pic'];

                                $image_path_user = UPLOAD_IMG . "/c/usr_pic/";
                                $image_path_user1 = UPLOAD_IMG . "/c/usr_pass_pic/";
                                $image_path_user2 = UPLOAD_IMG . "/c/usr_pass_pic/big/";

                                $name = "usr_" . $_REQUEST['google_user_id'] . ".png";

                                $fb_img_json = file_get_contents($array_values['profile_pic']);

                                $fp = fopen($image_path_user . $name, 'w+');
                                fputs($fp, $fb_img_json);
                                $fp1 = fopen($image_path_user1 . $name, 'w+');
                                fputs($fp1, $fb_img_json);
                                $fp2 = fopen($image_path_user2 . $name, 'w+');
                                fputs($fp2, $fb_img_json);


                                $array_values['profile_pic'] = $name;
                                //echo $array_values['profile_pic']."=".$name;
                                //exit();
                        }

                        if (isset($_REQUEST['device_id'])) {
                                $array_values['device_id'] = $_REQUEST['device_id'];
                        }

                        $objDB->Insert($array_values, "customer_user");

                        $where_clause['google_user_id'] = $_REQUEST['google_user_id'];
                        $RS = $objDB->Show("customer_user", $where_clause);
                        $Row = $RS->FetchRow();
                        //$_SESSION['customer_id'] = $Row['id'];
                        //$_SESSION['customer_info'] = $Row;
                        $json_array = array();
                        $json_array['status'] = "true";
                        $json_array['message'] = "Successfully Login.";
                        $json_array['customer_id'] = $Row['id'];
                        $json_array['customer_info'] = get_field_value($Row);

                        /* $cust_sql = 'select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>0 and  country <>""  and id='.$Row['id'];//.$customer_id;
                          $RS_cust_data=$objDB->Conn->Execute($cust_sql); */
                        $RS_cust_data = $objDB->Conn->Execute('select * from customer_user where postalcode <>"" and  gender<>"" and dob_year <>? and  country <>""  and id=?', array(0, $Row['id']));

                        $is_profileset = $RS_cust_data->RecordCount();

                        $json_array['is_profileset'] = $is_profileset;

                        $pos = strpos($json_array['customer_info']['profile_pic'], 'http');
                        if ($pos === false) {
                                if ($json_array['customer_info']['profile_pic'] != "") {
                                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . "/c/usr_pic/" . $json_array['customer_info']['profile_pic'];
                                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                                } else {
                                        $json_array['customer_info']['profile_pic'] = ASSETS_IMG . '/c/default_small_user.jpg';
                                        $json_array['customer_info']['facebook_profile_pic'] = 0;
                                }
                        } else {
                                $json_array['customer_info']['profile_pic'] = $json_array['customer_info']['profile_pic'];
                                $json_array['customer_info']['facebook_profile_pic'] = 1;
                        }
                        if ($json_array['customer_info']['card_id'] != "") {
                                $json_array['customer_info']['card_qrcode_url'] = WEB_PATH . "/merchant/demopdf/demo_qrcode_card.php?size=200&card_id=" . $json_array['customer_info']['card_id'];
                        }
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        } else {
                $json_array = array();
                $json_array['status'] = "false";
                $json_array['message'] = "Please enter valid google user id";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}


/**
 * @uses create unique code 
 * @param type $customer_id
 * @return string
 */
function create_unique_code($customer_id) {
        $code_length = 16;
        //echo $alfa = "1AB2CD3EF4G5HI6JK7LM8N9OP10QRSTU".$campaign_id."VWXYZ";
        $alfa = "12345678910ABCDEFGHIJKLMNOPQRSTUVWXYZ" . $customer_id . "abcdefghijklmnopqrstuvwxyz";
        $code = "";
        for ($i = 0; $i < $code_length; $i ++) {
                $code .= $alfa[rand(0, strlen($alfa) - 1)];
        }
        return $code;
}


/**
 * @uses forgot password sent email
 * @param type email
 * @return string
 */
if (isset($_REQUEST['btnForgotPasswordSentEmail'])) {
        $json_array = array();

        $array_where = array();
        $array_where['emailaddress'] = $_REQUEST['email'];

        $RS = $objDB->Show("customer_user", $array_where);

        if ($RS->RecordCount() <= 0) {
                $json_array['status'] = "false";
                $json_array['message'] = "There was a problem with your request.We're sorry.We weren't able to identify you given the information provided.";
                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {
                $token = create_unique_code($RS->fields['id']);

                $array_values['token'] = $token;
                $array_values['token_created_at'] = date('Y-m-d H:i:s');
                $array_where['emailaddress'] = $_REQUEST['email'];
                $objDB->Update($array_values, "customer_user", $array_where);

                $mail = new PHPMailer();
                $body = "<p>Hi " . $RS->fields['firstname'] . ",<br/><br/>";
                $body .= "Changing your password is simple. Please use the link below in 24 hours<br/><br/>";
                $body .= "<a href='" . WEB_PATH . "/forgot_password.php?token=" . $token . "'>" . WEB_PATH . "/forgot_password.php?token=" . $token . "</a></p>";

                $body .= "<br/><br/><p>Thank You,</p>";
                $body .= "<p>ScanFlip Support</p>";

                $mail->AddReplyTo('no-reply@scanflip.com', 'ScanFlip Support');
                $mail->AddAddress($_REQUEST['email']);
                $mail->From = "no-reply@scanflip.com";
                $mail->FromName = "ScanFlip Support";
                $mail->Subject = "Reset Your ScanFlip Password";
                $mail->MsgHTML($body);
                //echo $body;
                $mail->Send();

                //echo "<span style='color:green'>If the e-mail address you entered is associated with a customer account in our records, you will receive an e-mail from us with instructions for resetting your password.If you don't receive this e-mail, please check your junk mail folder or visit our Help pages to contact Customer Services for further assistance.</span>";
                //echo "success";
                $json_array['status'] = "true";
                $json_array['message'] = "Check your e-mail.If the e-mail address you entered is associated with a customer account in our records, you will receive an e-mail from us with instructions for resetting your password.If you don't receive this e-mail, please check your junk mail folder or visit our Help pages to contact Customer Services for further assistance.";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

/**
 * @uses update forgot password
 * @param type email
 * @return string
 */
if (isset($_REQUEST['btnUpdateForgotPassword'])) {
        $json_array = array();

        $array_where['token'] = $_REQUEST['token'];
        $RS = $objDB->Show("customer_user", $array_where);

        if ($RS->RecordCount() > 0) {
                $cust_id = $RS->fields['id'];

                $array = $json_array = $where_clause = array();
                $PasswordLib = new \PasswordLib\PasswordLib;
                //$hash = $PasswordLib->createPasswordHash($password);
                //$array['password'] = $PasswordLib->createPasswordHash($_REQUEST['password']);
                $array['password'] = $PasswordLib->createPasswordHash($_REQUEST['new_password']);

                $where_clause['id'] = $cust_id;

                $objDB->Update($array, "customer_user", $where_clause);

                $json_array['status'] = "true";
                $json_array['message'] = "Password has been changed successfully";
                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {
                $json_array['status'] = "false";
                $json_array['message'] = "Sorry, your token is expired";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

/**
 * @uses update forgot password
 * @param type email
 * @return string
 */
if (isset($_REQUEST['btnUpdatePassword'])) {
        $array = $json_array = array();
        // 369

        /*
          //$ is a comment this code
          if($_REQUEST['customer_id'] == ""){
          $array['id'] = $_SESSION['customer_id'];

          }else{
          $array['id'] = get_cutomer_session_id($_REQUEST['customer_id']);
          } */
        $array['id'] = $_REQUEST['customer_id'];
        if ($array['id'] == "") {
                $json_array['status'] = "false";
                $json_array['message'] = "Invalid customer id";
                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {

                if ($_REQUEST['old_password'] == "") {
                        $json_array['message'] = "Please enter current password";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
                if ($_REQUEST['new_password'] == "") {
                        $json_array['message'] = "Please enter new password";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
                if ($_REQUEST['con_new_password'] == "") {
                        $json_array['message'] = "Please enter confirm new password";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
                if ($_REQUEST['new_password'] != $_REQUEST['con_new_password']) {
                        $json_array['message'] = "New password does not match";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }


                //	$array['password'] = md5($_REQUEST['old_password']);
                $RS = $objDB->Show("customer_user", $array);
                $PasswordLib2 = new \PasswordLib\PasswordLib;
                $result = $PasswordLib2->verifyPasswordHash($_REQUEST['old_password'], $RS->fields['password']);
                if (!$result) {
                        $json_array['message'] = "Please enter Valid current Password";
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
                $array = $json_array = $where_clause = array();
                $PasswordLib = new \PasswordLib\PasswordLib;
//$hash = $PasswordLib->createPasswordHash($password);
                //$array['password'] = $PasswordLib->createPasswordHash($_REQUEST['password']);
                $array['password'] = $PasswordLib->createPasswordHash($_REQUEST['new_password']);

                /*
                  //$ is a comment this code
                  if($_REQUEST['customer_id'] == ""){
                  $where_clause['id'] = $_SESSION['customer_id'];
                  }else{
                  $where_clause['id'] = $_REQUEST['customer_id'];
                  } */
                $where_clause['id'] = $_REQUEST['customer_id'];

                if ($where_clause['id'] == "") {
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
        // 369
}


/**
 * @uses get participating locations
 * @param campaign_id,location_id,mycurrent_lati,mycurrent_long
 * @return string
 */
if (isset($_REQUEST['get_participating_locations'])) 
{
        $campaign_id = $_REQUEST['campaign_id'];
        $location_id = $_REQUEST['location_id'];

        $array_where = array();
        $array_where['id'] = $location_id;
        $RS = $objDB->Show("locations", $array_where);
        $lat = $RS->fields['latitude'];
        $lng = $RS->fields['longitude'];

        $mycurrent_lati = $_REQUEST['mycurrent_lati'];
        $mycurrent_long = $_REQUEST['mycurrent_long'];

        $json_array = array();

        if (isset($_REQUEST['customer_id'])) {
                $customer_id = $_REQUEST['customer_id'];
                /* $Sql = "Select l.id,l.location_name,l.address,l.city,l.state,l.country,l.latitude,l.longitude,cl.offers_left from campaign_location cl , locations l where campaign_id = ". $campaign_id ." and l.id = cl.location_id and cl.active=1" ;
                  $Rs = $objDB->execute_query($Sql); */
                $Rs =  $objDB->Conn->Execute("Select l.id,l.location_name,l.address,l.city,l.state,l.country,l.latitude,l.longitude,cl.offers_left from campaign_location cl , locations l where campaign_id =? and l.id = cl.location_id and cl.active=?", array($campaign_id, 1));

                $records = array();
                $count = 0;

                if ($Rs->RecordCount() > 0) {
                        while ($Row = $Rs->FetchRow()) {
                                //echo "loc id=".$Row['id'];

                                $array_where_camp1 = array();
                                $array_where_camp1['id'] = $campaign_id;
                                $RS_camp1 = $objDB->Show("campaigns", $array_where_camp1);

                                $array_where_camp2 = array();
                                $array_where_camp2['campaign_id'] = $campaign_id;
                                $array_where_camp2['customer_id'] = $customer_id;
                                $array_where_camp2['location_id'] = $Row['id'];
                                $RS_cust_camp = $objDB->Show("customer_campaigns", $array_where_camp2);
                                //echo $RS_cust_camp->RecordCount()."-";
                                $reserved = $RS_cust_camp->RecordCount();

                                $array_where_camp = array();
                                $array_where_camp['campaign_id'] = $campaign_id;
                                $array_where_camp['customer_id'] = $customer_id;
                                $array_where_camp['referred_customer_id'] = 0;
                                $array_where_camp['location_id'] = $Row['id'];
                                $RS_camp = $objDB->Show("reward_user", $array_where_camp);
                                //echo $RS_camp->RecordCount()."-";
                                //echo "bb".$Row->number_of_use."-";
                                $redeemed = $RS_camp->RecordCount();

                                /*
                                  echo "<hr/>";
                                  echo "Campaign Id = ".$campaign_id."<br/>";
                                  echo "Location Id = ".$Row['id']."<br/>";
                                  echo "Number Of Use = ".$RS_camp1->fields['number_of_use']."<br/>";
                                  echo "Offer Left = ".$Row['offers_left']."<br/>";
                                  echo "Reserved = ".$reserved."<br/>";
                                  echo "Redeemed = ".$redeemed."<br/>";
                                  echo "<hr/>";
                                 */

                                if ($RS_cust_camp->RecordCount() > 0 && $RS_camp->RecordCount() > 0 && $RS_camp1->fields['number_of_use'] == 1) {
                                        //echo "1 ".$campaign_id." ".$Row['id'];
                                } elseif ($RS_cust_camp->RecordCount() > 0 && $RS_camp->RecordCount() > 0 && ($RS_camp1->fields['number_of_use'] == 2 || $RS_camp1->fields['number_of_use'] == 3) && $Row['offers_left'] == 0) {
                                        //echo "2 ".$campaign_id." ".$Row['id'];
                                } elseif ($RS_cust_camp->RecordCount() == 0 && $RS_camp->RecordCount() == 0 && $Row['offers_left'] == 0) {
                                        //echo "3 ".$campaign_id." ".$Row['id'];
                                } else {
                                        $to_lati1 = $Row['latitude'];
                                        $to_long1 = $Row['longitude'];

                                        $deal_distance = $objJSON->distance($mycurrent_lati, $mycurrent_long, $to_lati1, $to_long1, "M");
                                        $deal_distance_from_location = $objJSON->distance($lat, $lng, $to_lati1, $to_long1, "M");

                                        if ($deal_distance_from_location <= 20) {
                                                $records[$count] = get_field_value($Row);
                                                $records[$count]['distance'] = $deal_distance;

                                                $count++;
                                        }
                                }
                        }
                        if ($count > 0) {
                                $json_array['status'] = "true";
                                $json_array["total_records"] = $count;
                                $json_array["records"] = $records;
                                $json = json_encode($json_array);
                                echo $json;
                                exit();
                        } else {
                                $json_array['status'] = "false";
                                $json_array["total_records"] = $count;
                                $json = json_encode($json_array);
                                echo $json;
                                exit();
                        }
                } else {
                        $json_array['status'] = "false";
                        $json_array["total_records"] = $count;
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        } else {
                /* $Sql = "Select l.id,l.location_name,l.address,l.city,l.state,l.country,l.latitude,l.longitude from campaign_location cl , locations l where campaign_id = ". $campaign_id ." and l.id = cl.location_id and cl.active=1 and cl.offers_left>0";
                  $Rs = $objDB->execute_query($Sql); */
                $Rs =  $objDB->Conn->Execute("Select l.id,l.location_name,l.address,l.city,l.state,l.country,l.latitude,l.longitude from campaign_location cl , locations l where campaign_id = ? and l.id = cl.location_id and cl.active=? and cl.offers_left>?", array($campaign_id, 1, 0));
                $records = array();
                $count = 0;

                if ($Rs->RecordCount() > 0) {
                        while ($Row = $Rs->FetchRow()) {
                                $to_lati1 = $Row['latitude'];
                                $to_long1 = $Row['longitude'];

                                $deal_distance = $objJSON->distance($mycurrent_lati, $mycurrent_long, $to_lati1, $to_long1, "M");
                                $deal_distance_from_location = $objJSON->distance($lat, $lng, $to_lati1, $to_long1, "M");

                                if ($deal_distance_from_location <= 20) {
                                        $records[$count] = get_field_value($Row);
                                        $records[$count]['distance'] = $deal_distance;
                                        $count++;
                                }
                        }
                        $json_array['status'] = "true";
                        $json_array["total_records"] = $count;
                        $json_array["records"] = $records;
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                } else {
                        $json_array['status'] = "false";
                        $json_array["total_records"] = $Rs->RecordCount();
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        }
}

/* * ***** check for mobile webservice ******************* */

/**
 * @uses get serach deal location
 * @param customer_id
 * @return string
 */
if (isset($_REQUEST['btnGetSearchDealLocations'])) {
        if (isset($_REQUEST['customer_id'])) {
                if (isset($_REQUEST['currentLocationName']) && $_REQUEST['currentLocationName'] != "") {
                        /* $sql_update = "update customer_user set current_location = '".$_REQUEST['currentLocationName']."' where id=".$_REQUEST['customer_id'];
                          $RS_cl_update = $objDB->Conn->Execute($sql_update); */
                        $RS_cl_update = $objDBWrt->Conn->Execute("update customer_user set current_location =? where id=?", array(urldecode($_REQUEST['currentLocationName']), $_REQUEST['customer_id']));
                }
        }
        $json_array = array();
        $records = array();
        $records_all = array();
        $json_array1 = array();
        $category_id = $_REQUEST['category_id'];
        //$dismile=$_REQUEST['dismile'];
        if (isset($_REQUEST['dismile'])) {
                $dismile = $_REQUEST['dismile'];
        } else {
                $dismile = 20;
        }
        if (isset($_REQUEST['is_current_location']) && $_REQUEST['is_current_location'] == 0) {
                $dismile = 20;
                $json_array['miles_data'] = 20;
        }
        $miles_array[0][2] = 0;
        $miles_array[0][5] = 0;
        $miles_array[0][10] = 0;
        $miles_array[0][15] = 0;
        $miles_array[0][20] = 0;
        $miles_indexes = array(2, 5, 10, 15, 20, 50);
        $current_index = array_search($dismile, $miles_indexes);
        $date_f = date("Y-m-d H:i:s");
        $mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];

        $user_mlatitude = $_REQUEST['user_mlatitude'];
        $user_mlongitude = $_REQUEST['user_mlongitude'];


        if (isset($_REQUEST['customer_id'])) {
                $customer_id = $_REQUEST['customer_id'];
        } else {
                $customer_id = 0;
        }
        $calculatedmiles = 20 * 20;
        //echo "call search_deal_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0)";
        $RS_limit_data = $objDB->Conn->Execute("call search_deal_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0,'" . CURR_TIMEZONE . "')");
        //$objDB->Conn->Close( );
        //$objDB = new DB();
//	$RS_limit_data=$objDB->Conn->Execute($limit_data);
        //echo $RS_limit_data->RecordCount();
        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                //$json_array['total_records'] = 17;//$RS_limit_data->RecordCount();
                //$json_array['all_records'] = 17;//$RS_limit_data->RecordCount();
                $json_array['miles_data'] = 5;
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['sp'] = "call search_deal_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0)";
                $json_array['data'] = "https://www.scanflip.com/includes/customer/process_mobile.php?btnGetSearchDealLocations=yes&category_id=" . $_REQUEST['category_id'] .
                        "&mlatitude=" . $_REQUEST['mlatitude'] .
                        "&mlongitude=" . $_REQUEST['mlongitude'] .
                        "&user_mlatitude=" . $_REQUEST['user_mlatitude'] .
                        "&user_mlongitude=" . $_REQUEST['user_mlongitude'] .
                        "&dismile=" . $_REQUEST['dismile'] .
                        "&is_current_location=" . $_REQUEST['is_current_location'] .
                        "&is_current_location=" . $_REQUEST['is_current_location'];

                //$json_array['total_records'] = $RS_limit_data->RecordCount();
                //	$json_array['all_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                $target_distance = $dismile;
                $isrecord_found = false;
                //$current_index = 0;

                while ($Row = $RS_limit_data->FetchRow()) {
                        $deal_distance = intval($objJSON->distance($mlatitude, $mlongitude, $Row['latitude'], $Row['longitude'], "M"));

                        //	echo $Row['distance']."-----".$target_distance."<br/>";
                        if ($deal_distance > $target_distance) {

                                if (!$isrecord_found) {

                                        if ($target_distance != count($miles_indexes)) {
                                                //echo "inn if";
                                                $current_index = array_search($target_distance, $miles_indexes);
                                                $target_distance = $miles_indexes[$current_index + 1];
                                                //	echo "++++".$target_distance."++++";
                                        } else {
                                                
                                        }
                                }
                        }
                        //echo "<br/>====".$Row['distance']."===".$target_distance."<br/>";
                        if ($deal_distance <= $target_distance) {
                                $isrecord_found = true;
                                $json_array['miles_data'] = $target_distance;
                                //echo "inn ifffff";
                                $isrecord_found = true;
                                $temp_merchant_arr = array();
                                //	echo "<br/>".count($arr_main_merchant_arr[$Row['merchant']])."===<br/>";
                                //	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                                
                                // remove undefined index error
                                if(isset($arr_main_merchant_arr[$Row['merchant']]))
                                {
								}
								else
								{
									$arr_main_merchant_arr[$Row['merchant']]="";
								}
								// remove undefined index error
								
                                if ($arr_main_merchant_arr[$Row['merchant']] != 0) {
                                        $temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                                }
                                if (!in_array($Row['merchant'], $all_mercahnts)) {
                                        array_push($all_mercahnts, $Row['merchant']);
                                }
                                $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                                //print_r($arr_main_location_arr);

                                array_push($temp_merchant_arr, get_field_value($Row));
                                //print_r($temp_merchant_arr);
                                $arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
                                //print_r($arr_main_merchant_arr[$Row['merchant']]);
                                //exit();
                                /* if (! array_key_exists($Row['location_id'], $arr_main_location_arr)) {
                                  $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                                  }
                                  else{
                                  if(! in_array($Row['location_id']."-".$Row['location_id'],$arr_main_location_arr))
                                  {
                                  $arr_main_location_arr[$Row['location_id']] = $Row['location_id']."-".$Row['location_id'];
                                  }
                                  } */

                                $records[$count] = get_field_value($Row);
                                //$records[$count]["rating"] = $objJSON->get_location_rating($Row["locid"]);

                                $count++;
                        }
                }
                //echo "<pre>";
                //print_r($arr_main_merchant_arr);
                //echo "</pre>";
                $json = json_encode($arr_main_merchant_arr);
                $id = 0;
                $final_array = array();
                $max_counter = 0;
                //	print_r($all_mercahnts);
                for ($k = 0; $k < count($all_mercahnts); $k++) {
                        $max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
                        if ($max_counter <= $max_counter1) {
                                $max_counter = $max_counter1;
                        }
                }
                $final_array = array();
                //echo $max_counter."==";
                for ($j = 0; $j < $max_counter; $j++) {
                        for ($y = 0; $y < count($all_mercahnts); $y++) {
							
							 // remove undefined index error
							if(isset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]))
							{
							}
							else
							{
								$arr_main_merchant_arr[$all_mercahnts[$y]][$j]="";
							}
							// remove undefined index error

                                if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "") {

                                        // start distance
                                        $location_latitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["latitude"];
                                        $location_longitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["longitude"];
                                        $deal_distance = $objJSON->distance($user_mlatitude, $user_mlongitude, $location_latitude, $location_longitude, "M");
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["distance"] = $deal_distance;
                                        // end distance
                                        // start location hour code
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"]."</br>";
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"]."</br>";

                                        $location_id = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];

                                        $time_zone = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone_name"];
                                        date_default_timezone_set($time_zone);
                                        $current_day = date('D');
                                        $current_time = date('g:i A');

                                        $location_time = "";
                                        $start_time = "";
                                        $end_time = "";
                                        $status_time = "";

                                        $start_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"];
                                        $end_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];
                                        $location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"] . " - ";
                                        $location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];


                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 1) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Open";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 0) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Close";
                                        }
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"] = $location_time;
                                        // end location hour code
                                        // start business name
                                        // end business name
                                        // start merchant business tags
                                        // end merchant business tags
                                        // start pricerange

                                        $val = "";
                                        $val_text = "";

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 1) {
                                                $val_text = "Inexpensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 2) {
                                                $val_text = "Moderate";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 3) {
                                                $val_text = "Expensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 4) {
                                                $val_text = "Very Expensive";
                                        } else {
                                                $val_text = "";
                                        }

                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"] = $val_text;

                                        // end pricerange
                                        // change for rating
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["avarage_rating"];

                                        $rating_number = 0;

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] < 0 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] < 1) {

                                                // echo "in .5";
                                                $rating_number = 0.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 1 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 1.74) {
                                                // echo "in 1";
                                                $rating_number = 1;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 1.75 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 2.24) {
                                                // echo "2";
                                                $rating_number = 2;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 2.25 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 2.74) {
                                                //echo "2,5";
                                                $rating_number = 2.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 2.75 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 3.24) {
                                                //echo "3";
                                                $rating_number = 3;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 3.25 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 3.74) {
                                                //  echo "3.5";
                                                $rating_number = 3.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 3.75 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 4.24) {
                                                // echo "4";
                                                $rating_number = 4;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 4.25 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 4.74) {
                                                //  echo "4.5";
                                                $rating_number = 4.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 4.75) {
                                                // echo "5";
                                                $rating_number = 5;
                                        }
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_main_ratings_number"] = $rating_number;

                                        // change for rating
                                        // start campaign array

                                        $count = 0;
                                        $campaignlist = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_id"];
                                        $cmapignlist_array = explode(",", $campaignlist);
                                        $businesslogo_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaign_businesslogo"]);
                                        $businesstitle_array = explode("##sep##", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_title"]);
                                        $campaigncategory_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_categories"]);
                                        $campaignwalkin_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_walkin"]);
                                        $campaignofferleft_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_offersleft"]);
                                        $campaignisnew_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_new_campaigns"]);
                                        $campaigndelavalue_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_dealvalue"]);
                                        $campaigndiscount_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_discount"]);
                                        $campaignsaving_discount = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_saving"]);
                                        $campaignexpiredate_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expiredate"]);
                                        $campaignexpirationdate_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expirationdate"]);
                                        $locationwise_campaign_array = array();
                                        for ($cnti = 0; $cnti < count($cmapignlist_array); $cnti++) {
                                                $campaign_array = array();
                                                $campaign_array['id'] = $cmapignlist_array[$cnti];
                                                $campaign_array['business_logo'] = $businesslogo_array[$cnti];
                                                $campaign_array['title'] = $businesstitle_array[$cnti];
                                                $campaign_array['category_id'] = $campaigncategory_array[$cnti];
                                                $campaign_array['is_walkin'] = $campaignwalkin_array[$cnti];
                                                $campaign_array['offers_left'] = $campaignofferleft_array[$cnti];
                                                $campaign_array['is_new'] = $campaignisnew_array[$cnti];
                                                $campaign_array['deal_value'] = $campaigndelavalue_array[$cnti];
                                                $campaign_array['discount'] = $campaigndiscount_array[$cnti];
                                                $campaign_array['saving'] = $campaignsaving_discount[$cnti];
                                                $campaign_array['expire_date'] = $campaignexpiredate_array[$cnti];
                                                $campaign_array['expiration_date'] = $campaignexpirationdate_array[$cnti];
                                                array_push($locationwise_campaign_array, $campaign_array);
                                        }

                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"] = $locationwise_campaign_array;


                                        // end campaign array
                                        // start location category
                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'] != "") {
                                                $count = 0;
                                                $catgory_array = array();
                                                $innercategory_array['id'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'];
                                                $innercategory_array['cat_name'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["locationcategories"];
                                                array_push($catgory_array, $innercategory_array);
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = $catgory_array;
                                        } else {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = array();
                                        }

                                        // end location category
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["locationcategories"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["cid"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_id"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaign_businesslogo"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_title"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_categories"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_walkin"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_offersleft"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_new_campaigns"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_dealvalue"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_discount"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_saving"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expiredate"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expirationdate"]);

                                        array_push($final_array, $arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
                                }
                        }
                }

                $json_array['status'] = "true";
                //echo "<pre>final array";
                //print_r($final_array);
                //echo "</pre>";
                //$json = json_encode($final_array);
                //echo $json;
                //exit();
                $json_array["records"] = $final_array;
                /*
                  while($Row = $RS_limit_data->FetchRow())
                  {
                  $records_all[$count] = get_field_value($Row);
                  $count++;
                  } */
                //	$json_array["marker_records"]= $records_all;
                //$json_array['total_records'] =  count($json_array['records']);
                $json_array['all_records'] = count($json_array['records']);
                $json_array['total_records'] = count($json_array['records']);
                $json_array['marker_total_records'] = count($json_array['records']);
        } else {
                $json_array['status'] = "false";
                $json_array['error_msg'] = $client_msg["search_deal"]["Msg_no_deal_in_20_miles"];
                $json_array['all_records'] = 0;
                $json_array["records"] = "";
                $json_array['status'] = "false";
                $json_array['error_msg'] = "";
                $json_array['total_records'] = 0;
                $json_array['is_profileset'] = 1;
                $json_array["marker_records"] = "";
                $json_array['marker_total_records'] = 0;

                $json = json_encode($json_array);
                echo $json;
                exit;
        }


        /* $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;

          // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

          $limit_data = "SELECT l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,".$get_dat." round((((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515 ),2) as distance , count(*) total_deals,mu.business,mu.merchant_icon,l.timezone FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." group by cl.location_id ORDER BY distance,c.expiration_date";


          $RS_limit_data=$objDB->Conn->Execute($limit_data);
          if($RS_limit_data->RecordCount()>0)
          {

          }
          else
          {
          $json_array['all_records'] = 0;
          $json_array["records"]="";
          $json_array['status'] = "false";
          $json_array['error_msg'] = "";
          $json_array['total_records'] = 0;
          $json_array['is_profileset'] = 1;
          $json_array["marker_records"]= "";
          $json_array['marker_total_records'] = 0;
          $json = json_encode($json_array);
          echo $json;
          exit();
          } */
        $json = json_encode($json_array);
        file_put_contents(SERVER_PATH . "/cached_json/mydata.json", $json);
        echo $json;
        exit();
}

/* * ************** store procedure for mydeals ******************* */
/**
 * @uses get subscribe merchant locations
 * @param customer_id
 * @return string
 */
if (isset($_REQUEST['btnGetSubscribedMerchantLocations'])) {
        if (isset($_REQUEST['customer_id'])) {
                if (isset($_REQUEST['currentLocationName']) && $_REQUEST['currentLocationName'] != "") {
                        /* $sql_update = "update customer_user set current_location = '".$_REQUEST['currentLocationName']."' where id=".$_REQUEST['customer_id'];
                          $RS_cl_update = $objDB->Conn->Execute($sql_update); */
                        $RS_cl_update = $objDBWrt->Conn->Execute("update customer_user set current_location =? where id=?", array(urldecode($_REQUEST['currentLocationName']), $_REQUEST['customer_id']));
                }
        }

        $json_array = array();
        $records = array();
        $records_all = array();
        $json_array1 = array();
        $category_id = $_REQUEST['category_id'];
        //$dismile=$_REQUEST['dismile'];
        //$dismile= 50;

        if (isset($_REQUEST['dismile'])) {
                $dismile = $_REQUEST['dismile'];
        } else {
                $dismile = 20;
        }
        if (isset($_REQUEST['is_current_location']) && $_REQUEST['is_current_location'] == 0) {
                //	$dismile=20;
                //	$json_array['miles_data'] = 20;
        }
        $miles_array[0][2] = 0;
        $miles_array[0][5] = 0;
        $miles_array[0][10] = 0;
        $miles_array[0][15] = 0;
        $miles_array[0][20] = 0;
        $miles_indexes = array(2, 5, 10, 15, 20, 50);
        $current_index = array_search($dismile, $miles_indexes);
        $date_f = date("Y-m-d H:i:s");
        $mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];

        $user_mlatitude = $_REQUEST['user_mlatitude'];
        $user_mlongitude = $_REQUEST['user_mlongitude'];


        if (isset($_REQUEST['customer_id'])) {
                $customer_id = $_REQUEST['customer_id'];
        } else {
                $customer_id = 0;
        }
        $calculatedmiles = 20 * 20;

        $id = $_REQUEST['customer_id'];
        //$miles=$_REQUEST['dismile'];

        $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;
        //$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;

        $date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0";

        $cust_where = "";

        $cat_str = "";
        /* if($_REQUEST['customer_id']!="")
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

          // 13-02-2013 also include checkin campaign
          $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 or c.is_walkin=1) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=".$customer_id." and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =".$customer_id." and location_id=cl.location_id) ) )";
          // 13-02-2013
          }
          else
          {
          $cust_where = " and c.level=1 ";
          // 13-02-2013 also include checkin campaign
          $cust_where = " and (c.level=1 or c.is_walkin=1) ";
          // 13-02-2013
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
          } */




        $limit_data = "SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
                 round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and c.is_walkin <> 1 and
 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=$id) or c.level =1 ) and 
l.id in (SELECT DISTINCT ss.location_id from subscribed_stores ss where ss.customer_id=$id and ss.subscribed_status=1)
and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id))  or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status =0)  ))
  " . $cat_str . "  " . $date_wh . " and " . $Where . " group by cl.location_id ORDER BY distance,c.expiration_date";

        //
        //and (
        //                           SELECT cl.location_id FROM categories CAT,campaigns c,campaign_location cl WHERE CAT.id=c.category_id and CAT.active=1 and cl.campaign_id = c.id
        //                            ".$cust_where."   and cl.location_id in(select id from locations l where l.active =1 ".$date_wh.")) and ".$Where." ORDER BY distance ";
        $calculatedmiles = 20 * 20;
        //echo "call mymerchant_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0)";
        $RS_limit_data = $objDB->Conn->Execute("call mymerchant_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0,'" . CURR_TIMEZONE . "')");
        //$objDB->Conn->Close( );
        //$objDB = new DB();
//	$RS_limit_data=$objDB->Conn->Execute($limit_data);

        if ($RS_limit_data->RecordCount() > 0) {
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                //$json_array['total_records'] = 17;//$RS_limit_data->RecordCount();
                //$json_array['all_records'] = 17;//$RS_limit_data->RecordCount();
                $json_array['miles_data'] = 5;
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['sp'] = "call mymerchant_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0)";
                $json_array['data'] = "https://www.scanflip.com/includes/customer/process_mobile.php?btnGetSubscribedMerchantLocations=yes&category_id=" . $_REQUEST['category_id'] .
                        "&mlatitude=" . $_REQUEST['mlatitude'] .
                        "&mlongitude=" . $_REQUEST['mlongitude'] .
                        "&user_mlatitude=" . $_REQUEST['user_mlatitude'] .
                        "&user_mlongitude=" . $_REQUEST['user_mlongitude'] .
                        "&dismile=" . $_REQUEST['dismile'] .
                        "&is_current_location=" . $_REQUEST['is_current_location'] .
                        "&is_current_location=" . $_REQUEST['is_current_location'];

                //$json_array['total_records'] = $RS_limit_data->RecordCount();
                //	$json_array['all_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                $target_distance = $dismile;
                $isrecord_found = false;
                //$current_index = 0;

                while ($Row = $RS_limit_data->FetchRow()) {
                        $deal_distance = intval($objJSON->distance($mlatitude, $mlongitude, $Row['latitude'], $Row['longitude'], "M"));

                        //	echo $Row['distance']."-----".$target_distance."<br/>";
                        if ($deal_distance > $target_distance) {

                                if (!$isrecord_found) {

                                        if ($target_distance != count($miles_indexes)) {
                                                //echo "inn if";
                                                $current_index = array_search($target_distance, $miles_indexes);
                                                $target_distance = $miles_indexes[$current_index + 1];
                                                //	echo "++++".$target_distance."++++";
                                        } else {
                                                
                                        }
                                }
                        }
                        //echo "<br/>====".$Row['distance']."===".$target_distance."<br/>";
                        if ($deal_distance <= $target_distance) {
                                $isrecord_found = true;
                                $json_array['miles_data'] = $target_distance;
                                //echo "inn ifffff";
                                $isrecord_found = true;
                                $temp_merchant_arr = array();
                                //	echo "<br/>".count($arr_main_merchant_arr[$Row['merchant']])."===<br/>";
                                //	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                                
                                 // remove undefined index error
								if(isset($arr_main_merchant_arr[$Row['merchant']]))
								{
								}
								else
								{
									$arr_main_merchant_arr[$Row['merchant']]="";
								}
								// remove undefined index error

                                if ($arr_main_merchant_arr[$Row['merchant']] != 0) {
                                        $temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                                }
                                if (!in_array($Row['merchant'], $all_mercahnts)) {
                                        array_push($all_mercahnts, $Row['merchant']);
                                }
                                $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                                //print_r($arr_main_location_arr);

                                array_push($temp_merchant_arr, get_field_value($Row));
                                //print_r($temp_merchant_arr);
                                $arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
                                //print_r($arr_main_merchant_arr[$Row['merchant']]);
                                //exit();
                                /* if (! array_key_exists($Row['location_id'], $arr_main_location_arr)) {
                                  $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                                  }
                                  else{
                                  if(! in_array($Row['location_id']."-".$Row['location_id'],$arr_main_location_arr))
                                  {
                                  $arr_main_location_arr[$Row['location_id']] = $Row['location_id']."-".$Row['location_id'];
                                  }
                                  } */

                                $records[$count] = get_field_value($Row);
                                //$records[$count]["rating"] = $objJSON->get_location_rating($Row["locid"]);

                                $count++;
                        }
                }
                //echo "<pre>";
                //print_r($arr_main_merchant_arr);
                //echo "</pre>";
                $json = json_encode($arr_main_merchant_arr);
                $id = 0;
                $final_array = array();
                $max_counter = 0;
                //	print_r($all_mercahnts);
                for ($k = 0; $k < count($all_mercahnts); $k++) {
                        $max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
                        if ($max_counter <= $max_counter1) {
                                $max_counter = $max_counter1;
                        }
                }
                $final_array = array();
                //echo $max_counter."==";
                for ($j = 0; $j < $max_counter; $j++) {
                        for ($y = 0; $y < count($all_mercahnts); $y++) {
							
							// remove undefined index error
							if(isset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]))
							{
							}
							else
							{
								$arr_main_merchant_arr[$all_mercahnts[$y]][$j]="";
							}
							// remove undefined index error

                                if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "") {

                                        // start distance
                                        $location_latitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["latitude"];
                                        $location_longitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["longitude"];
                                        $deal_distance = $objJSON->distance($user_mlatitude, $user_mlongitude, $location_latitude, $location_longitude, "M");
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["distance"] = $deal_distance;
                                        // end distance
                                        // start location hour code
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"]."</br>";
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"]."</br>";

                                        $location_id = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];

                                        $time_zone = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone_name"];
                                        date_default_timezone_set($time_zone);
                                        $current_day = date('D');
                                        $current_time = date('g:i A');

                                        $location_time = "";
                                        $start_time = "";
                                        $end_time = "";
                                        $status_time = "";

                                        $start_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"];
                                        $end_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];
                                        $location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"] . " - ";
                                        $location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];


                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 1) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Open";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 0) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Close";
                                        }
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"] = $location_time;
                                        // end location hour code
                                        // start business name
                                        // end business name
                                        // start merchant business tags
                                        // end merchant business tags
                                        // start pricerange

                                        $val = "";
                                        $val_text = "";

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 1) {
                                                $val_text = "Inexpensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 2) {
                                                $val_text = "Moderate";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 3) {
                                                $val_text = "Expensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 4) {
                                                $val_text = "Very Expensive";
                                        } else {
                                                $val_text = "";
                                        }

                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"] = $val_text;

                                        // end pricerange
                                        // change for rating
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["avarage_rating"];

                                        $rating_number = 0;
                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] < 0 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] < 1) {
                                                // echo "in .5";
                                                $rating_number = 0.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 1 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 1.74) {
                                                // echo "in 1";
                                                $rating_number = 1;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 1.75 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 2.24) {
                                                // echo "2";
                                                $rating_number = 2;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 2.25 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 2.74) {
                                                //echo "2,5";
                                                $rating_number = 2.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 2.75 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 3.24) {
                                                //echo "3";
                                                $rating_number = 3;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 3.25 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 3.74) {
                                                //  echo "3.5";
                                                $rating_number = 3.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 3.75 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 4.24) {
                                                // echo "4";
                                                $rating_number = 4;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 4.25 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 4.74) {
                                                //  echo "4.5";
                                                $rating_number = 4.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 4.75) {
                                                // echo "5";
                                                $rating_number = 5;
                                        }
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_main_ratings_number"] = $rating_number;

                                        // change for rating
                                        // start campaign array

                                        $count = 0;
                                        $campaignlist = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_id"];
                                        //echo "campaignlist = ".$campaignlist."<br/>";
                                        $cmapignlist_array = explode(",", $campaignlist);
                                        $businesslogo_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaign_businesslogo"]);
                                        $businesstitle_array = explode("##sep##", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_title"]);
                                        $campaigncategory_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_categories"]);
                                        $campaignwalkin_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_walkin"]);
                                        $campaignofferleft_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_offersleft"]);
                                        $campaignisnew_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_new_campaigns"]);
                                        $campaigndelavalue_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_dealvalue"]);
                                        $campaigndiscount_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_discount"]);
                                        $campaignsaving_discount = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_saving"]);
                                        $campaignexpiredate_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expiredate"]);
                                        $campaignexpirationdate_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expirationdate"]);
                                        $locationwise_campaign_array = array();
                                        for ($cnti = 0; $cnti < count($cmapignlist_array); $cnti++) {
                                                $campaign_array = array();
                                                $campaign_array['id'] = $cmapignlist_array[$cnti];
                                                $campaign_array['business_logo'] = $businesslogo_array[$cnti];
                                                $campaign_array['title'] = $businesstitle_array[$cnti];
                                                $campaign_array['category_id'] = $campaigncategory_array[$cnti];
                                                $campaign_array['is_walkin'] = $campaignwalkin_array[$cnti];
                                                $campaign_array['offers_left'] = $campaignofferleft_array[$cnti];
                                                $campaign_array['is_new'] = $campaignisnew_array[$cnti];
                                                $campaign_array['deal_value'] = $campaigndelavalue_array[$cnti];
                                                $campaign_array['discount'] = $campaigndiscount_array[$cnti];
                                                $campaign_array['saving'] = $campaignsaving_discount[$cnti];
                                                $campaign_array['expire_date'] = $campaignexpiredate_array[$cnti];
                                                $campaign_array['expiration_date'] = $campaignexpirationdate_array[$cnti];
                                                array_push($locationwise_campaign_array, $campaign_array);
                                        }

                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"] = $locationwise_campaign_array;


                                        // end campaign array
                                        // start location category
                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'] != "") {
                                                $count = 0;
                                                $catgory_array = array();
                                                $innercategory_array['id'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'];
                                                $innercategory_array['cat_name'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["locationcategories"];
                                                array_push($catgory_array, $innercategory_array);
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = $catgory_array;
                                        } else {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = array();
                                        }

                                        // end location category
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["locationcategories"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["cid"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_id"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaign_businesslogo"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_title"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_categories"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_walkin"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_offersleft"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_new_campaigns"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_dealvalue"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_discount"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_saving"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expiredate"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expirationdate"]);

                                        array_push($final_array, $arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
                                }
                        }
                }

                $json_array['status'] = "true";
                //echo "<pre>final array";
                //print_r($final_array);
                //echo "</pre>";
                //$json = json_encode($final_array);
                //echo $json;
                //exit();
                $json_array["records"] = $final_array;
                /*
                  while($Row = $RS_limit_data->FetchRow())
                  {
                  $records_all[$count] = get_field_value($Row);
                  $count++;
                  } */
                //	$json_array["marker_records"]= $records_all;
                //$json_array['total_records'] =  count($json_array['records']);
                $json_array['all_records'] = count($json_array['records']);
                $json_array['total_records'] = count($json_array['records']);
                $json_array['marker_total_records'] = count($json_array['records']);
                if (count($json_array['records']) == 0) {
                        $json_array['status'] = "false";
                        $json_array['error_msg'] = $client_msg["mymerchant"]["label_filter_area"];
                        $json_array['all_records'] = 0;
                        $json_array["records"] = "";
                        $json_array['status'] = "false";
                        $json_array['total_records'] = 0;
                        $json_array['is_profileset'] = 1;
                        $json_array["marker_records"] = "";
                        $json_array['marker_total_records'] = 0;

                        $json = json_encode($json_array);
                        echo $json;
                        exit;
                }
        } else {

                $json_array['status'] = "false";
                $json_array['error_msg'] = $client_msg["mymerchant"]["label_filter_area"];
                $json_array['all_records'] = 0;
                $json_array["records"] = "";
                $json_array['status'] = "false";
                $json_array['total_records'] = 0;
                $json_array['is_profileset'] = 1;
                $json_array["marker_records"] = "";
                $json_array['marker_total_records'] = 0;

                $json = json_encode($json_array);
                echo $json;
                exit;
        }


        /* $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;

          // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

          $limit_data = "SELECT l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,".$get_dat." round((((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515 ),2) as distance , count(*) total_deals,mu.business,mu.merchant_icon,l.timezone FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." group by cl.location_id ORDER BY distance,c.expiration_date";


          $RS_limit_data=$objDB->Conn->Execute($limit_data);
          if($RS_limit_data->RecordCount()>0)
          {

          }
          else
          {
          $json_array['all_records'] = 0;
          $json_array["records"]="";
          $json_array['status'] = "false";
          $json_array['error_msg'] = "";
          $json_array['total_records'] = 0;
          $json_array['is_profileset'] = 1;
          $json_array["marker_records"]= "";
          $json_array['marker_total_records'] = 0;
          $json = json_encode($json_array);
          echo $json;
          exit();
          } */
        $json = json_encode($json_array);
        echo $json;
        exit();
}

/**
 * @uses get saved offer
 * @param customer_id
 * @return string
 */
if (isset($_REQUEST['btnGetSavedOffers'])) {
        if (isset($_REQUEST['customer_id'])) {
                if (isset($_REQUEST['currentLocationName']) && $_REQUEST['currentLocationName'] != "") {
                        /* $sql_update = "update customer_user set current_location = '".$_REQUEST['currentLocationName']."' where id=".$_REQUEST['customer_id'];
                          $RS_cl_update = $objDB->Conn->Execute($sql_update); */
                        $RS_cl_update = $objDB->Conn->Execute("update customer_user set current_location =? where id=?", array(urldecode($_REQUEST['currentLocationName']), $_REQUEST['customer_id']));
                }
        }

        $json_array = array();
        $records = array();
        $records_all = array();
        $json_array1 = array();
        $category_id = $_REQUEST['category_id'];
        //$dismile=$_REQUEST['dismile'];
        //$dismile= 50;

        if (isset($_REQUEST['dismile'])) {
                $dismile = $_REQUEST['dismile'];
        } else {
                $dismile = 20;
        }
        if (isset($_REQUEST['is_current_location']) && $_REQUEST['is_current_location'] == 0) {
                //	$dismile=20;
                //	$json_array['miles_data'] = 20;
        }
        $miles_array[0][2] = 0;
        $miles_array[0][5] = 0;
        $miles_array[0][10] = 0;
        $miles_array[0][15] = 0;
        $miles_array[0][20] = 0;
        $miles_indexes = array(2, 5, 10, 15, 20, 50);
        $current_index = array_search($dismile, $miles_indexes);
        $date_f = date("Y-m-d H:i:s");
        $mlatitude = $_REQUEST['mlatitude'];
        $mlongitude = $_REQUEST['mlongitude'];

        $user_mlatitude = $_REQUEST['user_mlatitude'];
        $user_mlongitude = $_REQUEST['user_mlongitude'];


        if (isset($_REQUEST['customer_id'])) {
                $customer_id = $_REQUEST['customer_id'];
        } else {
                $customer_id = 0;
        }
        $calculatedmiles = 20 * 20;

        $id = $_REQUEST['customer_id'];



        $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;
        //$Sql = "SELECT sl.* FROM locations sl WHERE sl.created_by=".$merchantid." and ".$Where;

        $date_wh = " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1";

        $cust_where = "";

        $cat_str = "";
        if ($_REQUEST['customer_id'] != "") {
                $customer_id = $_REQUEST['customer_id'];
                $get_dat = " (select ss.subscribed_status from subscribed_stores ss where ss.customer_id=" . $customer_id . " and
	ss.location_id=l.id) is_subscribed ,( select count(*) from customer_campaigns where customer_id = " . $customer_id . "
	and location_id=cl.location_id) total_reserved,";

                //$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id." and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=".$customer_id." and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
                // 12-8-2013
                $cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
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
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) ) )";
                // 03-10-2013	
                // 05-10-2013 dist list deal display on search deal page if reserved also , remove problem of reserved dist list deal problem
                $cust_where = " and ((c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where mg.location_id= l.id and ms.user_id=" . $customer_id . " and l.id in (select ss.location_id from subscribed_stores ss where ss.customer_id=" . $customer_id . " and ss.location_id=l.id  and ss.subscribed_status=1)) or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  )) or ( c.id in ( select cg.campaign_id from merchant_subscribs ms inner join merchant_groups mg on mg.id = ms.group_id inner join campaign_groups cg on ms.group_id =cg.group_id where mg.location_id= l.id and ms.user_id=" . $customer_id . " and mg.private!=1 ) and c.id not in(select campaign_id from customer_campaigns where customer_id =" . $customer_id . " and location_id=cl.location_id) ) )";
                // 05-10-2013
        } else {
                $cust_where = " and c.level=1 ";
        }
        if (isset($_REQUEST['category_id'])) {
                if ($_REQUEST['category_id'] == 0) {
                        $cat_str = "";
                } else {
                        $cat_str = " and c.category_id = " . $_REQUEST['category_id'] . " and c.category_id in(select cat.id from categories cat where cat.active=1) ";
                }
        }

        /*
          $limit_data = "SELECT mu.business Business,l.*,c.*,l.id locid ,c.id cid,l.timezone timezone ,l.created_by l_created_by ,c.created_by c_created_by , cl.offers_left ,(((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515 ) as distance FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." ORDER BY distance,c.expiration_date";
         */




        $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=" . $dismile * $dismile;

        $limit_data = "SELECT l.id location_id ,l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,
                round((((acos(sin((" . $mlatitude . "*pi()/180)) * 
            sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
            cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
            pi()/180))))*180/pi())*60*1.1515),2) as distance,l.timezone ,l.timezone_name
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
 WHERE l.active = 1 and 
  c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
  " . $cat_str . "  " . $date_wh . " and " . $Where . " group by cl.location_id ORDER BY distance,c.expiration_date";
        //echo limit_data;
        //exit();
        // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

        $calculatedmiles = 20 * 20;

        //echo "call mydeals_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0)";

        $mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
        if (!$mysqli)
                die('Could not connect: ' . mysqli_error());
        mysqli_select_db($mysqli, DATABASE_NAME);
        if (!$mysqli)
                die('Could not connect to DB: ' . mysqli_error());

// GetAllUserSessions(IN username CHAR(20))
        $result = $mysqli->query("call mydeals_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0,'" . CURR_TIMEZONE . "')");
        if (!$result)
                die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
        $RS_limit_data = $result;

//$RS_limit_data=$objDB->Conn->Execute("call mydeals_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0,'".CURR_TIMEZONE."')");
        //if($RS_limit_data->RecordCount()>0)
        if ($RS_limit_data->num_rows > 0) {
                //echo "in if";
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                //$json_array['total_records'] = 17;//$RS_limit_data->RecordCount();
                //$json_array['all_records'] = 17;//$RS_limit_data->RecordCount();
                $json_array['miles_data'] = 5;
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                $json_array['is_profileset'] = 1;
                $json_array['status'] = "true";
                $json_array['sp'] = "call mydeals_script($calculatedmiles,$mlatitude,$mlongitude,$user_mlatitude,$user_mlongitude,$customer_id,0)";
                $json_array['data'] = "https://www.scanflip.com/includes/customer/process_mobile.php?btnGetSubscribedMerchantLocations=yes&category_id=" . $_REQUEST['category_id'] .
                        "&mlatitude=" . $_REQUEST['mlatitude'] .
                        "&mlongitude=" . $_REQUEST['mlongitude'] .
                        "&user_mlatitude=" . $_REQUEST['user_mlatitude'] .
                        "&user_mlongitude=" . $_REQUEST['user_mlongitude'] .
                        "&dismile=" . $_REQUEST['dismile'] .
                        "&is_current_location=" . $_REQUEST['is_current_location'] .
                        "&is_current_location=" . $_REQUEST['is_current_location'];

                //$json_array['total_records'] = $RS_limit_data->RecordCount();
                //	$json_array['all_records'] = $RS_limit_data->RecordCount();
                $count = 0;
                $arr_main_merchant_arr = array();
                $arr_main_location_arr = array();
                $all_mercahnts = array();
                $target_distance = $dismile;
                $isrecord_found = false;
                //$current_index = 0;

                while ($Row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                //while($Row =$RS_limit_data->FetchRow())
                        $deal_distance = intval($objJSON->distance($mlatitude, $mlongitude, $Row['latitude'], $Row['longitude'], "M"));

                        //echo $Row['distance']."-----".$target_distance."<br/>";
                        if ($deal_distance > $target_distance) {

                                if (!$isrecord_found) {

                                        if ($target_distance != count($miles_indexes)) {
                                                //echo "inn if";
                                                $current_index = array_search($target_distance, $miles_indexes);
                                                $target_distance = $miles_indexes[$current_index + 1];
                                                //	echo "++++".$target_distance."++++";
                                        } else {
                                                
                                        }
                                }
                        }
                        //echo "<br/>====".$Row['distance']."===".$target_distance."<br/>";
                        if ($deal_distance <= $target_distance) {
                                $isrecord_found = true;
                                $json_array['miles_data'] = $target_distance;
                                //echo "inn ifffff";
                                $isrecord_found = true;
                                $temp_merchant_arr = array();
                                //	echo "<br/>".count($arr_main_merchant_arr[$Row['merchant']])."===<br/>";
                                //	$temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];

								 // remove undefined index error
								if(isset($arr_main_merchant_arr[$Row['merchant']]))
								{
								}
								else
								{
									$arr_main_merchant_arr[$Row['merchant']]="";
								}
								// remove undefined index error
	
                                if (!empty($arr_main_merchant_arr) && $arr_main_merchant_arr[$Row['merchant']] != 0) {
                                        $temp_merchant_arr = $arr_main_merchant_arr[$Row['merchant']];
                                }
                                if (!in_array($Row['merchant'], $all_mercahnts)) {
                                        array_push($all_mercahnts, $Row['merchant']);
                                }
                                $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                                //print_r($arr_main_location_arr);

                                array_push($temp_merchant_arr, get_field_value($Row));
                                //print_r($temp_merchant_arr);
                                $arr_main_merchant_arr[$Row['merchant']] = $temp_merchant_arr;
                                //print_r($arr_main_merchant_arr[$Row['merchant']]);
                                //exit();
                                /* if (! array_key_exists($Row['location_id'], $arr_main_location_arr)) {
                                  $arr_main_location_arr[$Row['location_id']] = get_field_value($Row);
                                  }
                                  else{
                                  if(! in_array($Row['location_id']."-".$Row['location_id'],$arr_main_location_arr))
                                  {
                                  $arr_main_location_arr[$Row['location_id']] = $Row['location_id']."-".$Row['location_id'];
                                  }
                                  } */

                                $records[$count] = get_field_value($Row);
                                //$records[$count]["rating"] = $objJSON->get_location_rating($Row["locid"]);

                                $count++;
                        }
                }



                //echo "<pre>";
                //print_r($arr_main_merchant_arr);
                //echo "</pre>";
                $json = json_encode($arr_main_merchant_arr);
                $id = 0;
                $final_array = array();
                $max_counter = 0;
                //	print_r($all_mercahnts);
                for ($k = 0; $k < count($all_mercahnts); $k++) {
                        $max_counter1 = count($arr_main_merchant_arr[$all_mercahnts[$k]]);
                        if ($max_counter <= $max_counter1) {
                                $max_counter = $max_counter1;
                        }
                }
                $final_array = array();
                //echo $max_counter."==";
                for ($j = 0; $j < $max_counter; $j++) {
                        for ($y = 0; $y < count($all_mercahnts); $y++) {
							
							// remove undefined index error
							if(isset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]))
							{
							}
							else
							{
								$arr_main_merchant_arr[$all_mercahnts[$y]][$j]="";
							}
							// remove undefined index error

                                if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j] != "") {

                                        // start distance
                                        $location_latitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["latitude"];
                                        $location_longitude = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["longitude"];
                                        $deal_distance = $objJSON->distance($user_mlatitude, $user_mlongitude, $location_latitude, $location_longitude, "M");
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["distance"] = $deal_distance;
                                        // end distance
                                        // start location hour code
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"]."</br>";
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"]."</br>";

                                        $location_id = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"];

                                        $time_zone = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone_name"];
                                        date_default_timezone_set($time_zone);
                                        $current_day = date('D');
                                        $current_time = date('g:i A');

                                        $location_time = "";
                                        $start_time = "";
                                        $end_time = "";
                                        $status_time = "";

                                        $start_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"];
                                        $end_time = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];
                                        $location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"] . " - ";
                                        $location_time.= $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"];


                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 1) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Open";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["is_open"] == 0) {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["currently_open"] = "Currently Close";
                                        }
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_hours"] = $location_time;
                                        // end location hour code
                                        // start business name
                                        // end business name
                                        // start merchant business tags
                                        // end merchant business tags
                                        // start pricerange

                                        $val = "";
                                        $val_text = "";

                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 1) {
                                                $val_text = "Inexpensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 2) {
                                                $val_text = "Moderate";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 3) {
                                                $val_text = "Expensive";
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange"] == 4) {
                                                $val_text = "Very Expensive";
                                        } else {
                                                $val_text = "";
                                        }

                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["pricerange_text"] = $val_text;

                                        // end pricerange
                                        // change for rating
                                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["avarage_rating"];

                                        $rating_number = 0;
                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] < 0 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] < 1) {
                                                // echo "in .5";
                                                $rating_number = 0.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 1 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 1.74) {
                                                // echo "in 1";
                                                $rating_number = 1;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 1.75 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 2.24) {
                                                // echo "2";
                                                $rating_number = 2;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 2.25 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 2.74) {
                                                //echo "2,5";
                                                $rating_number = 2.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 2.75 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 3.24) {
                                                //echo "3";
                                                $rating_number = 3;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 3.25 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 3.74) {
                                                //  echo "3.5";
                                                $rating_number = 3.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 3.75 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 4.24) {
                                                // echo "4";
                                                $rating_number = 4;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 4.25 && $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] <= 4.74) {
                                                //  echo "4.5";
                                                $rating_number = 4.5;
                                        } else if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['avarage_rating'] >= 4.75) {
                                                // echo "5";
                                                $rating_number = 5;
                                        }
                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_main_ratings_number"] = $rating_number;

                                        // change for rating
                                        // start campaign array

                                        $count = 0;
                                        $campaignlist = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_id"];
                                        //echo "campaignlist = ".$campaignlist."<br/>";
                                        $cmapignlist_array = explode(",", $campaignlist);
                                        $businesslogo_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaign_businesslogo"]);
                                        $businesstitle_array = explode("##sep##", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_title"]);
                                        $campaigncategory_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_categories"]);
                                        $campaignwalkin_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_walkin"]);
                                        $campaignofferleft_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_offersleft"]);
                                        $campaignisnew_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_new_campaigns"]);
                                        $campaigndelavalue_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_dealvalue"]);
                                        $campaigndiscount_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_discount"]);
                                        $campaignsaving_discount = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_saving"]);
                                        $campaignexpiredate_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expiredate"]);
                                        $campaignexpirationdate_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expirationdate"]);
                                        $campaignnumberofusecampaign_array = explode(",", $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_numberofuse"]);
                                        $locationwise_campaign_array = array();

                                        //echo "count = ".count($cmapignlist_array);

                                        for ($cnti = 0; $cnti < count($cmapignlist_array); $cnti++) {

                                                /*                                                 * ******** */
                                                //echo "for loop in 1"."</br>";
                                                $array_where_camp = array();
                                                //echo $cmapignlist_array[$cnti]."-".$location_id."-".$_REQUEST['customer_id'];
                                                $array_where_camp['campaign_id'] = $cmapignlist_array[$cnti];
                                                $array_where_camp['customer_id'] = $_REQUEST['customer_id'];
                                                $array_where_camp['referred_customer_id'] = 0;
                                                $array_where_camp['location_id'] = $location_id;
                                                $RS_camp = $objDB->Show("reward_user", $array_where_camp);

                                                /*
                                                  echo "<pre>";
                                                  print_r($RS_camp);
                                                  echo "</pre>";
                                                 */
                                                //echo "for loop in 2"."</br>";
                                                if ($RS_camp->RecordCount() > 0 && $campaignnumberofusecampaign_array[$cnti] == "1") {
                                                        //echo "1 ".$Row_campaign['id'].",".$location_id."</br>";
                                                } else if ($RS_camp->RecordCount() > 0 && ($campaignnumberofusecampaign_array[$cnti] == "2" || $campaignnumberofusecampaign_array[$cnti] == "3" ) && $campaignofferleft_array[$cnti] == 0) {
                                                        //echo "2 ".$Row_campaign['id'].",".$location_id."</br>";
                                                } else {
                                                        //echo "else"."</br>";
                                                        $campaign_array = array();
                                                        $campaign_array['id'] = $cmapignlist_array[$cnti];
                                                        $campaign_array['business_logo'] = $businesslogo_array[$cnti];
                                                        $campaign_array['title'] = $businesstitle_array[$cnti];
                                                        $campaign_array['category_id'] = $campaigncategory_array[$cnti];
                                                        $campaign_array['is_walkin'] = $campaignwalkin_array[$cnti];
                                                        $campaign_array['offers_left'] = $campaignofferleft_array[$cnti];
                                                        $campaign_array['is_new'] = $campaignisnew_array[$cnti];
                                                        $campaign_array['deal_value'] = $campaigndelavalue_array[$cnti];
                                                        $campaign_array['discount'] = $campaigndiscount_array[$cnti];
                                                        $campaign_array['saving'] = $campaignsaving_discount[$cnti];
                                                        $campaign_array['expire_date'] = $campaignexpiredate_array[$cnti];
                                                        $campaign_array['expiration_date'] = $campaignexpirationdate_array[$cnti];
                                                        array_push($locationwise_campaign_array, $campaign_array);
                                                        $count++;
                                                }
                                                //echo "for loop in 3"."</br>";
                                                /*                                                 * ******** */
                                        }

                                        //echo "count1 = ".$count."</br>";

                                        $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["campaigns"] = $locationwise_campaign_array;


                                        // end campaign array
                                        // start location category
                                        if ($arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'] != "") {
                                                $catgory_array = array();
                                                $innercategory_array['id'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]['categories'];
                                                $innercategory_array['cat_name'] = $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["locationcategories"];
                                                array_push($catgory_array, $innercategory_array);
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = $catgory_array;
                                        } else {
                                                $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_categories"] = array();
                                        }

                                        // end location category
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["locationcategories"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_starttime"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_endtime"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["cid"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_id"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaign_businesslogo"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_title"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_categories"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_walkin"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_offersleft"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_new_campaigns"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_dealvalue"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_discount"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_saving"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expiredate"]);
                                        unset($arr_main_merchant_arr[$all_mercahnts[$y]][$j]["all_campaigns_expirationdate"]);

                                        //echo "count2 = ".$count."</br>";

                                        if ($count != 0) {
                                                array_push($final_array, $arr_main_merchant_arr[$all_mercahnts[$y]][$j]);
                                        }
                                }
                        }
                }

                $json_array['status'] = "true";
                //echo "<pre>final array";
                //print_r($final_array);
                //echo "</pre>";
                //$json = json_encode($final_array);
                //echo $json;
                //exit();
                $json_array["records"] = $final_array;
                /*
                  while($Row = $RS_limit_data->FetchRow())
                  {
                  $records_all[$count] = get_field_value($Row);
                  $count++;
                  } */
                //	$json_array["marker_records"]= $records_all;
                //$json_array['total_records'] =  count($json_array['records']);
                $json_array['all_records'] = count($json_array['records']);
                $json_array['total_records'] = count($json_array['records']);
                $json_array['marker_total_records'] = count($json_array['records']);
                if (count($json_array['records']) == 0) {
                        $json_array['status'] = "false";
                        $json_array['error_msg'] = $client_msg["my_deal"]["label_Currently_Not_Save_Offers"];
                        $json_array['all_records'] = 0;
                        $json_array["records"] = "";
                        $json_array['status'] = "false";
                        $json_array['total_records'] = 0;
                        $json_array['is_profileset'] = 1;
                        $json_array["marker_records"] = "";
                        $json_array['marker_total_records'] = 0;

                        $json = json_encode($json_array);
                        $result->close();
                        $mysqli->next_result();
                        $mysqli->close();
                        echo $json;
                        exit;
                }
        } else {

                $json_array['status'] = "false";
                $json_array['error_msg'] = $client_msg["my_deal"]["label_Currently_Not_Save_Offers"];
                $json_array['all_records'] = 0;
                $json_array["records"] = "";
                $json_array['status'] = "false";
                $json_array['total_records'] = 0;
                $json_array['is_profileset'] = 1;
                $json_array["marker_records"] = "";
                $json_array['marker_total_records'] = 0;

                $json = json_encode($json_array);
                $result->close();
                $mysqli->next_result();
                $mysqli->close();
                echo $json;
                exit;
        }


        /* $Where = "(69.1*(l.latitude-($mlatitude))*69.1*(l.latitude-($mlatitude)))+(53.0*(l.longitude-($mlongitude))*53.0*(l.longitude-($mlongitude)))<=".$dismile*$dismile ;

          // extra logic to check 20 miles deals available or not and send deals according to smallest miles found

          $limit_data = "SELECT l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.picture,l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,".$get_dat." round((((acos(sin((".$mlatitude."*pi()/180)) *
          sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
          cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
          pi()/180))))*180/pi())*60*1.1515 ),2) as distance , count(*) total_deals,mu.business,mu.merchant_icon,l.timezone FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id inner join merchant_user mu on mu.id =  l.created_by
          WHERE   l.active = 1  ".$cust_where." ".$cat_str  ."  ".$date_wh." and ".$Where." group by cl.location_id ORDER BY distance,c.expiration_date";


          $RS_limit_data=$objDB->Conn->Execute($limit_data);
          if($RS_limit_data->RecordCount()>0)
          {

          }
          else
          {
          $json_array['all_records'] = 0;
          $json_array["records"]="";
          $json_array['status'] = "false";
          $json_array['error_msg'] = "";
          $json_array['total_records'] = 0;
          $json_array['is_profileset'] = 1;
          $json_array["marker_records"]= "";
          $json_array['marker_total_records'] = 0;
          $json = json_encode($json_array);
          echo $json;
          exit();
          } */
        $json = json_encode($json_array);
        echo $json;
        $result->close();
        $mysqli->next_result();
        $mysqli->close();
        exit();
}

/**
 * @uses update pass book
 * @param customer_id
 * @return string
 */
if (isset($_REQUEST['btnUpdatePassbook'])) {



        // Put your device token here (without spaces):
        $deviceToken = '6269e18c5ca47d9fe8ad8a3874672ba4345766688b7b27b8b4752c0ffe769726';

        // Put your private key's passphrase here:
        $passphrase = 'scanflip';

        // Put your alert message here:
        $message = 'ScanFlip ' . $_REQUEST['deviceLibraryIdentifier'];

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'Scanflipck.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);

        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result)
                echo 'Message not delivered' . PHP_EOL;
        else
                echo 'Message successfully delivered' . PHP_EOL;

        // Close the connection to the server
        fclose($fp);
}
/**
 * 
 * @param type $min
 * @param type $max
 * @return type
 */
function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0)
                return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
                $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
}

//http://test.scanflip.com/process_mobile.php?reserve_giftcard=true&giftcard_id=2&user_id=2&order_note=test%20note
if(isset($_REQUEST['reserve_giftcard']))
    
{
	$user_id=$_REQUEST['user_id'];
	$giftcard_id=$_REQUEST['giftcard_id'];
        $order_note=$_REQUEST['order_note'];
        
        $RS = $objDB->Conn->Execute("SELECT * from giftcards where id=? and is_active=1 and is_deleted=0", array($giftcard_id));
        
        $total_records = $RS->RecordCount();
        $redeem_point_value = $RS->fields['redeem_point_value'];
        $card_value = $RS->fields['card_value'];
        $discount = $RS->fields['discount'];
	$is_per = $RS->fields['is_per'];
        $merchant_id = $RS->fields['merchant_id'];
        $RES = $objDB->Conn->Execute("SELECT pp.points, pp.currency_id from point_packages pp join merchant_user mu on mu.currency_id = pp.currency_id where mu.id=?",array($merchant_id));
        $conv_points = $RES->fields['points'];
	$currency_id = $RES->fields['currency_id'];
        $total_records = $RES->RecordCount();
	$array = $json_array = $array1 = $array2 = $array3 = $redeem = array();
                
	if($total_records==1)
	{
        $redeem = reward_zone_redeem_point($card_value,$discount,$conv_points,$is_per);
        
        $array1['total_redemption_points'] = $redeem['redeem'];
        $RS1 = $objDB->Conn->Execute("SELECT avail_points from customer_manage_points where customer_id=?", array($user_id));
        $avail_points = $RS1->fields['avail_points'];
        
        if ($array1['total_redemption_points'] > $avail_points) {
            $json_array['status'] = "false";
            $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_giftcard_points_reserve_check"];
            $json = json_encode($json_array);
            echo $json;
            exit();
        } else {
            $updated_points = $avail_points - $array1['total_redemption_points'];
            $objDBWrt->Conn->Execute("Update customer_manage_points set avail_points=? , modified_date=? where customer_id=?", array($updated_points, date("Y-m-d H:i:s"), $user_id,));
            $array2['customer_id'] = $user_id;
            $array2['debited'] = $array1['total_redemption_points'];
            $array2['currency_id'] = $currency_id;
            $array2['datetime'] = date("Y-m-d H:i:s");
            $objDB->Insert($array2, "customer_points");
            
            $RS1 = $objDB->Conn->Execute("SELECT points_earned_giftcard_pending from merchant_point_management where merchant_id=?", array($merchant_id));
            $points = $RS1->fields['points_earned_giftcard_pending'];
            $points_update = $points + $redeem['merchants_points'];
            $objDBWrt->Conn->Execute("Update merchant_point_management set points_earned_giftcard_pending=? where merchant_id=?", array($points_update,$merchant_id));
        }

        $array1['scanflip_application_fee'] = $redeem['app_fee'];
        $array1['purchase_price'] = $redeem['discounted_value'];
        $array1['card_value'] = $card_value;
        $array1['merchants_points'] = $redeem['merchants_points'];
        $code = create_unique_code_for_getcard();	
        $certificate_id = "GC".$code;
        $array1['certificate_id'] = $certificate_id;
        $array1['user_id'] = $user_id;
	$array1['giftcard_id'] = $giftcard_id;
	$array1['date_issued'] = date("Y-m-d H:i:s");
	$array1['status'] = 0;
        $array1['merchant_points_credited'] = 0;
        
        $id = $objDB->Insert($array1, "giftcard_certificate");
        
        $certi_id = $objDB->Conn->Insert_ID();
        $RS2 = $objDB->Conn->Execute("SELECT card_id from customer_user where id=?", array($user_id));
        $card_id = $RS2->fields['card_id'];
        $array3['card_id'] = $card_id;
        $array3['giftcard_certificate_id'] = $certi_id;
        $id = $objDB->Insert($array3, "customer_card");
         
        $array['user_id'] = $user_id;
	$array['giftcard_id'] = $giftcard_id;
	$array['order_date'] = date("Y-m-d H:i:s");
	$array['status'] = 2;
	$array['order_note'] = $order_note;
	$array['order_number'] = $giftcard_id.strtotime(date("Y-m-d H:i:s")).$array['user_id'];
	
	$id  = $objDB->Insert($array, "giftcard_order");
        
        $json_array['status'] = "true";
        $json_array['certificate_id'] = $certificate_id;
	$json_array['message'] = $merchant_msg["redeem-deal"]["Msg_giftcard_reserve"];
	$json = json_encode($json_array);
	echo $json;
	exit();
                
	}
	else
	{
        $json_array['status'] = "false";
        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_invalid_giftcard"];
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

if(isset($_REQUEST['reserve_rewardzone_campaign']))
    
{
	$user_id=$_REQUEST['user_id'];
	$campaign_id=$_REQUEST['campaign_id'];
        
        $RS = $objDB->Conn->Execute("SELECT * from rewardzone_campaigns where id=? and active=1 and is_deleted=0", array($campaign_id));
        
        $total_records = $RS->RecordCount();
        $merchant_id = $RS->fields['merchant_id'];
        $value = $RS->fields['value'];
        $discount = $RS->fields['discount'];
	$is_per = $RS->fields['is_percentage'];
        
        $RES = $objDB->Conn->Execute("SELECT pp.points, pp.currency_id from point_packages pp join merchant_user mu on mu.currency_id = pp.currency_id where mu.id=?",array($merchant_id));
        $conv_points = $RES->fields['points'];
	$currency_id = $RES->fields['currency_id'];
        
	$array = $array3 = $json_array = $redeem = array();
                
	if($total_records==1)
	{
        $redeem = reward_zone_redeem_point($value,$discount,$conv_points,$is_per);    
        $array['total_redemption_points'] = $redeem['redeem'];    
        $RS1 = $objDB->Conn->Execute("SELECT avail_points from customer_manage_points where customer_id=?", array($user_id));
        $avail_points = $RS1->fields['avail_points'];
        
        if ($array['total_redemption_points'] > $avail_points) {
            $json_array['status'] = "false";
            $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_reward_campaign_points_check"];
            $json = json_encode($json_array);
            echo $json;
            exit();
        } else {
            $updated_points = $avail_points - $array['total_redemption_points'];
            $objDBWrt->Conn->Execute("Update customer_manage_points set avail_points=? , modified_date=? where customer_id=?", array($updated_points, date("Y-m-d H:i:s"), $user_id,));
            $array2['customer_id'] = $user_id;
            $array2['debited'] = $array['total_redemption_points'];
            $array2['currency_id'] = $currency_id;
            $array2['datetime'] = date("Y-m-d H:i:s");
            $objDB->Insert($array2, "customer_points");
        }
        
        $code = create_unique_code_for_getcard();	
        $certificate_id = "CP".$code;
        $array['certificate_id'] = $certificate_id;
        $array['user_id'] = $user_id;
	$array['rewardzone_campaign_id'] = $campaign_id;
	$array['date_issued'] = date("Y-m-d H:i:s");
	$array['status'] = 1;
        $array['scanflip_points'] = $redeem['app_fee'];
        //$array['balance'] = $redeem['discounted_value'];
        $array['merchants_points'] = $redeem['merchants_points'];
        $array['expiry_date']= date("Y-m-d H:i:s",strtotime("+365 day", strtotime(date("Y-m-d H:i:s"))));
        //$array['total_redemption_points'] = 0;
        
        $id  = $objDB->Insert($array, "rewardzone_campaign_certificate");
        
        $certi_id = $objDB->Conn->Insert_ID();
        $RS2 = $objDB->Conn->Execute("SELECT card_id from customer_user where id=?", array($user_id));
        $card_id = $RS2->fields['card_id'];
        $array3['card_id'] = $card_id;
        $array3['reward_campaign_certificate_id'] = $certi_id;
        $id = $objDB->Insert($array3, "customer_card");
        
        $json_array['status'] = "true";
        $json_array['certificate_id'] = $certificate_id;
	$json_array['message'] = $merchant_msg["redeem-deal"]["Msg_reward_campaign_reserve"];
	$json = json_encode($json_array);
	echo $json;
	exit();
                
	}
	else
	{
        $json_array['status'] = "false";
        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_invalid_reward_campaign"];
	}
	$json = json_encode($json_array);
	echo $json;
	exit();
}

function reward_zone_redeem_point($oprice,$dprice,$conv_points,$is_per){

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
        $redeem = round($discounted_value + $app_fee)*$conv_points;
        $merchants_points = $discounted_value*$conv_points;
        
        return array('app_fee'=>$app_fee*$conv_points, 'redeem'=>$redeem,'discount'=>$discount,'discounted_value'=>$discounted_value,'merchants_points'=>$merchants_points);
}

function create_unique_code_for_getcard() 
{
        $code_length = 8;
        $alfa = "12345678910ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $code = "";
        for ($i = 0; $i < $code_length; $i ++) {
                $code .= $alfa[rand(0, strlen($alfa) - 1)];
        }
        return $code;
}
?>
