<?php
/******** 
@USE : press release detail page
@PARAMETER : 
@RETURN : 
@USED IN PAGES : header.php, press-release.php
*********/
//require_once("classes/Config.Inc.php");
//include_once(SERVER_PATH."/classes/DB.php");
//include_once(SERVER_PATH."/classes/JSON.php");

//$objDB = new DB('read');
/*$Sql_new = "SELECT * FROM press_release where status=1 and id=".$_REQUEST['id'];  
$RS_press_release = $objDB->Conn->Execute($Sql_new);*/
$RS_press_release = $objDB->Conn->Execute("SELECT * FROM press_release where status=? and id=?",array(1,$_REQUEST['id']));

//echo $RS_press_release->RecordCount();exit;


?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Press Release Details</title>
<?php require_once(CUST_LAYOUT."/head.php"); ?>
<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo ASSETS_CSS; ?>/c/template.css" rel="stylesheet" type="text/css">
<style>
#example_length
{
	display:none;
}
</style>
</head>
<body>
<?php
require_once(CUST_LAYOUT."/header.php");
?>
<div id="content" class="cantent">
	<div class="my_main_div">
		<div id="contentContainer" class="contentContainer">
		
	<!--<h1>Press Release Detail</h1> -->
	<div class="stories_wrapper" style="" >
	
	<?php
	if($RS_press_release->RecordCount()>0)
	{
		while($Row = $RS_press_release->FetchRow())
		{
	?>
	
			<div class="stories">
				<h4>
					<?php echo $Row['title'] ?>
				</h4>
				<h5>
					<?php 
						//echo $Row['release_date'];
						echo date("F j, Y",strtotime($Row['release_date']));	
					?>
				</h5>
				<?php echo $Row['description'] ?>
			</div> 
		
	<?php
		}
	}
	?>
	
	</div>
  
  </div>
	
	<?php require_once(CUST_LAYOUT."/before-footer.php");?>
</div><!--end of my_main_div-->
</div><!--end of content-->

<?php require_once(CUST_LAYOUT."/footer.php");?>

</body>
</html>
