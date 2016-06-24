<?php
/**
 * @uses change password
 * @used in pages : chnage-password.php,profile-left.php
 * @author Sangeeta Raghavani
 */
////require_once("../classes/Config.Inc.php");
check_merchant_session();
////include_once(SERVER_PATH."/classes/DB.php");
////$objDB = new DB('read');

$array_where['id'] = $_SESSION['merchant_id'];
$RS = $objDB->Show("merchant_user", $array_where);
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Change Password </title>
        <?php //require_once(MRCH_LAYOUT . "/head.php"); ?>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<link rel="stylesheet" href="<?= ASSETS_CSS ?>/m/bootstrap.css" />
		<script type="text/javascript" src="<?= ASSETS_JS ?>/bootstrap.min.js"></script>	
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/old_pass_strength.js"></script>
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/new_pass_strength.js"></script>
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/con_new_pass_strength.js"></script>
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/jquery.form.js"></script>
        <!-- Message box popup -->
        <link rel="stylesheet" type="text/css" href="<?= ASSETS_CSS ?>/m/fancybox/jquery.fancybox.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?= ASSETS_CSS ?>/m/fancybox/jquery.fancybox-buttons.css" media="screen" />
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/fancybox/jquery.fancybox.js"></script>
        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/fancybox/jquery.fancybox-buttons.js"></script>


        <!-- End Message box popup -->
        <script language="javascript">
                $(document).ready(function () {
                    // bind form using ajaxForm 
                    $('#change_password_form').ajaxForm({
                        beforeSubmit: processLoginorNot,
                        dataType: 'json',
                        success: processChangePasswordJson
                    });
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
                    jQuery("#popupcancel").live("click", function () {
                        jQuery.fancybox.close();
                        return false;
                    });


                });
                function processChangePasswordJson(data) {
                    if (data.status == "true") {

                        window.location.href = '<?= WEB_PATH ?>/merchant/change-password.php';
                    }
                    else
                    {

                        var head_msg = "<div class='head_msg'>Message Box</div>"
                        var content_msg = "<div class='content_msg'>" + data.message + "</div>";
                        var footer_msg = "<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok']; ?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
                        //alert(content_msg);
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
                            }
                        });


                    }
                }
                // 369
        </script>

    </head>

    <body>

        <div id="dialog-message" title="Message Box" style="display:none">

        </div>
        <div>

            <!--start header--><div>

                <?
                require_once(MRCH_LAYOUT."/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">
                <div id="content">

                    <table width="100%" border="0" cellspacing="2" cellpadding="2" >
                        <tr>
                            <td width="25%" align="left" valign="top">
                                <?
                                require_once(MRCH_LAYOUT."/profile-left.php");
                                ?>
                            </td>
                            <td width="75%" align="left" valign="top">
                                <form action="<?= WEB_PATH ?>/merchant/process.php" method="post" id="change_password_form">
                                    <table width="100%"  border="0" cellspacing="2" cellpadding="2" id="table_spacer">
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td class="table_errore_message" align="left" id="msg_div"><?= $_SESSION['msg'] ?>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="table_th_35"><?php echo $merchant_msg["changepassword"]["Field_current_password"]; ?></td>
                                            <td class="table_th_65">
                                                <input type="password" name="old_password" id="old_password" >
                                                <span id="result_old"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $merchant_msg["changepassword"]["Field_new_password"]; ?></td>
                                            <td><input type="password" name="new_password" id="new_password"  >
                                                <span id="result_new"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $merchant_msg["changepassword"]["Field_confirm_new_password"]; ?></td>
                                            <td><input type="password" name="con_new_password" id="con_new_password" >
                                                <span id="result_con_new"></span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <input type="submit" name="btnUpdatePassword" value="<?php echo $merchant_msg['index']['btn_save']; ?>" >
                                                &nbsp;&nbsp;
                                                <script>function btncanpassword() {
                                                            window.location = "<?= WEB_PATH ?>/merchant/my-account.php";
                                                        }

                                                </script>
                                                <input type="submit" name="btncancelpassword" value="<?php echo $merchant_msg['index']['btn_cancel']; ?>" onClick="btncanpassword()" >

                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                    </table>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <!--start footer--><div>
                <?
                require_once(MRCH_LAYOUT."/footer.php");
                ?>
                <!--end of footer--></div>

        </div>

    </body>
    <script type="text/javascript">
            //$("a#myprofile").css("background-color", "orange");
            $("a#password-link").css("color", "orange");
           // $("a#profile-link").css("color", "#0066FF");
           // $("a#payment-link").css("color", "#0066FF");
    </script>
</html>
<?
$_SESSION['msg'] = "";
?>
