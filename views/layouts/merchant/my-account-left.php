<?php
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$active_locations = 1;
//if($_SESSION['merchant_id'] != ""){
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {

        // 14 10 2013
        $arr1 = file(WEB_PATH . '/merchant/process.php?is_any_active_location=yes&merchant_id=' . $_SESSION['merchant_id']);
        if (trim($arr1[0]) == "") {
                unset($arr1[0]);
                $arr1 = array_values($arr1);
        }
        $json1 = json_decode($arr1[0]);
        $active_locations = $json1->active_locations;
        // 14 10 2013


        $Sql = "SELECT * from merchant_user_role where merchant_user_id =" . $_SESSION['merchant_id'];
        $RS_role = $objDB->Conn->Execute($Sql);

        while ($Row_role = $RS_role->FetchRow()) {
                $ass_page = unserialize($Row_role['ass_page']);
                $ass_role = unserialize($Row_role['ass_role']);
        }
}
$is_assigned = 1;
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
        $arr = file(WEB_PATH . '/merchant/process.php?is_package_assigned_to_merchant=yes&merchant_id=' . $_SESSION['merchant_id']);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
        $is_assigned = $json->is_assigned;
}
?>


<p class="dashboard-icon">
    <a id="myprofile" href="javascript:void(0);" link="my-profile.php"><img src="<?= ASSETS_IMG ?>/m/myprofile.png" /></a>
    <a class="dashboard-content" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_my_profile']; ?></a>
</p>
<?php
/* * *************  check for media access link media access permission for employee -> if employee can't add or view image -> do not show media management on dashboard******* */
if ($_SESSION['merchant_info']['merchant_parent'] != 0) {
        /*         * **** get employee role ( which role is assigned to user ) check upload,view ,delete uplaod image permission of employee ************** */
        $media_acc_array = array();
        $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
        ;
        $RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
        $media_val = unserialize($RSmedia->fields['media_access']);


        if (in_array("delete", $media_val)) {
                $flag_delete = true;
        } else {
                $flag_delete = false;
        }
        if (in_array("view-use", $media_val)) {
                $flag_view = true;
        } else {
                $flag_view = false;
        }
        if (in_array("upload", $media_val)) {
                $flag_upload = true;
        } else {
                $flag_upload = false;
        }
} else {
        $flag_delete = true;
        $flag_view = true;
        $flag_upload = true;
}
/* * ****** ****** */
if (!$flag_view && !$flag_upload) {
        
} else {
        ?>
        <p class="dashboard-icon"> 
            <a href="javascript:void(0);" link="media-management.php"><img src="<?= ASSETS_IMG ?>/m/media_management.png" /></a>
            <a class="dashboard-content" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_media_management']; ?></a>
        </p>
        <?php
}
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
        ?>
        <p class="dashboard-icon">
            <a id="locations" href="javascript:void(0);" link="locations.php"  assigned="<?php echo $is_assigned; ?>"><img src="<?= ASSETS_IMG ?>/m/manage_locations.png" /></a>
            <a class="dashboard-content" href="javascript:void(0);" assigned="<?php echo $is_assigned; ?>"><?php echo $merchant_msg['dashboard']['Field_manage_location']; ?></a>
        </p>   
        <?php
}
?>
<?php
if ($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-user.php", $ass_page)) {
        ?>
        <p class="dashboard-icon">
            <a href="javascript:void(0);" link="manage-users.php" active_locations="<?php echo $active_locations; ?>" assigned="<?php echo $is_assigned; ?>"><img src="<?= ASSETS_IMG ?>/m/manage_users.png" /></a>
            <a class="dashboard-content" href="javascript:void(0);" active_locations="<?php echo $active_locations; ?>" assigned="<?php echo $is_assigned; ?>"><?php echo $merchant_msg['dashboard']['Field_manage_employee']; ?></a>
        </p>
        <?php
}
?>
<?php
if ($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-group.php", $ass_page)) {
        ?>
        <p class="dashboard-icon">
            <a href="javascript:void(0);" link="manage-customers.php" active_locations="<?php echo $active_locations; ?>" assigned="<?php echo $is_assigned; ?>"><img src="<?= ASSETS_IMG ?>/m/manage_groups.png" /></a>
            <a class="dashboard-content" href="javascript:void(0);" active_locations="<?php echo $active_locations; ?>" assigned="<?php echo $is_assigned; ?>"><?php echo $merchant_msg['dashboard']['Field_manage_distlist']; ?></a>
        </p>
        <?php
}
?>
<?php
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
        ?>
        <p class="dashboard-icon">
            <a href="javascript:void(0);" link="purchase-points.php?action=active" id="purchase-points"><img src="<?= ASSETS_IMG ?>/m/purchase_points.png" /></a>
            <a class="dashboard-content" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_manage_points']; ?></a>
        </p>
        <?php
}
?>
<?php
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
        ?>
        <p class="dashboard-icon">
            <a id="pricelist" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);" link="pricelists.php"><img src="<?= ASSETS_IMG ?>/m/pricelist.png" /></a>
            <a class="dashboard-content" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_pricelist']; ?></a>
        </p>			
        <?php
}

?>
<?php
/*
  if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("templates.php",$ass_page))
  {

 */
?>
<p class="dashboard-icon">
    <a id="templates" href="javascript:void(0);" link="templates.php" ><img src="<?= ASSETS_IMG ?>/m/manage_campaign_templates.png" /></a>
    <a class="dashboard-content" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_manage_template']; ?></a>
</p>
<?php
//}
?>

<?php
/*
  if($_SESSION['merchant_info']['merchant_parent']==0 || in_array("compaigns.php", $ass_page) )
  {

 */
?>
<p class="dashboard-icon">
    <a active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);" link="compaigns.php?action=active"  assigned="<?php echo $is_assigned; ?>"><img src="<?= ASSETS_IMG ?>/m/campaign.png" /></a>
    <a class="dashboard-content" href="javascript:void(0);" active_locations="<?php echo $active_locations; ?>" assigned="<?php echo $is_assigned; ?>"><?php echo $merchant_msg['dashboard']['Field_manage_campaign']; ?></a>
</p>
<?php
//}
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
        ?>
        <p class="dashboard-icon">
            <a id="pricelist" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);" link="manage_cards.php"><img src="<?= ASSETS_IMG ?>/m/loyalty.png" /></a>
            <a class="dashboard-content" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_loyalty']; ?></a>
        </p>			
        <?php
}
?>
<?php
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
        ?>
        <p class="dashboard-icon">
            <a id="myprofile" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);" link="manage-marketing-material.php"><img src="<?= ASSETS_IMG ?>/m/manage-marketing-material.png" /></a>
            <a class="dashboard-content" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_marketing_material']; ?></a>
        </p>
        <?php
}
?>
<?php
if ($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("redeem-deal.php", $ass_page)) {
        ?>
        <p class="dashboard-icon">
            <a id="redeem-coupons" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);" link="redeem-deal.php"><img src="<?= ASSETS_IMG ?>/m/redeem_coupon.png" /></a>
            <a class="dashboard-content" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_redeem_coupon']; ?></a>
        </p>			
        <?php
}
?>
<?php
if ($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("reports.php", $ass_page)) {
        ?>
        <p class="dashboard-icon">
            <a id="report" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);" link="reports.php"><img src="<?= ASSETS_IMG ?>/m/report.png" /></a>
            <a class="dashboard-content" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_report']; ?></a>
        </p>
        <?php
}
?>
<?php
if ($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("manage-reward-zone.php", $ass_page)) {
        ?>
        <p class="dashboard-icon">
            <a id="reward-zone" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);" link="manage-reward-zone.php"><img src="<?= ASSETS_IMG ?>/m/manage-reward-zone.png" /></a>
            <a class="dashboard-content" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_manage_reward']; ?></a>
        </p>
        <?php
}
?>
<?php
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
        ?>
        <p class="dashboard-icon" style="display: none;">
            <a id="redeem-coupons" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);" link="manage-social.php"><img src="<?= ASSETS_IMG ?>/m/manage_social.png" /></a>
            <a class="dashboard-content" active_locations="<?php echo $active_locations; ?>" href="javascript:void(0);"><?php echo $merchant_msg['dashboard']['Field_Social_Publishing']; ?></a>
        </p>			
        <?php
}
?>

<?php /* if($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("manage-groups.php",$ass_page)){  */ ?>
<!--    <p class="dashboard-icon">

<a href="<?= WEB_PATH ?>/merchant/manage-groups.php"><img src="<?= ASSETS_IMG ?>/m/manage_groups.png" /></a>
<a class="dashboard-content" href="<?= WEB_PATH ?>/merchant/manage-groups.php">Manage Groups</a>
</p> -->

<?php
// } else {
///if( in_array("manage-groups.php",$ass_page)) { 
?>
<!-- <p class="dashboard-icon">

<a href="<?= WEB_PATH ?>/merchant/manage-groups.php"><img src="<?= ASSETS_IMG ?>/m/manage_groups.png" /></a>
<a class="dashboard-content" href="<?= WEB_PATH ?>/merchant/manage-groups.php">Manage Groups</a>

</p> --> <? //} } ?>


<div id="message-window" title="Message Box" style="display:none">

</div>
<script>
        jQuery(".dashboard-content[assigned='0']").click(function () {
            //    alert("There is no Package assigned to you. Please contact Scanflip account executive for your package subscription.");
            return false;
        });
        jQuery("a[assigned='0']").click(function () {
            var alert_msg = "<?php echo $merchant_msg['dashboard']['Msg_no_package_assigned']; ?>";
            var head_msg = "<div class='head_msg'>Message</div>";
            var footer_msg = "<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='content_msg'></div>";
            var content_msg = "<div class='msg_popup_cancel'>" + alert_msg + "</div>";
            jQuery("#message-window").html(head_msg + content_msg + footer_msg);
            jQuery.fancybox({
                content: jQuery('#message-window').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                // topRatio: 0,

                changeFade: 'fast',
                helpers: {
                    overlay: {
                        opacity: 0.3
                    } // overlay
                }
            });
            //  alert("There is no Package assigned to you. Please contact Scanflip account executive for your package subscription.");
            return false;
        });
        // 14 10 2013
        jQuery("a[active_locations='0']").click(function () {
            var alert_msg = "<?php echo $merchant_msg['dashboard']['Msg_no_active_location']; ?>";
            var head_msg = "<div class='head_msg'>Message</div>";
            var footer_msg = "<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='content_msg'></div>";
            var content_msg = "<div class='msg_popup_cancel'>" + alert_msg + "</div>";
            jQuery("#message-window").html(head_msg + content_msg + footer_msg);
            jQuery.fancybox({
                content: jQuery('#message-window').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                // topRatio: 0,

                changeFade: 'fast',
                helpers: {
                    overlay: {
                        opacity: 0.3
                    } // overlay
                }
            });
            //  alert("There is no Package assigned to you. Please contact Scanflip account executive for your package subscription.");
            return false;
        });
        // 14 10 2013
</script>
