<?php

//require_once("../classes/Config.Inc.php");
if (isset($_REQUEST['doAction'])) {
        switch ($_REQUEST['doAction']) {
                case "FileUploadGiftCard":

                        $target = "giftcards";

                        //echo $_REQUEST["img_type"]."==".$target;
                        //exit;
                        $uploaddir = UPLOAD_IMG . '/a/giftcards/';
                        //echo $uploaddir;
                        //   $file = $uploaddir . basename($_FILES['uploadfile']['name']); 
                        $filename = str_replace(" ", "_", strtolower(basename($_FILES['uploadfile']['name'])));


                        $ext = strtolower(substr($_FILES['uploadfile']['name'], strrpos($_FILES['uploadfile']['name'], ".") + 1));
                        //$name = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".".$ext;
                        $name = $_REQUEST['img_type'] . "_" . strtotime(date("Y-m-d H:i:s")) . ".jpg";
                        $filename = $uploaddir . $name;
                        $jpgname = $name . ".jpg";
                        $sourefile_for_jpg = $uploaddir1 . $name;
                        $size = $_FILES['uploadfile']['size'];
                        $mb_size = $size / pow(1024, 2);
                        
                        if ($mb_size < 7) {
                                if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) {
                                        list($width, $height, $type, $attr) = getimagesize($uploaddir . $name);
                                        $org_width = $width;
                                        $org_height = $height;
                                        /*
                                          if($width!=100 && $height!=100)
                                          {
                                          echo "image size must be  100*100"."|"."small";
                                          }
                                         */
                                        if ($width < 600 && $height < 300) {
                                                echo "Image size must be at least 600px X 300px" . "|" . "small";
                                        } else {
                                                if ($width < $height) {
                                                        echo "Image width must be greater then height." . "|" . "small";
                                                } else {
                                                        $aspect_ratio = $width / $height;
                                                        if ($aspect_ratio < 1.25 || $aspect_ratio > 1.75) {
                                                                echo "Upload proper size image" . "|" . "small";
                                                        } else {
                                                                $aspect_ratio = $width / $height;
                                                                echo "success" . "|" . $name;

                                                                // For desktop
                                                                passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:siz=428x340 " . $filename . " \
			  -thumbnail 214x170 -unsharp 0x.5 " . $filename);
                                                                /* passthru("/usr/bin/convert -strip -interlace Plane -quality 95%  ".$filename." -resize 340x270! -filter Lanczos ".$filename);	 */
                                                                /*
                                                                  search deal block image
                                                                 */


                                                                /* $newHeight=$newWidth/$aspect_ratio;

                                                                  $image_folder=SERVER_PATH.'/admin/templates/images/giftcards/block/';
                                                                  $upload_filename = $image_folder . $name;

                                                                  //passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);


                                                                  passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=428x340 ".$filename." \
                                                                  -thumbnail 214x170  -unsharp 0x.5 ".$upload_filename); */
                                                        }
                                                }
                                        }
                                } else {
                                        echo "error";
                                        exit;
                                }
                        } else {
                                echo 'Image size must be less then 7 MB';
                        }
                        break;
        }
}
?>
