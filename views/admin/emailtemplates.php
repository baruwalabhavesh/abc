<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

$RS = $objDB->Show("emailtemplate");
if(isset($_REQUEST['action']) == "delete")
{
     $Sql = "delete FROM emailtemplate WHERE id=".$_REQUEST['id'];
        
           $objDB->Conn->Execute($Sql);
           header("Location:emailtemplates.php");
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
                    <h2>Email Template</h2>
	<form action="" method="post">
	<!--edit delete icons table-->
	<div><table align="right"  cellspacing="10px" >
	<tr><td><a href="add-emailtemplate.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png"></a></td>
	
	
	</tr>
	
	</table>
		<!--end edit delete icons table--></div>
		<br/>
		<table width="100%"   class="tableAdmin">
		   <tr>
			<td colspan="3">&nbsp;<span style="color:#FF0000; "><?=$_SESSION['msg']?></span></td>
			
		  
		  </tr>
		  <tr>
			<th width="40%" align="left">Key</th>
			<th width="40%" align="left">Subject</th>
			
		    <th width="10%" align="left">&nbsp;</th>
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="left"><?=$Row['template_key']?></td>
			<td align="left"><?=$Row['subject']?></td>
			
		    <td align="left">
			<a href="edit-emailtemplate.php?id=<?=$Row['id']?>">Edit</a>&nbsp;
                        <a href="emailtemplates.php?id=<?=$Row['id']?>&action=delete">Delete</a>
			</td>
		  </tr>
		  <?
		  }
		  ?>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  
		  </tr>
		  <?
		  }else{
		  ?>
		  <tr>
			<td colspan="3" align="left">No Email template is Found.</td>
		  </tr>
		  <?
		  }
		  ?>
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<?php $_SESSION['msg']=""; ?>

</body>
</html>
