<?php
//require_once("../../classes/Config.Inc.php");
require_once(LIBRARY."/demopdf/dompdf_config.inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
if(isset($_REQUEST['is_loyalty']))
 {
	 	$RS_qrcode = $objDB->Conn->Execute("Select qrcode from qrcodes q, merchant_loyalty_card mlc where q.id=mlc.qrcode_id and mlc.id=?",array($_REQUEST['campid']));
 }
 else
 {
	if($_REQUEST['campid'] !=  0)
	 {
		// $Sql  = "Select qrcode from qrcodes where id in(select qrcode_id from qrcode_campaign where campaign_id=".$_REQUEST['campid']." )";
		
		//$RS_qrcode = $objDB->Conn->Execute("Select qrcode from qrcodes where id in(select qrcode_id from qrcode_campaign where campaign_id=? )",array($_REQUEST['campid']));
		$RS_qrcode = $objDB->Conn->Execute("Select qrcode from qrcodes where id = (select qrcode_id from campaigns where id=? )",array($_REQUEST['campid']));
	 }
	 else
	 {
		  //$Sql  = "Select qrcode from qrcodes where id in(select qrcode_id from qrcode_location where location_id=".$_REQUEST['locid']." )";
		  
		  //$RS_qrcode = $objDB->Conn->Execute("Select qrcode from qrcodes where id in(select qrcode_id from qrcode_location where location_id=? )",array($_REQUEST['locid']));
		  $RS_qrcode = $objDB->Conn->Execute("Select qrcode from qrcodes where id = (select qrcode_id from locations where id=? )",array($_REQUEST['locid']));
	 }
}


  //$RS_qrcode = $objDB->execute_query($Sql);

 $url = WEB_PATH."/qr.php?qrcode=".base64_encode($RS_qrcode->fields['qrcode']);
 $json_array["qrcode"] = $url ;
  $json = json_encode($json_array);
  
//  $qrcode_img_src =  WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d=".$json_array["qrcode"]."&size=80";//."&size=80";
//   $filename = "qr-create-stephan-brumme-com.png";

//$qr = file_get_contents($qrcode_img_src);
// file_put_contents($filename, $qr);
    $htmlFile = WEB_PATH."/merchant/demopdf/demo_template.php?locid=".$_REQUEST['locid']."&campid=".$_REQUEST['campid']."&image=".$_REQUEST['image']."&text=".urlencode($_REQUEST['text'])."&size_id=".$_REQUEST['size_id']."&activationcode=".$_REQUEST['activationcode']."&qrcode=".$_REQUEST['qrcode']."&merchant_id=".$_SESSION['merchant_id']."&is_loyalty=1";

$html =file_get_contents($htmlFile);

$dompdf = new DOMPDF();

$dompdf->load_html($html);

$dompdf->set_paper('A4', 'landscape');
$dompdf->render();
$dompdf->stream($_REQUEST['activationcode']."_".$_REQUEST['qrcode'].".pdf", array("Attachment" => true));
?>
