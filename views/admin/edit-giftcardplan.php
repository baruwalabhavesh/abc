<?php
check_admin_session();

$id = $_REQUEST['id'];
$gift_card_plans = $objDB->Conn->Execute('Select * from gift_card_plans where id=' . $id);
if ($gift_card_plans->RecordCount() > 0) {
        $gfp = $gift_card_plans->FetchRow();
}

$currencies = $objDB->Conn->Execute('Select * from currencies');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Admin Panel</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="<?= ASSETS_CSS ?>/a/template.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo ASSETS_JS ?>/a/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
        <script type="text/javascript">
                $(document).ready(function () {
                    $("#editgiftcardplan").validate({
                        rules: {
                            name: {
                                required: true,
                            },
                            min_val: {
                                required: true,
                                number: true,
                            },
                            max_val: {
                                required: true,
                                number: true,
                                checkval: true,
                            },
                            increment: {
                                required: true,
                                number: true,
                            }
                        },
                        messages: {
                            name: {
                                required: "Please enter a plan name"
                            },
                            min_val: {
                                required: "Please enter minimum value for gift card"
                            },
                            max_val: {
                                required: "Please enter maximum value for gift card"
                            },
                            increment: {
                                required: "Please enter value for increment"
                            }
                        }
                    });

                    $.validator.addMethod("checkval", function (value, element) {
                        var min = $('.min_val').val();
                        var max = $('.max_val').val();

                        if (min >= max) {
                            return false;
                        } else {
                            return true;
                        }

                    }, "Maximum value should be greter then the minimum value");
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
                    <div><h2>Edit Gift Card plan</h2></div>
                    <div>
                        <?php
                        echo $_SESSION['msg'];
                        $_SESSION['msg'] = "";
                        ?>
                    </div>
                    <form name="editgiftcardplan" method="POST" action="process.php" id="editgiftcardplan">
                        <input type="hidden" name="id" value="<?php echo $gfp['id']; ?>"/>
                        <table width="100%"   class="tableAdmin" id="giftcardplan">
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td><input type="text" class="" name="name" value="<?php echo $gfp['name']; ?>"/></td>
                                </tr>
                                <tr>
                                    <td>Minimum value</td>
                                    <td><input type="text" class="min_val" name="min_val" value="<?php echo $gfp['min_value']; ?>" /></td>
                                </tr>
                                <tr>
                                    <td>Maximum value</td>
                                    <td><input type="text" class="max_val" name="max_val" value="<?php echo $gfp['max_value']; ?>"/></td>
                                </tr>
                                <tr>
                                    <td>Increment</td>
                                    <td><input type="number" class="" name="increment" value="<?php echo $gfp['increment']; ?>"/></td>
                                </tr>
                                <tr>
                                    <td>Currency</td>
                                    <td>
                                        <?php if ($currencies->recordCount() > 0) {
                                                ?>
                                                <select class="" name="currency">
                                                    <?php
                                                    while ($row = $currencies->FetchRow()) {
                                                            ?>
                                                            <option value="<?php echo $row['currency_code']; ?>" <?php echo ($gfp['currency'] == $row['currency_code'] ? 'selected' : ''); ?>><?php echo $row['country_name'] . ' - ' . $row['currency_code']; ?></option>
                                                    <?php } ?>
                                                </select> 
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>

                                    <td colspan="2"><input type="submit" class="" name="updategiftcardplan" value="Submit" /></td>
                                </tr>
                            </tbody>

                        </table>
                    </form>
                </div>
            </div>
        </div>  
    </body>
</html>