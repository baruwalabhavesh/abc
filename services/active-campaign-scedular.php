<?php

//require_once("/var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
require LIBRARY . '/fb-sdk/src/facebook.php';
include_once(LIBRARY . "/fb-sdk/src/facebook_secret.php");

//$objDB = new DB();

$array_where_as = array();
$array_where_as['id'] = 4;
$RS = $objDB->Show("admin_settings", $array_where_as);
if ($RS->RecordCount() > 0) {
        //echo $RS->fields['action'];

        if ($RS->fields['action'] == 0) {
                $array_values_as = $array_where_as = array();
                $array_values_as['action'] = 1;
                $array_where_as['id'] = 4;
                $objDB->Update($array_values_as, "admin_settings", $array_where_as);


                $facebook = new Facebook(array(
                        'appId' => facebookappId,
                        'secret' => facebooksecret,
                ));

//$sql_time_zone="select * from locations";
//$RS_location_time_data = $objDB->execute_query($sql_time_zone);
                $RS_location_time_data = $objDB->Conn->Execute("select * from locations");
                if ($RS_location_time_data->RecordCount() > 0) {
                        while ($Row_time_data = $RS_location_time_data->FetchRow()) {
                                //$sql = "update locations set minimum_age=1 where id=".$Row_time_data['id'];
                                //$objDB->execute_query($sql);
                                // $location_time.="Today 12:00PM - 9:00PM";
                                if ($Row_time_data['timezone_name'] != "") {
                                        //$location_time.=$Row->timezone_name;
                                        $time_zone = $Row_time_data['timezone_name'];
                                }
                                date_default_timezone_set($time_zone);
                                $current_day = date('D');
                                $current_time = date('g:i A');

                                // $current_day=date('D');
                                //echo strtolower($current_day);
                                //$sql="select lh.start_time , lh.end_time , l.id location_id ,
                                //CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) c_time from location_hours lh inner join  locations l on l.id=lh.location_id
                                //				where day='".strtolower($current_day)."'";
                                //			 $RS_hours_data = $objDB->execute_query($sql);
                                //$sql="select * from location_hours where location_id=".$Row_time_data['id']." and day='".strtolower($current_day)."'";
                                /* $sql="select * from location_hours where location_id=".$Row_time_data['id'];

                                  $RS_hours_data = $objDB->execute_query($sql); */
                                $RS_hours_data = $objDB->Conn->Execute("select * from location_hours where location_id=?", array($Row_time_data['id']));

                                $location_time = "";
                                $start_time = "";
                                $end_time = "";
                                $status_time = "";
                                if ($RS_hours_data->RecordCount() > 0) {
                                        $times = array();
                                        while ($Row_data = $RS_hours_data->FetchRow()) {
                                                //echo $Row_data['start_time']." and ".$Row_data['end_time']."</br>";
                                                $start_time = $Row_data['start_time'];
                                                $end_time = $Row_data['end_time'];
                                                $location_time.=$Row_data['start_time'] . " - ";
                                                $location_time.=$Row_data['end_time'];

                                                $st_time = strtotime($start_time);
                                                $end_time = strtotime($end_time);

                                                //$cur_time   =   strtotime($Row_data['c_time']);
                                                $cur_time = strtotime($current_time);

                                                // new logic start

                                                $starttime = "";
                                                if (strpos($Row_data['start_time'], "A")) {
                                                        $pos = strpos($Row_data['start_time'], "A");
                                                        $starttime = substr($Row_data['start_time'], 0, $pos) . " " . substr($Row_data['start_time'], $pos);
                                                } else if (strpos($Row_data['start_time'], "P")) {
                                                        $pos = strpos($Row_data['start_time'], "P");
                                                        $starttime = substr($Row_data['start_time'], 0, $pos) . " " . substr($Row_data['start_time'], $pos);
                                                }

                                                $endtime = "";
                                                if (strpos($Row_data['end_time'], "A")) {
                                                        $pos = strpos($Row_data['end_time'], "A");
                                                        $endtime = substr($Row_data['end_time'], 0, $pos) . " " . substr($Row_data['end_time'], $pos);
                                                } else if (strpos($Row_data['end_time'], "P")) {
                                                        $pos = strpos($Row_data['end_time'], "P");
                                                        $endtime = substr($Row_data['end_time'], 0, $pos) . " " . substr($Row_data['end_time'], $pos);
                                                }

                                                $times[$Row_data['day']] = $starttime . " - " . $endtime;
                                        }
                                        $current_time = date('g:iA');
                                        $now = strtotime($current_time);
                                        $open = isOpen($now, $times);

                                        if ($open == 0) {

                                                /* $sql = "update locations set is_open=0 where id=".$Row_time_data['id'];

                                                  $objDB->execute_query($sql); */
                                                $objDBWrt->Conn->Execute("update locations set is_open=? where id=?", array(0, $Row_time_data['id']));
                                        } else {
                                                /* $sql = "update locations set is_open=1 where id=".$Row_time_data['id'];

                                                  $objDB->execute_query($sql); */
                                                $objDBWrt->Conn->Execute("update locations set is_open=? where id=?", array(1, $Row_time_data['id']));
                                        }
                                }
                        }
                }
                /*                 * **************** location status opne  logic *********** */
                ?>
                <?php

                /* $sql = "SELECT * FROM locations  WHERE active=1";

                  $RSStore = $objDB->execute_query($sql); */
                $RSStore = $objDB->Conn->Execute("SELECT * FROM locations  WHERE active=?", array(1));


                if ($RSStore->RecordCount() > 0) {
                        while ($Row = $RSStore->FetchRow()) {
                                $array = array();
                                $array['id'] = $Row['created_by'];
                                $RSmerchant = $objDB->Show("merchant_user", $array);
                                //$num_activation = $RSmerchant->fields['number_of_active_campaign'];

                                /* get active campaign per month from package */
                                $array_where_mb['merchant_id'] = $Row['created_by'];



                                $RS_mb = $objDB->Show("merchant_billing", $array_where_mb);
                                /* if today date is first day of month then reset value on no_of_campaigns */
                                $sql_cuur_date = "select now() curr_date";
//    $rs_cuur_date = $objDB->execute_query($sql_cuur_date);
                                $rs_cuur_date = $objDB->Conn->Execute($sql_cuur_date);

                                $curr_date = $rs_cuur_date->fields['curr_date'];

                                $date = date("d", strtotime($curr_date));
                                $array_where_bp['id'] = $RS_mb->fields['pack_id'];
                                $RS_bp = $objDB->Show("billing_packages", $array_where_bp);
                                if ($RS_bp->RecordCount() > 0) {
                                        $package_total_campaigns = $RS_bp->fields['total_no_of_camp_per_month'];
                                } else {
                                        $package_total_campaigns = 0;
                                }
//    if($date == 1)
//    {
//        $query_updatenooflocation = "update merchant_user set total_no_of_campaign=".$package_total_campaigns." where id=".$Row['created_by'];
//        
//        $objDB->execute_query($query_updatenooflocation);
//    }

                                /* if today date is first day of month then reset value on no_of_campaigns */
                                if ($RS_mb->RecordCount() > 0) {
                                        $array_where_bp['id'] = $RS_mb->fields['pack_id'];
                                        $RS_bp = $objDB->Show("billing_packages", $array_where_bp);
                                        $num_activation = $RS_bp->fields['no_of_active_camp_per_loca'];
                                } else {
                                        $num_activation = 0;
                                }
                                /* get active campaign per month from package */
                                $date_wh = "AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  C.start_date AND C.expiration_date";

                                //  $date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."','+00:00') BETWEEN CONVERT_TZ(C.start_date,'+00:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) AND CONVERT_TZ(C.expiration_date,'+00:00',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))"; 

                                $array = array();
                                $array['location_id'] = $Row['id'];
                                $array['active'] = 1;
                                $Active_campaign_by_location = $objDB->Show("campaign_location", $array);

                                if ($Active_campaign_by_location->RecordCount() > $num_activation) {
                                        /* $query_updatenooflocation = "update merchant_user set total_no_of_campaign=".$package_total_campaigns." where id=".$Row['created_by'];

                                          $objDB->execute_query($query_updatenooflocation); */
                                        $objDBWrt->Conn->Execute("update merchant_user set total_no_of_campaign=? where id=?", array($package_total_campaigns, $Row['created_by']));

                                        while ($Row_c_l = $Active_campaign_by_location->FetchRow()) {
                                                $array_values['campaign_id'] = $Row_c_l['campaign_id'];
                                                $array_values['location_id'] = $Row_c_l['location_id'];
                                                $where_clause['active'] = 0;
                                                $where_clause['active_in_future'] = 0;
                                                $objDBWrt->Update($where_clause, "campaign_location", $array_values);
                                                //  if()
                                        }
                                } else {

                                        while ($Row_c_l = $Active_campaign_by_location->FetchRow()) {
                                                /* $Sql= "Select * from campaigns C , campaign_location CL , locations l  where l.id=CL.location_id and C.id = CL.campaign_id and C.visible = 1 and CL.location_id=".$Row['id']." and C.id=".$Row_c_l['campaign_id']." ".$date_wh." and CL.active=1 and CL.active_in_future<>1 order by C.expiration_date ";

                                                  $RS_c = $objDB->execute_query($Sql); */
                                                $RS_c = $objDB->Conn->Execute("Select * from campaigns C , campaign_location CL , locations l  where l.id=CL.location_id and C.id = CL.campaign_id and C.visible = ? and CL.location_id=? and C.id=? " . $date_wh . " and CL.active=? and CL.active_in_future<>? order by C.expiration_date ", array(1, $Row['id'], $Row_c_l['campaign_id'], 1, 1));

                                                if ($RS_c->RecordCount() == 0) {
                                                        $array_values['campaign_id'] = $Row_c_l['campaign_id'];
                                                        $array_values['location_id'] = $Row_c_l['location_id'];
                                                        $where_clause['active'] = 0;
                                                        $where_clause['active_in_future'] = 0;
                                                        $objDBWrt->Update($where_clause, "campaign_location", $array_values);
//                                 $d_p = $RS_c->fields['block_point'];
//                                 $t_p = $RSmerchant->fields['available_point'] + $d_p;
//                                 $sql_update = "Update merchant_user set available_point=".$t_p." where id=". $RSmerchant->fields['id'];
//                                 $objDB->execute_query($sql_update);
                                                }
                                                //                    
                                        }

                                        $array = array();
                                        $array['location_id'] = $Row['id'];
                                        $array['active_in_future'] = 1;
                                        $Active_campaign_by_location_ = $objDB->Show("campaign_location", $array);
                                        while ($Row_c_l = $Active_campaign_by_location_->FetchRow()) {
//                             $Sql= "Select * from campaigns C , campaign_location CL , locations l  where l.id=CL.location_id and C.id = CL.campaign_id and 
//                                   CL.offers_left > 0  and 
//                                 C.visible = 1 and CL.location_id=".$Row['id']." and C.id=".$Row_c_l['campaign_id']." AND 
//                                     (((  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  C.start_date AND C.expiration_date  ))
//                                             and   CL.active_in_future =1) and CL.active <> 1 order by C.expiration_date ";
                                                /* $Sql= "Select * from campaigns C , campaign_location CL , locations l  where l.id=CL.location_id and C.id = CL.campaign_id and C.visible = 1 and CL.location_id=".$Row['id']." and C.id=".$Row_c_l['campaign_id']." AND (((  CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  C.start_date AND C.expiration_date  )) and   CL.active_in_future =1) and CL.active <> 1 order by C.expiration_date ";

                                                  $RS_c = $objDB->execute_query($Sql); */
                                                $RS_c = $objDB->Conn->Execute("Select * from campaigns C , campaign_location CL , locations l  where l.id=CL.location_id and C.id = CL.campaign_id and C.visible = ? and CL.location_id=? and C.id=? AND (((  CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  C.start_date AND C.expiration_date  )) and   CL.active_in_future =?) and CL.active <> ? order by C.expiration_date ", array(1, $Row['id'], $Row_c_l['campaign_id'], 1, 1));
                                                if ($RS_c->RecordCount() == 0) {
                                                        $array_values['campaign_id'] = $Row_c_l['campaign_id'];
                                                        $array_values['location_id'] = $Row_c_l['location_id'];
                                                        $where_clause['active'] = 0;
                                                        $where_clause['active_in_future'] = 0;
                                                        $objDBWrt->Update($where_clause, "campaign_location", $array_values);
//                                 $d_p = $RS_c->fields['block_point'];
//                                 $t_p = $RSmerchant->fields['available_point'] + $d_p;
//                                 $sql_update = "Update merchant_user set available_point=".$t_p." where id=". $RSmerchant->fields['id'];
//                                 $objDB->execute_query($sql_update);
                                                }
                                                //                    
                                        }
                                }
                                /* $Sql= "Select * from campaigns C , campaign_location CL , locations l  where l.id=CL.location_id and C.id = CL.campaign_id and 
                                  C.visible = 1 and CL.location_id=".$Row['id']."   ".$date_wh." and CL.active=1 order by C.expiration_date ";

                                  $RS_c = $objDB->execute_query($Sql); */
                                $RS_c = $objDB->Conn->Execute("Select * from campaigns C , campaign_location CL , locations l  where l.id=CL.location_id and C.id = CL.campaign_id and C.visible = ? and CL.location_id=?   " . $date_wh . " and CL.active=? order by C.expiration_date ", array(1, $Row['id'], 1));

                                $active_campaign_at_location = $RS_c->RecordCount();
                                /* if($Row['id']==18)
                                  { */

                                /* 	} */
                                /* $user_total_campaign_sql = "select total_no_of_campaign from merchant_user where id=".getmainmercahnt_id($Row['created_by']);
                                  $rs_total_campaign =$objDB->execute_query( $user_total_campaign_sql); */
                                $rs_total_campaign = $objDB->Conn->Execute("select total_no_of_campaign from merchant_user where id=?", array(getmainmercahnt_id($Row['created_by'])));

                                $exists_no_of_campaigns = $rs_total_campaign->fields['total_no_of_campaign'];
                                $counter_no_of_campaigns = $rs_total_campaign->fields['total_no_of_campaign'];

                                if ($num_activation != "") {
                                        // if(($num_activation-$active_campaign_at_location)>0)
                                        // {
                                        $Sql = "Select C.* from campaigns C , campaign_location CL , locations l  where l.id=CL.location_id and C.id = CL.campaign_id and  C.visible = 1 and CL.active<>1 and CL.location_id=" . $Row['id'] . " " . $date_wh . " order by C.expiration_date ";
                                        /* 	if($Row['id']==18)
                                          {

                                          } */
//                $active_campaigns = $objDB->execute_query($Sql);
                                        $active_campaigns = $objDB->Conn->Execute("Select C.* from campaigns C , campaign_location CL , locations l  where l.id=CL.location_id and C.id = CL.campaign_id and  C.visible = ? and CL.active<>? and CL.location_id=? " . $date_wh . " order by C.expiration_date ", array(1, 1, $Row['id']));
                                        /* if($Row['id']==18)
                                          {
												echo "<br />==============================". $active_campaigns->RecordCount()."========================<br />";
                                          } */
                                        $i = 0;
                                        //echo $Sql;
										//echo "<br />==============================". $active_campaigns->RecordCount()."========================<br />";
                                        while ($l = $active_campaigns->FetchRow()) {
                                                if ($i < ($num_activation - $active_campaign_at_location)) {
                                                        /* if($Row['id']==18)
                                                          {
                                                          echo "In IF";
                                                          } */
                                                        if ($counter_no_of_campaigns != 0) {
                                                                $array_values = array();
                                                                $where_clause = array();
                                                                $array_values['campaign_id'] = $l['id'];
                                                                $array_values['location_id'] = $Row['id'];
                                                                $where_clause['active'] = 1;
                                                                $where_clause['active_in_future'] = 0;

                                                                $i++;
                                                                /*  this is for only count once     */
                                                                /* $sql_is_active = "select * from campaign_location where campaign_id=".$l['id']." and active=1";
                                                                  $is_active_before =$objDB->execute_query( $sql_is_active); */
                                                                $is_active_before = $objDB->Conn->Execute("select * from campaign_location where campaign_id=? and active=?", array($l['id'], 1));
                                                                $is_active_before_counter = $is_active_before->RecordCount();
                                                                if ($is_active_before_counter == 0) {
                                                                        $counter_no_of_campaigns--;
                                                                }
                                                                $objDBWrt->Update($where_clause, "campaign_location", $array_values);

                                                                /*
                                                                  facebook share
                                                                 */
                                                                /* $sql_sharing="SELECT o.created_by,o.title,o.campaign_tag,o.deal_detail_description,o.business_logo,lo.location_id,o.id,lo.active,lo.permalink,lo.campaign_type FROM campaigns as o, campaign_location as lo WHERE o.id=".$l['id']." and lo.campaign_id=o.id";
                                                                  $result_campign = mysql_query($sql_sharing); */
                                                                $result_campign = $objDB->Conn->Execute("SELECT o.created_by,o.title,o.campaign_tag,o.deal_detail_description,o.business_logo,lo.location_id,o.id,lo.active,lo.permalink,lo.campaign_type FROM campaigns as o, campaign_location as lo WHERE o.id=? and lo.campaign_id=o.id", array($l['id']));
//                                                            while( $campaigndata = mysql_fetch_array($result_campign)){   
                                                                while ($campaigndata = $result_campign->FetchRow()) {
                                                                        if ($campaigndata['campaign_type'] == "1") {
                                                                                /* $sql_loc="select * from locations where id=".$campaigndata['location_id'];

                                                                                  $result_location_detail = mysql_query($sql_loc); */
                                                                                $result_location_detail = $objDB->Conn->Execute("select * from locations where id=?", array($campaigndata['location_id']));


                                                                                if ($campaigndata['business_logo'] != "") {
                                                                                        // $img_src=WEB_PATH."/merchant/images/logo/".$RS[0]->business_logo; 
                                                                                        $fb_img_share = ASSETS_IMG . "/m/campaign/" . $campaigndata['business_logo'];
                                                                                } else {
                                                                                        $fb_img_share = ASSETS_IMG . "/c/Merchant_Offer.png";
                                                                                }
                                                                                $tag_main = "";
                                                                                if ($campaigndata['campaign_tag'] != "") {
                                                                                        $fb_campaign_tag_temp = explode(",", $campaigndata['campaign_tag']);
                                                                                        $tag_count = count($fb_campaign_tag_temp);



                                                                                        for ($i = 0; $i < $tag_count; $i++) {
                                                                                                $tag_main.="#" . $fb_campaign_tag_temp[$i] . " ";
                                                                                        }
                                                                                }

                                                                                // while( $userData = mysql_fetch_array($result_location_detail)){
                                                                                while ($userData = $result_location_detail->FetchRow()) {

                                                                                        //When Location is yes then campaign share this location
                                                                                        if ($userData['facebook_page_access_token'] != "" && $userData['facebook_pageid'] != "") {
                                                                                                if ($userData['location_publish'] == "1") {
                                                                                                        try {
                                                                                                                $attachment = array(
                                                                                                                        'name' => $campaigndata['title'],
                                                                                                                        'link' => $campaigndata['permalink'],
                                                                                                                        'description' => $campaigndata['deal_detail_description'],
                                                                                                                        'picture' => $fb_img_share,
                                                                                                                        'access_token' => $userData['facebook_page_access_token'],
                                                                                                                        'message' => 'Launched campaign today on #scanflip' . ' ' . $tag_main,
                                                                                                                );
                                                                                                                $status = $facebook->api("/" . $userData['facebook_pageid'] . "/feed", "post", $attachment);
                                                                                                        } catch (FacebookApiException $x) {


                                                                                                                $sql_merchant_user_detail = "select * from merchant_user where id=" . $campaigndata['created_by'];
                                                                                                                $merchant_detail_data = $objDB->Conn->Execute($sql_merchant_user_detail);
                                                                                                                $mail = new PHPMailer();

                                                                                                                $email_address = $merchant_detail_data->fields['email'];

                                                                                                                $body = "";

                                                                                                                $body = "<div>" . $merchant_msg["manage-social"]["msg_not_send_post_body"] . "</div>";

                                                                                                                $mail->AddReplyTo('no-reply@scanflip.com', 'ScanFlip Support');

                                                                                                                $mail->AddAddress($email_address);

                                                                                                                $mail->From = "no-reply@scanflip.com";
                                                                                                                $mail->FromName = "ScanFlip Support";
                                                                                                                $mail->Subject = $merchant_msg["manage-social"]["msg_not_send_post_subject"];
                                                                                                                $mail->MsgHTML($body);
                                                                                                                $mail->Send();
                                                                                                        }
                                                                                                }
                                                                                                //End When Location is yes then campaign share this location
                                                                                        }
                                                                                }
                                                                        }
                                                                }
                                                                /* End of facebook share */
                                                                /* this is for only count once */
                                                                //$counter_no_of_campaigns--;
                                                        } else {
                                                                $array_values = array();
                                                                $where_clause = array();
                                                                $array_values['campaign_id'] = $l['id'];
                                                                $array_values['location_id'] = $Row['id'];
                                                                $where_clause['active'] = 0;
                                                                $where_clause['active_in_future'] = 1;
                                                                $objDBWrt->Update($where_clause, "campaign_location", $array_values);
                                                                $i++;
                                                        }
                                                } else {
                                                        /* if($Row['id']==18)
                                                          {

                                                          } */
                                                        $array_values = array();
                                                        $where_clause = array();
                                                        $array_values['campaign_id'] = $l['id'];
                                                        $array_values['location_id'] = $Row['id'];
                                                        $where_clause['active'] = 0;
                                                        $where_clause['active_in_future'] = 1;
                                                        $objDBWrt->Update($where_clause, "campaign_location", $array_values);
                                                        $i++;
                                                }
                                        }
                                        //  }
                                        /* $query_updatenooflocation = "update merchant_user set total_no_of_campaign=".$counter_no_of_campaigns." where id=".$Row['created_by'];

                                          $objDB->execute_query($query_updatenooflocation); */
                                        $objDB->Conn->Execute("update merchant_user set total_no_of_campaign=? where id=?", array($counter_no_of_campaigns, $Row['created_by']));
                                }
                        }
                }
                ?>
                <?php

                /* for add block points inmerchant's avialble points when cmapign exipre */
                $sql = "SELECT distinct b.* from campaign_location a  INNER JOIN campaigns b on a.campaign_id=b.id INNER JOIN locations c on a.location_id=c.id where 
 b.expiration_date < CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1))  and b.id not in (
SELECT y.id from campaign_location x  INNER JOIN campaigns y on x.campaign_id=y.id INNER JOIN locations z on x.location_id=z.id 
 WHERE CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(z.timezone,1, POSITION(',' IN z.timezone)-1)) BETWEEN y.start_date and y.expiration_date and ( x.active_in_future<>1 OR x.active=1 ) ) and a.active_in_future<>1 and a.active<>1 ";

//$RS_data = $objDB->execute_query($sql);
                $RS_data = $objDB->Conn->Execute($sql);

//echo $RS_data->RecordCount();
                if ($RS_data->RecordCount() > 0) {
                        while ($rc = $RS_data->FetchRow()) {
                                //if campaign expire then unlink qrcode

                                //$sql = "select qrcode_id from qrcode_campaign where campaign_id =" . $rc['id'];
								$sql = "select qrcode_id from campaigns where id =" . $rc['id'];
								
                                $RS = $objDB->Conn->Execute($sql);
                                $qrcode_id = $RS->fields['qrcode_id'];


                                if ($qrcode_id != "") 
                                {
									/*
									$sql_delete = "Delete  from qrcode_campaign where campaign_id =" . $rc['id'];
									$objDBWrt->Conn->Execute($sql_delete);
									*/
									$objDBWrt->Conn->Execute("Update campaigns set qrcode_id=0 where id= ?",array($rc['id']));
									
									$Sql_update = "Update qrcodes set reserve=0  where id= " . $qrcode_id;

									$objDBWrt->Conn->Execute($Sql_update);
                                }
                                //if campaign expire then unlink qrcode
                                $avail_point = $rc['block_point'];

                                $user_avial_sql = "select available_point from merchant_user where id=" . getmainmercahnt_id($rc['created_by']);
                                $rs_use_avail = $objDB->Conn->Execute($user_avial_sql);
                                $a_p = $rs_use_avail->fields['available_point'];
                                $t_p = $a_p + $avail_point;


                                $u_sql = "Update merchant_user set available_point=" . $t_p . "  where id=" . getmainmercahnt_id($rc['created_by']);

                                $objDBWrt->Conn->Execute($u_sql);
                                if ($avail_point != 0) {
                                        $c_u_sql = "Update campaigns set block_point=0 ,point_credited = " . $avail_point . " where id=" . $rc['id'];
                                } else {
                                        $c_u_sql = "Update campaigns set block_point=0 where id=" . $rc['id'];
                                }

//                      $objDB->execute_query( $c_u_sql);
                                $objDBWrt->Conn->Execute($c_u_sql);
//                      $sql_c_l_a = "Select * from campaign_location where campaign_id=".$rc['id'];
//                      $rs_cancel_act =$objDB->execute_query( $sql_c_l_a);
//                      while($can_act = $rs_cancel_act->FetchRow())
//                      {
//                          
//                      }
                        }
                }
                /* for add block points inmerchant's avialble points when cmapign exipre */


                /* for add sharing point to avialable points of merchant */

                $Sql = "select * from campaigns c where c.id not in ( select cl.campaign_id from campaign_location )";

                $sql = "SELECT distinct b.* from campaign_location a  INNER JOIN campaigns b on a.campaign_id=b.id INNER JOIN locations c on a.location_id=c.id where  
 ( a.num_activation_code <=0  AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date ) and  b.id not in (
SELECT y.id from campaign_location x  INNER JOIN campaigns y on x.campaign_id=y.id INNER JOIN locations z on x.location_id=z.id 
 WHERE x.active=1 or x.active_in_future=1)and a.active_in_future<>1 and a.active<>1 ";
                //$RS_data = $objDB->execute_query($sql);
                $RS_data = $objDB->Conn->Execute($sql);

//echo $RS_data->RecordCount();
                if ($RS_data->RecordCount() > 0) {
                        while ($rc = $RS_data->FetchRow()) {
                                $avail_point = $rc['block_point'];
                                $user_avial_sql = "select available_point from merchant_user where id=" . getmainmercahnt_id($rc['created_by']);
//                       $rs_use_avail =$objDB->execute_query( $user_avial_sql);
                                $rs_use_avail = $objDB->Conn->Execute($user_avial_sql);
                                $a_p = $rs_use_avail->fields['available_point'];

                                $t_p = $a_p + $avail_point;
                                $u_sql = "Update merchant_user set available_point=" . $t_p . "  where id=" . getmainmercahnt_id($rc['created_by']);

                                //$objDB->execute_query( $u_sql);
                                $objDBWrt->Conn->Execute($u_sql);
                                /* $c_u_sql ="Update campaigns set block_point=0 where id=".$rc['id'];

                                  $objDB->execute_query( $c_u_sql); */
                                $objDBWrt->Conn->Execute($c_u_sql);
                        }
                }

                $Sql = "select c.id from campaigns c where c.id not in ( select cl.campaign_id from campaign_location cl)";
//$RS_data = $objDB->execute_query($Sql);	
                $RS_data = $objDB->Conn->Execute($Sql);

                if ($RS_data->RecordCount() > 0) 
                {
                        while ($rc = $RS_data->FetchRow()) 
                        {
							/*
							$sql = "select qrcode_id from qrcode_campaign where campaign_id =" . $rc['id'];
							$RS = $objDB->Conn->Execute($sql);
							*/
							$RS = $objDB->Conn->Execute("select qrcode_id from campaigns where id =? and qrcode_id!=0",array($rc['id']));
							
							$qrcode_id = $RS->fields['qrcode_id'];


							if ($qrcode_id != "") 
							{
								/*	
								$sql_delete = "Delete  from qrcode_campaign where campaign_id =" . $rc['id'];
								$objDBWrt->Conn->Execute($sql_delete);
								*/
								$objDBWrt->Conn->Execute("Update campaigns set qrcode_id=0 where id= ?",array($rc['id']));
								
								$Sql_update = "Update qrcodes set reserve=0  where id= " . $qrcode_id;

								$objDBWrt->Conn->Execute($Sql_update);
							}
                        }
                }
                /* for add sharing point to avialable points of merchant */
                
                /* delink the qrcodes whose cmapigns donot exists */
                
                /*
                
                $Sql_campaign = "select campaign_id id from qrcode_campaign where campaign_id not in( select  c.id from campaigns c)";

				//$RS_data = $objDB->execute_query($Sql_campaign);	
                $RS_data = $objDB->Conn->Execute($Sql_campaign);

				
                if ($RS_data->RecordCount() > 0) 
                {
                        while ($rc = $RS_data->FetchRow()) 
                        {

                                $sql = "select qrcode_id from qrcode_campaign where campaign_id =" . $rc['id'];

                                $RS = $objDB->Conn->Execute($sql);
                                $qrcode_id = $RS->fields['qrcode_id'];


                                if ($qrcode_id != "") 
                                {
                                        $sql_delete = "Delete  from qrcode_campaign where campaign_id =" . $rc['id'];

                                        $objDBWrt->Conn->Execute($sql_delete);

                                        $Sql_update = "Update qrcodes set reserve=0  where id= " . $qrcode_id;

                                        $objDBWrt->Conn->Execute($Sql_update);
                                }
                        }
                }
                
                $Sql_location = "select location_id id  from qrcode_location where location_id not in( select  l.id from locations l)";
				//$RS_data = $objDB->execute_query($Sql_location);	
                $RS_data = $objDB->Conn->Execute($Sql_location);
				
                if ($RS_data->RecordCount() > 0) 
                {
                        while ($rc = $RS_data->FetchRow()) 
                        {
                                $sql = "select qrcode_id from qrcode_location where location_id =" . $rc['id'];
                                $RS = $objDB->Conn->Execute($sql);
                                
                                $qrcode_id = $RS->fields['qrcode_id'];

                                if ($qrcode_id != "") 
                                {
									$sql_delete = "Delete  from qrcode_location where location_id =" . $rc['id'];
									$objDBWrt->Conn->Execute($sql_delete);

									$Sql_update = "Update qrcodes set reserve=0  where id= " . $qrcode_id;
									$objDBWrt->Conn->Execute($Sql_update);
                                }
                        }
                }
                
                */
                
                /* delink the qrcodes whose cmapigns donot exists */
                
                

				//include_once("Config.Inc.php");
                

                /*                 * **************** new campaign logic start *********** */
                /*
                  $limit_data = "SELECT distinct c.id campid
                  FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id WHERE l.active = 1
                  AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0
                  and CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between
                  CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and
                  ( CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY )";
                 */
                $limit_data = "SELECT distinct c.id campid
FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id WHERE l.active = 1
 AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0 
 and (CONVERT_TZ(now(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
 DATE_SUB(c.start_date, INTERVAL 2 MINUTE)  and 
 ( c.start_date + INTERVAL 1 DAY) )";


                $RS_limit_data = $objDB->Conn->Execute($limit_data);
                $campaign_list = "";

                if ($RS_limit_data->RecordCount() > 0) {
                        while ($Row_camps = $RS_limit_data->FetchRow()) {
                                $campaign_list .= $Row_camps['campid'] . ",";
                        }
                        $campaign_list = trim($campaign_list, ",");
                        if ($campaign_list != "") {
                                $sql_update_new_24 = "update campaigns set is_new=1 where id in (" . $campaign_list . ")";
                                //echo $sql_update_new_24;
                                $objDBWrt->Conn->Execute($sql_update_new_24);

                                $sql_update_new_24 = "update campaigns set is_new=0 where id not in (" . $campaign_list . ")";
                                $objDBWrt->Conn->Execute($sql_update_new_24);
                        }
                } else {
                        $sql_update_new_24 = "update campaigns set is_new=0";
                        $objDBWrt->Conn->Execute($sql_update_new_24);
                }
                /*                 * **************** new campaign logic end *********** */
        }

        $array_values_as = $array_where_as = array();
        $array_values_as['action'] = 0;
        $array_where_as['id'] = 4;
        $objDBWrt->Update($array_values_as, "admin_settings", $array_where_as);
}

function compileHours($times, $timestamp) {
        if (isset($times[strtolower(date('D', $timestamp))])) {
                $times = $times[strtolower(date('D', $timestamp))];
                if (!strpos($times, '-'))
                        return array();
                $hours = explode(",", $times);
                $hours = array_map('explode', array_pad(array(), count($hours), '-'), $hours);
                $hours = array_map('array_map', array_pad(array(), count($hours), 'strtotime'), $hours, array_pad(array(), count($hours), array_pad(array(), 2, $timestamp)));
                end($hours);
                if ($hours[key($hours)][0] > $hours[key($hours)][1])
                        $hours[key($hours)][1] = strtotime('+1 day', $hours[key($hours)][1]);
                return $hours;
        }
}

function isOpen($now, $times) {
        $open = 0; // time until closing in seconds or 0 if closed
        // merge opening hours of today and the day before

        if (is_array(compileHours($times, strtotime('yesterday', $now))) && is_array(compileHours($times, $now))) {

                $hours = array_merge(compileHours($times, strtotime('yesterday', $now)), compileHours($times, $now));

                foreach ($hours as $h) {
                        if ($now >= $h[0] and $now < $h[1]) {
                                $open = $h[1] - $now;
                                return $open;
                        }
                }
                return $open;
        } else {
                return 0;
        }
}

function getmainmercahnt_id($id) {

        $objDB = new DB();
        $Sql = "select merchant_parent from merchant_user where id=" . $id;
        $rs = $objDB->execute_query($Sql);

        if ($rs->fields['merchant_parent'] == 0) {

                return $id;
        } else {
                //  $objDB = new DB();
                //$mainid= $rs->fields['merchant_parent'];
                return getmainmercahnt_id($rs->fields['merchant_parent']);
                //call_user_func("get_main_merchant_id",$mainid);
        }
}
?>
