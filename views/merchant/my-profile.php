<?php
/**
 * @uses my profile
 * @used in pages : redeem-deal.php,header.php,my-account-left.php,profile-left.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');

$array_where['id'] = $_SESSION['merchant_id'];

$row_data_result = $RS = $objDB->Show("merchant_user", $array_where);

$data_sub_merchant_id = $RS->fields['merchant_parent'];
//$row_data_result = $RS->FetchRow();

/*$sub_merchant_id="select merchant_parent from merchant_user where id = '".$_SESSION['merchant_id']."'";
$result_sub_merchant_id=mysql_query($sub_merchant_id);

$data_sub_merchant_id = mysql_fetch_assoc($result_sub_merchant_id);
$my_query="select * from merchant_user where id=".$_SESSION['merchant_id'];
$data_result=mysql_query($my_query);
$row_data_result = mysql_fetch_assoc($data_result);*/


//get parent merchant id for employee
$arr=file(WEB_PATH.'/merchant/process.php?getmainmercahnt_id=yes&mer_id='.$_SESSION['merchant_id']);
if(trim($arr[0]) == "")
{
	unset($arr[0]);
	$arr = array_values($arr);
}
$json = json_decode($arr[0]);
$main_merchant_id = $json->main_merchant_id;
			
// get billing package data
$array_where_mb['merchant_id'] = $main_merchant_id;
$RS_mb = $objDB->Show("merchant_billing", $array_where_mb);
$array_where_bp['id'] = $RS_mb->fields['pack_id'];
$RS_bp = $objDB->Show("billing_packages", $array_where_bp);
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | My Profile</title>
<?php //require_once(MRCH_LAYOUT."/head.php"); ?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
<script type="text/javascript" src="<?= ASSETS_JS ?>/bootstrap.min.js"></script>	
<link href="<?=ASSETS_CSS?>/m/template.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=ASSETS ?>/tinymce/tiny_mce.js"></script>
<?php
if($data_sub_merchant_id['merchant_parent'] == "0")
{ ?>
    <script language="javascript" src="<?=ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
<?php } ?>

<script type="text/javascript" src="<?=ASSETS_JS?>/m/jquery.form.js"></script>

<script language="javascript">
/****** submit form without refresh **********/
$(document).ready(function() { 
    // bind form using ajaxForm 
    $('#edit_profile_form').ajaxForm({
        beforeSubmit:processLoginorNot,
        dataType:  'json', 
        success:   processEditProfileJson 
    });
    jQuery("#expanderHeadPersonal").click(function(){
		jQuery("#expanderContentPersonal").slideToggle();
		if (jQuery("#expanderSignPersonal").text() == "+"){
			jQuery("#expanderSignPersonal").html("−")
		}
		else {
			jQuery("#expanderSignPersonal").text("+")
		}
	});	
	jQuery("#expanderHeadLocation").click(function(){
		jQuery("#expanderContentLocation").slideToggle();
		if (jQuery("#expanderSignLocation").text() == "+"){
			jQuery("#expanderSignLocation").html("−")
		}
		else {
			jQuery("#expanderSignLocation").text("+")
		}
	});	
	jQuery("#expanderHeadAbout").click(function(){
		jQuery("#expanderContentAbout").slideToggle();
		if (jQuery("#expanderSignAbout").text() == "+"){
			jQuery("#expanderSignAbout").html("−")
		}
		else {
			jQuery("#expanderSignAbout").text("+")
		}
	});		
	jQuery("#expanderHeadAddress").click(function(){
		jQuery("#expanderContentAddress").slideToggle();
		if (jQuery("#expanderSignAddress").text() == "+"){
			jQuery("#expanderSignAddress").html("−")
		}
		else {
			jQuery("#expanderSignAddress").text("+")
		}
	});
	jQuery("#expanderHeadPackage").click(function(){
		jQuery("#expanderContentPackage").slideToggle();
		if (jQuery("#expanderSignPackage").text() == "+"){
			jQuery("#expanderSignPackage").html("−")
		}
		else {
			jQuery("#expanderSignPackage").text("+")
		}
	});
});
function processEditProfileJson(data) { 
	if(data.status == "true")
	{

		//window.location.href='<?=WEB_PATH?>/merchant/my-profile.php';
		var msg_box = data.message;
		var head_msg="<div class='head_msg'>Message</div>"
		var content_msg="<div class='content_msg'>"+msg_box+"</div>";
		var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		    
		jQuery.fancybox({
			content:jQuery('#dialog-message').html(),
			type: 'html',
			openSpeed  : 300,
			closeSpeed  : 300,
			minWidth : 300,
			changeFade : 'fast',  
			helpers: {
				overlay: {
				opacity: 0.3
				} // overlay
			}
		});
		return false;
	}
	else
	{
		//alert(data.message);
	} 
}
function processLoginorNot()
    {
                        var flag=0;
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
                                    flag=1;
                                    
                                }
                                
                           }
                     });
                    
                     if(flag == 1)
                        {
                           return false;
                        }
                        else
                        {
                                 return true;
                        }
    }
// 369


// 369
jQuery(document).ready(function(){
window.tinymce.dom.Event.domLoaded = true;
tinyMCE.init({
		// General options
		//mode : "textareas",
		mode : "exact",
		elements:"aboutus,aboutus_short",
		theme : "advanced",
		//plugins : "lists,searchreplace",
		valid_elements :'p,br',
		// Theme options
		//theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		
		//theme_advanced_buttons1 : "replace,|,bullist,numlist,|,outdent,indent,blockquote,|,sub,sup,|,charmap",
		theme_advanced_buttons1 : "",
		
		//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
               
		// Example content CSS (should be your site CSS)
		content_css : "<?=ASSETS ?>/tinymce/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "<?=ASSETS ?>/tinymce/lists/template_list.js",
		external_link_list_url : "<?=ASSETS ?>/tinymce/lists/link_list.js",
		external_image_list_url : "<?=ASSETS ?>/tinymce/lists/image_list.js",
		media_external_list_url : "<?=ASSETS ?>/tinymce/lists/media_list.js",
		
		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		},
                charLimit:155,
		setup : function(ed) {
        //peform this action every time a key is pressed
        ed.onKeyDown.add(function(ed, e) {
		if(tinyMCE.activeEditor.id=="aboutus_short")
                {
			var textarea = tinyMCE.activeEditor.getContent(); 
			//alert(textarea);
			var lastcontent=textarea;
			//define local variables
			var tinymax, tinylen, htmlcount;
			//manually setting our max character limit
			tinymax = ed.settings.charLimit;
			//grabbing the length of the curent editors content
			tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
			//setting up the text string that will display in the path area
			//htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
			//if the user has exceeded the max turn the path bar red.
			//alert(tinylen);
			if((tinylen+1>tinymax && e.keyCode == 37) || (tinylen+1>tinymax && e.keyCode == 38) || (tinylen+1>tinymax && e.keyCode == 39) || (tinylen+1>tinymax && e.keyCode == 40))
			 {
				return true;
			 }
			if (tinylen+1>tinymax && e.keyCode != 8){
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
		}		
        });
		ed.onKeyUp.add(function(ed, e) {
		
			//alert("up");
			var textarea = tinyMCE.activeEditor.getContent(); 
			//alert(textarea);
			var lastcontent=textarea;
			//define local variables
			var tinymax, tinylen, htmlcount;
			//manually setting our max character limit
			tinymax = ed.settings.charLimit;
			//grabbing the length of the curent editors content
			tinylen = ed.getContent().replace(/(<([^>]+)>)/ig,"").length;
			//setting up the text string that will display in the path area
			//htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
			
			var l=tinymax-tinylen;
                        
                        if(tinyMCE.activeEditor.id=="aboutus_short")
                            document.getElementById("abt_us_remaining").innerHTML=l+" characters remaining";
                        
		});
		}
	});

jQuery(".textarea_loader").css("display","none");
jQuery(".textarea_container").css("display","block");

                } );
				
			
</script>
</head>

<body class="my-profile">

<div id="dialog-message" title="Message Box" style="display:none">
</div>
<div id="redeemption-message" title="Message Box" style="display:none">
	<div class='head_msg'>Message</div>
	    <div class='content_msg'>
			The redemption fee for campaign is based on the discounted campaign value. 
			<center>
				<table cellspacing="5" cellpadding="5" style="">
					<thead>
						<tr>
							<th>				
								Discounted Campaign Value
							</th>												
							<th>
								Amount
							</th>
						</tr>
					</thead>
					<tbody>	
					<?php 
					$Sql_r_f_c = "SELECT * FROM redeemption_fee_charge";
					$Rs_r_f_c = $objDB->Conn->Execute($Sql_r_f_c);
					$count = 0;
					while($Row_r_f_c = $Rs_r_f_c->FetchRow())
					{
					?>
					<tr>
						<td >
							<?php echo $Row_r_f_c['start_value']."-".$Row_r_f_c['end_value']; ?>
						</td>						
						<td style="text-align:right">
							<?php 
								if($Row_r_f_c['type']=="amount")
									echo "$".$Row_r_f_c['amount_value']; 
								else
									echo $Row_r_f_c['amount_value']."%"; 
							?>
						</td>
					</tr>
					<?php 
					}
					?>
					</tbody>
				</table>
			</center>
<br/>Fee Example:  (If it is 5% for your discount campaign value)
<br/>$5 off a $25 purchase
<br/>$20 x 5% = $1
<br/>The cost of a discounted campaign redeemption fee will be charged to the merchant's<br/> credit card on file weekly for coupons redeemed the previous week.
		</div>
		<div>
			<hr>
			<input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'>
		</div>
</div>
<div >
   
<!--start header--><div>

		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	<div id="content">
	
	<table id="table_spacer" width="100%"  border="0" cellspacing="2" cellpadding="2">
	<tr>
    <td width="25%" align="left" valign="top" >
	<?
		require_once(MRCH_LAYOUT."/profile-left.php");
	?>
	</td>
    <td width="75%" align="left" valign="top">
		<form action="<?=WEB_PATH?>/merchant/process.php" method="post" id="edit_profile_form">
                    <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?=$RS->fields['merchant_icon']?>" />
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
		
		<!--
		<table width="100%"  border="0" cellspacing="2" cellpadding="2">
			<tr>				
				<td class="table_errore_message" align="left" id="msg_div"><?php if(isset($_SESSION['msg'])) echo $_SESSION['msg'];?>&nbsp;</td>
			</tr>
		</table>
		-->
		<h4 id="expanderHeadPersonal" style="cursor:pointer;" class="collaspable_div" >
			My Info <span id="expanderSignPersonal" class="collaspable_sign">+</span>
		</h4>
		<div id="expanderContentPersonal" style="display:none">
			<table width="100%"  border="0" id="table_spacer">			
				<tr>
					<td width="40%"><?php echo $merchant_msg['profile']['Field_first_name']; ?></td>
					<td width="60%">
					<input type="text" name="firstname" id="firstname" class="profile_input" value="<?=$RS->fields['firstname']?>">
					</td>
				</tr>
				<tr>
					<td><?php echo $merchant_msg['profile']['Field_last_name']; ?></td>
					<td><input type="text" name="lastname" id="lastname" class="profile_input" value="<?=$RS->fields['lastname']?>"></td>
				</tr>
				<tr>
					<td><?php echo $merchant_msg['profile']['Field_phone_no']; ?></td>
					<?php 
					$mobileno=$RS->fields['phone_number'];
						$area_code=substr($mobileno,4,3);
						 $mobileno2=substr($mobileno,8,3);
				 $mobileno1=substr($mobileno,12,4);  
					   //$mobileno1=substr($mobileno,8,4);
					   ?>
					<td>
					<select name="mobile_country_code" id="mobile_country_code" style="display:none;">
						<option value="001">001</option>
					</select>
						<input type="text" name="mobileno_area_code" id="mobileno_area_code" class="mobile_code1"  value="<?php echo $area_code;?>" maxlength="3">-
					<input type="text" name="mobileno2" id="mobileno2"  class="mobile_code1" value="<?php echo $mobileno2;?>" maxlength="3">-
					<input type="text" name="mobileno" id="mobileno"  class="mobile_code2" value="<?php echo $mobileno1;?>" maxlength="4">
					
					</td>
				</tr>	
				<tr>
					<td>&nbsp;</td>
					<td style="padding:20px;">
						<input type="submit" name="btnUpdatePersonalInfo" value="<?php echo $merchant_msg['index']['btn_save'];?>" onClick="mycall();" id="btnUpdatePersonalInfo" > &nbsp;&nbsp;							
						 <input type="button" class="cancelprofile" id="btncancelPersonalInfo" name="btncancelPersonalInfo" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanprofile()"  >
						 <!-- <div><img class="offer_loader" src="<?php echo WEB_PATH."/" ?>images/24.GIF" style=""></div> -->
					</td>
				</tr>
			</table>
		</div>
		<?php
		if($_SESSION['merchant_info']['merchant_parent'] == 0 )
		{
			$arr_mp=array();	
			$arr_mp['created_by'] = $_SESSION['merchant_id'];
			$arr_mp['active'] = 1;
			$RSStore = $objDB->Show("locations", $arr_mp);
			
			?>
			
			<h4 id="expanderHeadLocation" style="cursor:pointer;" class="collaspable_div" >
				Assigned Location <span id="expanderSignLocation" class="collaspable_sign">+</span>
			</h4>
			<div id="expanderContentLocation" style="display:none">
				<table width="100%"  border="0" cellspacing="2" cellpadding="2" id="table_spacer">			
					<tr>
						<td width="40%"><?php echo $merchant_msg['profile']['Field_assigned_location']; ?></td>
						<td width="60%">
							<select id="redeem_location" name="redeem_location">
								<option value="0">Select Location</option>
								<?php	
								if($RSStore->RecordCount()>0)
								{ 
									while($Rowlc = $RSStore->FetchRow())
									{
									?>
										<option <?php if($_SESSION['merchant_info']['redeem_location']==$Rowlc['id']) echo "selected='selected'" ?> value="<?php echo $Rowlc['id'] ?>">
											<?php 
												$array_where = array();
												$array_where['id'] = $Rowlc['state'];
												$RS_state = $objDB->Show("state", $array_where);

												$array_where = array();
												$array_where['id'] = $Rowlc['city'];
												$RS_city = $objDB->Show("city", $array_where);

												$location_string = $Rowlc['address'] . ", " . $RS_city->fields['name'] . ", " . $RS_state->fields['short_form'] . ", " . $Rowlc['zip'];
												//echo $Rowlc['address'].", ".$Rowlc['city'].", ".$Rowlc['state'].", ".$Rowlc['zip']
												echo $location_string;
											?>
										</option>
									<?php	
									}
								}
								?>	
							</select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td style="padding:20px;">
							<input type="submit" name="btnUpdateLocationInfo" value="<?php echo $merchant_msg['index']['btn_save'];?>" onClick="mycall();" id="btnUpdateLocationInfo" > &nbsp;&nbsp;							
							 <input type="button" class="cancelprofile" id="btncancelPersonalInfo" name="btncancelPersonalInfo" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanprofile()"  >
							 <!-- <div><img class="offer_loader" src="<?php echo WEB_PATH."/" ?>images/24.GIF" style=""></div> -->
						</td>
					</tr>
				</table>
			</div>
		
		
		<?php	
		}
		else
		{
			$arr_tmp1 = array();
			$arr_tmp1['merchant_user_id'] = $_SESSION['merchant_id'];
			$RS_tmp1 = $objDB->Show("merchant_user_role",$arr_tmp1);
			
			$arr_mp=array();	
			$arr_mp['id'] = $RS_tmp1->fields['location_access'];
			$RSStore = $objDB->Show("locations", $arr_mp);
			
			$mobileno=substr($RSStore->fields['phone_number'],4);
		?>
			<h4 id="expanderHeadLocation" style="cursor:pointer;" class="collaspable_div" >
				Assigned Location <span id="expanderSignLocation" class="collaspable_sign">+</span>
			</h4>
			<div id="expanderContentLocation" style="display:none">
				<table width="100%"  border="0" cellspacing="2" cellpadding="2"id="table_spacer">			
					<tr>
						<td width="20%"><?php echo $merchant_msg['profile']['Field_assigned_location']; ?></td>
						<td width="60%">
								<?php	
								if($RSStore->RecordCount()>0)
								{ 
									while($Rowlc = $RSStore->FetchRow())
									{
									?>
										<?php echo $Rowlc['address'].", ".$Rowlc['city'].", ".$Rowlc['state'].", ".$Rowlc['zip'].", ".$mobileno?>
									<?php	
									}
								}
								?>
						</td>
					</tr>
				</table>
			</div>	
		<?php
		}
		?>
		<?php 
		if($_SESSION['merchant_info']['merchant_parent'] == 0 )
		{
		?>
		<h4 id="expanderHeadAddress" style="cursor:pointer;" class="collaspable_div" >
			Business Info <span id="expanderSignAddress" class="collaspable_sign">+</span>
		</h4>
		<div id="expanderContentAddress" style="display:none">
			<table width="100%"  border="0" cellspacing="2" cellpadding="2" id="table_spacer">
				<tr>
    <td  width="40%"><?php echo $merchant_msg['profile']['Field_address']; ?></td>
    <td  width="60%">
	<!-- <input type="text" name="address" id="address" class="profile_input" value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['address']; }else{ echo $row_data_result['address'];}?>"> -->
		<textarea name="address" id="address" class="profile_address"><?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo trim($RS->fields['address']);}else{echo trim($row_data_result['address']);}?></textarea>
	</td>
  </tr>
  
  
   <tr style="display:none;">
    <td><?php echo $merchant_msg['profile']['Field_country']; ?></td>
    <td>
		<?php
		//echo $RS->fields['country']."-".$RS->fields['state']."-".$RS->fields['city'];
		$array_where = array();
		$array_where['active'] = 1;
		$RS_country = $objDB->Show("country", $array_where," Order By `name` ASC ");
		?>
	<select name="country" id="country" >
	   <option value='0'>Please Select</option>
	   <?php
			if($RS_country->RecordCount()>0)
			{
				while($Row = $RS_country->FetchRow())
				{
				?>
					<option <?php if($Row['id']==$RS->fields['country']) {echo "selected";} ?> value="<?php echo $Row['id'] ?>"><?php echo $Row['name'] ?></option>
				<?php
				}
			}
			?>
	</select>
	
    </td>
  </tr>
  <tr>
    <td><?php echo $merchant_msg['profile']['Field_state']; ?></td>
    <td>
		<?php
		$array_where = array();
		$array_where['country_id'] = $RS->fields['country'];
		$array_where['active'] = 1;
		$RS_state = $objDB->Show("state", $array_where," Order By `name` ASC ");
		?>
	    <select name="state" id="state" class="" style="display:block">
			<option value='0'>Please Select</option>
			<?php
			if($RS_state->RecordCount()>0)
			{
				while($Row = $RS_state->FetchRow())
				{
				?>
					<option <?php if($Row['id']==$RS->fields['state']) {echo "selected";} ?> value="<?php echo $Row['id'] ?>"><?php echo $Row['name'] ?></option>
				<?php
				}
			}
			?>
		</select>
	
	
    </td>
  </tr>
  <tr>
    <td><?php echo $merchant_msg['profile']['Field_city']; ?></td>
    <td>
		<?php
		$array_where = array();
		$array_where['state_id'] = $RS->fields['state'];
		$array_where['active'] = 1;
		$RS_city = $objDB->Show("city", $array_where," Order By `name` ASC ");
		?>
		<select name="city" id="city" class="" style="display:block">
			<option value='0'>Please Select</option>
			<?php
			if($RS_city->RecordCount()>0)
			{
				while($Row = $RS_city->FetchRow())
				{
				?>
					<option <?php if($Row['id']==$RS->fields['city']) {echo "selected";} ?> value="<?php echo $Row['id'] ?>"><?php echo $Row['name'] ?></option>
				<?php
				}
			}
			?>
		</select>
	</td>
  </tr>
  <tr>
    <td><?php echo $merchant_msg['profile']['Field_zipcode']; ?></td>
    <td><input type="text" name="zipcode" id="zipcode" class="profile_input" value="<?php if($data_sub_merchant_id['merchant_parent'] == "0"){echo $RS->fields['zipcode']; }else{ echo $row_data_result['zipcode'];}?>"></td>
  </tr>
 
  
  
			<tr>
					<td>&nbsp;</td>
					<td style="padding:20px;">
						<input type="submit" name="btnUpdateAddressInfo" value="<?php echo $merchant_msg['index']['btn_save'];?>" onClick="mycall();" id="btnUpdateAddressInfo" > &nbsp;&nbsp;							
						 <input type="button"  class="cancelprofile" id="btncancelAddressInfo" name="btncancelAddressInfo" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanprofile()"  >
						 <!-- <div><img class="offer_loader" src="<?php echo WEB_PATH."/" ?>images/24.GIF" style=""></div> -->
					</td>
				</tr>
			</table>
		</div>
		
		<?php
		}
		?>
		<?php 
		if($_SESSION['merchant_info']['merchant_parent'] == 0 )
		{
		?>
		
		<h4 id="expanderHeadAbout" style="cursor:pointer;" class="collaspable_div" >
			Business Details <span id="expanderSignAbout" class="collaspable_sign">+</span>
		</h4>
		<div id="expanderContentAbout" style="display:none">
			<table width="100%"  border="0" cellspacing="2" cellpadding="2" id="table_spacer">
				<tr>
					<td><?php echo $merchant_msg['profile']['Field_business_name']; ?></td>
					<td><input type="text" name="business" id="business" class="profile_input" value="<?=$RS->fields['business']?>"></td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_business_tag']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_business_tags"]; ?>">&nbsp;&nbsp;&nbsp;</span></td></td>
					<td>
					
					<!--<input type="text" name="business_tags" id="business_tags" class="profile_input" value="<?=$RS->fields['business_tags']?>">-->
					<textarea name="business_tags" id="business_tags" class="profile_input" ><?php echo $RS->fields['business_tags']?></textarea>
					<br/><span id="numeric"><?php  echo  $merchant_msg["profile"]["Field_business_tag_add_upto"]; ?><span>
					</td>
				  </tr>
				   <tr>
					<td><?php echo $merchant_msg['profile']['Field_about_us_short']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_aboutus_short"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>
					<td align="left" class="profile_textare_td">
						<div align="center" class="textarea_loader">
							<img   src="<?php echo ASSETS_IMG."/32" ?>.GIF" class="defaul_table_loader" />
						</div>
						<div class="textarea_container" style="display: none;">	
							<textarea id="aboutus_short" name="aboutus_short" rows="5" cols="25" class="profile_textarea_short"><?=$RS->fields['aboutus_short']?></textarea>
						</div>
						<span id="abt_us_remaining" class="abt_us_remaining" class="profile_abt_us_count" >Maximum 155 characters | no HTML allowed</span>
					</td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_about_us']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_aboutus"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>
					<td align="left" class="profile_textare_td">
						<div align="center" class="textarea_loader">
							<img   src="<?php echo ASSETS_IMG."/32" ?>.GIF" class="defaul_table_loader" />
						</div>
						<div class="textarea_container" style="display: none;">	
							<textarea id="aboutus" name="aboutus" rows="5" cols="25" class="profile_textarea_short" ><?=$RS->fields['aboutus']?></textarea>
						</div><span id="abt_us_remaining">no HTML allowed</span>

					</td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_merchant_icon']; ?></td>
					<td><div class="cls_left">
						<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
						<div id="upload" >
						<span  >Upload Business Logo
						</span> 
						</div>
						</div>
					</td>
				  </tr>
				  
				  <tr><td align="right">&nbsp; </td>
						<td>

								<span id="status" ></span>
								<br/>

								<ul id="files" >

								 </ul>
						</td>
				  </tr>	
				  <tr>
					<td>&nbsp;</td>
					<td style="padding:20px;">
						<input type="submit" id="btnUpdateAboutInfo" name="btnUpdateAboutInfo" value="<?php echo $merchant_msg['index']['btn_save'];?>" onClick="mycall();"  > &nbsp;&nbsp;				
						 <input type="button"  class="cancelprofile" id="btncancelAboutInfo" name="btncancelAboutInfo" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanprofile()"  >
						 <!-- <div><img class="offer_loader" src="<?php echo WEB_PATH."/" ?>images/24.GIF" style=""></div> -->
					</td>
				</tr>
			</table>
		</div>
		
		<?php
		}
		?>
		
		<?php 
		if($RS_mb->RecordCount()>0 && $_SESSION['merchant_info']['merchant_parent'] == 0)
		{
		?>
		<h4 id="expanderHeadPackage" style="cursor:pointer;" class="collaspable_div" >
			Subscription Plan <span id="expanderSignPackage" class="collaspable_sign">+</span>
		</h4>
		<div id="expanderContentPackage" style="display:none">
			<table width="100%"  border="0" cellspacing="2" cellpadding="2" id="table_spacer">
				<?php
				if($RS->fields['enable_online_campaign']==1 && $RS->fields['visible_api_key_in_profile']==1)
				{
				?>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_apikey']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_apikey"]; ?>">&nbsp;&nbsp;&nbsp;</span></td></td>
					<td>
					<input type="text" name="apikey" id="apikey" disabled value="<?=$RS->fields['apikey']?>">
					</td>
				  </tr>
				<?php
				}
				?>
				<td><?php echo ($RS_mb->fields['is_canceled'] == 1)?'This will cancel at period end.':'' ?></td>
				<td>
					<div class="merchant_subscr" id="cancel_subscr_package" data-id="<?php echo $RS_bp->fields['id']; ?>" data-iscancel="<?php echo $RS_mb->fields['is_canceled']; ?>">Cancel Subscription</div>
					<div class="merchant_subscr" id="subscr_package" data-id="<?php echo $RS_bp->fields['id']; ?>">Change Subscription</div>
				</td>
				<tr>
				</tr>
				
				<tr>
					<td><?php echo $merchant_msg['profile']['Field_package']; ?></td>
					<td><input type="text" name="pack_name" id="pack_name" disabled class="profile_input" value="<?=$RS_bp->fields['pack_name']?>"></td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_package_price']; ?></td>
					<?php 
						$pack_price="";
						if($RS_bp->fields['price']<=0)
						{
							$pack_price = "To be decided";
						}
						else
						{
							$pack_price = $RS_bp->fields['price'];
						}
						
					?>
					<td><input type="text" name="pack_price" id="pack_price" disabled class="profile_input" value="<?=$pack_price?>"></td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_number_of_locations']; ?></td>
					<td><input type="text" name="no_of_loca" id="no_of_loca" disabled class="profile_input" value="<?=$RS_bp->fields['no_of_loca']?>"></td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_number_of_active_campaigns_per_location']; ?></td>
					<td><input type="text" name="no_of_active_camp_per_loca" id="no_of_active_camp_per_loca" disabled class="profile_input" value="<?=$RS_bp->fields['no_of_active_camp_per_loca']?>"></td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_total_number_of_campaigns_per_month']; ?></td>
					<td><input type="text" name="total_no_of_camp_per_month" id="total_no_of_camp_per_month" disabled class="profile_input" value="<?=$RS_bp->fields['total_no_of_camp_per_month']?>"></td>
				  </tr>
				    <?php
				  /***** get number of active camapigns of current month ******/
				  $arr_c=file(WEB_PATH.'/merchant/process.php?num_campaigns_per_month=yes&mer_id='.$_SESSION['merchant_id']);
				  
								
					if(trim($arr_c[0]) == "")
					{
						unset($arr_c[0]);
						$arr_c = array_values($arr_c);
					}
					$json = json_decode($arr_c[0]);
						
					$total_campaigns= $json->total_records;
					$addcampaign_status = $json->status;
					$max_camapign = $json->max_campaigns;
					$campaign_left=  $max_camapign - $total_campaigns;
				  ?>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_total_number_of_campaigns_left_for_month']; ?></td>
					<td>
						<!--
						<input type="text" name="total_no_of_camp_left_per_month" id="total_no_of_camp_left_per_month" disabled style="width:200px; " value="<?=$RS->fields['total_no_of_campaign']?>">
						-->
						<input type="text" name="total_no_of_camp_left_per_month" id="total_no_of_camp_left_per_month" disabled class="profile_input" value="<?=$total_campaigns?>">
					</td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_minimum_share_points']; ?></td>
					<td><input type="text" name="min_share_point" id="min_share_point" disabled class="profile_input" value="<?=$RS_bp->fields['min_share_point']?>"></td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_minimum_reward_points']; ?></td>
					<td><input type="text" name="min_reward_point" id="min_reward_point" disabled class="profile_input" value="<?=$RS_bp->fields['min_reward_point']?>"></td>
				  </tr>
				  <tr>
					<td><?php echo $merchant_msg['profile']['Field_transaction_fees']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_transaction_fees"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>
					<td><input type="text" id="transaction_fees" disabled class="profile_input" value="<?=$RS_bp->fields['transaction_fees']." Points"?>"> </td>
				  </tr>
				  <?php 
					if($RS_bp->fields['enable_coupon_redeemption_fee']==1)
					{
					?>
					  <tr>
						<td><?php echo $merchant_msg['profile']['Field_coupon_redeemption_fees']; ?></td>
						<td><span id="know_more">Click Here</span></td>
					  </tr>
					<?php
					}
					?>
                                        <tr>
                                                <td><?php echo $merchant_msg['profile']['Field_number_of_active_loyalty_card']; ?></td>
                                                <td><input type="text" disabled class="profile_input" value="<?=$RS_bp->fields['active_loyalty_cards']?>"/> </td>
                                        </tr>
                                              <tr>
                                              <td><?php echo $merchant_msg['profile']['Field_loyalty_card_transaction_fee']; ?><span class="notification_tooltip"  title="<?php  echo  $merchant_msg["profile"]["Tooltip_loyalty_card_transaction_fees"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>
                                              <td><input type="text" disabled class="profile_input" value="<?=$RS_bp->fields['transaction_fees_stamp']?>" /></td>
                                        </tr>

                                              <tr>
                                              <td>No of Reward zone active gift_card</td>
                                              <td><input type="text" disabled class="profile_input" value="<?=$RS_bp->fields['reward_zone_active_gift_card'] ?>" /></td>
                                        </tr>

                                                <tr>
                                                <td>No of Reward zone active campaign</td>
                                                <td><input type="text" disabled class="profile_input" value="<?=$RS_bp->fields['reward_zone_active_campaign'] ?>" /></td>
                                        </tr>
			</table>
		</div>
		<?php
		}
		?>
		<!--
		<table width="100%"  border="0" cellspacing="2" cellpadding="2">		  
		  <tr>
			<td>&nbsp;</td>
			<td style="padding:20px;">
				<input type="submit" name="btnUpdateProfile" value="<?php echo $merchant_msg['index']['btn_save'];?>" onClick="mycall();" id="btnUpdateProfile" >					
			</td>
		  </tr>
		</table>
		-->
		</form>
	</td>
  </tr>
</table>

	<!--end of content--></div>
<!--end of contentContainer--></div>
<!--start footer--><div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
                $_SESSION['msg']= "";
		?>
		<!--end of footer--></div>
</div>
<div id="cancel_message" style="display:none;">
	<div class="col-md-12">
		<h4 class="head_msg">End subscription</h4>

		  <span class="modal-error"></span>

			<form class="form form-horizontal" id="cancel_sub_form">
			    <div class="radio-button radio">
				<label class="rd-label">
				    <input type="radio" name="at_period_end" value="false" id="false"> <strong>Immediately</strong>
				    <em>End the subscription immediately.</em>
				</label>
			    </div>
			    
			    <div class="radio-button radio">
				<label class="rd-label">
				    <input type="radio" name="at_period_end" id="true" value="true"> <strong>At period end</strong>
				    <em>End the subscription at the end of the current billing period.</em>
				</label>
			    </div>
				<div class="radio">
					<input type="hidden" name="sid" id="sid" value='' />
					<div class="spinner"><div class="spinner-view" style="display: none;"></div></div>
				        <button type="button" name="close_modal" id="close" class="btn btn-default"><span>Cancel</span></button>
					<button type="button" name="cancel_sub_confirm" id="cancel_sub_confirm" class="btn btn-primary"><span>End subscription</span></button>
				</div>
			</form>
		
	</div>
</div>
</body>
</html>  
<script type="text/javascript">
function btncanprofile()
{                                                
	window.location="<?=WEB_PATH?>/merchant/my-account.php";
}
function mycall()
{
	//alert(tinyMCE.get('terms_condition').getContent());
	jQuery('#aboutus').val(tinyMCE.get('aboutus').getContent());
	jQuery('#aboutus_short').val(tinyMCE.get('aboutus_short').getContent());												   
}
function changetext2(){
   
    if(jQuery("body").find("#aboutus"))
        {
            var v = 600 - $("#aboutus").val().length;
            $(".span_c2").text(v+" characters remaining");
        }
   
}

//jQuery("a#myprofile").css("background-color","orange");
jQuery("a#profile-link").css("color","orange");
//jQuery("a#password-link").css("color","#0066FF");
//jQuery("a#payment-link").css("color","#0066FF");
</script>
<?php
if($data_sub_merchant_id['merchant_parent'] == "0")
{ ?>
<script>
changetext2();	
var file_path = "";
$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'merchant_media_upload.php?doAction=FileUploadMerchant&img_type=icon',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				if($('#files').children().length > 0)
				{
					$('#files li').detach();
				}
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				status.text('Uploading...');
			},
			onComplete: function(file, response){
				//On completion clear the status
				//alert(response);
                                
				var arr = response.split("|");
				if(arr[1]=="small")
                                {
                                    status.text(arr[0]);
                                }
                                else
                                {
                                    status.text('');
                                    //Add uploaded file to list
                                    file_path = arr[1];
                                    save_from_computer();
                                }
			}
		});
		
	});
</script>
<?php } ?>

<script>


 
/* start of script for PAY-508-28033*/
function save_from_library()
{
	 var sel_val = $('input[name=use_image]:checked').val();
	 <!--// 369-->
	 if (sel_val==undefined)
	 {
	 	close_popup('Notification');
	 }
	 else
	 {
		
		$("#hdn_image_id").val(sel_val);
		var sel_src = $("#li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
		//alert(sel_src);
	       $("#hdn_image_path").val(sel_src);
	       file_path = "";
	       close_popup('Notification');
	       var img = "<img src='<?=ASSETS_IMG?>/m/campaign/"+ sel_src +"' class='displayimg'>";
	       $('#files').html(img +"<br/><div class ='display_img_cls' ><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/mediya_delete.png' onclick='rm_image()' /></div></div></div>");
	}
	 <!--// 369-->
	
}
function rm_image()
{
	$("#hdn_image_path").val("");
	$("#hdn_image_id").val("");
	$('#files').html("");
	
}
function save_from_computer()
{
	$("#hdn_image_path").val(file_path);
	$("#hdn_image_id").val("");
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG?>/m/icon/"+ file_path +"' class='displayimg'>";
	$('#files').html(img +"<br/><div class='display_img_cls'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/mediya_delete.png' onclick='rm_image()' /></div></div></div>");
				
}

$(document).ready(function() {
		
$('#cancel_sub_form input[type="radio"]').on('click',function(){
	//$('#cancel_sub_form input[type="radio"]').prop('checked',false);  
	$("input:radio").removeAttr("checked");
	$(this).attr('checked',true);  
});
	//cancel subscription plan
	$(document).on('click','#cancel_sub_confirm',function(){
		if($('#cancel_sub_form input[type="radio"]').is(':checked')){
			var at_period_end = $('#cancel_sub_form input[name="at_period_end"]:checked').val();		
			var sid = $('#cancel_sub_form #sid').val();
			jQuery.ajax({
                            type: "POST",
                            url: 'process.php',
                            data: 'cancel_stripe_subcription=true&at_period_end='+at_period_end+'&sid='+sid,
                            success: function (msg)
                            {
                                var obj = jQuery.parseJSON(msg);
                                if (obj.status == "false")
                                {
                                    $('.modal-error').html(obj.serror);

                                } else {
					if(at_period_end == 'true'){
						$('#cancel_subscr_package').parent('td').prev('td').html('This will cancel at period end.');
						$('#cancel_subscr_package').data('iscancel',1);
					}else{
						$('#cancel_subscr_package').data('id',0);
						$('#subscr_package').data('id','0');
						$('#table_spacer input').val('');
						$('#cancel_subscr_package').parent('td').prev('td').html('');
					}
				   $.fancybox.close();
                                    return true;
                                }

                            }
                        });
		}else{
			$('.modal-error').html('Please choose atlist one.');
		}
		
		
	});

	$(document).on('click','#cancel_subscr_package',function(){
		processLoginorNot();

		var iscancel = $(this).data('iscancel');
		
		var id = $.trim($(this).data('id'));
		if(id == ''){id = 0; }
		$('#cancel_sub_form #sid').val(id);

		jQuery.fancybox({
				content:jQuery('#cancel_message').html(),
				type: 'html',
				openSpeed  : 300,
				closeSpeed  : 300,
				changeFade : 'fast', 
				//href: 'subscription_plan.php?pid='+id,
                                //type: 'ajax', 
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
			});
	});

	$(document).on('click','#subscr_package',function(){
			var id = $.trim($(this).data('id'));
			if(id == ''){id = 0; }

			processLoginorNot();
			jQuery.fancybox({
				//content:jQuery('#dialog-message').html(),
				type: 'html',
				openSpeed  : 300,
				closeSpeed  : 300,
				changeFade : 'fast', 
				href: 'subscription_plan.php?pid='+id,
                                type: 'ajax', 
				helpers: {
					overlay: {
					opacity: 0.3
					} // overlay
				}
			});

		});


	if($("#hdn_image_path").val() != ""){
	var img = "<img src='<?=ASSETS_IMG?>/m/icon/"+ $("#hdn_image_path").val() +"' class='displayimg'>";
	$('#files').html(img +"<br/><div class='display_img_cls'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG?>/m/mediya_delete.png' onclick='rm_image()' /></div></div></div>");
	}
	
	$('#country').change(function(){
	var change_value=this.value;
	
	if(change_value == "USA")
	{
	    $("#state").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");
	    
	   
	}
	else
	{
	     $("#state").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
	}
    });
    
    jQuery('#state').change(function(){
		var change_value=this.value;
		
		jQuery.ajax({
			type:"POST",
			url:'process.php',
			data :'btngetcityofstate=true&state_id='+change_value,
			async:false,
			success:function(msg)
			{
				var obj = jQuery.parseJSON(msg);
				if (obj.status=="false")     
				{
					jQuery("#city").html(obj.html);
				}
				else
				{
					jQuery("#city").html(obj.html);
				}
			}
		});
    
    });
	
	jQuery("#btnUpdateLocationInfo").click(function(){
		var msg_box="";
		var flag="true";
		var redeem_location=jQuery("#redeem_location").val();
		if(redeem_location==0 || redeem_location=="")
		{
			msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_select_location']; ?></div>";
			flag="false";
			
			var head_msg="<div class='head_msg'>Message</div>"
		    var content_msg="<div class='content_msg'>"+msg_box+"</div>";
		    var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		    jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		    
		    if(flag=="false")
		    {
		      
				jQuery.fancybox({
							content:jQuery('#dialog-message').html(),
							type: 'html',
							openSpeed  : 300,
							closeSpeed  : 300,
							changeFade : 'fast',  
							helpers: {
								overlay: {
								opacity: 0.3
								} // overlay
							}
				});
				return false;
				
		    }
	
		}
	});
	
	jQuery("#btnUpdateProfile").click(function(){
	            var msg_box="";
		    var mobileno_area_code=jQuery("#mobileno_area_code").val();
		    var mobileno=jQuery("#mobileno").val();
			var postal_code=jQuery("#zipcode").val();
			var country=jQuery("#country").val();
            var mobileno2=jQuery("#mobileno2").val();
            var address=jQuery("#address").val();
            var lastname=jQuery("#lastname").val();
            var firstname=jQuery("#firstname").val();
            var city=jQuery("#city").val();
			var business=jQuery("#business").val();
		    var aboutus=jQuery("#aboutus").val();
            var aboutus_short=jQuery("#aboutus_short").val();
		    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
		    var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
			
			var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
            var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
			
			var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
			
			var flag="true";
                        if(firstname == "")
                        {
                            msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_first_name']; ?></div>";
                            flag="false";
                        }
                        if(lastname == "")
                        {
                            msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_last_name']; ?></div>";
                            flag="false";
                        }
                        if(address == "")
                            {
                                msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_address']; ?></div>";
                                flag="false";
                            }
                        if(city == "")
	{
		msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_city']; ?></div>";
		flag="false";
	}
			if(postal_code=="")
			{
				//alert("Please enter postal/zipcode");
                                msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_postal_zipcode']; ?></div>";
				flag="false";
			}
			else
	{
                            postal_code=jQuery.trim(postal_code);
			postal_code=postal_code.toUpperCase();
			if(country=="USA")
			{
			   if(!usPostalReg.test(postal_code)) {
				
				//alert("Please enter valid postal/zipcode");
                                
                                msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_valid_postal_zipcode']; ?></div>";
				flag="false";
			   }	
				
			}
			else if(country == "Canada")
			{
                            
				if(!canadaPostalReg.test(postal_code)) {
                                  
				//alert("Please enter valid postal/zipcode");
                                msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_valid_postal_zipcode']; ?></div>";
				flag="false";
				}
			}
		}	
		    
                    if(mobileno_area_code == "" || mobileno =="" || mobileno2 == "" )
                        {
                            msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                             flag="false";
                        }
                        else 
                        {
                                    if(mobileno_area_code != "")
                                    {

                                        if(!numericReg.test(mobileno_area_code)) {
                                            //alert("Please Input Valid Mobile Number");
                                            msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                           flag="false";
                                            //return false;
                                        }
                                        else
                                            {
                                                if(mobileno_area_code.length != 3)
                                                {
                                                    //alert("Please Input Valid Area Code Number");
                                                    msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                                   flag="false";
                                                    //return false;
                                                }
                                            }

                                    }
                                    else if(mobileno != "")
                                    {
                                        if(!numericReg.test(mobileno)) {
                                            //alert("Please Input Valid Mobile Number");
                                            msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                           flag="false";
                                            //return false;
                                        }
                                        else
                                            {
                                                if(mobileno.length != 4)
                                                {
                                                    //alert("Please Input Valid Mobile Number");
                                                    msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                                    flag="false";
                                                    //return false;
                                                } 
                                            }

                                    }
                                    else if(mobileno2 != "")
                                    {
                                        if(!numericReg.test(mobileno2)) {
                                            //alert("Please Input Valid Mobile Number");
                                            msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                           flag="false";
                                            //return false;
                                        }
                                        else
                                            {
                                                if(mobileno2.length != 3)
                                                {
                                                    //alert("Please Input Valid Mobile Number");
                                                    msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                                    flag="false";
                                                    //return false;
                                                }
                                                
                                            }

                                    }

                                    
                                   
                                   
                        }
	if(business=="")
	{		
		msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_business_name']; ?></div>";
		flag="false";
	}	    
	if(aboutus=="")
	{		
		msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_about_you']; ?></div>";
		flag="false";
	}
    if(aboutus_short=="")
	{		
		msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_about_you_short']; ?></div>";
		flag="false";
	}
	
	if(jQuery("#hdn_image_path").val()=="")
	{
		msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_upload_merchant_icon']; ?></div>";
		flag="false";
	}
	var tag_value=jQuery("#business_tags").val();
	if(tag_value == "")
	{
		msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_please_add_business_tag"]; ?></div>";
		flag="false";
	}
	else
	{
		if(hastagRef.test(tag_value))
		{
			
			var tag_arr = tag_value.split(",");   
			//alert(tag_arr.length);
			if(tag_arr.length>15)
			{
				msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_add_business_tag"]; ?></div>";
				flag="false";
			}
			
		}
		else
		{
		
			msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_business_tag"]; ?></div>";
			flag="false";
		}
	}
																
		    var head_msg="<div class='head_msg'>Message</div>"
		    var content_msg="<div class='content_msg'>"+msg_box+"</div>";
		    var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		    jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		    
		    if(flag=="false")
		    {
		      
		    jQuery.fancybox({
					    content:jQuery('#dialog-message').html(),
					    type: 'html',
					    openSpeed  : 300,
					    closeSpeed  : 300,
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
			
			jQuery("#btnUpdatePersonalInfo").click(function(){
	            var msg_box="";
		    
				var lastname=jQuery("#lastname").val();
				var firstname=jQuery("#firstname").val();
				
				var mobileno_area_code=jQuery("#mobileno_area_code").val();
				var mobileno=jQuery("#mobileno").val();
				var mobileno2=jQuery("#mobileno2").val();
				var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
				
				var flag="true";
				if(firstname == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_first_name']; ?></div>";
					flag="false";
				}
				if(lastname == "")
				{
					msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_last_name']; ?></div>";
					flag="false";
				}
				if(mobileno_area_code == "" || mobileno =="" || mobileno2 == "" )
                        {
                            msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                             flag="false";
                        }
                        else 
                        {
                                    if(mobileno_area_code != "")
                                    {

                                        if(!numericReg.test(mobileno_area_code)) {
                                            //alert("Please Input Valid Mobile Number");
                                            msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                           flag="false";
                                            //return false;
                                        }
                                        else
                                            {
                                                if(mobileno_area_code.length != 3)
                                                {
                                                    //alert("Please Input Valid Area Code Number");
                                                    msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                                   flag="false";
                                                    //return false;
                                                }
                                            }

                                    }
                                    else if(mobileno != "")
                                    {
                                        if(!numericReg.test(mobileno)) {
                                            //alert("Please Input Valid Mobile Number");
                                            msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                           flag="false";
                                            //return false;
                                        }
                                        else
                                            {
                                                if(mobileno.length != 4)
                                                {
                                                    //alert("Please Input Valid Mobile Number");
                                                    msg_box +="<div> <?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                                    flag="false";
                                                    //return false;
                                                } 
                                            }

                                    }
                                    else if(mobileno2 != "")
                                    {
                                        if(!numericReg.test(mobileno2)) {
                                            //alert("Please Input Valid Mobile Number");
                                            msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                           flag="false";
                                            //return false;
                                        }
                                        else
                                            {
                                                if(mobileno2.length != 3)
                                                {
                                                    //alert("Please Input Valid Mobile Number");
                                                    msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_update_ your_ phone_ number']; ?></div>";
                                                    flag="false";
                                                    //return false;
                                                }
                                                
                                            }

                                    }

                                    
                                   
                                   
                        }		   
																	
				var head_msg="<div class='head_msg'>Message</div>"
				var content_msg="<div class='content_msg'>"+msg_box+"</div>";
				var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{
				  
						jQuery.fancybox({
							content:jQuery('#dialog-message').html(),
							type: 'html',
							openSpeed  : 300,
							closeSpeed  : 300,
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
			
			jQuery("#btnUpdateAddressInfo").click(function(){
	            var msg_box="";
		    var address=jQuery("#address").val(); 
			var postal_code=jQuery("#zipcode").val();
			
			var country=jQuery("#country").val();
			var state=jQuery("#state").val();
            var city=jQuery("#city").val();
		   
		    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
		    var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
			
			var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
            var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
			
			var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
			
			var flag="true";
                        
			if(address == "")
			{
				msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_address']; ?></div>";
				flag="false";
			}
			if(state == "0" || state == "")
			{
				msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_state']; ?></div>";
				flag="false";
			}
			if(city == "0" || city == "")
			{
				msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_city']; ?></div>";
				flag="false";
			}
			if(postal_code=="")
			{
				//alert("Please enter postal/zipcode");
				msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_postal_zipcode']; ?></div>";
				flag="false";
			}
			else
			{
				postal_code=jQuery.trim(postal_code);
				postal_code=postal_code.toUpperCase();
				
				if(country=="1")
				{
					if(!usPostalReg.test(postal_code))
					{
						//alert("Please enter valid postal/zipcode");
						msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_valid_postal_zipcode']; ?></div>";
						flag="false";
					}	

				}
				else if(country == "2")
				{
					if(!canadaPostalReg.test(postal_code)) 
					{
						//alert("Please enter valid postal/zipcode");
						msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_valid_postal_zipcode']; ?></div>";
						flag="false";
					}
				}
				
			}	
		                 
		    													
				var head_msg="<div class='head_msg'>Message</div>"
				var content_msg="<div class='content_msg'>"+msg_box+"</div>";
				var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
				
				if(flag=="false")
				{  
					jQuery.fancybox({
						content:jQuery('#dialog-message').html(),
						type: 'html',
						openSpeed  : 300,
						closeSpeed  : 300,
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
			
			jQuery("#btnUpdateAboutInfo").click(function(){
	            var msg_box="";
				
		    var business=jQuery("#business").val();
			var tag_value=jQuery("#business_tags").val();
		    var aboutus=jQuery("#aboutus").val();
            var aboutus_short=jQuery("#aboutus_short").val();
			
		    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
		    var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
			
			var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
            var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
			
			var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
			
			var flag="true";
                        
		    if(business=="")
			{		
				msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_business_name']; ?></div>";
				flag="false";
			}
			
			
			if(tag_value == "")
			{
				msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_please_add_business_tag"]; ?></div>";
				flag="false";
			}
			else
			{
				if(hastagRef.test(tag_value))
				{
					
					var tag_arr = tag_value.split(",");   
					//alert(tag_arr.length);
					if(tag_arr.length>15)
					{
						msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_add_business_tag"]; ?></div>";
						flag="false";
					}
					
				}
				else
				{
				
					msg_box +="<div><?php  echo  $merchant_msg["profile"]["Msg_business_tag"]; ?></div>";
					flag="false";
				}
			}
			
			if(aboutus=="")
			{		
				msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_about_you']; ?></div>";
				flag="false";
			}
			if(aboutus_short=="")
			{		
				msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_about_you_short']; ?></div>";
				flag="false";
			}
			
			if(jQuery("#hdn_image_path").val()=="")
			{
				msg_box +="<div><?php echo $merchant_msg['profile']['Msg_please_upload_merchant_icon']; ?></div>";
				flag="false";
			}
			
																
		    var head_msg="<div class='head_msg'>Message</div>"
		    var content_msg="<div class='content_msg'>"+msg_box+"</div>";
		    var footer_msg="<div><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		    jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		    
		    if(flag=="false")
		    {
		      
		    jQuery.fancybox({
					    content:jQuery('#dialog-message').html(),
					    type: 'html',
					    openSpeed  : 300,
					    closeSpeed  : 300,
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
	
	});
 jQuery("#popupcancel").live("click",function(){
           jQuery.fancybox.close(); 
       return false; 
    });
$(".tab_from_library a").click(function(){
	$("#sidemenu li a").each(function() {
		$(this).removeClass("current");
		});
	$(this).addClass("current");
	$(".div_from_library").css("display","block");
	$(".div_from_computer").css("display","none");
	});
$(".tab_from_computer a").click(function(){
	$("#sidemenu li a").each(function() {
		$(this).removeClass("current");
		});
	$(this).addClass("current");
	$(".div_from_library").css("display","none");
	$(".div_from_computer").css("display","block");
        
        
		
        
	});
        
        
       
        
function close_popup(popup_name)
{

	$("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
	$("#" + popup_name + "BackDiv").fadeOut(200, function () {
		 $("#" + popup_name + "PopUpContainer").fadeOut(200, function () {         
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				$("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
 function open_popup(popup_name)
{

	if($("#hdn_image_id").val()!="")
	{
		$('input[name=use_image][value='+$("#hdn_image_id").val()+']').attr("checked","checked");
	}
	$("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		$("#" + popup_name + "BackDiv").fadeIn(200, function () {
			 $("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
			 });
		});
	});
	
	
}
/* end of of script for PAY-508-28033*/
jQuery('.notification_tooltip').tooltip({
	track: true,
	delay: 0,
	showURL: false,
	showBody: "<br>",
	fade: 250
});

jQuery("#know_more").click(function(){

	jQuery.fancybox({
		content:jQuery('#redeemption-message').html(),
		type: 'html',
		openSpeed  : 300,
		closeSpeed  : 300,
		changeFade : 'fast',  
		helpers: {
			overlay: {
			opacity: 0.3
			} // overlay
		}
	});
});

</script>
