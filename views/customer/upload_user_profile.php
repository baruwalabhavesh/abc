<?php
/******** 
@USE : upload profile picture
@PARAMETER : 
@RETURN : 
@USED IN PAGES : my-profile.php
*********/
//require_once("classes/Config.Inc.php");
if(isset($_REQUEST['doAction']))
{
	switch($_REQUEST['doAction'])
	{
            case "FileUpload":
		//include('simpleimage.php');
			
			$target = "usr_pic" ;
			$target1 = "usr_pic/usr_pass_pic" ;
			$target2 = "usr_pic/usr_pass_pic/big" ;
		
		
             
                $uploaddir = UPLOAD_IMG.'/c/'.$target.'/'; 
                $uploaddir1 = UPLOAD_IMG.'/c/'.$target1.'/'; 
                $uploaddir2 = UPLOAD_IMG.'/c/'.$target2.'/'; 
		mkdir($uploaddir,0755);
                mkdir($uploaddir1,0755);
		mkdir($uploaddir2,0755);
                
             //   $file = $uploaddir . basename($_FILES['uploadfile']['name']); 
                 $filename = str_replace(" ","_",strtolower(basename($_FILES['uploadfile']['name'])));
		 $ext = strtolower(substr($_FILES['uploadfile']['name'],strrpos($_FILES['uploadfile']['name'],".")+1));
		 //$name = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".".$ext;
		 $name = "usr_".strtotime(date("Y-m-d H:i:s")).".png";
		 
		 $filename = $uploaddir . $name;
		 $filename1 = $uploaddir1 . $name;
		 $filename2 = $uploaddir2 . $name;
		    
                if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) { 
                
					echo "success"."|".$name;
				//	$image = new SimpleImage();
//$image->load($filename);
					//$image->resize(30,30);
					//$image->save($filename);
					$size = $client_msg['my_profile']['profile_pic_size'];
					//passthru("/usr/bin/convert ".$filename." -resize 50x50 ".$filename);
					//system("pwd"); 
					//echo "/usr/bin/convert -define jpeg:size=400x200 ".$filename." -thumbnail 10000@ \ -gravity center -background white -extent 160x180 ".$filename2;
					
					
					passthru("/usr/bin/convert -define jpeg:size=400x200 ".$filename." -thumbnail 10000@ \ -gravity center -background white -extent 160x180 ".$filename2);
					passthru("/usr/bin/convert -define jpeg:size=400x200 ".$filename." -thumbnail 10000@ \ -gravity center -background white -extent 80x90 ".$filename1);
					passthru("/usr/bin/convert -define jpeg:size=400x200 ".$filename." -thumbnail 10000@ \ -gravity center -background white -extent 50x50 ".$filename);
					
					/*
					passthru("/usr/local/bin/convert -define jpeg:size=400x200 ".$filename." -thumbnail 10000@ \ -gravity center -background white -extent 160x180 ".$filename2);
					passthru("/usr/local/bin/convert -define jpeg:size=400x200 ".$filename." -thumbnail 10000@ \ -gravity center -background white -extent 80x90 ".$filename1);
					passthru("/usr/local/bin/convert -define jpeg:size=400x200 ".$filename." -thumbnail 10000@ \ -gravity center -background white -extent 50x50 ".$filename);
                    */
                } else {
                        echo "error";
                        exit;
                }
	    break;
         
        }
}
?>
