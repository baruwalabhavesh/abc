<?php
	//delete a pricelist
	if(isset($_POST['id'])){
		$id=$_POST['id'];
		//include 'dbconnect.php';
		$TableName= "pricelists";
		$findId = 'SELECT * from '.$TableName.' WHERE id = '.$id.';';
		$Row = mysql_fetch_array(mysql_query($findId));
		echo $Row[5];
		$query= mysql_query($findId) or die($query."<br/><br/>".mysql_error());

		if($query){
			$Reorderquery = 'UPDATE ' .$TableName.' set display_order = display_order-1 where display_order > '.$Row[5].';';
		}
		if(mysql_fetch_array($query) !== false){
			$sql ='select json_url from '.$TableName.' where id='.$id.';' ;
			$SQLstring = 'delete from '.$TableName.' where id='.$id.';' ;
			$SelectFile = mysql_query($sql);
			$QueryResult = mysql_query($SQLstring);
			if ($QueryResult && $SelectFile) {
				$Row = mysql_fetch_row($SelectFile);
				if(file_exists(ROOT.'/assets/pricelist/'.$Row[0])){
					unlink(ROOT.'/assets/pricelist/'.$Row[0]);
					mysql_query($Reorderquery);
				}
				else{
					echo 'file not there';
				}
				header("Location: pricelists.php");
			}
		}
		else{
			echo '<script>alert("pricelist doesn\'t exist.")</script>';
			  echo '<script>window.location.href="pricelists.php"</script>';
		}

	}
?>
