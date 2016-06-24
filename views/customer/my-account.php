<?php
/******** 
@USE : customer my account page
@PARAMETER : 
@RETURN : 
@USED IN PAGES : register.php, login.php, facebook_login.php
*********/
require_once("classes/Config.Inc.php");
check_customer_session();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ScanFlip</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=WEB_PATH?>/templates/template.css" rel="stylesheet" type="text/css">
</head>


<body>

<div style="width:100%;text-align:center;">
<!--start header--><div>

		<?
		require_once(SERVER_PATH."/templates/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	<div id="content">
    <h1>My Account</h1>
	
  <!--end of content--></div>
<!--end of contentContainer--></div>
<!--start footer--><div>
		<?
		require_once(SERVER_PATH."/templates/footer.php");
		?>
		<!--end of footer--></div>
		</div>

</body>
</html>
