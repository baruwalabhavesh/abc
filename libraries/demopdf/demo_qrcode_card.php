<?php
require_once("../../classes/Config.Inc.php");
require_once("dompdf_config.inc.php");
include_once(SERVER_PATH."/classes/DB.php");
include_once(SERVER_PATH."/classes/JSON.php");

$url = WEB_PATH."/qr.php?qrcode=".base64_encode($_REQUEST['card_id']);
//$url = WEB_PATH."/qr.php?qrcode=".$_REQUEST['card_id'];
$qrcode_img_src= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d=".$url."&size=".$_REQUEST['size'];
// create jpgg image // 
$dirname = $_SESSION['merchant_id']."_upload";    
$filename1 = (SERVER_PATH."/merchant/demopdf/".$dirname."/");
  
if (file_exists($filename1)) 
{        
} 
else 
{
    mkdir(SERVER_PATH."/merchant/demopdf/" . "$dirname", 0777);        
}
$image_folder = SERVER_PATH."/merchant/demopdf/".$dirname."/";  
$filename_eps_path = $image_folder."card-qrcode-eps.eps";
$filename= $image_folder."card-qrcode.jpg";
  //echo $image_folder."template-qrcode.jpg";
$qrcode_size =$_REQUEST['size'];
$fname= WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url."&size=".$qrcode_size."'";
passthru("/usr/bin/convert ".$fname."  ".$filename_eps_path );
passthru("/usr/bin/convert ".$filename_eps_path."  ".$filename );
// create jpg image //


$file = SERVER_PATH."/merchant/demopdf/".$dirname."/card-qrcode.jpg";
 $savefilepath = $_REQUEST['card_id']."_".$_REQUEST['size']."X".$_REQUEST['size'] ;
 $fileToSend = $file; // "template-qrcode.jpg";
 header('Content-Description: File Transfer');
 header('Content-type: image/jpeg');
header('Content-Disposition: attachment; filename="'.$savefilepath);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header("Content-Length: ". filesize($fileToSend));
 ob_clean();
flush();
// Send the contents of the file
readfile($fileToSend);
   
?>
