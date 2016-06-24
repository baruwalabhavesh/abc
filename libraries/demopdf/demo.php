<?php
//require_once("../../classes/Config.Inc.php");
//require_once("dompdf_config.inc.php");
require_once(LIBRARY."/demopdf/dompdf_config.inc.php");

//$htmlFile = WEB_PATH."/merchant/campaign-pdf.php?id=".$_REQUEST['id']."&mer_id=".$_SESSION['merchant_id'];

if(isset($_REQUEST['is_loyaltycard']))
{
	$htmlFile = WEB_PATH."/merchant/download_qrcode_pdf.php?id=".$_REQUEST['id']."&mer_id=".$_SESSION['merchant_id']."&is_loyaltycard=1";
}
else
{
	$htmlFile = WEB_PATH."/merchant/download_qrcode_pdf.php?id=".$_REQUEST['id']."&mer_id=".$_SESSION['merchant_id']."&is_walkin=".$_REQUEST['is_walkin'];
}

//echo $htmlFile;
//exit();

$html =file_get_contents($htmlFile);
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream($_REQUEST['id']."_".$_REQUEST['activationcode']."_".$_REQUEST['qrcodeid'].".pdf", array("Attachment" => true));
?>
