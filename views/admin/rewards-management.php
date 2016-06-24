<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

$Sql = "SELECT RU.*, CU.firstname, CU.lastname, C.title
		FROM reward_user RU, campaigns C, customer_user CU
		WHERE RU.campaign_id=C.id AND CU.id=RU.customer_id
		ORDER BY RU.id DESC";
//echo $Sql."<hr>";		
//$RS = $objDB->Show("categories",''," Order By `order` ASC ");
$RS = $objDB->Conn->Execute($Sql);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reward Managment</title>
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
                                        "iDisplayLength" : 5,
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
		<h2>Reward Managment</h2>
	<form action="" method="post">
		<table width="100%"  class="tableAdmin" id="example">
		    <thead>
		  <tr>
			<th width="17%" align="left">Customer</th>
			<th width="17%" align="left">Campaign</th>
			<th width="17%" align="left">Earned Rewards</th>
		    <th width="17%" align="left">Referal Reward </th>
		    <th width="19%" align="left">Referal</th>
		    <th width="13%" align="left">Date</th>
		  </tr>
		  </thead>
		    <tbody>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
		  ?>
		  <tr>
			<td align="left"><?=$Row['firstname']." ".$Row['lastname']?></td>
			<td align="left"><?=$Row['title']?></td>
			<td align="left"><?=$Row['earned_reward']?></td>
		    <td align="left"><?=$Row['referral_reward']?></td>
		    <td align="left"><?=$Row['referred_customer_id']?></td>
		    <td align="left"><?=$Row['reward_date']?>

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
		  <!--<tr>
			<td colspan="6" align="left">No Reward User is Found.</td>
		  </tr>-->
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
