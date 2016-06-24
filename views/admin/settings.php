<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
if(isset($_REQUEST['btnApprove'])){
	foreach($_REQUEST['id'] as $id){
		$array_values = $where_clause = array();
		$array_values['approve'] = 1;
		$where_clause['id'] = $id;
		$objDB->Update($array_values, "merchant_user", $where_clause);
	}

}
$RS = $objDB->Show("merchant_user");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Setting</title>
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
		<h2>Setting</h2>
	<form action="" method="post">
		<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
		  <tr>
			<th width="6%">&nbsp;</th>
			<th width="22%" align="left">Email</th>
			<th width="24%" align="left">Name</th>
			<th width="40%" align="left">Address</th>
			<th width="8%" align="left">Approved</th>
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="center"><input type="checkbox" name="id[]" value="<?=$Row['id']?>" <? if($Row['approve'] == 1) echo "checked";?> /></td>
			<td align="left"><?=$Row['email']?></td>
			<td align="left"><?=$Row['firstname']." ".$Row['lastname']?></td>
			<td align="left"><?=$Row['address']?>, <?=$Row['city']?>, <?=$Row['state']?>, <?=$Row['zipcode']?>, <?=$Row['country']?></td>
			<td align="left">
			<?
			if($Row['approve'] == 1) echo "Yes"; else echo "No";
			?>
			</td>
		  </tr>
		  <?
		  }
		  ?>
		  <tr>
			<td align="center">
			<input type="submit" name="btnApprove" value="Approve"/>
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		  <?
		  }else{
		  ?>
		  <tr>
			<td colspan="5" align="left">No Merchant is Found.</td>
		  </tr>
		  <?
		  }
		  ?>
		</table>
		</form>
                     <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>




</body>
</html>
