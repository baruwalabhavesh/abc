<?php
check_admin_session();
require_once(LIBRARY . "/class.phpmailer.php");

if (isset($_REQUEST['action'])) {

        if ($_REQUEST['action'] == "Active") {
                $array_values = $where_clause = array();
                $array_values['approve'] = 1;
                $where_clause['id'] = $_REQUEST['id'];
                $objDB->Update($array_values, "merchant_user", $where_clause);
                header("Location: " . WEB_PATH . "/admin/merchant_detail.php?id=" . $_REQUEST['id']);
                exit();
        }
        if ($_REQUEST['action'] == "Pending") {
                $array_values = $where_clause = array();
                $array_values['approve'] = 2;
                $where_clause['id'] = $_REQUEST['id'];
                $objDB->Update($array_values, "merchant_user", $where_clause);
                header("Location: " . WEB_PATH . "/admin/merchant_detail.php?id=" . $_REQUEST['id']);
                exit();
        }
        if ($_REQUEST['action'] == "Block") {
                $array_values = $where_clause = array();
                $array_values['approve'] = 0;
                $where_clause['id'] = $_REQUEST['id'];
                $objDB->Update($array_values, "merchant_user", $where_clause);
                header("Location: " . WEB_PATH . "/admin/merchant_detail.php?id=" . $_REQUEST['id']);
                exit();
        }
}

$where_clause['id'] = $_REQUEST['id'];
$RS = $objDB->Show("merchant_user", $where_clause);

$merchant_gift_card_plans = $objDB->Conn->Execute('Select * from merchant_gift_card_plans where merchant_id = ' . $_REQUEST['id']);
$mplan = array();
if ($merchant_gift_card_plans->RecordCount() > 0) {
        $mplan = $merchant_gift_card_plans->FetchRow();
}

if (isset($_REQUEST['gift_card_plan_choosen'])) {

        $gfcard['merchant_id'] = $_REQUEST['id'];
        $gfcard['gift_card_plan_id'] = $_REQUEST['gf_plan'];
        $gfcard['created'] = date('Y-m-d H:i:s');

        if (empty($mplan)) {
                $objDBWrt->Insert($gfcard, "merchant_gift_card_plans");
        } else {
                $whr_arr['id'] = $mplan['id'];
                $objDBWrt->Update($gfcard, "merchant_gift_card_plans", $whr_arr);
        }
}

if (isset($_REQUEST['btnsaveac'])) {
        $merchant_user_arr = array();

        $merchant_user_arr['id'] = $_REQUEST['id'];
        $old_merchant_data = $objDB->Show("merchant_user", $merchant_user_arr);

        $pack_data_mu['id'] = $_REQUEST['pack_name'];
        $get_billing_pack_data_mu = $objDB->Show("billing_packages", $pack_data_mu);

        $pack_data['merchant_id'] = $_REQUEST['id'];
        $get_pack_data = $objDB->Show("merchant_billing", $pack_data);

        if ($get_pack_data->RecordCount() != 0) {

                $pack_data_mu_old['id'] = $get_pack_data->fields['pack_id'];
                $get_billing_pack_data_mu_old = $objDB->Show("billing_packages", $pack_data_mu_old);

                $old_campaigns = $get_billing_pack_data_mu_old->fields['total_no_of_camp_per_month'];
                $old_campaign_left = $old_merchant_data->fields['total_no_of_campaign'];
                $new_campaigns = $get_billing_pack_data_mu->fields['total_no_of_camp_per_month'];
                $new_campaign_left = $get_billing_pack_data_mu->fields['total_no_of_camp_per_month'];

                if ($new_campaigns > $old_campaigns) {
                        $campaign_left = $new_campaign_left - ( $old_campaigns - $old_campaign_left);
                } else {
                        if ($new_campaign_left > $old_campaigns) {
                                $campaign_left = $new_campaign_left - ( $old_campaigns - $old_campaign_left);
                        } else {
                                if ($new_campaign_left <= $old_campaign_left) {
                                        $campaign_left = $new_campaign_left;
                                } else {
                                        $campaign_left = $new_campaign_left;
                                }
                        }
                }
                /*                 * ********************************************** */
                $where_clause_pckg['merchant_id'] = $_REQUEST['id'];
                $array_values1['pack_id'] = $_REQUEST['pack_name'];
                $objDB->Update($array_values1, "merchant_billing", $where_clause_pckg);

                $where_clause_mu = $array_mu = array();
                $array_mu['total_no_of_campaign'] = $campaign_left;
                $where_clause_mu['id'] = $_REQUEST['id'];

                $objDB->Update($array_mu, "merchant_user", $where_clause_mu);
        } else {

                $array_values1 = array();
                $array_values1['merchant_id'] = $_REQUEST['id'];
                $array_values1['pack_id'] = $_REQUEST['pack_name'];
                $objDB->Insert($array_values1, "merchant_billing");


                $ot = $get_billing_pack_data_mu->fields['total_no_of_camp_per_month'];
                $where_clause_mu = $array_mu = array();
                $array_mu['total_no_of_campaign'] = $ot;
                $where_clause_mu['id'] = $_REQUEST['id'];

                $objDB->Update($array_mu, "merchant_user", $where_clause_mu);
        }
}

if (isset($_REQUEST['btnsaveac_setting'])) {
        $where_clause_pckg = array();
        $where_clause_pckg['id'] = $_REQUEST['id'];
        $array_values1['location_detail_title'] = $_REQUEST['location_detail_title'];
        $array_values1['location_detail_display'] = $_REQUEST['display_location_detail_title'];
        $array_values1['menu_price_title'] = $_REQUEST['menu_price_title'];
        $array_values1['menu_price_display'] = $_REQUEST['display_menu_price_title'];
        $objDB->Update($array_values1, "merchant_user", $where_clause_pckg);
}

if (isset($_REQUEST['btnsave_online_campaign_setting'])) {

        $where_clause = array();
        $array_values = array();
        $where_clause['id'] = $_REQUEST['id'];

        if (isset($_REQUEST['enable_online_campaign'])) {

                if ($_REQUEST['enable_online_campaign'] == 1) {

                        $array_values['enable_online_campaign'] = $_REQUEST['enable_online_campaign'];
                        $array_values['apikey'] = $_REQUEST['apikey'];
                        $array_values['visible_api_key_in_profile'] = $_REQUEST['visible_api_key_in_profile'];
                } else {

                        $array_values['enable_online_campaign'] = $_REQUEST['enable_online_campaign'];
                }

                $objDB->Update($array_values, "merchant_user", $where_clause);
        }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Admin Panel</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="<?= ASSETS_CSS ?>/a/template.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/a/jquery-1.7.2.min.js"></script>
        <style>

            #content div.mer_chant div.mer_chant_4 {
                background: none repeat scroll 0 0 #dddddd;
                border-bottom: 2px solid #ff9900;
                font-size: 18px;
            }

            #content div.mer_chant div.mer_chant_4 p span {
                font-weight: bold;
            }
            .mer_chant_4 table tr {
                display: inline-block;
                width: 100%;
            }
            .mer_chant_4 table tr:last-child td {
                width: 165px;
            }
            .mer_chant_4 table tr td:first-child {
                width: 170px;
            }
        </style>
        <!-- Message box popup -->

        <link rel="stylesheet" type="text/css" href="<?= ASSETS_CSS ?>/a/fancybox/jquery.fancybox-buttons.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?= ASSETS_CSS ?>/a/fancybox/jquery.fancybox.css" media="screen" />

<!--<script type="text/javascript" src="<?= ASSETS_JS ?>/a/fancybox/jquery.fancybox-buttons.js"></script>-->
        <script type="text/javascript" src="<?= ASSETS_JS ?>/a/fancybox/jquery.fancybox.js"></script>

        <!-- End Message box popup -->

    </head>

    <body>
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
                    <div style="display: block; overflow: hidden;">
                        <div style="float: left;"><h2>Merchants</h2></div>
                        <div style="float: right; clear: none">
                            <a href="<?= WEB_PATH ?>/admin/merchants.php">Back to list</a></div>
                        <div style="float: right; clear: both">
                            <?php
                            $where_clause['id'] = $_REQUEST['id'];
                            $RS_cl = $objDB->Show("merchant_user", $where_clause);
                            if ($RS_cl->RecordCount() > 0) {
                                    while ($Row_cl = $RS_cl->FetchRow()) {
                                            ?>
                                            <?php if ($Row_cl['approve'] == 0) { ?>
                                                                                                                                                <!--                            <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Block">Block</a> /-->
                                                    <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Pending">Pending</a> /
                                                    <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Active">Active</a> 

                                            <?php } elseif ($Row_cl['approve'] == 2) { ?>
                                                    <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Block">Block</a> /
                        <!--                            <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Pending">Pending</a> /-->
                                                    <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Active">Active</a> 

                                            <?php } elseif ($Row_cl['approve'] == 1) { ?>
                                                    <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Block">Block</a> /
                                                    <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Pending">Pending</a>
                        <!--                            <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Active">Active</a> -->

                                            <?php } else { ?>
                                                    <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Block">Block</a> /
                                                    <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Pending">Pending</a> /
                                                    <a href="<?= WEB_PATH ?>/admin/merchant_detail.php?id=<?= $_REQUEST['id'] ?>&action=Active">Active</a> 
                                            <?php } ?>
                                        </div>   
                                        <?php
                                }
                        }
                        ?>     
                    </div>
                    <div><?php
                        echo $_SESSION['msg'];
                        $_SESSION['msg'] = "";
                        ?></div>
                    <div class="mer_chant">
                        <?php
                        //$billing_packages = $objDB->Show("billing_packages");

                        $pack_data1 = $json_array = array();

                        $pack_data = $json_array = array();
                        $pack_data['merchant_id'] = $_REQUEST['id'];
                        $get_pack_data = $objDB->Show("merchant_billing", $pack_data);

                        $pack_data1['id'] = $get_pack_data->fields['pack_id'];
                        $get_billing_pack_data = $objDB->Show("billing_packages", $pack_data1);

						

                        if ($RS->RecordCount() > 0) {
                                while ($Row = $RS->FetchRow()) {
									$cntry = array();	
									$cntry['id'] = $Row['country'];
									$RS_cntry = $objDB->Show("country", $cntry);
									//echo "select * from billing_packages where currency= (select currency_code from currencies where country_name='" . $Row['country'] . "')";
									//echo "select * from billing_packages where currency= (select currency_code from currencies where country_name='" . $RS_cntry->fields['name'] . "')";
									//echo "select * from billing_packages where currency= (select currency_code from currencies where id_countries='" . $RS_cntry->fields['currency_id'] . "')";
									
                                        $billing_packages = $objDB->Conn->Execute("select * from billing_packages where currency= (select currency_code from currencies where id_countries='" . $RS_cntry->fields['currency_id'] . "')");
                                        ?>
                                        <div class="mer_chant_1">
                                            <p><span>Merchant name</span><span>&nbsp;:&nbsp;</span><?= $Row['firstname'] . " " . $Row['lastname'] ?></p>
                                            <p><span>Merchant Email</span><span>&nbsp;:&nbsp;</span><?= $Row['email'] ?></p>
                                            <p><span>Merchnat Address</span><span>&nbsp;:&nbsp;</span><?= $Row['address'] ?>, <?= $Row['city'] ?>, <?= $Row['state'] ?>, <?= $Row['zipcode'] ?>, <?= $Row['country'] ?></p>
                                            <p><span>Status</span><span>&nbsp;:&nbsp;</span><?php
                                                if ($Row['approve'] == 0) {
                                                        echo "Block";
                                                } else if ($Row['approve'] == 2) {
                                                        echo "Pending";
                                                } else if ($Row['approve'] == 1) {
                                                        echo "Active";
                                                }
                                                ?></p>
                                        </div>
                                        <form method="post">
                                            <?php
                                            if ($Row['merchant_parent'] == 0) {
                                                    ?>
                                                    <div class="mer_chant_2">
                                                        <p><span>Package</span><span>&nbsp;:&nbsp;</span>

                                                            <select id="pack_name" name="pack_name">
                                                                <?php while ($Row1 = $billing_packages->FetchRow()) { ?>
                                                                        <option value="<?php echo $Row1['id']; ?>" <?php if ($get_pack_data->fields['pack_id'] == $Row1['id']) echo "selected"; ?>><?php echo $Row1['pack_name']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <input type="submit" id="btnsaveac" name="btnsaveac" value="Save"/>
                                                        </p>
                                                        <?php
                                                        $where_clause['id'] = $_REQUEST['id'];
                                                        $RS_cl = $objDB->Show("merchant_user", $where_clause);
                                                        if ($RS_cl->RecordCount() > 0) {
                                                                while ($Row_cl = $RS_cl->FetchRow()) {
                                                                        $country = $Row_cl['country'];
                                                                        ?>
                                                                        <table style="font-size:14px !important">
                                                                            <tr>
                                                                                <th>Number of location: </th>
                                                                                <th align="left"><input type="text" name="no_of_loca" size="5" value="<?php echo $get_billing_pack_data->fields['no_of_loca']; ?>" disabled/></th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Number of active campaign per location: </th>
                                                                                <th align="left"><input type="text" name="no_of_active_camp_per_loca" size="5" value="<?php echo $get_billing_pack_data->fields['no_of_active_camp_per_loca']; ?>" disabled /></th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Total number of campaign per month: </th>
                                                                                <th align="left"><input type="text" name="total_no_of_camp_per_month" size="5" value="<?php echo $get_billing_pack_data->fields['total_no_of_camp_per_month']; ?>" disabled /></th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Minimum Share Point: </th>
                                                                                <th align="left"><input type="text" name="min_share_point" size="5" value="<?php echo $get_billing_pack_data->fields['min_share_point']; ?>" disabled /></th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Minimum Reward Point: </th>
                                                                                <th align="left"><input type="text" name="min_reward_point" size="5" value="<?php echo $get_billing_pack_data->fields['min_reward_point']; ?>"  disabled /></th>
                                                                            </tr>
                                                                        </table>
                                                                        <?php
                                                                }
                                                        }
                                                        ?>			 
                                                    </div>
                                                    <div class="mer_chant_6">
                                                        <?php
                                                        //echo 'Select * from gift_card_plans where is_deleted=0 And currency= (select currency_code from currencies where country_name= "' . $country . '")';
                                                        //echo 'Select * from gift_card_plans where is_deleted=0 And currency= (select currency_code from currencies where id_countries= "' . $RS_cntry->fields['currency_id'] . '")';
                                                        $gift_card_plans = $objDB->Conn->Execute('Select * from gift_card_plans where is_deleted=0 And currency= (select currency_code from currencies where id_countries= "' . $RS_cntry->fields['currency_id'] . '")');
                                                        ?>
                                                        <p><span>Gift Card Plans</span><span>&nbsp;:&nbsp;</span>
                                                            <?php
                                                            if ($gift_card_plans->recordCount() > 0) {
                                                                    ?>
                                                                    <select name="gf_plan" >
                                                                        <?php
                                                                        while ($row = $gift_card_plans->FetchRow()) {
                                                                                ?>
                                                                                <option value="<?php echo $row['id']; ?>" <?php echo ($mplan['gift_card_plan_id'] == $row['id']) ? 'selected' : ''; ?>><?php echo $row['min_value'] . ' ' . $row['currency'] . ' - ' . $row['max_value'] . ' ' . $row['currency']; ?></option>
                                                                                <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <input type="hidden" name="merchant_id" value="<?php echo $_REQUEST['id']; ?>" />
                                                                    <?php
                                                            }
                                                            ?>
                                                            <input type="submit" name="gift_card_plan_choosen" value="Submit"/>
                                                        </p>
                                                        <?php
                                                        ?>
                                                    </div>

                                                    <?php
                                                    $where_clause['id'] = $_REQUEST['id'];
                                                    $RS_cl = $objDB->Show("merchant_user", $where_clause);
                                                    if ($RS_cl->RecordCount() > 0) {
                                                            while ($Row_cl = $RS_cl->FetchRow()) {
                                                                    ?>
                                                                    <div class="mer_chant_4">
                                                                        <p><span>Online Campaign Setting</span><span>&nbsp;:&nbsp;</span>

                                                                        </p>
                                                                        <table style="font-size:14px !important">
                                                                            <?php
                                                                            if ($Row_cl['enable_online_campaign'] == 1) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td>Enable Online Campaign : </td>
                                                                                        <td align="left">
                                                                                            <input type="radio" name="enable_online_campaign" value="1" <?php
                                                                                            if ($Row_cl['enable_online_campaign'] == 1) {
                                                                                                    echo "checked";
                                                                                            }
                                                                                            ?> />Yes
                                                                                            <input type="radio" name="enable_online_campaign" value="0" <?php
                                                                                            if ($Row_cl['location_detail_display'] == 0) {
                                                                                                    echo "checked";
                                                                                            }
                                                                                            ?> />No
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr class="apikey_tr" >
                                                                                        <td>Api key : </td>
                                                                                        <td align="left">
                                                                                            <input type="text" id="apikey" name="apikey" value="<?php echo $Row_cl['apikey']; ?>" />
                                                                                            <input type="button" id="generate_api_key" name="generate_api_key" value="Generate Api Key"/>
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr class="apikey_tr" >
                                                                                        <td>Visible Api key in profile : </td>
                                                                                        <td align="left">
                                                                                            <input type="radio" name="visible_api_key_in_profile" value="1" <?php
                                                                                            if ($Row_cl['visible_api_key_in_profile'] == 1) {
                                                                                                    echo "checked";
                                                                                            }
                                                                                            ?> />Yes
                                                                                            <input type="radio" name="visible_api_key_in_profile" value="0" <?php
                                                                                            if ($Row_cl['visible_api_key_in_profile'] == 0) {
                                                                                                    echo "checked";
                                                                                            }
                                                                                            ?> />No
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                            } else {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td>Enable Online Campaign : </td>
                                                                                        <td align="left">
                                                                                            <input type="radio" name="enable_online_campaign" value="1" <?php
                                                                                            if ($Row_cl['enable_online_campaign'] == 1) {
                                                                                                    echo "checked";
                                                                                            }
                                                                                            ?> />Yes
                                                                                            <input type="radio" name="enable_online_campaign" value="0" <?php
                                                                                            if ($Row_cl['enable_online_campaign'] == 0) {
                                                                                                    echo "checked";
                                                                                            }
                                                                                            ?> />No
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr class="apikey_tr" style="display:none;">
                                                                                        <td>Api key : </td>
                                                                                        <td align="left">
                                                                                            <input type="text" id="apikey" name="apikey" value="<?php echo $Row_cl['apikey']; ?>" />
                                                                                            <input type="button" id="generate_api_key" name="generate_api_key" value="Generate Api Key"/>
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr class="apikey_tr" style="display:none;">
                                                                                        <td>Visible Api key in profile : </td>
                                                                                        <td align="left">
                                                                                            <input type="radio" name="visible_api_key_in_profile" value="1" <?php
                                                                                            if ($Row_cl['visible_api_key_in_profile'] == 1) {
                                                                                                    echo "checked";
                                                                                            }
                                                                                            ?> />Yes
                                                                                            <input type="radio" name="visible_api_key_in_profile" value="0" <?php
                                                                                            if ($Row_cl['visible_api_key_in_profile'] == 0) {
                                                                                                    echo "checked";
                                                                                            }
                                                                                            ?> />No
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                            }
                                                                            ?>
                                                                            <tr >
                                                                                <td>&nbsp;</td>
                                                                                <td align="left"> 
                                                                                    <input type="submit" id="btnsave_online_campaign_setting" name="btnsave_online_campaign_setting" value="Save"/>
                                                                                </td>
                                                                            </tr>
                                                                        </table>

                                                                    </div>
                                                                    <div class="mer_chant_2">

                                                                        <a href="add-location-detail.php?id=<?php echo $_REQUEST['id']; ?>">
                                                                            <?php echo $merchant_msg['locations']['add_location_detail']; ?>
                                                                        </a>
                                                                        &nbsp;&nbsp;
                                                                        <!--
                                                                        <a class="connectlocu" href="javascript:void(0);">
                                                                            Add Menu/Price List
                                                                        </a>
																		-->
                                                                    </div>
                                                                    <div class="mer_chant_2">
                                                                        <p><span>Display Setting</span><span>&nbsp;:&nbsp;</span>

                                                                        </p>
                                                                        <table style="font-size:14px !important">
                                                                            <tr>
                                                                                <th>Location Detail Title : </th>
                                                                                <th align="left"><input type="text" name="location_detail_title" value="<?php echo $Row_cl['location_detail_title']; ?>" /></th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Display Location Detail : </th>
                                                                                <th align="left">
                                                                                    <input type="radio" name="display_location_detail_title" value="1" <?php
                                                                                    if ($Row_cl['location_detail_display'] == 1) {
                                                                                            echo "checked";
                                                                                    }
                                                                                    ?> />Yes
                                                                                    <input type="radio" name="display_location_detail_title" value="0" <?php
                                                                                    if ($Row_cl['location_detail_display'] == 0) {
                                                                                            echo "checked";
                                                                                    }
                                                                                    ?> />No
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Menu/Price List Title : </th>
                                                                                <th align="left"><input type="text" name="menu_price_title" value="<?php echo $Row_cl['menu_price_title']; ?>" /></th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Display Menu/Price List : </th>
                                                                                <th align="left">
                                                                                    <input type="radio" id="" name="display_menu_price_title" value="1" <?php
                                                                                    if ($Row_cl['menu_price_display'] == 1) {
                                                                                            echo "checked";
                                                                                    }
                                                                                    ?> />Yes
                                                                                    <input type="radio" id="" name="display_menu_price_title" value="0" <?php
                                                                                    if ($Row_cl['menu_price_display'] == 0) {
                                                                                            echo "checked";
                                                                                    }
                                                                                    ?> />No
                                                                                </th>
                                                                            </tr>
                                                                            <tr >
                                                                                <th>&nbsp;</th>
                                                                                <th align="left"> <input type="submit" id="btnsaveac_setting" name="btnsaveac_setting" value="Save"/></th>
                                                                            </tr>
                                                                        </table>

                                                                    </div>

                                                                    <?php
                                                            }
                                                    }
                                            }
                                            ?>

                                            <?php
                                    }
                            }
                            ?>

                        </form>
                        <div class="mer_chant_2">
                            <?php
                            $arr = file(WEB_PATH . '/admin/process.php?btnGetAllactiveCampaignOfMerchant=yes&mer_id=' . $_REQUEST['id']);
                            if (trim($arr[0]) == "") {
                                    unset($arr[0]);
                                    $arr = array_values($arr);
                            }
                            $json = json_decode($arr[0]);
                            $total_records = $json->total_records;
                            $records_array = $json->records;
                            ?>
                            <p><span>Active Campaigns</span><span>&nbsp;:&nbsp;</span><?php echo $total_records ?></p>
                            <?php
                            if ($total_records > 0) {
                                    foreach ($records_array as $Row) {
                                            echo "<p><a href='" . WEB_PATH . "/admin/campaign-detail.php?id=" . $Row->id . "'>" . $Row->title . "</a></p>";
                                    }
                            }
                            ?>
                        </div>
                        <div class="mer_chant_3">
                            <?php
                            $array_values = $where_clause_loc = array();
                            $where_clause_loc['created_by'] = $_REQUEST['id'];
                            $where_clause_loc['active'] = "1";
                            $RS_loc = $objDB->Show("locations", $where_clause_loc);
                            ?>
                            <p><span>Active Locations</span><span>&nbsp;:&nbsp;</span><?php echo $RS_loc->RecordCount() ?></p>
                            <?php
                            if ($RS_loc->RecordCount() > 0) {
                                    while ($Row_loc = $RS_loc->FetchRow()) {
                                            //echo "<p>".$Row_loc['location_name']."</p>";
                                            echo "<p><a href='location-detail.php?id=" . $Row_loc['id'] . "'>" . $Row_loc['location_name'] . "</a></p>";
                                            ?>

                                            <?php
                                    }
                            }
                            ?>
                        </div>
                    </div>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <!--end of Container--></div>

        <div id="sharingPopUpContainer" class="container_popup"  style="display: none;">
            <div id="sharingBackDiv" class="divBack"></div>
            <div id="sharingFrontDivProcessing" class="Processing" style="display:none;">                                            
                <div id="sharingMaindivLoading" align="center" valign="middle" class="imgDivLoading" style="left:32%;top: 11%;">

                    <div id="sharingmainContainer" class="" style="background:none; left: 45%;position: fixed;top: 50%;width: auto;height:23px">
                        <div class="main_content">
                            <div class="message-box message-success" id="jqReviewHelpfulMessagesharing" style="display: block;height:30px;">
                                <div class="campaign_detail_div" style="">
                                    <img src="<?php echo ASSETS_IMG; ?>/c/<?php echo $merchant_msg["common"]["loader_image_name"]; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="dialog-message" title="Message Box" style="display:none">

        </div>
        <input type="hidden" name="venue_id" id="venue_id" value="" />
        <input type="hidden" name="hdn_location_id" id="hdn_location_id" value="" />
    </body>
</html>

<script type="text/javascript">
        jQuery(".connectlocu").live("click", function () {
            open_popup('sharing');
            var customer_id = "<?php echo $_REQUEST['id']; ?>";



            jQuery.ajax({
                type: "POST",
                url: '<?php echo WEB_PATH; ?>/merchant/locu_share_merchant.php',
                data: 'customer_id=' + customer_id,
                // async:false,
                success: function (msg)
                {

                    close_popup('sharing');
                    var data_arr = jQuery.trim(msg).split("###");
                    if (data_arr[0] == "success")
                    {

                        //var msg="We have detect locu Venue ID : <span style='font-weight:bold'>"+data_arr[1]+ "</span>, for your <span style='font-weight:bold'>"+ data_arr[3] +","+data_arr[4] +","+data_arr[5] +","+data_arr[6] +","+ data_arr[7]+"-"+data_arr[8] +"</span>. Please select other locations below if you would like us to use the same venue ID for publishing your menu.\n";
                        if (data_arr[11] == "notgetlocation")
                        {
                            var msg = "<?php echo $merchant_msg['locations']['Msg_detected_menu_price_list1']; ?><span style='font-weight:bold'>" + data_arr[10] + "</span>";
                        }
                        else
                        {
                            var msg = "<?php echo $merchant_msg['locations']['Msg_detected_menu_price_list1']; ?><span style='font-weight:bold'>" + data_arr[10] + "</span><?php echo $merchant_msg['locations']['Msg_detected_menu_price_list2']; ?>";
                        }
                        var location_detail = data_arr[9];
                        jQuery("#hdn_location_id").val(data_arr[2]);
                        jQuery("#venue_id").val(data_arr[1]);
                        var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>";
                        if (data_arr[11] == "notgetlocation")
                        {
                            var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;width:815px;'>" + msg + location_detail + "</div>";
                        }
                        else
                        {
                            var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;height:280px;overflow-y:auto;width:815px;'>" + msg + location_detail + "</div>";
                        }
                        var footer_msg = "<div style='text-align:center'><hr><input type='button'  value='Save & continue' id='popupsave' name='popupsave' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;' /><input type='button'  value='Cancel' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                        jQuery.fancybox({
                            content: jQuery('#dialog-message').html(),
                            type: 'html',
                            openSpeed: 300,
                            width: 800,
                            closeSpeed: 300,
                            // topRatio: 0,
                            //modal : 'true',
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
                    }
                    else
                    {
                        if (data_arr[1] == "allclick")
                        {
                            var msg = "<?php echo $merchant_msg['locations']['Msg_all_menu_price_get_venue_id']; ?>";
                            var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                            var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
                            var footer_msg = "<div style='text-align:center'><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                            jQuery.fancybox({
                                content: jQuery('#dialog-message').html(),
                                type: 'html',
                                openSpeed: 300,
                                closeSpeed: 300,
                                // topRatio: 0,
                                //modal : 'true',
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
                        }
                        else
                        {


                            var location_detail = data_arr[1];
                            var msg = "<?php echo $merchant_msg['locations']['Msg_no_find_any_location1']; ?>";
                            var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>";
                            var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;height:280px;overflow-y:auto;width:815px;'>" + msg + location_detail + "</div>";
                            var footer_msg = "<div style='text-align:left;padding:5px;margin-top:5px;'>Please <a href='https://locu.com/'>click here</a><?php echo $merchant_msg['locations']['Msg_no_find_any_location2']; ?></div><div style='text-align:center'><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                            jQuery.fancybox({
                                content: jQuery('#dialog-message').html(),
                                type: 'html',
                                openSpeed: 300,
                                closeSpeed: 300,
                                // topRatio: 0,
                                //modal : 'true',
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
                        }
                    }
                }
            });
        });

        jQuery("#popupcancel").live("click", function () {
            jQuery.fancybox.close();

        });
        jQuery("#popupsave").live("click", function () {

            var locationcount = jQuery('input:checkbox[name="location_venue"]:checked').size();
            if (locationcount == 0)
            {
                jQuery(".fancybox-inner #error_msg_checkbox").show();
                return false;
            }
            else
            {

                jQuery.fancybox.close();
                open_popup('sharing');
                var customer_id = "<?php echo $_REQUEST['id']; ?>";
                var location_id = [];

                jQuery("input[type=checkbox]:checked").each(function () {

                    location_id.push(jQuery(this).val());
                });

                location_id.push(jQuery("#hdn_location_id").val());
                var venue_id = jQuery("#venue_id").val();
                jQuery.ajax({
                    type: "POST",
                    //url:'<?php echo WEB_PATH; ?>/merchant/locu_venue_save.php',
                    url: '<?php echo WEB_PATH; ?>/merchant/locu_share_merchant.php',
                    data: 'location_id=' + location_id + '&customer_id=' + customer_id + '&venue_id=' + venue_id + '&savecontinue=yes',
                    // async:false,
                    success: function (msg)
                    {

                        close_popup('sharing');
                        var data_arr = jQuery.trim(msg).split("###");
                        if (data_arr[0] == "success")
                        {

                            //var msg="We have detect locu Venue ID : <span style='font-weight:bold'>"+data_arr[1]+ "</span>, for your <span style='font-weight:bold'>"+ data_arr[3] +","+data_arr[4] +","+data_arr[5] +","+data_arr[6] +","+ data_arr[7]+"-"+data_arr[8] +"</span>. Please select other locations below if you would like us to use the same venue ID for publishing your menu.\n";
                            if (data_arr[11] == "notgetlocation")
                            {
                                var msg = "<?php echo $merchant_msg['locations']['Msg_detected_menu_price_list1']; ?><span style='font-weight:bold'>" + data_arr[10] + "</span>";
                            }
                            else
                            {
                                var msg = "<?php echo $merchant_msg['locations']['Msg_detected_menu_price_list1']; ?><span style='font-weight:bold'>" + data_arr[10] + "</span><?php echo $merchant_msg['locations']['Msg_detected_menu_price_list2']; ?>";
                            }
                            var location_detail = data_arr[9];
                            jQuery("#hdn_location_id").val(data_arr[2]);
                            jQuery("#venue_id").val(data_arr[1]);
                            var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>";
                            if (data_arr[11] == "notgetlocation")
                            {
                                var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;width:815px;'>" + msg + location_detail + "</div>";
                            }
                            else
                            {
                                var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;height:280px;overflow-y:auto;width:815px;'>" + msg + location_detail + "</div>";
                            }
                            var footer_msg = "<div style='text-align:center'><hr><input type='button'  value='Save & continue' id='popupsave' name='popupsave' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;' /><input type='button'  value='Cancel' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                            jQuery.fancybox({
                                content: jQuery('#dialog-message').html(),
                                type: 'html',
                                openSpeed: 300,
                                width: 800,
                                closeSpeed: 300,
                                // topRatio: 0,
                                //modal : 'true',
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
                        }
                        else
                        {
                            if (data_arr[1] == "allclick")
                            {
                                var msg = "<?php echo $merchant_msg['locations']['Msg_all_menu_price_get_venue_id']; ?>";
                                var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
                                var footer_msg = "<div style='text-align:center'><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'></div>";
                                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                                jQuery.fancybox({
                                    content: jQuery('#dialog-message').html(),
                                    type: 'html',
                                    openSpeed: 300,
                                    closeSpeed: 300,
                                    // topRatio: 0,
                                    //modal : 'true',
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
                            }
                            else
                            {
                                var location_detail = data_arr[1];
                                var msg = "<?php echo $merchant_msg['locations']['Msg_no_find_any_location1']; ?>";
                                var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>";
                                var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;height:280px;overflow-y:auto;width:815px;'>" + msg + location_detail + "</div>";
                                var footer_msg = "<div style='text-align:left;padding:5px;margin-top:5px;'>Please <a href='https://locu.com/'>click here</a><?php echo $merchant_msg['locations']['Msg_no_find_any_location2']; ?></div><div style='text-align:center'><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;margin-right:10px;'></div>";
                                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                                jQuery.fancybox({
                                    content: jQuery('#dialog-message').html(),
                                    type: 'html',
                                    openSpeed: 300,
                                    closeSpeed: 300,
                                    // topRatio: 0,
                                    //modal : 'true',
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
                            }
                        }
                    }
                });
            }
        });

        function close_popup(popup_name)
        {

            $("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
                $("#" + popup_name + "BackDiv").fadeOut(200, function () {
                    $("#" + popup_name + "PopUpContainer").fadeOut(100, function () {
                        $("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
                        $("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
                        $("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
                    });
                });
            });

        }
        function open_popup(popup_name)
        {
            $("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
                $("#" + popup_name + "BackDiv").fadeIn(200, function () {
                    $("#" + popup_name + "PopUpContainer").fadeIn(200, function () {

                    });
                });
            });

        }

        jQuery("input[name='enable_online_campaign']:radio").change(function () {
            //alert(jQuery(this).attr("value"));
            if (jQuery(this).attr("value") == "1")
            {
                jQuery(".apikey_tr").css("display", "block");
            }
            else
            {
                jQuery(".apikey_tr").css("display", "none");
            }
        });

        jQuery("#generate_api_key").click(function () {
            var rString = randomString(6, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
            jQuery("#apikey").val(rString);
        });

        function randomString(length, chars)
        {
            var result = '';
            for (var i = length; i > 0; --i)
                result += chars[Math.round(Math.random() * (chars.length - 1))];
            return result;
        }
</script>
