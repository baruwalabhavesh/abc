<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$array_where['id'] = $_REQUEST['id'];
$RS = $objDB->Show("categories", $array_where);
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
	<form action="process.php" method="post">
		<table width="100%" align="center"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
		  <tr>
		    <th align="right">&nbsp;</th>
		    <th align="left" style="color:#FF0000; "><?=$_SESSION['msg']?></th>
	      </tr>
		  <tr>
			<th width="40%" align="right">Category: </th>
			<th width="60%" align="left"><input type="text" name="cat_name" value="<?=$RS->fields['cat_name']?>" /></th>
		  </tr>
		   <tr>
			<th width="40%" align="right">Category Image In Search Deal Block: </th>
			<th width="60%" align="left"><input type="text" name="cat_image" value="<?=$RS->fields['cat_image']?>" size="60"/></th>
		  </tr>
		   <tr>
			<th width="40%" align="right">Category Image On Scroller: </th>
			<th width="60%" align="left"><input type="text" name="cat_scroller_image" value="<?=$RS->fields['cat_scroller_image']?>" size="60"/></th>
		  </tr>
		   <tr>
			<th width="40%" align="right">Category Active Image On Scroller: </th>
			<th width="60%" align="left"><input type="text" name="cat_scroller_image_active" value="<?=$RS->fields['cat_scroller_image_active']?>" size="60"/></th>
		  </tr>
		   <tr>
			<th align="right">Order: </th>
			<th align="left"><input type="text" name="orders" size="5" value="<?=$RS->fields['orders']?>" /></th>
		  </tr>
		 
		  <tr>
			<td>&nbsp;</td>
			<td align="left">
			<input type="hidden" name="id" value="<?=$RS->fields['id']?>" />
			<input type="submit" name="btnEditCategoryYes" value="Save" />
                        <input type="submit" name="cancat" value="Cancel" />
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
