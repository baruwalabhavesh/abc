<?php

/**
 * @uses add more images to location
 * @used in pages : add-location.php,edit-location.php,merchant-setup.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objJSON = new JSON();

if (isset($_REQUEST['doAction'])) {
        switch ($_REQUEST['doAction']) {
                case "FileUpload":
                        include(LIBRARY . '/simpleimage.php');

                        $count_images = $_REQUEST['count_image'];
                        //echo $count_images;
                        // exit;

                        if ($_REQUEST["img_type"] == "template" || $_REQUEST["img_type"] == "campaign") {
                                $target = "campaign";
                        } else {
                                $target = "location";
                        }
                        //echo $_REQUEST["img_type"]."==".$target;
                        //exit;
                        $uploaddir = UPLOAD_IMG . '/m/' . $target . '/';
                        //echo $uploaddir;
                        //   $file = $uploaddir . basename($_FILES['uploadfile']['name']); 
                        $filename = str_replace(" ", "_", strtolower(basename($_FILES['uploadfile']['name'])));
                        $ext = strtolower(substr($_FILES['uploadfile']['name'], strrpos($_FILES['uploadfile']['name'], ".") + 1));
                        //$name = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".".$ext;
                        //$name = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".png";
                        $name = $_REQUEST['img_type'] . "_" . strtotime(date("Y-m-d H:i:s")) . ".jpg";
                        $name1 = $_REQUEST['img_type'] . "123_" . strtotime(date("Y-m-d H:i:s")) . ".jpg";
                        $filename = $uploaddir . $name;
                        $filename_thumb = $uploaddir . "thumb/" . $name;

                        $temp_filename = $uploaddir . "temp_main/" . $name;
                        $temp_filename_thumb = $uploaddir . "temp_thumb/" . $name;

                        $mer_image_count_total = $merchant_msg["mediamanagement"]["merchant_image_count"];
                        $mer_image_count_used = $_SESSION['merchant_info']['image_count'];

                        if ($mer_image_count_used < $mer_image_count_total) {
                                if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $temp_filename)) {

                                        list($width, $height, $type, $attr) = getimagesize(UPLOAD_IMG . "/m/" . $target . "/temp_main/" . $name);
                                        //echo $width."X". $height;
                                        //exit;
                                        $min_width = 200;
                                        $min_height = 200;
                                        $max_width = 400;
                                        $max_height = 310;

                                        if ($width >= 400 && $height >= 310) {
                                                if ($width > $height) {
                                                        $aspect_ratio = $width / $height;
                                                        $height = $max_width / $aspect_ratio;

                                                        echo "success" . "|" . $name;
                                                        $image = new SimpleImage();
                                                        $image->load($temp_filename);
                                                        $image->resize($max_width, $height);
                                                        $image->save($temp_filename);
                                                } else {
                                                        $aspect_ratio = $width / $height;
                                                        $width = $aspect_ratio * $max_height;

                                                        echo "success" . "|" . $name;
                                                        $image = new SimpleImage();
                                                        $image->load($temp_filename);
                                                        $image->resize($width, $max_height);
                                                        $image->save($temp_filename);
                                                }
                                                $objJSON->increase_image_count_for_merchant();

                                                /*
                                                  $image_thumb = new SimpleImage();
                                                  $image_thumb->load($temp_filename);
                                                  $image_thumb->resize(70,70);
                                                  $image_thumb->save($temp_filename_thumb);
                                                 */

                                                $image_folder = UPLOAD_IMG . '/m/' . $target . '/temp_main/';
                                                $upload_filename = $image_folder . $name;

                                                $image_folder1 = UPLOAD_IMG . '/m/' . $target . '/mythumb/';
                                                $upload_filename1 = $image_folder1 . $name1;


                                                //passthru("/usr/bin/convert  logo: \ -resize 140x -resize 'x140<'   -resize 50% \ -gravity center  -crop 70x70+0+0 +repage ".$upload_filename);
                                                //passthru("/usr/bin/convert ".$upload_filename." -liquid-rescale 75x100%\! ".$upload_filename1);
                                                //passthru("/usr/bin/convert ".$upload_filename." -resize 70x70 ".$upload_filename1);
                                                //passthru("/usr/bin/convert ".$upload_filename." -resize 70x70 ".$temp_filename_thumb);	
                                                //passthru("/usr/bin/convert -define jpeg:size=400x200 ".$upload_filename."  -auto-orient \ -thumbnail 100x100   -unsharp 0x.5 ".$temp_filename_thumb);	

                                                passthru("/usr/bin/convert -define jpeg:size=400x200 " . $upload_filename . " -thumbnail 10000@ \
          -gravity center -background white -extent 70x70 " . $temp_filename_thumb);
                                        } else if ($width > 400) {

                                                if ($height < $max_height) {
                                                        $aspect_ratio = $width / $height;
                                                        $height = $max_width / $aspect_ratio;

                                                        echo "success" . "|" . $name;
                                                        $image = new SimpleImage();
                                                        $image->load($temp_filename);
                                                        $image->resize(400, $height);
                                                        $image->save($temp_filename);
                                                } else {
                                                        $height = 310;

                                                        echo "success" . "|" . $name;
                                                        $image = new SimpleImage();
                                                        $image->load($temp_filename);
                                                        $image->resize(400, $height);
                                                        $image->save($temp_filename);
                                                }
                                                $objJSON->increase_image_count_for_merchant();

                                                /* 	
                                                  $image_thumb = new SimpleImage();
                                                  $image_thumb->load($temp_filename);
                                                  $image_thumb->resize(70,70);
                                                  $image_thumb->save($temp_filename_thumb);
                                                 */
                                                $image_folder = UPLOAD_IMG . '/m/' . $target . '/temp_main/';
                                                $upload_filename = $image_folder . $name;

                                                $image_folder1 = UPLOAD_IMG . '/m/' . $target . '/mythumb/';
                                                $upload_filename1 = $image_folder1 . $name1;


                                                //passthru("/usr/bin/convert  logo: \ -resize 140x -resize 'x140<'   -resize 50% \ -gravity center  -crop 70x70+0+0 +repage ".$upload_filename);
                                                //passthru("/usr/bin/convert ".$upload_filename." -liquid-rescale 75x100%\! ".$upload_filename1);
                                                //passthru("/usr/bin/convert ".$upload_filename." -resize 70x70 ".$upload_filename1);
                                                //passthru("/usr/bin/convert ".$upload_filename." -resize 70x70 ".$temp_filename_thumb);		

                                                passthru("/usr/bin/convert -define jpeg:size=400x200 " . $upload_filename . " -thumbnail 10000@ \
          -gravity center -background white -extent 70x70 " . $temp_filename_thumb);
                                        } else if ($height > 310) {

                                                if ($width < $max_width) {
                                                        $aspect_ratio = $width / $height;
                                                        $width = $aspect_ratio * $max_height;

                                                        echo "success" . "|" . $name;
                                                        $image = new SimpleImage();
                                                        $image->load($temp_filename);
                                                        $image->resize($width, 310);
                                                        $image->save($temp_filename);
                                                } else {
                                                        $width = 400;

                                                        echo "success" . "|" . $name;
                                                        $image = new SimpleImage();
                                                        $image->load($temp_filename);
                                                        $image->resize($width, 310);
                                                        $image->save($temp_filename);
                                                }
                                                $objJSON->increase_image_count_for_merchant();
                                                //$width=400;

                                                /*
                                                  $image_thumb = new SimpleImage();
                                                  $image_thumb->load($temp_filename);
                                                  $image_thumb->resize(70,70);
                                                  $image_thumb->save($temp_filename_thumb);
                                                 */
                                                $image_folder = UPLOAD_IMG . '/m/' . $target . '/temp_main/';
                                                $upload_filename = $image_folder . $name;

                                                $image_folder1 = UPLOAD_IMG . '/m/' . $target . '/mythumb/';
                                                $upload_filename1 = $image_folder1 . $name1;


                                                //passthru("/usr/bin/convert  logo: \ -resize 140x -resize 'x140<'   -resize 50% \ -gravity center  -crop 70x70+0+0 +repage ".$upload_filename);
                                                //passthru("/usr/bin/convert ".$upload_filename." -liquid-rescale 75x100%\! ".$upload_filename1);
                                                //passthru("/usr/bin/convert ".$upload_filename." -resize 70x70 ".$upload_filename1);
                                                //passthru("/usr/bin/convert ".$upload_filename." -resize 70x70 ".$temp_filename_thumb);

                                                passthru("/usr/bin/convert -define jpeg:size=400x200 " . $upload_filename . " -thumbnail 10000@ \
          -gravity center -background white -extent 70x70 " . $temp_filename_thumb);
                                        } else {
                                                echo "image size must be atleast 400px*200px" . "|" . "small";
                                        }
                                } else {
                                        echo $merchant_msg["addlocation"]["Msg_Not_Upload_Image"] . "|" . "small";

                                        exit;
                                }
                        } else {
                                echo $merchant_msg["mediamanagement"]["Msg_image_count_exceede"] . "|" . "small";
                        }

                        break;

                case "MediaFileUpload":
                        include(LIBRARY.'/simpleimage.php');
                        $name_media_arr = explode("_", $_REQUEST['imagename']);
                        $name_media = $name_media_arr[0] . "_" . strtotime(date("Y-m-d H:i:s")) . ".png";

                        copy(UPLOAD_IMG . '/m/' . $_REQUEST['imagename'], UPLOAD_IMG . '/m/location/temp_thumb/' . $name_media);
                        copy(UPLOAD_IMG . '/m/location/' . $_REQUEST['imagename'], UPLOAD_IMG . '/m/location/temp_main/' . $name_media);
                        // sleep(5);
                        $image_thumb = new SimpleImage();
                        $image_thumb->load(UPLOAD_IMG . '/m/location/' . $_REQUEST['imagename']);
                        $image_thumb->resize(70, 70);
                        $image_thumb->save(UPLOAD_IMG . '/m/location/temp_thumb/' . $name_media);
                        echo "success" . "|" . $name_media;

                        break;
        }
}
?>
