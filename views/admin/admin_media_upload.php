<?php

include(LIBRARY."/easy_upload/upload_class.php");//classes is the map where the class file is stored
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
include(LIBRARY."/simpleimage.php");
//$objDB = new DB();
//$objJSON = new JSON();

if(isset($_REQUEST['px-submit']))
{
	if($_SESSION['admin_id'] == "")
	{
		header("Location:".WEB_PATH."/admin/login.php");
	}
	
$upload = new file_upload();
if($_REQUEST["img_type"] == "template" || $_REQUEST["img_type"] == "campaign")
{
	$target = "campaign" ;
}
else{
	$target = "location";
}
if($_REQUEST['img_type']=="1")
{
	$i_type="campaign";
	$target = "campaign" ;
}
if($_REQUEST['img_type']=="2")
{
	$i_type="store";
	$target = "location";
}
$upload->upload_dir = UPLOAD_IMG.'/m/'.$target.'/';
$upload->extensions = array('.jpg','.png','.jpeg','.gif'); // specify the allowed extensions here
$upload->rename_file = true;

if(!empty($_FILES)) {
	// 25 10 2013
	$i_type="";
	if($_REQUEST['img_type']=="campaign")
	{
		$i_type="campaign";
	}
	else if($_REQUEST['img_type']=="template")
	{
		$i_type="template";
	}
	else if($_REQUEST['img_type']=="store")
	{
		$i_type="store";
	}
	// 
	if($_REQUEST['img_type']=="1")
	{
		$i_type="campaign";
		$target = "campaign" ;
	}
	if($_REQUEST['img_type']=="2")
	{
		$i_type="store";
		$target = "location";
	}
	
	
	// 25 10 2013
	$upload->the_temp_file = $_FILES['userfile']['tmp_name'];
	$filename = str_replace(" ","_",strtolower(basename($_FILES['userfile']['name'])));
	 $ext = strtolower(substr($_FILES['userfile']['name'],strrpos($_FILES['userfile']['name'],".")+1));
	//$filename = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".".$ext;
	
   
	
	// 10 02 2013
	//$filename = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".png";
	
	$uploaddir = UPLOAD_IMG.'/m/'.$target.'/'; 
	$uploaddir1 = UPLOAD_IMG.'/m/'.$target.'/'; 
				
	$name1 = "media_".$_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s"));
	
	//$name =$name1.".png";
	// 21-03-2014 all campaign images also jpg
	$name = $name1.".jpg";
	// 21-03-2014 all campaign images also jpg
		 
	$jpgname = $name1 .".jpg";
	$jpgname_landscape = $name1."_land.jpg";
	$filename = $uploaddir . $name;
	$sourefile_for_jpg=$uploaddir1.$name;
		 
	// 10 02 2013
	
	// 25 10 2013
	$filename = "media_".$i_type."_".strtotime(date("Y-m-d H:i:s")).".jpg";
	// 25 10 2013
	
	if($i_type=="store")
	{
		$filename = "media_".$i_type."_".strtotime(date("Y-m-d H:i:s")).".jpg";
	}
	
	// 09 01 2015
	$name = $filename;
	$sourefile_for_jpg=$uploaddir1.$filename;
	// 09 01 2015
	
	$upload->the_file = $filename;
	
	$upload->http_error = $_FILES['userfile']['error'];
	$upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
   
        
        if ($upload->upload())
        {
            
            
            
            //list($width, $height, $type, $attr) = getimagesize("images/".$target."/".$filename);
			
			list($width, $height, $type, $attr) = getimagesize(UPLOAD_IMG."/m/".$target."/".$filename);
			$org_width = $width;
			$org_height = $height;
			
			if($_REQUEST['img_type']=="1")
			{
				
				if($width<1600 && $height<1200)
				{
					$_SESSION['msg']="Failed to load image. Campaign listing looks best with photo atleast 1600px wide and 1200px high.";
					//echo '<div id="status">failed</div>';
					echo '<div id="message">Failed to load image. Campaign listing looks best with photo atleast 1600px wide and 1200px high.</div>';
				}
				else
				{
					$aspect_ratio=$width/$height;
					
					//echo $_REQUEST["image_type"]."1";
					/*
					$image = new SimpleImage();
					$image->load(SERVER_PATH."/merchant/images/".$target."/".$filename);
					$aspect_ratio=$width/$height;
					$height=400/$aspect_ratio;
					$image->resize(400,$height);					
					$image->save(SERVER_PATH."/merchant/images/".$target."/".$filename);
					*/
					
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
						//echo "else";
						//exit();
						passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=600x800 ".UPLOAD_IMG."/m/".$target."/".$filename." -thumbnail 20000@ \
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
					$upload_filename = $image_folder . $name;
				
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
					$upload_filename = $image_folder . $name;
				
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
					$upload_filename = $image_folder . $name;
				
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
					$upload_filename = $image_folder . $name;
				
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
					$upload_filename = $image_folder . $name;
				
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
					$upload_filename = $image_folder . $name;
				
					passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize ".$newWidth."x".$newHeight."! -filter Lanczos ".$upload_filename);
							
					
						
					echo '<div id="status">success</div>';
					echo '<div id="message">'. $upload->the_file .' Successfully Uploaded.';//to ' .  $upload->upload_dir . $upload->the_file . '</div>';
					
			//return the upload file
					//echo '<div id="uploadedfile">'. $upload->the_file .'</div>';
					$array = $json_array = array();
					$array['merchant_id'] = $_SESSION['admin_id'];
					$array['created_date'] = date("Y-m-d H:i:s");
					$array['image'] = $filename;
					//$array['image_type'] = $_REQUEST['img_type'];
					$array['media_type_id'] = $_REQUEST['img_type'];                
					$objDB->Insert($array, "merchant_media");
					//include("process.php");
				}
			}
			else if($_REQUEST['img_type']=="2")
			{
				
				/*
				if($width<225)
				{
					$_SESSION['msg']="Failed to upload.Location listing looks best with photo atleast 225px wide.";
					//echo '<div id="status">failed</div>';
					echo '<div id="message">Failed to upload.Location listing looks best with photo atleast 225px wide</div>';
				}
				else
				{
					//echo $_REQUEST["image_type"]."1";
			
					$aspect_ratio=$width/$height;
					$height=225/$aspect_ratio;
					$image = new SimpleImage();
					$image->load(SERVER_PATH."/merchant/images/".$target."/".$filename);
					$image->resize(225,$height);
					$image->save(SERVER_PATH."/merchant/images/".$target."/".$filename);
							
					echo '<div id="status">success</div>';
					echo '<div id="message">'.$upload->the_file .' Successfully Uploaded.';//to ' .  $upload->upload_dir . $upload->the_file . '</div>';
					
			//return the upload file
					//echo '<div id="uploadedfile">'. $upload->the_file .'</div>';
					$array = $json_array = array();
					$array['merchant_id'] = $_SESSION['merchant_id'];
					$array['created_date'] = date("Y-m-d H:i:s");
					$array['image'] = $filename;
					$array['image_type'] = $_REQUEST['img_type'];                
					$objDB->Insert($array, "merchant_media");
					//include("process.php");
				}
				*/
				
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
								
						echo "success"."|".$jpgname;
						$image = new SimpleImage();
						$image->load(UPLOAD_IMG."/m/".$target."/".$filename);
						$image->resize($max_width,$height);
						$image->save(UPLOAD_IMG."/m/".$target."/".$filename);						
						
						$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
						$upload_filename = $image_folder . $name;
						
						if($aspect_ratio>=1.75)
						{
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \          -gravity center -background white -extent 200x200  ".$upload_filename);	
						}
						else
						{
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
						}
		  
					}
					else
					{
						$aspect_ratio=$width/$height;
						$width=$aspect_ratio*$max_height;			
								
						 echo "success"."|".$jpgname;
						$image = new SimpleImage();
						$image->load(UPLOAD_IMG."/m/".$target."/".$filename);
						$image->resize($width,$max_height);
						$image->save(UPLOAD_IMG."/m/".$target."/".$filename);
						
						$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
						$upload_filename = $image_folder . $name;
						
						if($aspect_ratio<0.5)
						{
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% -define jpeg:size=400x310 ".$sourefile_for_jpg." -thumbnail 20000@ \          -gravity center -background white -extent 200x200  ".$upload_filename);	
						}
						else
						{
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95%  ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
						}
							
					}

					echo '<div id="status">success</div>';
					echo '<div id="message">'.$upload->the_file .' Successfully Uploaded.';//to ' .  $upload->upload_dir . $upload->the_file . '</div>';
					
			//return the upload file
					//echo '<div id="uploadedfile">'. $upload->the_file .'</div>';
					$array = $json_array = array();
					$array['merchant_id'] = $_SESSION['admin_id'];
					$array['created_date'] = date("Y-m-d H:i:s");
					$array['image'] = $filename;
					//$array['image_type'] = $_REQUEST['img_type'];                
					$array['media_type_id'] = $_REQUEST['img_type'];
					$objDB->Insert($array, "merchant_media");
					//include("process.php");
				}
				else if($width > 400)
				{
						/*
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
					   */
					   
					   if($height<$max_height) 
						   {
							   $aspect_ratio=$width/$height;
								$height=$max_width/$aspect_ratio;
			
								 echo "success"."|".$jpgname;
								$image = new SimpleImage();
								$image->load(UPLOAD_IMG."/m/".$target."/".$filename);
								$image->resize(400,$height);
								$image->save(UPLOAD_IMG."/m/".$target."/".$filename);
								
								$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
														
						   }
						   else
						   {
							   $height=310;
							   
							   echo "success"."|".$jpgname;
								$image = new SimpleImage();
							   $image->load(UPLOAD_IMG."/m/".$target."/".$filename);
							   $image->resize(400,$height);
							   $image->save(UPLOAD_IMG."/m/".$target."/".$filename);
							   
							   $image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
							
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
							   							
						   }
						  
					   echo '<div id="status">success</div>';
						echo '<div id="message">'.$upload->the_file .' Successfully Uploaded.';//to ' .  $upload->upload_dir . $upload->the_file . '</div>';
						
				//return the upload file
						//echo '<div id="uploadedfile">'. $upload->the_file .'</div>';
						$array = $json_array = array();
						$array['merchant_id'] = $_SESSION['admin_id'];
						$array['created_date'] = date("Y-m-d H:i:s");
						$array['image'] = $filename;
						//$array['image_type'] = $_REQUEST['img_type'];                
						$array['media_type_id'] = $_REQUEST['img_type'];
						$objDB->Insert($array, "merchant_media");
						//include("process.php");
								
				}
				else if($height > 310)
				{
						/*
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
						*/
						
						if($width < $max_width)
						{
							$aspect_ratio=$width/$height;
							$width=$aspect_ratio*$max_height;
		
							 echo "success"."|".$jpgname;
							$image = new SimpleImage();
							$image->load(UPLOAD_IMG."/m/".$target."/".$filename);
							$image->resize($width,310);
							$image->save(UPLOAD_IMG."/m/".$target."/".$filename);
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
							
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
							
						}
						else
						{
							 $width=400;
							 
							  echo "success"."|".$jpgname;
							$image = new SimpleImage();
							$image->load(UPLOAD_IMG."/m/".$target."/".$filename);
							$image->resize($width,310);
							$image->save(UPLOAD_IMG."/m/".$target."/".$filename);
							
							$image_folder=UPLOAD_IMG.'/m/'.$target.'/mythumb/';
							$upload_filename = $image_folder . $name;
							
							passthru("/usr/bin/convert -strip -interlace Plane -quality 95% ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);	
							
						}
							
						
						echo '<div id="status">success</div>';
						echo '<div id="message">'.$upload->the_file .' Successfully Uploaded.';//to ' .  $upload->upload_dir . $upload->the_file . '</div>';
						
				//return the upload file
						//echo '<div id="uploadedfile">'. $upload->the_file .'</div>';
						$array = $json_array = array();
						$array['merchant_id'] = $_SESSION['admin_id'];
						$array['created_date'] = date("Y-m-d H:i:s");
						$array['image'] = $filename;
						//$array['image_type'] = $_REQUEST['img_type'];                
						$array['media_type_id'] = $_REQUEST['img_type'];
						$objDB->Insert($array, "merchant_media");
						//include("process.php");
						
				} 
				else
				{
					$_SESSION['msg']="image size must be at-least ( W X H) 400px x 200px";
					echo "<div id='message'>image size must be at-least ( W X H) 400px x 200px</div>";
				}
				
			}
			
        } else {

                echo '<div id="status">failed</div>';
                echo '<div id="message">'. $upload->show_error_string() .'</div>';

        }
	
           
}

}
?>
