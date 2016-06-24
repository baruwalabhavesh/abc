<?php
/**
 * @uses why scanflip page
 * @used in pages :header.php
 * 
 */
//require_once("../classes/Config.Inc.php");
//check_merchant_session();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Benefits </title>
        <?php require_once(MRCH_LAYOUT . "/head.php"); ?>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

    </head>

    <body>
        <div>

            <!---start header---->
            <div>
                <?
                require_once(MRCH_LAYOUT."/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">

                <div id="content">

                    <h1> Benefits of using Scanflip </h1>

                    <div class="sectionB">
                        <ul><li class="whyscanflipheading">Practical and easy to manage</li></ul>
                    </div>
                    <div class="sectionA">Your Scanflip local business listing is an easy way to maintain an on-line presence even if you don't have a website. You can visit Scanflip any time to edit your information or manage your campaigns .
                    </div>
                    <div class="sectionB">
                        <ul><li class="whyscanflipheading">Premium options, all for free</li></ul>
                    </div>
                    <div class="sectionA">Make your listing really shine with your business details, services and products you offer, location attributes, photos , to encourage customers to make a first-time or repeat purchase.
                    </div>
                    <div class="sectionB">
                        <ul><li class="whyscanflipheading">Business Owner Dashboard.</li></ul>
                    </div>
                    <div class="sectionA">Monitor all locations in a single business account with real-time traffic analytics. Create campaigns and measure performance of your campaigns across all you locations. Redeem voucher code for your campaigns
                    </div>
                    <div class="sectionB">
                        <ul><li class="whyscanflipheading">Additional Dashboard Features </li></ul>
                    </div>
                    <div class="sectionA"> Manage your business profile, location addresses, location attributes, photos, customer list, campaign templates , Scanflip Smart QR Codes , marketing material and employee permission for using Scanflip dashboard for business.</div>	
                    <div class="sectionB">
                        <ul><li class="whyscanflipheading">Signing up for Scanflip is simple</li></ul>
                    </div>
                    <div class="sectionA">If you own a business, you probably know the basics. You can add extras like photos and descriptions if you have them handy, or come back to add them later. At the end of the 
                        <?php
                        if (isset($_SESSION['merchant_id']) && $_SESSION['merchant_id'] != "") {
                                ?>
                                sign-up
                                <?php
                        } else {
                                ?>
                                <a href="<?php echo WEB_PATH . '/merchant/register.php'; ?>">sign-up</a>
                                <?php
                        }
                        ?>, we'll verify your submission to make sure your listing appear correctly on scanflip.
                    </div>

                    <div class="clear">&nbsp;</div>
                    <!--end of content--></div>
                <!--end of contentContainer-->
            </div>

            <!---------start footer--------------->
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                ?>
                <!--end of footer--></div>

        </div>
    </body>
</html>
