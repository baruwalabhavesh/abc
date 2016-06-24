<?
//require_once("../classes/Config.Inc.php");
check_admin_session();
require_once(LIBRARY."/class.phpmailer.php");
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB();

if(isset($_REQUEST['checkreview']))
{	
	if(isset($_REQUEST['review_id']) && $_REQUEST['review_id']!="")
	{
		$sql_r = "select * from review_rating r inner join locations l on l.id=r.location_id  inner join campaigns c on r.campaign_id=c.id where r.id=".$_REQUEST['review_id'];
		$RS_r = $objDB->Conn->Execute($sql_r);
		//echo $RS_r->fields["review"];
		
		$array_c = array();
		$array_c['id'] = $RS_r->fields["customer_id"];		
		$RS_c = $objDB->Show("customer_user",$array_c);
	
		if($RS_r->RecordCount()>0)
		{
			//echo "We found review";
		}
		else
		{
			//echo "No review found";
			$_SESSION['err_msg']="No review found";
		}
	}
	else
	{
		//echo "Please enter review id";
		$_SESSION['err_msg']="Please enter review id";
	}
}
if(isset($_REQUEST['btnDeleteReview']))
{
	$sql_r = "select * from review_rating r inner join locations l on l.id=r.location_id  inner join campaigns c on r.campaign_id=c.id where r.id=". $_REQUEST['hdnereviewid'];
	$RS_r = $objDB->Conn->Execute($sql_r);
	$location_id = $RS_r->fields["location_id"];
			
	$array_where = array();
	$array_where['id'] = $_REQUEST['hdnereviewid'];
	$objDB->Remove("review_rating", $array_where);
	$_SESSION['err_msg']="Review deleted successfully";
	
	$old_avg_rating_sql = "select AVG(rating) avarage_rating  from review_rating where location_id = ".$location_id;
	$old_avg_rating_rs =  $objDB->Conn->Execute($old_avg_rating_sql);
	$old_avg_rating = $old_avg_rating_rs->fields['avarage_rating'];
	
	$update_array= array();
	$sql = "update locations set avarage_rating=".$old_avg_rating.",no_of_reviews=no_of_reviews-1,no_of_rating=no_of_rating-1 where id=".$location_id;
	$RS = $objDB->Conn->Execute($sql);	

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=ASSETS_CSS?>/a/template.css" rel="stylesheet" type="text/css">
</head>

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
		<div style="width: 100%; padding: 20px;">			
			Enter Review ID : <input type="text" name="review_id" id="review_id" size="5" value="<?php if(isset($_REQUEST['review_id'])) echo $_REQUEST['review_id']; else echo ""; ?>">
			<input type="submit" name="checkreview" id="checkreview" value="Submit">
			<?php 
			if(isset($_REQUEST['checkreview']))
			{				
			?>
					<div style="color: red; display: inline; margin-left: 15px;">
						<?php echo $_SESSION['err_msg']; ?>
					</div>
			<?php
			}
			if(isset($_REQUEST['btnDeleteReview']))
			{				
			?>
				<div style="color: green; display: inline; margin-left: 15px;">
					<?php echo $_SESSION['err_msg']; ?>
				</div>
			<?php				
			}
			?>
		</div>
		
		<?php
		if(isset($_REQUEST['checkreview']))
		{
			if($RS_r->RecordCount()>0)
			{
		?>
			<table width="100%" align="center"  border="0" cellspacing="2" cellpadding="2" class="tableAdmin reviewdelete">
			  <tr>
				<td width="20%" align="left"><b>Review :</b> </th>
				<td width="60%" align="left"><?php echo $RS_r->fields["review"]; ?></th>
			  </tr>
			  <tr>
				<td width="20%" align="left"><b>Reviewed By :</b>  </th>
				<td width="60%" align="left"><?php echo $RS_c->fields["firstname"]." ".$RS_c->fields["lastname"]; ?></th>
			  </tr>
			  <tr>
				<td width="20%" align="left"><b>Reviewed Date & time :</b>  </th>
				<td width="60%" align="left"><?php echo $RS_r->fields["reviewed_datetime"]; ?></th>
			  </tr>
			  
			  <tr>
				<td>&nbsp;</td>
				<td align="left">
				<input type="hidden" name="hdnereviewid" id="hdnereviewid" value="<?php echo $_REQUEST['review_id']; ?>" />
				<input type="submit" name="btnDeleteReview" id="btnDeleteReview" value="Delete" />
							<input type="submit" name="canreview" value="Cancel"  />
				</td>
			  </tr>
			 
			</table>
		<?php	
			}
		}
		else
		{
		?>
			
		<?php
		}
		?>
	  </form>
	<!--end of content--></div>
       <!--end of contentContainer--></div>
<!--end of Container--></div>


<?php
 $_SESSION['err_msg'] = "";
?>

</body>
</html>
