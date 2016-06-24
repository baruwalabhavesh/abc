<?php
/**
 * @uses redeem deal
 * @used in pages :my-account-left.php
 * @author Sangeeta Raghavani
 */
session_start();
//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
////$objDB = new DB();
?>
<!DOCTYPE HTML >
<html>
    <head>
        <title>ScanFlip | Manage Points </title>

        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
        <link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.min.css" />
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <!--- tooltip css --->
		<script type="text/javascript" src="<?= ASSETS_JS ?>/m/fancybox/jquery.fancybox.js"></script>
		<link rel="stylesheet" type="text/css" href="<?= ASSETS_CSS ?>/m/fancybox/jquery.fancybox.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?= ASSETS_CSS ?>/m/merchantcss.css" media="screen" />
		<!--<link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/font-awesome.min.css">-->
        <link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">
        <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
		<script type="text/javascript" src="<?= ASSETS_JS ?>/m/jquery.form.js"></script>
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.min.js"></script>
        <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>


    </head>

    <body class="redeem_page">

        <div id="dialog-message" title="Message Box" style="display:none">
        </div>

        <div >

<!--<script type="text/javascript" src="https://code.jquery.com/jquery-1.6.2.min.js"></script>-->
            <!-- load from CDN-->
            <?php //require_once(MRCH_LAYOUT . "/head.php"); ?>
           
            
            <?php
            if (isset($_COOKIE['merchant_' . $_SESSION['merchant_id']])) {
                if ($_COOKIE['merchant_' . $_SESSION['merchant_id']] != "") {
                    ?>

                    <?php
                    $lnglat = $_COOKIE['merchant_' . $_SESSION['merchant_id']];
                    $lnglat_arr = explode(";", $lnglat);
                    $curr_longitude = $lnglat_arr[0];
                    $curr_latitude = $lnglat_arr[1];
                }
            } else {
                ?>

                <?php
            }
            ?>
            <!---start header---->
            <div>
                <?php
//INCLUDE HEADER FILE
                require_once(MRCH_LAYOUT . "/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">

                <div id="content">

                    <div class="headding_titel" style="margin-bottom: 0px;">
                        <div class="col-sm-3 title_header head_titels">Manage Points</div>
                        <div class="col-sm-12 grnd_total_card">
                            <div class="col-sm-3 text-center">
                                <div class="tab_inner_bg">
                                    <label style="margin-bottom:0px;">Point Available </label>
                                    <p class="gt_cad"  id="available_points">0</p>
                                </div>				
                            </div>
                            <div class="col-sm-3 text-center">
                                <input class ="" type="button" value ="    Recent Transactions     " data-toggle="modal" data-target="#recent_transactions"> 
                            </div>
                            <div class="col-sm-3 text-center">   
                                <input class ="" type="button" id="make_payment_card" value ="    Manage Payment Cards    " data-toggle="modal" data-target="">
                            </div>
                            <div class="col-sm-3 text-center">
                                <?php
                                if ($_SESSION['merchant_info']['merchant_parent'] == 0) {
                                    ?>
                                    <input class ="p_btn1" type="button" value ="<?php echo $merchant_msg["edit-compaign"]["Field_purchase_scanflip_points"]; ?> ">                                   
                                    <?php
                                }
                                ?>   			
                            </div>
                        </div>
                    </div>

                    <div id="manage_payment_card_form" style="display:none;"  >
                        <?php //require_once(MRCH_LAYOUT . "/header.php"); ?>
                          <div >
							<button type="button" class="close close_link close_manage_payment_card_form">&times;</button>
    <div id="loading" style="display:none;">
	<img src="<?= ASSETS ?>/images/24.GIF" style="position:relative;top:119px;left: 450px;z-index: 1;">
    </div>
                    <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                        <tr>
                            <td width="75%" align="left" valign="top">
							<button id="newCard" class="btn btn-default add_new_card1" style="float:left;">Add Card</button>
                                <!--<input type="button" name="newCard" id="newCard" class="btn btn-default" value="Add"/> -->
                                <div class="datatable_container" style="display: none;">	
                                    <div id="div_payment_transaction_table">  
                                        <table width="100%"  border="0" cellspacing="1" cellpadding="10" class="tableMerchant"  id="payment_cards_table">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Number</th>
                                                    <th>Expires</th>
                                                    <th></th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>

                    </div>
                        <div id="card_edit_modal" style="display: block;">

            </div>
            <div id="card_del" style="display: none;">
                <h4 class="head_msg">Please Confirm</h4>
                <input type="hidden" name="del_id" id="del_id">
                <p>Do you want to delete this card ?</p>
                <button type="button" class="btn btn-default btn-no-card">No</button>
                <button type="button" class="btn btn-default btn-yes-card">Yes</button>
            </div>
                        
                    </div>


                    <div id="redem_contanr">

                        <div class="box_manage_point">
                            <div class="row tabs_details">
                                <div class="col-md-12">

                                    <div class="tabbable tabs-left" id="reward_card">
                                        <div class="row">
                                            <div class="col-md-2" id="reward_info">
                                                <ul class="nav nav-tabs no-margin">
                                                    <li class="active">
                                                        <a href="#a" class="a" data-toggle="tab">Points Blocked</a></li>
                                                    <li><a href="#b" class="b" data-toggle="tab">Points Spent</a></li>
                                                    <li><a href="#c" class="c" data-toggle="tab">Points Earned</a></li>
                                                    <li><a href="#d" class="d" data-toggle="tab">Points Pending Allocation</a></li>

                                                </ul>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="tab-content ">
                                                        <div class="tab-pane active" id="a">
                                                            <div class="left_points">
                                                                <table class="table table_th_98 purchase_points table-bordered">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>Campaign</td>
                                                                            <td>4000</td>
                                                                            <td><a href="#">Show More</a></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Loyalty Card</td>
                                                                            <td id="lc_pb"></td>
                                                                            <td id="lc_pb_show" class="redeem_gc lc_pb_show_class" style="cursor: pointer;">Show More</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Total Blocked</td>
                                                                            <td>10000</td>
                                                                            <td><a href="#"></a></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="b">
                                                            <div class="left_points">
                                                                <!--<div class="search_area01 points_filter">
                                                                    <label>Year:</label>
                                                                    <select id="card_filter_year">
                                                                        <option value="2013"> 2013 </option>
                                                                        <option value="2014"> 2014 </option>
                                                                        <option value="2015" selected=""> 2015 </option>
                                                                    </select>
                                                                    <input class="years_filter" type="button" value="Filter" name="filterrwdCard" id="filterrwdCard">
                                                                </div>-->
                                                                <table class="table table_th_98 purchase_points table-bordered">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>Campaign</td>
                                                                            <td>4000</td>
                                                                            <td><a href="#">Show More</a></td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>Loyalty Card</td>
                                                                            <td id = "lc_ps" >0</td>
                                                                            <td class="redeem_gc lc_ps_show_class" style="cursor: pointer;">Show More</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Customer Orders</td>
                                                                            <td>8000</td>
                                                                            <td><a href="#">Show More</a></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Total Spent</td>
                                                                            <td>21000</td>
                                                                            <td><a href="#"></a></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="c">
                                                            <div class="left_points">
                                                                <!--<div class="search_area01 points_filter">
                                                                    <label>Year:</label>
                                                                    <select id="card_filter_year">
                                                                        <option value="2013"> 2013 </option>
                                                                        <option value="2014"> 2014 </option>
                                                                        <option value="2015" selected=""> 2015 </option>
                                                                    </select>
                                                                    <input class="years_filter" type="button" value="Filter" name="filterrwdCard" id="filterrwdCard">
                                                                </div>-->
                                                                <table class="table table_th_98 purchase_points table-bordered">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>Rewards Campaign</td>
                                                                            <td id="point_earned_camp"></td>
                                                                            <td id="point_earned_camp_show_id" class="point_earned_camp_show redeem_gc" style="cursor: pointer;"><span style="cursor: pointer;">Show More</span></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Gift Card</td>
                                                                            <td id="point_earned_giftcard"></td>
                                                                            <td id="point_earned_gift_show_id" class="point_earned_gift_show redeem_gc"><span style="cursor: pointer;">Show More</span></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Total Earned</td>
                                                                            <td id="point_earned_total"></td>
                                                                            <td><a href="#"></a></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>



                                                            </div>
                                                        </div>

                                                        <div class="tab-pane" id="d">
                                                            <div class="left_points">
                                                                <table class="table purchase_points table-bordered points_pending_allocation">
                                                                    <tbody>
                                                                        <tr>
    <td>New Customer Refferal</td>
    <td>2000</td>

    </tr>
        <tr>
            <td>Customer Orders</td>
            <td>5000</td>
            
                </tr>
            <tr>
                    <td>Total Pending</td>
                <td>7000</td>

                </tr>
    </tbody>
    </table>     </div>
        </div>

    </div>

        </div>                                             </div>



            </div> 

            </div>
                </div>
            <!-- /tabs -->


        </div>

    

    </div>
        <div class="title_camp_show" style="display:none;">
                            <div><hr></div>
                            <h4 style="margin-top:0">Rewards Campaign </h4>
                            <div id="show_more_camp">
                                 <table width="100%"  border="0" class="tableMerchant"  id="earned_camp_show">
                                <thead>
                                <tr>
                                <th>Campaign</th>
                                <th>Total Points Earned</th>
                                </tr>
                                </thead>
                          </table>
                            </div>
                        </div>
        <div class="title_gift_show" style="display:none;">
                            <div><hr></div>
                            <h4 style="margin-top:0">Gift Card </h4>
                            <div id="show_more_gift" > 
                                <table width="100%"  border="0" class="tableMerchant"  id="earned_gift_show">
                                <thead>
                                <tr>
                                <th>Gift Card</th>
                                <th>Total Points Earned</th>
                                </tr>
                                </thead>
                          </table>
                            </div>
                        </div>
 
  <div class="title_pb_lc_show" style="display:none;">
                            <div><hr></div>
                            <h4 style="margin-top:0">Loyalty Card </h4>
                            <div id="show_more_loyalty_card_block" > 
                                <table width="100%"  border="0" class="tableMerchant"  id="point_block_lc_show">
                                <thead>
                                <tr>
                                <th>Loyalty Card</th>
                                <th>Total Points Blocked</th>
                                </tr>
                                </thead>
                          </table>
                            </div>
                        </div>
 
 <div class="title_ps_lc_show" style="display:none;">
                            <div><hr></div>
                            <h4 style="margin-top:0">Loyalty Card </h4>
                            <div id="show_more_loyalty_card_spend" > 
                                <table width="100%"  border="0" class="tableMerchant"  id="point_spend_lc_show">
                                <thead>
                                <tr>
                                <th>Loyalty Card</th>
                                <th>Total Points spend</th>
                                </tr>
                                </thead>
                          </table>
                            </div>
                        </div>
 
 
            <!--<div id="data1"> 		
            <table width="100%"  border="0" class="tableMerchant"  id="rewardzone_campaign_table">
<thead>
<tr>
<th>Title</th>
<th>Status</th>
</tr>
</thead>
            </table>
</div>-->


                    </div>
                </div>



            


            </div>
            
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


    <div id="recent_transactions" class="modal fade" role="dialog">
    <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
            <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Recent Transactions</h4>
            </div>
    <div class="modal-body">
    <div class="datatable_container" style="display: none;">	
    <div class="view_filter">
    <span>View : </span>
            <select name="filter_transactions" id="filter_transactions">
    <option value="0"> -- All -- </option>
    <option value="7"> Last 7 days </option>
                            <option value="90"> Last 90 days </option>
            <option value="365"> 1 Year </option>                        </select>
    </div>
    <div id="div_payment_transaction_table">  
            <table width="100%"  border="0" cellspacing="1" cellpadding="10" class="tableMerchant"  id="payment_transaction_table">
                            <thead>
    <tr>
    <th >Order Date</th>
    <th >Order Number</th>
            <th >Transaction Id</th>                                     <th >Amount</th>
                                    <th >Invoice</th>
                                    <!--<th >Reference Number</th>-->
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>


            </div>

        </div>

    </div>
</div>




</body>
</html>
<script>
    jQuery("#filter_transactions").change(function () {
        jQuery('#payment_transaction_table').dataTable().fnDraw();
    });
    var len = 5;
    function check_point(){
        $.ajax({
            type: "POST",
            url: "<?= WEB_PATH ?>/merchant/process.php",
            data: "filter_point_earned=true",
            success: function (msg) {
                var res = jQuery.parseJSON(msg);
                if (res.status == "true") {
                    jQuery('#point_earned_camp').text(res.points_campaign);
                    jQuery('#point_earned_total').text(res.total_earned);
                    jQuery('#point_earned_giftcard').text(res.points_giftcard);
                    jQuery('#lc_pb').text(res.points_blocked_lc);
                    if(res.points_blocked_lc == 0){
						 $(".lc_pb_show_class").off("click");
					}
                    if(res.points_spend_lc == "null"){
                    jQuery('#lc_ps').text("0");
                     $(".lc_ps_show_class").off("click");
                    }
                    else{
						jQuery('#lc_ps').text(res.points_spend_lc);
					}
                    if (res.points_giftcard == 0) {
                         $(".point_earned_gift_show").off("click");
                    }
                    if (res.points_campaign == 0) {
                       $(".point_earned_camp_show").off("click");
                    }
                    jQuery('#available_points').text(res.points_available_total);
                } else {
                    window.location.href = "<?php echo WEB_PATH ?>/merchant/register.php";
                }
            }
        });
}
		check_point();
jQuery(document).on("click", ".p_btn1", function () {
                    jQuery.fancybox({
                        content: jQuery('#point_block').html(),
                        type: 'html',
                        openSpeed: 300,
                        closeSpeed: 300,
                        openEffect :'none',
					    closeEffect :'none',
						minHeight:300,
						minWidth:400,
                        changeFade: 'fast',
                        afterShow: function () {
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
                        },
                        helpers: {
                            overlay:
                                    {
                                        closeClick: false,
                                        opacity: 0.3
                                    } // overlay
                        }
                    });
			jQuery(document).on('click','#btnPurchasePoints',function(){
				
				jQuery(".fancybox-inner .enter_n_card").hide();
				jQuery('.ad_cd_hd').show();	
			})
			jQuery(document).on('click','#purchase_now1',function(){
				check_point();
			})
			jQuery(document).on('change','.fancybox-inner #def_payment_card',function(){
				
				jQuery('.fancybox-inner #def_country').val(this.val);
			})
			
          });
    function logged_in_or_not() {
        jQuery.ajax({
            type: "POST",
            url: 'process.php',
            data: 'loginornot=true',
            success: function (msg)
            {
                var obj = jQuery.parseJSON(msg);
                //alert(msg);
                if (obj.status == "false")
                {
                    window.location.href = "<?php echo WEB_PATH ?>/merchant/register.php";

                } else {
                    return true;
                }

            }
        });
    }
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

            jQuery.getJSON(sSource, aoData, function (json) {
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

    $('#payment_transaction_table').dataTable({
        "bFilter": false,
        "bLengthChange": false,
        "info": false,
        "bSort": false,
        //"order": [[0, "asc"]],
        //"aoColumnDefs": [{"bSortable": false, "aTargets": [1, 2, 3]}],
        "iDisplayLength": len,
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
        "fnServerParams": function (aoData) {
            aoData.push({"name": "btngetpaymenthistory1", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'}, {"name": "days", "value": $('#filter_transactions').val()});

        },
        //"fnServerData": fnDataTablesPipeline,
        "oLanguage": {
            "sEmptyTable": "No payment transactions founds in the system.",
            "sZeroRecords": "No payment transactions to display",
            "sProcessing": "Loading..."
        },
    });
    jQuery(".table_loader").css("display", "none");
    jQuery(".datatable_container").css("display", "block");


    


    $(document).ready(function () {
		
		 var pecstable = $('#earned_camp_show').dataTable({
        "bFilter": false,
        "bLengthChange": false,
        "bPaginate": true,
        "info": false,
        "bSort": false,
        "iDisplayLength": 5,
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
        "oLanguage": {
            "sEmptyTable": "No campaign founds in the system.",
            "sZeroRecords": "No campaign to display",
            "sProcessing": "Loading..."
        },
        "fnPreDrawCallback": function (oSettings) {
            logged_in_or_not();
        },
        "fnServerParams": function (aoData) {
            aoData.push({"name": "point_earned_camp_show", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'});

        },
    });
    var pegstable = $('#earned_gift_show').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bPaginate": true,
                            "info": false,
                            "bSort": false,
                            "paging": true,
                            "searching": false,
                            "iDisplayLength": 5,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
        "oLanguage": {
            "sEmptyTable": "No Gift founds in the system.",
            "sZeroRecords": "No Gift to display",
            "sProcessing": "Loading..."
        },
        "fnPreDrawCallback": function (oSettings) {
            logged_in_or_not();
        },
        "fnServerParams": function (aoData) {
            aoData.push({"name": "point_earned_gift_show", "value": true},{"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'});

        },
    });
    var pblctable = $('#point_block_lc_show').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bPaginate": true,
                            "info": false,
                            "bSort": false,
                            "paging": true,
                            "searching": false,
                            "iDisplayLength": 5,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
        "oLanguage": {
            "sEmptyTable": "No Loyalty Card founds in the system.",
            "sZeroRecords": "No Loyalty Card to display",
            "sProcessing": "Loading..."
        },
        "fnPreDrawCallback": function (oSettings) {
            logged_in_or_not();
        },
        "fnServerParams": function (aoData) {
            aoData.push({"name": "point_block_loyalty_card_show", "value": true},{"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'});

        },
    });
    
     var pslctable = $('#point_spend_lc_show').dataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bPaginate": true,
                            "info": false,
                            "bSort": false,
                            "paging": true,
                            "searching": false,
                            "iDisplayLength": 5,
                            "bProcessing": true,
                            "bServerSide": true,
                            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
        "oLanguage": {
            "sEmptyTable": "No Loyalty Card founds in the system.",
            "sZeroRecords": "No Loyalty Card to display",
            "sProcessing": "Loading..."
        },
        "fnPreDrawCallback": function (oSettings) {
            logged_in_or_not();
        },
        "fnServerParams": function (aoData) {
            aoData.push({"name": "point_spend_loyalty_card_show", "value": true},{"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'});

        },
    });
	
	
        $('body').on('click', '#make_payment_card', function () {
			
            $('#manage_payment_card_form').slideDown(500);
            $('#manage_payment_card_form').focus();
        })
        $('body').on('click', '.close_manage_payment_card_form', function () {
            $(this).closest('#manage_payment_card_form').slideUp(500);
            $(this).closest('#manage_payment_card_form').siblings('.error_messages').find('.alert_pricelist_name').hide(500);
            $(this).closest('#manage_payment_card_form').siblings('.error_messages').find('.alert_html').hide(500);
        })
        jQuery(".title_gift_show").hide();
        jQuery(".title_gift_show").hide();
        jQuery(".title_pb_lc_show").hide();
        jQuery(".title_ps_lc_show").hide();
        $(".a").click(function () {
            jQuery(".title_gift_show").hide();
            jQuery(".title_camp_show").hide();
            //jQuery(".title_pb_lc_show").hide();
            jQuery(".title_ps_lc_show").hide();
        })
        $(".b").click(function () {
            jQuery(".title_gift_show").hide();
            jQuery(".title_camp_show").hide();
            jQuery(".title_pb_lc_show").hide();
           // jQuery(".title_ps_lc_show").hide();
        })
        $(".c").click(function () {
            //jQuery(".title_gift_show").hide();
            //jQuery(".title_camp_show").hide();
            jQuery(".title_pb_lc_show").hide();
            jQuery(".title_ps_lc_show").hide();
        })
        $(".d").click(function () {
            jQuery(".title_gift_show").hide();
            jQuery(".title_camp_show").hide();
            jQuery(".title_pb_lc_show").hide();
            jQuery(".title_ps_lc_show").hide();
        })

//check_point();
        $(document).on('click', '.redeem_gc', function () {
            jQuery('.redeem_gc').css("color", "black");
            jQuery('.redeem_gc').parent('td').removeClass('tabactive');
            jQuery(this).css("color", "blue");
            jQuery(this).parent('td').addClass('tabactive');
            var code = jQuery(this).attr("data-value");
            jQuery("#giftcard_code").val('');
            jQuery("#giftcard_code").val(code);
            jQuery("#mer_redeem_giftcard").show();
        });
       $(".point_earned_camp_show").click(function () {
            //alert($('#point_earned_camp_show_id').attr('class'));
          /*  if ($('#point_earned_camp_show_id').attr('class') == "point_earned_camp_show redeem_gc")
            {*/
                $(".title_gift_show").hide();
                jQuery(".title_pb_lc_show").hide();
                $(".title_camp_show").css("display", "block");
                //	pecstable.fnDraw();
            //}
        });
        $(".point_earned_gift_show").click(function () {
           /* if ($('#point_earned_gift_show_id').attr('class') == "point_earned_gift_show redeem_gc")
            {*/
                $(".title_camp_show").hide();
                jQuery(".title_pb_lc_show").hide();
                $(".title_gift_show").css("display", "block");
                //pegstable.fnDraw();
            //}
        })
         $(".lc_pb_show_class").click(function () {
            /*if ($('#point_earned_gift_show_id').attr('class') == "point_earned_gift_show redeem_gc")
            {*/
                $(".title_camp_show").hide();
                $(".title_gift_show").hide();
                $(".title_pb_lc_show").css("display", "block");
                //pegstable.fnDraw();
           // }
        })
        $(".lc_ps_show_class").click(function () {
            /*if ($('#point_earned_gift_show_id').attr('class') == "point_earned_gift_show redeem_gc")
            {*/
                $(".title_camp_show").hide();
                $(".title_gift_show").hide();
                $(".title_pb_lc_show").hide();
                $(".title_ps_lc_show").css("display", "block");
                //pegstable.fnDraw();
           // }
        })
        var otable = $('#payment_cards_table').dataTable({
            "bFilter": false,
            "bLengthChange": false,
            "info": false,
            "bSort": false,
            "iDisplayLength": 5,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
            "fnPreDrawCallback": function (oSettings) {
                logged_in_or_not();
            },
            "fnServerParams": function (aoData) {
                aoData.push({"name": "get_payment_cards", "value": true}, {"name": 'mer_id', "value": '<?php echo $_SESSION['merchant_id']; ?>'});

            },
            "fnServerData": fnDataTablesPipeline,
            "oLanguage": {
                "sEmptyTable": "No payment cards founds in the system.",
                "sZeroRecords": "No payment cards to display",
                "sProcessing": "<img src='<?= ASSETS ?>/images/24.GIF'>"
            },
        });
        jQuery(".table_loader").css("display", "none");
        jQuery(".datatable_container").css("display", "block");
        jQuery(document).on('click', '.card_action', function () {
            logged_in_or_not();
            var cardid = jQuery(this).attr('cardid');
            var data_ac = jQuery(this).attr('data-ac');

            if (data_ac == 'edit') {
                jQuery.fancybox({
                    //content: jQuery('#card_edit_modal').html(),
                    type: 'html',
                    openEffect :'none',
					closeEffect :'none',
                    minWidth: 400,
                    minHeight: 400,
                    openSpeed: 300,
                    closeSpeed: 300,
                    // topRatio: 0,
                    changeFade: 'fast',
                    href: '/merchant/edit-payment-card.php?id=' + cardid,
                    type: 'ajax',
                            helpers: {
                                overlay: {
                                    closeClick: true,
                                    opacity: 0.3
                                } // overlay
                            }
                });
            } else {
                $('#del_id').val(cardid);
                jQuery.fancybox({
                    content: $("#card_del").html(),
                    type: 'html',
                    minWidth: 200,
                    openSpeed: 300,
                    closeSpeed: 300,
                    openEffect :'none',
					closeEffect :'none',
                    // topRatio: 0,
                    changeFade: 'fast',
                    helpers: {
                        overlay: {
                            closeClick: false,
                            opacity: 0.3
                        } // overlay
                    }
                });

            }
        });
        $(document).on('click', '.btn-update', function () {
            logged_in_or_not();
            var fm = $('.edit_pay_card').serialize();
            //console.log(fm);
            jQuery.ajax({
                type: "POST",
                url: 'process.php',
                data: 'stripe_edit_card=true&' + fm,
                success: function (msg)
                {
                    var obj = jQuery.parseJSON(msg);
                    if (obj.status == "true")
                    {
                        $.fancybox.close();
                        oCache.iCacheLower = -1;
                        otable.fnDraw();
                    } else {
                        $('.edit_pay_card').prepend(obj.serror);
                    }


                }
            });
        });

        $(document).on('click', '.btn-yes-card', function () {
            logged_in_or_not();
            jQuery.ajax({
                type: "POST",
                url: 'process.php',
                data: 'stripe_delete_card=true&cid=' + $('#del_id').val(),
                success: function (msg)
                {
                    $.fancybox.close();
                    oCache.iCacheLower = -1;
                    otable.fnDraw();
                }
            });
        });

        $(document).on('click', '.sp_mk_df_card', function () {
			jQuery("#loading").css("display","block");
			jQuery("#manage_payment_card_form").children().bind('click', function(){ return false; });
			jQuery("#payment_cards_table").css("opacity","0.5");
            logged_in_or_not();
            var cid = $(this).data('id');
            jQuery.ajax({
                type: "POST",
                url: 'process.php',
                data: 'stripe_default_card=true&cid=' + cid,
                success: function (msg)
                {
					
					 var res = jQuery.parseJSON(msg);
					 cid = res.card_id;
                     jQuery("#def_payment_card option").removeAttr('selected');
                     jQuery("#def_payment_card>option[value='"+cid+"']").attr('selected', true);
                     oCache.iCacheLower = -1;
                     otable.fnDraw();
                     jQuery("#payment_cards_table").css("opacity","1");
					 jQuery("#loading").css("display","none");
					 jQuery("#manage_payment_card_form").children().unbind('click');
                }
            });
        });

        $(document).on('click', '.btn-no-card', function () {
            $.fancybox.close();
        });
        $(document).on('change', '#billing_country', function () {
            var cn = this.value;
            jQuery.ajax({
                type: "POST",
                url: 'get_state.php',
                data: 'country=' + cn,
                success: function (msg)
                {
                    $('#billing_state').html(msg);
                }
            });
        });
		
        $(document).on('click', '#newCard', function () {
            logged_in_or_not();
            jQuery.fancybox({
                content: jQuery('#card_edit_modal').html(),
                type: 'html',
                minWidth: 400,
                minHeight: 400,
                openEffect :'none',
				closeEffect :'none',
                autoScale: true,
                openSpeed: 300,
                closeSpeed: 300,
                // topRatio: 0,
                changeFade: 'fast',
                href: '/merchant/add-payment-card.php',
                type: 'ajax',
                        helpers: {
                            overlay: {
                                closeClick: true,
                                opacity: 0.3
                                //otable.fnDraw();
                            } // overlay
                        }
            });
			otable.fnDraw();
        });
        jQuery('#campaign_detail_form').ajaxForm({
            dataType: 'json',
            beforeSubmit: loginornot, // CALL REDEEEM FUNCTION
            success: processRedeemJson  /// IF ERROR RETURN BY REDEEM FUNCTION THEN HADLE BY THIS METHOD
        });

        //ON KEYUP VALIDATION AND PROCESS OF GIFTCARD  
        jQuery("#giftcard_value,#gc_value").on('keyup', function (e) {
            if (this.id == 'giftcard_value') {
                var gc_code = jQuery("#giftcard_code").val();
                var gc_value = jQuery("#giftcard_value").val();
                var trans = jQuery(".trans1 input[type='radio']:checked").val();
                var code_msg_div = '#message_in';
                var pts_msg_div = '#message_points_in';
            } else if (this.id == 'gc_value') {
                var gc_code = jQuery("#gc_code").val();
                var gc_value = jQuery("#gc_value").val();
                var trans = jQuery(".trans input[type='radio']:checked").val();
                var code_msg_div = '#message';
                var pts_msg_div = '#message_points';
            }
            if (gc_code.charAt(0) != "G" && gc_code.charAt(1) != "C" && gc_code.length != 10) {
                jQuery(code_msg_div).html('');
                jQuery(code_msg_div).html('Enter a valid Gift card code').css("color", "red");
                return false;
            }
            if (e.which != 8 && e.which != 0 && (e.which < 46 || e.which > 57) && (e.which < 96 || e.which > 105)) {
                jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_positive_numbers"]; ?>').css("color", "red");
                //console.log('1');        
                return false;
            } else {
                console.log('else');
                jQuery(pts_msg_div).html('');
                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'check_info_ajax=true&gc_code=' + gc_code + '&gc_value=' + gc_value + '&trans=' + trans,
                    success: function (msg)
                    {
                        if (msg != '') {
                            var obj = jQuery.parseJSON(msg);
                            jQuery(pts_msg_div).html(obj.msg).css("color", "red");
                        }
                    }
                });
                return true;
                jQuery(pts_msg_div).html('');
                jQuery(code_msg_div).html('');
            }


            return false;
            jQuery(pts_msg_div).html('');
            jQuery(code_msg_div).html('');



        });

        jQuery(".radio").change(function () {
            jQuery("#gc_value").val('');
            jQuery("#giftcard_value").val('');
            jQuery("#message_points").html('');
            jQuery("#message_points_in").html('');
            jQuery("#message").html('');
            jQuery("#messagein").html('');
        });

        //CLEAR ALL INPUT VALUES ON TAB SELECTION
        jQuery(document).on('click', '#main_redem_selection ul li,#reward_info', function () {
            $("input[type='text']").val("");
            $("input[type='number']").val("");
            jQuery('.rec_trans,.redeem_gc,.redeem_cp').css("color", "black");

        });


        //ON CLICK VALIDATION AND PROCESS OF GIFTCARD  
        jQuery(document).on('click', '#redeem_user_giftcard,#red_gif_card_submit', function () {
            processLoginorNot();
            if (this.id == 'redeem_user_giftcard') {
                var gc_code = jQuery("#gc_code").val();
                var gc_value = jQuery("#gc_value").val();
                var trans = jQuery(".trans input[type='radio']:checked").val();
                var code_msg_div = '#message';
                var pts_msg_div = '#message_points';
            } else if (this.id == 'red_gif_card_submit') {
                var gc_code = jQuery("#giftcard_code").val();
                var gc_value = jQuery("#giftcard_value").val();
                var trans = jQuery(".trans1 input[type='radio']:checked").val();
                var code_msg_div = '#message_in';
                var pts_msg_div = '#message_points_in';
            }

            if (gc_code.length == 0) {
                jQuery(code_msg_div).html('');
                jQuery(pts_msg_div).html('');
                jQuery(code_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard"]; ?>').css("color", "red");
                return false;
            }

            if (gc_value.length == 0) {
                jQuery(code_msg_div).html('');
                jQuery(pts_msg_div).html('');
                jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_points"]; ?>').css("color", "red");
                return false;
            }
            if (jQuery.isNumeric(gc_value) == false) {
                jQuery(code_msg_div).html('');
                jQuery(pts_msg_div).html('');
                jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"]; ?>').css("color", "red");
                return false;
            }
            if (gc_value <= 0) {
                jQuery(code_msg_div).html('');
                jQuery(pts_msg_div).html('');
                jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"]; ?>').css("color", "red");
                return false;
            }
            if (gc_value.indexOf("-") != -1) {
                jQuery(code_msg_div).html('');
                jQuery(pts_msg_div).html('');
                jQuery(pts_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard_numeric"]; ?>').css("color", "red");
                return false;
            }
            jQuery(code_msg_div).html('');
            jQuery(pts_msg_div).html('');
            jQuery.ajax({
                type: "POST",
                url: 'process.php',
                data: 'redeem_user_giftcard=true&gc_code=' + gc_code + '&gc_value=' + gc_value + '&trans=' + trans,
                success: function (msg)
                {
                    var obj = jQuery.parseJSON(msg);
                    //alert(obj.msg);                         
                    var head_msg = "<div class='head_msg'>Message</div>"
                    var content_msg = "<div class='content_msg'>" + obj.msg + "</div>";
                    var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                    jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                    jQuery('#user_giftcards').dataTable().fnDraw();
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
            });
            return false;
        });

        //jQuery("#redeem_rzcampaign,.redeem_cp").click(function(){
        jQuery(document).on('click', '#redeem_rzcampaign,.redeem_cp', function () {
            var cp_code = jQuery("#cp_code").val();
            var sel_class = jQuery(this).attr("class");
            if (sel_class == 'redeem_cp') {
                jQuery(this).css("color", "blue");
                var cp_code = jQuery(this).attr("data-value");
            }
            if (cp_code.length === 0) {
                jQuery('#reward_campaign_message').html('');
                jQuery('#reward_campaign_message').html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_reward_campaign_code"]; ?>').css("color", "red");
                return false;
            }
            jQuery('#reward_campaign_message').html('');
            jQuery.ajax({
                type: "POST",
                url: 'process.php',
                data: 'redeem_reward_campaign=true&cp_code=' + cp_code,
                success: function (msg)
                {
                    var obj = jQuery.parseJSON(msg);
                    //alert(obj.msg);
                    var obj = jQuery.parseJSON(msg);
                    //alert(obj.msg);                         
                    var head_msg = "<div class='head_msg'>Message</div>"
                    var content_msg = "<div class='content_msg'>" + obj.msg + "</div>";
                    var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                    jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);
                    jQuery('#user_reward_campaigns').dataTable().fnDraw();
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
            });
            return false;
        });

        jQuery(document).on('click', '.redeem_reward_card_popup,.reward_btn', function () {
            processLoginorNot();
            jQuery(".reward_btn").css("display", "block");
            jQuery("#reward_card").css("display", "none");
            jQuery("#txt_rewardcard").val('');
            jQuery("#msg_reward_card").html('');
            jQuery("#myModal").modal();
        });

        jQuery(".closetab_src").click(function () {
            jQuery('#myModal').modal('toggle');
        });

        // RECENT TRANSACTIONS VIEW
        jQuery(document).on('click', '.recentTransaction,.rec_trans', function () {
            processLoginorNot();
            var sel_class = jQuery(this).attr("class");
            if (sel_class == 'recentTransaction') {
                var gc_code = $.trim(jQuery("#gc_code").val());
                var code_msg_div = '#message';
                var pts_msg_div = '#message_points';
            } else if (sel_class == 'rec_trans') {
                jQuery('.rec_trans').css("color", "black");
                jQuery(this).css("color", "blue");
                var gc_code = jQuery(this).attr("data-value");
                var code_msg_div = '#message_in';
                var pts_msg_div = '#message_points_in';
            }
            if (gc_code.length === 0) {
                jQuery(pts_msg_div).html('');
                jQuery(code_msg_div).html('');
                jQuery(code_msg_div).html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard"]; ?>').css("color", "red");
                return false;
            }
            jQuery(pts_msg_div).html('');
            jQuery(code_msg_div).html('');
            var otable = '';
            //if(gc_code)
            $('#recent_giftcard_transactions').dataTable().fnDestroy();

            otable = $('#recent_giftcard_transactions').dataTable({
                "bFilter": false,
                "bLengthChange": false,
                "bPaginate": true,
                "info": false,
                "bSort": false,
                "iDisplayLength": 5,
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                "oLanguage": {
                    "sEmptyTable": "No transactions founds in the system.",
                    "sZeroRecords": "No transactions to display",
                    "sProcessing": "Loading..."
                },
                "fnPreDrawCallback": function (oSettings) {
                    //logged_in_or_not();
                },
                "fnServerParams": function (aoData) {
                    aoData.push({"name": "recent_giftcard_transactions", "value": true}, {"name": 'gc_code', "value": gc_code});

                },
            });


            jQuery('#rewardGiftModal').modal('show');

        });

        jQuery(".closetab_rgm").click(function () {
            jQuery('#rewardGiftModal').modal('toggle');
        });

        jQuery('.recentBal').on('click', function () {
            processLoginorNot();
            var gc_code = jQuery("#gc_code").val();
            console.log(gc_code.length);
            if (gc_code.length === 0) {
                jQuery('#message_points').html('');
                jQuery('#message').html('');
                jQuery('#message').html('<?php echo $merchant_msg["redeem-deal"]["Msg_enter_giftcard"]; ?>').css("color", "red");
                return false;
            }
            jQuery('#message_points').html('');
            jQuery('#message').html('');
            jQuery.ajax({
                type: "POST",
                url: 'process.php',
                data: 'giftcard_balance=true&gc_code=' + gc_code,
                success: function (msg)
                {
                    var obj = jQuery.parseJSON(msg);
                    if (obj.status == 1) {
                        jQuery('#giftcard_balance tbody').html('<tr><td>' + obj.title + '</td><td>' + obj.value + '</td><td>' + obj.balance + '</td></tr>');
                    } else {
                        jQuery('#giftcard_balance tbody').html('<tr><td colspan="3"><h4>' + obj.msg + '</h4></td></tr>');
                    }
                    jQuery('#checkbal').modal('show');
                }
            });


        });

        jQuery(".closetab_gcb").click(function () {
            jQuery('#checkbal').modal('toggle');
        });

        //GIFTCARD REDEEM FROM MERCHANT SIDE
        $(document).on('click', '.redeem_gc', function () {
            jQuery('.redeem_gc').css("color", "black");
            jQuery('.redeem_gc').parent('td').removeClass('tabactive');
            jQuery(this).css("color", "blue");
            jQuery(this).parent('td').addClass('tabactive');
            var code = jQuery(this).attr("data-value");
            jQuery("#giftcard_code").val('');
            jQuery("#giftcard_code").val(code);
            jQuery("#mer_redeem_giftcard").show();
        });

    })
    jQuery("#btn_redeem_card").click(function () {
        var merchant_id = "<?php echo $_SESSION['merchant_id'] ?>";
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
            var stampcard_number = jQuery("#txt_stampcard").val();
            var revenue = jQuery("#txt_revenue").val();
            if (stampcard_number == "")
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'>Please enter card</div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

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
            else if (revenue == "")
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'>Please enter revenue</div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

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
            else
            {
                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'punchcard_for_user=yes&merchant_id=' + merchant_id + "&code=" + stampcard_number + "&revenue=" + revenue,
                    async: false,
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                        if (obj.status == "true")
                        {
                            var head_msg = "<div class='head_msg'>Message</div>"
                            var content_msg = "<div class='content_msg'>" + obj.message + "</div>";
                            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                            //alert(content_msg);

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

                            jQuery("#txt_stampcard").val('');
                            jQuery("#txt_revenue").val('');
                            return false;
                        }
                        else
                        {
                            var head_msg = "<div class='head_msg'>Message</div>";
                            var content_msg = "<div class='content_msg'>" + obj.message + "</div>";
                            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                            //alert(content_msg);

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
    jQuery("#btn_get_campaignlist").click(function () {
        processLoginorNot();
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
            var reward_card_number = jQuery("#txt_rewardcard").val();
            if (reward_card_number == "")
            {

                jQuery("#msg_reward_card").html("<?php echo $merchant_msg["redeem-deal"]["Msg_enter_reward_card"]; ?>").css("color", "red");

                //alert(content_msg);

                return false;
            }
            else
            {
                //GIFTCARD TABLE       
                var otable = '';
                $('#user_giftcards').dataTable().fnDestroy();

                otable = $('#user_giftcards').dataTable({
                    "bFilter": false,
                    "iDisplayStart": 0,
                    "bLengthChange": false,
                    "bPaginate": false,
                    "info": false,
                    "bSort": false,
                    "iDisplayLength": 5,
                    "bProcessing": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                    "oLanguage": {
                        "sEmptyTable": "No transactions founds in the system.",
                        "sZeroRecords": "Sorry currently customer does not have any gift cards.",
                        "sProcessing": "Loading..."
                    },
                    "fnPreDrawCallback": function (oSettings) {
                        //logged_in_or_not();
                    },
                    "fnServerParams": function (aoData) {
                        aoData.push({"name": "view_user_giftcards", "value": true}, {"name": 'reward_card_number', "value": reward_card_number});

                    },
                });

                //REWARD CAMPAIGN TABLE
                var rctable = '';
                $('#user_reward_campaigns').dataTable().fnDestroy();

                rctable = $('#user_reward_campaigns').dataTable({
                    "bFilter": false,
                    "bLengthChange": false,
                    "bPaginate": false,
                    "info": false,
                    "bSort": false,
                    "iDisplayLength": 5,
                    "bProcessing": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo WEB_PATH; ?>/merchant/process.php",
                    "oLanguage": {
                        "sEmptyTable": "No transactions founds in the system.",
                        "sZeroRecords": "Sorry currently customer does not have any active reward campaigns.",
                        "sProcessing": "Loading..."
                    },
                    "fnPreDrawCallback": function (oSettings) {
                        //logged_in_or_not();
                    },
                    "fnServerParams": function (aoData) {
                        aoData.push({"name": "view_user_reward_campaigns", "value": true}, {"name": 'reward_card_number', "value": reward_card_number});

                    },
                });

                //CAMPAIGN TABLE
                jQuery.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'getcamplist_from_rewardcard=true&reward_card_number=' + reward_card_number,
                    async: false,
                    success: function (msg)
                    {
                        var obj = jQuery.parseJSON(msg);
                        if (obj.code_status == 1)
                        {
                            jQuery(".reward_btn").css("display", "none");
                            jQuery("#reward_card").css("display", "block");
                        }
                        if (obj.status == "true")
                        {
                            jQuery("#enter_reward_number").css("display", "none");
                            jQuery("#campaign_list_div").html(obj.html);
                            jQuery("#campaign_list_div").css("display", "block");
                            bind_campaign_select();
                        }
                        else
                        {
                            jQuery("#campaign_list_div").css("display", "block");
                            jQuery("#msg_campaign").html('<?php echo $merchant_msg["redeem-deal"]["Msg_no_saved_campaigns"]; ?>');

                            //alert(content_msg);

                            return false;
                        }
                    }
                });
            }
        }
        jQuery('#myModal').modal('toggle');
    });

    function bind_campaign_select()
    {
        jQuery("input[id^=campaign_select_]").click(function () {
            //alert(jQuery(this).attr('coupon_code'));
            var v_code = jQuery(this).attr('coupon_code');
            jQuery("#campaign_list_div").css("display", "none");
            jQuery("#txt_barcode").val(v_code);
            jQuery("#redem_div").css("display", "block");
        });

        jQuery("#goback_rewardcard").click(function () {
            jQuery("#campaign_list_div").css("display", "none");
            jQuery("#enter_reward_number").css("display", "block");
        });

        jQuery("input[class=fltr_button]").click(function () {
            var fltr_option = jQuery(this).attr("id");
            /*
             alert(fltr_option);
             alert(jQuery("div.campaign_inner_div").size());	
             alert(jQuery("div.campaign_inner_div[camp='1']").size());	
             alert(jQuery("div.campaign_inner_div[camp='2']").size());	
             */

            jQuery("div.filters input").removeClass("fltr_selected");
            if (fltr_option == "fltr_all")
            {
                jQuery(this).addClass("fltr_selected");
                jQuery("div.campaign_inner_div").css("display", "block");
            }
            else if (fltr_option == "fltr_campaign")
            {
                jQuery(this).addClass("fltr_selected");
                jQuery("div.campaign_inner_div").css("display", "none");
                jQuery("div.campaign_inner_div[camp='1']").css("display", "block");
            }
            else if (fltr_option == "fltr_stamp")
            {
                jQuery(this).addClass("fltr_selected");
                jQuery("div.campaign_inner_div").css("display", "none");
                jQuery("div.campaign_inner_div[camp='2']").css("display", "block");
            }
        });
    }
    jQuery("input[type=radio][name=redem_option]").click(function () {
        var redem_option = jQuery(this).val();
        //alert(redem_option);
        if (redem_option == "voucher")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#redem_div").css("display", "block");

        }
        else if (redem_option == "reward")
        {
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#reward_div").css("display", "block");
            jQuery("#campaign_list_div").css("display", "none");
            jQuery("#enter_reward_number").css("display", "block");

        }
        else if (redem_option == "stamp")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "block");
        }

    });


    


    /***** IF VOUCHER CODE IS ENTERD AND VOUCHER CODE LENGTH MUST BE GREATER THEN 10 AND REDEEM DEAL VALUE MUST NE ENTERD THEN ENABLE THE REDEEM BUTTON ACTIVE********/
    jQuery("input[type='text']").keyup(function () {

        if (jQuery("#txt_barcode").val().length == 8 && jQuery("#txt_redeem_deal_value").val().length > 0)
        {
            jQuery('#btn_redeem').removeAttr("disabled");
            jQuery('#btn_redeem').removeClass("disabled");
        }
        else
        {
            jQuery('#btn_redeem').attr("disabled", "");
            jQuery('#btn_redeem').addClass("disabled");
        }

        if (jQuery("#txt_stampcard").val().length == 8 && jQuery("#txt_revenue").val().length > 0)
        {
            jQuery('#btn_redeem_card').removeAttr("disabled");
            jQuery('#btn_redeem_card').removeClass("disabled");
        }
        else
        {
            jQuery('#btn_redeem_card').attr("disabled", "");
            jQuery('#btn_redeem_card').addClass("disabled");
        }
    });

    jQuery(".fancybox-inner #popupredeem").live("click", function () {
        window.location = "redeem-deal.php";
    });

    /***** IF CURRENT MERCAHNT SESSION TIME OUT THEN SEND TO REGISTER PAGE AND AFTER REGISTRATION AGAIN COME TO REDEEM DEAL PAGE , 
     OTHERWISE CONTINUE WITH REDEEM FUNCTIONALITY
     BEFORE SUBMITING FROM CHECK WHETHER VOUCHER CODE IS ENETER AND IS PROPER REDEEM VALUE IS ENTERED OR NOT AND GIVE PROPER ERROR MESSAGE
     *****/
    function loginornot()
    {
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
            var barcode = jQuery("#txt_barcode").val();
            if (barcode == "")
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_enter_proper_coupon_code"]; ?></div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

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
            var deal_value = jQuery("#txt_redeem_deal_value").val();
            var numbers = /^[0-9]+$/;
            var float_value = /^[+-]?\d+(\.\d+)?$/;
            /*
             if(deal_value.match(float_value))
             {
             alert("match");
             }
             else
             {
             alert("not match");
             }
             return false;
             */
            if (deal_value.match(float_value) && deal_value != "")
            {

                if (deal_value <= 0)
                {
                    //alert("Enter proper deal value");
                    var head_msg = "<div class='head_msg'>Message</div>"
                    var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_update_sales"]; ?></div>";
                    var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                    jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                    //alert(content_msg);

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
                else
                {
                    return true;
                }
            }
            else
            {
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_update_sales"]; ?></div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

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
    }

    function processLoginorNot()
    {
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
            return true;
        }
    }

    function processLogJson(data)
    {
        // alert(data.status);
        $("#err_msg").html("");
        if (data.status == "true")
        {

            //  window.location = "redeem-deal.php?id="+data.id;

            //$("#div_error_content").html("<p>"+data.point_message+"</p>");

            var head_msg = "<div class='head_msg'>Message</div>"
            var content_msg = "<div class='content_msg'>" + "* " + data.point_message + "</div>";
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
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });
            $.ajax({
                type: "POST",
                url: 'process.php',
                data: 'id=' + data.id + '&btn_campaign_detail=true&coupon_code=' + $("#txt_barcode").val(),
                success: function (msg)
                {
                    $("#div_campaign_info").html(msg);
                    $("#btn_submit").css("display", "none");
                }
            });

        }
        else
        {
            // window.location = "redeem-deal.php";
            //$("#div_error_content").html("<p>"+data.message+"</p>");
            var head_msg = "<div class='head_msg'>Message</div>"
            var content_msg = "<div class='content_msg'>" + "* " + data.message + "</div>";
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
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });
            $("#div_campaign_info").html("");
            return false;
        }
    }
    function precheckingRedeem(formData, jqForm, options)
    {

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


            var val = parseInt($("#txt_redeem_deal_value").val());

            if (val <= 0)
            {
                //alert("Enter proper deal value");
                var head_msg = "<div class='head_msg'>Message</div>"
                var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_update_sales"]; ?></div>";
                var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

                //alert(content_msg);

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
            } else
            {
                return true;
            }
        }


    }
    function processRedeemJson(data)
    {
        if (data.status == "true") {
            var head_msg = "<div class='head_msg'>Message</div>"
            var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_coupon_redeemed"]; ?></div>";
            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupredeem' name='popupredeem' class='msg_popup_cancel'></div>";
            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);


            jQuery.fancybox({
                content: jQuery('#dialog-message').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });
            //window.location = "redeem-deal.php";
        } else
        {
            //$("#div_error_content").html("<p>"+data.message+"</p>");

            var head_msg = "<div class='head_msg'>Message</div>"
            var content_msg = "<div class='content_msg'>" + data.message + "</div>";
            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

            //alert(content_msg);

            jQuery.fancybox({
                content: jQuery('#dialog-message').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });

            $("#div_campaign_info").html("");
            return false;
        }
    }

    $("a#redeem-coupons").css("background-color", "orange");


    /****** AUTO COMPLETE VOUCHER CODE TEXT BOX FROM DATABASE *******/
    function gofor_autocomplete(val)
    {
        var flag = 0;
        jQuery.ajax({
            type: "POST",
            url: 'process.php',
            data: 'loginornot=true',
            async: true,
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

        if (flag == 0)
        {
            if (val.length >= 4)
            {
                //alert(val);
                url = '<?= WEB_PATH ?>/merchant/get_vouchercode.php';
                //alert(url);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "couponname=" + val,
                    async: true,
                    beforeSend: function () {
                        //closePopup(200);
                        //open_popup('Wait');				   				  
                    },
                    success: function (result) {

                        //alert(result);
                        document.getElementById("autocomplete").style.display = "block";
                        document.getElementById("autocomplete").innerHTML = result;


                    }
                });
            }
            else
            {
                document.getElementById("autocomplete").style.display = "none";
                document.getElementById("div_error_content").innerHTML = "";
            }
        }
    }
    jQuery("#popupcancel").live("click", function () {
        jQuery.fancybox.close();
        return false;
    });

    function repalcevalue(val)
    {
        var val = val.innerHTML;
        document.getElementById("txt_barcode").value = val;
        document.getElementById("autocomplete").style.display = "none";

        if (jQuery("#txt_barcode").val().length >= 10 && jQuery("#txt_redeem_deal_value").val().length > 0)
        {
            jQuery('#btn_redeem').removeAttr("disabled");
            jQuery('#btn_redeem').removeClass("disabled");
        }
        else
        {
            jQuery('#btn_redeem').attr("disabled", "");
            jQuery('#btn_redeem').addClass("disabled");
        }
    }
<!--// 369-->

    var merchant_ = '<?php echo $_SESSION['merchant_info']['merchant_parent']; ?>';
//alert(merchant_);
    if (merchant_ == 0)
    {
        var merchant_redeem = '<?php echo $_SESSION['merchant_info']['redeem_location']; ?>';
        //alert(merchant_redeem);
        if (merchant_redeem == "")
        {
            var head_msg = "<div class='head_msg'>Message</div>";
            var content_msg = "<div class='content_msg'><?php echo $merchant_msg["redeem-deal"]["Msg_select_location"]; ?></div>";
            var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' onclick='btncanprofile()' class='msg_popup_cancel'></div>";
            jQuery("#dialog-message").html(head_msg + content_msg + footer_msg);

            //alert(content_msg);

            jQuery.fancybox({
                content: jQuery('#dialog-message').html(),
                type: 'html',
                openSpeed: 300,
                closeSpeed: 300,
                changeFade: 'fast',
                helpers: {
                    overlay: {
                        closeClick: false,
                        opacity: 0.3
                    } // overlay
                }
            });
        }
    }
    function btncanprofile()
    {
        window.location = "<?= WEB_PATH ?>/merchant/my-profile.php";
    }

    jQuery("#main_redem_selection ul li").click(function () {
        processLoginorNot();
        jQuery("#main_redem_selection ul input").prop("checked", "false");
        jQuery(this).find("input").prop("checked", "true");
        //jQuery(this).find("input").trigger("click");

        var redem_option = jQuery(this).find("input").val();
        if (redem_option == "voucher")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#redem_div").css("display", "block");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "none");
        }
        else if (redem_option == "reward")
        {
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#reward_div").css("display", "block");
            jQuery("#campaign_list_div").css("display", "none");
            jQuery("#enter_reward_number").css("display", "block");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "none");

        }
        else if (redem_option == "stamp")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "block");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "none");
        }
        else if (redem_option == "rew_camp")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#rew_camp_div").css("display", "block");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "none");
        }
        else if (redem_option == "loy_rew_card")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "block");
            jQuery("#gift_card_div").css("display", "none");
        }
        else if (redem_option == "gift_card")
        {
            jQuery("#reward_div").css("display", "none");
            jQuery("#redem_div").css("display", "none");
            jQuery("#stamp_div").css("display", "none");
            jQuery("#rew_camp_div").css("display", "none");
            jQuery("#loy_rew_card_div").css("display", "none");
            jQuery("#gift_card_div").css("display", "block");
        }

    });

</script>
