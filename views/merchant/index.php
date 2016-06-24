<?php
/**
 * @uses home page 
 * @used in pages :
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip Merchant</title>
        <?php require_once(MRCH_LAYOUT . "/head.php"); ?>
        <meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <script type="text/javascript" src="<?= ASSETS_JS ?>/m/jquery.form.js"></script>
        <script language="javascript">
                jQuery(document).ready(function () {
                    // bind form using ajaxForm 
                    jQuery('#search_frm').ajaxForm({
                        dataType: 'json',
                        success: processSearchJson
                    });

                });
                function processSearchJson(data) {

                    if (data.status == "true") {
                        window.location = data.message;
                    } else {
                        window.location = "search-deal.php";
                        return false;
                    }

                }

        </script>
        <!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>-->
        <script type="text/javascript" src="<?php echo ASSETS_JS; ?>/m/fadeslideshow.js"></script>

        <script type="text/javascript">
                var mygallery1 = new fadeSlideShow({
                    wrapperid: "fadeshow11", //ID of blank DIV on page to house Slideshow
                    dimensions: [1000, 304], //width/height of gallery in pixels. Should reflect dimensions of largest image
                    imagearray: [
                        ["/assets/images/m/slide/slide1.jpg", "", "", ""],
                        ["/assets/images/m/slide/slide2.jpg", "", "", ""],
                        ["/assets/images/m/slide/slide3.jpg", "", "", ""]

                    ],
                    displaymode: {type: 'auto', pause: 2500, cycles: 0, wraparound: false},
                    persist: false, //remember last viewed slide and recall within same session?
                    fadeduration: 1500, //transition duration (milliseconds)
                    descreveal: "ondemand",
                    togglerid: ""
                })

        </script>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

    </head>

    <body>
        <div >
            <!--start header--><div>

                <?
                require_once(MRCH_LAYOUT."/header.php");

                if(isset($_SESSION['profile_complete']))
                {
                if($_SESSION['profile_complete']==0)
                {
                header("Location:".WEB_PATH."/merchant/merchant-setup.php");
                }
                else
                {
                //header("Location:".WEB_PATH."/merchant/my-account.php");
                }
                }

                ?>
                <!--end header--></div>
            <div id="contentContainer">

                <div id="fadeshow11">

                    <!--end of slide--></div>
                <div id="content">

                    <?php
                    echo $merchant_msg['index']['middle_content'];
                    ?>
                    <div class="clear">&nbsp;</div><!--end of content--></div>

                <!--end of contentContainer--></div>

            <!--start footer--><div>
                <?
                require_once(MRCH_LAYOUT."/footer.php");
                ?>
                <!--end of footer--></div>
        </div>
    </body>
</html>
