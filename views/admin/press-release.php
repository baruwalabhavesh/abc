<?php
//require_once("../classes/Config.Inc.php");
check_admin_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
//$where_arr= array();
//$RS = $objDB->Show("press_release",$where_arr);
$Sql_new = "SELECT * FROM press_release order by id DESC";  
$RS = $objDB->Conn->Execute($Sql_new);
if(isset($_REQUEST['action']) == "delete")
{
     $Sql = "delete FROM press_release WHERE id=".$_REQUEST['id'];
        
           $objDB->Conn->Execute($Sql);
		   $_SESSION['msg']="Press Release is deleted successfully";
           header("Location:press-release.php");
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
		  <h2>Press Release</h2>
	<form action="" method="post">
	<!--edit delete icons table-->
	<div><table align="right"  cellspacing="10px" >
	<tr><td><a href="add-press-release.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png"></a></td>
	
	
	</tr>
	
	</table>
		<!--end edit delete icons table--></div>
		<br/>
		<table width="100%"   class="tableAdmin">
		   <tr>
			<td colspan="5">&nbsp;<span style="color:#FF0000; "><?=$_SESSION['msg']?></span></td>
			
		  
		  </tr>
		  <tr>
			<th width="20%" align="left">Title</th>
			<th width="40%" align="left">Description</th>
			<th width="12%" align="left">Status</th>
			<th width="18%" align="left">Release Date</th>
		    <th width="10%" align="left">&nbsp;</th>
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
                        //    print_r($RS);
		  ?>
		  <tr>
			<td><?=$Row['title']?></td> 
            <td><?=$Row['description']?></td>
			<td><?if($Row['status']==0) echo "Deactive"; else echo "Active";?></td>
			<td><?=$Row['release_date']?></td>
        
			
		    <td align="left">
			<a href="edit-press-release.php?id=<?=$Row['id']?>">Edit</a>
                        <a href="press-release.php?id=<?=$Row['id']?>&action=delete">Delete</a>
			</td>
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
		  </tr>
		  <?
		  }else{
		  ?>
		  <tr>
			<td colspan="5" align="left">No Press Release found.</td>
		  </tr>
		  <?
		  } 
		  ?>
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
<?php 
$_SESSION['msg']="";
?>

</body>
</html>
