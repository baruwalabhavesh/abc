<?php
/**
 * @uses display campaigns list
 * @used in pages : add-compaigns.php,add-online-compaign.php,add-user.php,edit-compaign.php,edit-user.php,process.php,my-account-left.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH . "/classes/DB.php");

require_once(LIBRARY . "/class.phpmailer.php");
include_once(ROOT . "/services/active-campaign-scedular.php");

/* * ****** Check inserted campaign will go where active , active-in-future , pause ********** */


//http://www.scanflip.com/merchant/process.php?check_campaign_status=yes&cid=924&mer_id=17
if (isset($_SESSION['msg']) && isset($_SESSION['inserted_id'])) {
        if (isset($_SESSION['action']) && $_SESSION['action'] == "add") {

                $arr = file(WEB_PATH . '/merchant/process.php?check_campaign_status=yes&mer_id=' . $_SESSION['merchant_id'] . '&cid=' . $_SESSION['inserted_id']);
                if (trim($arr[0]) == "") {
                        unset($arr[0]);
                        $arr = array_values($arr);
                }
                $json = json_decode($arr[0]);
                $campaign_inserted_status = $json->campaign_status;
                if ($campaign_inserted_status == "active") {
                        $_SESSION['msg'] = $merchant_msg["edit-compaign"]['New_msg_active_campaign'];
                } else if ($campaign_inserted_status == "active-in-future") {
                        $_SESSION['msg'] = $merchant_msg["edit-compaign"]['New_msg_schedule_campaign'];
                } else if ($campaign_inserted_status == "pause") {
                        $_SESSION['msg'] = $merchant_msg["edit-compaign"]['New_msg_all_pause_campaign'];
                } else {
                        
                }
        } else if (isset($_SESSION['action']) && $_SESSION['action'] == "pause") {
                $arr = file(WEB_PATH . '/merchant/process.php?check_campaign_status=yes&mer_id=' . $_SESSION['merchant_id'] . '&cid=' . $_SESSION['inserted_id']);
                if (trim($arr[0]) == "") {
                        unset($arr[0]);
                        $arr = array_values($arr);
                }
                $json = json_decode($arr[0]);
                $campaign_inserted_status = $json->campaign_status;
                if ($campaign_inserted_status == "active") {
                        $_SESSION['msg'] = $merchant_msg["edit-compaign"]['New_msg_pause_campaign'];
                } else if ($campaign_inserted_status == "active-in-future") {
                        $_SESSION['msg'] = $merchant_msg["edit-compaign"]['New_msg_pause_campaign'];
                } else if ($campaign_inserted_status == "pause") {
                        $_SESSION['msg'] = $merchant_msg["edit-compaign"]['New_msg_all_pause_campaign'];
                } else {
                        $_SESSION['msg'] = $merchant_msg["edit-compaign"]['New_msg_pause_delete_campaign'];
                }
        }
}

/* * ****** Check inserted campaign will go where active , active-in-future , pause ********** */

/* * ****** 
  @USE : delete campaign logic( Remove all related data according to campaign)
 * ********* */

if ($_REQUEST['action'] == "delete") {
        $array_where = array();
        $array_where['id'] = $_REQUEST['id'];
        $objDBWrt->Remove("campaigns", $array_where);

        $array_where1['campaign_id'] = $_REQUEST['id'];
        $objDBWrt->Remove("campaign_location", $array_where1);

        $array_where2['campaign_id'] = $_REQUEST['id'];
        $objDBWrt->Remove("campaign_groups", $array_where2);

        $array_where3['campaign_id'] = $_REQUEST['id'];
        $objDBWrt->Remove("customer_campaigns", $array_where3);

        $array_where4['campaign_id'] = $_REQUEST['id'];
        $objDBWrt->Remove("reward_user", $array_where4);

        $array_where5['campaign_id'] = $_REQUEST['id'];
        $objDBWrt->Remove("activation_codes", $array_where5);

        $array_where6['customer_campaign_code'] = $_REQUEST['id'];
        $objDBWrt->Remove("coupon_codes", $array_where6);

        $_SESSION['msg'] = "Campaign has been deleted successfully";
        header("Location: " . WEB_PATH . "/merchant/compaigns.php?action=active");
        exit();
}

/* * ****** check if any active location is there or not if not then dont allow to add campaign  ***************** */
$active_locations = 1;
if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
//echo WEB_PATH . '/merchant/process.php?is_any_active_location=yes&merchant_id=' . $_SESSION['merchant_id'];
        $arr = file(WEB_PATH . '/merchant/process.php?is_any_active_location=yes&merchant_id=' . $_SESSION['merchant_id']);
        if (trim($arr[0]) == "") {
                unset($arr[0]);
                $arr = array_values($arr);
        }
        $json = json_decode($arr[0]);
        $active_locations = $json->active_locations;
}
/* * ****** check if any active location is there or not if not then dont allow to add campaign  ***************** */

/* * ******* get all sub merchant of current login employee **************** */
$arr = file(WEB_PATH . '/merchant/process.php?getallsubmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
if (trim($arr[0]) == "") {
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);
$ids = $json->all_sub_merchant_id;

$all_sub_mer_array = explode(",", $ids);
/* * ******* get all sub merchant of current login employee **************** */


/* * ******* get all sub merchant of current login employee **************** */
$arr = file(WEB_PATH . '/merchant/process.php?getallmainmercahnt_id=yes&mer_id=' . $_SESSION['merchant_id']);
if (trim($arr[0]) == "") {
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);
$all_main_merchant_id = $json->all_main_merchant_id;
$all_main_mer_array = explode(",", $all_main_merchant_id);
/* * **** get employee role ( which role is assigned to user ) ************** */
$arr = file(WEB_PATH . '/merchant/process.php?btnGetMerchantRole=yes&mer_id=' . $_SESSION['merchant_id']);
if (trim($arr[0]) == "") {
        unset($arr[0]);
        $arr = array_values($arr);
}
$json = json_decode($arr[0]);
$total_records1 = $json->total_records;
$records_array1 = $json->records;
$ass_page = array();
if ($total_records1 > 0) {

        foreach ($records_array1 as $Row_role) {
                $Row_role->merchant_user_id;
                $ass_page = unserialize($Row_role->ass_page);
                $ass_role = unserialize($Row_role->ass_role);
        }
} else {
        echo "";
}
//print_r($ass_page);
//permission code
$array = $json_array = array();
$array1 = $json_array = array();
/* $array['id'] = $_SESSION['merchant_id'];
  $RS = $objDB->Show("merchant_user", $array); */
$merchant_parent_value = $_SESSION['merchant_info']['merchant_parent'];
$array1['id'] = $merchant_parent_value;
$RS1 = $objDB->Show("merchant_user", $array1);
//print_r($RS1);
//End of permission code

$arr_c = file(WEB_PATH . '/merchant/process.php?num_campaigns_per_month=yes&mer_id=' . $_SESSION['merchant_id']);

if (trim($arr_c[0]) == "") {
		unset($arr_c[0]);
		$arr_c = array_values($arr_c);
}
$json = json_decode($arr_c[0]);

$total_campaigns = $json->total_records;
$addcampaign_status = $json->status;
$max_camapign = $json->max_campaigns;
	
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Cache-control" content="public,max-age=86400,must-revalidate">
        <title>ScanFlip | Manage Campaigns</title>
        <?php require_once(MRCH_LAYOUT . "/head.php"); ?>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
    </head>

    <body>

        <div >

            <link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
            <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
            <script type="text/javascript" charset="utf-8">
                    $a = jQuery.noConflict();
                    /******* initialize jQuery data table ****/
                    $a(document).ready(function () {

                        var oTable = $a('#manage_campaigns_table').dataTable({
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
                                "sEmptyTable": "No campaigns founds in the system.",
                                "sZeroRecords": "No campaigns to display",
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
                            //"iDeferLoading": <?php //echo $total_records1;                       ?>,
                            "fnServerParams": function (aoData) {
                                aoData.push({"name": "btnGetAllCampaignOfMerchant_allmonths", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'}, {"name": "status", "value": jQuery("#opt_filter_status").val()}, {"name": "year", "value": jQuery("#opt_filter_year").val()}, {"name": "month", "value": jQuery("#opt_filter_month").val()}, {"name": "category", "value": jQuery("#opt_filter_category").val()}, {"name": "location_id", "value": jQuery("#opt_filter_location").val()}, {"name": "action", "value": jQuery("#opt_filter_status").val()}, {"name": "all_submerchant_id", "value": "<?php echo $ids; ?>"}, {"name": "addcampaign_status", "value": "<?php echo $addcampaign_status; ?>"}, {"name": "all_sub_mer_array", "value": <?php echo json_encode($all_sub_mer_array); ?>}, {"name": "all_main_mer_array", "value": <?php echo json_encode($all_main_mer_array); ?>}, {"name": "ass_page", "value": <?php echo json_encode($ass_page); ?>}, {"name": "addcampaign_status", "value": "<?php echo $addcampaign_status; ?>"});
                                //jQuery( "td.actiontd" ).unbind( "click");
                                //jQuery( "td.actiontd" ).unbind( "touchstart");
                                //bind_more_action_click();

                            },
                            //"fnServerData": fnDataTablesPipeline,
                            "aoColumns": [null, null, null, null, {"sClass": "actiontd"}]

                        });
                        /*,{"name":"year","value":jQuery("#opt_filter_year").val()},{"name":"month","value":jQuery("#opt_filter_month").val()},{"name":"category","value":jQuery("#opt_filter_category").val()},{"name":"location_id","value":jQuery("#opt_filter_location").val()},{"name":"action","value":jQuery("#opt_filter_status").val()},{"name":"all_submerchant_id","value":"<?php echo $ids; ?>"},{"name":"addcampaign_status","value":"<?php echo $addcampaign_status; ?>"}*/


                        jQuery(".table_loader").css("display", "none");
                        jQuery(".textarea_container").css("display", "block");
                        jQuery("#btnfilterstaticscampaigns").click(function () {
                            var val_y = jQuery("#opt_filter_year").val();
                            var val_m = jQuery("#opt_filter_month").val();
                            var val_c = jQuery("#opt_filter_category").val();
                            var val_l = jQuery("#opt_filter_location").val();

                            var val_s = jQuery("#opt_filter_status").val();
                            oTable.fnDraw();
                        });

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
                    <div class="title_header"><?php echo $merchant_msg['edit-compaign']['manage_campaign']; ?></div>

                    <!-- In order to edit a campaign please select your campaign and then click on edit from  more action links. <br /> You can also create a new campaign by selecting <font color="#0066FF" style="font-size:1.2em" >Add New Campaign </font>button. -->
                    <?php
                    $arr_c = file(WEB_PATH . '/merchant/process.php?num_campaigns_per_month=yes&mer_id=' . $_SESSION['merchant_id']);

                    if (trim($arr_c[0]) == "") {
                            unset($arr_c[0]);
                            $arr_c = array_values($arr_c);
                    }
                    $json = json_decode($arr_c[0]);

                    $total_campaigns = $json->total_records;
                    $addcampaign_status = $json->status;
                    $max_camapign = $json->max_campaigns;
                    /*                     * *** 
                      @ Add Campaign button Validation:
                      - If main merchant then check whether number of active campaigns counter is reached fro month is reached or not . If yes the give error message
                      - If employee is login then check whether employee have permission to add campaign OR number  of active campaigns counter is reached fro month is reached or not then  give error message.
                      - If no active location is there in merchant account then merchant will no allow to add campaign
                     * **** */

                    if ($_SESSION['merchant_info']['merchant_parent'] == 0) {

                            if ($active_locations > 0) {
                                    if ($addcampaign_status == "true") {
                                            ?>
                                            <h4 align="right"  class="addcampignh4"><a link="add-compaign.php" class="commonclass" href="javascript:void(0);"><?php echo $merchant_msg['edit-compaign']['add_new_campaign']; ?></a></h4>
                                    <?php } else {
                                            ?>                            
                                            <h4 align="right" class="addcampignh4">
                                                <a href="javascript:void(0)" class="addcampaign_chk">Add New Campaign</a></h4>
                                            <?php
                                    }
                            } else {
                                    ?>
                                    <h4 align="right"  class="addcampignh4"><a href="javascript:void(0)" onClick="show_error('No active location available.')" >
                                            Add New Campaign</a></h4>
                                    <?php
                            }
                    } else {

                            if ($total_records1 > 0) {

                                    if (in_array("add-compaign.php", $ass_page)) {

                                            if ($addcampaign_status == "true") {
                                                    ?>                            
                                                    <h4 align="right"  class="addcampignh4"><a href="<?= WEB_PATH ?>/merchant/add-compaign.php"><?php echo $merchant_msg['edit-compaign']['add_new_campaign']; ?></a></h4>
                                                    <?php
                                            } else {
                                                    ?>                            
                                                    <h4 align="right"  class="addcampignh4">
                                                        <a href="javascript:void(0)" class="addcampaign_chk"><?php echo $merchant_msg['edit-compaign']['add_new_campaign']; ?></a></h4>
                                                    <?php
                                            }
                                    }
                                    // }
                            } else {
                                    echo "";
                            }
                            ?>
                            <?php
                    }
                    /**                     * *
                      @ Get location list for campaign grid
                      -	If main merchant then get all active locations
                      -	If location employee  is login  then  get employee \91s assigned location information
                     * ** */
                    if ($_SESSION['merchant_info']['merchant_parent'] != 0) {
                            $media_acc_array = array();
                            $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
                            $RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
                            $location_val = $RSmedia->fields['location_access'];
                            $arr1 = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $_SESSION['merchant_id'] . '&loc_id=' . $location_val);
                    } else {
                            $arr1 = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $_SESSION['merchant_id']);
                    }

                    if (trim($arr1[0]) == "") {
                            unset($arr1[0]);
                            $arr1 = array_values($arr1);
                    }
                    $json1 = json_decode($arr1[0]);
                    $total_records2 = $json1->total_records;
                    $records_array2 = $json1->records;
                    ?>

                    <div align="center">
                        <img   src="<?php echo ASSETS_IMG ?>/32.GIF" class="table_loader defaul_table_loader" />
                    </div>
                    <div class="textarea_container" style="display: none;">					
                        <div>	

                            <table border="0" cellspacing="1" cellpadding="10"  class="tableMerchant" id="manage_campaigns_table">
                                <thead>


                                <td colspan="5" align="left" class="filter_result table_filter_area"  >
                                    <input type="button" style="float:right" value="<?php echo $merchant_msg['report']['Campaign_Filter_Button']; ?>" name="btnfilterstaticscampaigns" id="btnfilterstaticscampaigns" />
                                    <div class="fltr_div_td">
                                        <div class="cls_filter">Filter By :</div>
                                        <div class="cls_filter_top">

                                            <b><?php echo $merchant_msg['report']['Campaign_Filter_Year'];  //echo "==".date('m');      ?></b>
                                            <select id="opt_filter_year" >
                                                <?php
                                                for ($y = date('Y') - 2; $y <= date('Y'); $y++) {
                                                        ?>
                                                        <option value="<?php echo $y; ?>" <?php
                                                        if ($y == date('Y')) {
                                                                echo "selected";
                                                        }
                                                        ?>  > <?php echo $y; ?> </option>
                                                        <?php }
                                                        ?>
                                            </select>
                                            <b>  <?php echo $merchant_msg['report']['Campaign_Filter_Month']; ?> </b>
                                            <select id="opt_filter_month" >
                                                <option value="0" selected  >---- ALL ----</option>
                                                <option value="01"  >January</option>
                                                <option value="02"  >February</option>
                                                <option value="03"  >March</option>
                                                <option value="04"  >April</option>
                                                <option value="05"  >May</option>
                                                <option value="06"  >Jun</option>
                                                <option value="07"  >July</option>
                                                <option value="08"  >August</option>
                                                <option value="09"  >September</option>
                                                <option value="10"  >October</option>
                                                <option value="11"  >November</option>
                                                <option value="12"  >December</option>
                                            </select>
                                            <b>  <?php echo $merchant_msg['report']['Campaign_Filter_Category']; ?></b>
                                            <select id="opt_filter_category" >
                                                <?php
                                                $array_cat = array();
                                                $array_cat['active'] = 1;
                                                $RSCat = $objDB->Show("categories", $array_cat);
                                                ?>
                                                <option value="0" selected="selected"  >--- ALL ---</option>
                                                <?
                                                while ($Row = $RSCat->FetchRow()) {
                                                ?>
                                                <option value="<?= $Row['id'] ?>"  ><?= $Row['cat_name'] ?></option>
                                                <?
                                                }
                                                ?>
                                            </select> 
                                            &nbsp;&nbsp;&nbsp;
                                            <b>	<?php echo $merchant_msg['report']['Campaign_Filter_Status']; ?></b>
                                            <select id="opt_filter_status" >active
                                                <option value="active" selected="selected" > --- Active---</option>
                                                <option value="endcampaigns"  > --- Paused  ---</option>
                                                <option value="activeinfuture" > --- Scheduled ---</option>
                                                <option value="expired"  > --- Expired ---</option>
                                            </select> 

                                        </div>
                                        <div class="cls_filter_bottom">

                                            <?php
                                            if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
                                                    ?>
                                                    <b>
                                                        <?php echo $merchant_msg['report']['Campaign_Filter_Loactions']; ?></b>
                                                    <select id="opt_filter_location">
                                                        <option value="0" selected="selected" > --- ALL ---</option>
                                                        <?php
                                                        // display location list for filter data according to location wise
                                                        if ($total_records2 > 0) {
                                                                $cnt = 0;
                                                                foreach ($records_array2 as $Row) {
                                                                        if ($Row->location_name == "") {
                                                                                $locname = $Row->address . " - " . $Row->zip;
                                                                        } else {
                                                                                $locname = $Row->location_name . " - " . $Row->zip;
                                                                        }
                                                                        $location_string = $Row->address . ", " . $Row->city . ", " . $Row->state . ", " . $Row->zip;
                                                                        $location_string = (strlen($location_string) > 57) ? substr($location_string, 0, 57) . '...' : $location_string;
                                                                        ?>
                                                                        <option title="<?php echo $Row->address . ", " . $Row->city . ", " . $Row->state . ", " . $Row->zip; ?>" value="<?php echo $Row->id; ?>" ><?php echo $location_string; ?> </option>
                                                                        <?php
                                                                }
                                                        }
                                                        ?>
                                                    </select>
                                                    <?php
                                            } else {
                                                    ?>
                                                    <input type="hidden"  name="opt_filter_location" id="opt_filter_location" value="<?php echo $location_val; ?>" />
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>

                                </tr>
                                <tr>
                                    <td colspan="5" align="left" class="table_errore_message"><span id="error_message"><?php if (isset($_SESSION['msg'])) echo $_SESSION['msg']; ?></span>&nbsp;</td>
                                </tr>
                                <tr>
                                    <th >Campaign Name</th>
                                    <th  >Category</th>

                                    <th >Start Date</th>
                                    <th>Expiration Date</th>
                                    <?php ?>
                                    <th >Actions</th>

                                </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                    <div class="clear">&nbsp;</div>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>

            <!---------start footer--------------->
            <div>
                <?php
                //include footer file from merchant/template  folder
                require_once(MRCH_LAYOUT . "/footer.php");
                ?>
                <!--end of footer--></div>

        </div>
        <div style="display:none">
            <div class="campaign_detail_div">
            </div>
            <div class="campaign_expire_div" >
            </div> 
        </div>


        <div id="message-window" title="Message Box" style="display:none">

        </div>

        <div id="dialog-message" title="Message Box" style="display:none">

        </div>

        <?php
        $_SESSION['msg'] = "";
        $_SESSION['inserted_id'] = "";
        $_SESSION['action'] = "";
        ?>  <div id="dialog-message" title="Message" style="display:none">
        </div>
        <div id="Notification1PopUpContainer" class="container_popup"  style="display: none;">
            <div id="Notification1BackDiv" class="divBack">
            </div>
            <div id="Notification1FrontDivProcessing" class="Processing" style="display:none;">

                <div id="Notification1MaindivLoading" align="center" valign="middle" class="imgDivLoading"
                     >

                    <div id="Notification1mainContainer" class="innerContainer" style="height:auto;width:auto">
                        <img src="<?= ASSETS_IMG ?>/loading.gif" style="display: block;" id="image_loader_div"/>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<script>
	setInterval(function() {
      jQuery("#error_message").text('');
}, 10000);

        jQuery(document).ready(function () {
		

            /**** If number of active campaigns per month limit is over the this method is called ****/
            jQuery(".addcampaign_chk").live("click", function () {
                var msg = "At present you can only add <b> <?php echo $max_camapign; ?> </b> campaign per month.<br /> Please contact scanflip sales team if you wish to increase number of campaigns for your account.";
                jQuery("#error_message").text('');

                var head_msg = "<div class='head_msg'>Message Box</div>";
                var content_msg = "<div class='content_msg'>" + msg + "</div>";
                var footer_msg = "<div><hr><input class='msg_popup_cancel' type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' ></div>";
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

            });
        });


        var merchant_parent_value = "<?php echo $merchant_parent_value; ?>";
        var main_merchant_approve = "<?php echo $_SESSION['merchant_info']['approve']; ?>";

        var sub_merchant_approve = "<?php echo $RS1->fields['approve']; ?>";
        //alert(merchant_parent_value);
        //alert(main_merchant_approve);
        //alert(sub_merchant_approve);
        if (merchant_parent_value == 0)
        {
            if (main_merchant_approve == 2)
            {
                //alert("first");
                jQuery(".addcampignh4").hide();
            }

        }
        else
        {
            if (sub_merchant_approve == 2)
            {
                //alert("second");
                jQuery(".addcampignh4").hide();

            }
        }

        // var $ = jQuery.noConflict();


        jQuery.download = function (url, data, method) {
            //url and data options required

            if (url && data) {
                var form = jQuery('<form />', {action: url, method: (method || 'get')});
                jQuery.each(data, function (key, value) {
                    var input = $('<input />', {
                        type: 'hidden',
                        name: key,
                        value: value
                    }).appendTo(form);
                });
                return form.appendTo('body').submit().remove();
            }
            throw new Error('jQuery.download(url, data) - url or data invalid');
        };

        /******* Close More action div block click any where in the window if block open *****/
        $a = jQuery.noConflict();

        $a("body").click
                (
                        function (e)
                        {
                            if ((e.target.className).trim() == "actiontd")
                                    //if ((e.target.className) == "actiontd ")
                                    {

                                    }
                            else
                            {
                                $a('.actiontd').find('#actiondiv').slideUp("slow");
                            }
                        }
                );

        /******* Open More action div block on click more action link *****/


        function bind_more_action_click()
        {

            var isMobile = {
                Android: function () {
                    return navigator.userAgent.match(/Android/i);
                },
                BlackBerry: function () {
                    return navigator.userAgent.match(/BlackBerry/i);
                },
                iOS: function () {
                    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                },
                Opera: function () {
                    return navigator.userAgent.match(/Opera Mini/i);
                },
                Windows: function () {
                    return navigator.userAgent.match(/IEMobile/i);
                },
                any: function () {
                    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
                }
            };

            if (isMobile.iOS() || isMobile.Android())
            {
                //alert("ipad");		
                jQuery(document).on("touchstart", 'td.actiontd', function () {
                    //alert("td touch");
                    if (jQuery(this).find('#actiondiv').css('display') == 'none')
                    {
                        //alert("show");
                        jQuery('.actiondivclass').css("display", "none");
                        //$(this).find('#actiondiv').css('display','block');
                        jQuery(this).find('#actiondiv').slideDown("slow");
                    }
                    else
                    {
                        //alert("close");
                        jQuery(this).find('#actiondiv').slideUp('slow');
                    }
                    /*
                     },function(){
                     $(this).find('#actiondiv').css('display','none');  */
                });
                jQuery(document).on("touchstart", 'body',
                        function (e)
                        {
                            //alert("body touch");
                            if ((e.target.className).trim() == "actiontd")
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
                jQuery(document).on("click", 'td.actiontd', function () {

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
        bind_more_action_click();

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

        /******** Open campaign detail on click campaign title in campaign grid ********/
        $a = jQuery.noConflict();
        $a("a[id^='showCamp_']").live("click", function () {

            var arr = $a(this).attr("id").split("_");
            var cid = arr[1];
            $a.ajax({
                type: "POST",
                url: "<?= WEB_PATH ?>/merchant/process.php",
                data: "campaign_id=" + cid + "&getCmapaigndetail_for_popup=yes",
                async: false,
                success: function (msg) {

                    $a(".campaign_detail_div").html(msg);
                    //$("#jqReviewHelpfulMessageNotification").html(msg);
                    jQuery.fancybox({
                        content: jQuery('.campaign_detail_div').html(),
                        type: 'html',
                        //autoDimensions: false,
                        //autoSize: false,
                        //fitToView:false,
                        width: 800,
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
        });

        /******** Display end campaign popup
         - check merchant session active or not ? If not then send it to register page
         - If session active then display all location where campaign is active and offer left greater then 0
         - give one button for end campaign
         *****/
        $a("a[id^='expireCamp_']").live("click", function () {
            var arr = $a(this).attr("id").split("_");
            var cid = arr[1];
            $a.ajax({
                type: "POST",
                url: "<?= WEB_PATH ?>/merchant/process.php",
                data: "campaign_id=" + cid + "&endcampaign_popup=yes",
                async: false,
                success: function (msg) {
                    $a(".campaign_expire_div").html(msg);
                    jQuery("#btn_expirecampaign").addClass("disabled");
                    jQuery("#btn_expirecampaign").attr("disabled", "disabled");
                }
            });

            var linkurl = jQuery(this).attr("link");

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


                        // window.location.href = "<?= WEB_PATH ?>/merchant/"+linkurl;
                        //open_popup('expire');
                        jQuery.fancybox({
                            content: jQuery('.campaign_expire_div').html(),
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
                }
            });
        });
        /*************** Make enable pause button after selecting any location *************/
        jQuery('.fancybox-inner input[name="location_id[]"]').live("click", function () {
            var check_select = false;
            jQuery('.fancybox-inner input[name="location_id[]"]').each(function () {
                if (jQuery(this).is(":checked"))
                {
                    check_select = true;
                }
                else
                {

                }
            });
            if (check_select)
            {
                jQuery(".fancybox-inner #btn_expirecampaign").removeAttr("disabled");
                jQuery(".fancybox-inner #btn_expirecampaign").removeClass("disabled");
            }
            else
            {
                jQuery(".fancybox-inner #btn_expirecampaign").attr("disabled", "disabled");
                jQuery(".fancybox-inner #btn_expirecampaign").addClass("disabled");
            }

        });
        /*************** Make enable pause button after selecting any location *************/

        /******** End campaign for selected location
         - check merchant session active or not ? If not then send it to register page
         - If session active then end campaign for selected locations
         - end campaign means set offer left to zero and return all blocked point which are reserved with that location's offer left
         - called method : process.php?btn_expirecampaign=yes&location_id={locationid array}
         - It will refresh the page
         *****/
        jQuery(document).on("click", "#btn_expirecampaign", function(e) {
		e.preventDefault();
		var $ths = jQuery(this);
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
                        var footer_msg = "<div><hr><a href='" + href + "' class='msg_popup_cancel_anchor' ><?php echo $merchant_msg['index']['fancybox_ok']; ?></a></div>";
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
		var arr_unchecked_values = jQuery('#campaign_to_paused input[type=checkbox]:checked').map(function(){return this.value}).get();
		var camp_id = jQuery('#campaign_id').val();
		
                //return false;
                jQuery.ajax({
                    type: "GET",
                    url: 'process.php',
                    data: 'campaign_id='+camp_id+'&location_id='+arr_unchecked_values+'&btn_expirecampaign=Pause',
                    async: false,
                    success: function (msg)
                    {
                            if(msg == 1){
				jQuery.fancybox.close();
				jQuery('#error_message').text('<?php echo $merchant_msg["edit-compaign"]['Msg_campaign_pause_successfully']; ?>');
					jQuery('#manage_campaigns_table').dataTable().fnDraw();
			            return false;
				}
                    }
                });
                //return true;
            }
        });


        jQuery("#btn_cancel").live("click", function () {
            jQuery.fancybox.close();
            return false;
        });
        jQuery("#popupcancel").live("click", function () {
            jQuery.fancybox.close();
            return false;
        });

        jQuery(".commonclass").live("click", function () {

            var linkurl = jQuery(this).attr("link");

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
                        if (linkurl == "add-compaign.php")
                        {
                            jQuery.ajax({
                                type: "POST",
                                url: 'process.php',
                                data: 'check_enable_online_campaign=true',
                                async: false,
                                success: function (msg)
                                {
                                    var obj = jQuery.parseJSON(msg);
                                    if (obj.status == "true")
                                    {
                                        var message = "<center><div><input type='radio' name='campaign_type' value='1' /> In-store campaign</div><div><input type='radio' name='campaign_type' value='2' /> Online campaign</div></center>";
                                        var alert_msg = message;
                                        var head_msg = "<div class='head_msg'>Select Campaign Type</div>";
                                        var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupAddcampaign' name='popupAddcampaign' class='msg_popup_cancel'></div>";
                                        var content_msg = "<div class='content_msg'>" + alert_msg + "</div>";
                                        jQuery("#message-window").html(head_msg + content_msg + footer_msg);
                                        jQuery.fancybox({
                                            content: jQuery('#message-window').html(),
                                            type: 'html',
                                            openSpeed: 300,
                                            closeSpeed: 300,
                                            // topRatio: 0,

                                            changeFade: 'fast',
                                            beforeShow: function () {
                                                var newWidth = 265;
                                                var newHeight = 200;
                                                this.width = newWidth;
                                                this.height = newHeight;
                                            },
                                            helpers: {
                                                overlay: {
                                                    closeClick: false,
                                                    opacity: 0.3
                                                } // overlay

                                            }
                                        });
                                    }
                                    else
                                    {
                                        window.location.href = "<?= WEB_PATH ?>/merchant/" + linkurl;
                                    }
                                }
                            });
                        }
                        else
                        {
                            window.location.href = "<?= WEB_PATH ?>/merchant/" + linkurl;
                        }
                    }
                }
            });
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
            //  alert("There is no Package assigned to you. Please contact Scanflip account executive for your package subscription.");
            return false;
            // alert(message);
        }

</script>
<?php
if ($_REQUEST['action'] == "active") {
        ?>
        <script>
                jQuery("#active_link").css("color", 'orange');
                jQuery("#expire_link").css("color", '#0066FF');
                jQuery("#schedule_link").css("color", '#0066FF');
                jQuery("#end_link").css("color", '#0066FF');
        </script>
        <?php
} elseif ($_REQUEST['action'] == "expired") {
        ?>
        <script>
                jQuery("#active_link").css("color", '#0066FF');
                jQuery("#expire_link").css("color", 'orange');
                jQuery("#schedule_link").css("color", '#0066FF');
                jQuery("#end_link").css("color", '#0066FF');
        </script>
        <?php
} elseif ($_REQUEST['action'] == "activeinfuture") {
        ?>
        <script>
                jQuery("#active_link").css("color", '#0066FF');
                jQuery("#expire_link").css("color", '#0066FF');
                jQuery("#schedule_link").css("color", 'orange');
                jQuery("#end_link").css("color", '#0066FF');
        </script>
        <?php
} elseif ($_REQUEST['action'] == "endcampaigns") {
        ?>
        <script>
                jQuery("#active_link").css("color", '#0066FF');
                jQuery("#expire_link").css("color", '#0066FF');
                jQuery("#schedule_link").css("color", '#0066FF');
                jQuery("#end_link").css("color", 'orange');
        </script>
        <?php
}
?>
<script type="text/javascript">

        jQuery(".fancybox-inner #popupAddcampaign").live("click", function () {
            //alert(jQuery(".fancybox-inner input[name='campaign_type']:radio:checked").val());
            var campaign_type = jQuery(".fancybox-inner input[name='campaign_type']:radio:checked").val();
            jQuery.fancybox.close();
            if (campaign_type == 1)
            {
                window.location.href = "<?= WEB_PATH ?>/merchant/add-compaign.php";
            }
            else if (campaign_type == 2)
            {
                window.location.href = "<?= WEB_PATH ?>/merchant/add-online-compaign.php";
            }
        });


</script>
