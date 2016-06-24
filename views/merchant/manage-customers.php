<?php
/**
 * @uses manage customers
 * @used in pages : edit-customer.php,edit-distributionlist.php,group_clone_distributionlist.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
include(LIBRARY . "/easy_upload/upload_class.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
//$objDBWrt = new DB('write');


$active_locations = 1;
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
        /*         * ****** check if any active location is there or not if not then dont allow to add campaign  ***************** */
        $arr = file(WEB_PATH . '/merchant/process.php?is_any_active_location=yes&merchant_id=' . $_SESSION['merchant_id']);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
        $active_locations = $json->active_locations;
}
/* * ******* get all sub merchant of current login employee **************** */


$array = array();
$array_where = array();
if (isset($_REQUEST['action'])) {
        if ($_REQUEST['action'] == "delete") {
                $array_where['user_id'] = $_REQUEST['id'];
                $array_where['merchant_id'] = $_REQUEST['merchant_id'];
                $objDBWrt->Remove("merchant_subscribs", $array_where);
                $_SESSION['msg'] = "Customer is deleted successfully";
                header("Location: " . WEB_PATH . "/merchant/manage-customers.php");
                exit();
        }
}

/* * ***** Get employee role ********* */
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
} else {
        echo "";
}

$array = array();
$array['merchant_id'] = $_SESSION['merchant_id'];

$m_parent = $_SESSION['merchant_info']['merchant_parent'];
if ($m_parent == 0) {
        $m_parent = $_SESSION['merchant_id'];
}
$media_acc_array = array();
$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
$RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
$location_val = $RSmedia->fields['location_access'];

/* * **************** Get location list of login merchan t*********** */
//echo WEB_PATH.'/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id='.$m_parent.'&loc_id='.$location_val;
$arr_submerchant = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $m_parent . '&loc_id=' . $location_val);
if (trim($arr_submerchant[0]) == "") {
        unset($arr_submerchant[0]);
        $arr_submerchant = array_values($arr_submerchant);
}

$json = json_decode($arr_submerchant[0]);

$total_records_submerchant = $json->total_records;
$records_array_submerchant = $json->records;
//print_r($records_array_submerchant);
//echo json_encode($sort_permision_arr);
/* * ************** Get All Sub merchants ************* */
$arr = file(WEB_PATH . '/merchant/process.php?getallsubmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
if (trim($arr[0]) == "") {
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);

$all_sub_merchant_id = $json->all_sub_merchant_id;
$all_sub_mer_array = explode(",", $all_sub_merchant_id);
/* * **** get parent merchant id for employee ***** */
//$ass_page = array();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Manage Customer List</title>
        <?php require_once(MRCH_LAYOUT . "/head.php"); ?>
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>-->
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <!-- <meta http-equiv="refresh" content = "302; url=http://www.scanflip.com/merchant/index.php"> -->
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

        <!--- tooltip css --->
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.js"></script>
        <!--<script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.min.js"></script>--->
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
        <!--- tooltip css --->

        <link href="<?= ASSETS_CSS ?>/m/ui-lightness/jquery-ui-1.8.14.custom.css" rel="stylesheet" type="text/css" />
        <link href="<?= ASSETS_CSS ?>/m/fileUploader.css" rel="stylesheet" type="text/css" />
        <!--<script src="<?= ASSETS_JS ?>/m/jquery-ui-1.8.14.custom.min.js" type="text/javascript"></script> -->
        <script src="<?= ASSETS_JS ?>/m/jquery.fileUploader.js" type="text/javascript"></script>
        <!--<style type="text/css" title="currentStyle">
                                @import "<?php echo ASSETS_CSS ?>/m/demo_page.css";
                                @import "<?php echo ASSETS_CSS ?>/m/demo_table.css";
                        </style>
                        
                        <script type="text/javascript" language="javascript" src="<?php //echo ASSETS_JS         ?>/m/jquery.dataTables.js"></script> -->
        <link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
        <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf-8">
                /****** initialize datatable **********/
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
                    


                    var oTable = $('#manage_customer_list_table').dataTable({
                        "bFilter": false,
                        "bLengthChange": false,
                        "bSort": false,
                        "info": false,
                        // "sPaginationType": "full_numbers",
                        "bProcessing": true,
                        "bServerSide": true,
                        //"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0,1,2,3,4] }],
                        "iDisplayLength": 10,
                        "oLanguage": {
                            "sEmptyTable": "No customers founds in the system.",
                            "sZeroRecords": "No customers List to display",
                            "sProcessing": "Loading..."
                        },
			"fnPreDrawCallback": function( oSettings ) {
			      $.ajax({
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
                        //"iDeferLoading": <?php //echo $total_records1;                               ?>,
                        "fnServerParams": function (aoData) {
                            aoData.push({"name": "filterlocation", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'}, {"name": "parent", "value": <?php echo $_SESSION['merchant_info']['merchant_parent']; ?>}, {"name": "locationid", "value": ((jQuery("#hdn_employee_location").val() != "") ? jQuery("#hdn_employee_location").val() : jQuery("#filterlocation").val())}, {"name": "status", "value": $('#status_active').val()}, {"name": "all_sub_mer_array", "value": <?php echo json_encode($all_sub_mer_array); ?>}, {"name": "ass_page", "value": <?php echo json_encode($ass_page); ?>}, {"name": "total_records1", "value": <?php echo $total_records1; ?>});

                        },
                        "fnServerData": fnDataTablesPipeline,
                        "aoColumns": [null, null, null, {"sClass": "actiontd"}]

                    });

                    jQuery(".filter").on("click", function () {
                        $(".active-filter").removeClass("active-filter");
                        $(this).addClass("active-filter");
                        var id = this.id;
                        $('#status_active').val(id);
                        oTable.fnDraw();
                    });
                    jQuery("#filterlocation").on("change", function () {
                        oTable.fnDraw();
                    });
                    jQuery(".table_loader").css("display", "none");
                    jQuery(".datatable_container").css("display", "block");


                    $(document).on("click", "#popupyes", function () {
                        var linkurl = jQuery(this).attr("link");
                        $.fancybox.close();
                        //window.location.href = linkurl;
                        $.ajax({
                            type: "POST",
                            url: linkurl,
                            //data: '<?= WEB_PATH ?>/merchant/' + linkurl,
                            async: false,
                            success: function (msg)
                            {
                                var obj = $.parseJSON(msg);
                                if (obj.status == 'true') {

                                    oCache.iCacheLower = -1;
                                    oTable.fnDraw();
                                    $('.table_errore_message').text(obj.message);
                                }
                            }
                        });

                    });

                    $(document).on("click", '.commonclass', function () {
                        var linkurl = $(this).attr("link");
                        var act = $(this).text();
                        var flag = 0;
                        var chkclass = jQuery(this).hasClass("delete_dist_list");
                        //alert(chkclass);
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
                                    if (chkclass == false)
                                    {

                                        //window.location.href = "<?= WEB_PATH ?>/merchant/" + linkurl;

                                        if (act == "Deactivate" || act == 'Activate') {
                                            $.ajax({
                                                type: "POST",
                                                url: '<?= WEB_PATH ?>/merchant/' + linkurl,
                                                //data: '<?= WEB_PATH ?>/merchant/' + linkurl,
                                                async: false,
                                                success: function (msg)
                                                {
                                                    if (msg == 1) {
                                                        //console.log('ss');
                                                        flag = 1;
                                                    }
                                                }
                                            });
                                            if (flag == 1) {
                                                oCache.iCacheLower = -1;
                                                oTable.fnDraw();

                                            }
                                        } else {
                                            window.location.href = "<?= WEB_PATH ?>/merchant/" + linkurl;
                                        }

                                    }
                                    else
                                    {
                                        // dist list delete


                                        var msg = "<div style='width: 252px;'>Are you sure you want to delete customer list ?</div>";
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

                                        // dist list delete
                                    }

                                    //open_popup('expire');
                                }
                            }
                        });
                    });

                });
        </script>
    </head>

    <body >
        <div >

            <!---start header---->
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">

                <div  id="fadeshow11">

                    <!--end of slide--></div>
                <div id="content">

                    <div id="dialog-message" title="Message Box" style="display:none">

                    </div>


                    <div class="title_header"><?php echo urldecode($merchant_msg["manage-customers"]['Title_Manage_Customer_Distribution_List']); ?></div>

                    <div id="backdashboard1" > 
                        <?php
                        if ($_SESSION['merchant_info']['merchant_parent'] == 0) 
                        {

                                ?>
                                        <h4 align="right" style="float: right;" class="add_customer_list" >
											|<a class="dashboard-content1 addnew commonclass" link="segment-customers.php" href="javascript:void(0);">
												<?php echo $merchant_msg["manage-customers"]["Title_Add_New_Segment_Distribution_List"]; ?>
											</a>
											Customer List
										</h4>
                                        <h4 align="right" style="float: right;" class="add_customer_list" >
											<a class="dashboard-content1 addnew commonclass" link="import-customers.php" href="javascript:void(0);">
												<?php echo $merchant_msg["manage-customers"]["Title_Add_New_Distribution_List"]; ?>
											</a>
										</h4>
                                <?php
                        
                        } else {
                                if (in_array("add-group.php", $ass_page)) {
                                        ?>
										<h4 align="right" style="float: right;" class="add_customer_list" >
											|<a class="dashboard-content1 addnew commonclass" link="segment-customers.php" href="javascript:void(0);">
												<?php echo $merchant_msg["manage-customers"]["Title_Add_New_Segment_Distribution_List"]; ?>
											</a>Customer List
										</h4>		
                                        <h4 align="right" style="float: right;" class="add_customer_list" >
											<a class="dashboard-content1 addnew commonclass" link="import-customers.php" href="javascript:void(0);">
												<?php echo $merchant_msg["manage-customers"]["Title_Add_New_Distribution_List"]; ?>
											</a>
										</h4>
                                        
                                <?php
                                }
                        }
                        ?>
                    </div>
                    <div class="cls_clear"></div>
                    <?php
                    $Row_businessname = $_SESSION['merchant_info']['business'];

                    if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
                            $display_css = "";
                            $sloc = $merchant_msg["manage-customers"]["Field_select_location_merchant"];
                    } else {
                            $display_css = "cls_display_none";
                            $sloc = $merchant_msg["manage-customers"]["Field_select_location_emp"];
                    }
                    ?>
                    <div class="filterlocationclass" class="cls_display_none" style="display:none;">
                        <label class="locationclass" "><?php echo $sloc; ?>
<?php if ($_SESSION['merchant_info']['merchant_parent'] == 0) { ?>
                                    <select id="filterlocation">
                                        <option value="admin">--Merchant Admin--</option>
                                        <option value="all" selected="selected">--All Locations--</option>
                                        <?php
                                        foreach ($records_array_submerchant as $RSStore) {

                                                if ($all_loctions_counter[$RSStore->id] != 1) {
                                                        ?>

                                                        <option value="<?php echo $RSStore->id ?>" title="<?php echo $RSStore->address . "," . $RSStore->state . "," . $RSStore->city . "," . $RSStore->zip; ?>">

                                                            <?php
                                                            $location_string = $RSStore->address . "," . $RSStore->state . "," . $RSStore->city . "," . $RSStore->zip;
                                                            $location_string = (strlen($location_string) > 57) ? substr($location_string, 0, 57) . '...' : $location_string;

                                                            echo $location_string;
                                                            ?>

                                                        </option>

                                                        <?php
                                                }
                                        }
                                        $location_employee = "";
                                        ?>

                                    </select>
                                    <?php
                            } else {
                                    if ($total_records_submerchant > 0) {

                                            foreach ($records_array_submerchant as $RSStore) {

                                                    $sub_location_id = $RSStore->id;
                                                    //echo $RSStore->location_name;
                                                    echo $RSStore->address;
                                            }
                                    }
                                    $location_employee = $RSStore->id;
                            }
                            ?>
                            <input type="hidden" name="hdn_employee_location" id="hdn_employee_location" value="<?php echo $location_employee; ?>" />
                    </div>
                    <tr>
                        <td width="100%" align="left" valign="top">

                            <div id="upload_business_logo_ajax1" class="upload_business_logo_ajax nax_scrape_indicator hidden_elem" style="display:none;">
                                <img height="32" width="32" alt="" src="<?php echo ASSETS_IMG ?>/24.GIF" >
                            </div>	
                            <div align="center">
                                <img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="table_loader defaul_table_loader" />
                            </div>
                            <div id="div_manage_customer_list_table"> 
                                <div class="datatable_container" style="display:none">
                                    <input type="hidden" name="status_active" id="status_active" value="all" />

                                    <table border="0" class="tableMerchant" id="manage_customer_list_table">
                                        <thead>
                                            <tr role="row" >
                                                <td colspan="4" align="center" rowspan="1" style="height:26px;">
                                                    <b>Filter By : <span class="notification_tooltip" title="<?php echo $merchant_msg["manage-customers"]["Tooltip_Status"]; ?>" >&nbsp;&nbsp;&nbsp</span> </b>
                                                    <a id="all" class="filter active-filter" href="javascript:void(0)" ><?php echo $merchant_msg["manage-customers"]["Field_Satus_All"]; ?> </a> | 
                                                    <a id="active-inuse" class="filter"  href="javascript:void(0)"  > <?php echo $merchant_msg["manage-customers"]["Field_Satus_Active-In-Use"]; ?> </a> | 
                                                    <a id="active-notinuse" class="filter" href="javascript:void(0)" > <?php echo $merchant_msg["manage-customers"]["Field_Satus_Active-Not-In-Use"]; ?></a>
                                                    <!--<a id="not-active" class="filter" href="javascript:void(0)"  > <?php echo $merchant_msg["manage-customers"]["Field_Satus_Not-Active"]; ?></a></td>-->
                                            </tr>
                                            <tr> 
                                                <td colspan="4" align="left" class="table_errore_message" style="height:26px;">
                                                    <?php
                                                    echo $_SESSION['msg'];
                                                    ?>&nbsp;
                                                </td>
                                            </tr>
                                            <tr>
                                                <th ><?php echo $merchant_msg["manage-customers"]["Field_Distribution_List"]; ?></th>
                                               
                                                <th ><?php echo $merchant_msg["manage-customers"]["Field_Total_Customers"]; ?></th>
                                                <th ><?php echo $merchant_msg["manage-customers"]["Field_Status"]; ?></th>
                                                <th ><?php echo $merchant_msg["manage-customers"]["Field_Actions"]; ?></th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>

                        </td>
                    </tr>
                    <!--</table>-->
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
        <script>
                $("input[type=radio][name=image_type]").click(function () {
                    $("#img_type").val($(this).val());
                });
        </script>

        <div id="message-window" title="Message Box" style="display:none">

        </div>
        <div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
            <div id="NotificationBackDiv" class="divBack">
            </div>
            <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">

                <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading">

                    <div id="NotificationmainContainer" class="innerContainer" >
                        <img src="<?= ASSETS_IMG ?>/loading.gif" style="display: block;" id="image_loader_div"/>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script type="text/javascript">
        $("a#myaccount").css("background-color", "orange");
        /******* Close More action div block click any where in the window if block open *****/
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
        show_tooltip();
        function show_tooltip() {
            jQuery('.notification_tooltip').tooltip({
                content: function () {
                    return $(this).attr('title');
                },
                track: true,
                delay: 0,
                showURL: false,
                showBody: "<br>",
                fade: 250
            });
        }

        /******* Open More action div block on click more action link *****/
        //$('.actiontd').live("click",function(){
        $(document).on("click touchstart", 'td.actiontd', function () {
            if ($(this).find('#actiondiv').css('display') == 'none')
            {

                $('.actiondivclass').css("display", "none");
                $(this).find('#actiondiv').slideDown("slow");

            }
            else
                $(this).find('#actiondiv').slideUp('slow');
            /*
             },function(){
             $(this).find('#actiondiv').css('display','none');  */
        });
        //}


        //jQuery("#popupcancel").on("click", function () {
		jQuery(document).on( "click","#popupcancel",function () {	
            jQuery.fancybox.close();
            return false;
        });



        function show_error(message)
        {
            var alert_msg = message;
            var head_msg = "<div class='head_msg'>Message</div>";
            var footer_msg = "<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
            //  alert("There is no Package assigned to you. Please contact Scanflip account executive for your package subscription.");
            return false;
            // alert(message);
        }
        /* Timer */
        var timer = 0;
        function set_interval() {
            alert("interval set");
            // the interval 'timer' is set as soon as the page loads
            timer = setInterval("auto_logout()", 1200000);
            // the figure '10000' above indicates how many milliseconds the timer be set to.
            // Eg: to set it to 5 mins, calculate 5min = 5x60 = 300 sec = 300,000 millisec.
            // So set it to 300000
        }

        function reset_interval() {
            //resets the timer. The timer is reset on each of the below events:
            // 1. mousemove   2. mouseclick   3. key press 4. scroliing
            //first step: clear the existing timer

            if (timer != 0) {
                clearInterval(timer);
                timer = 0;
                // second step: implement the timer again
                timer = setInterval("auto_logout()", 1200000);
                // completed the reset of the timer
            }
        }

        function auto_logout() {
            // this function will redirect the user to the logout script
            window.location = "<?php echo WEB_PATH; ?>/merchant/register.php";
        }

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

</script>
