<?php
/**
 * @uses import customers
 * @used in pages :manage-customers.php
 * @author Sangeeta Raghavani
 */

//require_once("../classes/Config.Inc.php");
check_merchant_session();
//include_once(SERVER_PATH."/classes/JSON.php");
//$objJSON = new JSON();

//require_once("../classes/Config.Inc.php");
//check_merchant_session();
//include_once(SERVER_PATH."/classes/DB.php");
//$objDB = new DB('read');
$RSCat = $objDB->Show("categories");
$array =array();

if(isset($_REQUEST['btnImportCustomers'])){
	//echo "<hr>";
	//print_r($_FILES);
	//$objJSON->import_customers();
	$_SESSION['msg']= "Customer(s) has been imported Sucessfully";
	//header("Location: import-customers.php");
	//exit();
}

$array_where_user['id'] = $_SESSION['merchant_id'];
$RS_User = $objDB->Show("merchant_user", $array_where_user);
$m_parent = $RS_User->fields['merchant_parent'];
if($RS_User->fields['merchant_parent'] == 0)
{
    /*
	$array['created_by'] = $_SESSION['merchant_id'];
        $array['active'] = 1;
	$RSStore = $objDB->Show("locations", $array);
     
     */
}
else
{
	$media_acc_array = array(); 
	$media_acc_array['merchant_user_id'] = $_SESSION['merchant_id']; 
	$RSmedia = $objDB->Show("merchant_user_role",$media_acc_array); 
	$location_val = $RSmedia->fields['location_access'];
	
	
	//$sql = "SELECT * FROM locations  WHERE id in (select l.id from locations l where l.created_by=".$m_parent ." and l.id = ".$location_val .")";
	//$RSStore = $objDB->execute_query($sql);
       // echo WEB_PATH.'/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id='.$m_parent.'&loc_id='.$location_val;
	$arr=file(WEB_PATH.'/merchant/process.php?btnGetAllLocationOfMerchant=yes&mer_id='.$m_parent.'&loc_id='.$location_val);
	if(trim($arr[0]) == "")
	{
		unset($arr[0]);
		$arr = array_values($arr);
	}
	$json = json_decode($arr[0]);
        
	$total_records= $json->total_records;
	$records_array = $json->records;
}

                                        
?>
<!DOCTYPE HTML>
<html>
<head>
<title>ScanFlip | Add Customer List</title>
<?php require_once(MRCH_LAYOUT."/head.php"); ?>
<link href="<?=ASSETS_CSS ?>/m/template.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/bootstrap.css" />
<!--<script type="text/javascript" src="<?= ASSETS_JS ?>/m/bootstrap.min.js"></script>-->
<!--<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>-->
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.js"></script>
<script type="text/javascript" src="<?=ASSETS_JS ?>/m/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/datepicker.css">
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.ui.core.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery.ui.datepicker.js"></script>
<script type="text/javascript" language="javascript" src="<?=ASSETS_JS ?>/m/jquery-ui-timepicker-addon.js"></script>
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.ui.datepicker.css">
<link rel="stylesheet" href="<?=ASSETS_CSS ?>/m/jquery.ui.theme.css">
</head>
<body>
	<div id="dialog-message" title="Message Box" style="display:none">

    </div>
<div >

<!--<script type="text/javascript" src="<?=ASSETS_JS ?>/m/jquery-1.7.2.min.js"></script>-->
<!-- load from CDN-->
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->


        
	
<!---start header---->
	<div><?require_once(MRCH_LAYOUT."/header.php");?>
		<!--end header--></div>
	<div id="contentContainer">	
   
	<div id="content">
  <div class="title_header">Segment Customer List</div>

    
<table width="100%"  border="0" cellspacing="2" cellpadding="2">
  <tr>  
   	<td width="100%" align="left" valign="top">
		<form action="process.php" method="post" enctype="multipart/form-data">	
	<table width="100%"  border="0" cellspacing="2" cellpadding="2" >    
  		<tr>
    		<td width="15%">&nbsp;</td>
    		<td width="85%" class="table_errore_message" align="left" id="msg_div"><?=$_SESSION['msg']?>&nbsp;</td>    
  		</tr>
        <tr>
        	<td colspan="2">

		
            <table width="100%" class="main_import">
                 <tr class="tr_top">
                    <td>
                        
                        
                    
                        <div class="td_right"><input type="text" name="group_name" id="group_name" placeholder="Enter segmentation name" /></div>
			<span class="span_error_msg"></span>
                    </td>
                     <td>
                       <div class="tr_top">
                        <input type="submit" class="" name="create_segment_list" id="create_segment_list" value="<?php echo $merchant_msg['index']['btn_save'];?>" />&nbsp;&nbsp;
                        
                        <input type="submit" class="" name="btnCanDistributionlist" id="btnCanDistributionlist" value="<?php echo $merchant_msg['index']['btn_cancel'];?>" />
                    </div> 
                  </td>
                    </tr>
             
              <tr class="tr_top">
                 
              </tr>
              </table>
              </td>
		</tr>
	</table>
	<div class="tabbable tabs-left segment-tab" style="width: 70%;float: left;">
		<div class="row">
			<div class="col-md-2" style="width: 25%;">
				<ul class="nav nav-tabs no-margin">
					<li class="active"><a data-toggle="tab" href="#a" aria-expanded="true">Demographics</a></li>
					<li class=""><a data-toggle="tab" href="#b" aria-expanded="false">Location</a></li>
					<li class=""><a data-toggle="tab" href="#d" aria-expanded="false">Social</a></li>
				</ul>
			</div>
			<div class="col-md-10"  style="width: 74%;">
				<div class="row">
					<div class="tab-content ">
					<div id="a" class="tab-pane active">
						<div class="seg">Demographics</div>
						<p class="segp">Segment your users by demographic information.</p>
						
						<div class="tab-row row">
							<div class="col-md-3">
							 Age<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["age"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<label class=""><input type="checkbox" name="optage[]" class="" value="1"><span>17 & below</span></label>							
								<label class=""><input type="checkbox" name="optage[]" class="" value="2"><span>18-24</span></label>							
								<label class=""><input type="checkbox" name="optage[]" class="" value="3"><span>25-44</span></label>							
								<label class=""><input type="checkbox" name="optage[]" class="" value="4"><span>45-54</span></label>							
								<label class=""><input type="checkbox" name="optage[]" class="" value="5"><span>55-64</span></label>
								<label class=""><input type="checkbox" name="optage[]" class="" value="6"><span>65+</span></label>
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Gender<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["gender"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<label class=""><input type="checkbox" name="optgender[]" class="" value="Male"><span>Male</span></label>	
								<label class=""><input type="checkbox" name="optgender[]" class="" value="Female"><span>Female</span></label>	
								<label class=""><input type="checkbox" name="optgender[]" class="" value="Unknown"><span>Unknown</span></label>	
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Visited For<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["visitedfor_demoraphics"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<label class=""><input type="radio" name="optvisitedfor_demoraphics" class="" value="Campaigns"><span>Campaigns</span></label>	
								<label class=""><input type="radio" name="optvisitedfor_demoraphics" class="" value="Loyalty Card"><span>Loyalty Card</span></label>	
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Visited Date Range<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["date_range_demoraphics"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<div class="row">
									<div class="col-md-6">
										From :
								<input style="width:100%;" type="text" id="optvisitedfrom_demoraphics" name="optvisitedfrom_demoraphics">
									</div>
									<div class="col-md-6">
										
								To :
								<input style="width:100%;" type="text" id="optvisitedto_demoraphics" name="optvisitedto_demoraphics">
									</div>
								</div>
								
							</div>
						</div>
						
									
					</div>
					<div id="b" class="tab-pane">
						<div class="seg">Location</div>
						<p class="segp">Segment your users by locations visited.</p>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Visited For<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["visitedfor_location"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<label class=""><input type="radio" name="optvisitedfor_location" class="" value="Campaigns"><span>Campaigns</span></label>	
								<label class=""><input type="radio" name="optvisitedfor_location" class="" value="Loyalty Card"><span>Loyalty Card</span></label>	
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
							 Locations Visited<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["locations_visited"]; ?>"></span>
							</div>
							<div class="col-md-9 segloc">
								
								
								  <!--
								  <select multiple class="form-control" name="optlocation">
								  <?php
                                   $result1=$objDB->Conn->Execute("SELECT * from locations where active=1 and created_by=".$_SESSION['merchant_id']);
                                   while($Row= $result1->FetchRow()) 
                                   { ?>
                                      <option value="<?=$Row['id']?>">
                                        <?php 
										
                                        ?>
                                        <?php 
											$location_string = $Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip'];
											$location_string = (strlen($location_string) > 57) ? substr($location_string,0,57).'...' : $location_string;
											echo $location_string; 
										 ?>
                                      </option>
                                   <? }
                                  ?>
								  </select>
								  -->
									<?php
									$result1=$objDB->Conn->Execute("SELECT * from locations where active=1 and created_by=".$_SESSION['merchant_id']);
									while($Row= $result1->FetchRow()) 
									{
										$location_string = $Row['address'].", ".$Row['city'].", ".$Row['state'].", ".$Row['zip'];
										$location_string = (strlen($location_string) > 57) ? substr($location_string,0,57).'...' : $location_string;
									?>
										<label class=""><input type="checkbox" name="optlocation[]" value="<?=$Row['id']?>" ><span><?php echo $location_string; ?></span></label>
									<?php
									}
									?>
								
							</div>		
							
											
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Timezone<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["timezone"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<?php
									$timezoness=$objDB->Conn->Execute("SELECT * from day_timezone");
									while($Row= $timezoness->FetchRow()) 
									{
									?>
										<label class=""><input type="checkbox" name="opttimezone[]" value='<?php echo $Row['id'] ?>'><span><?php echo $Row['timevalue']; ?></span></label>
									<?php	
									}
								?>
							</div>
						</div>
						
						<div class="tab-row row ratedrow">
							<div class="col-md-3">
								Rated Locations<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["rated_locations"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<select class="input-mini1" id="optratedlocation" name="optratedlocation">
									<option value=">">></option>
									<option value="<"><</option>
								</select>
								<select class="" id="optrated" name="optrated">
									<option value="">Please Select</option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Amount Spent<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["amount_spent"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<select class="input-mini1" id="optamountspent" name="optamountspent">
									<option value=">">></option>
									<option value="<"><</option>
									<option value="=">=</option>
								</select>
								<input type="text" id="optrevenue" name="optrevenue">
							</div>
						</div>	
						
						<div class="tab-row row">
							<div class="col-md-3">
							 Age<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["age_location"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<label class=""><input type="checkbox" name="optage_location[]" class="" value="1"><span>17 & below</span></label>							
								<label class=""><input type="checkbox" name="optage_location[]" class="" value="2"><span>18-24</span></label>							
								<label class=""><input type="checkbox" name="optage_location[]" class="" value="3"><span>25-44</span></label>							
								<label class=""><input type="checkbox" name="optage_location[]" class="" value="4"><span>45-54</span></label>							
								<label class=""><input type="checkbox" name="optage_location[]" class="" value="5"><span>55-64</span></label>
								<label class=""><input type="checkbox" name="optage_location[]" class="" value="6"><span>65+</span></label>
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Gender<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["gender_location"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<label class=""><input type="checkbox" name="optgender_location[]" class="" value="Male"><span>Male</span></label>	
								<label class=""><input type="checkbox" name="optgender_location[]" class="" value="Female"><span>Female</span></label>	
								<label class=""><input type="checkbox" name="optgender_location[]" class="" value="Unknown"><span>Unknown</span></label>	
							</div>
						</div>
																
						
						<div class="tab-row row">
							<div class="col-md-3">
								Visited Date Range<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["date_range_location"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<div class="row">
									<div class="col-md-6">
										From :
								<input style="width:100%;" type="text" id="optvisitedfrom" name="optvisitedfrom">
									</div>
									<div class="col-md-6">
										
								To :
								<input style="width:100%;" type="text" id="optvisitedto" name="optvisitedto">
									</div>
								</div>
								
							</div>
						</div>
							
					</div>
					
					<div id="d" class="tab-pane">
						<div class="seg">Social</div>
						<p class="segp">Segment your users by social interaction.</p>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Shared Campaigns<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["shared_campaigns"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<select class="input-mini1" id="optsharedcampaigns" name="optsharedcampaigns">
									<option value=">">></option>
									<option value="<"><</option>
								</select>
								<input type="text" id="optsharedcampaignsvalue" name="optsharedcampaignsvalue">
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Shared Medium<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["shared_medium"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<label class=""><input type="checkbox" name="optmedium[]" class="" value="1"><span>Facebook</span></label>	
								<label class=""><input type="checkbox" name="optmedium[]" class="" value="2"><span>Twitter</span></label>	
								<label class=""><input type="checkbox" name="optmedium[]" class="" value="3"><span>Google+</span></label>	
								<label class=""><input type="checkbox" name="optmedium[]" class="" value="4"><span>Mail</span></label>	
								<label class=""><input type="checkbox" name="optmedium[]" class="" value="5"><span>Other</span></label>	
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
							 Age<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["age_social"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<label class=""><input type="checkbox" name="optage_social[]" class="" value="1"><span>17 & below</span></label>							
								<label class=""><input type="checkbox" name="optage_social[]" class="" value="2"><span>18-24</span></label>							
								<label class=""><input type="checkbox" name="optage_social[]" class="" value="3"><span>25-44</span></label>							
								<label class=""><input type="checkbox" name="optage_social[]" class="" value="4"><span>45-54</span></label>							
								<label class=""><input type="checkbox" name="optage_social[]" class="" value="5"><span>55-64</span></label>
								<label class=""><input type="checkbox" name="optage_social[]" class="" value="6"><span>65+</span></label>
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Gender<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["gender_social"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<label class=""><input type="checkbox" name="optgender_social[]" class="" value="Male"><span>Male</span></label>	
								<label class=""><input type="checkbox" name="optgender_social[]" class="" value="Female"><span>Female</span></label>	
								<label class=""><input type="checkbox" name="optgender_social[]" class="" value="Unknown"><span>Unknown</span></label>	
							</div>
						</div>
						
						<div class="tab-row row">
							<div class="col-md-3">
								Date Range<span class="notification_tooltip" title="<?php echo $merchant_msg["segmentation"]["date_range_social"]; ?>"></span>
							</div>
							<div class="col-md-7">
								<div class="row">
									<div class="col-md-6">
										From :
								<input style="width:100%;" type="text" id="optsharedfrom" name="optsharedfrom">
									</div>
									<div class="col-md-6">
										
								To :
								<input style="width:100%;" type="text" id="optsharedto" name="optsharedto">
									</div>
								</div>
								
							</div>
						</div>	
					</div>
					
					
				</div>
					
				</div>
			</div>
				
				</div>
					
				
			 
		</div>
		
		<div class="segmentationQuery">
			<div class="segQuery">Segmentation Query</div>
			<hr/>
			<div class="segQueryDetail">
				No Filter
			</div>
		</div>
		<input type="hidden" id="seg_query" name="seg_query"/>
</form>
    </td>
  </tr>
</table>
  
<div class="clear">&nbsp;</div>
<!--end of content--></div>
<!--end of contentContainer--></div>

<!---------start footer--------------->
       <div>
		<?
		require_once(MRCH_LAYOUT."/footer.php");
		$_SESSION['msg']= "";
		?>
		<!--end of footer--></div>
	
</div>





</body>
	
<!--- tooltip css --->


<!--- tooltip css --->
</html>

<script type="text/javascript">

jQuery("#create_segment_list").click(function(){
	var alert_msg="<?php  echo  $merchant_msg["index"]["validating_data"]; ?>";
	var head_msg="<div class='head_msg'>Message</div>";
	var content_msg="<div class='content_msg validatingdata' style='background:white;'>"+alert_msg+"</div>";
	jQuery("#NotificationloadermainContainer").html(content_msg);

	jQuery("#NotificationloaderFrontDivProcessing").css("display","block");
	jQuery("#NotificationloaderBackDiv").css("display","block");
	jQuery("#NotificationloaderPopUpContainer").css("display","block");

	var flaglogin=0;
	jQuery.ajax({
		type:"POST",
		url:'process.php',
		data :'loginornot=true',
		async:false,
		success:function(msg)
		{
			var obj = jQuery.parseJSON(msg);
			if (obj.status=="false")     
			{
				flaglogin=1;
				window.location.href=obj.link;
			}
			else
			{
			}
		}
	});
	if(flaglogin == 1)
	{
		return false;
	}
	else
	{							 
		var group_name=jQuery("#group_name").val();
		var flag="";
		var msg_box="";
		
		if(group_name == "")
		{
			msg_box +="<div>* <?php echo $merchant_msg["segmentation"]['Msg_enter_segmentaion_name'] ?></div>";
			flag = "false";
		}
		
		var sel_tab = jQuery("#seg_query").val();
		
		if(sel_tab == "demographics")
		{
			var optvisitedfor_demoraphics = jQuery("input[name='optvisitedfor_demoraphics']:checked").val();
			console.log(optvisitedfor_demoraphics);
		
			if(optvisitedfor_demoraphics=="" || typeof(optvisitedfor_demoraphics)==="undefined" )
			{
				msg_box +="<div>* <?php echo $merchant_msg["segmentation"]['Msg_select_visitedfor'] ?></div>";
				flag = "false";
			}
			
			var visted_from = jQuery("#optvisitedfrom_demoraphics").val();
			var visted_to = jQuery("#optvisitedto_demoraphics").val();
			//console.log(visted_from);
			//console.log(visted_to);
			if(visted_from == "" || visted_to == "")
			{
				msg_box +="<div>* <?php echo $merchant_msg["segmentation"]['Msg_select_daterange'] ?></div>";
				flag = "false";
			}
		}
		else if(sel_tab == "location")
		{
			var optvisitedfor_location = jQuery("input[name='optvisitedfor_location']:checked").val();
			console.log(optvisitedfor_location);
		
			if(optvisitedfor_location=="" || typeof(optvisitedfor_location)==="undefined" )
			{
				msg_box +="<div>* <?php echo $merchant_msg["segmentation"]['Msg_select_visitedfor'] ?></div>";
				flag = "false";
			}
			
			var visted_from = jQuery("#optvisitedfrom").val();
			var visted_to = jQuery("#optvisitedto").val();
			//console.log(visted_from);
			//console.log(visted_to);
			if(visted_from == "" || visted_to == "")
			{
				msg_box +="<div>* <?php echo $merchant_msg["segmentation"]['Msg_select_daterange'] ?></div>";
				flag = "false";
			}
		}
		else if(sel_tab == "social")
		{
			var visted_from = jQuery("#optsharedfrom").val();
			var visted_to = jQuery("#optsharedto").val();
			//console.log(visted_from);
			//console.log(visted_to);
			if(visted_from == "" || visted_to == "")
			{
				msg_box +="<div>* <?php echo $merchant_msg["segmentation"]['Msg_select_daterange'] ?></div>";
				flag = "false";
			}
		}
		else if(sel_tab == "")
		{
			msg_box +="<div>* <?php echo $merchant_msg["segmentation"]['Msg_select_any_option'] ?></div>";
			flag = "false";
		}
		
		/*
		var checked_location_count = jQuery("input[name='optlocation[]']:checked").length;
		//console.log(checked_location_count);
		if(checked_location_count == 0)
		{
			msg_box +="<div>* <?php echo $merchant_msg["import-customers"]['Msg_select_location'] ?></div>";
			flag = "false";
		}
		*/
		var head_msg="<div class='head_msg'>Message</div>"
		var content_msg="<div class='content_msg'>"+msg_box+"</div>";
		var footer_msg="<div><hr><input type='button'  value='<?php echo $merchant_msg['index']['fancybox_ok'];?>' id='popupcancel' name='popupcancel' class='msg_popup_cancel'></div>";
		jQuery( "#dialog-message" ).html(head_msg + content_msg +footer_msg);
		if(flag=="false")
		{
			close_popup("Notificationloader");
		
			jQuery.fancybox({
				content:jQuery('#dialog-message').html(),
				
				type: 'html',
				
				openSpeed  : 300,
				
				closeSpeed  : 300,
				// topRatio: 0,

				changeFade : 'fast',  
				
				helpers: {
						overlay: {
						opacity: 0.3
						} // overlay
				}
			});
			return false;
		}
		else
		{
			var alert_msg="<?php  echo  $merchant_msg["index"]["saving_data"]; ?>";
			var head_msg="<div class='head_msg'>Message</div>";
			var content_msg="<div class='content_msg savingdata' style='background:white;'>"+alert_msg+"</div>";
			jQuery("#NotificationloadermainContainer").html(content_msg);
			return true;
		}
	}
});

//jQuery(".tabbable").click(function(){
jQuery("#content").click(function(){
	
	//console.log("click");
	
	// Start Demographics Tab
		
		//console.log("total checked age count "+jQuery("input[name='optage']:checked").length);
		var checked_age_count = jQuery("input[name='optage[]']:checked").length;
		var checked_age_value = "";
		jQuery("input[name='optage[]']").each(function () {
			if(jQuery(this).is(':checked'))
			{
				var checked_value = jQuery(this).next("span").html();
				//console.log("age : "+checked_value);
				checked_age_value += checked_value+",";
			}
		});
		
		checked_age_value = checked_age_value.slice(0,-1);
		//console.log(checked_age_value);
		
		//console.log("total checked gender count "+jQuery("input[name='optgender']:checked").length);
		var checked_gender_count = jQuery("input[name='optgender[]']:checked").length;
		var checked_gender_value = "";
		jQuery("input[name='optgender[]']").each(function () {
			if(jQuery(this).is(':checked'))
			{
				var checked_value = jQuery(this).next("span").html();
				//console.log("age : "+checked_value);
				checked_gender_value += checked_value+",";
			}
		});
		
		checked_gender_value = checked_gender_value.slice(0,-1);
		//console.log(checked_gender_value);
		
		var visited_for_demoraphics_count = jQuery("input[name='optvisitedfor_demoraphics']:checked").length;
		var visited_for_demoraphics = jQuery("input[name='optvisitedfor_demoraphics']:checked").val();
		var optvisitedfrom_demoraphics = jQuery("#optvisitedfrom_demoraphics").val();
		var optvisitedto_demoraphics = jQuery("#optvisitedto_demoraphics").val();
		
    // End Demographics Tab
    
    // Start Location Tab
		
		var visited_for_location_count = jQuery("input[name='optvisitedfor_location']:checked").length;
		var visited_for_location = jQuery("input[name='optvisitedfor_location']:checked").val();
		
		var checked_location_count = jQuery("input[name='optlocation[]']:checked").length;
		
		//console.log(checked_location_count);
		var checked_location_id = "";
		var checked_location_value = "";
		//jQuery("#optlocation > option").each(function() {
		jQuery("input[name='optlocation[]']").each(function() {
			 if (jQuery(this).is(':checked'))
			 {
				 var checked_value = jQuery(this).next("span").html();
				 checked_location_id += jQuery(this).val()+",";
				 checked_location_value += checked_value+",";
			 }
		});
    
		checked_location_id = checked_location_id.slice(0,-1);
		checked_location_value = checked_location_value.slice(0,-1);
		
		//console.log(checked_location_id);
		//console.log(checked_location_value);
		
		var visted_from = jQuery("#optvisitedfrom").val();
		var visted_to = jQuery("#optvisitedto").val();
		
		//console.log("total checked age count "+jQuery("input[name='opttimezone']:checked").length);
		var checked_timezone_count = jQuery("input[name='opttimezone[]']:checked").length;
		var checked_timezone_id = "";
		var checked_timezone_value = "";
		jQuery("input[name='opttimezone[]']").each(function () {
			if(jQuery(this).is(':checked'))
			{
				var checked_value = jQuery(this).next("span").html();
				//console.log("age : "+checked_value);
				checked_timezone_id += jQuery(this).val()+",";
				checked_timezone_value += checked_value+",";
			}
		});
		checked_timezone_id = checked_timezone_id.slice(0,-1);
		checked_timezone_value = checked_timezone_value.slice(0,-1);
		//console.log(checked_timezone_value);
		
		var optrated = jQuery("#optrated option:selected").val();
		var rated_location = jQuery('#optratedlocation option:selected').val();
		
		var optrevenue = jQuery("#optrevenue").val();
		var amount_spent = jQuery('#optamountspent option:selected').val();
		
		var checked_age_location_count = jQuery("input[name='optage_location[]']:checked").length;
		var checked_age_location_value = "";
		jQuery("input[name='optage_location[]']").each(function () {
			if(jQuery(this).is(':checked'))
			{
				var checked_value = jQuery(this).next("span").html();
				//console.log("age : "+checked_value);
				checked_age_location_value += checked_value+",";
			}
		});
		
		checked_age_location_value = checked_age_location_value.slice(0,-1);
		//console.log(checked_age_location_value);
		
		//console.log("total checked gender count "+jQuery("input[name='optgender_location']:checked").length);
		var checked_gender_location_count = jQuery("input[name='optgender_location[]']:checked").length;
		var checked_gender_location_value = "";
		jQuery("input[name='optgender_location[]']").each(function () {
			if(jQuery(this).is(':checked'))
			{
				var checked_value = jQuery(this).next("span").html();
				//console.log("age : "+checked_value);
				checked_gender_location_value += checked_value+",";
			}
		});
		
		checked_gender_location_value = checked_gender_location_value.slice(0,-1);
		//console.log(checked_gender_location_value);
		
		
    // End Location Tab
    
    // Start Social Tab
    
		var optsharedcampaignsvalue = jQuery("#optsharedcampaignsvalue").val();
		var optsharedcampaigns = jQuery('#optsharedcampaigns option:selected').val();
		
		var checked_medium_count = jQuery("input[name='optmedium[]']:checked").length;
		var checked_medium_id = "";
		var checked_medium_value = "";
		jQuery("input[name='optmedium[]']").each(function () {
			if(jQuery(this).is(':checked'))
			{
				var checked_value = jQuery(this).next("span").html();
				//console.log("age : "+checked_value);
				checked_medium_id += jQuery(this).val()+",";
				checked_medium_value += checked_value+",";
			}
		});
		checked_medium_id = checked_medium_id.slice(0,-1);
		checked_medium_value = checked_medium_value.slice(0,-1);
    
		var visited_for_social_count = jQuery("input[name='optvisitedfor_social']:checked").length;
		var visited_for_social = jQuery("input[name='optvisitedfor_social']:checked").val();
		
		var checked_age_social_count = jQuery("input[name='optage_social[]']:checked").length;
		var checked_age_social_value = "";
		jQuery("input[name='optage_social[]']").each(function () {
			if(jQuery(this).is(':checked'))
			{
				var checked_value = jQuery(this).next("span").html();
				//console.log("age : "+checked_value);
				checked_age_social_value += checked_value+",";
			}
		});
		
		checked_age_social_value = checked_age_social_value.slice(0,-1);
		//console.log(checked_age_social_value);
		
		//console.log("total checked gender count "+jQuery("input[name='optgender_social']:checked").length);
		var checked_gender_social_count = jQuery("input[name='optgender_social[]']:checked").length;
		var checked_gender_social_value = "";
		jQuery("input[name='optgender_social[]']").each(function () {
			if(jQuery(this).is(':checked'))
			{
				var checked_value = jQuery(this).next("span").html();
				//console.log("age : "+checked_value);
				checked_gender_social_value += checked_value+",";
			}
		});
		
		checked_gender_social_value = checked_gender_social_value.slice(0,-1);
		//console.log(checked_gender_social_value);
		
		var optsharedfrom = jQuery("#optsharedfrom").val();
		var optsharedto = jQuery("#optsharedto").val();
		
    // End Social Tab
    
   
    
    var Demographics_query ="";
    var Location_query ="";
    var Social_query ="";
    
    jQuery(".segQueryDetail").html("");
    if(checked_age_count>0 || checked_gender_count>0 || visited_for_demoraphics_count>0 || (optvisitedfrom_demoraphics!="" && optvisitedto_demoraphics!="") 
		|| visited_for_location_count>0 || checked_location_count>0 || (visted_from!="" && visted_to!="") || checked_timezone_count>0 
		|| optrated!="" || optrevenue!="" || checked_age_location_count>0 || checked_gender_location_count>0 
		|| optsharedcampaignsvalue!="" || checked_medium_count>0 || (optsharedfrom!="" && optsharedto!="") || checked_age_social_count>0 || checked_gender_social_count>0 
		 )
    {
		if(checked_age_count>0 || checked_gender_count>0 || visited_for_demoraphics_count>0 
			|| (optvisitedfrom_demoraphics!="" && optvisitedto_demoraphics!="") )
		{
			Demographics_query+="<div id='SegDemographics' class='segQueryWrapper'><div class='segQueryHeader'>Demographics<span class='segQueryClose' id='segCloseDemographics'>x</span></div>";
			
			if(checked_age_count>0)
			{
				Demographics_query+="<div class='segOption'><span>Age :</span> "+checked_age_value+"</div>";
			}
			
			if(checked_gender_count>0)
			{
				Demographics_query+="<div class='segOption'><span>Gender :</span> "+checked_gender_value+"</div>";
			}
			
			if(visited_for_demoraphics_count>0)
			{
				Demographics_query+="<div class='segOption'><span>Visited For :</span> "+visited_for_demoraphics+"</div>";
			}
			
			if(optvisitedfrom_demoraphics!="" && optvisitedto_demoraphics!="")
			{
				Demographics_query+="<div class='segOption'><span>Visited :</span> "+optvisitedfrom_demoraphics+" - "+optvisitedto_demoraphics +" </div>";
			}
			
			Demographics_query+="</div>";
				
			jQuery(".segQueryDetail").html(Demographics_query);
		}
		if(visited_for_location_count>0 || checked_location_count>0 || (visted_from!="" && visted_to!="") || checked_timezone_count>0 || optrated!="" 
			|| optrevenue!="" || checked_age_location_count>0 || checked_gender_location_count>0)
		{
			Location_query+="<div id='SegLocation' class='segQueryWrapper'><div class='segQueryHeader'>Location<span class='segQueryClose' id='segCloseLocation'>x</span></div>";
			
			if(visited_for_location_count>0)
			{
				Location_query+="<div class='segOption'><span>Visited For :</span> "+visited_for_location+"</div>";
			}
			
			if(checked_location_count>0)
			{
				Location_query+="<div class='segOption'><span>Locations :</span> "+checked_location_value+"</div>";
			}
			
			if(checked_timezone_count>0)
			{
				Location_query+="<div class='segOption'><span>Timezone :</span> "+checked_timezone_value+"</div>";
			}
			
			if(optrated!="")
			{
				Location_query+="<div class='segOption'><span>Rated Location :</span> "+rated_location+" "+optrated+"</div>";
			}
			
			if(optrevenue!=0)
			{
				Location_query+="<div class='segOption'><span>Amount Spent :</span> "+amount_spent+" "+optrevenue+"</div>";
			}
			
			if(checked_age_location_count>0)
			{
				Location_query+="<div class='segOption'><span>Age :</span> "+checked_age_location_value+"</div>";
			}
			
			if(checked_gender_location_count>0)
			{
				Location_query+="<div class='segOption'><span>Gender :</span> "+checked_gender_location_value+"</div>";
			}
			
			if(visted_from!="" && visted_to!="")
			{
				Location_query+="<div class='segOption'><span>Visited :</span> "+visted_from+" - "+visted_to +" </div>";
			}
			
			Location_query+="</div>";	
			jQuery(".segQueryDetail").html(jQuery(".segQueryDetail").html() + Location_query);
		}
		
		if(optsharedcampaignsvalue!="" || checked_medium_count>0 || (optsharedfrom!="" && optsharedto!="") || checked_age_social_count>0 || checked_gender_social_count>0 )
		{
			Social_query+="<div id='SegShared' class='segQueryWrapper'><div class='segQueryHeader'>Social<span class='segQueryClose' id='segCloseShared'>x</span></div>";
			
			if(optsharedcampaignsvalue!=0)
			{
				Social_query+="<div class='segOption'><span>Shared Campaigns :</span> "+optsharedcampaigns+" "+optsharedcampaignsvalue+"</div>";
			}
			if(checked_medium_count>0)
			{
				Social_query+="<div class='segOption'><span>Mediums :</span> "+checked_medium_value+"</div>";
			}
			if(checked_age_social_count>0)
			{
				Social_query+="<div class='segOption'><span>Age :</span> "+checked_age_social_value+"</div>";
			}
			
			if(checked_gender_social_count>0)
			{
				Social_query+="<div class='segOption'><span>Gender :</span> "+checked_gender_social_value+"</div>";
			}
			if(optsharedfrom!="" && optsharedto!="")
			{
				Social_query+="<div class='segOption'><span>Shared :</span> "+optsharedfrom+" - "+optsharedto +" </div>";
			}
			Social_query+="</div>";
				
			jQuery(".segQueryDetail").html(jQuery(".segQueryDetail").html() + Social_query);
		}
		
	}
	else
	{
		jQuery(".segQueryDetail").html('No Filter');
	}
	//console.log("Demographics_query="+Demographics_query);
	//console.log("Location_query="+Location_query);
	//console.log("Social_query="+Social_query);
	//console.log(jQuery(".tabbable ul li:nth-child(1) a").text());
	//console.log(jQuery(".tabbable ul li:nth-child(2) a").text());
	//console.log(jQuery(".tabbable ul li:nth-child(3) a").text());
	
	if(Demographics_query!="")
	{
		console.log("if demographics");
		jQuery("#seg_query").val("demographics");
		jQuery(".tabbable ul li:nth-child(2) a").attr("data-toggle","");
		jQuery(".tabbable ul li:nth-child(3) a").attr("data-toggle","");
		
		jQuery(".tabbable ul li:nth-child(2) a").attr("aria-expanded","false");
		jQuery(".tabbable ul li:nth-child(3) a").attr("aria-expanded","false");
		
		jQuery(".tabbable ul li:nth-child(2) a").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3) a").addClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(2)").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3)").addClass("disabledTab");
		
	}
	else if(Location_query!="")
	{
		console.log("if location");
		jQuery("#seg_query").val("location");
		jQuery(".tabbable ul li:nth-child(1) a").attr("data-toggle","");
		jQuery(".tabbable ul li:nth-child(3) a").attr("data-toggle","");
		
		jQuery(".tabbable ul li:nth-child(1) a").attr("aria-expanded","false");
		jQuery(".tabbable ul li:nth-child(3) a").attr("aria-expanded","false");
		
		jQuery(".tabbable ul li:nth-child(1) a").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3) a").addClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(1)").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3)").addClass("disabledTab");
	}
	else if(Social_query!="")
	{
		console.log("if social");
		jQuery("#seg_query").val("social");
		jQuery(".tabbable ul li:nth-child(1) a").attr("data-toggle","");
		jQuery(".tabbable ul li:nth-child(2) a").attr("data-toggle","");
		
		jQuery(".tabbable ul li:nth-child(1) a").attr("aria-expanded","false");
		jQuery(".tabbable ul li:nth-child(2) a").attr("aria-expanded","false");
		
		jQuery(".tabbable ul li:nth-child(1) a").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(2) a").addClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(1)").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(2)").addClass("disabledTab");
	}
	else
	{
		jQuery("#seg_query").val("");
		jQuery(".tabbable ul li:nth-child(1) a").attr("data-toggle","tab");
		jQuery(".tabbable ul li:nth-child(2) a").attr("data-toggle","tab");
		jQuery(".tabbable ul li:nth-child(3) a").attr("data-toggle","tab");
		
		jQuery(".tabbable ul li:nth-child(1) a").attr("aria-expanded","true");
		jQuery(".tabbable ul li:nth-child(2) a").attr("aria-expanded","true");
		jQuery(".tabbable ul li:nth-child(3) a").attr("aria-expanded","true");
		
		jQuery(".tabbable ul li:nth-child(1) a").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(2) a").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3) a").removeClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(1)").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(2)").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3)").removeClass("disabledTab");
	}
	
	/*
	if(Demographics_query!="")
	{
		console.log("if demographics");
		jQuery(".tabbable ul li:nth-child(2) a").attr("data-toggle","");
		jQuery(".tabbable ul li:nth-child(3) a").attr("data-toggle","");
		
		jQuery(".tabbable ul li:nth-child(2) a").attr("aria-expanded","false");
		jQuery(".tabbable ul li:nth-child(3) a").attr("aria-expanded","false");
		
		jQuery(".tabbable ul li:nth-child(2) a").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3) a").addClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(2)").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3)").addClass("disabledTab");
		
	}
	else
	{
		console.log("else demographics");
		jQuery(".tabbable ul li:nth-child(2) a").attr("data-toggle","tab");
		jQuery(".tabbable ul li:nth-child(3) a").attr("data-toggle","tab");
		
		jQuery(".tabbable ul li:nth-child(2) a").attr("aria-expanded","true");
		jQuery(".tabbable ul li:nth-child(3) a").attr("aria-expanded","true");
		
		jQuery(".tabbable ul li:nth-child(2) a").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3) a").removeClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(2)").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3)").removeClass("disabledTab");
	}
	if(Location_query!="")
	{
		console.log("if location");
		jQuery(".tabbable ul li:nth-child(1) a").attr("data-toggle","");
		jQuery(".tabbable ul li:nth-child(3) a").attr("data-toggle","");
		
		jQuery(".tabbable ul li:nth-child(1) a").attr("aria-expanded","false");
		jQuery(".tabbable ul li:nth-child(3) a").attr("aria-expanded","false");
		
		jQuery(".tabbable ul li:nth-child(1) a").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3) a").addClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(1)").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3)").addClass("disabledTab");
	}
	else
	{
		console.log("else location");
		jQuery(".tabbable ul li:nth-child(1) a").attr("data-toggle","tab");
		jQuery(".tabbable ul li:nth-child(3) a").attr("data-toggle","tab");
		
		jQuery(".tabbable ul li:nth-child(1) a").attr("aria-expanded","true");
		jQuery(".tabbable ul li:nth-child(3) a").attr("aria-expanded","true");
		
		jQuery(".tabbable ul li:nth-child(1) a").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3) a").removeClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(1)").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(3)").removeClass("disabledTab");
	}
	if(Social_query!="")
	{
		console.log("if social");
		jQuery(".tabbable ul li:nth-child(1) a").attr("data-toggle","");
		jQuery(".tabbable ul li:nth-child(2) a").attr("data-toggle","");
		
		jQuery(".tabbable ul li:nth-child(1) a").attr("aria-expanded","false");
		jQuery(".tabbable ul li:nth-child(2) a").attr("aria-expanded","false");
		
		jQuery(".tabbable ul li:nth-child(1) a").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(2) a").addClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(1)").addClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(2)").addClass("disabledTab");
	}
	else
	{
		console.log("else social");
		jQuery(".tabbable ul li:nth-child(1) a").attr("data-toggle","tab");
		jQuery(".tabbable ul li:nth-child(2) a").attr("data-toggle","tab");
		
		jQuery(".tabbable ul li:nth-child(1) a").attr("aria-expanded","true");
		jQuery(".tabbable ul li:nth-child(2) a").attr("aria-expanded","true");
		
		jQuery(".tabbable ul li:nth-child(1) a").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(2) a").removeClass("disabledTab");
		
		jQuery(".tabbable ul li:nth-child(1)").removeClass("disabledTab");
		jQuery(".tabbable ul li:nth-child(2)").removeClass("disabledTab");
	}
	*/
	bind_close();
});

jQuery("input[name='optvisitedfor_location']").change(function(){
	var ele_val = jQuery("input[name='optvisitedfor_location']:checked").val(); 
	console.log(ele_val);
	if(ele_val=="Loyalty Card")
	{
		jQuery(".ratedrow").hide();
	}
	else
	{
		jQuery(".ratedrow").show();
	}
});

function clear_controls(tab)
{
	jQuery('#'+tab+' input:checkbox').removeAttr('checked');
	jQuery('#'+tab+' input:radio').removeAttr('checked');
	jQuery('#'+tab+' input[type=text],textarea').val("");
	jQuery('#'+tab+' select').prop('selectedIndex', 0);
}

function bind_close()
{
	jQuery("#segCloseDemographics").click(function(){
		clear_controls("a");
		jQuery('#SegDemographics').remove(); 
	});

	jQuery("#segCloseLocation").click(function(){
		clear_controls("b");
		jQuery('#SegLocation').remove();
	});
	
	jQuery("#segCloseShared").click(function(){
		clear_controls("d");
		jQuery('#SegShared').remove(); 
	});
	
	
}
var actualDate = new Date;

jQuery("#optvisitedfrom_demoraphics").datepicker({
	minDate : new Date(actualDate.getFullYear()-1, actualDate.getMonth(), actualDate.getDate()),
	maxDate : new Date,
	changeMonth: true,
    changeYear: true
});

jQuery("#optvisitedto_demoraphics").datepicker({
	minDate : new Date(actualDate.getFullYear()-1, actualDate.getMonth(), actualDate.getDate()),
	maxDate : new Date,
	changeMonth: true,
    changeYear: true
});

jQuery("#optvisitedfrom").datepicker({
	minDate : new Date(actualDate.getFullYear()-1, actualDate.getMonth(), actualDate.getDate()),
	maxDate : new Date,
	changeMonth: true,
    changeYear: true
});

jQuery("#optvisitedto").datepicker({
	minDate : new Date(actualDate.getFullYear()-1, actualDate.getMonth(), actualDate.getDate()),
	maxDate : new Date,
	changeMonth: true,
    changeYear: true
});

jQuery("#optsharedfrom").datepicker({
	minDate : new Date(actualDate.getFullYear()-1, actualDate.getMonth(), actualDate.getDate()),
	maxDate : new Date,
	changeMonth: true,
    changeYear: true
});

jQuery("#optsharedto").datepicker({
	minDate : new Date(actualDate.getFullYear()-1, actualDate.getMonth(), actualDate.getDate()),
	maxDate : new Date,
	changeMonth: true,
    changeYear: true
});

   
//jQuery("#popupcancel").live("click",function(){
	jQuery(document).on( "click","#popupcancel",function(){
           jQuery.fancybox.close(); 
       return false; 
    });


$("a#myaccount").css("background-color","orange");
 function show_tooltip()
                {
                    jQuery('.notification_tooltip').tooltip({
                        content: function () {
                            return jQuery(this).attr('title');
                        },
                        track: true,
                        delay: 0,
                        showURL: false,
                        showBody: "<br>",
                        fade: 250
                    });
                }
                show_tooltip();
function close_popup(popup_name)
{

	jQuery("#" + popup_name + "FrontDivProcessing").fadeOut(200, function () {
	jQuery("#" + popup_name + "BackDiv").fadeOut(200, function () {
		 jQuery("#" + popup_name + "PopUpContainer").fadeOut(200, function () {         
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "BackDiv,#" + popup_name + "PopUpContainer").css("display", "none");
				jQuery("#" + popup_name + "FrontDivProcessing,#" + popup_name + "PopUpContainer").css("opacity", "1");
				jQuery("#" + popup_name + "BackDiv").css("opacity", "0.5").css("filter", "alpha(opacity=50)").css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)");
		 });
	});
	});
	
}
 function open_popup(popup_name)
{

	if($("#hdn_image_id").val()!="")
	{
		$('input[name=use_image][value='+$("#hdn_image_id").val()+']').attr("checked","checked");
	}
	$("#" + popup_name + "FrontDivProcessing").fadeIn(200, function () {
		$("#" + popup_name + "BackDiv").fadeIn(200, function () {
			 $("#" + popup_name + "PopUpContainer").fadeIn(200, function () {         
	
			 });
		});
	});
	
	
}
</script>
