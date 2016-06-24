<?php
//require_once("../../classes/Config.Inc.php");
//require_once("dompdf_config.inc.php");
require_once(LIBRARY."/demopdf/dompdf_config.inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB();

$filename = $_REQUEST['qrcodeid']."_".$_REQUEST['size']."X".$_REQUEST['size'].".jpg";
header('Content-Description: File Transfer');
header('Content-type: image/jpeg');
header('Content-Disposition: attachment; filename="'.$filename);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header("Content-Length: ".@filesize(ASSETS_IMG."/m/qrcode/jpeg/".$filename));
ob_clean();
flush();
// Send the contents of the file
@readfile(ASSETS_IMG."/m/qrcode/jpeg/".$filename);
?>
