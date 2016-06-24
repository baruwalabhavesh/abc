<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$where_arr= array();

$RS = $objDB->Show("marketing_material",$where_arr);
if(isset($_REQUEST['action']) == "delete")
{
     $Sql = "delete FROM marketing_material WHERE id=".$_REQUEST['id'];
        
           $objDB->Conn->Execute($Sql);
           header("Location:marketingmaterial.php");
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
		  <h2>Marketing Material</h2>
	<form action="" method="post">
	<!--edit delete icons table-->
	<div><table align="right"  cellspacing="10px" >
	<tr><td><a href="add-marketingmaterial.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png"></a></td>
	
	
	</tr>
	
	</table>
		<!--end edit delete icons table--></div>
		<br/>
		<table width="100%"   class="tableAdmin">
		   <tr>
			<td colspan="3">&nbsp;<span style="color:#FF0000; "><?=$_SESSION['msg']?></span></td>
			
		  
		  </tr>
		  <tr>
			<th width="40%" align="left">Material name</th>
			<th width="40%" align="left">Document size</th>
  			
			
		    <th width="10%" align="left">&nbsp;</th>
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
                        //    print_r($RS);
		  ?>
		  <tr>
			<td><?=$Row['material_name']?></td> 
                        <td>
                          <?php
                                                    $array_document_size = array();
                                                    $array_document_size['id'] = $Row['material_size'];
                                                    $Rs_size = $objDB->Show("marketingmaterial_size",$array_document_size);
                                                   ?>
                                                <?php echo $Rs_size->fields['size_name']."( full bleed size- ".$Rs_size->fields['full_bleed_width_mm']."X".$Rs_size->fields['full_bleed_height_mm']." mm )" ?> 
                                                    <?php 
                                                ?>
                        </td>
        
			
		    <td align="left">
			<a href="edit-marketingmaterial.php?id=<?=$Row['id']?>">Edit</a>
                        <a href="marketingmaterial.php?id=<?=$Row['id']?>&action=delete">Delete</a>
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
			<td colspan="3" align="left">No Default marketing material is Found.</td>
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
