<?php
/* * ****** 
  @USE : my notification setting page
  @PARAMETER :
  @RETURN :
  @USED IN PAGES : profile-left.php, process.php
 * ******* */
//require_once("classes/Config.Inc.php");
check_customer_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objJSON = new JSON();
$JSON = $objJSON->get_customer_profile();
$RS = json_decode($JSON);
//$objDB = new DB();
//Update email settings

$where_array = array();
$where_array['id'] = $_SESSION['customer_id'];
$RS = $objDB->Show("customer_user", $where_array);
if ($RS->RecordCount() > 0) {
        $notification_setting = $RS->fields['notification_setting'];
}
//Update email settings
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Manage Notifications </title>
        <?php require_once(CUST_LAYOUT . "/head.php"); ?>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.2.min.js"></script>-->
        <link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">
    </head>


    <body>

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

                        <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                            <tr>
                                <td width="25%" align="left" valign="top">
                                    <?php
                                    require_once(CUST_LAYOUT . "/profile-left.php");
                                    ?>
                                </td>
                                <td width="75%" align="left" valign="top">
                                            <form method="post" action="<?= WEB_PATH ?>/process.php" ><!--- id="reg_form" action="<?= WEB_PATH ?>/process.php" -->
                                        <table width="100%"  border="0" cellspacing="2" cellpadding="2">

                                            <tr>

                                                <td colspan=2><h2>Manage Notifications</h2></td>

                                            </tr>
                                            <tr>
                                                <td colspan=2>
                                                    <?php
                                                    if (isset($_SESSION['msg_notification_setting'])) {
                                                            if ($_SESSION['msg_notification_setting'] != "") {
                                                                    ?>
                                                                    <div class="success" id="succssbox" style="display: block">
                                                                        <img src="<?php echo ASSETS_IMG; ?>/c/hoory.png" alt="" />
                                                                        <span id="msg_div1" style="font-size: 13px;font-weight: bold;margin-left:10px;"><?php echo $_SESSION['msg_notification_setting']; ?> </span>
                                                                    </div>
                                                                    <?php
                                                            }
                                                    }
                                                    ?>
                                                    <div class="warning" id="warningsbox_error" style="display:none;">
                                                        <img src="<?php echo ASSETS_IMG; ?>/c//wornning.png" alt="" />
                                                        <span id="msg_div_eroor" style="font-size: 13px;font-weight: bold;margin-left:10px;">
                                                            Search radius must be between 2 miles to 50 miles
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?php echo $language_msg["profile"]["notifications"]; ?></td>
                                                <td> <input type="radio" name="rdo_notification_setting" value="1" <?php if ($notification_setting == 1) echo "checked"; ?>   > ON &nbsp;&nbsp;
                                                    <input type="radio" name="rdo_notification_setting" value="0"  <?php if ($notification_setting != 1) echo "checked"; ?> > OFF</td>
                                            </tr>

                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>
                                                    <input type="submit" name="btnUpdatenotificationsettings" value="Save" id="btnUpdatenotificationsettings" >
                                                    <script>function btncanprofile() {
                                                                window.location = "<?= WEB_PATH ?>/my-deals.php";
                                                            }

                                                    </script>
                                                    <input type="submit" name="btncancelprofile" value="Cancel" onClick="btncanprofile()"  >
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
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
<?php
$_SESSION['msg_notification_setting'] = "";
?>
<?php ?>
<script language="javascript">
        jQuery('input').focus(function () {

            //jQuery("#succssbox").css("display","none");
            jQuery("#succssbox").fadeOut("slow");
        });
        jQuery(document).ready(function () {
            // bind form using ajaxForm 
            /*
             jQuery('#reg_form').ajaxForm({ 
             dataType:  'json', 
             success:   processUpdateJson 
             });
             */

        });
        function processUpdateJson(data) {
            alert(data.message);
            jQuery(".success").show();
            document.getElementById("msg_div").innerHTML = data.message;
            return false;
        }
</script>
<link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>/c/jquery.tooltip.css" />
<script src="<?php echo ASSETS_JS; ?>/c/jquery.tooltip.js" type="text/javascript"></script>
<script type="text/javascript">
        jQuery('.notification_tooltip').tooltip({
            track: true,
            delay: 0,
            showURL: false,
            showBody: "<br>",
            fade: 250
        });

        $("a#mynotification-link").css("color", "orange");
</script>
<style>
    h3 {
        color: black !important;
        font-size:14px !important;
        font-weight: lighter;
        background-color:hsl(0, 0%, 93%) !important;
        border:none !important;
    }
</style>
