<?php
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//echo SERVER_PATH.'/fb-sdk/src/facebook.php';
//require_once(SERVER_PATH.'/fb-sdk/src/facebook.php';
//include_once(SERVER_PATH.'/fb-sdk/src/facebook_secret.php");
//$objDB = new DB();

$web_path = WEB_PATH . "/";

$pageURL = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

// 28 04 2014 after last_tab coding because request_uri include query string parameter last_tab
$pageURL = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER['PHP_SELF'];
?>
<!-- Message box popup -->


<!--<link rel="stylesheet" type="text/css" href="<?= WEB_PATH ?>/merchant/css/fancybox/jquery.fancybox-buttons.css" media="screen" /> -->
<link rel="stylesheet" type="text/css" href="<?= ASSETS_CSS ?>/m/fancybox/jquery.fancybox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?= ASSETS_CSS ?>/m/merchantcss.css" media="screen" />

<!--<script type="text/javascript" src="<?= WEB_PATH ?>/merchant/js/fancybox/jquery.fancybox-buttons.js"></script>-->
<script type="text/javascript" src="<?= ASSETS_JS ?>/m/fancybox/jquery.fancybox.js"></script>
<div id="NotificationloaderPopUpContainer" class="container_popup"  style="display: none;">
    <div id="NotificationloaderBackDiv" class="divBack">
    </div>
    <div id="NotificationloaderFrontDivProcessing" class="Processing" style="display:none;">

        <div id="NotificationloaderMaindivLoading" align="center" valign="middle" class="imgDivLoading"
             >

            <div id="NotificationloadermainContainer" class="loading innerContainer" style="height:auto;width:auto;box-shadow:none">
                <img src="<?= ASSETS_IMG ?>/128.GIF" style="display: block;" id="image_loader_div"/>
            </div>
        </div>
    </div>
</div>
<!-- End Message box popup -->
<div class="header">


    <div id="headerContainer" class="wrapper">
        <div id="loginDiv" class="loginDiv">
            <?php
            if (isset($_SESSION['merchant_id'])) {
                    
            } else {
                    $_SESSION['merchant_id'] = "";
            }
            if ($_SESSION['merchant_id'] == "") {
                    ?>
                    <div class="login_regi">
                        <a id="loginlink" href="javascript:void(0);">
                            <img  src="<?php echo ASSETS_IMG; ?>/m/login-icon.png" alt="Login Icon"/>Sign In</a> 
                        </a>
                        <div id="lgn_popup">
                            <a class="cta-button" href="<?php echo WEB_PATH ?>/register.php">Scanflip Customer</a>
                            <a class="cta-button" href="<?php echo WEB_PATH ?>/merchant/register.php">Scanflip Merchant</a>
                        </div>
                    </div>
                    <?php
            } else {
                    if ($pageURL != $web_path . "merchant/merchant-setup.php") {
                            ?>
                            <?php
                            if (isset($_SESSION['facebook_merchant_register'])) {
                                    if ($_SESSION['facebook_merchant_register'] == 1) {
                                            ?>
                                            <?php
                                    }
                            } else {
                                    ?>
                                    <a id="logout_ele" href="<?= WEB_PATH ?>/merchant/logout.php"  >Logout</a>
                                    <?php
                            }
                            ?>
                            <?php
                    } else {
                            ?>
                            <a id="logout_ele" href="<?= WEB_PATH ?>/merchant/logout.php"  >Logout</a>
                            <!--<a href="<?= WEB_PATH ?>/merchant/my-profile.php">Your Profile<img style="padding-left:5px; border:0px;" src="images/setting-merchant.png" alt="Your Setting"/></a>-->
                            <?php
                    }
            }
            ?>
        </div>
        <?php
        if ($pageURL != $web_path . "merchant/merchant-setup.php") {
                ?>
                <div id="logo"><a href="<?= WEB_PATH . '/merchant/index.php' ?>"><img src="<?= ASSETS_IMG ?>/m/logo-merchant.png" class="scanflip_merchant_logo" alt="ScanFlip Merchant Logo"/></a><!--end of logo-->
                    <?php
            } else {
                    ?>
                    <div id="logo"><img src="<?= ASSETS_IMG ?>/m/logo-merchant.png" width="360" height="46" border="0px" alt="ScanFlip Merchant Logo"/><!--end of logo-->	
                        <?php
                }
                ?>
            </div>


            <div id="menuContainer"><img src="<?= ASSETS_IMG ?>/m/menubar-left.jpg" width="60" height="40" alt="Left Flip">
                <div id="menuBar">
                    <?php
                    if ($pageURL != $web_path . "merchant/merchant-setup.php") {
                            ?>
                            <ul>
                                <li class="firstmenu"><a href="<?= WEB_PATH ?>/merchant/index.php">Home</a></li>
                                <li><a href="<?= WEB_PATH ?>/merchant/why-scanflip.php">Why ScanFlip? </a></li>
                                <!--<li><a href="<?= WEB_PATH ?>/merchant/success-stories.php">Success Stories </a></li> -->
                                <li><a href="<?= WEB_PATH ?>/merchant/merchant-services.php">How it works</a></li>
                                <li><a href="<?= WEB_PATH ?>/merchant/contact-us.php">Contact Us </a></li>
                                <?php
                                if (isset($_SESSION['merchant_id'])) {
                                        if ($_SESSION['merchant_id'] != "") {
                                                $Sql = "SELECT * from merchant_user_role where merchant_user_id =" . $_SESSION['merchant_id'];
                                                $RS_role = $objDB->Conn->Execute($Sql);
                                                while ($Row_role = $RS_role->FetchRow()) {
                                                        $ass_page = unserialize($Row_role['ass_page']);
                                                        $ass_role = unserialize($Row_role['ass_role']);
                                                }
                                                ?>
                                                <?php
                                                if ($pageURL != $web_path . "merchant/merchant-setup.php") {
                                                        ?>

                                                        <!--// 369 -->
                                                        <?php
                                                        if ($pageURL != $web_path . "merchant/merchant-setup.php") {
                                                                ?>
                                                                <li><a class="dashborad" href="<?= WEB_PATH ?>/merchant/my-account.php">Dashboard</a></li>

                                                                <?php
                                                        }
                                                        ?>

                                                        <!--end regiMenuBar--></div>
                                                <!--end registeredMenu--></div>
                                            <?php
                                    }
                                    ?>
                                    <?php
                            }
                    }
                    ?>
                    </ul>
                    <?php
            }
            ?>
            <!--end menuBar--></div>
        <!--end menuContainer--></div>

    <!--end of headerConatiner--></div>

<script type="text/javascript">
        jQuery(document).ready(function () {
            var pathname = window.location.pathname;
            var str = "/merchant/press-release-detail.php";
            pathname.indexOf("/merchant/press-release-detail.php");

            if (pathname == "/merchant/merchant-services.php" || pathname == "/merchant/why-scanflip.php" || pathname == "/merchant/terms.php" || pathname.indexOf("/merchant/press-release-detail.php") != -1 || pathname == "/merchant/press-release.php" || pathname == "/merchant/privacy-assist.php" || pathname == "/merchant/contact-us.php" || pathname == "/merchant/verify_email.php" || pathname == "/merchant/register.php" || pathname == "/merchant/index.php" || pathname == "/merchant/" || pathname == "/merchant/forgot_password.php")
            {
            }
            else
            {
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

                    }
                });
            }

        });


        //jQuery('body').live("click", function () {
		 jQuery(document).on( "click","body", function () {
            jQuery.ajax({
                type: "POST",
                url: 'process.php',
                data: 'check_emplayee_active=true',
                async: true,
                success: function (msg)
                {
                    var obj = jQuery.parseJSON(msg);
                    //alert(obj.status);
                    if (obj.status == "false")
                    {
                        window.location.href = obj.redirecting;
                    }
                }
            });

        });


        String.prototype.startsWith = function (str)
        {
            return (this.match("^" + str) == str)
        }

</script>


<script type="text/javascript">

        jQuery(window).on('load', function () {
            var imgheight = jQuery("#fadeshow11").children().children().height();
            //alert(imgheight+"===image height1");
            jQuery("#fadeshow11").css("height", imgheight + "px");
        });

        jQuery(window).resize(function () {
            var imgheight = jQuery("#fadeshow11").children().children().height();
            //alert(imgheight+"===image height2");
            jQuery("#fadeshow11").css("height", imgheight + "px");
        });


</script>
