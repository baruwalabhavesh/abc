<?php
/**
 * @uses edit marketing material
 * @used in pages :add_more.php,apply_filter.php,locations.php
 * @author Sangeeta Raghavani
 */
if(isset($_REQUEST['doAction']))
{
	switch($_REQUEST['doAction'])
	{
            case "FileUpload":
		include(LIBRARY.'/simpleimage.php');
			$target = "campaign" ;
		
		//echo $_REQUEST["img_type"]."==".$target;
		//exit;
                $uploaddir = UPLOAD_IMG.'/m/'.$target.'/'; 
                
             //   $file = $uploaddir . basename($_FILES['uploadfile']['name']); 
                 $filename = str_replace(" ","_",strtolower(basename($_FILES['uploadfile']['name'])));
		 $ext = strtolower(substr($_FILES['uploadfile']['name'],strrpos($_FILES['uploadfile']['name'],".")+1));
		 $name = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".".$ext;
		 
		 $filename = $uploaddir . $name;
                
		if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) { 
                  //echo "success"."|".$name; 
                    list($width, $height, $type, $attr) = getimagesize(UPLOAD_IMG."/m/".$target."/".$name);
		     echo "success"."|".$name;

                } else {
                        echo "error";
                        exit;
                }
	    break;
            case "FileUploadMerchant":
		if($_REQUEST["img_type"] == "icon")
		{
			$target = "icon" ;
		}
		
                $uploaddir = UPLOAD_IMG.'/m/'.$target.'/'; 
                
             //   $file = $uploaddir . basename($_FILES['uploadfile']['name']); 
                 $filename = str_replace(" ","_",strtolower(basename($_FILES['uploadfile']['name'])));
                 
                                 
		 $ext = strtolower(substr($_FILES['uploadfile']['name'],strrpos($_FILES['uploadfile']['name'],".")+1));
		 $name = $_REQUEST['img_type']."_".strtotime(date("Y-m-d H:i:s")).".".$ext;
		 $filename = $uploaddir . $name;
                if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) 
                { 
                    list($width, $height, $type, $attr) = getimagesize(UPLOAD_IMG."/m/icon/".$name);
                    if($width!=100 && $height!=100)
                    {
                        echo "image size must be  100*100"."|"."small";
                    }
                    else
                    {
                        echo "success"."|".$name;
			
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
