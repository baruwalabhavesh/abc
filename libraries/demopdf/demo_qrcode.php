<?php
//require_once("../../classes/Config.Inc.php");
require_once(LIBRARY."/demopdf/dompdf_config.inc.php");

if(isset($_REQUEST['is_location']))
{
    $htmlFile = WEB_PATH."/merchant/qrcodelocation-pdf.php?id=".$_REQUEST['id']."&size=".$_REQUEST['size'];

}
else if(isset($_REQUEST['is_loyaltycard']))
{

    $htmlFile = WEB_PATH."/merchant/qrcode-loyalty-pdf.php?id=".$_REQUEST['id']."&size=".$_REQUEST['size'];    
}
else
{
	$htmlFile = WEB_PATH."/merchant/qrcode-pdf.php?id=".$_REQUEST['id']."&size=".$_REQUEST['size'];    
}
$html =file_get_contents($htmlFile);
$dompdf = new DOMPDF();

$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream($_REQUEST['qrcodeid']."_".$_REQUEST['size']."X".$_REQUEST['size'].".pdf", array("Attachment" => true));
?>
