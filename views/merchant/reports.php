	<?php
	/**
	 * @uses report generation
	 * @used in pages :my-account-left.php
	 * @author Sangeeta Raghavani
	 */
	//require_once("../classes/Config.Inc.php");
	check_merchant_session();
	//include_once(SERVER_PATH . "/classes/DB.php");
	//$objDB = new DB();
	$array = array();
	$months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	$str_activated = $str_expired = $str_campaigns = $str_generated_coupons = $str_redeemed_coupons = "";
	$str_activated1 = $str_expired1 = $str_campaigns1 = "";
	$domain_arr['share_email'] = 0;
	$domain_arr['pageview_email'] = 0;
	$domain_arr['share_facebook'] = 0;
	$domain_arr['pageview_facebook'] = 0;
	$domain_arr['share_twitter'] = 0;
	$domain_arr['pageview_twitter'] = 0;
	$domain_arr['share_google'] = 0;
	$domain_arr['pageview_google'] = 0;
	$domain_arr['share_other'] = 0;
	$domain_arr['pageview_other'] = 0;
	$domain_arr['pageview_allmedium'] = 0;
	$domain_arr['pageview_qrcode'] = 0;
	$only_keys = array_keys($domain_arr);

	function listofmonths() {
		?>
		<li class="active_li"  ><a month="01" title="CLICK HERE TO FILTER RESULT">Jan</a></li>
		<li ><a month="02" title="CLICK HERE TO FILTER RESULT">Feb</a></li>
		<li ><a month="03" title="CLICK HERE TO FILTER RESULT">Mar</a></li>
		<li ><a month="04" title="CLICK HERE TO FILTER RESULT">Apr</a></li>
		<li ><a month="05" title="CLICK HERE TO FILTER RESULT">May</a></li>
		<li ><a month="06" title="CLICK HERE TO FILTER RESULT">Jun</a></li>
		<li ><a month="07" title="CLICK HERE TO FILTER RESULT">Jul</a></li>
		<li ><a month="08" title="CLICK HERE TO FILTER RESULT">Aug</a></li>
		<li ><a month="09" title="CLICK HERE TO FILTER RESULT">Sep</a></li>
		<li ><a month="10" title="CLICK HERE TO FILTER RESULT">Oct</a></li>
		<li ><a month="11" title="CLICK HERE TO FILTER RESULT">Nov</a></li>
		<li ><a month="12" title="CLICK HERE TO FILTER RESULT">Dec</a></li>
		<?php
	}

	$cnt1 = 1;
	?>

	<!DOCTYPE HTML>
	<html>
	    <head>
		<title>ScanFlip | Report</title>
		<?php //require_once(MRCH_LAYOUT."/head.php");  ?>
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.min.js"></script>
		<link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
		
		<!-- for loyaltycard report tab -->

		<!-- for loyaltycard report tab -->
		
		<link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
		<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> -->

		<script src="<?= ASSETS_JS ?>/m/highcharts.js"></script>
		<script src="<?= ASSETS_JS ?>/m/drilldown.js"></script>
		
		<script src="<?= ASSETS_JS ?>/m/no-data-to-display.js"></script>
		
		<link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
		<style>
		    /* bootstrap hack: fix content width inside hidden tabs */
		    .tab-content > .tab-pane,
		    .pill-content > .pill-pane {
		        display: block;     /* undo display:none          */
		        height: 0;          /* height:0 is also invisible */ 
		        overflow-y: hidden; /* no-overflow                */
		    }
		    .tab-content > .active,
		    .pill-content > .active {
		        height: auto;       /* let the content decide it  */
		    } /* bootstrap hack end */
		</style>
	    </head>

	    <body class="reports">
		<?php
		/*         * ******* If employee is login then he can able to  view report of only assigned location ********** */
		if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
		        $sub_location_id = "";
		} else {
		        $media_acc_array = array();
		        $media_acc_array['merchant_user_id'] = $_SESSION['merchant_id'];
		        $RSmedia = $objDB->Show("merchant_user_role", $media_acc_array);
		        $location_val = $RSmedia->fields['location_access'];
		        /*                 * ********* Get location list of login merchant ********** */
		        $arr = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $m_parent . '&loc_id=' . $location_val);
		        if (trim($arr[0]) == "") {
		                unset($arr[0]);
		                $arr = array_values($arr);
		        }
		        $json = json_decode($arr[0]);

		        $total_records = $json->total_records;
		        $records_array = $json->records;
		        if ($total_records > 0) {
		                foreach ($records_array as $RSStore) {
		                        $sub_location_id = $RSStore->id;
		                }
		        }
		}
		?>
		<input type="hidden" name="hdn_employee_location" id="hdn_employee_location" value="<?php echo $sub_location_id; ?>" />
		<div >
		    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
		    <script type="text/javascript" charset="utf-8">
		            ///// initailizing datatable for cmapiagn grid 
		            // $a = jQuery.noConflict();
		            jQuery(document).ready(function () {

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

		                var oTable = jQuery('#report_campaign_table').dataTable({
		                    "bFilter": false,
		                    "bLengthChange": false,
		                    "bSort": false,
		                    "info": false,
		                    // "sPaginationType": "full_numbers",
		                    "bProcessing": true,
		                    "bServerSide": true,
		                    //"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0,1,2,3,4] }],
		                    "iDisplayLength": 25,
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
		                    //"iDeferLoading": <?php //echo $total_records1;                                    ?>,
		                    "fnServerParams": function (aoData) {
		                        aoData.push({"name": "get_filter_campaigns", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'}, {"name": "status", "value": jQuery("#opt_filter_status").val()}, {"name": "year", "value": jQuery("#opt_filter_year").val()}, {"name": "month", "value": jQuery("#opt_filter_month").val()}, {"name": "category", "value": jQuery("#opt_filter_category").val()}, {"name": "location_id", "value": jQuery("#opt_filter_location").val()}, {"name": "action", "value": jQuery("#opt_filter_status").val()});

		                    },
		                    "fnServerData": fnDataTablesPipeline,
		                    "aoColumns": [null, null, null, null, null]

		                });

		                $("#btnfilterstaticscampaigns").on("click", function () {
		                    oTable.fnDraw();
		                });

		            });
		    </script> 

		    <div>
		        <?php
		        require_once(MRCH_LAYOUT . "/header.php");
		        ?>
		        <!--end header--></div>
		    <div id="contentContainer">

		        <div id="content">
		            <div class="title_header"><?php echo $merchant_msg['report']['reports']; ?></div>
		            <!--<div id="backdashboard" >
		                                    <a id="dashboard" href="<?= WEB_PATH ?>/merchant/my-account.php"><img src="<?//=ASSETS_IMG ?>/m/back_dashboard.png" /></a>
		                            </div> -->
		            <!--- tab listing --->
		            <div id="media-upload-header">
		                <ul id="sidemenu">
		                    <li id="tab-type" class="div_location"><a class="current"><?php echo $merchant_msg['report']['Tab_Location']; ?></a></li>
		                    <li id="tab-type" class="statistics_report"><a ><?php echo $merchant_msg['report']['Tab_Campaigns']; ?></a></li>
		                    <li id="tab-type" class="loyalty_report"><a ><?php echo $merchant_msg['report']['Tab_Loyalty']; ?></a></li>
		                    <!--<li id="tab-type" class="qrcode_report"><a ><?php //echo $merchant_msg['report']['Tab_Qrcode_Scans']; ?></a></li>-->
		                    <li id="tab-type" class="campaign_report" style="display:none"><a>Campaign report</a><span id="close_span2"></span></li>
		                    <li id="tab-type" class="newcampaign_report" style="display:none"><a>Campaign Report</a><span id="close_span"></span></li>
		                    <li id="tab-type" class="loyaltycard_report" style="display:none"><a>Loyalty Card Report</a><span id="close_span1"></span></li>
		                </ul>
		            </div>
		            <div class="inner_tabdiv">
		                <div id="campaigns" class="tabs" style="display: none;">
		                    <div class="reportContent">

		                        <div id="container6" class="containerreferraluser"></div>


		                        <!--- reserved campaigns -->
		                        <select id="s1-containerreserved-reserved_campaigns">
		                            <?php
		                            for ($y = 2000; $y <= date('Y'); $y++) {
		                                    ?>
		                                    <option value="<?php echo $y; ?>" <?php
		                                    if (date('Y') == $y) {
		                                            echo "selected";
		                                    }
		                                    ?> > <?php echo $y; ?> </option>
		                                    <?php }
		                                    ?>
		                        </select>
		                        <div id="containerreserved" class="containerreferraluser"></div>
		                        <!--<a  id="toggle-generated_report">plus</a>-->
		                        <div class="mainIcon mainIcon_minus" id="toggle-reserved_report1" > </div> <span class="padding-left-20">Collapse</span> 
		                        <ul id="ul_reserved_campaigns" class="report_list">
		                            <?php listofmonths(); ?>

		                        </ul>
		                        <div class="cls_clear" ></div>

		                        <div id="reserved_report1">
		                            <div id="reserved_report">

		                            </div>
		                            <div id="demo_reserved_campaigns" class="jPaginate" ></div>
		                        </div>
		                        <hr />
		                        <!--- end of reserved campaigns -->

		                        <select id="s1-container2-generated_campaigns">
		                            <?php
		                            for ($y = 2000; $y <= date('Y'); $y++) {
		                                    ?>
		                                    <option value="<?php echo $y; ?>" <?php
		                                    if (date('Y') == $y) {
		                                            echo "selected";
		                                    }
		                                    ?> > <?php echo $y; ?> </option>
		                                    <?php }
		                                    ?>
		                        </select>
		                        <div id="container2" class="containerreferraluser"></div>
		                        <!--<a  id="toggle-generated_report">plus</a>-->
		                        <div class="mainIcon mainIcon_minus" id="toggle-generated_report1" > </div> <span class="padding-left-20">Collapse</span> 
		                        <ul id="ul_generated_campaigns" class="report_list">
		                            <?php listofmonths(); ?>

		                        </ul>
		                        <div class="cls_clear" ></div>

		                        <div id="generated_report1">
		                            <div id="generated_report">

		                            </div>
		                            <div id="demo_generated_campaigns" class="jPaginate" ></div>
		                        </div>
		                        <hr />
		                        <select id="s1-container1-active_campaigns">
		                            <?php
		                            for ($y = 2000; $y <= (date('Y') + 2); $y++) {
		                                    ?>
		                                    <option value="<?php echo $y; ?>" <?php
		                                    if (date('Y') == $y) {
		                                            echo "selected";
		                                    }
		                                    ?> > <?php echo $y; ?> </option>
		                                    <?php }
		                                    ?>
		                        </select>
		                        <div id="container1" class="containerreferraluser"></div>
		                        <div class="mainIcon mainIcon_minus" id="toggle-activated_report1" ></div> <span class="padding-left-20">Collapse</span> 
		                        <ul id="ul_activated_campaigns" class="report_list">
		                            <?php listofmonths(); ?>

		                        </ul>
		                        <div class="cls_clear" ></div>
		                        <div id="activated_report1">
		                            <div id="activated_report"></div>
		                            <div id="demo_activated_campaigns" class="jPaginate" ></div>
		                        </div>
		                        <hr />
		                        <select id="s1-container3-expire_campaigns">
		                            <?php
		                            for ($y = 2000; $y <= (date('Y') + 2); $y++) {
		                                    ?>
		                                    <option value="<?php echo $y; ?>" <?php
		                                    if (date('Y') == $y) {
		                                            echo "selected";
		                                    }
		                                    ?> > <?php echo $y; ?> </option>
		                                    <?php }
		                                    ?>
		                        </select>
		                        <div id="container3" class="containerreferraluser"></div>
		                        <div class="mainIcon mainIcon_minus" id="toggle-expired_report1" ></div> <span class="padding-left-20">Collapse</span> 
		                        <ul id="ul_expired_campaigns" class="report_list">
		                            <?php listofmonths(); ?>

		                        </ul>
		                        <div class="cls_clear" ></div>
		                        <div id="expired_report1">
		                            <div id="expired_report"></div>
		                            <div id="demo_expired_campaigns" class="jPaginate" ></div>
		                        </div>
		                        <!--end of content--></div>
		                </div>
		                <!-- end for campaigns -->
		                <!-- fro redeem coupons -->

		                <div id="Redeem_coupons" class="tabs" style="display: none;">
		                    <div class="reportContent">
		                        <select id="s1-container5-generated_coupons">
		                            <?php
		                            for ($y = 2000; $y <= (date('Y') + 2); $y++) {
		                                    ?>
		                                    <option value="<?php echo $y; ?>" <?php
		                                    if (date('Y') == $y) {
		                                            echo "selected";
		                                    }
		                                    ?> > <?php echo $y; ?> </option>
		                                    <?php }
		                                    ?>
		                        </select>
		                        <div id="container5" class="containerreferraluser"></div>
		                        <div class="mainIcon mainIcon_minus" id="toggle-generated_coupon_report1" ></div> <span class="padding-left-20">Collapse</span> 
		                        <ul id="ul_generated_coupons" class="report_list">
		                            <?php listofmonths(); ?>

		                        </ul>

		                        <div class="cls_clear"></div>
		                        <div id="generated_coupon_report1">
		                            <div id="generated_coupon_report"></div>
		                            <div id="demo_generated_coupons" class="jPaginate" ></div>
		                        </div>
		                        <hr />

		                        <select id="s1-container4-reddemed_coupons">
		                            <?php
		                            for ($y = 2000; $y <= (date('Y') + 2); $y++) {
		                                    ?>
		                                    <option value="<?php echo $y; ?>" <?php
		                                    if (date('Y') == $y) {
		                                            echo "selected";
		                                    }
		                                    ?> > <?php echo $y; ?> </option>
		                                    <?php }
		                                    ?>
		                        </select>
		                        <div id="container4" class="containerreferraluser"></div>
		                        <div class="mainIcon mainIcon_minus" id="toggle-redeemed_coupon_report1" ></div> <span class="padding-left-20"Collapse</span> 
		                        <ul id="ul_redeemed_coupons" class="report_list">
		                            <?php listofmonths(); ?>

		                        </ul>

		                        <div class="cls_clear"></div>
		                        <div id="redeemed_coupon_report1">
		                            <div id="redeemed_coupon_report"></div>
		                            <div id="demo_redeemed_coupons" class="jPaginate" ></div>
		                        </div>
		                        <hr />
		                        <div id="container7" class="containerreferraluser" ></div>
		                    </div>
		                </div>
		                <!-- end of for redeem coupons -->

		                <!-- start for location	-->
		                <div id="upload_business_logo_ajax1" class="upload_business_logo_ajax nax_scrape_indicator hidden_elem" style="display:none;">
		                    <img height="32" width="32" alt="" src="<?php echo ASSETS_IMG ?>/24.GIF" >
		                </div>	
		                <div id="div_location" class="tabs width_948" style="display: block;" >

		                    <?php
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
		                    $total_records1 = $json1->total_records;
		                    $records_array1 = $json1->records;

		                    $arr = file(WEB_PATH . '/merchant/process.php?get_point_package=yes');
		                    if (trim($arr[0]) == "") {
		                            unset($arr[0]);
		                            $arr = array_values($arr);
		                    }
		                    $json = json_decode($arr[0]);
		                    $total_records_ = $json->total_records;
		                    $records_array_ = $json->records;
		                    if ($total_records_ > 0) {
		                            foreach ($records_array_ as $Row_) {

		                                    $price = $Row_->price;
		                                    $point_ = $Row_->points;
		                                    $p = (1 * $price) / $point_;
		                            }
		                            $B4 = $p;
		                    }
		                    // itrate throgh the location list
		                    if ($total_records1 > 0) {
		                            $cnt = 0;
		                            foreach ($records_array1 as $Row) {
		                                    ?>

		                                    <div class="location_heading">
		                                        <div id="toggleIt-plus_<?php echo $Row->id ?>" class="mainIcon"></div>
		                                        <span><?php echo $merchant_msg['report']['location']; ?><?= $Row->address . ", " . $Row->city . ", " . $Row->state . ", " . $Row->zip ?></span>
		                                        <!-- <span class="plus_">+</span>-->
		                    <!--                    <div id="toggleIt-plus_<?php echo $Row->id ?>" class="mainIcon <?php
		                                        if ($cnt == 0) {
		                                                echo 'mainIcon_minus';
		                                        }
		                                        ?>"></div>             -->

		                                    </div>
		                                    <div class="new_loca_eta" id="plus_<?php echo $Row->id ?>" style="display:<?php echo 'none'; ?>">			 
		                                        <div role="">

		                                            <!-- Nav tabs -->
		                                            <ul class="nav nav-tabs tablist_lc">
		                                                <li class="active"><a href="#lc_display_<?php echo $Row->id ?>" data-toggle="tab">Campaigns</a></li>
		                                                <li><a href="#qr_code_scan_<?php echo $Row->id ?>" data-toggle="tab">QR Code Scans</a></li>

		                                            </ul>

		                                            <!-- Tab panes -->
		                                            <div class="tab-content">
		                                                <div role="tabpanel" class="tab-pane active" id="lc_display_<?php echo $Row->id ?>"><div class="l_content">
		                                                        <div class="l_year_09"> <?php echo $merchant_msg['report']['year']; ?> <select class="slocation_select" id="slocation_select_<?php echo $Row->id ?>" data-id="<?php echo $Row->id ?>"><?php
		                                                                for ($y = date('Y') - 2; $y <= date('Y'); $y++) {
		                                                                        ?>
		                                                                        <option <?php
		                                                                        if ($y == date("Y")) {
		                                                                                echo "selected";
		                                                                        }
		                                                                        ?> value="<?php echo $y; ?>" > <?php echo $y; ?> </option>
		                                                                    <?php }
		                                                                    ?>
		                                                            </select>&nbsp;&nbsp;&nbsp;</div>
		                                                        <div class="divsummary_<?php echo $Row->id ?>"></div>

		                                                        <?php
		                                                        $total_coupon_redeemed = 0;
		                                                        $total_coupon_issued = 0;
		                                                        $arr_new_cust_ref = array();
		                                                        $arr_exsting_cust_reft = array();
		                                                        $arr_age = array();
		                                                        $t_v = 0;
		                                                        $sql = "SELECT  distinct c.* , cl.location_id location_id , cl.num_activation_code num_activation_code   from  campaigns c , campaign_location cl ,locations l
		                    where l.id=cl.location_id and  cl.location_id=" . $Row->id . " and c.id = cl.campaign_id and cl.active<>1 and cl.active_in_future<>1  AND c.expiration_date <  CONVERT_TZ(NOW(),'" . CURR_TIMEZONE . "',SUBSTR(l.timezone,1, POSITION(',' IN l.timezone)-1)) ";


		                                                        $RS_l = $objDB->Conn->Execute($sql);
		                                                        $r_l = $RS_l->RecordCount();

		                                                        $t_c = $r_l;
		                                                        $total_cost = 0;
		                                                        $campaign_list_arr = array();
		                                                        if ($r_l > 0) {
		                                                                while ($loc = $RS_l->FetchRow()) {
		                                                                        array_push($campaign_list_arr, $loc['id']);
		                                                                }
		                                                                ?>
		                                                                <?php
		                                                                $campaign_list = implode(",", $campaign_list_arr);

		                                                                $sql = "select  c.gender , c.dob_year , b.customer_id ,b.customer_campaign_code ,b.location_id , a.coupon_id ,a.redeem_value 
		                                    from `coupon_redeem` a inner join coupon_codes b on a.coupon_id = b.id inner join customer_user c on c.id= b.customer_id
		                                    where  b.customer_campaign_code in ($campaign_list) and b.location_id =" . $Row->id;
		                                                                $a = $objDB->Conn->Execute($sql);
		                                                                $t_c = $a->RecordCount();
		                                                        }
		                                                        ?>    
		                                                        <div class="cls_clear"></div><?php
		                                                        
		                                                        $total_cust = 0;
		                                                        ?>


		                                                        <div  class="chart_container">
		                                                            <div class="location_report_div_left">
		                                                                <div id="container_<?php echo $Row->id; ?>" class="location_report_gender"   cust_avail="<?php
		                                                                if ($total_cust == 0) {
		                                                                        echo "0";
		                                                                } else {
		                                                                        echo "1";
		                                                                }
		                                                                ?>" ></div>
		                                                            </div>
		                                                            <div class="location_report_div_right">
		                                                                <div id="containerage_<?php echo $Row->id; ?>" class="location_report_gender"></div>
		                                                            </div>
		                                                        </div>
		                                                        <?php
		                                                         
		                                                        ?>
		                                                        <div id="container_report_<?php echo $Row->id; ?>" class="location_report_statics"  camp_avail="<?php
		                                                        if ($t_c == 0) {
		                                                                echo "0";
		                                                        } else {
		                                                                echo "1";
		                                                        }
		                                                        ?>"></div>

		                                                        <div class="cls_clear"></div>   

		                                                    </div>
		                                                </div>
		                                                <div role="tabpanel" class="tab-pane" id="qr_code_scan_<?php echo $Row->id ?>">

		                                                    <div class="l_year_09"> <?php echo $merchant_msg['report']['year']; ?> <select class="qr_sc_select" id="qr_select_<?php echo $Row->id ?>" data-id="<?php echo $Row->id ?>"><?php
		                                                            for ($y = date('Y') - 2; $y <= date('Y'); $y++) {
		                                                                    ?>
		                                                                    <option <?php
		                                                                    if ($y == date("Y")) {
		                                                                            echo "selected";
		                                                                    }
		                                                                    ?> value="<?php echo $y; ?>" > <?php echo $y; ?> </option>
		                                                                <?php }
		                                                                ?>
		                                                        </select>&nbsp;&nbsp;&nbsp;</div>
		                                                    <div class="cls_clear"></div>
		                                                    <div class="" id="qr_scan_<?php echo $Row->id ?>" style="position:relative;margin-top:20px;">
		                                                        <div id="container_qrcodecampaignreport_<?php echo $Row->id ?>"  class="qrcode_container" ></div>
		                                                        <div class="cls_clear"></div>
		                                                        <div id="container_qrcodelocationreport_<?php echo $Row->id ?>" class="qrcode_container"></div>					
		                                                    </div>
		                                                </div>
		                                                <div class="loader_location" style="display:none;">
		                                                    <span class="load_dt">Please wait we are generating your location report ...</span>								
		                                                    <!--<img src="<?php echo ASSETS_IMG; ?>/128.GIF">-->
		                                                </div>
		                                            </div>

		                                        </div>


		                                        <script>
		                                                $ = jQuery.noConflict();
		                                        </script>
		                                    </div>

		                                    <?php
		                                    $cnt++;
		                            }
		                    }
		                    ?>

		                </div>
		                <!-- end of for locations -->

		                <!-- start for Sub merchant	-->
		                <div id="Sub_merchant" class="tabs" style="display: none;">

		                    <div class="reportContent">

		                        <!--- for referral user -->
		                        <select id="s1-containerreferraluser-referral_user">
		                            <?php
		                            for ($y = 2000; $y <= (date('Y') + 2); $y++) {
		                                    ?>
		                                    <option value="<?php echo $y; ?>" <?php
		                                    if (date('Y') == $y) {
		                                            echo "selected";
		                                    }
		                                    ?> > <?php echo $y; ?> </option>
		                                    <?php }
		                                    ?>
		                        </select>
		                        <div id="containerreferraluser" class="containerreferraluser"></div>
		                        <div class="mainIcon mainIcon_minus" id="toggle-referral_user_report1" ></div> <span class="padding-left-20">Collapse</span> 
		                        <ul id="ul_referral_user" class="report_list">
		                            <?php listofmonths(); ?>

		                        </ul>

		                        <div class="cls_clear"></div>
		                        <div id="referral_user_report1">
		                            <div id="referral_user_report"></div>
		                            <div id="demo_referral_user" class="jPaginate" ></div>
		                        </div>
		                        <hr />
		                        <!--  end for redeemed user -->

		                        <!--- for redeemed user -->
		                        <select id="s1-containerredeemeduser-redeemed_user">
		                            <?php
		                            for ($y = 2000; $y <= (date('Y') + 2); $y++) {
		                                    ?>
		                                    <option value="<?php echo $y; ?>" <?php
		                                    if (date('Y') == $y) {
		                                            echo "selected";
		                                    }
		                                    ?> > <?php echo $y; ?> </option>
		                                    <?php }
		                                    ?>
		                        </select>
		                        <div id="containerredeemeduser" class="containerreferraluser"></div>
		                        <div class="mainIcon mainIcon_minus" id="toggle-redeemed_user_report1" ></div> <span class="padding-left-20">Collapse</span> 
		                        <ul id="ul_redeemed_user" class="report_list">
		                            <?php listofmonths(); ?>

		                        </ul>

		                        <div class="cls_clear"></div>
		                        <div id="redeemed_user_report1">
		                            <div id="redeemed_user_report"></div>
		                            <div id="demo_redeemed_user" class="jPaginate" ></div>
		                        </div>

		                    </div>
		                </div>
		                <!-- end of for sub merchant -->

		                <!-- start for Points	-->
		                <div id="points" class="tabs" style="display: none;">

		                    <div class="reportContent">
		                        <select id="s1-container10-used_points">
		                            <?php
		                            for ($y = 2000; $y <= (date('Y') + 2); $y++) {
		                                    ?>
		                                    <option value="<?php echo $y; ?>" <?php
		                                    if (date('Y') == $y) {
		                                            echo "selected";
		                                    }
		                                    ?> > <?php echo $y; ?> </option>
		                                    <?php }
		                                    ?>
		                        </select>
		                        <div id="container10" class="containerreferraluser"></div>
		                        <div class="mainIcon mainIcon_minus" id="toggle-usedpoint_report1" ></div> <span class="padding-left-20">Collapse</span> 
		                        <ul id="ul_usedpoints" class="report_list">
		                            <?php listofmonths(); ?>

		                        </ul>

		                        <div class="cls_clear"></div>
		                        <div id="usedpoint_report1">
		                            <div id="usedpoint_report"></div>
		                            <div id="demo_usedpoints" class="jPaginate" ></div>
		                        </div>
		                        <hr />
		                    </div>
		                </div>
		                <!-- end of for points -->

		                <div id="statistics_report" class="tabs statist_rep_tab statistics_report">
		                    <div id="tbl_campaigns" class="width_948">
		                        <table  border="0" cellspacing="0" cellpadding="10"  class="tableMerchant" id="report_campaign_table">
		                            <thead>
		                                <!--<tr><th colspan="6" align="right" class="table_button_colspan" >
		                                
		                                <tr> -->
		                            <td colspan="6" align="left" class="filter_result table_filter_area"  >
		                                <input type="button" style="float:right;" value="<?php echo $merchant_msg['report']['Campaign_Filter_Button']; ?>" name="btnfilterstaticscampaigns" id="btnfilterstaticscampaigns" />
		                                <div class="fltr_div_td">											
		                                    <div class="cls_filter" >Filter By:</div>
		                                    <div class="cls_filter_top">

		                                        <b><?php echo $merchant_msg['report']['Campaign_Filter_Year'];  //echo "==".date('m');            ?></b>
		                                        <select id="opt_filter_year" >
		                                            <!-- <option value="0"   >--- ALL ---</option>-->
		                                            <?php
	//for($y=2000;$y<=date('Y');$y++)
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
		                                            <option value="0" selected >---- ALL ----</option>
		                                            <option value="01"   >January</option>
		                                            <option value="02"  >February</option>
		                                            <option value="03"  >March</option>
		                                            <option value="04"  >April</option>
		                                            <option value="05"  >May</option>
		                                            <option value="06" >Jun</option>
		                                            <option value="07" >July</option>
		                                            <option value="08" >August</option>
		                                            <option value="09"  >September</option>
		                                            <option value="10">October</option>
		                                            <option value="11"  >November</option>
		                                            <option value="12" >December</option>
		                                        </select>
		                                        <b>  <?php echo $merchant_msg['report']['Campaign_Filter_Category']; ?></b>
		                                        <select id="opt_filter_category" >
		                                            <?php $RSCat = $objDB->Show("categories"); ?>
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
		                                            <option value="all" selected="selected" > --- ALL ---</option>
		                                            <option value="active" selected="selected" > --- Active ---</option>
		                                            <option value="endcampaigns"  > --- Paused  ---</option>
		                                            <!--<option value="activeinfuture" > --- Scheduled ---</option>-->
		                                            <option value="expired"  > --- Expired ---</option>
		                                        </select> 
	<!-- <input type="button" value="Show Result" name="btnfilterstaticscampaigns" id="btnfilterstaticscampaigns" /> -->
		                                    </div>
		                                    <div class="cls_filter_bottom">
		                                        <?php
		                                        if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
		                                                ?>
		                                                <b>
		                                                    <?php echo $merchant_msg['report']['Campaign_Filter_Loactions']; ?></b>
		                                                <select id="opt_filter_location" >
		                                                    <option value="0" selected="selected" > --- ALL ---</option>
		                                                    <?php
		                                                    if ($total_records1 > 0) {
		                                                            $cnt = 0;
		                                                            foreach ($records_array1 as $Row) {
		                                                                    if ($Row->location_name == "") {
		                                                                            $locname = $Row->address . " - " . $Row->zip;
		                                                                    } else {
		                                                                            $locname = $Row->location_name . " - " . $Row->zip;
		                                                                    }
		                                                                    $location_string = $Row->address . ", " . $Row->city . ", " . $Row->state . ", " . $Row->zip;
		                                                                    $location_string = (strlen($location_string) > 57) ? substr($location_string, 0, 57) . '...' : $location_string;
		                                                                    ?>
		                                                                    <option value="<?php echo $Row->id; ?>" title="<?php echo $Row->address . ", " . $Row->city . ", " . $Row->state . ", " . $Row->zip; ?>"  > <?php echo $location_string; ?> </option>
		                                                                    <?php
		                                                            }
		                                                    }
		                                                    ?>
		                                                </select>
		                                                <?php
		                                        } else {
		                                                $arr = file(WEB_PATH . '/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id=' . $m_parent . '&loc_id=' . $location_val);
		                                                if (trim($arr[0]) == "") {
		                                                        unset($arr[0]);
		                                                        $arr = array_values($arr);
		                                                }
		                                                $json = json_decode($arr[0]);

		                                                $total_records_l = $json->total_records;
		                                                $records_array_l = $json->records;
		                                                if ($total_records_l > 0) {
		                                                        foreach ($records_array_l as $RSStore) {
		                                                                $sub_location_id = $RSStore->id;
		                                                                //echo $RSStore->location_name;
		                                                                //echo $RSStore->address.", ".$RSStore->city.", ".$RSStore->state.", ".$RSStore->zip;
		                                                        }
		                                                }
		                                                ?>
		                                                <input type="hidden"  name="opt_filter_location" id="opt_filter_location" value="<?php echo $sub_location_id; ?>" />
		                                        <?php } ?>
		                                    </div>
		                                </div>
		                            </td>


		                            </tr>

		                            <tr style="height:26px;">
		                                <th  ><?php echo $merchant_msg['report']['Campaign_Field_campaignname']; ?></th>
		                                <th  ><?php echo $merchant_msg['report']['Campaign_Field_category']; ?></th>
		                                      <!--  <th align="left" class="tableDealTh" style="width:15%;">Locations</th>-->
		                                <!--                <th align="left" class="tableDealTh">Groups</th>-->
		                                <th ><?php echo $merchant_msg['report']['Campaign_Field_campaigncode']; ?></th>                

		                                <th ><?php echo $merchant_msg['report']['Campaign_Field_startdate']; ?></th>
		                                <th ><?php echo $merchant_msg['report']['Campaign_Field_expirationdate']; ?></th>
		                            </tr>
		                            </thead>

		                        </table>
		                    </div>
		                </div>
		                
		            <div id="loyalty_report" class="tabs" style="display: none;">
						
						<button type="button" class="close close_link close_loyalty_report_form">×</button>
						
						<div id="loyalty_report_form">
						<div>
						 
							<table width="100%" border="0" cellspacing="2" cellpadding="2">
                                <tbody>
									<tr>	
										<td>
										<div class="tbl_top">	
										    <div class="disply_09">
												<label>Year: </label>
															<select id="opt_filter_year_lc" >
																<?php
																for ($y = date('Y') - 2; $y <= date('Y'); $y++) 
																{
																?>
																	<option value="<?php echo $y; ?>" 
																	<?php
																	if ($y == date('Y')) 
																	{
																		echo "selected";
																	}
																	?>  > <?php echo $y; ?> 
																	</option>
																<?php 
																}
																?>
															</select>
															&nbsp;&nbsp;&nbsp;
															<label>Status: </label>
															<select id="opt_filter_status_lc" >
																<option value="24" selected="selected" >Published/Unpublished</option>
																<option value="3"  > Expire </option>
															</select> 
															<input type="button" value="Filter" name="btnfilterCards" id="btnfilterCards" />
											</div>
										
										
												
												<table class="tableMerchant display" id="card_table">
													<thead>
									
														<tr>
															<th class="lcr_head_title" >Card Title</th>
															<th class="ldr_head_card_status">Card Status</th>
															<th class="lcr_head_date" >Date Created</th>
														</tr>
													</thead>
												</table>
											</div>
										</td>
									</tr>
								</tbody>
						</table>	
							
						<div style="width: 100%;border-top: 1px dashed #eaeaea; margin-top:10px;"></div>
								<!--<button id="add_tab">Add Tab</button>-->
								
								
						</div>
						
					</div>
					
						<div id="media-upload-header_loyaltyreport" style="display:none;">
									<ul id="sidemenu_loyaltyreport">
										<!--
										<li id="tab-type" class="div_card_1">
											<a class="current">card 1</a>
											<span class="loyaltyreport_close">X</span>
										</li>
										<li id="tab-type" class="div_card_2">
											<a >card 2</a>
											<span class="loyaltyreport_close">X</span>
										</li>
										-->
									</ul>
								</div>
								<div class="inner_tabdiv_loyaltyreport" style="display:none;">
									<!--
									<div id="div_card_1" class="tabs1" style="display: block;">div_card_1</div>
									<div id="div_card_2" class="tabs1" style="display: none;">div_card_2</div>
									-->
								</div>
					</div>
					
		                
		                <?php ?>

		                <div id="campaign_report" class="tabs campaign_rep_tab width_948" style="display:none;">
		                    Campaign Statistics report
		                </div>
		                
		                <div id="newcampaign_report" class="tabs" style="display:none;">
							
							<div style="background-color: rgb(240, 240, 240); padding: 5px; width: 99%;margin-bottom: 10px;margin-left:5px;border:1px solid #aaa;border-radius:5px;">
							<p ><span class="report_heading"><label id="campaign_title"></label></span></p>
								<table style="width: 100%;">
									<tr class="total_camp">
										<td style="width:20%;">
											<p><span class="report_heading"><?php echo $merchant_msg['report']['toatl_campaign_cost']; ?></span></p>
										</td>
										<td>
											<label id="total_campaign_cost"></label>
										</td>
									</tr>
									<tr class="total_camp">
										<td>
											<p class=""><span class="report_heading"><?php echo $merchant_msg['report']['total_campaign_revenue']; ?></span></p>
										</td>
										<td>
											<label id="total_campaign_revenue"></label>
										</td>
									</tr>            
								</table>
							</div>
							<div style="overflow:hidden;">
								<div class= "campaign_metrics_container" >
									<div class="campaign_metrics_filter" style="display:none;">
										<label>Filter By :</label>
										<select id="opt_filter_activation_metrics" >
										</select>
									</div>
									<div class="loader_activation_metrics" style="display: none;">
										<span class="loader_text">Please wait we are generating your report ...</span>
									</div>
									<div class="nodata_activation_metrics" style="display: none;">
										<span class="">Not enough data to generate report ...</span>
									</div>
									<div id="campaign_activation_code" style="overflow:hidden;width:100%;z-index:1;">
										
									</div>
								</div>
								<div class= "campaign_metrics_container" >
									<div class="campaign_metrics_filter" style="display:none;">
										<label>Filter By :</label>
										<select id="opt_filter_point_metrics" >
										</select>
									</div>
									<div class="loader_point_metrics" style="display: none;">
										<span class="loader_text">Please wait we are generating your report ...</span>
									</div>
									<div class="nodata_point_metrics" style="display: none;">
										<span class="">Not enough data to generate report ...</span>
									</div>
									<div id="campaign_point" style="overflow:hidden;width:100%;z-index:1;">
										
									</div>
								</div>
							</div>
							<div style="overflow:hidden;">
								<div class= "campaign_metrics_container" >
									<div class="campaign_metrics_filter" style="display:none;">
										<label>Filter By :</label>
										<select id="opt_filter_qrcode_metrics" >
										</select>
									</div>
									<div class="loader_qrcode_metrics" style="display: none;">
										<span class="loader_text">Please wait we are generating your report ...</span>
									</div>
									<div class="nodata_qrcode_metrics" style="display: none;">
										<span class="">Not enough data to generate report ...</span>
									</div>
									<div id="campaign_qrcode" style="overflow:hidden;width:100%;z-index:1;">
										
									</div>
								</div>
								<div class= "campaign_metrics_container" >
									<div class="campaign_metrics_filter" style="display:none;">
										<label>Filter By :</label>
										<select id="opt_filter_revenueage_metrics" >
										</select>
									</div>
									<div class="loader_revenueage_metrics" style="display: none;">
										<span class="loader_text">Please wait we are generating your report ...</span>
									</div>
									<div class="nodata_revenueage_metrics" style="display: none;">
										<span class="">Not enough data to generate report ...</span>
									</div>
									<div id="revenue_metrics_byage" style="overflow:hidden;width:100%;z-index:1;">
										
									</div>
								</div>
							</div>
							<div style="overflow:hidden;">
								<div class= "campaign_metrics_container" >
									<div class="campaign_metrics_filter" style="display:none;">
										<label>Filter By :</label>
										<select id="opt_filter_revenuetime_metrics" >
										</select>
									</div>
									<div class="loader_revenuetime_metrics" style="display: none;">
										<span class="loader_text">Please wait we are generating your report ...</span>
									</div>
									<div class="nodata_revenuetime_metrics" style="display: none;">
										<span class="">Not enough data to generate report ...</span>
									</div>
									<div id="revenue_metrics_bytime" style="overflow:hidden;width:100%;z-index:1;">
										
									</div>
								</div>
								<div class= "campaign_metrics_container" >
									<div class="campaign_metrics_filter" style="display:none;">
										<label>Filter By :</label>
										<select id="opt_filter_viewshare_metrics" >
										</select>
									</div>
									<div class="loader_viewshare_metrics" style="display: none;">
										<span class="loader_text">Please wait we are generating your report ...</span>
									</div>
									<div class="nodata_viewshare_metrics" style="display: none;">
										<span class="">Not enough data to generate report ...</span>
									</div>
									<div id="campaign_view_share_chart_container" style="overflow:hidden;width:100%;z-index:1;">
										
									</div>
								</div>
							</div>
							<input type="hidden" name="hdncampaignid" id="hdncampaignid" />
						</div>
						
		                <div id="loyaltycard_report" class="tabs" style="display:none;">
							<div style="overflow:hidden;">
								<div class= "loyalty_card_metrics_container" >
									<div class="loyalty_card_metrics_filter" style="display:none;">
										<label>Filter By :</label>
										<select id="opt_filter_days_metric" >
											<option value="30" selected="selected" >30 Days</option>
											<option value="90">90 Days</option>
											<option value="180">6 Month</option>
											<option value="365">1 Year</option>
										</select>
									</div>
									<div class="loader_loyalty_metrics" style="display: none;">
										<span class="loader_text">Please wait we are generating your report ...</span>
									</div>
									<div class="nodata_loyalty_metrics nodataloyalty" style="display: none;">
										<span class="">Not enough data to generate report ...</span>
									</div>
									<div id="card_metrics_drilldown_container" style="overflow:hidden;width:100%;z-index:1;">
										
									</div>
								</div>
								<div class ="loyalty_card_metrics_container" >
									<div class="loyalty_card_metrics_filter" style="display:none;">
										<label>Filter By :</label>
										<select id="opt_filter_days_revenue" >
											<option value="30" selected="selected" >30 Days</option>
											<option value="90">90 Days</option>
											<option value="180">6 Month</option>
											<option value="365">1 Year</option>
										</select>
									</div>
									<div class="loader_loyalty_revenue" style="display: none;">
										<span class="loader_text">Please wait we are generating your report ...</span>
									</div>
									<div class="nodata_loyalty_revenue nodataloyalty" style="display: none;">
										<span class="">Not enough data to generate report ...</span>
									</div>
									<div id="card_revenue_container" style="overflow:hidden;width:100%;z-index:1;">
					
									</div>
								</div>
							</div>
							<div class ="chart_container_100">
								<div class="loyalty_card_metrics_filter" style="display:none;">
									<label>Filter By :</label>
									<select id="opt_filter_location_age" >
										
									</select>
									<select id="opt_filter_days_age" >
										<option value="30" selected="selected" >30 Days</option>
										<option value="90">90 Days</option>
										<option value="180">6 Month</option>
										<option value="365">1 Year</option>
									</select>
								</div>
								<div class="loader_loyalty_age" style="display: none;">
									<span class="loader_text">Please wait we are generating your report ...</span>
								</div>
								<div class="nodata_loyalty_age nodataloyalty" style="display: none;">
									<span class="">Not enough data to generate report ...</span>
								</div>	
								<div id="card_visit_by_age_container" style="overflow:hidden;width:100%;z-index:1;">
				
								</div>
							</div>
							<div class="chart_container_100">
								<div class="loyalty_card_metrics_filter" style="display:none;">
									<label>Filter By :</label>
									<select id="opt_filter_location_time" >
										
									</select>
									<select id="opt_filter_days_time" >
										<option value="30" selected="selected" >30 Days</option>
										<option value="90">90 Days</option>
										<option value="180">6 Month</option>
										<option value="365">1 Year</option>
									</select>
								</div>
								<div class="loader_loyalty_time" style="display: none;">
									<span class="loader_text" >Please wait we are generating your report ...</span>
								</div>
								<div class="nodata_loyalty_time nodataloyalty" style="display: none;">
									<span class="">Not enough data to generate report ...</span>
								</div>	
								<div id="card_visit_by_time_container" style="overflow:hidden;width:100%;z-index:1;">
				
								</div>
							</div>
							<input type="hidden" name="hdncardid" id="hdncardid" />
		                </div>
		                <div class="clear">&nbsp;</div>
		            </div>
		            <!--end of content--></div>
		        <!--end of contentContainer--></div>

		    <!---------start footer--------------->
		    <div>
		        <?php
		        require_once(MRCH_LAYOUT . "/footer.php");
		        ?>
		        <!--end of footer--></div>

		</div>
		<?php
		$_SESSION['msg'] = "";
		?>
		<div id="dialog-message" title="Message" style="display:none">
		</div>
		<div id="NotificationloaderPopUpContainer" class="container_popup"  style="display: none;">
		    <div id="NotificationloaderBackDiv" class="divBack">
		    </div>
		    <div id="NotificationloaderFrontDivProcessing" class="Processing" style="display:none;">

		        <div id="NotificationloaderMaindivLoading" align="center" valign="middle" class="imgDivLoading"
		             >

		            <div id="NotificationloadermainContainer" class="loading innerContainer" style="height:auto;width:auto">
		                <img src="<?= ASSETS_IMG ?>/128.GIF" style="display: block;" id="image_loader_div"/>
		            </div>
		        </div>
		    </div>
		</div>
		
		
		<script>
		
		
		// start loyalty card report tabs
		
		
		
		// end loyalty card report tabs
		
		$('body').on('click', '.close_loyalty_report_form', function () {
			if($("#loyalty_report_form").css("display")=="block")
			{
				$('#loyalty_report_form').slideUp(500);
				$(this).text("+");
			}
			else
			{
				$('#loyalty_report_form').slideDown(500);
				$(this).text("×");
			}
           
        });
		        $ = jQuery.noConflict();
		        $(document).ready(function () {

						// start for loyalty cards
						
						var loyalty_metric_chart,loyalty_revenue_chart,loyalty_visitbyage_chart,loyalty_visitbytime_chart;
						var campaign_activation_code_metrics,campaign_point_metrics,campaign_qrcode_metrics,revenue_metrics_byage,revenue_metrics_bytime,campaign_view_share_chart_container;
						
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

						function fnDataTablesPipeline(sSource, aoData, fnCallback) 
						{
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
						
					  //data table function on pricelist index page table
						var oTable = jQuery('#card_table').dataTable( {
							//"bStateSave": true,
						   "bFilter": false,
							"bSort" : false,
							"bLengthChange": false,
							"info": false,
							"iDisplayLength": 10,
							"bProcessing": true,
							 "bServerSide": true,
							"oLanguage": {
											"sEmptyTable": "No card founds in the system. Please add at least one.",
											"sZeroRecords": "No card to display",
											"sProcessing": "Loading..."
										},
							 "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",            
							 "fnServerParams": function (aoData) {
											aoData.push({"name": "btnGetAllCardlistReport", "value": true},{"name": "status", "value": jQuery('#opt_filter_status_lc').val()},{"name": "year", "value": jQuery('#opt_filter_year_lc').val()} )
											//bind_more_action_click();
										},
							 //"fnServerData": fnDataTablesPipeline,     
							 
							 "aoColumns": [
											{"bVisible": true, "bSearchable": false, "bSortable": false},
											{"bVisible": true, "bSearchable": false, "bSortable": false},
											{"bVisible": true, "bSearchable": false, "bSortable": false}
										]      
										
						} );
						
						jQuery("#btnfilterCards").click(function(){
					  
							 jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
										oTable.fnDraw();		
									}
								}
							});
						});
						
						jQuery(".getanalytics").live("click",function(){
							
							
							var card_id = jQuery(this).attr('cardid');
							var str_text = jQuery(this).text();
							//jQuery("#hdncardid").val(card_id);
							var active = jQuery(this).attr('active');
							var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
							//alert(card_id);			
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{

											if(jQuery("#sidemenu_loyaltyreport .div_card_"+card_id).length>0) // loyalty report is already there then not call api
											{
											}
											else
											{
												jQuery("#media-upload-header_loyaltyreport").css("display","block");
												jQuery(".inner_tabdiv_loyaltyreport").css("display","block");
												
												jQuery("#sidemenu_loyaltyreport li").each(function () { // remove current class from all anchor
													jQuery(this).find("a").removeClass("current");
												});
												jQuery(".inner_tabdiv_loyaltyreport .tabs1").each(function () { // display none all loyalty report 
													jQuery(this).css("display", "none");
												});	
											
												var html_li ='<li id="tab-type" class="div_card_'+card_id+'"><a class="current">'+str_text+'</a><span class="loyaltyreport_close">X</span></li>';
												jQuery("#sidemenu_loyaltyreport").append(html_li);
												
												var html_div='<div id="div_card_'+card_id+'" class="tabs1" style="display: block;">';
												//html_div +='card id = '+card_id;
												
												html_div +='<div style="overflow:hidden;">\
												<div class= "loyalty_card_metrics_container metricchart">\
													<div class="loyalty_card_metrics_filter" >\
														<label>Filter By :</label>\
														<select id="opt_filter_days_metric" card_id="'+card_id+'">\
															<option value="30" selected="selected" >30 Days</option>\
															<option value="90">90 Days</option>\
															<option value="180">6 Month</option>\
															<option value="365">1 Year</option>\
														</select>\
													</div>\
													<div class="loader_loyalty_metrics" style="display: none;">\
														<span class="loader_text">Please wait we are generating your report ...</span>\
													</div>\
													<div class="nodata_loyalty_metrics nodataloyalty" style="display: none;">\
														<span class="">Not enough data to generate report ...</span>\
													</div>\
													<div id="card_metrics_drilldown_container_'+card_id+'" style="overflow:hidden;width:100%;z-index:1;">\
														\
													</div>\
												</div>\
												<div class ="loyalty_card_metrics_container revenuechart" >\
													<div class="loyalty_card_metrics_filter" >\
														<label>Filter By :</label>\
														<select id="opt_filter_days_revenue" card_id="'+card_id+'">\
															<option value="30" selected="selected" >30 Days</option>\
															<option value="90">90 Days</option>\
															<option value="180">6 Month</option>\
															<option value="365">1 Year</option>\
														</select>\
													</div>\
													<div class="loader_loyalty_revenue" style="display: none;">\
														<span class="loader_text">Please wait we are generating your report ...</span>\
													</div>\
													<div class="nodata_loyalty_revenue nodataloyalty" style="display: none;">\
														<span class="">Not enough data to generate report ...</span>\
													</div>\
													<div id="card_revenue_container_'+card_id+'" style="overflow:hidden;width:100%;z-index:1;">\
									\
													</div>\
												</div>\
											</div>\
											<div class ="chart_container_100 agechart">\
												<div class="loyalty_card_metrics_filter" >\
													<label>Filter By :</label>\
													<select id="opt_filter_location_age"  card_id="'+card_id+'">\
														\
													</select>\
													<select id="opt_filter_days_age"  card_id="'+card_id+'">\
														<option value="30" selected="selected" >30 Days</option>\
														<option value="90">90 Days</option>\
														<option value="180">6 Month</option>\
														<option value="365">1 Year</option>\
													</select>\
												</div>\
												<div class="loader_loyalty_age" style="display: none;">\
													<span class="loader_text">Please wait we are generating your report ...</span>\
												</div>\
												<div class="nodata_loyalty_age nodataloyalty" style="display: none;">\
													<span class="">Not enough data to generate report ...</span>\
												</div>	\
												<div id="card_visit_by_age_container_'+card_id+'" style="overflow:hidden;width:100%;z-index:1;">\
								\
												</div>\
											</div>\
											<div class="chart_container_100 timechart">\
												<div class="loyalty_card_metrics_filter" style="display:none;">\
													<label>Filter By :</label>\
													<select id="opt_filter_location_time"  card_id="'+card_id+'">\
														\
													</select>\
													<select id="opt_filter_days_time"  card_id="'+card_id+'">\
														<option value="30" selected="selected" >30 Days</option>\
														<option value="90">90 Days</option>\
														<option value="180">6 Month</option>\
														<option value="365">1 Year</option>\
													</select>\
												</div>\
												<div class="loader_loyalty_time" style="display: none;">\
													<span class="loader_text" >Please wait we are generating your report ...</span>\
												</div>\
												<div class="nodata_loyalty_time nodataloyalty" style="display: none;">\
													<span class="">Not enough data to generate report ...</span>\
												</div>	\
												<div id="card_visit_by_time_container_'+card_id+'" style="overflow:hidden;width:100%;z-index:1;">\
								\
												</div>\
											</div>\
											<input type="hidden" name="hdncardid" id="hdncardid" />\
										</div>';
												html_div +='</div>';
												
												jQuery(".inner_tabdiv_loyaltyreport").append(html_div);
												
												
												/*
												var obj_exist_in_cache=0;
												var cached_loyalty_object;
												jQuery.ajax({
													  type: "POST", // HTTP method POST or GET
													  url: "process.php", //Where to make Ajax calls
													  data:"check_object_in_memcached=yes&var="+card_id+"_objLoyaltyAll",
													  async:false,
													  success:function(response){
															var obj = jQuery.parseJSON(response);
															if (obj.status=="true") 
															{
																obj_exist_in_cache =1;
																//obj_exist_in_cache =0;
																cached_loyalty_object = obj.result;
															}
													  }
												});
												*/
												//if (obj_exist_in_cache==1)     
												if(card_id+"_objLoyaltyAll" in localStorage && localStorage.getItem(card_id+"_objLoyaltyAll") != null)
												{
													/*
													console.log(cached_loyalty_object);
													var obj = jQuery.parseJSON(cached_loyalty_object);
													//console.log(obj.status);
													*/
													var obj = JSON.parse(localStorage.getItem(card_id+"_objLoyaltyAll"));
													expireString = obj.timestamp,
													nowString = new Date().getTime().toString();
													console.log("et : "+expireString);
													console.log("ct : "+nowString);
													console.log(obj);
													obj=obj.value;
													/*
													if (nowString > expireString)
													{
														console.log("expire");
														localStorage.removeItem(card_id+"_objLoyaltyAll");
													}
													*/
																if (obj.status=="true") 
																{
																
																	
																
																	jQuery('#div_card_'+card_id+' .loyalty_card_metrics_filter').css("display","block");
																	jQuery("#div_card_"+card_id+" #opt_filter_location_age").empty().append(obj.options_age);															
																	jQuery("#div_card_"+card_id+" #opt_filter_location_time").empty().append(obj.options_time);
																
																	jQuery('#div_card_'+card_id+' #opt_filter_days_metric').prop('selectedIndex', 0);
																	jQuery('#div_card_'+card_id+' #opt_filter_days_revenue').prop('selectedIndex', 0);
																	jQuery('#div_card_'+card_id+' #opt_filter_days_age').prop('selectedIndex', 0);
																	jQuery('#div_card_'+card_id+' #opt_filter_days_time').prop('selectedIndex', 0);
																	
																	Highcharts.setOptions({
																		lang: {
																			drillUpText: '<< Back'
																		}
																	});
																	
																	// start drill down report									
															
																	var total_cards_activated=parseInt(obj.total_cards_activated);
																	var active_male=parseInt(obj.total_male_activated_cards);
																	var active_female=parseInt(obj.total_female_activated_cards);
																	var active_male_desktop=parseInt(obj.total_male_activated_desktop_cards);
																	var active_male_mobile=parseInt(obj.total_male_activated_mobile_cards);
																	var active_male_qrcode=parseInt(obj.total_male_activated_qr_cards);
																	var active_female_desktop=parseInt(obj.total_female_activated_desktop_cards);
																	var active_female_mobile=parseInt(obj.total_female_activated_mobile_cards);
																	var active_female_qrcode=parseInt(obj.total_female_activated_qr_cards);
																	
																	var total_cards_rewarded=parseInt(obj.total_cards_rewarded);
																	var reward_male=parseInt(obj.total_male_rewarded_cards);
																	var reward_female=parseInt(obj.total_female_rewarded_cards);
																	var reward_male_desktop=parseInt(obj.total_male_rewarded_desktop_cards);
																	var reward_male_mobile=parseInt(obj.total_male_rewarded_mobile_cards);
																	var reward_male_qrcode=parseInt(obj.total_male_rewarded_qr_cards);
																	var reward_female_desktop=parseInt(obj.total_female_rewarded_desktop_cards);
																	var reward_female_mobile=parseInt(obj.total_female_rewarded_mobile_cards);
																	var reward_female_qrcode=parseInt(obj.total_female_rewarded_qr_cards);
																	
																	var total_cards_deleted=parseInt(obj.total_cards_deleted);
																	var delete_male=parseInt(obj.total_male_deleted_cards);
																	var delete_female=parseInt(obj.total_female_deleted_cards);	
																	var delete_male_desktop=parseInt(obj.total_male_deleted_desktop_cards);
																	var delete_male_mobile=parseInt(obj.total_male_deleted_mobile_cards);
																	var delete_male_qrcode=parseInt(obj.total_male_deleted_qr_cards);
																	var delete_female_desktop=parseInt(obj.total_female_deleted_desktop_cards);
																	var delete_female_mobile=parseInt(obj.total_female_deleted_mobile_cards);
																	var delete_female_qrcode=parseInt(obj.total_female_deleted_qr_cards);
																	
																	if(total_cards_activated==0)
																		total_cards_activated_dd=null;
																	else
																		total_cards_activated_dd="total_cards_activated";
																		
																	if(total_cards_rewarded==0)
																		total_cards_rewarded_dd=null;
																	else
																		total_cards_rewarded_dd="total_cards_rewarded";	
																		
																	if(total_cards_deleted==0)
																		total_cards_deleted_dd=null;
																	else
																		total_cards_deleted_dd="total_cards_deleted";
																		
																	if(active_male==0)
																		active_male_dd=null;
																	else
																		active_male_dd="active_male";
																	
																	if(active_female==0)
																		active_female_dd=null;
																	else
																		active_female_dd="active_female";		
																		
																			
																	// Create the chart
			
			
																	loyalty_metric_chart = new Highcharts.Chart({
																		chart: {
																			renderTo: 'card_metrics_drilldown_container_'+card_id,
																			type: 'column',
																			//borderColor: 'rgb(69, 114, 167)',
																			//borderWidth: 1
																			//plotBorderColor: 'rgb(69, 114, 167)',
																			//plotBorderWidth:2
																			
																		},
																		title: {
																			text: 'Card Metrics'
																		},
																		subtitle: {
																			text: 'Click the columns to view detail result'
																		},
																		xAxis: {
																			type: 'category'
																			
																		},
																		yAxis: {
																			title: {
																				text: 'Total Cards'
																			},
																			 gridLineWidth: 0,
																			  lineWidth:1
																		},
																		legend: {
																			enabled: true
																		},
																		credits: {
																			enabled: false
																		},
																		plotOptions: {
																			series: {
																				borderWidth: 0,
																				dataLabels: {
																					enabled: true,
																					format: '{point.y:.1f}'
																				}
																			}
																		},
																		
																		tooltip: {
																			headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																			pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
																		},
																		
																		series: [{
																			name: 'Loyalty Card Metrics',
																			colorByPoint: true,
																			data: [{
																						name: 'Total Cards Activated',
																						y: total_cards_activated,
																						drilldown: total_cards_activated_dd,															
																					},
																					{
																						name: 'Total Cards Rewarded',
																						y: total_cards_rewarded,
																						drilldown: total_cards_rewarded_dd
																					},
																					{
																						name: 'Total Cards Deleted',
																						y: total_cards_deleted,
																						drilldown: total_cards_deleted_dd
																					},
																					]
																		}],
																		drilldown: {
																			drillUpButton: {
																				relativeTo: 'spacingBox',
																				position: {
																					y: 0,
																					x: 0
																				},
																				theme: {
																					fill: '#eaeaea',
																					'stroke-width': 1,
																					stroke: 'silver',
																					r: 0,
																					states: {
																						
																						select: {
																							stroke: '#039',
																							fill: '#bada55'
																						}
																					}
																				}

																			},
																			series: [{
																						id: 'total_cards_activated',
																						name: 'Total Cards Activated',
																						data: [{
																								name: 'Male',
																								y: active_male,
																								drilldown: active_male_dd
																								},
																								{
																								name: 'Female',
																								y: active_female,
																								drilldown: active_female_dd
																								}
																							]
																					},
																					{
																						id: 'total_cards_rewarded',
																						name: 'Total Cards Rewarded',
																						data: [{
																								name: 'Male',
																								y: reward_male,
																								//drilldown: 'reward_male'
																								},
																								{
																								name: 'Female',
																								y: reward_female,
																								//drilldown: 'reward_female'
																								}
																							]
																					},
																					{
																						id: 'total_cards_deleted',
																						name: 'Total Cards Deleted',
																						data: [{
																								name: 'Male',
																								y: delete_male,
																								//drilldown: 'delete_male'
																								},
																								{
																								name: 'Female',
																								y: delete_female,
																								//drilldown: 'delete_female'
																								}
																							]
																					}, 
																					{

																						id: 'active_male',
																						name: 'Total Cards Activated By Male',
																						data: [
																							   ['Mobile Device',active_male_mobile],
																							   ['QR Code Scan',active_male_qrcode]]
																					}, 
																					{

																						id: 'active_female',
																						name: 'Total Cards Activated By Female',
																						data: [
																							   ['Mobile Device',active_female_mobile],
																							   ['QR Code Scan',active_female_qrcode]]
																					}, 
																					{

																						id: 'reward_male',
																						name: 'Total Cards Rewarded By Male',
																						data: [
																							   ['Mobile Device',reward_male_mobile],
																							   ['QR Code Scan',reward_male_qrcode]]
																					}, 
																					{

																						id: 'reward_female',
																						name: 'Total Cards Rewarded By Female',
																						data: [
																							   ['Mobile Device',reward_female_mobile],
																							   ['QR Code Scan',reward_female_qrcode]]
																					}, 
																					{

																						id: 'delete_male',
																						name: 'Total Cards Deleted By Male',
																						data: [
																							   ['Mobile Device',delete_male_mobile],
																							   ['QR Code Scan',delete_male_qrcode]]
																					}, 
																					{

																						id: 'delete_female',
																						name: 'Total Cards Deleted By Female',
																						data: [
																							   ['Mobile Device',delete_female_mobile],
																							   ['QR Code Scan',delete_female_qrcode]]
																					}]
																		}
																	});   
																	
																	// end drill down report
																	
																	// start revenue report
															
																	var categories = ['Total Revenue','Average Revenue per visit','Average  Revenue per location'];
																	
																	var total_revenue=parseInt(obj.total_revenue);
																	var total_male_revenue=parseInt(obj.total_male_revenue);
																	var total_female_revenue=parseInt(obj.total_female_revenue);
																	
																	var total_average_revenue_per_visit=parseInt(obj.total_average_revenue_per_visit);
																	var total_average_male_revenue_per_visit=parseInt(obj.total_average_male_revenue_per_visit);
																	var total_average_female_revenue_per_visit=parseInt(obj.total_average_female_revenue_per_visit);
					
																	var total_average_revenue_per_location=parseInt(obj.total_average_revenue_per_location);
																	var total_average_male_revenue_per_location=parseInt(obj.total_average_male_revenue_per_location);
																	var total_average_female_revenue_per_location=parseInt(obj.total_average_female_revenue_per_location);
																	
																	if(total_revenue==0)
																		total_revenue_dd=null;
																	else
																		total_revenue_dd="total_revenue";
																	
																	if(total_average_revenue_per_visit==0)
																		average_revenue_per_visit_dd=null;
																	else
																		average_revenue_per_visit_dd="average_revenue_per_visit";	
																		
																	if(total_average_revenue_per_location==0)
																		average_revenue_per_location_dd=null;
																	else
																		average_revenue_per_location_dd="average_revenue_per_location";	
		
																	//jQuery('#card_revenue_container').highcharts({
																	loyalty_revenue_chart = new Highcharts.Chart({
																			chart: {
																				renderTo: 'card_revenue_container_'+card_id,
																				type: 'column',
																				//borderColor: 'rgb(69, 114, 167)',
																				//borderWidth: 1,
																				//plotBorderColor: 'rgb(69, 114, 167)',
																				//plotBorderWidth:2
																				
																			},
																			title: {
																				text: 'Revenue Summary'
																			},
																			subtitle: {
																				text: 'Click the columns to view detail result'
																			},
																			xAxis: {
																				type: 'category',
																				
																			},
																			yAxis: {
																				title: {
																					text: 'Revenue'
																				},
																				 gridLineWidth: 0,
																				lineWidth:1
																			},
																			legend: {
																				enabled: true
																			},
																			credits: {
																				enabled: false
																			},
																			plotOptions: {
																				series: {
																					borderWidth: 0,
																					dataLabels: {
																						enabled: true,
																						format: '{point.y:.1f}'
																					}
																				}
																			},
																			
																			tooltip: {
																				headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																				pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
																			},
																			
																			series: [{
																				name: 'Revenue Metrics',
																				colorByPoint: true,
																				data: [{
																							name: 'Total Revenue',
																							y: total_revenue,
																							drilldown: total_revenue_dd
																						},
																						{
																							name: 'Average Revenue per visit',
																							y: total_average_revenue_per_visit,
																							drilldown: average_revenue_per_visit_dd
																						},
																						{
																							name: 'Average  Revenue per location',
																							y: total_average_revenue_per_location,
																							drilldown: average_revenue_per_location_dd
																						},
																						]
																			}],
																			drilldown: {
																				drillUpButton: {
																					relativeTo: 'spacingBox',
																					position: {
																						y: 0,
																						x: 0
																					},
																					theme: {
																						fill: '#eaeaea',
																						'stroke-width': 1,
																						stroke: 'silver',
																						r: 0,
																						states: {
																							select: {
																								stroke: '#039',
																								fill: '#bada55'
																							}
																						}
																					}

																				},
																				series: [{
																							id: 'total_revenue',
																							name: 'Total Revenue',
																							data: [{
																									name: 'Male',
																									y: total_male_revenue
																									},
																									{
																									name: 'Female',
																									y: total_female_revenue
																									}
																								]
																						},
																						{
																							id: 'average_revenue_per_visit',
																							name: 'Average Revenue Per Visit',
																							data: [{
																									name: 'Male',
																									y: total_average_male_revenue_per_visit
																									},
																									{
																									name: 'Female',
																									y: total_average_female_revenue_per_visit
																									}
																								]
																						},
																						{
																							id: 'average_revenue_per_location',
																							name: 'Average Revenue Per Location',
																							data: [{
																									name: 'Male',
																									y: total_average_male_revenue_per_location
																									},
																									{
																									name: 'Female',
																									y: total_average_female_revenue_per_location
																									}
																								]
																						}]
																			}
																		});
																
																// end revenue report	
																					
																	// start for customer visit by age
															
															var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
															
															var f_17 = parseInt(obj.f_17);
															var f_18 = parseInt(obj.f_18);
															var f_25 = parseInt(obj.f_25);
															var f_45 = parseInt(obj.f_45);
															var f_55 = parseInt(obj.f_55);
															var f_65 = parseInt(obj.f_65);
															
															var female_visit = [f_17,f_18,f_25,f_45,f_55,f_65];
															
															var m_17 = parseInt(obj.m_17);
															var m_18 = parseInt(obj.m_18);
															var m_25 = parseInt(obj.m_25);
															var m_45 = parseInt(obj.m_45);
															var m_55 = parseInt(obj.m_55);
															var m_65 = parseInt(obj.m_65);
															
															var male_visit = [m_17,m_18,m_25,m_45,m_55,m_65];
															
															var rf_17 = parseInt(obj.rf_17);
															var rf_18 = parseInt(obj.rf_18);
															var rf_25 = parseInt(obj.rf_25);
															var rf_45 = parseInt(obj.rf_45);
															var rf_55 = parseInt(obj.rf_55);
															var rf_65 = parseInt(obj.rf_65);
															
															var revenue_female = [rf_17,rf_18,rf_25,rf_45,rf_55,rf_65];
															
															var rm_17 = parseInt(obj.rm_17);
															var rm_18 = parseInt(obj.rm_18);
															var rm_25 = parseInt(obj.rm_25);
															var rm_45 = parseInt(obj.rm_45);
															var rm_55 = parseInt(obj.rm_55);
															var rm_65 = parseInt(obj.rm_65);
															
															var revenue_male = [rm_17,rm_18,rm_25,rm_45,rm_55,rm_65];
															
															var rt_17 = parseInt(obj.rt_17);
															var rt_18 = parseInt(obj.rt_18);
															var rt_25 = parseInt(obj.rt_25);
															var rt_45 = parseInt(obj.rt_45);
															var rt_55 = parseInt(obj.rt_55);
															var rt_65 = parseInt(obj.rt_65);
															
															var revenue_total = [rt_17,rt_18,rt_25,rt_45,rt_55,rt_65];
															
															//jQuery('#card_visit_by_age_container').highcharts({
															loyalty_visitbyage_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_age_container_'+card_id
																	//plotBorderColor: 'rgb(69, 114, 167)',
																	//plotBorderWidth:2
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Age',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Customer Age'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#9BBB59',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#C0504D',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
															
															// end for customer visit by age
															
															// start for customer visit by time
															
															var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
															
															var f_1 = parseInt(obj.f_1);
															var f_2 = parseInt(obj.f_2);
															var f_3 = parseInt(obj.f_3);
															var f_4 = parseInt(obj.f_4);
															
															var female_visit = [f_1,f_2,f_3,f_4];
															
															var m_1 = parseInt(obj.m_1);
															var m_2 = parseInt(obj.m_2);
															var m_3 = parseInt(obj.m_3);
															var m_4 = parseInt(obj.m_4);
															
															var male_visit = [m_1,m_2,m_3,m_4];
															
															var rf_1 = parseInt(obj.rf_1);
															var rf_2 = parseInt(obj.rf_2);
															var rf_3 = parseInt(obj.rf_3);
															var rf_4 = parseInt(obj.rf_4);
															
															var revenue_female = [rf_1,rf_2,rf_3,rf_4];
															
															var rm_1 = parseInt(obj.rm_1);
															var rm_2 = parseInt(obj.rm_2);
															var rm_3 = parseInt(obj.rm_3);
															var rm_4 = parseInt(obj.rm_4);
															
															var revenue_male = [rm_1,rm_2,rm_3,rm_4];
															
															var rt_1 = parseInt(obj.rt_1);
															var rt_2 = parseInt(obj.rt_2);
															var rt_3 = parseInt(obj.rt_3);
															var rt_4 = parseInt(obj.rt_4);
															
															var revenue_total = [rt_1,rt_2,rt_3,rt_4];
															
															//jQuery('#card_visit_by_time_container').highcharts({
															loyalty_visitbytime_chart = new Highcharts.Chart({
																	chart: {
																		renderTo: 'card_visit_by_time_container_'+card_id
																	},
																	title: {
																		text: 'Customer Visit & Revenue Metrics By Time',
																		x: -20 //center
																	},
																	subtitle: {
																		text: '',
																		x: -20
																	},
																	credits: {
																		enabled: false
																	},
																	xAxis:{
																		title: {
																			text: 'Time'
																		},
																		categories: categories
																	},
																	yAxis: [{ // Primary yAxis
																		   title: {
																			   text: 'Customer Visits'
																		   },
																		   min: 0,
																		   gridLineWidth: 0,
																		   lineWidth:1
																	  }, { // Secondary yAxis
																		title: {
																			text: 'Revenue'
																		},
																		  min: 0,
																		  gridLineWidth: 0,
																		  lineWidth:1,
																		opposite: true
																	}],       
																	legend: {
																		layout: 'vertical',
																		align: 'right',
																		verticalAlign: 'middle',
																		borderWidth: 0
																	},
																	/*
																	plotOptions: {
																		series: {
																			stacking: 'normal'
																		}
																	},
																	*/
																	series: [{
																		name: 'Female Visit',
																		color: '#7E629F',
																		type: 'column',
																		yAxis: 0,
																		data: female_visit
																	},{
																		name: 'Male Visit',
																		color: '#9ABA59',
																		type: 'column',
																		yAxis: 0,
																		data: male_visit
																	},{
																		name: 'Revenue Female',
																		color: '#F59240',
																		type: 'spline',
																		yAxis: 1,
																		data: revenue_female
																	},{
																		name: 'Revenue Male',
																		color: '#46AAC4',
																		type: 'spline',
																		yAxis: 1,
																		data: revenue_male
																	},{
																		name: 'Total Revenue',
																		color: '#7D5FA0',
																		type: 'spline',
																		yAxis: 1,
																		data: revenue_total
																	}]
																});
															
															// end for customer visit by time
															
															if(total_cards_activated==0 && total_cards_rewarded==0 && total_cards_deleted==0)												
															{
																//jQuery('#div_card_'+card_id+' .metricchart .nodata_loyalty_metrics').css("display","block");
																//jQuery('#card_metrics_drilldown_container_'+card_id).css("display","none");
																//jQuery('#div_card_'+card_id+' .metricchart .loyalty_card_metrics_filter').css("display","none");
																
																loyalty_metric_chart = new Highcharts.Chart({
																		chart: {
																			renderTo: 'card_metrics_drilldown_container_'+card_id,
																			type: 'column',
																			//borderColor: 'rgb(69, 114, 167)',
																			//borderWidth: 1
																			//plotBorderColor: 'rgb(69, 114, 167)',
																			//plotBorderWidth:2
																			
																		},
																		title: {
																			text: 'Card Metrics'
																		},
																		subtitle: {
																			text: 'Click the columns to view detail result'
																		},
																		xAxis: {
																			type: 'category'
																			
																		},
																		yAxis: {
																			title: {
																				text: 'Total Cards'
																			},
																			 gridLineWidth: 0,
																			  lineWidth:1
																		},
																		legend: {
																			enabled: true
																		},
																		credits: {
																			enabled: false
																		},															
																		series: [{
																			name: 'Loyalty Card Metrics',
																			colorByPoint: true,
																			data: []
																		}]
																	}); 
															}
															if(total_revenue==0 && total_average_revenue_per_visit==0 && total_average_revenue_per_location==0)												
															{
																//jQuery('#div_card_'+card_id+' .revenuechart .nodata_loyalty_revenue').css("display","block");
																//jQuery('#card_revenue_container_'+card_id).css("display","none");
																//jQuery('#div_card_'+card_id+' .revenuechart .loyalty_card_metrics_filter').css("display","none");
																
																loyalty_revenue_chart = new Highcharts.Chart({
																			chart: {
																				renderTo: 'card_revenue_container_'+card_id,
																				type: 'column',
																				//borderColor: 'rgb(69, 114, 167)',
																				//borderWidth: 1,
																				//plotBorderColor: 'rgb(69, 114, 167)',
																				//plotBorderWidth:2
																				
																			},
																			title: {
																				text: 'Revenue Summary'
																			},
																			subtitle: {
																				text: 'Click the columns to view detail result'
																			},
																			xAxis: {
																				type: 'category',
																				
																			},
																			yAxis: {
																				title: {
																					text: 'Revenue'
																				},
																				 gridLineWidth: 0,
																				lineWidth:1
																			},
																			legend: {
																				enabled: true
																			},
																			credits: {
																				enabled: false
																			},
																			series: [{
																				name: 'Revenue Metrics',
																				colorByPoint: true,
																				data: []
																		}]
																	});
																			
															}
															if(f_17==0 && f_18==0 && f_25==0 && f_45==0 && f_55==0 && f_65==0 && 
																m_17==0 && m_18==0 && m_25==0 && m_45==0 && m_55==0 && m_65==0 && 
																rf_17==0 && rf_18==0 && rf_25==0 && rf_45==0 && rf_55==0 && rf_65==0 && 
																rm_17==0 && rm_18==0 && rm_25==0 && rm_45==0 && rm_55==0 && rm_65==0 && 
																rt_17==0 && rt_18==0 && rt_25==0 && rt_45==0 && rt_55==0 && rt_65==0 )												
															{
																//jQuery('#div_card_'+card_id+' .agechart .nodata_loyalty_age').css("display","block");
																//jQuery('#card_visit_by_age_container_'+card_id).css("display","none");
																//jQuery('#div_card_'+card_id+' .agechart .loyalty_card_metrics_filter').css("display","none");
																
																	loyalty_visitbyage_chart = new Highcharts.Chart({
																	chart: {
																		renderTo: 'card_visit_by_age_container_'+card_id
																		//plotBorderColor: 'rgb(69, 114, 167)',
																		//plotBorderWidth:2
																	},
																	title: {
																		text: 'Customer Visit & Revenue Metrics By Age',
																		x: -20 //center
																	},
																	subtitle: {
																		text: '',
																		x: -20
																	},
																	credits: {
																		enabled: false
																	},
																	xAxis:{
																		title: {
																			text: 'Customer Age'
																		},
																		categories: categories
																	},
																	yAxis: [{ // Primary yAxis
																		   title: {
																			   text: 'Customer Visits'
																		   },
																		   min: 0,
																		   gridLineWidth: 0,
																		   lineWidth:1
																	  }, { // Secondary yAxis
																		title: {
																			text: 'Revenue'
																		},
																		  min: 0,
																		  gridLineWidth: 0,
																		  lineWidth:1,
																		opposite: true
																	}],       
																	legend: {
																		layout: 'vertical',
																		align: 'right',
																		verticalAlign: 'middle',
																		borderWidth: 0
																	},																
																	series: [{
																		name: 'Female Visit',
																		color: '#9BBB59',
																		type: 'column',
																		yAxis: 0,
																		data: []
																	},{
																		name: 'Male Visit',
																		color: '#C0504D',
																		type: 'column',
																		yAxis: 0,
																		data: []
																	},{
																		name: 'Revenue Female',
																		color: '#F59240',
																		type: 'spline',
																		yAxis: 1,
																		data: []
																	},{
																		name: 'Revenue Male',
																		color: '#46AAC4',
																		type: 'spline',
																		yAxis: 1,
																		data: []
																	},{
																		name: 'Total Revenue',
																		color: '#7D5FA0',
																		type: 'spline',
																		yAxis: 1,
																		data: []
																	}]
																});
															}
															if(f_1==0 && f_2==0 && f_3==0 && f_4==0 && 
																m_1==0 && m_2==0 && m_3==0 && m_4==0 && 
																rf_1==0 && rf_2==0 && rf_3==0 && rf_4==0 && 
																rt_1==0 && rt_2==0 && rt_3==0 && rt_4==0 )												
															{
																//jQuery('#div_card_'+card_id+' .timechart .nodata_loyalty_time').css("display","block");
																//jQuery('#card_visit_by_time_container_'+card_id).css("display","none");
																//jQuery('#div_card_'+card_id+' .timechart .loyalty_card_metrics_filter').css("display","none");
																
																loyalty_visitbytime_chart = new Highcharts.Chart({
																	chart: {
																		renderTo: 'card_visit_by_time_container_'+card_id
																	},
																	title: {
																		text: 'Customer Visit & Revenue Metrics By Time',
																		x: -20 //center
																	},
																	subtitle: {
																		text: '',
																		x: -20
																	},
																	credits: {
																		enabled: false
																	},
																	xAxis:{
																		title: {
																			text: 'Time'
																		},
																		categories: categories
																	},
																	yAxis: [{ // Primary yAxis
																		   title: {
																			   text: 'Customer Visits'
																		   },
																		   min: 0,
																		   gridLineWidth: 0,
																		   lineWidth:1
																	  }, { // Secondary yAxis
																		title: {
																			text: 'Revenue'
																		},
																		  min: 0,
																		  gridLineWidth: 0,
																		  lineWidth:1,
																		opposite: true
																	}],       
																	legend: {
																		layout: 'vertical',
																		align: 'right',
																		verticalAlign: 'middle',
																		borderWidth: 0
																	},																
																	series: [{
																		name: 'Female Visit',
																		color: '#7E629F',
																		type: 'column',
																		yAxis: 0,
																		data: []
																	},{
																		name: 'Male Visit',
																		color: '#9ABA59',
																		type: 'column',
																		yAxis: 0,
																		data: []
																	},{
																		name: 'Revenue Female',
																		color: '#F59240',
																		type: 'spline',
																		yAxis: 1,
																		data: []
																	},{
																		name: 'Revenue Male',
																		color: '#46AAC4',
																		type: 'spline',
																		yAxis: 1,
																		data: []
																	},{
																		name: 'Total Revenue',
																		color: '#7D5FA0',
																		type: 'spline',
																		yAxis: 1,
																		data: []
																	}]
																});
															}
															
															
															
																}
													// expire localstorage		
													if (nowString > expireString)
													{
														console.log("expire");
														localStorage.removeItem(card_id+"_objLoyaltyAll");
													}
													// expire localstorage		
												}
												else
												{
													jQuery.ajax({
													  type: "POST", // HTTP method POST or GET
													  url: "card_report.php", //Where to make Ajax calls
													  data:"btn_filter_loyalty_card_metrics=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&active="+active+"&opt_filter_days=30",
													  success:function(response){
														
															var obj = jQuery.parseJSON(response);
														
															// start store object in memcache
															/*
															jQuery.ajax({
																  type: "POST", // HTTP method POST or GET
																  url: "process.php", //Where to make Ajax calls
																  //data:"store_object_in_memcached=yes&var=objLoyaltyAll&value="+obj,
																  data:"store_object_in_memcached=yes&var="+card_id+"_objLoyaltyAll&value="+JSON.stringify(obj),
																  async:false,
																  success:function(response){
																		var obj = jQuery.parseJSON(response);
																		if (obj.status=="true") 
																		{
																			
																		}
																  }
															});
															*/
															// end store object in memcache
														
															// start store object in local storage
															
															d = new Date();
															units = 20 // minute
															expireTime = d.getTime() + units*60000;
															var object = {value: obj, timestamp: expireTime};
															localStorage.setItem(card_id+"_objLoyaltyAll", JSON.stringify(object));
													
															// end store object in local storage
															
															if (obj.status=="true") 
															{
															
																
															
																jQuery('#div_card_'+card_id+' .loyalty_card_metrics_filter').css("display","block");
																jQuery("#div_card_"+card_id+" #opt_filter_location_age").empty().append(obj.options_age);															
																jQuery("#div_card_"+card_id+" #opt_filter_location_time").empty().append(obj.options_time);
															
																jQuery('#div_card_'+card_id+' #opt_filter_days_metric').prop('selectedIndex', 0);
																jQuery('#div_card_'+card_id+' #opt_filter_days_revenue').prop('selectedIndex', 0);
																jQuery('#div_card_'+card_id+' #opt_filter_days_age').prop('selectedIndex', 0);
																jQuery('#div_card_'+card_id+' #opt_filter_days_time').prop('selectedIndex', 0);
																
																Highcharts.setOptions({
																	lang: {
																		drillUpText: '<< Back'
																	}
																});
																
																// start drill down report									
														
																var total_cards_activated=parseInt(obj.total_cards_activated);
																var active_male=parseInt(obj.total_male_activated_cards);
																var active_female=parseInt(obj.total_female_activated_cards);
																var active_male_desktop=parseInt(obj.total_male_activated_desktop_cards);
																var active_male_mobile=parseInt(obj.total_male_activated_mobile_cards);
																var active_male_qrcode=parseInt(obj.total_male_activated_qr_cards);
																var active_female_desktop=parseInt(obj.total_female_activated_desktop_cards);
																var active_female_mobile=parseInt(obj.total_female_activated_mobile_cards);
																var active_female_qrcode=parseInt(obj.total_female_activated_qr_cards);
																
																var total_cards_rewarded=parseInt(obj.total_cards_rewarded);
																var reward_male=parseInt(obj.total_male_rewarded_cards);
																var reward_female=parseInt(obj.total_female_rewarded_cards);
																var reward_male_desktop=parseInt(obj.total_male_rewarded_desktop_cards);
																var reward_male_mobile=parseInt(obj.total_male_rewarded_mobile_cards);
																var reward_male_qrcode=parseInt(obj.total_male_rewarded_qr_cards);
																var reward_female_desktop=parseInt(obj.total_female_rewarded_desktop_cards);
																var reward_female_mobile=parseInt(obj.total_female_rewarded_mobile_cards);
																var reward_female_qrcode=parseInt(obj.total_female_rewarded_qr_cards);
																
																var total_cards_deleted=parseInt(obj.total_cards_deleted);
																var delete_male=parseInt(obj.total_male_deleted_cards);
																var delete_female=parseInt(obj.total_female_deleted_cards);	
																var delete_male_desktop=parseInt(obj.total_male_deleted_desktop_cards);
																var delete_male_mobile=parseInt(obj.total_male_deleted_mobile_cards);
																var delete_male_qrcode=parseInt(obj.total_male_deleted_qr_cards);
																var delete_female_desktop=parseInt(obj.total_female_deleted_desktop_cards);
																var delete_female_mobile=parseInt(obj.total_female_deleted_mobile_cards);
																var delete_female_qrcode=parseInt(obj.total_female_deleted_qr_cards);
																
																if(total_cards_activated==0)
																	total_cards_activated_dd=null;
																else
																	total_cards_activated_dd="total_cards_activated";
																	
																if(total_cards_rewarded==0)
																	total_cards_rewarded_dd=null;
																else
																	total_cards_rewarded_dd="total_cards_rewarded";	
																	
																if(total_cards_deleted==0)
																	total_cards_deleted_dd=null;
																else
																	total_cards_deleted_dd="total_cards_deleted";
																	
																if(active_male==0)
																	active_male_dd=null;
																else
																	active_male_dd="active_male";
																
																if(active_female==0)
																	active_female_dd=null;
																else
																	active_female_dd="active_female";		
																	
																		
																// Create the chart
		
		
																loyalty_metric_chart = new Highcharts.Chart({
																	chart: {
																		renderTo: 'card_metrics_drilldown_container_'+card_id,
																		type: 'column',
																		//borderColor: 'rgb(69, 114, 167)',
																		//borderWidth: 1
																		//plotBorderColor: 'rgb(69, 114, 167)',
																		//plotBorderWidth:2
																		
																	},
																	title: {
																		text: 'Card Metrics'
																	},
																	subtitle: {
																		text: 'Click the columns to view detail result'
																	},
																	xAxis: {
																		type: 'category'
																		
																	},
																	yAxis: {
																		title: {
																			text: 'Total Cards'
																		},
																		 gridLineWidth: 0,
																		  lineWidth:1
																	},
																	legend: {
																		enabled: true
																	},
																	credits: {
																		enabled: false
																	},
																	plotOptions: {
																		series: {
																			borderWidth: 0,
																			dataLabels: {
																				enabled: true,
																				format: '{point.y:.1f}'
																			}
																		}
																	},
																	
																	tooltip: {
																		headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																		pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
																	},
																	
																	series: [{
																		name: 'Loyalty Card Metrics',
																		colorByPoint: true,
																		data: [{
																					name: 'Total Cards Activated',
																					y: total_cards_activated,
																					drilldown: total_cards_activated_dd,															
																				},
																				{
																					name: 'Total Cards Rewarded',
																					y: total_cards_rewarded,
																					drilldown: total_cards_rewarded_dd
																				},
																				{
																					name: 'Total Cards Deleted',
																					y: total_cards_deleted,
																					drilldown: total_cards_deleted_dd
																				},
																				]
																	}],
																	drilldown: {
																		drillUpButton: {
																			relativeTo: 'spacingBox',
																			position: {
																				y: 0,
																				x: 0
																			},
																			theme: {
																				fill: '#eaeaea',
																				'stroke-width': 1,
																				stroke: 'silver',
																				r: 0,
																				states: {
																					
																					select: {
																						stroke: '#039',
																						fill: '#bada55'
																					}
																				}
																			}

																		},
																		series: [{
																					id: 'total_cards_activated',
																					name: 'Total Cards Activated',
																					data: [{
																							name: 'Male',
																							y: active_male,
																							drilldown: active_male_dd
																							},
																							{
																							name: 'Female',
																							y: active_female,
																							drilldown: active_female_dd
																							}
																						]
																				},
																				{
																					id: 'total_cards_rewarded',
																					name: 'Total Cards Rewarded',
																					data: [{
																							name: 'Male',
																							y: reward_male,
																							//drilldown: 'reward_male'
																							},
																							{
																							name: 'Female',
																							y: reward_female,
																							//drilldown: 'reward_female'
																							}
																						]
																				},
																				{
																					id: 'total_cards_deleted',
																					name: 'Total Cards Deleted',
																					data: [{
																							name: 'Male',
																							y: delete_male,
																							//drilldown: 'delete_male'
																							},
																							{
																							name: 'Female',
																							y: delete_female,
																							//drilldown: 'delete_female'
																							}
																						]
																				}, 
																				{

																					id: 'active_male',
																					name: 'Total Cards Activated By Male',
																					data: [
																						   ['Mobile Device',active_male_mobile],
																						   ['QR Code Scan',active_male_qrcode]]
																				}, 
																				{

																					id: 'active_female',
																					name: 'Total Cards Activated By Female',
																					data: [
																						   ['Mobile Device',active_female_mobile],
																						   ['QR Code Scan',active_female_qrcode]]
																				}, 
																				{

																					id: 'reward_male',
																					name: 'Total Cards Rewarded By Male',
																					data: [
																						   ['Mobile Device',reward_male_mobile],
																						   ['QR Code Scan',reward_male_qrcode]]
																				}, 
																				{

																					id: 'reward_female',
																					name: 'Total Cards Rewarded By Female',
																					data: [
																						   ['Mobile Device',reward_female_mobile],
																						   ['QR Code Scan',reward_female_qrcode]]
																				}, 
																				{

																					id: 'delete_male',
																					name: 'Total Cards Deleted By Male',
																					data: [
																						   ['Mobile Device',delete_male_mobile],
																						   ['QR Code Scan',delete_male_qrcode]]
																				}, 
																				{

																					id: 'delete_female',
																					name: 'Total Cards Deleted By Female',
																					data: [
																						   ['Mobile Device',delete_female_mobile],
																						   ['QR Code Scan',delete_female_qrcode]]
																				}]
																	}
																});   
																
																// end drill down report
																
																// start revenue report
														
																var categories = ['Total Revenue','Average Revenue per visit','Average  Revenue per location'];
																
																var total_revenue=parseInt(obj.total_revenue);
																var total_male_revenue=parseInt(obj.total_male_revenue);
																var total_female_revenue=parseInt(obj.total_female_revenue);
																
																var total_average_revenue_per_visit=parseInt(obj.total_average_revenue_per_visit);
																var total_average_male_revenue_per_visit=parseInt(obj.total_average_male_revenue_per_visit);
																var total_average_female_revenue_per_visit=parseInt(obj.total_average_female_revenue_per_visit);
				
																var total_average_revenue_per_location=parseInt(obj.total_average_revenue_per_location);
																var total_average_male_revenue_per_location=parseInt(obj.total_average_male_revenue_per_location);
																var total_average_female_revenue_per_location=parseInt(obj.total_average_female_revenue_per_location);
																
																if(total_revenue==0)
																	total_revenue_dd=null;
																else
																	total_revenue_dd="total_revenue";
																
																if(total_average_revenue_per_visit==0)
																	average_revenue_per_visit_dd=null;
																else
																	average_revenue_per_visit_dd="average_revenue_per_visit";	
																	
																if(total_average_revenue_per_location==0)
																	average_revenue_per_location_dd=null;
																else
																	average_revenue_per_location_dd="average_revenue_per_location";	
	
																//jQuery('#card_revenue_container').highcharts({
																loyalty_revenue_chart = new Highcharts.Chart({
																		chart: {
																			renderTo: 'card_revenue_container_'+card_id,
																			type: 'column',
																			//borderColor: 'rgb(69, 114, 167)',
																			//borderWidth: 1,
																			//plotBorderColor: 'rgb(69, 114, 167)',
																			//plotBorderWidth:2
																			
																		},
																		title: {
																			text: 'Revenue Summary'
																		},
																		subtitle: {
																			text: 'Click the columns to view detail result'
																		},
																		xAxis: {
																			type: 'category',
																			
																		},
																		yAxis: {
																			title: {
																				text: 'Revenue'
																			},
																			 gridLineWidth: 0,
																			lineWidth:1
																		},
																		legend: {
																			enabled: true
																		},
																		credits: {
																			enabled: false
																		},
																		plotOptions: {
																			series: {
																				borderWidth: 0,
																				dataLabels: {
																					enabled: true,
																					format: '{point.y:.1f}'
																				}
																			}
																		},
																		
																		tooltip: {
																			headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																			pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
																		},
																		
																		series: [{
																			name: 'Revenue Metrics',
																			colorByPoint: true,
																			data: [{
																						name: 'Total Revenue',
																						y: total_revenue,
																						drilldown: total_revenue_dd
																					},
																					{
																						name: 'Average Revenue per visit',
																						y: total_average_revenue_per_visit,
																						drilldown: average_revenue_per_visit_dd
																					},
																					{
																						name: 'Average  Revenue per location',
																						y: total_average_revenue_per_location,
																						drilldown: average_revenue_per_location_dd
																					},
																					]
																		}],
																		drilldown: {
																			drillUpButton: {
																				relativeTo: 'spacingBox',
																				position: {
																					y: 0,
																					x: 0
																				},
																				theme: {
																					fill: '#eaeaea',
																					'stroke-width': 1,
																					stroke: 'silver',
																					r: 0,
																					states: {
																						select: {
																							stroke: '#039',
																							fill: '#bada55'
																						}
																					}
																				}

																			},
																			series: [{
																						id: 'total_revenue',
																						name: 'Total Revenue',
																						data: [{
																								name: 'Male',
																								y: total_male_revenue
																								},
																								{
																								name: 'Female',
																								y: total_female_revenue
																								}
																							]
																					},
																					{
																						id: 'average_revenue_per_visit',
																						name: 'Average Revenue Per Visit',
																						data: [{
																								name: 'Male',
																								y: total_average_male_revenue_per_visit
																								},
																								{
																								name: 'Female',
																								y: total_average_female_revenue_per_visit
																								}
																							]
																					},
																					{
																						id: 'average_revenue_per_location',
																						name: 'Average Revenue Per Location',
																						data: [{
																								name: 'Male',
																								y: total_average_male_revenue_per_location
																								},
																								{
																								name: 'Female',
																								y: total_average_female_revenue_per_location
																								}
																							]
																					}]
																		}
																	});
															
															// end revenue report	
																				
																// start for customer visit by age
														
														var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
														
														var f_17 = parseInt(obj.f_17);
														var f_18 = parseInt(obj.f_18);
														var f_25 = parseInt(obj.f_25);
														var f_45 = parseInt(obj.f_45);
														var f_55 = parseInt(obj.f_55);
														var f_65 = parseInt(obj.f_65);
														
														var female_visit = [f_17,f_18,f_25,f_45,f_55,f_65];
														
														var m_17 = parseInt(obj.m_17);
														var m_18 = parseInt(obj.m_18);
														var m_25 = parseInt(obj.m_25);
														var m_45 = parseInt(obj.m_45);
														var m_55 = parseInt(obj.m_55);
														var m_65 = parseInt(obj.m_65);
														
														var male_visit = [m_17,m_18,m_25,m_45,m_55,m_65];
														
														var rf_17 = parseInt(obj.rf_17);
														var rf_18 = parseInt(obj.rf_18);
														var rf_25 = parseInt(obj.rf_25);
														var rf_45 = parseInt(obj.rf_45);
														var rf_55 = parseInt(obj.rf_55);
														var rf_65 = parseInt(obj.rf_65);
														
														var revenue_female = [rf_17,rf_18,rf_25,rf_45,rf_55,rf_65];
														
														var rm_17 = parseInt(obj.rm_17);
														var rm_18 = parseInt(obj.rm_18);
														var rm_25 = parseInt(obj.rm_25);
														var rm_45 = parseInt(obj.rm_45);
														var rm_55 = parseInt(obj.rm_55);
														var rm_65 = parseInt(obj.rm_65);
														
														var revenue_male = [rm_17,rm_18,rm_25,rm_45,rm_55,rm_65];
														
														var rt_17 = parseInt(obj.rt_17);
														var rt_18 = parseInt(obj.rt_18);
														var rt_25 = parseInt(obj.rt_25);
														var rt_45 = parseInt(obj.rt_45);
														var rt_55 = parseInt(obj.rt_55);
														var rt_65 = parseInt(obj.rt_65);
														
														var revenue_total = [rt_17,rt_18,rt_25,rt_45,rt_55,rt_65];
														
														//jQuery('#card_visit_by_age_container').highcharts({
														loyalty_visitbyage_chart = new Highcharts.Chart({
															chart: {
																renderTo: 'card_visit_by_age_container_'+card_id
																//plotBorderColor: 'rgb(69, 114, 167)',
																//plotBorderWidth:2
															},
															title: {
																text: 'Customer Visit & Revenue Metrics By Age',
																x: -20 //center
															},
															subtitle: {
																text: '',
																x: -20
															},
															credits: {
																enabled: false
															},
															xAxis:{
																title: {
																	text: 'Customer Age'
																},
																categories: categories
															},
															yAxis: [{ // Primary yAxis
																   title: {
																	   text: 'Customer Visits'
																   },
																   min: 0,
																   gridLineWidth: 0,
																   lineWidth:1
															  }, { // Secondary yAxis
																title: {
																	text: 'Revenue'
																},
																  min: 0,
																  gridLineWidth: 0,
																  lineWidth:1,
																opposite: true
															}],       
															legend: {
																layout: 'vertical',
																align: 'right',
																verticalAlign: 'middle',
																borderWidth: 0
															},
															/*
															plotOptions: {
																series: {
																	stacking: 'normal'
																}
															},
															*/
															series: [{
																name: 'Female Visit',
																color: '#9BBB59',
																type: 'column',
																yAxis: 0,
																data: female_visit
															},{
																name: 'Male Visit',
																color: '#C0504D',
																type: 'column',
																yAxis: 0,
																data: male_visit
															},{
																name: 'Revenue Female',
																color: '#F59240',
																type: 'spline',
																yAxis: 1,
																data: revenue_female
															},{
																name: 'Revenue Male',
																color: '#46AAC4',
																type: 'spline',
																yAxis: 1,
																data: revenue_male
															},{
																name: 'Total Revenue',
																color: '#7D5FA0',
																type: 'spline',
																yAxis: 1,
																data: revenue_total
															}]
														});
														
														// end for customer visit by age
														
														// start for customer visit by time
														
														var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
														
														var f_1 = parseInt(obj.f_1);
														var f_2 = parseInt(obj.f_2);
														var f_3 = parseInt(obj.f_3);
														var f_4 = parseInt(obj.f_4);
														
														var female_visit = [f_1,f_2,f_3,f_4];
														
														var m_1 = parseInt(obj.m_1);
														var m_2 = parseInt(obj.m_2);
														var m_3 = parseInt(obj.m_3);
														var m_4 = parseInt(obj.m_4);
														
														var male_visit = [m_1,m_2,m_3,m_4];
														
														var rf_1 = parseInt(obj.rf_1);
														var rf_2 = parseInt(obj.rf_2);
														var rf_3 = parseInt(obj.rf_3);
														var rf_4 = parseInt(obj.rf_4);
														
														var revenue_female = [rf_1,rf_2,rf_3,rf_4];
														
														var rm_1 = parseInt(obj.rm_1);
														var rm_2 = parseInt(obj.rm_2);
														var rm_3 = parseInt(obj.rm_3);
														var rm_4 = parseInt(obj.rm_4);
														
														var revenue_male = [rm_1,rm_2,rm_3,rm_4];
														
														var rt_1 = parseInt(obj.rt_1);
														var rt_2 = parseInt(obj.rt_2);
														var rt_3 = parseInt(obj.rt_3);
														var rt_4 = parseInt(obj.rt_4);
														
														var revenue_total = [rt_1,rt_2,rt_3,rt_4];
														
														//jQuery('#card_visit_by_time_container').highcharts({
														loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_time_container_'+card_id
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														
														// end for customer visit by time
														
														if(total_cards_activated==0 && total_cards_rewarded==0 && total_cards_deleted==0)												
														{
															//jQuery('#div_card_'+card_id+' .metricchart .nodata_loyalty_metrics').css("display","block");
															//jQuery('#card_metrics_drilldown_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .metricchart .loyalty_card_metrics_filter').css("display","none");
															
															loyalty_metric_chart = new Highcharts.Chart({
																	chart: {
																		renderTo: 'card_metrics_drilldown_container_'+card_id,
																		type: 'column',
																		//borderColor: 'rgb(69, 114, 167)',
																		//borderWidth: 1
																		//plotBorderColor: 'rgb(69, 114, 167)',
																		//plotBorderWidth:2
																		
																	},
																	title: {
																		text: 'Card Metrics'
																	},
																	subtitle: {
																		text: 'Click the columns to view detail result'
																	},
																	xAxis: {
																		type: 'category'
																		
																	},
																	yAxis: {
																		title: {
																			text: 'Total Cards'
																		},
																		 gridLineWidth: 0,
																		  lineWidth:1
																	},
																	legend: {
																		enabled: true
																	},
																	credits: {
																		enabled: false
																	},															
																	series: [{
																		name: 'Loyalty Card Metrics',
																		colorByPoint: true,
																		data: []
																	}]
																}); 
														}
														if(total_revenue==0 && total_average_revenue_per_visit==0 && total_average_revenue_per_location==0)												
														{
															//jQuery('#div_card_'+card_id+' .revenuechart .nodata_loyalty_revenue').css("display","block");
															//jQuery('#card_revenue_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .revenuechart .loyalty_card_metrics_filter').css("display","none");
															
															loyalty_revenue_chart = new Highcharts.Chart({
																		chart: {
																			renderTo: 'card_revenue_container_'+card_id,
																			type: 'column',
																			//borderColor: 'rgb(69, 114, 167)',
																			//borderWidth: 1,
																			//plotBorderColor: 'rgb(69, 114, 167)',
																			//plotBorderWidth:2
																			
																		},
																		title: {
																			text: 'Revenue Summary'
																		},
																		subtitle: {
																			text: 'Click the columns to view detail result'
																		},
																		xAxis: {
																			type: 'category',
																			
																		},
																		yAxis: {
																			title: {
																				text: 'Revenue'
																			},
																			 gridLineWidth: 0,
																			lineWidth:1
																		},
																		legend: {
																			enabled: true
																		},
																		credits: {
																			enabled: false
																		},
																		series: [{
																			name: 'Revenue Metrics',
																			colorByPoint: true,
																			data: []
																	}]
																});
																		
														}
														if(f_17==0 && f_18==0 && f_25==0 && f_45==0 && f_55==0 && f_65==0 && 
															m_17==0 && m_18==0 && m_25==0 && m_45==0 && m_55==0 && m_65==0 && 
															rf_17==0 && rf_18==0 && rf_25==0 && rf_45==0 && rf_55==0 && rf_65==0 && 
															rm_17==0 && rm_18==0 && rm_25==0 && rm_45==0 && rm_55==0 && rm_65==0 && 
															rt_17==0 && rt_18==0 && rt_25==0 && rt_45==0 && rt_55==0 && rt_65==0 )												
														{
															//jQuery('#div_card_'+card_id+' .agechart .nodata_loyalty_age').css("display","block");
															//jQuery('#card_visit_by_age_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .agechart .loyalty_card_metrics_filter').css("display","none");
															
																loyalty_visitbyage_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_age_container_'+card_id
																	//plotBorderColor: 'rgb(69, 114, 167)',
																	//plotBorderWidth:2
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Age',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Customer Age'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},																
																series: [{
																	name: 'Female Visit',
																	color: '#9BBB59',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Male Visit',
																	color: '#C0504D',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																}]
															});
														}
														if(f_1==0 && f_2==0 && f_3==0 && f_4==0 && 
															m_1==0 && m_2==0 && m_3==0 && m_4==0 && 
															rf_1==0 && rf_2==0 && rf_3==0 && rf_4==0 && 
															rt_1==0 && rt_2==0 && rt_3==0 && rt_4==0 )												
														{
															//jQuery('#div_card_'+card_id+' .timechart .nodata_loyalty_time').css("display","block");
															//jQuery('#card_visit_by_time_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .timechart .loyalty_card_metrics_filter').css("display","none");
															
															loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_time_container_'+card_id
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},																
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																}]
															});
														}
														
														
														
															}
														}
												});
									
												}	
												
												bind_loyaltycard_report_tab_event();
											}
									}
								}
							});
						});
						
						
						
						jQuery(".getanalytics1").live("click",function(){
							
							jQuery("#sidemenu li a").each(function () {
								jQuery(this).removeClass("current");
							});
							jQuery("li.loyaltycard_report a").addClass("current");
							var cls = jQuery(this).parent().attr("class");
							jQuery(".tabs").each(function () {
								jQuery(this).css("display", "none");
							});

							jQuery("#loyaltycard_report").css("display", "block");
								
							jQuery(".loyaltycard_report").css("display", "block");
							var str_text = jQuery(this).text();
							jQuery(".loyaltycard_report a").text(str_text);
							jQuery("#close_span1").text("");
							jQuery("#close_span1").append('<span id="close_card" title="Close" >X</span>');
														
								
								jQuery(".nodata_loyalty_metrics").css("display","none");
								jQuery(".nodata_loyalty_revenue").css("display","none");
								jQuery(".nodata_loyalty_age").css("display","none");
								jQuery(".nodata_loyalty_time").css("display","none");
								
								jQuery('#card_metrics_drilldown_container').css("display","none");
								jQuery('#card_revenue_container').css("display","none");
								jQuery('#card_visit_by_age_container').css("display","none");
								jQuery('#card_visit_by_time_container').css("display","none");
														
								jQuery(".loader_loyalty_metrics").css("display","block");
								jQuery(".loader_loyalty_revenue").css("display","block");
								jQuery(".loader_loyalty_age").css("display","block");
								jQuery(".loader_loyalty_time").css("display","block");
								
								
								var card_id = jQuery(this).attr('cardid');
								jQuery("#hdncardid").val(card_id);
								var active = jQuery(this).attr('active');
								var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
								//alert(card_id);			
								jQuery.ajax({
									type:"POST",
									url:'process.php',
									data :'loginornot=true',
									async:false,
									success:function(msg)
									{
										var obj = jQuery.parseJSON(msg);
										if (obj.status=="false")     
										{
											window.location.href=obj.link;
										}
										else
										{
											var obj_exist_in_cache=0;
											var cached_loyalty_object;
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "process.php", //Where to make Ajax calls
												  data:"check_object_in_memcached=yes&var="+card_id+"_objLoyaltyAll",
												  async:false,
												  success:function(response){
														var obj = jQuery.parseJSON(response);
														if (obj.status=="true") 
														{
															obj_exist_in_cache =1;
															//obj_exist_in_cache =0;
															cached_loyalty_object = obj.result;
														}
												  }
											});
											
											if (obj_exist_in_cache==1)     
											{
												//console.log(cached_loyalty_object);
												
												var obj = jQuery.parseJSON(cached_loyalty_object);
												//console.log(obj.status);
												if (obj.status=="true") 
												{
													jQuery('#card_metrics_drilldown_container').css("display","block");
														jQuery('#card_revenue_container').css("display","block");
														jQuery('#card_visit_by_age_container').css("display","block");
														jQuery('#card_visit_by_time_container').css("display","block");
														
														jQuery('.loyalty_card_metrics_filter').css("display","block");
														jQuery("#opt_filter_location_age").empty().append(obj.options_age);
														jQuery("#opt_filter_location_time").empty().append(obj.options_time);
													
														jQuery('#opt_filter_days_metric').prop('selectedIndex', 0);
														jQuery('#opt_filter_days_revenue').prop('selectedIndex', 0);
														jQuery('#opt_filter_days_age').prop('selectedIndex', 0);
														jQuery('#opt_filter_days_time').prop('selectedIndex', 0);
														
														if(active==1)
														{
															jQuery(".loyalty_card_metrics_filter").css("display","block");
															
														}
														else
														{
															jQuery(".loyalty_card_metrics_filter").css("display","none");
														}
														
														Highcharts.setOptions({
															lang: {
																drillUpText: '<< Back'
															}
														});
    
														// start drill down report
														
														
														var total_cards_activated=parseInt(obj.total_cards_activated);
														var active_male=parseInt(obj.total_male_activated_cards);
														var active_female=parseInt(obj.total_female_activated_cards);
														var active_male_desktop=parseInt(obj.total_male_activated_desktop_cards);
														var active_male_mobile=parseInt(obj.total_male_activated_mobile_cards);
														var active_male_qrcode=parseInt(obj.total_male_activated_qr_cards);
														var active_female_desktop=parseInt(obj.total_female_activated_desktop_cards);
														var active_female_mobile=parseInt(obj.total_female_activated_mobile_cards);
														var active_female_qrcode=parseInt(obj.total_female_activated_qr_cards);
														
														var total_cards_rewarded=parseInt(obj.total_cards_rewarded);
														var reward_male=parseInt(obj.total_male_rewarded_cards);
														var reward_female=parseInt(obj.total_female_rewarded_cards);
														var reward_male_desktop=parseInt(obj.total_male_rewarded_desktop_cards);
														var reward_male_mobile=parseInt(obj.total_male_rewarded_mobile_cards);
														var reward_male_qrcode=parseInt(obj.total_male_rewarded_qr_cards);
														var reward_female_desktop=parseInt(obj.total_female_rewarded_desktop_cards);
														var reward_female_mobile=parseInt(obj.total_female_rewarded_mobile_cards);
														var reward_female_qrcode=parseInt(obj.total_female_rewarded_qr_cards);
														
														var total_cards_deleted=parseInt(obj.total_cards_deleted);
														var delete_male=parseInt(obj.total_male_deleted_cards);
														var delete_female=parseInt(obj.total_female_deleted_cards);	
														var delete_male_desktop=parseInt(obj.total_male_deleted_desktop_cards);
														var delete_male_mobile=parseInt(obj.total_male_deleted_mobile_cards);
														var delete_male_qrcode=parseInt(obj.total_male_deleted_qr_cards);
														var delete_female_desktop=parseInt(obj.total_female_deleted_desktop_cards);
														var delete_female_mobile=parseInt(obj.total_female_deleted_mobile_cards);
														var delete_female_qrcode=parseInt(obj.total_female_deleted_qr_cards);
														
														
														try
														{
														// Create the chart
														
														//jQuery('#card_metrics_drilldown_container').highcharts({
														loyalty_metric_chart = new Highcharts.Chart({
															chart: {
																renderTo: 'card_metrics_drilldown_container',
																type: 'column',
																//borderColor: 'rgb(69, 114, 167)',
																//borderWidth: 1
																//plotBorderColor: 'rgb(69, 114, 167)',
																//plotBorderWidth:2
																
															},
															title: {
																text: 'Card Metrics'
															},
															subtitle: {
																text: 'Click the columns to view detail result'
															},
															xAxis: {
																type: 'category'
																
															},
															yAxis: {
																title: {
																	text: 'Total Cards'
																},
																 gridLineWidth: 0,
																  lineWidth:1
															},
															legend: {
																enabled: true
															},
															credits: {
																enabled: false
															},
															plotOptions: {
																series: {
																	borderWidth: 0,
																	dataLabels: {
																		enabled: true,
																		format: '{point.y:.1f}'
																	}
																}
															},
															
															tooltip: {
																headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
															},
															
															series: [{
																name: 'Loyalty Card Metrics',
																colorByPoint: true,
																data: [{
																			name: 'Total Cards Activated',
																			y: total_cards_activated,
																			drilldown: 'total_cards_activated'
																		},
																		{
																			name: 'Total Cards Rewarded',
																			y: total_cards_rewarded,
																			drilldown: 'total_cards_rewarded'
																		},
																		{
																			name: 'Total Cards Deleted',
																			y: total_cards_deleted,
																			drilldown: 'total_cards_deleted'
																		},
																		]
															}],
															drilldown: {
																drillUpButton: {
																	relativeTo: 'spacingBox',
																	position: {
																		y: 0,
																		x: 0
																	},
																	theme: {
																		fill: '#eaeaea',
																		'stroke-width': 1,
																		stroke: 'silver',
																		r: 0,
																		states: {
																			
																			select: {
																				stroke: '#039',
																				fill: '#bada55'
																			}
																		}
																	}

																},
																series: [{
																			id: 'total_cards_activated',
																			name: 'Total Cards Activated',
																			data: [{
																					name: 'Male',
																					y: active_male,
																					drilldown: 'active_male'
																					},
																					{
																					name: 'Female',
																					y: active_female,
																					drilldown: 'active_female'
																					}
																				]
																		},
																		{
																			id: 'total_cards_rewarded',
																			name: 'Total Cards Rewarded',
																			data: [{
																					name: 'Male',
																					y: reward_male,
																					drilldown: 'reward_male'
																					},
																					{
																					name: 'Female',
																					y: reward_female,
																					drilldown: 'reward_female'
																					}
																				]
																		},
																		{
																			id: 'total_cards_deleted',
																			name: 'Total Cards Deleted',
																			data: [{
																					name: 'Male',
																					y: delete_male,
																					drilldown: 'delete_male'
																					},
																					{
																					name: 'Female',
																					y: delete_female,
																					drilldown: 'delete_female'
																					}
																				]
																		}, 
																		{

																			id: 'active_male',
																			name: 'Total Cards Activated By Male',
																			data: [['Desktop',active_male_desktop],
																				   ['Mobile Device',active_male_mobile],
																				   ['QR Code Scan',active_male_qrcode]]
																		}, 
																		{

																			id: 'active_female',
																			name: 'Total Cards Activated By Female',
																			data: [['Desktop',active_female_desktop],
																				   ['Mobile Device',active_female_mobile],
																				   ['QR Code Scan',active_female_qrcode]]
																		}, 
																		{

																			id: 'reward_male',
																			name: 'Total Cards Rewarded By Male',
																			data: [['Desktop',reward_male_desktop],
																				   ['Mobile Device',reward_male_mobile],
																				   ['QR Code Scan',reward_male_qrcode]]
																		}, 
																		{

																			id: 'reward_female',
																			name: 'Total Cards Rewarded By Female',
																			data: [['Desktop',reward_female_desktop],
																				   ['Mobile Device',reward_female_mobile],
																				   ['QR Code Scan',reward_female_qrcode]]
																		}, 
																		{

																			id: 'delete_male',
																			name: 'Total Cards Deleted By Male',
																			data: [['Desktop',delete_male_desktop],
																				   ['Mobile Device',delete_male_mobile],
																				   ['QR Code Scan',delete_male_qrcode]]
																		}, 
																		{

																			id: 'delete_female',
																			name: 'Total Cards Deleted By Female',
																			data: [['Desktop',delete_female_desktop],
																				   ['Mobile Device',delete_female_mobile],
																				   ['QR Code Scan',delete_female_qrcode]]
																		}]
															}
														});   
														
														// end drill down report
														
														// start revenue report
														
															var categories = ['Total Revenue','Average Revenue per visit','Average  Revenue per location'];
															
															var total_revenue=parseInt(obj.total_revenue);
															var total_male_revenue=parseInt(obj.total_male_revenue);
															var total_female_revenue=parseInt(obj.total_female_revenue);
															
															var total_average_revenue_per_visit=parseInt(obj.total_average_revenue_per_visit);
															var total_average_male_revenue_per_visit=parseInt(obj.total_average_male_revenue_per_visit);
															var total_average_female_revenue_per_visit=parseInt(obj.total_average_female_revenue_per_visit);
			
															var total_average_revenue_per_location=parseInt(obj.total_average_revenue_per_location);
															var total_average_male_revenue_per_location=parseInt(obj.total_average_male_revenue_per_location);
															var total_average_female_revenue_per_location=parseInt(obj.total_average_female_revenue_per_location);
															
															//jQuery('#card_revenue_container').highcharts({
															loyalty_revenue_chart = new Highcharts.Chart({
																	chart: {
																		renderTo: 'card_revenue_container',
																		type: 'column',
																		//borderColor: 'rgb(69, 114, 167)',
																		//borderWidth: 1,
																		//plotBorderColor: 'rgb(69, 114, 167)',
																		//plotBorderWidth:2
																		
																	},
																	title: {
																		text: 'Revenue Summary'
																	},
																	subtitle: {
																		text: 'Click the columns to view detail result'
																	},
																	xAxis: {
																		type: 'category',
																		
																	},
																	yAxis: {
																		title: {
																			text: 'Revenue'
																		},
																		 gridLineWidth: 0,
																		lineWidth:1
																	},
																	legend: {
																		enabled: true
																	},
																	credits: {
																		enabled: false
																	},
																	plotOptions: {
																		series: {
																			borderWidth: 0,
																			dataLabels: {
																				enabled: true,
																				format: '{point.y:.1f}'
																			}
																		}
																	},
																	
																	tooltip: {
																		headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																		pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
																	},
																	
																	series: [{
																		name: 'Revenue Metrics',
																		colorByPoint: true,
																		data: [{
																					name: 'Total Revenue',
																					y: total_revenue,
																					drilldown: 'total_revenue'
																				},
																				{
																					name: 'Average Revenue per visit',
																					y: total_average_revenue_per_visit,
																					drilldown: 'average_revenue_per_visit'
																				},
																				{
																					name: 'Average  Revenue per location',
																					y: total_average_revenue_per_location,
																					drilldown: 'average_revenue_per_location'
																				},
																				]
																	}],
																	drilldown: {
																		drillUpButton: {
																			relativeTo: 'spacingBox',
																			position: {
																				y: 0,
																				x: 0
																			},
																			theme: {
																				fill: '#eaeaea',
																				'stroke-width': 1,
																				stroke: 'silver',
																				r: 0,
																				states: {
																					select: {
																						stroke: '#039',
																						fill: '#bada55'
																					}
																				}
																			}

																		},
																		series: [{
																					id: 'total_revenue',
																					name: 'Total Revenue',
																					data: [{
																							name: 'Male',
																							y: total_male_revenue
																							},
																							{
																							name: 'Female',
																							y: total_female_revenue
																							}
																						]
																				},
																				{
																					id: 'average_revenue_per_visit',
																					name: 'Average Revenue Per Visit',
																					data: [{
																							name: 'Male',
																							y: total_average_male_revenue_per_visit
																							},
																							{
																							name: 'Female',
																							y: total_average_female_revenue_per_visit
																							}
																						]
																				},
																				{
																					id: 'average_revenue_per_location',
																					name: 'Average Revenue Per Location',
																					data: [{
																							name: 'Male',
																							y: total_average_male_revenue_per_location
																							},
																							{
																							name: 'Female',
																							y: total_average_female_revenue_per_location
																							}
																						]
																				}]
																	}
																});
														
														// end revenue report
														
														// start for customer visit by age
														
														var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
														
														var f_17 = parseInt(obj.f_17);
														var f_18 = parseInt(obj.f_18);
														var f_25 = parseInt(obj.f_25);
														var f_45 = parseInt(obj.f_45);
														var f_55 = parseInt(obj.f_55);
														var f_65 = parseInt(obj.f_65);
														
														var female_visit = [f_17,f_18,f_25,f_45,f_55,f_65];
														
														var m_17 = parseInt(obj.m_17);
														var m_18 = parseInt(obj.m_18);
														var m_25 = parseInt(obj.m_25);
														var m_45 = parseInt(obj.m_45);
														var m_55 = parseInt(obj.m_55);
														var m_65 = parseInt(obj.m_65);
														
														var male_visit = [m_17,m_18,m_25,m_45,m_55,m_65];
														
														var rf_17 = parseInt(obj.rf_17);
														var rf_18 = parseInt(obj.rf_18);
														var rf_25 = parseInt(obj.rf_25);
														var rf_45 = parseInt(obj.rf_45);
														var rf_55 = parseInt(obj.rf_55);
														var rf_65 = parseInt(obj.rf_65);
														
														var revenue_female = [rf_17,rf_18,rf_25,rf_45,rf_55,rf_65];
														
														var rm_17 = parseInt(obj.rm_17);
														var rm_18 = parseInt(obj.rm_18);
														var rm_25 = parseInt(obj.rm_25);
														var rm_45 = parseInt(obj.rm_45);
														var rm_55 = parseInt(obj.rm_55);
														var rm_65 = parseInt(obj.rm_65);
														
														var revenue_male = [rm_17,rm_18,rm_25,rm_45,rm_55,rm_65];
														
														var rt_17 = parseInt(obj.rt_17);
														var rt_18 = parseInt(obj.rt_18);
														var rt_25 = parseInt(obj.rt_25);
														var rt_45 = parseInt(obj.rt_45);
														var rt_55 = parseInt(obj.rt_55);
														var rt_65 = parseInt(obj.rt_65);
														
														var revenue_total = [rt_17,rt_18,rt_25,rt_45,rt_55,rt_65];
														
														//jQuery('#card_visit_by_age_container').highcharts({
														loyalty_visitbyage_chart = new Highcharts.Chart({
															chart: {
																renderTo: 'card_visit_by_age_container',
																//plotBorderColor: 'rgb(69, 114, 167)',
																//plotBorderWidth:2
															},
															title: {
																text: 'Customer Visit & Revenue Metrics By Age',
																x: -20 //center
															},
															subtitle: {
																text: '',
																x: -20
															},
															credits: {
																enabled: false
															},
															xAxis:{
																title: {
																	text: 'Customer Age'
																},
																categories: categories
															},
															yAxis: [{ // Primary yAxis
																   title: {
																	   text: 'Customer Visits'
																   },
																   min: 0,
																   gridLineWidth: 0,
																   lineWidth:1
															  }, { // Secondary yAxis
																title: {
																	text: 'Revenue'
																},
																  min: 0,
																  gridLineWidth: 0,
																  lineWidth:1,
																opposite: true
															}],       
															legend: {
																layout: 'vertical',
																align: 'right',
																verticalAlign: 'middle',
																borderWidth: 0
															},
															/*
															plotOptions: {
																series: {
																	stacking: 'normal'
																}
															},
															*/
															series: [{
																name: 'Female Visit',
																color: '#9BBB59',
																type: 'column',
																yAxis: 0,
																data: female_visit
															},{
																name: 'Male Visit',
																color: '#C0504D',
																type: 'column',
																yAxis: 0,
																data: male_visit
															},{
																name: 'Revenue Female',
																color: '#F59240',
																type: 'spline',
																yAxis: 1,
																data: revenue_female
															},{
																name: 'Revenue Male',
																color: '#46AAC4',
																type: 'spline',
																yAxis: 1,
																data: revenue_male
															},{
																name: 'Total Revenue',
																color: '#7D5FA0',
																type: 'spline',
																yAxis: 1,
																data: revenue_total
															}]
														});
														
														// end for customer visit by age
														
														// start for customer visit by time
														
														var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
														
														var f_1 = parseInt(obj.f_1);
														var f_2 = parseInt(obj.f_2);
														var f_3 = parseInt(obj.f_3);
														var f_4 = parseInt(obj.f_4);
														
														var female_visit = [f_1,f_2,f_3,f_4];
														
														var m_1 = parseInt(obj.m_1);
														var m_2 = parseInt(obj.m_2);
														var m_3 = parseInt(obj.m_3);
														var m_4 = parseInt(obj.m_4);
														
														var male_visit = [m_1,m_2,m_3,m_4];
														
														var rf_1 = parseInt(obj.rf_1);
														var rf_2 = parseInt(obj.rf_2);
														var rf_3 = parseInt(obj.rf_3);
														var rf_4 = parseInt(obj.rf_4);
														
														var revenue_female = [rf_1,rf_2,rf_3,rf_4];
														
														var rm_1 = parseInt(obj.rm_1);
														var rm_2 = parseInt(obj.rm_2);
														var rm_3 = parseInt(obj.rm_3);
														var rm_4 = parseInt(obj.rm_4);
														
														var revenue_male = [rm_1,rm_2,rm_3,rm_4];
														
														var rt_1 = parseInt(obj.rt_1);
														var rt_2 = parseInt(obj.rt_2);
														var rt_3 = parseInt(obj.rt_3);
														var rt_4 = parseInt(obj.rt_4);
														
														var revenue_total = [rt_1,rt_2,rt_3,rt_4];
														
														//jQuery('#card_visit_by_time_container').highcharts({
														loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_time_container'
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														
														// end for customer visit by time
														
														}
														catch(e)
														{
															console.log(e);
														}
														
														jQuery(".loader_loyalty_metrics").css("display","none");
														jQuery(".loader_loyalty_revenue").css("display","none");
														jQuery(".loader_loyalty_age").css("display","none");
														jQuery(".loader_loyalty_time").css("display","none");
												}
												else
												{
													jQuery('.loyalty_card_metrics_filter').css("display","block");
													
													jQuery("#opt_filter_location_age").empty().append(obj.options_age);
													jQuery("#opt_filter_location_time").empty().append(obj.options_time);	
													
														//alert(obj.message);
														/*
														jQuery('#card_metrics_drilldown_container').html(obj.message);
														jQuery('#card_revenue_container').html(obj.message);
														jQuery('#card_visit_by_age_container').html(obj.message);
														jQuery('#card_visit_by_time_container').html(obj.message);
														*/
														jQuery(".loader_loyalty_metrics").css("display","none");
														jQuery(".loader_loyalty_revenue").css("display","none");
														jQuery(".loader_loyalty_age").css("display","none");
														jQuery(".loader_loyalty_time").css("display","none");
														
														jQuery(".nodata_loyalty_metrics").css("display","block");
														jQuery(".nodata_loyalty_revenue").css("display","block");
														jQuery(".nodata_loyalty_age").css("display","block");
														jQuery(".nodata_loyalty_time").css("display","block");
												}
											 
											}
											else
											{
												
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_card_metrics=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&active="+active+"&opt_filter_days=30",
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													
													jQuery.ajax({
														  type: "POST", // HTTP method POST or GET
														  url: "process.php", //Where to make Ajax calls
														  //data:"store_object_in_memcached=yes&var=objLoyaltyAll&value="+obj,
														  data:"store_object_in_memcached=yes&var="+card_id+"_objLoyaltyAll&value="+JSON.stringify(obj),
														  async:false,
														  success:function(response){
																var obj = jQuery.parseJSON(response);
																if (obj.status=="true") 
																{
																	
																}
														  }
													});
													
													if (obj.status=="true") 
													{
														jQuery('#card_metrics_drilldown_container').css("display","block");
														jQuery('#card_revenue_container').css("display","block");
														jQuery('#card_visit_by_age_container').css("display","block");
														jQuery('#card_visit_by_time_container').css("display","block");
														
														jQuery('.loyalty_card_metrics_filter').css("display","block");
														jQuery("#opt_filter_location_age").empty().append(obj.options_age);
														jQuery("#opt_filter_location_time").empty().append(obj.options_time);
													
														jQuery('#opt_filter_days_metric').prop('selectedIndex', 0);
														jQuery('#opt_filter_days_revenue').prop('selectedIndex', 0);
														jQuery('#opt_filter_days_age').prop('selectedIndex', 0);
														jQuery('#opt_filter_days_time').prop('selectedIndex', 0);
														
														if(active==1)
														{
															jQuery(".loyalty_card_metrics_filter").css("display","block");
															
														}
														else
														{
															jQuery(".loyalty_card_metrics_filter").css("display","none");
														}
														
														Highcharts.setOptions({
															lang: {
																drillUpText: '<< Back'
															}
														});
    
														// start drill down report
														
														
														var total_cards_activated=parseInt(obj.total_cards_activated);
														var active_male=parseInt(obj.total_male_activated_cards);
														var active_female=parseInt(obj.total_female_activated_cards);
														var active_male_desktop=parseInt(obj.total_male_activated_desktop_cards);
														var active_male_mobile=parseInt(obj.total_male_activated_mobile_cards);
														var active_male_qrcode=parseInt(obj.total_male_activated_qr_cards);
														var active_female_desktop=parseInt(obj.total_female_activated_desktop_cards);
														var active_female_mobile=parseInt(obj.total_female_activated_mobile_cards);
														var active_female_qrcode=parseInt(obj.total_female_activated_qr_cards);
														
														var total_cards_rewarded=parseInt(obj.total_cards_rewarded);
														var reward_male=parseInt(obj.total_male_rewarded_cards);
														var reward_female=parseInt(obj.total_female_rewarded_cards);
														var reward_male_desktop=parseInt(obj.total_male_rewarded_desktop_cards);
														var reward_male_mobile=parseInt(obj.total_male_rewarded_mobile_cards);
														var reward_male_qrcode=parseInt(obj.total_male_rewarded_qr_cards);
														var reward_female_desktop=parseInt(obj.total_female_rewarded_desktop_cards);
														var reward_female_mobile=parseInt(obj.total_female_rewarded_mobile_cards);
														var reward_female_qrcode=parseInt(obj.total_female_rewarded_qr_cards);
														
														var total_cards_deleted=parseInt(obj.total_cards_deleted);
														var delete_male=parseInt(obj.total_male_deleted_cards);
														var delete_female=parseInt(obj.total_female_deleted_cards);	
														var delete_male_desktop=parseInt(obj.total_male_deleted_desktop_cards);
														var delete_male_mobile=parseInt(obj.total_male_deleted_mobile_cards);
														var delete_male_qrcode=parseInt(obj.total_male_deleted_qr_cards);
														var delete_female_desktop=parseInt(obj.total_female_deleted_desktop_cards);
														var delete_female_mobile=parseInt(obj.total_female_deleted_mobile_cards);
														var delete_female_qrcode=parseInt(obj.total_female_deleted_qr_cards);
														
														
														try
														{
														// Create the chart
														
														//jQuery('#card_metrics_drilldown_container').highcharts({
														loyalty_metric_chart = new Highcharts.Chart({
															chart: {
																renderTo: 'card_metrics_drilldown_container',
																type: 'column',
																//borderColor: 'rgb(69, 114, 167)',
																//borderWidth: 1
																//plotBorderColor: 'rgb(69, 114, 167)',
																//plotBorderWidth:2
																
															},
															title: {
																text: 'Card Metrics'
															},
															subtitle: {
																text: 'Click the columns to view detail result'
															},
															xAxis: {
																type: 'category'
																
															},
															yAxis: {
																title: {
																	text: 'Total Cards'
																},
																 gridLineWidth: 0,
																  lineWidth:1
															},
															legend: {
																enabled: true
															},
															credits: {
																enabled: false
															},
															plotOptions: {
																series: {
																	borderWidth: 0,
																	dataLabels: {
																		enabled: true,
																		format: '{point.y:.1f}'
																	}
																}
															},
															
															tooltip: {
																headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
															},
															
															series: [{
																name: 'Loyalty Card Metrics',
																colorByPoint: true,
																data: [{
																			name: 'Total Cards Activated',
																			y: total_cards_activated,
																			drilldown: 'total_cards_activated'
																		},
																		{
																			name: 'Total Cards Rewarded',
																			y: total_cards_rewarded,
																			drilldown: 'total_cards_rewarded'
																		},
																		{
																			name: 'Total Cards Deleted',
																			y: total_cards_deleted,
																			drilldown: 'total_cards_deleted'
																		},
																		]
															}],
															drilldown: {
																drillUpButton: {
																	relativeTo: 'spacingBox',
																	position: {
																		y: 0,
																		x: 0
																	},
																	theme: {
																		fill: '#eaeaea',
																		'stroke-width': 1,
																		stroke: 'silver',
																		r: 0,
																		states: {
																			
																			select: {
																				stroke: '#039',
																				fill: '#bada55'
																			}
																		}
																	}

																},
																series: [{
																			id: 'total_cards_activated',
																			name: 'Total Cards Activated',
																			data: [{
																					name: 'Male',
																					y: active_male,
																					drilldown: 'active_male'
																					},
																					{
																					name: 'Female',
																					y: active_female,
																					drilldown: 'active_female'
																					}
																				]
																		},
																		{
																			id: 'total_cards_rewarded',
																			name: 'Total Cards Rewarded',
																			data: [{
																					name: 'Male',
																					y: reward_male,
																					drilldown: 'reward_male'
																					},
																					{
																					name: 'Female',
																					y: reward_female,
																					drilldown: 'reward_female'
																					}
																				]
																		},
																		{
																			id: 'total_cards_deleted',
																			name: 'Total Cards Deleted',
																			data: [{
																					name: 'Male',
																					y: delete_male,
																					drilldown: 'delete_male'
																					},
																					{
																					name: 'Female',
																					y: delete_female,
																					drilldown: 'delete_female'
																					}
																				]
																		}, 
																		{

																			id: 'active_male',
																			name: 'Total Cards Activated By Male',
																			data: [['Desktop',active_male_desktop],
																				   ['Mobile Device',active_male_mobile],
																				   ['QR Code Scan',active_male_qrcode]]
																		}, 
																		{

																			id: 'active_female',
																			name: 'Total Cards Activated By Female',
																			data: [['Desktop',active_female_desktop],
																				   ['Mobile Device',active_female_mobile],
																				   ['QR Code Scan',active_female_qrcode]]
																		}, 
																		{

																			id: 'reward_male',
																			name: 'Total Cards Rewarded By Male',
																			data: [['Desktop',reward_male_desktop],
																				   ['Mobile Device',reward_male_mobile],
																				   ['QR Code Scan',reward_male_qrcode]]
																		}, 
																		{

																			id: 'reward_female',
																			name: 'Total Cards Rewarded By Female',
																			data: [['Desktop',reward_female_desktop],
																				   ['Mobile Device',reward_female_mobile],
																				   ['QR Code Scan',reward_female_qrcode]]
																		}, 
																		{

																			id: 'delete_male',
																			name: 'Total Cards Deleted By Male',
																			data: [['Desktop',delete_male_desktop],
																				   ['Mobile Device',delete_male_mobile],
																				   ['QR Code Scan',delete_male_qrcode]]
																		}, 
																		{

																			id: 'delete_female',
																			name: 'Total Cards Deleted By Female',
																			data: [['Desktop',delete_female_desktop],
																				   ['Mobile Device',delete_female_mobile],
																				   ['QR Code Scan',delete_female_qrcode]]
																		}]
															}
														});   
														
														// end drill down report
														
														// start revenue report
														
															var categories = ['Total Revenue','Average Revenue per visit','Average  Revenue per location'];
															
															var total_revenue=parseInt(obj.total_revenue);
															var total_male_revenue=parseInt(obj.total_male_revenue);
															var total_female_revenue=parseInt(obj.total_female_revenue);
															
															var total_average_revenue_per_visit=parseInt(obj.total_average_revenue_per_visit);
															var total_average_male_revenue_per_visit=parseInt(obj.total_average_male_revenue_per_visit);
															var total_average_female_revenue_per_visit=parseInt(obj.total_average_female_revenue_per_visit);
			
															var total_average_revenue_per_location=parseInt(obj.total_average_revenue_per_location);
															var total_average_male_revenue_per_location=parseInt(obj.total_average_male_revenue_per_location);
															var total_average_female_revenue_per_location=parseInt(obj.total_average_female_revenue_per_location);
															
															//jQuery('#card_revenue_container').highcharts({
															loyalty_revenue_chart = new Highcharts.Chart({
																	chart: {
																		renderTo: 'card_revenue_container',
																		type: 'column',
																		//borderColor: 'rgb(69, 114, 167)',
																		//borderWidth: 1,
																		//plotBorderColor: 'rgb(69, 114, 167)',
																		//plotBorderWidth:2
																		
																	},
																	title: {
																		text: 'Revenue Summary'
																	},
																	subtitle: {
																		text: 'Click the columns to view detail result'
																	},
																	xAxis: {
																		type: 'category',
																		
																	},
																	yAxis: {
																		title: {
																			text: 'Revenue'
																		},
																		 gridLineWidth: 0,
																		lineWidth:1
																	},
																	legend: {
																		enabled: true
																	},
																	credits: {
																		enabled: false
																	},
																	plotOptions: {
																		series: {
																			borderWidth: 0,
																			dataLabels: {
																				enabled: true,
																				format: '{point.y:.1f}'
																			}
																		}
																	},
																	
																	tooltip: {
																		headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																		pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
																	},
																	
																	series: [{
																		name: 'Revenue Metrics',
																		colorByPoint: true,
																		data: [{
																					name: 'Total Revenue',
																					y: total_revenue,
																					drilldown: 'total_revenue'
																				},
																				{
																					name: 'Average Revenue per visit',
																					y: total_average_revenue_per_visit,
																					drilldown: 'average_revenue_per_visit'
																				},
																				{
																					name: 'Average  Revenue per location',
																					y: total_average_revenue_per_location,
																					drilldown: 'average_revenue_per_location'
																				},
																				]
																	}],
																	drilldown: {
																		drillUpButton: {
																			relativeTo: 'spacingBox',
																			position: {
																				y: 0,
																				x: 0
																			},
																			theme: {
																				fill: '#eaeaea',
																				'stroke-width': 1,
																				stroke: 'silver',
																				r: 0,
																				states: {
																					select: {
																						stroke: '#039',
																						fill: '#bada55'
																					}
																				}
																			}

																		},
																		series: [{
																					id: 'total_revenue',
																					name: 'Total Revenue',
																					data: [{
																							name: 'Male',
																							y: total_male_revenue
																							},
																							{
																							name: 'Female',
																							y: total_female_revenue
																							}
																						]
																				},
																				{
																					id: 'average_revenue_per_visit',
																					name: 'Average Revenue Per Visit',
																					data: [{
																							name: 'Male',
																							y: total_average_male_revenue_per_visit
																							},
																							{
																							name: 'Female',
																							y: total_average_female_revenue_per_visit
																							}
																						]
																				},
																				{
																					id: 'average_revenue_per_location',
																					name: 'Average Revenue Per Location',
																					data: [{
																							name: 'Male',
																							y: total_average_male_revenue_per_location
																							},
																							{
																							name: 'Female',
																							y: total_average_female_revenue_per_location
																							}
																						]
																				}]
																	}
																});
														
														// end revenue report
														
														// start for customer visit by age
														
														var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
														
														var f_17 = parseInt(obj.f_17);
														var f_18 = parseInt(obj.f_18);
														var f_25 = parseInt(obj.f_25);
														var f_45 = parseInt(obj.f_45);
														var f_55 = parseInt(obj.f_55);
														var f_65 = parseInt(obj.f_65);
														
														var female_visit = [f_17,f_18,f_25,f_45,f_55,f_65];
														
														var m_17 = parseInt(obj.m_17);
														var m_18 = parseInt(obj.m_18);
														var m_25 = parseInt(obj.m_25);
														var m_45 = parseInt(obj.m_45);
														var m_55 = parseInt(obj.m_55);
														var m_65 = parseInt(obj.m_65);
														
														var male_visit = [m_17,m_18,m_25,m_45,m_55,m_65];
														
														var rf_17 = parseInt(obj.rf_17);
														var rf_18 = parseInt(obj.rf_18);
														var rf_25 = parseInt(obj.rf_25);
														var rf_45 = parseInt(obj.rf_45);
														var rf_55 = parseInt(obj.rf_55);
														var rf_65 = parseInt(obj.rf_65);
														
														var revenue_female = [rf_17,rf_18,rf_25,rf_45,rf_55,rf_65];
														
														var rm_17 = parseInt(obj.rm_17);
														var rm_18 = parseInt(obj.rm_18);
														var rm_25 = parseInt(obj.rm_25);
														var rm_45 = parseInt(obj.rm_45);
														var rm_55 = parseInt(obj.rm_55);
														var rm_65 = parseInt(obj.rm_65);
														
														var revenue_male = [rm_17,rm_18,rm_25,rm_45,rm_55,rm_65];
														
														var rt_17 = parseInt(obj.rt_17);
														var rt_18 = parseInt(obj.rt_18);
														var rt_25 = parseInt(obj.rt_25);
														var rt_45 = parseInt(obj.rt_45);
														var rt_55 = parseInt(obj.rt_55);
														var rt_65 = parseInt(obj.rt_65);
														
														var revenue_total = [rt_17,rt_18,rt_25,rt_45,rt_55,rt_65];
														
														//jQuery('#card_visit_by_age_container').highcharts({
														loyalty_visitbyage_chart = new Highcharts.Chart({
															chart: {
																renderTo: 'card_visit_by_age_container',
																//plotBorderColor: 'rgb(69, 114, 167)',
																//plotBorderWidth:2
															},
															title: {
																text: 'Customer Visit & Revenue Metrics By Age',
																x: -20 //center
															},
															subtitle: {
																text: '',
																x: -20
															},
															credits: {
																enabled: false
															},
															xAxis:{
																title: {
																	text: 'Customer Age'
																},
																categories: categories
															},
															yAxis: [{ // Primary yAxis
																   title: {
																	   text: 'Customer Visits'
																   },
																   min: 0,
																   gridLineWidth: 0,
																   lineWidth:1
															  }, { // Secondary yAxis
																title: {
																	text: 'Revenue'
																},
																  min: 0,
																  gridLineWidth: 0,
																  lineWidth:1,
																opposite: true
															}],       
															legend: {
																layout: 'vertical',
																align: 'right',
																verticalAlign: 'middle',
																borderWidth: 0
															},
															/*
															plotOptions: {
																series: {
																	stacking: 'normal'
																}
															},
															*/
															series: [{
																name: 'Female Visit',
																color: '#9BBB59',
																type: 'column',
																yAxis: 0,
																data: female_visit
															},{
																name: 'Male Visit',
																color: '#C0504D',
																type: 'column',
																yAxis: 0,
																data: male_visit
															},{
																name: 'Revenue Female',
																color: '#F59240',
																type: 'spline',
																yAxis: 1,
																data: revenue_female
															},{
																name: 'Revenue Male',
																color: '#46AAC4',
																type: 'spline',
																yAxis: 1,
																data: revenue_male
															},{
																name: 'Total Revenue',
																color: '#7D5FA0',
																type: 'spline',
																yAxis: 1,
																data: revenue_total
															}]
														});
														
														// end for customer visit by age
														
														// start for customer visit by time
														
														var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
														
														var f_1 = parseInt(obj.f_1);
														var f_2 = parseInt(obj.f_2);
														var f_3 = parseInt(obj.f_3);
														var f_4 = parseInt(obj.f_4);
														
														var female_visit = [f_1,f_2,f_3,f_4];
														
														var m_1 = parseInt(obj.m_1);
														var m_2 = parseInt(obj.m_2);
														var m_3 = parseInt(obj.m_3);
														var m_4 = parseInt(obj.m_4);
														
														var male_visit = [m_1,m_2,m_3,m_4];
														
														var rf_1 = parseInt(obj.rf_1);
														var rf_2 = parseInt(obj.rf_2);
														var rf_3 = parseInt(obj.rf_3);
														var rf_4 = parseInt(obj.rf_4);
														
														var revenue_female = [rf_1,rf_2,rf_3,rf_4];
														
														var rm_1 = parseInt(obj.rm_1);
														var rm_2 = parseInt(obj.rm_2);
														var rm_3 = parseInt(obj.rm_3);
														var rm_4 = parseInt(obj.rm_4);
														
														var revenue_male = [rm_1,rm_2,rm_3,rm_4];
														
														var rt_1 = parseInt(obj.rt_1);
														var rt_2 = parseInt(obj.rt_2);
														var rt_3 = parseInt(obj.rt_3);
														var rt_4 = parseInt(obj.rt_4);
														
														var revenue_total = [rt_1,rt_2,rt_3,rt_4];
														
														//jQuery('#card_visit_by_time_container').highcharts({
														loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_time_container'
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														
														// end for customer visit by time
														
														}
														catch(e)
														{
															console.log(e);
														}
														
														jQuery(".loader_loyalty_metrics").css("display","none");
														jQuery(".loader_loyalty_revenue").css("display","none");
														jQuery(".loader_loyalty_age").css("display","none");
														jQuery(".loader_loyalty_time").css("display","none");
														
														
								
													}
													else
													{
														
															
														jQuery('.loyalty_card_metrics_filter').css("display","block");
														
														jQuery("#opt_filter_location_age").empty().append(obj.options_age);
														jQuery("#opt_filter_location_time").empty().append(obj.options_time);
														
														//alert(obj.message);
														/*
														jQuery('#card_metrics_drilldown_container').html(obj.message);
														jQuery('#card_revenue_container').html(obj.message);
														jQuery('#card_visit_by_age_container').html(obj.message);
														jQuery('#card_visit_by_time_container').html(obj.message);
														*/
														jQuery(".loader_loyalty_metrics").css("display","none");
														jQuery(".loader_loyalty_revenue").css("display","none");
														jQuery(".loader_loyalty_age").css("display","none");
														jQuery(".loader_loyalty_time").css("display","none");
														
														jQuery(".nodata_loyalty_metrics").css("display","block");
														jQuery(".nodata_loyalty_revenue").css("display","block");
														jQuery(".nodata_loyalty_age").css("display","block");
														jQuery(".nodata_loyalty_time").css("display","block");
								
														
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
											
											}
										}
									}
								});
								
							});
						
											
					
						jQuery("#opt_filter_days_metric1").live("change",function(){
							
							jQuery(".nodata_loyalty_metrics").css("display","none");
							jQuery('#card_metrics_drilldown_container').css("display","none");
							jQuery(".loader_loyalty_metrics").show();
							//alert("hi");	
							var opt_filter_days = jQuery(this).val();
							//alert(opt_filter_days);
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								//async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										var card_id = jQuery("#hdncardid").val();
										var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_card_metrics=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														//alert("success");
														jQuery('#card_metrics_drilldown_container').css("display","block");														
														
														// start drill down report
														
														var total_cards_activated=parseInt(obj.total_cards_activated);
														var active_male=parseInt(obj.total_male_activated_cards);
														var active_female=parseInt(obj.total_female_activated_cards);
														var active_male_desktop=parseInt(obj.total_male_activated_desktop_cards);
														var active_male_mobile=parseInt(obj.total_male_activated_mobile_cards);
														var active_male_qrcode=parseInt(obj.total_male_activated_qr_cards);
														var active_female_desktop=parseInt(obj.total_female_activated_desktop_cards);
														var active_female_mobile=parseInt(obj.total_female_activated_mobile_cards);
														var active_female_qrcode=parseInt(obj.total_female_activated_qr_cards);
														
														var total_cards_rewarded=parseInt(obj.total_cards_rewarded);
														var reward_male=parseInt(obj.total_male_rewarded_cards);
														var reward_female=parseInt(obj.total_female_rewarded_cards);
														var reward_male_desktop=parseInt(obj.total_male_rewarded_desktop_cards);
														var reward_male_mobile=parseInt(obj.total_male_rewarded_mobile_cards);
														var reward_male_qrcode=parseInt(obj.total_male_rewarded_qr_cards);
														var reward_female_desktop=parseInt(obj.total_female_rewarded_desktop_cards);
														var reward_female_mobile=parseInt(obj.total_female_rewarded_mobile_cards);
														var reward_female_qrcode=parseInt(obj.total_female_rewarded_qr_cards);
														
														var total_cards_deleted=parseInt(obj.total_cards_deleted);
														var delete_male=parseInt(obj.total_male_deleted_cards);
														var delete_female=parseInt(obj.total_female_deleted_cards);	
														var delete_male_desktop=parseInt(obj.total_male_deleted_desktop_cards);
														var delete_male_mobile=parseInt(obj.total_male_deleted_mobile_cards);
														var delete_male_qrcode=parseInt(obj.total_male_deleted_qr_cards);
														var delete_female_desktop=parseInt(obj.total_female_deleted_desktop_cards);
														var delete_female_mobile=parseInt(obj.total_female_deleted_mobile_cards);
														var delete_female_qrcode=parseInt(obj.total_female_deleted_qr_cards);
														
														
														try
														{
														// Create the chart
														
														
														jQuery('#card_metrics_drilldown_container').highcharts({
															chart: {
																type: 'column'
															},
															title: {
																text: 'Card Metrics'
															},
															subtitle: {
																text: 'Click the columns to view detail result'
															},
															xAxis: {
																type: 'category'
																
															},
															yAxis: {
																title: {
																	text: 'Total Cards'
																},
																 gridLineWidth: 0,
																  lineWidth:1
															},
															credits: {
																enabled: false
															},
															legend: {
																enabled: true
															},
															plotOptions: {
																series: {
																	borderWidth: 0,
																	dataLabels: {
																		enabled: true,
																		format: '{point.y:.1f}'
																	}
																}
															},
															
															tooltip: {
																headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
															},
															
															series: [{
																name: 'Loyalty Card Metrics',
																colorByPoint: true,
																data: [{
																			name: 'Total Cards Activated',
																			y: total_cards_activated,
																			drilldown: 'total_cards_activated'
																		},
																		{
																			name: 'Total Cards Rewarded',
																			y: total_cards_rewarded,
																			drilldown: 'total_cards_rewarded'
																		},
																		{
																			name: 'Total Cards Deleted',
																			y: total_cards_deleted,
																			drilldown: 'total_cards_deleted'
																		},
																		]
															}],
															drilldown: {
																drillUpButton: {
																	relativeTo: 'spacingBox',
																	position: {
																		y: 0,
																		x: 0
																	},
																	theme: {
																		fill: '#eaeaea',
																		'stroke-width': 1,
																		stroke: 'silver',
																		r: 0,
																		states: {
																			
																			select: {
																				stroke: '#039',
																				fill: '#bada55'
																			}
																		}
																	}

																},
																series: [{
																			id: 'total_cards_activated',
																			name: 'Total Cards Activated',
																			data: [{
																					name: 'Male',
																					y: active_male,
																					drilldown: 'active_male'
																					},
																					{
																					name: 'Female',
																					y: active_female,
																					drilldown: 'active_female'
																					}
																				]
																		},
																		{
																			id: 'total_cards_rewarded',
																			name: 'Total Cards Rewarded',
																			data: [{
																					name: 'Male',
																					y: reward_male,
																					drilldown: 'reward_male'
																					},
																					{
																					name: 'Female',
																					y: reward_female,
																					drilldown: 'reward_female'
																					}
																				]
																		},
																		{
																			id: 'total_cards_deleted',
																			name: 'Total Cards Deleted',
																			data: [{
																					name: 'Male',
																					y: delete_male,
																					drilldown: 'delete_male'
																					},
																					{
																					name: 'Female',
																					y: delete_female,
																					drilldown: 'delete_female'
																					}
																				]
																		}, 
																		{

																			id: 'active_male',
																			name: 'Total Cards Activated By Male',
																			data: [['Desktop',active_male_desktop],
																				   ['Mobile Device',active_male_mobile],
																				   ['QR Code Scan',active_male_qrcode]]
																		}, 
																		{

																			id: 'active_female',
																			name: 'Total Cards Activated By Female',
																			data: [['Desktop',active_female_desktop],
																				   ['Mobile Device',active_female_mobile],
																				   ['QR Code Scan',active_female_qrcode]]
																		}, 
																		{

																			id: 'reward_male',
																			name: 'Total Cards Rewarded By Male',
																			data: [['Desktop',reward_male_desktop],
																				   ['Mobile Device',reward_male_mobile],
																				   ['QR Code Scan',reward_male_qrcode]]
																		}, 
																		{

																			id: 'reward_female',
																			name: 'Total Cards Rewarded By Female',
																			data: [['Desktop',reward_female_desktop],
																				   ['Mobile Device',reward_female_mobile],
																				   ['QR Code Scan',reward_female_qrcode]]
																		}, 
																		{

																			id: 'delete_male',
																			name: 'Total Cards Deleted By Male',
																			data: [['Desktop',delete_male_desktop],
																				   ['Mobile Device',delete_male_mobile],
																				   ['QR Code Scan',delete_male_qrcode]]
																		}, 
																		{

																			id: 'delete_female',
																			name: 'Total Cards Deleted By Female',
																			data: [['Desktop',delete_female_desktop],
																				   ['Mobile Device',delete_female_mobile],
																				   ['QR Code Scan',delete_female_qrcode]]
																		}]
															}
														});   
														
														
														
														// end drill down report
														jQuery(".loader_loyalty_metrics").hide();
														
														}
														catch(e)
														{
															console.log(e);
														}
													}
													else
													{
														//alert(obj.message);
														jQuery(".loader_loyalty_metrics").hide();
														jQuery(".nodata_loyalty_metrics").css("display","block");
													
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});							
						});
						
						
						jQuery("#opt_filter_days_revenue1").live("change",function(){
							
							
							jQuery(".nodata_loyalty_revenue").css("display","none");
							jQuery('#card_revenue_container').css("display","none");
							jQuery(".loader_loyalty_revenue").css("display","block");
							
							var opt_filter_days = jQuery(this).val();
							//alert(opt_filter_days);
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								//async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										var card_id = jQuery("#hdncardid").val();
										var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_card_revenue=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														jQuery('#card_revenue_container').css("display","block");
														// start revenue report
														
															var categories = ['Total Revenue','Average Revenue per visit','Average  Revenue per location'];
															
															var total_revenue=parseInt(obj.total_revenue);
															var total_male_revenue=parseInt(obj.total_male_revenue);
															var total_female_revenue=parseInt(obj.total_female_revenue);
															
															var total_average_revenue_per_visit=parseInt(obj.total_average_revenue_per_visit);
															var total_average_male_revenue_per_visit=parseInt(obj.total_average_male_revenue_per_visit);
															var total_average_female_revenue_per_visit=parseInt(obj.total_average_female_revenue_per_visit);
			
															var total_average_revenue_per_location=parseInt(obj.total_average_revenue_per_location);
															var total_average_male_revenue_per_location=parseInt(obj.total_average_male_revenue_per_location);
															var total_average_female_revenue_per_location=parseInt(obj.total_average_female_revenue_per_location);
															
															jQuery('#card_revenue_container').highcharts({
																	chart: {
																		type: 'column'
																	},
																	title: {
																		text: 'Revenue Summary'
																	},
																	subtitle: {
																		text: 'Click the columns to view detail result'
																	},
																	xAxis: {
																		type: 'category',
																	},
																	yAxis: {
																		title: {
																			text: 'Revenue'
																		},
																		gridLineWidth: 0,
																		lineWidth:1
																	},
																	credits: {
																		enabled: false
																	},
																	legend: {
																		enabled: true
																	},
																	plotOptions: {
																		series: {
																			borderWidth: 0,
																			dataLabels: {
																				enabled: true,
																				format: '{point.y:.1f}'
																			}
																		}
																	},
																	
																	tooltip: {
																		headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																		pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
																	},
																	
																	series: [{
																		name: 'Revenue Metrics',
																		colorByPoint: true,
																		data: [{
																					name: 'Total Revenue',
																					y: total_revenue,
																					drilldown: 'total_revenue'
																				},
																				{
																					name: 'Average Revenue per visit',
																					y: total_average_revenue_per_visit,
																					drilldown: 'average_revenue_per_visit'
																				},
																				{
																					name: 'Average  Revenue per location',
																					y: total_average_revenue_per_location,
																					drilldown: 'average_revenue_per_location'
																				},
																				]
																	}],
																	drilldown: {
																		drillUpButton: {
																			relativeTo: 'spacingBox',
																			position: {
																				y: 0,
																				x: 0
																			},
																			theme: {
																				fill: '#eaeaea',
																				'stroke-width': 1,
																				stroke: 'silver',
																				r: 0,
																				states: {
																					
																					select: {
																						stroke: '#039',
																						fill: '#bada55'
																					}
																				}
																			}

																		},
																		series: [{
																					id: 'total_revenue',
																					name: 'Total Revenue',
																					data: [{
																							name: 'Male',
																							y: total_male_revenue
																							},
																							{
																							name: 'Female',
																							y: total_female_revenue
																							}
																						]
																				},
																				{
																					id: 'average_revenue_per_visit',
																					name: 'Average Revenue Per Visit',
																					data: [{
																							name: 'Male',
																							y: total_average_male_revenue_per_visit
																							},
																							{
																							name: 'Female',
																							y: total_average_female_revenue_per_visit
																							}
																						]
																				},
																				{
																					id: 'average_revenue_per_location',
																					name: 'Average Revenue Per Location',
																					data: [{
																							name: 'Male',
																							y: total_average_male_revenue_per_location
																							},
																							{
																							name: 'Female',
																							y: total_average_female_revenue_per_location
																							}
																						]
																				}]
																	}
																});
														
														// end revenue report
														jQuery(".loader_loyalty_revenue").css("display","none");
													}
													else
													{
														//alert(obj.message);
														jQuery(".loader_loyalty_revenue").css("display","none");
														jQuery(".nodata_loyalty_revenue").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
							//jQuery(".loader_loyalty_revenue").css("display","none");
						});
						
						
						jQuery("#opt_filter_days_metric").live("change",function(){
							
							//alert("hi");	
							var opt_filter_days = jQuery(this).val();
							//alert(opt_filter_days);
							var card_id = jQuery(this).attr("card_id");
							
							jQuery("#div_card_"+card_id+" .metricchart .nodata_loyalty_metrics").css("display","none");
							jQuery('#card_metrics_drilldown_container_'+card_id).css("display","none");
							jQuery("#div_card_"+card_id+" .metricchart .loader_loyalty_metrics").show();
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								//async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										
										var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_card_metrics=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														//alert("success");
														jQuery('#card_metrics_drilldown_container_'+card_id).css("display","block");														
														
														// start drill down report
														
														var total_cards_activated=parseInt(obj.total_cards_activated);
														var active_male=parseInt(obj.total_male_activated_cards);
														var active_female=parseInt(obj.total_female_activated_cards);
														var active_male_desktop=parseInt(obj.total_male_activated_desktop_cards);
														var active_male_mobile=parseInt(obj.total_male_activated_mobile_cards);
														var active_male_qrcode=parseInt(obj.total_male_activated_qr_cards);
														var active_female_desktop=parseInt(obj.total_female_activated_desktop_cards);
														var active_female_mobile=parseInt(obj.total_female_activated_mobile_cards);
														var active_female_qrcode=parseInt(obj.total_female_activated_qr_cards);
														
														var total_cards_rewarded=parseInt(obj.total_cards_rewarded);
														var reward_male=parseInt(obj.total_male_rewarded_cards);
														var reward_female=parseInt(obj.total_female_rewarded_cards);
														var reward_male_desktop=parseInt(obj.total_male_rewarded_desktop_cards);
														var reward_male_mobile=parseInt(obj.total_male_rewarded_mobile_cards);
														var reward_male_qrcode=parseInt(obj.total_male_rewarded_qr_cards);
														var reward_female_desktop=parseInt(obj.total_female_rewarded_desktop_cards);
														var reward_female_mobile=parseInt(obj.total_female_rewarded_mobile_cards);
														var reward_female_qrcode=parseInt(obj.total_female_rewarded_qr_cards);
														
														var total_cards_deleted=parseInt(obj.total_cards_deleted);
														var delete_male=parseInt(obj.total_male_deleted_cards);
														var delete_female=parseInt(obj.total_female_deleted_cards);	
														var delete_male_desktop=parseInt(obj.total_male_deleted_desktop_cards);
														var delete_male_mobile=parseInt(obj.total_male_deleted_mobile_cards);
														var delete_male_qrcode=parseInt(obj.total_male_deleted_qr_cards);
														var delete_female_desktop=parseInt(obj.total_female_deleted_desktop_cards);
														var delete_female_mobile=parseInt(obj.total_female_deleted_mobile_cards);
														var delete_female_qrcode=parseInt(obj.total_female_deleted_qr_cards);
														
														
														if(total_cards_activated==0)
															total_cards_activated_dd=null;
														else
															total_cards_activated_dd="total_cards_activated";
															
														if(total_cards_rewarded==0)
															total_cards_rewarded_dd=null;
														else
															total_cards_rewarded_dd="total_cards_rewarded";	
															
														if(total_cards_deleted==0)
															total_cards_deleted_dd=null;
														else
															total_cards_deleted_dd="total_cards_deleted";
															
														if(active_male==0)
															active_male_dd=null;
														else
															active_male_dd="active_male";

														if(active_female==0)
															active_female_dd=null;
														else
															active_female_dd="active_female";
															
														try
														{
														// Create the chart
														
														
														jQuery('#card_metrics_drilldown_container_'+card_id).highcharts({
															chart: {
																type: 'column'
															},
															title: {
																text: 'Card Metrics'
															},
															subtitle: {
																text: 'Click the columns to view detail result'
															},
															xAxis: {
																type: 'category'
																
															},
															yAxis: {
																title: {
																	text: 'Total Cards'
																},
																 gridLineWidth: 0,
																  lineWidth:1
															},
															credits: {
																enabled: false
															},
															legend: {
																enabled: true
															},
															plotOptions: {
																series: {
																	borderWidth: 0,
																	dataLabels: {
																		enabled: true,
																		format: '{point.y:.1f}'
																	}
																}
															},
															
															tooltip: {
																headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
															},
															
															series: [{
																name: 'Loyalty Card Metrics',
																colorByPoint: true,
																data: [{
																			name: 'Total Cards Activated',
																			y: total_cards_activated,
																			drilldown: total_cards_activated_dd
																		},
																		{
																			name: 'Total Cards Rewarded',
																			y: total_cards_rewarded,
																			drilldown: total_cards_rewarded_dd
																		},
																		{
																			name: 'Total Cards Deleted',
																			y: total_cards_deleted,
																			drilldown: total_cards_deleted_dd
																		},
																		]
															}],
															drilldown: {
																drillUpButton: {
																	relativeTo: 'spacingBox',
																	position: {
																		y: 0,
																		x: 0
																	},
																	theme: {
																		fill: '#eaeaea',
																		'stroke-width': 1,
																		stroke: 'silver',
																		r: 0,
																		states: {
																			
																			select: {
																				stroke: '#039',
																				fill: '#bada55'
																			}
																		}
																	}

																},
																series: [{
																			id: 'total_cards_activated',
																			name: 'Total Cards Activated',
																			data: [{
																					name: 'Male',
																					y: active_male,
																					drilldown: active_male_dd
																					},
																					{
																					name: 'Female',
																					y: active_female,
																					drilldown: active_female_dd
																					}
																				]
																		},
																		{
																			id: 'total_cards_rewarded',
																			name: 'Total Cards Rewarded',
																			data: [{
																					name: 'Male',
																					y: reward_male,
																					//drilldown: 'reward_male'
																					},
																					{
																					name: 'Female',
																					y: reward_female,
																					//drilldown: 'reward_female'
																					}
																				]
																		},
																		{
																			id: 'total_cards_deleted',
																			name: 'Total Cards Deleted',
																			data: [{
																					name: 'Male',
																					y: delete_male,
																					//drilldown: 'delete_male'
																					},
																					{
																					name: 'Female',
																					y: delete_female,
																					//drilldown: 'delete_female'
																					}
																				]
																		}, 
																		{

																			id: 'active_male',
																			name: 'Total Cards Activated By Male',
																			data: [['Mobile Device',active_male_mobile],
																				   ['QR Code Scan',active_male_qrcode]]
																		}, 
																		{

																			id: 'active_female',
																			name: 'Total Cards Activated By Female',
																			data: [['Mobile Device',active_female_mobile],
																				   ['QR Code Scan',active_female_qrcode]]
																		}, 
																		{

																			id: 'reward_male',
																			name: 'Total Cards Rewarded By Male',
																			data: [['Mobile Device',reward_male_mobile],
																				   ['QR Code Scan',reward_male_qrcode]]
																		}, 
																		{

																			id: 'reward_female',
																			name: 'Total Cards Rewarded By Female',
																			data: [['Mobile Device',reward_female_mobile],
																				   ['QR Code Scan',reward_female_qrcode]]
																		}, 
																		{

																			id: 'delete_male',
																			name: 'Total Cards Deleted By Male',
																			data: [['Mobile Device',delete_male_mobile],
																				   ['QR Code Scan',delete_male_qrcode]]
																		}, 
																		{

																			id: 'delete_female',
																			name: 'Total Cards Deleted By Female',
																			data: [['Mobile Device',delete_female_mobile],
																				   ['QR Code Scan',delete_female_qrcode]]
																		}]
															}
														});   
														
														
														
														// end drill down report
														jQuery("#div_card_"+card_id+" .metricchart .loader_loyalty_metrics").hide();
														
														if(total_cards_activated==0 && total_cards_rewarded==0 && total_cards_deleted==0)												
														{
															//jQuery('#div_card_'+card_id+' .metricchart .nodata_loyalty_metrics').css("display","block");
															//jQuery('#card_metrics_drilldown_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .metricchart .loyalty_card_metrics_filter').css("display","none");
															
															loyalty_metric_chart = new Highcharts.Chart({
																	chart: {
																		renderTo: 'card_metrics_drilldown_container_'+card_id,
																		type: 'column',
																		//borderColor: 'rgb(69, 114, 167)',
																		//borderWidth: 1
																		//plotBorderColor: 'rgb(69, 114, 167)',
																		//plotBorderWidth:2
																		
																	},
																	title: {
																		text: 'Card Metrics'
																	},
																	subtitle: {
																		text: 'Click the columns to view detail result'
																	},
																	xAxis: {
																		type: 'category'
																		
																	},
																	yAxis: {
																		title: {
																			text: 'Total Cards'
																		},
																		 gridLineWidth: 0,
																		  lineWidth:1
																	},
																	legend: {
																		enabled: true
																	},
																	credits: {
																		enabled: false
																	},															
																	series: [{
																		name: 'Loyalty Card Metrics',
																		colorByPoint: true,
																		data: []
																	}]
																}); 
														}
														
														}
														catch(e)
														{
															console.log(e);
														}
													}
													else
													{
														//alert(obj.message);
														jQuery("#div_card_"+card_id+" .metricchart .loader_loyalty_metrics").hide();
														jQuery("#div_card_"+card_id+" .metricchart .nodata_loyalty_metrics").css("display","block");
													
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});							
						});
						
						
						jQuery("#opt_filter_days_revenue").live("change",function(){
							
							var opt_filter_days = jQuery(this).val();
							//alert(opt_filter_days);
							var card_id = jQuery(this).attr("card_id");
							
							jQuery("#div_card_"+card_id+" .revenuechart .nodata_loyalty_revenue").css("display","none");
							jQuery('#card_revenue_container_'+card_id).css("display","none");
							jQuery("#div_card_"+card_id+" .revenuechart .loader_loyalty_revenue").css("display","block");
							
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								//async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										
										var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_card_revenue=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														jQuery('#card_revenue_container_'+card_id).css("display","block");
														// start revenue report
														
															var categories = ['Total Revenue','Average Revenue per visit','Average  Revenue per location'];
															
															var total_revenue=parseInt(obj.total_revenue);
															var total_male_revenue=parseInt(obj.total_male_revenue);
															var total_female_revenue=parseInt(obj.total_female_revenue);
															
															var total_average_revenue_per_visit=parseInt(obj.total_average_revenue_per_visit);
															var total_average_male_revenue_per_visit=parseInt(obj.total_average_male_revenue_per_visit);
															var total_average_female_revenue_per_visit=parseInt(obj.total_average_female_revenue_per_visit);
			
															var total_average_revenue_per_location=parseInt(obj.total_average_revenue_per_location);
															var total_average_male_revenue_per_location=parseInt(obj.total_average_male_revenue_per_location);
															var total_average_female_revenue_per_location=parseInt(obj.total_average_female_revenue_per_location);
															
															if(total_revenue==0)
																total_revenue_dd=null;
															else
																total_revenue_dd="total_revenue";

															if(total_average_revenue_per_visit==0)
																average_revenue_per_visit_dd=null;
															else
																average_revenue_per_visit_dd="average_revenue_per_visit";	
																
															if(total_average_revenue_per_location==0)
																average_revenue_per_location_dd=null;
															else
																average_revenue_per_location_dd="average_revenue_per_location";	
	
															jQuery('#card_revenue_container_'+card_id).highcharts({
																	chart: {
																		type: 'column'
																	},
																	title: {
																		text: 'Revenue Summary'
																	},
																	subtitle: {
																		text: 'Click the columns to view detail result'
																	},
																	xAxis: {
																		type: 'category',
																	},
																	yAxis: {
																		title: {
																			text: 'Revenue'
																		},
																		gridLineWidth: 0,
																		lineWidth:1
																	},
																	credits: {
																		enabled: false
																	},
																	legend: {
																		enabled: true
																	},
																	plotOptions: {
																		series: {
																			borderWidth: 0,
																			dataLabels: {
																				enabled: true,
																				format: '{point.y:.1f}'
																			}
																		}
																	},
																	
																	tooltip: {
																		headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
																		pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
																	},
																	
																	series: [{
																		name: 'Revenue Metrics',
																		colorByPoint: true,
																		data: [{
																					name: 'Total Revenue',
																					y: total_revenue,
																					drilldown: total_revenue_dd
																				},
																				{
																					name: 'Average Revenue per visit',
																					y: total_average_revenue_per_visit,
																					drilldown: average_revenue_per_visit_dd
																				},
																				{
																					name: 'Average  Revenue per location',
																					y: total_average_revenue_per_location,
																					drilldown: average_revenue_per_location_dd
																				},
																				]
																	}],
																	drilldown: {
																		drillUpButton: {
																			relativeTo: 'spacingBox',
																			position: {
																				y: 0,
																				x: 0
																			},
																			theme: {
																				fill: '#eaeaea',
																				'stroke-width': 1,
																				stroke: 'silver',
																				r: 0,
																				states: {
																					
																					select: {
																						stroke: '#039',
																						fill: '#bada55'
																					}
																				}
																			}

																		},
																		series: [{
																					id: 'total_revenue',
																					name: 'Total Revenue',
																					data: [{
																							name: 'Male',
																							y: total_male_revenue
																							},
																							{
																							name: 'Female',
																							y: total_female_revenue
																							}
																						]
																				},
																				{
																					id: 'average_revenue_per_visit',
																					name: 'Average Revenue Per Visit',
																					data: [{
																							name: 'Male',
																							y: total_average_male_revenue_per_visit
																							},
																							{
																							name: 'Female',
																							y: total_average_female_revenue_per_visit
																							}
																						]
																				},
																				{
																					id: 'average_revenue_per_location',
																					name: 'Average Revenue Per Location',
																					data: [{
																							name: 'Male',
																							y: total_average_male_revenue_per_location
																							},
																							{
																							name: 'Female',
																							y: total_average_female_revenue_per_location
																							}
																						]
																				}]
																	}
																});
														
														// end revenue report
														jQuery("#div_card_"+card_id+" .revenuechart .loader_loyalty_revenue").css("display","none");
														
														if(total_revenue==0 && total_average_revenue_per_visit==0 && total_average_revenue_per_location==0)												
														{
															//jQuery('#div_card_'+card_id+' .revenuechart .nodata_loyalty_revenue').css("display","block");
															//jQuery('#card_revenue_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .revenuechart .loyalty_card_metrics_filter').css("display","none");
															
															loyalty_revenue_chart = new Highcharts.Chart({
																		chart: {
																			renderTo: 'card_revenue_container_'+card_id,
																			type: 'column',
																			//borderColor: 'rgb(69, 114, 167)',
																			//borderWidth: 1,
																			//plotBorderColor: 'rgb(69, 114, 167)',
																			//plotBorderWidth:2
																			
																		},
																		title: {
																			text: 'Revenue Summary'
																		},
																		subtitle: {
																			text: 'Click the columns to view detail result'
																		},
																		xAxis: {
																			type: 'category',
																			
																		},
																		yAxis: {
																			title: {
																				text: 'Revenue'
																			},
																			 gridLineWidth: 0,
																			lineWidth:1
																		},
																		legend: {
																			enabled: true
																		},
																		credits: {
																			enabled: false
																		},
																		series: [{
																			name: 'Revenue Metrics',
																			colorByPoint: true,
																			data: []
																	}]
																});
																		
														}
													}
													else
													{
														//alert(obj.message);
														jQuery("#div_card_"+card_id+" .revenuechart .loader_loyalty_revenue").css("display","none");
														jQuery("#div_card_"+card_id+" .revenuechart .nodata_loyalty_revenue").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
							//jQuery(".loader_loyalty_revenue").css("display","none");
						});
						
						
						
						
						jQuery("#opt_filter_days_age1").live("change",function(){
							
							jQuery(".nodata_loyalty_age").css("display","none");
							jQuery('#card_visit_by_age_container').css("display","none");
							jQuery('#card_visit_by_age_container').css("display","block");
							jQuery(".loader_loyalty_age").css("display","block");
							
							var opt_filter_days = jQuery(this).val();
							var opt_filter_location = jQuery("#opt_filter_location_age").val();
							//alert(opt_filter_days);
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										var card_id = jQuery("#hdncardid").val();
										var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_by_age=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_location="+opt_filter_location+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													console.log(obj.status);
													if (obj.status=="true") 
													{
														jQuery('#card_visit_by_age_container').css("display","block");
														var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
														
														var f_17 = parseInt(obj.f_17);
														var f_18 = parseInt(obj.f_18);
														var f_25 = parseInt(obj.f_25);
														var f_45 = parseInt(obj.f_45);
														var f_55 = parseInt(obj.f_55);
														var f_65 = parseInt(obj.f_65);
														
														var female_visit = [f_17,f_18,f_25,f_45,f_55,f_65];
														
														var m_17 = parseInt(obj.m_17);
														var m_18 = parseInt(obj.m_18);
														var m_25 = parseInt(obj.m_25);
														var m_45 = parseInt(obj.m_45);
														var m_55 = parseInt(obj.m_55);
														var m_65 = parseInt(obj.m_65);
														
														var male_visit = [m_17,m_18,m_25,m_45,m_55,m_65];
														
														var rf_17 = parseInt(obj.rf_17);
														var rf_18 = parseInt(obj.rf_18);
														var rf_25 = parseInt(obj.rf_25);
														var rf_45 = parseInt(obj.rf_45);
														var rf_55 = parseInt(obj.rf_55);
														var rf_65 = parseInt(obj.rf_65);
														
														var revenue_female = [rf_17,rf_18,rf_25,rf_45,rf_55,rf_65];
														
														var rm_17 = parseInt(obj.rm_17);
														var rm_18 = parseInt(obj.rm_18);
														var rm_25 = parseInt(obj.rm_25);
														var rm_45 = parseInt(obj.rm_45);
														var rm_55 = parseInt(obj.rm_55);
														var rm_65 = parseInt(obj.rm_65);
														
														var revenue_male = [rm_17,rm_18,rm_25,rm_45,rm_55,rm_65];
														
														var rt_17 = parseInt(obj.rt_17);
														var rt_18 = parseInt(obj.rt_18);
														var rt_25 = parseInt(obj.rt_25);
														var rt_45 = parseInt(obj.rt_45);
														var rt_55 = parseInt(obj.rt_55);
														var rt_65 = parseInt(obj.rt_65);
														
														var revenue_total = [rt_17,rt_18,rt_25,rt_45,rt_55,rt_65];
														
														if(typeof loyalty_visitbyage_chart === 'undefined')
														{	
															console.log("not set");
															loyalty_visitbyage_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_age_container',
																	//plotBorderColor: 'rgb(69, 114, 167)',
																	//plotBorderWidth:2
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Age',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Customer Age'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#9BBB59',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#C0504D',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														}
														else
														{
															console.log("set");														
															loyalty_visitbyage_chart.series[0].setData(female_visit);
															loyalty_visitbyage_chart.series[1].setData(male_visit);
															loyalty_visitbyage_chart.series[2].setData(revenue_female);
															loyalty_visitbyage_chart.series[3].setData(revenue_male);
															loyalty_visitbyage_chart.series[4].setData(revenue_total);
														}
														jQuery(".loader_loyalty_age").css("display","none");
													}
													else
													{
														//alert(obj.message);
														jQuery(".loader_loyalty_age").css("display","none");
														jQuery(".nodata_loyalty_age").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
							//jQuery(".loader_loyalty_age").css("display","none");
						});
						
						jQuery("#opt_filter_location_age1").live("change",function(){
							
							jQuery(".nodata_loyalty_age").css("display","none");
							jQuery('#card_visit_by_age_container').css("display","none");
							jQuery('#card_visit_by_age_container').css("display","block");
							jQuery(".loader_loyalty_age").css("display","block");
							
							var opt_filter_location = jQuery(this).val();
							var opt_filter_days = jQuery("#opt_filter_days_age").val();
							//alert(opt_filter_days);
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										var card_id = jQuery("#hdncardid").val();
										var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_by_age=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_location="+opt_filter_location+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														jQuery('#card_visit_by_age_container').css("display","block");
														
														var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
														
														var f_17 = parseInt(obj.f_17);
														var f_18 = parseInt(obj.f_18);
														var f_25 = parseInt(obj.f_25);
														var f_45 = parseInt(obj.f_45);
														var f_55 = parseInt(obj.f_55);
														var f_65 = parseInt(obj.f_65);
														
														var female_visit = [f_17,f_18,f_25,f_45,f_55,f_65];
														
														var m_17 = parseInt(obj.m_17);
														var m_18 = parseInt(obj.m_18);
														var m_25 = parseInt(obj.m_25);
														var m_45 = parseInt(obj.m_45);
														var m_55 = parseInt(obj.m_55);
														var m_65 = parseInt(obj.m_65);
														
														var male_visit = [m_17,m_18,m_25,m_45,m_55,m_65];
														
														var rf_17 = parseInt(obj.rf_17);
														var rf_18 = parseInt(obj.rf_18);
														var rf_25 = parseInt(obj.rf_25);
														var rf_45 = parseInt(obj.rf_45);
														var rf_55 = parseInt(obj.rf_55);
														var rf_65 = parseInt(obj.rf_65);
														
														var revenue_female = [rf_17,rf_18,rf_25,rf_45,rf_55,rf_65];
														
														var rm_17 = parseInt(obj.rm_17);
														var rm_18 = parseInt(obj.rm_18);
														var rm_25 = parseInt(obj.rm_25);
														var rm_45 = parseInt(obj.rm_45);
														var rm_55 = parseInt(obj.rm_55);
														var rm_65 = parseInt(obj.rm_65);
														
														var revenue_male = [rm_17,rm_18,rm_25,rm_45,rm_55,rm_65];
														
														var rt_17 = parseInt(obj.rt_17);
														var rt_18 = parseInt(obj.rt_18);
														var rt_25 = parseInt(obj.rt_25);
														var rt_45 = parseInt(obj.rt_45);
														var rt_55 = parseInt(obj.rt_55);
														var rt_65 = parseInt(obj.rt_65);
														
														var revenue_total = [rt_17,rt_18,rt_25,rt_45,rt_55,rt_65];
														
														if(typeof loyalty_visitbyage_chart === 'undefined')
														{	
															console.log("not set");
															loyalty_visitbyage_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_age_container',
																	//plotBorderColor: 'rgb(69, 114, 167)',
																	//plotBorderWidth:2
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Age',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Customer Age'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#9BBB59',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#C0504D',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														}
														else
														{
															console.log("set");														
															loyalty_visitbyage_chart.series[0].setData(female_visit);
															loyalty_visitbyage_chart.series[1].setData(male_visit);
															loyalty_visitbyage_chart.series[2].setData(revenue_female);
															loyalty_visitbyage_chart.series[3].setData(revenue_male);
															loyalty_visitbyage_chart.series[4].setData(revenue_total);
														}
														jQuery(".loader_loyalty_age").css("display","none");
													}
													else
													{
														//alert(obj.message);
														jQuery(".loader_loyalty_age").css("display","none");
														jQuery(".nodata_loyalty_age").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
							//jQuery(".loader_loyalty_age").css("display","none");
						});
						
						
						jQuery("#opt_filter_days_time1").live("change",function(){
							
							jQuery(".nodata_loyalty_time").css("display","none");
							jQuery('#card_visit_by_time_container').css("display","none");
							jQuery('#card_visit_by_time_container').css("display","block");
							jQuery(".loader_loyalty_time").css("display","block");
							
							var opt_filter_days = jQuery(this).val();
							var opt_filter_location = jQuery("#opt_filter_location_time").val();
							//alert(opt_filter_days);
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										var card_id = jQuery("#hdncardid").val();
										var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_by_time=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_location="+opt_filter_location+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														jQuery('#card_visit_by_time_container').css("display","block");
															
														var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
														
														var f_1 = parseInt(obj.f_1);
														var f_2 = parseInt(obj.f_2);
														var f_3 = parseInt(obj.f_3);
														var f_4 = parseInt(obj.f_4);
														
														var female_visit = [f_1,f_2,f_3,f_4];
														
														var m_1 = parseInt(obj.m_1);
														var m_2 = parseInt(obj.m_2);
														var m_3 = parseInt(obj.m_3);
														var m_4 = parseInt(obj.m_4);
														
														var male_visit = [m_1,m_2,m_3,m_4];
														
														var rf_1 = parseInt(obj.rf_1);
														var rf_2 = parseInt(obj.rf_2);
														var rf_3 = parseInt(obj.rf_3);
														var rf_4 = parseInt(obj.rf_4);
														
														var revenue_female = [rf_1,rf_2,rf_3,rf_4];
														
														var rm_1 = parseInt(obj.rm_1);
														var rm_2 = parseInt(obj.rm_2);
														var rm_3 = parseInt(obj.rm_3);
														var rm_4 = parseInt(obj.rm_4);
														
														var revenue_male = [rm_1,rm_2,rm_3,rm_4];
														
														var rt_1 = parseInt(obj.rt_1);
														var rt_2 = parseInt(obj.rt_2);
														var rt_3 = parseInt(obj.rt_3);
														var rt_4 = parseInt(obj.rt_4);
														
														var revenue_total = [rt_1,rt_2,rt_3,rt_4];
														
														if(typeof loyalty_visitbytime_chart === 'undefined')
														{
															loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																		renderTo: 'card_visit_by_time_container'
																	},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														}
														else
														{
															loyalty_visitbytime_chart.series[0].setData(female_visit);
															loyalty_visitbytime_chart.series[1].setData(male_visit);
															loyalty_visitbytime_chart.series[2].setData(revenue_female);
															loyalty_visitbytime_chart.series[3].setData(revenue_male);
															loyalty_visitbytime_chart.series[4].setData(revenue_total);	
														}
														jQuery(".loader_loyalty_time").css("display","none");
													}
													else
													{
														//alert(obj.message);
														jQuery(".loader_loyalty_time").css("display","none");
														jQuery(".nodata_loyalty_time").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
						});
						
						jQuery("#opt_filter_location_time1").live("change",function(){
							
							jQuery(".nodata_loyalty_time").css("display","none");
							jQuery('#card_visit_by_time_container').css("display","none");
							jQuery('#card_visit_by_time_container').css("display","block");
							jQuery(".loader_loyalty_time").css("display","block");
							
							var opt_filter_location = jQuery(this).val();
							var opt_filter_days = jQuery("#opt_filter_days_time").val();
							//alert(opt_filter_days);
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										var card_id = jQuery("#hdncardid").val();
										var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_by_time=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_location="+opt_filter_location+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														jQuery('#card_visit_by_time_container').css("display","block");
														var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
														
														var f_1 = parseInt(obj.f_1);
														var f_2 = parseInt(obj.f_2);
														var f_3 = parseInt(obj.f_3);
														var f_4 = parseInt(obj.f_4);
														
														var female_visit = [f_1,f_2,f_3,f_4];
														
														var m_1 = parseInt(obj.m_1);
														var m_2 = parseInt(obj.m_2);
														var m_3 = parseInt(obj.m_3);
														var m_4 = parseInt(obj.m_4);
														
														var male_visit = [m_1,m_2,m_3,m_4];
														
														var rf_1 = parseInt(obj.rf_1);
														var rf_2 = parseInt(obj.rf_2);
														var rf_3 = parseInt(obj.rf_3);
														var rf_4 = parseInt(obj.rf_4);
														
														var revenue_female = [rf_1,rf_2,rf_3,rf_4];
														
														var rm_1 = parseInt(obj.rm_1);
														var rm_2 = parseInt(obj.rm_2);
														var rm_3 = parseInt(obj.rm_3);
														var rm_4 = parseInt(obj.rm_4);
														
														var revenue_male = [rm_1,rm_2,rm_3,rm_4];
														
														var rt_1 = parseInt(obj.rt_1);
														var rt_2 = parseInt(obj.rt_2);
														var rt_3 = parseInt(obj.rt_3);
														var rt_4 = parseInt(obj.rt_4);
														
														var revenue_total = [rt_1,rt_2,rt_3,rt_4];
														
														if(typeof loyalty_visitbytime_chart === 'undefined')
														{
															loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																		renderTo: 'card_visit_by_time_container'
																	},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														}
														else
														{
															loyalty_visitbytime_chart.series[0].setData(female_visit);
															loyalty_visitbytime_chart.series[1].setData(male_visit);
															loyalty_visitbytime_chart.series[2].setData(revenue_female);
															loyalty_visitbytime_chart.series[3].setData(revenue_male);
															loyalty_visitbytime_chart.series[4].setData(revenue_total);	
														}
														
														jQuery(".loader_loyalty_time").css("display","none");
														
													}
													else
													{
														//alert(obj.message);
														jQuery(".loader_loyalty_time").css("display","none");
														jQuery(".nodata_loyalty_time").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
						});
						
						
									
						jQuery("#opt_filter_days_age").live("change",function(){
							
							var opt_filter_days = jQuery(this).val();
							var opt_filter_location = jQuery("#opt_filter_location_age").val();
							//alert(opt_filter_days);
							var card_id = jQuery(this).attr("card_id");
							var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
										
							jQuery("#div_card_"+card_id+" .agechart .nodata_loyalty_age").css("display","none");
							jQuery('#card_visit_by_age_container_'+card_id).css("display","none");
							jQuery('#card_visit_by_age_container_'+card_id).css("display","block");
							jQuery("#div_card_"+card_id+" .agechart .loader_loyalty_age").css("display","block");
							
							
										
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_by_age=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_location="+opt_filter_location+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													console.log(obj.status);
													if (obj.status=="true") 
													{
														jQuery('#card_visit_by_age_container_'+card_id).css("display","block");
														var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
														
														var f_17 = parseInt(obj.f_17);
														var f_18 = parseInt(obj.f_18);
														var f_25 = parseInt(obj.f_25);
														var f_45 = parseInt(obj.f_45);
														var f_55 = parseInt(obj.f_55);
														var f_65 = parseInt(obj.f_65);
														
														var female_visit = [f_17,f_18,f_25,f_45,f_55,f_65];
														
														var m_17 = parseInt(obj.m_17);
														var m_18 = parseInt(obj.m_18);
														var m_25 = parseInt(obj.m_25);
														var m_45 = parseInt(obj.m_45);
														var m_55 = parseInt(obj.m_55);
														var m_65 = parseInt(obj.m_65);
														
														var male_visit = [m_17,m_18,m_25,m_45,m_55,m_65];
														
														var rf_17 = parseInt(obj.rf_17);
														var rf_18 = parseInt(obj.rf_18);
														var rf_25 = parseInt(obj.rf_25);
														var rf_45 = parseInt(obj.rf_45);
														var rf_55 = parseInt(obj.rf_55);
														var rf_65 = parseInt(obj.rf_65);
														
														var revenue_female = [rf_17,rf_18,rf_25,rf_45,rf_55,rf_65];
														
														var rm_17 = parseInt(obj.rm_17);
														var rm_18 = parseInt(obj.rm_18);
														var rm_25 = parseInt(obj.rm_25);
														var rm_45 = parseInt(obj.rm_45);
														var rm_55 = parseInt(obj.rm_55);
														var rm_65 = parseInt(obj.rm_65);
														
														var revenue_male = [rm_17,rm_18,rm_25,rm_45,rm_55,rm_65];
														
														var rt_17 = parseInt(obj.rt_17);
														var rt_18 = parseInt(obj.rt_18);
														var rt_25 = parseInt(obj.rt_25);
														var rt_45 = parseInt(obj.rt_45);
														var rt_55 = parseInt(obj.rt_55);
														var rt_65 = parseInt(obj.rt_65);
														
														var revenue_total = [rt_17,rt_18,rt_25,rt_45,rt_55,rt_65];
														
														if(typeof loyalty_visitbyage_chart === 'undefined')
														{	
															console.log("not set");
															loyalty_visitbyage_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_age_container_'+card_id,
																	//plotBorderColor: 'rgb(69, 114, 167)',
																	//plotBorderWidth:2
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Age',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Customer Age'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#9BBB59',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#C0504D',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														}
														else
														{
															console.log("set");														
															loyalty_visitbyage_chart.series[0].setData(female_visit);
															loyalty_visitbyage_chart.series[1].setData(male_visit);
															loyalty_visitbyage_chart.series[2].setData(revenue_female);
															loyalty_visitbyage_chart.series[3].setData(revenue_male);
															loyalty_visitbyage_chart.series[4].setData(revenue_total);
														}
														jQuery("#div_card_"+card_id+" .agechart .loader_loyalty_age").css("display","none");
														
														if(f_17==0 && f_18==0 && f_25==0 && f_45==0 && f_55==0 && f_65==0 && 
															m_17==0 && m_18==0 && m_25==0 && m_45==0 && m_55==0 && m_65==0 && 
															rf_17==0 && rf_18==0 && rf_25==0 && rf_45==0 && rf_55==0 && rf_65==0 && 
															rm_17==0 && rm_18==0 && rm_25==0 && rm_45==0 && rm_55==0 && rm_65==0 && 
															rt_17==0 && rt_18==0 && rt_25==0 && rt_45==0 && rt_55==0 && rt_65==0 )												
														{
															//jQuery('#div_card_'+card_id+' .agechart .nodata_loyalty_age').css("display","block");
															//jQuery('#card_visit_by_age_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .agechart .loyalty_card_metrics_filter').css("display","none");
															
																loyalty_visitbyage_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_age_container_'+card_id
																	//plotBorderColor: 'rgb(69, 114, 167)',
																	//plotBorderWidth:2
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Age',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Customer Age'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},																
																series: [{
																	name: 'Female Visit',
																	color: '#9BBB59',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Male Visit',
																	color: '#C0504D',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																}]
															});
														}
													}
													else
													{
														//alert(obj.message);
														jQuery("#div_card_"+card_id+" .agechart .loader_loyalty_age").css("display","none");
														jQuery("#div_card_"+card_id+" .agechart .nodata_loyalty_age").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
							//jQuery(".loader_loyalty_age").css("display","none");
						});
						
						jQuery("#opt_filter_location_age").live("change",function(){
							
							var opt_filter_location = jQuery(this).val();
							var opt_filter_days = jQuery("#opt_filter_days_age").val();
							//alert(opt_filter_days);
							var card_id = jQuery(this).attr("card_id");
							var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
							
							jQuery("#div_card_"+card_id+" .agechart .nodata_loyalty_age").css("display","none");
							jQuery('#card_visit_by_age_container_'+card_id).css("display","none");
							jQuery('#card_visit_by_age_container_'+card_id).css("display","block");
							jQuery("#div_card_"+card_id+" .agechart .loader_loyalty_age").css("display","block");
							
							
										
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_by_age=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_location="+opt_filter_location+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														jQuery('#card_visit_by_age_container_'+card_id).css("display","block");
														
														var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
														
														var f_17 = parseInt(obj.f_17);
														var f_18 = parseInt(obj.f_18);
														var f_25 = parseInt(obj.f_25);
														var f_45 = parseInt(obj.f_45);
														var f_55 = parseInt(obj.f_55);
														var f_65 = parseInt(obj.f_65);
														
														var female_visit = [f_17,f_18,f_25,f_45,f_55,f_65];
														
														var m_17 = parseInt(obj.m_17);
														var m_18 = parseInt(obj.m_18);
														var m_25 = parseInt(obj.m_25);
														var m_45 = parseInt(obj.m_45);
														var m_55 = parseInt(obj.m_55);
														var m_65 = parseInt(obj.m_65);
														
														var male_visit = [m_17,m_18,m_25,m_45,m_55,m_65];
														
														var rf_17 = parseInt(obj.rf_17);
														var rf_18 = parseInt(obj.rf_18);
														var rf_25 = parseInt(obj.rf_25);
														var rf_45 = parseInt(obj.rf_45);
														var rf_55 = parseInt(obj.rf_55);
														var rf_65 = parseInt(obj.rf_65);
														
														var revenue_female = [rf_17,rf_18,rf_25,rf_45,rf_55,rf_65];
														
														var rm_17 = parseInt(obj.rm_17);
														var rm_18 = parseInt(obj.rm_18);
														var rm_25 = parseInt(obj.rm_25);
														var rm_45 = parseInt(obj.rm_45);
														var rm_55 = parseInt(obj.rm_55);
														var rm_65 = parseInt(obj.rm_65);
														
														var revenue_male = [rm_17,rm_18,rm_25,rm_45,rm_55,rm_65];
														
														var rt_17 = parseInt(obj.rt_17);
														var rt_18 = parseInt(obj.rt_18);
														var rt_25 = parseInt(obj.rt_25);
														var rt_45 = parseInt(obj.rt_45);
														var rt_55 = parseInt(obj.rt_55);
														var rt_65 = parseInt(obj.rt_65);
														
														var revenue_total = [rt_17,rt_18,rt_25,rt_45,rt_55,rt_65];
														
														if(typeof loyalty_visitbyage_chart === 'undefined')
														{	
															console.log("not set");
															loyalty_visitbyage_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_age_container_'+card_id,
																	//plotBorderColor: 'rgb(69, 114, 167)',
																	//plotBorderWidth:2
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Age',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Customer Age'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#9BBB59',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#C0504D',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														}
														else
														{
															console.log("set");														
															loyalty_visitbyage_chart.series[0].setData(female_visit);
															loyalty_visitbyage_chart.series[1].setData(male_visit);
															loyalty_visitbyage_chart.series[2].setData(revenue_female);
															loyalty_visitbyage_chart.series[3].setData(revenue_male);
															loyalty_visitbyage_chart.series[4].setData(revenue_total);
														}
														jQuery("#div_card_"+card_id+" .agechart .loader_loyalty_age").css("display","none");
														
														if(f_17==0 && f_18==0 && f_25==0 && f_45==0 && f_55==0 && f_65==0 && 
															m_17==0 && m_18==0 && m_25==0 && m_45==0 && m_55==0 && m_65==0 && 
															rf_17==0 && rf_18==0 && rf_25==0 && rf_45==0 && rf_55==0 && rf_65==0 && 
															rm_17==0 && rm_18==0 && rm_25==0 && rm_45==0 && rm_55==0 && rm_65==0 && 
															rt_17==0 && rt_18==0 && rt_25==0 && rt_45==0 && rt_55==0 && rt_65==0 )												
														{
															//jQuery('#div_card_'+card_id+' .agechart .nodata_loyalty_age').css("display","block");
															//jQuery('#card_visit_by_age_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .agechart .loyalty_card_metrics_filter').css("display","none");
															
																loyalty_visitbyage_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_age_container_'+card_id
																	//plotBorderColor: 'rgb(69, 114, 167)',
																	//plotBorderWidth:2
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Age',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Customer Age'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},																
																series: [{
																	name: 'Female Visit',
																	color: '#9BBB59',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Male Visit',
																	color: '#C0504D',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																}]
															});
														}
													}
													else
													{
														//alert(obj.message);
														jQuery("#div_card_"+card_id+" .agechart .loader_loyalty_age").css("display","none");
														jQuery("#div_card_"+card_id+" .agechart .nodata_loyalty_age").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
							//jQuery(".loader_loyalty_age").css("display","none");
						});
						
						
						jQuery("#opt_filter_days_time").live("change",function(){
							
							var opt_filter_days = jQuery(this).val();
							var opt_filter_location = jQuery("#opt_filter_location_time").val();
							//alert(opt_filter_days);
							var card_id = jQuery(this).attr("card_id");
							var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
							
							jQuery("#div_card_"+card_id+" .timechart .nodata_loyalty_time").css("display","none");
							jQuery('#card_visit_by_time_container_'+card_id).css("display","none");
							jQuery('#card_visit_by_time_container_'+card_id).css("display","block");
							jQuery("#div_card_"+card_id+" .timechart .loader_loyalty_time").css("display","block");
							
							
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_by_time=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_location="+opt_filter_location+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														jQuery('#card_visit_by_time_container_'+card_id).css("display","block");
															
														var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
														
														var f_1 = parseInt(obj.f_1);
														var f_2 = parseInt(obj.f_2);
														var f_3 = parseInt(obj.f_3);
														var f_4 = parseInt(obj.f_4);
														
														var female_visit = [f_1,f_2,f_3,f_4];
														
														var m_1 = parseInt(obj.m_1);
														var m_2 = parseInt(obj.m_2);
														var m_3 = parseInt(obj.m_3);
														var m_4 = parseInt(obj.m_4);
														
														var male_visit = [m_1,m_2,m_3,m_4];
														
														var rf_1 = parseInt(obj.rf_1);
														var rf_2 = parseInt(obj.rf_2);
														var rf_3 = parseInt(obj.rf_3);
														var rf_4 = parseInt(obj.rf_4);
														
														var revenue_female = [rf_1,rf_2,rf_3,rf_4];
														
														var rm_1 = parseInt(obj.rm_1);
														var rm_2 = parseInt(obj.rm_2);
														var rm_3 = parseInt(obj.rm_3);
														var rm_4 = parseInt(obj.rm_4);
														
														var revenue_male = [rm_1,rm_2,rm_3,rm_4];
														
														var rt_1 = parseInt(obj.rt_1);
														var rt_2 = parseInt(obj.rt_2);
														var rt_3 = parseInt(obj.rt_3);
														var rt_4 = parseInt(obj.rt_4);
														
														var revenue_total = [rt_1,rt_2,rt_3,rt_4];
														
														if(typeof loyalty_visitbytime_chart === 'undefined')
														{
															loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																		renderTo: 'card_visit_by_time_container_'+card_id
																	},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														}
														else
														{
															loyalty_visitbytime_chart.series[0].setData(female_visit);
															loyalty_visitbytime_chart.series[1].setData(male_visit);
															loyalty_visitbytime_chart.series[2].setData(revenue_female);
															loyalty_visitbytime_chart.series[3].setData(revenue_male);
															loyalty_visitbytime_chart.series[4].setData(revenue_total);	
														}
														jQuery("#div_card_"+card_id+" .timechart .loader_loyalty_time").css("display","none");
														if(f_1==0 && f_2==0 && f_3==0 && f_4==0 && 
															m_1==0 && m_2==0 && m_3==0 && m_4==0 && 
															rf_1==0 && rf_2==0 && rf_3==0 && rf_4==0 && 
															rt_1==0 && rt_2==0 && rt_3==0 && rt_4==0 )												
														{
															//jQuery('#div_card_'+card_id+' .timechart .nodata_loyalty_time').css("display","block");
															//jQuery('#card_visit_by_time_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .timechart .loyalty_card_metrics_filter').css("display","none");
															
															loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_time_container_'+card_id
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},																
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																}]
															});
														}
													}
													else
													{
														//alert(obj.message);
														jQuery("#div_card_"+card_id+" .timechart .loader_loyalty_time").css("display","none");
														jQuery("#div_card_"+card_id+" .timechart .nodata_loyalty_time").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
						});
						
						jQuery("#opt_filter_location_time").live("change",function(){
							
							var opt_filter_location = jQuery(this).val();
							var opt_filter_days = jQuery("#opt_filter_days_time").val();
							//alert(opt_filter_days);
							var card_id = jQuery(this).attr("card_id");
							var merchant_id = '<?php echo $_SESSION['merchant_id'] ?>';
							
							jQuery("#div_card_"+card_id+" .timechart .nodata_loyalty_time").css("display","none");
							jQuery('#card_visit_by_time_container_'+card_id).css("display","none");
							jQuery('#card_visit_by_time_container_'+card_id).css("display","block");
							jQuery("#div_card_"+card_id+" .timechart .loader_loyalty_time").css("display","block");
							
							
							
							jQuery.ajax({
								type:"POST",
								url:'process.php',
								data :'loginornot=true',
								async:false,
								success:function(msg)
								{
									var obj = jQuery.parseJSON(msg);
									if (obj.status=="false")     
									{
										window.location.href=obj.link;
									}
									else
									{
									
										
											jQuery.ajax({
												  type: "POST", // HTTP method POST or GET
												  url: "card_report.php", //Where to make Ajax calls
												  data:"btn_filter_loyalty_by_time=yes&card_id="+card_id+"&merchant_id="+merchant_id+"&opt_filter_location="+opt_filter_location+"&opt_filter_days="+opt_filter_days,
												  success:function(response){
													
													var obj = jQuery.parseJSON(response);
													if (obj.status=="true") 
													{
														jQuery('#card_visit_by_time_container_'+card_id).css("display","block");
														var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
														
														var f_1 = parseInt(obj.f_1);
														var f_2 = parseInt(obj.f_2);
														var f_3 = parseInt(obj.f_3);
														var f_4 = parseInt(obj.f_4);
														
														var female_visit = [f_1,f_2,f_3,f_4];
														
														var m_1 = parseInt(obj.m_1);
														var m_2 = parseInt(obj.m_2);
														var m_3 = parseInt(obj.m_3);
														var m_4 = parseInt(obj.m_4);
														
														var male_visit = [m_1,m_2,m_3,m_4];
														
														var rf_1 = parseInt(obj.rf_1);
														var rf_2 = parseInt(obj.rf_2);
														var rf_3 = parseInt(obj.rf_3);
														var rf_4 = parseInt(obj.rf_4);
														
														var revenue_female = [rf_1,rf_2,rf_3,rf_4];
														
														var rm_1 = parseInt(obj.rm_1);
														var rm_2 = parseInt(obj.rm_2);
														var rm_3 = parseInt(obj.rm_3);
														var rm_4 = parseInt(obj.rm_4);
														
														var revenue_male = [rm_1,rm_2,rm_3,rm_4];
														
														var rt_1 = parseInt(obj.rt_1);
														var rt_2 = parseInt(obj.rt_2);
														var rt_3 = parseInt(obj.rt_3);
														var rt_4 = parseInt(obj.rt_4);
														
														var revenue_total = [rt_1,rt_2,rt_3,rt_4];
														
														if(typeof loyalty_visitbytime_chart === 'undefined')
														{
															loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																		renderTo: 'card_visit_by_time_container_'+card_id
																	},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},
																/*
																plotOptions: {
																	series: {
																		stacking: 'normal'
																	}
																},
																*/
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: female_visit
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: male_visit
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_female
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_male
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: revenue_total
																}]
															});
														}
														else
														{
															loyalty_visitbytime_chart.series[0].setData(female_visit);
															loyalty_visitbytime_chart.series[1].setData(male_visit);
															loyalty_visitbytime_chart.series[2].setData(revenue_female);
															loyalty_visitbytime_chart.series[3].setData(revenue_male);
															loyalty_visitbytime_chart.series[4].setData(revenue_total);	
														}
														
														jQuery("#div_card_"+card_id+" .timechart .loader_loyalty_time").css("display","none");
														
														if(f_1==0 && f_2==0 && f_3==0 && f_4==0 && 
															m_1==0 && m_2==0 && m_3==0 && m_4==0 && 
															rf_1==0 && rf_2==0 && rf_3==0 && rf_4==0 && 
															rt_1==0 && rt_2==0 && rt_3==0 && rt_4==0 )												
														{
															//jQuery('#div_card_'+card_id+' .timechart .nodata_loyalty_time').css("display","block");
															//jQuery('#card_visit_by_time_container_'+card_id).css("display","none");
															//jQuery('#div_card_'+card_id+' .timechart .loyalty_card_metrics_filter').css("display","none");
															
															loyalty_visitbytime_chart = new Highcharts.Chart({
																chart: {
																	renderTo: 'card_visit_by_time_container_'+card_id
																},
																title: {
																	text: 'Customer Visit & Revenue Metrics By Time',
																	x: -20 //center
																},
																subtitle: {
																	text: '',
																	x: -20
																},
																credits: {
																	enabled: false
																},
																xAxis:{
																	title: {
																		text: 'Time'
																	},
																	categories: categories
																},
																yAxis: [{ // Primary yAxis
																	   title: {
																		   text: 'Customer Visits'
																	   },
																	   min: 0,
																	   gridLineWidth: 0,
																	   lineWidth:1
																  }, { // Secondary yAxis
																	title: {
																		text: 'Revenue'
																	},
																	  min: 0,
																	  gridLineWidth: 0,
																	  lineWidth:1,
																	opposite: true
																}],       
																legend: {
																	layout: 'vertical',
																	align: 'right',
																	verticalAlign: 'middle',
																	borderWidth: 0
																},																
																series: [{
																	name: 'Female Visit',
																	color: '#7E629F',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Male Visit',
																	color: '#9ABA59',
																	type: 'column',
																	yAxis: 0,
																	data: []
																},{
																	name: 'Revenue Female',
																	color: '#F59240',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Revenue Male',
																	color: '#46AAC4',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																},{
																	name: 'Total Revenue',
																	color: '#7D5FA0',
																	type: 'spline',
																	yAxis: 1,
																	data: []
																}]
															});
														}
													}
													else
													{
														//alert(obj.message);
														jQuery("#div_card_"+card_id+" .timechart .loader_loyalty_time").css("display","none");
														jQuery("#div_card_"+card_id+" .timechart .nodata_loyalty_time").css("display","block");
													}	
													
												  },
												  error:function (xhr, ajaxOptions, thrownError){
													  alert(thrownError);
												  }
											});
										
									}
								}
							});
						});
						
						
						
						
						jQuery(document).on("click", "#close_card", function () {
							//alert("In close");

							jQuery(".loyaltycard_report").css("display", "none");

							jQuery("#sidemenu li a").each(function () {
								jQuery(this).removeClass("current");
							});
							jQuery("li.loyalty_report a").addClass("current");
							var cls = jQuery(this).parent().attr("class");
							jQuery(".tabs").each(function () {
								jQuery(this).css("display", "none");
							});

							jQuery('#loyalty_report').css("display", "block");

						});
		
		
						// end for loyalty cards
		
		            $("#btnfilterlocationdata").on("click", function () {

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
		                            open_loader();
		                            //jQuery("#upload_business_logo_ajax1").show();
		                            var year = $("#location_select_2").val();
		                            var month = $("#location_select_1").val();
		                            var status = $("#location_select_0").val();
		                            $.ajax({
		                                type: "POST",
		                                url: 'process.php',
		                                data: 'mer_id=' +<?php echo $_SESSION['merchant_id'] ?> + '&get_filter_locationwie_report=true&year=' + year + '&month=' + month + '&status=' + status,
		                                async: true,
		                                success: function (msg)
		                                {
		                                    //  alert(msg)   ;
		                                    $("#div_location").html(msg);

		                                    bind_event();
		                                    close_loader();
		                                    //jQuery("#upload_business_logo_ajax1").hide();
		                                }
		                            });
		                        }
		                    }
		                });
		            });
		        });

		        $("#btnfilterqrcodedata").on("click", function () {
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
		                        //open_popup('Notification');
		                        //jQuery("#upload_business_logo_ajax1").show();
		                        open_popup();
		                        var year = $("#qrcodescan_select_2").val();
		                        //      alert('process.php?mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&get_filter_qrcodewie_report=true&year='+year+'&status='+status);
		                        $.ajax({
		                            type: "POST",
		                            url: 'process.php',
		                            data: 'mer_id=' +<?php echo $_SESSION['merchant_id'] ?> + '&get_filter_qrcodewie_report=true&year=' + year + '&status=' + status,
		                            async: true,
		                            success: function (msg)
		                            {
		                                //alert(msg);

		                                $("#div_qrcodes").html(msg);
		                                //$("#qrcode_report").css('display','block');  
		                                bind_event();
		                                close_popup();
		                                //jQuery("#upload_business_logo_ajax1").hide();
		                            }
		                        });
		                        bind_label_event();
		                    }
		                }
		            });


		        });
		</script>
		<script>

		        $(document).ready(function () {
		            var hashval = window.location.hash;
		            if (hashval.length != 0)
		            {
		                $('li.statistics_report a').trigger('click');
		                window.location.hash = '';
		            }

		        });
		</script>
		<script language="javascript">
		        function paginate(counter, demo_div, report_div) {
		            var cnt = counter;
		            cnt = Math.ceil(parseInt(cnt) - 1);

		            if (cnt > 1)
		            {

		                jQuery('#' + demo_div).paginate({
		                    count: cnt,
		                    start: 1,
		                    display: 3,
		                    border: true,
		                    border_color: '#BFBFBF',
		                    text_color: 'black',
		                    background_color: 'white',
		                    border_hover_color: 'black',
		                    text_hover_color: '#000',
		                    background_hover_color: '#fff',
		                    images: false,
		                    mouse: 'press',
		                    onChange: function (page) {

		                        jQuery('._current', '#' + report_div).removeClass('_current').hide();
		                        jQuery('#' + report_div + ' #p' + page).addClass('_current').show();
		                    }
		                });
		            }
		        }


		        $("div[id^='toggle-']").toggle(function () {

		            var a = $(this).attr("id").split("-");
		            $("#" + a[1]).slideUp("fast");
		            $(this).removeClass('mainIcon_minus');
		            $(this).next("span").text("Exapnd");

		        },
		                function () {

		                    var a = $(this).attr("id").split("-");
		                    $("#" + a[1]).slideDown("slow");

		                    $(this).addClass('mainIcon_minus');
		                    $(this).next("span").text("Collapse");

		                }
		        );



		        var location_gender_chart_container = [];
		        var location_age_chart_container = [];
		        var location_cmapaign_chart_container = [];
		        var qrcode_campaignscan_container;
		        var qrcode_loactionscan_container;

		        var campaign_genger_chart_container;
		        var campaign_age_chart_container;
		        var campaign_share_chart_container;
		        var campaign_view_chart_container;
		        var campaign_rating_chart_container;

		        var campaign_loc_genger_chart_container = [];
		        var campaign_loc_age_chart_container = [];
		        var campaign_loc_share_chart_container = [];
		        var campaign_loc_view_chart_container = [];
		        var campaign_loc_rating_chart_container = [];

		        $("#sidemenu li#tab-type a").click(function () {

		            $("#sidemenu li a").each(function () {
		                $(this).removeClass("current");
		            });
		            $(this).addClass("current");
		            var cls = $(this).parent().attr("class");
		            $(".tabs").each(function () {
		                $(this).css("display", "none");
		            });
		            //draw_location_chart();

		            $('#' + cls).css("display", "block");



		            //$(window).resize();
		            /*** load campaign report on selection of campaign  tab to slove responsivechart problem ***/
		            if (cls == "div_location")
		            {
		                for (var prop in location_gender_chart_container) {

		                    if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                    {
		                        width = jQuery("#container_" + prop).width();
		                        height = jQuery("#container_" + prop).height();
		                        var p = parseInt(prop);
		                        location_gender_chart_container[p].setSize(width, height, doAnimation = true);
		                    }
		                }
		                /*** for age ***/
		                for (var prop in location_age_chart_container) {

		                    if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                    {
		                        width = jQuery("#containerage_" + prop).width();
		                        height = jQuery("#containerage_" + prop).height();
		                        var p = parseInt(prop);
		                        location_age_chart_container[p].setSize(width, height, doAnimation = true);
		                    }
		                }
		                /**** for campaign report ***/
		                for (var prop in location_cmapaign_chart_container) {

		                    if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                    {
		                        width = jQuery("#container_report_" + prop).width();
		                        height = jQuery("#container_report_" + prop).height();
		                        var p = parseInt(prop);
		                        location_cmapaign_chart_container[p].setSize(width, height, doAnimation = true);
		                    }
		                }

		                bind_locationdetail_event();

		            }
		            if (cls == "campaign_report")
		            {
						
		                /**** for gender report ***/
		                for (var prop in campaign_loc_genger_chart_container) {
		                    if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                    {
		                        width = jQuery("#locationidgender_" + prop).width();
		                        height = jQuery("#locationidgender_" + prop).height();
		                        var p = parseInt(prop);
		                        campaign_loc_genger_chart_container[p].setSize(width, height, doAnimation = true);
		                    }
		                }
		                /**** for age report ***/
		                for (var prop in campaign_loc_age_chart_container) {
		                    if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                    {
		                        width = jQuery("#containeragewisegender_" + prop).width();
		                        height = jQuery("#containeragewisegender_" + prop).height();
		                        var p = parseInt(prop);
		                        campaign_loc_age_chart_container[p].setSize(width, height, doAnimation = true);
		                    }
		                }
		                /**** for share report ***/
		                for (var prop in campaign_loc_share_chart_container) {
		                    if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                    {
		                        width = jQuery("#summerisedshare_" + prop).width();
		                        height = jQuery("#summerisedshare_" + prop).height();
		                        var p = parseInt(prop);
		                        campaign_loc_share_chart_container[p].setSize(width, height, doAnimation = true);
		                    }
		                }

		                /**** for view report ***/
		                for (var prop in campaign_loc_view_chart_container) {
		                    if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                    {
		                        width = jQuery("#summerisedview_" + prop).width();
		                        height = jQuery("#summerisedview_" + prop).height();
		                        var p = parseInt(prop);
		                        campaign_loc_view_chart_container[p].setSize(width, height, doAnimation = true);
		                    }
		                }
		                /**** For rating report ***/
		                for (var prop in campaign_loc_rating_chart_container) {
		                    if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                    {
		                        width = jQuery("#summerisedrating_" + prop).width();
		                        height = jQuery("#summerisedrating_" + prop).height();
		                        var p = parseInt(prop);
		                        campaign_loc_rating_chart_container[p].setSize(width, height, doAnimation = true);
		                    }
		                }
		                /*** for summeriesed data ***/
		                /*
		                if (jQuery("#summerisedidgender").length != 0)
		                {
		                    width = jQuery("#summerisedidgender").width();
		                    height = jQuery("#summerisedidgender").height();
		                    campaign_genger_chart_container.setSize(width, height, doAnimation = true);
		                }
		                if (jQuery("#summerisedidage").length != 0)
		                {
		                    width = jQuery("#summerisedidage").width();
		                    height = jQuery("#summerisedidage").height();
		                    campaign_age_chart_container.setSize(width, height, doAnimation = true);
		                }
		                if (jQuery("#summerisedshare").length != 0)
		                {
		                    width = jQuery("#summerisedshare").width();
		                    height = jQuery("#summerisedshare").height();
		                    campaign_share_chart_container.setSize(width, height, doAnimation = true);
		                }
		                if (jQuery("#summerisedview").length != 0)
		                {
		                    width = jQuery("#summerisedview").width();
		                    height = jQuery("#summerisedview").height();
		                    campaign_view_chart_container.setSize(width, height, doAnimation = true);
		                }
		                if (jQuery("#summerisedrating").length != 0)
		                {
		                    width = jQuery("#summerisedrating").width();
		                    height = jQuery("#summerisedrating").height();
		                    campaign_rating_chart_container.setSize(width, height, doAnimation = true);
		                } 
						*/
		            }
		            /*** load qrcode report on selection of qrcode sacn tab to slove responsivechart problem ***/
		            if (cls == "qrcode_report")
		            {

		                // Load loader and call report only if scan report is not loaded before 
		                if (jQuery("#container_qrcodecampaignreport").html() == "")
		                {
		                    open_loader();
		                    /* for location */
		                    if (jQuery("#hdn_employee_location").val() == "")
		                    {
		                        var location_list = getCookie("selected_location_list_location_<?php echo $_SESSION['merchant_id'] ?>");
		                        if (location_list != null && location_list != "")
		                        {
		                            selected_locations1 = getCookie("selected_location_list_location_<?php echo $_SESSION['merchant_id'] ?>");
		                        }
		                        else
		                        {
		                            selected_locations1 = "";
		                        }
		                    }
		                    else
		                    {
		                        selected_locations1 = jQuery("#hdn_employee_location").val();
		                    }
		                    if (selected_locations1 != "")
		                    {
		                        var arr_v1 = selected_locations1.split(";");
		                        if (arr_v1.length != 1)
		                        {
		                            default_qrcode_location_report(selected_locations1);
		                        }
		                        else
		                        {
		                            single_location_qrcode_report(selected_locations1);
		                        }
		                        close_loader();
		                    }
		                    else
		                    {
		                        jQuery.ajax({
		                            type: "POST",
		                            url: 'process.php',
		                            data: 'check_locations_locationreport=1&year=2013&month=0&selected_locations=&mer_id=<?php echo $_SESSION['merchant_id'] ?>',
		                            async: true,
		                            success: function (msg)
		                            {
		                                var obj = jQuery.parseJSON(msg);
		                                if (obj.total_locations == 1)
		                                {
		                                    single_location_qrcode_report(obj.location_id);
		                                }
		                                else
		                                {
		                                    default_qrcode_location_report(selected_locations1);
		                                }
		                                bind_locationdetail_event();
		                                close_loader();
		                            }
		                        });
		                    }

		                    //	  alert(  jQuery(".highcharts-axis-labels .lbl_qrcode-location").length + "=== location length");

		                    jQuery(document).on("click", ".highcharts-axis-labels .lbl_qrcode-location", function () {
		                        if (jQuery(this).text() == "Jan" || jQuery(this).text() == "Feb" || jQuery(this).text() == "Mar" || jQuery(this).text() == "Apr" || jQuery(this).text() == "May" || jQuery(this).text() == "Jun" || jQuery(this).text() == "Jul" || jQuery(this).text() == "Aug" || jQuery(this).text() == "Sep" || jQuery(this).text() == "Oct" || jQuery(this).text() == "Nov" || jQuery(this).text() == "Dec")
		                        {
		                            var arr_val = jQuery(this).attr("val").split("-");
		                            jQuery("#Notification2PopUpContainer #spanp1").html("Total Scans By Male");
		                            jQuery("#Notification2PopUpContainer #span1").html(arr_val[1] + "%");
		                            jQuery("#Notification2PopUpContainer #spanp2").html("Total Scans By Female");
		                            jQuery("#Notification2PopUpContainer #span2").html(arr_val[2] + "%");
		                            jQuery("#Notification2PopUpContainer #spanp3").html("Total Scans By Unknown");
		                            jQuery("#Notification2PopUpContainer #span3").html(arr_val[3].toString() + "%");
		                            jQuery("#Notification2PopUpContainer #spanp4").html("<?php echo $merchant_msg['report']['total_num_unique_scan']; ?>");
		                            jQuery("#Notification2PopUpContainer #span4").html(arr_val[4].toString());
		                            jQuery("#Notification2PopUpContainer #spanp5").html("<?php echo $merchant_msg['report']['total_num_scans']; ?>");
		                            jQuery("#Notification2PopUpContainer #span5").html(arr_val[5].toString());
		                            jQuery.fancybox({
		                                content: jQuery('#Notification2mainContainer').html(),
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
		                                beforeShow: function () {
		                                    jQuery(".fancybox-inner").css("overflow", "hidden");
		                                },
		                                afterShow: function () {
		                                    jQuery(".fancybox-inner").css("overflow", "hidden");
		                                },
		                            });
		                            //open_popup('Notification1');
		                        }
		                    });

		                    /* for location */
		                    /* for campaign */
		                    var year = jQuery("#qrcodescan_select_2").val();
		                    if (jQuery("#hdn_employee_location").val() == "")
		                    {
		                        var location_list = getCookie("selected_location_list_<?php echo $_SESSION['merchant_id'] ?>");
		                        //alert("location_list = "+location_list);
		                        if (location_list != null && location_list != "")
		                        {
		                            //alert("if");
		                            selected_locations = getCookie("selected_location_list_<?php echo $_SESSION['merchant_id'] ?>");
		                        }
		                        else
		                        {
		                            //alert("else");
		                            selected_locations = "";
		                        }
		                    }
		                    else
		                    {
		                        selected_locations = jQuery("#hdn_employee_location").val();
		                    }

		                    //alert("selected_locations = "+selected_locations);

		                    if (selected_locations != "")
		                    {
		                        var arr_v1 = selected_locations.split(";");

		                        //alert("arr_v1.length = "+arr_v1.length);
		                        if (arr_v1.length != 1)
		                        {
		                            default_qrcode_campaign_report(selected_locations);
		                        }
		                        else
		                        {
		                            single_campaign_qrcode_report(selected_locations);
		                        }
		                        close_loader();
		                    }
		                    else
		                    {
		                        jQuery.ajax({
		                            type: "POST",
		                            url: 'process.php',
		                            data: 'check_locations_report=1&year=' + year + '&month=0&selected_locations=&mer_id=<?php echo $_SESSION['merchant_id'] ?>',
		                            async: true,
		                            success: function (msg)
		                            {
		                                var obj = jQuery.parseJSON(msg);
		                                if (obj.total_locations == 1)
		                                {
		                                    single_campaign_qrcode_report(obj.location_id);
		                                }
		                                else
		                                {
		                                    default_qrcode_campaign_report(selected_locations);
		                                }
		                                close_loader();
		                            }
		                        });
		                        //default_qrcode_campaign_report(selected_locations);
		                    }
		                    /* for campaign */
		                    bind_label_event();
		                }
		                else
		                {
		                    if (jQuery("#container_qrcodecampaignreport").length != 0)
		                    {
		                        width = jQuery("#container_qrcodecampaignreport").width();
		                        height = jQuery("#container_qrcodecampaignreport").height();
		                        qrcode_campaignscan_container.setSize(width, height, doAnimation = true);
		                    }
		                    if (jQuery("#container_qrcodecampaignreport").length != 0)
		                    {
		                        width = jQuery("#container_qrcodelocationreport").width();
		                        height = jQuery("#container_qrcodelocationreport").height();
		                        qrcode_loactionscan_container.setSize(width, height, doAnimation = true);
		                    }
		                    bind_label_event();
		                }



		            }
					console.log(cls);
					if (cls == "loyalty_report")
					{
						 $('#loyalty_report_form').slideDown(500);
					}
		        });

		        // end for anlytic reports //
		        
		        // start for loyalty reports //
		        
				function bind_loyaltycard_report_tab_event()
				{
					 $("#sidemenu_loyaltyreport li#tab-type a").click(function () {

						$("#sidemenu_loyaltyreport li a").each(function () {
							$(this).removeClass("current");
						});
						$(this).addClass("current");
						var cls = $(this).parent().attr("class");
						$(".tabs1").each(function () {
							$(this).css("display", "none");
						});
						//draw_location_chart();

						$('#' + cls).css("display", "block");
						console.log(cls);
						//if (cls == "loyalty_report")
						//{
							// $('#loyalty_report_form').slideDown(500);
						//}
					});
				}
				
				bind_loyaltycard_report_tab_event();
				
		        // end for loyalty reports //
		        
		        // add new tab in loyalty report
		        
		        jQuery(document).on("click","#add_tab",function() {
					
					jQuery("#media-upload-header_loyaltyreport").css("display","block");
					jQuery(".inner_tabdiv_loyaltyreport").css("display","block");
					
					jQuery("#sidemenu_loyaltyreport li").each(function () { // remove current class from all anchor
						jQuery(this).find("a").removeClass("current");
					});
					jQuery(".inner_tabdiv_loyaltyreport .tabs1").each(function () { // display none all loyalty report 
						jQuery(this).css("display", "none");
					});	
						
					var html_li ='<li id="tab-type" class="div_card_3"><a class="current">card 3</a><span class="loyaltyreport_close">X</span></li>';
					jQuery("#sidemenu_loyaltyreport").append(html_li);
					
					var html_div='<div id="div_card_3" class="tabs1" style="display: block;">div_card_3</div>';
					jQuery(".inner_tabdiv_loyaltyreport").append(html_div);
					
					bind_loyaltycard_report_tab_event();
				});
		        
		        // loyalty card report close event
		        
		        jQuery(document).on("click",".loyaltyreport_close",function() {
					var div_ele = jQuery(this).parent(); // get li tag
					console.log(div_ele);
					var a_ele = jQuery(this).prev(); // get anchor tag
					console.log(a_ele);
					
					var div_name = jQuery(div_ele).attr("class"); // get div tag name
					console.log(div_name);
					jQuery("#"+div_name).remove(); // remove div tag
					jQuery(div_ele).remove(); // remove li tag
					
					if(jQuery(a_ele).hasClass("current"))
					{
						if(jQuery("#sidemenu_loyaltyreport li").length==0)
						{
							jQuery("#media-upload-header_loyaltyreport").css("display","none");
							jQuery(".inner_tabdiv_loyaltyreport").css("display","none");
						}
						else
						{
							var first_li=jQuery("#sidemenu_loyaltyreport li:nth-child(1)"); // get first li
							var first_anchor=jQuery("#sidemenu_loyaltyreport li:nth-child(1) a"); // get first anchor
							jQuery(first_anchor).addClass("current");
							var div_name=jQuery(first_li).attr("class"); // get div name
							jQuery("#"+div_name).css("display","block");
						}	
						
						
					}
					
					
				});
		         
		        jQuery("a#report").css("background-color", "orange");
		        jQuery("#report_option").change(function () {
		            window.location.href = "<?= WEB_PATH ?>/merchant/reports.php?action=active";
		        });
		</script>



	    </div>
	    <!--end of contentContainer--></div>

	<!---------start footer--------------->
	<div>
	    <?php
	    require_once(MRCH_LAYOUT . "/footer.php");
	    ?>
	    <!--end of footer--></div>

	</div>
	<script>

		$("#btn_load_GA").click(function () {
		    //open_popup('Notification');
		    jQuery("#upload_business_logo_ajax1").show();
		    $.ajax({
		        type: "POST",
		        url: 'filter_report_by_year.php',
		        data: 'loadGAReport=true&month=' + $("#ga_select_1").val() + '&year=' + $("#ga_select_2").val(),
		        success: function (msg)
		        {

		        }
		    });

		});


	</script>
	<div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
	    <div id="NotificationBackDiv" class="divBack">
	    </div>
	    <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">

		<div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading"
		     >

		    <div id="NotificationmainContainer" class="innerContainer" style="height:auto;width:auto">
		        <img src="<?= ASSETS_IMG ?>/loading.gif" style="display: block;" id="image_loader_div"/>
		    </div>
		</div>
	    </div>
	</div>

	<div id="Notification2PopUpContainer" class="container_popup"  style="display: none;">
	    <div id="Notification2BackDiv" class="divBack">
	    </div>
	    <div id="Notification2FrontDivProcessing" class="Processing" style="display:none;">

		<div id="Notification2MaindivLoading" align="center" valign="middle" class="imgDivLoading"
		     >

		    <div class="modal-close-button" style="visibility: visible;">

		        <a  tabindex="0" onclick="close_popup('Notification2');" id="fancybox-close" style="display: inline;"></a>
		    </div>
		    <div id="Notification2mainContainer" class="innerContainer qrcodereport_detail" >
		        <div class="main_content qrcodereport_detail"> 	
		            <div class="head_msg">
		                QR Code Scan Report Detail
		            </div>
		            <!--<div class="message-box message-success" id="jqReviewHelpfulMessageNotification" > -->
		            <div class="campaign_detail_div" style="">
		                <div class="new_loca_eta1"  >
		                    <div class="qrcode_location_heading" ></div>

		                    <p ><span id="spanp1"></span> : <span id="span1"> </span></p>
		                    <p ><span id="spanp2"></span>  : <span id="span2">  </span></p>
		                    <p ><span id="spanp3"></span> :<span id="span3"> </span></p>
		                    <p ><span id="spanp4"></span>  : <span id="span4">  </span></p>
		                    <p ><span id="spanp5"></span>  : <span id="span5">  </span></p>
		                </div>	
		            </div>



		            <!-- </div> -->

		        </div>
		    </div>
		</div>
	    </div> 
	</div>

	<div id="Notification1PopUpContainer" class="container_popup"  >
	    <div id="Notification1BackDiv" class="divBack">
	    </div>
	    <div id="Notification1FrontDivProcessing" class="Processing" style="display:none;">

		<div id="Notification1MaindivLoading" align="center" valign="middle" class="imgDivLoading"
		     >

		    <div class="modal-close-button" style="visibility: visible;">

		        <a  tabindex="0" onclick="close_popup('Notification1');" id="fancybox-close" style="display: inline;"></a>
		    </div>
		    <div id="Notification1mainContainer" class="innerContainer qrcodereport_detail" >
		        <div class="main_content location_summary_report" > 	

		            <div class='head_msg'>
		                Location Campaigns Summary By Month
		            </div>

		            <div class="campaign_detail_div" style="">
		                <div class="new_loca_eta1" id="plus_<?php echo $Row->id ?>"  >			 

		                    <p >Total Number Of Active Campaigns : <span id="span1"> </span></p>
		                    <p >Total Offer Issued : <span id="span6">  </span></p>
		                    <p >Total Offers Redeemed:<span id="span5"> </span></p>
		                    <p >Total Revenue : <span id="span2">  </span></p>
		                    <p >Total Campaign Cost : <span id="span3">  </span></p>
		                </div>	
		            </div>
		        </div>
		    </div>
		</div>
	    </div> 
	</div>
	<script>

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
	<script>

		$("span[class^='plus_']").click(function () {

		    var id = $(this).attr("class");

		    $("#" + id).slideToggle("slow");
		});
		function bind_event() {
		}


		function draw_loc_chart(lid, l_id) {
		    $('.qr_sc_select').focusout();
		    $("#plus_" + lid + " #qr_scan_" + lid + " .qrcode_container").show();
		    $('#plus_' + lid + ' .loader_location').show("fast");
		    $("#plus_" + lid + " #qr_scan_" + lid).show();
		    $("#plus_" + lid + " #qr_code_scan_" + lid + " .error_message").hide();
		    $("#plus_" + lid + " #qr_scan_" + lid).show();

		    $.ajax({
		        type: "POST",
		        url: 'qrcode-ajax-monthwise.php',
		        data: 'year=' + l_id + '&month=0&selected_locations=' + lid,
		        //async: false,
		        success: function (msg)
		        {
		            obj4 = jQuery.parseJSON(msg);
		            tot_arr_locatino_json1 = obj4.arr_locatino_json;
		            tot_arr_male_arr_campaign = obj4.arr_male_arr_campaign;
		            tot_arr_female_arr_campaign = obj4.arr_female_arr_campaign;
		            tot_arr_unknown_arr_campaign = obj4.arr_unknown_arr_campaign;
		            tot_arr_all_qrcodecampaign_arr = obj4.arr_all_qrcodecampaign_arr;
		            tot_arr_unique_qrcodecampaign_arr = obj4.arr_unique_qrcodecampaign_arr;
		            all_locations = obj4.allfilterlocations;
		            //jQuery("#qrcode_select_campaign").html(obj4.location_text);

		            var chart;
		            
		            //console.log(jQuery('#container_qrcodecampaignreport_'+ lid).html());
		            
		            
		            if(jQuery('#container_qrcodecampaignreport_'+ lid).html()=="")
		            {
						console.log("redraw");
							qrcode_campaignscan_container = new Highcharts.Chart({
								chart: {
									renderTo: 'container_qrcodecampaignreport_' + lid,
									borderWidth: 1
											//zoomType: 'xy'
								},
								credits: {
									enabled: false
								},
								title: {
									text: '<?php echo $merchant_msg['report']['customer_qr_code_scan_profile']; ?>'
								},
								subtitle: {
									text: ''
								},
								plotOptions: {
									point: {
										events: {
											legendItemClick: function () {
												return false;
											}
										}
									}, allowPointSelect: false,
								},
								xAxis: [{
										categories: ['Jan-0', 'Feb-1', 'Mar-2', 'Apr-3', 'May-4', 'Jun-5',
											'Jul-6', 'Aug-7', 'Sep-8', 'Oct-9', 'Nov-10', 'Dec-11'],
										labels: {
											useHTML: true,
											formatter: function () {
												var detailid = (this.value).split("-")[1];
												var str_val = obj4.male_arr_campaign[detailid] + "-" + obj4.female_arr_campaign[detailid] + "-" + obj4.unknown_arr_campaign[detailid] + "-" + obj4.arr_unique_qrcodetotal_arr[detailid] + "-" + obj4.arr_unique_qrcodecampaign_arr[detailid];

												return '<div class="lbl_qrcode" val="' + str_val + '" >' + (this.value).split("-")[0] + '</div>';
											},
											style: {
												cursor: 'pointer'
											}
										}
									}],
								yAxis: [{// Primary yAxis
										labels: {
											formatter: function () {
												return this.value + '?C';
											},
											style: {
												color: 'red'
											}
										},
										title: {
											text: '',
											style: {
												color: 'black'
											}
										},
										opposite: true

									}, {// Secondary yAxis
										gridLineWidth: 0,
										title: {
											text: '<?php echo $merchant_msg['report']['scan']; ?>',
											style: {
												color: '#4572A7',
												fontWeight: '500'
											}
										},
										formatter: function () {
											return this.value;
										},
										style: {
											color: '#4572A7'
										}
										, min: 0, max: 100

									}, {// Tertiary yAxis
										gridLineWidth: 0,
										title: {
											text: '<?php echo $merchant_msg['report']['total_num_scans']; ?>',
											style: {
												color: '#AA4643',
												fontWeight: '500'
											}
										},
										labels: {
											formatter: function () {
												return this.value;
											},
											style: {
												color: '#AA4643'
											}
										},
										min: 0,
										opposite: true
									}],
								plotOptions: {
									series: {
										events: {
											legendItemClick: function (event) {

												return false;

											}
										}
									}, allowPointSelect: false,
								},
										tooltip: {
											formatter: function () {
												var unit = {
													'Rainfall': '',
													'Temperature': '?C',
													'Sea-Level Pressure': ''
												}[this.series.name];
												if (this.series.name == 'Male')
												{
													return '<span style="color:' + this.series.color + '">Scanned By Male : ' +
															+this.y + '% ';
												}
												if (this.series.name == 'Female')
												{
													return '<span style="color:' + this.series.color + '">Scanned By Female : ' +
															+this.y + '% ';
												}
												if (this.series.name == 'Unknown')
												{
													return '<span style="color:' + this.series.color + '">Scanned By Unknown : ' +
															+this.y + '% ';
												}
												if (this.series.name == '<?php echo $merchant_msg['report']['total_num_scans']; ?>')
												{
													return '<span style="color:' + this.series.color + '"><?php echo $merchant_msg['report']['total_num_scans']; ?> : ' +
															+this.y;
												}
												if (this.series.name == '<?php echo $merchant_msg['report']['total_num_unique_scan']; ?>')
												{
													return '<span style="color:' + this.series.color + '"><?php echo $merchant_msg['report']['total_num_unique_scan']; ?> : ' +
															+this.y;
												}


											}
										},
								series: [{
										name: 'Male',
										color: '#ef6c1c',
										type: 'column',
										yAxis: 1,
										data: obj4.male_arr_campaign

									},
									{
										name: 'Female',
										color: '#d32c48',
										type: 'column',
										yAxis: 1,
										data: obj4.female_arr_campaign
									},
									{
										name: 'Unknown',
										color: '#34b7e3',
										type: 'column',
										yAxis: 1,
										data: obj4.unknown_arr_campaign


									},
									{
										name: '<?php echo $merchant_msg['report']['total_num_scans']; ?>',
										type: 'spline',
										color: '#d6b300',
										yAxis: 2,
										data: obj4.arr_unique_qrcodetotal_arr



									},
									{
										name: '<?php echo $merchant_msg['report']['total_num_unique_scan']; ?>',
										type: 'spline',
										color: '#5540b9',
										yAxis: 2,
										data: obj4.arr_unique_qrcodecampaign_arr



									}]
							});
					}
					else
					{
						
						qrcode_campaignscan_container=jQuery('#container_qrcodecampaignreport_'+ lid).highcharts();
						qrcode_campaignscan_container.series[0].setData(obj4.male_arr_campaign);
						qrcode_campaignscan_container.series[1].setData(obj4.female_arr_campaign);
						qrcode_campaignscan_container.series[2].setData(obj4.unknown_arr_campaign);
						qrcode_campaignscan_container.series[3].setData(obj4.arr_unique_qrcodetotal_arr);
						qrcode_campaignscan_container.series[4].setData(obj4.arr_unique_qrcodecampaign_arr);
						
					}
		            bind_monthwise_report();

		        }
		    });

		    $.ajax({
		        type: "POST",
		        url: 'qrcodelocation-ajax-monthwise.php',
		        data: 'year=' + l_id + '&month=0&selected_locations=' + lid,
		        //async: false,
		        success: function (msg)
		        {

		            obj3 = jQuery.parseJSON(msg);

		            tot_arr_locatino_json = obj3.arr_locatino_json;
		            tot_arr_male_arr_location = obj3.arr_male_arr_location;
		            tot_arr_female_arr_location = obj3.arr_female_arr_location;
		            tot_arr_unknown_arr_location = obj3.arr_unknown_arr_location;
		            tot_arr_unique_qrcodelocation_arr = obj3.arr_unique_qrcodelocation_arr;
		            tot_arr_unique_qrcodetotal_arr = obj3.arr_unique_qrcodetotal_arr;
		            all_locations = obj3.allfilterlocations;
		            //jQuery("#qr_scan_"+lid).html(obj3.location_text);
		            $('#plus_' + lid + ' .loader_location').hide();
		            var chart;

					if(jQuery('#container_qrcodelocationreport_'+ lid).html()=="")
		            {
								qrcode_loactionscan_container = new Highcharts.Chart({
									chart: {
										renderTo: 'container_qrcodelocationreport_' + lid,
										borderWidth: 1
												// zoomType: 'xy'
									},
									credits: {
										enabled: false
									},
									title: {
										text: '<?php echo $merchant_msg['report']['customer_qr_code_scan_location']; ?>'
									},
									subtitle: {
										text: ''
									},
									plotOptions: {
										series: {
											events: {
												legendItemClick: function (event) {

													return false;

												}
											}
										}, allowPointSelect: false,
									},
									xAxis: [{
											categories: ['Jan-0', 'Feb-1', 'Mar-2', 'Apr-3', 'May-4', 'Jun-5',
												'Jul-6', 'Aug-7', 'Sep-8', 'Oct-9', 'Nov-10', 'Dec-11'],
											labels: {
												useHTML: true,
												formatter: function () {
													var detailid = (this.value).split("-")[1];
													//alert(obj3.male_arr_location[detailid]);
													var str_val = obj3.male_arr_location[detailid] + "-" + obj3.arr_female_arr_location[detailid] + "-" + obj3.arr_unknown_arr_location[detailid] + "-" + obj3.arr_unique_qrcodetotal_arr[detailid] + "-" + obj3.arr_unique_qrcodelocation_arr[detailid];
													return '<div class="lbl_qrcode" val="' + str_val + '" >' + (this.value).split("-")[0] + '</div>';
												},
												style: {
													cursor: 'pointer'
												}
											}
										}],
									yAxis: [{// Primary yAxis
											labels: {
												formatter: function () {
													return this.value + '?C';
												},
												style: {
													color: '#89A54E'
												}
											},
											title: {
												text: '',
												style: {
													color: '#89A54E'
												}
											},
											opposite: true

										}, {// Secondary yAxis
											gridLineWidth: 0,
											title: {
												text: '<?php echo $merchant_msg['report']['scan']; ?>',
												style: {
													color: '#4572A7',
													fontWeight: '500'
												}
											},
											labels: {
												formatter: function () {
													return this.value;
												},
												style: {
													color: '#4572A7'
												}
											}, min: 0, max: 100

										}, {// Tertiary yAxis
											gridLineWidth: 0,
											title: {
												text: '<?php echo $merchant_msg['report']['total_num_scans']; ?>',
												style: {
													color: '#AA4643',
													fontWeight: '500'
												}
											},
											labels: {
												formatter: function () {
													return this.value;
												},
												style: {
													color: '#AA4643'
												}
											},
											min: 0,
											opposite: true
										}],
									plotOptions: {
										series: {
											events: {
												legendItemClick: function (event) {

													return false;

												}
											}
										}, allowPointSelect: false
									},
									tooltip: {
										formatter: function () {
											var unit = {
												'Rainfall': '',
												'Temperature': '?C',
												'Sea-Level Pressure': ''
											}[this.series.name];
											if (this.series.name == 'Male')
											{
												return '<span style="color:' + this.series.color + '">Scanned By Male : ' +
														+this.y + '% ';
											}
											if (this.series.name == 'Female')
											{
												return '<span style="color:' + this.series.color + '">Scanned By Female : ' +
														+this.y + '% ';
											}
											if (this.series.name == 'Unknown')
											{
												return '<span style="color:' + this.series.color + '">Scanned By Unknown : ' +
														+this.y + '% ';
											}
											if (this.series.name == '<?php echo $merchant_msg['report']['total_num_scans']; ?>')
											{
												return '<span style="color:' + this.series.color + '"><?php echo $merchant_msg['report']['total_num_scans']; ?> : ' +
														+this.y;
											}
											if (this.series.name == '<?php echo $merchant_msg['report']['total_num_unique_scan']; ?>')
											{
												return '<span style="color:' + this.series.color + '"><?php echo $merchant_msg['report']['total_num_unique_scan']; ?> : ' +
														+this.y;
											}
										}
									},
									series: [{
											name: 'Male',
											color: '#ef6c1c',
											type: 'column',
											yAxis: 1,
											data: obj3.male_arr_location

										},
										{
											name: 'Female',
											color: '#d32c48',
											type: 'column',
											yAxis: 1,
											data: obj3.female_arr_location
										},
										{
											name: 'Unknown',
											color: '#34b7e3',
											type: 'column',
											yAxis: 1,
											data: obj3.unknown_arr_location

										},
										{
											name: '<?php echo $merchant_msg['report']['total_num_scans']; ?>',
											type: 'spline',
											color: '#d6b300',
											yAxis: 2,
											data: obj3.unique_qrcodetotal_arr



										},
										{
											name: '<?php echo $merchant_msg['report']['total_num_unique_scan']; ?>',
											type: 'spline',
											color: '#5540b9',
											yAxis: 2,
											data: obj3.unique_qrcodelocation_arr



										}]

								});
					}
					else
					{
						
						qrcode_loactionscan_container=jQuery('#container_qrcodelocationreport_'+ lid).highcharts();
						qrcode_loactionscan_container.series[0].setData(obj3.male_arr_location);
						qrcode_loactionscan_container.series[1].setData(obj3.female_arr_location);
						qrcode_loactionscan_container.series[2].setData(obj3.unknown_arr_location);
						qrcode_loactionscan_container.series[3].setData(obj3.unique_qrcodetotal_arr);
						qrcode_loactionscan_container.series[4].setData(obj3.unique_qrcodelocation_arr);
						
					}
					
		            bind_monthwise_report();
		        }

		    });

		    //$('#plus_' + lid + ' .loader_location').hide();

		    //bind_monthwise_report();


		}

		function bind_monthwise_report()
		{
		    jQuery(".highcharts-axis-labels .lbl_qrcode-location").on("click", function ()
		    {

		        if ($(this).text() == "Jan" || $(this).text() == "Feb" || $(this).text() == "Mar" || $(this).text() == "Apr" || $(this).text() == "May" || $(this).text() == "Jun" || $(this).text() == "Jul" || $(this).text() == "Aug" || $(this).text() == "Sep" || $(this).text() == "Oct" || $(this).text() == "Nov" || $(this).text() == "Dec")
		        {

		            var arr_val = $(this).attr("val").split("-");
				
		            $("#Notification2PopUpContainer #spanp1").html("Total Scans By Male");
		            $("#Notification2PopUpContainer #span1").html(arr_val[1] + "%");

		            $("#Notification2PopUpContainer #spanp2").html("Total Scans By Female");
		            $("#Notification2PopUpContainer #span2").html(arr_val[2] + "%");
		            $("#Notification2PopUpContainer #spanp3").html("Total Scans By Unknown");
		            $("#Notification2PopUpContainer #span3").html(arr_val[3].toString() + "%");
		            $("#Notification2PopUpContainer #spanp4").html("<?php echo $merchant_msg['report']['total_num_unique_scan']; ?>");
		            $("#Notification2PopUpContainer #span5").html(arr_val[4].toString());
		            $("#Notification2PopUpContainer #spanp5").html("<?php echo $merchant_msg['report']['total_num_scans']; ?>");
		            $("#Notification2PopUpContainer #span4").html(arr_val[5].toString());
		            jQuery.fancybox({
		                content: jQuery('#Notification2mainContainer').html(),
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
		                beforeShow: function () {

		                    $(".fancybox-inner").css("overflow", "hidden");
		                },
		                afterShow: function () {
		                    $(".fancybox-inner").css("overflow", "hidden");
		                },
		            });

		        }
		    });

		    $(".highcharts-axis-labels .lbl_qrcode").on("click", function () {

		        if ($(this).text() == "Jan" || $(this).text() == "Feb" || $(this).text() == "Mar" || $(this).text() == "Apr" || $(this).text() == "May" || $(this).text() == "Jun" || $(this).text() == "Jul" || $(this).text() == "Aug" || $(this).text() == "Sep" || $(this).text() == "Oct" || $(this).text() == "Nov" || $(this).text() == "Dec")
		        {

		            var arr_val = $(this).attr("val").split("-");
				console.log(arr_val);
		            $("#Notification2PopUpContainer #spanp1").html("Total Scans By Male");
		            $("#Notification2PopUpContainer #span1").html(arr_val[0] + "%");

		            $("#Notification2PopUpContainer #spanp2").html("Total Scans By Female");
		            $("#Notification2PopUpContainer #span2").html(arr_val[1] + "%");
		            $("#Notification2PopUpContainer #spanp3").html("Total Scans By Unknown");
		            $("#Notification2PopUpContainer #span3").html(arr_val[2].toString() + "%");
		            $("#Notification2PopUpContainer #spanp4").html("<?php echo $merchant_msg['report']['total_num_unique_scan']; ?>");
		            $("#Notification2PopUpContainer #span5").html(arr_val[3].toString());
		            $("#Notification2PopUpContainer #spanp5").html("<?php echo $merchant_msg['report']['total_num_scans']; ?>");
		            $("#Notification2PopUpContainer #span4").html(arr_val[4].toString());
		            $.fancybox({
		                content: $('#Notification2mainContainer').html(),
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
		                beforeShow: function () {

		                    $(".fancybox-inner").css("overflow", "hidden");
		                },
		                afterShow: function () {
		                    $(".fancybox-inner").css("overflow", "hidden");
		                },
		            });

		            //open_popup('Notification1');
		        }
		    });
		}


		$(document).ready(function () {

		    $(document).on('change', '.qr_sc_select', function () {
		        var lid = $(this).data('id');
		        var l_id = $('#' + this.id).val();

		        draw_loc_chart(lid, l_id);

		    });

		    $(document).on('change', '.slocation_select', function () {

		        var lid = $(this).data('id');
		        var l_id = $('#' + this.id).val();
		        $('#plus_' + lid + ' .loader_location').show();
		        $.ajax({
		            type: "POST",
		            url: 'filter_location_chart.php',
		            data: 'locationchartdata=true&location_id=' + lid + '&year=' + l_id,
		            async: true,
		            success: function (msg)
		            {
		                $('#plus_' + lid + ' .loader_location').hide();
		                obj = jQuery.parseJSON(msg);
		                if (obj.status == "true")
		                {
		                    $("#plus_" + lid + " .l_content div.error_message").remove();
		                    $("#plus_" + lid + " .l_content .divsummary_" + lid).show();
		                    $("#plus_" + lid + " .l_content .chart_container").show();
		                    $("#plus_" + lid + " .l_content .location_report_statics").show();

		                    var total_unique_campaigns = obj.total_campaigns;

		                    var tot_campaigns_arr = obj.campaigns_records;
		                    var tot_offerreserved_arr = obj.reserve_records;
		                    var tot_offerredeem_arr = obj.redeem_records;

		                    var tot_revenue_arr = obj.total_revenue;
		                    var tot_campaigncost_arr = obj.total_cost;

		                    var tot_customer_arr = obj.total_customer;
		                    var new_customer_arr = obj.new_customer;
		                    var total_redeem = 0;
		                    var tv = 0;
		                    var html = '<div class="location_name_heading"></div>';
		                    for (k = 0; k < 12; k++)
		                    {

		                        if (tot_offerredeem_arr[k] == "undefined") {
		                            tv = 0;
		                        }
		                        else {
		                            tv = tot_offerredeem_arr[k];
		                        }
		                        total_redeem = total_redeem + tv;
		                    }
		                    var total_val = 0;
		                    total_val = 0;
		                    for (k = 0; k < 12; k++)
		                    {
		                        total_val = total_val + tot_campaigns_arr[k];

		                    }
		                    var total_campaigns_count = total_unique_campaigns;
		                    html += '<p ><?php echo $merchant_msg['report']['total_num_of_campaign']; ?><span> ' + total_unique_campaigns + ' </span></p>';
		                    total_val = 0.00;
		                    for (k = 0; k < 12; k++)
		                    {
		                        total_val = total_val + tot_revenue_arr[k];
		                    }
		                    html += '<p ><?php echo $merchant_msg['report']['total_revenue']; ?><span> $' + parseFloat(total_val).toFixed(2) + '</span></p>';
		                    total_val = 0;
		                    for (k = 0; k < 12; k++)
		                    {
		                        total_val = total_val + tot_campaigncost_arr[k];
		                    }

		                    html += '<p ><?php echo $merchant_msg['report']['total_campaign_cost']; ?><span> $' + parseFloat(total_val).toFixed(2) + ' </span></p>';
		                    total_val = 0;
		                    for (k = 0; k < 12; k++)
		                    {
		                        total_val = total_val + tot_customer_arr[k];
		                    }
		                    html += '<p ><?php echo $merchant_msg['report']['total_number_of_customer_visited']; ?><span>' + total_val + ' </span></p>';
		                    total_val = 0;
		                    for (k = 0; k < 12; k++)
		                    {
		                        total_val = total_val + new_customer_arr[k];
		                    }
		                    html += '<p ><?php echo $merchant_msg['report']['total_number_of_new_customer_visited']; ?><span>' + obj.tot_new_customers + ' </span></p>';
		                    // html += '<p ><?php echo $merchant_msg['report']['customer_profile_for_location']; ?><span><?php echo $merchant_msg['report']['see_below']; ?></span></p>';

		                    $(".divsummary_" + lid).html(html);

		                    if (total_campaigns_count != 0) {
		                        if (total_redeem != 0) {
		                            var container1 = (obj.gender_str).split('-');
		                            var v1 = parseInt(container1[0]);
		                            var v2 = parseInt(container1[1]);
		                            var v3 = parseInt(container1[2]);

		                            var container2 = (obj.age_str).split('-');
		                            var agewise_gender_male = (obj.agewise_male).split('-');
		                            var ahm1 = parseInt(agewise_gender_male [0]);
		                            var ahm2 = parseInt(agewise_gender_male [1]);
		                            var ahm3 = parseInt(agewise_gender_male [2]);
		                            var ahm4 = parseInt(agewise_gender_male [3]);
		                            var ahm5 = parseInt(agewise_gender_male [4]);
		                            var ahm6 = parseInt(agewise_gender_male [5]);

		                            var agewise_gender_female = (obj.agewise_female).split('-');
		                            var afm1 = parseInt(agewise_gender_female [0]);
		                            var afm2 = parseInt(agewise_gender_female [1]);
		                            var afm3 = parseInt(agewise_gender_female [2]);
		                            var afm4 = parseInt(agewise_gender_female [3]);
		                            var afm5 = parseInt(agewise_gender_female [4]);
		                            var afm6 = parseInt(agewise_gender_female [5]);

		                            var c1 = parseInt(container2[0]);
		                            var c2 = parseInt(container2[1]);
		                            var c3 = parseInt(container2[2]);
		                            var c4 = parseInt(container2[3]);
		                            var c5 = parseInt(container2[4]);
		                            var c6 = parseInt(container2[5]);
		                            var chart;


		                            /*location_gender_chart_container[lid] = new Highcharts.Chart({
		                                chart: {
		                                    renderTo: 'container_' + lid,
		                                    borderWidth: 1,
		                                    plotBackgroundColor: null,
		                                    plotBorderWidth: null,
		                                    plotShadow: false
		                                },
		                                title: {
		                                    text: '<?php echo $merchant_msg['report']['cust_by_gender']; ?>',
		                                    align: 'center',
		                                    verticalAlign: 'middle',
		                                    y: -85
		                                },
		                                tooltip: {
		                                    pointFormat: '<b>{point.percentage:.1f}%</b>'
		                                },
		                                plotOptions: {
		                                    pie: {
		                                        dataLabels: {
		                                            enabled: true,
		                                            distance: -30,
		                                            style: {
		                                                fontWeight: 'bold',
		                                                color: 'white',
		                                                textShadow: '0px 1px 2px black'
		                                            }
		                                        },
		                                        startAngle: -90,
		                                        endAngle: 90,
		                                        center: ['50%', '75%']
		                                    }
		                                },
		                                credits: {
		                                    enabled: false
		                                },
		                                series: [{
		                                        type: 'pie',
		                                        showInLegend: false,
		                                        //name: '',
		                                        innerSize: '50%',
		                                        data: [
		                                            ["Male", v1], ['Female', v2],
		                                        ]
		                                    }]
		                            });
									*/	
									location_gender_chart_container[lid]=jQuery('#container_'+ lid).highcharts();
									location_gender_chart_container[lid].series[0].setData([v1,v2]);
									
									/*	
		                            categories = ['17 Or Below', '18 to 24', '25 to 44', '45 to 54',
		                                '55 to 64', '65+'];
		                            location_age_chart_container[lid] = new Highcharts.Chart({
		                                chart: {
		                                    renderTo: 'containerage_' + lid,
		                                    borderWidth: 1,
		                                    type: 'bar'
		                                },
		                                title: {
		                                    text: '<?php echo $merchant_msg['report']['cust_by_age']; ?>'
		                                },
		                                xAxis: [{
		                                        title: {
		                                            text: 'Age Distribution'
		                                        },
		                                        categories: categories,
		                                        reversed: false
		                                    }, {// mirror axis on right side
		                                        opposite: true,
		                                        reversed: false,
		                                        categories: categories,
		                                        linkedTo: 0
		                                    }],
		                                yAxis: {
		                                    title: {
		                                        text: null
		                                    },
		                                    labels: {
		                                        formatter: function () {
		                                            return (Math.abs(this.value)) + '%';
		                                        }
		                                    },
		                                    min: -100,
		                                    max: 100
		                                },
		                                plotOptions: {
		                                    series: {
		                                        stacking: 'normal'
		                                    }
		                                },
		                                tooltip: {
		                                    formatter: function () {
		                                        return '<b>' + this.series.name + ' Age ( ' + this.point.category + ' ) : </b>' +
		                                                Highcharts.numberFormat(Math.abs(this.point.y), 0) + '%';
		                                    }
		                                },
		                                credits: {
		                                    enabled: false
		                                },
		                                series: [{
		                                        name: 'Male',
		                                        data: [ahm1, ahm2, ahm3, ahm4, ahm5, ahm6]
		                                    }, {
		                                        name: 'Female',
		                                        data: [-afm1, -afm2, -afm3, -afm4, -afm5, -afm6]
		                                    }]
		                            });
		                            */
		                            
		                            location_age_chart_container[lid]=jQuery('#containerage_'+ lid).highcharts();
									location_age_chart_container[lid].series[0].setData([ahm1, ahm2, ahm3, ahm4, ahm5, ahm6]);
									location_age_chart_container[lid].series[1].setData([-afm1, -afm2, -afm3, -afm4, -afm5, -afm6]);
									
		                            //});
		                        }
		                        else {
		                            $("#container_" + lid).css("display", "none");
		                            $("#container_" + lid).parent().parent().css("display", "none");
		                            $("#containerrange_" + lid).css("display", "none");
		                        }

		                        var chart;
								
								
		                        location_cmapaign_chart_container[lid] = new Highcharts.Chart({
		                            chart: {
		                                renderTo: 'container_report_' + lid,
		                                borderWidth: 1
		                                        //zoomType: 'xy'
		                            },
		                            credits: {
		                                enabled: false
		                            },
		                            title: {
		                                text: '<?php echo $merchant_msg['report']['location_report']; ?>'
		                            },
		                            subtitle: {
		                                text: ''
		                            },
		                            plotOptions: {
		                                series: {
		                                    events: {
		                                        legendItemClick: function (event) {

		                                            return false;

		                                        }
		                                    }
		                                }, allowPointSelect: false,
		                            },
		                            xAxis: [{
		                                    categories: ['Jan-0', 'Feb-1', 'Mar-2', 'Apr-3', 'May-4', 'Jun-5',
		                                        'Jul-6', 'Aug-7', 'Sep-8', 'Oct-9', 'Nov-10', 'Dec-11'],
		                                    labels: {
		                                        useHTML: true,
		                                        formatter: function () {

		                                            var detailid = (this.value).split("-")[1];
		                                            var str_val = tot_campaigns_arr[detailid] + "-$" + tot_revenue_arr[detailid] + "-$" + tot_campaigncost_arr[detailid] + "-" + tot_customer_arr[detailid] + "-" + tot_offerredeem_arr[detailid] + "-" + tot_offerreserved_arr[detailid];

		                                            return '<div class="lbllocationdetail" lbllocationdetail_id="' + str_val + '">' + (this.value).split("-")[0] + '</div>';
		                                        },
		                                        style: {
		                                            cursor: 'pointer'
		                                        }
		                                    }

		                                }],
		                            yAxis: [{// Primary yAxis
		                                    labels: {
		                                        formatter: function () {
		                                            return this.value + '?C';
		                                        },
		                                        style: {
		                                            color: '#89A54E'
		                                        }
		                                    },
		                                    title: {
		                                        text: '',
		                                        style: {
		                                            color: '#89A54E'
		                                        }
		                                    },
		                                    opposite: true

		                                }, {// Secondary yAxis
		                                    gridLineWidth: 0,
		                                    title: {
		                                        text: '<?php echo $merchant_msg['report']['ct_total_camp_cust_offers']; ?>',
		                                        style: {
		                                            color: '#4572A7'
		                                        }
		                                    },
		                                    labels: {
		                                        formatter: function () {
		                                            return this.value;
		                                        },
		                                        style: {
		                                            color: '#4572A7'
		                                        }
		                                    }, min: 0

		                                }, {// Tertiary yAxis
		                                    gridLineWidth: 0,
		                                    title: {
		                                        text: '<?php echo $merchant_msg['report']['ct_total_camp_cost_revenue']; ?>',
		                                        style: {
		                                            color: '#AA4643'
		                                        }
		                                    },
		                                    labels: {
		                                        formatter: function () {
		                                            return this.value;
		                                        },
		                                        style: {
		                                            color: '#AA4643'
		                                        }
		                                    },
		                                    min: 0,
		                                    opposite: true
		                                }],
		                            tooltip: {
		                                formatter: function () {
		                                    var unit = {
		                                        'Rainfall': '',
		                                        'Temperature': '?C',
		                                        'Sea-Level Pressure': ''
		                                    }[this.series.name];
		                                    if (this.series.name == "Total Campaign Cost" || this.series.name == "Total Revenue")
		                                    {
		                                        return '<span style="color:' + this.series.color + '">' + this.series.name
		                                                + ': $' + this.y
		                                    }
		                                    else
		                                    {
		                                        return '<span style="color:' + this.series.color + '">' + this.series.name
		                                                + ': ' + this.y
		                                    }
		                                }
		                            },
		                            series: [{
		                                    name: '<?php echo $merchant_msg['report']['ct_total_campaigns']; ?>',
		                                    color: '#4572A7',
		                                    type: 'column',
		                                    yAxis: 1,
		                                    data: tot_campaigns_arr

		                                },
		                                {
		                                    name: '<?php echo $merchant_msg['report']['ct_total_offers_issued']; ?>',
		                                    color: '#D1524C',
		                                    type: 'column',
		                                    yAxis: 1,
		                                    data: tot_offerreserved_arr

		                                },
		                                {
		                                    name: '<?php echo $merchant_msg['report']['ct_total_offers_redeemed']; ?>',
		                                    color: '#89A54E',
		                                    type: 'column',
		                                    yAxis: 1,
		                                    data: tot_offerredeem_arr

		                                },
		                                {
		                                    name: '<?php echo $merchant_msg['report']['ct_total_revenue']; ?>',
		                                    type: 'spline',
		                                    color: '#4572A7',
		                                    yAxis: 2,
		                                    data: tot_revenue_arr



		                                }, {
		                                    name: '<?php echo $merchant_msg['report']['ct_total_campaign_cost']; ?>',
		                                    color: '#3D96AE',
		                                    yAxis: 2,
		                                    type: 'spline',
		                                    data: tot_campaigncost_arr
		                                }]
		                        });
		                        
		                        /*
		                        location_cmapaign_chart_container[lid]=jQuery('#container_report_'+ lid).highcharts();
								location_cmapaign_chart_container[lid].series[0].setData(tot_campaigns_arr);
								location_cmapaign_chart_container[lid].series[1].setData(tot_offerreserved_arr);
								location_cmapaign_chart_container[lid].series[2].setData(tot_offerredeem_arr);
								location_cmapaign_chart_container[lid].series[3].setData(tot_revenue_arr);
								location_cmapaign_chart_container[lid].series[4].setData(tot_campaigncost_arr);
								*/
									
		                        //}
		                        $('div[id^="container_report_"] .highcharts-axis-labels span .lbllocationdetail').hover(function () {
		                            //         alert("iiii");
		                            $(this).css('color', '#0192b5');
		                            //		
		                        }, function () {
		                            //		
		                            $(this).css('color', '#666666');

		                        });
		                        bind_locationdetail_event();

		                        /// });
		                    }

		                    $('#plus_' + lid + ' .loader_location').hide();

		                }
		                else {
		                    $("#plus_" + lid + " .l_content div.error_message").remove();
		                    $("#plus_" + lid + " .l_content").prepend('<div class="error_message">No data found in the system. please try changing your filter settings.</div>');
		                    $("#plus_" + lid + " #qr_code_scan_" + lid).prepend('<div class="error_message">No data found in the system. please try changing your filter settings.</div>');
		                    $("#plus_" + lid + " #qr_scan_" + lid).hide();
		                    $("#plus_" + lid + " .l_content .divsummary_" + lid).hide();
		                    $("#plus_" + lid + " .l_content .chart_container").hide();
		                    $("#container_report_" + lid).css("display", "none");

		                    $("#plus_" + lid).show();
		                    $('#plus_' + lid + ' .loader_location').hide();
		                }

		            }
		        });
		    });

		    $(document).on("click", ".location_heading", function () {
				//console.log("hi");
		        var idd = '';
		        var mtogl = '';
		        var is_location_chart=0;
		        if ($(this).hasClass('location_heading')) 
		        {
					console.log("if");
		            
		            mtogl = $(this).find("div[id^='toggleIt-']"); // location report location tab
					
					console.log(mtogl.length);
		            
		            if (mtogl.length == 0) 
		            {
		                mtogl = $(this).find("div[id^='report_toggleIt-']"); // campaign report location tab
		                idd = mtogl.attr('id');
		                is_location_chart=0;
		            } 
		            else 
		            {
		                idd = mtogl.attr('id');
		                is_location_chart=1;
		            }
		        } 
		        else 
		        {
					console.log("else");
		            
		            mtogl = $(this);
		            idd = mtogl.attr('id');
		        }
				
				if(is_location_chart==1)
				{
					console.log("location chart");
				}
				else
				{
					console.log("campaign chart");
				}
				
		        var flag = 0;

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
		                    flag = 1;
		                }
		            }
		        });

		        if (flag == 1)
		        {
		            return false;
		        }
		        else
		        {
		            var a = idd.split("-");

		            var lid = a[1].split("_")[1];


		            if ($("#" + a[1]).css("display") == "block")
		            {

		                $("#" + a[1]).slideUp("fast");
		                //$(this).removeClass('mainIcon_minus'); 
		                mtogl.removeClass('mainIcon_minus');

		            }
		            else
		            {
						if(is_location_chart==1)
						{
							
						//if(is_location_chart==1)
						//{
						//console.log($("#container_report_" + lid).attr("camp_avail"));
		                if ($("#container_report_" + lid).attr("camp_avail") != 0)
		                {
							//console.log("if");	
		                            
		                }
		                else
		                {
							//console.log("else"); 
								
		                    $("#container_report_" + lid).css("display", "none");

		                    $("#container_report_" + lid).parent().parent().css("display", "none");
		                    $("#container_" + lid).css("display", "none");
		                    $("#container_" + lid).parent().parent().css("display", "none");
		                    $("#containerrange_" + lid).css("display", "none");
		                }
		                $("#" + a[1]).slideDown("slow");
		                ///$(this).addClass('mainIcon_minus');
		                mtogl.addClass('mainIcon_minus');

		                if ($("#container_report_" + lid).html() == "") {

		                    if (jQuery("#container_report_" + lid).attr("camp_avail") != 0) {
		                        //open_popup('Notification');
		                        //jQuery("#upload_business_logo_ajax1").show();
		                        $('#plus_' + lid + ' .loader_location').show();

		                    }

		                    if (jQuery("#container_report_" + lid).attr("camp_avail") != 0) {
		                        var l_id = $("#slocation_select_" + lid).val();
		                        jQuery.ajax({
		                            type: "POST",
		                            url: 'filter_location_chart.php',
		                            data: 'locationchartdata=true&location_id=' + lid + '&year=' + l_id,
		                            async: true,
		                            success: function (msg)
		                            {

		                                obj = jQuery.parseJSON(msg);
		                                if (obj.status == "true")
		                                {
		                                    $("#plus_" + lid + " .l_content .divsummary_" + lid).show();
		                                    $("#plus_" + lid + " .l_content .chart_container").show();
		                                    $("#plus_" + lid + " .l_content div.error_message").remove();
		                                    var total_unique_campaigns = obj.total_campaigns;
		                                    //alert(total_unique_campaigns+"=="+obj.total_campaigns);
		                                    var tot_campaigns_arr = obj.campaigns_records;
		                                    var tot_offerreserved_arr = obj.reserve_records;
		                                    var tot_offerredeem_arr = obj.redeem_records;

		                                    var tot_revenue_arr = obj.total_revenue;
		                                    var tot_campaigncost_arr = obj.total_cost;

		                                    var tot_customer_arr = obj.total_customer;
		                                    var new_customer_arr = obj.new_customer;
		                                    var total_redeem = 0;
		                                    var tv = 0;
		                                    var html = '<div class="location_name_heading"></div>';
		                                    for (k = 0; k < 12; k++)
		                                    {

		                                        if (tot_offerredeem_arr[k] == "undefined") {
		                                            tv = 0;
		                                        }
		                                        else {
		                                            tv = tot_offerredeem_arr[k];
		                                        }
		                                        total_redeem = total_redeem + tv;
		                                    }
		                                    var total_val = 0;
		                                    total_val = 0;
		                                    for (k = 0; k < 12; k++)
		                                    {
		                                        total_val = total_val + tot_campaigns_arr[k];

		                                    }
		                                    var total_campaigns_count = total_unique_campaigns;
		                                    html += '<p ><?php echo $merchant_msg['report']['total_num_of_campaign']; ?><span> ' + total_unique_campaigns + ' </span></p>';
		                                    total_val = 0.00;
		                                    for (k = 0; k < 12; k++)
		                                    {
		                                        total_val = total_val + tot_revenue_arr[k];
		                                    }
		                                    html += '<p ><?php echo $merchant_msg['report']['total_revenue']; ?><span> $' + parseFloat(total_val).toFixed(2) + '</span></p>';
		                                    total_val = 0;
		                                    for (k = 0; k < 12; k++)
		                                    {
		                                        total_val = total_val + tot_campaigncost_arr[k];
		                                    }

		                                    html += '<p ><?php echo $merchant_msg['report']['total_campaign_cost']; ?><span> $' + parseFloat(total_val).toFixed(2) + ' </span></p>';
		                                    total_val = 0;
		                                    for (k = 0; k < 12; k++)
		                                    {
		                                        total_val = total_val + tot_customer_arr[k];
		                                    }
		                                    html += '<p ><?php echo $merchant_msg['report']['total_number_of_customer_visited']; ?><span>' + total_val + ' </span></p>';
		                                    total_val = 0;
		                                    for (k = 0; k < 12; k++)
		                                    {
		                                        total_val = total_val + new_customer_arr[k];
		                                    }
		                                    html += '<p ><?php echo $merchant_msg['report']['total_number_of_new_customer_visited']; ?><span>' + obj.tot_new_customers + ' </span></p>';
		                                    //html += '<p ><?php echo $merchant_msg['report']['customer_profile_for_location']; ?><span><?php echo $merchant_msg['report']['see_below']; ?></span></p>';
		                                   
		                                    $(".divsummary_" + lid).html(html);
		                                    
		                                    if (total_campaigns_count != 0) {
		                                        if (total_redeem != 0) {
		                                            var container1 = (obj.gender_str).split('-');
		                                            var v1 = parseInt(container1[0]);
		                                            var v2 = parseInt(container1[1]);
		                                            var v3 = parseInt(container1[2]);

		                                            var container2 = (obj.age_str).split('-');
		                                            var agewise_gender_male = (obj.agewise_male).split('-');
		                                            var ahm1 = parseInt(agewise_gender_male [0]);
		                                            var ahm2 = parseInt(agewise_gender_male [1]);
		                                            var ahm3 = parseInt(agewise_gender_male [2]);
		                                            var ahm4 = parseInt(agewise_gender_male [3]);
		                                            var ahm5 = parseInt(agewise_gender_male [4]);
		                                            var ahm6 = parseInt(agewise_gender_male [5]);

		                                            var agewise_gender_female = (obj.agewise_female).split('-');
		                                            var afm1 = parseInt(agewise_gender_female [0]);
		                                            var afm2 = parseInt(agewise_gender_female [1]);
		                                            var afm3 = parseInt(agewise_gender_female [2]);
		                                            var afm4 = parseInt(agewise_gender_female [3]);
		                                            var afm5 = parseInt(agewise_gender_female [4]);
		                                            var afm6 = parseInt(agewise_gender_female [5]);

		                                            var c1 = parseInt(container2[0]);
		                                            var c2 = parseInt(container2[1]);
		                                            var c3 = parseInt(container2[2]);
		                                            var c4 = parseInt(container2[3]);
		                                            var c5 = parseInt(container2[4]);
		                                            var c6 = parseInt(container2[5]);
		                                            var chart;


		                                            location_gender_chart_container[lid] = new Highcharts.Chart({
		                                                chart: {
		                                                    renderTo: 'container_' + lid,
		                                                    borderWidth: 1,
		                                                    plotBackgroundColor: null,
		                                                    plotBorderWidth: null,
		                                                    plotShadow: false
		                                                },
		                                                title: {
		                                                    text: '<?php echo $merchant_msg['report']['cust_by_gender']; ?>',
		                                                    align: 'center',
		                                                    verticalAlign: 'middle',
		                                                    y: -85
		                                                },
		                                                tooltip: {
		                                                    pointFormat: '<b>{point.percentage:.1f}%</b>'
		                                                },
		                                                plotOptions: {
		                                                    pie: {
		                                                        dataLabels: {
		                                                            enabled: true,
		                                                            distance: -30,
		                                                            style: {
		                                                                fontWeight: 'bold',
		                                                                color: 'white',
		                                                                textShadow: '0px 1px 2px black'
		                                                            }
		                                                        },
		                                                        startAngle: -90,
		                                                        endAngle: 90,
		                                                        center: ['50%', '75%']
		                                                    }
		                                                },
		                                                credits: {
		                                                    enabled: false
		                                                },
		                                                series: [{
		                                                        type: 'pie',
		                                                        showInLegend: false,
		                                                        //name: '',
		                                                        innerSize: '50%',
		                                                        data: [
		                                                            ["Male", v1], ['Female', v2],
		                                                        ]
		                                                    }]
		                                            });




		                                            categories = ['17 Or Below', '18 to 24', '25 to 44', '45 to 54',
		                                                '55 to 64', '65+'];
		                                            location_age_chart_container[lid] = new Highcharts.Chart({
		                                                chart: {
		                                                    renderTo: 'containerage_' + lid,
		                                                    borderWidth: 1,
		                                                    type: 'bar'
		                                                },
		                                                title: {
		                                                    text: '<?php echo $merchant_msg['report']['cust_by_age']; ?>'
		                                                },
		                                                xAxis: [{
		                                                        title: {
		                                                            text: 'Age Distribution'
		                                                        },
		                                                        categories: categories,
		                                                        reversed: false
		                                                    }, {// mirror axis on right side
		                                                        opposite: true,
		                                                        reversed: false,
		                                                        categories: categories,
		                                                        linkedTo: 0
		                                                    }],
		                                                yAxis: {
		                                                    title: {
		                                                        text: null
		                                                    },
		                                                    labels: {
		                                                        formatter: function () {
		                                                            return (Math.abs(this.value)) + '%';
		                                                        }
		                                                    },
		                                                    min: -100,
		                                                    max: 100
		                                                },
		                                                plotOptions: {
		                                                    series: {
		                                                        stacking: 'normal'
		                                                    }
		                                                },
		                                                tooltip: {
		                                                    formatter: function () {
		                                                        return '<b>' + this.series.name + ' Age ( ' + this.point.category + ' ) : </b>' +
		                                                                Highcharts.numberFormat(Math.abs(this.point.y), 0) + '%';
		                                                    }
		                                                },
		                                                credits: {
		                                                    enabled: false
		                                                },
		                                                series: [{
		                                                        name: 'Male',
		                                                        data: [ahm1, ahm2, ahm3, ahm4, ahm5, ahm6]
		                                                    }, {
		                                                        name: 'Female',
		                                                        data: [-afm1, -afm2, -afm3, -afm4, -afm5, -afm6]
		                                                    }]
		                                            });
		                                            //});
		                                        }
		                                        else {
		                                            $("#container_" + lid).css("display", "none");
		                                            $("#container_" + lid).parent().parent().css("display", "none");
		                                            $("#containerrange_" + lid).css("display", "none");
		                                        }

		                                        var chart;
												//alert("hi");
		                                        location_cmapaign_chart_container[lid] = new Highcharts.Chart({
		                                            chart: {
		                                                renderTo: 'container_report_' + lid,
		                                                borderWidth: 1
		                                                        //zoomType: 'xy'
		                                            },
		                                            credits: {
		                                                enabled: false
		                                            },
		                                            title: {
		                                                text: '<?php echo $merchant_msg['report']['location_report']; ?>'
		                                            },
		                                            subtitle: {
		                                                text: ''
		                                            },
		                                            plotOptions: {
		                                                series: {
		                                                    events: {
		                                                        legendItemClick: function (event) {

		                                                            return false;

		                                                        }
		                                                    }
		                                                }, allowPointSelect: false,
		                                            },
		                                            xAxis: [{
		                                                    categories: ['Jan-0', 'Feb-1', 'Mar-2', 'Apr-3', 'May-4', 'Jun-5',
		                                                        'Jul-6', 'Aug-7', 'Sep-8', 'Oct-9', 'Nov-10', 'Dec-11'],
		                                                    labels: {
		                                                        useHTML: true,
		                                                        formatter: function () {

		                                                            var detailid = (this.value).split("-")[1];
		                                                            var str_val = tot_campaigns_arr[detailid] + "-$" + tot_revenue_arr[detailid] + "-$" + tot_campaigncost_arr[detailid] + "-" + tot_customer_arr[detailid] + "-" + tot_offerredeem_arr[detailid] + "-" + tot_offerreserved_arr[detailid];

		                                                            return '<div class="lbllocationdetail" lbllocationdetail_id="' + str_val + '">' + (this.value).split("-")[0] + '</div>';
		                                                        },
		                                                        style: {
		                                                            cursor: 'pointer'
		                                                        }
		                                                    }

		                                                }],
		                                            yAxis: [{// Primary yAxis
		                                                    labels: {
		                                                        formatter: function () {
		                                                            return this.value + '?C';
		                                                        },
		                                                        style: {
		                                                            color: '#89A54E'
		                                                        }
		                                                    },
		                                                    title: {
		                                                        text: '',
		                                                        style: {
		                                                            color: '#89A54E'
		                                                        }
		                                                    },
		                                                    opposite: true

		                                                }, {// Secondary yAxis
		                                                    gridLineWidth: 0,
		                                                    title: {
		                                                        text: '<?php echo $merchant_msg['report']['ct_total_camp_cust_offers']; ?>',
		                                                        style: {
		                                                            color: '#4572A7'
		                                                        }
		                                                    },
		                                                    labels: {
		                                                        formatter: function () {
		                                                            return this.value;
		                                                        },
		                                                        style: {
		                                                            color: '#4572A7'
		                                                        }
		                                                    }, min: 0

		                                                }, {// Tertiary yAxis
		                                                    gridLineWidth: 0,
		                                                    title: {
		                                                        text: '<?php echo $merchant_msg['report']['ct_total_camp_cost_revenue']; ?>',
		                                                        style: {
		                                                            color: '#AA4643'
		                                                        }
		                                                    },
		                                                    labels: {
		                                                        formatter: function () {
		                                                            return this.value;
		                                                        },
		                                                        style: {
		                                                            color: '#AA4643'
		                                                        }
		                                                    },
		                                                    min: 0,
		                                                    opposite: true
		                                                }],
		                                            tooltip: {
		                                                formatter: function () {
		                                                    var unit = {
		                                                        'Rainfall': '',
		                                                        'Temperature': '?C',
		                                                        'Sea-Level Pressure': ''
		                                                    }[this.series.name];
		                                                    if (this.series.name == "Total Campaign Cost" || this.series.name == "Total Revenue")
		                                                    {
		                                                        return '<span style="color:' + this.series.color + '">' + this.series.name
		                                                                + ': $' + this.y
		                                                    }
		                                                    else
		                                                    {
		                                                        return '<span style="color:' + this.series.color + '">' + this.series.name
		                                                                + ': ' + this.y
		                                                    }
		                                                }
		                                            },
		                                            series: [{
		                                                    name: '<?php echo $merchant_msg['report']['ct_total_campaigns']; ?>',
		                                                    color: '#4572A7',
		                                                    type: 'column',
		                                                    yAxis: 1,
		                                                    data: tot_campaigns_arr

		                                                },
		                                                {
		                                                    name: '<?php echo $merchant_msg['report']['ct_total_offers_issued']; ?>',
		                                                    color: '#D1524C',
		                                                    type: 'column',
		                                                    yAxis: 1,
		                                                    data: tot_offerreserved_arr

		                                                },
		                                                {
		                                                    name: '<?php echo $merchant_msg['report']['ct_total_offers_redeemed']; ?>',
		                                                    color: '#89A54E',
		                                                    type: 'column',
		                                                    yAxis: 1,
		                                                    data: tot_offerredeem_arr

		                                                },
		                                                {
		                                                    name: '<?php echo $merchant_msg['report']['ct_total_revenue']; ?>',
		                                                    type: 'spline',
		                                                    color: '#4572A7',
		                                                    yAxis: 2,
		                                                    data: tot_revenue_arr



		                                                }, {
		                                                    name: '<?php echo $merchant_msg['report']['ct_total_campaign_cost']; ?>',
		                                                    color: '#3D96AE',
		                                                    yAxis: 2,
		                                                    type: 'spline',
		                                                    data: tot_campaigncost_arr
		                                                }
		                                                /*, {
		                                                 name: '<?php echo $merchant_msg['report']['ct_total_num_customers_visited']; ?>',
		                                                 color: 'green',
		                                                 yAxis: 1,
		                                                 type: 'spline',
		                                                 data:  tot_customer_arr 
		                                                 } */]
		                                        });
		                                        
		                                        $('div[id^="container_report_"] .highcharts-axis-labels span .lbllocationdetail').hover(function () {
		                                            
		                                            $(this).css('color', '#0192b5');
		                                            		
		                                        }, function () {
		                                            		
		                                            $(this).css('color', '#666666');

		                                        });
		                                        bind_locationdetail_event();

		                                        
		                                    }
		                                    $('#plus_' + lid + ' .loader_location').hide();

		                                    draw_loc_chart(lid, l_id);
		                                }
		                                else {

		                                    $("#plus_" + lid + " .l_content div.error_message").remove();
		                                    $("#plus_" + lid + " .l_content").prepend('<div class="error_message">No data found in the system. please try changing your filter settings.</div>');
		                                    $("#plus_" + lid + " .l_content .divsummary_" + lid).hide();
		                                    $("#plus_" + lid + " .l_content .chart_container").hide();
		                                    $("#container_report_" + lid).css("display", "none");
		                                    //$("#container_report_" + lid).parent().parent().css("display", "none");
		                                    $("#plus_" + lid).show();
		                                    $("#plus_" + lid + " #qr_code_scan_" + lid).prepend('<div class="error_message">No data found in the system. please try changing your filter settings.</div>');
		                                    $("#plus_" + lid + " #qr_scan_" + lid).hide();
		                                    $('#plus_' + lid + ' .loader_location').hide();

		                                }

		                            }
		                        });

		                    } else {
					$("#plus_" + lid + " .l_content div.error_message").remove();
		                        $("#plus_" + lid + " .l_content").prepend('<div class="error_message">No data found in the system. please try changing your filter settings.</div>');
		                        $("#container_report_" + lid).css("display", "none");
		                        //$("#container_report_" + lid).parent().parent().css("display", "none");
					$("#container_report_" + lid).parent().parent().show();
		                        
		                    }

		                }
		                else
		                {
		                    for (var prop in location_gender_chart_container) {

		                        if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                        {
		                            width = jQuery("#container_" + prop).width();
		                            height = jQuery("#container_" + prop).height();
		                            var p = parseInt(prop);
		                            location_gender_chart_container[p].setSize(width, height, doAnimation = true);
		                        }
		                    }
		                    /*** for age ***/
		                    for (var prop in location_age_chart_container) {

		                        if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                        {
		                            width = jQuery("#containerage_" + prop).width();
		                            height = jQuery("#containerage_" + prop).height();
		                            var p = parseInt(prop);
		                            location_age_chart_container[p].setSize(width, height, doAnimation = true);
		                        }
		                    }
		                    /**** for campaign report ***/
		                    for (var prop in location_cmapaign_chart_container) {

		                        if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		                        {
		                            width = jQuery("#container_report_" + prop).width();
		                            height = jQuery("#container_report_" + prop).height();
		                            var p = parseInt(prop);
		                            location_cmapaign_chart_container[p].setSize(width, height, doAnimation = true);
		                        }
		                    }
		                }
		            
						}
						else
						{
							/*
							$("#" + a[1]).slideDown("slow");
							mtogl.addClass('mainIcon_minus');
							*/

							//var a = $(this).attr("id").split("-");
							$("#" + a[1]).slideDown("slow", function () {
								draw_all_chart(lid);
							});
							mtogl.addClass('mainIcon_minus');
            
						}
		            
		            }
		        }
		        bind_locationdetail_event();
		        
		        console.log("success");
		        
		    });


		});
		//}


		bind_event();

		jQuery("#span_location").click(function () {
		    // jQuery("#span_campaign")
		    jQuery("#qrcode_select_location").slideToggle("slow");
		});

		jQuery("#span_campaign").click(function () {

		    jQuery("#qrcode_select_campaign").slideToggle("slow");
		});
		function getCookie(c_name)
		{
		    var c_value = document.cookie;
		    var c_start = c_value.indexOf(" " + c_name + "=");
		    if (c_start == -1)
		    {
		        c_start = c_value.indexOf(c_name + "=");
		    }
		    if (c_start == -1)
		    {
		        c_value = null;
		    }
		    else
		    {
		        c_start = c_value.indexOf("=", c_start) + 1;
		        var c_end = c_value.indexOf(";", c_start);
		        if (c_end == -1)
		        {
		            c_end = c_value.length;
		        }
		        c_value = unescape(c_value.substring(c_start, c_end));
		    }
		    return c_value;
		}
		function setCookie(c_name, value, exdays)
		{
		    var exdate = new Date();
		    exdate.setDate(exdate.getDate() + exdays);
		    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
		    document.cookie = c_name + "=" + c_value;
		}
		jQuery("#popupcancel").on("click", function () {
		    jQuery.fancybox.close();
		    return false;
		});
		function bind_locationdetail_event() {
		    $('div[id^="container_report_"] .highcharts-axis-labels span .lbllocationdetail').click(function () {
		        //alert($(this).text());
		        //alert("hi");
		        //$( "text" ).each(function() {
		        //var index_val=console.log( index + ": " + $(this).text() );

		        //alert(tot_campaigns_arr[1].toString());
		        if ($(this).text() == "Jan" || $(this).text() == "Feb" || $(this).text() == "Mar" || $(this).text() == "Apr" || $(this).text() == "May" || $(this).text() == "Jun" || $(this).text() == "Jul" || $(this).text() == "Aug" || $(this).text() == "Sep" || $(this).text() == "Oct" || $(this).text() == "Nov" || $(this).text() == "Dec")
		        {

		            var arr_val = $(this).attr("lbllocationdetail_id").split("-");
		           
		            $("#Notification1PopUpContainer #span1").html(arr_val[0].toString());
		            $("#Notification1PopUpContainer #span2").html(arr_val[1].toString());
		            $("#Notification1PopUpContainer #span3").html(arr_val[2].toString());
		            //$("#Notification1PopUpContainer #span4").html(arr_val[3].toString());
		            $("#Notification1PopUpContainer #span5").html(arr_val[4].toString());
		            $("#Notification1PopUpContainer #span6").html(arr_val[5]);

		            jQuery.fancybox({
		                content: jQuery('#Notification1mainContainer').html(),
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

		            //open_popup('Notification1');
		        }
		    });
		}
		jQuery(document).on("click", "#close_campaign", function () {
			/*
		    //alert("In close");
		    $(".campaign_report").css("display", "none");
		    //alert($('li.statistics_report a').length);
		    $(".campaign_report").css("display", "none");
		    //	 jQuery('li.statistics_report a').bind("click");
		    $("#sidemenu li a").each(function () {
		        $(this).removeClass("current");
		    });
		    $("li.statistics_report a").addClass("current");
		    var cls = $(this).parent().attr("class");
		    $(".tabs").each(function () {
		        $(this).css("display", "none");
		    });
		    $('#statistics_report').css("display", "block");
		    */
		    
		    
		    $(".newcampaign_report").css("display", "none");
		    $(".newcampaign_report").css("display", "none");
		    
		    $("#sidemenu li a").each(function () {
		        $(this).removeClass("current");
		    });
		    $("li.statistics_report a").addClass("current");
		    var cls = $(this).parent().attr("class");
		    $(".tabs").each(function () {
		        $(this).css("display", "none");
		    });
		    $('#statistics_report').css("display", "block");
		});		
		
		jQuery(document).on("click", ".show_camp", function () {
		    //open_loader();
			jQuery("#hdncampaignid").val(jQuery(this).attr("campid"));
		    var flag = 0;
		    //jQuery("#upload_business_logo_ajax1").show();
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
		                flag = 1;
		            }


		        }
		    });

		    if (flag == 1)
		    {
		        return false;
		    }
		    else
		    {

				/*
		        $.ajax({
		            type: "POST",
		            url: 'campaign_report.php',
		            data: 'id=' + jQuery(this).attr("campid"),
		            async: true,
		            success: function (msg)
		            {
		                close_loader();
		                
		                //jQuery("#campaign_report").css("display", "block");
		                //jQuery("#campaign_report").html(msg);
		                
						jQuery("#newcampaign_report").css("display", "block");
		                jQuery("#newcampaign_report").html(msg);
		                close_loader();
		            }

		        });
		        */
		        
		        jQuery(".campaign_metrics_filter").css("display","block");
		        
		        jQuery(".loader_activation_metrics").css("display","block");
		        jQuery(".loader_point_metrics").css("display","block");
		        jQuery(".loader_qrcode_metrics").css("display","block");
		        jQuery(".loader_revenueage_metrics").css("display","block");
		        jQuery(".loader_revenuetime_metrics").css("display","block");
		        jQuery(".loader_viewshare_metrics").css("display","block");
		        
		        jQuery.ajax({
		            type: "POST",
		            url: '<?php echo WEB_PATH ?>/merchant/newcampaignreport.php',
		            data: 'campaign_id=' + jQuery(this).attr("campid"),
		            
		            success: function (msg)
		            {
						var obj = jQuery.parseJSON(msg);
						
						jQuery("#opt_filter_activation_metrics").empty().append(obj.options_loc);
						jQuery("#opt_filter_point_metrics").empty().append(obj.options_loc);
						jQuery("#opt_filter_qrcode_metrics").empty().append(obj.options_loc);
						jQuery("#opt_filter_revenueage_metrics").empty().append(obj.options_loc);
						jQuery("#opt_filter_revenuetime_metrics").empty().append(obj.options_loc);
						jQuery("#opt_filter_viewshare_metrics").empty().append(obj.options_loc);
						
						jQuery("#campaign_title").html(obj.campaign_title);
						
						jQuery("#total_campaign_cost").html("$"+obj.total_campaign_cost);
						jQuery("#total_campaign_revenue").html("$"+obj.total_campaign_revenue);
						
						
						var activation_code_issued = [obj.activation_code_issued_male,obj.activation_code_issued_female];
						var reserved_by_existing = [obj.reserved_by_exsting_customer_male,obj.reserved_by_exsting_customer_female];
						var reserved_by_new = [obj.reserved_by_new_customer_male,obj.reserved_by_new_customer_female];
						var redeemed_by_existing = [obj.redeemed_by_exsting_customer_male,obj.redeemed_by_exsting_customer_female];
						var redeemed_by_new = [obj.redeemed_by_new_customer_male,obj.redeemed_by_new_customer_female];
						var activation_code_not_redeemed = [obj.activation_code_not_redeemed_male,obj.activation_code_not_redeemed_female];
						
						var categories = ['Male','Female'];
						
						jQuery("#campaign_activation_code").css("display","block");
						jQuery("#campaign_point").css("display","block");
						jQuery("#campaign_qrcode").css("display","block");
						jQuery("#revenue_metrics_byage").css("display","block");
						jQuery("#revenue_metrics_bytime").css("display","block");
						jQuery("#campaign_view_share_chart_container").css("display","block");
						
						campaign_activation_code_metrics = new Highcharts.Chart({
							chart: {
								renderTo: 'campaign_activation_code'
							},
							title: {
								text: 'Campaign Activation Code Metrics',
								x: -20 //center
							},
						   exporting: { enabled: false },
							subtitle: {
								text: '',
								x: -20
							},
							credits: {
								enabled: false
							},
							xAxis:{
								title: {
									text: 'Customer'
								},
								categories: categories
							},
							yAxis: [{ // Primary yAxis
								title: {
									text: 'Activation Code'
								},
								min: 0,
								gridLineWidth: 0,
								lineWidth:1
							}],       
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'middle',
								borderWidth: 0
							},
							series: [{
								name: 'Activation code issued',
								color: '#4872AD',
								type: 'column',
							   data: activation_code_issued
							},{
								name: 'Reserved by existing customer',
								color: '#C0504D',
								type: 'column',
								data: reserved_by_existing
							},{
								name: 'Reserved by new customer',
								color: '#9BBB59',
								type: 'column',
								data: reserved_by_new
							},{
								name: 'Redeemed by existing customer',
								color: '#8064A2',
								type: 'column',
								data: redeemed_by_existing
							},{
								name: 'Redeemed by new customer',
								color: '#459FB7',
								type: 'column',
								data: redeemed_by_new
							},{
								name: 'Activation code not redeemed',
								color: '#C77938',
								type: 'column',
								data: activation_code_not_redeemed
							}
							]
						});
		                
		                var total_point_spent = [obj.total_point_spent_male,obj.total_point_spent_female];
						var campaign_referral = [obj.campaign_referral_male,obj.campaign_referral_female];
						var campaign_redeemption = [obj.campaign_redeemption_male,obj.campaign_redeemption_female];
						var application_fee = [obj.application_fee_male,obj.application_fee_female];
						
						var categories = ['Male','Female'];
						
						campaign_point_metrics = new Highcharts.Chart({
							chart: {
								renderTo: 'campaign_point'
							},
							title: {
								text: 'Campaign Points Metrics',
								x: -20 //center
							},
						   exporting: { enabled: false },
							subtitle: {
								text: '',
								x: -20
							},
							credits: {
								enabled: false
							},
							xAxis:{
								title: {
									text: 'Customer'
								},
								categories: categories
							},
							yAxis: [{ // Primary yAxis
								title: {
									text: 'Points'
								},
								min: 0,
								gridLineWidth: 0,
								lineWidth:1
							}],       
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'middle',
								borderWidth: 0
							},
							series: [{
								name: 'Total point spent',
								color: '#4872AD',
								type: 'column',
							   data: total_point_spent
							},{
								name: 'Campaign referral',
								color: '#C0504D',
								type: 'column',
								data: campaign_referral
							},{
								name: 'Campaign redeemption',
								color: '#9BBB59',
								type: 'column',
								data: campaign_redeemption
							},{
								name: 'Application fee',
								color: '#8064A2',
								type: 'column',
								data: application_fee
							}
							]
						});
						
						var total_scan = [obj.total_scan];
						var unique_scan = [obj.unique_scan];
						
						var categories = ['QR Code Scan'];
						
						campaign_qrcode_metrics = new Highcharts.Chart({
							chart: {
								renderTo: 'campaign_qrcode'
							},
							title: {
								text: 'Campaign QR Code Scan Metrics',
								x: -20 //center
							},
						   exporting: { enabled: false },
							subtitle: {
								text: '',
								x: -20
							},
							credits: {
								enabled: false
							},
							xAxis:{
								title: {
									text: ''
								},
								categories: categories
							},
							yAxis: [{ // Primary yAxis
								title: {
									text: 'No Of Scan'
								},
								min: 0,
								gridLineWidth: 0,
								lineWidth:1
							}],       
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'middle',
								borderWidth: 0
							},
							series: [{
								name: 'Total Scans',
								color: '#4872AD',
								type: 'column',
							   data: total_scan
							},{
								name: 'Unique Scans',
								color: '#C0504D',
								type: 'column',
								data: unique_scan
							}
							]
						});
						
						var categories = ['17 & below', '18-24', '25-44', '45-54', '55-64', '65+'];
						var existing_customer = [25,45,20,15,5,0];
						var new_customer = [35,35,10,10,5,5];
						var female = [40,50,20,20,8,2];
						var male = [20,30,10,5,2,3];
						
						revenue_metrics_byage = new Highcharts.Chart({
							chart: {
								renderTo: 'revenue_metrics_byage',
								//plotBorderColor: 'rgb(69, 114, 167)',
								//plotBorderWidth:2
							},
							title: {
								text: 'Revenue Metrics By Age Distribution',
								x: -20 //center
							},
							subtitle: {
								text: '',
								x: -20
							},
							 exporting: { enabled: false },
							credits: {
								enabled: false
							},
							xAxis:{
								title: {
									text: 'Age'
								},
								categories: categories
							},
							yAxis: [{ // Primary yAxis
								title: {
									text: 'Revenue'
								},
								min: 0,
								gridLineWidth: 0,
								lineWidth:1
							}],       
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'middle',
								borderWidth: 0
							},       
							series: [{
								name: 'Existing Customer',
								color: '#9BBB59',
								type: 'column',
								data: existing_customer
							},{
								name: 'New Customer',
								color: '#C0504D',
								type: 'column',
								data: new_customer
							},{
								name: 'Female',
								color: '#F59240',
								type: 'spline',
								data: female
							},{
								name: 'Male',
								color: '#46AAC4',
								type: 'spline',
								data: male
							}]
						});
						
						var categories = ['12.00 AM-11.00 AM', '11.01 AM-03.00 PM', '03:01 PM-07.00 PM', '07.01 PM-11.99 PM'];
						var existing_customer = [35,25,30,10];
						var new_customer = [40,30,20,10];
						var female = [45,10,20,15];
						var male = [35,45,30,5];
						
						revenue_metrics_bytime = new Highcharts.Chart({
							chart: {
								renderTo: 'revenue_metrics_bytime',
								//plotBorderColor: 'rgb(69, 114, 167)',
								//plotBorderWidth:2
							},
							title: {
								text: 'Revenue Metrics By Time Zone',
								x: -20 //center
							},
							subtitle: {
								text: '',
								x: -20
							},
							 exporting: { enabled: false },
							credits: {
								enabled: false
							},
							xAxis:{
								title: {
									text: 'Timezone'
								},
								categories: categories
							},
							yAxis: [{ // Primary yAxis
								title: {
									text: 'Revenue'
								},
								min: 0,
								gridLineWidth: 0,
								lineWidth:1
							}],       
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'middle',
								borderWidth: 0
							},
							series: [{
								name: 'Existing Customer',
								color: '#9BBB59',
								type: 'column',
								data: existing_customer
							},{
								name: 'New Customer',
								color: '#C0504D',
								type: 'column',
								data: new_customer
							},{
								name: 'Female',
								color: '#F59240',
								type: 'spline',
								data: female
							},{
								name: 'Male',
								color: '#46AAC4',
								type: 'spline',
								data: male
							}]
						});
						
						var categories =  ['Email', 'Facebook', 'Twitter', 'Google+', 'Other (Medium)'];
						var pageview = [obj.view_email,obj.view_facebook,obj.view_twitter,obj.view_googleplus,obj.view_other];
						var shareview = [obj.share_email,obj.share_facebook,obj.share_twitter,obj.share_googleplus,obj.share_other];
						
						campaign_view_share_chart_container = new Highcharts.Chart({
							chart: {
								renderTo: 'campaign_view_share_chart_container',
								type: 'bar',
								
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false
								
							},
							title: {
								text: 'Campaign Page View and Share Metrics'
							},
							 exporting: { enabled: false },
							xAxis: {
								categories: categories,
								title: {
									text: null
								}
							},
							yAxis: {
								min: 0,
								gridLineWidth: 0,
								title: {
									text: 'Total Count',
									align: 'middle'
								},
								labels: {
									overflow: 'justify'
								}
							},
							tooltip: {
								valueSuffix: '',
								enabled: false
							},
							plotOptions: {
								bar: {
									dataLabels: {
										enabled: true
									}
								}
							},
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'top',
								
								floating: true,
								borderWidth: 1,
								backgroundColor: '#FFFFFF',
								shadow: true
							},
							credits: {
								enabled: false
							},
							series: [{
								name: 'Page View',
								data: pageview
							},
							{
								name: 'share',
								data: shareview
							}]
						});
		                
		                jQuery(".loader_activation_metrics").css("display","none");
						jQuery(".loader_point_metrics").css("display","none");
						jQuery(".loader_qrcode_metrics").css("display","none");
						jQuery(".loader_revenueage_metrics").css("display","none");
						jQuery(".loader_revenuetime_metrics").css("display","none");
						jQuery(".loader_viewshare_metrics").css("display","none");
		                
		                //close_loader();
		            }

		        });
		        
		        $("#sidemenu li a").each(function () {
		            $(this).removeClass("current");
		        });
		        /*
		        $("li.campaign_report a").addClass("current");
		        */
		        $("li.newcampaign_report a").addClass("current");
		        var cls = $(this).parent().attr("class");
		        $(".tabs").each(function () {
		            $(this).css("display", "none");
		        });
				/*
		        $('#campaign_report').css("display", "block");
				*/
				$('#newcampaign_report').css("display", "block");
		        var str_text = $(this).text();
				/*	
		        $(".campaign_report").css("display", "block");
		        $(".campaign_report a").text(str_text);
		        */
		        $(".newcampaign_report").css("display", "block");
		        $(".newcampaign_report a").text(str_text);
		        $("#close_span").text("");
		        $("#close_span").append('<span id="close_campaign" title="Close" >X</span>');


		    }

		    //jQuery("#upload_business_logo_ajax1").hide();
		});

		jQuery("#opt_filter_activation_metrics").live("change",function(){
			
			var campaign_activation_code_metrics1=campaign_activation_code_metrics;
			
			var flag = 0;
		    //jQuery("#upload_business_logo_ajax1").show();
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
		                flag = 1;
		            }


		        }
		    });

		    if (flag == 1)
		    {
		        return false;
		    }
		    else
		    {
				jQuery('.loader_activation_metrics').css("display","block");
				var opt_filter_loc = jQuery(this).val();
				
				jQuery.ajax({
		            type: "POST",
		            url: '<?php echo WEB_PATH ?>/merchant/newcampaignreport.php',
		            data: 'activation=yes&campaign_id=' + jQuery("#hdncampaignid").val()+'&location_id='+opt_filter_loc,
		            
		            success: function (msg)
		            {
						var obj = jQuery.parseJSON(msg);
						
						var activation_code_issued = [obj.activation_code_issued_male,obj.activation_code_issued_female];
						var reserved_by_existing = [obj.reserved_by_exsting_customer_male,obj.reserved_by_exsting_customer_female];
						var reserved_by_new = [obj.reserved_by_new_customer_male,obj.reserved_by_new_customer_female];
						var redeemed_by_existing = [obj.redeemed_by_exsting_customer_male,obj.redeemed_by_exsting_customer_female];
						var redeemed_by_new = [obj.redeemed_by_new_customer_male,obj.redeemed_by_new_customer_female];
						var activation_code_not_redeemed = [obj.activation_code_not_redeemed_male,obj.activation_code_not_redeemed_female];
						
						var categories = ['Male','Female'];
						
						console.log(campaign_activation_code_metrics1);
						
						if(typeof campaign_activation_code_metrics1 === 'undefined')
						{
								campaign_activation_code_metrics = new Highcharts.Chart({
								chart: {
									renderTo: 'campaign_activation_code'
								},
								title: {
									text: 'Campaign Activation Code Metrics',
									x: -20 //center
								},
							   exporting: { enabled: false },
								subtitle: {
									text: '',
									x: -20
								},
								credits: {
									enabled: false
								},
								xAxis:{
									title: {
										text: 'Customer'
									},
									categories: categories
								},
								yAxis: [{ // Primary yAxis
									title: {
										text: 'Activation Code'
									},
									min: 0,
									gridLineWidth: 0,
									lineWidth:1
								}],       
								legend: {
									layout: 'vertical',
									align: 'right',
									verticalAlign: 'middle',
									borderWidth: 0
								},
								series: [{
									name: 'Activation code issued',
									color: '#4872AD',
									type: 'column',
								   data: activation_code_issued
								},{
									name: 'Reserved by existing customer',
									color: '#C0504D',
									type: 'column',
									data: reserved_by_existing
								},{
									name: 'Reserved by new customer',
									color: '#9BBB59',
									type: 'column',
									data: reserved_by_new
								},{
									name: 'Redeemed by existing customer',
									color: '#8064A2',
									type: 'column',
									data: redeemed_by_existing
								},{
									name: 'Redeemed by new customer',
									color: '#459FB7',
									type: 'column',
									data: redeemed_by_new
								},{
									name: 'Activation code not redeemed',
									color: '#C77938',
									type: 'column',
									data: activation_code_not_redeemed
								}
								]
							});
						}
						else
						{
							campaign_activation_code_metrics1.series[0].setData(activation_code_issued);
							campaign_activation_code_metrics1.series[1].setData(reserved_by_existing);
							campaign_activation_code_metrics1.series[2].setData(reserved_by_new);
							campaign_activation_code_metrics1.series[3].setData(redeemed_by_existing);
							campaign_activation_code_metrics1.series[4].setData(redeemed_by_new);
							campaign_activation_code_metrics1.series[5].setData(activation_code_not_redeemed);
						}
						jQuery('.loader_activation_metrics').css("display","none");
					}
				});	
			}
		});
		
		jQuery("#opt_filter_point_metrics").live("change",function(){
			
			var campaign_point_metrics1=campaign_point_metrics;
			
			var flag = 0;
		    //jQuery("#upload_business_logo_ajax1").show();
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
		                flag = 1;
		            }


		        }
		    });

		    if (flag == 1)
		    {
		        return false;
		    }
		    else
		    {
				jQuery('.loader_point_metrics').css("display","block");
				var opt_filter_loc = jQuery(this).val();
				
				jQuery.ajax({
		            type: "POST",
		            url: '<?php echo WEB_PATH ?>/merchant/newcampaignreport.php',
		            data: 'point=yes&campaign_id=' + jQuery("#hdncampaignid").val()+'&location_id='+opt_filter_loc,
		            
		            success: function (msg)
		            {
						var obj = jQuery.parseJSON(msg);
						
						var total_point_spent = [obj.total_point_spent_male,obj.total_point_spent_female];
						var campaign_referral = [obj.campaign_referral_male,obj.campaign_referral_female];
						var campaign_redeemption = [obj.campaign_redeemption_male,obj.campaign_redeemption_female];
						var application_fee = [obj.application_fee_male,obj.application_fee_female];
						
						var categories = ['Male','Female'];
						
						console.log(campaign_point_metrics1);
						
						if(typeof campaign_point_metrics1 === 'undefined')
						{
								campaign_point_metrics = new Highcharts.Chart({
								chart: {
									renderTo: 'campaign_point'
								},
								title: {
									text: 'Campaign Points Metrics',
									x: -20 //center
								},
							   exporting: { enabled: false },
								subtitle: {
									text: '',
									x: -20
								},
								credits: {
									enabled: false
								},
								xAxis:{
									title: {
										text: 'Customer'
									},
									categories: categories
								},
								yAxis: [{ // Primary yAxis
									title: {
										text: 'Points'
									},
									min: 0,
									gridLineWidth: 0,
									lineWidth:1
								}],       
								legend: {
									layout: 'vertical',
									align: 'right',
									verticalAlign: 'middle',
									borderWidth: 0
								},
								series: [{
									name: 'Total point spent',
									color: '#4872AD',
									type: 'column',
								   data: total_point_spent
								},{
									name: 'Campaign referral',
									color: '#C0504D',
									type: 'column',
									data: campaign_referral
								},{
									name: 'Campaign redeemption',
									color: '#9BBB59',
									type: 'column',
									data: campaign_redeemption
								},{
									name: 'Application fee',
									color: '#8064A2',
									type: 'column',
									data: application_fee
								}
								]
							});
						}
						else
						{
							campaign_point_metrics1.series[0].setData(total_point_spent);
							campaign_point_metrics1.series[1].setData(campaign_referral);
							campaign_point_metrics1.series[2].setData(campaign_redeemption);
							campaign_point_metrics1.series[3].setData(application_fee);

						}
						jQuery('.loader_point_metrics').css("display","none");
					}
				});	
			}
		});
		
		jQuery("#opt_filter_qrcode_metrics").live("change",function(){
			
			var campaign_qrcode_metrics1=campaign_qrcode_metrics;
			
			var flag = 0;
		    //jQuery("#upload_business_logo_ajax1").show();
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
		                flag = 1;
		            }


		        }
		    });

		    if (flag == 1)
		    {
		        return false;
		    }
		    else
		    {
				jQuery('.loader_qrcode_metrics').css("display","block");
				var opt_filter_loc = jQuery(this).val();
				
				jQuery.ajax({
		            type: "POST",
		            url: '<?php echo WEB_PATH ?>/merchant/newcampaignreport.php',
		            data: 'qrcode=yes&campaign_id=' + jQuery("#hdncampaignid").val()+'&location_id='+opt_filter_loc,
		            
		            success: function (msg)
		            {
						var obj = jQuery.parseJSON(msg);
						
						var total_scan = [obj.total_scan];
						var unique_scan = [obj.unique_scan];
						
						var categories = ['QR Code Scan'];
						
						console.log(campaign_qrcode_metrics1);
						
						if(typeof campaign_qrcode_metrics1 === 'undefined')
						{
								campaign_qrcode_metrics = new Highcharts.Chart({
								chart: {
									renderTo: 'campaign_qrcode'
								},
								title: {
									text: 'Campaign QR Code Scan Metrics',
									x: -20 //center
								},
							   exporting: { enabled: false },
								subtitle: {
									text: '',
									x: -20
								},
								credits: {
									enabled: false
								},
								xAxis:{
									title: {
										text: ''
									},
									categories: categories
								},
								yAxis: [{ // Primary yAxis
									title: {
										text: 'No Of Scan'
									},
									min: 0,
									gridLineWidth: 0,
									lineWidth:1
								}],       
								legend: {
									layout: 'vertical',
									align: 'right',
									verticalAlign: 'middle',
									borderWidth: 0
								},
								series: [{
									name: 'Total Scans',
									color: '#4872AD',
									type: 'column',
								   data: total_scan
								},{
									name: 'Unique Scans',
									color: '#C0504D',
									type: 'column',
									data: unique_scan
								}
								]
							});
						}
						else
						{
							campaign_qrcode_metrics1.series[0].setData(total_scan);
							campaign_qrcode_metrics1.series[1].setData(unique_scan);
						}
						jQuery('.loader_qrcode_metrics').css("display","none");
					}
				});	
			}
		});
		
		jQuery("#opt_filter_viewshare_metrics").live("change",function(){
			
			var campaign_view_share_chart_container1=campaign_view_share_chart_container;
			
			var flag = 0;
		    //jQuery("#upload_business_logo_ajax1").show();
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
		                flag = 1;
		            }


		        }
		    });

		    if (flag == 1)
		    {
		        return false;
		    }
		    else
		    {
				jQuery('.loader_viewshare_metrics').css("display","block");
				var opt_filter_loc = jQuery(this).val();
				
				jQuery.ajax({
		            type: "POST",
		            url: '<?php echo WEB_PATH ?>/merchant/newcampaignreport.php',
		            data: 'pageviewshare=yes&campaign_id=' + jQuery("#hdncampaignid").val()+'&location_id='+opt_filter_loc,
		            
		            success: function (msg)
		            {
						var obj = jQuery.parseJSON(msg);
						
						var categories =  ['Email', 'Facebook', 'Twitter', 'Google+', 'Other (Medium)'];
						var pageview = [obj.view_email,obj.view_facebook,obj.view_twitter,obj.view_googleplus,obj.view_other];
						var shareview = [obj.share_email,obj.share_facebook,obj.share_twitter,obj.share_googleplus,obj.share_other];
						
						console.log(campaign_view_share_chart_container1);
						
						if(typeof campaign_view_share_chart_container1 === 'undefined')
						{
							
							
							campaign_view_share_chart_container = new Highcharts.Chart({
								chart: {
									renderTo: 'campaign_view_share_chart_container',
									type: 'bar',
									
									plotBackgroundColor: null,
									plotBorderWidth: null,
									plotShadow: false
									
								},
								title: {
									text: 'Campaign Page View and Share Metrics'
								},
								 exporting: { enabled: false },
								xAxis: {
									categories: categories,
									title: {
										text: null
									}
								},
								yAxis: {
									min: 0,
									gridLineWidth: 0,
									title: {
										text: 'Total Count',
										align: 'middle'
									},
									labels: {
										overflow: 'justify'
									}
								},
								tooltip: {
									valueSuffix: '',
									enabled: false
								},
								plotOptions: {
									bar: {
										dataLabels: {
											enabled: true
										}
									}
								},
								legend: {
									layout: 'vertical',
									align: 'right',
									verticalAlign: 'top',
									
									floating: true,
									borderWidth: 1,
									backgroundColor: '#FFFFFF',
									shadow: true
								},
								credits: {
									enabled: false
								},
								series: [{
									name: 'Page View',
									data: pageview
								},
								{
									name: 'share',
									data: shareview
								}]
							});
						}
						else
						{
							campaign_view_share_chart_container1.series[0].setData(pageview);
							campaign_view_share_chart_container1.series[1].setData(shareview);
						}
						jQuery('.loader_viewshare_metrics').css("display","none");
					}
				});	
			}
		});
		
		jQuery(document).ready(function () {

		    jQuery(".highcharts-axis-labels .lbl_qrcode").on("click", function () {
				
		        if ($(this).text() == "Jan" || $(this).text() == "Feb" || $(this).text() == "Mar" || $(this).text() == "Apr" || $(this).text() == "May" || $(this).text() == "Jun" || $(this).text() == "Jul" || $(this).text() == "Aug" || $(this).text() == "Sep" || $(this).text() == "Oct" || $(this).text() == "Nov" || $(this).text() == "Dec")
		        {

		            var arr_val = $(this).attr("val").split("-");
		            //                      


		            $("#Notification2PopUpContainer #spanp1").html("Total Scans By Male");
		            $("#Notification2PopUpContainer #span1").html(arr_val[1] + "%");

		            $("#Notification2PopUpContainer #spanp2").html("Total Scans By Female");
		            $("#Notification2PopUpContainer #span2").html(arr_val[2] + "%");
		            $("#Notification2PopUpContainer #spanp3").html("Total Scans By Unknown");
		            $("#Notification2PopUpContainer #span3").html(arr_val[3].toString() + "%");
		            $("#Notification2PopUpContainer #spanp4").html("<?php echo $merchant_msg['report']['total_num_unique_scan']; ?>");
		            $("#Notification2PopUpContainer #span4").html(arr_val[4].toString());
		            $("#Notification2PopUpContainer #spanp5").html("<?php echo $merchant_msg['report']['total_num_scans']; ?>");
		            $("#Notification2PopUpContainer #span5").html(arr_val[5].toString());
		            jQuery.fancybox({
		                content: jQuery('#Notification2mainContainer').html(),
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
		                beforeShow: function () {

		                    $(".fancybox-inner").css("overflow", "hidden");
		                },
		                afterShow: function () {
		                    $(".fancybox-inner").css("overflow", "hidden");
		                },
		            });

		            //open_popup('Notification1');
		        }
		    });
		});

		function draw_all_chart(loc_id)
		{
			
		    if (loc_id == 0)
		    {
		        //alert("in summerised data");
		        //alert(jQuery("#summerisedidgender").attr("genderdata")+ "summerised data");

		        //alert(V1+"==="+V2);
		        //console.log(jQuery("#summerisedidgender").length);
		        if (jQuery("#summerisedidgender").length != 0)
		        {
					
		            var g = (jQuery("#summerisedidgender").attr("genderdata")).split("-");
		            var V1 = parseFloat(g[0]);
		            var V2 = parseFloat(g[1]);
		            
		            if(jQuery("#summerisedidgender").html().trim()=="")
		            {
		            campaign_genger_chart_container = new Highcharts.Chart({
		                chart: {
		                    renderTo: 'summerisedidgender',
		                    borderWidth: 1,
		                    plotBackgroundColor: null,
		                    plotBorderWidth: null,
		                    plotShadow: false

		                },
		                title: {
		                    text: '<?php echo $merchant_msg['report']['cust_by_gender']; ?>',
		                    align: 'center',
		                    verticalAlign: 'middle',
		                    y: -85
		                },
		                tooltip: {
		                    pointFormat: '<b>{point.percentage:.1f}%</b>'
		                },
		                plotOptions: {
		                    pie: {
		                        dataLabels: {
		                            enabled: true,
		                            distance: -30,
		                            style: {
		                                fontWeight: 'bold',
		                                color: 'white',
		                                textShadow: '0px 1px 2px black'
		                            },
		                            formatter: function () {
		                                if (this.y != 0) {
		                                    return this.point.name;
		                                } else {
		                                    return null;
		                                }
		                            }
		                        },
		                        startAngle: -90,
		                        endAngle: 90,
		                        center: ['50%', '75%']
		                    }
		                },
		                credits: {
		                    enabled: false
		                },
		                series: [{
		                        type: 'pie',
		                        showInLegend: false,
		                        //name: '',
		                        innerSize: '50%',
		                        data: [
		                            ["Male", V1], ['Female', V2],
		                        ]
		                    }]
		            });
		           }
		           else
		           {
						campaign_genger_chart_container=jQuery('#summerisedidgender').highcharts();
						campaign_genger_chart_container.series[0].setData([V1,V2]);
				   }

		        }
		        if (jQuery("#summerisedidage").length != 0)
		        {
		            var male_arr = (jQuery("#summerisedidage").attr("maledata")).split("-");
		            var female_arr = (jQuery("#summerisedidage").attr("femaledata")).split("-");
		            categories = ['17 Or Below', '18 to 24', '25 to 44', '45 to 54',
		                '55 to 64', '65+'];
		             //console.log(jQuery("#summerisedidage").html());   
		            if(jQuery("#summerisedidage").html().trim()=="")
					{
	    
						campaign_age_chart_container = new Highcharts.Chart({
							chart: {
								renderTo: 'summerisedidage',
								borderWidth: 1,
								type: 'bar'

							},
							title: {
								text: '<?php echo $merchant_msg['report']['cust_by_age']; ?>'
							},
							xAxis: [{
									title: {
										text: 'Age Distribution'
									},
									categories: categories,
									reversed: false
								}, {// mirror axis on right side
									opposite: true,
									reversed: false,
									categories: categories,
									linkedTo: 0
								}],
							yAxis: {
								title: {
									text: null
								},
								labels: {
									formatter: function () {
										return (Math.abs(this.value)) + '%';
									}
								},
								min: -100,
								max: 100
							},
							plotOptions: {
								series: {
									stacking: 'normal'
								}
							},
							tooltip: {
								formatter: function () {
									return '<b>' + this.series.name + ' Age ( ' + this.point.category + ' ) : </b>' +
											Highcharts.numberFormat(Math.abs(this.point.y), 0) + '%';
								}
							},
							credits: {
								enabled: false
							},
							series: [{
									name: 'Male',
									data: [parseInt(male_arr[0]), parseInt(male_arr[1]), parseInt(male_arr[2]), parseInt(male_arr[3]), parseInt(male_arr[4]), parseInt(male_arr[5])]
								}, {
									name: 'Female',
									data: [-female_arr[0], -female_arr[1], -female_arr[2], -female_arr[3], -female_arr[4], -female_arr[5]]
								}]
						});
					}
					else
					{
						campaign_age_chart_container=jQuery('#summerisedidage').highcharts();
						campaign_age_chart_container.series[0].setData([parseInt(male_arr[0]), parseInt(male_arr[1]), parseInt(male_arr[2]), parseInt(male_arr[3]), parseInt(male_arr[4]), parseInt(male_arr[5])]);
						campaign_age_chart_container.series[1].setData([-female_arr[0], -female_arr[1], -female_arr[2], -female_arr[3], -female_arr[4], -female_arr[5]]);
					}	
		        }
		        /**** initializing sharing chart *********/
		        if (jQuery("#summerisedshare").length != 0)
		        {
		            only_one = (jQuery("#summerisedshare").attr("sharingdata")).split("-");
		            
		            if(jQuery("#summerisedshare").html().trim()=="")
					{

							campaign_share_chart_container = new Highcharts.Chart({
								chart: {
									renderTo: 'summerisedshare',
									type: 'bar',
									borderWidth: 1,
									plotBackgroundColor: null,
									plotBorderWidth: null,
									plotShadow: false

								},
								title: {
									text: '<?php echo $merchant_msg['report']['sharechart_heading']; ?>'
								},
								xAxis: {
									categories: ['<?php echo $merchant_msg['report'][$only_keys[0]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[2]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[4]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[6]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[8]]; ?>'],
									title: {
										text: null
									}
								},
								yAxis: {
									min: 0,
									title: {
										text: 'Total Count',
										align: 'middle'
									},
									labels: {
										overflow: 'justify'
									}
								},
								tooltip: {
									valueSuffix: '',
									enabled: false
								},
								plotOptions: {
									bar: {
										dataLabels: {
											enabled: true
										}
									}
								},
								legend: {
									layout: 'vertical',
									align: 'right',
									verticalAlign: 'top',
									x: -40,
									y: 100,
									floating: true,
									borderWidth: 1,
									backgroundColor: '#FFFFFF',
									shadow: true
								},
								credits: {
									enabled: false
								},
								series: [{
										showInLegend: false,
										name: '<?php echo $merchant_msg['report']['sharechart_heading2']; ?>',
										data: [parseFloat(only_one[0]), parseFloat(only_one[1]), parseFloat(only_one[2]), parseFloat(only_one[3]), parseFloat(only_one[4])],
									}]
							});
					}
					else
					{
						campaign_share_chart_container=jQuery('#summerisedshare').highcharts();
						campaign_share_chart_container.series[0].setData([parseFloat(only_one[0]), parseFloat(only_one[1]), parseFloat(only_one[2]), parseFloat(only_one[3]), parseFloat(only_one[4])]);
					}		
		        }
		        /**** initializing sharing chart *********/

		        /**** initializing sharing view chart *********/
		        /**** initializing sharing view chart *********/
		        if (jQuery("#summerisedview").length != 0)
		        {

		            //	alert(jQuery("#summerisedview").attr("sharingview"));
		            only_one = (jQuery("#summerisedview").attr("sharingview")).split("-");
		            // alert(only_one[0]+"=="+only_one[1]+"=="+only_one[2]+"=="+only_one[3]);
					
					if(jQuery("#summerisedview").html().trim()=="")
					{

						campaign_view_chart_container = new Highcharts.Chart({
							chart: {
								renderTo: 'summerisedview',
								type: 'bar',
								borderWidth: 1,
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false

							},
							title: {
								text: '<?php echo $merchant_msg['report']['pageview_heading']; ?>'
							},
							xAxis: {
								categories: ['<?php echo $merchant_msg['report'][$only_keys[1]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[3]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[5]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[7]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[9]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[10]] ?>', '<?php echo $merchant_msg['report'][$only_keys[11]] ?>'],
								title: {
									text: null
								}
							},
							yAxis: {
								min: 0,
								title: {
									text: 'Total Count',
									align: 'middle'
								},
								labels: {
									overflow: 'justify'
								}
							},
							tooltip: {
								valueSuffix: '',
								enabled: false
							},
							plotOptions: {
								bar: {
									dataLabels: {
										enabled: true
									}
								}
							},
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'top',
								x: -40,
								y: 100,
								floating: true,
								borderWidth: 1,
								backgroundColor: '#FFFFFF',
								shadow: true
							},
							credits: {
								enabled: false
							},
							series: [{
									showInLegend: false,
									name: '<?php echo $merchant_msg['report']['pageview_heading2']; ?>',
									data: [parseFloat(only_one[0]), parseFloat(only_one[1]), parseFloat(only_one[2]), parseFloat(only_one[3]), parseFloat(only_one[4]), parseFloat(only_one[5]), parseInt(only_one[6])],
								}]
						});
					}
					else
					{
						campaign_view_chart_container=jQuery('#summerisedview').highcharts();
						campaign_view_chart_container.series[0].setData([parseFloat(only_one[0]), parseFloat(only_one[1]), parseFloat(only_one[2]), parseFloat(only_one[3]), parseFloat(only_one[4]), parseFloat(only_one[5]), parseInt(only_one[6])]);
					}
		        }
		        /**** initializing sharing view chart *********/

		        /**** initializing rating chart *********/
		        if ($('#summerisedrating').length != 0)
		        {

		            only_one = (jQuery('#summerisedrating').attr("ratingdata")).split("-");
		            
		            if(jQuery("#summerisedrating").html().trim()=="")
					{
	
						campaign_rating_chart_container = new Highcharts.Chart({
							chart: {
								renderTo: 'summerisedrating',
								type: 'bar',
								borderWidth: 1,
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false

							},
							title: {
								text: '<?php echo $merchant_msg['report']['rating_heading'] ?>'
							},
							xAxis: {
								title: {
									text: 'Rating Type'
								},
								categories: ['<?php echo $merchant_msg['report']['rating_excellent'] ?>', '<?php echo $merchant_msg['report']['rating_very_good'] ?>', '<?php echo $merchant_msg['report']['rating_good'] ?>', '<?php echo $merchant_msg['report']['rating_fair'] ?>', '<?php echo $merchant_msg['report']['rating_poor'] ?>'],
								title: {
									text: null
								}
							},
							yAxis: {
								min: 0,
								title: {
									text: '<?php echo $merchant_msg['report']['rating_heading3'] ?>',
									align: 'middle'
								},
								labels: {
									overflow: 'justify'
								}
							},
							tooltip: {
								valueSuffix: '',
								enabled: false
							},
							plotOptions: {
								bar: {
									dataLabels: {
										enabled: true
									}
								}
							},
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'top',
								x: -40,
								y: 100,
								floating: true,
								borderWidth: 1,
								backgroundColor: '#FFFFFF',
								shadow: true
							},
							credits: {
								enabled: false
							},
							series: [{
									showInLegend: false,
									name: '<?php echo $merchant_msg['report']['rating_heading2'] ?>',
									data: [parseFloat(only_one[4]), parseFloat(only_one[3]), parseFloat(only_one[2]), parseFloat(only_one[1]), parseFloat(only_one[0])],
								}]
						});
					}
					else
					{
						campaign_rating_chart_container=jQuery('#summerisedrating').highcharts();
						campaign_rating_chart_container.series[0].setData([parseFloat(only_one[4]), parseFloat(only_one[3]), parseFloat(only_one[2]), parseFloat(only_one[1]), parseFloat(only_one[0])]);
					}	

		        }
		        /**** initializing rating chart *********/
		        
		    }
		    else
		    {

		        if (jQuery("#locationidgender_" + loc_id).length != 0)
		        {
		            var g = (jQuery("#locationidgender_" + loc_id).attr("genderdata")).split("-");
		            var V1 = parseFloat(g[0]);
		            var V2 = parseFloat(g[1]);
		            
		            if(jQuery("#locationidgender_"+ loc_id).html().trim()=="")
					{
						campaign_loc_genger_chart_container[loc_id] = new Highcharts.Chart({
							chart: {
								renderTo: 'locationidgender_' + loc_id,
								borderWidth: 1,
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false

							},
							title: {
								text: '<?php echo $merchant_msg['report']['cust_by_gender']; ?>',
								align: 'center',
								verticalAlign: 'middle',
								y: -85
							},
							tooltip: {
								pointFormat: '<b>{point.percentage:.1f}%</b>'
							},
							plotOptions: {
								pie: {
									dataLabels: {
										enabled: true,
										distance: -30,
										style: {
											fontWeight: 'bold',
											color: 'white',
											textShadow: '0px 1px 2px black'
										},
										formatter: function () {
											if (this.y != 0) {
												return this.point.name;
											} else {
												return null;
											}
										}
									},
									startAngle: -90,
									endAngle: 90,
									center: ['50%', '75%']
								}
							},
							credits: {
								enabled: false
							},
							series: [{
									type: 'pie',
									showInLegend: false,
									//name: '',
									innerSize: '50%',
									data: [
										["Male", V1], ['Female', V2],
									]
								}]
						});
					}
					else
					{
						campaign_loc_genger_chart_container[loc_id]=jQuery('#locationidgender_'+ loc_id).highcharts();
						campaign_loc_genger_chart_container[loc_id].series[0].setData([V1,V2]);
					}
		        }
		        if (jQuery("#containeragewisegender_" + loc_id).length != 0)
		        {
		            var male_arr = (jQuery("#containeragewisegender_" + loc_id).attr("maledata")).split("-");
		            var female_arr = (jQuery("#containeragewisegender_" + loc_id).attr("femaledata")).split("-");
		            categories = ['17 Or Below', '18 to 24', '25 to 44', '45 to 54',
		                '55 to 64', '65+'];
		            if(jQuery("#containeragewisegender_"+ loc_id).html().trim()=="")
					{    
						campaign_loc_age_chart_container[loc_id] = new Highcharts.Chart({
							chart: {
								renderTo: "containeragewisegender_" + loc_id,
								borderWidth: 1,
								type: 'bar'

							},
							title: {
								text: '<?php echo $merchant_msg['report']['cust_by_age']; ?>'
							},
							xAxis: [{
									title: {
										text: 'Age Distribution'
									},
									categories: categories,
									reversed: false
								}, {// mirror axis on right side
									opposite: true,
									reversed: false,
									categories: categories,
									linkedTo: 0
								}],
							yAxis: {
								title: {
									text: null
								},
								labels: {
									formatter: function () {
										return (Math.abs(this.value)) + '%';
									}
								},
								min: -100,
								max: 100
							},
							plotOptions: {
								series: {
									stacking: 'normal'
								}
							},
							tooltip: {
								formatter: function () {
									return '<b>' + this.series.name + ' Age ( ' + this.point.category + ' ) : </b>' +
											Highcharts.numberFormat(Math.abs(this.point.y), 0) + '%';
								}
							},
							credits: {
								enabled: false
							},
							series: [{
									name: 'Male',
									data: [parseInt(male_arr[0]), parseInt(male_arr[1]), parseInt(male_arr[2]), parseInt(male_arr[3]), parseInt(male_arr[4]), parseInt(male_arr[5])]
								}, {
									name: 'Female',
									data: [-female_arr[0], -female_arr[1], -female_arr[2], -female_arr[3], -female_arr[4], -female_arr[5]]
								}]
						});
					}
					else
					{
						campaign_loc_age_chart_container[loc_id]=jQuery('#containeragewisegender_'+ loc_id).highcharts();
						campaign_loc_age_chart_container[loc_id].series[0].setData([parseInt(male_arr[0]), parseInt(male_arr[1]), parseInt(male_arr[2]), parseInt(male_arr[3]), parseInt(male_arr[4]), parseInt(male_arr[5])]);
						campaign_loc_age_chart_container[loc_id].series[1].setData([-female_arr[0], -female_arr[1], -female_arr[2], -female_arr[3], -female_arr[4], -female_arr[5]]);
					}	
		        }
		        /**** initializing sharing chart *********/
		        if (jQuery("#summerisedshare_" + loc_id).length != 0)
		        {
		            only_one = (jQuery("#summerisedshare_" + loc_id).attr("sharingdata")).split("-");
		            if(jQuery("#summerisedshare_"+ loc_id).html().trim()=="")
					{
						campaign_loc_share_chart_container[loc_id] = new Highcharts.Chart({
							chart: {
								renderTo: "summerisedshare_" + loc_id,
								type: 'bar',
								borderWidth: 1,
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false

							},
							title: {
								text: '<?php echo $merchant_msg['report']['sharechart_heading']; ?>'
							},
							xAxis: {
								categories: ['<?php echo $merchant_msg['report'][$only_keys[0]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[2]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[4]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[6]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[8]]; ?>'],
								title: {
									text: null
								}
							},
							yAxis: {
								min: 0,
								title: {
									text: 'Total Count',
									align: 'middle'
								},
								labels: {
									overflow: 'justify'
								}
							},
							tooltip: {
								valueSuffix: '',
								enabled: false
							},
							plotOptions: {
								bar: {
									dataLabels: {
										enabled: true
									}
								}
							},
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'top',
								x: -40,
								y: 100,
								floating: true,
								borderWidth: 1,
								backgroundColor: '#FFFFFF',
								shadow: true
							},
							credits: {
								enabled: false
							},
							series: [{
									showInLegend: false,
									name: '<?php echo $merchant_msg['report']['sharechart_heading2']; ?>',
									data: [parseFloat(only_one[0]), parseFloat(only_one[1]), parseFloat(only_one[2]), parseFloat(only_one[3]), parseFloat(only_one[4])],
								}]
						});
					}
					else
					{
						campaign_loc_share_chart_container[loc_id]=jQuery('#summerisedshare_'+ loc_id).highcharts();
						campaign_loc_share_chart_container[loc_id].series[0].setData([parseFloat(only_one[0]), parseFloat(only_one[1]), parseFloat(only_one[2]), parseFloat(only_one[3]), parseFloat(only_one[4])]);
					}	
		        }
		        /**** initializing sharing chart *********/

		        /**** initializing sharing view chart *********/
		        /**** initializing sharing view chart *********/
		        if (jQuery("#summerisedview_" + loc_id).length != 0)
		        {
		            //alert('<?php echo $merchant_msg['report'][$only_keys[10]] ?>');
		            //alert(jQuery("#summerisedview").attr("sharingview"));
		            only_one = (jQuery("#summerisedview_" + loc_id).attr("sharingview")).split("-");
		            // alert(only_one[0]+"=="+only_one[1]+"=="+only_one[2]+"=="+only_one[3]);

					if(jQuery("#summerisedview_"+ loc_id).html().trim()=="")
					{
						campaign_loc_view_chart_container[loc_id] = new Highcharts.Chart({
							chart: {
								renderTo: "summerisedview_" + loc_id,
								type: 'bar',
								borderWidth: 1,
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false

							},
							title: {
								text: '<?php echo $merchant_msg['report']['pageview_heading']; ?>'
							},
							xAxis: {
								categories: ['<?php echo $merchant_msg['report'][$only_keys[1]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[3]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[5]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[7]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[9]]; ?>', '<?php echo $merchant_msg['report'][$only_keys[10]] ?>', '<?php echo $merchant_msg['report'][$only_keys[11]] ?>'],
								title: {
									text: null
								}
							},
							yAxis: {
								min: 0,
								title: {
									text: 'Total Count',
									align: 'middle'
								},
								labels: {
									overflow: 'justify'
								}
							},
							tooltip: {
								valueSuffix: '',
								enabled: false
							},
							plotOptions: {
								bar: {
									dataLabels: {
										enabled: true
									}
								}
							},
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'top',
								x: -40,
								y: 100,
								floating: true,
								borderWidth: 1,
								backgroundColor: '#FFFFFF',
								shadow: true
							},
							credits: {
								enabled: false
							},
							series: [{
									showInLegend: false,
									name: '<?php echo $merchant_msg['report']['pageview_heading2']; ?>',
									data: [parseFloat(only_one[0]), parseFloat(only_one[1]), parseFloat(only_one[2]), parseFloat(only_one[3]), parseFloat(only_one[4]), parseFloat(only_one[5]), parseInt(only_one[6])],
								}]
						});
					}
					else
					{
						campaign_loc_view_chart_container[loc_id]=jQuery('#summerisedview_'+ loc_id).highcharts();
						campaign_loc_view_chart_container[loc_id].series[0].setData([parseFloat(only_one[0]), parseFloat(only_one[1]), parseFloat(only_one[2]), parseFloat(only_one[3]), parseFloat(only_one[4]), parseFloat(only_one[5]), parseInt(only_one[6])]);
					}	
		        }
		        /**** initializing sharing view chart *********/

		        /**** initializing rating chart *********/
		        if ($('#summerisedrating_' + loc_id).length != 0)
		        {

		            only_one = (jQuery('#summerisedrating_' + loc_id).attr("ratingdata")).split("-");
		            
		            if(jQuery("#summerisedrating_"+ loc_id).html().trim()=="")
					{
						campaign_loc_rating_chart_container[loc_id] = new Highcharts.Chart({
							chart: {
								renderTo: "summerisedrating_" + loc_id,
								type: 'bar',
								borderWidth: 1,
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false

							},
							title: {
								text: '<?php echo $merchant_msg['report']['rating_heading'] ?>'
							},
							xAxis: {
								title: {
									text: 'Rating Type'
								},
								categories: ['<?php echo $merchant_msg['report']['rating_excellent'] ?>', '<?php echo $merchant_msg['report']['rating_very_good'] ?>', '<?php echo $merchant_msg['report']['rating_good'] ?>', '<?php echo $merchant_msg['report']['rating_fair'] ?>', '<?php echo $merchant_msg['report']['rating_poor'] ?>'],
								title: {
									text: null
								}
							},
							yAxis: {
								min: 0,
								title: {
									text: '<?php echo $merchant_msg['report']['rating_heading3'] ?>',
									align: 'middle'
								},
								labels: {
									overflow: 'justify'
								}
							},
							tooltip: {
								valueSuffix: '',
								enabled: false
							},
							plotOptions: {
								bar: {
									dataLabels: {
										enabled: true
									}
								}
							},
							legend: {
								layout: 'vertical',
								align: 'right',
								verticalAlign: 'top',
								x: -40,
								y: 100,
								floating: true,
								borderWidth: 1,
								backgroundColor: '#FFFFFF',
								shadow: true
							},
							credits: {
								enabled: false
							},
							series: [{
									showInLegend: false,
									name: '<?php echo $merchant_msg['report']['rating_heading2'] ?>',
									data: [parseFloat(only_one[4]), parseFloat(only_one[3]), parseFloat(only_one[2]), parseFloat(only_one[1]), parseFloat(only_one[0])],
								}]
						});
					}
					else
					{
						campaign_loc_rating_chart_container[loc_id]=jQuery('#summerisedrating_'+ loc_id).highcharts();
						campaign_loc_rating_chart_container[loc_id].series[0].setData([parseFloat(only_one[4]), parseFloat(only_one[3]), parseFloat(only_one[2]), parseFloat(only_one[1]), parseFloat(only_one[0])]);
					}
		        }
		        /**** initializing rating chart *********/
		        
		        
		    }
			
		}
		
		jQuery(window).resize(function () {
			console.log("resize");
		    $("#sidemenu li a").each(function () {
		        if ($(this).attr("class") == "current")
		        {
		            cls = $(this).parent().attr("class");
		        }
		    });
		    if (cls == "div_location")
		    {
		        for (var prop in location_gender_chart_container) {

		            if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		            {
		                width = jQuery("#container_" + prop).width();
		                height = jQuery("#container_" + prop).height();
		                var p = parseInt(prop);
		                location_gender_chart_container[p].setSize(width, height, doAnimation = true);
		            }
		        }
		        /*** for age ***/
		        for (var prop in location_age_chart_container) {

		            if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		            {
		                width = jQuery("#containerage_" + prop).width();
		                height = jQuery("#containerage_" + prop).height();
		                var p = parseInt(prop);
		                location_age_chart_container[p].setSize(width, height, doAnimation = true);
		            }
		        }
		        /**** for campaign report ***/
		        for (var prop in location_cmapaign_chart_container) {

		            if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
		            {
		                width = jQuery("#container_report_" + prop).width();
		                height = jQuery("#container_report_" + prop).height();
		                var p = parseInt(prop);
		                location_cmapaign_chart_container[p].setSize(width, height, doAnimation = true);
		            }
		        }

		    }
		    if (cls == "campaign_report")
		    {
				try
		        {
					/**** for gender report ***/
					for (var prop in campaign_loc_genger_chart_container) {
						if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
						{
							width = jQuery("#locationidgender_" + prop).width();
							height = jQuery("#locationidgender_" + prop).height();
							var p = parseInt(prop);
							campaign_loc_genger_chart_container[p].setSize(width, height, doAnimation = true);
						}
					}
					/**** for age report ***/
					for (var prop in campaign_loc_age_chart_container) {
						if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
						{
							width = jQuery("#containeragewisegender_" + prop).width();
							height = jQuery("#containeragewisegender_" + prop).height();
							var p = parseInt(prop);
							campaign_loc_age_chart_container[p].setSize(width, height, doAnimation = true);
						}
					}
					/**** for share report ***/
					for (var prop in campaign_loc_share_chart_container) {
						if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
						{
							width = jQuery("#summerisedshare_" + prop).width();
							height = jQuery("#summerisedshare_" + prop).height();
							var p = parseInt(prop);
							campaign_loc_share_chart_container[p].setSize(width, height, doAnimation = true);
						}
					}

					/**** for view report ***/
					for (var prop in campaign_loc_view_chart_container) {
						if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
						{
							width = jQuery("#summerisedview_" + prop).width();
							height = jQuery("#summerisedview_" + prop).height();
							var p = parseInt(prop);
							campaign_loc_view_chart_container[p].setSize(width, height, doAnimation = true);
						}
					}
					/**** For rating report ***/
					for (var prop in campaign_loc_rating_chart_container) {
						if (prop != "indexOf" && prop != "NaN" && prop != "null" && prop != "in_array")
						{
							width = jQuery("#summerisedrating_" + prop).width();
							height = jQuery("#summerisedrating_" + prop).height();
							var p = parseInt(prop);
							campaign_loc_rating_chart_container[p].setSize(width, height, doAnimation = true);
						}
					}
					/*** for summeriesed data ***/
					console.log("resize summary");
					if (jQuery("#summerisedidgender").length != 0)
					{
						width = jQuery("#summerisedidgender").width();
						height = jQuery("#summerisedidgender").height();
						campaign_genger_chart_container.setSize(width, height, doAnimation = true);
					}
					if (jQuery("#summerisedidage").length != 0)
					{
						width = jQuery("#summerisedidage").width();
						height = jQuery("#summerisedidage").height();
						campaign_age_chart_container.setSize(width, height, doAnimation = true);
					}
					if (jQuery("#summerisedshare").length != 0)
					{
						width = jQuery("#summerisedshare").width();
						height = jQuery("#summerisedshare").height();
						campaign_share_chart_container.setSize(width, height, doAnimation = true);
					}
					if (jQuery("#summerisedview").length != 0)
					{
						width = jQuery("#summerisedview").width();
						height = jQuery("#summerisedview").height();
						campaign_view_chart_container.setSize(width, height, doAnimation = true);
					}
					if (jQuery("#summerisedrating").length != 0)
					{
						width = jQuery("#summerisedrating").width();
						height = jQuery("#summerisedrating").height();
						campaign_rating_chart_container.setSize(width, height, doAnimation = true);
					}
				}
				catch(e)
				{
					console.log(e.message);
				}

		    }
		    /*** load qrcode report on selection of qrcode sacn tab to slove responsivechart problem ***/
		});
		/*
    if (localStorage) {
	  // LocalStorage is supported!
	  console.log("LocalStorage is supported!");
	  var cid=1;
	  var vlu="hi";
	   localStorage.setItem('card_'+cid+'_object', vlu);
	   console.log("get local storage value : "+localStorage.getItem(('card_'+cid+'_object')));
	   vlu=localStorage.getItem(('card_'+cid+'_object')) +" hello";
	   localStorage.setItem('card_'+cid+'_object', vlu);
	   console.log("get local storage value : "+localStorage.getItem(('card_'+cid+'_object')));
	   
	   //localStorage.removeItem("key");
	   //console.log(localStorage.getItem("key"));
		if("key" in localStorage && localStorage.getItem("key") != null)
		{
			console.log('key exists');
				
			var object = JSON.parse(localStorage.getItem("key"));
			expireString = object.timestamp,
			nowString = new Date().getTime().toString();
			console.log("et : "+expireString);
			console.log("ct : "+nowString);
			
			if (nowString > expireString)
			{
				console.log("expire");
				localStorage.removeItem("key");
			}
		} 
		else 
		{
			console.log('key not exists');
			d = new Date();
			units = 2 // minute
			expireTime = d.getTime() + units*60000;
			var object = {value: "my value", timestamp: expireTime};
			localStorage.setItem("key", JSON.stringify(object));
		}
		
	   
	} 
	else 
	{
	  // No support. Use a fallback such as browser cookies or store on the server.
	  console.log("No support. Use a fallback such as browser cookies or store on the server.");
	}
	*/
	</script>
	</body>
	</html>
