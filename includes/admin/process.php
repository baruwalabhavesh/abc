<?php
//require_once("../classes/Config.Inc.php");
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
include_once(SERVER_PATH."/admin/phpqrcode/qrlib.php");   
//$objDB = new DB();
//$objJSON = new JSON();
function get_merchant_session_id($session_data){
	global $objDB;
	$Sql = "SELECT * FROM user_sessions WHERE session_data='".mysql_escape_string($session_data)."' ORDER BY id DESC LIMIT 1";
	//echo $Sql."<hr>";
	$RS = $objDB->Conn->Execute($Sql);
	if($RS->RecordCount()>0){
		$session_id = base64_decode($RS->fields['session_id']);
		return $session_id;
	}
	return "";
}
if($_REQUEST['loginornot']== "true")
{
    if($_SESSION['admin_id'] == "")
	{       
		$json_array = array();
		$json_array['status'] = "false";

		$json_array['link'] =WEB_PATH."/admin/login.php";

		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	else
	{
		$json_array = array();
		$json_array['status'] = "true";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}		
}
if(isset($_REQUEST['btnLogin'])){
	$array = $json_array = array();
	$array['username'] = $_REQUEST['username'];
	$array['password'] = $_REQUEST['password'];
	
	$RS = $objDB->Show("admin_user",$array);
	if($RS->RecordCount()<=0){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
	$Row = $RS->FetchRow();
	$_SESSION['admin_id'] = $Row['id'];
	$_SESSION['admin_info'] = $Row;
	
	$array_values = $where_clause = $array = array();
	$array_values['lastvisit_date'] = date("Y-m-d H:i:s");
	$where_clause['id'] = $_SESSION['admin_id'];
	$objDB->Update($array_values, "admin_user", $where_clause);
	
	$array = array();
	$user_session = session_id();
	$array['sessiontime'] = strtotime(date("Y-m-d H:i:s")); 
	$array['session_id'] = base64_encode($_SESSION['admin_id']); 
	$array['session_data'] = md5("admin".$array['sessiontime'].$user_session); 
	$objDB->Insert($array, "user_sessions");
	
	$json_array['status'] = "true";
	$json_array['admin_id'] = $array['session_data'];
	$json = json_encode($json_array);
	echo $json;
	exit();
}
if(isset($_REQUEST['btnApproveMerchant'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$array_values = $where_clause = array();
	$array_values['approve'] = 1;
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array_values, "merchant_user", $where_clause);
	$json_array['status'] = "true";
	$json_array['message'] = "Marchant has been Approved successfully";
	$json = json_encode($json_array);
	echo $json;
	exit();
}
if(isset($_REQUEST['btnGetAllMerchants'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$RS = $objDB->Show("merchant_user");
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$Row['password'] = "";
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}
if(isset($_REQUEST['btnGetApprovedMerchants'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$array = array();
	$array['approve'] = "1";
	$RS = $objDB->Show("merchant_user", $array);
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$Row['password'] = "";
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}

if(isset($_REQUEST['btnGetNotApprovedMerchants'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$array = array();
	$array['approve'] = "0";
	$RS = $objDB->Show("merchant_user", $array);
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$Row['password'] = "";
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}

if(isset($_REQUEST['btnGetAllCustomers'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$RS = $objDB->Show("customer_user");
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$Row['password'] = "";
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}

if(isset($_REQUEST['btnGetActiveCustomers'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$array = array();
	$array['active'] = "1";
	$RS = $objDB->Show("customer_user", $array);
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$Row['password'] = "";
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}

if(isset($_REQUEST['btnGetInActiveCustomers'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$array = array();
	$array['active'] = "0";
	$RS = $objDB->Show("customer_user", $array);
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$Row['password'] = "";
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}

if(isset($_REQUEST['btnForgotPassword'])){
	$array = array();
	$array['emailaddress'] = $_REQUEST['emailaddress'];
	$RS = $objDB->Show("admin_user", $array);
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = 1;
		$mail = new PHPMailer();
		$body = "<p>Your Username and password are given below </p>";
		$body .= "<p><b>Username:</b> ".$RS->fields['username']."</p>";
		$body .= "<p><b>Password:</b> ".$RS->fields['password']."</p>";
		$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
		$mail->AddAddress($RS->fields['emailaddress']);
		$mail->Subject    = "Forgot Username or Password";
		$mail->MsgHTML($body);
		$mail->Send();
		$json_array['message'] = "Your login details have been email to you";
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();

}
if(isset($_REQUEST['btnGetAllCategories'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$RS = $objDB->Show("categories",''," Order By `orders` ASC ");
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();

}

if(isset($_REQUEST['btnAddpac'])){

	require_once(LIBRARY.'/stripe-php/config.php');	

	$array = array();

	$interval = $_REQUEST['interval'];
	if($interval == 'q'){
		$interval = 'month';
		$interval_count = 3;
	}else if($interval == 'a'){
		$interval = 'year';
		$interval_count = 1;
	}else{
		$interval = 'month';
		$interval_count = 1;
	}

	$raw_slug = $_REQUEST['pac_name'].'_'.$_REQUEST['currency'].'_'.$_REQUEST['interval'];
	$string = preg_replace('/([^a-zA-Z0-9\_]+)/', '', $raw_slug);
	$slug = strtolower(preg_replace('/-+/', '_', $string));

	$json_array['status'] = "true";
	if($_REQUEST['price'] > 0){
		try{
			/*$metadata = array(
					'location'=>$p['no_of_loca'],
					'active_campaign_per_locaion'=>$p['no_of_active_camp_per_loca'],
					'total_campaign_per_month'=>$p['total_no_of_camp_per_month'],
					'coupon_transaction_fee'=>$p['transaction_fees'],
					'enable_coupon_redeemption_fee'=>$p['enable_coupon_redeemption_fee'],
					'min_share_point'=>$p['min_share_point'],
					'min_reward_point'=>$p['min_reward_point'],
					'active_loyalty_cards'=>$p['active_loyalty_cards'],
					'enable_coupon_redeemption_fee'=>$p['enable_coupon_redeemption_fee'],
					'transaction_fees_stamp'=>$p['transaction_fees_stamp']
				);*/

				$s_plan = \Stripe\Plan::create(array(
				  "amount" => $_REQUEST['price']*100,
				  "interval" => $interval,
				  "interval_count"=>$interval_count,
				  "name" => $_REQUEST['pac_name'],
				  "currency" => $_REQUEST['currency'],
				  "trial_period_days"=>$_REQUEST['trial_period_days'],
				  //"metadata"=>$metadata,
				  "id" => $slug)
				);
		
		} catch (Exception $e) {
		            $json_array['status'] = "false";
		            $json_array['serror'] = $e->getMessage();
		}
	}

	if($json_array['status'] = "true"){
		$array['pack_name'] = $_REQUEST['pac_name'];
		$array['price'] = $_REQUEST['price'];
		$array['currency'] = $_REQUEST['currency'];	
		$array['interval'] = $_REQUEST['interval'];
		$array['trial_period_days'] = $_REQUEST['trial_period_days'];
		$array['slug'] = $slug;		
		$array['no_of_loca'] = $_REQUEST['no_of_loca'];
		$array['no_of_active_camp_per_loca'] = $_REQUEST['no_of_active_camp_per_loca'];
		$array['total_no_of_camp_per_month'] = $_REQUEST['total_no_of_camp_per_month'];
		$array['min_share_point'] = $_REQUEST['min_share_point'];
		$array['min_reward_point'] = $_REQUEST['min_reward_point'];
		$array['reward_zone_active_campaign'] = $_REQUEST['reward_zone_active_campaign'];
		$array['reward_zone_active_gift_card'] = $_REQUEST['reward_zone_active_gift_card'];
		$array['created'] = date('Y-m-d H:i:s');
			$array['transaction_fees'] = $_REQUEST['transaction_fees'];
			if(isset($_REQUEST['chk_c_r_fee']))
			{
				$array['enable_coupon_redeemption_fee'] = 1;
			}
			else
			{
				$array['enable_coupon_redeemption_fee'] = 0;
			}
			$array['active_loyalty_cards'] = $_REQUEST['active_loyalty_cards'];
			$array['transaction_fees_stamp'] = $_REQUEST['transaction_fees_stamp'];
		$array['image_upload_limit'] = $_REQUEST['image_upload_limit'];
		$array['video_upload_limit'] = $_REQUEST['video_upload_limit'];
		$objDB->Insert($array, "billing_packages");
		$_SESSION['msg']="New Package has been added successfully";
		$msg = "1";	
		
	}else{
		$_SESSION['msg']="New Package has been failed";
		$msg = "1";
	}
		header("Location: payment.php?msg=".$msg);
		exit();	
}
/*
if(isset($_REQUEST['btnAddCategory'])){
    
	$admin_id = get_merchant_session_id($_SESSION['admin_id']);
	if($admin_id == "" ){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
	$array = array();
	$array['cat_name'] = $_REQUEST['cat_name'];
	$array['cat_image'] = $_REQUEST['cat_image'];		
	$array['orders'] = $_REQUEST['order'];
	$objDB->Insert($array, "categories");	

	$json_array['status'] = "true";
	$json_array['message'] = "Category has been added successfully";
	$json = json_encode($json_array);
	echo $json;
	exit();

}
*/


if(isset($_REQUEST['btnAddNewCategory'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
	$array = array();
	$array['cat_name'] = $_REQUEST['cat_name'];	
	$array['cat_image'] = $_REQUEST['cat_image'];
	$array['cat_scroller_image'] = $_REQUEST['cat_scroller_image'];
	$array['cat_scroller_image_active'] = $_REQUEST['cat_scroller_image_active'];
	$array['orders'] = $_REQUEST['orders'];
	$objDB->Insert($array, "categories");	

	$json_array['status'] = "true";
	$json_array['message'] = "Category has been added successfully";
	$json = json_encode($json_array);
	echo $json;
	exit();

}

if(isset($_REQUEST['btnEditCategory'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == "" ){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
	$array = $where_clause = array();
	$array['cat_name'] = $_REQUEST['cat_name'];	
	$array['cat_image'] = $_REQUEST['cat_image'];
	$array['orders'] = $_REQUEST['orders'];
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "categories", $where_clause);
	$json_array['status'] = "true";
	$json_array['message'] = "Category has been Editted successfully";
	$json = json_encode($json_array);
	echo $json;
	exit();

}

if(isset($_REQUEST['btnGetAllStoreLocations'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$Sql = "SELECT L.*, MU.firstname, MU.lastname
			FROM locations L, merchant_user MU
			WHERE L.created_by = MU.id
			ORDER BY L.id DESC";
	$RS = $objDB->Conn->Execute($Sql);

	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}

if(isset($_REQUEST['btnGetActiveStoreLocations'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$Sql = "SELECT L.*, MU.firstname, MU.lastname
			FROM locations L, merchant_user MU
			WHERE L.created_by = MU.id AND L.active='1'
			ORDER BY L.id DESC";
	$RS = $objDB->Conn->Execute($Sql);

	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}

if(isset($_REQUEST['btnGetInactiveStoreLocations'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$Sql = "SELECT L.*, MU.firstname, MU.lastname
			FROM locations L, merchant_user MU
			WHERE L.created_by = MU.id AND L.active='0'
			ORDER BY L.id DESC";
	$RS = $objDB->Conn->Execute($Sql);

	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}
if(isset($_REQUEST['btnActivateLocation'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
	$array_values = $where_clause = array();
	$array_values['active'] = 1;
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array_values, "locations", $where_clause);
	$json_array['status'] = "true";
	$json_array['message'] = "Location has been Activated successfully";
	$json = json_encode($json_array);
	echo $json;
	exit();

}

if(isset($_REQUEST['btnInactivateLocation'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
	$array_values = $where_clause = array();
	$array_values['active'] = 0;
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array_values, "locations", $where_clause);
	$json_array['status'] = "true";
	$json_array['message'] = "Location has been Activated successfully";
	$json = json_encode($json_array);
	echo $json;
	exit();

}


if(isset($_REQUEST['btnGetAllCampaings'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$Sql = "SELECT C.*, MU.firstname, MU.lastname
			FROM campaigns C, merchant_user MU
			WHERE C.created_by = MU.id
			ORDER BY C.id DESC";
	//echo $Sql."<hr>";		
	$RS = $objDB->Conn->Execute($Sql);
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
}

if(isset($_REQUEST['btnGetApprovedCampaings'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$Sql = "SELECT C.*, MU.firstname, MU.lastname
			FROM campaigns C, merchant_user MU
			WHERE C.created_by = MU.id AND C.visible='1'
			ORDER BY C.id DESC";
	//echo $Sql."<hr>";		
	$RS = $objDB->Conn->Execute($Sql);
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
}

if(isset($_REQUEST['btnGetUnApprovedCampaings'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	$Sql = "SELECT C.*, MU.firstname, MU.lastname
			FROM campaigns C, merchant_user MU
			WHERE C.created_by = MU.id AND C.visible='0'
			ORDER BY C.id DESC";
	//echo $Sql."<hr>";		
	$RS = $objDB->Conn->Execute($Sql);
	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
}

// Today 21-07-2012
if(isset($_REQUEST['btnApproveCampaing'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
	$array_values = $where_clause = array();
	$array_values['visible'] = 1;
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array_values, "campaigns", $where_clause);
	
	$json_array['status'] = "true";
	$json_array['message'] = "Campaing has been Approved successfully";
	$json = json_encode($json_array);
	echo $json;
	exit();

}
if(isset($_REQUEST['btnUnApproveCampaing'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
	$array_values = $where_clause = array();
	$array_values['visible'] = 0;
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array_values, "campaigns", $where_clause);
	
	$json_array['status'] = "true";
	$json_array['message'] = "Campaing has been UnApproved successfully";
	$json = json_encode($json_array);
	echo $json;
	exit();

}

if(isset($_REQUEST['btnGetAllRewardUsers'])){
	$admin_id = get_merchant_session_id($_REQUEST['admin_id']);
	if($admin_id == ""){
		$json_array['status'] = "false";
		$json_array['message'] = "Please enter valid Username/Password";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	//echo $admin_id."<hr>";
	$Sql = "SELECT RU.*, CU.firstname, CU.lastname, C.title
			FROM reward_user RU, campaigns C, customer_user CU
			WHERE RU.campaign_id=C.id AND CU.id=RU.customer_id
			ORDER BY RU.id DESC";
	$RS = $objDB->Conn->Execute($Sql);

	if($RS->RecordCount()>0){
		$json_array['status'] = "true";
		$json_array['total_records'] = $RS->RecordCount();
		$count=0;
		while($Row = $RS->FetchRow()){
			$json_array[$count] = $Row;
			$count++;
		}
	}else{
		$json_array['status'] = "false";
		$json_array['total_records'] = 0;
	}
	//echo "<pre>";print_r($json_array);echo "</pre>";
	$json = json_encode($json_array);
	echo $json;
	exit();
	
}
if(isset($_REQUEST['btnEditGiftCard']))
{
		$array = array();
	$array['title'] = $_REQUEST['title'];	
	$array['keywords'] = $_REQUEST['keywords'];
	if($_REQUEST['hdn_image_path'] == "")
	{
		$array['image'] = "";
	}
	else
	{		
		$array['image'] = trim($_REQUEST['hdn_image_path']);	
	}

	$array['merchant_id'] = $_REQUEST['merchant_id'];
	/* $array['value'] = $_REQUEST['card_value']; */
	$array['category_id'] = $_REQUEST['category_id'];
	$array['card_value'] = $_REQUEST['giftcard_value'] ;
	$array['redeem_point_value'] = $_REQUEST['giftcard_rdem_value'] ;
	$array['ship_to'] = $_REQUEST['giftcard_Shipto'];
	$array['description'] = $_REQUEST['description'];
	$array['is_active'] = 1;
	$where_clause['id'] = $_REQUEST['hdn_id'];
	$objDB->Update($array, "giftcards", $where_clause);
	$_SESSION['msg'] = "Gift Card merchant has been updated successfully.";
	header("Location: giftcards.php");
	exit();
}
if(isset($_REQUEST['btnAddGiftCard'])){

	$array = array();
	$array['title'] = $_REQUEST['title'];
	$array['keywords'] = $_REQUEST['keywords'];	
	if($_REQUEST['hdn_image_path'] == "")
	{
		$array['image'] = "";
	}
	else
	{		
		$array['image'] = trim($_REQUEST['hdn_image_path']);	
	}

	$array['merchant_id'] = $_REQUEST['merchant_id'];
	/* $array['value'] = $_REQUEST['card_value']; */
	$array['category_id'] = $_REQUEST['category_id'];
	$array['card_value'] = $_REQUEST['giftcard_value'] ;
	$array['redeem_point_value'] = $_REQUEST['giftcard_rdem_value'] ;
	$array['ship_to'] = $_REQUEST['giftcard_Shipto'];
	$array['description'] = $_REQUEST['description'];
	$array['is_active'] = 1;
	$objDB->Insert($array, "giftcards");	
	$_SESSION['msg'] = "Gift Card merchant has been added successfully.";
	header("Location: giftcards.php");
	exit();

}
if(isset($_REQUEST['btnAddGiftcardmerchant'])){
	$array = array();
	$array['merchant_name'] = $_REQUEST['mer_name'];	
	$array['active'] = 1;
	$objDB->Insert($array, "giftcard_merchant_user");	
	$_SESSION['msg'] = "Gift Card merchant has been added successfully.";
	header("Location: giftcard-merchants.php");
	exit();

}

if(isset($_REQUEST['btnAddrewardtype'])){
	$array = array();
	$array['reward_type'] = $_REQUEST['cat_name'];	
	$array['order'] = $_REQUEST['order'];	
    $array['active'] = 1;
	$objDB->Insert($array, "reward_types");	
	header("Location: rewardtypes.php");
	$_SESSION['msg'] = "Reward type has been added successfully.";
	exit();

}
if(isset($_REQUEST['btnAddGiftcardCategory'])){
	$array = array();
	$array['cat_name'] = $_REQUEST['cat_name'];	
	$array['order'] = $_REQUEST['order'];	
    $array['active'] = 1;
	$objDB->Insert($array, "giftcard_categories");	
	$_SESSION['msg'] = "Gift Card Category has been added successfully.";
	header("Location: giftcard_categories.php");
	exit();

}

if(isset($_REQUEST['btnAddCategory'])){
	$array = array();
	$array['cat_name'] = $_REQUEST['cat_name'];	
	$array['cat_image'] = $_REQUEST['cat_image'];
	$array['orders'] = $_REQUEST['order'];
        $array['active'] = 1;
	$objDB->Insert($array, "categories");	
	$_SESSION['msg'] = "Category has been added successfully.";
	header("Location: add-category.php");
	exit();

}
if(isset($_REQUEST['btnAddLoyaltyCategory'])){
	$array = array();
	$array['name'] = $_REQUEST['name'];	
	$array['display_order'] = $_REQUEST['display_order'];
    $array['active'] = 1;
	$objDB->Insert($array, "loyaltycard_category");	
	$_SESSION['msg'] = "Category has been added successfully.";
	header("Location: add-loyalty-category.php");
	exit();

}

if(isset($_REQUEST['btnAddCountry'])){
	$array = array();
	$array['name'] = $_REQUEST['name'];	

    $array['active'] = 0;
    $array['currency_id'] = $_REQUEST['currency'];
	$objDB->Insert($array, "country");	
	$_SESSION['msg'] = "Country has been added successfully.";
	header("Location: add-country.php");
	exit();

}

if(isset($_REQUEST['btnAddState'])){
	$array = array();
	$array['name'] = $_REQUEST['name'];	

    $array['active'] = 0;
    $array['country_id'] = $_REQUEST['country_id'];
    $array['short_form'] = $_REQUEST['short_form'];
	$objDB->Insert($array, "state");	
	$_SESSION['msg'] = "State has been added successfully.";
	header("Location: add-state.php?id=".$_REQUEST['country_id']);
	exit();

}

if(isset($_REQUEST['btnAddCity'])){
	$array = array();
	$array['name'] = $_REQUEST['name'];	

    $array['active'] = 0;
    $array['state_id'] = $_REQUEST['state_id'];
    
    //$string_address = $_REQUEST['address'].", ".$_REQUEST['city'].", ".$_REQUEST['state'].", ".$_REQUEST['zip'];
    $string_address = $_REQUEST['name'];
	$string_address = urlencode($string_address);
    $geocode= file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$string_address."&sensor=false");
    $geojson= json_decode($geocode,true);
	if($geojson['status']=='OK')
	{
		$array['latitude'] = $geojson['results'][0]['geometry']['location']['lat'];
		$array['longitude'] = $geojson['results'][0]['geometry']['location']['lng'];
	}
	$objDB->Insert($array, "city");	
	$_SESSION['msg'] = "City has been added successfully.";
	header("Location: add-city.php?id=".$_REQUEST['state_id']."&cid=".$_REQUEST['country_id']);
	exit();

}

if(isset($_REQUEST['cangiftmer']))
{
	header("Location: giftcard-merchants.php");
	exit();
}
if(isset($_REQUEST['btnCancelgiftcard']))
{
	header("Location: giftcards.php");
	exit();
}

if(isset($_REQUEST['canrewrdtype']))
{
	header("Location: rewardtypes.php");
	exit();
}
if(isset($_REQUEST['btnEditgiftcardMerchantYes'])){
	$array = array();
	$array['merchant_name'] = $_REQUEST['cat_name'];	
	
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "giftcard_merchant_user", $where_clause);
	$_SESSION['msg'] = "Gift Card merchant has been edited successfully.";
	header("Location: giftcard-merchants.php");
	exit();
}

if(isset($_REQUEST['btnEditrewrdtype'])){
	$array = array();
	$array['reward_type'] = $_REQUEST['cat_name'];	
	$array['order'] = $_REQUEST['order'];	
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "reward_types", $where_clause);
	$_SESSION['msg'] = "Reward types has been edited successfully.";
	header("Location: rewardtypes.php");
	exit();
}

if(isset($_REQUEST['btnEditgiftcardCategoryYes'])){
	$array = array();
	$array['cat_name'] = $_REQUEST['cat_name'];	
	$array['order'] = $_REQUEST['order'];	
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "giftcard_categories", $where_clause);
	$_SESSION['msg'] = "Gift Card Category has been edited successfully.";
	header("Location: giftcard_categories.php");
	exit();
}
if(isset($_REQUEST['btnEditCategoryYes'])){
	$array = array();
	$array['cat_name'] = $_REQUEST['cat_name'];	
	$array['cat_image'] = $_REQUEST['cat_image'];	
	$array['cat_scroller_image'] = $_REQUEST['cat_scroller_image'];
	$array['cat_scroller_image_active'] = $_REQUEST['cat_scroller_image_active'];
	$array['orders'] = $_REQUEST['orders'];
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "categories", $where_clause);
	$_SESSION['msg'] = "Category has been edited successfully.";
	header("Location: edit-category.php?id=".$_REQUEST['id']);
	exit();
}
if(isset($_REQUEST['btnEditLoyaltyCategory'])){
	$array = array();
	$array['name'] = $_REQUEST['name'];	
	$array['display_order'] = $_REQUEST['display_order'];
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "loyaltycard_category", $where_clause);
	$_SESSION['msg'] = "Category has been edited successfully.";
	header("Location: edit-loyalty-category.php?id=".$_REQUEST['id']);
	exit();
}

if(isset($_REQUEST['btnEditCountry'])){
	$array = array();
	$array['name'] = $_REQUEST['name'];	

	$array['currency_id'] = $_REQUEST['currency'];
	//print_r($array);
	//exit();
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "country", $where_clause);
	$_SESSION['msg'] = "Country has been edited successfully.";
	header("Location: edit-country.php?id=".$_REQUEST['id']);
	exit();
}

if(isset($_REQUEST['btnEditState'])){
	$array = array();
	$array['name'] = $_REQUEST['name'];	

	$array['country_id'] = $_REQUEST['country_id'];
	$array['short_form'] = $_REQUEST['short_form'];
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "state", $where_clause);
	$_SESSION['msg'] = "State has been edited successfully.";
	header("Location: edit-state.php?id=".$_REQUEST['id']."&cid=".$_REQUEST['country_id']);
	exit();
}

if(isset($_REQUEST['btnEditCity'])){
	$array = array();
	$array['name'] = $_REQUEST['name'];	

	//$array['state_id'] = $_REQUEST['state_id'];
	$array['state_id'] = $_REQUEST['state'];
	
	//$string_address = $_REQUEST['address'].", ".$_REQUEST['city'].", ".$_REQUEST['state'].", ".$_REQUEST['zip'];
    $string_address = $_REQUEST['name'];
	$string_address = urlencode($string_address);
    $geocode= file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$string_address."&sensor=false");
    $geojson= json_decode($geocode,true);
	if($geojson['status']=='OK')
	{
		$array['latitude'] = $geojson['results'][0]['geometry']['location']['lat'];
		$array['longitude'] = $geojson['results'][0]['geometry']['location']['lng'];
	}
	
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "city", $where_clause);
	$_SESSION['msg'] = "City has been edited successfully.";
	//header("Location: edit-city.php?id=".$_REQUEST['id']."&sid=".$_REQUEST['state_id']);
	header("Location: edit-city.php?id=".$_REQUEST['id']."&sid=".$_REQUEST['state']);
	exit();
}

if(isset($_REQUEST['btnupdatepac'])){

	require_once(LIBRARY.'/stripe-php/config.php');	

	$array = array();
	$json_array['status'] = "true";

	if($_REQUEST['price'] > 0){
		try{
			$p = \Stripe\Plan::retrieve($_REQUEST['slug']);		
			$p->name = $_REQUEST['pac_name'];
			//$p->interval = $interval;
			//$p->interval_count = $interval_count;
			//$p->currency = $_REQUEST['currency'];
			$p->save();
		
		} catch (Exception $e) {
		            $json_array['status'] = "false";
		            $json_array['serror'] = $e->getMessage();
		}
	}
	
	if($json_array['status'] == 'true'){
		$array['pack_name'] = $_REQUEST['pac_name'];	
			$where_clause['id'] = $_REQUEST['id'];
		//$array['price'] = $_REQUEST['price'];		
			$array['no_of_loca'] = $_REQUEST['no_of_loca'];
		$array['no_of_active_camp_per_loca'] = $_REQUEST['no_of_active_camp_per_loca'];
		$array['total_no_of_camp_per_month'] = $_REQUEST['total_no_of_camp_per_month'];
		$array['min_share_point'] = $_REQUEST['min_share_point'];
		$array['min_reward_point'] = $_REQUEST['min_reward_point'];
		$array['transaction_fees'] = $_REQUEST['transaction_fees'];
		$array['reward_zone_active_campaign'] = $_REQUEST['reward_zone_active_campaign'];
		$array['reward_zone_active_gift_card'] = $_REQUEST['reward_zone_active_gift_card'];
			if(isset($_REQUEST['chk_c_r_fee']))
			{
				$array['enable_coupon_redeemption_fee'] = 1;
			}
			else
			{
				$array['enable_coupon_redeemption_fee'] = 0;
			}
			$array['active_loyalty_cards'] = $_REQUEST['active_loyalty_cards'];
			$array['transaction_fees_stamp'] = $_REQUEST['transaction_fees_stamp'];
			$array['image_upload_limit'] = $_REQUEST['image_upload_limit'];
			$array['video_upload_limit'] = $_REQUEST['video_upload_limit'];
			$objDB->Update($array, "billing_packages", $where_clause);
				//exit();
			
	
		$Sql = "select * from merchant_billing mb , merchant_user mu where mb.merchant_id = mu.id and  pack_id=".$_REQUEST['id'];
		
		$RS = $objDB->Conn->Execute($Sql);
		$flag = true;
		while($Row = $RS->FetchRow()){
			$old_campaigns = $_REQUEST['hdn_total_no_of_camp_per_month'];
			$old_campaign_left = $Row['total_no_of_campaign'];
			$new_campaigns = $_REQUEST['total_no_of_camp_per_month'];
			$new_campaign_left = $_REQUEST['total_no_of_camp_per_month'];
	
			if($new_campaigns > $old_campaigns)
			{
				$campaign_left = $new_campaign_left - ( $old_campaigns - $old_campaign_left);
			}
			else
			{
				if($new_campaign_left > $old_campaigns )
				{
					$campaign_left = $new_campaign_left - ( $old_campaigns - $old_campaign_left);
				}
				else
				{ 
					if($new_campaign_left <= $old_campaign_left)
					{
						$campaign_left =$new_campaign_left;
					}
					else{
							$campaign_left =$new_campaign_left;	
					}
				}
			} 
				  $update_sql = "Update merchant_user set total_no_of_campaign=".$campaign_left." where id=".$Row['merchant_id'];
		     
		        $objDBWrt->Conn->Execute($update_sql);
	       
		     
		}
		 
			
	}
	
	header("Location:payment.php");
	exit();
}
if(isset($_REQUEST['btnupdate_redeem_fee'])){
	$array = array();
	
	$where_clause['id'] = $_REQUEST['id'];
	
	$array['start_value'] = $_REQUEST['start_value'];	
	$array['end_value'] = $_REQUEST['end_value'];		
	$array['type'] = $_REQUEST['type'];
	$array['amount_value'] = $_REQUEST['amount_value'];
	$objDB->Update($array, "redeemption_fee_charge", $where_clause);
	header("Location:payment.php");
	exit();
}
if(isset($_REQUEST['can_redeem_fee'])){
	header("Location:payment.php");
	exit();
}
if(isset($_REQUEST['btnAddPointpac'])){
 $array = array();
//	$array['package_name'] = $_REQUEST['pac_name'];
	$array['price'] = $_REQUEST['pac_prize'];		
	$array['points'] = $_REQUEST['points'];
	$objDB->Insert($array, "point_packages");	
        
	$msg = "1";	
        $_SESSION['msg'] = "Conversation Rate has been successfully added";
	header("Location: payment.php");
	exit();
}
if(isset($_REQUEST['btnupdatepointpac'])){
	$array = array();
//	$array['package_name'] = $_REQUEST['pac_name'];	
	$array['price'] = $_REQUEST['pac_prize'];	
	$array['points'] = $_REQUEST['points'];
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "point_packages", $where_clause);
	$_SESSION['msg'] = "Conversation rate has been Updated successfully.";
	header("Location:payment.php");
	exit();
}
if(isset($_REQUEST['canpac'])){
    header("Location: payment.php");
    exit();
}
if(isset($_REQUEST['cancat'])){
    header("Location:categories.php");
    exit();
}
if(isset($_REQUEST['canloyaltycat'])){
    header("Location:manage_category.php");
    exit();
}
if(isset($_REQUEST['canCountry'])){
    header("Location:country-management.php");
    exit();
}
if(isset($_REQUEST['cangiftcat'])){
    header("Location:giftcard_categories.php");
    exit();
}
if(isset($_REQUEST['canreview'])){
    header("Location:manage_review.php");
    exit();
}
/* add template method */
if(isset($_REQUEST['btnAddEmailtemplate'])){
	
	
	$array = array();
	$array['template_key'] = $_REQUEST['key'];
	$array['value'] = $_REQUEST['value'];		
	$array['subject'] = $_REQUEST['subject'];
	$objDB->Insert($array, "emailtemplate");	

	$json_array['status'] = "true";
	$json_array['message'] = "Email template has been added successfully";
	$json = json_encode($json_array);
	//echo $json;
        $_SESSION['msg'] = "Email template has been added successfully";
	header("Location:emailtemplates.php");
	exit();

}
if(isset($_REQUEST['btnEditEmailtemplate']))
{
 $array = $where_clause = array();
	$array['value'] = $_REQUEST['value'];	
	$array['subject'] = $_REQUEST['subject'];
	
	$where_clause['id'] = $_REQUEST['id'];
	$objDB->Update($array, "emailtemplate", $where_clause);
	$json_array['status'] = "true";
	$json_array['message'] = "Email template has been Updated successfully";
	$json = json_encode($json_array);
	$_SESSION['msg'] = "Email template has been Updated successfully.";
	header("Location:emailtemplates.php");
	exit();
}

if(isset($_REQUEST['canEmailtemplate'])){
   header("Location:emailtemplates.php");
    exit();
}

if(isset($_REQUEST['check_key_name_exist'])){
  $Sql= "select * from emailtemplate where template_key='".$_REQUEST['key_name']."'";

  $RS = $objDB->Conn->Execute($Sql);
$json_array = array();
	if($RS->RecordCount()>0){
		$json_array['status'] = "false";
		
	}else{
		$json_array['status'] = "true";
		
	}
        $json  =json_encode($json_array);
        echo $json;
}

function getemailcontent($username,$emailkey,$to,$from)
{
    
   $Sql= "select * from emailtemplate where template_key='".$emailkey."' ";
//$objDB = new DB();
  $RS = $objDB->Conn->Execute($Sql);


	if($RS->RecordCount()>0){
  
            $content = $RS->fields['value'];
            $re_content = str_replace("##username##",$username,$content);
         //   echo $re_content;
            /* for send mail */
                $mail = new PHPMailer();
		
		$body .=  $re_content;
		$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
		$mail->AddAddress($to);
		$mail->From = "no-reply@scanflip.com";
		$mail->FromName = "ScanFlip Support";
		$mail->Subject    = $RS->fields['subject'];
		$mail->MsgHTML($body);
	//	$mail->Send();
               /* for send mail */
        }
}
///getemailcontent("Punita Patel" ,"asdf","a","a");
/* */
if(isset($_REQUEST['btnAddTemplate'])){
$array = array();
	/*if($_FILES["business_logo"]["tmp_name"] != ''){
		$Extension = strtolower(substr($_FILES["business_logo"]["name"],strrpos($_FILES["business_logo"]["name"],".")+1));
		$array['business_logo'] = "template_".strtotime(date("Y-m-d H:i:s")).".".$Extension;
		copy($_FILES["business_logo"]["tmp_name"], SERVER_PATH."/merchant/images/logo/$array[business_logo]"); 
	}*/
	/*  start of PAY-508-28033  */
	if($_REQUEST['hdn_image_path'] == "")
	{
		$array['business_logo'] = "";
	}
	else
	{		
		$array['business_logo'] = trim($_REQUEST['hdn_image_path']);	
	}
	/*  end of PAY-508-28033  */
        
	$array['title'] = $_REQUEST['title'];
	//$array['discount'] = $_REQUEST['discount'];
	$array['category_id'] = $_REQUEST['category_id'];
	$array['description'] = $_REQUEST['description'];  
        $array['terms_condition'] = $_REQUEST['terms_condition'];
        $array['is_default'] = 1;
        //$array['print_coupon_description'] = $_REQUEST['print_coupon_description'];
	
	$json_array['status']='false';
	$json_array['message']=$_REQUEST['description'];
	//$json_array['message']=$_REQUEST['hdndescription'];

	
	$array['created_date'] = date("Y-m-d H:i:s");
$array['modified_date'] = date("Y-m-d H:i:s");
//	if(isset($_REQUEST['mer_id']))
//	{
//		$array['created_by'] =  $_REQUEST['mer_id'];
//	}
//	else
//	{
//		if($_REQUEST['created_by'] == ""){
//			$array['created_by'] = $_SESSION['merchant_id'];
//		}else{
//			$array['created_by'] = get_merchant_session_id($_REQUEST['created_by']);
//		}
//	}
        $array['created_by'] = 0;
	$objDB->Insert($array, "campaigns_template");
	// 369
	$_SESSION['msg'] = "Template is Added successfully";
	$json_array=array();
	$json_array['status']='true';
	$json_array['message']='Template is Added successfully';
	$json = json_encode($json_array);
	//echo $json;
    header("Location:campaigntemplates.php");
	exit();
}

if(isset($_REQUEST['btneditTemplate'])){
	$array = array();
	if($_REQUEST['hdn_image_path'] == "")
	{
		$array['business_logo'] = "";
	}
	else
	{		
		$array['business_logo'] = trim($_REQUEST['hdn_image_path']);	
	}
	
	$array['title'] = $_REQUEST['title'];
        //$array['discount'] = $_REQUEST['discount'];
	$array['category_id'] = $_REQUEST['category_id'];
	$array['description'] = $_REQUEST['description'];
        $array['terms_condition'] = $_REQUEST['terms_condition'];
        //$array['print_coupon_description'] = $_REQUEST['print_coupon_description'];
        
	$array['created_date'] = date("Y-m-d H:i:s");

//	if($_REQUEST['created_by'] == ""){
//		$where_clause['created_by'] = $_SESSION['merchant_id'];
//	}else{
//		$where_clause['created_by'] = get_merchant_session_id($_REQUEST['created_by']);
//	}	
        $where_clause['created_by'] = 0;
	$where_clause['id'] = $_REQUEST['bna'];
	//print_r($array);
	$objDB->Update($array, "campaigns_template", $where_clause);
	// 369
	$_SESSION['msg'] = "Template is Updated successfully";
	$json_array=array();
	$json_array['status']='true';
	$json_array['message']='Template is Updated successfully';
	$json = json_encode($json_array);
	//echo $json;
        header("Location:campaigntemplates.php");
    exit();
	// 369

}
if(isset($_REQUEST['btnCanceltemplate']))
{
     header("Location:campaigntemplates.php");
    exit();
}

if(isset($_REQUEST['btnGetAllCampaignOfMerchant']))
{
	
	 $json_array = array();
	 $records = array();
	 
	 $merchant_id = $_REQUEST['mer_id'];
	 $action=$_REQUEST['action'];
	 $today_date = date("Y-m-d")." 00:00:00";
	 $Where = "";
	 
	 if($action == "active")
	 {
	//	$Where .= " AND CONVERT_TZ(NOW(),'-06:00','+00:00') BETWEEN CONVERT_TZ(b.start_date,'+00:00',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) AND CONVERT_TZ(b.expiration_date,'+00:00',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) and a.active =1 and a.active_in_future<>1";
            
             $Where .= " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date and a.active =1 and a.active_in_future<>1 and a.offers_left>0 ";
            //$Where .= " AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date and a.active =1 and a.active_in_future<>1";
             
	}
		
        /*
	 if(isset($_REQUEST['parent']))
	 {
		//$Sql = "SELECT * FROM campaigns WHERE (created_by = $merchant_id or created_by in ( select id from merchant_user where merchant_parent = $merchant_id )) $Where ORDER BY modified_date DESC";
                $Sql = "SELECT distinct b.* from campaign_location a  INNER JOIN campaigns b on a.campaign_id=b.id INNER JOIN locations c on a.location_id=c.id where 
                    (b.created_by = $merchant_id or b.created_by in ( select id from merchant_user where merchant_parent = $merchant_id))  $Where ORDER BY modified_date DESC";
                
	 }
	 else
	 {
		//$Sql = "SELECT * FROM campaigns WHERE created_by = $merchant_id $Where ORDER BY modified_date DESC";
		 $Sql = "SELECT distinct b.* from campaign_location a  INNER JOIN campaigns b on a.campaign_id=b.id INNER JOIN locations c on a.location_id=c.id where 
                    b.created_by = $merchant_id   $Where ORDER BY modified_date DESC";
	 }
	 */
        $Sql = "SELECT distinct b.* from campaign_location a  INNER JOIN campaigns b on a.campaign_id=b.id INNER JOIN locations c on a.location_id=c.id where 
                    b.created_by = $merchant_id   $Where ORDER BY modified_date DESC";
		  
     $json_array['query'] = $Sql;
	 $RS = $objDB->Conn->Execute($Sql);
	 
	 
	  
	 
	 if($RS->RecordCount()>0)
	 {		 
		 
		  $json_array['status'] = "true";
		  
                  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] =get_field_value($Row);
		   	$count++;
		  }
		  $json_array["records"]= $records;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }

	 $json = json_encode($json_array);
	 echo $json;
	 exit();
}

function get_field_value($Row)
{
	$ar = $Row;
	
	$ar1 = array_unique($ar);
	for ($i = 0; $i < (count($ar)) ; $i++) {
		if(key_exists($i,$ar))
		{
			unset($ar[$i]);
		}
	    }
	
	return $ar;	
}

if(isset($_REQUEST['btnGetAllactiveCampaignOfMerchant']))
{
  $json_array = array();
  $records = array();
  
  $merchant_id = $_REQUEST['mer_id'];
  //$location_id = $_REQUEST['loc_id'];
 
  $today_date = date("Y-m-d")." 00:00:00";
  $Where = "";
  
  $Where .= " c.created_by=".$merchant_id." AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date and a.active =1 and a.active_in_future<>1";
             
  $Sql = "SELECT distinct b.* from campaign_location a  INNER JOIN campaigns b on a.campaign_id=b.id INNER JOIN locations c on a.location_id=c.id where $Where ORDER BY modified_date DESC";
 
  $RS = $objDB->Conn->Execute($Sql);
  if($RS->RecordCount()>0)
  {   
    $json_array['status'] = "true";
    $json_array['total_records'] = $RS->RecordCount();
    $count=0;
    while($Row = $RS->FetchRow())
    {
      $records[$count] =get_field_value($Row);
      $count++;
    }
    $json_array["records"]= $records;
  }
  else
  {
    $json_array['status'] = "false";
    $json_array['total_records'] = 0;
    $json = json_encode($json_array);
    echo $json;
    exit();
  }
  $json = json_encode($json_array);
  echo $json;
  exit();
}

if(isset($_REQUEST['btnGetAllactiveCampaignOfMerchantLocation']))
{
  $json_array = array();
  $records = array();
  
  //$merchant_id = $_REQUEST['mer_id'];
  $location_id = $_REQUEST['loc_id'];
 
  $today_date = date("Y-m-d")." 00:00:00";
  $Where = "";
  
  $Where .= " c.id=".$location_id." AND CONVERT_TZ(NOW(),'".CURR_TIMEZONE."',SUBSTR(c.timezone,1, POSITION(',' IN c.timezone)-1)) BETWEEN b.start_date AND b.expiration_date and a.active =1 and a.active_in_future<>1";
             
  $Sql = "SELECT distinct b.* from campaign_location a  INNER JOIN campaigns b on a.campaign_id=b.id INNER JOIN locations c on a.location_id=c.id where $Where ORDER BY modified_date DESC";
 
  $RS = $objDB->Conn->Execute($Sql);
  if($RS->RecordCount()>0)
  {   
    $json_array['status'] = "true";
    $json_array['total_records'] = $RS->RecordCount();
    $count=0;
    while($Row = $RS->FetchRow())
    {
      $records[$count] =get_field_value($Row);
      $count++;
    }
    $json_array["records"]= $records;
  }
  else
  {
    $json_array['status'] = "false";
    $json_array['total_records'] = 0;
    $json = json_encode($json_array);
    echo $json;
    exit();
  }
  $json = json_encode($json_array);
  echo $json;
  exit();
}
if(isset($_REQUEST['btnUpdateemailsettings']))
{

    $sql_frequency = "Select action from admin_settings where  setting='Email Frequency'";
     $rs_frequency = $objDB->Conn->Execute($sql_frequency);
	 if($_REQUEST['rd_scanflip_merchant_campaign'] == 1)
	 {
	 
    // if($rs_frequency->fields['action'] != $_REQUEST['email_frequency'])
    // {
          $sql = "Update admin_settings set action=".$_REQUEST['email_frequency']." where setting='Email Frequency'";
          $objDB->Conn->Execute($sql);
//          print_r($_REQUEST);
//          exit;
          /* change schedular */
           /// echo exec('crontab -r');
            exec("crontab -r");
            exec("rm -f -r /var/www/vhosts/scanflip.com/httpdocs/scanflip/admin/cron-adminmail.php");
            if($_REQUEST['email_frequency'] == 1)
            {
                 $data1 = "0 0,3,6,9,12,15,18 * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/frequent-mail-by-admin.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/reserve-campaign-schedular.php\n* 0 1 * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/active-camapigns-of-month-cron.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/active-campaign-scedular.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/token_expire.php\n";
            }
            else if($_REQUEST['email_frequency'] == 2)
            {
                 $data1 = "0 0,6,12,18 * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/frequent-mail-by-admin.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/reserve-campaign-schedular.php\n* 0 1 * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/active-camapigns-of-month-cron.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/active-campaign-scedular.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/token_expire.php\n";
            }
            else if($_REQUEST['email_frequency'] == 3)
            {
                 $data1 = "0 0,12 * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/frequent-mail-by-admin.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/reserve-campaign-schedular.php\n* 0 1 * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/active-camapigns-of-month-cron.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/active-campaign-scedular.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/token_expire.php\n";
            }
            else if($_REQUEST['email_frequency'] == 4)
            {
                 $data1 = "0 6 * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/frequent-mail-by-admin.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/reserve-campaign-schedular.php\n* 0 1 * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/active-camapigns-of-month-cron.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/active-campaign-scedular.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/token_expire.php\n";
            }
					
           
            file_put_contents('/var/www/vhosts/scanflip.com/httpdocs/scanflip/admin/cron-adminmail.php', $data1);
            exec('crontab /var/www/vhosts/scanflip.com/httpdocs/scanflip/admin/cron-adminmail.php', $output, $return);
    // }
	 }
	 else{
	
	   exec("crontab -r");
            exec("rm -f -r /var/www/vhosts/scanflip.com/httpdocs/scanflip/admin/cron-adminmail.php");
            if($_REQUEST['email_frequency'] == 1)
            {
                 $data1 = "* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/reserve-campaign-schedular.php\n* 0 1 * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/active-camapigns-of-month-cron.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/active-campaign-scedular.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/token_expire.php\n";
            }
            else if($_REQUEST['email_frequency'] == 2)
            {
                 $data1 = "* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/reserve-campaign-schedular.php\n* 0 1 * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/active-camapigns-of-month-cron.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/active-campaign-scedular.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/token_expire.php\n";
            }
            else if($_REQUEST['email_frequency'] == 3)
            {
                 $data1 = "* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/reserve-campaign-schedular.php\n* 0 1 * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/active-camapigns-of-month-cron.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/active-campaign-scedular.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/token_expire.php\n";
            }
            else if($_REQUEST['email_frequency'] == 4)
            {
                 $data1 = "* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/reserve-campaign-schedular.php\n* 0 1 * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/merchant/active-camapigns-of-month-cron.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/active-campaign-scedular.php\n* * * * * /usr/bin/php -q /var/www/vhosts/scanflip.com/httpdocs/scanflip/token_expire.php\n";
            }
            
           
            file_put_contents('/var/www/vhosts/scanflip.com/httpdocs/scanflip/admin/cron-adminmail.php', $data1);
            exec('crontab /var/www/vhosts/scanflip.com/httpdocs/scanflip/admin/cron-adminmail.php', $output, $return);
	 
	 }
   
    $sql1 = "Update admin_settings set action=".$_REQUEST['rd_scanflip_merchant_campaign']." where setting='New Scanflip merchants campaign'";
    $objDB->Conn->Execute($sql1);
    header("Location:emailsettings.php");
    exit();
}
if(isset($_REQUEST['btncancelemailsettings']))
{
     header("Location:emailsettings.php");
     exit();
}
if(isset($_REQUEST['btnAddmarketingmaterial']))
{
	      
	$array['material_name'] = $_REQUEST['name'];
	$array['material_size'] = $_REQUEST['document_size'];
	
	$array['material_format'] = $_REQUEST['material_format'];

	$objDB->Insert($array, "marketing_material");

	$_SESSION['msg'] = "material is Added successfully";
	$json_array=array();
	$json_array['status']='true';
	$json_array['message']='material is Added successfully';
	$json = json_encode($json_array);

    header("Location:marketingmaterial.php");
	exit();	
}
if(isset($_REQUEST['btnCancelmarketingmaterial']))
{
	   header("Location:marketingmaterial.php");
	exit();	
}
if(isset($_REQUEST['btnEditmarketingmaterial']))
{
	//      print_r($_REQUEST);
        //      exit();	
	$array['material_name'] = $_REQUEST['name'];
	$array['material_size'] = $_REQUEST['document_size'];
	$array['material_format'] = $_REQUEST['material_format'];

	
	$where_clause['id'] = $_REQUEST['hdn_id'];
	//print_r($array);
	$objDB->Update($array, "marketing_material", $where_clause);

	$_SESSION['msg'] = "material is updated successfully";
	$json_array=array();
	$json_array['status']='true';
	$json_array['message']='material is updated successfully';
	$json = json_encode($json_array);

    header("Location:marketingmaterial.php");
	exit();	
}

if(isset($_REQUEST['btnaddqrcodegroup']))
{

	$array['group_name'] = $_REQUEST['name'];
	$array['no_of_qrcodes'] = 0;
	$array['merchant_id'] =  $_REQUEST['merchant_id'];
	$array['active'] = 1;

	$insertedgroupid = $objDB->Insert($array, "qrcode_group");
        
    
    header("Location:QRcodegrouplist.php");
	exit();	
}
if(isset($_REQUEST['btnCancelqrcodegroup']))
{
	   header("Location:QRcodelist.php");
	exit();	
}
if(isset($_REQUEST['btndeleteqrcodes']))
{
	$qrcodeids = $_REQUEST['qrcodeids'];
	
	$pos = strpos($qrcodeids,",");
	if ($pos === false) 
	{
		//echo "digit";
		
		$Sql = "SELECT qrcode from qrcodes where id=".$qrcodeids;
		//echo $Sql."<br/>";
		$RS = $objDB->Conn->Execute($Sql);
		//echo $RS->fields['qrcode']."<br/>";
		
		$SQLstring1 = 'delete from qrcodes where id ='.$qrcodeids;
		$QueryResult = $objDB->Conn->Execute($SQLstring1);
		
		$SQLstring2 = 'delete from qrcodegroup_qrcode where qrcode_id ='.$qrcodeids;
		$QueryResult = $objDB->Conn->Execute($SQLstring2);
		
		// start image delete logic
	
		$filename_eps_80  = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($RS->fields['qrcode'])."_80X80.eps";
		$filename_eps_120 = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($RS->fields['qrcode'])."_120X120.eps";
		$filename_eps_200 = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($RS->fields['qrcode'])."_200X200.eps";
		$filename_eps_275 = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($RS->fields['qrcode'])."_275X275.eps";
		
		unlink($filename_eps_80);
		unlink($filename_eps_120);
		unlink($filename_eps_200);
		unlink($filename_eps_275);
		
		$filename_jpg_80  = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($RS->fields['qrcode'])."_80X80.jpg";
		$filename_jpg_120 = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($RS->fields['qrcode'])."_120X120.jpg";
		$filename_jpg_200 = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($RS->fields['qrcode'])."_200X200.jpg";
		$filename_jpg_275 = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($RS->fields['qrcode'])."_275X275.jpg";
		
		unlink($filename_jpg_80);
		unlink($filename_jpg_120);
		unlink($filename_jpg_200);
		unlink($filename_jpg_275);
		
		// end image delete logic
			
	}
	else
	{
		//echo "string";

		$arr =  explode(",",$qrcodeids);
	
		foreach ($arr as $value) 
		{
			$Sql = "SELECT qrcode from qrcodes where id=".$value;
			//echo $Sql."<br/>";
			$RS = $objDB->Conn->Execute($Sql);
			//echo $RS->fields['qrcode']."<br/>";
			
			$SQLstring1 = 'delete from qrcodes where id ='.$value;
			$QueryResult = $objDB->Conn->Execute($SQLstring1);
			
			$SQLstring2 = 'delete from qrcodegroup_qrcode where qrcode_id ='.$value;
			$QueryResult = $objDB->Conn->Execute($SQLstring2);
	
			// start image delete logic
	
			$filename_eps_80  = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($RS->fields['qrcode'])."_80X80.eps";
			$filename_eps_120 = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($RS->fields['qrcode'])."_120X120.eps";
			$filename_eps_200 = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($RS->fields['qrcode'])."_200X200.eps";
			$filename_eps_275 = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($RS->fields['qrcode'])."_275X275.eps";
			
			unlink($filename_eps_80);
			unlink($filename_eps_120);
			unlink($filename_eps_200);
			unlink($filename_eps_275);
			
			$filename_jpg_80  = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($RS->fields['qrcode'])."_80X80.jpg";
			$filename_jpg_120 = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($RS->fields['qrcode'])."_120X120.jpg";
			$filename_jpg_200 = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($RS->fields['qrcode'])."_200X200.jpg";
			$filename_jpg_275 = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($RS->fields['qrcode'])."_275X275.jpg";
			
			unlink($filename_jpg_80);
			unlink($filename_jpg_120);
			unlink($filename_jpg_200);
			unlink($filename_jpg_275);
			
			// end image delete logic
	
		}
	}
	
	$json_array=array();
	$json_array['status']='true';
	$json = json_encode($json_array);
	echo $json;
	exit();
}	
if(isset($_REQUEST['btnGetAllqrcodes']))
{	
	$sLimit = "";
	if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ". $_REQUEST['iDisplayStart'] .", ".
			 $_REQUEST['iDisplayLength'] ;
	}
	
	
	//$SQLstring = 'SELECT SQL_CALC_FOUND_ROWS * FROM qrcodes order by created_date desc '.$sLimit;
	
	$SQLstring = 'SELECT SQL_CALC_FOUND_ROWS q.* FROM qrcodes q, qrcodegroup_qrcode qq, qrcode_group qg where qq.qrcodegroup_id = qg.id and qq.qrcode_id = q.id and qg.merchant_id='.$_REQUEST['mer_id'].' order by created_date desc '.$sLimit;
	
	$QueryResult = $objDB->Conn->Execute($SQLstring);

	
	$sQuery = $objDB->Conn->Execute("SELECT FOUND_ROWS()");
	$total= $sQuery->fields['FOUND_ROWS()'];
	
	$Json = array();
	$Json['iDisplayLength'] = $_REQUEST['iDisplayLength'];
	$Json['iDisplayStart'] = $_REQUEST['iDisplayStart'];
	$Json['iTotalDisplayRecords'] = $total;
	$Json['sEcho'] = $_REQUEST['sEcho'];
	$Json['sSearch'] = $_REQUEST['sSearch'];
	$Json['aaData'] = array();
		
	if($QueryResult->RecordCount() >0)
	{
		$k =0;
		while($Row = $QueryResult->FetchRow())
		{
			if($Row['reserve'] != 1 )
			{ 
				$Json['aaData'][$k][]='<input type="checkbox" name="chkassigngroup[]" id="chk_'.$Row['id'].'" value="'.$Row['id'].'">';
			}
			else
			{
				$Json['aaData'][$k][]='';
			}
			$Json['aaData'][$k][]=$Row['qrcode'];
			if($Row['reserve']==1) 
            { 
				$Json['aaData'][$k][]="Assigned"; 
			}
            else 
            { 
				$Json['aaData'][$k][]="Not Assigned"; 
			}
			$array = array();
                
			$array['qrcode_id']=$Row['id'];
			
			$sql_group = "Select * from qrcodegroup_qrcode qq, qrcode_group qg where qq.qrcodegroup_id = qg.id and qq.qrcode_id = ".$Row['id'];
		
			$RS_groupinfo = $objDB->Conn->Execute($sql_group); 
			
			if($RS_groupinfo->RecordCount()!= 0)
			{
				
				$array = array();
		   
				$array['id']=$RS_groupinfo->fields['merchant_id'];
				$RS_merchantinfo = $objDB->Show("merchant_user",$array);
				if($RS_groupinfo->fields['merchant_id'] != 0)
				{
                    $Json['aaData'][$k][]= "Assigned"; 
                }
                else 
                { 
					$Json['aaData'][$k][]= "Not Assigned"; 
				}
                $Json['aaData'][$k][]= $RS_groupinfo->fields['group_name'];
                if($RS_groupinfo->fields['merchant_id'] != 0)
                { 
					$Json['aaData'][$k][]= $RS_merchantinfo->fields['business']."-".$RS_merchantinfo->fields['firstname']." ".$RS_merchantinfo->fields['lastname'];
                }
                else
                {
                    $Json['aaData'][$k][]= "Super Admin";
                }
            }
            
			$k++;
		}
	}
	else
	{
	}
	echo json_encode($Json);
    exit;
}
if(isset($_REQUEST['btnCancelqrcodegrouplist']))
{
	   header("Location:QRcodegrouplist.php");
	exit();	
}
function create_campaign_code()
{
    $code_length=8;
    //echo $alfa = "1AB2CD3EF4G5HI6JK7LM8N9OP10QRSTU".$campaign_id."VWXYZ";
    $alfa = "12345678910ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $code="";
    for($i = 0; $i < $code_length; $i ++) 
    {
      $code .= $alfa[rand(0, strlen($alfa)-1)];
    } 
    return $code;
}
if(isset($_REQUEST['btnaddmoreqrcodes']))
{
    $array ['id']=$_REQUEST['group_id'];
$RS = $objDB->Show("qrcode_group",$array );
$n_o_qrcode = $RS->fields['no_of_qrcodes'];
$nw_n_o = $n_o_qrcode + $_REQUEST['no_of_qrcode'];

$sql = 'Update qrcode_group set no_of_qrcodes='.$nw_n_o.' where id='.$_REQUEST['group_id'];

   $objDB->Conn->Execute($sql);
   $insertedgroupid = $_REQUEST['group_id'];
        
   /*  $PNG_TEMP_DIR = SERVER_PATH.'/admin/phpqrcode/temp/';
    
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    //include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    
    $filename = $PNG_TEMP_DIR.'test.png';

    //include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
 $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
       // $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);
          */
        for($i=0;$i<$_REQUEST['no_of_qrcode'];$i++)
        {
            $data=create_campaign_code();
           // echo "====".$data;
//            $fn = 'test'.md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
//             $filename = $PNG_TEMP_DIR.$fn;
//          //   echo "===".$filename."==";
//        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
        $fn = '';
        $array_qrtag = array();
        $array_qrtag['qrcode'] = $data;
        $array_qrtag['qrcode_image'] = $fn ;
        $array_qrtag['reserve'] = 0 ;
        $array_qrtag['created_date'] = date("Y-m-d H:i:s") ;
        $insertedid = $objDB->Insert($array_qrtag, "qrcodes");
        $array_qg = array();
        $array_qg['qrcode_id']=$insertedid;
        $array_qg['qrcodegroup_id']=$insertedgroupid;
        $objDB->Insert($array_qg, "qrcodegroup_qrcode");
        }
        $array = array();
  $array['id'] = $_REQUEST['merchant_id'];
                $RS = $objDB->Show("merchant_user",$array);
	$_SESSION['msg'] = "QRcode is generated for ".$RS->fields['firstname']." ".$RS->fields['lastname'];
	$json_array=array();
	$json_array['status']='true';
	$json_array['message']="QRcode is generated for ".$RS->fields['firstname']." ".$RS->fields['lastname'];
	$json = json_encode($json_array);

    header("Location:QRcodelist.php");
	exit();	
}
if(isset($_REQUEST['btneditqrcodes']))
{
  
    if($_REQUEST['is_existinggroup'] == 0)
    {
       
        $array= array();
        $array['group_name'] = $_REQUEST['txt_groupname'];
	$array['no_of_qrcodes'] = 0;
	$array['merchant_id'] =  $_REQUEST['merchant_id'];
	$array['active'] = 1;

	$groupid = $objDB->Insert($array, "qrcode_group");
    }
    else{
        
      
        $groupid = $_REQUEST['qrcode_group_list'];
    }
    
    $arr_g = array();
    $arr_g['qrcode_id'] = $_REQUEST['qid'];
      $RS_qrcode = $objDB->Show("qrcodegroup_qrcode",$arr_g );
    
   $array = array() ;
   $array ['id'] = $RS_qrcode->fields['qrcodegroup_id'];
    $RS = $objDB->Show("qrcode_group",$array );
    $n_o_qrcode = $RS->fields['no_of_qrcodes'];
    $nw_n_o = $n_o_qrcode - 1;

    $sql = 'Update qrcode_group set no_of_qrcodes='.$nw_n_o.' where id='.$RS_qrcode->fields['qrcodegroup_id'];
    
    $objDB->Conn->Execute($sql);
    $sql =  "update qrcodegroup_qrcode set qrcodegroup_id=".$groupid." where qrcode_id=".$_REQUEST['qid'];
  $array = array() ;
    $objDB->Conn->Execute($sql);
    $array ['id']=$groupid;
    $RS = $objDB->Show("qrcode_group",$array );
    $n_o_qrcode = $RS->fields['no_of_qrcodes'];
    $nw_n_o = $n_o_qrcode + 1;

    $sql = 'Update qrcode_group set no_of_qrcodes='.$nw_n_o.' where id='.$groupid;

   $objDB->Conn->Execute($sql);
  header("Location:QRcodelist.php"); 
	exit();	

}
if(isset($_REQUEST['btn_assigngroup']))
{
    if($_REQUEST['is_existinggroup'] == 0)
    {
       
        $array= array();
        $array['group_name'] = $_REQUEST['txt_groupname'];
	$array['no_of_qrcodes'] = 0;
	$array['merchant_id'] =  $_REQUEST['merchant_id'];
	$array['active'] = 1;

	$groupid = $objDB->Insert($array, "qrcode_group");
    }
    else{
        
      
        $groupid = $_REQUEST['qrcode_group_list'];
    }
  $qrcode_array = explode(';',$_REQUEST['hdn_qrcodelist']);
  //$qrcode_array = $_REQUEST['chkassigngroup'];
  for($i=0;$i<count($qrcode_array);$i++)
  {
        $sql =  "update qrcodegroup_qrcode set qrcodegroup_id=".$groupid." where qrcode_id=".$qrcode_array[$i];
 // echo $sql."<br/>";
    $objDB->Conn->Execute($sql);
      $array= array();
    $array ['id']=$groupid;
    $RS = $objDB->Show("qrcode_group",$array );
    $n_o_qrcode = $RS->fields['no_of_qrcodes'];
    $nw_n_o = $n_o_qrcode + 1;

    $sql = 'Update qrcode_group set no_of_qrcodes='.$nw_n_o.' where id='.$groupid;

   $objDB->Conn->Execute($sql);
      
  }
    header("Location:QRcodelist.php");
	exit();	
}

if(isset($_REQUEST['btneditqrcodegroup']))
{
       $array= array();
        $array['group_name'] = $_REQUEST['name'];
	$array['no_of_qrcodes'] = 0;
	//$array['merchant_id'] =  $_REQUEST['merchant_id'];
	$array['active'] = 1;

	$where_clause['id'] = $_REQUEST['group_id'];
	//print_r($array);
	$objDB->Update($array, "qrcode_group", $where_clause);
         header("Location:QRcodegrouplist.php");
	exit();	
}
if(isset($_REQUEST['btn_addnewqrcode']))
{
	
    $sql_super_admin = "select id from qrcode_group where merchant_id=0 and active=1 ";
	$Rs_superadmin_group = $objDB->Conn->Execute($sql_super_admin );
	$admin_group_id = $Rs_superadmin_group->fields['id'];

        
    $array  = array();
    $array ['id']=$admin_group_id;
    $RS = $objDB->Show("qrcode_group",$array );
    $n_o_qrcode = $RS->fields['no_of_qrcodes'];
    $nw_n_o = $n_o_qrcode ;
    
    for($i=0;$i<$_REQUEST['txt_no_qrcode'];$i++)
    {
		
		$data=create_campaign_code();
		
		// start make eps file for 80,120,200,275 size
        
        $filename_eps_80  = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($data)."_80X80.eps";
        $filename_eps_120 = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($data)."_120X120.eps";
        $filename_eps_200 = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($data)."_200X200.eps";
        $filename_eps_275 = UPLOAD_IMG."/m/qrcode/eps/".strtoupper($data)."_275X275.eps";
        
        $url = WEB_PATH."/qr.php?qrcode=".base64_encode(strtoupper($data));
        
		$fname_eps_80  = WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=80'";
		$fname_eps_120 = WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=120'";
		$fname_eps_200 = WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=200'";
		$fname_eps_275 = WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=275'";
		
		//echo $fname_eps_80." ".$filename_eps_80;
		//exit;
		
		passthru("/usr/bin/convert ".$fname_eps_80."  ".$filename_eps_80);
		passthru("/usr/bin/convert ".$fname_eps_120."  ".$filename_eps_120);
		passthru("/usr/bin/convert ".$fname_eps_200."  ".$filename_eps_200);
		passthru("/usr/bin/convert ".$fname_eps_275."  ".$filename_eps_275);
		
		// end make eps file for 80,120,200,275 size
		
		// start make jpeg file for 80,120,200,275 size
		
		$filename_jpg_80  = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($data)."_80X80.jpg";
        $filename_jpg_120 = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($data)."_120X120.jpg";
        $filename_jpg_200 = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($data)."_200X200.jpg";
        $filename_jpg_275 = UPLOAD_IMG."/m/qrcode/jpeg/".strtoupper($data)."_275X275.jpg";
        
        $url = WEB_PATH."/qr.php?qrcode=".base64_encode(strtoupper($data));
        
		$fname_jpg_80  = WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=80'";
		$fname_jpg_120 = WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=120'";
		$fname_jpg_200 = WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=200'";
		$fname_jpg_275 = WEB_PATH."/QR-Generator-PHP-master/php/qr.php?d='".$url ."&size=275'";
		
		//echo $fname_eps_80." ".$filename_eps_80;
		//exit;
		
		passthru("/usr/bin/convert ".$fname_jpg_80."  ".$filename_jpg_80);
		passthru("/usr/bin/convert ".$fname_jpg_120."  ".$filename_jpg_120);
		passthru("/usr/bin/convert ".$fname_jpg_200."  ".$filename_jpg_200);
		passthru("/usr/bin/convert ".$fname_jpg_275."  ".$filename_jpg_275);
		
		// end make jpeg file for 80,120,200,275 size
		
          
        $fn = '';
        $array_qrtag = array();
        $array_qrtag['qrcode'] = $data;
        $array_qrtag['qrcode_image'] = $fn ;
        $array_qrtag['reserve'] = 0 ;
        $array_qrtag['created_date'] = date("Y-m-d H:i:s") ;
        $insertedid = $objDB->Insert($array_qrtag, "qrcodes");
       
        $array_qg = array();
        $array_qg['qrcode_id'] = $insertedid;
        $array_qg['qrcodegroup_id'] = $admin_group_id;
        $objDB->Insert($array_qg, "qrcodegroup_qrcode");
        
        $nw_n_o = $nw_n_o + 1;
         
    }
    
    $sql = 'Update qrcode_group set no_of_qrcodes='.$nw_n_o.' where id='.$admin_group_id;
	$objDB->Conn->Execute($sql);
    $_SESSION['msg'] = "QRcode successfully generated";
    header("location:QRcodelist.php");
     
}

if(isset($_REQUEST['btnAddPressrelease']))
{
	$array=array();      
	$array['title'] = $_REQUEST['title'];
	$array['description'] = $_REQUEST['description'];
	$array['status'] = $_REQUEST['status'];
	$array['release_date'] = $_REQUEST['release_date'];

	$objDB->Insert($array, "press_release");

	$_SESSION['msg'] = "Press Release is added successfully";
	$json_array=array();
	$json_array['status']='true';
	$json_array['message']='Press Release is added successfully';
	$json = json_encode($json_array);

    header("Location:press-release.php");
	exit();	
}
if(isset($_REQUEST['btnCancelPressrelease']))
{
	   header("Location:press-release.php");
	exit();	
}
if(isset($_REQUEST['btnEditPressrelease']))
{
	$array=array();      
	$array['title'] = $_REQUEST['title'];
	$array['description'] = $_REQUEST['description'];
	$array['status'] = $_REQUEST['status'];
	$array['release_date'] = $_REQUEST['release_date'];
	
	$where_clause=array();
	$where_clause['id'] = $_REQUEST['hdn_id'];

	$objDB->Update($array, "press_release", $where_clause);

	$_SESSION['msg'] = "Press Release is updated successfully";
	$json_array=array();
	$json_array['status']='true';
	$json_array['message']='Press Release is updated successfully';
	$json = json_encode($json_array);

    header("Location:press-release.php");
	exit();	
}

if(isset($_REQUEST['btnAddLocationDetail'])){
	
	//print_r($_POST);
	//exit();
	/*
	echo "<pre>";
	print_r($_POST['chk_location']);
	echo "</pre>";
	exit();
	*/
	$mer_id=$_REQUEST['hdn_mer_id'];
	//echo "aaa ".$mer_id." bbb";
	//exit();
	foreach($_POST['chk_location'] as $lid)
	{
		$array = $where_clause = $json_array = array();
		
		if(isset($_POST['chk_dining']))
		{
			$dining="";
			foreach($_POST['chk_dining'] as $dining1)
			{
				$dining.=$dining1.",";
			}
			$dining=substr($dining, 0, -1);
			$array['dining'] = $dining;
		}
		
		if(isset($_POST['rdo_reservation']))
		{
            //$array['reservation'] = $_POST['rdo_reservation'];                       
			if($_POST['rdo_reservation']!="Not Applicable")
                    {
			$array['reservation'] = $_POST['rdo_reservation'];
                    }
                    else
                    {
                        $array['reservation'] = "";
                    }		
		}
		
		if(isset($_POST['rdo_takeout']))
		{
            //$array['takeout'] = $_POST['rdo_takeout'];                        
			if($_POST['rdo_takeout']!="Not Applicable")
                    {
			$array['takeout'] = $_POST['rdo_takeout'];
                    }
                    else
                    {
                        $array['takeout'] = "";
                    }
		}
		
		if(isset($_POST['chk_ambience']))
		{
			$ambience="";
			foreach($_POST['chk_ambience'] as $ambience1)
			{
				$ambience.=$ambience1.",";
			}
			$ambience=substr($ambience, 0, -1);
			$array['ambience'] = $ambience;
		}
		if(isset($_POST['chk_attire']))
		{
			$attire="";
			foreach($_POST['chk_attire'] as $attire1)
			{
				$attire.=$attire1.",";
			}
			$attire=substr($attire, 0, -1);
			$array['attire'] = $attire;
		}
		if(isset($_POST['chk_goodfor']))
		{
			$good_for="";
			foreach($_POST['chk_goodfor'] as $good_for1)
			{
				$good_for.=$good_for1.",";
			}
			$good_for=substr($good_for, 0, -1);
			$array['good_for'] = $good_for;
		}
		if(isset($_POST['chk_payment_method']))
		{
			$payment_method="";
			foreach($_POST['chk_payment_method'] as $payment_method1)
			{
				$payment_method.=$payment_method1.",";
			}
			$payment_method=substr($payment_method, 0, -1);
			$array['payment_method'] = $payment_method;
		}
		
		if(isset($_POST['rdo_pet']))
		{
            //$array['pet'] = $_POST['rdo_pet'];                        
			if($_POST['rdo_pet']!="Not Applicable")
                        {
                            $array['pet'] = $_POST['rdo_pet'];
                        }
                        else
                        {
                            $array['pet'] = "";
                        }
		}
		if(isset($_POST['rdo_wheelchair']))
		{                   
			//$array['wheelchair'] = $_POST['rdo_wheelchair'];                       
			if($_POST['rdo_wheelchair']!="Not Applicable")
                        {
			$array['wheelchair'] = $_POST['rdo_wheelchair'];
                        }
                        else
                        {
                            $array['wheelchair'] = "";
                        } 
		}
		if(isset($_POST['rdo_wifi']))
		{                   
			//$array['wifi'] = $_POST['rdo_wifi'];  
			if($_POST['rdo_wifi']!="Not Applicable")
                        {
			$array['wifi'] = $_POST['rdo_wifi'];
                        }
                        else
                        {
                         $array['wifi'] = "";
                        }
		}
		if(isset($_POST['rdo_tv']))
		{                  
			//$array['has_tv'] = $_POST['rdo_tv'];                       
			if($_POST['rdo_tv']!="Not Applicable")
                        {
			$array['has_tv'] = $_POST['rdo_tv'];
                        }
                        else
                        {
                         $array['has_tv'] = "";
                        }
		}
		if(isset($_POST['rdo_airconditioned']))
		{                   
			//$array['airconditioned'] = $_POST['rdo_airconditioned'];                       
			if($_POST['rdo_airconditioned']!="Not Applicable")
                        {
			$array['airconditioned'] = $_POST['rdo_airconditioned'];
                        }
                        else
                        {
                         $array['airconditioned'] = "";  
                        }
		}
		if(isset($_POST['rdo_smoking']))
		{                  
			//$array['smoking'] = $_POST['rdo_smoking'];                       
			 if($_POST['rdo_smoking']!="Not Applicable")
                        {
			$array['smoking'] = $_POST['rdo_smoking'];
                        }
                        else
                        {
                         $array['smoking'] = "";
                        }
		}
		if(isset($_POST['rdo_alcohol']))
		{                  
			//$array['alcohol'] = $_POST['rdo_alcohol']; 
			if($_POST['rdo_alcohol']!="Not Applicable")
                        {
                        $array['alcohol'] = $_POST['rdo_alcohol'];
                        }
                        else
                        {
                          $array['alcohol'] = "";   
                        }
		}
		if(isset($_POST['rdo_noiselevel']))
		{                   
            //$array['noise_level'] = $_POST['rdo_noiselevel']; 		
			if($_POST['rdo_noiselevel']!="Not Applicable")
                        {
                        $array['noise_level'] = $_POST['rdo_noiselevel'];
                        }
                        else
                        {
                          $array['noise_level'] = "";   
                        }
		}
		if(isset($_POST['minimum_age']))
		{
			$array['minimum_age'] = $_POST['minimum_age'];
		}
		
		if(isset($_POST['rdo_will_deliver']))
		{
			$array['will_deliver'] = $_POST['rdo_will_deliver'];
			if($_POST['rdo_will_deliver']=="Yes")
			{
				$array['deliveryarea_from']=strtoupper($_POST['delivery_from']);
				$array['deliveryarea_to']=strtoupper($_POST['delivery_to']);
			}
			if(isset($_POST['minimum_order']))
			{
				$array['minimum_order']=$_POST['minimum_order'];
			} 
			if($_POST['rdo_will_deliver']!="Not Applicable")
                        {
                            $array['will_deliver'] = $_POST['rdo_will_deliver'];
                        }
						else
						{
							$array['will_deliver'] = "";
						}		
		}
		if(isset($_POST['rdo_caters']))
		{         
			//$array['caters'] = $_POST['rdo_caters'];                  
			if($_POST['rdo_caters']!="Not Applicable")
                    {
			$array['caters'] = $_POST['rdo_caters'];
                    }
                    else
                    {
                        $array['caters'] = "";
                    }
		}
		if(isset($_POST['services']))
		{         
			$array['services'] = $_POST['services'];         
		}
		if(isset($_POST['amenities']))
		{         
			$array['amenities'] = $_POST['amenities'];         
		}
			
		$array['modified_by'] = $_SESSION['merchant_id'];
		
		$where_clause['id'] = $lid;
		$where_clause['created_by'] = $mer_id;
		
		$objDB->Update($array, "locations", $where_clause);
	}
	
                       
	// 369
	$_SESSION['msg'] = $merchant_msg['addlocationdetail']['Msg_location_detail_added'];
	$json_array=array();
	$json_array['status']='true';
	$json_array['message']= $merchant_msg['addlocationdetail']['Msg_location_detail_added'];
	$json = json_encode($json_array);
	header("Location:".WEB_PATH."/admin/merchant_detail.php?id=".$mer_id);
	echo $json;
    exit();
	// 369
}
if(isset($_REQUEST['btnEditLocationDetail'])){

	/*
	echo $_REQUEST['hdn_location_id'];
	print_r($_POST);
	exit();
	*/	

		$lid=$_REQUEST['id'];
		
		$array = $where_clause = $json_array = array();
		
		if(isset($_POST['chk_dining']))
		{
			$dining="";
			foreach($_POST['chk_dining'] as $dining1)
			{
				$dining.=$dining1.",";
			}
			$dining=substr($dining, 0, -1);
			$array['dining'] = $dining;
		}
		if(isset($_POST['chk_ambience']))
		{
			$ambience="";
			foreach($_POST['chk_ambience'] as $ambience1)
			{
				$ambience.=$ambience1.",";
			}
			$ambience=substr($ambience, 0, -1);
			$array['ambience'] = $ambience;
		}
		if(isset($_POST['chk_attire']))
		{
			$attire="";
			foreach($_POST['chk_attire'] as $attire1)
			{
				$attire.=$attire1.",";
			}
			$attire=substr($attire, 0, -1);
			$array['attire'] = $attire;
		}
		if(isset($_POST['chk_goodfor']))
		{
			$good_for="";
			foreach($_POST['chk_goodfor'] as $good_for1)
			{
				$good_for.=$good_for1.",";
			}
			$good_for=substr($good_for, 0, -1);
			$array['good_for'] = $good_for;
		}
		if(isset($_POST['chk_payment_method']))
		{
			$payment_method="";
			foreach($_POST['chk_payment_method'] as $payment_method1)
			{
				$payment_method.=$payment_method1.",";
			}
			$payment_method=substr($payment_method, 0, -1);
			$array['payment_method'] = $payment_method;
		}
		if(isset($_POST['rdo_pet']))
		{
			$array['pet'] = $_POST['rdo_pet'];
		}
		if(isset($_POST['rdo_wheelchair']))
		{
			$array['wheelchair'] = $_POST['rdo_wheelchair'];
		}
		if(isset($_POST['rdo_wifi']))
		{
			$array['wifi'] = $_POST['rdo_wifi'];
		}
		if(isset($_POST['rdo_tv']))
		{
			$array['has_tv'] = $_POST['rdo_tv'];
		}
		if(isset($_POST['rdo_airconditioned']))
		{                  
			$array['airconditioned'] = $_POST['rdo_airconditioned'];                     
		}
		if(isset($_POST['rdo_smoking']))
		{
			$array['smoking'] = $_POST['rdo_smoking'];                      
		}
		if(isset($_POST['rdo_alcohol']))
		{		
			$array['alcohol'] = $_POST['rdo_alcohol'];			
		}
		if(isset($_POST['rdo_noiselevel']))
		{                   
            $array['noise_level'] = $_POST['rdo_noiselevel'];                       			
		}
		if(isset($_POST['minimum_age']))
		{
			$array['minimum_age'] = $_POST['minimum_age'];
		}
		
		if(isset($_POST['rdo_will_deliver']))
		{
			$array['will_deliver'] = $_POST['rdo_will_deliver'];
			if($_POST['rdo_will_deliver']=="Yes")
			{
				$array['deliveryarea_from']=$_POST['delivery_from'];
				$array['deliveryarea_to']=$_POST['delivery_to'];
			}
			if(isset($_POST['minimum_order']))
			{
				$array['minimum_order']=$_POST['minimum_order'];
			}                       
		}
		if(isset($_POST['rdo_caters']))
		{
			$array['caters'] = $_POST['rdo_caters'];
		}
		
		$where_clause['id'] = $lid;
		
		$objDB->Update($array, "locations", $where_clause);

	
                       
	// 369
	$_SESSION['msg'] = $merchant_msg['addlocationdetail']['Msg_location_detail_edited'];
	$json_array=array();
	$json_array['status']='true';
	$json_array['message']= $merchant_msg['addlocationdetail']['Msg_location_detail_edited'];
	$json = json_encode($json_array);
	header("Location:".WEB_PATH."/admin/location-detail.php?id=".$lid);
	echo $json;
    exit();
	// 369
}
if(isset($_REQUEST['btnupdatetimezone']))
{
$location_array = $_REQUEST['chk'];
for($i=0;$i<count($location_array);$i++)
{
		//echo "http://api.timezonedb.com/?zone=".$location_array[$i]."&key=".timezonedb_apikey."&format=json";
   	    $geocode= file_get_contents("http://api.timezonedb.com/?zone=".$location_array[$i]."&key=".timezonedb_apikey."&format=json");
		$geojson= json_decode($geocode,true);	
		//print_r($geojson);
		if($geojson['dst'] == 1)
		{
			$timezone_name = $geojson['zoneName'];
			$zonehouroffset = timezone_offsethour_string($geojson['gmtOffset']).",0";
			//echo $Row['id']."===old timezone-".$Row['timezone_name']."-".$Row['timezone']."===".$Row['latitude']."===".$Row['longitude']."==".timezone_offsethour_string($geojson['gmtOffset'])."=new time zone".$geojson['zoneName']."<br/>";
		}
		else
		{
			$timezone_name = $geojson['zoneName'];
			$zonehouroffset = getStandardOffsetUTC($geojson['zoneName'],$geojson['gmtOffset']).",0";
			//echo $Row['id']."===old timezone-".$Row['timezone_name']."-".$Row['timezone']."===".$Row['latitude']."===".$Row['longitude']."==".getStandardOffsetUTC($geojson['zoneName'],$geojson['gmtOffset'])."=new time zone".$geojson['zoneName']."<br/>";
		} 
		$sql = "update locations set timezone_name='".$timezone_name."' , timezone='".$zonehouroffset."' , is_dst='".$geojson['dst']."' where timezone_name='".$location_array[$i]."'";
		//echo $sql;
		$objDB->Conn->Execute($sql); 
		
}
$_SESSION['msg'] = "Timezone updated successfully";
		header("Location:".WEB_PATH."/admin/update-location-timezone-list.php");		
} 
if(isset($_REQUEST['reset_timezonedst']))
{

try{
	$sql = "select * from locations limit ".$_REQUEST['startlimit'].",".$_REQUEST['endlimit'];
	//echo $sql;
	$RS = $objDB->Conn->Execute($sql);  
	while($Row = $RS->FetchRow()){
	
    $geocode= file_get_contents("http://api.timezonedb.com/?lat=".$Row['latitude']."&lng=".$Row['longitude']."&key=".timezonedb_apikey."&format=json");
	$geojson= json_decode($geocode,true);	
	//print_r($geojson);
		if($geojson['dst'] == 1)
		{
			$timezone_name = $geojson['zoneName'];
			$zonehouroffset = timezone_offsethour_string($geojson['gmtOffset']).",0";
			//echo $Row['id']."===old timezone-".$Row['timezone_name']."-".$Row['timezone']."===".$Row['latitude']."===".$Row['longitude']."==".timezone_offsethour_string($geojson['gmtOffset'])."=new time zone".$geojson['zoneName']."<br/>";
		}
		else
		{
			$timezone_name = $geojson['zoneName'];
			$zonehouroffset = getStandardOffsetUTC($geojson['zoneName'],$geojson['gmtOffset']).",0";
			//echo $Row['id']."===old timezone-".$Row['timezone_name']."-".$Row['timezone']."===".$Row['latitude']."===".$Row['longitude']."==".getStandardOffsetUTC($geojson['zoneName'],$geojson['gmtOffset'])."=new time zone".$geojson['zoneName']."<br/>";
		} 
		$sql = "update locations set timezone_name='".$timezone_name."' , timezone='".$zonehouroffset."' , is_dst='".$geojson['dst']."' where id=".$Row['id'];
		$objDB->Conn->Execute($sql);
		
	}
	}catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	} 
	echo $_REQUEST['endlimit']." records successfully updated";
}
// get UTC/GMT timezone from offset

function timezone_offsethour_string( $offset )
{
       return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
}
function getStandardOffsetUTC($timezone,$offset)
{
if($timezone == 'UTC') {
	return '';
} else {
	$timezone = new DateTimeZone($timezone);
	$transitions = array_slice($timezone->getTransitions(), -3, null, true);

	foreach (array_reverse($transitions, true) as $transition)
	{
		if ($transition['isdst'] == 1)
		{
			continue;
		}

		return sprintf('%+03d:%02u', $offset / 3600, abs($offset) % 3600 / 60);
	}

	return false;

}
} 
/*** get all unique timzone of system for update timezone ****/
if(isset($_REQUEST['unique_timzone_list']))
{
	$Sql = "select timezone , timezone_name from locations where is_dst =1 group by timezone_name";
	 $RS = $objDB->Conn->Execute($Sql);
	 if($RS->RecordCount()>0)
	 {
		  $json_array['status'] = "true";
		  $json_array['total_records'] = $RS->RecordCount();
		  $count=0;
		  while($Row = $RS->FetchRow())
		  {
		   	$records[$count] = get_field_value($Row);
			//$active_campaign_status[(string)$Row['id']] = "active";
		   	$count++;
		  }
		  $json_array["records"]= $records;
		 // $json_array["active_campaign_status"]=$active_campaign_status;
	 }
	 else
	 {
		  $json_array['status'] = "false";
		  $json_array['total_records'] = 0;
		  $json = json_encode($json_array);
		  echo $json;
		  exit();
	 }
//	 print_r($json_array);
	 $json = json_encode($json_array);
	 echo $json;
	 exit();
	
}
/*** get all unique timzone of system for update timezone ****/

if(isset($_REQUEST["btnInviteCustomers"]))
{
	$json_array = array();
	  
	//$objDB = new DB();
	
	
	$id = $_REQUEST['merchant_id'];
	$limit = $_REQUEST['num_of_cust'];
	
	if( $limit >0 )
	{
		//echo $id;
		
		$Sql = "SELECT * from merchant_user WHERE id=".$id;
		$RS = $objDB->Conn->Execute($Sql);
		if($RS->RecordCount()>0)
		{
			 $Row = $RS->FetchRow();
		}
		$mechant_business = $Row['business'];
		$business_icon = ASSETS_IMG."/m/icon/".$Row['merchant_icon'];

	   
		//$where_arr= array();
		//$RS = $objDB->Show("press_release",$where_arr);
		/*
		$Sql_new = "select distinct cu.id,cu.firstname,cu.lastname,cu.emailaddress,cu.active,cu.is_registered,cu.opt_in
		from merchant_subscribs ms,merchant_user mu,merchant_groups mg,customer_user cu,locations l
		where mu.id=ms.merchant_id and ms.group_id = mg.id and mu.id=mg.merchant_id and ms.user_id =cu.id and cu.active=1 and mu.id=l.created_by  and mg.location_id=l.id and l.active=1 and mu.id=".$id ." and cu.is_registered!='1' and cu.opt_in!='0'";
		*/
		$Sql_new = "select distinct cu.id,cu.firstname,cu.lastname,cu.emailaddress,cu.active,cu.is_registered,cu.opt_in,cic.invite_counter
	from merchant_subscribs ms,merchant_user mu,merchant_groups mg,customer_user cu,locations l,customer_invite_counter cic
	where mu.id=ms.merchant_id and ms.group_id = mg.id and mu.id=mg.merchant_id and ms.user_id =cu.id 
	and mu.id=l.created_by and mg.location_id=l.id and l.active=1 and mu.id=".$id." and cu.is_registered!='1' and cu.opt_in!='0' and cic.customer_id=cu.id ORDER BY cic.invite_counter limit ".$limit;
	
		$Sql_new = "select cu.id,cu.firstname,cu.lastname,cu.emailaddress,cu.active,cu.is_registered,cu.opt_in,(select cic.invite_counter from customer_invite_counter cic where cic.customer_id=cu.id and cic.merchant_id=".$id.") 'invite_counter'
from  customer_user cu
where 
cu.id in (
       select distinct cu.id
	from merchant_subscribs ms,merchant_user mu,merchant_groups mg,customer_user cu,locations l,customer_invite_counter cic
	where mu.id=ms.merchant_id and ms.group_id = mg.id and mu.id=mg.merchant_id and ms.user_id =cu.id 
	and mu.id=l.created_by and mg.location_id=l.id and l.active=1 and mu.id=".$id." and cu.is_registered!='1' and cu.opt_in!='0' and cic.customer_id=cu.id 
   ) 
ORDER BY invite_counter limit ".$limit;
	
		//echo $Sql_new;

		$RS = $objDB->Conn->Execute($Sql_new);
		//echo "records count = ".$RS->RecordCount();
		if($RS->RecordCount()>0)
		{
			while($Row = $RS->FetchRow())
			{
				//echo $Row['id']." ".$Row['firstname']." ".$Row['lastname']." ".$Row['emailaddress']." ".$Row['active']." ".$Row['is_registered']." ".$Row['opt_in']."<br/>";
				
							
				$body='<body bgcolor="#e4e4e4" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" style="-webkit-font-smoothing: antialiased;width:100% !important;background:#e4e4e4;-webkit-text-size-adjust:none;">

	<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#e4e4e4">
	<tr>
		<td bgcolor="#e4e4e4" width="100%">
			<table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="table"
				style="background: #D2D2D2; border: 2px solid #ddd; padding: 20px; border-radius: 10px;">
				<tr>
					<td width="600" class="cell">
						<table width="600" cellpadding="0" cellspacing="0" border="0" class="table">
							<tr>
								<td width="250" class="logocell">
									<img src="'.ASSETS_IMG.'/c/scanflip-logo.png" width="205" height="30" alt="Scanflip"
										style="-ms-interpolation-mode: bicubic; padding: 20px 0;">
								</td>
							</tr>
						</table>
						<repeater>
			<layout label="New feature">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td bgcolor="#F37E0A" nowrap><img border="0" src="'.ASSETS_IMG.'/m/spacer.gif" width="5" height="1"></td>
				<td width="100%" bgcolor="#ffffff">
			
					<table width="100%" cellpadding="20" cellspacing="0" border="0">
					<tr>
						<td valign="top" style="padding:15px 0px 0px 15px;">
							<img style="border:0" src="'.$business_icon.'">
						</td>
						<td bgcolor="white" class="contentblock">
							<h4 style="color:black; font-size:16px; line-height:24px; font-family:Helvetica Neue,Helvetica,Arial,sans-serif; margin-top:0; margin-bottom:10px; padding-top:0; padding-bottom:0; font-weight:normal;"><strong><singleline label="Title">Hi '.$Row['firstname'].',</singleline></strong></h4>
							<multiline label="Description">
							<p style=" color: black; font-family:Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; font-weight: normal; line-height: 19px; margin-bottom: 12px; margin-top: 0; padding-bottom: 0; padding-top: 0;">
							'.$mechant_business.'  would like to invites you to browse, share and redeem campaigns on Scanflip.
			Download scanflip App for your <a href="#">iphone</a> and <a href="#">android</a> devices.</p>
							<p >
							<h5 style="margin-bottom:0px;"><a href="'.WEB_PATH.'/register.php" style=" background: none repeat scroll 0 0 #F37E0A; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; border-radius: 3px; color: #FFFFFF; font-size: 14px; padding: 5px 15px; text-align: center; text-decoration: none;">Confirm Request</a></h5>
							</p>
							</multiline>
							<h5><p style=" color: black; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; font-weight: normal; line-height: 19px; margin-bottom: 12px; margin-top: 0; padding-bottom: 0; padding-top: 0;">Thank You,<br/>Scanflip Support Team.</p></h5>
						</td>
					</tr>
					<tr>
							<td colspan="2">
							<table border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse;width:620px">

	<tbody><tr><td style="font-size:11px;font-family:lucida grande,tahoma,verdana,arial,sans-serif;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;color:#999999;border:none">

	If you don\'t want to receive these emails from Scanflip in the future, please <a href="'.WEB_PATH.'/process.php?btnunsubscribecustomer=yes&id='.$Row['id'].'" style="color:#3b5998;text-decoration:none" target="_blank">unsubscribe</a>.<br>

	Scanflip Corp , 21133 Victory Blvd Suite# 201 Los Angeles , CA 91303</td></tr></tbody></table>	
							</td>
						</tr>
					</table>
				</td>
			</tr>
			</table>  
			</layout>
		</repeater>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>

	</body>';
				//echo $body;
				
				
				$user_id = $Row['id'];
					
				$mail = new PHPMailer();
				$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
				$mail->AddAddress($Row['emailaddress']);
				$mail->From = "no-reply@scanflip.com";
				$mail->FromName = "ScanFlip Support";
				$mail->Subject    = $mechant_business." invites you on Scanflip";
				$mail->MsgHTML($body);
				//echo $body;
				$mail->Send();
					
				$Sql = "update customer_invite_counter set invite_counter=invite_counter + 1 where customer_id=".$user_id." and merchant_id=".$id;
				$RS_cic = $objDB->Conn->Execute($Sql);
					
				$user_id = $Row['id'];
				$array_cust_invite = array();
				$array_cust_invite['customer_id'] = $user_id;
				$array_cust_invite['merchant_id'] = $id;
				$RS_cust_invite = $objDB->Show("customer_invite_counter",$array_cust_invite);
				
				if($RS_cust_invite->RecordCount()>0)
				{
						if($RS_cust_invite->fields['invite_counter']>=3)
						{
							$Sql = "update customer_user set opt_in=0 where id=".$user_id;
							$RS_cu = $objDB->Conn->Execute($Sql);
						}
				}	
			}
			
			$json_array['status'] = "true";
			$json_array['total_records'] = $RS->RecordCount();
			$json = json_encode($json_array);
			echo $json;
			exit();
			  
		}
	}
	else
	{
		$json_array['status'] = "false";
		//$json_array['message'] = "Please add number of customer greater than zero";
		$json = json_encode($json_array);
		echo $json;
		exit();
	}
	
}

if(isset($_REQUEST["generate_campaign_sitemap"]))
{
	file_get_contents(WEB_PATH."/create_sitemap_campaign.php");
	$json_array = array();
	$json_array['status'] = "true";
	$json = json_encode($json_array);
	echo $json;
	exit();
}
if(isset($_REQUEST["generate_location_sitemap"]))
{
	file_get_contents(WEB_PATH."/create_sitemap_location.php");
	$json_array = array();
	$json_array['status'] = "true";
	$json = json_encode($json_array);
	echo $json;
	exit();
}
if(isset($_REQUEST["generate_static_sitemap"]))
{
	file_get_contents(WEB_PATH."/create_sitemap_static.php");
	$json_array = array();
	$json_array['status'] = "true";
	$json = json_encode($json_array);
	echo $json;
	exit();
}
if(isset($_REQUEST["generate_locationkml_sitemap"]))
{
	file_get_contents(WEB_PATH."/create_sitemap_geositemap.php");
	$json_array = array();
	$json_array['status'] = "true";
	$json = json_encode($json_array);
	echo $json;
	exit();
}
if(isset($_REQUEST["generate_index_sitemap"]))
{
	file_get_contents(WEB_PATH."/create_sitemap_index.php");
	$json_array = array();
	$json_array['status'] = "true";
	$json = json_encode($json_array);
	echo $json;
	exit();
}
if(isset($_REQUEST['addordernote']))
{
	$array = array();
	$array['order_note'] = $_REQUEST['note'];
	$where_array['id'] = $_REQUEST['order_id'];
	$objDB->Update($array, "giftcard_order",$where_array);	
	echo "Note added Successfully";	
}
/*********** Get order detail ******************/
if(isset($_REQUEST["addordeDetail"]))
{
echo WEB_PATH.'/process.php?getgiftcardinformation=yes&giftcardid='.$_REQUEST['giftcard_id'];
	$json_array = array();
	?>
	 <div class="div_card">
					   
					   <h2>Order Detail</h2>
					   <?php
							$arr_loc=file(WEB_PATH.'/process.php?getgiftcardinformation=yes&giftcardid='.$_REQUEST['giftcardid']);
							if(trim($arr_loc[0]) == "")
							{
								unset($arr_loc[0]);
								$arr_loc = array_values($arr_loc);
							}
							$all_json_str_loc = $arr_loc[0];
							$json_loc = json_decode($arr_loc[0]);
							$total_records1_loc = $json_loc->total_records;
							$records_array1_loc = $json_loc->records; 
					   ?>
					   <?php if($total_records1_loc>0)
							{
								foreach($records_array1_loc as $Row_loc)
								{?>
							<li>
									<ul class="displayAddressUL">
										<li class="displayAddressLI displayAddressFullName"><b><?php  echo  $Row_loc->title; ?></b></li>
										<li class="displayAddressLI displayAddressAddressLine1"><span>Redeem Points Required:</span><?php  echo  $Row_loc->redeem_point_value; ?></li>
										<li class="displayAddressLI displayAddressAddressnote"><span>Please check your order carefully while redeeming your Scanflip points. Scanflip is unable to cancel orders for gift cards. Please allow up to 4 weeks for delivery of your order. <p>Deliveries cannot be made to a Post Office Box, rural routes, or to addresses outside of customer residential country.</span></li>
									
										
									</ul>
								</li>
								<?php } } ?>
					   
					   </div>
					   <?php
	$json_array['status'] = "true";
	$json = json_encode($json_array);
	//echo $json;
	exit();
}
if(isset($_REQUEST['addnextorders']))
{
	$sql = "select o.* , c.firstname , c.lastname , g.title  ,g.redeem_point_value , g.card_value from	
		giftcard_order o inner join customer_user c on c.id = o.user_id inner join giftcards g on g.id = o.giftcard_id  order by o.id desc  limit ".$_REQUEST['sartlimit'].",5";
	$RS = $objDB->Conn->Execute($sql);
			  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="left"><a href="javascript:void(0)" class="order_detail" data-orderid="<?=$Row['id']?>" data-giftcardid="<?=$Row['giftcard_id']?>"><?=$Row['order_number']?></a>
			<div style="display:none" class="orderdetail_td">
			<div class="fancybox_order_detail_wrapper">
		<div id="ship_to">
			<span class="fancybox_orderpopup_title">Ship to :</span> <span id="address"><?php echo $Row['ship_to_address'] ; ?></span>
		</div>
		<div id="order_detail">
			<div id="reward_detail">
				
				<span class="fancybox_orderpopup_title">Reward Detail :</span> <span id="reward_dtail"><?php echo $Row['title']; ?> </span>
			</div>
			<div id="reward_value">
				<span class="fancybox_orderpopup_title">Reward Value :</span> <span id="reward_vlue">$<?php echo $Row['redeem_point_value']; ?></span>
			</div>
			<div id="point_redeemed">
				<span class="fancybox_orderpopup_title">Points Redeemed :</span> <span id="point_redeem"><?php echo $Row['card_value']; ?></span>
			</div>
		</div>
		<div id="notes">
			<span class="fancybox_orderpopup_title">Notes :</span> <span class="note" id="ordernote_<?php echo $Row['id']; ?>">
			<?php 
			$note = $Row['order_note'];
			if(strlen($note)>413)
			{
				$notes = substr($note, 0, 413).'...';
			}
			echo $notes; ?></span>
		</div>
	</div>
	</div>
			</td>
			<td align="left"><?=$Row['order_date']?></td>
			<td align="left"><?=$Row['title']?></td>
			
			<td align="left"><?php
			if($Row['status'] == 0)
			{
				echo "Cancelled";
			}
			else if($Row['status'] == 2)
			{
				echo "pending";
			}
			else 
			{
				echo "Shipped";
			}
			?></td>
		    <td align="left">
                        <?php
			if($Row['status'] == 0)
			{
			
			}
			else if($Row['status'] == 2)
			{
			?>
			  <a class="change_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Change Status</a><span>&nbsp;|&nbsp;</span>
			  <a class="cancel_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Cancel Order</a><span>&nbsp;|&nbsp;</span>
              <?php } else { ?>        
			  <a class="change_st"  href="javascript:void(0);" data-orderstatus="2" data-orderid="<?=$Row['id']?>">Change Status</a><span>&nbsp;|&nbsp;</span>
			   <a class="cancel_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Cancel Order</a><span>&nbsp;|&nbsp;</span>
			  <?php  } ?>
                          <a href="javascript:void(0)" odr_id="<?php echo $Row['id']; ?>" class="addNote">Note</a>
                    </td> 
		  </tr>
		  <?
		  }
		} 
}
if(isset($_REQUEST['change_order_status']))
{
	$array_values = $where_clause = array();
	$array_values['status'] = $_REQUEST['status'];
    $where_clause['id'] = $_REQUEST['order_id'];
	$objDB->Update($array_values, "giftcard_order", $where_clause);
}
if(isset($_REQUEST['filter_giftcardorder']))
{
?>

<?php
$month =$_REQUEST['month'];
if ($month == 4 || $month == 6 || $month == 9 || $month == 11) {
//echo "in if";
            $m = 30;
        } else if ($month == 2) {
		//echo "in else if";
            $m = 28;
        } else {
		//echo "in else";
            $m = 31;
        }
		if($_REQUEST['year'] != 0)
		{
			$year =$_REQUEST['year'];
			if($month != 0)
			{
				$from_date = $year . "-" . $month . "-01 00:00:00";
				$to_date = $year . "-" . $month . "-" . $m . " 23:59:59";
				$ordred_date =  " where (o.order_date between '".$from_date."' and '".$to_date."')";
			
			}
			else
			{
				$from_date = $year . "-01-01 00:00:00";
				$to_date = $year . "-12-" . $m . " 23:59:59";
				$ordred_date =  " where (o.order_date between '".$from_date."' and '".$to_date."')";
			
			}
		}
		else
		{
			$redeem_date= "";
		}
		$var = $_REQUEST['status_act'];
	if($var >= 0)
	{
		if($ordred_date == "")
		{
			$ordred_status =  " where (o.status = ".$var.") ";
		}
		else
		{
			$ordred_status =  " and (o.status = ".$var.")";
		}
	}
	else{
		$ordred_status =  "";
	}
	$var = $_REQUEST['shipto'];
	if($var != "0")
	{
	 	if($ordred_status == "" && ordred_date == "")
		{
			$order_ship_to = " where g.ship_to ='".$var."' ";
		}
		else{
			$order_ship_to = " and g.ship_to ='".$var."' ";
		}
	 }
	else{
		$order_ship_to = "";
	}
	$sql = "select o.* , c.firstname , c.lastname , g.title  ,g.redeem_point_value , g.card_value from	
		giftcard_order o inner join customer_user c on c.id = o.user_id inner join giftcards g on g.id = o.giftcard_id  ".$ordred_date." ".$ordred_status." ".$order_ship_to."   order by o.id desc  limit ".$_REQUEST['sartlimit'].",".$language_msg['common']['bydefalt_entries_per_query'];;
	$sql_total = "select IFNULL(sum(g.card_value),0) total_card_value , IFNULL(sum(g.redeem_point_value),0) total_redeem_value from	
		giftcard_order o inner join customer_user c on c.id = o.user_id inner join giftcards g on g.id = o.giftcard_id  ".$ordred_date." ".$ordred_status." ".$order_ship_to."  and o.status <>0    order by o.id desc";
//	echo $sql;
	$RS = $objDB->Conn->Execute($sql);
	$RS_values = $objDB->Conn->Execute($sql_total);
	  if($RS->RecordCount()>0){
	?>
	<div class="orderpoints">
			<div><b>Total card value redeemed : </b> <?php echo $RS_values->fields['total_card_value'] ;?></div>
		<div><b>Total scanflip points redeemed : </b> <?php echo $RS_values->fields['total_redeem_value'] ;  ?></div>
		</div>
		<?php }
		else{
		?>
		<div  class="orderpoints">
			<div><b>Total card value redeemed : </b>0</div>
		<div><b>Total scanflip points redeemed : </b>0</div>
		</div>
		<?php
		}
		?>
		
 <table  width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
		  <thead>
		  <input type="hidden" name="total_pages" id="total_pages" value="" />
		  <input type="hidden" name="current_page" id="current_page" value="" />
		  
		  <tr>
			<th width="10%" align="left">Order No.</th>
			<th width="10%" align="left">Order Date</th>
			<th width="20%" align="left">Order Description</th>
			<th width="8%" align="left">Status</th>
			<th width="20%" align="left">&nbsp;</th>
		  </tr>
		  </thead>
		  <tbody>
	
	<?php
	
			  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		 
		  <tr>
			<td align="left"><a href="javascript:void(0)" class="order_detail" data-orderid="<?=$Row['id']?>" data-giftcardid="<?=$Row['giftcard_id']?>"><?=$Row['order_number']?></a>
			<div style="display:none" class="orderdetail_td">
			<div class="fancybox_order_detail_wrapper">
		<div id="ship_to">
			<span class="fancybox_orderpopup_title">Ship to :</span> <span id="address"><?php echo $Row['ship_to_address'] ; ?></span>
		</div>
		<div id="order_detail">
			<div id="reward_detail">
				
				<span class="fancybox_orderpopup_title">Reward Detail :</span> <span id="reward_dtail"><?php echo $Row['title']; ?> </span>
			</div>
			<div id="reward_value">
				<span class="fancybox_orderpopup_title">Reward Value :</span> <span id="reward_vlue">$<?php echo $Row['redeem_point_value']; ?></span>
			</div>
			<div id="point_redeemed">
				<span class="fancybox_orderpopup_title">Points Redeemed :</span> <span id="point_redeem"><?php echo $Row['card_value']; ?></span>
			</div>
		</div>
		<div id="notes">
			<span class="fancybox_orderpopup_title">Notes :</span> <span class="note" id="ordernote_<?php echo $Row['id']; ?>">
			<?php 
			$note = $Row['order_note'];
			if(strlen($note)>413)
			{
				$notes = substr($note, 0, 413).'...';
			}
			echo $notes; ?></span>
		</div>
	</div>
	</div>
			</td>
			<td align="left"><?=$Row['order_date']?></td>
			<td align="left"><?=$Row['title']?></td>
			
			<td align="left"><?php
			if($Row['status'] == 0)
			{
				echo "Cancelled";
			}
			else if($Row['status'] == 2)
			{
				echo "pending";
			}
			else 
			{
				echo "Shipped";
			}
			?></td>
		    <td align="left">
                        <?php
			if($Row['status'] == 0)
			{
			
			}
			else if($Row['status'] == 2)
			{
			?>
			  <a class="change_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Change Status</a><span>&nbsp;|&nbsp;</span>
			  <a class="cancel_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Cancel Order</a><span>&nbsp;|&nbsp;</span>
              <?php } else { ?>        
			  <a class="change_st"  href="javascript:void(0);" data-orderstatus="2" data-orderid="<?=$Row['id']?>">Change Status</a><span>&nbsp;|&nbsp;</span>
			   <a class="cancel_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Cancel Order</a><span>&nbsp;|&nbsp;</span>
			  <?php  } ?>
                          <a href="javascript:void(0)" odr_id="<?php echo $Row['id']; ?>" class="addNote">Note</a>
                    </td> 
		  </tr>
		  <?
		  }
		} 
	?>
	</table>
	<?php
}
if(isset($_REQUEST['addnextorderss']))
{

$month =$_REQUEST['month'];
if ($month == 4 || $month == 6 || $month == 9 || $month == 11) {
//echo "in if";
            $m = 30;
        } else if ($month == 2) {
		//echo "in else if";
            $m = 28;
        } else {
		//echo "in else";
            $m = 31;
        }
		if($_REQUEST['year'] != 0)
		{
			$year =$_REQUEST['year'];
			if($month != 0)
			{
				$from_date = $year . "-" . $month . "-01 00:00:00";
				$to_date = $year . "-" . $month . "-" . $m . " 23:59:59";
				$ordred_date =  " where (o.order_date between '".$from_date."' and '".$to_date."')";
			
			}
			else
			{
				$from_date = $year . "-01-01 00:00:00";
				$to_date = $year . "-12-" . $m . " 23:59:59";
				$ordred_date =  " where (o.order_date between '".$from_date."' and '".$to_date."')";
			
			}
		}
		else
		{
			$redeem_date= "";
		}
	$var = $_REQUEST['status_act'];
	if($var >= 0)
	{
	
		if($ordred_date == "")
		{
			$ordred_status =  " where (o.status = ".$var.") ";
		}
		else
		{
			$ordred_status =  " and (o.status = ".$var.")";
		}
	}
	else{
		$ordred_status =  "";
	}
	$var = $_REQUEST['shipto'];
	if($var != "0")
	{
	 	if($ordred_status == "" && ordred_date == "")
		{
			$order_ship_to = " where g.ship_to ='".$var."' ";
		}
		else{
			$order_ship_to = " and g.ship_to ='".$var."' ";
		}
	 }
	else{
		$order_ship_to = "";
	}
	$sql = "select o.* , c.firstname , c.lastname , g.title  ,g.redeem_point_value , g.card_value from	
		giftcard_order o inner join customer_user c on c.id = o.user_id inner join giftcards g on g.id = o.giftcard_id  ".$ordred_date." ".$ordred_status."  ".$order_ship_to."  order by o.id desc  limit ".$_REQUEST['sartlimit'].",".$language_msg['common']['bydefalt_entries_per_query'];
	$RS = $objDB->Conn->Execute($sql);
	if($_REQUEST['load_from_scrtch']  == 1)
	{
		$sql_total = "select sum(g.card_value) total_card_value , sum(g.redeem_point_value) total_redeem_value  from	
		giftcard_order o inner join customer_user c on c.id = o.user_id inner join giftcards g on g.id = o.giftcard_id  ".$ordred_date." ".$ordred_status."  ".$order_ship_to."  and o.status <>0  order by o.id desc";
//	echo $sql;
	$RS = $objDB->Conn->Execute($sql);
	$RS_values = $objDB->Conn->Execute($sql_total);
	  if($RS->RecordCount()>0){
	?>
	<div  class="orderpoints">
			<div><b>Total card value redeemed : </b> <?php echo $RS_values->fields['total_card_value'] ;?></div>
		<div><b>Total scanflip points redeemed : </b> <?php echo $RS_values->fields['total_redeem_value'] ;  ?></div>
		</div>
		<?php }
		else{
		?>
		<div class="orderpoints">
			<div><b>Total card value redeemed : </b>0</div>
			<div><b>Total scanflip points redeemed : </b>0</div>
		</div>
		<?php
		}
		?>
		 <table  width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
		  <thead>
		  <input type="hidden" name="total_pages" id="total_pages" value="" />
		  <input type="hidden" name="current_page" id="current_page" value="" />
		  
		  <tr>
			<th width="10%" align="left">Order No.</th>
			<th width="10%" align="left">Order Date</th>
			<th width="20%" align="left">Order Description</th>
			<th width="8%" align="left">Status</th>
			<th width="20%" align="left">&nbsp;</th>
		  </tr>
		  </thead>
		  <tbody>
		<?php
	
	}
	else{
		
	}
			  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		 
		  <tr>
			<td align="left"><a href="javascript:void(0)" class="order_detail" data-orderid="<?=$Row['id']?>" data-giftcardid="<?=$Row['giftcard_id']?>"><?=$Row['order_number']?></a>
			<div style="display:none" class="orderdetail_td">
			<div class="fancybox_order_detail_wrapper">
		<div id="ship_to">
			<span class="fancybox_orderpopup_title">Ship to :</span> <span id="address"><?php echo $Row['ship_to_address'] ; ?></span>
		</div>
		<div id="order_detail">
			<div id="reward_detail">
				
				<span class="fancybox_orderpopup_title">Reward Detail :</span> <span id="reward_dtail"><?php echo $Row['title']; ?> </span>
			</div>
			<div id="reward_value">
				<span class="fancybox_orderpopup_title">Reward Value :</span> <span id="reward_vlue">$<?php echo $Row['redeem_point_value']; ?></span>
			</div>
			<div id="point_redeemed">
				<span class="fancybox_orderpopup_title">Points Redeemed :</span> <span id="point_redeem"><?php echo $Row['card_value']; ?></span>
			</div>
		</div>
		<div id="notes">
			<span class="fancybox_orderpopup_title">Notes :</span> <span class="note" id="ordernote_<?php echo $Row['id']; ?>">
			<?php 
			$note = $Row['order_note'];
			if(strlen($note)>413)
			{
				$notes = substr($note, 0, 413).'...';
			}
			echo $notes; ?></span>
		</div>
	</div>
	</div>
			</td>
			<td align="left"><?=$Row['order_date']?></td>
			<td align="left"><?=$Row['title']?></td>
			
			<td align="left"><?php
			if($Row['status'] == 0)
			{
				echo "Cancelled";
			}
			else if($Row['status'] == 2)
			{
				echo "pending";
			}
			else 
			{
				echo "Shipped";
			}
			?></td>
		    <td align="left">
                        <?php
			if($Row['status'] == 0)
			{
			
			}
			else if($Row['status'] == 2)
			{
			?>
			  <a class="change_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Change Status</a><span>&nbsp;|&nbsp;</span>
			  <a class="cancel_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Cancel Order</a><span>&nbsp;|&nbsp;</span>
              <?php } else { ?>        
			  <a class="change_st"  href="javascript:void(0);" data-orderstatus="2" data-orderid="<?=$Row['id']?>">Change Status</a><span>&nbsp;|&nbsp;</span>
			   <a class="cancel_st" href="javascript:void(0);" data-orderstatus="1" data-orderid="<?=$Row['id']?>">Cancel Order</a><span>&nbsp;|&nbsp;</span>
			  <?php  } ?>
                          <a href="javascript:void(0)" odr_id="<?php echo $Row['id']; ?>" class="addNote">Note</a>
                    </td> 
		  </tr>
		  <?
		  }
		} 
		if($_REQUEST['load_from_scrtch']  == 1)
	{
	?>
	</tbody>
	</table>
	<?php
	}
	
}
if(isset($_REQUEST['btngetpaymenthistory']))
{
    $sql="select ppo.date,ppo.order_id,ppo.amount,ppo.refrence_number,mu.business from purchase_point_order ppo,merchant_user mu where mu.id=ppo.merchant_id  order by ppo.date desc limit 0,".$language_msg['common']['bydefalt_entries_per_query'];
    $RS = $objDB->execute_query($sql);
    
	if($RS->RecordCount()>0)
	{
		$json_array['status'] = "true";
		$count=0;
		while($Row = $RS->FetchRow())
		{
			$records[$count] = get_field_value($Row);
			$count++;
		}
		$json_array["records"]= $records;
		$json_array['total_records'] = $count;       
		$json = json_encode($json_array);
		echo $json;
		return $json;
	}
	else
	{
		$json_array['status'] = "false"; 
		$json_array['total_records'] = 0;  		
		$json = json_encode($json_array);
		echo $json;
		return $json;
	}
}
if(isset($_REQUEST['filter_purchasepoint_order']))
{
	$month =$_REQUEST['month'];
	$year =$_REQUEST['year'];
	$business =$_REQUEST['business'];
	
	if ($month == 4 || $month == 6 || $month == 9 || $month == 11) 
	{
		//echo "in if";
		$m = 30;
	}
	else if ($month == 2) 
	{
		//echo "in else if";
		$m = 28;
	}
	else
	{
		//echo "in else";
		$m = 31;
	}
	if($_REQUEST['year'] != 0)
	{
		if($month != 0)
		{
			$from_date = $year . "-" . $month . "-01 00:00:00";
			$to_date = $year . "-" . $month . "-" . $m . " 23:59:59";
			$ordred_date =  " and (ppo.date between '".$from_date."' and '".$to_date."')";
		}
		else
		{
			$from_date = $year . "-01-01 00:00:00";
			$to_date = $year . "-12-" . $m . " 23:59:59";
			$ordred_date =  " and (ppo.date between '".$from_date."' and '".$to_date."')";
		}
	}
	
	$busnase = "";
	
	if($business==0)
	{
		$busnase ="";
	}
	else
	{
		$busnase =" and mu.id=".$business;
	}
	
	$sql="select ppo.date,ppo.order_id,ppo.amount,ppo.refrence_number,mu.business from purchase_point_order ppo,merchant_user mu where 
	    mu.id=ppo.merchant_id ".$ordred_date.$busnase."  order by ppo.date desc limit ".$_REQUEST['sartlimit'].",".$language_msg['common']['bydefalt_entries_per_query'];
	
	$RS = $objDB->Conn->Execute($sql);	 
		?>
		
	<table  width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
		<thead>
		  <input type="hidden" name="total_pages" id="total_pages" value="" />
		  <input type="hidden" name="current_page" id="current_page" value="" />
		  
			<tr>
				<th width="15%" align="left">Order Date</th>
				<th width="10%" align="left">Order Number</th>
				<th width="20%" align="left">Merchant Name</th>
				<th width="10%" align="left">Amount</th>
				<th width="10%" align="left">Reference Number</th>
			</tr>
		</thead>
		<tbody>
		  <?
		    if($RS->RecordCount()>0)
			{
				while($Row = $RS->FetchRow())
				{
		  ?>
				<tr>
					<td align="left"><?php echo $Row['date'] ?></td>
					<td align="left"><?php echo $Row['order_id'] ?></td>
					<td align="left"><?php echo $Row['business'] ?></td>
					<td align="left"><?php echo $Row['amount'] ?></td>
					<td align="left"><?php echo $Row['refrence_number'] ?></td>
				</tr>	
			<?php
				}
		  }
		  
		  ?>
		  </tbody>	
		</table>
	<?php
}
if(isset($_REQUEST['addnext_purchasepoint_order']))
{
	$month =$_REQUEST['month'];
	$year =$_REQUEST['year'];
	$business =$_REQUEST['business'];
	
	if ($month == 4 || $month == 6 || $month == 9 || $month == 11) 
	{
		//echo "in if";
		$m = 30;
	}
	else if ($month == 2) 
	{
		//echo "in else if";
		$m = 28;
	}
	else
	{
		//echo "in else";
		$m = 31;
	}
	if($_REQUEST['year'] != 0)
	{
		if($month != 0)
		{
			$from_date = $year . "-" . $month . "-01 00:00:00";
			$to_date = $year . "-" . $month . "-" . $m . " 23:59:59";
			$ordred_date =  " and (ppo.date between '".$from_date."' and '".$to_date."')";
		}
		else
		{
			$from_date = $year . "-01-01 00:00:00";
			$to_date = $year . "-12-" . $m . " 23:59:59";
			$ordred_date =  " and (ppo.date between '".$from_date."' and '".$to_date."')";
		}
	}
	
	$busnase = "";
	
	if($business==0)
	{
		$busnase ="";
	}
	else
	{
		$busnase =" and mu.id=".$business;
	}
	
	$sql="select ppo.date,ppo.order_id,ppo.amount,ppo.refrence_number,mu.business from purchase_point_order ppo,merchant_user mu where 
	    mu.id=ppo.merchant_id ".$ordred_date.$busnase ."  order by ppo.date desc limit ".$_REQUEST['sartlimit'].",".$language_msg['common']['bydefalt_entries_per_query'];
	
	$RS = $objDB->Conn->Execute($sql);	 
	
	if($_REQUEST['load_from_scrtch']  == 1)
	{
	
	?>
		
	<table  width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
		<thead>
		  <input type="hidden" name="total_pages" id="total_pages" value="" />
		  <input type="hidden" name="current_page" id="current_page" value="" />
		  
			<tr>
				<th width="15%" align="left">Order Date</th>
				<th width="10%" align="left">Order Number</th>
				<th width="20%" align="left">Merchant Name</th>
				<th width="10%" align="left">Amount</th>
				<th width="10%" align="left">Reference Number</th>
			</tr>
		</thead>
		<tbody>
	<?php
	}
	else{
		
	}
		    if($RS->RecordCount()>0)
			{
				while($Row = $RS->FetchRow())
				{
		  ?>
				<tr>
					<td align="left"><?php echo $Row['date'] ?></td>
					<td align="left"><?php echo $Row['order_id'] ?></td>
					<td align="left"><?php echo $Row['business'] ?></td>
					<td align="left"><?php echo $Row['amount'] ?></td>
					<td align="left"><?php echo $Row['refrence_number'] ?></td>
				</tr>	
			<?php
				}
		  }
		 if($_REQUEST['load_from_scrtch']  == 1)
			{ 
		  ?>
		  </tbody>	
		</table>
	<?php
		}
}

if($_REQUEST['mediaactiondelete'] == "yes")
{
	$folder ="";
	if($_REQUEST['image_type']=="1")
	{
		$folder = "campaign";
		$c_array = array();
		$c_array ['business_logo'] = $_REQUEST['src'];
		$merchant_info = $objDB->Show("campaigns",$c_array );
		
		$total = $merchant_info->RecordCount();
	}
	if($_REQUEST['image_type']=="2")
	{
		$folder = "location";
		$c_array = array();
		$c_array ['picture'] = $_REQUEST['src'];
		$merchant_info = $objDB->Show("locations",$c_array );
		$total = $merchant_info->RecordCount();
	}

	if($total== 0)
	{
		if($_REQUEST['image_type']=="1")
		{
			$array_where = array();
			$array_where['id'] = $_REQUEST['id'];
			$objDB->Remove("merchant_media", $array_where);
			unlink(UPLOAD_IMG."/m/".$folder."/".$_REQUEST['src']);
			unlink(UPLOAD_IMG."/m/".$folder."/block/".$_REQUEST['src']);
			unlink(UPLOAD_IMG."/m/".$folder."/ldpi/".$_REQUEST['src']);
			unlink(UPLOAD_IMG."/m/".$folder."/mdpi/".$_REQUEST['src']);
			unlink(UPLOAD_IMG."/m/".$folder."/hdpi/".$_REQUEST['src']);
			unlink(UPLOAD_IMG."/m/".$folder."/xhdpi/".$_REQUEST['src']);
			unlink(UPLOAD_IMG."/m/".$folder."/xxhdpi/".$_REQUEST['src']);
		}
		
		if($_REQUEST['image_type'] == "2")
		{
			$array_where = array();
			$array_where['id'] = $_REQUEST['id'];
			$objDB->Remove("merchant_media", $array_where);
			unlink(UPLOAD_IMG."/m/".$folder."/".$_REQUEST['src']);
			
			$thumb_image_folder=UPLOAD_IMG.'/m/location/mythumb/';
			unlink($thumb_image_folder.$_REQUEST['src']);
		}
		
		//$_SESSION['msg']="Image is deleted successfully";
				echo "success";
				//echo "Image is deleted successfully";
		//header("Location: ".WEB_PATH."/merchant/media-management.php");
		//exit();
	}
	else
	{
		//$_SESSION['msg']="This image is used anywhere.";
			echo "error";
		//header("Location: ".WEB_PATH."/merchant/media-management.php");
	//exit();
	}
}

if(isset($_REQUEST['show_more_media']))
{
	$start_index = $_REQUEST['next_index'];
	$num_of_records = $_REQUEST['num_of_records'];
	$next_index = $start_index + $num_of_records;
	
	$query = "select * from merchant_media where merchant_id=1 order by id desc limit $start_index,$num_of_records" ;
		             
   // echo $query;   
   
	$RS = $objDB->execute_query($query);
		
	$json_array = array();	
	$html="";
	
	if($RS->RecordCount()>0)
	{
		while($Row = $RS->FetchRow())
		{
			
				$target = "campaign" ;
			
				$html.='<div class="mediya_img_blk">';
				$html.='<a href="javascript:void(0)" class="mediya_camp_loca">';
				$html.='<img title="Campaign" src="'.ASSETS_IMG.'/m/mediya_campaign.png">';
				$html.='</a>';
				$html.='<img src="'.ASSETS_IMG.'/m/'.$target.'/'.$Row['image'].'" class="mediya_grid"/>';
				$html.='<a href="javascript:void(0)" src="'.$Row['image'].'" image_type="'.$Row['media_type_id'].'" id="mediadeleteid_'.$Row['id'].'" class="mediya_delete">
						<img src="'.ASSETS_IMG.'/m/mediya_delete.png">
						</a>';		
				$html.='</div>';
		}
	}
	
	$json_array['status'] = "true";
	$json_array['html'] = $html ;
	$json_array['records_return'] = $RS->RecordCount();
	
	$json = json_encode($json_array);
	echo $json;
	exit();		
}
if (isset($_REQUEST['addgiftcardplan'])) {

        $gfcard = array();
        $gfcard['name'] = $_REQUEST['name'];
        $gfcard['min_value'] = $_REQUEST['min_val'];
        $gfcard['max_value'] = $_REQUEST['max_val'];
	$gfcard['increment'] = $_REQUEST['increment'];
        $gfcard['currency'] = $_REQUEST['currency'];
	$gfcard['created'] = date('Y-m-d H:i:s');
        if ($objDBWrt->Insert($gfcard, "gift_card_plans")) {
                $_SESSION['msg'] = "Gift card plan has been added successfully.";
                header("Location: giftcard-plan.php");
                exit();
        } else {
                $_SESSION['msg'] = "Failed to add Gift card plan.";
                header("Location: giftcard-plan.php");
                exit();
        }
}
if (isset($_REQUEST['updategiftcardplan'])) {

        $gfcard = array();
        $gfcard['name'] = $_REQUEST['name'];
        $gfcard['min_value'] = $_REQUEST['min_val'];
        $gfcard['max_value'] = $_REQUEST['max_val'];
	$gfcard['increment'] = $_REQUEST['increment'];
        $gfcard['currency'] = $_REQUEST['currency'];
        $objDBWrt->Update($gfcard, "gift_card_plans",array('id'=>$_REQUEST['id']));
                $_SESSION['msg'] = "Gift card plan has been updated successfully.";
                header("Location: giftcard-plan.php");
                exit();
        
}
if(isset($_REQUEST['deletegiftcardplan'])){
	$id = $_REQUEST['id'];
	$gfcard['is_deleted'] = 1;
	$objDBWrt->Update($gfcard, "gift_card_plans",array('id'=>$id));
                $_SESSION['msg'] = "Gift card plan has been deleted successfully.";
                header("Location: giftcard-plan.php");
                exit();
       
}

/******** 
@USE : Get all country list 
@PARAMETER : 
@RETURN : country list
@USED IN PAGES : country-management.php
*********/
if(isset($_REQUEST['btnGetAllCountry']))
{
	$json_array = array();
	$records = array();
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ". $_REQUEST['iDisplayStart'].", ".
			 $_REQUEST['iDisplayLength'] ;
	}
	
	/* 
	 * Searching
	 */
	$search =''; 
	if(isset($_REQUEST['sSearch']))
	{
		if($_REQUEST['sSearch']=='' )
		{
			
		}
		else
		{
			$search ='where name like "%'.$_REQUEST['sSearch'].'%"';
		}
	}
	//echo "SELECT SQL_CALC_FOUND_ROWS * FROM country ".$search." ORDER BY display_order DESC ".$sLimit;
	$RS = $objDB->Conn->Execute("SELECT SQL_CALC_FOUND_ROWS * FROM country ".$search." ORDER BY name asc ".$sLimit);
	$sQuery = $objDB->Conn->Execute("SELECT FOUND_ROWS()");
	$total= $sQuery->fields['FOUND_ROWS()'];

	$Json = array();
	$Json['iDisplayLength'] = $_REQUEST['iDisplayLength'];
	$Json['iDisplayStart'] = $_REQUEST['iDisplayStart'];
	$Json['iTotalDisplayRecords'] = $total;
	$Json['sEcho'] = $_REQUEST['sEcho'];
	$Json['sSearch'] = $_REQUEST['sSearch'];
	$Json['aaData'] = array();
	
	if($RS->RecordCount()>0)
	{
		$k =0;
		while($Row = $RS->FetchRow())
		{
			$Json['aaData'][$k][]=$Row['name'];
			if($Row['active']==1)
				$Json['aaData'][$k][]="Active";
			else
				$Json['aaData'][$k][]="Deactive";
			
			
			$html ='<a href="edit-country.php?id='.$Row['id'].'"><span>Edit</span></a>&nbsp;|&nbsp;';
			$html .='<a href="add-state.php?id='.$Row['id'].'">Add State</a>&nbsp;|&nbsp;';
			if($Row['active']==1)
			{
				$html .='<a href="javascript:void(0)" class="active_country" active="0" id="'.$Row['id'].'">Deactivate</a>';
			}
			else
			{
				$html .='<a href="javascript:void(0)" class="active_country" active="1" id="'.$Row['id'].'">Activate</a>';
			}
			$Json['aaData'][$k][]=$html;
			$k++;
		}
	}
	echo json_encode($Json);
    exit;
}

/******** 
@USE : active-deactive country
@PARAMETER :  
@RETURN : 
@USED IN PAGES : country-management.php
*********/
if(isset($_REQUEST['deactivateCountry']))
{
	$json_array = array();
	$array_values = $where_clause = $array = array();
	$array_values['active'] = $_REQUEST['active'];
	$where_clause['id'] = $_REQUEST['country_id'];
	$objDBWrt->Update($array_values, "country", $where_clause);
	$json_array['status'] = "true";
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/******** 
@USE : Get all state list 
@PARAMETER : 
@RETURN : state list
@USED IN PAGES : country-management.php
*********/
if(isset($_REQUEST['btnGetAllState']))
{
	$json_array = array();
	$records = array();
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ". $_REQUEST['iDisplayStart'].", ".
			 $_REQUEST['iDisplayLength'] ;
	}
	
	/**
	* Searching	
	*/
	
	$search ='';
	if(isset($_REQUEST['country_id']))
	{
		if($_REQUEST['country_id']==0 )
		{
			
		}
		else
		{
			$search ='AND s.country_id='.$_REQUEST['country_id'];
		}
	}
	if(isset($_REQUEST['sSearch']))
	{
		if($_REQUEST['sSearch']=='' )
		{
			
		}
		else
		{
			$search .=' AND s.name like "%'.$_REQUEST['sSearch'].'%"';
		}
	}
	
	$RS = $objDB->Conn->Execute("SELECT SQL_CALC_FOUND_ROWS c.name 'country_name',s.* from country c,state s where c.id=s.country_id ".$search." ORDER BY s.name asc ".$sLimit);
	$sQuery = $objDB->Conn->Execute("SELECT FOUND_ROWS()");
	$total= $sQuery->fields['FOUND_ROWS()'];

	$Json = array();
	$Json['iDisplayLength'] = $_REQUEST['iDisplayLength'];
	$Json['iDisplayStart'] = $_REQUEST['iDisplayStart'];
	$Json['iTotalDisplayRecords'] = $total;
	$Json['sEcho'] = $_REQUEST['sEcho'];
	$Json['sSearch'] = $_REQUEST['sSearch'];
	$Json['aaData'] = array();
	
	if($RS->RecordCount()>0)
	{
		$k =0;
		while($Row = $RS->FetchRow())
		{
			$Json['aaData'][$k][]=$Row['name'];
			$Json['aaData'][$k][]=$Row['country_name'];
			if($Row['active']==1)
				$Json['aaData'][$k][]="Active";
			else
				$Json['aaData'][$k][]="Deactive";
			
			
			$html ='<a href="edit-state.php?id='.$Row['id'].'&cid='.$Row['country_id'].'"><span>Edit</span></a>&nbsp;|&nbsp;';
			$html .='<a href="add-city.php?id='.$Row['id'].'&cid='.$Row['country_id'].'">Add City</a>&nbsp;|&nbsp;';
			if($Row['active']==1)
			{
				$html .='<a href="javascript:void(0)" class="active_state" active="0" id="'.$Row['id'].'">Deactivate</a>';
			}
			else
			{
				$html .='<a href="javascript:void(0)" class="active_state" active="1" id="'.$Row['id'].'">Activate</a>';
			}
			$Json['aaData'][$k][]=$html;
			$k++;
		}
	}
	echo json_encode($Json);
    exit;
}

/******** 
@USE : active-deactive state
@PARAMETER :  
@RETURN : 
@USED IN PAGES : country-management.php
*********/
if(isset($_REQUEST['deactivateState']))
{
	$json_array = array();
	$array_values = $where_clause = $array = array();
	$array_values['active'] = $_REQUEST['active'];
	$where_clause['id'] = $_REQUEST['state_id'];
	$objDBWrt->Update($array_values, "state", $where_clause);
	$json_array['status'] = "true";
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/******** 
@USE : Get all city list 
@PARAMETER : 
@RETURN : city list
@USED IN PAGES : country-management.php
*********/
if(isset($_REQUEST['btnGetAllCity']))
{
	$json_array = array();
	$records = array();
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ". $_REQUEST['iDisplayStart'].", ".
			 $_REQUEST['iDisplayLength'] ;
	}
	
	/**
	* Searching	
	*/
	
	$search ='';
	if(isset($_REQUEST['country_id']))
	{
		if($_REQUEST['country_id']==0 )
		{
			
		}
		else
		{
			$search ='AND c.id='.$_REQUEST['country_id'];
		}
	}
	if(isset($_REQUEST['state_id']))
	{
		if($_REQUEST['state_id']==0 )
		{
			
		}
		else
		{
			$search .=' AND s.id='.$_REQUEST['state_id'];
		}
	}
	if(isset($_REQUEST['sSearch']))
	{
		if($_REQUEST['sSearch']=='' )
		{
			
		}
		else
		{
			$search .=' AND ci.name like "%'.$_REQUEST['sSearch'].'%"';
		}
	}
	//echo "SELECT SQL_CALC_FOUND_ROWS c.name 'country_name',s.name 'state_name',ci.* from country c,state s,city ci where ci.state_id=s.id and c.id=s.country_id ".$search." ORDER BY display_order DESC ".$sLimit;
	
	$RS = $objDB->Conn->Execute("SELECT SQL_CALC_FOUND_ROWS c.name 'country_name',s.name 'state_name',ci.* from country c,state s,city ci where ci.state_id=s.id and c.id=s.country_id ".$search." ORDER BY ci.name asc ".$sLimit);
	$sQuery = $objDB->Conn->Execute("SELECT FOUND_ROWS()");
	$total= $sQuery->fields['FOUND_ROWS()'];

	$Json = array();
	$Json['iDisplayLength'] = $_REQUEST['iDisplayLength'];
	$Json['iDisplayStart'] = $_REQUEST['iDisplayStart'];
	$Json['iTotalDisplayRecords'] = $total;
	$Json['sEcho'] = $_REQUEST['sEcho'];
	$Json['sSearch'] = $_REQUEST['sSearch'];
	$Json['aaData'] = array();
	
	if($RS->RecordCount()>0)
	{
		$k =0;
		while($Row = $RS->FetchRow())
		{
			$Json['aaData'][$k][]=$Row['name'];
			$Json['aaData'][$k][]=$Row['state_name'];
			$Json['aaData'][$k][]=$Row['country_name'];
			
			if($Row['active']==1)
				$Json['aaData'][$k][]="Active";
			else
				$Json['aaData'][$k][]="Deactive";
			
			
			$html ='<a href="edit-city.php?id='.$Row['id'].'&sid='.$Row['state_id'].'"><span>Edit</span></a>&nbsp;|&nbsp;';
			if($Row['active']==1)
			{
				$html .='<a href="javascript:void(0)" class="active_city" active="0" id="'.$Row['id'].'">Deactivate</a>';
			}
			else
			{
				$html .='<a href="javascript:void(0)" class="active_city" active="1" id="'.$Row['id'].'">Activate</a>';
			}
			$Json['aaData'][$k][]=$html;
			$k++;
		}
	}
	echo json_encode($Json);
    exit;
}

/******** 
@USE : active-deactive city
@PARAMETER :  
@RETURN : 
@USED IN PAGES : country-management.php
*********/
if(isset($_REQUEST['deactivateCity']))
{
	$json_array = array();
	$array_values = $where_clause = $array = array();
	$array_values['active'] = $_REQUEST['active'];
	$where_clause['id'] = $_REQUEST['city_id'];
	$objDBWrt->Update($array_values, "city", $where_clause);
	$json_array['status'] = "true";
	$json = json_encode($json_array);
	echo $json;
	exit();
}

/******** 
@USE : to get state of country
@PARAMETER :
@RETURN : json data
@USED IN PAGES : merchant-setup.php
*********/

if(isset($_REQUEST['btngetstateofcountry'])){
	$json_array = array();
	
	$array_where = array();
	$array_where['country_id'] = $_REQUEST['country_id'];
	$array_where['active'] = 1;
	$RS_state = $objDB->Show("state", $array_where," Order By `name` ASC ");
	
	$html="<option value='0'>Please Select</option>";
	
	if($RS_state->RecordCount()>0)
	{
		while($Row = $RS_state->FetchRow())
		{
			$html .="<option value='".$Row['id']."'>". $Row['short_form']."</option>";
		}
		$json_array['status'] = "true";
	}
	else
	{
		$json_array['status'] = "false";
	}
	$json_array["html"]= $html;
	$json_array["total_records"]= $RS_state->RecordCount();	
	
	$json = json_encode($json_array);
	echo $json;
	return $json;	
} 

/******** 
@USE : my images ajax tab
@PARAMETER : 
@RETURN :  json data
@USED IN PAGES : media-management.php
*********/

if(isset($_REQUEST['btnGetImageOfMerchantAjax']))
{
	$start_index = 0;
	$num_of_records = 16;
	$next_index = $start_index + $num_of_records;
	
	$query = "select * from merchant_media where merchant_id=1 order by id desc limit 0,$num_of_records" ;
	$query1 = "select count(*) total from merchant_media where merchant_id=1 order by id desc" ;
	
	$myQuery = $query;    
	$RS = $objDB->Conn->Execute($query);
	$RS1 = $objDB->Conn->Execute($query1);
	  
	$total_images = $RS1->fields['total'];
		
	
	
	//echo $RS->RecordCount();	
	if($RS->RecordCount()>0)
	{ 
		$html="<div id='mediya_image_container'>";
		while($Row = $RS->FetchRow())
		{	
			
			$target = "campaign" ;
			$html.='<div class="mediya_img_blk campaignfilter">';
			
			$html.='<a href="javascript:void(0)" class="mediya_camp_loca">';		
			$html.='<img title="Campaign" src="'.ASSETS_IMG.'/m/mediya_campaign.png" />'; 	
			$html.='</a>';
			
			$html.='<img src="'.ASSETS_IMG.'/m/'.$target.'/'.$Row['image'].'" class= "mediya_grid" />';
						
			$html.='<a href="javascript:void(0)" src="'.$Row['image'].'" image_type="'.$Row['media_type_id'].'" id="mediadeleteid_'.$Row['id'].'" class="mediya_delete">';
			$html.='<img src="'.ASSETS_IMG.'/m/mediya_delete.png">';
			$html.='</a>';
				
			$html.='</div>';	
		}
		$html.='</div>';
	}
	
		
	if($RS->RecordCount()>0 && $total_images>$num_of_records)
	{
	$html.='<div id="mediya_showmore" style="display:block;">';
			$html.='<input type="button" id="show_more_mediya" name="show_more_mediya" value="Show More" next_index="'.$next_index.'" num_of_records="'.$num_of_records.'" total_images="'.$total_images.'" />';
		$html.='</div>';
	
	}				
	$json_array['status'] = "true";
	$json_array['html'] = $html;
	$json = json_encode($json_array);
	echo $json;
	exit();
}
?>
