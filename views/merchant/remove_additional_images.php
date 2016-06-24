<?php
/**
 * @uses to delete image in add edit loaction
 * @used in pages :add-location.php,edit-location.php,merchant-setup.php
 * @author Sangeeta Raghavani
 */
//require_once("../classes/Config.Inc.php");

//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
//$objDB = new DB('read');
//$objJSON = new JSON();

// $sql="DELETE from location_images where location_id='". $_REQUEST['locationid']."' and main_image='".$_REQUEST['imagename']."'";
// echo $sql; 
 //$objDB->execute_query($sql);
unlink(UPLOAD_IMG."/m/location/temp_main/".$_REQUEST['imagename']);
unlink(UPLOAD_IMG."/m/location/temp_thumb/".$_REQUEST['imagename']);

// decrease image count when delete image
		
	$objJSON->decrease_image_count_for_merchant();
	
	// decrease image count when delete image

  //echo "Delete Complete";      
?>
