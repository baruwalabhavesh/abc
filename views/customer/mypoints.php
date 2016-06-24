<?php
/* * ****** 
  @USE : my points page
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : my-orders.php, myreviews.php, profile-left.php
 * ******* */
//require_once("classes/Config.Inc.php");
check_customer_session();
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");

if (isset($_COOKIE['myck'])) {
        $myck = $_COOKIE['myck'];
}
$mlatitude = "";
$mlongitude = "";

if (isset($_COOKIE['myck'])) {
        if ($myck == "true") {
                $locc = GetLatitudeLongitude($_COOKIE['searched_location']);
                if ($locc != "") {

                        $mlatitude = $locc["location"]["latitude"];
                        $mlongitude = $locc["location"]["longitude"];

                        $cookie_life = time() + 31536000;
                        setcookie('mycurrent_lati', $mlatitude, $cookie_life);
                        setcookie('mycurrent_long', $mlongitude, $cookie_life);

                        $_SESSION['mycurrent_lati'] = $mlatitude;
                        $_SESSION['mycurrent_long'] = $mlongitude;
                }
        } else {
                $mlatitude = $_COOKIE['mycurrent_lati'];
                $mlongitude = $_COOKIE['mycurrent_long'];
                $lati = $_COOKIE['mycurrent_lati'];
                $long = $_COOKIE['mycurrent_long'];

                if ($lati == "" && $long == "") {
                        if ($_COOKIE['searched_location'] == "") {
                                $mlatitude = $_SESSION['customer_info']['curr_latitude'];
                                $mlongitude = $_SESSION['customer_info']['curr_longitude'];
                        } else {

                                $mlatitude = $_COOKIE['mycurrent_lati'];
                                $mlongitude = $_COOKIE['mycurrent_long'];
                        }
                } else {
                        $curr_latitude = $lati;
                        $curr_longitude = $long;
                }
        }
}
if (isset($_COOKIE['miles_cookie'])) {
        $miles = $_COOKIE['miles_cookie'];
} else {
        $miles = 50;
}
// to set lati & long in cookie

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
    </head>

    <!--<body onload="checkCookie()">-->
    <body >

        <script src="<?php echo ASSETS_JS; ?>/c/jquery.js"></script>
        <style type="text/css" title="currentStyle">
            @import "<?php echo ASSETS_CSS; ?>/c/demo_page.css";
            @import "<?php echo ASSETS_CSS; ?>/c/demo_table.css";
        </style>
        <!-- start scroll top -->    


        <script src="<?php echo ASSETS_JS; ?>/c/jquery.tooltip.js" type="text/javascript"></script>
        <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>/c/ui.totop.css" />
        <link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>/c/jquery.tooltip.css" />
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


                        <script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
                        <script type="text/javascript" charset="utf-8">
                                $(document).ready(function () {

                                    var days = $('#filter_transactions').val();

                                    var table = $('#mytransactions').dataTable({
                                        'bFilter': false,
                                        "sPaginationType": "full_numbers",
                                        "bProcessing": true,
                                        "bServerSide": true,
                                        "iDisplayLength": 25,
                                        "sAjaxSource": "<?php echo WEB_PATH; ?>/process.php",
                                        //"iDeferLoading": <?php //echo $total_records1; ?>,
                                        "fnServerParams": function (aoData) {
                                            aoData.push({"name": "getfiltermy_points", "value": true}, {"name": 'lati', "value": '<?php echo $mlatitude; ?>'}, {"name": "long", "value": '<?php echo $mlongitude; ?>'}, {"name": 'customer_id', "value": '<?php echo $_SESSION['customer_id']; ?>'}, {"name": 'miles', "value": '<?php echo $miles; ?>'}, {"name": 'days', "value": $('#filter_transactions').val()});

                                        },
                                        "aoColumns": [
                                            {"bVisible": true, "bSearchable": false, "bSortable": false},
                                            {"bVisible": true, "bSearchable": false, "bSortable": false},
                                            {"bVisible": true, "bSearchable": false, "bSortable": false},
                                            {"bVisible": true, "bSearchable": false, "bSortable": false},
                                            {"bVisible": true, "bSearchable": false, "bSortable": false},
                                        ]
                                    });
                                    //table.fnDraw();

                                    $('#filter_transactions').change(function () {
                                        var days = $('#filter_transactions').val();
                                        table.fnDraw();
                                    });


                                    $().UItoTop({easingType: 'easeOutQuart'});

                                });</script>

                        <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                            <tr>
                                <td width="15%" align="left" valign="top">
                                    <?
                                    require_once(CUST_LAYOUT."/profile-left.php");
                                    ?>
                                </td>
                                <td class="mypoint" width="85%" align="left" valign="top">


                                    <!-- <div style="clear:both"><br/></div> -->

                                    <div class="point_balance">
                                        <span class="c1" > <span class="curr_pnt_bal"><b>Current Point Balance</b>
                                                <span class="star_bg"></span><span class="c2" id="balance"> <?php echo $point_balance; ?> </span></span> </span>  
                                        <span class="c3"> View : 
                                            <select name="filter_transactions" id="filter_transactions">
                                                <!--                        <option value="0"> -- All -- </option>-->
                                                <option value="7"> Last 7 days </option>
                                                <option value="45"> Last 45 days </option>
                                                <option value="365"> 1 Year </option>
                                            </select>
                        <!--                    <input type="submit" value="Shop & Redeem"/> --> </span>
                                    </div>
                        <!--                     <input type="text" id="mysearch_text" name="mysearch_text" value=""/>
                                             <input type="button" id="mysearch_btn" name="mysearch_btn" value="Filter"/>-->

                                    <div id="tbl_campaigns">
                                        <table width="100%"  border="0" cellspacing="1" cellpadding="2" class="tableMerchant" id="mytransactions">
                                            <thead>
                                                <tr>
                                                    <th align="left" class="tableDealTh" style="width:155px;">Date</th>
                                                    <th align="left" class="tableDealTh" >Merchant Location</th>              
                                                    <th align="left" class="tableDealTh" >Campaign Title</th>

                                                    <th align="left" class="tableDealTh">Points Earned</th>

                                                    <th align="left" class="tableDealTh">Amount Spent</th>
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

    <div id="NotificationLoadingDataPopUpContainer" class="container_popup"  style="display: none;">
        <div id="NotificationLoadingDataBackDiv" class="divBack">
        </div>
        <div id="NotificationLoadingDataFrontDivProcessing" class="Processing" style="display:none;">

            <div id="NotificationLoadingDataMaindivLoading" align="center" valign="middle" class="textDivLoading"
                 style="left: 45%;top: 40%;">

                <div id="NotificationLoadingDatamainContainer" class="loading innerContainer" style="height:auto;width:auto">
                    Loading ...
                </div>
            </div>
        </div>
    </div>

</html>
<script type="text/javascript">
        $("a#mypoints-link").css("color", "orange");

        $(document).ready(function () {
           /* $(".btnfilterstaticscampaigns").live("click", function () {
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
            });*/
        });
        /*
         $('#mysearch_btn').click(function() {
         tableobj=$('#example').dataTable();
         tableobj.fnFilter($('#mysearch_text').val());
         });
         */
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
<div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
    <div id="NotificationBackDiv" class="divBack">
    </div>
    <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">

        <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading"
             style="left: 45%;top: 40%;">

            <div id="NotificationmainContainer" class="innerContainer" style="height:auto;width:auto">
                <img src="<?php echo ASSETS_IMG; ?>/c/loading.gif" style="display: block;" id="image_loader_div"/>
            </div>
        </div>
    </div>
</div>

<?php

function GetLatitudeLongitude($_zipcodeaddress) {

//echo $_zipcodeaddress."GetLatitudeLongitude";
        $_zipcodeaddress = urlencode($_zipcodeaddress);
        $_geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $_zipcodeaddress . "&sensor=false");
        $_geojson = json_decode($_geocode, true);
//print_r($_geojson);
        if ($_geojson['status'] == 'OK') {
                $_lat = $_geojson['results'][0]['geometry']['location']['lat'];
                $_lng = $_geojson['results'][0]['geometry']['location']['lng'];
                $_address = $_geojson['results'][0]['formatted_address'];

                $_output = array(
                        "location" =>
                        array(
                                "latitude" => "$_lat",
                                "longitude" => "$_lng",
                                "address" => "$_address[0]",
                                "city" => "$_address[1]",
                                "zip" => "$_address[2]",
                                "state" => "$_address[2]",
                                "country" => "$_address[2]"
                        ),
                );
        } else {
                $_output = '';
        }
        return $_output;
}
?>
