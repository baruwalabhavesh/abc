<?php
	//mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
	//mysql_select_db(DATABASE_NAME);
	
	//processing page for edit page
	//include connection file
	//include 'dbconnect.php';
	//remove spaces
	$pricelist_name=  trim(preg_replace('!\s+!', ' ',$_POST['name']));
	$TableName= "pricelists";
	$id= $_POST["id"];
	//
	$findId = 'SELECT * from '.$TableName.' WHERE id = '.$id.';';
	//$query= mysql_query($findId) or die($query."<br/><br/>".mysql_error());
	
	$query = $objDB->Conn->Execute($findId);

	//if(mysql_fetch_array($query) !== false){
	if($query->RecordCount() >0){	
			$file_url_query=  'SELECT json_url from '.$TableName.' WHERE id = '.$id.';';
			//$file_url = mysql_query($file_url_query);
			$file_url = $objDB->Conn->Execute($file_url_query);
			global $file_link;
			//while($Row = mysql_fetch_object($file_url)) {
			
				 //$GLOBALS['file_link'] =  $Row->json_url;
				$GLOBALS['file_link'] = $file_url->fields[0];
			//}
			echo $file_link;

		//$SQLstring = 'UPDATE '.$TableName.' set pricelist_name="'.$pricelist_name.'", last_update = CURRENT_TIMESTAMP ,json_url ="json/'.$pricelist_name.'_'.$_POST['id'].'.json" where id ='.$_POST['id'].';';
		//$QueryResult = mysql_query($SQLstring);
		//echo "123";
		
		$array_new = $where_clause1 = array();
		$array_new['pricelist_name'] = $pricelist_name;			
		$array_new['last_update'] = date("Y-m-d H:i:s");				
		$array_new['json_url'] = 'json/'.$pricelist_name.'_'.$_POST['id'].'.json';			
		$where_clause1['id'] = $_POST['id'];
		$QueryResult = $objDBWrt->Update($array_new , $TableName, $where_clause1);
		
		//if ($QueryResult) {
			
			$json= $_POST['json'];
			$pos=1;
			
			$json = substr_replace($json, '"pricelist_id":"'.$_POST['id'].'",', $pos, 1);
			//echo $file_link;
			if ($file_link ==  'json/'.$pricelist_name.'_'.$_POST['id'].'.json' ){
				//echo $file_link." == ".'json/'.$pricelist_name.'_'.$_POST['id'].'.json';
				//echo file_exists(ROOT.'/assets/pricelist/json/'.$pricelist_name.'_'.$_POST['id'].'.json');
				if(file_exists(ROOT.'/assets/pricelist/json/'.$pricelist_name.'_'.$_POST['id'].'.json')){
					//$json = addslashes($json);
					//file_put_contents(ROOT.'/assets/pricelist/json/'.$pricelist_name.'_'.$_POST['id'].'.json', $json);
					file_put_contents(ROOT.'/assets/pricelist/json/'.$_POST['id'].'.json', $json);
					
					// start memcache
					$memcached->set("pricelistjson_".$_POST['id'],$json,time() + CACHE_30_MIN);
					// end memcache
				}
				else{

				}
			}
			else{
				unlink(ROOT.'/assets/pricelist/'.$file_link);
				//$json = addslashes($json);
				//file_put_contents(ROOT.'/assets/pricelist/json/'.$pricelist_name.'_'.$_POST['id'].'.json', $json);
				file_put_contents(ROOT.'/assets/pricelist/json/'.$_POST['id'].'.json', $json);
				
				// start memcache
				$memcached->set("pricelistjson_".$_POST['id'],$json,time() + CACHE_30_MIN);
				// end memcache
			}
		//} else {
		//    echo "Error: <br>" . mysql_error();
		//}
	}
	else{
		echo('failed');

	}




?>
