<?
header('Content-type: text/html; charset=utf-8');
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where = array();
$array_where['merchant_parent'] = 0;
$RS = $objDB->Show("merchant_user",$array_where);
//echo base64_decode("MTIzNDU2");
$conversation_rate = 0.01;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel | Transaction Summary</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="<?php echo ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
</head>

<body>
     <div id="container">

              <!---start header---->
	
		<?
		require_once(ADMIN_LAYOUT."/header.php");
		?>
		<div id="contentContainer">

	
	<div  id="sidebarLeft">
		<?
		require_once(ADMIN_VIEW."/quick-links.php");
		?>
		<!--end of sidebar Left--></div>

		<div id="content">
	<form action="" method="post">
	<h2>Transaction Summary</h2>
	<div class="filter_section">
	<?php
	$month =date('m');
//echo $month."==";
if ($month == 4 || $month == 6 || $month == 9 || $month == 11) {
//echo "in if";
            $m = 30;
        } else if ($month == 2) {
	//	echo "in else if";
            $m = 28;
        } else {
		//echo "in else";
            $m = 31;
        }
		$year =date('Y');
		$from_date = $year . "-" . date('m') . "-01 00:00:00";
        $to_date = $year . "-" . date('m') . "-" . $m . " 23:59:59";

		$redeem_date =  " and (cr.redeem_date between '".$from_date."' and '".$to_date."')";
		$redeem_date2 =  " and (reward_date between '".$from_date."' and '".$to_date."')";
		$redeem_date3=  "  (reward_date between '".$from_date."' and '".$to_date."')";
	?>
	<select id="opt_filter_year">
<?php

	for($y = date('Y') - 2; $y <= date('Y'); $y++) {
    ?>
                                        <option value="<?php echo $y; ?>" <?php if (date('Y') == $y) {
                                        echo "selected";
                                    } ?> > <?php echo $y; ?> </option>
                                    <?php }
                                    ?>
            </select>
			<select id="opt_filter_month" >
				<option value="0" <?php if(date('m') == 0) { echo "selected"; } ?>>---- ALL ----</option>
				<option value="01" <?php if(date('m') == 1) { echo "selected"; } ?>  >January</option>
				<option value="02" <?php if(date('m') == 2) { echo "selected"; } ?>  >February</option>
				<option value="03" <?php if(date('m') == 3) { echo "selected"; } ?> >March</option>
				<option value="04" <?php if(date('m') == 4) { echo "selected"; } ?> >April</option>
				<option value="05" <?php if(date('m') == 5) { echo "selected"; } ?> >May</option>
				<option value="06" <?php if(date('m') == 6) { echo "selected"; } ?> >Jun</option>
				<option value="07" <?php if(date('m') == 7) { echo "selected"; } ?>>July</option>
				<option value="08" <?php if(date('m') == 8) { echo "selected"; } ?>>August</option>
				<option value="09" <?php if(date('m') == 9) { echo "selected"; } ?> >September</option>
				<option value="10" <?php if(date('m') == 10) { echo "selected"; } ?> >October</option>
				<option value="11" <?php if(date('m') == 11) { echo "selected"; } ?> >November</option>
				<option value="12" <?php if(date('m') == 12) { echo "selected"; } ?>>December</option>
			</select>
			<input type="button" name="btn_submit" id="btn_submit" value="Show Result" />
	</div>
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
	<div id="detail_section">
		
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
	</div>
	<div id="NotificationloaderPopUpContainer" class="container_popup"  style="display: none;">
    <div id="NotificationloaderBackDiv" class="divBack">
    </div>
    <div id="NotificationloaderFrontDivProcessing" class="Processing" style="display:none;">

        <div id="NotificationloaderMaindivLoading" align="center" valign="middle" class="imgDivLoading"
             style="left: 45%;top: 40%;">

            <div id="NotificationloadermainContainer" class="loading innerContainer" style="height:auto;width:auto">
                <img src="<?= ASSETS_IMG ?>/128.GIF" style="display: block;" id="image_loader_div"/>
            </div>
        </div>
    </div>
</div>
	  </form>
	                     <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
<script>
    $(document).ready(function(){
            $("div[id^='toggleIt-']").live("click",function(){
             	var a = $(this).attr("id").split("-");

                var lid = a[1].split("_")[1];
				 if($("#"+a[1]).css("display") == "block")
					{


						$("#"+a[1]).slideUp("fast");
						$(this).removeClass('mainIcon_minus'); 

					}else
					{
					   $("#"+a[1]).slideDown("slow");
                    $(this).addClass('mainIcon_minus');
					}
				});
				
				$("div[id^='togglelocation-']").live("click",function(){
             	var a = $(this).attr("id").split("-");
				var lid = a[1].split("_")[1];
				 if($("#"+a[1]).css("display") == "block")
					{


						$("#"+a[1]).slideUp("fast");
						$(this).removeClass('mainIcon_minus'); 

					}else
					{
					   $("#"+a[1]).slideDown("slow");
                    $(this).addClass('mainIcon_minus');
					}
				});
	});
	jQuery("#btn_submit").click(function(){
	popup_name = 'Notificationloader';
							$("#" + popup_name + "FrontDivProcessing").css("display","block");
							$("#" + popup_name + "PopUpContainer").css("display","block");
							$("#" + popup_name + "BackDiv").css("display","block");
		//alert('transaction_fee_filterreport.php?showresult=true&year='+jQuery("#opt_filter_year").val()+'&month='+jQuery("#opt_filter_month").val());
		jQuery.ajax({
				   type:"POST",
				   url:'transaction_fee_filterreport.php',
				  data :'showresult=true&year='+jQuery("#opt_filter_year").val()+'&month='+jQuery("#opt_filter_month").val(),
				  async:true,
				   success:function(msg)
				   {
						jQuery("#detail_section").html(msg);
							$("#" + popup_name + "FrontDivProcessing").css("display","none");
							$("#" + popup_name + "PopUpContainer").css("display","none");
							$("#" + popup_name + "BackDiv").css("display","none");
					}
		});

	});
	
</script>
</html>
