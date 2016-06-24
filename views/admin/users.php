<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();


$array_where = array();
if(isset($_REQUEST['action']))
{
if($_REQUEST['action'] == "delete"){
	
        $array_where['id'] = $_REQUEST['id'];
	$objDB->Remove("customer_user", $array_where);
        
	$array_where = array();
        $array_where['user_id'] = $_REQUEST['id'];
	$objDB->Remove("merchant_subscribs", $array_where);
        
	$array_where11['customer_id'] = $_REQUEST['id'];
	$objDB->Remove("subscribed_stores", $array_where11);
        
        $array_where12['customer_id'] = $_REQUEST['id'];
	$objDB->Remove("customer_campaigns", $array_where12);
            
        $array_where14['customer_id'] = $_REQUEST['id'];
	$objDB->Remove("reward_user", $array_where14);
	
	$array_where15['customer_id'] = $_REQUEST['id'];
	$objDB->Remove("coupon_codes", $array_where15);
	
	$array_where16['customer_id'] = $_REQUEST['id'];
	$objDB->Remove("customer_email_settings", $array_where16);
        
        header("Location: ".WEB_PATH."/admin/users.php");
	exit();
}
}


if(isset($_REQUEST['btnApprove'])){
	foreach($_REQUEST['id'] as $id){
		$array_values = $where_clause = array();
		$array_values['active'] = 1;
		$where_clause['id'] = $id;
		$objDB->Update($array_values, "customer_user", $where_clause);
               
	}

}
$RS = $objDB->Show("customer_user");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
 <script type="text/javascript" src="<?php echo ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>
	<style type="text/css" title="currentStyle">
			@import "<?php echo ASSETS_CSS?>/a/demo_page.css";
			@import "<?php echo ASSETS_CSS?>/a/demo_table.css";
		</style>
		
		<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('#example').dataTable( {
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					 "aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                                        "iDisplayLength" : 20,
					"aoColumns": [ { "bSortable": false },
						null,
						null, 
						null, 
						null
						]
				} );
			} );
		</script>
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
		<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
		<thead>
		  <tr>
			<th width="19%">&nbsp;</th>
			<th width="30%" align="left">Name</th>
			<th width="25%" align="left">Status</th>
            <th width="15%" align="left"></th>
	    <th width="25%" align="left"></th>
		  </tr>
		  </thead>
		<tbody>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="center"><input type="checkbox" name="id[]" value="<?=$Row['id']?>" <? if($Row['active'] == 1) echo "checked";?> /></td>
			<td align="left"><?=$Row['emailaddress']?></td>
			<td align="left">
			<?
			if($Row['active'] == 1) echo "Yes"; else echo "No";
			?>
			</td>
             <td><a href="<?=WEB_PATH?>/admin/users.php?id=<?=$Row['id']?>&action=delete">Delete</a>
	     
	     
	     </td>
	     <td>
		<a href="<?=WEB_PATH?>/admin/subscription_detail.php?id=<?=$Row['id']?>">Details</a>
	     </td>
		  </tr>
		  <?
		  }
		  ?>
		 
		  <?
		  }else{
		  ?>
		  
		  <?
		  }
		  ?>
		  </tbody>
		<tfoot>
			 <tr>
			<td align="center">
			<input type="submit" name="btnApprove" value="Approve"/>
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
                        <td>&nbsp;</td>
            <td>&nbsp;</td>
		  </tr>
		</tfoot>
		</table>
	  </form>
	
                     <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
