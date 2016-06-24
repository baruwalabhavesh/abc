<?php

header('Content-type: text/html; charset=utf-8');
//require_once("../classes/Config.Inc.php");
include_once(SERVER_PATH . "/classes/DB.php");
//$objDB = new DB();
$conversation_rate =0.01;
$unknown_gender = 0;
  $male_gender = 0;
  $female_gender = 0;
  $coustomer_counter_1 = 0 ;
  $coustomer_counter_2 = 0;
     $arr_age = array();
	 $array_where = array();
$array_where['merchant_parent'] = 0;
	  $RS = $objDB->Show("merchant_user",$array_where);
//echo 123;
//if (isset($_REQUEST['locationchartdata'])) {
$month =$_REQUEST['month'];
if ($month == 4 || $month == 6 || $month == 9 || $month == 11) {
//echo "in if";
            $m = 30;
        } else if ($month == 2) {
		//echo "in else if";
            $m = 28;
        } else {
		//echo "in else";
            $m = 31;
        }
		if($_REQUEST['year'] != 0)
		{
			$year =$_REQUEST['year'];
			if($month != 0)
			{
				$from_date = $year . "-" . $month . "-01 00:00:00";
				$to_date = $year . "-" . $month . "-" . $m . " 23:59:59";
				$redeem_date =  " and (cr.redeem_date between '".$from_date."' and '".$to_date."')";
				$redeem_date2 =  " and (reward_date between '".$from_date."' and '".$to_date."')";
				$redeem_date3=  "  (reward_date between '".$from_date."' and '".$to_date."')";
			}
			else
			{
				$from_date = $year . "-01-01 00:00:00";
				$to_date = $year . "-12-" . $m . " 23:59:59";
				$redeem_date =  " and (cr.redeem_date between '".$from_date."' and '".$to_date."')";
				$redeem_date2 =  " and (reward_date between '".$from_date."' and '".$to_date."')";
				$redeem_date3=  "  (reward_date between '".$from_date."' and '".$to_date."')";
			}
		}
		else
		{
			$redeem_date= "";
		}	
		
   ?>
   <?php
		 
			$sql = "SELECT sum(transaction_fees_price) as total_transaction_fees , sum(cr.transaction_fees) as total_transaction_fees_point ,
			c.redeem_rewards,count(*) no_of_records ,sum(redeem_value) as total_revenue
					FROM `coupon_redeem` cr , coupon_codes cc inner join campaigns c on c.id = cc.customer_campaign_code 
					where cr.coupon_id= cc.id ".$redeem_date." and cc.location_id group by cc.customer_campaign_code
					  ";
					  	  //echo $sql;
			  $alltotal = $objDB->Conn->Execute($sql);
			  $total_transaction_point_ex =0 ;
			  $total_transaction_point_fees =0;
			  $total_cost_redeem = 0;
			  $total_cost_sharing = 0;
			  $total_revenue = 0;
			while($row_redeemed_data = $alltotal->FetchRow())
			{
				 //print_r($row_redeemed_data);
					$total_transaction_point_ex= $total_transaction_point_ex + $row_redeemed_data['total_transaction_fees_point'];
					$total_transaction_point_fees= $total_transaction_point_fees + $row_redeemed_data['total_transaction_fees'];
					$total_cost_redeem =  $total_cost_redeem + ( $row_redeemed_data['no_of_records'] * $row_redeemed_data['redeem_rewards']);
					$total_revenue = $total_revenue + $row_redeemed_data['total_revenue'];
			}
			/***** calculate redempation / sharing point ********/
			$sql = "select  SUM(referral_reward) ref_point from reward_user r  inner join campaigns c on c.id = r.campaign_id inner join locations l on l.id = r.location_id
						where  r.reward_date between '".$from_date."' and '".$to_date."' 
						";
            //echo "<br/>===".$sql."===".$month."<br/>";
			$abc = $objDB->Conn->Execute($sql);
			while ($rowabc = $abc->FetchRow()) {
				$total_cost_sharing = $total_cost_sharing + ($rowabc['ref_point']  ) ;
			}
			
			/****************************************************/
						
			$redeemdata = $objDB->Conn->Execute($sql);
	?>
		
	<div class="location_summary">
		<h4><?php echo $language_msg['transaction_fee_report']['main_summary']; ?></h4>
		<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_transaction_point']; ?></div>
		<div  class="heading_val"> : <?php echo ($total_transaction_point_ex == '' ? '0' : $total_transaction_point_ex ) ; ?></div>
		<div class="heading"><?php echo $language_msg['transaction_fee_report']['table_transaction_fee']; ?></div>
		<div  class="heading_val"> : <?php echo ( $total_transaction_point_fees == '' ? '$0.00' : "$".$total_transaction_point_fees ); ?></div>
		<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_redemption_point']; ?></div>
		<div class="heading_val"> : <?php echo $total_cost_redeem; ?></div>
		<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_redemption_cost']; ?></div>
		<div class="heading_val"> : $<?php echo $total_cost_redeem*$conversation_rate; ?></div>
		<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_sharing_point']; ?></div>
		<div class="heading_val"> : <?php echo $total_cost_sharing; ?></div>
		<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_sharing_cost']; ?></div>
		<div class="heading_val"> : $<?php echo $total_cost_sharing*$conversation_rate; ?></div>
		<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_revenue']; ?></div>
		<div class="heading_val"> : $<?php echo $total_revenue; ?></div>
		

	</div>
		<div class="clear"></div>
	<?php
	   	  while($row =  $RS->FetchRow())
		  {
			$sql = "SELECT sum(transaction_fees_price) as total_transaction_fees , sum(cr.transaction_fees) as total_transaction_fees_point ,
			c.redeem_rewards,count(*) no_of_records ,sum(redeem_value) as total_revenue
					FROM `coupon_redeem` cr , coupon_codes cc inner join campaigns c on c.id = cc.customer_campaign_code 
					where cr.coupon_id= cc.id ".$redeem_date." and cc.location_id 
					 and cc.location_id in (select id from locations where created_by= ".$row['id'].")
					group by cc.customer_campaign_code 
					  ";
					  
				  $alltotal = $objDB->Conn->Execute($sql);
				  $total_transaction_point_ex =0 ;
				  $total_transaction_point_fees =0;
				  $total_cost_redeem = 0;
				  $total_cost_sharing = 0;
				  $total_revenue= 0;
			while($row_redeemed_data = $alltotal->FetchRow())
			{
				 //print_r($row_redeemed_data);
					$total_transaction_point_ex= $total_transaction_point_ex + $row_redeemed_data['total_transaction_fees_point'];
					$total_transaction_point_fees= $total_transaction_point_fees + $row_redeemed_data['total_transaction_fees'];
					$total_cost_redeem =  $total_cost_redeem + ( $row_redeemed_data['no_of_records'] * $row_redeemed_data['redeem_rewards']);
					$total_revenue = $total_revenue + $row_redeemed_data['total_revenue'];
			}
			/***** calculate redempation / sharing point ********/
			$sql = "select  SUM(referral_reward) ref_point from reward_user r  
						where  r.reward_date between '".$from_date."' and '".$to_date."' 
						and  r.location_id in (select id from locations where created_by= ".$row['id'].")
						";
                            //echo "<br/>===".$sql."===".$month."<br/>";
			$abc = $objDB->Conn->Execute($sql);
			while ($rowabc = $abc->FetchRow()) {
				$total_cost_sharing = $total_cost_sharing + ($rowabc['ref_point']  ) ;
			}
		  
		  
		  
		  
			
	?>
			<div class="location_heading">
				<div id="toggleIt-plus_<?php echo $row['id']; ?>" class="mainIcon"></div>
				<span><?php echo $row['business']; ?></span>
			</div>
			
			<div id="plus_<?php echo $row['id']; ?>" class="mer_chant_div new_loca_eta" style="display:none">
			<div class="location_summary">
			
						<h4><?php echo $language_msg['transaction_fee_report']['main_summary']; ?></h4>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_transaction_point']; ?></div>
						<div  class="heading_val"> : <?php echo ($total_transaction_point_ex == '' ? '0' : $total_transaction_point_ex ) ; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['table_transaction_fee']; ?></div>
						<div  class="heading_val"> : <?php echo ( $total_transaction_point_fees == '' ? '$0.00' : "$".$total_transaction_point_fees ); ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_redemption_point']; ?></div>
						<div class="heading_val"> : <?php echo $total_cost_redeem; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_redemption_cost']; ?></div>
						<div class="heading_val"> : $<?php echo $total_cost_redeem*$conversation_rate; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_sharing_point']; ?></div>
						<div class="heading_val"> : <?php echo $total_cost_sharing; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_sharing_cost']; ?></div>
						<div class="heading_val"> : $<?php echo $total_cost_sharing*$conversation_rate; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_revenue']; ?></div>
						<div class="heading_val"> : $<?php echo $total_revenue; ?></div>

						</div>
						<div class="clear"></div>
						
				<?php 
					$array_where = array();
					$array_where['created_by'] = $row['id'];
					$RS_locations = $objDB->Show("locations",$array_where);
					while($row_loc = $RS_locations->FetchRow())
					{
					
					$sql = "SELECT sum(transaction_fees_price) as total_transaction_fees , sum(cr.transaction_fees) as total_transaction_fees_point ,
					c.redeem_rewards,count(*) no_of_records ,sum(redeem_value) as total_revenue
					FROM `coupon_redeem` cr , coupon_codes cc inner join campaigns c on c.id = cc.customer_campaign_code 
					where cr.coupon_id= cc.id ".$redeem_date."
					 and cc.location_id = ".$row_loc['id'] ."
					and cc.location_id group by cc.customer_campaign_code
					  ";
					//  echo $sql;
					  $alltotal = $objDB->Conn->Execute($sql);
					  $total_transaction_point_ex =0 ;
					  $total_transaction_point_fees =0;
					  $total_cost_redeem = 0;
					  $total_cost_sharing = 0;
					  $total_revenue = 0;
					while($row_redeemed_data = $alltotal->FetchRow())
					{
						 //print_r($row_redeemed_data);
							$total_transaction_point_ex= $total_transaction_point_ex + $row_redeemed_data['total_transaction_fees_point'];
							$total_transaction_point_fees= $total_transaction_point_fees + $row_redeemed_data['total_transaction_fees'];
							$total_cost_redeem =  $total_cost_redeem + ( $row_redeemed_data['no_of_records'] * $row_redeemed_data['redeem_rewards']);
							$total_revenue = $total_revenue + $row_redeemed_data['total_revenue'];
					}
					/***** calculate redempation / sharing point ********/
					$sql = "select  SUM(referral_reward) ref_point from reward_user r inner join campaigns c on c.id = r.campaign_id inner join locations l on l.id = r.location_id 
								where  r.reward_date between '".$from_date."' and '".$to_date."' and r.location_id = ".$row_loc['id']." 
								";
					//echo "<br/>===".$sql."===".$month."<br/>";
					$abc = $objDB->Conn->Execute($sql);
					while ($rowabc = $abc->FetchRow()) {
						$total_cost_sharing = $total_cost_sharing + ($rowabc['ref_point']  ) ;
					}

				
				
						
							?>
						
						
							<div class="location_heading_location">
								
								<div id="togglelocation-locationplus_<?php echo $row_loc['id']; ?>" class="mainIcon"></div>
								<span><?php echo $merchant_msg['report']['location'];?><?= $row_loc['address'] . ", " . $row_loc['city'] . ", " . $row_loc['state'] . ", " . $row_loc['zip'] ?></span>
							</div>
							<div id="locationplus_<?php echo $row_loc['id']; ?>" class="mer_chant_div new_loca_eta location_tab" style="display:none">
							
						<div class="location_summary">
			
						<h4><?php echo $language_msg['transaction_fee_report']['location_summary']; ?></h4>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_transaction_point']; ?></div>
						<div  class="heading_val"> : <?php echo ($total_transaction_point_ex == '' ? '0' : $total_transaction_point_ex ) ; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['table_transaction_fee']; ?></div>
						<div  class="heading_val"> : <?php echo ( $total_transaction_point_fees == '' ? '$0.00' : "$".$total_transaction_point_fees ); ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_redemption_point']; ?></div>
						<div class="heading_val"> : <?php echo $total_cost_redeem; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_redemption_cost']; ?></div>
						<div class="heading_val"> : $<?php echo $total_cost_redeem*$conversation_rate; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_sharing_point']; ?></div>
						<div class="heading_val"> : <?php echo $total_cost_sharing; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_sharing_cost']; ?></div>
						<div class="heading_val"> : $<?php echo $total_cost_sharing*$conversation_rate; ?></div>
						<div class="heading"><?php echo $language_msg['transaction_fee_report']['total_revenue']; ?></div>
						<div class="heading_val"> : $<?php echo $total_revenue; ?></div>

						</div>
							<div class="clear"></div>
						<hr/>
						<div class="clear"></div>
								<?php 
									/*$r_sql_t_f = "SELECT c.id campaign_id , cr.redeem_date ,c.title campaign_title , cc.customer_campaign_code ,sum(redeem_value) as total_revenue
									,c.redeem_rewards,count(*) no_of_records ,
												 cc.location_id ,CASE WHEN  sum(transaction_fees_price)  IS NULL THEN 0 else  sum(transaction_fees_price) END as total_transaction_fees , 
												 CASE WHEN  sum(cr.transaction_fees)  IS NULL THEN 0 else  sum(cr.transaction_fees) END as total_transaction_fees_point
												FROM `coupon_redeem` cr , coupon_codes cc inner join campaigns c on c.id = cc.customer_campaign_code
												 where cr.coupon_id= cc.id ".$redeem_date." and  cc.location_id=". $row_loc['id'] ." 
												group by cc.customer_campaign_code"; */
												
									$r_sql_t_f = "select * from (
												(SELECT  c.id campaign_id , cr.redeem_date , c.title campaign_title ,
												ROUND(sum(redeem_value), 2) as total_revenue , c.redeem_rewards,0 as referral_reward ,count(*) no_of_records , cc.location_id ,
												ROUND(sum(transaction_fees_price), 2) as total_transaction_fees ,sum(cr.transaction_fees) as total_transaction_fees_point
												 FROM `coupon_redeem` cr , coupon_codes cc inner join campaigns c on c.id = cc.customer_campaign_code 
												where cr.coupon_id= cc.id and (cr.redeem_date between '".$from_date."' and '".$to_date."') and cc.location_id=".$row_loc['id']." 
												group by cc.customer_campaign_code )
												union
												(select r.campaign_id , null,c.title,0 , 0,sum(r.referral_reward) , 0,r.location_id , 0 as total_transaction_fees , 0 as total_transaction_fees_point
												from reward_user r inner join coupon_codes cc on cc.customer_campaign_code = r.campaign_id  inner join campaigns c on c.id = r.campaign_id 
												where r.reward_date between '".$from_date."' and '".$to_date."' and r.location_id = ".$row_loc['id']." and r.customer_id = cc.customer_id and r.location_id = cc.location_id
												group by r.campaign_id )
												) as tbl
												group by campaign_id";

								
									//echo $r_sql_t_f;
									//$rs_t_f = $objDB->Conn->Execute($r_sql_t_f);
									//  echo $sql;
									$alltotal = $objDB->Conn->Execute($r_sql_t_f);
									
									
									
									if($alltotal->RecordCount() > 0 )
									{
									?>
									<div class="Table transaction">
									<div class="Heading">
										<div class="Cell">
											<p><?php echo $language_msg['transaction_fee_report']['table_campaign']; ?></p>
										</div>
										<div class="Cell">
											<p><?php echo $language_msg['transaction_fee_report']['table_redemption_point']; ?></p>
										</div>
										<div class="Cell">
											<p><?php echo $language_msg['transaction_fee_report']['table_redempation']; ?></p>
										</div>
										<div class="Cell">
											<p><?php echo $language_msg['transaction_fee_report']['table_sharing_piont']; ?></p>
										</div>
										<div class="Cell">
											<p><?php echo $language_msg['transaction_fee_report']['table_sharing']; ?></p>
										</div>
										<div class="Cell">
											<p><?php echo $language_msg['transaction_fee_report']['table_transaction_point']; ?></p>
										</div>
										<div class="Cell">
											<p><?php echo $language_msg['transaction_fee_report']['table_transaction_fee']; ?></p>
										</div>
										<div class="Cell">
											<p><?php echo $language_msg['transaction_fee_report']['table_revenue']; ?></p>
										</div>
									</div>
									<?php
									while($row_campaigns = $alltotal->FetchRow())
									{
												
											
											$total_transaction_point_ex =0 ;
											$total_transaction_point_fees =0;
											$total_cost_redeem = 0;
											$total_cost_sharing = 0;	
											$total_transaction_point_ex= $total_transaction_point_ex + $row_campaigns['total_transaction_fees_point'];
											$total_transaction_point_fees= $total_transaction_point_fees + $row_campaigns['total_transaction_fees'];
											$total_cost_redeem =  $total_cost_redeem + ( $row_campaigns['no_of_records'] * $row_campaigns['redeem_rewards']);
							
							$sql = "select  SUM(referral_reward) ref_point from reward_user r   inner join campaigns c on c.id = r.campaign_id inner join locations l on l.id = r.location_id 
												where  r.reward_date between '".$from_date."' and '".$to_date."' and r.location_id = ".$row_loc['id']." and r.campaign_id= ".$row_campaigns['campaign_id'];
									//echo "<br/>===".$sql."===".$month."<br/>";
									$abc = $objDB->Conn->Execute($sql);
									while ($rowabc = $abc->FetchRow()) {
										$total_cost_sharing = $total_cost_sharing + ($rowabc['ref_point']  ) ;
									}
							
									?>
											<div class="Row">
												<div class="Cell">
													<p><?php echo $row_campaigns['campaign_title']; ?></p>
												</div>
												<div class="Cell p_val"> <?php echo $total_cost_redeem; ?>
												</div>
												<div class="Cell p_val"><?php echo $total_cost_redeem*$conversation_rate; ?>
												</div>
												<div class="Cell p_val"><?php echo $total_cost_sharing; ?>
												</div>
												<div class="Cell p_val"><?php echo $total_cost_sharing*$conversation_rate; ?>
												</div>
												<div class="Cell p_val">
													<p><?php
													if($alltotal->RecordCount() == 0 )
													{
														echo "0";
													}
													else
													{
														echo $total_transaction_point_ex; 
														
													}
													?></p>
												</div>
												<div class="Cell p_val">
													<p><?php 
													if($alltotal->RecordCount() == 0 )
													{
														echo "0.00";
													}
													else
													{
														echo $total_transaction_point_fees; 
														
													}
													?></p>
												</div>
												<div>
													$<?php echo $row_campaigns['total_revenue']; ?>
												</div>
											</div>
											
											
										
									<?php
									}
									?>
									</div>
									<?php
									}
								?>
	
								
							</div>
						<?php
					}
				?>
			</div>
	<?php } ?>