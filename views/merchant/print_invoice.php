<?php
/**
 * @uses payment invoice
 * @used in pages : payment-history.php
 * @author Sangeeta Raghavani
 */
check_merchant_session();
$RS = $objDB->Conn->Execute("select ppo.* ,mu.firstname,mu.lastname,mu.business,mu.address,mu.city,mu.state,mu.country,mu.zipcode,mu.phone_number,mu.stripe_customer_id from purchase_point_order ppo inner join merchant_user mu on  mu.id=ppo.merchant_id
	where  mu.id=? and ppo.id=?", array($_SESSION['merchant_id'], $_REQUEST['id']));
$Row = array();
require_once(LIBRARY . '/stripe-php/config.php');
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>ScanFlip | Payment Invoice</title>
        <?php //require_once(MRCH_LAYOUT . "/head.php");  ?>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <link href="<?= ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo ASSETS ?>/pricelist/css/jquery.dataTables.css">

        <script type="text/javascript" src="<?= ASSETS_JS ?>/bootstrap.min.js"></script>

    </head>
    <body>
        <div style="width:100%;text-align:center;">
            <script type="text/javascript" charset="utf-8">
                    $(document).ready(function () {

                    });
            </script>
            <!---start header---->
            <div>
                <?php
// include header file from merchant/template directory 
                require_once(MRCH_LAYOUT . "/header.php");
                ?>
                <!--end header--></div>
            <div id="contentContainer">
                <div id="content">
                    <table width="100%"  border="0" cellspacing="2" cellpadding="2">
                        <tr>
                            <td width="25%" align="left" valign="top" >
                                <?php
                                require_once(MRCH_LAYOUT . "/profile-left.php");
                                ?>
                            </td>
                            <td width="75%" align="left" valign="top">
                                <div class="title_header">Invoice</div>
                                <div class="datatable_container" >

                                    <?php
                                    if ($RS->RecordCount() > 0) {
                                            $Row = $RS->FetchRow();
                                            $charge = unserialize($Row['stripe_response']);

                                            //$charge = \Stripe\Charge::retrieve($Row['transaction_id']);
                                            ?>
                                            <div class="inv_dwn"><a href="print_invoice_download.php?id=<?php echo $Row['id']; ?>">Download Invoice</a></div></br>
                                            <div class="inv_main_blk">
                                                <div class="inv_block inv_line_ht">

                                                    <div class="inv_img_blk">
                                                        <img src=" <?= ASSETS_IMG ?>/m/logo-merchant.png" class="inv_logo">
                                                        <span>Invoice</span>
                                                    </div>

                                                    <div class="inv_blk_rht inv_line_ht">
                                                        <p>Customer: <?php echo $Row['stripe_customer_id']; ?></p>
                                                        <p>Invoice: <?php echo $Row['transaction_id']; ?></p>
                                                        <p>Order: <?php echo $Row['order_id']; ?></p>
                                                        <p>Invoice Date: <?php echo date('d M Y', strtotime($Row['date'])); ?></p>
                                                        <p>Invoice Status: Paid</p>
                                                    </div>
                                                </div>

                                                <div class="inv_block inv_line_ht">
                                                    <p>To,</p>
                                                    <p><?php echo $Row['business']; ?></p>
                                                    <p><?php echo $Row['address']; ?></p>
                                                    <p><?php echo $Row['city'] . ', ' . $Row['state'] . ', ' . $Row['zipcode']; ?></p>
                                                    <p><?php echo $Row['country']; ?></p>
                                                    <p>Ph# <?php 
                                                    $phno = explode("-", $Row['phone_number']);
                                                    $newphno = "(".$phno[1].") ".$phno[2]."-".$phno[3];
                                                    echo $newphno; ?></p>
                                                </div>

                                                <div class="inv_block">

                                                    <table style="width:100%" class="table table-hover" border='0' cellspacing='0'>
                                                        <tr style="background-color:#DEDEDE">
                                                            <th >Description</th>
                                                            <th >Price</th>
                                                            <th>Total</th>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $charge->description; ?></td>
                                                            <td ><?php echo strtoupper($charge->currency).' '.($charge->amount / 100);  ?></td>
                                                            <td><?php echo strtoupper($charge->currency).' '.($charge->amount / 100);  ?></td>
                                                        </tr>

                                                    </table>

                                                </div>

                                                <div class="inv_block">

                                                    <p><strong>Comment:</strong></p>
                                                    <p>Paid Via CC Ending ****<?php echo $charge->source->last4; ?> , Expiry: <?php echo $charge->source->exp_month . '/' . $charge->source->exp_year; ?></p>

                                                </div>

                                                <div class ="inv_footer">Thank You</div>
                                            </div>
                                    <?php }
                                    ?>

                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="clear">&nbsp;</div><br>

                    <!--end of content--></div>
                <!--end of contentContainer--></div>

            <!---------start footer--------------->
            <div>
                <?php
                require_once(MRCH_LAYOUT . "/footer.php");
                ?>
                <!--end of footer--></div>
        </div>

    </body>
</html>
<?php
$_SESSION['msg'] = "";
?>
