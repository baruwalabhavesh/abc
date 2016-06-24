<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
if(isset($_REQUEST['btnApprove'])){
	foreach($_REQUEST['id'] as $id){
		$array_values = $where_clause = array();
		$array_values['visible'] = 1;
		$where_clause['id'] = $id;
		$objDB->Update($array_values, "campaigns", $where_clause);
	}

}
if(isset($_REQUEST['action']))
{
if($_REQUEST['action'] == "delete"){
	$array_where['id'] = $_REQUEST['id'];
	$objDB->Remove("campaigns", $array_where);
	
	$array_where1['campaign_id'] = $_REQUEST['id'];
	$objDB->Remove("campaign_location", $array_where1);
	
	$array_where2['campaign_id'] = $_REQUEST['id'];
	$objDB->Remove("campaign_groups", $array_where2);
	
	$array_where3['campaign_id'] = $_REQUEST['id'];
	$objDB->Remove("customer_campaigns", $array_where3);
	
	$array_where4['campaign_id'] = $_REQUEST['id'];
	$objDB->Remove("reward_user", $array_where4);
	
	$array_where5['campaign_id'] = $_REQUEST['id'];
	$objDB->Remove("activation_codes", $array_where5);
	
	$array_where6['customer_campaign_code'] = $_REQUEST['id'];
	$objDB->Remove("coupon_codes", $array_where6);
	
	
	
	header("Location: ".WEB_PATH."/admin/campaigns.php");
	exit();
}
}

$RS = $objDB->Show("campaigns");
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
                                        "iDisplayLength" :15,
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
	<h2>Campaigns</h2>
		<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
		<thead>
		  <tr>
			<!--<th width="5%">&nbsp;</th>-->
			<th width="25%" align="left">Business Name</th>
			<th width="45%" align="left">Campaign Title</th>
			<!--<th width="46%" align="left">Description</th> -->
			<th width="15%" align="left">Start Date </th>
			<th width="15%" align="left">Expire Date </th>
			<th width="10%" align="left"></th>
			<!--<th width="5%" align="center">Approved</th>-->
		  </tr>
		  </thead>
		<tbody>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
				$where_clause = array();
				$where_clause['id'] = $Row['created_by'];
				$RSMerchant = $objDB->Show("merchant_user", $where_clause);
		  ?>
		  <tr>
		<!--	<td align="center"><input type="checkbox" name="id[]" value="<?=$Row['id']?>" <? if($Row['visible'] == 1) echo "checked";?> /></td> -->
			<td align="left"><?=$RSMerchant->fields['firstname']." ".$RSMerchant->fields['lastname']?></td>
			<td align="left"><a href="campaign-detail.php?id=<?=$Row['id']?>" ><?=$Row['title']?></a></td>
		<!--	<td align="left"><?=$Row['description']?></td> -->
			<td align="left"><?=date("m-d-Y", strtotime($Row['start_date']))?></td>
			<td align="left"><?=date("m-d-Y", strtotime($Row['expiration_date']))?></td>
			
			<td><a href="<?=WEB_PATH?>/admin/campaigns.php?id=<?=$Row['id']?>&action=delete">Delete</a></td>
			
			<!--<td align="center">-->
			<?
			//if($Row['visible'] == 1) echo "Yes"; else echo "No";
			?>
			<!--</td>-->
		  </tr>
		  <?
		  }
		  ?>
		
		  <?
		  }else{
		  ?>
		  <!--<tr>
			<td colspan="7" align="left">No Campaign is Found.</td>
		  </tr>-->
		  <?
		  }
		  ?>
		  </tbody>
		<tfoot>
			  <tr>
		<!--	<td align="center">
			<input type="submit" name="btnApprove" value="Approve"/>
			</td> -->
			
			<td>&nbsp;</td>
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
