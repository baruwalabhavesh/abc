<?php
header('Content-type: text/html; charset=utf-8');
//require_once("../classes/Config.Inc.php");
require_once(LIBRARY . "/class.phpmailer.php");
//include_once(SERVER_PATH . "/classes/DB.php");
//include_once(SERVER_PATH . "/classes/JSON.php");
include(LIBRARY . '/simpleimage.php');
require_once(LIBRARY . "/PHP-PasswordLib-master/lib/PasswordLib/PasswordLib.php");
//$objDB = new DB('read');
//$objDBWrt = new DB('write');
//$objJSON = new JSON();

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

if (isset($_REQUEST['btnLogin'])) {
        $array = $json_array = array();
        $array_role = $json_array = array();
        $array1 = $json_array = array();
        $array2 = $json_array = array();


        $array['email'] = $_REQUEST['email'];
        //$array['password'] = md5($_REQUEST['password']);

        $RS = $objDB->Show("merchant_user", $array);
        $merchant_parent_value = $RS->fields['merchant_parent'];

        $array2['email'] = $_REQUEST['email'];
        $get_password = $objDB->Show("merchant_user", $array2);


        if ($get_password->RecordCount() <= 0) {
                $json_array['status'] = "false";
                $json_array['message'] = $merchant_msg['login_register']['Msg_not_register'];
                $json = json_encode($json_array);
                echo $json;
                exit();
        } else {
                $PasswordLib2 = new \PasswordLib\PasswordLib;
                ///echo "hello";
                //print_r($RS);

                 if($RS->fields['password']=="")
				{
					 $result = 0;
				}
				else
				{
					$result = $PasswordLib2->verifyPasswordHash($_REQUEST['password'], $RS->fields['password']);
				}
                if (!$result) {
                        $json_array['status'] = "false";
                        $json_array['message'] = $merchant_msg['login_register']['Msg_login_password_not_match'];
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        }

        if ($merchant_parent_value == 0) {

                if ($RS->fields['active'] == 0) {

                        $json_array['status'] = "false";
                        $json_array['message'] = $merchant_msg['login_register']['Msg_login_password_not_match'];
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
                if ($RS->fields['approve'] == 0) {
                        $json_array['status'] = "false";
                        $json_array['message'] = $merchant_msg['login_register']['Msg_status_blocked'];
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        } else {
                $array1['id'] = $merchant_parent_value;
                $RS1 = $objDB->Show("merchant_user", $array1);

                if ($RS1->fields['approve'] == 0) {

                        $json_array['status'] = "false";
                        $json_array['message'] = $merchant_msg['login_register']['Msg_status_blocked'];
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
                if ($RS->fields['active'] == 0) {

                        $json_array['status'] = "false";
                        $json_array['message'] = $merchant_msg['login_register']['Msg_login_password_not_match'];
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
                if ($RS->fields['approve'] == 0) {

                        $json_array['status'] = "false";
                        $json_array['message'] = $merchant_msg['login_register']['Msg_employee_status_blocked'];
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                }
        }

        $Row = $RS->FetchRow();

        $phone_number = $Row['phone_number'];
        $address = $Row['address'];

        if ($phone_number == "" || $address == "") {
                //$_SESSION['notsetprofile']="yes";
        }

        /* $Sql = "SELECT * from merchant_user_role where merchant_user_id =".$Row['id'];      
          $RS_role = $objDB->Conn->Execute($Sql); */
        $RS_role = $objDB->Conn->Execute("SELECT * from merchant_user_role where merchant_user_id =?", array($Row['id']));

        if ($RS_role->RecordCount() > 0) {
                while ($Row_role = $RS_role->FetchRow()) {
                        $Row_role['merchant_user_id'];
                        $ass_page = unserialize($Row_role['ass_page']);
                        $ass_role = unserialize($Row_role['ass_role']);
                        foreach ($ass_page as $op_page) {
                                $op_page;
                        }
                        foreach ($ass_role as $op_role) {
                                $op_role;
                        }
                }
        } else {
                echo "";
        }

        $array_values = $where_clause = $array = array();
        $array_values['last_login'] = date("Y-m-d H:i:s");
        $where_clause['id'] = $Row['id'];
        $objDBWrt->Update($array_values, "merchant_user", $where_clause);

        $array = array();
        $user_session = session_id();
        $array['sessiontime'] = strtotime(date("Y-m-d H:i:s"));
        $array['session_id'] = base64_encode($Row['id']);
        $array['session_data'] = md5("merchant" . $array['sessiontime'] . $user_session);
        //$array['user_type'] = 2;	

        $objDBWrt->Insert($array, "user_sessions");

        $json_array['status'] = "true";
        $json_array['merchant_id'] = $Row['id'];
        /* 	$Sql = "SELECT * from merchant_user where id =".$Row['id'];       
          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("SELECT * from merchant_user where id =?", array($Row['id']));

        $ar = ($RS->fields);
        $filed_value = get_field_value($ar);
        $filed_value['phone_number'] = substr($filed_value['phone_number'], 4);
        $json_array['merchant_info'] = $filed_value;

        /* $br_sql = "Select * from merchant_user where id=".$Row['id'];
          $br_rs =  $objDB->Conn->Execute($br_sql); */
        $br_rs = $objDB->Conn->Execute("Select * from merchant_user where id=?", array($Row['id']));
        $br = $br_rs->fields['profile_complete'];
        if ($br == "1") {
                $json_array['profile_complete'] = "1";
        } else {
                $json_array['status'] = "merchantsetup";
                if (isset($_REQUEST['redirecttab'])) {
                        if ($_REQUEST['redirecttab'] != "") {
                                $json_array['url'] = "merchant-setup.php?tab=" . $_REQUEST['redirecttab'];
                        } else {
                                $json_array['url'] = "merchant-setup.php";
                        }
                }
                $json_array['profile_complete'] = "0";
        }



        if ($br_rs->fields['merchant_parent'] == 0) {
                $json_array['enable_redeem'] = 1;
                $json_array['location_id'] = $br_rs->fields['redeem_location'];
        } else {
                /* $Sql = "SELECT * from merchant_user_role where merchant_user_id =".$br_rs->fields['id'];
                  $RS_role = $objDB->Conn->Execute($Sql); */
                $RS_role = $objDB->Conn->Execute("SELECT * from merchant_user_role where merchant_user_id =?", array($br_rs->fields['id']));

                while ($Row_role = $RS_role->FetchRow()) {
                        $ass_page = unserialize($Row_role['ass_page']);
                        $ass_role = unserialize($Row_role['ass_role']);
                }

                if (in_array("redeem-deal.php", $ass_page)) {
                        $json_array['enable_redeem'] = 1;
                } else {
                        $json_array['enable_redeem'] = 0;
                }

                $arr = array();
                $arr['merchant_user_id'] = $br_rs->fields['id'];
                $RS_arr = $objDB->Show("merchant_user_role", $arr);

                $json_array['location_id'] = $RS_arr->fields['location_access'];

                $arr_mp = array();
                $arr_mp['id'] = $RS_arr->fields['location_access'];
                $Rowlc = $objDB->Show("locations", $arr_mp);

                $mobileno = substr($Rowlc->fields['phone_number'], 4);
                $json_array['location_address'] = $Rowlc->fields['address'] . ", " . $Rowlc->fields['city'] . ", " . $Rowlc->fields['state'] . ", " . $Rowlc->fields['zip'] . ", " . $mobileno;
        }

        $json_array['password_changed'] = $br_rs->fields['password_changed'];
        $json = json_encode($json_array);
        echo $json;
        exit();
}

if (isset($_REQUEST['btn_getlist_from_rewardcard'])) {
        $array = $json_array = array();
        $campaign_records = array();
        $count = 0;

        $reward_card_number = $_REQUEST['reward_card_number'];
        $merchant_id = $_REQUEST['merchant_id'];
        $location_id = $_REQUEST['location_id'];

        $arr_cust = array();
        $arr_cust['card_id'] = $reward_card_number;
        $RS_cust = $objDB->Show("customer_user", $arr_cust);

        if ($RS_cust->RecordCount() > 0) {
                $customer_id = $RS_cust->fields['id'];

                /* $campaign_sql = "SELECT c.id,c.title,c.business_logo,cc.coupon_code 
                  FROM campaign_location  cl
                  inner join campaigns c  on cl.campaign_id =c.id
                  inner join locations l on l.id = cl.location_id
                  inner join coupon_codes cc on cc.customer_campaign_code=c.id
                  WHERE l.id=$location_id and l.active = 1
                  and c.id in ( select campaign_id from customer_campaigns where customer_id =$customer_id and location_id=cl.location_id and activation_status=1)
                  AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date
                  and cl.active=1 and cc.customer_id=$customer_id and cc.location_id=$location_id
                  ORDER BY c.expiration_date";

                  $RS_campaign_data=$objDB->Conn->Execute($campaign_sql); */
                $RS_campaign_data = $objDB->Conn->Execute("SELECT c.id,c.title,c.business_logo,cc.coupon_code 
				FROM campaign_location  cl
				inner join campaigns c  on cl.campaign_id =c.id 
				inner join locations l on l.id = cl.location_id 
				inner join coupon_codes cc on cc.customer_campaign_code=c.id 
				WHERE l.id=? and l.active = ? 
					  and c.id in ( select campaign_id from customer_campaigns where customer_id =? and location_id=cl.location_id and activation_status=?) 
					  AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date 
					  and cl.active=? and cc.customer_id=? and cc.location_id=? 
				ORDER BY c.expiration_date", array($location_id, 1, $customer_id, 1, 1, $customer_id, $location_id));


                if ($RS_campaign_data->RecordCount() > 0) {
                        while ($Row_campaign = $RS_campaign_data->FetchRow()) {
                                //echo $Row_campaign['id'].</br>";
                                $campaign_records[$count] = get_field_value($Row_campaign);
                                $campaign_records[$count]["title"] = ucwords(strtolower($campaign_records[$count]["title"]));
                                $count++;
                        }

                        $json_array['status'] = "true";
                        $json_array['total_records'] = $RS_campaign_data->RecordCount();
                        $json_array['records'] = $campaign_records;
                        $json = json_encode($json_array);
                        echo $json;
                        exit();
                } else {
                        /* $campaign_sql = "SELECT c.id,c.title,c.business_logo,cc.coupon_code 
                          FROM campaign_location  cl
                          inner join campaigns c  on cl.campaign_id =c.id
                          inner join locations l on l.id = cl.location_id
                          inner join coupon_codes cc on cc.customer_campaign_code=c.id
                          WHERE l.active = 1
                          and c.id in ( select campaign_id from customer_campaigns where customer_id =$customer_id and location_id=cl.location_id and activation_status=1)
                          AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date
                          and cl.active=1 and cc.customer_id=$customer_id
                          ORDER BY c.expiration_date";

                          $RS_campaign_data=$objDB->Conn->Execute($campaign_sql); */
                        $RS_campaign_data = $objDB->Conn->Execute("SELECT c.id,c.title,c.business_logo,cc.coupon_code 
				FROM campaign_location  cl
				inner join campaigns c  on cl.campaign_id =c.id 
				inner join locations l on l.id = cl.location_id 
				inner join coupon_codes cc on cc.customer_campaign_code=c.id 
				WHERE l.active = ? and c.id in ( select campaign_id from customer_campaigns where customer_id =? and location_id=cl.location_id and activation_status=?) AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=? and cc.customer_id=?  
				ORDER BY c.expiration_date", array(1, $customer_id, 1, 1, $customer_id));

                        if ($RS_campaign_data->RecordCount() > 0) {
                                $json_array['status'] = "false";
                                $json_array['error_msg'] = $merchant_msg["redeem-deal"]["Msg_no_offer_assigned_location"];
                                $json_array['total_records'] = 0;
                                $json = json_encode($json_array);
                                echo $json;
                                exit;
                        } else {

                                $json_array['status'] = "false";
                                $json_array['error_msg'] = $merchant_msg["redeem-deal"]["Msg_no_offer"];
                                $json_array["records"] = "";
                                $json_array['total_records'] = 0;
                                $json = json_encode($json_array);
                                echo $json;
                                exit;
                        }
                }
        } else {

                $json_array['status'] = "false";
                $json_array['error_msg'] = $merchant_msg["redeem-deal"]["Msg_enter_reward_card"];
                $json_array['total_records'] = 0;
                $json = json_encode($json_array);
                echo $json;
                exit;
        }
}

function getOneLevel($id) {
        $objDB = new DB();
        /* $s = "select * from merchant_user where merchant_parent =".$id;
          $r = $objDB->execute_query($s); */
        $r = $objDB->Conn->Execute("select * from merchant_user where merchant_parent =?", array($id));
        $mer_id = array();
        if ($r->RecordCount() > 0) {
                while ($row = $r->FetchRow()) {
                        $mer_id[] = $row['id'];
                }
        }
        return $mer_id;
}

function getallsubmercahnt_id($id) {

        $tree = Array();
        if (!empty($id)) {
                $tree = getOneLevel($id);
                foreach ($tree as $key => $val) {
                        $ids = getallsubmercahnt_id($val);
                        $tree = array_merge($tree, $ids);
                }
        }
        return $tree;
}

if (isset($_REQUEST['btnUpdateProfileOfMerchant'])) {

        $array = $json_array = $where_clause = array();
        $merchant_id = $_REQUEST['merchant_id'];
        if (isset($_REQUEST['firstname']))
                $array['firstname'] = $_REQUEST['firstname'];
        if (isset($_REQUEST['lastname']))
                $array['lastname'] = $_REQUEST['lastname'];
        if (isset($_REQUEST['address']))
                $array['address'] = $_REQUEST['address'];
        if (isset($_REQUEST['city']))
                $array['city'] = $_REQUEST['city'];
        if (isset($_REQUEST['state']))
                $array['state'] = $_REQUEST['state'];
        if (isset($_REQUEST['zipcode']))
                $array['zipcode'] = $_REQUEST['zipcode'];
        if (isset($_REQUEST['country']))
                $array['country'] = $_REQUEST['country'];
        if (isset($_REQUEST['phone_number']))
                $array['phone_number'] = "001-" . $_REQUEST['phone_number'];

        if (isset($_REQUEST['business']))
                $array['business'] = ucwords(strtolower($_REQUEST['business']));
        if (isset($_REQUEST['aboutus']))
                $array['aboutus'] = $_REQUEST['aboutus'];
        if (isset($_REQUEST['aboutus_short']))
                $array['aboutus_short'] = $_REQUEST['aboutus_short'];
        if (isset($_REQUEST['business_tags']))
                $array['business_tags'] = $_REQUEST['business_tags'];

        $where_clause['id'] = $merchant_id;


        if ($where_clause['id'] == "") {
                $json_array['status'] = "false";
                $json_array['message'] = "Invalid Merchant ID";
        } else {
                $objDBWrt->Update($array, "merchant_user", $where_clause);
                $json_array['status'] = "true";
                $json_array['message'] = "Profile has been updated successfully";
        }

        $json = json_encode($json_array);
        echo $json;
        exit();
}
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
if (isset($_REQUEST['btnGetAllLocationforgrid'])) {
        $json_array = array();
        $records = array();

        $merchant_id = $_REQUEST['merchant_id'];
        $mlatitude = $_REQUEST['mycurrent_lati'];
        $mlongitude = $_REQUEST['mycurrent_long'];

        $array_where_mer['id'] = $merchant_id;
        $RSMerchant = $objDB->Show("merchant_user", $array_where_mer);
        $merchant_parent_id = $RSMerchant->fields['merchant_parent'];

        //echo $RSMerchant->fields['merchant_parent']; 
        if ($merchant_parent_id == 0) {
                /*  $Sql = "SELECT (((acos(sin((".$mlatitude."*pi()/180)) * 
                  sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
                  cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
                  pi()/180))))*180/pi())*60*1.1515 ) as distance,l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.timezone,l.picture,l.pricerange,l.phone_number,
                  l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,mu.business
                  FROM locations l,merchant_user mu WHERE mu.id=l.created_by and l.created_by=$merchant_id  ORDER BY distance"; */
                $RS = $objDB->Conn->Execute("SELECT (((acos(sin((" . $mlatitude . "*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ) as distance,l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.timezone,l.picture,l.pricerange,l.phone_number,
				l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,mu.business 
				FROM locations l,merchant_user mu WHERE mu.id=l.created_by and l.created_by=?  ORDER BY distance", array($merchant_id));
        } else {
                $main_merchant_id = getmainmercahnt_id($merchant_id);
                $array_where_mer['id'] = $main_merchant_id;
                $RSMerchant = $objDB->Show("merchant_user", $array_where_mer);

                $media_acc_array = array();
                $media_acc_array['merchant_user_id'] = $merchant_id;
                $RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
                $location_val = $RSmedia->fields['location_access'];


                /* $Sql = "SELECT (((acos(sin((".$mlatitude."*pi()/180)) * 
                  sin((`latitude`*pi()/180))+cos((".$mlatitude."*pi()/180)) *
                  cos((`latitude`*pi()/180)) * cos(((".$mlongitude."- `longitude`)*
                  pi()/180))))*180/pi())*60*1.1515 ) as distance, l.id location_id ,l.timezone,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.phone_number,l.picture,
                  l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,l.website,l.facebook,l.google,
                  mu.business,mu.aboutus,mu.aboutus_short,mu.location_detail_title,mu.location_detail_display,mu.menu_price_title,mu.menu_price_display
                  FROM locations l,merchant_user mu where mu.id =  l.created_by and l.id = ".$location_val; */
                $RS = $objDB->Conn->Execute("SELECT (((acos(sin((" . $mlatitude . "*pi()/180)) * 
	sin((`latitude`*pi()/180))+cos((" . $mlatitude . "*pi()/180)) * 
	cos((`latitude`*pi()/180)) * cos(((" . $mlongitude . "- `longitude`)* 
	pi()/180))))*180/pi())*60*1.1515 ) as distance, l.id location_id ,l.timezone,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.phone_number,l.picture,
	l.pricerange,l.latitude,l.longitude,l.avarage_rating,l.categories,l.is_open,l.website,l.facebook,l.google,
	mu.business,mu.aboutus,mu.aboutus_short,mu.location_detail_title,mu.location_detail_display,mu.menu_price_title,mu.menu_price_display 
	FROM locations l,merchant_user mu where mu.id =  l.created_by and l.id = ?", array($location_val));
        }

        //$RS = $objDB->Conn->Execute($Sql);
        if ($RS->RecordCount() > 0) {
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $count = 0;
                while ($Row = $RS->FetchRow()) {
                        $records[$count] = get_field_value($Row);
                        //$records[$count]["title"] = ucwords(strtolower($records[$count]["title"]));
                        // start location hour code
                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["location_id"]."</br>";
                        //echo $arr_main_merchant_arr[$all_mercahnts[$y]][$j]["timezone"]."</br>";

						$records[$count]['business'] = urldecode($records[$count]['business']);
                        $records[$count]['phone_number'] = substr($Row['phone_number'], 4);

                        $rating_number = 0;
                        if ($Row['avarage_rating'] < 0 && $Row['avarage_rating'] < 1) {
                                // echo "in .5";
                                $class = "orange-half";
                                $rating_number = 0.5;
                                $rating_title = "Poor";
                        } else if ($Row['avarage_rating'] >= 1 && $Row['avarage_rating'] <= 1.74) {
                                // echo "in 1";
                                $class = "orange-one";
                                $rating_number = 1;
                                $rating_title = "Poor";
                        } else if ($Row['avarage_rating'] >= 1.75 && $Row['avarage_rating'] <= 2.24) {
                                // echo "2";
                                $class = "orange-two";
                                $rating_number = 2;
                                $rating_title = "Fair";
                        } else if ($Row['avarage_rating'] >= 2.25 && $Row['avarage_rating'] <= 2.74) {
                                //echo "2,5";
                                $class = "orange-two_h";
                                $rating_number = 2.5;
                                $rating_title = "Good";
                        } else if ($Row['avarage_rating'] >= 2.75 && $Row['avarage_rating'] <= 3.24) {
                                //echo "3";
                                $class = "orange-three";
                                $rating_number = 3;
                                $rating_title = "Good";
                        } else if ($Row['avarage_rating'] >= 3.25 && $Row['avarage_rating'] <= 3.74) {
                                //  echo "3.5";
                                $class = "orange-three_h";
                                $rating_number = 3.5;
                                $rating_title = "Very Good";
                        } else if ($Row['avarage_rating'] >= 3.75 && $Row['avarage_rating'] <= 4.24) {
                                // echo "4";
                                $class = "orange-four";
                                $rating_number = 4;
                                $rating_title = "Very Good";
                        } else if ($Row['avarage_rating'] >= 4.25 && $Row['avarage_rating'] <= 4.74) {
                                //  echo "4.5";
                                $class = "orange-four_h";
                                $rating_number = 4.5;
                                $rating_title = "Excellent";
                        } else if ($Row['avarage_rating'] >= 4.75) {
                                // echo "5";
                                $class = "orange";
                                $rating_number = 5;
                                $rating_title = "Excellent";
                        }
                        $records[$count]["location_main_ratings_number"] = $rating_number;

                        $from_lati1 = $_REQUEST['mycurrent_lati'];

                        $from_long1 = $_REQUEST['mycurrent_long'];

                        $to_lati1 = $Row['latitude'];

                        $to_long1 = $Row['longitude'];

                        $deal_distance = $objJSON->distance($from_lati1, $from_long1, $to_lati1, $to_long1, "M") . "Mi";
                        $records[$count]["miles_away"] = $deal_distance;

                        if ($merchant_parent_id == 0) {
                                $merchant = $Row["merchant"];
                        } else {
                                $merchant = getmainmercahnt_id($Row["merchant"]);
                        }

                        $location_id = $Row["location_id"];

                        if ($merchant_parent_id != 0) {
                                $time_zone = $Row["timezone"];
                                date_default_timezone_set($time_zone);
                                $current_day = date('D');
                                $current_time = date('g:i A');
                                /* $sql="select * from location_hours where location_id=".$location_id." and day='".strtolower($current_day)."'";
                                  $RS_hours_data = $objDB->execute_query($sql); */
                                $RS_hours_data = $objDB->Conn->Execute("select * from location_hours where location_id=? and day=?", array($location_id, strtolower($current_day)));

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

                                if ($Row["is_open"] == 1) {
                                        $records[$count]["currently_open"] = "Currently Open";
                                } else if ($Row["is_open"] == 0) {
                                        $records[$count]["currently_open"] = "Currently Close";
                                }
                                $records[$count]["location_hours"] = $location_time;
                                // end location hour code
                                // start merchant business tags

                                /* $tags_sql = "SELECT business_tags from merchant_user where id =".$merchant; 
                                  $Row_tags=$objDB->Conn->Execute($tags_sql); */
                                $Row_tags = $objDB->Conn->Execute("SELECT business_tags from merchant_user where id =?", array($merchant));
                                $records[$count]['business_tags'] = $Row_tags->fields['business_tags'];

                                // end merchant business tags
                        }

                        // start pricerange

                        $val = "";
                        $val_text = "";

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

                        $records[$count]["pricerange_text"] = $val_text;

                        // end pricerange


                        if ($merchant_parent_id != 0) {
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

                                // location hours

                                /* $Sql_lh = "SELECT * FROM location_hours where location_id =".$location_id." and day=LCASE(left(DAYNAME(now()),3))";

                                  $RS_lh = $objDB->Conn->Execute($Sql_lh); */
                                $RS_lh = $objDB->Conn->Execute("SELECT * FROM location_hours where location_id =? and day=LCASE(left(DAYNAME(now()),3))", array($location_id));
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

                                if (file_exists($image_path_main . 'street_' . $location_id . '.jpeg')) {
                                        
                                } else {
                                        $street_main_image = file_get_contents('https://maps.googleapis.com/maps/api/streetview?size=400x310&location=' . $Row['latitude'] . "," . $Row['longitude'] . '&sensor=false&key=AIzaSyBsvIV_4NNaCz9d2tSS6EeW01wIj98lmFA');
                                        $fp = fopen($image_path_main . 'street_' . $location_id . '.jpeg', 'w+');
                                        fputs($fp, $street_main_image);

                                        $image = new SimpleImage();
                                        $image->load($image_path_main . 'street_' . $location_id . '.jpeg');
                                        $image->resize(70, 70);
                                        $image->save($image_path_main . 'thumb/street_' . $location_id . '.jpeg');
                                }

                                $lp_records = array();
                                $lp_records[0]['id'] = 0;
                                $lp_records[0]['main_image'] = 'street_' . $location_id . '.jpeg';
                                $lp_records[0]['counter'] = 1;


                                $lp_records[1]['id'] = 1;
                                $lp_records[1]['main_image'] = $Row['picture'];
                                $lp_records[1]['counter'] = 2;


                                /* $Sql_lp = "SELECT * FROM location_images where location_id =".$location_id." order by image_id";

                                  $RS_lp = $objDB->Conn->Execute($Sql_lp); */
                                $RS_lp = $objDB->Conn->Execute("SELECT * FROM location_images where location_id =? order by image_id", array($location_id));

                                if ($RS_lp->RecordCount() > 0) {
                                        $count3 = 2;

                                        while ($Row_lp = $RS_lp->FetchRow()) {
                                                $lp_records[$count3] = get_field_value($Row_lp);
                                                $lp_records[$count3][counter] = $count3 + 1;
                                                $count3++;
                                        }
                                }
                                $records[0]['location_photos'] = $lp_records;

                                // location review				

                                /* $Sql_lr = "select rr.id review_id,review,reviewed_datetime,rating,is_usefull,is_notusefull,cu.firstname,cu.lastname,cu.profile_pic from review_rating rr,customer_user cu where review!='' and cu.id=rr.customer_id and location_id=".$location_id." order by reviewed_datetime desc limit 10";

                                  $RS_lr = $objDB->Conn->Execute($Sql_lr); */
                                $RS_lr = $objDB->Conn->Execute("select rr.id review_id,review,reviewed_datetime,rating,is_usefull,is_notusefull,cu.firstname,cu.lastname,cu.profile_pic from review_rating rr,customer_user cu where review!='' and cu.id=rr.customer_id and location_id=? order by reviewed_datetime desc limit 10", array($location_id));

                                if ($RS_lr->RecordCount() > 0) {
                                        $count4 = 0;
                                        $lr_records = array();

                                        while ($Row_lr = $RS_lr->FetchRow()) {
                                                $lr_records[$count4] = get_field_value($Row_lr);

                                                $lr_records[$count4]['review'] = trim(strip_tags(str_replace("&nbsp;", " ", $Row_lr['review'])));
                                                $lr_records[$count4]['reviewed_datetime'] = date('M j, Y | g:i A', strtotime($Row_lr['reviewed_datetime']));

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

                                /* $sql_rc = "select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  where  location_id = ".$location_id." group by re.rating";

                                  $RS_rc = $objDB->Conn->Execute($sql_rc); */
                                $RS_rc = $objDB->Conn->Execute("select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  where  location_id = ? group by re.rating", array($location_id));

                                if ($RS_rc->RecordCount() > 0) {
                                        $one = 0;
                                        $two = 0;
                                        $three = 0;
                                        $four = 0;
                                        $five = 0;
                                        $lrc_records = array();
										$total_ratings =0;
                                        //$RS_rc = $objDB->Conn->Execute($sql_rc);
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

                                        $array_where_loc1 = array();
                                        $array_where_loc1['id'] = $location_id;
                                        $RSlocation1 = $objDB->Show("locations", $array_where_loc1);

                                        $records[0]["location_main_total_reviews"] = $RSlocation1->fields['no_of_reviews'];
                                        $records[0]["location_main_total_ratings"] = $RSlocation1->fields['no_of_rating'];

                                        if ($RSlocation1->fields['avarage_rating'] < 1) {
                                                // echo "in .5";
                                                $class = "orange-half";
                                                $rating_title = "Poor";
                                                $rating_number = 0.5;
                                        } else if ($RSlocation1->fields['avarage_rating'] >= 1 && $RSlocation1->fields['avarage_rating'] <= 1.74) {
                                                // echo "in 1";
                                                $class = "orange-one";
                                                $rating_title = "Poor";
                                                $rating_number = 1;
                                        } else if ($RSlocation1->fields['avarage_rating'] >= 1.75 && $RSlocation1->fields['avarage_rating'] <= 2.24) {
                                                // echo "2";
                                                $class = "orange-two";
                                                $rating_title = "Fair";
                                                $rating_number = 2;
                                        } else if ($RSlocation1->fields['avarage_rating'] >= 2.25 && $RSlocation1->fields['avarage_rating'] <= 2.74) {
                                                //echo "2,5";
                                                $class = "orange-three_h";
                                                $rating_title = "Good";
                                                $rating_number = 2.5;
                                        } else if ($RSlocation1->fields['avarage_rating'] >= 2.75 && $RSlocation1->fields['avarage_rating'] <= 3.24) {
                                                //echo "3";
                                                $class = "orange-three";
                                                $rating_title = "Good";
                                                $rating_number = 3;
                                        } else if ($RSlocation1->fields['avarage_rating'] >= 3.25 && $RSlocation1->fields['avarage_rating'] <= 3.74) {
                                                //  echo "3.5";
                                                $class = "orange-three-f";
                                                $rating_title = "Very Good";
                                                $rating_number = 3.5;
                                        } else if ($RSlocation1->fields['avarage_rating'] >= 3.75 && $RSlocation1->fields['avarage_rating'] <= 4.24) {
                                                // echo "4";
                                                $class = "orange-four";
                                                $rating_title = "Very Good";
                                                $rating_number = 4;
                                        } else if ($RSlocation1->fields['avarage_rating'] >= 4.25 && $RSlocation1->fields['avarage_rating'] <= 4.74) {
                                                //  echo "4.5";
                                                $class = "orang-one-fourt";
                                                $rating_title = "Excellent";
                                                $rating_number = 4.5;
                                        } else if ($RSlocation1->fields['avarage_rating'] >= 4.75) {
                                                // echo "5";
                                                $class = "orange";
                                                $rating_title = "Excellent";
                                                $rating_number = 5;
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
                        } else {
                                /* $Sql_lh = "SELECT b.id,b.title,b.business_logo,b.category_id,b.is_walkin,a.offers_left,b.is_new,b.deal_value,b.discount,b.saving,DATE_FORMAT(b.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date from campaign_location a 
                                  INNER JOIN campaigns b on a.campaign_id=b.id
                                  INNER JOIN locations c on a.location_id=c.id
                                  where c.id=".$location_id."
                                  AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date and a.active =1 and a.active_in_future<>1 and a.offers_left>0 ";
                                  $RS_lh = $objDB->Conn->Execute($Sql_lh); */

                                $RS_lh = $objDB->Conn->Execute("SELECT b.id,b.title,b.business_logo,b.category_id,b.is_walkin,a.offers_left,b.is_new,b.deal_value,b.discount,b.saving,DATE_FORMAT(b.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date from campaign_location a 
						INNER JOIN campaigns b on a.campaign_id=b.id 
						INNER JOIN locations c on a.location_id=c.id 
						where c.id=? 
						AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date and a.active =? and a.active_in_future<>? and a.offers_left>? ", array($location_id, 1, 1, 0));

                                //echo $Sql_lh."<br/>";
                                $count2 = 0;
                                $campaign_records = array();

                                $records[$count]['total_campaigns'] = $RS_lh->RecordCount();
                                if ($RS_lh->RecordCount() > 0) {
                                        while ($Row_lh = $RS_lh->FetchRow()) {
                                                $campaign_records[$count2] = get_field_value($Row_lh);
                                                $campaign_records[$count2]["title"] = ucwords(strtolower($campaign_records[$count2]["title"]));
                                                $count2++;
                                        }
                                        $records[$count]['campaigns'] = $campaign_records;
                                }
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

function getmainmercahnt_id($id) {
        $objDB = new DB('read');
        /* $Sql = "select merchant_parent from merchant_user where id=".$id;
          $rs =$objDB->execute_query($Sql); */
        $rs = $objDB->Conn->Execute("select merchant_parent from merchant_user where id=?", array($id));

        if ($rs->fields['merchant_parent'] == 0) {
                return $id;
        } else {

                return getmainmercahnt_id($rs->fields['merchant_parent']);
                //call_user_func("get_main_merchant_id",$mainid);
        }
}

if (isset($_REQUEST['get_location_details'])) 
{
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

                        if ($Row["location_detail_display"] == "") {
                                $records[$count]["location_detail_display"] = 0;
                        }
                        if ($Row["menu_price_display"] == "") {
                                $records[$count]["menu_price_display"] = 0;
                        }

                        // location miles away

                        $from_lati1 = $_REQUEST['mycurrent_lati'];

                        $from_long1 = $_REQUEST['mycurrent_long'];

                        $to_lati1 = $Row['latitude'];

                        $to_long1 = $Row['longitude'];

                        $deal_distance = $objJSON->distance($from_lati1, $from_long1, $to_lati1, $to_long1, "M") . "Mi";
                        $records[$count]["miles_away"] = $deal_distance;

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

                        /* $Sql_lr = "select rr.id review_id,review,reviewed_datetime,rating,is_usefull,is_notusefull,cu.firstname,cu.lastname,cu.city,cu.state,cu.profile_pic from review_rating rr,customer_user cu where review!='' and cu.id=rr.customer_id and location_id=".$_REQUEST['location_id']." order by reviewed_datetime desc limit 10";

                          $RS_lr = $objDB->Conn->Execute($Sql_lr); */
                        $RS_lr = $objDB->Conn->Execute("select rr.id review_id,review,reviewed_datetime,rating,is_usefull,is_notusefull,cu.firstname,cu.lastname,cu.city,cu.state,cu.profile_pic from review_rating rr,customer_user cu where review!='' and cu.id=rr.customer_id and location_id=? order by reviewed_datetime desc limit 10", array($_REQUEST['location_id']));

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
                        $RS_rc = $objDB->Conn->Execute("select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  where  location_id = ? group by re.rating", array($_REQUEST['location_id']));

                        if ($RS_rc->RecordCount() > 0) {
                                $one = 0;
                                $two = 0;
                                $three = 0;
                                $four = 0;
                                $five = 0;
                                $lrc_records = array();
                                $total_ratings = 0;
                                
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
						$records[$count]['aboutus']=urldecode($records[$count]['aboutus']);
						$records[$count]['aboutus_short']=urldecode($records[$count]['aboutus_short']);
                        if ($Row['venue_id'] != "") {
                                $my_file = '../locu_files/locu_' . $Row['venue_id'] . '.txt';
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
                        $RS_lp = $objDB->Conn->Execute("SELECT dining 'Dining Option',reservation 'Takes Reservation',takeout 'Takeout',good_for 'Good For',pricerange 'Price Range',
							parking 'Parking',wheelchair 'Wheelchair Accessible',payment_method 'Payment method',minimum_age 'Minimum Age Restriction',pet 'Pet Allowed',
							ambience 'Ambience',attire 'Attire',noise_level 'Noise Level',wifi 'Wifi',has_tv 'Has TV',airconditioned 'Airconditioned',smoking 'Smoking',alcohol 'Alcohol',
							will_deliver 'Will Deliver',minimum_order 'Minimum Order',deliveryarea_from 'Delivery Area From',deliveryarea_to 'To',caters 'Caters',services 'Services',amenities 'Amenities' 
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
        $RS = $objDB->Conn->Execute("select rr.id review_id,review,reviewed_datetime,rating,is_usefull,is_notusefull,rr.customer_id,cu.firstname,cu.lastname,cu.city,cu.state,cu.profile_pic from review_rating rr,customer_user cu where review!='' and cu.id=rr.customer_id and location_id=? order by reviewed_datetime desc limit " . $start_value . "," . $per_page, array($_REQUEST['location_id']));

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
if (isset($_REQUEST['btnCampaignsOfMerchantLocation'])) {
        $json_array = array();
        $records = array();

        $merchant_id = $_REQUEST['merchant_id'];
        $location_id = $_REQUEST['location_id'];
	
        $ids = getallsubmercahnt_id($_REQUEST['merchant_id']);
        $ids = implode(",", $ids);
        if ($ids == "")
                $ids = $merchant_id;

        //$action=$_REQUEST['action'];
        $action = $_REQUEST['active'];

        $today_date = date("Y-m-d") . " 00:00:00";
        $Where = "";

        $merchant_array = array();
        $merchant_array['id'] = $merchant_id;
        $merchant_info = $objDB->Show("merchant_user", $merchant_array);
	
        if ($merchant_info->fields['merchant_parent'] == 0) {
                /* $Sql = "SELECT l.id location_id from locations l  
                  where (l.created_by = $merchant_id or l.created_by in ( $ids)) and l.id=".$location_id; */
                $RS = $objDB->Conn->Execute("SELECT l.id location_id from locations l  
				where (l.created_by = ? or l.created_by in ( ?)) and l.id=?", array($merchant_id, $ids, $location_id));
        } else {
                $merchant_array = array();
                $merchant_array['merchant_user_id'] = $merchant_id;
                $merchant_info1 = $objDB->Show("merchant_user_role", $merchant_array);

                /* $Sql = "SELECT l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.latitude,l.longitude
                  from locations l
                  where l.id=".$location_id; */
                $RS = $objDB->Conn->Execute("SELECT l.id location_id ,l.timezone_name, l.created_by merchant,l.location_name,l.address,l.city,l.state,l.zip,l.country,l.latitude,l.longitude
				from locations l  where l.id=?", array($location_id));
        }

        //$json_array['query'] = $Sql;
        //$RS = $objDB->Conn->Execute($Sql);
        if ($RS->RecordCount() > 0) {
                $json_array['status'] = "true";
                $json_array['total_records'] = $RS->RecordCount();
                $count = 0;
                while ($Row = $RS->FetchRow()) {
                        $records[$count] = get_field_value($Row);

                        $count2 = 0;
                        $campaign_records = array();
                        //echo $merchant_info->fields['merchant_parent']."=".$Row['location_id'];

                        /* $Sql_lh = "SELECT b.id,b.title,b.business_logo,b.deal_value,b.discount,b.saving from campaign_location a 
                          INNER JOIN campaigns b on a.campaign_id=b.id
                          INNER JOIN locations c on a.location_id=c.id
                          where c.id=".$Row['location_id']."
                          AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date and a.active =1 and a.active_in_future<>1 and a.offers_left>0 ";

                          $RS_lh = $objDB->Conn->Execute($Sql_lh); */
                        $RS_lh = $objDB->Conn->Execute("SELECT b.id,b.title,b.business_logo,b.deal_value,b.discount,b.saving from campaign_location a INNER JOIN campaigns b on a.campaign_id=b.id INNER JOIN locations c on a.location_id=c.id where c.id=? AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date and a.active =? and a.active_in_future<>? and a.offers_left>? ", array($Row['location_id'], 1, 1, 0));

                        $records[$count]['total_campaigns'] = $RS_lh->RecordCount();
                        while ($Row_lh = $RS_lh->FetchRow()) {

                                $campaign_records[$count2] = get_field_value($Row_lh);
                                $image = explode(".", $Row_lh['business_logo']);
                                //echo $image[0].".jpg";
                                $campaign_records[$count2]["business_logo"] = $image[0] . ".jpg";
                                $campaign_records[$count2]["title"] = ucwords(strtolower($campaign_records[$count2]["title"]));
                                $count2++;
                        }
                        $records[$count]['campaigns'] = $campaign_records;

                        $count++;
                }
                $json_array["records"] = $records;
        } else {
                $json_array['status'] = "false";
                $json_array['total_records'] = 0;
                $json_array["records"] = "";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
        $json = json_encode($json_array);
        echo $json;
        exit();
}
if (isset($_REQUEST['btnGetCampaign'])) {
        $json_array = array();
        $records = array();

        $campaign_id = $_REQUEST["campaign_id"];
        $location_id = $_REQUEST["location_id"];


        //$Sql = "SELECT * from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=".$campaign_id." and l.id=".$location_id;

        /* $Sql = "SELECT DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date,l.timezone_name  ,l.timezone, business_logo,discount,title,description,deal_detail_description,terms_condition,mob_img_hover,
          redeem_rewards,referral_rewards,offers_left,expiration_date,number_of_use,new_customer,campaign_tag,is_walkin,is_new,c.deal_value,c.discount,c.saving,
          latitude,longitude,address,city,state,zip,country,permalink from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=".$campaign_id." and l.id=".$location_id;

          $RS = $objDB->Conn->Execute($Sql); */
        $RS = $objDB->Conn->Execute("SELECT DATE_FORMAT(c.expiration_date, '%m/%d/%Y %H:%i:%S') expire_date,l.timezone_name  ,l.timezone, business_logo,discount,title,description,deal_detail_description,terms_condition,mob_img_hover,	redeem_rewards,referral_rewards,offers_left,expiration_date,number_of_use,new_customer,campaign_tag,is_walkin,is_new,c.deal_value,c.discount,c.saving,
		latitude,longitude,address,city,state,zip,country,permalink from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=? and l.id=?", array($campaign_id, $location_id));

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


                        //$Sql = "SELECT * from campaigns c,locations l,campaign_location cl,categories cat where c.id=cl.campaign_id and l.id=cl.location_id and cat.id=c.category_id and c.id=".$campaign_id." and l.id=".$location_id;

                        $arr1 = file(WEB_PATH . '/merchant/process_mobile.php?getlocationbusinessname=yes&l_id=' . $location_id);
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
                $json_array['campaign_end_message'] = $client_msg['campaign']['campaign_ended_message'];

                $json_array['total_records'] = 0;
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
        $json = json_encode($json_array);
        echo $json;
        exit();
}
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
if (isset($_REQUEST['get_location_menu'])) {
        $json_array = array();
        /* $sql_venue_id="select * from locations where id=".$_REQUEST['location_id']; 
          $location_venue_id=  $objDB->Conn->Execute($sql_venue_id); */
        $location_venue_id = $objDB->Conn->Execute("select * from locations where id=?", array($_REQUEST['location_id']));
        if ($location_venue_id->fields['venue_id'] != "") {
                $my_file = SERVER_PATH . '/locu_files/locu_' . $location_venue_id->fields['venue_id'] . '.txt';
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

if (isset($_REQUEST['send_gcm_message'])) {
        $json_array = array();

        /*
          $message = "the test message";
          $tickerText = "ticker text message";
          $contentTitle = "content title";
          $contentText = "content body";
         */

        $message = "";
        /*
          $tickerText = $_REQUEST['ticker_text'];
          $contentTitle = $_REQUEST['content_title'];
          $contentText = $_REQUEST['content_text'];
         */
        $redeem_point = $_REQUEST['redeem_point'];
        $business_name = urldecode($_REQUEST['business_name']);
        $notificationtype = $_REQUEST['notificationtype'];
        $customer_id = $_REQUEST['customer_id'];

        $tickerText = "You earned " . $redeem_point . " Scanflip points for your recent visit at " . $business_name . ". Rate your visit.";
        $contentTitle = $merchant_msg['redeem-deal']['Msg_gcm_title'];
        $contentText = "You earned " . $redeem_point . " Scanflip points for your recent visit at " . $business_name . ". Rate your visit.";

        $registrationId = $_REQUEST['device_id'];
        $apiKey = GCM_API_KEY;

        $response = sendNotification(
                $apiKey, array($registrationId), array('message' => $message, 'tickerText' => $tickerText, 'contentTitle' => $contentTitle, "contentText" => $contentText, "notificationType" => $notificationtype, "customer_id" => $customer_id)
        );

        //echo $response;

        $json_array['status'] = "true";
        $json_array['message'] = $response;
        $json = json_encode($json_array);
        echo $json;
        exit();
}

function sendPushNotification($devideId, $messageData) {
        // Put your device token here (without spaces):
        $deviceToken = $devideId;

        // Put your private key's passphrase here:
        $passphrase = 'scanflip';

        // Put your alert message here:
        $message = $messageData;

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

        return $result;
}

if (isset($_REQUEST['send_push_message'])) {
        $json_array = array();

        /*
          $message = "the test message";
          $tickerText = "ticker text message";
          $contentTitle = "content title";
          $contentText = "content body";
         */

        $message = "";
        /*
          $tickerText = $_REQUEST['ticker_text'];
          $contentTitle = $_REQUEST['content_title'];
          $contentText = $_REQUEST['content_text'];
         */
        $redeem_point = $_REQUEST['redeem_point'];
        $business_name = urldecode($_REQUEST['business_name']);
        $notificationtype = $_REQUEST['notificationtype'];
        $customer_id = $_REQUEST['customer_id'];

        $tickerText = "You earned " . $redeem_point . " Scanflip points for your recent visit at " . $business_name . ". Rate your visit.";
        $contentTitle = $merchant_msg['redeem-deal']['Msg_gcm_title'];
        $contentText = "You earned " . $redeem_point . " Scanflip points for your recent visit at " . $business_name . ". Rate your visit.";


        // Put your device token here (without spaces):
        $deviceToken = $_REQUEST['device_id'];

        // Put your private key's passphrase here:
        $passphrase = 'scanflip';

        // Put your alert message here:
        $message = $contentText;
        $arr = array();
        $arr['customer_id'] = $customer_id;
        $arr['notificationType'] = $notificationtype;

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
            'sound' => 'default',
            'param' => $arr
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

        $json_array['status'] = "true";
        $json_array['message'] = $result;
        $json = json_encode($json_array);
        echo $json;
        exit();
}

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

if (isset($_REQUEST['btnForgotPasswordSentEmail'])) {
        $json_array = array();

        $array_where = array();
        $array_where['email'] = $_REQUEST['email'];

        $RS = $objDB->Show("merchant_user", $array_where);

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
                $array_where['email'] = $_REQUEST['email'];
                $objDBWrt->Update($array_values, "merchant_user", $array_where);

                $mail = new PHPMailer();
                $body = "<p>Hi " . $RS->fields['firstname'] . ",<br/><br/>";
                $body .= "Changing your password is simple. Please use the link below in 24 hours<br/><br/>";
                $body .= "<a href='" . WEB_PATH . "/merchant/forgot_password.php?token=" . $token . "'>" . WEB_PATH . "/merchant/forgot_password.php?token=" . $token . "</a></p>";

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
                $json_array['message'] = "Check your e-mail.If the e-mail address you entered is associated with a merchant account in our records, you will receive an e-mail from us with instructions for resetting your password.If you don't receive this e-mail, please check your junk mail folder or visit our Help pages to contact Merchant Services for further assistance.";
                $json = json_encode($json_array);
                echo $json;
                exit();
        }
}

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

                $objDBWrt->Update($array, "customer_user", $where_clause);

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

        /* $Sql = "Select l.id,l.location_name,l.address,l.city,l.state,l.country,l.latitude,l.longitude from campaign_location cl , locations l where campaign_id = ". $campaign_id ." and l.id = cl.location_id and cl.active=1 and cl.offers_left>0" ;
          $Rs = $objDB->execute_query($Sql); */
        $Rs = $objDB->Conn->Execute("Select l.id,l.location_name,l.address,l.city,l.state,l.country,l.latitude,l.longitude from campaign_location cl , locations l where campaign_id = ? and l.id = cl.location_id and cl.active=? and cl.offers_left>?", array($campaign_id, 1, 0));
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
                                //$records[$count]['deal_distance'] = $deal_distance_from_location;

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
if (isset($_REQUEST['btn_redeem'])) {
        try {
//$objDB->Conn->StartTrans(); 
                $json_array = array();
                $used = 0;

                $barcode = $_REQUEST['txt_barcode'];
                $merchant_id = $_REQUEST['merchant_id'];
                $txt_redeem_deal_value = $_REQUEST['txt_redeem_deal_value'];

                $merchant_array = array();
                $merchant_array['id'] = $merchant_id;
                $merchant_info = $objDB->Show("merchant_user", $merchant_array);
                $merchant_parent_id = $merchant_info->fields['merchant_parent'];


                if ($barcode != "") {
                        if ($txt_redeem_deal_value != "") {
                                /* $Sql = "SELECT * FROM coupon_codes where coupon_code='".$barcode."'";			
                                  $RS = $objDB->Conn->Execute($Sql); */
                                $RS = $objDB->Conn->Execute("SELECT * FROM coupon_codes where coupon_code=?", array($barcode));
                                if ($RS->RecordCount() > 0) {
                                        if ($merchant_parent_id == 0) {

                                                $arr_ids = getallsubmercahnt_id($merchant_id);
                                                $ids = implode(",", $arr_ids);

                                                if ($ids == "")
                                                        $ids = $merchant_id;

                                                /* $Sql = "SELECT * FROM campaigns cp WHERE cp.id=".$RS->fields['customer_campaign_code']." and (cp.created_by = '$merchant_id' or cp.created_by in (".$ids."))";

                                                  $RS = $objDB->Conn->Execute($Sql); */
                                                $RS = $objDB->Conn->Execute("SELECT * FROM campaigns cp WHERE cp.id=? and (cp.created_by =? or cp.created_by in (?))", array($RS->fields['customer_campaign_code'], $merchant_id, $ids));

                                                $arr = file(WEB_PATH . '/merchant/process.php?get_point_package=yes');
                                                if (trim($arr[0]) == "") {
                                                        unset($arr[0]);
                                                        $arr = array_values($arr);
                                                }
                                                $json = json_decode($arr[0]);
                                                $total_records = $json->total_records;
                                                $records_array = $json->records;
                                                if ($total_records > 0) {
                                                        foreach ($records_array as $Row) {

                                                                $price = $Row->price;
                                                                $point_ = $Row->points;
                                                                $p = (1 * $price) / $point_;
                                                        }
                                                }
                                                $B4 = (1 * $price) / $point_;
                                                if ($RS->RecordCount() <= 0) {
                                                        $json_array['status'] = "false";
                                                        //$json_array['message'] = "This coupon is not for this store.";

                                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_different_merchant"];

                                                        $json = json_encode($json_array);
                                                        //     $objDB->Conn->CompleteTrans(); 
                                                        echo $json;
                                                        return $json;
                                                        exit;
                                                }

                                                // 15 12 2014 start check coupon code is for assigned location or not

                                                /* $Sql = "SELECT c.*,cp.redeem_rewards ,cp.transaction_fees, cp.referral_rewards ,cp.block_point,cp.max_no_sharing 
                                                  FROM coupon_codes c, campaigns cp WHERE c.active =1 and
                                                  c.coupon_code='".$barcode."' and cp.id=c.customer_campaign_code  and c.location_id in
                                                  (select id from locations where active =1 )  and  cp.id in
                                                  (select cl.campaign_id from campaign_location cl , merchant_user mur where
                                                  mur.id = ".$merchant_id ." and cl.location_id = mur.redeem_location and c.location_id=mur.redeem_location) ";


                                                  $RS = $objDB->Conn->Execute($Sql); */
                                                $RS = $objDB->Conn->Execute("SELECT c.*,cp.redeem_rewards ,cp.transaction_fees, cp.referral_rewards ,cp.block_point,cp.max_no_sharing FROM coupon_codes c, campaigns cp WHERE c.active =? and 
							c.coupon_code=? and cp.id=c.customer_campaign_code  and c.location_id in
								(select id from locations where active =1 )  and  cp.id in 
								(select cl.campaign_id from campaign_location cl , merchant_user mur where
								mur.id = ? and cl.location_id = mur.redeem_location and c.location_id=mur.redeem_location) ", array(1, $barcode, $merchant_id));


                                                if ($RS->RecordCount() <= 0) {
                                                        $json_array['status'] = "false";
                                                        //$json_array['message'] = "This coupon is not for this store.";

                                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_different_location"];

                                                        $json = json_encode($json_array);
                                                        $objDB->Conn->CompleteTrans();
                                                        echo $json;
                                                        return $json;
                                                        exit;
                                                }

                                                // 15 12 2014 end check coupon code is for assigned location or not
                                        } else {
                                                //$Sql = "SELECT * FROM campaigns cp WHERE cp.id=".$RS->fields['customer_campaign_code']." and cp.created_by = '$merchant_id'";
                                                //04 10 2013

                                                /* $Sql = "SELECT c.*,cp.redeem_rewards ,cp.transaction_fees, cp.referral_rewards ,cp.block_point,cp.max_no_sharing 
                                                  FROM coupon_codes c, campaigns cp WHERE c.active =1 and
                                                  c.coupon_code='".$_REQUEST['txt_barcode']."' and cp.id=c.customer_campaign_code  and c.location_id in
                                                  (select id from locations where active =1 )  and  cp.id in
                                                  (select cl.campaign_id from campaign_location cl , merchant_user_role mur where
                                                  mur.merchant_user_id = ".$merchant_id." and cl.location_id = mur.location_access and c.location_id=mur.location_access) ";


                                                  //04 10 2013

                                                  $RS = $objDB->Conn->Execute($Sql); */
                                                $RS = $objDB->Conn->Execute("SELECT c.*,cp.redeem_rewards ,cp.transaction_fees, cp.referral_rewards ,cp.block_point,cp.max_no_sharing 
							FROM coupon_codes c, campaigns cp WHERE c.active =? and 
							c.coupon_code=? and cp.id=c.customer_campaign_code  and c.location_id in
								(select id from locations where active =1 )  and  cp.id in 
								(select cl.campaign_id from campaign_location cl , merchant_user_role mur where
								mur.merchant_user_id =? and cl.location_id = mur.location_access and c.location_id=mur.location_access) ", array(1, $_REQUEST['txt_barcode'], $merchant_id));

                                                if ($RS->RecordCount() <= 0) {
                                                        $json_array['status'] = "false";
                                                        //$json_array['message'] = "This coupon is not for this store.";

                                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_different_location"];

                                                        $json = json_encode($json_array);
                                                        // $objDB->Conn->CompleteTrans(); 
                                                        echo $json;
                                                        return $json;
                                                        exit;
                                                }
                                        }
                                        if ($merchant_parent_id == 0) {
                                                // to redeem all child coupon
                                                $arr_ids = getallsubmercahnt_id($merchant_id);
                                                $ids = implode(",", $arr_ids);

                                                if ($ids == "")
                                                        $ids = $merchant_id;
                                                // 	to redeem all child coupon
                                                //$Sql = "SELECT COUNT(*) as total FROM campaigns WHERE start_date<='$date_f' AND expiration_date >='$date_f' AND visible='1' AND id =".$RS->fields['customer_campaign_code'] ." AND (created_by = '$merchant_id' or created_by in ( select id from merchant_user where merchant_parent = ". $merchant_id ." ))";
                                                //$Sql = "SELECT c.*,cp.redeem_rewards , cp.referral_rewards ,cp.block_point,cp.max_no_sharing  FROM coupon_codes c, campaigns cp WHERE c.active =1 and c.coupon_code='".$_REQUEST['txt_barcode']."' and cp.id=c.customer_campaign_code and c.location_id in (select id from locations where active =1 ) and (cp.created_by = '$merchant_id' or cp.created_by in ( select id from merchant_user where merchant_parent = ". $merchant_id ." ))";
                                                // change query for all child 
                                                 $Sql = "SELECT c.*,cp.redeem_rewards , cp.referral_rewards ,cp.block_point,cp.max_no_sharing  FROM coupon_codes c, campaigns cp WHERE c.active =1 and c.coupon_code='".$_REQUEST['txt_barcode']."' and cp.id=c.customer_campaign_code and c.location_id in (select id from locations where active =1 ) and (cp.created_by = '$merchant_id' or cp.created_by in (".$ids."))"; 
                                                // change query for all child 
                                                
                                        }
                                        else {


                                                $Where_miles = "";

                                                 $Sql = "SELECT c.*,cp.redeem_rewards ,cp.transaction_fees, cp.referral_rewards ,cp.block_point,cp.max_no_sharing 
                                                  FROM coupon_codes c, campaigns cp WHERE c.active =1 and
                                                  c.coupon_code='".$_REQUEST['txt_barcode']."' and cp.id=c.customer_campaign_code  and c.location_id in
                                                  (select id from locations where active =1 )  and ( cp.id in
                                                  (select cl.campaign_id from campaign_location cl , merchant_user_role mur where
                                                  mur.merchant_user_id = ".$merchant_id." and cl.location_id = mur.location_access ) ".$Where_miles." )"; 
                                               
                                        }

                                        $RS = $objDB->Conn->Execute($Sql);
                                        if ($RS->RecordCount() <= 0) {
                                                $json_array['status'] = "false";
                                                //$json_array['message'] = "This coupon is not for this store.";
                                                $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_voucher_code_not_valid"];
                                                $json = json_encode($json_array);
                                                // $objDB->Conn->CompleteTrans(); 
                                                echo $json;
                                                return $json;
                                        } else {
                                                $array_loc['id'] = $RS->fields['location_id'];
                                                $RS_location = $objDB->Show("locations", $array_loc);
                                                $time_zone = $RS_location->fields['timezone_name'];
                                                date_default_timezone_set($time_zone);

                                                //$dt_wh = " CONVERT_TZ(NOW(),'+00:00','+00:00') BETWEEN CONVERT_TZ(start_date,'+00:00',SUBSTR(timezone,1, POSITION(',' IN timezone)-1)) AND CONVERT_TZ(expiration_date,'+00:00',SUBSTR(timezone,1, POSITION(',' IN timezone)-1)) ";
                                                //$date_wh = "AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','+00:00') BETWEEN CONVERT_TZ(c.start_date,'+00:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) AND CONVERT_TZ(c.expiration_date,'+00:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))"; 
                                                $date_wh = "AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date   and cl.active_in_future<>1";
                                                $date_f = date("Y-m-d H:i:s");
                                                $Sql = "SELECT COUNT(*) as total FROM campaigns c , locations l, campaign_location cl WHERE cl.location_id= l.id AND c.id =cl.campaign_id  " . $date_wh . " AND visible='1' AND c.id =" . $RS->fields['customer_campaign_code'] . " and l.id =" . $RS->fields['location_id'];

                                                $RS_t = $objDB->Conn->Execute($Sql);
                                                if ($RS_t->fields['total'] == 0) {
                                                        $json_array['status'] = "false";
                                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_campaign_expire"];
                                                        $json = json_encode($json_array);
                                                        //  $objDB->Conn->CompleteTrans(); 
                                                        echo $json;
                                                        return $json;
                                                } else {
                                                        $sharing_msg = ".";
                                                        $Sql = "SELECT number_of_use, redeem_rewards FROM campaigns WHERE id =".$RS->fields['customer_campaign_code'];
                                                        $RS_camp_detail = $objDB->Conn->Execute($Sql); 
                                                        

                                                        //-- start for point deduction
                                                        $array_where_user = array();
                                                        $array_where_user['id'] = $merchant_id;
                                                        $RS_merchant_User = $objDB->Show("merchant_user", $array_where_user);
                                                        $m_parent = $RS_merchant_User->fields['merchant_parent'];
                                                        //if($RS_User->fields['merchant_parent'] == 0)
                                                        if ($RS_merchant_User->fields['merchant_parent'] == 0) {
                                                                $merchant_id = $merchant_id;
                                                        } else {
                                                                $merchant_id = $RS_merchant_User->fields['merchant_parent'];
                                                        }

                                                        $array_where_user = array();
                                                        $array_where_user['id'] = $merchant_id;
                                                        $RS_merchant_User = $objDB->Show("merchant_user", $array_where_user);
                                                        //-- End of point deduction
                                                        //        echo "=================".$RS_camp_detail->fields['number_of_use']."========================";
                                                        if ($RS_camp_detail->fields['number_of_use'] == 1) {
                                                                 $Sql = "SELECT * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']. " and location_id=".$RS->fields['location_id'];
                                                                  $RS_user_exist = $objDB->Conn->Execute($Sql); 
                                                                

                                                                if ($RS_user_exist->RecordCount() == 0) {
                                                                        $st = true;
                                                                        /* Don't give point if max nof sharing rich */

                                                                         $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$RS->fields['customer_campaign_code']." and referred_customer_id<>0 and referral_reward<>0";
                                                                          // echo $Sql_shared ;
                                                                          $RS_shared = $objDB->Conn->Execute($Sql_shared); 
                                                                        

                                                                        if ($RS_shared->RecordCount() < $RS->fields['max_no_sharing']) {

                                                                                $Sql = "SELECT * FROM reward_user WHERE referred_customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." AND referral_reward = 0  AND location_id=".$RS->fields['location_id'];


                                                                                  $RS_referral_customer = $objDB->Conn->Execute($Sql); 
                                                                                

                                                                                if ($RS_referral_customer->RecordCount() != 0) {
                                                                                        $where = array();
                                                                                        $where['referred_customer_id'] = $RS->fields['customer_id'];
                                                                                        $where['campaign_id'] = $RS->fields['customer_campaign_code'];
                                                                                        $where['referral_reward'] = 0;
                                                                                        $where['location_id'] = $RS->fields['location_id'];

                                                                                        $u_array = array();
                                                                                        if (($RS->fields['block_point'] - $RS->fields['redeem_rewards']) >= $RS->fields['referral_rewards']) {
                                                                                                //$u_array['referral_reward'] = $RS->fields['referral_rewards'];
                                                                                                $sharing_msg = " and referral customer will earn " . $RS->fields['referral_rewards'] . " scanflip points.";
                                                                                        } else {
                                                                                                $sharing_msg = ".";
                                                                                        }
                                                                                }
                                                                        }
                                                                        /* Don't give point if max nof sharing rich */
                                                                        /*
                                                                          if($RS->fields['block_point'] < $RS_camp_detail->fields['redeem_rewards'])
                                                                          {
                                                                          $json_array['point_message'] = "Customer will earn O points.";
                                                                          }
                                                                          else
                                                                          {
                                                                          $json_array['point_message'] = "Customer will earn ".$RS_camp_detail->fields['redeem_rewards']." scanflip points".$sharing_msg;
                                                                          }
                                                                          $json_array['status'] = "true";
                                                                          $json_array['id'] = $RS->fields['customer_campaign_code'];
                                                                          $json_array['message'] = "";
                                                                          $json = json_encode($json_array);
                                                                          echo $json;
                                                                          return $json;
                                                                         */
                                                                } else {
                                                                        $json_array['status'] = "false";
                                                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_campaign_already_redeemed"];
                                                                        $json = json_encode($json_array);
                                                                        // $objDB->Conn->CompleteTrans(); 
                                                                        echo $json;
                                                                        return $json;
                                                                }
                                                        } else if ($RS_camp_detail->fields['number_of_use'] == 2) {
                                                                //                                    $Sql_2_use =  "SELECT * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                //                                    $RS_2_use = $objDB->Conn->Execute($Sql_2_use);
                                                                //                                    if($RS_2_use->RecordCount() >0){
                                                                 $location_max_sql = "Select num_activation_code , offers_left from campaign_location where  campaign_id=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                  $location_max = $objDB->Conn->Execute($location_max_sql); 
                                                                

                                                                $max_coupon = $location_max->fields['num_activation_code'];
                                                                $o_left = $location_max->fields['offers_left'];
                                                                //    }
                                                                $flag_redeem = 1;
                                                                // $Sql_2_use =  "SELECT * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                 $Sql_2_use =  "Select * from coupon_redeem where coupon_id = (select id from coupon_codes where customer_id=".$RS->fields['customer_id']." and customer_campaign_code=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id']." )";
                                                                  $RS_2_use = $objDB->Conn->Execute($Sql_2_use); 
                                                                
                                                                if ($RS_2_use->RecordCount() <= 0) {
                                                                        $flag_redeem = 0;
                                                                }
                                                                //                                while($loopcouponid=$Sql_2_use->FetchRow())
                                                                //                                {
                                                                //                                    $r_coupon_id = $loopcouponid['coupon_id'];
                                                                //                                }
                                                                if ($o_left > 0 || $flag_redeem == 0) {

                                                                        //	$Sql = "SELECT * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code'] ." and  DATE_FORMAT(reward_date, '%Y-%m-%d')  =  '".date('Y-m-d')."'  and location_id=".$RS->fields['location_id'];
                                                                         $Sql = "Select * from coupon_redeem where coupon_id = (select id from coupon_codes where customer_id=".$RS->fields['customer_id']." and customer_campaign_code=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id']." )  and  DATE_FORMAT(redeem_date, '%Y-%m-%d')  =  '".date('Y-m-d')."'  ";

                                                                          $RS_user_exist = $objDB->Conn->Execute($Sql); 
                                                                        


                                                                        if ($RS_user_exist->RecordCount() == 0) {
                                                                                $st = true;
                                                                                /* Don't give point if max nof sharing rich */

                                                                                 $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$RS->fields['customer_campaign_code']." and referred_customer_id<>0 and referral_reward<>0";

                                                                                  $RS_shared = $objDB->Conn->Execute($Sql_shared); 
                                                                                

                                                                                if ($RS_shared->RecordCount() < $RS->fields['max_no_sharing']) {

                                                                                         $Sql = "SELECT * FROM reward_user WHERE referred_customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." AND referral_reward = 0  AND location_id=".$RS->fields['location_id'];

                                                                                          $RS_referral_customer = $objDB->Conn->Execute($Sql); 
                                                                                        

                                                                                        if ($RS_referral_customer->RecordCount() != 0) {
                                                                                                $where = array();
                                                                                                $where['referred_customer_id'] = $RS->fields['customer_id'];
                                                                                                $where['campaign_id'] = $RS->fields['customer_campaign_code'];
                                                                                                $where['referral_reward'] = 0;
                                                                                                $where['location_id'] = $RS->fields['location_id'];

                                                                                                $u_array = array();
                                                                                                if (($RS->fields['block_point'] - $RS->fields['redeem_rewards']) >= $RS->fields['referral_rewards']) {
                                                                                                        //$u_array['referral_reward'] = $RS->fields['referral_rewards'];
                                                                                                        $sharing_msg = " and referral customer will earn " . $RS->fields['referral_rewards'] . " scanflip points.";
                                                                                                } else {
                                                                                                        $sharing_msg = ".";
                                                                                                }
                                                                                        }
                                                                                }
                                                                                /* Don't give point if max nof sharing rich */
                                                                                /*
                                                                                  if($RS->fields['block_point'] < $RS_camp_detail->fields['redeem_rewards'])
                                                                                  {
                                                                                  $json_array['point_message'] = "Customer will earn O scanflip points.";
                                                                                  }
                                                                                  else
                                                                                  {
                                                                                  $json_array['point_message'] = "Customer will earn ".$RS_camp_detail->fields['redeem_rewards']." scanflip points".$sharing_msg;
                                                                                  }
                                                                                  $json_array['status'] = "true";
                                                                                  $json_array['id'] = $RS->fields['customer_campaign_code'];
                                                                                  $json_array['message'] = "";
                                                                                  $json = json_encode($json_array);
                                                                                  echo $json;
                                                                                  return $json;
                                                                                 */
                                                                        } else {
                                                                                $msg = "Sorry!  This voucher is already redeemed today by customer at " . date("g:i A", strtotime($RS_user_exist->fields['redeem_date'])) . " . Offer Limit : 1 per customer per day";
                                                                                $json_array['status'] = "false";
                                                                                $json_array['message'] = $msg;
                                                                                $json = json_encode($json_array);
                                                                                //  $objDB->Conn->CompleteTrans();
                                                                                echo $json;
                                                                                return $json;
                                                                        }
                                                                } else {
                                                                        $json_array['status'] = "false";
                                                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_no_offer_available"];

                                                                        $json = json_encode($json_array);
                                                                        //  $objDB->Conn->CompleteTrans();
                                                                        echo $json;
                                                                        return $json;
                                                                }
                                                        } else if ($RS_camp_detail->fields['number_of_use'] == 3) {
                                                                 $location_max_sql = "Select num_activation_code , offers_left from campaign_location where  campaign_id=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                  $location_max = $objDB->Conn->Execute($location_max_sql); 
                                                                

                                                                $max_coupon = $location_max->fields['num_activation_code'];
                                                                $o_left = $location_max->fields['offers_left'];
                                                                //    }
                                                                $flag_redeem = 1;
                                                                //     $Sql_2_use =  "SELECT * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                 $Sql_2_use =  "Select * from coupon_redeem where coupon_id = (select id from coupon_codes where customer_id=".$RS->fields['customer_id']." and customer_campaign_code=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id']." )";
                                                                  $RS_2_use = $objDB->Conn->Execute($Sql_2_use); 
                                                                
                                                                if ($RS_2_use->RecordCount() <= 0) {
                                                                        $flag_redeem = 0;
                                                                }
                                                                if ($o_left > 0 || $flag_redeem == 0) {
                                                                        $st = true;
                                                                        /* Don't give point if max nof sharing rich */

                                                                         $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$RS->fields['customer_campaign_code']." and referred_customer_id<>0 and referral_reward<>0";

                                                                          $RS_shared = $objDB->Conn->Execute($Sql_shared); 
                                                                        

                                                                        if ($RS_shared->RecordCount() < $RS->fields['max_no_sharing']) {

                                                                                 $Sql = "SELECT * FROM reward_user WHERE referred_customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." AND referral_reward = 0  AND location_id=".$RS->fields['location_id'];					
                                                                                  $RS_referral_customer = $objDB->Conn->Execute($Sql); 
                                                                                

                                                                                if ($RS_referral_customer->RecordCount() != 0) {
                                                                                        $where = array();
                                                                                        $where['referred_customer_id'] = $RS->fields['customer_id'];
                                                                                        $where['campaign_id'] = $RS->fields['customer_campaign_code'];
                                                                                        $where['referral_reward'] = 0;
                                                                                        $where['location_id'] = $RS->fields['location_id'];

                                                                                        $u_array = array();
                                                                                        if (($RS->fields['block_point'] - $RS->fields['redeem_rewards']) >= $RS->fields['referral_rewards']) {
                                                                                                //$u_array['referral_reward'] = $RS->fields['referral_rewards'];
                                                                                                $sharing_msg = " and referral customer will earn " . $RS->fields['referral_rewards'] . " scanflip points.";
                                                                                        } else {
                                                                                                $sharing_msg = ".";
                                                                                        }
                                                                                }
                                                                        }
                                                                        /* Don't give point if max nof sharing rich */
                                                                        /*
                                                                          if($RS->fields['block_point'] < $RS_camp_detail->fields['redeem_rewards'])
                                                                          {
                                                                          $json_array['point_message'] = "Customer will earn O scanflip points.";
                                                                          }
                                                                          else
                                                                          {
                                                                          $json_array['point_message'] = "Customer will earn ".$RS_camp_detail->fields['redeem_rewards']." scanflip points".$sharing_msg ;
                                                                          }

                                                                          $json_array['status'] = "true";
                                                                          $json_array['loginstatus'] = "true";
                                                                          $json_array['id'] = $RS->fields['customer_campaign_code'];
                                                                          $json_array['message'] = "";
                                                                          $json = json_encode($json_array);
                                                                          echo $json;
                                                                          return $json;
                                                                         */
                                                                } else {
                                                                        $json_array['loginstatus'] = "true";
                                                                        $json_array['status'] = "false";
                                                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_no_offer_available"];

                                                                        $json = json_encode($json_array);
                                                                        //   $objDB->Conn->CompleteTrans();
                                                                        echo $json;
                                                                        return $json;
                                                                }
                                                        }
                                                }
                                        }

                                        $array_loc['id'] = $RS->fields['location_id'];
                                        $RS_location = $objDB->Show("locations", $array_loc);
                                        $time_zone = $RS_location->fields['timezone_name'];
                                        date_default_timezone_set($time_zone);

                                        if ($merchant_parent_id == 0) {

                                                // to redeem all child coupon
                                                $arr_ids = getallsubmercahnt_id($merchant_id);
                                                $ids = implode(",", $arr_ids);
                                                if ($ids == "")
                                                        $ids = $merchant_id;
                                                // 	to redeem all child coupon
                                                //$Sql = "SELECT c.*,cp.redeem_rewards , cp.referral_rewards ,cp.max_no_sharing ,cp.block_point  FROM coupon_codes c, campaigns cp WHERE c.active =1 and  c.coupon_code='".$_REQUEST['hdn_coupon_code']."' and cp.id=c.customer_campaign_code  and c.location_id in (select id from locations where active =1 ) and (cp.created_by = '$merchant_id' or cp.created_by in ( select id from merchant_user where merchant_parent = ". $merchant_id ." ))";
                                                // change query for all child 
                                                 $Sql = "SELECT c.*,cp.redeem_rewards ,cp.no_of_shared, cp.transaction_fees ,cp.referral_rewards ,cp.max_no_sharing ,cp.block_point 
                                                  FROM coupon_codes c, campaigns cp WHERE c.active =1 and  c.coupon_code='".$barcode."'
                                                  and cp.id=c.customer_campaign_code  and c.location_id in (select l.id from locations l where l.active =1 )
                                                  and (cp.created_by = '$merchant_id' or cp.created_by in (".$ids."))"; 
                                                // change query for all child 
                                                
                                        }
                                        else {

                                                $Where_miles = "";
                                                 $Sql = "SELECT c.*,cp.redeem_rewards ,cp.no_of_shared,cp.transaction_fees , cp.referral_rewards  ,cp.max_no_sharing ,cp.block_point 
                                                  FROM coupon_codes c, campaigns cp WHERE c.active =1 and c.coupon_code='".$barcode."'
                                                  and cp.id=c.customer_campaign_code and ( c.location_id in (select l.id from locations l where l.active =1  )
                                                  and cp.id in (select cl.campaign_id from
                                                  campaign_location cl , merchant_user_role mur where mur.merchant_user_id = ".$_REQUEST['merchant_id']."
                                                  and cl.location_id = mur.location_access ) ".$Where_miles." )"; 

                                                
                                        }
                                        //echo $Sql;
                                        $RS = $objDB->Conn->Execute($Sql);
                                        if ($RS->RecordCount() <= 0) {
                                                $json_array['status'] = "false";
                                                $json_array['message'] = "This coupon is not for this store";
                                                $json = json_encode($json_array);
                                                $objDB->Conn->CompleteTrans();
                                                echo $json;
                                                return $json;
                                        } else {
                                                $redeem_deal_value = 0;
                                                if (($RS->fields['deal_value'] - $RS->fields['saving']) > $_REQUEST['txt_redeem_deal_value']) {
                                                        $redeem_deal_value = $RS->fields['deal_value'] - $RS->fields['saving'];
                                                } else {
                                                        $redeem_deal_value = $_REQUEST['txt_redeem_deal_value'];
                                                }

                                                // 11/08/2014 insert redeemption fee if enable in package

                                                $redeemption_fee = 0;

                                                $discounted_coupon_value = $RS->fields['deal_value'] - $RS->fields['saving'];
                                                $array_where_mb = array();
                                                $array_where_mb['merchant_id'] = $_REQUEST['merchant_id'];
                                                $RS_mb = $objDB->Show("merchant_billing", $array_where_mb);
                                                $array_where_bp = array();
                                                $array_where_bp['id'] = $RS_mb->fields['pack_id'];
                                                $RS_bp = $objDB->Show("billing_packages", $array_where_bp);
                                                if ($RS_bp->fields['enable_coupon_redeemption_fee'] == 1) {
                                                        $Sql_r_f_c = "SELECT * FROM redeemption_fee_charge";
                                                        $Rs_r_f_c = $objDB->Conn->Execute($Sql_r_f_c);
                                                        $count = 0;
                                                        while ($Row_r_f_c = $Rs_r_f_c->FetchRow()) {
                                                                //echo $discounted_coupon_value.">=".$Row_r_f_c['start_value']." && ".$discounted_coupon_value."<=".$Row_r_f_c['end_value'];
                                                                if ($discounted_coupon_value >= $Row_r_f_c['start_value'] && $discounted_coupon_value <= $Row_r_f_c['end_value']) {
                                                                        if ($Row_r_f_c['type'] == "amount") {
                                                                                //echo "amount";
                                                                                $redeemption_fee = $Row_r_f_c['amount_value'];
                                                                        } else {
                                                                                //echo "percentage";
                                                                                $redeemption_fee = ($discounted_coupon_value * $Row_r_f_c['amount_value']) / 100;
                                                                        }
                                                                }
                                                        }
                                                }
                                                //echo "Discounted coupon value : ".$discounted_coupon_value;	
                                                //echo " Redeemption fee : ".$redeemption_fee;
                                                //exit();
                                                // 11/08/2014 


                                                /*
                                                  echo "deal value = ".$RS->fields['deal_value']." ";
                                                  echo "saving = ".$RS->fields['saving']." ";
                                                  echo "sales value = ".$_REQUEST['txt_redeem_deal_value']." ";
                                                  echo "redeem value = ".$redeem_deal_value;
                                                  exit();
                                                 */
                                                //			$dt_wh = " CONVERT_TZ(NOW(),'+00:00','+00:00') BETWEEN CONVERT_TZ(start_date,'+00:00',SUBSTR(timezone,1, POSITION(',' IN timezone)-1)) AND CONVERT_TZ(expiration_date,'+00:00',SUBSTR(timezone,1, POSITION(',' IN timezone)-1)) ";
                                                //			$date_f = date("Y-m-d H:i:s");
                                                //			$Sql = "SELECT COUNT(*) as total FROM campaigns WHERE ".$dt_wh." AND visible='1' AND id =".$RS->fields['customer_campaign_code'];
                                                //$date_wh = "AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date"; 
                                                $date_wh = "AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date  and cl.active_in_future<>1";
                                                //  $dt_wh = "AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date and a.active =1 and a.active_in_future<>1";
                                                $date_f = date("Y-m-d H:i:s");
                                                 $Sql = "SELECT COUNT(*) as total FROM campaigns c , locations l, campaign_location cl WHERE cl.location_id= l.id AND c.id =cl.campaign_id  ".$date_wh ." AND visible='1' AND c.id =".$RS->fields['customer_campaign_code']." and l.id =".$RS->fields['location_id'];

                                                  $RS_t = $objDB->Conn->Execute($Sql); 
                                                

                                                if ($RS_t->fields['total'] == 0) {
                                                        
                                                } else {
                                                         $Sql = "SELECT number_of_use FROM campaigns WHERE id =".$RS->fields['customer_campaign_code'];
                                                          $RS_camp_detail = $objDB->Conn->Execute($Sql); 
                                                        

                                                        $flag = true;
                                                        if ($RS_camp_detail->fields['number_of_use'] == 1) {
                                                                //$Sql = "SELECT * FROM reward_user WHERE referred_customer_id = 0 and  customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code'] ." and location_id=".$RS->fields['location_id'];
                                                                $Sql = "Select * from coupon_redeem where coupon_id = (select id from coupon_codes where customer_id=" . $RS->fields['customer_id'] . " and customer_campaign_code=" . $RS->fields['customer_campaign_code'] . " and location_id=" . $RS->fields['location_id'] . " )";
                                                        } else if ($RS_camp_detail->fields['number_of_use'] == 2) {

                                                                //$Sql = "SELECT * FROM reward_user WHERE referred_customer_id = 0 and  customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code'] ." and  DATE_FORMAT(reward_date, '%Y-%m-%d')  =  '".date('Y-m-d')."'  and location_id=".$RS->fields['location_id'];
                                                                $Sql = "Select * from coupon_redeem where coupon_id = (select id from coupon_codes where customer_id=" . $RS->fields['customer_id'] . " and customer_campaign_code=" . $RS->fields['customer_campaign_code'] . " and location_id=" . $RS->fields['location_id'] . " )  and  DATE_FORMAT(redeem_date, '%Y-%m-%d')  =  '" . date('Y-m-d') . "'  ";
                                                        } else if ($RS_camp_detail->fields['number_of_use'] == 3) {
                                                                $flag = false;
                                                        }
                                                         $SQl_coupon_id = "select id from coupon_codes where customer_id=".$RS->fields['customer_id']." and customer_campaign_code=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id']." ";

                                                          $Rs_coupon_id =  $objDB->Conn->Execute($SQl_coupon_id); 
                                                        

                                                        $generated_coupon_id = $Rs_coupon_id->fields['id'];

                                                        if ($flag) {

                                                                 $location_max_sql = "Select num_activation_code , offers_left, used_offers from campaign_location where  campaign_id=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                  $location_max = $objDB->Conn->Execute($location_max_sql); 
                                                                
                                                                $max_coupon = $location_max->fields['num_activation_code'];
                                                                $o_left = $location_max->fields['offers_left'];
                                                                $used_offer = $location_max->fields['used_offers'];
                                                                //    }
                                                                $flag_redeem = 1;
                                                                //  $Sql_2_use =  "SELECT * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                 $Sql_2_use =  "Select * from coupon_redeem where coupon_id = (select id from coupon_codes where customer_id=".$RS->fields['customer_id']." and customer_campaign_code=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id']." )";

                                                                  $RS_2_use = $objDB->Conn->Execute($Sql_2_use); 
                                                                
                                                                if ($RS_2_use->RecordCount() <= 0) {
                                                                        $flag_redeem = 0;
                                                                }

                                                                if ($o_left > 0 || $flag_redeem == 0) {

                                                                        //  $Sql_2_use =  "SELECT * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                         $Sql_2_use =  "Select * from coupon_redeem where coupon_id = (select id from coupon_codes where customer_id=".$RS->fields['customer_id']." and customer_campaign_code=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id']." )";

                                                                          $RS_2_use = $objDB->Conn->Execute($Sql_2_use); 
                                                                        

                                                                        if ($RS_2_use->RecordCount() > 0) {
                                                                                $used = 1;
                                                                        }
                                                                        $RS_user_exist = $objDB->Conn->Execute($Sql);
                                                                        

                                                                        if ($RS_user_exist->RecordCount() == 0) {

                                                                                $deduct_points = 0;
                                                                                //-- start for point deduction
                                                                                $array_where_user = array();
                                                                                $array_where_user['id'] = $merchant_id;
                                                                                $RS_User = $objDB->Show("merchant_user", $array_where_user);
                                                                                $m_parent = $RS_User->fields['merchant_parent'];
                                                                                if ($RS_User->fields['merchant_parent'] == 0) {
                                                                                        $merchant_id = $merchant_id;
                                                                                } else {
                                                                                        $merchant_id = $RS_User->fields['merchant_parent'];
                                                                                }

                                                                                $array_where_user = array();
                                                                                $array_where_user['id'] = $merchant_id;
                                                                                $RS_User = $objDB->Show("merchant_user", $array_where_user);
                                                                                //-- End of point deduction
                                                                                /*
                                                                                  if($RS_User->fields['available_point'] < 100)
                                                                                  {
                                                                                  $mail = new PHPMailer();
                                                                                  $body = "<p>Dear Scanflip merchant user,</p>";
                                                                                  $body .= "<p> We are informing u that your points about to finish. So please to get scanflip functionality benefits renew your package as early as possible </p>";
                                                                                  $mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
                                                                                  $mail->AddAddress($RS_User->fields['email']);
                                                                                  $mail->From = "no-reply@scanflip.com";
                                                                                  $mail->FromName = "ScanFlip Support";
                                                                                  $mail->Subject    = "ScanFlip Subscription";
                                                                                  $mail->MsgHTML($body);
                                                                                  $mail->Send();
                                                                                  }
                                                                                 */
                                                                                $redeem_array = array();
                                                                                $redeem_array['customer_id'] = $RS->fields['customer_id'];
                                                                                $redeem_array['campaign_id'] = $RS->fields['customer_campaign_code'];
                                                                                if ($RS->fields['block_point'] >= ($RS->fields['redeem_rewards'] - $RS->fields['transaction_fees'])) {
                                                                                        $redeem_array['earned_reward'] = $RS->fields['redeem_rewards'];
                                                                                        $giving_points = $RS->fields['redeem_rewards'];
                                                                                        $deduct_points = $RS->fields['block_point'] - $RS->fields['redeem_rewards'] - $RS->fields['transaction_fees'];
                                                                                } else {
                                                                                        $redeem_array['earned_reward'] = 0;
                                                                                        $giving_points = 0;
                                                                                        $deduct_points = $RS->fields['block_point'];
                                                                                }
                                                                                //					$Sql = "Update campaigns set block_point =".$deduct_points." where id=".$RS->fields['customer_campaign_code'];
                                                                                //						
                                                                                //						$objDB->Conn->Execute($Sql);
                                                                                if ($flag_redeem == 0) {

                                                                                        // insert here //
                                                                                        $redeem_array['referral_reward'] = 0;
                                                                                        $redeem_array['referred_customer_id'] = 0;
                                                                                        $redeem_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                        $redeem_array['coupon_code_id'] = $RS->fields['id'];
                                                                                        $redeem_array['location_id'] = $RS->fields['location_id'];
                                                                                        $redeem_array['redeem_deal_value'] = $redeem_deal_value;
                                                                                        $objDBWrt->Insert($redeem_array, "reward_user");
                                                                                } else {
                                                                                        // insert here //
                                                                                         $sql_get_earned_reward = "select * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                                          $rs_get_earned_reward =$objDB->Conn->Execute($sql_get_earned_reward); 
                                                                                        

                                                                                        $total_earned_reward = $rs_get_earned_reward->fields['earned_reward'];
                                                                                         $up_sql ="Update reward_user set earned_reward =".($total_earned_reward + $giving_points)."  where referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                                          $objDB->Conn->Execute($up_sql); 
                                                                                        
                                                                                }

                                                                                $coupon_redeem_array = array();



                                                                                $coupon_redeem_array['coupon_id'] = $RS->fields['id'];
                                                                                $coupon_redeem_array['redeem_date'] = date("Y-m-d H:i:s");
                                                                                $coupon_redeem_array['redeem_value'] = $redeem_deal_value;
                                                                                $coupon_redeem_array['redeemption_fee'] = $redeemption_fee;
                                                                                $coupon_redeem_array['redeem_merchant_id'] = $merchant_id;
                                                                                $coupon_redeem_array['transaction_fees'] = $RS->fields['transaction_fees'];
                                                                                $coupon_redeem_array['transaction_fees_price'] = ($RS->fields['transaction_fees']) * $B4;

                                                                                $objDBWrt->Insert($coupon_redeem_array, "coupon_redeem");


                                                                                // redeem trigger	

                                                                                $redeem_point = $redeem_array['earned_reward'];
                                                                                $arr_bus = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $RS->fields['location_id']);
                                                                                if (trim($arr_bus[0]) == "") {
                                                                                        unset($arr_bus[0]);
                                                                                        $arr_bus = array_values($arr_bus);
                                                                                }
                                                                                $json_bus = json_decode($arr_bus[0]);
                                                                                $business_name = $json_bus->bus_name;

                                                                                $array_gcm = array();
                                                                                $array_gcm['id'] = $RS->fields['customer_id'];
                                                                                $RS_gcm = $objDB->Show("customer_user", $array_gcm);
                                                                                if ($RS_gcm->fields['notification_setting'] == 1) {
                                                                                        if ($RS_gcm->fields['gcm_registration_id'] != "") {
                                                                                                $gcm_registration_id = $RS_gcm->fields['gcm_registration_id'];

                                                                                                $device_id = $gcm_registration_id;
                                                                                                $arr = file(WEB_PATH . '/merchant/process.php?send_gcm_message=yes&customer_id=' . $RS->fields['customer_id'] . '&device_id=' . $device_id . '&redeem_point=' . $redeem_point . '&business_name=' . urlencode($business_name) . '&notificationtype=point_for_visit');
                                                                                        }
                                                                                        if ($RS_gcm->fields['device_id'] != "") {
                                                                                                $device_id = $RS_gcm->fields['device_id'];

                                                                                                $arr = file(WEB_PATH . '/merchant/process.php?send_push_message=yes&customer_id=' . $RS->fields['customer_id'] . '&device_id=' . $device_id . '&redeem_point=' . $redeem_point . '&business_name=' . urlencode($business_name) . '&notificationtype=point_for_visit');
                                                                                        }
                                                                                }

                                                                                // redeem trigger				  

                                                                                /* for make entry in subscribed store table */
                                                                                 $Sql_r_c = "Select * from coupon_redeem where coupon_id =".$generated_coupon_id ;

                                                                                  $RS_r_c =  $objDB->Conn->Execute($Sql_r_c); 
                                                                                
                                                                                if ($RS_r_c->RecordCount() == 1) {
                                                                                         $sql_chk ="select * from subscribed_stores where customer_id= ".$RS->fields['customer_id'] ." and location_id=". $RS->fields['location_id'];
                                                                                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); 
                                                                                        

                                                                                        if ($subscibed_store_rs->RecordCount() != 0 && $subscibed_store_rs->fields['first_redeemed_date'] == "0000-00-00 00:00:00") {
                                                                                                 $up_subscribed_store = "Update subscribed_stores set first_redeemed_date='".date("Y-m-d H:i:s")."'  where  customer_id= ".$RS->fields['customer_id']." and location_id=".$RS->fields['location_id'];
                                                                                                  $objDB->Conn->Execute($up_subscribed_store); 
                                                                                                
                                                                                        }
                                                                                }
                                                                                /* for make entry in subscribed store table */
                                                                                if ($used == 1) {
                                                                                         $of_up=  "Update campaign_location set offers_left=".($o_left-1)." , used_offers=".($used_offer+1)."  where  campaign_id=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];

                                                                                          $objDB->Conn->Execute($of_up); 
                                                                                        
                                                                                }


                                                                                /* Don't give point if max nof sharing rich */

                                                                                 $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$RS->fields['customer_campaign_code']." and referred_customer_id<>0 and referral_reward<>0";

                                                                                  $RS_shared = $objDB->Conn->Execute($Sql_shared); 
                                                                                

                                                                                $shared_counter = $RS->fields['no_of_shared'];
                                                                                //echo $shared_counter."==share counter";
                                                                                if ($RS->fields['no_of_shared'] < $RS->fields['max_no_sharing']) {


                                                                                        // set actually shared campaign 
                                                                                        $sql = "SELECT * FROM reward_user WHERE referred_customer_id=".$RS->fields['customer_id']." and campaign_id = ".$RS->fields['customer_campaign_code']."   AND location_id=".$RS->fields['location_id'];
                                                                                          $RS_referral_customer = $objDB->Conn->Execute($sql); 
                                                                                        

                                                                                        $where = array();
                                                                                        $where['referred_customer_id'] = $RS->fields['customer_id'];
                                                                                        $where['campaign_id'] = $RS->fields['customer_campaign_code'];
                                                                                        $where['referral_reward'] = 0;
                                                                                        $where['location_id'] = $RS->fields['location_id'];
                                                                                        $u_array = array();
                                                                                        //echo "<br/>record count".$RS_referral_customer->RecordCount();
                                                                                        //echo "<br/>deduct point ==".$deduct_points."==".$RS->fields['referral_rewards'];
                                                                                        if ($RS_referral_customer->RecordCount() != 0) {
                                                                                                if ($RS_referral_customer->fields['referral_reward'] == 0) {
                                                                                                        if ($deduct_points >= $RS->fields['referral_rewards']) {
                                                                                                                $u_array['referral_reward'] = $RS->fields['referral_rewards'];
                                                                                                                $u_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                                                $deduct_points = $deduct_points - $RS->fields['referral_rewards'];
                                                                                                                $shared_counter++;
                                                                                                        } else {
                                                                                                                $u_array['referral_reward'] = 0;
                                                                                                                $u_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                                                $deduct_points = $RS->fields['block_point'];
                                                                                                        }

                                                                                                        /*
                                                                                                          echo "<pre>u array====";
                                                                                                          print_r($u_array);
                                                                                                          echo "where:";
                                                                                                          print_r($where);
                                                                                                          echo "</pre>"; */

                                                                                                        $objDBWrt->Update($u_array, "reward_user", $where);
                                                                                                }
                                                                                        }
                                                                                         $Sql = "SELECT * FROM reward_user WHERE referred_customer_id=".$RS->fields['customer_id']."  AND referral_reward = 0 AND location_id=".$RS->fields['location_id'];

                                                                                          $RS_referral_customer = $objDB->Conn->Execute($Sql); 
                                                                                        

                                                                                        if ($RS_referral_customer->RecordCount() != 0) {
                                                                                                while ($row_referral_customer = $RS_referral_customer->FetchRow()) {
                                                                                                         $sql = "Select * from reward_user where customer_id =".$row_referral_customer['referred_customer_id']." and location_id =".$row_referral_customer['location_id'] ." and campaign_id = ". $row_referral_customer['campaign_id'];
                                                                                                          $RS_coupon_redeem = $objDB->Conn->Execute($sql); 
                                                                                                        
                                                                                                        if ($RS_coupon_redeem->RecordCount() != 0) {
                                                                                                                $where = array();
                                                                                                                $where['referred_customer_id'] = $RS->fields['customer_id'];
                                                                                                                $where['campaign_id'] = $row_referral_customer['campaign_id'];
                                                                                                                $where['referral_reward'] = 0;
                                                                                                                $where['location_id'] = $RS->fields['location_id'];
                                                                                                                $u_array = array();
                                                                                                                //	echo "<br/>record count".$RS_referral_customer->RecordCount();
                                                                                                                //			echo "<br/>deduct point ==".$deduct_points."==".$RS->fields['referral_rewards'];
                                                                                                                //
														if ($shared_counter < $RS->fields['max_no_sharing']) {
                                                                                                                        if ($deduct_points >= $RS->fields['referral_rewards']) {
                                                                                                                                $u_array['referral_reward'] = $RS->fields['referral_rewards'];
                                                                                                                                $u_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                                                                $deduct_points = $deduct_points - $RS->fields['referral_rewards'];
                                                                                                                                $u_array['campaign_id'] = $RS->fields['customer_campaign_code'];
                                                                                                                                $shared_counter++;
                                                                                                                        } else {
                                                                                                                                $u_array['referral_reward'] = 0;
                                                                                                                                $u_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                                                                $deduct_points = $RS->fields['block_point'];
                                                                                                                        }
                                                                                                                        /*
                                                                                                                          echo "<pre>u array";
                                                                                                                          print_r($u_array);
                                                                                                                          echo "where:";
                                                                                                                          print_r($where);
                                                                                                                          echo "</pre>"; */
                                                                                                                        $objDBWrt->Update($u_array, "reward_user", $where);
                                                                                                                }
                                                                                                        }
                                                                                                }
                                                                                        }
                                                                                }
                                                                                //update sharing counter 
                                                                                 $sql = "update campaigns set no_of_shared=".$shared_counter." where id=". $RS->fields['customer_campaign_code'];
                                                                                  $objDB->Conn->Execute($sql); 
                                                                                

                                                                                // if sharing point not available then send message
                                                                                 $Sql_shared = "SELECT * from reward_user WHERE campaign_id=".$RS->fields['customer_campaign_code']." and referred_customer_id<>0 ";
                                                                                  $RS_shared_ = $objDB->Conn->Execute($Sql_shared); 
                                                                                
                                                                                if ($RS_shared_->RecordCount() > $RS->fields['max_no_sharing']) {
                                                                                        //$sql_get_merchant = "";
                                                                                }
                                                                                // 
                                                                                /* Don't give point if max nof sharing rich  */






                                                                                //-- start for point deduction



                                                                                 $Sql = "Update campaigns set block_point =".$deduct_points." where id=".$RS->fields['customer_campaign_code'];

                                                                                  $objDB->Conn->Execute($Sql); 
                                                                                

                                                                                //-- End of point deduction	
                                                                                // review insert here //
                                                                                 $sql = "update reward_user set review_rating_visibility = 1 where customer_id=".$RS->fields['customer_id']." and location_id= ".$RS->fields['location_id']." and campaign_id=".$RS->fields['customer_campaign_code'];
                                                                                  $rs = $objDB->Conn->Execute($sql); 
                                                                                
                                                                                $json_array['status'] = "true";
                                                                                $json_array['id'] = $RS->fields['customer_campaign_code'];
                                                                                $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_coupon_redeemed"];
                                                                                $_SESSION['msg'] = $merchant_msg["redeem-deal"]["Msg_coupon_redeemed"];
                                                                                $json = json_encode($json_array);
                                                                                //finish transaction - This will rollback the transcation if any error occure
                                                                                $objDB->Conn->CompleteTrans();
                                                                                echo $json;
                                                                                return $json;
                                                                        }
                                                                }
                                                        } else {
                                                                 $location_max_sql = "Select num_activation_code , offers_left , used_offers from campaign_location where  campaign_id=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                  $location_max = $objDB->Conn->Execute($location_max_sql); 
                                                                
                                                                $max_coupon = $location_max->fields['num_activation_code'];
                                                                $o_left = $location_max->fields['offers_left'];
                                                                $used_offer = $location_max->fields['used_offers'];
                                                                //    }
                                                                $flag_redeem = 1;
                                                                //$Sql_2_use =  "SELECT * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                 $Sql_2_use =  "Select * from coupon_redeem where coupon_id = (select id from coupon_codes where customer_id=".$RS->fields['customer_id']." and customer_campaign_code=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id']." )";
                                                                  $RS_2_use = $objDB->Conn->Execute($Sql_2_use); 
                                                                
                                                                if ($RS_2_use->RecordCount() <= 0) {
                                                                        $flag_redeem = 0;
                                                                }
                                                                if ($o_left > 0 || $flag_redeem == 0) {
                                                                        //$Sql_2_use =  "SELECT * FROM reward_user WHERE referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                         $Sql_2_use =  "Select * from coupon_redeem where coupon_id = (select id from coupon_codes where customer_id=".$RS->fields['customer_id']." and customer_campaign_code=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id']." )";
                                                                          $RS_2_use = $objDB->Conn->Execute($Sql_2_use); 
                                                                        
                                                                        if ($RS_2_use->RecordCount() > 0) {
                                                                                $used = 1;
                                                                        }
                                                                        $deduct_points = 0;

                                                                        //-- start for point deduction
                                                                        $array_where_user = array();
                                                                        $array_where_user['id'] = $merchant_id;
                                                                        $RS_User = $objDB->Show("merchant_user", $array_where_user);
                                                                        $m_parent = $RS_User->fields['merchant_parent'];
                                                                        if ($RS_User->fields['merchant_parent'] == 0) {
                                                                                $merchant_id = $merchant_id;
                                                                        } else {
                                                                                $merchant_id = $RS_User->fields['merchant_parent'];
                                                                        }

                                                                        $array_where_user = array();
                                                                        $array_where_user['id'] = $merchant_id;
                                                                        $RS_User = $objDB->Show("merchant_user", $array_where_user);
                                                                        //-- End of point deduction
                                                                        /*
                                                                          if($RS_User->fields['available_point'] < 100)
                                                                          {
                                                                          $mail = new PHPMailer();
                                                                          $body = "<p>Dear Scanflip merchant user,</p>";
                                                                          $body .= "<p> We are inform u that your points about to finish. So please to get scanflip functionality benefits renew your package as early as possible </p>";
                                                                          $mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
                                                                          $mail->AddAddress($RS_User->fields['email']);
                                                                          $mail->From = "no-reply@scanflip.com";
                                                                          $mail->FromName = "ScanFlip Support";
                                                                          $mail->Subject    = "ScanFlip Subscription";
                                                                          $mail->MsgHTML($body);
                                                                          $mail->Send();
                                                                          }
                                                                         */
                                                                        $redeem_array = array();
                                                                        $redeem_array['customer_id'] = $RS->fields['customer_id'];
                                                                        $redeem_array['campaign_id'] = $RS->fields['customer_campaign_code'];

                                                                        if ($RS->fields['block_point'] >= $RS->fields['redeem_rewards']) {
                                                                                $redeem_array['earned_reward'] = $RS->fields['redeem_rewards'];
                                                                                $deduct_points = $RS->fields['block_point'] - $RS->fields['redeem_rewards'];
                                                                                $giving_points = $RS->fields['redeem_rewards'];
                                                                        } else {
                                                                                $redeem_array['earned_reward'] = 0;
                                                                                $deduct_points = $RS->fields['block_point'];
                                                                                $giving_points = 0;
                                                                        }
                                                                        if ($flag_redeem == 0) {


                                                                                $redeem_array['referral_reward'] = 0;
                                                                                $redeem_array['referred_customer_id'] = 0;
                                                                                $redeem_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                $redeem_array['coupon_code_id'] = $RS->fields['id'];
                                                                                $redeem_array['location_id'] = $RS->fields['location_id'];
                                                                                $redeem_array['redeem_deal_value'] = $redeem_deal_value;
                                                                                $objDBWrt->Insert($redeem_array, "reward_user");
                                                                        } else {
                                                                                $sql_get_earned_reward = "select * FROM reward_user WHERE referred_customer_id = 0 and customer_id=" . $RS->fields['customer_id'] . " and campaign_id =" . $RS->fields['customer_campaign_code'] . " and location_id=" . $RS->fields['location_id'];
                                                                                $rs_get_earned_reward = $objDB->Conn->Execute($sql_get_earned_reward);
                                                                                $total_earned_reward = $rs_get_earned_reward->fields['earned_reward'];
                                                                                 $up_sql ="Update reward_user set earned_reward =".($total_earned_reward + $giving_points)."  where referred_customer_id = 0 and customer_id=".$RS->fields['customer_id']." and campaign_id =".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                                  $objDB->Conn->Execute($up_sql); 
                                                                                
                                                                        }

                                                                        $coupon_redeem_array = array();


                                                                        $coupon_redeem_array['coupon_id'] = $RS->fields['id'];
                                                                        $coupon_redeem_array['redeem_date'] = date("Y-m-d H:i:s");
                                                                        $coupon_redeem_array['redeem_value'] = $_REQUEST['txt_redeem_deal_value'];
                                                                        $coupon_redeem_array['redeemption_fee'] = $redeemption_fee;
                                                                        $coupon_redeem_array['redeem_merchant_id'] = $merchant_id;
                                                                        $coupon_redeem_array['transaction_fees'] = $RS->fields['transaction_fees'];
                                                                        $coupon_redeem_array['transaction_fees_price'] = ($RS->fields['transaction_fees']) * $B4;
                                                                        $objDBWrt->Insert($coupon_redeem_array, "coupon_redeem");

                                                                        // redeem trigger	

                                                                        $redeem_point = $redeem_array['earned_reward'];
                                                                        $arr_bus = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $RS->fields['location_id']);
                                                                        if (trim($arr_bus[0]) == "") {
                                                                                unset($arr_bus[0]);
                                                                                $arr_bus = array_values($arr_bus);
                                                                        }
                                                                        $json_bus = json_decode($arr_bus[0]);
                                                                        $business_name = $json_bus->bus_name;

                                                                        $array_gcm = array();
                                                                        $array_gcm['id'] = $RS->fields['customer_id'];
                                                                        $RS_gcm = $objDB->Show("customer_user", $array_gcm);
                                                                        if ($RS_gcm->fields['notification_setting'] == 1) {
                                                                                if ($RS_gcm->fields['gcm_registration_id'] != "") {
                                                                                        $gcm_registration_id = $RS_gcm->fields['gcm_registration_id'];

                                                                                        $device_id = $gcm_registration_id;
                                                                                        $arr = file(WEB_PATH . '/merchant/process.php?send_gcm_message=yes&customer_id=' . $RS->fields['customer_id'] . '&device_id=' . $device_id . '&redeem_point=' . $redeem_point . '&business_name=' . urlencode($business_name) . '&notificationtype=point_for_visit');
                                                                                }
                                                                                if ($RS_gcm->fields['device_id'] != "") {
                                                                                        $device_id = $RS_gcm->fields['device_id'];

                                                                                        $arr = file(WEB_PATH . '/merchant/process.php?send_push_message=yes&customer_id=' . $RS->fields['customer_id'] . '&device_id=' . $device_id . '&redeem_point=' . $redeem_point . '&business_name=' . urlencode($business_name) . '&notificationtype=point_for_visit');
                                                                                }
                                                                        }

                                                                        // redeem trigger

                                                                        /* for make entry in subscribed store table */
                                                                        //  $Sql_r_c = "Select * from reward_user where location_id=".$RS->fields['location_id']." and customer_id=".$RS->fields['customer_id'];
                                                                         $Sql_r_c = "Select * from coupon_redeem where coupon_id =".$generated_coupon_id ;
                                                                          $RS_r_c =  $objDB->Conn->Execute($Sql_r_c); 
                                                                        
                                                                        if ($RS_r_c->RecordCount() == 1) {
                                                                                 $sql_chk ="select * from subscribed_stores where customer_id= ".$RS->fields['customer_id'] ." and location_id=". $RS->fields['location_id'];
                                                                                  $subscibed_store_rs =$objDB->Conn->Execute($sql_chk); 
                                                                                
                                                                                if ($subscibed_store_rs->RecordCount() != 0) {
                                                                                         $up_subscribed_store = "Update subscribed_stores set first_redeemed_date='".date("Y-m-d H:i:s")."'  where  customer_id= ".$RS->fields['customer_id']." and location_id=".$RS->fields['location_id'];
                                                                                          $objDB->Conn->Execute($up_subscribed_store); 
                                                                                        
                                                                                }
                                                                        }
                                                                        /* for make entry in subscribed store table */
                                                                        if ($used == 1) {
                                                                                 $of_up=  "Update campaign_location set offers_left=".($o_left-1)." , used_offers=".($used_offer+1)."  where  campaign_id=".$RS->fields['customer_campaign_code']." and location_id=".$RS->fields['location_id'];
                                                                                  $objDB->Conn->Execute($of_up); 
                                                                                
                                                                        }

                                                                        /* Don't give point if max nof sharing rich */

                                                                        /* Don't give point if max nof sharing rich */

                                                                        $Sql_shared = "SELECT * from reward_user WHERE campaign_id=" . $RS->fields['customer_campaign_code'] . " and referred_customer_id<>0 and referral_reward<>0";
                                                                        // echo $Sql_shared ;
                                                                        //$RS_shared = $objDB->Conn->Execute($Sql_shared);

                                                                        $shared_counter = $RS->fields['no_of_shared'];
                                                                        //echo $shared_counter."==share counter";
                                                                        if ($shared_counter < $RS->fields['max_no_sharing']) {

                                                                                // set actually shared campaign 
                                                                                 $sql = "SELECT * FROM reward_user WHERE referred_customer_id=".$RS->fields['customer_id']." and campaign_id = ".$RS->fields['customer_campaign_code']."  AND location_id=".$RS->fields['location_id'];

                                                                                  $RS_referral_customer = $objDB->Conn->Execute($sql); 
                                                                                

                                                                                $where = array();
                                                                                $where['referred_customer_id'] = $RS->fields['customer_id'];
                                                                                $where['campaign_id'] = $RS->fields['customer_campaign_code'];
                                                                                $where['referral_reward'] = 0;
                                                                                $where['location_id'] = $RS->fields['location_id'];
                                                                                $u_array = array();
                                                                                //echo "<br/>record count".$RS_referral_customer->RecordCount();
                                                                                //echo "<br/>deduct point ==".$deduct_points."==".$RS->fields['referral_rewards'];
                                                                                if ($RS_referral_customer->RecordCount() != 0) {
                                                                                        if ($RS_referral_customer->fields['referral_reward'] == 0) {
                                                                                                if ($deduct_points >= $RS->fields['referral_rewards']) {
                                                                                                        $u_array['referral_reward'] = $RS->fields['referral_rewards'];
                                                                                                        $u_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                                        $deduct_points = $deduct_points - $RS->fields['referral_rewards'];
                                                                                                        $u_array['campaign_id'] = $RS->fields['customer_campaign_code'];
                                                                                                        $shared_counter++;
                                                                                                } else {
                                                                                                        $u_array['referral_reward'] = 0;
                                                                                                        $u_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                                        $deduct_points = $RS->fields['block_point'];
                                                                                                }

                                                                                                /*
                                                                                                  echo "<pre>u array===";
                                                                                                  print_r($u_array);
                                                                                                  echo "where:";
                                                                                                  print_r($where);
                                                                                                  echo "</pre>"; */

                                                                                                $objDB->Update($u_array, "reward_user", $where);
                                                                                        }
                                                                                }
                                                                                 $Sql = "SELECT * FROM reward_user WHERE referred_customer_id=".$RS->fields['customer_id']."  AND referral_reward = 0 AND location_id=".$RS->fields['location_id'];

                                                                                  $RS_referral_customer = $objDB->Conn->Execute($Sql); 
                                                                                
                                                                                //echo "<br/>".$RS_referral_customer->RecordCount();								
                                                                                if ($RS_referral_customer->RecordCount() != 0) {

                                                                                        while ($row_referral_customer = $RS_referral_customer->FetchRow()) {
                                                                                                 $sql = "Select * from reward_user where customer_id =".$row_referral_customer['referred_customer_id']." and location_id =".$row_referral_customer['location_id'] ." and campaign_id = ".$row_referral_customer['campaign_id'];

                                                                                                  $RS_coupon_redeem = $objDB->Conn->Execute($sql); 
                                                                                                
                                                                                                if ($RS_coupon_redeem->RecordCount() != 0) {
                                                                                                        $where = array();
                                                                                                        $where['referred_customer_id'] = $RS->fields['customer_id'];
                                                                                                        $where['campaign_id'] = $row_referral_customer['campaign_id'];
                                                                                                        $where['referral_reward'] = 0;
                                                                                                        $where['location_id'] = $RS->fields['location_id'];
                                                                                                        $u_array = array();
                                                                                                        //echo "<br/>record count".$RS_referral_customer->RecordCount();
                                                                                                        //echo "<br/>deduct point ==".$deduct_points."==".$RS->fields['referral_rewards'];

                                                                                                        if ($shared_counter < $RS->fields['max_no_sharing']) {
                                                                                                                if ($deduct_points >= $RS->fields['referral_rewards']) {
                                                                                                                        $u_array['referral_reward'] = $RS->fields['referral_rewards'];
                                                                                                                        $u_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                                                        $deduct_points = $deduct_points - $RS->fields['referral_rewards'];
                                                                                                                        $shared_counter++;
                                                                                                                } else {
                                                                                                                        $u_array['referral_reward'] = 0;
                                                                                                                        $u_array['reward_date'] = date("Y-m-d H:i:s");
                                                                                                                        $deduct_points = $RS->fields['block_point'];
                                                                                                                }
                                                                                                                /* echo "<pre>u array";
                                                                                                                  print_r($u_array);
                                                                                                                  echo "where:";
                                                                                                                  print_r($where);
                                                                                                                  echo "</pre>"; */
                                                                                                                $objDBWrt->Update($u_array, "reward_user", $where);
                                                                                                        }
                                                                                                }
                                                                                        }
                                                                                }
                                                                        }

                                                                        //update sharing counter 
                                                                         $sql = "update campaigns set no_of_shared=".$shared_counter." where id=". $RS->fields['customer_campaign_code'];
                                                                          $objDB->Conn->Execute($sql); 
                                                                        
                                                                        /*                                                                         * *ich */



                                                                        //-- start for point deduction



                                                                         $Sql = "Update campaigns set block_point =".$deduct_points." where id=".$RS->fields['customer_campaign_code'];

                                                                          $objDB->Conn->Execute($Sql); 
                                                                        

                                                                        //-- End of point deduction	
                                                                        // review insert here //
                                                                         $sql = "update reward_user set review_rating_visibility = 1 where customer_id=".$RS->fields['customer_id']." and location_id= ".$RS->fields['location_id']." and campaign_id=".$RS->fields['customer_campaign_code'];
                                                                          $rs = $objDB->Conn->Execute($sql); 
                                                                        

                                                                        $json_array['status'] = "true";
                                                                        $json_array['id'] = $RS->fields['customer_campaign_code'];
                                                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_coupon_redeemed"];
                                                                        $_SESSION['msg'] = $merchant_msg["redeem-deal"]["Msg_coupon_redeemed"];
                                                                        $json = json_encode($json_array);
                                                                        $objDB->Conn->CompleteTrans();
                                                                        echo $json;
                                                                        return $json;
                                                                } else {
                                                                        // review insert here //
                                                                         $sql = "update reward_user set review_rating_visibility = 1 where customer_id=".$RS->fields['customer_id']." and location_id= ".$RS->fields['location_id']." and campaign_id=".$RS->fields['customer_campaign_code'];
                                                                          $rs = $objDB->Conn->Execute($sql); 
                                                                        

                                                                        //$rs = $objDB->Conn->Execute($sql);
                                                                        $json_array['status'] = "true";
                                                                        $json_array['id'] = $RS->fields['customer_campaign_code'];
                                                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_campaign_ended"];
                                                                        $_SESSION['msg'] = $merchant_msg["redeem-deal"]["Msg_campaign_ended"];
                                                                        $json = json_encode($json_array);
                                                                        $objDB->Conn->CompleteTrans();
                                                                        echo $json;
                                                                        return $json;
                                                                }
                                                        }
                                                }
                                        }
                                } else { // Wrong data
                                        $json_array['loginstatus'] = "true";
                                        $json_array['status'] = "false";
                                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_coupon_code_not_exist"];
                                        $json = json_encode($json_array);
                                        $objDB->Conn->CompleteTrans();
                                        echo $json;
                                        return $json;
                                }
                        } else {
                                $json_array['loginstatus'] = "true";
                                $json_array['status'] = "false";
                                $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_update_sales"];
                                $json = json_encode($json_array);
                                $objDB->Conn->CompleteTrans();
                                echo $json;
                                return $json;
                        }
                } else {
                        $json_array['loginstatus'] = "true";
                        $json_array['status'] = "false";
                        $json_array['message'] = $merchant_msg["redeem-deal"]["Msg_enter_proper_coupon_code"];
                        $json = json_encode($json_array);
                        $objDB->Conn->CompleteTrans();
                        echo $json;
                        return $json;
                }
        } catch (Exception $e) {
                $json_array = array();
                $json_array['status'] = "false";
                $json_array['message'] = "My sql error cuming" . $e->getMessage();
                $json = json_encode($json_array);
                $objDB->Conn->CompleteTrans();
                echo $json;
                return $json;
                exit;
        }
}

if (isset($_REQUEST['redeem_user_giftcard'])) {
        $merchant_id = $_REQUEST['merchant_id'];
        $gc_code = $_REQUEST['gc_code'];
        $gc_points = $_REQUEST['gc_points'];
        $trans_type = $_REQUEST['trans'];
        $location_val = $_REQUEST['location_id'];

        $array = $array1 = $json = $media_acc_array = array();
        
        /*if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
              $location_val = $_SESSION['merchant_info']['redeem_location'];
                } else {
                         $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
                         $RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
                          $location_val = $RSmedia->fields['location_access'];
                        }*/

        $RS = $objDB->Conn->Execute("SELECT * from giftcard_certificate where certificate_id=? and is_deleted=0", array($gc_code));
        $total_records = $RS->RecordCount();

        if ($total_records == 1) {
            $gc_id = $RS->fields['giftcard_id'];
            $gift_certi_id = $RS->fields['id'];
            $user_id = $RS->fields['user_id'];
            $merchant_points = $RS->fields['merchants_points'];
            $mer_pts_cre = $RS->fields['merchant_points_credited'];
            $status = $RS->fields['status'];
            $date_issued = $RS->fields['date_issued'];
            $expiry_date = date("Y-m-d H:i:s",strtotime("+365 day", strtotime($date_issued)));
            $today = date("Y-m-d H:i:s", mktime());
            //echo $date_issued.'exp'.$expiry_date.'tod'.$today;exit();
            
            if(strtotime($today) > strtotime($expiry_date)){
                $array['status'] = "false";
                $array['msg'] = "Your giftcard points has been expired";
                $json = json_encode($array);
                echo $json;
                exit();
            }

            if ($trans_type == "debit") {
                if ($status == 1) {
                    $array['status'] = "false";
                    $array['msg'] = "Your card points has been finished";
                    $json = json_encode($array);
                    echo $json;
                    exit();
                }
                if (($merchant_points - $mer_pts_cre) < $gc_points) {
                    $array['status'] = "false";
                    $array['msg'] = "Insufficient points";
                    $json = json_encode($array);
                    echo $json;
                    exit();
                }
                
                $RS1 = $objDB->Conn->Execute("SELECT points_earned_giftcard_pending, points_earned_giftcard, points_available from merchant_point_management where merchant_id=?", array($merchant_id));
                $points_pending = $RS1->fields['points_earned_giftcard_pending'];
                $points_earned = $RS1->fields['points_earned_giftcard'];
                $points_avail = $RS1->fields['points_available'];
                $points_avail_update = $points_avail + $gc_points;
                $points_pending_update = $points_pending - $gc_points;  
                $points_earned_update = $points_earned + $gc_points;
                $objDBWrt->Conn->Execute("Update merchant_point_management set points_earned_giftcard_pending=?, points_earned_giftcard=?, points_available=? where merchant_id=?", array($points_pending_update,$points_earned_update,$points_avail_update,$merchant_id));
                
                $array1['date'] = date("Y-m-d H:i:s");
                $array1['user_id'] = $user_id;
                $array1['giftcard_certificate_id'] = $gift_certi_id;
                $array1['amount'] = $gc_points;
                $array1['type'] = 1;
                $array1['location_id'] = $location_val;
                $objDB->Insert($array1, "giftcard_transaction");

                $new_mer_points = $mer_pts_cre + $gc_points;
                $objDBWrt->Conn->Execute("Update giftcard_certificate set merchant_points_credited=? where user_id=? and certificate_id=?", array($new_mer_points,$user_id,$gc_code));
                $array['status'] = "true";
                $array['msg'] = "Your gift card has been redeemed";
                $json = json_encode($array);
                echo $json;
                exit();
            }
            if ($trans_type == "credit") {
                
                if($mer_pts_cre > $gc_points)
                {
                $array1['date'] = date("Y-m-d H:i:s");
                $array1['user_id'] = $user_id;
                $array1['giftcard_certificate_id'] = $gift_certi_id;
                $array1['amount'] = $gc_points;
                $array1['type'] = 0;
                $array1['location_id'] = $location_val;
                $objDB->Insert($array1, "giftcard_transaction");
                $mer_pts_cred = $mer_pts_cre - $gc_points;
                $objDBWrt->Conn->Execute("Update giftcard_certificate set merchant_points_credited=? where user_id=? and certificate_id=?", array($mer_pts_cred,$user_id,$gc_code));
                
                $RS1 = $objDB->Conn->Execute("SELECT points_earned_giftcard, points_available from merchant_point_management where merchant_id=?", array($merchant_id));
                $points_earned = $RS1->fields['points_earned_giftcard'];
                $points_avail = $RS1->fields['points_available'];
                $new_points_avail = $points_avail - $gc_points;
                $new_points_earned = $points_earned - $gc_points;
                $objDBWrt->Conn->Execute("Update merchant_point_management set points_earned_giftcard=?, points_available=? where merchant_id=?", array($new_points_earned,$new_points_avail,$merchant_id));
                
                $array['status'] = "true";
                $array['msg'] = "User's gift card points has been credited";
                $json = json_encode($array);
                echo $json;
                exit();
                }else{
                    $array['status'] = "false";
                    $array['msg'] = "User's gift card points cannot be credited";
                    $json = json_encode($array);
                    echo $json;
                    exit();
                    
                }
                }
                
            }
            else {
            $array['status'] = "false";
            $array['msg'] = "Invalid code";
            $json = json_encode($array);
            echo $json;
            exit();
        }
            
            
        }
        
        http://scanflip/merchant/process_mobile.php?redeem_reward_campaign=true&merchant_id=17&cp_code=cpV6IOL1HK
        if (isset($_REQUEST['redeem_reward_campaign'])) {
        $merchant_id = $_REQUEST['merchant_id'];
        $cp_code = $_REQUEST['cp_code'];
        $location_val = $_REQUEST['location_id'];
        
        $array = $array1 = $json = $media_acc_array = array();
        
        /*if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
              $location_val = $_SESSION['merchant_info']['redeem_location'];
                } else {
                         $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
                         $RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
                          $location_val = $RSmedia->fields['location_access'];
                        }*/

        $RS = $objDB->Conn->Execute("SELECT * from rewardzone_campaign_certificate where certificate_id=? and is_deleted=0 and is_redeem=0 and is_points_credited=0", array($cp_code));
        
        $total_records = $RS->RecordCount();
        if ($total_records == 1) {
            $cp_id = $RS->fields['id'];
            $user_id = $RS->fields['user_id'];
            $merchant_points = $RS->fields['merchants_points'];
            //$status = $RS->fields['status'];
            $date_issued = $RS->fields['date_issued'];
            $expiry_date = date("Y-m-d H:i:s",strtotime("+365 day", strtotime($date_issued)));
            $today = date("Y-m-d H:i:s", mktime());
            //echo $date_issued.'exp'.$expiry_date.'tod'.$today;exit();
            
            if(strtotime($today) > strtotime($expiry_date)){
                $array['status'] = "false";
                $array['msg'] = "Your campaign has been expired";
                $json = json_encode($array);
                echo $json;
                exit();
            }
            
                /*if ($status == 1) {
                    $array['status'] = "false";
                    $array['msg'] = "Your card points has been finished";
                    $json = json_encode($array);
                    echo $json;
                    exit();
                }*/
                
                $RS1 = $objDB->Conn->Execute("SELECT points_available from merchant_point_management where merchant_id=?", array($merchant_id));
                $points_avail = $RS1->fields['points_available'];
                $points_avail_update = $points_avail + $merchant_points;
                $objDBWrt->Conn->Execute("Update merchant_point_management set points_available=? where merchant_id=?", array($points_avail_update,$merchant_id));
                
                $array1['redeem_date'] = date("Y-m-d H:i:s");
                $array1['location_id'] = $location_val;
                $array1['employee_id'] = $merchant_id;
                $array1['rewardzone_campaign_certificate_id'] = $cp_id;
                $objDB->Insert($array1, "rewardzone_campaign_transaction");
                
                $objDBWrt->Conn->Execute("Update rewardzone_campaign_certificate set is_redeem=1, is_points_credited=1 where user_id=? and certificate_id=?", array($user_id,$cp_code));
                $array['status'] = "true";
                $array['msg'] = "Your rewardzone campaign has been redeemed";
                $json = json_encode($array);
                echo $json;
                exit();
            
        } else {
            $array['status'] = "false";
            $array['msg'] = "Invalid code";
            $json = json_encode($array);
            echo $json;
            exit();
        }
}
?>
