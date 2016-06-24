<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();
/*******get all unique timzone of system for update timezone  *************/
	$arr=file(WEB_PATH.'/admin/process.php?unique_timzone_list=yes');
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update Location Timezone List</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
<script src="<?=ASSETS_JS?>/a/jquery-1.7.2.min.js"></script>
<style type="text/css" title="currentStyle">
			@import "<?php echo ASSETS_CSS?>/a/demo_page.css";
			@import "<?php echo ASSETS_CSS?>/a/demo_table.css";
		</style>
<script type="text/javascript" language="javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
		 /******* initialize jQuery data table ****/
			$(document).ready(function() {
				$('#example').dataTable( {
					 "sPaginationType": "full_numbers",
					'bFilter': false,
					"aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
					"iDisplayLength" : 10,
					"aoColumns": [{"bSortable":false},null
                                          
                                        ],
                                          "aaSorting":[]
				});
				//jQuery(".table_loader").css("display","none");
				//jQuery(".datatable_container").css("display","block");
			} );
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
		<h2>Update Location Timezone List According To DST Format</h2>
	<form action="<?php echo WEB_PATH."/admin/process.php" ?>" method="post" >    <? 
                    if(isset($_REQUEST['msg']))
                    {
                    }
                    ?>
	<div style="color:#FF0000; "><?=$_SESSION['msg']?></div>
	<div width="100%"><table cellspacing="10px" width="100%" >
	
	<tr>
		<td width="100%">
		
		 <?
		if($total_records>0){
			?>
			<table width="100%"  border="0" cellspacing="1" cellpadding="10" class="tableMerchant"  id="example">
		<thead>
		
	  <tr>
		<th align="left" class="tableDealTh" width="2%"></th>
		<th align="left" class="tableDealTh">Timezone</th>
	</tr>
	  </thead>
		<tbody>
			<?php
			foreach($records_array as $Row)
			{
			?>
				<tr>
					<td><input type="checkbox" name="chk[]" value="<?php echo  $Row->timezone_name;?>" />
					</td>
					<td>
						<?php echo $Row->timezone_name; ?>
					</td>
				</tr>
			<?php
			}
			?>
			</table>
			<?php
		}
		?>
		</td>
	</tr>
	<tr  align="center" >
		<td  align="center" ><input type="submit" id="btnupdatetimezone" name="btnupdatetimezone" value="Update Timezone"/></td>
	</tr>
	<tr>
		<td>
			<div class="suCcesslist" style="display:none">
			<ul style="list-style:none">
				<li class="uploadingstatusli">This process will take few time. Updating in progres .......</li>
			</ul>
			</div>
	</td>
	</tr>
	
	</table>
	<?php 
	/********** set total number of location in hidden ***************/
	   $RS = $objDB->Show("locations" );
	?>
	<input type="hidden" name="hdn_total_location" id="hdn_total_location" value="<?php echo $RS->RecordCount(); ?>" />	
               
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


</body>
</html>
<?php 
$_SESSION['msg'] = "";
?>
<!-- -->
<script type="text/javascript">
jQuery("#btnupdatetimezone").click(function(){ 

});
/*    
jQuery("#btnupdatetimezone").click(function(){ 
jQuery(".suCcesslist").css("display","block");
	open_loader();
	//return false;
	
	var total_no_of_location = jQuery("#hdn_total_location").val();
	for(i=0;i<=total_no_of_location;i=i+10)
		{
		j = i+10;
		k = i+10;
		if(j>total_no_of_location)
		{	
			j=total_no_of_location;
			k= total_no_of_location;
		}
		j= (j-i);
		alert(j+"==="+i+"==="+k);
		alert( "<?=WEB_PATH?>/admin/process.php?startlimit=" + i +"&reset_timezonedst=yes"+"&endlimit=" + j);
			jQuery.ajax({
				type: "POST",
				url: "<?=WEB_PATH?>/admin/process.php",
				data: "startlimit=" + i +"&reset_timezonedst=yes"+"&endlimit=" + j,
				async:false,
				success: function(msg) {
					jQuery(".suCcesslist").append("<li>"+msg+"</li>");
				}
			}); 
			//jQuery(".suCcesslist ul").append("<li>10 records updated completely	</li>");
			if(k==total_no_of_location)
			{
				jQuery(".suCcesslist ul").append("<li class='sucess_msg'>Timezone update process completed succesfully.</li>");
				jQuery(".uploadingstatusli").css("display","none");
			}
		}
			
		close_loader();
		

});
*/
    
</script>