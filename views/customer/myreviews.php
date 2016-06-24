<?php
/* * ****** 
  @USE : display my reviews
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : getrating.php, profile-left.php, header.php
 * ******* */
//require_once("classes/Config.Inc.php");
check_customer_session();
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");

$arr = array();

$arr1 = array();
$arr1 = file(WEB_PATH . '/process.php?getuserpointsbalance=yes&customer_id=' . $_SESSION['customer_id']);
if (trim($arr1[0]) == "") {
        unset($arr1[0]);
        $arr1 = array_values($arr1);
}
$all_json_str1 = $arr1[0];
$json1 = json_decode($arr1[0]);
$point_balance = $json1->point_balance;
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | My Points</title>
        <?php require_once(CUST_LAYOUT . "/head.php"); ?>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css"> 
        <style type="text/css" title="currentStyle">
            @import "<?php echo ASSETS_CSS; ?>/c/demo_page.css";
            @import "<?php echo ASSETS_CSS; ?>/c/demo_table.css";
        </style>
        <!-- start scroll top -->    

        <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>/c/ui.totop.css" />
    </head>

    <body >
        <?php
        require_once(CUST_LAYOUT . "/header.php");
        ?>
        <div id="content" class="cantent">
            <div class="my_main_div">
                <div id="contentContainer" class="contentContainer">

                    <div style="min-height:530px;" class="manage_profile">

                        <!-- easing plugin ( optional ) -->
                        <script src="<?php echo ASSETS_JS; ?>/c/easing.js" type="text/javascript"></script>
                        <!-- UItoTop plugin -->
                        <script src="<?php echo ASSETS_JS; ?>/c/jquery.ui.totop.js" type="text/javascript"></script>
                        <!-- Starting the plugin -->


                        <script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS; ?>/jquery.dataTables.js"></script>
                        <script type="text/javascript" charset="utf-8">
                                $(document).ready(function () {

                                    $('#myreviews').dataTable({
                                        'bFilter': false,
                                        "sPaginationType": "full_numbers",
                                        "bProcessing": true,
                                        "bServerSide": true,
                                        "iDisplayLength": 25,
                                        "sAjaxSource": "<?php echo WEB_PATH; ?>/process.php",
                                        //"iDeferLoading": <?php //echo $total_records1;       ?>,
                                        "fnServerParams": function (aoData) {
                                            aoData.push({"name": "review_notification", "value": true}, {"name": 'customer_id', "value": '<?php echo $_SESSION['customer_id']; ?>'});

                                        },
                                        "aoColumns": [
                                            {"bVisible": true, "bSearchable": false, "bSortable": false},
                                            {"bVisible": true, "bSearchable": false, "bSortable": false},
                                            {"bVisible": true, "bSearchable": false, "bSortable": false},
                                            {"bVisible": true, "bSearchable": false, "bSortable": false},
                                        ]

                                    });

                                    $().UItoTop({easingType: 'easeOutQuart'});

                                });
                        </script>
                        <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                            <tr>
                                <td width="15%" align="left" valign="top">
                                    <?
                                    require_once(CUST_LAYOUT."/profile-left.php");
                                    ?>
                                </td>
                                <td width="85%" align="left" valign="top" class="myrevew">

                                    <div class="point_balance">
                                        <span class="pend_revew" >Pending Reviews</span>

                                    </div>
                                    <div id="tbl_campaigns">
                                        <table width="100%"  border="0" cellspacing="1" cellpadding="2" class="tableMerchant" id="myreviews">
                                            <thead>
                                                <tr>
                                                    <th align="left" style="width:20%;" class="tableDealTh" >Date</th>
                                                    <th align="left" style="width:25%;" class="tableDealTh" >Merchant Location</th>              
                                                    <th align="left" style="" class="tableDealTh" >Campaign Title</th>

                                                    <th align="left" style="" class="tableDealTh">Pending Reviews</th>

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
        $('#reviews').on('keyup', function () {
            //$(this).css('height', '100%');
        });
        $("a#mypending-link").css("color", "orange");

        jQuery(document).ready(function () {
            jQuery(document).on('click',".rateit2", function () {
//rating-main rated_campaign_id rated_location_id
                var msg = "";
                var head_msg = "<div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>Rate & Review Your Visit</div>"
                var content_msg = '<div style="text-align:left;margin-top:10px;padding:5px;">' + jQuery(".rating-main").html() + '</div>';
                var footer_msg = "<div><hr><input type='button'  value='Ok' id='popupcancel' name='popupcancel' style='cursor:pointer;background-color:#E6E6E6 !important;background-image:none !important;color:#000;border:1px solid #D3D3D3;border-radius:4px 4px 4px 4px;'></div>";
                jQuery("#dialog-message").html(head_msg + content_msg);//+footer_msg);
                var campid = $(this).attr("campid");
                var locid = $(this).attr("locid");
                var empid = $(this).attr("empid");

                jQuery.fancybox({
                    // content:jQuery('#dialog-message').html(),
                    href: "<?= WEB_PATH ?>/getrating.php?camp_id=" + campid + "&loc_id=" + locid + "&emplid=" + empid,
                    type: 'iframe',
                    openSpeed: 300,
                    closeSpeed: 300,
                    changeFade: 'fast',
                    width: 400,
                    height: 300,
                    afterShow: function () {
                        //rating
                        // jQuery(".fancybox-inner #rating").rety();
                        jQuery("#close").unbind("click");
                        $('.fancybox-close').attr('id', 'close');
                        jQuery("#close").detach();
                    },
                    afterClose: function () {

                    },
                    helpers: {
                        overlay: {
                            opacity: 0.3
                        } // overlay
                    }
                    ,
                    helpers:  {
                        overlay: {
                            opacity: 0.3,
                            closeClick: false
                        } // overlay
                    },
                    enableEscapeButton: false,
                    keys: {
                        close: null
                    }
                });

            });
        })

</script>
