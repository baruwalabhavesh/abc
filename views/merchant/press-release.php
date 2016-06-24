<?php
/**
 * @uses press release page
 * @used in pages : press-release.php,header.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB('read');
/* $Sql_new = "SELECT * FROM press_release where status=1 order by id DESC";  
  $RS_press_release = $objDB->Conn->Execute($Sql_new); */
$RS_press_release = $objDB->Conn->Execute("SELECT * FROM press_release where status=? order by id DESC", array(1));
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Press Release</title>
        <?php require_once(MRCH_LAYOUT . "/head.php"); ?>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">

        <link href="<?php echo ASSETS_CSS ?>/m/demo_page.css" rel="stylesheet" type="text/css">
        <link href="<?php echo ASSETS_CSS ?>/m/demo_table.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
        <script type="text/javascript" >
                jQuery(document).ready(function () {
                    jQuery('#example').dataTable({
                        'bFilter': false,
                        "aaSorting": [],
                        "sPaginationType": "full_numbers",
                        //"aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                        "iDisplayLength": 5
                    });
                });
        </script>

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
                    <div class="press-release">
                        <h3>Press Release</h3>
                        <div class="stories_wrapper" style="" >
                            <table width="100%"  border="0" cellspacing="2" cellpadding="2" id="example">
                                <thead>
                                    <tr>
                                        <td ></td>
                                    </tr>
                                </thead>	
                                <tbody>
                                    <?php
                                    if ($RS_press_release->RecordCount() > 0) {
                                            while ($Row = $RS_press_release->FetchRow()) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="stories">
                                                                <h4>
                                                                    <a target="_blank" href="<?php echo WEB_PATH . "/merchant/press-release-detail.php?id=" . $Row['id'] ?>" ><?php echo $Row['title'] ?></a>
                                                                </h4>
                                                                <h5>
                                                                    <?php
                                                                    //echo $Row['release_date'];
                                                                    echo date("F j, Y", strtotime($Row['release_date']));
                                                                    ?>
                                                                </h5>
                                                                <?php //echo $Row['description'] ?>
                                                            </div> 
                                                        </td>
                                                    </tr>	
                                                    <?php
                                            }
                                    }
                                    ?>
                                </tbody>
                            </table>
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
