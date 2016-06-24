<?php
/**
 * @uses edit location
 * @used in pages :upload_additional_images.php,apply_filter.php,locations.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where = array();
$array_where['created_by'] = $_SESSION['merchant_id'];
$array_where['id'] = $_REQUEST['id'];
$RS = $objDB->Show("locations", $array_where);

$array_more_images_where = array();
$array_more_images_where=$_REQUEST['id'];
$RS_more_images = $objDB->Show("location_images", $array_more_images_where);
//echo  $count_images=$RS_more_images->RecordCount();
//print_r($RS_more_images);

$timezoness =array(
    'Pacific/Wake' => '(GMT-12:00) International Date Line West',
    'Pacific/Apia' => '(GMT-11:00) Samoa',
    'Pacific/Honolulu' => '(GMT-10:00) Hawaii',
    'America/Anchorage' => '(GMT-09:00) Alaska',
    'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada); Tijuana',
    'America/Phoenix' => '(GMT-07:00) Arizona',
    'America/Chihuahua' => '(GMT-07:00) Mazatlan',
    'America/Denver' => '(GMT-07:00) Mountain Time (US & Canada)',
    'America/Managua' => '(GMT-06:00) Central America',
    'America/Chicago' => '(GMT-06:00) Central Time (US & Canada)',
    'America/Mexico_City' => '(GMT-06:00) Monterrey',
    'America/Regina' => '(GMT-06:00) Saskatchewan',
    'America/Bogota' => '(GMT-05:00) Quito',
    'America/New_York' => '(GMT-05:00) Eastern Time (US & Canada)',
    'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
    'America/Halifax' => '(GMT-04:00) Atlantic Time (Canada)',
    'America/Caracas' => '(GMT-04:00) La Paz',
    'America/Santiago' => '(GMT-04:00) Santiago',
    'America/St_Johns' => '(GMT-03:30) Newfoundland',
    'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
    'America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown',
    'America/Godthab' => '(GMT-03:00) Greenland',
    'America/Noronha' => '(GMT-02:00) Mid-Atlantic',
    'Atlantic/Azores' => '(GMT-01:00) Azores',
    'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
    'Africa/Casablanca' => '(GMT) Monrovia',
    'Europe/London' => '(GMT) London',
    'Europe/Berlin' => '(GMT+01:00) Vienna',
    'Europe/Belgrade' => '(GMT+01:00) Prague',
    'Europe/Paris' => '(GMT+01:00) Paris',
    'Europe/Sarajevo' => '(GMT+01:00) Zagreb',
    'Africa/Lagos' => '(GMT+01:00) West Central Africa',
    'Europe/Istanbul' => '(GMT+02:00) Minsk',
    'Europe/Bucharest' => '(GMT+02:00) Bucharest',
    'Africa/Cairo' => '(GMT+02:00) Cairo',
    'Africa/Johannesburg' => '(GMT+02:00) Pretoria',
    'Europe/Helsinki' => '(GMT+02:00) Vilnius',
    'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
    'Asia/Baghdad' => '(GMT+03:00) Baghdad',
    'Asia/Riyadh' => '(GMT+03:00) Riyadh',
    'Europe/Moscow' => '(GMT+03:00) Volgograd',
    'Africa/Nairobi' => '(GMT+03:00) Nairobi',
    'Asia/Tehran' => '(GMT+03:30) Tehran',
    'Asia/Muscat' => '(GMT+04:00) Muscat',
    'Asia/Tbilisi' => '(GMT+04:00) Yerevan',
    'Asia/Kabul' => '(GMT+04:30) Kabul',
    'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
    'Asia/Karachi' => '(GMT+05:00) Tashkent',
    'Asia/Calcutta' => '(GMT+05:30) New Delhi',
    'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
    'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
    'Asia/Dhaka' => '(GMT+06:00) Dhaka',
    'Asia/Colombo' => '(GMT+06:00) Sri Jayawardenepura',
    'Asia/Rangoon' => '(GMT+06:30) Rangoon',
    'Asia/Bangkok' => '(GMT+07:00) Jakarta',
    'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
    'Asia/Hong_Kong' => '(GMT+08:00) Urumqi',
    'Asia/Irkutsk' => '(GMT+08:00) Ulaan Bataar',
    'Asia/Singapore' => '(GMT+08:00) Singapore',
    'Australia/Perth' => '(GMT+08:00) Perth',
    'Asia/Taipei' => '(GMT+08:00) Taipei',
    'Asia/Tokyo' => '(GMT+09:00) Tokyo',
    'Asia/Seoul' => '(GMT+09:00) Seoul',
    'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
    'Australia/Adelaide' => '(GMT+09:30) Adelaide',
    'Australia/Darwin' => '(GMT+09:30) Darwin',
    'Australia/Brisbane' => '(GMT+10:00) Brisbane',
    'Australia/Sydney' => '(GMT+10:00) Sydney',
    'Pacific/Guam' => '(GMT+10:00) Port Moresby',
    'Australia/Hobart' => '(GMT+10:00) Hobart',
    'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
    'Asia/Magadan' => '(GMT+11:00) Solomon Is.',
    'Pacific/Auckland' => '(GMT+12:00) Wellington',
    'Pacific/Fiji' => '(GMT+12:00) Marshall Is.',
    'Pacific/Tongatapu' => '(GMT+13:00) Nuku\'alofa',
);
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Edit Location</title>
<?php //require_once(MRCH_LAYOUT."/head.php"); ?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<!-- image more slider -->
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.carouFredSel-6.2.1-packed.js"></script>
<!-- End image more slider -->

  <script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery.timepicker.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=ASSETS_CSS ?>/m/jquery.timepicker.css" />
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery.form.js"></script>
<!-- T_7 -->
<script language="javascript" src="<?=ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
<!--<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.tooltip.css" />
<script src="<?=ASSETS_JS ?>/m/jquery.tooltip.js" type="text/javascript"></script> -->

<!-- T_7 -->
<script language="javascript">

function email_validation(email){
	filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(email))
	  return true;
	else
	  return false;
}
// 369
$(document).ready(function() { 
    // bind form using ajaxForm 
    $('#edit_location_form').ajaxForm({ 
        beforeSubmit:processLoginorNot,
    
        dataType:  'json', 
        success:   processEditLocaJson 
    });
	
});
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
                                    flag=1;
                                    window.location.href=obj.link;
                                    
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
function processEditLocaJson(data) { 
	if(data.status == "true"){
		window.location.href='<?=WEB_PATH.'/merchant/locations.php'?>';
	}else{
		//alert(data.message);
                var head_msg="<div class='head_msg'>Message</div>";
				var content_msg="<div class='content_msg'>"+data.message+"</div>";
				var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
				jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
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
	}
     
}
// 369
</script>

<script type="text/javascript" src="<?=ASSETS_JS ?>/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/bootstrap.css" />
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

<!--- tooltip css --->
<!--<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.js"></script> -->

<!--- tooltip css --->

</head>

<body>
    
    <div id="dialog-message" title="Message Box" style="display:none">
    </div>
    
 <div >
<!--start header--><div>

		<?
		require_once(MRCH_LAYOUT."/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
		<div id="content">  <h3><?php echo $merchant_msg["addlocation"]["Field_location_edit"];?></h3>
		<p>Please tell us about your location - <span class='cls_bold'>* fields are required</span></p>
	<!--// 369--><form action="process.php" method="post" id="edit_location_form" class="loca_edit">
            <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<?=$RS->fields['picture']?>" />    
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
				
				<?php
				if($RS->fields['categories']!="")
				{
					$categories=$RS->fields['categories'];
					$array_cat=explode(",",$categories);
					//echo count($array_cat);
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
				<?php
                                    $array_values = $where_clause = $array = array();
$where_clause['id'] = $_SESSION['merchant_id'];
$RSBN=$objDB->Show("merchant_user", $where_clause);

if($RS->fields['location_name'] == "")
{
    $l_name =  $RSBN->fields['business'];
}
else{
    $l_name =  $RS->fields['location_name'];
}
?>
				<table width="100%"  border="0" cellspacing="2" cellpadding="2">
				  <!-- <tr>
				    <td colspan="2" align="center" style="color:#FF0000; "><?php if(isset($_REQUEST['rmsg'])){echo $_REQUEST['rmsg'];} ?></td>
			      </tr>-->
                                    
				 <tr style="display:none;">
					<td width="20%" align="right"><?php echo $merchant_msg["addlocation"]["Field_location_name"];?></td>
					<td width="80%" align="left">
						<input type="text" name="location_name" id="location_name" value="<?=$l_name?>" />
						&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chk_is_primary" class="chk" id="chk_is_primary"  value="<?php echo $RS->fields['is_primary']; ?>" <?php if($RS->fields['is_primary'] == "1") echo checked; ?>/> Primary Location <span class="notification_tooltip" title="<?php echo $merchant_msg["addlocation"]["Tooltip_Loction_Primary"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					
					</td>
				  </tr>
				</table>
				
				<h4 id="expanderHeadAddress" style="cursor:pointer;" class="collaspable_div" >
					Address <span id="expanderSignAddress" class="collaspable_sign">+</span>
				</h4>
				<div id="expanderContentAddress" style="display:none">
					<table width="100%"  border="0" cellspacing="2" cellpadding="2">
						<tr>
					<td width="30%" align="right"><?php echo $merchant_msg["addlocation"]["Field_address"];?></td>
					<td align="left">
						<!--<input type="text" name="address" id="address" class="input_location" value="<?=$RS->fields['address']?>" />-->
						<textarea name="address" id="address" class="location_address"><?php echo $RS->fields['address'];?></textarea>
					</td>
				  </tr>
				  
                                   <tr style="display:none;">
					<td align="right"><?php echo $merchant_msg["addlocation"]["Field_country"];?></td>
					<td align="left">
								<?php
								//echo $RS->fields['country']."-".$RS->fields['state']."-".$RS->fields['city'];
								$array_where = array();
								$array_where['active'] = 1;
								$RS_country = $objDB->Show("country", $array_where," Order By `name` ASC ");
								?>
                                            <select name="country" id="country" class="coun">
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
					<td align="right"><?php echo $merchant_msg["addlocation"]["Field_state"];?></td>
					<td align="left">
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
					<td align="right"><?php echo $merchant_msg["addlocation"]["Field_city"];?></td>
					<td align="left">
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
					<td align="right"><?php echo $merchant_msg["addlocation"]["Field_zipcode"];?></td>
					<td align="left">
						<input type="text" name="zip" id="zip" class="input_location" value="<?=$RS->fields['zip']?>" />
					</td>
				  </tr>
				  <tr>
					<td align="right"><?php echo $merchant_msg["addlocation"]["Field_phone_number"];?></td>
					<?php 
					$mobileno=$RS->fields['phone_number'];
					$area_code=substr($mobileno,4,3);
                                        $mobileno2=substr($mobileno,8,3);
                                        $mobileno1=substr($mobileno,12,4);  
				       ?>
					<td align="left">
						<!--<input type="text" name="phone_number" id="phone_number" value="<?=$RS->fields['phone_number']?>" />-->
						
						<select name="mobile_country_code" id="mobile_country_code" style="display: none">
                                                    <option value="001">001</option>
                                                </select>
                                                <input type="text" name="mobileno_area_code" id="mobileno_area_code" class="mobile_code1" value="<?php echo $area_code;?>" maxlength="3">-
                                                 <input type="text" name="mobileno2" id="mobileno2" class="mobile_code1" value="<?php echo $mobileno2;?>" maxlength="3">-
                                                <input type="text" name="mobileno" id="mobileno" class="mobile_code2" value="<?php echo $mobileno1;?>" maxlength="4">
					</td>
				  </tr>
					</table>
				</div>
				
				<h4 id="expanderHeadWeb" style="cursor:pointer;" class="collaspable_div" >
					Web Info <span id="expanderSignWeb" class="collaspable_sign">+</span>
				</h4>
				<div id="expanderContentWeb" style="display:none">
					<table width="100%"  border="0" cellspacing="2" cellpadding="2">
						<tr>
					<td width="30%" align="right"><?php echo $merchant_msg["addlocation"]["Field_website"];?></td>
					<td align="left">
						<input type="text" placeholder="http://www.YourSite.com/LandingPage" name="website" id="website"class="input_location"  value="<?=$RS->fields['website']?>" />
					</td>
				  </tr>
                                  <tr>
					<td align="right"><?php echo $merchant_msg["addlocation"]["Field_facebook"];?></td>
					<td align="left">
						<input type="text" placeholder="https://www.facebook.com/LandingPage" name="facebook" id="facebook" class="input_location" value="<?=$RS->fields['facebook']?>" />
					</td>
				  </tr>
                                  <tr>
					<td align="right"><?php echo $merchant_msg["addlocation"]["Field_google"];?></td>
					<td align="left">
						<input type="text" placeholder="https://plus.google.com/LandingPage" name="google" id="google" class="input_location" value="<?=$RS->fields['google']?>" />
					</td>
				  </tr>
				  <tr>
					<td align="right"><?php echo $merchant_msg["addlocation"]["Field_email"];?></td>
					<td align="left">
						<input type="text" name="email" id="email" class="input_location" value="<?=$RS->fields['email']?>" />
					</td>
				  </tr>
					</table>
				</div>
				  
				<h4 id="expanderHeadLocation" style="cursor:pointer;" class="collaspable_div" >
					Basic Details <span id="expanderSignLocation" class="collaspable_sign">+</span>
				</h4>
				<div id="expanderContentLocation" style="display:none">
					<table width="100%"  border="0" cellspacing="2" cellpadding="2"> 
						<tr>
							<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_payment_method"];?></td>
							<td align="left">
								<?php
								$payment_method=$RS->fields['payment_method'];
								$str = array();
								$str=explode(",",$payment_method);
								//print_r($str);
								?>
								<input <?php if(in_array("Cash", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Cash" id="Cash" /><label for="Cash" class='chk_align'><span><?php echo "Cash" ?></span></label>
								<input <?php if(in_array("Cash only", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Cash only" id="Cash only" /><label for="Cash only" class='chk_align'><span><?php echo "Cash only" ?></span></label>
								<input <?php if(in_array("Visa", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Visa" id="Visa" /><label for="Visa" class='chk_align'><span><?php echo "Visa" ?></span></label>
								<input <?php if(in_array("Master card", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Master card" id="Master card" /><label for="Master card" class='chk_align'><span><?php echo "Master card" ?></span></label>
								<input <?php if(in_array("AMEX", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="AMEX" id="AMEX" /><label for="AMEX" class='chk_align'><span><?php echo "AMEX" ?></span></label>
								<input <?php if(in_array("Discover", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Discover" id="Discover" /><label for="Discover" class='chk_align'><span><?php echo "Discover" ?></span></label>
								<input <?php if(in_array("Dinerclub", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Dinerclub" id="Dinerclub" /><label  for="Dinerclub" class='chk_align'><span><?php echo "Dinerclub" ?></span></label>
								<input <?php if(in_array("Paypal", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Paypal" id="Paypal" /><label for="Paypal" class='chk_align'><span><?php echo "Paypal" ?></span></label>
								<input <?php if(in_array("Square", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Square" id="Square" /><label for="Square" class='chk_align'><span><?php echo "Square" ?></span></label>
								<input <?php if(in_array("Debit card", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_payment_method[]" value="Debit card" id="Debit card" /><label for="Debit card" class='chk_align'><span><?php echo "Debit card" ?></span></label>
							</td>
						</tr>
						<tr>
							<td align="right">
							<?php echo $merchant_msg["addlocationdetail"]["Field_services"];?>
							<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_services"]; ?>" >&nbsp;&nbsp;&nbsp</span>
							</td>
							<td align="left">
								<textarea name="services" id="services" class="location_services_textarea" placeholder="Optional"><?php echo $RS->fields['services']; ?></textarea>
								<br/><span id="numeric"><span>Add 15 Services | Comma separated</span>
							</td>
						</tr>
						<tr>
							<td align="right">
							<?php echo $merchant_msg["addlocationdetail"]["Field_amenities"];?>
							<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_amenities"]; ?>" >&nbsp;&nbsp;&nbsp</span>
							</td>
							<td align="left">
								<textarea name="amenities" id="amenities" class="location_amenities_textarea" placeholder="Optional"><?php echo $RS->fields['amenities']; ?></textarea>
								<br/><span id="numeric"><span>Add 15 Amenities | Comma separated</span>
							</td>
						</tr>
						<tr>
							<td align="right">
							<?php echo $merchant_msg["addlocationdetail"]["Field_restrictions"];?>
							<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_restrictions"]; ?>" >&nbsp;&nbsp;&nbsp</span>
							</td>
							<td align="left">
								<textarea name="restrictions" id="restrictions" class="location_services_textarea" placeholder="Optional"><?php echo $RS->fields['restrictions']; ?></textarea>
								<br/><span id="numeric"><span>Add 15 Restrictions | Comma separated</span>
							</td>
						</tr>
						<tr>
							<?php
							$display_google_street_view_image=$RS->fields['display_google_street_view_image'];
							?>
							<td align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_google_street_view"];?></td>
							<td align="left" class="street-view">
								<input id="google_street_yes" <?php if($display_google_street_view_image=="Yes") echo "checked='checked'"; ?> type="radio" name="rdo_google_street" value="Yes" /><label for= "google_street_yes" class='chk_align'><?php echo "Yes" ?></label>
								<input id="google_street_no" <?php if($display_google_street_view_image=="No") echo "checked='checked'"; ?>  type="radio" name="rdo_google_street" value="No" /><label for= "google_street_no" class='chk_align'><?php echo "No" ?></label>
							</td>
						</tr>
						<tr>
					<td width="30%" align="right"><?php echo $merchant_msg["addlocationdetail"]["Field_price_range"];?></td>
					<td align="left">
						<select name="pricerange" id="pricerange">        
								 <option value="0" <? if($RS->fields['pricerange'] == "0") echo "selected";?>>Unspecified</option>
								 <option value="1" <? if($RS->fields['pricerange'] == "1") echo "selected";?>>$ (Under $10)</option>
								<option value="2" <? if($RS->fields['pricerange'] == "2") echo "selected";?>>$$ ($11 - $30)</option>
								<option value="3" <? if($RS->fields['pricerange'] == "3") echo "selected";?>>$$$ ($31 - $60)</option>
								<option value="4" <? if($RS->fields['pricerange'] == "4") echo "selected";?>>$$$$ (Above $61)</option>		
						 </select>
					</td>
				</tr>
				<tr class="prk_cls">
					<td align="right">
					<?php echo $merchant_msg["addlocationdetail"]["Field_parking"];?>
					<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocationdetail"]["tooltip_parking"]; ?>" >&nbsp;&nbsp;&nbsp</span>
					</td>
					<td align="left">
						<?php
						$parking=$RS->fields['parking'];
						$str=explode(",",$parking);
						//print_r($str);
						?>
						<input <?php if(in_array("Garage", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_parking[]" value="Garage" id="Garage" /><label for="Garage" class="chk_align"><?php echo "Garage" ?></label>
						<input <?php if(in_array("Lot", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_parking[]" value="Lot" id="Lot" /><label for="Lot"class="chk_align"><?php echo "Lot" ?></label>
						<input <?php if(in_array("Street", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_parking[]" value="Street" id="Street" /><label for="Street" class="chk_align"><?php echo "Street" ?></label>
						<input <?php if(in_array("Valet", $str)) echo "checked='checked'"; ?> type="checkbox" name="chk_parking[]" value="Valet" id="Valet" /><label for="Valet" class="chk_align"><?php echo "Valet" ?></label>
					</td>
				</tr>
<!--				  <tr>
					<td align="right">Fax Number: </td>
					<td align="left">
						<input type="text" name="fax_number" id="fax_number" value="<?=$RS->fields['fax_number']?>" />
					</td>
				  </tr>-->
				  <?php /**** timezone is commented because now timezone calclation is based on latitude and longitude *******/ ?>
                                 <!--  <tr>
                                      <td align="right"><?php echo $merchant_msg["addlocation"]["Field_time_zone"];?></td>
                                      <td align="left">
                                          <select readonly name='timezone' id='timezone' class='timezone_box'>
                                              <?php
                                              foreach($timezoness as $key=>$value){
                                                  ?>
                                              <option value='<?php echo $key ?>' <?php if($RS->fields['timezone_name']==$key){echo "selected=\"selected\"";} ?> ><?php echo $value; ?></option>
                                              <?php
                                              }
                                              ?>
                                          </select>
										 <img src="<?=ASSETS_CSS ?>/m/images/001.png" class="loctimezonedivimg" alt="" /> 
										  <div class="loctimezonediv">
										   <?php echo $merchant_msg["addlocation"]["time_zone_tooltip"];?>
										  </div> 
                                          
<div  id='helptext'></div>
 <input type="hidden" name="businessname" id="businessname" value="<?php echo $RSBN->fields['business'];?>" />
           <input type="hidden" name="time_zone_v" id="time_zone_v" value=""/>
<script type='text/javascript'>TORBIT.dom={get:function(a){return document.getElementsByTagName(a)},gh:function(){return TORBIT.dom.get("head")[0]},ah:function(a){TORBIT.dom.gh().appendChild(a)},ce:function(a){return document.createElement(a)},gei:function(a){return document.getElementById(a)},ls:function(a,b){var c=TORBIT.dom.ce("script");c.type="text/javascript";c.src=a;if("function"==typeof b){c.onload=function(){if(!c.onloadDone){c.onloadDone=true;b()}};c.onreadystatechange=function(){if(("loaded"===c.readyState||"complete"===c.readyState)&&!c.onloadDone){c.onloadDone=true;b()}}}TORBIT.dom.ah(c)}};(function(){var a=window.TORBIT.timing={};var b=function(){if(window.performance==void 0||window.performance.timing==void 0){k(e);h(f);return}h(d)};var c=function(){var b=window.performance.timing;var c=b.navigationStart;for(var d in b){var e=b[d];if(typeof e!="number"||e==0){continue}a[d]=e-c;var f=/(.+)End$/i.exec(d);if(f){a[f[1]+"Elapsed"]=b[d]-b[f[1]+"Start"]}}};var d=function(){c();g()};var e=function(){a.or=(new Date).getTime()-TORBIT.start_time};var f=function(){a.ol=(new Date).getTime()-TORBIT.start_time;g()};var g=function(){var b="/torbit-timing.php?";for(var c in a){b+=c+"="+a[c]+"&"}if(TORBIT.fv==1)b+="fv=1&";if(TORBIT.opt==0)b+="not_opt=1&";TORBIT.dom.ls(b)};var h=function(a){if(typeof window.onload!="function"){return window.onload=a}var b=window.onload;window.onload=function(){b();a()}};var i=false;var j=function(){};var k=function(a){j=l(a);m()};var l=function(a){return function(){if(!i){i=true;a()}}};var m=function(){if(document.addEventListener){document.addEventListener("DOMContentLoaded",j,false)}else if(document.attachEvent){document.attachEvent("onreadystatechange",j);var a=false;try{a=window.frameElement==null}catch(b){}if(document.documentElement.doScroll&&a){n()}}};var n=function(){if(i){return}try{document.documentElement.doScroll("left")}catch(a){setTimeout(n,5);return}j()};b()})();TORBIT.opt=0;TORBIT.fv=1;</script></body>
                                      </td>
<?php   
  $offset=(5*60*60)+(30*60); //converting 5 hours to seconds.  
  $dateFormat="d-m-Y h:iA";    
 date("d-m-Y h:i:s", mktime(date("H")+1,date("i"),date("s"),date("n"),date("j"),date("y")))."</br>"; 
  $timeNdate=gmdate($dateFormat, time()+$offset);
?>

<script>    
jQuery('#timezone').change(function(){
    var text=  jQuery('#timezone :selected').val();    
    jQuery('#time_zone_v').val(text);
});
</script></tr> -->
<tr>
    <td width="30%" align="right">Location Hours :<span class="notification_tooltip" title="<?php echo $merchant_msg["addlocation"]["Tooltip_Loction_Hours"]; ?>" >&nbsp;&nbsp;&nbsp</span></td>
     <td align="left">
         <?php 
                /*$sql="select * from location_hours where location_id=".$_REQUEST['id']." ORDER BY id ASC" ;
                $RS_hours = $objDB->execute_query($sql);*/
		$RS_hours = $objDB->Conn->Execute("select * from location_hours where location_id=? ORDER BY id ASC",array($_REQUEST['id']));

                $count_week=$RS_hours->RecordCount();
                $weekname=array();
                $allweekname=array('mon','tue','wed','thu','fri','sat','sun');
                if($RS_hours->RecordCount()>0){
                    ?>
                    <div class="hoursdata" >
                         
                    <?php
                    
                        while($Row = $RS_hours->FetchRow()){
                            array_push($weekname,$Row['day']);
                            ?>
                            
                
                                    <input type="hidden" class="weeknamehdn" id="<?php echo $Row['day'];?>hdn" value="<?php echo $Row['day']."-".$Row['start_time']."-".$Row['end_time'];?>" name="<?php echo $Row['day'];?>hdn" />
                                    <div class="cls_left" style="width:100%;"id="<?php echo ucwords($Row['day']);?>">  
                                        <div class="weekday_timing">
                                            <span id="dis_<?php echo ucwords($Row['day']);?>">From : <?php echo $Row['start_time'];?> To : <?php echo $Row['end_time'];?> - <?php echo ucwords($Row['day']);?></span>
                                        </div>
                                        <div class="weekday_timing_close"  >
                                        
                                        <a  id="remove_<?php echo ucwords($Row['day']);?>"  class="removeclass closebuttonclass" href="javascript:void(0)" >

                                           
                                        </a>
                                        </div>
                                    </div>
                
                            
                        <?php } ?>
                           <?php 
                           for($i=0;$i<count($allweekname);$i++)
                           {
                               
                               if(!in_array($allweekname[$i],$weekname ))
                               { ?>
                                   <input type="hidden" class="weeknamehdn" id="<?php echo $allweekname[$i];?>hdn" value="" name="<?php echo $allweekname[$i];?>hdn" />
                               <?php }
                           }
                           
                           
                           ?>         
                            
                                
                        
                                    </div>
         <?php
                }else
                {?>
                    <div class="hoursdata" style="display:none;">
                
                                    <input type="hidden" class="weeknamehdn" id="monhdn" value="" name="monhdn" />
                                    <input type="hidden" class="weeknamehdn" id="tuehdn" value="" name="tuehdn"/>
                                    <input type="hidden" class="weeknamehdn" id="wedhdn" value="" name="wedhdn"/>
                                    <input type="hidden" class="weeknamehdn" id="thuhdn" value="" name="thuhdn"/>
                                    <input type="hidden" class="weeknamehdn" id="frihdn" value="" name="frihdn"/>
                                    <input type="hidden" class="weeknamehdn" id="sathdn" value="" name="sathdn"/>
                                    <input type="hidden" class="weeknamehdn" id="sunhdn" value="" name="sunhdn"/>
                
                
                     </div>
                <?php }
		
                
         ?>
         
         
         <div class="addhoursdiv">
             <input type="button" id="addhoursid" value="Add hours" />

             
         </div>
         
         <div class="timeclass" style="display: none;">
             <div>
             <script>
		  $(function() {
			$('#defaultValueFrom').timepicker({ 'scrollDefaultNow': true });
                        $('#defaultValueTo').timepicker({ 'scrollDefaultNow': true });
		  });
		</script>
		From: <input id="defaultValueFrom" name="from" type="text" class="time hour" />
                To: <input id="defaultValueTo" name="to" type="text" class="time hour" />
                
         
             </div>
	     
             <div id="weekdiv">
                    <span id="monspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass"  id="mon" name="mon" value="Mon" />Mon</span>
                    <span id="tuespan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="tue" name="tue" value="Tue"/>Tue</span>
                    <span id="wedspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="wed" name="wed" value="Wed"/>Wed</span>
                    <span id="thuspan" class="weekspan"><input type="checkbox"  from="" to="" class="weelclass" id="thu" name="thu" value="Thu"/>Thu</span>
                    <span id="frispan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="fri" name="fri" value="Fri"/>Fri</span>
                    <span id="satspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="sat" name="sat" value="Sat"/>Sat</span>
                    <span id="sunspan" class="weekspan"><input type="checkbox" from="" to="" class="weelclass" id="sun" name="sun" value="Sun"/>Sun</span>
             </div>
         <div  class="add_hour_div">
            <input type="button" id="addhourssaveid" value="<?php echo $merchant_msg['index']['btn_save'];?>"  />
            <input type="button" id="addhourscancelid" class="cancl" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" />
         </div>
         
         </div>
         
         
         
         
    </td>
</tr>



<tr id="loc_cat_1">
					<td align="right" class="vertically_top">
					<?php echo $merchant_msg["addlocationdetail"]["Field_location_categories"];
						//echo "a".$RS->fields['categories']."b";
						//echo count($array_cat);
					?>
					<?php
					if($RS->fields['categories']!="")
					{
						if(count($array_cat)==3)
						{
							$c=count($array_cat);
							?>
							<div id="add_cat_tr" ">
								<a href="javascript:void(0);" id="add_cat" name="add_cat" total="<?php echo $c ?>">Add another category</a>
							</div>
							<?php
						}
						else
						{
							$c=count($array_cat)+1;
							$c=count($array_cat);
							?>
							<div id="add_cat_tr" ><a href="javascript:void(0);" id="add_cat" name="add_cat" total="<?php echo $c ?>">Add another category</a></div>
							<?php
						}						
					}
					else
					{
					?>
						<div id="add_cat_tr" ><a href="javascript:void(0);" id="add_cat" name="add_cat" total="1">Add another category</a></div>
					<?php
					}
					?>
					</td>
					<td align="left">
						<?php
						if($RS->fields['categories']=="")
						{
						?>
							<div id="first_lc" style="display:block;">
							<select name="first_cat_first_level" id="first_cat_first_level" size="9">
							<?php
								$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

								$RS_cat_first = $objDB->Conn->Execute($Sql);

								if($RS_cat_first->RecordCount()>0)
								{
									while($Row_cat_first = $RS_cat_first->FetchRow())
									{
										/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
										$RS1=$objDB->Conn->Execute($Sql);*/
										$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));

										if($RS1->RecordCount()>0)
										{
										?>
											<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
										<?php
										}
										else
										{
										?>						
											<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
										<?php
										}
									}
								}	
							?>
							</select>
							</div>
						<?php
						}
						else
						{
						?>
							<div id="first_lc" style="display:none;">
							<select name="first_cat_first_level" id="first_cat_first_level" size="9">
							<?php
								/*$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

								$RS_cat_first = $objDB->Conn->Execute($Sql);*/
								$RS_cat_first = $objDB->Conn->Execute("SELECT * FROM category_level where parent_id=? order by cat_name",array(0));

								if($RS_cat_first->RecordCount()>0)
								{
									while($Row_cat_first = $RS_cat_first->FetchRow())
									{
										/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
										$RS1=$objDB->Conn->Execute($Sql);*/
											
$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));
										if($RS1->RecordCount()>0)
										{
										?>
											<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
										<?php
										}
										else
										{
										?>						
											<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
										<?php
										}
									}
								}	
							?>
							</select>
							</div>
						<?php
						}
						?>
						<div id="second_lc" style="display:none;">
						<select name="second_cat_first_level" id="second_cat_first_level" size="9">
						<?php
							/*$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

							$RS_cat_first = $objDB->Conn->Execute($Sql);*/
							$RS_cat_first = $objDB->Conn->Execute("SELECT * FROM category_level where parent_id=? order by cat_name",array(0));

							if($RS_cat_first->RecordCount()>0)
							{
								while($Row_cat_first = $RS_cat_first->FetchRow())
								{
									/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
									$RS1=$objDB->Conn->Execute($Sql);*/
									$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));

									if($RS1->RecordCount()>0)
									{
									?>
										<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
									<?php
									}
									else
									{
									?>						
										<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
									<?php
									}
								}
							}	
						?>
						</select>
						</div>
						<div id="third_lc" style="display:none;">
						<select name="third_cat_first_level" id="third_cat_first_level" size="9">
						<?php
							/*$Sql = "SELECT * FROM category_level where parent_id=0 order by cat_name";

							$RS_cat_first = $objDB->Conn->Execute($Sql);*/
							$RS_cat_first = $objDB->Conn->Execute("SELECT * FROM category_level where parent_id=? order by cat_name",array(0));

							if($RS_cat_first->RecordCount()>0)
							{
								while($Row_cat_first = $RS_cat_first->FetchRow())
								{
									/*$Sql="select * from category_level where parent_id=".$Row_cat_first['id']." order by cat_name";
									$RS1=$objDB->Conn->Execute($Sql);*/
									$RS1=$objDB->Conn->Execute("select * from category_level where parent_id=? order by cat_name",array($Row_cat_first['id']));

									if($RS1->RecordCount()>0)
									{
									?>
										<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'].' >' ?></option>
									<?php
									}
									else
									{
									?>						
										<option value="<?php echo $Row_cat_first['id'] ?>"><?php echo $Row_cat_first['cat_name'] ?></option>
									<?php
									}
								}
							}	
						?>
						</select>
						</div>
												
					</td>
				  </tr>
				  
				  
				  <tr >
					<td align="right">
						&nbsp;
						
					</td>
					<td align="left">
						<?php
						if($RS->fields['categories']!="")
						{
							if(count($array_cat)==3)
							{
							?>
								<div id="first_lc_delete" class="lc_delete" style="display:block;">
									Selected Category : <span id="first_selected_cat"><?php echo $cat_name1 ?></span><span id="first_selected_cat_delete" class="selected_delete" catid=""></span>
								</div>
							
							<?php
							}
							elseif(count($array_cat)==2)
							{
							?>
								<div id="first_lc_delete" class="lc_delete" style="display:block;">
									Selected Category :<span id="first_selected_cat"><?php echo $cat_name1 ?></span><span id="first_selected_cat_delete" class="selected_delete" catid=""></span>
								</div>
								
							<?php
							}
							elseif(count($array_cat)==1)
							{
							?>
								<div id="first_lc_delete" class="lc_delete" style="display:block;">
									Selected Category : <span id="first_selected_cat"><?php echo $cat_name1 ?></span><span id="first_selected_cat_delete" class="selected_delete" catid=""></span>
								</div>
							<?php
							}
						}
						else
						{
						?>
						
						<div id="first_lc_delete" class="lc_delete" style="display:none;">
							Selected Category : <span id="first_selected_cat"></span><span id="first_selected_cat_delete" class="selected_delete" catid=""></span>
						</div>
						
						<?php
						}
						?>
					</td>
				  </tr>	
					</table>
				</div>
				  
				<h4 id="expanderHeadLocationProfile" style="cursor:pointer;" class="collaspable_div" >
					Location Profile Image <span id="expanderSignLocationProfile" class="collaspable_sign">+</span>
				</h4>
				<div id="expanderContentLocationProfile" style="display:none">
					<table width="100%"  border="0" cellspacing="2" cellpadding="2">
						<tr>
					<td width="30%" align="right"><?php echo $merchant_msg["addlocation"]["Field_picture"];?></td>
					<td align="left">
						<!-- start of  PAY-508-28033   -->
						<!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
						<div class="cls_left">
									<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
									<div id="upload" >
									<span  >Upload Image
									</span> 
									</div>
									</div> 
                                                <div class="browse_right_content"> &nbsp;&nbsp;<span >Or select from </span><a class="mediaclass" > media library </a></div>  

					 <!-- <input type="file" name="business_logo" id="business_logo" />-->
					 <!-- end of  PAY-508-28033   -->
					</td>
				  </tr>
				  <!-- T_7 -->
				  <tr><td align="right">&nbsp; </td>
					<td>
			
						<span id="status" ></span>
						<br/>
           
						<ul id="files" >
          
						 </ul>
					</td>
				  </tr>
					</table>
				</div>		

				<h4 id="expanderHeadLocationAdditional" style="cursor:pointer;display:none;" class="collaspable_div" >
					Additional Images <span id="expanderSignLocationAdditional" class="collaspable_sign">+</span>
				</h4>
				<div id="expanderContentLocationAdditional" style="display:none">
					<table width="100%"  border="0" cellspacing="2" cellpadding="2">
					
						<tr>
                                      <td width="30%" align="right"><?php echo $merchant_msg["addlocation"]["Field_add_additional_images"];?> <span class="notification_tooltip" title="<?php echo $merchant_msg["addlocation"]["Tooltip_additional_images"]; ?>" >&nbsp;&nbsp;&nbsp</span></td>
                                      <td align="left">
						<!-- start of  PAY-508-28033   -->
						<!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
						<div class="cls_left">
									<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
									<div id="upload_more" >
									<span  >Upload Image
									</span> 
									</div>
                                                </div>
                                                <div class="browse_right_content"> &nbsp;&nbsp;<span >Or select from </span><a class="mediaclassmore"  > media library </a></div>    
                                                <div class="browse_right_content"> &nbsp;&nbsp;<span ></span></div>
                                                
					 <!-- <input type="file" name="business_logo" id="business_logo" />-->
					 <!-- end of  PAY-508-28033   -->
					</td>
                                  </tr>
                                  
                                  <tr><td align="right">&nbsp; </td>
					<td>
                                            
						<span id="status_more" ></span> 
                                                <span id="uploading_msg_more"></span> 
                                                <?php 
                                                  
                                                  /*$sql="select * from location_images where location_id=".$_REQUEST['id']." ORDER BY image_id";
                                                  $RS_images = $objDB->execute_query($sql);*/
							$RS_images = $objDB->Conn->Execute("select * from location_images where location_id=? ORDER BY image_id",array($_REQUEST['id']));

                                                  $count_images=$RS_images->RecordCount();
                                                  
                                                  ?>
                                                
                                                    <?php 
                                                 if($count_images > 0)
                                                  {?>  
                                                <!--
												<div class="list_carousel" >
                                                <ul id="files_more" style="">
												-->
												 <div id="additional_images_id">
														<ul id="files_more">
                                                  <?php 
                                                  while($Row = $RS_images->FetchRow()){
                                                      $image_name=  explode(".",$Row['main_image']);
                                                      
                                                  ?>
													<!--
                                                    <li style="list-style:none" id="li_<?php echo $image_name[0];?>">
                                                        <div class='mainmoreclass' style='position:relative'>
                                                            <div class='imagemoreclass' style=''>
                                                                <img src="<?=ASSETS_IMG ?>/m/location/thumb/<?php echo $Row['main_image'];?>" id="img_<?php echo $image_name[0];?>" class='displayimg'></img>
                                                                
                                                            </div>
                                                           
                                                            <input type='hidden' id='hdn_val_<?php echo $image_name[0]; ?>'  name='hdn_more_images[]' value='<?php echo $Row['main_image'];?>' />
                                                            <a style="float: left;margin-top:-18px;margin-top:-25px\9;position: absolute;right: 2px;top: 0;width: 16px;" href="javascript:void(0)" class="closebuttonclass" id="<?php echo $Row['main_image'];?>" onclick='rm_image_more(this.id)' >
                                                                      
                                                                    &#10060;
                                                                </a>
                                                            
                                                        </div>
                                                        
                                                    </li> 
													-->
													<li id="li_<?php echo $image_name[0];?>">
														<div class='mainmoreclass' >
                                                            <div class='imagemoreclass' >
															
																<img src="<?=ASSETS_IMG ?>/m/location/thumb/<?php echo $Row['main_image'];?>" id="img_<?php echo $image_name[0];?>" class='displayimg'>
															</div>
															
                                                           <a  href="javascript:void(0)" class="closebuttonclass" id="<?php echo $Row['main_image'];?>" onclick='rm_image_more(this.id)' >
                                                                      
                                                                    
                                                                </a>
																
                                                            <input type='hidden' id='hdn_val_<?php echo $image_name[0]; ?>'  name='hdn_more_images[]' value='<?php echo $Row['main_image'];?>' />
															
                                                            
                                                           
                                                        </div>
													</li>
                                                  <?php } 
                                                  ?>
												  <!--
                                                    </ul>
                                                        <div class="clearfix"></div>
                                                        <a id="prev2" class="prev" href="#"><img src="<?=ASSETS_IMG ?>/m/pre_add_campaign.png"></img></a>
                                                        <a id="next2" class="next" href="#"><img src="<?=ASSETS_IMG ?>/m/next_add_campaign.png"></img></a>
                                                    </div>
													-->
													</ul>														
													</div>
                                                 <?php }
                                                 else
                                                 {
                                                 ?>
													<!--
                                                     <div class="list_carousel" style="display: none">
                                                         <ul id="files_more" style=""></ul>
                                                         <div class="clearfix"></div>
                                                        <a id="prev2" class="prev" href="#"><img src="<?=ASSETS_IMG ?>/m/pre_add_campaign.png"></img></a>
                                                        <a id="next2" class="next" href="#"><img src="<?=ASSETS_IMG ?>/m/next_add_campaign.png"></img></a>
                                                     </div>    
													 -->
													  <div id="additional_images_id">
														<ul id="files_more">
														</ul>														
													</div>
                                                  <?php   
                                                 }
                                                  ?>  
                                                   
                                                
                                               
					</td>
				  </tr>
					</table>
				</div>
<!--				  <tr>
					<td align="right">Picture: </td>
					<td align="left">
						<input type="file" name="picture" />
					</td>
				  </tr>-->
                                  
				  <!-- T_7 -->
                                  
								<!--	
                                  <tr>
                                      <td align="right" style='width:22%'>
                                          <div style="margin-left:-60px;"><?php echo $merchant_msg["addlocation"]["Field_manage_social_stream"];?> <span class="notification_tooltip" title="<?php echo $merchant_msg["addlocation"]["Tooltip_manage_social_stream"]; ?>" >&nbsp;&nbsp;&nbsp</span></div></td>
                                      <td align="left">
                                          <table>
                                              <tr>
                                                  <td>
                                                      Facebook Page :
                                                  </td>
                                                  <td>
                                                      <input type="radio" id="facebookyes" value="1" <?php if($RS->fields['location_publish'] == "1") echo "checked"; ?> name="facebookradio" /> Yes
                                                        <input type="radio" id="facebookno" value="0" name="facebookradio" <?php if($RS->fields['location_publish'] == "0") echo "checked"; ?>  /> No
                                                      
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td>
                                                      Google+ Page : 
                                                  </td>
                                                  <td>
                                                      <input type="radio" id="googleyes" value="1" <?php if($RS->fields['location_publish_google'] == "1") echo "checked"; ?> name="googleradio" /> Yes
                                              <input type="radio" id="googleno" value="0" name="googleradio" <?php if($RS->fields['location_publish_google'] == "0") echo "checked"; ?>  /> No
                                                      
                                                  </td>
                                              </tr>
                                          </table>
                                          
					</td>
                                  </tr>
								  -->
				  <table width="100%"  border="0" cellspacing="2" cellpadding="2">
				  <tr>
					
					<td align="center" class="location_save">
                                            <script>function btncanLocation(){                                                
                                                window.location="<?=WEB_PATH?>/merchant/locations.php";}</script>
						<input type="hidden" name="id" value="<?=$RS->fields['id']?>" />
						<input type="submit" name="btnEditLocation" value="<?php echo $merchant_msg['index']['btn_update'];?>"  id="btnEditLocation">
                                                 <!--// 369-->
                        <input type="submit" name="btnCancel" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" onClick="btncanLocation()">
                        <!--// 369-->                                             
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
	<!-- start of image upload popup  PAY-508-28033 -->
 <div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
                                        <div id="NotificationBackDiv" class="divBack">
                                        </div>
                                        <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">
                                            
                                             <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                                              style="left:30%;top: 11%;">
                                                
                                                                <div class="modal-close-button" style="visibility: visible;">
                                                             
                                                                    <a  tabindex="0" onclick="close_popup('Notification');" id="fancybox-close" style="display: inline;"></a>
                                                                </div>
                                                <div id="NotificationmainContainer" class="innerContainer" style="height:330px;width:600px">
                                                        <div class="main_content"> 	
                                                         <div style=" background: none repeat scroll 0 0 #222222;color: #CFCFCF !important;padding: 4px;border-radius: 10px 10px 0 0;">
								<font style="font-family: Arial,Helvetica,sans-serif;font-size: 22px;font-weight: bold;letter-spacing: 1px;line-height: 24px;margin: 0;padding: 0 0 2px;text-shadow:1px 1px 1px #DCAAA1">
									<?php echo $merchant_msg["addlocation"]["Field_add_campaign_logo"];?>
								</font>
							 </div>
                                                            <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
							<!-- -->
							<div id="media-upload-header">
								<ul id="sidemenu">
								<li id="tab-type" class="tab_from_library"><a class="current" ><?php echo $merchant_msg["addlocation"]["Field_media_library"];?></a></li>
								
								</ul>
							</div>
							<!-- -->
								
                                                          
							   <div style="clear: both" ></div>
							   <div style="display: none;padding-left: 13px; padding-right: 13px;" class="div_from_computer">
								<div  style="padding-top:10px;padding-bottom:10px"><?php echo $merchant_msg["addlocation"]["Field_add_media_library"];?>
									
								</div>
								<div style="clear: both" ></div>
								<div style="width: 100%;height: 168px;border: dashed 1px black;display: block;" align="center">
									<div style="padding-top:20px;">
									<!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
									<div id="upload" >
									<span  >Upload Photo
									</span>
									</div>
									</div>
								</div>
								<div  align="center" style="padding-top:10px">
                                                                    <input class="save_btn" type="button" name="btn_save_from_computer" id="btn_save_from_computer" onclick="save_from_computer()"  value="<?php echo $merchant_msg['index']['btn_save'];?>"/>
								</div>
							   </div>
							   <div style="display:block;padding-left: 13px; padding-right: 13px;" class="div_from_library">
								<div  style="padding-top:10px;padding-bottom:10px"><?php echo $merchant_msg["addlocation"]["Field_add_campaign_logo_media_library"];?></div>
								<?php
								
									$flag = true;
									$merchant_array = array();
									$merchant_array['id'] = $_SESSION['merchant_id'];
									$merchant_info = $objDB->Show("merchant_user",$merchant_array);
									if($merchant_info->fields['merchant_parent'] != 0)
									{
										
										$media_acc_array = array();
										$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];;
										$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array);
										$media_val = unserialize($RSmedia->fields['media_access']);
										if(in_array("view-use",$media_val))
										{
											$flag = true;
										}
										else{
											$flag = false;	
										}
									}
									else{
										$flag = true;
									}
									
									if($flag)
									{
				
								?>
								<div id="media_library_listing" style="width:100%;height:180px;border:1px dashed #000;overflow:auto;">
									<div style="clear: both"></div>
									<ul class="ul_image_list">
									<?php
										//$query = "select * from merchant_media where image_type='store' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'].") order by id desc" ;
//										$RSImages = $objDB->execute_query($query);
//										 if($RSImages->RecordCount()>0){
//										while($Row = $RSImages->FetchRow()){
										$arr=file(WEB_PATH.'/merchant/process.php?btnGetLogosOfMerchant=yes&mer_id='.$_SESSION['merchant_id']."&img_type=store&mer_parent_id=".$merchant_info->fields['merchant_parent']."&start_index=0&num_of_records=12");
										if(trim($arr[0]) == "")
										{
											unset($arr[0]);
											$arr = array_values($arr);
										}
										$json = json_decode($arr[0]);
										$total_records= $json->total_records;
										$records_array = $json->records;
										if($total_records>0){
											foreach($records_array as $Row)
											{
										?>
									
										
										<li class="li_image_list" id="li_img_<?=$Row->id;?>">
											<div>
												<img src="<?php echo ASSETS_IMG .'/m/location/'.$Row->image;  ?>" height="50px" width="50px" />
												<span style="vertical-align: top" id="span_img_text_<?=$Row->id;?>"><?=$Row->image?></span>
												<span style="vertical-align: top;float: right"> Use this image&nbsp;<input type="radio" name="use_image" value="<?=$Row->id?>" /></span>
											</div>
											
										</li>
										<?php }}?>
									</ul>
									
								</div>
								<div  align="center" style="padding-top:10px">
										<input type="button" class="save_btn" name="btn_save_from_library" id="btn_save_from_library" onclick="save_from_library()"  value="<?php echo $merchant_msg['index']['btn_save'];?>"/>
									</div>
							   </div>
							   <?php
									}
									else{
										?>
										<div  style="padding-top:10px;padding-bottom:10px">
											<?php echo $merchant_msg["addlocation"]["Msg_dont_access_images"];?>
										</div>
										<?php
									}
							   ?>
                                                            </div>
                                                          
                                                        </div>
                                                 </div>
                                            </div>
                                       </div> 
  </div>
	<?php
		echo file_get_contents(WEB_PATH.'/merchant/import_media_library.php?mer_id='.$_SESSION['merchant_id'].'&img_type=store&start_index=0');
	?>
	<?php
		echo file_get_contents(WEB_PATH.'/merchant/import_media_library_more.php?mer_id='.$_SESSION['merchant_id'].'&img_type=store&start_index=0');
	?>
 
	</form>
        <script>
			var file_path = "";

jQuery("#show_more_mediya_browse").live("click",function(){
	var cur_el= jQuery(this);
	var next_index = parseInt(jQuery(this).attr('next_index'));
	var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'show_more_media_browse=yes&next_index='+next_index+'&num_of_records='+num_of_records+"&img_type=campaign",
		async:true,
		success:function(msg)
		{
			var obj = jQuery.parseJSON(msg);
			//alert(obj.status);
			jQuery(".fancybox-inner .ul_image_list").append(obj.html);
			cur_el.attr('next_index',next_index + num_of_records);
			if(parseInt(obj.total_records)<num_of_records)
			{
				cur_el.css("display","none");
			}
		}
	});
});
			
jQuery(".ul_image_list li").live("click",function(){
	
	jQuery(".useradioclass").prop( "checked", false );
	var imgid=jQuery(this).attr("id").split("img_");
	imgid=imgid[1];
	//alert(imgid);
	jQuery(this).find(".useradioclass").prop( "checked", true );
	
	jQuery(".ul_image_list li").removeClass("current");
	jQuery(this).addClass("current");
	
	jQuery(".fancybox-inner .useradioclass").each(function(){
               
		if(jQuery(".fancybox-inner .useradioclass").is(":checked"))
		{
			
			jQuery(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
			jQuery(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
			jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#3C99F4 !important");
		}
		else
		{
			jQuery(".fancybox-inner #btn_save_from_library").attr("disabled",true);
			jQuery(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
			jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#ABABAB !important");
		}
		
	});
            
	
});	
			
var li_count=jQuery('#files_more li').size();
jQuery(function(){
		var btnUpload=jQuery('#upload');
		var status=jQuery('#status');
		
		new AjaxUpload(btnUpload, {
			action: 'merchant_media_upload.php?doAction=FileUpload&img_type=location',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				if(jQuery('#files').children().length > 0)
				{
					jQuery('#files li').detach();
				}
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('<?php echo $merchant_msg["addlocation"]["Msg_Image_Validation"];?>');
					return false;
				}
				status.text('<?php echo $merchant_msg["addlocation"]["Msg_uploading"];?>');
			},
			onComplete: function(file, response){
				//On completion clear the status
				/*
				var arr = response.split("|");
				
				status.text('');
				//Add uploaded file to list
				file_path = arr[1];
				save_from_computer();
                                */
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
                
                /* More Images Upload Code*/
                var btnUpload=$('#upload_more');
                 var uploading=$('#uploading_msg_more');
		var status_more=$('#status_more');
                
                
               
                
              
		var count_image="<?php echo $count_images;?>";
		new AjaxUpload(btnUpload, {
                    
                       
			action: 'upload_additional_images.php?doAction=FileUpload&img_type=location&count_image='+jQuery('#files_more li').length,
			name: 'uploadfile',
			onSubmit: function(file, ext){
				 
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                  
					status_more.text('<?php echo $merchant_msg["addlocation"]["Msg_Image_Validation"];?>');
					return false;
				}       
                                 if(jQuery('#files_more li').length >= 24)
                                     {
                                        
                                         status_more.text('<?php echo $merchant_msg["addlocation"]["Msg_Max_Image_Upload"];?>');
                                          return false;
                                     }
                                     status_more.text('');
                               uploading.text('<?php echo $merchant_msg["addlocation"]["Msg_uploading"];?>');
			},
			onComplete: function(file, response){
                                var arr = response.split("|");
                                
				if(arr[1]=="small")
                                {
                                    status_more.text(arr[0]);
                                    uploading.text('');
                                    
                                }
                                                             
                                else
                                {
                                    
                                uploading.text('');
                                    status_more.text('');
                                    
                                    file_path = arr[1];
                                    var arr = file_path.split("."); 
                                    
                                    //jQuery(".list_carousel").show();
                                    var img = "<div class='mainmoreclass' style='position:relative'><div class='imagemoreclass' style=''><img src='<?=ASSETS_IMG ?>/m/location/temp_thumb/"+ file_path +"' class='displayimg'></div>";
                                    jQuery('<li style="list-style:none" id="li_'+arr[0]+'"></li>').appendTo('#files_more').append(img +"<a  href='javascript:void(0)' id='"+file_path+"' class='closebuttonclass' onclick='rm_image_more(this.id)' ></a><input type='hidden' id='hdn_val_"+arr[0]+"' name='hdn_more_images[]' value='"+ file_path +"' /></div>");	
                                    
                                    /*
									jQuery('#files_more').carouFredSel({
                                                auto: false,
                                                prev: '#prev2',
                                                next: '#next2',
                                                pagination: "#pager2",
                                                mousewheel: true,
                                                height : 80,
                                                width :420,
                                                align:"left",
                                                swipe: {
                                                        onMouse: true,
                                                        onTouch: true
                                                }
                                            });
                                            jQuery('.list_carousel').css("overflow","inherit");
                                    */
                                } 
                                
			}
		});
                /* End More Images Upload Code*/
                function set_variable()
                {
                    alert(count_image);
                   count_image++;  
                   alert(count_image);
                }
                
});

$(document).ready(function() {

	if($("#hdn_image_path").val() != "")
	{
		var img = "<img src='<?=ASSETS_IMG ?>/m/location/"+ $("#hdn_image_path").val() +"' class='displayimg'>";
			
		if ($("#hdn_image_path").val().startsWith("media"))
		{
			$('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png'  class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
		}
		else
		{
			$('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png'  class='cancel_remove_image' onclick='rm_image_permanent(this.id)' /></div></div></div>");
		}
		
	}
});
/* start of image upload div  PAY-508-28033 */
function save_from_library()
{
        $av = jQuery.noConflict();
	 var sel_val = $av('input[name=use_image]:checked').val();
         
	 <!--// 369-->
	 if (sel_val==undefined)
	 {
	 	jQuery.fancybox.close();
	 }
	 else
	 {
		
                
		$av("#hdn_image_id").val(sel_val);
		
            var sel_src = $av(".fancybox-inner #li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
     
	       $av("#hdn_image_path").val(sel_src);
               
	       /* NPE-252-19046 */
	       
	       /* NPE-252-19046 */
	       file_path = "";
	       //close_popup('Notification');
               jQuery.fancybox.close();
	     var img = "<img src='<?=ASSETS_IMG ?>/m/location/"+ sel_src +"' class='displayimg'>";
           
	       $av('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png'  class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
		   /*
		   jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'make_thumb_image_when_assign=yes&image_type=location&filename='+sel_src,
				async:false,
				success:function(msg)
				{
				}
			});
			*/
	 }
	 <!--// 369-->
}

function save_from_library_more()
{
        $av = jQuery.noConflict();
	 var sel_val = $av('input[name=use_image]:checked').val();
         
	 <!--// 369-->
	 if (sel_val==undefined)
	 {
	 	jQuery.fancybox.close();
	 }
	 else
	 {
		
                
	//	$av("#hdn_image_id").val(sel_val);
		
            var sel_src = $av(".fancybox-inner #li_img_"+sel_val+" span[id=span_img_text_"+sel_val+"]").text();
          
	       //$av("#hdn_image_path").val(sel_src);
               var arr = sel_src.split("."); 
               
	       /* NPE-252-19046 */
	       
	       /* NPE-252-19046 */
	       file_path = "";
	       //close_popup('Notification');
               jQuery.fancybox.close();
	     //var img = "<img src='<?=ASSETS_IMG ?>/m/location/"+ sel_src +"' class='displayimg'>";
           
	       //$av('#files_more').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
              jQuery.ajax({
                        type:"POST",
			
			 url:'upload_additional_images.php',
                           data :'doAction=MediaFileUpload&img_type=location&imagename='+sel_src,
			
			success: function(response){
								var status_more=jQuery('#status_more');
								 if(jQuery('#files_more li').length >= 24)
                                     {
                                        
                                         status_more.text('<?php echo $merchant_msg["addlocation"]["Msg_Max_Image_Upload"];?>');
                                          return false;
                                     }
                                var arr_media = response.split("|");
                  
                                
                                
				if(arr_media[0]=="success")
                                {
                                    
                                  //alert("success");
                                    var file_path = arr_media[1];
                                   
                                    var arr_media_split = arr_media[1].split("."); 
                                    //$("#hdn_image_path_more").val(file_path);
                                    //$("#hdn_image_id").val("");
                                    //close_popup('Notification');
                                    //jQuery(".list_carousel").show();
                                    
                                   
                                    var img = "<div class='mainmoreclass' style='position:relative'><div class='imagemoreclass' style=''><img src='<?=ASSETS_IMG ?>/m/location/temp_thumb/"+ arr_media[1] +"' class='displayimg'></div>";
                                    jQuery('<li style="list-style:none" id="li_'+arr_media_split[0]+'"></li>').appendTo('#files_more').append(img +"<a   href='javascript:void(0)' id='"+arr_media[1]+"' class='closebuttonclass' onclick='rm_image_more(this.id)' ></a><input type='hidden' id='hdn_val_"+arr_media_split[0]+"' name='hdn_more_images[]' value='"+ arr_media[1] +"' /></div>");	
                                    //count_image++; 
                                    //save_more_from_computer();
                                    /*
									jQuery('#files_more').carouFredSel({
                                                auto: false,
                                                prev: '#prev2',
                                                next: '#next2',
                                                pagination: "#pager2",
                                                mousewheel: true,
                                                height : 80,
                                                width :420,
                                                align:"left",
                                                swipe: {
                                                        onMouse: true,
                                                        onTouch: true
                                                }
                                            });
                                            jQuery('.list_carousel').css("overflow","inherit"); 
											*/
                                   }
			}
		});
             
	 }
	 <!--// 369-->
}
function save_more_from_computer()
{
        
	
    //$('#files_more').append(img +"<div style='display:table;float:left'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image_more()' /></div></div></div></li></div>");
}
function rm_image()
{
		jQuery("#hdn_image_path").val("");
		jQuery("#hdn_image_id").val("");
		jQuery('#files').html("");
                   
	
}
function rm_image_permanent(id)
{
	
	jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'is_image_delete=yes&image_type=location&filename='+id,
                          async:false,
                           success:function(msg)
                           {
								jQuery("#hdn_image_path").val("");
								jQuery("#hdn_image_id").val("");
								jQuery('#files').html("");
                           }
                           
                     });
	
}
function rm_image_more(data)
{
	
       var arr = data.split("."); 
       var path="<?=ASSETS_IMG ?>/m/location/";
       var locationid="<?php echo $_REQUEST['id'];?>";
       //jQuery("#hdn_val_"+arr[0]).remove();
       jQuery(".list_carousel").append("<input type='hidden' name='hdn_remove_more[]' id='"+arr[0] +"' value='"+data+"' />");
       jQuery("#li_"+arr[0]).remove();
       
	   /*
       jQuery('#files_more').carouFredSel({
                                                auto: false,
                                                prev: '#prev2',
                                                next: '#next2',
                                                pagination: "#pager2",
                                                mousewheel: true,
                                                height : 80,
                                                width :420,
                                                align:"left",
                                                swipe: {
                                                        onMouse: true,
                                                        onTouch: true
                                                }
                                            });
                                            jQuery('.list_carousel').css("overflow","inherit");
       
        jQuery('.list_carousel ul').each(function() {
                          
                        if (jQuery(this).children().length == 0) {
                          jQuery('.list_carousel').hide();
                        }
                      });  
		*/			  
       /* jQuery.ajax({
                           type:"POST",
                           url:'remove_additional_images.php',
                           data :'imagename='+data+'&path='+path+'&locationid='+locationid,
                          async:false,
                           success:function(msg)
                           {
                             
                            // jQuery("#li_"+arr[0]).remove();
                            
                           }
                           
                     });*/
        
}
function rm_image_parmanent(id)
{
	
	
					jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'is_image_delete=yes&image_type=location&filename='+id,
                          async:false,
                           success:function(msg)
                           {
								jQuery("#hdn_image_path").val("");
								jQuery("#hdn_image_id").val("");
								jQuery('#files').html("");
                           }
                           
                     });
	
	
}
function save_from_computer()
{
	jQuery("#hdn_image_path").val(file_path);
	jQuery("#hdn_image_id").val("");
	close_popup('Notification');
	var img = "<img src='<?=ASSETS_IMG ?>/m/location/"+ file_path +"' class='displayimg'>";
	jQuery('#files').html(img +"<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?=ASSETS_IMG ?>/m/mediya_delete.png' id='"+file_path+"' class='cancel_remove_image' onclick='rm_image_parmanent(this.id)'  /></div></div></div>");
}

jQuery("#btnEditLocation").click(function(){
	
	var alert_msg="<?php  echo  $merchant_msg["index"]["validating_data"]; ?>";
var head_msg="<div class='head_msg'>Message</div>";
var content_msg="<div class='content_msg validatingdata' style='background:white;'>"+alert_msg+"</div>";
jQuery("#NotificationloadermainContainer").html(content_msg);

jQuery("#NotificationloaderFrontDivProcessing").css("display","block");
jQuery("#NotificationloaderBackDiv").css("display","block");
jQuery("#NotificationloaderPopUpContainer").css("display","block");
		
	            var address1=jQuery("#address").val();
                    var country=jQuery("#country").val();
					var state=jQuery("#state").val();
                    var city1=jQuery('#city').val();
                    var zipcode1=jQuery('#zip').val();
					
                    
		    var mobileno_area_code=jQuery("#mobileno_area_code").val();
                    var mobileno_area_code_length=jQuery("#mobileno_area_code").val().length;
                    
		    var mobileno=jQuery("#mobileno").val();
                    var mobileno_length=jQuery("#mobileno").val().length;
                      
		    var mobileno2=jQuery("#mobileno2").val()
		    var mobileno2_length=jQuery("#mobileno2").val().length;
                     
                    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
		    var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
			var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
            var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
			zipcode1=zipcode1.toUpperCase();
			
		    var flag="";
                    var msgbox="";
                    var hastagRef=/^[a-zA-Z ,\-&_]+$/i;
                    if(address1 == "")
                    {
                       msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_address"];?></div>";
                       flag="false";
                    }
                   if(state == "0" || state == "")
					{
						msgbox +="<div><?php echo $merchant_msg['profile']['Msg_please_enter_your_state']; ?></div>";
						flag="false";
					}
					
                    if(city1 == "0" || city1 == "")
                    {
                       msgbox +="<div><?php echo $merchant_msg["profile"]["Msg_please_enter_your_city"];?></div>";
                       flag="false";
                    }
                     if(zipcode1 == "")
                    {
                       msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_postal_zipcode"];?></div>";
                       flag="false";
                    }
					else
					{
					  zipcode1=jQuery.trim(zipcode1);
                                    zipcode1=zipcode1.toUpperCase();
					   if(country=="1")
					   {
						   if(!usPostalReg.test(zipcode1)) {
							
							msgbox +="<?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_postal_zipcode"];?>";
							flag="false";
						   }	
							
						}
						else if(country == "2")
						{
							if(!canadaPostalReg.test(zipcode1)) {
							msgbox +="<?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_postal_zipcode"];?>";
							flag="false";
							}
						} 
					}
                    
                    if(mobileno_area_code != "")
		    {
			
			  if(!numericReg.test(mobileno_area_code)) {
			
			
                              msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_area_code_number"];?></div>";
                              flag="false";
                              
			  }
                          else if(mobileno_area_code_length<=2)
                          {
                              msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_area_code_number"];?></div>";
                              flag="false";
                          }
                         
                          
		     }
                     else
                     {
                         msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_area_code_number"];?></div>";
                              flag="false";
                     }
		      if(mobileno != "" || mobileno2 != "")
		      {
                          
			  if(!numericReg.test(mobileno) || !numericReg.test(mobileno2)) {
			      //alert("Please Input Valid Mobile Number");
			      //return false;
                              msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_mobile_number"];?></div>";
                              flag="false";
			  }
                          else if(mobileno_length <=3 || mobileno2_length <= 2)
                          {
                              msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_mobile_number"];?></div>";
                              flag="false";
                          }
                          
			  
		      }
                      else
                      {
                                msgbox +="<div><?php echo $merchant_msg["addlocation"]["Msg_please_enter_valid_mobile_number"];?></div>";
                                flag="false";
                          
                      }
					  if(jQuery("#chk_is_primary").is(":checked")){
							jQuery("#chk_is_primary").attr('value','1');   
								p_status = 1;
							}
							else{
							jQuery("#chk_is_primary").attr('value','0'); 
							
								p_status = 3;
								
							}
		               
		                mobile_no = jQuery("#mobile_country_code").val()+"-"+jQuery("#mobileno_area_code").val()+"-"+jQuery("#mobileno2").val()+"-"+jQuery("#mobileno").val();
						
						merchant_phoneno = "<?php echo $_SESSION['merchant_info']['phone_number']; ?>";
						//alert(mobile_no + " and " +merchant_phoneno);
						var loca_id="<?php echo $_REQUEST['id'];?>";
						
					 
					 /*
					 if(jQuery("#hdnlc1").val()=="" && jQuery("#hdnlc2").val()=="" && jQuery("#hdnlc3").val()=="")
					{
						msgbox+="<div><?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_category"];?></div>";
						flag="false";
					}
					*/
					
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
						/*	
						}
						else
						{
							//alert("reg exp failer");
							msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_add_valid_services"]; ?></div>";
							flag="false";
						}
						*/
					}

					var amenities=jQuery("#amenities").val();
					if(amenities == "")
					{
						
					}
					else
					{
						//if(hastagRef.test(amenities))
						//{
							
							var tag_arr = amenities.split(",");   
							//alert(tag_arr.length);
							if(tag_arr.length>15)
							{
								msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_please_add_amenities"]; ?></div>";
								flag="false";
							}
						/*	
						}
						else
						{
						
							msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_add_valid_amenities"]; ?></div>";
							flag="false";
						}
						*/
					}
					
					var restrictions=jQuery("#restrictions").val();
					if(restrictions == "")
					{
						
					}
					else
					{
						//if(hastagRef.test(restrictions))
						//{
							
							var tag_arr = restrictions.split(",");   
							//alert(tag_arr.length);
							if(tag_arr.length>15)
							{
								msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_please_add_restrictions"]; ?></div>";
								flag="false";
							}
						/*	
						}
						else
						{
						
							msgbox +="<div><?php  echo  $merchant_msg["addlocationdetail"]["Msg_add_valid_restrictions"]; ?></div>";
							flag="false";
						}
						*/
					}
					
					var parkingcount=jQuery('input:checkbox[name="chk_parking[]"]:checked').size();
					if(parkingcount==0)
					{
						//alert("<?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_parking"];?>");
						//return false;
						msgbox+="<div><?php echo $merchant_msg["addlocationdetail"]["Msg_please_select_parking"];?></div>";
						flag="false";
					}
	
					 if(p_status == "1")
					 {
					 jQuery.ajax({
                           type:"POST",
                           url:'process.php',
                           data :'checkprimarylocation=yes&checkboxvalue='+p_status+'&location_id='+loca_id,
                          async:false,
                           success:function(msg)
                           {
                            
                             if(msg == "1")
							 {
							 
							    msgbox +="<div>* <?php echo $merchant_msg["addlocation"]['Msg_primary'] ?></div>";
                                flag="false";
							 }
							 else
							 {
							 
							 
							 }
                               
                           }
                           
                     });
					 }
		   
                   
					var hdn_image_path=jQuery("#hdn_image_path").val();
					//alert(aboutus);
					if(hdn_image_path=="")
					{		
						msgbox +="<div><?php echo $merchant_msg['addlocation']['Msg_Please_Upload_Location_Image']; ?></div>";
						flag="false";
					}	
                      
                     
                      var head_msg="<div class='head_msg'>Message</div>"
                        var content_msg="<div class='content_msg'>"+msgbox+"</div>";
                        var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                        jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
                        
                        if(flag=="false")
                        {
                          
                        close_popup("Notificationloader");
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
                        else
                        {
							var flaglogin=0;
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
                                    flaglogin=1;
                                    window.location.href=obj.link;
                                    
                                }
                                else if(obj.approve == 0)
                                {

                                     var alert_msg="Merchant Status: Blocked , Please contact Scanflip";
                                      var head_msg="<div class='head_msg'>Message</div>"
                                       var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
                                       var href="<?php echo WEB_PATH; ?>/merchant/logout.php";
                                       var footer_msg="<div><hr><a href='"+href+"' class='msg_popup_cancel_anchor'><?php echo $merchant_msg['index']['fancybox_ok'];?></a></div>";
                                       jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);


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
                                                               },
                                                               afterClose: function () {
                                                                       window.location.href=href;
                                                               }

                                       });

                                       flaglogin=1;

                                }
                                else if(obj.approve == 2)
                                {
                                        var alert_msg="Merchant Status: Pending , Please contact Scanflip";
                                      var head_msg="<div class='head_msg'>Message</div>"
                                       var content_msg="<div class='content_msg'>"+alert_msg+"</div>";
                                       var href="<?php echo WEB_PATH; ?>/merchant/my-account.php";
                                       var footer_msg="<div><hr><a href='"+href+"' class='msg_popup_cancel_anchor'><?php echo $merchant_msg['index']['fancybox_ok'];?></a></div>";
                                       jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);


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
                                                               },
                                                               afterClose: function () {
                                                                       window.location.href=href;
                                                               }

                                       });

                                       flaglogin=1;
                                    
                                }
                                else
                                {
                                   flaglogin=0;
                                }
                                
                                
                           }
                           
                         });
                       
                         if(flaglogin == 1)
                         {
                              return false;
                         }
                         else
                         {
							var alert_msg="<?php  echo  $merchant_msg["index"]["saving_data"]; ?>";
							var head_msg="<div class='head_msg'>Message</div>";
							var content_msg="<div class='content_msg savingdata' style='background:white;'>"+alert_msg+"</div>";
							jQuery("#NotificationloadermainContainer").html(content_msg);
		
                            return true;
							
                         }
						}
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

	jQuery("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
	jQuery("#" + popup_name + "BackDiv").fadeOut(200, function () {
		 jQuery("#" + popup_name + "PopUpContainer").fadeOut(200, function () {         
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				jQuery("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
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
/* end of script for PAY-508-28033 */
</script>
</body>
</html>
<script type="text/javascript">
$("a#locations").css("background-color","orange");
$('#country').change(function(){
	var change_value=this.value;
	
	if(change_value == "Canada")
	{
	    
	    $("#state").html("<option value='AB'>AB</option><option value='BC'>BC</option><option value='LB'>LB</option><option value='MB'>MB</option><option value='NB'>NB</option><option value='NF'>NF</option><option value='NS'>NS</option><option value='NT'>NT</option><option value='NU'>NU</option><option value='ON'>ON</option><option value='PE'>PE</option><option value='PQ'>PQ</option><option value='QB'>QB</option><option value='QC'>QC</option><option value='SK'>SK</option><option value='YT'>YT</option>");    
	   
	}
	else
	{
	     
             $("#state").html("<option value='AK'>AK</option><option value='AL'>AL</option><option value='AP'>AP</option><option value='AR'>AR</option><option value='AS'>AS</option><option value='AZ'>AZ</option><option value='CA'>CA</option><option value='CO'>CO</option><option value='CT'>CT</option><option value='DC'>DC</option><option value='DE'>DE</option><option value='FL'>FL</option><option value='FM'>FM</option><option value='GA'>GA</option><option value='GS'>GS</option><option value='GU'>GU</option><option value='HI'>HI</option><option value='IA'>IA</option><option value='ID'>ID</option><option value='IL'>IL</option><option value='IN'>IN</option><option value='KS'>KS</option><option value='KY'>KY</option><option value='LA'>LA</option><option value='MA'>MA</option><option value='MD'>MD</option><option value='ME'>ME</option><option value='MH'>MH</option><option value='MI'>MI</option><option value='MN'>MN</option><option value='MO'>MO</option><option value='MP'>MP</option><option value='MS'>MS</option><option value='MT'>MT</option><option value='NC'>NC</option><option value='ND'>ND</option><option value='NE'>NE</option><option value='NH'>NH</option><option value='NJ'>NJ</option><option value='NM'>NM</option><option value='NV'>NV</option><option value='NY'>NY</option><option value='OH'>OH</option><option value='OK'>OK</option><option value='OR'>OR</option><option value='PA'>PA</option><option value='PR'>PR</option><option value='PW'>PW</option><option value='RI'>RI</option><option value='SC'>SC</option><option value='SD'>SD</option><option value='TN'>TN</option><option value='TX'>TX</option><option value='UT'>UT</option><option value='VA'>VA</option><option value='VI'>VI</option><option value='VT'>VT</option><option value='WA'>WA</option><option value='WI'>WI</option><option value='WV'>WV</option><option value='WY'>WY</option>");
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
    
    $('.mediaclass').click(function(){
	jQuery.fancybox({
                content:jQuery('#mediablock').html(),

                type: 'html',

                openSpeed  : 300,
                closeSpeed  : 300,
				minHeight:300,
				minWidth:400,
                // topRatio: 0,

                changeFade : 'fast',  

                helpers: {
                        overlay: {
                        opacity: 0.3
                        } // overlay
                }

        });
    });
    
    $('.mediaclassmore').click(function(){
	jQuery.fancybox({
                content:jQuery('#mediablockmore').html(),

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
    });
    
     $('#addhoursid').click(function(){
         jQuery(".timeclass").show();
        jQuery(".addhoursdiv").hide();
        jQuery(".hoursdata").hide();

     var arr_mon_hdn = jQuery("#monhdn").val().split("-");
     var arr_tue_hdn = jQuery("#tuehdn").val().split("-");
     var arr_wed_hdn = jQuery("#wedhdn").val().split("-");
     var arr_thu_hdn = jQuery("#thuhdn").val().split("-");
     var arr_fri_hdn = jQuery("#frihdn").val().split("-");
     var arr_sat_hdn = jQuery("#sathdn").val().split("-");
     var arr_sun_hdn = jQuery("#sunhdn").val().split("-");
     
      jQuery("#defaultValueFrom").val('');
      jQuery("#defaultValueTo").val('');
     jQuery(".weelclass").removeAttr("checked");    
     
     
     if(arr_mon_hdn[0] == "mon")
     {
             jQuery("#monspan").hide();
        
          jQuery("#mon").removeAttr( "checked" );    
     }
     if(arr_tue_hdn[0] == "tue")
     {
             jQuery("#tuespan").hide();
            jQuery("#tue").removeAttr( "checked" ); 
     }
     if(arr_wed_hdn[0] == "wed")
     {
             jQuery("#wedspan").hide();
             
             jQuery("#wed").removeAttr( "checked" ); 
     }
     if(arr_thu_hdn[0] == "thu")
     {
             jQuery("#thuspan").hide();
           jQuery("#thu").removeAttr( "checked" );
            
     }
     if(arr_fri_hdn[0] == "fri")
     {
             jQuery("#frispan").hide();
             jQuery("#fri").removeAttr( "checked" );
     }
     if(arr_sat_hdn[0] == "sat")
     {
             jQuery("#satspan").hide();
             jQuery("#sat").removeAttr( "checked" );
     }
     if(arr_sun_hdn[0] == "sun")
     {
             jQuery("#sunspan").hide();
             jQuery("#sun").removeAttr( "checked" );
     }
     
        var i=1;
      
        jQuery(".weekspan").each(function(index){
            
        
            if (jQuery(this).css('display') == 'none') {
                    
              }
              else
                  {
                      
                        if(jQuery("input[class=weelclass]:checked").length == "0")
                           {
                
                                jQuery("#addhourssaveid").attr("disabled", "disabled");
                                jQuery("#addhourssaveid").addClass("disabled");
                                jQuery("#addhourssaveid").css("color","#ABABAB !important");
                        
                           }
                           else
                           {
                               jQuery("#addhourssaveid").removeAttr("disabled");
                               jQuery("#addhourssaveid").removeClass( "disabled" );
                               jQuery("#addhourssaveid").css("color","#0066FF !important");
                           }    
                      
                  }
                  
        });
         
     });
     jQuery('#addhourscancelid').click(function(){
         jQuery(".timeclass").hide();
         jQuery(".addhoursdiv").show();
         
         //alert($('.hoursdata').contents().length);
		 //alert($('.hoursdata div').size());
         if(jQuery('.hoursdata div').size() <= 0)
             {
                jQuery(".hoursdata").hide(); 
             }
             else
                 {
                     jQuery(".hoursdata").show();
                 }
         
         
     });
     jQuery('#addhourssaveid').click(function(){
         
         
         jQuery(".timeclass").hide();
         jQuery(".addhoursdiv").show();
         jQuery(".hoursdata").show();
         //var from=$("#addhourdata").attr("from");
         //var to=$("#addhourdata").attr("to");
         var from=jQuery("#defaultValueFrom").val();
         var to=jQuery("#defaultValueTo").val();
         //alert(from + to);
         var checkboxval="";
         var totalchecked=jQuery('input[class=weelclass]:checked').size();
         
         
         jQuery("input[class=weelclass]:checked").each(function(index) {
           
        
       var weekname= jQuery(this).val().toLowerCase();
       jQuery("#"+weekname+"hdn").val(weekname+"-"+from+"-"+to);
       jQuery("#"+weekname+"hdn").attr("from",from);
       jQuery("#"+weekname+"hdn").attr("to",to);
       
        checkboxval = $(this).val();
        jQuery("#remove_"+weekname).remove();
       //alert(checkboxval);
        //alert(from + to + checkboxval);
                jQuery(".hoursdata").append("<div style='float:left;width:100%;' id='"+jQuery(this).val()+"' ><div class='weekday_timing'><span id='dis_"+ checkboxval +"'>From : " + from.toUpperCase() +" To : " + to.toUpperCase() + " - "+ checkboxval+"</span></div><div class= 'weekday_timing_close'><a href='javascript:void(0)' class='removeclass closebuttonclass' id='remove_"+ checkboxval+"'> </a></div></div>");
               
            });
         
           if(jQuery(".removeclass").length == "7")
                {
                    jQuery("#addhoursid").attr("disabled", "disabled");
                    jQuery("#addhoursid").addClass("disabled");
                    jQuery("#addhoursid").css("color","#ABABAB !important");
                }
                else
                    {
                        
                        jQuery("#addhoursid").removeAttr("disabled");
                        jQuery("#addhoursid").removeClass( "disabled" );
                        jQuery("#addhoursid").css("color","#0066FF !important");
                    }
                    
           
           
           
           
        
         
     });
     
    
   
   
    jQuery("a[id^='remove_']").live("click",function(){
//alert("in");
   var arr = jQuery(this).attr("id").split("_");
   //alert(arr);
    var cid= arr[1];
       jQuery("#"+cid).remove();
         //jQuery("#dis_"+cid).remove();
         
           // jQuery("#remove_"+cid).remove();
            var weekname= arr[1].toLowerCase();
            jQuery("#"+weekname+"hdn").val("0");
            jQuery("#"+weekname+"hdn").attr("from","");
            jQuery("#"+weekname+"hdn").attr("to","");
            jQuery("#"+weekname+"span").show();
            jQuery("#"+weekname).removeAttr("checked");
            jQuery(".weeknamehdn").each(function(index){
               var arr_val=jQuery(this).val().split("-");

               if(jQuery(this).val() == "" || arr_val[0] == "0" )
                   {
                       
                       jQuery("#addhoursid").attr("disabled", false);
                       jQuery("#addhoursid").removeClass( "disabled" );
                       jQuery("#addhoursid").css("color","#0066FF !important");
                   }
                   
            });
           
                   if(jQuery(".removeclass").length == "0")
                    {
                        jQuery(".hoursdata").hide();
                    }
                else
                    {
                        jQuery(".hoursdata").show();
                    
                    }
                    jQuery(".hoursdata").append("<input type='hidden' id='remove_"+ weekname +"' value='"+ weekname +"'  name='remove_"+ weekname +"' />");
                    
            
    });
    
    jQuery(".weelclass").change(function(){
        from(); 
    });
     jQuery("#defaultValueFrom").blur(function(){
        from();
    });
    jQuery("#defaultValueTo").blur(function(){
        from();
    });
    jQuery("#defaultValueFrom").change(function(){
        from();
    })
    jQuery("#defaultValueTo").change(function(){
        from();
    });
    function from()
    {
        
        var from=jQuery("#defaultValueFrom").val();
        var to=jQuery("#defaultValueTo").val();
        var dateReg = /^(1[012]|[1-9]):[0-5][0-9](\\s)?(am|pm)+$/;
         var flag_textbox="true";   
        if(from == "" || to=="")
        {
            //alert("nathi avtu");
            //$("#addhourssaveid").attr("disabled", "disabled"); 
            flag_textbox="false"; 
        }
        else
        {
            
             if(!dateReg.test(from) || !dateReg.test(to)) {
                //$("#addhourssaveid").attr("disabled", "disabled"); 
                flag_textbox="false"; 
             }
             else
                 {
                    flag_textbox="true"; 
                 }
                 
                 
        }
        var flag_checkbox="true";
        jQuery(".weelclass").each(function(index){
            var totalchecked=jQuery('input[class=weelclass]:checked').size();
            
                if(totalchecked == 0)
                {
                   //$("#addhourssaveid").attr("disabled", "disabled"); 
                   flag_checkbox="false";
                }
                else
                {
                    //$("#addhourssaveid").removeAttr("disabled");
                    flag_checkbox="true";
                }
           
        });
         if(flag_textbox == "false"  || flag_checkbox == "false")
            {
                jQuery("#addhourssaveid").attr("disabled", "disabled"); 
                jQuery("#addhourssaveid").addClass("disabled");
                jQuery("#addhourssaveid").css("color","#ABABAB !important");
            }
            else
            {
                jQuery("#addhourssaveid").removeAttr("disabled");
                 jQuery("#addhourssaveid").removeClass( "disabled" );
               jQuery("#addhourssaveid").css("color","#0066FF !important");
            }
    }
   
   
   jQuery(document).ready(function(){
       from();
            if(jQuery(".removeclass").length == "7")
            {
                jQuery("#addhoursid").attr("disabled", "disabled");
                jQuery("#addhoursid").addClass("disabled");
                jQuery("#addhoursid").css("color","#ABABAB !important");
            }
            else
            {

               jQuery("#addhoursid").removeAttr("disabled");
                jQuery("#addhoursid").removeClass( "disabled" );
                jQuery("#addhoursid").css("color","#0066FF !important");
            }
     jQuery(".fancybox-inner .useradioclass").live("change",function(){
             
            jQuery(".fancybox-inner .useradioclass").each(function(){
               
                if(jQuery(".fancybox-inner .useradioclass").is(":checked"))
                    {
                        
                        jQuery(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
                        jQuery(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
                        jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#3C99F4 !important");
                    }
                    else
                    {
                        jQuery(".fancybox-inner #btn_save_from_library").attr("disabled",true);
                        jQuery(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
                        jQuery(".fancybox-inner #btn_save_from_library").css("background-color","#ABABAB !important");
                    }
            })
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
	jQuery("#expanderHeadWeb").click(function(){
		jQuery("#expanderContentWeb").slideToggle();
		if (jQuery("#expanderSignWeb").text() == "+"){
			jQuery("#expanderSignWeb").html("−")
		}
		else {
			jQuery("#expanderSignWeb").text("+")
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
	
	jQuery("#expanderHeadLocationProfile").click(function(){
		jQuery("#expanderContentLocationProfile").slideToggle();
		if (jQuery("#expanderSignLocationProfile").text() == "+"){
			jQuery("#expanderSignLocationProfile").html("−")
		}
		else {
			jQuery("#expanderSignLocationProfile").text("+")
		}
	});
	
	jQuery("#expanderHeadLocationAdditional").click(function(){
		jQuery("#expanderContentLocationAdditional").slideToggle();
		if (jQuery("#expanderSignLocationAdditional").text() == "+"){
			jQuery("#expanderSignLocationAdditional").html("−")
		}
		else {
			jQuery("#expanderSignLocationAdditional").text("+")
		}
	});
   });
   jQuery(document).ready(function(){
	//jQuery('.list_carousel').css("height","145px");
	/*
	jQuery('#files_more').carouFredSel({
	    auto: false,
	    prev: '#prev2',
	    next: '#next2',
	    pagination: "#pager2",
	    mousewheel: true,
            height : 80,
            
	    swipe: {
		    onMouse: true,
		    onTouch: true
	    }
	});
	jQuery('.list_carousel').css("overflow","inherit");
	*/
    });
   function show_tooltip(){
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
					jQuery("#first_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
					jQuery("#first_lc_delete").css("display","block");
					jQuery("#first_selected_cat").text(jQuery("#hdnlcat1").val());
					jQuery("#first_selected_cat_delete").attr("catid",jQuery("#hdnlc1").val());
					
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
		jQuery("#first_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
		jQuery("#first_lc_delete").css("display","block");
		jQuery("#first_selected_cat").text(jQuery("#hdnlcat1").val());
		jQuery("#first_selected_cat_delete").attr("catid",jQuery("#hdnlc1").val());
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
					jQuery("#second_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
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
		jQuery("#second_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
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
					jQuery("#third_cat_second_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
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
		jQuery("#third_cat_third_level").after('<div id="cat_selected1" style="display: inline; vertical-align: top;"><span class="selected"></span></div>');
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
