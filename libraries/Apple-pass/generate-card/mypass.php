<?php
require_once("../../classes/Config.Inc.php");
require_once('PKPass.php');
include_once(SERVER_PATH."/classes/DB.php");
$objDB = new DB();
//echo SERVER_PATH;
//exit();
	try
	{
	
	//echo "1";

	// Predefined data
	$labels = array(
		'SFO' => 'San Francisco',
		'LAX' => 'Los Angeles',
		'LHR' => 'London'
	);
	$gates = array('F12','G43','A2','C5','K9');
	
	// User-set vars
	$cust_id = $_REQUEST['cust_id'];
	
	$array2 = array();
	$array2['id'] = $cust_id;
	$RS = $objDB->Show("customer_user",$array2);
	
	$cust_name =  $RS->fields['firstname']." ".$RS->fields['lastname'];
	$card_number = $RS->fields['card_id'];
	
	//echo $cust_name ."=".$card_number;
	//exit();
	//echo "2";
	
	// Create pass
	
	//Set certifivate and path in the constructor
	$pass = new PKPass('Certificates.p12', 'scanflip'); 
	//echo "a";
	// Add the WWDR certificate 
	$pass->setWWDRcertPath('WWDR.pem');
	//echo "b";
	//Check if an error occured within the constructor
	if($pass->checkError($error) == true) {
		//echo "c";
		exit('An error occured: '.$error);
	}

	//echo "3";
	
	//Or do it manually outside of the constructor
	/*
	// Set the path to your Pass Certificate (.p12 file)
	if($pass->setCertificate('../../Certificate.p12') == false) {
		echo 'An error occured';
		if($pass->checkError($error) == true) {
			echo ': '.$error;
		}
		exit('.');
	} 
	// Set password for certificate
	if($pass->setCertificatePassword('test123') == false) {
		echo 'An error occured';
		if($pass->checkError($error) == true) {
			echo ': '.$error;
		}
		exit('.');
	}  */
	
		

	$pass->setJSON('{
   "passTypeIdentifier": "pass.com.lanetteam.lastock-pass",
	"formatVersion": 1,
	"organizationName": "Scanflip",
	"serialNumber": "'.$card_number.'",
	"teamIdentifier": "Y55EBSG532",
	"backgroundColor": "rgb(107,156,196)",
	"logoText": "Scanflip Rewards",
	"description": "Powering smart savings from local merchants",

  "barcode" : {
    "message" : "Scanflip Customer '.$card_number.'",
    "format" : "PKBarcodeFormatQR",
    "messageEncoding" : "iso-8859-1"
  },
  "foregroundColor" : "rgb(0,0,0)",
  "backgroundColor" : "rgb(197, 31, 31)",
  "labelColor" : "rgb(255,255,255)",
  "generic" : {
    "primaryFields" : [
      {
        "key" : "member",
        "value" : "'.$cust_name.'"
      }
    ],
    "secondaryFields" : [
            {
                "key" : "card_number",
                "label" : "Card Number",
                "value" : "'.$card_number.'"
            }
        ],
     "backFields": [
            {
                "key": "scanflip_back",
                "label": "Scanflip",
                "value": "To earn Scanflip reward points, just show your Scanflip card to the cashier when you check out at participating merchant locations. Please make sure you do so at the start of your transaction. By using Scanflip Rewards card you agree to scanflip terms & condition and privacy policy, both of which may change periodically. Scanflip Reward Card is not a credit / debit card. Scanflip is trademark of Scanflip Corp."
            }
        ],
    
  }
}
');


    if($pass->checkError($error) == true) {
    	exit('An error occured: '.$error);
    }

	//echo "4";
	
    // add files to the PKPass package
    $pass->addFile('../images/icon.png');
    $pass->addFile('../images/icon@2x.png');
    $pass->addFile('../images/logo.png');
    $pass->addFile('../images/logo@2x.png');
    
    if($RS->fields['profile_pic']!="")
    {
		
		$image_path_user=SERVER_PATH."/images/usr_pic/";
		$name ="thumbnail.png";
		$name2 ="thumbnail@2x.png";
					
	    $fb_img_json1= file_get_contents(WEB_PATH."/images/usr_pic/usr_pass_pic/".$RS->fields['profile_pic']);
		$fp1  = fopen($image_path_user.$name, 'w+');
		fputs($fp1, $fb_img_json1);
		
		$fb_img_json2= file_get_contents(WEB_PATH."/images/usr_pic/usr_pass_pic/big/".$RS->fields['profile_pic']);
		$fp2  = fopen($image_path_user.$name2, 'w+');
		fputs($fp2, $fb_img_json2);
					
		$pass->addFile(SERVER_PATH."/images/usr_pic/thumbnail.png");
		$pass->addFile(SERVER_PATH."/images/usr_pic/thumbnail@2x.png");
	}
	else
	{	
		$pass->addFile('../images/thumbnail.png');
		$pass->addFile('../images/thumbnail@2x.png');
	}
    if($pass->checkError($error) == true) {
    	//echo "in error";
    	exit('An error occured: '.$error);
    }

	
	//If you pass true, the class will output the zip into the browser.
    $result = $pass->create(true,$cust_id);
    if ($result == false) { // Create and output the PKPass
    	echo $pass->getError();
    }

	//echo "5";
	
	}
	catch(Exception $e)
	{
		//echo "in error :".$e->getMessage();	
	}
    
?>
