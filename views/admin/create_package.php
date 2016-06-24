<?php
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY . "/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Admin Panel</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="<?= ASSETS_CSS ?>/a/template.css" rel="stylesheet" type="text/css">
    </head>

    <div id="container">

        <!---start header---->

        <?php
        require_once(ADMIN_LAYOUT . "/header.php");
        ?>
        <div id="contentContainer">


            <div  id="sidebarLeft">
                <?php
                require_once(ADMIN_VIEW . "/quick-links.php");
                ?>
                <!--end of sidebar Left--></div>

            <div id="content">
                <h2>Create Package</h2>
                <form action="process.php" method="post">
                    <table width="75%" align="center"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
                        <tr>
                            <th align="right">&nbsp;</th>
                            <th align="left" style="color:#FF0000; "><?= $_SESSION['msg'] ?></th>
                        </tr>
                        <tr>
                            <th width="50%" align="right">Package Name:</th>
                            <th width="50%" align="left"><input type="text" name="pac_name" id="pac_name"/></th>
                        </tr>
                        <tr>
                            <th width="50%" align="right">Monthly Subscription Fee : </th>
                            <th width="50%" align="left"><input type="text" name="price" id="price" /></th>
                        </tr>
                        <tr>
                            <th align="right">No. of locations: </th>
                            <th align="left"><input type="text" name="no_of_loca" id="no_of_loca" size="5" /></th>
                        </tr>
                        <tr>
                            <th align="right">No of active campaigns per location: </th>
                            <th align="left"><input type="text" name="no_of_active_camp_per_loca" id="no_of_active_camp_per_loca" size="5" /></th>
                        </tr>
                        <tr>
                            <th align="right">Total number of campaigns per month: </th>
                            <th align="left"><input type="text" name="total_no_of_camp_per_month" id="total_no_of_camp_per_month" size="5" /></th>
                        </tr>
                        <tr>
                            <th align="right">Minimum referral points: </th>
                            <th align="left"><input type="text" name="min_share_point" id="min_share_point" size="5" /></th>
                        </tr>
                        <tr>
                            <th align="right">Minimum reward Points: </th>
                            <th align="left"><input type="text" name="min_reward_point" id="min_reward_point" size="5" /></th>
                        </tr>
                        <tr>
                            <th align="right">Campaign redemption (Transaction) fee in points:</th>
                            <th align="left"><input type="text" name="transaction_fees" id="transaction_fees"  /></th>
                        </tr>

                        <tr>
                            <th align="right">Coupon redemption fee (% of discount campaign value):</th>
                            <th align="left"><input type="checkbox" name="chk_c_r_fee" id="chk_c_r_fee"  />Apply Redemption Fee</th>
                        </tr>
                        <tr>
                            <th align="right">No. of active loyalty cards:</th>
                            <th align="left"><input type="text" name="active_loyalty_cards" id="active_loyalty_cards"  /></th>
                        </tr>
                        <tr>
                            <th align="right">Loyalty card stamp redemption (Transaction) fee in points:</th>
                            <th align="left"><input type="text" name="transaction_fees_stamp" id="transaction_fees_stamp"  /></th>
                        </tr>
                        <tr>
                            <th align="right">No of Reward zone active gift_card:</th>
                            <th align="left"><input type="text" name="reward_zone_active_gift_card" id="reward_zone_active_gift_card"  /></th>
                        </tr>
                        <tr>
                            <th align="right">No of Reward zone active campaign:</th>
                            <th align="left"><input type="text" name="reward_zone_active_campaign" id="reward_zone_active_campaign"  /></th>
                        </tr>
                        <?php
                        $currencies = $objDB->Conn->Execute('Select country_name,currency_code from currencies');
                        ?>
                        <tr>
                            <th align="right">Currency:</th>
                            <th align="left">
                                <select name="currency" id="currency">
                                    <?php
                                    if ($currencies->RecordCount() > 0) {
                                            while ($c = $currencies->FetchRow()) {
                                                    ?>
                                                    <option value="<?php echo $c['currency_code']; ?>"><?php echo $c['country_name'] . ' - ' . $c['currency_code']; ?></option>
                                                    <?php
                                            }
                                    }
                                    ?>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th align="right">Interval:</th>
                            <th align="left">
                                <select name="interval" id="interval">
                                    <option value="m">Monthly</option>
                                    <option value="q">Quarterly</option>
                                    <option value="a">Annual</option>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th align="right">Trial Period Days:</th>
                            <th align="left">
                                <input type="text" name="trial_period_days" id="trial_period_days"  value="0"/>
                            </th>
                        </tr>
						<tr>
                            <th align="right">Image Upload Limit:</th>
                            <th align="left">
                                <input type="text" name="image_upload_limit" id="image_upload_limit"  value="0"/>
                            </th>
                        </tr>
                        <tr>
                            <th align="right">Video Upload Limit:</th>
                            <th align="left">
                                <input type="text" name="video_upload_limit" id="video_upload_limit"  value="0"/>
                            </th>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td align="left">
                                <input type="submit" name="btnAddpac" id="btnAddpac" value="Save" />                       
                                <input type="submit" name="canpac" value="Cancel" />                        
                            </td>
                        </tr>

                    </table>
                </form>
                <!--end of content--></div>
            <!--end of contentContainer--></div>
        <!--end of Container--></div>


    <?php
    $_SESSION['msg'] = "";
    ?>
    <script type="text/javascript">

            jQuery("#btnAddpac").click(function () {
                var numericReg = /[0-9]/;
                var floatReg = /^[-+]?[0-9]*\.?[0-9]+$/;
                alert_msg = "";
                var flag = "true";

                var price = parseFloat(jQuery("#price").val());
                var no_of_loca = parseInt(jQuery("#no_of_loca").val());
                var no_of_active_camp_per_loca = parseInt(jQuery("#no_of_active_camp_per_loca").val());
                var total_no_of_camp_per_month = parseInt(jQuery("#total_no_of_camp_per_month").val());
                var min_share_point = parseInt(jQuery("#min_share_point").val());
                var transcation_fees = parseInt(jQuery("#transaction_fees").val());
                var min_reward_point = parseInt(jQuery("#min_reward_point").val());



                if (jQuery("#pac_name").val() == "")
                {
                    alert_msg += "* Package Name Can't Be Blank.\n";
                    flag = "false";

                }
                if (!floatReg.test(price))
                {
                    alert_msg += "* Please Enter Valid Price.\n";
                    flag = "false";
                }
                if (!numericReg.test(no_of_loca) || no_of_loca == 0)
                {
                    alert_msg += "* Please Enter No Of Location Greater Than Zero.\n";
                    flag = "false";

                }
                if (!numericReg.test(no_of_active_camp_per_loca) || no_of_active_camp_per_loca == 0)
                {
                    alert_msg += "* Please Enter No Of Active Campaign Per Location Greater Than Zero.\n";
                    flag = "false";
                }
                if (!numericReg.test(total_no_of_camp_per_month) || total_no_of_camp_per_month == 0)
                {
                    alert_msg += "* Please Enter Total No Of Campaign Per Month Greater Than Zero.\n";
                    flag = "false";
                }
                if (!numericReg.test(min_share_point))
                {
                    alert_msg += "* Please Enter Valid Minimum Share Point.\n";
                    flag = "false";
                }
                if (!numericReg.test(transcation_fees))
                {
                    alert_msg += "* Please Enter Valid fees number.\n";
                    flag = "false";
                }
                if (!numericReg.test(min_reward_point))
                {
                    alert_msg += "* Please Enter Valid Minimum Reward Point.\n";
                    flag = "false";
                }

                if (flag == "true")
                {
                    return true;
                }
                else
                {
                    alert(alert_msg);
                    return false;
                }
            });

    </script>
</body>
</html>
