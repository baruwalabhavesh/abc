<?php
/**
 * @uses get location data for report generation
 * @used in pages :reports.php
 * @author Sangeeta Raghavani
 */

header('Content-type: text/html; charset=utf-8');
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH . "/classes/DB.php");
/******** 
@USE : Initializing chart detail
@PARAMETER : current login merchant id , year 
@RETURN : Json for initializing chart
@USED IN PAGES : reports.php
@Explaination : This report include per month report of selected year
- Example , In January report
	- All campaigns active in january month
	- Only january months transaction will be count
	- Rserved / Redeem coupon also will be count of january month
*********/
//$objDB = new DB('read');
$unknown_gender = 0;
  $male_gender = 0;
  $female_gender = 0;
  $coustomer_counter_1 = 0 ;
  $coustomer_counter_2 = 0;
     $arr_age = array();
	 $all_campaign_list_arr = array();
//echo 123;
if (isset($_REQUEST['locationchartdata'])) {
    $new_customers = array();
    $total_customers = array();
    $redeem_array = array();
    $reserve_array = array();
    $campaigns_array = array();
    $cost_array = array();
    $revenue_array = array();
    $_array = array();
    $months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
    $total_customer_arr = array();
    $total_customer_age_arr = array();
    $lid = $_REQUEST['location_id'];
	$all_users = Array();
	$cnt_new = 0;
	$agewisegender = Array();
    foreach ($months as $month) {
          $coustomer_counter_1 = 0 ;
  $coustomer_counter_2 = 0;
        if ($month == 4 || $month == 6 || $month == 9 || $month == 11) {
            $m = 30;
        } else if (isset($_REQUEST['month']) == 2) {
            $m = 28;
        } else {
            $m = 31;
        }
        $total_counpon_issued_sql = 0;
        $total_coupon_redeem_sql = 0;
        if ($_REQUEST['year'] != 0) {
            $year = $_REQUEST['year'];
        } else {
            $year = date("Y");
        }
        $from_date = $year . "-" . $month . "-01 00:00:00";
        $to_date = $year . "-" . $month . "-" . $m . " 23:59:59";
        $month_expr = " and DATE_FORMAT(b.expiration_date,'%m') =" . $month;
        //$dt_wh = "b.expiration_date between '" . $from_date . "' and  '" . $to_date . "' ";
		$dt_wh = " and (
		(
			(CONVERT_TZ(b.start_date,'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) between '".$from_date."' and '".$to_date."' ) 
			OR
			(CONVERT_TZ(b.expiration_date,'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) between '".$from_date."' and '".$to_date."' )
		)
		OR
		( 
			(CONVERT_TZ(b.start_date,'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) < '".$from_date."'  and 
				(
				(CONVERT_TZ(b.expiration_date,'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1))  > '".$to_date."') or
				(CONVERT_TZ(b.expiration_date,'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1))  between '".$to_date."' and '".$to_date."' )
				)
			)
		))";

        $arr1 = file(WEB_PATH . '/merchant/process.php?getlocationinfo=yes&loc_id=' . $lid);
        if (trim($arr1[0]) == "") {
            unset($arr1[0]);
            $arr1 = array_values($arr1);
        }

        $json1 = json_decode($arr1[0]);

        $total_records1 = $json1->total_records;
        $records_array1 = $json1->records;

        $arr = file(WEB_PATH . '/merchant/process.php?get_point_package=yes');
        if (trim($arr[0]) == "") {
            unset($arr[0]);
            $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
        $total_records_ = $json->total_records;
        $records_array_ = $json->records;

        if ($total_records_ > 0) {
            foreach ($records_array_ as $Row_) {
                $price = $Row_->price;
                $point_ = $Row_->points;
                $p = (1 * $price) / $point_;
            }
            $B4 = $p;
        }

        if ($total_records1 > 0) {
            $cnt = 0;
            $Where = "";
            foreach ($records_array1 as $Row) {

             
                $t_v = 0;
                $arr_new_cust_ref = array();
                $arr_exsting_cust_reft = array();
				
				$c2  = 0;
                $wh_status = "";
                $total_cust = 0;
                $a_c = 0;
                $new_cust = 0;
                if (isset($_REQUEST['status'])) {
                    if ($_REQUEST['status'] == "active") {
                        $wh_status = " and cl.active=1  and cl.active_in_future<>1";
                    } elseif ($_REQUEST['status'] == "expire") {
                        $wh_status = " and cl.active<>1 and cl.active_in_future<>1";
                    } elseif ($_REQUEST['status'] == "end") {
                        $wh_status = "";
                    }
                } else {
                    $wh_status = "";
                }

                $Where .= " AND CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date ";
                $dt_wh1 = "";

                //  $tc_sql = $sql = "SELECT  distinct c.* , cl.location_id location_id , cl.num_activation_code num_activation_code   from  campaigns c , campaign_location cl , locations l where l.id=cl.location_id and cl.location_id=".$Row->id." and c.id = cl.campaign_id   and ($dt_wh) $month_expr and cl.active<>1 and cl.active_in_future<>1  ";
               /* $sql = "SElECT a.location_id location_id , a.num_activation_code num_activation_code  , b.* 
				from campaign_location a inner join campaigns b on b.id=a.campaign_id inner join locations c on c.id= a.location_id where
                                a.location_id =" . $Row->id . "  $dt_wh ";		
	
                $RS_l = $objDB->Conn->Execute($sql);*/
		$RS_l = $objDB->Conn->Execute("SElECT a.location_id location_id , a.num_activation_code num_activation_code  , b.* 
				from campaign_location a inner join campaigns b on b.id=a.campaign_id inner join locations c on c.id= a.location_id where a.location_id =?  $dt_wh ",array($Row->id));
               
                $campaign_list_arr = array();

                $t_c = $RS_l->RecordCount();

				$new_cust = 0;
                $total_coupon_issued = 0;
                $total_cost = 0;
				$total_cost_sharing = 0;
                $total_coupon_redeemed = 0;
				$total_transaction_point_ex =0;
				$total_cost_redeem = 0;
                if ($RS_l->RecordCount() != 0) {


                    while ($loc = $RS_l->FetchRow()) {
                        array_push($campaign_list_arr, $loc['id']);
						array_push($all_campaign_list_arr, $loc['id']);
                    }
					
					$campaign_list = implode(",", $campaign_list_arr);
                  
				  $coupon_code_date_sql = " and generated_date between '". $from_date ."' and '".$to_date."'" ;
                    /*$total_counpon_issued_sql = "select * from coupon_codes where customer_campaign_code in (" . $campaign_list . ")  and location_id =" . $_REQUEST['location_id']." ".$coupon_code_date_sql;
                    
                    $total_coupon_issued_rs = $objDB->Conn->Execute($total_counpon_issued_sql);*/
			$total_coupon_issued_rs = $objDB->Conn->Execute("select * from coupon_codes where customer_campaign_code in (?)  and location_id =? ".$coupon_code_date_sql,array($campaign_list,$_REQUEST['location_id']));

                    $total_coupon_issued = $total_coupon_issued_rs->RecordCount();
                    $dealvalue = $loc['deal_value'];

					/*$redeem_date_sql = " and a.redeem_date between '". $from_date ."' and '".$to_date."'" ;
                    $sql = "select  c.gender , c.dob_year , c.dob_month , c.dob_day , b.customer_id ,b.customer_campaign_code ,b.location_id , a.coupon_id ,a.redeem_value 
							from `coupon_redeem` a inner join coupon_codes b on a.coupon_id = b.id inner join customer_user c on c.id= b.customer_id
							where  b.customer_campaign_code in ($campaign_list) and b.location_id =" . $_REQUEST['location_id']." ".$redeem_date_sql;

				
                    $a = $objDB->Conn->Execute($sql);*/
			$a = $objDB->Conn->Execute("select  c.gender , c.dob_year , c.dob_month , c.dob_day , b.customer_id ,b.customer_campaign_code ,b.location_id , a.coupon_id ,a.redeem_value from `coupon_redeem` a inner join coupon_codes b on a.coupon_id = b.id inner join customer_user c on c.id= b.customer_id where  b.customer_campaign_code in (?) and b.location_id =? and a.redeem_date between ? and ?",array($campaign_list,$_REQUEST['location_id'],$from_date,$to_date));

                    
                    if ($a->RecordCount() != 0) { 
                       while ($newcust_row = $a->FetchRow()) {
					   
                         
						  if(!in_array($newcust_row['customer_id'],$all_users) ){
						 
								$cnt_new =  $cnt_new + 1;
						  }
						  else{
						 
						  }
						   array_push($all_users,$newcust_row['customer_id']);
                           $t_v = $t_v + $newcust_row['redeem_value'];
                            $total_customer_arr[$newcust_row['customer_id']] = $newcust_row['gender'];
                          
                            /* counter for male female */
                             
                            /* counter for male female */
									$age = date("Y") - $newcust_row['dob_year'];
									array_push($total_customer_age_arr, $age);
									if (!key_exists($newcust_row['customer_id'], $arr_new_cust_ref)) {
										if ($newcust_row['gender'] == "") {
											$unknown_gender = $unknown_gender + 1;
										} else if ($newcust_row['gender'] == 1) {
										   $male_gender = $male_gender + 1;
										} else {
											$female_gender = $female_gender + 1;
										}
                                
                              
                                $arr_new_cust_ref[$newcust_row['customer_id']] = $newcust_row['gender'];
                                 $coustomer_counter_2 = $coustomer_counter_2 + 1;
                            
                                if ($newcust_row['dob_year'] != "") {
                                     $today = new DateTime();
                                $birthdate = new DateTime($newcust_row['dob_year']."-".$newcust_row['dob_month']."-".$newcust_row['dob_day']." 09:48:00");
                                $interval = $today->diff($birthdate);
                               $age = $interval->format('%y');
                                    array_push($arr_age, $age);
									array_push($agewisegender,$newcust_row['gender']);
                                }
                            } else {
                                if ($newcust_row['gender'] == "") {
                             
                                $unknown_gender = $unknown_gender + 1;
                            } else if ($newcust_row['gender'] == 1) {
                              
                                $male_gender = $male_gender + 1;
                            } else {
                             
                                $female_gender = $female_gender + 1;
                            }
                           
                                $today = new DateTime();
                                $birthdate = new DateTime($newcust_row['dob_year']."-".$newcust_row['dob_month']."-".$newcust_row['dob_day']." 09:48:00");
                                $interval = $today->diff($birthdate);
                               $age = $interval->format('%y');
                              
                                $coustomer_counter_1 = $coustomer_counter_1 + 1;
                             
                                $arr_exsting_cust_reft[$newcust_row['customer_id']] = $newcust_row['gender'];
                                if ($newcust_row['dob_year'] != "") {
                                    array_push($arr_age, $age);
									array_push($agewisegender,$newcust_row['gender']);
                                }
                            }
                            $a_c = $a->RecordCount();
                            
                            $total_cust =  $coustomer_counter_2 + $coustomer_counter_1;
							 $new_cust =  $coustomer_counter_2  ;
                       
                            
							/* transaction fess */
						
                        }
						/******** clculate campaign cost ***************/
						
						/*$sql = "select  SUM(referral_reward) ref_point from reward_user r  
						where  r.location_id=" . $_REQUEST['location_id'] . " and r.reward_date between '".$from_date."' and '".$to_date."' 
						and r.campaign_id in(" . $campaign_list . ") ";
                            
							$abc = $objDB->Conn->Execute($sql);*/
					$abc = $objDB->Conn->Execute("select  SUM(referral_reward) ref_point from reward_user r  
						where  r.location_id=? and r.reward_date between ? and ? 
						and r.campaign_id in(?) ",array($_REQUEST['location_id'],$from_date,$to_date,$campaign_list));

                            while ($rowabc = $abc->FetchRow()) {
                                $total_cost_sharing = $total_cost_sharing + ($rowabc['ref_point'] * $B4 ) ;
                            }
							/* transcation fess */
							$redeem_date_sql = " and cr.redeem_date between '". $from_date ."' and '".$to_date."'" ;
							
							$rs_t_f = $objDB->Conn->Execute("SELECT  sum(transaction_fees_price) as total_transaction_fees , c.redeem_rewards,count(*) no_of_records FROM `coupon_redeem` cr , coupon_codes cc inner join campaigns c on c.id = cc.customer_campaign_code where cr.coupon_id= cc.id  and cc.location_id = ? and cc.customer_campaign_code in (?) ".$redeem_date_sql." 
								group by cc.customer_campaign_code",array($_REQUEST['location_id'],$campaign_list));

							while($row_redeemed_data = $rs_t_f->FetchRow())
							{
								
									$total_transaction_point_ex = $total_transaction_point_ex + $row_redeemed_data['total_transaction_fees'];
									$total_cost_redeem =  $total_cost_redeem + ( $row_redeemed_data['no_of_records'] * $row_redeemed_data['redeem_rewards']);
							
							}
							
							$total_cost_redeem = $total_cost_redeem * $B4;
							
							$total_cost = $total_transaction_point_ex + $total_cost + $total_cost_sharing+$total_cost_redeem;
						/******** calculate campaign cost **************/
						
                        // countoing customers for calculations
                    }
                } else {
                    $a_c = 0;
                    $total_cust = 0;
                    $new_cust = 0;
                    $total_cost = 0;
                }
				$new_arr =  array_count_values($all_users);//array_intersect_key($arr_exsting_cust_reft, $arr_new_cust_ref);
						foreach($new_arr  as $key=>$value)
						{
							if($value == 1){
								$c2 =  $c2 + 1;
							}
						}
						
                if ($RS_l->RecordCount() == 0) {
                    array_push($redeem_array, 0);
                    array_push($reserve_array, 0);
                    array_push($campaigns_array, 0);
                    array_push($cost_array, 0);
                    array_push($revenue_array, 0);
                    array_push($total_customers, 0);
                    array_push($new_customers, 0);
                } else {

                    array_push($redeem_array, $a_c);
                    array_push($reserve_array, $total_coupon_issued);
                    array_push($campaigns_array, $t_c);
                    array_push($cost_array, $total_cost);
                    array_push($revenue_array, round($t_v,2));
                    array_push($total_customers, $total_cust);
                    array_push($new_customers, $new_cust);
                }
            }
        }
    }

    $c_1 = $c_2 = $c_3 = $c_4 = $c_5 = $c_6 = 0;

    $total_cust = count($total_customer_age_arr);
    $arr_age_txt = array();
	 $agewise_gender_male['65 Or Above'] = 0;
$agewise_gender_male['55 to 64'] = 0;
$agewise_gender_male['45 to 54']= 0;
$agewise_gender_male['25 to 44']= 0;
 $agewise_gender_male['18 to 24']= 0;
$agewise_gender_male['17 Or Below'] = 0;

$agewise_gender_female['65 Or Above'] = 0;
$agewise_gender_female['55 to 64'] = 0;
$agewise_gender_female['45 to 54']= 0;
$agewise_gender_female['25 to 44']= 0;
 $agewise_gender_female['18 to 24']= 0;
$agewise_gender_female['17 Or Below'] = 0;

    for ($c = 0; $c < count($arr_age); $c++) {
        if ($arr_age[$c] >= 65) {
			if($agewisegender[$c] == 1)
			{
			
				$agewise_gender_male['65 Or Above']  =  $agewise_gender_male['65 Or Above'] +1;
			}
			else if($agewisegender[$c] == 2)
			{
				$agewise_gender_female['65 Or Above']  =  $agewise_gender_female['65 Or Above'] +1;
			}
			
            $c_6 = $c_6 + 1;
        } else if ($arr_age[$c] >= 55 && $arr_age[$c] <= 64) {
			if($agewisegender[$c] == 1)
			{
				$agewise_gender_male['55 to 64']  =  $agewise_gender_male['55 to 64'] +1;
			}
			else if($agewisegender[$c] == 2)
			{
				$agewise_gender_female['55 to 64']  =  $agewise_gender_female['55 to 64'] +1;
			}
            $c_5 = $c_5 + 1;
        } else if ($arr_age[$c] >= 45 && $arr_age[$c] <= 54) {
		if($agewisegender[$c] == 1)
			{
				$agewise_gender_male['45 to 54']  =  $agewise_gender_male['45 to 54'] +1;
			}
			else if($agewisegender[$c] == 2)
			{
				$agewise_gender_female['45 to 54']  =  $agewise_gender_female['45 to 54'] +1;
			}
            $c_4 = $c_4 + 1;
        } else if ($arr_age[$c] >= 25 && $arr_age[$c] <= 44) {
		if($agewisegender[$c] == 1)
			{
				$agewise_gender_male['25 to 44']  =  $agewise_gender_male['25 to 44'] +1;
			}
			else if($agewisegender[$c] == 2)
			{
				$agewise_gender_female['25 to 44']  =  $agewise_gender_female['25 to 44'] +1;
			}
            $c_3 = $c_3 + 1;
        } else if ($arr_age[$c] >= 18 && $arr_age[$c] <= 24) {
		if($agewisegender[$c] == 1)
			{
				$agewise_gender_male['18 to 24']  =  $agewise_gender_male['18 to 24'] +1;
			}
			else if($agewisegender[$c] == 2)
			{
				$agewise_gender_female['18 to 24']  =  $agewise_gender_female['18 to 24'] +1;
			}
            $c_2 = $c_2 + 1;
        } else if ($arr_age[$c] <= 17) {
		if($agewisegender[$c] == 1)
			{
				$agewise_gender_male['17 Or Below']  =  $agewise_gender_male['17 Or Below'] +1;
			}
			else if($agewisegender[$c] == 2)
			{
				$agewise_gender_female['17 Or Below']  =  $agewise_gender_female['17 Or Below'] +1;
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
	
					
//echo "<br />======".$c2."=====";
    $new_arr = array();

    $json_str = json_encode($arr_age_txt);
    $json_array = array();

    $json_array['redeem_records'] = "";
    $json_array['reserve_records'] = "";
    $json_array['campaigns_records'] = "";
    $json_array['total_cost'] = "";
    $json_array['total_revenue'] = "";
    $json_array['total_customer'] = "";
    $json_array['new_customer'] = "";
    $json_array['gender_str'] = "";
    $json_array['age_str'] = "";
	
    if ($total_cust != 0) {

        $male_per = ($male_gender * 100) / $total_cust;
        $unknowne_per = ($unknown_gender * 100) / $total_cust;
        $female_per = ($female_gender * 100 ) / $total_cust;
        $total_gender_str = round($male_per, 2) . "-" . round($female_per, 2) . "-" . round($unknowne_per, 2);
        $total_age_arr = round((($c_1 * 100) / $total_cust)) . "-" . round((($c_2 * 100) / $total_cust)) . "-" . round((($c_3 * 100) / $total_cust)) . "-" . round((($c_4 * 100) / $total_cust)) . "-" . round((($c_5 * 100) / $total_cust)) . "-" . round((($c_6 * 100) / $total_cust));
		
		$agewise_mail_array =  round((($agewise_gender_male['17 Or Below'] * 100) / $total_cust))  . "-" .round((( $agewise_gender_male['18 to 24'] * 100) / $total_cust)) . "-" .round((($agewise_gender_male['25 to 44'] * 100) / $total_cust)) . "-" .round((($agewise_gender_male['45 to 54'] * 100) / $total_cust)) . "-" .round((($agewise_gender_male['55 to 64']* 100) / $total_cust)). "-" .round((($agewise_gender_male['65 Or Above'] * 100) / $total_cust));
		$agewise_femail_array = round((($agewise_gender_female['17 Or Below'] * 100) / $total_cust))  . "-" .round((( $agewise_gender_female['18 to 24'] * 100) / $total_cust)) . "-" .round((($agewise_gender_female['25 to 44'] * 100) / $total_cust)) . "-" .round((($agewise_gender_female['45 to 54'] * 100) / $total_cust)) . "-" .round((($agewise_gender_female['55 to 64']* 100) / $total_cust)). "-" .round((($agewise_gender_female['65 Or Above'] * 100) / $total_cust));
	    $arr = array_unique($all_campaign_list_arr);
		$json_array['total_campaigns'] = count($arr);
		$json_array['redeem_records'] = $redeem_array;
        $json_array['reserve_records'] = $reserve_array;
        $json_array['campaigns_records'] = $campaigns_array;
        $json_array['total_cost'] = $cost_array;
        $json_array['total_revenue'] = $revenue_array;
        $json_array['total_customer'] = $total_customers;
        $json_array['new_customer'] = $new_customers;
        $json_array['gender_str'] = $total_gender_str;
		$json_array['agewise_male'] =$agewise_mail_array;
		$json_array['agewise_female'] =$agewise_femail_array;
        $json_array['age_str'] = $total_age_arr;
        $json_array['status'] = 'true';
		$json_array['tot_new_customers'] =$cnt_new;
        $json = json_encode($json_array);
         echo $json;
        exit();
    } else {
	$json_array['total_campaigns'] = 0;
        $json_array['status'] = 'false';
		$json_array['total_camaigns'] = 0;
        $json = json_encode($json_array);
        echo $json;
        exit();
    }
}
?>
