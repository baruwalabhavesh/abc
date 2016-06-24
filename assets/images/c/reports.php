<?
require_once("../classes/Config.Inc.php");

check_merchant_session();
include_once(SERVER_PATH."/classes/DB.php");



require('gapi.class.php');


///-- For google analytic reports ///

define('ga_account','punitapatel26@gmail.com');
define('ga_password','puni2689');
define('ga_profile_id'  ,'65247208');
 
$ga = new gapi(ga_account,ga_password);


$dimensions = array('customVarValue1');
$metrics    = array('visits');
$filter = 'ga:customVarValue1 != yes &&  ga:customVarValue1 != no &&  ga:customVarValue1 != 123 &&  ga:customVarValue1 != 5 && year == '.date("Y").' && month == '.date("m");
$ga->requestReportData(ga_profile_id, $dimensions, $metrics,'-visits',$filter,'','',1,25);
$gaResults = $ga->getResults();
$i=1;
$str_campaign_names_for_ga = "";
$str_campaign_total_visits = array();
foreach($gaResults as $result)
{
 // echo $result->getCustomVarValue1()."  ".$result->getVisits()."<br />";
 $str_campaign_names_for_ga.= "'".$result->getCustomVarValue1()."',";
 array_push($str_campaign_total_visits,intval($result->getVisits()));
}
$str_campaign_names_for_ga = trim($str_campaign_names_for_ga,",");
$str_campaign_total_visits = implode(",",$str_campaign_total_visits);



$objDB = new DB();
$array = array();
$months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
$str_activated = $str_expired = $str_campaigns = $str_generated_coupons = $str_redeemed_coupons = "";
$str_activated1 = $str_expired1 = $str_campaigns1 = "";

foreach($months as $month){
	$from_date = date("Y")."-".$month."-01 00:00:00";
	$to_date = date("Y")."-".$month."-31 23:59:59";
	$from_date = urlencode($from_date);
	$to_date = urlencode($to_date);
// -- for active campaigns
	$arr=file(WEB_PATH.'/merchant/process.php?active_campaign_list_of_month=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
	if($total_records>0){
		foreach($records_array as $R)
		{
			$str_activated .= $R->total.",";
			if($month == date('m') )
			{
				$total_active_campaigns = $R->total;
			}
		}
	}
// --- for all campaigns

	$arr=file(WEB_PATH.'/merchant/process.php?campaign_list_of_month=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	//if($month == date('m') )
	//{
	//	$total_all_campaigns = $total_records;
	//}
	if($total_records>0){
		foreach($records_array as $R)
		{
			$str_campaigns .= $R->total.",";
		}
	}
// --- for expired campigns

	$arr=file(WEB_PATH.'/merchant/process.php?expired_campaign_list_of_month=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
	if($total_records>0){
		foreach($records_array as $R)
		{
			$str_expired .= $R->total.",";
			if($month == date('m') )
			{
				$total_expired_campaigns = $R->total;
			}
		}
	}
//-- For generated coupons
	$arr=file(WEB_PATH.'/merchant/process.php?generated_coupon_monthly=yes&mer_id='.$_SESSION["merchant_id"].'&from_date='.$from_date.'&to_date='.$to_date);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_records= $json->total_records;
	$records_array = $json->records;
	
	if($total_records>0){
		foreach($records_array as $R)
		{
			$str_generated_coupons .= $R->total.",";
			//if($month == date('m') )
			//{
			//	$total_expired_campaigns = $R->total;
			//}
		}
	}

	

}

// -- For redeemed coupons
	
	$arr=file(WEB_PATH.'/merchant/process.php?redeem_coupon_monthly=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	
	$str_redeemed_coupons = $json->records;
$str_redeemed_coupons =  json_encode($str_redeemed_coupons);
//-- End for redeem coupons


// -- For monthly used point
	
	$arr=file(WEB_PATH.'/merchant/process.php?campaignwise_used_points=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	
	$str_used_points = $json->records;
$str_used_points =  json_encode($str_used_points);
//-- End For monthly used point

// -- For avarage coupons
$arr=file(WEB_PATH.'/merchant/process.php?get_avarage_coupon_percentage=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	$total_generated = $json->total_generated;
	$redeemed_total = $json->redeemed_total;
	$not_redeemed_total = ($total_generated - $redeemed_total);
	
	$r_per = round(($redeemed_total * 100)/$total_generated);
	$nr_per = round(($not_redeemed_total * 100)/$total_generated);
//-- End for avarage coupons



// --- For Locationwise created campaigns
	
	$arr=file(WEB_PATH.'/merchant/process.php?locationwise_avarage_campaigns=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	
	$str_loactionwise_campaign = $json->records;
$str_loactionwise_campaign =  json_encode($str_loactionwise_campaign);
$locations = $json->locations ;
// --- End of Locationwise created campaigns

//-- For  merchantwise campaigns
    $arr=file(WEB_PATH.'/merchant/process.php?merchantwise_campaigns=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);

	$merchant_name_array =json_encode( $json->merchant_name_array);
	$merchant_tot_campaign =json_encode( $json->merchant_tot_campaign);
	$merchant_display_id_array = json_encode( $json->merchant_display_id_array);
//--- End of merchantwise campaigns


//-- for campaigns
$str_activated = substr($str_activated, 0, strlen($str_activated)-1);
$str_expired = substr($str_expired, 0, strlen($str_expired)-1);
$str_campaigns = substr($str_campaigns, 0, strlen($str_campaigns)-1);

$t_avg = $total_active_campaigns + $total_expired_campaigns;
$a_avg = round(($total_active_campaigns * 100)/$t_avg);
$e_avg = round(($total_expired_campaigns * 100)/$t_avg);
//-- end of campaigns

// -- for coupons
$str_generated_coupons= substr($str_generated_coupons, 0, strlen($str_generated_coupons)-1);

// -- End of coupon



// --  For campaignwise total point used

$arr=file(WEB_PATH.'/merchant/process.php?campaignswise_usedtotPoint=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	
	$str_campaign_total_point = $json->campaign_total_values;  //  $str_campaign_total_point $str_campaign_names
$str_campaign_names =  $json->campaigns;

//$locations = $json->locations ;
// --- End  For campaignwise total point used

// --  For campaignwise total shared point used
$arr=file(WEB_PATH.'/merchant/process.php?campaignswise_usedsharedPoint=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	
	$str_campaign_total_point2 = $json->campaign_total_values;  //  $str_campaign_total_point $str_campaign_names
$str_campaign_names2=  $json->campaigns;
// --- End  For campaignwise total shared point used

// --  For campaignwise total redeemed point used
$arr=file(WEB_PATH.'/merchant/process.php?campaignswise_usedRdeemedPoint=yes&mer_id='.$_SESSION['merchant_id']);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
	
	$str_campaign_total_point1 = $json->campaign_total_values;  //  $str_campaign_total_point $str_campaign_names
$str_campaign_names1 =  $json->campaigns;
// --- End  For campaignwise total redeemed point used deal value

function listofmonths()
{
	?>
	<li class="active_li"  >
					<a month="01" title="CLICK HERE TO FILTER RESULT">Jan</a>
				</li>
				<li >
					<a month="02" title="CLICK HERE TO FILTER RESULT">Feb</a>
				</li>
				<li >
					<a month="03" title="CLICK HERE TO FILTER RESULT">Mar</a>
				</li>
				<li >
					<a month="04" title="CLICK HERE TO FILTER RESULT">Apr</a>
				</li>
				<li >
					<a month="05" title="CLICK HERE TO FILTER RESULT">May</a>
				</li>
				<li >
					<a month="06" title="CLICK HERE TO FILTER RESULT">Jun</a>
				</li>
				<li >
					<a month="07" title="CLICK HERE TO FILTER RESULT">Jul</a>
				</li>
				<li >
					<a month="08" title="CLICK HERE TO FILTER RESULT">Aug</a>
				</li>
				<li >
					<a month="09" title="CLICK HERE TO FILTER RESULT">Sep</a>
				</li>
				<li >
					<a month="10" title="CLICK HERE TO FILTER RESULT">Oct</a>
				</li><li >
					<a month="11" title="CLICK HERE TO FILTER RESULT">Nov</a>
				</li>
				<li >
					<a month="12" title="CLICK HERE TO FILTER RESULT">Dec</a>
				</li>
<?php	
}
$cnt1 = 1;
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ScanFlip</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?=WEB_PATH?>/merchant/templates/template.css" rel="stylesheet" type="text/css">
<script src="http://code.jquery.com/jquery-latest.js"></script>

<script src="<?=WEB_PATH?>/js/jquery.paginate.js"></script>
<link href="<?=WEB_PATH?>/css/style1.css" rel="stylesheet" type="text/css">
<script>
	
	
</script>

 
</head>

<body>
<div style="width:100%;text-align:center;">
	<style>
			.report_list li {
				background-color: #F5F5F5;
				float: left;
				list-style: none outside none;
				padding: 5px 20px;
				width: 38px;
			    }
			.report_list li a{
				cursor: pointer;
				
			}
			.report_list {
				background-color: #F5F5F5;
				padding: 0 0 0 10px;
				width: 100%;
			    }
			    .active_li
			    {
				background-color: #3C9AF4 !important;
			    }
			     .active_li a
			    {
				color: white !important;
			    }
			    .tree_icon
				{
					 background: url(./images/plus_minus.jpg) no-repeat scroll 0 0 transparent;	
					  background-position: top left;		
					 height: 15px;
					 width : 14px;
					 position:absolute;
					 margin-top: 2px;
				}
				.mainIcon
{
	 background: url(./images/plus_minus.jpg) no-repeat scroll 0 0 transparent;	
	  background-position: top left;		
	 height: 15px;
	 width : 14px;
	 position:absolute;
	 margin-top: 2px;
	
}
.mainIcon_hover
{
	background-position: 0px -35px;
}
.mainIcon_minus
{
   background-position: -15px 0px;  
}
.mainIcon_minus_hover
{
	background-position: -15px -35px;
}
		</style>
<style>
	.reportContent{
		  background-color: #FFFFFF;
    border: 1px solid #D6D6D6;
    color: #000000;
    font-family: Arial,Helvetica,sans-serif;
    margin-left: auto;
    margin-right: auto;
    min-height: 300px;
    padding: 10px;
    text-align: left;
    width: 956px;
	}
</style>
<!--<script src="<?php echo WEB_PATH ?>/admin/js/jquery.js"></script>-->
<!---start header---->
	<div>
		<?
		require_once(SERVER_PATH."/merchant/templates/header.php");
		?>
		<!--end header--></div>
	<div id="contentContainer">
	
    <div style="margin-left:auto;margin-right:auto;" id="fadeshow11">

	<!--end of slide--></div>
	<div id="content">


  <h1>Reports</h1>
 
	<div id="media-upload-header">
		<ul id="sidemenu">
			<li id="tab-type" class="campaigns"><a class="current" >Campaigns</a></li>
			<li id="tab-type" class="Redeem_coupons"><a >Coupons</a></li>
			<li id="tab-type" class="location"><a >Locations</a></li>
			<li id="tab-type" class="Sub_merchant" style="display: none;"><a >Sub merchant</a></li>
			<li id="tab-type" class="points"><a >Points</a></li>
			<li id="tab-type" class="google_analytics"><a >Analytics Report</a></li>
		</ul>
	</div>
	<div>
		
		<!-- For campaigns -->
		<!--<script src="<?=WEB_PATH?>/admin/js/jquery.js"></script>-->
<script src="<?=WEB_PATH?>/admin/js/highcharts.js"></script>

		
		
		
		<div id="campaigns" class="tabs" style="display: block;">
			<div class="reportContent">
	<div id="container6" style="display:none;min-width: 250px; height: 350px; margin: 0 auto"></div>
	<select id="s1-container2-generated_campaigns">
	 <?php
  for($y=2000;$y<=date('Y');$y++)
  { ?>
	<option value="<?php echo $y; ?>" <?php if(date('Y')== $y ){ echo "selected"; } ?> > <?php echo $y; ?> </option>
  <?php }
  ?>
	</select>
	<div id="container2" style="min-width: 250px; height: 350px; margin: 0 auto"></div>
	<!--<a  id="toggle-generated_report">plus</a>-->
	<div class="mainIcon mainIcon_minus" id="toggle-generated_report1" > </div> <span style="padding-left: 20px">Collapse</span> 
	<ul id="ul_generated_campaigns" class="report_list">
				<?php listofmonths(); ?>
	
			</ul>
	<div style="clear: both" ></div>
	
	<div id="generated_report1">
	<div id="generated_report">
	
	</div>
	<div id="demo_generated_campaigns" class="jPaginate" ></div>
        </div>
	<hr />
        <select id="s1-container1-active_campaigns">
	 <?php
  for($y=2000;$y<=(date('Y')+2);$y++)
  { ?>
	<option value="<?php echo $y; ?>" <?php if(date('Y')== $y ){ echo "selected"; } ?> > <?php echo $y; ?> </option>
  <?php }
  ?>
	</select>
	<div id="container1" style="min-width: 250px; height: 350px; margin: 0 auto"></div>
	<div class="mainIcon mainIcon_minus" id="toggle-activated_report1" ></div> <span style="padding-left: 20px">Collapse</span> 
	<ul id="ul_activated_campaigns" class="report_list">
				<?php listofmonths(); ?>

			</ul>
	<div style="clear: both" ></div>
        <div id="activated_report1">
	<div id="activated_report"></div>
	<div id="demo_activated_campaigns" class="jPaginate" ></div>
        </div>
	<hr />
        <select id="s1-container3-expire_campaigns">
	 <?php
  for($y=2000;$y<=(date('Y')+2);$y++)
  { ?>
	<option value="<?php echo $y; ?>" <?php if(date('Y')== $y ){ echo "selected"; } ?> > <?php echo $y; ?> </option>
  <?php }
  ?>
	</select>
	<div id="container3" style="min-width: 250px; height: 350px; margin: 0 auto"></div>
	<div class="mainIcon mainIcon_minus" id="toggle-expired_report1" ></div> <span style="padding-left: 20px">Collapse</span> 
	<ul id="ul_expired_campaigns" class="report_list">
				<?php listofmonths(); ?>

			</ul>
	<div style="clear: both" ></div>
        <div id="expired_report1">
	<div id="expired_report"></div>
	<div id="demo_expired_campaigns" class="jPaginate" ></div>
        </div>
	    <!--end of content--></div>
			
			
		</div>
		
		<!-- end for campaigns -->
		<!-- fro redeem coupons -->
		
		<div id="Redeem_coupons" class="tabs" style="display: none;">
			<div class="reportContent">
                            <select id="s1-container5-generated_coupons">
				<?php
			 for($y=2000;$y<=(date('Y')+2);$y++)
			 { ?>
			       <option value="<?php echo $y; ?>" <?php if(date('Y')== $y ){ echo "selected"; } ?> > <?php echo $y; ?> </option>
			 <?php }
			 ?>
			       </select>
			<div id="container5" style="min-width: 250px; height: 350px; margin: 0 auto"></div>
			<div class="mainIcon mainIcon_minus" id="toggle-generated_coupon_report1" ></div> <span style="padding-left: 20px">Collapse</span> 
			<ul id="ul_generated_coupons" class="report_list">
				<?php listofmonths(); ?>

			</ul>
			
			<div style="clear: both"></div>
                        <div id="generated_coupon_report1">
			<div id="generated_coupon_report"></div>
			<div id="demo_generated_coupons" class="jPaginate" ></div>
                        </div>
			<hr />
                        <select id="s1-container4-reddemed_coupons">
				<?php
			 for($y=2000;$y<=(date('Y')+2);$y++)
			 { ?>
			       <option value="<?php echo $y; ?>" <?php if(date('Y')== $y ){ echo "selected"; } ?> > <?php echo $y; ?> </option>
			 <?php }
			 ?>
			       </select>
			<div id="container4" style="min-width: 250px; height: 350px; margin: 0 auto"></div>
			<div class="mainIcon mainIcon_minus" id="toggle-redeemed_coupon_report1" ></div> <span style="padding-left: 20px">Collapse</span> 
			<ul id="ul_redeemed_coupons" class="report_list">
				<?php listofmonths(); ?>

			</ul>
			
			<div style="clear: both"></div>
                        <div id="redeemed_coupon_report1">
			<div id="redeemed_coupon_report"></div>
			<div id="demo_redeemed_coupons" class="jPaginate" ></div>
                        </div>
			<hr />
			<div id="container7" style="min-width: 250px; height: 350px; margin: 0 auto"></div>
			</div>
		</div>
		<!-- end of for redeem coupons -->
		
		<!-- start for location	-->
		<div id="location" class="tabs" style="display: none;">
			<h1>locations</h1>
			<div class="reportContent">
			<div id="container8" style="min-width: 250px; height: 350px; margin: 0 auto"></div></div>
				<div class="mainIcon mainIcon_minus" id="toggle-locationwise_campaigns_report1" ></div> <span style="padding-left: 20px">Collapse</span> 
			  <table width="100%" cellspacing="0" id="ul_locationwise_campaigns"  ><tr style="background-color: #F5F5F5" >
				<?php
				$arr1=file(WEB_PATH.'/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id='.$_SESSION['merchant_id']);
				if(trim($arr1[0]) == "")
				{
					unset($arr1[0]);
					$arr1 = array_values($arr1);
				}
				$json1 = json_decode($arr1[0]);
				$total_records1= $json1->total_records;
				$records_array1 = $json1->records;
			$cnt =0 ;
				if($total_records1 > 0){
				  foreach($records_array1 as $Row)
				  {
				?>
				<td style="padding: 10px" <?php if($cnt == 0){ echo "class='active_li'";} ?>>
					<a location_id="<?=$Row->id?>" style="cursor: pointer"  ><?=$Row->location_name?></a>
				</td>
				<?php
				$cnt++;
				}
				}
				?>

			</tr></table>
			
			<div style="clear: both"></div>
                        <div id="locationwise_campaigns_report1">
			<div id="locationwise_campaigns_report"></div>
			<div id="demo_locationwise_campaigns" class="jPaginate" ></div>
                        </div>
		</div>
		<!-- end of for locations -->
		
		<!-- start for Sub merchant	-->
		<div id="Sub_merchant" class="tabs" style="display: none;">
			
			<div class="reportContent">
			<div id="container9" style="min-width: 250px; height: 350px; margin: 0 auto"></div></div>
			<div class="mainIcon mainIcon_minus" id="toggle-merchantwise_campaigns_report1" ></div> <span style="padding-left: 20px">Collapse</span> 
			<table width="100%" cellspacing="0" id="ul_merchantwise_campaigns"  ><tr style="background-color: #F5F5F5" >
				<?php
				$cnt =0 ;
				$arr_m_id = json_decode($merchant_display_id_array);
				$arr_m_name = json_decode($merchant_name_array);
				for($i=0;$i<count($arr_m_id);$i++)
				{
				?>
				<td style="padding: 10px;"  <?php if($cnt == 0){ echo "class='active_li'";} ?>>
					<a merchant_id="<?=$arr_m_id[$i]?>" style="cursor: pointer"><?php echo $arr_m_name[$i];   ?></a>
				</td>
				<?php     $cnt++;   }?>

			</tr></table>
			
			<div style="clear: both"></div>
                        <div id="merchantwise_campaigns_report1">
			<div id="merchantwise_campaigns_report"></div>
			<div id="demo_merchantwise_campaigns" class="jPaginate" ></div>
                        </div>
		</div>
		<!-- end of for sub merchant -->
		
		<!-- start for Points	-->
		<div id="points" class="tabs" style="display: none;">
			
			<div class="reportContent">
			
			<div id="container10" style="min-width: 250px; height: 350px; margin: 0 auto"></div>
			<div class="mainIcon mainIcon_minus" id="toggle-usedpoint_report1" ></div> <span style="padding-left: 20px">Collapse</span> 
			<ul id="ul_usedpoints" class="report_list">
				<?php listofmonths(); ?>

			</ul>
			
			<div style="clear: both"></div>
                        <div id="usedpoint_report1">
			<div id="usedpoint_report"></div>
			<div id="demo_usedpoints" class="jPaginate" ></div>
                        </div>
			<hr />
			<!--<div id="container11" style="min-width: 250px; height: 350px; margin: 0 auto"></div></div>-->
			</div>
		
		</div>
		<!-- end of for points -->
		<div id="google_analytics" class="tabs" style="display: none;width: 980px">
			
			  <button id="authorize-button" style="visibility: hidden">
        Authorize Analytics</button>
<br />
  
  <select id="ga_select_1" name="ga_select_1">
	<option value="01" <?php if(date('m') == '01'){ echo "selected" ;} ?> >January</option>
	<option value="02" <?php if(date('m') == '02'){ echo "selected" ;} ?> >February</option>
	<option value="03" <?php if(date('m') == '03'){ echo "selected" ;} ?>  >March</option>
	<option value="04" <?php if(date('m') == '04'){ echo "selected" ;} ?> >April</option>
	<option value="05" <?php if(date('m') == '05'){ echo "selected" ;} ?> >May</option>
	<option value="06" <?php if(date('m') == '06'){ echo "selected" ;} ?> >Jun</option>
	<option value="07" <?php if(date('m') == '07'){ echo "selected" ;} ?> >July</option>
	<option value="08" <?php if(date('m') == '08'){ echo "selected" ;} ?> >August</option>
	<option value="09" <?php if(date('m') == '09'){ echo "selected" ;} ?> >September</option>
	<option value="10" <?php if(date('m') == '10'){ echo "selected" ;} ?>>October</option>
	<option value="11" <?php if(date('m') == '11'){ echo "selected" ;} ?> >November</option>
	<option value="12" <?php if(date('m') == '12'){ echo "selected" ;} ?> >December</option>
  </select>
   &nbsp;&nbsp;&nbsp; <select id="ga_select_2" name="ga_select_2">
  <?php
  for($y=2000;$y<=date('Y');$y++)
  { ?>
	<option value="<?php echo $y; ?>" <?php if(date('Y')== $y ){ echo "selected"; } ?> > <?php echo $y; ?> </option>
  <?php }
  ?>
  </select>
  <input type="button" name="btn_load_GA" id="btn_load_GA" value="Apply" />
  <div id="div_chart_error" align="center" ></div>
  
<div id="container16" style="width: 980px"></div>
<div id="container12" style="width: 980px"></div>
<div id="container13" style="width: 980px"></div>
<div id="container14" style="width: 980px"></div>
		</div>

<div class="clear">&nbsp;</div>
<!--end of content--></div>
<!--end of contentContainer--></div>

<!---------start footer--------------->
       <div>
		<?
		require_once(SERVER_PATH."/merchant/templates/footer.php");
		?>
		<!--end of footer--></div>
	
</div>
<?
$_SESSION['msg'] = "";
?>
</body>
</html>
<script language="javascript">
	function paginate(counter,demo_div,report_div){
			var cnt = counter;              
                        cnt = Math.ceil(parseInt(cnt) - 1);
                
                        if(cnt > 1)
                        {
                             
				jQuery('#'+demo_div).paginate({
                                        count: cnt,
                                        start: 1,
                                        display: 3,
                                        border: true,
                                        border_color: '#BFBFBF',
                                        text_color: 'black',
                                        background_color: 'white',        
                                        border_hover_color: 'black',
                                        text_hover_color: '#000',
                                        background_hover_color: '#fff', 
                                        images: false,
                                        mouse: 'press',
                                        onChange: function(page){
                                           
								jQuery('._current','#'+report_div).removeClass('_current').hide();
								 jQuery('#'+report_div+' #p'+page).addClass('_current').show();
							   }
                                });
                        }
	}

		
	$("div[id^='toggle-']").toggle(function(){
	    
	       var a = $(this).attr("id").split("-");
	       $("#"+a[1]).slideUp("fast");
			$(this).removeClass('mainIcon_minus'); 
			       $(this).next("span").text("Exapnd");
															       
	      },
	      function(){
	      
	       var a = $(this).attr("id").split("-");
	       $("#"+a[1]).slideDown("slow");
	            
	        $(this).addClass('mainIcon_minus');
	       $(this).next("span").text("Collapse");
			       
			      }
       );
	
	
	$(document).ready(function(){
		var v= $("select[id^='s1-container2-']").val();
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id=<?php echo $_SESSION['merchant_id'] ?>&list_generated_campaign=true&month=01&year='+v,
		    success:function(msg)
		    {
			  $("#generated_report").html(msg);
			 $('#demo_generated_campaigns').html("");
			  	paginate($("#hdn_cnt").val(),"demo_generated_campaigns","generated_report");
			}
		});
		var v= $("select[id^='s1-container1-']").val();
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id=<?php echo $_SESSION['merchant_id'] ?>&list_activated_campaign=true&month=01&year='+v,
		    success:function(msg)
		    {
			
			  $("#activated_report").html(msg);
			  $('#demo_activated_campaigns').html("");
			  paginate($("#hdn_cnt1").val(),"demo_activated_campaigns","activated_report");
			 
			}
		});
			var v= $("select[id^='s1-container3-']").val();
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_expired_campaign=true&month=01&year='+v,
		    success:function(msg)
		    {
			
			  $("#expired_report").html(msg);
			    
			  $('#demo_expired_campaigns').html("");
			  paginate($("#hdn_cnt2").val(),"demo_expired_campaigns","expired_report");
			
			}
		});
		var v= $("select[id^='s1-container5-']").val();
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_generated_coupons=true&month=01&year='+v,
		    success:function(msg)
		    {
			
			  $("#generated_coupon_report").html(msg);
			$('#demo_generated_coupons').html("");
			  paginate($("#hdn_cnt3").val(),"demo_generated_coupons","generated_coupon_report");
			}
		});
		var v= $("select[id^='s1-container4-']").val();
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_redeemed_coupons=true&month=01&year='+v,
		    success:function(msg)
		    {
			    $("#redeemed_coupon_report").html(msg);
			    
			  $('#demo_redeemed_coupons').html("");
			  paginate($("#hdn_cnt4").val(),"demo_redeemed_coupons","redeemed_coupon_report");
			
			 
			}
		});
		
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_campaignwise_used_points=true&month=01',
		    success:function(msg)
		    {
			    $("#usedpoint_report").html(msg);
			    
			  $('#demo_usedpoints').html("");
			  paginate($("#hdn_cnt4").val(),"demo_usedpoints","usedpoint_report"); 
			
			 
			}
		});
		var loc1_1 = $("#ul_locationwise_campaigns td:first").find("a").attr("location_id");
		
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_locationwise_campaigns=true&l_id='+loc1_1,
		    success:function(msg)
		    {
			  $("#locationwise_campaigns_report").html(msg);
			   $('#demo_locationwise_campaigns').html("");
			  paginate($("#hdn_cnt5").val(),"demo_locationwise_campaigns","locationwise_campaigns_report");
			   
			
			}
		});
		var usr1_1 = $("#ul_merchantwise_campaigns td:first").find("a").attr("merchant_id");
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_merchantwise_campaigns=true&m_id='+usr1_1,
		    success:function(msg)
		    {
			   $("#merchantwise_campaigns_report").html(msg);
			$('#demo_merchantwise_campaigns').html("");
		
			  paginate($("#hdn_cnt6").val(),"demo_merchantwise_campaigns","merchantwise_campaigns_report");
			
			}
		});
		
	});
	$("#ul_generated_campaigns li a").click(function() {
		$("#ul_generated_campaigns li").each(function(){
			$(this).removeClass("active_li");
			});
		$(this).parent().addClass("active_li");
		var v= $("select[id^='s1-container2-']").val();
                open_popup('Notification');
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id=<?php echo $_SESSION['merchant_id'] ?>&list_generated_campaign=true&month='+$(this).attr("month")+'&year='+v,
		    success:function(msg)
		    {
			  $("#generated_report").html(msg);
			  $('#demo_generated_campaigns').html("");
			  	paginate($("#hdn_cnt").val(),"demo_generated_campaigns","generated_report");
                                 close_popup('Notification');
			}
		});
               	
	});

	$("#ul_activated_campaigns li a").click(function() {
		var v= $("select[id^='s1-container1-']").val();
		$("#ul_activated_campaigns li").each(function(){
			$(this).removeClass("active_li");
			});
		$(this).parent().addClass("active_li");
		var v= $("select[id^='s1-container1-']").val();
                open_popup('Notification');
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id=<?php echo $_SESSION['merchant_id'] ?>&list_activated_campaign=true&month='+$(this).attr("month")+'&year='+v,
		    success:function(msg)
		    {
			
			  $("#activated_report").html(msg);
			  
			  $('#demo_activated_campaigns').html("");
			paginate($("#hdn_cnt1").val(),"demo_activated_campaigns","activated_report");
                          close_popup('Notification');
			}
		});
	});

	//
	$("#ul_expired_campaigns li a").click(function() {
		$("#ul_expired_campaigns li").each(function(){
			$(this).removeClass("active_li");
			});
		$(this).parent().addClass("active_li");
		var v= $("select[id^='s1-container3-']").val();
                open_popup('Notification');
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_expired_campaign=true&month='+$(this).attr("month")+'&year='+v,
		    success:function(msg)
		    {
			
			
				  $("#expired_report").html(msg);
			    
			  $('#demo_expired_campaigns').html("");
			  paginate($("#hdn_cnt2").val(),"demo_expired_campaigns","expired_report");
                            close_popup('Notification');
			}
		});
	});
	
	$("#ul_generated_coupons li a").click(function() {
		$("#ul_generated_coupons li").each(function(){
			$(this).removeClass("active_li");
			});
		$(this).parent().addClass("active_li");
		var v= $("select[id^='s1-container5-']").val();
                 open_popup('Notification');
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_generated_coupons=true&month='+$(this).attr("month")+'&year='+v,
		    success:function(msg)
		    {
			
			  $("#generated_coupon_report").html(msg);
			   $('#demo_generated_coupons').html("");
			  paginate($("#hdn_cnt3").val(),"demo_generated_coupons","generated_coupon_report");
                           close_popup('Notification');
			}
		});
	});
	
	$("#ul_redeemed_coupons li a").click(function() {
		$("#ul_redeemed_coupons li").each(function(){
			$(this).removeClass("active_li");
			});
		$(this).parent().addClass("active_li");
		var v= $("select[id^='s1-container4-']").val();
                open_popup('Notification');
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_redeemed_coupons=true&month='+$(this).attr("month")+'&year='+v,
		    success:function(msg)
		    {
			
			  $("#redeemed_coupon_report").html(msg);
			  $('#demo_redeemed_coupons').html("");
			  paginate($("#hdn_cnt4").val(),"demo_redeemed_coupons","redeemed_coupon_report");
                          close_popup('Notification');
			}
		});
	});
	
	
	$("#ul_usedpoints li a").click(function() {
		$("#ul_usedpoints li").each(function(){
			$(this).removeClass("active_li");
		});
	$(this).parent().addClass("active_li");
		//var v= $("select[id^='s1-container4-']").val();
                open_popup('Notification');
	$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_campaignwise_used_points=true&month='+$(this).attr("month"), 
		    success:function(msg)
		    {
	
			    $("#usedpoint_report").html(msg);
			    
			  $('#demo_usedpoints').html("");
			  paginate($("#hdn_cnt4").val(),"demo_usedpoints","usedpoint_report"); 
			
			  close_popup('Notification');
			}
		});
	});
	   $("#ul_locationwise_campaigns td a").click(function() {
	
		$("#ul_locationwise_campaigns td").each(function(){
			$(this).removeClass("active_li");
			});
		$(this).parent().addClass("active_li");
		 open_popup('Notification');
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_locationwise_campaigns=true&l_id='+$(this).attr("location_id"),
		    success:function(msg)
		    {
			  $("#locationwise_campaigns_report").html(msg);
			  $('#demo_locationwise_campaigns').html("");
			  paginate($("#hdn_cnt5").val(),"demo_locationwise_campaigns","locationwise_campaigns_report");
                           close_popup('Notification');
			}
		});
	});
	   $("#ul_merchantwise_campaigns td a").click(function() {
		$("#ul_merchantwise_campaigns td").each(function(){
			$(this).removeClass("active_li");
			});
		$(this).parent().addClass("active_li");
		open_popup('Notification');
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_merchantwise_campaigns=true&m_id='+$(this).attr("merchant_id"),
		    success:function(msg)
		    {
			  $("#merchantwise_campaigns_report").html(msg);
			  $('#demo_merchantwise_campaigns').html("");
			  paginate($("#hdn_cnt6").val(),"demo_merchantwise_campaigns","merchantwise_campaigns_report");
                           close_popup('Notification');
			}
		});
	});
	
var chart;
$(document).ready(function() {
	chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container2',
			type: 'column',
			marginRight: 130,
			marginBottom: 30
		},
		title: {
			text: 'Monthly Genarated Campaigns',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: ['Jan <?=date("y")?>', 'Feb <?=date("y")?>', 'Mar <?=date("y")?>', 'Apr <?=date("y")?>', 'May <?=date("y")?>', 'Jun <?=date("y")?>',
				'Jul <?=date("y")?>', 'Aug <?=date("y")?>', 'Sep <?=date("y")?>', 'Oct <?=date("y")?>', 'Nov <?=date("y")?>', 'Dec <?=date("y")?>']
		},
		yAxis: {
			title: {
				text: 'Campaigns'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'Generated',
			data: [<?php echo $str_campaigns; ?>]
		}]
	});
});
</script>
		<script language="javascript">

var chart;
$(document).ready(function() {
	
	chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container1',
			type: 'column',
			marginRight: 130,
			marginBottom: 30
		},
		title: {
			text: 'Monthly active Campaigns',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: ['Jan <?=date("y")?>', 'Feb <?=date("y")?>', 'Mar <?=date("y")?>', 'Apr <?=date("y")?>', 'May <?=date("y")?>', 'Jun <?=date("y")?>',
				'Jul <?=date("y")?>', 'Aug <?=date("y")?>', 'Sep <?=date("y")?>', 'Oct <?=date("y")?>', 'Nov <?=date("y")?>', 'Dec <?=date("y")?>']
		},
		yAxis: {
			title: {
				text: 'Campaigns'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'active',
			data: [<?php echo $str_activated; ?>]
		}]
	});
	
      chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container6',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Browse avarage campaigns of <?php echo date("M")." , ".date("Y"); ?>'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
            	percentageDecimals: 1
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %';
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Campaigns',
                data: [
                    ['Active',<?php echo $a_avg; ?>],
                    ['Expired',<?php echo $e_avg; ?>]
                  
                ]
            }]
        });
});
</script>
	
		<script language="javascript">

var chart;
$(document).ready(function() {
	chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container3',
			type: 'column',
			marginRight: 130,
			marginBottom: 30
		},
		title: {
			text: 'Monthly expired Campaigns',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: ['Jan <?=date("y")?>', 'Feb <?=date("y")?>', 'Mar <?=date("y")?>', 'Apr <?=date("y")?>', 'May <?=date("y")?>', 'Jun <?=date("y")?>',
				'Jul <?=date("y")?>', 'Aug <?=date("y")?>', 'Sep <?=date("y")?>', 'Oct <?=date("y")?>', 'Nov <?=date("y")?>', 'Dec <?=date("y")?>']
		},
		yAxis: {
			title: {
				text: 'Campaigns'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'Expired',
			data: [<?php echo $str_expired; ?>]
		}]
	});
});
</script>

<script type="text/javascript">
	
$("li#tab-type a").click(function() {
   
		$('#ul_merchantwise_campaigns td:first a').trigger('click');
	$("#sidemenu li a").each(function() {
		$(this).removeClass("current");
		});
	$(this).addClass("current");
	var cls = $(this).parent().attr("class");
	$(".tabs").each(function(){
		$(this).css("display","none");
	});
	
	$('#'+cls).css("display","block");
        	
	if($(this).parent().hasClass("Redeem_coupons") )
	{
		chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container5',
			type: 'column',
			marginRight: 130,
			marginBottom: 30
		},
		title: {
			text: 'Monthly Generated Coupons',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: ['Jan <?=date("y")?>', 'Feb <?=date("y")?>', 'Mar <?=date("y")?>', 'Apr <?=date("y")?>', 'May <?=date("y")?>', 'Jun <?=date("y")?>',
				'Jul <?=date("y")?>', 'Aug <?=date("y")?>', 'Sep <?=date("y")?>', 'Oct <?=date("y")?>', 'Nov <?=date("y")?>', 'Dec <?=date("y")?>']
		},
		yAxis: {
			title: {
				text: 'Coupons'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'geneareted',
			data: [<?php  echo $str_generated_coupons; ?>]
		}]
	});
		
		var val = <?php echo $str_redeemed_coupons ?>;
	chart = new Highcharts.Chart({
    
            chart: {
                renderTo: 'container4',
                type: 'column'
            },
    
            title: {
                text: 'Total redeemed coupons , grouped by campaigns'
            },
    
            xAxis: {
                categories:['Jan <?=date("y")?>', 'Feb <?=date("y")?>', 'Mar <?=date("y")?>', 'Apr <?=date("y")?>', 'May <?=date("y")?>', 'Jun <?=date("y")?>',
				'Jul <?=date("y")?>', 'Aug <?=date("y")?>', 'Sep <?=date("y")?>', 'Oct <?=date("y")?>', 'Nov <?=date("y")?>', 'Dec <?=date("y")?>']
            },
    
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Number of coupons'
                }
            },
    
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
    
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
    
            series: val
        });
	 chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container7',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Total  redeemed/not redeemed coupons from generated coupons of <?php echo date('Y'); ?>'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
            	percentageDecimals: 1
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %';
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'coupons',
                data: [
                    ['redeemed',<?php echo $r_per; ?>],
                    ['Generated but Not Redeemed',<?php echo $nr_per; ?>]
                  
                ]
            }]
        });
	}
	else if($(this).parent().hasClass("location") )
	{
		 //c8
		 var val1 = <?php echo $str_loactionwise_campaign; ?>;
	chart = new Highcharts.Chart({
    
            chart: {
                renderTo: 'container8',
                type: 'column'
            },
    
            title: {
                text: 'Total campaigns locationwise'
            },
    
            xAxis: {
                categories:[<?php echo $locations; ?>]
            },
    
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Number of campaigns'
                }
            },
    
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
    
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
    
            series: val1
        });
	}
	else if($(this).parent().hasClass("Sub_merchant") )
	{
		var name_val = <?php echo $merchant_name_array;?>;
		var total_val = <?php echo $merchant_tot_campaign;?>;
		 chart = new Highcharts.Chart({
		chart: {
		renderTo: 'container9',
			type: 'line',
			marginRight: 130,
			marginBottom: 25
		},
		title: {
			text: 'Merchant and sub merchant wise no. of campiagns',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: name_val
		},
		yAxis: {
			title: {
				text: 'Campaigns'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'generated',
			data: total_val
		}]
	});
	}
	else if($(this).parent().hasClass("points") )
	{
		var val_used_point = <?php echo $str_used_points ?>;
	chart = new Highcharts.Chart({
    
            chart: {
                renderTo: 'container10',
                type: 'column'
            },
    
            title: {
                text: 'Total used point , grouped by campaigns'
            },
    
            xAxis: {
                categories:['Jan <?=date("y")?>', 'Feb <?=date("y")?>', 'Mar <?=date("y")?>', 'Apr <?=date("y")?>', 'May <?=date("y")?>', 'Jun <?=date("y")?>',
				'Jul <?=date("y")?>', 'Aug <?=date("y")?>', 'Sep <?=date("y")?>', 'Oct <?=date("y")?>', 'Nov <?=date("y")?>', 'Dec <?=date("y")?>']
            },
    
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'No. of points used by customers'
                }
            },
    
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
    
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
    
            series: val_used_point
        });
	    //container11
//	   	 chart = new Highcharts.Chart({
//            chart: {
//                renderTo: 'container11',
//                plotBackgroundColor: null,
//                plotBorderWidth: null,
//                plotShadow: false
//            },
//            title: {
//                text: 'Total available points(Your purchased points 1000)'
//            },
//            tooltip: {
//        	    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
//            	percentageDecimals: 1
//            },
//            plotOptions: {
//                pie: {
//                    allowPointSelect: true,
//                    cursor: 'pointer',
//                    dataLabels: {
//                        enabled: true,
//                        color: '#000000',
//                        connectorColor: '#000000',
//                        formatter: function() {
//                            return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %';
//                        }
//                    }
//                }
//            },
//            series: [{
//                type: 'pie',
//                name: 'points',
//                data: [
//                    ['avilable',70],
//                    ['Used',30]
//                  
//                ]
//            }]
//        });
	    
	}
	else if($(this).parent().hasClass("google_analytics") )
	{
            
           	  
chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container16',
			type: 'column',
			marginRight: 130,
			marginBottom: 30
		},
		title: {
			text: 'Number of visitors of campaign for <?php echo date('M')." - ".date("Y");  ?>',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: [<?php echo $str_campaign_names_for_ga; ?>]
		},
		yAxis: {
			title: {
				text: 'visits'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'visits',
			data: [<?php echo $str_campaign_total_visits; ?>]
		}]
	});	
            // End for chart //
           
		
	}
	  
});	
	 
	  
	 
//  for anlytic reports //
 
   chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container12',
			type: 'column',
			marginRight: 130,
			marginBottom: 30
		},
		title: {
			text: 'Total points used by campaigns',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: [<?php echo $str_campaign_names; ?>]
		},
		yAxis: {
			title: {
				text: 'points'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'used',
			data: [<?php echo $str_campaign_total_point; ?>]
		}]
	});
		chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container13',
			type: 'column',
			marginRight: 130,
			marginBottom: 30
		},
		title: {
			text: 'Redeemed points used by campaigns',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: [<?php echo $str_campaign_names1; ?>]
		},
		yAxis: {
			title: {
				text: 'points'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'Redeemed point',
			data: [<?php echo $str_campaign_total_point1; ?>]
		}]
	});
		chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container14',
			type: 'column',
			marginRight: 130,
			marginBottom: 30
		},
		title: {
			text: 'Shared points used by campaigns',
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: [<?php echo $str_campaign_names2; ?>]
		},
		yAxis: {
			title: {
				text: 'points'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: 'shared point',
			data: [<?php echo $str_campaign_total_point2; ?>]
		}]
	});
	
        
// end for anlytic reports //
        
$("a#report").css("background-color","orange");
$("#report_option").change(function(){
 window.location.href = "<?=WEB_PATH?>/merchant/reports.php?action=active";	
});
</script>

  

        </div>
<!--end of contentContainer--></div>

<!---------start footer--------------->
       <div>
		<?
		require_once(SERVER_PATH."/merchant/templates/footer.php");
		?>
		<!--end of footer--></div>
	
</div>

</body>
</html>
<script type="text/javascript">
$("a#googlereport").css("background-color","orange");
</script>

<script>

$("#btn_load_GA").click(function() {
    open_popup('Notification');
	$.ajax({
		  type:"POST",
		  url:'filter_report_by_year.php',
		  data : 'loadGAReport=true&month='+$("#ga_select_1").val()+'&year='+$("#ga_select_2").val(),
		    success:function(msg)
		    {
			var obj = eval('('+msg+')');
			var a1 = obj.names ;
			var a2 = obj.visits ;
			chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container16',
						type: 'column',
						marginRight: 130,
						marginBottom: 30
					},
					title: {
						text: 'Number of visitors of campaign for '+$("#ga_select_1 :selected").text() + ' - ' + $("#ga_select_2").val(),
						x: -20 //center
					},
					subtitle: {
						text: ' ',
						x: -20
					},
					xAxis: {
						categories: a1
					},
					yAxis: {
						title: {
							text: 'visits'
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
					tooltip: {
						formatter: function() {
								return '<b>'+ this.series.name +'</b><br/>'+
								this.x +': '+ this.y +'';
						}
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'top',
						x: 0,
						y: 100,
						borderWidth: 0
					},
					series: [{
						name: 'visits',
						data: a2
					}]
				});
                        close_popup('Notification');	
		    }
		});
	
});


</script>

<script>
$("select[id^='s1-']").change(function() {
open_popup('Notification');
div_value = $(this).attr("id").split("-")[1];

method_value = $(this).attr("id").split("-")[2];

y_val = $(this).val();
	$.ajax({
		  type:"POST",
		  url:'filter_report_by_year.php',
		  data : method_value+'=true&year='+$(this).val(),
		    success:function(msg)
		    {
			var m = trim(msg);
			var m_arr = eval("(" + m+")");
			if(div_value=="container2")
			{
				common_container1("container2","Monthly Genarated Campaigns",y_val,"Campaigns","Generated",m_arr);
			}
			else if(div_value=="container1")
			{
				common_container1("container1","Monthly active Campaigns",y_val,"Campaigns","active",m_arr);
			}
			else if(div_value=="container3")
			{
				common_container1("container3","Monthly expired Campaigns",y_val,"Campaigns","expire",m_arr);
			}
			else if(div_value=="container5")
			{
				common_container1("container5","Monthly Generated Coupons",y_val,"Coupons","geneareted",m_arr);
			}
			else if(div_value=="container4")
			{
				common_container2("container4","Total redeemed coupons , grouped by campaigns",y_val,"Number of coupons",m_arr);
			}
			else if(div_value=="container10")
			{
				common_container2("container4","Total used point , grouped by campaigns",y_val,"No. of points used by customers",m_arr);
			}
                        close_popup('Notification');	
		    }
		});
	
});

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}

function common_container2(c_name,title,year,yaxis_title,data1)
{
	var val = <?php echo $str_redeemed_coupons ?>;
	chart = new Highcharts.Chart({
    
            chart: {
                renderTo: c_name,
                type: 'column'
            },
    
            title: {
                text: title
            },
    
            xAxis: {
               categories: ['Jan '+year, 'Feb '+ year, 'Mar '+year, 'Apr '+ year, 'May '+ year, 'Jun '+ year,
				'Jul '+ year, 'Aug '+ year, 'Sep '+ year, 'Oct '+ year, 'Nov '+ year, 'Dec '+ year]
            },
    
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: yaxis_title
                }
            },
    
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
    
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
    
            series: data1
        });
	if(c_name== "container4"){
		var v= $("select[id^='s1-"+c_name+"']").val();
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_redeemed_coupons=true&month=01&year='+v,
		    success:function(msg)
		    {
			    $("#redeemed_coupon_report").html(msg);
			    
			  $('#demo_redeemed_coupons').html("");
			  paginate($("#hdn_cnt4").val(),"demo_redeemed_coupons","redeemed_coupon_report");
			
			 
			}
		});
	
		$("#ul_redeemed_coupons li").each(function(){
			$(this).removeClass("active_li");
		});
		$("#ul_redeemed_coupons li:first").addClass("active_li");
	}
}
function common_container1(c_name,title,year,yaxis_title,series_title,data1)
{
	
	chart = new Highcharts.Chart({
		chart: {
			renderTo: c_name,
			type: 'column',
			marginRight: 130,
			marginBottom: 30
		},
		title: {
			text:title,
			x: -20 //center
		},
		subtitle: {
			text: ' ',
			x: -20
		},
		xAxis: {
			categories: ['Jan '+year, 'Feb '+ year, 'Mar '+year, 'Apr '+ year, 'May '+ year, 'Jun '+ year,
				'Jul '+ year, 'Aug '+ year, 'Sep '+ year, 'Oct '+ year, 'Nov '+ year, 'Dec '+ year]
		},
		yAxis: {
			title: {
				text: yaxis_title
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		tooltip: {
			formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'';
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 100,
			borderWidth: 0
		},
		series: [{
			name: series_title,
			data: data1
		}]
	});
	if(c_name== "container2"){
		var v= $("select[id^='s1-"+c_name+"']").val();
		$.ajax({
			  type:"POST",
			  url:'process.php',
			  data :'mer_id=<?php echo $_SESSION['merchant_id'] ?>&list_generated_campaign=true&month=01&year='+v,
			    success:function(msg)
			    {
				
				
				$("#generated_report").html(msg);
				 $('#demo_generated_campaigns').html("");
					paginate($("#hdn_cnt").val(),"demo_generated_campaigns","generated_report");
					
				}
		});
	
		$("#ul_generated_campaigns li").each(function(){
			$(this).removeClass("active_li");
		});
		$("#ul_generated_campaigns li:first").addClass("active_li");
	}
	else if(c_name== "container1"){
		
		var v= $("select[id^='s1-"+c_name+"']").val();
		
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id=<?php echo $_SESSION['merchant_id'] ?>&list_activated_campaign=true&month=01&year='+v,
		    success:function(msg)
		    {
			  $("#activated_report").html(msg);
			  $('#demo_activated_campaigns').html("");
			  paginate($("#hdn_cnt1").val(),"demo_activated_campaigns","activated_report");
			 
			}
		});
	
		$("#ul_activated_campaigns li").each(function(){
			$(this).removeClass("active_li");
		});
		$("#ul_activated_campaigns li:first").addClass("active_li");
	}
	else if(c_name== "container3"){
		
		var v= $("select[id^='s1-"+c_name+"']").val();
		
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_expired_campaign=true&month=01&year='+v,
		    success:function(msg)
		    {
			
			  $("#expired_report").html(msg);
			    
			  $('#demo_expired_campaigns').html("");
			  paginate($("#hdn_cnt2").val(),"demo_expired_campaigns","expired_report");
			
			}
		});
	
		$("#ul_expired_campaigns li").each(function(){
			$(this).removeClass("active_li");
		});
		$("#ul_expired_campaigns li:first").addClass("active_li");
	}
	else if(c_name== "container5"){
		
		var v= $("select[id^='s1-"+c_name+"']").val();
		
		$.ajax({
		  type:"POST",
		  url:'process.php',
		  data :'mer_id='+<?php echo $_SESSION['merchant_id'] ?>+'&list_generated_coupons=true&month=01&year='+v,
		    success:function(msg)
		    {
			
			  $("#generated_coupon_report").html(msg);
			$('#demo_generated_coupons').html("");
			  paginate($("#hdn_cnt3").val(),"demo_generated_coupons","generated_coupon_report");
			}
		});
	
		$("#ul_generated_coupons li").each(function(){
			$(this).removeClass("active_li");
		});
		$("#ul_generated_coupons li:first").addClass("active_li");
	}
}
</script>

<style>
	.innerContainer {
    
    border-bottom-left-radius: 0px;
    border-bottom-right-radius: 0px;
    border-top-left-radius: 0px;
    border-top-right-radius: 0px;
	}
</style>
 <div id="NotificationPopUpContainer" class="container_popup"  style="display: none;">
                                        <div id="NotificationBackDiv" class="divBack">
                                        </div>
                                        <div id="NotificationFrontDivProcessing" class="Processing" style="display:none;">
                                            
                                             <div id="NotificationMaindivLoading" align="center" valign="middle" class="imgDivLoading"
                                              style="left: 45%;top: 40%;">
                                                
                                                <div id="NotificationmainContainer" class="innerContainer" style="height:auto;width:auto">
                                                        <img src="<?=WEB_PATH?>/images/loading.gif" style="display: block;" />
                                                 </div>
                                            </div>
                                        </div>
                                </div>
<script>

function close_popup(popup_name)
{

	$("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
	$("#" + popup_name + "BackDiv").fadeOut(200, function () {
		 $("#" + popup_name + "PopUpContainer").fadeOut(200, function () {         
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				$("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				$("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
 function open_popup(popup_name)
{

	
	$("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		$("#" + popup_name + "BackDiv").fadeIn(200, function () {
			 $("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
			 });
		});
	});
	
	
}
</script>