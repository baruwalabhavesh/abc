<?php
/* * ****** 
  @USE : review delivery address
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : address_select.php, process.php
 * ******* */
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//require_once("classes/Config.Inc.php");
check_customer_session();
//$objDB = new DB();

$arr_loc = file(WEB_PATH . '/process.php?getselectedaddressbookofuser=yes&user_id=' . $_SESSION['customer_id'] . '&addrid=' . $_REQUEST['edit']);
if (trim($arr_loc[0]) == "") {
        unset($arr_loc[0]);
        $arr_loc = array_values($arr_loc);
}
$all_json_str_loc = $arr_loc[0];
$json_loc = json_decode($arr_loc[0]);
$total_records1_loc = $json_loc->total_records;
$records_array1_loc = $json_loc->records;
$arr1 = array();
$arr1 = file(WEB_PATH . '/process.php?getuserpointsbalance=yes&customer_id=' . $_SESSION['customer_id']);
if (trim($arr1[0]) == "") {
        unset($arr1[0]);
        $arr1 = array_values($arr1);
}
$all_json_str1 = $arr1[0];
$json1 = json_decode($arr1[0]);
$point_balance = $json1->point_balance;
$sql = "select redeem_point_value from giftcards where id=" . base64_decode($_REQUEST['order']);
$RS_order = $objDB->Conn->Execute($sql);
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Review Order</title>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?= ASSETS_CSS ?>/c/template.css" rel="stylesheet" type="text/css">
    </head>
    <?php flush(); ?>
    <body>
        <?php
        require_once(CUST_LAYOUT . "/header.php");
        ?>
        <input type="hidden" name="hdn_giftcardid" id="hdn_giftcardid" value="<?php echo base64_decode($_REQUEST['order']); ?>" />
        <input type="hidden" name="hdn_available_points" value="<?php echo $point_balance; ?>" id="hdn_available_points" />
        <input type="hidden" name="hdn_needed_points" value="<?php echo $RS_order->fields['redeem_point_value']; ?>" id="hdn_needed_points" />
        <input type="hidden" name="hdn_user_country" id="hdn_user_country" value="<?php echo $_SESSION['customer_info']['country']; ?>" />
        <div id="content" class="cantent">
            <div class="my_main_div">
                <div id="contentContainer" class="contentContainer">

                    <div class="address_select">
                        <h1>Review Your Order</h1>
                        <div class="p_div_wrap">
                            <?php if ($total_records1_loc > 0) {
                                    ?>
                                    <p>By clicking on the 'Check Out' button, Your order will get processed.</p>

                                    <?} else { ?>
                            <?php } ?>
                            <div class="div_btn_wrap">
                                <input type="button" name="proccessedredeem" id="proccessedredeem" value="Check Out" />
                                <input type="button" name="proccessedcancel" id="proccessedcancel" value="Cancel" onclick="window.location.href = '<?php echo WEB_PATH; ?>/shop-redeem.php'"/>
                                <input type="button" name="backtoaddress1" id="backtoaddress1" value="Back" onclick="window.location.href = '<?php echo WEB_PATH; ?>/address_select.php?order=<?php echo $_REQUEST['order']; ?>'"/>
                            </div>
                        </div>
                        <hr/>
                        <div class="div_ship">
                            <h2>Ship to address</h2>
                            <ul>
                                <?php
                                if ($total_records1_loc > 0) {
                                        foreach ($records_array1_loc as $Row_loc) {
                                                ?>
                                                <li>
                                                    <?php $full_address = $Row_loc->full_name . ", " . $Row_loc->address_line_1 . ", " . $Row_loc->address_line_2 . ",  " . $Row_loc->city . ", " . $Row_loc->state . ", " . $Row_loc->zip . ", " . $Row_loc->country . ", " . $Row_loc->phone_number; ?>
                                                    <input type="hidden" name="hdn_ship_to_address" id="hdn_ship_to_address" value="<?php echo urlencode($full_address); ?>" />
                                                    <ul class="displayAddressUL">
                                                        <li class="displayAddressLI displayAddressFullName"><b><?php echo $Row_loc->full_name; ?></b></li>
                                                        <li class="displayAddressLI displayAddressAddressLine1"><?php echo $Row_loc->address_line_1; ?></li>
                                                        <li class="displayAddressLI displayAddressAddressLine2"><?php echo $Row_loc->address_line_2; ?></li>
                                                        <li class="displayAddressLI displayAddressCityStateOrRegionPostalCode"><?php echo $Row_loc->city . ", " . $Row_loc->state, ", " . $Row_loc->zip; ?></li>
                                                        <li class="displayAddressLI displayAddressCountryName"><?php echo $Row_loc->country; ?></li>
                                                        <li class="displayAddressLI displayAddressPhoneNumber">Phone:<?php echo $Row_loc->phone_number; ?></li>
                                                        <li class="displayAddressLI displayAddressPhoneNumber">Address Type :
                                                            <?php
                                                            if ($Row_loc->address_type == 1)
                                                                    echo "Residential";
                                                            else
                                                                    echo "Commercial";
                                                            ?>
                                                        </li>

                                                    </ul>
                                                </li>
                                        <?php
                                        }
                                }
                                ?>

                            </ul>

                        </div>


                        <div class="div_card">

                            <h2>Order Detail</h2>
                            <?php
                            $arr_loc = file(WEB_PATH . '/process.php?getgiftcardinformation=yes&giftcardid=' . base64_decode($_REQUEST['order']));
                            if (trim($arr_loc[0]) == "") {
                                    unset($arr_loc[0]);
                                    $arr_loc = array_values($arr_loc);
                            }
                            $all_json_str_loc = $arr_loc[0];
                            $json_loc = json_decode($arr_loc[0]);
                            $total_records1_loc = $json_loc->total_records;
                            $records_array1_loc = $json_loc->records;
                            ?>
                            <?php
                            if ($total_records1_loc > 0) {
                                    foreach ($records_array1_loc as $Row_loc) {
                                            ?>
                                            <li>
                                                <ul class="displayAddressUL">
                                                    <li class="displayAddressLI displayAddressFullName"><b><?php echo $Row_loc->title; ?></b></li>
                                                    <li class="displayAddressLI displayAddressAddressLine1"><span>Redeem Points Required:</span><?php echo $Row_loc->redeem_point_value; ?></li>
                                                    <li class="displayAddressLI displayAddressAddressnote"><span>Please check your order carefully while redeeming your Scanflip points. Scanflip is unable to cancel orders for gift cards. Please allow up to 4 weeks for delivery of your order. <p>Deliveries cannot be made  to Alaska/Hawaii, US Protectorates , Post Office Box, rural routes, or to addresses outside of customer residential country.</span><li>
                                                    <!--<li class="displayAddressLI displayAddressAddressLine2"><span>Ship To :</span><?php echo $Row_loc->ship_to; ?></li>
                                                    <li class="displayAddressLI displayAddressCityStateOrRegionPostalCode"><p><?php echo $Row_loc->description; ?></p></li> -->


                                                </ul>
                                            </li>
                                            <?php
                                    }
                            }
                            ?>

                        </div>
                        <hr/>

                        <div class="address_form" style="display:<?php
                        if ($total_records1_loc != 0) {
                                echo "none";
                        } else {
                                echo "block";
                        }
                        ?>">
                            <h1>Enter a new delivery address. </h1>
                            <p>When finished, click the "Continue" button. </p>
                            <form action="process.php">
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
                                            </td></tr><tr><td style="vertical-align:middle;" class="enterAddressFieldLabel"><span><label for="enterAddressPostalCode"><b>Postal code:&nbsp;</b></label></span></td><td><span>



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




                               <!-- <tr><td colspan="2"><br><span id="deliveryPreferences"><b>Additional Address Details</b></span>&nbsp;<span class="tiny" id="whatsThisLink">(<a id="whatsThisPopoverLink" href="#"><b>What's this?</b></a>)</span></td></tr>
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
                                                                            <!--<tr><td class="enterAddressFieldLabel"><span><label for="AddressType"><b>How Address appear in dropbox:</b></label></span></td><td><span>
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
    <div id="dialog-message" style="display:none;">
    </div>
</html>
<script>
        jQuery("#proccessedredeem").click(function () {
            /*** check user is login or not ***********/
            loginstatus = false;
            $.ajax({
                type: "POST",
                url: 'process.php',
                data: 'shop_redeem_login_or_not=true',
                async: false,
                success: function (msg)
                {
                    var obj = jQuery.parseJSON(msg);
                    if (obj.status == "false")
                    {
                        loginstatus = false;
                        window.location.href = obj.link;//+obj.hash_link;
                        //location.reload();

                    }
                    else
                    {
                        loginstatus = true;
                    }
                }
            });
            var availbalestatus = false;
            var giftcardshipto = jQuery(".displayAddressCountryName").html();
            if (loginstatus)
            {
                if (jQuery("#hdn_user_country").val() == giftcardshipto)
                {
                    availbalestatus = true;
                }
                else {
                    var msg_box = "Sorry this giftcard is excluded from your ship to country.";
                    var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                    var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg_box + "</div>";
                    var footer_msg = "<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"]; ?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                    jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                    try
                    {

                        jQuery.fancybox({
                            content: jQuery('#dialog-message').html(),
                            type: 'html',
                            openSpeed: 300,
                            closeSpeed: 300,
                            // topRatio: 0,
                            changeFade: 'fast',
                            beforeShow: function () {
                                jQuery(".fancybox-inner").addClass("msgClass");
                            },
                            helpers: {
                                overlay: {
                                    opacity: 0.3
                                } // overlay
                            }
                        });
                    }
                    catch (e)
                    {
                        //alert(e);
                    }
                    availbalestatus = false;
                }
                if (availbalestatus)
                {
                    if (parseInt(jQuery("#hdn_available_points").val()) < parseInt(jQuery("#hdn_needed_points").val()))
                    {
                        var msg_box = "You do not have enough scanflip points to redeem this order.";
                        var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                        var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg_box + "</div>";
                        var footer_msg = "<div style='text-align:center'><hr><input type='button'  value='<?php echo $client_msg["index"]["Btn_Fancy_Ok"]; ?>' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                        jQuery.fancybox({
                            content: jQuery('#dialog-message').html(),
                            type: 'html',
                            openSpeed: 300,
                            closeSpeed: 300,
                            // topRatio: 0,

                            changeFade: 'fast',
                            beforeShow: function () {
                                $(".fancybox-inner").addClass("msgClass");
                            },
                            helpers: {
                                overlay: {
                                    opacity: 0.3
                                } // overlay
                            }
                        });
                        return false;
                    }
                    else {
                        $.ajax({
                            type: "POST",
                            url: 'process.php',
                            data: 'proccessedredeem=true&giftcard_id=' + jQuery("#hdn_giftcardid").val() + '&shipping_address=' + jQuery("#hdn_ship_to_address").val(),
                            async: false,
                            success: function (msg)
                            {
                                var obj = jQuery.parseJSON(msg);
                                window.location.href = "<?php echo WEB_PATH; ?>/success-order.php?order=" + obj.order_number;
                            }
                        });
                    }
                }
            }
        });
        jQuery("#popupcancel").live("click", function () {
            jQuery.fancybox.close();
            return false;
        });
</script>
<?php
$_SESSION['req_pass_msg'] = "";
?>
