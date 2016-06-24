<?php

/**
 * @uses payment invoice download
 * @used in pages : print_invoice.php
 * @author Sangeeta Raghavani
 */
require_once(LIBRARY . "/demopdf/dompdf_config.inc.php");
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
check_merchant_session();
$RS = $objDB->Conn->Execute("select ppo.* ,mu.firstname,mu.lastname,mu.business,mu.address,mu.city,mu.state,mu.country,mu.zipcode,mu.phone_number,mu.stripe_customer_id from purchase_point_order ppo inner join merchant_user mu on  mu.id=ppo.merchant_id
	where  mu.id=? and ppo.id=?", array($_SESSION['merchant_id'], $_REQUEST['id']));
$Row = array();
if ($RS->RecordCount() > 0) {
        require_once(LIBRARY . '/stripe-php/config.php');
        $Row = $RS->FetchRow();
        $charge = unserialize($Row['stripe_response']);


        $html_front = "";
        $html_back = "";

        $array = array();


        $html_start = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><style>';
        $html_start .='.main_wrapper{width: 571px;/*height:366px;*/text-align: justify;}';
        $html_start .='body, p {margin: 0px;padding: 0px;font-family: Arial, Helvetica, sans-serif;font-size:13px;}.tnk_u{text-align: center;background: #eaeaea;padding: 10px;font-weight: bold;color: #666161;}';
        $html_start .='.front {margin-bottom: 20px;background-color: #ffffff;border: 1px solid #000;}p{margin:2px 0;}.table tr td,.table tr th{padding: 5px;}.table tr th{background:#ccc;}.div_full{width:100%;clear:both;display:inline-block;}.div_mrg{margin:10px 0;}';
        $html_start .='</style></head>';
        $html_start.='<body><div style="padding:40px;margin:0px auto;">';
        
        $phno = explode("-", $Row['phone_number']);
        $newphno = "(".$phno[1].") ".$phno[2]."-".$phno[3];
        
// create jpg image //
        $html_back .='<div class="div_full div_mrg">

                                <div style="display: inline-block;width:50%;margin-right:20px;">
                                        <img src="' . ASSETS_IMG . '/m/logo-merchant.png" class="scanflip_merchant_logo" style="width: 200px;" alt="ScanFlip Merchant Logo">
                                        <span style="margin-left: -40px;">Invoice</span>
                                </div>

                                <div style="display: inline-block;width: 50%;">
                                        <p>Customer: ' . $Row['stripe_customer_id'] . '</p>
                                        <p>Invoice: ' . $Row['transaction_id'] . '</p>
                                        <p>Order: ' . $Row['order_id'] . '</p>
                                        <p>Invoice Date: ' . date('d M Y', strtotime($Row['date'])) . '</p>
                                        <p>Invoice Status: Paid</p>
                                </div>
                                </div>

                                <div class="div_mrg">
                                        <p>To,</p>
                                        <p>' . $Row['business'] . '</p>
                                        <p>' . $Row['address'] . '</p>
                                        <p>' . $Row['city'] . ', ' . $Row['state'] . ', ' . $Row['zipcode'] . '</p>
                                        <p>' . $Row['country'] . '</p>
                                        <p>Ph# ' . $newphno . '</p>
                                </div>

                        <div class="div_mrg">

                                <table class="table table-hover" border="0" cellspacing="0" style="width: 100%;text-align:left;font-size: 13px;">
                                        <tr>
                                                <th style="text-align:left;">Description</th>
                                                <th style="text-align:left;">Price</th>
                                                <th style="text-align:left;">Total</th>
                                        </tr>
                                        <tr>
                                                <td>' . $charge->description . '</td>
                                                <td >' . strtoupper($charge->currency) .' '.($charge->amount / 100) .'</td>
                                                <td >' . strtoupper($charge->currency) .' '.($charge->amount / 100) .'</td>
                                        </tr>
                                </table>
                        </div>

                        <div class="div_mrg">

                                <p><strong>Comment:</strong></p>
                                <p>Paid Via CC Ending ****' . $charge->source->last4 . ' , Expiry: ' . $charge->source->exp_month . '/' . $charge->source->exp_year . '</p>

                        </div>

                        <div class="div_mrg tnk_u">Thank You</div>';


        $html_end = '</div></body></html>';

        $html = $html_start . $html_front . $html_back . $html_end;
        echo $html;
        //exit;
        $html = ob_get_clean();
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        $dompdf->stream("Invoice-" . $Row['order_id'] . ".pdf", array("Attachment" => true));
}
?>
