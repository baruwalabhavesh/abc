<?php
/******** 
@USE : create campaign sitemap
@PARAMETER : 
@RETURN : 
@USED IN PAGES : admin process.php
*********/
//require_once("classes/Config.Inc.php");
////include_once(SERVER_PATH."/classes/DB.php");	
/******************************************************************************\
 * Author : Binny V Abraham                                                   *
 * Website: http://www.bin-co.com/                                            *
 * E-Mail : binnyva at gmail                                                  *
 * Get more PHP scripts from http://www.bin-co.com/php/                       *
 ******************************************************************************
 * Name    : PHP Google Search Sitemap Generator                              *
 * Version : 1.00.A                                                           *
 * Date    : Friday 17 November 2006                                          *
 * Page    : http://www.bin-co.com/php/programs/sitemap_generator/			  *
 *                                                                            *
 * You can use this script to create the sitemap for your site automatically. *
 * 		The script will recursively visit all files on your site and create a *
 * 		sitemap XML file in the format needed by Google.					  *
 *                                                                            *
 * Get more PHP scripts from http://www.bin-co.com/php/                       *
\******************************************************************************/
		
// Please edit these values before running your script.
//////////////////////////////////// Options ////////////////////////////////////
$url = WEB_PATH."/"; //The Url of the site - the last '/' is needed

$root_dir = WEB_PATH.'/'; //Where the root of the site is with relation to this file.

$file_mask = '*.php'; //Or *.html or whatever - Any pattern that can be used in the glob() php function can be used here.

//The file to which the result is written to - must be writable. The file name is relative from root.
$sitemap_file = 'sitemap_campaign1.xml'; 
$sitemap_save_file = SERVER_PATH.'/sitemap/sitemap_campaign1.xml'; 
	
// Stuff to be ignored...
//Ignore the file/folder if these words appear in the name
$always_ignore = array(
	'local_common.php','images'
);

//These files will not be linked in the sitemap.
$ignore_files = array(
	'404.php','error.php','configuration.php','include.inc'
);

//The script will not enter these folders
$ignore_folders = array(
	'classes','css','customer','demo_popup','fancyapps-fancyBox-18d1712','fb-sdk','font','fonts','google-api-php-client','images','img','include',
	'jcarousel','js','languages','locu_files','nbproject','QR-Generator-PHP-master','rateit','raty','session','starrating','templates','twitteroauth',
	'twitteroauth-master','ui','admin/phpqrcode','admin/templates','merchant/classes','merchant/config','merchant/demopdf','merchant/fonts','merchant/html2pdf','merchant/ImageMagick-6.8.6-3','merchant/newpopupdemo','merchant/pdf','merchant/script','merchant/templates','upload'
);

//The default priority for all pages - the priority of all pages will increase/decrease with respect to this.
$starting_priority = 70;

/////////////////////////// Stop editing now - Configurations are over ////////////////////////////


///////////////////////////////////////////////////////////////////////////////////////////////////
function generateSiteMap() {

	$objDB = new DB();
	
	$admin_setting_arr = array();
	$admin_setting_arr['id'] = 35;
	$sitemap_campaign = $objDB->Show("admin_settings",$admin_setting_arr);
	
	if($sitemap_campaign->fields['action']==0)
	{
		$array_values_sc = $where_clause_sc = array();
		$array_values_sc['action'] = 1;
		$where_clause_sc['id'] = 35;
        $objDB->Update($array_values_sc, "admin_settings", $where_clause_sc);
		
		// remove previous sitemap files for campaign
		
		$Sql = "SELECT * FROM sitemap where type='campaign'";	 
		$RS = $objDB->Conn->Execute($Sql);
		if($RS->RecordCount()>0)
		{
			while($Row = $RS->FetchRow())
			{
				unlink("sitemap/".$Row['filename']);
				
				$array_where = array();
				$array_where['filename'] = $Row['filename'];
				$objDB->Remove("sitemap", $array_where);
			}
		}

		// remove previous sitemap files for campaign

		$xml_file_count=1;
		global $url, $file_mask, $root_dir, $sitemap_file, $starting_priority,$sitemap_save_file;
		global $always_ignore, $ignore_files, $ignore_folders;
		global $total_file_count,$average, $lowest_priority_page, $lowest_priority;

		/////////////////////////////////////// Code ////////////////////////////////////
		chdir($root_dir);
		$all_pages = getFiles('');
		
		$xml_string = '<?xml version="1.0" encoding="UTF-8"?>
	<urlset
	  xmlns="https://www.google.com/schemas/sitemap/0.84"
	  xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance"
	  xsi:schemaLocation="https://www.google.com/schemas/sitemap/0.84
						  https://www.google.com/schemas/sitemap/0.84/sitemap.xsd">

	';
		//echo $xml_string;
		$modified_priority = array();
		for ($i=30;$i>0;$i--) array_push($modified_priority,$i);
		
		$lowest_priority = 100;
		$lowest_priority_page = "";
		//Process the files
		
		// start campaigns link
		
		$Sql = "SELECT cl.permalink,c.expiration_date FROM campaign_location cl,campaigns c where cl.active=1 and c.id=cl.campaign_id";	 
		$RS = $objDB->Conn->Execute($Sql);
		$current_cnt=1;
		if($RS->RecordCount()>0)
		{
			while($Row = $RS->FetchRow())
			{			
				$mod=($current_cnt%50000);
				if($mod==0) // to start new file after 50000 record
				{
					$xml_string .= '<url>';
					$xml_string .= '<loc>' . $Row['permalink'] . '</loc>';
					$xml_string .= '<expires>' . $Row['expiration_date'] . '</expires>';
					$xml_string .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
					$xml_string .= '<changefreq>daily</changefreq>';
					$xml_string .= '<priority>1.0</priority>';
					$xml_string .= '</url>';
					
					$xml_string .= "</urlset>";

					if(!$hndl = fopen($sitemap_save_file,'w')) 
					{
						//header("Content-type:text/plain");
						print "Can't open sitemap file - '$sitemap_file'.\nDumping result to screen...\n<br /><br /><br />\n\n\n";
						print '<textarea rows="25" cols="70" style="width:100%">'.$xml_string.'</textarea>';
					} else {
						print '<p>Sitemap was written to <a href="' . $url.$sitemap_file .'">'. $url.$sitemap_file .'</a></p>';

						fputs($hndl,$xml_string);
						fclose($hndl);
					}
					
					// add file name in table
					$add_sitemap_file = array();
					$add_sitemap_file['filename'] = $sitemap_file;
					$add_sitemap_file['type'] = 'campaign';
					$add_sitemap_file['modified_date'] = date("Y-m-d");
					$objDB->Insert($add_sitemap_file, "sitemap");
					// add file name in table
					
					$xml_file_count++;
					$sitemap_file = 'sitemap_campaign'.$xml_file_count.'.xml'; 
					$sitemap_save_file = SERVER_PATH.'/sitemap/sitemap_campaign'.$xml_file_count.'.xml'; 
					
					$xml_string = '<?xml version="1.0" encoding="UTF-8"?>
					<urlset
					  xmlns="https://www.google.com/schemas/sitemap/0.84"
					  xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance"
					  xsi:schemaLocation="https://www.google.com/schemas/sitemap/0.84
										  https://www.google.com/schemas/sitemap/0.84/sitemap.xsd">

					';

					//exit();
					
				}
				if($mod!=0) 
				{
					$xml_string .= '<url>';
					$xml_string .= '<loc>' . $Row['permalink'] . '</loc>';
					$xml_string .= '<expires>' . $Row['expiration_date'] . '</expires>';
					$xml_string .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
					$xml_string .= '<changefreq>daily</changefreq>';
					$xml_string .= '<priority>1.0</priority>';
					$xml_string .= '</url>';
				}
				if($RS->RecordCount()==$current_cnt) // to end file when total records completed
				{	
					$xml_string .= "</urlset>";

					if(!$hndl = fopen($sitemap_save_file,'w')) 
					{
						//header("Content-type:text/plain");
						print "Can't open sitemap file - '$sitemap_file'.\nDumping result to screen...\n<br /><br /><br />\n\n\n";
						print '<textarea rows="25" cols="70" style="width:100%">'.$xml_string.'</textarea>';
					} else {
						print '<p>Sitemap was written to <a href="' . $url.$sitemap_file .'">'. $url.$sitemap_file .'</a></p>';

						fputs($hndl,$xml_string);
						fclose($hndl);
					}
					// add file name in table
					$add_sitemap_file = array();
					$add_sitemap_file['filename'] = $sitemap_file;
					$add_sitemap_file['type'] = 'campaign';
					$add_sitemap_file['modified_date'] = date("Y-m-d");
					$objDB->Insert($add_sitemap_file, "sitemap");
					// add file name in table
				}
				$current_cnt++;			
			}
		}
		
		// end campaigns link	
		
		$xml_string .= "</urlset>";
		
		
		$total_file_count = count($all_pages);
		$average = round(($total_priority/$total_file_count),2);
	}

	$array_values_sc = $where_clause_sc = array();
	$array_values_sc['action'] = 0;
	$where_clause_sc['id'] = 35;
	$objDB->Update($array_values_sc, "admin_settings", $where_clause_sc);	
}

///////////////////////////////////////// Functions /////////////////////////////////
// File finding function.
function getFiles($cd) {
	$links = array();
	$directory = ($cd) ? $cd . '/' : '';//Add the slash only if we are in a valid folder

	$files = glob($directory . $GLOBALS['file_mask']);
	foreach($files as $link) {
		//Use this only if it is NOT on our ignore lists
		if(in_array($link,$GLOBALS['ignore_files'])) continue; 
		if(in_array(basename($link),$GLOBALS['always_ignore'])) continue;
		array_push($links, $link);
	}
	//asort($links);//Sort 'em - to get the index at top.

	//Get All folders.	
	$folders = glob($directory . '*',GLOB_ONLYDIR);//GLOB_ONLYDIR not avalilabe on windows.
	foreach($folders as $dir) {
		//Use this only if it is NOT on our ignore lists
		$name = basename($dir);
		if(in_array($name,$GLOBALS['always_ignore'])) continue;
		if(in_array($dir,$GLOBALS['ignore_folders'])) continue; 
		
		$more_pages = getFiles($dir); // :RECURSION: 
		if(count($more_pages)) $links = array_merge($links,$more_pages);//We need all thing in 1 single dimentional array.
	}
	
	return $links;
}

//////////////////////////////// Display /////////////////////////////


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.1 Transitional//EN">
<html>
<head>
<title>Sitemap Generation Using PHP</title>
<style type="text/css">
a {color:blue;text-decoration:none;}
a:hover {color:red;}
</style>
</head>
<body>
<?php
generateSiteMap();
?>
</body>
</html>
