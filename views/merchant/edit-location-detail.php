<?php
/**
 * @uses edit location detail
 * @used in pages :apply_filter.php,locations.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');

//include_once(SERVER_PATH."/classes/JSON.php");
//$objJSON = new JSON();
if($_SESSION['merchant_id'] != "")
{
    $JSON = $objJSON->get_main_merchant_id($_SESSION['merchant_id']);
    $JSON = json_decode($JSON);

    //echo $JSON;

    $array_values = $where_clause = $array = array();
    $where_clause['id'] = $JSON;
    $RSBN=$objDB->Show("merchant_user", $where_clause);
}
$array_where = array();
$array_where['created_by'] = $_SESSION['merchant_id'];
$array_where['id'] = $_REQUEST['id'];
$RS = $objDB->Show("locations", $array_where); 
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Edit Location Detail</title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.2.min.js"></script>-->
<!--<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery-1.7.2.min.js"></script>-->
<!-- load from CDN-->
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->

<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery.form.js"></script>



<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
<!--- tooltip css --->
<!--<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.js"></script> -->
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/bootstrap.css" />
<!--- tooltip css --->
	

</head>

<body>
     <!--start header--><div>

		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
		
     <div id="dialog-message" title="Message Box" style="display:none">

    </div>
   
 <div >

	<div id="contentContainer">
		<div id="content">  <h3><?php echo $merchant_msg["addlocationdetail"]["Field_location_lable_edit"];?></h3>
		<form id="add_location_detail_form" action="process.php"  method="post" enctype="multipart/form-data">
		<input type="hidden" name="hdn_redirectpath" id="hdn_redirectpath" value="<?=WEB_PATH.'/merchant/locations.php'?>" />
		<!-- T_5 -->
            <input type="hidden" name="hdn_location_id" id="hdn_location_id" value="<?php echo $_REQUEST['id'] ?>" />
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
				<?php
				if($RS->fields['categories']!="")
				{
					$categories=$RS->fields['categories'];
					$array_cat=explode(",",$categories);
					if(count($array_cat)==3)
					{
						$arr=file(WEB_PATH.'/merchant/process.php?getcatlevelnamefromid=yes&id='.$array_cat[0]);
						if(trim($arr[0]) == "")
						{
							unset($arr[0]);
							$arr = array_values($arr);
						}
						$all_json_str = $arr[0];

						$json = json_decode($arr[0]);
						$cat_name1 = $json->name;
						
						$arr=file(WEB_PATH.'/merchant/process.php?getcatlevelnamefromid=yes&id='.$array_cat[1]);
						if(trim($arr[0]) == "")
						{
							unset($arr[0]);
							$arr = array_values($arr);
						}
						$all_json_str = $arr[0];

						$json = json_decode($arr[0]);
						$cat_name2 = $json->name;
						
						$arr=file(WEB_PATH.'/merchant/process.php?getcatlevelnamefromid=yes&id='.$array_cat[2]);
						if(trim($arr[0]) == "")
						{
							unset($arr[0]);
							$arr = array_values($arr);
						}
						$all_json_str = $arr[0];

						$json = json_decode($arr[0]);
						$cat_name3 = $json->name;

				?>
					<input type="hidden" name="hdnlc1" id="hdnlc1" value="<?php echo $array_cat[0] ?>" />
					<input type="hidden" name="hdnlcat1" id="hdnlcat1" value="<?php echo $cat_name1 ?>" />
					
					<input type="hidden" name="hdnlc2" id="hdnlc2" value="<?php echo $array_cat[1] ?>" />
					<input type="hidden" name="hdnlcat2" id="hdnlcat2" value="<?php echo $cat_name2 ?>" />
					
					<input type="hidden" name="hdnlc3" id="hdnlc3" value="<?php echo $array_cat[2] ?>" />
					<input type="hidden" name="hdnlcat3" id="hdnlcat3" value="<?php echo $cat_name3 ?>" />
				<?php
					}
					elseif(count($array_cat)==2)
					{
						$arr=file(WEB_PATH.'/merchant/process.php?getcatlevelnamefromid=yes&id='.$array_cat[0]);
						if(trim($arr[0]) == "")
						{
							unset($arr[0]);
							$arr = array_values($arr);
						}
						$all_json_str = $arr[0];

						$json = json_decode($arr[0]);
						$cat_name1 = $json->name;
						
						$arr=file(WEB_PATH.'/merchant/process.php?getcatlevelnamefromid=yes&id='.$array_cat[1]);
						if(trim($arr[0]) == "")
						{
							unset($arr[0]);
							$arr = array_values($arr);
						}
						$all_json_str = $arr[0];

						$json = json_decode($arr[0]);
						$cat_name2 = $json->name;
						
				?>
					<input type="hidden" name="hdnlc1" id="hdnlc1" value="<?php echo $array_cat[0] ?>" />
					<input type="hidden" name="hdnlcat1" id="hdnlcat1" value="<?php echo $cat_name1 ?>" />
					
					<input type="hidden" name="hdnlc2" id="hdnlc2" value="<?php echo $array_cat[1] ?>" />
					<input type="hidden" name="hdnlcat2" id="hdnlcat2" value="<?php echo $cat_name2 ?>" />
					
					<input type="hidden" name="hdnlc3" id="hdnlc3" value="" />
					<input type="hidden" name="hdnlcat3" id="hdnlcat3" value="" />
				<?php
					}
					elseif(count($array_cat)==1)
					{
						$arr=file(WEB_PATH.'/merchant/process.php?getcatlevelnamefromid=yes&id='.$array_cat[0]);
						if(trim($arr[0]) == "")
						{
							unset($arr[0]);
							$arr = array_values($arr);
						}
						$all_json_str = $arr[0];

						$json = json_decode($arr[0]);
						$cat_name1 = $json->name;
				?>
					<input type="hidden" name="hdnlc1" id="hdnlc1" value="<?php echo $array_cat[0] ?>" />
					<input type="hidden" name="hdnlcat1" id="hdnlcat1" value="<?php echo $cat_name1 ?>" />
					
					<input type="hidden" name="hdnlc2" id="hdnlc2" value="" />
					<input type="hidden" name="hdnlcat2" id="hdnlcat2" value="" />
					
					<input type="hidden" name="hdnlc3" id="hdnlc3" value="" />
					<input type="hidden" name="hdnlcat3" id="hdnlcat3" value="" />
				<?php
					}
				}
				else
				{
				?>
					<input type="hidden" name="hdnlc1" id="hdnlc1" value="" />
					<input type="hidden" name="hdnlcat1" id="hdnlcat1" value="" />
					
					<input type="hidden" name="hdnlc2" id="hdnlc2" value="" />
					<input type="hidden" name="hdnlcat2" id="hdnlcat2" value="" />
					
					<input type="hidden" name="hdnlc3" id="hdnlc3" value="" />
					<input type="hidden" name="hdnlcat3" id="hdnlcat3" value="" />
				<?php
				}
				?>
				<table width="100%"  border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td width="20%" align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_current_location"];?></td>
					<td width="80%" align="left">
						<table width="100%"  border="0" cellspacing="1" cellpadding="10" class="tableMerchant cls_left"  id="example">
							<thead>
								<tr>
									<th align="left" class="tableDealTh table_th_30">Address</th>
									<th align="left" class="tableDealTh table_th_15">Phone Number</th>
								</tr>
							</thead>
							<tr>
							<tbody>
							<?php
							$json_array = array();
							$records = array();
							$merchant_id = $_SESSION['merchant_id'];
							/*$Sql = "SELECT * FROM locations WHERE id='".$_REQUEST['id']."' and created_by='".$merchant_id."' and active=1";

							$RS_lok = $objDB->Conn->Execute($Sql);*/
							$RS_lok = $objDB->Conn->Execute("SELECT * FROM locations WHERE id=? and created_by=? and active=?",array($_REQUEST['id'],$merchant_id,1));

							if($RS_lok->RecordCount()>0)
							{
								while($Row_lok = $RS_lok->FetchRow())
								{
									?>
									<tr>
										<td class="table_th_40"> 
											<?php echo $Row_lok['address'].", ".$Row_lok['city'].", ".$Row_lok['state'].", ".$Row_lok['zip']; ?>
										</td class="table_th_15"> 
										<td>
											<?php 
											//echo $Row_lok['phone_number']; 
											$phno = explode("-", $Row_lok['phone_number']);
											echo $newphno = "(".$phno[1].") ".$phno[2]."-".$phno[3];
											?>
										</td>
									</tr>
									<?php
								}
							}
							?>								
							</tbody>
						</table>	
					</td>
				  </tr>
				

				 <tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_dining"];?></td>
					<td align="left">
					<?php
						$dining=$RS->fields['dining'];
						$str=explode(",",$dining);
						//print_r($str);
					?>
						<input <?php if(in_array("Breakfast/Brunch", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_dining[]" value="Breakfast/Brunch" id="Breakfast/Brunch" /><label for="Breakfast/Brunch" class='chk_align'><?php echo "Breakfast/Brunch" ?></label>
						<input <?php if(in_array("Lunch", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_dining[]" value="Lunch" id="Lunch" /><label for="Lunch" class='chk_align'> <?php echo "Lunch" ?></label>
						<input <?php if(in_array("Dinner", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_dining[]" value="Dinner" id="Dinner" /><label for="Dinner" class='chk_align'><?php echo "Dinner" ?></label>
						<input <?php if(in_array("Late Night", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_dining[]" value="Late Night" id="Late Night" /><label for="Late Night"class='chk_align'><?php echo "Late Night"?></label>
						<input <?php if(in_array("Dessert", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_dining[]" value="Dessert" id="Dessert" /><label for="Dessert" class='chk_align'><?php echo "Dessert" ?></label>
					</td>
				  </tr>
	
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_reservation"];?></td>
					<td align="left">
						<?php
						$reservation=$RS->fields['reservation'];
						?>
						<input id="reservation_yes" <?php if($reservation=="Yes") echo "checked='checked'"; ?> type="radio" name="rdo_reservation" value="Yes" /><label for= "reservation_yes" class='chk_align'><?php echo "Yes" ?></label>
						<input id="reservation_no" <?php if($reservation=="No") echo "checked='checked'"; ?> type="radio" name="rdo_reservation" value="No" /><label for= "reservation_no" class='chk_align'><?php echo "No" ?></label>
                        <input id="reservation_notapplicable" <?php if($reservation=="Not Applicable") echo "checked='checked'"; ?> type="radio" name="rdo_reservation" value="Not Applicable" /><label for= "reservation_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_takeout"];?></td>
					<td align="left">
						<?php
						$takeout=$RS->fields['takeout'];
						?>
						<input id="takeout_yes" <?php if($takeout=="Yes") echo "checked='checked'"; ?> type="radio" name="rdo_takeout" value="Yes" /><label for="takeout_yes" class='chk_align'><?php echo "Yes" ?></label>
						<input id="takeout_no" <?php if($takeout=="No") echo "checked='checked'"; ?> type="radio" name="rdo_takeout" value="No" /><label for="takeout_no" class='chk_align'><?php echo "No" ?></label>
                        <input id="takeout_notapplicable" <?php if($takeout=="Not Applicable") echo "checked='checked'"; ?> type="radio" name="rdo_takeout" value="Not Applicable" /><label for="takeout_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_ambience"];?></td>
					<td align="left">
						<?php
						$ambience=$RS->fields['ambience'];
						$str=explode(",",$ambience);
						//print_r($str);
						?>
						<input <?php if(in_array("Classy", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_ambience[]" value="Classy" id="Classy" /><label for="Classy" class='chk_align'><?php echo "Classy" ?></label>
						<input <?php if(in_array("Romantic", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_ambience[]" value="Romantic" id="Romantic" /><label for="Romantic" class='chk_align'><?php echo "Romantic" ?></label>
						<input <?php if(in_array("Upscale", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_ambience[]" value="Upscale" id="Upscale" /><label for="Upscale" class='chk_align'><?php echo "Upscale" ?></label>
						<input <?php if(in_array("Touristy", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_ambience[]" value="Touristy" id="Touristy" /><label for="Touristy" class='chk_align'><?php echo "Touristy" ?></label>
						<input <?php if(in_array("Trendy", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_ambience[]" value="Trendy" id="Trendy" /><label for="Trendy" class='chk_align'><?php echo "Trendy" ?></label>
						<input <?php if(in_array("Casual", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_ambience[]" value="Casual" id="ambience_Casual" /><label for="ambience_Casual" class='chk_align'><?php echo "Casual" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_attire"];?></td>
					<td align="left">
						<?php
						$attire=$RS->fields['attire'];
						$str=explode(",",$attire);
						//print_r($str);
						?>
						<input <?php if(in_array("Dress", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_attire[]" value="Dress" id="Dress" /><label for="Dress"class='chk_align'><?php echo "Dress" ?></label>
						<input <?php if(in_array("Formal", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_attire[]" value="Formal" id="Formal" /><label for="Formal" class='chk_align'><?php echo "Formal" ?></label>
						<input <?php if(in_array("Casual", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_attire[]" value="Casual" id="attire_Casual" /><label for="attire_Casual" class='chk_align'><?php echo "Casual" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_goodfor"];?></td>
					<td align="left">
						<?php
						$good_for=$RS->fields['good_for'];
						$str =array();
						$str=explode(",",$good_for);
						//print_r($str);
						?>
						<input <?php if(in_array("Groups", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_goodfor[]" value="Groups" id="Groups" /><label for="Groups"class='chk_align'><?php echo "Groups" ?></label>
						<input <?php if(in_array("kids", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_goodfor[]" value="kids" id="kids" /><label for="kids" class='chk_align'><?php echo "kids"?></label>
						<input <?php if(in_array("Family with children’s", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_goodfor[]" value="Family with children’s" id="Family with children’s" /><label for="Family with children’s" class='chk_align'><?php echo $str = htmlallentities("Family with children’s");  ?></label>
						<input <?php if(in_array("Bar Scene", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_goodfor[]" value="Bar Scene" id="Bar Scene" /><label for="Bar Scene"class='chk_align'><?php echo "Bar Scene" ?></label>
						<input <?php if(in_array("Romance", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_goodfor[]" value="Romance" id="Romance" /><label for="Romance" class='chk_align'><?php echo "Romance" ?></label>
						<input <?php if(in_array("Special Occasion", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_goodfor[]" value="Special Occasion" id="Special Occasion" /><label for="Special Occasion" class='chk_align'><?php echo "Special Occasion" ?></label>
						<input <?php if(in_array("Entertaining Clients", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_goodfor[]" value="Entertaining Clients" id="Entertaining Clients" /><label for="Entertaining Clients" class='chk_align'><?php echo "Entertaining Clients" ?></label>
						<input <?php if(in_array("Outdoor seating", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_goodfor[]" value="Outdoor seating" id="Outdoor seating" /><label for="Outdoor seating"class='chk_align'><?php echo "Outdoor seating" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_payment_method"];?></td>
					<td align="left">
						<?php
						$payment_method=$RS->fields['payment_method'];
						$str = array();
						$str=explode(",",$payment_method);
						//print_r($str);
						?>
                                                <input <?php if(in_array("Cash", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Cash" id="Cash" /><label for="Cash" class='chk_align'><?php echo "Cash" ?></label>
						<input <?php if(in_array("Cash only", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Cash only" id="Cash only" /><label for="Cash only" class='chk_align'><?php echo "Cash only" ?></label>
						<input <?php if(in_array("Visa", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Visa" id="Visa" /><label for="Visa" class='chk_align'><?php echo "Visa" ?></label>
						<input <?php if(in_array("Master card", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Master card" id="Master card" /><label for="Master card" class='chk_align'><?php echo "Master card" ?></label>
						<input <?php if(in_array("AMEX", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="AMEX" id="AMEX" /><label for="AMEX" class='chk_align'><?php echo "AMEX" ?></label>
						<input <?php if(in_array("Discover", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Discover" id="Discover" /><label for="Discover" class='chk_align'><?php echo "Discover" ?></label>
						<input <?php if(in_array("Dinerclub", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Dinerclub" id="Dinerclub" /><label  for="Dinerclub" class='chk_align'><?php echo "Dinerclub" ?></label>
						<input <?php if(in_array("Paypal", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Paypal" id="Paypal" /><label for="Paypal" class='chk_align'><?php echo "Paypal" ?></label>
						<input <?php if(in_array("Square", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Square" id="Square" /><label for="Square" class='chk_align'><?php echo "Square" ?></label>
						<input <?php if(in_array("Debit card", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Debit card" id="Debit card" /><label for="Debit card" class='chk_align'><?php echo "Debit card" ?></label>
					</td>
				</tr>
				
				
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_pet"];?></td>
					<td align="left">
						<?php
						$pet=$RS->fields['pet'];
						?>
						<input id="pet_yes" <?php if($pet=="Yes") echo "checked='checked'"; ?> type="radio" name="rdo_pet" value="Yes" /><label for="pet_yes" class='chk_align'><?php echo "Yes" ?></label>
						<input id="pet_no" <?php if($pet=="No") echo "checked='checked'"; ?> type="radio" name="rdo_pet" value="No" /><label for="pet_no" class='chk_align'><?php echo "No" ?></label>
                        <input id="pet_notapplicable" <?php if($pet=="Not Applicable") echo "checked='checked'"; ?> type="radio" name="rdo_pet" value="Not Applicable" /><label for="pet_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_wheelchair"];?></td>
					<td align="left">
						<?php
						$wheelchair=$RS->fields['wheelchair'];
						?>
						<input id="wheelchair_yes" <?php if($wheelchair=="Yes") echo "checked='checked'"; ?> type="radio" name="rdo_wheelchair" value="Yes" /><label for="wheelchair_yes" class='chk_align'><?php echo "Yes" ?></label>
						<input id="wheelchair_no" <?php if($wheelchair=="No") echo "checked='checked'"; ?> type="radio" name="rdo_wheelchair" value="No" /><label for="wheelchair_no" class='chk_align'><?php echo "No" ?></label>
                        <input id="wheelchair_notapplicable" <?php if($wheelchair=="Not Applicable") echo "checked='checked'"; ?> type="radio" name="rdo_wheelchair" value="Not Applicable" /><label for="wheelchair_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_wifi"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_wifi"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<?php
						$wifi=$RS->fields['wifi'];
						?>
						<input <?php if($wifi=="Free") echo "checked='checked'"; ?> id="wifi_free" type="radio" name="rdo_wifi" value="Free" /><label for="wifi_free" class='chk_align'><?php echo "Free" ?></label>
						<input <?php if($wifi=="Paid") echo "checked='checked'"; ?> id="wifi_paid" type="radio" name="rdo_wifi" value="Paid" /><label for="wifi_paid" class='chk_align'><?php echo "Paid" ?></label>
						<input <?php if($wifi=="No") echo "checked='checked'"; ?> id="wifi_no" type="radio" name="rdo_wifi" value="No" /><label for="wifi_no" class='chk_align'><?php echo "No" ?></label>
                        <input <?php if($wifi=="Not Applicable") echo "checked='checked'"; ?> id="wifi_notapplicable" type="radio" name="rdo_wifi" value="Not Applicable" /><label for="wifi_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_tv"];?></td>
					<td align="left">
						<?php
						$has_tv=$RS->fields['has_tv'];
						?>
						<input <?php if($has_tv=="Yes") echo "checked='checked'"; ?> id="tv_yes" type="radio" name="rdo_tv" value="Yes" /><label for="tv_yes" class='chk_align'><?php echo "Yes" ?></label>
						<input <?php if($has_tv=="No") echo "checked='checked'"; ?> id="tv_no" type="radio" name="rdo_tv" value="No" /><label for="tv_no" class='chk_align'><?php echo "No" ?></label>
                        <input <?php if($has_tv=="Not Applicable") echo "checked='checked'"; ?> id="tv_notapplicable" type="radio" name="rdo_tv" value="Not Applicable" /><label for="tv_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_airconditioned"];?></td>
					<td align="left">
						<?php
						$airconditioned=$RS->fields['airconditioned'];
						?>
						<input <?php if($airconditioned=="Yes") echo "checked='checked'"; ?> id="ac_yes" type="radio" name="rdo_airconditioned" value="Yes" /><label for="ac_yes" class='chk_align'><?php echo "Yes" ?></label>
						<input <?php if($airconditioned=="No") echo "checked='checked'"; ?> id="ac_no" type="radio" name="rdo_airconditioned" value="No" /><label for="ac_no" class='chk_align'><?php echo "No" ?></label>
                        <input <?php if($airconditioned=="Not Applicable") echo "checked='checked'"; ?> id="ac_notapplicable" type="radio" name="rdo_airconditioned" value="Not Applicable" /><label for="ac_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_smoking"];?></td>
					<td align="left">
						<?php
						$smoking=$RS->fields['smoking'];
						?>
						<input <?php if($smoking=="Yes") echo "checked='checked'"; ?> id="smoking_yes" type="radio" name="rdo_smoking" value="Yes" /><label for="smoking_yes" class='chk_align'><?php echo "Yes" ?></label>
						<input <?php if($smoking=="No") echo "checked='checked'"; ?> id="smoking_no" type="radio" name="rdo_smoking" value="No" /><label for="smoking_no" class='chk_align'><?php echo "No" ?></label>
						<input <?php if($smoking=="Outdoor") echo "checked='checked'"; ?> id="smoking_outdoor" type="radio" name="rdo_smoking" value="Outdoor" /><label for="smoking_outdoor" class='chk_align'><?php echo "Outdoor" ?></label>
                        <input <?php if($smoking=="Not Applicable") echo "checked='checked'"; ?> id="smoking_notapplicable" type="radio" name="rdo_smoking" value="Not Applicable" /><label for="smoking_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_alcohol"];?></td>
					<td align="left">
						<?php
						$alcohol=$RS->fields['alcohol'];
						?>
						
						<input <?php if($alcohol=="Beer and wine") echo "checked='checked'"; ?> id="Beer and wine" type="radio" name="rdo_alcohol" value="Beer and wine" /><label for="Beer and wine" class='chk_align'><?php echo "Beer and wine" ?></label>
						<input <?php if($alcohol=="Full bar") echo "checked='checked'"; ?> id="Full bar" type="radio" name="rdo_alcohol" value="Full bar" /><label for="Full bar" class='chk_align'><?php echo "Full bar" ?></label>
						<input <?php if($alcohol=="Not Applicable") echo "checked='checked'"; ?> id="beer_notapplicable" type="radio" name="rdo_alcohol" value="Not Applicable" /><label for="beer_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
						
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_noiselevel"];?></td>
					<td align="left">
						<?php
						$noise_level=$RS->fields['noise_level'];

						?>
                        <input <?php if($noise_level=="Quite") echo "checked='checked'"; ?> id="noiselevel_quite" type="radio" name="rdo_noiselevel" value="Quite" /><label for="noiselevel_quite" class='chk_align'><?php echo "Quiet" ?></label>
						<input <?php if($noise_level=="Typical") echo "checked='checked'"; ?> id="noiselevel_typical" type="radio" name="rdo_noiselevel" value="Typical" /><label for="noiselevel_typical" class='chk_align'><?php echo "Typical" ?></label>
						<input <?php if($noise_level=="Loud") echo "checked='checked'"; ?> id="noiselevel_loud" type="radio" name="rdo_noiselevel" value="Loud" /><label for="noiselevel_loud" class='chk_align'><?php echo "Loud" ?></label>
                        <input <?php if($noise_level=="Not Applicable") echo "checked='checked'"; ?> id="noiselevel_notapplicable" type="radio" name="rdo_noiselevel" value="Not Applicable" /><label for="noiselevel_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_minimum_age"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_age"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<input value="<?php echo $RS->fields['minimum_age']; ?>" type="text" name="minimum_age" id="minimum_age" size="10"/>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_will_deliver"];?></td>
					<td align="left">
						<?php
						$will_deliver=$RS->fields['will_deliver'];
						?>
						<input <?php if($will_deliver=="Yes") echo "checked='checked'"; ?> id="deliver_yes" type="radio" name="rdo_will_deliver" value="Yes" /><label for="deliver_yes"class='chk_align'><?php echo "Yes" ?></label>
						<input <?php if($will_deliver=="No") echo "checked='checked'"; ?> id="deliver_no" type="radio" name="rdo_will_deliver" value="No" /><label for="deliver_no" class='chk_align'><?php echo "No" ?></label>
						<input <?php if($will_deliver=="Not Applicable") echo "checked='checked'"; ?> id="deliver_notapplicable" type="radio" name="rdo_will_deliver" value="Not Applicable" /><label for="deliver_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				<?php
				if($will_deliver=="Yes")
				{
				?>
				<tr class="delivery_area">
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_minimum_order"];?></td>
					<td align="left">
						<input value="<?php echo $RS->fields['minimum_order']; ?>" type="text" name="minimum_order" id="minimum_order" />
					</td>
				</tr>
				<tr class="delivery_area">
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_delivery_area"];?></td>
					<td align="left">
						From :&nbsp;<input value="<?php echo $RS->fields['deliveryarea_from']; ?>" type="text" name="delivery_from" id="delivery_from" />&nbsp;
						To :&nbsp;<input value="<?php echo $RS->fields['deliveryarea_to']; ?>" type="text" name="delivery_to" id="delivery_to" />
					</td>
				</tr>
				<?php
				}
				else
				{
				?>
				<tr class="delivery_area" style="display:none;">
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_minimum_order"];?></td>
					<td align="left">
						<input value="<?php echo $RS->fields['minimum_order']; ?>" type="text" name="minimum_order" id="minimum_order" />
					</td>
				</tr>
				<tr class="delivery_area" style="display:none;">
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_delivery_area"];?></td>
					<td align="left">
						From :&nbsp;<input value="<?php echo $RS->fields['deliveryarea_from']; ?>" type="text" name="delivery_from" id="delivery_from" />&nbsp;
						To :&nbsp;<input value="<?php echo $RS->fields['deliveryarea_to']; ?>" type="text" name="delivery_to" id="delivery_to" />
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_caters"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_cater"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<?php
						$caters=$RS->fields['caters'];
						?>
						<input <?php if($caters=="Yes") echo "checked='checked'"; ?> id="cater_yes" type="radio" name="rdo_caters" value="Yes" /><label for="cater_yes" class='chk_align'><?php echo "Yes" ?></label>
						<input <?php if($caters=="No") echo "checked='checked'"; ?> id="cater_no" type="radio" name="rdo_caters" value="No" /><label for="cater_no"class='chk_align'><?php echo "No" ?></label>
                        <input <?php if($caters=="Not Applicable") echo "checked='checked'"; ?> id="cater_notapplicable" type="radio" name="rdo_caters" value="Not Applicable" /><label for="cater_notapplicable" class='chk_align'><?php echo "Not Applicable" ?></label>
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_services"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_services"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<textarea name="services" id="services" class="location_services_textarea" placeholder="Optional"><?php echo $RS->fields['services']; ?></textarea>
						<br/><span id="numeric"><span>Add 15 Services | Comma separated | no HTML allowed</span>
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_amenities"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_amenities"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<textarea name="amenities" id="amenities" class="location_amenities_textarea" placeholder="Optional"><?php echo $RS->fields['amenities']; ?></textarea>
						<br/><span id="numeric"><span>Add 15 Amenities | Comma separated | no HTML allowed</span>
					</td>
				</tr>
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<!-- T_5 -->
						<input type="submit" name="btnEditLocationDetail" value="<?php echo $merchant_msg['index']['btn_save'];?>" id="btnEditLocationDetail">
						<!--// 369-->
                         <script>function btncanLocation(){                                                
                                                window.location="<?=WEB_PATH?>/merchant/locations.php";}</script>
							<input type="button" name="btnCancel" id="btnCancel" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanLocation();">
                        <!--// 369-->
						<!-- T_5 -->
					</td>
				  </tr>
				</table>
				
		<div class="clear">&nbsp;</div><!--end of content--></div>

<!--end of contentContainer--></div>

<!--start footer--><div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		?>
		<!--end of footer--></div>
	
</div>

 
 </form>
<!-- end of image upload popup div PAY-508-28033 -->
<script>
jQuery('input:radio[name="rdo_will_deliver"]').change(function(){
    if(jQuery(this).val() == 'Yes')
	{
       jQuery('.delivery_area').show();
    }
	else if(jQuery(this).val() == 'No')
	{
		jQuery('.delivery_area').css("display","none");
	}
	else if(jQuery(this).val() == 'Not Applicable')
	{
		jQuery('.delivery_area').css("display","none");
	}
});

jQuery('#btnEditLocationDetail').ajaxForm({ 
        beforeSubmit:processLoginorNot,    
        dataType:  'json'
});

function processLoginorNot()
{
    
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
				
		   }
		   
	 });  
}

jQuery('#btnEditLocationDetail').click(function(){
	//alert("hi");
	var flag="";
	var msgbox="";
	var currencyReg = /^\$?[0-9]+(\.[0-9][0-9])?$/;
	var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
	
	var paymentcount=jQuery('input:checkbox[name="chk_payment_method[]"]:checked').size();
	//alert(paymentcount);
	if(paymentcount==0)
	{
		//alert("<?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_payment"];?>");
		//return false;
		msgbox+="<div><?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_payment"];?></div>";
		flag="false";
		
	}

	
	var minimum_age=jQuery("#minimum_age").val();
	if(minimum_age!="")
	{
		if (!currencyReg.test(jQuery("#minimum_age").val()))
		{
			msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_please_select_proper_min_age"]; ?></div>";
			flag="false";
		}
		else
		{
			var n=minimum_age.indexOf("$");
			if(n!="-1")
			{
				msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_please_select_proper_min_age"]; ?></div>";
				flag="false";
			}
			
		}
	}
	
	
	if(jQuery('input[name=rdo_will_deliver]:radio:checked').val()=="Yes")
	{
		var minimum_order=jQuery("#minimum_order").val();
		if(minimum_order!="")
		{
			if (!currencyReg.test(jQuery("#minimum_order").val()))
			{
				msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_please_select_proper_min_order"]; ?></div>";
				flag="false";
			}
			else
			{
				var n=minimum_age.indexOf("$");
				if(n!="-1")
				{
					jQuery("#minimum_order").val((jQuery("#minimum_order").val()).substring(n+1, (jQuery("#minimum_order").val()).length));
				}
				
			}
		}
	}
	
	var services=jQuery("#services").val();
	if(services == "")
	{
		
	}
	else
	{
		//if(hastagRef.test(services))
		//{
			
			var tag_arr = services.split(",");   
			//alert(tag_arr.length);
			if(tag_arr.length>15)
			{
				msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_please_add_services"]; ?></div>";
				flag="false";
			}
			
		//}
		//else
		//{
		
		//	msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_add_valid_services"]; ?></div>";
		//	flag="false";
		//}
	}
	
	var amenities=jQuery("#amenities").val();
	if(amenities == "")
	{
		
	}
	else
	{
		if(hastagRef.test(amenities))
		{
			
			var tag_arr = amenities.split(",");   
			//alert(tag_arr.length);
			if(tag_arr.length>15)
			{
				msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_please_add_amenities"]; ?></div>";
				flag="false";
			}
			
		}
		else
		{
		
			msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_add_valid_amenities"]; ?></div>";
			flag="false";
		}
	}
	
	var head_msg="<div class='head_msg'>Message</div>"
	var content_msg="<div class='content_msg'>"+msgbox+"</div>";
	var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
	jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
	
	if(flag=="false")
	{
	  
	
		jQuery.fancybox({
								content:jQuery('#dialog-message').html(),
								
								type: 'html',
								
								openSpeed  : 300,
								
								closeSpeed  : 300,
								// topRatio: 0,
				
								changeFade : 'fast',  
								
								helpers: {
										overlay: {
										opacity: 0.3
										} // overlay
								}
		});
		return false;
	}
}); 
	

jQuery("#popupcancel").live("click",function(){
    jQuery.fancybox.close(); 
    return false; 
});
  
function show_tooltip()
{
	jQuery('.notification_tooltip').tooltip({
	track: true,
	delay: 0,
	showURL: false,
	showBody: "<br>",
	fade: 250
	});
}
show_tooltip();


jQuery('#first_cat_first_level').on('change', function() {
	jQuery("#cat_selected1").remove();
	var cat_first_id=jQuery(this).val();
	jQuery("#hdnlc1").val(cat_first_id);
	jQuery("#hdnlcat1").val(jQuery("#first_cat_first_level option:selected").text().replace('>', ''));
	jQuery.ajax({
		type: "POST",
		url: "<?=WEB_PATH?>/merchant/process.php",
		data: "lc=1&cat_first_id=" + cat_first_id +"&get_second_category_level=yes",
		success: function(msg) 
		{
			jQuery("#first_cat_second_level").remove();
			jQuery("#first_cat_third_level").remove();
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{	
				jQuery("#first_cat_first_level").after(obj.html);
				bind_change_event();
			}
			
		}
    });
});

jQuery('#second_cat_first_level').on('change', function() {
	jQuery("#cat_selected1").remove();
	var cat_first_id=jQuery(this).val();
	jQuery("#hdnlc2").val(cat_first_id);
	jQuery("#hdnlcat2").val(jQuery("#second_cat_first_level option:selected").text().replace('>', ''));
	jQuery.ajax({
		type: "POST",
		url: "<?=WEB_PATH?>/merchant/process.php",
		data: "lc=2&cat_first_id=" + cat_first_id +"&get_second_category_level=yes",
		success: function(msg) 
		{
			jQuery("#second_cat_second_level").remove();
			jQuery("#second_cat_third_level").remove();
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{	
				jQuery("#second_cat_first_level").after(obj.html);
				
				jQuery("#second_cat_second_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
				
				bind_change_event();
			}
			
		}
    });
});

jQuery('#third_cat_first_level').on('change', function() {
	jQuery("#cat_selected1").remove();
	var cat_first_id=jQuery(this).val();
	jQuery("#hdnlc3").val(cat_first_id);
	jQuery("#hdnlcat3").val(jQuery("#third_cat_first_level option:selected").text().replace('>', ''));
	jQuery.ajax({
		type: "POST",
		url: "<?=WEB_PATH?>/merchant/process.php",
		data: "lc=3&cat_first_id=" + cat_first_id +"&get_second_category_level=yes",
		success: function(msg) 
		{
			jQuery("#third_cat_second_level").remove();
			jQuery("#third_cat_third_level").remove();
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="true")     
			{	
				jQuery("#third_cat_first_level").after(obj.html);
				
				jQuery("#third_cat_second_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
				jQuery("#third_cat_second_level option[value='"+jQuery("#hdnlc2").val()+"']").remove();

				bind_change_event();
			}
			
		}
    });
});

function bind_change_event()
{

	jQuery('#first_cat_second_level').on('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_second_id=jQuery(this).val();
		jQuery("#hdnlc1").val(cat_second_id);
		jQuery("#hdnlcat1").val(jQuery("#first_cat_second_level option:selected").text().replace('>', ''));
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/merchant/process.php",
			data: "lc=1&cat_second_id=" + cat_second_id +"&get_third_category_level=yes",
			success: function(msg) 
			{
				jQuery("#first_cat_third_level").remove();
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
					jQuery("#first_cat_second_level").after(obj.html);
					//bind_change_event();
				}
				else
				{
					jQuery("#first_cat_second_level").next().html("");
					jQuery("#first_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected">&#10003;</span></div>');
					
					if(jQuery("#add_cat").attr("total")!=3)
					{
						jQuery("#add_cat_tr").css("display","block");
					}
					
					//bind_change_event();
				}
				
			}
		});
	});
	jQuery('#first_cat_third_level').live('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_third_id=jQuery(this).val();
		jQuery("#hdnlc1").val(cat_third_id);
		jQuery("#hdnlcat1").val(jQuery("#first_cat_third_level option:selected").text().replace('>', ''));
		jQuery("#first_cat_third_level").next().html("");
		jQuery("#first_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected">&#10003;</span></div>');
		if(jQuery("#add_cat").attr("total")!=3)
		{
			jQuery("#add_cat_tr").css("display","block");
		}
	});

	jQuery('#second_cat_second_level').on('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_second_id=jQuery(this).val();
		jQuery("#hdnlc2").val(cat_second_id);
		jQuery("#hdnlcat2").val(jQuery("#second_cat_second_level option:selected").text().replace('>', ''));
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/merchant/process.php",
			data: "lc=2&cat_second_id=" + cat_second_id +"&get_third_category_level=yes",
			success: function(msg) 
			{
				jQuery("#second_cat_third_level").remove();
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
					jQuery("#second_cat_second_level").after(obj.html);
					
					jQuery("#second_cat_third_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
					
					//bind_change_event();
				}
				else
				{
					jQuery("#second_cat_second_level").next().html("");
					jQuery("#second_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected">&#10003;</span></div>');
					if(jQuery("#add_cat").attr("total")!=3)
					{
						jQuery("#add_cat_tr").css("display","block");
					}
					//bind_change_event();
				}
				
			}
		});
	});
	jQuery('#second_cat_third_level').live('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_third_id=jQuery(this).val();
		jQuery("#hdnlc2").val(cat_third_id);
		jQuery("#hdnlcat2").val(jQuery("#second_cat_third_level option:selected").text().replace('>', ''));
		jQuery("#second_cat_third_level").next().html("");
		jQuery("#second_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected">&#10003;</span></div>');
		if(jQuery("#add_cat").attr("total")!=3)
		{
			jQuery("#add_cat_tr").css("display","block");
		}
	});

	jQuery('#third_cat_second_level').on('change', function() {
		jQuery("#cat_selected1").remove();
	  var cat_second_id=jQuery(this).val();
		jQuery("#hdnlc3").val(cat_second_id);
		jQuery("#hdnlcat3").val(jQuery("#third_cat_second_level option:selected").text().replace('>', ''));
		jQuery.ajax({
			type: "POST",
			url: "<?=WEB_PATH?>/merchant/process.php",
			data: "lc=3&cat_second_id=" + cat_second_id +"&get_third_category_level=yes",
			success: function(msg) 
			{
				jQuery("#third_cat_third_level").remove();
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="true")     
				{
					jQuery("#third_cat_second_level").after(obj.html);
					
					jQuery("#third_cat_third_level option[value='"+jQuery("#hdnlc1").val()+"']").remove();
					jQuery("#third_cat_third_level option[value='"+jQuery("#hdnlc2").val()+"']").remove();
					
					//bind_change_event();
				}
				else
				{
					jQuery("#third_cat_second_level").next().html("");
					jQuery("#third_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected">&#10003;</span></div>');
					if(jQuery("#add_cat").attr("total")!=3)
					{
						jQuery("#add_cat_tr").css("display","inline-block");
					}
					
					//jQuery("#third_lc").css("display","none");
					jQuery("#loc_cat_3").css("display","none");
					jQuery("#third_lc_delete").css("display","block");
					jQuery("#third_selected_cat").text(jQuery("#hdnlcat3").val());
					jQuery("#third_selected_cat_delete").attr("catid",jQuery("#hdnlc3").val());
					
					if(jQuery("#hdnlc1").val()!="" && jQuery("#hdnlc2").val()!="" && jQuery("#hdnlc3").val()!="")
                                        {
                                            jQuery("#add_cat_tr").css("display","none");
                                        }
                                            
					//bind_change_event();
				}
				
			}
		});
	});
	jQuery('#third_cat_third_level').live('change', function() {
		jQuery("#cat_selected1").remove();
	    var cat_third_id=jQuery(this).val();
		jQuery("#hdnlc3").val(cat_third_id);
		jQuery("#hdnlcat3").val(jQuery("#third_cat_third_level option:selected").text().replace('>', ''));
		jQuery("#third_cat_third_level").next().html("");
		jQuery("#third_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected">&#10003;</span></div>');
		if(jQuery("#add_cat").attr("total")!=3)
		{
			jQuery("#add_cat_tr").css("display","block");
		}
		
		//jQuery("#third_lc").css("display","none");
		jQuery("#loc_cat_3").css("display","none");
		jQuery("#third_lc_delete").css("display","block");
		jQuery("#third_selected_cat").text(jQuery("#hdnlcat3").val());
		jQuery("#third_selected_cat_delete").attr("catid",jQuery("#hdnlc3").val());
		
                if(jQuery("#hdnlc1").val()!="" && jQuery("#hdnlc2").val()!="" && jQuery("#hdnlc3").val()!="")
                {
                    jQuery("#add_cat_tr").css("display","none");
                }
	});
	
}
jQuery("#add_cat").live("click",function(){
	var total=parseInt(jQuery(this).attr("total"));

	if(total==1)
	{
		
		
		if(jQuery("#hdnlcat1").val()=="" && jQuery("#hdnlcat3").val()!="")
		{
			//jQuery("#loc_cat_1").css("display","table-row");
			jQuery("#first_lc").css("display","block");
			
			jQuery("#second_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat2").val()=="")
		{
			jQuery("#second_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat3").val()=="")
		{
			jQuery("#third_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#second_lc").css("display","none");
		}
	}
	else if(total==2)
	{
		//jQuery("#second_lc").css("display","none");
		jQuery("#loc_cat_2").css("display","none")
		
		if(jQuery("#hdnlcat1").val()=="" && jQuery("#hdnlcat3").val()!="")
		{
			//jQuery("#loc_cat_1").css("display","table-row");
			jQuery("#first_lc").css("display","block");
			jQuery("#second_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat2").val()=="")
		{
			jQuery("#second_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#third_lc").css("display","none");
		}
		else if(jQuery("#hdnlcat3").val()=="")
		{
			jQuery("#third_lc").css("display","block");
			jQuery("#first_lc").css("display","none");
			jQuery("#second_lc").css("display","none");
		}
	}
	if(jQuery("#hdnlcat1").val()!="")
	{
		jQuery("#first_lc_delete").css("display","block");
		jQuery("#first_selected_cat").text(jQuery("#hdnlcat1").val());
		jQuery("#first_selected_cat_delete").attr("catid",jQuery("#hdnlc1").val());
	}
	if(jQuery("#hdnlcat2").val()!="")
	{
		jQuery("#second_lc_delete").css("display","block");
		jQuery("#second_selected_cat").text(jQuery("#hdnlcat2").val());
		jQuery("#second_selected_cat_delete").attr("catid",jQuery("#hdnlc2").val());
	}
	if(jQuery("#hdnlcat3").val()!="")
	{
		jQuery("#third_lc_delete").css("display","block");
		jQuery("#third_selected_cat").text(jQuery("#hdnlcat3").val());
		jQuery("#third_selected_cat_delete").attr("catid",jQuery("#hdnlc3").val());
	}
	jQuery(this).attr("total",total+1);
	jQuery("#add_cat_tr").css("display","none");
});
jQuery("#first_selected_cat_delete").live("click",function(){
	var total=parseInt(jQuery("#add_cat").attr("total"));
	jQuery("#add_cat").attr("total",total-1);
	jQuery("#hdnlc1").val("");
	jQuery("#hdnlcat1").val("");
	jQuery("#first_lc_delete").css("display","none");
	/*
	jQuery("#first_lc").css("display","block");
	jQuery("#second_lc").css("display","none");
	jQuery("#third_lc").css("display","none");
	*/
	jQuery("#first_cat_second_level").remove();
	jQuery("#first_cat_third_level").remove();
	jQuery("#cat_selected1").css("display","none");
	
	if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="" )
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");		
	}
	
});
jQuery("#second_selected_cat_delete").live("click",function(){
	var total=parseInt(jQuery("#add_cat").attr("total"));
	jQuery("#add_cat").attr("total",total-1);
	jQuery("#hdnlc2").val("");
	jQuery("#hdnlcat2").val("");
	jQuery("#second_lc_delete").css("display","none");
	/*
	jQuery("#second_lc").css("display","block");
	jQuery("#first_lc").css("display","none");
	jQuery("#third_lc").css("display","none");
	*/
	jQuery("#second_cat_second_level").remove();
	jQuery("#second_cat_third_level").remove();
	jQuery("#cat_selected1").css("display","none");
	
	if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="")
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");
	}
});
jQuery("#third_selected_cat_delete").live("click",function(){
	var total=parseInt(jQuery("#add_cat").attr("total"));
	jQuery("#add_cat").attr("total",total-1);
	jQuery("#hdnlc3").val("");
	jQuery("#hdnlcat3").val("");
	jQuery("#third_lc_delete").css("display","none");
	/*
	jQuery("#third_lc").css("display","block");
	jQuery("#first_lc").css("display","none");
	jQuery("#second_lc").css("display","none");
	*/
	jQuery("#third_cat_second_level").remove();
	jQuery("#third_cat_third_level").remove();
	jQuery("#cat_selected1").css("display","none");
	
	if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="")
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");
	}
});
</script>
<?php
function htmlallentities($str){
  $res = '';
  $strlen = strlen($str);
  for($i=0; $i<$strlen; $i++){
    $byte = ord($str[$i]);
    if($byte < 128) // 1-byte char
      $res .= $str[$i];
    elseif($byte < 192); // invalid utf8
    elseif($byte < 224) // 2-byte char
      $res .= '&#'.((63&$byte)*64 + (63&ord($str[++$i]))).';';
    elseif($byte < 240) // 3-byte char
      $res .= '&#'.((15&$byte)*4096 + (63&ord($str[++$i]))*64 + (63&ord($str[++$i]))).';';
    elseif($byte < 248) // 4-byte char
      $res .= '&#'.((15&$byte)*262144 + (63&ord($str[++$i]))*4096 + (63&ord($str[++$i]))*64 + (63&ord($str[++$i]))).';';
  }
  return $res;
}
?>
