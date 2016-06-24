<?php
/**
 * @uses add new gift card in reward zone.
 * @used in pages : 
 * @author Sangeeta Raghavani
 */
check_merchant_session();

?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Add Gift Card</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.min.js"></script>
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
        <script src="<?= ASSETS ?>/tinymce4/tinymce.min.js"></script>
        <script language="javascript" src="<?= ASSETS_JS ?>/a/ajaxupload.3.5.js" ></script>
<!--        <script type="text/javascript" src="<?= ASSETS ?>/tinymce/tiny_mce.js"></script>-->
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
        <script>
                jQuery(document).ready(function () {
                    window.tinymce.dom.Event.domLoaded = true;
                    tinyMCE.init({
                        // General options
                        //mode : "textareas",
                        mode: "exact",
                        elements: "description",
                        //theme: "advanced",
                        plugins: [
                            "advlist autolink lists link charmap searchreplace "
                        ],
                        toolbar1: "searchreplace | undo redo | bullist numlist | subscript superscript | charmap | link ",
                        menubar: false,
                        toolbar_items_size: 'small',
                        charLimit: 1200,
                        setup: function (ed) {
                            //peform this action every time a key is pressed
                            ed.on('keydown', function (e) {
                                var textarea = tinyMCE.activeEditor.getContent();
                                //alert(textarea);
                                var lastcontent = textarea;
                                //define local variables
                                var tinymax, tinylen, htmlcount;
                                //manually setting our max character limit
                                tinymax = ed.settings.charLimit;
                                //grabbing the length of the curent editors content
                                tinylen = ed.getContent().replace(/(<([^>]+)>)/ig, "").length;

                                if (tinylen + 1 > tinymax && e.keyCode != 8) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return false;
                                }
                            });
                            ed.on('keyup', function (e) {
                                //alert("up");
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


                            });
                        }
                    });
                    jQuery(".textarea_loader").css("display", "none");
                    jQuery(".textarea_container").css("display", "block");
                });
        </script>

    </head>
    <body>
        <div id="dialog-message" title="Message Box" style="display:none">
        </div>
        <?php
        require_once(MRCH_LAYOUT . "/header.php");
        ?>
        <div id="content">
            <div class="title_header">Add Gift Card</div>
            <div>
                <span class="span_error_msg" style="color:#FF0000; "></span>
            </div>
            <form action="process.php" method="post" class="gift_card08">
                <input type="hidden" name="hdn_image_path" id="hdn_image_path" value="">
                <input type="hidden" name="hdn_image_id" id="hdn_image_id" value="">
                <table width="100%" border="0" id="table_spacer">
                    <tbody>
			<!--<tr>
                            <td width="20%" align="right">Gift Card Title : </td>
                            <td width="80%" align="left">
                                <input type="text" name="title" id="title" class="gift_card_input" placeholder="Title" required >
                            </td>
                        </tr>-->
                        <tr>
                            <td width="20%" align="right">Gift Card Keywords : </td>
                            <td width="80%" align="left">
                                <input type="textarea" name="keywords" id="keywords" class="gift_card_input" placeholder="keywords separated by commas ">
                                <span data-toggle="tooltip" data-placement="right" class="notification_tooltip" title="" data-html="true" data-original-title="Keywords will help customer search for your gift card." style="display: none;">&nbsp;&nbsp;&nbsp;</span>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" align="right">Gift Card Value : </td>
                            <td width="70%" align="left">
                                <?php
                                //$Plans = $objDB->Conn->Execute('Select * from gift_card_plans where is_deleted=0');
                                $Plans = $objDB->Conn->Execute('Select m.merchant_id,gp.* from merchant_gift_card_plans m inner join gift_card_plans gp on gp.id = m.gift_card_plan_id where m.merchant_id=?', array($_SESSION['merchant_id']));
                                if ($Plans->RecordCount() > 0) {
                                        $p = $Plans->FetchRow();
                                        $min = $p['min_value'];
                                        $max = $p['max_value'];
                                        $inc = $p['increment'];
                                        $curr = $p['currency'];
                                        ?>
                                        <select name="giftcard_value" id="giftcard_value" required>
                                            <?php
                                            for ($i = $min; $i <= $max; $i +=$inc) {
                                                    ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i . ' ' . $curr; ?></option>
                                                    <?php
                                            }
                                            ?>
                                        </select>
                                        <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="20%" align="right">Gift Card Discount : </td>
                            <td width="80%" align="left">
                                <input type="text" name="giftcard_disc_value" id="giftcard_disc_value" placeholder="Optional">
                                <select id="is_per" name="is_per">
                                    <option value="per">% OFF</option>
                                    <option value="curr">$ OFF</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Category: </td>
                            <td align="left">
                                <select name="category_id" id="category_id">
                                    <option value="0">Select Category</option>
                                    <?php
                                    $sql = "Select * from giftcard_categories where active=1 ";
                                    $RS = $objDB->Conn->Execute($sql);
                                    while ($Row = $RS->FetchRow()) {
                                            ?>
                                            <option value="<?= $Row['id'] ?>"><?= $Row['cat_name'] ?></option>
                                            <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Gift Card Image : </td>
                            <td align="left">
                                <div class="pull-left">

                                    <div id="upload" >
                                        <span  >Browse</span>					
                                    </div>
                                </div>
                                <div class="browse_right_content">
                                    &nbsp;&nbsp;<span >Or select from </span><a class="mediaclass"  > media library </a>
                                    <span data-toggle="tooltip" data-placement="right" class="notification_tooltip" title="" data-html="true" data-original-title="Gift Card images uploaded must be at least 600 pixel x 300 pixel ( width x height )." style="display: none;">&nbsp;&nbsp;&nbsp; </span>
                                </div>
                            </td>
                        </tr>
                        <tr><td align="right">&nbsp; </td>
                            <td>
                        <div id="status" ></div>
                                <ul id="files" class="pull-left">
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Description:</td>
                            <td align="left">
                                <div align="center" class="textarea_loader" style="display: none;">
                                    <img src="<?php echo ASSETS_IMG; ?>/32.GIF" class="defaul_table_loader">
                                </div>
                                <div class="textarea_container">	
                                    <textarea id="description" name="description" rows="15" cols="80" style="width: 80%"></textarea>
                                    <span id="desc_limit">Maximum 1200 characters | no HTML allowed</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td align="left">
                                <input type="submit" name="btnAddGiftCard" id="btnAddGiftCard" value="Save">
                                <input type="button" name="btnCancelgiftcard" value="Cancel" onclick="window.location.href = '<?php echo WEB_PATH; ?>/merchant/manage-reward-zone.php';">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <!--end of content--></div>
        <script>
                function changetext2() {
                    var v = 8 - $("#discount").val().length;
                    $(".span_c2").text(v + " characters remaining");
                }
                var file_path = "";
                $(function () {
                    var btnUpload = $('#upload');
                    var status = $('#status');

                    new AjaxUpload(btnUpload, {
                        action: '<?php echo WEB_PATH; ?>/admin/upload_giftcard.php?doAction=FileUploadGiftCard&img_type=giftcard',
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

                    jQuery(".gift_card08").validate({
                        rules: {
                            /*title: {
                                required: true,
                                maxlength: 76
                            },*/
                            keywords: {
                                required: true,
                                keyword_length: true
                                        //regex: /^[a-zA-Z'.\s]{1,40}$/
                            },
                            giftcard_value: {
                                required: true,
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
                            keywords: {
                                required: "Enter upto 3 keywords saperated by commas.",
                            },
                            giftcard_value: {
                                required: "Please select gift card value."
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
                            data: 'show_more_media_browse=yes&next_index=' + next_index + '&num_of_records=' + num_of_records + "&img_type=giftcard",
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


                });

                /* start of script for PAY-508-28033*/
                function save_from_library()
                {


                    $("#hdn_image_id").val(sel_val);
                    var sel_src = $("#li_img_" + sel_val + " span[id=span_img_text_" + sel_val + "]").text();
                    //alert(sel_src);
                    $("#hdn_image_path").val(sel_src);
                    file_path = "";

                    var img = "<img src='<?= ASSETS_IMG ?>/a/giftcards/" + sel_src + "' class='displayimg'>";
                    //$('#files').html(img + "<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' onclick='rm_image()' /></div></div></div>");
                    $('#files').html("<img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' onclick='rm_image()' class='delete_imag01' />" + img);

                }
                function rm_image()
                {
                    $("#hdn_image_path").val("");
                    $("#hdn_image_id").val("");
                    $('#files').html("");

                }
                function save_from_computer()
                {
                    //$("#hdn_image_path").val(file_path);
                    $("#hdn_image_id").val("");

                    var img = "<img src='<?= ASSETS_IMG ?>/a/giftcards/" + $("#hdn_image_path").val() + "' class='displayimg'>";
                    //$('#files').html(img + "<br/><div style='margin-top: 10px; display:table'><div style='display:table-row;'><div style='display:table-cell;'><img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' onclick='rm_image()' /></div></div></div>");
                    $('#files').html("<img src='<?= ASSETS_IMG ?>/m/mediya_delete.png' onclick='rm_image()' class='delete_imag01' />" + img);

                }

        </script>
        <?php
        echo file_get_contents(WEB_PATH . '/merchant/import_media_library.php?mer_id=' . $_SESSION['merchant_id'] . '&img_type=giftcard&start_index=0');
        ?>
        <?php
        $_SESSION['msg'] = "";
        ?>		
    </body>
</html>
