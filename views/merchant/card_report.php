<?php 

	check_merchant_session();

	/*
	$RS =  $objDB->Conn->Execute("select distinct 
	(select count(*) from cards where card_status=2 and DATE_FORMAT(created_date,'%Y')=? and DATE_FORMAT(created_date,'%m')=? and merchant_id=?) total_cards_activated,
	(select count(*) from customer_loyalty_reward_card where card_status=4 and DATE_FORMAT(created_date,'%Y')=? and DATE_FORMAT(created_date,'%m')=? and merchant_id=?) total_cards_rewarded ,
	(select count(*) from cards where card_status=3 and DATE_FORMAT(created_date,'%Y')=? and DATE_FORMAT(created_date,'%m')=? and merchant_id=?) total_cards_deleted 
	from cards",array($year,$month,$merchant_id,$year,$month,$merchant_id,$year,$month,$merchant_id));
	*/
	
if(isset($_REQUEST['btn_filter_loyalty_card_metrics']))
{
	$json_array = array();
	
	//$card_id = 2;
	$card_id = $_REQUEST['card_id'];
	//$merchant_id = 17
	$merchant_id = $_REQUEST['merchant_id'];

	
	if(isset($_REQUEST['opt_filter_days'])) // after filter
	{
		$days = $_REQUEST['opt_filter_days'];
		$y = date("Y");
		if($days==0)
		{
			$RS =  $objDB->Conn->Execute("select distinct (select count(*) from customer_loyalty_reward_card where card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.")  total_cards_activated,
			   (select count(*) from customer_loyalty_reward_card where card_status=4 and card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_cards_rewarded, 
			   (select count(*) from customer_loyalty_reward_card where card_status=2 and card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y.") total_cards_deleted
			   from merchant_loyalty_card where merchant_id=".$merchant_id);	   
		}
		else
		{
			$RS =  $objDB->Conn->Execute("select distinct (select count(*) from customer_loyalty_reward_card where card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',date_activated ) <= ".$days."))  total_cards_activated,
			   (select count(*) from customer_loyalty_reward_card where card_status=4 and card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',date_activated ) <= ".$days."))  total_cards_rewarded, 
			   (select count(*) from customer_loyalty_reward_card where card_status=2 and card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',date_deleted ) <= ".$days."))  total_cards_deleted
			   from merchant_loyalty_card where merchant_id=".$merchant_id);	
			  
			      
		}
		
		//if($RS->RecordCount()>0 && $RS->fields['total_cards_activated']>0 && $RS->fields['total_cards_rewarded']>0 && $RS->fields['total_cards_deleted']>0)
		if($RS->RecordCount()>0)
		{
			if($days!=0)
			{
				$RS1 =  $objDB->Conn->Execute("select distinct
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.")) total_male_activated_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=1) total_male_activated_desktop_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=2) total_male_activated_mobile_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=3) total_male_activated_qr_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.")) total_female_activated_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=1) total_female_activated_desktop_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=2) total_female_activated_mobile_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=3) total_female_activated_qr_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.")) total_male_rewarded_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=1) total_male_rewarded_desktop_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=2) total_male_rewarded_mobile_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=3) total_male_rewarded_qr_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.")) total_female_rewarded_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=1) total_female_rewarded_desktop_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=2) total_female_rewarded_mobile_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_activated ) <= ".$days.") and uc.device_activation_mode=3) total_female_rewarded_qr_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_deleted ) <= ".$days.")) total_male_deleted_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_deleted ) <= ".$days.") and uc.device_activation_mode=1) total_male_deleted_desktop_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_deleted ) <= ".$days.") and uc.device_activation_mode=2) total_male_deleted_mobile_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_deleted ) <= ".$days.") and uc.device_activation_mode=3) total_male_deleted_qr_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_deleted ) <= ".$days.")) total_female_deleted_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_deleted ) <= ".$days.") and uc.device_activation_mode=1) total_female_deleted_desktop_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_deleted ) <= ".$days.") and uc.device_activation_mode=2) total_female_deleted_mobile_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',uc.date_deleted ) <= ".$days.") and uc.device_activation_mode=3) total_female_deleted_qr_cards 
				from merchant_loyalty_card where merchant_id=".$merchant_id);	
			}
			else
			{
				$RS1 =  $objDB->Conn->Execute("select distinct
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_male_activated_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=1) total_male_activated_desktop_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=2) total_male_activated_mobile_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=3) total_male_activated_qr_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_female_activated_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=1) total_female_activated_desktop_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=2) total_female_activated_mobile_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=3) total_female_activated_qr_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_male_rewarded_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=1) total_male_rewarded_desktop_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=2) total_male_rewarded_mobile_cards,
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=3) total_male_rewarded_qr_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_female_rewarded_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=1) total_female_rewarded_desktop_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=2) total_female_rewarded_mobile_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=3) total_female_rewarded_qr_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y.") total_male_deleted_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=1) total_male_deleted_desktop_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=2) total_male_deleted_mobile_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=3) total_male_deleted_qr_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y.") total_female_deleted_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=1) total_female_deleted_desktop_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=2) total_female_deleted_mobile_cards, 
				(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=3) total_female_deleted_qr_cards 
				from merchant_loyalty_card where merchant_id=".$merchant_id);	
			}
			
			// activated
			
			$total_cards_activated = $RS->fields['total_cards_activated'];
			$total_male_activated_cards = $RS1->fields['total_male_activated_cards'];
			$total_female_activated_cards = $RS1->fields['total_female_activated_cards'];
			
			$total_male_activated_desktop_cards = $RS1->fields['total_male_activated_desktop_cards'];
			$total_male_activated_mobile_cards = $RS1->fields['total_male_activated_mobile_cards'];
			$total_male_activated_qr_cards = $RS1->fields['total_male_activated_qr_cards'];
			
			$total_female_activated_desktop_cards = $RS1->fields['total_female_activated_desktop_cards'];
			$total_female_activated_mobile_cards = $RS1->fields['total_female_activated_mobile_cards'];
			$total_female_activated_qr_cards = $RS1->fields['total_female_activated_qr_cards'];
			
			// rewarded
			
			$total_cards_rewarded = $RS->fields['total_cards_rewarded'];
			$total_male_rewarded_cards = $RS1->fields['total_male_rewarded_cards'];
			$total_female_rewarded_cards = $RS1->fields['total_female_rewarded_cards'];
			
			$total_male_rewarded_desktop_cards = $RS1->fields['total_male_rewarded_desktop_cards'];
			$total_male_rewarded_mobile_cards = $RS1->fields['total_male_rewarded_mobile_cards'];
			$total_male_rewarded_qr_cards = $RS1->fields['total_male_rewarded_qr_cards'];
			
			$total_female_rewarded_desktop_cards = $RS1->fields['total_female_rewarded_desktop_cards'];
			$total_female_rewarded_mobile_cards = $RS1->fields['total_female_rewarded_mobile_cards'];
			$total_female_rewarded_qr_cards = $RS1->fields['total_female_rewarded_qr_cards'];
			
			// deleted
			
			$total_cards_deleted = $RS->fields['total_cards_deleted'];
			$total_male_deleted_cards = $RS1->fields['total_male_deleted_cards'];
			$total_female_deleted_cards = $RS1->fields['total_female_deleted_cards'];
			
			$total_male_deleted_desktop_cards = $RS1->fields['total_male_deleted_desktop_cards'];
			$total_male_deleted_mobile_cards = $RS1->fields['total_male_deleted_mobile_cards'];
			$total_male_deleted_qr_cards = $RS1->fields['total_male_deleted_qr_cards'];
			
			$total_female_deleted_desktop_cards = $RS1->fields['total_female_deleted_desktop_cards'];
			$total_female_deleted_mobile_cards = $RS1->fields['total_female_deleted_mobile_cards'];
			$total_female_deleted_qr_cards = $RS1->fields['total_female_deleted_qr_cards'];
			
			$json_array['status'] = "true";
			
			// start for metrics
			
			$json_array['total_cards_activated'] = $total_cards_activated;
			$json_array['total_male_activated_cards'] = $total_male_activated_cards;
			$json_array['total_female_activated_cards'] = $total_female_activated_cards;
			$json_array['total_male_activated_desktop_cards'] = $total_male_activated_desktop_cards;
			$json_array['total_male_activated_mobile_cards'] = $total_male_activated_mobile_cards;
			$json_array['total_male_activated_qr_cards'] = $total_male_activated_qr_cards;
			$json_array['total_female_activated_desktop_cards'] = $total_female_activated_desktop_cards;
			$json_array['total_female_activated_mobile_cards'] = $total_female_activated_mobile_cards;
			$json_array['total_female_activated_qr_cards'] = $total_female_activated_qr_cards;
			
			$json_array['total_cards_rewarded'] = $total_cards_rewarded;
			$json_array['total_male_rewarded_cards'] = $total_male_rewarded_cards;
			$json_array['total_female_rewarded_cards'] = $total_female_rewarded_cards;
			$json_array['total_male_rewarded_desktop_cards'] = $total_male_rewarded_desktop_cards;
			$json_array['total_male_rewarded_mobile_cards'] = $total_male_rewarded_mobile_cards;
			$json_array['total_male_rewarded_qr_cards'] = $total_male_rewarded_qr_cards;
			$json_array['total_female_rewarded_desktop_cards'] = $total_female_rewarded_desktop_cards;
			$json_array['total_female_rewarded_mobile_cards'] = $total_female_rewarded_mobile_cards;
			$json_array['total_female_rewarded_qr_cards'] = $total_female_rewarded_qr_cards;
			
			$json_array['total_cards_deleted'] = $total_cards_deleted;
			$json_array['total_male_deleted_cards'] = $total_male_deleted_cards;
			$json_array['total_female_deleted_cards'] = $total_female_deleted_cards;
			$json_array['total_male_deleted_desktop_cards'] = $total_male_deleted_desktop_cards;
			$json_array['total_male_deleted_mobile_cards'] = $total_male_deleted_mobile_cards;
			$json_array['total_male_deleted_qr_cards'] = $total_male_deleted_qr_cards;
			$json_array['total_female_deleted_desktop_cards'] = $total_female_deleted_desktop_cards;
			$json_array['total_female_deleted_mobile_cards'] = $total_female_deleted_mobile_cards;
			$json_array['total_female_deleted_qr_cards'] = $total_female_deleted_qr_cards;
			
			// end for metrics
			
			// start for revenue
			
			$RS1 =  $objDB->Conn->Execute("select 
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_revenue',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_male_revenue',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_female_revenue',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_average_revenue_per_visit',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_average_male_revenue_per_visit',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_average_female_revenue_per_visit',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." group by location_id ) customer_loyalty_card_transaction) 'total_average_revenue_per_location',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") group by location_id ) customer_loyalty_card_transaction) 'total_average_male_revenue_per_location',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") group by location_id ) customer_loyalty_card_transaction) 'total_average_female_revenue_per_location'
");
			
			
			$total_revenue=$RS1->fields['total_revenue'];
			$total_male_revenue=$RS1->fields['total_male_revenue'];
			$total_female_revenue=$RS1->fields['total_female_revenue'];
			
			$total_average_revenue_per_visit=$RS1->fields['total_average_revenue_per_visit'];
			$total_average_male_revenue_per_visit=$RS1->fields['total_average_male_revenue_per_visit'];
			$total_average_female_revenue_per_visit=$RS1->fields['total_average_female_revenue_per_visit'];
			
			$total_average_revenue_per_location=$RS1->fields['total_average_revenue_per_location'];
			$total_average_male_revenue_per_location=$RS1->fields['total_average_male_revenue_per_location'];
			$total_average_female_revenue_per_location=$RS1->fields['total_average_female_revenue_per_location'];
			
			$json_array['total_revenue'] = $total_revenue;
			$json_array['total_male_revenue'] = $total_male_revenue;
			$json_array['total_female_revenue'] = $total_female_revenue;
			
			$json_array['total_average_revenue_per_visit'] = $total_average_revenue_per_visit;
			$json_array['total_average_male_revenue_per_visit'] = $total_average_male_revenue_per_visit;
			$json_array['total_average_female_revenue_per_visit'] = $total_average_female_revenue_per_visit;
			
			$json_array['total_average_revenue_per_location'] = $total_average_revenue_per_location;
			$json_array['total_average_male_revenue_per_location'] = $total_average_male_revenue_per_location;
			$json_array['total_average_female_revenue_per_location'] = $total_average_female_revenue_per_location;
    
			// end for revenue
			
			// start customer visit by age
			
			$RS3 =  $objDB->Conn->Execute("select 
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'f_17',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'f_18',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'f_25',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'f_45',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'f_55',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'f_65',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'm_17',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'm_18',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'm_25',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'm_45',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'm_55',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'm_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rf_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rf_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rf_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rf_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rf_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rf_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rm_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rm_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rm_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rm_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rm_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rm_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rt_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rt_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rt_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rt_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rt_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rt_65'");
			
			$f_17 = $RS3->fields['f_17'];
			$f_18 = $RS3->fields['f_18'];
			$f_25 = $RS3->fields['f_25'];
			$f_45 = $RS3->fields['f_45'];
			$f_55 = $RS3->fields['f_55'];
			$f_65 = $RS3->fields['f_65'];
			
			$m_17 = $RS3->fields['m_17'];
			$m_18 = $RS3->fields['m_18'];
			$m_25 = $RS3->fields['m_25'];
			$m_45 = $RS3->fields['m_45'];
			$m_55 = $RS3->fields['m_55'];
			$m_65 = $RS3->fields['m_65'];

			$rf_17 = $RS3->fields['rf_17'];
			$rf_18 = $RS3->fields['rf_18'];
			$rf_25 = $RS3->fields['rf_25'];
			$rf_45 = $RS3->fields['rf_45'];
			$rf_55 = $RS3->fields['rf_55'];
			$rf_65 = $RS3->fields['rf_65'];
			
			$rm_17 = $RS3->fields['rm_17'];
			$rm_18 = $RS3->fields['rm_18'];
			$rm_25 = $RS3->fields['rm_25'];
			$rm_45 = $RS3->fields['rm_45'];
			$rm_55 = $RS3->fields['rm_55'];
			$rm_65 = $RS3->fields['rm_65'];
			
			$rt_17 = $RS3->fields['rt_17'];
			$rt_18 = $RS3->fields['rt_18'];
			$rt_25 = $RS3->fields['rt_25'];
			$rt_45 = $RS3->fields['rt_45'];
			$rt_55 = $RS3->fields['rt_55'];
			$rt_65 = $RS3->fields['rt_65'];
																																				
			$json_array['f_17'] = $f_17;
			$json_array['f_18'] = $f_18;
			$json_array['f_25'] = $f_25;
			$json_array['f_45'] = $f_45;
			$json_array['f_55'] = $f_55;
			$json_array['f_65'] = $f_65;
			
			$json_array['m_17'] = $m_17;
			$json_array['m_18'] = $m_18;
			$json_array['m_25'] = $m_25;
			$json_array['m_45'] = $m_45;
			$json_array['m_55'] = $m_55;
			$json_array['m_65'] = $m_65;
			
			$json_array['rf_17'] = $rf_17;
			$json_array['rf_18'] = $rf_18;
			$json_array['rf_25'] = $rf_25;
			$json_array['rf_45'] = $rf_45;
			$json_array['rf_55'] = $rf_55;
			$json_array['rf_65'] = $rf_65;
			
			$json_array['rm_17'] = $rm_17;
			$json_array['rm_18'] = $rm_18;
			$json_array['rm_25'] = $rm_25;
			$json_array['rm_45'] = $rm_45;
			$json_array['rm_55'] = $rm_55;
			$json_array['rm_65'] = $rm_65;
			
			$json_array['rt_17'] = $rt_17;
			$json_array['rt_18'] = $rt_18;
			$json_array['rt_25'] = $rt_25;
			$json_array['rt_45'] = $rt_45;
			$json_array['rt_55'] = $rt_55;
			$json_array['rt_65'] = $rt_65;
			
			$options_age='<option value="0" selected="selected" >All Participating Locations</option>';
			
			$sql_loy_loc = 'SELECT location_id,address,city,state,zip FROM loyaltycard_location ll,locations l where l.id=ll.location_id and loyalty_card_id ='.$card_id;
			$rs_loy_loc = $objDB->Conn->Execute($sql_loy_loc);
			while($Row_loy_loc = $rs_loy_loc->FetchRow())
			{
				$loc_add_str = $Row_loy_loc['address'].','.$Row_loy_loc['city'].','.$Row_loy_loc['state'].','.$Row_loy_loc['zip'];
				if(strlen($loc_add_str)>28)
				{
					$loc_add_str = substr($loc_add_str,0,28)."...";
				}
				$options_age .= '<option value="'.$Row_loy_loc['location_id'].'" >'.$loc_add_str.'</option>';			
			}
				
			$json_array['options_age']=$options_age;
			
			// end customer visit by age
			
			// start customer visit by time
			
			$RS4 =  $objDB->Conn->Execute("select 
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_1',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_2',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_3',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_4',

(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_1',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_2',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_3',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_4',

(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_4',

(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_4',


(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_4'");
				
			$f_1 = $RS4->fields['f_1'];
			$f_2 = $RS4->fields['f_2'];
			$f_3 = $RS4->fields['f_3'];
			$f_4 = $RS4->fields['f_4'];
			
			$m_1 = $RS4->fields['m_1'];
			$m_2 = $RS4->fields['m_2'];
			$m_3 = $RS4->fields['m_3'];
			$m_4 = $RS4->fields['m_4'];

			$rf_1 = $RS4->fields['rf_1'];
			$rf_2 = $RS4->fields['rf_2'];
			$rf_3 = $RS4->fields['rf_3'];
			$rf_4 = $RS4->fields['rf_4'];
			
			$rm_1 = $RS4->fields['rm_1'];
			$rm_2 = $RS4->fields['rm_2'];
			$rm_3 = $RS4->fields['rm_3'];
			$rm_4 = $RS4->fields['rm_4'];
			
			$rt_1 = $RS4->fields['rt_1'];
			$rt_2 = $RS4->fields['rt_2'];
			$rt_3 = $RS4->fields['rt_3'];
			$rt_4 = $RS4->fields['rt_4'];
																																				
			$json_array['f_1'] = $f_1;
			$json_array['f_2'] = $f_2;
			$json_array['f_3'] = $f_3;
			$json_array['f_4'] = $f_4;
			
			$json_array['m_1'] = $m_1;
			$json_array['m_2'] = $m_2;
			$json_array['m_3'] = $m_3;
			$json_array['m_4'] = $m_4;
			
			$json_array['rf_1'] = $rf_1;
			$json_array['rf_2'] = $rf_2;
			$json_array['rf_3'] = $rf_3;
			$json_array['rf_4'] = $rf_4;
			
			$json_array['rm_1'] = $rm_1;
			$json_array['rm_2'] = $rm_2;
			$json_array['rm_3'] = $rm_3;
			$json_array['rm_4'] = $rm_4;
			
			$json_array['rt_1'] = $rt_1;
			$json_array['rt_2'] = $rt_2;
			$json_array['rt_3'] = $rt_3;
			$json_array['rt_4'] = $rt_4;	
			
			$json_array['options_time']=$options_age;
			
			// end customer visit by time
			
			$json = json_encode($json_array);
			echo $json;
			exit();
			
		}
		else
		{
			$options_age='<option value="0" selected="selected" >All Participating Locations</option>';
			
			$sql_loy_loc = 'SELECT location_id,address,city,state,zip FROM loyaltycard_location ll,locations l where l.id=ll.location_id and loyalty_card_id ='.$card_id;
			$rs_loy_loc = $objDB->Conn->Execute($sql_loy_loc);
			while($Row_loy_loc = $rs_loy_loc->FetchRow())
			{
				$loc_add_str = $Row_loy_loc['address'].','.$Row_loy_loc['city'].','.$Row_loy_loc['state'].','.$Row_loy_loc['zip'];
				if(strlen($loc_add_str)>28)
				{
					$loc_add_str = substr($loc_add_str,0,28)."...";
				}
				$options_age .= '<option value="'.$Row_loy_loc['location_id'].'" >'.$loc_add_str.'</option>';			
			}
				
			$json_array['options_age']=$options_age;
			$json_array['options_time']=$options_age;
			
			$json_array['status'] = "false";
			$json_array['message'] = "No data found";
			$json = json_encode($json_array);
			echo $json;
			exit();
		}
	}	
	else // first time current year data
	{
	   
		$y = date("Y");
		$RS =  $objDB->Conn->Execute("select distinct (select count(*) from customer_loyalty_reward_card where card_status=1 and card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.")  total_cards_activated,
			   (select count(*) from customer_loyalty_reward_card where card_status=4 and card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_cards_rewarded, 
			   (select count(*) from customer_loyalty_reward_card where card_status=2 and card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y.") total_cards_deleted
			   from merchant_loyalty_card  where merchant_id=".$merchant_id);	   

		if($RS->RecordCount()>0 && $RS->fields['total_cards_activated']>0 && $RS->fields['total_cards_rewarded']>0 && $RS->fields['total_cards_deleted']>0)
		{
			
			$RS1 =  $objDB->Conn->Execute("select distinct
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_male_activated_cards,
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=1) total_male_activated_desktop_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=2) total_male_activated_mobile_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=3) total_male_activated_qr_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_female_activated_cards,
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=1) total_female_activated_desktop_cards,
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=2) total_female_activated_mobile_cards,
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=1 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=3) total_female_activated_qr_cards,
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_male_rewarded_cards,
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=1) total_male_rewarded_desktop_cards,
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=2) total_male_rewarded_mobile_cards,
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=3) total_male_rewarded_qr_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y.") total_female_rewarded_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=1) total_female_rewarded_desktop_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=2) total_female_rewarded_mobile_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=4 and uc.card_id=".$card_id." and DATE_FORMAT(date_activated,'%Y')=".$y." and uc.device_activation_mode=3) total_female_rewarded_qr_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y.") total_male_deleted_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=1) total_male_deleted_desktop_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=2) total_male_deleted_mobile_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=1 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=3) total_male_deleted_qr_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y.") total_female_deleted_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=1) total_female_deleted_desktop_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=2) total_female_deleted_mobile_cards, 
			(select count(*) from customer_loyalty_reward_card uc,customer_user cu where uc.user_id=cu.id and cu.gender=2 and uc.card_status=2 and uc.card_id=".$card_id." and DATE_FORMAT(date_deleted,'%Y')=".$y." and uc.device_activation_mode=3) total_female_deleted_qr_cards 
			from merchant_loyalty_card  where merchant_id=".$merchant_id);	
			
			// activated
			
			$total_cards_activated = $RS->fields['total_cards_activated'];
			$total_male_activated_cards = $RS1->fields['total_male_activated_cards'];
			$total_female_activated_cards = $RS1->fields['total_female_activated_cards'];
			
			$total_male_activated_desktop_cards = $RS1->fields['total_male_activated_desktop_cards'];
			$total_male_activated_mobile_cards = $RS1->fields['total_male_activated_mobile_cards'];
			$total_male_activated_qr_cards = $RS1->fields['total_male_activated_qr_cards'];
			
			$total_female_activated_desktop_cards = $RS1->fields['total_female_activated_desktop_cards'];
			$total_female_activated_mobile_cards = $RS1->fields['total_female_activated_mobile_cards'];
			$total_female_activated_qr_cards = $RS1->fields['total_female_activated_qr_cards'];
			
			// rewarded
			
			$total_cards_rewarded = $RS->fields['total_cards_rewarded'];
			$total_male_rewarded_cards = $RS1->fields['total_male_rewarded_cards'];
			$total_female_rewarded_cards = $RS1->fields['total_female_rewarded_cards'];
			
			$total_male_rewarded_desktop_cards = $RS1->fields['total_male_rewarded_desktop_cards'];
			$total_male_rewarded_mobile_cards = $RS1->fields['total_male_rewarded_mobile_cards'];
			$total_male_rewarded_qr_cards = $RS1->fields['total_male_rewarded_qr_cards'];
			
			$total_female_rewarded_desktop_cards = $RS1->fields['total_female_rewarded_desktop_cards'];
			$total_female_rewarded_mobile_cards = $RS1->fields['total_female_rewarded_mobile_cards'];
			$total_female_rewarded_qr_cards = $RS1->fields['total_female_rewarded_qr_cards'];
			
			// deleted
			
			$total_cards_deleted = $RS->fields['total_cards_deleted'];
			$total_male_deleted_cards = $RS1->fields['total_male_deleted_cards'];
			$total_female_deleted_cards = $RS1->fields['total_female_deleted_cards'];
			
			$total_male_deleted_desktop_cards = $RS1->fields['total_male_deleted_desktop_cards'];
			$total_male_deleted_mobile_cards = $RS1->fields['total_male_deleted_mobile_cards'];
			$total_male_deleted_qr_cards = $RS1->fields['total_male_deleted_qr_cards'];
			
			$total_female_deleted_desktop_cards = $RS1->fields['total_female_deleted_desktop_cards'];
			$total_female_deleted_mobile_cards = $RS1->fields['total_female_deleted_mobile_cards'];
			$total_female_deleted_qr_cards = $RS1->fields['total_female_deleted_qr_cards'];
			
			$json_array['status'] = "true";
			
			// start for metrics
			
			$json_array['total_cards_activated'] = $total_cards_activated;
			$json_array['total_male_activated_cards'] = $total_male_activated_cards;
			$json_array['total_female_activated_cards'] = $total_female_activated_cards;
			$json_array['total_male_activated_desktop_cards'] = $total_male_activated_desktop_cards;
			$json_array['total_male_activated_mobile_cards'] = $total_male_activated_mobile_cards;
			$json_array['total_male_activated_qr_cards'] = $total_male_activated_qr_cards;
			$json_array['total_female_activated_desktop_cards'] = $total_female_activated_desktop_cards;
			$json_array['total_female_activated_mobile_cards'] = $total_female_activated_mobile_cards;
			$json_array['total_female_activated_qr_cards'] = $total_female_activated_qr_cards;
			
			$json_array['total_cards_rewarded'] = $total_cards_rewarded;
			$json_array['total_male_rewarded_cards'] = $total_male_rewarded_cards;
			$json_array['total_female_rewarded_cards'] = $total_female_rewarded_cards;
			$json_array['total_male_rewarded_desktop_cards'] = $total_male_rewarded_desktop_cards;
			$json_array['total_male_rewarded_mobile_cards'] = $total_male_rewarded_mobile_cards;
			$json_array['total_male_rewarded_qr_cards'] = $total_male_rewarded_qr_cards;
			$json_array['total_female_rewarded_desktop_cards'] = $total_female_rewarded_desktop_cards;
			$json_array['total_female_rewarded_mobile_cards'] = $total_female_rewarded_mobile_cards;
			$json_array['total_female_rewarded_qr_cards'] = $total_female_rewarded_qr_cards;
			
			$json_array['total_cards_deleted'] = $total_cards_deleted;
			$json_array['total_male_deleted_cards'] = $total_male_deleted_cards;
			$json_array['total_female_deleted_cards'] = $total_female_deleted_cards;
			$json_array['total_male_deleted_desktop_cards'] = $total_male_deleted_desktop_cards;
			$json_array['total_male_deleted_mobile_cards'] = $total_male_deleted_mobile_cards;
			$json_array['total_male_deleted_qr_cards'] = $total_male_deleted_qr_cards;
			$json_array['total_female_deleted_desktop_cards'] = $total_female_deleted_desktop_cards;
			$json_array['total_female_deleted_mobile_cards'] = $total_female_deleted_mobile_cards;
			$json_array['total_female_deleted_qr_cards'] = $total_female_deleted_qr_cards;
			
			// end for metrics
			
			// start for revenue		
			
			$RS2 =  $objDB->Conn->Execute("select 
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_revenue',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_male_revenue',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_female_revenue',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_average_revenue_per_visit',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_average_male_revenue_per_visit',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_average_female_revenue_per_visit',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." group by location_id ) customer_loyalty_card_transaction) 'total_average_revenue_per_location',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." group by location_id ) customer_loyalty_card_transaction) 'total_average_male_revenue_per_location',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." group by location_id ) customer_loyalty_card_transaction) 'total_average_female_revenue_per_location'
");
			
			$total_revenue=$RS2->fields['total_revenue'];
			$total_male_revenue=$RS2->fields['total_male_revenue'];
			$total_female_revenue=$RS2->fields['total_female_revenue'];
			
			$total_average_revenue_per_visit=$RS2->fields['total_average_revenue_per_visit'];
			$total_average_male_revenue_per_visit=$RS2->fields['total_average_male_revenue_per_visit'];
			$total_average_female_revenue_per_visit=$RS2->fields['total_average_female_revenue_per_visit'];
			
			$total_average_revenue_per_location=$RS2->fields['total_average_revenue_per_location'];
			$total_average_male_revenue_per_location=$RS2->fields['total_average_male_revenue_per_location'];
			$total_average_female_revenue_per_location=$RS2->fields['total_average_female_revenue_per_location'];
			
			$json_array['total_revenue'] = $total_revenue;
			$json_array['total_male_revenue'] = $total_male_revenue;
			$json_array['total_female_revenue'] = $total_female_revenue;
			
			$json_array['total_average_revenue_per_visit'] = $total_average_revenue_per_visit;
			$json_array['total_average_male_revenue_per_visit'] = $total_average_male_revenue_per_visit;
			$json_array['total_average_female_revenue_per_visit'] = $total_average_female_revenue_per_visit;
			
			$json_array['total_average_revenue_per_location'] = $total_average_revenue_per_location;
			$json_array['total_average_male_revenue_per_location'] = $total_average_male_revenue_per_location;
			$json_array['total_average_female_revenue_per_location'] = $total_average_female_revenue_per_location;
    
			// end for revenue
			
			// start for customer visit by age
			
			$RS3 =  $objDB->Conn->Execute("select 
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'f_17',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'f_18',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'f_25',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'f_45',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'f_55',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'f_65',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'm_17',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'm_18',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'm_25',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'm_45',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'm_55',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'm_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rf_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rf_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rf_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rf_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rf_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rf_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rm_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rm_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rm_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rm_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rm_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rm_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rt_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rt_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rt_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rt_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rt_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rt_65'");

			$f_17 = $RS3->fields['f_17'];
			$f_18 = $RS3->fields['f_18'];
			$f_25 = $RS3->fields['f_25'];
			$f_45 = $RS3->fields['f_45'];
			$f_55 = $RS3->fields['f_55'];
			$f_65 = $RS3->fields['f_65'];
			
			$m_17 = $RS3->fields['m_17'];
			$m_18 = $RS3->fields['m_18'];
			$m_25 = $RS3->fields['m_25'];
			$m_45 = $RS3->fields['m_45'];
			$m_55 = $RS3->fields['m_55'];
			$m_65 = $RS3->fields['m_65'];

			$rf_17 = $RS3->fields['rf_17'];
			$rf_18 = $RS3->fields['rf_18'];
			$rf_25 = $RS3->fields['rf_25'];
			$rf_45 = $RS3->fields['rf_45'];
			$rf_55 = $RS3->fields['rf_55'];
			$rf_65 = $RS3->fields['rf_65'];
			
			$rm_17 = $RS3->fields['rm_17'];
			$rm_18 = $RS3->fields['rm_18'];
			$rm_25 = $RS3->fields['rm_25'];
			$rm_45 = $RS3->fields['rm_45'];
			$rm_55 = $RS3->fields['rm_55'];
			$rm_65 = $RS3->fields['rm_65'];
			
			$rt_17 = $RS3->fields['rt_17'];
			$rt_18 = $RS3->fields['rt_18'];
			$rt_25 = $RS3->fields['rt_25'];
			$rt_45 = $RS3->fields['rt_45'];
			$rt_55 = $RS3->fields['rt_55'];
			$rt_65 = $RS3->fields['rt_65'];
																																				
			$json_array['f_17'] = $f_17;
			$json_array['f_18'] = $f_18;
			$json_array['f_25'] = $f_25;
			$json_array['f_45'] = $f_45;
			$json_array['f_55'] = $f_55;
			$json_array['f_65'] = $f_65;
			
			$json_array['m_17'] = $m_17;
			$json_array['m_18'] = $m_18;
			$json_array['m_25'] = $m_25;
			$json_array['m_45'] = $m_45;
			$json_array['m_55'] = $m_55;
			$json_array['m_65'] = $m_65;
			
			$json_array['rf_17'] = $rf_17;
			$json_array['rf_18'] = $rf_18;
			$json_array['rf_25'] = $rf_25;
			$json_array['rf_45'] = $rf_45;
			$json_array['rf_55'] = $rf_55;
			$json_array['rf_65'] = $rf_65;
			
			$json_array['rm_17'] = $rm_17;
			$json_array['rm_18'] = $rm_18;
			$json_array['rm_25'] = $rm_25;
			$json_array['rm_45'] = $rm_45;
			$json_array['rm_55'] = $rm_55;
			$json_array['rm_65'] = $rm_65;
			
			$json_array['rt_17'] = $rt_17;
			$json_array['rt_18'] = $rt_18;
			$json_array['rt_25'] = $rt_25;
			$json_array['rt_45'] = $rt_45;
			$json_array['rt_55'] = $rt_55;
			$json_array['rt_65'] = $rt_65;
				
			// end for customer visit by age
			
			// start for customer visit by time
			
			$RS4 =  $objDB->Conn->Execute("select 
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=1 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'f_1',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=2 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'f_2',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=3 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'f_3',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=4 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'f_4',

(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=1 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'm_1',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=2 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'm_2',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=3 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'm_3',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=4 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'm_4',

(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=1 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rf_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=2 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rf_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=3 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rf_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=4 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rf_4',

(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=1 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rm_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=2 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rm_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=3 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rm_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=4 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rm_4',


(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=1 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rt_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=2 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rt_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=3 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rt_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=4 and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'rt_4'");

			$f_1 = $RS4->fields['f_1'];
			$f_2 = $RS4->fields['f_2'];
			$f_3 = $RS4->fields['f_3'];
			$f_4 = $RS4->fields['f_4'];
			
			$m_1 = $RS4->fields['m_1'];
			$m_2 = $RS4->fields['m_2'];
			$m_3 = $RS4->fields['m_3'];
			$m_4 = $RS4->fields['m_4'];

			$rf_1 = $RS4->fields['rf_1'];
			$rf_2 = $RS4->fields['rf_2'];
			$rf_3 = $RS4->fields['rf_3'];
			$rf_4 = $RS4->fields['rf_4'];
			
			$rm_1 = $RS4->fields['rm_1'];
			$rm_2 = $RS4->fields['rm_2'];
			$rm_3 = $RS4->fields['rm_3'];
			$rm_4 = $RS4->fields['rm_4'];
			
			$rt_1 = $RS4->fields['rt_1'];
			$rt_2 = $RS4->fields['rt_2'];
			$rt_3 = $RS4->fields['rt_3'];
			$rt_4 = $RS4->fields['rt_4'];
																																				
			$json_array['f_1'] = $f_1;
			$json_array['f_2'] = $f_2;
			$json_array['f_3'] = $f_3;
			$json_array['f_4'] = $f_4;
			
			$json_array['m_1'] = $m_1;
			$json_array['m_2'] = $m_2;
			$json_array['m_3'] = $m_3;
			$json_array['m_4'] = $m_4;
			
			$json_array['rf_1'] = $rf_1;
			$json_array['rf_2'] = $rf_2;
			$json_array['rf_3'] = $rf_3;
			$json_array['rf_4'] = $rf_4;
			
			$json_array['rm_1'] = $rm_1;
			$json_array['rm_2'] = $rm_2;
			$json_array['rm_3'] = $rm_3;
			$json_array['rm_4'] = $rm_4;
			
			$json_array['rt_1'] = $rt_1;
			$json_array['rt_2'] = $rt_2;
			$json_array['rt_3'] = $rt_3;
			$json_array['rt_4'] = $rt_4;
			
			// end for customer visit by time
			
			$json = json_encode($json_array);
			echo $json;
			exit();
			
		}
		else
		{
			$json_array['status'] = "false";
			$json_array['message'] = "No data found";
			$json = json_encode($json_array);
			echo $json;
			exit();
		}
	}
}

if(isset($_REQUEST['btn_filter_loyalty_card_revenue']))
{
	$json_array = array();
	
	//$card_id = 2;
	$card_id = $_REQUEST['card_id'];

	//$merchant_id = 17
	$merchant_id = $_REQUEST['merchant_id'];

	
	if(isset($_REQUEST['opt_filter_days'])) // after filter
	{
		$days = $_REQUEST['opt_filter_days'];
		$y = date("Y");
		if($days!=0)
		{
			$RS1 =  $objDB->Conn->Execute("select 
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_revenue',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_male_revenue',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_female_revenue',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_average_revenue_per_visit',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_average_male_revenue_per_visit',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'total_average_female_revenue_per_visit',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." group by location_id ) customer_loyalty_card_transaction) 'total_average_revenue_per_location',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") group by location_id ) customer_loyalty_card_transaction) 'total_average_male_revenue_per_location',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") group by location_id ) customer_loyalty_card_transaction) 'total_average_female_revenue_per_location'
");		
		}
		else
		{
			$RS1 =  $objDB->Conn->Execute("select 
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_revenue',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_male_revenue',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_female_revenue',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_average_revenue_per_visit',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_average_male_revenue_per_visit',
(select IFNULL(ROUND(AVG(revenue),2),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y.") 'total_average_female_revenue_per_visit',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." group by location_id ) customer_loyalty_card_transaction) 'total_average_revenue_per_location',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=1 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." group by location_id ) customer_loyalty_card_transaction) 'total_average_male_revenue_per_location',
(select IFNULL(ROUND(avg(total_average_revenue_per_location),2),0) from (select IFNULL(ROUND(avg(revenue),2),0) 'total_average_revenue_per_location' from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and cu.gender=2 and uc.card_id=".$card_id." and DATE_FORMAT(transaction_date,'%Y')=".$y." group by location_id ) customer_loyalty_card_transaction) 'total_average_female_revenue_per_location'
");	
		}		
			
			$json_array['status'] = "true";
			
			// start for revenue
			
			$total_revenue=$RS1->fields['total_revenue'];
			$total_male_revenue=$RS1->fields['total_male_revenue'];
			$total_female_revenue=$RS1->fields['total_female_revenue'];
			
			$total_average_revenue_per_visit=$RS1->fields['total_average_revenue_per_visit'];
			$total_average_male_revenue_per_visit=$RS1->fields['total_average_male_revenue_per_visit'];
			$total_average_female_revenue_per_visit=$RS1->fields['total_average_female_revenue_per_visit'];
			
			$total_average_revenue_per_location=$RS1->fields['total_average_revenue_per_location'];
			$total_average_male_revenue_per_location=$RS1->fields['total_average_male_revenue_per_location'];
			$total_average_female_revenue_per_location=$RS1->fields['total_average_female_revenue_per_location'];
			
			$json_array['total_revenue'] = $total_revenue;
			$json_array['total_male_revenue'] = $total_male_revenue;
			$json_array['total_female_revenue'] = $total_female_revenue;
			
			$json_array['total_average_revenue_per_visit'] = $total_average_revenue_per_visit;
			$json_array['total_average_male_revenue_per_visit'] = $total_average_male_revenue_per_visit;
			$json_array['total_average_female_revenue_per_visit'] = $total_average_female_revenue_per_visit;
			
			$json_array['total_average_revenue_per_location'] = $total_average_revenue_per_location;
			$json_array['total_average_male_revenue_per_location'] = $total_average_male_revenue_per_location;
			$json_array['total_average_female_revenue_per_location'] = $total_average_female_revenue_per_location;
    
			// end for revenue
			
			$json = json_encode($json_array);
			echo $json;
			exit();
			
	}
	else
	{
		$json_array['status'] = "false";
		$json_array['message'] = "No data found";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}	
	
}

if(isset($_REQUEST['btn_filter_loyalty_by_age']))
{
	$json_array = array();
	
	//$card_id = 2;
	$card_id = $_REQUEST['card_id'];

	//$merchant_id = 17
	$merchant_id = $_REQUEST['merchant_id'];

	
	if(isset($_REQUEST['opt_filter_location']) && isset($_REQUEST['opt_filter_days'])) // after filter
	{
		$opt_filter_location = $_REQUEST['opt_filter_location'];
		$days = $_REQUEST['opt_filter_days'];
		$y = date("Y");
		if($days!=0 && $opt_filter_location!=0)
		{
			$RS3 =  $objDB->Conn->Execute("select 
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'f_17',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'f_18',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'f_25',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'f_45',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'f_55',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'f_65',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'm_17',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'm_18',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'm_25',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'm_45',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'm_55',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'm_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rf_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rf_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rf_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rf_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rf_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rf_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rm_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rm_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rm_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rm_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rm_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rm_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rt_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rt_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rt_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rt_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rt_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rt_65'");

		}
		else // only days!=0 && opt_filter_location==0
		{
			$RS3 =  $objDB->Conn->Execute("select 
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'f_17',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'f_18',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'f_25',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'f_45',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'f_55',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'f_65',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'm_17',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'm_18',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'm_25',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'm_45',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'm_55',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'm_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rf_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rf_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rf_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rf_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rf_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rf_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rm_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rm_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rm_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rm_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rm_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rm_65',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=17) 'rt_17',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=18 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=24) 'rt_18',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=25 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=44) 'rt_25',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=45 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=54) 'rt_45',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=55 and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)<=64) 'rt_55',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.") and (DATE_FORMAT(CURDATE(),'%Y')-dob_year)>=65) 'rt_65'");

		}		
			
			$json_array['status'] = "true";
			
			$f_17 = $RS3->fields['f_17'];
			$f_18 = $RS3->fields['f_18'];
			$f_25 = $RS3->fields['f_25'];
			$f_45 = $RS3->fields['f_45'];
			$f_55 = $RS3->fields['f_55'];
			$f_65 = $RS3->fields['f_65'];
			
			$m_17 = $RS3->fields['m_17'];
			$m_18 = $RS3->fields['m_18'];
			$m_25 = $RS3->fields['m_25'];
			$m_45 = $RS3->fields['m_45'];
			$m_55 = $RS3->fields['m_55'];
			$m_65 = $RS3->fields['m_65'];

			$rf_17 = $RS3->fields['rf_17'];
			$rf_18 = $RS3->fields['rf_18'];
			$rf_25 = $RS3->fields['rf_25'];
			$rf_45 = $RS3->fields['rf_45'];
			$rf_55 = $RS3->fields['rf_55'];
			$rf_65 = $RS3->fields['rf_65'];
			
			$rm_17 = $RS3->fields['rm_17'];
			$rm_18 = $RS3->fields['rm_18'];
			$rm_25 = $RS3->fields['rm_25'];
			$rm_45 = $RS3->fields['rm_45'];
			$rm_55 = $RS3->fields['rm_55'];
			$rm_65 = $RS3->fields['rm_65'];
			
			$rt_17 = $RS3->fields['rt_17'];
			$rt_18 = $RS3->fields['rt_18'];
			$rt_25 = $RS3->fields['rt_25'];
			$rt_45 = $RS3->fields['rt_45'];
			$rt_55 = $RS3->fields['rt_55'];
			$rt_65 = $RS3->fields['rt_65'];
																																				
			$json_array['f_17'] = $f_17;
			$json_array['f_18'] = $f_18;
			$json_array['f_25'] = $f_25;
			$json_array['f_45'] = $f_45;
			$json_array['f_55'] = $f_55;
			$json_array['f_65'] = $f_65;
			
			$json_array['m_17'] = $m_17;
			$json_array['m_18'] = $m_18;
			$json_array['m_25'] = $m_25;
			$json_array['m_45'] = $m_45;
			$json_array['m_55'] = $m_55;
			$json_array['m_65'] = $m_65;
			
			$json_array['rf_17'] = $rf_17;
			$json_array['rf_18'] = $rf_18;
			$json_array['rf_25'] = $rf_25;
			$json_array['rf_45'] = $rf_45;
			$json_array['rf_55'] = $rf_55;
			$json_array['rf_65'] = $rf_65;
			
			$json_array['rm_17'] = $rm_17;
			$json_array['rm_18'] = $rm_18;
			$json_array['rm_25'] = $rm_25;
			$json_array['rm_45'] = $rm_45;
			$json_array['rm_55'] = $rm_55;
			$json_array['rm_65'] = $rm_65;
			
			$json_array['rt_17'] = $rt_17;
			$json_array['rt_18'] = $rt_18;
			$json_array['rt_25'] = $rt_25;
			$json_array['rt_45'] = $rt_45;
			$json_array['rt_55'] = $rt_55;
			$json_array['rt_65'] = $rt_65;
			
			$json = json_encode($json_array);
			echo $json;
			exit();
			
	}
	else
	{
		$json_array['status'] = "false";
		$json_array['message'] = "No data found";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}	
	
}

if(isset($_REQUEST['btn_filter_loyalty_by_time']))
{
	$json_array = array();
	
	//$card_id = 2;
	$card_id = $_REQUEST['card_id'];

	//$merchant_id = 17
	$merchant_id = $_REQUEST['merchant_id'];

	
	if(isset($_REQUEST['opt_filter_location']) && isset($_REQUEST['opt_filter_days'])) // after filter
	{
		$opt_filter_location = $_REQUEST['opt_filter_location'];
		$days = $_REQUEST['opt_filter_days'];
		$y = date("Y");
		if($days!=0 && $opt_filter_location!=0)
		{
			$RS4 =  $objDB->Conn->Execute("select 
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_1',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_2',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_3',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_4',

(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_1',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_2',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_3',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_4',

(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_4',

(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_4',


(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.location_id=".$opt_filter_location." and pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_4'");
		}
		else // only days!=0 && opt_filter_location==0
		{
			$RS4 =  $objDB->Conn->Execute("select 
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_1',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_2',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_3',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'f_4',

(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_1',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_2',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_3',
(select count(*) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'm_4',

(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=2 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rf_4',

(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and cu.gender=1 and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rm_4',


(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=1 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_1',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=2 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_2',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=3 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_3',
(select IFNULL(sum(revenue),0) from customer_loyalty_card_transaction pc,customer_loyalty_reward_card uc,customer_user cu where pc.user_card_id=uc.id and uc.user_id=cu.id and uc.card_id=".$card_id." and day_timezone_id=4 and (DATEDIFF('".date("Y-m-d H:i:s")."',transaction_date ) <= ".$days.")) 'rt_4'");
		}		
			
			$json_array['status'] = "true";
			
			$f_1 = $RS4->fields['f_1'];
			$f_2 = $RS4->fields['f_2'];
			$f_3 = $RS4->fields['f_3'];
			$f_4 = $RS4->fields['f_4'];
			
			$m_1 = $RS4->fields['m_1'];
			$m_2 = $RS4->fields['m_2'];
			$m_3 = $RS4->fields['m_3'];
			$m_4 = $RS4->fields['m_4'];

			$rf_1 = $RS4->fields['rf_1'];
			$rf_2 = $RS4->fields['rf_2'];
			$rf_3 = $RS4->fields['rf_3'];
			$rf_4 = $RS4->fields['rf_4'];
			
			$rm_1 = $RS4->fields['rm_1'];
			$rm_2 = $RS4->fields['rm_2'];
			$rm_3 = $RS4->fields['rm_3'];
			$rm_4 = $RS4->fields['rm_4'];
			
			$rt_1 = $RS4->fields['rt_1'];
			$rt_2 = $RS4->fields['rt_2'];
			$rt_3 = $RS4->fields['rt_3'];
			$rt_4 = $RS4->fields['rt_4'];
																																				
			$json_array['f_1'] = $f_1;
			$json_array['f_2'] = $f_2;
			$json_array['f_3'] = $f_3;
			$json_array['f_4'] = $f_4;
			
			$json_array['m_1'] = $m_1;
			$json_array['m_2'] = $m_2;
			$json_array['m_3'] = $m_3;
			$json_array['m_4'] = $m_4;
			
			$json_array['rf_1'] = $rf_1;
			$json_array['rf_2'] = $rf_2;
			$json_array['rf_3'] = $rf_3;
			$json_array['rf_4'] = $rf_4;
			
			$json_array['rm_1'] = $rm_1;
			$json_array['rm_2'] = $rm_2;
			$json_array['rm_3'] = $rm_3;
			$json_array['rm_4'] = $rm_4;
			
			$json_array['rt_1'] = $rt_1;
			$json_array['rt_2'] = $rt_2;
			$json_array['rt_3'] = $rt_3;
			$json_array['rt_4'] = $rt_4;
			
			$json = json_encode($json_array);
			echo $json;
			exit();
			
	}
	else
	{
		$json_array['status'] = "false";
		$json_array['message'] = "No data found";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}	
	
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ScanFlip | Card Report</title>
	<?php require_once(MRCH_LAYOUT."/head.php"); ?>
	<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap.min.css">
	<!--<link rel="stylesheet" href="<?php //echo ASSETS?>/loyalty/css/reset.css"> -->
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery-ui.css">
	<!--<script src="<?php //echo ASSETS?>/loyalty/js/jquery.js"></script>-->
	<script src="<?php echo ASSETS?>/pricelist/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
	<script src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo ASSETS ?>/pricelist/js/jquery-ui.js"></script>
	<script src="<?= ASSETS_JS ?>/m/highcharts.js"></script>
	<script src="<?= ASSETS_JS ?>/m/drilldown.js"></script>
</head>
<body>
	<!---------start header--------------->	
	
	<div>
	<?
	// include header file from merchant/template directory 
	require_once(MRCH_LAYOUT."/header.php");
	?>
	<!--end header-->
	</div>
	<div id="contentContainer">
		
	<div id="content">	
		<table class="table display" id="card_table">
			<thead>
				<tr>
					<td colspan="5" align="left" class="filter_result table_filter_area" >
						<input type="button" style="float:right;" value="<?php echo $merchant_msg['report']['Campaign_Filter_Button']; ?>" name="btnfilterCards" id="btnfilterCards" /></th>
							<div class="fltr_div_td">
							   <div class="cls_filter">Filter By :</div>
								   <div class="cls_filter_top">
										<b>Year</b>
										<select id="opt_filter_year" >
											<?php
											for ($y = date('Y') - 2; $y <= date('Y'); $y++) 
											{
											?>
												<option value="<?php echo $y; ?>" 
												<?php
												if ($y == date('Y')) 
												{
													echo "selected";
												}
												?>  > <?php echo $y; ?> 
												</option>
											<?php 
											}
											?>
										</select>
										&nbsp;&nbsp;&nbsp;
										<b>Status</b>
										<select id="opt_filter_status" >
											<option value="24" selected="selected" > --- Active or Pause---</option>
											<option value="3"  > --- Expire ---</option>
										</select> 
								   </div>
							  </div>  
					 </td>
				</tr>
			  <tr>
				<th>Card Title</th>
				<th>Date Created</th>
			  </tr>
			</thead>
      </table>
      
		<div id="card_metrics_container" >
			
		</div>
		
		<div id="card_revenue_container" >
			
		</div>
		
		<div id="card_visit_by_age_container" >
			
		</div>
		
		<div id="card_visit_by_time_container" >
			
		</div>
		
		<div id="loyalty_card_metrics">
			<div id="loyalty_card_metrics_filter" style="width: 250px;display:none;">
				<input type="button" style="float:right;" value="<?php echo $merchant_msg['report']['Campaign_Filter_Button']; ?>" name="btnfilterLoyaltyCards" id="btnfilterLoyaltyCards" />
				<select id="opt_filter_days" >
					<option value="0" selected="selected" >Current Year</option>
					<option value="30">30 Days</option>
					<option value="90">90 Days</option>
					<option value="180">6 Month</option>
					<option value="365">1 Year</option>
				</select>
			</div>
			<div id="card_metrics_drilldown_container" >
				
			</div>
		</div>
		<input type="hidden" name="hdncardid" id="hdncardid" />
	</div><!-- end of content-->
	
	</div><!-- end of contentContainer-->
	
	<!---------start footer--------------->
    <div>
	<?
		require_once(MRCH_LAYOUT."/footer.php");
	?>
	<!--end of footer-->
	</div>

</body>
</html>
<script type="text/javascript">
	
	jQuery(document).ready(function(){
		
		/*var oCache = {
                    iCacheLower: -1
                };

                function fnSetKey(aoData, sKey, mValue)
                {
                    for (var i = 0, iLen = aoData.length; i < iLen; i++)
                    {
                        if (aoData[i].name == sKey)
                        {
                            aoData[i].value = mValue;
                        }
                    }
                }

                function fnGetKey(aoData, sKey)
                {
                    for (var i = 0, iLen = aoData.length; i < iLen; i++)
                    {
                        if (aoData[i].name == sKey)
                        {
                            return aoData[i].value;
                        }
                    }
                    return null;
                }

                function fnDataTablesPipeline(sSource, aoData, fnCallback) 
                {
                    var iPipe = 2; // Adjust the pipe size 

                    var bNeedServer = false;
                    var sEcho = fnGetKey(aoData, "sEcho");
                    var iRequestStart = fnGetKey(aoData, "iDisplayStart");
                    var iRequestLength = fnGetKey(aoData, "iDisplayLength");
                    var iRequestEnd = iRequestStart + iRequestLength;
                    oCache.iDisplayStart = iRequestStart;

                    // outside pipeline?
                    if (oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper)
                    {
                        bNeedServer = true;
                    }

                    //sorting etc changed ? 
                    if (oCache.lastRequest && !bNeedServer)
                    {
                        for (var i = 0, iLen = aoData.length; i < iLen; i++)
                        {
                            if (aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho")
                            {
                                if (aoData[i].value != oCache.lastRequest[i].value)
                                {
                                    bNeedServer = true;
                                    break;
                                }
                            }
                        }
                    }

                    // Store the request for checking next time around 
                    oCache.lastRequest = aoData.slice();

                    if (bNeedServer)
                    {
                        if (iRequestStart < oCache.iCacheLower)
                        {
                            iRequestStart = iRequestStart - (iRequestLength * (iPipe - 1));
                            if (iRequestStart < 0)
                            {
                                iRequestStart = 0;
                            }
                        }

                        oCache.iCacheLower = iRequestStart;
                        oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
                        oCache.iDisplayLength = fnGetKey(aoData, "iDisplayLength");
                        fnSetKey(aoData, "iDisplayStart", iRequestStart);
                        fnSetKey(aoData, "iDisplayLength", iRequestLength * iPipe);

                        $.getJSON(sSource, aoData, function (json) {
                            // Callback processing 
                            oCache.lastJson = jQuery.extend(true, {}, json);

                            if (oCache.iCacheLower != oCache.iDisplayStart)
                            {
                                json.aaData.splice(0, oCache.iDisplayStart - oCache.iCacheLower);
                            }
                            json.aaData.splice(oCache.iDisplayLength, json.aaData.length);

                            fnCallback(json)
                        });
                    }
                    else
                    {
                        json = jQuery.extend(true, {}, oCache.lastJson);
                        json.sEcho = sEcho; // Update the echo for each response 
                        json.aaData.splice(0, iRequestStart - oCache.iCacheLower);
                        json.aaData.splice(iRequestLength, json.aaData.length);
                        fnCallback(json);
                        return;
                    }
                }
                
	  //data table function on pricelist index page table
		var oTable = $('#card_table').dataTable( {
			//"bStateSave": true,
		   "bFilter": false,
			"bSort" : false,
			"bLengthChange": false,
			"info": false,
			"iDisplayLength": 10,
			"bProcessing": true,
			 "bServerSide": true,
			"oLanguage": {
							"sEmptyTable": "No card founds in the system. Please add at least one.",
							"sZeroRecords": "No card to display",
							"sProcessing": "Loading..."
						},
			 "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",            
			 "fnServerParams": function (aoData) {
							aoData.push({"name": "btnGetAllCardlistReport", "value": true},{"name": "status", "value": jQuery('#opt_filter_status').val()},{"name": "year", "value": jQuery('#opt_filter_year').val()} )
							//bind_more_action_click();
						},
			 //"fnServerData": fnDataTablesPipeline,     
			 
			 "aoColumns": [
							{"bVisible": true, "bSearchable": false, "bSortable": false},
							{"bVisible": true, "bSearchable": false, "bSortable": false}
						]      
						
		} ); */
		
		jQuery("#btnfilterCards").click(function(){
	  
			 jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'loginornot=true',
				async:false,
				success:function(msg)
				{
					var obj = jQuery.parseJSON(msg);
					if (obj.status=="false")     
					{
						window.location.href=obj.link;
					}
					else
					{
						oTable.fnDraw();		
					}
				}
			});
		});
		  
		jQuery(".getanalytics").live("click",function(){
			
			var card_id = jQuery(this).attr('cardid');
			jQuery("#hdncardid").val(card_id);
			var active = jQuery(this).attr('active');
			var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
			//alert(card_id);			
			jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'loginornot=true',
				async:false,
				success:function(msg)
				{
					var obj = jQuery.parseJSON(msg);
					if (obj.status=="false")     
					{
						window.location.href=obj.link;
					}
					else
					{
						
						jQuery.ajax({
							  type: "POST", // HTTP method POST or GET
							  url: "card_report.php", //Where to make Ajax calls
							  data:"btn_filter_loyalty_card_metrics=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&active="+active,
							  success:function(response){
								
								var obj = jQuery.parseJSON(response);
								if (obj.status=="true") 
								{
									// start drill down report
									
									
									if(active==1)
									{
										jQuery("#loyalty_card_metrics_filter").css("display","block");
									}
									else
									{
										jQuery("#loyalty_card_metrics_filter").css("display","none");
									}
									
									var total_cards_activated=parseInt(obj.total_cards_activated);
									var active_male=parseInt(obj.total_male_activated_cards);
									var active_female=parseInt(obj.total_female_activated_cards);
									var active_male_desktop=parseInt(obj.total_male_activated_desktop_cards);
									var active_male_mobile=parseInt(obj.total_male_activated_mobile_cards);
									var active_male_qrcode=parseInt(obj.total_male_activated_qr_cards);
									var active_female_desktop=parseInt(obj.total_female_activated_desktop_cards);
									var active_female_mobile=parseInt(obj.total_female_activated_mobile_cards);
									var active_female_qrcode=parseInt(obj.total_female_activated_qr_cards);
									
									var total_cards_rewarded=parseInt(obj.total_cards_rewarded);
									var reward_male=parseInt(obj.total_male_rewarded_cards);
									var reward_female=parseInt(obj.total_female_rewarded_cards);
									var reward_male_desktop=parseInt(obj.total_male_rewarded_desktop_cards);
									var reward_male_mobile=parseInt(obj.total_male_rewarded_mobile_cards);
									var reward_male_qrcode=parseInt(obj.total_male_rewarded_qr_cards);
									var reward_female_desktop=parseInt(obj.total_female_rewarded_desktop_cards);
									var reward_female_mobile=parseInt(obj.total_female_rewarded_mobile_cards);
									var reward_female_qrcode=parseInt(obj.total_female_rewarded_qr_cards);
									
									var total_cards_deleted=parseInt(obj.total_cards_deleted);
									var delete_male=parseInt(obj.total_male_deleted_cards);
									var delete_female=parseInt(obj.total_female_deleted_cards);	
									var delete_male_desktop=parseInt(obj.total_male_deleted_desktop_cards);
									var delete_male_mobile=parseInt(obj.total_male_deleted_mobile_cards);
									var delete_male_qrcode=parseInt(obj.total_male_deleted_qr_cards);
									var delete_female_desktop=parseInt(obj.total_female_deleted_desktop_cards);
									var delete_female_mobile=parseInt(obj.total_female_deleted_mobile_cards);
									var delete_female_qrcode=parseInt(obj.total_female_deleted_qr_cards);
									
									
									try
									{
									// Create the chart
									
									jQuery('#card_metrics_drilldown_container').highcharts({
										chart: {
											type: 'column'
										},
										title: {
											text: 'Card Metrics'
										},
										subtitle: {
											text: 'Click the columns to view detail result'
										},
										xAxis: {
											type: 'category',
										},
										yAxis: {
											title: {
												text: 'Total Cards'
											}
										},
										legend: {
											enabled: true
										},
										plotOptions: {
											series: {
												borderWidth: 0,
												dataLabels: {
													enabled: true,
													format: '{point.y:.1f}'
												}
											}
										},
										
										tooltip: {
											headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
											pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
										},
										
										series: [{
											name: 'Loyalty Card Metrics',
											colorByPoint: true,
											data: [{
														name: 'Total Cards Activated',
														y: total_cards_activated,
														drilldown: 'total_cards_activated'
													},
													{
														name: 'Total Cards Rewarded',
														y: total_cards_rewarded,
														drilldown: 'total_cards_rewarded'
													},
													{
														name: 'Total Cards Deleted',
														y: total_cards_deleted,
														drilldown: 'total_cards_deleted'
													},
													]
										}],
										drilldown: {
											series: [{
														id: 'total_cards_activated',
														name: 'Total Cards Activated',
														data: [{
																name: 'Male',
																y: active_male,
																drilldown: 'active_male'
																},
																{
																name: 'Female',
																y: active_female,
																drilldown: 'active_female'
																}
															]
													},
													{
														id: 'total_cards_rewarded',
														name: 'Total Cards Rewarded',
														data: [{
																name: 'Male',
																y: reward_male,
																drilldown: 'reward_male'
																},
																{
																name: 'Female',
																y: reward_female,
																drilldown: 'reward_female'
																}
															]
													},
													{
														id: 'total_cards_deleted',
														name: 'Total Cards Deleted',
														data: [{
																name: 'Male',
																y: delete_male,
																drilldown: 'delete_male'
																},
																{
																name: 'Female',
																y: delete_female,
																drilldown: 'delete_female'
																}
															]
													}, 
													{

														id: 'active_male',
														name: 'Total Cards Activated By Male',
														data: [['Desktop',active_male_desktop],
															   ['Mobile Device',active_male_mobile],
															   ['QR Code Scan',active_male_qrcode]]
													}, 
													{

														id: 'active_female',
														name: 'Total Cards Activated By Female',
														data: [['Desktop',active_female_desktop],
															   ['Mobile Device',active_female_mobile],
															   ['QR Code Scan',active_female_qrcode]]
													}, 
													{

														id: 'reward_male',
														name: 'Total Cards Rewarded By Male',
														data: [['Desktop',reward_male_desktop],
															   ['Mobile Device',reward_male_mobile],
															   ['QR Code Scan',reward_male_qrcode]]
													}, 
													{

														id: 'reward_female',
														name: 'Total Cards Rewarded By Female',
														data: [['Desktop',reward_female_desktop],
															   ['Mobile Device',reward_female_mobile],
															   ['QR Code Scan',reward_female_qrcode]]
													}, 
													{

														id: 'delete_male',
														name: 'Total Cards Deleted By Male',
														data: [['Desktop',delete_male_desktop],
															   ['Mobile Device',delete_male_mobile],
															   ['QR Code Scan',delete_male_qrcode]]
													}, 
													{

														id: 'delete_female',
														name: 'Total Cards Deleted By Female',
														data: [['Desktop',delete_female_desktop],
															   ['Mobile Device',delete_female_mobile],
															   ['QR Code Scan',delete_female_qrcode]]
													}]
										}
									});   
									
									// end drill down report
									}
									catch(e)
									{
										console.log(e);
									}
								}
								else
								{
									//alert(obj.message);
									jQuery('#card_metrics_drilldown_container').html(obj.message);
								}	
								
							  },
							  error:function (xhr, ajaxOptions, thrownError){
								  alert(thrownError);
							  }
						});
					}
				}
			});
			
		});
	
		jQuery("#btnfilterLoyaltyCards").live("click",function(){
			var opt_filter_days = jQuery("#opt_filter_days").val();
			//alert(opt_filter_days);
			
			jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'loginornot=true',
				async:false,
				success:function(msg)
				{
					var obj = jQuery.parseJSON(msg);
					if (obj.status=="false")     
					{
						window.location.href=obj.link;
					}
					else
					{
					
						var card_id = jQuery("#hdncardid").val();
						var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
						
							jQuery.ajax({
								  type: "POST", // HTTP method POST or GET
								  url: "card_report.php", //Where to make Ajax calls
								  data:"btn_filter_loyalty_card_metrics=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_days="+opt_filter_days,
								  success:function(response){
									
									var obj = jQuery.parseJSON(response);
									if (obj.status=="true") 
									{
										// start drill down report
										
										
										var total_cards_activated=parseInt(obj.total_cards_activated);
										var active_male=parseInt(obj.total_male_activated_cards);
										var active_female=parseInt(obj.total_female_activated_cards);
										var active_male_desktop=parseInt(obj.total_male_activated_desktop_cards);
										var active_male_mobile=parseInt(obj.total_male_activated_mobile_cards);
										var active_male_qrcode=parseInt(obj.total_male_activated_qr_cards);
										var active_female_desktop=parseInt(obj.total_female_activated_desktop_cards);
										var active_female_mobile=parseInt(obj.total_female_activated_mobile_cards);
										var active_female_qrcode=parseInt(obj.total_female_activated_qr_cards);
										
										var total_cards_rewarded=parseInt(obj.total_cards_rewarded);
										var reward_male=parseInt(obj.total_male_rewarded_cards);
										var reward_female=parseInt(obj.total_female_rewarded_cards);
										var reward_male_desktop=parseInt(obj.total_male_rewarded_desktop_cards);
										var reward_male_mobile=parseInt(obj.total_male_rewarded_mobile_cards);
										var reward_male_qrcode=parseInt(obj.total_male_rewarded_qr_cards);
										var reward_female_desktop=parseInt(obj.total_female_rewarded_desktop_cards);
										var reward_female_mobile=parseInt(obj.total_female_rewarded_mobile_cards);
										var reward_female_qrcode=parseInt(obj.total_female_rewarded_qr_cards);
										
										var total_cards_deleted=parseInt(obj.total_cards_deleted);
										var delete_male=parseInt(obj.total_male_deleted_cards);
										var delete_female=parseInt(obj.total_female_deleted_cards);	
										var delete_male_desktop=parseInt(obj.total_male_deleted_desktop_cards);
										var delete_male_mobile=parseInt(obj.total_male_deleted_mobile_cards);
										var delete_male_qrcode=parseInt(obj.total_male_deleted_qr_cards);
										var delete_female_desktop=parseInt(obj.total_female_deleted_desktop_cards);
										var delete_female_mobile=parseInt(obj.total_female_deleted_mobile_cards);
										var delete_female_qrcode=parseInt(obj.total_female_deleted_qr_cards);
										
										
										try
										{
										// Create the chart
										
										jQuery('#card_metrics_drilldown_container').highcharts({
											chart: {
												type: 'column'
											},
											title: {
												text: 'Card Metrics'
											},
											subtitle: {
												text: 'Click the columns to view detail result'
											},
											xAxis: {
												type: 'category',
											},
											yAxis: {
												title: {
													text: 'Total Cards'
												}
											},
											legend: {
												enabled: true
											},
											plotOptions: {
												series: {
													borderWidth: 0,
													dataLabels: {
														enabled: true,
														format: '{point.y:.1f}'
													}
												}
											},
											
											tooltip: {
												headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
												pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
											},
											
											series: [{
												name: 'Loyalty Card Metrics',
												colorByPoint: true,
												data: [{
															name: 'Total Cards Activated',
															y: total_cards_activated,
															drilldown: 'total_cards_activated'
														},
														{
															name: 'Total Cards Rewarded',
															y: total_cards_rewarded,
															drilldown: 'total_cards_rewarded'
														},
														{
															name: 'Total Cards Deleted',
															y: total_cards_deleted,
															drilldown: 'total_cards_deleted'
														},
														]
											}],
											drilldown: {
												series: [{
															id: 'total_cards_activated',
															name: 'Total Cards Activated',
															data: [{
																	name: 'Male',
																	y: active_male,
																	drilldown: 'active_male'
																	},
																	{
																	name: 'Female',
																	y: active_female,
																	drilldown: 'active_female'
																	}
																]
														},
														{
															id: 'total_cards_rewarded',
															name: 'Total Cards Rewarded',
															data: [{
																	name: 'Male',
																	y: reward_male,
																	drilldown: 'reward_male'
																	},
																	{
																	name: 'Female',
																	y: reward_female,
																	drilldown: 'reward_female'
																	}
																]
														},
														{
															id: 'total_cards_deleted',
															name: 'Total Cards Deleted',
															data: [{
																	name: 'Male',
																	y: delete_male,
																	drilldown: 'delete_male'
																	},
																	{
																	name: 'Female',
																	y: delete_female,
																	drilldown: 'delete_female'
																	}
																]
														}, 
														{

															id: 'active_male',
															name: 'Total Cards Activated By Male',
															data: [['Desktop',active_male_desktop],
																   ['Mobile Device',active_male_mobile],
																   ['QR Code Scan',active_male_qrcode]]
														}, 
														{

															id: 'active_female',
															name: 'Total Cards Activated By Female',
															data: [['Desktop',active_female_desktop],
																   ['Mobile Device',active_female_mobile],
																   ['QR Code Scan',active_female_qrcode]]
														}, 
														{

															id: 'reward_male',
															name: 'Total Cards Rewarded By Male',
															data: [['Desktop',reward_male_desktop],
																   ['Mobile Device',reward_male_mobile],
																   ['QR Code Scan',reward_male_qrcode]]
														}, 
														{

															id: 'reward_female',
															name: 'Total Cards Rewarded By Female',
															data: [['Desktop',reward_female_desktop],
																   ['Mobile Device',reward_female_mobile],
																   ['QR Code Scan',reward_female_qrcode]]
														}, 
														{

															id: 'delete_male',
															name: 'Total Cards Deleted By Male',
															data: [['Desktop',delete_male_desktop],
																   ['Mobile Device',delete_male_mobile],
																   ['QR Code Scan',delete_male_qrcode]]
														}, 
														{

															id: 'delete_female',
															name: 'Total Cards Deleted By Female',
															data: [['Desktop',delete_female_desktop],
																   ['Mobile Device',delete_female_mobile],
																   ['QR Code Scan',delete_female_qrcode]]
														}]
											}
										});   
										
										// end drill down report
										}
										catch(e)
										{
											console.log(e);
										}
									}
									else
									{
										//alert(obj.message);
										jQuery('#card_metrics_drilldown_container').html(obj.message);
									}	
									
								  },
								  error:function (xhr, ajaxOptions, thrownError){
									  alert(thrownError);
								  }
							});
						
					}
				}
			});
		});
	});
	
	
	
jQuery(function () {
	
	var categories = ['Total Cards Activated','Total Cards Rewarded','Total Cards Deleted'];
	var total_cards_activated=20;
	var total_cards_rewarded=10;
	var total_cards_deleted=2;
	
	jQuery('#card_metrics_container').highcharts({
        chart: {
            type: 'column'
        },
        credits: {
			enabled: false
		},
        title: {
            text: 'Card Metrics'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: categories,
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total Cards'
            }
        },
        tooltip: {
			formatter: function () {
					return this.key+" = "+this.y				
			}
		},
        legend: {
            enabled: true
        },        
        series: [{
            name: 'Loyalty Card Metrics',
            color: '#4F81BD',
            data: [
                [total_cards_activated],
                [total_cards_rewarded],
                [total_cards_deleted]
            ],
            
        }]
    });
    
    var categories = ['Total Revenue','Average Revenue per visit','Average  Revenue per location','Total Loyalty Card Campaign Cost'];
    var total_revenue=400;
    var average_revenue_per_visit=22;
    var average_revenue_per_location=75;
    var total_loyalty_card_campaign_cost=45;
    
    jQuery('#card_revenue_container').highcharts({
        chart: {
            type: 'column'
        },
        credits: {
			enabled: false
		},
        title: {
            text: 'Revenue & Cost Summary'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: categories,
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Revenue'
            }
        },
        tooltip: {
			formatter: function () {
					return this.key+" = "+this.y				
			}
		},
        legend: {
            enabled: true
        },        
        series: [{
            name: 'Revenue Metrics',
            color: '#4F81BD',
            data: [
                [total_revenue],
                [average_revenue_per_visit],
                [average_revenue_per_location],
                [total_loyalty_card_campaign_cost]
            ],
            
        }]
    });
    
    var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
    var female_visit = [10,23,12,13,6,5];
    var male_visit = [15,22,18,7,4,3];
    var revenue_female = [75,97,25,30,16,15];
    var revenue_male = [125,78,40,65,24,20];
    var revenue_total = [200,175,65,95,40,35];
    
    jQuery('#card_visit_by_age_container').highcharts({
        title: {
            text: 'Customer Visit & Revenue Metrics By Age',
            x: -20 //center
        },
        subtitle: {
            text: '',
            x: -20
        },
        credits: {
			enabled: false
		},
        xAxis:{
			title: {
                text: 'Customer Age'
            },
            categories: categories
        },
        yAxis:[{
            title: {
                text: 'Customer Visits'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },{// mirror axis on right side
			title: {
                text: 'Revenue'
            },
            opposite: true,
			reversed: false,
			linkedTo: 0
		}],       
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: [{
            name: 'Female Visit',
            color: '#9BBB59',
            type: 'column',
            data: female_visit
        },{
            name: 'Male Visit',
			color: '#C0504D',
			type: 'column',
            data: male_visit
        },{
            name: 'Revenue Female',
            color: '#F59240',
			type: 'spline',
            data: revenue_female
        },{
            name: 'Revenue Male',
            color: '#46AAC4',
			type: 'spline',
            data: revenue_male
        },{
            name: 'Total Revenue',
            color: '#7D5FA0',
			type: 'spline',
            data: revenue_total
        }]
    });
    
    var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
    var female_visit = [10,20,15,5];
    var male_visit = [10,20,15,5];
    var revenue_female = [43,33,55,15];
    var revenue_male = [22,42,40,10];
    var revenue_total = [65,75,95,25];
    
    jQuery('#card_visit_by_time_container').highcharts({
        title: {
            text: 'Customer Visit & Revenue Metrics By Time',
            x: -20 //center
        },
        subtitle: {
            text: '',
            x: -20
        },
        credits: {
			enabled: false
		},
        xAxis:{
			title: {
                text: 'Time'
            },
            categories: categories
        },
        yAxis:[{
            title: {
                text: 'Customer Visits'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },{// mirror axis on right side
			title: {
                text: 'Revenue'
            },
            opposite: true,
			reversed: false,
			linkedTo: 0
		}],       
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: [{
            name: 'Female Visit',
            color: '#7E629F',
            type: 'column',
            data: female_visit
        },{
            name: 'Male Visit',
			color: '#9ABA59',
			type: 'column',
            data: male_visit
        },{
            name: 'Revenue Female',
            color: '#F59240',
			type: 'spline',
            data: revenue_female
        },{
            name: 'Revenue Male',
            color: '#46AAC4',
			type: 'spline',
            data: revenue_male
        },{
            name: 'Total Revenue',
            color: '#7D5FA0',
			type: 'spline',
            data: revenue_total
        }]
    });
    
    
});
</script>
