<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();
//$objJSON = new JSON();
if($_REQUEST['id'] != "")
{
    $JSON = $objJSON->get_main_merchant_id($_REQUEST['id']);
    $JSON = json_decode($JSON);

    //echo $JSON;

    $array_values = $where_clause = $array = array();
    $where_clause['id'] = $JSON;
    $RSBN=$objDB->Show("merchant_user", $where_clause);
}



//echo base64_decode("MTIzNDU2");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="<?php echo ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>

<script type="text/javascript" src="<?=ASSETS_JS?>/a/jquery.form.js"></script>
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">

<!-- Message box popup -->

<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS ?>/a/fancybox/jquery.fancybox-buttons.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS ?>/a/fancybox/jquery.fancybox.css" media="screen" />

<!--<script type="text/javascript" src="<?=ASSETS_JS ?>/a/fancybox/jquery.fancybox-buttons.js"></script>-->
<script type="text/javascript" src="<?=ASSETS_JS ?>/a/fancybox/jquery.fancybox.js"></script>

<!-- End Message box popup -->

<style type="text/css" media="all">
		table#activationcode tr td
			 {
				border-right:1px dashed #ccc;
			 }
			 table.location_listing_area tr td
			 {
				border-right:1px dashed #ccc;
				border-bottom:1px dashed #ccc;
			 }
			.list_carousel {
				background: none repeat scroll 0 0 #FFFFFF;
				border: 4px solid #ccc;
				border-radius: 4px 4px 4px 4px;
				box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
				
				width: 59% !important;
				height:83px;
				overflow:hidden;
			}
			.list_carousel ul {
				margin: 0;
				padding: 0;
				list-style: none;
				display: block;
			}
			.list_carousel li {
				
				border:3px solid #CCCCCC;
				text-align: center;
				padding: 0;
				margin: 6px;
				display: block;
				float: left;
				
			}
			.list_carousel.responsive {
				width: auto;
				margin-left: 0;
			}
			.clearfix {
				float: none;
				clear: both;
			}
			.prev {
				float: left;
				margin-left: -32px;
				margin-top: -58px;
			}
			.next {
				float: right;
				margin-right: -32px;
				margin-top: -58px;
			}
			.locationgrid {
				width: 81%;
				height:45px;
			}
			.selected
			{
				font-family:entypo;
				font-size:3em;
				margin-left:5px;
				color:green;
				vertical-align:top;
			}
			.selected_delete
			{
				color: #FF0000;
				cursor: pointer;
				font-family: entypo;
				font-size: 3em;
				line-height: 0;
				margin-left: 10px;
				vertical-align: top;
			}
			.lc_delete
			{
				margin-bottom:5px;
			}
		</style>
                
<style>
    .disabled{
        color:#ABABAB !important;
    }
</style>
<style>
#tooltip h3 {
    color: black !important;
	font-size:14px !important;
    font-weight: lighter;
	background-color:hsl(0, 0%, 93%) !important;
	border:none !important;
}
#tooltip{
	z-index:9999 !important;
	opacity:1.0 !important;
}
    .active-filter{
          color : orange !important;
          }
          .disabledmedia{
                            background-color: #ABABAB !important;
                            background-image:url("/assets/images/a/button-corner-dis1.jpg") !important;
                        }
                        .disabledmedia:hover{
                            background-color: #ABABAB !important;
                                background-image:url("/assets/images/a/button-corner-hover1.png") !important;
                        }
		  </style>
                  <style>
    .disabled{   color:#ABABAB !important;}
/*    .loca_edit input{width: 300px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }*/
    .chk {width:40px!important;}
/*   .loca_edit select {padding: 10px; border: 1px solid #ccc; border-radius: 5px;width: 321px !important;}*/
   .hoursdata  {display: block;padding: 10px;width: 60%; border: 1px solid #ccc; border-radius: 5px;overflow: hidden;}
   .hoursdata > span { float: left; padding: 12px 4px 4px;  width: 90%;}
    .addhoursdiv {  margin-top: 10px;}
   
   .timeclass {  padding: 17px;border: 1px solid #ccc; border-radius: 5px;overflow: hidden; width: 60%;}
/*  .timeclass input { border: 1px solid #ccc !important;  padding: 5px; width: 90px;}*/
/*  .disabled {  width: 140px !important;}
  .timeclass .cancl{  width: 140px !important;}*/
 .loca_edit {

    margin: 0 auto;
    padding: 10px;
    width: 750px;
}
 /*start tables*/

.tableMerchant{cellpadding:2px;}
.tableMerchant:hover{background-color:#f5f5f5;}
.tableMerchant th{background-color:#3c9af4;height:15px;padding:5px;color:#fff;cursor:pointer;}
.tableMerchant tr:nth-child(even):hover{background: #a0d0fe}
.tableMerchant tr:nth-child(even){background: #e5ebf3}
.tableMerchant tr:nth-child(odd):hover{background: #a0d0fe}
.tableMerchant tr:nth-child(odd){background: #f2f6fb}
 /*end tables*/ 

</style>

 <link rel="stylesheet" href="<?=ASSETS_CSS ?>/a/jquery.tooltip.css" />

<script src="<?=ASSETS_JS ?>/a/jquery.tooltip.js" type="text/javascript"></script>

</head>

<body>
     <div id="container">

              <!---start header---->
	
		<?
		require_once(ADMIN_LAYOUT."/header.php");
		?>
		
		 <div id="dialog-message" title="Message Box" style="display:none">

    </div>
	
		<div id="contentContainer">

	
	<div  id="sidebarLeft">
		<?
		require_once(ADMIN_VIEW."/quick-links.php");
		?>
		<!--end of sidebar Left--></div>

		<div id="content">  
		<h1><?php echo $merchant_msg["addlocationdetail"]["Field_location_lable"];?></h1>
		<form id="add_location_detail_form" action="process.php"  method="post" enctype="multipart/form-data">
		<input type="hidden" name="hdn_mer_id" id="hdn_mer_id" value="<?php echo $_REQUEST['id'] ?>" />
		<input type="hidden" name="hdn_redirectpath" id="hdn_redirectpath" value="<?=WEB_PATH.'/admin/merchant_detail.php?id='.$_REQUEST['id'] ?>" />
		<!-- T_5 -->
            <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="" />
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
				
				<input type="hidden" name="hdnlc1" id="hdnlc1" value="" />
				<input type="hidden" name="hdnlcat1" id="hdnlcat1" value="" />
				
				<input type="hidden" name="hdnlc2" id="hdnlc2" value="" />
				<input type="hidden" name="hdnlcat2" id="hdnlcat2" value="" />
				
				<input type="hidden" name="hdnlc3" id="hdnlc3" value="" />
				<input type="hidden" name="hdnlcat3" id="hdnlcat3" value="" />
				
				<table width="100%"  border="0" cellspacing="2" cellpadding="2">
				  <!--<tr>
				    <td colspan="2" align="center" style="color:#FF0000; "><?=$_REQUEST['rmsg']?></td>
			      </tr>-->
				  <tr>
					<td width="20%" align="right" style="vertical-align:baseline;"><?php echo $merchant_msg["addlocationdetail"]["Field_select_location"];?></td>
					<td width="80%" align="left">
						<table width="100%"  border="0" cellspacing="1" cellpadding="10" class="tableMerchant" style="float: left" id="example1">
							<thead>
								<tr>
									<th width="1%;"><input style="margin-left:13px;" type="checkbox" name="chk_all_location" value="" id="chk_all_location" /></th>
									<!--<th align="left" class="tableDealTh" style="width:25%;">Location Name</th>-->
									<th align="left" class="tableDealTh" style="width:30%;">Address</th>
									<th align="left" class="tableDealTh" style="width:14%;">Phone Number</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							
						</table>
						<div style="float:left;height:280px;overflow-y:auto;width:100%;">	
						<table width="100%"  border="0" cellspacing="1" cellpadding="10" class="tableMerchant" style="float: left" id="example">
							<tr>
							<tbody>
							<?php
							$json_array = array();
							$records = array();
							$merchant_id = $_REQUEST['id'];
							$Sql = "SELECT * FROM locations WHERE created_by=$merchant_id  and active=1 and ((payment_method is null or payment_method='') or  (parking is null or parking='') or (categories is null or categories='') )";

							$RS_lok = $objDB->Conn->Execute($Sql);

							if($RS_lok->RecordCount()>0)
							{
								while($Row_lok = $RS_lok->FetchRow())
								{
									?>
									<tr>
										<td style="text-align:center;width:8%;">
											<input type="checkbox" name="chk_location[]" value="<?php echo $Row_lok['id'] ?>" id="<?php echo $Row_lok['id'].'_chk' ?>" />
										</td>
                                                                                <!--
										<td style="width:35%;"> 
											<?php echo $Row_lok['location_name']; ?>
										</td>
                                                                                -->
										<td style="width:64%;"> 
											<?php echo $Row_lok['address'].", ".$Row_lok['city'].", ".$Row_lok['state'].", ".$Row_lok['zip']; ?>
										</td > 
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
						</div>	
					</td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
				  </tr>
				  
				   
				  <tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_dining"];?></td>
					<td align="left">
						<input type="checkbox" name="chk_dining[]" value="Breakfast/Brunch" id="Breakfast/Brunch" /><?php echo "Breakfast/Brunch" ?>
						<input type="checkbox" name="chk_dining[]" value="Lunch" id="Lunch" /><?php echo "Lunch" ?>
						<input type="checkbox" name="chk_dining[]" value="Dinner" id="Dinner" /><?php echo "Dinner" ?>
						<input type="checkbox" name="chk_dining[]" value="Late Night" id="Late Night" /><?php echo "Late Night" ?>
						<input type="checkbox" name="chk_dining[]" value="Dessert" id="Dessert" /><?php echo "Dessert" ?>
					</td>
				  </tr>
	
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_reservation"];?></td>
					<td align="left">
						<input type="radio" name="rdo_reservation" value="Yes" /><?php echo "Yes" ?>
						<input type="radio" name="rdo_reservation" value="No" /><?php echo "No" ?>
                        <input type="radio" name="rdo_reservation" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_takeout"];?></td>
					<td align="left">
						<input type="radio" name="rdo_takeout" value="Yes" /><?php echo "Yes" ?>
						<input type="radio" name="rdo_takeout" value="No" /><?php echo "No" ?>
                        <input type="radio" name="rdo_takeout" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_ambience"];?></td>
					<td align="left">
						<input type="checkbox" name="chk_ambience[]" value="Classy" id="Classy" /><?php echo "Classy" ?>
						<input type="checkbox" name="chk_ambience[]" value="Romantic" id="Romantic" /><?php echo "Romantic" ?>
						<input type="checkbox" name="chk_ambience[]" value="Upscale" id="Upscale" /><?php echo "Upscale" ?>
						<input type="checkbox" name="chk_ambience[]" value="Touristy" id="Touristy" /><?php echo "Touristy" ?>
						<input type="checkbox" name="chk_ambience[]" value="Trendy" id="Trendy" /><?php echo "Trendy" ?>
						<input type="checkbox" name="chk_ambience[]" value="Casual" id="Casual" /><?php echo "Casual" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_attire"];?></td>
					<td align="left">
						<input type="checkbox" name="chk_attire[]" value="Dress" id="Dress" /><?php echo "Dress" ?>
						<input type="checkbox" name="chk_attire[]" value="Formal" id="Formal" /><?php echo "Formal" ?>
						<input type="checkbox" name="chk_attire[]" value="Casual" id="Casual" /><?php echo "Casual" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_goodfor"];?></td>
					<td align="left">
						<input type="checkbox" name="chk_goodfor[]" value="Groups" id="Groups" /><?php echo "Groups" ?>
						<input type="checkbox" name="chk_goodfor[]" value="kids" id="kids" /><?php echo "kids" ?>
						<input type="checkbox" name="chk_goodfor[]" value="Family with children’s" id="Family with children’s" /><?php echo $str = htmlallentities("Family with children’s");  ?>
						<input type="checkbox" name="chk_goodfor[]" value="Bar Scene" id="Bar Scene" /><?php echo "Bar Scene" ?>
						<input type="checkbox" name="chk_goodfor[]" value="Romance" id="Romance" /><?php echo "Romance" ?>
						<input type="checkbox" name="chk_goodfor[]" value="Special Occasion" id="Special Occasion" /><?php echo "Special Occasion" ?>
						<input type="checkbox" name="chk_goodfor[]" value="Entertaining Clients" id="Entertaining Clients" /><?php echo "Entertaining Clients" ?>
						<input type="checkbox" name="chk_goodfor[]" value="Outdoor seating" id="Outdoor seating" /><?php echo "Outdoor seating" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_payment_method"];?></td>
					<td align="left">
                                                <input type="checkbox" name="chk_payment_method[]" value="Cash" id="Cash" /><?php echo "Cash" ?>
						<input type="checkbox" name="chk_payment_method[]" value="Cash only" id="Cash only" /><?php echo "Cash only" ?>
						<input type="checkbox" name="chk_payment_method[]" value="Visa" id="Visa" /><?php echo "Visa" ?>
						<input type="checkbox" name="chk_payment_method[]" value="Master card" id="Master card" /><?php echo "Master card" ?>
						<input type="checkbox" name="chk_payment_method[]" value="AMEX" id="AMEX" /><?php echo "AMEX" ?>
						<input type="checkbox" name="chk_payment_method[]" value="Discover" id="Discover" /><?php echo "Discover" ?>
						<input type="checkbox" name="chk_payment_method[]" value="Dinerclub" id="Dinerclub" /><?php echo "Dinerclub" ?>
						<input type="checkbox" name="chk_payment_method[]" value="Paypal" id="Paypal" /><?php echo "Paypal" ?>
						<input type="checkbox" name="chk_payment_method[]" value="Square" id="Square" /><?php echo "Square" ?>
						<input type="checkbox" name="chk_payment_method[]" value="Debit card" id="Debit card" /><?php echo "Debit card" ?>
					</td>
				</tr>
				
			
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_pet"];?></td>
					<td align="left">
						<input type="radio" name="rdo_pet" value="Yes" /><?php echo "Yes" ?>
						<input type="radio" name="rdo_pet" value="No" /><?php echo "No" ?>
                        <input type="radio" name="rdo_pet" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_wheelchair"];?></td>
					<td align="left">
						<input type="radio" name="rdo_wheelchair" value="Yes" /><?php echo "Yes" ?>
						<input type="radio" name="rdo_wheelchair" value="No" /><?php echo "No" ?>
                                                <input type="radio" name="rdo_wheelchair" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_wifi"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_wifi"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<input id="wifi_free" type="radio" name="rdo_wifi" value="Free" /><?php echo "Free" ?>
						<input id="wifi_paid" type="radio" name="rdo_wifi" value="Paid" /><?php echo "Paid" ?>
						<input id="wifi_no" type="radio" name="rdo_wifi" value="No" /><?php echo "No" ?>
                                                <input type="radio" name="rdo_wifi" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_tv"];?></td>
					<td align="left">
						<input id="tv_yes" type="radio" name="rdo_tv" value="Yes" /><?php echo "Yes" ?>
						<input id="tv_no" type="radio" name="rdo_tv" value="No" /><?php echo "No" ?>
                                                <input type="radio" name="rdo_tv" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_airconditioned"];?></td>
					<td align="left">
						<input id="ac_yes" type="radio" name="rdo_airconditioned" value="Yes" /><?php echo "Yes" ?>
						<input id="ac_no" type="radio" name="rdo_airconditioned" value="No" /><?php echo "No" ?>
                                                <input type="radio" name="rdo_airconditioned" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_smoking"];?></td>
					<td align="left">
						<input id="smoking_yes" type="radio" name="rdo_smoking" value="Yes" /><?php echo "Yes" ?>
						<input id="smoking_no" type="radio" name="rdo_smoking" value="No" /><?php echo "No" ?>
						<input id="smoking_outdoor" type="radio" name="rdo_smoking" value="Outdoor" /><?php echo "Outdoor" ?>
                        <input type="radio" name="rdo_smoking" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_alcohol"];?></td>
					<td align="left">
						<input id="Beer and wine" type="radio" name="rdo_alcohol" value="Beer and wine" /><?php echo "Beer and wine" ?>
						<input id="Full bar" type="radio" name="rdo_alcohol" value="Full bar" /><?php echo "Full bar" ?>
						<input type="radio" name="rdo_alcohol" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_noiselevel"];?></td>
					<td align="left">
                                                <input id="noiselevel_quite" type="radio" name="rdo_noiselevel" value="Quite" /><?php echo "Quiet" ?>
						<input id="noiselevel_typical" type="radio" name="rdo_noiselevel" value="Typical" /><?php echo "Typical" ?>
						<input id="noiselevel_loud" type="radio" name="rdo_noiselevel" value="Loud" /><?php echo "Loud" ?>
                                                <input type="radio" name="rdo_noiselevel" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_minimum_age"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_age"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<input type="text" name="minimum_age" id="minimum_age" size="10"/>
					</td>
				</tr>
				
				<tr>
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_will_deliver"];?></td>
					<td align="left">
						<input id="deliver_yes" type="radio" name="rdo_will_deliver" value="Yes" /><?php echo "Yes" ?>
						<input id="deliver_no" type="radio" name="rdo_will_deliver" value="No" /><?php echo "No" ?>
                                                <input type="radio" name="rdo_will_deliver" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				
				<tr class="delivery_area" style="display:none;">
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_minimum_order"];?></td>
					<td align="left">
						<input type="text" name="minimum_order" id="minimum_order" />
					</td>
				</tr>
				
				<tr class="delivery_area" style="display:none;">
					<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_delivery_area"];?></td>
					<td align="left">
						From :&nbsp;<input type="text" name="delivery_from" id="delivery_from" />&nbsp;
						To :&nbsp;<input type="text" name="delivery_to" id="delivery_to" />
					</td>
				</tr>
				
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_caters"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_cater"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<input id="cater_yes" type="radio" name="rdo_caters" value="Yes" /><?php echo "Yes" ?>
						<input id="cater_no" type="radio" name="rdo_caters" value="No" /><?php echo "No" ?>
                                                <input type="radio" name="rdo_caters" value="Not Applicable" /><?php echo "Not Applicable" ?>
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_services"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_services"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<textarea name="services" id="services" class="location_services_textarea" placeholder="Optional"></textarea>
						<br/><span id="numeric"><span>Add 15 Services | Comma separated</span>
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_amenities"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_amenities"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<textarea name="amenities" id="amenities" class="location_amenities_textarea" placeholder="Optional"></textarea>
						<br/><span id="numeric"><span>Add 15 Services | Comma separated</span>
					</td>
				</tr>
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<!-- T_5 -->
						<input type="submit" name="btnAddLocationDetail" value="<?php echo $merchant_msg['index']['btn_save'];?>" id="btnAddLocationDetail">
						<!--// 369-->
                         <script>function btncanLocation(){                                                
                                                window.location="<?=WEB_PATH.'/admin/merchant_detail.php?id='.$_REQUEST['id'] ?>";}</script>
							<input type="button" name="btnCancel" id="btnCancel" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanLocation();">
                        <!--// 369-->
						<!-- T_5 -->
					</td>
				  </tr>
				</table>
				 </form>
		<div class="clear">&nbsp;</div><!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
<script>
try
{	
jQuery('input:radio[name="rdo_will_deliver"]').change(function(){
	//alert("hi");
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
}
catch(e)
{
	alert(e.message);
}
jQuery('#btnAddLocationDetail').ajaxForm({ 
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

jQuery('#btnAddLocationDetail').click(function(){

	var flag="";
	var msgbox="";
	var currencyReg = /^\$?[0-9]+(\.[0-9][0-9])?$/;
	var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
	
	var locationcount=jQuery('input:checkbox[name="chk_location[]"]:checked').size();
	if(locationcount==0)
	{
		//alert("<?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_location"];?>");
		//return false;
		msgbox+="<div><?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_location"];?></div>";
		flag="false";
		
	}

	var paymentcount=jQuery('input:checkbox[name="chk_payment_method[]"]:checked').size();
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
		//alert("blank");
	}
	else
	{
		//if(hastagRef.test(services))
		//{
			//alert("reg exp success");
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
			//alert("reg exp failer");
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
	
	var head_msg="<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message</div>"
	var content_msg="<div style='text-align:left;margin-top:10px;padding:5px;'>"+msgbox+"</div>";
	var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
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
	
	if(jQuery("#hdnlc1").val("")=="" && jQuery("#hdnlc2").val("")=="" && jQuery("#hdnlc3").val("")=="")
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
	
	if(jQuery("#hdnlc1").val("")=="" && jQuery("#hdnlc2").val("")=="" && jQuery("#hdnlc3").val("")=="")
	{
		jQuery("#first_lc").css("display","block");
		jQuery("#second_lc").css("display","none");
		jQuery("#third_lc").css("display","none");
		jQuery("#cat_selected1").css("display","none");
		jQuery("#add_cat").attr("total","1");
	}
});

jQuery("#chk_all_location").change(function(){
    //alert(jQuery(this).is(':checked'));
	if(jQuery(this).is(':checked')) 
	{ // check select status
		jQuery("input[id$='_chk']").each(function() {
			this.checked = true;              
		});
	}
	else
	{
		jQuery("input[id$='_chk']").each(function() {
			this.checked = false;                    
		});         
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
