<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
</head>

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
			<h2>Add Country</h2>
	<form action="process.php" method="post">
		<table width="100%" align="center"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
		  <tr>
		    <th align="right">&nbsp;</th>
		    <th align="left" style="color:#FF0000; "><?=$_SESSION['msg']?></th>
	      </tr>
		  <tr>
			<th width="40%" align="right">Country Name: </th>
			<th width="60%" align="left"><input type="text" name="name" /></th>
		  </tr>
		  
		  <?php
			$currencies = $objDB->Conn->Execute('Select id_countries,country_name,currency_code from currencies');
			?>
			<tr>
				<th align="right">Currency:</th>
				<th align="left">
					<select name="currency" id="currency">
						<?php
						if ($currencies->RecordCount() > 0) {
								while ($c = $currencies->FetchRow()) {
										?>
										<option value="<?php echo $c['id_countries']; ?>"><?php echo $c['country_name'] . ' - ' . $c['currency_code']; ?></option>
										<?php
								}
						}
						?>
					</select>
				</th>
			</tr>
		  <tr>
			<td>&nbsp;</td>
			<td align="left">
			<input type="submit" name="btnAddCountry" value="Save" />
                        <input type="submit" name="canCountry" value="Cancel" />
			</td>
		  </tr>
		 
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


<?
$_SESSION['msg'] = "";
?>

</body>
</html>
