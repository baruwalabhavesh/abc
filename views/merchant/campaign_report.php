<?php
/**
 * @uses generate campaign report
 * @used in pages : reports.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB('read');

$array = array();
$array['id'] = $_REQUEST['id'];
$array['created_by'] = $_SESSION['merchant_id'];
$agewisegender = array();
$total_transaction_point_ex = 0;
$total_transaction_point_new = 0;
$total_transaction_only_points_ex = 0;
$total_transaction_only_points_new = 0;


//echo WEB_PATH.'/merchant/process.php?btnGetCampaignDetail=yes&mer_id='.$_SESSION['merchant_id']."&id=".$_REQUEST['id'];
$arr = file(WEB_PATH.'/merchant/process.php?btnGetCampaignDetail=yes&mer_id='.$_SESSION['merchant_id']."&id=".$_REQUEST['id']);
if(trim($arr[0]) == "")
{
unset($arr[0]);
$arr = array_values($arr);
}
$json = json_decode($arr[0]);
$total_records = $json->total_records;
$records_array = $json->records;

$array = array();

if($total_records>0){
foreach($records_array as $Row)
{
$array['campaign_id'] = $Row->id;
}
}

$RSCode = $objDB->Show("activation_codes", $array);

$array = array();
$array['merchant_id'] = $_SESSION['merchant_id'];
$RSGroups = $objDB->Show("merchant_groups", $array);


//$objJSON = new JSON();
$JSON = $objJSON->get_compain_details($_REQUEST['id']);
$RS1 = json_decode($JSON);
//echo md5("123456");
?>

<?
if($total_records>0){
foreach($records_array as $RS)
{
?>
<div class="mer_chant_div">
    <table width="100%" cellspacing="0" class="campaign_det">
        <tr>
            <td colspan="2">

            </td>
        </tr>

        <tr>
            <td colspan="2">
                <h3 class="campaign_report_heading"><?php echo $RS->title; ?></h3>
<!--				<p><span>Campaign Title&nbsp;:&nbsp;</span><?= $RS->discount." ".$RS->title ?></p>-->
            </td>


        </tr>
        <?php
        $arr_age = Array();
        /* $Sql = "SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM campaign_location cl WHERE cl.campaign_id = ".$RS->id." ) and l.active=1";
          $RS_locations =  $objDB->execute_query($Sql); */
        $RS_locations = $objDB->Conn->Execute("SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM campaign_location cl WHERE cl.campaign_id =?) and l.active=?", array($RS->id, 1));

        $locations_str = "";
        while($Row = $RS_locations->FetchRow()){ $where_clause = array();
        $m_arr = array();
        $m_arr['campaign_id'] = $RS->id;
        $m_arr['location_id'] = $Row['id'];
        $RS_m_c = $objDB->Show("campaign_location", $m_arr);
        $max_coupon = $RS_m_c->fields['num_activation_code'];
//				
//				
        /* $remain_sql="select count(*) as total from coupon_codes where customer_campaign_code =".$RS->id." AND location_id=".$Row['id'];

          $RS_remain = $objDB->Conn->Execute($remain_sql); */
        $RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes where customer_campaign_code =? AND location_id=?", array($RS->id, $Row['id']));

        $remain_val = $RS_remain->fields['total'];

        /* $r_sql = "SELECT * FROM `reward_user` where campaign_id = ".$RS->id." and  location_id =".$Row['id']." and referred_customer_id=0 group by campaign_id ,customer_id";

          $RS_redeem = $objDB->Conn->Execute($r_sql); */
        $RS_redeem = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id = ? and  location_id =? and referred_customer_id=0 group by campaign_id ,customer_id", array($RS->id, $Row['id']));

        $redeem_val = $RS_redeem->RecordCount();

        $remain_val = $remain_val-$redeem_val;
        $locations_str .= $Row['location_name'].",";
        }
        $locations_str = trim($locations_str, ",");
        ?>
<!--  <tr>
<td colspan="2" >
<p><span>Campaign locations&nbsp;:&nbsp;</span><?= $locations_str ?></p>
</td>
</tr>
<tr>
<td colspan="2" >
        <?php $B3 = $RS->deal_value; ?>
<p><span>Avarage campaign value&nbsp;:&nbsp;</span>$<?= $B3 ?></p>
</td>
</tr>-->

        <?php
        $arr = file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
        if(trim($arr[0]) == "")
        {
        unset($arr[0]);
        $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
        $total_records = $json->total_records;
        $records_array = $json->records;
        if($total_records>0){
        foreach($records_array as $Row)
        {

        $price = $Row->price;
        $point_ = $Row->points;
        $p = (1*$price)/$point_;
        }
        }
        $B4 = (1*$price)/$point_;
        $B5 = $RS->referral_rewards * $B4;
        $B6 = $RS->redeem_rewards * $B4;
        $B7 = $RS->referral_rewards;
        $B8 = $RS->redeem_rewards;
        ?>
        <?php
        $cid = $_REQUEST['id'];
        /* $sql1 = "SELECT *  FROM coupon_codes WHERE customer_campaign_code =".$cid;
          $RS1 =  $objDB->Conn->Execute($sql1); */
        $RS1 = $objDB->Conn->Execute("SELECT *  FROM coupon_codes WHERE customer_campaign_code =?", array($cid));

        $total_reserved_coupon = $RS1->RecordCount();
        $arr_exsting_cust = array();
        $arr_new_cust = array();

        /* $remain_sql="select count(*) as total from coupon_codes where customer_campaign_code =".$cid;

          $RS_remain = $objDB->Conn->Execute($remain_sql); */
        $RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes where customer_campaign_code =?", array($cid));

        $remain_val = $RS_remain->fields['total'];

        /* $referral_sql = "SELECT sum(referral_reward) as total FROM `reward_user` where campaign_id = ".$cid." and referred_customer_id<>0 ";
          $RS_ref =  $objDB->Conn->Execute($referral_sql); */
        $RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` where campaign_id =? and referred_customer_id<>? ", array($cid, 0));


        /* $sql_tot_redeem_coupon = "SELECT *  FROM `reward_user` where campaign_id=".$cid." and referred_customer_id=0  ";

          $RS_tot_redeem_coupon =  $objDB->Conn->Execute( $sql_tot_redeem_coupon); */
        $RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id=? and referred_customer_id=?", array($cid, 0));





        //-- referral customer counting 
        /* $referral_sql = "SELECT *  FROM `reward_user` where campaign_id = ".$cid." and referred_customer_id<>0 ";
          $RS_ref_cnt =  $objDB->Conn->Execute($referral_sql); */
        $RS_ref_cnt = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and referred_customer_id<>?", array($cid, 0));
        $totla_redeem_point_by_exsting_cust_ref = 0;
        $totla_redeem_point_by_new_cust_ref = 0;
        while($Row1 = $RS_ref_cnt->FetchRow())
        {

        /* $sql2 =  "SELECT *  FROM `reward_user` where campaign_id = ".$cid." and customer_id = ".$Row1['customer_id']." and referred_customer_id<>0";
          $RS2 =  $objDB->Conn->Execute($sql2); */
        $RS2 = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and customer_id =? and referred_customer_id<>?", array($cid, $Row1['customer_id'], 0));

        if($RS2->RecordCount()== 1)
        {
        if(!key_exists($Row1['customer_id'], $arr_new_cust))
        {
        $arr_new_cust_ref[$Row1['customer_id']] = $RS2->RecordCount();
        }
        $totla_redeem_point_by_new_cust_ref = $totla_redeem_point_by_new_cust_ref + $Row1['referral_reward'];
        }
        else if($RS2->RecordCount()>1) {
        $arr_exsting_cus_reft[$Row1['customer_id']] = $RS2->RecordCount();
        $totla_redeem_point_by_exsting_cust_ref = $totla_redeem_point_by_exsting_cust_ref+$Row1['referral_reward'];
        }

        }

        //--- referral customer counting
        /* $sql_getcampaign_location = "select * from campaign_location where  campaign_id=".$cid;
          $Rs_getcampaign_location =  $objDB->Conn->Execute($sql_getcampaign_location ); */
        $Rs_getcampaign_location = $objDB->Conn->Execute("select * from campaign_location where  campaign_id=?", array($cid));
        $loc_str = "";
        $cnt = 1;

        while($camp_location = $Rs_getcampaign_location->FetchRow())
        {

        $loc_str .= " location_id=".$camp_location['location_id'];
        if($Rs_getcampaign_location->RecordCount()!= $cnt)
        {
        $loc_str .= " or ";
        }
        $cnt++;
        }

        $male_gender = 0;
        $female_gender = 0;
        $unknown_gender = 0;
        //--- count exsting /new reserve  coupons
        /* $r_sql_1 = "Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
          customer_campaign_code=".$cid." and   ( ".$loc_str." )  "; */

        $male_gender = 0;
        $unknown_gender = 0;
        $female_gender = 0;
        //$RS_reserve = $objDB->Conn->Execute($r_sql_1);
        $RS_reserve = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
						customer_campaign_code=? and   ( " . $loc_str . " )  ", array($cid));
        $total_reserved_by_new_cust = array();
        $total_reserved_by_exist_cust = array();
        while($Row1 = $RS_reserve->FetchRow())
        {


        // $sql_reserve_count = "select * from coupon_codes WHERE customer_campaign_code=".$cid." and customer_id= ".$Row1['customer_id']." and ( ".$loc_str." )";
        /* $sql_reserve_count = "select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ".$Row1['customer_id']." and ( ".$loc_str." )  ) ";
          $RS2 =  $objDB->Conn->Execute($sql_reserve_count); */
        $RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

        if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
        {
        if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust))
        {
        $total_reserved_by_new_cust[$Row1['customer_id']] = $RS2->RecordCount();
        }
        }
        else {
        array_push($total_reserved_by_exist_cust, $Row1['customer_id']);
        }
        }


        $coupons_reserved_by_new_customer = count($total_reserved_by_new_cust);
        $coupons_reserved_by_exsting_customer = count($total_reserved_by_exist_cust);

        //--- count exsting /new reserve coupons
        //$r_sql = "SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid."  ";
        /* $r_sql = "SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
          FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid;
          //echo $r_sql."===";
          $RS_redeem = $objDB->Conn->Execute($r_sql); */
        $RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cr.coupon_id= cc.id and cc.customer_campaign_code=?", array($cid));

        $redeem_val = $RS_redeem->RecordCount();

        $remain_val = $remain_val-$redeem_val;
        $total_redeem_point = 0;
        $total_referral_point = $RS_ref->fields['total'];

        $totla_redeem_point_by_new_cust = 0;
        $totla_redeem_point_by_exsting_cust = 0;
        $total_revenue_cost_by_new_cust = 0;
        $total_revenue_cost_by_exist_cust = 0;
        $C28 = 0;
        $C34 = 0;
        //if($redeem_val > 0)
        //{
        while($Row1 = $RS_redeem->FetchRow())
        {
        /* */
        if ($Row1['gender'] == "") {
        //  echo "<br/>unknown".$month."<br />";
        $unknown_gender = $unknown_gender + 1;
        } else if ($Row1['gender'] == 1) {
        //  echo "<br/>male".$month."<br />";
        $male_gender = $male_gender + 1;
        } else {
        //  echo "<br/>Female".$month."<br />";
        $female_gender = $female_gender + 1;
        }

        $today = new DateTime();
        $birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
        $interval = $today->diff($birthdate);
        $age = $interval->format('%y');
        array_push($arr_age, $age);
        array_push($agewisegender, $Row1['gender']);


        /* $r_sql_unique = "select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ".$Row1['customer_id']." and ( ".$loc_str." )  ) ";
          $r_sql1 = "SELECT * FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid." and  cc.customer_id=".$Row1['customer_id'];

          //  echo "<br />sq[=".$r_sql_unique."==sql==";
          $sql2 =  "SELECT * FROM `reward_user` where campaign_id = ".$cid." and customer_id = ".$Row1['customer_id']." and   referred_customer_id=0 ";

          $RS2 =  $objDB->Conn->Execute($r_sql_unique); */
        $RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

        //$Rs3 = $objDB->Conn->Execute($sql2);
        $Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? ", array($cid, $Row1['customer_id'], 0));


        if($RS2->RecordCount()== 1)
        {
        if(!key_exists($Row1['customer_id'], $arr_new_cust))
        {
        $arr_new_cust[$Row1['customer_id']] = $RS2->RecordCount();
        }
        $totla_redeem_point_by_new_cust = $totla_redeem_point_by_new_cust + $Rs3->fields['earned_reward'];
        $total_revenue_cost_by_new_cust = $total_revenue_cost_by_new_cust + $Row1['redeem_value'];
        }
        else if($RS2->RecordCount()>1) {
        //$arr_exsting_cust[$Row1['customer_id']]= $RS2->RecordCount();
        array_push($arr_exsting_cust, $Row1['customer_id']);
        $totla_redeem_point_by_exsting_cust = $totla_redeem_point_by_exsting_cust+$Rs3->fields['earned_reward'];
        $total_revenue_cost_by_exist_cust = $total_revenue_cost_by_exist_cust + $Row1['redeem_value'];
        }
        $total_redeem_point = $total_redeem_point + $Row1['earned_reward'];
        }
        $tot_revenue_cost_existing = $totla_redeem_point_by_exsting_cust_ref+ $totla_redeem_point_by_exsting_cust;
        $tot_revenue_cost_new = $totla_redeem_point_by_new_cust+$totla_redeem_point_by_new_cust_ref;
        /* echo "<pre>";
          print_r($arr_exsting_cust);
          echo "</pre>"; */
        $arr_exsting_cust_unique = array_unique($arr_exsting_cust);
        foreach($arr_exsting_cust_unique as $key => $value)
        {
        /* $r_sql_t_f = "SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid." and  cc.customer_id=".$arr_exsting_cust_unique[$key];

          $rs_t_f = $objDB->Conn->Execute($r_sql_t_f); */
        $rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));

        $total_transaction_point_ex = $total_transaction_point_ex + $rs_t_f->fields['total_transaction_fees'];
        $total_transaction_only_points_ex = $total_transaction_only_points_ex + $rs_t_f->fields['total_transaction_points'];
        }
        $arr_new_cust = array_keys($arr_new_cust);
        foreach($arr_new_cust as $key => $value)
        {
        //for($i=0;$i<count($arr_new_cust);$i++)
        //{
        /* $r_sql_t_f = "SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid." and  cc.customer_id=".$arr_new_cust[$key];

          $rs_t_f = $objDB->Conn->Execute($r_sql_t_f); */
        $rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));

        $total_transaction_point_new = $total_transaction_point_new + $rs_t_f->fields['total_transaction_fees'];
        $total_transaction_only_points_new = $total_transaction_only_points_new + $rs_t_f->fields['total_transaction_points'];
        }
        $C14 = $total_reserved_coupon;
        $C15 = count($arr_exsting_cust);
        $C16 = count($arr_new_cust);
        $C17 = $C15 + $C16;
        $C18 = $C14 - ($RS_tot_redeem_coupon->RecordCount());
        /* calculate tota refferal points */
        /* $Sql_2_share =  "SELECT * FROM reward_user WHERE referred_customer_id <> 0 and referral_reward<>0 and  campaign_id =".$cid;

          $RS_2_share = $objDB->Conn->Execute($Sql_2_share); */
        $RS_2_share = $objDB->Conn->Execute("SELECT * FROM reward_user WHERE referred_customer_id <> ? and referral_reward<>? and  campaign_id =?", array(0, 0, $cid));

        $total_share_count = $RS_2_share->RecordCount();

        $total_cust = $C17;
        if($total_cust != 0) {
        $c_1 = $c_2 = $c_3 = $c_4 = $c_5 = $c_6 = 0;


        $arr_age_txt = array();
        $agewise_gender_male['65 Or Above'] = 0;
        $agewise_gender_male['55 to 64'] = 0;
        $agewise_gender_male['45 to 54'] = 0;
        $agewise_gender_male['25 to 44'] = 0;
        $agewise_gender_male['18 to 24'] = 0;
        $agewise_gender_male['17 Or Below'] = 0;

        $agewise_gender_female['65 Or Above'] = 0;
        $agewise_gender_female['55 to 64'] = 0;
        $agewise_gender_female['45 to 54'] = 0;
        $agewise_gender_female['25 to 44'] = 0;
        $agewise_gender_female['18 to 24'] = 0;
        $agewise_gender_female['17 Or Below'] = 0;
        for ($c = 0;
        $c < count($arr_age);
        $c++) {
        //	 echo "<br/>".$arr_age[$c]."===".$agewisegender[$c];
        if ($arr_age[$c] >= 65) {
        if($agewisegender[$c] == 1)
        {

        $agewise_gender_male['65 Or Above'] = $agewise_gender_male['65 Or Above'] +1;
        }
        else if($agewisegender[$c] == 2)
        {
        $agewise_gender_female['65 Or Above'] = $agewise_gender_female['65 Or Above'] +1;
        }

        $c_6 = $c_6 + 1;
        } else if ($arr_age[$c] >= 55 && $arr_age[$c] <= 64) {
        if($agewisegender[$c] == 1)
        {
        $agewise_gender_male['55 to 64'] = $agewise_gender_male['55 to 64'] +1;
        }
        else if($agewisegender[$c] == 2)
        {
        $agewise_gender_female['55 to 64'] = $agewise_gender_female['55 to 64'] +1;
        }
        $c_5 = $c_5 + 1;
        } else if ($arr_age[$c] >= 45 && $arr_age[$c] <= 54) {
        if($agewisegender[$c] == 1)
        {
        $agewise_gender_male['45 to 54'] = $agewise_gender_male['45 to 54'] +1;
        }
        else if($agewisegender[$c] == 2)
        {
        $agewise_gender_female['45 to 54'] = $agewise_gender_female['45 to 54'] +1;
        }
        $c_4 = $c_4 + 1;
        } else if ($arr_age[$c] >= 25 && $arr_age[$c] <= 44) {
        if($agewisegender[$c] == 1)
        {
        $agewise_gender_male['25 to 44'] = $agewise_gender_male['25 to 44'] +1;
        }
        else if($agewisegender[$c] == 2)
        {
        $agewise_gender_female['25 to 44'] = $agewise_gender_female['25 to 44'] +1;
        }
        $c_3 = $c_3 + 1;
        } else if ($arr_age[$c] >= 18 && $arr_age[$c] <= 24) {
        if($agewisegender[$c] == 1)
        {
        $agewise_gender_male['18 to 24'] = $agewise_gender_male['18 to 24'] +1;
        }
        else if($agewisegender[$c] == 2)
        {
        $agewise_gender_female['18 to 24'] = $agewise_gender_female['18 to 24'] +1;
        }
        $c_2 = $c_2 + 1;
        } else if ($arr_age[$c] <= 17) {
        if($agewisegender[$c] == 1)
        {
        $agewise_gender_male['17 Or Below'] = $agewise_gender_male['17 Or Below'] +1;
        }
        else if($agewisegender[$c] == 2)
        {
        $agewise_gender_female['17 Or Below'] = $agewise_gender_female['17 Or Below'] +1;
        }
        $c_1 = $c_1 + 1;
        }
        }
        //array_push()

        if ($c_1 > 0) {
        $arr_age_txt['17 Or Below'] = $c_1;
        }
        if ($c_2 > 0) {
        $arr_age_txt['18 to 24'] = $c_2;
        }
        if ($c_3 > 0) {
        $arr_age_txt['25 to 44'] = $c_3;
        }
        if ($c_4 > 0) {
        $arr_age_txt['45 to 54'] = $c_4;
        }
        if ($c_5 > 0) {
        $arr_age_txt['55 to 64'] = $c_5;
        }
        if ($c_6 > 0) {
        $arr_age_txt['65 Or Above'] = $c_6;
        }

        $male_per = ($male_gender * 100) / $total_cust;
        $unknowne_per = ($unknown_gender * 100) / $total_cust;
        $female_per = ($female_gender * 100 ) / $total_cust;
        $gender1 = round($male_per, 2);
        $gender2 = round($female_per, 2);
        $gender3 = round($unknowne_per, 2);


        $ahm1 = round((($agewise_gender_male['17 Or Below'] * 100) / $total_cust));
        $ahm2 = round((( $agewise_gender_male['18 to 24'] * 100) / $total_cust));
        $ahm3 = round((($agewise_gender_male['25 to 44'] * 100) / $total_cust));
        $ahm4 = round((($agewise_gender_male['45 to 54'] * 100) / $total_cust));
        $ahm5 = round((($agewise_gender_male['55 to 64']* 100) / $total_cust));
        $ahm6 = round((($agewise_gender_male['65 Or Above'] * 100) / $total_cust));
        $afm1 = round((($agewise_gender_female['17 Or Below'] * 100) / $total_cust));
        $afm2 = round((( $agewise_gender_female['18 to 24'] * 100) / $total_cust));
        $afm3 = round((($agewise_gender_female['25 to 44'] * 100) / $total_cust));
        $afm4 = round((($agewise_gender_female['45 to 54'] * 100) / $total_cust));
        $afm5 = round((($agewise_gender_female['55 to 64']* 100) / $total_cust));
        $afm6 = round((($agewise_gender_female['65 Or Above'] * 100) / $total_cust));
        $total_age_arr = round((($c_1 * 100) / $total_reserved_coupon)) . "-" . round((($c_2 * 100) /$total_reserved_coupon)) . "-" . round((($c_3 * 100) / $total_reserved_coupon)) . "-" . round((($c_4 * 100) / $total_cust)) . "-" . round((($c_5 * 100) / $total_cust)) . "-" . round((($c_6 * 100) / $total_cust));
        /*  */


        /* */
        ?>
        <script>
                /*	var V1 = <?php echo $gender1; ?>;
                 var V2 = <?php echo $gender2; ?>;
                 //	alert(V1+"==="+V2);
                 chart = new Highcharts.Chart({
                 chart: {
                 renderTo: 'summerisedidgender',
                 borderWidth:1,
                 plotBackgroundColor: null,
                 plotBorderWidth: null,
                 plotShadow: false
                                                                 
                 },
                 title: {
                 text: '<?php echo $merchant_msg['report']['cust_by_gender']; ?>',
                 align: 'center',
                 verticalAlign: 'middle',
                 y:-85
                 },
                 tooltip: {
                 pointFormat: '<b>{point.percentage:.1f}%</b>'
                 },
                 plotOptions: {
                 pie: {
                 dataLabels: {
                 enabled: true,
                 distance: -30,
                 style: {
                 fontWeight: 'bold',
                 color: 'white',
                 textShadow: '0px 1px 2px black'
                 },
                 formatter: function() {
                 if (this.y != 0) {
                 return this.point.name;
                 } else {
                 return null;
                 }
                 }
                 },
                 startAngle: -90,
                 endAngle: 90,
                 center: ['50%', '75%']
                 }
                 },
                 credits: {
                 enabled: false
                 },
                 series: [{
                 type: 'pie',
                 showInLegend: false,
                 //name: '',
                 innerSize: '50%',
                 data: [
                 ["Male",V1],['Female',V2],
                                                                 
                 ]
                 }]
                 });
                 categories = ['17 Or Below', '18 to 24', '25 to 44', '45 to 54',
                 '55 to 64', '65+'];
                 chart = new Highcharts.Chart({
                 chart: {
                 renderTo: 'summerisedidage',
                 borderWidth:1,
                 type: 'bar'
                                                                 
                 },
                 title: {
                 text: '<?php echo $merchant_msg['report']['cust_by_age']; ?>'
                 },
                                                                 
                 xAxis: [{
                 title: {
                 text: 'Age Distribution'
                 },
                 categories: categories,
                 reversed: false
                 }, { // mirror axis on right side
                 opposite: true,
                 reversed: false,
                 categories: categories,
                 linkedTo: 0
                 }],
                 yAxis: {
                 title: {
                 text: null
                 },
                 labels: {
                 formatter: function(){
                 return (Math.abs(this.value)) + '%';
                 }
                 },
                 min: -100,
                 max: 100
                 },
                                                                 
                 plotOptions: {
                 series: {
                 stacking: 'normal'
                 }
                 },
                                                                 
                 tooltip: {
                 formatter: function(){
                 return '<b>'+ this.series.name +' Age ( '+ this.point.category +' ) : </b>'+
                 Highcharts.numberFormat(Math.abs(this.point.y), 0)+'%' ;
                 }
                 },
                 credits: {
                 enabled: false
                 },    
                 series: [{
                 name: 'Male',
                 data: [<?php echo $ahm1; ?> , <?php echo $ahm2; ?>,<?php echo $ahm3; ?>,<?php echo $ahm4; ?>,<?php echo $ahm5; ?>,<?php echo $ahm6; ?>]
                 }, {
                 name: 'Female',
                 data: [-<?php echo $afm1; ?>, -<?php echo $afm2; ?>, -<?php echo $afm3; ?>, -<?php echo $afm4; ?>, -<?php echo $afm5; ?>,-<?php echo $afm6; ?>]
                 }]
                 }); */

        </script>
        <?php
        }
        $total_transaction_point = $total_transaction_only_points_ex + $total_transaction_only_points_new;
        $total_transaction_fee = $total_transaction_point_ex + $total_transaction_point_new;
        $C22 = $total_share_count * $B7;
        $C23 = $C15 * $B8;
        $C24 = $C16 * $B8;
        $C25 = $C23 + $C24;
        $C26 = $C25 + $C22;
        $C29 = $C22 * $B4;
        $C30 = ($C24 * $B4); //+  $total_transaction_point_new;
        $C31 = ($C23 * $B4); //+  $total_transaction_point_ex;
        $C32 = $C30 + $C31;
        $C28 = $C29 + $C32 + $total_transaction_fee;
        //echo "<br/>".$C24."==".$B4."==".$total_transaction_point_new."==new<br/>";
        //echo "<br/>".$C23."==".$B4."==".$total_transaction_point_ex."==ex<br/>";
        $C34 = $total_revenue_cost_by_new_cust + $total_revenue_cost_by_exist_cust;
        $C35 = $total_revenue_cost_by_new_cust;
        $C36 = $total_revenue_cost_by_exist_cust;


        if($C35!=0)
        {
			$C40 = round(($C29 + $C30)/$C35, 2);
		}
		if($C36!=0)
        {
			$C41 = round(($C29 + $C31)/$C36, 2);
		}

        if(strlen($C40) == 0)
        {
        $C40 = 0;
        }
        if(strlen($C41) == 0)
        {
        $C41 = 0;
        }
        //}
        ?>
<!-- <tr>
<td colspan="2" >
<p><span>Scanflip point value&nbsp;:&nbsp;</span>$<?php echo $B4; ?></p>
</td>
</tr>
<tr>
<td colspan="2" >
<p><span>Campaign refferal point value&nbsp;:&nbsp;</span>$<?= $B5 ?></p>
</td>
</tr>
<tr>
<td colspan="2">
<p><span>Campaign redemption point value&nbsp;:&nbsp;</span>$<?= $B6 ?></p>
</td>
</tr>
<tr>
<td colspan="2" >
<p><span>Campaign refferal point&nbsp;:&nbsp;</span><?= $B7 ?></p>
</td>
</tr>
<tr>
<td colspan="2" >
<p><span>Campaign redemption point&nbsp;:&nbsp;</span><?= $B8 ?></p>
</td>
</tr>-->
        <tr class="total_camp">
            <td>
                <p class=""><span class="report_heading"><?php echo $merchant_msg['report']['toatl_campaign_cost']; ?></span>$<?php echo $C28 ?></p>
                <p class="second"><span  class="report_heading"><?php echo $merchant_msg['report']['total_campaign_revenue']; ?></span>$<?php echo $C34; ?></p>
            </td>
        </tr>            
    </table>
</div>
<div class="location_heading">
    <span><?php echo "Summerised Statistics" ?></span>

    <div class="mainIcon" id="report_toggleIt-reportplus_0" ></div>

</div> 
<div class="mer_chant_div show_hide" id="reportplus_0" style="display:none">

    <?php
    $emailshare = 'Email Share';
    $sql_all_domains = "select * from share_domain ";
    $RS_domains_data = $objDB->Conn->Execute($sql_all_domains);
    $domain_arr['share_email'] = 0;
    $domain_arr['pageview_email'] = 0;
    $domain_arr['share_facebook'] = 0;
    $domain_arr['pageview_facebook'] = 0;
    $domain_arr['share_twitter'] = 0;
    $domain_arr['pageview_twitter'] = 0;
    $domain_arr['share_google'] = 0;
    $domain_arr['pageview_google'] = 0;
    $domain_arr['share_other'] = 0;
    $domain_arr['pageview_other'] = 0;
    $domain_arr['pageview_allmedium'] = 0;
    $domain_arr['pageview_qrcode'] = 0;

    $only_values = array_keys($domain_arr);
    //  print_r($only_values);
    $display_flag = false;
    $display_flag1 = false;
    while($Row_domain = $RS_domains_data->FetchRow())
    {
    /* $sql_t = "select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
      where campaign_id = ".$_REQUEST['id']." and d.id=". $Row_domain['id'];
      $RS_t = $objDB->Conn->Execute($sql_t); */
    $RS_t = $objDB->Conn->Execute("select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
										where campaign_id =? and d.id=?", array($_REQUEST['id'], $Row_domain['id']));

    while($Row_total = $RS_t->FetchRow())
    {
    if($Row_total['total'] >0)
    {
    $display_flag = true;
    }
    if($Row_domain['id'] == 1)
    {
    $domain_arr['share_facebook'] = $domain_arr['share_facebook'] + $Row_total['total'];
    }
    else if($Row_domain['id'] == 2)
    {
    $domain_arr['share_twitter'] = $domain_arr['share_twitter'] + $Row_total['total'];
    }
    else if($Row_domain['id'] == 3)
    {
    $domain_arr['share_google'] = $domain_arr['share_google'] + $Row_total['total'];
    }
    else if($Row_domain['id'] == 4)
    {
    $domain_arr['share_email'] = $domain_arr['share_email'] + $Row_total['total'];
    }
    else if($Row_domain['id'] == 5)
    {
    $domain_arr['share_other'] = $domain_arr['share_other'] + $Row_total['total'];
    }
    }
    $only_values = array_values($domain_arr);

    /* $sql_t = "select count(*) as total ,  p.campaign_id , p.location_id , d.domain from pageview_counter p inner join share_domain d on d.id= p.pageview_domain
      where campaign_id = ".$_REQUEST['id']." and d.id=". $Row_domain['id'];

      $RS_t = $objDB->Conn->Execute($sql_t); */
    $RS_t = $objDB->Conn->Execute("select count(*) as total ,  p.campaign_id , p.location_id , d.domain from pageview_counter p inner join share_domain d on d.id= p.pageview_domain where campaign_id =? and d.id=?", array($_REQUEST['id'], $Row_domain['id']));

    while($Row_total = $RS_t->FetchRow())
    {
    if($Row_total['total'] >0)
    {
    $display_flag1 = true;
    }
    if($Row_domain['id'] == 1)
    {
    $domain_arr['pageview_facebook'] = $domain_arr['pageview_facebook'] + $Row_total['total'];
    }
    else if($Row_domain['id'] == 2)
    {
    $domain_arr['pageview_twitter'] = $domain_arr['pageview_twitter'] + $Row_total['total'];
    }
    else if($Row_domain['id'] == 3)
    {
    $domain_arr['pageview_google'] = $domain_arr['pageview_google'] + $Row_total['total'];
    }
    else if($Row_domain['id'] == 4)
    {
    $domain_arr['pageview_email'] = $domain_arr['pageview_email'] + $Row_total['total'];
    }
    else if($Row_domain['id'] == 5)
    {
    $domain_arr['pageview_other'] = $domain_arr['pageview_other'] + $Row_total['total'];
    }
    }
    }
    /*     * ** qr code scan ** */
    /* $sql_t = "select * from scan_qrcode where campaign_id = ".$_REQUEST['id'];

      $RS_qrcodes_view = $objDB->Conn->Execute($sql_t); */
    $RS_qrcodes_view = $objDB->Conn->Execute("select * from scan_qrcode where campaign_id = ?", array($_REQUEST['id']));

    if($RS_qrcodes_view->RecordCount() > 0)
    {
    $domain_arr['pageview_qrcode'] = $RS_qrcodes_view->RecordCount();
    if($RS_qrcodes_view->RecordCount() > 0)
    {
    $display_flag1 = true;
    }
    }

    $domain_arr['pageview_allmedium'] = $domain_arr['pageview_other']+$domain_arr['pageview_email']+$domain_arr['pageview_google']+$domain_arr['pageview_twitter']+$domain_arr['pageview_facebook'];
    $only_keys = array_keys($domain_arr);
    //   print_r($only_keys);
    $only_values = array_values($domain_arr);
    //print_r($only_values);
    ?>
    <?php
    $one = 0;
    $two = 0;
    $three = 0;
    $four = 0;
    $five = 0;
    $dispplay_rating_flag = false;
    $key_value_pair = array();

    /* $sql = "select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  
      where   campaign_id= ".$_REQUEST['id']." group by re.rating";
      //echo $sql;
      $RS_ = $objDB->Conn->Execute($sql); */
    $RS_ = $objDB->Conn->Execute("select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  
						 where   campaign_id= ? group by re.rating", array($_REQUEST['id']));

    if($RS_->RecordCount() > 0 )
    {
    $dispplay_rating_flag = true;
    }
    $avarage_rating = array();
    while ($rating_row = $RS_->FetchRow()) {
    $total_ratings = $total_ratings + $rating_row['avarage_rating_counter'];
    if($rating_row['avarage_rating'] <=1)
    {
    $one = $one + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Poor'] = $one;
    }
    else if($rating_row['avarage_rating'] >1 && $rating_row['avarage_rating'] <= 2)
    {
    $two = $two + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Fair'] = $two;
    }
    else if($rating_row['avarage_rating'] >2 && $rating_row['avarage_rating'] <= 3)
    {
    $three = $three + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Good'] = $three;
    }
    else if($rating_row['avarage_rating'] >3 && $rating_row['avarage_rating'] <= 4)
    {
    $four = $four + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Very Good'] = $four;
    }
    else if($rating_row['avarage_rating'] >4 && $rating_row['avarage_rating'] <= 5)
    {
    $five = $five + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Excellent'] = $five;
    }

    }
    //echo $one."=".$two."=".$three."=".$four."=".$five;
    //  print_r($only_keys);
    ?>


    <?php if($display_flag1) { ?>

    <?php } ?>
    <?php if($display_flag) { ?>

    <?php } ?>
    <?php if($dispplay_rating_flag){ ?>

    <?php } ?>

    <?php if($total_cust !=0 ) { ?>
    <div class="campaign_report_div_left">
        <div cust_avail="0" class="campaign_report_gender" id="summerisedidgender" genderdata="<?php echo $gender1."-".$gender2; ?>"></div>

    </div>
    <div align="center" class="campaign_report_div_left">
        <div class="campaign_report_gender" id="summerisedidage" maledata="<?php echo $ahm1.'-'.$ahm2.'-'.$ahm3.'-'.$ahm4.'-'.$ahm5.'-'.$ahm6; ?>" femaledata="<?php echo $afm1.'-'.$afm2.'-'.$afm3.'-'.$afm4.'-'.$afm5.'-'.$afm6; ?>">

        </div>
    </div>

    <?php }
    ?>

    <?php if($display_flag1) { ?>

    <div align="center" class="campaign_report_div_left1">
        <div cust_avail="0" class=" campaign_report_gender1" id="summerisedview" sharingview="<?php echo $only_values[1]."-".$only_values[3]."-".$only_values[5]."-".$only_values[7]."-".$only_values[9]."-".$only_values[10]."-".$only_values[11]; ?>" >

        </div>
    </div>
    <?php } ?>

    <?php if($display_flag) { ?>
    <div align="center" class="campaign_report_div_left1">
        <div class=" campaign_report_gender1" id="summerisedshare" sharingdata="<?php echo $only_values[0]."-".$only_values[2]."-".$only_values[4]."-".$only_values[6]."-".$only_values[8]; ?>" >

        </div>
    </div>
    <?php } ?>
    <?php if($dispplay_rating_flag){ ?>
    <div align="center" class="campaign_report_div_left">
        <div cust_avail="0" class="campaign_report_gender" id="summerisedrating" ratingdata="<?php echo $five."-".$four."-".$three."-".$two."-".$one; ?>" >

        </div>
    </div>

    <?php } ?>



    <table width="100%" cellspacing="0" class="showhide_tab" >
        <tr>
            <td colspan="2" class="dashed_line">
                <p class="report_heading"> <?php echo $merchant_msg['report']['campaign_metrics']; ?></p>
            </td>
        </tr>
        <tr><td><p><span>Total number of activation code issued</span></p></td><td>&nbsp;<?php echo $C14; ?></td></tr>
        <tr><td><p><span>Total number of activation code reserved by existing customer : </span></p></td><td width="20%"> &nbsp;<?php echo $coupons_reserved_by_exsting_customer; ?></td></tr>
        <tr><td><p><span>Total number of activation code reserved by New customer : </span></p></td><td width="20%"> &nbsp;<?php echo $coupons_reserved_by_new_customer; ?></td></tr>
        <tr><td><p><span>Total number of activation code redeemed by existing customer : </span></p></td><td>&nbsp;<?php echo $C15; ?></td></tr>
        <tr><td><p><span>Total number of activation code redeemed by new customer : </span></p></td><td>&nbsp;<?php echo $C16; ?></td></tr>
        <tr><td><p><span>Total number of activation code redeemed by customer : </span></p></td><td>&nbsp;<?= $C17; ?></td></tr>
        <tr><td><p><span>Total number of activation code reserved by customer but not redeemed : </span></p></td><td>&nbsp;<?= $C18 ?></td></tr>
        <tr>
            <td colspan="2"  class="dashed_line" >
                <p class="report_heading"> <?php echo $merchant_msg['report']['campaign_qr_code_scan_metrics']; ?></p>
            </td>
        </tr>
        <?php
        /* $sql = " select distinct a.* ,b.location_name ,b.created_by ,b.id location_id 
          from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id
          where a.campaign_id=".$cid;
          // echo $sql;
          $RS_2_qrcode = $objDB->Conn->Execute($sql); */
        $RS_2_qrcode = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id where a.campaign_id=?", array($cid));

        /* $sql = " select distinct a.* ,b.location_name ,b.created_by ,b.id location_id 
          from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id
          where a.campaign_id=".$cid." and a.is_unique=1";
          //    echo "===".$sql;
          $RS_2_qrcodeun = $objDB->Conn->Execute($sql); */
        $RS_2_qrcodeun = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id where a.campaign_id=? and a.is_unique=?", array($cid, 1));
        ?>
        <tr><td ><p><span>Total number of QR Code scans</span></p></td><td>&nbsp;<?php echo $RS_2_qrcode->RecordCount(); ?></td></tr>
        <tr><td ><p><span>Total number of unique QR Code scans : </span></p></td><td width="20%"> &nbsp;<?php echo $RS_2_qrcodeun->RecordCount(); ?></td></tr>

        <tr>
            <td colspan="2"  class="dashed_line" >
                <p class="report_heading"> <?php echo $merchant_msg['report']['scanflip_point_metrics']; ?></p>
            </td>
        </tr>
<?php
$arr = file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
if(trim($arr[0]) == "")
{
unset($arr[0]);
$arr = array_values($arr);
}
$json = json_decode($arr[0]);
$total_records = $json->total_records;
$records_array = $json->records;
if($total_records>0){
foreach($records_array as $Row)
{

$price = $Row->price;
$point_ = $Row->points
?>
        <?php }} ?>


        <tr><td ><p><span>Total referral points given to new and existing customer : </span></p></td><td>&nbsp;<?= $C22 ?></td></tr>
        <tr><td ><p><span>Total redemption point given to existing customer : </span></p></td><td>&nbsp;<?= $C23 ?></td></tr>
        <tr><td ><p><span>Total redemption point given to new customer : </span></p></td><td>&nbsp;<?= $C24 ?></td></tr>
        <tr><td ><p><span>Total redemption point given to new and existing customer : </span></p></td><td>&nbsp;<?= $C25 ?></td></tr>
        <tr><td ><p><span>Total referral and redemption point given to new and exsting customer : </span></p></td><td>&nbsp;<?= $C26 ?></td></tr>
        <tr><td ><p><span>Total transaction points ( minimum coupon redemption fee in points) : </span></p></td><td>&nbsp;<?php echo $total_transaction_point; ?></td></tr>
        <tr>
            <td class="dashed_line">
                <p  class="report_heading"><?php echo $merchant_msg['report']['toatl_campaign_cost']; ?></p></td>
            <td class="dashed_line">$<?php echo $C28 ?>
            </td>
        </tr>


        <tr><td ><p><span>Total referral/viral marketing cost : </span></p></td><td>&nbsp;$<?= $C29 ?></td></tr>
        <tr><td ><p><span>Total redemption points cost for new  customer : </span></p></td><td>&nbsp;$<?= $C30 ?></td></tr>
        <tr><td ><p><span>Total redemption points cost for existing customer : </span></p></td><td>&nbsp;$<?= $C31 ?></td></tr>
        <tr><td ><p><span>Total redemption points cost for new and existing customers : </span></p></td><td>&nbsp;$<?= $C32 ?></td></tr>
        <tr><td ><p><span>Total transaction cost ( minimum coupon redemption cost) : </span></p></td><td>&nbsp;$<?php echo $total_transaction_fee; ?></td></tr>




        <tr>
            <td class="dashed_line" >
                <p  class="report_heading"><?php echo $merchant_msg['report']['total_campaign_revenue']; ?></p></td><td class="dashed_line">&nbsp;&nbsp;$<?php echo $C34; ?>
            </td>
        </tr>
        <tr><td ><p><span>Total revenue from new customer : </span></p></td><td>&nbsp;$<?php echo $C35 ?></td></tr>
        <tr><td ><p><span>Total revenue from exsting customer : </span></p></td><td>&nbsp;$<?php echo $C36 ?></td></tr>

        <tr>
            <td colspan="2" class="dashed_line">
                <p class="report_heading"><?php echo $merchant_msg['report']['customer_acquisition_cost']; ?></p>
            </td>
        </tr>
        <tr><td ><p><span>New customer acquisition cost : </span></p></td><td>&nbsp;$<?php echo $C40 ?></td></tr>
        <tr><td ><p><span>Existing customer acquisition cost : </span></p></td><td>&nbsp;$<?php echo $C41 ?></td></tr>
    </table>
</div>     
<?php
if($_SESSION['merchant_info']['merchant_parent'] != 0)
{
$media_acc_array = array();
$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
$RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
$location_val = $RSmedia->fields['location_access'];

//$Sql = "SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM campaign_location cl WHERE cl.campaign_id = ".$cid." ) and l.active=1 and l.id=".$location_val;
$RS_locations = $objDB->Conn->Execute("SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM campaign_location cl WHERE cl.campaign_id =? ) and l.active=1 and l.id=?", array($cid, $location_val));

}
else
{
//$Sql = "SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM campaign_location cl WHERE cl.campaign_id = ".$cid." ) and l.active=1 ";
$RS_locations = $objDB->Conn->Execute("SELECT * FROM locations l WHERE l.id IN (SELECT cl.location_id FROM campaign_location cl WHERE cl.campaign_id =? ) and l.active=?", array($cid, 1));
}
//$RS_locations =  $objDB->execute_query($Sql);

while($Row_location = $RS_locations->FetchRow()){ $where_clause = array();
$m_arr = array();
$m_arr['campaign_id'] = $cid;
$m_arr['location_id'] = $Row_location['id'];
$RS_m_c = $objDB->Show("campaign_location", $m_arr);
$max_coupon = $RS_m_c->fields['num_activation_code'];
/* $remain_sql="select count(*) as total from coupon_codes where customer_campaign_code =".$cid." AND location_id=".$Row_location['id'];

  $RS_remain = $objDB->Conn->Execute($remain_sql); */
$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes where customer_campaign_code =? AND location_id=?", array($cid, $Row_location['id']));

$remain_val = $RS_remain->fields['total'];
//    
/* $r_sql = "SELECT * FROM `reward_user` where campaign_id = ".$cid." and coupon_code_id in (select id from coupon_codes where customer_campaign_code=".$RS->id." AND location_id =".$Row_location['id']." ) and referred_customer_id=0 group by campaign_id ,customer_id";

  $RS_redeem = $objDB->Conn->Execute($r_sql); */
$RS_redeem = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id = ? and coupon_code_id in (select id from coupon_codes where customer_campaign_code=? AND location_id =? ) and referred_customer_id=0 group by campaign_id ,customer_id", array($cid, $RS->id, $Row_location['id']));
$redeem_val = $RS_redeem->RecordCount();

$remain_val = $remain_val-$redeem_val;
?>
<div class="location_heading">
    <span><?php echo "Location : "; ?><?php echo $Row_location['location_name']." "; ?><?= $Row_location['address'].", ". $Row_location['city'].", ". $Row_location['state'].", ". $Row_location['zip'] ?></span>
<!--                    <span class="plus_<?php echo $Row_location['id'] ?>">+</span>-->
    <div class="mainIcon" id="report_toggleIt-reportplus_<?php echo $Row_location['id'] ?>" ></div> <!--<span style="padding-left: 20px">Collapse</span> -->

</div> 
<div class="mer_chant_div show_hide" id="reportplus_<?php echo $Row_location['id'] ?>" style="display:none">

<div style="display:none;" class="cmp_loader_location" id="cmp_loc_loader_<?php echo $Row_location['id'] ?>">
	<span class="load_dt">Please wait we are generating your location report ...</span>								
</div>

    <!--<div class="mer_chant_div show_hide" id="reportplus_<?php echo $Row_location['id'] ?>" style="display:none">-->

<?php
$lid = $Row_location['id'];
$cid = $_REQUEST['id'];
/* $sql1 = "SELECT *  FROM coupon_codes WHERE customer_campaign_code =".$cid." and location_id=".$lid;
  $RS1 =  $objDB->Conn->Execute($sql1); */
$RS1 = $objDB->Conn->Execute("SELECT *  FROM coupon_codes WHERE customer_campaign_code =? and location_id=?", array($cid, $lid));

$total_reserved_coupon = $RS1->RecordCount();
$arr_exsting_cust = array();
$arr_new_cust = array();

/* $remain_sql="select count(*) as total from coupon_codes where customer_campaign_code =".$cid." and location_id=".$lid;

  $RS_remain = $objDB->Conn->Execute($remain_sql); */
$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes where customer_campaign_code =? and location_id=?", array($cid, $lid));
$remain_val = $RS_remain->fields['total'];
//    
/* $referral_sql = "SELECT sum(referral_reward) as total FROM `reward_user` where campaign_id = ".$cid." and referred_customer_id<>0 and location_id=".$lid;
  $RS_ref =  $objDB->Conn->Execute($referral_sql); */
$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` where campaign_id = ? and referred_customer_id<>? and location_id=?", array($cid, 0, $lid));

/* $sql_tot_redeem_coupon = "SELECT *  FROM `reward_user` where campaign_id=".$cid." and referred_customer_id=0   and location_id=".$lid." ";
  $RS_tot_redeem_coupon =  $objDB->Conn->Execute( $sql_tot_redeem_coupon); */
$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id=? and referred_customer_id=?   and location_id=? ", array($cid, 0, $lid));


//-- referral customer counting 
/* $referral_sql = "SELECT *  FROM `reward_user` where campaign_id = ".$cid." and referred_customer_id<>0  and location_id=".$lid;
  $RS_ref_cnt =  $objDB->Conn->Execute($referral_sql); */
$RS_ref_cnt = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and referred_customer_id<>?  and location_id=?", array($cid, 0, $lid));

$totla_redeem_point_by_exsting_cust_ref = 0;
$totla_redeem_point_by_new_cust_ref = 0;
while($Row1 = $RS_ref_cnt->FetchRow())
{

/* $sql2 =  "SELECT *  FROM `reward_user` where campaign_id = ".$cid." and customer_id = ".$Row1['customer_id']." and referred_customer_id<>0 and location_id=".$lid;
  $RS2 =  $objDB->Conn->Execute($sql2); */
$RS2 = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and customer_id =? and referred_customer_id<>? and location_id=?", array($cid, $Row1['customer_id'], 0, $lid));

if($RS2->RecordCount()== 1)
{
if(!key_exists($Row1['customer_id'], $arr_new_cust))
{
$arr_new_cust_ref[$Row1['customer_id']] = $RS2->RecordCount();
}
$totla_redeem_point_by_new_cust_ref = $totla_redeem_point_by_new_cust_ref + $Row1['referral_reward'];
}
else if($RS2->RecordCount()>1) {
$arr_exsting_cus_reft[$Row1['customer_id']] = $RS2->RecordCount();
$totla_redeem_point_by_exsting_cust_ref = $totla_redeem_point_by_exsting_cust_ref+$Row1['referral_reward'];
}

}

//--- referral customer counting
//--- count exsting /new reserve  coupons

/* $r_sql_1 = "Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
  customer_campaign_code=".$cid." and     ( location_id=".$lid."  )   ";
  //$r_sql_1 = "Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes WHERE customer_campaign_code=".$cid." and   ( location_id=".$lid."  )  ";

  $RS_reserve = $objDB->Conn->Execute($r_sql_1); */
$RS_reserve = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
						customer_campaign_code=? and ( location_id=?)", array($cid, $lid));

$total_reserved_by_new_cust = array();
$total_reserved_by_exist_cust = array();
$arr_age = Array();
$agewisegender = Array();
$male_gender = 0;
$unknown_gender = 0;
$female_gender = 0;
while($Row1 = $RS_reserve->FetchRow())
{


//  $sql_reserve_count = "select * from coupon_codes WHERE customer_campaign_code=".$cid." and customer_id= ".$Row1['customer_id']." and (location_id=".$lid." )";
/* $sql_reserve_count = "select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ".$Row1['customer_id']." and (location_id=".$lid." ) ) ";
  $RS2 =  $objDB->Conn->Execute($sql_reserve_count); */
$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? and (location_id=? ) ) ", array($Row1['customer_id'], $lid));

if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
{
if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust))
{
$total_reserved_by_new_cust[$Row1['customer_id']] = $RS2->RecordCount();
}
}
else {
array_push($total_reserved_by_exist_cust, $Row1['customer_id']);
}
}

$coupons_reserved_by_new_customer = count($total_reserved_by_new_cust);
$coupons_reserved_by_exsting_customer = count($total_reserved_by_exist_cust);


//--- count exsting /new reserve coupons
//     $r_sql = "SELECT * FROM `reward_user` where campaign_id = ".$cid." and coupon_code_id in (select id from coupon_codes where customer_campaign_code=".$cid." and location_id=".$lid." ) and referred_customer_id=0 and location_id=".$lid;
// $r_sql = "SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , 
//cr.coupon_id , cr.redeem_value FROM `coupon_redeem` cr , coupon_codes cc 
//	where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid."  and cc.location_id=".$lid;
/* $r_sql = "SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
  FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid."  and cc.location_id=".$lid;

  $RS_redeem = $objDB->Conn->Execute($r_sql); */
$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
					FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cr.coupon_id= cc.id and cc.customer_campaign_code=?  and cc.location_id=?", array($cid, $lid));

$redeem_val = $RS_redeem->RecordCount();

$remain_val = $remain_val-$redeem_val;
$total_redeem_point = 0;
$total_referral_point = $RS_ref->fields['total'];
$totla_redeem_point_by_new_cust = 0;
$totla_redeem_point_by_exsting_cust = 0;
$total_revenue_cost_by_new_cust = 0;
$total_revenue_cost_by_exist_cust = 0;
while($Row1 = $RS_redeem->FetchRow())
{
if ($Row1['gender'] == "") {

$unknown_gender = $unknown_gender + 1;
} else if ($Row1['gender'] == 1) {

$male_gender = $male_gender + 1;
} else {

$female_gender = $female_gender + 1;
}

$today = new DateTime();
$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
$interval = $today->diff($birthdate);
$age = $interval->format('%y');
array_push($arr_age, $age);
array_push($agewisegender, $Row1['gender']);

// $r_sql_unique = "select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ".$Row1['customer_id']." and ( location_id =".$lid." )  ) ";
// $sql2 =  "SELECT * FROM `reward_user` where campaign_id = ".$cid." and customer_id = ".$Row1['customer_id']." and  coupon_code_id in (select id from coupon_codes where customer_campaign_code=".$cid." and location_id=".$lid." ) and referred_customer_id=0 and location_id=".$lid;
$r_sql1 = "SELECT * FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid." and  cc.customer_id=".$Row1['customer_id'] ." and cc.location_id=".$lid;
// $sql2 =  "SELECT * FROM `reward_user` where campaign_id = ".$cid." and customer_id = ".$Row1['customer_id']." and   referred_customer_id=0 and location_id=".$lid;
//$RS2 =  $objDB->Conn->Execute($r_sql_unique);
/* $Rs3 =  $objDB->Conn->Execute($sql2); */
$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? and ( location_id =? )  ) ", array($Row1['customer_id'], $lid));
$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? and location_id=?", array($cid, $Row1['customer_id'], 0, $lid));

if($RS2->RecordCount()== 1)
{
if(!key_exists($Row1['customer_id'], $arr_new_cust))
{
$arr_new_cust[$Row1['customer_id']] = $RS2->RecordCount();
}
$total_revenue_cost_by_new_cust = $total_revenue_cost_by_new_cust + $Row1['redeem_value'];
$totla_redeem_point_by_new_cust = $totla_redeem_point_by_new_cust + $Rs3->fields['redeem_value'];
}
else if($RS2->RecordCount()>1) {
//  $arr_exsting_cust[$Row1['customer_id']]= $RS2->RecordCount();
array_push($arr_exsting_cust, $Row1['customer_id']);
$totla_redeem_point_by_exsting_cust = $totla_redeem_point_by_exsting_cust + $Rs3->fields['earned_reward'];

$total_revenue_cost_by_exist_cust = $total_revenue_cost_by_exist_cust + $Row1['redeem_value'];
}
$total_redeem_point = $total_redeem_point + $Row1['earned_reward'];
}
$total_transaction_point_ex = 0;
$total_transaction_point_new = 0;
$total_transaction_only_points_ex = 0;
$total_transaction_only_points_new = 0;
$arr_new_cust = array_keys($arr_new_cust);
$arr_exsting_cust_unique = array_unique($arr_exsting_cust);
//for($i=0;$i<count($arr_exsting_cust_unique);$i++)
foreach($arr_exsting_cust_unique as $key => $value)
{
/* $r_sql_t_f = "SELECT sum(transaction_fees_price) as total_transaction_fees  , sum(transaction_fees) as total_transaction_points  FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid." and  cc.customer_id=".$arr_exsting_cust_unique[$key] ." and cc.location_id=".$lid;
  $rs_t_f = $objDB->Conn->Execute($r_sql_t_f); */
$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees  , sum(transaction_fees) as total_transaction_points  FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=? and cc.location_id=?", array($cid, $arr_exsting_cust_unique[$key], $lid));
$total_transaction_point_ex = $total_transaction_point_ex + $rs_t_f->fields['total_transaction_fees'];
$total_transaction_only_points_ex = $total_transaction_only_points_ex + $rs_t_f->fields['total_transaction_points'];

}

//for($i=0;$i<count($arr_new_cust);$i++)
foreach($arr_new_cust as $key => $value)
{
/* $r_sql_t_f = "SELECT sum(transaction_fees_price) as total_transaction_fees,  sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=".$cid." and  cc.customer_id=".$arr_new_cust[$key] ." and cc.location_id=".$lid;
  $rs_t_f = $objDB->Conn->Execute($r_sql_t_f); */
$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees,  sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=? and cc.location_id=?", array($cid, $arr_new_cust[$key], $lid));
$total_transaction_point_new = $total_transaction_point_new + $rs_t_f->fields['total_transaction_fees'];
$total_transaction_only_points_new = $total_transaction_only_points_ex + $rs_t_f->fields['total_transaction_points'];

}
$tot_revenue_cost_existing = $totla_redeem_point_by_exsting_cust_ref+ $totla_redeem_point_by_exsting_cust;
$tot_revenue_cost_new = $totla_redeem_point_by_new_cust+$totla_redeem_point_by_new_cust_ref;
$C14 = $total_reserved_coupon;
$C15 = count($arr_exsting_cust);
$C16 = count($arr_new_cust);
$C17 = $C15 + $C16;
//	echo "<<<<<<<<".$C17.">>>>>>";
$C18 = $C14 - ($RS_tot_redeem_coupon->RecordCount());

/* calculate tota refferal points */
/* $Sql_2_share =  "SELECT * FROM reward_user WHERE referred_customer_id <> 0 and referral_reward<>0 and  campaign_id =".$cid ." and location_id=".$lid ;

  $RS_2_share = $objDB->Conn->Execute($Sql_2_share); */
$RS_2_share = $objDB->Conn->Execute("SELECT * FROM reward_user WHERE referred_customer_id <> 0 and referral_reward<>0 and  campaign_id =? and location_id=?", array($cid, $lid));

$total_share_count = $RS_2_share->RecordCount();

/* */
$C22 = $total_share_count * $B7;
// $C22 = $C16 * $B7;
$total_transaction_point = $total_transaction_only_points_ex + $total_transaction_only_points_new;
$total_transaction_fee = $total_transaction_point_ex + $total_transaction_point_new;
$C23 = $C15 * $B8;
$C24 = $C16 * $B8;
$C25 = $C23 + $C24;
$C26 = $C25 + $C22;

$C29 = $C22 * $B4;
/* $C30 = $C24 * $B4;
  $C31 = $C23 * $B4; */
$C30 = ($C24 * $B4); // +  $total_transaction_point_new;
$C31 = ($C23 * $B4); // +  $total_transaction_point_ex;
$C32 = $C30 + $C31;
$C28 = $C29 + $C32 + $total_transaction_fee;


$C34 = $total_revenue_cost_by_new_cust + $total_revenue_cost_by_exist_cust;
$C35 = $total_revenue_cost_by_new_cust;
$C36 = $total_revenue_cost_by_exist_cust;
//                            $C34 = $C17 * $B3;
//                            $C35 = $C16 * $B3;
//                            $C36 = $C15 * $B3;

if($C35!=0)
{
	$C40 = round(($C29 + $C30)/$C35, 2); //($C29 + $C30)/$C35;
}
if($C36!=0)
{
	$C41 = round(($C29 + $C31)/$C36, 2); //($C29 + $C31)/$C36;
}

if(strlen($C40) == 0)
{
$C40 = 0;
}
if(strlen($C41) == 0)
{
$C41 = 0;
}
$total_cust = $C17;
// echo "hii";
$sql_all_domains = "select * from share_domain ";
$RS_domains_data = $objDB->Conn->Execute($sql_all_domains);
$domain_arr['share_email'] = 0;
$domain_arr['pageview_email'] = 0;
$domain_arr['share_facebook'] = 0;
$domain_arr['pageview_facebook'] = 0;
$domain_arr['share_twitter'] = 0;
$domain_arr['pageview_twitter'] = 0;
$domain_arr['share_google'] = 0;
$domain_arr['pageview_google'] = 0;
$domain_arr['share_other'] = 0;
$domain_arr['pageview_other'] = 0;
$domain_arr['pageview_allmedium'] = 0;
$domain_arr['pageview_qrcode'] = 0;
$display_flag = false;
$display_flag1 = false;
while($Row_domain = $RS_domains_data->FetchRow())
{
/* $sql_t = "select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
  where campaign_id = ".$_REQUEST['id']." and location_id = ".$lid." and d.id=". $Row_domain['id'];

  $RS_t = $objDB->Conn->Execute($sql_t); */
$RS_t = $objDB->Conn->Execute("select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
										where campaign_id = ? and location_id = ? and d.id=?", array($_REQUEST['id'], $lid, $Row_domain['id']));

while($Row_total = $RS_t->FetchRow())
{
if($Row_total['total'] >0)
{
$display_flag = true;
}
if($Row_domain['id'] == 1)
{
$domain_arr['share_facebook'] = $domain_arr['share_facebook'] + $Row_total['total'];
}
else if($Row_domain['id'] == 2)
{
$domain_arr['share_twitter'] = $domain_arr['share_twitter'] + $Row_total['total'];
}
else if($Row_domain['id'] == 3)
{
$domain_arr['share_google'] = $domain_arr['share_google'] + $Row_total['total'];
}
else if($Row_domain['id'] == 4)
{
$domain_arr['share_email'] = $domain_arr['share_email'] + $Row_total['total'];
}
else if($Row_domain['id'] == 5)
{
$domain_arr['share_other'] = $domain_arr['share_other'] + $Row_total['total'];
}
}

$only_values = array_values($domain_arr);

/* $sql_t = "select count(*) as total ,  p.campaign_id , p.location_id , d.domain from pageview_counter p inner join share_domain d on d.id= p.pageview_domain
  where campaign_id = ".$_REQUEST['id']." and location_id = ".$lid." and d.id=". $Row_domain['id'];

  $RS_t = $objDB->Conn->Execute($sql_t); */
$RS_t = $objDB->Conn->Execute("select count(*) as total ,  p.campaign_id , p.location_id , d.domain from pageview_counter p inner join share_domain d on d.id= p.pageview_domain
										where campaign_id = ? and location_id =? and d.id=?", array($_REQUEST['id'], $lid, $Row_domain['id']));

while($Row_total = $RS_t->FetchRow())
{
if($Row_total['total'] >0)
{
$display_flag1 = true;
}
if($Row_domain['id'] == 1)
{
$domain_arr['pageview_facebook'] = $domain_arr['pageview_facebook'] + $Row_total['total'];
}
else if($Row_domain['id'] == 2)
{
$domain_arr['pageview_twitter'] = $domain_arr['pageview_twitter'] + $Row_total['total'];
}
else if($Row_domain['id'] == 3)
{
$domain_arr['pageview_google'] = $domain_arr['pageview_google'] + $Row_total['total'];
}
else if($Row_domain['id'] == 4)
{
$domain_arr['pageview_email'] = $domain_arr['pageview_email'] + $Row_total['total'];
}
else if($Row_domain['id'] == 5)
{
$domain_arr['pageview_other'] = $domain_arr['pageview_other'] + $Row_total['total'];
}
}
}
/* * ** qr code scan ** */
/* $sql_t = "select * from scan_qrcode where campaign_id = ".$_REQUEST['id'] ." and location_id=".$lid;

  $RS_qrcodes_view = $objDB->Conn->Execute($sql_t); */
$RS_qrcodes_view = $objDB->Conn->Execute("select * from scan_qrcode where campaign_id = ? and location_id=?", array($_REQUEST['id'], $lid));
if($RS_qrcodes_view->RecordCount() > 0)
{
$domain_arr['pageview_qrcode'] = $RS_qrcodes_view->RecordCount();
if($RS_qrcodes_view->RecordCount() > 0)
{
$display_flag1 = true;
}
}
$domain_arr['pageview_allmedium'] = $domain_arr['pageview_other']+$domain_arr['pageview_email']+$domain_arr['pageview_google']+$domain_arr['pageview_twitter']+$domain_arr['pageview_facebook'];
$only_keys = array_keys($domain_arr);

$only_values = array_values($domain_arr);
?>
    <?php
    $one = 0;
    $two = 0;
    $three = 0;
    $four = 0;
    $five = 0;
    $dispplay_rating_flag = false;
    $key_value_pair = array();

    /* $sql = "select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  
      where   campaign_id= ".$_REQUEST['id']." and location_id=".$lid." group by re.rating";

      $RS_ = $objDB->Conn->Execute($sql); */
    $RS_ = $objDB->Conn->Execute("select re.rating avarage_rating, count(*) avarage_rating_counter  from review_rating re  
						 where   campaign_id= ? and location_id=? group by re.rating", array($_REQUEST['id'], $lid));
    $avarage_rating = array();
    if($RS_->RecordCount() > 0 )
    {
    $dispplay_rating_flag = true;
    }
    while ($rating_row = $RS_->FetchRow()) {

    $total_ratings = $total_ratings + $rating_row['avarage_rating_counter'];
    if($rating_row['avarage_rating'] <=1)
    {
    $one = $one + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Poor'] = $one;
    }
    else if($rating_row['avarage_rating'] >1 && $rating_row['avarage_rating'] <= 2)
    {
    $two = $two + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Fair'] = $two;
    }
    else if($rating_row['avarage_rating'] >2 && $rating_row['avarage_rating'] <= 3)
    {
    $three = $three + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Good'] = $three;
    }
    else if($rating_row['avarage_rating'] >3 && $rating_row['avarage_rating'] <= 4)
    {
    $four = $four + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Very Good'] = $four;
    }
    else if($rating_row['avarage_rating'] >4 && $rating_row['avarage_rating'] <= 5)
    {
    $five = $five + $rating_row['avarage_rating_counter'];
    $key_value_pair ['Excellent'] = $five;
    }

    }
    //echo $one."=".$two."=".$three."=".$four."=".$five;
    ?>

    <?php if($display_flag1) { ?>

    <?php } ?>
    <?php if($display_flag) { ?>
    <script>

    </script>
    <?php } ?>
    <?php if($dispplay_rating_flag){ ?>
    <script>

    </script>
    <?php } ?>
    <?php
    if($total_cust != 0)
    {
    ?>

    <?php
    $c_1 = $c_2 = $c_3 = $c_4 = $c_5 = $c_6 = 0;


    $arr_age_txt = array();
    $agewise_gender_male['65 Or Above'] = 0;
    $agewise_gender_male['55 to 64'] = 0;
    $agewise_gender_male['45 to 54'] = 0;
    $agewise_gender_male['25 to 44'] = 0;
    $agewise_gender_male['18 to 24'] = 0;
    $agewise_gender_male['17 Or Below'] = 0;

    $agewise_gender_female['65 Or Above'] = 0;
    $agewise_gender_female['55 to 64'] = 0;
    $agewise_gender_female['45 to 54'] = 0;
    $agewise_gender_female['25 to 44'] = 0;
    $agewise_gender_female['18 to 24'] = 0;
    $agewise_gender_female['17 Or Below'] = 0;
    for ($c = 0;
    $c < count($arr_age);
    $c++) {
    if ($arr_age[$c] >= 65) {
    if($agewisegender[$c] == 1)
    {

    $agewise_gender_male['65 Or Above'] = $agewise_gender_male['65 Or Above'] +1;
    }
    else if($agewisegender[$c] == 2)
    {
    $agewise_gender_female['65 Or Above'] = $agewise_gender_female['65 Or Above'] +1;
    }

    $c_6 = $c_6 + 1;
    } else if ($arr_age[$c] >= 55 && $arr_age[$c] <= 64) {
    if($agewisegender[$c] == 1)
    {
    $agewise_gender_male['55 to 64'] = $agewise_gender_male['55 to 64'] +1;
    }
    else if($agewisegender[$c] == 2)
    {
    $agewise_gender_female['55 to 64'] = $agewise_gender_female['55 to 64'] +1;
    }
    $c_5 = $c_5 + 1;
    } else if ($arr_age[$c] >= 45 && $arr_age[$c] <= 54) {
    if($agewisegender[$c] == 1)
    {
    $agewise_gender_male['45 to 54'] = $agewise_gender_male['45 to 54'] +1;
    }
    else if($agewisegender[$c] == 2)
    {
    $agewise_gender_female['45 to 54'] = $agewise_gender_female['45 to 54'] +1;
    }
    $c_4 = $c_4 + 1;
    } else if ($arr_age[$c] >= 25 && $arr_age[$c] <= 44) {
    if($agewisegender[$c] == 1)
    {
    $agewise_gender_male['25 to 44'] = $agewise_gender_male['25 to 44'] +1;
    }
    else if($agewisegender[$c] == 2)
    {
    $agewise_gender_female['25 to 44'] = $agewise_gender_female['25 to 44'] +1;
    }
    $c_3 = $c_3 + 1;
    } else if ($arr_age[$c] >= 18 && $arr_age[$c] <= 24) {
    if($agewisegender[$c] == 1)
    {
    $agewise_gender_male['18 to 24'] = $agewise_gender_male['18 to 24'] +1;
    }
    else if($agewisegender[$c] == 2)
    {
    $agewise_gender_female['18 to 24'] = $agewise_gender_female['18 to 24'] +1;
    }
    $c_2 = $c_2 + 1;
    } else if ($arr_age[$c] <= 17) {
    if($agewisegender[$c] == 1)
    {
    $agewise_gender_male['17 Or Below'] = $agewise_gender_male['17 Or Below'] +1;
    }
    else if($agewisegender[$c] == 2)
    {
    $agewise_gender_female['17 Or Below'] = $agewise_gender_female['17 Or Below'] +1;
    }
    $c_1 = $c_1 + 1;
    }
    }
    //array_push()

    if ($c_1 > 0) {
    $arr_age_txt['17 Or Below'] = $c_1;
    }
    if ($c_2 > 0) {
    $arr_age_txt['18 to 24'] = $c_2;
    }
    if ($c_3 > 0) {
    $arr_age_txt['25 to 44'] = $c_3;
    }
    if ($c_4 > 0) {
    $arr_age_txt['45 to 54'] = $c_4;
    }
    if ($c_5 > 0) {
    $arr_age_txt['55 to 64'] = $c_5;
    }
    if ($c_6 > 0) {
    $arr_age_txt['65 Or Above'] = $c_6;
    }

    $male_per = ($male_gender * 100) / $total_cust;
    $unknowne_per = ($unknown_gender * 100) / $total_cust;
    $female_per = ($female_gender * 100 ) / $total_cust;
    $gender1 = round($male_per, 2);
    $gender2 = round($female_per, 2);
    $gender3 = round($unknowne_per, 2);



    $ahm1 = round((($agewise_gender_male['17 Or Below'] * 100) / $total_cust));
    $ahm2 = round((( $agewise_gender_male['18 to 24'] * 100) / $total_cust));
    $ahm3 = round((($agewise_gender_male['25 to 44'] * 100) / $total_cust));
    $ahm4 = round((($agewise_gender_male['45 to 54'] * 100) / $total_cust));
    $ahm5 = round((($agewise_gender_male['55 to 64']* 100) / $total_cust));
    $ahm6 = round((($agewise_gender_male['65 Or Above'] * 100) / $total_cust));
    $afm1 = round((($agewise_gender_female['17 Or Below'] * 100) / $total_cust));
    $afm2 = round((( $agewise_gender_female['18 to 24'] * 100) / $total_cust));
    $afm3 = round((($agewise_gender_female['25 to 44'] * 100) / $total_cust));
    $afm4 = round((($agewise_gender_female['45 to 54'] * 100) / $total_cust));
    $afm5 = round((($agewise_gender_female['55 to 64']* 100) / $total_cust));
    $afm6 = round((($agewise_gender_female['65 Or Above'] * 100) / $total_cust));
    $total_age_arr = round((($c_1 * 100) / $total_reserved_coupon)) . "-" . round((($c_2 * 100) /$total_reserved_coupon)) . "-" . round((($c_3 * 100) / $total_reserved_coupon)) . "-" . round((($c_4 * 100) / $total_cust)) . "-" . round((($c_5 * 100) / $total_cust)) . "-" . round((($c_6 * 100) / $total_cust));
    //  $coupons_reserved_by_new_customer = count($total_reserved_by_new_cust);
    //  $coupons_reserved_by_exsting_customer = count($total_reserved_by_exist_cust); 
    ?>
    <div align="center" class="campaign_report_div_left">
        <div cust_avail="0" class="campaign_report_gender" id="locationidgender_<?php echo $Row_location['id'] ?>" genderdata="<?php echo $gender1."-".$gender2; ?>" >

        </div>
    </div>
    <div align="center" class="campaign_report_div_left">
        <div class="campaign_report_gender" id="containeragewisegender_<?php echo $Row_location['id'] ?>" maledata="<?php echo $ahm1.'-'.$ahm2.'-'.$ahm3.'-'.$ahm4.'-'.$ahm5.'-'.$ahm6; ?>" femaledata="<?php echo $afm1.'-'.$afm2.'-'.$afm3.'-'.$afm4.'-'.$afm5.'-'.$afm6; ?>" >

        </div>
    </div>
<?php
}
?>

    <?php if($display_flag1) { ?>
    <div align="center" class="campaign_report_div_left1">
        <div cust_avail="0" class="campaign_report_gender1" id="summerisedview_<?php echo $lid; ?>" sharingview="<?php echo $only_values[1]."-".$only_values[3]."-".$only_values[5]."-".$only_values[7]."-".$only_values[9]."-".$only_values[10]."-".$only_values[11]; ?>"  >

        </div>
    </div>
    <?php } ?>
<?php if($display_flag) { ?>
    <div align="center" class="campaign_report_div_left1">
        <div class="campaign_report_gender1" id="summerisedshare_<?php echo $lid; ?>" sharingdata="<?php echo $only_values[0]."-".$only_values[2]."-".$only_values[4]."-".$only_values[6]."-".$only_values[8]; ?>" >

        </div>
    </div>
    <?php } ?>
<?php if($dispplay_rating_flag){ ?>
    <div align="center" class="campaign_report_div_left">
        <div cust_avail="0" class="campaign_report_gender" id="summerisedrating_<?php echo $lid; ?>"  ratingdata="<?php echo $five."-".$four."-".$three."-".$two."-".$one; ?>" >

        </div>
    </div>

<?php } ?>


    <table width="100%" cellspacing="0" class="showhide_tab">  
        <tr>
            <td colspan="2"  class="dashed_line">
                <p class="report_heading"><?php echo $merchant_msg['report']['campaign_metrics']; ?></p>
            </td>
        </tr>
        <tr><td ><p><span>Total number of activation code issued</span></p></td><td>&nbsp;<?php echo $C14; ?></td></tr>
        <tr><td ><p><span>Total number of activation code reserved by existing customer : </span></p></td><td width="20%"> &nbsp;<?php echo $coupons_reserved_by_exsting_customer; ?></td></tr>
        <tr><td ><p><span>Total number of activation code reserved by New customer : </span></p></td><td width="20%"> &nbsp;<?php echo $coupons_reserved_by_new_customer; ?></td></tr>
        <tr><td ><p><span>Total number of activation code redeemed by existing customer : </span></p></td><td>&nbsp;<?php echo $C15; ?></td></tr>
        <tr><td ><p><span>Total number of activation code redeemed by new customer : </span></p></td><td>&nbsp;<?php echo $C16; ?></td></tr>
        <tr><td ><p><span>Total number of activation code redeemed by customer : </span></p></td><td>&nbsp;<?= $C17; ?></td></tr>
        <tr><td ><p><span>Total number of activation code reserved by customer but not redeemed : </span></p></td><td>&nbsp;<?= $C18 ?></td></tr>
        <tr>
            <td colspan="2"  class="dashed_line" >
                <p class="report_heading"><?php echo $merchant_msg['report']['campaign_qr_code_scan_metrics']; ?></p>
            </td>
        </tr>
<?php
/* $sql = " select distinct a.* ,b.location_name ,b.created_by ,b.id location_id 
  from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id
  where a.campaign_id=".$cid." and b.id=".$lid;

  $RS_2_qrcode = $objDB->Conn->Execute($sql); */
$RS_2_qrcode = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id 
                                                                    from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id 
								   where a.campaign_id=? and b.id=?", array($cid, $lid));

/* $sql = " select distinct a.* ,b.location_name ,b.created_by ,b.id location_id 
  from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id
  where a.campaign_id=".$cid." and a.is_unique=1 and b.id=".$lid;

  $RS_2_qrcodeun = $objDB->Conn->Execute($sql); */
$RS_2_qrcodeun = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id 
                                                                    from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id 
								   where a.campaign_id=? and a.is_unique=1 and b.id=?", array($cid, $lid));
?>
        <tr><td ><p><span>Total number of QR Code scans</span></p></td><td>&nbsp;<?php echo $RS_2_qrcode->RecordCount(); ?></td></tr>
        <tr><td ><p><span>Total number of unique QR Code scans : </span></p></td><td width="20%"> &nbsp;<?php echo $RS_2_qrcodeun->RecordCount(); ?></td></tr>
        <tr>
            <td colspan="2" class="dashed_line">
                <p class="report_heading"><?php echo $merchant_msg['report']['scanflip_point_metrics']; ?></p>
            </td>
        </tr>
<?php
$arr = file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
if(trim($arr[0]) == "")
{
unset($arr[0]);
$arr = array_values($arr);
}
$json = json_decode($arr[0]);
$total_records = $json->total_records;
$records_array = $json->records;
if($total_records>0){
foreach($records_array as $Row)
{

$price = $Row->price;
$point_ = $Row->points
?>
        <?php }} ?>


        <tr><td ><p><span>Total referral points given to new and existing customer : </span></p></td><td>&nbsp;<?= $C22 ?></td></tr>
        <tr><td ><p><span>Total redemption point given to existing customer : </span></p></td><td>&nbsp;<?= $C23 ?></td></tr>
        <tr><td ><p><span>Total redemption point given to new customer : </span></p></td><td>&nbsp;<?= $C24 ?></td></tr>
        <tr><td ><p><span>Total redemption point given to new and existing customer : </span></p></td><td>&nbsp;<?= $C25 ?></td></tr>
        <tr><td ><p><span>Total referral and redemption point given to new and exsting customer : </span></p></td><td>&nbsp;<?= $C26 ?></td></tr>
        <tr><td ><p><span>Total transaction points ( minimum coupon redemption fee in points) : </span></p></td><td>&nbsp;<?php echo $total_transaction_point; ?></td></tr>
        <tr>
            <td class="dashed_line">
                <p class="report_heading"><?php echo $merchant_msg['report']['toatl_campaign_cost']; ?></p></td><td class="dashed_line">$<?php echo $C28 ?>
            </td>
        </tr>


        <tr><td ><p><span>Total referral/viral marketing cost : </span></p></td><td>&nbsp;$<?= $C29 ?></td></tr>
        <tr><td ><p><span>Total redemption points cost for new  customer : </span></p></td><td>&nbsp;$<?= $C30 ?></td></tr>
        <tr><td ><p><span>Total redemption points cost for existing customer : </span></p></td><td>&nbsp;$<?= $C31 ?></td></tr>

        <tr><td ><p><span>Total redemption points cost for new and existing customers : </span></p></td><td>&nbsp;$<?= $C32 ?></td></tr>
        <tr><td ><p><span>Total transaction cost ( minimum coupon redemption cost) : </span></p></td><td>&nbsp;$<?php echo $total_transaction_fee; ?></td></tr>

<!--<tr><td ><p><span>Total referral and redemption point given to new and existing customer : </span></p></td><td>&nbsp;$<?= ((($total_redeem_point+$total_referral_point) * $price)/$point_) ?></td></tr>-->
<!--    <tr>
<td style="border-top:1px dashed #D4D4D4"  colspan="2" >
<p  class="report_heading"><?php echo $merchant_msg['report']['total_transaction_fee']; ?></p></td>
</tr>
<tr><td ><p><span>Total transaction point : </span></p></td><td>&nbsp;<?php echo $total_transaction_point; ?></td></tr>
<tr><td ><p><span>Total transaction fee : </span></p></td><td>&nbsp;$<?php echo $total_transaction_fee; ?></td></tr>
        -->

        <tr>
            <td class="dashed_line" >
                <p  class="report_heading"><?php echo $merchant_msg['report']['total_campaign_revenue']; ?></p></td><td class="dashed_line">&nbsp;&nbsp;$<?php echo $C34; ?>
            </td>
        </tr>
        <tr><td ><p><span>Total revenue from new customer : </span></p></td><td>&nbsp;$<?php echo $C35 ?></td></tr>
        <tr><td ><p><span>Total revenue from exsting customer : </span></p></td><td>&nbsp;$<?php echo $C36 ?></td></tr>

        <tr>
            <td colspan="2" class="dashed_line">
                <p class="report_heading"><?php echo $merchant_msg['report']['customer_acquisition_cost']; ?></p>
            </td>
        </tr>
        <tr><td ><p><span>New customer acquisition cost : </span></p></td><td>&nbsp;$<?php echo $C40 ?></td></tr>
        <tr><td ><p><span>Existing customer acquisition cost : </span></p></td><td>&nbsp;$<?php echo $C41 ?></td></tr>
    </table>
</div>
<script type="text/javascript">
        /*
         chart = new Highcharts.Chart({
         chart: {
         renderTo: 'container_'+lid,
         borderWidth:1,
         plotBackgroundColor: null,
         plotBorderWidth: null,
         plotShadow: false
         },
         title: {
         text: '<?php echo $merchant_msg['report']['cust_by_gender']; ?>',
         align: 'center',
         verticalAlign: 'middle',
         y:-85
         },
         tooltip: {
         pointFormat: '<b>{point.percentage:.1f}%</b>'
         },
         plotOptions: {
         pie: {
         dataLabels: {
         enabled: true,
         distance: -30,
         style: {
         fontWeight: 'bold',
         color: 'white',
         textShadow: '0px 1px 2px black'
         }
         },
         startAngle: -90,
         endAngle: 90,
         center: ['50%', '75%']
         }
         },
         credits: {
         enabled: false
         },
         series: [{
         type: 'pie',
         showInLegend: false,
         //name: '',
         innerSize: '50%',
         data: [
         ["Male",v1],['Female',v2],
                            
         ]
         }]
         }); */
</script>
<?php
}
?>


<?php
}
}
?>

<script>
        $("span[class^='plus_']").click(function () {

            var id = $(this).attr("class");

            $("#" + id).slideToggle("slow");
        });
        
        /*
        jQuery("div[id^='report_toggleIt-']").live("click", function () {
            //alert("in click");
            var a = $(this).attr("id").split("-");
            //alert( "#"+a[1]);
            $("#" + a[1]).slideDown("slow");
            //alert("slide down");
            var loc_id = a[1].split("_")[1];

        });
		*/
		
        jQuery("div[id^='report_toggleIt-']").toggle(function () {
				
				
            var a = jQuery(this).attr("id").split("-");
			var loc_id = a[1].split("_")[1];
			
			jQuery("#" + a[1]).slideDown("slow");
			
			jQuery("#cmp_loc_loader_"+loc_id).css("display","block");
			
			/*
            jQuery("#" + a[1]).slideDown("slow", function () {
				
                draw_all_chart(loc_id);
						
            });
            */
            draw_all_chart(loc_id);

            jQuery(this).addClass('mainIcon_minus');
            
			console.log(jQuery("#cmp_loc_loader_"+loc_id).css("display"));
			
			//alert("1");
			
			//
			
			setTimeout(function(){
				jQuery("#cmp_loc_loader_"+loc_id).css("display","none"); 
			}, 1000);
			
			//alert("2");
			
			console.log(jQuery("#cmp_loc_loader_"+loc_id).css("display"));

        },function(){


                    var a = $(this).attr("id").split("-");
                    $("#" + a[1]).slideUp("fast");
                    $(this).removeClass('mainIcon_minus');

		}
        );

</script>

<!--	
</div>
-->
<!-- <script src="<?php echo WEB_PATH ?>/admin/js/jquery.js"></script> -->


