<?php
/**
 * @uses to copy compaign
 * @used in pages : add-user.php,apply_filter.php,compaigns.php,edit-user.php,footer.php
 *
 */
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where_act['active'] = 1;
$RSCat = $objDB->Show("categories", $array_where_act);

//$array_where['created_by'] = $_SESSION['merchant_id'];
$array_where['id'] = $_REQUEST['id'];
$RS = $objDB->Show("campaigns", $array_where);
$array = array();
$array_where_user['id'] = $_SESSION['merchant_id'];
$RS_User = $objDB->Show("merchant_user", $array_where_user);
$m_parent = $RS_User->fields['merchant_parent'];
if ($RS_User->fields['merchant_parent'] == 0) {
        $array['created_by'] = $_SESSION['merchant_id'];
        $array['active'] = 1;
        $RSStore = $objDB->Show("locations", $array);
        $RSStore1 = $objDB->Show("locations", $array);
} else {
        $media_acc_array = array();
        $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
        $RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
        $location_val = $RSmedia->fields['location_access'];


        //$sql = "SELECT * FROM locations WHERE active=1 and id in (select l.id from locations l where l.created_by=".$m_parent ." and l.id = ".$location_val .")";
        /* $sql = "SELECT * FROM locations WHERE active=1 and id=".$location_val;

          $RSStore = $objDB->execute_query($sql);
          $RSStore1 = $objDB->execute_query($sql); */
        $RSStore = $objDB->Conn->Execute("SELECT * FROM locations WHERE active=1 and id=?", array($location_val));
}

$array = $location_id = array();
$array['campaign_id'] = $_REQUEST['id'];
$RSComp = $objDB->Show("campaign_location", $array);
while ($Row = $RSComp->FetchRow()) {
        $location_id[] = $Row['location_id'];
}
$array = array();
$array['campaign_id'] = $_REQUEST['id'];
$RSCode = $objDB->Show("activation_codes", $array);

$array = array();
$array['merchant_id'] = $_SESSION['merchant_id'];
$RSGroups = $objDB->Show("merchant_groups", $array);

/* * **** */
$arr = file(WEB_PATH . '/merchant/process.php?check_campaign_active=yes&mer_id=' . $_SESSION['merchant_id'] . '&cid=' . $_REQUEST['id']);
if (trim($arr[0]) == "") {
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);
$is_active = $json->total_records;
if ($is_active > 0) {
        //  echo "active";
        $check_active = "true";
} else {
        // echo "inactive";
        $check_active = "false";
}
/* * ** */


$pack_data1 = $json_array = array();
$pack_data = $json_array = array();
if ($RS_User->fields['merchant_parent'] == 0) {
        $pack_data['merchant_id'] = $_SESSION['merchant_id'];
        $get_pack_data = $objDB->Show("merchant_billing", $pack_data);
        $pack_data1['id'] = $get_pack_data->fields['pack_id'];
        $get_billing_pack_data = $objDB->Show("billing_packages", $pack_data1);
} else {
        $arr = file(WEB_PATH . '/merchant/process.php?getmainmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);

        $main_merchant_id = $json->main_merchant_id;


        $pack_data['merchant_id'] = $main_merchant_id;
        $get_pack_data = $objDB->Show("merchant_billing", $pack_data);
        $pack_data1['id'] = $get_pack_data->fields['pack_id'];
        $get_billing_pack_data = $objDB->Show("billing_packages", $pack_data1);
}

$imag_exist = 1;
if (file_exists(UPLOAD_IMG . '/m/campaign/' . $RS->fields['business_logo'])) {
        //echo "exist";
        $imag_exist = 1;
} else {
        //echo "not exist";
        $imag_exist = 0;
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Copy Campaign</title>
        <?php //require_once(MRCH_LAYOUT . "/head.php"); ?>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/main.css">

        <script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/m/jquery.carouFredSel-6.2.1-packed.js"></script>

        <script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/m/prototype-1.js"></script>
        <script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/m/prototype-base-extensions.js"></script>
        <script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/m/prototype-date-extensions.js"></script>
        <script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/m/behaviour.js"></script>

        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/datepicker.css">
        <script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/m/behaviors.js"></script>
        <!-- T_7 -->
        <script language="javascript" src="<?= ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
        <!-- T_7 -->
        <!--// 369-->
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/jquery.form.js"></script>
        <!--// 369-->
        <script type="text/javascript" src="<?= ASSETS ?>/tinymce/tiny_mce.js"></script>
        <script language="javascript">
                /*$(function()
                 {		
                 $('#start_date').datepick({dateFormat: 'mm-dd-yyyy'});
                 $('#expiration_date').datepick({dateFormat: 'mm-dd-yyyy'});
                 });*/
                jQuery(document).ready(function () {
                    window.tinymce.dom.Event.domLoaded = true;
                    tinyMCE.init({
                        // General options
                        //mode : "textareas",
                        mode: "exact",
                        elements: "description,terms_condition",
                        theme: "advanced",
                        plugins: "lists,searchreplace",
                        valid_elements: 'p,br,ul,ol,li,sub,sup',
                        // Theme options
                        //theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                        theme_advanced_buttons1: "replace,|,bullist,numlist,|,sub,sup,|,charmap",
                        //theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                        //theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
                        theme_advanced_toolbar_location: "top",
                        theme_advanced_toolbar_align: "left",
                        theme_advanced_statusbar_location: "bottom",
                        theme_advanced_resizing: true,
                        // Example content CSS (should be your site CSS)
                        content_css: "<?= ASSETS ?>/tinymce/content.css",
                        // Drop lists for link/image/media/template dialogs
                        template_external_list_url: "<?= ASSETS ?>/tinymce/lists/template_list.js",
                        external_link_list_url: "<?= ASSETS ?>/tinymce/lists/link_list.js",
                        external_image_list_url: "<?= ASSETS ?>/tinymce/lists/image_list.js",
                        media_external_list_url: "<?= ASSETS ?>/tinymce/lists/media_list.js",
                        // Style formats
                        style_formats: [
                            {title: 'Bold text', inline: 'b'},
                            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                            {title: 'Example 1', inline: 'span', classes: 'example1'},
                            {title: 'Example 2', inline: 'span', classes: 'example2'},
                            {title: 'Table styles'},
                            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                        ],
                        // Replace values for the template plugin
                        template_replace_values: {
                            username: "Some User",
                            staffid: "991234"
                        },
                        charLimit: 1200,
                        setup: function (ed) {
                            //peform this action every time a key is pressed
                            ed.onKeyDown.add(function (ed, e) {
                                var textarea = tinyMCE.activeEditor.getContent();
                                
                                var lastcontent = textarea;
                                //define local variables
                                var tinymax, tinylen, htmlcount;
                                //manually setting our max character limit
                                tinymax = ed.settings.charLimit;
                                //grabbing the length of the curent editors content
                                tinylen = ed.getContent().replace(/(<([^>]+)>)/ig, "").length;
                                //setting up the text string that will display in the path area
                                //htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;
                                //if the user has exceeded the max turn the path bar red.
                                
                                if (tinylen + 1 > tinymax && e.keyCode != 8) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return false;
                                }
                            });
                            ed.onKeyUp.add(function (ed, e) {
                                
                                var textarea = tinyMCE.activeEditor.getContent();
                                var lastcontent = textarea;
                                //define local variables
                                var tinymax, tinylen, htmlcount;
                                //manually setting our max character limit
                                tinymax = ed.settings.charLimit;
                                //grabbing the length of the curent editors content
                                tinylen = ed.getContent().replace(/(<([^>]+)>)/ig, "").length;
                                //setting up the text string that will display in the path area
                                //htmlcount = "HTML Character Count: " + tinylen + "/" + tinymax;

                                var l = tinymax - tinylen;

                                if (tinyMCE.activeEditor.id == "description")
                                    document.getElementById("desc_limit").innerHTML = l + " characters remaining | no HTML allowed";
                                if (tinyMCE.activeEditor.id == "terms_condition")
                                    document.getElementById("terms_limit").innerHTML = l + " characters remaining | no HTML allowed";
                            });
                        }
                    });
                    jQuery(".textarea_loader").css("display", "none");
                    jQuery(".textarea_container").css("display", "block");
                });
        </script>
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.min.js"></script>
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
        <!--<link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/jquery.tooltip.css" />
        
        <script src="<?= ASSETS_JS ?>/m/jquery.tooltip.js" type="text/javascript"></script> -->
        <!--- tooltip css --->
        
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
                <div id="content">  <h3><?php echo $merchant_msg["edit-compaign"]["Field_copy_campaign"]; ?></h3>
                    <!--// 369--><form action="process.php" method="post" enctype="multipart/form-data" id="edit_campaign_form" ><!-- edit_campaign_form-->
                        <input type="hidden" name="hdn_transcation_fees" id="hdn_transcation_fees" value="<?php echo $get_billing_pack_data->fields['transaction_fees']; ?>" />
                        <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="<? if($imag_exist==1){echo $RS->fields['business_logo'];}?>" />
                        <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
                        <table width="100%"  border="0" cellspacing="2" cellpadding="2" class="cls_left" id="table_spacer">

                            <tr>
                                <td width="21%" align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_campaign_title"]; ?>

                                </td>
                                <td width="79%" align="left">
                                    <?php
                                    $camp_tit = $RS->fields['title'];
                                    ?>
                                    <input type="text" placeholder="Required" name="title" id="title" value="<?= htmlentities($camp_tit) ?>" maxlength="76" size="50" onkeyup="changetext1()" /><span class="span_c1">Maximum 76 characters | no HTML allowed</span>
                                </td>  
                            </tr>
                            <tr>
                                <td width="21%" align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_campaign_tag"]; ?>
                                </td>
                                <td width="79%" align="left"><input type="text" placeholder="Enter upto 3 keywords separated by commas" name="campaign_tag" id="campaign_tag"   size="50" value="" /> <span class="notification_tooltip"  title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_Campaign_Tag"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>  
                            </tr>


                            <tr>
                                <td align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_deal_value"]; ?>
                                </td>
                                <td align="left">
                                    <input type="text" name="deal_value" placeholder="00"id="deal_value" value="<?= $RS->fields['deal_value'] ?>" /><span class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_deal_value"]; ?>" >&nbsp;&nbsp;&nbsp</span><span id="numeric">numeric, no currency symbol</span>
                                </td>
                            </tr>
                            <tr id="tr_discount" >
                                <td width="20%" align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_discount_rate"]; ?>
                                </td>
                                <td width="20%" align="left">
                                    <input type="text" name="discount" placeholder="00" id="discount" value="<?= $RS->fields['discount'] ?>" placeholder="please enter discount %" size="25"/><span class="notification_tooltip"  title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_discount_rate"]; ?>">&nbsp;&nbsp;&nbsp;</span><span id="numeric">numeric</span>
                                </td>

                            </tr> 

                            <tr id="tr_saving" >
                                <td width="20%" align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_saving_rate"]; ?>
                                </td>
                                <td width="20%" align="left">
                                    <input type="text" name="saving" placeholder="00" id="saving" value="<?= $RS->fields['saving'] ?>" placeholder="Please enter amount" size="25"/><span class="notification_tooltip"  title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_saving_rate"]; ?>">&nbsp;&nbsp;&nbsp;</span><span id="numeric">numeric, no currency symbol</span>
                                </td>

                            </tr>

                            <tr>
                                <td align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_category"]; ?>
                                </td>
                                <td align="left">
                                    <select name="category_id" id="category_id">
                                        <option value="0">Select Category</option>
                                        <?
                                        while($Row = $RSCat->FetchRow()){
                                        ?>
                                        <option value="<?= $Row['id'] ?>" <? if($RS->fields['category_id'] == $Row['id']) echo "selected";?>><?= $Row['cat_name'] ?></option>
                                        <?
                                        }
                                        ?>
                                    </select><span class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_category"]; ?>" >&nbsp;&nbsp;&nbsp </span>
                                </td>
                            </tr>

                            <tr>
                                <td align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_campaign_logo"]; ?>
                                </td>
                                <td align="left">
                                    <!-- start of  PAY-508-28033 -->
                                    <!--<input type="button" name="btn_start_upload" id="btn_start_upload" value="manage images" onclick="open_popup('Notification');" />-->
                                    <div class="cls_left">
                                                            <!--<input type="file" name="business_logo" id="business_logo" class="file_btn" />-->
                                        <div id="upload" title='Attach image for the campaign' >
                                            <span  >Browse
                                            </span> 
                                        </div>
                                    </div>  <div class="browse_right_content" > &nbsp;&nbsp;<span >Or select from </span><a class="mediaclass"  > media library </a>
                                        <span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["tooltip_upload_image"]; ?>" >&nbsp;&nbsp;&nbsp </span>
                                    </div> 
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

                            <tr>
                                <td align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_deal_description"]; ?>
                                    <span class="notification_tooltip deal_desc_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_campaign_web_description"]; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
                                <td align="left" class="textare_td">
                                    <div align="center" class="textarea_loader">
                                        <img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="defaul_table_loader" />
                                    </div>
                                    <div class="textarea_container" style="display: none;">	

                                        <textarea id="description" name="description" rows="15" cols="80" class="table_th_90"><?= htmlentities($RS->fields['description']) ?></textarea>
                                    </div><span id="desc_limit">Maximum 1200 characters | no HTML allowed</span>
                                </td>
                            </tr>

                            <tr>
                                <td align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_terms_condition"]; ?>
                                    <span class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_terms_condition"]; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
                                <td align="left" class="textare_td">
                                    <div align="center" class="textarea_loader">
                                        <img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="defaul_table_loader" />
                                    </div>
                                    <div class="textarea_container" style="display: none;">	
                                        <textarea id="terms_condition" name="terms_condition" rows="15" cols="80"  class="table_th_90"><?= htmlentities($RS->fields['terms_condition']) ?></textarea>
                                    </div><span id="terms_limit">Maximum 1200 characters | no HTML allowed</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">&nbsp;
                                </td>
                            </tr>
                            <?php if ($_SESSION['merchant_info']['merchant_parent'] == 0) { ?>
                                    <tr>
                                        <td colspan="2">
                                            <div class="select_location_heading">
                                                <?php echo $merchant_msg["edit-compaign"]["Field_select_location"]; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">

                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" colspan="2">
                                            <table class="locationgrid" id="activationcode" width="100%"  border="0" cellspacing="1" cellpadding="1" >
                                                <tr class="locationgrid_heading">
                                                    <td class="table_th_9">&nbsp;
                                                    </td>
                                                    <td class="table_th_63">
                                                        <?php echo $merchant_msg["edit-compaign"]["Field_location"]; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $merchant_msg["edit-compaign"]["Field_number_of_activation_code"]; ?>
                                                        <span class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_total_offers"]; ?>">&nbsp;&nbsp;&nbsp;</span>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div class="activationcode_container" >
                                                <table id="activationcode" width="100%"  border="0" cellspacing="1" cellpadding="1">

                                                    <?php
                                                    if ($RSStore->RecordCount() > 0) {
                                                            $cnt = 0;
                                                            while ($Row = $RSStore->FetchRow()) {
                                                                    ?>
                                                                    <tr>
                                                                        <td width="7%">
                                                                            <input type="checkbox" id="lctn_id_<?= $Row['id'] ?>" name="location_id[]" value="<?= $Row['id'] ?>" class="camp_location" /> 
                                                                        </td>
                                                                        <td width="45%">

                                                                            <label for="lctn_id_<?= $Row['id'] ?>">
                                                                                <?= $Row['address'] . ", " . $Row['city'] . ", " . $Row['state'] . ", " . $Row['zip'] ?>
                                                                            </label>
                                                                        </td>
                                                                        <td width="25%"> 
                                                                            <input id="num_activation_code_<?= $Row['id'] ?>" class="clsnum_activation_code" type="textbox" placeholder="00" name="num_activation_code_<?= $Row['id'] ?>" value="" /> 

                                                                        </td>
                                                                    </tr>

                                                                    <?php
                                                                    $cnt++;
                                                            }
                                                    }
                                                    ?>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                            <?php } else { ?>
                                    <tr>

                                        <td align="left"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div class="activationcode_container_employee" >
                                                <?php echo $merchant_msg["edit-compaign"]["Field_select_location1"]; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" colspan="2" >
                                            <div> 
                                                <table class="locationgrid1" id="activationcode" width="100%"  border="0" cellspacing="1" cellpadding="1" >
                                                    <tr class="locationgrid_heading">

                                                        <td>
                                                            <?php echo $merchant_msg["edit-compaign"]["Field_location"]; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $merchant_msg["edit-compaign"]["Field_number_of_activation_code"]; ?>
                                                            <span class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_total_offers"]; ?>">&nbsp;&nbsp;&nbsp;</span>
                                                        </td>
                                                    </tr>

                                                    <tr class="employee_assigned_location">
                                                        <td width="41%" align="left" >

                                                            <?php echo $RSStore->fields['address'] . ", " . $RSStore->fields['city'] . ", " . $RSStore->fields['state'] . ", " . $RSStore->fields['zip'] ?></td>

                                                        <td width="17%">
                                                            <?php
                                                            $arr_code = array();
                                                            $arr_code['location_id'] = $RSStore->fields['id'];
                                                            $sub_location_id = $RSStore->fields['id'];
                                                            $arr_code['campaign_id'] = $_REQUEST['id'];
                                                            $RS_num_act_code = $objDB->Show("campaign_location", $arr_code);
                                                            ?>
                                                            <input type="textbox" placeholder="00"id="num_activation_code_<?= $RSStore->fields['id'] ?>" name="num_activation_code_<?= $RSStore->fields['id'] ?>" value="" /> 

                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>

                                        </td>




                                    </tr>
                                    <input type="hidden" name="hdn_location_id" id="hdn_location_id" value="<?= $RSStore->fields['id'] ?>" />
                            <?php } ?>

                            <td colspan="2">&nbsp;
                            </td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_date"]; ?>
                                </td>
                                <td align="left">

                                    &nbsp; &nbsp; &nbsp; &nbsp;<?php echo $merchant_msg["edit-compaign"]["Field_start_date"]; ?><input readonly type="text" class="datetimepicker" name="start_date" id="start_date" placeholder="Required" value="">
                                    <span class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_startdate"]; ?>">&nbsp;&nbsp;&nbsp;</span>


                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <?php echo $merchant_msg["edit-compaign"]["Field_end_date"]; ?><input type="text" readonly name="expiration_date" class="datetimepicker" id="expiration_date" placeholder="Required"  value="">
                                    <span class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_expiredate"]; ?>">&nbsp;&nbsp;&nbsp;</span>
                                </td>
                            </tr>


                            <!--b-->
                            <tr>
                                <td align="right"><?php echo $merchant_msg["edit-compaign"]["Field_redeem_points"]; ?></td>
                                <td align="left">
                                    <table style="width: 100%">
                                        <tr>
                                            <td align="left" width="50%"><input type="text" placeholder="00"id="redeem_rewards" name="redeem_rewards" value="<?= $RS->fields['redeem_rewards'] ?>"/><span title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_redeem_points"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span></td>
                                            <td align="left" width="50%"><?php echo $merchant_msg["edit-compaign"]["Field_available_scanflip_points"]; ?>&nbsp;
                                                <b><?php
                                                    $arr = file(WEB_PATH . '/merchant/process.php?get_available_points_of_login_user=yes&mer_id=' . $_SESSION['merchant_id']);
                                                    if (trim($arr[0]) == "") {
                                                            unset($arr[0]);
                                                            $arr = array_values($arr);
                                                    }
                                                    $json = json_decode($arr[0]);
                                                    $total_points = $json->available_points;

                                                    echo "<span class='available_point_span' >" . $total_points . "</span>";
                                                    ?></b><span class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_available_points"]; ?>">&nbsp;&nbsp;&nbsp;</span>
                                            </td>
                                        </tr>			
                                    </table>

                                </td>
                            </tr>
                            <tr>
                                <td align="right"><?php echo $merchant_msg["edit-compaign"]["Field_sharing_points"]; ?></td>
                                <td align="left">
                                    <table style="width: 100%">
                                        <tr>
                                            <td align="left" width="50%"><input type="text" id="referral_rewards" placeholder="00" name="referral_rewards" value="<?= $RS->fields['referral_rewards'] ?>"/><span title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_sharing_points"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span></td>
                                            <td align="left" width="50%">
                                                <?php
                                                if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
                                                        ?>
                                                        <a href="javascript:void(0)" class="p_btn" ><?php echo $merchant_msg["edit-compaign"]["Field_purchase_scanflip_points"]; ?></a>
                                                        <?php
                                                        $arr = file(WEB_PATH . '/merchant/process.php?get_point_package=yes');
                                                        if (trim($arr[0]) == "") {
                                                                unset($arr[0]);
                                                                $arr = array_values($arr);
                                                        }
                                                        $json = json_decode($arr[0]);
                                                        $total_records = $json->total_records;
                                                        $records_array = $json->records;
                                                        if ($total_records > 0) {
                                                                foreach ($records_array as $Row) {
                                                                        ?>
                                                                        <span class="notification_tooltip" title="$<?= $Row->price ?> = <?= $Row->points ?><?php echo $merchant_msg["edit-compaign"]["Tooltip_purchase_scanflip_points"]; ?>">&nbsp;&nbsp;&nbsp;</span>
                                                                        <?php
                                                                }
                                                        }
                                                        ?>
                                                        <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>			
                                    </table>

                                </td>
                            </tr>
                            <tr>
                                <td align="right"><?php echo $merchant_msg["edit-compaign"]["Field_referral_customer_limitaion"]; ?></td>
                                <td align="left">
                                    <table style="width: 100%">
                                        <tr>
                                            <td align="left" width="50%"><input type="text" placeholder="00" id="max_no_of_sharing" name="max_no_of_sharing" value="<?= $RS->fields['max_no_sharing'] ?>" /><span title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_referral_customer_limit"]; ?>" class="notification_tooltip">&nbsp;&nbsp;&nbsp;</span></td>
                                            <td align="left">

                                            </td>
                                        </tr>			
                                    </table>        
                                </td>
                            </tr>

                            <tr>
                                <td align="right" valign="top">&nbsp;  </td>
                                <td align="left" valign="top">
                                    <input type="radio" name="is_walkin" class="is_walkin" id="groupid" value="0" <?php
                                    if ($RS->fields['is_walkin'] == 0) {
                                            echo "checked";
                                    }
                                    ?> onclick ="check_is_walkin(this.value)" /><label for='groupid' class="chk_align"><?php echo $merchant_msg["edit-compaign"]["Field_group"]; ?></label>
                                    <input type="radio" name="is_walkin" class="is_walkin" id="walkinid" value="1" <?php
                                    if ($RS->fields['is_walkin'] == 1) {
                                            echo "checked";
                                    }
                                    ?> onclick="check_is_walkin(this.value)" /><label for='walkinid' class="chk_align"> <?php echo $merchant_msg["edit-compaign"]["Field_walking"]; ?></label><span title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_walkin"]; ?>" class="notification_tooltip" >&nbsp;&nbsp;&nbsp;</span>

                                </td>
                            </tr>
                            <tr id="group" style="display:<?php
                            if ($RS->fields['is_walkin'] == "1") {
                                    echo "none";
                            } else {
                                    echo "";
                            }
                            ?>">
                                <td align="right" valign="top"><?php echo $merchant_msg["edit-compaign"]["Field_camapaign_visibility"]; ?><span class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_visibility"]; ?>">&nbsp;&nbsp;&nbsp; </span>:  </td>
                                <td align="left" valign="top">
                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="left">
                                                <input type="checkbox" id="assign_group" name="assign_group" class="chk_private" value="1" />
                                                <label class='chk_align' for="assign_group"><?php echo $merchant_msg["edit-compaign"]["Field_public"]; ?></label>
                                            </td>

                                        </tr>

                                        <tr>
                                            <td colspan="2">
                                                <table class="location_listing_area" >
                                                    <?php
                                                    if ($_SESSION['merchant_info']['merchant_parent'] != 0) {

                                                            $RS_2 = $objDB->Conn->Execute("select l.*,l.location_name ,l.id lid, mg.*,(select count(*) as Cust_Total from merchant_subscribs mu where mu.group_id =mg.id and mu.user_id in (select id from customer_user cu where cu.active=1)) Total 
    from merchant_groups mg , locations l where mg.isDeleted!=? and mg.active = ? and l.active = ? and l.id = mg.location_id and  mg.location_id =?", array(1, 1, 1, $sub_location_id));


                                                            if ($RS_2->RecordCount() > 0) {
                                                                    $temp_val = $RS_2->fields['lid'];
                                                                    ?>
                                                                    <tr id="locationid_<?php echo $RS_2->fields['lid']; ?>">
                                                                        <td class="table_th_5">
                                                                            <img src="<?php echo ASSETS_IMG ?>/m/marker_rounded_grey.png" />
                                                                        </td>
                                                                        <td class="table_th_34">

                                                                            <?php echo $RS_2->fields['address'] . ", " . $RS_2->fields['city'] . ", " . $RS_2->fields['state'] . ", " . $RS_2->fields['zip'] ?>&nbsp;:&nbsp;
                                                                        </td>
                                                                        <td >
                                                                            <input type="hidden" name="hdn_campaign_type_<?php echo $RS_2->fields['lid']; ?>" id="hdn_campaign_type_<?php echo $RS_2->fields['lid']; ?>" value="0" />
                                                                            <input type="hidden" name="hdn_campaign_type1_<?php echo $RS_2->fields['lid']; ?>" id="hdn_campaign_type1_<?php echo $RS_2->fields['lid']; ?>" value="0" />
                                                                            <ul class="locationgroup_" id="locationgroup_<?php echo $RS_2->fields['lid']; ?>">
                                                                                <?php
                                                                                while ($Row = $RS_2->FetchRow()) {
                                                                                        if ($Row['private'] == 1) {
                                                                                                //  echo $Row['id']."==".$_REQUEST['id']."==".$_SESSION['merchant_id'];
                                                                                                $array_2 = array();
                                                                                                $array_2['group_id'] = $Row['id'];
                                                                                                $array_2['campaign_id'] = $_REQUEST['id'];
                                                                                                $array_2['merchant_id'] = $_SESSION['merchant_id'];
                                                                                                $RSCheck = $objDB->Show("campaign_groups", $array_2);
                                                                                                //print_r($RSCheck);
                                                                                                //echo $RSCheck->RecordCount();
                                                                                                // 03-04-2014 

                                                                                                $arr_sub = file(WEB_PATH . '/merchant/process.php?getallsubmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
                                                                                                if (trim($arr_sub[0]) == "") {
                                                                                                        unset($arr_sub[0]);
                                                                                                        $arr_sub = array_values($arr_sub);
                                                                                                }
                                                                                                $json_sub = json_decode($arr_sub[0]);
                                                                                                $ids = $json_sub->all_sub_merchant_id;

                                                                                                if ($ids == "")
                                                                                                        $ids = $_SESSION['merchant_id'];

                                                                                                $arr_main = file(WEB_PATH . '/merchant/process.php?getallmainmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
                                                                                                if (trim($arr_main[0]) == "") {
                                                                                                        unset($arr_main[0]);
                                                                                                        $arr_main = array_values($arr_main);
                                                                                                }
                                                                                                $json_main = json_decode($arr_main[0]);
                                                                                                $ids1 = $json_main->all_main_merchant_id;

                                                                                                if ($ids1 == "")
                                                                                                        $ids1 = $_SESSION['merchant_id'];

                                                                                                /* $Sql = "SELECT * FROM campaign_groups WHERE group_id=".$Row['id']." and campaign_id=".$_REQUEST['id']." and (merchant_id=". $_SESSION['merchant_id']." or merchant_id in($ids) or merchant_id in ($ids1))";
                                                                                                  $RSCheck = $objDB->Conn->Execute($Sql); */
                                                                                                $RSCheck = $objDB->Conn->Execute("SELECT * FROM campaign_groups WHERE group_id=? and campaign_id=? and (merchant_id=? or merchant_id in(?) or merchant_id in (?))", array($Row['id'], $_REQUEST['id'], $_SESSION['merchant_id'], $ids, $ids1));

                                                                                                // 03-04-2014
                                                                                                ?>
                                                                                                <li class="location_li">
                                                                                                    <input type="checkbox" id="chk_location_groups" name="chk_location_groups[]" value="<?php echo $Row['id']; ?>" <?php
                                                                                                    if ($Row['private'] == 1) {
                                                                                                            echo "class='private_group' private_group=private_group";
                                                                                                    } else {
                                                                                                            echo "class='other_group'";
                                                                                                    }
                                                                                                    ?> >
                                                                                                           <?php //echo $Row['group_name'];  ?>
                                                                                                    Location Subscribers
                                                                                                </li>
                                                                                                <?php
                                                                                                $camp_type = 0;
                                                                                                if ($RS->fields['level'] == 1) {
                                                                                                        $camp_type = 1;
                                                                                                } else {
                                                                                                        if ($Row['private'] == 1) {
                                                                                                                $camp_type = 2;
                                                                                                        } else {
                                                                                                                $camp_type = 3;
                                                                                                        }
                                                                                                }
                                                                                                ?>
                                                                                                <script type="text/javascript">
                                                                                                        //jQuery("#hdn_campaign_type_<?php echo $RS_2->fields['lid'] ?>").val("<?php echo $camp_type ?>");
                                                                                                </script>
                                                                                                <?php
                                                                                        } else if ($Row['private'] == 0) {
                                                                                                if ($Row['Total'] > 0) { // condition to show other dist list with customers > 0
                                                                                                        //  echo $Row['id']."==".$_REQUEST['id']."==".$_SESSION['merchant_id'];
                                                                                                        $array_2 = array();
                                                                                                        $array_2['group_id'] = $Row['id'];
                                                                                                        $array_2['campaign_id'] = $_REQUEST['id'];
                                                                                                        $array_2['merchant_id'] = $_SESSION['merchant_id'];
                                                                                                        $RSCheck = $objDB->Show("campaign_groups", $array_2);

                                                                                                        $arr_sub = file(WEB_PATH . '/merchant/process.php?getallsubmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
                                                                                                        if (trim($arr_sub[0]) == "") {
                                                                                                                unset($arr_sub[0]);
                                                                                                                $arr_sub = array_values($arr_sub);
                                                                                                        }
                                                                                                        $json_sub = json_decode($arr_sub[0]);
                                                                                                        $ids = $json_sub->all_sub_merchant_id;

                                                                                                        if ($ids == "")
                                                                                                                $ids = $_SESSION['merchant_id'];

                                                                                                        $arr_main = file(WEB_PATH . '/merchant/process.php?getallmainmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
                                                                                                        if (trim($arr_main[0]) == "") {
                                                                                                                unset($arr_main[0]);
                                                                                                                $arr_main = array_values($arr_main);
                                                                                                        }
                                                                                                        $json_main = json_decode($arr_main[0]);
                                                                                                        $ids1 = $json_main->all_main_merchant_id;

                                                                                                        if ($ids1 == "")
                                                                                                                $ids1 = $_SESSION['merchant_id'];

                                                                                                        /* $Sql = "SELECT * FROM campaign_groups WHERE group_id=".$Row['id']." and campaign_id=".$_REQUEST['id']." and (merchant_id=". $_SESSION['merchant_id']." or merchant_id in($ids) or merchant_id in ($ids1))";
                                                                                                          $RSCheck = $objDB->Conn->Execute($Sql); */
                                                                                                        $RSCheck = $objDB->Conn->Execute("SELECT * FROM campaign_groups WHERE group_id=? and campaign_id=? and (merchant_id=? or merchant_id in(?) or merchant_id in (?))", array($Row['id'], $_REQUEST['id'], $_SESSION['merchant_id'], $ids, $ids1));

                                                                                                        // 03-04-2014
                                                                                                        ?>
                                                                                                        <li class="location_li">
                                                                                                            <input type="checkbox" id="chk_location_groups" name="chk_location_groups[]"  value="<?php echo $Row['id']; ?>" <?php
                                                                                                            if ($Row['private'] == 1) {
                                                                                                                    echo "class='private_group' private_group=private_group";
                                                                                                            } else {
                                                                                                                    echo "class='other_group'";
                                                                                                            }
                                                                                                            ?> ><?php echo $Row['group_name']; ?>
                                                                                                        </li>
                                                                                                        <?php
                                                                                                        $camp_type = 0;
                                                                                                        if ($RS->fields['level'] == 1) {
                                                                                                                $camp_type = 1;
                                                                                                        } else {
                                                                                                                if ($Row['private'] == 1) {
                                                                                                                        $camp_type = 2;
                                                                                                                } else {
                                                                                                                        $camp_type = 3;
                                                                                                                }
                                                                                                        }
                                                                                                        ?>
                                                                                                        <script type="text/javascript">
                                                                                                                //jQuery("#hdn_campaign_type_<?php echo $RS_2->fields['lid'] ?>").val("<?php echo $camp_type ?>");
                                                                                                        </script>
                                                                                                        <?php
                                                                                                }
                                                                                        }
                                                                                }
                                                                                ?>
                                                                            </ul>

                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                            }
                                                    }
                                                    ?>
                                                </table>
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="right"><?php echo $merchant_msg["edit-compaign"]["Field_camapaign_limitation"]; ?></td>
                                <td align="left">
                                    <input type="radio"  id="one_per_customer" name="number_of_use" value="1" <? if($RS->fields['number_of_use'] == 1) echo "checked";?> />
                                           <label for="one_per_customer" class="chk_align"><?php echo $merchant_msg["edit-compaign"]["Field_one_per_customer"]; ?></label>

                                    <input type="radio" id="one_per_customer_per_day" name="number_of_use" value="2" <? if($RS->fields['number_of_use'] == 2) echo "checked";?> />
                                           <label for="one_per_customer_per_day" class="chk_align"><?php echo $merchant_msg["edit-compaign"]["Field_one_per_customer_per_day"]; ?></label>

                                    <input type="radio" id="multiple_use" name="number_of_use" value="3" <? if($RS->fields['number_of_use'] == 3) echo "checked";?> />
                                           <label for="multiple_use" class="chk_align"><?php echo $merchant_msg["edit-compaign"]["Field_redeem_point_visit"]; ?></label>
                                </td>
                            </tr>

                            <tr>
                                <td align="right"><?php echo $merchant_msg["edit-compaign"]["Field_valid_new_customer"]; ?></td>
                                <td align="left">
                                    <input type="checkbox" name="new_customer_only" id="new_customer_only"  /> 

                                </td>
                            </tr>

                            <tr>
                                <td>&nbsp;</td>
                                <td align="left">
                                    <input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>">
                                    <input type="submit" name="btnAddCampaigns" id="btnAddCampaigns" value="<?php echo $merchant_msg['index']['btn_save']; ?>" onclick="mycall()" >
                                    <!--// 369-->
                                    <script>
                                            function btncanCampaign() {
                                                window.location = "<?= WEB_PATH ?>/merchant/compaigns.php?action=active";
                                            }
                                            function mycall()
                                            {

                                                jQuery('#description').val(tinyMCE.get('description').getContent());
                                                //jQuery('#deal_detail_description').val(tinyMCE.get('deal_detail_description').getContent());
                                                jQuery('#terms_condition').val(tinyMCE.get('terms_condition').getContent());

                                            }
                                    </script>
                                    <input type="submit" name="btnCanCampaigns" value="<?php echo $merchant_msg['index']['btn_cancel']; ?>" onClick="btncanCampaign()">

                                    <input type="hidden" id="required_point" name="required_point" value="">
                                    <input type="hidden" id="remaining_point" name="remaining_point" value="">

                                    <script type="text/javascript">
                                            var alert_msg = "";
                                            var check_active = "<?php echo $check_active; ?>";
                                            if (check_active == "true")
                                            {
                                                jQuery(".camp_location").each(function () {
                                                    if (jQuery(this).attr("checked") == "checked")
                                                    {
                                                        jQuery(this).attr("onclick", "return false");
                                                        jQuery(this).attr("onchange", "return false");
                                                    }
                                                });
                                                //  jQuery("#start_date").attr("disabled","true");       
                                                //  jQuery("#expiration_date").attr("disabled","true");
                                            }
                                            function validation(filed, message)
                                            {

                                                var f = "";
                                                if (jQuery(filed).val() == "")
                                                {
                                                    alert_msg += message;
                                                    f = "false";
                                                }
                                                else
                                                {
                                                    f = "true";
                                                }
                                                return f;
                                            }
                                            function validation_negative(filed, message)
                                            {
                                                var f = "";

                                                if (jQuery(filed).val() < 0)
                                                {
                                                    alert_msg += message;
                                                    f = "false";
                                                }
                                                else
                                                {
                                                    f = "true";
                                                }
                                                return f;
                                            }

                                            jQuery("#btnAddCampaigns").click(function () {

                                                //open_popup("Notificationloader");

                                                var alert_msg = "<?php echo $merchant_msg["index"]["validating_data"]; ?>";
                                                var head_msg = "<div class='head_msg'>Message</div>";
                                                var content_msg = "<div class='content_msg validatingdata' style='background:white;'>" + alert_msg + "</div>";
                                                jQuery("#NotificationloadermainContainer").html(content_msg);

                                                jQuery("#NotificationloaderFrontDivProcessing").css("display", "block");
                                                jQuery("#NotificationloaderBackDiv").css("display", "block");
                                                jQuery("#NotificationloaderPopUpContainer").css("display", "block");
						
                                                var alert_msg = "";
                                                var total_no_of_activation_code = 0;
                                                var flag = "true";
                                                var flag_hdn = "true";
                                                var total_no_of_locations = 0;

                                                var currencyReg = /^\$?[0-9]+(\.[0-9][0-9])?$/;
                                                var hastagRef = /^[a-z0-9 ,\-&_]+$/i;
                                                var numbers = /^[0-9]+$/;

                                                jQuery(".camp_location").each(function () {
                                                    var loc_id = jQuery(this).val();
                                                    if (jQuery(this).attr("checked") == "checked")
                                                    {
                                                        total_no_of_locations = total_no_of_locations + 1;
                                                    }
                                                });
                                                if (jQuery(".camp_location").length != 0)
                                                {
                                                    if (total_no_of_locations <= 0)
                                                    {
                                                        flag = "false";
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_at_least_one_location"]; ?></div>";
                                                    }
                                                }
						
                                                // return false;
                                                var min_share_point = "<?php echo $get_billing_pack_data->fields['min_share_point'] ?>";
                                                var min_reward_point = "<?php echo $get_billing_pack_data->fields['min_reward_point'] ?>";

                                                var referral_rewards = "0";
                                                var f = validation("#title", "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_campaign_title"]; ?></div>");
                                                if (f == 'false') {
                                                    flag = "false";
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_campaign_title"]; ?></div>";
                                                }
						
                                                var tag_value = jQuery("#campaign_tag").val();
                                                var tag_temp1 = 0;
                                                if (tag_value == "")
                                                {
                                                    //flag="true";
                                                }
                                                else
                                                {
                                                    if (hastagRef.test(tag_value))
                                                    {
                                                        //flag="true";
                                                    }
                                                    else
                                                    {
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_Tag"]; ?></div>";
                                                        flag = "false";
                                                        tag_temp1 = 1;
                                                    }
                                                }
						
                                                var deal_value = jQuery("#deal_value").val();
                                                if (jQuery("#deal_value").val() == "")
                                                {
                                                    //flag = validation("#redeem_rewards","<div>* Please Enter Reedem Points.</div>");
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_deal_value"]; ?></div>";
                                                    flag = "false";
                                                }
                                                else
                                                {
                                                    if (!currencyReg.test(jQuery("#deal_value").val())) {
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_deal_value_proper"]; ?></div>";
                                                        flag = "false";
                                                    }
                                                    else {
                                                        var n = deal_value.indexOf("$");
                                                        if (n != "-1")
                                                        {
                                                            jQuery("#deal_value").val((jQuery("#deal_value").val()).substring(n + 1, (jQuery("#deal_value").val()).length));
                                                        }
                                                    }

                                                }
                                                var discount = jQuery("#discount").val();
                                                if (discount == "")
                                                {

                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_discount"]; ?></div>";
                                                    flag = "false";
                                                }
                                                if (parseInt(discount) > 100)
                                                {
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_proper_discount"]; ?></div>";
                                                    flag = "false";
                                                }
                                                var saving = jQuery("#saving").val();
                                                if (saving == "")
                                                {

                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_saving"]; ?></div>";
                                                    flag = "false";
                                                }
                                                if (parseFloat(deal_value) == 0 && (parseFloat(discount) > 0 || parseFloat(saving) > 0))
                                                {
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_deal_value_proper"]; ?></div>";
                                                    flag = "false";
                                                }
                                                if (parseFloat(deal_value) > 0 && (parseFloat(discount) == 0 || parseFloat(saving) == 0))
                                                {
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_proper_discount_saving"]; ?></div>";
                                                    flag = "false";
                                                }
                                                var cat_id = jQuery('#category_id option:selected').val();
                                                if (cat_id == 0)
                                                {
                                                    flag = "false";
                                                    alert_msg += "<div><?php echo $merchant_msg['edit-compaign']['Msg_select_campaign_category']; ?></div>";
                                                }
                                                var term_box_val = jQuery('#terms_condition').val(tinyMCE.get('terms_condition').getContent());
                                                var final_term_val = term_box_val.val();

                                                if (jQuery('#groupid').is(':checked'))
                                                {
                                                    if (jQuery(".chk_private").is(':checked')) {

                                                    } else {


                                                        jQuery("input[name^='hdn_campaign_type1']").each(function (index) {
                                                            var hdn_campaign_type_value = jQuery(this).val();

                                                            if (hdn_campaign_type_value == 0)
                                                            {
                                                                flag_hdn = "false";

                                                            }
                                                        });
                                                        if (flag_hdn == "false")
                                                        {
                                                            flag = "false";

                                                            alert_msg += "<?php echo $merchant_msg["edit-compaign"]["Msg_select_campaign_visibility"]; ?>";
                                                        }
                                                        else
                                                        {

                                                        }
                                                    }
                                                }

                                                /*Activation code validation */
                                                if (jQuery(".camp_location").length == 0)
                                                {


                                                    var loc_act_code1 = jQuery('input[name^="num_activation_code_"]').val();
                                                    var loc_act_code = parseInt(jQuery('input[name^="num_activation_code_"]').val());


                                                    if (loc_act_code1 == "" || loc_act_code1 == "NaN")
                                                    {

                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_activation_code"]; ?></div>";
                                                        flag = "false";
                                                        //return false;
                                                    }
                                                    else if (!numbers.match(loc_act_code))
                                                    {
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_activation_code_not_number"]; ?></div>";
                                                        flag = "false";
                                                        //return false;
                                                    }
                                                    else
                                                    {
                                                        total_no_of_activation_code = total_no_of_activation_code + loc_act_code;
                                                    }
                                                }
                                                else
                                                {
                                                    var activation_msg = "";
                                                    var dist_list_id_str = "";
                                                    jQuery(".camp_location").each(function () {

                                                        if (jQuery(this).attr("checked") == "checked")
                                                        {

                                                            var loc_id = jQuery(this).val();

                                                            // 16-09-2013

                                                            jQuery.ajax({
                                                                type: "POST",
                                                                url: "<?= WEB_PATH ?>/merchant/process.php",
                                                                data: "loc_id=" + loc_id + "&check_location_active=yes",
                                                                async: false,
                                                                success: function (msg) {
                                                                    var obj = jQuery.parseJSON(msg);
                                                                    //alert(obj.status);
                                                                    //alert(obj.message);
                                                                    //alert_msg +="<div>* "+ obj.message +"</div>";
                                                                    if (obj.status == "false")
                                                                    {
                                                                        flag = "false";
                                                                        alert_msg += "<div>* " + obj.message + "</div>";
                                                                    }
                                                                }
                                                            });

                                                            jQuery("ul[id^='locationgroup_" + loc_id + "']").each(function (index) {

                                                                jQuery(this).find("li").each(function (index) {

                                                                    var dist_list_id = jQuery(this).find("input[type='checkbox']").attr("value");
                                                                    if (jQuery("#chk_location_groups_" + dist_list_id).is(":checked"))
                                                                    {
                                                                        dist_list_id_str = dist_list_id_str + dist_list_id + ",";

                                                                    }
                                                                });
                                                            });

                                                            // 26-09-2013 check dist list active

                                                            if (jQuery("#num_activation_code_" + loc_id).val() == "" || jQuery("#num_activation_code_" + loc_id).val() == "NaN")
                                                            {

                                                                activation_msg = "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_activation_code"]; ?></div>";
                                                                flag = "false";
                                                                return;
                                                            }
                                                            else if (!jQuery("#num_activation_code_" + loc_id).val().match(numbers) || jQuery("#num_activation_code_" + loc_id).val() == 0)
                                                            {
                                                                activation_msg = "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_activation_code_not_number"]; ?></div>";
                                                                flag = "false";
                                                                return;
                                                            }
                                                            else
                                                            {
                                                                total_no_of_activation_code = total_no_of_activation_code + parseInt(jQuery("#num_activation_code_" + loc_id).val());
                                                                //flag="false";
                                                            }
                                                        }
                                                    });

                                                    // 30 09 2013

                                                    dist_list_id_str = dist_list_id_str.substr(0, dist_list_id_str.length - 1);
                                                    if (dist_list_id_str != "")
                                                    {
                                                        jQuery.ajax({
                                                            type: "POST",
                                                            url: "<?= WEB_PATH ?>/merchant/process.php",
                                                            data: "dist_list_id_str=" + dist_list_id_str + "&check_dist_list_active_bulk=yes",
                                                            async: false,
                                                            success: function (msg) {
                                                                var obj = jQuery.parseJSON(msg);

                                                                if (obj.status == "false")
                                                                {
                                                                    flag = "false";
                                                                    //alert_msg +="<div>* "+ obj.message +"</div>";
                                                                    alert_msg += obj.message;
                                                                }
                                                            }
                                                        });
                                                    }
                                                    // 30 09 2013

                                                    alert_msg += activation_msg;
                                                }
                                                /*End Activation code validation */

                                                /* $ code	*/
                                                if (jQuery("#start_date").val() == "")
                                                {
                                                    flag = "false";
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_start_date"]; ?></div>";
                                                }
                                                if (jQuery("#expiration_date").val() == "")
                                                {
                                                    flag = "false";
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_expiration_date"]; ?></div>";
                                                }
                                                /* End $ code	*/
                                                if (jQuery("#redeem_rewards").val() == "")
                                                {
                                                    //flag = validation("#redeem_rewards","<div>* Please Enter Reedem Points.</div>");
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_redeem_points"]; ?></div>";
                                                    flag = "false";
                                                }
                                                else
                                                {
                                                    numbers = /^[0-9]+$/;
                                                    //if(jQuery("#redeem_rewards").val().match(numbers)){
                                                    if (!jQuery("#redeem_rewards").val().match(numbers))
                                                    {
                                                        //flag = validation_negative("#redeem_rewards","<div>* Reedem Point Can't Be Negative.</div>");
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_redeem_points_not_number"]; ?></div>";
                                                        flag = "false";
                                                    }

                                                }

                                                if (jQuery("#referral_rewards").val() == "")
                                                {
                                                    //flag = validation("#referral_rewards","<div>* Please Enter Sharing Points.</div>");
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_sharing_points"]; ?></div>";
                                                    flag = "false";

                                                }
                                                else
                                                {
                                                    if (!jQuery("#referral_rewards").val().match(numbers))
                                                    {
                                                        //if(parseInt(jQuery("#referral_rewards").val())<=0)
                                                        //{
                                                        //flag = validation_negative("#referral_rewards","<div>* Sharing Point Can't Be Negative.</div>");
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_sharing_points_not_number"]; ?></div>";
                                                        flag = "false";
                                                    }

                                                }


                                                if (jQuery("#max_no_of_sharing").val() == "")
                                                {
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_max_sharing_customers"]; ?></div>";

                                                    //flag = validation("#max_no_of_sharing","<div>* Please Enter Max Number of Sharing For New Customer Registration.</div>");
                                                    flag = "false";
                                                }
                                                else
                                                {
                                                    numbers = /^[0-9]+$/;
                                                    // if(jQuery("#max_no_of_sharing").val().match(numbers)){
                                                    if (!jQuery("#max_no_of_sharing").val().match(numbers))
                                                    {
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_enter_max_sharing_customers_not_negative"]; ?></div>";
                                                        flag = "false";
                                                        //flag = validation_negative("#max_no_of_sharing","<div>* Max Number of Sharing Can't Be Negative.</div>");
                                                    }

                                                }


                                                if (parseInt(jQuery("#redeem_rewards").val()) == 0 && jQuery("#multiple_use").attr("checked") == "checked")
                                                {
                                                    flag = "false";
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_redeem_point_negative"]; ?></div>";

                                                }
                                                else if (parseInt(jQuery("#redeem_rewards").val()) < min_reward_point)
                                                {

                                                    flag = "false";
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_minimum_redeem_point"]; ?>" + min_reward_point + "</div>";

                                                }
                                                if (min_share_point == "0")
                                                {
                                                    jQuery("#max_no_of_sharing").val(referral_rewards);
                                                }
                                                if (parseInt(jQuery("#referral_rewards").val()) < min_share_point)
                                                {
                                                    flag = "false";
                                                    alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_minimum_sharing_point"]; ?>" + min_share_point + "</div>";
                                                }

                                                var radio_val1 = jQuery("#one_per_customer");
                                                var radio_val2 = jQuery("#one_per_customer_per_day");
                                                var radio_val3 = jQuery("#multiple_use");
                                                var new_customer_only = jQuery("#new_customer_only");


                                                if (radio_val2.attr("checked") == "checked")
                                                {
                                                    if (new_customer_only.attr("checked") == "checked")
                                                    {
                                                        flag = "false";
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_new_customer_with_one_per_customer"]; ?></div>";
                                                    }
                                                }

                                                if (radio_val3.attr("checked") == "checked")
                                                {
                                                    if (new_customer_only.attr("checked") == "checked")
                                                    {
                                                        flag = "false";
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_new_customer_with_one_per_customer"]; ?></div>";
                                                    }
                                                }
                                                if (new_customer_only.attr("checked") == "checked")
                                                {
                                                    jQuery("#new_customer_only").val('1');
                                                }
                                                else
                                                {
                                                    jQuery("#new_customer_only").val('0');
                                                }

                                                if (jQuery("#title").val() == "" || jQuery("#redeem_rewards").val() == "" || jQuery("#referral_rewards").val() == "" || jQuery("#max_no_of_sharing").val() == "" || jQuery("#start_date").val() == "" || jQuery("#expiration_date").val() == "" || tag_temp1 == 1)
                                                {
                                                    //   alert("in if");
                                                    flag = "false";

                                                }
                                                numbers = /^[0-9]+$/;
                                                if (jQuery("#redeem_rewards").val() != "" && jQuery("#redeem_rewards").val() != "0" && jQuery("#referral_rewards") != "" && jQuery("#referral_rewards") != "0" &&
                                                        jQuery("#max_no_of_sharing").val() != "")
                                                {

                                                    if (jQuery("#hdn_transcation_fees").val() != 0)
                                                    {
                                                        var total1 = parseInt(jQuery("#redeem_rewards").val()) * total_no_of_activation_code + (total_no_of_activation_code * jQuery("#hdn_transcation_fees").val());
                                                    }
                                                    else
                                                    {
                                                        var total1 = parseInt(jQuery("#redeem_rewards").val()) * total_no_of_activation_code;
                                                    }

                                                    var total2 = parseInt(jQuery("#referral_rewards").val()) * parseInt(jQuery("#max_no_of_sharing").val());
                                                    var block_point = total1 + total2;
                                                    var purchase_point = parseInt(jQuery(".available_point_span").html());

                                                    if (purchase_point < block_point)
                                                    {
                                                        flag = "false";
                                                        //alert_msg+="* You Can Not Create Campaign As a Required Point Is More Than Available Point.\n  Your Required Point Is : " + block_point + "\n";
                                                        alert_msg += "<div><?php echo $merchant_msg["edit-compaign"]["Msg_not_activate_campaign"]; ?></div>";
                                                    }
                                                    else
                                                    {

                                                        jQuery("#required_point").val(block_point);
                                                        var remain = purchase_point - block_point;
                                                        jQuery("#remaining_point").val(remain);
                                                    }
                                                }

                                                if (flag == "true")
                                                {

                                                    var flaglogin = 0;
                                                    jQuery.ajax({
                                                        type: "POST",
                                                        url: 'process.php',
                                                        data: 'loginornot=true',
                                                        async: false,
                                                        success: function (msg)
                                                        {


                                                            var obj = jQuery.parseJSON(msg);


                                                            if (obj.status == "false")
                                                            {
                                                                flaglogin = 1;
                                                                window.location.href = obj.link;

                                                            }
                                                            else if (obj.approve == 0)
                                                            {

                                                                var alert_msg = "Merchant Status: Blocked , Please contact Scanflip";
                                                                var head_msg = "<div class='head_msg'>Message</div>"
                                                                var content_msg = "<div class='content_msg'>" + alert_msg + "</div>";
                                                                var href = "<?php echo WEB_PATH; ?>/merchant/logout.php";
                                                                var footer_msg = "<div><hr><a href='" + href + "' class='msg_popup_cancel_anchor'><?php echo $merchant_msg['index']['fancybox_ok']; ?></a></div>";
                                                                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);


                                                                jQuery.fancybox({
                                                                    content: jQuery('#dialog-message').html(),
                                                                    type: 'html',
                                                                    openSpeed: 300,
                                                                    closeSpeed: 300,
                                                                    // topRatio: 0,

                                                                    changeFade: 'fast',
                                                                    helpers: {
                                                                        overlay: {
                                                                            opacity: 0.3
                                                                        } // overlay
                                                                    },
                                                                    afterClose: function () {
                                                                        window.location.href = href;
                                                                    }

                                                                });

                                                                flaglogin = 1;

                                                            }
                                                            else if (obj.approve == 2)
                                                            {
                                                                var alert_msg = "Merchant Status: Pending , Please contact Scanflip";
                                                                var head_msg = "<div class='head_msg'>Message</div>"
                                                                var content_msg = "<div class='content_msg'>" + alert_msg + "</div>";
                                                                var href = "<?php echo WEB_PATH; ?>/merchant/my-account.php";
                                                                var footer_msg = "<div><hr><a href='" + href + "' class='msg_popup_cancel_anchor'><?php echo $merchant_msg['index']['fancybox_ok']; ?></a></div>";
                                                                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);


                                                                jQuery.fancybox({
                                                                    content: jQuery('#dialog-message').html(),
                                                                    type: 'html',
                                                                    openSpeed: 300,
                                                                    closeSpeed: 300,
                                                                    // topRatio: 0,

                                                                    changeFade: 'fast',
                                                                    helpers: {
                                                                        overlay: {
                                                                            opacity: 0.3
                                                                        } // overlay
                                                                    },
                                                                    afterClose: function () {
                                                                        window.location.href = href;
                                                                    }

                                                                });

                                                                flaglogin = 1;

                                                            }
                                                            else
                                                            {
                                                                flaglogin = 0;
                                                            }


                                                        }

                                                    });

                                                    if (flaglogin == 1)
                                                    {
                                                        return false;
                                                    }
                                                    else
                                                    {
                                                        var alert_msg = "<?php echo $merchant_msg["index"]["saving_data"]; ?>";
                                                        var head_msg = "<div class='head_msg'>Message</div>";
                                                        var content_msg = "<div class='content_msg savingdata' style='background:white;'>" + alert_msg + "</div>";
                                                        jQuery("#NotificationloadermainContainer").html(content_msg);

                                                        return true;
                                                    }
                                                }
                                                else
                                                {
                                                    close_popup("Notificationloader");

                                                    var head_msg = "<div class='head_msg'>Message</div>"
                                                    var content_msg = "<div class='content_msg'>" + alert_msg + "</div>";
                                                    var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                                    jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);


                                                    jQuery.fancybox({
                                                        content: jQuery('#dialog-message').html(),
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
                                                    return false;
                                                }

                                            });

                                            jQuery("#popupcancel").live("click", function () {
                                                jQuery.fancybox.close();
                                                return false;
                                            });
                                    </script>
                                    <!--// 369-->
                                </td>
                            </tr>
                        </table>
                        <div class="clear">&nbsp;</div><!--end of content--></div>

                <!--end of contentContainer--></div>

            <!--start footer--><div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                ?>
                <!--end of footer--></div>
        </div>
        <!-- start of image upload popup div PAY-508-28033  -->
        <div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
            <div id="NotificationBackDiv" class="divBack">
            </div>
            <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">

                <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                     >

                    <div class="modal-close-button" style="visibility: visible;">

                        <a  tabindex="0" onclick="close_popup('Notification');" id="fancybox-close" style="display: inline;"></a>
                    </div>
                    <div id="NotificationmainContainer" class="innerContainer" style="height:330px;width:600px">
                        <div class="main_content"> 	
                            <div style=" background: none repeat scroll 0 0 #222222;color: #CFCFCF !important;padding: 4px;border-radius: 10px 10px 0 0;">
                                <font style="font-family: Arial,Helvetica,sans-serif;font-size: 22px;font-weight: bold;letter-spacing: 1px;line-height: 24px;margin: 0;padding: 0 0 2px;text-shadow:1px 1px 1px #DCAAA1">
                                <?php echo $merchant_msg["edit-compaign"]["Field_add_campaign_logo"]; ?>
                                </font>
                            </div>
                            <div class="message-box message-success" id="jqReviewHelpfulMessageNotification" style="display: block;height:30px;">
                                <!-- -->
                                <div id="media-upload-header">
                                    <ul id="sidemenu">
                                        <li id="tab-type" class="tab_from_library"><a class="current" ><?php echo $merchant_msg["edit-compaign"]["Field_media_library"]; ?></a></li>

                                    </ul>
                                </div>
                                <!-- -->


                                <div style="clear: both" ></div>
                                <div style="display: none;padding-left: 13px; padding-right: 13px;" class="div_from_computer">
                                    <div  style="padding-top:10px;padding-bottom:10px">Add media from your computer

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
                                        <input class="save_btn" type="button" name="btn_save_from_computer" id="btn_save_from_computer" onclick="save_from_computer()"  value="<?php echo $merchant_msg['index']['btn_save']; ?>"/>
                                    </div>
                                </div>
                                <div style="display:block;padding-left: 13px; padding-right: 13px;" class="div_from_library">
                                    <div  style="padding-top:10px;padding-bottom:10px"><?php echo $merchant_msg["edit-compaign"]["Field_add_campaign_logo_media_library"]; ?></div>
                                    <?php
                                    $flag = true;
                                    $merchant_array = array();
                                    $merchant_array['id'] = $_SESSION['merchant_id'];
                                    $merchant_info = $objDB->Show("merchant_user", $merchant_array);
                                    if ($merchant_info->fields['merchant_parent'] != 0) {

                                            $media_acc_array = array();
                                            $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
                                            ;
                                            $RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
                                            $media_val = unserialize($RSmedia->fields['media_access']);
                                            if (in_array("view-use", $media_val)) {
                                                    $flag = true;
                                            } else {
                                                    $flag = false;
                                            }
                                    } else {
                                            $flag = true;
                                    }

                                    if ($flag) {
                                            ?>
                                            <div id="media_library_listing" style="width:100%;height:180px;border:1px dashed #000;overflow:auto;">
                                                <div style="clear: both"></div>
                                                <ul class="ul_image_list">
                                                    <?php
                                                    /* $query = "select * from merchant_media where image_type='campaign' and (merchant_id=".$_SESSION['merchant_id'] ." or merchant_id=".$merchant_info->fields['merchant_parent'].") order by id desc" ;
                                                      $RSImages = $objDB->execute_query($query); */
                                                    $RSImages = $objDB->Conn->Execute("select * from merchant_media where image_type=? and (merchant_id=? or merchant_id=?) order by id desc", array('campaign', $_SESSION['merchant_id'], $merchant_info->fields['merchant_parent']));

                                                    if ($RSImages->RecordCount() > 0) {
                                                            while ($Row = $RSImages->FetchRow()) {
                                                                    ?>


                                                                    <li class="li_image_list" id="li_img_<?= $Row['id']; ?>">
                                                                        <div>
                                                                            <img src="<?php echo ASSETS_IMG . '/m/campaign/' . $Row['image']; ?>" height="50px" width="50px" />
                                                                            <span style="vertical-align: top" id="span_img_text_<?= $Row['id']; ?>"><?= $Row['image'] ?></span>
                                                                            <span style="vertical-align: top;float: right"> Use this image&nbsp;<input type="radio" name="use_image" value="<?= $Row['id'] ?>" /></span>
                                                                        </div>

                                                                    </li>
                                                                    <?php
                                                            }
                                                    }
                                                    ?>
                                                </ul>

                                            </div>
                                            <div  align="center" style="padding-top:10px">
                                                <input type="button" class="save_btn" name="btn_save_from_library" id="btn_save_from_library" onclick="save_from_library()"  value="<?php echo $merchant_msg['index']['btn_save']; ?>"/>
                                            </div>
                                        </div>
                                        <?php
                                } else {
                                        ?>
                                        <div  style="padding-top:10px;padding-bottom:10px">
                                            You don't have access to use media library images.
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
        echo file_get_contents(WEB_PATH . '/merchant/import_media_library.php?mer_id=' . $_SESSION['merchant_id'] . '&img_type=campaign&start_index=0');
        ?>
    </form>
    <!-- end of popup div  PAY-508-28033 -->
    <script>

            jQuery("#show_more_mediya_browse").live("click", function () {

                var cur_el = jQuery(this);
                var next_index = parseInt(jQuery(this).attr('next_index'));
                var num_of_records = parseInt(jQuery(this).attr('num_of_records'));

                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'show_more_media_browse=yes&next_index=' + next_index + '&num_of_records=' + num_of_records + "&img_type=campaign",
                    async: true,
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                        //alert(obj.status);
                        jQuery(".fancybox-inner .ul_image_list").append(obj.html);
                        cur_el.attr('next_index', next_index + num_of_records);
                        if (parseInt(obj.total_records) < num_of_records)
                        {
                            cur_el.css("display", "none");
                        }
                    }
                });

            });

            jQuery(".ul_image_list li").live("click", function () {

                jQuery(".useradioclass").prop("checked", false);
                var imgid = jQuery(this).attr("id").split("img_");
                imgid = imgid[1];
                //alert(imgid);
                jQuery(this).find(".useradioclass").prop("checked", true);

                jQuery(".ul_image_list li").removeClass("current");
                jQuery(this).addClass("current");

                jQuery(".fancybox-inner .useradioclass").each(function () {

                    if (jQuery(".fancybox-inner .useradioclass").is(":checked"))
                    {

                        jQuery(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
                        jQuery(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
                        jQuery(".fancybox-inner #btn_save_from_library").css("background-color", "#3C99F4 !important");
                    }
                    else
                    {
                        jQuery(".fancybox-inner #btn_save_from_library").attr("disabled", true);
                        jQuery(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
                        jQuery(".fancybox-inner #btn_save_from_library").css("background-color", "#ABABAB !important");
                    }

                });


            });

            function check_is_walkin(val)
            {
                $av = jQuery.noConflict();

                if (val == 0)
                {
                    $av("#group").css("display", "");
                }
                else
                {
                    $av("#group").css("display", "none");
                    $av("#one_per_customer").attr("checked", "checked");
                }


            }
            /* start of script for PAY-508-28033*/
            function save_from_library()
            {
                $av = jQuery.noConflict();
                var sel_val = $av('input[name=use_image]:checked').val();
                //alert(sel_val);
                $av("#hdn_image_id").val(sel_val);

                var sel_src = $av(".fancybox-inner #li_img_" + sel_val + " span[id=span_img_text_" + sel_val + "]").text();

                //alert(sel_src);
                $av("#hdn_image_path").val(sel_src);
                /* NPE-252-19046 */

                /* NPE-252-19046 */
                file_path = "";
                jQuery.fancybox.close();
                var img = "<img src='<?= ASSETS_IMG ?>/m/campaign/" + sel_src + "' class='displayimg'>";

                $av('#files').html(img + "<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
            }
            function rm_image()
            {
                $rm = jQuery.noConflict();
                $rm("#hdn_image_path").val("");
                $rm("#hdn_image_id").val("");
                $rm('#files').html("");

            }
            function rm_image_permanent(id)
            {
                $rm = jQuery.noConflict();

                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'is_image_delete=yes&image_type=campaign&filename=' + id,
                    async: false,
                    success: function (msg)
                    {
                        $rm("#hdn_image_path").val("");
                        $rm("#hdn_image_id").val("");
                        $rm('#files').html("");
                    }

                });

            }

            function save_from_computer()
            {
                $as = jQuery.noConflict();
                $as("#hdn_image_path").val(file_path);
                $as("#hdn_image_id").val("");
                /* NPE-252-19046 */

                /* NPE-252-19046 */
                close_popup('Notification');
                var img = "<img src='<?= ASSETS_IMG ?>/m/campaign/" + file_path + "' class='displayimg'>";
                $as('#files').html(img + "<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' id='" + file_path + "' class='cancel_remove_image' onclick='rm_image_permanent(this.id)' /></div></div></div>");
            }
            $atab = jQuery.noConflict();
            /* NPE-252-19046 */
            $atab(document).ready(function () {
                if ($atab("#hdn_image_path").val() != "")
                {
                    var img = "<img src='<?= ASSETS_IMG ?>/m/campaign/" + $atab("#hdn_image_path").val() + "' class='displayimg'>";

                    if ($atab("#hdn_image_path").val().startsWith("media"))
                    {
                        $atab('#files').html(img + "<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
                        //alert("media");
                    }
                    else
                    {
                        $atab('#files').html(img + "<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?= ASSETS_IMG ?>/m/mediya_delete.png'  class='cancel_remove_image' onclick='rm_image_permanent(this.id)' /></div></div></div>");
                        //alert("other");
                    }
                }
                var original_description = $atab("#description").text();
                var original_category = $atab("#category_id").val();
                var original_image = $atab("#hdn_image_path").val();

                $atab(".tempate_preview_image").click(function () {

                    //alert($atab(this).next().next().val());

                    var tempEle = $atab(this).next().next();
                    tempEle.trigger("click");

                });

                $atab("input:radio[name=template_value]").click(function () {
                    var t_val = $atab(this).attr("value");
                    $atab.ajax({
                        type: "POST",
                        url: "<?= WEB_PATH ?>/merchant/load_template.php",
                        data: "template_id=" + t_val,
                        success: function (msg) {
                            //alert($atab('#template_id').val());
                            if (t_val == "0")
                            {
                                atab("#deal_detail_description").text("");
                                tinyMCE.activeEditor.setContent(original_description);

                                var sel_val = $atab('input[name=use_image]:checked').val();
                                var sel_src = $atab("#li_img_" + sel_val + " span[id=span_img_text_" + sel_val + "]").text();
                                //alert(sel_src );
                                $atab("#category_id").val(original_category);
                                if (sel_src == "")
                                {
                                    $atab("#hdn_image_path").val(sel_src);
                                } else {

                                    $atab("#hdn_image_path").val(original_image);
                                }
                                $atab("#img_div").css("display", "none");
                            } else {
                                var obj = eval('(' + msg + ')');

                                if (obj['fields'].business_logo != "")
                                {
                                    var img_businesslog = "<?= ASSETS_IMG ?>/m/campaign/" + obj['fields'].business_logo;
                                }
                                else
                                {
                                    var img_businesslog = "<?= ASSETS_IMG ?>/m/campaign/Merchant_Offer.png";
                                }
                                var img = "<img src='" + img_businesslog + "' class='displayimg'>";
                                $atab('#files').html(img + "<br/><div style='display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' class='cancel_remove_image' onclick='rm_image()' /></div></div></div>");
                                tinyMCE.activeEditor.setContent(obj['fields'].description);
                                $atab("#deal_detail_description").text(obj['fields'].print_coupon_description);
                                //$atab("#discount").val(obj['fields'].discount);
                                //                                        $atab("#description").text(obj['fields'].description);
                                $atab("#title").val(obj['fields'].title);
                                $atab("#category_id").val(obj['fields'].category_id);
                                $atab("#hdn_image_path").val(obj['fields'].business_logo);
                                $atab("#img_div").css("display", "block");
                            }
                        }
                    });

                });
                jQuery('input:radio[name="number_of_use"]').change(function () {
                    //alert(jQuery(this).parent().parent().next().html());
                    //alert(jQuery(this).val());
                    if (jQuery(this).val() == "2" || jQuery(this).val() == "3")
                    {
                        jQuery("#new_customer_only").prop("checked", false);
                        jQuery(this).parent().parent().next().css('display', 'none');
                    }
                    else
                    {
                        jQuery(this).parent().parent().next().css('display', 'table-row');
                    }
                });
                //jQuery('input:radio[id="one_per_customer"]').prop("checked",true);


            });
            $atab(function () {
                var btnUpload = $atab('#upload');
                var status = $atab('#status');

                new AjaxUpload(btnUpload, {
                    action: 'merchant_media_upload.php?doAction=FileUpload&img_type=campaign',
                    name: 'uploadfile',
                    onSubmit: function (file, ext) {
                        if ($atab('#files').children().length > 0)
                        {
                            $atab('#files li').detach();
                        }
                        if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                            // extension is not allowed 
                            status.text('Only JPG, PNG or GIF files are allowed');
                            return false;
                        }
                        status.text('Uploading...');
                    },
                    onComplete: function (file, response) {

                        var arr = response.split("|");
                        if (arr[1] == "small")
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
            /* NPE-252-19046 */
            $atab(".tab_from_library a").click(function () {
                $atab("#sidemenu li a").each(function () {
                    $atab(this).removeClass("current");
                });
                $atab(this).addClass("current");
                $atab(".div_from_library").css("display", "block");
                $atab(".div_from_computer").css("display", "none");
            });
            $atab(".tab_from_computer a").click(function () {
                $atab("#sidemenu li a").each(function () {
                    $atab(this).removeClass("current");
                });
                $atab(this).addClass("current");
                $atab(".div_from_library").css("display", "none");
                $atab(".div_from_computer").css("display", "block");
            });
            function close_popup(popup_name)
            {
                $ac = jQuery.noConflict();
                $ac("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
                    $ac("#" + popup_name + "BackDiv").fadeOut(200, function () {
                        $ac("#" + popup_name + "PopUpContainer").fadeOut(200, function () {
                            $ac("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
                            $ac("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
                            $ac("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
                        });
                    });
                });

            }
            function open_popup(popup_name)
            {
                $ao = jQuery.noConflict();
                if ($ao("#hdn_image_id").val() != "")
                {
                    $ao('input[name=use_image][value=' + $ao("#hdn_image_id").val() + ']').attr("checked", "checked");
                }
                $ao("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
                    $ao("#" + popup_name + "BackDiv").fadeIn(200, function () {
                        $ao("#" + popup_name + "PopUpContainer").fadeIn(200, function () {

                        });
                    });
                });


            }
            /* end of script for PAY-508-28033*/

    </script>
    <?
    $_SESSION['msg'] = "";
    ?>
</body>
</html>
<script language="javascript">

// 369
        $abc = jQuery.noConflict();
        $abc(document).ready(function () {

            jQuery("#one_per_customer").trigger("click");
            // bind form using ajaxForm 
            $abc('#edit_campaign_form').ajaxForm({
                dataType: 'json',
                success: processEditCampJson
            });

            function processEditCampJson(data) {
                if (data.status == "true") {

                    window.location.href = '<?= WEB_PATH . '/merchant/compaigns.php?action=active' ?>';
                }
                else
                {
                    //alert(data.message);
                    var head_msg = "<div class='head_msg'>Message</div>";
                    var content_msg = "<div class='content_msg'>" + data.message + "</div>";
                    var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";

                    jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                    jQuery.fancybox({
                        content: jQuery('#dialog-message').html(),
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

                }

            }

        });

// 369
//$abc = jQuery.noConflict();
        $abc(".camp_location").click(function () {

            t_val = $abc(this).val();
            if ($abc(this).is(':checked'))
            {
                if ($abc(".chk_private").is(':checked'))
                {
                    var private_flag = 1;
                }
                else
                {
                    var private_flag = 0;
                }
                $abc.ajax({
                    type: "POST",
                    url: "<?= WEB_PATH ?>/merchant/process.php",
                    data: "location_id=" + t_val + "&getlocationwisegroup=yes&public=" + private_flag,
                    success: function (msg) {
                        $abc(".location_listing_area").append(msg);

                        //19-09-2013

                        if ($abc(".chk_private").is(':checked'))
                        {

                            $abc("ul[id^='locationgroup']").each(function (index) {
                                //alert("hi");
                                $abc(this).find("li:not(:first)").css("display", "none");
                            });
                        }
                        else
                        {

                            $abc("ul[id^='locationgroup']").each(function (index) {

                                $abc(this).find("li:not(:first)").css("display", "block");
                            });
                        }

                    }
                });
            }
            else
            {
                $abc("#locationid_" + $abc(this).val()).detach();
            }
        });
        $abc(".chk_private").click(function () {
            if ($abc(this).is(':checked'))
            {
                $abc("input[type=checkbox][private_group]").each(function () {
                    $abc(this).attr("checked", "checked");
                    $abc(this).attr("disabled", false);

                });
                // 8-8-2013
                $abc("input[type=checkbox][class=other_group]").each(function () {
                    //$abc(this).attr("checked","unchecked") ;
                    $abc(this).prop('checked', false);
                    $abc(this).attr("disabled", true);
                });
                jQuery("input[id^='hdn_campaign_type_']").val("1");
                jQuery("input[id^='hdn_campaign_type1_']").val("1");

                jQuery("ul[id^='locationgroup']").each(function (index) {
                    //alert("hi");
                    jQuery(this).find("li:not(:first)").css("display", "none");
                });

                //19-09-2013
            }
            else
            {
                $abc("input[type=checkbox][private_group]").each(function () {
                    $abc(this).removeAttr("checked");
                    $abc(this).attr("disabled", false);
                });
                $abc("input[type=checkbox][class=other_group]").each(function () {

                    $abc(this).attr("disabled", false);
                });
                jQuery("input[id^='hdn_campaign_type_']").val("0");
                jQuery("input[id^='hdn_campaign_type1_']").val("0");

                jQuery("ul[id^='locationgroup']").each(function (index) {
                    //alert("hi");
                    jQuery(this).find("li:not(:first)").css("display", "block");
                });

            }
        });

// 8-8-2013
        $abc(".other_group").live("click", function () {
            var locationidname = $abc(this).parent().parent().attr("id").split("_");
            var locationid = locationidname[1];
            var oldvalue = jQuery("#hdn_campaign_type1_" + locationid).val();

            var is_checkboxval = parseInt(oldvalue) + 1;
            var is_not_checkboxval = parseInt(oldvalue) - 1;
            if ($abc(this).is(':checked'))
            {

                jQuery("#hdn_campaign_type1_" + locationid).val(is_checkboxval);
            }
            else
            {
                jQuery("#hdn_campaign_type1_" + locationid).val(is_not_checkboxval);
            }


            if (jQuery("#hdn_campaign_type1_" + locationid).val() == 0)
            {
                $abc(this).parent().parent().find(".private_group").attr("disabled", false);
            }
            else
            {
                $abc(this).parent().parent().find(".private_group").attr("disabled", true);
            }

            if ($abc(this).parent().parent().find(".private_group").is(':checked'))
            {
                //return false;
            }
            else
            {
                jQuery("#hdn_campaign_type_" + locationid).val("3");
            }
        });

        $abc(".private_group").live("click", function () {
            var locationidname = $abc(this).parent().parent().attr("id").split("_");

            var locationid = locationidname[1];

            if ($abc(".chk_private").is(':checked'))
            {
                if ($abc(this).is(':checked'))
                {
                    jQuery("#hdn_campaign_type1_" + locationid).val("1");
                    jQuery("#hdn_campaign_type_" + locationid).val("2");
                    $abc("#locationgroup_" + locationid + " .other_group").attr("disabled", true);
                }
                else
                {

                    return false;
                    jQuery("#hdn_campaign_type1_" + locationid).val("0");

                }
            }
            else
            {

                if ($abc(this).is(':checked'))
                {
                    jQuery("#hdn_campaign_type1_" + locationid).val("1");
                    jQuery("#hdn_campaign_type_" + locationid).val("2");
                    $abc("#locationgroup_" + locationid + " .other_group").attr("disabled", true);
                }
                else
                {


                    jQuery("#hdn_campaign_type1_" + locationid).val("0");


                    $abc("#locationgroup_" + locationid + " .other_group").attr("disabled", false);

                }
            }

        })

// 8-8-2013
        function changetext1() {
            var v = 76 - jQuery("#title").val().length;
            jQuery(".span_c1").text(v + " characters remaining");
        }
        function changetext2() {
            var v = 8 - jQuery("#discount").val().length;
            jQuery(".span_c2").text(v + " characters remaining");
        }
        function changetextpcd() {
            var v = 300;//- jQuery("#deal_detail_description").val().length;
            //jQuery(".span_pcd").text(v+" characters remaining"); 
        }
        changetext1();

        changetextpcd();
        function  getcancelnow()
        {
            jQuery.fancybox.close();
        }

        jQuery('.fancybox-inner input#txt_price').live("keyup", function (e) {
            if (e.keyCode == '13')
            {
                //alert("if");
                //e.preventDefault();
                //$abc("#popupcancel").focus();
                jQuery.fancybox.close();
            }
            else
            {
                var numbers = /^[0-9]+$/;
                if ($abc(".fancybox-inner #txt_price").val() == "")
                {
                    jQuery(".fancybox-inner .purchasepointclass").html("");
                }
                else if ($abc(".fancybox-inner #txt_price").val().match(numbers))
                {
                    $abc.ajax({
                        type: "POST",
                        url: "<?= WEB_PATH ?>/merchant/process.php",
                        data: "txt_price=" + $abc(".fancybox-inner #txt_price").val() + "&getpoints=yes",
                        success: function (msg)
                        {

                            $abc(".fancybox-inner #span_con_point").text(msg);
                            $abc(".fancybox-inner .success_msg").text("");

                        }
                    });
                }
                else
                {

                }
            }
        });
        function getpointvalue1()
        {
            alert("hi");
            var numbers = /^[0-9]+$/;
            if ($abc("#txt_price").val() == "")
            {

            }
            else if ($abc("#txt_price").val().match(numbers))
            {
                $abc.ajax({
                    type: "POST",
                    url: "<?= WEB_PATH ?>/merchant/process.php",
                    data: "txt_price=" + $abc("#txt_price").val() + "&getpoints=yes",
                    success: function (msg)
                    {

                        $abc("#span_con_point").text(msg);
                        $abc(".success_msg").text("");

                    }
                });
            }
            else
            {
                var content_msg = "<div class='table_errore_message'><?php echo $merchant_msg['edit-compaign']['please_enter_correct_amount']; ?></div>";
                //var footer_msg="<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";

                jQuery("#dialog-message").html(content_msg);
                jQuery("#dialog-message").show();

                $abc("#span_con_point").text("");
                $abc(".success_msg").text("");
            }
        }
</script>
<!-- start datepicker -->
<script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/m/jquery.ui.core.js"></script>

<script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/m/jquery.ui.datepicker.js"></script>
<script type="text/javascript" language="javascript" src="<?= ASSETS_JS ?>/m/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/jquery.ui.datepicker.css">
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/jquery.ui.theme.css">

<!-- end datepicker -->
<script type="text/javascript">
        jQuery("a#compaigns").css("background-color", "orange");
        jQuery("#expiration_date").datetimepicker({onSelect: function () {
                //jQuery('#expiration_date').datepicker('option', "dateFormat", "yy-mm-dd" );
            }});

        jQuery("#start_date").datetimepicker({onSelect: function () {
                var date = jQuery("#start_date").datepicker('getDate');

                var actualDate = new Date(date);

                var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate() + 30);
                jQuery('#expiration_date').datepicker('option', {
                    minDate: jQuery(this).datepicker('getDate'),
                    //maxDate : newDate
                });

            }});

        jQuery("#walkinid").click(function () {
            if (jQuery('#walkinid').is(':checked'))
            {
                jQuery("#one_per_customer_per_day").attr("disabled", "disabled");
                jQuery("#multiple_use").attr("disabled", "disabled");
            }

        });
        jQuery("#groupid").click(function () {
            if (jQuery('#groupid').is(':checked'))
            {
                jQuery("#one_per_customer_per_day").removeAttr("disabled");
                jQuery("#multiple_use").removeAttr("disabled");
            }

        });
        jQuery(".p_btn1").click(function () {
            jQuery.fancybox({
                content: jQuery('#point_block').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                // topRatio: 0,

                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });

        });

        jQuery('.mediaclass').click(function () {
            jQuery.fancybox({
                content: jQuery('#mediablock').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                // topRatio: 0,

                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });

        });

        jQuery(".fancybox-inner .useradioclass").live("change", function () {

            jQuery(".fancybox-inner .useradioclass").each(function () {

                if (jQuery(".fancybox-inner .useradioclass").is(":checked"))
                {

                    jQuery(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
                    jQuery(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
                    jQuery(".fancybox-inner #btn_save_from_library").css("background-color", "#3C99F4 !important");
                }
                else
                {
                    jQuery(".fancybox-inner #btn_save_from_library").attr("disabled", true);
                    jQuery(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
                    jQuery(".fancybox-inner #btn_save_from_library").css("background-color", "#ABABAB !important");
                }
            })
        });

        jQuery('.notification_tooltip').tooltip({
            content: function () {
                return jQuery(this).attr('title');
            },
            track: true,
            delay: 0,
            showURL: false,
            showBody: "<br>",
            fade: 250
        });

</script>
<script type="text/javascript">
        jQuery.noConflict();
        jQuery(document).ready(function () {
            //jQuery('.list_carousel').css("height","145px");

            if (jQuery('#foo2').length)
            {
                jQuery('#foo2').carouFredSel({
                    auto: false,
                    prev: '#prev2',
                    next: '#next2',
                    pagination: "#pager2",
                    mousewheel: true,
                    swipe: {
                        onMouse: true,
                        onTouch: true
                    }
                });
                jQuery('.list_carousel').css("overflow", "inherit");
            }
        });

        jQuery("#discount").blur(function () {
            jQuery("#discount").val(jQuery("#discount").val().trim());
            var deal_value = jQuery("#deal_value").val();
            var discount = jQuery("#discount").val();


            var n = deal_value.indexOf("$");
            if (n != "-1")
            {
                //alert("found");
                deal_value = deal_value.substring(n + 1, deal_value.length);
            }
            //alert(deal_value);

            if (deal_value != "0")
            {
                if (discount != "" && discount != "NaN")
                {
                    var saving = (parseFloat(deal_value) * parseFloat(discount)) / 100;
                    if (isNaN(saving))
                    {
                        jQuery("#saving").val("");
                    }
                    else
                    {
                        jQuery("#saving").val(Math.round(100 * saving) / 100);
                    }
                }
            }
        });
        jQuery("#saving").blur(function () {
            jQuery("#saving").val(jQuery("#saving").val().trim());
            var deal_value = jQuery("#deal_value").val();
            var saving = jQuery("#saving").val();


            var n = deal_value.indexOf("$");
            if (n != "-1")
            {
                //alert("found");
                deal_value = deal_value.substring(n + 1, deal_value.length);
            }
            //alert(deal_value);

            if (deal_value != "0")
            {
                if (saving != "" && saving != "NaN")
                {
                    var discount = (parseFloat(saving) * 100) / parseFloat(deal_value);
                    if (isNaN(discount))
                    {
                        jQuery("#discount").val("");
                    }
                    else
                    {
                        jQuery("#discount").val(Math.round(100 * discount) / 100);
                    }
                }
            }
        });
        jQuery("#deal_value").blur(function () {
            jQuery("#deal_value").val(jQuery("#deal_value").val().trim());
            var deal_value = jQuery("#deal_value").val();
            var discount = jQuery("#discount").val();
            var saving = jQuery("#saving").val();

            var n = deal_value.indexOf("$");
            if (n != "-1")
            {
                //alert("found");
                deal_value = deal_value.substring(n + 1, deal_value.length);
            }
            //alert(deal_value);

            if (deal_value == "0")
            {
                jQuery("#discount").val("0");
                jQuery("#saving").val("0");
            }
            if (deal_value != "" && deal_value != "NaN")
            {
                if (discount != "" && discount != "NaN")
                {
                    var saving = (parseFloat(deal_value) * parseFloat(discount)) / 100;
                    if (isNaN(saving))
                    {
                        jQuery("#saving").val("");
                    }
                    else
                    {
                        jQuery("#saving").val(Math.round(100 * saving) / 100);
                    }
                }

                if (saving != "" && saving != "NaN")
                {
                    var discount = (parseFloat(saving) * 100) / parseFloat(deal_value);
                    if (isNaN(discount))
                    {
                        jQuery("#discount").val("");
                    }
                    else
                    {
                        jQuery("#discount").val(Math.round(100 * discount) / 100);
                    }
                }
            }

            if (deal_value != "")
            {
                jQuery("#tr_discount").css("display", "table-row");
                jQuery("#tr_saving").css("display", "table-row");
                jQuery("#discount").focus();
            }
            else
            {
                jQuery("#tr_discount").css("display", "none");
                jQuery("#tr_saving").css("display", "none");
                jQuery("#deal_value").focus();
            }

        });
        /*************** ****************************/
        jQuery(document).ready(function () {
            function add() {
                if (jQuery(this).val() === '') {
                    jQuery(this).val(jQuery(this).attr('placeholder')).addClass('placeholder');
                }
            }

            function remove() {
                if (jQuery(this).val() === jQuery(this).attr('placeholder')) {
                    jQuery(this).val('').removeClass('placeholder');
                }
            }

            // Create a dummy element for feature detection
            if (!('placeholder' in jQuery('<input>')[0])) {

                // Select the elements that have a placeholder attribute
                jQuery('input[placeholder], textarea[placeholder]').blur(add).focus(remove).each(add);

                // Remove the placeholder text before the form is submitted
                jQuery('form').submit(function () {
                    jQuery(this).find('input[placeholder], textarea[placeholder]').each(remove);
                });
            }
        });
</script>
<div class="validating_data" style="display:none;">Validating data, please wait...</div> 
