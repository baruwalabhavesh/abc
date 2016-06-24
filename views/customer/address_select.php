<?php
/******** 
@USE : select delivery address
@PARAMETER : 
@RETURN : 
@USED IN PAGES : addnew-address.php, edit-address.php, process.php, review-address.php, shop-redeem.php
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//require_once("classes/Config.Inc.php");
check_customer_session();
//$objDB = new DB();

if(isset($_REQUEST['id']))
{
	$array_where = array();
	$array_where['id'] = $_REQUEST['id'];
	$objDBWrt->Remove("customer_addressbook", $array_where);
}
//echo WEB_PATH.'/includes/customer/process.php?getaddressbookofuser=yes&user_id='.$_SESSION['customer_id'];
$arr_loc=file(WEB_PATH.'/process.php?getaddressbookofuser=yes&user_id='.$_SESSION['customer_id']);
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
        <title>ScanFlip | Select Address</title>
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
                        <h1>Select a delivery address</h1>
						<?php 
						if($total_records1_loc>0)
						{
						?>
							<p>Click the Edit button next to any piece of information below that you'd like to update. </p>
							<?php
							if($total_records1_loc<2)
							{
							?>
							<p>Click here to add a new address. -> <a href="<?php echo WEB_PATH; ?>/addnew-address.php?order=<?php echo $_REQUEST['order']; ?>">Enter a new address</a></p>
						<?
							}
						} 
						else 
						{					
						 ?>
							<p>Click here to add a new address. -> <a href="<?php echo WEB_PATH; ?>/addnew-address.php?order=<?php echo $_REQUEST['order']; ?>">Enter a new address</a></p>
						<?php					
						}
						?>
                        <hr/>
                       <?php 
						if($total_records1_loc>0)
						{
						?>
					   <div class="address_list">
						<ul>
						    <?php
							foreach($records_array1_loc as $Row_loc)
							{
							?>
							<li>
									<ul class="displayAddressUL">
										<li class="displayAddressLI displayAddressFullName"><b><?php  echo  $Row_loc->full_name; ?></b></li>
										<li class="displayAddressLI displayAddressAddressLine1"><?php  echo  $Row_loc->address_line_1; ?></li>
										<li class="displayAddressLI displayAddressAddressLine2"><?php  echo  $Row_loc->address_line_2; ?></li>
										<li class="displayAddressLI displayAddressCityStateOrRegionPostalCode"><?php  echo  $Row_loc->city.", ".$Row_loc->state,", ".$Row_loc->zip; ?></li>
										<li class="displayAddressLI displayAddressCountryName"><?php  echo  $Row_loc->country; ?></li>
										<li class="displayAddressLI displayAddressPhoneNumber">Phone:<?php  echo  $Row_loc->phone_number; ?></li>
										<li class="displayAddressLI displayAddressPhoneNumber">Address Type :
										<?php
											if($Row_loc->address_type==1)
												echo "Residential";
											else	
												echo "Commercial";
										?>
										</li>
										<li><a href="<?php echo WEB_PATH; ?>/edit-address.php?edit=<?php echo urlencode($Row_loc->id); ?>&order=<?php echo $_REQUEST['order']; ?>">Edit</a> &nbsp; &nbsp; &nbsp; <a id="delete_address" href="<?php echo WEB_PATH."/address_select.php?order=".$_REQUEST['order']."&id=".$Row_loc->id ?>">Delete</a> &nbsp; &nbsp; &nbsp; <a href="<?php echo WEB_PATH; ?>/review-address.php?edit=<?php echo urlencode($Row_loc->id); ?>&order=<?php echo $_REQUEST['order']; ?>">Use this address</a></li>
									</ul>
							</li>
							<?php 
							}
							?>							
						</ul>
						<hr />
					   </div>
						<?php
						} 
						?>
                        <div class="address_form" style="display:<?php if($total_records1_loc !=0){ echo "none";} else { echo "none";} ?>">
						 <h1>Enter a new delivery address. </h1>
                        <p>When finished, click the "Continue" button. </p>
						<form action="includes/process.php">
                            <table cellspacing="10" class="enterAddressFormTable"><tbody>
							<tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressFullName"><b>Full name:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="50" size="50" class="enterAddressFormField" id="enterAddressFullName" name="enterAddressFullName">
                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressAddressLine1"><b>Address Line 1:&nbsp;</b><br><font size="-2">(or company name)</font></label></span></td><td><span>



                                                <input type="text" maxlength="60" size="50" class="enterAddressFormField" id="enterAddressAddressLine1" name="enterAddressAddressLine1">
                                            </span>
                                            <br><span class="tiny">Flat/House No, Floor, Building</span></td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressAddressLine2"><b>Address Line 2:&nbsp;</b><br><font size="-2">(optional)</font></label></span></td><td><span>



                                                <input type="text" maxlength="60" size="50" class="enterAddressFormField" id="enterAddressAddressLine2" name="enterAddressAddressLine2">
                                            </span>
                                            <br><span class="tiny">Colony/Society, Street, Locality/Area</span></td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressCity"><b>Town/City:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="50" size="25" class="enterAddressFormField" id="enterAddressCity" name="enterAddressCity">
                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressStateOrRegion"><b>State:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="50" size="15" class="enterAddressFormField" id="enterAddressStateOrRegion" name="enterAddressStateOrRegion">
                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressPostalCode"><b>Postal Code:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="20" size="20" class="enterAddressFormField" id="enterAddressPostalCode" name="enterAddressPostalCode">
                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressCountryCode"><b>Country:&nbsp;</b></label></span></td><td><span>


                                                <select id="enterAddressCountryCode" class="enterAddressFormField" name="enterAddressCountryCode">
                                                    <option value="USA">USA</option>
                                                    <option value="Canada">Canada</option>
                                                   
                                                </select>





                                            </span>
                                        </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressPhoneNumber"><b>Mobile number:&nbsp;</b></label></span></td><td><span>



                                                <input type="text" maxlength="20" size="15" class="enterAddressFormField" id="enterAddressPhoneNumber" name="enterAddressPhoneNumber">
                                            </span>
                                            <span class="tiny" id="learnMoreLink"><a id="learnMorePopoverLink" href="#"><b>Learn more</b></a></span></td></tr>




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




                             <!--   <tr><td colspan="2"><br><span id="deliveryPreferences"><b>Additional Address Details</b></span>&nbsp;<span class="tiny" id="whatsThisLink">(<a id="whatsThisPopoverLink" href="#"><b>What's this?</b></a>)</span></td></tr>
								<tr><td class="enterAddressFieldLabel"><span><label for="Landmark"><b>Landmark:</b></label></span><br><span class="tiny">&nbsp;</span></td><td><span>


                                            <input type="text" maxlength="60" size="26" class="enterDeliveryPrefsField" id="Landmark" name="Landmark"></span>
                                        <br><span class="tiny">A landmark helps us in locating your address better</span></td></tr> -->
										<tr><td class="enterAddressFieldLabel"><span><label for="AddressType"><b>Address Type:</b></label></span></td><td><span>



                                            <select id="AddressType" class="enterDeliveryPrefsField" style="width:185px;" name="AddressType">
                                                <option selected="" value="OTH"> Select an Address Type </option>
                                                <option value="1"> Residential </option>
                                                <option value="2"> Commercial </option>
                                            </select>
                                        </span>
                                    </td></tr>
								<!--	<tr><td class="enterAddressFieldLabel"><span><label for="AddressType"><b>How Address appear in dropbox:</b></label></span></td><td><span>
										<input type="text" name="address_appear" id="address_appear" />
                                        </span>
                                    </td></tr>
									<tr><td class="enterAddressFieldLabel" colspan="2"><span><input type="checkbox" name="chk_set_default"  />Set as default delivery address</td></tr> -->
									<tr><td colspan="2"><input type="submit" value="Save & Continue" name="savecontueaddress" id="savecontueaddress" /></td>
									</tr>
									
                                </tbody></table>
								</form>
                        </div>
                    </div>   
                </div>

               <?php require_once(CUST_LAYOUT . "/before-footer.php"); ?> 
            </div><!--end of my_main_div-->
			
        </div><!--end of content-->

        <?php require_once(CUST_LAYOUT . "/footer.php"); ?>

    </body>
</html>
<?php
$_SESSION['req_pass_msg'] = "";
?>
