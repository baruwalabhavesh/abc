<?php
/**
 * @uses get bussiness page (facebook data)
 * @used in pages :mecrhant-setup.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');

require LIBRARY.'/fb-sdk/src/facebook.php';
//include_once(LIBRARY."/fb-sdk/src/facebook_secret.php");
$facebook = new Facebook(array(
  'appId'  => facebookappId,
  'secret' => facebooksecret,
));

$user = $facebook->getUser();


if ($user) 
{
	try 
	{
		$json_array=array();
		
		$user_profile = $facebook->api('/me');
		
		//echo "5";
		//$page_data = $facebook->api('/'.+$_REQUEST['page']);
		$page='/'.$_REQUEST['page'];
		
		try 
		{
			$page_data = $facebook->api($page);
		} 
		catch (FacebookApiException $e) 
		{
			$json_array['status'] = "false";
			$json_array['message'] = "Please enter valid facebook page address";
			$json = json_encode($json_array);
			echo $json;
			exit();
		}
	
		$json_array['status'] = "true";
		$json_array['data'] = $page_data;
		
		$json_array['first_name'] = $user_profile['first_name'];
		$json_array['last_name'] = $user_profile['last_name'];
		
		//$json_array['string'] = "$facebook->api('/'.+$_REQUEST['page'])";
		
		
		$page_profile_photo='/'.$_REQUEST['page'].'?fields=picture.width(144).height(144)';
		$page_profile_photo_data = $facebook->api($page_profile_photo);
		
		//$page_photos='/'.$_REQUEST['page'].'?fields=albums.fields(photos)';
		//$page_photos_data = $facebook->api($page_photos);
		
		//echo "page id=".$page_data['id']."<br/>";
		
		$fql = "SELECT src_big FROM photo WHERE pid IN (SELECT pid FROM photo_tag WHERE subject='".$page_data['id']."') OR pid IN (SELECT pid FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner='".$page_data['id']."' AND type!='profile'))";
		$page_photos_data = $facebook->api(array(
					 'method'       => 'fql.query',
					 //'access_token' => $access_token, //tried either with/without this
					 'query'        => $fql,
		));
		
		//print_r($page_photos_data);
		//exit();
		
		//echo "<pre>";
		
		//print_r($page_photos_data['albums']['data'][0]['photos']['data'][0]);
		
		//echo "</pre>";
		
		/*
		$photo_loops=$page_photos_data['albums']['data'][0]['photos']['data'];
		$photos_array=array();
		for($i=0;$i<count($photo_loops);$i++)
		{
			$photos_array[$i]=$photo_loops[$i]['images'][0]['source'];	
		}
		*/
		
		$photo_loops=$page_photos_data;
		$photos_array=array();
		for($i=0;$i<count($photo_loops);$i++)
		{
			$photos_array[$i]=$photo_loops[$i]['src_big'];	
		}
		
		$json_array['additional_images'] = $photos_array;
		
		
		$json_array['page_profile_photo'] = $page_profile_photo_data;
		
		$json_array_photo = json_decode($json_array);
		
		
			if($page_profile_photo_data['picture']['data']['url'])
			{
					$image_path_main=UPLOAD_IMG."/m/icon/";
					$name ="icon_".strtotime(date("Y-m-d H:i:s")).".png";
			 $json_array['icon_profile_image'] = $name;
			 
					 $street_main_image = file_get_contents($page_profile_photo_data['picture']['data']['url']); 
					 $fp  = fopen($image_path_main.$name, 'w+');
					 fputs($fp, $street_main_image);
			 

					

			}
		
		
		$json_array1 = json_decode($json_array);
		
		
			if($page_data['cover']['source'])
			{
					$image_path_main=UPLOAD_IMG."/m/location/";
					$name ="location_".strtotime(date("Y-m-d H:i:s")).".jpg";
			 $json_array['location_profile_image'] = $name;
			 
					 $street_main_image = file_get_contents($page_data['cover']['source']); 
					 $fp  = fopen($image_path_main.$name, 'w+');
					 fputs($fp, $street_main_image);
			 
					$sourefile_for_jpg = UPLOAD_IMG.'/m/location/'.$name;
					$image_folder=UPLOAD_IMG.'/m/location/mythumb/';
					
					$upload_filename = $image_folder . $name;
					passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);
					

			}
			$json = json_encode($json_array);
			echo $json;
	}
	catch (FacebookApiException $e) 
	{	
		//echo "6";
		error_log($e);
		$user = null;
	}
}
else
{
	try 
	{
		$json_array=array();
		
		//$user_profile = $facebook->api('/me');
		
		//echo "5";
		//$page_data = $facebook->api('/'.+$_REQUEST['page']);
		$page='/'.$_REQUEST['page'];
		
		try 
		{
			$page_data = $facebook->api($page);
		} 
		catch (FacebookApiException $e) 
		{
			$json_array['status'] = "false";
			$json_array['message'] = $merchant_msg["login_register"]["Msg_enter_valid_facebook_page_address"];
			$json = json_encode($json_array);
			echo $json;
			exit();
		}
	
		$json_array['status'] = "true";
		$json_array['data'] = $page_data;
		
		//$json_array['first_name'] = $user_profile['first_name'];
		//$json_array['last_name'] = $user_profile['last_name'];
		
		//$json_array['string'] = "$facebook->api('/'.+$_REQUEST['page'])";
		
		
		$page_profile_photo='/'.$_REQUEST['page'].'?fields=picture.width(144).height(144)';
		$page_profile_photo_data = $facebook->api($page_profile_photo);
		
		//$page_photos='/'.$_REQUEST['page'].'?fields=albums.fields(photos)';
		//$page_photos_data = $facebook->api($page_photos);
		
		//echo "page id=".$page_data['id']."<br/>";
		
		$fql = "SELECT src_big FROM photo WHERE pid IN (SELECT pid FROM photo_tag WHERE subject='".$page_data['id']."') OR pid IN (SELECT pid FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner='".$page_data['id']."' AND type!='profile'))";
		$page_photos_data = $facebook->api(array(
					 'method'       => 'fql.query',
					 //'access_token' => $access_token, //tried either with/without this
					 'query'        => $fql,
		));
		
		//print_r($page_photos_data);
		//exit();
		
		//echo "<pre>";
		
		//print_r($page_photos_data['albums']['data'][0]['photos']['data'][0]);
		
		//echo "</pre>";
		
		/*
		$photo_loops=$page_photos_data['albums']['data'][0]['photos']['data'];
		$photos_array=array();
		for($i=0;$i<count($photo_loops);$i++)
		{
			$photos_array[$i]=$photo_loops[$i]['images'][0]['source'];	
		}
		*/
		
		$photo_loops=$page_photos_data;
		$photos_array=array();
		for($i=0;$i<count($photo_loops);$i++)
		{
			$photos_array[$i]=$photo_loops[$i]['src_big'];	
		}
		
		$json_array['additional_images'] = $photos_array;
		
		
		$json_array['page_profile_photo'] = $page_profile_photo_data;
		
		$json_array_photo = json_decode($json_array);
		
		
			if($page_profile_photo_data['picture']['data']['url'])
			{
					$image_path_main=UPLOAD_IMG."/m/icon/";
					$name ="icon_".strtotime(date("Y-m-d H:i:s")).".png";
			 $json_array['icon_profile_image'] = $name;
			 
					 $street_main_image = file_get_contents($page_profile_photo_data['picture']['data']['url']); 
					 $fp  = fopen($image_path_main.$name, 'w+');
					 fputs($fp, $street_main_image);
			 

					

			}
		
		
		$json_array1 = json_decode($json_array);
		
		
			if($page_data['cover']['source'])
			{
					$image_path_main=UPLOAD_IMG."/m/location/";
					$name ="location_".strtotime(date("Y-m-d H:i:s")).".jpg";
			 $json_array['location_profile_image'] = $name;
			 
					 $street_main_image = file_get_contents($page_data['cover']['source']); 
					 $fp  = fopen($image_path_main.$name, 'w+');
					 fputs($fp, $street_main_image);
			 
					$sourefile_for_jpg = UPLOAD_IMG.'/m/location/'.$name;
					$image_folder=UPLOAD_IMG.'/m/location/mythumb/';
					
					$upload_filename = $image_folder . $name;
					passthru("/usr/bin/convert ".$sourefile_for_jpg." -resize 200x200! -filter Lanczos ".$upload_filename);
					

			}
			$json = json_encode($json_array);
			echo $json;
	}
	catch (FacebookApiException $e) 
	{	
		//echo "6";
		error_log($e);
		$user = null;
	}
}

//echo "7";
?>
