<?php
/* * ****** 
  @USE : my orders
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : success-order.php, profile-left.php
 * ******* */
//require_once("classes/Config.Inc.php");
check_customer_session();
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();

$arr = array();

// start total points redeemed 28 07 214

$total_sum_gifcard = 0;

/* $Sql = "select giftcard_id from giftcard_order where user_id=".$_SESSION['customer_id']." and status!=0" ;
  $RS = $objDB->Conn->Execute($Sql); */
$RS = $objDB->Conn->Execute("select giftcard_id from giftcard_order where user_id=? and status!=0", array($_SESSION['customer_id']));

if ($RS->RecordCount() > 0) {
        while ($Row = $RS->FetchRow()) {
                /* $Sql1 = "select id,redeem_point_value from giftcards where id=" . $Row['giftcard_id'];
                  $RS1 = $objDB->Conn->Execute($Sql1); */
                $RS1 = $objDB->Conn->Execute("select id,redeem_point_value from giftcards where id=?", array($Row['giftcard_id']));

                if ($RS1->RecordCount() > 0) {
                        $total_sum_gifcard = $total_sum_gifcard + $RS1->fields['redeem_point_value'];
                }
        }
}

// end total points redeemed 28 07 214	
?>
<!DOCTYPE HTML>
<html>
        <head>
                <title>ScanFlip | My Orders</title>
                <?php require_once(CUST_LAYOUT . "/head.php"); ?>
                <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        </head>

        <body>

                <script src="<?php echo ASSETS_JS ?>/c/jquery.js"></script>
                <style type="text/css" title="currentStyle">
                    @import "<?php echo ASSETS_CSS ?>/c/demo_page.css";
                    @import "<?php echo ASSETS_CSS ?>/c/demo_table.css";
                </style>
                <!-- start scroll top -->    
                <!-- jquery -->

                <script src="<?php echo ASSETS_JS; ?>/c/jquery.tooltip.js" type="text/javascript"></script>
                <link rel="stylesheet" href="<?php echo ASSETS_CSS ?>/c/ui.totop.css" />
                <link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">
                <link rel="stylesheet" href="<?php echo ASSETS_CSS ?>/c/jquery.tooltip.css" />
                <?php
                require_once(CUST_LAYOUT . "/header.php");
                ?>
                <div id="content" class="cantent">
                        <div class="my_main_div">
                                <div id="contentContainer" class="contentContainer">
                                    <?php
                //require_once(SERVER_PATH."/templates/zipcodediv.php");
                                    ?>
                                        <div style="min-height:530px;" class="manage_profile">

                                            <!-- easing plugin ( optional ) -->
                                            <script src="<?php echo ASSETS_JS; ?>/c/easing.js" type="text/javascript"></script>
                                            <!-- UItoTop plugin -->
                                            <script src="<?php echo ASSETS_JS; ?>/c/jquery.ui.totop.js" type="text/javascript"></script>
                                            <!-- Starting the plugin -->


                                            <script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS; ?>/jquery.dataTables.js"></script>
                                            <script type="text/javascript" charset="utf-8">
                                                    $(document).ready(function () {

                                                        var table = $('#myordertable').dataTable({
                                                            'bFilter': false,
                                                            "sPaginationType": "full_numbers",
                                                            "bProcessing": true,
                                                            "bServerSide": true,
                                                            "iDisplayLength": 25,
                                                            "sAjaxSource": "<?php echo WEB_PATH; ?>/process.php",
                                                            //"iDeferLoading": <?php //echo $total_records1;             ?>,
                                                            "fnServerParams": function (aoData) {
                                                                aoData.push({"name": "getCustomerorders", "value": true}, {"name": 'customer_id', "value": '<?php echo $_SESSION['customer_id']; ?>'}, {"name": 'days', "value": $('#filter_orders').val()});

                                                            },
                                                            "fnDrawCallback": function () {
                                                                bind_note_event();
                                                            },
                                                            "aoColumns": [
                                                                {"bVisible": true, "bSearchable": false, "bSortable": false},
                                                                {"bVisible": true, "bSearchable": false, "bSortable": false},
                                                                {"bVisible": true, "bSearchable": false, "bSortable": false},
                                                                {"bVisible": true, "bSearchable": false, "bSortable": false},
                                                            ]

                                                        });

                                                        $(document).on('change', '#filter_orders', function () {
                                                            table.fnDraw();
                                                        });

                                                        $().UItoTop({easingType: 'easeOutQuart'});
                                                    });
                                            </script>

                                                <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                                                        <tr>
                                                                <td width="15%" align="left" valign="top">
                                                                    <?php
                                                                    require_once(CUST_LAYOUT . "/profile-left.php");
                                                                    ?>
                                                                </td>
                                                                <td class="mypoint" width="85%" align="left" valign="top">

                                                                        <div class="point_balance">
                                                                                <span class="c1" > <span class="curr_pnt_bal"><b>Total Points Redeemed</b>
                                                                                        <span class="star_bg"></span><span class="c2" id="balance"> <?php echo $total_sum_gifcard; ?> </span></span> </span>  
                                                                                <span class="c3"> View : 
                                                                                        <select name="filter_orders" id="filter_orders">
                                                                                            <!--                        <option value="0"> -- All -- </option>-->
                                                                                            <option value="90"> Last 90 days </option>
                                                                                            <option value="180"> Last 6 Months </option>
                                                                                            <option value="365"> Last 1 Year </option>
                                                                                        </select>
                                                                                </span>
                                                                        </div>      
                                                                    

                                                                        <div id="tbl_campaigns">
                                                                                <table width="100%"  border="0" cellspacing="1" cellpadding="2" class="tableMerchant" id="myordertable">
                                                                                        <thead>
                                                                                                <tr>
                                                                                                        <th align="left" class="tableDealTh" >Order Date</th>
                                                                                                        <th align="left" class="tableDealTh" >Order Number</th>              
                                                                                                        <th align="left" class="tableDealTh" style="width:230px;">Description</th>
                                                                                                        <th align="left" class="tableDealTh">Order Status</th>
                                                                                                </tr>
                                                                                        </thead>

                                                                                </table>


                                                                        </div>
                                                                    <div style="clear: both"></div>

                                                                </td>
                                                        </tr>
                                                </table>
                                        </div>
                                </div>
                            <?php require_once(CUST_LAYOUT . "/before-footer.php"); ?>
                        </div>
                </div>
            <?
            require_once(CUST_LAYOUT."/footer.php");
            ?>
        </body>

</html>
<script type="text/javascript">
        $("a#myorder-link").css("color", "orange");
        $(document).ready(function () {
            $(".btnfilterstaticscampaigns").on("click", function () {
                // alert($(this).attr('mycatid'));
                $(".btnfilterstaticscampaigns").each(function (index) {
                    $(this).css("color", "#3B3B3B");
                });
                $(this).css('color', 'orange');

                open_popup('Notification');


                var val_c = $(this).attr('mycatid');
                setCookie("cat_remember", val_c, 365);
                // alert('get_filter_campaigns=true&year='+val_y+'&month='+val_m+"&category="+val_c);
                $.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: 'get_filter_my_points=true&category=' + val_c,
                    success: function (msg)
                    {
                        //alert(msg);
                        $("#tbl_campaigns").html(msg);

                        //$('#example').dataTable().fnDestroy();
                        $('#example').dataTable({
                            "sPaginationType": "full_numbers",
                            'bFilter': true,
                            "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                            "iDisplayLength": 5,
                            "aoColumns": [{"bSortable": false},
                                null,
                                //null,

                                {"bSortable": false}
                            ]

                        });


                        close_popup('Notification');
                        bind_hover();
                        //bind_print_event();
                    }
                });


            });



        });

        function close_popup(popup_name)
        {

            $("#" + popup_name + "FrontDivProcessing").fadeOut(100, function () {
                $("#" + popup_name + "BackDiv").fadeOut(100, function () {
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


            $("#" + popup_name + "FrontDivProcessing").fadeIn(100, function () {
                $("#" + popup_name + "BackDiv").fadeIn(100, function () {
                    $("#" + popup_name + "PopUpContainer").fadeIn(100, function () {

                    });
                });
            });


        }
        function open_popup1(popup_name)
        {
            $ = jQuery.noConflict();
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
        bind_hover();
        function bind_hover()
        {
            $('.miles').hover(
                    function () {
                        $(this).find('span').css("display", "inline");
                    },
                    function () {
                        $(this).find('span').css("display", "none");
                    }
            );
        }
</script>

<script type="text/javascript">

        function bind_note_event()
        {
            jQuery(".notclass").click(function () {

                var ship_to = jQuery(this).attr("ship_to");
                var reward_detail = jQuery(this).attr("reward_detail");
                var reward_value = jQuery(this).attr("reward_value");
                var point_redeemed = jQuery(this).attr("point_redeemed");
                var notes = jQuery(this).attr("notes");
                ship_to = ship_to.substr(0, ship_to.lastIndexOf(","));

                //alert(notes.length);
                if (notes.length > 413)
                {
                    notes = notes.substr(0, 413) + '...';
                }
                jQuery.fancybox({
                    content: jQuery("#fancybox_order_detail").html(),
                    type: 'html',
                    openSpeed: 300,
                    closeSpeed: 300,
                    // topRatio: 0,
                    changeFade: 'fast',
                    beforeShow: function () {
                        jQuery(".fancybox-wrap").addClass("dsiplay_not");
                        jQuery(".fancybox-wrap .fancybox-inner #fancybox_order_detail_wrapper #ship_to #address").html(ship_to);
                        jQuery(".fancybox-wrap .fancybox-inner #fancybox_order_detail_wrapper #order_detail #reward_detail #reward_dtail").html(reward_detail);
                        jQuery(".fancybox-wrap .fancybox-inner #fancybox_order_detail_wrapper #order_detail #reward_value #reward_vlue").html("$" + reward_value);
                        jQuery(".fancybox-wrap .fancybox-inner #fancybox_order_detail_wrapper #order_detail #point_redeemed #point_redeem").html(point_redeemed);
                        jQuery(".fancybox-wrap .fancybox-inner #fancybox_order_detail_wrapper #notes #note").html(notes);
                    },
                    helpers: {
                        overlay:
                                {
                                    closeClick: false,
                                    opacity: 0.3
                                } // overlay
                    }
                });
            });
        }
        // bind_note_event();
</script>
<div id="fancybox_order_detail" style="display:none;">
    <div id="fancybox_order_detail_wrapper">
        <div id='ship_to'>
            <span class="fancybox_orderpopup_title">Ship to :</span> <span id="address">Vijay Advani , 34 little Norway crescent, apt# 203, ,  Toronto, ON, M5V3A3, Canada, 4163033355</span>
        </div>
        <div id='order_detail'>
            <div id="reward_detail">
                <span class="fancybox_orderpopup_title">Reward Detail :</span> <span id="reward_dtail">Best Buy $50 Gift Card</span>
            </div>
            <div id="reward_value">
                <span class="fancybox_orderpopup_title">Reward Value :</span> <span id="reward_vlue">500</span>
            </div>
            <div id="point_redeemed">
                <span class="fancybox_orderpopup_title">Points Redeemed :</span> <span id="point_redeem">10</span>
            </div>
        </div>
        <div id='notes'>
            <span class="fancybox_orderpopup_title">Notes :</span> <span id="note">defalut note</span>
        </div>
    </div>
</div>
