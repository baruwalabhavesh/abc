<?php
header('Content-type: text/html; charset=utf-8');
//require_once("/var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/Config.Inc.php");
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objJSON = new JSON();
//$objDB = new DB();

$array_where_as = array();
$array_where_as['id'] = 11;
$RS = $objDB->Show("admin_settings", $array_where_as);
if($RS->RecordCount()>0)
{
	//echo $RS->fields['action'];

	if($RS->fields['action']==0)
	{	
		$array_values_as = $array_where_as = array();
		$array_values_as['action'] = 1;
		$array_where_as['id'] = 11;
		$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
$where_clause = $array_values = array();
$array = $json_array = $where_clause = array();

//$where_clause['id'] = 99;
$RS_users = $objDB->Show("customer_user", $where_clause);
if($RS_users->RecordCount()>0)
{
	$location_change_flag=0;
	while($Row_user = $RS_users->FetchRow())
	{ 
		//$str = $Row_user['id']."-".$Row_user['curr_latitude']."-".$Row_user['curr_longitude'];
		//echo $str."<br/>";
		
		$array_where = array();
		$array_where['customer_id'] = $Row_user['id'];
		$array_where['notification_type_id'] = 1;
		$objDB->Remove("notification", $array_where);
		
		$array_where = array();
		$array_where['customer_id'] = $Row_user['id'];
		$array_where['notification_type_id'] = 2;
		$objDB->Remove("notification", $array_where);
		
		$array_where = array();
		$array_where['customer_id'] = $Row_user['id'];
		$array_where['notification_type_id'] = 4;
		$objDB->Remove("notification", $array_where);
		
		$array_where = array();
		$array_where['customer_id'] = $Row_user['id'];
		$array_where['notification_type_id'] = 5;
		$objDB->Remove("notification", $array_where);
			
			/*
			echo "=================================================";
			echo "<br/>"."customer id=". $Row_user['id']."<br/>";	
			echo "---------------------------------------------------------------------------------------------------------------------------------------------";
			echo "<br/>";
			*/
			
			$date_wh = " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN  c.start_date AND c.expiration_date and cl.active=1"; 
			

			$customer_id =$Row_user['id'];
			$cust_where = " and (c.id  in  (select cg.campaign_id  from  merchant_subscribs ms inner join  campaign_groups cg on ms.group_id =cg.group_id  inner join merchant_groups mg on mg.id=ms.group_id  where  ms.user_id=".$customer_id.") or c.level =1 ) and ((c.id not in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id)) or (c.id  in ( select campaign_id from customer_campaigns where customer_id = $customer_id and location_id=cl.location_id and activation_status =0)  ))";
			$cat_str = "";
			$id= $customer_id;
				 
			$limit_data1 = "SELECT distinct c.id cid,c.number_of_use,location_id locid 
					FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id
				 WHERE l.active = 1 and 
				 (c.id in (select cg.campaign_id from merchant_subscribs ms inner join campaign_groups cg on ms.group_id =cg.group_id where ms.user_id=$id)) 
				and c.id  in ( select campaign_id from customer_campaigns where customer_id = $id and location_id=cl.location_id and activation_status=1)
				  ".$cat_str  ."  ".$date_wh." and 
				 CONVERT_TZ(c.expiration_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
				CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
				 CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY"; 	 

			//echo $limit_data1;				
			$RS_limit_data1=$objDB->Conn->Execute($limit_data1);
			$cnt=0;
			$camp_ary=array();
			if($RS_limit_data1->RecordCount()>0)
			{
			
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
							//array_push($camp_ary,$Row['cid']);
							//$records[$cnt] = get_field_value($Row);
							//echo $Row['cid'];
							
							$cnt++;
						}
					}
				}
				/*
				echo "<table border=2>";
				echo "<tr>";
				echo "<thead>";
				echo "<td>notification</td><td>type</td>";	
				echo "</thead>";
				echo "<tbody>";
				
				echo "<tr>";
				
				echo "<td>";
				if($cnt==1)
				{
					echo $cnt." campaign reserved by you is expiring today.";
				}
				else
				{
					echo $cnt." campaigns reserved by you are expiring today.";
				}			
				*/
				// add expire_today notification of user
				
				if($cnt>0)
				{
					$array = array();
					$array['notification_type_id'] = 1; 
					$array['counter'] = $cnt; 
					$array['is_read'] = 0;
					$array['customer_id'] = $customer_id;
					$array['current_location'] = $Row_user['current_location'];
					$array['last_updated_time'] = date("Y-m-d H:i:s");
					$objDB->Insert($array, "notification");
				}
				/*
				echo "</td>";
							
				echo "<td>";
				echo "expire_today";
				echo "</td>";
				
				echo "</tbody>";
				echo "</table>";			
				*/
			}
			
			/*
			$limit_data = "SELECT distinct c.id campid
	FROM campaign_location cl inner join campaigns c on cl.campaign_id =c.id inner join locations l on l.id = cl.location_id WHERE l.active = 1 and c.is_walkin <> 1 ".$cat_str."   
	 AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) BETWEEN c.start_date AND c.expiration_date and cl.active=1 and cl.offers_left>0 
	 and CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))  between 
	CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) and 
	 CONVERT_TZ(c.start_date,'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) + INTERVAL 1 DAY ";
			*/
 
			if($Row_user['curr_latitude']!="" && $Row_user['curr_longitude']!="")
			{
				$mlatitude = $Row_user['curr_latitude'];
				$mlongitude = $Row_user['curr_longitude'];
				$dismile = 50;
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
		 
				//echo "<br/>".$limit_data."<br/>";
				$RS_limit_data=$objDB->Conn->Execute($limit_data);
				$cnt=0;
				$camp_ary=array();
				if($RS_limit_data->RecordCount()>0)
				{
					/*
					echo "<table border=2>";
					echo "<tr>";
					echo "<thead>";
					echo "<td>notification</td><td>type</td>";	
					echo "</thead>";
					echo "<tbody>";
					
					echo "<tr>";
					
					echo "<td>";
					if($RS_limit_data->RecordCount()==1)
					{
						echo "Today ".$RS_limit_data->RecordCount()." new campaign is launched by your subscribe merchant.";
					}
					else
					{
						echo "Today ".$RS_limit_data->RecordCount()." new campaigns were launched by your subscribe merchants.";
					}
					*/
					// add new_campaign notification of user

					$array = array();
					$array['notification_type_id'] = 2; 
					$array['counter'] = $RS_limit_data->RecordCount(); 
					$array['is_read'] = 0;
					$array['customer_id'] = $customer_id;
					$array['current_location'] = $Row_user['current_location'];
					$array['last_updated_time'] = date("Y-m-d H:i:s");
					$objDB->Insert($array, "notification");

					/*
					echo "</td>";
								
					echo "<td>";
					echo "new_campaign";
					echo "</td>";
					
					echo "</tbody>";
					echo "</table>";
					*/
				}	
			}
			
			$Sql = "Select (select redeem_merchant_id from coupon_redeem where id=max(cr.id)   ) last_redeem_employee , rw.reward_date ,l.*,c.* , rw.campaign_id campaign_id, rw.location_id location_id 
			   , rw.coupon_code_id coupon_id ,l.id location_id  , max(redeem_date) last_redeemdate 
			 from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id 
			left outer join coupon_redeem cr on cr.coupon_id = rw.coupon_code_id where rw.customer_id = ".$id." 
			and DATEDIFF(NOW(),if(cr.redeem_date IS NULL,rw.reward_date,cr.redeem_date)) <=30 and review_rating_visibility=1 
			group by rw.campaign_id , rw.location_id , rw.customer_id order by last_redeemdate";
			
			$RS_pending_review = $objDB->Conn->Execute($Sql);
			//echo "user =".$id." RS_pending_review = ".$RS_pending_review->RecordCount()."<br/>";
			if($RS_pending_review->RecordCount()>0)
			{
				/*
				echo "<table border=2>";
				echo "<tr>";
				echo "<thead>";
				echo "<td>notification</td><td>type</td>";	
				echo "</thead>";
				echo "<tbody>";
				
				echo "<tr>";
				
				echo "<td>";
				if($RS_pending_review->RecordCount()==1)
				{
					echo "You have ".$RS_pending_review->RecordCount()." pending review for your recent visit.";
				}
				else
				{
					echo "You have ".$RS_pending_review->RecordCount()." pending reviews for your recent visits.";
				}
				*/
				// add pending_review notification of user
				

				$Sql_pending_previous = "SELECT * FROM notification Where notification_type_id=3 and customer_id = ".$Row_user['id']; 
				$RS_pending_previous = $objDB->Conn->Execute($Sql_pending_previous);
				$prev_total_pending = 0;
				
				if($RS_pending_previous->RecordCount()>0)
				{
					while($Row_pending_previous = $RS_pending_previous->FetchRow())
					{
						$prev_total_pending=$Row_pending_previous['counter'];
					}
				}
				else
				{
					$prev_total_pending=0;
				}
				//echo "prev_total_pending = ".$prev_total_pending."<br/>";
				
				$array_where = array();
				$array_where['customer_id'] = $Row_user['id'];
				$array_where['notification_type_id'] = 3;
				$objDB->Remove("notification", $array_where);
		
				if($RS_pending_review->RecordCount()!=$prev_total_pending)
				{
					$array = array();
					$array['notification_type_id'] = 3; 
					$array['counter'] = $RS_pending_review->RecordCount(); 
					$array['is_read'] = 0;
					$array['customer_id'] = $customer_id;
					$array['current_location'] = $Row_user['current_location'];
					$array['last_updated_time'] = date("Y-m-d H:i:s");
					$objDB->Insert($array, "notification");
				}
				
				/*	
				echo "</td>";
							
				echo "<td>";
				echo "pending_review";
				echo "</td>";
				
				echo "</tbody>";
				echo "</table>";
				*/
			}
			
			// earned scanflip points for recent visit total of scanflip merchants
			
			$limit_data = "SELECT sum(c.redeem_rewards) redeem_values
FROM `coupon_redeem` cr,
 coupon_codes cc inner join locations l on l.id = cc.location_id  inner join campaigns c on c.id = cc.customer_campaign_code inner join merchant_user mu on mu.id=l.created_by
where cr.coupon_id= cc.id and
CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) 
between cr.redeem_date and cr.redeem_date + INTERVAL 1 DAY 
and cc.customer_id=".$customer_id;
			
			$RS_earned_for_visit = $objDB->Conn->Execute($limit_data);
			if($RS_earned_for_visit->RecordCount()>0)
			{
				/*
				echo "<table border=2>";
				echo "<tr>";
				echo "<thead>";
				echo "<td>notification</td><td>type</td>";	
				echo "</thead>";
				echo "<tbody>";
				
				echo "<tr>";
				
				echo "<td>";
				
				if($RS_earned_for_visit->fields['redeem_values']==1)
				{
					echo "You earned ".$RS_earned_for_visit->fields['redeem_values']." scanflip point from scanflip merchant in last 24 hour.";					
				}
				else
				{
					echo "You earned ".$RS_earned_for_visit->fields['redeem_values']." scanflip points from scanflip merchants in last 24 hour.";
				}
				*/
				// add earned_for_visit notification of user
				
				if($RS_earned_for_visit->fields['redeem_values']==0)
				{	
					/*
					$array = array();
					$array['notification_type_id'] = 4; 
					$array['counter'] = $RS_earned_for_visit->fields['redeem_values']; 
					$array['is_read'] = 1;
					$array['customer_id'] = $customer_id;
					$array['current_location'] = $Row_user['current_location'];
					$array['last_updated_time'] = date("Y-m-d H:i:s");
					$objDB->Insert($array, "notification");
					*/
				}
				else
				{
					$array = array();
					$array['notification_type_id'] = 4; 
					$array['counter'] = $RS_earned_for_visit->fields['redeem_values']; 
					$array['is_read'] = 0;
					$array['customer_id'] = $customer_id;
					$array['current_location'] = $Row_user['current_location'];
					$array['last_updated_time'] = date("Y-m-d H:i:s");
					$objDB->Insert($array, "notification");
				}
				/*
				echo "</td>";
							
				echo "<td>";
				echo "point_for_visit";
				echo "</td>";
				
				echo "</tbody>";
				echo "</table>";
				*/
			}
			
			
			// earned scanflip points for new customer referral total of scanflip merchants
			
			$limit_data = "select sum(rw.referral_reward) referral_points 
from reward_user rw inner join campaigns c on c.id=rw.campaign_id inner join locations l on l.id=rw.location_id  
inner join merchant_user mu on l.created_by=mu.id 
where CONVERT_TZ(now(),'".CURR_TIMEZONE."',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1))
 between rw.reward_date and rw.reward_date + INTERVAL 1 DAY 
and rw.customer_id=".$customer_id;
			//echo $limit_data;
			$RS_earned_for_referral = $objDB->Conn->Execute($limit_data);
			if($RS_earned_for_referral->RecordCount()>0)
			{
				/*
				echo "<table border=2>";
				echo "<tr>";
				echo "<thead>";
				echo "<td>notification</td><td>type</td>";	
				echo "</thead>";
				echo "<tbody>";
				
				echo "<tr>";
				
				echo "<td>";

				if($RS_earned_for_referral->fields['referral_points']==1)
				{
					echo "You earned ".$RS_earned_for_referral->fields['referral_points']." scanflip point for new customer referral in last 24 hour.
					Share campaigns and earn referral reward points from scanflip merchants.";					
				}
				else
				{
					echo "You earned ".$RS_earned_for_referral->fields['referral_points']." scanflip points for new customer referral in last 24 hour.
					Share campaigns and earn referral reward points from scanflip merchants.";		
				}
				*/
				// add earned_for_visit notification of user
				
				if($RS_earned_for_referral->fields['referral_points']==0)
				{	
					/*
					$array = array();
					$array['notification_type_id'] = 5; 
					$array['counter'] = $RS_earned_for_referral->fields['referral_points']; 
					$array['is_read'] = 1;
					$array['customer_id'] = $customer_id;
					$array['current_location'] = $Row_user['current_location'];
					$array['last_updated_time'] = date("Y-m-d H:i:s");
					$objDB->Insert($array, "notification");
					*/
				}
				else
				{
					$array = array();
					$array['notification_type_id'] = 5; 
					$array['counter'] = $RS_earned_for_referral->fields['referral_points']; 
					$array['is_read'] = 0;
					$array['customer_id'] = $customer_id;
					$array['current_location'] = $Row_user['current_location'];
					$array['last_updated_time'] = date("Y-m-d H:i:s");
					$objDB->Insert($array, "notification");
				}
				/*
				echo "</td>";
							
				echo "<td>";
				echo "point_for_referral";
				echo "</td>";
				
				echo "</tbody>";
				echo "</table>";
				*/
			}
	}
}



	}	
	
	$array_values_as = $array_where_as = array();
	$array_values_as['action'] = 0;
	$array_where_as['id'] = 11;
	$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
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
?>
