<?php
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

if(isset($_REQUEST['active']) && $_REQUEST['status']=="country")
{
    $array_values = $where_clause = array();
    $array_values['active'] = $_REQUEST['active'];
    $where_clause['id'] = $_REQUEST['id'];
    $objDB->Update($array_values, "country", $where_clause);
}

if(isset($_REQUEST['active']) && $_REQUEST['status']=="state")
{
    $array_values = $where_clause = array();
    $array_values['active'] = $_REQUEST['active'];
    $where_clause['id'] = $_REQUEST['id'];
    $objDB->Update($array_values, "state", $where_clause);
}

if(isset($_REQUEST['active']) && $_REQUEST['status']=="city")
{
    $array_values = $where_clause = array();
    $array_values['active'] = $_REQUEST['active'];
    $where_clause['id'] = $_REQUEST['id'];
    $objDB->Update($array_values, "city", $where_clause);
}
/*
if(isset($_REQUEST['delete']) && $_REQUEST['status']=="country")
{
	$RS_states = $objDB->Conn->Execute("SELECT id from state where country_id=". $_REQUEST['id']);
	$state_ids = $RS_states->fields['ids'];
	$objDB->Conn->Execute("remove from city where state_id in (".$state_ids.")");
	 	
	$array_where = array();
	$array_where['country_id'] = $_REQUEST['id'];
	$objDB->Remove("state", $array_where);
	
	$array_where = array();
	$array_where['id'] = $_REQUEST['id'];
	$objDB->Remove("country", $array_where);
}

if(isset($_REQUEST['delete']) && $_REQUEST['status']=="state")
{
	$array_where = array();
	$array_where['state_id'] = $_REQUEST['id'];
	$objDB->Remove("city", $array_where);
	
	$array_where = array();
	$array_where['id'] = $_REQUEST['id'];
	$objDB->Remove("state", $array_where);
}

if(isset($_REQUEST['delete']) && $_REQUEST['status']=="city")
{
	$array_where = array();
	$array_where['id'] = $_REQUEST['id'];
	$objDB->Remove("city", $array_where);
}

$RS_state = $objDB->Conn->Execute("SELECT c.name 'country_name',s.* from country c,state s where c.id=s.country_id Order By s.display_order ASC");

$RS_city = $objDB->Conn->Execute("SELECT c.name 'country_name',s.name 'state_name',ci.* from country c,state s,city ci where ci.state_id=s.id and c.id=s.country_id Order By ci.display_order ASC");
*/ 

$RS_country = $objDB->Show("country",''," Order By `name` ASC "); // for filter dropdown
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
			<h2>Country Management</h2>
			<form action="" method="post">
				<!--edit delete icons table-->
				<div>
					<table align="right"  cellspacing="10px" >
						<tr>
							<td>
								<a href="add-country.php"><img src="<?php echo ASSETS_IMG; ?>/a/icon-add.png"></a>
							</td>
						</tr>
					</table>
				<!--end edit delete icons table-->
				</div>
			<br/>
			<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example_country">
				<thead>
					<tr>
						<th width="40%" align="left">Country Name</th>
						<th width="10%" align="left">Status</th>
						<th width="40%" align="left">Actions</th>
					</tr>
				</thead>
				
			</table>
		
			<h2>State Management</h2>
			<br/>
			<?php 
			$RS_country->MoveFirst();
			//$RS_country = $objDB->Show("country",''," Order By `display_order` ASC ");
			?>
			Country  : <select name="country_filter_state" id="country_filter_state">
				<option value="0" >Please Select</option>
				<?php
				if($RS_country->RecordCount()>0)
				{
					while($Row = $RS_country->FetchRow())
					{
						?>
						<option value="<?php echo $Row['id'] ?>" ><?php echo $Row['name'] ?></option>
						<?php
					}
				}
				?>
			</select>
			<input id="filter_state" name="filter_state" type="button" value="Filter">
			<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example_state">
				<thead>
					<tr>
						<th width="15%" align="left">State Name</th>
						<th width="10%" align="left">Country Name</th>
						<th width="7%" align="left">Status</th>
						<th width="20%" align="left">&nbsp;</th>
					</tr>
				</thead>
				
			</table>
			
			<h2>City Management</h2>
			<br/>
			<br/>
			<?php 
			$RS_country->MoveFirst();
			//$RS_country = $objDB->Show("country",''," Order By `display_order` ASC ");
			?>
			Country  : <select name="country_filter_city" id="country_filter_city">
				<option value="0" >Please Select</option>
				<?php
				if($RS_country->RecordCount()>0)
				{
					while($Row = $RS_country->FetchRow())
					{
						?>
						<option value="<?php echo $Row['id'] ?>" ><?php echo $Row['name'] ?></option>
						<?php
					}
				}
				?>
			</select>
			State  : <select name="state_filter_city" id="state_filter_city">
				<option value="0" >Please Select</option>
				
			</select>
			<input id="filter_city" name="filter_city" type="button" value="Filter">
			<table width="100%"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin" id="example_city">
				<thead>
					<tr>
						<th width="15%" align="left">City Name</th>
						<th width="13%" align="left">State Name</th>
						<th width="12%" align="left">Country Name</th>
						<th width="7%" align="left">Status</th>
						<th width="20%" align="left">&nbsp;</th>
					</tr>
				</thead>
				
			</table>
	</form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
<script type="text/javascript">
	
	jQuery(document).ready(function(){
		
		var oCache = {
			iCacheLower: -1
		};

		function fnSetKey(aoData, sKey, mValue)
		{
			for (var i = 0, iLen = aoData.length; i < iLen; i++)
			{
				if (aoData[i].name == sKey)
				{
					aoData[i].value = mValue;
				}
			}
		}

		function fnGetKey(aoData, sKey)
		{
			for (var i = 0, iLen = aoData.length; i < iLen; i++)
			{
				if (aoData[i].name == sKey)
				{
					return aoData[i].value;
				}
			}
			return null;
		}

		function fnDataTablesPipeline(sSource, aoData, fnCallback) {
			var iPipe = 2; /* Ajust the pipe size */

			var bNeedServer = false;
			var sEcho = fnGetKey(aoData, "sEcho");
			var iRequestStart = fnGetKey(aoData, "iDisplayStart");
			var iRequestLength = fnGetKey(aoData, "iDisplayLength");
			var iRequestEnd = iRequestStart + iRequestLength;
			oCache.iDisplayStart = iRequestStart;

			/* outside pipeline? */
			if (oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper)
			{
				bNeedServer = true;
			}

			/* sorting etc changed? */
			if (oCache.lastRequest && !bNeedServer)
			{
				for (var i = 0, iLen = aoData.length; i < iLen; i++)
				{
					if (aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho")
					{
						if (aoData[i].value != oCache.lastRequest[i].value)
						{
							bNeedServer = true;
							break;
						}
					}
				}
			}

			/* Store the request for checking next time around */
			oCache.lastRequest = aoData.slice();

			if (bNeedServer)
			{
				if (iRequestStart < oCache.iCacheLower)
				{
					iRequestStart = iRequestStart - (iRequestLength * (iPipe - 1));
					if (iRequestStart < 0)
					{
						iRequestStart = 0;
					}
				}

				oCache.iCacheLower = iRequestStart;
				oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
				oCache.iDisplayLength = fnGetKey(aoData, "iDisplayLength");
				fnSetKey(aoData, "iDisplayStart", iRequestStart);
				fnSetKey(aoData, "iDisplayLength", iRequestLength * iPipe);

				$.getJSON(sSource, aoData, function (json) {
					/* Callback processing */
					oCache.lastJson = jQuery.extend(true, {}, json);

					if (oCache.iCacheLower != oCache.iDisplayStart)
					{
						json.aaData.splice(0, oCache.iDisplayStart - oCache.iCacheLower);
					}
					json.aaData.splice(oCache.iDisplayLength, json.aaData.length);

					fnCallback(json)
				});
			}
			else
			{
				json = jQuery.extend(true, {}, oCache.lastJson);
				json.sEcho = sEcho; /* Update the echo for each response */
				json.aaData.splice(0, iRequestStart - oCache.iCacheLower);
				json.aaData.splice(iRequestLength, json.aaData.length);
				fnCallback(json);
				return;
			}
		}
          
        var oTable_country = jQuery('#example_country').dataTable({
					"bFilter": true,
					"bSort" : false,
					"bLengthChange": false,
					"info": false,
					"sPaginationType": "full_numbers",
					"bProcessing": true,
					"bServerSide": true,
					"iDisplayLength": 10,
					"oLanguage": {
						"sEmptyTable": "No country found in the system. Please add at least one.",
						"sZeroRecords": "No country to display",
						"sProcessing": "Loading..."
					},
					"sAjaxSource": "<?php echo WEB_PATH; ?>/admin/process.php",
					"fnServerParams": function (aoData) {
						aoData.push({"name": "btnGetAllCountry", "value": true});
					}
				});
		jQuery(document).on("click",'.active_country', function () {
			var active=jQuery(this).attr("active");
			var id=jQuery(this).attr("id");
			jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'deactivateCountry=true&country_id='+id+'&active='+active,
				async:false,
				success:function(msg)
				{
					
					var obj = jQuery.parseJSON(msg);
					if(obj.status=="true")     
					{
						console.log(obj.status);
						oTable_country.fnDraw();
						console.log(obj.status);
					}
				}	
			});
		});
         
        var oTable_state = jQuery('#example_state').dataTable({
					"bFilter": true,
					"bSort" : false,
					"bLengthChange": false,
					"info": false,
					"sPaginationType": "full_numbers",
					"bProcessing": true,
					"bServerSide": true,
					"iDisplayLength": 10,
					"oLanguage": {
						"sEmptyTable": "No state found in the system. Please add at least one.",
						"sZeroRecords": "No state to display",
						"sProcessing": "Loading..."
					},
					"sAjaxSource": "<?php echo WEB_PATH; ?>/admin/process.php",
					"fnServerParams": function (aoData) {
						aoData.push({"name": "btnGetAllState", "value": true},{"name": "country_id", "value": jQuery('#country_filter_state').val()});
					}
				});
		jQuery(document).on("click",'.active_state', function () {
			var active=jQuery(this).attr("active");
			var id=jQuery(this).attr("id");
			jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'deactivateState=true&state_id='+id+'&active='+active,
				async:false,
				success:function(msg)
				{
					
					var obj = jQuery.parseJSON(msg);
					if(obj.status=="true")     
					{
						console.log(obj.status);
						oTable_state.fnDraw();
						console.log(obj.status);
					}
				}	
			});
		});  
		
		var oTable_city = jQuery('#example_city').dataTable({
					"bFilter": true,
					"bSort" : false,
					"bLengthChange": false,
					"info": false,
					"sPaginationType": "full_numbers",
					"bProcessing": true,
					"bServerSide": true,
					"iDisplayLength": 10,
					"oLanguage": {
						"sEmptyTable": "No city found in the system. Please add at least one.",
						"sZeroRecords": "No city to display",
						"sProcessing": "Loading..."
					},
					"sAjaxSource": "<?php echo WEB_PATH; ?>/admin/process.php",
					"fnServerParams": function (aoData) {
						aoData.push({"name": "btnGetAllCity", "value": true},{"name": "country_id", "value": jQuery('#country_filter_city').val()},{"name": "state_id", "value": jQuery('#state_filter_city').val()});
					}
				});
		jQuery(document).on("click",'.active_city', function () {
			var active=jQuery(this).attr("active");
			var id=jQuery(this).attr("id");
			jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'deactivateCity=true&city_id='+id+'&active='+active,
				async:false,
				success:function(msg)
				{
					
					var obj = jQuery.parseJSON(msg);
					if(obj.status=="true")     
					{
						console.log(obj.status);
						oTable_city.fnDraw();
						console.log(obj.status);
					}
				}	
			});
		}); 
		
		
		jQuery("#filter_state").click(function(){
			var sel_country = jQuery("#country_filter_state").val();
			console.log(sel_country);
			oTable_state.fnDraw();
		});
				     
		jQuery('#country_filter_city').change(function(){
			var change_value=this.value;
			
			jQuery.ajax({
				type:"POST",
				url:'process.php',
				data :'btngetstateofcountry=true&country_id='+change_value,
				async:false,
				success:function(msg)
				{
					var obj = jQuery.parseJSON(msg);
					if (obj.status=="false")     
					{
						jQuery("#state_filter_city").html(obj.html);
					}
					else
					{
						jQuery("#state_filter_city").html(obj.html);
					}
				}
			});
		
		});
    
		
		jQuery("#filter_city").click(function(){
			var sel_country = jQuery("#country_filter_city").val();
			console.log(sel_country);
			if(sel_country==0)
			{
				//alert("please select country");
				//return false;
			}
			var sel_state = jQuery("#state_filter_city").val();
			console.log(sel_state);
			if(sel_state==0)
			{
				//alert("please select state");
				//return false;
			}
			oTable_city.fnDraw();
		});
	});
</script>
