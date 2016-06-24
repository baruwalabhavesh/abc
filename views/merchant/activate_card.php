<?php 
  $id=$_POST['card_id'];
  //echo $id;
  //include 'dbconnect.php';

	
  /*$findid= 'select * from cards where id =?';
  $idrs=$conn->Execute($findid,$id);*/
  $idrs=$objDB->Conn->Execute('select * from merchant_loyalty_card where id =?',array($id));
  
	
	$max_query=  'SELECT points_available from merchant_point_management where 	merchant_id='.$_SESSION['merchant_id'];
	$rs_max_query = $objDB->Conn->Execute($max_query);
	$available_points = $rs_max_query->fields[0];
	
	$points_Required = $idrs->fields['points_required'];
	//$points_Required = 12000;
	
	//echo " Available Points = ".$available_points." Points Required = ".$points_Required;
	//exit();
	
  if($idrs === false) 
  {
    echo 'failed';  
  }
  else 
  {
	
	if($points_Required<=$available_points)
	{	  
		//if($idrs->fields['card_status']==4)
		if($idrs->fields['is_published']==1)
		{
			// bydefault card status 4 (pause) so no points increase or decrease.
		}
		else
		{
			$available_point = $available_points - $points_Required;
			$sql1='update merchant_point_management set points_available = '.$available_point.', points_blocked_loyaltycard = points_blocked_loyaltycard + '.$points_Required.',points_blocked = points_blocked + '.$points_Required.'  where merchant_id ='.$_SESSION['merchant_id'].';';
			$rs1=$objDBWrt->Conn->Execute($sql1);
		}
		
		$activation_code = create_loyalty_activation_code();
		$sql='update merchant_loyalty_card set is_published=1,card_status = 2,activationcode="'.$activation_code.'" where id ='.$id.';';
		$rs=$objDBWrt->Conn->Execute($sql);
		
		
			
		if($rs === false) 
		{
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $objDBWrt->ErrorMsg(), E_USER_ERROR);
		}
		else
		{
		  echo 'activate success';
		}
	}
	else
	{
		
	}
  }
  
  function create_loyalty_activation_code()
	{
		$code_length=6;
		$alfa = "123456789ABCDEFGHJKLMNPQRSTUVWXYZ";
		$code="";
		for($i = 0; $i < $code_length; $i ++) 
		{
		  $code .= $alfa[rand(0, strlen($alfa)-1)];
		} 
		return $code;
	}


?>
