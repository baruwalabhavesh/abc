<?php 

  //taking data from ajax post
  $card_id = $_POST['cardid'];
  $card_title = $_POST['cardtitle'];
  //$card_type = $_POST['cardtype'];
  $text_color = $_POST['textcolor'];
  $card_background = $_POST['cardbackground'];
  $reward_background_image = $_POST['rewardbackgroundimage'];
  $stamps_per_card = $_POST['stampspercard'];
  $reward_title = $_POST['rewardtitle'];
  $reward_color = $_POST['textcolor'];
  $reward_points = $_POST['rewardpoints'];
  $terms_conditions = $_POST['termsandconditions'];
  $card_volume= $_POST['cardvolume'];
  $reward_per_visit= $_POST['rewardpervisit'];
  $additional_reward_points = $_POST['additionalrewardpoints'];
  $participating_locations = $_POST['locations'];
  $original_volume = $_POST['originalvolume'];
  $merchant_id = $_SESSION['merchant_id'];
  $redeemption_limit = $_POST['redeemption_limit'];
  $card_category = $_POST['card_category'];
  $card_keyword = $_POST['card_keyword'];
  $stamp_image = $_POST['stamp_image'];
   
  //total_points is points required
  $total_points =$_POST['totalpoints'];
  // setting reward per visit to 0 if its null
  if($reward_per_visit=="" ){
    $reward_per_visit =0;
  }


  //include 'dbconnect.php';
  //checks if it is a new id . -1 means new card 
  //by default card id is set to -1 if it is -1 insert query is executed. on update page id is set to its value on db. 
  if($card_id == "-1"){


    $sql="INSERT INTO merchant_loyalty_card (title,stamp_image_type_id,card_text_color,card_background,stamps_per_card,reward_per_visit,card_volume,cards_left,card_status,terms_conditions,points_required,merchant_id,created_date,modified_date,redeemption_limit,card_category,card_keyword,is_published) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $a_bind_params = array($card_title,$stamp_image,$text_color,$card_background,$stamps_per_card,$reward_per_visit,$card_volume,$card_volume,4,$terms_conditions,$total_points,$merchant_id,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$redeemption_limit,$card_category,$card_keyword,0);
    $rs = $objDBWrt->Conn->Execute($sql, $a_bind_params);
	
	$max_query=  'SELECT MAX(id) id from merchant_loyalty_card';
	$rs_max_query = $objDB->Conn->Execute($max_query);
	$card_id = $rs_max_query->fields[0];
	
	// insert data in merchant_loyalty_reward_card table
	
	$sql1="INSERT INTO merchant_loyalty_reward_card (loyalty_card_id,reward_title,reward_points,reward_background_image) VALUES (?,?,?,?)";
    $a_bind_params1 = array($card_id,$reward_title,$reward_points,$reward_background_image);
    $rs1 = $objDBWrt->Conn->Execute($sql1, $a_bind_params1);
    
    // insert data in merchant_loyalty_reward_card table
    
	$loc_arr = explode(",",$participating_locations);
	foreach($loc_arr as $loc) {
		
		$query=  'SELECT active from locations where id='.$loc;
		$rs_query = $objDB->Conn->Execute($query);
		$active = $rs_query->fields[0];
	
		if($active==1)
		{
			$sql1="INSERT INTO loyaltycard_location (loyalty_card_id,location_id) VALUES (".$card_id.",".$loc.")";
			$rs1 = $objDBWrt->Conn->Execute($sql1);
		}
	}
	
    if($rs === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $objDBWrt->ErrorMsg(), E_USER_ERROR);
    } 
    else {
      
    }
  
    
  }

  //if id  not equal to -1 then its id exist in db . then it is updated
  else
  {
	  
	// new code for increase or decrease merchant points 
	
	$total_points1 = $_POST['total_points1'];
	$original_cards_left = $_POST['original_cards_left'];
	$new_cards_left = $_POST['new_cards_left'];
	
	$query=  'SELECT is_published from merchant_loyalty_card where id='.$card_id;
	$rs_query = $objDB->Conn->Execute($query);
	$is_published = $rs_query->fields[0];
	
	if($is_published==1) 
	{
		/*
		  if card is published once then only in edit, block points and release points 
		  otherwise when created cv=2 cl=2
		  edit cv=3 cl=3, save => it will block point for 1 cl
		  and when activate => it will block point for 3 cl
		*/
		
		if($new_cards_left!=$original_cards_left)
		{
			if($new_cards_left>$original_cards_left)
			{
				// deduct points from merchant's available points
				
				$sql1='update merchant_point_management set points_available = points_available - '.$total_points1.', points_blocked_loyaltycard = points_blocked_loyaltycard + '.$total_points1.',	points_blocked = points_blocked + '.$total_points1.' where merchant_id ='.$_SESSION['merchant_id'].';';
				$rs1=$objDBWrt->Conn->Execute($sql1);
			}
			else
			{
				// add points to merchant's available points
				
				$sql1='update merchant_point_management set points_available = points_available + '.$total_points1.', points_blocked_loyaltycard = points_blocked_loyaltycard - '.$total_points1.',	points_blocked = points_blocked - '.$total_points1.' where merchant_id ='.$_SESSION['merchant_id'].';';
				$rs1=$objDBWrt->Conn->Execute($sql1);
			}
		}
	}
	
	// new code for increase or decrease merchant points  
    
    //$new_cards_left = $original_volume - $card_volume;
    echo $new_cards_left;
	
	$sql="UPDATE  merchant_loyalty_card set title= ?,stamp_image_type_id=?,card_text_color=?,card_background=?,card_volume=?,cards_left=?,terms_conditions=?,points_required=?,redeemption_limit=?,modified_date=?,card_category=?,card_keyword=? where id = ".$card_id.";";
    //$a_bind_params = array($card_title,$stamp_image,$text_color,$card_background,$card_volume,$card_volume,$terms_conditions,$total_points,$redeemption_limit,date("Y-m-d H:i:s"),$card_category,$card_keyword);
    $a_bind_params = array($card_title,$stamp_image,$text_color,$card_background,$card_volume,$new_cards_left,$terms_conditions,$total_points,$redeemption_limit,date("Y-m-d H:i:s"),$card_category,$card_keyword);
    $rs = $objDBWrt->Conn->Execute($sql, $a_bind_params);
    
    // if card_left become zero, then update card status to pause
    if($new_cards_left==0)
	{
		$sql='update merchant_loyalty_card set card_status = 4 where id ='.$card_id.';';
		$rs=$objDBWrt->Conn->Execute($sql);
	}
	// if card_left become zero, then update card status to pause
		
    $sql1="UPDATE  merchant_loyalty_reward_card set reward_title=?,reward_background_image=? where loyalty_card_id = ".$card_id.";";
    $a_bind_params1 = array($reward_title,$reward_background_image);
    $rs1 = $objDBWrt->Conn->Execute($sql1, $a_bind_params1);
    
    $sql="delete from loyaltycard_location where loyalty_card_id = ".$card_id;
    $rs = $objDBWrt->Conn->Execute($sql);
    
    $loc_arr = explode(",",$participating_locations);
	foreach($loc_arr as $loc) {		
		$sql1="INSERT INTO loyaltycard_location (loyalty_card_id,location_id) VALUES (".$card_id.",".$loc.")";
		$rs1 = $objDBWrt->Conn->Execute($sql1);
	}
	
	
    if($rs === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $objDBWrt->ErrorMsg(), E_USER_ERROR);
    } else {
      echo 'success';
    }
  }
  
 ?>
