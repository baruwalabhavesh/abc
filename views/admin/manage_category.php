<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

if(isset($_REQUEST['active']))
{
    $array_values = $where_clause = array();
    $array_values['active'] = $_REQUEST['active'];
    $where_clause['id'] = $_REQUEST['id'];
    $objDB->Update($array_values, "loyaltycard_category", $where_clause);
}
$RS = $objDB->Show("loyaltycard_category",''," Order By `display_order` ASC ");
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
		
	<form action="" method="post">
	<!--edit delete icons table-->
	<div><table align="right"  cellspacing="10px" >
	<tr><td><a href="add-loyalty-category.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png"></a></td>
<!--	<td><a href="edit-category.php?id=<?=$Row['id']?>"><img src="<?php echo ASSETS_IMG; ?>/a/icon-edit.png"></a></td>
	<td><a href="#"><img src="<?php echo ASSETS_IMG; ?>/a/icon-delete.png"></a></td>-->
	</tr>
	
	</table>
		<!--end edit delete icons table--></div>
		<br/>
		<table width="100%"   class="tableAdmin">
		  
		  <tr>
			<th width="30%" align="left">Category</th>
			<th width="10%" align="left">Order</th>
		    <th width="20%" align="left">&nbsp;</th>
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="left"><?=$Row['name']?></td>
			<td align="left">
			<?
			echo $Row['display_order'];
			?>
			</td>
		    <td align="left">
                        
			<a href="edit-loyalty-category.php?id=<?=$Row['id']?>">Edit</a>&nbsp;|&nbsp;
                        
                            <?php
                                if($Row['active']==1)
                                {
                            ?>
                                    <a href="manage_category.php?active=0&id=<?=$Row['id']?>">Deactivate</a>        
                            <?php                                  
                                }
                                else
                                {
                                ?>
                                    <a href="manage_category.php?active=1&id=<?=$Row['id']?>">Activate</a>        
                            <?php
                                }
                            ?>
                        </a>
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
			<td colspan="3" align="left">No Category is Found.</td>
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
