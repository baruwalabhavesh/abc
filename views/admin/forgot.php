<?
//require_once("../classes/Config.Inc.php");
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

if(isset($_REQUEST['btnSubmit'])){
	$array = array();
	$array['emailaddress'] = $_REQUEST['emailaddress'];
	//echo "<pre>";print_r($_REQUEST);echo "</pre>";
	$RS = $objDB->Show("admin_user",$array);
	if($RS->RecordCount()<=0){
		header("Location: ".WEB_PATH."/admin/forgot.php?msg=Please enter valid Email Address");
		exit();
	}
	$mail = new PHPMailer();
	$body = "<p>Your Username and password are given below </p>";
	$body .= "<p><b>Username:</b> ".$RS->fields['username']."</p>";
	$body .= "<p><b>Password:</b> ".$RS->fields['password']."</p>";
	$mail->AddReplyTo('no-reply@scanflip.com','ScanFlip Support');
	$mail->AddAddress($RS->fields['emailaddress']);
	$mail->Subject    = "Forgot Username or Password";
	$mail->MsgHTML($body);
	$mail->Send();
	header("Location: ".WEB_PATH."/admin/forgot.php?msg=Your login details have been email to you");
	exit();
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
</head>

<body>
     <div id="container">

              <!---start header---->
	
		<?
		require_once(ADMIN_LAYOUT."/header.php");
		?>
		<div id="contentContainer">

	
	<div  id="sidebarLeft">
		<?
		require_once(ADMIN_VIEW."/quick-links.php");
		?>
		<!--end of sidebar Left--></div>

		<div id="content">
	<form action="forgot.php" method="post">
				<table width="50%" align="center"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
				  <tr>
				  	<td></td>
				    <td align="left" style="color:#FF0000; "><?php
                                    if(isset($_REQUEST['msg']))
                                    {
                                        $_REQUEST['msg'];
                                    }
                                            
                                            ?></td>
			      </tr>
				  <tr>
					<td width="25%" align="left">Email:</td>
					<td width="75%" align="left">
						<input type="text" name="emailaddress" id="emailaddress" />
					</td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<input type="submit" name="btnSubmit" value="Submit">
					</td>
				  </tr>
				</table>
	  </form>

 	       <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
