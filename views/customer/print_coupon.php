<?php
/* * ****** 
  @USE : print coupon page
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : campaign.php
 * ******* */
//require_once("classes/Config.Inc.php");
check_customer_session();

//require_once(SERVER_PATH."/classes/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();
//$objDBWrt = new DB('write');
//if($_SESSION['customer_id'] != ""){
//   $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id =( select  id from merchant_groups where location_id  =".$_REQUEST['l_id']." and private = 1 )";
//       
//        $RS_check_s = $objDB->Conn->Execute($Sql_new);
//        if($RS_check_s->RecordCount()<=0)
//        { 
//                $url = WEB_PATH."/activate-deal.php?campaign_id=".$_REQUEST['campaign_id']."&l_id=".$_REQUEST['l_id'];//."--------------------";
//		header("Location: ".$url);
//		exit;
//        }  
//}
//Make entry in subscribed_stre table for first time subscribe to loaction
//                        $sql_group = "select id , merchant_id from merchant_groups where location_id =".$_REQUEST['l_id']." and private = 1";
//                        $RS_group = $objDB->Conn->Execute($sql_group);
//                  
//                          $sql_chk ="select * from subscribed_stores where customer_id= ".$_SESSION['customer_id']." and location_id=".$_REQUEST['l_id'];
//                          $subscibed_store_rs =$objDB->Conn->Execute($sql_chk);
//                        if($subscibed_store_rs->RecordCount()==0)
//                        {
//                        $insert_subscribed_store_sql ="insert into subscribed_stores set customer_id= ".$_SESSION['customer_id']." ,location_id=".$_REQUEST['l_id']." ,subscribed_date='".date('Y-m-d H:i:s')."' ,subscribed_status=1";
//                          $objDB->Conn->Execute($insert_subscribed_store_sql);
//                        }
//                        else {
//                            if($subscibed_store_rs->fields['subscribed_status']==0)
//                            {
//                                $up_subscribed_store = "Update subscribed_stores set subscribed_status=1  where  customer_id= ".$_SESSION['customer_id']." and location_id=".$_REQUEST['l_id'];
//                                 $objDB->Conn->Execute($up_subscribed_store);
//                            }
//                        }
//
if ($_SESSION['customer_id'] != "") {
//     $Sql_new = "SELECT * FROM merchant_subscribs WHERE user_id='".$_SESSION['customer_id']."' AND group_id in( select  id from merchant_groups where location_id  =".$_REQUEST['l_id']."  and private = 1)";
//       
//        $RS_check_s = $objDB->Conn->Execute($Sql_new);
//        if($RS_check_s->RecordCount()<=0)
//        { 
//            $sql_group = "select id , merchant_id from merchant_groups where location_id =".$_REQUEST['l_id']." and private = 1";
//                   $RS_group = $objDB->Conn->Execute($sql_group);
//                    $array_group= array();
//                            $array_group['merchant_id']=$RS_group->fields['merchant_id'];
//                            $array_group['group_id']=$RS_group->fields['id'];
//                            $array_group['user_id']=$_SESSION['customer_id'];
//                            $objDB->Insert($array_group, "merchant_subscribs");
//        }  
}

if (isset($_REQUEST['print_coupon_back'])) {
        // header("Location:".WEB_PATH."/my-deals.php");
}

if (isset($_REQUEST['campaign_id']) && isset($_REQUEST['l_id'])) {
        /* $Sql_new = "SELECT * FROM customer_campaigns WHERE customer_id='".$_SESSION['customer_id']."' AND campaign_id='".$_REQUEST['campaign_id']."' AND location_id =".$_REQUEST['l_id'];
          $RS_new = $objDB->Conn->Execute($Sql_new); */
        $RS_new = $objDB->Conn->Execute("SELECT * FROM customer_campaigns WHERE customer_id=? AND campaign_id=? AND location_id =?", array($_SESSION['customer_id'], $_REQUEST['campaign_id'], $_REQUEST['l_id']));

        if ($RS_new->RecordCount() <= 0) {
                /* $Sql = "SELECT * FROM activation_codes WHERE campaign_id=".$_REQUEST['campaign_id'];
                  $RS = $objDB->Conn->Execute($Sql); */
                $RS = $objDB->Conn->Execute("SELECT * FROM activation_codes WHERE campaign_id=?", array($_REQUEST['campaign_id']));

                if ($RS->RecordCount() > 0) {
                        $activation_code = $RS->fields['activation_code'];
                        /* $Sql = "INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
                          customer_id='".$_SESSION['customer_id']."', campaign_id=".$_REQUEST['campaign_id']." , location_id=".$_REQUEST['l_id'];
                          $objDB->Conn->Execute($Sql); */
                        $objDBWrt->Conn->Execute("INSERT INTO customer_campaigns SET activation_status='1', activation_code='$activation_code', activation_date= Now(), coupon_generation_date=Now(),
				customer_id=?, campaign_id=?, location_id=?", array($_SESSION['customer_id'], $_REQUEST['campaign_id'], $_REQUEST['l_id']));
                }
        }
}
$br = $_REQUEST['barcodea'];
if (isset($_REQUEST['barcodea'])) {
        $where_clause = array();
        $where_clause['id'] = $_REQUEST['campaign_id'];
        $RSCompDetails = $objDB->Show("campaigns", $where_clause);
        $where_clause = array();
        $where_clause['campaign_id'] = $_REQUEST['campaign_id'];
        $where_clause['location_id'] = $_REQUEST['l_id'];
        $RSComp = $objDB->Show("campaign_location", $where_clause);
        $campaign_redirecting_url = $RSComp->fields['permalink'];
        if ($RSComp->RecordCount() > 0) {
                while ($Row = $RSComp->FetchRow()) {
                        $where_clause = array();
                        $where_clause['id'] = $Row['location_id'];
                        $RSLocation = $objDB->Show("locations", $where_clause);
                }
        }

        $array = $json_array = array();
        $array['customer_id'] = $_SESSION['customer_id'];
        $array['customer_campaign_code'] = $_REQUEST['campaign_id'];
        $array['location_id'] = $_REQUEST['l_id'];
        $RS = $objDB->Show("coupon_codes", $array);
        if ($RS->RecordCount() > 0) {
                $array['generated_date'] = date('Y-m-d H:i:s');
                $where_clause_arr['customer_id'] = $_SESSION['customer_id'];

                $where_clause_arr['location_id'] = $_REQUEST['l_id'];
                $where_clause_arr['customer_campaign_code'] = $_REQUEST['campaign_id'];
                $array['active'] = 1;
                $objDB->Update($array, "coupon_codes", $where_clause_arr);
        } else {
                //echo "<pre>";print_r($_REQUEST);echo "</pre>";
                $RS = $objDB->Show("coupon_codes", $array);

                $array_ = $json_array = array();
                $array_['customer_id'] = $_SESSION['customer_id'];
                $array_['customer_campaign_code'] = $_REQUEST['campaign_id'];
                $array_['coupon_code'] = $br;
                $array_['active'] = 1;
                $array_['location_id'] = $_REQUEST['l_id'];
                $array_['generated_date'] = date('Y-m-d H:i:s');
                $objDB->Insert($array_, "coupon_codes");

                /* $Sql_num_activation = "Select offers_left , used_offers from campaign_location where campaign_id=".$_REQUEST['campaign_id']." and location_id =".$_REQUEST['l_id']." ";
                  // echo  "<br/>".$Sql_num_activation ."<br/>";
                  $RS_num_activation = $objDB->Conn->Execute($Sql_num_activation); */
                $RS_num_activation = $objDB->Conn->Execute("Select offers_left , used_offers from campaign_location where campaign_id=? and location_id =?", array($_REQUEST['campaign_id'], $_REQUEST['l_id']));

                $offers_left = $RS_num_activation->fields['offers_left'];
                $used_campaign = $RS_num_activation->fields['used_offers'];

                /* $update_num_activation = "Update  campaign_location set offers_left=".($offers_left-1)." , used_offers=".($used_campaign+1)." where campaign_id=".$_REQUEST['campaign_id']." and location_id =".$_REQUEST['l_id']." ";
                  //echo $update_num_activation;
                  $objDB->Conn->Execute($update_num_activation); */
                $objDBWrt->Conn->Execute("Update  campaign_location set offers_left=?, used_offers=? where campaign_id=? and location_id =?", array(($offers_left - 1), ($used_campaign + 1), $_REQUEST['campaign_id'], $_REQUEST['l_id']));
        }
}

if (isset($_REQUEST['new-location'])) {
        $array_values = $where_clause = $array = array();
        $array_values['current_location'] = mysql_escape_string($_REQUEST['new-location']);
        $where_clause['id'] = $_SESSION['customer_id'];
        $objDB->Update($array_values, "customer_user", $where_clause);
}
//$objJSON = new JSON();
if (isset($_REQUEST['order'])) {
        $JSON = $objJSON->get_customer_deals("", $_REQUEST['order']);
        $JSON = json_decode($JSON);
        $_SESSION['customer_id'];
        $where_clause = $_SESSION['customer_id'];
        $RScst = $objDB->Show("customer_user", $where_clause);
        if ($RScst->RecordCount() > 0) {
                while ($Row = $RScst->FetchRow()) {
                        $where_clause = array();
                        $where_clause['id'] = $_SESSION['customer_id'];
                        $RSCust = $objDB->Show("customer_user", $where_clause);
                        $Custemail = $RSCust->fields['emailaddress'];
                        $Custfsname = $RSCust->fields['firstname'];
                }
        }
}
if (isset($_REQUEST['print_coupon'])) {

        $br = $_REQUEST['bna'];

        $to_email = $_REQUEST['emailto'];

        $msg_content = $_POST["coupon_content_print"];
        /*
          $msg_html	=
          "<body>
          Hello ".$Custfsname." <br/><br/>
          Please find the copy of your saved offer.<br/><br/>
          ".$msg_content."
          <br/><br/>
          Thank You, <br/>
          Scanflip Support Team
          </body>";
         */
        /*
          $barcode_send	= mail($to_email, $subject, $msg, $headers);
          if ( $barcode_send == true )
          {
          header("Location: ".WEB_PATH."/my-deals.php");

          } else {

          header("Location: ".WEB_PATH."/print_coupon.php?barcodea=$br");
          }
         */


        $cid = $_POST['hdn_campaign_id'];
        $lid = $_POST['hdn_location_id'];
        $barcode = $_POST['bna'];
        $objDB = new DB();
        $where_clause = array();
        $where_clause['id'] = $cid;
        $RSCompDetails = $objDB->Show("campaigns", $where_clause);

        $where_clause_l = array();
        $where_clause_l['id'] = $lid;
        $loc_nm = $objDB->Show("locations", $where_clause_l);



        $title = $RSCompDetails->fields['title'];
        $expdate = $RSCompDetails->fields['expiration_date'];
        $expdate = date("m/d/y g:i:s A", strtotime($expdate));
        $discont = $RSCompDetails->fields['discount'];
        $desc = $RSCompDetails->fields['deal_detail_description'];
        $terms = $RSCompDetails->fields['terms_condition'];
        $rpoint = $RSCompDetails->fields['redeem_rewards'];
        $timzone = $RSCompDetails->fields['timezone'];

        $where_x = array();
        $where_x['campaign_id'] = $cid;
        $CodeDetails = $objDB->Show("activation_codes", $where_x);
        $act_code = $CodeDetails->fields['activation_code'];

        $where_clause_m = array();
        $where_clause_m['id'] = $RSCompDetails->fields['created_by'];
        $merchant_user = $objDB->Show("merchant_user", $where_clause_m);

        $merchant_icon = $merchant_user->fields['merchant_icon'];


        $msg_html = '<body>
    Hello ' . $Custfsname . ', <br/><br/>
        Please find the copy of your saved offer.<br/><br/>';


        $msg_html.='
<div id="coupon_div" style="-webkit-print-color-adjust:exact; ">
    
		<div style="border: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px; border-bottom: none;" class="c_div">            
            <div style="padding: 5px; font-weight: bold; font-size: 18px; color: rgb(255, 255, 255); background: none repeat scroll 0px 0px rgb(35, 30, 30);height:15px;" class="c_title">
                <span style="float: right; font-weight: normal; font-size: 15px;font-weight: bold" class="expr_div">Expiration Date :- ' . $expdate . '</span>
            </div>
            <div class="main_top_img" style="overflow:hidden;margin:10px 0 0;float:left;width:380px;position:relative;">            
                <div class="main_merimg" style="float:left;">
                    <div class="merc_image" style="overflow:hidden;border: 1px solid white;width:133px;">';

        $img_src = "";
        if ($loc_nm->fields['picture'] != "") {
                $img_src = ASSETS_IMG . "/m/location/" . $loc_nm->fields['picture'];
        } else if ($merchant_icon != "") {
                $img_src = ASSETS_IMG . "/m/icon/" . $merchant_icon;
        } else {
                $img_src = ASSETS_IMG . "/m/default_merchant_icon.jpg";
        }

        $msg_html.='<img src="' . $img_src . '" style="max-width:125px;height:auto!important;width:auto!important;max-height:150px;border: 4px solid #fff;"/>
                    </div>
                    <div class="scanf_logo businessname" style="text-overflow:ellipsis;white-space: nowrap;width:238px;overflow:hidden;padding:7px 0 0;font:bold 16px/18px Arial;color:#FC930B;text-decoration:none;">';

        $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $lid);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
        $busines_name = $json->bus_name;
        $msg_html.= $busines_name . '
                                                 
                </div>
                </div> 
               
                
                <div class="dis_title" style="border:0px;width: 343px;margin:20px 0 0;">';

        if ($discont != "") {
                /*
                  $msg_html.= '<span class="offer_span" style="width:120px;height:110px;text-align:center;position:absolute !important;padding:0px 0 0;right:28px;top:0px;font-size:35px;"> <img src="'.WEB_PATH .'/templates/images/offer_bg.png" style="display:block"/><div class="img_span" style="position: absolute !important;top:13px !important;left: 0px !important;width: 100%;color:#FFFFFF;">'.$discont.'</div></span>';
                 */
                $msg_html.= '<span class="offer_span" style="width:120px;height:110px;text-align:center;position:absolute;padding:0px 0 0;right:28px;top:0px;font-size:35px; "> <div class="img_span" style="
 position: absolute; right: -120px;background:#ff0000;  border-radius: 40px;top: 43px;left: 0px;width: 27%;color:#FFFFFF;margin-left:67%;">' . $discont . '</div></span>';
        }

        $msg_html.='<div style="margin: 10px 0 0 0;"><img src="' . WEB_PATH . '/showbarcode.php?br=' . $br . '" alt="barcode" /></div>
                    
                    
                </div>
            </div>
            <div class="right_coupon_div" style="border:2px dashed #000000;float:left;height:214px;margin:13px 0 0;overflow:hidden;padding:10px;width:415px;">
                <div id="right_title" style="width:100%;font-size:15px; color:#333; text-align:justify; font-weight: bold;"> 
                    ' . $title . '
                </div>
            	<div class="coupon_desc" style="font-size:13px;margin:0px;padding:0px!important;text-align:justify;">
                    ' . $desc . '
                </div>  
               
            </div>
                    
        </div>';



        $str = "";
        if ($RSCompDetails->fields['number_of_use'] == 1) {
                $str = "* Limit one per customer, Not valid with any other offer";
        } elseif ($RSCompDetails->fields['number_of_use'] == 2) {
                $str = "* Limit one per customer per day , not valid with any other offer";
        } elseif ($RSCompDetails->fields['number_of_use'] == 3) {
                $str = "* Earn Redemption Points On Every Visit";
        }

        $arr = file(WEB_PATH . '/process.php?get_location_name_of_campaigns=yes&camp_id=' . $cid);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
        $locations = $json->records;
        $t_records = $json->total_records;
        $cnt = 1;
        if ($t_records > 1 || $str != "") {
                $msg_html.='<div class="c_middle_div" style="border-left: 1px solid rgb(0, 0, 0);  border-right: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px; border-top:1px solid #ccc;border-bottom: none;">';
        }


        if ($t_records > 1 || $str != "") {
                $str1 = "<div style=' float:left;'>Please advice crew member of offer prior to ordering  </div>";
                $msg_html.='<div class="coupon_use" style=" font-size: 15px; width:100%;">';
                $msg_html.= $str1;
                $msg_html.= '<div style="float:right;">' . $str . '</div>';
                $msg_html.= '</div>';
        }
        if ($t_records > 1 || $str != "") {
                $msg_html.='</div>';
        }


        $msg_html.='<div class="c_last_div" style="  border: 1px solid rgb(0, 0, 0);  padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px;text-align:left;">
                <span class="contents_location" style="padding-left:10px;float:left;display:block;line-height:18px;">Where to Redeem :- </span>';



        $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $loc_nm->fields['id']);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
        $busines_name = $json->bus_name;




        $msg_html.='<span style="padding-left:10px;max-width:228px;float:left;display:block;line-height:18px;">';

        $address = $loc_nm->fields['address'] . ", " . $loc_nm->fields['city'] . ", " . $loc_nm->fields['state'] . ", " . $loc_nm->fields['zip'] . ", " . $loc_nm->fields['country'];
        $msg_html.=$address . '; 
                                            </span>
                                            
                                            <span  style="padding-left:10px;float:left;display:block;line-height:18px;">Ph# (' . substr($loc_nm->fields['phone_number'], 4, 3) . ') ' . substr($loc_nm->fields['phone_number'], 8) . '</span>
                                            <span class="contents_location" style="float:left;display:block;line-height:18px;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                 <span class="contents_location" style="float:left;display:block;line-height:18px;">Redemption point :- </span><span style="font-weight: bold; padding-left: 10px;float:left;display:block;line-height:18px;">' . $rpoint . '</span><span class="contents_location" style="float:left;display:block;line-height:18px;">&nbsp;&nbsp;|&nbsp;&nbsp;</span> 
                <div class="head_lg" style="float:right;overflow: hidden;height:18px;"><img style="max-width:85px;" src="' . ASSETS_IMG . '/c/header-logo.jpg" alt="Scanflip"/></div>
            </div>
<div class="coupon_terms" style="font-size:13px;text-align:justify;margin-left:63px;width:822px;margin-top: 10px;padding:10px!important;border:1px solid black">
       <div style="font-weight: bold;">
           Terms & Condition :
       </div>'
                . $terms .
                '</div> 
    </div>';

        $msg_html.='<br/>Thank You, <br/>
    Scanflip Support Team
</body>';

        $mail = new PHPMailer();
        $mail->AddReplyTo('no-reply@scanflip.com', 'ScanFlip Support');
        $mail->AddAddress($_REQUEST['emailto']);
        $mail->From = "no-reply@scanflip.com";
        $mail->FromName = "ScanFlip Support";
        $mail->Subject = "Scanflip offer Coupon";
        $mail->MsgHTML($msg_html);
        $mail->Send();



        header("Location: " . WEB_PATH . "/my-deals.php");

        //header("Location: ".WEB_PATH."/my-deals.php");
        exit();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>ScanFlip</title>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?= ASSETS_CSS ?>/c/template.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?= ASSETS_JS ?>/c/jquery-1.6.2.min.js"></script>

        <script type="text/javascript">
                /*
                 $b=jQuery.noConflict();
                 $b(function() {
                 
                 $b('.print').click(function() {
                 //var container = $(this).attr('rel');
                 $b('#coupon_div').printArea();
                 return false;
                 });
                 
                 });
                 */
        </script>

    </head>

    <body>
        <div id="main_parent_print_div" style="display:none">
            <div id="print_coupon_div" >

            </div> 
        </div>
        <?php
        require_once(CUST_LAYOUT . "/header.php");
        ?>
        <div id="content" class="cantent">
            <div class="my_main_div">
                <div id="contentContainer" class="contentContainer">
                    <!--            <div style="font-size: 18px;">Print coupon.</div>-->
                    <br/>
                    <?php
                    $where_clause = array();
                    $where_clause['id'] = $_REQUEST['campaign_id'];
                    $RSCompDetails = $objDB->Show("campaigns", $where_clause);

                    $where_clause_l = array();
                    $where_clause_l['id'] = $_REQUEST['l_id'];
                    $loc_nm = $objDB->Show("locations", $where_clause_l);



                    $title = $RSCompDetails->fields['title'];
                    $expdate = $RSCompDetails->fields['expiration_date'];
                    $expdate = date("m/d/y g:i:s A", strtotime($expdate));
                    $discont = $RSCompDetails->fields['discount'];
//$desc = $RSCompDetails->fields['deal_detail_description'];
                    $desc = $RSCompDetails->fields['description'];
                    $terms = $RSCompDetails->fields['terms_condition'];
                    $rpoint = $RSCompDetails->fields['redeem_rewards'];
                    $timzone = $RSCompDetails->fields['timezone'];

                    $where_x = array();
                    $where_x['campaign_id'] = $_REQUEST['campaign_id'];
                    $CodeDetails = $objDB->Show("activation_codes", $where_x);
                    $act_code = $CodeDetails->fields['activation_code'];

                    $where_clause_m = array();
                    $where_clause_m['id'] = $RSCompDetails->fields['created_by'];
                    $merchant_user = $objDB->Show("merchant_user", $where_clause_m);

                    $merchant_icon = $merchant_user->fields['merchant_icon'];
                    ?>
                    <style>
                        body{ -webkit-print-color-adjust:exact;print-color-adjust: exact; }
                    </style>

                    <div id="coupon_div" class="scan_coupon">

                        <div class="now_scan">            
                            <div class="redeem">
                <!--                <span style="text-transform:uppercase"><?= $title; ?></span>-->
                                <span class="expr_div">Expiration Date :- <?= $expdate; ?></span>
                            </div>
                            <div class="dll_1">     
                                <div >

                                    <div >
                                        <?php
                                        $img_src = "";

                                        $where_clause123 = array();
                                        $where_clause123['id'] = $loc_nm->fields['created_by'];
                                        $RSMer = $objDB->Show("merchant_user", $where_clause123);

                                        if ($RSMer->fields['merchant_icon'] != "") {
                                                $img_src = ASSETS_IMG . "/m/icon/" . $RSMer->fields['merchant_icon'];
                                        } else {
                                                $img_src = ASSETS_IMG . "/c/Merchant.png";
                                        }

                                        $size = getimagesize($img_src);
                                        $height = $size[1];
                                        if ($height >= 111) {
                                                ?>
                                                <img src="<?php echo $img_src ?>" style="width:144px;height:111px;border: 4px solid #fff;"/>
                                                <?php
                                        } else {
                                                ?>
                                                <img src="<?php echo $img_src ?>" style="width:144px;border: 4px solid #fff;"/>
                                                <?php
                                        }
                                        ?>
                                    </div>

                                    <div class="best">
                                        <?php
                                        // for business name 
                                        $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $_REQUEST['l_id']);
                                        if (trim($arr[0]) == "") {
                                                unset($arr[0]);
                                                $arr = array_values($arr);
                                        }
                                        $json = json_decode($arr[0]);
                                        $busines_name = $json->bus_name;
                                        echo $busines_name;
                                        // for business name 
                                        ?></div>
                                </div> 



                            </div>
                            <div class="dll_2">
                                <span>
                                    <?php
                                    echo $title;
                                    ?>
                                </span>
                                <div class="barcode">
                                    <img src="showbarcode.php?br=<?php echo $br; ?>" alt="barcode" />
                                </div>
                                <script>function printpage() {
                                            window.print()
                                        }
                                        function save_image() {
                                            window.open("<?= WEB_PATH ?>" + "/showbarcode.php?br=" + "<?php echo $br; ?>");
                                        }
                                </script>
                            </div>



                        </div>


                        <?php
                        $str = "";
                        if ($RSCompDetails->fields['number_of_use'] == 1) {
                                $str = "* Limit one per customer, Not valid with any other offer";
                        } elseif ($RSCompDetails->fields['number_of_use'] == 2) {
                                $str = "* Limit one per customer per day , not valid with any other offer";
                        } elseif ($RSCompDetails->fields['number_of_use'] == 3) {
                                $str = "* Earn Redemption Points On Every Visit";
                        }
                        //echo WEB_PATH.'/process.php?get_location_name_of_campaigns=yes&camp_id='. $_REQUEST['campaign_id'];
                        $arr = file(WEB_PATH . '/process.php?get_location_name_of_campaigns=yes&camp_id=' . $_REQUEST['campaign_id']);
                        if (trim($arr[0]) == "") {
                                unset($arr[0]);
                                $arr = array_values($arr);
                        }
                        $json = json_decode($arr[0]);
                        $locations = $json->records;
                        $t_records = $json->total_records;
                        $cnt = 1;
                        if ($t_records > 1 || $str != "") {
                                ?>
                                <div class="location_point">
                                    <?php
                            }

                            if ($t_records > 1) {
                                    ?>
                                    <div style="display:none;"> You can find this deal available on following location also.
                                        <?php
                                        foreach ($locations as $Rownew) {
                                                if ($Rownew->id != $_REQUEST['l_id']) {
                                                        $address = $Rownew->address . ", " . $Rownew->city . ", " . $Rownew->state . ", " . $Rownew->zip . ", " . $Rownew->country;
                                                        ?>
                                                        <p style="margin-bottom: 8px;margin-top: 8px;  padding: 0px !important; padding-left: 20px !important;">
                                                            <?php //echo $Rownew->location_name?>
                                                            <?php
                                                            // for business name 
                                                            $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $Rownew->id);
                                                            if (trim($arr[0]) == "") {
                                                                    unset($arr[0]);
                                                                    $arr = array_values($arr);
                                                            }
                                                            $json = json_decode($arr[0]);
                                                            $busines_name = $json->bus_name;
                                                            echo $busines_name;
                                                            // for business name 
                                                            ?>
                                                            &nbsp;<span>(<?= $address ?>)</span></p>
                                                        <?php
                                                }
                                        }
                                        ?>


                                    </div>   
                                    <?php
                            }
                            if ($t_records > 1 || $str != "") {
                                    $str1 = "<div class='location_point_left'>Please advice crew member of offer prior to ordering  </div>";
                                    echo "<div style=' font-size: 15px; width:100%;'>";
                                    echo $str1;
                                    echo "<div class='location_point_right'>" . $str . "</div>";
                                    echo "</div>";
                            }
                            if ($t_records > 1 || $str != "") {
                                    ?>
                                </div>
                        <?php }
                        ?>


                        <div class="advice">
                            <ul>
                                <li>
                                    <span>Where to Redeem :- </span>
                                </li>

<!--<span class="loca_name" style="font-weight:bold; padding-left: 18px">
                                <?php //echo $loc_nm->fields['location_name'];?>
                                <?php
                                // for business name 
                                $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $loc_nm->fields['id']);
                                if (trim($arr[0]) == "") {
                                        unset($arr[0]);
                                        $arr = array_values($arr);
                                }
                                $json = json_decode($arr[0]);
                                $busines_name = $json->bus_name;
                                //echo $busines_name;
                                // for business name 
                                ?>
</span>-->
                                <li>
                                    <span class="address_div">
                                        <?php //echo $loc_nm->fields['address']; ?>
                                        <?php $address = $loc_nm->fields['address'] . ", " . $loc_nm->fields['city'] . ", " . $loc_nm->fields['state'] . ", " . $loc_nm->fields['zip'] . ", " . $loc_nm->fields['country']; ?>
                                        <?php echo $address; ?>
                                    </span>
                                </li>
                                <li>
                                    <span>Ph# (<?= substr($loc_nm->fields['phone_number'], 4, 3); ?>) <?= substr($loc_nm->fields['phone_number'], 8); ?></span>
                                </li>
                                <li><span>| </span></li>
                                <li>
                                    <span>Redemption point:-</span>
                                </li> 
                                <li>
                                    <span><?= $rpoint; ?></span>
                                </li>
                                <li>
                                    <span class="contents_location" >|</span> 
                                </li>
                            </ul>
                            <div  class="scan_barcodelogo"><img style="max-width:85px;" src="<?php echo ASSETS_IMG . "/c/header-logo.jpg"; ?>" alt="Scanflip"/></div>
                        </div>
                        <!--            <div class="c_last_div" style=" border: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px;text-align:right;">
                                        Participating Location :- <div class="straddress">
                                                    <div class="strname" style="font-weight: bold;"><?= $loc_nm->fields['location_name']; ?></div>
                                                    <div class="straddress" style="font-weight: bold;"><?= $loc_nm->fields['address']; ?></div>
                                                    <div class="straphone" style="font-weight: bold;"><?= $loc_nm->fields['phone_number']; ?></div>
                                                </div>&nbsp;&nbsp;|&nbsp;&nbsp; 
                                        Redemption point :- <span style="font-weight: bold;"><?= $rpoint; ?></span>&nbsp;&nbsp;|&nbsp;&nbsp; 
                                        <span style="font-weight: bold;">Scanflip merchant</span>
                                    </div>-->
                        <?php
                        if ($desc != "") {
                                ?>
                                <div class="campaign_coupon">
                                    <div style="font-weight: bold;">
                                        Campaign Description : 
                                    </div> 
                                    <?php
                                    echo $desc;
                                    ?>
                                </div> 
                                <?php
                        }
                        ?>			
                        <div class="terms_condition">
                            <div style="font-weight: bold;">
                                Terms & Condition :
                            </div> <?php
                            if ($terms != "") {
                                    echo $terms . "<p>Additional Terms</p><p>
												No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.
												</p>";
                            } else {
                                    echo "<p>No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.
												</p>";
                            }
                            ?>
                        </div> 
                    </div>



                    <?php
//load_print_coupon_data($_REQUEST['campaign_id'],$_REQUEST['l_id'],$br,0);
                    ?>

                    <div align="center" class="bold-remove">
                        <!--// b--> 
                        <br /> <br />
                    <!--		<input type="submit" value="Save Barcode Image" onclick="save_image()" />&nbsp;&nbsp;
                                    <input type="submit" value="Print this page" onclick="printpage()" />&nbsp;&nbsp;-->
                        <input type="submit" value="Print" class="btn_print1" class="print" cid="<?php echo $_REQUEST['campaign_id'] ?>" lid="<?php echo $_REQUEST['l_id'] ?>" barcodea="<?php echo $_REQUEST['barcodea'] ?>" />
                        <script type="text/javascript">
                                /*
                                 $(function() {
                                 
                                 $('.print').click(function() {
                                 //var container = $(this).attr('rel');
                                 $('#coupon_div').printArea();
                                 return false;
                                 });
                                 
                                 });
                                 */
                                function PrintDiv() {
                                    var barcode = "";
                                    var divToPrint = document.getElementById('coupon_div');
                                    var popupWin = window.open('', '_blank');
                                    $.get('showbarcode.php?br=<?php echo $br; ?>', function (data) {
                                        barcode = data;
                                    });
                                    popupWin.document.writeln('<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"https://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><title>print page</title></head><body onload="window.print()"><style> body{ -webkit-print-color-adjust:exact;  )</style><div class="listing-details" id="listing-details">' + divToPrint.innerHTML + "bbbb" + barcode + '</div></body></html>');
                                    popupWin.focus();
                                    popupWin.print();
                                    popupWin.document.close();
                                    popupWin.close();

                                }
                                function printcoupon() {
                                    var divToPrint = $('#coupon_div').html();
                                    //  alert($('#coupon_div').html());

                                    var newWin = window.open('', 'Print-Window', 'width=100,height=100');
                                    newWin.document.open();
                                    newWin.document.write('<html><body onload="window.print()">' + $('#coupon_div').html() + '</body></html>');
                                    newWin.document.close();
                                    setTimeout(function () {
                                        newWin.close();
                                    }, 10);
                                }

                        </script>
                        <input type="button" value="Back" name="print_coupon_back" id="print_coupon_back" class="pri_pon_back" onclick="backclick()"  />
                        <br/><br/>

                        <!--end of content--></div>
                </div>

            </div><!--end of my_main_div-->
        </div><!--end of content-->

        <?php require_once(CUST_LAYOUT . "/footer.php"); ?>

    </body>
</html>
<?php

function load_print_coupon_data($cid, $lid, $br) {
        ?>
        <?php
        $objDB = new DB();
        $where_clause = array();
        $where_clause['id'] = $cid;
        $RSCompDetails = $objDB->Show("campaigns", $where_clause);

        $where_clause_l = array();
        $where_clause_l['id'] = $lid;
        $loc_nm = $objDB->Show("locations", $where_clause_l);



        $title = $RSCompDetails->fields['title'];
        $expdate = $RSCompDetails->fields['expiration_date'];
        $expdate = date("m/d/y g:i:s A", strtotime($expdate));
        $discont = $RSCompDetails->fields['discount'];
        $desc = $RSCompDetails->fields['deal_detail_description'];
        $terms = $RSCompDetails->fields['terms_condition'];
        $rpoint = $RSCompDetails->fields['redeem_rewards'];
        $timzone = $RSCompDetails->fields['timezone'];

        $where_x = array();
        $where_x['campaign_id'] = $cid;
        $CodeDetails = $objDB->Show("activation_codes", $where_x);
        $act_code = $CodeDetails->fields['activation_code'];

        $where_clause_m = array();
        $where_clause_m['id'] = $RSCompDetails->fields['created_by'];
        $merchant_user = $objDB->Show("merchant_user", $where_clause_m);

        $merchant_icon = $merchant_user->fields['merchant_icon'];
        ?>
        <div id="coupon_div" style="-webkit-print-color-adjust:exact; ">

            <div style="border: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px; border-bottom: none;" class="c_div">            
                <div style="padding: 5px; font-weight: bold; font-size: 18px; color: rgb(255, 255, 255); background: none repeat scroll 0px 0px rgb(35, 30, 30);height:15px;" class="c_title">
        <!--                <span style="text-transform:uppercase"><?= $title; ?></span>-->
                    <span style="float: right; font-weight: normal; font-size: 15px;font-weight: bold" class="expr_div">Expiration Date :- <?= $expdate; ?></span>
                </div>
                <div class="main_top_img" style="overflow:hidden;margin:10px 0 0;float:left;width:380px;position:relative;">            
                    <div class="main_merimg" style="float:left;">
                        <div class="merc_image" style="overflow:hidden;border: 1px solid white;width:133px;"><img src="<?php echo ASSETS_IMG . "/m/icon/" . $merchant_icon; ?>" alt="Merchant User" style="max-width:125px;height:auto!important;width:auto!important;max-height:150px;border: 4px solid #fff;"/></div>
                        <div class="scanf_logo businessname" style="text-overflow:ellipsis;white-space: nowrap;width:238px;overflow:hidden;padding:7px 0 0;font:bold 16px/18px Arial;color:#FC930B;text-decoration:none;"><?php
                            // for business name 
                            $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $lid);
                            if (trim($arr[0]) == "") {
                                    unset($arr[0]);
                                    $arr = array_values($arr);
                            }
                            $json = json_decode($arr[0]);
                            $busines_name = $json->bus_name;
                            echo $busines_name;
                            // for business name 
                            ?></div>
                    </div> 


                    <div class="dis_title" style="border:0px;width: 343px;margin:20px 0 0;">
                        <?php
                        if ($discont != "") {
                                ?>
                                <span class="offer_span" style="width:120px;height:110px;text-align:center;position:absolute;padding:0px 0 0;right:28px;top:0px;font-size:35px;"> <img src="<?php echo ASSETS_IMG ?>/c/offer_bg.png" style="display:block"/><div class="img_span" style="position: absolute;top:13px;left: 0px;width: 100%;color:#FFFFFF;"><?= $discont; ?></div></span>
                                <?php
                        }
                        ?>
                        <div style="margin: 10px 0 0 0;"><img src="showbarcode.php?br=<?php echo $br; ?>" alt="barcode" /></div>
                        <script>function printpage() {
                                    window.print()
                                }
                                function save_image() {
                                    window.open("<?= WEB_PATH ?>" + "/showbarcode.php?br=" + "<?php echo $br; ?>");
                                }
                        </script>
                        <!--// b--> 
                            <!--		<input type="submit" value="Save Barcode Image" onclick="save_image()" />
                            <input type="submit" value="Print this page" onclick="printpage()" /><br/><br/>-->
                        <!--// b-->
                    </div>
                </div>
                <div class="right_coupon_div" style="border:2px dashed #000000;float:left;height:214px;margin:13px 0 0;overflow:hidden;padding:10px;width:415px;">
                    <div id="right_title" style="width:100%;font-size:15px; color:#333; text-align:justify; font-weight: bold;"> 
                        <?php
                        echo $title;
                        ?></div>
                    <div class="coupon_desc" style="font-size:13px;margin:0px;padding:0px!important;text-align:justify;">
                        <?php
                        echo $desc;
                        ?>
                    </div>  

                </div>

            </div>


            <?php
            //echo "aaaaa";
            $str = "";
            if ($RSCompDetails->fields['number_of_use'] == 1) {
                    $str = "* Limit one per customer, Not valid with any other offer";
            } elseif ($RSCompDetails->fields['number_of_use'] == 2) {
                    $str = "* Limit one per customer per day , not valid with any other offer";
            } elseif ($RSCompDetails->fields['number_of_use'] == 3) {
                    $str = "* Earn Redemption Points On Every Visit";
            }

            $arr = file(WEB_PATH . '/process.php?get_location_name_of_campaigns=yes&camp_id=' . $cid);
            if (trim($arr[0]) == "") {
                    unset($arr[0]);
                    $arr = array_values($arr);
            }
            $json = json_decode($arr[0]);
            $locations = $json->records;
            $t_records = $json->total_records;
            $cnt = 1;
            if ($t_records > 1 || $str != "") {
                    ?>
                    <div class="c_middle_div" style="border-left: 1px solid rgb(0, 0, 0);  border-right: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px; border-top:1px solid #ccc;border-bottom: none;">
                        <?php
                }

                if ($t_records > 1) {
                        ?>
                        <div class="participating_location" style="display:none;"> You can find this deal available on following location also.
                            <?php
                            foreach ($locations as $Rownew) {
                                    if ($Rownew->id != $lid) {
                                            $address = $Rownew->address . ", " . $Rownew->city . ", " . $Rownew->state . ", " . $Rownew->zip . ", " . $Rownew->country;
                                            ?>
                                            <p style="margin-bottom: 8px;margin-top: 8px;  padding: 0px !important; padding-left: 20px !important;">
                                                <?php //echo $Rownew->location_name?>
                                                <?php
                                                // for business name 
                                                $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $Rownew->id);
                                                if (trim($arr[0]) == "") {
                                                        unset($arr[0]);
                                                        $arr = array_values($arr);
                                                }
                                                $json = json_decode($arr[0]);
                                                $busines_name = $json->bus_name;
                                                echo $busines_name;
                                                // for business name 
                                                ?>
                                                &nbsp;<span>(<?= $address ?>)</span></p>
                                            <?php
                                    }
                            }
                            ?>


                        </div>   
                        <?php
                }
                if ($t_records > 1 || $str != "") {
                        $str1 = "<div style=' float:left;'>Please advice crew member of offer prior to ordering  </div>";
                        echo "<div class='coupon_use' style=' font-size: 15px; width:100%;'>";
                        echo $str1;
                        echo "<div style=' float:right;'>" . $str . "</div>";
                        echo "</div>";
                }
                if ($t_records > 1 || $str != "") {
                        ?>
                    </div>
            <?php }
            ?>


            <div class="c_last_div" style="  border: 1px solid rgb(0, 0, 0);  padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px;text-align:left;">
                <span class="contents_location" style="padding-left:10px;float:left;display:block;line-height:18px;">Where to Redeem :- </span><!--<span class="loca_name" style="font-weight:bold; padding-left: 18px">
                <?php //echo $loc_nm->fields['location_name'];?>
                <?php
                // for business name 
                $arr = file(WEB_PATH . '/process.php?getlocationbusinessname=yes&l_id=' . $loc_nm->fields['id']);
                if (trim($arr[0]) == "") {
                        unset($arr[0]);
                        $arr = array_values($arr);
                }
                $json = json_decode($arr[0]);
                $busines_name = $json->bus_name;
                //echo $busines_name;
                // for business name 
                ?>
                </span>-->
                <span style="padding-left:10px;max-width:228px;float:left;display:block;line-height:18px;">
                    <?php //echo $loc_nm->fields['address']; ?>
                    <?php $address = $loc_nm->fields['address'] . ", " . $loc_nm->fields['city'] . ", " . $loc_nm->fields['state'] . ", " . $loc_nm->fields['zip'] . ", " . $loc_nm->fields['country']; ?>
                    <?php echo $address; ?>
                </span>

                <span  style="padding-left:10px;float:left;display:block;line-height:18px;">Ph# (<?= substr($loc_nm->fields['phone_number'], 4, 3); ?>) <?= substr($loc_nm->fields['phone_number'], 8); ?></span>
                <span class="contents_location" style="float:left;display:block;line-height:18px;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                <span class="contents_location" style="float:left;display:block;line-height:18px;">Redemption point :- </span><span style="font-weight: bold; padding-left: 10px;float:left;display:block;line-height:18px;"><?= $rpoint; ?></span><span class="contents_location" style="float:left;display:block;line-height:18px;">&nbsp;&nbsp;|&nbsp;&nbsp;</span> 
                <div class="head_lg" style="float:right;overflow: hidden;height:18px;"><img style="max-width:85px;" src="<?php echo ASSETS_IMG . "/c/header-logo.jpg"; ?>" alt="Scanflip"/></div>
            </div>
            <!--            <div class="c_last_div" style=" border: 1px solid rgb(0, 0, 0); padding: 10px; margin: 0px auto; display: block; overflow: hidden; width: 820px;text-align:right;">
                            Participating Location :- <div class="straddress">
                                        <div class="strname" style="font-weight: bold;"><?= $loc_nm->fields['location_name']; ?></div>
                                        <div class="straddress" style="font-weight: bold;"><?= $loc_nm->fields['address']; ?></div>
                                        <div class="straphone" style="font-weight: bold;"><?= $loc_nm->fields['phone_number']; ?></div>
                                    </div>&nbsp;&nbsp;|&nbsp;&nbsp; 
                            Redemption point :- <span style="font-weight: bold;"><?= $rpoint; ?></span>&nbsp;&nbsp;|&nbsp;&nbsp; 
                            <span style="font-weight: bold;">Scanflip merchant</span>
                        </div>-->
            <div class="coupon_terms" style="font-size:13px;text-align:justify;margin-left:63px;width:822px;margin-top: 10px;padding:10px!important;border:1px solid black">
                <div style="font-weight: bold;">
                    Terms & Condition :
                </div>
                <?php
                if ($terms != "") {
                        echo $terms . "<p>Additional Terms</p><p>
									No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.
									</p>";
                } else {
                        echo "<p>No cash value unless otherwise indicated in these terms. Scanflip does not make any warranty in relation to the campaigns, including without limitation their validity and/or value. Scanflip is not a party to any transaction that the advertiser and user may enter into.
									</p>";
                }
                ?>
            </div> 
        </div>

        <?php
}
?>

<script type="text/javascript">

        function backclick()
        {

            window.location = "<?php echo $campaign_redirecting_url; ?>";
        }
</script>
<script type="text/javascript" src="<?= ASSETS_JS ?>/c/jquery.PrintArea.js_4.js"></script>
<script>
        // alert("hii"+$("#coupon_div").width());
        //   alert("hii"+$("#coupon_div").height());
        //alert(jQuery("#coupon_div").html());
        jQuery(document).ready(function () {
            //jQuery("#coupon_content_print").val(jQuery("#coupon_div").html());
        });

        //alert( jQuery("#coupon_content_print").val());

        jQuery(".btn_print1").live('click', function () {
            var cid = jQuery(this).attr('cid');
            var lid = jQuery(this).attr('lid');
            var barcodea = jQuery(this).attr('barcodea');
            ele = jQuery(this);

            try
            {
                jQuery.ajax({
                    type: "POST",
                    url: '<?php echo WEB_PATH ?>/func_print_coupon1.php',
                    data: 'load_print_coupon_data1=true&cid=' + cid + '&lid=' + lid + '&barcodea=' + barcodea,
                    async: false,
                    success: function (msg)
                    {
                        //alert(msg);
                        jQuery("#print_coupon_div").html(msg);

                        jQuery("#coupon_div_" + cid + "_" + lid).css('display', 'block');
                        //jQuery("#print_coupon_div").printArea();

                        //jQuery("#image_barcode_"+cid+"_"+lid).attr("src",jQuery("#image_barcode_"+cid+"_"+lid).attr("src")+"&rnd="+Math.random());
                        //alert(jQuery("#image_barcode_"+cid+"_"+lid).attr("src"));	
                        timeout = setInterval(function ()
                        {
                            //alert('hi')
                            jQuery("#print_coupon_div").printArea();
                            clearTimeout(timeout);
                        }, 1000);
                    }
                });
            }
            catch (e)
            {
                // alert(e);
            }

        });
</script>
