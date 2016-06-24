<?php
/**
 * @uses press release detail
 * @used in pages : press-release.php,header.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
/* $Sql_new = "SELECT * FROM press_release where status=1 and id=".$_REQUEST['id'];  
  $RS_press_release = $objDB->Conn->Execute($Sql_new); */
$RS_press_release = $objDB->Conn->Execute("SELECT * FROM press_release where status=1 and id=?", array($_REQUEST['id']));
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Press Release Details</title>
        <?php require_once(MRCH_LAYOUT . "/head.php"); ?>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

    </head>
    <body>

        <div class="my_main_div">
            <!--start header--><div class="my_inner_div">
                <?
                require_once(MRCH_LAYOUT."/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">
                <div id="content" >
                    <div class="press-release-detail">
                        <h1>Press Release Detail</h1>
                        <div class="stories_wrapper"  >

                            <?php
                            if ($RS_press_release->RecordCount() > 0) {
                                    while ($Row = $RS_press_release->FetchRow()) {
                                            ?>

                                            <div class="stories">
                                                <h4>
                                                    <?php echo $Row['title'] ?>
                                                </h4>
                                                <h5>
                                                    <?php
                                                    //echo $Row['release_date'];
                                                    echo date("F j, Y", strtotime($Row['release_date']));
                                                    ?>
                                                </h5>
                                                <?php echo $Row['description'] ?>
                                            </div> 

                                            <?php
                                    }
                            }
                            ?>

                        </div>
                    </div>
                    <!--end of content--></div>
                <!--end of contentContainer--></div>
            <!--start footer--><div>
                <?
                require_once(MRCH_LAYOUT."/footer.php");
                ?>
                <!--end of footer--></div>
        </div>

    </body>
</html>
