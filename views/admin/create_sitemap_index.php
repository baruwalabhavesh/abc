<?php
/******** 
@USE : create index sitemap
@PARAMETER : 
@RETURN : 
@USED IN PAGES : admin process.php
*********/
//require_once("classes/Config.Inc.php");
////include_once(SERVER_PATH."/classes/DB.php");	

function generateSiteMap() 
{
	//The file to which the result is written to - must be writable. The file name is relative from root.
	$sitemap_file = 'sitemap.xml'; 
	
	$xml_string = '<?xml version="1.0" encoding="UTF-8"?>
   <sitemapindex xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';
	
	$objDB = new DB();
	
	$admin_setting_arr = array();
	$admin_setting_arr['id'] = 39;
	$sitemap_index = $objDB->Show("admin_settings",$admin_setting_arr);
	
	if($sitemap_index->fields['action']==0)
	{
		$array_values_sc = $where_clause_sc = array();
		$array_values_sc['action'] = 1;
		$where_clause_sc['id'] = 39;
		$objDB->Update($array_values_sc, "admin_settings", $where_clause_sc);
	
		$Sql = "SELECT * FROM sitemap";	 
		$RS = $objDB->Conn->Execute($Sql);
		if($RS->RecordCount()>0)
		{
			while($Row = $RS->FetchRow())
			{
				if($Row['type']!="kml")
				{
					$xml_string .= '<sitemap>';
					$xml_string .= '<loc>' . WEB_PATH.'/sitemap/'.$Row['filename'] . '</loc>';
					$xml_string .= '<lastmod>' . $Row['modified_date'] . '</lastmod>';
					$xml_string .= '</sitemap>';
				}
			}
		}
		
		$xml_string .= " </sitemapindex>";
		
		if(!$hndl = fopen($sitemap_file,'w')) {
			//header("Content-type:text/plain");
			print "Can't open sitemap file - '$sitemap_file'.\nDumping result to screen...\n<br /><br /><br />\n\n\n";
			print '<textarea rows="25" cols="70" style="width:100%">'.$xml_string.'</textarea>';
		} else {
			print '<p>Sitemap was written to <a href="' . $url.$sitemap_file .'">'. $url.$sitemap_file .'</a></p>';

			fputs($hndl,$xml_string);
			fclose($hndl);
		}
		
		$total_file_count = count($all_pages);
		$average = round(($total_priority/$total_file_count),2);
	}

	$array_values_sc = $where_clause_sc = array();
	$array_values_sc['action'] = 0;
	$where_clause_sc['id'] = 39;
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
