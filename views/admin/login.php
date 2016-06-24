<?php
//require_once("../classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
if(isset($_REQUEST['btnLogin'])){
	
	$array = array();
	$array['username'] = $_REQUEST['username'];
	$array['password'] = md5($_REQUEST['password']);
	
	$RS = $objDB->Show("admin_user",$array);
        
	if($RS->RecordCount()<=0){
		header("Location: ".WEB_PATH."/admin/login.php?msg=Please enter valid Username/Password");
		exit();
	}
	if($RS->fields['active'] == 0){
		header("Location: ".WEB_PATH."/admin/login.php?msg=Your account is not Activated");
		exit();
	}
		
	$Row = $RS->FetchRow();
      
        
	$_SESSION['admin_id'] = $Row['id'];
	$_SESSION['admin_info'] = $Row;
	
	$array_values = $where_clause = $array = array();
	$array_values['lastvisit_date'] = date("Y-m-d H:i:s");
	$where_clause['id'] = $_SESSION['admin_id'];
	$objDB->Update($array_values, "admin_user", $where_clause);
	
	header("Location: ".WEB_PATH."/admin/index.php");
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
<div style="width:100%;text-align:center;">
<div class="adminLogin">
<div class="adminLoginBox" >
<form action="<?=WEB_PATH?>/admin/login.php" method="post">
				<table width="50%" align="center"  border="0" cellspacing="2" cellpadding="2">
				  <tr>
				    <td colspan="2" align="center" style="color:#FF0000; ">
                                        <?php
                                    if(isset($_REQUEST['msg']))
                                    {
                                        echo $_REQUEST['msg'];
                                    }       
                                            ?>
                                    
                                    </td>
			      </tr>
				  <tr>
					<td width="40%" align="right">Username: </td>
					<td width="60%" align="left">
						<input type="text" name="username" id="username" />
					</td>
				  </tr>
				  
				  <tr>
					<td align="right">Password: </td>
					<td align="left">
						<input type="password" name="password" id="password" />
					</td>
				  </tr>
				   <tr>
					<td align="left"> </td>
					<td align="left">
						<input type="checkbox" name="keepme" id="keepme" /> 
						Keep me signin on this computer
					</td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
					<td align="left">
						<input type="submit" id="btnLogin" name="btnLogin" value="Login">
					</td>
				  </tr>
				  <tr>
				    <td>&nbsp;</td>
				    <td align="left">
						<a href="forgot.php">
							I Forgot Password Password or Username 
						</a>
						</td>
			      </tr>
				</table>
				</form>
				<!--End adminLoginBox--></div>
				<!--End adminLogin--></div>
</div>
</body>
</html>

