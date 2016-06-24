<?php
/**
 * @uses manage users
 * @used in pages : add-user.php,edit-user.php,process.php,my-account-left.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where = array();
$active_locations = 1;
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
        /*         * ************ Check if any active location is there or not if not then dont allow to add campaign  ******** */
        $arr = file(WEB_PATH . '/merchant/process.php?is_any_active_location=yes&merchant_id=' . $_SESSION['merchant_id']);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
        $active_locations = $json->active_locations;
}

//permission code
$array = $json_array = array();
$array1 = $json_array = array();
$array['id'] = $_SESSION['merchant_id'];


//$RS = $objDB->Show("merchant_user",$array);
$merchant_parent_value = $_SESSION['merchant_info']['merchant_parent'];

$array1['id'] = $merchant_parent_value;
$RS1 = $objDB->Show("merchant_user", $array1);
//End of permission code

/* * ************* Get all sub merchant of current login employee ************** */
$arr = file(WEB_PATH . '/merchant/process.php?getallsubmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
if (trim($arr[0]) == "") {
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);
$ids = $json->all_sub_merchant_id;

$all_sub_mer_array = explode(",", $ids);

if ($_SESSION['merchant_info']['merchant_parent'] != 0) {
        /*         * *********** Get all sub merchant of current login employee *************** */
        $arr = file(WEB_PATH . '/merchant/process.php?getallmainmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);

        $all_main_merchant_id = $json->all_main_merchant_id;
        //print_r($all_main_merchant_id); 
        $all_main_mer_array = explode(",", $all_main_merchant_id);
} else {
        $all_main_mer_array = array('');
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Manage Location Employee</title>
        <?php //require_once(MRCH_LAYOUT . "/head.php"); ?>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
    </head>

    <body class="my_user">
        <div>
            <link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
            <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>

            <div>
                <?php
                require_once(MRCH_LAYOUT . "/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">

                <div id="content">
					 <div id="dialog-message" title="Message Box" style="display:none">

                    </div>
                    
                    <div class="title_header"><?php echo $merchant_msg['locationemployee']['manage_location_employee']; ?></div>

                    <div id="backdashboard1"> 
                        <?php
                        if ($_SESSION['merchant_id'] != "") {
                                /*                                 * ********** Get employee role ********** */
                                $arr = file(WEB_PATH . '/merchant/process.php?btnGetMerchantRole=yes&mer_id=' . $_SESSION['merchant_id']);
                                if (trim($arr[0]) == "") {
                                        unset($arr[0]);
                                        $arr = array_values($arr);
                                }
                                $json = json_decode($arr[0]);
                                $total_records1 = $json->total_records;
                                $records_array1 = $json->records;

                                if ($total_records1 > 0) {

                                        foreach ($records_array1 as $Row_role) {
                                                $Row_role->merchant_user_id;
                                                $ass_page = unserialize($Row_role->ass_page);
                                                $ass_role = unserialize($Row_role->ass_role);
                                        }

                                        foreach ($ass_role as $op_role) {
                                                $op_role;
                                        }
                                } else {
                                        echo "";
                                }
                                ?>
                        <?php } ?> 

                        <?php
                        if ($_SESSION['merchant_info']['merchant_parent'] == 0 || in_array("add-user.php", $ass_page)) {
                                if ($active_locations > 0) {
                                        ?>
                                        <h4 align="right" class="add_employee" ><a class="commonclass" link="add-user.php" href="javascript:void(0);"><?php echo $merchant_msg['locationemployee']['add_new_location_employee']; ?></a></h4>
                                <?php } else {
                                        ?>
                                        <h4 align="right" class="add_employee" ><a href="javascript:void(0)"  onclick="show_error('Sorry no active location available to add employee')"><?php echo $merchant_msg['locationemployee']['add_new_location_employee']; ?></a></h4>
                                        <?php
                                }
                        }
                        ?>
                    </div>	
                    <div class="cls_clear" ></div>
                    <div align="center">
                        <img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="table_loader defaul_table_loader" />
                    </div>
                    <div class="datatable_container" style="display: none;">

                        <table border="0" cellspacing="1" cellpadding="10" class="tableMerchant" id="manage_loc_employee_table">
                            <thead>
                                <tr>
                                    <td colspan="5" align="left" class="table_errore_message"><?php echo (isset($_SESSION['msg'])) ? $_SESSION['msg'] : ''; ?>&nbsp;</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone No</th>
                                    <th>Email</th>
                                    <th>Assigned Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                        </table>

                    </div>
                    <div class="clear">&nbsp;</div>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                $_SESSION['msg'] = "";
                ?>
                <!--end of footer--></div>

        </div>

        <div id="message-window" title="Message Box" style="display:none">
        </div>
        <div class="employee_detail_div" style="display:none">

        </div>
        <script type="text/javascript">
			
			/******* initialize jQuery data table ****/
                                
                                var oCache = {
                        iCacheLower: -1
                    };

                    function fnSetKey(aoData, sKey, mValue)
                    {
                        for (var i = 0, iLen = aoData.length; i < iLen; i++)
                        {
                            if (aoData[i].name == sKey)
                            {
                                aoData[i].value = mValue;
                            }
                        }
                    }

                    function fnGetKey(aoData, sKey)
                    {
                        for (var i = 0, iLen = aoData.length; i < iLen; i++)
                        {
                            if (aoData[i].name == sKey)
                            {
                                return aoData[i].value;
                            }
                        }
                        return null;
                    }

                    function fnDataTablesPipeline(sSource, aoData, fnCallback) {
                        var iPipe = 2; /* Ajust the pipe size */

                        var bNeedServer = false;
                        var sEcho = fnGetKey(aoData, "sEcho");
                        var iRequestStart = fnGetKey(aoData, "iDisplayStart");
                        var iRequestLength = fnGetKey(aoData, "iDisplayLength");
                        var iRequestEnd = iRequestStart + iRequestLength;
                        oCache.iDisplayStart = iRequestStart;

                        /* outside pipeline? */
                        if (oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper)
                        {
                            bNeedServer = true;
                        }

                        /* sorting etc changed? */
                        if (oCache.lastRequest && !bNeedServer)
                        {
                            for (var i = 0, iLen = aoData.length; i < iLen; i++)
                            {
                                if (aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho")
                                {
                                    if (aoData[i].value != oCache.lastRequest[i].value)
                                    {
                                        bNeedServer = true;
                                        break;
                                    }
                                }
                            }
                        }

                        /* Store the request for checking next time around */
                        oCache.lastRequest = aoData.slice();

                        if (bNeedServer)
                        {
                            if (iRequestStart < oCache.iCacheLower)
                            {
                                iRequestStart = iRequestStart - (iRequestLength * (iPipe - 1));
                                if (iRequestStart < 0)
                                {
                                    iRequestStart = 0;
                                }
                            }

                            oCache.iCacheLower = iRequestStart;
                            oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
                            oCache.iDisplayLength = fnGetKey(aoData, "iDisplayLength");
                            fnSetKey(aoData, "iDisplayStart", iRequestStart);
                            fnSetKey(aoData, "iDisplayLength", iRequestLength * iPipe);

                            $.getJSON(sSource, aoData, function (json) {
                                /* Callback processing */
                                oCache.lastJson = jQuery.extend(true, {}, json);

                                if (oCache.iCacheLower != oCache.iDisplayStart)
                                {
                                    json.aaData.splice(0, oCache.iDisplayStart - oCache.iCacheLower);
                                }
                                json.aaData.splice(oCache.iDisplayLength, json.aaData.length);

                                fnCallback(json)
                            });
                        }
                        else
                        {
                            json = jQuery.extend(true, {}, oCache.lastJson);
                            json.sEcho = sEcho; /* Update the echo for each response */
                            json.aaData.splice(0, iRequestStart - oCache.iCacheLower);
                            json.aaData.splice(iRequestLength, json.aaData.length);
                            fnCallback(json);
                            return;
                        }
                    }
                    
                    $(document).ready(function () {
                                var oTable =   $('#manage_loc_employee_table').dataTable({
                                        // "sPaginationType": "full_numbers",
                                        "oLanguage": {
                                            "sEmptyTable": "No employees in the system currently, please add at least one.",
                                        },
                                        "bPaginate": true,
                                        "bFilter": false,
                                        "bSort": false,
                                        "bLengthChange": false,
                                        "info": false,
                                        "bProcessing": true,
                                        "bServerSide": true,
                                        "iDisplayLength": 10,
					"fnPreDrawCallback": function( oSettings ) {
					      jQuery.ajax({
						type:"POST",
						url:'process.php',
						data :'loginornot=true',
						success:function(msg)
							{
								var obj = jQuery.parseJSON(msg);
								if (obj.status=="false") 
								{
									window.location.href="<?php echo WEB_PATH ?>/merchant/register.php";
								}
						
							}
						});
					},
                                        "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                                        "fnServerParams": function (aoData) {
                                            aoData.push({"name": "btnGetUsersOfMerchant", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'}, {"name": "all_main_mer_array", "value":<?php echo ((!empty($all_main_mer_array) ? json_encode($all_main_mer_array) : '')); ?>}, {"name": "ass_page", "value":<?php echo json_encode($ass_page); ?>}, {"name": "all_sub_mer_array", "value":<?php echo json_encode($all_sub_mer_array); ?>});

                                        },
                                        "aoColumns": [null, null, null, null, {"sClass": "actiontd"}]
                                    });

                                    jQuery(".table_loader").css("display", "none");
                                    jQuery(".datatable_container").css("display", "block");

                              
                                
                $("a#myaccount").css("background-color", "orange");

                var merchant_parent_value = "<?php echo $merchant_parent_value; ?>";
                var main_merchant_approve = "<?php echo $_SESSION['merchant_info']['approve']; ?>";

                var sub_merchant_approve = "<?php echo $RS1->fields['approve']; ?>";

                if (merchant_parent_value == 0)
                {
                    if (main_merchant_approve == 2)
                    {
                        //alert("first");
                        jQuery("#backdashboard1").hide();

                    }

                }
                else
                {
                    if (sub_merchant_approve == 2)
                    {
                        //alert("second");
                        jQuery("#backdashboard1").hide();

                    }
                }
                /********** close more action popup on click anywhere in body ************/
                $("body").click
                        (
                                function (e)
                                {

                                    if ((e.target.className).trim() == "actiontd")
                                    {

                                    }
                                    else
                                    {
                                        $('.actiontd').find('#actiondiv').slideUp("slow");
                                    }
                                }
                        );

                /********** open more action popup ************/
                $(document).on('click', '.actiontd', function () {

                    if ($(this).find('#actiondiv').css('display') == 'none')
                    {

                        $('.actiondivclass').css("display", "none");
                        //$(this).find('#actiondiv').css('display','block');
                        $(this).find('#actiondiv').slideDown("slow");
                    }
                    else
                        $(this).find('#actiondiv').slideUp('slow');
                    /*
                     },function(){
                     $(this).find('#actiondiv').css('display','none');  */
                });
                jQuery("#popupcancel").live("click", function () {
                    jQuery.fancybox.close();
                    return false;
                });
                
                 jQuery(document).on("click", "#popupyes", function () {
                        var linkurl = jQuery(this).attr("link");
                        jQuery.fancybox.close();
                        //window.location.href = linkurl;
                        jQuery.ajax({
                            type: "POST",
                            url: linkurl,
                            //data: '<?= WEB_PATH ?>/merchant/' + linkurl,
                            async: false,
                            success: function (msg)
                            {
                                var obj = jQuery.parseJSON(msg);
                                if (obj.status == 'true') {
									oCache.iCacheLower = -1;
                                    oTable.fnDraw();
                                    jQuery('.table_errore_message').text(obj.message);
                                }
                            }
                        });

                    });
                    

                /******* check user session active or not before perform any more action event **************/
                $(document).on('click', '.commonclass', function () {
                    var linkurl = jQuery(this).attr("link");
                    var txt = jQuery(this).text();
					 var chkclass = jQuery(this).hasClass("empdelete");
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
                                window.location.href = obj.link;
                            }
                            else
                            {
                                if (txt == 'Edit' || linkurl == 'add-user.php') 
                                {
                                    window.location.href = "<?= WEB_PATH ?>/merchant/" + linkurl;
                                } 
                                else 
                                {
									if (chkclass == false) // activate
                                    {	
										jQuery.ajax({
											type: "POST",
											url: "<?= WEB_PATH ?>/merchant/" + linkurl,
											success: function (msg) {
												jQuery('#manage_loc_employee_table').dataTable().fnDraw();
											}
										});
									}
									else // delete
									{
										var msg = "<div style='width: 252px;'>Are you sure you want to delete employee ?</div>";
                                        var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Message Box</div>"
                                        var content_msg = "<div style='text-align:left;margin-top:10px;padding:5px;'>" + msg + "</div>";
                                        var footer_msg = "<div style='text-align:center'><hr><input type='button' link='<?php echo WEB_PATH ?>/merchant/" + linkurl + "' value='Yes' id='popupyes' name='popupyes' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'>&nbsp;&nbsp;&nbsp;<input type='button'  value='No' id='popupcancel' name='popupcancel' style='padding:5px 15px;cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                                        jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

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
                                }

                            }
                        }
                    });
                });
				
                /*********** get employee detail information of employye , click employee first name in grid *****************/
                jQuery("a[id^='empfirstname_']").click(function () {

                    var arr = jQuery(this).attr("id").split("_");
                    var cid = arr[1];

                    jQuery.ajax({
                        type: "POST",
                        url: "<?= WEB_PATH ?>/merchant/process.php",
                        data: "empid=" + cid + "&getEmployeedetail_for_popup=yes",
                        async: false,
                        success: function (msg) {

                            jQuery(".employee_detail_div").html(msg);
                            //$("#jqReviewHelpfulMessageNotification").html(msg);
                            jQuery.fancybox({
                                content: jQuery('.employee_detail_div').html(),
                                type: 'html',
                                maxWidth: 790,
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
                    });

                    //open_popup('Notification');
                });
                function show_error(message)
                {
                    var alert_msg = message;
                    var head_msg = "<div class='head_msg'>Message</div>";
                    var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                    var content_msg = "<div class='content_msg'>" + alert_msg + "</div>";
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

                    return false;

                }
                }); // for document ready
        </script>
    </body>
</html>
