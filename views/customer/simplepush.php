<?php

// Put your device token here (without spaces):
//$deviceToken = 'da12ad62158677a7be5c045f572f7d72daf1a97b6bdfe5aa161539fcf0fc9185';
//$deviceToken = '8d79ad06722587d1d6cf165cb84f2779396ec141fca77fc5d55bd6e7081531e8';

$deviceToken = '3b21351a7d51c9243cb4123646052b0cd0417807b8130e054a9e014408ea1742'; // ipad

//$deviceToken = '6269e18c5ca47d9fe8ad8a3874672ba4345766688b7b27b8b4752c0ffe769726'; 
// Put your private key's passphrase here:
$passphrase = 'scanflip';

// Put your alert message here:  
$message = 'ScanFlip notification!';

////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'Scanflipck.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
$fp = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
$body['aps'] = array(
	'alert' =>  $message,
	'sound' => 'default',
	'notification_id'=> 16,
	'badge'=>1
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

?>
