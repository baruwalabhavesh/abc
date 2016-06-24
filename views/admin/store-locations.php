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
		$objDB->Remove("locations", $array_where);
		
		   $array['location_id'] = $_REQUEST['id'];
		$RSDistribution = $objDB->Show("merchant_groups",$array);
			if($RSDistribution->RecordCount()>0){		
			while($RowDis = $RSDistribution->FetchRow()){
				//echo $Row['id'];
							$array_where2['group_id'] = $RowDis['id'];
							$objDB->Remove("merchant_subscribs", $array_where2);
			}
			}
			
			$array_where1['location_id'] = $_REQUEST['id'];
		$objDB->Remove("merchant_groups", $array_where1);
			
			
			
		header("Location: ".WEB_PATH."/admin/store-locations.php");
		exit();
	}
}

        
                
if(isset($_REQUEST['btnActive'])){
	foreach($_REQUEST['id'] as $id){
		$array_values = $where_clause = array();
		$array_values['active'] = 1;
		$where_clause['id'] = $id;
		$objDB->Update($array_values, "locations", $where_clause);
	}

}

$Sql = "SELECT L.*, MU.firstname, MU.lastname
		FROM locations L, merchant_user MU
		WHERE L.created_by = MU.id
		ORDER BY L.id DESC";
//echo $Sql."<hr>";
$RS = $objDB->Conn->Execute($Sql);


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
                                        "iDisplayLength" : 10,
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
	<h2>Store Locations</h2>
		<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example">
	  <thead>
	  <tr>
		<td colspan="8" align="left" style="color:#FF0000; "><?php 
                if(isset($_REQUEST['msg']))
                {
                    $_REQUEST['msg'];
                }
                ?>&nbsp;</td>
	  </tr>
	 
	  <tr>
	   <!-- <th width="5%" align="left">&nbsp;</th> -->
		<th width="20%" align="left">Business Name</th>
		<th width="36%" align="left">Address</th>
		<th width="10%" align="left">Phone</th>
		<!--<th width="11%" align="left">Website</th>-->
		<th width="7%" align="left">Status</th>
        <th width="7%" align="left"></th>
		<!--<th width="14%" align="left">Merchant</th>-->
	  </tr>
	   </thead>
	  <tbody>
	  <?
	  if($RS->RecordCount()>0){
	  	while($Row = $RS->FetchRow()){
	  ?>
	  <tr>
	  <!--  <td align="center"><input type="checkbox" name="id[]" value="<?=$Row['id']?>" <? if($Row['active'] == 1) echo "checked";?> /></td> -->
		<td><a href="location-detail.php?id=<?=$Row['id']?>"><?=$Row['location_name']?></a></td>
		<td><?=$Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip']?></td>
		<td><?=$Row['phone_number']?></td>
		<!--<td><?=$Row['website']?></td>-->
		<td>
		<?
		if($Row['active'] == 1) echo "Active"; else echo "Inactive";
		?>
		</td>
        <td><a href="<?=WEB_PATH?>/admin/store-locations.php?id=<?=$Row['id']?>&action=delete">Delete</a></td>
		<!--<td><?=$Row['firstname']." ".$Row['lastname']?></td>-->
        
	  </tr>
	  
	  <?
	  	}
	?>
	
	
	<?	
	  }else{
	  ?>
	  <!--<tr>
		<td colspan="7">No Location Found</td>
		</tr>-->
	  <?
	  }
	  ?>
	  </tbody>
	  <tfoot>
		<tr>
	   <!-- <td align="center">
		<input type="submit" name="btnActive" value="Active"/>
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
