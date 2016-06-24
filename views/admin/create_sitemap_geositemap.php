<?php
/******** 
@USE : create geositemap sitemap
@PARAMETER : 
@RETURN : 
@USED IN PAGES : admin process.php
*********/
//require_once("classes/Config.Inc.php");
////include_once(SERVER_PATH."/classes/DB.php");	

function generateSiteMap() 
{
	$objDB = new DB();
	
	$admin_setting_arr = array();
	$admin_setting_arr['id'] = 37;
	$sitemap_geositemap = $objDB->Show("admin_settings",$admin_setting_arr);
	
	if($sitemap_geositemap->fields['action']==0)
	{
		$array_values_sc = $where_clause_sc = array();
		$array_values_sc['action'] = 1;
		$where_clause_sc['id'] = 37;
		$objDB->Update($array_values_sc, "admin_settings", $where_clause_sc);
		
		// remove previous sitemap files for location
		
		$Sql = "SELECT * FROM sitemap where type='kml'";	 
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

		// remove previous sitemap files for location
		
		//The file to which the result is written to - must be writable. The file name is relative from root.
		$sitemap_file = 'locations1.kml'; 
		$sitemap_save_file = SERVER_PATH.'/sitemap/locations1.kml';
		
		$xml_file_count=1;
		
		$xml_string = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="https://www.opengis.net/kml/2.2" xmlns:gx="https://www.google.com/kml/ext/2.2" xmlns:kml="https://www.opengis.net/kml/2.2" xmlns:atom="https://www.w3.org/2005/Atom" xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://www.opengis.net/kml/2.2 https://schemas.opengis.net/kml/2.2.0/ogckml22.xsd https://www.google.com/kml/ext/2.2 https://developers.google.com/kml/schema/kml22gx.xsd https://www.w3.org/2005/Atom https://schemas.opengis.net/kml/2.2.0/atom-author-link.xsd">
	<Document>
		<atom:link href="'.WEB_PATH.'" rel="related" />
		<Style id="style_favorites">
			<IconStyle>
				<Icon>
					<href>'.ASSETS_IMG.'/c/pin-small.png</href>
				</Icon>
			</IconStyle>
		</Style>';
		
		$Sql = "SELECT distinct l.id,l.address,l.city,l.state,l.country,l.zip,l.latitude,l.longitude,l.phone_number,l.is_open,l.categories,l.location_permalink,mu.business,mu.aboutus_short,mu.merchant_icon FROM locations l,campaign_location cl,merchant_user mu where l.id=cl.location_id and cl.active=1 and mu.id=l.created_by";	 
		$RS = $objDB->Conn->Execute($Sql);
		$current_cnt=1;
		if($RS->RecordCount()>0)
		{
			while($Row = $RS->FetchRow())
			{
					
				$mod=($current_cnt%100);
				if($mod==0) // to start new file after 100 record
				{
					$phno = explode("-",$Row['phone_number']);
                    $newphno = "(".$phno[1].") ".$phno[2]."-".$phno[3];					 
	
					$xml_string .= '<Placemark id="loc_'.$Row['id'].'">
						<name><![CDATA['.$Row['business'].']]></name>
						<styleUrl>#style_favorites</styleUrl>
						<ExtendedData>';
						
					if($Row['categories']!="")
					{
						$loc_cat = array();
						$loc_cat['id'] = $Row['categories'];
						$loc_cat_data = $objDB->Show("category_level",$loc_cat);					
					
						$xml_string .= '<Data name="Location Category">
						<value><![CDATA['.$loc_cat_data->fields['cat_name'].']]></value>
						</Data>';
					}	
					$xml_string .= '<Data name="Address ">
						<value><![CDATA[<a href="'.$Row['location_permalink'].'">'.$Row['address'].','.$Row['city'].','.$Row['state'].','.$Row['country'].','.$Row['zip'].'</a>]]></value>
						</Data>
						<Data name="Phone Number">
						<value>'.$newphno.'</value>	
						</Data>
						</ExtendedData>
						<Point>
							<coordinates>'.$Row['latitude'].','.$Row['longitude'].',0</coordinates>
						</Point>		
					</Placemark>';
					
					$xml_string .= "</Document></kml>";

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
					$add_sitemap_file['type'] = 'kml';
					$add_sitemap_file['modified_date'] = date("Y-m-d");
					$objDB->Insert($add_sitemap_file, "sitemap");
					// add file name in table
					
					$xml_file_count++;
					$sitemap_file = 'locations'.$xml_file_count.'.kml'; 
					$sitemap_save_file = SERVER_PATH.'/sitemap/locations'.$xml_file_count.'.kml'; 
					
					$xml_string = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="https://www.opengis.net/kml/2.2" xmlns:gx="https://www.google.com/kml/ext/2.2" xmlns:kml="https://www.opengis.net/kml/2.2" xmlns:atom="https://www.w3.org/2005/Atom" xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://www.opengis.net/kml/2.2 https://schemas.opengis.net/kml/2.2.0/ogckml22.xsd https://www.google.com/kml/ext/2.2 https://developers.google.com/kml/schema/kml22gx.xsd https://www.w3.org/2005/Atom https://schemas.opengis.net/kml/2.2.0/atom-author-link.xsd">
	<Document>
		<atom:link href="'.WEB_PATH.'" rel="related" />
		<Style id="style_favorites">
			<IconStyle>
				<Icon>
					<href>https://maps.google.com/mapfiles/kml/pushpin/grn-pushpin.png</href>
				</Icon>
			</IconStyle>
		</Style>';

					//exit();
					
				}
				if($mod!=0) 
				{
					$phno = explode("-",$Row['phone_number']);
                    $newphno = "(".$phno[1].") ".$phno[2]."-".$phno[3];
					$xml_string .= '<Placemark id="loc_'.$Row['id'].'">
						<name><![CDATA['.$Row['business'].']]></name>
						<styleUrl>#style_favorites</styleUrl>
						<ExtendedData>';
					
					if($Row['categories']!="")
					{
						$loc_cat = array();
						$loc_cat['id'] = $Row['categories'];
						$loc_cat_data = $objDB->Show("category_level",$loc_cat);					
					
						$xml_string .= '<Data name="Location category">
						<value><![CDATA['.$loc_cat_data->fields['cat_name'].']]></value>
						</Data>';
					}	
					$xml_string .= '<Data name="Address ">
						<value><![CDATA[<a href="'.$Row['location_permalink'].'">'.$Row['address'].','.$Row['city'].','.$Row['state'].','.$Row['country'].','.$Row['zip'].'</a>]]></value>
						</Data>
						<Data name="Phone Number">
						<value>'.$newphno.'</value>
						</Data>	
						</ExtendedData>
						<Point>
							<coordinates>'.$Row['latitude'].','.$Row['longitude'].',0</coordinates>
						</Point>		
					</Placemark>';
				}
				if($RS->RecordCount()==$current_cnt) // to end file when total records completed
				{	
					$xml_string .= "</Document></kml>";

					if(!$hndl = fopen($sitemap_save_file,'w')) 
					{
						//header("Content-type:text/plain");
						print "Can't open sitemap file - '$sitemap_file'.\nDumping result to screen...\n<br /><br /><br />\n\n\n";
						print '<textarea rows="25" cols="70" style="width:100%">'.$xml_string.'</textarea>';
					} else {
						print '<p>Sitemap was written to <a href="' . $url.$sitemap_file .'">'. $url.$sitemap_file .'</a></p>';

						fputs($hndl,$xml_string);
						fclose($hndl);
						// add file name in table
						$add_sitemap_file = array();
						$add_sitemap_file['filename'] = $sitemap_file;
						$add_sitemap_file['type'] = 'kml';
						$add_sitemap_file['modified_date'] = date("Y-m-d");
						$objDB->Insert($add_sitemap_file, "sitemap");
						// add file name in table
					
					}
				}
				$current_cnt++;	
			}
		}
		
		$xml_string .= "</Document></kml>";
		
		$total_file_count = count($all_pages);
		$average = round(($total_priority/$total_file_count),2);
	}
	$array_values_sc = $where_clause_sc = array();
	$array_values_sc['action'] = 0;
	$where_clause_sc['id'] = 37;
	$objDB->Update($array_values_sc, "admin_settings", $where_clause_sc);
}
function generateSiteMap1() 
{
	//The file to which the result is written to - must be writable. The file name is relative from root.
	$sitemap_file = 'geositemap.xml'; 
	$sitemap_save_file = SERVER_PATH.'/sitemap/geositemap.xml';  
	
	$objDB = new DB();
	
	$admin_setting_arr = array();
	$admin_setting_arr['id'] = 37;
	$sitemap_geositemap = $objDB->Show("admin_settings",$admin_setting_arr);
	
	if($sitemap_geositemap->fields['action']==0)
	{
		$array_values_sc = $where_clause_sc = array();
		$array_values_sc['action'] = 1;
		$where_clause_sc['id'] = 37;
		$objDB->Update($array_values_sc, "admin_settings", $where_clause_sc);
		
		$Sql = "SELECT * FROM sitemap where type='kml'";
		$RS = $objDB->Conn->Execute($Sql);
		if($RS->RecordCount()>0)
		{
			$xml_string = '<?xml version="1.0" encoding="UTF-8"?>
							<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/kml/2.2 http://schemas.opengis.net/kml/2.2.0/ogckml22.xsd http://www.google.com/kml/ext/2.2 https://developers.google.com/kml/schema/kml22gx.xsd http://www.w3.org/2005/Atom http://schemas.opengis.net/kml/2.2.0/atom-author-link.xsd">
							<NetworkLink>';
							
			while($Row = $RS->FetchRow())
			{
				$xml_string .= '<Link>';
				$xml_string .= '<href>'.WEB_PATH.'/sitemap/'.$Row['filename'].'</href>';
				$xml_string .= '<refreshMode>onInterval</refreshMode>';
				$xml_string .= '<refreshInterval>3600</refreshInterval>';
				$xml_string .= '</Link>';
			}
			$xml_string .= '</NetworkLink>
						</kml>';

			if(!$hndl = fopen($sitemap_save_file,'w')) 
			{
				//header("Content-type:text/plain");
				print "Can't open sitemap file - '$sitemap_file'.\nDumping result to screen...\n<br /><br /><br />\n\n\n";
				print '<textarea rows="25" cols="70" style="width:100%">'.$xml_string.'</textarea>';
			} 
			else 
			{
				print '<p>Sitemap was written to <a href="' . $url.$sitemap_file .'">'. $url.$sitemap_file .'</a></p>';

				fputs($hndl,$xml_string);
				fclose($hndl);
			}
			
		}
	}

	$array_values_sc = $where_clause_sc = array();
	$array_values_sc['action'] = 0;
	$where_clause_sc['id'] = 37;
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
generateSiteMap1();
?>
</body>
</html>
