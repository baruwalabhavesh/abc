<?php 
  $id=$_POST['card_id'];
  echo $id;
  //include 'dbconnect.php';

  /*$findid= 'select * from cards where id =?';
  $idrs=$conn->Execute($findid,$id);*/
  $idrs=$objDB->Conn->Execute('select * from merchant_loyalty_card where id =?',array($id));
  if($idrs === false) {
    echo 'failed';  
  }
  else {
    $sql='update merchant_loyalty_card set card_status = 4 where id ='.$id.';';
    $rs=$objDBWrt->Conn->Execute($sql);
    if($rs === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $objDBWrt->ErrorMsg(), E_USER_ERROR);
    }
    else{
      echo ' activate success';
    }
  }


?>
