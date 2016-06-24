<?php 
  $id=$_POST['card_id'];

  //include 'dbconnect.php';


  $findid= 'select * from merchant_loyalty_card where id ='.$id;
  $idrs=$objDB->Conn->Execute($findid);
  if($idrs === false) {
    echo 'failed';  
  }
  else {
    echo 'success';
  }
?>
