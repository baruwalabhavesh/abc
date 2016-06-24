<?php
/**
 * @uses edit distribution list
 * @used in pages : manage-customers.php,process.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//$objJSON = new JSON();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
//require_once("../classes/Config.Inc.php");
//check_merchant_session();


$RSCat = $objDB->Show("categories");
$array = array();

//$array_where['merchant_id'] = $_SESSION['merchant_id'];
$array_where['id'] = $_REQUEST['id'];
$RS = $objDB->Show("merchant_customerlist", $array_where);

if (isset($_REQUEST['btnImportCustomers'])) {

        //$objJSON->import_customers();
        $_SESSION['msg'] = "Customer(s) has been imported Sucessfully";
}

$array_where_user['id'] = $_SESSION['merchant_id'];
$RS_User = $objDB->Show("merchant_user", $array_where_user);
$m_parent = $RS_User->fields['merchant_parent'];
if ($RS_User->fields['merchant_parent'] == 0) {
        $array['created_by'] = $_SESSION['merchant_id'];
        $array['active'] = 1;
        $RSStore = $objDB->Show("locations", $array);
} else {
        $media_acc_array = array();
        $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
        $RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
        $location_val = $RSmedia->fields['location_access'];


        //$sql = "SELECT * FROM locations  WHERE id in (select l.id from locations l where l.created_by=".$m_parent ." and l.id = ".$location_val .")";
        //$RSStore = $objDB->execute_query($sql);
        $arr = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $m_parent . '&loc_id=' . $location_val);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);

        $total_records = $json->total_records;
        $records_array = $json->records;
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Edit Customer List</title>
        <?php require_once(MRCH_LAYOUT . "/head.php"); ?>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
    </head>
    <body>
        <div id="dialog-message" title="Message Box" style="display:none">

        </div>
        <div >
                <!--<script type="text/javascript" src="<?= ASSETS_JS ?>/m/jquery-1.7.2.min.js"></script>-->
            <!-- load from CDN-->
            <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->

            <script language="javascript" src="<?= ASSETS_JS ?>/m/ajaxupload.3.5.js" ></script>
            <!---start header---->
            <div><?require_once(MRCH_LAYOUT."/header.php");?>
                <!--end header--></div>
            <div id="contentContainer">	
                <div id="content">
                    <div class="title_header">Edit Customer List</div>
                    <div id="backdashboard" align="right" >
                        <a id="dashboard" href="<?= WEB_PATH ?>/merchant/manage-customers.php" ><img src="<?= ASSETS_IMG ?>/m/back_but.png" /></a>
                    </div>
                    <div class="disclaimer">Disclaimer: We'll automatically clean duplicate instances of email addresses from the list. Importing does not send any confirmation emails to your list, because we trust you've already received permission. Make sure everyone on your list has actually granted you permission to email them. Do not import any third party lists, prospects, lists that you scraped from websites, chambers of commerce lists, etc. </div>	
                    <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                        <tr>  
                            <td width="100%" align="left" valign="top">
                                <form action="process.php?id=<?php echo $_REQUEST['id']; ?>" method="post" enctype="multipart/form-data">	
                                    <table width="100%"  border="0" cellspacing="2" cellpadding="2" >    
                                        <tr>
                                            <td width="15%">&nbsp;</td>
                                            <td width="85%" class="table_errore_message  " align="left" id="msg_div"><?= $_SESSION['msg'] ?>&nbsp;</td>    
                                        </tr>
                                        <tr >
                                            <td colspan="2">
                                                <table width="100%" class="main_import">
                                                    <tr >
                                                        <td>
                                                            <div class="td_left">Distribution List Name :</div>

                                                            <?php
                                                            $d_arr = array();
                                                            ?>
                                                            <div class="td_right" ><input type="text" name="group_name" id="group_name" value="<?php echo $RS->fields['group_name'] ?>" disabled /></div>
                                                        </td>
                                                    </tr>

                                                    <?php 
                                                    /*
                                                    if ($_SESSION['merchant_info']['merchant_parent'] == 0) { ?>
                                                            <tr  class="tr_top">
                                                                <td>
                                                                    <div class="td_left">Change Location :</div>
                                                                    <div class="td_right">
                                                                        <?php
                                                                        $disable = '';
                                                                        $sStatus = strtolower($_REQUEST['status']);
                                                                        if ($sStatus == 'active') {
                                                                                $disable = 'disabled';
                                                                        }
                                                                        ?>
                                                                        <select name="location_name" id="location_name" <?php echo $disable; ?> >
                                                                            <?php
                                                                            $result1 = $objDB->Conn->Execute("SELECT * from locations where active=1 and created_by=" . $_SESSION['merchant_id']);
                                                                            while ($Row = $result1->FetchRow()) {
                                                                                    ?>
                                                                                    <option value="<?= $Row['id'] ?>" <?php
                                                                                    if ($RS->fields['location_id'] == $Row['id']) {
                                                                                            echo "selected";
                                                                                    }
                                                                                    ?> >

                                                                                        <?php
                                                                                        $location_string = $Row['address'] . ", " . $Row['city'] . ", " . $Row['state'] . ", " . $Row['zip'];
                                                                                        $location_string = (strlen($location_string) > 57) ? substr($location_string, 0, 57) . '...' : $location_string;

                                                                                        echo $location_string;
                                                                                        ?>
                                                                                    </option>
                                                                            <?php }
                                                                            ?>

                                                                        </select>


                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                    } else {
                                                            ?>
                                                            <tr class="tr_top">
                                                                <td>
                                                                    <div class="td_left">Assigned Location :</div>
                                                                    <?php
                                                                    if ($total_records > 0) {
                                                                            foreach ($records_array as $RSStore) {
                                                                                    $sub_location_id = $RSStore->id;
                                                                                    //echo $RSStore->location_name;
                                                                                    $location_string = $RSStore->address . ", " . $RSStore->city . ", " . $RSStore->state . ", " . $RSStore->zip;
                                                                                    $location_string = (strlen($location_string) > 57) ? substr($location_string, 0, 57) . '...' : $location_string;

                                                                                    echo $location_string;
                                                                            }
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <input type="hidden" name="location_name" id="location_name" value="<?= $RSStore->id ?>" />
                                                            <?php
                                                    }
                                                    */
                                                    ?>


                                                    <tr class="divide tr_top">
                                                        <td>
                                                            <div class="import_customer">Add new customer list ( CSV File) :</div>
                                                            <div  class="browse_btn" ><div id="upload" >
                                                                    <span  >Choose File
                                                                    </span> 
                                                                </div>
                                                                <span id="status" ></span>
                                                                <span id="uploadedfile"></span></div>
                                                            <div class="imprt_cust_tooltip"> <span class="notification_tooltip"  data-toggle="tooltip" data-placement="right" title="<?php echo $merchant_msg["import-customers"]["Tooltip_csv_file"]; ?>" >&nbsp;&nbsp;&nbsp</span></div>   
                                                        </td>
                                                    </tr>

                                                    <tr class="tr_top">
                                                        <td>
                                                            <div class="btn_submit_div" >
                                                                <input type="submit" name="edit_distribution_list" id="edit_distribution_list" value="<?php echo $merchant_msg['index']['btn_save']; ?>" />&nbsp;&nbsp;

                                                                <script>function btncanDistributionlist() {
                                                                            window.location = "<?= WEB_PATH ?>/merchant/manage-customers.php";
                                                                        }
                                                                </script>
                                                                <input type="submit" name="btnCanDistributionlist" value="<?php echo $merchant_msg['index']['btn_cancel']; ?>" onClick="btncanDistributionlist()">
                                                            </div> 
                                                        </td>
                                                    </tr>


                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <input type="hidden" name="hdn_csvfile" id="hdn_csvfile" value="" /> 
                                    <input type="hidden" name="group_id" id="group_id" value="<? echo $_REQUEST['id']?>"/>          
                                </form>
                                <?php if (isset($_COOKIE["csvfilecookie"]) == 1) { ?>
                                        <div id="wrong_file_data">
                                            <form  action="process.php?id=<?php echo $_REQUEST['id']; ?>" method="post" enctype="multipart/form-data">
                                                <div id="div_msg" class="wrong_file_msg" >
                                                    <?php echo $_SESSION['dowload_err_msg']; ?>
                                                </div>

                                                <div class="wrong_file_button" align="center">
                                                    <input type="submit" class="btn btn-success" name="download_csv_problem" id="download_csv_problem" value="Download" /> &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type="submit" class="popupcancel" name="popupcancel" id="popupcancel" value="<?php echo $merchant_msg['index']['fancybox_ok']; ?>" /></div>
                                            </form>

                                        </div>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>

                    <div class="clear">&nbsp;</div>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>

            <!---------start footer--------------->
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                $_SESSION['msg'] = "";
                ?>
                <!--end of footer--></div>

        </div>
    </body>

    <!--- tooltip css --->
    <script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.js"></script>
    <script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.min.js"></script>

    <!--- tooltip css --->
</html>
<?php
if (isset($_COOKIE["csvfilecookie"])) {
        $csvfilecookie = $_COOKIE["csvfilecookie"];
} else {
        $csvfilecookie = "";
}
?>
<script type="text/javascript">
var file_path = "";
        $(function () {
            var btnUpload = $('#upload');
            var status = $('#status');

            new AjaxUpload(btnUpload, {
                action: 'edit-csv.php?doAction=FileUpload',
                name: 'uploadcsvfile',
                onSubmit: function (file, ext) {
                    if ($('#files').children().length > 0)
                    {
                        $('#files li').detach();
                    }
                    if (!(ext && /^(csv|xls|xlsx)$/.test(ext))) {
                        // extension is not allowed 
                        jQuery("#hdn_csvfile").val("");
                        jQuery("#uploadedfile").text("");
                        status.text('Only csv,xls,xlsx files are allowed');
                        return false;
                    }
                    status.text('Uploading...');
                },
                onComplete: function (file, response) {
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

                    status.text('');
                    //Add uploaded file to list
                    file_path = arr[1];
                    file_name = arr[2];
                    status.text('');
                    jQuery("#hdn_csvfile").val(file_path);
                    jQuery("#uploadedfile").text(file_name);


                }
            });
        });
        jQuery("#download_csv_problem").live("click", function () {

            jQuery.fancybox.close();
        });
        jQuery(document).ready(function () {

            var cookies_val = "<?php echo $csvfilecookie; ?>";


            //if (document.cookie.indexOf(" " + "csvfilecookie" + "=") >= 0) {
            if (cookies_val == 1) {


                //alert(cookies_val);

                jQuery.fancybox({
                    content: jQuery('#wrong_file_data').html(),
                    type: 'html',
                    width: 400,
                    height: 320,
                    openEffect: 'elastic',
                    openSpeed: 300,
                    scrolling: 'no',
                    closeEffect: 'elastic',
                    closeSpeed: 300,
                    closeBtn: false,
                    beforeShow: function () {
                        $(".fancybox-inner").addClass("Class_fancy_ie_login");
                    },
                    afterClose: function () {
                        var path = "<?php echo SERVER_PATH . '/merchant/ProblemOfCSV' . $_SESSION['merchant_id'] . '.csv' ?>";
                        $.ajax({
                            type: "POST",
                            url: "<?= WEB_PATH ?>/merchant/process.php",
                            data: "deletecsvfile=yes&path=" + path,
                            async: false,
                            success: function (msg) {

                            }
                        });

                    },
                    helpers: {
                        overlay: {
                            opacity: 0.3,
                            closeClick: false,
                        } // overlay
                    },
                    keys: {
                        close: null
                    }
                });


                del_cookie("csvfilecookie");

            }
        });

        jQuery("#edit_distribution_list").live("click", function () {

            var alert_msg = "<?php echo $merchant_msg["index"]["validating_data"]; ?>";
            var head_msg = "<div class='head_msg'>Message</div>";
            var content_msg = "<div class='content_msg validatingdata' style='background:white;'>" + alert_msg + "</div>";
            jQuery("#NotificationloadermainContainer").html(content_msg);
            jQuery("#NotificationloaderFrontDivProcessing").css("display", "block");
            jQuery("#NotificationloaderBackDiv").css("display", "block");
            jQuery("#NotificationloaderPopUpContainer").css("display", "block");

            var flag = true;
            var msg_box = "";
            var file_msg = "";
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
                        flag = 1;
                        window.location.href = obj.link;

                    }


                }

            });
            if (flag == 1)
            {
                // return false; 
            }
            else
            {
                return true;
            }
            loc_id = $("#location_name").val();

            $.ajax({
                type: "POST",
                url: "<?= WEB_PATH ?>/merchant/process.php",
                data: "loc_id=" + loc_id + "&check_location_active=yes",
                async: false,
                success: function (msg) {
                    var obj = jQuery.parseJSON(msg);
                    if (obj.status == "false")
                    {
                        flag = "false";
                        msg_box += "<div>* <?php echo $merchant_msg["manage-customers"]['Title_Manage_Customer_Distribution_List'] ?></div>";
                        flag = false;
                    }
                }
            });
            if (!flag)
            {
                close_popup("Notificationloader");

                var head_msg = "<div class='head_msg'>Message</div>";
                var content_msg = "<div class='content_msg'>" + msg_box + file_msg + "</div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel2' name='popupcancel2' class='msg_popup_cancel'></div>";

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
            else
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
        });
        jQuery("#popupcancel2").live("click", function () {
            jQuery.fancybox.close();
            return false;
        });
        jQuery("#popupcancel").live("click", function () {

            var path = "<?php echo SERVER_PATH . '/merchant/ProblemOfCSV' . $_SESSION['merchant_id'] . '.csv' ?>";
            // alert(path );
            //             File.Delete(path);
            // alert(File );
            $.ajax({
                type: "POST",
                url: "<?= WEB_PATH ?>/merchant/process.php",
                data: "deletecsvfile=yes&path=" + path,
                async: false,
                success: function (msg) {

                }
            });
            //jQuery.fancybox.close();

            window.location.href = "<?php echo WEB_PATH; ?>/merchant/manage-customers.php";
            return false;
        });
        function del_cookie(name)
        {
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }
        $("a#myaccount").css("background-color", "orange");
        jQuery(document).ready(function () {
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

            if ($("#hdn_image_id").val() != "")
            {
                $('input[name=use_image][value=' + $("#hdn_image_id").val() + ']').attr("checked", "checked");
            }
            $("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
                $("#" + popup_name + "BackDiv").fadeIn(200, function () {
                    $("#" + popup_name + "PopUpContainer").fadeIn(200, function () {

                    });
                });
            });


        }
</script>
