<?php
 $file = 'qrcode.pdf'; // file to be downloaded
 header("Expires: 0");
 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
 header("Cache-Control: no-store, no-cache, must-revalidate");
 header("Cache-Control: post-check=0, pre-check=0", false);
 header("Pragma: no-cache");  header("Content-type: application/pdf");
 header('Content-length: '.filesize($file));
 header('Content-disposition: attachment; filename='.basename($file));
 readfile($file);
 exit;
?>
