<?php
/**
 * @uses add new reward zone campaigns
 * @used in pages : manage-reward-zone.php
 */
check_merchant_session();

$array_where_act['active'] = 1;
$RSCat = $objDB->Show("categories", $array_where_act);
$array = array();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Manage Reward Zone Campaigns</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
       <!-- <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/main.css"> -->

        <script language="javascript" src="<?= ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
        <!-- T_7 -->
        <!--// 369-->
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/jquery.form.js"></script>
        <!--// 369-->
        <!--<script type="text/javascript" src="<?= ASSETS ?>/tinymce/tiny_mce.js"></script>-->
        <script src="<?= ASSETS ?>/tinymce4/tinymce.min.js"></script>
        <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
        <script language="javascript">

                jQuery(document).ready(function () {
                    window.tinymce.dom.Event.domLoaded = true;
                    tinyMCE.init({
                        // General options
                        //mode : "textareas",
                        mode: "exact",
                        elements: "description,terms_condition",
                        //theme: "modern",
                        plugins: [
                            "advlist autolink lists link charmap searchreplace "
                        ],
                        toolbar1: "searchreplace | undo redo | bullist numlist | subscript superscript | charmap | link ",
                        menubar: false,
                        toolbar_items_size: 'small',
                        charLimit: 1200,
                        setup: function (ed) {
                            ed.on('keydown', function (e) {

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
                                //alert(tinylen);
                                if (tinylen + 1 > tinymax && e.keyCode != 8) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return false;
                                }

                            })

                            ed.on('keyup', function (e) {

                                var textarea = tinyMCE.activeEditor.getContent();
                                //alert(textarea);
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
                                    document.getElementById("desc_limit").innerHTML = l + " characters remaining";
                                if (tinyMCE.activeEditor.id == "terms_condition")
                                    document.getElementById("terms_limit").innerHTML = l + " characters remaining";
                            });
                        }
                    });
                    jQuery(".textarea_loader").css("display", "none");
                    jQuery(".textarea_container").css("display", "block");
                });
                //  alert(jQuery("#description_parent").length);
                //jQuery("#description_parent").css("float",'left');
        </script>

        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.min.js"></script>
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

    </head>

    <body>
        <div id="dialog-message" title="Message Box" style="display:none">

        </div>
        <div >
            <!--start header--><div>

                <?php
                require_once(MRCH_LAYOUT . "/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">
                <div id="content">   <div class="title_header"><?php echo $merchant_msg["edit-compaign"]["Field_add_campaign"]; ?></div>

                    <!--// 369--><form action="process.php" method="post" enctype="multipart/form-data" id="add_rew_campaign_form">

                        <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="" />
                        <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="" />
                        <table width="100%"  border="0" id="table_spacer">
                                          <!--<tr>
                                            <td colspan="2" align="center" style="color:#FF0000; "><?= $_SESSION['msg'] ?></td>
                                      </tr>-->
                            <tr>
                                <td width="21%" align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_campaign_title"]; ?>
                                </td>
                                <td width="79%" align="left"><input type="text" placeholder="Required" name="title" id="title" maxlength="76" size="50" value=""/><span class="span_c1" >Maximum 76 characters | No HTML allowed</span></td>  
                            </tr>
                            <tr>
                                <td width="21%" align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_campaign_tag"]; ?>
                                </td>
                                <td width="79%" align="left"><input type="text" placeholder="Enter upto 3 keywords separated by commas " name="campaign_tag" id="campaign_tag"   size="50" value=""  /> <span data-toggle="tooltip" data-placement="right" class="notification_tooltip"  data-toggle="tooltip"  title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_Campaign_Tag"]; ?>">&nbsp;&nbsp;&nbsp;</span></td>  
                            </tr>

                            <tr>
                                <td align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_deal_value"]; ?>
                                </td>
                                <td align="left">
                                    <input type="text" name="deal_value" id="deal_value" placeholder="00" size="25"/><span class="notification_tooltip"  data-toggle="tooltip" data-placement="right" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_deal_value"]; ?>" >&nbsp;&nbsp;&nbsp</span><span id="numeric">numeric, no currency symbol</span>
                                </td>
                            </tr> 
                            <tr>
                                <td width="21%" align="right"><?php echo $merchant_msg["edit-compaign"]["Field_deal_discount"]; ?> </td>
                                <td width="79%" align="left">
                                    <input type="text" name="campaign_disc_value" id="campaign_disc_value" placeholder="Optional">
                                    <select id="is_per" name="is_per">
                                        <option value="per">% OFF</option>
                                        <option value="curr">$ OFF</option>
                                    </select>
                                </td>


                                <!-- end of NPE-252-19046 -->
                            <tr>
                                <td align="right">
                                    <?php echo $merchant_msg["edit-compaign"]["Field_category"]; ?>
                                </td>
                                <td align="left">
                                    <select name="category_id" id="category_id" required="">
                                        <option value="0">Select Category</option>
                                        <?php
                                        while ($Row = $RSCat->FetchRow()) {
                                                ?>
                                                <option value="<?= $Row['id'] ?>"><?= $Row['cat_name'] ?></option>
                                                <?php
                                        }
                                        ?>
                                    </select><span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_category"]; ?>" >&nbsp;&nbsp;&nbsp </span>
                                </td>
                            </tr>
                            <tr>
                                <td align="right"><?php echo $merchant_msg["edit-compaign"]["Field_campaign_logo"]; ?></td>
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
                                        <span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["tooltip_upload_image"]; ?>" >&nbsp;&nbsp;&nbsp </span></div> 
                                    <!-- end of  PAY-508-28033   -->
                                </td>
                            </tr>
                            <!-- T_7 -->
                            <tr><td align="right">&nbsp; </td>
                                <td>

                                    <div id="status" ></div>
                                    <ul id="files" class="pull-left">

                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td align="right"><?php echo $merchant_msg["edit-compaign"]["Field_deal_description"]; ?>
                                    <span  data-toggle="tooltip" data-placement="right" class="notification_tooltip deal_desc_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_campaign_web_description"]; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
                                <td align="left" class="textare_td" >
                                    <div align="center" class="textarea_loader">
                                        <img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="defaul_table_loader" />
                                    </div>
                                    <div class="textarea_container" style="display: none;">	
                                        <textarea id="description" name="description" rows="15" cols="80" class="table_th_90"></textarea>
                                    </div>
                                    <span id="desc_limit">Maximum 1200 characters | no HTML allowed</span>
                                </td>
                            </tr>

                            <tr>
                                <td align="right"><?php echo $merchant_msg["edit-compaign"]["Field_terms_condition"]; ?> 
                                    <span  data-toggle="tooltip" data-placement="right"  class="notification_tooltip" title="<?php echo $merchant_msg["edit-compaign"]["Tooltip_terms_condition"]; ?>">&nbsp;&nbsp;&nbsp;</span>: </td>
                                <td align="left" class="textare_td">
                                    <div align="center" class="textarea_loader">
                                        <img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="defaul_table_loader" />
                                    </div>
                                    <div class="textarea_container" style="display: none;">	

                                        <textarea id="terms_condition" name="terms_condition" rows="15" cols="80" class="table_th_90"></textarea>
                                    </div>
                                    <span id="terms_limit">Maximum 1200 characters | no HTML allowed</span>
                                </td>
                            </tr>

                            <tr>
                                <td>&nbsp;</td>
                                <td align="left">
                                    <input type="submit" name="btnRewardAddCampaigns" id="btnRewardAddCampaigns" value="<?php echo $merchant_msg['index']['btn_save']; ?>" >
                                    <input type="button" name="btnCanCampaigns" value="<?php echo $merchant_msg['index']['btn_cancel']; ?>" onclick="window.location.href = '<?php echo WEB_PATH; ?>/merchant/manage-reward-zone.php';">
                                </td>
                            </tr>
                        </table>
                    </form>
                    <div class="clear">&nbsp;</div>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>

            <!---------start footer--------------->
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                ?>
            </div>
        </div>
        <?php
        echo file_get_contents(WEB_PATH . '/merchant/import_media_library.php?mer_id=' . $_SESSION['merchant_id'] . '&img_type=campaign&start_index=0');

        $_SESSION['msg'] = "";
        ?>
        <!--- tooltip css --->
        <div class="validating_data" style="display:none;">Validating data, please wait...</div> 
        <script type="text/javascript">
                $(document).ready(function () {

                    $("#add_rew_campaign_form").validate({
                        rules: {
                            title: {
                                required: true,
                                maxlength: 76
                            },
                            campaign_tag: {
                                required: true,
                                keyword_length: true
//regex: /^[a-zA-Z'.\s]{1,40}$/
                            },
                            deal_value: {
                                required: true,
                                number: true,
                            },
                            category_id: {
                                cat: true

                            },
                            description: {
                                required: true,
                                maxlength: 1200

                            }
                        },
                        messages: {
                            title: {
                                required: "Please enter a gift card title",
                                maxlength: "Maximum 76 characters | No HTML allowed"
                            },
                            campaign_tag: {
                                required: "Enter upto 3 keywords saperated by commas.",
                            },
                            deal_value: {
                                required: "Please enter numeric campaign value"
                            },
                            category_id: {
                                cat: "Please select category.",
                            },
                            description: {
                                required: "Please enter description.",
                                maxlength: "Maximum 1200 characters | no HTML allowed",
                            }
                        }
                    });
                    $.validator.addMethod("keyword_length", function (value, element) {

                        var res = value.split(",");
                        if (res.length <= 3) {
                            return true;
                        } else {
                            return false;
                        }
                    }, "Maximum 3 keywords are allowed");
                    $.validator.addMethod("cat", function (value, element) {

                        if (value > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }, "Please select category.");

                    $('.mediaclass').click(function () {

                        $.fancybox({
                            content: $('#mediablock').html(),
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
                    });
                    var file_path = "";
                    $(document).on("click", "#show_more_mediya_browse", function () {
                        var cur_el = jQuery(this);
                        var next_index = parseInt(jQuery(this).attr('next_index'));
                        var num_of_records = parseInt(jQuery(this).attr('num_of_records'));
                        $.ajax({
                            type: "POST",
                            url: 'process.php',
                            data: 'show_more_media_browse=yes&next_index=' + next_index + '&num_of_records=' + num_of_records + "&img_type=campaign",
                            async: true,
                            success: function (msg)
                            {
                                var obj = jQuery.parseJSON(msg);
//alert(obj.status);
                                $(".fancybox-inner .ul_image_list").append(obj.html);
                                cur_el.attr('next_index', next_index + num_of_records);
                                if (parseInt(obj.total_records) < num_of_records)
                                {
                                    cur_el.css("display", "none");
                                }
                            }
                        });
                    });
                    $(document).on("click", ".ul_image_list li", function () {

                        $(".useradioclass").prop("checked", false);
                        var imgid = jQuery(this).attr("id").split("img_");
                        imgid = imgid[1];
//alert(imgid);
                        $(this).find(".useradioclass").prop("checked", true);
                        $(".ul_image_list li").removeClass("current");
                        $(this).addClass("current");
                        $(".fancybox-inner .useradioclass").each(function () {

                            if ($(".fancybox-inner .useradioclass").is(":checked"))
                            {

                                $(".fancybox-inner #btn_save_from_library").removeAttr("disabled");
                                $(".fancybox-inner #btn_save_from_library").removeClass("disabledmedia");
                                $(".fancybox-inner #btn_save_from_library").css("background-color", "#3C99F4 !important");
                            }
                            else
                            {
                                $(".fancybox-inner #btn_save_from_library").attr("disabled", true);
                                $(".fancybox-inner #btn_save_from_library").addClass("disabledmedia");
                                $(".fancybox-inner #btn_save_from_library").css("background-color", "#ABABAB !important");
                            }

                        });
                    });
                    var btnUpload = $('#upload');
                    var status = $('#status');
                    new AjaxUpload(btnUpload, {
                        action: 'merchant_media_upload.php?doAction=FileUpload&img_type=campaign',
                        name: 'uploadfile',
                        onSubmit: function (file, ext) {
                            if ($('#files').children().length > 0)
                            {
                                $('#files li').detach();
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
                                $("#hdn_image_path").val(file_path);
                                save_from_computer();
                            }
                        }
                    });
                });
                /* start of script for PAY-508-28033*/
                function save_from_library()
                {

                    var sel_val = $('input[name=use_image]:checked').val();
                    if (sel_val == undefined)
                    {
                        jQuery.fancybox.close();
                    }
                    else
                    {


                        $("#hdn_image_id").val(sel_val);
                        var sel_src = $(".fancybox-inner #li_img_" + sel_val + " span[id=span_img_text_" + sel_val + "]").text();
                        $("#hdn_image_path").val(sel_src);

                        file_path = "";

                        jQuery.fancybox.close();
                        var img = "<img src='<?= ASSETS_IMG ?>/m/campaign/" + sel_src + "' class='displayimg'>";
                        //$('#files').html(img + "<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' id='' class='cancel_remove_image' onclick='rm_image_lib()' /></div></div></div>");
                        $('#files').html("<img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' id='' class='delete_imag01' onclick='rm_image_lib()' />"+img);
                    }
                }
                function rm_image_lib()
                {
                    $("#hdn_image_path").val("");
                    $("#hdn_image_id").val("");
                    $('#files').html("");
                }
                function rm_image(id)
                {

                    $.ajax({
                        type: "POST",
                        url: 'process.php',
                        data: 'is_image_delete=yes&image_type=campaign&filename=' + id,
                        async: false,
                        success: function (msg)
                        {
                            $("#hdn_image_path").val("");
                            $("#hdn_image_id").val("");
                            $('#files').html("");
                        }

                    });

                }
                function save_from_computer()
                {
                    $("#hdn_image_id").val("");
                    var img = "<img src='<?= ASSETS_IMG ?>/m/campaign/" + $("#hdn_image_path").val() + "' class='displayimg'>";
                    //$('#files').html(img + "<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' id='" + $("#hdn_image_path").val() + "' class='cancel_remove_image' onclick='rm_image(this.id)' /></div></div></div>");
                    $('#files').html("<img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' id='' class='delete_imag01' onclick='rm_image_lib()' />"+img);
                }
        </script>
    </body>
</html>