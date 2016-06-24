<?php
/******** 
@USE : show barcode
@PARAMETER : 
@RETURN : 
@USED IN PAGES : mymerchants.php, my-deals.php, process_mobile.php, print_coupon.php, location_detail.php, search-deal.php,process.php, func_print_coupon.php
*********/
// Including all required classes
require_once(LIBRARY.'/barcode/BCGFontFile.php');
require_once(LIBRARY.'/barcode/BCGColor.php');
require_once(LIBRARY.'/barcode/BCGDrawing.php');

// Including the barcode technology
require_once(LIBRARY.'/barcode/BCGcode39.barcode.php');

// Loading Font
$font = new BCGFontFile(LIBRARY.'/barcode/font/Arial.ttf', 18);
$br = $_REQUEST['br'];
// The arguments are R, G, B for color.
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;
try {
	$code = new BCGcode39();
	$code->setScale(2); // Resolution
	$code->setThickness(30); // Thickness
	$code->setForegroundColor($color_black); // Color of bars
	$code->setBackgroundColor($color_white); // Color of spaces
	$code->setFont($font); // Font (or 0)
	$code->parse($br); // Text
} catch(Exception $exception) {
	$drawException = $exception;
}

/* Here is the list of the arguments 
1 - Filename (empty : display on screen)
2 - Background color */
$drawing = new BCGDrawing('', $color_white);
if($drawException) {
	$drawing->drawException($drawException);
} else {
	$drawing->setBarcode($code);
	$drawing->draw();
}

// Header that says it is an image (remove it if you save the barcode to a file)
header('Content-Type: image/png');

// Draw (or save) the image into PNG format.
$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
?>
<script>
	alert("asda");
	//window.location="http://www.google.com?";
</script>
