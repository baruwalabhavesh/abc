<?php
/**
 * @uses my account
 * @used in pages : add-compaign.php,add-location.php,add-user.php,apply_pointfilter.php,change-password.php,compaigns.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");

check_merchant_session();
// 369
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');

$array = array();
$months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
$str_activated = $str_expired = $str_reddem = "";
$str_activated1 = $str_expired1 = $str_reddem1 = "";
$today_date = date("Y-m-d") . " 00:00:00";
$Where = "";


/* $Sql="select * from merchant_user where id=".$_SESSION['merchant_id'];
  $RS = $objDB->Conn->Execute($Sql); */
$RS = $objDB->Conn->Execute("select * from merchant_user where id=?", array($_SESSION['merchant_id']));
if ($RS->RecordCount() > 0) {
        while ($Row = $RS->FetchRow()) {

                if ($Row['phone_number'] == "" || $Row['address'] == "") {

                        $_SESSION['notsetprofile'] = "yes";
                        $_SESSION['profilecountry'] = $Row['country'];
                }
        }
}
//$Where .= " AND expiration_date >= '".$today_date."' ";
//$Sql = "SELECT * FROM campaigns WHERE  $Where ORDER BY id DESC";
//$RS = $objDB->Conn->Execute($Sql);
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | My Account</title>
        <?php require_once(MRCH_LAYOUT . "/head.php"); ?>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Cache-control" content="public,max-age=86400,must-revalidate">
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

    </head>

    <body>
        <div style="width:100%;text-align:center;">
            <script src="<?= ASSETS_JS ?>/m/highcharts.js"></script>
            <script language="javascript">

                    var chart;
                    $(document).ready(function () {



                        jQuery(".dashboard-icon").click(function () {

                            var linkurl = jQuery(this).find("a").attr("link");

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


                                        window.location.href = "<?= WEB_PATH ?>/merchant/" + linkurl;

                                    }
                                }
                            });

                        });


                    });
                    jQuery("#popupcancel").live("click", function () {
                        jQuery.fancybox.close();
                        return false;
                    });
            </script>
            <!---start header---->
            <div>
                <?
                require_once(MRCH_LAYOUT."/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">

                <div style="margin-left:auto;margin-right:auto;" id="fadeshow11">

                    <!--end of slide--></div>
                <div id="content">


                    <h1 Style=" width:100%; text-align:center; display:inline-block;">Dashboard</h1>        

                    <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                        <tr>
                            <!-- // 369 -->
                            <td width="20%" align="center" valign="top">
                                <?
                                require_once(MRCH_LAYOUT."/my-account-left.php");
                                ?>
                            </td>
                        </tr>
                    </table>


                    <div class="clear">&nbsp;</div>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>

            <!---------start footer--------------->
            <div>
                <?
                require_once(MRCH_LAYOUT."/footer.php");
                ?>
                <!--end of footer--></div>

        </div>
        <div class="campaign_detail_div" style="display: none">

            <div style="width: 400px;height: 310px;text-align: left !important">
                <div style='line-height:2;height:40px;width:100%;border-radius:4px 4px 4px 4px;background:#D5D5D5;font-size:18px;font-weight:bold;text-align:center'>
                    Update Profile Detail
                </div>
                <form id="profileform">

                    <table style="padding: 10px">

                        <tr>
                            <td>
                                Address :
                            </td>
                            <td>
                                <input type="text" style="width: 150px" id="address" class="addressclass" name="address">

                            </td>
                        </tr>
                        <tr style="height: 20px">
                            <td colspan="2">

                                <div  class="errormsgaddressclass" style="margin-left: 111px;color:red;font-size:12px;display: none;height: 17px">&nbsp;</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                City :
                            </td>
                            <td>
                                <input type="text" style="width: 150px" id="city" class="addressclass" name="address">

                            </td>
                        </tr>
                        <tr style="height: 20px">
                            <td colspan="2">

                                <div  class="errormsgcityclass" style="margin-left: 111px;color:red;font-size:12px;display: none;height: 17px">&nbsp;</div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Zipcode :
                            </td>
                            <td>
                                <input type="text" style="width: 150px" id="zipcode" class="addressclass" name="address">

                            </td>
                        </tr>

                        <tr style="height: 20px">
                            <td colspan="2">

                                <div  class="errormsgpostalcodeclass" style="margin-left: 111px;color:red;font-size:12px;display: none;height: 17px">&nbsp;</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Phone Number :
                            </td>
                            <td>
                                <select name="mobile_country_code" id="mobile_country_code">
                                    <option value="001">001</option>
                                </select>-
                                <input type="text" name="mobileno_area_code" id="mobileno_area_code" style="width:30px; " value="" maxlength="3">-
                                <input type="text" name="mobileno2" id="mobileno2" style="width:30px; " value="" maxlength="3">-
                                <input type="text" name="mobileno" id="mobileno" style="width:40px; " value="" maxlength="4">

                            </td>
                        </tr>
                        <tr style="height: 20px">
                            <td colspan="2">

                                <div  class="errormsgphoneclass" style="margin-left: 111px;color:red;font-size:12px;display: none;height: 17px">&nbsp;</div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" align="center">
                                <input type="button" name="btnUpdateProfile" value="Save" id="btnUpdateProfile" >
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>


    </body>
    <!--// 369 -->
    <script type="text/javascript">
            $("a#myaccount").css("background-color", "orange");

    </script>
</html>
