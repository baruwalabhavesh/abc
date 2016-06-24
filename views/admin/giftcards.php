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
if(isset($_REQUEST['action']))
{
		/* if($_REQUSET['action'] == "delete")
		{ */
			  $array_values = $where_clause = array();
			$array_values['is_deleted'] = 1;
			$where_clause['id'] = $_REQUEST['id'];
			$objDB->Update($array_values, "giftcards", $where_clause);
header("Location:".WEB_PATH."/admin/giftcards.php");
		//}
}
if(isset($_REQUEST['active']))
{
    $array_values = $where_clause = array();
    $array_values['is_active'] = $_REQUEST['active'];
    $where_clause['id'] = $_REQUEST['id'];
    $objDB->Update($array_values, "giftcards", $where_clause);
}
$Sql = "select id,title,image , value , card_value ,redeem_point_value,ship_to ,is_active,
(select merchant_name from giftcard_merchant_user where id =merchant_id) merchant_name,
(select cat_name from giftcard_categories where id =category_id) category
from
giftcards where is_deleted = 0 and is_merchant = 0
order by id desc
";
$RS =  $objDB->Conn->Execute($Sql);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>

<style type="text/css" title="currentStyle">
			@import "<?php echo ASSETS_CSS?>/a/demo_page.css";
			@import "<?php echo ASSETS_CSS?>/a/demo_table.css";
</style>
		
<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
<script>
$(document).ready(function() {
			
				var table = $('#example').dataTable(  {
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					 "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                    "iDisplayLength" : 25,
					"aaSorting": [],
					 "aoColumns": [
					  { "bSortable": false },
					  { "bSortable": false },
					    { "bSortable": false },
					   { "bSortable": false },
					  { "bSortable": false },
					    { "bSortable": false }
					  ]
				} );
});
</script>
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
	<h2>
	Manage Gift Card
	</h2>
	<!--edit delete icons table-->
	<div><table align="right"  cellspacing="10px" >
	<tr><td><a href="add-giftcard.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png"></a></td>
<!--	<td><a href="edit-category.php?id=<?=$Row['id']?>"><img src="<?php echo ASSETS_IMG; ?>/a/icon-edit.png"></a></td>
	<td><a href="#"><img src="<?php echo ASSETS_IMG; ?>/a/icon-delete.png"></a></td>-->
	</tr>
	
	</table>
	<div style="color:#FF0000; "><?=$_SESSION['msg'];?></div>
		<!--end edit delete icons table--></div>
		<br/>
		<table width="100%"   class="tableAdmin" id="example">
		  <thead>
		  <tr>
			<th width="30%" align="left">Title</th>
			<th width="10%" align="left">Merchant</th>
			<th width="10%" align="left">Category</th>
		    <th width="10%" align="left">Card Value</th>
			<th width="12%" align="left">Ship To</th>
			<th width="" align="left">&nbsp;</th>
		  </tr>
		  </thead>
		  <tbody>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="left"><?=$Row['title']?></td>
			<td align="left"><?=$Row['merchant_name']?></td>
			<td align="left">
			<?
			echo $Row['category'];
			?>
			</td>
			<td align="left">
			<?
			echo $Row['card_value'];
			?>
			</td>
			<td align="left">
			<?
			echo $Row['ship_to'];
			?>
			</td>
		    <td align="left">
                        
			<a href="edit-giftcard.php?id=<?=$Row['id']?>">Edit</a>&nbsp;|&nbsp;
                        
                            <?php
                                if($Row['is_active']==1)
                                {
                            ?>
                                    <a href="giftcards.php?active=0&id=<?=$Row['id']?>">Deactivate</a>        
                            <?php                                  
                                }
                                else
                                {
                                ?>
                                    <a href="giftcards.php?active=1&id=<?=$Row['id']?>">Activate</a>        
                            <?php
                                }
                            ?>
                        &nbsp;|&nbsp;<a href="giftcards.php?action=delete&id=<?=$Row['id']?>">Delete</a>
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
			<td>&nbsp;</td>
		  </tr>
		  <?
		  }else{
		  ?>
		  <tr>
			<td colspan="6" align="left">No gift card is Found.</td>
		  </tr>
		  <?
		  }
		  ?>
		  </tbody>
		</table>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
