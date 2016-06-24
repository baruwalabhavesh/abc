<?php
	//mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
	//mysql_select_db(DATABASE_NAME);
	//include 'dbconnect.php';
	$pricelist_name=  trim(preg_replace('!\s+!', ' ',$_POST['name']));
	$TableName= "pricelists";
	$id=$_POST['id'];
	


	if($id == -1){
		$pricelist_type = $_POST['pricelist_type'];
		if($pricelist_type == "product"){
			$pricelist_type_id = 1;

		}
		else if($pricelist_type == "service"){
			$pricelist_type_id = 2;
		}
		
		//$maxOrderQuery = mysql_query('SELECT MAX(display_order) from '.$TableName);
		//$maxOrder = mysql_fetch_array($maxOrderQuery);
		
		$SQLstring = 'SELECT MAX(display_order) from '.$TableName;
		//$QueryResult = mysql_query($SQLstring);
		$QueryResult = $objDB->Conn->Execute($SQLstring);
		$maxOrder = $QueryResult->fields[0];
						
		//$SQLstring = 'INSERT INTO '. $TableName .' (pricelist_name,merchant_id,display_order,last_update, pricelist_type_id) VALUES("'.$pricelist_name.'",'.$_SESSION['merchant_id'].',"'.($maxOrder + 1).'",CURRENT_TIMESTAMP,'.$pricelist_type_id.');';
		//$QueryResult = mysql_query($SQLstring);
		
		$array = array();
		$array['pricelist_name'] = $pricelist_name;
		$array['merchant_id'] = $_SESSION['merchant_id'];
		$array['display_order'] = ($maxOrder + 1);
		$array['last_update'] = date("Y-m-d H:i:s");
		$array['pricelist_type_id'] = $pricelist_type_id;
		//$array['user_type'] = 2;        
        $QueryResult_insert_id = $objDBWrt->Insert($array, $TableName);
        
		if ($QueryResult) {
			$json= $_POST['json'];
			$pos=1;
			//$json = substr_replace($json, '"pricelist_id":"'.mysql_insert_id().'",', $pos, 1);
			$json = substr_replace($json, '"pricelist_id":"'.$QueryResult_insert_id.'",', $pos, 1);
			$id = $QueryResult_insert_id;
			echo $QueryResult_insert_id;
		} else {
		    echo "Error: " . $QueryResult . "<br>" . mysql_error();
		}

		//replace pricelist name with merchant id in json file name
		//$SQLstring = 'UPDATE pricelists set json_url ="json/'.$pricelist_name.'_'.mysql_insert_id().'.json" where id ='.mysql_insert_id().';';
		//mysql_query($SQLstring);
		
		$array_new = $where_clause1 = array();
		$array_new['json_url'] = 'json/'.$pricelist_name.'_'.$QueryResult_insert_id.'.json';		
		$where_clause1['id'] = $QueryResult_insert_id;
		$objDBWrt->Update($array_new , $TableName, $where_clause1);
			
		//$filename = 'json/'.$pricelist_name.'_'.$id.'.json';
		$filename = realpath("json").'/'.$pricelist_name.'_'.$id.'.json';
		//file_put_contents(ROOT.'/assets/pricelist/json/'.$pricelist_name.'_'.$_POST['id'].'.json', $json);		
		//echo ROOT.'/assets/pricelist/json/'.$pricelist_name.'_'.$id.'.json';
		
		//$json = addslashes($json);
		//file_put_contents(ROOT.'/assets/pricelist/json/'.$pricelist_name.'_'.$id.'.json', $json);
		file_put_contents(ROOT.'/assets/pricelist/json/'.$id.'.json', $json);
		
		// start memcache
		$memcached->set("pricelistjson_".$id,$json,time() + CACHE_30_MIN);
		// end memcache
	
	}else{
		$SQLstring = 'UPDATE pricelists set pricelist_name="'.$pricelist_name.'", last_update = CURRENT_TIMESTAMP ,json_url ="json/'.$pricelist_name.'_'.$_POST['id'].'.json" where id ='.$_POST['id'].';';
		//$QueryResult = mysql_query($SQLstring);
		$QueryResult = $objDB->Conn->Execute($SQLstring);
		
		/*$array_new = $where_clause1 = array();
		$array_new['pricelist_name'] = $pricelist_name;			
		$array_new['last_update'] = date("Y-m-d H:i:s");				
		$array_new['json_url'] = 'json/'.$pricelist_name.'_'.$_POST['id'].'.json';			
		$where_clause1['id'] = $_POST['id'];
		$QueryResult = $objDBWrt->Update($array_new , $TableName, $where_clause1);*/
		
		if ($QueryResult) {
			$json= $_POST['json'];
			$pos=1;
			$json = substr_replace($json, '"pricelist_id":"'.$_POST['id'].'",', $pos, 1);
		} else {
		    echo "Error: " . $SQLstring . "<br>" . mysql_error();
		}
			echo $id;
		//file_put_contents('json/'.$pricelist_name.'_'.$_POST['id'].'.json', $json);
		
		//$json = addslashes($json);
		//file_put_contents(ROOT.'/assets/pricelist/json/'.$pricelist_name.'_'.$_POST['id'].'.json', $json);
		file_put_contents(ROOT.'/assets/pricelist/json/'.$_POST['id'].'.json', $json);
		
		// start memcache
		$memcached->set("pricelistjson_".$_POST['id'],$json,time() + CACHE_30_MIN);
		// end memcache				
	}
?>
