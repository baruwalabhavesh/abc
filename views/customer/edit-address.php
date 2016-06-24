<?php
/******** 
@USE : edit delivery address
@PARAMETER : 
@RETURN : 
@USED IN PAGES : address_select.php
*********/
//require_once("classes/Config.Inc.php");
?>
<?
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//require_once("classes/Config.Inc.php");
check_customer_session();
//$objDB = new DB();
							$arr_loc=file(WEB_PATH.'/process.php?getselectedaddressbookofuser=yes&user_id='.$_SESSION['customer_id'].'&addrid='.urldecode($_REQUEST['edit']));
							if(trim($arr_loc[0]) == "")
							{
								unset($arr_loc[0]);
								$arr_loc = array_values($arr_loc);
							}
							$all_json_str_loc = $arr_loc[0];
							$json_loc = json_decode($arr_loc[0]);
							$total_records1_loc = $json_loc->total_records;
							$records_array1_loc = $json_loc->records;
	
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Edit Address</title>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?= ASSETS_CSS ?>/c/template.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php
        require_once(CUST_LAYOUT . "/header.php");
        ?>
        <div id="content" class="cantent">
            <div class="my_main_div">
                <div id="contentContainer" class="contentContainer">

                    <div class="address_select">
                        <h1>Edit an address</h1>
						
                        <p>Future mailing labels will appear exactly as you enter them below. This change will not affect orders currently being processed.  </p>
						<hr/>
                       
						
                        <div class="address_form" >
						<p>When finished, click the "Continue" button. </p>
					<!--	<div style="display: none;" id="errorbox" class="errorbox">
						<!--<img alt="" src="images/hoory.png"> 
						<p ;="" >!</p>
						<span id="msg_div">Your request is missing information or needs correcting. Please fix the areas indicated below. When you are done, click the Continue button to send your information again.</span>
						</div> -->
						<?php if($total_records1_loc>0)
							{
								foreach($records_array1_loc as $Row_loc)
								{?>
						<form action="process.php">
						<input type="hidden" name="processorder" id="processorder" value="<?php echo $_REQUEST['order']; ?>" />
							<input type="hidden" name="addr_id" id="addr_id" value="<?php echo urldecode($_REQUEST['edit']); ?>"  />
                            <table cellspacing="10" class="enterAddressFormTable"><tbody>
							<tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressFullName"><b>Full name:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="50" size="50" class="enterAddressFormField" id="enterAddressFullName" name="enterAddressFullName" value="<?php  echo  $Row_loc->full_name; ?>">
                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressAddressLine1"><b>Address Line 1:&nbsp;</b><br><font size="-2">(or company name)</font></label></span></td><td><span>



                                                <input type="text" maxlength="60" size="50" class="enterAddressFormField" id="enterAddressAddressLine1" name="enterAddressAddressLine1" value="<?php  echo  $Row_loc->address_line_1; ?>">
                                            </span>
                                            <br><span class="tiny">Flat/House No, Floor, Building</span></td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressAddressLine2"><b>Address Line 2:&nbsp;</b><br><font size="-2">(optional)</font></label></span></td><td><span>



                                                <input type="text" maxlength="60" size="50" class="enterAddressFormField" id="enterAddressAddressLine2" name="enterAddressAddressLine2" value="<?php  echo  $Row_loc->address_line_2; ?>">
                                            </span>
                                            <br><span class="tiny">Colony/Society, Street, Locality/Area</span></td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressCity"><b>Town/City:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="50" size="25" class="enterAddressFormField" id="enterAddressCity" name="enterAddressCity" value="<?php  echo  $Row_loc->city; ?>">
                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressStateOrRegion"><b>State:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="50" size="15" class="enterAddressFormField" id="enterAddressStateOrRegion" name="enterAddressStateOrRegion" value="<?php  echo  $Row_loc->state; ?>">
                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressPostalCode"><b>Postal Code:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="20" size="20" class="enterAddressFormField" id="enterAddressPostalCode" name="enterAddressPostalCode" value="<?php  echo  $Row_loc->zip; ?>">
                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressCountryCode"><b>Country:&nbsp;</b></label></span></td><td><span>


                                                <select id="enterAddressCountryCode" class="enterAddressFormField" name="enterAddressCountryCode">
												
                                                    <option value="USA" <?php if($Row_loc->country =="USA") { echo "selected";} ?>>USA</option>
                                                    <option value="Canada" <?php if($Row_loc->country =="Canada") { echo "selected";} ?>>Canada</option>
                                                   
                                                </select>





                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressPhoneNumber"><b>Mobile number:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="20" size="15" class="enterAddressFormField" id="enterAddressPhoneNumber" name="enterAddressPhoneNumber" value="<?php  echo  $Row_loc->phone_number; ?>">
                                            </span>
                                            <span class="tiny" id="learnMoreLink"><a id="learnMorePopoverLink" href="#"><b>Learn more</b></a>
											<div id="learn_more_div" style="display:none;">
												This number will be used to assist with scheduling delivery.
											</div>
											</span></td></tr>




                                <input type="hidden" value="0" name="enterAddressIsDomestic">




                                <style type="text/css">

                                    .enterDeliveryPrefsLabel {
                                        text-align: right;
                                        vertical-align: middle;
                                    }

                                    #deliveryPreferences {
                                        color: #E47911;
                                        text-decoration: none;
                                    }

                                    #whatsThisLink a {
                                        color: #004B91;
                                        text-decoration: none;
                                    }

                                    #whatsThisLink a:hover, #whatsThisLink a:active, #whatsThisLink a:hover span, #whatsThisLink a:active span {
                                        color: #E47911;
                                        text-decoration: underline;
                                    }

                                </style>



										<tr><td class="enterAddressFieldLabel"><span><label for="AddressType"><b>Address Type:</b></label></span></td><td><span>



                                            <select id="AddressType" class="enterDeliveryPrefsField" style="width:185px;" name="AddressType">
                                                <option selected="" value="OTH"> Select an Address Type </option>
                                                <option value="1" <?php if($Row_loc->address_type =="1") { echo "selected";} ?>> Residential </option>
                                                <option value="2" <?php if($Row_loc->address_type =="2") { echo "selected";} ?>> Commercial </option>
                                            </select>
                                        </span>
                                    </td></tr>

									<tr><td colspan="2">
									<input type="submit" value="Save & Continue" name="editcontueaddress" id="editcontueaddress" />
									<input type="button" name="backtoaddress" id="backtoaddress" value="Cancel" onclick="window.location.href='<?php echo WEB_PATH;?>/address_select.php?order=<?php echo $_REQUEST['order']; ?>'"/>
									</td>
									</tr>
									
                                </tbody></table>
								</form>
								<?php
								 }
								 }
								?>
                        </div>
                    </div>   
                </div>

                <?php require_once(CUST_LAYOUT. "/before-footer.php"); ?>
            </div><!--end of my_main_div-->
			
        </div><!--end of content-->

        <?php require_once(CUST_LAYOUT. "/footer.php"); ?>

    </body>
</html>
<script>

jQuery("#editcontueaddress").click(function(){
	var flag= true;
	var html = "";
	var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
    var characterReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
    var usPostalReg = /^\d{5}([\-]?\d{4})?$/;
    var canadaPostalReg = /^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/;
	var country = jQuery("#enterAddressCountryCode").val();
	jQuery(".enterAddressFieldError").detach();
	if(jQuery("#enterAddressFullName").val() == "")
	{
		flag = false;
		html = "<span id='enterAddressFullNameerror' class='enterAddressFieldError' >Please enter full name for this address.</span>";
		ele = jQuery("#enterAddressFullName").parent().parent();
		ele.append(html);
		
	}
	if(jQuery("#enterAddressAddressLine1").val() == "" && jQuery("#enterAddressAddressLine2").val() == "")
	{
		flag = false;
		html = "<span id='enterAddressAddressLine1error' class='enterAddressFieldError' >At least one address line must be entered.</span>";
		ele = jQuery("#enterAddressAddressLine1").parent().parent();
		ele.append(html);
	}
	if(jQuery("#enterAddressCity").val() == "" )
	{
		flag = false;
		html = "<span id='enterAddressCityerror' class='enterAddressFieldError' >Please enter city for this address.</span>";
		ele = jQuery("#enterAddressCity").parent().parent();
		ele.append(html);
	}
	if(jQuery("#enterAddressStateOrRegion").val() == "" )
	{
		flag = false;
		html = "<span id='enterAddressStateOrRegionerror' class='enterAddressFieldError' >Please enter State/Region for this address.</span>";
		ele = jQuery("#enterAddressStateOrRegion").parent().parent();
		ele.append(html);
	}
	if(jQuery("#enterAddressPostalCode").val() == "" )
	{
		flag = false;
		html = "<span id='enterAddressPostalCodeerror' class='enterAddressFieldError' >Please enter postal code for this address.</span>";
		ele = jQuery("#enterAddressPostalCode").parent().parent();
		ele.append(html);
	}
	else
	{
		postal_code = jQuery("#enterAddressPostalCode").val();
			postal_code=jQuery.trim(postal_code);
		postal_code=postal_code.toUpperCase();
			//alert(country);
			//alert(postal_code);
			if(country=="USA")
			{
			   if(!usPostalReg.test(postal_code)) {
					flag = false;
					html = "<span id='enterAddressPostalCodeerror' class='enterAddressFieldError' >Please enter postal code for this address.</span>";
		ele = jQuery("#enterAddressPostalCode").parent().parent();
					ele.append(html);
			   }	

			}
			else if(country == "Canada")
			{
				if(!canadaPostalReg.test(postal_code)) {
					flag = false;
					html = "<span id='enterAddressPostalCodeerror' class='enterAddressFieldError' >Please enter postal code for this address.</span>";
		ele = jQuery("#enterAddressPostalCode").parent().parent();
					ele.append(html);
				}
			}
	}
	if(jQuery("#enterAddressPhoneNumber").val() == "" )
	{
		flag = false;
		html = "<span id='enterAddressPhoneNumbererror' class='enterAddressFieldError' >Please enter phone number for this address.</span>";
		ele = jQuery("#enterAddressPhoneNumber").parent().parent();
		ele.append(html);
	}
	else{
		if(!numericReg.test(jQuery("#enterAddressPhoneNumber").val())) {
			flag = false;
			html = "<span id='enterAddressPhoneNumbererror' class='enterAddressFieldError' >Please enter phone number for this address.</span>";
			ele = jQuery("#enterAddressPhoneNumber").parent().parent();
			ele.append(html);
		}
	}
	if(jQuery("#address_appear").val() == "" )
	{
		flag = false;
		html = "<span id='address_appearerror' class='enterAddressFieldError' >Please enter how this address appears in dropbox.</span>";
		ele = jQuery("#address_appear").parent().parent();
		ele.append(html);
	}
	if(jQuery("#AddressType").val() == "OTH" )
	{
		flag = false;
		html = "<span id='AddressTypeerror' class='enterAddressFieldError' >Please select address type.</span>";
		ele = jQuery("#AddressType").parent().parent();
		ele.append(html);
	}
	if(!flag)
	{
		jQuery("#errorbox").css("display","block");
		return false;
	}
	else{
		jQuery("#errorbox").css("display","none");
	}

});
jQuery("#learnMorePopoverLink").toggle(function(){
	jQuery(this).next().css("display","block");
},function()
{
	jQuery(this).next().css("display","none");
});
</script>
<?php
$_SESSION['req_pass_msg'] = "";
?>
