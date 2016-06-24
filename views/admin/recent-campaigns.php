<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Recently Added Campaigns</title>
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
</head>

<body>
     <div id="container">

              <!---start header---->
	
		<?
		require_once(ADMIN_LAYOUT."/header.php");
		?>
		<div id="contentContainer">

	
	

	
<?
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
$RS = $objDB->Show("campaigns","", " ORDER BY id DESC LIMIT 10");
?>
<h2>Recently Added Campaigns</h2>
<table width="780px"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin">
		  <tr>
			<th width="10%" align="left">Business Name</th>
			<th width="13%" align="left">Campaign Title</th>
			<!--<th width="46%" align="left">Description</th>-->
			<th width="10%" align="left">Start Date </th>
			<th width="11%" align="left">Expire Date </th>
			<!--<th width="5%" align="center">Approved</th>-->
		  </tr>
		  <?
		  if($RS->RecordCount()>0){
		  	while($Row = $RS->FetchRow()){
				$where_clause = array();
				$where_clause['id'] = $Row['created_by'];
				$RSMerchant = $objDB->Show("merchant_user", $where_clause);
		  ?>
		  <tr>
			<td align="left"><?=$RSMerchant->fields['firstname']." ".$RSMerchant->fields['lastname']?></td>
			<td align="left"><?=$Row['title']?></td>
			<!--<td align="left"><?=$Row['description']?></td>-->
			<td align="left"><?=date("m-d-Y", strtotime($Row['start_date']))?></td>
			<td align="left"><?=date("m-d-Y", strtotime($Row['expiration_date']))?></td>
			<!--<td align="center">
			<?
			//if($Row['visible'] == 1) echo "Yes"; else echo "No";
			?>
			</td> -->
		  </tr>
		  <?
		  }
		  ?>
		  <tr>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		  <?
		  }else{
		  ?>
		  <tr>
			<td colspan="6" align="left">No Campaign is Found.</td>
		  </tr>
		  <?
		  }
		  ?>
		</table>
		                    <!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>
</body>
</html>


