<?php
/**
 * @uses generate qrcode pdf
 * @used in pages :demo_qrcode.php
 * @author Sangeeta Raghavani
 */

//ini_set("allow_url_fopen", true);
//require_once('html2pdf/html2pdf.class.php');

//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
$html_front = "";
$html_back = "";

//$objDB = new DB('read');
//$objJSON = new JSON();

//$RS = $objDB->Conn->Execute($Sql);
$array = array();

/*
//$objJSON = new JSON();
$JSON = $objJSON->get_compain_details($_REQUEST['id']);
$RS1 = json_decode($JSON);
//echo md5("123456");
$array = array();
$array['campaign_id'] = $_REQUEST['id'];
$RSCode = $objDB->Show("activation_codes",$array);
*/

$html_start='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><style>';
$html_start.='.main_wrapper{width: 571px;/*height:366px;*/text-align: justify;}';
$html_start.='body, p {margin: 0px;padding: 0px;font-family: Arial, Helvetica, sans-serif;}';
$html_start.='.front {margin-bottom: 20px;background-color: #ffffff;border: 1px solid #000;}';
$html_start.='</style></head>';
$html_start.='<body><div style="width:560px;padding:40px;margin:0px auto;">';

$array = array();
$array['id'] = $_REQUEST['id'];
/*$sql = "Select qrcode from qrcodes q, qrcode_campaign qc where q.id=qc.qrcode_id and qc.campaign_id=".$_REQUEST['id'];
$rs_qrcode = $objDB->Conn->Execute($sql);*/
$rs_qrcode = $objDB->Conn->Execute("Select qrcode from qrcodes q, merchant_loyalty_card mlc where q.id=mlc.qrcode_id and mlc.id=?",array($_REQUEST['id']));

//$url = WEB_PATH."/qr.php?qrcode=".$rs_qrcode->fields['qrcode'];//
$url = WEB_PATH."/qr.php?qrcode=".base64_encode($rs_qrcode->fields['qrcode']);
$qrcode_img_src= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d=".$url."&size=".$_REQUEST['size'];

// create jpg image //
                   $html_back.='   <table >
    <tr>
    <td style="width:120px;height:110px;padding:0 10px 0 0">
                          <img  src="'.$qrcode_img_src.'" />
                   </td></tr>
				   </table>';
  

$html_end='</div></body></html>';

$html=$html_start.$html_front.$html_back.$html_end;
    echo $html;

?>
