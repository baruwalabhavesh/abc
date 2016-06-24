<?php
check_admin_session();

$gift_card_plans = $objDB->Conn->Execute('Select * from gift_card_plans where is_deleted=0');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Admin Panel</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="<?= ASSETS_CSS ?>/a/template.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/a/jquery-1.7.2.min.js"></script>
        <style type="text/css" title="currentStyle">
            @import "<?php echo ASSETS_CSS ?>/a/demo_page.css";
            @import "<?php echo ASSETS_CSS ?>/a/demo_table.css";
        </style>
        <script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf-8">
                $(document).ready(function () {
                    $('#giftcardplan').dataTable({
                        "sPaginationType": "full_numbers",
                        "bFilter": false,
                        "bLengthChange": false,
                        "bSort": false,
                        "info": false,
                        "iDisplayLength": 10,
                    });
                });
        </script>
    </head>
    <body>
        <div id="container">
            <?php
            require_once(ADMIN_LAYOUT . "/header.php");
            ?>
            <div id="contentContainer">
                <div  id="sidebarLeft">
                    <?php
                    require_once(ADMIN_VIEW . "/quick-links.php");
                    ?>
                </div>
                <div id="content">
                    <div><h2>Gift Card plans</h2></div>
                    <div>
                        <?php
                        echo $_SESSION['msg'];
                        $_SESSION['msg'] = "";
                        ?>
                    </div>
                    <div class=""><a href="add-giftcardplan.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png"></a></div>
                    <div>
                        <table class="tableAdmin" id="giftcardplan">
                            <thead>
                                <tr>
                                    <th align="left">Name</th>
                                    <th align="left">Minimum value</th>
                                    <th align="left">Maximum value</th>
                                    <th align="left">Currency</th>
                                    <th align="left">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($gift_card_plans->recordCount() > 0) {
                                        while ($row = $gift_card_plans->FetchRow()) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['name']; ?></td>
                                                    <td><?php echo $row['min_value']; ?></td>
                                                    <td><?php echo $row['max_value']; ?></td>
                                                    <td><?php echo $row['currency']; ?></td>
                                                    <td><a href="edit-giftcardplan.php?id=<?php echo $row['id']; ?>">Edit</a> | <a href="process.php?deletegiftcardplan=treu&id=<?php echo $row['id']; ?>">Delete</a></td>
                                                </tr>
                                                <?php
                                        }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>  
    </body>
</html>

