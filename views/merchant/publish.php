<?php
	//change publish status
	if(isset($_POST['id'])){
		$id=$_POST['id'];
		//include 'dbconnect.php';
		$TableName= "pricelists";
		$sql ='UPDATE '.$TableName.' SET publish = !publish WHERE id= '.$id.';' ;
		$Query = mysql_query($sql);

		if ($Query ) {

		   header("Location: pricelists.php");
		}
		else{
			echo "sorry couldn't publish or unpublish";
		}
	}
?>
