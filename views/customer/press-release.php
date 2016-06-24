<?php
/******** 
@USE : press release list page
@PARAMETER : 
@RETURN : 
@USED IN PAGES : header.php, footer.php
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");

//$objDB = new DB();
/*$Sql_new = "SELECT * FROM press_release where status=1 order by id DESC";  
$RS_press_release = $objDB->Conn->Execute($Sql_new);*/
$RS_press_release = $objDB->Conn->Execute("SELECT * FROM press_release where status=? order by id DESC",array(1));
//echo $RS_press_release->RecordCount();


?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Press Release</title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">

<link href="<?php echo ASSETS_CSS; ?>/c/demo_page.css" rel="stylesheet" type="text/css">
<link href="<?php echo ASSETS_CSS; ?>/c/demo_table.css" rel="stylesheet" type="text/css">

<style>
#example_length
{
	display:none;
}
.dataTables_wrapper
{
	width:100%;
}
</style>
</head>
<body>
<?php
require_once CUST_LAYOUT."/header.php";
?>
<div id="content" class="cantent">
	<div class="my_main_div">
		<div id="contentContainer" class="contentContainer">
	<div class="press_release">
	<h1>Press Release</h1>
	<div class="stories_wrapper" style="" >
	 <table width="100%"  border="0" cellspacing="2" cellpadding="2" id="example">
	 <thead>
		<tr>
			<td ></td>
		</tr>
	</thead>	
	 <tbody>
	<?php
	if($RS_press_release->RecordCount()>0)
	{
		while($Row = $RS_press_release->FetchRow())
		{
	?>
	<tr>
		<td>
			<div class="stories">
				<h4>
					<a target="_blank" href="<?php echo WEB_PATH."/press-release-detail.php?id=".$Row['id'] ?>" ><?php echo $Row['title'] ?></a>
				</h4>
				<h5>
					<?php 
						//echo $Row['release_date'];
						echo date("F j, Y",strtotime($Row['release_date']));	
					?>
				</h5>
				<?php //echo $Row['description'] ?>
			</div> 
		</td>
	</tr>	
	<?php
		}
	}
	?>
	</tbody>
	</table>
	</div>
  	
	</div>	
	</div>
	
<?php require_once(CUST_LAYOUT."/before-footer.php");?>
</div><!--end of my_main_div-->
</div><!--end of content-->

<?php require_once(CUST_LAYOUT."/footer.php");?>

</body>
</html>
<?php
$_SESSION['req_pass_msg']="";
?>
<!--<script src="<?php echo WEB_PATH ?>/admin/js/jquery.js"></script>-->
<script type="text/javascript" src="<?php echo ASSETS_JS ?>/jquery.dataTables.js"></script>
<script type="text/javascript" >
			jQuery(document).ready(function() {
                            jQuery('#example').dataTable( {
                                'bFilter': false,
								 "aaSorting": [],
					 "sPaginationType": "full_numbers",
					  //"aLengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                      "iDisplayLength" : 5
				} );
			});
</script>
