<?php
header('Content-type: text/html; charset=utf-8');
//require_once("/var/www/vhosts/scanflip.com/httpdocs/scanflip/classes/Config.Inc.php");
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");
include_once(LIBRARY."/languages/merchant_labels.php");

//$objJSON = new JSON();
//$objDB = new DB();

$array_where_as = array();
$array_where_as['id'] = 9;
$RS = $objDB->Show("admin_settings", $array_where_as);
if($RS->RecordCount()>0)
{
	//echo $RS->fields['action'];

	if($RS->fields['action']==0)
	{	
		$array_values_as = $array_where_as = array();
		$array_values_as['action'] = 1;
		$array_where_as['id'] = 9;
		$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
$where_clause = $array_values = array();
$array = $json_array = $where_clause = array();

//$where_clause['id'] = 99;
$RS_users = $objDB->Show("customer_user", $where_clause);
if($RS_users->RecordCount()>0)
{
	$location_change_flag=0;
	while($Row_user = $RS_users->FetchRow())
	{ 
		$customer_id = $Row_user['id'];
		//echo $customer_id;
		//echo "<br/>";
		
		if($Row_user['curr_timezone']!="")
		{
			$timezone=$Row_user['curr_timezone'];
			
			/*
			$user_timezone_name = get_nearest_timezone($Row_user['curr_latitude'],$Row_user['curr_longitude']);
			date_default_timezone_set($user_timezone_name);
			*/
			
			//echo "Offset = ".$timezone;
			//echo "<br/>";
			$arr = explode(":",$timezone);

			if($arr[1]=="00")
				$user_offset = $arr[0]*3600;
			else
				$user_offset = $arr[0]*3600+1800;
				
			//$user_offset = -4*3600; // for -04:00
			//$user_offset = 5*3600+1800; // for +05:30
			
			date_default_timezone_set('UTC');
			$diff = "$user_offset seconds";
			if ((substr($diff,0,1) != '+') && (substr($diff,0,1) != '-')) $diff = '+' . $diff;
			$usertime = strtotime($diff, time());
			//echo "<br/>";
			//echo "Hi ==== ".date('Y-m-d H:i:s', $usertime);
			//echo "<br/>";
			$now = time();
			$now += $user_offset;
			$currentTime = $now;
			
			//echo date('H:i',$currentTime); 
			//echo "<br/>";
			
			/*
			if (((int) date('H', $currentTime)) >= 10) 
			{
				echo "greatrer than 10";
			}
			else
			{
				echo "less than 10";
			}
			echo "<br/>";
			*/	
			
			if($Row_user['notification_setting']==1)
			{
				if($Row_user['gcm_registration_id']!="")
				{				
					// start for pending review notification
					
					$pending_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type,t.time_10_flag FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=3 and t.is_read=0 and t.customer_id=".$customer_id;
					$pending_data=$objDB->Conn->Execute($pending_data_query);
					if($pending_data->RecordCount()>0)
					{
						while($Row = $pending_data->FetchRow())
						{
							if($Row['time_10_flag']!=1)
							{
								if (((int) date('H', $currentTime)) >= 10) 
								{
									if($Row['counter']!=0)
									{
										if($Row['counter']==1)
										{
											$message = "You have ".$Row['counter']." pending review for your recent visit.";
											send_gcm_message($customer_id,$Row_user['gcm_registration_id'],$message,"pending_review");
											
											$where = array();
											$where['customer_id'] = $customer_id;
											$where['notification_type_id'] = 3;
											$u_array['time_10_flag'] = 1;
											$u_array['time_10_time'] = date("Y-m-d H:i:s");
											$objDB->Update($u_array, "notification", $where);
													
										}
										else
										{	
											$message = "You have ".$Row['counter']." pending reviews for your recent visits.";
											send_gcm_message($customer_id,$Row_user['gcm_registration_id'],$message,"pending_review");
											
											$where = array();
											$where['customer_id'] = $customer_id;
											$where['notification_type_id'] = 3;
											$u_array['time_10_flag'] = 1;
											$u_array['time_10_time'] = date("Y-m-d H:i:s");
											$objDB->Update($u_array, "notification", $where);
										}
									}
								}
							}		
						}
					}
				}
				
				if($Row_user['device_id']!="")
				{				
					// start for pending review notification
					
					$pending_data_query = "SELECT t.id 'id',t.counter 'counter',tp.notification_type,t.time_10_flag FROM notification t,notification_type tp where t.notification_type_id=tp.id and tp.id=3 and t.is_read=0 and t.customer_id=".$customer_id;
					$pending_data=$objDB->Conn->Execute($pending_data_query);
					if($pending_data->RecordCount()>0)
					{
						while($Row = $pending_data->FetchRow())
						{
							if($Row['time_10_flag']!=1)
							{
								if (((int) date('H', $currentTime)) >= 10) 
								{
									if($Row['counter']!=0)
									{
										if($Row['counter']==1)
										{
											$message = "You have ".$Row['counter']." pending review for your recent visit.";
											send_push_message($customer_id,$Row_user['device_id'],$message,"pending_review");
											
											$where = array();
											$where['customer_id'] = $customer_id;
											$where['notification_type_id'] = 3;
											$u_array['time_10_flag'] = 1;
											$u_array['time_10_time'] = date("Y-m-d H:i:s");
											$objDB->Update($u_array, "notification", $where);
													
										}
										else
										{	
											$message = "You have ".$Row['counter']." pending reviews for your recent visits.";
											send_push_message($customer_id,$Row_user['device_id'],$message,"pending_review");
											
											$where = array();
											$where['customer_id'] = $customer_id;
											$where['notification_type_id'] = 3;
											$u_array['time_10_flag'] = 1;
											$u_array['time_10_time'] = date("Y-m-d H:i:s");
											$objDB->Update($u_array, "notification", $where);
										}
									}
								}
							}		
						}
					}
				}
			}
		
		
		}
	}
}


	}	
	
	$array_values_as = $array_where_as = array();
	$array_values_as['action'] = 0;
	$array_where_as['id'] = 9;
	$objDB->Update($array_values_as, "admin_settings", $array_where_as);
		
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


function send_gcm_message($customer_id,$deviceid,$message,$message_type)
{
	//echo "in send gcm";
	$tickerText = $message;
	//print_r($merchant_msg['redeem-deal']);
	//$contentTitle = $merchant_msg['redeem-deal']['Msg_gcm_title'];
	$contentTitle = "Scanflip";
	$contentText = $message;
														
	$registrationId = $deviceid;
	$apiKey = GCM_API_KEY;
	
	//echo "aaa ".$contentTitle." bbb";	
	$response = sendNotification(
				$apiKey,
				array($registrationId),
				array('message' => $message, 'tickerText' => $tickerText, 'contentTitle' => $contentTitle, "contentText" => $contentText,"notificationType" => $message_type,"customer_id" => $customer_id)
				);
	//echo $response;			
}

function sendNotification( $apiKey, $registrationIdsArray, $messageData )
{
	$apiKey = GCM_API_KEY; 
	$headers = array("Content-Type:" . "application/json", "Authorization:" . "key=" . $apiKey);
	$data = array(
	'data' => $messageData,
	'registration_ids' => $registrationIdsArray
	);
	 
	$ch = curl_init();
	 
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send" );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
	 
	$response = curl_exec($ch);
	curl_close($ch);
	 
	return $response;
}

function send_push_message($customer_id,$deviceid,$message,$message_type)
{
	$deviceToken = $deviceid;

	// Put your private key's passphrase here:
	$passphrase = 'scanflip';

	// Put your alert message here:
	$message = $message;
	$arr= array();
	$arr['customer_id']=$customer_id;
	$arr['notificationType']=$message_type;
	
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', 'Scanflipck.pem');
	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

	// Open a connection to the APNS server
	$fp = stream_socket_client(
		'ssl://gateway.sandbox.push.apple.com:2195', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

	if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);
	
	// Create the payload body
	$body['aps'] = array(
		'alert' => $message,
		'sound' => 'default',
		'param'=>$arr
		);

	// Encode the payload as JSON
	$payload = json_encode($body);

	// Build the binary notification
	$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

	// Send it to the server
	$result = fwrite($fp, $msg, strlen($msg));

	if (!$result)
		echo 'Message not delivered' . PHP_EOL;
	else
		echo 'Message successfully delivered' . PHP_EOL;

	// Close the connection to the server
	fclose($fp);			
}

function sendPushNotification( $devideId, $messageData )
{
	// Put your device token here (without spaces):
	$deviceToken = $devideId;

	// Put your private key's passphrase here:
	$passphrase = 'scanflip';

	// Put your alert message here:
	$message = $messageData;
	
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', 'Scanflipck.pem');
	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

	// Open a connection to the APNS server
	$fp = stream_socket_client(
		'ssl://gateway.sandbox.push.apple.com:2195', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

	if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);
	
	// Create the payload body
	$body['aps'] = array(
		'alert' => $message,
		'sound' => 'default'
		);

	// Encode the payload as JSON
	$payload = json_encode($body);

	// Build the binary notification
	$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

	// Send it to the server
	$result = fwrite($fp, $msg, strlen($msg));

	if (!$result)
		echo 'Message not delivered' . PHP_EOL;
	else
		echo 'Message successfully delivered' . PHP_EOL;

	// Close the connection to the server
	fclose($fp);
	
	return $result;
}

function get_nearest_timezone($cur_lat, $cur_long, $country_code = '')
 {
    $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
                                    : DateTimeZone::listIdentifiers();

    if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

        $time_zone = '';
        $tz_distance = 0;

        //only one identifier?
        if (count($timezone_ids) == 1) {
            $time_zone = $timezone_ids[0];
        } else {

            foreach($timezone_ids as $timezone_id) {
                $timezone = new DateTimeZone($timezone_id);
                $location = $timezone->getLocation();
                $tz_lat   = $location['latitude'];
                $tz_long  = $location['longitude'];

                $theta    = $cur_long - $tz_long;
                $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat))) 
                + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                $distance = acos($distance);
                $distance = abs(rad2deg($distance));
                // echo '<br />'.$timezone_id.' '.$distance; 

                if (!$time_zone || $tz_distance > $distance) {
                    $time_zone   = $timezone_id;
                    $tz_distance = $distance;
                } 

            }
        }
        return  $time_zone;
    }
    return 'unknown';
}
?>
