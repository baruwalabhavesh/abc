<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Recently Added Locations</title>
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
$Sql = "SELECT L.*, MU.firstname, MU.lastname
		FROM locations L, merchant_user MU
		WHERE L.created_by = MU.id
		ORDER BY L.id DESC LIMIT 10";
//echo $Sql."<hr>";
$RS = $objDB->Conn->Execute($Sql);

?>
<h2>Recently Added Locations</h2>
<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
	  <tr>
		<td colspan="6" align="left" style="color:#FF0000; "><?php
                if(isset($_REQUEST['msg']))
                {
                    $_REQUEST['msg'];
                }
                ?>&nbsp;</td>
		</tr>
	  <tr>
	    <th width="20%" align="left">Location Name</th>
		<th width="30%" align="left">Address</th>
		<th width="10%" align="left">Phone</th>
		<th width="15%" align="left">Website</th>
		<th width="5%" align="left">Status</th>
		<th width="20%" align="left">Merchant</th>
	  </tr>
	  <?
	  if($RS->RecordCount()>0){
	  	while($Row = $RS->FetchRow()){
	  ?>
	  <tr>
	    <td><?=$Row['location_name']?></td>
		<td><?=$Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip']?></td>
		<td><?=$Row['phone_number']?></td>
		<td><?=$Row['website']?></td>
		<td>
		<?
		if($Row['active'] == 1) echo "Active"; else echo "Inactive";
		?>
		</td>
		<td><?=$Row['firstname']." ".$Row['lastname']?></td>
	  </tr>
	  
	  <?
	  	}
	?>
	
	<tr>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    </tr>
	
	<?	
	  }else{
	  ?>
	  <tr>
		<td colspan="6">No Location Found</td>
		</tr>
	  <?
	  }
	  ?>
	</table>
	  </form>
	</td>
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



</html>
