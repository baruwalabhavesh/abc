<?php

/**
 * @uses image upload in location , campaign
 * @used in pages :add-compaign.php,add-loaction.php,add-template.php,copy-compaign.php,customize-template.php,edit-compaign.php,edit-location.php,edit-template.php,merchant-setup.php,my-profile.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objJSON = new JSON();
if(isset($_REQUEST['doAction']))
{
	switch($_REQUEST['doAction'])
	{
            case "FileUpload":
		include(LIBRARY.'/simpleimage.php');
		if($_REQUEST["img_type"] == "template" || $_REQUEST["img_type"] == "campaign")
		{
			$target = "campaign" ;
		}
		else if($_REQUEST["img_type"] == "giftcard")
		{
			$target = "giftcard";
		}
		else{
			$target = "location";
		}
		//echo $_REQUEST["img_type"]."==".$target;
		//exit;
                $uploaddir = UPLOAD_IMG.'/m/'.$target.'/'; 
				$uploaddir1 = UPLOAD_IMG.'/m/'.$target.'/'; 
				
				$uploaddir2 = UPLOAD_IMG.'/m/'.$target.'/new/'; 

		$max_length_filename = 100;
		
		if(strlen($_FILES['uploadfile']['name'])>$max_length_filename)
		{
			echo "error";
            exit;
		}
					
                //echo $uploaddir;
             //   $file = $uploaddir . basename($_FILES['uploadfile']['name']); 
                 $filename = str_replace(" ","_",strtolower(basename($_FILES['uploadfile']['name'])));
		 $ext = strtolower(substr($_FILES['uploadfile']['name'],strrpos($_FILES['uploadfile']['name'],".")+1));
		 //$name = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".".$ext;
		 $name1 = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s"));
		 
		 //$name =$name1.".png";
		 // 21-03-2014 all campaign images also jpg
		 $name =$name1.".jpg";
		 // 21-03-2014 all campaign images also jpg
		 
		 $jpgname = $name1 .".jpg";
		 $jpgname_landscape = $name1."_land.jpg";
		 $filename = $uploaddir . $name;
		 $filename1 = $uploaddir2 . $name;
		 $sourefile_for_jpg=$uploaddir1.$name;
		 
		if($target=="location")
		{
			$filename = $uploaddir . $jpgname;
		}
		
		$pack_data_mu_old =  array();
		$pack_data_mu_old['merchant_id'] =  $_SESSION['merchant_id'];
		$get_billing_pack_data_mu_old = $objDB->Show("merchant_billing", $pack_data_mu_old);
		
		$pack_data_mu_old1 =  array();
		$pack_data_mu_old1['id'] = $get_billing_pack_data_mu_old->fields['pack_id'];
		$get_billing_pack_data_mu_old1 = $objDB->Show("billing_packages", $pack_data_mu_old1);
    	
		//$mer_image_count_total = $merchant_msg["mediamanagement"]["merchant_image_count"];
		$mer_image_count_total = $get_billing_pack_data_mu_old1->fields['image_upload_limit'];
		$mer_image_count_used = $_SESSION['merchant_info']['image_count'];
    
		if($mer_image_count_used < $mer_image_count_total)
		{
                if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) 
                { 
                  //echo "success"."|".$name;
		    
                    
					
					if($target=="campaign" || $target=="template") // if($target!="location")
					{
						list($width, $height, $type, $attr) = getimagesize(UPLOAD_IMG."/m/".$target."/".$name);
						$org_width = $width;
						$org_height = $height;
						
						if($width<1600 && $height<1200)
						{
							echo "image size must be atleast 1600px*1200px"."|"."small";
						}
						else
						{
							$objJSON->increase_image_count_for_merchant();	
							echo "success"."|".$name;
							
							$aspect_ratio=$width/$height;
							
							/*
							$image = new SimpleImage();
							$image->load($filename);								
							$aspect_ratio=$width/$height;
							$height=400/$aspect_ratio;
							$image->resize(400,$height);
							$image->save($filename);
							*/
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/';
							$upload_filename = $image_folder . $name;
							
							if($org_width>$org_height)
							{
								//echo "if";
								//exit();
								passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=800x600 ".$sourefile_for_jpg." \
			  -thumbnail 400x300  -unsharp 0x.5 ".$upload_filename);
							}
							else
							{
								//echo "else";
								//exit();
								passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=600x800 ".$sourefile_for_jpg." -thumbnail 20000@ \
			  -gravity center -background white -extent 400x300  ".$upload_filename);
							}
							
						
							
							
							/*
								search deal block image
							*/
							
							$newWidth=240;
							$newHeight=200;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/block/';
							$upload_filename = $image_folder . $name;
						
							//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							//echo $org_width."x".$org_height;
							//exit();
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
							
		  
							/*
								ldpi
								240 dpi image 
								dpi*1.5=width
								height=width/1.33
								360*270
								133(ppi pixel density) for  240 x 320        --(ldpi)
							*/
							
							$newWidth=240;
							$newWidth=280;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/ldpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// ldpi landscape
							/*
							$newWidth=320;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/ldpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							/*
								mdpi
								320 dpi image 
								dpi*1.5=width
								height=width/1.33
								480*360
								158(ppi pixel density) for  320 x 480       --(mdpi)
							*/
							
							$newWidth=320;
							$newWidth=350;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/mdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// mdpi landscape
							/*
							$newWidth=480;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/mdpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							/*
								hdpi
								480 dpi image 
								dpi*1.5=width
								height=width/1.33
								720*540
								233 (ppi pixel density)  for 480 x 800      --(hdpi)
							*/
							
							$newWidth=480;
							$newWidth=520;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/hdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// hdpi landscape
							/*
							$newWidth=800;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/hdpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							/*
								xhdpi
								720 dpi image 
								dpi*1.5=width
								height=width/1.33
								1080*810
								306 (ppi pixel density)   for 720 x 1280   --(xhdpi)
							*/
							
							$newWidth=720;
							$newWidth=780;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/xhdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// xhdpi landscape
							/*
							$newWidth=1280;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/xhdpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							/*
								xxhdpi
								1080 dpi image 
								dpi*1.5=width
								height=width/1.33
								1600*1200
								480 (ppi pixel density)   for  1080 x 1920   --(xxhdpi)
							*/
							
							$newWidth=1080;
							$newWidth=1150;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/xxhdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// xxhdpi landscape
							/*
							$newWidth=1920;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/xxhdpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							// tablet
							
							$newWidth=800;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/large-mdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
						}
					}
					else if($target=="giftcard" )
					{
						list($width, $height, $type, $attr) = getimagesize(UPLOAD_IMG."/m/".$target."/".$name);
						$org_width = $width;
						$org_height = $height;
						
						if($width<1600 && $height<1200)
						{
							echo "image size must be atleast 1600px*1200px"."|"."small";
						}
						else
						{
							$objJSON->increase_image_count_for_merchant();	
							echo "success"."|".$name;
							
							$aspect_ratio=$width/$height;
							
							/*
							$image = new SimpleImage();
							$image->load($filename);								
							$aspect_ratio=$width/$height;
							$height=400/$aspect_ratio;
							$image->resize(400,$height);
							$image->save($filename);
							*/
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/';
							$upload_filename = $image_folder . $name;
							
							if($org_width>$org_height)
							{
								//echo "if";
								//exit();
								passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=800x600 ".$sourefile_for_jpg." \
			  -thumbnail 400x300  -unsharp 0x.5 ".$upload_filename);
							}
							else
							{
								//echo "else";
								//exit();
								passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=600x800 ".$sourefile_for_jpg." -thumbnail 20000@ \
			  -gravity center -background white -extent 400x300  ".$upload_filename);
							}
							
						
							
							
							/*
								search deal block image
							*/
							
							$newWidth=240;
							$newHeight=200;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/block/';
							$upload_filename = $image_folder . $name;
						
							//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							//echo $org_width."x".$org_height;
							//exit();
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
							
		  
							/*
								ldpi
								240 dpi image 
								dpi*1.5=width
								height=width/1.33
								360*270
								133(ppi pixel density) for  240 x 320        --(ldpi)
							*/
							
							$newWidth=240;
							$newWidth=280;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/ldpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// ldpi landscape
							/*
							$newWidth=320;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/ldpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							/*
								mdpi
								320 dpi image 
								dpi*1.5=width
								height=width/1.33
								480*360
								158(ppi pixel density) for  320 x 480       --(mdpi)
							*/
							
							$newWidth=320;
							$newWidth=350;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/mdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// mdpi landscape
							/*
							$newWidth=480;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/mdpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							/*
								hdpi
								480 dpi image 
								dpi*1.5=width
								height=width/1.33
								720*540
								233 (ppi pixel density)  for 480 x 800      --(hdpi)
							*/
							
							$newWidth=480;
							$newWidth=520;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/hdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// hdpi landscape
							/*
							$newWidth=800;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/hdpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							/*
								xhdpi
								720 dpi image 
								dpi*1.5=width
								height=width/1.33
								1080*810
								306 (ppi pixel density)   for 720 x 1280   --(xhdpi)
							*/
							
							$newWidth=720;
							$newWidth=780;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/xhdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// xhdpi landscape
							/*
							$newWidth=1280;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/xhdpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							/*
								xxhdpi
								1080 dpi image 
								dpi*1.5=width
								height=width/1.33
								1600*1200
								480 (ppi pixel density)   for  1080 x 1920   --(xxhdpi)
							*/
							
							$newWidth=1080;
							$newWidth=1150;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/xxhdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
							// xxhdpi landscape
							/*
							$newWidth=1920;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=SERVER_PATH.'/merchant/images/'.$target.'/xxhdpi/';
							$upload_filename = $image_folder . $jpgname_landscape;
						
							passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							*/
							// landscape
							
							// tablet
							
							$newWidth=800;
							
							$newHeight=$newWidth/$aspect_ratio;
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/large-mdpi/';
							$upload_filename = $image_folder . $jpgname;
						
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
						}
					}
					else // location
					{
						// location image
						
						/*
						if($width<225)
						{
							echo "image width must be atleast 225px"."|"."small";
						}
						else
						{
							echo "success"."|".$name;
							$aspect_ratio=$width/$height;
							$height=225/$aspect_ratio;
							$image = new SimpleImage();
							$image->load($filename);
							$image->resize(225,$height);
							$image->save($filename);
						}
						*/
						list($width, $height, $type, $attr) = getimagesize(UPLOAD_IMG."/m/".$target."/".$jpgname);
						$min_width=200;
						$min_height=200;
						$max_width=400;
						$max_height=310;
								
						if($width>=400 && $height>=310)
						{
							/*
							echo "success"."|".$jpgname;
							$image = new SimpleImage();
							$image->load($filename);
							$image->resize(400,310);
							$image->save($filename);
							*/
							if($width>=$height)
							{
								  $aspect_ratio=$width/$height;
									$height=$max_width/$aspect_ratio;
								$objJSON->increase_image_count_for_merchant();		
								echo "success"."|".$jpgname;
								$image = new SimpleImage();
								$image->load($filename);
								$image->resize($max_width,$height);
								$image->save($filename);
								
								$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
						
							//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);
							
							//passthru("/usr/bin/convert -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \          -gravity center -background white -extent 200x200  ".$upload_filename);
								if($aspect_ratio>=1.75)
								{
									passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \          -gravity center -background white -extent 200x200  ".$upload_filename);	
								}
								else
								{
									passthru("/usr/bin/convert -strip -interlace Plane -quality 95%  ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
								}
			
							//echo "<br/>".$sourefile_for_jpg."<br/>";
							//echo $upload_filename;
							
							}
							else
							{
								$aspect_ratio=$width/$height;
								$width=$aspect_ratio*$max_height;
									$objJSON->increase_image_count_for_merchant();	
								 echo "success"."|".$jpgname;
								$image = new SimpleImage();
								$image->load($filename);
								$image->resize($width,$max_height);
								$image->save($filename);
								
								$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
						
							//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);
							
							//passthru("/usr/bin/convert -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \          -gravity center -background white -extent 200x200  ".$upload_filename);
							
							//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
								//echo $aspect_ratio;
								if($aspect_ratio<0.5)
								{
									passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \          -gravity center -background white -extent 200x200  ".$upload_filename);	
								}
								else
								{
									passthru("/usr/bin/convert -strip -interlace Plane -quality 95%  ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
								}
							//echo "<br/>".$sourefile_for_jpg."<br/>";
							//echo $upload_filename;
							}	
						}
						else if($width > 400)
						{
							/*
							if($height > $min_height)
							{
							   if($height<$max_height) 
							   {
								   
							   }
							   else
							   {
								   $height=310;
							   }
							   
								echo "success"."|".$jpgname;
								$image = new SimpleImage();
							   $image->load($filename);
							   $image->resize(400,$height);
							   $image->save($filename);
							}
							else
							{
								echo "image height must be atleast 200px"."|"."small";
							}
							*/
							if($height<$max_height) 
						   {
							   $aspect_ratio=$width/$height;
								$height=$max_width/$aspect_ratio;
								$objJSON->increase_image_count_for_merchant();
								 echo "success"."|".$jpgname;
								$image = new SimpleImage();
								$image->load($filename);
								$image->resize(400,$height);
								$image->save($filename);
								
								$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
						
							//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);
							
							//passthru("/usr/bin/convert -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \          -gravity center -background white -extent 200x200  ".$upload_filename);
							
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
							
							//echo "<br/>".$sourefile_for_jpg."<br/>";
							//echo $upload_filename;
						   }
						   else
						   {
							   $height=310;
							   $objJSON->increase_image_count_for_merchant();
							   echo "success"."|".$jpgname;
								$image = new SimpleImage();
							   $image->load($filename);
							   $image->resize(400,$height);
							   $image->save($filename);
							   
							   $image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
						
							//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);
							
							//passthru("/usr/bin/convert -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \           -gravity center -background white -extent 200x200  ".$upload_filename);
							
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
							
							//echo "<br/>".$sourefile_for_jpg."<br/>";
							//echo $upload_filename;
						   }		
						}
						else if($height > 310)
						{
							/*
							if($width > $min_width)
							{
								if($width < $max_width)
								{
									
								}
								else
								{
									 $width=400;
								}
								//$width=400;
								 
								 echo "success"."|".$jpgname;
								$image = new SimpleImage();
								$image->load($filename);
								$image->resize($width,310);
								$image->save($filename);
							}
							else
							{
							   echo "image width must be atleast 200px"."|"."small";
							}
							*/
							if($width < $max_width)
							{
								$aspect_ratio=$width/$height;
								$width=$aspect_ratio*$max_height;
							$objJSON->increase_image_count_for_merchant();
								 echo "success"."|".$jpgname;
								$image = new SimpleImage();
								$image->load($filename);
								$image->resize($width,310);
								$image->save($filename);
								
								$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
						
							//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);
							
							//passthru("/usr/bin/convert -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \          -gravity center -background white -extent 200x200  ".$upload_filename);
							
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
							
							//echo "<br/>".$sourefile_for_jpg."<br/>";
							//echo $upload_filename;
							}
							else
							{
								 $width=400;
								 $objJSON->increase_image_count_for_merchant();
								  echo "success"."|".$jpgname;
								$image = new SimpleImage();
								$image->load($filename);
								$image->resize($width,310);
								$image->save($filename);
								
								$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
						
							//passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);
							
							//passthru("/usr/bin/convert -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \          -gravity center -background white -extent 200x200  ".$upload_filename);
							
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
							
							//echo "<br/>".$sourefile_for_jpg."<br/>";
							//echo $upload_filename;
							}	
						} 
						else
						{
							echo "image size must be atleast 400px*200px"."|"."small";
						}
					}
                } 
                else 
                {
                        echo "error";
                        exit;
                }
        }
        else
        {
			echo "Sorry, you can't upload more than ". $get_billing_pack_data_mu_old1->fields['image_upload_limit'] ."  images."."|"."small";
		}   
	    break;
            case "FileUploadMerchant":
                include(LIBRARY.'/simpleimage.php');
		if($_REQUEST["img_type"] == "icon")
		{
			$target = "icon" ;
		}
		
		$max_length_filename = 100;
		
		if(strlen($_FILES['uploadfile']['name'])>$max_length_filename)
		{
			echo "error";
            exit;
		}
		
		//echo $_REQUEST["img_type"]."==".$target;
		//exit;
                $uploaddir = UPLOAD_IMG.'/m/'.$target.'/'; 
                //echo $uploaddir;
             //   $file = $uploaddir . basename($_FILES['uploadfile']['name']); 
                 $filename = str_replace(" ","_",strtolower(basename($_FILES['uploadfile']['name'])));
                 
                                 
		 $ext = strtolower(substr($_FILES['uploadfile']['name'],strrpos($_FILES['uploadfile']['name'],".")+1));
		 //$name = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".".$ext;
                 $name = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".jpg";
		 $filename = $uploaddir . $name;
                if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) 
                { 
                    list($width, $height, $type, $attr) = getimagesize(UPLOAD_IMG."/m/icon/".$name);
					/*
                    if($width!=100 && $height!=100)
                    {
                        echo "image size must be  100*100"."|"."small";
                    }
					*/
					if($width<100 && $height<100)
                    {
                        echo "image size must be at least 100px X 100px"."|"."small";
                    }
                    else
                    {	
						$aspect_ratio=$width/$height;
						$height=144/$aspect_ratio;
                        echo "success"."|".$name;
                        /*
						$image = new SimpleImage();
						$image->load($filename);
						$image->resize(144,$height);
						$image->save($filename);
						*/
						
					
						if($width>$height)
						{
							passthru("/usr/bin/convert -define jpeg:size=300x300 ".$filename." \
          -thumbnail 150x150  -unsharp 0x.5 ".$filename);
						}
						else
						{
							passthru("/usr/bin/convert -define jpeg:size=300x300 ".$filename." -thumbnail 20000@ \
          -gravity center -background white -extent 150x150  ".$filename);
						}
                    }
                } 
                else 
                {
                        echo "error";
                        exit;
                }
	    break;
        }
}
?>
