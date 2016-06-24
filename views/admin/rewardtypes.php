<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
if(isset($_REQUEST['btnApprove'])){
	foreach($_REQUEST['id'] as $id){
		$array_values = $where_clause = array();
		$array_values['active'] = 1;
		$where_clause['id'] = $id;
		$objDB->Update($array_values, "customer_user", $where_clause);
	}

}
if(isset($_REQUEST['active']))
{
    $array_values = $where_clause = array();
    $array_values['active'] = $_REQUEST['active'];
    $where_clause['id'] = $_REQUEST['id'];
    $objDB->Update($array_values, "reward_types", $where_clause);
}

$RS = $objDB->Show("reward_types",''," Order By `order` ASC ");

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
	<h2>Manage Reward Types </h2>
	<!--edit delete icons table-->
	<div><table align="right"  cellspacing="10px" >
	<tr><td><a href="add-rewardtype.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png"></a></td>
<!--	<td><a href="edit-category.php?id=<?=$Row['id']?>"><img src="<?php echo ASSETS_IMG; ?>/a/icon-edit.png"></a></td>
	<td><a href="#"><img src="<?php echo ASSETS_IMG; ?>/a/icon-delete.png"></a></td>-->
	</tr>
	
	</table>
	<div style="color:#FF0000; "><?=$_SESSION['msg'];?></div>
		<!--end edit delete icons table--></div>
		<br/>
		<table width="100%"   class="tableAdmin">
		  
		  <tr>
			<th width="70%" align="left">Reward Type</th>
			<th width="10%" align="left">Order</th>
			<th width="20%" align="left">&nbsp;</th>
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="left"><?=$Row['reward_type']?></td>
			<td align="left"><?=$Row['order']?></td>
			<td align="left">
                        
			<a href="edit-rewardtype.php?id=<?=$Row['id']?>">Edit</a>&nbsp;|&nbsp;
                        
                            <?php
                                if($Row['active']==1)
                                {
                            ?>
                                    <a href="rewardtypes.php?active=0&id=<?=$Row['id']?>">Deactivate</a>        
                            <?php                                  
                                }
                                else
                                {
                                ?>
                                    <a href="rewardtypes.php?active=1&id=<?=$Row['id']?>">Activate</a>        
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
			<td colspan="3" align="left">No Reward Type is Found.</td>
		  </tr>
		  <?
		  }
		  ?>
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
