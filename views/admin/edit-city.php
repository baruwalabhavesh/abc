<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

$array_where = array();
$array_where['id'] = $_REQUEST['sid'];
$RS_state = $objDB->Show("state", $array_where);

$array_where = array();
$array_where['id'] = $RS_state->fields['country_id'];
$RS_country = $objDB->Show("country", $array_where);

$array_where = array();
$array_where['id'] = $_REQUEST['id'];
$RS = $objDB->Show("city", $array_where);

$array_where = array();
$array_where['country_id'] = $RS_state->fields['country_id'];
$RS_state_data = $objDB->Show("state", $array_where);

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
			<h2>Edit City</h2>
	<form action="process.php" method="post">
		<table width="100%" align="center"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
		  <tr>
		    <th align="right">&nbsp;</th>
		    <th align="left" style="color:#FF0000; "><?=$_SESSION['msg']?></th>
	      </tr>
		  <tr>
			<th width="40%" align="right">Country Name: </th>
			<th width="60%" align="left"><?php echo $RS_country->fields['name']?></th>
		  </tr>
		   <tr>
			<th width="40%" align="right">State Name: </th>
			<th width="60%" align="left">
				<?php //echo $RS_state->fields['name']?>
				<select name="state" id="state">
					<?php
					if($RS_state_data->RecordCount()>0)
					{
						while($Row = $RS_state_data->FetchRow())
						{
							?>
							<option value="<?php echo $Row['id'] ?>" <?php if($Row['id']==$RS->fields['state_id']){echo "selected";} ?> ><?php echo $Row['short_form'] ?></option>
							<?php
						}
					}
					?>
				</select>
			</th>
		  </tr>
		  <tr>
			<th width="40%" align="right">City Name: </th>
			<th width="60%" align="left"><input type="text" name="name" value="<?=$RS->fields['name']?>" /></th>
		  </tr>
		  
		 
		  <tr>
			<td>&nbsp;</td>
			<td align="left">
			<input type="hidden" id="state_id" name="state_id" value="<?php echo $_REQUEST['sid']; ?>" />	
			<input type="hidden" name="id" value="<?=$RS->fields['id']?>" />
			<input type="submit" name="btnEditCity" value="Save" />
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
