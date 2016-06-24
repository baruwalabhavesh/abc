<?php
/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

#!! IMPORTANT: 
#!! this file is just an example, it doesn't incorporate any security checks and 
#!! is not recommended to be used in production environment as it is. Be sure to 
#!! revise it and customize to your needs.

// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* 
// Support CORS
header("Access-Control-Allow-Origin: *");
// other CORS headers if any...
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	exit; // finish preflight CORS requests here
}
*/

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);


// Settings
//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
$targetDir = UPLOAD_IMG.'/m/campaign/'; 
$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds


// Create target dir
if (!file_exists($targetDir)) {
	@mkdir($targetDir);
}

// Get a file name
if (isset($_REQUEST["name"])) {
	$fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
	$fileName = $_FILES["file"]["name"];
} else {
	$fileName = uniqid("file_");
}

$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
//echo $filePath;
// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


// start new code

/*
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";
*/
$i_type="";
$target="";

if($_REQUEST['image_type']=="1")
{
	$i_type="campaign";
	$target = "campaign" ;
	$targetDir = UPLOAD_IMG.'/m/'.$target.'/';
}

//$filename = $fileName;
$filename = "media_".$i_type."_".rand(11,99).strtotime(date("Y-m-d H:i:s")).".jpg";
$name = $filename;
$sourefile_for_jpg = $targetDir.$filename;
$filePath = $targetDir . $filename;

//echo " filePath= ".$filePath." ";


// end new code

// Remove old temp files	
if ($cleanupTargetDir) {
	if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// If temp file is current file proceed to the next
		if ($tmpfilePath == "{$filePath}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			@unlink($tmpfilePath);
		}
	}
	closedir($dir);
}	


// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {	
	if (!$in = @fopen("php://input", "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
	// Strip the temp .part suffix off 
	rename("{$filePath}.part", $filePath);
}

// start new code

//chmod($filePath, 0777);

//echo "file_exists2 : ".file_exists(UPLOAD_IMG."/m/".$target."/".$filename)."<br/>";
list($width, $height, $type, $attr) = getimagesize(UPLOAD_IMG."/m/".$target."/".$filename);
$org_width = $width;
$org_height = $height;
/*
echo "<pre>";
echo $width.",".$height.",".$type.",".$attr;
echo "</pre>";
*/

			
	if($_REQUEST['image_type']=="1")
	{
		if($width<1600 && $height<1200)
		{
			unlink(UPLOAD_IMG.'/m/'.$target.'/'.$name);
			die('{"jsonrpc" : "2.0", "error" : {"code": 501, "message": "Failed to load image. Campaign listing looks best with photo atleast 1600px wide and 1200px high."}, "id" : "id"}');
		}
		else
		{	
			$aspect_ratio=$width/$height;
							
			//echo $_REQUEST["image_type"]."1";
			
			
			//search deal block image
											
			$newWidth=240;
			$newHeight=200;
			
			$newHeight=$newWidth/$aspect_ratio;
			
			$image_folder=UPLOAD_IMG.'/m/'.$target.'/block/';
			$upload_filename = $image_folder . $name;

			//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
			
			if($org_width>$org_height)
			{
				//echo "if";
				//exit();
				passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=500x400 ".$sourefile_for_jpg." \
		-thumbnail 240x240  -unsharp 0x.5 ".$upload_filename);
			}
			else
			{
				//echo "else";
				//exit();
				passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=400x500 ".$sourefile_for_jpg." -thumbnail 20000@ \
		-gravity center -background white -extent 240x200  ".$upload_filename);
			}
			//passthru("/usr/bin/mogrify -strip ".$upload_filename);
			
			//	ldpi 240 dpi image dpi*1.5=width height=width/1.33
								
							
			$newWidth=240;
			$newWidth=280;
			
			$newHeight=$newWidth/$aspect_ratio;
			
			$image_folder=UPLOAD_IMG.'/m/'.$target.'/ldpi/';
			$upload_filename = $image_folder . $name;

			passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
			//passthru("/usr/bin/mogrify -strip ".$upload_filename);
			
			// mdpi 320 dpi image dpi*1.5=width  height=width/1.33
			
			$newWidth=320;
			$newWidth=350;
			
			$newHeight=$newWidth/$aspect_ratio;
			
			$image_folder=UPLOAD_IMG.'/m/'.$target.'/mdpi/';
			$upload_filename = $image_folder . $name;

			passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
			//passthru("/usr/bin/mogrify -strip ".$upload_filename);

			//  hdpi 480 dpi image dpi*1.5=width height=width/1.33
			
			$newWidth=480;
			$newWidth=520;
			
			$newHeight=$newWidth/$aspect_ratio;
			
			$image_folder=UPLOAD_IMG.'/m/'.$target.'/hdpi/';
			$upload_filename = $image_folder . $name;

			passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
			//passthru("/usr/bin/mogrify -strip ".$upload_filename);
			
			// xhdpi 720 dpi image dpi*1.5=width height=width/1.33
			
			$newWidth=720;
			$newWidth=780;
			
			$newHeight=$newWidth/$aspect_ratio;
			
			$image_folder=UPLOAD_IMG.'/m/'.$target.'/xhdpi/';
			$upload_filename = $image_folder . $name;

			passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
			//passthru("/usr/bin/mogrify -strip ".$upload_filename);
			
			//	xxhdpi 1080 dpi image dpi*1.5=width height=width/1.33
			
			$newWidth=1080;
			$newWidth=1150;
			
			$newHeight=$newWidth/$aspect_ratio;
			
			$image_folder=UPLOAD_IMG.'/m/'.$target.'/xxhdpi/';
			$upload_filename = $image_folder . $name;

			passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
			//passthru("/usr/bin/mogrify -strip ".$upload_filename);
			
			// tablet
					
			$newWidth=800;
			
			$newHeight=$newWidth/$aspect_ratio;
			
			$image_folder=UPLOAD_IMG.'/m/'.$target.'/large-mdpi/';
			$upload_filename = $image_folder . $name;

			passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
			//passthru("/usr/bin/mogrify -strip ".$upload_filename);
			
			// start 400*250
			
			$image_folder=UPLOAD_IMG.'/m/'.$target.'/';
			$upload_filename = $image_folder . $name;
					
			if($org_width>$org_height)
			{
				//echo "if";
				//exit();
				passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=800x600 ".UPLOAD_IMG."/m/".$target."/".$filename." \
		-thumbnail 400x300  -unsharp 0x.5 ".$upload_filename);
			}
			else
			{
				passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=600x800 ".UPLOAD_IMG."/m/".$target."/".$filename." -thumbnail 20000@ \
		-gravity center -background white -extent 400x300  ".$upload_filename);
			}
			//passthru("/usr/bin/mogrify -strip ".$upload_filename);
			
			$objJSON->increase_image_count_for_merchant();
			             
			
			$array = $json_array = array();
			$array['merchant_id'] = $_SESSION['admin_id'];
			$array['created_date'] = date("Y-m-d H:i:s");
			$array['image'] = $filename;
			$array['media_type_id'] = $_REQUEST['image_type'];                
			$objDBWrt->Insert($array, "merchant_media");
		}
	}

// end new code

// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
