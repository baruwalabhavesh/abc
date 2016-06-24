<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
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
		<?
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$RS = $objDB->Show("merchant_user","", " ORDER BY last_login DESC LIMIT 10");
?>
<h2>Most Active Merchants</h2>
<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
		  <tr>
			<th width="16%" align="left">Business Name</th>
			<!--<th width="20%" align="left">Email</th> -->
			<th width="19%" align="left">Country</th>
			<th width="29%" align="left">State</th>
			<th width="16%" align="left">Last Visited </th>
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="left"><?=$Row['firstname']." ".$Row['lastname']?></td>
			<!--<td align="left"><?=$Row['email']?></td> -->
			<td align="left">
			<?=$Row['country']?>
			</td>
			<td align="left"><?=$Row['state']?></td>
			<td align="left">
			<?
			echo date("m-d-Y H:i:s", strtotime($Row['last_login']));
			?>
			</td>
		  </tr>
		  <?
		  }
		 
		  }else{
		  ?>
		  <tr>
			<td colspan="5" align="left">No Merchant is Found.</td>
		  </tr>
		  <?
		  }
		  ?>
		  <tr>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		</table>
		</td>
	  </tr>
	</table>
                     <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
