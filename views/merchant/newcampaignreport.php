<?php

//error_reporting(E_ALL);
//ini_set("display_errors","1");

$array1 = array();
$array1['id'] = $_REQUEST['campaign_id'];
$RS_campaign = $objDB->Show("campaigns",$array1);
//echo $RS_campaign->fields['title'];
//exit();

$array2 = array();
$array2['campaign_id'] = $_REQUEST['campaign_id'];
$RS_location_list = $objDB->Show("campaign_location",$array2);

$options_loc='<option value="0" selected="selected" >All Participating Locations</option>';

while($location_detail = $RS_location_list->FetchRow())
{
	$array3 = array();
	$array3['id'] = $location_detail['location_id'] ;
	$RS_location = $objDB->Show("locations",$array3);
                                                            
	$loc_add_str = $RS_location->fields['address'].','.$RS_location->fields['city'].','.$RS_location->fields['state'].','.$RS_location->fields['zip'];
	if(strlen($loc_add_str)>28)
	{
		$loc_add_str = substr($loc_add_str,0,28)."...";
	}
	$options_loc .= '<option value="'.$location_detail['location_id'].'" >'.$loc_add_str.'</option>';			
}

if(isset($_REQUEST['campaign_id']) && !isset($_REQUEST['location_id']))
{

	$cid = $_REQUEST['campaign_id'];

	//========

	$RS_male = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =?", array($cid));
	$activation_code_issued_male = $RS_male->RecordCount();

	//--------

	$RS_female = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =?", array($cid));
	$activation_code_issued_female = $RS_female->RecordCount();

	//========

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

	$RS_reserve_male = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
							customer_campaign_code=? and c.gender=1 and  ( " . $loc_str . " )  ", array($cid));
							
	$total_reserved_by_new_cust_male = array();
	$total_reserved_by_exist_cust_male = array();

	while($Row1 = $RS_reserve_male->FetchRow())
	{
		$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

		if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
		{
			if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_male))
			{
				$total_reserved_by_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
			}
		}
		else 
		{
			array_push($total_reserved_by_exist_cust_male, $Row1['customer_id']);
		}
	}
					
	$reserved_by_exsting_customer_male = count($total_reserved_by_exist_cust_male);
	$reserved_by_new_customer_male = count($total_reserved_by_new_cust_male);

	//-------

	$RS_reserve_female = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
							customer_campaign_code=? and c.gender=2 and  ( " . $loc_str . " )  ", array($cid));
							
	$total_reserved_by_new_cust_female = array();
	$total_reserved_by_exist_cust_female = array();

	while($Row1 = $RS_reserve_female->FetchRow())
	{
		$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

		if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
		{
			if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_female))
			{
				$total_reserved_by_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
			}
		}
		else 
		{
			array_push($total_reserved_by_exist_cust_female, $Row1['customer_id']);
		}
	}
					
	$reserved_by_exsting_customer_female = count($total_reserved_by_exist_cust_female);
	$reserved_by_new_customer_female = count($total_reserved_by_new_cust_female);

	//========

	$arr_exsting_cust_male = array();
	$arr_new_cust_male = array();

	$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =?", array($cid));
	$remain_val_male = $RS_remain->fields['total'];

	$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
						FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=? ", array($cid));
	$redeem_val_male = $RS_redeem->RecordCount();
			
	$remain_val_male = $remain_val_male-$redeem_val_male;
	$total_redeem_point_male = 0;
	$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id =? and referred_customer_id<>? ", array($cid, 0));
	$total_referral_point_male = $RS_ref->fields['total'];
	
	$totla_redeem_point_by_new_cust_male = 0;
	$totla_redeem_point_by_exsting_cust_male = 0;
	$total_revenue_cost_by_new_cust_male = 0;
	$total_revenue_cost_by_exist_cust_male = 0;
	
	$male_gender_male =0;
	$female_gender_male =0;
	$arr_age_male = array();
	$agewisegender_male = array();
	while($Row1 = $RS_redeem->FetchRow())
	{
		if ($Row1['gender'] == "") 
		{
			$unknown_gender = $unknown_gender + 1;
		} 
		else if ($Row1['gender'] == 1) 
		{
			$male_gender = $male_gender + 1;
		} 
		else 
		{
			$female_gender = $female_gender + 1;
		}

		$today = new DateTime();
		$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
		$interval = $today->diff($birthdate);
		$age = $interval->format('%y');
		array_push($arr_age_male, $age);
		array_push($agewisegender_male, $Row1['gender']);

		

		$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? ) ", array($Row1['customer_id']));
		$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? ", array($cid, $Row1['customer_id'], 0));

		if($RS2->RecordCount()== 1)
		{
			if(!key_exists($Row1['customer_id'], $arr_new_cust_male))
			{
				$arr_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
			}
			$total_revenue_cost_by_new_cust_male = $total_revenue_cost_by_new_cust_male + $Row1['redeem_value'];
			$totla_redeem_point_by_new_cust_male = $totla_redeem_point_by_new_cust_male + $Rs3->fields['redeem_value'];
		}
		else if($RS2->RecordCount()>1) 
		{	
			array_push($arr_exsting_cust_male, $Row1['customer_id']);
			$totla_redeem_point_by_exsting_cust_male = $totla_redeem_point_by_exsting_cust_male + $Rs3->fields['earned_reward'];
			$total_revenue_cost_by_exist_cust_male = $total_revenue_cost_by_exist_cust_male + $Row1['redeem_value'];
		}
		$total_redeem_point_male = $total_redeem_point_male + $Row1['earned_reward'];
	}

	$redeemed_by_exsting_customer_male = count($arr_exsting_cust_male);
	$redeemed_by_new_customer_male = count($arr_new_cust_male);

	//--------

	$arr_exsting_cust_female = array();
	$arr_new_cust_female = array();

	$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =?", array($cid));
	$remain_val_female = $RS_remain->fields['total'];

	$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
						FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=? ", array($cid));
	$redeem_val_female = $RS_redeem->RecordCount();
			
	$remain_val_female = $remain_val_female-$redeem_val_female;
	$total_redeem_point_female = 0;
	$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id =? and referred_customer_id<>? ", array($cid, 0));
	$total_referral_point_female = $RS_ref->fields['total'];
	
	$totla_redeem_point_by_new_cust_female = 0;
	$totla_redeem_point_by_exsting_cust_female = 0;
	$total_revenue_cost_by_new_cust_female = 0;
	$total_revenue_cost_by_exist_cust_female = 0;
	
	$male_gender =0;
	$female_gender =0;
	$arr_age_female = array();
	$agewisegender_female = array();
	while($Row1 = $RS_redeem->FetchRow())
	{
		if ($Row1['gender'] == "") 
		{
			$unknown_gender = $unknown_gender + 1;
		} 
		else if ($Row1['gender'] == 1) 
		{
			$male_gender = $male_gender + 1;
		} 
		else 
		{
			$female_gender = $female_gender + 1;
		}

		$today = new DateTime();
		$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
		$interval = $today->diff($birthdate);
		$age = $interval->format('%y');
		array_push($arr_age_female, $age);
		array_push($agewisegender_female, $Row1['gender']);

		

		$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? ) ", array($Row1['customer_id']));
		$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? ", array($cid, $Row1['customer_id'], 0));

		if($RS2->RecordCount()== 1)
		{
			if(!key_exists($Row1['customer_id'], $arr_new_cust_female))
			{
				$arr_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
			}
			$total_revenue_cost_by_new_cust_female = $total_revenue_cost_by_new_cust_female + $Row1['redeem_value'];
			$totla_redeem_point_by_new_cust_female = $totla_redeem_point_by_new_cust_female + $Rs3->fields['redeem_value'];
		}
		else if($RS2->RecordCount()>1) 
		{	
			array_push($arr_exsting_cust_female, $Row1['customer_id']);
			$totla_redeem_point_by_exsting_cust_female = $totla_redeem_point_by_exsting_cust_female + $Rs3->fields['earned_reward'];
			$total_revenue_cost_by_exist_cust_female = $total_revenue_cost_by_exist_cust_female + $Row1['redeem_value'];
		}
		$total_redeem_point_female = $total_redeem_point_female + $Row1['earned_reward'];
	}

	$redeemed_by_exsting_customer_female = count($arr_exsting_cust_female);
	$redeemed_by_new_customer_female = count($arr_new_cust_female);


	//========

	$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id=? and referred_customer_id=?  ", array($cid, 0));
	$activation_code_not_redeemed_male = $activation_code_issued_male - ($RS_tot_redeem_coupon->RecordCount());

	//--------

	$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id=? and referred_customer_id=?  ", array($cid, 0));
	$activation_code_not_redeemed_female = $activation_code_issued_female - ($RS_tot_redeem_coupon->RecordCount());

	//========
	
	$arr = file(WEB_PATH.'/merchant/process.php?btnGetCampaignDetail=yes&mer_id='.$_SESSION['merchant_id']."&id=".$cid);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records = $json->total_records;
	$records_array = $json->records;
	
	$referral_rewards = 0;
	$redeem_rewards = 0;
	
	if($total_records>0)
	{
		foreach($records_array as $RS)
		{
			$referral_rewards = $RS->referral_rewards;
			$redeem_rewards = $RS->redeem_rewards;
		}
	}
	$arr = file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records = $json->total_records;
	$records_array = $json->records;
	if($total_records>0)
	{
		foreach($records_array as $Row)
		{
			$price = $Row->price;
			$point_ = $Row->points;
			$p = (1*$price)/$point_;
		}
	}
	$B4 = (1*$price)/$point_;
	$B5 = $referral_rewards * $B4;
	$B6 = $redeem_rewards * $B4;
	$B7 = $referral_rewards;
	$B8 = $redeem_rewards;
        
	$arr_new_cust = array();
	
	$RS_ref_cnt_male = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id = ? and referred_customer_id<>?", array($cid, 0));
	$totla_redeem_point_by_exsting_cust_ref_male = 0;
	$totla_redeem_point_by_new_cust_ref_male = 0;
	while($Row1 = $RS_ref_cnt_male->FetchRow())
	{
		$RS2 = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and customer_id =? and referred_customer_id<>?", array($cid, $Row1['customer_id'], 0));

		if($RS2->RecordCount()== 1)
		{
			if(!key_exists($Row1['customer_id'], $arr_new_cust))
			{
			$arr_new_cust_ref[$Row1['customer_id']] = $RS2->RecordCount();
			}
			$totla_redeem_point_by_new_cust_ref_male = $totla_redeem_point_by_new_cust_ref_male + $Row1['referral_reward'];
		}
		else if($RS2->RecordCount()>1) 
		{
			$arr_exsting_cus_reft[$Row1['customer_id']] = $RS2->RecordCount();
			$totla_redeem_point_by_exsting_cust_ref_male = $totla_redeem_point_by_exsting_cust_ref_male+$Row1['referral_reward'];
		}
	}
        
	$tot_revenue_cost_existing_male = $totla_redeem_point_by_exsting_cust_ref_male + $totla_redeem_point_by_exsting_cust_male;
    $tot_revenue_cost_new_male = $totla_redeem_point_by_new_cust_male+$totla_redeem_point_by_new_cust_ref_male;
    $arr_exsting_cust_unique = array_unique($arr_exsting_cust_male);
    
	foreach($arr_exsting_cust_unique as $key => $value)
	{
		//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
		$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc,customer_user cu where cu.id=cc.customer_id and cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
		$total_transaction_point_ex_male = $total_transaction_point_ex_male + $rs_t_f->fields['total_transaction_fees'];
		$total_transaction_only_points_ex_male = $total_transaction_only_points_ex_male + $rs_t_f->fields['total_transaction_points'];
	}
	
	$arr_new_cust = array_keys($arr_new_cust);
	foreach($arr_new_cust as $key => $value)
	{
		//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
		$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc ,customer_user cu where cu.id=cc.customer_id and cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
		$total_transaction_point_new_male = $total_transaction_point_new_male + $rs_t_f->fields['total_transaction_fees'];
		$total_transaction_only_points_new_male = $total_transaction_only_points_new_male + $rs_t_f->fields['total_transaction_points'];
	}
	$C14 = $activation_code_issued_male; 
	$C15 = count($arr_exsting_cust_male);
	$C16 = count($arr_new_cust);
	$C17 = $C15 + $C16;
	$C18 = $C14 - ($RS_tot_redeem_coupon->RecordCount());

	$RS_2_share = $objDB->Conn->Execute("SELECT * FROM reward_user ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and referred_customer_id <> ? and referral_reward<>? and  campaign_id =?", array(0, 0, $cid));

	$total_share_count = $RS_2_share->RecordCount();

	$total_cust = $C17;
	if($total_cust != 0) 
	{
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
		for ($c = 0;$c < count($arr_age);$c++) 
		{
			if ($arr_age[$c] >= 65) 
			{
				if($agewisegender[$c] == 1)
				{

				$agewise_gender_male['65 Or Above'] = $agewise_gender_male['65 Or Above'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['65 Or Above'] = $agewise_gender_female['65 Or Above'] +1;
				}

				$c_6 = $c_6 + 1;
			} 
			else if ($arr_age[$c] >= 55 && $arr_age[$c] <= 64) 
			{
				if($agewisegender[$c] == 1)
				{
				$agewise_gender_male['55 to 64'] = $agewise_gender_male['55 to 64'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['55 to 64'] = $agewise_gender_female['55 to 64'] +1;
				}
				$c_5 = $c_5 + 1;
			} 
			else if ($arr_age[$c] >= 45 && $arr_age[$c] <= 54) 
			{
				if($agewisegender[$c] == 1)
				{
				$agewise_gender_male['45 to 54'] = $agewise_gender_male['45 to 54'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['45 to 54'] = $agewise_gender_female['45 to 54'] +1;
				}
				$c_4 = $c_4 + 1;
			} 
			else if ($arr_age[$c] >= 25 && $arr_age[$c] <= 44) 
			{
				if($agewisegender[$c] == 1)
				{
				$agewise_gender_male['25 to 44'] = $agewise_gender_male['25 to 44'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['25 to 44'] = $agewise_gender_female['25 to 44'] +1;
				}
				$c_3 = $c_3 + 1;
			} 
			else if ($arr_age[$c] >= 18 && $arr_age[$c] <= 24) 
			{
				if($agewisegender[$c] == 1)
				{
				$agewise_gender_male['18 to 24'] = $agewise_gender_male['18 to 24'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['18 to 24'] = $agewise_gender_female['18 to 24'] +1;
				}
				$c_2 = $c_2 + 1;
			} 
			else if ($arr_age[$c] <= 17) 
			{
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
	

		if ($c_1 > 0) 
		{
			$arr_age_txt['17 Or Below'] = $c_1;
		}
		if ($c_2 > 0) 
		{
			$arr_age_txt['18 to 24'] = $c_2;
		}
		if ($c_3 > 0) 
		{
			$arr_age_txt['25 to 44'] = $c_3;
		}
		if ($c_4 > 0) 
		{
			$arr_age_txt['45 to 54'] = $c_4;
		}
		if ($c_5 > 0) 
		{
			$arr_age_txt['55 to 64'] = $c_5;
		}
		if ($c_6 > 0) 
		{
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

	}
	$total_transaction_point_male = $total_transaction_only_points_ex_male + $total_transaction_only_points_new_male;
	$total_transaction_fee_male = $total_transaction_point_ex_male + $total_transaction_point_new_male;
	
	$C22 = $total_share_count * $B7;
	$C23 = $C15 * $B8;
	$C24 = $C16 * $B8;
	$C25 = $C23 + $C24;
	$C26 = $C25 + $C22;
	$C29 = $C22 * $B4;
	$C30 = ($C24 * $B4); //+  $total_transaction_point_new;
	$C31 = ($C23 * $B4); //+  $total_transaction_point_ex;
	$C32 = $C30 + $C31;
	$C28 = $C29 + $C32 + $total_transaction_fee_male;
	
	//echo "<br/>".$C24."==".$B4."==".$total_transaction_point_new."==new<br/>";
	//echo "<br/>".$C23."==".$B4."==".$total_transaction_point_ex."==ex<br/>";
	
	$C34 = $total_revenue_cost_by_new_cust_male + $total_revenue_cost_by_exist_cust_male;
	$C35 = $total_revenue_cost_by_new_cust_male;
	$C36 = $total_revenue_cost_by_exist_cust_male;

	$C28_male=$C28;
	$C34_male=$C34;
	
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
	
	$total_point_spent_male = $C26;
	$campaign_referral_male = $C22;
	$campaign_redeemption_male = $C25;
	$application_fee_male = $total_transaction_fee_male;
	
	//--------
	
	$arr = file(WEB_PATH.'/merchant/process.php?btnGetCampaignDetail=yes&mer_id='.$_SESSION['merchant_id']."&id=".$cid);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records = $json->total_records;
	$records_array = $json->records;
	
	$referral_rewards = 0;
	$redeem_rewards = 0;
	
	if($total_records>0)
	{
		foreach($records_array as $RS)
		{
			$referral_rewards = $RS->referral_rewards;
			$redeem_rewards = $RS->redeem_rewards;
		}
	}
	$arr = file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records = $json->total_records;
	$records_array = $json->records;
	if($total_records>0)
	{
		foreach($records_array as $Row)
		{
			$price = $Row->price;
			$point_ = $Row->points;
			$p = (1*$price)/$point_;
		}
	}
	$B4 = (1*$price)/$point_;
	$B5 = $referral_rewards * $B4;
	$B6 = $redeem_rewards * $B4;
	$B7 = $referral_rewards;
	$B8 = $redeem_rewards;
        
	$arr_new_cust = array();
	
	$RS_ref_cnt_female = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id = ? and referred_customer_id<>?", array($cid, 0));
	$totla_redeem_point_by_exsting_cust_ref_female = 0;
	$totla_redeem_point_by_new_cust_ref_female = 0;
	while($Row1 = $RS_ref_cnt_male->FetchRow())
	{
		$RS2 = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and customer_id =? and referred_customer_id<>?", array($cid, $Row1['customer_id'], 0));

		if($RS2->RecordCount()== 1)
		{
			if(!key_exists($Row1['customer_id'], $arr_new_cust))
			{
			$arr_new_cust_ref[$Row1['customer_id']] = $RS2->RecordCount();
			}
			$totla_redeem_point_by_new_cust_ref_female = $totla_redeem_point_by_new_cust_ref_female + $Row1['referral_reward'];
		}
		else if($RS2->RecordCount()>1) 
		{
			$arr_exsting_cus_reft[$Row1['customer_id']] = $RS2->RecordCount();
			$totla_redeem_point_by_exsting_cust_ref_female = $totla_redeem_point_by_exsting_cust_ref_female+$Row1['referral_reward'];
		}
	}
        
	$tot_revenue_cost_existing_female = $totla_redeem_point_by_exsting_cust_ref_female + $totla_redeem_point_by_exsting_cust_female;
    $tot_revenue_cost_new_female = $totla_redeem_point_by_new_cust_female+$totla_redeem_point_by_new_cust_ref_female;
    $arr_exsting_cust_unique = array_unique($arr_exsting_cust_female);
    
	foreach($arr_exsting_cust_unique as $key => $value)
	{
		//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
		$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc ,customer_user cu where cu.id=cc.customer_id and cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
		$total_transaction_point_ex_female = $total_transaction_point_ex_female + $rs_t_f->fields['total_transaction_fees'];
		$total_transaction_only_points_ex_female = $total_transaction_only_points_ex_female + $rs_t_f->fields['total_transaction_points'];
	}
	
	$arr_new_cust = array_keys($arr_new_cust);
	foreach($arr_new_cust as $key => $value)
	{
		//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
		$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc ,customer_user cu where cu.id=cc.customer_id and cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
		$total_transaction_point_new_female = $total_transaction_point_new_female + $rs_t_f->fields['total_transaction_fees'];
		$total_transaction_only_points_new_female = $total_transaction_only_points_new_female + $rs_t_f->fields['total_transaction_points'];
	}
	$C14 = $activation_code_issued_female;
	$C15 = count($arr_exsting_cust_female);
	$C16 = count($arr_new_cust);
	$C17 = $C15 + $C16;
	$C18 = $C14 - ($RS_tot_redeem_coupon->RecordCount());

	$RS_2_share = $objDB->Conn->Execute("SELECT * FROM reward_user ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and referred_customer_id <> ? and referral_reward<>? and  campaign_id =?", array(0, 0, $cid));

	$total_share_count = $RS_2_share->RecordCount();

	$total_cust = $C17;
	if($total_cust != 0) 
	{
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
		for ($c = 0;$c < count($arr_age);$c++) 
		{
			if ($arr_age[$c] >= 65) 
			{
				if($agewisegender[$c] == 1)
				{

				$agewise_gender_male['65 Or Above'] = $agewise_gender_male['65 Or Above'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['65 Or Above'] = $agewise_gender_female['65 Or Above'] +1;
				}

				$c_6 = $c_6 + 1;
			} 
			else if ($arr_age[$c] >= 55 && $arr_age[$c] <= 64) 
			{
				if($agewisegender[$c] == 1)
				{
				$agewise_gender_male['55 to 64'] = $agewise_gender_male['55 to 64'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['55 to 64'] = $agewise_gender_female['55 to 64'] +1;
				}
				$c_5 = $c_5 + 1;
			} 
			else if ($arr_age[$c] >= 45 && $arr_age[$c] <= 54) 
			{
				if($agewisegender[$c] == 1)
				{
				$agewise_gender_male['45 to 54'] = $agewise_gender_male['45 to 54'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['45 to 54'] = $agewise_gender_female['45 to 54'] +1;
				}
				$c_4 = $c_4 + 1;
			} 
			else if ($arr_age[$c] >= 25 && $arr_age[$c] <= 44) 
			{
				if($agewisegender[$c] == 1)
				{
				$agewise_gender_male['25 to 44'] = $agewise_gender_male['25 to 44'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['25 to 44'] = $agewise_gender_female['25 to 44'] +1;
				}
				$c_3 = $c_3 + 1;
			} 
			else if ($arr_age[$c] >= 18 && $arr_age[$c] <= 24) 
			{
				if($agewisegender[$c] == 1)
				{
				$agewise_gender_male['18 to 24'] = $agewise_gender_male['18 to 24'] +1;
				}
				else if($agewisegender[$c] == 2)
				{
				$agewise_gender_female['18 to 24'] = $agewise_gender_female['18 to 24'] +1;
				}
				$c_2 = $c_2 + 1;
			} 
			else if ($arr_age[$c] <= 17) 
			{
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
	

		if ($c_1 > 0) 
		{
			$arr_age_txt['17 Or Below'] = $c_1;
		}
		if ($c_2 > 0) 
		{
			$arr_age_txt['18 to 24'] = $c_2;
		}
		if ($c_3 > 0) 
		{
			$arr_age_txt['25 to 44'] = $c_3;
		}
		if ($c_4 > 0) 
		{
			$arr_age_txt['45 to 54'] = $c_4;
		}
		if ($c_5 > 0) 
		{
			$arr_age_txt['55 to 64'] = $c_5;
		}
		if ($c_6 > 0) 
		{
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

	}
	$total_transaction_point_female = $total_transaction_only_points_ex_female + $total_transaction_only_points_new_female;
	$total_transaction_fee_female = $total_transaction_point_ex_female + $total_transaction_point_new_female;
	
	$C22 = $total_share_count * $B7;
	$C23 = $C15 * $B8;
	$C24 = $C16 * $B8;
	$C25 = $C23 + $C24;
	$C26 = $C25 + $C22;
	$C29 = $C22 * $B4;
	$C30 = ($C24 * $B4); //+  $total_transaction_point_new;
	$C31 = ($C23 * $B4); //+  $total_transaction_point_ex;
	$C32 = $C30 + $C31;
	$C28 = $C29 + $C32 + $total_transaction_fee_female;
	
	//echo "<br/>".$C24."==".$B4."==".$total_transaction_point_new."==new<br/>";
	//echo "<br/>".$C23."==".$B4."==".$total_transaction_point_ex."==ex<br/>";
	
	$C34 = $total_revenue_cost_by_new_cust_female + $total_revenue_cost_by_exist_cust_female;
	$C35 = $total_revenue_cost_by_new_cust_female;
	$C36 = $total_revenue_cost_by_exist_cust_female;

	$C28_female=$C28;
	$C34_female=$C34;
	
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
	
	//echo "campaign cost = ".$C28_male + $C28_female."<br/>";
	//echo "campaign revenue = ".$C34_male + $C34_female."<br/>";
	//exit();
	
	$total_point_spent_female = $C26;
	$campaign_referral_female = $C22;
	$campaign_redeemption_female = $C25;
	$application_fee_female = $total_transaction_fee_female;
	
	
	//========
	
	$RS_2_qrcode = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id where a.campaign_id=?", array($cid));
	$RS_2_qrcode_male = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and cu.gender=1", array($cid));
	$RS_2_qrcode_female = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and cu.gender=2", array($cid));
	
	//-------
	
	$RS_2_qrcodeun = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and a.is_unique=?", array($cid, 1));
	$RS_2_qrcodeun_male = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and a.is_unique=? and cu.gender=1", array($cid, 1));
	$RS_2_qrcodeun_female = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and a.is_unique=? and cu.gender=2", array($cid, 1));
	
	//========
	
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
    
    while($Row_domain = $RS_domains_data->FetchRow())
    {
		/* $sql_t = "select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
		  where campaign_id = ".$_REQUEST['id']." and d.id=". $Row_domain['id'];
		  $RS_t = $objDB->Conn->Execute($sql_t); */
		$RS_t = $objDB->Conn->Execute("select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
											where campaign_id =? and d.id=?", array($cid, $Row_domain['id']));

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
		$RS_t = $objDB->Conn->Execute("select count(*) as total ,  p.campaign_id , p.location_id , d.domain from pageview_counter p inner join share_domain d on d.id= p.pageview_domain where campaign_id =? and d.id=?", array($cid, $Row_domain['id']));

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
    
    $RS_qrcodes_view = $objDB->Conn->Execute("select * from scan_qrcode where campaign_id = ?", array($cid));

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

	//========
    
	/*
	echo "activation_code_issued_male = ".$activation_code_issued_male."<br/>";
	echo "activation_code_issued_female = ".$activation_code_issued_female."<br/><br/>";

	echo "reserved_by_exsting_customer_male = ".$reserved_by_exsting_customer_male."<br/>";
	echo "reserved_by_exsting_customer_female = ".$reserved_by_exsting_customer_female."<br/><br/>";

	echo "reserved_by_new_customer_male = ".$reserved_by_new_customer_male."<br/>";
	echo "reserved_by_new_customer_female = ".$reserved_by_new_customer_female."<br/><br/>";


	echo "redeemed_by_exsting_customer_male = ".$redeemed_by_exsting_customer_male."<br/>";
	echo "redeemed_by_exsting_customer_female = ".$redeemed_by_exsting_customer_female."<br/><br/>";

	echo "redeemed_by_new_customer_male = ".$redeemed_by_new_customer_male."<br/>";
	echo "redeemed_by_new_customer_female = ".$redeemed_by_new_customer_female."<br/><br/>";

	echo "activation_code_not_redeemed_male = ".$activation_code_not_redeemed_male."<br/>";
	echo "activation_code_not_redeemed_female = ".$activation_code_not_redeemed_female."<br/><br/>";
	
	echo "total_point_spent_male = ".$total_point_spent_male."<br/>";
	echo "total_point_spent_female = ".$total_point_spent_female."<br/><br/>";
	echo "campaign_referral_male = ".$campaign_referral_male."<br/>";
	echo "campaign_referral_female = ".$campaign_referral_female."<br/><br/>";
	echo "campaign_redeemption_male = ".$campaign_redeemption_male."<br/>";
	echo "campaign_redeemption_female = ".$campaign_redeemption_female."<br/><br/>";
	echo "application_fee_male = ".$application_fee_male."<br/>";
	echo "application_fee_female = ".$application_fee_female."<br/><br/>";
	
	*/
	
	$json_array=array();
	$json_array['status']='true';
	
	$json_array['options_loc']=$options_loc;
	
	$json_array['campaign_title'] = $RS_campaign->fields['title'];
	
	$json_array['total_campaign_cost']=$C28_male + $C28_female;
	$json_array['total_campaign_revenue']=$C34_male + $C34_female;
	
	$json_array['activation_code_issued_male']=$activation_code_issued_male;
	$json_array['activation_code_issued_female']=$activation_code_issued_female;
	$json_array['reserved_by_exsting_customer_male']=$reserved_by_exsting_customer_male;
	$json_array['reserved_by_exsting_customer_female']=$reserved_by_exsting_customer_female;
	$json_array['reserved_by_new_customer_male']=$reserved_by_new_customer_male;
	$json_array['reserved_by_new_customer_female']=$reserved_by_new_customer_female;
	$json_array['redeemed_by_exsting_customer_male']=$redeemed_by_exsting_customer_male;
	$json_array['redeemed_by_exsting_customer_female']=$redeemed_by_exsting_customer_female;
	$json_array['redeemed_by_new_customer_male']=$redeemed_by_new_customer_male;
	$json_array['redeemed_by_new_customer_female']=$redeemed_by_new_customer_female;
	$json_array['activation_code_not_redeemed_male']=$activation_code_not_redeemed_male;
	$json_array['activation_code_not_redeemed_female']=$activation_code_not_redeemed_female;
	
	$json_array['total_point_spent_male']=$total_point_spent_male;
	$json_array['total_point_spent_female']=$total_point_spent_female;
	$json_array['campaign_referral_male']=$campaign_referral_male;
	$json_array['campaign_referral_female']=$campaign_referral_female;
	$json_array['campaign_redeemption_male']=$campaign_redeemption_male;
	$json_array['campaign_redeemption_female']=$campaign_redeemption_female;
	$json_array['application_fee_male']=$application_fee_male;
	$json_array['application_fee_female']=$application_fee_female;
	
	$json_array['total_scan']=$RS_2_qrcode->RecordCount();
	$json_array['total_scan_male']=$RS_2_qrcode_male->RecordCount();
	$json_array['total_scan_female']=$RS_2_qrcode_female->RecordCount();
	
	$json_array['unique_scan']=$RS_2_qrcodeun->RecordCount();
	$json_array['unique_scan_male']=$RS_2_qrcodeun_male->RecordCount();
	$json_array['unique_scan_female']=$RS_2_qrcodeun_female->RecordCount();
	
	$json_array['view_email']=$only_values[1];
	$json_array['view_facebook']=$only_values[3];
	$json_array['view_twitter']=$only_values[5];
	$json_array['view_googleplus']=$only_values[7];
	$json_array['view_other']=$only_values[9];
	
	$json_array['share_email']=$only_values[0];
	$json_array['share_facebook']=$only_values[2];
	$json_array['share_twitter']=$only_values[4];
	$json_array['share_googleplus']=$only_values[6];
	$json_array['share_other']=$only_values[8];
	
	$json = json_encode($json_array);
	echo $json;
	exit();

}
if(isset($_REQUEST['activation']))
{
	if(isset($_REQUEST['location_id']) && $_REQUEST['location_id']!=0)
	{	
		// location wise

		$activation_code_issued_male = 0;
		$activation_code_issued_female =  0;

		$reserved_by_exsting_customer_male = 0; 
		$reserved_by_exsting_customer_female =  0;

		$reserved_by_new_customer_male =  0;
		$reserved_by_new_customer_female =  0;

		$redeemed_by_exsting_customer_male =  0;
		$redeemed_by_exsting_customer_female =  0;

		$redeemed_by_new_customer_male =  0;
		$redeemed_by_new_customer_female =  0;

		$activation_code_not_redeemed_male =  0;
		$activation_code_not_redeemed_female =  0;

		//$array= array(70,71,80,197,199,258,259);
		$array= array($_REQUEST['location_id']);
		$cid = $_REQUEST['campaign_id'];
		$lid = $_REQUEST['location_id'];
		foreach($array as $val)
		{
			/*
			echo "-------------------------------------";
			echo "location : ".$val. " activation code metrics";
			echo "-------------------------------------"."<br/>";
			*/
			$lid = $val;
			
			//========
			
			$RS1 = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =? and location_id=?", array($cid, $lid));
			$activation_code_issued_male = $RS1->RecordCount();
			
			//-------
			
			$RS1 = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =? and location_id=?", array($cid, $lid));
			$activation_code_issued_female = $RS1->RecordCount();

			//========
			
			$loc_str = " location_id=".$lid;
				   
			$RS_reserve_male = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
									customer_campaign_code=? and c.gender=1 and  ( " . $loc_str . " )  ", array($cid));
									
			$total_reserved_by_new_cust_male = array();
			$total_reserved_by_exist_cust_male = array();
			while($Row1 = $RS_reserve_male->FetchRow())
			{
				$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

				if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
				{
					if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_male))
					{
						$total_reserved_by_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
					}
				}
				else 
				{
					array_push($total_reserved_by_exist_cust_male, $Row1['customer_id']);
				}
			}
							
			$reserved_by_exsting_customer_male = count($total_reserved_by_exist_cust_male);
			$reserved_by_new_customer_male = count($total_reserved_by_new_cust_male);
			
			//-------
			
			$loc_str = " location_id=".$lid;
				   
			$RS_reserve_female = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
									customer_campaign_code=? and c.gender=2 and  ( " . $loc_str . " )  ", array($cid));
									
			$total_reserved_by_new_cust_female = array();
			$total_reserved_by_exist_cust_female = array();
			while($Row1 = $RS_reserve_female->FetchRow())
			{
				$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

				if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
				{
					if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_female))
					{
						$total_reserved_by_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
					}
				}
				else 
				{
					array_push($total_reserved_by_exist_cust_female, $Row1['customer_id']);
				}
			}
							
			$reserved_by_exsting_customer_female = count($total_reserved_by_exist_cust_female);
			$reserved_by_new_customer_female = count($total_reserved_by_new_cust_female);
			
			//========
			
			$arr_exsting_cust_male = array();
			$arr_new_cust_male = array();

			$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =? AND location_id=?", array($cid, $lid));
			$remain_val_male = $RS_remain->fields['total'];

			$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
								FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=?  and cc.location_id=?", array($cid, $lid));
			$redeem_val_male = $RS_redeem->RecordCount();
					
			$remain_val_male = $remain_val_male-$redeem_val_male;
			$total_redeem_point_male = 0;
			$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id = ? and referred_customer_id<>? and location_id=?", array($cid, 0, $lid));
			$total_referral_point_male = $RS_ref->fields['total'];
			
			$totla_redeem_point_by_new_cust_male = 0;
			$totla_redeem_point_by_exsting_cust_male = 0;
			$total_revenue_cost_by_new_cust_male = 0;
			$total_revenue_cost_by_exist_cust_male = 0;
			
			$male_gender =0;
			$female_gender =0;
			$arr_age_male = array();
			$agewisegender_male = array();
			while($Row1 = $RS_redeem->FetchRow())
			{
				if ($Row1['gender'] == "") 
				{
					$unknown_gender = $unknown_gender + 1;
				} 
				else if ($Row1['gender'] == 1) 
				{
					$male_gender = $male_gender + 1;
				} 
				else 
				{
					$female_gender = $female_gender + 1;
				}

				$today = new DateTime();
				$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
				$interval = $today->diff($birthdate);
				$age = $interval->format('%y');
				array_push($arr_age_male, $age);
				array_push($agewisegender_male, $Row1['gender']);

				

				$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? and ( location_id =? )  ) ", array($Row1['customer_id'], $lid));
			$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? and location_id=?", array($cid, $Row1['customer_id'], 0, $lid));

				if($RS2->RecordCount()== 1)
				{
					if(!key_exists($Row1['customer_id'], $arr_new_cust))
					{
						$arr_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
					}
					$total_revenue_cost_by_new_cust_male = $total_revenue_cost_by_new_cust_male + $Row1['redeem_value'];
					$totla_redeem_point_by_new_cust_male = $totla_redeem_point_by_new_cust_male + $Rs3->fields['redeem_value'];
				}
				else if($RS2->RecordCount()>1) 
				{	
					array_push($arr_exsting_cust_male, $Row1['customer_id']);
					$totla_redeem_point_by_exsting_cust_male = $totla_redeem_point_by_exsting_cust_male + $Rs3->fields['earned_reward'];
					$total_revenue_cost_by_exist_cust_male = $total_revenue_cost_by_exist_cust_male + $Row1['redeem_value'];
				}
				$total_redeem_point_male = $total_redeem_point_male + $Row1['earned_reward'];
			}

			$redeemed_by_exsting_customer_male = count($arr_exsting_cust_male);
			$redeemed_by_new_customer_male = count($arr_new_cust_male);
			
			//------
			
			$arr_exsting_cust_female = array();
			$arr_new_cust_female = array();

			$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =? AND location_id=?", array($cid, $lid));
			$remain_val_female = $RS_remain->fields['total'];

			$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
								FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=?  and cc.location_id=?", array($cid, $lid));
			$redeem_val_female = $RS_redeem->RecordCount();
					
			$remain_val_female = $remain_val_female-$redeem_val_female;
			$total_redeem_point_female = 0;
			$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id = ? and referred_customer_id<>? and location_id=?", array($cid, 0, $lid));
			$total_referral_point_female = $RS_ref->fields['total'];
			
			$totla_redeem_point_by_new_cust_female = 0;
			$totla_redeem_point_by_exsting_cust_female = 0;
			$total_revenue_cost_by_new_cust_female = 0;
			$total_revenue_cost_by_exist_cust_female = 0;
			
			$male_gender =0;
			$female_gender =0;
			$arr_age_female = array();
			$agewisegender_female = array();
			
			while($Row1 = $RS_redeem->FetchRow())
			{
				if ($Row1['gender'] == "") 
				{
					$unknown_gender = $unknown_gender + 1;
				} 
				else if ($Row1['gender'] == 1) 
				{
					$male_gender = $male_gender + 1;
				} 
				else 
				{
					$female_gender = $female_gender + 1;
				}

				$today = new DateTime();
				$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
				$interval = $today->diff($birthdate);
				$age = $interval->format('%y');
				array_push($arr_age_female, $age);
				array_push($agewisegender_female, $Row1['gender']);

				

				$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? and ( location_id =? )  ) ", array($Row1['customer_id'], $lid));
			$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? and location_id=?", array($cid, $Row1['customer_id'], 0, $lid));

				if($RS2->RecordCount()== 1)
				{
					if(!key_exists($Row1['customer_id'], $arr_new_cust))
					{
						$arr_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
					}
					$total_revenue_cost_by_new_cust_female = $total_revenue_cost_by_new_cust_female + $Row1['redeem_value'];
					$totla_redeem_point_by_new_cust_female = $totla_redeem_point_by_new_cust_female + $Rs3->fields['redeem_value'];
				}
				else if($RS2->RecordCount()>1) 
				{	
					array_push($arr_exsting_cust_female, $Row1['customer_id']);
					$totla_redeem_point_by_exsting_cust_female = $totla_redeem_point_by_exsting_cust_female + $Rs3->fields['earned_reward'];
					$total_revenue_cost_by_exist_cust_female = $total_revenue_cost_by_exist_cust_female + $Row1['redeem_value'];
				}
				$total_redeem_point_female = $total_redeem_point_female + $Row1['earned_reward'];
			}

			$redeemed_by_exsting_customer_female = count($arr_exsting_cust_female);
			$redeemed_by_new_customer_female = count($arr_new_cust_female);
			
			//========

			$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id=? and referred_customer_id=?   and location_id=? ", array($cid, 0, $lid));
			$activation_code_not_redeemed_male = $activation_code_issued_male - ($RS_tot_redeem_coupon->RecordCount());

			//--------
			
			$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id=? and referred_customer_id=?   and location_id=? ", array($cid, 0, $lid));
			$activation_code_not_redeemed_female = $activation_code_issued_female - ($RS_tot_redeem_coupon->RecordCount());
			
			//========
			
		}
	}
	elseif(isset($_REQUEST['location_id']) && $_REQUEST['location_id']==0)
	{

		$cid = $_REQUEST['campaign_id'];

		//========

		$RS_male = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =?", array($cid));
		$activation_code_issued_male = $RS_male->RecordCount();

		//--------

		$RS_female = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =?", array($cid));
		$activation_code_issued_female = $RS_female->RecordCount();

		//========

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

		$RS_reserve_male = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
								customer_campaign_code=? and c.gender=1 and  ( " . $loc_str . " )  ", array($cid));
								
		$total_reserved_by_new_cust_male = array();
		$total_reserved_by_exist_cust_male = array();

		while($Row1 = $RS_reserve_male->FetchRow())
		{
			$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

			if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
			{
				if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_male))
				{
					$total_reserved_by_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
				}
			}
			else 
			{
				array_push($total_reserved_by_exist_cust_male, $Row1['customer_id']);
			}
		}
						
		$reserved_by_exsting_customer_male = count($total_reserved_by_exist_cust_male);
		$reserved_by_new_customer_male = count($total_reserved_by_new_cust_male);

		//-------

		$RS_reserve_female = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
								customer_campaign_code=? and c.gender=2 and  ( " . $loc_str . " )  ", array($cid));
								
		$total_reserved_by_new_cust_female = array();
		$total_reserved_by_exist_cust_female = array();

		while($Row1 = $RS_reserve_female->FetchRow())
		{
			$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

			if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
			{
				if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_female))
				{
					$total_reserved_by_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
				}
			}
			else 
			{
				array_push($total_reserved_by_exist_cust_female, $Row1['customer_id']);
			}
		}
						
		$reserved_by_exsting_customer_female = count($total_reserved_by_exist_cust_female);
		$reserved_by_new_customer_female = count($total_reserved_by_new_cust_female);

		//========

		$arr_exsting_cust_male = array();
		$arr_new_cust_male = array();

		$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =?", array($cid));
		$remain_val_male = $RS_remain->fields['total'];

		$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
							FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=? ", array($cid));
		$redeem_val_male = $RS_redeem->RecordCount();
				
		$remain_val_male = $remain_val_male-$redeem_val_male;
		$total_redeem_point_male = 0;
		$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id =? and referred_customer_id<>? ", array($cid, 0));
		$total_referral_point_male = $RS_ref->fields['total'];
		
		$totla_redeem_point_by_new_cust_male = 0;
		$totla_redeem_point_by_exsting_cust_male = 0;
		$total_revenue_cost_by_new_cust_male = 0;
		$total_revenue_cost_by_exist_cust_male = 0;
		
		$male_gender_male =0;
		$female_gender_male =0;
		$arr_age_male = array();
		$agewisegender_male = array();
		while($Row1 = $RS_redeem->FetchRow())
		{
			if ($Row1['gender'] == "") 
			{
				$unknown_gender = $unknown_gender + 1;
			} 
			else if ($Row1['gender'] == 1) 
			{
				$male_gender = $male_gender + 1;
			} 
			else 
			{
				$female_gender = $female_gender + 1;
			}

			$today = new DateTime();
			$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
			$interval = $today->diff($birthdate);
			$age = $interval->format('%y');
			array_push($arr_age_male, $age);
			array_push($agewisegender_male, $Row1['gender']);

			

			$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? ) ", array($Row1['customer_id']));
			$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? ", array($cid, $Row1['customer_id'], 0));

			if($RS2->RecordCount()== 1)
			{
				if(!key_exists($Row1['customer_id'], $arr_new_cust_male))
				{
					$arr_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
				}
				$total_revenue_cost_by_new_cust_male = $total_revenue_cost_by_new_cust_male + $Row1['redeem_value'];
				$totla_redeem_point_by_new_cust_male = $totla_redeem_point_by_new_cust_male + $Rs3->fields['redeem_value'];
			}
			else if($RS2->RecordCount()>1) 
			{	
				array_push($arr_exsting_cust_male, $Row1['customer_id']);
				$totla_redeem_point_by_exsting_cust_male = $totla_redeem_point_by_exsting_cust_male + $Rs3->fields['earned_reward'];
				$total_revenue_cost_by_exist_cust_male = $total_revenue_cost_by_exist_cust_male + $Row1['redeem_value'];
			}
			$total_redeem_point_male = $total_redeem_point_male + $Row1['earned_reward'];
		}

		$redeemed_by_exsting_customer_male = count($arr_exsting_cust_male);
		$redeemed_by_new_customer_male = count($arr_new_cust_male);

		//--------

		$arr_exsting_cust_female = array();
		$arr_new_cust_female = array();

		$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =?", array($cid));
		$remain_val_female = $RS_remain->fields['total'];

		$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
							FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=? ", array($cid));
		$redeem_val_female = $RS_redeem->RecordCount();
				
		$remain_val_female = $remain_val_female-$redeem_val_female;
		$total_redeem_point_female = 0;
		$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id =? and referred_customer_id<>? ", array($cid, 0));
		$total_referral_point_female = $RS_ref->fields['total'];
		
		$totla_redeem_point_by_new_cust_female = 0;
		$totla_redeem_point_by_exsting_cust_female = 0;
		$total_revenue_cost_by_new_cust_female = 0;
		$total_revenue_cost_by_exist_cust_female = 0;
		
		$male_gender =0;
		$female_gender =0;
		$arr_age_female = array();
		$agewisegender_female = array();
		while($Row1 = $RS_redeem->FetchRow())
		{
			if ($Row1['gender'] == "") 
			{
				$unknown_gender = $unknown_gender + 1;
			} 
			else if ($Row1['gender'] == 1) 
			{
				$male_gender = $male_gender + 1;
			} 
			else 
			{
				$female_gender = $female_gender + 1;
			}

			$today = new DateTime();
			$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
			$interval = $today->diff($birthdate);
			$age = $interval->format('%y');
			array_push($arr_age_female, $age);
			array_push($agewisegender_female, $Row1['gender']);

			

			$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? ) ", array($Row1['customer_id']));
			$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? ", array($cid, $Row1['customer_id'], 0));

			if($RS2->RecordCount()== 1)
			{
				if(!key_exists($Row1['customer_id'], $arr_new_cust_female))
				{
					$arr_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
				}
				$total_revenue_cost_by_new_cust_female = $total_revenue_cost_by_new_cust_female + $Row1['redeem_value'];
				$totla_redeem_point_by_new_cust_female = $totla_redeem_point_by_new_cust_female + $Rs3->fields['redeem_value'];
			}
			else if($RS2->RecordCount()>1) 
			{	
				array_push($arr_exsting_cust_female, $Row1['customer_id']);
				$totla_redeem_point_by_exsting_cust_female = $totla_redeem_point_by_exsting_cust_female + $Rs3->fields['earned_reward'];
				$total_revenue_cost_by_exist_cust_female = $total_revenue_cost_by_exist_cust_female + $Row1['redeem_value'];
			}
			$total_redeem_point_female = $total_redeem_point_female + $Row1['earned_reward'];
		}

		$redeemed_by_exsting_customer_female = count($arr_exsting_cust_female);
		$redeemed_by_new_customer_female = count($arr_new_cust_female);


		//========

		$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id=? and referred_customer_id=?  ", array($cid, 0));
		$activation_code_not_redeemed_male = $activation_code_issued_male - ($RS_tot_redeem_coupon->RecordCount());

		//--------

		$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id=? and referred_customer_id=?  ", array($cid, 0));
		$activation_code_not_redeemed_female = $activation_code_issued_female - ($RS_tot_redeem_coupon->RecordCount());


		//========
		
	}

	$json_array=array();
	$json_array['status']='true';
	$json_array['options_loc']=$options_loc;
	$json_array['activation_code_issued_male']=$activation_code_issued_male;
	$json_array['activation_code_issued_female']=$activation_code_issued_female;
	$json_array['reserved_by_exsting_customer_male']=$reserved_by_exsting_customer_male;
	$json_array['reserved_by_exsting_customer_female']=$reserved_by_exsting_customer_female;
	$json_array['reserved_by_new_customer_male']=$reserved_by_new_customer_male;
	$json_array['reserved_by_new_customer_female']=$reserved_by_new_customer_female;
	$json_array['redeemed_by_exsting_customer_male']=$redeemed_by_exsting_customer_male;
	$json_array['redeemed_by_exsting_customer_female']=$redeemed_by_exsting_customer_female;
	$json_array['redeemed_by_new_customer_male']=$redeemed_by_new_customer_male;
	$json_array['redeemed_by_new_customer_female']=$redeemed_by_new_customer_female;
	$json_array['activation_code_not_redeemed_male']=$activation_code_not_redeemed_male;
	$json_array['activation_code_not_redeemed_female']=$activation_code_not_redeemed_female;
	
	$json = json_encode($json_array);
	echo $json;
	exit();
}

if(isset($_REQUEST['point']))
{
	if(isset($_REQUEST['location_id']) && $_REQUEST['location_id']!=0)
	{	
		// location wise

		$activation_code_issued_male = 0;
		$activation_code_issued_female =  0;

		$reserved_by_exsting_customer_male = 0; 
		$reserved_by_exsting_customer_female =  0;

		$reserved_by_new_customer_male =  0;
		$reserved_by_new_customer_female =  0;

		$redeemed_by_exsting_customer_male =  0;
		$redeemed_by_exsting_customer_female =  0;

		$redeemed_by_new_customer_male =  0;
		$redeemed_by_new_customer_female =  0;

		$activation_code_not_redeemed_male =  0;
		$activation_code_not_redeemed_female =  0;

		//$array= array(70,71,80,197,199,258,259);
		$array= array($_REQUEST['location_id']);
		$cid = $_REQUEST['campaign_id'];
		$lid = $_REQUEST['location_id'];
		foreach($array as $val)
		{
			/*
			echo "-------------------------------------";
			echo "location : ".$val. " activation code metrics";
			echo "-------------------------------------"."<br/>";
			*/
			$lid = $val;
			
			//========
			
			$RS1 = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =? and location_id=?", array($cid, $lid));
			$activation_code_issued_male = $RS1->RecordCount();
			
			//-------
			
			$RS1 = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =? and location_id=?", array($cid, $lid));
			$activation_code_issued_female = $RS1->RecordCount();

			//========
			
			$loc_str = " location_id=".$lid;
				   
			$RS_reserve_male = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
									customer_campaign_code=? and c.gender=1 and  ( " . $loc_str . " )  ", array($cid));
									
			$total_reserved_by_new_cust_male = array();
			$total_reserved_by_exist_cust_male = array();
			while($Row1 = $RS_reserve_male->FetchRow())
			{
				$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

				if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
				{
					if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_male))
					{
						$total_reserved_by_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
					}
				}
				else 
				{
					array_push($total_reserved_by_exist_cust_male, $Row1['customer_id']);
				}
			}
							
			$reserved_by_exsting_customer_male = count($total_reserved_by_exist_cust_male);
			$reserved_by_new_customer_male = count($total_reserved_by_new_cust_male);
			
			//-------
			
			$loc_str = " location_id=".$lid;
				   
			$RS_reserve_female = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
									customer_campaign_code=? and c.gender=2 and  ( " . $loc_str . " )  ", array($cid));
									
			$total_reserved_by_new_cust_female = array();
			$total_reserved_by_exist_cust_female = array();
			while($Row1 = $RS_reserve_female->FetchRow())
			{
				$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

				if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
				{
					if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_female))
					{
						$total_reserved_by_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
					}
				}
				else 
				{
					array_push($total_reserved_by_exist_cust_female, $Row1['customer_id']);
				}
			}
							
			$reserved_by_exsting_customer_female = count($total_reserved_by_exist_cust_female);
			$reserved_by_new_customer_female = count($total_reserved_by_new_cust_female);
			
			//========
			
			$arr_exsting_cust_male = array();
			$arr_new_cust_male = array();

			$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =? AND location_id=?", array($cid, $lid));
			$remain_val_male = $RS_remain->fields['total'];

			$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
								FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=?  and cc.location_id=?", array($cid, $lid));
			$redeem_val_male = $RS_redeem->RecordCount();
					
			$remain_val_male = $remain_val_male-$redeem_val_male;
			$total_redeem_point_male = 0;
			$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id = ? and referred_customer_id<>? and location_id=?", array($cid, 0, $lid));
			$total_referral_point_male = $RS_ref->fields['total'];
			
			$totla_redeem_point_by_new_cust_male = 0;
			$totla_redeem_point_by_exsting_cust_male = 0;
			$total_revenue_cost_by_new_cust_male = 0;
			$total_revenue_cost_by_exist_cust_male = 0;
			
			$male_gender =0;
			$female_gender =0;
			$arr_age_male = array();
			$agewisegender_male = array();
			while($Row1 = $RS_redeem->FetchRow())
			{
				if ($Row1['gender'] == "") 
				{
					$unknown_gender = $unknown_gender + 1;
				} 
				else if ($Row1['gender'] == 1) 
				{
					$male_gender = $male_gender + 1;
				} 
				else 
				{
					$female_gender = $female_gender + 1;
				}

				$today = new DateTime();
				$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
				$interval = $today->diff($birthdate);
				$age = $interval->format('%y');
				array_push($arr_age_male, $age);
				array_push($agewisegender_male, $Row1['gender']);

				

				$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? and ( location_id =? )  ) ", array($Row1['customer_id'], $lid));
			$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? and location_id=?", array($cid, $Row1['customer_id'], 0, $lid));

				if($RS2->RecordCount()== 1)
				{
					if(!key_exists($Row1['customer_id'], $arr_new_cust))
					{
						$arr_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
					}
					$total_revenue_cost_by_new_cust_male = $total_revenue_cost_by_new_cust_male + $Row1['redeem_value'];
					$totla_redeem_point_by_new_cust_male = $totla_redeem_point_by_new_cust_male + $Rs3->fields['redeem_value'];
				}
				else if($RS2->RecordCount()>1) 
				{	
					array_push($arr_exsting_cust_male, $Row1['customer_id']);
					$totla_redeem_point_by_exsting_cust_male = $totla_redeem_point_by_exsting_cust_male + $Rs3->fields['earned_reward'];
					$total_revenue_cost_by_exist_cust_male = $total_revenue_cost_by_exist_cust_male + $Row1['redeem_value'];
				}
				$total_redeem_point_male = $total_redeem_point_male + $Row1['earned_reward'];
			}

			$redeemed_by_exsting_customer_male = count($arr_exsting_cust_male);
			$redeemed_by_new_customer_male = count($arr_new_cust_male);
			
			//------
			
			$arr_exsting_cust_female = array();
			$arr_new_cust_female = array();

			$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =? AND location_id=?", array($cid, $lid));
			$remain_val_female = $RS_remain->fields['total'];

			$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
								FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=?  and cc.location_id=?", array($cid, $lid));
			$redeem_val_female = $RS_redeem->RecordCount();
					
			$remain_val_female = $remain_val_female-$redeem_val_female;
			$total_redeem_point_female = 0;
			$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id = ? and referred_customer_id<>? and location_id=?", array($cid, 0, $lid));
			$total_referral_point_female = $RS_ref->fields['total'];
			
			$totla_redeem_point_by_new_cust_female = 0;
			$totla_redeem_point_by_exsting_cust_female = 0;
			$total_revenue_cost_by_new_cust_female = 0;
			$total_revenue_cost_by_exist_cust_female = 0;
			
			$male_gender =0;
			$female_gender =0;
			$arr_age_female = array();
			$agewisegender_female = array();
			
			while($Row1 = $RS_redeem->FetchRow())
			{
				if ($Row1['gender'] == "") 
				{
					$unknown_gender = $unknown_gender + 1;
				} 
				else if ($Row1['gender'] == 1) 
				{
					$male_gender = $male_gender + 1;
				} 
				else 
				{
					$female_gender = $female_gender + 1;
				}

				$today = new DateTime();
				$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
				$interval = $today->diff($birthdate);
				$age = $interval->format('%y');
				array_push($arr_age_female, $age);
				array_push($agewisegender_female, $Row1['gender']);

				

				$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? and ( location_id =? )  ) ", array($Row1['customer_id'], $lid));
			$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? and location_id=?", array($cid, $Row1['customer_id'], 0, $lid));

				if($RS2->RecordCount()== 1)
				{
					if(!key_exists($Row1['customer_id'], $arr_new_cust))
					{
						$arr_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
					}
					$total_revenue_cost_by_new_cust_female = $total_revenue_cost_by_new_cust_female + $Row1['redeem_value'];
					$totla_redeem_point_by_new_cust_female = $totla_redeem_point_by_new_cust_female + $Rs3->fields['redeem_value'];
				}
				else if($RS2->RecordCount()>1) 
				{	
					array_push($arr_exsting_cust_female, $Row1['customer_id']);
					$totla_redeem_point_by_exsting_cust_female = $totla_redeem_point_by_exsting_cust_female + $Rs3->fields['earned_reward'];
					$total_revenue_cost_by_exist_cust_female = $total_revenue_cost_by_exist_cust_female + $Row1['redeem_value'];
				}
				$total_redeem_point_female = $total_redeem_point_female + $Row1['earned_reward'];
			}

			$redeemed_by_exsting_customer_female = count($arr_exsting_cust_female);
			$redeemed_by_new_customer_female = count($arr_new_cust_female);
			
			//========

			$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id=? and referred_customer_id=?   and location_id=? ", array($cid, 0, $lid));
			$activation_code_not_redeemed_male = $activation_code_issued_male - ($RS_tot_redeem_coupon->RecordCount());

			//--------
			
			$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id=? and referred_customer_id=?   and location_id=? ", array($cid, 0, $lid));
			$activation_code_not_redeemed_female = $activation_code_issued_female - ($RS_tot_redeem_coupon->RecordCount());
			
			//========
	
			$arr = file(WEB_PATH.'/merchant/process.php?btnGetCampaignDetail=yes&mer_id='.$_SESSION['merchant_id']."&id=".$cid);
			if(trim($arr[0]) == "")
			{
				unset($arr[0]);
				$arr = array_values($arr);
			}
			$json = json_decode($arr[0]);
			$total_records = $json->total_records;
			$records_array = $json->records;
			
			$referral_rewards = 0;
			$redeem_rewards = 0;
			
			if($total_records>0)
			{
				foreach($records_array as $RS)
				{
					$referral_rewards = $RS->referral_rewards;
					$redeem_rewards = $RS->redeem_rewards;
				}
			}
			$arr = file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
			if(trim($arr[0]) == "")
			{
				unset($arr[0]);
				$arr = array_values($arr);
			}
			$json = json_decode($arr[0]);
			$total_records = $json->total_records;
			$records_array = $json->records;
			if($total_records>0)
			{
				foreach($records_array as $Row)
				{
					$price = $Row->price;
					$point_ = $Row->points;
					$p = (1*$price)/$point_;
				}
			}
			$B4 = (1*$price)/$point_;
			$B5 = $referral_rewards * $B4;
			$B6 = $redeem_rewards * $B4;
			$B7 = $referral_rewards;
			$B8 = $redeem_rewards;
				
			$arr_new_cust = array();
			
			$RS_ref_cnt_male = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id = ? and referred_customer_id<>? and location_id=?", array($cid, 0,$lid));
			$totla_redeem_point_by_exsting_cust_ref_male = 0;
			$totla_redeem_point_by_new_cust_ref_male = 0;
			while($Row1 = $RS_ref_cnt_male->FetchRow())
			{

				$RS2 = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and customer_id =? and referred_customer_id<>?", array($cid, $Row1['customer_id'], 0));

				if($RS2->RecordCount()== 1)
				{
					if(!key_exists($Row1['customer_id'], $arr_new_cust))
					{
					$arr_new_cust_ref[$Row1['customer_id']] = $RS2->RecordCount();
					}
					$totla_redeem_point_by_new_cust_ref_male = $totla_redeem_point_by_new_cust_ref_male + $Row1['referral_reward'];
				}
				else if($RS2->RecordCount()>1) 
				{
					$arr_exsting_cus_reft[$Row1['customer_id']] = $RS2->RecordCount();
					$totla_redeem_point_by_exsting_cust_ref_male = $totla_redeem_point_by_exsting_cust_ref_male+$Row1['referral_reward'];
				}
			}
				
			$tot_revenue_cost_existing_male = $totla_redeem_point_by_exsting_cust_ref_male + $totla_redeem_point_by_exsting_cust_male;
			$tot_revenue_cost_new_male = $totla_redeem_point_by_new_cust_male+$totla_redeem_point_by_new_cust_ref_male;
			$arr_exsting_cust_unique = array_unique($arr_exsting_cust_male);
			
			foreach($arr_exsting_cust_unique as $key => $value)
			{
				//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
				$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc,customer_user cu where cu.id=cc.customer_id and cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?  and cc.location_id=?", array($cid, $arr_exsting_cust_unique[$key],$lid));
				$total_transaction_point_ex_male = $total_transaction_point_ex_male + $rs_t_f->fields['total_transaction_fees'];
				$total_transaction_only_points_ex_male = $total_transaction_only_points_ex_male + $rs_t_f->fields['total_transaction_points'];
			}
			
			$arr_new_cust = array_keys($arr_new_cust);
			foreach($arr_new_cust as $key => $value)
			{
				//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
				$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc ,customer_user cu where cu.id=cc.customer_id and cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?  and cc.location_id=?", array($cid, $arr_new_cust[$key],$lid));
				$total_transaction_point_new_male = $total_transaction_point_new_male + $rs_t_f->fields['total_transaction_fees'];
				$total_transaction_only_points_new_male = $total_transaction_only_points_new_male + $rs_t_f->fields['total_transaction_points'];
			}
			$C14 = $activation_code_issued_male; 
			$C15 = count($arr_exsting_cust_male);
			$C16 = count($arr_new_cust);
			$C17 = $C15 + $C16;
			$C18 = $C14 - ($RS_tot_redeem_coupon->RecordCount());

			$RS_2_share = $objDB->Conn->Execute("SELECT * FROM reward_user ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and referred_customer_id <> ? and referral_reward<>? and  campaign_id =? and location_id=?", array(0, 0, $cid,$lid));

			$total_share_count = $RS_2_share->RecordCount();

			$total_cust = $C17;
			if($total_cust != 0) 
			{
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
				for ($c = 0;$c < count($arr_age);$c++) 
				{
					if ($arr_age[$c] >= 65) 
					{
						if($agewisegender[$c] == 1)
						{

						$agewise_gender_male['65 Or Above'] = $agewise_gender_male['65 Or Above'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['65 Or Above'] = $agewise_gender_female['65 Or Above'] +1;
						}

						$c_6 = $c_6 + 1;
					} 
					else if ($arr_age[$c] >= 55 && $arr_age[$c] <= 64) 
					{
						if($agewisegender[$c] == 1)
						{
						$agewise_gender_male['55 to 64'] = $agewise_gender_male['55 to 64'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['55 to 64'] = $agewise_gender_female['55 to 64'] +1;
						}
						$c_5 = $c_5 + 1;
					} 
					else if ($arr_age[$c] >= 45 && $arr_age[$c] <= 54) 
					{
						if($agewisegender[$c] == 1)
						{
						$agewise_gender_male['45 to 54'] = $agewise_gender_male['45 to 54'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['45 to 54'] = $agewise_gender_female['45 to 54'] +1;
						}
						$c_4 = $c_4 + 1;
					} 
					else if ($arr_age[$c] >= 25 && $arr_age[$c] <= 44) 
					{
						if($agewisegender[$c] == 1)
						{
						$agewise_gender_male['25 to 44'] = $agewise_gender_male['25 to 44'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['25 to 44'] = $agewise_gender_female['25 to 44'] +1;
						}
						$c_3 = $c_3 + 1;
					} 
					else if ($arr_age[$c] >= 18 && $arr_age[$c] <= 24) 
					{
						if($agewisegender[$c] == 1)
						{
						$agewise_gender_male['18 to 24'] = $agewise_gender_male['18 to 24'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['18 to 24'] = $agewise_gender_female['18 to 24'] +1;
						}
						$c_2 = $c_2 + 1;
					} 
					else if ($arr_age[$c] <= 17) 
					{
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
			

				if ($c_1 > 0) 
				{
					$arr_age_txt['17 Or Below'] = $c_1;
				}
				if ($c_2 > 0) 
				{
					$arr_age_txt['18 to 24'] = $c_2;
				}
				if ($c_3 > 0) 
				{
					$arr_age_txt['25 to 44'] = $c_3;
				}
				if ($c_4 > 0) 
				{
					$arr_age_txt['45 to 54'] = $c_4;
				}
				if ($c_5 > 0) 
				{
					$arr_age_txt['55 to 64'] = $c_5;
				}
				if ($c_6 > 0) 
				{
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

			}
			$total_transaction_point_male = $total_transaction_only_points_ex_male + $total_transaction_only_points_new_male;
			$total_transaction_fee_male = $total_transaction_point_ex_male + $total_transaction_point_new_male;
			
			$C22 = $total_share_count * $B7;
			$C23 = $C15 * $B8;
			$C24 = $C16 * $B8;
			$C25 = $C23 + $C24;
			$C26 = $C25 + $C22;
			$C29 = $C22 * $B4;
			$C30 = ($C24 * $B4); //+  $total_transaction_point_new;
			$C31 = ($C23 * $B4); //+  $total_transaction_point_ex;
			$C32 = $C30 + $C31;
			$C28 = $C29 + $C32 + $total_transaction_fee_male;
			
			//echo "<br/>".$C24."==".$B4."==".$total_transaction_point_new."==new<br/>";
			//echo "<br/>".$C23."==".$B4."==".$total_transaction_point_ex."==ex<br/>";
			
			$C34 = $total_revenue_cost_by_new_cust_male + $total_revenue_cost_by_exist_cust_male;
			$C35 = $total_revenue_cost_by_new_cust_male;
			$C36 = $total_revenue_cost_by_exist_cust_male;


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
			
			$total_point_spent_male = $C26;
			$campaign_referral_male = $C22;
			$campaign_redeemption_male = $C25;
			$application_fee_male = $total_transaction_fee_male;
			
			//--------
			
			$arr = file(WEB_PATH.'/merchant/process.php?btnGetCampaignDetail=yes&mer_id='.$_SESSION['merchant_id']."&id=".$cid);
			if(trim($arr[0]) == "")
			{
				unset($arr[0]);
				$arr = array_values($arr);
			}
			$json = json_decode($arr[0]);
			$total_records = $json->total_records;
			$records_array = $json->records;
			
			$referral_rewards = 0;
			$redeem_rewards = 0;
			
			if($total_records>0)
			{
				foreach($records_array as $RS)
				{
					$referral_rewards = $RS->referral_rewards;
					$redeem_rewards = $RS->redeem_rewards;
				}
			}
			$arr = file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
			if(trim($arr[0]) == "")
			{
				unset($arr[0]);
				$arr = array_values($arr);
			}
			$json = json_decode($arr[0]);
			$total_records = $json->total_records;
			$records_array = $json->records;
			if($total_records>0)
			{
				foreach($records_array as $Row)
				{
					$price = $Row->price;
					$point_ = $Row->points;
					$p = (1*$price)/$point_;
				}
			}
			$B4 = (1*$price)/$point_;
			$B5 = $referral_rewards * $B4;
			$B6 = $redeem_rewards * $B4;
			$B7 = $referral_rewards;
			$B8 = $redeem_rewards;
				
			$arr_new_cust = array();
			
			$RS_ref_cnt_female = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id = ? and referred_customer_id<>? and location_id=?", array($cid, 0,$lid));
			$totla_redeem_point_by_exsting_cust_ref_female = 0;
			$totla_redeem_point_by_new_cust_ref_female = 0;
			while($Row1 = $RS_ref_cnt_male->FetchRow())
			{
				
				$RS2 = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and customer_id =? and referred_customer_id<>?", array($cid, $Row1['customer_id'], 0));

				if($RS2->RecordCount()== 1)
				{
					if(!key_exists($Row1['customer_id'], $arr_new_cust))
					{
					$arr_new_cust_ref[$Row1['customer_id']] = $RS2->RecordCount();
					}
					$totla_redeem_point_by_new_cust_ref_female = $totla_redeem_point_by_new_cust_ref_female + $Row1['referral_reward'];
				}
				else if($RS2->RecordCount()>1) 
				{
					$arr_exsting_cus_reft[$Row1['customer_id']] = $RS2->RecordCount();
					$totla_redeem_point_by_exsting_cust_ref_female = $totla_redeem_point_by_exsting_cust_ref_female+$Row1['referral_reward'];
				}
			}
				
			$tot_revenue_cost_existing_female = $totla_redeem_point_by_exsting_cust_ref_female + $totla_redeem_point_by_exsting_cust_female;
			$tot_revenue_cost_new_female = $totla_redeem_point_by_new_cust_female+$totla_redeem_point_by_new_cust_ref_female;
			$arr_exsting_cust_unique = array_unique($arr_exsting_cust_female);
			
			foreach($arr_exsting_cust_unique as $key => $value)
			{
				//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
				$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc ,customer_user cu where cu.id=cc.customer_id and cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?  and cc.location_id=?", array($cid, $arr_exsting_cust_unique[$key],$lid));
				$total_transaction_point_ex_female = $total_transaction_point_ex_female + $rs_t_f->fields['total_transaction_fees'];
				$total_transaction_only_points_ex_female = $total_transaction_only_points_ex_female + $rs_t_f->fields['total_transaction_points'];
			}
			
			$arr_new_cust = array_keys($arr_new_cust);
			foreach($arr_new_cust as $key => $value)
			{
				//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
				$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc ,customer_user cu where cu.id=cc.customer_id and cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?  and cc.location_id=?", array($cid, $arr_new_cust[$key],$lid));
				$total_transaction_point_new_female = $total_transaction_point_new_female + $rs_t_f->fields['total_transaction_fees'];
				$total_transaction_only_points_new_female = $total_transaction_only_points_new_female + $rs_t_f->fields['total_transaction_points'];
			}
			$C14 = $activation_code_issued_female;
			$C15 = count($arr_exsting_cust_female);
			$C16 = count($arr_new_cust);
			$C17 = $C15 + $C16;
			$C18 = $C14 - ($RS_tot_redeem_coupon->RecordCount());

			$RS_2_share = $objDB->Conn->Execute("SELECT * FROM reward_user ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and referred_customer_id <> ? and referral_reward<>? and  campaign_id =?  and location_id=?", array(0, 0, $cid,$lid));

			$total_share_count = $RS_2_share->RecordCount();

			$total_cust = $C17;
			if($total_cust != 0) 
			{
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
				for ($c = 0;$c < count($arr_age);$c++) 
				{
					if ($arr_age[$c] >= 65) 
					{
						if($agewisegender[$c] == 1)
						{

						$agewise_gender_male['65 Or Above'] = $agewise_gender_male['65 Or Above'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['65 Or Above'] = $agewise_gender_female['65 Or Above'] +1;
						}

						$c_6 = $c_6 + 1;
					} 
					else if ($arr_age[$c] >= 55 && $arr_age[$c] <= 64) 
					{
						if($agewisegender[$c] == 1)
						{
						$agewise_gender_male['55 to 64'] = $agewise_gender_male['55 to 64'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['55 to 64'] = $agewise_gender_female['55 to 64'] +1;
						}
						$c_5 = $c_5 + 1;
					} 
					else if ($arr_age[$c] >= 45 && $arr_age[$c] <= 54) 
					{
						if($agewisegender[$c] == 1)
						{
						$agewise_gender_male['45 to 54'] = $agewise_gender_male['45 to 54'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['45 to 54'] = $agewise_gender_female['45 to 54'] +1;
						}
						$c_4 = $c_4 + 1;
					} 
					else if ($arr_age[$c] >= 25 && $arr_age[$c] <= 44) 
					{
						if($agewisegender[$c] == 1)
						{
						$agewise_gender_male['25 to 44'] = $agewise_gender_male['25 to 44'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['25 to 44'] = $agewise_gender_female['25 to 44'] +1;
						}
						$c_3 = $c_3 + 1;
					} 
					else if ($arr_age[$c] >= 18 && $arr_age[$c] <= 24) 
					{
						if($agewisegender[$c] == 1)
						{
						$agewise_gender_male['18 to 24'] = $agewise_gender_male['18 to 24'] +1;
						}
						else if($agewisegender[$c] == 2)
						{
						$agewise_gender_female['18 to 24'] = $agewise_gender_female['18 to 24'] +1;
						}
						$c_2 = $c_2 + 1;
					} 
					else if ($arr_age[$c] <= 17) 
					{
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
			

				if ($c_1 > 0) 
				{
					$arr_age_txt['17 Or Below'] = $c_1;
				}
				if ($c_2 > 0) 
				{
					$arr_age_txt['18 to 24'] = $c_2;
				}
				if ($c_3 > 0) 
				{
					$arr_age_txt['25 to 44'] = $c_3;
				}
				if ($c_4 > 0) 
				{
					$arr_age_txt['45 to 54'] = $c_4;
				}
				if ($c_5 > 0) 
				{
					$arr_age_txt['55 to 64'] = $c_5;
				}
				if ($c_6 > 0) 
				{
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

			}
			$total_transaction_point_female = $total_transaction_only_points_ex_female + $total_transaction_only_points_new_female;
			$total_transaction_fee_female = $total_transaction_point_ex_female + $total_transaction_point_new_female;
			
			$C22 = $total_share_count * $B7;
			$C23 = $C15 * $B8;
			$C24 = $C16 * $B8;
			$C25 = $C23 + $C24;
			$C26 = $C25 + $C22;
			$C29 = $C22 * $B4;
			$C30 = ($C24 * $B4); //+  $total_transaction_point_new;
			$C31 = ($C23 * $B4); //+  $total_transaction_point_ex;
			$C32 = $C30 + $C31;
			$C28 = $C29 + $C32 + $total_transaction_fee_female;
			
			//echo "<br/>".$C24."==".$B4."==".$total_transaction_point_new."==new<br/>";
			//echo "<br/>".$C23."==".$B4."==".$total_transaction_point_ex."==ex<br/>";
			
			$C34 = $total_revenue_cost_by_new_cust_female + $total_revenue_cost_by_exist_cust_female;
			$C35 = $total_revenue_cost_by_new_cust_female;
			$C36 = $total_revenue_cost_by_exist_cust_female;


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
			
			$total_point_spent_female = $C26;
			$campaign_referral_female = $C22;
			$campaign_redeemption_female = $C25;
			$application_fee_female = $total_transaction_fee_female;
			//========
			
			
			
		}
	}
	elseif(isset($_REQUEST['location_id']) && $_REQUEST['location_id']==0)
	{

		$cid = $_REQUEST['campaign_id'];

		//========

		$RS_male = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =?", array($cid));
		$activation_code_issued_male = $RS_male->RecordCount();

		//--------

		$RS_female = $objDB->Conn->Execute("SELECT *  FROM coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =?", array($cid));
		$activation_code_issued_female = $RS_female->RecordCount();

		//========

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

		$RS_reserve_male = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
								customer_campaign_code=? and c.gender=1 and  ( " . $loc_str . " )  ", array($cid));
								
		$total_reserved_by_new_cust_male = array();
		$total_reserved_by_exist_cust_male = array();

		while($Row1 = $RS_reserve_male->FetchRow())
		{
			$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

			if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
			{
				if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_male))
				{
					$total_reserved_by_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
				}
			}
			else 
			{
				array_push($total_reserved_by_exist_cust_male, $Row1['customer_id']);
			}
		}
						
		$reserved_by_exsting_customer_male = count($total_reserved_by_exist_cust_male);
		$reserved_by_new_customer_male = count($total_reserved_by_new_cust_male);

		//-------

		$RS_reserve_female = $objDB->Conn->Execute("Select cc.*,c.gender , c.dob_year , c.dob_month , c.dob_day from coupon_codes cc inner join customer_user c on c.id= cc.customer_id WHERE
								customer_campaign_code=? and c.gender=2 and  ( " . $loc_str . " )  ", array($cid));
								
		$total_reserved_by_new_cust_female = array();
		$total_reserved_by_exist_cust_female = array();

		while($Row1 = $RS_reserve_female->FetchRow())
		{
			$RS2 = $objDB->Conn->Execute("select *  from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id=? and ( " . $loc_str . " )  ) ", array($Row1['customer_id']));

			if($RS2->RecordCount()== 1 || $RS2->RecordCount()==0)
			{
				if(!key_exists($Row1['customer_id'], $total_reserved_by_new_cust_female))
				{
					$total_reserved_by_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
				}
			}
			else 
			{
				array_push($total_reserved_by_exist_cust_female, $Row1['customer_id']);
			}
		}
						
		$reserved_by_exsting_customer_female = count($total_reserved_by_exist_cust_female);
		$reserved_by_new_customer_female = count($total_reserved_by_new_cust_female);

		//========

		$arr_exsting_cust_male = array();
		$arr_new_cust_male = array();

		$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=1 and customer_campaign_code =?", array($cid));
		$remain_val_male = $RS_remain->fields['total'];

		$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
							FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=? ", array($cid));
		$redeem_val_male = $RS_redeem->RecordCount();
				
		$remain_val_male = $remain_val_male-$redeem_val_male;
		$total_redeem_point_male = 0;
		$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id =? and referred_customer_id<>? ", array($cid, 0));
		$total_referral_point_male = $RS_ref->fields['total'];
		
		$totla_redeem_point_by_new_cust_male = 0;
		$totla_redeem_point_by_exsting_cust_male = 0;
		$total_revenue_cost_by_new_cust_male = 0;
		$total_revenue_cost_by_exist_cust_male = 0;
		
		$male_gender_male =0;
		$female_gender_male =0;
		$arr_age_male = array();
		$agewisegender_male = array();
		while($Row1 = $RS_redeem->FetchRow())
		{
			if ($Row1['gender'] == "") 
			{
				$unknown_gender = $unknown_gender + 1;
			} 
			else if ($Row1['gender'] == 1) 
			{
				$male_gender = $male_gender + 1;
			} 
			else 
			{
				$female_gender = $female_gender + 1;
			}

			$today = new DateTime();
			$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
			$interval = $today->diff($birthdate);
			$age = $interval->format('%y');
			array_push($arr_age_male, $age);
			array_push($agewisegender_male, $Row1['gender']);

			

			$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? ) ", array($Row1['customer_id']));
			$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? ", array($cid, $Row1['customer_id'], 0));

			if($RS2->RecordCount()== 1)
			{
				if(!key_exists($Row1['customer_id'], $arr_new_cust_male))
				{
					$arr_new_cust_male[$Row1['customer_id']] = $RS2->RecordCount();
				}
				$total_revenue_cost_by_new_cust_male = $total_revenue_cost_by_new_cust_male + $Row1['redeem_value'];
				$totla_redeem_point_by_new_cust_male = $totla_redeem_point_by_new_cust_male + $Rs3->fields['redeem_value'];
			}
			else if($RS2->RecordCount()>1) 
			{	
				array_push($arr_exsting_cust_male, $Row1['customer_id']);
				$totla_redeem_point_by_exsting_cust_male = $totla_redeem_point_by_exsting_cust_male + $Rs3->fields['earned_reward'];
				$total_revenue_cost_by_exist_cust_male = $total_revenue_cost_by_exist_cust_male + $Row1['redeem_value'];
			}
			$total_redeem_point_male = $total_redeem_point_male + $Row1['earned_reward'];
		}

		$redeemed_by_exsting_customer_male = count($arr_exsting_cust_male);
		$redeemed_by_new_customer_male = count($arr_new_cust_male);

		//--------

		$arr_exsting_cust_female = array();
		$arr_new_cust_female = array();

		$RS_remain = $objDB->Conn->Execute("select count(*) as total from coupon_codes cc, customer_user cu WHERE cc.customer_id=cu.id and cu.gender=2 and customer_campaign_code =?", array($cid));
		$remain_val_female = $RS_remain->fields['total'];

		$RS_redeem = $objDB->Conn->Execute("SELECT cc.customer_id ,cc.customer_campaign_code ,cc.location_id , cr.coupon_id , cr.redeem_value ,cu.gender , cu.dob_year , cu.dob_month , cu.dob_day
							FROM `coupon_redeem` cr , coupon_codes cc inner join customer_user cu on cu.id = cc.customer_id  where cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=? ", array($cid));
		$redeem_val_female = $RS_redeem->RecordCount();
				
		$remain_val_female = $remain_val_female-$redeem_val_female;
		$total_redeem_point_female = 0;
		$RS_ref = $objDB->Conn->Execute("SELECT sum(referral_reward) as total FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id =? and referred_customer_id<>? ", array($cid, 0));
		$total_referral_point_female = $RS_ref->fields['total'];
		
		$totla_redeem_point_by_new_cust_female = 0;
		$totla_redeem_point_by_exsting_cust_female = 0;
		$total_revenue_cost_by_new_cust_female = 0;
		$total_revenue_cost_by_exist_cust_female = 0;
		
		$male_gender =0;
		$female_gender =0;
		$arr_age_female = array();
		$agewisegender_female = array();
		while($Row1 = $RS_redeem->FetchRow())
		{
			if ($Row1['gender'] == "") 
			{
				$unknown_gender = $unknown_gender + 1;
			} 
			else if ($Row1['gender'] == 1) 
			{
				$male_gender = $male_gender + 1;
			} 
			else 
			{
				$female_gender = $female_gender + 1;
			}

			$today = new DateTime();
			$birthdate = new DateTime($Row1['dob_year']."-".$Row1['dob_month']."-".$Row1['dob_day']." 09:48:00");
			$interval = $today->diff($birthdate);
			$age = $interval->format('%y');
			array_push($arr_age_female, $age);
			array_push($agewisegender_female, $Row1['gender']);

			

			$RS2 = $objDB->Conn->Execute("select * from coupon_redeem where coupon_id in ( select id from coupon_codes WHERE customer_id= ? ) ", array($Row1['customer_id']));
			$Rs3 = $objDB->Conn->Execute("SELECT * FROM `reward_user` where campaign_id =? and customer_id =? and   referred_customer_id=? ", array($cid, $Row1['customer_id'], 0));

			if($RS2->RecordCount()== 1)
			{
				if(!key_exists($Row1['customer_id'], $arr_new_cust_female))
				{
					$arr_new_cust_female[$Row1['customer_id']] = $RS2->RecordCount();
				}
				$total_revenue_cost_by_new_cust_female = $total_revenue_cost_by_new_cust_female + $Row1['redeem_value'];
				$totla_redeem_point_by_new_cust_female = $totla_redeem_point_by_new_cust_female + $Rs3->fields['redeem_value'];
			}
			else if($RS2->RecordCount()>1) 
			{	
				array_push($arr_exsting_cust_female, $Row1['customer_id']);
				$totla_redeem_point_by_exsting_cust_female = $totla_redeem_point_by_exsting_cust_female + $Rs3->fields['earned_reward'];
				$total_revenue_cost_by_exist_cust_female = $total_revenue_cost_by_exist_cust_female + $Row1['redeem_value'];
			}
			$total_redeem_point_female = $total_redeem_point_female + $Row1['earned_reward'];
		}

		$redeemed_by_exsting_customer_female = count($arr_exsting_cust_female);
		$redeemed_by_new_customer_female = count($arr_new_cust_female);


		//========

		$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id=? and referred_customer_id=?  ", array($cid, 0));
		$activation_code_not_redeemed_male = $activation_code_issued_male - ($RS_tot_redeem_coupon->RecordCount());

		//--------

		$RS_tot_redeem_coupon = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id=? and referred_customer_id=?  ", array($cid, 0));
		$activation_code_not_redeemed_female = $activation_code_issued_female - ($RS_tot_redeem_coupon->RecordCount());

		//========
		
		$arr = file(WEB_PATH.'/merchant/process.php?btnGetCampaignDetail=yes&mer_id='.$_SESSION['merchant_id']."&id=".$cid);
		if(trim($arr[0]) == "")
		{
			unset($arr[0]);
			$arr = array_values($arr);
		}
		$json = json_decode($arr[0]);
		$total_records = $json->total_records;
		$records_array = $json->records;
		
		$referral_rewards = 0;
		$redeem_rewards = 0;
		
		if($total_records>0)
		{
			foreach($records_array as $RS)
			{
				$referral_rewards = $RS->referral_rewards;
				$redeem_rewards = $RS->redeem_rewards;
			}
		}
		$arr = file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
		if(trim($arr[0]) == "")
		{
			unset($arr[0]);
			$arr = array_values($arr);
		}
		$json = json_decode($arr[0]);
		$total_records = $json->total_records;
		$records_array = $json->records;
		if($total_records>0)
		{
			foreach($records_array as $Row)
			{
				$price = $Row->price;
				$point_ = $Row->points;
				$p = (1*$price)/$point_;
			}
		}
		$B4 = (1*$price)/$point_;
		$B5 = $referral_rewards * $B4;
		$B6 = $redeem_rewards * $B4;
		$B7 = $referral_rewards;
		$B8 = $redeem_rewards;
			
		$arr_new_cust = array();
		
		$RS_ref_cnt_male = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and campaign_id = ? and referred_customer_id<>?", array($cid, 0));
		$totla_redeem_point_by_exsting_cust_ref_male = 0;
		$totla_redeem_point_by_new_cust_ref_male = 0;
		while($Row1 = $RS_ref_cnt_male->FetchRow())
		{
			
			$RS2 = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and customer_id =? and referred_customer_id<>?", array($cid, $Row1['customer_id'], 0));

			if($RS2->RecordCount()== 1)
			{
				if(!key_exists($Row1['customer_id'], $arr_new_cust))
				{
				$arr_new_cust_ref[$Row1['customer_id']] = $RS2->RecordCount();
				}
				$totla_redeem_point_by_new_cust_ref_male = $totla_redeem_point_by_new_cust_ref_male + $Row1['referral_reward'];
			}
			else if($RS2->RecordCount()>1) 
			{
				$arr_exsting_cus_reft[$Row1['customer_id']] = $RS2->RecordCount();
				$totla_redeem_point_by_exsting_cust_ref_male = $totla_redeem_point_by_exsting_cust_ref_male+$Row1['referral_reward'];
			}
		}
			
		$tot_revenue_cost_existing_male = $totla_redeem_point_by_exsting_cust_ref_male + $totla_redeem_point_by_exsting_cust_male;
		$tot_revenue_cost_new_male = $totla_redeem_point_by_new_cust_male+$totla_redeem_point_by_new_cust_ref_male;
		$arr_exsting_cust_unique = array_unique($arr_exsting_cust_male);
		
		foreach($arr_exsting_cust_unique as $key => $value)
		{
			//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
			$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc,customer_user cu where cu.id=cc.customer_id and cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
			$total_transaction_point_ex_male = $total_transaction_point_ex_male + $rs_t_f->fields['total_transaction_fees'];
			$total_transaction_only_points_ex_male = $total_transaction_only_points_ex_male + $rs_t_f->fields['total_transaction_points'];
		}
		
		$arr_new_cust = array_keys($arr_new_cust);
		foreach($arr_new_cust as $key => $value)
		{
			//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
			$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc ,customer_user cu where cu.id=cc.customer_id and cu.gender=1 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
			$total_transaction_point_new_male = $total_transaction_point_new_male + $rs_t_f->fields['total_transaction_fees'];
			$total_transaction_only_points_new_male = $total_transaction_only_points_new_male + $rs_t_f->fields['total_transaction_points'];
		}
		$C14 = $activation_code_issued_male; 
		$C15 = count($arr_exsting_cust_male);
		$C16 = count($arr_new_cust);
		$C17 = $C15 + $C16;
		$C18 = $C14 - ($RS_tot_redeem_coupon->RecordCount());

		$RS_2_share = $objDB->Conn->Execute("SELECT * FROM reward_user ru,customer_user cu where cu.id=ru.customer_id and cu.gender=1 and referred_customer_id <> ? and referral_reward<>? and  campaign_id =?", array(0, 0, $cid));

		$total_share_count = $RS_2_share->RecordCount();

		$total_cust = $C17;
		if($total_cust != 0) 
		{
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
			for ($c = 0;$c < count($arr_age);$c++) 
			{
				if ($arr_age[$c] >= 65) 
				{
					if($agewisegender[$c] == 1)
					{

					$agewise_gender_male['65 Or Above'] = $agewise_gender_male['65 Or Above'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['65 Or Above'] = $agewise_gender_female['65 Or Above'] +1;
					}

					$c_6 = $c_6 + 1;
				} 
				else if ($arr_age[$c] >= 55 && $arr_age[$c] <= 64) 
				{
					if($agewisegender[$c] == 1)
					{
					$agewise_gender_male['55 to 64'] = $agewise_gender_male['55 to 64'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['55 to 64'] = $agewise_gender_female['55 to 64'] +1;
					}
					$c_5 = $c_5 + 1;
				} 
				else if ($arr_age[$c] >= 45 && $arr_age[$c] <= 54) 
				{
					if($agewisegender[$c] == 1)
					{
					$agewise_gender_male['45 to 54'] = $agewise_gender_male['45 to 54'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['45 to 54'] = $agewise_gender_female['45 to 54'] +1;
					}
					$c_4 = $c_4 + 1;
				} 
				else if ($arr_age[$c] >= 25 && $arr_age[$c] <= 44) 
				{
					if($agewisegender[$c] == 1)
					{
					$agewise_gender_male['25 to 44'] = $agewise_gender_male['25 to 44'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['25 to 44'] = $agewise_gender_female['25 to 44'] +1;
					}
					$c_3 = $c_3 + 1;
				} 
				else if ($arr_age[$c] >= 18 && $arr_age[$c] <= 24) 
				{
					if($agewisegender[$c] == 1)
					{
					$agewise_gender_male['18 to 24'] = $agewise_gender_male['18 to 24'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['18 to 24'] = $agewise_gender_female['18 to 24'] +1;
					}
					$c_2 = $c_2 + 1;
				} 
				else if ($arr_age[$c] <= 17) 
				{
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
		

			if ($c_1 > 0) 
			{
				$arr_age_txt['17 Or Below'] = $c_1;
			}
			if ($c_2 > 0) 
			{
				$arr_age_txt['18 to 24'] = $c_2;
			}
			if ($c_3 > 0) 
			{
				$arr_age_txt['25 to 44'] = $c_3;
			}
			if ($c_4 > 0) 
			{
				$arr_age_txt['45 to 54'] = $c_4;
			}
			if ($c_5 > 0) 
			{
				$arr_age_txt['55 to 64'] = $c_5;
			}
			if ($c_6 > 0) 
			{
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

		}
		$total_transaction_point_male = $total_transaction_only_points_ex_male + $total_transaction_only_points_new_male;
		$total_transaction_fee_male = $total_transaction_point_ex_male + $total_transaction_point_new_male;
		
		$C22 = $total_share_count * $B7;
		$C23 = $C15 * $B8;
		$C24 = $C16 * $B8;
		$C25 = $C23 + $C24;
		$C26 = $C25 + $C22;
		$C29 = $C22 * $B4;
		$C30 = ($C24 * $B4); //+  $total_transaction_point_new;
		$C31 = ($C23 * $B4); //+  $total_transaction_point_ex;
		$C32 = $C30 + $C31;
		$C28 = $C29 + $C32 + $total_transaction_fee_male;
		
		//echo "<br/>".$C24."==".$B4."==".$total_transaction_point_new."==new<br/>";
		//echo "<br/>".$C23."==".$B4."==".$total_transaction_point_ex."==ex<br/>";
		
		$C34 = $total_revenue_cost_by_new_cust_male + $total_revenue_cost_by_exist_cust_male;
		$C35 = $total_revenue_cost_by_new_cust_male;
		$C36 = $total_revenue_cost_by_exist_cust_male;


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
		
		$total_point_spent_male = $C26;
		$campaign_referral_male = $C22;
		$campaign_redeemption_male = $C25;
		$application_fee_male = $total_transaction_fee_male;
		
		//--------
		
		$arr = file(WEB_PATH.'/merchant/process.php?btnGetCampaignDetail=yes&mer_id='.$_SESSION['merchant_id']."&id=".$cid);
		if(trim($arr[0]) == "")
		{
			unset($arr[0]);
			$arr = array_values($arr);
		}
		$json = json_decode($arr[0]);
		$total_records = $json->total_records;
		$records_array = $json->records;
		
		$referral_rewards = 0;
		$redeem_rewards = 0;
		
		if($total_records>0)
		{
			foreach($records_array as $RS)
			{
				$referral_rewards = $RS->referral_rewards;
				$redeem_rewards = $RS->redeem_rewards;
			}
		}
		$arr = file(WEB_PATH.'/merchant/process.php?get_point_package=yes');
		if(trim($arr[0]) == "")
		{
			unset($arr[0]);
			$arr = array_values($arr);
		}
		$json = json_decode($arr[0]);
		$total_records = $json->total_records;
		$records_array = $json->records;
		if($total_records>0)
		{
			foreach($records_array as $Row)
			{
				$price = $Row->price;
				$point_ = $Row->points;
				$p = (1*$price)/$point_;
			}
		}
		$B4 = (1*$price)/$point_;
		$B5 = $referral_rewards * $B4;
		$B6 = $redeem_rewards * $B4;
		$B7 = $referral_rewards;
		$B8 = $redeem_rewards;
			
		$arr_new_cust = array();
		
		$RS_ref_cnt_female = $objDB->Conn->Execute("SELECT *  FROM `reward_user` ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and campaign_id = ? and referred_customer_id<>?", array($cid, 0));
		$totla_redeem_point_by_exsting_cust_ref_female = 0;
		$totla_redeem_point_by_new_cust_ref_female = 0;
		while($Row1 = $RS_ref_cnt_male->FetchRow())
		{
			
			$RS2 = $objDB->Conn->Execute("SELECT *  FROM `reward_user` where campaign_id = ? and customer_id =? and referred_customer_id<>?", array($cid, $Row1['customer_id'], 0));

			if($RS2->RecordCount()== 1)
			{
				if(!key_exists($Row1['customer_id'], $arr_new_cust))
				{
				$arr_new_cust_ref[$Row1['customer_id']] = $RS2->RecordCount();
				}
				$totla_redeem_point_by_new_cust_ref_female = $totla_redeem_point_by_new_cust_ref_female + $Row1['referral_reward'];
			}
			else if($RS2->RecordCount()>1) 
			{
				$arr_exsting_cus_reft[$Row1['customer_id']] = $RS2->RecordCount();
				$totla_redeem_point_by_exsting_cust_ref_female = $totla_redeem_point_by_exsting_cust_ref_female+$Row1['referral_reward'];
			}
		}
			
		$tot_revenue_cost_existing_female = $totla_redeem_point_by_exsting_cust_ref_female + $totla_redeem_point_by_exsting_cust_female;
		$tot_revenue_cost_new_female = $totla_redeem_point_by_new_cust_female+$totla_redeem_point_by_new_cust_ref_female;
		$arr_exsting_cust_unique = array_unique($arr_exsting_cust_female);
		
		foreach($arr_exsting_cust_unique as $key => $value)
		{
			//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
			$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc ,customer_user cu where cu.id=cc.customer_id and cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_exsting_cust_unique[$key]));
			$total_transaction_point_ex_female = $total_transaction_point_ex_female + $rs_t_f->fields['total_transaction_fees'];
			$total_transaction_only_points_ex_female = $total_transaction_only_points_ex_female + $rs_t_f->fields['total_transaction_points'];
		}
		
		$arr_new_cust = array_keys($arr_new_cust);
		foreach($arr_new_cust as $key => $value)
		{
			//$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc where cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
			$rs_t_f = $objDB->Conn->Execute("SELECT sum(transaction_fees_price) as total_transaction_fees , sum(transaction_fees) as total_transaction_points FROM `coupon_redeem` cr , coupon_codes cc ,customer_user cu where cu.id=cc.customer_id and cu.gender=2 and cr.coupon_id= cc.id and cc.customer_campaign_code=? and  cc.customer_id=?", array($cid, $arr_new_cust[$key]));
			$total_transaction_point_new_female = $total_transaction_point_new_female + $rs_t_f->fields['total_transaction_fees'];
			$total_transaction_only_points_new_female = $total_transaction_only_points_new_female + $rs_t_f->fields['total_transaction_points'];
		}
		$C14 = $activation_code_issued_female;
		$C15 = count($arr_exsting_cust_female);
		$C16 = count($arr_new_cust);
		$C17 = $C15 + $C16;
		$C18 = $C14 - ($RS_tot_redeem_coupon->RecordCount());

		$RS_2_share = $objDB->Conn->Execute("SELECT * FROM reward_user ru,customer_user cu where cu.id=ru.customer_id and cu.gender=2 and referred_customer_id <> ? and referral_reward<>? and  campaign_id =?", array(0, 0, $cid));

		$total_share_count = $RS_2_share->RecordCount();

		$total_cust = $C17;
		if($total_cust != 0) 
		{
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
			for ($c = 0;$c < count($arr_age);$c++) 
			{
				if ($arr_age[$c] >= 65) 
				{
					if($agewisegender[$c] == 1)
					{

					$agewise_gender_male['65 Or Above'] = $agewise_gender_male['65 Or Above'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['65 Or Above'] = $agewise_gender_female['65 Or Above'] +1;
					}

					$c_6 = $c_6 + 1;
				} 
				else if ($arr_age[$c] >= 55 && $arr_age[$c] <= 64) 
				{
					if($agewisegender[$c] == 1)
					{
					$agewise_gender_male['55 to 64'] = $agewise_gender_male['55 to 64'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['55 to 64'] = $agewise_gender_female['55 to 64'] +1;
					}
					$c_5 = $c_5 + 1;
				} 
				else if ($arr_age[$c] >= 45 && $arr_age[$c] <= 54) 
				{
					if($agewisegender[$c] == 1)
					{
					$agewise_gender_male['45 to 54'] = $agewise_gender_male['45 to 54'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['45 to 54'] = $agewise_gender_female['45 to 54'] +1;
					}
					$c_4 = $c_4 + 1;
				} 
				else if ($arr_age[$c] >= 25 && $arr_age[$c] <= 44) 
				{
					if($agewisegender[$c] == 1)
					{
					$agewise_gender_male['25 to 44'] = $agewise_gender_male['25 to 44'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['25 to 44'] = $agewise_gender_female['25 to 44'] +1;
					}
					$c_3 = $c_3 + 1;
				} 
				else if ($arr_age[$c] >= 18 && $arr_age[$c] <= 24) 
				{
					if($agewisegender[$c] == 1)
					{
					$agewise_gender_male['18 to 24'] = $agewise_gender_male['18 to 24'] +1;
					}
					else if($agewisegender[$c] == 2)
					{
					$agewise_gender_female['18 to 24'] = $agewise_gender_female['18 to 24'] +1;
					}
					$c_2 = $c_2 + 1;
				} 
				else if ($arr_age[$c] <= 17) 
				{
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
		

			if ($c_1 > 0) 
			{
				$arr_age_txt['17 Or Below'] = $c_1;
			}
			if ($c_2 > 0) 
			{
				$arr_age_txt['18 to 24'] = $c_2;
			}
			if ($c_3 > 0) 
			{
				$arr_age_txt['25 to 44'] = $c_3;
			}
			if ($c_4 > 0) 
			{
				$arr_age_txt['45 to 54'] = $c_4;
			}
			if ($c_5 > 0) 
			{
				$arr_age_txt['55 to 64'] = $c_5;
			}
			if ($c_6 > 0) 
			{
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

		}
		$total_transaction_point_female = $total_transaction_only_points_ex_female + $total_transaction_only_points_new_female;
		$total_transaction_fee_female = $total_transaction_point_ex_female + $total_transaction_point_new_female;
		
		$C22 = $total_share_count * $B7;
		$C23 = $C15 * $B8;
		$C24 = $C16 * $B8;
		$C25 = $C23 + $C24;
		$C26 = $C25 + $C22;
		$C29 = $C22 * $B4;
		$C30 = ($C24 * $B4); //+  $total_transaction_point_new;
		$C31 = ($C23 * $B4); //+  $total_transaction_point_ex;
		$C32 = $C30 + $C31;
		$C28 = $C29 + $C32 + $total_transaction_fee_female;
		
		//echo "<br/>".$C24."==".$B4."==".$total_transaction_point_new."==new<br/>";
		//echo "<br/>".$C23."==".$B4."==".$total_transaction_point_ex."==ex<br/>";
		
		$C34 = $total_revenue_cost_by_new_cust_female + $total_revenue_cost_by_exist_cust_female;
		$C35 = $total_revenue_cost_by_new_cust_female;
		$C36 = $total_revenue_cost_by_exist_cust_female;


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
		
		$total_point_spent_female = $C26;
		$campaign_referral_female = $C22;
		$campaign_redeemption_female = $C25;
		$application_fee_female = $total_transaction_fee_female;
		//========
		
		
	}

	$json_array=array();
	$json_array['status']='true';
	
	$json_array['total_point_spent_male']=$total_point_spent_male;
	$json_array['total_point_spent_female']=$total_point_spent_female;
	$json_array['campaign_referral_male']=$campaign_referral_male;
	$json_array['campaign_referral_female']=$campaign_referral_female;
	$json_array['campaign_redeemption_male']=$campaign_redeemption_male;
	$json_array['campaign_redeemption_female']=$campaign_redeemption_female;
	$json_array['application_fee_male']=$application_fee_male;
	$json_array['application_fee_female']=$application_fee_female;
	
	$json = json_encode($json_array);
	echo $json;
	exit();

}

if(isset($_REQUEST['qrcode']))
{
	$cid = $_REQUEST['campaign_id'];
	$lid = $_REQUEST['location_id'];
	
	if(isset($_REQUEST['location_id']) && $_REQUEST['location_id']!=0)
	{
		$RS_2_qrcode = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id where a.campaign_id=? and a.location_id=?", array($cid,$lid));
		$RS_2_qrcode_male = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and a.location_id=? and cu.gender=1", array($cid,$lid));
		$RS_2_qrcode_female = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and a.location_id=? and cu.gender=2", array($cid,$lid));
	
		$RS_2_qrcodeun = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id where a.campaign_id=? and a.is_unique=? and a.location_id=?", array($cid, 1,$lid));
		$RS_2_qrcodeun_male = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and a.is_unique=? and a.location_id=? and cu.gender=1", array($cid, 1,$lid));
		$RS_2_qrcodeun_female = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and a.is_unique=? and a.location_id=? and cu.gender=2", array($cid, 1,$lid));
	}
	elseif(isset($_REQUEST['location_id']) && $_REQUEST['location_id']==0)
	{
		$RS_2_qrcode = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id where a.campaign_id=?", array($cid));
		$RS_2_qrcode_male = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and cu.gender=1", array($cid));
		$RS_2_qrcode_female = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and cu.gender=2", array($cid));

		$RS_2_qrcodeun = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id where a.campaign_id=? and a.is_unique=?", array($cid, 1));
		$RS_2_qrcodeun_male = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and a.is_unique=? and cu.gender=1", array($cid, 1));
		$RS_2_qrcodeun_female = $objDB->Conn->Execute(" select distinct a.* ,b.location_name ,b.created_by ,b.id location_id from scan_qrcode a inner join locations b on a.location_id = b.id inner join campaigns c on a.campaign_id = c.id inner join customer_user cu on cu.id=a.user_id where a.campaign_id=? and a.is_unique=? and cu.gender=2", array($cid, 1));
	}
	
	$json_array=array();
	$json_array['status']='true';
	$json_array['total_scan']=$RS_2_qrcode->RecordCount();
	$json_array['total_scan_male']=$RS_2_qrcode_male->RecordCount();
	$json_array['total_scan_female']=$RS_2_qrcode_female->RecordCount();
	$json_array['unique_scan']=$RS_2_qrcodeun->RecordCount();
	$json_array['unique_scan_male']=$RS_2_qrcodeun_male->RecordCount();
	$json_array['unique_scan_female']=$RS_2_qrcodeun_female->RecordCount();	
	
	$json = json_encode($json_array);
	echo $json;
	exit();
}

if(isset($_REQUEST['pageviewshare']))
{
	$cid = $_REQUEST['campaign_id'];
	$lid = $_REQUEST['location_id'];
	
	if(isset($_REQUEST['location_id']) && $_REQUEST['location_id']!=0)
	{
		//========
	
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
		
		while($Row_domain = $RS_domains_data->FetchRow())
		{
			/* $sql_t = "select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
			  where campaign_id = ".$_REQUEST['id']." and d.id=". $Row_domain['id'];
			  $RS_t = $objDB->Conn->Execute($sql_t); */
			$RS_t = $objDB->Conn->Execute("select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
												where campaign_id =? and d.id=? and c.location_id=?", array($cid, $Row_domain['id'],$lid));

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
			$RS_t = $objDB->Conn->Execute("select count(*) as total ,  p.campaign_id , p.location_id , d.domain from pageview_counter p inner join share_domain d on d.id= p.pageview_domain where campaign_id =? and d.id=? and p.location_id=?", array($cid, $Row_domain['id'],$lid));

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
		
		$RS_qrcodes_view = $objDB->Conn->Execute("select * from scan_qrcode where campaign_id = ? and location_id=?", array($cid,$lid));

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

		//========
	}
	elseif(isset($_REQUEST['location_id']) && $_REQUEST['location_id']==0)
	{
		//========
	
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
		
		while($Row_domain = $RS_domains_data->FetchRow())
		{
			/* $sql_t = "select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
			  where campaign_id = ".$_REQUEST['id']." and d.id=". $Row_domain['id'];
			  $RS_t = $objDB->Conn->Execute($sql_t); */
			$RS_t = $objDB->Conn->Execute("select count(*) as total , c.campaign_id , c.location_id , d.domain from share_counter c inner join share_domain d on d.id= c.campaign_share_domain
												where campaign_id =? and d.id=?", array($cid, $Row_domain['id']));

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
			$RS_t = $objDB->Conn->Execute("select count(*) as total ,  p.campaign_id , p.location_id , d.domain from pageview_counter p inner join share_domain d on d.id= p.pageview_domain where campaign_id =? and d.id=?", array($cid, $Row_domain['id']));

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
		
		$RS_qrcodes_view = $objDB->Conn->Execute("select * from scan_qrcode where campaign_id = ?", array($cid));

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

		//========
	}
	$json_array=array();
	$json_array['status']='true';
	
	$json_array['view_email']=$only_values[1];
	$json_array['view_facebook']=$only_values[3];
	$json_array['view_twitter']=$only_values[5];
	$json_array['view_googleplus']=$only_values[7];
	$json_array['view_other']=$only_values[9];
	
	$json_array['share_email']=$only_values[0];
	$json_array['share_facebook']=$only_values[2];
	$json_array['share_twitter']=$only_values[4];
	$json_array['share_googleplus']=$only_values[6];
	$json_array['share_other']=$only_values[8];
	
	$json = json_encode($json_array);
	echo $json;
	exit();
}

?>
