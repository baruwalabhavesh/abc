<?php
/**
 * @uses download marketing material for location
 * @used in pages :location_for_marketingmaterial.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");

/*
$url = WEB_PATH."/qr.php?qrcode=".base64_encode($_REQUEST['qrcodeid']);
$filename= $_REQUEST['qrcodeid']."_".$_REQUEST['size']."X".$_REQUEST['size'].".eps";
$fname= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=".$_REQUEST['size']."'";
//echo $fname;
//exit;
passthru("/usr/bin/convert ".$fname."  ".$filename);
//passthru("/usr/bin/convert ".$filename."  -resize ".$_REQUEST['size']."x".$_REQUEST['size']."! ".$filename);
*/

$filename = $_REQUEST['qrcodeid']."_".$_REQUEST['size']."X".$_REQUEST['size'].".eps";
function downloadFile($file, $type)
{
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=".$file);
        header("Content-Type: Content-type: $type");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".@filesize(ASSETS_IMG."/m/qrcode/eps/".$file));
        @readfile(ASSETS_IMG."/m/qrcode/eps/".$file);
}
downloadFile($filename, "image/eps");
?>
