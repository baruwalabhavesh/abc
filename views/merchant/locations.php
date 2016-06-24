<?php
/**
 * @uses display locations
 * @used in pages :add-location.php,edit-location.php,process.php,my-account-left.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$array_where = array();
$array_where_user['id'] = $_SESSION['merchant_id'];
$RS_User = $objDB->Show("merchant_user", $array_where_user);
$m_parent = $RS_User->fields['merchant_parent'];

if ($RS_User->fields['merchant_parent'] == 0) {
        $array_where['created_by'] = $_SESSION['merchant_id'];
        $order_by = " ORDER BY id DESC ";
        /*         * ***** Get all location list of login merchant user ************ */

        //echo WEB_PATH.'/merchant/process.php?btnGetAllLocationforgrid=yes&mer_id='.$_SESSION['merchant_id'];

        /* $arr = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationforgrid=yes&mer_id=' . $_SESSION['merchant_id']);
          if (trim($arr[0]) == "") {
          unset($arr[0]);
          $arr = array_values($arr);
          }
          $json = json_decode($arr[0]);
          $total_records = $json->total_records;
          $records_array = $json->records; */
}
//permission code
$array = $json_array = array();
$array1 = $json_array = array();
$array['id'] = $_SESSION['merchant_id'];


$RS = $objDB->Show("merchant_user", $array);
$merchant_parent_value = $RS->fields['merchant_parent'];

$array1['id'] = $merchant_parent_value;
$RS1 = $objDB->Show("merchant_user", $array1);
//End of permission code
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Manage Locations</title>
        <?php //require_once(MRCH_LAYOUT . "/head.php"); ?>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
    </head>
   
    <body>

        <div>

           <!-- <style type="text/css" title="currentStyle">
                @import "<?php //echo ASSETS_CSS ?>/m/demo_page.css";
                @import "<?php //echo ASSETS_CSS ?>/m/demo_table.css"; 
            </style> -->
			<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
			<link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
            <script type="text/javascript" src="<?= ASSETS_JS ?>/bootstrap.min.js"></script>
           
           <!-- <script type="text/javascript" language="javascript" src="<?php //echo ASSETS_JS ?>/m/jquery.dataTables.js"></script> -->
			<script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
           <!--<script type="text/javascript" language="javascript" src="<?php //echo ASSETS_JS ?>/m/jquery.carouFredSel-6.2.1-packed.js"></script> -->
            <script type="text/javascript" charset="utf-8">
                    /******* initialize jQuery data table ****/
                    $(document).ready(function () {

                        jQuery(".table_loader").css("display", "none");
                        jQuery(".datatable_container").css("display", "block");
                    });
            </script>
            <!---start header---->
            <div>
                <?php
                // include header file from merchant/template directory 
                require_once(MRCH_LAYOUT . "/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">

                <div id="content">


                    <h3><?php echo $merchant_msg['locations']['manage_locations']; ?></h3>
                    <?php
                    if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
                            ?>
                            <!--<div id="backdashboard">
                                <a id="dashboard" href="<?//= WEB_PATH ?>/merchant/my-account.php"><img src="<?//= ASSETS_IMG ?>/m/back_dashboard.png" /></a>
                            </div> -->
                            <h4 align="right"  class="add_new_location"><a class="clsAddLocation" href="javascript:void(0);"><?php echo $merchant_msg['locations']['add_new_location']; ?></a></h4>
                            <?php
                            if ($total_records > 0) {
                                    ?>
                                    <?php
                            }
                            ?>
                            <?php
                            if ($total_records > 0) {
                                    
                            }
                            ?> 
                    <?php } ?>
                    <?php
                    if ($_SESSION['merchant_id'] != "") {
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
                                            $ass_role = unserialize($Row_role->ass_role);
                                            //foreach ($ass_page as $op_page)
                                            //{
                                            if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
                                                    ?>
                                                   <!-- <div id="backdashboard">
                                                        <a id="dashboard" href="<?//= WEB_PATH ?>/merchant/my-account.php"><img src="<?//= ASSETS_IMG ?>/m//back_dashboard.png" /></a>
                                                    </div> -->
                                                    <h4 align="right" class="add_new_location"><a href="<?= WEB_PATH ?>/merchant/add-location.php"><?php echo $merchant_msg['locations']['add_new_location']; ?></a></h4>
                                                    <h4 align="right" class="add_new_location"><a href="<?= WEB_PATH ?>/merchant/add-location-detail.php"><?php echo $merchant_msg['locations']['add_location_detail']; ?></a></h4>
                                                    <?php
                                            }
                                    }
                            } else {
                                    //echo ""; 
                            }
                            ?>
                    <?php } ?>
                    <div id="upload_business_logo_ajax1" class="upload_business_logo_ajax nax_scrape_indicator hidden_elem" style="display:none;">
                        <img height="32" width="32" alt="" src="<?php echo ASSETS_IMG . "/24" ?>.GIF" >
                    </div>
                    <!--<div align="center">
                        <img   src="<?php //echo ASSETS_IMG . "/32" ?>.GIF" class="table_loader defaul_table_loader" />
                    </div> -->
                    <input type="hidden" id="lstatus" value="0" />
                    <div class="datatable_container" style="display: none;">	
                        <div >  
                            <table border="0"  class="tableMerchant"  id="manage_location_table">
                                <thead>
                                    <tr role="row" >
                                        <td colspan="6" align="center" rowspan="1" style="height:26px;" >
                                            <b>Filter by location status <span class="notification_tooltip" title="<?php echo $merchant_msg["locations"]["Tooltip_Loction_Status"]; ?>"  >&nbsp;&nbsp;&nbsp</span>: </b>
                                            <a id="all" class="filter active-filter" href="javascript:void(0)" ><?php echo $merchant_msg["manage-customers"]["Field_Satus_All"]; ?> </a> | 
                                            <a id="active-inuse" class="filter"  href="javascript:void(0)"  > <?php echo $merchant_msg["manage-customers"]["Field_Satus_Active-In-Use"]; ?> </a> | 
                                           
                                            <a id="not-active" class="filter" href="javascript:void(0)"  > <?php echo $merchant_msg["manage-customers"]["Field_Satus_Not-Active"]; ?></a></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" align="left" class="table_errore_message " style="height:26px;"><?php
                                            if (isset($_SESSION['msg'])) {
                                                    echo $_SESSION['msg'];
                                            }
                                            ?><?php
                                            if (isset($_SESSION['loc_detail_msg'])) {
                                                    echo $_SESSION['loc_detail_msg'];
                                            }
                                            ?>&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <th>Address</th>
                                        <th>Phone Number</th>
                                        <th>Location Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                    <div class="clear">&nbsp;</div><br>

                    <!--end of content--></div>
                <!--end of contentContainer--></div>

            <!---------start footer--------------->
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                ?>
                <!--end of footer--></div>

        </div>
        <div id="dialog-message" title="Message Box" style="display:none">

        </div>

        <!--// 369-->

        <div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
            <div id="NotificationBackDiv" class="divBack">
            </div>
            <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">

                <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading" >

                    <div id="NotificationmainContainer" class="innerContainer" >
                        <img src="<?= ASSETS_IMG ?>/loading.gif" style="display: block;" id="image_loader_div"/>
                    </div>
                </div>
            </div>
        </div>

        <div id="sharingPopUpContainer" class="container_popup"  style="display: none;">
            <div id="sharingBackDiv" class="divBack"></div>
            <div id="sharingFrontDivProcessing" class="Processing" style="display:none;">                                            
                <div id="sharingMaindivLoading" align="center" valign="middle" class="imgDivLoading" >

                    <div id="sharingmainContainer" class="" >
                        <div class="main_content">
                            <div class="message-box message-success" id="jqReviewHelpfulMessagesharing" >
                                <div class="campaign_detail_div" >
                                    <img src="<?php echo ASSETS_IMG ?>/m/<?php echo $merchant_msg["common"]["loader_image_name"]; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="venue_id" id="venue_id" value="" />
        <input type="hidden" name="hdn_location_id" id="hdn_location_id" value="" />

        <script type="text/javascript">
                $("a#locations").css("background-color", "orange");
        </script>

        <script type="text/javascript">

                var merchant_parent_value = "<?php echo $merchant_parent_value; ?>";
                var main_merchant_approve = "<?php echo $RS->fields['approve']; ?>";

                var sub_merchant_approve = "<?php echo $RS1->fields['approve']; ?>";
                if (merchant_parent_value == 0)
                {
                    if (main_merchant_approve == 2)
                    {
                        jQuery(".add_new_location").hide();

                    }

                }
                else
                {
                    if (sub_merchant_approve == 2)
                    {
                        //alert("second");
                        jQuery(".add_new_location").hide();

                    }
                }


                //});
               jQuery("body").click
                        (
                                function (e)
                                {

                                    if ((e.target.className).trim() == "actiontd")
                                    {

                                    }
                                    else
                                    {
										//alert("hi");
                                        jQuery('.actiontd').find('#actiondiv').slideUp("slow");
                                    }
                                }
                        );
                function bind_more_action_click() 
                {
					var isMobile = {
							Android: function() {
								return navigator.userAgent.match(/Android/i);
							},
							BlackBerry: function() {
								return navigator.userAgent.match(/BlackBerry/i);
							},
							iOS: function() {
								return navigator.userAgent.match(/iPhone|iPad|iPod/i);
							},
							Opera: function() {
								return navigator.userAgent.match(/Opera Mini/i);
							},
							Windows: function() {
								return navigator.userAgent.match(/IEMobile/i);
							},
							any: function() {
								return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
							}
						};	
					if( isMobile.iOS() || isMobile.Android()) 
					{
						//alert("ipad");	
						jQuery(document).on("touchstart",'td.actiontd', function () {
							
							if (jQuery(this).find('#actiondiv').css('display') == 'none')
							{
								//alert("open");
								jQuery('.actiondivclass').css("display", "none");
								//$(this).find('#actiondiv').css('display','block');
								jQuery(this).find('#actiondiv').slideDown("slow");
							}
							else
							{
								//alert("close");
								jQuery(this).find('#actiondiv').slideUp('slow');
							}

						});
						jQuery(document).on("touchstart",'body',
					
							function (e)
							{
								if((e.target.className).trim() == "actiontd")
								//if ((e.target.className) == "actiontd ")
								{

								}
								else
								{
									jQuery('.actiontd').find('#actiondiv').slideUp("slow");
								}
							}
						);
					}
					else
					{
						//alert("windows");		
						jQuery(document).on("click",'td.actiontd', function () {
						
								if (jQuery(this).find('#actiondiv').css('display') == 'none')
								{
									
									jQuery('.actiondivclass').css("display", "none");
									//$(this).find('#actiondiv').css('display','block');
									jQuery(this).find('#actiondiv').slideDown("slow");
								}
								else
								{
									
									jQuery(this).find('#actiondiv').slideUp('slow');
								}
								/*
								 },function(){
								 $(this).find('#actiondiv').css('display','none');  */
						});
					}
				}
                //}
                bind_more_action_click();

                /*********** before go to add location page  check  whether max number Of location limit is reached or not **************/
                $(".clsAddLocation").click(function () {

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

                                $.ajax({
                                    type: "POST",
                                    url: 'process.php',
                                    data: 'btnGetMaxNumberOfLocation=true',
                                    success: function (msg)
                                    {
                                        var obj = jQuery.parseJSON(msg);
                                        if (obj.status == "true")
                                        {
                                            window.location.href = "<?= WEB_PATH ?>/merchant/add-location.php";
                                        }
                                        else
                                        {

                                            //alert(obj.message);
                                            var head_msg = "<div class='head_msg'>Message Box</div>"
                                            var content_msg = "<div class='content_msg'>" + obj.message + "</div>";
                                            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                                            jQuery.fancybox({
                                                content: jQuery('#dialog-message').html(),
                                                type: 'html',
                                                openSpeed: 300,
                                                closeSpeed: 300,
                                                changeFade: 'fast',
                                                helpers: {
                                                    overlay: {
                                                        opacity: 0.3
                                                    } // overlay
                                                }
                                            });

                                            return false;
                                        }
                                    }
                                });


                            }
                        }
                    });




                });
                
                jQuery("#popupcancel").live("click", function () {
                    jQuery.fancybox.close();
                    return false;
                });

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


                var oTable = $('#manage_location_table').dataTable({
                    "bFilter": false,
					"bSort" : false,
					"bLengthChange": false,
					"info": false,
                    //"sPaginationType": "full_numbers",
                    "bProcessing": true,
                    "bServerSide": true,
                    "iDisplayLength": 10,
                    "oLanguage": {
						"sEmptyTable": "No Locations founds in the system. Please add at least one.",
						"sZeroRecords": "No Locations to display",
                        "sProcessing": "Loading..."
                    },
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
                    //"iDeferLoading": <?php //echo $total_records1;                ?>,
                    "fnServerParams": function (aoData) {
                        aoData.push({"name": "btnGetAllLocationforgrid", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'}, {"name": "lstatus", "value": $('#lstatus').val()});
						//bind_more_action_click();

                    },
                    "fnServerData": fnDataTablesPipeline,
                    "aoColumns": [
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false},                      
                        {"bVisible": true, "bSearchable": false, "bSortable": false},
                        {"bVisible": true, "bSearchable": false, "bSortable": false, "sClass": "actiontd"},
                    ]

                });

		/***** Check merchant session is active or not before perform any more action link **********/
		var flag = 0;
                $(document).on("click",'.commonclass', function () {
                    var linkurl = $(this).attr("link");
		    var act = $(this).text();
				
                    $.ajax({
                        type: "POST",
                        url: 'process.php',
                        data: 'loginornot=true',
                        async: false,
                        success: function (msg)
                        {


                            var obj = $.parseJSON(msg);


                            if (obj.status == "false")
                            {

                                window.location.href = obj.link;
                            }
                            else
                            {
				if(act == "Deactivate" || act == 'Activate'){
		                        //window.location.href = "<?= WEB_PATH ?>/merchant/" + linkurl;
					 $.ajax({
						type: "POST",
						url: '<?= WEB_PATH ?>/merchant/' + linkurl,
						//data: '<?= WEB_PATH ?>/merchant/' + linkurl,
						async: false,
						success: function (msg)
						{
							if(msg ==1){
								//console.log('ss');
								flag =1;						
							}
						}
					});
					if(flag ==1){
						oCache.iCacheLower = -1;
					      	oTable.fnDraw();
					
					}
				}else{
					window.location.href = "<?= WEB_PATH ?>/merchant/" + linkurl;
				}				

                            }
                        }
                    });
                });

		
                /******** filter data according to location status ************/
                jQuery(".filter").live("click", function () {

                    jQuery(".active-filter").removeClass("active-filter");
                    jQuery(this).addClass("active-filter");

                    //var oTable = jQuery('#manage_location_table').DataTable();

                    if (jQuery(this).attr("id") == "active-inuse")
                    {
                        $('#lstatus').val('1');
                        oTable.fnDraw();
                        //searchTerm = 'Active - In Use';
                        //oTable.fnFilter("^" + searchTerm + "$", 3, true, false);
                    }
                    else if (jQuery(this).attr("id") == "active-notinuse")
                    {
                        $('#lstatus').val('2');
                        oTable.fnDraw();
                        //searchTerm = 'Active - Not In Use';
                        //oTable.fnFilter("^" + searchTerm + "$", 3, true, false);
                    }
                    else if (jQuery(this).attr("id") == "not-active")
                    {
                        $('#lstatus').val('3');
                        oTable.fnDraw();
                        //searchTerm = 'Not - Active';
                        //oTable.fnFilter("^" + searchTerm + "$", 3, true, false);
                    }
                    else if (jQuery(this).attr("id") == "all")
                    {
                        $('#lstatus').val('0');
                        oTable.fnDraw();
                        //searchTerm = 'Active - In Use'; 
                        /*var oSettings = oTable.fnSettings();
                         for (iCol = 0; iCol < oSettings.aoPreSearchCols.length; iCol++)
                         {
                         oSettings.aoPreSearchCols[ iCol ].sSearch = '';
                         }
                         oSettings.oPreviousSearch.sSearch = '';
                         oTable.fnDraw();*/
                    }



                });
                function show_tooltip()
                {
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
                show_tooltip();
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
                jQuery(".connectlocu").live("click", function () {
                    open_popup('sharing');
                    var customer_id = "<?php echo $_SESSION['merchant_id']; ?>";


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
                                $.ajax({
                                    type: "POST",
                                    url: '<?php echo WEB_PATH; ?>/locu_share_merchant.php',
                                    data: 'customer_id=' + customer_id,
                                    // async:false,
                                    success: function (msg)
                                    {

                                        close_popup('sharing');
                                        var data_arr = jQuery.trim(msg).split("###");
                                        if (data_arr[0] == "success")
                                        {

                                            if (data_arr[11] == "notgetlocation")
                                            {
                                                var msg = "<?php echo $merchant_msg['locations']['Msg_detected_menu_price_list1']; ?><span class='cls_bold'>" + data_arr[10] + "</span>";
                                            }
                                            else
                                            {
                                                var msg = "<?php echo $merchant_msg['locations']['Msg_detected_menu_price_list1']; ?><span class='cls_bold'>" + data_arr[10] + "</span><?php echo $merchant_msg['locations']['Msg_detected_menu_price_list2']; ?>";
                                            }
                                            var location_detail = data_arr[9];
                                            jQuery("#hdn_location_id").val(data_arr[2]);
                                            jQuery("#venue_id").val(data_arr[1]);
                                            var head_msg = "<div class='head_msg'>Message Box</div>";
                                            if (data_arr[11] == "notgetlocation")
                                            {
                                                var content_msg = "<div class='content_msg'>" + msg + location_detail + "</div>";
                                            }
                                            else
                                            {
                                                var content_msg = "<div class='content_msg'>" + msg + location_detail + "</div>";
                                            }
                                            var footer_msg = "<div><hr><input type='button'  value='Save & continue' id='popupsave' name='popupsave' class='msg_popup_save' /><input type='button'  value='Cancel' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                                var head_msg = "<div  class='head_msg'>Message Box</div>"
                                                var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                                var footer_msg = "<div ><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                                var head_msg = "<div class='head_msg'>Message Box</div>";
                                                var content_msg = "<div class='content_msg'>" + msg + location_detail + "</div>";
                                                var footer_msg = "<div class='locu_content'>Please <a href='https://locu.com/'>click here</a><?php echo $merchant_msg['locations']['Msg_no_find_any_location2']; ?></div><div ><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                        }
                    });


                });
                jQuery("#popupcancel").live("click", function () {
                    jQuery.fancybox.close();

                });
                jQuery("#popupsave").live("click", function () {
                    jQuery.fancybox.close();
                    open_popup('sharing');
                    var customer_id = "<?php echo $_SESSION['merchant_id']; ?>";
                    var location_id = [];

                    jQuery("input[type=checkbox]:checked").each(function () {

                        location_id.push(jQuery(this).val());
                    });

                    location_id.push(jQuery("#hdn_location_id").val());
                    var venue_id = jQuery("#venue_id").val();
                    jQuery.ajax({
                        type: "POST",
                        //url:'<?php echo WEB_PATH; ?>/merchant/locu_venue_save.php',
                        url: '<?php echo WEB_PATH; ?>/locu_share_merchant.php',
                        data: 'location_id=' + location_id + '&customer_id=' + customer_id + '&venue_id=' + venue_id + '&savecontinue=yes',
                        // async:false,
                        success: function (msg)
                        {

                            close_popup('sharing');
                            var data_arr = jQuery.trim(msg).split("###");
                            if (data_arr[0] == "success")
                            {

                                if (data_arr[11] == "notgetlocation")
                                {
                                    var msg = "<?php echo $merchant_msg['locations']['Msg_detected_menu_price_list1']; ?><span class='cls_bold'>" + data_arr[10] + "</span>";
                                }
                                else
                                {
                                    var msg = "<?php echo $merchant_msg['locations']['Msg_detected_menu_price_list1']; ?><span  class='cls_bold'>" + data_arr[10] + "</span><?php echo $merchant_msg['locations']['Msg_detected_menu_price_list2']; ?>";
                                }
                                var location_detail = data_arr[9];
                                jQuery("#hdn_location_id").val(data_arr[2]);
                                jQuery("#venue_id").val(data_arr[1]);
                                var head_msg = "<div class='head_msg'>Message Box</div>";
                                if (data_arr[11] == "notgetlocation")
                                {
                                    var content_msg = "<div class='content_msg'>" + msg + location_detail + "</div>";
                                }
                                else
                                {
                                    var content_msg = "<div class='locu_content_msg2'>" + msg + location_detail + "</div>";
                                }
                                var footer_msg = "<div ><hr><input type='button'  value='Save & continue' id='popupsave' name='popupsave' class='msg_popup_save' /><input type='button'  value='Cancel' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                    var head_msg = "<div class='head_msg'>Message Box</div>"
                                    var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                    var footer_msg = "<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel' ></div>";
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
                                    var head_msg = "<div class='head_msg'>Message Box</div>";
                                    var content_msg = "<div class='content_msg'>" + msg + location_detail + "</div>";
                                    var footer_msg = "<div class='locu_content'>Please <a href='https://locu.com/'>click here</a><?php echo $merchant_msg['locations']['Msg_no_find_any_location2']; ?></div><div ><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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

                $('.updatemenu').live("click", function () {
                    var linkurl = jQuery(this).attr("link");
                    //  open_popup('sharing'); 
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



                                var customer_id = "<?php echo $_SESSION['merchant_id']; ?>";
                                var location_id = linkurl;

                                //var venue_id=jQuery("#venue_id").val();
                                jQuery.ajax({
                                    type: "POST",
                                    //url:'<?php echo WEB_PATH; ?>/merchant/locu_venue_save.php',
                                    url: '<?php echo WEB_PATH; ?>/locu_venue_save.php',
                                    data: 'location_id=' + location_id + '&customer_id=' + customer_id,
                                    // async:false,
                                    success: function (msg)
                                    {

                                        close_popup('sharing');
                                        if (msg == "urlblank")
                                        {

                                            var msg = "<?php echo $merchant_msg['locations']['Msg_update_menu_no']; ?>";
                                            var head_msg = "<div class='head_msg'>Message Box</div>"
                                            var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                            var footer_msg = "<div ><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                        else if (msg == "noupdate")
                                        {


                                            var msg = "<?php echo $merchant_msg['locations']['Msg_update_menu_hase_no']; ?>";
                                            var head_msg = "<div  class='head_msg'>Message Box</div>"
                                            var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                            var footer_msg = "<div ><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                        else {

                                            var msg = "<?php echo $merchant_msg['locations']['Msg_update_menu_hase_yes']; ?>";
                                            var head_msg = "<div class='head_msg'>Message Box</div>"
                                            var content_msg = "<div class='content_msg'>" + msg + "</div>";
                                            var footer_msg = "<div ><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
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
                                });

                            }
                        }
                    });
                });
        </script>
        <?php
        $_SESSION['msg'] = "";
        ?>
        <!--// 369-->
    </body>
</html>
