<?php 
  //$id=$_POST['card_id'];
  $id=$_REQUEST['card_id'];
  $crdid= $_POST['card_id'];
  //include 'dbconnect.php';

  /*$sql='delete  from cards where id =?';
  $findid= 'select * from cards where id =?';
  $idrs=$conn->Execute($findid,$id);*/
  
  $pack_data = array();
  $pack_data['merchant_id'] = $_SESSION['merchant_id'];
  $get_pack_data = $objDB->Show("merchant_billing",$pack_data);

  $pack_data1 = array();
  $pack_data1['id'] = $get_pack_data->fields['pack_id'];
  $get_billing_pack_data = $objDB->Show("billing_packages",$pack_data1);

  $idrs=$objDB->Conn->Execute('select * from merchant_loyalty_card where id =?',array($id));

  $array_mlrc = array();
  $array_mlrc['loyalty_card_id'] = $idrs->fields['id'];
  $RS_mlrc = $objDB->Show("merchant_loyalty_reward_card",$array_mlrc);
					
  $transaction_fees_stamp = $get_billing_pack_data->fields['transaction_fees_stamp'];
  $card_volume = $idrs->fields['cards_left'];
  $number_of_stamp = $idrs->fields['stamps_per_card'];
  $points_per_visit = $idrs->fields['reward_per_visit'];
  $additional_reward = $RS_mlrc->fields['reward_points'];

  $total_points = ($card_volume*$number_of_stamp*$transaction_fees_stamp) + ($points_per_visit*$number_of_stamp*$card_volume) + ($card_volume*$additional_reward);
  //echo " Total Points = ".$total_points;
  //exit();
  
  if($idrs === false) {
    echo 'failed';  
  }
  else {
	    
    $sql='update merchant_loyalty_card set card_status = 3,cards_left=0,qrcode_id=0 where id ='.$id;
    $rs=$objDBWrt->Conn->Execute($sql);
    if($rs === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $objDBWrt->ErrorMsg(), E_USER_ERROR);
    }
    else
    {
		/*
		$sql='update merchant_user set available_point = available_point+'.$total_points.' where id ='.$_SESSION['merchant_id'].';';
		$rs=$objDBWrt->Conn->Execute($sql);
		*/
		$sql1='update merchant_point_management set points_available = points_available + '.$total_points.', points_blocked_loyaltycard = points_blocked_loyaltycard - '.$total_points.', points_blocked = points_blocked - '.$total_points.' where merchant_id ='.$_SESSION['merchant_id'].';';
		$rs1=$objDBWrt->Conn->Execute($sql1);

		echo ' delete success';
    }
  }
?>
