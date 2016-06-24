<?php
/******** 
@USE : unsubscribed from email
@PARAMETER : 
@RETURN : 
@USED IN PAGES : customer process.php
*********/
//require_once("classes/Config.Inc.php");
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Email Unsubscribe</title>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?=ASSETS_CSS?>/c/template.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
require_once(CUST_LAYOUT."/header.php");
?>
<div id="content" class="cantent">
	<div class="my_main_div">
		<div id="contentContainer" class="contentContainer">
			<div class="page-content" id="page-content">
				<div class="vignette">
					<h1 class="page-title"></h1>
					<span class="tagline"></span>
				</div>

				<div class="unsubscribe-content novignette">    
					<div class="page-title-pma">Email Preferences</div>
					<p class="unsub-msg">

					<?php
					
						$array_cust['id'] = $_REQUEST['id'];
						$RS_cust = $objDB->Show("customer_user",$array_cust);
				
					?>

					<?php echo $RS_cust->fields['emailaddress'] ?> has been unsubscribed from Scanflip email.



					<br>
					To view or change Scanflip email subscriptions, please visit our <a href="<?php echo WEB_PATH ?>/my-emailsettings.php">Email Preferences</a> page.
					</p>
				</div>
			</div>
		</div>
	
	<?php require_once(CUST_LAYOUT."/before-footer.php");?>
</div><!--end of my_main_div-->
</div><!--end of content-->

<?php require_once(CUST_LAYOUT."/footer.php");?>

</body>
</html>
<?php
$_SESSION['req_pass_msg']="";
?>
