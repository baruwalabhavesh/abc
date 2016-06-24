<?php
/**
 * @uses merchant services
 * @used in pages : header.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
//check_merchant_session();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | How it Works </title>
        <?php require_once(MRCH_LAYOUT."/head.php"); ?>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

    </head>

    <body>
        <div style="width:100%;text-align:center;">

            <!---start header---->
            <div>
                <?
                require_once(MRCH_LAYOUT."/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">

                <div id="content">


                    <h1>How It Works</h1>
                    <div class="sectionB">
                        <ul><li class="merchantserviceheading" style="font-weight:bold;">Pay for performance:</li></ul>
                    </div>
                    <div class="sectionA">You only pay when customer redeems campaigns at your location.<strong>Scanflip does not charge businesses for campaign ad displays based on pay per click</strong>, effectively minimizing marketing cost for campaigns.
                    </div>
                    <div class="sectionB">
                        <ul><li class="merchantserviceheading">Drive Measurable traffic to you Location:</li></ul>
                    </div>
                    <div class="sectionA">Control number of offer codes, reward points, referral points, campaign duration, campaign visibility, participating locations for your campaign and drive measurable traffic to your location. Make sure they visit you, not your competition-78% of people who search locally on their phone make a purchase.*
                    </div>
                    <div class="sectionB">
                        <ul><li class="merchantserviceheading">Reach the right customer</li></ul>
                    </div>
                    <div class="sectionA">Connect with customers who are looking for campaigns based on their location and interest. Actively use Scanflip Smart QR Codes to connect your campaign, messages and information to drive customers to your location.
                    </div>
                    <div class="sectionB">
                        <ul><li class="merchantserviceheading">Measure performance of your campaign</li></ul>
                    </div>
                    <div class="sectionA">Scanflip offer powerful analytics tools to monitor and measure performance of your campaign across multiple locations in a single business account.</br></div>
                    <span class="middle_heading">Before you get started, there are a few things to keep in mind:</span>
                    <div class="sectionB">
                        <ul>Right now, scanflip is available for businesses in USA and Canada</ul>
                    </div>		
                    <ul class="merchantserviceheading">It&#39;s free.</ul>
                    <div class="sectionA">Adding your local business listing to Scanflip is free. However your listing will be visible to customers when you are running campaigns on Scanflip.
                    </div>
                    <ul class="merchantserviceheading">Check out our quality guidelines.</ul>
                    <div class="sectionA">In order for your business location to be approved to appear on Scanflip, it should follow our <a href="<?php echo WEB_PATH . '/merchant/guideline.php'; ?>" >quality guidelines</a>. Be sure to familiarize yourself with them.&nbsp;</div>
                    <br />
                    <p>
                        <span class="merchant-service-footer" >*comScore local search usage study, 2013&nbsp;</span>
                    </p>



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
    </body>
</html>
