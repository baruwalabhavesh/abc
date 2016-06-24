<?php 
	//reordering 
	//include 'dbconnect.php';
	//mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
	//mysql_select_db(DATABASE_NAME);
	$id = $_POST['key'];
	$order = $_POST['value'];
	$id_array = explode(",", $id);
	$order_array = explode(",", $order);
	$id_length = count($id_array);
	$order_length = count($order_array);
	echo $id_length;
	for($i = 0;$i < $id_length ; $i++){
		$TableName= "pricelists";
		$sql ='UPDATE '.$TableName.' SET display_order = "'.$order_array[$i].'" WHERE id= "'.$id_array[$i].'";' ;
		//$Query= mysql_query($sql);
		$Query=  $objDB->Conn->Execute($sql);
		
		if ($Query ) {

			echo 'hello';
		}
		echo 'id'.$id_array[$i].'<br>';
		echo 'order'.$order_array[$i];
	}
	header("Location: pricelists.php");


	


 ?>
